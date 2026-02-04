<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Flood', 
                'icon' => 'flood', 
                'color_code' => '#F97316', // オレンジ系
                'description' => 'River flooding and street inundation.'
            ],
            [
                'name' => 'Heavy Rain', 
                'icon' => 'heavy_rain', 
                'color_code' => '#3B82F6', 
                'description' => 'Extreme rainfall and storm alerts.'
            ],
            [
                'name' => 'Tsunami', 
                'icon' => 'tsunami', 
                'color_code' => '#0EA5E9', 
                'description' => 'Tsunami warnings and sea level alerts.'
            ],
            [
                'name' => 'Road Closure', 
                'icon' => 'road_closure', 
                'color_code' => '#F59E0B', 
                'description' => 'Infrastructure damage and blocked paths.'
            ],
            [
                'name' => 'Fire', 
                'icon' => 'fire', 
                'color_code' => '#EF4444', 
                'description' => 'Fire incidents and smoke hazards.'
            ],
            [
                'name' => 'Lightning', 
                'icon' => 'lightning', 
                'color_code' => '#FBBF24', 
                'description' => 'Thunderstorms and electrical hazards.'
            ],
            [
                'name' => 'Water Outage', 
                'icon' => 'water_outage', 
                'color_code' => '#60A5FA', 
                'description' => 'Water supply maintenance and failures.'
            ],
            [
                'name' => 'Power Outage', 
                'icon' => 'power_outage', 
                'color_code' => '#FACC15', 
                'description' => 'Grid failures and electricity loss.'
            ],
            [
                'name' => 'Unstable Internet', 
                'icon' => 'unstable_internet', 
                'color_code' => '#A855F7', 
                'description' => 'Network connectivity and signal issues.'
            ],
        ];

        foreach ($categories as $cat) {
            // 名前をキーにして、重複があれば更新、なければ作成するにょ
            Category::updateOrCreate(['name' => $cat['name']], $cat);
        }
    }
}