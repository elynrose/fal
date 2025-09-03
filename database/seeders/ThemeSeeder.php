<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $themes = [
            [
                'name' => 'Corporate',
                'description' => 'Professional business and office settings',
                'prompt_template' => 'professional corporate portrait, business attire, office environment, high quality, professional lighting',
                'icon' => 'briefcase'
            ],
            [
                'name' => 'Travel',
                'description' => 'Adventure and travel destinations',
                'prompt_template' => 'travel photography, scenic location, adventure, outdoor setting, natural lighting',
                'icon' => 'plane'
            ],
            [
                'name' => 'Fashion',
                'description' => 'Stylish fashion and modeling shots',
                'prompt_template' => 'fashion photography, stylish outfit, runway, studio lighting, high fashion',
                'icon' => 'tshirt'
            ],
            [
                'name' => 'Casual',
                'description' => 'Everyday casual and lifestyle photos',
                'prompt_template' => 'casual lifestyle photography, relaxed pose, natural setting, soft lighting',
                'icon' => 'smile'
            ],
            [
                'name' => 'Artistic',
                'description' => 'Creative and artistic interpretations',
                'prompt_template' => 'artistic portrait, creative composition, artistic lighting, unique style',
                'icon' => 'palette'
            ],
            [
                'name' => 'Sport',
                'description' => 'Athletic and sports activities',
                'prompt_template' => 'sports photography, athletic pose, dynamic action, sports environment',
                'icon' => 'trophy'
            ],
            [
                'name' => 'Wedding',
                'description' => 'Elegant wedding and formal events',
                'prompt_template' => 'wedding photography, elegant attire, romantic setting, soft romantic lighting',
                'icon' => 'heart'
            ],
            [
                'name' => 'Nature',
                'description' => 'Outdoor and natural environments',
                'prompt_template' => 'nature photography, outdoor setting, natural environment, golden hour lighting',
                'icon' => 'tree'
            ]
        ];

        foreach ($themes as $theme) {
            Theme::create($theme);
        }
    }
}
