<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CastMember;
use Faker\Generator as Faker;

$factory->define(CastMember::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'type' => rand(1, 10) % 5 === 0 ? CastMember::TYPE_DIRECTOR : CastMember::TYPE_ACTOR
    ];
});
