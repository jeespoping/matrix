import Tabla from '../components/Tabla';

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
    components: {
        Tabla,
    },
    template: `
    <div v-bind:style="styleObject">
        <Tabla></Tabla>
    </div>`
}