<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Definicion Condiciones de Proyeccion de Ingresos x CCO (T123)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro173.php Ver. 2015-09-07</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function bi($d,$n,$k)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			$val=strncmp(strtoupper($k),strtoupper($d[$lm][0]),20);
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li][0]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls][0]))
					return $ls;
				else
					return -1;
	}
	elseif(isset($d[0][0]) and $d[0][0] == $k)
			return 0;
		else
			return -1;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro173.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(isset($ok))
	{
		for ($i=0;$i<$num;$i++)
		{
			$query = "select count(*) from ".$empresa."_000123 where Cpiano = ".$wanop." and Cpicco='".$data[$i][0]."' and Cpiemp = '".$wemp."' ";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			if($row[0] > 0)
			{
				if(isset($data[$i][2]))
					$data[$i][2] = "on";
				else
					$data[$i][2] = "off";
				$query = "update ".$empresa."_000123 set Cpipre = '".$data[$i][2]."', Cpical = '".$data[$i][3]."', Cpiuni = '".$data[$i][4]."'  where Cpiano=".$wanop." and Cpicco='".$data[$i][0]."' and Cpiemp = '".$wemp."' ";
				$err1 = mysql_query($query,$conex) or die("Error en la Actualizacion");
			}
			else
			{
				if(isset($data[$i][2]))
					$data[$i][2] = "on";
				else
					$data[$i][2] = "off";
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "insert ".$empresa."_000123 (medico,fecha_data,hora_data, Cpiemp, Cpiano, Cpicco, Cpipre, Cpical, Cpiuni, Seguridad) values (";
				$query .=  "'".$empresa."','";
				$query .=  $fecha."','";
				$query .=  $hora."','";
				$query .=  $wemp."',";
				$query .=  $wanop.",'";
				$query .=  $data[$i][0]."','";
				$query .=  $data[$i][2]."','";
				$query .=  $data[$i][3]."','";
				$query .=  $data[$i][4]."','C-".$empresa."')";
				$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO DATOS : ".mysql_errno().":".mysql_error());
			}
		}
		echo "<table border=0 align=center>";
		echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/logo1.jpg' ></td><td  align=center bgcolor=#cccccc><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>DATOS ACTUALIZADOS!!!</b></td></tr>";
		echo "<td bgcolor=#cccccc colspan=8 align=center><input type='submit' value='CONTINUAR'></td><tr>";
		echo"</table>";
		unset($ok);
		unset($wanop);
		unset($wemp);
	}
	else
	{
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wanob) or !isset($wmesb))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>DEFINICION CONDICIONES DE PROYECCION DE INGRESOS X CCO (T123)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Presupuesto</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Base</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanob' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes A&ntilde;o Base</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesb' size=2 maxlength=2></td></tr>";
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
			echo "<center><input type='HIDDEN' name= 'wemp' value='".$wemp."'>";
			$query = "SELECT Cierre_Ppto from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes = 0 ";
			$query = $query."    and emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
				echo "<table border=0 align=center>";
				echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/logo1.jpg' ></td><td  align=center bgcolor=#cccccc colspan=7><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
				echo "<tr><td bgcolor=#999999 colspan=5 align=center><b>DEFINICION CONDICIONES DE PROYECCION DE INGRESOS X CCO (T123)</b></td></tr>";
				echo "<tr><td bgcolor=#CCCCCC align=center><b>C.C.</b></td><td bgcolor=#CCCCCC align=center><b>DESCRIPCION</b></td><td bgcolor=#CCCCCC align=center><b>PRESUPUESTAR</b></td><td bgcolor=#CCCCCC align=center><b>FORMA CALCULO<br>INGRESO PROMEDIO</b></td><td bgcolor=#CCCCCC align=center><b>UNIDAD DE MEDIDA<br>INGRESO PROMEDIO</b></td></tr>";
				$CCO=array();
				$query  = "SELECT Cpicco, Cconom, Cpipre, Cpical, Cpiuni from ".$empresa."_000123,".$empresa."_000005 where Cpiano = ".$wanop." and Cpiemp = '".$wemp."' and Cpicco = Ccocod  and Cpiemp = Ccoemp ";
				$query .= "    order by 1 ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$CCO[$i][0]=$row[0];
						$CCO[$i][1]=$row[1];
						$CCO[$i][2]=$row[2];
						$CCO[$i][3]=$row[3];
						$CCO[$i][4]=$row[4];
					}
				}
				$numcco=$num;
				$query  = "select Miocco, cconom, sum(Mioito) from ".$empresa."_000063,".$empresa."_000005 ";
				$query .= " where Mioano = ".$wanob;
				$query .= "   and Miomes <= ".$wmesb;
				$query .= "   and Mioemp = '".$wemp."' ";
				$query .= "   and Miocco = Ccocod ";
				$query .= "   and Mioemp = Ccoemp ";
				$query .= "  group by 1,2 ";
				$query .= "  order by 1 ";
				$err = mysql_query($query,$conex) or die("ERROR : ".mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$data=array();
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$pos=bi($CCO,$numcco,$row[0]);
						if($pos != -1)
						{
							$wdata[$i][0] = $CCO[$pos][0];
							$wdata[$i][1] = $CCO[$pos][1];
							$wdata[$i][2] = $CCO[$pos][2];
							$wdata[$i][3] = $CCO[$pos][3];
							$wdata[$i][4] = $CCO[$pos][4];
						}
						else
						{
							$wdata[$i][0] = $row[0];
							$wdata[$i][1] = $row[1];
							$wdata[$i][2] = "off" ;
							$wdata[$i][3] = "1";
							$wdata[$i][4] = "1";
						}
						if($i % 2 == 0)
							$color="#dddddd";
						else
							$color="#cccccc";
						echo "<tr><td bgcolor=".$color." align=center>".$wdata[$i][0]."</td><td bgcolor=".$color.">".$wdata[$i][1]."</td>";
						if($wdata[$i][2] == "on")
							echo "<td bgcolor=".$color." align=center><input type='checkbox' name='data[".$i."][2]' checked>";
						else
							echo "<td bgcolor=".$color." align=center><input type='checkbox' name='data[".$i."][2]'>";
						switch($wdata[$i][3])
						{
							case 1:
								echo "<td bgcolor=".$color."><input type='RADIO' name='data[".$i."][3]' value=1 checked><font color= #006600>Manual</font><input type='RADIO' name='data[".$i."][3]' value=2><font color= #006600>Automatico</font></td>";
							break;
							case 2:
								echo "<td bgcolor=".$color."><input type='RADIO' name='data[".$i."][3]' value=1><font color= #006600>Manual</font><input type='RADIO' name='data[".$i."][3]' value=2 checked><font color= #006600>Automatico</font></td>";
							break;
						}
						switch($wdata[$i][4])
						{
							case 1:
								echo "<td bgcolor=".$color."><input type='RADIO' name='data[".$i."][4]' value=1 checked><font color= #006600>Procedimiento</font><input type='RADIO' name='data[".$i."][4]' value=2><font color= #006600>Mes</font><input type='RADIO' name='data[".$i."][4]' value=3><font color= #006600>Dia</font></td>";
							break;
							case 2:
								echo "<td bgcolor=".$color."><input type='RADIO' name='data[".$i."][4]' value=1><font color= #006600>Procedimiento</font><input type='RADIO' name='data[".$i."][4]' value=2 checked><font color= #006600>Mes</font><input type='RADIO' name='data[".$i."][4]' value=3><font color= #006600>Dia</font></td>";
							break;
							case 3:
								echo "<td bgcolor=".$color."><input type='RADIO' name='data[".$i."][4]' value=1><font color= #006600>Procedimiento</font><input type='RADIO' name='data[".$i."][4]' value=2><font color= #006600>Mes</font><input type='RADIO' name='data[".$i."][4]' value=3 checked><font color= #006600>Dia</font></td>";
							break;
						}
						echo "<input type='HIDDEN' name= 'data[".$i."][0]' value='".$wdata[$i][0]."'>";
						echo "</tr>";
					}
					echo "<td bgcolor=#cccccc colspan=5 align=center>DATOS OK!! <input type='checkbox' name='ok'></td><tr>";
					echo "<input type='HIDDEN' name= 'wanop' value='".$wanop."'>";
					echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
				}
				echo "<td bgcolor=#999999 colspan=8 align=center><input type='submit' value='ACTUALIZAR'></td><tr>";
				echo"</table>";
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
}
?>
</body>
</html>
