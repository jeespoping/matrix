<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Distribucion de Rubros Presupuestados Basado en Movimiento Real (T1 - T26 - T43)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro196.php Ver. 2017-03-22</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro196.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>DISTRIBUCION DE RUBROS PRESUPUESTADOS BASADO EN MOVIMIENTO REAL (T1 - T26 - T43)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Rubro de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wrub' size=3 maxlength=3></td></tr>";
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
			$wemp=substr($wemp,0,strpos($wemp,"-"));
			$query = "SELECT sum(Resmon)  from ".$empresa."_000043 ";
			$query = $query."  where Resano = ".$wanop;
			$query = $query."    and Resper = ".$wmes;
			$query = $query."    and Resemp = '".$wemp."' ";
			$query = $query."    and Rescpr = '".$wrub."' ";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$wsumap = $row[0];
			$query = "SELECT Mensaje  from ".$empresa."_000001 ";
			$query = $query."  where Codigo = '"."1".$wemp."' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$row = mysql_fetch_array($err);
				$rub = explode(",", $row[0]);
				$wsw = 0;
				for ($i=0;$i<count($rub);$i++)
					if($rub[$i] == $wrub)
						$wsw = 1;	
			}
			
			if($wsumap != 0 and $wsw == 1)
			{
				$k=0;
				$query = "delete from ".$empresa."_000043 ";
				$query = $query."  where resano = ".$wanop;
				$query = $query."    and Resper = ".$wmes;
				$query = $query."    and resemp = '".$wemp."' ";
				$query = $query."    and Rescpr = '".$wrub."' ";
				$err = mysql_query($query,$conex);
				$query = "SELECT Meccco, sum(Mecval) from ".$empresa."_000026 ";
				$query = $query."  where Mecano = ".$wanop;
				$query = $query."    and Mecmes = ".$wmes;
				$query = $query."    and Mecemp = '".$wemp."' ";
				$query = $query."    and Meccpr = '".$wrub."' ";
				$query = $query."  group by 1 ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					$VAL = array();
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$VAL[$i][0] = $row[0];
						$VAL[$i][1] = $row[1];
						$VAL[$i][2] = 0;
					}
					$wsumar = 0;
					for ($i=0;$i<$num;$i++)
						$wsumar += $VAL[$i][1];
					for ($i=0;$i<$num;$i++)
						$VAL[$i][2] = $VAL[$i][1] / $wsumar;
				}
				for ($i=0;$i<$num;$i++)
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$wmonto = $wsumap * $VAL[$i][2];
					$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Resemp, Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$VAL[$i][0]."','".$wrub."',".$wanop.",".$wmes.",".$wmonto.",'196','C-".$empresa."')";
					$err1 = mysql_query($query,$conex);
					if ($err1 != 1)
						echo mysql_errno().":".mysql_error()."<br>";
					else
					{
						$k++;
						echo "REGISTRO INSERTADO  : ".$k."<br>";
						}
				}
				echo "REGISTROS INSERTADOS : ".$k;
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL RUBRO NO TIENE VALOR EN T 43 O NO HACE PARTE DE LOS RUBROS A DISTRIBUIR DE LA TABLA 1</MARQUEE></FONT>";
				echo "<br><br>";			
			}
		}
	}
?>
</body>
</html>
