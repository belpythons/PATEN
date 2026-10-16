<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PosTransaction extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'pos_transactions';

    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'stand_id',
        'menu_id',
        'qty',
        'subtotal',
        'metode_pembayaran',
        'created_at',
    ];

    protected $casts = [
        'qty' => 'integer',
        'subtotal' => 'decimal:2',
        'created_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function stand(): BelongsTo
    {
        return $this->belongsTo(EventStand::class, 'stand_id');
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(StandMenu::class, 'menu_id');
    }
}
