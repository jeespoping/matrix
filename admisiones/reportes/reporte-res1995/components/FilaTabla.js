export default {
    name: 'FilaTabla',
    props:['titulo','fecha'],
    computed: {
        valorFecha: {
            get() {
                return this.fecha;
            },
            set(fecha){
                this.$emit('input', fecha);
            },
        }
    },
    template: `<tr>
        <td class='fila1' width=85>{{titulo}}</td>
        <td class='fila2' align='center' width=250>
            <input type="date" 
                v-model="valorFecha"
                size=10
                maxlength=10
                class='textoNormal'/>
        </td>
    </tr>`,
}