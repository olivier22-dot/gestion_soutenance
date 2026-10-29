<?php

namespace App\Http\Controllers;

use App\Models\Jury;
use Illuminate\Http\Request;

class JuryController extends Controller
{
    public function index()
    {
        return response()->json(Jury::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'num_jury' => 'required|string|unique:jurys,num_jury',
            'nom' => 'required|string|max:100',
            'prenoms' => 'nullable|string|max:150',
            'grade' => 'nullable|string|max:100',
            'role' => 'nullable|string|max:100',
        ]);

        $jury = Jury::create($validated);
        return response()->json($jury, 201);
    }

    public function show($num_jury)
    {
        $jury = Jury::findOrFail($num_jury);
        return response()->json($jury);
    }

    public function update(Request $request, $num_jury)
    {
        $jury = Jury::findOrFail($num_jury);

        $validated = $request->validate([
            'nom' => 'sometimes|required|string|max:100',
            'prenoms' => 'nullable|string|max:150',
            'grade' => 'nullable|string|max:100',
            'role' => 'nullable|string|max:100',
        ]);

        $jury->update($validated);
        return response()->json($jury);
    }

    public function destroy($num_jury)
    {
        $jury = Jury::findOrFail($num_jury);
        $jury->delete();
        return response()->json(['message' => 'Jury supprimé']);
    }
}