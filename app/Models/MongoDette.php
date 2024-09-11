<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class MongoDette extends Model
{
    protected $connection = 'mongodb';
    protected $collection;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->collection = 'dettes_' . date('Y_m_d');
    }

    protected $fillable = [
        'montant',
        'client_id',
        'created_at',
        'updated_at',
        'montant_verse',
        'montant_du',
        'client',
        'articles',
        'paiements'
    ];
}
