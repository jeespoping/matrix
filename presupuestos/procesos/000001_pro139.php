<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Actualizacion de Nits en Tabla de Explicaciones</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro139.php Ver. 2015-12-18</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function validar1($chain)
{
	$decimal ="^(\+|-)?([[:digit:]]+)\.([[:digit:]]+)$";
	if (ereg($decimal,$chain,$occur))
		if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			return false;
		else
			return true;
}
function validar2($chain)
{
	$regular="^(\+|-)?([[:digit:]]+)$";
	if (ereg($regular,$chain,$occur))
		if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			return false;
		else
			return true;
	else
		return false;
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro139.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(isset($ok))
	{
		for ($i=0;$i<$num;$i++)
		{
			if(isset($C[$i]))
			{
				$query = "update ".$empresa."_000011 set Expnit=".$wnitf." where Expano=".$wanop." and Expper=".$wmesp." and Expnit='".$wniti."' and Expexp='".$data[$i]."' and Expemp='".$wemp."' ";
				$err = mysql_query($query,$conex) or die("Error en la Actualizacion");
			}
		}
		echo "<table border=0 align=center>";
		echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/logo1.gif' ></td><td  align=center bgcolor=#cccccc><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>ACTUALIZACION DE NITS EN TABLA DE EXPLICACIONES</b></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>NITS ACTUALIZAD0S</b></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=8 align=center><input type='submit' value='CONTINUAR'></td></tr>";
		echo"</table>";
		unset($ok);
		unset($wanop);
	}
	else
	{
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wmesp) or !isset($wniti) or !isset($wnitf))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>ACTUALIZACION DE NITS EN TABLA DE EXPLICACIONES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmesp' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Nit Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wniti' size=15 maxlength=15></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Nit Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wnitf' size=15 maxlength=15></td></tr>";
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
			echo "<table border=0 align=center>";
			echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/logo1.gif' ></td><td  align=center bgcolor=#cccccc colspan=4><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
			echo "<tr><td bgcolor=#999999 colspan=5 align=center><b>ACTUALIZACION DE NITS EN TABLA DE EXPLICACIONES</b></td></tr>";
			echo "<tr><td bgcolor=#999999 colspan=5 align=center><b>A&Ntilde;O : ".$wanop." MES : ".$wmesp." NIT INICIAL : ".$wniti." NIT FINAL : ".$wnitf."</b></td></tr>";
			echo "<tr><td bgcolor=#CCCCCC align=center><b>EXPLICACION </b></td><td bgcolor=#CCCCCC align=center><b>SELECCIONAR</b></td></tr>";
			$query = "select expexp from ".$empresa."_000011 ";
			$query .= "where expano = ".$wanop;
			$query .= "  and expper = ".$wmesp;  
			$query .= "  and expemp = '".$wemp."' ";
			$query .= "  and expnit = '".$wniti."'"; 
			$query .= " group by 1 ";
			$err = mysql_query($query,$conex) or die("ERROR AL CONSULTAR TABLA 11");
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				$data=array();
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$data[$i]=$row[0];
					if($i % 2 == 0)
						$color="#99CCFF";
					else
						$color="#FFFFFF";
					echo "<tr><td bgcolor=".$color.">".$data[$i]."</td><td bgcolor=".$color." align=center><input type='checkbox' name='C[".$i."]'></td></tr>";
				}
				echo "<input type='HIDDEN' name= 'wanop' value='".$wanop."'>";
				echo "<input type='HIDDEN' name= 'wmesp' value='".$wmesp."'>";
				echo "<input type='HIDDEN' name= 'wniti' value='".$wniti."'>";
				echo "<input type='HIDDEN' name= 'wnitf' value='".$wnitf."'>";
				echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
				echo "<input type='HIDDEN' name= 'wemp' value='".$wemp."'>";
				for ($i=0;$i<$num;$i++)
					echo "<input type='HIDDEN' name= 'data[".$i."]' value='".$data[$i]."'>";
				echo "<tr><td bgcolor=#cccccc colspan=5 align=center>DATOS OK!! <input type='checkbox' name='ok'></td></tr>";
				echo "<tr><td bgcolor=#999999 colspan=5 align=center><input type='submit' value='ACTUALIZAR'></td><tr>";
				echo"</table>";
			}
		}
	}
}
?>
</body>
</html>
