<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Video;
use Faker\Generator as Faker;

$factory->define(Video::class, function (Faker $faker) {
    $rating = Video::RATING_LIST[array_rand(Video::RATING_LIST)];
    return [
        'title' => $faker->sentence(3),
        'description' => $faker->sentence(10),
        'year_launched' => rand(1920, 2020),
        'rating' => $rating,
        'opened' => (bool)rand(0, 1),
        'duration' => rand(1, 3)
    ];
});
