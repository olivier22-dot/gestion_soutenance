<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Reservation extends Model
{
    protected $table = 'reservations';
    protected $primaryKey = 'num_reservation';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'num_reservation', 'date_heure_demande', 'statut',
        'num_matricule', 'num_creneau'
    ];

    protected $casts = [
        'date_heure_demande' => 'datetime',
    ];

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(Etudiant::class, 'num_matricule', 'num_matricule');
    }

    public function creneau(): BelongsTo
    {
        return $this->belongsTo(Creneau::class, 'num_creneau', 'num_creneau');
    }

    public function jurys(): BelongsToMany
    {
        return $this->belongsToMany(Jury::class, 'jury_reservation', 'num_reservation', 'num_jury');
    }
}