<?php

use Illuminate\Database\Seeder;

class RoomsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // University of Wollongong
        DB::table('rooms')->insert([
            'venue_id' => '1',
            'name' => 'University Hall',
            'type' => 'Hall',
            'capacity' => '800'
        ]);

        DB::table('rooms')->insert([
            'venue_id' => '2',
            'name' => 'Room 123',
            'type' => 'Room',
            'capacity' => '30'
        ]);

        DB::table('rooms')->insert([
            'venue_id' => '2',
            'name' => 'Room 124',
            'type' => 'Room',
            'capacity' => '30'
        ]);

        DB::table('rooms')->insert([
            'venue_id' => '2',
            'name' => 'Room 125',
            'type' => 'Room',
            'capacity' => '30'
        ]);

        DB::table('rooms')->insert([
            'venue_id' => '2',
            'name' => 'Room 126',
            'type' => 'Room',
            'capacity' => '30'
        ]);

        DB::table('rooms')->insert([
            'venue_id' => '2',
            'name' => 'Room 127',
            'type' => 'Room',
            'capacity' => '30'
        ]);

        // Rosehill Gardens
        DB::table('rooms')->insert([
            'venue_id' => '3',
            'name' => 'Grand Pavilion',
            'type' => 'Pavilion',
            'capacity' => '1000'
        ]);
    }
}
