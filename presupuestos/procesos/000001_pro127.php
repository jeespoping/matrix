<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Proyeccion General de Ingresos</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro127.php Ver. 1.00</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
function bisiesto($year)
{
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro127.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wper1) or $wper1 < 1 or $wper1 > 12 )
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>PROYECCION GENERAL DE INGRESOS</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A�o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Incremento</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			#PROYECCION INGRESOS UNIDADES QUIRURGICAS Y HOSPITALARIAS
			echo "<br><br><b>PROYECCION INGRESOS UNIDADES QUIRURGICAS Y HOSPITALARIAS</b><br>";
			$k=0;
			$k1=0;
			$wanopa=$wanop - 1;
			$query = "delete  from ".$empresa."_000033 ";
			$query = $query."  where dipano = ".$wanop;
			$query = $query."      and diptip = 'QH' ";
			$err = mysql_query($query,$conex);
			$query =" select Mopano,Mopmes,Mopcco,Ipllin,Iplpte,Pretip,sum(Mopcan * Iplinp) ";
			$query = $query." from ".$empresa."_000014,".$empresa."_000031,".$empresa."_000003  ";
			$query = $query." where iplano = ".$wanopa; 
			$query = $query." and iplcco in (select ccocod from ".$empresa."_000005 where ccouni = '1Q' )";
			$query = $query." and iplcco = mopcco  ";
			$query = $query." and mopano = ".$wanop;
			$query = $query." and ipllin = precod   ";
			$query = $query." group by mopano,mopmes,mopcco,ipllin,Iplpte,pretip ";
			$query = $query." union ";
			$query = $query." select Mopano,Mopmes,Mopcco,Ipllin,Iplpte,Pretip,sum(Mopcan * Iplinp) ";
			$query = $query." from ".$empresa."_000014,".$empresa."_000031,".$empresa."_000003  ";
			$query = $query." where iplano = ".$wanopa; 
			$query = $query." and iplcco in (select ccocod from ".$empresa."_000005 where ccouni = '2H' )";
			$query = $query." and iplcco = mopcco  ";
			$query = $query." and mopano = ".$wanop;
			$query = $query." and Mopcod = '12' ";
			$query = $query." and ipllin = precod   ";
			$query = $query." group by mopano,mopmes,mopcco,ipllin,Iplpte,pretip ";
			$query = $query." order by mopano,mopmes,mopcco ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."<br>");
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$query =" select Inees1, Inees2, Inees3    from ".$empresa."_000010 ";
					$query = $query." where Ineano = ".$wanop; 
					$query = $query."     and Inecco = '".$row[2]."'"; 
					$query = $query."     and Inetip = '".$row[5]."'"; 
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						$wes1=$row1[0];
						$wes2=$row1[1];
						$wes3=$row1[2];
					}
					else
					{
						$wes1=0;
						$wes2=0;
						$wes3=0;
					}
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$wit = $row[6] * $row[4] / 100;
					$wip = $row[6] - $wit;
					if($row[1] >= $wper1)
					{
						$wip1 = $wip * (1 + ($wes1 / 100));
						$wip2 = $wip * (1 + ($wes2 / 100));
						$wip3 = $wip * (1 + ($wes3 / 100));
						$wit1 = $wit * (1 + ($wes1 / 100));
						$wit2 = $wit * (1 + ($wes2 / 100));
						$wit3 = $wit * (1 + ($wes3 / 100));
					}
					else
					{
						$wip1 = $wip;
						$wip2 = $wip;
						$wip3 = $wip;
						$wit1 = $wit;
						$wit2 = $wit;
						$wit3 = $wit;
					}
					$query = "insert ".$empresa."_000033 (medico,fecha_data,hora_data,Dipano, Dipmes, Dipcco, Diplin, Dipip1, Dipit1, Dipip2, Dipit2, Dipip3, Dipit3, Diptip, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$row[1].",'".$row[2]."','".$row[3]."',".$wip1.",".$wit1.",".$wip2.",".$wit2.",".$wip3.",".$wit3.",'QH','C-".$empresa."')";
	       			$err2 = mysql_query($query,$conex);
	       			if ($err2 != 1)
						echo mysql_errno().":".mysql_error()."<br>";
					else
					{
	           			$k++;
	           			echo "REGISTRO INSERTADO  : ".$k."<br>";
   					}
   				}
   				$k1+=$k;
   				echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k."</b><br>";
			}
			
			#PROYECCION INGRESOS UNIDADES AMBULATORIAS (AUT) Y SERV. FARMACEUTICO
			echo "<br><br><b>PROYECCION INGRESOS UNIDADES AMBULATORIAS (AUT) Y SERV. FARMACEUTICO</b><br>";
			$k=0;
			$wanopa=$wanop - 1;
			$query = "delete  from ".$empresa."_000033 ";
			$query = $query."  where dipano = ".$wanop;
			$query = $query."      and diptip = 'AF' ";
			$err = mysql_query($query,$conex);
			$query = " select Mopano, Mopmes, '1050', Mopcod, sum(Mopcan)  from ".$empresa."_000031";
			$query = $query." where mopano = ".$wanop;
			$query = $query."     and Mopcco in (select ccocod from ".$empresa."_000005 where  ccouni = '2H')";
			$query = $query."     and Mopcod = '12' ";
			$query = $query." group by Mopano, Mopmes, Mopcod ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."<br>");
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query= "insert into ".$empresa."_000031(medico,Fecha_data, Hora_data, Mopano, Mopmes, Mopcco, Mopcod, Mopcan, Seguridad)  values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$row[1].",'".$row[2]."','".$row[3]."',".$row[4].",'C-".$empresa."')";
					$err2 = mysql_query($query,$conex);
	       			if ($err2 != 1)
						echo mysql_errno().":".mysql_error()."<br>";
				}
			}
			$query =" select Mopano,Mopmes,Mopcco,Ipllin,Iplpte,Pretip,sum(Mopcan * Iplinp) ";
			$query = $query." from ".$empresa."_000014,".$empresa."_000031,".$empresa."_000003  ";
			$query = $query." where iplano = ".$wanopa; 
			$query = $query." and iplcco in (select ccocod from ".$empresa."_000005 where ccouni != '1Q' and  ccouni != '2H')";
			$query = $query." and iplcco = mopcco  ";
			$query = $query." and mopano = ".$wanop;
			$query = $query." and ipllin = precod   ";
			$query = $query." group by mopano,mopmes,mopcco,ipllin,Iplpte,pretip ";
			$query = $query." order by mopano,mopmes,mopcco ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."<br>");
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$query =" select Inees1, Inees2, Inees3    from ".$empresa."_000010 ";
					$query = $query." where Ineano = ".$wanop; 
					$query = $query."     and Inecco = '".$row[2]."'"; 
					$query = $query."     and Inetip = '".$row[5]."'"; 
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						$wes1=$row1[0];
						$wes2=$row1[1];
						$wes3=$row1[2];
					}
					else
					{
						$wes1=0;
						$wes2=0;
						$wes3=0;
					}
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$wit = $row[6] * $row[4] / 100;
					$wip = $row[6] - $wit;
					if($row[1] >= $wper1)
					{
						$wip1 = $wip * (1 + ($wes1 / 100));
						$wip2 = $wip * (1 + ($wes2 / 100));
						$wip3 = $wip * (1 + ($wes3 / 100));
						$wit1 = $wit * (1 + ($wes1 / 100));
						$wit2 = $wit * (1 + ($wes2 / 100));
						$wit3 = $wit * (1 + ($wes3 / 100));
					}
					else
					{
						$wip1 = $wip;
						$wip2 = $wip;
						$wip3 = $wip;
						$wit1 = $wit;
						$wit2 = $wit;
						$wit3 = $wit;
					}
					$query = "insert ".$empresa."_000033 (medico,fecha_data,hora_data,Dipano, Dipmes, Dipcco, Diplin, Dipip1, Dipit1, Dipip2, Dipit2, Dipip3, Dipit3, Diptip, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$row[1].",'".$row[2]."','".$row[3]."',".$wip1.",".$wit1.",".$wip2.",".$wit2.",".$wip3.",".$wit3.",'AF','C-".$empresa."')";
	       			$err2 = mysql_query($query,$conex);
	       			if ($err2 != 1)
						echo mysql_errno().":".mysql_error()."<br>";
					else
					{
	           			$k++;
	           			echo "REGISTRO INSERTADO  : ".$k."<br>";
   					}
   				}
   				$query = "delete  from ".$empresa."_000031 ";
				$query = $query."  where Mopano = ".$wanop;
				$query = $query."      and Mopcco = '1050' ";
				$err = mysql_query($query,$conex);
				$k1+=$k;
   				echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k."</b><br>";
			}
			
			#PROYECCION INGRESOS UNIDADES EN PESOS
			echo "<br><br><b>PROYECCION INGRESOS UNIDADES EN PESOS</b><br>";
			$k=0;
			$wanopa=$wanop - 1;
			$query = "delete  from ".$empresa."_000033 ";
			$query = $query."  where dipano = ".$wanop;
			$query = $query."      and diptip = 'PE' ";
			$err = mysql_query($query,$conex);
			$query =" select Ippano,Ippmes,Ippcco,Ipplin,Inppte,Pretip,sum(Ippipp) ";
			$query = $query." from ".$empresa."_000030,".$empresa."_000018,".$empresa."_000003  ";
			$query = $query." where Ippano = ".$wanop; 
			$query = $query." and Inpano = ".$wanopa;
			$query = $query." and Ippmes = Inpmes ";
			$query = $query." and Ippcco = Inpcco   ";
			$query = $query." and Ipplin = Inplin   ";
			$query = $query." and Ipplin = precod   ";
			$query = $query." group by Ippano,Ippmes,Ippcco,Ipplin,Inppte,Pretip ";
			$query = $query." order by Ippano,Ippmes,Ippcco ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."<br>");
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$query =" select Inees1, Inees2, Inees3    from ".$empresa."_000010 ";
					$query = $query." where Ineano = ".$wanop; 
					$query = $query."     and Inecco = '".$row[2]."'"; 
					$query = $query."     and Inetip = '".$row[5]."'"; 
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						$wes1=$row1[0];
						$wes2=$row1[1];
						$wes3=$row1[2];
					}
					else
					{
						$wes1=0;
						$wes2=0;
						$wes3=0;
					}
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$wit = $row[6] * $row[4] / 100;
					$wip = $row[6] - $wit;
					if($row[1] >= $wper1)
					{
						$wip1 = $wip * (1 + ($wes1 / 100));
						$wip2 = $wip * (1 + ($wes2 / 100));
						$wip3 = $wip * (1 + ($wes3 / 100));
						$wit1 = $wit * (1 + ($wes1 / 100));
						$wit2 = $wit * (1 + ($wes2 / 100));
						$wit3 = $wit * (1 + ($wes3 / 100));
					}
					else
					{
						$wip1 = $wip;
						$wip2 = $wip;
						$wip3 = $wip;
						$wit1 = $wit;
						$wit2 = $wit;
						$wit3 = $wit;
					}
					$query = "insert ".$empresa."_000033 (medico,fecha_data,hora_data,Dipano, Dipmes, Dipcco, Diplin, Dipip1, Dipit1, Dipip2, Dipit2, Dipip3, Dipit3, Diptip, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$row[1].",'".$row[2]."','".$row[3]."',".$wip1.",".$wit1.",".$wip2.",".$wit2.",".$wip3.",".$wit3.",'PE','C-".$empresa."')";
	       			$err2 = mysql_query($query,$conex);
	       			if ($err2 != 1)
						echo mysql_errno().":".mysql_error()."<br>";
					else
					{
	           			$k++;
	           			echo "REGISTRO INSERTADO  : ".$k."<br>";
   					}
   				}
   				$k1+=$k;
   				echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k."</b><br>";
			}
			
			#PROYECCION INGRESOS LABORATORIO
			echo "<br><br><b>PROYECCION INGRESOS LABORATORIO</b><br>";
			$k=0;
			$wanopa=$wanop - 1;
			$query = "delete  from ".$empresa."_000033 ";
			$query = $query."  where dipano = ".$wanop;
			$query = $query."      and diptip = 'LA' ";
			$err = mysql_query($query,$conex);
			$wtable= date("YmdHis");
			$wtable=" temp_".$wtable;
			$query = "Create table  IF NOT EXISTS ".$wtable." as ";
			$query = $query." select Mopano,Mopmes,'3081' as mopcco,sum(Labinp * Mopcan) as labinp,sum(Labter) as labter ";
			$query = $query." from ".$empresa."_000031,".$empresa."_000017  ";
			$query = $query." where Mopano = ".$wanop; 
			$query = $query." and Labano = ".$wanopa;
			$query = $query." and Mopcco in (select ccocod from ".$empresa."_000005 where ccouni != '2H') ";
			$query = $query." and Mopcco = Labcco   ";
			$query = $query." group by Mopano,Mopmes ";
			$query = $query." union ";
			$query = $query." select Mopano,Mopmes,'3081' as mopcco,sum(Labinp * Mopcan) as labinp,sum(Labter) as labter ";
			$query = $query." from ".$empresa."_000031,".$empresa."_000017  ";
			$query = $query." where Mopano = ".$wanop; 
			$query = $query." and Labano = ".$wanopa;
			$query = $query." and Mopcco in (select ccocod from ".$empresa."_000005 where ccouni = '2H') ";
			$query = $query." and Mopcod = '12' ";
			$query = $query." and Mopcco = Labcco   ";
			$query = $query." group by Mopano,Mopmes ";
			$query = $query." union ";
			$query = $query." select ".$wanop." as mopano,1 as mopmes,'3081' as mopcco,Labinp ,Labter  from ".$empresa."_000017 ";
			$query = $query." where  Labano = ".$wanopa;
			$query = $query."     and  Labcco = '99' ";
			$query = $query." group by Mopano,Mopmes ";
			$query = $query." union ";
			$query = $query." select ".$wanop." as mopano,2 as mopmes,'3081' as mopcco,Labinp ,Labter  from ".$empresa."_000017 ";
			$query = $query." where  Labano = ".$wanopa;
			$query = $query."     and  Labcco = '99' ";
			$query = $query." group by Mopano,Mopmes ";
			$query = $query." union ";
			$query = $query." select ".$wanop." as mopano,3 as mopmes,'3081' as mopcco,Labinp ,Labter  from ".$empresa."_000017 ";
			$query = $query." where  Labano = ".$wanopa;
			$query = $query."     and  Labcco = '99' ";
			$query = $query." group by Mopano,Mopmes ";
			$query = $query." union ";
			$query = $query." select ".$wanop." as mopano,4 as mopmes,'3081' as mopcco,Labinp ,Labter  from ".$empresa."_000017 ";
			$query = $query." where  Labano = ".$wanopa;
			$query = $query."     and  Labcco = '99' ";
			$query = $query." group by Mopano,Mopmes ";
			$query = $query." union ";
			$query = $query." select ".$wanop." as mopano,5 as mopmes,'3081' as mopcco,Labinp ,Labter  from ".$empresa."_000017 ";
			$query = $query." where  Labano = ".$wanopa;
			$query = $query."     and  Labcco = '99' ";
			$query = $query." group by Mopano,Mopmes ";
			$query = $query." union ";
			$query = $query." select ".$wanop." as mopano,6 as mopmes,'3081' as mopcco,Labinp ,Labter  from ".$empresa."_000017 ";
			$query = $query." where  Labano = ".$wanopa;
			$query = $query."     and  Labcco = '99' ";
			$query = $query." group by Mopano,Mopmes ";
			$query = $query." union ";
			$query = $query." select ".$wanop." as mopano,7 as mopmes,'3081' as mopcco,Labinp ,Labter  from ".$empresa."_000017 ";
			$query = $query." where  Labano = ".$wanopa;
			$query = $query."     and  Labcco = '99' ";
			$query = $query." group by Mopano,Mopmes ";
			$query = $query." union ";
			$query = $query." select ".$wanop." as mopano,8 as mopmes,'3081' as mopcco,Labinp ,Labter  from ".$empresa."_000017 ";
			$query = $query." where  Labano = ".$wanopa;
			$query = $query."     and  Labcco = '99' ";
			$query = $query." group by Mopano,Mopmes ";
			$query = $query." union ";
			$query = $query." select ".$wanop." as mopano,9 as mopmes,'3081' as mopcco,Labinp ,Labter  from ".$empresa."_000017 ";
			$query = $query." where  Labano = ".$wanopa;
			$query = $query."     and  Labcco = '99' ";
			$query = $query." group by Mopano,Mopmes ";
			$query = $query." union ";
			$query = $query." select ".$wanop." as mopano,10 as mopmes,'3081' as mopcco,Labinp ,Labter  from ".$empresa."_000017 ";
			$query = $query." where  Labano = ".$wanopa;
			$query = $query."     and  Labcco = '99' ";
			$query = $query." group by Mopano,Mopmes ";
			$query = $query." union ";
			$query = $query." select ".$wanop." as mopano,11 as mopmes,'3081' as mopcco,Labinp ,Labter  from ".$empresa."_000017 ";
			$query = $query." where  Labano = ".$wanopa;
			$query = $query."     and  Labcco = '99' ";
			$query = $query." group by Mopano,Mopmes ";
			$query = $query." union ";
			$query = $query." select ".$wanop." as mopano,12 as mopmes,'3081' as mopcco,Labinp ,Labter  from ".$empresa."_000017 ";
			$query = $query." where  Labano = ".$wanopa;
			$query = $query."     and  Labcco = '99' ";
			$query = $query." group by Mopano,Mopmes ";
			$query = $query." order by Mopano,Mopmes	 ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."<br>");
			$query = "select mopano,mopmes,mopcco,sum(labinp),sum(labter) from ".$wtable." group by mopano,mopmes,mopcco";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."<br>");
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$query =" select Inees1, Inees2, Inees3    from ".$empresa."_000010 ";
					$query = $query." where Ineano = ".$wanop; 
					$query = $query."     and Inecco = '".$row[2]."'"; 
					$query = $query."     and Inetip = 'H' "; 
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						$wes1=$row1[0];
						$wes2=$row1[1];
						$wes3=$row1[2];
					}
					else
					{
						$wes1=0;
						$wes2=0;
						$wes3=0;
					}
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$wit = $row[3] * $row[4] / 100;
					$wip = $row[3] - $wit;
					if($row[1] >= $wper1)
					{
						$wip1 = $wip * (1 + ($wes1 / 100));
						$wip2 = $wip * (1 + ($wes2 / 100));
						$wip3 = $wip * (1 + ($wes3 / 100));
						$wit1 = $wit * (1 + ($wes1 / 100));
						$wit2 = $wit * (1 + ($wes2 / 100));
						$wit3 = $wit * (1 + ($wes3 / 100));
					}
					else
					{
						$wip1 = $wip;
						$wip2 = $wip;
						$wip3 = $wip;
						$wit1 = $wit;
						$wit2 = $wit;
						$wit3 = $wit;
					}
					$query = "insert ".$empresa."_000033 (medico,fecha_data,hora_data,Dipano, Dipmes, Dipcco, Diplin, Dipip1, Dipit1, Dipip2, Dipit2, Dipip3, Dipit3, Diptip, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$row[1].",'".$row[2]."','05',".$wip1.",".$wit1.",".$wip2.",".$wit2.",".$wip3.",".$wit3.",'LA','C-".$empresa."')";
	       			$err2 = mysql_query($query,$conex);
	       			if ($err2 != 1)
						echo mysql_errno().":".mysql_error()."<br>";
					else
					{
	           			$k++;
	           			echo "REGISTRO INSERTADO  : ".$k."<br>";
   					}
   				}
   				$k1+=$k;
   				echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k."</b><br>";
   				$query = "DROP table ".$wtable;
				$err = mysql_query($query,$conex);
			}
			
			#PROYECCION INGRESOS UNIDADES MANUALES
			echo "<br><br><b>PROYECCION INGRESOS UNIDADES MANUALES</b><br>";
			$k=0;
			$wanopa=$wanop - 1;
			$query = "delete  from ".$empresa."_000033 ";
			$query = $query."  where dipano = ".$wanop;
			$query = $query."      and diptip = 'MA' ";
			$err = mysql_query($query,$conex);
			$query =" select Mopano,Mopmes,Mopcco,Ipmlin,Pretip,sum(Ipminp * Mopcan),sum(Ipminp * Mopcan *(Ipmpte/100)) ";
			$query = $query." from ".$empresa."_000041,".$empresa."_000031,".$empresa."_000003  ";
			$query = $query." where Ipmano = ".$wanopa; 
			$query = $query." and Ipmcco = mopcco  ";
			$query = $query." and Ipmgru = Mopcod  ";
			$query = $query." and mopano = ".$wanop;
			$query = $query." and Ipmlin = precod   ";
			$query = $query." group by Mopano,Mopmes,Mopcco,Ipmlin,Pretip ";
			$query = $query." order by Mopano,Mopmes,Mopcco ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error()."<br>");
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$query =" select Inees1, Inees2, Inees3    from ".$empresa."_000010 ";
					$query = $query." where Ineano = ".$wanop; 
					$query = $query."     and Inecco = '".$row[2]."'"; 
					$query = $query."     and Inetip = '".$row[4]."'"; 
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						$row1 = mysql_fetch_array($err1);
						$wes1=$row1[0];
						$wes2=$row1[1];
						$wes3=$row1[2];
					}
					else
					{
						$wes1=0;
						$wes2=0;
						$wes3=0;
					}
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$wit = $row[6] ;
					$wip = $row[5] - $wit;
					if($row[1] >= $wper1)
					{
						$wip1 = $wip * (1 + ($wes1 / 100));
						$wip2 = $wip * (1 + ($wes2 / 100));
						$wip3 = $wip * (1 + ($wes3 / 100));
						$wit1 = $wit * (1 + ($wes1 / 100));
						$wit2 = $wit * (1 + ($wes2 / 100));
						$wit3 = $wit * (1 + ($wes3 / 100));
					}
					else
					{
						$wip1 = $wip;
						$wip2 = $wip;
						$wip3 = $wip;
						$wit1 = $wit;
						$wit2 = $wit;
						$wit3 = $wit;
					}
					$query = "insert ".$empresa."_000033 (medico,fecha_data,hora_data,Dipano, Dipmes, Dipcco, Diplin, Dipip1, Dipit1, Dipip2, Dipit2, Dipip3, Dipit3, Diptip, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$wanop.",".$row[1].",'".$row[2]."','".$row[3]."',".$wip1.",".$wit1.",".$wip2.",".$wit2.",".$wip3.",".$wit3.",'MA','C-".$empresa."')";
	       			$err2 = mysql_query($query,$conex);
	       			if ($err2 != 1)
						echo mysql_errno().":".mysql_error()."<br>";
					else
					{
	           			$k++;
	           			echo "REGISTRO INSERTADO  : ".$k."<br>";
   					}
   				}
   				$k1+=$k;
   				echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k."</b><br>";
   				echo "<br><br><b>TOTAL REGISTROS INSERTADOS : ".$k1."</b><br>";
			}
   		}
}		
?>
</body>
</html>
