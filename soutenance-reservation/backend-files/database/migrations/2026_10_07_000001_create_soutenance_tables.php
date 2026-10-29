<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emplacements', function (Blueprint $t) {
            $t->string('code_emplacement', 20)->primary();
            $t->string('adresse');
            $t->timestamps();
        });

        Schema::create('salles', function (Blueprint $t) {
            $t->string('num_salle', 20)->primary();
            $t->unsignedSmallInteger('capacite');
            $t->string('code_emplacement', 20);
            $t->timestamps();

            $t->foreign('code_emplacement')->references('code_emplacement')->on('emplacements')
                ->restrictOnDelete()->cascadeOnUpdate();
        });

        Schema::create('etudiants', function (Blueprint $t) {
            $t->string('num_matricule', 20)->primary();
            $t->string('nom', 100);
            $t->string('prenoms', 150);
            $t->string('niveau', 20);
            $t->string('parcours', 100);
            $t->string('telephone', 30)->nullable();
            $t->string('email', 150)->unique();
            $t->boolean('visa_soutenance')->default(false);
            $t->boolean('rapport_lecture')->default(false);
            $t->string('memoire')->nullable();
            $t->timestamps();
        });

        Schema::create('jurys', function (Blueprint $t) {
            $t->id('num_jury');
            $t->string('nom', 100);
            $t->string('prenoms', 150);
            $t->string('grade', 100);
            $t->string('role', 100);
            $t->timestamps();
        });

        // Association ENCADREUR (ÉTUDIANT 0,n — 0,n JURY)
        Schema::create('encadrements', function (Blueprint $t) {
            $t->string('num_matricule', 20);
            $t->unsignedBigInteger('num_jury');
            $t->primary(['num_matricule', 'num_jury']);

            $t->foreign('num_matricule')->references('num_matricule')->on('etudiants')
                ->cascadeOnDelete()->cascadeOnUpdate();
            $t->foreign('num_jury')->references('num_jury')->on('jurys')->cascadeOnDelete();
        });

        // Créneaux : durée variable (heure_debut / heure_fin) + association SE_DEROULER_SUR (salle)
        Schema::create('creneaux', function (Blueprint $t) {
            $t->id('num_creneau');
            $t->date('date_creneau');
            $t->time('heure_debut');
            $t->time('heure_fin');
            $t->string('num_salle', 20);
            $t->timestamps();

            $t->foreign('num_salle')->references('num_salle')->on('salles')
                ->restrictOnDelete()->cascadeOnUpdate();
            $t->index(['num_salle', 'date_creneau']);
        });

        Schema::create('reservations', function (Blueprint $t) {
            $t->id('num_reservation');
            $t->timestamp('date_heure_demande')->useCurrent();
            $t->string('statut', 20)->default('en_attente');
            $t->string('num_matricule', 20);
            $t->unsignedBigInteger('num_creneau');
            $t->timestamps();

            $t->foreign('num_matricule')->references('num_matricule')->on('etudiants')
                ->restrictOnDelete()->cascadeOnUpdate();
            $t->foreign('num_creneau')->references('num_creneau')->on('creneaux')->restrictOnDelete();
            $t->index(['num_creneau', 'statut', 'date_heure_demande']);
        });

        // Les 3 membres du jury d'une réservation
        Schema::create('jury_reservation', function (Blueprint $t) {
            $t->unsignedBigInteger('num_reservation');
            $t->unsignedBigInteger('num_jury');
            $t->primary(['num_reservation', 'num_jury']);

            $t->foreign('num_reservation')->references('num_reservation')->on('reservations')->cascadeOnDelete();
            $t->foreign('num_jury')->references('num_jury')->on('jurys')->restrictOnDelete();
        });

        // Règles de gestion garanties directement par PostgreSQL
        DB::statement("ALTER TABLE creneaux ADD CONSTRAINT creneaux_heures_chk CHECK (heure_fin > heure_debut)");
        DB::statement("ALTER TABLE reservations ADD CONSTRAINT reservations_statut_chk CHECK (statut IN ('en_attente','confirmee','annulee'))");
        // Un créneau ne peut avoir qu'UNE réservation confirmée
        DB::statement("CREATE UNIQUE INDEX reservations_une_confirmee_par_creneau ON reservations (num_creneau) WHERE statut = 'confirmee'");
        // Un étudiant ne peut avoir qu'UNE réservation active (confirmée ou en attente)
        DB::statement("CREATE UNIQUE INDEX reservations_une_active_par_etudiant ON reservations (num_matricule) WHERE statut IN ('en_attente','confirmee')");

        // Règle "exactement 3 jurys par réservation" : contrôlée à la validation de la transaction
        DB::unprepared(<<<'SQL'
CREATE OR REPLACE FUNCTION verifier_trois_jurys() RETURNS trigger AS $$
DECLARE
    nb INTEGER;
    ref BIGINT;
BEGIN
    ref := COALESCE(NEW.num_reservation, OLD.num_reservation);
    IF EXISTS (SELECT 1 FROM reservations WHERE num_reservation = ref) THEN
        SELECT COUNT(*) INTO nb FROM jury_reservation WHERE num_reservation = ref;
        IF nb <> 3 THEN
            RAISE EXCEPTION 'La réservation % doit avoir exactement 3 membres de jury (actuellement %)', ref, nb;
        END IF;
    END IF;
    RETURN NULL;
END;
$$ LANGUAGE plpgsql
SQL);
        DB::unprepared("CREATE CONSTRAINT TRIGGER trg_trois_jurys AFTER INSERT OR DELETE ON jury_reservation DEFERRABLE INITIALLY DEFERRED FOR EACH ROW EXECUTE FUNCTION verifier_trois_jurys()");
    }

    public function down(): void
    {
        Schema::dropIfExists('jury_reservation');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('creneaux');
        Schema::dropIfExists('encadrements');
        Schema::dropIfExists('jurys');
        Schema::dropIfExists('etudiants');
        Schema::dropIfExists('salles');
        Schema::dropIfExists('emplacements');
        DB::unprepared('DROP FUNCTION IF EXISTS verifier_trois_jurys() CASCADE');
    }
};
