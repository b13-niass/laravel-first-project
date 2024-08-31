<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Dette;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::factory()->withUser()->count(3)->create()->each(function ($client) {
            Dette::factory()->count(2)->create(['client_id' => $client->id]);
        });
    }
}
