<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->userRole()->create(['email' => 'user1@mail.com']);
        User::factory()->userRole()->create(['email' => 'user2@mail.com']);
        User::factory()->administrator()->create(['email' => 'admin@mail.com']);
    }
}
