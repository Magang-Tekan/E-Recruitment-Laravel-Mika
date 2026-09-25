<?php

namespace Database\Seeders;

use App\Models\PapiAspect;
use App\Models\PapiFactor;
use App\Models\PapiNorm;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PapiKostickMasterSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Data 7 Aspek Utama PAPI Kostick
        $aspects = [
            1 => ['name' => 'Arah Kerja', 'english_name' => 'Work Direction', 'order_number' => 1],
            2 => ['name' => 'Kepemimpinan', 'english_name' => 'Leadership', 'order_number' => 2],
            3 => ['name' => 'Aktivitas', 'english_name' => 'Activity', 'order_number' => 3],
            4 => ['name' => 'Pergaulan', 'english_name' => 'Social Nature', 'order_number' => 4],
            5 => ['name' => 'Gaya Kerja', 'english_name' => 'Work Style', 'order_number' => 5],
            6 => ['name' => 'Sifat', 'english_name' => 'Temperament', 'order_number' => 6],
            7 => ['name' => 'Ketaatan', 'english_name' => 'Followership', 'order_number' => 7],
        ];

        $aspectIds = [];
        foreach ($aspects as $key => $asp) {
            $record = PapiAspect::updateOrCreate(
                ['name' => $asp['name']],
                [
                    'english_name' => $asp['english_name'],
                    'order_number' => $asp['order_number'],
                ]
            );
            $aspectIds[$key] = $record->id;
        }

        // 2. Data 20 Faktor (Peran / Role & Kebutuhan / Need)
        $factors = [
            // Arah Kerja
            [
                'aspect_id' => $aspectIds[1],
                'code' => 'N',
                'name' => 'Penyelesaian secara prestasi',
                'english_name' => 'Need to finish task',
                'type' => 'need',
                'description' => 'Mengukur kebutuhan untuk menyelesaikan tugas secara tuntas dan mandiri dengan standar hasil kerja yang baik.',
            ],
            [
                'aspect_id' => $aspectIds[1],
                'code' => 'G',
                'name' => 'Peranan sebagai pekerja keras',
                'english_name' => 'Hard intense worked',
                'type' => 'role',
                'description' => 'Mengukur persepsi individu sebagai pekerja keras yang mencurahkan tenaga, ketekunan, dan dedikasi dalam bekerja.',
            ],
            [
                'aspect_id' => $aspectIds[1],
                'code' => 'A',
                'name' => 'Hasrat untuk berprestasi',
                'english_name' => 'Need to achieve',
                'type' => 'need',
                'description' => 'Mengukur ambisi pribadi dan dorongan untuk mencapai sukses, keunggulan, serta standar prestasi tinggi.',
            ],

            // Kepemimpinan
            [
                'aspect_id' => $aspectIds[2],
                'code' => 'L',
                'name' => 'Peran sebagai pimpinan',
                'english_name' => 'Leadership role',
                'type' => 'role',
                'description' => 'Tingkat dimana seseorang memproyeksikan dirinya sebagai pemimpin dan menggunakan orang lain untuk mencapai tujuan.',
            ],
            [
                'aspect_id' => $aspectIds[2],
                'code' => 'P',
                'name' => 'Pengendalian orang lain',
                'english_name' => 'Need to control others',
                'type' => 'need',
                'description' => 'Kebutuhan untuk menerima tanggung jawab atas pekerjaan dan tindakan orang lain serta mengatur bawahan.',
            ],
            [
                'aspect_id' => $aspectIds[2],
                'code' => 'I',
                'name' => 'Mudah dalam mengambil keputusan',
                'english_name' => 'Ease in decision making',
                'type' => 'role',
                'description' => 'Peran dan kelancaran individu dalam membuat keputusan, mulai dari berhati-hati hingga berani dan tegas.',
            ],

            // Aktivitas
            [
                'aspect_id' => $aspectIds[3],
                'code' => 'T',
                'name' => 'Tipe selalu sibuk',
                'english_name' => 'Pace',
                'type' => 'role',
                'description' => 'Mengukur tempo kerja dan keaktifan internal/mental dalam menangani berbagai aktivitas pekerjaan.',
            ],
            [
                'aspect_id' => $aspectIds[3],
                'code' => 'V',
                'name' => 'Tipe yang bersemangat',
                'english_name' => 'Vigorous type',
                'type' => 'role',
                'description' => 'Mengukur energi fisik, stamina, keaktifan gerak, dan semangat dinamis dalam bekerja.',
            ],

            // Pergaulan / Relasi Sosial
            [
                'aspect_id' => $aspectIds[4],
                'code' => 'X',
                'name' => 'Kebutuhan untuk mendapatkan perhatian',
                'english_name' => 'Need to be noticed',
                'type' => 'need',
                'description' => 'Mengukur kebutuhan untuk dikenal, diapresiasi, dan mendapatkan perhatian nyata dari orang lain.',
            ],
            [
                'aspect_id' => $aspectIds[4],
                'code' => 'S',
                'name' => 'Pergaulan luas',
                'english_name' => 'Social extension',
                'type' => 'role',
                'description' => 'Tingkat kepercayaan dalam relasi interpersonal dan minat terhadap interaksi sosial kemasyarakatan.',
            ],
            [
                'aspect_id' => $aspectIds[4],
                'code' => 'B',
                'name' => 'Kebutuhan berkelompok',
                'english_name' => 'Need to belong to groups',
                'type' => 'need',
                'description' => 'Kebutuhan untuk menjadi bagian dari kelompok dan rasa keterikatan/penerimaan dalam tim.',
            ],
            [
                'aspect_id' => $aspectIds[4],
                'code' => 'O',
                'name' => 'Kebutuhan untuk dekat dan menyayangi',
                'english_name' => 'Need for closeness and affection',
                'type' => 'need',
                'description' => 'Kebutuhan akan kehangatan hubungan antarpribadi dan kedekatan emosional di lingkungan kerja.',
            ],

            // Gaya Kerja
            [
                'aspect_id' => $aspectIds[5],
                'code' => 'R',
                'name' => 'Tipe teoritikal',
                'english_name' => 'Theoretical type',
                'type' => 'role',
                'description' => 'Kecenderungan berpikir konseptual, analitis, penalaran logis, dan pertimbangan teoritis.',
            ],
            [
                'aspect_id' => $aspectIds[5],
                'code' => 'D',
                'name' => 'Suka pekerjaan yang terperinci',
                'english_name' => 'Interest in working with details',
                'type' => 'role',
                'description' => 'Minat dan ketelitian dalam menangani hal-hal detail, spesifik, dan memerlukan kecermatan tinggi.',
            ],
            [
                'aspect_id' => $aspectIds[5],
                'code' => 'C',
                'name' => 'Tipe teratur',
                'english_name' => 'Organized type',
                'type' => 'role',
                'description' => 'Tingkat keteraturan, kerapian, metode kerja sistematis, dan ketaatan terhadap struktur kerja.',
            ],

            // Sifat / Temperamen
            [
                'aspect_id' => $aspectIds[6],
                'code' => 'Z',
                'name' => 'Hasrat untuk berubah',
                'english_name' => 'Need for change',
                'type' => 'need',
                'description' => 'Kebutuhan akan variasi, hal baru, inovasi, fleksibilitas lingkungan, serta adaptasi perubahan.',
            ],
            [
                'aspect_id' => $aspectIds[6],
                'code' => 'E',
                'name' => 'Pengendalian emosi',
                'english_name' => 'Emotional resistant',
                'type' => 'role',
                'description' => 'Pengendalian perasaan, kestabilan emosi, ketenangan di bawah tekanan, serta pengekangan diri.',
            ],
            [
                'aspect_id' => $aspectIds[6],
                'code' => 'K',
                'name' => 'Agresi',
                'english_name' => 'Need to be forceful',
                'type' => 'need',
                'description' => 'Dorongan bersaing, ketegasan pendirian, agresi positif dalam pekerjaan, dan penanganan konflik.',
            ],

            // Ketaatan / Posisi Atasan-Bawahan
            [
                'aspect_id' => $aspectIds[7],
                'code' => 'F',
                'name' => 'Dukungan terhadap atasan',
                'english_name' => 'Need to support authority',
                'type' => 'need',
                'description' => 'Kebutuhan untuk loyal, mendukung, dan membantu figur otoritas/atasan secara pribadi maupun profesional.',
            ],
            [
                'aspect_id' => $aspectIds[7],
                'code' => 'W',
                'name' => 'Kebutuhan taat pada aturan dan pengarahan',
                'english_name' => 'Need for rules and supervision',
                'type' => 'need',
                'description' => 'Kebutuhan akan aturan baku, kepatuhan prosedur kerja, dan pengarahan serta supervisi dari manajemen.',
            ],
        ];

        $factorMap = [];
        foreach ($factors as $factorData) {
            $f = PapiFactor::updateOrCreate(
                ['code' => $factorData['code']],
                $factorData
            );
            $factorMap[$factorData['code']] = $f->id;
        }

        // 3. Data Norma Interpretasi Skor (disesuaikan dengan rentang nilai referensi PAPI Kostick)
        $norms = [
            // N - Kebutuhan Menyelesaikan Tugas Secara Mandiri
            ['code' => 'N', 'min' => 0, 'max' => 2, 'desc' => 'Menunda atau menghindari pekerjaan', 'interpret' => 'Cenderung ragu-ragu dalam situasi pengambilan keputusan, menunda atau menghindari situasi pengambilan keputusan'],
            ['code' => 'N', 'min' => 3, 'max' => 4, 'desc' => 'Berhati-hati atau ragu', 'interpret' => 'Berhati-hati dan cenderung ragu-ragu'],
            ['code' => 'N', 'min' => 4, 'max' => 6, 'desc' => 'Cukup bertanggung jawab pada pekerjaan', 'interpret' => 'Cukup bertanggung jawab terhadap pekerjaan'],
            ['code' => 'N', 'min' => 6, 'max' => 9, 'desc' => 'Tekun, tanggung jawab tinggi', 'interpret' => 'Ketekunan, tanggung jawab terhadap tugas tinggi'],

            // A - Kebutuhan Berprestasi
            ['code' => 'A', 'min' => 0, 'max' => 5, 'desc' => 'Ketidakpastian tujuan, kepuasan dalam suatu pekerjaan, tidak ada usaha lebih', 'interpret' => 'Mencerminkan ketidakpastian tujuan. Juga mencerminkan kepuasan dalam suatu pekerjaan, tidak perlu melanjutkan usaha untuk sukses'],
            ['code' => 'A', 'min' => 6, 'max' => 9, 'desc' => 'Tujuan jelas, kebutuhan sukses dan ambisi tinggi', 'interpret' => 'Tujuan-tujuan didefinisikan secara jelas, kebutuhan untuk sukses tinggi, ambisi pribadi tinggi'],

            // G - Peran Pekerja Keras
            ['code' => 'G', 'min' => 3, 'max' => 4, 'desc' => 'Bekerja untuk kesenangan saja, bukan hasil optimal', 'interpret' => 'Bekerja hanya untuk mengejar kesenangan saja bukan untuk memberikan suatu hasil yang baik'],
            ['code' => 'G', 'min' => 4, 'max' => 7, 'desc' => 'Kemauan bekerja keras tinggi', 'interpret' => 'Kemauan bekerja keras tinggi'],

            // L - Peran Pemimpin
            ['code' => 'L', 'min' => 0, 'max' => 4, 'desc' => 'Cenderung tidak secara aktif menggunakan orang lain dalam bekerja', 'interpret' => 'Cenderung tidak suka aktif menggunakan orang lain dalam bekerja'],
            ['code' => 'L', 'min' => 5, 'max' => 9, 'desc' => 'Memproyeksikan diri sebagai pemimpin, menggunakan orang lain untuk mencapai tujuan', 'interpret' => 'Yaitu tingkat dimana seseorang memproyeksikan dirinya sebagai pemimpin suatu tingkat, dimana ia mencoba menggunakan orang lain untuk mencapai tujuannya. Nilai S menunjukkan apakah pola kepemimpinannya bersifat persuasive, demokratis, atau otoriter'],

            // P - Kebutuhan Mengatur Orang Lain
            ['code' => 'P', 'min' => 0, 'max' => 4, 'desc' => 'Menurunnya keinginan untuk bertanggung jawab pada pekerjaan dan tindakan orang lain', 'interpret' => 'Menurunnya keinginan untuk bertanggung jawab terhadap pekerjaan dan tindakan orang lain'],
            ['code' => 'P', 'min' => 5, 'max' => 9, 'desc' => 'Tingkat kebutuhan untuk menerima tanggung jawab orang lain, menjadi orang yang bertanggung jawab', 'interpret' => 'Tingkat kebutuhan untuk menerima tanggung jawab orang lain, menjadi orang yang bertanggung jawab'],

            // I - Peran Membuat Keputusan
            ['code' => 'I', 'min' => 0, 'max' => 2, 'desc' => 'Ragu – menolak mengambil keputusan', 'interpret' => 'Ragu-ragu sampai penundaan/menolak situasi pengambilan keputusan'],
            ['code' => 'I', 'min' => 3, 'max' => 4, 'desc' => 'Berhati-hati membuat keputusan', 'interpret' => 'Berhati-hati sampai ragu-ragu dalam membuat keputusan'],
            ['code' => 'I', 'min' => 5, 'max' => 7, 'desc' => 'Berhati-hati – lancar dan mudah mengambil keputusan', 'interpret' => 'Mudah dan lancar sampai berhati-hati dalam membuat keputusan'],
            ['code' => 'I', 'min' => 8, 'max' => 9, 'desc' => 'Tidak ragu dalam mengambil keputusan', 'interpret' => 'Tidak ragu-ragu dalam proses pengambilan keputusan'],

            // T - Peran Sibuk
            ['code' => 'T', 'min' => 0, 'max' => 3, 'desc' => 'Melakukan segala sesuatu menurut kemauannya sendiri', 'interpret' => 'Melakukan segala sesuatu menurut kemauannya sendiri'],
            ['code' => 'T', 'min' => 4, 'max' => 6, 'desc' => 'Tergolong aktif secara internal dan mental', 'interpret' => 'Tergolong aktif secara internal dan mental'],

            // V - Peran Penuh Semangat
            ['code' => 'V', 'min' => 0, 'max' => 4, 'desc' => 'Cenderung pasif', 'interpret' => 'Keaktifannya tergolong rendah, cenderung pasif (hanya duduk-duduk saja)'],
            ['code' => 'V', 'min' => 5, 'max' => 7, 'desc' => 'Aktif secara fisik, cenderung sportif', 'interpret' => 'Keaktifannya secara fisik tergolong agak baik, cenderung tipe sportif'],

            // X - Kebutuhan Untuk Diperhatikan
            ['code' => 'X', 'min' => 0, 'max' => 1, 'desc' => 'Cenderung pemalu', 'interpret' => 'Cenderung pemalu, suka menyendiri'],
            ['code' => 'X', 'min' => 2, 'max' => 3, 'desc' => 'Rendah hati, tulus', 'interpret' => 'Rendah hati, tulus'],
            ['code' => 'X', 'min' => 4, 'max' => 5, 'desc' => 'Memiliki pola perilaku yang unik', 'interpret' => 'Khusus, memiliki pola yang nyata'],
            ['code' => 'X', 'min' => 6, 'max' => 9, 'desc' => 'Membutuhkan perhatian nyata', 'interpret' => 'Membutuhkan perhatian yang nyata'],

            // S - Peran Hubungan Sosial
            ['code' => 'S', 'min' => 0, 'max' => 5, 'desc' => 'Perhatian rendah terhadap hubungan sosial, kurang percaya pada orang lain', 'interpret' => 'Memiliki penilaian yang rendah terhadap hubungan sosial, cenderung kurang percaya pada orang lain'],
            ['code' => 'S', 'min' => 6, 'max' => 9, 'desc' => 'Kepercayaan tinggi dalam hubungan sosial, suka interaksi sosial', 'interpret' => 'Tingkat kepercayaan dalam hubungan sosial tinggi, menyukai interaksi sosial'],

            // B - Kebutuhan Diterima Dalam Kelompok
            ['code' => 'B', 'min' => 0, 'max' => 3, 'desc' => 'Selektif', 'interpret' => 'Selektif, secara umum melepaskan diri dari kelompok'],
            ['code' => 'B', 'min' => 4, 'max' => 5, 'desc' => 'Butuh diterima, tapi tidak mudah dipengaruhi kelompok', 'interpret' => 'Ada kebutuhan untuk diterima dan diakui tetapi tidak terlalu mudah dipengaruhi oleh kelompok'],
            ['code' => 'B', 'min' => 6, 'max' => 9, 'desc' => 'Butuh disukai dan diakui, mudah dipengaruhi', 'interpret' => 'Kebutuhan untuk disukai, diakui oleh semua orang. Mudah dipengaruhi kelompok'],

            // O - Kebutuhan Kedekatan dan Kasih Sayang
            ['code' => 'O', 'min' => 0, 'max' => 2, 'desc' => 'Tidak suka hubungan perorangan', 'interpret' => 'Tidak menyukai hubungan antar pribadi. Tidak menyukai interaksi perseorangan'],
            ['code' => 'O', 'min' => 3, 'max' => 4, 'desc' => 'Sadar akan hubungan perorangan, tapi tidak terlalu tergantung', 'interpret' => 'Sadar akan kebutuhan antar pribadi tetapi dapat melepaskan diri dari orang lain/tidak terlalu tergantung'],
            ['code' => 'O', 'min' => 5, 'max' => 9, 'desc' => 'Sangat tergantung, butuh penerimaan diri', 'interpret' => 'Ketergantungan yang sangat besar akan pengakuan dan penerimaan diri'],

            // R - Peran Orang Yang Teoritis
            ['code' => 'R', 'min' => 0, 'max' => 4, 'desc' => 'Kurang perhatian, bersifat praktis', 'interpret' => 'Kurang perhatian-praktis'],
            ['code' => 'R', 'min' => 5, 'max' => 9, 'desc' => 'Nilai-nilai penalaran tergolong tinggi', 'interpret' => 'Penekanan pada nilai-nilai penalaran tergolong tinggi'],

            // D - Peran Bekerja Dengan Hal-Hal Rinci
            ['code' => 'D', 'min' => 0, 'max' => 3, 'desc' => 'Menyadari kebutuhan akan kecermatan, tetapi tidak berminat bekerja detail', 'interpret' => 'Menyadari kebutuhan akan kecermatan tetapi secara pribadi tidak berminat menangani hal-hal detail'],
            ['code' => 'D', 'min' => 4, 'max' => 9, 'desc' => 'Minat tinggi untuk bekerja secara detail', 'interpret' => 'Minat menangani hal-hal detail tergolong tinggi'],

            // C - Peran Mengatur
            ['code' => 'C', 'min' => 0, 'max' => 2, 'desc' => 'Fleksibel – tidak teratur', 'interpret' => 'Fleksibilitas sampai ketidak-teraturan'],
            ['code' => 'C', 'min' => 3, 'max' => 5, 'desc' => 'Teratur tetapi tidak tergolong fleksibel', 'interpret' => 'Tergolong teratur tetapi dengan fleksibilitas'],
            ['code' => 'C', 'min' => 6, 'max' => 9, 'desc' => 'Keteraturan tinggi cenderung kaku', 'interpret' => 'Memiliki keteraturan yang sangat tinggi, cenderung kaku'],

            // Z - Kebutuhan Untuk Berubah
            ['code' => 'Z', 'min' => 0, 'max' => 2, 'desc' => 'Tidak suka berubah', 'interpret' => 'Tidak menyukai dan menolak perubahan. Cenderung menggunakan pendekatan-pendekatan tradisional'],
            ['code' => 'Z', 'min' => 3, 'max' => 4, 'desc' => 'Tidak suka perubahan jika dipaksakan', 'interpret' => 'Tidak suka akan perubahan jika dipaksakan kepadanya'],
            ['code' => 'Z', 'min' => 5, 'max' => 6, 'desc' => 'Mudah menyesuaikan diri', 'interpret' => 'Mudah menyesuaikan diri'],
            ['code' => 'Z', 'min' => 6, 'max' => 7, 'desc' => 'Membuat perubahan yang selektif, berfikir jauh ke depan', 'interpret' => 'Pembuat perubahan yang selektif. Berpikir jauh ke depan'],
            ['code' => 'Z', 'min' => 8, 'max' => 9, 'desc' => 'Mudah gelisah, frustasi, karena segala sesuatu tidak berjalan fantastis', 'interpret' => 'Mudah gelisah, mudah frustrasi mungkin karena segala sesuatu bergerak tidak cukup cepat'],

            // E - Peran Pengendalian Emosi
            ['code' => 'E', 'min' => 0, 'max' => 1, 'desc' => 'Terbuka, cepat bereaksi, tidak normative', 'interpret' => 'Terbuka , cepat bereaksi , tidak memikirkan nilai dalam pengendalian diri'],
            ['code' => 'E', 'min' => 2, 'max' => 3, 'desc' => 'Terbuka', 'interpret' => 'Terbuka'],
            ['code' => 'E', 'min' => 4, 'max' => 6, 'desc' => 'Punya pendekatan emosional seimbang, mampu mengendalikan', 'interpret' => 'Memiliki pendekatan emosional yang seimbang. Mampu mengendalikan perasaannya'],
            ['code' => 'E', 'min' => 7, 'max' => 9, 'desc' => 'Sangat normative, kebutuhan pengendalian diri yang berlebihan', 'interpret' => 'Sangat normative , kebutuhan pengendalian diri yang berlebihan'],

            // K - Kebutuhan Untuk Agresif
            ['code' => 'K', 'min' => 0, 'max' => 2, 'desc' => 'Menghindari masalah, menolak untuk mengenali situasi sebagai masalah', 'interpret' => 'Selalu menghindari masalah. Cenderung mengabaikan situasi atau cenderung menolak untuk mengenali sesuatu sebagai sebuah masalah'],
            ['code' => 'K', 'min' => 3, 'max' => 4, 'desc' => 'Suka lingkungan tenang, menghindari konflik', 'interpret' => 'Lebih menyukai lingkungan yang tenang. Menghindari konflik. Cenderung menunda masalah'],
            ['code' => 'K', 'min' => 5, 'max' => 5, 'desc' => 'Keras kepala', 'interpret' => 'Kukuh pendirian, cenderung keras kepala'],
            ['code' => 'K', 'min' => 6, 'max' => 7, 'desc' => 'Agresi berhubungan dengan kerja, dorongan semangat bersaing', 'interpret' => 'Agresi pribadi yang berkaitan dengan pekerjaan, dorongan dan semangat bersaing'],
            ['code' => 'K', 'min' => 8, 'max' => 9, 'desc' => 'Agresif, cenderung defensive', 'interpret' => 'Agresif, cenderung defensive'],

            // F - Kebutuhan Membantu Atasan
            ['code' => 'F', 'min' => 0, 'max' => 1, 'desc' => 'Cenderung egois, kemungkinan bisa memberontak', 'interpret' => 'Cenderung egois, kemungkinan bisa bersikap memberontak'],
            ['code' => 'F', 'min' => 2, 'max' => 3, 'desc' => 'Mengurus kepentingan sendiri', 'interpret' => 'Mengurus kepentingan diri sendiri'],
            ['code' => 'F', 'min' => 4, 'max' => 5, 'desc' => 'Setia terhadap perusahaan', 'interpret' => 'Setia terhadap perusahaan'],
            ['code' => 'F', 'min' => 6, 'max' => 9, 'desc' => 'Bersikap setia dan membantu, kemungkinan bantuannya bersifat politis', 'interpret' => 'Bersikap setia dan membantu secara pribadi, ada kemungkinan bantuannya bermotivasi politis'],

            // W - Kebutuhan Mengikuti Aturan dan Pengawasan
            ['code' => 'W', 'min' => 0, 'max' => 3, 'desc' => 'Berorientasi pada tujuan, mandiri', 'interpret' => 'Berorientasi pada tujuan, mandiri'],
            ['code' => 'W', 'min' => 4, 'max' => 5, 'desc' => 'Kebutuhan akan pengarahan dan harapan yang dirumuskan untuknya', 'interpret' => 'Kebutuhan akan pengarahan dan harapan yang dirumuskan untuknya'],
            ['code' => 'W', 'min' => 6, 'max' => 9, 'desc' => 'Meningkatnya orientasi terhadap tugas dan membutuhkan instruksi yang jelas', 'interpret' => 'Meningkatnya orientasi terhadap tugas dan membutuhkan instruksi yang jelas'],
        ];

        PapiNorm::truncate();

        foreach ($norms as $norm) {
            PapiNorm::create([
                'factor_id' => $factorMap[$norm['code']],
                'factor_code' => $norm['code'],
                'min_score' => $norm['min'],
                'max_score' => $norm['max'],
                'interpretation' => $norm['interpret'],
                'description' => $norm['desc'],
            ]);
        }
    }
}
