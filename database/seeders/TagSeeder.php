<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run()
    {
        $tags = [
            ['name' => 'Urgent', 'color' => '#ef4444'],
            ['name' => 'Loan', 'color' => '#3b82f6'],
            ['name' => 'Research', 'color' => '#10b981'],
            ['name' => 'Technical', 'color' => '#8b5cf6'],
            ['name' => 'General', 'color' => '#64748b'],
        ];

        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
