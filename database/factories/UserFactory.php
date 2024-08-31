<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    // protected $model = User::class;

    public function definition()
    {
        return [
            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'login' => $this->faker->unique()->userName,
            'password' => 'password',
            'role_id' => null,
            'active' => true,
        ];
    }

    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'role_id' => Role::where('role', 'ADMIN')->first()->id,
            ];
        });
    }

    public function boutiquier()
    {
        return $this->state(function (array $attributes) {
            return [
                'role_id' => Role::where('role', 'BOUTIQUIER')->first()->id,
            ];
        });
    }

    public function client()
    {
        return $this->state(function (array $attributes) {
            return [
                'role_id' => Role::where('role', 'CLIENT')->first()->id,
            ];
        });
    }
}
