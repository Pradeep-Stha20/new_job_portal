<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create some users, categories and job types first so foreign keys exist
        \App\Models\User::factory(5)->create();

        \App\Models\Category::factory(5)->create();
        \App\Models\JobType::factory(5)->create();

        // Create jobs after the related tables have records
        \App\Models\Job::factory(20)->create();
    }
}
