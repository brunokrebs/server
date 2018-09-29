<?php

use Illuminate\Database\Seeder;
use Faker\Factory;
use App\Photo;

class PhotosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Factory::create();
        Photo::truncate();
        foreach(range(1, 10) as $i) {
        	$photo = Photo::create([
                //'user_id' => $i,
                'user_id' => 1,
        		'name' => $faker->sentence,
        		'description' => $faker->paragraph(mt_rand(10, 20)),
        		'image' => 'test.jpg'
        	]);
        	
        }
    }
}
