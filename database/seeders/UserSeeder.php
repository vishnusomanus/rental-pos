<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::updateOrCreate([
            'email' => 'superadmin@gmail.com'
        ], [
            'first_name' => 'Admin',
            'last_name' => 'admin',
            'email' => 'superadmin@gmail.com',
            'phone' => '9876543210',
            'role' => 'super_admin',
            'white_label_id' => null,
            'address' => 'Chadayamangalam Kollam Kerala',
            'password' => bcrypt('admin123')
        ]);
    
        User::updateOrCreate([
            'email' => 'admin@gmail.com'
        ], [
            'first_name' => 'admin',
            'last_name' => 'admin',
            'email' => 'admin@gmail.com',
            'phone' => '1234567890',
            'role' => 'admin',
            'white_label_id' => 1,
            'address' => 'Some Address 1',
            'password' => bcrypt('admin123')
        ]);
        
        User::updateOrCreate([
            'email' => 'staff@gmail.com'
        ], [
            'first_name' => 'staff',
            'last_name' => 'staff',
            'email' => 'staff@gmail.com',
            'phone' => '9876543210',
            'role' => 'staff',
            'white_label_id' => 1,
            'address' => 'Some Address 2',
            'password' => bcrypt('admin123')
        ]);
    }
    
}
