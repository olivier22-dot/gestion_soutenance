import { useState } from 'react';
import CrudPage from '../components/CrudPage.jsx';
import ReservationsAdmin from './ReservationsAdmin.jsx';
import Encadrement from './Encadrement.jsx';
import { CRENEAUX, EMPLACEMENTS, ETUDIANTS, JURYS, SALLES } from './config.js';

const ONGLETS = [
  { id: 'reservations', label: 'Réservations' },
  { id: 'creneaux', label: 'Créneaux' },
  { id: 'etudiants', label: 'Étudiants' },
  { id: 'encadrement', label: 'Encadrement' },
  { id: 'jurys', label: 'Jury' },
  { id: 'salles', label: 'Salles' },
  { id: 'emplacements', label: 'Emplacements' },
];

export default function Admin() {
  const [onglet, setOnglet] = useState('reservations');

  return (
    <div>
      <div className="onglets">
        {ONGLETS.map((o) => (
          <button key={o.id} className={onglet === o.id ? 'actif' : ''} onClick={() => setOnglet(o.id)}>
            {o.label}
          </button>
        ))}
      </div>
      {onglet === 'reservations' && <ReservationsAdmin />}
      {onglet === 'creneaux' && <CrudPage {...CRENEAUX} />}
      {onglet === 'etudiants' && <CrudPage {...ETUDIANTS} />}
      {onglet === 'encadrement' && <Encadrement />}
      {onglet === 'jurys' && <CrudPage {...JURYS} />}
      {onglet === 'salles' && <CrudPage {...SALLES} />}
      {onglet === 'emplacements' && <CrudPage {...EMPLACEMENTS} />}
    </div>
  );
}
