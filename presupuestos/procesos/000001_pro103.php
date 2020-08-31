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
				if ((event.keyCode < 48 || event.keyCode > 57  || event.keyCode == 13) && event.keyCode != 45 && event.keyCode != 46 && event.keyCode != 8) event.returnValue = false;
			}
			else
			{
				return ((e.which >= 48 && e.which <= 57) || e.which == 45 || e.which == 46 || e.which == 0 || e.which == 8);
			}
		}
	</script>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Definicion Condiciones Crecimiento Ingresos Unidades sin Procedimientos Asociados (T25)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro103.php Ver. 2015-11-06</b></font></tr></td></table>
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
	echo "<form action='000001_pro103.php' method=post>";
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(isset($ok))
	{
		$query = "DELETE from ".$empresa."_000025 ";
		$query = $query." where Cipano = ".$wanop;
		$query = $query."   and Cipemp = '".$wemp."' ";
		$err = mysql_query($query,$conex) or die("ERROR EN BORRADO T25");
		for ($i=0;$i<$num;$i++)
		{
			if(validar1($data[$i][4]) and validar2($data[$i][5]) and validar2($data[$i][6]) and validar2($data[$i][7]) and validar2($data[$i][8]) and validar2($data[$i][9]))
			{
				if($data[$i][2] == "0")
					$wtin="on";
				else
					$wtin="off";
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$empresa = "costosyp";
				$query = "insert ".$empresa."_000025 (medico, fecha_data, Hora_data,Cipemp, Cipano, Cipcco, Cipinp, Ciptin, Cipuni, Cipini, Cipper, Cipmax, Cipinc, Cipene, Cipdic, Seguridad) values ('";
				$query .=  $empresa."','";
				$query .=  $fecha."','";
				$query .=  $hora."','";
				$query .=  $wemp."','";
				$query .=  $wanop."','";
				$query .=  $data[$i][0]."',";
				$query .=  $data[$i][1].",'";
				$query .=  $wtin."','";
				$query .=  $data[$i][3]."',";
				$query .=  $data[$i][4].",";
				$query .=  $data[$i][5].",";
				$query .=  $data[$i][6].",";
				$query .=  $data[$i][7].",";
				$query .=  $data[$i][8].",";
				$query .=  $data[$i][9].",";
				$query .=  "'C-".$empresa."')";
				$err2 = mysql_query($query,$conex) or die("ERROR GRABANDO ".$empresa."_000025: ".mysql_errno().":".mysql_error());
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>ERROR EN LOS DATOS REVISE  !!!!!</MARQUEE></FONT>";
				echo "<br><br>";
			}
		}
		echo "<table border=0 align=center>";
		echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/logo1.jpg' ></td><td  align=center bgcolor=#cccccc><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>DEFINICION CONDICIONES CRECIMIENTO INGRESOS UNIDADES SIN PROCEDIMIENTOS ASOCIADOS (T25)</b></td></tr>";
		echo "<tr><td bgcolor=#999999 colspan=2 align=center><b>DATOS ACTUALIZADOS</b></td></tr>";
		echo "<td bgcolor=#cccccc colspan=8 align=center><input type='submit' value='CONTINUAR'></td><tr>";
		echo"</table>";
		unset($ok);
		unset($wanop);
		unset($wanob);
	}
	else
	{
		if(!isset($wanop) or !isset($wanob) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>DEFINICION CONDICIONES CRECIMIENTO INGRESOS UNIDADES SIN PROCEDIMIENTOS ASOCIADOS (T25)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Presupuestacion</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o Base de Presupuestacion</td>";
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
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and mes = 0 ";
			$query = $query."    and Emp = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "on")
			{
				$CRE=array();
				$query  = "SELECT Cipcco,Ciptin,Cipini,Cipper,Cipmax,Cipinc,Cipene,Cipdic from ".$empresa."_000025 ";
				$query .= "  where Cipano = ".$wanop;
				$query .= "    and Cipemp = '".$wemp."' ";
				$query .= "   Order by 1 ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$CRE[$i][0]=$row[0];
						$CRE[$i][1]=$row[1];
						$CRE[$i][2]=$row[2];
						$CRE[$i][3]=$row[3];
						$CRE[$i][4]=$row[4];
						$CRE[$i][5]=$row[5];
						$CRE[$i][6]=$row[6];
						$CRE[$i][7]=$row[7];
					}
				}
				$numcre=$num;
				echo "<table border=0 align=center>";
				echo "<tr><td bgcolor=#999999 align=center><IMG SRC='/MATRIX/images/medical/root/logo1.jpg' ></td><td  align=center bgcolor=#cccccc colspan=9><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
				echo "<tr><td bgcolor=#999999 colspan=10 align=center><b>DEFINICION CONDICIONES CRECIMIENTO INGRESOS UNIDADES SIN PROCEDIMIENTOS ASOCIADOS (T25)</b></td></tr>";
				echo "<tr><td bgcolor=#CCCCCC align=center><b>C.C.</b></td><td bgcolor=#CCCCCC align=center><b>INGRESO<br>PROMEDIO</b></td><td bgcolor=#CCCCCC align=center><b>TIPO INCREMENTO</b></td><td bgcolor=#CCCCCC align=center><b>UNIDAD DE MEDIDA</b></td><td bgcolor=#CCCCCC align=center><b>% INCREMENTO<Br>/PESOS INICIALES</b></td><td bgcolor=#CCCCCC align=center><b>MES DE INICIO</b></td><td bgcolor=#CCCCCC align=center><b>PESOS<BR>MAXIMOS<br>X MES</b></td><td bgcolor=#CCCCCC align=center><b>INC. MENSUAL<BR>PESOS</b></td><td bgcolor=#CCCCCC align=center><b>AJUSTE<BR>ENERO</b></td><td bgcolor=#CCCCCC align=center><b>AJUSTE<BR>DICIEMBRE</b></td></tr>";
				//                 0        1       2       3       4       5       6       7      8       9       10
				$query = "SELECT Cpicco, Cpiuni, Iplinp  from ".$empresa."_000123,".$empresa."_000014 ";
				$query .= "  where Cpiano = ".$wanop;
				$query .= "    and Cpiemp = '".$wemp."' ";
				$query .= "    and Cpipre = 'on' ";
				$query .= "    and Cpical = '2' ";
				$query .= "    and Cpiuni in ('2','3') ";
				$query .= "    and Cpicco = Iplcco";
				$query .= "    and Cpiemp = Iplemp";
				$query .= "    and Iplano = ".$wanob;
				$query .= " order by Cpicco ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					$data=array();
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$pos=bi($CRE,$numcre,$row[0]);
						if($pos != -1)
						{
							$data[$i][0]=$row[0];
							$data[$i][1]=$row[2];
							$data[$i][2]=$CRE[$pos][1];
							$data[$i][3]=$row[1];
							$data[$i][4]=$CRE[$pos][2];
							$data[$i][5]=$CRE[$pos][3];
							$data[$i][6]=$CRE[$pos][4];
							$data[$i][7]=$CRE[$pos][5];
							$data[$i][8]=$CRE[$pos][6];
							$data[$i][9]=$CRE[$pos][7];
						}
						else
						{
							$data[$i][0]=$row[0];
							$data[$i][1]=$row[2];
							$data[$i][2]="on";
							$data[$i][3]=$row[1];
							$data[$i][4]=0;
							$data[$i][5]=0;
							$data[$i][6]=0;
							$data[$i][7]=0;
							$data[$i][8]=0;
							$data[$i][9]=0;
						}
						if($i % 2 == 0)
							$color="#dddddd";
						else
							$color="#cccccc";
						echo "<tr><td bgcolor=".$color." align=center>".$data[$i][0]."</td><td bgcolor=".$color." align=right>".number_format((double)$data[$i][1],2,'.',',')."</td>";
						if($data[$i][2] == "on")
							echo "<td bgcolor=".$color." align=center><input type='radio' name='data[".$i."][2]' value=0 checked>Porcentaje<input type='radio' name='data[".$i."][2]' value=1>Pesos</td>";
						else
							echo "<td bgcolor=".$color." align=center><input type='radio' name='data[".$i."][2]' value=0>Porcentaje<input type='radio' name='data[".$i."][2]' value=1 checked>Pesos</td>";
						if($data[$i][3] == "2")
							echo "<td bgcolor=".$color." align=center>Mes</td>";
						else
							echo "<td bgcolor=".$color." align=center>Dia</td>";
						echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$i."][4]' size=10 maxlength=12 value=".number_format((double)$data[$i][4],2,'.','')." onkeypress='return teclado(event)'></td>";
						echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$i."][5]' size=10 maxlength=12 value=".number_format((double)$data[$i][5],0,'.','')." onkeypress='return teclado(event)'></td>";
						echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$i."][6]' size=10 maxlength=12 value=".number_format((double)$data[$i][6],0,'.','')." onkeypress='return teclado(event)'></td>";
						echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$i."][7]' size=10 maxlength=12 value=".number_format((double)$data[$i][7],0,'.','')." onkeypress='return teclado(event)'></td>";
						echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$i."][8]' size=10 maxlength=12 value=".number_format((double)$data[$i][8],0,'.','')." onkeypress='return teclado(event)'></td>";
						echo "<td bgcolor=".$color." align=center><input type='TEXT' name='data[".$i."][9]' size=10 maxlength=12 value=".number_format((double)$data[$i][9],0,'.','')." onkeypress='return teclado(event)'></td></tr>";
					}
					echo "<td bgcolor=#cccccc colspan=11 align=center>DATOS OK!! <input type='checkbox' name='ok'></td><tr>";
					echo "<input type='HIDDEN' name= 'wanop' value='".$wanop."'>";
					echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
					for ($i=0;$i<$num;$i++)
					{
						echo "<input type='HIDDEN' name= 'data[".$i."][0]' value='".$data[$i][0]."'>";
						echo "<input type='HIDDEN' name= 'data[".$i."][1]' value='".$data[$i][1]."'>";
						echo "<input type='HIDDEN' name= 'data[".$i."][3]' value='".$data[$i][3]."'>";
					}
				}
				echo "<center><input type='HIDDEN' name= 'wemp' value='".$wemp."'>";
				echo "<td bgcolor=#999999 colspan=11 align=center><input type='submit' value='ACTUALIZAR'></td><tr>";
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
