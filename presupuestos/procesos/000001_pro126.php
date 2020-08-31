<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Variacion Presupuestal x Rubro (Mvto Pptal Resumido)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro126.php Ver. 2015-11-17</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro126.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop)  or !isset($wrub)  or !isset($wpor) or !isset($wtip) or (strtoupper ($wtip) != "I" and strtoupper ($wtip) != "D") or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>VARIACION PRESUPUESTAL X RUBRO (MVTO PPTAL RESUMIDO)</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Rubro Presupuestal</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wrub' size=3 maxlength=3></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Incremento o Decremento ? (I/D)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wtip' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Porcentaje</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpor' size=6 maxlength=6></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT Empcod,Empdes  from ".$empresa."_000153 order by Empcod";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wemp'>";
				echo "<option>Seleccione</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			#INICIO PROGRAMA
			$wemp=substr($wemp,0,strpos($wemp,"-"));
			$wtip=strtoupper ($wtip);
			$k=0;
			if($wtip == "I")
				$mult=1;
			else
				$mult=-1;
			$query = "select Rescco, Rescpr, Resano, Resper, Resmon,Id from ".$empresa."_000043 ";
			$query = $query." where Resano =  ".$wanop; 
			$query = $query."   and Resemp = '".$wemp."' ";
			$query = $query."   and Rescpr = '".$wrub."'"; 
			// Si el rubro presupuestal es 201 (Nomina) solo afacta los registros de ptores generados por el proceso 114 (Generacion Resumen de Gastos de Personal)
			if($wrub == "201")
				$query = $query."     and Resind =  '114' "; 
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$row[4] = $row[4] * (1  + (($wpor / 100)*$mult));
					$query = "update ".$empresa."_000043 set Resmon=".$row[4]."  where Id=".$row[5];
	       			$err2 = mysql_query($query,$conex) or die ("Error en la Actualizacion : ".$query);
	           		$k++;
	           		echo "REGISTROS ACTUALIZADO  : ".$k."<br>";
   				}
   				echo "<b>NUMERO DE REGISTROS ACTUALIZADOS : ".$k."</b><br>";
			}
   		}
}		
?>
</body>
</html>
