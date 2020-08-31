<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion Automatica De Drivers De Actividad</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro146.php Ver. 2016-04-29</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro146.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wmes) or !isset($wccoi) or !isset($wccof))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION AUTOMATICA DE DRIVERS DE ACTIVIDAD</td></tr>";
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
			$query = "delete from ".$empresa."_000090 ";
			$query .= "  where Mdaano = ".$wanop;
			$query .= "    and Mdaemp = '".$wemp."' ";
			$query .= "    and Mdames = ".$wmes;
			$query .= "    and Mdacco between '".$wccoi."' and '".$wccof."' "; 
			$err = mysql_query($query,$conex);
			
			//                  0       1      2      3     4       5           6
			$query  = "select mprcco,mprpro,progru,Procod,Rdsdri,Procan,sum(estcan) from ".$empresa."_000095,".$empresa."_000100,".$empresa."_000141,".$empresa."_000142 ";
			$query .= " where mprdri = 'on' "; 
			$query .= "   and mpremp = '".$wemp."' ";
			$query .= "   and mprcco between '".$wccoi."' and '".$wccof."' "; 
			$query .= "   and mpremp = proemp  ";
			$query .= "   and mprcco = procco  ";
			$query .= "   and mprpro = propro  ";
			$query .= "   and mprcon = procon  ";
			$query .= "   and mprgru = progru  ";
			$query .= "   and procco = proccp  ";
			$query .= "   and protip = '1' ";
			$query .= "   and proemp = estemp  ";
			$query .= "   and procco = estcco  ";
			$query .= "   and propro = estcod ";
			$query .= "   and procon = estcon ";
			$query .= "   and estano = ".$wanop; 
			$query .= "   and estmes = ".$wmes; 
			$query .= "   and proemp = Rdsemp ";
			$query .= "   and procco = Rdscco ";
			$query .= "   and Procod = Rdssub ";
			$query .= " group by 1,2,3,4,5,6 ";
			$query .= " order by 1,4,3,2,5,6 ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			$suma=0;
			$llave="";
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($llave != $row[0].$row[3].$row[4])
					{
						if($i > 0)
						{
							$k++;
							echo "REGISTRO INSERTADO  : ".$k."<br>";
							$fecha = date("Y-m-d");
							$hora = (string)date("H:i:s");
							$query = "insert ".$empresa."_000090 (medico,fecha_data,hora_data,Mdaemp, Mdaano, Mdames, Mdacco, Mdasub, Mdadri, Mdacan ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wmes.",'".$wccoa."','".$wsub."','".$wdri."',".$wval.",'C-".$empresa."')";
							$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						}
						$llave=$row[0].$row[3].$row[4];
						$wccoa=$row[0];
						$wsub=$row[3];
						$wdri=$row[4];
						$wval=0;
					}
					$wval += $row[5] * $row[6];
				}
				$k++;
				echo "REGISTRO INSERTADO  : ".$k."<br>";
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "insert ".$empresa."_000090 (medico,fecha_data,hora_data,Mdaemp, Mdaano, Mdames, Mdacco, Mdasub, Mdadri, Mdacan ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wmes.",'".$wccoa."','".$wsub."','".$wdri."',".$wval.",'C-".$empresa."')";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			}
			
			//                  0       1      2      3     4       5           6
			$query  = "select mprcco,mprpro,Pqugru,Pqucod,Rdsdri,Pqucan,sum(estcan) from ".$empresa."_000095,".$empresa."_000099,".$empresa."_000141,".$empresa."_000142 ";
			$query .= " where mprdri = 'on' "; 
			$query .= "   and mpremp = '".$wemp."' ";
			$query .= "   and mprcco between '".$wccoi."' and '".$wccof."' "; 
			$query .= "   and mpremp = Pquemp  ";
			$query .= "   and mprcco = Pqucco  ";
			$query .= "   and mprpro = Pqupro  ";
			$query .= "   and mprcon = Pqucon  ";
			$query .= "   and mprgru = Pqugru  ";
			$query .= "   and Pqucco = Pquccp  ";
			$query .= "   and Pqutip = '1' ";
			$query .= "   and Pquemp = estemp  ";
			$query .= "   and Pqucco = estcco  ";
			$query .= "   and Pqupro = estcod ";
			$query .= "   and Pqucon = estcon ";
			$query .= "   and estano = ".$wanop; 
			$query .= "   and estmes = ".$wmes; 
			$query .= "   and Pquemp = Rdsemp  ";
			$query .= "   and Pqucco = Rdscco ";
			$query .= "   and Pqucod = Rdssub ";
			$query .= " group by 1,2,3,4,5,6 ";
			$query .= " order by 1,4,3,2,5,6 ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			$suma=0;
			$llave="";
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if($llave != $row[0].$row[3].$row[4])
					{
						if($i > 0)
						{
							$k++;
							echo "REGISTRO INSERTADO  : ".$k."<br>";
							$fecha = date("Y-m-d");
							$hora = (string)date("H:i:s");
							$query  = "Select count(*) from ".$empresa."_000090 where Mdaano=".$wanop." and Mdames=".$wmes." and Mdacco='".$wccoa."' and Mdasub='".$wsub."' and Mdadri='".$wdri."' and Mdaemp='".$wemp."'";
							$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
							$row1 = mysql_fetch_array($err1);
							if($row1[0] == 0)
								$query = "insert ".$empresa."_000090 (medico,fecha_data,hora_data, Mdaemp, Mdaano, Mdames, Mdacco, Mdasub, Mdadri, Mdacan ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wmes.",'".$wccoa."','".$wsub."','".$wdri."',".$wval.",'C-".$empresa."')";
							else
								$query = "update ".$empresa."_000090 set Mdacan = Mdacan + ".$wval." where Mdaano=".$wanop." and Mdames=".$wmes." and Mdacco='".$wccoa."' and Mdasub='".$wsub."' and Mdadri='".$wdri."' and Mdaemp='".$wemp."'";
							$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						}
						$llave=$row[0].$row[3].$row[4];
						$wccoa=$row[0];
						$wsub=$row[3];
						$wdri=$row[4];
						$wval=0;
					}
					$wval += $row[5] * $row[6];
				}
				$k++;
				echo "REGISTRO INSERTADO  : ".$k."<br>";
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query  = "Select count(*) from ".$empresa."_000090 where Mdaano=".$wanop." and Mdames=".$wmes." and Mdacco='".$wccoa."' and Mdasub='".$wsub."' and Mdadri='".$wdri."' and Mdaemp='".$wemp."'";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$row1 = mysql_fetch_array($err1);
				if($row1[0] == 0)
					$query = "insert ".$empresa."_000090 (medico,fecha_data,hora_data,Mdaemp, Mdaano, Mdames, Mdacco, Mdasub, Mdadri, Mdacan ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wmes.",'".$wccoa."','".$wsub."','".$wdri."',".$wval.",'C-".$empresa."')";
				else
					$query = "update ".$empresa."_000090 set Mdacan = Mdacan + ".$wval." where Mdaano=".$wanop." and Mdames=".$wmes." and Mdacco='".$wccoa."' and Mdasub='".$wsub."' and Mdadri='".$wdri."' and Mdaemp='".$wemp."'";
				$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			}
			echo "<b>TOTAL REGISTROS INSERTADOS : ".$k."</b>";
		}
	}
?>
</body>
</html>
