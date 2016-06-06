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
        // University Hall of University of Wollongong
        // for ($i = 0; $i < 800; $i++) {
        //     DB::table('seats')->insert([
        //         'venue_id' => '1',
        //         'room_name' => 'University Hall',
        //         'seat_num' => $i + 1
        //     ]);
        // }
        //
        // for ($i = 0; $i < 30; $i++) {
        //     DB::table('seats')->insert([
        //         'venue_id' => '2',
        //         'room_name' => 'Room 123',
        //         'seat_num' => $i + 1
        //     ]);
        // }
        //
        // for ($i = 0; $i < 30; $i++) {
        //     DB::table('seats')->insert([
        //         'venue_id' => '2',
        //         'room_name' => 'Room 124',
        //         'seat_num' => $i + 1
        //     ]);
        // }
        //
        // for ($i = 0; $i < 30; $i++) {
        //     DB::table('seats')->insert([
        //         'venue_id' => '2',
        //         'room_name' => 'Room 125',
        //         'seat_num' => $i + 1
        //     ]);
        // }
        //
        // for ($i = 0; $i < 30; $i++) {
        //     DB::table('seats')->insert([
        //         'venue_id' => '2',
        //         'room_name' => 'Room 126',
        //         'seat_num' => $i + 1
        //     ]);
        // }
        //
        // for ($i = 0; $i < 30; $i++) {
        //     DB::table('seats')->insert([
        //         'venue_id' => '2',
        //         'room_name' => 'Room 127',
        //         'seat_num' => $i + 1
        //     ]);
        // }
        //
        // for ($i = 0; $i < 1000; $i++) {
        //     DB::table('seats')->insert([
        //         'venue_id' => '3',
        //         'room_name' => 'Grand Pavilion',
        //         'seat_num' => $i + 1
        //     ]);
        // }

        // Venue_id = 11
        for ($i = 0; $i < 200; $i++) {
            DB::table('seats')->insert([
                'venue_id' => '11',
                'name' => 'Stage A',
                'seat_num' => $i + 1
            ]);
        }

        for ($i = 0; $i < 200; $i++) {
            DB::table('seats')->insert([
                'venue_id' => '11',
                'name' => 'Stage B',
                'seat_num' => $i + 1
            ]);
        }

        for ($i = 0; $i < 200; $i++) {
            DB::table('seats')->insert([
                'venue_id' => '11',
                'name' => 'Stage B',
                'seat_num' => $i + 1
            ]);
        }

        // Venue_id = 9
        // for ($i = 0; $i < 2000; $i++) {
        //     DB::table('seats')->insert([
        //         'venue_id' => '9',
        //         'name' => 'Room 1',
        //         'seat_num' => $i + 1
        //     ]);
        // }
        //
        // for ($i = 0; $i < 2000; $i++) {
        //     DB::table('seats')->insert([
        //         'venue_id' => '9',
        //         'name' => 'Room 2',
        //         'seat_num' => $i + 1
        //     ]);
        // }
        //
        // for ($i = 0; $i < 1000; $i++) {
        //     DB::table('seats')->insert([
        //         'venue_id' => '9',
        //         'name' => 'Room 3',
        //         'seat_num' => $i + 1
        //     ]);
        // }
        //
        // for ($i = 0; $i < 500; $i++) {
        //     DB::table('seats')->insert([
        //         'venue_id' => '9',
        //         'name' => 'Room 4',
        //         'seat_num' => $i + 1
        //     ]);
        // }
        //
        // for ($i = 0; $i < 500; $i++) {
        //     DB::table('seats')->insert([
        //         'venue_id' => '9',
        //         'name' => 'Room 5',
        //         'seat_num' => $i + 1
        //     ]);
        // }
        //
        // for ($i = 0; $i < 30; $i++) {
        //     DB::table('seats')->insert([
        //         'venue_id' => '9',
        //         'name' => 'Room 6',
        //         'seat_num' => $i + 1
        //     ]);
        // }
    }
}
