<?php

namespace App\Models;

use App\Filters\DetteFilter;
use App\Observers\DetteObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\QueryBuilder;

#[ObservedBy([DetteObserver::class])]
class Dette extends Model
{
    use HasFactory;

    protected $fillable = [
        'montant',
        'client_id',
    ];
    // Définition des attributs transients
    protected $appends = ['montant_verse', 'montant_du'];

    // Transient attributes
    protected $transients = [
        'articles_transients' => null,
        'paiement_transients' => null
    ];

    /**
     * Calculer le montant versé en fonction des paiements
     */
    public function getMontantVerseAttribute()
    {
        $montantVerse = $this->paiements()->sum('montant');
        return (float)number_format((float)$montantVerse, 2, '.', '');
    }

    /**
     * Calculer le montant dû (montant total de la dette - montant versé)
     */
    public function getMontantDuAttribute()
    {
        $montantDu = $this->montant - $this->montant_verse;
        return (float)number_format((float)$montantDu, 2, '.', '');
    }

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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'montant' => 'float'
        ];
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

    /**
     * Scope a query to apply custom filters.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilter(Builder $query, $request)
    {
        $query = QueryBuilder::for($query);
        $dettes = $query->get();
        if ($request->has('status')) {
            $status = $request->query('status');
            if ($status === 'oui') {
                $dettes = $dettes->filter(fn($item) => $item->montant_du == 0);
            }
            if ($status === 'non') {
                $dettes = $dettes->filter(fn($item) => $item->montant_du > 0);
            }
        }
        return $dettes;
    }

    /**
     * Scope a query to apply custom filters.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterWith(Builder $query, $id=null,$with=null)
    {
        if ($with) {
            $query->with($with);
        }
        if ($id){
            $query->where('id', $id);
        }
        $query = QueryBuilder::for($query);
        $dettes = $query->get();
        return $dettes;
    }

}
