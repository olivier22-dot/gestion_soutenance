const BASE = '/api';

async function request(path, options = {}) {
  const res = await fetch(BASE + path, {
    ...options,
    headers: { 'Content-Type': 'application/json', Accept: 'application/json' },
  });
  if (res.status === 204) return null;
  const data = await res.json().catch(() => null);
  if (!res.ok) {
    const erreur = new Error(data?.message || `Erreur serveur (${res.status})`);
    erreur.errors = data?.errors || {};
    erreur.status = res.status;
    throw erreur;
  }
  return data;
}

export const api = {
  get: (path) => request(path),
  post: (path, body) => request(path, { method: 'POST', body: JSON.stringify(body ?? {}) }),
  put: (path, body) => request(path, { method: 'PUT', body: JSON.stringify(body ?? {}) }),
  del: (path) => request(path, { method: 'DELETE' }),
};

/** Transforme une erreur API en texte lisible (messages de validation inclus). */
export function messageErreur(e) {
  const details = Object.values(e.errors || {}).flat();
  return details.length ? details.join(' ') : e.message;
}

export const heure = (h) => (h ? String(h).slice(0, 5) : '');
