<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Salle extends Model
{
    protected $table = 'salles';
    protected $primaryKey = 'num_salle';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['num_salle', 'capacite', 'code_emplacement'];

    public function emplacement(): BelongsTo
    {
        return $this->belongsTo(Emplacement::class, 'code_emplacement', 'code_emplacement');
    }

    public function creneaux(): HasMany
    {
        return $this->hasMany(Creneau::class, 'num_salle', 'num_salle');
    }
}