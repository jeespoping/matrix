import FilaTabla from './FilaTabla';
import {TODAYSTRING} from '../common/dateTime';
import Empresa from '../common/Empresa';

export default {
    name: 'TablaFechas',
    data: function() {
        return {
            fechaInicio: TODAYSTRING,
            fechaFin: TODAYSTRING,
            wemp_pmla: new Empresa().obtenerIdEmpresa(),
        }
    },
    methods: {
        onSubmit: function() {
            alert("Enviando información");
            fetch('../presap/controllers/Reporte1995Controller.php',{
                method: 'POST',
                headers:{
                    'Content-Type': 'application/json'
                },
                body:JSON.stringify({
                    'fechaInicio': this.fechaInicio,
                    'fechaFin': this.fechaFin,
                    'wemp_pmla': this.wemp_pmla,
                })
            })
            .then(response => {
                if(response.ok){
                    return response.text();
                }else {
                    throw "Error en la llamada";
                }
            })
            .then(text => {
                console.log(text);
            })
            .catch(err => {
                console.log(err);
            });
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
