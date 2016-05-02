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
        $faker = Faker\Factory::create();

        DB::table('users')->insert([
            'email' => 'toby@gmail.com',
            'username' => 'toby',
            'password' => 'toby'
        ]);

        DB::table('users')->insert([
            'email' => 'matthew@gmail.com',
            'username' => 'matthew',
            'password' => 'matthew'
        ]);

        DB::table('users')->insert([
            'email' => 'michael@gmail.com',
            'username' => 'michael',
            'password' => 'michael'
        ]);

        DB::table('users')->insert([
            'email' => 'sandon@gmail.com',
            'username' => 'sandon',
            'password' => 'sandon'
        ]);

        DB::table('users')->insert([
            'email' => 'cy@gmail.com',
            'username' => 'cy',
            'password' => 'cy'
        ]);

        DB::table('users')->insert([
            'email' => 'looyee@gmail.com',
            'username' => 'looyee',
            'password' => 'looyee'
        ]);

        DB::table('users')->insert([
            'email' => 'blithe@gmail.com',
            'username' => 'blithe',
            'password' => 'blithe'
        ]);
    }
}
