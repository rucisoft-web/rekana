<?php

namespace App\Services;

use App\Enums\CardStatus;
use App\Enums\ContractMode;
use App\Enums\ContractStatus;
use App\Enums\RiskLevel;
use App\Models\AccessCard;
use App\Models\Contract;
use App\Models\User;
use App\Models\Worker;
use App\Support\RuleViolation;

/**
 * M4: rapat kick-off, bridging document, izin mulai kerja, dan kartu akses QR.
 * Kartu berlaku sampai tanggal terpendek di antara akhir kontrak, surat sehat, dan masa berlaku induksi.
 */
class MobilizationService
{
    public function kickoff(Contract $contract, string $notes, ?string $bridging): Contract
    {
        $plan = $contract->workPlan;
        $needsBridging = $plan && ($plan->risk_category === RiskLevel::Tinggi
            || ($plan->risk_category === RiskLevel::Menengah && $plan->contract_mode === ContractMode::Mode2));

        RuleViolation::throwIf([
            $contract->status !== ContractStatus::Mobilisasi ? 'Kick-off hanya pada tahap mobilisasi.' : null,
            $needsBridging && blank($bridging) ? 'Pekerjaan ini wajib bridging document (pembagian tanggung jawab K3 owner dan kontraktor).' : null,
        ]);

        $contract->forceFill(['kickoff_at' => now(), 'kickoff_notes' => $notes, 'bridging_document' => $bridging])->save();

        return $contract;
    }

    public function approveStart(Contract $contract, User $approver): Contract
    {
        RuleViolation::throwIf([
            $contract->status !== ContractStatus::Mobilisasi ? 'Kontrak tidak dalam tahap mobilisasi.' : null,
            ! $contract->kickoff_at ? 'Rapat kick-off HSE belum dicatat.' : null,
            ! $contract->contractor->validCertificate($contract->owner_id) ? 'Sertifikat CSMS rekanan tidak berlaku.' : null,
            $contract->accessCards()->usable()->doesntExist() ? 'Belum ada pekerja dengan kartu akses aktif.' : null,
        ]);

        $contract->forceFill(['status' => ContractStatus::Berjalan, 'start_approved_at' => now(), 'start_approved_by' => $approver->id])->save();

        return $contract;
    }

    /** @return array<int, string> alasan pekerja belum boleh diberi kartu (kosong = boleh). */
    public function cardBlockers(Contract $contract, Worker $worker): array
    {
        return array_values(array_filter([
            $worker->contractor_id !== $contract->contractor_id ? "{$worker->name} bukan pekerja {$contract->contractor->name}." : null,
            ! $worker->is_active ? "{$worker->name} tidak aktif." : null,
            ! $worker->medicalValid() ? "Surat keterangan sehat {$worker->name} belum ada atau kedaluwarsa." : null,
            ! $worker->validInduction($contract->owner_id) ? "{$worker->name} belum lulus induksi yang masih berlaku." : null,
            in_array($contract->status, [ContractStatus::Selesai, ContractStatus::Dihentikan], true) ? 'Kontrak tidak aktif.' : null,
            $contract->end_date->endOfDay()->isPast() ? 'Kontrak sudah berakhir.' : null,
        ]));
    }

    public function issueCard(Contract $contract, Worker $worker): AccessCard
    {
        RuleViolation::throwIf($this->cardBlockers($contract, $worker));

        $induction = $worker->validInduction($contract->owner_id);
        $validUntil = collect([$contract->end_date, $worker->medical_until, $induction->valid_until])->min();

        AccessCard::withoutGlobalScopes()->where('owner_id', $contract->owner_id)->where('contract_id', $contract->id)
            ->where('worker_id', $worker->id)->where('status', CardStatus::Aktif)
            ->update(['status' => CardStatus::Dicabut->value, 'blocked_reason' => 'Diganti kartu baru']);

        return AccessCard::create([
            'owner_id' => $contract->owner_id,
            'contract_id' => $contract->id,
            'worker_id' => $worker->id,
            'issued_at' => today(),
            'valid_until' => $validUntil,
            'status' => CardStatus::Aktif,
        ]);
    }

    public function blockCard(AccessCard $card, string $reason): void
    {
        $card->forceFill(['status' => CardStatus::Diblokir, 'blocked_reason' => $reason])->save();
    }

    /** Dijalankan harian: kartu yang lewat masa berlaku menjadi Kedaluwarsa. */
    public function expireCards(): int
    {
        return AccessCard::withoutGlobalScopes()->where('status', CardStatus::Aktif)->whereDate('valid_until', '<', today())
            ->update(['status' => CardStatus::Kedaluwarsa->value]);
    }
}
