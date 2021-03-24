import Reporte from './containers/Reporte';

new Vue({
    el: '#app',
    data: () => {
        return {
            subtituloPagina2: 'subtituloPagina2',
        }
    },
    components: {
        Reporte,
    },
    template: `
    <div>
        <span v-bind:class="[subtituloPagina2]">
            Ingrese los parametros:
            <br />
            <br />
        </span>
        <Reporte></Reporte>
    </div>`
});