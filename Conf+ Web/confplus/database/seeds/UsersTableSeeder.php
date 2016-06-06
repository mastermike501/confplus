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
        //$faker = Faker\Factory::create();

        $plainPw = 'password';
        $hashedPw = hash('sha256', $plainPw);
        $dbHashedPw = Hash::make($hashedPw);

        // users for CeBIT
        DB::table('users')->insert([
            'email' => 'ken@gCeBIT.speaker',
            'username' => 'KEN GALLACHER',
            'password' => $dbHashedPw
        ]);

        DB::table('users')->insert([
            'email' => 'rod@gCeBIT.speaker',
            'username' => 'ROD SMITH',
            'password' => $dbHashedPw
        ]);

        DB::table('users')->insert([
            'email' => 'lynwen@gCeBIT.speaker',
            'username' => 'LYNWEN CONNICK',
            'password' => $dbHashedPw
        ]);

        DB::table('users')->insert([
            'email' => 'duncan@gCeBIT.speaker',
            'username' => 'DUNCAN CHALLEN',
            'password' => $dbHashedPw
        ]);

        DB::table('users')->insert([
            'email' => 'rocky@gCeBIT.speaker',
            'username' => 'ROCKY SCOPELLITI',
            'password' => $dbHashedPw
        ]);

        DB::table('users')->insert([
            'email' => 'john@gCeBIT.speaker',
            'username' => 'JOHN DARDO',
            'password' => $dbHashedPw
        ]);

        DB::table('users')->insert([
            'email' => 'gilbert@gCeBIT.speaker',
            'username' => 'GILBERT VERDIAN',
            'password' => $dbHashedPw
        ]);

        DB::table('users')->insert([
            'email' => 'brad@gCeBIT.speaker',
            'username' => 'BRAD ROSSER',
            'password' => $dbHashedPw
        ]);

        DB::table('users')->insert([
            'email' => 'steve@gCeBIT.speaker',
            'username' => 'STEVE BAXTER',
            'password' => $dbHashedPw
        ]);

        DB::table('users')->insert([
            'email' => 'peter@gCeBIT.speaker',
            'username' => 'PETER STROHKORB',
            'password' => $dbHashedPw
        ]);

        DB::table('users')->insert([
            'email' => 'stuart@gCeBIT.speaker',
            'username' => 'STUART CORNER',
            'password' => $dbHashedPw
        ]);

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
