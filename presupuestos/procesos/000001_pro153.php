<html>
<head>
  <title>MATRIX</title>
  <script type="text/javascript">

  function teclado(e)  
	{ 
		var navegador = navigator.appName;
		var version = navigator.appVersion;
		if(navegador.substring(0,9) == "Microsoft")
		{
			if (event.keyCode < 48 || event.keyCode > 57  || event.keyCode == 13)  event.returnValue = false;
		}
		else
		{
			return (e.which >= 48 && e.which <= 57  || e.which == 13 || e.which < 32); //Solo números
		}
	}
</script>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Numero de Camas e Indice de Ocupacion a Presupuestar x Unidad (T31)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro153.php Ver. 2015-11-06</b></font></tr></td></table>
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
	echo "<form action='000001_pro153.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(isset($ok))
	{
		$query = "delete  from ".$empresa."_000031 ";
		$query = $query."  where Mopano = ".$wanop;
		$query = $query."    and Mopemp = '".$wemp."' ";
		$query = $query."    and Mopcod in ('27','34') ";
		$err = mysql_query($query,$conex);
		for ($i=0;$i<$num;$i++)
		{
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			for ($w=2;$w<14;$w++)
			{
				if($data[$i][$w] == "")
					$data[$i][$w]=0;
				$wmes=$w - 1;
				$query = "insert ".$empresa."_000031 (medico,fecha_data,hora_data,Mopemp, Mopano, Mopmes, Mopcco, Mopcod, Mopcan, Moptip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wmes.",'".$data[$i][0]."','34',".$data[$i][$w].",'H','C-".$empresa."')";
				$err = mysql_query($query,$conex) or die("Error en la Actualizacion ".mysql_errno().":".mysql_error());
			}
			for ($w=14;$w<26;$w++)
			{
				if($data[$i][$w] == "")
					$data[$i][$w]=0;
				$wmes=$w - 13;
				$query = "insert ".$empresa."_000031 (medico,fecha_data,hora_data,Mopemp, Mopano, Mopmes, Mopcco, Mopcod, Mopcan, Moptip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wmes.",'".$data[$i][0]."','27',".$data[$i][$w].",'H','C-".$empresa."')";
				$err = mysql_query($query,$conex) or die("Error en la Actualizacion ".mysql_errno().":".mysql_error());
			}
		}
		echo "<table border=0 align=center>";
		echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/logo1.jpg' ></td><td  align=center bgcolor=#cccccc><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>NUMERO DE CAMAS E INDICE DE OCUPACION A PRESUPUESTAR X UNIDAD (T31)</b></td></tr>";
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
			echo "<tr><td align=center colspan=2>NUMERO DE CAMAS E INDICE DE OCUPACION A PRESUPUESTAR X UNIDAD (T31)</td></tr>";
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
			#INICIO PROGRAMA
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
				$query = "SELECT Ccocod,Cconom from ".$empresa."_000005 ";
				$query = $query."  where Ccouni = '2H' ";
				$query = $query."    and Ccoest = 'on' ";
				$query = $query."    and Ccoemp = '".$wemp."' ";
				$query = $query." order by Ccocod ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$val=array();
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						for ($j=1;$j<13;$j++)
						{
							$val[$row[0]][$j][27]=0;
							$val[$row[0]][$j][34]=0;
						}
					}
				}
				//                  0     1      2      3
				$query = "select Mopcco,Mopmes,Mopcod,Mopcan from ".$empresa."_000031 ";
				$query = $query."  where Mopano = ".$wanop;
				$query = $query."    and Mopemp = '".$wemp."' ";
				$query = $query."    and Mopcod in ('27','34') ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$val[$row[0]][$row[1]][$row[2]]=$row[3];
					}
				}
				echo "<table border=0 align=center>";
				echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/logo1.jpg' ></td><td  align=center bgcolor=#cccccc colspan=13><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
				echo "<tr><td bgcolor=#999999 colspan=14 align=center><b>NUMERO DE CAMAS E INDICE DE OCUPACION A PRESUPUESTAR X UNIDAD A&Ntilde;O ".$wanop."</b></td></tr>";
				echo "<tr><td bgcolor=#CCCCCC align=center><b>C.C.</b></td><td bgcolor=#CCCCCC align=center><b>DESCRIPCION</b></td><td bgcolor=#CCCCCC align=center><b>ENE</b></td><td bgcolor=#CCCCCC align=center><b>FEB</b></td><td bgcolor=#CCCCCC align=center><b>MAR</b></td><td bgcolor=#CCCCCC align=center><b>ABR</b></td><td bgcolor=#CCCCCC align=center><b>MAY</b></td><td bgcolor=#CCCCCC align=center><b>JUN</b></td><td bgcolor=#CCCCCC align=center><b>JUL</b></td><td bgcolor=#CCCCCC align=center><b>AGO</b></td><td bgcolor=#CCCCCC align=center><b>SEP</b></td><td bgcolor=#CCCCCC align=center><b>OCT</b></td><td bgcolor=#CCCCCC align=center><b>NOV</b></td><td bgcolor=#CCCCCC align=center><b>DIC</b></td></tr>";

				$query = "SELECT Ccocod,Cconom from ".$empresa."_000005 ";
				$query = $query."  where Ccouni = '2H' ";
				$query = $query."    and Ccoest = 'on' ";
				$query = $query."    and Ccoemp = '".$wemp."' ";
				$query = $query." order by Ccocod ";
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

						if($i % 2 == 0)
							$color="#C3D9FF";
						else
							$color="#E8EEF7";
						echo "<tr><td bgcolor=#00008B colspan=14 align=left><font color=#FFFFFF><b>".$row[0]."-".$row[1]."</b></font></td></tr>";
						echo "<tr><td bgcolor=".$color." colspan=2 align=left>Numero de Camas</td>";
						for ($w=0;$w<12;$w++)
						{
							$pos=$w + 2;
							echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$i."][".$pos."]' size=3 maxlength=3 value='".$val[$row[0]][$w+1][34]."' onkeypress='return teclado(event)'></td>";
						}
						echo "</tr><tr><td bgcolor=".$color." colspan=2 align=left>Porcentaje de Ocupacion</td>";
						for ($w=0;$w<12;$w++)
						{
							$pos=$w + 14;
							echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$i."][".$pos."]' size=3 maxlength=3 value='".$val[$row[0]][$w+1][27]."' onkeypress='return teclado(event)'></td>";
						}
						echo "</tr>";

					}
					echo "<td bgcolor=#cccccc colspan=14 align=center>DATOS OK!! <input type='checkbox' name='ok'></td><tr>";
					echo "<input type='HIDDEN' name= 'wanop' value='".$wanop."'>";
					echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
					for ($i=0;$i<$num;$i++)
					{
						echo "<input type='HIDDEN' name= 'data[".$i."][0]' value='".$data[$i][0]."'>";
						echo "<input type='HIDDEN' name= 'data[".$i."][1]' value='".$data[$i][1]."'>";
					}
				}
				echo "<center><input type='HIDDEN' name= 'wemp' value='".$wemp."'>";
				echo "<td bgcolor=#999999 colspan=14 align=center><input type='submit' value='ACTUALIZAR'></td><tr>";
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
