<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'events';

    protected $fillable = [
        'slug',
        'nama_event',
        'deskripsi',
        'penyelenggara',
        'lokasi_venue',
        'tanggal_mulai',
        'tanggal_selesai',
        'secure_management_token',
        'template_theme',
        'color_mode',
        'color_primary',
        'color_secondary',
        'poster_asset_url',
        'gamifikasi_mode',
        'harga_htm',
        'is_awarding_active',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'harga_htm' => 'decimal:2',
        'is_awarding_active' => 'boolean',
    ];

    protected $hidden = [
        'secure_management_token',
    ];

    public function rundowns(): HasMany
    {
        return $this->hasMany(EventRundown::class, 'event_id')->orderBy('urutan');
    }

    public function stands(): HasMany
    {
        return $this->hasMany(EventStand::class, 'event_id');
    }

    public function attendees(): HasMany
    {
        return $this->hasMany(Attendee::class, 'event_id');
    }

    public function posTransactions(): HasMany
    {
        return $this->hasMany(PosTransaction::class, 'event_id');
    }

    public function spkEvaluations(): HasMany
    {
        return $this->hasMany(SpkUmkmEvaluation::class, 'event_id');
    }
}
