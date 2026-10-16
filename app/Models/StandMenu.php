<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StandMenu extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'stand_menus';

    protected $fillable = [
        'stand_id',
        'nama_item',
        'harga',
        'is_available',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'is_available' => 'boolean',
    ];

    public function stand(): BelongsTo
    {
        return $this->belongsTo(EventStand::class, 'stand_id');
    }

    public function posTransactions(): HasMany
    {
        return $this->hasMany(PosTransaction::class, 'menu_id');
    }
}
