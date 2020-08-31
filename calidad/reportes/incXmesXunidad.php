<html>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<?php
include_once("conex.php");

/********************************************************
 *           Autor: Ana Maria Betancur					*
 *			Fecha de Creación:2005-04-19				*
 *	Realiza un informes de inconsistencias en la 		*	
 *	facturacion, entre año-mes determinados por el 		*
 *	usuario. Los permisos determinan si tiene derecho 	*
 *	a ver los de todos las Unidades o solo la del CC al *
 *						cual pertenece					*
 ********************************************************/
session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='' method=post>";
	if(!isset($permiso)){
		/*Busca que permiso tiene el usuario
		es decir que unidad tiene derecho a ver*/
		include_once("calidad/permiso.php");
	}
	if(!isset($anoini) and $permiso != "")
	{
		echo  "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td colspan=2 align=center><b>INFORME INCONSIATENCIAS POR UNIDAD</b></td></tr>";
		if($permiso != 'Todos'){
			/*Solo tiene derecho a ver la unidad establecida or el permiso*/
			echo "<input type='hidden' name='unidad' value='".$permiso."'>";

		}else{
			/*Personal administrativo que tiene derecho a ver de todas las unidades*/
			echo "<tr><td bgcolor=#cccccc align=center>Unidad:</td>";
			echo "<td bgcolor=#cccccc align=center><select name='unidad'>";
			$query = "select DISTINCT(calidad_000003.Unidad_inc) from calidad_000003 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo "<option>Todas</option>";
			if ($num > 0){
				for($i=0;$i<$num;$i++){
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."</option>";
				}
			}
			echo "</select></td></tr>";
		}
		/*Escoger el año mes inicial y final*/
		echo  "<tr><td bgcolor=#cccccc align=center>Año Ini</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='anoini' size=10 maxlength=4> AAAA</td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Mes Ini</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='mesini' size=10 maxlength=2> mm</td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Año Fin</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='anofin' size=10 maxlength=4> AAAA</td></tr>";
		echo  "<tr><td bgcolor=#cccccc align=center>Fecha Fin</td>";
		echo  "<td bgcolor=#cccccc align=center><input type='TEXT' name='mesfin' size=10 maxlength=2> mm</td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else if (isset($unidad))
	{
		$fechaini=$anoini."-".$mesini;
		$fechafin=$anofin."-".$mesfin;
		if($fechafin >= $fechaini){
			/*La fecha inicial es menor que la final*/
			$fecfin=mktime(0,0,0,intval($mesfin),1,$anofin);
			$meses=1;
			$m[$meses]=date("Y-m",mktime(0,0,0,intval($mesini),1,$anoini));
			if($fechaini != $fechafin){
				/*Creo un vector en donde esten los año-meses a mostrar*/
				do{
					$fecact=mktime(0,0,0,intval($mesini)+$meses,1,$anoini);
					$meses++;
					$m[$meses]=date("Y-m",$fecact);	
				}while($fecact != $fecfin);
			}
			
			echo "<table border='1' width='600'>";			
			if($unidad == "Todas"){
				/*Se va ha hacer informe de todas las unidades*/
				$unidad="";
				$colspan=($meses*2)+1;
				echo "<tr><td colspan='".$colspan."' align=center bgcolor='#000066'><font color='#FFFFFF'><b>INFORME DE INCONSISTENCIAS POR UNIDAD</b></td></tr>";
				
			}else{
				/*Reporte de una sola unidad*/
				$colspan=$meses*2;
				echo "<tr><td colspan='".$colspan."' align=center bgcolor='#000066'><font color='#FFFFFF'><b>INFORME DE INCONSISTENCIAS POR UNIDAD</b></td></tr>";
				echo "<tr><td colspan='".$colspan."' align=center bgcolor='#000066'><font color='#FFFFFF' ><b>$unidad</b></td></tr></table>";				
				$unidad=" Subcodigo='".substr($unidad,0,2)."' and";
			}
			
			echo "<tr><td colspan='".$colspan."' align=center bgcolor='#000066'><b><font color='#FFFFFF'>Desde $fechaini hasta $fechafin</b></td></tr></table>";
			
			/*La prefactura*/
			$tabla='calidad_000005';
			$var='fecha_inc';
			echo "<BR><BR><BR><table border='1' width='600'>";			
			echo "<tr><td colspan='".$colspan."' align=center bgcolor='#000066'><font color='#FFFFFF'><b>EN LA PREFACTURA</b></td></tr>";
			include_once("calidad/mesXmes.php");//imprimir en pantalla por unidad por mes 
			
			/*La factura*/
			$tabla='calidad_000003';
			$var="fecha_inconsistencia";
			echo "</table><BR><BR><table border='1' width='600'>";		
			echo "<tr><td colspan='".$colspan."' align=center bgcolor='#000066'><font color='#FFFFFF'><b>EN LA FACTURA</b></td></tr>";
			include_once("calidad/mesXmes.php");//imprimir en pantalla por unidad por mes 
			
			
		}else{
			echo "La Fecha de inicio debe ser mayor a la de fin.";
		}
	}else{
		echo "No esta autorizado para ver estos reportes consulte con sistemas";
	}
}
?>
</body>
</html>