<?php

namespace App\Http\Controllers;

use App\Models\Creneau;
use App\Models\Reservation;

/** Créneaux à venir avec leur disponibilité et la longueur de la file d'attente. */
class PlanningController extends Controller
{
    public function index()
    {
        return Creneau::with('salle.emplacement')
            ->withCount([
                'reservations as nb_confirmees' => fn ($q) => $q->where('statut', Reservation::CONFIRMEE),
                'reservations as nb_attente' => fn ($q) => $q->where('statut', Reservation::EN_ATTENTE),
            ])
            ->where('date_creneau', '>=', today()->toDateString())
            ->orderBy('date_creneau')
            ->orderBy('heure_debut')
            ->get();
    }
}
