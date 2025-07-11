<?php

// database/factories/TransactionFactory.php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Category;
use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        return [
            'type' => $this->faker->randomElement(['income', 'expense']),
            'amount' => $this->faker->randomFloat(2, 0, 1000),
            'category_id' => Category::inRandomOrder()->first()->id ?? Category::create(['name' => 'Default Category', 'type' => 'expense', 'user_id' => User::inRandomOrder()->first()->id])->id,
            'account_id' => Account::inRandomOrder()->first()->id ?? Account::create(['name' => 'Default Account', 'type' => 'cash', 'balance' => 0, 'user_id' => User::inRandomOrder()->first()->id])->id,
            'date' => $this->faker->date(),
            'description' => $this->faker->sentence,
            'user_id' => User::inRandomOrder()->first()->id,
        ];
    }
}