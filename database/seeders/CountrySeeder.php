<?php

namespace Database\Seeders;

use App\Models\Country;
use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Seed the countries table
     *
     * @return void
     */
    public function run(): void
    {
        Country::query()->delete();

        $csvData = fopen(base_path('database/seeders/dumps/countries.csv'), 'r');
        $transRow = true;
        $countriesArray = [];
        while (($data = fgetcsv($csvData, 555, ',')) !== false) {
            if (!$transRow) {
                $countriesArray[] = [
                    'iso' => $data['1'],
                    'name' => $data['2'],
                    'iso3' => $data['3'],
                    'num_code' => $data['4'],
                    'phone_code' => $data['5']
                ];
            }
            $transRow = false;
        }
        fclose($csvData);

        Country::insert($countriesArray);
    }
}
