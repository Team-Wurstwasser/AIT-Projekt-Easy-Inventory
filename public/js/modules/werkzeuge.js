import { apiGet, apiPost, apiPut, apiDelete, barcodeUrl } from "./api.js";
import { currentObject } from "./state.js";
import { loadWerkzeugTypen, loadStatus } from "./lookups.js";
import { openAddTypeDialog, openAddStatusDialog } from "./entities.js";
import { showTable, showTableFor } from "./table.js";
import { $, showToast, showError, openDialog, bindValidation, hasText, getBarcodeFromEntry } from "./ui.js";

/* ---------- Objekt anlegen ---------- */

// Auswahl: Barcode scannen oder erzeugen
export function openCreationMethodDialog() {
	const dialog = $("objectCreationMethodSelectionDialog");
	$("closeBtn").onclick = () => dialog.close();
	$("scannerMode").onclick = () => { dialog.close(); openCreateScanDialog(); };
	$("manualMode").onclick = () => { dialog.close(); generateBarcode(); };
	dialog.showModal();
}

function openCreateScanDialog() {
	openDialog($("objectCreateScanBarcodeDialog"), $("closeBtn1"));
}

// Nach erfolgreichem Scan (vom Scanner aufgerufen)
export function onCreateScan(barcode) {
	currentObject.id = barcode;
	updateOverviewUI();
	openConfigDialog();
}

// Barcode über die API erzeugen lassen
async function generateBarcode() {
	const json = await apiGet("barcode");
	currentObject.id = json.barcode;
	updateOverviewUI();
	openConfigDialog();
}

function updateOverviewUI() {
	$("objectID").value = currentObject.id;
	$("objectOverviewID").textContent = "ID: " + currentObject.id;
	$("objectOverviewName").textContent = "Name: " + currentObject.name;
	$("objectOverviewType").textContent = "Typ: " + currentObject.typName;
	$("objectOverviewPurchaseDate").textContent = "Anschaffungsdatum: " + currentObject.date;
}

async function openConfigDialog() {
	const dialog = $("objectConfigDialog");
	const name = $("objectName");
	const date = $("objectPurchaseDate");
	const typeSelect = $("objectTypeSelect");
	const submit = $("objectSubmitBtn");

	name.value = "";
	date.value = "";
	await loadWerkzeugTypen(typeSelect);

	const addTypeBtn = $("addObjectTypeBtn");
	addTypeBtn.title = "Neuen Typ hinzufügen";
	addTypeBtn.onclick = async () => {
		await openAddTypeDialog();
		await loadWerkzeugTypen(typeSelect);
	};

	bindValidation([name], submit, () => hasText(name) && currentObject.id.length > 0);

	submit.onclick = async () => {
		Object.assign(currentObject, {
			name: name.value,
			date: date.value,
			typId: typeSelect.value,
			typName: typeSelect.options[typeSelect.selectedIndex].text
		});
		dialog.close();

		if (!(await saveNewObject())) return;

		$("barcodeimg").src = barcodeUrl(currentObject.id);
		updateOverviewUI();
		openOverviewDialog();
	};

	openDialog(dialog, $("cancelObjectConfigBtn"));
}

async function saveNewObject() {
	const result = await apiPost("werkzeuge", {
		barcode: currentObject.id,
		bezeichnung: currentObject.name,
		typ_id: currentObject.typId,
		anschaffungsdatum: currentObject.date
	});

	if (result.success) {
		showToast("Objekt erfolgreich angelegt!");
		showTableFor("werkzeuge");
		return true;
	}
	showError("Fehler beim Anlegen", result);
	return false;
}

// Zusammenfassung des erstellten Objekts inkl. Barcode-Download
function openOverviewDialog() {
	$("downloadBarcodeBtn").onclick = async () => {
		const blob = await (await fetch($("barcodeimg").src)).blob();
		const link = document.createElement("a");
		link.href = URL.createObjectURL(blob);
		link.download = `barcode_${currentObject.id}.png`;
		document.body.appendChild(link);
		link.click();
		document.body.removeChild(link);
		URL.revokeObjectURL(link.href);
	};
	openDialog($("createdObjectOverviewDialog"), null);
	$("closeCreatedObjectOverviewBtn").onclick = () => $("createdObjectOverviewDialog").close();
}

/* ---------- Objekt bearbeiten ---------- */

export async function openEditDialog(entry) {
	const dialog = $("editWerkzeugDialog");
	const bezeichnung = $("editBezeichnung");
	const typeSelect = $("editTypeSelect");
	const statusSelect = $("editStatusSelect");
	const date = $("editPurchaseDate");
	const saveBtn = $("saveEditBtn");

	$("editBarcode").textContent = entry.Barcode;
	bezeichnung.value = entry.Bezeichnung;
	date.value = entry.Anschaffungsdatum;

	await loadWerkzeugTypen(typeSelect);
	await loadStatus(statusSelect);

	const addStatusBtn = $("addStatusBtn");
	addStatusBtn.title = "Neuen Status hinzufügen";
	addStatusBtn.onclick = async () => {
		await openAddStatusDialog();
		await loadStatus(statusSelect);
	};

	typeSelect.value = entry.Typ_ID || "";
	statusSelect.value = entry.Status_ID || "";

	bindValidation([bezeichnung, typeSelect], saveBtn, () => hasText(bezeichnung));

	saveBtn.onclick = async () => {
		const result = await apiPut("werkzeuge", {
			barcode: entry.Barcode,
			bezeichnung: bezeichnung.value.trim(),
			typ_id: parseInt(typeSelect.value) || 0,
			status_id: parseInt(statusSelect.value) || 0,
			anschaffungsdatum: date.value
		});
		if (result.success) {
			showToast("Objekt aktualisiert");
			dialog.close();
			showTable();
		} else {
			showError("Aktualisierung fehlgeschlagen", result);
		}
	};

	openDialog(dialog, $("cancelEditBtn"));
}

/* ---------- Objekt löschen ---------- */

export function openDeleteDialog(entry) {
	const dialog = $("deleteObjectDialog");
	const barcode = getBarcodeFromEntry(entry);
	$("deleteBarcode").textContent = barcode;
	$("deleteName").textContent = entry.Bezeichnung || "";

	$("confirmDeleteBtn").onclick = async () => {
		const result = await apiDelete("werkzeuge", { barcode });
		if (result.success) {
			showToast("Objekt gelöscht");
			dialog.close();
			showTable();
		} else {
			showError("Löschen fehlgeschlagen", result);
		}
	};
	openDialog(dialog, $("cancelDeleteBtn"));
}
