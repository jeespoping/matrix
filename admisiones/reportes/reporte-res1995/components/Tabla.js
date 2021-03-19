
export default {
    name: 'Reporte',
    data: function () {
        return {
            styleObject: {
                color: 'red',
                fontSize: '13px'
            }
        }
    },
    template: `
        <table align='center' border=0 width=402>
            <tr>
                <td class='fila1' width=85>Fecha inicial</td>
                <td class='fila2' align='center' width=250>
                <input type="text" name='wfec_i' size=10 maxlength=10  id='wfec_i' readonly='readonly' value="" class='textoNormal'>
                </td>
            </tr>
            <tr>
                <td class='fila1' width=85>Fecha final</td>
                <td class='fila2' align='center' width=250>
                <input type="text" name='wfec_i' size=10 maxlength=10  id='wfec_i' readonly='readonly' value="" class='textoNormal'>
                </td>
            </tr>
        </table>
    `
}