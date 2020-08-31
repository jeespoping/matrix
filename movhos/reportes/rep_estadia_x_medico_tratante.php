<?php
include_once("conex.php");
if(!isset($_POST['accion']))
{
    echo '<!DOCTYPE html>';
}
/**
 PROGRAMA       : rep_estadia_x_medico_tratante.php
 AUTOR          : Juan Felipe Balcero
 FECHA CREACION : 11 de septiembre 2018

 DESCRIPCION: Reporte del promedio de estadía de pacientes por médico tratante.

 Notas:
 --
*/ $wactualiza = "Septiembre 20 de 2018"; /*
* ACTUALIZACIONES:
* 
*/
$consultaAjax='';
$fecha_actual = date("Y-m-d");
$hora_actual  = date("H:i:s");






if(isset($accion) && !array_key_exists('user',$_SESSION))
{
    $msjss = "Recargue o inicie sesión nuevamente en la página principal de Matrix para que pueda seguir utilizando este programa normalmente.";
    $data = array('error'=>1,'mensaje'=>$msjss,'html'=>'');
    echo json_encode($data);
    return;
}
elseif(!isset($accion) && !array_key_exists('user',$_SESSION))
{
    echo '  <br /><br /><br /><br />
            <div style="color: #676767;font-family: verdana;background-color: #E4E4E4; text-align:center;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
$key = substr($user,2,strlen($user));

// Funciones

function consultarNombresMedicos($conex, $wbasedato)
{
    $arr_nombres = array();

    $select = " SELECT Medtdo, Meddoc, Medno1, Medno2, Medap1, Medap2, Espnom
                FROM {$wbasedato}_000048, {$wbasedato}_000044
                WHERE Medesp = Espcod; ";

    if($result = mysql_query($select, $conex))
    {
        while($row = mysql_fetch_assoc($result))
        {
            $arr_nombres[trim($row['Meddoc'])]['nombre'] = utf8_encode($row['Medno1'].' '.$row['Medno2'].' '.$row['Medap1'].' '.$row['Medap2']);
            $arr_nombres[trim($row['Meddoc'])]['especialidad'] = utf8_encode($row['Espnom']);
        }
    }
    else
    {
        $arr_nombres = 'Algo salió mal';   
    }

    return $arr_nombres;
}

function consultaPrincipalReporte($conex, $wbasedato, $wbasedatocli, $fechaI, $fechaF)
{
    $arr_medicos_tratantes = array();
    $arr_medicos_tratantesAUX = array();

    $select = " SELECT Egrhis, Egring, Egrdxi, Descripcion, Egrest, Egrmee, Pacno1, Pacno2, Pacap1, Pacap2 
                FROM {$wbasedatocli}_000108 cli108, {$wbasedatocli}_000112, {$wbasedato}_000011, root_000011, {$wbasedatocli}_000100
                WHERE Egrhis = Serhis
                AND Egring = Sering
                AND Sercod = Ccocod
                AND Egrdxi = Codigo
                AND Egrhis = Pachis
                AND Seregr = 'on'
                AND Ccohos = 'on'
                AND cli108.Fecha_data BETWEEN '{$fechaI}' AND '{$fechaF}'; ";
    
    if($result = mysql_query($select, $conex))
    {
        while($row = mysql_fetch_assoc($result))
        {
            $paciente['historia'] = $row['Egrhis'];
            $paciente['ingreso'] = $row['Egring'];
            $paciente['dias'] = $row['Egrest'];
            $paciente['nombre'] = utf8_encode($row['Pacno1'].' '.$row['Pacno2'].' '.$row['Pacap1'].' '.$row['Pacap2']);

            $arr_medicos_tratantesAUX[trim($row['Egrmee'])]['diagnosticos'][$row['Egrdxi']]['diagnostico'] = $row['Descripcion'];
            $arr_medicos_tratantesAUX[trim($row['Egrmee'])]['diagnosticos'][$row['Egrdxi']]['pacientes'][] = $paciente;
            $arr_medicos_tratantesAUX[trim($row['Egrmee'])]['diagnosticos'][$row['Egrdxi']]['suma'] += $row['Egrest'];
            $arr_medicos_tratantesAUX[trim($row['Egrmee'])]['diagnosticos'][$row['Egrdxi']]['numero'] += 1;
            $arr_medicos_tratantesAUX[trim($row['Egrmee'])]['suma'] += $row['Egrest'];
            $arr_medicos_tratantesAUX[trim($row['Egrmee'])]['numero'] += 1;
        }

        foreach ($arr_medicos_tratantesAUX as $key => $value) {
            $valores['key'] = (string)$key;
            $valores['numero'] = $value['numero'];
            $valores['promedio'] = $value['suma'] / $value['numero'];
            $valores['diagnosticos'] = $value['diagnosticos'];
            $arr_medicos_tratantes[] = $valores;
        }
    }
    else
    {
        $arr_medicos_tratantes = $select;   
    }

    return $arr_medicos_tratantes;
}

// Llamados AJAX
if(isset($accion))
{
    $data = array('error'=>0,'mensaje'=>'','html'=>'','sql'=>'', 'arr_ajx_opciones'=>array());
    $no_exec_sub = 'No se ejecutó ningúna rutina interna del programa';

    // Cada caso representa una acción iniciada desde el front del programa.

    switch ($accion) 
    {
        case 'generarReporte':
            $data['respuesta'] = consultaPrincipalReporte($conex, $wbasedato, $wbasedatocli, $fechaInicial, $fechaFinal);
            break;

        case 'consultarNombresMedicos':
            $data['respuesta'] = consultarNombresMedicos($conex, $wbasedato);
            $data['fechahoy'] = date("Y-m-d");
            break;

        default:
            $data['mensaje'] = $no_exec_sub;
            $data['error'] = 1;
            break;
    }
    echo json_encode($data);
    return;
}

include_once("root/comun.php");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$wbasedatocli = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');

?>

<html lang="es-ES">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>REPORTE ESTANCIA POR MEDICO TRATANTE</title>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />  
    <link rel="stylesheet" href="../../../include/root/bootstrap.min.css">
    <link rel="stylesheet" href="../../../include/gentelella/vendors/font-awesome/css/font-awesome.min.css">   
    <style>
    .formulario {
        text-align: center;
    }
    .formulario__input {
        padding: .375rem .75rem;
        font-size: 13px;
        line-height: 1.5;
        color: #495057;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        border-radius: .25rem;
        transition: border-color .15s ease-in-out,box-shadow .2s ease-in-out;
        margin-right: 0;
    }
    .formulario__input:focus {
        border-color: rgb(128, 189, 255);
        box-shadow: rgba(0, 123, 255, 0.25) 0px 0px 0px 2px;
    }
    .formulario__submit {
        margin-left: 10px;
    }
    .contenedor {
        width: 60%;
        margin: 10px auto;
    }
    .buscador {
        margin-bottom: 10px;
    }
    .tabla-principal {       
        width: 100%;
        margin-bottom: 20px;
    }
    .tabla__encabezado {
        background-color: #2A5DB0;
        color: #FFFFFF;
        font-size: 11pt;
        font-weight: bold;
        text-align: center;
    }
    .tabla__encabezado > th {
        padding: 5px;
        text-align: center;
        border: #ffffff 2px solid;
        font-family: verdana;
        font-size: 10pt;
    }
    .tabla-principal__encabezado__medico {
        width: 40%;
    }
    .tabla-principal__encabezado__nDocumento {
        width: 25%;
    }
    .tabla-principal__encabezado__pacientes {
        width: 15%;
    }
    .tabla-principal__encabezado__promedio {
        width: 20%;
    }

    .tabla-principal__fila {
        font-size: 10pt;
        height: 19px;
        text-transform: uppercase;
        color: #000000;
    }
    .tabla-principal__fila > td {
        padding: 4px;        
        border: #ffffff 2px solid;
    }
    .tabla-principal__fila--1 {
        background-color: #C3D9FF;
    }
    .tabla-principal__fila--2 {
        background-color: #E8EEF7;
    }

    .boton-desplegar {
        width: 15px;
        height: 15px;
        cursor: pointer;
        margin-right: 5px;
    }
    .indentado {
        width: 4%;
    }
    .indentadoDiag {
        width: 10%;
    }
    .contenido-desplegar {
        width: 96%;
        padding: 15px 0;
    }
    .contenido-desplegarDiag {
        width: 90%;
        padding: 15px 0;
    }
    .tabla-pacientes {
        margin-right: 0;
        margin-left: auto;
    }
    .fade-enter-active, .fade-leave-active {
        /* transform-origin: top;
        transform: scaleY(1); */
        transition: opacity .3s ease;
    }
    .fade-enter, .fade-leave-to /* .fade-leave-active below version 2.1.8 */ {
        /* transform: scaleY(0); */
        opacity: 0;
    }
    .centered {
        text-align: center;
    }
    .isDisabled {
        opacity: 0.5;
        text-decoration: none;
        pointer-events: none;
        cursor: not-allowed;
    }

    .contador {
        padding-top: 7px;
        font-weight: bold;
    }

    .fila1                           
    {
        background-color: #C3D9FF;
        color: #000000;
        font-size: 10pt;
    }
    .fila2                               
    {
        background-color: #E8EEF7;
        color: #000000;
        font-size: 10pt;
    }
    .fila3                               
    {
        background-color: #ffffcc;
        color: #000000;
        font-size: 10pt;
    }
    .label
    {
        color: black;
    }
    td.container > div {
        width: 100%;
        height: 100%;
    }
    td.container {
        height: 80px;
    }
    .titulopagina2
    {
        border-bottom-width: 1px;
        /*border-color: <?=$bordemenu?>;*/
        border-left-width: 1px;
        border-top-width: 1px;
        font-family: verdana;
        font-size: 18pt;
        font-weight: bold;
        height: 30px;
        margin: 2pt;
        overflow: hidden;
        text-transform: uppercase;
    }
    table#principalTitleMatrix td.fila2
    {
        text-align: right !Important;
        padding-right: 10px !Important;
    }
    table#principalTitleMatrix td.fila2 span
    {
        font-size: xx-small !Important;
    }
    .col-sm{
        display: inline-block;
        margin-left: 12px;
    }
    .has-error .form-control{
        background-color: #f2dede;
        color: #a94442;
    }
    .label {
        color: #000!Important;
    }
    
    </style>

    <script src="../../../include/root/vue/vue.min.js"></script>
    <script src="../../../include/root/jquery.min.js"></script>
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script src="../../../include/root/bootstrap.min.js"></script>   
    
</head>
<body>
<?php
    encabezado("<div class='titulopagina2'>ESTANCIA POR MÉDICO TRATANTE</div>", $wactualiza, "clinica");
?>
    <input type="hidden" id="wbasedato" value="<?php echo $wbasedato ?>">
    <input type="hidden" id="wbasedatocli" value="<?php echo $wbasedatocli ?>">
    <input type="hidden" id="wemp_pmla" value="<?php echo $wemp_pmla ?>">
    
    <div id="app">
        <div class="container">
            <div class="well formulario">
                <div class="row">
                    
                    <label class="label label-sm">Fecha inicial de egreso</label>
                    <input v-model="fechaInicial" placeholder="Fecha inicial" id="fechaInicial" class="formulario__input" readonly>
                    
                    <label class="label label-sm">Fecha final de egreso</label>
                    <input v-model="fechaFinal" placeholder="Fecha final" id="fechaFinal" class="formulario__input" readonly>

                    <button class="btn btn-primary formulario__submit" v-on:click="generarInforme" v-bind:disabled="fechaInicial === ''">Generar</button>
                </div>
            </div>
        </div>
        <div v-if="medicosTratantes.length != 0">
            <div class="contenedor">
                <div class="row buscador">
                    <div class="col-md-3 contador">
                        Total Pacientes: {{ totalPacientes }}
                    </div>
                    <div class="col-md-4 contador">
                        Promedio estancia: {{ estanciaPromedio | dosDecimales }} días
                    </div>
                    <div class="col-md-5">
                        <div class="input-group">
                            <span class="input-group-addon"><i class="fa fa-search"></i></span>
                            <input v-model="busqueda" placeholder="Buscar médico" id="buscar" class="form-control">
                        </div>
                    </div>
                </div>
                <template v-if="medicos_a_mostrar.length != 0">
                    <table class="tabla-principal">
                        <thead>
                            <tr class="tabla__encabezado">
                                <th class="tabla-principal__encabezado__medico" colspan="2">Médico</th>
                                <th class="tabla-principal__encabezado__nDocumento">Especialidad</th>
                                <th class="tabla-principal__encabezado__pacientes">Número de pacientes</th>
                                <th class="tabla-principal__encabezado__promedio">Estancia promedio (Días)</th>
                            </tr>
                        </thead>
                        <template v-for="(medico, index) in medicos_a_mostrar">
                            <medico v-bind:medico="medico" v-bind:nombre="nombresMedicos[medico.key]" v-bind:index="index"></medico>
                        </template>
                    </table>
                    <div class="centered">
                        <paginacion v-bind:paginacion="paginacion"></paginacion>
                    </div>
                </template>
                <template v-else>
                    <div class="centered">
                        No se encontraron resultados.
                    </div>
                </template>
            </div>
        </div>
        <div class="centered" v-if="resultados == 0">
            No se encontraron resultados.
        </div>
    </div>

    <div class="modal fade" id="cargando" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">                        
                <div class="modal-body">
                    Cargando...
                </div>
            </div>
        </div>
    </div><!-- Cierre del modal-->

    <script type="text/x-template" id="medico-template">
        <tbody>
            <tr :class="{ fila1: index % 2 != 0, fila2: index % 2 == 0}" class="tabla-principal__fila">
                <td colspan="2"><img v-bind:src="urlImagen" class="boton-desplegar" v-on:click="desplegar">{{ nombre.nombre }}</td>
                <td>{{ nombre.especialidad }}</td>
                <td class="text-center">{{ total }}</td>
                <td class="text-center">{{ promedio | dosDecimales }}</td>
            </tr>
            <transition name="fade">  
                <tr v-if="desplegado">
                    <td class="indentado"></td>
                    <td class="contenido-desplegar" colspan="4">
                        <table class="tabla-pacientes">
                            <thead class="tabla__encabezado">
                                <th colspan="2" width="55%">Diagnóstico</th>
                                <th width="20%">Número de pacientes</th>
                                <th width="20%">Estancia promedio (Días)</th>
                            </thead>
                            <template v-for="(value, key, index) in diagnosticos">
                                <diagnostico v-bind:diagnostico="value" v-bind:index="index" v-bind:codigo="key"></diagnostico>
                            </template>
                        </table>
                    </td>                    
                </tr>
            </transition>
        </tbody>
    </script>

    <script type="text/x-template" id="diagnostico-template">
        <tbody>
            <tr class="tabla-principal__fila fila3">
                <td colspan="2"><img v-bind:src="urlImagen" class="boton-desplegar" v-on:click="expandir">{{ codigoDiag + '-' + descripcion }}</td>
                <td class="text-center">{{ total }}</td>
                <td class="text-center">{{ promedio | dosDecimales }}</td>
            </tr>
            <transition name="fade">  
                <tr v-if="expandido">
                    <td class="indentadoDiag"></td>
                    <td class="contenido-desplegarDiag" colspan="3">
                        <table class="tabla-pacientes">
                            <thead class="tabla__encabezado">
                                <th width="15%">Historia e ingreso</th>
                                <th width="50%">Paciente</th>
                                <th width="15%">Estancia (Días)</th>
                            </thead>
                            <tbody>
                                <tr v-for="(paciente, index) in pacientes" :class="{ fila1: index % 2 != 0, fila2: index % 2 == 0}" class="tabla-principal__fila">
                                    <td class="text-center">{{ paciente.historia + '-' + paciente.ingreso }}</td>
                                    <td>{{ paciente.nombre }}</td>
                                    <td class="text-center">{{ paciente.dias }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>                    
                </tr>
            </transition>
        </tbody>
    </script>

    <script type="text/x-template" id="paginacion-template">
        <nav aria-label="Page navigation">
            <ul class="pagination">
                <li>
                <a href="#" aria-label="Previous" :class="{isDisabled : noPrev}" v-on:click.prevent="paginacion.pagina -= 1">
                    <span aria-hidden="true">&laquo;</span>
                </a>
                </li>
                <li v-for="link in linksPaginacion" :class="{ active : link.activo }">
                    <a href="#" v-if="link.valor != '...'" v-on:click.prevent="paginacion.pagina = link.valor">{{ link.valor }}</a>
                    <a href="#" v-if="link.valor == '...'" v-on:click.prevent="" disabled>{{ link.valor }}</a>
                </li>
                <li>
                <a href="#" aria-label="Next" :class="{isDisabled : noNext}" v-on:click.prevent="paginacion.pagina += 1">
                    <span aria-hidden="true">&raquo;</span>
                </a>
                </li>
            </ul>
        </nav>
    </script>

    <script>
        var diagnostico = {
            data: function() {
                return {
                    codigoDiag: this.codigo,
                    descripcion: this.diagnostico.diagnostico,
                    total: this.diagnostico.numero,
                    pacientes: this.diagnostico.pacientes,
                    suma: this.diagnostico.suma,
                    expandido: false
                }
            },
            computed: {
                promedio() {
                    return this.suma / this.total;
                },
                urlImagen() {
                    if(this.expandido) {
                        return '../../images/medical/sgc/menos.png';
                    }
                    else {
                        return '../../images/medical/sgc/mas.png';
                    }
                }
            },
            props: ['diagnostico', 'index', 'codigo'],
            methods: {
                expandir: function () {
                    (this.expandido) ? this.expandido = false : this.expandido = true;
                }
            },
            filters: {
                dosDecimales: function(valor) {
                    return Number(valor).toFixed(2);
                }
            },
            template: '#diagnostico-template'
        }

        Vue.component('paginacion', {
            data: function() {
                return {    
                    linksPaginacion: [],
                    longitudPaginacion: 10
                }
            },
            created: function () {
                this.makeLinks();
            },
            computed: {
                noPrev() {
                    if (this.paginacion.pagina == 1) {
                        return true;
                    } else {
                        return false;
                    }
                },
                noNext() {
                    if (this.paginacion.pagina == this.paginacion.paginas) {
                        return true;
                    } else {
                        return false;
                    }
                },
                paginaActual() {
                    return this.paginacion.pagina;
                }
            },
            watch: {
                paginaActual: function() {
                    this.makeLinks();
                },
                $props: {
                    handler() {
                        this.makeLinks();
                    },
                    deep: true,
                    immediate: true
                }
            },
            props: ['paginacion'],
            methods: {
                makeLinks: function() {
                    let arr_temp = [];
                    if(this.paginacion.paginas <= this.longitudPaginacion) {
                        for(var i = 0; i < this.paginacion.paginas; i++) {                            
                            let linkPagina = {
                                valor: i + 1,
                                activo: false
                            }
                            if(this.paginacion.pagina == linkPagina.valor) {
                                linkPagina.activo = true;
                            }
                            arr_temp.push(linkPagina);
                        }
                    } else if (this.paginacion.paginas > this.longitudPaginacion && this.paginacion.pagina <= 6) {
                        for(var i = 0; i < this.longitudPaginacion - 2; i++) {                            
                            let linkPagina = {
                                valor: i + 1,
                                activo: false
                            }
                            if(this.paginacion.pagina == linkPagina.valor) {
                                linkPagina.activo = true;
                            }
                            arr_temp.push(linkPagina);
                        }
                        let puntos = {
                            valor: '...',
                            active: false
                        }
                        let ultimaPagina = {
                            valor: this.paginacion.paginas,
                            activo: false
                        }
                        arr_temp.push(puntos);
                        arr_temp.push(ultimaPagina);
                    }
                    else if (this.paginacion.paginas > this.longitudPaginacion && this.paginacion.pagina >= this.paginacion.paginas - 4) {

                        let puntos = {
                            valor: '...',
                            active: false
                        }
                        let primeraPagina = {
                            valor: 1,
                            activo: false
                        }
                        arr_temp.push(primeraPagina);
                        arr_temp.push(puntos);

                        for(var i = this.paginacion.paginas - this.longitudPaginacion + 2; i < this.paginacion.paginas; i++) {                            
                            let linkPagina = {
                                valor: i + 1,
                                activo: false
                            }
                            if(this.paginacion.pagina == linkPagina.valor) {
                                linkPagina.activo = true;
                            }
                            arr_temp.push(linkPagina);
                        }
                    }
                    else if (this.paginacion.paginas > this.longitudPaginacion && this.paginacion.pagina > 6 && this.paginacion.pagina < this.paginacion.paginas - 4) {

                        let puntos = {
                            valor: '...',
                            active: false
                        }
                        let primeraPagina = {
                            valor: 1,
                            activo: false
                        }
                        arr_temp.push(primeraPagina);
                        arr_temp.push(puntos);

                        for(var i = this.paginacion.pagina - 4; i < this.paginacion.pagina + 2; i++) {                            
                            let linkPagina = {
                                valor: i + 1,
                                activo: false
                            }
                            if(this.paginacion.pagina == linkPagina.valor) {
                                linkPagina.activo = true;
                            }
                            arr_temp.push(linkPagina);
                        }
                        let ultimaPagina = {
                            valor: this.paginacion.paginas,
                            activo: false
                        }
                        arr_temp.push(puntos);
                        arr_temp.push(ultimaPagina);
                    }
                    this.linksPaginacion = arr_temp;
                }
            },
            template: '#paginacion-template'
        });

        Vue.component('medico', {
            data: function() {
                return {
                    documento: this.medico.key,
                    total: this.medico.numero,
                    promedio: this.medico.promedio,
                    diagnosticos: this.medico.diagnosticos,
                    desplegado: false
                }
            },
            computed: {
                urlImagen() {
                    if(this.desplegado) {
                        return '../../images/medical/sgc/menos.png';
                    }
                    else {
                        return '../../images/medical/sgc/mas.png';
                    }
                }
            },
            watch: {
                medico: function() {
                    this.documento = this.medico.key,
                    this.promedio = this.medico.promedio,
                    this.diagnosticos = this.medico.diagnosticos,
                    this.total = this.medico.numero,
                    this.desplegado = false
                }
            },
            props: ['medico', 'nombre', 'index'],
            methods: {
                desplegar: function () {
                    (this.desplegado) ? this.desplegado = false : this.desplegado = true;
                }
            },
            filters: {
                dosDecimales: function(valor) {
                    return Number(valor).toFixed(2);
                }
            },
            components: {
                'diagnostico': diagnostico
            },
            template: '#medico-template'
        });
        
        var app = new Vue({
            el: '#app',
            data: {
                wbasedato: $('#wbasedato').val(),
                wbasedatocli: $('#wbasedatocli').val(),
                wemp_pmla: $('#wemp_pmla').val(),
                url: 'rep_estadia_x_medico_tratante.php',
                fechaInicial: '',
                fechaFinal: '',
                resultados: 1,
                medicosTratantes: [],
                nombresMedicos: null,
                paginacion: {
                    medicosPorPagina: 20,
                    pagina: 1,
                    paginas: 0
                },
                busqueda: ''
            },
            computed: {                
                sorted_medicos() {
                    return this.medicosTratantes.sort((a, b) => { return b.promedio - a.promedio; });
                },
                filtered_medicos() {
                    if (this.busqueda != '') {
                        that = this;
                        return that.sorted_medicos.filter(function (medico) {
                            return (medico.key.includes(that.busqueda) || that.nombresMedicos[medico.key].nombre.toLowerCase().includes(that.busqueda.toLowerCase()) || that.nombresMedicos[medico.key].especialidad.toLowerCase().includes(that.busqueda.toLowerCase()));
                        });
                    }
                    else {
                        return this.sorted_medicos;
                    }
                },
                medicos_a_mostrar() {
                    let pagina_paginacion = this.paginacion.pagina - 1;
                    return this.filtered_medicos.slice(pagina_paginacion * this.paginacion.medicosPorPagina, (pagina_paginacion + 1) * this.paginacion.medicosPorPagina);
                },
                totalPacientes() {
                    var suma = 0;
                    this.medicosTratantes.forEach(medico => {
                        suma += medico.numero;
                    });
                    return suma;
                },
                estanciaPromedio() {
                    var numerador = 0;
                    var denominador = 0;
                    this.medicosTratantes.forEach(medico => {
                        numerador += (medico.numero * medico.promedio);
                        denominador += medico.numero;
                    });
                    return numerador / denominador;
                }
            },
            watch: {
                filtered_medicos: function() {
                    this.paginacion.paginas = Math.ceil(this.filtered_medicos.length / this.paginacion.medicosPorPagina);
                    this.paginacion.pagina = 1;
                }
            },
            filters: {
                dosDecimales: function(valor) {
                    return Number(valor).toFixed(2);
                }
            },
            methods: {
                generarInforme: function() {
                    var body = {
                        wbasedato: this.wbasedato,
                        wbasedatocli: this.wbasedatocli,
                        wemp_pmla: this.wemp_pmla,
                        accion: 'generarReporte',
                        fechaInicial: this.fechaInicial,
                        fechaFinal: this.fechaFinal
                    }
                    
                    that = this;

                    $('#cargando').modal({
                        backdrop: 'static',
                        keyboard: false
                    });

                    $.post(this.url, body,
                        function(data){                            
                            that.medicosTratantes = data.respuesta;
                            (data.respuesta == '') ? that.resultados = 0 : that.resultados = 1;
                            $('#cargando').modal('toggle');
                            return data;
                    }, 'json');
                    this.paginacion.pagina = 1;
                },
                consultarNombresMedicos: function() {
                    var body = {
                        wbasedato: this.wbasedato,
                        wemp_pmla: this.wemp_pmla,
                        accion: 'consultarNombresMedicos'
                    }

                    that = this;

                    $.post(this.url, body,
                        function(data){                            
                            that.nombresMedicos = data.respuesta;
                            that.fechaInicial = data.fechahoy;
                            that.fechaFinal = data.fechahoy;
                            return data;
                    }, 'json');
                }
            },
            mounted: function() {
                this.consultarNombresMedicos();
                
                that = this;
                $("#fechaInicial,#fechaFinal").datepicker({
                    closeText: 'Cerrar',
                    prevText: 'Antes',
                    nextText: 'Despues',
                    monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                    'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
                    monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
                    'Jul','Ago','Sep','Oct','Nov','Dic'],
                    dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
                    dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
                    dayNamesMin: ['D','L','M','M','J','V','S'],
                    weekHeader: 'Sem.',
                    dateFormat: 'yy-mm-dd',
                    yearSuffix: '',
                    showOn: "button",
                    buttonText: '<i class="fa fa-calendar"></i>',
                    buttonImageOnly: false,
                    changeMonth: true,
                    changeYear: true,
                    // maxDate:fechaActual
                }).on(
                    "change", function() {
                        that.fechaInicial = $('#fechaInicial').val();
                        that.fechaFinal = $('#fechaFinal').val();
                    }
                );
            }
        });
    </script>
</body>
</html>