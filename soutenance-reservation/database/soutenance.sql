-- Table EMPLACEMENT
CREATE TABLE emplacements (
    code_emplacement VARCHAR(50) PRIMARY KEY,
    add_emplacements VARCHAR(255)
);

-- Table SALLE
CREATE TABLE salles (
    num_salle VARCHAR(50) PRIMARY KEY,
    capacite INT NOT NULL
);

-- Table de liaison SALLE_EMPLACEMENT (Relation Sutier)
CREATE TABLE salle_emplacement (
    num_salle VARCHAR(50),
    code_emplacement VARCHAR(50),
    PRIMARY KEY (num_salle, code_emplacement),
    FOREIGN KEY (num_salle) REFERENCES salles(num_salle) ON DELETE CASCADE,
    FOREIGN KEY (code_emplacement) REFERENCES emplacements(code_emplacement) ON DELETE CASCADE
);

-- Table ETUDIANT
CREATE TABLE etudiants (
    num_matricule VARCHAR(50) PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenoms VARCHAR(150),
    niveau VARCHAR(50),
    parcours VARCHAR(100),
    telephone VARCHAR(20),
    email VARCHAR(150),
    visa_soutenance BOOLEAN DEFAULT FALSE,
    rapport_lecture VARCHAR(255),
    memoire VARCHAR(255)
);

-- Table JURY
CREATE TABLE jurys (
    num_jury VARCHAR(50) PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenoms VARCHAR(150),
    grade VARCHAR(100),
    role VARCHAR(100)
);

-- Table CRÉNEAU
CREATE TABLE creneaux (
    num_creneau VARCHAR(50) PRIMARY KEY,
    date_heure_demande DATETIME,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    num_salle VARCHAR(50),
    FOREIGN KEY (num_salle) REFERENCES salles(num_salle) ON DELETE SET NULL
);

-- Table RÉSERVATION
CREATE TABLE reservations (
    num_reservation VARCHAR(50) PRIMARY KEY,
    date_heure_demande DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(50) DEFAULT 'En attente',
    mat_etudiant VARCHAR(50),
    num_creneau VARCHAR(50),
    num_jury VARCHAR(50),
    FOREIGN KEY (mat_etudiant) REFERENCES etudiants(num_matricule) ON DELETE CASCADE,
    FOREIGN KEY (num_creneau) REFERENCES creneaux(num_creneau) ON DELETE SET NULL,
    FOREIGN KEY (num_jury) REFERENCES jurys(num_jury) ON DELETE SET NULL
);

-- Table de liaison RESERVATION_SALLE (Relation CONCERNER si N:N)
CREATE TABLE reservation_salle (
    num_reservation VARCHAR(50),
    num_salle VARCHAR(50),
    PRIMARY KEY (num_reservation, num_salle),
    FOREIGN KEY (num_reservation) REFERENCES reservations(num_reservation) ON DELETE CASCADE,
    FOREIGN KEY (num_salle) REFERENCES salles(num_salle) ON DELETE CASCADE
);

-- Table de liaison ETUDIANT_JURY (Relation ENCADREUR)
CREATE TABLE etudiant_jury (
    mat_etudiant VARCHAR(50),
    num_jury VARCHAR(50),
    PRIMARY KEY (mat_etudiant, num_jury),
    FOREIGN KEY (mat_etudiant) REFERENCES etudiants(num_matricule) ON DELETE CASCADE,
    FOREIGN KEY (num_jury) REFERENCES jurys(num_jury) ON DELETE CASCADE
);