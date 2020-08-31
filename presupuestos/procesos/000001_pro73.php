<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Calculo de Protocolos de Conjuntos</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro73.php Ver. 2016-05-26</b></font></td></tr></table>
</center>

<?php
include_once("conex.php");

/*EVALUACION RECURSIVA DE PROTOCOLOS*/		
function protocolo($ano,$mes, $cco, $pro, $por, $gru, $con, $tip, $conex, $empresa)
{
	Global $wemp;
	$wtot=0;
	//SUBTOTAL DE COSTOS X ACTIVIDADES
	if($tip == "T")
	    $query = "SELECT sum(Pqucan*cxapro) as total from ".$empresa."_000099,".$empresa."_000083 ";
	else
		$query = "SELECT sum(Pqucan*cxacvp) as total from ".$empresa."_000099,".$empresa."_000083 ";
	// ELIANA DIJO CULPA DE ELLA
	$query = $query." where Pqucco = '".$cco."'";
	$query = $query."   and Pquemp = '".$wemp."'";
	$query = $query."   and Pqupro = '".$pro."'";
	$query = $query."   and Pqutip = '1' ";
	$query = $query."   and pquemp = cxaemp ";
	$query = $query."   and Pqucod = cxasub ";
	$query = $query."   and Pquccp = cxacco ";
	$query = $query."   and cxaano = '".$ano."'";
	$query = $query."   and cxames = '".$mes."'";
	$err2 = mysql_query($query,$conex);
	$num2 = mysql_num_rows($err2);
	if($num2 > 0)
	{
		$row2 = mysql_fetch_array($err2);
		$wtot+= $row2[0];
	}

	//SUBTOTAL DE COSTOS X INSUMOS
	$query = "SELECT sum(Pqucan*minpro) as total from ".$empresa."_000099,".$empresa."_000093 ";
	$query = $query." where Pqucco = '".$cco."'";
	$query = $query."   and Pquemp = '".$wemp."'";
	$query = $query."   and Pqupro = '".$pro."'";
	$query = $query."   and Pqutip = '2' ";
	$query = $query."   and Pquemp = minemp ";
	$query = $query."   and Pqucod = mincod ";
	$query = $query."   and minano = '".$ano."'";
	$query = $query."   and minmes = '".$mes."'";
	$err2 = mysql_query($query,$conex);
	$num2 = mysql_num_rows($err2);
	if($num2 > 0)
	{
		$row2 = mysql_fetch_array($err2);
		$wtot+= $row2[0];
	}
	
	
	//SUBTOTAL PROCEDIMIENTOS EXTERNOS
	$query = "SELECT sum(Pqucan*fijmon) as total from ".$empresa."_000099,".$empresa."_000086 ";
	$query = $query." where Pqucco = '".$cco."'";
	$query = $query."   and Pquemp = '".$wemp."'";
	$query = $query."   and Pqupro = '".$pro."'";
	$query = $query."   and Pqutip = '4' ";
	$query = $query."   and fijano = '".$ano."'";
	$query = $query."   and Pquemp = fijemp ";
	$query = $query."   and Pquccp = fijcco ";
	$query = $query."   and Pqucod = fijcod ";
	$err2 = mysql_query($query,$conex);
	$num2 = mysql_num_rows($err2);
	if($num2 > 0)
	{
		$row2 = mysql_fetch_array($err2);
		$wtot+= $row2[0];
	}
	
	//SUBTOTAL DE COSTOS X PROTOCOLOS
	if($tip == "T")
	{
		$query = "SELECT sum(Pqucan*Pcapro) from ".$empresa."_000099,".$empresa."_000097 ";
		$query = $query." where Pqucco = '".$cco."'";
		$query = $query."   and Pquemp = '".$wemp."'";
		$query = $query."   and Pqupro = '".$pro."'";
		$query = $query."   and Pqugru = '".$gru."'";
		$query = $query."   and Pqucon = '".$con."'";
		$query = $query."   and Pqutip = '3' ";
		$query = $query."   and Pquemp = Pcaemp ";
		$query = $query."   and Pquccp = Pcacco ";
		$query = $query."   and Pqucod = Pcacod ";
		$query = $query."   and Pqugrp = Pcagru";
		$query = $query."   and Pqucoa = Pcacon ";
		$query = $query."   and pcaano = '".$ano."'";
		$query = $query."   and pcames = '".$mes."'";
	}
	else
	{
		$query = "SELECT sum(Pqucan*Cvapro) from ".$empresa."_000099,".$empresa."_000082 ";
		$query = $query." where Pqucco = '".$cco."'";
		$query = $query."   and Pquemp = '".$wemp."'";
		$query = $query."   and Pqupro = '".$pro."'";
		$query = $query."   and Pqugru = '".$gru."'";
		$query = $query."   and Pqucon = '".$con."'";
		$query = $query."   and Pqutip = '3' ";
		$query = $query."   and Pquemp = Cvaemp ";
		$query = $query."   and Pquccp = Cvacco ";
		$query = $query."   and Pqucod = Cvacod ";
		$query = $query."   and Pqugrp = Cvagru";
		$query = $query."   and Pqucoa = Cvacon ";
		$query = $query."   and Cvaano = '".$ano."'";
		$query = $query."   and Cvames = '".$mes."'";
	}
	$err2 = mysql_query($query,$conex);
	$num2 = mysql_num_rows($err2);
	if($num2 > 0)
	{
		$row2 = mysql_fetch_array($err2);
		$wtot+= $row2[0];
	}
	
	//ASIGNACION DEL VALOR TOTAL AL PROTOCOLO
	$protocolo = $wtot;
	return $protocolo;
}	
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro73.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or !isset($wcco1)  or !isset($wcco2) or !isset($wtip) or (strtoupper($wtip)  != "T" and strtoupper($wtip) != "V") or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO  DE PROTOCOLOS DE CONJUNTOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Tipo de Proceso : (T - Totales / V - Variables)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wtip' size=1 maxlength=1></td></tr>";
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
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			$query = "SELECT cierre_costos from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and emp = '".$wemp."'";
			$query = $query."    and mes = ".$wper1;
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if(($num > 0 and $row[0] == "off"))
			{
				$wcco2=strtolower($wcco2);
				$wtip=strtoupper($wtip);
				if ($wtip == "T")
				{
					$query = "DELETE from ".$empresa."_000097";
		            $query = $query."  where pcaano = ".$wanop;
		            $query = $query."    and pcaemp = '".$wemp."'";
		            $query = $query."    and pcames = ".$wper1;
		            $query = $query."    and pcacco between '".$wcco1."' and '".$wcco2."'";
		            $query = $query."    and pcatip = 'C' ";
		            $err = mysql_query($query,$conex);
	            }
	            else
	            {
		            $query = "DELETE from ".$empresa."_000082";
		            $query = $query."  where cvaano = ".$wanop;
		            $query = $query."    and cvaemp = '".$wemp."'";
		            $query = $query."    and cvames = ".$wper1;
		            $query = $query."    and cvacco between '".$wcco1."' and '".$wcco2."'";
		            $query = $query."    and Cvacod in (Select Mprpro from ".$empresa."_000095 where Mprtip='C' and Mprcco=cvacco) ";
					$err = mysql_query($query,$conex);
				}
				echo "<table border=1>";
				echo "<tr><td align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
				echo "<tr><td align=center>DIRECCION DE INFORMATICA</td></tr>";
				echo "<tr><td align=center>CALCULO RECURSIVO DE PROTOCOLOS</td></tr>";
				echo "<tr><td align=center>EMPRESA : ".$wempt."</td></tr>";
				echo "<tr><td align=center>UNIDAD  INICIAL: <b> ".$wcco1. "</b> UNIDAD  FINAL: <b> ".$wcco2. "</b></td></tr>";
				echo "<tr><td align=center>PERIODO  : ".$wper1." A&Ntilde;O : ".$wanop."</td></tr>";
				$count=0;
				//                 0      1      2      3       4      5      6
				$query  = "SELECT mprcco,mprpro,mprpor,mprgru,Pqupro,Pqucan,mprcon from ".$empresa."_000095,".$empresa."_000099 ";
				$query .= " where mprcco between'".$wcco1."' and '".$wcco2."'";
				$query .= "   and mpremp = '".$wemp."'";
				$query .= "   and mprtip = 'C' ";
				$query .= "   and mpremp = Pquemp ";
				$query .= "   and mprcco = Pqucco ";
				$query .= "   and mprpro = Pqupro ";
				$query .= "   and mprgru = Pqugru ";
				$query .= "   and mprcon = Pqucon ";
				$query .= "  group by 1,2,3,4,5,7 "; 
				$query .= "  order by mprcco,mprpro ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<tr><td  align=center>PROCESANDO PROTOCOLO : ".$row[0]." - ".$row[1]."</td></tr>";
					$wtotal= protocolo($wanop,$wper1, $row[0], $row[1], $row[2], $row[3], $row[6], $wtip, $conex,$empresa);
					if($wtip == "T")
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$wtmn=$wtotal / (1 - $row[2]);
						$query = "insert ".$empresa."_000097 (medico,fecha_data,hora_data,Pcaemp, Pcaano, Pcames, Pcacco, Pcacod, Pcagru, Pcacon, Pcapor, Pcactp, Pcatmn, Pcapro, Pcatip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$row[0]."','".$row[1]."','".$row[3]."','".$row[6]."',".$row[2].",".$wtotal.",".$wtmn.",0,'C','C-".$empresa."')";
						$err1 = mysql_query($query,$conex);
						if ($err1 != 1)
							echo mysql_errno().":".mysql_error()."<br>";
						$count++;
					}
					else
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$wtmn=$wtotal / (1 - $row[2]);
						$query = "insert ".$empresa."_000082 (medico, fecha_data, hora_data,Cvaemp, Cvaano, Cvames, Cvacco, Cvacod, Cvagru, Cvacon, Cvapor, Cvactp, Cvatmn, Cvapro, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$row[0]."','".$row[1]."','".$row[3]."','".$row[6]."',".$row[2].",".$wtotal.",".$wtmn.",0,'C-".$empresa."')";
						$err1 = mysql_query($query,$conex);
						if ($err1 != 1)
							echo mysql_errno().":".mysql_error()."<br>";
						$count++;
					}
				}
				echo "</table>";
				echo "TOTAL REGISTROS ACTUALIZADOS : ".$count;
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
