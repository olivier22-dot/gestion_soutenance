<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Etudiant extends Model
{
    protected $table = 'etudiants';
    protected $primaryKey = 'num_matricule';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'num_matricule', 'nom', 'prenoms', 'niveau',
        'parcours', 'telephone', 'email', 'visa_soutenance',
        'rapport_lecture', 'memoire'
    ];

    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'num_matricule', 'num_matricule');
    }

    public function jurys(): BelongsToMany
    {
        return $this->belongsToMany(Jury::class, 'encadrements', 'num_matricule', 'num_jury');
    }
}