<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<title>Programa de comentarios y sugerencias</title>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>	

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
		$("#fecha").datepicker({
	       showOn: "button",
	       buttonImage: "../../images/medical/root/calendar.gif",
	       buttonImageOnly: true
	    });
});

</script>
</head>
<body>
<?php
include_once("conex.php");

/*****************************************GENERACION DE DIAS FESTIVOS *******************************************************
 * 
 * Este programa permite general los dias no habiles de un año, para que se tenga en cuenta en la semaforizacion
 * 
 * @name  matrix\magenta\procesos\festivos.php
 * @author Carolina Castaño Portilla.
 * 
 * @created 2006-04-12
 * @version 2006-01-31
 * 
 * @modified 2006-01-31  Se crea la opcion de borrar fechas, Carolina Castaño
 * 
 * @table magenta_000026, select, insert
 * 
 *  @var $dia, nombre del dia de la semana para el festivo a ingresar
 *  @var $exp para hacer un explode
 *  @var $fecha, fecha ingresada como festivo
*/
/************************************************************************************************************************* 
  Actualizaciones:
            2017-01-02 (Arleyda Insignares C.)
                        -Se elimina restricción de días a mostrar en calendario
 			2016-05-02 (Arleyda Insignares C.)
 						-Se Modifica el campo de calendario fecha festivo con utilidad jquery y se elimina
						 uso de Zapatec Calendar por errores en la clase.
						-Se cambia encabezado y titulos con ultimo formato
*************************************************************************************************************************/
$wautor="Carolina Castano P.";
$wversion='2007-01-31';
$wactualiz='2017-01-02';
//===========================================================================================================================
/************************html**********************************/

/**
 * Funcion que pinta el encabezado del programa 
 *
 */

function encabezadogral()
{
    global $wactualiz;
    /////////////////////////////////////////////////encabezado general///////////////////////////////////
    $titulo = "SISTEMA DE COMENTARIOS Y SUGERENCIAS";
    // Se muestra el encabezado del programa    
    encabezado($titulo,$wactualiz, "clinica");  

/*	global $empresa;
	global $wautor;
	global $wversion;
	echo "<table align='right'>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Autor: ".$wautor."</font></td>";
	echo "</tr>" ;
	echo "<tr>" ;
	echo "<td><font color=\"#D02090\" size='2'>Version: ".$wversion."</font></td>" ;
	echo "</tr>" ;
	echo "</table></br></br></br>" ;
	echo "<table align='center' border='3' bgcolor='#336699' >\n" ;
	echo "<tr>" ;
	echo "<td><img src='/matrix/images/medical/root/magenta.gif' height='61' width='113'></td>";
	echo "<td><font color=\"#ffffff\"><font size=\"5\"><b>&nbsp;SISTEMA DE COMENTARIOS Y SUGERENCIAS &nbsp;</br></b></font></font></td>" ;
	echo "</tr>" ;
	echo "<tr>" ;
	echo "</table></br>" ;
*/	
     echo "<div align='center'><div align='center' class='fila1' style='width:520px;'><font size='4'><b>GENERACION DE DIAS FESTIVOS</b></font></div></div></BR>";
}

/**
 * traduce el dia de la semana de ingles a español
 *
 * @param unknown_type $day dia en ingles
 * @return unknown
 */
function traducir($day){
	switch ($day){
		case 'Monday':
		$dia="lunes";
		break;
		case 'Tuesday':
		$dia="martes";
		break;
		case 'Wednesday':
		$dia="miercoles";
		break;
		case 'Thursday':
		$dia="jueves";
		break;
		case 'Friday':
		$dia="viernes";
		break;

	}
	return $dia;
}


/********************************funciones************************************/


/****************************PROGRAMA************************************************/
include_once("root/comun.php");

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{   
	/////////////////////////////////////////////////inicialización de variables///////////////////////////////////
	/**
	 * conexion a Matrix
	 */
	

	


	$empresa='magenta';

	/////////////////////////////////////////////////inicialización de variables//////////////////////////
	/////////////////////////////////////////////////acciones concretas///////////////////////////////////

	if (isset ($fecha) and $fecha!='')//si he enviado el formulario, debo guardar una fecha
	{

		//guardo el dia festivo dado si no es sabado o domingo
		$exp=explode('-',$fecha);
		if (date("l", mktime(0, 0, 0, $exp[1], $exp[2], $exp[0]))!='Saturday' and date("l", mktime(0, 0, 0, $exp[1], $exp[2], $exp[0]))!='Sunday')
		{
			//verifico que la fecha no se encuentre registrada
			$query ="SELECT cfec, cdia FROM " .$empresa."_000026 where cfec='".$fecha."' ";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);
			//echo $query;
			if  ($num <=0) //se llena los valores
			{
				$dia=traducir(date("l", mktime(0, 0, 0, $exp[1], $exp[2], $exp[0])));

				$query= " INSERT INTO  " .$empresa."_000026 (medico, Fecha_data, Hora_data, cfec, cdia, seguridad)";
				$query= $query. "VALUES ('magenta','".date("Y-m-d")."','".date("h:i:s")."','".$fecha."', '".$dia."', 'magenta') ";
				$res=mysql_query($query,$conex);
			}
		}
		else
		{
			echo '<script language="Javascript">';
			echo 'alert ("No es necesario ingresar festivos que coincidan con dias sábados o domingos con en este caso")';
			echo '</script>';
		}

	}
	
	if (isset ($borrar))//si he enviado el formulario,con la orden de borrar
	{
				$query= " DELETE FROM  " .$empresa."_000026 WHERE id=".$id;
				$res=mysql_query($query,$conex);
	}


	encabezadogral();

	//busco los dias festivos del año presente
	$query ="SELECT cfec, cdia, id FROM " .$empresa."_000026 where cfec>='".date('Y')."-01-01' and cdia!='sabado' and cdia!='domingo' order by cfec ";
	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);
	//echo $query;
	if  ($num >0) //se llena los valores
	{
		echo "<table align='center' border=1 bordercolor=#000080 width=500 style='border:solid;'>";
		echo "<tr class='encabezadotabla'>";
		echo "<td align=center ><font size=3  face='arial' color='white'>FECHA</td>";
		echo "<td align=center ><font size=3  face='arial' color='white'>DIA</td>";
		echo "<td align=center ><font size=3  face='arial' color='white'>&nbsp;</td></TR>";

		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($err);
			echo "<tr>";
			echo "<td align=center ><font size=3  face='arial' color='#000080'>".$row[0]."</td>";
			echo "<td align=center ><font size=3  face='arial' color='#000080'>".$row[1]."</td>";
			echo "<td align=center ><font size=3  face='arial' color='#000080'><A HREF='festivos.php?borrar=1&id=".$row[2]."'>Borrar</a></td></TR>";

		}
		echo "</TABLE></br>";
		echo "</fieldset></br></br>";

		if (isset ($fecha) and $num==1)//se entro el pprimer dia festivo
		{
			//guardo los sabados y domingos del año
			for ($i=1;$i<=12;$i++) //rotacion de meses
			{
				for ($j=1;$j<=31;$j++) //rotacion de dias
				{

					if (date("l", mktime(0, 0, 0, $i, $j, date('Y')))=='Saturday' )
					{
						$query= " INSERT INTO  " .$empresa."_000026 (medico, Fecha_data, Hora_data, cfec, cdia, seguridad)";
						$query= $query. "VALUES ('magenta','".date("Y-m-d")."','".date("h:i:s")."','".date('Y')."-".$i."-".$j."', 'sabado', 'magenta') ";
						$res=mysql_query($query,$conex);

					}

					if (date("l", mktime(0, 0, 0, $i, $j, date('Y')))=='Sunday')
					{

						$query= " INSERT INTO  " .$empresa."_000026 (medico, Fecha_data, Hora_data, cfec, cdia, seguridad)";
						$query= $query. "VALUES ('magenta','".date("Y-m-d")."','".date("h:i:s")."','".date('Y')."-".$i."-".$j."', 'domingo', 'magenta') ";
						$res=mysql_query($query,$conex);
					}

				}
			}

		}
	}
	else
	{

		echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
		echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>NO EXISTE REGISTRADO NINGUN DIA FESTIVO PARA EL AÑO: ".date('Y')."</td><tr>";
		echo "</table></br></br>";

	}

	//pinto formulario para ingreso de nueva fecha
	echo "<fieldset style='border:solid;border-color:#00008B; width=700' align=center></br>";
	echo "<form NAME='ingreso' ACTION='' METHOD='POST'>";
	echo "<table align='center'>";
	echo "<tr>";
	//$cal="calendario('fecha','1')";
	echo "<td align=center ><font size=3  face='arial' color='#000080'>INGRESE EL DIA FESTIVO DESEADO</td></TR>";
	echo "<td align=center >&nbsp;</td></TR>";
	echo "<tr><td align=center ><font size=3  face='arial' ></font><input type='text' readonly='readonly' id='fecha' name='fecha' value='' class=tipo3 ></td></tr>";
	echo "</td>";
	echo "</tr></TABLE></br>";
	echo "<TABLE align=center><tr>";
	echo "<tr>";
	echo "<td align=center colspan=14><font size='2'  align=center face='arial'><input type='submit' name='aceptar' value='INGRESAR' ></td></tr>";
	echo "</TABLE>";
	echo "</td>";
	echo "</tr>	";
	echo "</form>";
	echo "</fieldset>";

}
?>
</body>
</html>

