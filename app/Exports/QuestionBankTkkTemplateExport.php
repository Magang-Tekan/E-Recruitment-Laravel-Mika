<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Template import bank soal khusus Tes Kemampuan Kerja (TKK).
 *
 * Semua baris sudah memakai kategori "Tes Kemampuan Kerja" agar soal langsung
 * masuk ke kategori yang dipakai paket ujian TKK (kategori id 26).
 *
 * Kolom sub_kategori:
 * Menentukan aspek kompetensi yang dinilai pada evaluasi TKK (Dual-Gate Passing Rule).
 * Disarankan menggunakan format "Aspek Induk – Topik Spesifik", misalnya:
 * 1. Ketelitian & Pengecekan Data
 *    - Ketelitian & Pengecekan Data – Pencocokan PO, Surat Jalan & Invoice
 *    - Ketelitian & Pengecekan Data – Rekening & Data Vendor
 *    - Ketelitian & Pengecekan Data – Angka & Hitungan Dokumen
 *    - Ketelitian & Pengecekan Data – Identitas, Tanggal & Nomor Dokumen
 * 2. Alur Kerja & SOP
 *    - Alur Kerja & SOP – Utang Usaha (AP)
 *    - Alur Kerja & SOP – Piutang & Penagihan (AR)
 *    - Alur Kerja & SOP – Kas & Bank
 *    - Alur Kerja & SOP – Closing & Persetujuan
 * 3. Komunikasi Tertulis
 *    - Komunikasi Tertulis – Ke Vendor
 *    - Komunikasi Tertulis – Ke Atasan
 *    - Komunikasi Tertulis – Ke Rekan Divisi Lain
 *    - Komunikasi Tertulis – Ke Pelanggan
 * 4. Etika & Integritas (Wajib lolos batas kelulusan 70)
 *    - Etika & Integritas – Kejujuran Dokumen & Laporan
 *    - Etika & Integritas – Kerahasiaan Data
 *    - Etika & Integritas – Hadiah & Konflik Kepentingan
 *    - Etika & Integritas – Menyikapi Kesalahan & Pelanggaran Orang Lain
 * 5. Inisiatif, Empati & Kerja Sama Tim
 *    - Inisiatif, Empati & Kerja Sama Tim – Serah Terima & Staf Cuti
 *    - Inisiatif, Empati & Kerja Sama Tim – Menghadapi Kendala
 *    - Inisiatif, Empati & Kerja Sama Tim – Kesalahan Sendiri
 *    - Inisiatif, Empati & Kerja Sama Tim – Proaktif
 *    - Inisiatif, Empati & Kerja Sama Tim – Kerja Sama Tim
 *    - Inisiatif, Empati & Kerja Sama Tim – Menjaga Suasana Tim
 * 6. Sikap & Disiplin Kerja
 *    - Sikap & Disiplin Kerja – Respons Instruksi Atasan
 *    - Sikap & Disiplin Kerja – Sopan Santun
 *    - Sikap & Disiplin Kerja – Disiplin Kerja
 *    - Sikap & Disiplin Kerja – Kebersihan & Kerapian Area Kerja
 *
 * Kolom opsional (boleh dikosongkan):
 * - bobot_opsi            : isi (mis. 2,1,0,0) untuk soal bergradasi/SJT.
 * - referensi             : sumber/rujukan pembahasan.
 * - jenis_lampiran        : label jenis lampiran (mis. tabel, gambar, invoice).
 * - detail_lampiran_dokumen: JSON dokumen yang dirender di dalam soal
 *                            (pasangkan dengan penanda [[DOK]] pada teks soal).
 */
class QuestionBankTkkTemplateExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    public const CATEGORY = 'Tes Kemampuan Kerja';

    public function title(): string
    {
        return 'Template Soal TKK';
    }

    public function headings(): array
    {
        return [
            'kategori',
            'sub_kategori',
            'soal',
            'tipe_soal',
            'poin',
            'opsi_a',
            'opsi_b',
            'opsi_c',
            'opsi_d',
            'kunci_jawaban',
            'bobot_opsi',
            'pembahasan',
            'referensi',
            'jenis_lampiran',
            'detail_lampiran_dokumen',
        ];
    }

    public function collection()
    {
        return collect([
            [
                'kategori' => self::CATEGORY,
                'sub_kategori' => 'Sikap & Disiplin Kerja – Respons Instruksi Atasan',
                'soal' => 'Seorang staf menerima dua pekerjaan dengan tenggat yang sama. Apa langkah pertama yang paling tepat?',
                'tipe_soal' => 'multiple_choice',
                'poin' => 1,
                'opsi_a' => 'Mengerjakan keduanya bersamaan agar selesai cepat',
                'opsi_b' => 'Menanyakan prioritas kepada atasan lalu menyusun urutan kerja',
                'opsi_c' => 'Mengerjakan pekerjaan yang paling mudah lebih dahulu',
                'opsi_d' => 'Menunda salah satu pekerjaan sampai ada teguran',
                'kunci_jawaban' => 'B',
                'bobot_opsi' => '',
                'pembahasan' => 'Prioritas ditetapkan bersama atasan agar hasil kerja selaras dengan kebutuhan tim.',
                'referensi' => 'Manajemen prioritas & komunikasi kerja',
                'jenis_lampiran' => '',
                'detail_lampiran_dokumen' => '',
            ],
            [
                'kategori' => self::CATEGORY,
                'sub_kategori' => 'Ketelitian & Pengecekan Data – Angka & Hitungan Dokumen',
                'soal' => "Data penjualan bulan ini:\n- Produk A: 120 unit\n- Produk B: 80 unit\n- Produk C: 200 unit\n\nProduk mana yang paling banyak terjual?",
                'tipe_soal' => 'multiple_choice',
                'poin' => 1,
                'opsi_a' => 'Produk A',
                'opsi_b' => 'Produk B',
                'opsi_c' => 'Produk C',
                'opsi_d' => 'Tidak dapat ditentukan',
                'kunci_jawaban' => 'C',
                'bobot_opsi' => '',
                'pembahasan' => 'Produk C terjual 200 unit, jumlah tertinggi di antara ketiga produk.',
                'referensi' => 'Kemampuan numerik dasar: membaca data',
                'jenis_lampiran' => '',
                'detail_lampiran_dokumen' => '',
            ],
            [
                'kategori' => self::CATEGORY,
                'sub_kategori' => 'Etika & Integritas – Menyikapi Kesalahan & Pelanggaran Orang Lain',
                'soal' => 'Rekan kerja Anda melakukan kesalahan input yang berpotensi terlambat terdeteksi. Sikap Anda yang paling tepat adalah ...',
                'tipe_soal' => 'multiple_choice',
                'poin' => 2,
                'opsi_a' => 'Segera memberi tahu rekan kerja tersebut dan membantu memperbaikinya',
                'opsi_b' => 'Melaporkan langsung ke atasan tanpa membicarakannya dengan rekan kerja',
                'opsi_c' => 'Membiarkan karena bukan bagian dari tanggung jawab Anda',
                'opsi_d' => 'Memperbaiki sendiri tanpa memberi tahu siapa pun',
                'kunci_jawaban' => 'A',
                'bobot_opsi' => '2,1,0,0',
                'pembahasan' => 'Contoh soal bergradasi (SJT): A paling tepat (2), B cukup baik (1), C dan D kurang tepat (0). Poin otomatis mengikuti bobot tertinggi.',
                'referensi' => 'Integritas & kerja sama tim',
                'jenis_lampiran' => '',
                'detail_lampiran_dokumen' => '',
            ],
            [
                'kategori' => self::CATEGORY,
                'sub_kategori' => 'Ketelitian & Pengecekan Data – Pencocokan PO, Surat Jalan & Invoice',
                'soal' => "Bagian gudang mencatat mutasi stok barang X pada Oktober 2026.\n\n[[DOK]]\n\nBerapa stok akhir barang X setelah mutasi?",
                'tipe_soal' => 'multiple_choice',
                'poin' => 1,
                'opsi_a' => '40 unit',
                'opsi_b' => '50 unit',
                'opsi_c' => '60 unit',
                'opsi_d' => '80 unit',
                'kunci_jawaban' => 'C',
                'bobot_opsi' => '',
                'pembahasan' => 'Stok akhir = stok awal 50 + penerimaan 30 - pengeluaran 20 = 60 unit.',
                'referensi' => 'Kartu stok: pencatatan mutasi masuk dan keluar',
                'jenis_lampiran' => 'tabel',
                'detail_lampiran_dokumen' => '{"title":"KARTU STOK","meta":[["Barang","X"],["Periode","Okt 2026"]],"al":["l","r","r"],"cols":["Keterangan","Masuk","Keluar"],"rows":[["Stok awal","50","-"],["Penerimaan","30","-"],["Pengeluaran","-","20"]],"foot":[["Stok akhir","60"]]}',
            ],
            [
                'kategori' => self::CATEGORY,
                'sub_kategori' => 'Alur Kerja & SOP – Utang Usaha (AP)',
                'soal' => 'Sebelum memproses pembayaran tagihan dari supplier, dokumen apa saja yang wajib diverifikasi dalam prinsip 3-way matching?',
                'tipe_soal' => 'multiple_choice',
                'poin' => 1,
                'opsi_a' => 'Purchase Order (PO), Surat Jalan/Laporan Penerimaan Barang (GRN), dan Invoice',
                'opsi_b' => 'Kwitansi, Surat Penawaran Harga, dan Faktur Pajak',
                'opsi_c' => 'Rekening Koran, Bukti Kas Keluar, dan Memo Internal',
                'opsi_d' => 'Purchase Requisition (PR), Brosur Produk, dan Surat Jalan',
                'kunci_jawaban' => 'A',
                'bobot_opsi' => '',
                'pembahasan' => 'Prinsip 3-way matching membandingkan Purchase Order (PO), bukti penerimaan barang (Surat Jalan/GRN), dan Invoice dari vendor.',
                'referensi' => 'SOP Verifikasi Utang Usaha & 3-Way Matching',
                'jenis_lampiran' => '',
                'detail_lampiran_dokumen' => '',
            ],
            [
                'kategori' => self::CATEGORY,
                'sub_kategori' => 'Komunikasi Tertulis – Ke Rekan Divisi Lain',
                'soal' => 'Anda membutuhkan data mutasi stok dari divisi gudang yang mendesak untuk penyusunan laporan keuangan. Cara penyampaian tertulis yang paling profesional adalah ...',
                'tipe_soal' => 'multiple_choice',
                'poin' => 2,
                'opsi_a' => 'Mengirim pesan resmi dengan menyebutkan data spesifik yang dibutuhkan, tenggat waktu, dan alasan urgensinya secara santun',
                'opsi_b' => 'Menghubungi berkali-kali di grup chat umum agar segera direspons',
                'opsi_c' => 'Langsung melaporkan ke atasan bagian gudang bahwa timnya lambat bekerja',
                'opsi_d' => 'Menunggu hingga bagian gudang berinisiatif mengirimkan sendiri tanpa diingatkan',
                'kunci_jawaban' => 'A',
                'bobot_opsi' => '2,1,0,0',
                'pembahasan' => 'Komunikasi lintas divisi yang efektif menjelaskan kebutuhan secara spesifik, santun, dan menyampaikan konteks urgensi tanpa menciptakan konflik.',
                'referensi' => 'Komunikasi Efektif Antar Divisi',
                'jenis_lampiran' => '',
                'detail_lampiran_dokumen' => '',
            ],
        ]);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 24, // kategori
            'B' => 45, // sub_kategori
            'C' => 70, // soal
            'D' => 18, // tipe_soal
            'E' => 8,  // poin
            'F' => 32, // opsi_a
            'G' => 32, // opsi_b
            'H' => 32, // opsi_c
            'I' => 32, // opsi_d
            'J' => 14, // kunci_jawaban
            'K' => 14, // bobot_opsi
            'L' => 50, // pembahasan
            'M' => 36, // referensi
            'N' => 16, // jenis_lampiran
            'O' => 60, // detail_lampiran_dokumen
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:O1');

        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '16264A']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            'B' => [
                'alignment' => ['vertical' => Alignment::VERTICAL_TOP],
            ],
            'C' => [
                'alignment' => ['wrapText' => true, 'vertical' => Alignment::VERTICAL_TOP],
            ],
            'L' => [
                'alignment' => ['wrapText' => true, 'vertical' => Alignment::VERTICAL_TOP],
            ],
        ];
    }
}
