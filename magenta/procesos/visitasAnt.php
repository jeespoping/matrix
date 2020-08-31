<html>
<head>
<title>AFINIDAD</title>

</head>
<body >

<?php
include_once("conex.php");

/*********************************** 	REPORTE DE VISITAS DE AFINIDAD AAA-BBB-VIP    *****************************************
 * 
 * Este es un reporte que muestra las visitas AAA y BBB y VIP del día, fecha de ingreso, egreso, lugar de ingreso etc 
 * 
 * @name  matrix\magenta\procesos\visitasAnt.php
 * @author Carolina Castaño Portilla.
 * 
 * @created 2006-02-07
 * @version 2007-01-23
 * 
 * @modified  2007-01-23 Se documenta y se muestra el dato del ultimo contacto
 * 
 * @table magenta_000014, select
 * 
 *  @wvar $ano, ano inicial de la busqueda de visitas
 *  @wvar $ano2, ano final de la busqueda de visitas
 *  @wvar $color variar de coloers al presentar los resultados
 *  @wvar $dia, dia inicial de la busqueda de visitas
 *  @wvar $dia2, dia final de la busqueda de visitas
 *  @wvar $doc documento del paciente
 *  @wvar $fecFin fecha final de la busqueda de visitas
 *  @wvar $fecha fecha inicial predefinida como fecha de busqueda
 *  @wvar $fecIni fecha inicial de la busqueda de visitas
 *  @wvar $mes mes inicial de la busqueda de visitas
 *  @wvar $mes2 mes final de la busqueda de visitas
 *  @wvar $tipDoc, tipo de documento del paciente
 *  @wvar $trozos para guardar los explodes
 **********************************************************************************************************************************
 Actualizacion
 Fecha: 2016-05-05  Arleyda Insignares C. Se coloca encabezado, titulos y tablas con ultimo formato
 Fecha: 11-05-2012  Descripcion: Se cambiaron las rutas de los include, ya que presentaron error por el cambio de servidor - Viviana Rodas

***********************************************************************************************************************************/

$wautor="Carolina Castano P.";
$wversion='2007-01-23';
$wactualiz='2016-05-05';

/**********************************************    PROGRAMA    *****************************************************************/
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

	/**
	 * conexion con matrix
	 */
	

	

	$bd='facturacion';
	/**
	 * conexion con unix
	 */
	include_once("magenta/socket2.php");
	//entro a programa cuando la conexión se realizó
	if($conex_o != false)
	{

		//////////////forma para ingresar la fecha de búsqueda////////////////////


		if (!isset ($ano)) // si no se han ingresado las fechas de busqueda
		{
			$pass= intval(date('m'))-1;

			if ($pass<10)
			$pass='0'.$pass;

			$fecha=date('Y').'-'.$pass.'-'.date('d');

			if ($pass==0)
			{
				$fecha= intval (date('Y'))-1;
				$fecha=$fecha.'-12-'.date('d');

			}

			ECHO "<table border=0 align=center size=100%>";
			echo "<div align='center'><div align='center' class='fila1' style='width:520px;'><font size='4'><b>LISTA DE VISITAS</b></font></div></div></BR>";
			ECHO "</table></br></br></br>";

			echo "<table align='center'>";
			echo "<tr><td align=center class='fila1'><font size=3>INGRESE POR FAVOR EL RANGO DE FECHAS EN EL CUAL DESEA REALIZAR LA BÚSQUEDA, DENTRO DEL SIGUIENTE RANGO:</font></td>";
			echo "</table></br></br>";

			echo "<fieldset align=center></br>";

			echo "<form NAME='ingreso' ACTION='' METHOD='POST'>";
			echo "<table align='center'>";
			echo "<tr>";

			echo "<td align=center class='encabezadotabla'  width='150'><font size=3  face='arial'>Fecha inicial:&nbsp</td>";
			echo "<td align=center class='fila2' ><font size=2  face='arial'>Año:</b>&nbsp</td>";
			echo "<td align=center class='fila2' ><font size='2'  align=center face='arial'><input type='text' name='ano'  value='".SUBSTR ($fecha,0,4)."' size='2'></td>";
			echo "<td align=center class='fila2'><font size=2  face='arial'>Mes:</b>&nbsp</td>";
			echo "<td align=center class='fila2'><font size='2'  align=center face='arial'><input type='text' name='mes'  value='".SUBSTR ($fecha,5,2)."' size='1'></td>";
			echo "<td align=center class='fila2'><font size=2  face='arial'>Día:</b>&nbsp</td>";
			echo "<td align=center class='fila2' ><font size='2'  align=center face='arial'><input type='text' name='dia'  value='".date ('d')."' size='1'></td>";
			echo "<td align=center >&nbsp;&nbsp;&nbsp;&nbsp;</td>";
			echo "<td align=center class='encabezadotabla'  width='150'><font size=3  face='arial'>Fecha final:&nbsp</td>";
			echo "<td align=center class='fila2'><font size=2  face='arial'>Año:</b>&nbsp</td>";
			echo "<td align=center class='fila2' ><font size='2'  align=center face='arial'><input type='text' name='ano2'  value='".date ('Y')."' size='2'></td>";
			echo "<td align=center class='fila2'><font size=2  face='arial'>Mes:</b>&nbsp</td>";
			echo "<td align=center class='fila2' ><font size='2'  align=center face='arial'><input type='text' name='mes2'  value='".date ('m')."' size='1'></td>";
			echo "<td align=center class='fila2'><font size=2  face='arial'>Día:</b>&nbsp</td>";
			echo "<td align=center class='fila2' ><font size='2'  align=center face='arial'><input type='text' name='dia2'  value='".date ('d')."' size='1'></td>";

			echo "</td>";
			echo "</tr></TABLE></br>";
			echo "<TABLE align=center><tr>";
			echo "<tr><td align=center colspan=14><font size='2'  align=center face='arial'><input type='submit' name='aceptar' value='BUSCAR' ></td></tr>";
			echo "</TABLE>";
			echo "</td>";
			echo "</tr>";
			echo "</form>";
			echo "</fieldset>";



		}else // ya se han ingresado las fechas de busqueda
		{


			/////////////////////////////////Validación de campos de fecha////////////////////

			if ((strlen ($ano) != 4) or (strlen ($mes)!=2) or (strlen ($dia)!=2) or (strlen ($ano2)!= 4) or (strlen ($mes2) != 2) or (strlen ($dia2)!= 2))
			{
				echo '<script language="Javascript">';
				echo 'window.location.href=window.location.href;';
				echo 'alert ("El formato para ingresar las fechas es como el siguiente ejemplo: Año:2006  Mes:02  Día:25 ")';
				echo '</script>';
			}

			//////////////////////////////////////////////////////////selección de clientes AAA////////////////////////////
			$i=0;
			$fecIni=$ano."-".$mes."-".$dia;
			$fecFin=$ano2."-".$mes2."-".$dia2;

			ECHO "<table border=0 align=center size=100%>";
			echo "<div align='center'><div align='center' class='fila1' style='width:520px;'><font size='4'><b>LISTA DE VISITAS: desde $fecIni hasta $fecFin</b></font></div></div></BR>";
			ECHO "</table></br>";

			echo "<center><img SRC='/MATRIX/images/medical/Magenta/AAA.gif' ><center>";

			echo '<table align=center>';
			echo "<form NAME='VOLVER' ACTION='visitasAnt.php' METHOD='POST'>";
			echo "<tr> <td align=center>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
			echo "<td align=center>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
			echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
			echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
			echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
			echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
			echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp";
			echo "&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <td> ";

			echo "<td align=right colspan=2><font size='2' align=center face='arial'><input type='submit' name='aceptar' value='VOLVER' ></td></tr>";
			echo "</form></table>";

			$query="select * from magenta_000014 where reping  between '$fecIni' and '$fecFin' and repusu='AFIN-AAA-1' order by repser, reping, rephor";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);

			if ($num>=1)
			{
				ECHO "<table border=0 align=center size=100%>";
				ECHO "<tr><td align=center ><font size=3 color='blue'>Número de Visitas AAA: $num</font></td></tr>";
				ECHO "</table>";

				echo '</br><table align=center>';
				ECHO "<Tr class='encabezadotabla'>";
				echo "<td align=center><font size=2>Documento</font></td>";
				echo "<td align=center><font size=2>Nº Historia</font></td>";
				echo "<td align=center width=15%><font size=2>Nombre</font></td>";
				echo "<td align=center><font size=2>Fecha de ingreso</font></td>";
				echo "<td align=center width=10%><font size=2>Unidad de ingreso</font></td>";
				echo "<td align=center><font size=2>Hora de Ingreso</font></td>";
				echo "<td align=center><font size=2>Amb</font></td>";
				echo "<td align=center><font size=2>Ultima visita</font></td>";
				echo "<td align=center><font size=2>Actualizado</font></td>";
				echo "<td align=center width=15%><font size=2>Ac. Por:</font></td>";
				echo "<td align=center><font size=2>Datos ok.</font></td>";
				ECHO "</Tr >";
				ECHO "<Tr >";

				While ( $resulta = mysql_fetch_array($err) )
				{
					if (is_int ($i/2))
					$color='fila1';
					else
					$color='fila2';

					$trozos = explode(" ", $resulta[3]);
					$doc= $trozos[0]; // trozo1
					$tipDoc=substr($resulta[3],-2);

					switch (trim($tipDoc))
					{
						case "CC": $tipDoc="CC-CEDULA DE CIUDADANIA";
						break;
						case "TI": $tipDoc="TI-Tarjeta de Identidad";
						break;
						case "NU": $tipDoc="NU-Numero Unico de Identificación";
						break;
						case "CE": $tipDoc="CE-Cedula de Extrangeria";
						break;
						case "PA": $tipDoc="PA-Pasaporte";
						break;
						case "RC": $tipDoc="RC-Registro Civil";
						break;
						case "AS": $tipDoc="AS-Adulto Sin Identificación";
						break;
						case "MS": $tipDoc="MS-Menor Sin Identificación";
						break;
					}

					ECHO "<Tr class='$color'>";
					echo "<td  ><a href='/Matrix/Magenta/procesos/Magenta.php?ced=1&doc=".$doc."&tipDoc=".$tipDoc."' target='_blank'><font size=2>".$resulta[3]."</font></a></td>";
					echo "<td  align=center><font size=2>$resulta[4]</font></td>";
					echo "<td  align=center><font size=2>$resulta[5]</font></td>";
					echo "<td  align=center><font size=2>$resulta[6]</font></td>";
					echo "<td  align=center><font size=2>$resulta[7]</font></td>";
					echo "<td  align=center><font size=2>$resulta[8]</font></td>";


					switch ($resulta[12])
					{
						case 0:
						//echo "<td bgcolor='$color' align=center >".$array[$i][6].'</td>';
						echo "<td ><input type='checkbox' name='Ambulatorio'  checked ></td>";
						break;
						case 1:
						//$query="select A.traser, B.sernom    ";
						//$query= $query."from inmtra A , inser B ";
						//$query= $query."where A.trahis='".$array[$i][1]."' and A.tranum='".$array[$i][8]."' and A.traing='".$array[$i][3]."'  and A.traegr is null ";
						//$query= $query."and B.sercod=A.traser ";

						//$err_o = odbc_exec($conex_o,$query);
						//$resulta=odbc_result($err_o,2);
						//echo "<td bgcolor='$color' align=center>".$resulta."</td>";
						echo "<td ><input type='checkbox' name='Ambulatorio'></td>";
						break;
					}


					echo "<td  align=center><font size=2>$resulta[17]</font></td>";

					switch ($resulta[10])
					{
						case 'off':
						echo "<td ><center><input type='checkbox' name='Actualizado'>&nbsp;<font size=2>".$resulta[14]."</font></center></td>";
						break;
						case 'on':
						echo "<td ><input type='checkbox' name='Actualizado' checked>&nbsp;<font size=2>".$resulta[14]."</font></td>";
						break;
					}

					echo "<td align=center><font size=2>".$resulta[13].'</font></td>';

					switch ($resulta[11])
					{
						case 'off':
						echo "<td ><input type='checkbox' name='Datos' ></td>";
						break;
						case 'on':
						echo "<td ><input type='checkbox' name='Datos' checked ></td>";
						break;
					}

					$i++;

				}

				echo "</table>";
			}ELSE
			{
				//echo "<table>";
				echo "<CENTER><fieldset style='border:solid;border-color:#ADD8E6; width=700' >";
				echo "<tr><td colspan='2'><font size=3  color=''blue face='arial'><b>NINGUNO HA INGRESO DURANTES LAS FECHAS INGRESADAS</td><tr>";
				echo "</fieldset>";
			}

			//////////////////////////////////////////////////////////Fin selección de clientes aaa////////////////////////////
			/////////////////////////////////////////////////////////selección de clientes bbb////////////////////////////
			echo "</br></br></br></br><center><img SRC='/MATRIX/images/medical/Magenta/BBB.gif' ><center></br></br>";

			$i=0;
			$fecIni=$ano."-".$mes."-".$dia;
			$fecFin=$ano2."-".$mes2."-".$dia2;

			$query="select * from magenta_000014 where reping  between '$fecIni' and '$fecFin' and repusu='AFIN-BBB-2' order by repser, reping, rephor";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);

			if ($num>=1)
			{

				ECHO "<table border=0 align=center size=100%>";
				ECHO "<tr><td align=center ><font size=3 color='blue'>Número de Visitas BBB: $num</font></td></tr>";
				ECHO "</table>";

				echo '</br></br></br><table align=center>';
				ECHO "<Tr class='encabezadotabla'>";
				echo "<td align=center><font size=2>Documento</font></td>";
				echo "<td align=center><font size=2>Nº Historia</font></td>";
				echo "<td align=center width=15%><font size=2>Nombre</font></td>";
				echo "<td align=center><font size=2>Fecha de ingreso</font></td>";
				echo "<td align=center width=10%><font size=2>Unidad de ingreso</font></td>";
				echo "<td align=center><font size=2>Hora de Ingreso</font></td>";
				echo "<td align=center><font size=2>Amb</font></td>";
				echo "<td align=center><font size=2>Ultima visita</font></td>";
				echo "<td align=center><font size=2>Actualizado</font></td>";
				echo "<td align=center width=15%><font size=2>Ac. Por:</font></td>";
				echo "<td align=center><font size=2>Datos ok.</font></td>";
				ECHO "</Tr >";
				ECHO "<Tr >";

				While ( $resulta = mysql_fetch_array($err) )
				{
					if (is_int ($i/2))
					$color='fila1';
					else
					$color='fila2';

					$trozos = explode(" ", $resulta[3]);
					$doc= $trozos[0]; // trozo1
					$tipDoc=substr($resulta[3],-2);

					switch (trim($tipDoc))
					{
						case "CC": $tipDoc="CC-CEDULA DE CIUDADANIA";
						break;
						case "TI": $tipDoc="TI-Tarjeta de Identidad";
						break;
						case "NU": $tipDoc="NU-Numero Unico de Identificación";
						break;
						case "CE": $tipDoc="CE-Cedula de Extrangeria";
						break;
						case "PA": $tipDoc="PA-Pasaporte";
						break;
						case "RC": $tipDoc="RC-Registro Civil";
						break;
						case "AS": $tipDoc="AS-Adulto Sin Identificación";
						break;
						case "MS": $tipDoc="MS-Menor Sin Identificación";
						break;
					}

					ECHO "<Tr class='$color'>";
					echo "<td ><a href='/Matrix/Magenta/procesos/Magenta.php?ced=1&doc=".$doc."&tipDoc=".$tipDoc."' target='_blank'><font size=2>".$resulta[3]."</font></a></td>";
					echo "<td align=center><font size=2>$resulta[4]</font></td>";
					echo "<td align=center><font size=2>$resulta[5]</font></td>";
					echo "<td align=center><font size=2>$resulta[6]</font></td>";
					echo "<td align=center><font size=2>$resulta[7]</font></td>";
					echo "<td align=center><font size=2>$resulta[8]</font></td>";


					switch ($resulta[12])
					{
						case 0:
						//echo "<td bgcolor='$color' align=center >".$array[$i][6].'</td>';
						echo "<td ><input type='checkbox' name='Ambulatorio'  checked ></td>";
						break;
						case 1:
						//$query="select A.traser, B.sernom    ";
						//$query= $query."from inmtra A , inser B ";
						//$query= $query."where A.trahis='".$array[$i][1]."' and A.tranum='".$array[$i][8]."' and A.traing='".$array[$i][3]."'  and A.traegr is null ";
						//$query= $query."and B.sercod=A.traser ";

						//$err_o = odbc_exec($conex_o,$query);
						//$resulta=odbc_result($err_o,2);
						//echo "<td bgcolor='$color' align=center>".$resulta."</td>";
						echo "<td ><input type='checkbox' name='Ambulatorio'></td>";
						break;
					}

					echo "<td  align=center><font size=2>$resulta[17]</font></td>";

					switch ($resulta[10])
					{
						case 'off':
						echo "<td ><center><input type='checkbox' name='Actualizado'>&nbsp;<font size=2>".$resulta[14]."</font></center></td>";
						break;
						case 'on':
						echo "<td ><input type='checkbox' name='Actualizado' checked>&nbsp;<font size=2>".$resulta[14]."</font></td>";
						break;
					}

					echo "<td align=center><font size=2>".$resulta[13].'</font></td>';

					switch ($resulta[11])
					{
						case 'off':
						echo "<td ><input type='checkbox' name='Datos' ></td>";
						break;
						case 'on':
						echo "<td ><input type='checkbox' name='Datos' checked ></td>";
						break;
					}

					$i++;

				}

				echo "</table>";
			}ELSE
			{
				//echo "<table>";
				echo "<CENTER><fieldset style='border:solid;border-color:#ADD8E6; width=700' >";
				echo "<tr><td colspan='2'><font size=3  face='arial'><b>NINGUNO HA INGRESO DURANTE LAS FECHAS INGRESADAS</td><tr>";
				echo "</fieldset>";
			}

			//////////////////////////////////////////////////////////Fin selección de clientes BBB////////////////////////////
			/////////////////////////////////////////////////////////selección de clientes VIP////////////////////////////
			echo "</br></br></br></br><center><img SRC='/MATRIX/images/medical/Magenta/VIP2.gif' ><center></br>";

			$i=0;
			$fecIni=$ano."-".$mes."-".$dia;
			$fecFin=$ano2."-".$mes2."-".$dia2;




			$query="select * from magenta_000014 where repusu='VIP' and reping  between '$fecIni' and '$fecFin'  order by repser, reping, rephor";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);



			if ($num>=1)
			{


				ECHO "<table border=0 align=center size=100%>";
				ECHO "<tr><td align=center ><font size=3 color='#FF00FF'>Número de Visitas VIP: $num</font></td></tr>";
				ECHO "</table>";

				echo '</br><table align=center>';
				ECHO "<Tr class='encabezadotabla' >";
				echo "<td align=center><font size=2>Documento</font></td>";
				echo "<td align=center><font size=2>Nº Historia</font></td>";
				echo "<td align=center width=15%><font size=2>Nombre</font></td>";
				echo "<td align=center><font size=2>Fecha de ingreso</font></td>";
				echo "<td align=center width=10%><font size=2>Unidad de ingreso</font></td>";
				echo "<td align=center><font size=2>Hora de Ingreso</font></td>";
				echo "<td align=center><font size=2>Amb</font></td>";
				echo "<td align=center><font size=2>Actualizado</font></td>";
				echo "<td align=center width=15%><font size=2>Ac. Por:</font></td>";
				echo "<td align=center><font size=2>Datos ok.</font></td>";
				ECHO "</Tr >";
				ECHO "<Tr >";

				While ( $resulta = mysql_fetch_array($err) )
				{
					if (is_int ($i/2))
					$color='fila1';
					else
					$color='fila2';

					$trozos = explode(" ", $resulta[3]);
					$doc= $trozos[0]; // trozo1
					$tipDoc=substr($resulta[3],-2);

					switch (trim($tipDoc))
					{
						case "CC": $tipDoc="CC-CEDULA DE CIUDADANIA";
						break;
						case "TI": $tipDoc="TI-Tarjeta de Identidad";
						break;
						case "NU": $tipDoc="NU-Numero Unico de Identificación";
						break;
						case "CE": $tipDoc="CE-Cedula de Extrangeria";
						break;
						case "PA": $tipDoc="PA-Pasaporte";
						break;
						case "RC": $tipDoc="RC-Registro Civil";
						break;
						case "AS": $tipDoc="AS-Adulto Sin Identificación";
						break;
						case "MS": $tipDoc="MS-Menor Sin Identificación";
						break;
					}

					ECHO "<Tr class='$color'>";
					echo "<td ><a href='/Matrix/Magenta/procesos/Magenta.php?ced=1&doc=".$doc."&tipDoc=".$tipDoc."' target='_blank'><font size=2>".$resulta[3]."</font></a></td>";
					echo "<td align=center><font size=2>$resulta[4]</font></td>";
					echo "<td align=center><font size=2>$resulta[5]</font></td>";
					echo "<td align=center><font size=2>$resulta[6]</font></td>";
					echo "<td align=center><font size=2>$resulta[7]</font></td>";
					echo "<td align=center><font size=2>$resulta[8]</font></td>";

					switch ($resulta[12])
					{
						case 0:
						//echo "<td bgcolor='$color' align=center >".$array[$i][6].'</td>';
						echo "<td ><input type='checkbox' name='Ambulatorio'  checked ></td>";
						break;
						case 1:
						//$query="select A.traser, B.sernom    ";
						//$query= $query."from inmtra A , inser B ";
						//$query= $query."where A.trahis='".$array[$i][1]."' and A.tranum='".$array[$i][8]."' and A.traing='".$array[$i][3]."'  and A.traegr is null ";
						//$query= $query."and B.sercod=A.traser ";

						//$err_o = odbc_exec($conex_o,$query);
						//$resulta=odbc_result($err_o,2);
						//echo "<td bgcolor='$color' align=center>".$resulta."</td>";
						echo "<td ><input type='checkbox' name='Ambulatorio'></td>";
						break;
					}

					switch ($resulta[10])
					{
						case 'off':
						echo "<td ><center><input type='checkbox' name='Actualizado'>&nbsp;<font size=2>".$resulta[14]."</font></center></td>";
						break;
						case 'on':
						echo "<td ><input type='checkbox' name='Actualizado' checked>&nbsp;<font size=2>".$resulta[14]."</font></td>";
						break;
					}

					echo "<td align=center><font size=2>".$resulta[13].'</font></td>';

					switch ($resulta[11])
					{
						case 'off':
						echo "<td ><input type='checkbox' name='Datos' ></td>";
						break;
						case 'on':
						echo "<td ><input type='checkbox' name='Datos' checked ></td>";
						break;
					}

					$i++;

				}

				echo "</table>";
			}ELSE
			{
				//echo "<table>";
				echo "<CENTER><fieldset style='border:solid;border-color:#ADD8E6; width=700' >";
				echo "<tr><td colspan='2'><font size=3  face='arial'><b>NINGUNO INGRESO DURANTE LAS FECHAS INGRESADAS</td><tr>";
				echo "</fieldset>";
			}

			//////////////////////////////////////////////////////////Fin selección de clientes VIP////////////////////////////
			
			//////////////////////////////////////////////////////////selección de personalidades AAA////////////////////////////
			$i=0;

			echo "</br></br></br></br><center><B>PERSONALIDADES <img SRC='/MATRIX/images/medical/Magenta/AAA.gif' ></B><center></br></br>";

			$query="select * from magenta_000014 where reping  between '$fecIni' and '$fecFin' and repusu='PER-AAA-1' order by repser, reping, rephor";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);

			if ($num>=1)
			{
				ECHO "<table border=0 align=center size=100%>";
				ECHO "<tr ><td align=center ><font size=3 color='blue'>Número de Visitas AAA: $num</font></td></tr>";
				ECHO "</table>";

				echo '</br><table align=center>';
				ECHO "<Tr >";
				echo "<td align=center><font size=2>Documento</font></td>";
				echo "<td align=center><font size=2>Nº Historia</font></td>";
				echo "<td align=center width=15%><font size=2>Nombre</font></td>";
				echo "<td align=center><font size=2>Fecha de ingreso</font></td>";
				echo "<td align=center width=10%><font size=2>Unidad de ingreso</font></td>";
				echo "<td align=center><font size=2>Hora de Ingreso</font></td>";
				echo "<td align=center><font size=2>Amb</font></td>";
				echo "<td align=center><font size=2>Ultima visita</font></td>";
				echo "<td align=center><font size=2>Actualizado</font></td>";
				echo "<td align=center width=15%><font size=2>Ac. Por:</font></td>";
				echo "<td align=center><font size=2>Datos ok.</font></td>";
				ECHO "</Tr >";
				ECHO "<Tr >";

				While ( $resulta = mysql_fetch_array($err) )
				{
					if (is_int ($i/2))
					$color='fila1';
					else
					$color='fila2';

					$trozos = explode(" ", $resulta[3]);
					$doc= $trozos[0]; // trozo1
					$tipDoc=substr($resulta[3],-2);

					switch (trim($tipDoc))
					{
						case "CC": $tipDoc="CC-CEDULA DE CIUDADANIA";
						break;
						case "TI": $tipDoc="TI-Tarjeta de Identidad";
						break;
						case "NU": $tipDoc="NU-Numero Unico de Identificación";
						break;
						case "CE": $tipDoc="CE-Cedula de Extrangeria";
						break;
						case "PA": $tipDoc="PA-Pasaporte";
						break;
						case "RC": $tipDoc="RC-Registro Civil";
						break;
						case "AS": $tipDoc="AS-Adulto Sin Identificación";
						break;
						case "MS": $tipDoc="MS-Menor Sin Identificación";
						break;
					}

					ECHO "<Tr class='$color'>";
					echo "<td ><a href='/Matrix/Magenta/procesos/Magenta.php?ced=1&doc=".$doc."&tipDoc=".$tipDoc."' target='_blank'><font size=2>".$resulta[3]."</font></a></td>";
					echo "<td align=center><font size=2>$resulta[4]</font></td>";
					echo "<td align=center><font size=2>$resulta[5]</font></td>";
					echo "<td align=center><font size=2>$resulta[6]</font></td>";
					echo "<td align=center><font size=2>$resulta[7]</font></td>";
					echo "<td align=center><font size=2>$resulta[8]</font></td>";


					switch ($resulta[12])
					{
						case 0:
						//echo "<td bgcolor='$color' align=center >".$array[$i][6].'</td>';
						echo "<td ><input type='checkbox' name='Ambulatorio'  checked ></td>";
						break;
						case 1:
						//$query="select A.traser, B.sernom    ";
						//$query= $query."from inmtra A , inser B ";
						//$query= $query."where A.trahis='".$array[$i][1]."' and A.tranum='".$array[$i][8]."' and A.traing='".$array[$i][3]."'  and A.traegr is null ";
						//$query= $query."and B.sercod=A.traser ";

						//$err_o = odbc_exec($conex_o,$query);
						//$resulta=odbc_result($err_o,2);
						//echo "<td bgcolor='$color' align=center>".$resulta."</td>";
						echo "<td ><input type='checkbox' name='Ambulatorio'></td>";
						break;
					}


					echo "<td align=center><font size=2>$resulta[17]</font></td>";

					switch ($resulta[10])
					{
						case 'off':
						echo "<td ><center><input type='checkbox' name='Actualizado'>&nbsp;<font size=2>".$resulta[14]."</font></center></td>";
						break;
						case 'on':
						echo "<td ><input type='checkbox' name='Actualizado' checked>&nbsp;<font size=2>".$resulta[14]."</font></td>";
						break;
					}

					echo "<td align=center><font size=2>".$resulta[13].'</font></td>';

					switch ($resulta[11])
					{
						case 'off':
						echo "<td ><input type='checkbox' name='Datos' ></td>";
						break;
						case 'on':
						echo "<td ><input type='checkbox' name='Datos' checked ></td>";
						break;
					}

					$i++;

				}

				echo "</table>";
			}ELSE
			{
				//echo "<table>";
				echo "<CENTER><fieldset style='border:solid;border-color:#ADD8E6; width=700' >";
				echo "<tr><td colspan='2'><font size=3  face='arial'><b>NINGUNO HA INGRESO DURANTES LAS FECHAS INGRESADAS</td><tr>";
				echo "</fieldset>";
			}

			//////////////////////////////////////////////////////////Fin selección de personalidades aaa////////////////////////////
			/////////////////////////////////////////////////////////selección de personalidades bbb////////////////////////////
			echo "</br></br></br></br><center><B>PERSONALIDADES </B><img SRC='/MATRIX/images/medical/Magenta/BBB.gif' ><center></br></br>";

			$i=0;
			$fecIni=$ano."-".$mes."-".$dia;
			$fecFin=$ano2."-".$mes2."-".$dia2;

			$query="select * from magenta_000014 where reping  between '$fecIni' and '$fecFin' and repusu='PER-BBB-2' order by repser, reping, rephor";
			$err=mysql_query($query,$conex);
			$num=mysql_num_rows($err);

			if ($num>=1)
			{

				ECHO "<table border=0 align=center size=100%>";
				ECHO "<tr><td align=center ><font size=3 color='#FF00FF'>Número de Visitas BBB: $num</font></td></tr>";
				ECHO "</table>";

				echo '</br></br></br><table align=center>';
				ECHO "<Tr class='encabezadotabla'>";
				echo "<td align=center><font size=2>Documento</font></td>";
				echo "<td align=center><font size=2>Nº Historia</font></td>";
				echo "<td align=center width=15%><font size=2>Nombre</font></td>";
				echo "<td align=center><font size=2>Fecha de ingreso</font></td>";
				echo "<td align=center width=10%><font size=2>Unidad de ingreso</font></td>";
				echo "<td align=center><font size=2>Hora de Ingreso</font></td>";
				echo "<td align=center><font size=2>Amb</font></td>";
				echo "<td align=center><font size=2>Ultima visita</font></td>";
				echo "<td align=center><font size=2>Actualizado</font></td>";
				echo "<td align=center width=15%><font size=2>Ac. Por:</font></td>";
				echo "<td align=center><font size=2>Datos ok.</font></td>";
				ECHO "</Tr >";
				ECHO "<Tr >";

				While ( $resulta = mysql_fetch_array($err) )
				{
					if (is_int ($i/2))
					$color='fila1';
					else
					$color='fila2';

					$trozos = explode(" ", $resulta[3]);
					$doc= $trozos[0]; // trozo1
					$tipDoc=substr($resulta[3],-2);


					switch (trim($tipDoc))
					{
						case "CC": $tipDoc="CC-CEDULA DE CIUDADANIA";
						break;
						case "TI": $tipDoc="TI-Tarjeta de Identidad";
						break;
						case "NU": $tipDoc="NU-Numero Unico de Identificación";
						break;
						case "CE": $tipDoc="CE-Cedula de Extrangeria";
						break;
						case "PA": $tipDoc="PA-Pasaporte";
						break;
						case "RC": $tipDoc="RC-Registro Civil";
						break;
						case "AS": $tipDoc="AS-Adulto Sin Identificación";
						break;
						case "MS": $tipDoc="MS-Menor Sin Identificación";
						break;
					}

					ECHO "<Tr class='$color'>";
					echo "<td ><a href='/Matrix/Magenta/procesos/Magenta.php?ced=1&doc=".$doc."&tipDoc=".$tipDoc."' target='_blank'><font size=2>".$resulta[3]."</font></a></td>";
					echo "<td align=center><font size=2>$resulta[4]</font></td>";
					echo "<td align=center><font size=2>$resulta[5]</font></td>";
					echo "<td align=center><font size=2>$resulta[6]</font></td>";
					echo "<td align=center><font size=2>$resulta[7]</font></td>";
					echo "<td align=center><font size=2>$resulta[8]</font></td>";


					switch ($resulta[12])
					{
						case 0:
						//echo "<td bgcolor='$color' align=center >".$array[$i][6].'</td>';
						echo "<td ><input type='checkbox' name='Ambulatorio'  checked ></td>";
						break;
						case 1:
						//$query="select A.traser, B.sernom    ";
						//$query= $query."from inmtra A , inser B ";
						//$query= $query."where A.trahis='".$array[$i][1]."' and A.tranum='".$array[$i][8]."' and A.traing='".$array[$i][3]."'  and A.traegr is null ";
						//$query= $query."and B.sercod=A.traser ";

						//$err_o = odbc_exec($conex_o,$query);
						//$resulta=odbc_result($err_o,2);
						//echo "<td bgcolor='$color' align=center>".$resulta."</td>";
						echo "<td ><input type='checkbox' name='Ambulatorio'></td>";
						break;
					}

					echo "<td align=center><font size=2>$resulta[17]</font></td>";

					switch ($resulta[10])
					{
						case 'off':
						echo "<td ><center><input type='checkbox' name='Actualizado'>&nbsp;<font size=2>".$resulta[14]."</font></center></td>";
						break;
						case 'on':
						echo "<td ><input type='checkbox' name='Actualizado' checked>&nbsp;<font size=2>".$resulta[14]."</font></td>";
						break;
					}

					echo "<td align=center><font size=2>".$resulta[13].'</font></td>';

					switch ($resulta[11])
					{
						case 'off':
						echo "<td ><input type='checkbox' name='Datos' ></td>";
						break;
						case 'on':
						echo "<td ><input type='checkbox' name='Datos' checked ></td>";
						break;
					}

					$i++;

				}

				echo "</table>";
			}ELSE
			{
				//echo "<table>";
				echo "<CENTER><fieldset style='border:solid;border-color:#ADD8E6; width=700' >";
				echo "<tr><td colspan='2'><font size=3  face='arial'><b>NINGUNO HA INGRESO DURANTE LAS FECHAS INGRESADAS</td><tr>";
				echo "</fieldset>";
			}

			//////////////////////////////////////////////////////////Fin selección de personalidades BBB////////////////////////////
		}
	}else
	{
		echo "ERROR : "."$errstr ($errno)<br>\n";
	}

	//Cerrar conexiones
	//include_once("free.php");
}
?>