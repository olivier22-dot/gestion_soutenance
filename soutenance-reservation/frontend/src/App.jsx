import { useState } from 'react';
import Etudiant from './pages/Etudiant.jsx';
import Admin from './pages/Admin.jsx';

export default function App() {
  const [vue, setVue] = useState('etudiant');

  return (
    <div className="app">
      <header className="entete">
        <h1>Réservation de soutenances</h1>
        <nav>
          <button className={vue === 'etudiant' ? 'actif' : ''} onClick={() => setVue('etudiant')}>
            Espace étudiant
          </button>
          <button className={vue === 'admin' ? 'actif' : ''} onClick={() => setVue('admin')}>
            Administration
          </button>
        </nav>
      </header>
      <main>{vue === 'etudiant' ? <Etudiant /> : <Admin />}</main>
    </div>
  );
}
