<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'login' => $this->faker->unique()->safeEmail, // Génère un email unique
            'password' => Hash::make('password'), // ou bcrypt('password')
            'role' => $this->faker->randomElement(['ADMIN', 'BOUTIQUIER']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Admin user
     */
    public function admin(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'ADMIN',
        ]);
    }
    /**
     * Admin boutiquier
     */
    public function boutiquier(): static
    {
        return $this->state(fn(array $attributes) => [
            'role' => 'BOUTIQUIER',
        ]);
    }
}
