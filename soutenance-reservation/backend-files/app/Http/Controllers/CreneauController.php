<?php

namespace App\Http\Controllers;

use App\Models\Creneau;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CreneauController extends Controller
{
    public function index()
    {
        return response()->json(Creneau::with('salle')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date_creneau' => ['required', 'date_format:Y-m-d'],
            'heure_debut' => ['required', 'date_format:H:i'],
            'heure_fin' => ['required', 'date_format:H:i', 'after:heure_debut'],
            'num_salle' => ['required', 'string', 'exists:salles,num_salle'],
        ]);

        $this->verifier($validated);

        $creneau = Creneau::create($validated);
        return response()->json($creneau, 201);
    }

    public function show($num_creneau)
    {
        $creneau = Creneau::with('salle')->findOrFail($num_creneau);
        return response()->json($creneau);
    }

    public function update(Request $request, $num_creneau)
    {
        $creneau = Creneau::findOrFail($num_creneau);

        $validated = $request->validate([
            'date_creneau' => ['sometimes', 'required', 'date_format:Y-m-d'],
            'heure_debut' => ['sometimes', 'required', 'date_format:H:i'],
            'heure_fin' => ['sometimes', 'required', 'date_format:H:i', 'after:heure_debut'],
            'num_salle' => ['sometimes', 'required', 'string', 'exists:salles,num_salle'],
        ]);

        $this->verifier(array_merge($creneau->toArray(), $validated), $creneau);

        $creneau->update($validated);
        return response()->json($creneau);
    }

    public function destroy($num_creneau)
    {
        $creneau = Creneau::findOrFail($num_creneau);
        $creneau->delete();
        return response()->json(['message' => 'Créneau supprimé']);
    }

    private function verifier(array $data, ?Model $existant = null): void
    {
        $chevauche = Creneau::where('num_salle', $data['num_salle'])
            ->where('date_creneau', $data['date_creneau'])
            ->where('heure_debut', '<', $data['heure_fin'])
            ->where('heure_fin', '>', $data['heure_debut'])
            ->when($existant, fn ($q) => $q->where('num_creneau', '!=', $existant->num_creneau))
            ->exists();

        if ($chevauche) {
            throw ValidationException::withMessages([
                'heure_debut' => 'Ce créneau chevauche un autre créneau de la même salle.',
            ]);
        }
    }
}