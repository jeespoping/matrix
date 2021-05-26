var app = new Vue({
    el: '#vueapp',
    data: {
        baseDatos: '',
        numHis: '',
        numIde: '',
        fecIni: '',
        fecFin: '',
        servicios: [],
        numeroHistoria: 0,
        numeroDocumento: '',
        documento: '',
        nombre: '',
        disNumHis: 0,
        disNumIde: 0
    },
    mounted: () => console.log('Reporte de Servicios'),
    methods: {
        abrirFactuacion: (ingreso, servicio, wemp_pmla) => {
            Swal.fire({
                title: `Usar el ingreso ${ingreso} del servicio ${servicio}`,
                text: 'Si la información es correcta por favor continue.',
                icon: 'question',
                showCancelButton: true,
                // confirmButtonColor: '#3085d6',
                // cancelButtonColor: '#d33',
                confirmButtonText: 'Continuar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open(`/presap/matrix/gesapl/procesos/gestor_aplicaciones.php?wemp_pmla=${wemp_pmla}&wtema=IPSERP&wing=${ingreso}&whistoria=${this.numHis}, '', 'fullscreen = no, status = no, menubar = no, toolbar = no, directories = no, resizable = yes, scrollbars = yes, titlebar = yes`);
                    window.close();
                }
            })
        },
        filtrarServicios: (baseDatos) => {
            if (this.numHis == "" && this.numIde == "") {
                Swal.fire({
                    icon: 'error',
                    title: 'Algo salio mal!',
                    text: 'Debes ingresar el numero de Historia o Documento',
                    footer: 'Los campos Fecha inicial y Fecha final son opcionales.'
                });
                return;
            }
            let formData = new FormData();
            formData.append('baseDatos', baseDatos)
            formData.append('numHis', this.numHis)
            formData.append('numIde', this.numIde)
            formData.append('fecIni', this.fecIni)
            formData.append('fecFin', this.fecFin)

            var servicio = {};
            formData.forEach(function (value, key) { servicio[key] = value; });


            Swal.fire({
                title: 'Consultando datos del paciente!',
                timer: 2000,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                    getPaciente(formData);
                    getServicios(formData);
                }
            })
        },
        bloquearCampo: () => {
            this.disNumIde = 0;
            this.disNumHis = 0;
            this.nombre = "";
            if (this.numHis != "" && this.numIde == "") {
                this.disNumIde = 1;
                this.disNumHis = 0;
            } else if (this.numHis == "" && this.numIde != "") {
                this.disNumIde = 0;
                this.disNumHis = 1;
            }
        },
        resetForm: () => {
            this.numHis = '';
            this.numIde = '';
            this.fecIni = '';
            this.fecFin = '';
            this.disNumHis = 0;
            this.disNumIde = 0;
            this.servicios = [];
        },
        cerrarVentana: () => window.close()
    }

});

const getPaciente = (formData) => {
    axios({
        method: 'post',
        url: 'reporteServicios/api/paciente.php',
        data: formData,
        config: { headers: { 'Content-Type': 'multipart/form-data' } }
    }).then((response) => {
        let data = response.data[0];
        app.numHis = data.numeroHistoria;
        app.tipoDocumento = data.tipoDocumento;
        app.numIde = data.documento;
        app.nombre = data.nombre;
    }).catch((error) => {
        Swal.insertQueueStep({
            icon: 'error',
            title: error
        })
    })
}

const getServicios = (formData) => {
    axios({
        method: 'post',
        url: 'reporteServicios/api/servicios.php',
        data: formData,
        config: { headers: { 'Content-Type': 'multipart/form-data' } }
    }).then((response) => {
        //handle success
        if (response.data) {
            app.servicios = response.data;
            console.log(response.data.estado);
        } else {
            app.servicios = null;
            Swal.fire({
                title: 'No se encuentran datos del paciente!',
                text: 'Verifique la información e intente nuevamente!',
                icon: 'error',
                didOpen: () => { app.resetForm() }
            });
        }
    }).catch((error) => {
        Swal.insertQueueStep({
            icon: 'error',
            title: error
        })
    })
}
