<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('stations')->insert([
            ['name'=>'Station 1', 'token'=>'abcdefgh', 'added_at'=>now(), 'ip_address'=>'192.168.22.201', 'mac_address'=>'84:0D:8E:AF:22:FF'],
            ['name'=>'Station 2', 'token'=>'abcdefgh', 'added_at'=>now(), 'ip_address'=>'192.168.22.202', 'mac_address'=>'84:0D:8E:AF:22:FA'],
            ['name'=>'Station A', 'token'=>'abcdefgh', 'added_at'=>now(), 'ip_address'=>'192.168.22.203', 'mac_address'=>'84:0D:8E:AF:22:FA'],
            ['name'=>'Station B', 'token'=>'abcdefgh', 'added_at'=>now(), 'ip_address'=>'192.168.22.204', 'mac_address'=>'84:0D:8E:AF:22:FA']
        ]);
    }
}
