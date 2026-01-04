<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'Laravel Blog',
                'type' => 'string',
            ],
            [
                'key' => 'primary_color',
                'value' => '#6366f1',
                'type' => 'color',
            ],
            [
                'key' => 'seo_title',
                'value' => 'Laravel Blog - Engineering Notes on Backend Development',
                'type' => 'text',
            ],
            [
                'key' => 'seo_description',
                'value' => 'Practical articles about web development, clean architecture, performance, and modern software engineering practices.',
                'type' => 'text',
            ],
        ];

        foreach ($settings as $setting) {
            SiteSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
