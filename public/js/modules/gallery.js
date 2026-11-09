import { $ } from "./ui.js";

// Zertifikat-Bilder ein- und ausblenden
export function toggleImages() {
	const gallery = $("imageGallery");
	const btn = $("toggleImagesBtn");
	gallery.classList.toggle("show");
	btn.textContent = gallery.classList.contains("show") ? "Certifikate ausblenden" : "Certifikate anzeigen";
}
