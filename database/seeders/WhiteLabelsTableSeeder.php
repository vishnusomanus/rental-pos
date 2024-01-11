<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\WhiteLabel;

class WhiteLabelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $whiteLabels = [
            [
                'domain' => 'localhost',
                'description' => 'localhost',
                'url' => 'http://localhost'
            ],
            [
                'domain' => 'pos.local',
                'description' => 'localhost hostname',
                'url' => 'http://pos.local'
            ],
        ];

        foreach ($whiteLabels as $whiteLabelData) {
            WhiteLabel::create($whiteLabelData);
        }
    }
}
