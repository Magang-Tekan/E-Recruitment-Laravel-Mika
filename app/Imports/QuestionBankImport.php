<?php

namespace App\Imports;

use App\Models\QuestionBank;
use App\Models\QuestionOption;
use App\Models\TestCategory;
use App\Models\TestSubCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;

class QuestionBankImport implements ToCollection, WithHeadingRow
{
    public $importedCount = 0;
    public $errors = [];

    /**
     * Terima bobot opsi dalam format "2,0,1,0", "2;1;0;0", atau "[2,1,0,0]".
     * Mengembalikan null jika kolom kosong/tidak valid agar import lama tetap aman.
     */
    protected function parseOptionWeights($raw): ?array
    {
        if (is_array($raw)) {
            $values = $raw;
        } else {
            $raw = trim((string) ($raw ?? ''));
            if ($raw === '') {
                return null;
            }

            $decoded = str_starts_with($raw, '[') ? json_decode($raw, true) : null;
            $values = is_array($decoded) ? $decoded : preg_split('/\s*[,;|\/]\s*/', $raw);
        }

        if (!is_array($values) || count($values) < 2) {
            return null;
        }

        $weights = [];
        foreach ($values as $value) {
            $value = trim((string) $value);
            if ($value === '' || !is_numeric($value)) {
                return null;
            }
            $weights[] = (float) $value;
        }

        return $weights;
    }

    public function collection(Collection $rows)
    {
        $categories = TestCategory::all()->keyBy(function ($cat) {
            return strtolower(trim($cat->name));
        });
        $categoryIds = TestCategory::pluck('id')->toArray();
        $subCategoryCache = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                // Determine row number for error logging (heading row is row 1)
                $rowNumber = $index + 2;

                $rawCategory = trim((string)($row['kategori'] ?? $row['category'] ?? ''));
                
                // Get question text from various possible header names in Excel
                $questionText = trim((string)(
                    $row['soal'] 
                    ?? $row['question'] 
                    ?? $row['pertanyaan'] 
                    ?? $row['teks_soal'] 
                    ?? $row['question_text'] 
                    ?? $row['deskripsi'] 
                    ?? $row['pernyataan'] 
                    ?? ''
                ));

                // Clean up leading/trailing line breaks and extra whitespace
                $questionText = preg_replace('/^[\s\r\n]+|[\s\r\n]+$/u', '', $questionText);

                $rawType = strtolower(trim((string)($row['tipe_soal'] ?? $row['question_type'] ?? 'multiple_choice')));
                $points = (int) ($row['poin'] ?? $row['points'] ?? 1);
                if ($points <= 0) {
                    $points = 1;
                }

                // Kolom opsional untuk soal bergradasi (SJT): 2 = terbaik, 1 = cukup baik, 0 = kurang tepat.
                $optionWeights = $this->parseOptionWeights(
                    $row['bobot_opsi'] ?? $row['option_weights'] ?? $row['bobot_jawaban'] ?? null
                );

                // Normalize Question Type
                $questionType = 'multiple_choice';
                if (in_array($rawType, ['essay', 'uraian', 'isihan'])) {
                    $questionType = 'essay';
                } elseif (in_array($rawType, ['disc', 'disc_test', 'kepribadian_disc'])) {
                    $questionType = 'disc';
                }

                // Default question text for DISC if empty in Excel
                if (empty($questionText)) {
                    if ($questionType === 'disc') {
                        $questionText = "Pilihlah satu yang Paling Menggambarkan (Most) dan Satu yang Paling Tidak Menggambarkan (Least) diri Anda.";
                    } else {
                        continue; // Skip blank non-DISC rows
                    }
                }

                // Resolve Category ID
                $categoryId = null;
                $catKey = strtolower($rawCategory);
                if (is_numeric($rawCategory) && in_array((int)$rawCategory, $categoryIds)) {
                    $categoryId = (int)$rawCategory;
                } elseif ($catKey !== '' && isset($categories[$catKey])) {
                    $categoryId = $categories[$catKey]->id;
                } else {
                    $category = TestCategory::firstOrCreate(['name' => $rawCategory ?: 'Umum']);
                    $categoryId = $category->id;
                    $categories[strtolower($category->name)] = $category;
                }

                $imagePath = trim((string)($row['gambar'] ?? $row['image_path'] ?? $row['lampiran'] ?? '')) ?: null;

                $metadata = [];
                $subCategoryModel = null;
                $subCat = trim((string)($row['sub_kategori'] ?? $row['sub_category'] ?? $row['topik'] ?? $row['topic'] ?? ''));
                if (!empty($subCat)) {
                    $subCat = mb_substr($subCat, 0, 255);
                    $subCatCacheKey = $categoryId . '|' . mb_strtolower($subCat);

                    if (isset($subCategoryCache[$subCatCacheKey])) {
                        $subCategoryModel = $subCategoryCache[$subCatCacheKey];
                    } else {
                        // 1. Exact match atau case-insensitive
                        $subCategoryModel = TestSubCategory::where('category_id', $categoryId)
                            ->where(function ($q) use ($subCat) {
                                $q->where('name', $subCat)
                                  ->orWhereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($subCat)]);
                            })
                            ->first();

                        // 2. Fuzzy match toleran tanda pisah (dash "-", en-dash "–", em-dash "—" dan spasi)
                        if (!$subCategoryModel) {
                            $normalizedSearch = preg_replace('/[\s\x{2013}\x{2014}-]+/u', '-', mb_strtolower($subCat));
                            $candidates = TestSubCategory::where('category_id', $categoryId)->get();
                            foreach ($candidates as $cand) {
                                $normalizedCand = preg_replace('/[\s\x{2013}\x{2014}-]+/u', '-', mb_strtolower(trim($cand->name)));
                                if ($normalizedSearch === $normalizedCand) {
                                    $subCategoryModel = $cand;
                                    break;
                                }
                            }
                        }

                        // 3. Jika belum pernah ada, buat baru
                        if (!$subCategoryModel) {
                            $subCategoryModel = TestSubCategory::create([
                                'category_id' => $categoryId,
                                'name' => $subCat,
                            ]);
                        }

                        $subCategoryCache[$subCatCacheKey] = $subCategoryModel;
                    }

                    $canonicalSubName = $subCategoryModel?->name ?: $subCat;
                    $metadata['sub_category'] = $canonicalSubName;
                    $metadata['topic'] = $canonicalSubName;
                }
                if (!empty($row['pembahasan'] ?? $row['explanation'] ?? '')) {
                    $metadata['explanation'] = trim($row['pembahasan'] ?? $row['explanation']);
                }
                if (!empty($row['referensi'] ?? $row['reference'] ?? '')) {
                    $metadata['reference'] = trim($row['referensi'] ?? $row['reference']);
                }
                $rawDoc = $row['detail_lampiran_dokumen'] ?? $row['detail_dokumen'] ?? null;
                if (!empty($rawDoc)) {
                    $metadata['document'] = is_string($rawDoc) ? json_decode($rawDoc, true) : $rawDoc;
                }
                if (!empty($row['jenis_lampiran'])) {
                    $metadata['doc_type'] = trim($row['jenis_lampiran']);
                }

                if ($optionWeights !== null) {
                    $metadata['option_weights'] = $optionWeights;
                    $maxWeight = max($optionWeights);
                    if ($maxWeight > 0) {
                        $points = (int) ceil($maxWeight);
                    }
                }

                $questionBank = QuestionBank::create([
                    'category_id' => $categoryId,
                    'sub_category_id' => $subCategoryModel?->id,
                    'question' => $questionText,
                    'question_type' => $questionType,
                    'points' => $points,
                    'image_path' => $imagePath,
                    'metadata' => !empty($metadata) ? $metadata : null,
                ]);

                if ($questionType === 'multiple_choice') {
                    $optA = trim($row['opsi_a'] ?? $row['option_a'] ?? '');
                    $optB = trim($row['opsi_b'] ?? $row['option_b'] ?? '');
                    $optC = trim($row['opsi_c'] ?? $row['option_c'] ?? '');
                    $optD = trim($row['opsi_d'] ?? $row['option_d'] ?? '');
                    $rawCorrect = strtoupper(trim($row['kunci_jawaban'] ?? $row['correct_option'] ?? 'A'));

                    $options = [$optA, $optB, $optC, $optD];

                    $correctIndex = 0;
                    if (in_array($rawCorrect, ['A', '1', '0'])) {
                        $correctIndex = 0;
                    } elseif (in_array($rawCorrect, ['B', '2', '1'])) {
                        $correctIndex = 1;
                    } elseif (in_array($rawCorrect, ['C', '3', '2'])) {
                        $correctIndex = 2;
                    } elseif (in_array($rawCorrect, ['D', '4', '3'])) {
                        $correctIndex = 3;
                    }

                    foreach ($options as $idx => $optText) {
                        QuestionOption::create([
                            'question_id' => $questionBank->id,
                            'option_text' => $optText ?: 'Opsi ' . chr(65 + $idx),
                            'is_correct' => ($idx === $correctIndex),
                        ]);
                    }
                } elseif ($questionType === 'disc') {
                    // Check if explicit DISC columns exist (disc_d, disc_i, disc_s, disc_c)
                    $textD = trim($row['disc_d'] ?? $row['opsi_disc_d'] ?? '');
                    $textI = trim($row['disc_i'] ?? $row['opsi_disc_i'] ?? '');
                    $textS = trim($row['disc_s'] ?? $row['opsi_disc_s'] ?? '');
                    $textC = trim($row['disc_c'] ?? $row['opsi_disc_c'] ?? '');

                    // Standard template mapping: opsi_a (D), opsi_b (I), opsi_c (S), opsi_d (C)
                    if (empty($textD) && empty($textI) && empty($textS) && empty($textC)) {
                        $textD = trim($row['opsi_a'] ?? $row['option_a'] ?? '');
                        $textI = trim($row['opsi_b'] ?? $row['option_b'] ?? '');
                        $textS = trim($row['opsi_c'] ?? $row['option_c'] ?? '');
                        $textC = trim($row['opsi_d'] ?? $row['option_d'] ?? '');
                    }

                    $discOptions = [
                        'D' => $textD,
                        'I' => $textI,
                        'S' => $textS,
                        'C' => $textC,
                    ];

                    foreach ($discOptions as $tag => $optText) {
                        QuestionOption::create([
                            'question_id' => $questionBank->id,
                            'option_text' => $optText ?: 'Opsi ' . $tag,
                            'attribute_tag' => $tag,
                            'is_correct' => false,
                        ]);
                    }
                }

                $this->importedCount++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
