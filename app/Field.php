<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Field extends Model
{
    protected $fillable = [
        'width',
        'height',
    ];
    protected $with = ['cells'];

    /**
     * @return BelongsTo
     */
    public function game(): BelongsTo
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * @return HasMany
     */
    public function cells(): HasMany
    {
        return $this->hasMany(Cell::class);
    }

    public $timestamps = false;
}
