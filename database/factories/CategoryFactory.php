<?php
// database/factories/CategoryFactory.php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'type' => $this->faker->randomElement(['income', 'expense']),
            'user_id' => User::inRandomOrder()->first()->id ?? User::create(['name' => 'Default User', 'email' => 'default@example.com', 'password' => 'password'])->id,
        ];
    }
}