<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion Diaria de Habilitacion</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> HCE_IDC_HAB.php Ver. 2015-11-19</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
	$empresa = "hceidc";
	$k = 0;
	$conexidc = mysqli_connect('192.168.0.2:3306','pmla','pmla800067065','pacidc') or die("No se realizo Conexion con el IDC");
	// mysql_select_db("pacidc"); 	
	echo "CONEXION IDC OK<br>";
	

	

	
	$query  = "select count(*) from habidc ";
	$err = mysql_query($query,$conexidc) or die("ERROR CONSULTANDO TABLA habidc ".mysql_errno().":".mysql_error());
	$row = mysql_fetch_array($err);
	echo "Numero de Registros ".$row[0]."<br>";
	
	$query  = "delete from habidc where habFec='".date("Y-m-d")."'";
	$err = mysql_query($query,$conexidc) or die("ERROR BORRANDO TABLA Habidc ".mysql_errno().":".mysql_error());
	//                                   0                    1
	$query  = "select mhosidc_000047.Methis,mhosidc_000047.Meting ";
	$query .= " from mhosidc_000047 ";
	$query .= " where mhosidc_000047.metest='on' ";
	$query .= " group by 1,2 ";
	$query .= " order by 1,2 ";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if($num > 0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$habidc=array();
			$habidc[0] = date("Y-m-d"); //Fecha
			$habidc[1] = $row[0];//Historia
			$habidc[2] = $row[1];//Ingreso
			$habidc[3] = 0;//Peso Adultos
			$habidc[4] = 0;//Peso Niños
			$habidc[5] = 0;//Talla Adultos
			$habidc[6] = 0;//Talla Niños
			$habidc[7] = 0;//SC Adultos
			$habidc[8] = 0;//SC Niños
			$habidc[9] = " ";//Alergias
			$habidc[10] = " ";//Medicamentos
			$habidc[11] = "1900-01-01";//Fecha de Registro
			$habidc[12] = "00-00-00";//Hora de Registro
			$query  = "Select ".$empresa."_000036.Fecha_data,".$empresa."_000036.Hora_data from ".$empresa."_000036"; 
			$query .= " where ".$empresa."_000036.Firpro='000051' "; 
			$query .= "   and ".$empresa."_000036.Firhis='".$row[0]."' ";
			$query .= "   and ".$empresa."_000036.Firing='".$row[1]."' ";
			$query .= " Order by 1 desc,2 desc";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$query  = "Select ".$empresa."_000051.fecha_data,".$empresa."_000051.movcon,".$empresa."_000051.movdat,".$empresa."_000051.hora_data from ".$empresa."_000051"; 
				$query .= " where ".$empresa."_000051.movpro     = '000051' "; 
				$query .= "   and ".$empresa."_000051.Fecha_data = '".$row1[0]."' ";
				$query .= "   and ".$empresa."_000051.Hora_data  = '".$row1[1]."' ";
				$query .= "   and ".$empresa."_000051.movhis     = '".$row[0]."' ";
				$query .= "   and ".$empresa."_000051.moving     = '".$row[1]."' ";
				$query .= "   and ".$empresa."_000051.movcon in (157,42,62,208,263,154,8,184) ";
				$query .= " Order by 1 desc,2 ";
				$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num2 = mysql_num_rows($err2);
				if($num2 > 0)
				{
					for ($j=0;$j<$num2;$j++)
					{
						$row2 = mysql_fetch_array($err2);
						if(($row2[0] > $habidc[11] and $j == 0) or ($row2[0] == $habidc[11] and $j > 0) or ($row2[0] == $habidc[11] and $j == 0 and $row2[3] > $habidc[12]))
						{
							$habidc[11] = $row2[0];
							$habidc[12] = $row2[3];
							switch(trim($row2[1]))
							{
								case 157:
									$habidc[7] = $row2[2];
								break;
								case 42:
									$habidc[9] = $row2[2];
								break;
								case 62:
									$habidc[10] = $row2[2];
								break;
								case 208:
									$habidc[5] = $row2[2];
								break;
								case 263:
									$habidc[3] = $row2[2];
								break;
								case 154:
									$habidc[8] = $row2[2];
								break;
								case 8:
									$habidc[6] = $row2[2];
								break;
								case 184:
									$habidc[4] = $row2[2];
								break;
							}
						}
					}
				}
			}
			$query  = "Select ".$empresa."_000036.Fecha_data,".$empresa."_000036.Hora_data from ".$empresa."_000036"; 
			$query .= " where ".$empresa."_000036.Firpro='000052' "; 
			$query .= "   and ".$empresa."_000036.Firhis='".$row[0]."' ";
			$query .= "   and ".$empresa."_000036.Firing='".$row[1]."' ";
			$query .= " Order by 1 desc,2 desc";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$query  = "Select ".$empresa."_000052.fecha_data,".$empresa."_000052.movcon,".$empresa."_000052.movdat,".$empresa."_000052.hora_data from ".$empresa."_000052"; 
				$query .= " where ".$empresa."_000052.movpro     = '000052' "; 
				$query .= "   and ".$empresa."_000052.Fecha_data = '".$row1[0]."' ";
				$query .= "   and ".$empresa."_000052.Hora_data  = '".$row1[1]."' ";
				$query .= "   and ".$empresa."_000052.movhis     = '".$row[0]."' ";
				$query .= "   and ".$empresa."_000052.moving     = '".$row[1]."' ";
				$query .= "   and ".$empresa."_000052.movcon in (118,42,62,116,26,93,9,115) ";
				$query .= " Order by 1 desc,2 ";
				$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num2 = mysql_num_rows($err2);
				if($num2 > 0)
				{
					for ($j=0;$j<$num2;$j++)
					{
						$row2 = mysql_fetch_array($err2);
						if(($row2[0] > $habidc[11] and $j == 0) or ($row2[0] == $habidc[11] and $j > 0) or ($row2[0] == $habidc[11] and $j == 0 and $row2[3] > $habidc[12]))
						{
							$habidc[11] = $row2[0];
							$habidc[12] = $row2[3];
							switch(trim($row2[1]))
							{
								case 118:
									$habidc[7] = $row2[2];
								break;
								case 42:
									$habidc[9] = $row2[2];
								break;
								case 62:
									$habidc[10] = $row2[2];
								break;
								case 116:
									$habidc[5] = $row2[2];
								break;
								case 26:
									$habidc[3] = $row2[2];
								break;
								case 93:
									$habidc[8] = $row2[2];
								break;
								case 9:
									$habidc[6] = $row2[2];
								break;
								case 115:
									$habidc[4] = $row2[2];
								break;
							}
						}
					}
				}
			}	
			$query  = "Select ".$empresa."_000036.Fecha_data,".$empresa."_000036.Hora_data from ".$empresa."_000036"; 
			$query .= " where ".$empresa."_000036.Firpro='000063' "; 
			$query .= "   and ".$empresa."_000036.Firhis='".$row[0]."' ";
			$query .= "   and ".$empresa."_000036.Firing='".$row[1]."' ";
			$query .= " Order by 1 desc,2 desc";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$query  = "Select ".$empresa."_000063.fecha_data,".$empresa."_000063.movcon,".$empresa."_000063.movdat,".$empresa."_000063.hora_data from ".$empresa."_000063"; 
				$query .= " where ".$empresa."_000063.movpro     = '000063' "; 
				$query .= "   and ".$empresa."_000063.Fecha_data = '".$row1[0]."' ";
				$query .= "   and ".$empresa."_000063.Hora_data  = '".$row1[1]."' ";
				$query .= "   and ".$empresa."_000063.movhis     = '".$row[0]."' ";
				$query .= "   and ".$empresa."_000063.moving     = '".$row[1]."' ";
				$query .= "   and ".$empresa."_000063.movcon in (193,57,87,188,266,263,189,187) ";
				$query .= " Order by 1 desc,2 ";
				$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num2 = mysql_num_rows($err2);
				if($num2 > 0)
				{
					for ($j=0;$j<$num2;$j++)
					{
						$row2 = mysql_fetch_array($err2);
						if(($row2[0] > $habidc[11] and $j == 0) or ($row2[0] == $habidc[11] and $j > 0) or ($row2[0] == $habidc[11] and $j == 0 and $row2[3] > $habidc[12]))
						{
							$habidc[11] = $row2[0];
							$habidc[12] = $row2[3];
							switch(trim($row2[1]))
							{
								case 193:
									$habidc[7] = $row2[2];
								break;
								case 57:
									$habidc[9] = $row2[2];
								break;
								case 87:
									$habidc[10] = $row2[2];
								break;
								case 188:
									$habidc[5] = $row2[2];
								break;
								case 266:
									$habidc[3] = $row2[2];
								break;
								case 263:
									$habidc[8] = $row2[2];
								break;
								case 189:
									$habidc[6] = $row2[2];
								break;
								case 187:
									$habidc[4] = $row2[2];
								break;
							}
						}
					}
				}
			}
			for ($j=1;$j<9;$j++)
			{
				if(!is_numeric($habidc[$j]))
					$habidc[$j] = (integer)$habidc[$j];
			}
			$query = "insert habidc (habfec,habhis,habing,habpea,habpen,habtaa,habtan,habsca,habscn,habale,habmed) values ('";
			$query .=  $habidc[0]."',";
			$query .=  $habidc[1].",";
			$query .=  $habidc[2].",";
			$query .=  $habidc[3].",";
			$query .=  $habidc[4].",";
			$query .=  $habidc[5].",";
			$query .=  $habidc[6].",";
			$query .=  $habidc[7].",";
			$query .=  $habidc[8].",'";
			$query .=  $habidc[9]."','";
			$query .=  $habidc[10]."')";
			//echo $query."<br>";
			$err2 = mysql_query($query,$conexidc) or die("ERROR GRABANDO HABILITACION IDC : ".mysql_errno().":".mysql_error());
			$k++;
			echo "REGISTRO : ".$k." GRABADO <br>";
		}
	}
	echo "<b>TOTAL REGISTROS : ".$num." GRABADOS</b>";

?>
</body>
</html>
