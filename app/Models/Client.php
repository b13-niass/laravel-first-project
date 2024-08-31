<?php

namespace App\Models;

use EloquentFilter\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'user_id'
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
}
