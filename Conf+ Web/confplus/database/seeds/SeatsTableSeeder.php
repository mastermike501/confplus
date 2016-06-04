<?php

use Illuminate\Database\Seeder;

class SeatsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$limit = 1000;

        for ($i = 0; $i < 2000; $i++) {
            DB::table('seats')->insert([
                'venue_id' => '9',
                'name' => 'Room 1',
                'seat_num' => $i + 1
            ]);
        }

        for ($i = 0; $i < 2000; $i++) {
            DB::table('seats')->insert([
                'venue_id' => '9',
                'name' => 'Room 2',
                'seat_num' => $i + 1
            ]);
        }

        for ($i = 0; $i < 1000; $i++) {
            DB::table('seats')->insert([
                'venue_id' => '9',
                'name' => 'Room 3',
                'seat_num' => $i + 1
            ]);
        }

        for ($i = 0; $i < 500; $i++) {
            DB::table('seats')->insert([
                'venue_id' => '9',
                'name' => 'Room 4',
                'seat_num' => $i + 1
            ]);
        }

        for ($i = 0; $i < 500; $i++) {
            DB::table('seats')->insert([
                'venue_id' => '9',
                'name' => 'Room 5',
                'seat_num' => $i + 1
            ]);
        }

        for ($i = 0; $i < 30; $i++) {
            DB::table('seats')->insert([
                'venue_id' => '9',
                'name' => 'Room 6',
                'seat_num' => $i + 1
            ]);
        }
    }
}
