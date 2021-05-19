<?php
include_once("root/comun.php");

$wemp_pmla = empty($_GET['wemp_pmla']) ? 0 : $_GET['wemp_pmla'];
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wInstitucion = $institucion->nombre;
$baseDatos = $institucion->baseDeDatos;
$wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, $institucion->baseDeDatos);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="Windows-1252">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de Servicios</title>
    <link href="../../assets/vendor/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link href="../../assets/vendor/sweetalert/css/sweetalert2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="reporteServicios/assets/css/style.css">
</head>

<body class="container">
    <div>
        <?php encabezado("Reporte de Servicios", "Mayo 07 de 2021", "Logo_idc"); ?>
    </div>
    <br>
    <div id='vueapp' class="">
        <form>

            <div class="row mb-1 justify-content-around">
                <div class="col-6 col-lg-3 my-1">
                    <label for="numHis" class="form-label">N&uacute;mero Historia</label>
                    <input type="text" class="form-control" id="numHis" placeholder="999999" v-model="numHis" v-on:keyup="bloquearCampo" :disabled="disNumHis==1" v-on:keyup.enter="filtrarServicios('<?php echo $wtabcco ?>')">
                </div>
                <div class="col-6 col-lg-3 align-self-center my-1">
                    <label for="numIde" class="form-label">Numero Identificaci&oacute;n</label>
                    <input type="text" class="form-control" id="numIde" placeholder="Identificaci&oacute;n" v-model="numIde" v-on:keyup="bloquearCampo" :disabled="disNumIde==1" v-on:keyup.enter="filtrarServicios('<?php echo $wtabcco ?>')">
                </div>
                <div class=" col-6 col-lg-3 align-self-center my-1">
                    <label for="fecIni" class="form-label">Fecha Inicial</label>
                    <input type="date" class="form-control" id="fecIni" placeholder="Fecha Inicial" v-model="fecIni">
                </div>
                <div class="col-6 col-lg-3 align-self-center my-1">
                    <label for="fecFin" class="form-label">Fecha Final</label>
                    <input type="date" class="form-control" id="fecFin" placeholder="Fecha Fin" v-model="fecFin">
                </div>
                <div class="col-6 col-lg-12 align-self-center text-center my-1">
                    <button type="button" class="btn btn-primary" @click="filtrarServicios('<?php echo $wtabcco ?>')">Filtrar</button>
                </div>
                <div class="col-6 col-lg-12 align-self-center text-center my-1">
                    <button type="button" class="btn btn-primary" @click="resetForm()">Limpiar</button>
                </div>
            </div>
        </form>
        <br>
        <div v-if="servicios.length">
            <h3>N&uacute;mero Historia: <span class="badge bg-secondary">{{ numHis }}</span>&nbsp;{{ nombre }}</h3>
            <br>
            <table class="table table-hover table-sm">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Ingreso</th>
                        <th scope="col">Servicio</th>
                        <th scope="col">Fecha Ingreso</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Facturado</th>
                        <th class="d-none" scope="col">Acci&oacute;n</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for='servicio in servicios'>

                        <th scope="row">
                            <button type="button" @click="abrirFactuacion(servicio.ingreso, servicio.servicio, '<?php echo $wemp_pmla ?>')" class="btn btn-outline-dark btn-sm">{{ servicio.ingreso }}</button>
                        </th>
                        <td>{{ servicio.servicio }}</td>
                        <td>{{ servicio.fechaIngreso }}</td>
                        <td>
                            <span class="badge rounded-pill bg-success" v-if="servicio.estado == 'on'">Activo</span>
                            <span class="badge rounded-pill bg-warning" v-else-if="servicio.estado == 'off'">Inactivo</span>
                            <span class="" v-else>&nbsp;</span>
                        </td>
                        <td></td>
                        <td class="d-none"><button class="btn btn-outline-danger btn-sm">ReAbrir</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br>
        <div class="text-center">
            <button class="btn btn-primary" @click="cerrarVentana()">Cerrar Ventana</button>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>

    <script src="../../assets/vendor/vuejs/vue.js"></script>
    <script src="../../assets/vendor/vuejs/axios.min.js"></script>
    <script src="../../assets/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script src="../../assets/vendor/sweetalert/js/sweetalert2.all.min.js"></script>
    <script type="module" src="reporteServicios/assets/js/script.js"></script>
</body>

</html>