var today = new Date();
var dd = String(today.getDate()).padStart(2, '0');
var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
var yyyy = today.getFullYear();

export default {
    name: 'TablaFechas',
    data: function() {
        return {
            fechaInicio:`${yyyy}-${mm}-${dd}`,
            fechaFin:`${yyyy}-${mm}-${dd}`,
        }
    },
    template: `
    <form>
        <table align='center' border=0 width=402>
            <tr>
                <td class='fila1' width=85>Fecha inicial</td>
                <td class='fila2' align='center' width=250>
                    <input v-model="fechaInicio" type="date" 
                        size=10 
                        maxlength=10  
                        class='textoNormal'/>
                </td>
            </tr>
            <tr>
                <td class='fila1' width=85>Fecha final:</td>
                <td class='fila2' align='center' width=250>
                <input v-model="fechaFin" type="date" 
                        name='wfec_i' 
                        size=10 
                        maxlength=10  
                        class='textoNormal'/>
                </td>
            </tr>
        </table>
    </form>
    `
}