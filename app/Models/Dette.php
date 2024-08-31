<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dette extends Model
{
    use HasFactory;

    protected $fillable = [
        'montant',
        'montantDu',
        'montantRestant',
        'client_id',
    ];

    /**
     * The attributes that are not mass assignable
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function client(){
        return $this->belongsTo(Client::class);
    }

    /**
     * La relation "plusieurs à plusieurs" avec le modèle Article.
     */
    public function articles()
    {
        return $this->belongsToMany(Article::class, 'article_dette')
            ->withPivot('qteVente', 'prixVente')
            ->withTimestamps();
    }
}
