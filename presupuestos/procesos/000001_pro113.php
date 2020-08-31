<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Actualizacion Porcentajes de Crecimiento Costos y Gastos (T46) </font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro113.php Ver. 2015-09-07</b></font></tr></td></table>
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
	echo "<form action='000001_pro113.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(isset($ok))
	{
		for ($i=0;$i<$num;$i++)
		{
			if(validar1($data[$i][3]) and validar2($data[$i][4]))
			{
				$query = "update ".$empresa."_000046 set Icgpor =".$data[$i][3].",Icgper=".$data[$i][4].",Icgtip='".$data[$i][5]."'  where Icgano=".$wanop." and Icgcpr='".$data[$i][1]."' and Icgemp='".$wemp."'";
				$err = mysql_query($query,$conex) or die("Error en la Actualizacion");
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>ERROR EN LOS DATOS DEL PORCENTAJE ".$data[$i][3]." PERIODO ".$data[$i][4]."  REVISE  !!!!!</MARQUEE></FONT>";
				echo "<br><br>";
			}
		}
		echo "<table border=0 align=center>";
		echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/logo1.jpg' ></td><td  align=center bgcolor=#cccccc><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>ACTUALIZACION PORCENTAJES DE CRECIMIENTO COSTOS Y GASTOS</b></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>PORCENTAJES ACTUALIZADOS</b></td></tr>";
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
			echo "<tr><td align=center colspan=2>ACTUALIZACION PORCENTAJES DE CRECIMIENTO COSTOS Y GASTOS (T46)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Presupuestaci&oacute;n</td>";
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
				echo "<tr><td bgcolor=#999999 colspan=8 align=center><b>ACTUALIZACION PORCENTAJES DE CRECIMIENTO COSTOS Y GASTOS</b></td></tr>";
				echo "<tr><td bgcolor=#CCCCCC align=center><b>A&Ntilde;O</b></td><td bgcolor=#CCCCCC align=center><b>CODIGO</b></td><td bgcolor=#CCCCCC align=center><b>DESCRIPCION</b></td><td bgcolor=#CCCCCC align=rigth><b>% INCREMENTO</b></td><td bgcolor=#CCCCCC align=center><b>MES INCREMENTO</b></td><td bgcolor=#CCCCCC align=center><b>CENTRALIZADO</b></td></tr>";
				$query = "select Mgacod, Mganom  from ".$empresa."_000028 ";
				$query = $query."  where (Mgacod >= '200' ";
				$query = $query." 	 and Mgacod <= '299') " ;
				$query = $query."         or (Mgacod >= '400' ";
				$query = $query." 	 and Mgacod <= '699') " ;
				$query = $query."  order by Mgacod ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000046 (medico,fecha_data,hora_data, Icgemp, Icgano, Icgcpr, Icgnom, Icgpor, Icgper, Icgtip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",'".$row[0]."','".$row[1]."',0,1,'N','C-".$empresa."')";
						$err2 = mysql_query($query,$conex);
					}
				}
				$query = "SELECT Icgano,Icgcpr, Icgnom, Icgpor, Icgper,Icgtip  from ".$empresa."_000046 ";
				$query = $query."  where Icgano = ".$wanop;
				$query = $query."    and Icgemp = '".$wemp."'";
				$query = $query." order by Icgcpr";
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
						if($i % 2 == 0)
							$color="#dddddd";
						else
							$color="#cccccc";
						echo "<tr><td bgcolor=".$color." align=center>".$row[0]."</td><td bgcolor=".$color." align=center>".$row[1]."</td><td bgcolor=".$color.">".$row[2]."</td><td bgcolor=".$color." align=center><input type='TEXT' name='data[".$i."][3]' size=6 maxlength=6 value=".number_format((double)$row[3],2,'.',',')."></td><td bgcolor=".$color." align=center><input type='TEXT' name='data[".$i."][4]' size=2 maxlength=2 value=".number_format((double)$row[4],0,'.',',')."></td>";
						if($row[5] == 'S')
							echo "<td bgcolor=".$color."><input type='RADIO' name='data[".$i."][5]' value=S checked><font color= #006600>CENTRALIZADO</font><input type='RADIO' name='data[".$i."][5]' value=N><font color= #FF0000> NO CENTRALIZADO</font></td></tr>";
						else
							echo "<td bgcolor=".$color."><input type='RADIO' name='data[".$i."][5]' value=S><font color= #006600> CENTRALIZADO</font><input type='RADIO' name='data[".$i."][5]' value=N checked><font color= #FF0000>  NO CENTRALIZADO</font></td></tr>";
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
