// Ausleihe, Rückgabe und Verlängerung
import { apiPost, apiPut } from "./api.js";
import { currentObject } from "./state.js";
import { loadMitarbeiter } from "./lookups.js";
import { showTable, showTableFor } from "./table.js";
import { $, showToast, showError, openDialog, bindValidation, hasText } from "./ui.js";

const hasBarcode = () => currentObject.id.length > 0;

/* ---------- Ausleihe ---------- */

export function openBorrowScanDialog() {
	openDialog($("borrowScanBarcodeDialog"), $("closeBorrowScanBtn"));
}

export async function onBorrowScan(barcode) {
	currentObject.id = barcode;
	await openBorrowDialog();
}

async function openBorrowDialog() {
	const dialog = $("borrowDialog");
	const duration = $("borrowDuration");
	const borrower = $("borrowerIdSelect");
	const submit = $("borrowSubmitBtn");

	$("borrowBarcode").value = currentObject.id;
	duration.value = "";

	const validate = bindValidation([duration, borrower], submit, () =>
		hasBarcode() && (parseInt(duration.value) || 0) > 0 && (parseInt(borrower.value) || 0) > 0);

	submit.onclick = () => {
		transmitBorrow();
		dialog.close();
	};
	openDialog(dialog, $("closeBorrowDialogBtn"));

	await loadMitarbeiter(borrower);
	validate();
}

async function transmitBorrow() {
	const result = await apiPost("ausleihen", {
		barcode: currentObject.id,
		ausleihdauer: $("borrowDuration").value,
		mitarbeiter_id: $("borrowerIdSelect").value
	});

	if (result.success) {
		showToast("Objekt erfolgreich ausgeliehen!");
		showTableFor("werkzeuge");
	} else {
		showError("Fehler beim Ausleihen", result,
			`\nMöglicherweise ist das Objekt mit der Barcodenummer :\n${currentObject.id} \nnicht exestent , bereits ausgeliehen oder es waren nicht alle Daten korrekt eingegeben.`);
	}
}

/* ---------- Rückgabe ---------- */

export function openReturnScanDialog() {
	openDialog($("returnScanBarcodeDialog"), $("closeReturnScanBtn"));
}

export function onReturnScan(barcode) {
	currentObject.id = barcode;
	openReturnDialog();
}

function openReturnDialog() {
	const dialog = $("returnDialog");
	const condition = $("returnCondition");
	const submit = $("returnSubmitBtn");

	$("returnBarcode").value = currentObject.id;
	condition.value = "";
	bindValidation([condition], submit, () => hasBarcode() && hasText(condition));

	submit.onclick = () => {
		transmitReturn();
		dialog.close();
	};
	openDialog(dialog, $("closeReturnDialogBtn"));
}

async function transmitReturn() {
	const result = await apiPost("abgeben", {
		barcode: currentObject.id,
		zustand: $("returnCondition").value
	});

	if (result.success) {
		showToast("Objekt erfolgreich zurückgegeben!");
		showTableFor("werkzeuge");
	} else {
		showError("Fehler bei der Rückgabe", result,
			`\nMöglicherweise ist das Objekt mit der Barcodenummer :\n${currentObject.id} \nnicht exestent oder es waren nicht alle Daten korrekt eingegeben.`);
	}
}

/* ---------- Verlängern ---------- */

export function openExtendDialog(entry) {
	const dialog = $("verlängernDialog");
	const duration = $("verlängernDuration");
	const submit = $("verlängernSubmitBtn");

	$("verlängernBarcode").value = entry.Barcode || "";
	duration.value = "";
	bindValidation([duration], submit, () => (parseInt(duration.value) || 0) > 0);

	submit.onclick = async () => {
		const result = await apiPut("verlängern", {
			ausleih_id: parseInt(entry.Ausleih_ID) || 0,
			zusatz_tage: parseInt(duration.value) || 0
		});
		if (result.success) {
			showToast("Ausleihe verlängert");
			dialog.close();
			showTable();
		} else {
			showError("Verlängerung fehlgeschlagen", result);
		}
	};
	openDialog(dialog, $("closeVerlängernBtn"));
}
