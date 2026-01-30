<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Heavy Rain', 'icon' => 'heavy rain', 'color_code' => '#3B82F6'],
            ['name' => 'Tsunami', 'icon' => 'tsunami', 'color_code' => '#0EA5E9'],
            ['name' => 'Road Closure', 'icon' => 'road closure', 'color_code' => '#F59E0B'],
            ['name' => 'Fire', 'icon' => 'fire', 'color_code' => '#EF4444'],
            ['name' => 'Lightning', 'icon' => 'lightning', 'color_code' => '#FBBF24'],
            ['name' => 'Water Outage', 'icon' => 'water outage', 'color_code' => '#60A5FA'],
            ['name' => 'Power Outage', 'icon' => 'power outage', 'color_code' => '#FACC15'],
            ['name' => 'Unstable Internet', 'icon' => 'unstable internet', 'color_code' => '#A855F7'],
        ];

        foreach ($categories as $cat) {
            Category::updateOrCreate(['name' => $cat['name']], $cat);
        }
    }
}