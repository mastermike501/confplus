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

        $plainPw = 'password';
        $hashedPw = hash('sha256', $plainPw);
        $dbHashedPw = Hash::make($hashedPw);

        DB::table('users')->insert([
            'email' => 'delilah@CeBIT.attendee',
            'username' => 'DKessler',
            'password' => $dbHashedPw,
            'title' => 'Mr',
            'first_name' => 'Delilah',
            'last_name' => 'Kessler',
            'dob' => $faker->date,
            'street' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'country' => $faker->country
        ]);

        DB::table('users')->insert([
            'email' => 'elian@CeBIT.attendee',
            'username' => 'EKing',
            'password' => $dbHashedPw,
            'title' => 'Mr',
            'first_name' => 'Elian',
            'last_name' => 'King',
            'dob' => $faker->date,
            'street' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'country' => $faker->country
        ]);

        DB::table('users')->insert([
            'email' => 'maud@CeBIT.attendee',
            'username' => 'MDooley',
            'password' => $dbHashedPw,
            'title' => 'Mr',
            'first_name' => 'Maud',
            'last_name' => 'Dooley',
            'dob' => $faker->date,
            'street' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'country' => $faker->country
        ]);

        DB::table('users')->insert([
            'email' => 'josiane@CeBIT.attendee',
            'username' => 'JRunolfsdottir',
            'password' => $dbHashedPw,
            'title' => 'Miss',
            'first_name' => 'Josiane',
            'last_name' => 'Runolfsdottir',
            'dob' => $faker->date,
            'street' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'country' => $faker->country
        ]);

        DB::table('users')->insert([
            'email' => 'gideon@CeBIT.attendee',
            'username' => 'GHerman',
            'password' => $dbHashedPw,
            'title' => 'Mr',
            'first_name' => 'Gideon',
            'last_name' => 'Herman',
            'dob' => $faker->date,
            'street' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'country' => $faker->country
        ]);

        DB::table('users')->insert([
            'email' => 'sheldon@CeBIT.attendee',
            'username' => 'SWiegand',
            'password' => $dbHashedPw,
            'title' => 'Mr',
            'first_name' => 'Sheldon',
            'last_name' => 'Wiegand',
            'dob' => $faker->date,
            'street' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'country' => $faker->country
        ]);

        DB::table('users')->insert([
            'email' => 'kaylin@CeBIT.attendee',
            'username' => 'KLegros',
            'password' => $dbHashedPw,
            'title' => 'Miss',
            'first_name' => 'Kaylin',
            'last_name' => 'Legros',
            'dob' => $faker->date,
            'street' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'country' => $faker->country
        ]);

        DB::table('users')->insert([
            'email' => 'chaya@CeBIT.attendee',
            'username' => 'CRippin',
            'password' => $dbHashedPw,
            'title' => 'Miss',
            'first_name' => 'Chaya',
            'last_name' => 'Rippin',
            'dob' => $faker->date,
            'street' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'country' => $faker->country
        ]);

        DB::table('users')->insert([
            'email' => 'ardith@CeBIT.attendee',
            'username' => 'ARolfson',
            'password' => $dbHashedPw,
            'title' => 'Mr',
            'first_name' => 'Ardith',
            'last_name' => 'Rolfson',
            'dob' => $faker->date,
            'street' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'country' => $faker->country
        ]);

        DB::table('users')->insert([
            'email' => 'efren@CeBIT.attendee',
            'username' => 'ERobel',
            'password' => $dbHashedPw,
            'title' => 'Mr',
            'first_name' => 'Efren',
            'last_name' => 'Robel',
            'dob' => $faker->date,
            'street' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'country' => $faker->country
        ]);

        DB::table('users')->insert([
            'email' => 'felipa@CeBIT.attendee',
            'username' => 'FLeffler',
            'password' => $dbHashedPw,
            'title' => 'Miss',
            'first_name' => 'Felipa',
            'last_name' => 'Leffler',
            'dob' => $faker->date,
            'street' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'country' => $faker->country
        ]);

        DB::table('users')->insert([
            'email' => 'vincent@CeBIT.attendee',
            'username' => 'VPagac',
            'password' => $dbHashedPw,
            'title' => 'Mr',
            'first_name' => 'Vincent',
            'last_name' => 'Pagac',
            'dob' => $faker->date,
            'street' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'country' => $faker->country
        ]);

        DB::table('users')->insert([
            'email' => 'evelyn@CeBIT.attendee',
            'username' => 'EConsidine',
            'password' => $dbHashedPw,
            'title' => 'Miss',
            'first_name' => 'Evelyn',
            'last_name' => 'Considine',
            'dob' => $faker->date,
            'street' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'country' => $faker->country
        ]);

        DB::table('users')->insert([
            'email' => 'charity@CeBIT.attendee',
            'username' => 'CEichmann',
            'password' => $dbHashedPw,
            'title' => 'Miss',
            'first_name' => 'Charity',
            'last_name' => 'Eichmann',
            'dob' => $faker->date,
            'street' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'country' => $faker->country
        ]);

        DB::table('users')->insert([
            'email' => 'vida@CeBIT.attendee',
            'username' => 'VKlocko',
            'password' => $dbHashedPw,
            'title' => 'Miss',
            'first_name' => 'Vida',
            'last_name' => 'Klocko',
            'dob' => $faker->date,
            'street' => $faker->streetAddress,
            'city' => $faker->city,
            'state' => $faker->state,
            'country' => $faker->country
        ]);

        // $limit = 20;
        //
        // for ($i = 0; $i < $limit; $i++) {
        //     DB::table('users')->insert([
        //         'email' => $faker->email,
        //         'username' => str_random(5),
        //         'password' => str_random(10),
        //         'title' => 'Mr',
        //         'first_name' => $faker->firstName,
        //         'last_name' => $faker->lastName,
        //         'dob' => $faker->date,
        //         'street' => $faker->streetAddress,
        //         'city' => $faker->city,
        //         'state' => $faker->state,
        //         'country' => $faker->country
        //     ]);
        // }

        // users for CeBIT
        // DB::table('users')->insert([
        //     'email' => 'ken@gCeBIT.speaker',
        //     'username' => 'KEN GALLACHER',
        //     'password' => $dbHashedPw
        // ]);
        //
        // DB::table('users')->insert([
        //     'email' => 'rod@gCeBIT.speaker',
        //     'username' => 'ROD SMITH',
        //     'password' => $dbHashedPw
        // ]);
        //
        // DB::table('users')->insert([
        //     'email' => 'lynwen@gCeBIT.speaker',
        //     'username' => 'LYNWEN CONNICK',
        //     'password' => $dbHashedPw
        // ]);
        //
        // DB::table('users')->insert([
        //     'email' => 'duncan@gCeBIT.speaker',
        //     'username' => 'DUNCAN CHALLEN',
        //     'password' => $dbHashedPw
        // ]);
        //
        // DB::table('users')->insert([
        //     'email' => 'rocky@gCeBIT.speaker',
        //     'username' => 'ROCKY SCOPELLITI',
        //     'password' => $dbHashedPw
        // ]);
        //
        // DB::table('users')->insert([
        //     'email' => 'john@gCeBIT.speaker',
        //     'username' => 'JOHN DARDO',
        //     'password' => $dbHashedPw
        // ]);
        //
        // DB::table('users')->insert([
        //     'email' => 'gilbert@gCeBIT.speaker',
        //     'username' => 'GILBERT VERDIAN',
        //     'password' => $dbHashedPw
        // ]);
        //
        // DB::table('users')->insert([
        //     'email' => 'brad@gCeBIT.speaker',
        //     'username' => 'BRAD ROSSER',
        //     'password' => $dbHashedPw
        // ]);
        //
        // DB::table('users')->insert([
        //     'email' => 'steve@gCeBIT.speaker',
        //     'username' => 'STEVE BAXTER',
        //     'password' => $dbHashedPw
        // ]);
        //
        // DB::table('users')->insert([
        //     'email' => 'peter@gCeBIT.speaker',
        //     'username' => 'PETER STROHKORB',
        //     'password' => $dbHashedPw
        // ]);
        //
        // DB::table('users')->insert([
        //     'email' => 'stuart@gCeBIT.speaker',
        //     'username' => 'STUART CORNER',
        //     'password' => $dbHashedPw
        // ]);

        // DB::table('users')->insert([
        //     'email' => 'toby@gmail.com',
        //     'username' => 'toby',
        //     'password' => $dbHashedPw
        // ]);
        //
        // DB::table('users')->insert([
        //     'email' => 'matthew@gmail.com',
        //     'username' => 'matthew',
        //     'password' => $dbHashedPw
        // ]);
        //
        // DB::table('users')->insert([
        //     'email' => 'michael@gmail.com',
        //     'username' => 'michael',
        //     'password' => $dbHashedPw
        // ]);
        //
        // DB::table('users')->insert([
        //     'email' => 'sandon@gmail.com',
        //     'username' => 'sandon',
        //     'password' => $dbHashedPw
        // ]);
        //
        // DB::table('users')->insert([
        //     'email' => 'cy@gmail.com',
        //     'username' => 'cy',
        //     'password' => $dbHashedPw
        // ]);
        //
        // DB::table('users')->insert([
        //     'email' => 'looyee@gmail.com',
        //     'username' => 'looyee',
        //     'password' => $dbHashedPw
        // ]);
        //
        // DB::table('users')->insert([
        //     'email' => 'blithe@gmail.com',
        //     'username' => 'blithe',
        //     'password' => $dbHashedPw
        // ]);
    }
}
