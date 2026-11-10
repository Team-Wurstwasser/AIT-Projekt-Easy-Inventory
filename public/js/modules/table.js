import { apiGet, barcodeUrl } from "./api.js";
import { $ } from "./ui.js";

// Spalten, die in der Tabelle nicht angezeigt werden
const HIDDEN_COLUMNS = {
	werkzeuge: ["Typ_ID", "Status_ID"],
	mitarbeiter: ["Abteilung_ID"]
};

// Zeilenaktionen je Ressource: { edit, remove } oder { extend }
const rowActions = {};
export function registerRowActions(resource, actions) {
	rowActions[resource] = actions;
}

export async function showTableFor(resource) {
	$("typeSelect").value = resource;
	await showTable();
}

// Tabelle mit Daten der gewählten Kategorie füllen
export async function showTable() {
	const message = $("WaitingMessage");
	const resource = $("typeSelect").value;
	const head = $("headerRow");
	const body = $("tableData");

	message.textContent = "Tabelle wird geladen...";
	head.innerHTML = "";
	body.innerHTML = "";

	const json = await apiGet(resource);
	if (json.success === false) {
		message.textContent = "Fehler: " + json.error;
		return;
	}

	const rows = json.data;
	if (!rows || rows.length === 0) {
		message.textContent = "Keine Daten vorhanden!";
		return;
	}
	message.textContent = "";

	const hidden = HIDDEN_COLUMNS[resource] || [];
	const columns = Object.keys(rows[0]).filter((c) => !hidden.includes(c));
	const actions = rowActions[resource];

	columns.forEach((key, index) => head.appendChild(buildHeaderCell(key, index)));
	if (actions) {
		const th = document.createElement("th");
		th.textContent = actions.extend ? "Verlängern" : "Aktion";
		head.appendChild(th);
	}

	rows.forEach((entry) => {
		const tr = document.createElement("tr");
		columns.forEach((key) => tr.appendChild(buildCell(key, entry)));
		if (actions) tr.appendChild(buildActionCell(actions, entry));
		body.appendChild(tr);
	});
}

function buildHeaderCell(key, index) {
	const th = document.createElement("th");

	const sortHeader = document.createElement("div");
	sortHeader.className = "sort-header";
	sortHeader.title = "sort";

	const title = document.createElement("span");
	title.textContent = key;
	const icon = document.createElement("span");
	icon.className = "sort-icon";
	icon.textContent = " ↕";

	sortHeader.append(title, icon);
	sortHeader.onclick = () => sortTable(index, th);

	const filter = document.createElement("input");
	filter.type = "text";
	filter.placeholder = "filtern...";
	filter.addEventListener("input", filterTable);

	th.append(sortHeader, document.createElement("br"), filter);
	return th;
}

function buildCell(key, entry) {
	const td = document.createElement("td");
	const value = entry[key];

	if (key.toLowerCase() === "barcode") {
		const link = document.createElement("a");
		link.href = barcodeUrl(value);
		link.target = "_blank";
		link.textContent = value;
		td.appendChild(link);
	} else {
		td.textContent = value ?? "";
	}
	return td;
}

function actionButton(label, onClick) {
	const btn = document.createElement("button");
	btn.textContent = label;
	btn.className = "table-action-button";
	btn.onclick = onClick;
	return btn;
}

function buildActionCell(actions, entry) {
	const td = document.createElement("td");
	if (actions.extend) {
		td.appendChild(actionButton("Verlängern", () => actions.extend(entry)));
	} else {
		td.append(
			actionButton("Bearbeiten", () => actions.edit(entry)),
			actionButton("Löschen", () => actions.remove(entry))
		);
	}
	return td;
}

// Zeilen nach Spaltenfiltern ein-/ausblenden
function filterTable() {
	const inputs = $("headerRow").getElementsByTagName("input");
	for (const row of $("tableData").rows) {
		const cells = row.cells;
		let show = true;
		for (let i = 0; i < inputs.length; i++) {
			const needle = inputs[i].value.toLowerCase();
			if (needle && cells[i] && !cells[i].textContent.toLowerCase().includes(needle)) {
				show = false;
				break;
			}
		}
		row.style.display = show ? "" : "none";
	}
}

// Tabelle nach Spalte sortieren (auf-/absteigend im Wechsel)
function sortTable(index, th) {
	const body = $("tableData");
	const ascending = th.getAttribute("data-sort") !== "asc";

	for (const other of $("headerRow").getElementsByTagName("th")) {
		other.removeAttribute("data-sort");
		const icon = other.querySelector(".sort-icon");
		if (icon) icon.textContent = " ↕";
	}
	th.setAttribute("data-sort", ascending ? "asc" : "desc");
	th.querySelector(".sort-icon").textContent = ascending ? " ↑" : " ↓";

	const text = (row) => (row.cells[index] ? row.cells[index].innerText.trim() : "");
	const sorted = Array.from(body.rows).sort((a, b) => {
		const [x, y] = [text(a), text(b)];
		const [nx, ny] = [parseFloat(x), parseFloat(y)];
		const cmp = !isNaN(nx) && !isNaN(ny) ? nx - ny : x.localeCompare(y);
		return ascending ? cmp : -cmp;
	});
	sorted.forEach((row) => body.appendChild(row));
}
