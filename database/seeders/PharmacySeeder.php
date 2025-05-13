<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pharmacy;

class PharmacySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $pharmacies = [
            [
                'name' => 'ABC Pharmacy',
                'latitude' => 24.7136,
                'longitude' => 46.6753,
                'address' => 'Riyadh, Saudi Arabia',
                'rating' => 4.5, // إضافة التقييم
            ],
            [
                'name' => 'XYZ Pharmacy',
                'latitude' => 24.7266,
                'longitude' => 46.6877,
                'address' => 'Riyadh, Saudi Arabia',
                'rating' => 3.8, // إضافة التقييم
            ],
            [
                'name' => 'MediCare Pharmacy',
                'latitude' => 24.7605,
                'longitude' => 46.6738,
                'address' => 'Riyadh, Saudi Arabia',
                'rating' => 4.2, // إضافة التقييم
            ],
        ];

        foreach ($pharmacies as $pharmacy) {
            Pharmacy::create($pharmacy);
        }
    }
}
