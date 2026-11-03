{{-- Halaman depan publik: fitur utama Akredia dan pintu masuk tamu ke perguruan tinggi contoh. --}}
@php
    use App\Http\Controllers\DemoController;
    use App\Support\Workshop;

    $tryUrl = $guestUrl ? '#data-contoh' : $loginUrl;
    $signedIn = auth()->check();
@endphp
<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Akredia · Persiapan akreditasi institusi IAPT 4.1</title>
<meta name="description" content="Akredia membantu perguruan tinggi menyiapkan akreditasi institusi IAPT 4.1: dasbor kesiapan, butir kerja, pustaka bukti, LED, LKPT, rekonsiliasi PDDikti, dan asesmen lapangan.">
<link rel="icon" href="{{ asset('favicon.svg') }}">
<link rel="stylesheet" href="{{ asset('fonts/filament/filament/inter/index.css') }}">
<link rel="stylesheet" href="{{ asset('css/landing.css') }}?v={{ @filemtime(public_path('css/landing.css')) }}">
</head>
<body class="akl">
<header class="akl-nav">
    <div class="akl-wrap akl-nav-in">
        <a class="akl-brand" href="{{ route('home') }}">
            <img src="{{ asset('favicon.svg') }}" width="34" height="34" alt="">
            <span><b>Akredia</b><small>Persiapan akreditasi institusi</small></span>
        </a>
        <nav class="akl-links" aria-label="Navigasi halaman">
            <a href="#fitur">Fitur</a>
            @if ($guestUrl)<a href="#data-contoh">Data contoh</a>@endif
            <a href="#workshop">Workshop</a>
            <a href="#keamanan">Keamanan &amp; AI</a>
            <a href="{{ route('pimpinan') }}">Untuk Pimpinan</a>
            <a href="#tanya-jawab">Tanya jawab</a>
            <a href="https://rucisoft.id">Rucisoft.id</a>
        </nav>
        <a class="akl-btn outline small" href="{{ $signedIn ? url('/app') : $loginUrl }}">{{ $signedIn ? 'Buka aplikasi' : 'Masuk' }}</a>
    </div>
</header>

<main>
    <section class="akl-hero">
        <div class="akl-wrap akl-hero-in">
            <div>
                <div class="akl-eyebrow">Untuk pimpinan dan tim akreditasi perguruan tinggi</div>
                <h1>Akreditasi institusi yang terukur, terbukti, dan siap diasesmen.</h1>
                <p class="akl-lead">Akredia menghimpun seluruh persiapan IAPT 4.1 di satu tempat: penilaian mandiri per sub-indikator, bukti berversi, narasi LED, tabel LKPT, dan tindak lanjut. Pimpinan melihat kesiapan dalam satu angka dan tahu siapa yang harus bergerak minggu ini.</p>
                <div class="akl-cta">
                    <a class="akl-btn" href="{{ $tryUrl }}">{{ $guestUrl ? 'Coba sebagai tamu' : 'Masuk' }}</a>
                    <a class="akl-btn outline" href="{{ route('pimpinan') }}">Akredia untuk Pimpinan PT</a>
                </div>
                @if ($guestUrl)
                    <p class="akl-note">Tanpa mendaftar. Anda masuk ke <b>{{ [2 => 'dua ', 3 => 'tiga '][$samples->count()] ?? '' }}perguruan tinggi contoh</b> berisi data demo, hanya untuk dilihat. <a href="{{ route('pimpinan') }}">Baca panduan untuk pimpinan</a>.</p>
                @endif
            </div>
            <figure class="akl-shot">
                <img src="{{ asset('img/pimpinan-dasbor.jpg') }}" alt="Dasbor Eksekutif berisi Indeks Kesiapan Akreditasi, proyeksi skor, dan syarat perlu" width="1440" height="900">
                <span class="akl-badge">DATA CONTOH</span>
            </figure>
        </div>
    </section>

    @include('partials.rucisoft-alur')

    <section class="akl-sec soft" id="fitur">
        <div class="akl-wrap">
            <div class="akl-eyebrow">Fitur</div>
            <h2>Satu aplikasi dari penilaian mandiri sampai asesmen lapangan</h2>
            <div class="akl-grid three">
                @foreach ([
                    ['Dasbor Eksekutif & AKI', 'Indeks Kesiapan Akreditasi dari lima komponen, proyeksi skor jalur Terakreditasi dan Unggul, peta panas 39 indikator, dan langkah pimpinan minggu ini.'],
                    ['Peta Instrumen IAPT 4.1', 'Empat kriteria, 39 indikator, dan 64 sub-indikator dengan deskriptor, kata kunci, dan syarat perlu, untuk delapan varian Buku 3 (PTN-BH, PTN, PTS, vokasi, PJJ).'],
                    ['Butir Kerja & Kertas Kerja', 'Satu butir kerja per sub-indikator lengkap dengan PIC, reviewer, penilaian mandiri, analisis kesenjangan, dan rencana tindak lanjut dalam satu kertas kerja gabungan.'],
                    ['Pustaka Bukti', 'Dokumen bernomor dan berversi, divalidasi orang kedua, dengan pengingat kedaluwarsa dan pemeriksa tautan agar bukti yang dikutip di LED tetap bisa dibuka.'],
                    ['Penyusunan LED', 'Editor per sub-indikator dengan batas kata, sitasi bukti [B:KODE], angka metrik otomatis, cek cakupan kata kunci deskriptor, alur reviu, dan pengesahan pimpinan.'],
                    ['Simulasi Penilaian', 'Centang sub-indikator yang akan dipenuhi dan lihat proyeksi skor serta status syarat perlu berubah, sebelum pimpinan mengunci jalur akreditasi.'],
                    ['Data Kinerja & Tabel LKPT', 'Dosen, statistik mahasiswa, kohort, publikasi, HKI, dan dana penelitian diimpor dari XLSX/CSV; 18 metrik IAPT terhitung dan 12 tabel LKPT siap diekspor.'],
                    ['Rekonsiliasi PD Dikti', 'Angka internal dibandingkan dengan PDDikti, selisih diberi tingkat keparahan, lalu diselidiki dan diperbaiki di sumbernya sebelum data dikunci.'],
                    ['SPMI', 'Standar mutu, audit mutu internal, temuan, CAPA dengan verifikasi oleh orang lain, dan rapat tinjauan manajemen, tertaut ke indikator yang dinilai.'],
                    ['Pengendalian', 'Tugas dan RTL, papan Kanban, kalender tenggat, register risiko, keputusan pimpinan, eskalasi otomatis, dan laporan mingguan.'],
                    ['Asesmen Lapangan', 'Agenda sesi, narasumber, bank pertanyaan per kelompok dengan status gladi, dan pencatatan rekomendasi asesor.'],
                    ['Portal Bukti & Ekspor', 'Portal bukti publik untuk asesor tanpa login, ekspor LED (DOCX), kertas kerja dan LKPT (XLSX), laporan syarat perlu, dan log audit.'],
                ] as [$title, $text])
                    <div class="akl-card"><h3><i></i>{{ $title }}</h3><p>{{ $text }}</p></div>
                @endforeach
            </div>
        </div>
    </section>

    @foreach ([
        ['Sebelum memilih jalur', 'Syarat perlu disorot sebelum menjadi kejutan', 'Simulasi Penilaian menghitung proyeksi skor dari penilaian per sub-indikator dan menandai syarat perlu yang belum terpenuhi. Pada data contoh UNJ, jalur Unggul tertahan oleh proporsi lektor kepala dan guru besar, sehingga pimpinan bisa memutuskan tindak lanjut jauh sebelum pengajuan.', ['Ambang skor tiap jalur ditampilkan berdampingan', 'Sub-indikator yang belum dinilai dihitung sebagai belum terpenuhi', 'Jalur dikunci pimpinan setelah simulasi'], 'pimpinan-simulasi.jpg', 'Simulasi Penilaian pada data contoh UNJ'],
        ['Menulis LED', 'Narasi diperiksa terhadap deskriptor saat ditulis', 'Editor LED menunjukkan kata kunci deskriptor yang belum disebut, panjang narasi terhadap batas kata, dan bukti yang dikutip. Asisten narasi memeriksa alur PPEPP, analisis akar masalah, dan sitasi, lalu memberi saran; keputusan isi tetap pada PIC dan reviewer.', ['Kata kunci deskriptor ditandai hijau atau merah', 'Sitasi [B:KODE] membuka dokumen di Pustaka Bukti', 'Riwayat revisi, komentar reviu, dan pengesahan pimpinan'], 'landing-led.jpg', 'Editor LED sub-indikator 1A pada data contoh UNJ'],
        ['Sebelum data dikunci', 'Selisih dengan PDDikti diselesaikan lebih dulu', 'Rekonsiliasi membandingkan angka internal dengan PD Dikti untuk mahasiswa aktif, lulusan, dosen tetap, dan rasio-rasio utama. Selisih di atas toleransi diberi tingkat keparahan dan ditugaskan ke unit pemilik data untuk diselidiki.', ['Selisih absolut dan persentase per data', 'Status investigasi sampai terverifikasi', 'Angka yang sama dipakai di LED dan tabel LKPT'], 'landing-rekon.jpg', 'Rekonsiliasi PD Dikti pada data contoh UNJ'],
    ] as $i => [$kicker, $title, $text, $points, $img, $alt])
        <section class="akl-sec {{ $i % 2 ? 'soft' : '' }}">
            <div class="akl-wrap akl-split {{ $i % 2 ? 'flip' : '' }}">
                <div>
                    <div class="akl-eyebrow">{{ $kicker }}</div>
                    <h2>{{ $title }}</h2>
                    <p>{{ $text }}</p>
                    <ul class="akl-check">@foreach ($points as $point)<li>{{ $point }}</li>@endforeach</ul>
                </div>
                <figure class="akl-shot">
                    <img src="{{ asset('img/'.$img) }}" alt="{{ $alt }}" width="1440" height="900" loading="lazy">
                    <span class="akl-badge">DATA CONTOH</span>
                </figure>
            </div>
        </section>
    @endforeach

    @if ($guestUrl)
        <section class="akl-sec dark" id="data-contoh">
            <div class="akl-wrap">
                <div class="akl-eyebrow">Coba sendiri</div>
                <h2>{{ [2 => 'Dua perguruan tinggi', 3 => 'Tiga perguruan tinggi'][$samples->count()] ?? 'Perguruan tinggi' }} contoh dengan data demo</h2>
                <p class="akl-sub">Profil diambil dari laman resmi masing-masing perguruan tinggi, sedangkan angka kinerja, nama staf, dan aktivitas adalah ilustrasi. Setiap layar di aplikasi diberi label <b>DATA DEMO</b>.</p>
                <div class="akl-grid {{ $samples->count() === 2 ? 'two' : 'three' }}">
                    @foreach ($samples as $pt)
                        @php([$name, $kind, $story, $email] = DemoController::STORIES[$pt->slug] ?? [$pt->name, $pt->type?->getLabel().' · '.$pt->city, 'Perguruan tinggi contoh dengan data demo untuk menjelajahi seluruh fitur Akredia.', null])
                        <div class="akl-sample">
                            <span class="akl-tag">{{ DemoController::shortName($pt) }}</span>
                            <h3>{{ $name }}</h3>
                            <div class="akl-kind">{{ $kind }}</div>
                            <p>{{ $story }}</p>
                            <div class="akl-sample-foot">
                                <a class="akl-btn gold small" href="{{ DemoController::guestUrl($pt->slug) }}">Masuk sebagai tamu</a>
                                @if ($email)<small>Akun pimpinan: <code>{{ $email }}</code></small>@endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="akl-fine">Akun tamu dipakai bersama pengunjung lain dan hanya dapat melihat: tombol buat, ubah, hapus, dan sahkan disembunyikan, dan data tidak dapat diubah. Perguruan tinggi contoh dapat diganti kapan saja dari pita di atas layar. Jangan mengunggah dokumen sungguhan.</p>
            </div>
        </section>
    @endif

    <section class="akl-sec akl-ws" id="workshop">
        <div class="akl-wrap">
            <div class="akl-ws-head">
                <div>
                    <div class="akl-eyebrow">Workshop 2 hari · IAPT 4.1</div>
                    <h2>{{ Workshop::TITLE }}</h2>
                    <p class="akl-sub">{{ Workshop::TAGLINE }} Peserta pulang membawa peta syarat perlu, struktur tim, analisis kesenjangan awal, draf LED, dan rencana aksi 90 hari untuk kampusnya.</p>
                </div>
                <div class="akl-ws-partner">
                    <img src="{{ asset('img/mitra-uin-sunan-kalijaga.png') }}" alt="Logo UIN Sunan Kalijaga Yogyakarta" width="72" height="101">
                    <span>Bekerja sama dengan<b>{{ Workshop::PARTNER }}</b></span>
                </div>
            </div>
            <div class="akl-grid two">
                @foreach (Workshop::DAYS as $n => [$label, $theme, $sessions])
                    <div class="akl-card akl-ws-day">
                        <div class="akl-ws-daylabel">{{ $label }}</div>
                        <h3>{{ $theme }}</h3>
                        <ul class="akl-check">
                            @foreach ($sessions as [$time, $title])
                                @if (str_starts_with($title, 'Sesi'))<li>{{ \Illuminate\Support\Str::after($title, '· ') }}</li>@endif
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
            <div class="akl-cta">
                <a class="akl-btn" href="{{ route('workshop') }}">Lihat materi lengkap</a>
                <a class="akl-btn outline" href="{{ Workshop::waUrl() }}" target="_blank" rel="noopener">Tanya jadwal &amp; daftar via WhatsApp</a>
            </div>
            <p class="akl-fine">Untuk pimpinan, LPM/SPM, tim akreditasi, dan pengelola data PDDikti. Dapat diselenggarakan terbuka maupun khusus untuk satu perguruan tinggi (in-house). Informasi: {{ Workshop::EMAIL }} · WA {{ Workshop::PHONE }}.</p>
        </div>
    </section>

    <section class="akl-sec" id="keamanan">
        <div class="akl-wrap">
            <div class="akl-eyebrow">Keamanan &amp; AI</div>
            <h2>Data institusi tetap di server kampus sendiri</h2>
            <div class="akl-grid two">
                <div class="akl-card">
                    <h3>Akses dan jejak</h3>
                    <ul class="akl-check">
                        <li>Delapan peran, dari Administrator, Pimpinan, dan Manajer Akreditasi sampai PIC, Reviewer, Asesor Internal, dan Pengamat.</li>
                        <li>PIC dan pemilik data hanya bekerja dalam cakupan unitnya; dokumen rahasia dibatasi per unit.</li>
                        <li>Prinsip empat mata: tidak ada yang mereviu atau memvalidasi pekerjaannya sendiri.</li>
                        <li>Verifikasi dua langkah (MFA) untuk Administrator, Manajer, dan Pimpinan.</li>
                        <li>Jejak audit untuk setiap perubahan penting; unduhan dokumen lewat tautan bertanda tangan.</li>
                        <li>Portal bukti publik hanya menampilkan dokumen yang ditandai publik.</li>
                    </ul>
                </div>
                <div class="akl-card">
                    <h3>Asisten AI yang opsional</h3>
                    <ul class="akl-check">
                        <li>Mati sampai kunci AI dipasang di server, dan setiap institusi dapat mematikannya.</li>
                        <li>Tanpa AI pun pemeriksaan narasi tetap berjalan: kata kunci deskriptor, alur PPEPP, akar masalah, sitasi bukti, dan batas kata.</li>
                        <li>AI hanya memberi saran atas narasi yang sedang dibuka; tidak ada yang tersimpan otomatis.</li>
                        <li>Keputusan isi LED tetap pada PIC, reviewer, dan pimpinan.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="akl-sec soft">
        <div class="akl-wrap">
            <div class="akl-eyebrow">Pemasangan</div>
            <h2>Berjalan di hosting cPanel biasa</h2>
            <ol class="akl-steps">
                <li><b>Unggah paket aplikasi</b><span>ke hosting dengan PHP 8.3 dan MySQL 8, lalu jalankan migrasi.</span></li>
                <li><b>Buat akun administrator</b><span>dan daftarkan institusi: kode PT, varian instrumen, unit, dan program studi.</span></li>
                <li><b>Pasang satu cron schedule:run</b><span>agar pengingat kedaluwarsa, eskalasi tugas, dan laporan mingguan berjalan sendiri.</span></li>
            </ol>
            <p class="akl-sub">Tidak memerlukan Node.js, Redis, atau server khusus. Antrean, cache, dan sesi memakai basis data.</p>
        </div>
    </section>

    <section class="akl-sec" id="tanya-jawab">
        <div class="akl-wrap akl-faq">
            <h2>Tanya jawab</h2>
            <details><summary>Apakah data contoh di situs demo milik perguruan tinggi tersebut?</summary><p>Tidak. Nama dan profil umum diambil dari laman resmi untuk menghidupkan skenario, tetapi angka kinerja, nama staf, dokumen, dan aktivitas adalah ilustrasi yang disusun untuk menjelaskan fitur Akredia.</p></details>
            <details><summary>Apakah AKI sama dengan nilai BAN-PT?</summary><p>Tidak. AKI dan proyeksi skor adalah alat manajemen internal untuk memantau kesiapan. Keputusan akreditasi sepenuhnya kewenangan BAN-PT.</p></details>
            <details><summary>Apakah instrumennya sesuai jenis kampus kami?</summary><p>Akredia memuat delapan varian Buku 3 IAPT 4.1. Deskriptor, kata kunci, dan syarat perlu dapat diperbarui administrator bila BAN-PT merevisi lampiran.</p></details>
            <details><summary>Bisakah memakai data yang sudah ada?</summary><p>Bisa. Data dosen, statistik prodi, kohort, publikasi, dan survei dapat diimpor dari XLSX atau CSV. Dokumen bukti diunggah sekali lalu ditautkan ke banyak sub-indikator.</p></details>
            <details><summary>Apakah satu pemasangan bisa dipakai beberapa institusi?</summary><p>Bisa. Setiap institusi terpisah datanya, dan satu pengguna dapat menjadi anggota beberapa institusi dengan peran berbeda.</p></details>
            <details><summary>Bagaimana jika pengajuan sudah dekat?</summary><p>Justru paling bermanfaat. Cek syarat perlu, rekonsiliasi PDDikti, gerbang Siap Diajukan, dan bank pertanyaan asesmen lapangan membantu menutup celah dalam hitungan minggu.</p></details>
        </div>
    </section>

    <section class="akl-end">
        <div class="akl-wrap">
            <h2>Lihat sendiri dalam lima menit</h2>
            <p>Buka Dasbor Eksekutif, coba Simulasi Penilaian, lalu baca narasi LED yang sudah disahkan.</p>
            <div class="akl-cta center">
                <a class="akl-btn gold" href="{{ $tryUrl }}">{{ $guestUrl ? 'Coba sebagai tamu' : 'Masuk' }}</a>
                <a class="akl-btn ghost" href="{{ $loginUrl }}">Masuk dengan akun</a>
            </div>
        </div>
    </section>
</main>

<footer class="akl-foot">
    <div class="akl-wrap">
        <b>Akredia</b> · persiapan akreditasi institusi IAPT 4.1 · <a href="https://rucisoft.id" style="color:inherit;text-decoration:underline">Rucisoft</a> · <a href="https://rucisoft.id" style="color:inherit">rucisoft.id</a>
        <p>AKI dan proyeksi skor adalah alat manajemen internal, bukan prediksi resmi BAN-PT. Data pada perguruan tinggi contoh adalah data demo untuk menjelaskan fitur.</p>
    </div>
</footer>
</body>
</html>
