<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
	<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion de % de Costos Variables</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro11.php Ver. 2015-09-25</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro11.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center>APLICACION DE PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center>GENERACION DE % DE COSTOS VARIABLES</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
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
			$query = "SELECT vblcco,vblccv,vblcpc,id from ".$empresa."_000047 where vblano=".$wanop." and vblemp='".$wemp."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
   			if ($num>0)
			{
				$k=0;
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);	
					 $query = "SELECT sum(mecval) as suma1 from ".$empresa."_000026 ";
					 $query = $query." where mecano = ".$wanop;
					 $query = $query."   and mecemp = '".$wemp."' ";
        			 $query = $query."   and meccco = '".$row[0]."'";
       				 $query = $query."   and meccpr = '".$row[1]."'";
       				 $err1 = mysql_query($query,$conex);
					 $num1 = mysql_num_rows($err1);
       				 $query = "SELECT sum(mecval) as suma2 from ".$empresa."_000026 ";
       				 $query = $query." where mecano = ".$wanop;
       				 $query = $query."   and mecemp = '".$wemp."' ";
        			 $query = $query."   and meccco = '".$row[0]."'";
        			 $query = $query."   and meccpr = '".$row[2]."'";
					 $err2 = mysql_query($query,$conex);
					 $num2 = mysql_num_rows($err2);
        			 if($num1>0 and $num2>0)
        			 {
	        			 $row1 = mysql_fetch_array($err1);	
	        			 $row2 = mysql_fetch_array($err2);	
	        			 $por=$row1[0]/$row2[0];
	        			 $query = "update ".$empresa."_000047 set vblpor=".$por." where id=".$row[3];
	        			 $err3 = mysql_query($query,$conex);	
	        			 $k++;
        			}
        			else
        			{
	        			$query = "update ".$empresa."_000047 set vblpor=0 where id=".$row[3];
	        			 $err3 = mysql_query($query,$conex);
        			 }
        		 }
        		 echo "NUMERO DE REGISTROS ACTUALIZADOS : ".$k."<br>";
        		 echo "NUMERO DE REGISTROS TOTALES : ".$num."<br>";
        	}
        }
}		
?>
</body>
</html>
