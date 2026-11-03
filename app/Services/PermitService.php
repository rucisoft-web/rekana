<?php

namespace App\Services;

use App\Enums\AreaClass;
use App\Enums\PermitStatus;
use App\Enums\PermitType;
use App\Models\User;
use App\Models\Worker;
use App\Models\WorkPermit;
use App\Support\RuleViolation;

/**
 * Permit to Work dasar: draf → diajukan → disetujui → aktif → ditutup (atau dibatalkan).
 * Syarat pengajuan: kontrak sudah boleh bekerja, semua pekerja berkartu aktif, daftar periksa jenis izin lengkap,
 * uji gas dalam batas untuk ruang terbatas, titik isolasi untuk LOTO dan ruang terbatas.
 */
class PermitService
{
    /** @return array<int, string> */
    public function submitBlockers(WorkPermit $permit): array
    {
        $contract = $permit->contract;
        $checklist = (array) $permit->checklist;
        $missing = collect($permit->type->checklist())->reject(fn ($label, $key) => ! empty($checklist[$key]));
        $hours = $permit->starts_at->diffInMinutes($permit->ends_at) / 60;
        $workers = $permit->workers()->get();

        $reasons = [
            $permit->status !== PermitStatus::Draf ? 'Izin ini sudah diajukan.' : null,
            ! $contract->isWorkable() ? 'Kontrak belum mendapat izin mulai kerja atau sudah berakhir.' : null,
            ! $contract->contractor->validCertificate($permit->owner_id) ? 'Sertifikat CSMS rekanan tidak berlaku.' : null,
            $permit->ends_at <= $permit->starts_at ? 'Waktu selesai harus setelah waktu mulai.' : null,
            $hours > (int) config('rekana.permit_max_hours') ? 'Satu izin paling lama '.config('rekana.permit_max_hours').' jam (satu shift); buat izin baru untuk shift berikutnya.' : null,
            $workers->isEmpty() ? 'Tambahkan pekerja yang tercakup izin ini.' : null,
            $missing->isNotEmpty() ? 'Daftar periksa belum lengkap: '.$missing->implode('; ').'.' : null,
            $permit->type->needsIsolationPoints() && blank($permit->isolation_points) ? 'Tuliskan titik-titik isolasi energi.' : null,
        ];

        foreach ($workers as $worker) {
            /** @var Worker $worker */
            $card = $worker->activeCard($permit->owner_id);
            $reasons[] = ! $card || $card->contract_id !== $contract->id ? "{$worker->name} tidak punya kartu akses aktif untuk kontrak ini." : null;
        }

        if ($permit->type->needsGasTest() || ($permit->type === PermitType::KerjaPanas && $permit->area?->hazard_class === AreaClass::Bahaya)) {
            $reasons = array_merge($reasons, $this->gasTestProblems((array) $permit->gas_test, $permit->type));
        }

        return array_values(array_filter($reasons));
    }

    /** @return array<int, string> */
    public function gasTestProblems(array $gas, PermitType $type): array
    {
        $l = config('rekana.gas_limits');
        $filled = fn (string $k) => isset($gas[$k]) && $gas[$k] !== '' && $gas[$k] !== null;

        if (! $filled('lel')) {
            return ['Hasil uji gas belum dicatat.'];
        }

        $problems = [(float) $gas['lel'] > $l['lel_max'] ? "LEL {$gas['lel']}% melebihi batas {$l['lel_max']}%." : null];

        if ($type === PermitType::RuangTerbatas) {
            foreach (['o2', 'h2s', 'co'] as $k) {
                $problems[] = ! $filled($k) ? 'Hasil uji gas '.strtoupper($k).' belum dicatat.' : null;
            }
            if ($filled('o2') && ((float) $gas['o2'] < $l['o2_min'] || (float) $gas['o2'] > $l['o2_max'])) {
                $problems[] = "O2 {$gas['o2']}% di luar rentang aman {$l['o2_min']}–{$l['o2_max']}%.";
            }
            $problems[] = $filled('h2s') && (float) $gas['h2s'] > $l['h2s_max'] ? "H2S {$gas['h2s']} ppm melebihi batas {$l['h2s_max']} ppm." : null;
            $problems[] = $filled('co') && (float) $gas['co'] > $l['co_max'] ? "CO {$gas['co']} ppm melebihi batas {$l['co_max']} ppm." : null;
        }

        return array_values(array_filter($problems));
    }

    public function submit(WorkPermit $permit, User $by): WorkPermit
    {
        RuleViolation::throwIf($this->submitBlockers($permit));
        $permit->forceFill(['status' => PermitStatus::Diajukan, 'requested_by' => $permit->requested_by ?? $by->id])->save();

        return $permit;
    }

    public function approve(WorkPermit $permit, User $approver): WorkPermit
    {
        RuleViolation::throwIf([
            $permit->status !== PermitStatus::Diajukan ? 'Hanya izin berstatus Diajukan yang dapat disetujui.' : null,
            $permit->requested_by === $approver->id ? 'Izin harus disetujui orang lain, bukan pemohon.' : null,
            ! $approver->roleIn($permit->owner_id)?->canAuthorizePermit() ? 'Peran Anda tidak berwenang menyetujui izin kerja.' : null,
        ]);
        $permit->forceFill(['status' => PermitStatus::Disetujui, 'approved_by' => $approver->id, 'approved_at' => now()])->save();

        return $permit;
    }

    public function activate(WorkPermit $permit): WorkPermit
    {
        RuleViolation::throwIf([
            $permit->status !== PermitStatus::Disetujui ? 'Izin belum disetujui.' : null,
            $permit->ends_at->isPast() ? 'Waktu izin sudah lewat; buat izin baru.' : null,
        ]);
        $permit->forceFill(['status' => PermitStatus::Aktif, 'activated_at' => now()])->save();

        return $permit;
    }

    public function close(WorkPermit $permit, User $by, string $notes): WorkPermit
    {
        RuleViolation::throwIf([$permit->status !== PermitStatus::Aktif ? 'Hanya izin aktif yang dapat ditutup.' : null]);
        $permit->forceFill(['status' => PermitStatus::Ditutup, 'closed_by' => $by->id, 'closed_at' => now(), 'close_notes' => $notes])->save();

        return $permit;
    }

    public function cancel(WorkPermit $permit, string $reason): WorkPermit
    {
        RuleViolation::throwIf([in_array($permit->status, [PermitStatus::Ditutup, PermitStatus::Dibatalkan], true) ? 'Izin sudah ditutup.' : null]);
        $permit->forceFill(['status' => PermitStatus::Dibatalkan, 'cancel_reason' => $reason])->save();

        return $permit;
    }
}
