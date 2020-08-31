<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<title>Programa de AFINIDAD</title>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>	
<script src="efecto.php"></script>
<script>
        $.datepicker.regional['esp'] = {
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
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['esp']);
        $(document).ready(function() {
		$("#fecha, #fecha1").datepicker({
	       showOn: "button",
	       buttonImage: "../../images/medical/root/calendar.gif",
	       buttonImageOnly: true,
	       maxDate:"+1D"
	    });
		});
</script>

<SCRIPT LANGUAGE="JavaScript1.2">
function onLoad() {
	loadMenus();
}
</SCRIPT>

</head>
<body>
<?php
include_once("conex.php"); 

/**
 * 	Reporte de visitas y gestion de AAA
 * 
 * reporte que suministra entre dos fechas la siguiente información: porcentaje de afines hospitalizados visitados, porcentaje de AAA O BBB no ubicados, porcentaje de AAA O BBB no visitados,  porcentaje de AAA O BBB en cada una de las unidades. 
 * 
 * @name  matrix\magenta\procesos\gestionAAA.php
 * @author Carolina Castaño Portilla.
 * 
 * @created 2007-01-17
 * @version 2007-03-06
 * 
 * @modified 2006-03-06 Carolina Castano  Se modifica la forma en que se realiza la agrupacion de las unidades de los BBB
 Actualizacion: Se agrego en la funcion promedio2 comillas dobles para los ultimos dos parametros, tambien antes de la variable a darle formato se agrego (float).  Viviana Rodas 2012-05-18
 * 
 * @table magenta_000014, select, 
 * 
 *  @var $AAA cantidad de AFINES AAA que vinieron entre dos fechas a hopitalizacion
 *  @var $AAA cantidad de PERSONALIDADES AAA que vinieron entre dos fechas a hopitalizacion
 *  @var $ant guarda la unidad anterior de un for, para compararla con la actual y tomar acciones si son diferentes
 *  @var $BBB cantidad de AFINES BBB que vinieron entre dos fechas a hopitalizacion
 *  @var $BBB cantidad de PERSONALIDADES BBB que vinieron entre dos fechas a hopitalizacion
 *  @var $color alternar el color en las tablas
 *  @var $encAAA cantidad de AFINES AAA hospitalizados no encontrados
 *  @var $encBBB cantidad de AFINES  BBB hospitalizados no encontrados
 *  @var $encAAA cantidad de PERSONALIDADES AAA hospitalizados no encontrados
 *  @var $encBBB cantidad de PERSONALIDADES BBB hospitalizados no encontrados
 *  @var $fecha fecha inicial de busqueda del reporte
 *  @var $fecha1 fecha final de  busqueda del reporte
 *  @var $noAAA cantidad de AFINES AAA hospitalizados no visitados
 *  @var $noBBB cantidad de AFINES BBB hospitalizados no visitados
 *  @var $visAAA cantidad de AFINES AAA hospitalizados visitados
 *  @var $visBBB cantidad de AFINES BBB hospitalizados visitados
 *  @var $noAAAP cantidad de PERSONALIDADES AAA hospitalizados no visitados
 *  @var $noBBBP cantidad de PERSONALIDADES BBB hospitalizados no visitados
 *  @var $visAAAP cantidad de PERSONALIDADES AAA hospitalizados visitados
 *  @var $visBBBP cantidad de PERSONALIDADES BBB hospitalizados visitados
*/
/************************************************************************************************************************* 
  Actualizaciones:
 			2016-05-02 (Arleyda Insignares C.)
 						-Se Modifican los campos de calendario fecha inicial y fecha final con utilidad jquery y se elimina
						 uso de Zapatec Calendar por errores en la clase.
						-Se cambia encabezado y titulos con ultimo formato.
*************************************************************************************************************************/

$wautor="Carolina Castano P.";
$wversion='2007-03-06';
$wactualiz='2016-05-05';

/***************************************************   funciones   *****************************************************/

/**
 * mensaje de error a conectarse a base de datos
 *
 */
function  DisplayError()
{
	echo '<script language="Javascript">';
	echo 'alert ("Error al conectarse con la base de datos, intente más tarde")';
	echo '</script>';

}


/**
 * calcula el promedio de un valor sobre otro y evita division por cero
 *
 * @param unknown_type $val1
 * @param unknown_type $val2
 * @return unknown
 */
function promedio2($val1, $val2){
	if ($val2>0)
	{
		$variac1=($val1*100)/$val2;
		$variac1=round($variac1 * 100) / 100 ;
	}else
	{
		$variac1='Sin';
	}
	return number_format((float)$variac1,2,".",",");
}


/****************************PROGRAMA************************************************/
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
    include_once("root/comun.php");
	/////////////////////////////////////////////////encabezado general///////////////////////////////////
    $titulo = "SISTEMA DE COMENTARIOS Y SUGERENCIAS";
    // Se muestra el encabezado del programa
    encabezado($titulo,$wactualiz, "clinica");  
	echo "<table align='center'>\n" ;
	echo "<tr>" ;
	echo "<td><img src='/matrix/images/medical/Magenta/aaa.gif'  height='61' width='113'></td>";
	echo "</tr>" ;
	echo "</table></br>" ;
	echo "<br></br>";
    echo "<div align='center'><div align='center' class='fila1' style='width:520px;'><font size='4'><b>REPORTE DE GESTION AAA</b></font></div></div></BR>";
    echo "<br></br>";
	echo "\n" ;

	/////////////////////////////////////////////////encabezado general///////////////////////////////////
	/////////////////////////////////////////////////inicialización de variables///////////////////////////////////

	

	


	$empresa='magenta';

	/////////////////////////////////////////////////inicialización de variables//////////////////////////
	/////////////////////////////////////////////////acciones concretas///////////////////////////////////


	if (!isset ($fecha)) ///////////////////////////entramos por primera vez al reporte/////////////////////////
	{
		// iniciacion de fechas
		$pass= intval(date('m'))-2;

		//se organiza un mes por defecto como griterio para el reporte

		if ($pass<10)
		$pass='0'.$pass;

		$fecha=date('Y').'-'.$pass.'-'.date('d');

		if ($pass==0)
		{
			$fecha= intval (date('Y'))-1;
			$fecha=$fecha.'-12-'.date('d');

		}
		if ($pass<0)
		{
			$fecha= intval (date('Y'))-1;
			$fecha=$fecha.'-11-'.date('d');

		}

		$fecha1=date('Y-m-d');

		echo "<center><font color='#00008B'>INGRESE POR FAVOR EL RANGO DE FECHAS EN EL CUAL DESEA CONSULTAR EL REPORTE DE GESTION:</font></center></BR>";


		// Busqueda de comentario entre dos fechas

		echo "<fieldset width=700' align=center></br>";

		echo "<form NAME='ingreso' ACTION='' METHOD='POST'>";
		echo "<table align='center'>";
		echo "<tr>";
		//$cal="calendario('fecha','1')";
		echo "<td align=center bgcolor=#336699 ><font size=3  face='arial' color='#ffffff'>FECHA INICIAL:</font><input type='text' readonly='readonly' id='fecha' name='fecha' value='".$fecha."' class=tipo3 ></td>";
		echo "<td align=center bgcolor=#336699><font size=3  face='arial' color='#ffffff'>FECHA FINAL:</font><input type='text' readonly='readonly' id='fecha1' name='fecha1' value='".$fecha1."' class=tipo3 ></td>";
		echo "</td>";
		echo "</tr></TABLE></br>";
		echo "<TABLE align=center><tr>";
		echo "<tr>";
		echo "<td align=center colspan=14><font size='2'  align=center face='arial'><input type='submit' name='aceptar' value='BUSCAR' ></td></tr>";
		echo "</TABLE>";
		echo "</td>";
		echo "</tr>	";
		echo "</form>";
		echo "</fieldset>";
}
else ////////////////////se han seleccionado las fechas para el reporte///////////////////////////////////
{
	echo "<center><font color='#00008B'>PORCENTAJES DE GESTION:</font></center></BR>";
	//busco los porcentajes para pintar

	$query ="SELECT * FROM ".$empresa."_000014 where repvis='on' and reping between '".$fecha."' and '".$fecha1."' and repusu='AFIN-AAA-1' and rephos='1' ";
	$err=mysql_query($query,$conex);
	$visAAA=mysql_num_rows($err);

	$query ="SELECT * FROM ".$empresa."_000014 where repvis='on' and reping between '".$fecha."' and '".$fecha1."' and repusu='AFIN-BBB-2' and rephos='1' ";
	$err=mysql_query($query,$conex);
	$visBBB=mysql_num_rows($err);

	$query ="SELECT * FROM ".$empresa."_000014 where repvis='on' and reping between '".$fecha."' and '".$fecha1."' and repusu='PER-AAA-1' and rephos='1' ";
	$err=mysql_query($query,$conex);
	$visAAAP=mysql_num_rows($err);

	$query ="SELECT * FROM ".$empresa."_000014 where repvis='on' and reping between '".$fecha."' and '".$fecha1."' and repusu='PER-BBB-2' and rephos='1' ";
	$err=mysql_query($query,$conex);
	$visBBBP=mysql_num_rows($err);

	$query ="SELECT * FROM ".$empresa."_000014 where repenc='on' and reping between '".$fecha."' and '".$fecha1."' and repusu='AFIN-AAA-1' and rephos='1' ";
	$err=mysql_query($query,$conex);
	$encAAA=mysql_num_rows($err);

	$query ="SELECT * FROM ".$empresa."_000014 where repenc='on' and reping between '".$fecha."' and '".$fecha1."' and repusu='AFIN-BBB-2' and rephos='1' ";
	$err=mysql_query($query,$conex);
	$encBBB=mysql_num_rows($err);

	$query ="SELECT * FROM ".$empresa."_000014 where repenc='on' and reping between '".$fecha."' and '".$fecha1."' and repusu='PER-AAA-1' and rephos='1' ";
	$err=mysql_query($query,$conex);
	$encAAAP=mysql_num_rows($err);

	$query ="SELECT * FROM ".$empresa."_000014 where repenc='on' and reping between '".$fecha."' and '".$fecha1."' and repusu='PER-BBB-2' and rephos='1' ";
	$err=mysql_query($query,$conex);
	$encBBBP=mysql_num_rows($err);


	$query ="SELECT * FROM ".$empresa."_000014 where repvis='' and repenc='' and reping between '".$fecha."' and '".$fecha1."' and repusu='AFIN-AAA-1' and rephos='1' ";
	$err=mysql_query($query,$conex);
	$noAAA=mysql_num_rows($err);

	$query ="SELECT * FROM ".$empresa."_000014 where repvis='' and repenc='' and reping between '".$fecha."' and '".$fecha1."' and repusu='AFIN-BBB-2' and rephos='1' ";
	$err=mysql_query($query,$conex);
	$noBBB=mysql_num_rows($err);

	$query ="SELECT * FROM ".$empresa."_000014 where repvis='' and repenc='' and reping between '".$fecha."' and '".$fecha1."' and repusu='PER-AAA-1' and rephos='1' ";
	$err=mysql_query($query,$conex);
	$noAAAP=mysql_num_rows($err);

	$query ="SELECT * FROM ".$empresa."_000014 where repvis='' and repenc='' and reping between '".$fecha."' and '".$fecha1."' and repusu='PER-BBB-2' and rephos='1' ";
	$err=mysql_query($query,$conex);
	$noBBBP=mysql_num_rows($err);


	$AAA=$visAAA+$encAAA+$noAAA;
	$BBB=$visBBB+$encBBB+$noBBB;
	$AAAP=$visAAAP+$encAAAP+$noAAAP;
	$BBBP=$visBBBP+$encBBBP+$noBBBP;

	echo "<table align='center' border='1' width='100%'>";
	echo "<tr>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>&nbsp;</font></td>";
	echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>NUMERO DE AFINES AAA</font></td>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>PORCENTAJE DE AFINES AAA</font></td>";
	echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>NUMERO DE AFINES BBB</font></td>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>PORCENTAJE DE AFINES BBB</font></td>";
	echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>NUMERO DE PERSONALIDADES AAA</font></td>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>PORCENTAJE DE PERSONALIDADES AAA</font></td>";
	echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>NUMERO DE PERSONALIDADES BBB</font></td>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>PORCENTAJE DE PERSONALIDADES BBB</font></td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>HOSPITALIZADOS VISITADOS</font></td>";
	echo "	<td  width='8%' align='center'><font color='#336699'>".$visAAA."</font></td>";
	echo "<td  width='8%' align='center'><font color='#336699'>".promedio2($visAAA, $AAA)."</font></td>";
	echo "	<td  width='8%' align='center'><font color='#336699'>".$visBBB."</font></td>";
	echo "<td  width='8%' align='center'><font color='#336699'>".promedio2($visBBB, $BBB)."</font></td>";
	echo "	<td  width='8%' align='center'><font color='#336699'>".$visAAAP."</font></td>";
	echo "<td  width='8%' align='center'><font color='#336699'>".promedio2($visAAAP, $AAAP)."</font></td>";
	echo "	<td  width='8%' align='center'><font color='#336699'>".$visBBB."</font></td>";
	echo "<td  width='8%' align='center'><font color='#336699'>".promedio2($visBBBP, $BBBP)."</font></td>";
	echo "</tr>";
	echo "</tr>";

	echo "<tr>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>HOSPITALIZADOS NO ENCONTRADOS</font></td>";
	echo "	<td bgcolor='#F8F8FF' width='8%' align='center'><font color='#336699'>".$encAAA."</font></td>";
	echo "<td bgcolor='#F8F8FF' width='8%' align='center'><font color='#336699'>".promedio2($encAAA, $AAA)."</font></td>";
	echo "	<td bgcolor='#F8F8FF' width='8%' align='center'><font color='#336699'>".$encBBB."</font></td>";
	echo "<td bgcolor='#F8F8FF' width='8%' align='center'><font color='#336699'>".promedio2($encBBB, $BBB)."</font></td>";
	echo "	<td bgcolor='#F8F8FF' width='8%' align='center'><font color='#336699'>".$encAAAP."</font></td>";
	echo "<td bgcolor='#F8F8FF' width='8%' align='center'><font color='#336699'>".promedio2($encAAAP, $AAAP)."</font></td>";
	echo "	<td bgcolor='#F8F8FF' width='8%' align='center'><font color='#336699'>".$encBBBP."</font></td>";
	echo "<td bgcolor='#F8F8FF' width='8%' align='center'><font color='#336699'>".promedio2($encBBBP, $BBBP)."</font></td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td  bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>HOSPITALIZADOS NO VISITADOS</font></td>";
	echo "	<td  width='8%' align='center'><font color='#336699'>".$noAAA."</font></td>";
	echo "<td  width='8%' align='center'><font color='#336699'>".promedio2($noAAA, $AAA)."</font></td>";
	echo "	<td  width='8%' align='center'><font color='#336699'>".$noBBB."</font></td>";
	echo "<td  width='8%' align='center'><font color='#336699'>".promedio2($noBBB, $BBB)."</font></td>";
	echo "	<td  width='8%' align='center'><font color='#336699'>".$noAAAP."</font></td>";
	echo "<td  width='8%' align='center'><font color='#336699'>".promedio2($noAAAP, $AAAP)."</font></td>";
	echo "	<td  width='8%' align='center'><font color='#336699'>".$noBBBP."</font></td>";
	echo "<td  width='8%' align='center'><font color='#336699'>".promedio2($noBBBP, $BBBP)."</font></td>";
	echo "</tr>";

	echo "<tr>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>HOSPITALIZADOS TOTALES</font></td>";
	echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>".$AAA."</font></td>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>&nbsp;</font></td>";
	echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>".$BBB."</font></td>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>&nbsp;</font></td>";
	echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>".$AAAP."</font></td>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>&nbsp;</font></td>";
	echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>".$BBBP."</font></td>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>&nbsp;</font></td>";
	echo "</tr></TABLE>";

	//AHORA HAGO UN QUERY PARA UNIDADES ENTRE DOS FECHAS

	echo "</br><center><font color='#00008B'>PORCENTAJES DE PACIENTES AFINES AAA POR UNIDADES:</font></center></BR>";

	$query ="SELECT repser FROM ".$empresa."_000014 where  reping between '".$fecha."' and '".$fecha1."' and repusu='AFIN-AAA-1' order by repser";
	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);

	$y=0;//cuenta el numero de afines por unidad
	$ant=0;
	$color='#FFFFFF';

	echo "<table align='center' border='1' width='100%'>";
	echo "<tr>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>UNIDAD</font></td>";
	echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>NUMERO DE AFINES AAA</font></td>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>PORCENTAJE DE AFINES AAA</font></td>";
	echo "</tr>";


	for($i=1;$i<=$num;$i++)
	{
		$row = mysql_fetch_row($err);

		if ($num==$i)
		{
			if ($row[0]==$ant)
			{
				$y=$y+1;
			}
			echo "<tr>";
			echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>".$row[0]."</font></td>";
			echo "	<td bgcolor='#ffffff' width='8%' align='center'><font color='#336699'>".($y)."</font></td>";
			echo "	<td bgcolor='#ffffff' width='8%' align='center'><font color='#336699'>".promedio2(($y), $num)."</font></td>";
			echo "</tr>";
			$y=1;
		}

		if ($row[0]!=$ant and $i!=1)
		{
			echo "<tr>";
			echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>".$ant."</font></td>";
			echo "	<td bgcolor='".$color."' width='8%' align='center'><font color='#336699'>".$y."</font></td>";
			echo "	<td bgcolor='".$color."' width='8%' align='center'><font color='#336699'>".promedio2($y, $num)."</font></td>";
			echo "</tr>";
			$y=1;
			if ($color=='#FFFFFF')
			{
				$color='#F8F8FF';
			}
			else
			{
				$color='#FFFFFF';
			}
		}
		else
		{
			$y=$y+1;
		}
		$ant=$row[0];
	}

	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>TOTAL</font></td>";
	echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>".$num."</font></td>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>100%</font></td>";
	echo "</tr></table></br>";

	echo "<center><font color='#00008B'>PORCENTAJES DE PACIENTES AFINES BBB POR UNIDADES:</font></center></BR>";
	$query ="SELECT repser FROM ".$empresa."_000014 where  reping between '".$fecha."' and '".$fecha1."' and repusu='AFIN-BBB-2' order by repser";

	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);


	$y=0;//cuenta el numero de afines por unidad
	$ant=0;
	$color='#FFFFFF';

	echo "<table align='center' border='1' width='100%'>";
	echo "<tr>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>UNIDAD</font></td>";
	echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>NUMERO DE AFINES BBB</font></td>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>PORCENTAJE DE AFINES BBB</font></td>";
	echo "</tr>";
	
	for($i=1;$i<=$num;$i++)
	{
		$row = mysql_fetch_row($err);

		if ($num==$i)
		{
			if ($row[0]==$ant)
			{
				$y=$y+1;
			}
			echo "<tr>";
			echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>".$row[0]."</font></td>";
			echo "	<td bgcolor='#ffffff' width='8%' align='center'><font color='#336699'>".($y)."</font></td>";
			echo "	<td bgcolor='#ffffff' width='8%' align='center'><font color='#336699'>".promedio2(($y), $num)."</font></td>";
			echo "</tr>";
			$y=1;
		}

		if ($row[0]!=$ant and $i!=1)
		{
			echo "<tr>";
			echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>".$ant."</font></td>";
			echo "	<td bgcolor='".$color."' width='8%' align='center'><font color='#336699'>".$y."</font></td>";
			echo "	<td bgcolor='".$color."' width='8%' align='center'><font color='#336699'>".promedio2($y, $num)."</font></td>";
			echo "</tr>";
			$y=1;
			if ($color=='#FFFFFF')
			{
				$color='#F8F8FF';
			}
			else
			{
				$color='#FFFFFF';
			}
		}
		else
		{
			$y=$y+1;
		}
		$ant=$row[0];
	}
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>TOTAL</font></td>";
	echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>".$num."</font></td>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>100%</font></td>";
	echo "</tr>";

		echo "</tr></table></br>";
	///////PARA LAS PERSONALIDADES

	echo "</br><center><font color='#00008B'>PORCENTAJES DE PERSONALIDADES AAA POR UNIDADES:</font></center></BR>";

	$query ="SELECT repser FROM ".$empresa."_000014 where  reping between '".$fecha."' and '".$fecha1."' and repusu='PER-AAA-1' order by repser";
	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);

	$y=0;//cuenta el numero de afines por unidad
	$ant=0;
	$color='#FFFFFF';

	echo "<table align='center' border='1' width='100%'>";
	echo "<tr>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>UNIDAD</font></td>";
	echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>NUMERO DE PERSONALIDADES AAA</font></td>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>PORCENTAJE DE PERSONALIDADES AAA</font></td>";
	echo "</tr>";


	for($i=1;$i<=$num;$i++)
	{
		$row = mysql_fetch_row($err);

		if ($num==$i)
		{
			if ($row[0]==$ant)
			{
				$y=$y+1;
			}
			echo "<tr>";
			echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>".$row[0]."</font></td>";
			echo "	<td bgcolor='#ffffff' width='8%' align='center'><font color='#336699'>".($y)."</font></td>";
			echo "	<td bgcolor='#ffffff' width='8%' align='center'><font color='#336699'>".promedio2(($y), $num)."</font></td>";
			echo "</tr>";
			$y=1;
		}

		if ($row[0]!=$ant and $i!=1)
		{
			echo "<tr>";
			echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>".$ant."</font></td>";
			echo "	<td bgcolor='".$color."' width='8%' align='center'><font color='#336699'>".$y."</font></td>";
			echo "	<td bgcolor='".$color."' width='8%' align='center'><font color='#336699'>".promedio2($y, $num)."</font></td>";
			echo "</tr>";
			$y=1;
			if ($color=='#FFFFFF')
			{
				$color='#F8F8FF';
			}
			else
			{
				$color='#FFFFFF';
			}
		}
		else
		{
			$y=$y+1;
		}
		$ant=$row[0];
	}

	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>TOTAL</font></td>";
	echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>".$num."</font></td>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>100%</font></td>";
	echo "</tr></table></br>";

	echo "<center><font color='#00008B'>PORCENTAJES DE PACIENTES PERSONALIDADES BBB POR UNIDADES:</font></center></BR>";
	$query ="SELECT repser FROM ".$empresa."_000014 where  reping between '".$fecha."' and '".$fecha1."' and repusu='PER-BBB-2' order by repser";

	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);


	$y=0;//cuenta el numero de afines por unidad
	$ant=0;
	$color='#FFFFFF';

	echo "<table align='center' border='1' width='100%'>";
	echo "<tr>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>UNIDAD</font></td>";
	echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>NUMERO DE PERSONALIDADES BBB</font></td>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>PORCENTAJE DE PERSONALIDADES BBB</font></td>";
	echo "</tr>";

	for($i=1;$i<=$num;$i++)
	{
		$row = mysql_fetch_row($err);

		if ($num==$i)
		{
			if ($row[0]==$ant)
			{
				$y=$y+1;
			}
			echo "<tr>";
			echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>".$row[0]."</font></td>";
			echo "	<td bgcolor='#ffffff' width='8%' align='center'><font color='#336699'>".($y)."</font></td>";
			echo "	<td bgcolor='#ffffff' width='8%' align='center'><font color='#336699'>".promedio2(($y), $num)."</font></td>";
			echo "</tr>";
			$y=$y+1;
		}

		if ($row[0]!=$ant and $i!=1 )
		{
			$y=1;
			echo "<tr>";
			echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>".$ant."</font></td>";
			echo "	<td bgcolor='".$color."' width='8%' align='center'><font color='#336699'>".$y."</font></td>";
			echo "	<td bgcolor='".$color."' width='8%' align='center'><font color='#336699'>".promedio2($y, $num)."</font></td>";
			echo "</tr>";

			if ($color=='#FFFFFF')
			{
				$color='#F8F8FF';
			}
			else
			{
				$color='#FFFFFF';
			}
		}
		else
		{
			$y=$y+1;
		}
		$ant=$row[0];
	}
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>TOTAL</font></td>";
	echo "	<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>".$num."</font></td>";
	echo "<td bgcolor='#336699' width='8%' align='center'><font color='#ffffff'>100%</font></td>";
	echo "</tr>";
}

}

?>

</body>

</html>







