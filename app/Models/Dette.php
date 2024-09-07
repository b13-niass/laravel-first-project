<?php

namespace App\Models;

use App\Observers\DetteObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([DetteObserver::class])]
class Dette extends Model
{
    use HasFactory;

    protected $fillable = [
        'montant',
        'client_id',
    ];

    // Transient attributes
    protected $transients = [
        'articles_transients' => null,
        'paiement_transients' => null
    ];

    /**
     * Set the transient articles attribute
     */
    public function setArticlesTransientsAttribute(array $articles)
    {
        $this->transients['articles_transients'] = $articles;
    }

    /**
     * Set the transient articles attribute
     */
    public function setPaiementTransientsAttribute(array $paiement)
    {
        $this->transients['paiement_transients'] = $paiement;
    }

    /**
     * Get the transient articles attribute
     */
    public function getArticlesTransientsAttribute()
    {
        return $this->transients['articles_transients'];
    }
    /**
     * Get the transient paiement attribute
     */
    public function getPaiementTransientsAttribute()
    {
        return $this->transients['paiement_transients'];
    }

    /**
     * The attributes that are not mass assignable
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function client(){
        return $this->belongsTo(Client::class);
    }

    public function paiements(){
        return $this->hasMany(Paiement::class);
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
