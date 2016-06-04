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
        $limit = 33;

        for ($i = 0; $i < $limit; $i++) {
            DB::table('seats')->insert([
                'venue_id' => '9',
                'name' => 'Room 1',
                'seat_num' => $i + 1
            ]);
        }
    }
}
