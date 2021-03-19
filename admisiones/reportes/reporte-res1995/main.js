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
            Parametros de la consulta para generar el reporte según la resolución 1995:
            <br />
            <br />
        </span>
        <Reporte></Reporte>
    </div>`
});