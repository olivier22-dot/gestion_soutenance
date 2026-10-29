<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Emplacement extends Model
{
    protected $table = 'emplacements';
    protected $primaryKey = 'code_emplacement';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['code_emplacement', 'adresse'];

    public function salles(): HasMany
    {
        return $this->hasMany(Salle::class, 'code_emplacement', 'code_emplacement');
    }
}