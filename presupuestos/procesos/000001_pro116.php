<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Proyeccion de Ingresos y Costos Variables (T112 + T33 = T43)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro116.php Ver. 2017-11-23</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro116.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wesc) or $wesc < 1 or $wesc > 3  or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>PROYECCION DE INGRESOS Y COSTOS VARIABLES (T112 + T33 = T43)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Escenario (1 o 2 o 3)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wesc' size=1 maxlength=1></td></tr>";
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
			$k=0;
			$wanopa=$wanop - 1;
			$query = "delete from ".$empresa."_000043 ";
			$query = $query."  where resano = ".$wanop;
			$query = $query."    and resemp = '".$wemp."' ";
			$query = $query."    and resind = '116'";
			$err = mysql_query($query,$conex);
			switch ($wesc)
			{
				case 1:
					$query = "select Dipcco,Mcvcpr,Dipano,Dipmes,sum((Mcvval/100)* Dipip1) ";
				break;
				case 2:
					$query = "select Dipcco,Mcvcpr,Dipano,Dipmes,sum((Mcvval/100)* Dipip2) ";
				break;
				case 3:
					$query = "select Dipcco,Mcvcpr,Dipano,Dipmes,sum((Mcvval/100)* Dipip3) ";
				break;
			}
			$query = $query."  from ".$empresa."_000112,".$empresa."_000033  ";
			$query = $query."  where mcvano = ".$wanopa;
			$query = $query."    and mcvemp = '".$wemp."' ";
			$query = $query."    and mcvcpr ='200' and mcvtip = '0' ";
			$query = $query."    and dipano = ".$wanop; 
			$query = $query."    and mcvcco = dipcco  ";
			$query = $query."    and mcvemp = dipemp  ";
			$query = $query."  group by dipcco,mcvcpr,dipano,dipmes ";
			$query = $query."  union ";
			switch ($wesc)
			{
				case 1:
					$query = $query."  select Dipcco,Mcvcpr,Dipano,Dipmes,sum((Mcvval/100)* Dipip1) ";
				break;
				case 2:
					$query = $query."  select Dipcco,Mcvcpr,Dipano,Dipmes,sum((Mcvval/100)* Dipip2) ";
				break;
				case 3:
					$query = $query."  select Dipcco,Mcvcpr,Dipano,Dipmes,sum((Mcvval/100)* Dipip3) ";
				break;
			}
			$query = $query."  from ".$empresa."_000112,".$empresa."_000033  ";
			$query = $query."  where mcvano = ".$wanopa; 
			$query = $query."    and mcvemp = '".$wemp."' ";
			$query = $query."    and ((mcvcpr = '200' and mcvtip = '1')  ";
			$query = $query."     or  mcvcpr = '202'  ";
			$query = $query."     or  mcvcpr = '218'  ";
			$query = $query."     or  mcvcpr = '248'  ";
			$query = $query."     or  mcvcpr = '250'  ";
			$query = $query."     or mcvcpr = '257') ";
			$query = $query."    and dipano =".$wanop;
			$query = $query."    and mcvcco = dipcco   ";
			$query = $query."    and mcvemp = dipemp  ";
			$query = $query."  group by dipcco,mcvcpr,dipano,dipmes "; 
			$query = $query."  union ";
			$query = $query."  select Mopcco,Mcvcpr,Mopano,Mopmes,sum(Mcvval * Mopcan)  ";
			$query = $query."  from ".$empresa."_000112,".$empresa."_000031  ";
			$query = $query."  where mcvano = ".$wanopa; 
			$query = $query."    and mcvemp = '".$wemp."' ";
			$query = $query."    and (mcvcpr = '217'  ";
			$query = $query."     or  mcvcpr = '235')  ";
			$query = $query."    and mcvcco in (select ccocod from ".$empresa."_000005 where ccouni = '2H' and ccoemp = '".$wemp."') ";
			$query = $query."    and Mopano = ".$wanop;
			$query = $query."    and mcvcco = mopcco  ";
			$query = $query."    and mcvemp = mopemp  ";
			$query = $query."    and Mopcod = '12' ";
			$query = $query."  group by Mopcco,Mcvcpr,Mopano,Mopmes ";
			$query = $query."  union ";
			$query = $query."  select Mopcco,Mcvcpr,Mopano,Mopmes,sum(Mcvval * Mopcan)  ";
			$query = $query."  from ".$empresa."_000112,".$empresa."_000031  ";
			$query = $query."  where mcvano = ".$wanopa; 
			$query = $query."    and mcvemp = '".$wemp."' ";
			$query = $query."    and (mcvcpr = '217'  ";
			$query = $query."     or  mcvcpr = '235')  ";
			$query = $query."    and mcvcco not in (select ccocod from ".$empresa."_000005 where ccouni = '2H' and ccoemp = '".$wemp."') ";
			$query = $query."    and Mopano = ".$wanop;
			$query = $query."    and mcvcco = mopcco  ";
			$query = $query."    and mcvemp = mopemp  ";
			$query = $query."  group by Mopcco,Mcvcpr,Mopano,Mopmes ";
			$err = mysql_query($query,$conex)  or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[1] == '217' or $row[1] == '235')
				{
					$query = "SELECT Icgpor, Icgper  from ".$empresa."_000046 ";
					$query = $query."  where Icgano = ".$wanop;
					$query = $query."    and Icgemp = '".$wemp."' ";
					$query = $query."    and  (Icgcpr = '217' ";
					$query = $query."     or   Icgcpr = '235' )";
					$err2 = mysql_query($query,$conex);
					$num2 = mysql_num_rows($err2);
					$row2 = mysql_fetch_array($err2);
					if($row[3] >= $row2[1])
						$row[4] = $row[4] * (1 + $row2[0] / 100);
				}
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Resemp, Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."','".$row[1]."',".$row[2].",".$row[3].",".$row[4].",'116','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
           			$k++;
           			echo "REGISTRO INSERTADO  : ".$k."<br>";
				}
    		}

			$query = "  select Mcvcco,Mcvval ";
			$query = $query."  from ".$empresa."_000112  ";
			$query = $query."  where mcvano = ".$wanopa; 
			$query = $query."    and mcvemp = '".$wemp."' ";
			$query = $query."    and (mcvcpr = '200' and mcvtip = '2')  ";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$cco = $row[0];
			$val1 = $row[1];

			switch ($wesc)
			{
				case 1:
					$query = "select Dipano,Dipmes,sum(Dipip1) ";
				break;
				case 2:
					$query = "select Dipano,Dipmes,sum(Dipip2) ";
				break;
				case 3:
					$query = "select Dipano,Dipmes,sum(Dipip3) ";
				break;
			}
			$query = $query."  from ".$empresa."_000033  ";
			$query = $query."  where dipano =".$wanop;
			$query = $query."    and dipemp = '".$wemp."' ";
			$query = $query."  group by dipano,dipmes ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$val = ($val1 / 100) * $row[2];
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Resemp, Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$cco."','200',".$row[0].",".$row[1].",".$val.",'116','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
           			$k++;
           			echo "REGISTRO INSERTADO  : ".$k."<br>";
				}
			}

			$query = "  select Mcvcco,Mcvval ";
			$query = $query."  from ".$empresa."_000112  ";
			$query = $query."  where mcvano = ".$wanopa; 
			$query = $query."    and mcvemp = '".$wemp."' ";
			$query = $query."    and (mcvcpr = '364' and mcvtip = '0')  ";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$cco = $row[0];
			$val1 = $row[1];

			switch ($wesc)
			{
				case 1:
					$query = "select Dipano,Dipmes,sum(Dipip1) ";
				break;
				case 2:
					$query = "select Dipano,Dipmes,sum(Dipip2) ";
				break;
				case 3:
					$query = "select Dipano,Dipmes,sum(Dipip3) ";
				break;
			}
			$query = $query."  from ".$empresa."_000033  ";
			$query = $query."  where dipano =".$wanop;
			$query = $query."    and dipemp = '".$wemp."' ";
			$query = $query."  group by dipano,dipmes ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$val = ($val1 / 100) * $row[2];
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Resemp, Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$cco."','364',".$row[0].",".$row[1].",".$val.",'116','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
           			$k++;
           			echo "REGISTRO INSERTADO  : ".$k."<br>";
				}
			}

			$query = "  select Mcvcco,Mcvval ";
			$query = $query."  from ".$empresa."_000112  ";
			$query = $query."  where mcvano = ".$wanopa; 
			$query = $query."    and mcvemp = '".$wemp."' ";
			$query = $query."    and (mcvcpr = '600' and mcvtip = '0')  ";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$cco = $row[0];
			$val1 = $row[1];

			switch ($wesc)
			{
				case 1:
					$query = "select Dipano,Dipmes,sum(Dipip1) ";
				break;
				case 2:
					$query = "select Dipano,Dipmes,sum(Dipip2) ";
				break;
				case 3:
					$query = "select Dipano,Dipmes,sum(Dipip3) ";
				break;
			}
			$query = $query."  from ".$empresa."_000033  ";
			$query = $query."  where dipano =".$wanop;
			$query = $query."    and dipemp = '".$wemp."' ";
			$query = $query."  group by dipano,dipmes ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$val = ($val1 / 100) * $row[2];
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Resemp, Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$cco."','600',".$row[0].",".$row[1].",".$val.",'116','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
           			$k++;
           			echo "REGISTRO INSERTADO  : ".$k."<br>";
				}
			}

			switch ($wesc)
			{
				case 1:
					$query = "select Dipcco,Mcvcpr,Dipano,Dipmes,sum((Mcvval/100)* Dipip1) ";
				break;
				case 2:
					$query = "select Dipcco,Mcvcpr,Dipano,Dipmes,sum((Mcvval/100)* Dipip2) ";
				break;
				case 3:
					$query = "select Dipcco,Mcvcpr,Dipano,Dipmes,sum((Mcvval/100)* Dipip3) ";
				break;
			}
			$query = $query."  from ".$empresa."_000112,".$empresa."_000033  ";
			$query = $query."  where mcvano = ".$wanopa; 
			$query = $query."    and mcvemp = '".$wemp."' ";
			$query = $query."    and (mcvcpr = '750' and mcvtip = '0')  ";
			$query = $query."    and dipano =".$wanop;
			$query = $query."    and mcvcco = dipcco   ";
			$query = $query."    and mcvemp = dipemp   ";
			$query = $query."  group by dipcco,mcvcpr,dipano,dipmes "; 
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Resemp, Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."','290',".$row[2].",".$row[3].",".$row[4].",'116','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
           			$k++;
           			echo "REGISTRO INSERTADO  : ".$k."<br>";
				}
			}

			$query = "  select Mcvcco,Mcvval ";
			$query = $query."  from ".$empresa."_000112  ";
			$query = $query."  where mcvano = ".$wanopa; 
			$query = $query."    and mcvemp = '".$wemp."' ";
			$query = $query."    and (mcvcpr = '295' and mcvtip = '0')  ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$row = mysql_fetch_array($err);
			$cco = $row[0];
			$val1 = $row[1];

			$query = "select Resano,Resper,sum(Resmon) ";
			$query = $query."  from ".$empresa."_000043  ";
			$query = $query."  where Resano =".$wanop;
			$query = $query."    and Resemp = '".$wemp."' ";
			$query = $query."    and (Rescpr = '201')  ";
			$query = $query."  group by Resano,Resper ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$val = ($val1 / 100) * $row[2];
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Resemp, Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$cco."','295',".$row[0].",".$row[1].",".$val.",'116','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
           			$k++;
           			echo "REGISTRO INSERTADO  : ".$k."<br>";
				}
			}
			
			$query = "  select Mcvcco,Mcvval ";
			$query = $query."  from ".$empresa."_000112  ";
			$query = $query."  where mcvano = ".$wanopa; 
			$query = $query."    and mcvemp = '".$wemp."' ";
			$query = $query."    and (mcvcpr = '395' and mcvtip = '0')  ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$row = mysql_fetch_array($err);
			$cco = $row[0];
			$val1 = $row[1];

			$query = "select Resano,Resper,sum(Resmon) ";
			$query = $query."  from ".$empresa."_000043  ";
			$query = $query."  where Resano =".$wanop;
			$query = $query."    and Resemp = '".$wemp."' ";
			$query = $query."    and (Rescpr = '301')  ";
			$query = $query."  group by Resano,Resper ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$val = ($val1 / 100) * $row[2];
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Resemp, Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$cco."','395',".$row[0].",".$row[1].",".$val.",'116','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
           			$k++;
           			echo "REGISTRO INSERTADO  : ".$k."<br>";
				}
			}
			
			$query = "  select Mcvcco,Mcvval ";
			$query = $query."  from ".$empresa."_000112  ";
			$query = $query."  where mcvano = ".$wanopa; 
			$query = $query."    and mcvemp = '".$wemp."' ";
			$query = $query."    and (mcvcpr = '895' and mcvtip = '0')  ";
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$row = mysql_fetch_array($err);
			$cco = $row[0];
			$val1 = $row[1];

			$query = "select Resano,Resper,sum(Resmon) ";
			$query = $query."  from ".$empresa."_000043  ";
			$query = $query."  where Resano =".$wanop;
			$query = $query."    and Resemp = '".$wemp."' ";
			$query = $query."    and (Rescpr = '801')  ";
			$query = $query."  group by Resano,Resper ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$val = ($val1 / 100) * $row[2];
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Resemp, Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$cco."','895',".$row[0].",".$row[1].",".$val.",'116','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
           			$k++;
           			echo "REGISTRO INSERTADO  : ".$k."<br>";
				}
			}
			
			$query = "  select Mcvcco,Mcvval ";
			$query = $query."  from ".$empresa."_000112  ";
			$query = $query."  where mcvano = ".$wanopa; 
			$query = $query."    and mcvemp = '".$wemp."' ";
			$query = $query."    and (mcvcpr = '296' and mcvtip = '0')  ";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$cco = $row[0];
			$val1 = $row[1];

			switch ($wesc)
			{
				case 1:
					$query = "select Dipano,Dipmes,sum(Dipip1) ";
				break;
				case 2:
					$query = "select Dipano,Dipmes,sum(Dipip2) ";
				break;
				case 3:
					$query = "select Dipano,Dipmes,sum(Dipip3) ";
				break;
			}
			$query = $query."  from ".$empresa."_000033  ";
			$query = $query."  where dipano =".$wanop;
			$query = $query."    and dipemp = '".$wemp."' ";
			$query = $query."  group by dipano,dipmes ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$val = ($val1 / 100) * $row[2];
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Resemp, Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$cco."','296',".$row[0].",".$row[1].",".$val.",'116','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
           			$k++;
           			echo "REGISTRO INSERTADO  : ".$k."<br>";
				}
			}
			
			$query = "  select Mcvcco,Mcvval ";
			$query = $query."  from ".$empresa."_000112  ";
			$query = $query."  where mcvano = ".$wanopa; 
			$query = $query."    and mcvemp = '".$wemp."' ";
			$query = $query."    and (mcvcpr = '102' and mcvtip = '0')  ";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$cco = $row[0];
			$val1 = $row[1];

			switch ($wesc)
			{
				case 1:
					$query = "select Dipano,Dipmes,sum(Dipip1) ";
				break;
				case 2:
					$query = "select Dipano,Dipmes,sum(Dipip2) ";
				break;
				case 3:
					$query = "select Dipano,Dipmes,sum(Dipip3) ";
				break;
			}
			$query = $query."  from ".$empresa."_000033  ";
			$query = $query."  where dipano =".$wanop;
			$query = $query."    and dipemp = '".$wemp."' ";
			$query = $query."  group by dipano,dipmes ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$val = ($val1 / 100) * $row[2];
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Resemp, Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$cco."','102',".$row[0].",".$row[1].",".$val.",'116','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
           			$k++;
           			echo "REGISTRO INSERTADO  : ".$k."<br>";
				}
			}
			
			
			$query = "  select Mcvcco,Mcvval ";
			$query = $query."  from ".$empresa."_000112  ";
			$query = $query."  where mcvano = ".$wanopa; 
			$query = $query."    and mcvemp = '".$wemp."' ";
			$query = $query."    and (mcvcpr = '112' and mcvtip = '0')  ";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$cco = $row[0];
			$val1 = $row[1];

			switch ($wesc)
			{
				case 1:
					$query = "select Dipano,Dipmes,sum(Dipip1) ";
				break;
				case 2:
					$query = "select Dipano,Dipmes,sum(Dipip2) ";
				break;
				case 3:
					$query = "select Dipano,Dipmes,sum(Dipip3) ";
				break;
			}
			$query = $query."  from ".$empresa."_000033  ";
			$query = $query."  where dipano =".$wanop;
			$query = $query."    and dipemp = '".$wemp."' ";
			$query = $query."  group by dipano,dipmes ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$val = ($val1 / 100) * $row[2];
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Resemp, Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$cco."','112',".$row[0].",".$row[1].",".$val.",'116','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
           			$k++;
           			echo "REGISTRO INSERTADO  : ".$k."<br>";
				}
			}
			
			$query = "  select Mcvcco,Mcvval ";
			$query = $query."  from ".$empresa."_000112  ";
			$query = $query."  where mcvano = ".$wanopa; 
			$query = $query."    and mcvemp = '".$wemp."' ";
			$query = $query."    and (mcvcpr = '103' and mcvtip = '0')  ";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$cco = $row[0];
			$val1 = $row[1];

			switch ($wesc)
			{
				case 1:
					$query = "select Dipano,Dipmes,sum(Dipip1) ";
				break;
				case 2:
					$query = "select Dipano,Dipmes,sum(Dipip2) ";
				break;
				case 3:
					$query = "select Dipano,Dipmes,sum(Dipip3) ";
				break;
			}
			$query = $query."  from ".$empresa."_000033  ";
			$query = $query."  where dipano =".$wanop;
			$query = $query."    and dipemp = '".$wemp."' ";
			$query = $query."  group by dipano,dipmes ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$val = ($val1 / 100) * $row[2];
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Resemp, Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$cco."','103',".$row[0].",".$row[1].",".$val.",'116','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
           			$k++;
           			echo "REGISTRO INSERTADO  : ".$k."<br>";
				}
			}
			
			$query = "  select Mcvcco,Mcvval ";
			$query = $query."  from ".$empresa."_000112  ";
			$query = $query."  where mcvano = ".$wanopa; 
			$query = $query."    and mcvemp = '".$wemp."' ";
			$query = $query."    and (mcvcpr = '113' and mcvtip = '0')  ";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$cco = $row[0];
			$val1 = $row[1];

			switch ($wesc)
			{
				case 1:
					$query = "select Dipano,Dipmes,sum(Dipip1) ";
				break;
				case 2:
					$query = "select Dipano,Dipmes,sum(Dipip2) ";
				break;
				case 3:
					$query = "select Dipano,Dipmes,sum(Dipip3) ";
				break;
			}
			$query = $query."  from ".$empresa."_000033  ";
			$query = $query."  where dipano =".$wanop;
			$query = $query."    and dipemp = '".$wemp."' ";
			$query = $query."  group by dipano,dipmes ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$val = ($val1 / 100) * $row[2];
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Resemp, Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$cco."','113',".$row[0].",".$row[1].",".$val.",'116','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
           			$k++;
           			echo "REGISTRO INSERTADO  : ".$k."<br>";
				}
			}
			
			$query = "  select Mcvcco,Mcvval ";
			$query = $query."  from ".$empresa."_000112  ";
			$query = $query."  where mcvano = ".$wanopa; 
			$query = $query."    and mcvemp = '".$wemp."' ";
			$query = $query."    and (mcvcpr = '104' and mcvtip = '0')  ";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$cco = $row[0];
			$val1 = $row[1];

			switch ($wesc)
			{
				case 1:
					$query = "select Dipano,Dipmes,sum(Dipip1) ";
				break;
				case 2:
					$query = "select Dipano,Dipmes,sum(Dipip2) ";
				break;
				case 3:
					$query = "select Dipano,Dipmes,sum(Dipip3) ";
				break;
			}
			$query = $query."  from ".$empresa."_000033  ";
			$query = $query."  where dipano =".$wanop;
			$query = $query."    and dipemp = '".$wemp."' ";
			$query = $query."  group by dipano,dipmes ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$val = ($val1 / 100) * $row[2];
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Resemp, Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$cco."','104',".$row[0].",".$row[1].",".$val.",'116','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
           			$k++;
           			echo "REGISTRO INSERTADO  : ".$k."<br>";
				}
			}
			
			$query = "  select Mcvcco,Mcvval ";
			$query = $query."  from ".$empresa."_000112  ";
			$query = $query."  where mcvano = ".$wanopa; 
			$query = $query."    and mcvemp = '".$wemp."' ";
			$query = $query."    and (mcvcpr = '425' and mcvtip = '0')  ";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$cco = $row[0];
			$val1 = $row[1];

			switch ($wesc)
			{
				case 1:
					$query = "select Dipano,Dipmes,sum(Dipip1) ";
				break;
				case 2:
					$query = "select Dipano,Dipmes,sum(Dipip2) ";
				break;
				case 3:
					$query = "select Dipano,Dipmes,sum(Dipip3) ";
				break;
			}
			$query = $query."  from ".$empresa."_000033  ";
			$query = $query."  where dipano =".$wanop;
			$query = $query."    and dipemp = '".$wemp."' ";
			$query = $query."  group by dipano,dipmes ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$val = ($val1 / 100) * $row[2];
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Resemp, Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$cco."','425',".$row[0].",".$row[1].",".$val.",'116','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
           			$k++;
           			echo "REGISTRO INSERTADO  : ".$k."<br>";
				}
			}
			
			
			
			$query = "select Rescco,Mcvcpr,Resano,Resper,sum((Mcvval/100)* Resmon) ";
			$query = $query."  from ".$empresa."_000112,".$empresa."_000043  ";
			$query = $query."  where mcvano = ".$wanopa; 
			$query = $query."    and mcvemp = '".$wemp."' ";
			$query = $query."    and mcvcco != '2048' ";
			$query = $query."    and (mcvcpr = '411' and mcvtip = '0')  ";
			$query = $query."    and Resano =".$wanop;
			$query = $query."    and mcvcco = Rescco   ";
			$query = $query."    and mcvemp = Resemp   ";
			$query = $query."    and Rescpr = '200' ";
			$query = $query."  group by Rescco,mcvcpr,Resano,Resper "; 
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
        		$query = "insert ".$empresa."_000043 (medico,fecha_data,hora_data,Resemp, Rescco, Rescpr, Resano ,Resper, Resmon ,Resind ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."','".$row[1]."',".$row[2].",".$row[3].",".$row[4].",'116','C-".$empresa."')";
				$err1 = mysql_query($query,$conex);
				if ($err1 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
           			$k++;
           			echo "REGISTRO INSERTADO  : ".$k."<br>";
				}
			}
			echo "<b>REGISTROS INSERTADOS : ".$k."</b>";
		}
	}
?>
</body>
</html>
