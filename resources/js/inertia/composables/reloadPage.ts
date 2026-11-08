/** Lädt die Seite komplett neu (neue Sitzung, neues CSRF-Token). Eigene Funktion, damit Tests sie ersetzen können. */
export function reloadPage(): void {
  window.location.reload();
}
