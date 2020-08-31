<html>
<head>
<title>BUSQUEDA DE RECIBOS Y NOTAS</title>

<script language="javascript">

     function valorar()
     {

     	//VALIDA SI PARA EL PARAMETRO SECCIONADO ESTAN LOS DATOS INGRESADOS


     	if (document.forma.radio[0].checked)
     	{
     		if ((document.forma.val.value)==(''))
     		{
     			alert('Si selecciona el valor total como el parametro de busqueda debe ingresar un valor');
     			document.forma.bandera.value=0;
     		}
     	}

     	if (document.forma.radio[1].checked)
     	{
     		if ((document.forma.can.value)==(''))
     		{
     			alert('Si selecciona el valor a cancelar como el parametro de busqueda debe ingresar un valor');
     			document.forma.bandera.value=0;
     		}
     	}

     	if (document.forma.radio[2].checked)
     	{
     		if (((document.forma.nomBen1.value)==('')) & ((document.forma.nomBen2.value)==('')) & (document.forma.nomBen3.value)==(''))
     		{
     			alert('Si selecciona el nombre del beneficiario como el parametro de busqueda debe ingresar al menos una parte de su nombre');
     			document.forma.bandera.value=0;
     		}

     	}

     	if (document.forma.radio[3].checked)
     	{
     		if ((document.forma.docBen.value)==(''))
     		{
     			alert('Si selecciona el documento del beneficiario como el parametro de busqueda debe ingresar un documento');
     			document.forma.bandera.value=0;
     		}
     	}

     	if (document.forma.radio[4].checked)
     	{
     		if ((document.forma.docRes.value)==(''))
     		{
     			alert('Si selecciona el documento o nit del responsable como el parametro de busqueda debe ingresar un dato');
     			document.forma.bandera.value=0;
     		}
     	}
     	document.forma.submit();
     }

    </script>

<style type="text/css">
//
body {
	background: white url(portal.gif) transparent center no-repeat scroll;
}

.titulo1 {
	color: #FFFFFF;
	background: #006699;
	font-size: 20pt;
	font-family: Arial;
	font-weight: bold;
	text-align: center;
}

.titulo2 {
	color: #003366;
	background: #A4E1E8;
	font-size: 9pt;
	font-family: Arial;
	font-weight: bold;
	text-align: center;
}

.titulo3 {
	color: #003366;
	background: #57C8D5;
	font-size: 12pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: left;
}

.titulo4 {
	color: #003366;
	font-size: 12pt;
	font-family: Arial;
	font-weight: bold;
	text-align: center;
}

.texto1 {
	color: #006699;
	background: #FFFFFF;
	font-size: 9pt;
	font-family: Tahoma;
	text-align: center;
}

.texto2 {
	color: #006699;
	background: #f5f5dc;
	font-size: 9pt;
	font-family: Tahoma;
	text-align: center;
}

.texto3 {
	color: #006699;
	background: #A4E1E8;
	font-size: 9pt;
	font-weight: bold;
	font-family: Tahoma;
	text-align: center;
}

.texto4 {
	color: #006699;
	background: #FFFFFF;
	font-size: 9pt;
	font-weight: bold;
	font-family: Tahoma;
	text-align: left;
}

.texto5 {
	color: #006699;
	background: #f5f5dc;
	font-size: 9pt;
	font-family: Tahoma;
	text-align: right;
}

.texto6 {
	color: #006699;
	background: #FFFFFF;
	font-size: 9pt;
	font-family: Tahoma;
	text-align: right;
}

.acumulado1 {
	color: #003366;
	background: #FFCC66;
	font-size: 9pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: right;
}

.acumulado2 {
	color: #003366;
	background: #FFCC66;
	font-size: 9pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: center;
}

.acumulado3 {
	color: #003366;
	background: #FFDBA8;
	font-size: 9pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: center;
}

.acumulado4 {
	color: #003366;
	background: #FFDBA8;
	font-size: 9pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: right;
}

.error1 {
	color: #FF0000;
	font-size: 10pt;
	font-family: Tahoma;
	font-weight: bold;
	text-align: center;
}
</style>



</head>
<body>

<?php
include_once("conex.php");

/**
 * REPORTE DE RECIBOS Y NOTAS
 *
 * Este programa permite la busqueda de notas o recibos, por los siguientes parametros:
 * 	Valor
 * 	Nombre del beneficiario
 *  documento del beneficiario
 * 	Documento o nit del responsable
 *  Con la combinacion de estos parametros y entre dos fechas y para la fuente seleccionada, busca cada uno de los documentos que cumplen
 *  con los parametros ingresados, indicando ademas de los parametros la fecha de creacion, las facturas que afecta, los valores a cancelar
 *  y valores en conceptos, estos documentos salen agrupados por centros de costos. En el numero del comentario, hay un link que lleva al programa de recibos y notas (recibos_y_notas.php) para mostrar
 *  el detalle del recibo y tener la posibilidad de imprimirlo.
 *
 *
 * @name matrix\ips\procesos\busquedaRecNot.php
 * @author Carolina Castaño Portilla
 *
 * @created 2007-02-05
 * @version 2007-02-05
 *
 * @modified
 *
 * @table 000018 select
 * @table 000020 select
 * @table 000021 select
 * @table 000022 select
 * @table 000024 select
 * @table 000100 select
 *
 * @wvar $bandera, indica si es la primera vez que se ingresa al programa porque no esta setiada o que no se puede pasar a la segunda pantalla
 * 				   cuando no cumple las validaciones de javascript 	en los parametros de busqueda, teniendo un cero como valor.
 * 					Se pasa a la segunda pantalla cuando toma valor igual a 1
 * @wvar $docBen   documento del beneficiario como parametro de busqueda
 * @wvar $docRes   documento del responsable como parametro de busqueda
 * @wvar $nomBen   nombre del beneficiario como parametro de busqueda
 * @wvar $nomBen1  primer nombre del beneficiario como parametro de busqueda
 * @wvar $nomBen2  segundo nombre del beneficiario como parametro de busqueda
 * @wvar $nomBen3  primer apellido del beneficiario como parametro de busqueda
 * @wvar $nomBen4  segundo apellido del beneficiario como parametro de busqueda
 * @wvar $radio  indica cual de los parametros de busqueda se escogio (radio button)
 * @wvar $resultado vector que indica todos los datos de los documentos encontrados con los parametros de busqueda
 * @wvar $selectFuentes  es la cadena que contiene la lista de fuentes para desplegar en drop down
 * @wvar $val contiene el valor del documento como parametro de busqueda
 * @wvar $wfecfin fecha final como parametro de busqueda
 * @wvar $wfecini fecha inicial como parametro de busqueda
 * @wvar $wfue fuente seleccionada como parametro de busqueda
 * @wvar $wbasedato, nombre de la base de datos mandada por invocacion
 * @wvar $wusuario, codigo del usuario del reporte
 *
 */

//=================================================================================================================================
//////////////////////////////////////////////FUNCIONES DE PRESENTACION/////////////////////////////////////
/**
 * Pinta informacion del programa
 *
 */
function pintarVersionPrograma ()
{
	$wautor="Carolina Castano P.";
	$wversion="2007-02-05";
	echo "<table align='right'>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Autor: ".$wautor."</font></td>";
	echo "</tr>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Version: ".$wversion."</font></td>" ;
	echo "</tr>" ;
	echo "</table></br></br></br>" ;
}

/**
 * pinta el formulario para ingreso de parametros de la busqueda
 *
 */
function pintarParametrosBusqueda ($selectFuentes, $wfecini, $wfecfin)
{
	global $wbasedato;

	echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
	echo "<center><table width='90%'>";
	echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=500 HEIGHT=100></td></tr>";
	echo "<tr><td class='titulo1'>BUSQUEDA DE RECIBOS Y NOTAS</td></tr>";

	//pinto formulario para rango de fechas
	echo "<tr>";

	echo "<td align=center class='texto3'><b>FECHA INICIAL DE FACTURACION: </font></b>";
	campoFecha("wfecini");
	echo "</td>";

	echo "<td align=center class='texto3'><b>FECHA FINAL DE FACTURACION: </font></b>";
	campoFecha("wfecfin");
	echo "</td>";

	echo "</tr>";

	//pinto drop down de fuentes
	echo "<tr><td align=center class='texto3' colspan=2><b>FUENTE: ";
	echo "<select name='wfue'>".$selectFuentes."</select></td>";
	echo "</tr>";

	//espacio
	echo "<tr><tr><td align=center class='texto3' colspan=2><b>&nbsp; ";
	echo "</td>";
	echo "</tr>";

	echo "</table>";

	echo "<center><table  width='90%'>";
	//titulo
	echo "<tr><td align=center class='texto3' colspan=3><b>INGRESE EL PARAMETRO DE BUSQUEDA DESEADO: ";
	echo "</td>";
	echo "</tr>";

	//ingreso de valor
	echo "<tr><td align=center class='texto3' width='10%'>&nbsp;</td><td  class='texto4'  width='70%'><input type='Radio' name='radio' value='VAL' checked><b>VALOR TOTAL DOCUMENTO: ";
	echo "<input type='text' NAME= 'val' value=''></td><td align=center class='texto3'  width='10%'>&nbsp;</td></tr>";

	//ingreso de valor
	echo "<tr><td align=center class='texto3' width='10%'>&nbsp;</td><td  class='texto4'  width='70%'><input type='Radio' name='radio' value='CAN'><b>VALOR A CANCELAR: ";
	echo "<input type='text' NAME= 'can' value=''></td><td align=center class='texto3'  width='10%'>&nbsp;</td></tr>";

	//ingreso de nombre del beneficiario
	echo "<tr><td align=center class='texto3' width='10%'>&nbsp;</td><td  class='texto4'  width='70%'><input type='Radio' name='radio' value='NOMBEN'> <b>NOMBRE DEL BENEFICIARIO: ";
	echo "(Nom1) <input type='text' NAME= 'nomBen1' value='' size='5' >";
	echo " (Nom2) <input type='text' NAME= 'nomBen2' value='' size='5'>";
	echo " (Ape1) <input type='text' NAME= 'nomBen3' value='' size='5'>";
	echo " (Ape2) <input type='text' NAME= 'nomBen4' value='' size='5'></td><td align=center class='texto3' width='10%' >&nbsp;</td></tr>";

	//ingreso de documento del beneficiario
	echo "<tr><td align=center class='texto3' width='10%'>&nbsp;</td><td  class='texto4'  width='70%'><input type='Radio' name='radio' value='DOCBEN'> <b>DOCUMENTO DEL BENEFICIARIO: ";
	echo "<input type='text' NAME= 'docBen' value=''></td><td align=center class='texto3'  width='10%'>&nbsp;</td></tr>";

	//documento o nit del responsable
	echo "<tr><td align=center class='texto3' width='10%'>&nbsp;</td><td  class='texto4'  width='70%'><input type='Radio' name='radio' value='DOCRES'><b>DOCUMENTO O NIT DEL RESPONSABLE: ";
	echo "<input type='text' NAME= 'docRes' value=''></td><td align=center class='texto3'  width='10%'>&nbsp;</td></tr>";

	echo "<input type='HIDDEN' NAME= 'wbasedato' value='".$wbasedato."'>";
	echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";

	echo "<tr>";
	echo "<td align=center class='texto3' colspan=3><input type='submit' value='OK'></td>";
	echo "</tr></table></br></form>";


}


/**
 * Muestra en pantalla los parametros ingresados para la busqueda
 *
 * @param unknown_type $wfecini fecha inicial para el rango donde esta la fecha creacion del documento
 * @param unknown_type $wfecfin fecha final para el rango donde esta la fecha creacion del documento
 * @param unknown_type $wfue fuente seleccionada del docuemnto
 * @param unknown_type $valVar valor del parametro seleccionado para la busqueda (input text)
 * @param unknown_type $nomVar nombre del parametro seleccionado para la busqueda (input radio)
 */
function pintarParametrosSeleccionados($wfecini, $wfecfin, $wfue, $valVar, $nomVar)
{
	global $wbasedato;
	global $wemp_pmla;

	echo "<table  align=center width='60%'>";
	echo "<tr><td>&nbsp;</td></tr>";
	echo "<tr><td align=CENTER><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
	echo "<tr><td>&nbsp;</td></tr>";
	echo "<tr><td><B>Fecha: ".date('Y-m-d')."</B></td></tr>";
	echo "<tr><td><B>REPORTE DE: ".$wfue."</B></td></tr>";
	echo "</tr><td align=right ><A href='busquedaRecNot.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;wbasedato=".$wbasedato."'>VOLVER</A></td></tr>";
	echo "<tr><td><tr><td>Fecha inicial: ".$wfecini."</td></tr>";
	echo "<tr><td>Fecha final: ".$wfecfin."</td></tr>";
	echo "<tr><td>Parametro de busqueda: ".$nomVar.": ".$valVar."</td></tr>";
	echo "</table></br>";
}

/**
 * Despliega en pantalla la informacion de los documentos encontrados, que estan almacenados en un vector llamado resulado
 *
 * @param unknown_type $resultado vector con la info de los documentos encontrados
 * @param unknown_type $wfue fuente seleccionada para los documentos
 */
function pintarResultado($resultado, $wfue)
{
	global $wbasedato;

	if (count($resultado['numDoc'])>=1)
	{
		echo "<table  align =center width='60%'>";
		echo "<tr><td  class='titulo2'>N DOCUMENTO</td>";
		echo "<th align=CENTER class='titulo2'>FECHA</th>";
		echo "<th align=CENTER class='titulo2'>CENTRO DE COSTOS</th>";
		echo "<th align=CENTER class='titulo2'>VLR DOCUMENTO</th>";
		echo "<th align=CENTER class='titulo2'>RESPONSABLE</th>";
		echo "<th align=CENTER class='titulo2'>FUENTE FACTURA</th>";
		echo "<th align=CENTER class='titulo2'>NRO FACTURA</th>";
		echo "<th align=CENTER class='titulo2'>BENFICIARIO</th></tr>";

		for ($i=1;$i<=count($resultado['numDoc']);$i++)
		{
			if (is_int ($i/2))
			{
				$clase1="class='texto1'";
				$clase2="class='texto6'";
			}
			else
			{
				$clase1="class='texto2'";
				$clase2="class='texto5'";
			}

			echo "<TR><th  ".$clase1." ><A href='/MATRIX/IPS/PROCESOS/RecibosNotasN.php?wnrodoc=".strtoupper($resultado['numDoc'][$i])."&amp;wfcon=".$wfue."&amp;wbasedato=".$wbasedato."&amp;wcco2=".$resultado['ccoDoc'][$i]."&amp;bandera=1&amp;wbuscador= ' TARGET='new' >".$resultado['numDoc'][$i]."</a></th>";
			echo "<th  ".$clase1." >".$resultado['fec'][$i]."</th>";
			echo "<th  ".$clase1." >".$resultado['ccoDoc'][$i]."</th>";
			echo "<th  $clase2  >".number_format($resultado['valDoc'][$i],0,'.',',')."</th>";
			echo "<th ".$clase1." >".$resultado['emp'][$i]."</th>";

			for ($j=1;$j<=count($resultado['numFac'][$i]);$j++)
			{
				if ($j!=1)
				{
					echo "<TR><th  ".$clase1." >&nbsp</th>";
					echo "<th  ".$clase1." >&nbsp</th>";
					echo "<th  ".$clase1." >&nbsp</th>";
					echo "<th  $clase2  >&nbsp</th>";
					echo "<th ".$clase1." >&nbsp</th>";

				}
				echo "<th ".$clase1.">".$resultado['numFac'][$i][$j]."</th>";
				echo "<th  ".$clase1.">".$resultado['fueFac'][$i][$j]."</th>";
				echo "<th  ".$clase1.">".$resultado['ben'][$i][$j]."</th></tr><tr>";

			}


		}
		echo "</table>";
	}
	else
	{
		echo "<fieldset style='border:solid;border-color:#000080; width=330' ; color=#000080 align='center'>";
		echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
		echo "<tr><td colspan='2' ALIGN=CENTER><font size=3 color='#000080' face='arial' ALIGN=CENTER><b>NO SE HAN ENCONTRADO DOCUMENTOS CON LOS PARAMETROS SELECCIONADOS</td><tr>";
		echo "<tr><td colspan='2' align='center'></td><tr>";
		echo "</table></fieldset>";
	}
}

////////////////////////////////////////////////FUNCIONES DE PERSISTENCIA///////////////////////////////////

/**
 * Organiza el select de fuentes existentes para recibos y notas
 *
 * @param unknown_type $wusuario tipo de susuario
 * @return $selectFuentes
 */
function prepararSelectFuentes()
{
	global $wbasedato;
	global $conex;
	//consulto las fuentes y armo el select
	$selectFuentes='';

	//recibos y abonos
	$q= "   SELECT carfue, cardes "
	."     FROM ".$wbasedato."_000040 "
	."      where carrec ='on' and carest='on' ";


	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	for ($i=1;$i<=$num1;$i++)
	{
		$row1 = mysql_fetch_array($res1);
		$selectFuentes=$selectFuentes."<option>".$row1[0]." - ".$row1[1]."</option>";
	}

	//notas creditos
	$q= "   SELECT carfue, cardes "
	."     FROM ".$wbasedato."_000040 "
	."     where carncr ='on' and carest='on' ";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	for ($i=1;$i<=$num1;$i++)
	{
		$row1 = mysql_fetch_array($res1);
		$selectFuentes=$selectFuentes. "<option>".$row1[0]." - ".$row1[1]."</option>";
	}

	//notas debito
	$q= "   SELECT carfue, cardes  "
	."     FROM ".$wbasedato."_000040 "
	."      where carndb ='on' and carest='on' ";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);
	for ($i=1;$i<=$num1;$i++)
	{
		$row1 = mysql_fetch_array($res1);
		$selectFuentes=$selectFuentes. "<option>".$row1[0]." - ".$row1[1]."</option>";
	}

	return $selectFuentes;

}


/**
 * Consulta entre dos fechas un documento de una fuente determinada y con un valor determinado
 *
 * @param unknown_type $wfecini
 * @param unknown_type $wfecfin
 * @param unknown_type $wfue
 * @param unknown_type $val
 * @return $resultado vector con los datos del documento para mostrar en pantalla
 */
function consultarXVal($wfecini, $wfecfin, $wfue, $val)
{
	global $wbasedato;
	global $conex;

	//organizo la fuente
	$print=explode ('-',$wfue);
	$wfue=$print[0];

	//busco los documentos con ese valor y sus facturas
	$query= "select rennum, renfec, rencco, renvca, empnit, empraz "
	.      "from  ".$wbasedato."_000020, ".$wbasedato."_000024 "
	.      " where renest='on' and renvca='".$val."' and renfue='".$wfue."' and renfec between  '".$wfecini."' and '".$wfecfin."' "
	.      " and empcod=rencod and empest='on' "
	.      " order by rennum ";

	$res = mysql_query($query,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);  //asumiendo que solo hay un envio
			$resultado['numDoc'][$i]=$row['rennum'];
			$resultado['fec'][$i]=$row['renfec'];
			$resultado['ccoDoc'][$i]=$row['rencco'];
			$resultado['valDoc'][$i]=$row['renvca'];
			$resultado['emp'][$i]=$row['empnit'].'-'.$row['empraz'];

			//busco las facturas con su responsable
			$query= "select rdefac, rdeffa, rdehis "
			.      "from  ".$wbasedato."_000021"
			.      " where rdeest='on' and rdefue='".$wfue."' and rdenum='".$resultado['numDoc'][$i]."' and rdecco='".$resultado['ccoDoc'][$i]."' "
			.      " order by rdefac ";


			$res2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($res2);

			for ($j=1;$j<=$num2;$j++)
			{
				$row2 = mysql_fetch_array($res2);
				$resultado['numFac'][$i][$j]=$row2['rdefac'];
				$resultado['fueFac'][$i][$j]=$row2['rdeffa'];

				$query= "select pacdoc, pactdo, pacno1, pacno2, pacap1, pacap2 "
				.      "from   ".$wbasedato."_000100 "
				.      " where pachis='".$row2['rdehis']."' ";

				$res3 = mysql_query($query,$conex);
				$row3 = mysql_fetch_array($res3);
				$resultado['ben'][$i][$j]=$row3['pacdoc'].'-'.$row3['pactdo'].'-'.$row3['pacno1'].' '.$row3['pacno2'].' '.$row3['pacap1'].' '.$row3['pacap2'];
			}
		}
	}
	else
	{
		$resultado=false;
	}
	return $resultado;
}


/**
 * Consulta entre dos fechas un documento de una fuente determinada y con un valor a cancelar determinado
 *
 * @param unknown_type $wfecini
 * @param unknown_type $wfecfin
 * @param unknown_type $wfue
 * @param unknown_type $val
 * @return $resultado vector con los datos del documento para mostrar en pantalla
 */
function consultarXCan($wfecini, $wfecfin, $wfue, $can)
{
	global $wbasedato;
	global $conex;

	//organizo la fuente
	$print=explode ('-',$wfue);
	$wfue=$print[0];

	//busco los documentos con ese valor
	$query= "select rdenum, Sum(rdevca), renfec, rdecco, renvca, empnit, empraz "
	.      "from  ".$wbasedato."_000021, ".$wbasedato."_000020, ".$wbasedato."_000024"
	.      " where rdeest='on' and rdefue='".$wfue."' "
	.      " and rdenum=rennum and rdefue=renfue and rdecco=rencco and renfec between  '".$wfecini."' and '".$wfecfin."' "
	.      " and empcod=rencod and empest='on' "
	.      " group by rdenum, rdefue, rdecco having Sum(rdevca)='".$can."' ";


	$res = mysql_query($query,$conex);
	$num = mysql_num_rows($res);

	if ($num>0)
	{
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);  //asumiendo que solo hay un envio
			$resultado['numDoc'][$i]=$row['rdenum'];
			$resultado['fec'][$i]=$row['renfec'];
			$resultado['ccoDoc'][$i]=$row['rdecco'];
			$resultado['valDoc'][$i]=$row['renvca'];
			$resultado['emp'][$i]=$row['empnit'].'-'.$row['empraz'];

			//busco las facturas con su responsable
			$query= "select rdefac, rdeffa, rdehis "
			.      "from  ".$wbasedato."_000021"
			.      " where rdeest='on' and rdefue='".$wfue."' and rdenum='".$resultado['numDoc'][$i]."' and rdecco='".$resultado['ccoDoc'][$i]."' "
			.      " order by rdefac ";

			$res2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($res2);

			for ($j=1;$j<=$num2;$j++)
			{
				$row2 = mysql_fetch_array($res2);
				$resultado['numFac'][$i][$j]=$row2['rdefac'];
				$resultado['fueFac'][$i][$j]=$row2['rdeffa'];

				$query= "select pacdoc, pactdo, pacno1, pacno2, pacap1, pacap2 "
				.      "from   ".$wbasedato."_000100 "
				.      " where pachis='".$row2['rdehis']."' ";

				$res3 = mysql_query($query,$conex);
				$row3 = mysql_fetch_array($res3);
				$resultado['ben'][$i][$j]=$row3['pacdoc'].'-'.$row3['pactdo'].'-'.$row3['pacno1'].' '.$row3['pacno2'].' '.$row3['pacap1'].' '.$row3['pacap2'];
			}
		}
	}
	else
	{
		$resultado=false;
	}
	return $resultado;
}



/**
 * Consulta entre dos fechas un documento de una fuente determinada y documento de beneficiario determinado
 *
 * @param unknown_type $wfecini
 * @param unknown_type $wfecfin
 * @param unknown_type $wfue
 * @param unknown_type $docBen
 * @return unknown
 */
function consultarXDocBen($wfecini, $wfecfin, $wfue, $docBen)
{
	global $wbasedato;
	global $conex;

	//organizo la fuente
	$print=explode ('-',$wfue);
	$wfue=$print[0];

	//primero busco la historia clinica del benficiario y su nombre completo y tipo de documento
	$query= "select pachis "
	.      "from   ".$wbasedato."_000100 "
	.      " where pacdoc='".$docBen."' ";

	$res3 = mysql_query($query,$conex);
	$row3 = mysql_fetch_array($res3);
	$num3 = mysql_num_rows($res3);

	if ($num3>0)
	{

		//busco los documentos para esa historia, entre dos fechas
		$query= "select distinct rdenum, rdecco "
		.      "from  ".$wbasedato."_000021"
		.      " where rdeest='on' and rdefue='".$wfue."' and rdehis='".$row3['pachis']."' and fecha_data between '".$wfecini."' and '".$wfecfin."' "
		.      " order by rdenum ";
		$res = mysql_query($query,$conex);
		$num = mysql_num_rows($res);
		if ($num>0)
		{
			for ($i=1;$i<=$num;$i++)
			{
				$row = mysql_fetch_array($res);

				$query= "select rennum, renfec, rencco, renvca, empnit, empraz "
				.      "from  ".$wbasedato."_000020, ".$wbasedato."_000024 "
				.      " where renest='on' and rennum='".$row['rdenum']."' and renfue='".$wfue."' and rencco='".$row['rdecco']."' "
				.      " and empcod=rencod and empest='on' "
				.      " order by rennum ";

				$res4 = mysql_query($query,$conex);
				$row4 = mysql_fetch_array($res4);

				$resultado['numDoc'][$i]=$row4['rennum'];
				$resultado['fec'][$i]=$row4['renfec'];
				$resultado['ccoDoc'][$i]=$row4['rencco'];
				$resultado['valDoc'][$i]=$row4['renvca'];
				$resultado['emp'][$i]=$row4['empnit'].'-'.$row4['empraz'];

				//busco las facturas con su beneficiario
				$query= "select rdefac, rdeffa, rdehis "
				.      "from  ".$wbasedato."_000021"
				.      " where rdeest='on' and rdefue='".$wfue."' and rdenum='".$resultado['numDoc'][$i]."' and rdecco='".$resultado['ccoDoc'][$i]."' "
				.      " order by rdefac ";


				$res2 = mysql_query($query,$conex);
				$num2 = mysql_num_rows($res2);

				for ($j=1;$j<=$num2;$j++)
				{
					$row2 = mysql_fetch_array($res2);
					$resultado['numFac'][$i][$j]=$row2['rdefac'];
					$resultado['fueFac'][$i][$j]=$row2['rdeffa'];

					$query= "select pacdoc, pactdo, pacno1, pacno2, pacap1, pacap2 "
					.      "from   ".$wbasedato."_000100 "
					.      " where pachis='".$row2['rdehis']."' ";

					$res3 = mysql_query($query,$conex);
					$row3 = mysql_fetch_array($res3);
					$resultado['ben'][$i][$j]=$row3['pacdoc'].'-'.$row3['pactdo'].'-'.$row3['pacno1'].' '.$row3['pacno2'].' '.$row3['pacap1'].' '.$row3['pacap2'];
				}
			}
		}
		else
		{
			$resultado=false;
		}
	}
	else
	{
		$resultado=false;
	}
	return $resultado;
}

/**
 * Consulta entre dos fechas un documento de una fuente determinada y con un nombre de beneficiario determinado
 *
 * @param unknown_type $wfecini
 * @param unknown_type $wfecfin
 * @param unknown_type $wfue
 * @param unknown_type $nomBen1
 * @param unknown_type $nomBen2
 * @param unknown_type $nomBen3
 * @param unknown_type $nomBen4
 * @return unknown
 */
function consultarXNomBen($wfecini, $wfecfin, $wfue, $nomBen1, $nomBen2, $nomBen3, $nomBen4)
{
	global $wbasedato;
	global $conex;

	//organizo la fuente
	$print=explode ('-',$wfue);
	$wfue=$print[0];

	//primero busco la historia clinica del benficiario y su nombre completo y tipo de documento
	$query= "select pachis "
	.      "from   ".$wbasedato."_000100 "
	.      " where  ";

	if ($nomBen1!='')
	{
		$query=$query. "pacno1='".strtoupper($nomBen1)."' and ";
	}

	if ($nomBen2!='')
	{
		$query=$query. "pacno2='".strtoupper($nomBen2)."' and ";
	}

	if ($nomBen3!='')
	{
		$query=$query. "pacap1='".strtoupper($nomBen3)."' and ";
	}

	if ($nomBen4!='')
	{
		$query=$query. "pacap2='".strtoupper($nomBen4)."' and ";
	}

	$query=substr($query,0,-4);

	$res3 = mysql_query($query,$conex);
	$num3 = mysql_num_rows($res3);

	if ($num3>0)
	{
		$contador=1;
		for ($i=1;$i<=$num3;$i++)
		{
			$row3 = mysql_fetch_array($res3);

			//busco los documentos para esa historia, entre dos fechas
			$query= "select distinct rdenum, rdecco "
			.      "from  ".$wbasedato."_000021"
			.      " where rdeest='on' and rdefue='".$wfue."' and rdehis='".$row3['pachis']."' and fecha_data between '".$wfecini."' and '".$wfecfin."' "
			.      " order by rdenum ";
			$res = mysql_query($query,$conex);
			$num = mysql_num_rows($res);

			if ($num>0)
			{
				for ($j=1;$j<=$num;$j++)
				{
					$row = mysql_fetch_array($res);
					$vector['num'][$contador]=$row['rdenum'];
					$vector['cco'][$contador]=$row['rdecco'];
					$contador++;
				}
			}
		}
	}



	if (isset ($vector) and count($vector['num'])>=1)
	{
		for ($i=1;$i<=count($vector['num']);$i++)
		{
			$query= "select rennum, renfec, rencco, renvca, empnit, empraz "
			.      "from  ".$wbasedato."_000020, ".$wbasedato."_000024 "
			.      " where renest='on' and rennum='".$vector['num'][$i]."' and renfue='".$wfue."' and rencco='".$vector['cco'][$i]."' "
			.      " and empcod=rencod and empest='on' "
			.      " order by rennum ";

			$res4 = mysql_query($query,$conex);
			$row4 = mysql_fetch_array($res4);

			$resultado['numDoc'][$i]=$row4['rennum'];
			$resultado['fec'][$i]=$row4['renfec'];
			$resultado['ccoDoc'][$i]=$row4['rencco'];
			$resultado['valDoc'][$i]=$row4['renvca'];
			$resultado['emp'][$i]=$row4['empnit'].'-'.$row4['empraz'];

			//busco las facturas con su beneficiario
			$query= "select rdefac, rdeffa, rdehis "
			.      "from  ".$wbasedato."_000021"
			.      " where rdeest='on' and rdefue='".$wfue."' and rdenum='".$resultado['numDoc'][$i]."' and rdecco='".$resultado['ccoDoc'][$i]."' "
			.      " order by rdefac ";


			$res2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($res2);

			for ($j=1;$j<=$num2;$j++)
			{
				$row2 = mysql_fetch_array($res2);
				$resultado['numFac'][$i][$j]=$row2['rdefac'];
				$resultado['fueFac'][$i][$j]=$row2['rdeffa'];

				$query= "select pacdoc, pactdo, pacno1, pacno2, pacap1, pacap2 "
				.      "from   ".$wbasedato."_000100 "
				.      " where pachis='".$row2['rdehis']."' ";

				$res3 = mysql_query($query,$conex);
				$row3 = mysql_fetch_array($res3);
				$resultado['ben'][$i][$j]=$row3['pacdoc'].'-'.$row3['pactdo'].'-'.$row3['pacno1'].' '.$row3['pacno2'].' '.$row3['pacap1'].' '.$row3['pacap2'];
			}
		}
	}
	else
	{
		$resultado=false;
	}
	return $resultado;
}

/**
 * Consulta entre dos fechas un documento de una fuente determinada y con un documento o nit de responsable determinado
 *
 * @param unknown_type $wfecini
 * @param unknown_type $wfecfin
 * @param unknown_type $wfue
 * @param unknown_type $docRes
 * @return unknown
 */
function consultarXDocRes($wfecini, $wfecfin, $wfue, $docRes)
{
	global $wbasedato;
	global $conex;

	//organizo la fuente
	$print=explode ('-',$wfue);
	$wfue=$print[0];

	//primero busco si existe el nit ingresado como empresa y meto en for los codigos para ese nit
	$query= "select empcod "
	.      "from   ".$wbasedato."_000024 "
	.      " where empnit='".$docRes."' and empest='on' ";

	$res3 = mysql_query($query,$conex);
	$num3 = mysql_num_rows($res3);

	if ($num3>0)
	{
		$contador=1;
		for ($i=1;$i<=$num3;$i++)
		{
			$row3 = mysql_fetch_array($res3);

			//busco los documentos para esa historia, entre dos fechas
			$query= "select distinct rennum, rencco "
			.      "from  ".$wbasedato."_000020"
			.      " where renest='on' and renfue='".$wfue."' and rencod='".$row3['empcod']."' and renfec between '".$wfecini."' and '".$wfecfin."' "
			.      " order by rennum ";
			$res = mysql_query($query,$conex);
			$num = mysql_num_rows($res);

			if ($num>0)
			{
				for ($j=1;$j<=$num;$j++)
				{
					$row = mysql_fetch_array($res);
					$vector['num'][$contador]=$row['rennum'];
					$vector['cco'][$contador]=$row['rencco'];
					$contador++;
				}
			}
		}
	}
	else
	{
		//lo busco como un particular en la tabla 18 facturas para ese responsable
		$query= "select fenfac, fenffa, fencco "
		.      "from   ".$wbasedato."_000018 "
		.      " where fendpa='".$docRes."' and fenest='on' and fennit='99999' ";

		$res3 = mysql_query($query,$conex);
		$num3 = mysql_num_rows($res3);


		if ($num3>0)
		{
			$contador=1;
			for ($i=1;$i<=$num3;$i++)
			{
				$row3 = mysql_fetch_array($res3);

				//busco los documentos para esas facturas, entre dos fechas
				$query= "select distinct rdenum, rdecco "
				.      "from  ".$wbasedato."_000021"
				.      " where rdeest='on' and rdefue='".$wfue."' and rdefac='".$row3['fenfac']."' and rdeffa='".$row3['fenffa']."' and rdecco='".$row3['fencco']."' and fecha_data between '".$wfecini."' and '".$wfecfin."' "
				.      " order by rdenum ";
				$res = mysql_query($query,$conex);
				$num = mysql_num_rows($res);

				if ($num>0)
				{
					for ($j=1;$j<=$num;$j++)
					{
						$row = mysql_fetch_array($res);
						$vector['num'][$contador]=$row['rdenum'];
						$vector['cco'][$contador]=$row['rdecco'];
						$contador++;
					}
				}
			}
		}

	}

	if (isset($vector) and  count($vector['num'])>=1)
	{
		for ($i=1;$i<=count($vector['num']);$i++)
		{
			$query= "select rennum, renfec, rencco, renvca, empnit, empraz "
			.      "from  ".$wbasedato."_000020, ".$wbasedato."_000024 "
			.      " where renest='on' and rennum='".$vector['num'][$i]."' and renfue='".$wfue."' and rencco='".$vector['cco'][$i]."' "
			.      " and empcod=rencod and empest='on' "
			.      " order by rennum ";

			$res4 = mysql_query($query,$conex);
			$row4 = mysql_fetch_array($res4);

			$resultado['numDoc'][$i]=$row4['rennum'];
			$resultado['fec'][$i]=$row4['renfec'];
			$resultado['ccoDoc'][$i]=$row4['rencco'];
			$resultado['valDoc'][$i]=$row4['renvca'];
			$resultado['emp'][$i]=$row4['empnit'].'-'.$row4['empraz'];

			//busco las facturas con su beneficiario
			$query= "select rdefac, rdeffa, rdehis "
			.      "from  ".$wbasedato."_000021"
			.      " where rdeest='on' and rdefue='".$wfue."' and rdenum='".$resultado['numDoc'][$i]."' and rdecco='".$resultado['ccoDoc'][$i]."' "
			.      " order by rdefac ";


			$res2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($res2);

			for ($j=1;$j<=$num2;$j++)
			{
				$row2 = mysql_fetch_array($res2);
				$resultado['numFac'][$i][$j]=$row2['rdefac'];
				$resultado['fueFac'][$i][$j]=$row2['rdeffa'];

				$query= "select pacdoc, pactdo, pacno1, pacno2, pacap1, pacap2 "
				.      "from   ".$wbasedato."_000100 "
				.      " where pachis='".$row2['rdehis']."' ";

				$res3 = mysql_query($query,$conex);
				$row3 = mysql_fetch_array($res3);
				$resultado['ben'][$i][$j]=$row3['pacdoc'].'-'.$row3['pactdo'].'-'.$row3['pacno1'].' '.$row3['pacno2'].' '.$row3['pacap1'].' '.$row3['pacap2'];
			}
		}
	}
	else
	{
		$resultado=false;
	}
	return $resultado;
}

//////////////////////////////////////////PROGRAMA O CONTROLADOR///////////////////////////////////////////////////
include_once("root/comun.php");

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}

	$wusuario = substr($user,2,strlen($user));

	$conex = obtenerConexionBD("matrix");

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato = strtolower($institucion->baseDeDatos);
	$wentidad = $institucion->nombre;

	pintarVersionPrograma();

	echo "<form action='BusquedaRecNot.php' method='post' name='forma' onsubmit='javascript:valorar()'>";
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
	//no pasa a la pantalla de resultados
	if (!isset($wfecini) or !isset($radio) or (isset($bandera) and $bandera==0))
	{
		$wfecha=date("Y-m-d");

		//Adecuo las fechas en caso de que no hayan sido ingresadas,
		if (!isset ($wfecini))
		{
			$wfecini=$wfecha;
			$wfecfin=$wfecha;
		}

		$selectFuentes=prepararSelectFuentes();
		pintarParametrosBusqueda($selectFuentes, $wfecini, $wfecfin);
	}
	else //MUESTRA LOS DOCUMENTOS SEGUN LOS PARAMETROS DE BUSQUEDA
	{
		switch ($radio)
		{
			case 'VAL':
				pintarParametrosSeleccionados($wfecini, $wfecfin, $wfue, number_format($val,0,'.',',').'$', 'VALOR TOTAL DEL DOCUMENTO');
				$resultado=consultarXVal($wfecini, $wfecfin, $wfue, $val);
				break;

			case 'CAN':
				pintarParametrosSeleccionados($wfecini, $wfecfin, $wfue, number_format($can,0,'.',',').'$', 'VALOR A CANCELAR');
				$resultado=consultarXCan($wfecini, $wfecfin, $wfue, $can);
				break;

			case 'NOMBEN':
				$nomBen=$nomBen1.' '.$nomBen2.' '.$nomBen3.' '.$nomBen4;
				pintarParametrosSeleccionados($wfecini, $wfecfin, $wfue, $nomBen, 'NOMBRE DEL BENEFICIARIO');
				$resultado=consultarXNomBen($wfecini, $wfecfin, $wfue, $nomBen1, $nomBen2, $nomBen3, $nomBen4);
				break;

			case 'DOCBEN':
				pintarParametrosSeleccionados($wfecini, $wfecfin, $wfue, $docBen, 'DOCUMENTO DEL BENEFICIARIO');
				$resultado=consultarXDocBen($wfecini, $wfecfin, $wfue,  $docBen);
				break;

			case 'DOCRES':
				pintarParametrosSeleccionados($wfecini, $wfecfin, $wfue,  $docRes, 'DOCUMENTO DEL RESPONSABLE');
				$resultado=consultarXDocRes($wfecini, $wfecfin, $wfue, $docRes);
				break;
		}

		pintarResultado($resultado, $wfue);
	}

}
?>
</body>
</html>
