<?php

use Illuminate\Database\Seeder;

class EventsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('events')->insert([
            'name' => 'CeBIT Australia 2016',
            'type' => 'Conference',
            'from_date' => '2016-08-02 10:00:00',
            'to_date' => '2016-08-04 17:00:00',
            'venue_id' => '11',
            'description' => 'Over 15,000 attendees will attend the CeBIT exhibition to join a community of business technology professionals keen to discover the latest cutting edge technologies. More than 300 exhibiting organisations will showcase a range of technologies and services across 12 showfloor categories including Cloud; Unified Communications; Big Data + Analytics; Mobility/M2M & IoT; Financial Tech + eCommerce; Education; Digital Marketing; Smart Office; Enterprise Security; BPM + Software; IT Hardware + Data Storage; & Managed Services. CeBIT is the one-stop-shop for all of the latest business technology solutions. Regardless of your business issues, you’ll find a solution at CeBIT.',
            'url' => 'abc',
            'privacy' => 'public',
            'payee' => 'toby@eventure.management',
            'cardNum' => '1234 5678 9012 3456',
            'contact_num' => '0478123456'
        ]);
    }
}
