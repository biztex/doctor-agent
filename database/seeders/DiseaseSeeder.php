<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Disease;

class DiseaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Level 1 Diseases (Low urgency)
        $level1Diseases = [
            'かぜ（急性上気道炎）',
            'アレルギー性鼻炎',
            '咽頭アレルギー',
            '声帯疲労',
            '季節性アレルギー',
            '軽度の脱水症状',
            '喉風邪',
            '環境変化による体調不良',
            '精神的ストレス由来の発熱',
            'ウイルス性胃腸炎（軽症）'
        ];

        // Level 2 Diseases (Medium urgency)
        $level2Diseases = [
            'インフルエンザ',
            'COVID-19（軽症～中等症）',
            'マイコプラズマ肺炎',
            '急性気管支炎',
            'RSウイルス感染症',
            '喉頭炎',
            '副鼻腔炎',
            '咳喘息',
            '咽頭結膜熱',
            '喉の膿瘍'
        ];

        // Level 3 Diseases (High urgency)
        $level3Diseases = [
            '肺炎',
            '急性喉頭蓋炎',
            '敗血症',
            '髄膜炎',
            '心筋炎',
            'COVID-19（重症）',
            'インフルエンザ脳症',
            '呼吸不全',
            '百日咳（重症化）',
            'SARS/MERS'
];

        // Insert Level 1 diseases
        foreach ($level1Diseases as $disease) {
            Disease::create([
                'name' => $disease,
                'urgency_level' => 'レベル1'
            ]);
        }

        // Insert Level 2 diseases
        foreach ($level2Diseases as $disease) {
            Disease::create([
                'name' => $disease,
                'urgency_level' => 'レベル2'
            ]);
        }

        // Insert Level 3 diseases
        foreach ($level3Diseases as $disease) {
            Disease::create([
                'name' => $disease,
                'urgency_level' => 'レベル3'
            ]);
        }
    }
} 