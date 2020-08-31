<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<title>Programa de Comentarios y Suegerencias</title>
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
<!--
function onLoad() {
	loadMenus();
}
//-->
</SCRIPT>
</head>
<body>
<?php
include_once("conex.php"); 

/********************* 	Reporte de pacientes que no volverian a la clinica por entidad   *********************************
 * 
 * reporte que suministra entre dos fechas la siguiente informaci�n: Personas que han dicho que no volverian a la clinica por entidad. 
 * 
 * @name  matrix\magenta\procesos\rep_volveria.php
 * @author Carolina Casta�o Portilla.
 * 
 * @created 2007-12-10
 * @version 2007-12-10
 * 
 * @modified 2007-12-10 Carolina Castano  Creacion del documento
 Actualizacion: Se agrego la funcion promedio3 para evaluar que no se divida por cero, en el porcentaje de satisfaccion de maternas Viviana Rodas 
 2012-05-18
 * 
 * @table magenta_000016, 000017 select
*/
/************************************************************************************************************************* 
  Actualizaciones:
			2018-05-24 (Juan Felipe Balcero L.)
						-Se adiciona consulta de datos del paciente a tabla cliame 100 ya que no siempre se tiene la información en la tabla magenta 16 y estaba causando un reporte incompleto de información.
						-En el total de comentarios se modificó el query de consulta a las tablas magenta 16 y 17, debido a que si el número de documento no se encontraba en la tabla 16, el query dejaba por fuera el comentario. Muchos usuarios no se encuentran registrados en magenta_000016 y los números no concordaban.
						-Se agrega un campo que muestra el número del comentario
 			2016-05-05 (Arleyda Insignares C.)
 						-Se Modifican los campos de calendario fecha inicial y fecha final con utilidad jquery y se elimina
						 uso de Zapatec Calendar por errores en la clase.
						-Se cambia encabezado y titulos con ultimo formato.
*************************************************************************************************************************/
$wautor="Carolina Castano P.";
$wversion='2007-12-10';
$wactualiz='2018-05-24';

/*********************************************    funciones    ************************************/

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
	return number_format($variac1,2,',','.');
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
    echo "</br><div align='center'><div align='center' class='fila1' style='width:520px;'><font size='4'><b>REPORTE DE PACIENTES QUE NO VOLVERIAN A LA CLINICA POR ENTIDAD</b></font></div></div></br></br>";
	echo "\n" ;

	/////////////////////////////////////////////////inicialización de variables///////////////////////////////////

	

	


	$empresa='magenta';

	/////////////////////////////////////////////////acciones concretas///////////////////////////////////

	if (!isset ($fecha)) ///////////////////////////entramos por primera vez al reporte/////////////////////////
	{
		// iniciacion de fechas
		$pass= intval(date('m'))-1;

		//se organiza un mes por defecto como criterio para el reporte

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

		echo "<center><font color='#00008B'>INGRESE POR FAVOR EL RANGO DE FECHAS EN EL CUAL DESEA CONSULTAR EL REPORTE:</font></center></br></br>";


		// Busqueda entre dos fechas

		echo "<fieldset width=700' align=center></br>";

		echo "<form NAME='ingreso' ACTION='' METHOD='POST'>";
		echo "<table align='center'>";
		echo "<tr class='encabezadotabla'>";
		$cal="calendario('fecha','1')";
		echo "<td align=center ><font size=3  face='arial' color='#ffffff'>FECHA INICIAL:</font><input type='text' readonly='readonly' id='fecha' name='fecha' value='".$fecha."' class=tipo3 ></td>";
		echo "<td align=center ><font size=3  face='arial' color='#ffffff'>FECHA FINAL:</font><input type='text' readonly='readonly' id='fecha1' name='fecha1' value='".$fecha1."' class=tipo3 ></td>";
		echo "</td>";
		echo "</tr></TABLE></br>";
		echo "<TABLE align=center><tr>";
		echo "<tr>";
		echo "<td align='center' colspan=14><font size='2'  align=center face='arial'><input type='checkbox' name='resumido' value='on' >Resumido</td></tr>";
		echo "<tr>";
		echo "<td align='center' colspan=14><font size='2'  align=center face='arial'><input type='submit' name='aceptar' value='BUSCAR' ></td></tr>";
		echo "</TABLE>";
		echo "</td>";
		echo "</tr>	";
		echo "</form>";
		echo "</fieldset>";
}
else ////////////////////se han seleccionado las fechas para el reporte///////////////////////////////////
{
	$conexi=odbc_connect('facturacion','','')
	or die("No se realizo conexión con la BD de Facturación");

	echo "<center><font color='#00008B'>REPORTE DE PACIENTES QUE NO VOLVERIAN A LA CLINICA POR ENTIDADES</font></center></BR>";
	//busco los porcentajes para pintar

	$query = " SELECT Ccoent, Ccofori, Ccoori, Cpedoc, Cpetdoc, Cpeno1, Cpeno2, Cpeap1, Cpeap2, Ccohis, Cconum ";
	$query .= " FROM ".$empresa."_000017 A ";
	$query .= " LEFT JOIN ".$empresa."_000016 B ";													
	$query .= " ON A.id_persona = CONCAT(Cpedoc, '-', Cpetdoc) ";
	$query .= " WHERE ccovol = 'NO' ";
	$query .= " AND A.Fecha_data BETWEEN '".$fecha."' and '".$fecha1."' ";
	$query .= " ORDER BY 1,2 ";
	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);

	echo "<table align='center' border='1' width='90%'>";

	$y=0;
	$m=0;
	$a=0;
	$ant=0;
	$maternas=0;
	$afines=0;
	$color='#ffffff';
	for($i=1;$i<=$num;$i++)
	{
		$row = mysql_fetch_row($err);

		if ($row[0]!=$ant)
		{
			if($i!=1)
			{
				if(!isset($resumido))
				{
					echo "<td class='fila2' width='8%' align='left' COLSPAN='3'><font size='2'>TOTAL ".$ant.":</font></td>";
					echo "<td class='fila2' width='8%' align='right'><font size='2'>".$y."</font></td>";
					echo "<td class='fila2' width='8%' align='right'><font size='2'>MATERNAS:</font></td>";
					echo "<td class='fila2' width='8%' align='right'><font size='2'>".$m."</font></td>";
					echo "<td class='fila2' width='8%' align='right'><font size='2'>AFIN: ".$a."</font></td></tr>";
				}
				else
				{
					if($color=='fila2')
					{
						$color='fila1';
					}
					else
					{
						$color='fila2';
					}
					echo "<td class='fila2' width='40%' align='left' COLSPAN='3'><font size='2'>TOTAL ".$ant.":</font></td>";
					echo "<td class='fila2' width='10%' align='right'><font size='2'>".$y."</font></td>";
					echo "<td class='fila2' width='30%' align='right'><font size='2'>MATERNAS:</font></td>";
					echo "<td class='fila2' width='10%' align='right'><font size='2'>".$m."</font></td>";
					echo "<td class='fila2' width='20%' align='right'><font size='2'>AFIN: ".$a."</font></td></tr>";
				}
			}
			$y=0;
			$m=0;
			$a=0;

			if(!isset($resumido))
			{
				echo "<tr>";
				echo "<td class='fila2' width='8%' align='left' colspan='7'><font >Entidad: ".$row[0]."</font></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td class='fila1' width='8%' align='left'><font color='#336699' size='2'>NUMERO COMENTARIO</font></td>";
				echo "<td class='fila1' width='8%' align='left'><font color='#336699' size='2'>FECHA</font></td>";
				echo "<td class='fila1' width='13%' align='left'><font color='#336699' size='2'>ORIGEN</font></td>";
				echo "<td class='fila1' width='25%' align='left'><font color='#336699' size='2'>DOCUMENTO DE IDENTIDAD</font></td>";
				echo "<td class='fila1' width='31%' align='left'><font color='#336699' size='2'>NOMBRE</font></td>";
				echo "<td class='fila1' width='5%'  align='left'><font color='#336699' size='2'>MATERNA</font></td>";
				echo "<td class='fila1' width='10%' align='left'><font color='#336699' size='2'>AFIN</font></td>";
				echo "</tr>";
			}
		}

		if($row[9]!='')
		{
			$exp=explode('-',$row[1]);
			$tiempo = mktime(0, 0, 0, $exp[1], $exp[2], $exp[0]) + (40 * 24 * 60 * 60);
			$feca = date('Y/m/d', $tiempo);

			$tiempo = mktime(0, 0, 0, $exp[1], $exp[2], $exp[0]) - (40 * 24 * 60 * 60);
			$fecb = date('Y/m/d', $tiempo);

			$quer1 = " SELECT count(*) "
			."   FROM indiapar"
			."  WHERE diapard1 between '".$fecb."' AND '".$feca."' "
			."    AND diaparhis = '".$row[9]."' ";

			$err1 = odbc_do($conexi,$quer1) or die("ERROR EN QUERY 1");
			if(odbc_fetch_row($err1))
			{
				$num1=odbc_result($err1,1);
			}
			else
			{
				$num1=0;
			}

		}
		else
		{
			$num1=0;
		}


		$query =" SELECT Clitip ";
		$query = $query." FROM ".$empresa."_000008 A ";
		$query = $query." where clidoc='".$row[3]."' and clitid='".$row[4]."'  ";
		$erra=mysql_query($query,$conex);
		$numa=mysql_num_rows($erra);

		if(!isset($resumido))
		{
			echo "<tr>";
			echo "	<td class='fila1' width='8%' align='left'><font color='#336699' size='2'>".$row[10]."</font></td>";
			echo "	<td class='fila1' width='8%' align='left'><font color='#336699' size='2'>".$row[1]."</font></td>";
			echo "	<td class='fila1' width='8%' align='left'><font color='#336699' size='2'>".$row[2]."</font></td>";
			
			// En caso de que no exista registro de la persona en la tabla de usuarios de magenta se busca la información en cliame para dejar los datos en el reporte
			if($row[3]== NULL)
			{
				$numeroHistoria = $row[9];
				$grupo = "cliame";

				$query = " SELECT Pacdoc, Pacno1, Pacno2, Pacap1, Pacap2, Pactdo ";
				$query .= " FROM ".$grupo."_000100 A ";
				$query .= " WHERE pachis = '".$numeroHistoria."' ";
				$respuesta = mysql_query($query,$conex);
				$datosPaciente = mysql_fetch_row($respuesta);

				//Asigno los valores faltando a los campos nulos
				$row[3] = $datosPaciente[0];    //Numero de documento
				$row[5] = $datosPaciente[1];	//Primer nombre
				$row[6] = $datosPaciente[2];	//Segundo nombre
				$row[7] = $datosPaciente[3];	//Primer Apellido
				$row[8] = $datosPaciente[4];	//Segundo apellido

				switch ($datosPaciente[5]) 
				{
					case 'CC':
						$row[4] = "CC-CEDULA DE CIUDADANIA";
						break;
					case 'TI':
						$row[4] = "TI-TARJETA DE IDENTIDAD";
						break;
					case 'CE':
						$row[4] = "CE-CEDULA DE EXTRANJERIA";
						break;
					case 'MS':
						$row[4] = "MS-MENOR SIN IDENTIFICACION";
						break;
					case 'RC':
						$row[4] = "RC-REGISTRO CIVIL";
						break;
					case 'AS':
						$row[4] = "AS-ADULTO SIN IDENTIFICACION";
						break;
					case 'PA':
						$row[4] = "PA-PASAPORTE";
						break;
					case 'NU':
						$row[4] = "NU-NUMERO UNICO DE IDENTIFICACION";
						break;
					case 'NI':
						$row[4] = "NI-NIT";
						break;
					case 'CD':
						$row[4] = "CD-CARNE DIPLOMATICO";
						break;
					case 'NV':
						$row[4] = "NV-CERTIFICADO DE NACIDO VIVO";
						break;
					case 'SC':
						$row[4] = "SC-SALVOCONDUCTO";
						break;
					case 'PE':
						$row[4] = "PE-PERMISO ESPECIAL DE PERMANENCIA";
						break;
					default:
						$ROW[4] = "DOCUMENTO INVALIDO";
						break;
				}
			}

			echo "	<td class='fila1' width='8%' align='left'><font color='#336699' size='2'>".$row[3]."-".$row[4]."</font></td>";
			echo "	<td class='fila1' width='8%' align='left'><font color='#336699' size='2'>".$row[5]." ".$row[6]." ".$row[7]." ".$row[8]."</font></td>";
			
			
			if($num1>0)
			{
				echo "	<td class='fila1' width='8%' align='left'><font color='#336699' size='2'>SI</font></td>";
			}
			else
			{
				echo "	<td class='fila1' width='8%' align='left'><font color='#336699'>&nbsp;</font></td>";
			}

			if($numa>0)
			{
				$rowa = mysql_fetch_row($erra);
				$exp=explode('-',$rowa[0]);
				echo "	<td class='fila1' width='8%' align='left'><font color='#336699' size='2'>".$exp[0]."-".$exp[1]."</font></td>";
			}
			else
			{
				echo "	<td class='fila1' width='8%' align='left'><font color='#336699'>&nbsp;</font></td>";
			}
			echo "</tr>";
		}

		if($num1>0)
		{
			$m++;
			$maternas++;
		}

		if($numa>0)
		{
			$a++;
			$afines++;
		}

		$y=$y+1;
		$ant=$row[0];

	}

	if(!isset($resumido))
	{
		echo "<td class='fila2' width='8%' align='left' COLSPAN='3'><font  size='2'>TOTAL ".$ant.":</font></td>";
		echo "<td class='fila2' width='8%' align='right'><font size='2'>".$y."</font></td>";
		echo "<td class='fila2' width='8%' align='right'><font size='2'>MATERNAS:</font></td>";
		echo "<td class='fila2' width='8%' align='right'><font size='2'>".$m."</font></td>";
		echo "<td class='fila2' width='8%' align='right'><font size='2'>AFIN: ".$a."</font></td></tr>";
	}
	else
	{
		echo "<td class='fila2' width='50%' align='left' COLSPAN='3'><font size='2'>TOTAL ".$ant.":</font></td>";
		echo "<td class='fila2' width='10%' align='right'><font size='2'>".$y."</font></td>";
		echo "<td class='fila2' width='30%' align='right'><font size='2'>MATERNAS:</font></td>";
		echo "<td class='fila2' width='10%' align='right'><font size='2'>".$m."</font></td>";
		echo "<td class='fila2' width='10%' align='right'><font size='2'>AFIN: ".$a."</font></td></tr>";
	}

	echo "<td class='fila2' width='8%' align='left' COLSPAN='3'><font size='2'>TOTAL</font></td>";
	echo "<td class='fila2' width='8%' align='right'><font size='2' >".$num."</font></td>";
	echo "<td class='fila2' width='8%' align='right'><font size='2'>MATERNAS:</font></td>";
	echo "<td class='fila2' width='8%' align='right'><font size='2'>".$maternas."</font></td>";
	echo "<td class='fila2' width='8%' align='right'><font size='2'>AFIN: ".$afines."</font></td>";
	echo "</tr></table></br>";

	//total de personas con comentarios, total de comentarios de maternas
	$query = " SELECT Ccoent, Ccofori, Ccoori, Cpedoc, Cpetdoc, Cpeno1, Cpeno2, Cpeap1, Cpeap2, Ccohis ";
	$query .= " FROM ".$empresa."_000017 A ";
	$query .= " LEFT JOIN ".$empresa."_000016 B ";
	$query .= " ON A.id_persona=CONCAT(Cpedoc, '-', Cpetdoc) ";
	$query .= " WHERE A.Fecha_data ";
	$query .= " BETWEEN '".$fecha."' and '".$fecha1."' ";
	$query .= " order by  1, 2 ";
	$err=mysql_query($query,$conex);
	$num2=mysql_num_rows($err);

	$num3=0;
	for($i=1;$i<=$num2;$i++)
	{
		$row = mysql_fetch_row($err);

		if($row[9]!='' and $row[1]!='0000-00-00')
		{
			$exp=explode('-',$row[1]);
			$tiempo = mktime(0, 0, 0, $exp[1], $exp[2], $exp[0]) + (40 * 24 * 60 * 60);
			$feca = date('Y/m/d', $tiempo);

			$tiempo = mktime(0, 0, 0, $exp[1], $exp[2], $exp[0]) - (40 * 24 * 60 * 60);
			$fecb = date('Y/m/d', $tiempo);

			$quer1 = " SELECT count(*) "
			."   FROM indiapar"
			."  WHERE diapard1 between '".$fecb."' AND '".$feca."' "
			."    AND diaparhis = '".$row[9]."' ";

			$err1 = odbc_do($conexi,$quer1) or die("ERROR EN QUERY 1");
			if(odbc_fetch_row($err1))
			{
				if($num1=odbc_result($err1,1))
				{
					$num3++;
				}
			}
		}

	}
	
	function promedio3($maternas, $num3){
	if ($num3>0)
	{
		//$variac1=($val1*100)/$val2;
		$variac1=round((100-$maternas*100/$num3), 2);
	}else
	{
		$variac1='0';
	}
	return number_format((float)$variac1,2,".",",");
	}
	echo "<center><font color='#00008B'>CANTIDAD TOTAL DE COMENTARIOS Y COMENTARIOS DE MATERNAS</font></center></BR>";

	echo "<table width='70%' align='center'>";
	echo "<td class='encabezadotabla' width='8%' align='right'><font color='#ffffff' size='2'>TOTAL DE COMENTARIOS</font></td>";
	echo "<td class='encabezadotabla' width='8%' align='right'><font color='#ffffff' size='2' >".$num2."</font></td></tr>";
	echo "<tr><td class='encabezadotabla' width='8%' align='right' ><font color='#ffffff' size='2'>TOTAL DE COMENTARIOS DE MATERNAS:</font></td>";
	echo "<td class='encabezadotabla' width='8%' align='right'><font color='#ffffff' size='2'>".$num3."</font></td></tr>";
	echo "<tr><td class='encabezadotabla' width='8%' align='right' ><font color='#ffffff' size='2'>PORCENTAJE DE SATISFACCION MATERNAS:</font></td>";
	//echo "<td bgcolor='#336699' width='8%' align='right'><font color='#ffffff' size='2'>".round((100-$maternas*100/$num3), 2)." %</font></td></tr>";
	echo "<td class='encabezadotabla' width='8%' align='right'><font color='#ffffff' size='2'>".promedio3($maternas, $num3)." %</font></td></tr>";
	echo "<tr><td class='encabezadotabla' width='8%' align='right' ><font color='#ffffff' size='2'>PORCENTAJE DE COMENTARIOS DE MATERNAS:</font></td>";
	echo "<td class='encabezadotabla' width='8%' align='right'><font color='#ffffff' size='2'>".round(($num3*100/$num2), 2)." %</font></td></tr>";
	echo "</table></br>";
	//porcentaje que dijeron que no sobre el total de maternas

	odbc_close($conexi);
	odbc_close_all();
}

}                               

?>
</body>
</html>
