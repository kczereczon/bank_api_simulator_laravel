<?php

namespace Database\Factories;

use App\Models\BankingAccount;
use Illuminate\Database\Eloquent\Factories\Factory;

class BankingAccountFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BankingAccount::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nrb' => $this->faker->regexify('[0-9]{2}', true) . env('BANK_NUMBER', "false") . $this->faker->unique()->regexify('[0-9]{16}', true),
            'balance' => $this->faker->randomFloat(2,0)
        ];
    }
}
