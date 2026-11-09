import { $ } from "./modules/ui.js";
import { showTable, showTableFor, registerRowActions } from "./modules/table.js";
import { initScanner } from "./modules/scanner.js";
import { ENTITY_ACTIONS, openCreateWorkerDialog } from "./modules/entities.js";
import * as werkzeuge from "./modules/werkzeuge.js";
import * as loans from "./modules/loans.js";
import { toggleImages } from "./modules/gallery.js";

// Zeilenaktionen der Tabellen registrieren
registerRowActions("werkzeuge", { edit: werkzeuge.openEditDialog, remove: werkzeuge.openDeleteDialog });
registerRowActions("ausgeliehen", { extend: loans.openExtendDialog });
Object.entries(ENTITY_ACTIONS).forEach(([resource, actions]) => registerRowActions(resource, actions));

// Barcode-Scanner
initScanner([
	{ dialog: $("objectCreateScanBarcodeDialog"), onScan: werkzeuge.onCreateScan },
	{ dialog: $("borrowScanBarcodeDialog"), onScan: loans.onBorrowScan },
	{ dialog: $("returnScanBarcodeDialog"), onScan: loans.onReturnScan }
]);

// Buttons
$("addWorkerBtn").addEventListener("click", openCreateWorkerDialog);
$("createObjectBtn").addEventListener("click", werkzeuge.openCreationMethodDialog);
$("borrowBtn").addEventListener("click", loans.openBorrowScanDialog);
$("returnBtn").addEventListener("click", loans.openReturnScanDialog);
$("loadButton").addEventListener("click", showTable);
$("toggleImagesBtn").addEventListener("click", toggleImages);

showTableFor("werkzeuge");
