<?php
include("conex.php"); //publicacion Matrix
include("root/comun.php"); //publicacion Matrix
if(!isset($_SESSION['user']))
{
    ?>
    <div align="center" xmlns="http://www.w3.org/1999/html">
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
//include('../MATRIX/include/root/conex.php'); //publicacion local
//include('../MATRIX/include/root/comun.php'); //publicacion local
//mysql_select_db('matrix'); //publicacion local
//$conex = obtenerConexionBD('matrix'); //publicacion local
//$wuse = '0100463';  //publicacion local
//$conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");

$accion = $_GET['accion'];      $nitCed = $_GET['nitCed'];          $tipoResp = $_GET['tipoResp'];
$fuente = $_GET['fuente'];      $fechafac = $_GET['fechafac'];      $plazo = $_GET['plazo'];
$docPac = $_GET['docPac'];      $nomPac = $_GET['nomPac'];
$nitResp = $_GET['nitResp'];    $descResp = $_GET['descResp'];      $tarifa = $_GET['tarifa'];
$tipoServ = $_GET['tipoServ'];
$concepto = $_GET['concep'];    $ccosto = $_GET['ccosto'];          $valcon = $_GET['valcon'];
$valdesc = $_GET['valdesc'];    $valneto = $_GET['valneto'];        $numFac = $_GET['numFac'];
$numRows = $_GET['numRows'];    $chkConcept = $_GET['chkConcept'];
$datoVal = $_GET['dato'];       $parametro = $_GET['parametro'];
$nombreResp = '%'.$_GET['nomresp'].'%';
$field_id = $_GET['field_id'];
//BORRAR DE amefactmp
$concepToDel = $_GET['concepToDel'];    $ccostoToDel = $_GET['ccostoToDel'];    $valconcepToDel = $_GET['valconcepToDel'];
$valDescToDel = $_GET['valDescToDel'];  $valnetoToDel = $_GET['valnetoToDel'];  $fecha_Actual = date('Y/m/d');
$factTemporal = $_GET['factTemporal'];


if($accion == 'verNit')
{
	// buscarDat('verNit',this.value,tipoResp)
    $query1 = "select * from conit WHERE nitnit = '$nitCed'";
    //echo("<br>q:$query1");
    $commit1 = odbc_do($conex_o, $query1);
    $nomResp = odbc_result($commit1, 3);
    //echo("<br>n:$nomResp"); 
    if($nomResp != null)
    {
        if($tipoResp == 'E')
        {
			// Cambio 2021-Abr-22 Luis Meneses. 
			// Por solicitud de usuarios
			// se cambia empcod por empnit al buscar
			// la identificación en la tabla inemp.
            $query2 = "select count(*) from inemp WHERE empnit = '$nitCed' AND empact = 'S'";
			//echo("<br>q2:$query2");
            $commit2 = odbc_do($conex_o, $query2);
            $countInemp = odbc_result($commit2, 1);
			//echo("<br>c:$countInemp");
			/* ?> <script> alert('cons cli'); </script> <?php */
            if($countInemp > 0)
            {
                ?>
                <input type="hidden" id="datoResp" name="datoResp" value="<?php echo $nomResp ?>">
                <script>
                    var docPaciente = opener.document.formDatos.docPac.value;
                    var nomPaciente = opener.document.formDatos.nomPac.value;
                    opener.document.formDatos.descResp.value = document.getElementById('datoResp').value;
                    opener.document.formDatos.descResp.title = document.getElementById('datoResp').value;
                    if(docPaciente == '')
                    {
                        opener.document.formDatos.docPac.value = <?php echo $nitCed ?>;
                        opener.document.formDatos.nomPac.value = document.getElementById('datoResp').value;
                    }
                    window.close();
                </script>
                <?php
            }
            else
            {
                ?>
                <script>
                    opener.document.formDatos.descResp.value = 'RESPONSABLE NO REGISTRADO';
                    opener.document.formDatos.nitResp.value = '';
                    opener.document.formDatos.docPac.value = '';
                    opener.document.formDatos.nomPac.value = '';
                    opener.document.formDatos.nitResp.focus = 'true';
                    window.close();
                    opener.alert('Responsable no registrado en el maestro de empresas(INEMP) o no es tipo empresa(E), por favor verifique');
                </script>
                <?php
            }
        }
        else
        {
            ?>
            <input type="hidden" id="datoResp" name="datoResp" value="<?php echo $nomResp ?>">
            <script>
                var docPaciente = opener.document.formDatos.docPac.value;
                    var nomPaciente = opener.document.formDatos.nomPac.value;
                    opener.document.formDatos.descResp.value = document.getElementById('datoResp').value;
                    opener.document.formDatos.descResp.title = document.getElementById('datoResp').value;
                    if(docPaciente == '')
                    {
                        opener.document.formDatos.docPac.value = <?php echo $nitCed ?>;
                        opener.document.formDatos.nomPac.value = document.getElementById('datoResp').value;
                    }
                    window.close();
            </script>
            <?php
        }
    }
    else
    {
        ?>
        <script>
            opener.document.formDatos.descResp.value = 'RESPONSABLE NO REGISTRADO';
            opener.document.formDatos.descResp.title = 'No se encontro en Unix, debe ser registrado por el area de Contabilidad';
            opener.document.formDatos.nitResp.value = '';
            window.close();
            opener.alert('Responsable no registrado en Unix, debe ser creado en Contabilidad');
        </script>
        <?php
    }
	

}

if($accion == 'saveTempo')
{
    $numRowsToHab = $numRows + 1;   //fila inmediatamente superior a NUMROWS
    ?>
    <script>
    //alert('NUM ROW TO HAB = '+<?php echo $numRowsToHab; ?>);
        //HABILITAR PARA EDITAR LINEA INFERIOR
        fieldConcepto = opener.document.getElementById('1-'+<?php echo $numRowsToHab ?>);       fieldCcosto = opener.document.getElementById('2-'+<?php echo $numRowsToHab ?>);
        fieldValConce = opener.document.getElementById('3-'+<?php echo $numRowsToHab ?>);       fieldValDes = opener.document.getElementById('4-'+<?php echo $numRowsToHab ?>);
        fieldValNeto = opener.document.getElementById('5-'+<?php echo $numRowsToHab ?>);        btnFindCon = opener.document.getElementById('chkConc'+<?php echo $numRowsToHab ?>);
        btnFindCcost = opener.document.getElementById('chkCcos'+<?php echo $numRowsToHab ?>);   btnPercent = opener.document.getElementById('chkPorcent'+<?php echo $numRowsToHab ?>);
        var netoTodo = opener.document.getElementById('totValnet').value;

        fieldConcepto.style.backgroundColor = '#FFFFFF';    fieldConcepto.readOnly = false;
        fieldValConce.style.backgroundColor = '#FFFFFF';    fieldValConce.readOnly = false;
        fieldValDes.style.backgroundColor = '#FFFFFF';      fieldValDes.readOnly = false;
        fieldValNeto.style.backgroundColor = '#FFFFFF';     fieldValNeto.readOnly = false;
        btnFindCon.style.pointerEvents = 'auto';            btnFindCon.style.backgroundColor = '#63B95B';
        btnFindCcost.style.pointerEvents = 'auto';          btnFindCcost.style.backgroundColor = '#63B95B';
        btnPercent.style.pointerEvents = 'auto';            btnPercent.style.backgroundColor = '#52C1E0';
    </script>
    <?php
	
	// Aquí se consulta si el concepto ya existe en la factura
    $queryPre = "select count(*) from amefactmp WHERE fac = '$numFac' AND con = '$concepto'";
    $datoPre = odbc_do($conex_o, $queryPre);
    $contPre = odbc_result($datoPre, 1);

	// Se quitó la validación de no permitir un concepto varias veces en la factura.
	// Para activarla de nuevo, quitar el true en el if
	// OJO: ESTÁ IGUAL EN $accion == 'saveTempo2'.
    if($contPre == 0 || true) 
    {
		/*
		?>
		 <script>
			alert ("insert");
        </script>
		<?php
		*/
        $queryTemp = "insert into amefactmp
                  VALUES('$numFac','$fechafac','$plazo','$docPac','$nomPac','$tipoResp','$nitResp','$tarifa','E',
                         '$concepto','$ccosto','$valcon','$valdesc','$valneto')";
        odbc_do($conex_o, $queryTemp);
    }

    if($numRows == 1)
    {
        ?>
        <script>
            if(netoTodo !== '0')
            {
                opener.document.getElementById('divListo').style.display = 'none';
                opener.document.getElementById('divSave').style.display = 'block';
            }
            window.close();
        </script>
        <?php
    }
    else
    {
        ?>
        <script>
            if(netoTodo !== '0')
            {
                opener.document.getElementById('divListo').style.display = 'none';
                opener.document.getElementById('divSave').style.display = 'block';
            }
            window.close();
        </script>
        <?php
    }
}

if($accion == 'saveTempo2')
{
    $numRowsToHab = $numRows + 1;
    $rowSuperior = $numRows;
    ?>
    <script>
    //alert('ROW SUPERIOR = '+<?php echo $rowSuperior; ?>);
        //HABILITAR PARA EDITAR LINEA SIGUIENTE
        fieldConcepto = opener.document.getElementById('1-'+<?php echo $numRowsToHab ?>);       fieldCcosto = opener.document.getElementById('2-'+<?php echo $numRowsToHab ?>);
        fieldValConce = opener.document.getElementById('3-'+<?php echo $numRowsToHab ?>);       fieldValDes = opener.document.getElementById('4-'+<?php echo $numRowsToHab ?>);
        fieldValNeto = opener.document.getElementById('5-'+<?php echo $numRowsToHab ?>);        btnFindCon = opener.document.getElementById('chkConc'+<?php echo $numRowsToHab ?>);
        btnFindCcost = opener.document.getElementById('chkCcos'+<?php echo $numRowsToHab ?>);   btnPercent = opener.document.getElementById('chkPorcent'+<?php echo $numRowsToHab ?>);
        netoTodo = opener.document.getElementById('5-'+<?php echo $numRowsToHab ?>).value;

        //HABILITAR CAMPOS Y BOTONES FILA SIGUIENTE;
        fieldConcepto.style.backgroundColor = '#FFFFFF';    fieldConcepto.readOnly = false;
        fieldValConce.style.backgroundColor = '#FFFFFF';    fieldValConce.readOnly = false;
        fieldValDes.style.backgroundColor = '#FFFFFF';      fieldValDes.readOnly = false;
        fieldValNeto.style.backgroundColor = '#FFFFFF';     fieldValNeto.readOnly = false;
        btnFindCon.style.pointerEvents = 'auto';            btnFindCon.style.backgroundColor = '#63B95B';
        btnPercent.style.pointerEvents = 'auto';            btnPercent.style.backgroundColor = '#52C1E0';

        //DESHABILITAR CAMPOS Y HABILITAR SOLO 'LIMPIAR' FILA ANTERIOR
        opener.document.getElementById('1-'+<?php echo $rowSuperior ?>).style.backgroundColor = '#CFD0CD';
        opener.document.getElementById('3-'+<?php echo $rowSuperior ?>).style.backgroundColor = '#CFD0CD';
        opener.document.getElementById('4-'+<?php echo $rowSuperior ?>).style.backgroundColor = '#CFD0CD';
        opener.document.getElementById('5-'+<?php echo $rowSuperior ?>).style.backgroundColor = '#CFD0CD';
        opener.document.getElementById('clean'+<?php echo $rowSuperior ?>).style.backgroundColor = '#D43F3A';
        opener.document.getElementById('clean'+<?php echo $rowSuperior ?>).style.pointerEvents = 'auto';
    </script>
    <?php

	// Aquí se consulta si el concepto ya existe en la factura
    $queryPre = "select count(*) from amefactmp WHERE fac = '$numFac' AND con = '$concepto'";
    $datoPre = odbc_do($conex_o, $queryPre);
    $contPre = odbc_result($datoPre, 1);

	// Se quitó la validación de no permitir un concepto varias veces en la factura.
	// Para activarla de nuevo, quitar el true en el if
	// OJO: ESTÁ IGUAL EN $accion == 'saveTempo'.
    if($contPre == 0 || true)
    {
        if($concepto == '2089' or $concepto == '2001' or $concepto == '2021' or $concepto == '2022' or $concepto == '2025' or $concepto == '2078' or $concepto == '2079' or $concepto == '4216' or $concepto == '9819')
        {
            $queryTemp = "insert into amefactmp VALUES('$numFac','$fechafac','$plazo','$docPac','$nomPac','$tipoResp','$nitResp','$tarifa','E',
                     '$concepto','$ccosto','$valcon','$valdesc','$valneto')";
            odbc_do($conex_o, $queryTemp);
            ?>
            <script>
                if(netoTodo !== 0)
                {
                    opener.document.getElementById('divSave').style.display = 'none';
                    opener.document.getElementById('divListo').style.display = 'block';
                    opener.alert('EL CONCEPTO DIGITADO REQUIERE INGRESAR UN CONCEPTO DE IVA');
                }
                window.close();
            </script>
            <?php
        }
        else
        {
            $queryTemp = "insert into amefactmp VALUES('$numFac','$fechafac','$plazo','$docPac','$nomPac','$tipoResp','$nitResp','$tarifa','E',
                     '$concepto','$ccosto','$valcon','$valdesc','$valneto')";
            odbc_do($conex_o, $queryTemp);
            ?>
            <script>
                var row = <?php echo $numRows ?>;
                if(row > 1)
                {
                    var idLess = 'less-'+<?php echo $numRows ?>;
                }
                if(netoTodo !== 0)
                {
                    opener.document.getElementById('divListo').style.display = 'block';
                    opener.document.getElementById('divSave').style.display = 'none';
                }
                window.close()
            </script>
            <?php
        }
    }
    else
    {
        ?>
        <script>
            opener.document.getElementById('divListo').style.display = 'block';
            opener.document.getElementById('divSave').style.display = 'none';
            window.close()
        </script>
        <?php
    }
}

if($accion == 'sumValCon')
{
    $query1 = "select sum(vlrcon) VALCON from amefactmp WHERE fac = '$numFac'";
    $commit1 = odbc_do($conex_o, $query1);
    $sumValCon = odbc_result($commit1,1);
    $sumValCon = $sumValCon + $valcon;

    if($sumValCon > 0)
    {
        ?>
        <script>
            opener.document.formDatos.totValcon.value = <?php echo $sumValCon ?>;
            opener.document.formDatos.totValcon2.value = <?php echo $sumValCon ?>;
            window.close();
        </script>
        <?php
    }
    else
    {
        ?>
        <script>
            opener.document.formDatos.totValcon.value = <?php echo $valcon ?>;
            opener.document.formDatos.totValcon2.value = <?php echo $valcon ?>;
            window.close();
        </script>
        <?php
    }
}

if($accion == 'sumValDes')
{
    $query1 = "select sum(vlrdes) VALCON, sum(vlrneto) VALNETO from amefactmp WHERE fac = '$numFac'";
    $commit1 = odbc_do($conex_o, $query1);
    $sumValDes = odbc_result($commit1,1);   $sumValNet = odbc_result($commit1,2);
    $sumValDes = $sumValDes + $valdesc;

    if($sumValDes > 0)
    {
        ?>
        <script>
            opener.document.formDatos.totValdes.value = <?php echo $sumValDes ?>;
            opener.document.formDatos.totValdes2.value = <?php echo $sumValDes ?>;
            //opener.document.getElementById('add').style.pointerEvents = 'auto';
            //opener.document.getElementById('add').style.backgroundColor = '#5BC0DE';
            window.close();
        </script>
        <?php
    }
    elseif($sumValDes <= 0)
    {
        ?>
        <script>
            opener.document.formDatos.totValdes.value = <?php echo $valdesc ?>;
            opener.document.formDatos.totValdes2.value = <?php echo $valdesc ?>;
            //opener.document.getElementById('add').style.pointerEvents = 'auto';
            //opener.document.getElementById('add').style.backgroundColor = '#5BC0DE';
            window.close();
        </script>
        <?php
    }
    ?>
    <script>
        var valor1 = opener.document.formDatos.totValcon.value;
        var valor2 = opener.document.formDatos.totValdes.value;
        var sumValNet = parseInt(valor1 - valor2);
        opener.document.formDatos.totValnet.value = sumValNet;
        opener.document.formDatos.totValnet2.value = sumValNet;
    </script>
    <?php


}

if($accion == 'chkConcepto')
{
    $query2 = "select sum(vlrneto) from amefactmp
               WHERE fac = '$numFac'
               AND con not in('2023','2088')";
               //AND con not in('2002','2023','2088')";
    $summit2 = odbc_do($conex_o, $query2);
    $valorNeto = odbc_result($summit2,1);

    //$cadena = '%'.'IVA'.'%';
    //$query1 = "select count(*) from facon WHERE concod = '$chkConcept' AND connom LIKE '$cadena'";
    //$commit1 = odbc_do($conex_o, $query1);
    //$conteoFacon = odbc_result($commit1,1);

    $query1 = "select count(*) from equipos_000008 WHERE concepto = '$chkConcept'";
    $commit1 = mysql_query($query1, $conex) or die (mysql_errno()." - en el query: ".$query1." - ".mysql_error());
    $conteoFacon = mysql_fetch_assoc($commit1);

    if($conteoFacon > 0)
    {
        $query1w = "select iva from equipos_000008 WHERE concepto = '$chkConcept'";
        $commit1w = mysql_query($query1w, $conex) or die (mysql_errno()." - en el query: ".$query1w." - ".mysql_error());
        $datoFacon = mysql_fetch_assoc($commit1w);
        $ivaCobrar = $datoFacon['iva'];

        //$ivaCobrar = substr($chkConcept, -2);   //tomar ultimos 2 digitos del concepto para calcular iva a cobrar
        $valConcIva = ($valorNeto * $ivaCobrar)/100;    $valConcIva = round($valConcIva);
        $totValConc = $valorNeto + $valConcIva;

        ?>
        <script>
            opener.document.getElementById(3+'-'+<?php echo $numRows ?>).value = <?php echo $valConcIva ?>;
            //opener.document.getElementById(3+'-'+<?php echo $numRows ?>).readOnly = 'true';
            opener.document.getElementById(4+'-'+<?php echo $numRows ?>).readOnly = 'true';
            opener.document.getElementById('totValcon').value = <?php echo $totValConc ?>;  //actualizar TOTAL VALOR CONCEPTO
            opener.document.getElementById('totValcon2').value = <?php echo $totValConc ?>;  //actualizar TOTAL VALOR CONCEPTO
            opener.document.getElementById(2+'-'+<?php echo $numRows ?>).focus();
            window.close();
        </script>
        <?php
    }
    else
    {
        $query1 = "select count(*) from facon WHERE concod = '$chkConcept'";
        $commit1 = odbc_do($conex_o, $query1);
        $conteoFacon = odbc_result($commit1,1);
        if($conteoFacon == 0)
        {
            ?>
            <script>
                opener.document.getElementById(1+'-'+<?php echo $numRows ?>).value = '';
                window.close();
                opener.alert('CONCEPTO NO ENCONTRADO EN EL MAESTRO DE CONCEPTOS');
                opener.document.getElementById(1+'-'+<?php echo $numRows ?>).focus();
            </script>
            <?php
        }
        else
        {
            ?>
            <script>
                window.close();
                opener.document.getElementById(2+'-'+<?php echo $numRows ?>).focus();
            </script>
            <?php
        }
    }
}

if($accion == 'sumValCon2')
{
    $query1 = "select sum(vlrcon) VALCON from amefactmp WHERE fac = '$numFac'";
    $commit1 = odbc_do($conex_o, $query1);
    $sumValCon = odbc_result($commit1,1);
    $sumValCon = $sumValCon + $valcon;

    $query2 = "select sum(vlrdes) VALDES, sum(vlrneto) VALNETO from amefactmp WHERE fac = '$numFac'";
    $commit2 = odbc_do($conex_o, $query2);
    $sumValDes = odbc_result($commit2,1);   $sumValNet = odbc_result($commit2,2);
    $sumValDes = $sumValDes + $valdesc;

    if($sumValCon > 0)
    {
        ?>
        <script>
            opener.document.formDatos.totValcon.value = <?php echo $sumValCon ?>;
            opener.document.formDatos.totValcon2.value = <?php echo $sumValCon ?>;
            //window.close();
        </script>
        <?php
    }
    else
    {
        ?>
        <script>
            opener.document.formDatos.totValcon.value = <?php echo $valcon ?>;
            opener.document.formDatos.totValcon2.value = <?php echo $valcon ?>;
            //window.close();
        </script>
        <?php
    }
    if($sumValDes > 0)
    {
        ?>
        <script>
            opener.document.formDatos.totValdes.value = <?php echo $sumValDes ?>;
            opener.document.formDatos.totValdes2.value = <?php echo $sumValDes ?>;
            window.close();
        </script>
        <?php
    }
    elseif($sumValDes <= 0)
    {
        ?>
        <script>
            opener.document.formDatos.totValdes.value = <?php echo $valdesc ?>;
            opener.document.formDatos.totValdes2.value = <?php echo $valdesc ?>;
            window.close();
        </script>
        <?php
    }
    ?>
    <script>
        var valor1 = opener.document.formDatos.totValcon.value;
        var valor2 = opener.document.formDatos.totValdes.value;
        var sumValNet = parseInt(valor1 - valor2);
        opener.document.formDatos.totValnet.value = sumValNet;
        opener.document.formDatos.totValnet2.value = sumValNet;
    </script>
    <?php
}

if($accion == 'validarDato')
{
    if($parametro == 1)
    {
        $query1 = "select count(*) from facon WHERE concod = '$datoVal'";
        $commit1 = odbc_do($conex_o, $query1);
        $conteoFacon = odbc_result($commit1,1);
        if($conteoFacon == 0)
        {
            ?>
            <script>
                opener.document.getElementById(1+'-'+<?php echo $numRows ?>).value = '';
                window.close();
                opener.alert('CONCEPTO NO ENCONTRADO EN EL MAESTRO DE CONCEPTOS');
                opener.document.getElementById(1+'-'+<?php echo $numRows ?>).focus();
            </script>
            <?php
        }
        else
        {
            $query11 = "select connom from facon WHERE concod = '$datoVal'";
            $commit11 = odbc_do($conex_o, $query11);
            $nomConc = odbc_result($commit11,1);
            ?>
            <script>
                concepto = opener.document.getElementById(1+'-'+<?php echo $numRows ?>).value; //obtener concepto digitado

                opener.document.getElementById(1+'-'+<?php echo $numRows ?>).title = concepto+'-'+'<?php echo $nomConc ?>'; //adicionar atributo tittle
                opener.document.getElementById('detConcepto'+<?php echo $numRows ?>).value = '<?php echo $nomConc ?>';  //adicionar atributo tittle
                Ccosto = opener.document.getElementById(2+'-'+1).value; //obtener centro de costos de la primera linea (para todas las lineas debe ser el mismo)
                opener.document.getElementById(2+'-'+<?php echo $numRows ?>).value = Ccosto; //replicar centro de costos
                opener.document.getElementById(2+'-'+<?php echo $numRows ?>).readOnly = false; //hacer editable el campo de centro de costos
                opener.document.getElementById(2+'-'+<?php echo $numRows ?>).style.backgroundColor = '#FFFFFF';
                opener.document.getElementById('chkCcos'+<?php echo $numRows ?>).style.pointerEvents = 'auto'; //habilitar el boton busqueda de Ccostos
                opener.document.getElementById('chkCcos'+<?php echo $numRows ?>).style.backgroundColor = '#5CB85C';

                desCost = opener.document.getElementById('detCcosto1').value;  //Obtener descripcion del ccostos primera linea
                opener.document.getElementById('detCcosto'+<?php echo $numRows ?>).value = desCost;  //adicionar atributo tittle

                if(concepto == '2089' || concepto == '2001' || concepto == '2021' || concepto == '2022' || concepto == '2025' || concepto == '2078' || concepto == '2079' || concepto == '4216' || concepto == '9819')
                {
                    var valIva = 19; concepIva = 'on';
                    <?php
                    include("facSer_js.js");
                    ?>
                    //OBTENER LOS VALORES, QUITAR LOS PUNTOS Y ASIGNAR VALOR 0 (CERO) SI ESTE VIENE EN NULL:
                    concepto1 = opener.document.getElementById('1-1').value;
                    if(concepto1 == '2089' || concepto1 == '2001' || concepto1 == '2021' || concepto1 == '2022' || concepto1 == '2025' || concepto1 == '2078' || concepto1 == '2079' || concepto1 == '4216' || concepto1 == '9819' )
                    {
                        var totConcep1 = opener.document.getElementById('5-1').value;   totConcep1 = quita_comas2(totConcep1);  if(totConcep1 == 0){totConcep1 = 0}
                    }
                    else
                    {
                        totConcep1 = 0;
                    }

                    concepto2 = opener.document.getElementById('1-2').value;
                    if(concepto2 == '2089' || concepto2 == '2001' || concepto2 == '2021' || concepto2 == '2022' || concepto2 == '2025' || concepto2 == '2078' || concepto2 == '2079' || concepto2 == '4216' || concepto2 == '9819' )
                    {
                        var totConcep2 = opener.document.getElementById('5-2').value;   totConcep2 = quita_comas2(totConcep2);  if(totConcep2 == 0){totConcep2 = 0}
                    }
                    else
                    {
                        totConcep2 = 0;
                    }

                    concepto3 = opener.document.getElementById('1-3').value;
                    if(concepto3 == '2089' || concepto3 == '2001' || concepto3 == '2021' || concepto3 == '2022' || concepto3 == '2025' || concepto3 == '2078' || concepto3 == '2079' || concepto3 == '4216' || concepto3 == '9819' )
                    {
                        var totConcep3 = opener.document.getElementById('5-3').value;   totConcep3 = quita_comas2(totConcep3);  if(totConcep3 == 0){totConcep3 = 0}
                    }
                    else
                    {
                        totConcep3 = 0;
                    }

                    concepto4 = opener.document.getElementById('1-4').value;
                    if(concepto4 == '2089' || concepto4 == '2001' || concepto4 == '2021' || concepto4 == '2022' || concepto4 == '2025' || concepto4 == '2078' || concepto4 == '2079' || concepto4 == '4216' || concepto4 == '9819' )
                    {
                        var totConcep4 = opener.document.getElementById('5-4').value;   totConcep4 = quita_comas2(totConcep4);  if(totConcep4 == 0){totConcep4 = 0}
                    }
                    else
                    {
                        totConcep4 = 0;
                    }

                    concepto5 = opener.document.getElementById('1-5').value;
                    if(concepto5 == '2089' || concepto5 == '2001' || concepto5 == '2021' || concepto5 == '2022' || concepto5 == '2025' || concepto5 == '2078' || concepto5 == '2079' || concepto5 == '4216' || concepto5 == '9819' )
                    {
                        var totConcep5 = opener.document.getElementById('5-5').value;   totConcep5 = quita_comas2(totConcep5);  if(totConcep5 == 0){totConcep5 = 0}
                    }
                    else
                    {
                        totConcep5 = 0;
                    }

                    //SUMAR VALORES NETOS LINEA POR LINEA:
                    var sumConceptos = parseInt(totConcep1) + parseInt(totConcep2) + parseInt(totConcep3) + parseInt(totConcep4) + parseInt(totConcep5);

                    //OBTENER EL TOTAL NETOS + IVA:
                    var totalConcMasIva = (parseInt(sumConceptos) * parseInt(valIva))/100;

                    //REDONDEAR SI ES UN NUMERO DECIMAL:
                    var totalTodo = totalConcMasIva.toFixed();

                    //ASIGNAR EL VALOR DEL IVA AL CAMPO VALOR CONCEPTO SI ES EL CONCEPTO DE IVA:
                    opener.document.getElementById(3+'-'+<?php echo $numRows ?>).value = totalTodo.toLocaleString();
                }
                else
                {
                    concepIva = 'off';
                    <?php
                    if($numRows > 1)
                    {
                        $rowProx = $numRows + 1;
                        ?>
                        opener.document.getElementById(3+'-'+<?php echo $numRows ?>).focus();
                        opener.document.getElementById(2+'-'+<?php echo $numRows ?>).readOnly = true;
                        <?php
                    }
                    else
                    {
                        ?>
                        opener.document.getElementById(2+'-'+<?php echo $numRows ?>).focus();
                        <?php
                    }
                    ?>
                }
                window.close();
            </script>
            <?php
        }
    }
    if($parametro == 2)
    {
        $query2 = "select count(*) from cocco WHERE ccocod = '$datoVal'";
        $commit2 = odbc_do($conex_o, $query2);
        $conteoCocco = odbc_result($commit2,1);
        if($conteoCocco == 0)
        {
            ?>
            <script>
                opener.document.getElementById(2+'-'+<?php echo $numRows ?>).value = '';
                window.close();
                opener.alert('CENTRO DE COSTOS NO ENCONTRADO');
                opener.document.getElementById(2+'-'+<?php echo $numRows ?>).focus();
            </script>
            <?php
        }
        else
        {
            $query22 = "select cconom from cocco WHERE ccocod = '$datoVal'";
            $commit22 = odbc_do($conex_o, $query22);
            $nomCco = odbc_result($commit22,1);
            ?>
            <script>
                opener.document.getElementById('detCcosto'+<?php echo $numRows ?>).value = '<?php echo $nomCco ?>';
                opener.document.getElementById(2+'-'+<?php echo $numRows ?>).title = '<?php echo $datoVal ?>'+'-'+'<?php echo $nomCco ?>'; //adicionar atributo tittle
                opener.document.getElementById(3+'-'+<?php echo $numRows ?>).focus();
                window.close();
            </script>
            <?php
        }
    }
}

if($accion == 'respxNom')
{
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MAESTRO DE RESPONSABLES SERVINTE - MATRIX</title>
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
    function copiarValor(nombreResp,nitResp)
    {
        opener.document.getElementById('docPac').value = nitResp;
        opener.document.getElementById('nomPac').value = nombreResp;
        opener.document.getElementById('nitResp').value = nitResp;
        opener.document.getElementById('descResp').value = nombreResp;
        window.close();
        opener.document.getElementById('nitResp').focus();
    }
    </script>
    </head>

    <body>
        <?php
            $query = "select count(*) from conit WHERE nitnom LIKE '$nombreResp' AND nitact = 'S'";
            $commit = odbc_do($conex_o,$query);
            $conteo = odbc_result($commit,1);
            if($conteo > 0)
            {
                ?>
                <div class="container">
                    <div class="row">
                        <div class="panel panel-primary filterable">
                            <div class="panel-heading">
                                <h3 class="panel-title">Maestro de Responsables</h3>
                                <div class="pull-right">
                                    <button class="btn btn-default btn-xs btn-filter"><span class="glyphicon glyphicon-filter"></span> Filtro</button>
                                </div>
                            </div>
                            <table class="table">
                                <thead>
                                <tr class="filters">
                                    <th><input type="text" class="form-control" placeholder="Nombre" disabled></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                    $query1 = "select * from conit WHERE nitnom LIKE '$nombreResp' AND nitact = 'S'";
                                    $commit1 = odbc_do($conex_o,$query1);

                                    while($dato1 = odbc_fetch_row($commit1))
                                    {
                                        ?>
                                        <form>
                                            <tr>
                                                <?php $nombre = odbc_result($commit1,3); $nitOced = odbc_result($commit1,1) ?>
                                                <td><label id="lblMedico">
                                                    <a href="#" onclick="copiarValor('<?php echo $nombre ?>','<?php echo $nitOced ?>')">
                                                        <?php echo $nombre ?>
                                                    </a>
                                                </label></td>
                                            </tr>
                                        </form>
                                        <?php
                                    }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
                <?php
            }
            else
            {
                ?>
                <div align="center" style="border: none; margin-top: 30px; display: block">
                    <h3>No se encontro informacion con el dato ingresado...</h3>
                    <script>opener.document.getElementById('descResp').value = '';</script>
                    <br>
                    <input type="button" id="btnListo" class="btn btn-info btn-sm" value="ACEPTAR" onclick="window.close()">
                </div>
                <?php
            }
        ?>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.filterable .btn-filter').click(function(){
                var $panel = $(this).parents('.filterable'),
                    $filters = $panel.find('.filters input'),
                    $tbody = $panel.find('.table tbody');
                if ($filters.prop('disabled') == true) {
                    $filters.prop('disabled', false);
                    $filters.first().focus();
                } else {
                    $filters.val('').prop('disabled', true);
                    $tbody.find('.no-result').remove();
                    $tbody.find('tr').show();
                }
            });

            $('.filterable .filters input').keyup(function(e){
                /* Ignore tab key */
                var code = e.keyCode || e.which;
                if (code == '9') return;
                /* Useful DOM data and selectors */
                var $input = $(this),
                    inputContent = $input.val().toLowerCase(),
                    $panel = $input.parents('.filterable'),
                    column = $panel.find('.filters th').index($input.parents('th')),
                    $table = $panel.find('.table'),
                    $rows = $table.find('tbody tr');
                /* Dirtiest filter function ever ;) */
                var $filteredRows = $rows.filter(function(){
                    var value = $(this).find('td').eq(column).text().toLowerCase();
                    return value.indexOf(inputContent) === -1;
                });
                /* Clean previous no-result if exist */
                $table.find('tbody .no-result').remove();
                /* Show all rows, hide filtered ones (never do that outside of a demo ! xD) */
                $rows.show();
                $filteredRows.hide();
                /* Prepend no-result row if all rows are filtered */
                if ($filteredRows.length === $rows.length) {
                    $table.find('tbody').prepend($('<tr class="no-result text-center"><td colspan="'+ $table.find('.filters th').length +'">No se encontraron resultados</td></tr>'));
                }
            });
        });
    </script>
    </body>
    </html>
    <?php
}

if($accion == 'verConcep')
{
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MAESTRO DE CONCEPTOS SERVINTE - MATRIX</title>
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
    function copiarValor(nombreResp,nitResp)
    {
        opener.document.getElementById('docPac').value = nitResp;
        opener.document.getElementById('nomPac').value = nombreResp;
        opener.document.getElementById('nitResp').value = nitResp;
        opener.document.getElementById('descResp').value = nombreResp;
        window.close();
        opener.document.getElementById('nitResp').focus();
    }
    function copiarValor2(campo,concepto,nombreCon)
    {
        opener.document.getElementById(campo).value = concepto;
        opener.document.getElementById(campo).title = concepto+'-'+nombreCon;
        cCostos = opener.document.getElementById('2-1').value;
        opener.document.getElementById('detConcepto'+<?php echo $numRows ?>).value = nombreCon;
        opener.document.getElementById('2-'+<?php echo $numRows ?>).value = cCostos;
        detCcost = opener.document.getElementById('detCcosto1').value;
        opener.document.getElementById('detCcosto'+<?php echo $numRows ?>).value = detCcost;
        opener.document.getElementById('chkCcos'+<?php echo $numRows ?>).style.pointerEvents = 'auto'; //habilitar el boton busqueda de Ccostos
        opener.document.getElementById('chkCcos'+<?php echo $numRows ?>).style.backgroundColor = '#5CB85C';

         if(concepto == '2089' || concepto == '2001' || concepto == '2021' || concepto == '2022' || concepto == '2025' || concepto == '2078' || concepto == '2079' || concepto == '4216' || concepto == '9819')
         {
                    var valIva = 19; concepIva = 'on';
                    <?php
                    include("facSer_js.js");
                    ?>
                    //OBTENER LOS VALORES, QUITAR LOS PUNTOS Y ASIGNAR VALOR 0 (CERO) SI ESTE VIENE EN NULL:
                    concepto1 = opener.document.getElementById('1-1').value;
                    if(concepto1 == '2089' || concepto1 == '2001' || concepto1 == '2021' || concepto1 == '2022' || concepto1 == '2025' || concepto1 == '2078' || concepto1 == '2079' || concepto1 == '4216' || concepto1 == '9819' )
                    {
                        var totConcep1 = opener.document.getElementById('5-1').value;   totConcep1 = quita_comas2(totConcep1);  if(totConcep1 == 0){totConcep1 = 0}
                    }
                    else
                    {
                        totConcep1 = 0;
                    }

                    concepto2 = opener.document.getElementById('1-2').value;
                    if(concepto2 == '2089' || concepto2 == '2001' || concepto2 == '2021' || concepto2 == '2022' || concepto2 == '2025' || concepto2 == '2078' || concepto2 == '2079' || concepto2 == '4216' || concepto2 == '9819' )
                    {
                        var totConcep2 = opener.document.getElementById('5-2').value;   totConcep2 = quita_comas2(totConcep2);  if(totConcep2 == 0){totConcep2 = 0}
                    }
                    else
                    {
                        totConcep2 = 0;
                    }

                    concepto3 = opener.document.getElementById('1-3').value;
                    if(concepto3 == '2089' || concepto3 == '2001' || concepto3 == '2021' || concepto3 == '2022' || concepto3 == '2025' || concepto3 == '2078' || concepto3 == '2079' || concepto3 == '4216' || concepto3 == '9819' )
                    {
                        var totConcep3 = opener.document.getElementById('5-3').value;   totConcep3 = quita_comas2(totConcep3);  if(totConcep3 == 0){totConcep3 = 0}
                    }
                    else
                    {
                        totConcep3 = 0;
                    }

                    concepto4 = opener.document.getElementById('1-4').value;
                    if(concepto4 == '2089' || concepto4 == '2001' || concepto4 == '2021' || concepto4 == '2022' || concepto4 == '2025' || concepto4 == '2078' || concepto4 == '2079' || concepto4 == '4216' || concepto4 == '9819' )
                    {
                        var totConcep4 = opener.document.getElementById('5-4').value;   totConcep4 = quita_comas2(totConcep4);  if(totConcep4 == 0){totConcep4 = 0}
                    }
                    else
                    {
                        totConcep4 = 0;
                    }

                    concepto5 = opener.document.getElementById('1-5').value;
                    if(concepto5 == '2089' || concepto5 == '2001' || concepto5 == '2021' || concepto5 == '2022' || concepto5 == '2025' || concepto5 == '2078' || concepto5 == '2079' || concepto5 == '4216' || concepto5 == '9819' )
                    {
                        var totConcep5 = opener.document.getElementById('5-5').value;   totConcep5 = quita_comas2(totConcep5);  if(totConcep5 == 0){totConcep5 = 0}
                    }
                    else
                    {
                        totConcep5 = 0;
                    }

                    //SUMAR VALORES NETOS LINEA POR LINEA:
                    var sumConceptos = parseInt(totConcep1) + parseInt(totConcep2) + parseInt(totConcep3) + parseInt(totConcep4) + parseInt(totConcep5);

                    //OBTENER EL TOTAL NETOS + IVA:
                    var totalConcMasIva = (parseInt(sumConceptos) * parseInt(valIva))/100;

                    //REDONDEAR SI ES UN NUMERO DECIMAL:
                    var totalTodo = totalConcMasIva.toFixed();

                    //ASIGNAR EL VALOR DEL IVA AL CAMPO VALOR CONCEPTO SI ES EL CONCEPTO DE IVA:
                    opener.document.getElementById(3+'-'+<?php echo $numRows ?>).value = totalTodo.toLocaleString();
                }
         else
         {
                    concepIva = 'off';
                    <?php
                    if($numRows > 1)
                    {
                        $rowProx = $numRows + 1;
                        ?>
                        opener.document.getElementById(3+'-'+<?php echo $numRows ?>).focus();
                        opener.document.getElementById(2+'-'+<?php echo $numRows ?>).readOnly = true;
                        <?php
                    }
                    else
                    {
                        ?>
                        opener.document.getElementById(2+'-'+<?php echo $numRows ?>).focus();
                        <?php
                    }
                    ?>
                }

        window.close();
    }
    </script>
    </head>

    <body>
        <?php
            $query = "select count(*) from facon WHERE conact = 'S'";
            $commit = odbc_do($conex_o,$query);
            $conteo = odbc_result($commit,1);
            if($conteo > 0)
            {
                ?>
                <div class="container">
                    <div class="row">
                        <div class="panel panel-primary filterable">
                            <div class="panel-heading">
                                <h3 class="panel-title">Maestro de Conceptos</h3>
                                <div class="pull-right">
                                    <button class="btn btn-default btn-xs btn-filter"><span class="glyphicon glyphicon-filter"></span> Filtro</button>
                                </div>
                            </div>
                            <table class="table">
                                <thead>
                                <tr class="filters">
                                    <th><input type="text" class="form-control" placeholder="Nombre" disabled></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                    $query1 = "select * from facon WHERE conact = 'S'";
                                    $commit1 = odbc_do($conex_o,$query1);

                                    while($dato1 = odbc_fetch_row($commit1))
                                    {
                                        ?>
                                        <form>
                                            <tr>
                                                <?php $nombre = odbc_result($commit1,2); $nitOced = odbc_result($commit1,1) ?>
                                                <td><label id="lblMedico">
                                                    <a href="#" onclick="copiarValor2('<?php echo $field_id ?>','<?php echo $nitOced ?>','<?php echo $nombre ?>')">
                                                        <?php echo $nitOced.'-'.$nombre ?>
                                                    </a>
                                                </label></td>
                                            </tr>
                                        </form>
                                        <?php
                                    }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
                <?php
            }
            else
            {
                ?>
                <div align="center" style="border: none; margin-top: 30px; display: block">
                    <h3>No se encontro informacion con el dato ingresado...</h3>
                    <script>opener.document.getElementById('descResp').value = '';</script>
                    <br>
                    <input type="button" id="btnListo" class="btn btn-info btn-sm" value="ACEPTAR" onclick="window.close()">
                </div>
                <?php
            }
        ?>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.filterable .btn-filter').click(function(){
                var $panel = $(this).parents('.filterable'),
                    $filters = $panel.find('.filters input'),
                    $tbody = $panel.find('.table tbody');
                if ($filters.prop('disabled') == true) {
                    $filters.prop('disabled', false);
                    $filters.first().focus();
                } else {
                    $filters.val('').prop('disabled', true);
                    $tbody.find('.no-result').remove();
                    $tbody.find('tr').show();
                }
            });

            $('.filterable .filters input').keyup(function(e){
                /* Ignore tab key */
                var code = e.keyCode || e.which;
                if (code == '9') return;
                /* Useful DOM data and selectors */
                var $input = $(this),
                    inputContent = $input.val().toLowerCase(),
                    $panel = $input.parents('.filterable'),
                    column = $panel.find('.filters th').index($input.parents('th')),
                    $table = $panel.find('.table'),
                    $rows = $table.find('tbody tr');
                /* Dirtiest filter function ever ;) */
                var $filteredRows = $rows.filter(function(){
                    var value = $(this).find('td').eq(column).text().toLowerCase();
                    return value.indexOf(inputContent) === -1;
                });
                /* Clean previous no-result if exist */
                $table.find('tbody .no-result').remove();
                /* Show all rows, hide filtered ones (never do that outside of a demo ! xD) */
                $rows.show();
                $filteredRows.hide();
                /* Prepend no-result row if all rows are filtered */
                if ($filteredRows.length === $rows.length) {
                    $table.find('tbody').prepend($('<tr class="no-result text-center"><td colspan="'+ $table.find('.filters th').length +'">No se encontraron resultados</td></tr>'));
                }
            });
        });
    </script>
    </body>
    </html>
    <?php
}

if($accion == 'verCcosto')
{
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MAESTRO DE CENTROS DE COSTOS SERVINTE - MATRIX</title>
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
    function copiarValor(nombreResp,nitResp)
    {
        opener.document.getElementById('docPac').value = nitResp;
        opener.document.getElementById('nomPac').value = nombreResp;
        opener.document.getElementById('nitResp').value = nitResp;
        opener.document.getElementById('descResp').value = nombreResp;
        window.close();
        opener.document.getElementById('nitResp').focus();
    }
    function copiarValor2(campo,valor,nombreCon)
    {
        opener.document.getElementById(campo).value = valor;
        opener.document.getElementById(campo).title = valor+'-'+nombreCon;
        opener.document.getElementById('detCcosto'+<?php echo $numRows ?>).value = nombreCon;
        opener.document.getElementById(campo).readOnly = 'true';
        window.close();
    }
    </script>
    </head>

    <body>
        <?php
            $query = "select count(*) from cocco WHERE ccoact = 'S'";
            $commit = odbc_do($conex_o,$query);
            $conteo = odbc_result($commit,1);
            if($conteo > 0)
            {
                ?>
                <div class="container">
                    <div class="row">
                        <div class="panel panel-primary filterable">
                            <div class="panel-heading">
                                <h3 class="panel-title">Maestro de Conceptos</h3>
                                <div class="pull-right">
                                    <button class="btn btn-default btn-xs btn-filter"><span class="glyphicon glyphicon-filter"></span> Filtro</button>
                                </div>
                            </div>
                            <table class="table">
                                <thead>
                                <tr class="filters">
                                    <th><input type="text" class="form-control" placeholder="Nombre" disabled></th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                    $query1 = "select * from cocco WHERE ccoact = 'S'";
                                    $commit1 = odbc_do($conex_o,$query1);

                                    while($dato1 = odbc_fetch_row($commit1))
                                    {
                                        ?>
                                        <form>
                                            <tr>
                                                <?php $nombre = odbc_result($commit1,2); $nitOced = odbc_result($commit1,1) ?>
                                                <td><label id="lblMedico">
                                                    <a href="#" onclick="copiarValor2('<?php echo $field_id ?>','<?php echo $nitOced ?>','<?php echo $nombre ?>')">
                                                        <?php echo $nitOced.'-'.$nombre ?>
                                                    </a>
                                                </label></td>
                                            </tr>
                                        </form>
                                        <?php
                                    }
                                ?>
                            </table>
                        </div>
                    </div>
                </div>
                <?php
            }
            else
            {
                ?>
                <div align="center" style="border: none; margin-top: 30px; display: block">
                    <h3>No se encontro informacion con el dato ingresado...</h3>
                    <script>opener.document.getElementById('descResp').value = '';</script>
                    <br>
                    <input type="button" id="btnListo" class="btn btn-info btn-sm" value="ACEPTAR" onclick="window.close()">
                </div>
                <?php
            }
        ?>
    <script type="text/javascript">
        $(document).ready(function(){
            $('.filterable .btn-filter').click(function(){
                var $panel = $(this).parents('.filterable'),
                    $filters = $panel.find('.filters input'),
                    $tbody = $panel.find('.table tbody');
                if ($filters.prop('disabled') == true) {
                    $filters.prop('disabled', false);
                    $filters.first().focus();
                } else {
                    $filters.val('').prop('disabled', true);
                    $tbody.find('.no-result').remove();
                    $tbody.find('tr').show();
                }
            });

            $('.filterable .filters input').keyup(function(e){
                /* Ignore tab key */
                var code = e.keyCode || e.which;
                if (code == '9') return;
                /* Useful DOM data and selectors */
                var $input = $(this),
                    inputContent = $input.val().toLowerCase(),
                    $panel = $input.parents('.filterable'),
                    column = $panel.find('.filters th').index($input.parents('th')),
                    $table = $panel.find('.table'),
                    $rows = $table.find('tbody tr');
                /* Dirtiest filter function ever ;) */
                var $filteredRows = $rows.filter(function(){
                    var value = $(this).find('td').eq(column).text().toLowerCase();
                    return value.indexOf(inputContent) === -1;
                });
                /* Clean previous no-result if exist */
                $table.find('tbody .no-result').remove();
                /* Show all rows, hide filtered ones (never do that outside of a demo ! xD) */
                $rows.show();
                $filteredRows.hide();
                /* Prepend no-result row if all rows are filtered */
                if ($filteredRows.length === $rows.length) {
                    $table.find('tbody').prepend($('<tr class="no-result text-center"><td colspan="'+ $table.find('.filters th').length +'">No se encontraron resultados</td></tr>'));
                }
            });
        });
    </script>
    </body>
    </html>
    <?php
}

if($accion == 'borrarReg')
{
    if($concepToDel != null || $ccostoToDel != null || $valconcepToDel != null || $valDescToDel != null || $valnetoToDel != null)
    {
        $query1 = "delete from amefactmp
                   WHERE fac = '$factTemporal'
                   AND con = '$concepToDel'
                   AND ccos = '$ccostoToDel'
                   AND vlrcon = '$valconcepToDel'
                   AND vlrdes = '$valDescToDel'
                   AND vlrneto = '$valnetoToDel'";
        $commit1 = odbc_do($conex_o, $query1);

        //echo 'EL QUERY = '.$query1;
    }
    ?>
    <script>
        window.close();
    </script>
    <?php
}
?>