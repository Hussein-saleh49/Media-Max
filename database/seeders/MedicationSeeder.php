<?php

// database/seeders/MedicationSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medication;
use Faker\Factory as Faker;

class MedicationSeeder extends Seeder
{
    public function run()
    {
        // Create an instance of Faker
        $faker = Faker::create();

        // Generate 50 fake medications
        foreach (range(1, 50) as $index) {
            Medication::create([
                'name' => $faker->word, // Generate a random medication name
                'active_ingredient' => $faker->word, // Generate a random active ingredient
                'search_count' => $faker->numberBetween(1, 100), // Random search count between 1 and 100
            ]);
        }
    }
}

