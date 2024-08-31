<?php

namespace Database\Factories;

use App\Models\Article;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    protected $model = Article::class;

    public function definition()
    {
        return [
            'libelle' => $this->faker->unique()->word,
            'prix' => $this->faker->randomFloat(2, 50, 500),
            'qte' => $this->faker->numberBetween(10, 100),
        ];
    }
}
