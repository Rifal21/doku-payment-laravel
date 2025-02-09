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
            [
                'name' => 'Gaming Laptop Z',
                'slug' => Str::slug('Gaming Laptop Z'),
                'short_description' => 'Powerful laptop for gaming and work',
                'category_id' => 1, // Electronics
                'description' => 'High-performance laptop with RTX 3060 and 16GB RAM.',
                'price' => 15000000,
                'stock' => 15,
                'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQpY7klmL6ZsM74nyBC8NAT78gwbRwtLvoKnw&s',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Bluetooth Headphones',
                'slug' => Str::slug('Bluetooth Headphones'),
                'short_description' => 'Wireless noise-canceling headphones',
                'category_id' => 1, // Electronics
                'description' => 'Enjoy high-quality sound with active noise cancellation.',
                'price' => 2000000,
                'stock' => 40,
                'image' => 'https://jete.id/wp-content/uploads/2023/06/jete-13-pro-13.jpg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Mechanical Keyboard',
                'slug' => Str::slug('Mechanical Keyboard'),
                'short_description' => 'RGB mechanical keyboard for gamers',
                'category_id' => 1, // Electronics
                'description' => 'Fast response and customizable RGB lighting for better gaming experience.',
                'price' => 800000,
                'stock' => 25,
                'image' => 'https://www.keychron.id/cdn/shop/products/Keychron-K6-compact-65-percent-wireless-mechanical-keyboard-for-Mac-Windows-iOS-keychron-switch-red-with-type-C-non-backlight_9b82e62e-48b9-4ec2-a21c-9775ab9ced60.jpg?v=1661500750&width=1214',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Smartwatch Pro',
                'slug' => Str::slug('Smartwatch Pro'),
                'short_description' => 'Advanced fitness tracker and smartwatch',
                'category_id' => 1, // Electronics
                'description' => 'Monitor your health and notifications on the go.',
                'price' => 1500000,
                'stock' => 35,
                'image' => 'https://i.pinimg.com/236x/72/09/94/72099421fbb7e708d6f48f7aa4ad8478.jpg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Wireless Mouse',
                'slug' => Str::slug('Wireless Mouse'),
                'short_description' => 'Ergonomic and precise wireless mouse',
                'category_id' => 1, // Electronics
                'description' => 'Perfect for work and gaming, with long battery life.',
                'price' => 500000,
                'stock' => 50,
                'image' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSOkiYhrMyLeq-x_ltgQXhwZog6zRPm1F9qgg&s',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Backpack Traveler',
                'slug' => Str::slug('Backpack Traveler'),
                'short_description' => 'Spacious and durable travel backpack',
                'category_id' => 3, // Accessories
                'description' => 'Designed for travelers with multiple compartments.',
                'price' => 700000,
                'stock' => 20,
                'image' => 'https://i.pinimg.com/236x/81/d7/68/81d768f58c74a0f37e9d758b2c4b1515.jpg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'name' => 'Digital Camera Pro',
                'slug' => Str::slug('Digital Camera Pro'),
                'short_description' => 'High-resolution digital camera',
                'category_id' => 1, // Electronics
                'description' => 'Capture stunning images with 4K video recording.',
                'price' => 8000000,
                'stock' => 10,
                'image' => 'https://i.pinimg.com/236x/47/12/a3/4712a3dc42c64f509de8cbb2f6d39f82.jpg',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('products')->insert($products);
    }
}
