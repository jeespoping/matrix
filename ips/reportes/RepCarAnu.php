<html>
<head>
  <title>REPORTE DE CARGOS ANULADOS POR FECHA INGRESO Y USUARIO</title>
<SCRIPT LANGUAGE="JavaScript1.2">
<!--
function onLoad() {
	loadMenus();
}
//-->
function Seleccionar()
{
	document.forma.submit();
}
</SCRIPT>
</head>
<?php
include_once("conex.php");
 /*************************************************************************************
   *     REPORTE DE CARGOS ANULADOS POR FECHA                                         *
   *               DE CLINICA DEL SUR                                                 *
 *************************************************************************************/
//=================================================================================================================================
//PROGRAMA: RepCarAnu.php
//AUTOR: Gabriel Agudelo.
  $wautor="Gabriel Agudelo.";
//TIPO DE SCRIPT: principal
//RUTA DEL SCRIPT: matrix\pos\procesos\RepCarAnu.php

//HISTORIAL DE REVISIONES DEL SCRIPT:
		//-------------------I------------------------I---------------------------------------------------------------------
		//	  FECHA           I     AUTOR              I   MODIFICACION
		//-------------------I------------------------I------------------------------------------------------------------------
		//  2006-11-01       I Gabriel Agudelo        I creación del script.
		//-------------------I------------------------I-----------------------------------------------------------------------
		//     I   I
		//-------------------I------------------------I-----------------------------------------------------------------------

//FECHA ULTIMA ACTUALIZACION 	: 2006-10-27 2:00 pm
  $wactualiz="(Versión octubre 27 de 2006)";

/*DESCRIPCION:Este reporte presenta las historias no facturadas por fecha de ingreso

TABLAS QUE UTILIZA:
 $wbasedato."_000106: Cargos de facturacion

 INCLUDES:
  conex.php = include para conexión mysql

 VARIABLES:
 $wbasedato= variable que permite el codigo multiempresa, se incializa desde invocación de programa
 $wfecha=date("Y-m-d");
 $wfecini= fecha inicial del reporte
 $wfecfin = fecha final del reporte
 =================================================================================================================================*/

 include_once("root/comun.php");
 session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 {

 if(!isset($wemp_pmla)){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
 }

$key = substr($user,2,strlen($user));

$conex = obtenerConexionBD("matrix");

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

$wbasedato = strtolower($institucion->baseDeDatos);
$wentidad = $institucion->nombre;

echo "<form action='RepCarAnu.php' method=post name='forma'>";

  echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
  $hora = (string)date("H:i:s");
  $wnomprog="RepCarAnu.php";  //nombre del reporte
  $wcf1="003366";  // color del fondo   -- Azul mas claro
  $wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
  $wcf3="006699";  //COLOR DEL FONDO 2  -- Azul OSCURO
  $wcf2="003366";  //COLOR DEL FONDO 3  -- AZUL
  $wcf4="99CCFF";  //COLOR DEL FONDO 4  -- AZUL
  $wcf5="00CCFF";
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro
  $wfecha=date("Y-m-d");
  $control=1; //controla el echo para que salga o no de total general

if (!isset($wfecini) or !isset($wfecfin) or !isset($resultado))
{
	  echo "<center><table border=2>";
	  echo "<tr><td align=center rowspan=2 COLSPAN=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=490 HEIGHT=100></td></tr>";
	  echo "<tr><td align=center bgcolor=".$wcf3."><font size=5 text color=#FFFFFF><b>REPORTE DE CARGOS ANULADOS POR USUARIO</b></font></td></tr>";

	//INGRESO DE VARIABLES PARA EL REPORTE//
	if (!isset ($bandera))
	{
		 $wfecini=$wfecha;
		 $wfecfin=$wfecha;
	}

	echo "<tr>";
	echo "<td bgcolor=".$wcf." align=center COLSPAN=2><b><font text color=".$wclfg.">FECHA INICIAL DE CARGOS (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecini' value=".$wfecini." SIZE=10></td>";
	echo "<td bgcolor=".$wcf." align=center ><b><font text color=".$wclfg.">FECHA FINAL DE CARGOS  (AAAA-MM-DD): </font></b><INPUT TYPE='text' NAME='wfecfin' value=".$wfecfin." SIZE=10></td>";
	echo "</tr>";
	echo "<tr><td align=center bgcolor=".$wcf." colspan=2><b><font text color=".$wclfg."> Usuario : </font></b><select name='wusuario'>";
			$q1= "   SELECT codigo, descripcion "
		            ."     FROM usuarios,".$wbasedato."_000030 "
	        		."    WHERE activo = 'A' "
	        		. " and codigo = cjeusu "
	        		."     order by codigo, descripcion";
				    $res2 = mysql_query($q1,$conex);
				    $num2 = mysql_num_rows($res2);
				    echo "<option>%-Todas los usuarios</option>";
			   		for ($i=1;$i<=$num2;$i++)
			         	{
	  						$row2 = mysql_fetch_array($res2);
	  						echo "<option>".$row2[0]." - ".$row2[1]."</option>";
	       				}
		    echo "</select></td>";



// seleccionar tipo de fuente
	echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
	echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";
    echo "<td align=center bgcolor=".$wcf." COLSPAN='4'><font text color=".$wclfg." ><b>";
	echo "<input type='radio' name='vol' value='SI' onclick='Seleccionar()' checked> DESPLEGAR REPORTE ";
	echo "</font></b></td></tr></table></br>";
	echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
}

//MUESTRA DE DATOS DEL REPORTE
else
  {
	echo "<table border=0 align=center width=100%>";
    echo "<h6><tr><td align=left><B>Facturacion:</B>$wentidad</td>";
    echo "<td align=right><B>Fecha:</B> ".date('Y-m-d')."</td></tr>";
    echo "<tr><td align=left><B>Programa:</B> ".$wnomprog."</td>";
    echo "<td align=right><B>Hora :</B> ".$hora."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
    echo "</table></br>";
	echo "<table border=0 align=center width=100%";
	echo "<tr><td align=center><H1>$wentidad</H1></td></tr>";
	echo "<tr><td align=center><B>REPORTE DE CARGOS ANULADOS POR USUARIO</B></td></tr>";
	echo "<tr><td align=center><B>Fecha inicial:</B> ".$wfecini."</td></tr>";
	echo "<tr><td align=center><B>Fecha final:</B> ".$wfecfin."</td></tr></h6>";
	echo "</table></br>";

    echo "<A href='RepCarAnu.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;bandera='1'><center>VOLVER</center></A><br>";
    echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
  	echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
  	echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
  	echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";

/***********************************Consulto lo pedido ********************/
// SE HACE LA SELECCION DE LOS DATOS QUE NECESITO
if ($wusuario!='%-Todas los usuarios')
		{
			$print1=explode('-', $wusuario);
			$wusucod=trim ($print1[0]);
			$wusudes=trim ($print1[1]);
		}
else
		{
			$wusucod='%';
			$control=2;

		}

	$q =  " SELECT ".$wbasedato."_000106.id, tcarhis, tcaring, tcarfec, tcarser, tcarconcod, tcarconnom, tcarprocod, tcarpronom, tcarcan, tcarvto,tcarfex, tcarfre, tcarusu, descripcion "
        ."  FROM  ".$wbasedato."_000106, usuarios "
        . " WHERE  tcarfec between '".$wfecini."'"
        . " and '".$wfecfin."'"
        . " and tcarest = 'off' "
        . " and tcarusu like '".$wusucod."'"
        . " and tcarusu = codigo "
        . " order by tcarusu,tcarhis,".$wbasedato."_000106.id, tcaring,tcarfec";

		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);

	// inicializo las variables
	$bandera1=0;
	$j=1;
	$i=1;
	$wtotfac = 0;
	$wtotexe = 0;
	$wtotrec = 0;
	$whisfac=0;
	$whisexe=0;
	$whisrec=0;
	$wtcarhis=0;
	$wtcaring=0;
	$pinto = 0;
	$pinto1=0;
	$wtcarusu=0;
	$wtcardes=0;
	echo "<table border=0 >";
//ESTE CICLO MUESTRA LOS DATOS DE LA SELECCION
	while ($i <= $num)
		{
			$row = mysql_fetch_array($err);
			if ($pinto1==0)
				{
					$wtcarusu=$row[13];
					$pinto1=1;
				}
			if ($wtcarusu!=$row[13])
					{
						echo '<tr>';
						echo "<th align=left bgcolor=$wcf2 colspan='10'><font size=3 color='FFFFFF'>TOTAL USUARIO</font></th>";
						echo "<th align=right bgcolor=$wcf2><font size=2 color='FFFFFF'>".number_format($whisfac,0,'.',',')."</font></th>";
						echo "<th align=right bgcolor=$wcf2><font size=2 color='FFFFFF'>".number_format($whisexe,0,'.',',')."</font></th>";
						echo "<th align=right bgcolor=$wcf2><font size=2 color='FFFFFF'>".number_format($whisrec,0,'.',',')."</font></th>";

						echo '</tr>';
						$whisfac=0;
						$whisexe=0;
						$whisrec=0;
						$wtcarusu=$row[13];
						$pinto=0;
					}
			if ($pinto==0 )
			 	{
						echo "<tr><b><td align=CENTER bgcolor=$wcf3 colspan=2><font size=4 text color=#FFFFFF>USUARIO :</font></td>";
						echo "<td align=center bgcolor=$wcf3 colspan=11><font size=4 text color=#FFFFFF>".$row[13]." - ".$row[14]."</font></td></b></tr>";
						echo '<tr>';
	  					echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>NRO REGISTRO</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>HISTORIA</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>INGRESO</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>FECHA CARGO</font></th>";
						echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>CENTRO COSTOS</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>CONCEPTO</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>DESCRIPCION</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>PROCEDIMIENTO</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>DESCRIPCION</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>CANTIDAD</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>VALOR TOTAL</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>RECONOCIDO</font></th>";
	        			echo "<th align=CENTER bgcolor=$wcf2><font size=2 text color=#FFFFFF>EXEDENTE</font></th>";
	        			echo '</tr>';
	        			$pinto=1;
   				}

   				$whisfac=$whisfac + $row[10];
				$whisexe=$whisexe + $row[11];
				$whisrec=$whisrec + $row[12];
             	if (is_int ($j/2))
		 			{
						$coloresumido='FFFFFF';
						$j=$j+1;
					}
				else
					{
						$coloresumido='DDDDDD';
						$j=$j+1;
					}
			   		echo '<tr>';
					echo "<th align=center bgcolor=$coloresumido><font size=2>".$row[0]."</font></th>";
					echo "<th align=center bgcolor=$coloresumido><font size=2>".$row[1]."</font></th>";
					echo "<th align=center bgcolor=$coloresumido><font size=2>".$row[2]."</font></th>";
					echo "<th align=center bgcolor=$coloresumido><font size=2>".$row[3]."</font></th>";
					echo "<th align=center bgcolor=$coloresumido><font size=2>".$row[4]."</font></th>";
					echo "<th align=center bgcolor=$coloresumido><font size=2>".$row[5]."</font></th>";
					echo "<th align=left bgcolor=$coloresumido><font size=2>".$row[6]."</font></th>";
					echo "<th align=center bgcolor=$coloresumido><font size=2>".$row[7]."</font></th>";
					echo "<th align=left bgcolor=$coloresumido><font size=2>".$row[8]."</font></th>";
					echo "<th align=center bgcolor=$coloresumido><font size=2>".$row[9]."</font></th>";
					echo "<th align=right bgcolor=$coloresumido><font size=2>".number_format($row[10],0,'.',',')."</font></th>";
					echo "<th align=right bgcolor=$coloresumido><font size=2>".number_format($row[11],0,'.',',')."</font></th>";
					echo "<th align=right bgcolor=$coloresumido><font size=2>".number_format($row[12],0,'.',',')."</font></th>";
					echo '</tr>';

			$wtotfac = $wtotfac+$row[10];
			$wtotexe = $wtotexe+$row[11];
			$wtotrec = $wtotrec+$row[12];
			$i= $i + 1;
		}
	if ($num==0)
		{
			echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
			echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>Sin ningún documento en el rango de fechas seleccionado</td><tr>";
		}
	else
		{
			echo '<tr>';
			echo "<th align=left bgcolor=$wcf2 colspan='10'><font size=3 color='FFFFFF'>TOTAL USUARIO</font></th>";
			echo "<th align=right bgcolor=$wcf2><font size=2 color='FFFFFF'>".number_format($whisfac,0,'.',',')."</font></th>";
			echo "<th align=right bgcolor=$wcf2><font size=2 color='FFFFFF'>".number_format($whisexe,0,'.',',')."</font></th>";
			echo "<th align=right bgcolor=$wcf2><font size=2 color='FFFFFF'>".number_format($whisrec,0,'.',',')."</font></th>";

			echo '</tr>';
			if ($control==2)
				{
					echo '<tr>';
					echo "<th align=left bgcolor=$wcf2 colspan='10'><font size=4 color='FFFFFF'>TOTAL GENERAL</font></th>";
					echo "<th align=right bgcolor=$wcf2><font size=2 color='FFFFFF'>".number_format($wtotfac,0,'.',',')."</font></th>";
					echo "<th align=right bgcolor=$wcf2><font size=2 color='FFFFFF'>".number_format($wtotexe,0,'.',',')."</font></th>";
					echo "<th align=right bgcolor=$wcf2><font size=2 color='FFFFFF'>".number_format($wtotrec,0,'.',',')."</font></th>";
					echo '</tr>';
				}
		}
    echo "</table>";
	echo "</br><center><A href='RepCarAnu.php?wemp_pmla=".$wemp_pmla."&amp;wfecini=".$wfecini."&amp;wfecfin=".$wfecfin."&amp;bandera='1'>VOLVER</A></center>";
	echo "<div align='center'><input type=button value='Cerrar ventana' onclick='javascript:window.close();'></div>";
}
}
liberarConexionBD($conex);
?>
</body>
</html>
