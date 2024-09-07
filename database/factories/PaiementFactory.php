<?php

namespace Database\Factories;

use App\Models\Dette;
use App\Models\Paiement;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Paiement>
 */
class PaiementFactory extends Factory
{
    protected $model = Paiement::class;

    public function definition()
    {
        return [
            'montant' => $this->faker->randomFloat(2, 50, 500),
            'dette_id' => Dette::factory(), // Associe le paiement à une dette
        ];
    }
}
