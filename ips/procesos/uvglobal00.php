<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	Tarjeta de dispositivo medico implantable
 * Fecha		:	2015-10-04
 * Por			:	Felipe Alvarez Sanchez
 * Descripcion	:
 * Condiciones  :
 *********************************************************************************************************

 Actualizaciones:


 **********************************************************************************************************/

$wactualiz = "2015-10-06";

if(!isset($_SESSION['user'])){
	echo "error";
	return;
}
//Para que las respuestas ajax acepten tildes y caracteres especiales
header('Content-type: text/html;charset=ISO-8859-1');

if( isset($consultaAjax) == false ){

?>
	<html>
	<head>
	<title>Ordenes Laboratorio</title>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<link type="text/css" href="../../../include/root/smartpaginator.css" rel="stylesheet" /> <!-- Autocomplete -->

	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
	<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
	<script src="../../../include/root/jquery.maskedinput.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
	<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
	<script type='text/javascript' src='../../../include/root/smartpaginator.js'></script>	<!-- Autocomplete -->

	<style>

		.tborder
		{
			/*border: solid black;*/
		}
		.visibilidad
		{
			display:none;
		}

		.campoObligatorio{
			border-style:solid;
			border-color:red;
			border-width:1px;
		}

		fieldset{
			border: 2px solid #e0e0e0;
		}
		legend{
			border: 2px solid #e0e0e0;
			border-top: 0px;
			font-family: Verdana;
			background-color: #e6e6e6;
			font-size: 16pt;
		}

		.ui-autocomplete{
			max-width: 	250px;
			max-height: 150px;
			overflow-y: auto;
			overflow-x: hidden;
			font-size: 	8pt;
		}

		// --> Estylo para los placeholder
		/*Chrome*/
		[tipo=obligatorio]::-webkit-input-placeholder {color:gray; background:lightyellow;font-size:8pt}
		/*Firefox*/
		[tipo=obligatorio]::-moz-placeholder {color:#000000; background:lightyellow;font-size:8pt}
		/*Interner E*/
		[tipo=obligatorio]:-ms-input-placeholder {color:gray; background:lightyellow;font-size:8pt}
		[tipo=obligatorio]:-moz-placeholder {color:gray; background:lightyellow;font-size:8pt}




	</style>
<script>

	function cerrarVentana()
	{
	 window.close()
	}

	function irnuevo(parametro)
	{
		$("#link_unico").attr("href",parametro);
		$("#link_unico")[0].click();

	}

	function enter()
	{
		document.forms.ordlab.submit();
	}


	$(document).ready( function () {

		$('#filtroEvaluacionPropias').quicksearch('#tableEvaluacionesPropias .find');


	} );
</script>
</head>

<?php

//  $user = "1-uvla01";   //Temporal!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!


	// $conex = mysql_connect('localhost','root','')
	// or die("No se ralizo Conexion con MySql ");
	include_once("root/comun.php");





	mysql_select_db("matrix") or die("No se selecciono la base de datos");

    //Busco el ccosto del usuario
	$query = "Select Cjecco,Cjeadm From uvglobal_000030 Where Cjeusu = '".substr($user,2,80)."'";
	$resultado = mysql_query($query);
	$registro = mysql_fetch_row($resultado);
	$sede = $registro[0];
	$admin= $registro[1];

	encabezado("Ordenes de laboratorio ", $wactualiz, "logo_uvglobal");
	echo"<a style='display:none' id='link_unico' HREF=''></a>";
	//"logo_".$wbasedato

	echo "<FORM name=ordlab action='uvglobal00.php' method=post>";

echo "<HTML>";

echo "<HEAD>";
echo "<TITLE>BIENVENIDA</TITLE>";
echo "</HEAD>";
echo "<BODY>";
echo "<table align='center' border=0>";
echo "<tr><td class='encabezadoTabla'><b><font>UNIDAD VISUAL GLOBAL S.A.</font></b><br>";
echo "<tr><td align=center class='fila1'><b><font >".$sede."</font></b><br></tr>";
//echo "<tr><td align=center class='fila2'><b><font > <i>Ver. 2008/01/22</font></b><br></tr>";




if ( $admin=='on')
{

 if (!isset($cco) or $cco=='')
 {
 ///////////////////////////////////////////////////////////////////////////////////////// seleccion para el centro de costos o sede
  echo "<tr><td align=CENTER class='fila1'><b><font ><B>Sede:</B></br></font></b><select name='cco' id='searchinput' onchange='enter()'>";

  $query1="SELECT Ccocod, Ccodes "
         ."  FROM uvglobal_000003 "
         ." ORDER BY Ccocod,ccodes";

  $err1 = mysql_query($query1,$conex);
  $num1 = mysql_num_rows($err1);
  $Ccostos=explode('-',$cco);

  echo "<option>&nbsp</option>";
  for ($i=1;$i<=$num1;$i++)
   {
	$row1 = mysql_fetch_array($err1);
	echo "<option>".$row1[0]."-".$row1[1]."</option>";
   }
  echo "</select></td></tr>";
  echo "</table>";
 }
 else
 {
 echo "<br><table align='center'>";
 echo "<br>";
 echo "<tr>";
 echo '<td><input type="button" value="Crear Nueva Orden" onclick="irnuevo(\'uvglobal01.php?wproceso=Nuevo\')"></td>';

 echo '<td><input type="button" value="Retornar" onclick="irnuevo(\'uvglobal00.php\')"></td>';
 echo "</tr>";
 echo "</table>";
 echo "<br>";
 echo "<table align='center' ><tr><td>";
 echo "<table border=0 align='left' >";
 echo "<tr ><td class='encabezadoTabla' nowrap=nowrap>Filtro de busqueda</td><td class='encabezadoTabla'><input type='text'  id='filtroEvaluacionPropias'></td><td colspan='7'></td></tr></table>";
 //echo "<tr  ><td colspan=9>&nbsp;</td></tr>";


 echo "<table id='tableEvaluacionesPropias' align='center'><tr><td align=center colspan=9 class='encabezadoTabla'><b>Ordenes Sede : ".$cco."</b></b></font></td></tr>";

 echo "<tr class='encabezadoTabla'>";
 echo "<td align=center ><b>Orden Nro<b></td>";
 echo "<td align=center ><b>Fecha<b></td>";
 echo "<td align=center ><b>Cedula<b></td>";
 echo "<td align=center ><b>Nombre<b></td>";
 echo "<td align=center ><b>Fuente<b></td>";
 echo "<td align=center ><b>Factura<b></td>";
 echo "<td align=center ><b>venta<b></td>";
 echo "<td align=center ></td>";
 echo "<td align=center ></td>";
 echo "</tr>";


     //SOLO MOSTRAMOS LAS ORDENES QUE NO SE HAN ENTREGADO Y DEL CENTRO DE COSTOS O SEDE DEL USUARIO
      $query = "SELECT "
       ."ordnro,orddoc,ordran,ordtus,orddsi,orddes,orddci,orddej,orddad,orddte,ordisi,ordies,ordici,ordiej,ordiad,ordite,"
       ."ordled,ordlei,ordedp,ordtra,ordbif,ordmon,ordref,ordmet,ordcom,ordcol,ordpin,ordde1,ordbra,ordde2,ordter,ordde3,"
       ."ordpla,ordde4,ordotr,ordde5,ordobs,ordcaj,ordffa,ordfac,ordfec,ordfre,ordfen,ordvel,ordvem,ordcco,CLINOM, ordinv, ordlot, ordloi, ordini, ordven"
       ." FROM uvglobal_000133,uvglobal_000041 WHERE ordfen = '0000-00-00' And ordcco = '".$cco."' And orddoc = clidoc "
       ." ORDER by ordnro DESC";
       $resultado = mysql_query($query);
       if ($resultado)
       {
		 $nroreg = mysql_num_rows($resultado);
		 $numcam = mysql_num_fields($resultado);

		$i = 1;
		While ($i <= $nroreg)
		{
	     // color de fondo
	     if (is_int ($i/2))  // Cuando la variable $i es par coloca este color
	      $wcf="fila1";
	   	 else
	   	  $wcf="fila2";

		 $registro = mysql_fetch_array($resultado);

		 $numventa ='';
		 if($registro['ordven'] == '')
		 {

			$select_fac ="SELECT  Fdenve FROM uvglobal_000019 WHERE Fdefac ='".$registro[39]."'";
			$resultado1  = mysql_query($select_fac);
			if($registro1   = mysql_fetch_array($resultado1))
			{
				$numventa = $registro1['Fdenve'];
			}
		 }else
		 {
			$numventa = $registro['ordven'];
		 }

		 echo "<tr class='find ".$wcf."'><td  >".$registro[0]."</td>";    //Nro de Orden
		 echo "<td >".$registro[40]."</td>";   //Fecha
		 echo "<td  >".$registro[1]."</td>";    //Cedula
		 echo "<td >".$registro[46]."</td>";   //Nombre
		 echo "<td  >".$registro[38]."</td>";   //Fuente
		 if($registro[39] == 'NO APLICA')
		 {
			echo "<td ></td>";   //Factura
		 }
		 else
		 {
			echo "<td >".$registro[39]."</td>";   //Factura
		 }
		 echo "<td  >".$numventa."</td>";   //Factura


	     echo "<td  >";
	     // LLAMADO SIN PARAMETROS
	     // echo "<A HREF='uvglobal01.php'>Nuevo</A></td>";

	     // LLAMADO CON PARAMETROS
	     // echo "<A HREF='uvglobal01.php?wnro=".$registro[0]."&wfec=".$registro[1]."&wdoc=".$registro[2]."'>Editar</A></td>";

	     /* SIN EMBARGO COMO EN ESTE CASO SON 45 CAMPOS QUE TENGO QUE ENVIAR COMO PARAMETROS, ENTONCES SI TENGO
	        LA PRECAUCION DE DAR NOMBRES DE LOS CAMPOS EN LA TABLA ASI:       ordnro,orddoc,ordran,... Y SI EN LA FORMA
	        UTILIZO COMO NOMBRE DE VARIABLES PARA MANIPULAR ESTOS  CAMPOS:      wnro,  wdoc,  wran,...
	        ARMO MEDIANTE UN STRING UN href TOMANDO LOS ULTIMOS TRES CARACTERES DE LOS NOMBRES DE LOS CAMPOS
	     */
	        $l="<A HREF='uvglobal01.php?w".substr(mysql_field_name($resultado,0),3)."=".$registro[0];
	        for ($j=1;$j<=$numcam-1;$j++)
			{
	          if($registro[$j] == 'NO APLICA')
			  {
				$registro[$j] ='';

			  }
			  $l = $l."&w".substr(mysql_field_name($resultado,$j),3)."=".$registro[$j];
			}
	        $l = $l."&wproceso=Modificar'>Editar</A></td>";  //Adiciono un columna adicional para indicar que voy A "Modificar"

	        $l = $l."<td  >";     // Otra que llame el programa que imprime
	        $l = $l."<A HREF='uvglobal02.php?wnro=".$registro[0]."'>Imprimir</A></td>";

	        echo $l;
            echo "</tr>";
          // OTRA FORMA SERIA ENVIAR SOLO LOS CAMPOS CLAVES EN ESTE CASO ordnro Y EL PROGRAMA LLAMADO EMPIEZO HACIENDO
          // UN SELECT PARA LLENAR LAS VARIABLES
 	      // echo "<A HREF='uvglobal01.php?wnro=".$registro[0]."'>Editar</A></td>";

          $i++;
	    }
       }
    }
 }
 else
 {
 echo "<table align='center'>";
 //echo "<br>";
 echo "<tr>";
echo '<td><input type="button" value="Crear Nueva Orden" onclick="irnuevo(\'uvglobal01.php?wproceso=Nuevo\')"></td>';

 echo '<td><input type="button" value="Retornar" onclick="irnuevo(\'uvglobal00.php\')"></td>';

 echo "</tr>";
 echo "</table>";
 echo "<br>";
 echo "<table align='center' ><tr><td>";
 echo "<table border=0 align='left' >";
 echo "<tr ><td class='encabezadoTabla' nowrap=nowrap>Filtro de busqueda</td><td class='encabezadoTabla'><input type='text'  id='filtroEvaluacionPropias'></td><td colspan='7'></td></tr></table>";
 echo "</td></tr><tr><td>";
 echo "<table border=0 align='center' id='tableEvaluacionesPropias'>";

 echo "<tr class='encabezadoTabla'><td align=center  colspan='9'><b>Ordenes Sede : ".$sede."</b></b></td></tr>";

 echo "<tr class='encabezadoTabla '>";
 echo "<td align=center ><b>Orden Nro<b></td>";
 echo "<td align=center ><b>Fecha<b></td>";
 echo "<td align=center><b>Cedula<b></td>";
 echo "<td align=center ><b>Nombre<b></td>";
 echo "<td align=center ><b>Fuente<b></td>";
 echo "<td align=center ><b>Factura<b></td>";
 echo "<td align=center ><b>venta<b></td>";
// echo "<td align=center bgcolor=#DDDDDD><b>Factura<b></td>";
 echo "<td align=center ></td>";
 echo "<td align=center ></td>";
 echo "</tr>";


     //SOLO MOSTRAMOS LAS ORDENES QUE NO SE HAN ENTREGADO Y DEL CENTRO DE COSTOS O SEDE DEL USUARIO
       $query = "SELECT "
       ."ordnro,orddoc,ordran,ordtus,orddsi,orddes,orddci,orddej,orddad,orddte,ordisi,ordies,ordici,ordiej,ordiad,ordite,"
       ."ordled,ordlei,ordedp,ordtra,ordbif,ordmon,ordref,ordmet,ordcom,ordcol,ordpin,ordde1,ordbra,ordde2,ordter,ordde3,"
       ."ordpla,ordde4,ordotr,ordde5,ordobs,ordcaj,ordffa,ordfac,ordfec,ordfre,ordfen,ordvel,ordvem,ordcco,CLINOM,ordinv,ordlot, ordloi, ordini,ordven"
       ." FROM uvglobal_000133,uvglobal_000041 WHERE ordfen = '0000-00-00' And ordcco = '".$sede."' And orddoc = clidoc "
       ." ORDER by ordnro DESC";
       $resultado = mysql_query($query);
       if ($resultado)
       {
		 $nroreg = mysql_num_rows($resultado);
		 $numcam = mysql_num_fields($resultado);

		$i = 1;
		While ($i <= $nroreg)
		{
	     // color de fondo
	     if (is_int ($i/2))  // Cuando la variable $i es par coloca este color
	      $wcf="fila1";
	   	 else
	   	  $wcf="fila2";




		 $registro = mysql_fetch_array($resultado);

		 $numventa ='';
		 if($registro['ordven'] == '')
		 {

			$select_fac ="SELECT  Fdenve FROM uvglobal_000019 WHERE Fdefac ='".$registro[39]."'";
			$resultado1  = mysql_query($select_fac);
			if($registro1   = mysql_fetch_array($resultado1))
			{
				$numventa = $registro1['Fdenve'];
			}
		 }else
		 {
			$numventa = $registro['ordven'];
		 }
		 echo "<tr class='find'><td  class=".$wcf.">".$registro[0]."</td>";    //Nro de Orden
		 echo "<td  class=".$wcf.">".$registro[40]."</td>";   //Fecha
		 echo "<td  class=".$wcf.">".$registro[1]."</td>";    //Cedula
		 echo "<td class=".$wcf.">".$registro[46]."</td>";   //Nombre
		 echo "<td  class=".$wcf.">".$registro[38]."</td>";   //Fuente
		 echo "<td  class=".$wcf.">".$registro[39]."</td>";   //Factura
		 echo "<td  class=".$wcf.">".$numventa."</td>";   //Factura


	     echo "<td align=center class=".$wcf.">";
	     // LLAMADO SIN PARAMETROS
	     // echo "<A HREF='uvglobal01.php'>Nuevo</A></td>";

	     // LLAMADO CON PARAMETROS
	     // echo "<A HREF='uvglobal01.php?wnro=".$registro[0]."&wfec=".$registro[1]."&wdoc=".$registro[2]."'>Editar</A></td>";

	     /* SIN EMBARGO COMO EN ESTE CASO SON 45 CAMPOS QUE TENGO QUE ENVIAR COMO PARAMETROS, ENTONCES SI TENGO
	        LA PRECAUCION DE DAR NOMBRES DE LOS CAMPOS EN LA TABLA ASI:       ordnro,orddoc,ordran,... Y SI EN LA FORMA
	        UTILIZO COMO NOMBRE DE VARIABLES PARA MANIPULAR ESTOS  CAMPOS:      wnro,  wdoc,  wran,...
	        ARMO MEDIANTE UN STRING UN href TOMANDO LOS ULTIMOS TRES CARACTERES DE LOS NOMBRES DE LOS CAMPOS
	     */


	        $l="<A HREF='uvglobal01.php?w".substr(mysql_field_name($resultado,0),3)."=".$registro[0];
	        for ($j=1;$j<=$numcam-1;$j++)
	          $l = $l."&w".substr(mysql_field_name($resultado,$j),3)."=".$registro[$j];

	        $l = $l."&wproceso=Modificar'>Editar</A></td>";  //Adiciono un columna adicional para indicar que voy A "Modificar"

	        echo $l;

	        $l = "";
	        $l = $l."<td align=center class=".$wcf.">";     // Otra que llame el programa que imprime
	        $l = $l."<A HREF='uvglobal02.php?wnro=".$registro[0]."'>Imprimir</A></td>";

	        echo $l;
            echo "</tr>";
          // OTRA FORMA SERIA ENVIAR SOLO LOS CAMPOS CLAVES EN ESTE CASO ordnro Y EL PROGRAMA LLAMADO EMPIEZO HACIENDO
          // UN SELECT PARA LLENAR LAS VARIABLES
 	      // echo "<A HREF='uvglobal01.php?wnro=".$registro[0]."'>Editar</A></td>";

          $i++;
	    }
       }
    }



echo "</table>";

echo "<table align='center'>";
echo "<tr align='center'> ";
//echo "<td colspan=8>&nbsp</td>";
echo '<td><input type="button" value="Retornar" onclick="irnuevo(\'uvglobal00.php\')"></td><td><input type=button value="Cerrar Ventana" onclick="cerrarVentana()"></td></tr>';
echo "</table>";

echo "</td></tr></table>";
echo "</BODY>";
echo "</HTML>";
echo "</form>";

}
else
{

if(isset($accion))
{
	switch($accion)
	{

		case "abrir_tabla":
		{

			echo abrir_tabla ($tablappal,$wusuariotabla,$wnombreopc,$parametro,$campobuscar);
			break;
		}

	}
	return;

}


}    // De la sesion
?>