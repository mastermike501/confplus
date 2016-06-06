<?php

use Illuminate\Database\Seeder;

class VenuesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$faker = Faker\Factory::create();

        DB::table('venues')->insert([
            'name' => 'UOW University Hall',
            'type' => 'Hall',
            'street' => 'Northfields Ave',
            'city' => 'Wollongong',
            'state' => 'NSW',
            'country' => 'Australia',
            'longitude' => '151.053073',
            'latitude' => '-34.040039'
        ]);

        DB::table('venues')->insert([
            'name' => 'UOW Building 3',
            'type' => 'Building',
            'street' => 'Northfields Ave',
            'city' => 'Wollongong',
            'state' => 'NSW',
            'country' => 'Australia',
            'longitude' => '151.053073',
            'latitude' => '-34.040039'
        ]);

        DB::table('venues')->insert([
            'name' => 'Rosehill Gardens',
            'type' => 'Garden',
            'street' => 'James Ruse Dr',
            'city' => 'Rosehill',
            'state' => 'NSW',
            'country' => 'Australia',
            'longitude' => '151.021343',
            'latitude' => '-33.823615'
        ]);

        DB::table('venues')->insert([
            'name' => 'Rosehill Gardens',
            'type' => 'Garden',
            'street' => 'James Ruse Dr',
            'city' => 'Rosehill',
            'state' => 'NSW',
            'country' => 'Australia',
            'longitude' => '151.021343',
            'latitude' => '-33.823615'
        ]);

        DB::table('venues')->insert([
            'name' => 'Rosehill Gardens',
            'type' => 'Garden',
            'street' => 'James Ruse Dr',
            'city' => 'Rosehill',
            'state' => 'NSW',
            'country' => 'Australia',
            'longitude' => '151.021343',
            'latitude' => '-33.823615'
        ]);

        // DB::table('venues')->insert([
        //     'name' => 'University of Wollongong',
        //     'type' => 'University',
        //     'street' => 'Northfields Ave',
        //     'city' => 'Wollongong',
        //     'state' => 'NSW',
        //     'country' => 'Australia',
        //     'longitude' => '151.053073',
        //     'latitude' => '-34.040039'
        // ]);
        //
        // // $limit = 5;
        // //
        // // for ($i = 0; $i < $limit; $i++) {
        // //     DB::table('venues')->insert([
        // //         'name' => 'venue2',
        // //         'type' => 'building',
        // //         'street' => $faker->streetAddress,
        // //         'city' => $faker->city,
        // //         'state' => $faker->state,
        // //         'country' => $faker->country,
        // //         'longitude' => $faker->longitude($min = -180, $max = 180),
        // //         'latitude' => $faker->latitude($min = -90, $max = 90)
        // //     ]);
        // // }
        //
        // DB::table('venues')->insert([
        //     'name' => 'venue2',
        //     'type' => 'building',
        //     'street' => $faker->streetAddress,
        //     'city' => $faker->city,
        //     'state' => $faker->state,
        //     'country' => $faker->country,
        //     'longitude' => $faker->longitude($min = -180, $max = 180),
        //     'latitude' => $faker->latitude($min = -90, $max = 90)
        // ]);
        //
        // DB::table('venues')->insert([
        //     'name' => 'venue3',
        //     'type' => 'building',
        //     'street' => $faker->streetAddress,
        //     'city' => $faker->city,
        //     'state' => $faker->state,
        //     'country' => $faker->country,
        //     'longitude' => $faker->longitude($min = -180, $max = 180),
        //     'latitude' => $faker->latitude($min = -90, $max = 90)
        // ]);
        //
        // DB::table('venues')->insert([
        //     'name' => 'venue4',
        //     'type' => 'building',
        //     'street' => $faker->streetAddress,
        //     'city' => $faker->city,
        //     'state' => $faker->state,
        //     'country' => $faker->country,
        //     'longitude' => $faker->longitude($min = -180, $max = 180),
        //     'latitude' => $faker->latitude($min = -90, $max = 90)
        // ]);
        //
        // DB::table('venues')->insert([
        //     'name' => 'venue5',
        //     'type' => 'building',
        //     'street' => $faker->streetAddress,
        //     'city' => $faker->city,
        //     'state' => $faker->state,
        //     'country' => $faker->country,
        //     'longitude' => $faker->longitude($min = -180, $max = 180),
        //     'latitude' => $faker->latitude($min = -90, $max = 90)
        // ]);
    }
}
