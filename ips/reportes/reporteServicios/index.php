<?php
include_once("conex.php");
include_once("root/comun.php");

$wemp_pmla = empty($_GET['wemp_pmla']) ? 0 : $_GET['wemp_pmla'];
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wInstitucion = $institucion->nombre;
$empresa = $institucion->baseDeDatos;
$wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, $institucion->baseDeDatos);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="Windows-1252">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte de Servicios</title>
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../../assets/vendor/bootstrap/css/bootstrap.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

</head>


<body class="container">
    <div>
        <?php
        encabezado("Reporte de Servicios", "Mayo 07 de 2021", "Logo_idc");
        ?>
        <!-- <table border="0" id="principalTitleMatrix" class="w-100">
            <tbody>
                <tr>
                    <td width="10%" rowspan="3"><img src="../images/medical/root/clinica.jpg" width="120" heigth="76" alt=""></td>
                    <td width="90%" class="fila1">
                        <div class="titulopagina" align="center">
                            <div class="titulopagina2">Reporte de Servicios</div>
                        </div>
                    </td>
                    <td width="10%" rowspan="3" style="display:none"><img src="../images/medical/root/fmatrix.jpg" width="120" heigth="76" alt=""></td>
                </tr>
                <tr>
                    <td colspan="1" align="right" class="fila2"><span class="version">Versi�n: (Mayo 07 de 2021) </span></td>
                </tr>
            </tbody>
        </table> -->
    </div>
    <br>
    <div class="">
        <form>
            <div class="row mb-1 justify-content-around">
                <div class="col-6 col-lg-3 my-1">
                    <label for="numHis" class="form-label">N&uacute;mero Historia</label>
                    <input type="text" class="form-control" id="numHis" placeholder="999999">
                </div>
                <div class="col-6 col-lg-3 align-self-center my-1">
                    <label for="numIde" class="form-label">Numero Identificaci&oacute;n</label>
                    <input type="text" class="form-control" id="numIde" placeholder="Identificaci&oacute;n">
                </div>
                <div class="col-6 col-lg-3 align-self-center my-1">
                    <label for="fecIni" class="form-label">Fecha Inicial</label>
                    <input type="date" class="form-control" id="fecIni" placeholder="Fecha Inicial">
                </div>
                <div class="col-6 col-lg-3 align-self-center my-1">
                    <label for="fecFin" class="form-label">Fecha Final</label>
                    <input type="date" class="form-control" id="fecFin" placeholder="Fecha Fin">
                </div>
                <div class="col-12 col-lg-12 align-self-center text-center my-1">
                    <button type="button" class="btn btn-primary">Filtrar</button>
                </div>
            </div>
        </form>
        <br>

        <h3>N&uacute;mero Historia: <span class="badge bg-secondary">999999</span></h3>
        <br>
        <table class="table table-hover table-sm">
            <thead class="table-light">
                <tr>
                    <th scope="col">Ingreso</th>
                    <th scope="col">Servicio</th>
                    <th scope="col">Fecha Ingreso</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Estado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <th scope="row">1</th>
                    <td>URGENCIAS</td>
                    <td>2020/01/01</td>
                    <td>Activo</td>
                    <td><button class="btn btn-outline-danger btn-sm">ReAbrir</button></td>
                </tr>
                <tr>
                    <th scope="row">2</th>
                    <td>Hospitalizaci&oacute;n</td>
                    <td>2020/01/01</td>
                    <td>Activo</td>
                    <td><button class="btn btn-outline-danger btn-sm">ReAbrir</button></td>
                </tr>
                <tr>
                    <th scope="row">3</th>
                    <td>Plastica</td>
                    <td>2020/01/01</td>
                    <td>Cerrado</td>
                    <td><button class="btn btn-outline-danger btn-sm">ReAbrir</button></td>
                </tr>
                <tr>
                    <th scope="row">4</th>
                    <td>Cirugia</td>
                    <td>2020/01/01</td>
                    <td>Inactivo</td>
                    <td><button class="btn btn-outline-danger btn-sm">ReAbrir</button></td>
                </tr>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-p34f1UUtsS3wqzfto5wAAmdvj+osOnFyQFpp4Ua3gs/ZVWx6oOypYoCJhGGScy+8" crossorigin="anonymous"></script>
</body>
<!-- <body>
    <h1>Contact Management</h1>
    <div id='vueapp'>
        <div class="container">
            <table border='1' width='100%' style='border-collapse: collapse;'>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Country</th>
                    <th>City</th>
                    <th>Job</th>

                </tr>

                <tr v-for='contact in contacts'>
                    <td>{{ contact.name }}</td>
                    <td>{{ contact.email }}</td>
                    <td>{{ contact.country }}</td>
                    <td>{{ contact.city }}</td>
                    <td>{{ contact.job }}</td>
                </tr>
            </table>
            </br>

            <form class="row g-3">
                <div class="col-md-6 form-floating">
                    <input class="form-control" type="text" id="name" name="name" v-model="name" placeholder="name">
                    <label for="name">Name</label>
                </div>
                <div class="col-md-6 form-floating">
                    <input class="form-control" type="email" name="email" id="email" v-model="email" placeholder="email">
                    <label for="email">Email</label>
                </div>
                <div class="col-md-6 form-floating">
                    <input class="form-control" type="text" name="country" id="country" v-model="country" placeholder="country">
                    <label for="country">Country</label>
                </div>
                <div class="col-md-6 form-floating">
                    <input class="form-control" type="text" name="city" id="city" v-model="city" placeholder="city">
                    <label for="city">City</label>
                </div>
                <div class="col-md-8 form-floating">
                    <input class="form-control" type="text" name="job" id="job" v-model="job" placeholder="job">
                    <label for="job">Job</label>
                </div>
                <div class="col-md-4 form-floating">
                    <input class="btn btn-primary" type="button" @click="createContact()" value="Add">
                </div>
            </form>
        </div>
    </div>
    <script src="../../../assets/vendor/vuejs/vue.js"></script>
    <script src="../../../assets/vendor/vuejs/axios.min.js"></script>
    <script src="../../../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script type="module" src="assets/js/script.js"></script>
</body> -->

</html>