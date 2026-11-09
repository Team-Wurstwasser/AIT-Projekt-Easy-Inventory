// Fängt Eingaben des Barcode-Scanners (Tastatureingabe + Enter) ab,
// solange einer der registrierten Scan-Dialoge offen ist.
export function initScanner(handlers) {
	let buffer = "";

	window.addEventListener("keydown", (e) => {
		const active = handlers.find(({ dialog }) => dialog.open);
		if (!active) return;

		if (e.key === "Enter") {
			if (buffer.length === 0) return;
			const barcode = buffer;
			buffer = "";
			active.dialog.close();
			active.onScan(barcode);
		} else if (e.key.length === 1) {
			buffer += e.key;
		}
	});
}
