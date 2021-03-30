import FilaTabla from "./FilaTabla.js";

// import { validacionContentType } from "../common/Response";
import DatosIngreso from "../common/DatosIngreso.js";

export default {
    name: "TablaFechas",
    data: function () {
        return {
            numeroHistoria: new DatosIngreso().obtenerNumeroHistoria(),
            numeroIngreso: new DatosIngreso().obtenerNumeroIngreso(),
            wemp_pmla: new DatosIngreso().obtenerIdEmpresa(),
        }
    },
    methods: {
        onSubmit: function () {
            fetch('?consultaAjax=&wemp_pmla=${this.wemp_pmla}&nHis=${this.numeroHistoria}&nIng=${this.numeroIngreso}', {
                method: "POST",
                body: JSON.stringify({
                    "numeroHistoria": this.numeroHistoria,
                    "numeroIngreso": this.numeroIngreso,
                    "wemp_pmla": this.wemp_pmla
                })
            })
                // .then(response => validacionContentType(response))
                .then(datos => {
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
                    }
                })
                .catch(err => console.error(err));
        },
        onClose: () => { window.close(); }
    },
    components: { FilaTabla },
    template: `
    <form v-on:submit.prevent="onSubmit" method="post">
        <table align="center" border=0 width=500>
            <FilaTabla titulo="Fecha Inicial" v-model="fechaInicio"></FilaTabla>
            <FilaTabla titulo="Fecha Final" v-model="fechaFin"></FilaTabla>
        </table>
        <table align="center" border=0 width=402>
            <tr>
                <td align=center bgcolor=cccccc colspan=2></b>
                    <input type="submit">
                    <button type=button v-on:click="onClose">Cerrar</button></b>
                </td>
            </tr>
        </table>
    </form>`
}