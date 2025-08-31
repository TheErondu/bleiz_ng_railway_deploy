<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run()
{
    $admin = User::firstOrCreate(
        ['email' => 'admin@bleiz.ng'],
        [
            'name' => 'Bleiz Admin',
            'password' => bcrypt('Test123#') // change this later
        ]
    );

    $admin->assignRole('admin');
}
}
