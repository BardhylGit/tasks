<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'id' => 1,
            'name' => 'admin',
            'display_name' => 'Administrator',
        ]);
        
        DB::table('roles')->insert([
            'id' => 2,
            'name' => 'regular',
            'display_name' => 'User',
        ]);
        
        DB::table('users')->insert([
            'f_name' => 'First',
            'l_name' => 'Admin',
            'email' => 'first_admin@tasks.org',
            'password' => bcrypt('123456'),
            'role_id' => 1,
        ]);
    }
}
