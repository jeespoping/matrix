export default class DatosIngreso {

    obtenerIdEmpresa() {
        return new URLSearchParams(window.location.search).get("wemp_pmla");
    }

    obtenerNumeroHistoria() {
        return new URLSearchParams(window.location.search).get("nHis");
    }

    obtenerNumeroIngreso() {
        return new URLSearchParams(window.location.search).get("nIng");
    }
}