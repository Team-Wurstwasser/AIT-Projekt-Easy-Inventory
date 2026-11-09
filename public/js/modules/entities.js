// Konfigurationsgetriebene Dialoge (Hinzufügen / Bearbeiten / Löschen)
// für Mitarbeiter, Abteilungen, Werkzeugtypen und Status.
import { apiPost, apiPut, apiDelete } from "./api.js";
import { loadAbteilungen } from "./lookups.js";
import { $, showToast, showError, openDialog, bindValidation, hasText } from "./ui.js";
import { showTable, showTableFor } from "./table.js";

/* ---------- Hinzufügen ---------- */

const ADD = {
	typ: {
		dialog: "addTypeDialog", input: "newTypeName", save: "saveTypeBtn", cancel: "cancelAddTypeBtn",
		resource: "werkzeug_typen", payload: (v) => ({ art: v }),
		empty: "Bitte einen Typ eingeben.", done: "Typ hinzugefügt", fail: "Typ konnte nicht hinzugefügt werden"
	},
	abteilung: {
		dialog: "addAbteilungDialog", input: "newAbteilungName", save: "saveAbteilungBtn", cancel: "cancelAddAbteilungBtn",
		resource: "abteilungen", payload: (v) => ({ name: v }),
		empty: "Bitte einen Namen eingeben.", done: "Abteilung hinzugefügt", fail: "Abteilung konnte nicht hinzugefügt werden"
	},
	status: {
		dialog: "addStatusDialog", input: "newStatusName", save: "saveStatusBtn", cancel: "cancelAddStatusBtn",
		resource: "status", payload: (v) => ({ bezeichnung: v }),
		empty: "Bitte eine Bezeichnung eingeben.", done: "Status hinzugefügt", fail: "Status konnte nicht hinzugefügt werden"
	}
};

// Liefert ein Promise<boolean>: true, wenn etwas hinzugefügt wurde
function openAddDialog(cfg) {
	const dialog = $(cfg.dialog);
	const input = $(cfg.input);
	input.value = "";

	return new Promise((resolve) => {
		openDialog(dialog, $(cfg.cancel));
		$(cfg.cancel).onclick = () => { dialog.close(); resolve(false); };

		$(cfg.save).onclick = async () => {
			const value = input.value.trim();
			if (!value) { alert(cfg.empty); return; }

			const result = await apiPost(cfg.resource, cfg.payload(value));
			if (result.success) {
				showToast(cfg.done);
				dialog.close();
				resolve(true);
			} else {
				showError(cfg.fail, result);
			}
		};
	});
}

export const openAddTypeDialog = () => openAddDialog(ADD.typ);
export const openAddAbteilungDialog = () => openAddDialog(ADD.abteilung);
export const openAddStatusDialog = () => openAddDialog(ADD.status);

/* ---------- Bearbeiten ---------- */

// field.kind: "text" | "email" | "select"
const EDIT = {
	mitarbeiter: {
		label: "Mitarbeiter", dialog: "editMitarbeiterDialog", save: "saveEditMitarbeiterBtn", cancel: "cancelEditMitarbeiterBtn",
		resource: "mitarbeiter", idInput: "editMitarbeiterId", idKey: "Mitarbeiter_ID", idPayload: "mitarbeiter_id",
		fields: [
			{ input: "editWorkerFirstName", from: "Vorname", payload: "vorname", kind: "text" },
			{ input: "editWorkerLastName", from: "Nachname", payload: "nachname", kind: "text" },
			{ input: "editWorkerEmail", from: "Email", payload: "email", kind: "email" },
			{ input: "editWorkerDepartmentSelect", from: "Abteilung_ID", payload: "abteilung_id", kind: "select", load: loadAbteilungen }
		]
	},
	abteilung: {
		label: "Abteilung", dialog: "editAbteilungDialog", save: "saveEditAbteilungBtn", cancel: "cancelEditAbteilungBtn",
		resource: "abteilungen", idInput: "editAbteilungId", idKey: "Abteilung_ID", idPayload: "abteilung_id",
		fields: [{ input: "editAbteilungName", from: "Name", payload: "name", kind: "text" }]
	},
	werkzeugTyp: {
		label: "Werkzeugtyp", dialog: "editWerkzeugTypDialog", save: "saveEditWerkzeugTypBtn", cancel: "cancelEditWerkzeugTypBtn",
		resource: "werkzeug_typen", idInput: "editWerkzeugTypId", idKey: "Typ_ID", idPayload: "typ_id",
		fields: [{ input: "editWerkzeugTypName", from: "Typ", payload: "art", kind: "text" }]
	},
	status: {
		label: "Status", dialog: "editStatusDialog", save: "saveEditStatusBtn", cancel: "cancelEditStatusBtn",
		resource: "status", idInput: "editStatusId", idKey: "Status_ID", idPayload: "status_id",
		fields: [{ input: "editStatusName", from: "Bezeichnung", payload: "bezeichnung", kind: "text" }]
	}
};

const fieldValue = (f, el) => (f.kind === "select" ? parseInt(el.value) || 0 : el.value.trim());
const fieldValid = (f, el) =>
	f.kind === "select" ? true :
	f.kind === "email" ? hasText(el) && el.checkValidity() :
	hasText(el);

async function openEditDialog(cfg, entry) {
	const dialog = $(cfg.dialog);
	const saveBtn = $(cfg.save);
	const els = cfg.fields.map((f) => $(f.input));

	$(cfg.idInput).value = entry[cfg.idKey] || "";
	for (const [i, f] of cfg.fields.entries()) {
		if (f.load) await f.load(els[i]);
		els[i].value = entry[f.from] || "";
	}

	bindValidation(els, saveBtn, () => cfg.fields.every((f, i) => fieldValid(f, els[i])));

	saveBtn.onclick = async () => {
		const payload = { [cfg.idPayload]: parseInt($(cfg.idInput).value) || 0 };
		cfg.fields.forEach((f, i) => { payload[f.payload] = fieldValue(f, els[i]); });

		const result = await apiPut(cfg.resource, payload);
		if (result.success) {
			showToast(`${cfg.label} aktualisiert`);
			dialog.close();
			showTable();
		} else {
			showError(`${cfg.label} konnte nicht aktualisiert werden`, result);
		}
	};

	openDialog(dialog, $(cfg.cancel));
}

/* ---------- Löschen ---------- */

const DELETE = {
	mitarbeiter: {
		label: "Mitarbeiter", dialog: "deleteMitarbeiterDialog", confirm: "confirmDeleteMitarbeiterBtn", cancel: "cancelDeleteMitarbeiterBtn",
		resource: "mitarbeiter",
		show: { deleteMitarbeiterName: (e) => `${e.Vorname || ""} ${e.Nachname || ""}`.trim(), deleteMitarbeiterEmail: (e) => e.Email || "" },
		body: (e) => ({ mitarbeiter_id: parseInt(e.Mitarbeiter_ID) || 0 })
	},
	abteilung: {
		label: "Abteilung", dialog: "deleteAbteilungDialog", confirm: "confirmDeleteAbteilungBtn", cancel: "cancelDeleteAbteilungBtn",
		resource: "abteilungen",
		show: { deleteAbteilungName: (e) => e.Name || "" },
		body: (e) => ({ abteilung_id: parseInt(e.Abteilung_ID) || 0 })
	},
	werkzeugTyp: {
		label: "Werkzeugtyp", dialog: "deleteWerkzeugTypDialog", confirm: "confirmDeleteWerkzeugTypBtn", cancel: "cancelDeleteWerkzeugTypBtn",
		resource: "werkzeug_typen",
		show: { deleteWerkzeugTypName: (e) => e.Typ || "" },
		body: (e) => ({ art: e.Typ || "" })
	},
	status: {
		label: "Status", dialog: "deleteStatusDialog", confirm: "confirmDeleteStatusBtn", cancel: "cancelDeleteStatusBtn",
		resource: "status",
		show: { deleteStatusName: (e) => e.Bezeichnung || "" },
		body: (e) => ({ status_id: parseInt(e.Status_ID) || 0 })
	}
};

function openDeleteDialog(cfg, entry) {
	const dialog = $(cfg.dialog);
	for (const [id, from] of Object.entries(cfg.show)) $(id).textContent = from(entry);

	$(cfg.confirm).onclick = async () => {
		const result = await apiDelete(cfg.resource, cfg.body(entry));
		if (result.success) {
			showToast(`${cfg.label} gelöscht`);
			dialog.close();
			showTable();
		} else {
			showError(`${cfg.label} konnte nicht gelöscht werden`, result);
		}
	};
	openDialog(dialog, $(cfg.cancel));
}

/* ---------- Öffentliche Aktionen je Tabelle ---------- */

export const ENTITY_ACTIONS = {
	mitarbeiter: { edit: (e) => openEditDialog(EDIT.mitarbeiter, e), remove: (e) => openDeleteDialog(DELETE.mitarbeiter, e) },
	abteilung: { edit: (e) => openEditDialog(EDIT.abteilung, e), remove: (e) => openDeleteDialog(DELETE.abteilung, e) },
	werkzeug_typen: { edit: (e) => openEditDialog(EDIT.werkzeugTyp, e), remove: (e) => openDeleteDialog(DELETE.werkzeugTyp, e) },
	status: { edit: (e) => openEditDialog(EDIT.status, e), remove: (e) => openDeleteDialog(DELETE.status, e) }
};

/* ---------- Mitarbeiter anlegen ---------- */

export async function openCreateWorkerDialog() {
	const dialog = $("createWorkerDialog");
	const select = $("workerDepartmentSelect");
	const inputs = ["workerFirstName", "workerLastName", "workerEmail"].map($);
	const [firstName, lastName, email] = inputs;
	const submitBtn = $("createWorkerSubmitBtn");

	inputs.forEach((i) => { i.value = ""; });
	await loadAbteilungen(select);

	const addBtn = $("addAbteilungBtn");
	addBtn.title = "Neue Abteilung hinzufügen";
	addBtn.onclick = async () => {
		await openAddAbteilungDialog();
		await loadAbteilungen(select);
	};

	bindValidation(inputs, submitBtn, () => hasText(firstName) && hasText(lastName) && hasText(email) && email.checkValidity());

	submitBtn.onclick = () => {
		saveWorker();
		dialog.close();
	};
	openDialog(dialog, null);
	$("closeBtnWorker").onclick = () => dialog.close();
}

async function saveWorker() {
	const result = await apiPost("mitarbeiter", {
		vorname: $("workerFirstName").value.trim(),
		nachname: $("workerLastName").value.trim(),
		email: $("workerEmail").value.trim(),
		abteilung_id: $("workerDepartmentSelect").value
	});
	if (result.success) {
		showToast("Mitarbeiter erfolgreich angelegt!");
		showTableFor("mitarbeiter");
	} else {
		showError("Fehler beim Anlegen", result, "\nMöglicherweise existiert bereits ein Mitarbeiter mit dieser E-Mail-Adresse");
	}
}
