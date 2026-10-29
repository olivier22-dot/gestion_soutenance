import { createElement } from 'react';
import { heure } from '../api.js';

const oui = (v) => (v ? 'Oui' : 'Non');

// Bouton Oui/Non cliquable : un clic inverse la valeur dans la base (voir "basculer" dans CrudPage.jsx)
const bouton = (champ) => (l, { basculer }) =>
  createElement(
    'button',
    {
      className: `badge ${l[champ] ? 'confirmee' : 'annulee'}`,
      title: 'Cliquer pour changer',
      onClick: () => basculer(l, champ),
    },
    oui(l[champ])
  );

// IMPORTANT : ces objets sont définis une seule fois (références stables pour React).
export const EMPLACEMENTS = {
  titre: 'Emplacements',
  endpoint: '/emplacements',
  pk: 'code_emplacement',
  colonnes: [
    { label: 'Code', key: 'code_emplacement' },
    { label: 'Adresse', key: 'adresse' },
  ],
  champs: [
    { name: 'code_emplacement', label: 'Code' },
    { name: 'adresse', label: 'Adresse' },
  ],
};

export const SALLES = {
  titre: 'Salles',
  endpoint: '/salles',
  pk: 'num_salle',
  colonnes: [
    { label: 'N° salle', key: 'num_salle' },
    { label: 'Capacité', key: 'capacite' },
    { label: 'Emplacement', rendu: (l) => l.emplacement?.code_emplacement },
  ],
  champs: [
    { name: 'num_salle', label: 'N° salle' },
    { name: 'capacite', label: 'Capacité', type: 'number' },
    {
      name: 'code_emplacement',
      label: 'Emplacement',
      type: 'select',
      source: '/emplacements',
      optionValue: 'code_emplacement',
      optionLabel: (o) => `${o.code_emplacement} — ${o.adresse}`,
    },
  ],
};

export const JURYS = {
  titre: 'Membres du jury',
  endpoint: '/jurys',
  pk: 'num_jury',
  pkAuto: true,
  colonnes: [
    { label: 'N°', key: 'num_jury' },
    { label: 'Nom', key: 'nom' },
    { label: 'Prénoms', key: 'prenoms' },
    { label: 'Grade', key: 'grade' },
    { label: 'Rôle', key: 'role' },
  ],
  champs: [
    { name: 'nom', label: 'Nom' },
    { name: 'prenoms', label: 'Prénoms' },
    { name: 'grade', label: 'Grade' },
    { name: 'role', label: 'Rôle' },
  ],
};

export const ETUDIANTS = {
  titre: 'Étudiants',
  endpoint: '/etudiants',
  pk: 'num_matricule',
  colonnes: [
    { label: 'Matricule', key: 'num_matricule' },
    { label: 'Nom', key: 'nom' },
    { label: 'Prénoms', key: 'prenoms' },
    { label: 'Niveau', key: 'niveau' },
    { label: 'Parcours', key: 'parcours' },
    { label: 'Visa', rendu: bouton('visa_soutenance') },
    { label: 'Rapport', rendu: bouton('rapport_lecture') },
    { label: 'Mémoire', rendu: bouton('memoire') },
    { label: 'Grille', rendu: bouton('grille_evaluation') },
    { label: 'Compte fin.', rendu: bouton('compte_financier') },
    { label: 'Encadreurs', rendu: (l) => (l.encadreurs || []).map((j) => `${j.nom} ${j.prenoms}`).join(', ') },
  ],
  champs: [
    { name: 'num_matricule', label: 'Matricule' },
    { name: 'nom', label: 'Nom' },
    { name: 'prenoms', label: 'Prénoms' },
    { name: 'niveau', label: 'Niveau' },
    { name: 'parcours', label: 'Parcours' },
    { name: 'telephone', label: 'Téléphone', required: false },
    { name: 'email', label: 'E-mail', type: 'email' },
    { name: 'visa_soutenance', label: 'Visa de soutenance', type: 'checkbox' },
    { name: 'rapport_lecture', label: 'Rapport de lecture', type: 'checkbox' },
    { name: 'memoire', label: 'Mémoire déposé', type: 'checkbox' },
    { name: 'grille_evaluation', label: "Grille d'évaluation", type: 'checkbox' },
    { name: 'compte_financier', label: 'Compte financier en règle', type: 'checkbox' },
  ],
};

export const CRENEAUX = {
  titre: 'Créneaux',
  endpoint: '/creneaux',
  pk: 'num_creneau',
  pkAuto: true,
  colonnes: [
    { label: 'N°', key: 'num_creneau' },
    { label: 'Date', key: 'date_creneau' },
    { label: 'Début', rendu: (l) => heure(l.heure_debut) },
    { label: 'Fin', rendu: (l) => heure(l.heure_fin) },
    { label: 'Salle', key: 'num_salle' },
  ],
  champs: [
    { name: 'date_creneau', label: 'Date', type: 'date' },
    { name: 'heure_debut', label: 'Heure de début', type: 'time' },
    { name: 'heure_fin', label: 'Heure de fin', type: 'time' },
    {
      name: 'num_salle',
      label: 'Salle',
      type: 'select',
      source: '/salles',
      optionValue: 'num_salle',
      optionLabel: (o) => `${o.num_salle} (${o.capacite} places)`,
    },
  ],
};