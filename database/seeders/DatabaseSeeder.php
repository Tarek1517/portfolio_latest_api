<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'name' => 'Md tarek Hassan',
            'email' => 'tarek.hasan041517@gmail.com',
            'phone' => '01866041517',
            'password' => Hash::make('12345678'),
        ]);
    }
}