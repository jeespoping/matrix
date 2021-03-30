// import Sticker from "./containers/Sticker.js";
import DatosIngreso from "./common/DatosIngreso.js";

new Vue({
    el: "#app",
    props: ['empresa'],
    data: () => ({
        numeroHistoria: new DatosIngreso().obtenerNumeroHistoria(),
        numeroIngreso: new DatosIngreso().obtenerNumeroIngreso(),
        wemp_pmla: new DatosIngreso().obtenerIdEmpresa(),
    }),
    mounted: function () {
        fetch("stickers/stickers.php?consultaAjax=&wemp_pmla=" + this.wemp_pmla + "&nHis=" + this.numeroHistoria + "&nIng=" + this.numeroIngreso + "&empresa=" + this.empresa, {
            method: "POST",
            headers: {"Content-Type": "application/json"},
            // body: JSON.stringify({
            //     "numeroHistoria": this.numeroHistoria,
            //     "numeroIngreso": this.numeroIngreso,
            //     "wemp_pmla": this.wemp_pmla
            // })
        })
            .then(datos => {
                console.log(datos);
                if (datos instanceof Blob) {
                    const url = window.URL.createObjectURL(datos);
                    const link = document.createElement("a");
                    link.href = url;
                    link.setAttribute("download", 'reporte-${this.fechaInicio}-${this.fechaFin}.csv');
                    document.body.appendChild(link);
                    link.click();
                } else {
                    const respuesta = JSON.parse(datos);
                    if (respuesta.error) {
                        alert(respuesta.mensaje);
                    }
                    // console.log(respuesta);
                }
            })
            .catch(err => console.log(err));
    },
    // components: {Sticker},
    template:
        `
   <div class="container">
        <form class="row g-1">
            <div class="form-floating col-md-6">
                <input type="number" class="form-control" id="nHistoria" placeholder="999999" :value="numeroHistoria">
                <label for="nHistoria">N&uacute;mero Historia</label>
            </div>
            <div class="form-floating col-md-6">
                <input type="number" class="form-control" id="nIngreso" placeholder="0" :value="numeroIngreso">
                <label for="nIngreso">N&uacute;mero Ingreso</label>
            </div>
            <div class="form-floating col-12">
                <input type="text" class="form-control" id="nombres" placeholder="Pedro Perez"
                    value="Pedro Neron Navarrete">
                <label for="nombres">Nombres Completos</label>
            </div>
            <div class="form-floating col-12">
                <input type="text" class="form-control" id="documento" placeholder="CC 99999999" value="CC 99999999">
                <label for="documento">Tipo y Documento</label>
            </div>
            <div class="form-floating col-md-4">
                <input type="text" class="form-control" id="genero" placeholder="F=Femenino; M=Masculino" value="M">
                <label for="genero">Genero</label>
            </div>
            <div class="form-floating col-md-4">
                <input type="text" class="form-control" id="edad" placeholder="En Meses o años" value="38 años">
                <label for="edad">Edad</label>
            </div>
            <div class="form-floating col-md-4">
                <input type="text" class="form-control" id="dx" placeholder="Diagnostico" value="K088">
                <label for="dx">Diagnostico</label>
            </div>
            <div class="form-floating col-md-12">
                <input type="text" class="form-control" id="eps" placeholder="Aseguradora" value="Sura EPS">
                <label for="eps">Aseguradora</label>
            </div>
            <div class="col-12 text-center">
                <button type="submit" class="btn btn-primary">Generar Sticker</button>
            </div>
        </form>
    </div>
   `
})
;