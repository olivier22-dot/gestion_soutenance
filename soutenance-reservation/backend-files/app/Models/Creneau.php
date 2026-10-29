<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Creneau extends Model
{
    protected $table = 'creneaux';
    protected $primaryKey = 'num_creneau';
    protected $fillable = ['date_creneau', 'heure_debut', 'heure_fin', 'num_salle'];
    protected $casts = ['date_creneau' => 'date:Y-m-d'];

    public function salle(): BelongsTo
    {
        return $this->belongsTo(Salle::class, 'num_salle', 'num_salle');
    }

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'num_creneau', 'num_creneau');
    }
}