<?php

namespace App\Http\Controllers;

use App\Models\Etudiant;
use Illuminate\Http\Request;

class EtudiantController extends Controller
{
    public function index()
    {
        return response()->json(Etudiant::with('jurys')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'num_matricule' => 'required|string|unique:etudiants,num_matricule',
            'nom' => 'required|string|max:100',
            'prenoms' => 'nullable|string|max:150',
            'niveau' => 'nullable|string|max:50',
            'parcours' => 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'visa_soutenance' => 'nullable|boolean',
            'rapport_lecture' => 'nullable|boolean',
            'memoire' => 'nullable|string|max:255',
        ]);

        $etudiant = Etudiant::create($validated);
        return response()->json($etudiant, 201);
    }

    public function show($num_matricule)
    {
        $etudiant = Etudiant::with('jurys', 'reservations')->findOrFail($num_matricule);
        return response()->json($etudiant);
    }

    public function update(Request $request, $num_matricule)
    {
        $etudiant = Etudiant::findOrFail($num_matricule);

        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:100',
            'prenoms' => 'nullable|string|max:150',
            'niveau' => 'nullable|string|max:50',
            'parcours' => 'nullable|string|max:100',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:150',
            'visa_soutenance' => 'sometimes|boolean',
            'rapport_lecture' => 'sometimes|boolean',
            'memoire' => 'nullable|string|max:255',
        ]);

        $etudiant->update($validated);
        return response()->json($etudiant);
    }

    public function destroy($num_matricule)
    {
        $etudiant = Etudiant::findOrFail($num_matricule);
        $etudiant->delete();
        return response()->json(['message' => 'Étudiant supprimé avec succès']);
    }
}