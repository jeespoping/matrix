export const validacionContentType = (response) => {
    const contentType = response.headers.get("content-type");
    if (response.ok) {
        if (contentType.includes('text/csv')) {
            return response.blob();
        } else {
            return response.text();
        }
    } else {
        throw "Error en la petición";
    }
}
