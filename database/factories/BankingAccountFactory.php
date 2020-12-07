<?php

namespace Database\Factories;

use App\Models\BankingAccount;
use App\Services\BankService;
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
        $bankService = new BankService();

        return [
            'nrb' => $bankService->generateIban(),
            'balance' => $this->faker->randomFloat(2,0)
        ];
    }
}
