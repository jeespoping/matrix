<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Calculo Recursivo de Protocolos (CP)</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro189.php Ver. 2016-05-26</b></font></td></tr></table>
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
	    $query = "SELECT sum(procan*cxppro) as total from ".$empresa."_000100,".$empresa."_000154 ";
	else
		$query = "SELECT sum(procan*cxpcvp) as total from ".$empresa."_000100,".$empresa."_000154 ";
	//SE CAMBIA cxpCOS POR cxpPRO !!!!POR CULPA DE ELIANA!!!!
	$query = $query." where procco = '".$cco."'";
	$query = $query."   and proemp = '".$wemp."'";
	$query = $query."   and propro = '".$pro."'";
	$query = $query."   and protip = '1' ";
	//CULPA DE ELIANA
	$query = $query."   and Progru =  '".$gru."'";
	$query = $query."   and Procon =  '".$con."'";
	$query = $query."   and proemp = cxpemp ";
	$query = $query."   and procod = cxpsub ";
	$query = $query."   and proccp = cxpcco ";
	$query = $query."   and cxpano = '".$ano."'";
	$query = $query."   and cxpmes = '".$mes."'";
	$err2 = mysql_query($query,$conex);
	$num2 = mysql_num_rows($err2);
	if($num2 > 0)
	{
		$row2 = mysql_fetch_array($err2);
		$wtot+= $row2[0];
	}
    

	//SUBTOTAL DE COSTOS X INSUMOS
	$query = "SELECT sum(procan*minpro) as total from ".$empresa."_000100,".$empresa."_000093 ";
	$query = $query." where procco = '".$cco."'";
	$query = $query."   and proemp = '".$wemp."'";
	$query = $query."   and propro = '".$pro."'";
	$query = $query."   and protip = '2' ";
	//CULPA DE ELIANA
	$query = $query."   and Progru =  '".$gru."'";
	$query = $query."   and Procon =  '".$con."'";
	$query = $query."   and proemp = minemp ";
	$query = $query."   and procod = mincod ";
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
	$query = "SELECT sum(procan*fijmon) as total from ".$empresa."_000100,".$empresa."_000086 ";
	$query = $query." where procco = '".$cco."'";
	$query = $query."   and proemp = '".$wemp."'";
	$query = $query."   and propro = '".$pro."'";
	$query = $query."   and protip = '4' ";
	//CULPA DE ELIANA
	$query = $query."   and Progru =  '".$gru."'";
	$query = $query."   and Procon =  '".$con."'";
	$query = $query."   and fijano = '".$ano."'";
	$query = $query."   and proemp = fijemp ";
	$query = $query."   and proccp = fijcco ";
	$query = $query."   and procod = fijcod ";
	$err2 = mysql_query($query,$conex);
	$num2 = mysql_num_rows($err2);
	if($num2 > 0)
	{
		$row2 = mysql_fetch_array($err2);
		$wtot+= $row2[0];
	}
	
	//SUBTOTAL DE COSTOS X PROTOCOLOS
	$wprot=array();
	$wpent=-1;
	$query = "SELECT proccp,procod,procan,mprpor,progrp,procoa from ".$empresa."_000100,".$empresa."_000095 ";
	$query = $query." where procco = '".$cco."'";
	$query = $query."   and proemp = '".$wemp."'";
	$query = $query."   and propro = '".$pro."'";
	$query = $query."   and protip = '3' ";
	$query = $query."   and Progru =  '".$gru."'";
	$query = $query."   and Procon =  '".$con."'";
	$query = $query."   and proemp = Mpremp ";
	$query = $query."   and proccp = Mprcco ";
	$query = $query."   and procod = Mprpro ";
	$query = $query."   and progrp = Mprgru ";
	$query = $query."   and procoa = Mprcon ";
	$query = $query."   and mprtip = 'P' ";
	$err2 = mysql_query($query,$conex);
	$num2 = mysql_num_rows($err2);
	if($num2 > 0)
	{
		$wpent=$num2;
		for ($j=0;$j<$num2;$j++)
		{
			$row2 = mysql_fetch_array($err2);
			$wprot[$j][0]=$ano;
			$wprot[$j][1]=$mes;
			$wprot[$j][2]=$row2[0];
			$wprot[$j][3]=$row2[1];
			$wprot[$j][4]=$row2[3];
			$wprot[$j][5]=$row2[4];
			$wprot[$j][6]=$tip;
			$wprot[$j][7]=$conex;
			$wprot[$j][8]=$row2[2];
			$wprot[$j][9]=$row2[5];
		}
	}
	if($wpent != (-1))
		for ($k=0;$k<$wpent;$k++)
		{
			$wtot +=  protocolo($wprot[$k][0],$wprot[$k][1], $wprot[$k][2], $wprot[$k][3],$wprot[$k][4],$wprot[$k][5],$wprot[$k][9], $wprot[$k][6], $wprot[$k][7], $empresa)*$wprot[$k][8];
		}
	$protocolo = $wtot;
	return $protocolo;
}	
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro189.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or !isset($wcco1)  or !isset($wcco2)  or !isset($wtip) or (strtoupper($wtip)  != "T" and strtoupper($wtip) != "V") or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO RECURSIVO DE PROTOCOLOS (CP)</td></tr>";
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
			$query = $query."    and mes =   ".$wper1;
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if(($num > 0 and $row[0] == "off"))
			{
				$wcco2=strtolower($wcco2);
				$wtip=strtoupper($wtip);
				if ($wtip == "T")
				{
					$query = "DELETE from ".$empresa."_000155";
		            $query = $query."  where pcpano = '".$wanop."'";
		            $query = $query."    and pcpemp = '".$wemp."'";
		            $query = $query."    and pcpmes = '".$wper1."'";
		            $query = $query."    and pcpcco between '".$wcco1."' and '".$wcco2."'";
		            $err = mysql_query($query,$conex);
	            }
	            else
	            {
		            $query = "DELETE from ".$empresa."_000156";
		            $query = $query."  where cvpano = '".$wanop."'";
					$query = $query."    and cvpemp = '".$wemp."'";
		            $query = $query."    and cvpmes = '".$wper1."'";
		            $query = $query."    and cvpcco between '".$wcco1."' and '".$wcco2."'";
					$err = mysql_query($query,$conex);
				}
				echo "<table border=1>";
				echo "<tr><td align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
				echo "<tr><td  align=center>DIRECCION DE INFORMATICA</td></tr>";
				echo "<tr><td  align=center>CALCULO RECURSIVO DE PROTOCOLOS (CP)</td></tr>";
				echo "<tr><td align=center>EMPRESA : ".$wempt."</td></tr>";
				echo "<tr><td  align=center>UNIDAD  INICIAL: <b> ".$wcco1. "</b> UNIDAD  FINAL: <b> ".$wcco2. "</b></td></tr>";
				echo "<tr><td  align=center>PERIODO  : ".$wper1." A&Ntilde;O : ".$wanop."</td></tr>";
				$count=0;
				//SE CAMBIA QUERY !!!!POR CULPA DE ELIANA!!!!
				//                 0       1     2      3       4      5       6
				$query  = "SELECT mprcco,mprpro,mprpor,mprgru,Propro,Procan,mprcon from ".$empresa."_000095,".$empresa."_000100 ";
				$query .= " where mprcco between'".$wcco1."' and '".$wcco2."'";
				$query .= "   and mpremp = '".$wemp."'";
				$query .= "   and mprtip = 'P' ";
				$query .= "   and mpremp = Proemp ";
				$query .= "   and mprcco = Procco ";
				$query .= "   and mprpro = Propro ";
				$query .= "   and mprgru = Progru ";
				$query .= "   and mprcon = Procon ";
				$query .= "  group by 1,2,4,5,7 "; 
				$query .= "  order by mprcco,mprpro ";
				
				//$query = "SELECT mprcco,mprpro,mprpor,mprgru from ".$empresa."_000095 ";
			    //$query = $query. " where mprcco between '".$wcco1."' and '".$wcco2."'";
			    //$query = $query. "   and mprtip = 'P' ";
			    //$query = $query. "   order by mprcco,mprpro ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<tr><td  align=center>PROCESANDO PROTOCOLO : ".$row[0]." - ".$row[1]."</td></tr>";
					$wtotal= protocolo($wanop,$wper1, $row[0], $row[1], $row[2], $row[3], $row[6], $wtip,$conex, $empresa);
					//echo $wtotal."<br>";
					if($wtip == "T")
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						if($row[2] == 1)
							$wtmn=$wtotal;
						else
							$wtmn=$wtotal / (1 - $row[2]);
						$query = "insert ".$empresa."_000155 (medico, fecha_data, hora_data,pcpemp, pcpano, pcpmes, pcpcco, pcpcod, pcpgru, pcpcon, pcppor, pcpctp, pcptmn, pcppro, pcptip, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$row[0]."','".$row[1]."','".$row[3]."','".$row[6]."',".$row[2].",".$wtotal.",".$wtmn.",0,'P','C-".$empresa."')";
						$err1 = mysql_query($query,$conex);
						if ($err1 != 1)
							echo mysql_errno().":".mysql_error()."<br>";
						$count++;
					}
					else
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						if($row[2] == 1)
							$wtmn=$wtotal;
						else
							$wtmn=$wtotal / (1 - $row[2]);
						$query = "insert ".$empresa."_000156 (medico, fecha_data, hora_data,cvpemp, cvpano, cvpmes, cvpcco, cvpcod, cvpgru, cvpcon, cvppor, cvpctp, cvptmn, cvppro, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$row[0]."','".$row[1]."','".$row[3]."','".$row[6]."',".$row[2].",".$wtotal.",".$wtmn.",0,'C-".$empresa."')";
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
