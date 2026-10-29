export default function Message({ message, onClose }) {
  if (!message) return null;
  return (
    <div className={`message ${message.type}`} role="alert">
      <span>{message.text}</span>
      {onClose && (
        <button className="lien" onClick={onClose} aria-label="Fermer">
          ×
        </button>
      )}
    </div>
  );
}
