<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'surnom' => $this->faker->unique()->word, // Génère un surnom unique
            'telephone' => $this->faker->unique()->phoneNumber, // Génère un numéro de téléphone unique
            'adresse' => $this->faker->address, // Adresse optionnelle
            'user_id' => User::factory(), // Crée un utilisateur associé
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function wihoutCompte(): static
    {
        return $this->state(fn(array $attributes) => [
            'user_id' => null,
        ]);
    }
}
