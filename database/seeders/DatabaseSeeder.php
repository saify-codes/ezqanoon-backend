<?php

namespace Database\Seeders;

use App\Models\Lawyer;
use App\Models\Rating;
use App\Models\Subscription;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Subscription::factory(3)->create();
        // Lawyer::factory(1)->create();
        // User::factory(10)->create();
    }


}
