<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\District;
use App\Models\Province;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $zambia = Country::firstOrCreate(['code' => 'ZMB'], ['name' => 'Zambia']);

        $provinces = [
            'Central',
            'Copperbelt',
            'Eastern',
            'Luapula',
            'Lusaka',
            'Muchinga',
            'North Western',
            'Northern',
            'Southern',
            'Western',
        ];

        foreach ($provinces as $provinceName) {
            $province = Province::firstOrCreate([
                'country_id' => $zambia->id,
                'name' => $provinceName,
            ]);

            $districts = match ($provinceName) {
                'Lusaka' => ['Lusaka Central', 'Chongwe', 'Kafue'],
                'Copperbelt' => ['Ndola', 'Kitwe', 'Chingola'],
                'Central' => ['Kabwe', 'Mumbwa', 'Serenje'],
                'Eastern' => ['Chipata', 'Petauke', 'Katete'],
                'Southern' => ['Livingstone', 'Mazabuka', 'Kalomo'],
                default => ['District A', 'District B'],
            };

            foreach ($districts as $districtName) {
                District::firstOrCreate([
                    'country_id' => $zambia->id,
                    'province_id' => $province->id,
                    'name' => $districtName,
                ]);
            }
        }
    }
}
