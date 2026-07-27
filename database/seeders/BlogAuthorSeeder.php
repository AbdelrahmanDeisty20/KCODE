<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BlogAuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'author@kcode.com'],
            [
                'name' => 'Blog Author',
                'password' => Hash::make('A11223344'),
                'phone' => '01000000000',
                'type' => 'blog_author',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
    }
}
