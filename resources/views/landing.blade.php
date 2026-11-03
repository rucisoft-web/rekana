@php
    use App\Http\Controllers\DemoController;
    $guest = DemoController::guestUrl();
@endphp
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Rekana · Contractor Safety Management System</title>
    <meta name="description" content="Rekana adalah software Contractor Safety Management System (CSMS) berbasis IOGP 423: penilaian risiko, prakualifikasi, seleksi, mobilisasi, izin kerja, dan evaluasi kinerja kontraktor.">
    <link rel="icon" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v={{ @filemtime(public_path('css/landing.css')) }}">
</head>
<body>
<header class="nav">
    <div class="wrap nav-in">
        <a href="{{ url('/') }}" class="brand">@include('partials.logo', ['size' => 34])<span>Rekana</span></a>
        <nav>
            <a href="#masalah">Masalah</a><a href="#penyebab">Penyebab</a><a href="#solusi">Solusi</a><a href="#langkah">Langkah</a><a href="#demo">Demo</a>
        </nav>
        <div class="nav-cta">
            <a href="{{ $loginUrl }}" class="btn ghost">Masuk</a>
            @if ($guest)<a href="{{ DemoController::guestUrl() }}" class="btn primary">Coba sebagai tamu</a>@endif
        </div>
    </div>
</header>

<section class="hero">
    <div class="wrap hero-in">
        <div>
            <span class="tag">Contractor Safety Management System · IOGP 423</span>
            <h1>Setiap kontraktor yang masuk lokasi Anda harus <em>layak</em>, <em>kompeten</em>, dan <em>terkendali</em>.</h1>
            <p class="lead">Rekana mengelola keselamatan kontraktor dari hulu ke hilir: menilai risiko pekerjaan, menyaring rekanan lewat prakualifikasi, memilih pemenang dengan HSE Plan, membuka gerbang hanya bagi pekerja yang lolos induksi, menjalankan izin kerja digital, dan menilai kinerja secara berkelanjutan.</p>
            <div class="hero-cta">
                @if ($guest)
                    <a href="{{ DemoController::guestUrl() }}" class="btn primary lg">Tamu sebagai Owner</a>
                    <a href="{{ DemoController::guestUrl(null, true) }}" class="btn dark lg">Tamu sebagai Kontraktor</a>
                @else
                    <a href="#solusi" class="btn ghost lg">Pelajari cara kerjanya</a>
                @endif
            </div>
            @if ($guest)<p class="hero-note">Tanpa akun. Tamu dapat membuka semua menu, formulir, dan tombol tindakan owner maupun kontraktor dengan data simulasi; perubahan tidak disimpan.</p>@endif
            <ul class="hero-points">
                <li>Enam tahap IOGP 423 dalam satu alur</li>
                <li>Portal kontraktor gratis</li>
                <li>Template pangan, migas, dan konstruksi</li>
            </ul>
        </div>
        <div class="hero-shot">
            <img src="{{ asset('img/layar/dasbor-owner.png') }}" alt="Tangkapan layar dasbor monitoring rekanan Rekana (data simulasi)" loading="eager">
            <span class="shot-note">Tangkapan layar dengan data simulasi</span>
        </div>
    </div>
    <div class="stripe" aria-hidden="true"></div>
</section>

<section id="masalah" class="sec">
    <div class="wrap">
        <span class="kicker">01 · Masalah</span>
        <h2>Masalah yang dihadapi perusahaan pemberi kerja</h2>
        <p class="sub">Sebagian besar pekerjaan berisiko tinggi di pabrik, lapangan migas, dan proyek gedung dikerjakan oleh kontraktor. Laporan kinerja keselamatan tahunan IOGP berulang kali menunjukkan bahwa porsi terbesar jam kerja dan korban fatal di industri migas ada di pihak kontraktor. Pola yang sama terlihat di manufaktur dan konstruksi.</p>
        <div class="cards six">
            <article class="card"><b>Kecelakaan melibatkan pekerja kontraktor</b><p>Pekerjaan ruang terbatas, kerja panas, ketinggian, dan isolasi energi paling sering diserahkan ke kontraktor, dan di situlah kecelakaan berat terjadi.</p></article>
            <article class="card"><b>Kontraktor dipilih karena harga</b><p>Rekam jejak K3, sertifikasi personel, dan sistem keselamatan rekanan jarang diperiksa secara seragam sebelum kontrak ditandatangani.</p></article>
            <article class="card"><b>Pekerja masuk tanpa syarat lengkap</b><p>Surat sehat, SIO, sertifikat kompetensi, atau induksi sudah kedaluwarsa, tetapi pekerja tetap lolos gerbang karena tidak ada yang mencocokkan.</p></article>
            <article class="card"><b>Izin kerja kertas sulit diawasi</b><p>Daftar periksa tidak lengkap, hasil uji gas tidak tercatat, izin melewati batas waktu, dan arsip sulit dicari saat investigasi.</p></article>
            <article class="card"><b>Risiko hukum dan temuan audit</b><p>UU No. 1 Tahun 1970 dan PP No. 50 Tahun 2012 menempatkan pengurus tempat kerja sebagai pihak yang bertanggung jawab. Pengendalian kontraktor juga menjadi butir audit SMK3 dan ISO 45001.</p></article>
            <article class="card"><b>Manajemen tidak punya gambaran utuh</b><p>Berapa rekanan yang layak, siapa yang bermasalah, berapa pekerja di lokasi hari ini, dan izin mana yang masih terbuka sulit dijawab dengan cepat.</p></article>
        </div>
    </div>
</section>

<section id="penyebab" class="sec dark">
    <div class="wrap">
        <span class="kicker">02 · Akar penyebab</span>
        <h2>Mengapa masalah itu terus berulang</h2>
        <div class="causes">
            <div class="cause"><span>1</span><div><b>Data tersebar</b><p>Dokumen rekanan ada di email, map fisik, spreadsheet, dan grup percakapan. Tidak ada satu sumber kebenaran.</p></div></div>
            <div class="cause"><span>2</span><div><b>Tidak ada standar penilaian</b><p>Setiap evaluator menilai dengan caranya sendiri. Tidak ada bobot, butir kritis, atau ambang kelulusan yang disepakati.</p></div></div>
            <div class="cause"><span>3</span><div><b>Tahapan terputus</b><p>Hasil prakualifikasi tidak terhubung ke undangan tender, kontrak tidak terhubung ke kartu akses, dan kartu tidak terhubung ke izin kerja.</p></div></div>
            <div class="cause"><span>4</span><div><b>Evaluasi hanya sekali</b><p>Rekanan dinilai saat mendaftar lalu dilupakan. Masa berlaku dokumen dan kinerja K3 di lapangan tidak dipantau.</p></div></div>
            <div class="cause"><span>5</span><div><b>Peran tidak jelas</b><p>Pengadaan, pemilik pekerjaan, dan tim HSE saling menunggu. Tidak jelas siapa menilai, siapa menyetujui, dan siapa mengawasi.</p></div></div>
            <div class="cause"><span>6</span><div><b>Bergantung pada ingatan orang</b><p>Pengetahuan tentang rekanan bermasalah hilang saat personel berganti, sehingga rekanan yang sama terpilih kembali.</p></div></div>
        </div>
    </div>
</section>

<section id="solusi" class="sec">
    <div class="wrap">
        <span class="kicker">03 · Solusi Rekana</span>
        <h2>Enam tahap IOGP 423, satu alur yang saling mengunci</h2>
        <p class="sub">Rekana mengikuti kerangka IOGP Report 423 dan memadukannya dengan ISO 45001 klausul 8.1.4, SMK3 (PP 50/2012), SMKK konstruksi, dan pedoman CSMS migas. Setiap tahap menjadi syarat tahap berikutnya, sehingga rekanan atau pekerja yang belum memenuhi syarat tertahan secara otomatis.</p>
        <div class="steps6">
            <article><i>1</i><b>Penilaian risiko pekerjaan</b><p>Matriks 5×5 untuk setiap aktivitas. Bahaya seperti ruang terbatas, kerja panas, ketinggian, amonia, dan H2S langsung menjadikan pekerjaan berisiko Tinggi. Mode kontrak IOGP 1, 2, atau 3 menentukan pembagian tanggung jawab.</p></article>
            <article><i>2</i><b>Prakualifikasi (PQ)</b><p>Kuesioner berbobot dengan butir kritis: satu butir kritis bernilai nol berarti gagal. Penilai dan penyetuju harus orang berbeda. Rekanan lulus mendapat sertifikat CSMS dua tahun dengan kode QR verifikasi.</p></article>
            <article><i>3</i><b>Seleksi</b><p>Undangan tender hanya bisa dikirim ke rekanan yang sertifikatnya mencakup kategori risiko pekerjaan. HSE Plan dinilai dengan rubrik enam kriteria sebelum digabung dengan nilai teknis.</p></article>
            <article><i>4</i><b>Mobilisasi dan orientasi</b><p>Kick-off dan bridging document untuk pekerjaan berisiko. Induksi dengan pre-test dan post-test. Kartu akses QR hanya terbit bila surat sehat, induksi, dan kontrak masih berlaku, lalu kedaluwarsa otomatis.</p></article>
            <article><i>5</i><b>Pengawasan: izin kerja digital</b><p>Izin kerja panas, ruang terbatas, ketinggian, LOTO, dan umum dengan daftar periksa, batas uji gas, titik isolasi, maksimal 12 jam, dan persetujuan oleh pihak yang berwenang.</p></article>
            <article><i>6</i><b>Evaluasi kinerja</b><p>Dasbor monitoring, rapor rekanan, LTIFR dan TRIR, serta matriks keputusan yang memisahkan rekanan andalan, perlu dipantau, perlu dibina, dan sebaiknya dihindari.</p></article>
        </div>

        <div class="feature-rows">
            <div class="feature">
                <div><span class="kicker">Dasbor owner</span><h3>Keputusan cepat dari satu layar</h3>
                    <ul class="checks"><li>Daftar "perlu keputusan sekarang" yang diurutkan dari yang paling mendesak</li><li>Corong rekanan dari pendaftaran sampai izin mulai kerja</li><li>Matriks skor PQ terhadap LTIFR, tren izin kerja, dan jatuh tempo dokumen</li><li>Rapor rekanan dengan rekomendasi: andalan, pantau ketat, perpanjang PQ, tinjau ulang</li></ul></div>
                <img src="{{ asset('img/layar/dasbor-owner-grafik.png') }}" alt="Grafik dasbor owner (data simulasi)" loading="lazy">
            </div>
            <div class="feature rev">
                <div><span class="kicker">Portal kontraktor gratis</span><h3>Rekanan mengurus kewajibannya sendiri</h3>
                    <ul class="checks"><li>Satu akun untuk semua perusahaan pemberi kerja yang memakai Rekana</li><li>Status gerbang setiap pekerja: siap masuk atau tertahan beserta alasannya</li><li>Daftar hal yang harus diurus: klarifikasi PQ, undangan tender, surat sehat, dokumen</li><li>Kemajuan kontrak dan riwayat izin kerja</li></ul></div>
                <img src="{{ asset('img/layar/dasbor-kontraktor.png') }}" alt="Dasbor portal kontraktor (data simulasi)" loading="lazy">
            </div>
        </div>

        <div class="cards four small">
            <article class="card"><b>Template siap pakai</b><p>Manufaktur pangan (ISO 45001 dan SMK3, termasuk keamanan pangan dan halal), hulu migas (H2S, well control, SIMOPS), dan konstruksi gedung (SMKK, perancah, crane, gondola).</p></article>
            <article class="card"><b>Peran dan empat mata</b><p>Admin, pimpinan, pemilik pekerjaan, pengadaan, evaluator HSE, pengawas, dan pengamat. Penilai tidak bisa menyetujui penilaiannya sendiri.</p></article>
            <article class="card"><b>Jejak audit</b><p>Setiap perubahan status tercatat siapa dan kapan, siap ditunjukkan saat audit SMK3, ISO 45001, atau investigasi insiden.</p></article>
            <article class="card"><b>Opsi Advance: RTLS</b><p>Pelacakan posisi pekerja di area berbahaya secara waktu nyata dapat ditambahkan sebagai modul berbayar terpisah.</p></article>
        </div>
    </div>
</section>

<section id="langkah" class="sec alt">
    <div class="wrap">
        <span class="kicker">04 · Langkah memanfaatkan</span>
        <h2>Cara mulai memakai Rekana</h2>
        <ol class="timeline">
            <li><b>Siapkan perusahaan</b><p>Kami aktifkan ruang kerja perusahaan Anda. Admin menambahkan area kerja beserta kelas bahayanya, lalu mengundang pengguna sesuai peran.</p></li>
            <li><b>Pilih dan sesuaikan template PQ</b><p>Salin template yang paling dekat dengan industri Anda, lalu sesuaikan butir, bobot, butir kritis, dan ambang kelulusan dengan prosedur internal.</p></li>
            <li><b>Undang rekanan ke portal kontraktor</b><p>Rekanan mendaftar gratis, melengkapi profil, dokumen legal, statistik K3 tiga tahun, dan data pekerja beserta kompetensinya.</p></li>
            <li><b>Jalankan prakualifikasi</b><p>Rekanan mengisi kuesioner dan mengunggah bukti. Evaluator HSE menilai, pimpinan menyetujui, dan sertifikat CSMS terbit otomatis.</p></li>
            <li><b>Nilai risiko dan seleksi setiap pekerjaan</b><p>Pemilik pekerjaan menilai risiko, pengadaan mengundang rekanan yang layak, dan HSE Plan dinilai sebelum pemenang ditetapkan.</p></li>
            <li><b>Mobilisasi, izin kerja, dan pantau</b><p>Kick-off, induksi, terbitkan kartu akses, jalankan izin kerja harian, lalu pantau dasbor untuk keputusan berikutnya.</p></li>
        </ol>
    </div>
</section>

<section id="demo" class="sec dark">
    <div class="wrap">
        <span class="kicker">05 · Coba sekarang</span>
        <h2>Pilih salah satu dari tiga data demo</h2>
        <p class="sub">Masuk sebagai tamu tanpa membuat akun. Pilih sisi perusahaan pemberi kerja (owner) atau sisi kontraktor; semua menu, formulir, dan tombol tindakan dapat dicoba, tetapi perubahan tidak disimpan.</p>
        <div class="disclaimer"><span>DATA SIMULASI</span><p><b>Seluruh data demo adalah data contoh simulasi untuk keperluan demo software Rekana.</b> Profil perusahaan (nama, lokasi, dan bidang usaha) diambil dari laporan tahunan yang dipublikasikan. Nama rekanan, staf, pekerja, skor, statistik K3, dan seluruh aktivitas adalah rekaan dan tidak berasal dari sistem atau dokumen internal perusahaan tersebut. Demo ini tidak menyiratkan hubungan kerja sama dengan perusahaan mana pun.</p></div>
        <div class="demos">
            @forelse ($samples as $o)
                @php([$short, $industry, $story] = DemoController::story($o))
                <article class="demo">
                    <span class="demo-tag">Simulasi</span>
                    <h3>{{ $short }}</h3>
                    <small>{{ $industry }}</small>
                    <p>{{ $story }}</p>
                    <div class="demo-cta">
                        <a class="btn primary" href="{{ DemoController::guestUrl($o->slug) }}">Tamu sebagai Owner</a>
                        @if ($c = DemoController::featuredContractor($o))
                            <a class="btn ghost" href="{{ DemoController::guestUrl($o->slug, true) }}">Tamu sebagai Kontraktor</a>
                        @endif
                    </div>
                </article>
            @empty
                <p class="sub">Data demo belum dimuat di server ini.</p>
            @endforelse
        </div>
    </div>
</section>

<section class="sec">
    <div class="wrap refs">
        <div><span class="kicker">Acuan</span><h2>Disusun mengikuti standar yang dipakai auditor</h2></div>
        <ul>
            <li><b>IOGP Report 423</b> Managing HSE in a contractor environment: enam tahap pengelolaan kontraktor dan tiga mode kontrak.</li>
            <li><b>ISO 45001:2018 klausul 8.1.4</b> pengendalian kontraktor dan pengadaan.</li>
            <li><b>PP No. 50 Tahun 2012</b> Sistem Manajemen K3 (SMK3), termasuk pengendalian pekerjaan pihak ketiga.</li>
            <li><b>Permen PUPR No. 10 Tahun 2021</b> Sistem Manajemen Keselamatan Konstruksi (SMKK).</li>
            <li><b>Pedoman CSMS hulu migas</b> yang dipakai KKKS untuk prakualifikasi kontraktor.</li>
        </ul>
    </div>
</section>

<section class="cta-band">
    <div class="wrap cta-in">
        <div><h2>Siap merapikan pengelolaan kontraktor Anda?</h2><p>Model SaaS per perusahaan. Portal kontraktor gratis.</p></div>
        <div class="hero-cta"><a href="#demo" class="btn primary lg">Coba demo</a><a href="{{ $loginUrl }}" class="btn light lg">Masuk</a><a href="{{ $contractorLoginUrl }}" class="btn light lg">Portal kontraktor</a></div>
    </div>
</section>

<footer class="foot"><div class="wrap">Rekana · Contractor Safety Management System · Rucisoft · Data pada demo adalah simulasi.</div></footer>
</body>
</html>
