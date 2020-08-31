<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Calculo Recursivo de Protocolos</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro69.php Ver. 2014-02-24</b></font></td></tr></table>
</center>

<?php
include_once("conex.php");

/*EVALUACI�N RECURSIVA DE PROTOCOLOS*/		
function protocolo($ano,$mes, $cco, $pro, $por, $gru, $con, $tip, $conex)
{
	$wtot=0;
	//SUBTOTAL DE COSTOS X ACTIVIDADES
	if($tip == "T")
    {
	    $query = "SELECT sum(procan*cxapro) as total from costosyp_000100,costosyp_000083 ";
	    //SE CAMBIA CXACOS POR CXAPRO !!!!POR CULPA DE ELIANA!!!!
	    $query = $query." where procco = '".$cco."'";
	    $query = $query."   and propro = '".$pro."'";
	    $query = $query."   and protip = '1' ";
	    //CULPA DE ELIANA
		$query = $query."   and Progru =  '".$gru."'";
		$query = $query."   and Procon =  '".$con."'";
	    $query = $query."   and procod = cxasub ";
	    $query = $query."   and proccp = cxacco ";
	    $query = $query."   and cxaano = '".$ano."'";
	    $query = $query."   and cxames = '".$mes."'";
	    $err2 = mysql_query($query,$conex);
		$num2 = mysql_num_rows($err2);
		if($num2 > 0)
		{
			$row2 = mysql_fetch_array($err2);
			$wtot+= $row2[0];
		}
    }

	//SUBTOTAL DE COSTOS X INSUMOS
	$query = "SELECT sum(procan*minpro) as total from costosyp_000100,costosyp_000093 ";
	$query = $query." where procco = '".$cco."'";
	$query = $query."   and propro = '".$pro."'";
	$query = $query."   and protip = '2' ";
	//CULPA DE ELIANA
	$query = $query."   and Progru =  '".$gru."'";
	$query = $query."   and Procon =  '".$con."'";
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
	$query = "SELECT sum(procan*fijmon) as total from costosyp_000100,costosyp_000086 ";
	$query = $query." where procco = '".$cco."'";
	$query = $query."   and propro = '".$pro."'";
	$query = $query."   and protip = '4' ";
	//CULPA DE ELIANA
	$query = $query."   and Progru =  '".$gru."'";
	$query = $query."   and Procon =  '".$con."'";
	$query = $query."   and fijano = '".$ano."'";
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
	$query = "SELECT proccp,procod,procan,mprpor,progrp,procoa from costosyp_000100,costosyp_000095 ";
	$query = $query." where procco = '".$cco."'";
	$query = $query."   and propro = '".$pro."'";
	$query = $query."   and protip = '3' ";
	$query = $query."   and Progru =  '".$gru."'";
	$query = $query."   and Procon =  '".$con."'";
	$query = $query."   and proccp = mprcco ";
	$query = $query."   and procod = mprpro ";
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
			$wtot +=  protocolo($wprot[$k][0],$wprot[$k][1], $wprot[$k][2], $wprot[$k][3],$wprot[$k][4],$wprot[$k][5],$wprot[$k][9], $wprot[$k][6], $wprot[$k][7])*$wprot[$k][8];
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
		

		

		echo "<form action='000001_pro69.php' method=post>";
		if(!isset($wanop) or !isset($wper1) or !isset($wcco1)  or !isset($wcco2)  or !isset($wtip) or (strtoupper($wtip)  != "T" and strtoupper($wtip) != "V") or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO RECURSIVO DE PROTOCOLOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A�o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Tipo de Proceso : (T - Totales / V - Variables)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wtip' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$query = "SELECT cierre_costos from costosyp_000048  ";
			$query = $query."  where ano = ".$wanop;
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
					$query = "DELETE from costosyp_000097";
		            $query = $query."  where pcaano = '".$wanop."'";
		            $query = $query."      and pcames = '".$wper1."'";
		            $query = $query."      and pcacco between '".$wcco1."' and '".$wcco2."'";
		            $err = mysql_query($query,$conex);
	            }
	            else
	            {
		            $query = "DELETE from costosyp_000082";
		            $query = $query."  where cvaano = '".$wanop."'";
		            $query = $query."      and cvames = '".$wper1."'";
		            $query = $query."      and cvacco between '".$wcco1."' and '".$wcco2."'";
					$err = mysql_query($query,$conex);
				}
				echo "<table border=1>";
				echo "<tr><td align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
				echo "<tr><td  align=center>DIRECCION DE INFORMATICA</td></tr>";
				echo "<tr><td  align=center>CALCULO RECURSIVO DE PROTOCOLOS</td></tr>";
				echo "<tr><td  align=center>UNIDAD  INICIAL: <b> ".$wcco1. "</b> UNIDAD  FINAL: <b> ".$wcco2. "</b></td></tr>";
				echo "<tr><td  align=center>PERIODO  : ".$wper1." A�O : ".$wanop."</td></tr>";
				$count=0;
				//SE CAMBIA QUERY !!!!POR CULPA DE ELIANA!!!!
				//                 0       1     2      3       4      5       6
				$query  = "SELECT mprcco,mprpro,mprpor,mprgru,Propro,Procan,mprcon from costosyp_000095,costosyp_000100 ";
				$query .= " where mprcco between'".$wcco1."' and '".$wcco2."'";
				$query .= "    and mprtip = 'P' ";
				$query .= "    and mprcco = Procco ";
				$query .= "    and mprpro = Propro ";
				$query .= "    and mprgru = Progru ";
				$query .= "    and mprcon = Procon ";
				$query .= "   group by 1,2,4,5,7 "; 
				$query .= "   order by mprcco,mprpro ";
				
				//$query = "SELECT mprcco,mprpro,mprpor,mprgru from costosyp_000095 ";
			    //$query = $query. " where mprcco between '".$wcco1."' and '".$wcco2."'";
			    //$query = $query. "   and mprtip = 'P' ";
			    //$query = $query. "   order by mprcco,mprpro ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<tr><td  align=center>PROCESANDO PROTOCOLO : ".$row[0]." - ".$row[1]."</td></tr>";
					$wtotal= protocolo($wanop,$wper1, $row[0], $row[1], $row[2], $row[3], $row[6], $wtip,$conex);
					if($wtip == "T")
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						if($row[2] == 1)
							$wtmn=$wtotal;
						else
							$wtmn=$wtotal / (1 - $row[2]);
						$query = "insert costosyp_000097 (medico, fecha_data, hora_data, Pcaano, Pcames, Pcacco, Pcacod, Pcagru, Pcacon, Pcapor, Pcactp, Pcatmn, Pcapro, Pcatip, seguridad) values ('costosyp','".$fecha."','".$hora."',".$wanop.",".$wper1.",'".$row[0]."','".$row[1]."','".$row[3]."','".$row[6]."',".$row[2].",".$wtotal.",".$wtmn.",0,'P','C-costosyp')";
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
						$query = "insert costosyp_000082 (medico, fecha_data, hora_data, Cvaano, Cvames, Cvacco, Cvacod, Cvagru, Cvacon, Cvapor, Cvactp, Cvatmn, Cvapro, seguridad) values ('costosyp','".$fecha."','".$hora."',".$wanop.",".$wper1.",'".$row[0]."','".$row[1]."','".$row[3]."','".$row[6]."',".$row[2].",".$wtotal.",".$wtmn.",0,'C-costosyp')";
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
