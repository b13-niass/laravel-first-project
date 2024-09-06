<?php

namespace App\Models;

use App\Models\Scopes\ClientTelephoneScope;
use App\Observers\ClientObserver;
use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

//#[ScopedBy([ClientTelephoneScope::class])]
#[ObservedBy([ClientObserver::class])]
class Client extends Model
{
    use HasFactory, Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'surnom',
        'telephone',
        'adresse',
        'user_id',
        'qrcode'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        // 'created_at',
        // 'updated_at'
    ];

    /**
     * The attributes that are not mass assignable
     */
    protected $guarded = ['id', 'created_at', 'updated_at'];

    /**
     * Relation Many-to-One avec User
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dettes()
    {
        return $this->hasMany(Dette::class);
    }

    // Définir un attribut transient
    public function getPhoto()
    {
        return 'images/profile.jpg';
    }

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
//        static::addGlobalScope(new ClientTelephoneScope());
    }
}
