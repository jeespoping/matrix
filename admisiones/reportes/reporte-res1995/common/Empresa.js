
export default class Empresa {
    constructor(){
        this.id= '01';
    }
    obtenerIdEmpresa() {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        this.id = urlParams.get('wemp_pmla');
        return this.id;
    }
}