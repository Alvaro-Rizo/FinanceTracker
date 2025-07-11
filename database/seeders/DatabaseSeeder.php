<?php



namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Database\Factories\CategoryFactory;
use Database\Factories\AccountFactory;
use Database\Factories\TransactionFactory;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Usuario demo
     $user = User::firstOrCreate([
            'email' => 'demo@financetracker.com',
        ], [
            'name' => 'Usuario Demo',
            'password' => Hash::make('password'),
            'currency' => 'NIO',
            'date_format' => 'd/m/Y'
        ]);
        // Utilizar factories para generar datos falsos
        Category::factory()->count(5)->create(['user_id' => $user->id]);
        Account::factory()->count(3)->create(['user_id' => $user->id]);
        Transaction::factory()->count(10)->create(['user_id' => $user->id]);
    }
}