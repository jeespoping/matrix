<html>
<head>
<title>REPORTE DE RECIBOS Y NOTAS DE CARTERA</title>

<!-- Funciones Javascript -->
<SCRIPT LANGUAGE="javascript">
	function onLoad(){	loadMenus();  }
	function seleccionar(){
		document.forma.submit();		
	}
	function calendario(id,vrl){
		if (vrl == "1"){
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecini',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		}
		if (vrl == "2"){
			Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfecfin',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
		}
	}	
</SCRIPT>

</head>

<!-- Inclusion del calendario -->
<link rel="stylesheet" href="../../zpcal/themes/winter.css" />
<script type="text/javascript" src="../../zpcal/src/utils.js"></script>
<script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
<script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
<script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
<!-- Fin inclusión calendario -->

<?php
include_once("conex.php");
/*
 * REPORTE DE RECIBOS Y NOTAS DE CARTERA 
 */
//=================================================================================================================================
//PROGRAMA: RepCartera.php :: Farmastore
//AUTOR: Mauricio Sánchez Castaño.
//TIPO DE SCRIPT: principal
//RUTA DEL SCRIPT: matrix\FARMASTORE\Reportes\RepNotas.php

//HISTORIAL DE REVISIONES DEL SCRIPT:
//+-------------------+------------------------+-----------------------------------------+
//|	   FECHA          |     AUTOR              |   MODIFICACION							 |
//+-------------------+------------------------+-----------------------------------------+
//|  2008-06-05       | Mauricio Sanchez       | creación del script.					 |
//+-------------------+------------------------+-----------------------------------------+
	
//FECHA ULTIMA ACTUALIZACION 	: 2008-06-05

/*DESCRIPCION:Este reporte presenta la lista de recibos y notas credito y debito por fuente 

TABLAS QUE UTILIZA:
 
INCLUDES: 
  conex.php = include para conexión mysql            

VARIABLES:
 $wbasedato= variable que permite el codigo multiempresa, se incializa desde invocación de programa
 $wfecha=date("Y-m-d");    
 $wfecini= fecha inicial del reporte
 $wfecfin = fecha final del reporte
 $wccocod = centro de costos
 $resultado = 
=================================================================================================================================*/

/**
 * Definicion de funciones
 */

/**
 * Consulta la información de la institución dado el codigo de la empresa en la Promotora
 *
 * @param unknown_type $wemp_pmla
 * @return unknown
 */
function consultar_informacion_institucion($wemp_pmla){
	global $wbasedato;
	global $conex;
	
	$q = " SELECT 
				detapl, detval, empdes
			FROM 
				root_000050, root_000051
			WHERE 
				empcod = '".$wemp_pmla."'
				AND empest = 'on'
				AND empcod = detemp;";
	
	$res = mysql_query($q, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);

			if ($row[0] == "cenmez")
			$wcenmez = $row[1];

			if ($row[0] == "afinidad")
			$wafinidad = $row[1];

			if ($row[0] == "movhos")
			$wbasedato = $row[1];

			if ($row[0] == "tabcco")
			$wtabcco = $row[1];

			$winstitucion=$row[2];
		}
	}
	
	$info[0] = $wcenmez;
	$info[1] = $wafinidad;
	$info[2] = $wbasedato;
	$info[3] = $wtabcco;
	$info[4] = $winstitucion;
	
	return $info;
}

function mensajeEmergente($mensaje)
{
	echo '<script language="javascript">';
	echo 'alert ("'.$mensaje.'")';
	echo '</script>';
}

//Inicio
//TODO: ESTO ES PROVISIONAL
$user = "07054";
$wemp_pmla = '01';

//Conexion base de datos
	

	


if($user == ''){
	echo "error";
}else{
	//Consulta la informacion de la institucion 
	$infoInstitucion = consultar_informacion_institucion($wemp_pmla);
	
	$wbasedato='farstore';

	

  	

  	
  	$wentidad=$infoInstitucion[4];
  	$wfecha=date("Y-m-d");
  	$hora = (string)date("H:i:s");
  	$wnomprog="RepNotas.php";  //nombre del reporte
  	
  	$wcf1="#41627e";  //Fondo encabezado del Centro de costos
  	$wcf="#c2dfff";   //Fondo procedimientos
  	$wcf2="003366";  //Fondo titulo pantalla de ingreso de parametros
  	$wcf3="#659ec7";  //Fondo encabezado del detalle
  	$wclfg="003366"; //Color letra parametros
  	
  	echo "<form action='RepNotas.php' method=post name='forma'>";
  	echo "<input type='HIDDEN' NAME='wbasedato' value='".$wbasedato."'>";

  if (!isset($wfecini) or !isset($wfecfin) or !isset($wfuecod) or !isset($bandera))
  	{
  		echo "<center><table border=0>";
  		echo "<tr><td align=center rowspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=350 HEIGHT=100></td></tr>";
  		echo "<tr><td align=center bgcolor=".$wcf2."><font size=6 text color=#FFFFFF><b>REPORTE DE RECIBOS Y NOTAS DE CARTERA</b></font></td></tr>";
  		 
		//Parámetros de consulta del reporte
  		if (!isset ($bandera))
  		{
  			$wfecini=$wfecha;
  			$wfecfin=$wfecha;
  		}	
  		
		//Fecha inicial de consulta	
  		$cal="calendario('wfecini','1')";
  		echo "<tr>";
  		echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha inicial: </font></b><INPUT TYPE='text' NAME='wfecini' value=".$wfecini." SIZE=10>";
  		echo "<button id='trigger1' onclick=".$cal.">...</button>";
  		echo "</td>";
  		echo "</td>";
  		
  		//Fecha final de consulta
  		$cal="calendario('wfecfin','2')";
  		echo "<td bgcolor=".$wcf." align=center><b><font text color=".$wclfg.">Fecha final: </font></b><INPUT TYPE='text' NAME='wfecfin' value=".$wfecfin." SIZE=10>";
  		echo "<button id='trigger2' onclick=".$cal.">...</button>";
  		echo "</td>";
  		echo "</tr>";

		//Fuente  		
  		echo "<tr>";
  		echo "<td align=center bgcolor=".$wcf." align=center colspan='2'><b><font text color=".$wclfg.">Fuente:</font></b>";
  		echo "<select name='wfuecod' style='width: 350px'>";
  		
  		$q= "SELECT 
  				carfue, cardes
  			FROM 
  				".$wbasedato."_000040
  			WHERE 
  				carrec='on' or carncr='on' or carndb='on';";
				
  		$res1 = mysql_query($q,$conex);
  		$num1 = mysql_num_rows($res1);
  		
  		if ($num1 > 0 )
  		{
  			for ($i=1;$i<=$num1;$i++)
  			{
  				$row1 = mysql_fetch_array($res1);
  				echo "<option>".$row1[0]."- ".$row1[1]."</option>";
  			}
  		}
  		echo "</select></td>";

  		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";
  		echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";
  		
  		echo "<tr>";
  		echo "<td align=center bgcolor=".$wcf." align=center colspan='2'><b><input type='button' value='Consultar' onclick='javascript:seleccionar();'></b>";
  		
    	
    	echo "</table>";
  	} else {
  		echo "<table border=0 width=100%>";
  		echo "<tr><td align=right><B>Fecha:</B> ".date('Y-m-d')."</td></tr>";
  		echo "<tr><td align=left><B>Programa:</B> ".$wnomprog."</td>";
  		echo "<td align=right><B>Hora :</B> ".$hora."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td></tr>";
  		echo "</table>";
  		echo "<table border=0 align=center >";
  		echo "<tr><td align=center><H1>$wentidad</H1></td></tr>";

  		echo "</table></br>";

  		echo "<table border=0 align=center >";
  		echo "<tr><td><B>Fecha inicial:</B> ".$wfecini."</td>";
  		echo "<td><B>Fecha final:</B> ".$wfecfin."</td>";
  		echo "<td><B>Fuente :</B> ".$wfuecod."</td></tr>";
  		echo "</table>";

  		//Preparación de los parámetros
  		$vecFue=explode('-', $wfuecod);
  		
  		echo "<A href='RepNotas.php?wfecini=".$wfecini."&wfecfin=".$wfecfin."&wfuecod=".$vecFue[0]."'><center>VOLVER</center></A><br>";
  		echo "<input type='HIDDEN' NAME= 'wfecini' value='".$wfecini."'>";
  		echo "<input type='HIDDEN' NAME= 'wfecfin' value='".$wfecfin."'>";
  		echo "<input type='HIDDEN' NAME= 'wfuecod' value='".$wfuecod."'>";
  		echo "<input type='HIDDEN' NAME= 'bandera' value='1'>";

  		
	
  		//Consulta
  		$q = "SELECT
				rennum, Rdefac, Rennom, Renfue, farstore_000020.Fecha_data, Renvca, Rdevca 
 			FROM 
 				".$wbasedato."_000020, ".$wbasedato."_000021
 			WHERE
 				Renfue = Rdefue
 				AND Rennum = Rdenum
 				AND Rencco = Rdecco
 				AND ".$wbasedato."_000020.Fecha_data BETWEEN '".$wfecini."' AND '".$wfecfin."'
 				AND Renfue = '".$vecFue[0]."'  
 			ORDER by 1;";
		
		$err = mysql_query($q,$conex);
		$num = mysql_num_rows($err);
		
		//Variables acumuladoras
		$acumValorCanceladoEncabezado = 0;
		$acumValorCanceladoDetalle = 0;
		
		if($num > 0){
			$cont1 = 0;			
			$row = mysql_fetch_array($err);
			
			echo "<table border=0 align=center>";
			
			//Encabezado reporte
			echo "<tr>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff><b>Documento</b></font></td>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff><b>Factura de venta</b></font></td>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff><b>Nombre</b></font></td>";
//			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff><b>Fuente</b></font></td>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff><b>Fecha</b></font></td>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff><b>Valor documento</b></font></td>";
			echo "<td align=center bgcolor=".$wcf1."><font text color=#efffff><b>Valor cancelado</b></font></td>";
			echo "</tr>";
				
			while($cont1 < $num){
				$colorFondo = $wcf;
				if($cont1 % 2){
					$colorFondo = $wcf3;
				}
				
				echo "<tr>";
				echo "<td align=center bgcolor=".$colorFondo.">".$row[0]."</td>";
				echo "<td align=center bgcolor=".$colorFondo.">".strtoupper($row[1])."</td>";
				echo "<td bgcolor=".$colorFondo.">".$row[2]."</td>";
//				echo "<td align=center bgcolor=".$colorFondo.">".$row[3]."</td>";
				echo "<td align=center bgcolor=".$colorFondo.">".$row[4]."</td>";
				echo "<td align=right bgcolor=".$colorFondo.">".number_format($row[5],0,'.',',')."</td>";
				echo "<td align=right bgcolor=".$colorFondo.">".number_format($row[6],0,'.',',')."</td>";
				echo "</tr>";
				
				//Acumuladores
				$acumValorCanceladoEncabezado += $row[5];
				$acumValorCanceladoDetalle += $row[6];
				
				$cont1++;
				$row = mysql_fetch_array($err);
			}
			echo "<tr>";
			echo "<td colspan='4'></td>";
			echo "<td align=right bgcolor=".$wcf1."><font text color=#efffff><b>Total: ".number_format($acumValorCanceladoEncabezado,0,'.',',')."</b></font></td>";
			echo "<td align=right bgcolor=".$wcf1."><font text color=#efffff><b>Total cancelado: ".number_format($acumValorCanceladoDetalle,0,'.',',')."</b></font></td>";
			echo "</tr>";
			echo "</table>";
			echo "<A href='RepNotas.php?wfecini=".$wfecini."&wfecfin=".$wfecfin."&wfuecod=".$vecFue[0]."'><center>VOLVER</center></A><br>";			
		} else {
			echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
			echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>No se encontraron documentos con los criterios especificados</td><tr>";
		}		
  	}
}
?>
</html>