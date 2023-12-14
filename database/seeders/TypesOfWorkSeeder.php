<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\types_work;

class TypesOfWorkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $TypesOfWorks = 
            [
                'Retail Wax Casting',
                'Wholsale wax casting',
                'Retail cam casting',
                'Wholsale cam casting',
                'Retail ready jewellery',
                'Wholsale ready jewellery',
            ];

        $OrderValues = [
            'RW',
            "WHW",
            "RC",
            "WHC",
            "RRJ",
            "WHRJ",
        ]    

        foreach ($TypesOfWorks as $TypesOfWork) {
            types_work::create(['types_of_works' => $TypesOfWork]);
       }

       foreach ($OrderValues as $OrderValue) {
           types_work::create(['order_value' => $OrderValue]);
   }
    }
}
