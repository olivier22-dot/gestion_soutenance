# Gestion de réservation de créneaux de soutenance et file d'attente

Stack : **PostgreSQL** + **Laravel (11 ou plus récent)** + **React (Vite)**.

```
soutenance-reservation/
├── database/soutenance.sql   # Base PostgreSQL complète (structure + données de démo)
├── backend-files/            # Fichiers à copier dans un projet Laravel neuf
│   ├── app/Models, app/Services, app/Http/Controllers
│   ├── database/migrations, database/seeders
│   └── routes/api.php
├── frontend/                 # Application React (Vite)
├── install.sh                # Installation automatique du backend (Linux / macOS / Git Bash)
└── README.md
```

## 1. MLD (issu de votre MCD)

- **emplacements** (<u>code_emplacement</u>, adresse)
- **salles** (<u>num_salle</u>, capacite, #code_emplacement) — association SUTIER
- **etudiants** (<u>num_matricule</u>, nom, prenoms, niveau, parcours, telephone, email, visa_soutenance, rapport_lecture, memoire)
- **jurys** (<u>num_jury</u>, nom, prenoms, grade, role)
- **encadrements** (<u>#num_matricule, #num_jury</u>) — association ENCADREUR (0,n – 0,n)
- **creneaux** (<u>num_creneau</u>, date_creneau, heure_debut, heure_fin, #num_salle) — association SE_DEROULER_SUR
- **reservations** (<u>num_reservation</u>, date_heure_demande, statut, #num_matricule, #num_creneau) — associations FAIRE et CONCERNER
- **jury_reservation** (<u>#num_reservation, #num_jury</u>) — les 3 membres du jury d'une réservation

Adaptations par rapport au MCD dessiné (à reporter dans votre MCD si vous les validez) :
1. Une réservation a **3 jurys** : `NumJury` dans RÉSERVATION devient la table `jury_reservation`.
2. `NumSalle` dans CRÉNEAU = association SE_DEROULER_SUR ; le créneau garde `date_creneau` + `heure_debut`/`heure_fin` (durée variable). `DateHeureDemande` du créneau est retiré (il existe déjà dans RÉSERVATION).
3. `NumSalle` : la salle d'une réservation est celle de son créneau (CONCERNER est donc dérivée, pas de colonne en plus).

## 2. Règles de gestion implémentées

- Réservation faite par l'étudiant lui-même ; 1 étudiant + **3 jurys distincts** + 1 salle + 1 créneau.
- **File d'attente** (premier arrivé, premier servi) : créneau libre → `confirmee` ; créneau déjà pris → `en_attente` avec une position.
- Si une réservation confirmée est **annulée**, la 1re demande en attente dont le jury est libre est confirmée automatiquement.
- Un créneau n'a qu'**une** réservation confirmée ; un étudiant n'a qu'**une** réservation active (index uniques partiels PostgreSQL).
- Un membre du jury ne peut pas être confirmé sur deux créneaux qui se chevauchent ; deux créneaux d'une même salle ne se chevauchent pas.
- Un étudiant doit avoir le **visa de soutenance** et le **rapport de lecture** (hypothèse tirée de vos attributs : modifiez `ReservationService::demander()` si la règle est différente).
- Pas d'authentification dans cette version : l'étudiant s'identifie par son matricule (suffisant pour un prototype de stage ; Laravel Sanctum est le prochain pas logique).

## 3. Installation

Prérequis : PHP ≥ 8.2 (extension `pdo_pgsql` activée), Composer, Node.js ≥ 18, PostgreSQL.

### a) Base de données
```bash
psql -U postgres -c "CREATE DATABASE soutenance"
```

### b) Backend (Laravel)
Automatique :
```bash
DB_PASSWORD=votre_mot_de_passe ./install.sh
```
Ou à la main :
```bash
composer create-project laravel/laravel backend
cd backend
php artisan install:api --no-interaction
# copier le contenu de ../backend-files dans backend (app, database, routes)
```
Dans `backend/.env` :
```
APP_TIMEZONE=Indian/Antananarivo
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=soutenance
DB_USERNAME=postgres
DB_PASSWORD=votre_mot_de_passe
```
Puis :
```bash
php artisan migrate --seed     # crée les tables + données de démo
php artisan serve              # http://127.0.0.1:8000
```
> **Alternative sans migrations** : `psql -U postgres -d soutenance -f database/soutenance.sql` (structure + données identiques). Utilisez l'un **ou** l'autre, pas les deux.

### c) Frontend (React)
```bash
cd frontend
npm install
npm run dev                    # http://localhost:5173  (proxy /api -> Laravel)
```
Matricules de démo : `2024001` (confirmé), `2024002` (en file d'attente), `2024003` (libre), `2024004` (non éligible).

## 4. API REST (préfixe `/api`)

| Méthode | Route | Rôle |
|---|---|---|
| GET/POST/PUT/DELETE | `/emplacements`, `/salles`, `/jurys`, `/etudiants`, `/creneaux` | CRUD |
| PUT | `/etudiants/{matricule}/encadreurs` | `{ "jurys": [1,3] }` |
| GET | `/planning` | créneaux à venir + disponibilité + taille de la file |
| GET | `/reservations?matricule=` | réservations (avec position en file) |
| POST | `/reservations` | `{ "num_matricule", "num_creneau", "jurys": [id,id,id] }` |
| POST | `/reservations/{id}/annuler` | annule + promeut le suivant de la file |
