<html>
<head>
  <title>MATRIX - INFORMACION GENERAL DEL TURNO DE FISIATRIA</title>
  <style type="text/css">
    	
    	#tipoG00{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;table-layout:fixed;text-align:center;}
    	#tipoG01{color:#000066;background:#99CCFF;font-size:8pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:2em;}
    	#tipoG02{color:#000066;background:#99CCFF;font-size:8pt;font-family:Tahoma;font-weight:bold;width:60em;text-align:left;height:2em;}
    	#tipoG03{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:left;height:2em;}
    	#tipoG04{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;width:60em;text-align:left;height:2em;}
    	#tipoM00{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:center;height:2em;}
    	#tipoM01{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:20em;text-align:center;height:2em;}
    	#tipoM02{color:#000066;background:#999999;font-size:8pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:center;height:2em;}
    	#tipoG08{color:#000066;background:#99CCFF;font-size:8pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:left;height:2em;}
    	#tipoG09{color:#000066;background:#FFFFFF;font-size:8pt;font-family:Tahoma;font-weight:bold;width:10em;text-align:left;height:2em;}
    	
    </style>
    <SCRIPT>
      function Cerrar() {
        setTimeout("close();",30000);
      }
      </SCRIPT>
</head>
<body onLoad="Cerrar();" BGCOLOR="#FFFFFF">
<BODY TEXT="#000066">

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='InfoTurFis' action='InfoTurFis.php' method=post>";
	

	

	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	//                 0      1          2             3          4        5
	$query = "select Cedula, Nombre, Responsable, Descripcion, Actividad, Tipo  from ".$empresa."_000017, ".$empresa."_000002 ";
	$query .= " where Terapeuta = '".$wter."' ";
	$query .= "   and Fecha = '".$wfecha."' ";
	$query .= "   and Hora_Inicial = '".substr($whin,0,2).substr($whin,3,2)."' ";
	$query .= "   and Responsable = Nit ";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if ($num>0)
	{
		echo "<center><table border=0 align=center id=tipoG00>";
		echo "<tr><td colspan=3>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
		echo "<tr><td colspan=3>UNIDAD DE MEDICINA FISICA Y REHABILITACION</td></tr>";
		echo "<tr><td colspan=3>DATOS DEL TURNOS</td></tr></table></center>";
		echo "<center><table border=0 align=center id=tipoG00>";
		echo "<tr><td id=tipoM00>CEDULA</td><td id=tipoM01>NOMBRE</td><td id=tipoM01>RESPONSABLE</td><td id=tipoM02>TIPO</td><td id=tipoM02>ESTADO</td></tr>";
		for ($i=0;$i<$num;$i++)
		{
			if($i % 2 ==0)
			{
				$color="tipoG01";
				$color1="tipoG08";
			}
			else
			{
				$color="tipoG03";
				$color1="tipoG09";
			}
			$row = mysql_fetch_array($err);
			echo "<tr>";
			echo "<td id=".$color1.">".$row[0]."</td>";
			echo "<td id=".$color.">".$row[1]."</td>";
			echo "<td id=".$color.">".$row[2]."-".$row[3]."</td>";
			switch ($row[5])
			{
				case "A":
					$colorA="#FFCC66";
					echo "<td bgcolor=".$colorA.">AMBULATORIO</td>";
				break;
				case "H":
					$colorA="#CCCCFF";
					echo "<td bgcolor=".$colorA.">HOSPITALIZADO</td>";
				break;
			}
			switch ($row[4])
			{
				case 0:
					$colorA="#0099CC";
					echo "<td bgcolor=".$colorA.">PENDIENTE</td>";
				break;
				case 1:
					$colorA="#33FF00";
					echo "<td bgcolor=".$colorA.">ASISTIO</td>";
				break;
				case 2:
					$colorA="#CC99FF";
					echo "<td bgcolor=".$colorA.">CANCELO</td>";
				break;
				case 3:
					$colorA="#FF0000";
					echo "<td bgcolor=".$colorA.">NO ASISTIO</td>";
				break;
			}
			echo "</tr>";
		}
		echo "</table></center>";
	}
	else
		echo "LA IDENTIFICACION DEL TURNO NO EXISTE EN LA BASE DE DATOS<BR> CONSULTE CON SISTEMAS";
	mysql_free_result($err);
	mysql_close($conex);
}
?>
</body>
</html>