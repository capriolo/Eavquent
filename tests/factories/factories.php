<?php

$factory->define(Company::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->company
    ];
});

$factory->define(\Capriolo\Eavquent\Value\Data\Varchar::class, function (Faker\Generator $faker) {
    return [];
});