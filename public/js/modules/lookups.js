import { apiGet } from "./api.js";
import { fillSelect } from "./ui.js";

// Erzeugt eine Funktion, die ein Select mit den Daten einer Ressource füllt
const selectLoader = (resource, valueKey, labelOf) => async (select) => {
	select.innerHTML = "";
	const json = await apiGet(resource);
	fillSelect(select, json.data || [], (i) => i[valueKey], labelOf);
};

export const loadWerkzeugTypen = selectLoader("werkzeug_typen", "Typ_ID", (t) => t.Typ);
export const loadMitarbeiter = selectLoader("mitarbeiter", "Mitarbeiter_ID", (m) => `${m.Vorname} ${m.Nachname}`);
export const loadAbteilungen = selectLoader("abteilungen", "Abteilung_ID", (a) => a.Name);
export const loadStatus = selectLoader("status", "Status_ID", (s) => s.Bezeichnung);
