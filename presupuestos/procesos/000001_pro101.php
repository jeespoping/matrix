<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Definicion Condiciones de Crecimiento de Numero de Procedimientos x Linea (T23)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro101.php Ver. 2015-11-06</b></font></tr></td></table>
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
	echo "<form action='000001_pro101.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(isset($ok))
	{
		for ($i=0;$i<$num;$i++)
		{
			if(validar1($data[$i][4]) and validar2($data[$i][5]) and validar2($data[$i][6]) and validar2($data[$i][7]))
			{
				if($data[$i][2] == "0")
					$wtin="on";
				else
					$wtin="off";
				if($data[$i][3] == "0")
					$wtpr="on";
				else
					$wtpr="off";	
				$query = "update ".$empresa."_000023 set Cnptin ='".$wtin."',Cnptpr ='".$wtpr."',Cnpini =".$data[$i][4].",Cnpper =".$data[$i][5].",Cnpmax =".$data[$i][6].",Cnpinc =".$data[$i][7].",Cnpene =".$data[$i][8].",Cnpdic =".$data[$i][9]." where Cnpano=".$wanop." and Cnpcco='".$data[$i][0]."' and  Cnpcod='".$data[$i][1]."' and  Cnpemp='".$wemp."'";
				$err = mysql_query($query,$conex) or die("Error en la Actualizacion");
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>ERROR EN LOS DATOS DEL C.C. ".$data[$i][0]." PROCEDIMIENTO ".$data[$i][1]."  REVISE  !!!!!</MARQUEE></FONT>";
				echo "<br><br>";
			}
		}
		echo "<table border=0 align=center>";
		echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/logo1.jpg' ></td><td  align=center bgcolor=#cccccc><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>DEFINICION CONDICIONES DE CRECIMIENTO DE NUMERO DE PROCEDIMIENTOS X LINEA (T23)</b></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>CRECIMIENTOS ACTUALIZADOS</b></td></tr>";
		echo "<td bgcolor=#cccccc colspan=8 align=center><input type='submit' value='CONTINUAR'></td><tr>";
		echo"</table>";
		unset($ok);
		unset($wanop);
	}
	else
	{
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>DEFINICION CONDICIONES DE CRECIMIENTO DE NUMERO DE PROCEDIMIENTOS X LINEA (T23)</td></tr>";
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
				echo "<table border=0 align=center>";
				echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/logo1.jpg' ></td><td  align=center bgcolor=#cccccc colspan=9><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
				echo "<tr><td bgcolor=#999999 colspan=10 align=center><b>DEFINICION CONDICIONES DE CRECIMIENTO DE NUMERO DE PROCEDIMIENTOS X LINEA (T23)</b></td></tr>";
				echo "<tr><td bgcolor=#CCCCCC align=center><b>C.C.</b></td><td bgcolor=#CCCCCC align=center><b>PROCEDIMIENTO</b></td><td bgcolor=#CCCCCC align=center><b>TIPO INCREMENTO</b></td><td bgcolor=#CCCCCC align=center><b>BASE PROYECCION</b></td><td bgcolor=#CCCCCC align=center><b>% INCREMENTO<Br>/PROC. INICIALES</b></td><td bgcolor=#CCCCCC align=center><b>MES DE INICIO</b></td><td bgcolor=#CCCCCC align=center><b>PROCEDIMIENTOS<BR>MAXIMOS<BR>ADICIONALES</b></td><td bgcolor=#CCCCCC align=center><b>INC. MENSUAL<BR>PROCEDIMIENTOS</b></td><td bgcolor=#CCCCCC align=center><b>AJUSTE<BR>ENERO</b></td><td bgcolor=#CCCCCC align=center><b>AJUSTE<BR>DICIEMBRE</b></td></tr>";
				$wanopa=$wanop - 1;
				$query = "select Ocncco,Ocncod from ".$empresa."_000022 ";
				$query = $query."  where Ocnano = ".$wanopa;
				$query = $query."    and Ocnemp = '".$wemp."' ";
				$query = $query."    and Ocntip != '0' ";
				$query = $query."  group by Ocncco,Ocncod "; 
				$query = $query."  order by Ocncco,Ocncod ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000023 (medico,fecha_data,hora_data,Cnpemp, Cnpano, Cnpcco, Cnpcod, Cnptin, Cnptpr, Cnpini, Cnpper, Cnpmax, Cnpinc, Cnpene, Cnpdic, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",'".$row[0]."','".$row[1]."','on','on',0,1,0,0,0,0,'C-".$empresa."')";
						$err2 = mysql_query($query,$conex);
					}
				}
				$query = "DELETE from ".$empresa."_000023 ";
				$query .= " where Cnpano = ".$wanop;
				$query .= "   and Cnpemp = '".$wemp."'";
				$query .= "   and Cnpcco IN (select Ocncco from ".$empresa."_000022 where Ocnano = ".$wanopa." and Ocnemp = '".$wemp."' and Ocncod = Cnpcod and Ocntip = '0') ";
				$err = mysql_query($query,$conex) or die("ERROR EN BORRADO T23");
				
				//                  0      1        2       3       4      5       6       7       8       9
				$query = "SELECT Cnpcco, Cnpcod, Cnptin, Cnptpr, Cnpini, Cnpper, Cnpmax, Cnpinc, Cnpene, Cnpdic  from ".$empresa."_000023 ";
				$query = $query."  where Cnpano = ".$wanop;
				$query = $query."    and Cnpemp = '".$wemp."' ";
				$query = $query." order by Cnpcco, Cnpcod ";
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
						$data[$i][5]=$row[5];
						$data[$i][6]=$row[6];
						$data[$i][7]=$row[7];
						$data[$i][8]=$row[8];
						$data[$i][9]=$row[9];
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
						echo "<tr><td bgcolor=".$color." align=center>".$row[0]."</td><td bgcolor=".$color.">".$row[1]."-".$lin."</td>";
						if($data[$i][2] == "on")
							echo "<td bgcolor=".$color." align=center><input type='radio' name='data[".$i."][2]' value=0 checked>Porcentaje<input type='radio' name='data[".$i."][2]' value=1>Procedimientos</td>";
						else
							echo "<td bgcolor=".$color." align=center><input type='radio' name='data[".$i."][2]' value=0>Porcentaje<input type='radio' name='data[".$i."][2]' value=1 checked>Procedimientos</td>";
						if($data[$i][3] == "on")
							echo "<td bgcolor=".$color." align=center><input type='radio' name='data[".$i."][3]' value=0 checked>Ciclo<input type='radio' name='data[".$i."][3]' value=1>Promedio</td>";
						else
							echo "<td bgcolor=".$color." align=center><input type='radio' name='data[".$i."][3]' value=0>Ciclo<input type='radio' name='data[".$i."][3]' value=1 checked>Promedio</td>";
						echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$i."][4]' size=7 maxlength=7 value=".number_format((double)$row[4],2,'.',',')."></td>";
						echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$i."][5]' size=4 maxlength=4 value=".number_format((double)$row[5],0,'.',',')."></td>";
						echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$i."][6]' size=4 maxlength=4 value=".number_format((double)$row[6],0,'.',',')."></td>";
						echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$i."][7]' size=4 maxlength=4 value=".number_format((double)$row[7],0,'.',',')."></td>";
						echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$i."][8]' size=6 maxlength=6 value=".number_format((double)$row[8],2,'.',',')."></td>";
						echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$i."][9]' size=6 maxlength=6 value=".number_format((double)$row[9],2,'.',',')."></td></tr>";
					}
					echo "<td bgcolor=#cccccc colspan=10 align=center>DATOS OK!! <input type='checkbox' name='ok'></td><tr>";
					echo "<input type='HIDDEN' name= 'wanop' value='".$wanop."'>";
					echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
					for ($i=0;$i<$num;$i++)
					{
						echo "<input type='HIDDEN' name= 'data[".$i."][0]' value='".$data[$i][0]."'>";
						echo "<input type='HIDDEN' name= 'data[".$i."][1]' value='".$data[$i][1]."'>";
					}
				}
				echo "<center><input type='HIDDEN' name= 'wemp' value='".$wemp."'>";
				echo "<td bgcolor=#999999 colspan=10 align=center><input type='submit' value='ACTUALIZAR'></td><tr>";
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
