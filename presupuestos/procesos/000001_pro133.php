<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Ajuste Valor Hora x Salario Flexible</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro133.php Ver. 2017-06-07</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro133.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		echo "<center><input type='HIDDEN' name= 'ODBC' value='".$ODBC."'>";
	

	

	if(!isset($wanop) or !isset($wper)  or $wper < 1 or $wper > 12 or !isset($wemp) or $wemp == "Seleccione" or !isset($wtip) or (strtoupper($wtip) != "AN"  and strtoupper($wtip) != "AC"))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>AJUSTE VALOR HORA X SALARIO FLEXIBLE</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  de Presupuestaci&oacute;n</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Mes  Proceso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper' size=4 maxlength=4></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Tipo de Proceso (AN (A&ntilde;o Anterior) / AC (A&ntilde;o Actual)</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wtip' size=2 maxlength=2></td></tr>";
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
		if(strtoupper($wtip) == "AN")
			$wanopa =$wanop - 1;
		else
			$wanopa =$wanop;
		$query = "SELECT query, Odbc from ".$empresa."_000049  ";
		$query = $query."  where codigo =  23";
		$query = $query."    and Empresa = '".$wemp."' ";
		$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla ".$empresa."_000049");
		$row = mysql_fetch_array($err);
		$ODBC = $row[1];
		//$conex_o = odbc_connect($ODBC,'','');
		$conex_o = odbc_connect("queryx7","","") or die(odbc_errormsg());
		$query = $row[0];
		$query=str_replace("ANO",$wanopa,$query);
		$query=str_replace("MES",$wper,$query);
		$err_o = odbc_do($conex_o,$query);
		$campos= odbc_num_fields($err_o);
		$k=0;
		while (odbc_fetch_row($err_o))
		{
			$odbc=array();
			for($m=1;$m<=$campos;$m++)
			{
				$odbc[$m-1]=odbc_result($err_o,$m);
			}
			$query = "SELECT Nomhco, Nombas from ".$empresa."_000034 ";
			$query = $query."  where Nomano = ".$wanop;
			$query = $query."    and Nomemp = '".$wemp."' ";
			$query = $query."    and Nomcod = '".$odbc[0]."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num > 0)
			{
				$row = mysql_fetch_array($err);
				$valor=($row[1] * 0.65) + ($odbc[1] / $row[0]);
				$query = "UPDATE ".$empresa."_000034 SET Nombas = ".$valor." WHERE Nomano=".$wanop." and Nomcod='".$odbc[0]."' and Nomemp='".$wemp."'";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
	       			$k++;
	       			echo "REGISTRO ACTUALIZADO  : ".$k."<br>";
				}
			}
		}
		echo "<B>REGISTROS ACTUALIZADOS : ".$k."</B><BR>";
	}
}
?>
</body>
</html>
