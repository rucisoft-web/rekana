<?php

namespace App\Http\Controllers;

use App\Models\AccessCard;
use App\Models\Contract;
use App\Models\CsmsCertificate;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\View\View;

/** Halaman cetak (sertifikat CSMS, kartu akses) dan verifikasi publik lewat kode QR. */
class PrintController extends Controller
{
    public static function qr(string $url): string
    {
        return (new QRCode(new QROptions(['outputBase64' => true, 'scale' => 6, 'imageTransparent' => false])))->render($url);
    }

    private function authorizeOwner(int $ownerId): void
    {
        abort_unless(auth()->user()?->activeMembership($ownerId), 403);
    }

    public function certificate(CsmsCertificate $certificate): View
    {
        $this->authorizeOwner($certificate->owner_id);

        return view('cetak.sertifikat', ['c' => $certificate->load('contractor', 'owner', 'prequalification'), 'qr' => self::qr(route('verifikasi.sertifikat', $certificate->verify_code))]);
    }

    public function card(AccessCard $card): View
    {
        $this->authorizeOwner($card->owner_id);

        return view('cetak.kartu', ['cards' => collect([$card->load('worker.competencies', 'contract.contractor', 'contract.owner', 'contract.workPlan.area')])]);
    }

    public function contractCards(Contract $contract): View
    {
        $this->authorizeOwner($contract->owner_id);
        $cards = AccessCard::withoutGlobalScopes()->where('contract_id', $contract->id)->usable()->with('worker.competencies', 'contract.contractor', 'contract.owner', 'contract.workPlan.area')->get();

        return view('cetak.kartu', ['cards' => $cards]);
    }

    public function verifyCertificate(string $code): View
    {
        $c = CsmsCertificate::withoutGlobalScopes()->where('verify_code', $code)->with('contractor', 'owner')->firstOrFail();

        return view('cetak.verifikasi', ['title' => 'Sertifikat CSMS', 'valid' => $c->isValid(), 'rows' => [
            'Nomor' => $c->number, 'Rekanan' => $c->contractor->name, 'Diterbitkan oleh' => $c->owner->name,
            'Boleh mengerjakan sampai risiko' => $c->max_risk->getLabel(), 'Berlaku' => $c->issued_at->translatedFormat('d M Y').' s.d. '.$c->valid_until->translatedFormat('d M Y'),
            'Status' => $c->revoked_at ? 'Dicabut: '.$c->revoke_reason : ($c->isValid() ? 'Berlaku' : 'Kedaluwarsa'),
        ], 'demo' => $c->owner->is_demo]);
    }

    public function verifyCard(string $code): View
    {
        $card = AccessCard::withoutGlobalScopes()->where('code', $code)->with('worker', 'contract.contractor', 'contract.owner')->firstOrFail();

        return view('cetak.verifikasi', ['title' => 'Kartu akses pekerja', 'valid' => $card->isUsable(), 'rows' => [
            'Pekerja' => $card->worker->name, 'Jabatan' => $card->worker->position, 'Rekanan' => $card->contract->contractor->name,
            'Lokasi' => $card->contract->owner->name, 'Kontrak' => $card->contract->title,
            'Berlaku s.d.' => $card->valid_until->translatedFormat('d M Y'), 'Status' => $card->status->getLabel().($card->blocked_reason ? ' ('.$card->blocked_reason.')' : ''),
        ], 'demo' => $card->contract->owner->is_demo]);
    }
}
