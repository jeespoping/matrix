<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Actualizacion de Inversiones Presupuestadas</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro124.php Ver. 2015-11-17</b></font></tr></td></table>
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
	echo "<form action='000001_pro124.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(isset($ok))
	{
		for ($i=0;$i<$num;$i++)
		{
			if(validar1($data[$i][3]) and validar2($data[$i][0]) and $data[$i][0] > 0 and $data[$i][0] < 13)
			{
				if($i < $num - 1)
				{
					$query = "update ".$empresa."_000019 set Invmes=".$data[$i][0].", Invcod='".substr($data[$i][2],0,2)."', Invdes='".$data[$i][4]."', Invmon=".$data[$i][3]." where Invano=".$wanop." and Invact='".$data[$i][1]."' and Invcco='".$wcco1."' and Invemp='".$wemp."'";
					$err = mysql_query($query,$conex) or die("Error en la Insercion");
				}
				else
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
	        		$query = "insert ".$empresa."_000019 (medico,fecha_data,hora_data,Invemp, Invano, Invmes, Invcco, Invact, Invcod, Invmon, Invvid, Invdes, Invest, Invtip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$data[$i][0].",'".$wcco1."','".$data[$i][1]."','".substr($data[$i][2],0,2)."',".$data[$i][3].",0,'".$data[$i][4]."',' ',' ','C-".$empresa."')";
					$err1 = mysql_query($query,$conex);
					if ($err1 != 1)
						echo mysql_errno().":".mysql_error()."<br>";
				}
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>ERROR EN LOS DATOS DEL ACTIVO ".$data[$i][1]."  REVISE !!!!!</MARQUEE></FONT>";
				echo "<br><br>";
			}
		}
		echo "<table border=0 align=center>";
		echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/clinica.jpg' ></td><td  align=center bgcolor=#cccccc><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>ACTUALIZACION DE INVERSIONES PRESUPUESTADAS</b></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>INVERSIONES ACTUALIZADAS</b></td></tr>";
		echo "<tr><td bgcolor=#cccccc colspan=8 align=center><input type='submit' value='CONTINUAR'></td></tr>";
		echo"</table>";
		echo "<input type='HIDDEN' name= 'wanop' value='".$wanop."'>";
		echo "<input type='HIDDEN' name= 'wcco1' value='".$wcco1."'>";
		unset($ok);
	}
	else
	{
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or (!isset($wcco1) and !isset($wccof)))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>ACTUALIZACION DE INVERSIONES PRESUPUESTADAS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Proceso</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT Ccocod ,Cconom  from ".$empresa."_000005 order by Ccocod";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wccof'>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
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
			if(isset($wccof))
			{
				$ini=strpos($wccof,"-");
				$wcco1=substr($wccof,0,$ini);
			}
			$wemp=substr($wemp,0,strpos($wemp,"-"));
			$query = "SELECT count(*) from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and Emp = '".$wemp."' ";
			$query = $query."    and Cierre_ppto =   'on' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($row[0] > 0)
			{
				$query = "SELECT Mgicod, Mginom  from ".$empresa."_000029 ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$tipo=array();
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$tipo[$i][0]=$row[0];
						$tipo[$i][1]=$row[1];
					}
				}
				$num2=$num;
				$query = "SELECT cconom from ".$empresa."_000005 ";
				$query = $query."  where ccocod = ".$wcco1;
				$query = $query."    and ccoemp = '".$wemp."' ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$row = mysql_fetch_array($err);
					$cconom=$row[0];
				}
				else
					$cconom="";
				$cconom = $wcco1."-".$cconom;
				echo "<table border=0 align=center>";
				echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/clinica.jpg' ></td><td  align=center bgcolor=#cccccc colspan=4><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
				echo "<tr><td bgcolor=#999999 colspan=5 align=center><b>ACTUALIZACION DE INVERSIONES PRESUPUESTADAS</b></td></tr>";
				echo "<tr><td bgcolor=#999999 colspan=5 align=center><b>UNIDAD : ".$cconom."</b></td></tr>";
				echo "<tr><td bgcolor=#CCCCCC align=center><b>MES </b></td><td bgcolor=#CCCCCC align=center><b>ACTIVO</b></td><td bgcolor=#CCCCCC align=center><b>TIPO</b></td><td bgcolor=#CCCCCC align=center><b>MONTO </b></td><td bgcolor=#CCCCCC align=center><b>DESCRIPCION </b></td></tr>";
				$query = "SELECT  Invmes,  Invact, Invcod, Invmon, Invdes  from ".$empresa."_000019 ";
				$query = $query."  where Invano = ".$wanop;
				$query = $query."    and Invemp = '".$wemp."' ";
				$query = $query."    and Invcco = '".$wcco1."'";
				$query = $query." order by Invact";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$data=array();
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$data[$i][0]=$row[0];
						$data[$i][1]=$row[1];
						$data[$i][2]=$row[2];
						$data[$i][3]=$row[3];
						$data[$i][4]=$row[4];
						if($i % 2 == 0)
							$color="#dddddd";
						else
							$color="#cccccc";
						echo "<tr><td bgcolor=".$color." align=center><input type='TEXT' name='data[".$i."][0]' size=2 maxlength=2 value=".$data[$i][0]."></td><td bgcolor=".$color." align=center>".$data[$i][1]."</td>";
						echo "<td bgcolor=".$color." align=center><select name='data[".$i."][2]'>";
						for ($j=0;$j<$num2;$j++)
						{
							$row2 = mysql_fetch_array($err2);
							if($data[$i][2] == $tipo[$j][0])
								echo "<option selected>".$tipo[$j][0]."_".$tipo[$j][1]."</option>";
							else
								echo "<option>".$tipo[$j][0]."_".$tipo[$j][1]."</option>";
						}
						echo "</select></td>";
						echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$i."][3]' size=12 maxlength=12 value=".number_format((double)$data[$i][3],2,'.','')."></td><td bgcolor=".$color."><textarea name='data[".$i."][4]' cols=40 rows=3>".$data[$i][4]."</textarea></tr>";
					}
					$fijo=substr($data[$num-1][1],0,5);
					$var=(integer)substr($data[$num-1][1],5,3);
					$var++;
					while(strlen($var) < 3)
						$var = "0".$var;
					$data[$num][1]=$fijo.$var;
					if($num % 2 == 0)
						$color="#dddddd";
					else
						$color="#cccccc";
					echo "<tr><td bgcolor=".$color." align=center><input type='TEXT' name='data[".$num."][0]' size=2 maxlength=2></td><td bgcolor=".$color." align=center>".$data[$num][1]."</td>";
					echo "<td bgcolor=".$color." align=center><select name='data[".$num."][2]'>";
					for ($j=0;$j<$num2;$j++)
					{
						$row2 = mysql_fetch_array($err2);
						echo "<option>".$tipo[$j][0]."_".$tipo[$j][1]."</option>";
					}
					echo "</select></td>";
					echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$num."][3]' size=12 maxlength=12></td><td bgcolor=".$color."><textarea name='data[".$num."][4]' cols=40 rows=3></textarea></tr>";
					$num++;
					echo "<tr><td bgcolor=#cccccc colspan=5 align=center>DATOS OK!! <input type='checkbox' name='ok'></td></tr>";
					echo "<input type='HIDDEN' name= 'wanop' value='".$wanop."'>";
					echo "<input type='HIDDEN' name= 'wcco1' value='".$wcco1."'>";
					echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
					for ($i=0;$i<$num;$i++)
						echo "<input type='HIDDEN' name= 'data[".$i."][1]' value='".$data[$i][1]."'>";
				}
				else
				{
					$fijo="P".$wcco1;
					$data[$num][1]=$fijo."001";
					if($num % 2 == 0)
						$color="#dddddd";
					else
						$color="#cccccc";
					echo "<tr><td bgcolor=".$color." align=center><input type='TEXT' name='data[".$num."][0]' size=2 maxlength=2></td><td bgcolor=".$color." align=center>".$data[$num][1]."</td>";
					echo "<td bgcolor=".$color." align=center><select name='data[".$num."][2]'>";
					for ($j=0;$j<$num2;$j++)
					{
						$row2 = mysql_fetch_array($err2);
						echo "<option>".$tipo[$j][0]."_".$tipo[$j][1]."</option>";
					}
					echo "</select></td>";
					echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$num."][3]' size=12 maxlength=12></td><td bgcolor=".$color."><textarea name='data[".$num."][4]' cols=40 rows=3></textarea></tr>";
					$num++;
					echo "<tr><td bgcolor=#cccccc colspan=5 align=center>DATOS OK!! <input type='checkbox' name='ok'></td></tr>";
					echo "<input type='HIDDEN' name= 'wanop' value='".$wanop."'>";
					echo "<input type='HIDDEN' name= 'wcco1' value='".$wcco1."'>";
					echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
					for ($i=0;$i<$num;$i++)
						echo "<input type='HIDDEN' name= 'data[".$i."][1]' value='".$data[$i][1]."'>";
				}
				echo "<center><input type='HIDDEN' name= 'wemp' value='".$wemp."'>";
				echo "<tr><td bgcolor=#999999 colspan=5 align=center><input type='submit' value='ACTUALIZAR'></td><tr>";
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
