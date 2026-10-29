import { useCallback, useEffect, useState } from 'react';
import { api, heure, messageErreur } from '../api.js';
import Message from '../components/Message.jsx';

export const LIBELLES = { confirmee: 'Confirmée', en_attente: "En file d'attente", annulee: 'Annulée' };

export default function ReservationsAdmin() {
  const [lignes, setLignes] = useState([]);
  const [message, setMessage] = useState(null);

  const charger = useCallback(async () => setLignes(await api.get('/reservations')), []);

  useEffect(() => {
    charger().catch((e) => setMessage({ type: 'err', text: messageErreur(e) }));
  }, [charger]);

  const annuler = async (reservation) => {
    if (!window.confirm('Annuler cette réservation ?')) return;
    try {
      const resultat = await api.post(`/reservations/${reservation.num_reservation}/annuler`);
      setMessage({
        type: 'ok',
        text: resultat.promue
          ? `Réservation annulée. ${resultat.promue.etudiant.nom} ${resultat.promue.etudiant.prenoms} passe de la file d'attente à « confirmée ».`
          : 'Réservation annulée.',
      });
      await charger();
    } catch (e) {
      setMessage({ type: 'err', text: messageErreur(e) });
    }
  };

  return (
    <section className="carte">
      <h2>Réservations</h2>
      <Message message={message} onClose={() => setMessage(null)} />
      <div className="tableau">
        <table>
          <thead>
            <tr>
              <th>N°</th>
              <th>Demandée le</th>
              <th>Étudiant</th>
              <th>Créneau</th>
              <th>Salle</th>
              <th>Jury</th>
              <th>Statut</th>
              <th />
            </tr>
          </thead>
          <tbody>
            {lignes.length === 0 && (
              <tr>
                <td colSpan="8" className="vide">
                  Aucune réservation.
                </td>
              </tr>
            )}
            {lignes.map((reservation) => (
              <tr key={reservation.num_reservation}>
                <td>{reservation.num_reservation}</td>
                <td>{new Date(reservation.date_heure_demande).toLocaleString('fr-FR')}</td>
                <td>
                  {reservation.etudiant.nom} {reservation.etudiant.prenoms}
                  <br />
                  <small>{reservation.num_matricule}</small>
                </td>
                <td>
                  {reservation.creneau.date_creneau} {heure(reservation.creneau.heure_debut)}–{heure(reservation.creneau.heure_fin)}
                </td>
                <td>
                  {reservation.creneau.num_salle}
                  <br />
                  <small>{reservation.creneau.salle?.emplacement?.code_emplacement}</small>
                </td>
                <td>{reservation.jurys.map((jury) => jury.nom).join(', ')}</td>
                <td>
                  <span className={`badge ${reservation.statut}`}>{LIBELLES[reservation.statut]}</span>
                  {reservation.position && <small> (n°{reservation.position})</small>}
                </td>
                <td className="actions">
                  {reservation.statut !== 'annulee' && (
                    <button className="danger" onClick={() => annuler(reservation)}>
                      Annuler
                    </button>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </section>
  );
}