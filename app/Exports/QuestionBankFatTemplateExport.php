<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class QuestionBankFatTemplateExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    public function title(): string
    {
        return 'Template Soal FAT';
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
                'kategori' => 'Tes Kompetensi FAT',
                'sub_kategori' => 'Dagang – Pembelian & Utang Usaha',
                'soal' => "Bagian AP CV Sumber Pangan menerima invoice berikut. Pembayaran dilakukan 9 Oktober 2026. Abaikan PPN.\n\n[[DOK]]\n\nBerapa jumlah yang ditransfer ke supplier?",
                'tipe_soal' => 'multiple_choice',
                'poin' => 1,
                'opsi_a' => 'Rp51.150.000',
                'opsi_b' => 'Rp53.900.000',
                'opsi_c' => 'Rp52.250.000',
                'opsi_d' => 'Rp51.205.000',
                'kunci_jawaban' => 'D',
                'pembahasan' => "Diskon dagang: Rp55.000.000 × 95% = Rp52.250.000. Diskon tunai 2%: Rp52.250.000 × 98% = Rp51.205.000.",
                'referensi' => 'Praktik utang usaha: diskon dagang dan syarat 2/10, n/30',
                'jenis_lampiran' => 'invoice',
                'detail_lampiran_dokumen' => '{"k":"invoice","title":"INVOICE","meta":[["No.","INV/01"],["Tanggal","2 Okt 2026"]],"cols":["Uraian","Qty","Harga","Jumlah"],"rows":[["Mi instan","500 dus","110.000","55.000.000"]],"foot":[["Total tagihan","52.250.000"]]}',
            ],
            [
                'kategori' => 'Tes Kompetensi FAT',
                'sub_kategori' => 'Pajak – PPh 21 Karyawan',
                'soal' => 'Karyawan tetap menerima gaji pokok Rp8.000.000 dan tunjangan transport Rp1.500.000. Berapa biaya jabatan maksimal per bulan yang dapat dikurangkan sesuai ketentuan pajak?',
                'tipe_soal' => 'multiple_choice',
                'poin' => 1,
                'opsi_a' => 'Rp475.000',
                'opsi_b' => 'Rp500.000',
                'opsi_c' => 'Rp600.000',
                'opsi_d' => 'Rp750.000',
                'kunci_jawaban' => 'B',
                'pembahasan' => 'Biaya jabatan adalah 5% dari penghasilan bruto, maksimal Rp500.000 per bulan (Rp6.000.000 setahun). 5% × Rp9.500.000 = Rp475.000, tetapi plafon maksimal adalah Rp500.000.',
                'referensi' => 'UU HPP & PMK terkait PPh Pasal 21',
                'jenis_lampiran' => '-',
                'detail_lampiran_dokumen' => '',
            ],
            [
                'kategori' => 'Tes Kompetensi FAT',
                'sub_kategori' => 'Manufaktur – Elemen Biaya & Bahan Baku',
                'soal' => 'PT Maju Logam membeli bahan baku senilai Rp120.000.000 dengan ongkos angkut pembelian Rp5.000.000 dan retur bahan baku Rp3.000.000. Berapakah nilai pembelian bersih bahan baku?',
                'tipe_soal' => 'multiple_choice',
                'poin' => 1,
                'opsi_a' => 'Rp122.000.000',
                'opsi_b' => 'Rp118.000.000',
                'opsi_c' => 'Rp125.000.000',
                'opsi_d' => 'Rp115.000.000',
                'kunci_jawaban' => 'A',
                'pembahasan' => 'Pembelian bersih = Pembelian + Ongkos angkut - Retur pembelian = Rp120.000.000 + Rp5.000.000 - Rp3.000.000 = Rp122.000.000.',
                'referensi' => 'Akuntansi Biaya: Penentuan Harga Pokok Bahan Baku',
                'jenis_lampiran' => '-',
                'detail_lampiran_dokumen' => '',
            ],
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '16264A']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}

