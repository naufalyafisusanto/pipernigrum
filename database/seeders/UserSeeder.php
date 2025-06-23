<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'username' => 'adminadmin',
                'password' => Hash::make('adminpipernigrum'),
                'admin' => 1,
                'date_created' => Carbon::now(),
                'last_login' => Carbon::now()
            ],
            [
                'name' => 'Operator',
                'username' => 'operator',
                'password' => Hash::make('operatorpipernigrum'),
                'admin' => 0,
                'date_created' => Carbon::now()->subDays(15),
                'last_login' => Carbon::now()->subHours(5)
            ]
        ]);
    }
}
