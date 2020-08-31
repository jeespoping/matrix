<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Consolidacion Nomina Para Costos</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro53.php Ver. 2018-01-19</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro53.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	

	

	if(!isset($wanop) or !isset($wper1) or !isset($wemp) or $wemp == "Seleccione")
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>CONSOLIDACION NOMINA PARA COSTOS</td></tr>";
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
		$query = "SELECT Cierre_costos from ".$empresa."_000048  ";
		$query = $query."  where ano = ".$wanop;
		$query = $query."    and mes = ".$wper1;
		$query = $query."    and emp = '".$wemp."' ";
		$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
		$num = mysql_num_rows($err);
		$row = mysql_fetch_array($err);
		if($num > 0 and $row[0] == "off")
		{
			$query = "delete from ".$empresa."_000094  ";
			$query = $query."  where mnoano =  ".$wanop;
			$query = $query."  	 and mnomes =  ".$wper1;
			$query = $query."    and mnoemp = '".$wemp."' ";
			$err = mysql_query($query,$conex);
			$query = "SELECT   Norcco, Norcar, Norcod, Normon, Norhor, Norpre, Norrec  from ".$empresa."_000036,".$empresa."_000005  ";
			$query = $query."  where Norano =  ".$wanop;
			$query = $query."    and Norper =  ".$wper1;
			$query = $query."    and Norfil = '".$wemp."' ";
			$query = $query."    and Norcod !=  '0022' " ;
			$query = $query."    and Norcco  =  ccocod " ;
			$query = $query."    and Norfil  =  ccoemp " ;
			$query = $query."    and ccocos  =  'S' " ;
			$query = $query."  order by  Norcco, Norcar, Norcod " ;
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$key="";
			$kcco="";
			$kofi="";
			$count=0;
			for ($i=0;$i<$num;$i++)
			{	
				$row = mysql_fetch_array($err);
				if($row[0].$row[1] != $key)
				{
					if($i != 0)
					{
						//$wpag=$wpag*(1 + $wfacrec);
						$wpag+=$wotro;
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000094 (medico,fecha_data,hora_data,Mnoemp, Mnoano, Mnomes, Mnocco, Mnoofi, Mnopag, Mnothb ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$kcco."','".$kofi."','".$wpag."',".$whoras.",'C-".$empresa."')";
						$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()." Error en la Insercion de la Explicacion");
						$count++;
						echo "REGISTROS INSERTADOS : ".$count."<BR>";
					}
					$key=$row[0].$row[1];
					$kcco=$row[0];
					$kofi=$row[1];
					$wpag=0;
					$whoras=0;
					$wotro=0;
					$wfacrec=$row[5];
				}
				$wfac=$row[5];
				if( $row[2] == "0030" or $row[2] == "0037")
					$wotro+=$row[3];
				else
					$wpag+=$row[3]*(1 + $wfac);
				if($row[2] == "0001")
					$whoras+=$row[4];
			}
			//$wpag=$wpag*(1 + $wfacrec);
			$wpag+=$wotro;
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			$query = "insert ".$empresa."_000094 (medico,fecha_data,hora_data,Mnoemp, Mnoano, Mnomes, Mnocco, Mnoofi, Mnopag, Mnothb ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$kcco."','".$kofi."','".$wpag."',".$whoras.",'C-".$empresa."')";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()." Error en la Insercion de la Explicacion");
			$count++;
			echo "REGISTROS INSERTADOS : ".$count."<BR>";
			echo "<B>REGISTROS ADICIONADOS : ".$count."</B><BR>";
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
