<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'libelle',
        'prix',
        'qte',
    ];

    /**
     * The attributes that are not mass assignable
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * La relation "plusieurs à plusieurs" avec le modèle Dette.
     */
    public function dettes()
    {
        return $this->belongsToMany(Dette::class, 'article_dette')
            ->withPivot('qteVente', 'prixVente')
            ->withTimestamps();
    }
}
