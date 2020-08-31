<?php
/************************************************************************************************************
 * Programa     : Reporte de Articulos.
 * Fecha        : 2020-01-13
 * Por          : Ing. Camilo Zapata
 * Descripcion  : Reporte diseñado para la consulta de los artículos manejados en la institución por parte de cualquier miembro del área asistencial o administrativa.
 * Condiciones  :
 *********************************************************************************************************

 Actualizaciones:
 **********************************************************************************************************/
if(!isset($_SESSION['user'])){
    echo "error";
    return;
}
$wactualiza = "2020-01-15";
include_once("conex.php");
include_once("root/comun.php");
/* Backend */
$wbasedato     = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
$table         = "000026";
if( isset( $consultaAjax ) ){
    $tablesJoin    = array(); // Array which contains tables required to join
    $fieldsToJoin  = array(); // Array which contains fields from the previous tables required to match them up.
    $booleanFields = array(); // Array which contains fields classified as boolean to change their value to one more understandable to users
    $results       = array();
    $response      = array( 'error'=>0, 'data'=>array(), 'message'=>'' );

    $query     = "SELECT *
                    FROM det_formulario
                   WHERE medico = '{$wbasedato}'
                     AND codigo = '{$table}'";
    $rs        = mysql_query( $query, $conex );
    while( $row = mysql_fetch_assoc( $rs ) ){
        switch ( $row['tipo'] ) {
            case '18':

                $sourceField      = $row['descripcion'];
                $specification    = $row['comentarios'];
                $specification    = explode( "-", $specification );
                $fieldsAmount     = $specification[0];
                $destinationField = array();

                if( ctype_alpha( $specification[1] ) ){
                    $destinationField['table'] = $specification[1]."_".$specification[2];
                    $startPoint                = 3;
                }else{
                    $destinationField['table'] = $wbasedato."_".$specification[1];
                    $startPoint                = 2;
                }
                if( !in_array( $destinationField['table'], $tablesJoin ) ){
                    $wantedPlaces = "";
                    $mainField      = "";
                    for( $i = $startPoint; $i < $startPoint + $fieldsAmount; $i++ ){
                        if(  $i == $startPoint ){
                             $wantedPlaces = "'".$specification[$i]."'";
                             $mainField = $specification[$i];
                        }else{
                             $wantedPlaces .=  ", '".$specification[$i]."'" ;
                             $secundaryField = $specification[$i];
                        }
                    }

                    $aux  = array();
                    $aux  = askDictionary( $destinationField['table'], $wantedPlaces );
                    $fieldsToJoin[$destinationField['table']]['sourceField']    = $sourceField;
                    $fieldsToJoin[$destinationField['table']]['relatedFields']  = $aux;
                    $fieldsToJoin[$destinationField['table']]['traduction']     = array();
                    $fieldsToJoin[$destinationField['table']]['mainField']      = $aux[$mainField];
                    $fieldsToJoin[$destinationField['table']]['secundaryField'] = $aux[$secundaryField];
                    for( $i = $startPoint; $i < $startPoint + $fieldsAmount; $i++ ){

                        array_push( $fieldsToJoin[$destinationField['table']]['traduction'], $fieldsToJoin[$destinationField['table']]['relatedFields'][$specification[$i]] );
                    }
                    array_push( $tablesJoin, $destinationField['table'] );
                }
                break;
            case '10':
                array_push( $booleanFields, $row['descripcion'] );
                break;

            default:
                break;
        }

    }

    if( count( $fieldsToJoin ) > 0 ){
        foreach( $fieldsToJoin as $wantedTable => $data ){
            $tableFields = implode( ", ", $data['traduction'] );
            $query = " SELECT {$tableFields}
                         FROM {$wantedTable}";
            $rs    = mysql_query( $query, $conex )or die( "it dies here: ".$query );
            while( $rowTabla = mysql_fetch_assoc( $rs ) ){
                if( !isset($fieldsToJoin[$wantedTable]['data']) )
                    $fieldsToJoin[$wantedTable]['data'] = array();
                $fieldsToJoin[$wantedTable]['data'][$rowTabla[$data['mainField']]] = $rowTabla[$data['secundaryField']];
            }
        }
    }
    $response['data'] = consolidateResults( $fieldsToJoin, $booleanFields );
    echo json_encode( $response );
    return;
}

function askDictionary( $table, $wantedPlaces ){
    global $conex;

    $table  = explode( "_", $table );
    $fields = array();
    $query  = "SELECT *
                 FROM det_formulario
                WHERE medico = '{$table[0]}'
                  AND codigo = '{$table[1]}'
                  AND campo  in ( $wantedPlaces )";
    $rs     = mysql_query( $query, $conex );
    while( $row = mysql_fetch_assoc( $rs ) ){
        $fields[$row['campo']] = $row['descripcion'];
    }
    return( $fields );
}

function consolidateResults( $auxiliarTablesData, $booleanFields ){
    global $conex,
           $table,
           $wbasedato,
           $parametros;

    $result = array();

    $query = "SELECT *
                FROM {$wbasedato}_{$table}
               WHERE Artesm = 'on'
                 AND Artest = 'on'
                 {$parametros}";

    $rs    = mysql_query( $query, $conex );

    while( $row = mysql_fetch_assoc( $rs ) ){

        foreach ($auxiliarTablesData as $key => $value) {
            if( isset( $value['data'][$row[$value['sourceField']]] ) ){
                $row[$value['sourceField']] = $row[$value['sourceField']]."-".$value['data'][$row[$value['sourceField']]];
            }else{
                $row[$value['sourceField']] = "SIN RELACION";
            }
        }
        foreach ( $booleanFields as $keyb => $valueb ){
            $row[$valueb] = ( $row[$valueb] == "on" ) ? "SI" : "NO";

        }
        foreach( $row as $key => $value ){
            $row[$key] = utf8_encode( $row[$key] );
        }
        unset($row['Artest']);
        array_push( $result, $row );
    }

    //  var_dump( $result );
    return ($result);
}


/* Main Variables Setting */
$aux = array();
$tableHeaders = array( 0=>array( 'columnName'=>'Medico', 'usersName'=>'Medico'),
                       1=>array( 'columnName'=>'Fecha_data', 'usersName'=>'Fecha_data'),
                       2=>array( 'columnName' =>'Hora_data', 'usersName'=>'Hora_data')
                     );
$tableFilters = array();

$query = " SELECT *
             FROM det_formulario a, root_000030 b
            WHERE a.medico = '{$wbasedato}'
              AND a.codigo = '{$table}'
              AND b.Dic_Usuario = a.medico
              AND b.Dic_Formulario = a.codigo
              AND b.Dic_Campo = a.campo
              AND a.descripcion NOT like '%est'";

$rs    = mysql_query( $query, $conex );
while( $row = mysql_fetch_assoc( $rs ) ){
    $aux['columnName'] = $row['descripcion'];
    $aux['usersName']  =  utf8_encode( $row['Dic_Descripcion'] );
    array_push( $tableHeaders, $aux );
    if( $row['tipo'] == "10" ){
        array_push( $tableFilters, $aux );
    }
}

$aux['columnName'] = 'Seguridad';
$aux['usersName']  = 'Seguridad';
array_push( $tableHeaders, $aux );
$aux['columnName'] ='id';
$aux['usersName']  ='id';
array_push( $tableHeaders, $aux );

$numberRows       = ceil(count( $tableFilters )/5);
$tableHeaders     = json_encode( $tableHeaders );
$tableFiltersJson = json_encode( $tableFilters );

?>
<html lang="es">
<head>
    <title> MAESTRO DE ARTICULOS </title>
</head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <!--<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">-->
    <link rel="stylesheet" href="../../../include/root/bootstrap.min.css">
    <link rel="stylesheet" href="../../../include/gentelella/vendors/font-awesome/css/font-awesome.min.css">
    <style>
        BODY {
            font-family: verdana;
            font-size: 10pt;
            width: 100%;
        }
        .panel-results{
            width: 95%;
            height: 700px;
            overflow-x: scroll;
            overflow-y: scroll;
        }
        .panel-form{
            width: 95%;
            text-align: left;
        }
        .td_menu{
             width: 14%;
        }
        .td_menu_select{
             width: 6%;
        }
        .bgSuccess {

        }
    </style>
    <script src="../../../include/root/vue/vue.min.js"></script>
    <script src="../../../include/root/jquery.min.js"></script>
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.quicksearch.js" type="text/javascript"></script>
    <!--<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>-->
    <script src="../../../include/root/bootstrap.min.js"></script>
<body width="100%">
<?php
    encabezado("<div class='titulopagina2'>REPORTE DE ARTICULOS</div>", $wactualiza, "clinica");
?>

    <!-- Constant HTML variables -->
    <input type="hidden" v-model="headers" name="tableHeaders" id="tableHeaders" value='<?=$tableHeaders?>'>
    <input type="hidden" v-model="headers" name="tableFilters" id="tableFilters" value='<?=$tableFiltersJson?>'>
    <input type="hidden" v-model="headers" name="numberRows" id="numberRows" value='<?=$numberRows?>'>
    <input type="hidden" v-model="headers" name="wemp_pmla" id="wemp_pmla" value='<?=$wemp_pmla?>'>
    <div id='app'>
        <!-- OPTIONS FORM  -->
        <div class="container" style="width:100%;padding:0px;" align="center">
            <div class="form-inline text-left" style="width:90%; padding:0px;">
                <span class="text-primary text-left" style="pointer:pointer" v-on:click="ocultarFormulario"> ver formulario.</span>
                <br>
            </div>
            <div id="div_formulario" class="form-inline text-left" style="width:90%; padding:0px;">
                <table style="width:100%; padding:0px;" class="table small table-bordered table-condensed">
                    <?php for( $i = 1; $i <= $numberRows; $i++ ){?>
                        <tr>
                            <?php for( $j = (5*$i-5); $j < (5*$i); $j++ ){?>
                                    <?php if( isset( $tableFilters[$j] ) ){ ?>
                                        <td class="bg-warning" v-bind:class="class_<?=$tableFilters[$j]['columnName']?>">
                                            <label for="busFuente" class="wn">&nbsp;&nbsp;<?php echo $tableFilters[$j]['usersName'] ?></label>
                                        </td>
                                        <td class="bg-warning" v-bind:class="class_<?=$tableFilters[$j]['columnName']?>">
                                            <select v-model="fil_<?=$tableFilters[$j]['columnName']?>" name="<?=$tableFilters[$j]['columnName']?>" v-on:change="cambiarEstadofiltro('<?=$tableFilters[$j]['columnName']?>')" >
                                                <option value="" selected>TODO</option>
                                                <option value="on">SI</option>
                                                <option value="off">NO</option>
                                            </select>
                                        </td>

                                    <?php } ?>

                            <?php } ?>
                        </tr>
                    <?php }?>
                </table>
            </div>
            <div class="form-inline text-left" style="width:90%; padding:0px;">
                <span class="input-group-text">
                    <button type="button" id="btn_buscar" class="btn btn-primary btn-sm" v-on:click="consultarMaestro">Consultar <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span></button>
                </span>
            </div>
        </div><br>

        <!-- RESULTS TABLE SECTION -->
        <center>

        <!--<div class="input-group panel-form">
            <span class="input-group-text">Formulario Busqueda:</span>
            <input class="form-control input-sm" type="inp_searcher" name="inp_searcher" value="" placeholder="Escriba para buscar">
        </div><br>-->
        <div class="modal fade" id="cargando" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        Cargando...
                    </div>
                </div>
            </div>
        </div><!-- Cierre del modal-->
        <div class="input-group panel-form" v-if="items.length > 0">
            <!--<span class="input-group-text">Formulario Busqueda:</span>-->
            <input class="form-control input-sm" type="inp_searcher" name="inp_searcher" id="inp_searcher" value="" placeholder="Escriba para buscar">
        </div>
        <div id="div_results" class="panel-body panel panel-default panel-results" v-if="items.length > 0">
            <table class="table small table-bordered table-condensed" id="tbl_articulos">
                <thead>
                    <tr>
                        <th class="text-center bg-info" v-for=" header in headers "><span v-text="header.usersName"></span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for=" ( item, keyI ) in items" :class="{ fila1: keyI % 2 != 0, fila2: keyI % 2 == 0}" tipo='registroEncontrado'>
                        <!--<td v-for="header in headers" ><span v-text=" header.columnName "></span></td>-->
                        <td tooltip="carajo" v-for="( value, key ) in item" ><span v-text="value"></span></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            <div class="centered">
                No se encontraron resultados.
            </div>
        </div>
        </center>

    </div>
    <!-- vue js template -->
    <script>

        var filtersAux = JSON.parse($("#tableFilters").val());
        var filtersCount = filtersAux.length;
        console.log( "cantidad: "+filtersCount );
        var setData  = '{';
            setData += ' "url": "",';
            setData += ' "mensaje": "Learning Vue.js",';
            setData += ' "articulo": "",';
            setData += ' "items": [],';
            setData += ' "headers": [],';
            setData += ' "filters": [],';
            setData += ' "wemp_pmla": "'+$("#wemp_pmla").val()+'",';
            setData += ' "filas": 0,';
            setData += ' "columns": 6,';
            setData += ' "formVisible": true,';

        i = 0;
        $(filtersAux).each(function( index, value ){
            i++;
            setData += ' "fil_'+value.columnName+'": "",';
            setData += ' "class_'+value.columnName+'": {}';
            if( i < filtersCount )
                setData += ',';
        });
        setData += '}';

        setData = JSON.parse( setData );

        var app = new Vue({
            el: '#app',
            data: setData,
            mounted:function(){
                var that = this;
                this.headers   = JSON.parse($("#tableHeaders").val());
                this.filas     = JSON.parse($("#numberRows").val());
                this.filters   = filtersAux;
                this.wemp_pmla = $("#wemp_pmla").val();
            },
            methods:{
                "consultarMaestro": function(){
                    this.url = "rep_articulos.php?wemp_pmla="+$("#wemp_pmla").val()+"&consultaAjax=''";
                    var hayParametros = false;
                    var parametros    = "";
                    $(this.filters).each(function( index, value ){
                        var valorActual   = $("SELECT[name='"+value.columnName+"']").val();
                        if( valorActual != "" ){
                            hayParametros = true;
                            parametros = parametros+" AND "+value.columnName+"='"+valorActual+"'";
                        }
                    });
                    if( hayParametros )
                        this.url = this.url+"&parametros="+parametros;

                    $('#cargando').modal({
                        backdrop: 'static',
                        keyboard: false
                    });

                    that = this;
                    var data = {
                        consultaAjax: '',
                        wemp_pmla: this.wemp_pmla
                    };

                    fetch( this.url, data  )
                    .then( res => res.json() )
                    .then( data => {
                        this.items = data.data;
                        $('#cargando').modal('toggle');

                        setTimeout( function(){
                            $("#inp_searcher").quicksearch("table#tbl_articulos tbody tr[tipo='registroEncontrado']");
                        }, 200 );
                    })
                    .catch( error => console.log( error.message ) );
                },

                "ocultarFormulario": function(){
                    $("#div_formulario").toggle();
                },

                "cambiarEstadofiltro": function( name ){
                    //name = $(obj).attr("name");

                    var valor = $("select[name='"+name+"']").val();
                    if( valor == "on" ){
                        clase = {'bg-danger': true};
                    }

                    if( valor == "off" ){
                        clase = {'bg-danger': true};
                    }

                    if( valor == "" ){
                        clase = {'bg-warning': true};
                    }

                    this["class_"+name] = clase;
                }
            }
        });
    </script>
</body>
</html>