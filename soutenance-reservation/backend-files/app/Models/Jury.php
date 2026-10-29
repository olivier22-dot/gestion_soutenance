<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Jury extends Model
{
    protected $table = 'jurys';
    protected $primaryKey = 'num_jury';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['num_jury', 'nom', 'prenoms', 'grade', 'role'];

    public function etudiants(): BelongsToMany
    {
        return $this->belongsToMany(Etudiant::class, 'encadrements', 'num_jury', 'num_matricule');
    }

    public function reservations(): BelongsToMany
    {
        return $this->belongsToMany(Reservation::class, 'jury_reservation', 'num_jury', 'num_reservation');
    }
}