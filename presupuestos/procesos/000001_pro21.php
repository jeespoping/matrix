<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="FFFFFF">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Montaje de Informacion de Nomina Real</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro21.php Ver. 2018-05-16</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form action='000001_pro21.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		echo "<center><input type='HIDDEN' name= 'ODBC' value='".$ODBC."'>";
	

	

	if(!isset($wanop) or !isset($wper1) or !isset($wemp) or $wemp == "Seleccione")
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>MONTAJE DE INFORMACION DE NOMINA REAL	</td></tr>";
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
		$query = "delete from ".$empresa."_000036  ";
		$query = $query."  where norano =  ".$wanop;
		$query = $query."    and norper =  ".$wper1;
		$query = $query."    and norfil = '".$wemp."' ";
		$err = mysql_query($query,$conex);
		$query = "SELECT query,Odbc from ".$empresa."_000049  ";
		$query = $query."  where codigo =  4";
		$query = $query."    and Empresa = '".$wemp."' ";
		$err = mysql_query($query,$conex) or die("No Existe el Query Especificado Consulte la tabla ".$empresa."_000049");
		$row = mysql_fetch_array($err);
		$ODBC = $row[1];
		//$conex_o = odbc_connect($ODBC,'','');
		$conex_o = odbc_connect($ODBC,"","") or die(odbc_errormsg());
		$query = $row[0];
		$query=str_replace("ANO",$wanop,$query);
		$query=str_replace("MES",$wper1,$query);
		$err_o = odbc_do($conex_o,$query);
		$campos= odbc_num_fields($err_o);
		$count=0;
		$klave = "";
		$data = array();
		$m1 = -1;
		while (odbc_fetch_row($err_o))
		{
			$odbc=array();
			for($m=1;$m<=$campos;$m++)
			{
				$odbc[$m-1]=odbc_result($err_o,$m);
			}
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			//if( $odbc[3] == "5133" Or $odbc[3] == "5135" Or $odbc[3] == "5237" Or $odbc[3] == "5307" Or  $odbc[3] == "5314" Or  $odbc[3] == "5320")
				//$odbc[4]=$odbc[4] * (-1);
			if( $odbc[3] == "0145")
			{
				$val145 = $odbc[4];
				$odbc[4]=$odbc[4] * (0);
			}

			//$odbc[2] =substr($odbc[2],1);
			$odbc[4]=str_replace(",",".",$odbc[4]);
			$odbc[5]=str_replace(",",".",$odbc[5]);
			if( $odbc[3] == "0120" Or $odbc[3] == "0121" Or $odbc[3] == "0143" Or  $odbc[3] == "0460" Or  $odbc[3] == "0463"  Or  $odbc[3] == "0464"  Or  $odbc[3] == "0465"  Or  $odbc[3] == "0466"  Or  $odbc[3] == "0467"  Or  $odbc[3] == "0468")
				$odbc[5] = 0;
			if( $odbc[3] == "0003" Or $odbc[3] == "0013" Or $odbc[3] == "0020" Or $odbc[3] == "0025" Or $odbc[3] == "0026" Or  $odbc[3] == "0028" Or  $odbc[3] == "0145"  Or  $odbc[3] == "0001"  Or  $odbc[3] == "0005"  Or  $odbc[3] == "0015"  Or  $odbc[3] == "0027")
				$odbc[5] = $odbc[5] * 8;
			if($klave != $odbc[2])
			{
				if($klave != "")
				{
					$sumaE = 0;
					for($m=0;$m<=$m1;$m++)
						if($data[6][$m] == "0001" or $data[6][$m] == "0013" or $data[6][$m] == "0003")
							$sumaE += $data[7][$m];
					$suma145 = 0;
					for($m=0;$m<=$m1;$m++)
						if($data[6][$m] == "0145")
							$suma145 += $data[13][$m];
					for($m=0;$m<=$m1;$m++)
						if($sumaE > 0)
							if($data[6][$m] == "0145")
								$data[11][$m] = $data[13][$m] / $suma145;
							else
								$data[11][$m] = $data[7][$m] / $sumaE;
						else
							$data[11][$m] = 0;
					for($m=0;$m<=$m1;$m++)
					{
						if($data[11][$m] < 1 and ($data[8][$m] * $data[11][$m]) > 1)
							$data[8][$m] = $data[8][$m] * $data[11][$m];
						$data[8][$m] = round($data[8][$m],0);
					}
					for($m=0;$m<=$m1;$m++)
					{
						$query = "insert ".$empresa."_000036 (medico,fecha_data,hora_data,norfil,norano,norper,norcco,norcar,noremp,norcod,normon,norhor,norpre,norrec,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$data[0][$m]."',".$data[1][$m].",".$data[2][$m].",'".$data[3][$m]."','".$data[4][$m]."','".$data[5][$m]."','".$data[6][$m]."',".$data[7][$m].",".$data[8][$m].",".$data[9][$m].",".$data[10][$m].",'C-".$empresa."')";
						//echo $query."<br>";
						$err1 = mysql_query($query,$conex) or die("Error en la Insercion de la Nomina ".mysql_errno().":".mysql_error());
						$count++;
						echo "REGISTROS INSERTADOS : ".$count."<BR>";
					}
				}
				$klave = $odbc[2];
				$data = array();
				$m1 = -1;
			}
			$m1++;
			$data[0][$m1] = $wemp;
			$data[1][$m1] = $wanop;
			$data[2][$m1] = $wper1;
			$data[3][$m1] = $odbc[0];
			$data[4][$m1] = $odbc[1];
			$data[5][$m1] = $odbc[2];
			$data[6][$m1] = $odbc[3];
			$data[7][$m1] = $odbc[4];
			$data[8][$m1] = $odbc[5];
			$data[9][$m1] = 0;
			$data[10][$m1] = 0;
			$data[13][$m1] = $val145;
		}
		$sumaE = 0;
		for($m=0;$m<=$m1;$m++)
			if($data[6][$m] == "0001")
				$sumaE += $data[7][$m];
		for($m=0;$m<=$m1;$m++)
			if($data[6][$m] == "0001")
				if($sumaE > 0)
					$data[11][$m] = $data[7][$m] / $sumaE;
				else
					$data[11][$m] = 0;
		for($m=0;$m<=$m1;$m++)
		{
			$data[8][$m] = $data[8][$m] * $data[11][$m];
			$data[8][$m] = round($data[8][$m],0);
		}
		for($m=0;$m<=$m1;$m++)
		{
			$query = "insert ".$empresa."_000036 (medico,fecha_data,hora_data,norfil,norano,norper,norcco,norcar,noremp,norcod,normon,norhor,norpre,norrec,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$data[0][$m]."',".$data[1][$m].",".$data[2][$m].",'".$data[3][$m]."','".$data[4][$m]."','".$data[5][$m]."','".$data[6][$m]."',".$data[7][$m].",".$data[8][$m].",".$data[9][$m].",".$data[10][$m].",'C-".$empresa."')";
			//echo $query."<br>";
			$err1 = mysql_query($query,$conex) or die("Error en la Insercion de la Nomina ".mysql_errno().":".mysql_error());
			$count++;
			echo "REGISTROS INSERTADOS : ".$count."<BR>";
		}
		echo "<B>REGISTROS ADICIONADOS : ".$count."</B><BR>";
	}
}
?>
</body>
</html>
