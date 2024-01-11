<?php
namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            ['key' => 'app_name', 'value' => 'POS', 'white_label_id' => 1],
            ['key' => 'currency_symbol', 'value' => '₹', 'white_label_id' => 1],
            ['key' => 'pagination', 'value' => '20', 'white_label_id' => 1],
        ];

        foreach ($data as $value) {
            Setting::updateOrCreate(
                ['key' => $value['key'], 'white_label_id' => $value['white_label_id']],
                ['value' => $value['value']]
            );
        }
    }
}
