<?php

namespace Database\Seeders;

use App\Models\News;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create 5 featured and published news
        News::factory()
            ->count(5)
            ->featured()
            ->published()
            ->create();

        // Create 20 regular published news
        News::factory()
            ->count(20)
            ->published()
            ->create();

        // Create 5 unpublished news (drafts)
        News::factory()
            ->count(5)
            ->unpublished()
            ->create();
    }
}
