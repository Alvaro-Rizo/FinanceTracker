<?php
// database/factories/AccountFactory.php

namespace Database\Factories;

use App\Models\User;
use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            'type' => $this->faker->randomElement(['cash', 'bank', 'credit']),
            'balance' => $this->faker->randomFloat(2, 0, 1000),
            'initial_balance' => $this->faker->randomFloat(2, 0, 1000),
            'user_id' => User::inRandomOrder()->first()->id ?? User::create(['name' => 'Default User', 'email' => 'default@example.com', 'password' => 'password'])->id,
        ];
    }
}