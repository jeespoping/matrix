import FilaTabla from './FilaTabla';
import {TODAYSTRING} from '../common/dateTime';
import Empresa from '../common/Empresa';

export default {
    name: 'TablaFechas',
    data: () => {
        return {
            fechaInicio: TODAYSTRING,
            fechaFin: TODAYSTRING,
            wemp_pmla: new Empresa().obtenerIdEmpresa(),
        }
    },
    methods: {
        onSubmit: () =>{
            alert("Enviando información");
        },
        onClose: () => {
            window.close();
        }
    },
    components:{
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
