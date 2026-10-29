import { useCallback, useEffect, useState } from 'react';
import { api, heure, messageErreur } from '../api.js';
import Message from '../components/Message.jsx';
import { LIBELLES } from './ReservationsAdmin.jsx';

// Indicateurs Oui / Non lus dans la table "etudiants" de la base de données
const INDICATEURS = [
  ['visa_soutenance', 'Visa de soutenance'],
  ['rapport_lecture', 'Rapport de lecture'],
  ['memoire', 'Mémoire'],
  ['grille_evaluation', "Grille d'évaluation"],
  ['compte_financier', 'Compte financier'],
];

export default function Etudiant() {
  const [matricule, setMatricule] = useState('');
  const [fiche, setFiche] = useState(null); // étudiant lu dans la base (nom, prénoms, téléphone, email, Oui/Non…)
  const [recherche, setRecherche] = useState(''); // '' | 'chargement' | 'introuvable'
  const [planning, setPlanning] = useState([]);
  const [jurys, setJurys] = useState([]);
  const [creneau, setCreneau] = useState(null);
  const [choisis, setChoisis] = useState([]);
  const [mes, setMes] = useState([]);
  const [message, setMessage] = useState(null);

  const chargerDonnees = useCallback(async () => {
    setPlanning(await api.get('/planning'));
    setJurys(await api.get('/jurys'));
  }, []);

  const chargerMes = useCallback(async (m) => {
    if (!m.trim()) {
      setMes([]);
      return;
    }
    setMes(await api.get(`/reservations?matricule=${encodeURIComponent(m.trim())}`));
  }, []);

  useEffect(() => {
    chargerDonnees().catch((e) => setMessage({ type: 'err', text: messageErreur(e) }));
  }, [chargerDonnees]);

  // Dès que le matricule est saisi : la fiche de l'étudiant est lue dans la base (GET /api/etudiants/{matricule})
  useEffect(() => {
    const m = matricule.trim();
    setFiche(null);
    setMes([]);
    if (!m) {
      setRecherche('');
      return undefined;
    }
    let annule = false; // ignore les réponses d'une ancienne saisie
    setRecherche('chargement');
    const minuteur = setTimeout(async () => {
      try {
        const etudiant = await api.get(`/etudiants/${encodeURIComponent(m)}`);
        if (annule) return;
        setFiche(etudiant);
        setRecherche('');
        await chargerMes(m);
      } catch (err) {
        if (annule) return;
        if (err.status === 404) {
          setRecherche('introuvable');
        } else {
          setRecherche('');
          setMessage({ type: 'err', text: messageErreur(err) });
        }
      }
    }, 400);
    return () => {
      annule = true;
      clearTimeout(minuteur);
    };
  }, [matricule, chargerMes]);

  const basculerJury = (id) =>
    setChoisis((c) => {
      if (c.includes(id)) return c.filter((x) => x !== id);
      return c.length >= 3 ? c : [...c, id]; // 3 membres maximum
    });

  const rechercher = async (e) => {
    e.preventDefault();
    try {
      await chargerMes(matricule);
      setMessage(null);
    } catch (err) {
      setMessage({ type: 'err', text: messageErreur(err) });
    }
  };

  const reserver = async () => {
    try {
      const r = await api.post('/reservations', {
        num_matricule: fiche.num_matricule,
        num_creneau: creneau.num_creneau,
        jurys: choisis,
      });
      setMessage({
        type: 'ok',
        text:
          r.statut === 'confirmee'
            ? 'Réservation confirmée !'
            : `Ce créneau est déjà pris : vous êtes en file d'attente (position ${r.position}). Vous serez confirmé automatiquement si le créneau se libère.`,
      });
      setCreneau(null);
      setChoisis([]);
      await chargerDonnees();
      await chargerMes(matricule);
    } catch (err) {
      setMessage({ type: 'err', text: messageErreur(err) });
    }
  };

  const annuler = async (r) => {
    if (!window.confirm('Annuler cette réservation ?')) return;
    try {
      await api.post(`/reservations/${r.num_reservation}/annuler`);
      setMessage({ type: 'ok', text: 'Réservation annulée.' });
      await chargerDonnees();
      await chargerMes(matricule);
    } catch (err) {
      setMessage({ type: 'err', text: messageErreur(err) });
    }
  };

  const etat = (c) => (c.nb_confirmees > 0 ? (c.nb_attente > 0 ? `Pris — ${c.nb_attente} en attente` : 'Pris') : 'Libre');

  return (
    <div>
      <Message message={message} onClose={() => setMessage(null)} />

      <section className="carte">
        <h2>1. Mon matricule</h2>
        <form className="ligne" onSubmit={rechercher}>
          <input
            placeholder="Ex : 2024001"
            value={matricule}
            onChange={(e) => setMatricule(e.target.value)}
            required
          />
          <button type="submit">Actualiser</button>
        </form>

        {recherche === 'chargement' && <small>Recherche dans la base…</small>}
        {recherche === 'introuvable' && <div className="alerte">Matricule introuvable dans la base de données.</div>}

        <div className="formulaire">
          <label>
            <span>Nom</span>
            <input value={fiche?.nom ?? ''} readOnly placeholder="—" />
          </label>
          <label>
            <span>Prénoms</span>
            <input value={fiche?.prenoms ?? ''} readOnly placeholder="—" />
          </label>
          <label>
            <span>Téléphone</span>
            <input value={fiche?.telephone ?? ''} readOnly placeholder="—" />
          </label>
          <label>
            <span>E-mail</span>
            <input value={fiche?.email ?? ''} readOnly placeholder="—" />
          </label>
        </div>

        {fiche && (
          <>
            <ul className="indicateurs">
              {INDICATEURS.map(([champ, libelle]) => (
                <li key={champ}>
                  {libelle} :
                  <span className={`badge ${fiche[champ] ? 'confirmee' : 'annulee'}`}>{fiche[champ] ? 'Oui' : 'Non'}</span>
                </li>
              ))}
            </ul>
            {fiche.conditions_manquantes.length > 0 && (
              <div className="alerte">
                Réservation impossible pour le moment. Il manque : {fiche.conditions_manquantes.join(', ')}.
              </div>
            )}
          </>
        )}

        {mes.length > 0 && (
          <div className="tableau">
            <table>
              <thead>
                <tr>
                  <th>Créneau</th>
                  <th>Salle</th>
                  <th>Statut</th>
                  <th />
                </tr>
              </thead>
              <tbody>
                {mes.map((r) => (
                  <tr key={r.num_reservation}>
                    <td>
                      {r.creneau.date_creneau} {heure(r.creneau.heure_debut)}–{heure(r.creneau.heure_fin)}
                    </td>
                    <td>
                      {r.creneau.num_salle} <small>({r.creneau.salle?.emplacement?.adresse})</small>
                    </td>
                    <td>
                      <span className={`badge ${r.statut}`}>{LIBELLES[r.statut]}</span>
                      {r.position && <small> position {r.position}</small>}
                    </td>
                    <td className="actions">
                      {r.statut !== 'annulee' && (
                        <button className="danger" onClick={() => annuler(r)}>
                          Annuler
                        </button>
                      )}
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        )}
      </section>

      <section className="carte">
        <h2>2. Choisir un créneau</h2>
        <div className="tableau">
          <table>
            <thead>
              <tr>
                <th>Date</th>
                <th>Horaire</th>
                <th>Salle</th>
                <th>Disponibilité</th>
                <th />
              </tr>
            </thead>
            <tbody>
              {planning.length === 0 && (
                <tr>
                  <td colSpan="5" className="vide">
                    Aucun créneau à venir.
                  </td>
                </tr>
              )}
              {planning.map((c) => (
                <tr key={c.num_creneau} className={creneau?.num_creneau === c.num_creneau ? 'selection' : ''}>
                  <td>{c.date_creneau}</td>
                  <td>
                    {heure(c.heure_debut)} – {heure(c.heure_fin)}
                  </td>
                  <td>
                    {c.num_salle} <small>({c.salle?.emplacement?.code_emplacement})</small>
                  </td>
                  <td>
                    <span className={`badge ${c.nb_confirmees > 0 ? 'en_attente' : 'confirmee'}`}>{etat(c)}</span>
                  </td>
                  <td className="actions">
                    <button onClick={() => setCreneau(c)}>
                      {c.nb_confirmees > 0 ? "Rejoindre la file" : 'Choisir'}
                    </button>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>

      {creneau && (
        <section className="carte">
          <h2>3. Choisir 3 membres du jury</h2>
          <p>
            Créneau du <strong>{creneau.date_creneau}</strong> de {heure(creneau.heure_debut)} à{' '}
            {heure(creneau.heure_fin)} — salle {creneau.num_salle}. Sélection : {choisis.length}/3
          </p>
          <div className="grille-cases">
            {jurys.map((j) => (
              <label key={j.num_jury} className="case">
                <input
                  type="checkbox"
                  checked={choisis.includes(j.num_jury)}
                  onChange={() => basculerJury(j.num_jury)}
                />
                <span>
                  {j.nom} {j.prenoms} <small>({j.grade} — {j.role})</small>
                </span>
              </label>
            ))}
          </div>
          <div className="actions">
            <button
              className="primaire"
              disabled={choisis.length !== 3 || !fiche || fiche.conditions_manquantes.length > 0}
              onClick={reserver}
            >
              {creneau.nb_confirmees > 0 ? "Demander (file d'attente)" : 'Réserver'}
            </button>
            <button onClick={() => setCreneau(null)}>Annuler</button>
          </div>
          {!fiche && <small>Saisissez un matricule valide à l'étape 1.</small>}
        </section>
      )}
    </div>
  );
}