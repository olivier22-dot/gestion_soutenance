import { useCallback, useEffect, useState } from 'react';
import { api, messageErreur } from '../api.js';
import Message from './Message.jsx';

function valeursInitiales(champs) {
  const v = {};
  champs.forEach((c) => {
    v[c.name] = c.type === 'checkbox' ? false : '';
  });
  return v;
}

/**
 * Page CRUD générique pilotée par une configuration (voir pages/config.js).
 */
export default function CrudPage({ titre, endpoint, pk, pkAuto = false, colonnes, champs }) {
  const [lignes, setLignes] = useState([]);
  const [options, setOptions] = useState({});
  const [form, setForm] = useState(null); // null = formulaire fermé
  const [edition, setEdition] = useState(false);
  const [message, setMessage] = useState(null);

  const charger = useCallback(async () => {
    setLignes(await api.get(endpoint));
    const opts = {};
    for (const c of champs.filter((c) => c.type === 'select')) {
      opts[c.name] = await api.get(c.source);
    }
    setOptions(opts);
  }, [endpoint, champs]);

  useEffect(() => {
    setForm(null);
    setMessage(null);
    charger().catch((e) => setMessage({ type: 'err', text: messageErreur(e) }));
  }, [charger]);

  const ouvrirNouveau = () => {
    setEdition(false);
    setForm(valeursInitiales(champs));
  };

  const ouvrirEdition = (ligne) => {
    const v = valeursInitiales(champs);
    champs.forEach((c) => {
      let val = ligne[c.name] ?? v[c.name];
      if (c.type === 'time' && typeof val === 'string') val = val.slice(0, 5);
      v[c.name] = val;
    });
    if (!pkAuto) v[pk] = ligne[pk];
    setEdition(true);
    setForm(v);
  };

  const enregistrer = async (e) => {
    e.preventDefault();
    try {
      const corps = {};
      champs.forEach((c) => {
        if (!edition || c.name !== pk) corps[c.name] = form[c.name];
      });
      if (!pkAuto && !edition) corps[pk] = form[pk];
      if (edition) await api.put(`${endpoint}/${encodeURIComponent(form[pk])}`, corps);
      else await api.post(endpoint, corps);
      setForm(null);
      setMessage({ type: 'ok', text: 'Enregistré.' });
      await charger();
    } catch (err) {
      setMessage({ type: 'err', text: messageErreur(err) });
    }
  };

  const supprimer = async (ligne) => {
    if (!window.confirm('Supprimer cet élément ?')) return;
    try {
      await api.del(`${endpoint}/${encodeURIComponent(ligne[pk])}`);
      setMessage({ type: 'ok', text: 'Supprimé.' });
      await charger();
    } catch (err) {
      setMessage({ type: 'err', text: messageErreur(err) });
    }
  };

  const modifier = (name, valeur) => setForm((f) => ({ ...f, [name]: valeur }));

  // Champs affichés dans le formulaire : la clé primaire manuelle vient en premier
  const champsForm = pkAuto ? champs : [{ name: pk, label: pk, type: 'text', pkManuel: true }, ...champs.filter((c) => c.name !== pk)];

  return (
    <section className="carte">
      <div className="barre">
        <h2>{titre}</h2>
        <button className="primaire" onClick={ouvrirNouveau}>
          + Ajouter
        </button>
      </div>
      <Message message={message} onClose={() => setMessage(null)} />

      {form && (
        <form className="formulaire" onSubmit={enregistrer}>
          {champsForm.map((c) => (
            <label key={c.name} className={c.type === 'checkbox' ? 'case' : ''}>
              <span>{c.label}</span>
              {c.type === 'select' ? (
                <select
                  value={form[c.name]}
                  required={c.required !== false}
                  onChange={(e) => modifier(c.name, e.target.value)}
                >
                  <option value="">— choisir —</option>
                  {(options[c.name] || []).map((o) => (
                    <option key={o[c.optionValue]} value={o[c.optionValue]}>
                      {c.optionLabel(o)}
                    </option>
                  ))}
                </select>
              ) : c.type === 'checkbox' ? (
                <input type="checkbox" checked={!!form[c.name]} onChange={(e) => modifier(c.name, e.target.checked)} />
              ) : (
                <input
                  type={c.type || 'text'}
                  value={form[c.name] ?? ''}
                  required={c.required !== false && c.type !== 'checkbox'}
                  disabled={c.pkManuel && edition}
                  min={c.type === 'number' ? 1 : undefined}
                  onChange={(e) => modifier(c.name, e.target.value)}
                />
              )}
            </label>
          ))}
          <div className="actions">
            <button type="submit" className="primaire">
              {edition ? 'Mettre à jour' : 'Créer'}
            </button>
            <button type="button" onClick={() => setForm(null)}>
              Annuler
            </button>
          </div>
        </form>
      )}

      <div className="tableau">
        <table>
          <thead>
            <tr>
              {colonnes.map((c) => (
                <th key={c.label}>{c.label}</th>
              ))}
              <th />
            </tr>
          </thead>
          <tbody>
            {lignes.length === 0 && (
              <tr>
                <td colSpan={colonnes.length + 1} className="vide">
                  Aucune donnée.
                </td>
              </tr>
            )}
            {lignes.map((l) => (
              <tr key={l[pk]}>
                {colonnes.map((c) => (
                  <td key={c.label}>{c.rendu ? c.rendu(l) : l[c.key]}</td>
                ))}
                <td className="actions">
                  <button onClick={() => ouvrirEdition(l)}>Modifier</button>
                  <button className="danger" onClick={() => supprimer(l)}>
                    Supprimer
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </section>
  );
}
