import FilaTabla from './FilaTabla';
import {
    TODAYSTRING
} from '../common/dateTime';
import {
    validacionContentType
} from '../common/Response';
import Empresa from '../common/Empresa';

const validarFechaRequerida = (fecha, nombreCampo) => {
    if (fecha === '') {
        Swal.fire({
            icon: 'error',
            title: 'Error...',
            text: 'El campo fecha inicio no puede estar vacío',
        });
    }
}
export default {
    name: 'TablaFechas',
    data: function () {
        return {
            fechaInicio: TODAYSTRING,
            wemp_pmla: new Empresa().obtenerIdEmpresa(),
            accion: 'DESCARGAR_REPORTE',
        }
    },
    methods: {
        onSubmit: function () {
            Swal.fire({
                    title: 'Confirmación',
                    text: '¿Desea generar el informe?',
                    confirmButtonText: 'Generar',
                    showLoaderOnConfirm: true,
                    showCancelButton: true,
                    cancelButtonText: 'Cancelar',
                    preConfirm: () => {
                        return fetch(`?consultaAjax=&wemp_pmla=${this.wemp_pmla}`, {
                                method: 'POST',
                                body: JSON.stringify({
                                    'fechaInicio': this.fechaInicio,
                                    'wemp_pmla': this.wemp_pmla,
                                    'accion': this.accion,
                                })
                            })
                            .then((response) => {
                                return validacionContentType(response);
                            })
                            .then(datos => {
                                if (datos instanceof Blob) {
                                    const url = window.URL.createObjectURL(datos);
                                    const link = document.createElement('a');
                                    link.href = url;
                                    link.setAttribute('download', `reporte-${this.fechaInicio}.csv`);
                                    document.body.appendChild(link);
                                    link.click();
                                } else {
                                    const respuesta = JSON.parse(datos);
                                    if (respuesta.error) {
                                        alert(respuesta.mensaje);
                                        throw new Error(respuesta.mensaje);
                                    } else {
                                        return respuesta;
                                    }
                                }
                            })
                            .catch(err => console.log(err));
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                })
                .then((result) => {
                    console.log(result.value);
                });
                validarFechaRequerida(this.fechaInicio, 'Fecha inicio');
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
            <FilaTabla titulo="Fecha" v-model="fechaInicio" :fecha="fechaInicio"></FilaTabla>
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