<?php

namespace App\Http\Controllers;

use App\Models\Salle;
use Illuminate\Http\Request;

class SalleController extends Controller
{
    public function index()
    {
        return response()->json(Salle::with('emplacement')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'num_salle' => 'required|string|unique:salles,num_salle',
            'capacite' => 'required|integer|min:1',
            'code_emplacement' => 'required|string|exists:emplacements,code_emplacement',
        ]);

        $salle = Salle::create($validated);
        return response()->json($salle, 201);
    }

    public function show($num_salle)
    {
        $salle = Salle::with('emplacement')->findOrFail($num_salle);
        return response()->json($salle);
    }

    public function update(Request $request, $num_salle)
    {
        $salle = Salle::findOrFail($num_salle);

        $validated = $request->validate([
            'capacite' => 'sometimes|required|integer|min:1',
            'code_emplacement' => 'sometimes|required|string|exists:emplacements,code_emplacement',
        ]);

        $salle->update($validated);
        return response()->json($salle);
    }

    public function destroy($num_salle)
    {
        $salle = Salle::findOrFail($num_salle);
        $salle->delete();
        return response()->json(['message' => 'Salle supprimée']);
    }
}