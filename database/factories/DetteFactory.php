<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Client;
use App\Models\Dette;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class DetteFactory extends Factory
{
    protected $model = Dette::class;
    public function definition()
    {
        $montant = $this->faker->randomFloat(2, 100, 1000);
        $montantDu = $this->faker->randomFloat(2, 50, $montant);
        return [
            'montant' => $montant,
            'montantDu' => $montantDu,
            'montantRestant' => $montant - $montantDu,
            'client_id' => Client::factory(),
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Dette $dette) {
            $articles = Article::factory()->count(3)->create();

            foreach ($articles as $article) {
                DB::table('article_dette')->insert([
                    'dette_id' => $dette->id,
                    'article_id' => $article->id,
                    'qteVente' => rand(1, 5),
                    'prixVente' => $article->prix,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        });
    }
}
