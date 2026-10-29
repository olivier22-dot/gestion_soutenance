<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;

trait ReponsesCrud
{
    protected function supprimer(Model $modele)
    {
        try {
            $modele->delete();
        } catch (QueryException $e) {
            return response()->json(
                ['message' => 'Suppression impossible : cet élément est utilisé ailleurs.'],
                409
            );
        }

        return response()->noContent();
    }
}
