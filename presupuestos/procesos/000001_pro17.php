<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Montaje de Movimiento de Explicaciones con Codigo Presupuestal</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro17.php Ver. 2017-03-03</b></font></tr></td></table>
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
	echo "<form action='000001_pro17.php' method=post>";
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<center><input type='HIDDEN' name= 'ODBC' value='".$ODBC."'>";
	

	

	if(!isset($wanop) or !isset($wper1) or !isset($wemp) or $wemp == "Seleccione")
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>MONTAJE DE MOVIMIENTO DE EXPLICACIONES CON CODIGO PRESUPUESTAL</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
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
		$NITS = array();
		$query = "SELECT query,Odbc from ".$empresa."_000049  ";
		$query = $query."  where codigo =  33";
		$query = $query."    and Empresa = '".$wemp."' ";
		$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla ".$empresa."_000049");
		$row = mysql_fetch_array($err);
		$ODBC = $row[1];
		$conex_o = odbc_connect($ODBC,'','');
		$query = $row[0];
		$err_o = odbc_do($conex_o,$query);
		$campos= odbc_num_fields($err_o);
		$nn = -1;
		$k=0;
		echo "<B>REGISTROS INCONSISTENTES : </B><BR><BR>";
		while (odbc_fetch_row($err_o))
		{
			$odbc=array();
			for($m=1;$m<=$campos;$m++)
			{
				$odbc[$m-1]=odbc_result($err_o,$m);
			}
			$nn++;
			$NITS[$nn][0] = $odbc[0];
			$NITS[$nn][1] = $odbc[1];
		}
		
		if(strpos($wemp,":") !== false)
			$ODBC = substr($wemp,strpos($wemp,":")+1);
		$query = "delete from ".$empresa."_000011  ";
		$query = $query."  where expano =  ".$wanop;
		$query = $query."    and expper =  ".$wper1;
		$query = $query."    and expemp = '".$wemp."' ";
		$err = mysql_query($query,$conex);
		$query = "SELECT query,Odbc from ".$empresa."_000049  ";
		$query = $query."  where codigo =  1";
		$query = $query."    and Empresa = '".$wemp."' ";
		$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla ".$empresa."_000049");
		$row = mysql_fetch_array($err);
		$ODBC = $row[1];
		$conex_o = odbc_connect($ODBC,'','');
		$query = $row[0];
		$query=str_replace("ANO",$wanop,$query);
		$query=str_replace("MES",$wper1,$query);
		$err_o = odbc_do($conex_o,$query);
		$campos= odbc_num_fields($err_o);
		$count=0;
		$k=0;
		echo "<B>REGISTROS INCONSISTENTES : </B><BR><BR>";
		while (odbc_fetch_row($err_o))
		{
			$odbc=array();
			for($m=1;$m<=$campos;$m++)
			{
				$odbc[$m-1]=odbc_result($err_o,$m);
			}
			$pos=bi($NITS,$nn,$odbc[4]);
			if($pos != -1)
				$odbc[5] = $NITS[$pos][1];

			if($odbc[10] != "0000999" or $odbc[9] != "93")
			{
				$query = "SELECT cconom from ".$empresa."_000005 ";
	        	$query = $query." where ccocod = '".$odbc[3]."'";
	        	$query = $query."   and ccoemp = '".$wemp."' ";
	        	$err1 = mysql_query($query,$conex);
	        	$row1 = mysql_fetch_array($err1);
	        	$CC=$row1[0];
	        	$query = "SELECT mcunom from ".$empresa."_000024 ";
	        	$query = $query." where mcucue = '".$odbc[2]."'";
	        	$query = $query."   and mcuemp = '".$wemp."' ";
	        	$err1 = mysql_query($query,$conex);
	        	$row1 = mysql_fetch_array($err1);
	        	$CU=$row1[0];
	        	$query = "SELECT rcpcup,rcpccd from ".$empresa."_000042 ";
	        	$query = $query." where rcpcuc = '".$odbc[2]."'";
	       		$query = $query. "  and rcpcco = '".$odbc[3]."'";
	       		$query = $query."   and rcpemp = '".$wemp."' ";
	        	$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if ($num1 >  0)
				{
					$k++;
					$row1 = mysql_fetch_array($err1);
	           		$wcodpre = $row1[0];
	           		$odbc[3] = $row1[1];
				}
				else
				{
					echo "<B> CUENTA : </B>".rtrim($odbc[2])."-".$CU. "    <B>CENTRO DE COSTOS : </B>".$odbc[3]."-".$CC."<BR><BR>";
					$wcodpre=".";
				}
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				if(((substr($odbc[2],0,1) == "5" or substr($odbc[2],0,1) == "6") and ($odbc[8] == "2" or $odbc[8] == "4")) or (substr($odbc[2],0,1) == "4"  and ($odbc[8] == "1" or $odbc[8] == "3")))
					$odbc[7]=$odbc[7] * (-1);
				if($empresa == "costosyp")
				{
					if($odbc[3] == "1051")
						$odbc[3]="1050";
					if($odbc[3] == "3160")
						$odbc[3]="1135";
					if($odbc[3] == "1251")
						$odbc[3]="1135";
					if($odbc[3] == "1750")
						$odbc[3]="2240";
				}
				elseif($empresa == "cyppat")
					{
						$query = "SELECT Parccf from ".$empresa."_000146 ";
						$query = $query." where parcci = '".$odbc[3]."'";
						$query = $query."   and paremp = '".$wemp."' ";
						$query = $query. "  and parcon = 'cyppat'";
						$query = $query. "  and parest = 'on'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						if ($num1 >  0)
						{
							$row1 = mysql_fetch_array($err1);
							$odbc[3] = $row1[0];
						}
					}
				$count++;
				$query = "insert ".$empresa."_000011 (medico,fecha_data,hora_data,expemp,expcco,expano,expper,expcue,expcpr,expnit,expnte,expexp,expmon,exppro,expcon,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$odbc[3]."',".$odbc[0].",".$odbc[1].",'".$odbc[2]."','".$wcodpre."','".$odbc[4]."','".$odbc[5]."','".$odbc[6]."',".$odbc[7].",'00',".$count.",'C-".$empresa."')";
				$err1 = mysql_query($query,$conex) or die("Error en la Insercion de la Explicacion en Query : ".$query);
			}
		}
		echo "<B>REGISTROS ADICIONADOS : ".$count."</B><BR>";
		echo "<B>REGISTROS CON CODIGO PRESUPUESTAL : ".$k."</B><BR>";;
	}
}
?>
</body>
</html>
