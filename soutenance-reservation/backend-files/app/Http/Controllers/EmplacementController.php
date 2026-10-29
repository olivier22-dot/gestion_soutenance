<?php

namespace App\Http\Controllers;

use App\Models\Emplacement;
use Illuminate\Http\Request;

class EmplacementController extends Controller
{
    public function index()
    {
        return response()->json(Emplacement::with('salles')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code_emplacement' => 'required|string|unique:emplacements,code_emplacement',
            'adresse' => 'required|string|max:255',
        ]);

        $emplacement = Emplacement::create($validated);
        return response()->json($emplacement, 201);
    }

    public function show($code_emplacement)
    {
        $emplacement = Emplacement::with('salles')->findOrFail($code_emplacement);
        return response()->json($emplacement);
    }

    public function update(Request $request, $code_emplacement)
    {
        $emplacement = Emplacement::findOrFail($code_emplacement);

        $validated = $request->validate([
            'adresse' => 'sometimes|required|string|max:255',
        ]);

        $emplacement->update($validated);
        return response()->json($emplacement);
    }

    public function destroy($code_emplacement)
    {
        $emplacement = Emplacement::findOrFail($code_emplacement);
        $emplacement->delete();
        return response()->json(['message' => 'Emplacement supprimé']);
    }
}