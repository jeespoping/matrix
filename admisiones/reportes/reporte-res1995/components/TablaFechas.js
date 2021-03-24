import FilaTabla from './FilaTabla';
import {
    TODAYSTRING
} from '../common/dateTime';
import {
    validacionContentType
} from '../common/Response';
import Empresa from '../common/Empresa';

export default {
    name: 'TablaFechas',
    data: function () {
        return {
            fechaInicio: TODAYSTRING,
            fechaFin: TODAYSTRING,
            wemp_pmla: new Empresa().obtenerIdEmpresa(),
            accion: 'DESCARGAR_REPORTE',
        }
    },
    methods: {
        onSubmit: function () {
            fetch(`?consultaAjax=&wemp_pmla=${this.wemp_pmla}`, {
                    method: 'POST',
                    body: JSON.stringify({
                        'fechaInicio': this.fechaInicio,
                        'fechaFin': this.fechaFin,
                        'wemp_pmla': this.wemp_pmla,
                        'accion': this.accion,
                    })
                })
                .then(response => {
                    return validacionContentType(response);
                })
                .then(datos => {
                    if (datos instanceof Blob) {
                        const url = window.URL.createObjectURL(datos);
                        const link = document.createElement('a');
                        link.href = url;
                        link.setAttribute('download', `reporte-${this.fechaInicio}-${this.fechaFin}.csv`);
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
        onClose: () => {
            window.close();
        }
    },
    components: {
        FilaTabla,
    },
    template: `
    <form v-on:submit.prevent="onSubmit" method="post">
        <table align='center' border=0 width=402>
            <FilaTabla titulo="Fecha Inicial" v-model="fechaInicio"></FilaTabla>
            <FilaTabla titulo="Fecha Final" v-model="fechaFin"></FilaTabla>
        </table>
        <table align='center' border=0 width=402>	
            <tr>
                <td align=center bgcolor=cccccc colspan=2></b>
                    <input type='submit'>
                    <button type=button v-on:click="onClose">Cerrar</button></b>
                </td>
            </tr>
        </table>
    </form>
    `
}