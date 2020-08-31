<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Generacion de Explicaciones Costos Indirectos</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro143.php Ver. 2015-09-18</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro143.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wmesi) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION DE EXPLICACIONES COSTOS INDIRECTOS</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesi' size=2 maxlength=2></td></tr>";
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
			$wemp = substr($wemp,0,2);
			$k=0;
			#INICIO PROGRAMA
			$query = "DELETE from ".$empresa."_000011 ";
			$query .= " where Expano = ".$wanop;
			$query .= "   and Expper = ".$wmesi;
			$query .="    and Expemp =  '".$wemp."' ";
			$query .= "   and Expnit IN ('1091','2034') ";
			$err = mysql_query($query,$conex);
			//                  0       1      2      3      4      5   6    7       8    9    10     11
			$query  = "select Mdicco,Mdiano,Mdimes,Midcue,Midcpr,Midcco,'',Middes,Mdimon,'0',Mdicco,Ccoclas from ".$empresa."_000054,".$empresa."_000050,".$empresa."_000005 ";
			$query .= " where mdiano = ".$wanop;
			$query .= "   and mdimes = ".$wmesi; 
			$query .="    and mdiemp = '".$wemp."' ";
			$query .= "   and mditip = 'R' ";
			$query .= "   and mdiind = midcod ";
			$query .="    and mdiemp = midemp ";
			$query .= "   and Mdicco = Ccocod ";
			$query .="    and midemp = Ccoemp";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					if($row[5] == "1091")
						$row[6] = "COSTOS INDIRECTOS";
					else
						$row[6] = "COSTOS INDIRECTOS PM";
					if($row[11] == "IND")
						$row[4] = "516";
					$query = "insert ".$empresa."_000011 (medico,fecha_data,hora_data,Expemp,Expcco, Expano, Expper, Expcue, Expcpr, Expnit, Expnte, Expexp, Expmon, Exppro,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[10]."',".$row[1].",".$row[2].",'".$row[3]."','".$row[4]."','".$row[5]."','".$row[6]."','".$row[7]."',".$row[8].",'".$row[9]."','C-".$empresa."')";
           			$err2 = mysql_query($query,$conex);
           			$k++;
           			echo "REGISTROS INSERTADOS : ".$k."<br>";
       			}
   			}
			echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k."</b><br>";
        }
}		
?>
</body>
</html>
