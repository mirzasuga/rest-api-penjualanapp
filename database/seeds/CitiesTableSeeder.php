<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitiesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      	$data = RajaOngkir::city();
      	foreach ($data as $kota) {
  			DB::table('cities')->insert([
  				'name' => $kota->city_name,
  			]);
      	}
    }
}
