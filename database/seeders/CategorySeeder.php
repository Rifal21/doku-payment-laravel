<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Electronics', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Fashion', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Home & Living', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Health & Beauty', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Sports & Outdoors', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ];

        DB::table('categories')->insert($categories);
    }
}
