<?php
/*************************************************************************************************************************************************************************
 * Programa     : Reporte de Articulos.
 * Fecha        : 2020-01-13
 * Por          : Ing. Camilo Zapata
 * Descripcion  : Reporte diseñado para la consulta de los artículos manejados en la institución por parte de cualquier miembro del área asistencial o administrativa.
 * Condiciones  :
 *************************************************************************************************************************************************************************

 Actualizaciones:
 Abril 23 de 2020			Edwin MG	- Se muestra todas las familias ATC del articulo
 Febrero 20 de 2020			Edwin MG	- Se cambia el texto del boton Ver formulario por ver filtros/ ocultar filtros según el caso
										- No se muestra los datos de medico, fecha_data, hora_data, Seguridad ni id
										- Se cambia el titulo del reporte por LISTADO BASICO DE MEDICAMENTOS
										- Encabezado y columnas fijas de la tabla de resultados de los articulos
 *************************************************************************************************************************************************************************/
if(!isset($_SESSION['user'])){
    echo "error";
    return;
}
$wactualiza = "2020-04-23";
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
	
	$no_mostrar_campos = [ 'Medico', 'Fecha_data', 'Hora_data', 'Seguridad', 'id', 'Artest' ];

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
		
		foreach( $no_mostrar_campos as $key => $value ){
			unset( $row[$value] );
        }
		
		/********************************************************************
		 * FAMILIA ATC
		 ********************************************************************/
		$famAtc = '';
		//Consulto los principios activos pertenecientes a la familia
		$sql = "SELECT Atccod, Atcdes
					FROM {$wbasedato}_000280 
				   WHERE Atccod IN ( '".implode( "','", explode( ",", $row['Artfat'] ) )."' )
					 AND Atcest = 'on'
				";
		
		$boolAtc = false;
		$resAtc = mysql_query($sql, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
		while($rowsAtc = mysql_fetch_array($resAtc))
		{	
			$famAtc .= "<div class='".( $boolAtc ? 'famAtcUno' : 'famAtcDos' )."'>".$rowsAtc['Atccod']."-".utf8_encode( ($rowsAtc['Atcdes']) )."</div>";
			
			$boolAtc != $boolAtc;
		}
		
		$row['Artfat'] = empty( $famAtc ) ? 'SIN RELACION' : $famAtc;
		/*********************************************************************/
        
		//unset($row['Artest']);
		
        array_push( $result, $row );
    }

    //  var_dump( $result );
    return ($result);
}


/* Main Variables Setting */
$aux = array();
// $tableHeaders = array( 0=>array( 'columnName'=>'Medico', 'usersName'=>'Medico'),
                       // 1=>array( 'columnName'=>'Fecha_data', 'usersName'=>'Fecha_data'),
                       // 2=>array( 'columnName' =>'Hora_data', 'usersName'=>'Hora_data')
                     // );
$tableHeaders = [];
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

// $aux['columnName'] = 'Seguridad';
// $aux['usersName']  = 'Seguridad';
// array_push( $tableHeaders, $aux );
// $aux['columnName'] ='id';
// $aux['usersName']  ='id';
// array_push( $tableHeaders, $aux );

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
			height: auto;
        }
        .panel-results{
            width: 95%;
            height: 680px;
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
		
		
		
		
		.table-header-fixed{
			table-layout: fixed;
			width: auto;
		}
		
		

		.table-header-fixed thead td{
			box-sizing: border-box;
		}
		
		.table-header-fixed thead th{
			display: static;
			box-sizing: border-box;
		}
		
		.table-header-fixed thead tr{
		  display: block;
		  position: relative;
		  box-sizing: border-box;
		}
		
		.table-header-fixed tbody {
		  display: block;
		  overflow: hidden auto;
		  width: auto;
		  height: 550px;
		}
		
		
		
		
		
		#tbl_articulos thead{
			position: sticky;
			top: 0;
			z-index: 500;
		}
		
		#tbl_articulos tr:nth-child(2n) > td:nth-child(1),
		#tbl_articulos tr:nth-child(2n) > td:nth-child(2),
		#tbl_articulos tr:nth-child(2n) > td:nth-child(3){
			background: #C3D9FF;
		}
		
		#tbl_articulos tr:nth-child(2n+1) > td:nth-child(1),
		#tbl_articulos tr:nth-child(2n+1) > td:nth-child(2),
		#tbl_articulos tr:nth-child(2n+1) > td:nth-child(3){
			background: #E8EEF7;
		}
		
		
		/*#tbl_articulos tr > td:nth-child(1), 
		#tbl_articulos tr > th:nth-child(1){
			position: sticky;
			left: 0px;
		}
		
		#tbl_articulos tr > th:nth-child(1){
			background : #d9edf7;
		}
		
		#tbl_articulos tr > td:nth-child(2), 
		#tbl_articulos tr > th:nth-child(2){
			position: sticky;
			border: 1px solid #ccc;
		}
		
		#tbl_articulos tr > td:nth-child(3), 
		#tbl_articulos tr > th:nth-child(3){
			position: sticky;
			border: 1px solid #ccc;
		}
		
		/**
		 * La tabla debe tener el valor separate en la propiedad border-collapse para que sea vea los bordes
		 * al usar la propiedad sticky
		 */
		#tbl_articulos {
			border-collapse : separate;
		}
    </style>
    <script src="../../../include/root/vue/vue.min.js"></script>
    <script src="../../../include/root/jquery.min.js"></script>
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.quicksearch.js" type="text/javascript"></script>
    <!--<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>-->
    <script src="../../../include/root/bootstrap.min.js"></script>
	
	<script type='text/javascript' src='../../../include/root/jquery.stickytableheaders.js'></script>

<body width="100%">
<?php
    encabezado("<div class='titulopagina2'>LISTADO BASICO DE MEDICAMENTOS</div>", $wactualiza, "clinica");
?>

    <!-- Constant HTML variables -->
    <input type="hidden" v-model="headers" name="tableHeaders" id="tableHeaders" value='<?=$tableHeaders?>'>
    <input type="hidden" name="tableFilters" id="tableFilters" value='<?=$tableFiltersJson?>'>
    <input type="hidden" name="numberRows" id="numberRows" value='<?=$numberRows?>'>
    <input type="hidden" name="wemp_pmla" id="wemp_pmla" value='<?=$wemp_pmla?>'>
    <div id='app'>
        <!-- OPTIONS FORM  -->
        <div class="container" style="width:100%;padding:0px;" align="center">
            <div class="form-inline text-left" style="width:90%; padding:0px;">
                <span class="text-primary text-left" style="cursor:pointer" v-on:click="ocultarFormulario"> ocultar filtros </span>
                <br>
            </div>
            <div id="div_formulario" class="form-inline text-left" style="width:90%; padding:0px;">
                <table style="width:100%; padding:0px;" class="table small table-bordered table-condensed">
                    <?php for( $i = 1; $i <= $numberRows; $i++ ){?>
                        <tr>
                            <?php for( $j = (5*$i-5); $j < (5*$i); $j++ ){?>
                                    <?php if( isset( $tableFilters[$j] ) ){ ?>
                                        <td class="bg-warning" v-bind:class="class_<?=$tableFilters[$j]['columnName']?>">
                                            <label for="busFuente" class="wn">&nbsp;&nbsp;<?php echo utf8_decode($tableFilters[$j]['usersName']) ?></label>
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
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-1">
							<span class="input-group-text">
								<button type="button" id="btn_buscar" class="btn btn-primary btn-sm" v-on:click="consultarMaestro">Consultar <span class="glyphicon glyphicon-refresh" aria-hidden="true"></span></button>
							</span>
						</div>
						<div class="col" style='display:flex; flex-wrap:wrap;'>
							
							<span v-for="sf in selectFiltros" class="btn-sm bg-success input-group-text" style='margin:1px;' v-html="sf"></span>
							
						</div>
					</div>
				</div>
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
        <div class="input-group panel-form" v-if="items.length > 0" style="display:flex;">
            <!--<span class="input-group-text">Formulario Busqueda:</span>-->
				
			<span class="input-group-text col-sm-9">
				<input class="form-control input-sm" type="inp_searcher" name="inp_searcher" id="inp_searcher" value="" placeholder="Escriba para buscar">
			</span>
			
			<span class="input-group-text col-sm-1" style="display:flex;align-self:center;">
				<span style="padding: 0 5px;">{{ searchTotal }}</span>de<span style='font-weight:bold;padding: 0 5px'>{{ items.length }}</span>
			</span>
			
			<span class="input-group-text col-sm-2">
				<button type="button" id='btn_fixColumn' class="btn btn-success btn-sm" onClick="fixedColumns(this);">Fijar columnas</span></button>
			</span>
			
			<!-- <span class="input-group-text col-sm-1">
				<button type="button" id='btn_fixHeader' class="btn btn-success btn-sm" onClick="fixedHeader(this);">Fijar encabezado</span></button>
			</span> -->
			
        </div>
        <div id="div_results" class="panel-body panel panel-default panel-results" style='padding: 0;margin:15px;' v-if="items.length > 0">
            <table class="table small table-bordered table-condensed" id="tbl_articulos">
                <thead>
                    <tr>
                        <th v-for=" header in headers " :class=" header.columnName == 'Artcod' ? 'text-center bg-info columnfixed' : 'text-center bg-info '+header.columnName"><span v-text="header.usersName"></span></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for=" ( item, keyI ) in items" :class="{ fila1: keyI % 2 != 0, fila2: keyI % 2 == 0}" tipo='registroEncontrado'>
                        <!--<td v-for="header in headers" ><span v-text=" header.columnName "></span></td>-->
                        <td tooltip="carajo" v-for="( value, key ) in item" :class=" key == 'Artcod' ? 'columnfixed' : key"><span v-html="value"></span></td>
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
        <div class="text-center">
			<button type="button" v-on:click="cerrarVentana" arial-label="">Cerrar Ventana</button>
        </div>
    </div>
    <!-- vue js template -->
    <script>
	
		var isFixedColumn = false;
		var isFixedHeader = false;
		
		function actualizarButtons(){
			
			if( isFixedColumn ){
				$( "#btn_fixColumn" )
					.removeClass( 'btn-success' )
					.addClass( 'btn-warning' )
					.html( 'Liberar columnas' )
			}
			else{
				$( "#btn_fixColumn" )
					.removeClass( 'btn-warning' )
					.addClass( 'btn-success' )
					.html( 'Fijar columnas' )
			}
			
			if( isFixedHeader ){
				$( "#btn_fixHeader" )
					.removeClass( 'btn-success' )
					.addClass( 'btn-warning' )
					.html( 'Liberar encabezado' )
			}
			else{
				$( "#btn_fixHeader" )
					.removeClass( 'btn-warning' )
					.addClass( 'btn-success' )
					.html( 'Fijar encabezado' )
			}
		}
		
		function fixedHeader(){
			
			if( !isFixedHeader ){
				
				isFixedHeader = true;
				
				$( "#tbl_articulos thead" ).css({
					position: 'sticky',
					top		: 0,
					zIndex  : 500,
				});
			}
			else{
				isFixedHeader = false;
				
				$( "#tbl_articulos thead" ).css({
					position: 'static',
					top		: 0,
					zIndex	: 0,
				});
			}
			
			actualizarButtons();
		}
		
		function fixedColumns( cmp ){
			
			if( !isFixedColumn ){
				actualizarLeftHeaders();
			}
			else{
				resetLeftHeaders();
			}
		}
	
	
		function actualizarLeftHeaders(){
			
			isFixedColumn = true;
			actualizarButtons()
			
			$( "#tbl_articulos th" ).each(function(x){
				
				//Solo se hace para el segundo y tercer elemento del encabezados ya que el primero siempre es 0
				if( x < 3 ){
					let iniLeft = this.getBoundingClientRect().left;
					
					//Obtengo el left del padre
					let parentLeft =  $( this ).parent()[0].getBoundingClientRect().left;
					
					//La diferencia es el left que debe quedar el elemento th
					let setLeft = iniLeft - parentLeft;
					
					//Deja la propiedad set para el elemento, esto es para que la propiedad css position: sticky funcione correctamente
					$( this ).css({
								position: 'sticky',
								left	: setLeft,
							});
					
					//Debo propagar esta propiedad para todos los td bajo la misma columna
					$( $( "td:eq("+x+")" , "#tbl_articulos tbody tr" ) ).css({
									position		: 'sticky',
									left			: setLeft,
									backgroundColor : '#fcf8e3',
								});
				}
				
				if( x >= 3 )
					return false;
			});
		}
		
		/**
		 * Antes de mostrar los resultado de una consulta, ya sea por el boton consultar o
		 * al buscar palabras, la propiedad left se debe dejar como auto, para reposicionar
		 * correctamente el elemento
		 */
		function resetLeftHeaders(){
			
			isFixedColumn = false;
			actualizarButtons()
			
			// try{
				// let scrollLeft = $( "#tbl_articulos").parent()[0].scrollLeft;
				// $( "#tbl_articulos").parent()[0].scrollLeft = 0;
			// }
			// catch(e){}
			
			$( "#tbl_articulos th" ).each(function(x){
				
				if( x < 3 ){
					$( this ).css({
							left	: 'auto', 
							position: 'static',
						});
						
					$( $( "td:eq("+x+")" , "#tbl_articulos tbody tr" ) ).css({
							left			: 'auto', 
							position		: 'static',
							backgroundColor : ''
						});
				}
				
				if( x >= 3 )
					return false;
			});
		}

        var filtersAux = JSON.parse($("#tableFilters").val());
        var filtersCount = filtersAux.length;
        
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
            setData += ' "selectFiltros": [],';
            setData += ' "searchTotal": 0,';
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
                    that = this;
                    this.url = "rep_articulos.php?wemp_pmla="+$("#wemp_pmla").val()+"&consultaAjax=''";
                    var hayParametros = false;
                    var parametros    = "";
					this.selectFiltros = [];
                    $(this.filters).each(function( index, value ){
                        var valorActual   = $("SELECT[name='"+value.columnName+"']").val();
                        if( valorActual != "" ){
                            hayParametros = true;
                            parametros = parametros+" AND "+value.columnName+"='"+valorActual+"'";
							console.log( this );
							that.selectFiltros.push( this.usersName + ( valorActual == 'on' ? ' <b>SI</b>' : ' <b>NO</b>' ))
                        }
                    });
                    if( hayParametros )
                        this.url = this.url+"&parametros="+parametros;

                    $('#cargando').modal({
                        backdrop: 'static',
                        keyboard: false
                    });

                    var data = {
                        consultaAjax: '',
                        wemp_pmla: this.wemp_pmla
                    };

					// $( "#tbl_articulos td, #tbl_articulos th" ).css({ left: 'auto'})
					resetLeftHeaders();
                    fetch( this.url, data  )
                    .then( res => res.json() )
                    .then( data => {
                        this.items = data.data;
						
						this.searchTotal = this.items.length;
                        $('#cargando').modal('toggle');

                        setTimeout( function(){
                            $("#inp_searcher").quicksearch("table#tbl_articulos tbody tr[tipo='registroEncontrado']",{
								delay	: 300,
								onBefore: resetLeftHeaders,
								onAfter : function(){ that.searchTotal = $("#tbl_articulos > tbody > tr:visible").length; },
							});
							// actualizarLeftHeaders()
                        }, 1000 );
                    })
                    .catch( error => console.log( error.message ) );
                },

                "ocultarFormulario": function( cmp ){
                    
					$("#div_formulario").toggle();
					
					if( $("#div_formulario").is(":visible") )
						$( cmp.target ).html('ocultar filtros')
					else
						$( cmp.target ).html('ver filtros')
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
                },

                cerrarVentana: function(){
                    console.log("entrando acá");
                    window.top.close();
                }
            }
        });
		
    </script>
</body>
</html>