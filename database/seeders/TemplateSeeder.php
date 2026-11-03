<?php

namespace Database\Seeders;

use App\Models\QuestionnaireTemplate;
use Illuminate\Database\Seeder;

/** Memuat template baku kuesioner prakualifikasi (owner_id kosong): manufaktur pangan, hulu migas, serta konstruksi dan gedung. Aman dijalankan ulang. */
class TemplateSeeder extends Seeder
{
    public const FILES = ['template-iso45001-smk3.php', 'template-migas-ptk005.php', 'template-konstruksi-smkk.php'];

    public function run(): void
    {
        foreach (self::FILES as $file) {
            $this->load(require database_path('data/'.$file));
        }
    }

    protected function load(array $data): void
    {
        if (QuestionnaireTemplate::whereNull('owner_id')->where('code', $data['code'])->exists()) {
            return;
        }

        $template = QuestionnaireTemplate::create(collect($data)->only(['code', 'name', 'standard', 'description'])->all());

        foreach ($data['elements'] as $e => [$code, $name, $items]) {
            $element = $template->elements()->create(['code' => $code, 'name' => $name, 'sort' => $e + 1]);

            foreach ($items as $i => [$itemCode, $question, $evidence, $weight, $critical, $minRisk]) {
                $element->items()->create([
                    'questionnaire_template_id' => $template->id,
                    'code' => $itemCode,
                    'question' => $question,
                    'evidence_hint' => $evidence,
                    'weight' => $weight,
                    'is_critical' => $critical,
                    'min_risk' => $minRisk,
                    'sort' => ($e + 1) * 10 + $i,
                ]);
            }
        }
    }
}
