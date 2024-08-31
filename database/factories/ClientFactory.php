<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    // protected $model = Client::class;

    public function definition()
    {
        return [
            'surnom' => $this->faker->unique()->userName,
            'telephone' => $this->faker->unique()->phoneNumber,
            'adresse' => $this->faker->address,
            'user_id' => null,  // à définir dans le seeder
        ];
    }

    public function withUser()
    {
        return $this->afterMaking(function (Client $client) {
            $client->user_id = User::factory()->client()->create()->id;
        });
    }
}
