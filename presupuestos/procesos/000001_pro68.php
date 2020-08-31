<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Actualizacion Mensual de Costos de Insumos</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro68.php Ver. 2016-12-26</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro68.php' method=post>";
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<center><input type='HIDDEN' name= 'ODBC' value='".$ODBC."'>";
	

	

	if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1)  or $wper1 < 1 or $wper1 > 12)
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>ACTUALIZACION MENSUAL DE COSTOS DE INSUMOS</td></tr>";
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
		echo "<tr><td bgcolor=#cccccc colspan=2 align=center><input type='RADIO' name='wtipoi' value=1 checked>Unix<input type='RADIO' name='wtipoi' value=2>Matrix</td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$wemp = substr($wemp,0,2);
		$query = "SELECT Cierre_costos from ".$empresa."_000048  ";
		$query = $query."  where ano = ".$wanop;
		$query = $query."    and mes =   ".$wper1;
		$query = $query."    and emp = '".$wemp."' ";
		$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);
		if($num > 0 and $row[0] == "off")
		{
			if($wtipoi == 1)
			{
				echo "EJECUCION POR ODBC<BR>";
				$query = "delete from ".$empresa."_000093 ";
				$query = $query."  where Minano = ".$wanop;
				$query = $query."    and Minmes = ".$wper1;
				$query = $query."    and Minemp = '".$wemp."' ";
				$err = mysql_query($query,$conex);
				$query = "SELECT query from ".$empresa."_000049  ";
				$query = $query."  where codigo =  17 ";
				$query = $query."    and empresa = '".$wemp."' ";
				$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla ".$empresa."_000049");
				$row = mysql_fetch_array($err);
				$conex_o = odbc_connect($ODBC,'','');
				$query = $row[0];
				$query=str_replace("ANO",$wanop,$query);
				$query=str_replace("MES",$wper1,$query);
				$err_o = odbc_do($conex_o,$query);
				$campos= odbc_num_fields($err_o);
				$count=0;
				while (odbc_fetch_row($err_o))
				{
					$odbc=array();
					for($m=1;$m<=$campos;$m++)
					{
						$odbc[$m-1]=odbc_result($err_o,$m);
					}
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000093 (medico,fecha_data,hora_data,Minemp, Minano, Minmes, Mincod, Mincpr, Minpro, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$odbc[0].",".$odbc[1].",'".$odbc[2]."',".$odbc[3].",0,'C-".$empresa."')";
					$err1 = mysql_query($query,$conex) or die("Error en la Insercion del Movimiento del Insumo");
					$count++;
					echo "REGISTRO ADICIONADO NRO : ".$count."<br>";
				}
				echo "<tr><td align=center  colspan=2><B>REGISTROS ADICIONADOS : ".$count."</B></td></tr></table>";
			}
			else
			{
				echo "EJECUCION POR MATRIX<BR>";
				$query = "delete from ".$empresa."_000093 ";
				$query = $query."  where Minano = ".$wanop;
				$query = $query."    and Minmes = ".$wper1;
				$query = $query."    and Minemp = '".$wemp."' ";
				$err = mysql_query($query,$conex);
				$query = "SELECT query from ".$empresa."_000049  ";
				$query = $query."  where codigo =  17 ";
				$query = $query."    and empresa = '".$wemp."' ";
				$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla ".$empresa."_000049");
				$row = mysql_fetch_array($err);
				$query = $row[0];
				$query=str_replace("ANO",$wanop,$query);
				$query=str_replace("MES",$wper1,$query);
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				$count=0;
				$k=0;
				if ($num>0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000093 (medico,fecha_data,hora_data,Minemp, Minano, Minmes, Mincod, Mincpr, Minpro, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$row[0].",".$row[1].",'".$row[2]."',".$row[3].",0,'C-".$empresa."')";
						$err1 = mysql_query($query,$conex) or die("Error en la Insercion del Movimiento del Insumo");
						$k++;
						echo "REGISTRO ADICIONADO NRO : ".$k."<br>";
					}
				}
				echo "<tr><td align=center  colspan=2><B>REGISTROS ADICIONADOS : ".$k."</B></td></tr></table>";
			}
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
?>
</body>
</html>
