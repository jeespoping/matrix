<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Montaje de Informacion Maestro de Personal (ODBC a T34)</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro111.php Ver. 2017-08-23</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro111.php' method=post>";
	echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<center><input type='HIDDEN' name= 'ODBC' value='".$ODBC."'>";
	

	

	if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione")
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>MONTAJE DE INFORMACION MAESTRO DE PERSONAL (ODBC A T34)</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o  de Presupuestacion</td>";
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
		$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos ".mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);
		if($num > 0 and $row[0] == "on")
		{
			$query = "delete from ".$empresa."_000034  ";
			$query = $query."  where nomano =  ".$wanop;
			$query = $query."    and nomemp = '".$wemp."' ";
			$err = mysql_query($query,$conex);
			$query = "SELECT query, Odbc from ".$empresa."_000049  ";
			$query = $query."  where codigo =  21";
			$query = $query."    and Empresa = '".$wemp."' ";
			$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla ".$empresa."_000049");
			$row = mysql_fetch_array($err);
			$ODBC = $row[1];
			//$conex_o = odbc_connect($ODBC,'','');
			$conex_o = odbc_connect($ODBC,"","") or die(odbc_errormsg());
			$query = $row[0];
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
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$nom=$odbc[0]." ".$odbc[1]." ".$odbc[2]." ".$odbc[3];
				$odbc[6] = str_replace(",",".",$odbc[6]);
				$odbc[7] = str_replace(",",".",$odbc[7]);
				$query = "insert ".$empresa."_000034 (medico, fecha_data, hora_data,Nomemp, Nomano, Nomcco, Nomcod, Nomofi, Nomnom, Nomhco, Nommin, Nommfi, Nombas, Nompre, Nomrec, Nomaju, Nommaj, Nombom, Nomobs, Nomtip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",'".$odbc[4]."','".$odbc[8]."','".$odbc[5]."','".$nom."',".$odbc[6].",1,12,".$odbc[7].",0,0,0,1,0,'.','E','C-".$empresa."')";
				//echo $query."<br>";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
					$k++;
					echo "REGISTRO INSERTADO  : ".$k."<br>";
					}
			}
			echo "<B>REGISTROS ADICIONADOS : ".$k."</B><BR>";
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
