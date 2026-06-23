<?php

namespace Database\Seeders;

use App\Models\TrainerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TrainerSampleSeeder extends Seeder
{
    public function run(): void
    {
        $baseLat = -1.2921; // Nairobi center
        $baseLng = 36.8219;

        $samples = [
            ['name' => 'James Mwangi', 'specialty' => 'Strength Training', 'category' => 'Personal Training', 'lat' => $baseLat + 0.003, 'lng' => $baseLng + 0.002, 'rating' => 4.9],
            ['name' => 'Aisha Njeri', 'specialty' => 'Cardio & HIIT', 'category' => 'Group Classes', 'lat' => $baseLat - 0.004, 'lng' => $baseLng + 0.004, 'rating' => 4.7],
            ['name' => 'Daniel Otieno', 'specialty' => 'Weight Loss', 'category' => 'Personal Training', 'lat' => $baseLat + 0.01, 'lng' => $baseLng - 0.006, 'rating' => 4.8],
            ['name' => 'Grace Wanjiru', 'specialty' => 'Yoga & Mobility', 'category' => 'Wellness', 'lat' => $baseLat - 0.009, 'lng' => $baseLng - 0.002, 'rating' => 4.6],
            ['name' => 'Peter Kamau', 'specialty' => 'Boxing & Conditioning', 'category' => 'Combat', 'lat' => $baseLat + 0.005, 'lng' => $baseLng + 0.01, 'rating' => 4.5],
        ];

        foreach ($samples as $s) {
            $user = User::create([
                'name' => $s['name'],
                'email' => Str::slug($s['name']).'@example.test',
                'password' => bcrypt('password'),
                'role' => 'trainer',
            ]);

            TrainerProfile::create([
                'user_id' => $user->id,
                'specialty' => $s['specialty'],
                'category' => $s['category'],
                'rating' => $s['rating'],
                'experience_years' => rand(1,8),
                'latitude' => $s['lat'],
                'longitude' => $s['lng'],
                'town' => 'Nairobi',
            ]);
        }
    }
}
