<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
<title>Programa de AFINIDAD</title>
</head>
<body>
<?php
include_once("conex.php"); 

/********************************************* 	Informacion de base de datos AAA y BBB ************************************
 * 
 * Generar un reporte que permita filtrar información relacionada con gustos, deportes, edades, principales causas de enfermedad, comunidades, ocupación, mes de nacimiento
 * 
 * @name  matrix\magenta\procesos\consultasAAA.php
 * @author Carolina Castaño Portilla.
 * 
 * @created 2006-01-18
 * @version 2006-01-24
 * 
 * @modified 2006-01-24, se adiciona hipervinculo a la pagina de cada afin
 * @modified 2007-05-02, Modificacion hecha por Juan David Londoño; se adiciona una funcion y se puso a consultar las fechas especiales como el dia de la madre
 * 
 * @table magenta_000008, select, 
 * @table magenta_000009, select, 
 * @table magenta_000011, select
 * @table inocu, select
 * 
 *  @wvar $buscar recibe el nombre del campo que es critrio para buscar 
 *  @wvar $cantidad cuenta el nmumero de bbb encontrados
 *  @wvar $color para alternar el color en la tabla de resultados
 *  @wvar $comunidades, comunidad seleccionada por el usuario como criterio de busqueda
 *  @wvar $cumpleanos, mes seleccionado por el usuario como criterio de busqueda
 *  @wvar $deportes deporte seleccionado por el usuario como criterio de busqueda
 *  @wvar $edades,  edad seleccionado por el usuario como criterio de busqueda
 *  @wvar $enfermedades, enfermedad seleccionado por el usuario como criterio de busqueda
 *  @wvar $fecha1 fecha incial para busqueda de afines por edades
 *  @wvar $fecha2 fecha final para busqueda de afines por edades
 *  @wvar $gustos gusto seleccionado por el usuario como criterio de busqueda
 *  @wvar $mes numero del mes seleccionado por el usuario para buscar cumpleanos
 *  @wvar $mostrar indica que no hay ninguna preferencia de contacto en 1, por lo que se desliega una rayita
 *  @wvar $ocupaciones,  ocupacion seleccionado por el usuario como criterio de busqueda
 *  @wvar $radio , radio button o criterio seleccionado para la busqueda
*/
/************************************************************************************************************************* 
  Actualizaciones:
 			2016-05-05 (Arleyda Insignares C.)
						-Se cambia encabezado y titulos con ultimo formato.
*************************************************************************************************************************/
$wautor="Carolina Castano P.";
$wversion='2007-05-02';
$wactualiz='2016-05-05';

/****************************************************  funciones   *************************************************/

/**
 * Mensaje si falla la conexion a base de datos
 *
 */
function  DisplayError()
{
	echo '<script language="Javascript">';
	echo 'alert ("Error al conectarse con la base de datos, intente más tarde")';
	echo '</script>';

}
/**
 * A cada gusto le encuentra su nombre en la base de datos
 *
 * @param unknown_type $gusto
 * @return unknown
 */
function adecuarGustos($gusto){

	switch ($gusto)
	{
		case 'Lectura':
		$gusto2='cdelec';
		break;

		case 'Cine':
		$gusto2='cdecin';
		break;

		case 'Musica':
		$gusto2='cdemus';
		break;

		case 'Artes plasticas':
		$gusto2='cdeapl';
		break;

		case 'Artes dramaticas':
		$gusto2='cdeadr';
		break;
	}

	return $gusto2;
}

/**
 * A cada deporte le encuentra su nombre en la base de datos
 *
 * @param unknown_type $deporte
 * @return unknown
 */
function adecuarDeportes($deporte){


	switch ($deporte)
	{
		case 'Futbol':
		$deporte2='cdefut';
		break;

		case 'Tennis':
		$deporte2='cdeten';
		break;

		case 'Gym':
		$deporte2='cdegym';
		break;

		case 'Golf':
		$deporte2='cdegol';
		break;

		case 'Equitacion':
		$deporte2='cdeequ';
		break;
	}
	return $deporte2;
}

/**
 * A cada enfermedad le encuentra su nombre en la base de datos
 *
 * @param unknown_type $enfermedad
 * @return unknown
 */
function adecuarEnfermedades($enfermedad){


	switch ($enfermedad)
	{
		case 'Sistema Nervioso':
		$enfermedad2='cdesnc';
		break;

		case 'Sistema Respiratorio':
		$enfermedad2='cdesrs';
		break;

		case 'Sistema Renal':
		$enfermedad2='cdesrn';
		break;

		case 'Sistema Cardiovascular':
		$enfermedad2='cdesca';
		break;

		case 'Sistema Musculoesqueletico':
		$enfermedad2='cdesmu';
		break;
	}
	return $enfermedad2;
}

/**
 * A cada comunidad le encuentra su nombre en la base de datos
 *
 * @param unknown_type $comunidad
 * @return unknown
 */
function adecuarComunidades($comunidad){

	switch ($comunidad)
	{
		case 'Bebes':
		$comunidad2='cdebeb';
		break;

		case 'Familias':
		$comunidad2='cdefam';
		break;

		case 'Adolecentes':
		$comunidad2='cdeado';
		break;

		case 'Adultos':
		$comunidad2='cdeadu';
		break;

	}

	return $comunidad2;
}


/**
 * A cada mes le encuentra su numero
 *
 * @param unknown_type $mes
 * @return unknown
 */
function numero_mes($mes){
	switch ($mes){
		case 'Enero':
		$numero_mes='01';
		break;
		case "Febrero":
		$numero_mes='02';
		break;
		case "Marzo":
		$numero_mes='03';
		break;
		case "Abril":
		$numero_mes='04';
		break;
		case "Mayo":
		$numero_mes='05';
		break;
		case "Junio":
		$numero_mes='06';
		break;
		case "Julio":
		$numero_mes='07';
		break;
		case "Agosto":
		$numero_mes='08';
		break;
		case "Septiembre":
		$numero_mes='09';
		break;
		case "Octubre":
		$numero_mes='10';
		break;
		case "Noviembre":
		$numero_mes='11';
		break;
		case "Diciembre":
		$numero_mes='12';
		break;
	}
	return $numero_mes;
}


/**
 * A cada fecha especial le retorna el sexo
 *
 * @param unknown_type $fecesp
 * @return unknown
 * 
 * creacion: 2007-05-02 --> Creada por Juan David Londoño
 */
function fechaEspecial($fecespe)
{

	switch ($fecespe)
	{
		case 'Dia de la madre':
		$sexo='F-FEMENINO';
		break;

		case 'Dia del padre':
		$sexo='M-MASCULINO';
		break;
	}

	return $sexo;
}





/**
 * A cada nombre de edad le encuentra la fecha inicial de busqueda
 *
 * @param unknown_type $edad
 * @return unknown
 */
function calcularFecha1($edad)
{
	switch ($edad)
	{
		case 'Bebe (0-3 anos)':
		$fecha1=date('Y')-3;
		break;

		case 'ninos (4-12 anos)':
		$fecha1=date('Y')-12;
		break;

		case 'adolecentes (13-18 anos)':
		$fecha1=date('Y')-18;
		break;

		case 'jovenes (19-29 anos)':
		$fecha1=date('Y')-29;
		break;

		case 'adultos (30-60 anos)':
		$fecha1=date('Y')-60;
		break;

		case 'Mayores (61 en adelante)':
		$fecha1=date('Y')-150;
		break;

	}

	$fecha1=$fecha1."-".date('m-d');
	return $fecha1;
}

/**
 * A cada edad le encuentra la fecha final de busqueda
 *
 * @param unknown_type $edad
 * @return unknown
 */
function calcularFecha2($edad)
{
	switch ($edad)
	{
		case 'Bebe (0-3 anos)':
		$fecha2=date('Y-m-d');
		break;

		case 'ninos (4-12 anos)':
		$fecha2=date('Y')-3;
		$fecha2=$fecha2."-".date('m-d');
		break;

		case 'adolecentes (13-18 anos)':
		$fecha2=date('Y')-12;
		$fecha2=$fecha2."-".date('m-d');
		break;

		case 'jovenes (19-29 anos)':
		$fecha2=date('Y')-18;
		$fecha2=$fecha2."-".date('m-d');
		break;

		case 'adultos (30-60 anos)':
		$fecha2=date('Y')-29;
		$fecha2=$fecha2."-".date('m-d');
		break;

		case 'Mayores (61 en adelante)':
		$fecha2=date('Y')-60;
		$fecha2=$fecha2."-".date('m-d');
		break;
	}

	return $fecha2;
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
	/////////////////////////////////////////////////encabezado general///////////////////////////////////
	echo "</br><table align='center'>\n" ;
	echo "<tr>" ;
	echo "<td><img src='/matrix/images/medical/Magenta/aaa.gif'  height='61' width='113'></td>";
	echo "</tr>" ;
	echo "</table></br></br>" ;
    echo "<div align='center'><div align='center' class='fila1' style='width:520px;'><font size='4'><b>CONSULTAS AFINIDAD</b></font></div></div></BR></br>";
	echo "\n" ;

	/////////////////////////////////////////////////inicialización de variables///////////////////////////////////

	/**
	 * include de conexión a base de datos Matrix
	 *
	 */
	


	

	$bd='facturacion';

	/**
	 * Include de conexion a base de datos Unix
	 *
	 */
	include_once("socket.php");

	$empresa='magenta';

	/////////////////////////////////////////////////inicialización de variables//////////////////////////
	/////////////////////////////////////////////////acciones concretas///////////////////////////////////


	if (!isset ($radio)) ///////////////////////////entramos por primera vez al reporte/////////////////////////
	{

		echo "<center><font color='#00008B'>SELECCIONE POR FAVOR SU CRITERIO DE BUSQUEDA:</font></center></BR>";


		// Busqueda de comentario entre dos fechas

		echo "<fieldset style='border:solid;border-color:#00008B; width=700' align=center></br>";

		echo "<form NAME='ingreso' ACTION='' METHOD='POST'>";
		echo "<table align='center'>";
		echo "<tr>";
		echo "<td  bgcolor=#336699 ><input type='Radio' name='radio' value='GUSTO'><font size=3  face='arial' color='#ffffff'>Gustos:</font>";
		echo "<Select name='gustos'>";
		echo "<option>Lectura</option><option>Cine</option><option>Musica</option><option>Artes plasticas</option><option>Artes dramaticas</option></select></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td  bgcolor=#336699 ><input type='Radio' name='radio' value='DEPORTE'><font size=3  face='arial' color='#ffffff'>Deportes:</font>";
		echo "<Select name='deportes'>";
		echo "<option>Futbol</option><option>Tennis</option><option>Gym</option><option>Golf</option><option>Equitacion</option></select></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td  bgcolor=#336699 ><input type='Radio' name='radio' value='EDAD'><font size=3  face='arial' color='#ffffff'>Edades:</font>";
		echo "<Select name='edades'>";
		echo "<option>Bebe (0-3 anos)</option><option>ninos (4-12 anos)</option><option>adolencentes (13-18 anos)</option><option>jovenes (19-29 anos)</option><option>adultos (30-60 anos)</option><option>Mayores (61 en adelante)</option></select></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#336699 ><input type='Radio' name='radio' value='ENFERMEDAD'><font size=3  face='arial' color='#ffffff'>Causas de enfermedad:</font>";
		echo "<Select name='enfermedades'>";
		echo "<option>Sistema Nervioso</option><option>Sistema Respiratorio</option><option>Sistema Renal</option><option>Sistema Cardiovascular</option><option>Sistema Musculoesqueletico</option></select></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td  bgcolor=#336699 ><input type='Radio' name='radio' value='COMUNIDAD'><font size=3  face='arial' color='#ffffff'>Comunidades:</font>";
		echo "<Select name='comunidades'>";
		echo "<option>Bebes</option><option>Familias</option><option>Adolecentes</option><option>Adultos</option></select></td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td  bgcolor=#336699 ><input type='Radio' name='radio' value='OCUPACION'><font size=3  face='arial' color='#ffffff'>Ocupacion:</font>";
		echo "<Select name='ocupaciones'>";

		$query="select ocucod,ocunom from inocu where ocuact='S' order by ocunom";
		$err_o = odbc_do($conex_o,$query);
		While (odbc_fetch_row($err_o))
		{
			echo "<option>".odbc_result($err_o,1)."-".odbc_result($err_o,2)."</option>";
		}
		echo "</Select>";
		echo "</tr>";
		echo "<tr>";
		echo "<td  bgcolor=#336699 ><input type='Radio' name='radio' value='CUMPLEANO'><font size=3  face='arial' color='#ffffff'>Mes de nacimiento:</font>";
		echo "<Select name='cumpleanos'>";
		echo "<option>Enero</option><option>Febrero</option><option>Marzo</option><option>Abril</option><option>Mayo</option><option>Junio</option><option>Julio</option><option>Agosto</option><option>Septiembre</option><option>Octubre</option><option>Noviembre</option><option>Diciembre</option></select></td>";
		echo "</tr>";
		echo "<tr>";// 2007-05-02
		echo "<td  bgcolor=#336699 ><input type='Radio' name='radio' value='FECESP'><font size=3  face='arial' color='#ffffff'>Fechas especiales:</font>";
		echo "<Select name='fecespe'>";
		echo "<option>Dia de la madre</option><option>Dia del padre</option></td>";
		echo "</tr>";
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
	else ////////////////////se han seleccionado los parametros del reporte///////////////////////////////////
	{
		echo "<center><font color='#00008B'>RESULTADOS PARA ".$radio.": ";

		switch ($radio)
		{
			case 'GUSTO':
			echo $gustos;
			$buscar=adecuarGustos($gustos);
			$query ="SELECT clidoc, clitid, clinom, cliap1, cliap2, clifna, clipro, clite1, clite2, climov, cliem1, clidir, climun, clilt1, clilt2, clilem, clilmo, clildi FROM ".$empresa."_000008, ".$empresa."_000009 where clidoc=cdedoc and clitid=cdetid and clitip='AFIN-AAA-1' and ".$buscar."='on' ";
			break;

			case 'DEPORTE':
			echo $deportes;
			$buscar=adecuarDeportes($deportes);
			$query ="SELECT clidoc, clitid, clinom, cliap1, cliap2, clifna, clipro, clite1, clite2, climov, cliem1, clidir, climun, clilt1, clilt2, clilem, clilmo, clildi FROM ".$empresa."_000008, ".$empresa."_000009 where clidoc=cdedoc and clitid=cdetid and clitip='AFIN-AAA-1' and ".$buscar."='on' ";
			break;

			case 'EDAD':
			echo $edades;
			$fecha1=calcularFecha1($edades);
			$fecha2=calcularFecha2($edades);
			$query ="SELECT clidoc, clitid, clinom, cliap1, cliap2, clifna, clipro, clite1, clite2, climov, cliem1, clidir, climun, clilt1, clilt2, clilem, clilmo, clildi FROM ".$empresa."_000008, ".$empresa."_000009 where clidoc=cdedoc and clitid=cdetid and clitip='AFIN-AAA-1' and clifna >='".$fecha1."' and clifna <'".$fecha2."' ";
			break;

			case 'ENFERMEDAD':
			echo $enfermedades;
			$buscar=adecuarEnfermedades($enfermedades);
			$query ="SELECT clidoc, clitid, clinom, cliap1, cliap2, clifna, clipro, clite1, clite2, climov, cliem1, clidir, climun, clilt1, clilt2, clilem, clilmo, clildi FROM ".$empresa."_000008, ".$empresa."_000009 where clidoc=cdedoc and clitid=cdetid and clitip='AFIN-AAA-1' and ".$buscar."='on' ";
			break;

			case 'COMUNIDAD':
			echo $comunidades;
			$buscar=adecuarComunidades($comunidades);
			$query ="SELECT clidoc, clitid, clinom, cliap1, cliap2, clifna, clipro, clite1, clite2, climov, cliem1, clidir, climun, clilt1, clilt2, clilem, clilmo, clildi FROM ".$empresa."_000008, ".$empresa."_000009 where clidoc=cdedoc and clitid=cdetid and clitip='AFIN-AAA-1'and ".$buscar."='on' ";
			break;

			case 'OCUPACION':
			echo $ocupaciones;
			$query ="SELECT clidoc, clitid, clinom, cliap1, cliap2, clifna, clipro, clite1, clite2, climov, cliem1, clidir, climun, clilt1, clilt2, clilem, clilmo, clildi FROM ".$empresa."_000008, ".$empresa."_000009 where clidoc=cdedoc and clitid=cdetid and clitip='AFIN-AAA-1' and clipro='".$ocupaciones."' ";
			break;

			case 'CUMPLEANO':
			echo $cumpleanos;
			$mes=numero_mes($cumpleanos);
			$query ="SELECT clidoc, clitid, clinom, cliap1, cliap2, clifna, clipro, clite1, clite2, climov, cliem1, clidir, climun, clilt1, clilt2, clilem, clilmo, clildi FROM ".$empresa."_000008, ".$empresa."_000009 where clidoc=cdedoc and clitid=cdetid and clitip='AFIN-AAA-1' and mid(clifna,6,2)='".$mes."' ";
			break;
			
			// 2007-05-02
			case 'FECESP':
			$sexo=fechaEspecial($fecespe);
			$query ="SELECT clidoc, clitid, clinom, cliap1, cliap2, clifna, clipro, clite1, clite2, climov, cliem1, clidir, climun, clilt1, clilt2, clilem, clilmo, clildi " .
					  "FROM ".$empresa."_000008, ".$empresa."_000011 " .
					 "where clidoc=Cafpdo " .
					   "and clisex='".$sexo."' " .
					   "and clitip='AFIN-AAA-1' " .
					   "and Cafrel like '%hij%' ";
			break;
			
		}

		$err=mysql_query($query,$conex);
		$num=mysql_num_rows($err);
		echo "</center></BR>";
		echo "<center> AFINES AAA </BR>";
		echo "<center>(".$num.")</font></center></BR>";

		echo "<table align='center' border='1' width='100%'>";
		echo "<tr>";
		echo "	<td bgcolor='#336699' width='8%' align='center'><font size='2' color='#ffffff'>DOCUMENTO DE IDENTIDAD</font></td>";
		echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>NOMBRE</font></td>";
		echo "	<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>FECHA DE NACIMIENTO</font></td>";
		echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>TELEFONO 1</font></td>";
		echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>TELEFONO 2</font></td>";
		echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>MOVIL</font></td>";
		echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>EMAIL</font></td>";
		echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>DIRECCION</font></td>";
		echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>PREFERENCIA DE CONTACTO</font></td>";
		echo "</tr>";

		$color='#F8F8FF';

		for($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_row($err);
			$exp=explode('-', $row[1]);

			if ($color=='#FFFFFF')
			{
				$color='#F8F8FF';
			}
			else
			{
				$color='#FFFFFF';
			}

			echo "<tr>";
			echo "	<td  width='8%' align='center' bgcolor='".$color."'><a href='/Matrix/Magenta/procesos/Magenta.php?ced=1&doc=".$row[0]."&tipDoc=".$row[1]."' target='_blank'><font size='2' color='#336699'>".$row[0]."-".$exp[0]."</a></font></td>";
			echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row[2]." ".$row[3]." ".$row[4]."</font></td>";
			echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row[5]."</font></td>";
			echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row[7]."</font></td>";
			echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row[8]."</font></td>";
			echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row[9]."</font></td>";
			echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row[10]."</font></td>";
			echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row[11]." ".$row[12]."</font></td>";

			if (isset($mostrar))
			unset ($mostrar);

			for($j=13;$j<=17;$j++)
			{
				if ($row[$j]=='1' and !isset($mostrar))
				{
					switch ($j)
					{
						case 13:
						echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>Telefono 1</font></td>";
						break;

						case 14:
						echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>Telefono 2</font></td>";
						break;

						case 15:
						echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>Email</font></td>";
						break;

						case 16:
						echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>Movil</font></td>";
						break;

						case 17:
						echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>Direccion</font></td>";
						break;
					}
					$mostrar=1;

				}
			}

			if (!isset($mostrar))
			echo "	<td  width='8%' align='center'><font size='2' color='#336699'>-</font></td>";
			echo "</tr>";
		}

		echo "</table>";

		/////////////////////////////////////LO MISMO PARA PERSONALIDADES
		switch ($radio)
		{
			case 'GUSTO':

			$buscar=adecuarGustos($gustos);
			$query ="SELECT clidoc, clitid, clinom, cliap1, cliap2, clifna, clipro, clite1, clite2, climov, cliem1, clidir, climun, clilt1, clilt2, clilem, clilmo, clildi FROM ".$empresa."_000008, ".$empresa."_000009 where clidoc=cdedoc and clitid=cdetid and clitip='PER-AAA-1' and ".$buscar."='on' ";
			break;

			case 'DEPORTE':

			$buscar=adecuarDeportes($deportes);
			$query ="SELECT clidoc, clitid, clinom, cliap1, cliap2, clifna, clipro, clite1, clite2, climov, cliem1, clidir, climun, clilt1, clilt2, clilem, clilmo, clildi FROM ".$empresa."_000008, ".$empresa."_000009 where clidoc=cdedoc and clitid=cdetid and clitip='PER-AAA-1' and ".$buscar."='on' ";
			break;

			case 'EDAD':

			$fecha1=calcularFecha1($edades);
			$fecha2=calcularFecha2($edades);
			$query ="SELECT clidoc, clitid, clinom, cliap1, cliap2, clifna, clipro, clite1, clite2, climov, cliem1, clidir, climun, clilt1, clilt2, clilem, clilmo, clildi FROM ".$empresa."_000008, ".$empresa."_000009 where clidoc=cdedoc and clitid=cdetid and clitip='PER-AAA-1' and clifna >='".$fecha1."' and clifna <'".$fecha2."' ";
			break;

			case 'ENFERMEDAD':

			$buscar=adecuarEnfermedades($enfermedades);
			$query ="SELECT clidoc, clitid, clinom, cliap1, cliap2, clifna, clipro, clite1, clite2, climov, cliem1, clidir, climun, clilt1, clilt2, clilem, clilmo, clildi FROM ".$empresa."_000008, ".$empresa."_000009 where clidoc=cdedoc and clitid=cdetid and clitip='PER-AAA-1' and ".$buscar."='on' ";
			break;

			case 'COMUNIDAD':

			$buscar=adecuarComunidades($comunidades);
			$query ="SELECT clidoc, clitid, clinom, cliap1, cliap2, clifna, clipro, clite1, clite2, climov, cliem1, clidir, climun, clilt1, clilt2, clilem, clilmo, clildi FROM ".$empresa."_000008, ".$empresa."_000009 where clidoc=cdedoc and clitid=cdetid and clitip='PER-AAA-1'and ".$buscar."='on' ";
			break;

			case 'OCUPACION':

			$query ="SELECT clidoc, clitid, clinom, cliap1, cliap2, clifna, clipro, clite1, clite2, climov, cliem1, clidir, climun, clilt1, clilt2, clilem, clilmo, clildi FROM ".$empresa."_000008, ".$empresa."_000009 where clidoc=cdedoc and clitid=cdetid and clitip='PER-AAA-1' and clipro='".$ocupaciones."' ";
			break;

			case 'CUMPLEANO':

			$mes=numero_mes($cumpleanos);
			$query ="SELECT clidoc, clitid, clinom, cliap1, cliap2, clifna, clipro, clite1, clite2, climov, cliem1, clidir, climun, clilt1, clilt2, clilem, clilmo, clildi FROM ".$empresa."_000008, ".$empresa."_000009 where clidoc=cdedoc and clitid=cdetid and clitip='PER-AAA-1' and mid(clifna,6,2)='".$mes."' ";
			break;
			
			// 2007-05-02
			case 'FECESP':
			$sexo=fechaEspecial($fecespe);
			$query ="SELECT clidoc, clitid, clinom, cliap1, cliap2, clifna, clipro, clite1, clite2, climov, cliem1, clidir, climun, clilt1, clilt2, clilem, clilmo, clildi " .
					  "FROM ".$empresa."_000008, ".$empresa."_000011 " .
					 "where clidoc=Cafpdo " .
					   "and clisex='".$sexo."' " .
					   "and clitip='PER-AAA-1' " .
					   "and Cafrel like '%hij%' ";
		}

		$err=mysql_query($query,$conex);
		$num=mysql_num_rows($err);
		echo "</BR><center><font color='#00008B'><B>PERSONALIDADES AAA </BR>";
		echo "(".$num.") </font></B></center></BR>";

		echo "<table align='center' border='1' width='100%'>";
		echo "<tr>";
		echo "	<td bgcolor='#336699' width='8%' align='center'><font size='2' color='#ffffff'>DOCUMENTO DE IDENTIDAD</font></td>";
		echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>NOMBRE</font></td>";
		echo "	<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>FECHA DE NACIMIENTO</font></td>";
		echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>TELEFONO 1</font></td>";
		echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>TELEFONO 2</font></td>";
		echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>MOVIL</font></td>";
		echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>EMAIL</font></td>";
		echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>DIRECCION</font></td>";
		echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>PREFERENCIA DE CONTACTO</font></td>";
		echo "</tr>";

		$color='#F8F8FF';

		for($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_row($err);
			$exp=explode('-', $row[1]);

			if ($color=='#FFFFFF')
			{
				$color='#F8F8FF';
			}
			else
			{
				$color='#FFFFFF';
			}

			echo "<tr>";
			echo "	<td  width='8%' align='center' bgcolor='".$color."'><a href='/Matrix/Magenta/procesos/Magenta.php?ced=1&doc=".$row[0]."&tipDoc=".$row[1]."' target='_blank'><font size='2' color='#336699'>".$row[0]."-".$exp[0]."</a></font></td>";
			echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row[2]." ".$row[3]." ".$row[4]."</font></td>";
			echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row[5]."</font></td>";
			echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row[7]."</font></td>";
			echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row[8]."</font></td>";
			echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row[9]."</font></td>";
			echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row[10]."</font></td>";
			echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row[11]." ".$row[12]."</font></td>";

			if (isset($mostrar))
			unset ($mostrar);

			for($j=13;$j<=17;$j++)
			{
				if ($row[$j]=='1' and !isset($mostrar))
				{
					switch ($j)
					{
						case 13:
						echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>Telefono 1</font></td>";
						break;

						case 14:
						echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>Telefono 2</font></td>";
						break;

						case 15:
						echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>Email</font></td>";
						break;

						case 16:
						echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>Movil</font></td>";
						break;

						case 17:
						echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>Direccion</font></td>";
						break;
					}
					$mostrar=1;

				}
			}

			if (!isset($mostrar))
			echo "	<td  width='8%' align='center'><font size='2' color='#336699'>-</font></td>";
			echo "</tr>";
		}

		echo "</table>";
		/////////////////////////////////////

		if ($radio=='EDAD')
		{

			$query ="SELECT clidoc, clitid, clinom, cliap1, cliap2, clifna FROM ".$empresa."_000008 where clitip='AFIN-BBB-2' and clifna >='".$fecha1."' and clifna <'".$fecha2."' ";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);

			if ($num>0)
			{
				echo "</BR><center><font color='#00008B'>AFINES BBB</font></center>";
				echo "<table align='center' border='1' width='100%'>";
				echo "<tr>";
				echo "	<td bgcolor='#336699' width='8%' align='center'><font size='2' color='#ffffff'>DOCUMENTO BBB</font></td>";
				echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>NOMBRE BBB</font></td>";
				echo "	<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>FECHA DE NACIMIENTO</font></td>";
				echo "	<td bgcolor='#336699' width='8%' align='center'><font size='2' color='#ffffff'>DOCUMENTO AAA</font></td>";
				echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>NOMBRE AAA</font></td>";
				echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>TELEFONO 1</font></td>";
				echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>TELEFONO 2</font></td>";
				echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>MOVIL</font></td>";
				echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>EMAIL</font></td>";
				echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>DIRECCION</font></td>";
				echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>PREFERENCIA DE CONTACTO</font></td>";
				echo "</tr>";
			}

			$cantidad=0;
			for($i=1;$i<=$num;$i++)
			{
				$row = mysql_fetch_row($err);
				$query ="select clidoc, clitid, clinom, cliap1, cliap2, clite1, clite2, climov, cliem1, clidir, climun, clilt1, clilt2, clilem, clilmo, clildi  FROM ".$empresa."_000008, ".$empresa."_000011 where cafrdo='".$row[0]."' and cafrti='".$row[1]."' and cafpdo=clidoc and cafpti=clitid and clitip='AFIN-AAA-1' ";
				$err2=mysql_query($query,$conex);
				$num2=mysql_num_rows($err2);

				IF ($num2>0)
				{
					if ($color=='#FFFFFF')
					{
						$color='#F8F8FF';
					}
					else
					{
						$color='#FFFFFF';
					}

					$row2 = mysql_fetch_row($err2);
					$exp=explode('-', $row[1]);
					$exp2=explode('-', $row2[1]);
					echo "<tr>";
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><a href='/Matrix/Magenta/procesos/Magenta.php?ced=1&doc=".$row[0]."&tipDoc=".$row[1]."' target='_blank'><font size='2' color='#336699'>".$row[0]."-".$exp[0]."</font></a></td>";
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row[2]." ".$row[3]." ".$row[4]."</font></td>";
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row[5]."</font></td>";
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row2[0]."-".$exp2[0]."</font></td>";
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row2[2]." ".$row2[3]." ".$row2[4]."</font></td>";
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row2[5]."</font></td>";
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row2[6]."</font></td>";
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row2[7]."</font></td>";
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row2[8]."</font></td>";
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row2[9]." ".$row2[10]."</font></td>";

					if (isset($mostrar))
					unset ($mostrar);

					for($j=11;$j<=15;$j++)
					{
						if ($row2[$j]=='1' and !isset($mostrar))
						{
							switch ($j)
							{
								case 11:
								echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>Telefono 1</font></td>";
								break;

								case 12:
								echo "	<td  width='8%' align='center' bgcolor='".$color."' ><font size='2' color='#336699'>Telefono 2</font></td>";
								break;

								case 13:
								echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>Email</font></td>";
								break;

								case 14:
								echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>Movil</font></td>";
								break;

								case 15:
								echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>Direccion</font></td>";
								break;
							}
							$mostrar=1;
						}
					}
					if (!isset($mostrar))
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>-</font></td>";
					echo "</tr>";

					$cantidad++;
				}
			}
			
			echo "</BR><center><font color='#00008B'>(".$cantidad.")</font></center></BR>";
			echo "</table>";

			//LO MISMO PARA PERSONALIDADES

			echo "</BR><center><font color='#00008B'>PERSONALIDADES BBB</font></center>";
			$query ="SELECT clidoc, clitid, clinom, cliap1, cliap2, clifna FROM ".$empresa."_000008 where clitip='PER-BBB-2' and clifna >='".$fecha1."' and clifna <'".$fecha2."' ";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);

			if ($num>0)
			{
				echo "</BR><center><font color='#00008B'>AFINES BBB </font></center></BR>";
				echo "<table align='center' border='1' width='100%'>";
				echo "<tr>";
				echo "	<td bgcolor='#336699' width='8%' align='center'><font size='2' color='#ffffff'>DOCUMENTO BBB</font></td>";
				echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>NOMBRE BBB</font></td>";
				echo "	<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>FECHA DE NACIMIENTO</font></td>";
				echo "	<td bgcolor='#336699' width='8%' align='center'><font size='2' color='#ffffff'>DOCUMENTO AAA</font></td>";
				echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>NOMBRE AAA</font></td>";
				echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>TELEFONO 1</font></td>";
				echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>TELEFONO 2</font></td>";
				echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>MOVIL</font></td>";
				echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>EMAIL</font></td>";
				echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>DIRECCION</font></td>";
				echo "<td bgcolor='#336699' width='8%' align='center'><font  size='2' color='#ffffff'>PREFERENCIA DE CONTACTO</font></td>";
				echo "</tr>";
			}

			$cantidad=0;
			for($i=1;$i<=$num;$i++)
			{
				$row = mysql_fetch_row($err);
				$query ="select clidoc, clitid, clinom, cliap1, cliap2, clite1, clite2, climov, cliem1, clidir, climun, clilt1, clilt2, clilem, clilmo, clildi  FROM ".$empresa."_000008, ".$empresa."_000011 where cafrdo='".$row[0]."' and cafrti='".$row[1]."' and cafpdo=clidoc and cafpti=clitid and clitip='AFIN-AAA-1' ";
				$err2=mysql_query($query,$conex);
				$num2=mysql_num_rows($err2);

				IF ($num2>0)
				{
					if ($color=='#FFFFFF')
					{
						$color='#F8F8FF';
					}
					else
					{
						$color='#FFFFFF';
					}

					$row2 = mysql_fetch_row($err2);
					$exp=explode('-', $row[1]);
					$exp2=explode('-', $row2[1]);
					echo "<tr>";
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><a href='/Matrix/Magenta/procesos/Magenta.php?ced=1&doc=".$row[0]."&tipDoc=".$row[1]."' target='_blank'><font size='2' color='#336699'>".$row[0]."-".$exp[0]."</font></a></td>";
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row[2]." ".$row[3]." ".$row[4]."</font></td>";
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row[5]."</font></td>";
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row2[0]."-".$exp2[0]."</font></td>";
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row2[2]." ".$row2[3]." ".$row2[4]."</font></td>";
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row2[5]."</font></td>";
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row2[6]."</font></td>";
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row2[7]."</font></td>";
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row2[8]."</font></td>";
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>".$row2[9]." ".$row2[10]."</font></td>";

					if (isset($mostrar))
					unset ($mostrar);

					for($j=11;$j<=15;$j++)
					{
						if ($row2[$j]=='1' and !isset($mostrar))
						{
							switch ($j)
							{
								case 11:
								echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>Telefono 1</font></td>";
								break;

								case 12:
								echo "	<td  width='8%' align='center' bgcolor='".$color."' ><font size='2' color='#336699'>Telefono 2</font></td>";
								break;

								case 13:
								echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>Email</font></td>";
								break;

								case 14:
								echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>Movil</font></td>";
								break;

								case 15:
								echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>Direccion</font></td>";
								break;
							}
							$mostrar=1;
						}
					}
					if (!isset($mostrar))
					echo "	<td  width='8%' align='center' bgcolor='".$color."'><font size='2' color='#336699'>-</font></td>";
					echo "</tr>";

					$cantidad++;
				}
			}
			echo "</BR><center><font color='#00008B'>(".$cantidad.")</font></center></BR>";
			echo "</table>";

		}
	}
}

?>

</body>

</html>







