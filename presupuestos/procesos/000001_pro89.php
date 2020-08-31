<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Actualizacion de porcentajes de Empresas x Escenario (Presupuestacion T6)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro89.php Ver. 2013-09-18</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function validar($chain)
{
	$decimal ="^(\+|-)?([[:digit:]]+)\.([[:digit:]]+)$";
	if (ereg($decimal,$chain,$occur))
		if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			return false;
		else
			return true;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro89.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(isset($ok))
	{
		for ($i=0;$i<$num;$i++)
		{
			if(validar($data[$i][1]) and validar($data[$i][2]) and validar($data[$i][3]))
			{
				$query = "update ".$empresa."_000006 set Piaes1=".$data[$i][1].", Piaes2=".$data[$i][2].", Piaes3=".$data[$i][3]." where Piaano=".$wanop." and Piapos=".$data[$i][0]." and Piaemp='".$wemp."'";
				$err = mysql_query($query,$conex) or die("Error en la Insercion");
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>ERROR EN LOS DATOS DE LA EMPRESA ".$data[$i][4]."  REVISE !!!!!</MARQUEE></FONT>";
				echo "<br><br>";
			}
		}
		echo "<table border=0 align=center>";
		echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/logo1.jpg' ></td><td  align=center bgcolor=#cccccc><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>ACTUALIZACION DE PORCENTAJES DE EMPRESAS X ESCENARIO</b></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>EMPRESAS ACTUALIZADAS</b></td></tr>";
		echo "<td bgcolor=#cccccc colspan=8 align=center><input type='submit' value='CONTINUAR'></td><tr>";
		echo"</table>";
		unset($ok);
		unset($wanop);
		unset($wemp);
	}
	else
	{
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>ACTUALIZACION DE PORCENTAJES DE EMPRESAS X ESCENARIO (PRESUPUESTACION T6)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Presupuestacion</td>";
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
			echo "<center><input type='HIDDEN' name= 'wemp' value='".$wemp."'>";
			$query = "SELECT count(*) from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and Cierre_ppto =   'on' ";
			$query = $query."    and emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($row[0] > 0)
			{
				echo "<table border=0 align=center>";
				echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/logo1.jpg' ></td><td  align=center bgcolor=#cccccc colspan=7><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
				echo "<tr><td bgcolor=#999999 colspan=8 align=center><b>ACTUALIZACION DE PORCENTAJES DE EMPRESAS X ESCENARIO</b></td></tr>";
				echo "<tr><td bgcolor=#CCCCCC align=center><b>POSICION </b></td><td bgcolor=#CCCCCC align=center><b>NIT </b></td><td bgcolor=#CCCCCC><b>EMPRESA </b></td><td bgcolor=#CCCCCC align=center><b>SEGMENTO </b></td><td bgcolor=#CCCCCC align=center><b>% PARTICIPACION </b></td><td bgcolor=#00FF00 align=center><b>ESCENARIO<BR>ALTO </b></td><td bgcolor=#FFFF00 align=center><b>ESCENARIO<BR>MEDIO </b></td><td bgcolor=#FF0000 align=center><b>ESCENARIO<BR> BAJO</b></td></tr>";
				$query = "SELECT Piapos, Piacin, Piades, Piaseg, Piappa,  Piaes1, Piaes2, Piaes3 from ".$empresa."_000006 ";
				$query = $query."  where Piaano = ".$wanop;
				$query = $query."    and Piaemp = '".$wemp."'";
				$query = $query." order by Piapos";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$data=array();
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$data[$i][0]=$row[0];
						$data[$i][4]=$row[2];
						$query = "SELECT Empnit from ".$empresa."_000061 ";
						$query = $query."  where Empcin = '".$row[1]."'";
						$query = $query."    and Empemp = '".$wemp."'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						if($num1 > 0)
						{
							$row1 = mysql_fetch_array($err1);
							$nit=$row1[0];
						}
						else
							$nit=$row[1];
						if($i % 2 == 0)
							$color="#dddddd";
						else
							$color="#cccccc";
						echo "<tr><td bgcolor=".$color." align=center>".$row[0]."</td><td bgcolor=".$color." align=center>".$nit."</td><td bgcolor=".$color.">".$row[2]."</td><td bgcolor=".$color." align=center>".$row[3]."</td><td bgcolor=".$color." align=center>".number_format((double)$row[4],2,'.',',')."</td><td bgcolor=#00FF00 align=center><input type='TEXT' name='data[".$i."][1]' size=4 maxlength=4 value=".number_format((double)$row[5],2,'.',',')."></td><td bgcolor=#FFFF00 align=center><input type='TEXT' name='data[".$i."][2]' size=4 maxlength=4 value=".number_format((double)$row[6],2,'.',',')."></td><td bgcolor=#FF0000 align=center><input type='TEXT' name='data[".$i."][3]' size=4 maxlength=4 value=".number_format((double)$row[7],2,'.',',')."></td></tr>";
					}
					echo "<td bgcolor=#cccccc colspan=8 align=center>DATOS OK!! <input type='checkbox' name='ok'></td><tr>";
					echo "<input type='HIDDEN' name= 'wanop' value='".$wanop."'>";
					echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
					for ($i=0;$i<$num;$i++)
					{
						echo "<input type='HIDDEN' name= 'data[".$i."][0]' value='".$data[$i][0]."'>";
						echo "<input type='HIDDEN' name= 'data[".$i."][4]' value='".$data[$i][4]."'>";
					}
				}
				echo "<td bgcolor=#999999 colspan=8 align=center><input type='submit' value='ACTUALIZAR'></td><tr>";
				echo"</table>";
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>EL PROCESO DE PRESUPUESTACION YA ESTA CERRADO</MARQUEE></FONT>";
				echo "<br><br>";
			}
		}
	}
}
?>
</body>
</html>
