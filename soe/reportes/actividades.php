<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.soe1.submit();
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Resumen de Actividades x Paciente</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> actividades.php Ver. 2010-05-14</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");
function array_s($item,&$actividad,$k,$ubicacion)
{
	$it1=substr($item,0,strpos($item," "))." Real.";
	for ($j=0;$j<=$k;$j++)
	{
		if($it1 == $actividad[$j][3] and $actividad[$j][7] == 0 and $actividad[$j][4] == $ubicacion)
		{
			$actividad[$j][7]=1;
			return true;
		}
	}
	return false;
}
	
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='soe1' action='actividades.php' method=post>";
	

	

	if(!isset($paciente))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>RESUMEN DE ACTIVIDADES X PACIENTE</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Paciente</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='paciente' size=12 maxlength=12></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$query = "select Pacdoc, Pacap1, Pacap2, Pacno1, Pacno2 from  soe_000100 ";
 		$query .= " where Pacdoc = '".$paciente."'";
		$err = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err);
		if($num1 == 0)
		{
			$query = "select Identificacion, Nombre1, Nombre2, Apellido1, Apellido2  from  soe1_000002 ";
	 		$query .= " where identificacion = '".$paciente."'";
			$err = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err);
		}
		if($num1 > 0)
		{
			$row1 = mysql_fetch_array($err);
			$nom=$row1[0]."-".$row1[1]." ".$row1[2]." ".$row1[3]." ".$row1[4];
			echo "<table border=0 align=center>";
			echo "<tr><td align=center bgcolor=#cccccc colspan=3 rowspan=3 align=center valign=middle><IMG SRC='/MATRIX/images/medical/Pos/logo_soe.png'></td></tr>";
			echo "<tr><td align=center bgcolor=#cccccc colspan=3 align=center valign=middle><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A. - SALUD ORAL ESPECIALIZADA (SOE)</b></font></td></tr>";
			echo "<tr><td bgcolor=#CCCCCC colspan=3 align=center><b>RESUMEN DE ACTIVIDADES X PACIENTE</b></td></tr>";
			echo "<tr><td bgcolor=#CCCCCC colspan=6 align=center><b>PACIENTE : ".$nom."</b></td></tr>";
			echo "<tr><td bgcolor=#999999 colspan=6 align=center><b><font size=4>RESUMEN</font></b></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center><b><font size=2>Fecha</font></b></td><td bgcolor=#cccccc align=center><b><font size=2>Hora</font></b></td><td bgcolor=#cccccc align=center><b><font size=2>Actividad</font></b></td><td bgcolor=#cccccc align=center><b><font size=2>Superficies</font></b></td><td bgcolor=#cccccc align=center><b><font size=2>Comentarios</font></b></td><td bgcolor=#cccccc align=center><b><font size=2>Odontologo</font></b></td></tr>";
			$query = "select Diente, Fecha, Hora, Actividad, Ubicacion, Comentarios, Odontologo from  soe_000130 ";
	 		$query .= " where identificacion = '".$paciente." '";
	 		$query .= " Order by Diente, Fecha desc, Hora desc";
			$err = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err);
			$k="";
			if($num1 > 0)
			{
				for ($i=0;$i<$num1;$i++)
				{
					$row1 = mysql_fetch_array($err);
					if($k != $row1[0])
					{
						if($i != 0)
						{
							for ($j=0;$j<=$kn;$j++)
							{
								if(substr($actividad[$kn-$j][3],strlen($actividad[$kn-$j][3])-5,5) == "Pend.")
									//if(array_s($actividad[$kn-$j][3],$actividad,$kn-$j,$actividad[$kn-$j][4]))
									if(array_s($actividad[$kn-$j][3],$actividad,$kn,$actividad[$kn-$j][4]))
										$actividad[$kn-$j][8] = 1;
									else
										$actividad[$kn-$j][8] = 2;
							}
							for ($j=0;$j<=$kn;$j++)
							{
								$color="#dddddd";
								echo "<tr><td bgcolor=".$color." align=center><b><font size=2>".$actividad[$j][1]."</font></b></td><td bgcolor=".$color." align=center><b><font size=2>".$actividad[$j][2]."</font></b></td>";
								if(substr($actividad[$j][3],strlen($actividad[$j][3])-5,5) !=  "Pend.")
									echo "<td bgcolor=".$color." align=center><b><font size=2>".$actividad[$j][3]."</font></b></td>";
								else
								{
									if($actividad[$j][8] == 1)
										echo "<td bgcolor=#CC99FF align=center><b><font size=2>".$actividad[$j][3]."</font></b></td>";
									else
										echo "<td bgcolor=#FF0000 align=center><b><font size=2>".$actividad[$j][3]."</font></b></td>";
								}
								echo "<td bgcolor=".$color."><b><font size=2>".$actividad[$j][4]."</font></b></td><td bgcolor=".$color."><b><font size=2>".$actividad[$j][5]."</font></b></td><td bgcolor=".$color."><b><font size=2>".$actividad[$j][6]."</font></b></td></tr>";
							}
						}
						$actividad=array();
						$kn=-1;
						$k=$row1[0];
						if($k == 99)
							echo "<tr><td bgcolor=#99CCFF colspan=6 align=center><b><font size=2>ACTIVIDADES GENERALES</font></b></td></tr>";
						else
							echo "<tr><td bgcolor=#99CCFF colspan=6 align=center><b><font size=2>PIEZA DENTAL NRo. ".$row1[0]."</font></b></td></tr>";
					}
					$kn +=1;
					$actividad[$kn][0]=$row1[0];
					$actividad[$kn][1]=$row1[1];
					$actividad[$kn][2]=$row1[2];
					$actividad[$kn][3]=$row1[3];
					$actividad[$kn][4]=$row1[4];
					$actividad[$kn][5]=$row1[5];
					$actividad[$kn][6]=$row1[6];
					$actividad[$kn][7]=0;
					$actividad[$kn][8]=0;
				}
				for ($j=0;$j<=$kn;$j++)
				{
					$color="#dddddd";
					echo "<tr><td bgcolor=".$color." align=center><b><font size=2>".$actividad[$j][1]."</font></b></td><td bgcolor=".$color." align=center><b><font size=2>".$actividad[$j][2]."</font></b></td>";
					if(substr($actividad[$j][3],strlen($actividad[$j][3])-5,5) !=  "Pend.")
						echo "<td bgcolor=".$color." align=center><b><font size=2>".$actividad[$j][3]."</font></b></td>";
					else
					{
						//if(array_s($actividad[$j][3],$actividad,$kn-$j,$actividad[$j][4]))
						if(array_s($actividad[$kn-$j][3],$actividad,$kn,$actividad[$kn-$j][4]))
							echo "<td bgcolor=#CC99FF align=center><b><font size=2>".$actividad[$j][3]."</font></b></td>";
						else
							echo "<td bgcolor=#FF0000 align=center><b><font size=2>".$actividad[$j][3]."</font></b></td>";
					}
					echo "<td bgcolor=".$color."><b><font size=2>".$actividad[$j][4]."</font></b></td><td bgcolor=".$color."><b><font size=2>".$actividad[$j][5]."</font></b></td><td bgcolor=".$color."><b><font size=2>".$actividad[$j][6]."</font></b></td></tr>";
				}
			}
			echo"</table><br><br>";
		}
	}
}
?>
</body>
</html>