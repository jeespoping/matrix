<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FACTURACION MANUAL SERVINTE - MATRIX</title>
    <link href="bootstrap.min.css" rel="stylesheet">
    <link href="facSer_style.css" rel="stylesheet">
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <link href="http://mtx.lasamericas.com.co/matrix/soporte/procesos/stylehelpDesk.css" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link rel="stylesheet" href="/resources/demos/style.css">
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="facSer_js.js" type="text/javascript"></script>
    <script>
        $( function() {
            $( "#tabs" ).tabs();
        } );
    </script> <!-- PESTAÑAS -->
    <script>
        function modificar(idRegistro,accion,Coddispo,ccoUnidad)
        {
            // definimos la anchura y altura de la ventana
            var altura=300;
            var anchura=800;
            // calculamos la posicion x e y para centrar la ventana
            var y=parseInt((window.screen.height/2)-(altura/2));
            var x=parseInt((window.screen.width/2)-(anchura/2));
            // mostramos la ventana centrada

            window.open("TrazProcess.php?accion="+accion.value+'&idRegistro='+idRegistro+'&Coddispo='+Coddispo+'&codCcoDispo='+ccoUnidad,
                target="blank","width="+anchura+",height="+altura+",top="+y+",left="+x+",toolbar=no,location=no,status=no,menubar=no,scrollbars=yes,directories=no,resizable=no");
        }
    </script> <!-- VENTANAS -->
    <script type="text/javascript">
        $(document).ready(function(){
            /** Funcion para añadir una nueva columna en la tabla:  */
            $("#add").click(function()
            {
                // Obtenemos el numero de filas (td) que tiene la primera columna
                // (tr) del id "tblConcept"
                var tds=$("#tblConcept tr:first td").length;
                // Obtenemos el total de columnas (tr) del id "tblConcept"
                var trs=$("#tblConcept tr").length; //contador de los rows - WILL
                document.getElementById('numRows').value = trs; //llevar a input hidden el numero de rows - WILL
                var numFact = document.getElementById('numFactu').value;
                var nuevaFila="<tr>";

                for(var i=0;i<tds;i++)
                {
                    // añadimos las columnas
                    var conteoTds = (i+1);  //contador de cada celda -WILL
                    var idTd = conteoTds+'-'+trs;  //id de cada celda (contador + numero del row) - WILL
                    var idLess = 'less-'+trs;

                    if(conteoTds === 3 || conteoTds === 4 || conteoTds === 5)
                    {
                        switch(conteoTds)
                        {
                            case 3: nuevaFila+="<td><input type='text' class='inpConcepto' required onchange='sumValCon(this.value); setFoco("+trs+")' id="+idTd+"></td>";
                                break;
                            case 4: nuevaFila+="<td><input type='text' class='inpConcepto' required value='0' onblur='sumValDes(this.value); setFoco3("+trs+")' id="+idTd+"></td>";
                                break;
                            case 5: nuevaFila+="<td>" +
                                                "<input type='text' class='inpConcepto' id="+idTd+">" +
                                                    "<a href='#' id="+idLess+" class='btn btn-warning btn-xs' title='Limpiar Valores' style='margin-right: -19px; margin-left: 5px'>" +
                                                        "<span class='glyphicon glyphicon-remove' onclick='cleanVal2(numRows)'></span>" +
                                                    "</a>" +
                                                "</td>";
                                break;
                        }
                    }
                    if(conteoTds === 2)
                    {
                        nuevaFila+="<td><input type='text' class='inpConcepto' required onfocus='consCcosto("+trs+")' id="+idTd+"></td>";
                    }
                    if(conteoTds === 1)
                    {
                        nuevaFila+="<td><input type='text' class='inpConcepto' required onblur='checkConcepto(this.value,"+trs+","+numFact+")' id="+idTd+"></td>";
                    }
                }
                // Añadimos una columna con el numero total de filas.
                // Añadimos uno al total, ya que cuando cargamos los valores para la columna, todavia no esta añadida
                //nuevaFila+="<td>"+(trs+1)+" filas";  //lo comento porque no lo necesito WILL
                //nuevaFila+="<td>";
                nuevaFila+="</tr>";
                $("#tblConcept").append(nuevaFila);
            });

            /**
             * Funcion para eliminar la ultima columna de la tabla.
             * Si unicamente queda una columna, esta no sera eliminada
             */
            $("#del").click(function(){
                // Obtenemos el total de columnas (tr) del id "tabla"
                var trs=$("#tblConcept tr").length;
                if(trs>1)
                {
                    // Eliminamos la ultima columna
                    $("#tblConcept tr:last").remove();
                }
            });
        });
    </script> <!-- ADICION DE LINEAS -->
    <style>
        .Ntooltip
        {
        }

        a.Ntooltip {
            position: relative; /* es la posición normal */
            text-decoration: none !important; /* forzar sin subrayado */
            color:#0080C0 !important; /* forzar color del texto */
            font-weight:bold !important; /* forzar negritas */
        }

        a.Ntooltip:hover {
            z-index:999; /* va a estar por encima de todos */
            background-color:#000000; /* DEBE haber un color de fondo */
        }

        a.Ntooltip span {
            display: none; /* el elemento va a estar oculto */
        }

        a.Ntooltip:hover span {
            display: block; /* se fuerza a mostrar el bloque */
            position: absolute; /* se fuerza a que se ubique en un lugar de la pantalla */
            top:2em; left:2em; /* donde va a estar */
            width:250px; /* el ancho por defecto que va a tener */
            padding:5px; /* la separación entre el contenido y los bordes */
            background-color: #0080C0; /* el color de fondo por defecto */
            color: #FFFFFF; /* el color de los textos por defecto */
        }
    </style>
    <?php
    //DESCOMENTAR PARA PUBLICACION EN MATRIX:
    ///*
    include("conex.php");
    include("root/comun.php");

    if(!isset($_SESSION['user']))
    {
        ?>
        <div align="center">
            <label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix o inicie sesion nuevamente.</label>
        </div>
        <?php
        return;
    }
    else
    {
        $user_session = explode('-', $_SESSION['user']);
        $wuse = $user_session[1];
        mysql_select_db("matrix");
        $conex = obtenerConexionBD("matrix");
        $conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
    }
    //*/
    //include('../MATRIX/include/root/conex.php'); //publicacion local
    //include('../MATRIX/include/root/comun.php'); //publicacion local
    //mysql_select_db('matrix'); //publicacion local
    //$conex = obtenerConexionBD('matrix'); //publicacion local
    //$wuse = '0100463';  //publicacion local
    $fuente = '20';
    $fecha_Actual = date('Y-m-d');  $hora_Actual = date('H:m:s');
    $ano_Actual = date('Y');        $mes_Actual = date('m');
    $cCostos = obtenerDatosUsuario(1,$wuse,$conex);
    $accion = $_POST['accion']; $numFactu = $_POST['numFactu']; $plazo = $_POST['plazo'];       $tipResp = $_POST['tipoResp'];
    $docPac = $_POST['docPac']; $nomPac = $_POST['nomPac'];     $nitResp = $_POST['nitResp'];   $desResp = $_POST['descResp'];
    $obsFac = $_POST['observacionFac']; $subaccion = $_POST['subaccion'];
    ?>
</head>

<body>
<div class="panel panel-info contenido" style="width: 95%">
    <div class="panel-heading encabezado">
        <div class="panel-title titulo1">Matrix - FACTURACION MANUAL SERVINTE</div>
    </div>

    <div align="center" class="panel panel-info divGeneral">
        <div id="tabs">
            <ul>
                <li><a href="#tabs-1">GRABAR</a></li>
                <!-- <li><a href="#tabs-2">CONSULTAR</a></li> -->
            </ul>

            <div id="tabs-1">
                <?php
                //////// GRABAR LA FACTURA:
                if($accion == 'grabar')
                {
					$ok = false;
                    // OBTENER CONSECUTIVO NUEVA FACTURA:
					txtLog("INICIO GRABACION FACTURA PARA DOC: $numFactu", true);
                    $nuevaFactura = obtenerNumFactura($fuente,$cCostos ,$conex_o);

					if ( $nuevaFactura < 0 )
                    {
						txtLog("NO SE PUDO GENERAR NUEVO CONSECUTIVO DE FACTURA. INTENTE DE NUEVO.");
						echo "<h4>___________________________________</h4>";
						echo "<h4>NO SE PUDO GENERAR NUEVO CONSECUTIVO DE FACTURA PARA EL DOC: $numFactu</h4>";
					}
					else
					{
						txtLog("GRABANDO FACTURA: $newConsecutivo");
						
						//SUMAR $PLAZO A LA FECHA ACTUAL:
						$fecha_Actual2 = strtotime(date('Y-m-d'));
						$mesMas = date("Y-m-d", strtotime("+$plazo day", $fecha_Actual2));
						$carfev = $mesMas;

						//FECHA Y HORA ACTUAL:
						$fecha_data = date('Y-m-d h:i:s');  $anoActualMesActual = $ano_Actual.$mes_Actual;  $fteFactura = $fuente.'-'.$nuevaFactura;
						$doctdo = 'F'.$tipResp; //para AHDOC

						//OBTENER VALOR NETO DE LA FACTURA DESDE LA TABLA TEMPORAL:
						$querySumFacTemp = "select sum(vlrneto) from amefactmp WHERE fac = '$numFactu'";
						$commSumFacTemp = odbc_do($conex_o, $querySumFacTemp);
						$totFactura = odbc_result($commSumFacTemp,1);

						//GUARDAR EN CACAR:
						$insertCacar = "insert into cacar VALUES('$fuente','$nuevaFactura','1','$ano_Actual','$mes_Actual','$cCostos','$fecha_Actual','$carfev',
																 '00','','','$tipResp','$docPac','$nomPac','E','$nitResp','$desResp','$fuente','$nuevaFactura',
																 '$totFactura','$totFactura','0','','0','0')";
						$comInsertCacar = odbc_do($conex_o, $insertCacar);
						if($comInsertCacar){?><!--<h4>CACAR => OK</h4>--><?php }
						else{?><h4>NO SE PUDO INSERTAR EN CACAR</h4><?php }

						//GUARDAR EN CACARCON:
						$insertCacarcon = "insert into cacarcon VALUES('$fuente','$nuevaFactura','0','$fuente','$nuevaFactura','8888','0','0','0','$totFactura','S','C')";
						$comInsertCacarcon = odbc_do($conex_o, $insertCacarcon);
						if($comInsertCacarcon){?><!--<h4>CACARCON => OK</h4>--><?php }
						else{?><h4>NO SE PUDO INSERTAR EN CACARCON</h4><?php }

						//GUARDAR CACARDES:
						$insertCacardes = "insert into cacardes VALUES('$fuente','$nuevaFactura','$fuente','$nuevaFactura')";
						$comInsertCacardes = odbc_do($conex_o, $insertCacardes);
						if($comInsertCacardes){?><!--<h4>CACARDES => OK</h4>--><?php }
						else{?><h4>NO SE PUDO INSERTAR EN CACARDES</h4><?php }

						//GUARDAR CACAROBS:
						$linea = lineaDeString ($obsFac, 1, 60);
						if ($linea != "") {
							//echo("<br>grabando lineas:<br>$linea");
							$insertCacarobs = "insert into cacarobs VALUES('$fuente','$nuevaFactura','$ano_Actual','$mes_Actual','1','$linea','0')";
							$comInsertCacarobs = odbc_do($conex_o, $insertCacarobs);
						}
						if($comInsertCacarobs) {
							$linea = lineaDeString ($obsFac, 2, 60);
							if ($linea != "") {
								//echo("<br>$linea");
								$insertCacarobs = "insert into cacarobs VALUES('$fuente','$nuevaFactura','$ano_Actual','$mes_Actual','2','$linea','0')";
								$comInsertCacarobs = odbc_do($conex_o, $insertCacarobs);
							}
							if($comInsertCacarobs) {
								$linea = lineaDeString ($obsFac, 3, 60);
								if ($linea != "") {
									//echo("<br>$linea");
									$insertCacarobs = "insert into cacarobs VALUES('$fuente','$nuevaFactura','$ano_Actual','$mes_Actual','3','$linea','0')";
									$comInsertCacarobs = odbc_do($conex_o, $insertCacarobs);
								}
							}
						}
						if($comInsertCacarobs){?><!--<h4>CACAROBS => OK</h4>--><?php }
						else{?><h4>NO SE PUDO INSERTAR TODO EN CACAROBS</h4><?php }

						//GUARDAR EN CAENC:
						$insertCaenc = "insert into caenc VALUES('$fuente','$nuevaFactura','$ano_Actual','$mes_Actual','','0','$wuse','AP')";
						$comInsertCaenc = odbc_do($conex_o, $insertCaenc);
						if($comInsertCaenc){?><!--<h4>CAENC => OK</h4>--><?php }
						else{?><h4>NO SE PUDO INSERTAR EN CAENC</h4><?php }

						//GUARDAR EN CAESTMOV:
						$insertCaestmov = "insert into caestmov VALUES('$fuente','$nuevaFactura','$anoActualMesActual','1','E','AP','$fuente','$nuevaFactura',
																	   'AP','$wuse','$fecha_data')";
						$comInsertCaestmov = odbc_do($conex_o, $insertCaestmov);
						if($comInsertCaestmov){?><!--<h4>CAESTMOV => OK</h4>--><?php }
						else{?><h4>NO SE PUDO INSERTAR EN CAESTMOV</h4><?php }

						//GUARDAR EN CASALLIN:
						$insertCasallin = "insert into casallin VALUES('$fuente','$nuevaFactura','$cCostos','$fecha_Actual','$carfev','00','','','E','$docPac',
																	   '$nomPac','$tipResp','$nitResp','$desResp','$totFactura','$totFactura','0','0','0','0')";
						$comInsertCasallin = odbc_do($conex_o, $insertCasallin);
						if($comInsertCasallin){?><!--<h4>CASALLIN => OK</h4>--><?php }
						else{?><h4>NO SE PUDO INSERTAR EN CASALLIN</h4><?php }

						//GUARDAR EN FAMOV:
						$insertFamov = "insert into famov VALUES('$fuente','$nuevaFactura','$ano_Actual','$mes_Actual','$fecha_Actual','$plazo','00','','',
																 '$docPac','$tipResp','$nitResp','$desResp','E','E','6','01','0')";
						$comInsertFamov = odbc_do($conex_o, $insertFamov);
						if($comInsertFamov){?><!--<h4>FAMOV => OK</h4>--><?php }
						else{?><h4>NO SE PUDO INSERTAR EN FAMOV</h4><?php }

						//GUARDAR EN FAMOVDET:
						$queryFacTmp = "select * from amefactmp WHERE fac = '$numFactu'";
						$commFacTmp = odbc_do($conex_o, $queryFacTmp);
						txtLog("guardando detalle--------------");
						while(odbc_fetch_row($commFacTmp))
						{
							$concTmp = odbc_result($commFacTmp,'con');      $cCostTmp = odbc_result($commFacTmp, 'ccos');
							$valConTmp = odbc_result($commFacTmp, 'vlrcon');

							$insertFamovdet = "insert into famovdet VALUES('$fuente','$nuevaFactura','$ano_Actual','$mes_Actual','$fecha_Actual','$concTmp',
																		   '$cCostTmp','0','$valConTmp','0','0','0','0')";
							txtLog($insertFamovdet);
							$commInsertFamovdet = odbc_do($conex_o, $insertFamovdet);
							if($commInsertFamovdet){?><!--<h4>FAMOVDET - CONCEPTO : <?php // echo $concTmp ?> => OK</h4>--><?php }
							else{?><h4>NO SE PUDO INSERTAR EN FAMOVDET, CARGO: <?php echo $concTmp ?> </h4><?php }
						}
						txtLog("------------------------------");

						//GUARDAR FAMOVOTR:
						$insertFamovotr = "insert into famovotr VALUES('$fuente','$nuevaFactura','','0','0','$totFactura','N')";
						$commInsertFamovotr = odbc_do($conex_o, $insertFamovotr);
						if($commInsertFamovotr){?><!--<h4>FAMOVOTR => OK</h4>--><?php }
						else{?><h4>NO SE PUDO INSERTAR EN FAMOVOTR</h4><?php }

						//GUARDAR EN AHDOC:
						$insertAhdoc = "insert into ahdoc VALUES('$fteFactura','$doctdo','$desResp','','$cCostos','','$fecha_Actual','','N','','','$nitResp',
																 '','','','$wuse','$fecha_data','','')";
						$commInsertahdoc = odbc_do($conex_o, $insertAhdoc);
						if($commInsertahdoc){?><!--<h4>AHDOC => OK</h4>--><?php }
						else{?><h4>NO SE PUDO INSERTAR EN AHDOC</h4><?php }

						//CONSULTAR CONSECUTIVO parsmo(AHPAR) Y REALIZAR UPDATE PARA AUMENTARLO EN 1:
						IncConsecutivoAHPAR($conex_o);

						//GUARDAR EN AHDOCACT:
						$insertAhdocact = "insert into ahdocact VALUES('$fteFactura','CARTERA','UBIPAL','$nuevaFactura','0','AR','$parsmo','1','0','$fecha_data')";
						$commInsertAhdocact = odbc_do($conex_o, $insertAhdocact);
						if($commInsertAhdocact){?><!--<h4>AHDOCACT => OK</h4>--><?php }
						else{?><h4>NO SE PUDO INSERTAR EN AHDOCACT</h4><?php }

						//GUARDAR EN FAMOVADJ:
						$insertFamovadj = "insert into famovadj VALUES('','$fuente','$nuevaFactura','','$fuente','$nuevaFactura','0')";
						$commInsertFamovadj = odbc_do($conex_o, $insertFamovadj);
						if($commInsertFamovadj){?><!--<h4>FAMOVADJ => OK</h4>--><?php }
						else{?><h4>NO SE PUDO INSERTAR EN FAMOVADJ</h4><?php }

						//GUARDAR EN FALOG:
						$insertFalog = "insert into falog VALUES('$wuse','$wuse','facSer_01.php','Fac-Automatica','Fte Factura','$fuente','Nro Factura',
																 '$nuevaFactura','','','','I','famov','$fecha_data')";
						$commInsertFalog = odbc_do($conex_o, $insertFalog);
						if($commInsertFalog){?><!--<h4>FALOG => OK</h4>--><?php }
						else{?><h4>NO SE PUDO INSERTAR EN FALOG</h4><?php }
						
						$ok = true;
						
					} // if $nuevaFactura < 0 else

					// Si se pudo grabar la factura
					if ($ok){
						echo '<div class="card bg-light divContHome" style="border: none">
						<div class="navigation" style="margin-top: 80px">
							<form id="formHome" name="formHome" method="post" action="facSer_01.php" style="margin-top: 50px">
								<h3>FACTURA GRABADA</h3>
								<h4>' . $nuevaFactura . '</h4>
								<input type="hidden" name="accion" value="home">
								<input type="hidden" name="subaccion" value="inicio">
								<input type="submit" class="btn btn-info btn-sm" value="ACEPTAR" style="margin: 50px auto 50px auto">
							</form>
						</div>
					</div>';
					}
					else {
						echo '<div class="card bg-light divContHome" style="border: none">
						<div class="navigation" style="margin-top: 80px">
							<form id="formHome" name="formHome" method="post" action="facSer_01.php" style="margin-top: 50px">
								<h3>INTENTE DE NUEVO EN UN MINUTO<br><br></h3>
								<input type="text" id="numFactu" name="numFactu" class="form-control form-sm" value=' . $numFactu . ' style="display:none;" readonly>
								<input type="hidden" id="accion" name="accion" value="grabar">
								<input type="submit" class="btn btn-success btn-sm" value="REINTENTAR" title="Reintentar" style="margin: 50px auto 50px auto">
							</form>
						</div>
					</div>';
					                                
                                

					}
					
					/*
					<div class="card bg-light divContHome" style="border: none">
						<div class="navigation" style="margin-top: 80px">
							<form id="formHome" name="formHome" method="post" action="facSer_01.php" style="margin-top: 50px">
								<h3><?php echo ($nuevaFactura>0?'FACTURA GRABADA:':'VOLVER') ?>  </h3>
								<h4><?php echo ($nuevaFactura>0?$nuevaFactura:'-') ?></h4>
								<input type="hidden" name="accion" value="home">
								<input type="hidden" name="subaccion" value="inicio">
								<input type="submit" class="btn btn-info btn-sm" value="ACEPTAR" style="margin: 50px auto 50px auto">
							</form>
						</div>
					</div>
					*/
					
                }
                //////// DILIGENCIAR LA FACTURA:
                else
                {
                    if($subaccion == 'crearFac')
                    {
                        ?>
                        <form id="formDatos" name="formDatos" method="post">
                            <div align="center" class="panel panel-info divTitulo" style="margin-bottom: 10px; background-color: #305496">
                                <table>
                                    <tr>
                                        <td>
                                            <div class="input-group" style="background-color: transparent; text-align: left; color: white">
                                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none">
                                                    <label for="anoActual">A&ncaron;o:</label>
                                                </span>
                                                <input type="text" id="anoActual" name="anoActual" class="form-control form-sm"
                                                       style="background-color:transparent; border: none; width: 80px; font-size: large; color: white"
                                                       value="<?php echo $ano_Actual ?>" readonly>

                                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 1px"></span>

                                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none">
                                                    <label for="mesActual">Mes:</label>
                                                </span>
                                                <input type="text" id="mesActual" name="mesActual" class="form-control form-sm"
                                                       style="background-color:transparent; border: none; width: 80px; font-size: large; color: white"
                                                       value="<?php echo $mes_Actual ?>" readonly>

                                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 3px"></span>

                                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none">
                                                    <label for="cCostos">Centro de Costos:</label>
                                                </span>
                                                <input type="text" id="cCostos" name="cCostos" class="form-control form-sm inpCampos"
                                                       style="background-color:transparent; border: none; width: 80px; font-size: large; color: white"
                                                       value="<?php echo $cCostos ?>" readonly>

                                                <span class="input-group-addon input-sm" style="border: none; background-color: #305496; width: 10px">
                                                    <label style="font-size: medium; font-weight: bold">&ensp;&ensp;FACTURAS CLINICA LAS AMERICAS  &ensp;&ensp;&ensp;&ensp;</label>
                                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div align="left" class="panel panel-info divTitulo">
                                <table>
                                    <tr>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-addon input-sm"><label for="fuente">Fuente:</label></span>
                                                <input type="text" id="fuente" name="fuente" class="form-control form-sm" value="20-INGRESOS X FACTURACION CLINICA"
                                                       style="width: 292px" readonly>

                                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 3px"></span>

                                                <span class="input-group-addon input-sm" style="text-align: left"><label for="numFactu">Numero de Factura:</label></span>
                                                <input type="text" id="numFactu" name="numFactu" class="form-control form-sm" value="<?php obtenerNumFacturaTEMP($fuente,$cCostos ,$conex) ?>"
                                                       style="width: 225px; font-weight: bold; font-size: medium" readonly>

                                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 128px"></span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="input-group" style="margin-top: 5px">
                                                <span class="input-group-addon input-sm"><label for="fechafac">Fecha :</label></span>
                                                <input type="date" id="fechafac" name="fechafac" class="form-control form-sm" value="<?php echo $fecha_Actual ?>"
                                                       style="width:245px">

                                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 3px"></span>

                                                <span class="input-group-addon input-sm" style="text-align: left"><label for="plazo">Plazo: &ensp;&ensp;</label></span>
                                                <input type="number" id="plazo" name="plazo" min="0" class="form-control form-sm" value="30" style="width: 90px">

                                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 334px"></span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="input-group" style="margin-top: 5px">
                                                <span class="input-group-addon input-sm"><label for="docPac">Documento Paciente:</label></span>
                                                <input type="text" id="docPac" name="docPac" class="form-control form-sm" style="width: 180px">

                                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 3px"></span>

                                                <span class="input-group-addon input-sm" style="text-align: left"><label for="nomPac">Nombre:</label></span>
                                                <input type="text" id="nomPac" name="nomPac" class="form-control form-sm" style="width: 437px">
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="input-group" style="margin-top: 5px">
                                                <span class="input-group-addon input-sm"><label for="tipoResp">Tipo Responsable:</label></span>
                                                <select id="tipoResp" name="tipoResp" class="form-control form-sm" style="width:80px" onchange="cleanFields()">
                                                    <option>E</option>
                                                    <option>P</option>
                                                </select>

                                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 34px"></span>

                                                <span class="input-group-addon input-sm" style="text-align: left"><label for="nitResp">Nit/Cedula:</label></span>
                                                <input type="text" id="nitResp" name="nitResp" class="form-control form-sm" onblur="buscarDat('verNit',this.value,tipoResp)"
                                                       style="width: 180px" required>

                                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 3px"></span>

                                                <span class="input-group-addon input-sm"><label for="descResp">Descripcion:</label></span>
                                                <input type="text" id="descResp" name="descResp" class="form-control form-sm" style="width: 200px" onchange="respxNom(this.value,tipoResp)">

                                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 3px"></span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="input-group" style="margin-top: 5px">
                                                <span class="input-group-addon input-sm"><label for="tarifa">Tarifa :</label></span>
                                                <input type="text" id="tarifa" name="tarifa" class="form-control form-sm" value="01" style="width:80px" readonly>

                                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 59px"></span>

                                                <span class="input-group-addon input-sm" style="text-align: left"><label for="tipoServ">Tipo Servicio:</label></span>
                                                <input type="text" id="tipoServ" name="tipoServ" class="form-control form-sm" value="E-EXTERNOS" style="width: 160px" readonly>

                                                <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 223px"></span>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- DETALLE DE CONCEPTOS: -->
                            <div align="center" class="panel panel-info divTitulo" style="margin-top: 5px; background-color: #305496">
                                <table>
                                    <tr>
                                        <td>
                                            <div class="input-group" style="background-color: transparent; text-align: left">
                                <span class="input-group-addon input-sm" style="border: none; background-color: transparent; width: 10px">
                                    <label style="color: white; font-size: medium; font-weight: bold">&ensp;&ensp;DETALLE DE CONCEPTOS:  &ensp;&ensp;&ensp;&ensp;</label>
                                </span>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div align="left" class="panel panel-info divContenido">
                                <table id="tblConcept" style="width: 100%" border="0">
                                    <tr style="background-color: #D9EDF7; font-weight: bold">
                                        <td>&ensp;</td>
                                        <td><label for="1-1">&ensp;&ensp;&ensp;&nbsp;&nbsp;&nbsp;CONCEPTO</label></td>
                                        <td><label for="2-1">&ensp;&ensp;&ensp;&nbsp;&nbsp;&nbsp;C. COSTOS</label></td>
                                        <td><label for="3-1">&ensp;VALOR CONCEPTO</label></td>
                                        <td><label for="4-1">&ensp;&ensp;&ensp;VALOR DESCUENTO</label></td>
                                        <td align="right"><label for="detValNet" style="margin-right: 51px">&ensp;&ensp;VALOR NETO</label></td>
                                    </tr>
                                    <tr style="font-size: 18px">
                                        <input type="hidden" id="numRows" name="numRows" value="1">
                                        <td>
                                            <a href="#" id="clean1" class="btn btn-danger btn-xs" title="Limpiar Campos">
                                                <span class="glyphicon glyphicon-remove" onclick="cleanField(1)"></span>
                                            </a>
                                        </td>
                                        <td>
                                            <input type="text" id="1-1" name="detConcep" class="inpConcepto Ntooltip" onchange="validarDato(this.value,1,1); validar9819(this.value,1)"> <!--valor,parametro,numero de fila-->
                                            <input type="text" id="detConcepto1" class="inpTotales" style="font-size: small; width: 170px" readonly>
                                            <a href="#" id="chkConc1" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-search" onclick="openConc('verConcep','1-1',1)"></span></a>
                                        </td>
                                        <td>
                                            <input type="text" id="2-1" name="detCcosto" class="inpConcepto" onchange="validarDato(this.value,2,1)">
                                            <input type="text" id="detCcosto1" class="inpTotales" style="font-size: small; width: 170px" readonly>
                                            <a href="#" id="chkCcos1" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-search" onclick="openConc('verCcosto','2-1',1)"></span></a>
                                        </td>
                                        <td>
                                            <input type="text" id="3-1" name="detValCon" class="inpConcepto concep" onkeyup="format(this)"
                                                   onchange="document.getElementById('3-11').value=this.value; setFoco(numRows); quita_comas(this.value,'3-11')">
                                            <input type="hidden" id="3-11">
                                        </td>
                                        <td>
                                            <input type="text" id="4-1" name="detValDes" class="inpConcepto desc" onkeyup="format(this)" onblur="setFoco2(1)"
                                                   onchange="document.getElementById('4-11').value=this.value; quita_comas(this.value,'4-11')" value="0">
                                            <input type="hidden" id="4-11">
                                            <a href="#" id="chkPorcent1" class="btn btn-info btn-xs" title="Calcular Porcentaje"><span onclick="percent('3-11','4-11',numRows,'4-1')">%</span></a>
                                        </td>
                                        <td>
                                            <input type="text" id="5-1" name="detValNet" class="inpConcepto neto" readonly>
                                            <a href="#" id="less" class="btn btn-warning btn-xs" title="Calcular Valor Neto" style="margin-right: -19px">
                                                <span class="glyphicon glyphicon-ok" onclick="setFoco2(1); copiarVal2(numRows)"></span>
                                            </a>
                                        </td>
                                    </tr>

                                    <tr style="font-size: 18px">
                                        <input type="hidden" id="numRows2" name="numRows2" value="2">
                                        <td>
                                            <a href="#" id="clean2" class="btn btn-danger btn-xs" title="Limpiar Campos" style="margin-right: -19px">
                                                <span class="glyphicon glyphicon-remove" onclick="cleanField(2)"></span>
                                            </a>
                                        </td>
                                        <td>
                                            <input type="text" id="1-2" name="detConcep" class="inpConcepto" onchange="validarDato(this.value,1,2); validar9819(this.value,2)">    <!-- dato,parametro,numrow -->
                                            <input type="text" id="detConcepto2" class="inpTotales" style="font-size: small; width: 170px" readonly>
                                            <a href="#" id="chkConc2" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-search" onclick="openConc('verConcep','1-2',2)"></span></a>
                                        </td>
                                        <td>
                                            <input type="text" id="2-2" name="detCcosto" class="inpConcepto">
                                            <input type="text" id="detCcosto2" class="inpTotales" style="font-size: small; width: 170px" readonly>
                                            <a href="#" id="chkCcos2" class="btn btn-success btn-xs" style="pointer-events: none"><span class="glyphicon glyphicon-search" onclick="openConc('verCcosto','2-2',2)"></span></a>
                                        </td>
                                        <td>
                                            <input type="text" id="3-2" name="detValCon" class="inpConcepto concep" onkeyup="format(this)"
                                                   onchange="document.getElementById('3-22').value=this.value; setFoco(numRows2); quita_comas(this.value,'3-22')">
                                            <input type="hidden" id="3-22">
                                        </td>
                                        <td>
                                            <input type="text" id="4-2" name="detValDes" class="inpConcepto desc" onkeyup="format(this)" onblur="setFoco2(2)"
                                                   onchange="document.getElementById('4-22').value=this.value; quita_comas(this.value,'4-22')" value="0">
                                            <input type="hidden" id="4-22">
                                            <a href="#" id="chkPorcent2" class="btn btn-info btn-xs" title="Calcular Porcentaje"><span onclick="percent('3-22','4-22',numRows2,'4-2')">%</span></a>
                                        </td>
                                        <td>
                                            <input type="text" id="5-2" name="detValNet" class="inpConcepto neto">
                                            <a href="#" id="less" class="btn btn-warning btn-xs" title="Calcular Valor Neto" style="margin-right: -19px">
                                                <span class="glyphicon glyphicon-ok" onclick="setFoco2(2); copiarVal2(numRows2)"></span>
                                            </a>
                                        </td>
                                    </tr>
                                    <script>dishabledFields(2); //deshabilitar edicion de campos </script>

                                    <tr style="font-size: 18px">
                                        <input type="hidden" id="numRows3" name="numRows3" value="3">
                                        <td>
                                            <a href="#" id="clean3" class="btn btn-danger btn-xs" title="Limpiar Campos" style="margin-right: -19px">
                                                <span class="glyphicon glyphicon-remove" onclick="cleanField(3)"></span>
                                            </a>
                                        </td>
                                        <td>
                                            <input type="text" id="1-3" name="detConcep" class="inpConcepto" onchange="validarDato(this.value,1,3); validar9819(this.value,3)">
                                            <input type="text" id="detConcepto3" class="inpTotales" style="font-size: small; width: 170px" readonly>
                                            <a href="#" id="chkConc3" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-search" onclick="openConc('verConcep','1-3',3)"></span></a>
                                        </td>
                                        <td>
                                            <input type="text" id="2-3" name="detCcosto" class="inpConcepto" onchange="validarDato(this.value,2,3)">
                                            <input type="text" id="detCcosto3" class="inpTotales" style="font-size: small; width: 170px" readonly>
                                            <a href="#" id="chkCcos3" class="btn btn-success btn-xs" style="pointer-events: none"><span class="glyphicon glyphicon-search" onclick="openConc('verCcosto','2-3',3)"></span></a>
                                        </td>
                                        <td>
                                            <input type="text" id="3-3" name="detValCon" class="inpConcepto concep" onkeyup="format(this)"
                                                   onchange="document.getElementById('3-33').value=this.value; setFoco(numRows3); quita_comas(this.value,'3-33')">
                                            <input type="hidden" id="3-33">
                                        </td>
                                        <td>
                                            <input type="text" id="4-3" name="detValDes" class="inpConcepto desc" onkeyup="format(this)" onblur="setFoco2(3)"
                                                   onchange="document.getElementById('4-33').value=this.value; quita_comas(this.value,'4-33')" value="0">
                                            <input type="hidden" id="4-33">
                                            <a href="#" id="chkPorcent3" class="btn btn-info btn-xs" title="Calcular Porcentaje"><span onclick="percent('3-33','4-33',numRows3,'4-3')">%</span></a>
                                        </td>
                                        <td>
                                            <input type="text" id="5-3" name="detValNet" class="inpConcepto neto" readonly>
                                            <a href="#" id="less" class="btn btn-warning btn-xs" title="Calcular Valor Neto" style="margin-right: -19px">
                                                <span class="glyphicon glyphicon-ok" onclick="setFoco2(3); copiarVal2(numRows3)"></span>
                                            </a>
                                        </td>
                                    </tr>
                                    <script>dishabledFields(3); //deshabilitar edicion de campos </script>

                                    <tr style="font-size: 18px">
                                        <input type="hidden" id="numRows4" name="numRows4" value="4">
                                        <td>
                                            <a href="#" id="clean4" class="btn btn-danger btn-xs" title="Limpiar Campos" style="margin-right: -19px">
                                                <span class="glyphicon glyphicon-remove" onclick="cleanField(4)"></span>
                                            </a>
                                        </td>
                                        <td>
                                            <input type="text" id="1-4" name="detConcep" class="inpConcepto" onchange="validarDato(this.value,1,4); validar9819(this.value,4)">
                                            <input type="text" id="detConcepto4" class="inpTotales" style="font-size: small; width: 170px" readonly>
                                            <a href="#" id="chkConc4" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-search" onclick="openConc('verConcep','1-4',4)"></span></a>
                                        </td>
                                        <td>
                                            <input type="text" id="2-4" name="detCcosto" class="inpConcepto" onchange="validarDato(this.value,2,4)">
                                            <input type="text" id="detCcosto4" class="inpTotales" style="font-size: small; width: 170px" readonly>
                                            <a href="#" id="chkCcos4" class="btn btn-success btn-xs" style="pointer-events: none"><span class="glyphicon glyphicon-search" onclick="openConc('verCcosto','2-4',4)"></span></a>
                                        </td>
                                        <td>
                                            <input type="text" id="3-4" name="detValCon" class="inpConcepto concep" onkeyup="format(this)"
                                                   onchange="document.getElementById('3-44').value=this.value; setFoco(numRows4); quita_comas(this.value,'3-44')">
                                            <input type="hidden" id="3-44">
                                        </td>
                                        <td>
                                            <input type="text" id="4-4" name="detValDes" class="inpConcepto desc" onkeyup="format(this)" onblur="setFoco2(4)"
                                                   onchange="document.getElementById('4-44').value=this.value; quita_comas(this.value,'4-44')" value="0">
                                            <input type="hidden" id="4-44">
                                            <a href="#" id="chkPorcent4" class="btn btn-info btn-xs" title="Calcular Porcentaje"><span onclick="percent('3-44','4-44',numRows4,'4-4')">%</span></a>
                                        </td>
                                        <td>
                                            <input type="text" id="5-4" name="detValNet" class="inpConcepto neto" readonly>
                                            <a href="#" id="less" class="btn btn-warning btn-xs" title="Calcular Valor Neto" style="margin-right: -19px">
                                                <span class="glyphicon glyphicon-ok" onclick="setFoco2(4); copiarVal2(numRows4)"></span>
                                            </a>
                                        </td>
                                    </tr>
                                    <script>dishabledFields(4); //deshabilitar edicion de campos </script>

                                    <tr style="font-size: 18px">
                                        <input type="hidden" id="numRows5" name="numRows5" value="5">
                                        <td>
                                            <a href="#" id="clean5" class="btn btn-danger btn-xs" title="Limpiar Campos" style="margin-right: -19px">
                                                <span class="glyphicon glyphicon-remove" onclick="cleanField(5)"></span>
                                            </a>
                                        </td>
                                        <td>
                                            <input type="text" id="1-5" name="detConcep" class="inpConcepto" onchange="validarDato(this.value,1,5); validar9819(this.value,5)">
                                            <input type="text" id="detConcepto5" class="inpTotales" style="font-size: small; width: 170px" readonly>
                                            <a href="#" id="chkConc5" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-search" onclick="openConc('verConcep','1-5',5)"></span></a>
                                        </td>
                                        <td>
                                            <input type="text" id="2-5" name="detCcosto" class="inpConcepto" onchange="validarDato(this.value,2,5)">
                                            <input type="text" id="detCcosto5" class="inpTotales" style="font-size: small; width: 170px" readonly>
                                            <a href="#" id="chkCcos5" class="btn btn-success btn-xs" style="pointer-events: none"><span class="glyphicon glyphicon-search" onclick="openConc('verCcosto','2-5',5)"></span></a>
                                        </td>
                                        <td>
                                            <input type="text" id="3-5" name="detValCon" class="inpConcepto concep" onkeyup="format(this)"
                                                   onchange="document.getElementById('3-55').value=this.value; setFoco(numRows5); quita_comas(this.value,'3-55')">
                                            <input type="hidden" id="3-55">
                                        </td>
                                        <td>
                                            <input type="text" id="4-5" name="detValDes" class="inpConcepto desc" onkeyup="format(this)" onblur="setFoco2(5)"
                                                   onchange="document.getElementById('4-55').value=this.value; quita_comas(this.value,'4-55')" value="0">
                                            <input type="hidden" id="4-55">
                                            <a href="#" id="chkPorcent5" class="btn btn-info btn-xs" title="Calcular Porcentaje"><span onclick="percent('3-55','4-55',numRows5,'4-5')">%</span></a>
                                        </td>
                                        <td>
                                            <input type="text" id="5-5" name="detValNet" class="inpConcepto neto" readonly>
                                            <a href="#" id="less" class="btn btn-warning btn-xs" title="Calcular Valor Neto" style="margin-right: -19px">
                                                <span class="glyphicon glyphicon-ok" onclick="setFoco2(5); copiarVal2(numRows5)"></span>
                                            </a>
                                        </td>
                                    </tr>
                                    <script>dishabledFields(5); //deshabilitar edicion de campos </script>
                                </table>
                            </div>

                            <!-- VALORES NETOS: -->
                            <div style="border: none; margin-top: 10px; text-align: right">
                                <table style="width: 100%" border="0">
                                    <tr style="font-size: 18px">
                                        <td style="width: 400px">&ensp;</td>
                                        <td>
                                            <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TOTALES:
                                            </label>
                                        </td>
                                        <td>
                                            <input type="text" id="totValcon2" name="totValcon2" class="inpTotales tvc2" value="0" readonly>
                                            <input type="hidden" id="totValcon" name="totValcon" class="inpTotales tvc" value="0">
                                        </td>
                                        <td>
                                            <input type="text" id="totValdes2" name="totValdes2" class="inpTotales tvd2" value="0" readonly>
                                            <input type="hidden" id="totValdes" name="totValdes" class="inpTotales tvd" value="0">
                                        </td>
                                        <td>
                                            <input type="text" id="totValnet2" name="totValnet2" class="inpTotales tvn2" value="0" readonly>
                                            <input type="hidden" id="totValnet" name="totValnet" class="inpTotales tvn" value="0">
                                        </td>
                                        <td style="width: 37px">
                                            <a href="#" id="chkConc" class="btn btn-success btn-xs">
                                                <span class="glyphicon glyphicon-ok" onclick="totalizar()"></span>
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <!-- OBSERVACION: -->
                            <div style="margin-top: 5px">
                                <table style="width: 100%" border="0">
                                    <tr>
                                        <td><label for="observacionFac">Observaciones:</label></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <textarea id="observacionFac" name="observacionFac" class="form-control rounded-0" cols="129" rows="1" maxlength="180" required></textarea>
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div id="divListo" style="border: none; margin-top: 15px; display: block">
                                <input type="button" id="btnListo" class="btn btn-info btn-sm" value="DATOS COMPLETOS !" onclick="validarTodo()"
                                       title="Al presionar este boton no podrá adicionar mas registros">
                            </div>

                            <div id="divSave" style="border: none; margin-top: 15px; display: none">
                                <input type="hidden" id="accion" name="accion" value="grabar">
                                <input type="submit" class="btn btn-success btn-sm" value="GRABAR" title="Grabar Factura">
                            </div>
                        </form>
                        <?php
                    }
                    else
                    {
                        ?>
                        <div class="card bg-light divContHome" style="border: none">
                            <div class="navigation" style="margin-top: 80px">
                                <form id="formHome" name="formHome" method="post" action="facSer_01.php" style="margin-top: 50px">
                                    <h3>GRABACION DE FACTURAS</h3>
                                    <input type="hidden" name="subaccion" value="crearFac">
                                    <input type="submit" class="btn btn-primary" value="NUEVA FACTURA" style="margin: 50px auto 50px auto">
                                </form>
                            </div>
                        </div>
                        <?php
                    }
                }
                ?>
            </div>

        </div>
    </div>
</div>

<?php
////////////FUNCIONES:

// Obtiene de $cadena, la linea $num, de $longitud máxima
function lineaDeString ($cadena, $num, $longitud)
{
	$linea = 1;
	$str = "";
	$arr = explode(' ',$cadena);
	foreach($arr as $palabra) {
		if (strlen(trim($str . " " . $palabra)) > $longitud) {
			if ($linea == $num) {  // retornar la línea construída
				break;
			}
			$linea++;
			$str = $palabra;
		}
		else
			$str = $str . ' ' . $palabra;
		//echo("<br>$str");
	}
	if ($linea < $num)
		$str = "";
	return $str;
}

function obtenerDatosUsuario($parametro,$wuse,$conex)
{
    switch($parametro)
    {
        case 1:
            $query1 = "select * from usuarios WHERE Codigo = '$wuse'";
            $commit1 = mysql_query($query1, $conex) or die (mysql_errno()." - en el query: ".$query1." - ".mysql_error());
            $dato1 = mysql_fetch_array($commit1);   $cCostosUsuario = $dato1['Ccostos'];
            return $cCostosUsuario;
            break;
    }
}

function obtenerNumFactura($fuente,$cCostos ,$conex_o)
{

	/*
    $query0 = "select count(*) as cantreg from cafue";
    $commit0 = odbc_do($conex_o, $query0);
    $cantreg = odbc_result($commit0, 1);    
	txtLog("cantreg: $cantreg");
	*/
	
    $query1 = "select fuesfu, fuecse from cafue WHERE fuecod = '$fuente' AND fuecco = '$cCostos'";
    $commit1 = odbc_do($conex_o, $query1);
    $fuesfu = odbc_result($commit1, 1);    
	$fuecse = odbc_result($commit1, 2);
	txtLog("query1: $query1");
	txtLog("fuesfu: $fuesfu");
	txtLog("fuecse: $fuecse");

    $query2 = "select * from cafue WHERE fuecod = '$fuesfu' AND fuecco = '$fuecse'";
    $commit2 = odbc_do($conex_o, $query2);
    $fuecod = odbc_result($commit2, 2); 
	$fuesec = odbc_result($commit2,14); 
	$fuecco = odbc_result($commit2, 5);
	txtLog("query2: $query2");
	txtLog("fuecod: $fuecod, fuesec: $fuesec, fuecco: $fuecco");
    $newConsecutivo = $fuesec + 1;
	
	txtLog("sig consecutivo encontrado: $newConsecutivo");
	
    $query3 = "update cafue set fuesecx = '$newConsecutivo' WHERE fuecod = '$fuecod' AND fuecco = '$fuecco'";
	$conq = 0;
	while ($conq++ < 4) {
		$res = odbc_do($conex_o, $query3);
		if ($res)
			break;
		else {
			// Si no se puede reservar el consecutivo, retornar -1
			$newConsecutivo=-1;
            txtLog (odbc_errormsg($conex_o));
		    echo '<script>
		    document.getElementById("tabs-1").innerHTML = ""
			</script>
			<br>Se presentó un error generando nuevo consecutivo:<br>'.odbc_errormsg($conex_o).'
			<br>'.$query3;
			txtLog("NO se pudo reservar consecutivo... $conq");
			// esperar 3 segundos
			usleep(3000000);
		}
	}
	//echo $newConsecutivo;
    return $newConsecutivo;
}

function IncConsecutivoAHPAR($conex_o)
{

	$IncConsecutivo = false;
	//CONSULTAR CONSECUTIVO parsmo(AHPAR) Y REALIZAR UPDATE PARA AUMENTARLO EN 1:
	$queryAhpar = "select parsmo from ahpar";
	$commitAhpar = odbc_do($conex_o, $queryAhpar);
	$parsmo = odbc_result($commitAhpar, 1); 
	$newParsmo = $parsmo + 1;
	
	$updateAhpar = "update ahpar set parsmo = '$newParsmo'";
	txtLog("$updateAhpar");
	$conq = 0;
	while ($conq++ < 4) {
		$res = odbc_do($conex_o, $updateAhpar);
		if ($res) {
			$IncConsecutivo = true;
			break;
		}
		else {
			// Si no se puede actualizar el consecutivo, reintentar
			if ( $conq== 4){
				echo '<br>Se presentó un error actualizando consecutivo en ahpar:<br>'.odbc_errormsg($conex_o).'
						<br>'.$updateAhpar;
			}
			txtLog("Se presentó un error actualizando consecutivo en ahpar: ".odbc_errormsg($conex_o));
			// esperar 3 segundos
			usleep(3000000);
		}
	}
    return $IncConsecutivo;
}

// Consecutivo generado en matrix, que se muestra al llenar la factura
// pero no será el que grabará finalmente en unix.
// PUEDE MEJORARSE, GRABÁNDOLO ÚNICAMENTE SI SE LOGRA GRABAR EN UNIX.
function obtenerNumFacturaTEMP($fuente,$cCostos ,$conex)
{
    $query = "select * from equipos_000009 ORDER BY consecutivo DESC LIMIT 1";
    $commit = mysql_query($query, $conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());;
    $dato = mysql_fetch_array($commit);

    $consecutivo = $dato[1];
    $newConsecutivo = $consecutivo + 1;

    $query2 = "insert into equipos_000009 VALUES('','$newConsecutivo','$cCostos')";
    mysql_query($query2, $conex) or die (mysql_errno()." - en el query: ".$query2." - ".mysql_error());

	//txtLog("obtenerNumFacturaTEMP: $newConsecutivo");
    echo $newConsecutivo;
}

function txtLog($txt, $inicializar=false)
    {
        try {
                $l = date('H:i:s', time()) . ' ' . $txt . "\n";
				if ($inicializar)
					file_put_contents('log_la.txt', $l, LOCK_EX);
				else
					file_put_contents('log_la.txt', $l, FILE_APPEND | LOCK_EX);
        } catch (\Exception $e) {
        }
}

?>
<script>
    const number = document.querySelector('.tvc2');
    function formatNumber (n) {
        n = String(n).replace(/\D/g, "");
        return n === '' ? n : Number(n).toLocaleString('en');
    }
    number.addEventListener('focus', (e) => {
        const element = e.target;
    const value = element.value;
    element.value = formatNumber(value);
    })

    const number2 = document.querySelector('.tvd2');
    function formatNumber2 (n) {
        n = String(n).replace(/\D/g, "");
        return n === '' ? n : Number(n).toLocaleString('en');
    }
    number2.addEventListener('focus', (e) => {
        const element = e.target;
    const value2 = element.value;
    element.value = formatNumber2(value2);
    })

    const number3 = document.querySelector('.tvn2');
    function formatNumber3 (n) {
        n = String(n).replace(/\D/g, "");
        return n === '' ? n : Number(n).toLocaleString('en');
    }
    number3.addEventListener('focus', (e) => {
        const element = e.target;
    const value3 = element.value;
    element.value = formatNumber3(value3);
    })
</script>
</body>
</html>