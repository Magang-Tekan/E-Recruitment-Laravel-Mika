<?php

namespace Database\Seeders;

use App\Models\QuestionBank;
use App\Models\QuestionOption;
use App\Models\TestCategory;
use Illuminate\Database\Seeder;

class PapiKostickQuestionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Dapatkan atau buat Kategori Tes PAPI Kostick
        $category = TestCategory::firstOrCreate(
            ['name' => 'Tes Kepribadian (PAPI Kostick)'],
            ['description' => 'Tes profil kepribadian PAPI Kostick (Personality and Preference Inventory) 90 Pasang Pernyataan.']
        );

        // Hapus soal PAPI Kostick sebelumnya agar tidak duplikat jika seeder dijalankan ulang
        QuestionBank::where('category_id', $category->id)->delete();

        // 90 Pasang Pernyataan PAPI Kostick sesuai dengan dokumen resmi "SOAL PAPI KOSTICK-.pdf"
        // Tag 'a' dan 'b' dipetakan 100% presisi sesuai matriks penilaian skoring Excel
        $questionsData = [
            1 => [
                ['text' => 'Saya seorang pekerja “keras”', 'tag' => 'G'],
                ['text' => 'Saya bukan seorang pemurung', 'tag' => 'E'],
            ],
            2 => [
                ['text' => 'Saya suka bekerja lebih baik dari orang lain', 'tag' => 'A'],
                ['text' => 'Saya suka mengerjakan apa yang sedang saya kerjakan, sampai selesai', 'tag' => 'N'],
            ],
            3 => [
                ['text' => 'Saya suka menunjukkan caranya melaksanakan sesuatu hal', 'tag' => 'P'],
                ['text' => 'Saya ingin bekerja sebaik mungkin', 'tag' => 'A'],
            ],
            4 => [
                ['text' => 'Saya suka berkelakar', 'tag' => 'X'],
                ['text' => 'Saya senang mengatakan kepada orang lain, apa yang harus dilakukannya', 'tag' => 'P'],
            ],
            5 => [
                ['text' => 'Saya suka menggabungkan diri dengan kelompok-kelompok', 'tag' => 'B'],
                ['text' => 'Saya suka diperhatikan oleh kelompok-kelompok', 'tag' => 'X'],
            ],
            6 => [
                ['text' => 'Saya senang bersahabat intim dengan seseorang', 'tag' => 'O'],
                ['text' => 'Saya senang bersahabat dengan sekolompok orang', 'tag' => 'B'],
            ],
            7 => [
                ['text' => 'Saya cepat berubah bila hal itu diperlukan', 'tag' => 'Z'],
                ['text' => 'Saya berusaha untuk intim dengan teman-teman', 'tag' => 'O'],
            ],
            8 => [
                ['text' => 'Saya suka “membalas dendam” bila saya benar-benar disakiti', 'tag' => 'K'],
                ['text' => 'Saya suka melakukan hal-hal yang baru dan berbeda', 'tag' => 'Z'],
            ],
            9 => [
                ['text' => 'Saya ingin atasan saya menyukai saya', 'tag' => 'F'],
                ['text' => 'Saya suka mengatakan kepada orang lain, bila mereka salah', 'tag' => 'K'],
            ],
            10 => [
                ['text' => 'Saya suka mengikuti perintah-perintah yang diberikan kepada saya', 'tag' => 'W'],
                ['text' => 'Saya suka menyenangkan hati orang yang memimpin saya', 'tag' => 'F'],
            ],
            11 => [
                ['text' => 'Saya mencoba sekuat tenaga', 'tag' => 'G'],
                ['text' => 'Saya seorang yang tertib. Saya meletakkan segala sesuatu pada tempatnya', 'tag' => 'C'],
            ],
            12 => [
                ['text' => 'Saya membuat orang lain melakukan apa yang saya inginkan', 'tag' => 'L'],
                ['text' => 'Saya bukan orang yang cepat gusar', 'tag' => 'E'],
            ],
            13 => [
                ['text' => 'Saya suka mengatakan kepada kelompok, apa yang harus dilakukan', 'tag' => 'P'],
                ['text' => 'Saya menekuni satu pekerjaan sampai selesai', 'tag' => 'N'],
            ],
            14 => [
                ['text' => 'Saya ingin tampak bersemangat dan menarik', 'tag' => 'X'],
                ['text' => 'Saya ingin menjadi sangat sukses', 'tag' => 'A'],
            ],
            15 => [
                ['text' => 'Saya suka menyelaraskan diri dengan kelompok', 'tag' => 'B'],
                ['text' => 'Saya suka membantu orang lain menentukan pendapatnya', 'tag' => 'P'],
            ],
            16 => [
                ['text' => 'Saya cemas kalau orang lain tidak menyukai saya', 'tag' => 'O'],
                ['text' => 'Saya senang kalau orang-orang memperhatikan saya', 'tag' => 'X'],
            ],
            17 => [
                ['text' => 'Saya suka mencoba sesuatu yang baru', 'tag' => 'Z'],
                ['text' => 'Saya lebih suka bekerja bersama orang-orang daripada bekerja sendiri', 'tag' => 'B'],
            ],
            18 => [
                ['text' => 'Kadang-kadang saya menyalahkan orang lain bila terjadi sesuatu kesalahan', 'tag' => 'K'],
                ['text' => 'Saya cemas bila seseorang tidak menyukai saya', 'tag' => 'O'],
            ],
            19 => [
                ['text' => 'Saya suka menyenangkan hati orang yang memimpin saya', 'tag' => 'F'],
                ['text' => 'Saya suka mencoba pekerjaan-pekerjaan yang baru dan berbeda', 'tag' => 'Z'],
            ],
            20 => [
                ['text' => 'Saya menyukai petunjuk yang terinci untuk melakukan sesuatu pekerjaan', 'tag' => 'W'],
                ['text' => 'Saya suka mengatakan kepada orang lain bila mereka mengganggu saya', 'tag' => 'K'],
            ],
            21 => [
                ['text' => 'Saya selalu mencoba sekuat tenaga', 'tag' => 'G'],
                ['text' => 'Saya senang bekerja dengan sangat cermat dan hati-hati', 'tag' => 'D'],
            ],
            22 => [
                ['text' => 'Saya adalah seorang pemimpin yang baik', 'tag' => 'L'],
                ['text' => 'Saya mengorganisir tugas-tugas secara baik', 'tag' => 'C'],
            ],
            23 => [
                ['text' => 'Saya mudah menjadi gusar', 'tag' => 'I'],
                ['text' => 'Saya seorang yang lambat dalam membuat keputusan', 'tag' => 'E'],
            ],
            24 => [
                ['text' => 'Saya senang mengerjakan beberapa pekerjaan pada waktu yang bersamaan', 'tag' => 'X'],
                ['text' => 'Bila dalam kelompok, saya lebih suka diam', 'tag' => 'N'],
            ],
            25 => [
                ['text' => 'Saya senang bila diundang', 'tag' => 'B'],
                ['text' => 'Saya ingin melakukan sesuatu lebih baik dari orang lain', 'tag' => 'A'],
            ],
            26 => [
                ['text' => 'Saya suka berteman intim dengan teman-teman saya', 'tag' => 'O'],
                ['text' => 'Saya suka memberi nasihat kepada orang lain', 'tag' => 'P'],
            ],
            27 => [
                ['text' => 'Saya suka melakukan hal-hal yang baru dan berbeda', 'tag' => 'Z'],
                ['text' => 'Saya suka menceritakan keberhasilan saya dalam mengerjakan tugas', 'tag' => 'X'],
            ],
            28 => [
                ['text' => 'Bila saya benar, saya suka mempertahankannya “mati-matian”', 'tag' => 'K'],
                ['text' => 'Saya suka bergabung ke dalam suatu kelompok', 'tag' => 'B'],
            ],
            29 => [
                ['text' => 'Saya tidak mau berbeda dengan orang lain', 'tag' => 'F'],
                ['text' => 'Saya berusaha untuk sangat intim dengan orang-orang', 'tag' => 'O'],
            ],
            30 => [
                ['text' => 'Saya suka diajari mengenai caranya mengerjakan suatu pekerjaan', 'tag' => 'W'],
                ['text' => 'Saya mudah merasa jemu (bosan)', 'tag' => 'Z'],
            ],
            31 => [
                ['text' => 'Saya bekerja “keras”', 'tag' => 'G'],
                ['text' => 'Saya banyak berpikir dan berencana', 'tag' => 'R'],
            ],
            32 => [
                ['text' => 'Saya memimpin kelompok', 'tag' => 'L'],
                ['text' => 'Hal-hal yang kecil (detail) menarik hati saya', 'tag' => 'D'],
            ],
            33 => [
                ['text' => 'Saya cepat dan mudah mengambil keputusan', 'tag' => 'I'],
                ['text' => 'Saya meletakkan segala sesuatu secara rapi dan teratur', 'tag' => 'C'],
            ],
            34 => [
                ['text' => 'Tugas-tugas saya kerjakan secara cepat', 'tag' => 'T'],
                ['text' => 'Saya jarang marah atau sedih', 'tag' => 'E'],
            ],
            35 => [
                ['text' => 'Saya ingin menjadi bagian dari kelompok', 'tag' => 'B'],
                ['text' => 'Pada suatu waktu tertentu, saya hanya ingin mengerjakan satu tugas saja', 'tag' => 'N'],
            ],
            36 => [
                ['text' => 'Saya berusaha untuk intim dengan teman-teman saya', 'tag' => 'O'],
                ['text' => 'Saya berusaha keras untuk menjadi yang terbaik', 'tag' => 'A'],
            ],
            37 => [
                ['text' => 'Saya menyukai mode baju baru dan tipe-tipe mobil baru', 'tag' => 'Z'],
                ['text' => 'Saya ingin menjadi penanggung jawab bagi orang-orang lain', 'tag' => 'P'],
            ],
            38 => [
                ['text' => 'Saya suka berdebat', 'tag' => 'K'],
                ['text' => 'Saya ingin diperhatikan', 'tag' => 'X'],
            ],
            39 => [
                ['text' => 'Saya suka menyenangkan hati orang yang memipin saya', 'tag' => 'F'],
                ['text' => 'Saya tertarik menjadi anggota dari suatu kelompok', 'tag' => 'B'],
            ],
            40 => [
                ['text' => 'Saya senang mengikuti aturan secara tertib', 'tag' => 'W'],
                ['text' => 'Saya suka orang-orang mengenal saya benar-benar', 'tag' => 'O'],
            ],
            41 => [
                ['text' => 'Saya mencoba sekuat tenaga', 'tag' => 'G'],
                ['text' => 'Saya sangat menyenangkan', 'tag' => 'S'],
            ],
            42 => [
                ['text' => 'Orang lain beranggapan bahwa saya adalah seorang pemimpin yang baik', 'tag' => 'L'],
                ['text' => 'Saya berpikir jauh ke depan dan terinci', 'tag' => 'R'],
            ],
            43 => [
                ['text' => 'Seringkali saya memanfaatkan peluang', 'tag' => 'I'],
                ['text' => 'Saya senang memperhatikan hal-hal sampai sekecil-kecilnya', 'tag' => 'D'],
            ],
            44 => [
                ['text' => 'Orang lain menganggap saya bekerja cepat', 'tag' => 'T'],
                ['text' => 'Orang lain menganggap saya dapat melakukan penataan yang rapi dan teratur', 'tag' => 'C'],
            ],
            45 => [
                ['text' => 'Saya menyukai permainan-permainan dan olahraga', 'tag' => 'V'],
                ['text' => 'Saya sangat menyenangkan', 'tag' => 'E'],
            ],
            46 => [
                ['text' => 'Saya senang bila orang-orang dapat intim dan bersahabat', 'tag' => 'O'],
                ['text' => 'Saya selalu berusaha menyelesaikan apa yang telah saya mulai', 'tag' => 'N'],
            ],
            47 => [
                ['text' => 'Saya suka bereksperimen dan mencoba sesuatu yang baru', 'tag' => 'Z'],
                ['text' => 'Saya suka mengerjakan pekerjaan-pekerjaan yang sulit dengan baik', 'tag' => 'A'],
            ],
            48 => [
                ['text' => 'Saya senang diperlakukan secara adil', 'tag' => 'K'],
                ['text' => 'Saya senang mengajari orang lain bagaimana caranya mengerjakan sesuatu', 'tag' => 'P'],
            ],
            49 => [
                ['text' => 'Saya suka mengerjakan apa yang diharapkan dari saya', 'tag' => 'F'],
                ['text' => 'Saya suka menarik perhatian', 'tag' => 'X'],
            ],
            50 => [
                ['text' => 'Saya suka petunjuk-petunjuk terinci dalam melaksanakan pekerjaan', 'tag' => 'W'],
                ['text' => 'Saya senang berada bersama dengan orang lain', 'tag' => 'B'],
            ],
            51 => [
                ['text' => 'Saya selalu berusaha mengerjakan tugas secara sempurna', 'tag' => 'G'],
                ['text' => 'Orang lain menganggap, saya tidak mengenal lelah, dalam kerja sehari-hari', 'tag' => 'V'],
            ],
            52 => [
                ['text' => 'Saya tergolong tipe pemimpin', 'tag' => 'L'],
                ['text' => 'Saya mudah berteman', 'tag' => 'S'],
            ],
            53 => [
                ['text' => 'Saya memanfaatkan peluang-peluang', 'tag' => 'I'],
                ['text' => 'Saya banyak berfikir', 'tag' => 'R'],
            ],
            54 => [
                ['text' => 'Saya bekerja dengan kecepatan yang mantap dan cepat', 'tag' => 'T'],
                ['text' => 'Saya senang mengerjakan hal-hal yang detail', 'tag' => 'D'],
            ],
            55 => [
                ['text' => 'Saya memiliki banyak energi untuk permainan-permainan dan olahraga', 'tag' => 'V'],
                ['text' => 'Saya menempatkan segala sesuatunya secara rapi dan teratur', 'tag' => 'C'],
            ],
            56 => [
                ['text' => 'Saya bergaul baik dengan semua orang', 'tag' => 'S'],
                ['text' => 'Saya “pandai mengendalikan diri”', 'tag' => 'E'],
            ],
            57 => [
                ['text' => 'Saya ingin berkenalan dengan orang-orang baru dan mengerjakan hal baru', 'tag' => 'Z'],
                ['text' => 'Saya selalu ingin menyelesaikan pekerjaan yang sudah saya mulai', 'tag' => 'N'],
            ],
            58 => [
                ['text' => 'Biasanya saya bersikeras mengenai apa yang saya yakini', 'tag' => 'K'],
                ['text' => 'Biasanya saya suka bekerja “keras”', 'tag' => 'A'],
            ],
            59 => [
                ['text' => 'Saya menyukai saran-saran dari orang-orang yang saya kagumi', 'tag' => 'F'],
                ['text' => 'Saya senang mengatur orang lain', 'tag' => 'P'],
            ],
            60 => [
                ['text' => 'Saya biarkan orang-orang lain mempengaruhi saya', 'tag' => 'W'],
                ['text' => 'Saya suka menerima banyak perhatian', 'tag' => 'X'],
            ],
            61 => [
                ['text' => 'Biasanya saya bekerja sangat “keras”', 'tag' => 'G'],
                ['text' => 'Biasanya saya bekerja cepat', 'tag' => 'T'],
            ],
            62 => [
                ['text' => 'Bila saya berbicara, kelompok akan mendengarkan', 'tag' => 'L'],
                ['text' => 'Saya terampil mempergunakan alat-alat kerja', 'tag' => 'V'],
            ],
            63 => [
                ['text' => 'Saya lambat membina persahabatan', 'tag' => 'I'],
                ['text' => 'Saya lambat dalam mengambil keputusan', 'tag' => 'S'],
            ],
            64 => [
                ['text' => 'Biasanya saya makan secara cepat', 'tag' => 'T'],
                ['text' => 'Saya suka membaca', 'tag' => 'R'],
            ],
            65 => [
                ['text' => 'Saya menyukai pekerjaan yang memungkinkan saya “berkeliling”', 'tag' => 'V'],
                ['text' => 'Saya menyukai pekerjaan yang harus dilakukan secara teliti', 'tag' => 'D'],
            ],
            66 => [
                ['text' => 'Saya berteman sebanyak mungkin', 'tag' => 'S'],
                ['text' => 'Saya dapat menemukan hal-hal yang telah saya pindahkan', 'tag' => 'C'],
            ],
            67 => [
                ['text' => 'Perencanaan saya jauh ke masa depan', 'tag' => 'R'],
                ['text' => 'Saya selalu menyenangkan', 'tag' => 'E'],
            ],
            68 => [
                ['text' => 'Saya merasa bangga akan nama baik saya', 'tag' => 'K'],
                ['text' => 'Saya tetap menekuni satu permasalahan sampai ia terselesaikan', 'tag' => 'N'],
            ],
            69 => [
                ['text' => 'Saya suka menyenangkan hati orang-orang yang saya kagumi', 'tag' => 'F'],
                ['text' => 'Saya suka menjadi seorang yang berhasil', 'tag' => 'A'],
            ],
            70 => [
                ['text' => 'Saya senang bila orang-orang lain mengambil keputusan untuk kelompok', 'tag' => 'W'],
                ['text' => 'Saya suka mengambil keputusan untuk kelompok', 'tag' => 'P'],
            ],
            71 => [
                ['text' => 'Saya selalu berusaha sangat “keras”', 'tag' => 'G'],
                ['text' => 'Saya cepat dan mudah mengambil keputusan', 'tag' => 'I'],
            ],
            72 => [
                ['text' => 'Biasanya kelompok saya mengerjakan hal-hal yang saya inginkan', 'tag' => 'L'],
                ['text' => 'Biasanya saya tergesa-gesa', 'tag' => 'T'],
            ],
            73 => [
                ['text' => 'Saya seringkali merasa lelah', 'tag' => 'I'],
                ['text' => 'Saya lambat di dalam mengambil keputusan', 'tag' => 'V'],
            ],
            74 => [
                ['text' => 'Saya bekerja secara cepat', 'tag' => 'T'],
                ['text' => 'Saya mudah mendapat kawan', 'tag' => 'S'],
            ],
            75 => [
                ['text' => 'Biasanya saya bersemangat atau bergairah', 'tag' => 'V'],
                ['text' => 'Sebagian besar waktu saya untuk berpikir', 'tag' => 'R'],
            ],
            76 => [
                ['text' => 'Saya sangat hangat kepada orang-orang', 'tag' => 'S'],
                ['text' => 'Saya menyukai pekerjaan yang menuntut ketepatan', 'tag' => 'D'],
            ],
            77 => [
                ['text' => 'Saya banyak berpikir dan merencana', 'tag' => 'R'],
                ['text' => 'Saya meletakkan segala sesuatu pada tempatnya', 'tag' => 'C'],
            ],
            78 => [
                ['text' => 'Saya suka tugas yang perlu ditekuni sampai kepada hal sedetilnya', 'tag' => 'D'],
                ['text' => 'Saya tidak cepat marah', 'tag' => 'E'],
            ],
            79 => [
                ['text' => 'Saya senang mengikuti orang-orang yang saya kagumi', 'tag' => 'F'],
                ['text' => 'Saya selalu menyelesaikan pekerjaan yang saya mulai', 'tag' => 'N'],
            ],
            80 => [
                ['text' => 'Saya menyukai petunjuk-petunjuk yang jelas', 'tag' => 'W'],
                ['text' => 'Saya suka bekerja “keras”', 'tag' => 'A'],
            ],
            81 => [
                ['text' => 'Saya mengejar apa yang saya inginkan', 'tag' => 'G'],
                ['text' => 'Saya adalah seorang pemimpin yang baik', 'tag' => 'L'],
            ],
            82 => [
                ['text' => 'Saya membuat orang lain bekerja keras', 'tag' => 'L'],
                ['text' => 'Saya adalah seorang yang “gampangan” (tak banyak pertimbangan)', 'tag' => 'I'],
            ],
            83 => [
                ['text' => 'Saya membuat keputusan-keputusan secara cepat', 'tag' => 'I'],
                ['text' => 'Bicara saya cepat', 'tag' => 'T'],
            ],
            84 => [
                ['text' => 'Biasanya saya bekerja tergesa-gesa', 'tag' => 'T'],
                ['text' => 'Secara teratur saya berolahraga', 'tag' => 'V'],
            ],
            85 => [
                ['text' => 'Saya tidak suka bertemu dengan orang-orang', 'tag' => 'V'],
                ['text' => 'Saya cepat lelah', 'tag' => 'S'],
            ],
            86 => [
                ['text' => 'Saya mempunyai banyak sekali teman', 'tag' => 'S'],
                ['text' => 'Banyak waktu saya untuk berpikir', 'tag' => 'R'],
            ],
            87 => [
                ['text' => 'Saya suka bekerja dengan teori', 'tag' => 'R'],
                ['text' => 'Saya suka bekerja sedetil-detilnya', 'tag' => 'D'],
            ],
            88 => [
                ['text' => 'Saya suka bekerja sampai sedetil-detilnya', 'tag' => 'D'],
                ['text' => 'Saya suka mengorganisir pekerjaan saya', 'tag' => 'C'],
            ],
            89 => [
                ['text' => 'Saya meletakkan segala sesuatu pada tempatnya', 'tag' => 'C'],
                ['text' => 'Saya selalu menyenangkan', 'tag' => 'E'],
            ],
            90 => [
                ['text' => 'Saya senang diberi petunjuk mengenai apa yang harus saya lakukan', 'tag' => 'W'],
                ['text' => 'Saya harus menyelesaikan apa yang sudah saya mulai', 'tag' => 'N'],
            ],
        ];

        foreach ($questionsData as $number => $options) {
            $question = QuestionBank::create([
                'category_id' => $category->id,
                'question' => "Pilihlah salah satu pernyataan dari pasangan pernyataan di bawah ini yang paling mendekati gambaran diri Anda atau paling menunjukkan perasaan Anda. (Nomor {$number})",
                'question_type' => 'papi_kostick',
                'metadata' => [
                    'number' => $number,
                    'instruction' => 'Pilih pernyataan yang paling menggambarkan diri Anda.',
                ],
                'points' => 1,
            ]);

            // Option A
            QuestionOption::create([
                'question_id' => $question->id,
                'option_text' => $options[0]['text'],
                'attribute_tag' => $options[0]['tag'],
                'is_correct' => false,
            ]);

            // Option B
            QuestionOption::create([
                'question_id' => $question->id,
                'option_text' => $options[1]['text'],
                'attribute_tag' => $options[1]['tag'],
                'is_correct' => false,
            ]);
        }

        // 3. Buat Paket Ujian untuk Karyawan jika belum ada
        $test = \App\Models\Test::updateOrCreate(
            [
                'category_id' => $category->id,
                'title' => 'Tes Kepribadian (PAPI Kostick)',
            ],
            [
                'job_id' => null,
                'test_type' => 'employee',
                'department_id' => null, // Berlaku untuk semua divisi
                'duration_minutes' => 60,
                'passing_score' => 0,
                'total_questions' => 90,
                'is_random' => false,
                'target_employee_type' => 'all', // Berlaku untuk semua karyawan
            ]
        );

        // Hubungkan ke-90 butir soal ke paket tes
        \App\Models\TestQuestion::where('test_id', $test->id)->delete();
        $allQuestions = QuestionBank::where('category_id', $category->id)->get()->sortBy(function ($q) {
            return (int) ($q->metadata['number'] ?? $q->id);
        });

        $order = 1;
        foreach ($allQuestions as $q) {
            \App\Models\TestQuestion::create([
                'test_id' => $test->id,
                'question_id' => $q->id,
                'order_number' => $order++,
            ]);
        }
    }
}
