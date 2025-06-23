<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('stations')->insert([
            ['name'=>'Station 1', 'token'=>'a1b2c3d4', 'added_at'=>Carbon::now()->subDays(30), 'ip_address'=>'192.168.22.201', 'mac_address'=>'84:0D:8E:AF:22:FF'],
            ['name'=>'Station 2', 'token'=>'e5f6g7h8', 'added_at'=>Carbon::now()->subDays(25), 'ip_address'=>'192.168.22.202', 'mac_address'=>'84:0D:8E:AF:22:FA'],
            ['name'=>'Station 3', 'token'=>'i9j0k1l2', 'added_at'=>Carbon::now()->subDays(20), 'ip_address'=>'192.168.22.203', 'mac_address'=>'84:0D:8E:B1:23:CD'],
            ['name'=>'Station 4', 'token'=>'m3n4o5p6', 'added_at'=>Carbon::now()->subDays(15), 'ip_address'=>'192.168.22.204', 'mac_address'=>'84:0D:8E:B2:24:DE'],
            ['name'=>'Station 5', 'token'=>'q7r8s9t0', 'added_at'=>Carbon::now()->subDays(10), 'ip_address'=>'192.168.22.205', 'mac_address'=>'84:0D:8E:B3:25:EF'],
            ['name'=>'Station 6', 'token'=>'u1v2w3x4', 'added_at'=>Carbon::now()->subDays(5), 'ip_address'=>'192.168.22.206', 'mac_address'=>'84:0D:8E:B4:26:FG'],
            ['name'=>'Station 7', 'token'=>'y5z6a7b8', 'added_at'=>Carbon::now(), 'ip_address'=>'192.168.22.207', 'mac_address'=>'84:0D:8E:B5:27:HI']
        ]);
    }
}
