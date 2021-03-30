export default {
    name: "FilaTabla",
    props: ["label", "text"],
    computed: {
        valorFecha: {
            get() {
                return this.fecha;
            },
            set(fecha) {
                this.$emit("input", fecha);
            },
        }
    },
    template: `<div class="row"><div class="col text-muted">{{ label }}</div><div class="col" v>{{ text }}</div></div>`,
}