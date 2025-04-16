<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'name' => 'مدير النظام',
            'username' => 'admin',
            'email' => 'admin@meca.com',
            'password' => Hash::make('MECA123meca'), // يمكنك تغييره حسب الحاجة
        ]);
    }
}
