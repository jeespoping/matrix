export default class Permisos {
    constructor() {
        this.codigoGrupo = '';
        this.codigoOpcion = '';
    }
    obtenerIdGrupo() {
        this.codigoGrupo = this.obtenerParametroURL('grupo');
        return this.codigoGrupo;
    }
    obtenerIdOpcion() {
        this.codigoOpcion = this.obtenerParametroURL('opcion');
        return this.codigoOpcion;
    }
    obtenerParametroURL(nombreParametro) {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        return urlParams.get(nombreParametro);
    }
}