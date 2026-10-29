<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jury;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $professeurs = [
            ['nom' => 'MAHATODY', 'prenoms' => 'Thomas', 'grade' => 'Permanent'],
            ['nom' => 'RAMAMONJISOA', 'prenoms' => 'Andriantiana Bertin Olivier', 'grade' => 'Professeur Titulaire'],
            ['nom' => 'RAFAMANTANANTSOA', 'prenoms' => 'Fontaine', 'grade' => 'Professeur'],
            ['nom' => 'RATIARSON', 'prenoms' => 'Venot', 'grade' => 'Maître de Conférences'],
            ['nom' => 'RABETAFIKA', 'prenoms' => 'Louis Haja', 'grade' => 'Maître de Conférences'],
            ['nom' => 'RALAIVAO', 'prenoms' => 'Jean Christian', 'grade' => 'Maître de Conférences'],
            ['nom' => 'GILANTE', 'prenoms' => 'Gesazafy', 'grade' => "Assistant d'Enseignement Supérieur et de Recherche"],
            ['nom' => 'RAZAFINDRAMONJA', 'prenoms' => 'Clément Aubert', 'grade' => "Assistant d'Enseignement Supérieur et de Recherche"],
            ['nom' => 'RATIANANTITRA', 'prenoms' => 'Volatiana Marielle', 'grade' => 'Maître de Conférences'],
            ['nom' => 'RAKOTOASIMBAHOAKA', 'prenoms' => 'Antsa Cyprienna', 'grade' => 'Maître de Conférences'],
            ['nom' => 'DIMBISOA', 'prenoms' => 'William', 'grade' => 'Maître de Conférences'],
            ['nom' => 'RAZAFIMAHATRATRA', 'prenoms' => 'Hajarisena', 'grade' => 'Maître de Conférences'],
            ['nom' => 'RANDRIANOMENJANAHARY', 'prenoms' => 'Lala Ferdinand', 'grade' => 'Maître de Conférences'],
            ['nom' => 'RANDRIAMIARISON', 'prenoms' => 'Zilga Heritiana', 'grade' => 'Maître de Conférences'],
            ['nom' => 'RAHERINIRINA', 'prenoms' => 'Angelo', 'grade' => 'Professeur'],
            ['nom' => 'RASOLOARIJAONA', 'prenoms' => 'Madison', 'grade' => 'Maître de Conférences'],
            ['nom' => 'RANDRIANANDRAINA', 'prenoms' => 'Germain Lalao Albert', 'grade' => 'Maître de Conférences'],
            ['nom' => 'TAREHY', 'prenoms' => 'Brice Evrard', 'grade' => 'Maître de Conférences'],
            ['nom' => 'RATOVONDRAHONA', 'prenoms' => 'Alain Josué', 'grade' => 'Docteur'],
            ['nom' => 'BERTIN', 'prenoms' => 'Andry', 'grade' => 'Doctorant'],
            ['nom' => 'RALAMBOARISOA', 'prenoms' => 'Bénédicte', 'grade' => 'Assistant'],
            ['nom' => 'RABEMANANTSOA', 'prenoms' => 'Auguste Patrice', 'grade' => 'Assistant'],
            ['nom' => 'RAZAFIMAMPIANDRA', 'prenoms' => 'Victor', 'grade' => 'Assistant'],
            ['nom' => 'RAFANOMEZANTSOA', 'prenoms' => 'Jean Roger', 'grade' => 'Assistant'],
            ['nom' => 'RAKOTONIRINA', 'prenoms' => 'Janvier Benjamin', 'grade' => 'Assistant'],
            ['nom' => 'RAZAFINDRANDRIATSIMANIRY', 'prenoms' => 'Marie Dieudonné Michel', 'grade' => 'Maître de Conférences'],
            ['nom' => 'RAKOTOASIMBAHOAKA', 'prenoms' => 'Cyprien Robert', 'grade' => 'Maître de Conférences'],
            ['nom' => 'FANOMEZANA', 'prenoms' => 'Mihajasoa Léa', 'grade' => 'Docteur'],
            ['nom' => 'RAMAROSANDRATANA', 'prenoms' => 'Vaninah', 'grade' => 'Assistant'],
            ['nom' => 'FANJIRINDRATOVO', 'prenoms' => 'Tovondahiniriko', 'grade' => 'Maître de Conférences'],
            ['nom' => 'RANDEVOLAHY', 'prenoms' => 'François', 'grade' => 'Assistant'],
            ['nom' => 'RANOELINDRINA', 'prenoms' => 'Florent Jean Yves', 'grade' => 'Assistant'],
            ['nom' => 'RABOTOSON', 'prenoms' => 'Angelo', 'grade' => 'Assistant'],
            ['nom' => 'ANDRIANTSILAVO', 'prenoms' => 'Raoelina Diamondra', 'grade' => 'Assistant'],
        ];

        foreach ($professeurs as $index => $prof) {
            Jury::firstOrCreate(
                ['num_jury' => 'JURY-' . sprintf('%02d', $index + 1)],
                [
                    'nom' => $prof['nom'],
                    'prenoms' => $prof['prenoms'],
                    'grade' => $prof['grade'],
                    'role' => null // Le rôle sera attribué lors de la réservation
                ]
            );
        }
    }
}