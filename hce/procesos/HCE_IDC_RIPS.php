<html>
<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Generacion Diaria de Rips IDC</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> HCE_IDC_RIPS.php Ver. 2014-05-20</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
include_once("root/comun.php");
	$whce = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
	$empresa = $whce;
	$k = 0;
	$conexidc = mysqli_connect('192.168.0.2:3306','pmla','pmla800067065',"pacidc") or die("No se realizo Conexion con el IDC");
	//$conexidc = mysql_connect('190.248.93.238:3306','pmla','pmla800067065') or die("No se realizo Conexion con el IDC");
	// mysql_select_db("pacidc"); 	
	echo "CONEXION IDC OK<br>";
	

	$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
	
	
	$fecharips = '2021-01-26'; //date("Y-m-d");
	//$fecharips = "2016-04-20";
	$query  = "select count(*) from Ripidc ";
	$err = mysql_query($query,$conexidc) or die("ERROR CONSULTANDO TABLA Ripidc ".mysql_errno().":".mysql_error());
	$row = mysql_fetch_array($err);
	echo "Numero de Registros ".$row[0]."<br>";
	
	$query  = "delete from Ripidc where RipFec='".$fecharips."'";
	$err = mysql_query($query,$conexidc) or die("ERROR BORRANDO TABLA Ripidc ".mysql_errno().":".mysql_error());
	//                                   0                       1                      2                      3                    4                    5                  6                  7                    8                    9
	$query  = "select ".$whce."_000036.Fecha_data,".$whce."_000036.Hora_data,".$wmovhos."_000047.Methis,".$wmovhos."_000047.Meting,".$wmovhos."_000048.Meduma,".$whce."_000036.firpro,root_000037.oritid,root_000037.oriced,".$whce."_000036.firrol,".$whce."_000036.firusu ";
	$query .= " from ".$wmovhos."_000047,".$wmovhos."_000048,".$whce."_000036,root_000037 ";
	$query .= " where ".$wmovhos."_000047.metest='on' ";
	$query .= "   and ".$wmovhos."_000047.mettdo = ".$wmovhos."_000048.medtdo "; 
	$query .= "   and ".$wmovhos."_000047.metdoc = ".$wmovhos."_000048.meddoc "; 
	$query .= "   and ".$wmovhos."_000047.Methis = ".$whce."_000036.firhis ";
	$query .= "   and ".$wmovhos."_000047.Meting = ".$whce."_000036.firing ";
	$query .= "   and ".$wmovhos."_000048.Meduma = ".$whce."_000036.firusu ";
	$query .= "   and ".$wmovhos."_000047.Methis = root_000037.orihis ";
	$query .= "   and ".$wmovhos."_000047.Meting = root_000037.oriing ";
	$query .= "   and root_000037.oriori = '10' ";
	$query .= "   and ".$whce."_000036.Fecha_data = '".$fecharips."' ";
	$query .= "   and ".$whce."_000036.firpro in ('000051','000052','000063') ";
	$query .= " group by 1,3,4,5,6,7,8,9,10 ";
	$query .= " order by 3,4,6 ";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if($num > 0)
	{
		for ($i=0;$i<$num;$i++)
		{
			$row = mysql_fetch_array($err);
			$ripidc=array();
			$ripidc[0] = $row[5];
			$ripidc[1] = $row[2];
			$ripidc[2] = $row[6];
			$ripidc[3] = $row[7];
			$ripidc[4] = "0000-00-00";
			$ripidc[5] = $row[9];
			$ripidc[6] = $row[8];
			$ripidc[7] = "NODX";
			$ripidc[8] = " ";
			$ripidc[9] = " ";
			$ripidc[10] = " ";
			$ripidc[11] = " ";
			$ripidc[12] = " ";
			switch($row[5])
			{
				case "000051":
					$query  = "Select ".$empresa."_".$ripidc[0].".movcon,".$empresa."_".$ripidc[0].".movdat from ".$empresa."_".$ripidc[0];
					$query .= " where ".$empresa."_".$ripidc[0].".fecha_data = '".$row[0]."' "; 
					$query .= "   and ".$empresa."_".$ripidc[0].".hora_data = '".$row[1]."' "; 
					$query .= "   and ".$empresa."_".$ripidc[0].".movpro='".$ripidc[0]."' "; 
					$query .= "   and ".$empresa."_".$ripidc[0].".movhis='".$row[2]."' ";
					$query .= "   and ".$empresa."_".$ripidc[0].".moving='".$row[3]."' ";
					$query .= "   and ".$empresa."_".$ripidc[0].".movcon in (2,78,182,189,266,11,261) ";
					$query .= " Order by 1 ";					
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							switch($row1[0])
							{
								case 2:
									$ripidc[4] = $row1[1];
								break;
								case 78:
									$ripidc[8] = substr($row1[1],0,2);
								break;
								case 182:
									$dx = trim(strip_tags($row1[1]));
									if(strlen($dx) < 4)
										$dx = "NODX";
									else
										$dx = substr($dx,0,4);
									$ripidc[7] = $dx;
								break;
								case 189:
									$ripidc[9] = $row1[1];
								break;
								case 266:
									$ripidc[10] = $row1[1];
								break;
								case 11:
									$ripidc[11] = $row1[1];
								break;
								case 261:
									$ripidc[12] = $row1[1];
								break;
							}
						}
					}
				break;
				case "000052":
					$query  = "Select ".$empresa."_".$ripidc[0].".movcon,".$empresa."_".$ripidc[0].".movdat from ".$empresa."_".$ripidc[0];
					$query .= " where ".$empresa."_".$ripidc[0].".fecha_data = '".$row[0]."' "; 
					$query .= "   and ".$empresa."_".$ripidc[0].".hora_data = '".$row[1]."' "; 
					$query .= "   and ".$empresa."_".$ripidc[0].".movpro='".$ripidc[0]."' "; 
					$query .= "   and ".$empresa."_".$ripidc[0].".movhis='".$row[2]."' ";
					$query .= "   and ".$empresa."_".$ripidc[0].".moving='".$row[3]."' ";
					$query .= "   and ".$empresa."_".$ripidc[0].".movcon in (2,19,141,210,212,57,147) ";
					$query .= " Order by 1 ";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							switch($row1[0])
							{
								case 2:
									$ripidc[4] = $row1[1];
								break;
								case 19:
									$ripidc[8] = substr($row1[1],0,2);
								break;
								case 141:
									$dx = trim(strip_tags($row1[1]));
									if(strlen($dx) < 4)
										$dx = "NODX";
									else
										$dx = substr($dx,0,4);
									$ripidc[7] = $dx;
								break;
								case 210:
									$ripidc[9] = $row1[1];
								break;
								case 212:
									$ripidc[10] = $row1[1];
								break;
								case 147:
									$ripidc[11] = $row1[1];
								break;
								case 57:
									$ripidc[12] = $row1[1];
								break;
							}
						}
					}
				break;
				case "000063":
					$query  = "Select ".$empresa."_".$ripidc[0].".movcon,".$empresa."_".$ripidc[0].".movdat from ".$empresa."_".$ripidc[0];
					$query .= " where ".$empresa."_".$ripidc[0].".fecha_data = '".$row[0]."' "; 
					$query .= "   and ".$empresa."_".$ripidc[0].".hora_data = '".$row[1]."' "; 
					$query .= "   and ".$empresa."_".$ripidc[0].".movpro='".$ripidc[0]."' "; 
					$query .= "   and ".$empresa."_".$ripidc[0].".movhis='".$row[2]."' ";
					$query .= "   and ".$empresa."_".$ripidc[0].".moving='".$row[3]."' ";
					$query .= "   and ".$empresa."_".$ripidc[0].".movcon in (2,45,240,248,249,260,262) ";
					$query .= " Order by 1 ";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$num1 = mysql_num_rows($err1);
					if($num1 > 0)
					{
						for ($j=0;$j<$num1;$j++)
						{
							$row1 = mysql_fetch_array($err1);
							switch($row1[0])
							{
								case 2:
									$ripidc[4] = $row1[1];
								break;
								case 45:
									$ripidc[8] = substr($row1[1],0,2);
								break;
								case 240:
									$dx = trim(strip_tags($row1[1]));
									if(strlen($dx) < 4)
										$dx = "NODX";
									else
										$dx = substr($dx,0,4);
									$ripidc[7] = $dx;
								break;
								case 249:
									$ripidc[9] = $row1[1];
								break;
								case 248:
									$ripidc[10] = $row1[1];
								break;
								case 260:
									$ripidc[11] = $row1[1];
								break;
								case 262:
									$ripidc[12] = $row1[1];
								break;
							}
						}
					}
				break;
			}
			
			$query = "insert Ripidc (RipTbl, RipHce, RipTdo, RipIde, RipFec, RipMed, RipEsp, RipDxp, RipTdx, RipTnm, RipTtn, Riptto, Ripdtt) values ('";
			$query .=  $ripidc[0]."','";
			$query .=  $ripidc[1]."','";
			$query .=  $ripidc[2]."','";
			$query .=  $ripidc[3]."','";
			$query .=  $ripidc[4]."','";
			$query .=  $ripidc[5]."','";
			$query .=  $ripidc[6]."','";
			$query .=  $ripidc[7]."','";
			$query .=  $ripidc[8]."','";
			$query .=  $ripidc[9]."','";
			$query .=  $ripidc[10]."','";
			$query .=  $ripidc[11]."','";
			$query .=  $ripidc[12]."')";
			//echo $query."<br>";
			$err2 = mysql_query($query,$conexidc) or die("ERROR GRABANDO RIPS IDC : ".mysql_errno().":".mysql_error());
			$k++;
			echo "REGISTRO : ".$k." GRABADO <br>";
		}
	}
	echo "<b>TOTAL REGISTROS : ".$num." GRABADOS</b>";

?>
</body>
</html>
