<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion Automatica De Numero de Procesos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro150.php Ver. 2016-05-13</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro150.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wmes) or !isset($wcco1)  or !isset($wcco2))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTACION</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION AUTOMATICA DE NUMERO DE PROCESOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Inicial de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Final de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco2' size=4 maxlength=4></td></tr>";
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
			$query = "delete from ".$empresa."_000091 ";
			$query .= "  where Mdrano = ".$wanop;
			$query .= "    and Mdremp = '".$wemp."' ";
			$query .= "    and Mdrmes = ".$wmes;
			$query .= "    and Mdrcco between '".$wcco1."' and '".$wcco2."'";
			$query .= "    and Mdrcod = 'NPRO' ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			
			//                   0     1      2       3         4
			$query  = "select Estano,Estmes,Estcco,Procod,sum(Estcan) from ".$empresa."_000141,".$empresa."_000100,".$empresa."_000142,".$empresa."_000095 ";
			$query .= "  where estano = ".$wanop;
			$query .= "    and estemp = '".$wemp."' ";
			$query .= "	   and estmes = ".$wmes; 
			$query .= "    and Estcco between '".$wcco1."' and '".$wcco2."'";
			$query .= "	   and estemp = proemp ";
			$query .= "	   and estcco = procco ";
			$query .= "	   and estcod = propro "; 
			$query .= "	   and estcon = procon ";
			$query .= "	   and protip = '1' ";
			$query .= "	   and proemp = rdsemp ";
			$query .= "	   and procco = rdscco ";
			$query .= "	   and procod = rdssub ";
			$query .= "	   and rdsnpr = 'on' ";
			$query .= "	   and proemp = mpremp ";
			$query .= "	   and procco = mprcco ";
			$query .= "	   and propro = mprpro ";
			$query .= "	   and progru = mprgru ";
			$query .= "	   and procon = mprcon ";
			$query .= "	   and mprest = 'on' ";
			$query .= "   group by 1,2,3,4 ";
			$query .= "   order by 1,2,3,4 ";   
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000091 (medico,fecha_data,hora_data,Mdremp, Mdrano, Mdrmes, Mdrcco, Mdrsub, Mdrcod, Mdrval, Mdrpor, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$row[0].",".$row[1].",'".$row[2]."','".$row[3]."','NPRO',".$row[4].",0,'C-".$empresa."')";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$k++;
					echo "REGISTRO INSERTADO  : ".$k."<br>";
				} 
			}
			
			//                   0     1      2       3         4
			$query  = "select Estano,Estmes,Estcco,Pqucod,sum(Estcan) from ".$empresa."_000141,".$empresa."_000099,".$empresa."_000142,".$empresa."_000095 "; 
			$query .= "  where estano = ".$wanop;
			$query .= "    and estemp = '".$wemp."' ";
			$query .= "	   and estmes = ".$wmes;  
			$query .= "    and Estcco between '".$wcco1."' and '".$wcco2."'";
			$query .= "	   and estemp = Pquemp ";
			$query .= "	   and estcco = Pqucco "; 
			$query .= "	   and estcod = Pqupro "; 
			$query .= "	   and estcon = Pqucon "; 
			$query .= "    and Pqutip = '1' "; 
			$query .= "	   and Pquemp = rdsemp ";
			$query .= "	   and Pqucco = rdscco "; 
			$query .= "    and Pqucod = rdssub "; 
			$query .= "	   and rdsnpr = 'on' "; 
			$query .= "	   and Pquemp = mpremp ";
			$query .= "	   and Pqucco = mprcco "; 
			$query .= "	   and Pqupro = mprpro "; 
			$query .= "	   and Pqugru = mprgru "; 
			$query .= "	   and Pqucon = mprcon "; 
			$query .= "	   and mprest = 'on' ";
			$query .= "  group by 1,2,3,4 ";
			$query .= "  order by 1,2,3,4 ";

			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query = "insert ".$empresa."_000091 (medico,fecha_data,hora_data,Mdremp, Mdrano, Mdrmes, Mdrcco, Mdrsub, Mdrcod, Mdrval, Mdrpor, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."',".$row[0].",".$row[1].",'".$row[2]."','".$row[3]."','NPRO',".$row[4].",0,'C-".$empresa."')";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$k++;
					echo "REGISTRO INSERTADO  : ".$k."<br>";
				} 
			}
			echo "<b>TOTAL REGISTROS INSERTADOS : ".$k."</b>";
		}
	}
?>
</body>
</html>
