export const validacionContentType = (response) => {
    const contentType = response.headers.get("content-type");
    if (response.ok) {
        if (contentType.includes('text/csv')) {
            return response.blob();
        } else {
            let text = response.text();
            console.log(text);
            return text;
        }
    } else {
        throw "Error en la petición";
    }
}
