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
            /**
             * Funcion para añadir una nueva columna en la tabla
             */
            $("#add").click(function(){
                // Obtenemos el numero de filas (td) que tiene la primera columna
                // (tr) del id "tblConcept"
                var tds=$("#tblConcept tr:first td").length;
                // Obtenemos el total de columnas (tr) del id "tblConcept"
                var trs=$("#tblConcept tr").length;
                document.getElementById('numRows').value = trs; //llevar a input hidden el numero de rows - WILL
                var nuevaFila="<tr>";
                for(var i=0;i<tds;i++)
                {
                    // añadimos las columnas
                    //nuevaFila+="<td>celda "+(i+1)+" </td>";
                    var idTd = (i+1)+'-'+trs;  //id de cada celda - WILL
                    //nuevaFila+="<td><input type='text' onclick='alert(this.id)' class='inpConcepto' id="+idTd+"></td>"; OK WILL
                    nuevaFila+="<td><input type='text' class='inpConcepto' id="+idTd+"></td>";
                }
                // Añadimos una columna con el numero total de filas.
                // Añadimos uno al total, ya que cuando cargamos los valores para la
                // columna, todavia no esta añadida
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
    </script>
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
    $cCostos = obtenerDatosUsuario(1,$wuse,$conex)
    ?>
</head>

<body>
<div class="panel panel-info contenido">
    <div class="panel-heading encabezado">
        <div class="panel-title titulo1">Matrix - FACTURACION MANUAL SERVINTE</div>
    </div>

    <div align="center" class="panel panel-info divGeneral">
        <div id="tabs">
            <ul>
                <li><a href="#tabs-1">GRABAR</a></li>
                <li><a href="#tabs-2">CONSULTAR</a></li>
            </ul>
            <!-- GRABAR FACTURAS: -->
            <div id="tabs-1">
                <form id="formDatos" name="formDatos">
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
                                        <input type="text" id="numFactu" name="numFactu" class="form-control form-sm" value="<?php obtenerNumFactura($fuente,$cCostos ,$conex_o) ?>"
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
                                        <input type="number" id="plazo" name="plazo" min="0" class="form-control form-sm" value="query" style="width: 90px">

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
                                        <select id="tipoResp" name="tipoResp" class="form-control form-sm" style="width:80px">
                                            <option>E</option>
                                            <option>P</option>
                                        </select>

                                        <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 34px"></span>

                                        <span class="input-group-addon input-sm" style="text-align: left"><label for="nitResp">Nit/Cedula:</label></span>
                                        <input type="text" id="nitResp" name="nitResp" class="form-control form-sm" onblur="buscarDat('verNit',this.value,tipoResp)"
                                               style="width: 180px" required>

                                        <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 3px"></span>

                                        <span class="input-group-addon input-sm"><label for="descResp">Descripcion:</label></span>
                                        <input type="text" id="descResp" name="descResp" class="form-control form-sm" style="width: 200px" readonly>

                                        <span class="input-group-addon input-sm" style="background-color: transparent; border: none; width: 3px"></span>
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
                                <td><label for="detConcep">&ensp;&ensp;&ensp;&nbsp;&nbsp;&nbsp;CONCEPTO</label></td>
                                <td><label for="detCcosto">&ensp;&ensp;&ensp;&nbsp;&nbsp;&nbsp;C. COSTOS</label></td>
                                <td><label for="detValCon">&ensp;&ensp;&ensp;VALOR CONCEPTO</label></td>
                                <td><label for="detValDes">&ensp;&ensp;&ensp;VALOR DESCUENTO</label></td>
                                <td align="right">
                                    <label for="detValNet" style="margin-right: 51px">&ensp;VALOR NETO</label>
                                    <a href="#" id="add" class="btn btn-info btn-xs" title="Adicionar Linea">
                                        <!--<span class="glyphicon glyphicon-plus" onclick="copiarVal(detConcep)"></span>-->
                                        <span class="glyphicon glyphicon-plus" onclick="copiarVal2(numRows)"></span>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td><input type="text" id="1-1" name="detConcep" class="inpConcepto"></td>
                                <td><input type="text" id="2-1" name="detCcosto" class="inpConcepto"></td>
                                <td><input type="text" id="3-1" name="detValCon" onchange="sumValCon(this.value)" class="inpConcepto"></td>
                                <td><input type="text" id="4-1" name="detValDes" class="inpConcepto"></td>
                                <td><input type="text" id="5-1" name="detValNet" class="inpConcepto"></td>
                            </tr>
                        </table>
                    </div>
                    <input type="hidden" id="numRows" name="numRows" value="1">

                    <div style="border: none; margin-top: 10px; text-align: right">
                        <table style="width: 100%" border="0">
                            <tr>
                                <td style="width: 220px">&ensp;</td>
                                <td>
                                    <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;TOTALES:
                                    </label>
                                </td>
                                <td><input type="text" id="totValcon" class="inpTotales"></td>
                                <td><input type="text" id="totValdes" class="inpTotales"></td>
                                <td><input type="text" id="totValnet" class="inpTotales"></td>
                                <td style="width: 37px">&nbsp;</td>
                            </tr>
                        </table>
                    </div>

                    <div style="border: none; margin-top: 15px">
                        <input type="button" class="btn btn-success btn-sm" value="GRABAR">
                    </div>
                </form>
            </div>

            <!-- CONSULTAR E IMPRIMIR FACTURAS: -->
            <div id="tabs-2">
                <h3>CONSULTA E IMPRESION DE FACTURAS</h3>
            </div>
        </div>
    </div>
</div>

<?php
////////////FUNCIONES:

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
    $query1 = "select fuesfu, fuecse from cafue WHERE fuecod = '$fuente' AND fuecco = '$cCostos'";
    $commit1 = odbc_do($conex_o, $query1);
    $fuesfu = odbc_result($commit1, 1);    $fuecse = odbc_result($commit1, 2);

    $query2 = "select * from cafue WHERE fuecod = '$fuesfu' AND fuecco = '$fuecse'";
    $commit2 = odbc_do($conex_o, $query2);
    $fuecod = odbc_result($commit2, 2); $fuesec = odbc_result($commit2,14); $fuecco = odbc_result($commit2, 5);
    $newConsecutivo = $fuesec + 1;

    $query3 = "update cafue set fuesec = '$newConsecutivo' WHERE fuecod = '$fuecod' AND fuecco = '$fuecco'";
    odbc_do($conex_o, $query3);
    echo $newConsecutivo;
}
?>
</body>
</html>