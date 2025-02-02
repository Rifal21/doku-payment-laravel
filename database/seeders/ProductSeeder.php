<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'name' => 'Smartphone X',
                'slug' => Str::slug('Smartphone X'),
                'short_description' => 'High-end smartphone with advanced features',
                'category_id' => 1, // Electronics
                'description' => 'This smartphone has a 6.5-inch display, 128GB storage, and a powerful processor.',
                'price' => 5000000,
                'stock' => 50,
                'image' => 'https://i.pinimg.com/236x/83/39/82/8339823656ee1fb3d5487e9ecd86c971.jpg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Running Shoes Pro',
                'slug' => Str::slug('Running Shoes Pro'),
                'short_description' => 'Comfortable and durable running shoes',
                'category_id' => 5, // Sports & Outdoors
                'description' => 'Perfect for long runs and everyday workouts with great cushioning.',
                'price' => 1200000,
                'stock' => 30,
                'image' => 'https://i.pinimg.com/236x/de/2d/02/de2d021e61235ccfcd81fa355f6790fc.jpg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Designer Jacket',
                'slug' => Str::slug('Designer Jacket'),
                'short_description' => 'Stylish and warm winter jacket',
                'category_id' => 2, // Fashion
                'description' => 'Premium fabric with a comfortable fit, perfect for cold weather.',
                'price' => 250000,
                'stock' => 20,
                'image' => 'https://i.pinimg.com/236x/e3/76/ae/e376aef7c23443df26dd9c95e20024a1.jpg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('products')->insert($products);
    }
}
