<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Proyeccion Numero de Dias Cama (T31)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro100.php Ver. 2015-11-06</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function bisiesto($year)
{
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro100.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>PROYECCION NUMERO DE DIAS CAMA (T31)</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Presupuestacion</td>";
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
			#INICIO PROGRAMA
			$wemp=substr($wemp,0,strpos($wemp,"-"));
			$query = "SELECT Cierre_Ppto from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes = 0 ";
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
				$k=0;
				$query = "delete  from ".$empresa."_000031 ";
				$query = $query."  where Mopano = ".$wanop;
				$query = $query."    and Mopemp = '".$wemp."' ";
				$query = $query."    and Mopcod = '12' ";
				$query = $query."    and Moptip = 'H' ";
				$err = mysql_query($query,$conex);
				$wtable1= date("YmdHis");
				$wtable1=" temp1_".$wtable1;
				$query = "Create table  IF NOT EXISTS ".$wtable1." as ";
				$query = $query." select Mopano, Mopmes, Mopcco, Mopcod, Mopcan   from ".$empresa."_000031 ";
				$query = $query." where Mopano = ".$wanop; 
				$query = $query."   and Mopemp = '".$wemp."' ";
				$query = $query."   and Mopmes between 1 and 12 "; 
				$query = $query."   and Mopcod = '27' "; 
				$err = mysql_query($query,$conex);
				$wtable2= date("YmdHis");
				$wtable2=" temp2_".$wtable2;
				$query = "Create table  IF NOT EXISTS ".$wtable2." as ";
				$query = $query." select Mopano, Mopmes, Mopcco, Mopcod, Mopcan   from ".$empresa."_000031 ";
				$query = $query." where Mopano = ".$wanop; 
				$query = $query."   and Mopemp = '".$wemp."' ";
				$query = $query."   and Mopmes between 1 and 12 "; 
				$query = $query."   and Mopcod = '34' "; 
				$err = mysql_query($query,$conex);
				$query = " select ".$wtable1.".Mopano, ".$wtable1.".Mopmes, ".$wtable1.".Mopcco, ".$wtable1.".Mopcan, ".$wtable2.".Mopcan, Perdia   from ".$wtable1.",".$wtable2.",".$empresa."_000040 ";
				$query = $query." where ".$wtable1.".Mopano = ".$wtable2.".Mopano"; 
				$query = $query."      and ".$wtable1.".Mopmes = ".$wtable2.".Mopmes "; 
				$query = $query."      and ".$wtable1.".Mopcco = ".$wtable2.".Mopcco ";  
				$query = $query."      and ".$wtable1.".Mopmes = Perper ";
				$query = $query."      and Perano = ".$wanop; 
				$query = $query." order by ".$wtable1.".Mopcco "; 
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						if($row[1] == 2)
							if(bisiesto($row[0]))
								$row[5] = 29;
							else
								$row[5] = 28;
						$dias=$row[4] * ($row[3] / 100) * $row[5];
						$query = "insert ".$empresa."_000031 (medico,fecha_data,hora_data,Mopemp, Mopano, Mopmes, Mopcco, Mopcod, Mopcan, Moptip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$row[1].",'".$row[2]."','12',".round($dias, 0).",'H','C-".$empresa."')";
						$err2 = mysql_query($query,$conex);
						if ($err2 != 1)
							echo mysql_errno().":".mysql_error()."<br>";
						else
						{
							$k++;
							echo "REGISTRO INSERTADO  : ".$k."<br>";
						}
					}
					$query = "DROP table ".$wtable1;
					$err = mysql_query($query,$conex);
					$query = "DROP table ".$wtable2;
					$err = mysql_query($query,$conex);
					echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k."</b><br>";
				}
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PERIODO  ESTA CERRADO !! -- NO SE PUEDE EJECUTAR ESTE PROCESO</MARQUEE></FONT>";
				echo "<br><br>";			
			}
   		}
}		
?>
</body>
</html>
