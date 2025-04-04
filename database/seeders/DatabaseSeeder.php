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
        User::factory(10)->create();
        Lawyer::factory(100)->create();
        Rating::factory(100)->create();
        Subscription::factory(3)->create();
    }


}
