import { useEffect, useState } from 'react';
import { api, messageErreur } from '../api.js';
import Message from '../components/Message.jsx';

/** Association ENCADREUR : choisir les encadreurs (jurys) d'un étudiant. */
export default function Encadrement() {
  const [etudiants, setEtudiants] = useState([]);
  const [jurys, setJurys] = useState([]);
  const [matricule, setMatricule] = useState('');
  const [choisis, setChoisis] = useState([]);
  const [message, setMessage] = useState(null);

  const charger = async () => {
    setEtudiants(await api.get('/etudiants'));
    setJurys(await api.get('/jurys'));
  };

  useEffect(() => {
    charger().catch((e) => setMessage({ type: 'err', text: messageErreur(e) }));
  }, []);

  const selectionner = (m) => {
    setMatricule(m);
    const e = etudiants.find((x) => x.num_matricule === m);
    setChoisis(e ? e.encadreurs.map((j) => j.num_jury) : []);
  };

  const basculer = (id) =>
    setChoisis((c) => (c.includes(id) ? c.filter((x) => x !== id) : [...c, id]));

  const enregistrer = async () => {
    try {
      await api.put(`/etudiants/${encodeURIComponent(matricule)}/encadreurs`, { jurys: choisis });
      setMessage({ type: 'ok', text: 'Encadreurs enregistrés.' });
      await charger();
    } catch (e) {
      setMessage({ type: 'err', text: messageErreur(e) });
    }
  };

  return (
    <section className="carte">
      <h2>Encadrement des étudiants</h2>
      <Message message={message} onClose={() => setMessage(null)} />
      <label className="champ">
        <span>Étudiant</span>
        <select value={matricule} onChange={(e) => selectionner(e.target.value)}>
          <option value="">— choisir —</option>
          {etudiants.map((e) => (
            <option key={e.num_matricule} value={e.num_matricule}>
              {e.num_matricule} — {e.nom} {e.prenoms}
            </option>
          ))}
        </select>
      </label>
      {matricule && (
        <>
          <div className="grille-cases">
            {jurys.map((j) => (
              <label key={j.num_jury} className="case">
                <input type="checkbox" checked={choisis.includes(j.num_jury)} onChange={() => basculer(j.num_jury)} />
                <span>
                  {j.nom} {j.prenoms} <small>({j.grade})</small>
                </span>
              </label>
            ))}
          </div>
          <button className="primaire" onClick={enregistrer}>
            Enregistrer
          </button>
        </>
      )}
    </section>
  );
}
