<?php

namespace Tests\Feature;

use App\Enums\CardStatus;
use App\Enums\PermitStatus;
use App\Enums\PermitType;
use App\Enums\PqStatus;
use App\Enums\RiskLevel;
use App\Models\AccessCard;
use App\Models\Area;
use App\Models\Contract;
use App\Models\Contractor;
use App\Models\Owner;
use App\Models\PqAnswer;
use App\Models\Prequalification;
use App\Models\Tender;
use App\Models\User;
use App\Models\WorkPermit;
use App\Services\MobilizationService;
use App\Services\PermitService;
use App\Services\PrequalificationService;
use App\Services\TenderService;
use App\Support\CurrentOwner;
use App\Support\RuleViolation;
use Database\Seeders\GarudafoodDemoSeeder;
use Database\Seeders\PakuwonDemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use LogicException;
use Tests\TestCase;

/** Aturan bisnis inti CSMS: isolasi owner, skor PQ, kelayakan seleksi, kartu akses, izin kerja, isolasi portal rekanan. */
class BusinessRulesTest extends TestCase
{
    use RefreshDatabase;

    protected Owner $owner;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(GarudafoodDemoSeeder::class);
        $this->owner = Owner::where('slug', 'garudafood-gresik')->firstOrFail();
    }

    public function test_owner_data_is_isolated(): void
    {
        $this->seed(PakuwonDemoSeeder::class);
        $other = Owner::where('slug', 'pakuwon-surabaya')->firstOrFail();

        app(CurrentOwner::class)->runAs($this->owner, function () {
            $this->assertSame(Area::withoutGlobalScopes()->where('owner_id', $this->owner->id)->count(), Area::count());
            $this->assertTrue(Area::get()->every(fn ($a) => $a->owner_id === $this->owner->id));
        });

        // Izin kerja tidak boleh merujuk kontrak milik owner lain.
        $foreignContract = Contract::withoutGlobalScopes()->where('owner_id', $other->id)->firstOrFail();
        $permit = WorkPermit::withoutGlobalScopes()->where('owner_id', $this->owner->id)->firstOrFail();
        $this->expectException(LogicException::class);
        $permit->contract_id = $foreignContract->id;
        $permit->save();
    }

    public function test_pq_thresholds_and_critical_items(): void
    {
        $service = app(PrequalificationService::class);
        $t = config('rekana.pq.thresholds');

        $this->assertSame(RiskLevel::Tinggi, $service->resultFor($t['tinggi'], RiskLevel::Tinggi, $this->owner));
        $this->assertSame(RiskLevel::Menengah, $service->resultFor($t['tinggi'] - 1, RiskLevel::Tinggi, $this->owner));
        $this->assertSame(RiskLevel::Menengah, $service->resultFor(99, RiskLevel::Menengah, $this->owner), 'Hasil dibatasi target yang diajukan.');
        $this->assertNull($service->resultFor($t['rendah'] - 1, RiskLevel::Tinggi, $this->owner));

        $pq = Prequalification::withoutGlobalScopes()->where('owner_id', $this->owner->id)->where('status', PqStatus::Disetujui)->firstOrFail();
        $before = $service->calculate($pq);
        $this->assertFalse($before['critical_failed']);
        $this->assertNotNull($before['result']);

        $critical = PqAnswer::where('prequalification_id', $pq->id)->whereHas('item', fn ($q) => $q->where('is_critical', true))->firstOrFail();
        $critical->forceFill(['score' => 0])->save();
        $after = $service->calculate($pq);
        $this->assertTrue($after['critical_failed']);
        $this->assertNull($after['result'], 'Butir kritis bernilai 0 menggugurkan PQ berapa pun skornya.');
    }

    public function test_four_eyes_on_pq_approval(): void
    {
        $pq = Prequalification::withoutGlobalScopes()->where('owner_id', $this->owner->id)->where('status', PqStatus::Disetujui)->firstOrFail();
        $pq->forceFill(['status' => PqStatus::Dinilai])->save();
        $evaluator = User::findOrFail($pq->evaluator_id);

        $this->expectException(RuleViolation::class);
        app(PrequalificationService::class)->approve($pq, $evaluator);
    }

    public function test_only_certified_contractors_can_be_invited(): void
    {
        $service = app(TenderService::class);
        $tender = Tender::withoutGlobalScopes()->where('owner_id', $this->owner->id)->with('workPlan')->firstOrFail();
        $category = $tender->workPlan->risk_category;
        $eligible = $service->eligibleContractors($tender);

        $this->assertNotEmpty($eligible);
        foreach ($eligible as $contractor) {
            $this->assertTrue($contractor->validCertificate($this->owner->id)->max_risk->covers($category));
        }

        $rejected = Contractor::where('name', 'PT Krikilan Karya Sipil')->firstOrFail();
        $this->assertFalse($eligible->contains('id', $rejected->id));
        $this->expectException(RuleViolation::class);
        $service->invite($tender, $rejected);
    }

    public function test_card_blockers_and_daily_expiry(): void
    {
        $service = app(MobilizationService::class);
        $card = AccessCard::withoutGlobalScopes()->where('owner_id', $this->owner->id)->where('status', CardStatus::Aktif)->with('contract', 'worker')->firstOrFail();
        $worker = $card->worker;

        $worker->forceFill(['medical_until' => today()->subDay()])->save();
        $this->assertNotEmpty(array_filter($service->cardBlockers($card->contract, $worker), fn ($r) => str_contains($r, 'sehat')));

        $card->forceFill(['valid_until' => today()->subDay()])->save();
        $this->artisan('rekana:expire-cards')->assertSuccessful();
        $this->assertSame(CardStatus::Kedaluwarsa, $card->fresh()->status);
    }

    public function test_permit_rules(): void
    {
        $service = app(PermitService::class);
        $g = config('rekana.gas_limits');

        $this->assertSame([], $service->gasTestProblems(['o2' => 20.8, 'lel' => 0, 'h2s' => 0, 'co' => 2], PermitType::RuangTerbatas));
        $this->assertCount(1, $service->gasTestProblems(['o2' => $g['o2_min'] - 1, 'lel' => 0, 'h2s' => 0, 'co' => 2], PermitType::RuangTerbatas));
        $this->assertCount(1, $service->gasTestProblems(['o2' => 20.8, 'lel' => $g['lel_max'] + 1, 'h2s' => 0, 'co' => 2], PermitType::RuangTerbatas));
        $this->assertSame(['Hasil uji gas belum dicatat.'], $service->gasTestProblems([], PermitType::RuangTerbatas));

        $permit = WorkPermit::withoutGlobalScopes()->where('owner_id', $this->owner->id)->where('status', PermitStatus::Draf)->firstOrFail();
        app(CurrentOwner::class)->set($this->owner);
        $permit->forceFill(['checklist' => [], 'ends_at' => $permit->starts_at->copy()->addHours(config('rekana.permit_max_hours') + 2)])->save();
        $blockers = implode(' ', $service->submitBlockers($permit->fresh()));
        $this->assertStringContainsString('Daftar periksa belum lengkap', $blockers);
        $this->assertStringContainsString('paling lama', $blockers);

        // Penyetuju tidak boleh pemohon.
        $submitted = WorkPermit::withoutGlobalScopes()->where('owner_id', $this->owner->id)->where('status', PermitStatus::Diajukan)->firstOrFail();
        $this->expectException(RuleViolation::class);
        $service->approve($submitted, User::findOrFail($submitted->requested_by));
    }

    public function test_contractor_portal_is_isolated(): void
    {
        $user = User::where('email', 'admin@mesin.garudafood.rekana.test')->firstOrFail();
        $mine = $user->contractors()->firstOrFail();
        $other = Contractor::where('id', '!=', $mine->id)->firstOrFail();

        $this->assertContains($this->actingAs($user)->get('/kontraktor/'.$other->slug)->status(), [403, 404]);

        $foreign = WorkPermit::withoutGlobalScopes()->whereHas('contract', fn ($q) => $q->withoutGlobalScopes()->where('contractor_id', '!=', $mine->id))->firstOrFail();
        $own = WorkPermit::withoutGlobalScopes()->whereHas('contract', fn ($q) => $q->withoutGlobalScopes()->where('contractor_id', $mine->id))->firstOrFail();
        $this->actingAs($user)->get('/kontraktor/'.$mine->slug.'/izin-kerja')->assertOk()->assertSee($own->number)->assertDontSee($foreign->number);
    }
}
