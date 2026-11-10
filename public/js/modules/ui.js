export const $ = (id) => document.getElementById(id);

// kurzes Popup nach erfolgreicher Aktion
export function showToast(message) {
	const toast = $("toast");
	toast.textContent = message;
	toast.classList.add("show");
	setTimeout(() => toast.classList.remove("show"), 3000);
}

export function showError(prefix, result, suffix = "") {
	alert(`${prefix}: ${result.error || "Unbekannter Fehler"}${suffix}`);
}

// Dialog öffnen und Abbrechen-Button verdrahten
export function openDialog(dialog, cancelBtn) {
	if (cancelBtn) cancelBtn.onclick = () => dialog.close();
	dialog.showModal();
}

// Speichern-Button nur aktivieren, wenn isValid() true liefert
export function bindValidation(inputs, saveBtn, isValid) {
	const validate = () => { saveBtn.disabled = !isValid(); };
	inputs.forEach((input) => {
		input.oninput = validate;
		input.onchange = validate;
	});
	validate();
	return validate;
}

export const hasText = (input) => input.value.trim().length > 0;

// Select mit Optionen füllen
export function fillSelect(select, items, valueOf, labelOf) {
	select.innerHTML = "";
	items.forEach((item) => {
		const option = document.createElement("option");
		option.value = valueOf(item);
		option.textContent = labelOf(item);
		select.appendChild(option);
	});
}

// Barcode-Wert eines Eintrags (Spalte, deren Name "barcode" enthält)
export function getBarcodeFromEntry(entry) {
	for (const key in entry) {
		if (key.toLowerCase().includes("barcode")) return entry[key];
	}
}
