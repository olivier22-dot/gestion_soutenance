<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index()
    {
        return response()->json(
            Reservation::with(['etudiant', 'creneau.salle', 'jurys'])->get()
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'num_matricule' => 'required|exists:etudiants,num_matricule',
            'num_creneau' => 'required|exists:creneaux,num_creneau',
            'statut' => 'nullable|string|max:20',
            'jurys' => 'required|array|size:3',
            'jurys.*' => 'integer|distinct|exists:jurys,num_jury',
        ]);

        $reservation = Reservation::create([
            'num_matricule' => $validated['num_matricule'],
            'num_creneau' => $validated['num_creneau'],
            'statut' => $validated['statut'] ?? 'en_attente',
            'date_heure_demande' => now(),
        ]);

        $reservation->jurys()->sync($validated['jurys']);

        return response()->json($reservation->load(['etudiant', 'creneau.salle', 'jurys']), 201);
    }

    public function show($num_reservation)
    {
        $reservation = Reservation::with(['etudiant', 'creneau.salle', 'jurys'])->findOrFail($num_reservation);
        return response()->json($reservation);
    }

    public function update(Request $request, $num_reservation)
    {
        $reservation = Reservation::findOrFail($num_reservation);

        $validated = $request->validate([
            'statut' => 'sometimes|string|max:20',
            'num_matricule' => 'sometimes|exists:etudiants,num_matricule',
            'num_creneau' => 'sometimes|exists:creneaux,num_creneau',
            'jurys' => 'sometimes|array|size:3',
            'jurys.*' => 'integer|distinct|exists:jurys,num_jury',
        ]);

        $reservation->update(collect($validated)->except('jurys')->all());

        if (isset($validated['jurys'])) {
            $reservation->jurys()->sync($validated['jurys']);
        }

        return response()->json($reservation->load(['etudiant', 'creneau.salle', 'jurys']));
    }

    public function destroy($num_reservation)
    {
        $reservation = Reservation::findOrFail($num_reservation);
        $reservation->delete();
        return response()->json(['message' => 'Réservation supprimée avec succès']);
    }
}