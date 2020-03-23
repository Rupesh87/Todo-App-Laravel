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
	    DB::table('users')->insert([
            'is_super_admin' => 1,
	        'is_admin'    => 1,
            'name'     => 'Test User',
            'email'    => 'test@test.com',
            'password' => bcrypt('1234'),
        ]);
    }
}