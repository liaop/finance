<?php

namespace Database\Factories;

use App\Models\Ledger;
use App\Models\LedgerDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LedgerDetail>
 */
class LedgerDetailFactory extends Factory
{
    protected $model = LedgerDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ledger_id' => Ledger::factory(),
            'type' => fake()->randomElement(['income', 'expense']),
            'category' => fake()->randomElement(['food', 'transport', 'salary', 'shopping']),
            'amount' => fake()->randomFloat(2, 10, 5000),
            'currency' => 'CNY',
            'occurred_at' => fake()->dateTimeBetween('-1 month', 'now'),
            'description' => fake()->sentence(),
            'merchant' => fake()->company(),
        ];
    }
}
