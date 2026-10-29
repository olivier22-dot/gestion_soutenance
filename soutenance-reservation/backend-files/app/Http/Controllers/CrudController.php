<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ReponsesCrud;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

/**
 * CRUD générique : chaque contrôleur enfant indique son modèle et ses règles de validation.
 */
abstract class CrudController extends Controller
{
    use ReponsesCrud;

    /** @return class-string<Model> */
    abstract protected function modele(): string;

    abstract protected function regles(?Model $existant = null): array;

    protected function relations(): array
    {
        return [];
    }

    /** Contrôles métier supplémentaires (lance une ValidationException si besoin). */
    protected function verifier(array $data, ?Model $existant = null): void
    {
    }

    protected function ordre(): array
    {
        $classe = $this->modele();
        return [(new $classe())->getKeyName()];
    }

    public function index()
    {
        $classe = $this->modele();
        $requete = $classe::with($this->relations());
        foreach ($this->ordre() as $colonne) {
            $requete->orderBy($colonne);
        }
        return $requete->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate($this->regles());
        $this->verifier($data);
        $classe = $this->modele();
        $modele = $classe::create($data);

        return response()->json($modele->load($this->relations()), 201);
    }

    public function show(string $id)
    {
        $classe = $this->modele();
        return $classe::with($this->relations())->findOrFail($id);
    }

    public function update(Request $request, string $id)
    {
        $classe = $this->modele();
        $modele = $classe::findOrFail($id);
        $data = $request->validate($this->regles($modele));
        $this->verifier($data, $modele);
        $modele->update($data);

        return $modele->load($this->relations());
    }

    public function destroy(string $id)
    {
        $classe = $this->modele();
        return $this->supprimer($classe::findOrFail($id));
    }
}
