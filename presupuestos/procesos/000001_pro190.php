<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Consolidacion de Protocolos (CP)</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro190.php Ver. 2016-05-20</b></font></td></tr></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro190.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>CONSOLIDACION DE PROTOCOLOS (CP)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
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
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			$query = "SELECT Cierre_costos from ".$empresa."_000048  ";
			$query = $query."  where ano = ".$wanop;
			$query = $query."    and emp = '".$wemp."'";
			$query = $query."    and mes =  ".$wper1;
			$err = mysql_query($query,$conex) or die("No Existe el Periodo LLame a Costos y Presupuestos");
			$num = mysql_num_rows($err);
			$row = mysql_fetch_array($err);
			if($num > 0 and $row[0] == "off")
			{
			$query = "delete from ".$empresa."_000155 ";
			$query = $query."  where Pcpano = ".$wanop;
			$query = $query."    and Pcpemp = '".$wemp."'";
			$query = $query."    and Pcpmes = ".$wper1;
			$query = $query."    and Pcptip = 'O' ";
			$err = mysql_query($query,$conex);
			$wtable= date("YmdHis");
			$wtable=" temp_".$wtable;
			$count=0;
			$query = "Create table  IF NOT EXISTS ".$wtable." as ";
			$query = $query."select Mprcco,Mprpro,Mprgru,Mprpor,Conccn,Conpro,Congrp,Mprcon,Concoa ";
 			$query = $query." from ".$empresa."_000095,".$empresa."_000080  ";
 			$query = $query." where mpremp = '".$wemp."'";
 			$query = $query."   and mpremp = conemp ";
			$query = $query."   and mprcco = conccg ";
			$query = $query."   and mprpro = conprg  ";
			$query = $query."   and Mprgru = congru  ";
			$query = $query."   and Mprcon = concon  ";
			$query = $query."   and conano = ".$wanop;
			$query = $query."   and conmes = ".$wper1;
			$err = mysql_query($query,$conex);
			$windex="temp_".date("His");
			$query = "CREATE INDEX ".$windex." on ".$wtable."(Mprcco(4),Mprpro(10),Mprgru(4),Mprpor,Conccn(4),Conpro(10),Congrp(4),Mprcon(4))";
			$err = mysql_query($query,$conex) or die("Error en la creacion del index ".$query);
			//                  0      1      2      3     4      5      6      7
			$query = "select Mprcco,Mprpro,Mprgru,Mprpor,Conccn,Conpro,Pcpctp,Mprcon ";
			$query = $query." from ".$empresa."_000155,".$wtable;
			$query = $query." where Pcpano = ".$wanop;
			$query = $query."   and Pcpemp = '".$wemp."'";
			$query = $query."   and Pcpmes = ".$wper1;
			$query = $query."   and Pcpcco = Conccn ";
			$query = $query."   and Pcpcod = Conpro ";
			$query = $query."   and Pcpgru = Congrp ";
			$query = $query."   and Pcpcon = Concoa ";
			$query = $query."  order by mprcco,mprpro,mprgru,Mprcon ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$key1="";
			$kcco="";
			$kpro="";
			$kgru="";
			$wpor=0;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$key2=$row[0].$row[1].$row[2].$row[7];
				if($row[0].$row[1].$row[2].$row[7] != $key1)
				{
					if($i > 0)
					{
						$wtotal = $wtotal / $occur;
						$wtmn = $wtotal / (1 - $wpor);
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000155 (medico,fecha_data,hora_data,Pcpemp, Pcpano, Pcpmes, Pcpcco, Pcpcod, Pcpgru, Pcpcon, Pcppor, Pcpctp, Pcptmn, Pcppro,Pcptip,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$kcco."','".$kpro."','".$kgru."','".$kcon."',".$wpor.",".$wtotal.",".$wtmn.",0,'O','C-".$empresa."')";
						$err1 = mysql_query($query,$conex);
						if ($err1 != 1)
							echo mysql_errno().":".mysql_error()."<br>";
						$count++;
						echo "REGISTRO ACTUALIZADO NRO : ".$count."<br>";
					}
					$key1=$row[0].$row[1].$row[2].$row[7];
					$kcco=$row[0];
					$kpro=$row[1];
					$kgru=$row[2];
					$kcon=$row[7]; 
					$wpor = $row[3];
					$occur = 0;
					$wtotal = 0;
				}
				$occur++;
				$wtotal+=$row[6];
			}
			
			//ACTUALIZACION 2007-07-18
			if($occur != 0)
				$wtotal = $wtotal / $occur;
			else
				$wtotal = 0;
			$wtmn = $wtotal / (1 - $wpor);
			$fecha = date("Y-m-d");
			$hora = (string)date("H:i:s");
			if($num > 0)
			{
				$query = "insert ".$empresa."_000155 (medico,fecha_data,hora_data,Pcpemp, Pcpano, Pcpmes, Pcpcco, Pcpcod, Pcpgru, Pcpcon, Pcppor, Pcpctp, Pcptmn, Pcppro,Pcptip,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$wanop.",".$wper1.",'".$kcco."','".$kpro."','".$kgru."','".$kcon."',".$wpor.",".$wtotal.",".$wtmn.",0,'O','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				$count++;
				echo "REGISTRO ACTUALIZADO NRO : ".$count."<br>";
			}
			//****************************************************
			
			echo "TOTAL REGISTROS ACTUALIZADOS : ".$count;
			$query = "DROP table ".$wtable;
			$err = mysql_query($query,$conex);
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
