<?php

use Illuminate\Database\Seeder;

class TestTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        // DB::table('test')->insert([
        //     'name' => str_random(10),
        //     'email' => str_random(10).'@gmail.com',
        //     'password' => bcrypt('secret'),
        // ]);

        $faker = Faker\Factory::create();

        // $limit = 33;
        //
        // for ($i = 0; $i < $limit; $i++) {
        //     DB::table('test')->insert([
        //         'name' => $faker->name,
        //         'email' => $faker->email,
        //         'password' => str_random(10),
        //     ]);
        // }
        DB::table('test')->insert([
            'name' => 'toby',
            'email' => 'toby@gmail.com',
            'password' => '1'
        ]);
    }
}
