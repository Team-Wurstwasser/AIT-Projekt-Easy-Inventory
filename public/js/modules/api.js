const url = (resource, query = "") => `/api/${resource}${query}`;

// Fragt die API an und liefert immer ein Objekt mit success/error zurück.
export async function api(resource, { method = "GET", body } = {}) {
	try {
		const response = await fetch(url(resource), {
			method,
			headers: body ? { "Content-Type": "application/json" } : undefined,
			body: body ? JSON.stringify(body) : undefined
		});
		return await response.json();
	} catch (err) {
		console.error(err);
		return { success: false, error: "Server nicht erreichbar" };
	}
}

export const apiGet = (resource) => api(resource);
export const apiPost = (resource, body) => api(resource, { method: "POST", body });
export const apiPut = (resource, body) => api(resource, { method: "PUT", body });
export const apiDelete = (resource, body) => api(resource, { method: "DELETE", body });

// Bild-URL eines Barcodes
export const barcodeUrl = (code) => url("barcode", `?code=${encodeURIComponent(code)}`);
