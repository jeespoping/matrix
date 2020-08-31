<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Actualizacion de Opciones de Autollenado de Numero de Procedimientos (T22)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro98.php Ver. 2015-11-06</b></font></tr></td></table>
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
	echo "<form action='000001_pro98.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(isset($ok))
	{
		for ($i=0;$i<$num;$i++)
		{
			$query = "update ".$empresa."_000022 set Ocntip =".$data[$i][3]." where Ocnano=".$wanop." and Ocncco='".$data[$i][0]."' and  Ocncod='".$data[$i][1]."' and  Ocnemp='".$wemp."'";
			$err = mysql_query($query,$conex) or die("Error en la Actualizacion");
		}
		echo "<table border=0 align=center>";
		echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/logo1.jpg' ></td><td  align=center bgcolor=#cccccc><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>ACTUALIZACION DE OPCIONES DE AUTOLLENADO DE NUMERO DE PROCEDIMIENTOS (T22)</b></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>CRECIMIENTOS ACTUALIZADOS</b></td></tr>";
		echo "<td bgcolor=#cccccc colspan=8 align=center><input type='submit' value='CONTINUAR'></td><tr>";
		echo"</table>";
		unset($ok);
		unset($wanop);
	}
	else
	{
		if(!isset($wanop) or !isset($wanob) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>ACTUALIZACION DE OPCIONES DE AUTOLLENADO DE NUMERO DE PROCEDIMIENTOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Base</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Presupuestacion</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanob' size=4 maxlength=4></td></tr>";
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
			$query = "SELECT Cierre_Ppto from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanob;
			$query = $query."    and mes = 0 ";
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
				$meses=array();
				$meses[1]="ENERO";
				$meses[2]="FEBRERO";
				$meses[3]="MARZO";
				$meses[4]="ABRIL";
				$meses[5]="MAYO";
				$meses[6]="JUNIO";
				$meses[7]="JULIO";
				$meses[8]="AGOSTO";
				$meses[9]="SEPTIEMBRE";
				$meses[10]="OCTUBRE";
				$meses[11]="NOVIEMBRE";
				$meses[12]="DICIEMBRE";
				echo "<table border=0 align=center>";
				echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/logo1.jpg' ></td><td  align=center bgcolor=#cccccc colspan=7><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
				echo "<tr><td bgcolor=#999999 colspan=8 align=center><b>ACTUALIZACION DE OPCIONES DE AUTOLLENADO DE NUMERO DE PROCEDIMIENTOS</b></td></tr>";
				echo "<tr><td bgcolor=#CCCCCC align=center><b>C.C.</b></td><td bgcolor=#CCCCCC align=center><b>PROCEDIMIENTO</b></td><td bgcolor=#CCCCCC align=center><b>ACTUALIZADO A</b></td><td bgcolor=#CCCCCC align=center><b>TIPO DE CRECIMIENTO </b></td></tr>";
				#$query = "delete  from ".$empresa."_000022 ";
				#$query = $query."  where Ocnano = ".$wanop;
				#$err = mysql_query($query,$conex);
				$query = "select Morcco,Morcod,max(Mormes) from ".$empresa."_000032,".$empresa."_000005 ";
				$query = $query."  where Morano = ".$wanop;
				$query = $query."    and Moremp = '".$wemp."' ";
				$query = $query."    and Morcco = ccocod ";
				$query = $query."    and Moremp = ccoemp ";
				$query = $query."    and Mortip = 'P'";
				$query = $query."    and ccouni != '2H'  ";
				$query = $query."  group by Morcco,Morcod "; 
				$query = $query."  order by Morcco,Morcod ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000022 (medico,fecha_data,hora_data,Ocnemp, Ocnano, Ocncco, Ocncod, Ocnmes, Ocntip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",'".$row[0]."','".$row[1]."',".$row[2].",'1','C-".$empresa."')";
						$err2 = mysql_query($query,$conex);
						if ($err2 != 1)
						{
							$query = "update ".$empresa."_000022 set Ocnmes =".$row[2]." where Ocnano=".$wanop." and Ocncco='".$row[0]."' and  Ocncod='".$row[1]."' and  Ocnemp='".$wemp."'";
							$err1 = mysql_query($query,$conex) or die("Error en la Actualizacion");
						}
					}
				}
				$query = "SELECT Ocncco, Ocncod, Ocnmes, Ocntip  from ".$empresa."_000022 ";
				$query = $query."  where Ocnano = ".$wanop;
				$query = $query."    and Ocnemp = '".$wemp."' ";
				$query = $query." order by Ocncco, Ocncod";
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
						$query = "SELECT Prodes  from ".$empresa."_000059 ";
						$query = $query."  where Procod = '".$row[1]."'";
						$query = $query."    and Proemp = '".$wemp."' ";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						if($num1 > 0)
						{
							$row1 = mysql_fetch_array($err1);
							$lin=$row1[0];
						}
						else
							$lin=$row[1];
						if($i % 2 == 0)
							$color="#dddddd";
						else
							$color="#cccccc";
						echo "<tr><td bgcolor=".$color." align=center>".$row[0]."</td><td bgcolor=".$color.">".$row[1]."-".$lin."</td><td bgcolor=".$color." align=center>".$meses[$row[2]]."</td>";
						switch($row[3])
						{
							case 0:
								echo "<td bgcolor=".$color."><input type='RADIO' name='data[".$i."][3]' value=0 checked><font color= #006600> NO PRESUPUESTAR</font><input type='RADIO' name='data[".$i."][3]' value=1><font color= #006600> PROMEDIO PONDERADO</font><input type='RADIO' name='data[".$i."][3]' value=2><font color= #FF0000> TASA DE CRECIMIENTO</font></td></tr>";
							break;
							case 1:
								echo "<td bgcolor=".$color."><input type='RADIO' name='data[".$i."][3]' value=0><font color= #006600> NO PRESUPUESTAR</font><input type='RADIO' name='data[".$i."][3]' value=1 checked><font color= #006600> PROMEDIO PONDERADO</font><input type='RADIO' name='data[".$i."][3]' value=2><font color= #FF0000> TASA DE CRECIMIENTO</font></td></tr>";
							break;
							case 2:
								echo "<td bgcolor=".$color."><input type='RADIO' name='data[".$i."][3]' value=0><font color= #006600> NO PRESUPUESTAR</font><input type='RADIO' name='data[".$i."][3]' value=1><font color= #006600> PROMEDIO PONDERADO</font><input type='RADIO' name='data[".$i."][3]' value=2 checked><font color= #FF0000> TASA DE CRECIMIENTO</font></td></tr>";
							break;
						}
					}
					echo "<td bgcolor=#cccccc colspan=8 align=center>DATOS OK!! <input type='checkbox' name='ok'></td><tr>";
					echo "<input type='HIDDEN' name= 'wanop' value='".$wanop."'>";
					echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
					for ($i=0;$i<$num;$i++)
					{
						echo "<input type='HIDDEN' name= 'data[".$i."][0]' value='".$data[$i][0]."'>";
						echo "<input type='HIDDEN' name= 'data[".$i."][1]' value='".$data[$i][1]."'>";
						echo "<input type='HIDDEN' name= 'data[".$i."][2]' value='".$data[$i][2]."'>";
					}
				}
				echo "<center><input type='HIDDEN' name= 'wemp' value='".$wemp."'>";
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
