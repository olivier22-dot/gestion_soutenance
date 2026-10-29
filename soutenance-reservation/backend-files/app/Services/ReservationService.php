<?php

namespace App\Services;

use App\Models\Creneau;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class ReservationService
{
    public function verifierDisponibiliteCreneau(string $numCreneau): bool
    {
        $existe = Reservation::where('num_creneau', $numCreneau)
            ->where('statut', 'confirmee')
            ->exists();

        return !$existe;
    }

    public function creerReservation(array $donnees): Reservation
    {
        if (isset($donnees['num_creneau']) && !$this->verifierDisponibiliteCreneau((string) $donnees['num_creneau'])) {
            throw new \RuntimeException('Ce créneau est déjà réservé.');
        }

        return DB::transaction(function () use ($donnees) {
            $reservation = Reservation::create([
                'num_matricule' => $donnees['num_matricule'],
                'num_creneau' => $donnees['num_creneau'] ?? null,
                'statut' => 'en_attente',
                'date_heure_demande' => now(),
            ]);

            if (!empty($donnees['jurys'])) {
                $reservation->jurys()->sync($donnees['jurys']);
            }

            return $reservation->fresh(['etudiant', 'creneau.salle', 'jurys']);
        });
    }
}