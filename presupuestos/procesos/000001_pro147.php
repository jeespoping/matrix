<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion Automatica De Ejecucion Operativa Real</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro147.php Ver. 2017-01-26</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro147.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wmes) or !isset($wccoi) or !isset($wccof))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION AUTOMATICA DE EJECUCION OPERATIVA REAL</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Año de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccoi' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccof' size=4 maxlength=4></td></tr>";
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
			$wres=strtoupper ($wres);
			$k=0;
			$query = "delete from ".$empresa."_000032 ";
			$query .= "  where Morano = ".$wanop;
			$query .= "    and Moremp = '".$wemp."' ";
			$query .= "    and Mormes = ".$wmes;
			$query .= "    and Morcco between '".$wccoi."' and '".$wccof."' "; 
			$query .= "    and Morfte = 'A' ";
			$err = mysql_query($query,$conex);
			
			//                  0       1      2         3       
			$query  = "select mprcco,mprpro,mprgru,sum(Estcan) from ".$empresa."_000095,".$empresa."_000141 ";
			$query .= " where Mprest = 'on' "; 
			$query .= "   and Mpremp = '".$wemp."' ";
			$query .= "   and mprcco between '".$wccoi."' and '".$wccof."' "; 
			$query .= "   and Mpremp = estemp  ";
			$query .= "   and mprcco = estcco  ";
			$query .= "   and mprpro = estcod ";
			$query .= "   and mprcon = estcon ";
			$query .= "   and estano = ".$wanop; 
			$query .= "   and estmes = ".$wmes; 
			$query .= " group by 1,2,3 ";
			$query .= " order by 1,3,2 ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			$suma=0;
			$llave="";
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($llave != $row[0].$row[2])
					{
						if($i > 0)
						{
							$k++;
							echo "REGISTRO INSERTADO  : ".$k."<br>";
							$fecha = date("Y-m-d");
							$hora = (string)date("H:i:s");
							$query = "insert ".$empresa."_000032 (medico,fecha_data,hora_data,Moremp, Morano, Mormes, Morcco, Morcod, Morcan, Morfte, Mortip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wmes.",'".$wccoa."','".$wgru."',".$wval.",'A','P','C-".$empresa."')";
							$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						}
						$llave=$row[0].$row[2];
						$wccoa=$row[0];
						$wgru=$row[2];
						$wval=0;
					}
					$wval += $row[3];
				}
				$k++;
				echo "REGISTRO INSERTADO  : ".$k."<br>";
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "insert ".$empresa."_000032 (medico,fecha_data,hora_data,Moremp, Morano, Mormes, Morcco, Morcod, Morcan, Morfte, Mortip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wmes.",'".$wccoa."','".$wgru."',".$wval.",'A','P','C-".$empresa."')";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			}
			echo "<b>TOTAL REGISTROS INSERTADOS : ".$k."</b>";
		}
	}
?>
</body>
</html>
