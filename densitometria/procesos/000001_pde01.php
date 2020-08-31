<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pde01.php' method=post>";
		if(!isset($confirm))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr>";
			echo "<tr><td align=center colspan=2><font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#ff0000 LOOP=-1>ESTA SEGURO DE EJECUTAR ESTE PROCESO ??????</MARQUEE></FONT></td><tr>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE DESITOMETRIA OSEA</td></tr>";
			echo "<tr><td align=center colspan=2>GENERACION DE ARCHIVO GENERAL</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>CONFIRMACION (S/N)</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='confirm' size=1 maxlength=1></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			if ($confirm=="S" or $confirm=="s")
			{
			$D=array();
			$m=0;
			$query = "SELECT Codigo, count(*)  from densito_000001 ";
			$query = $query." GROUP BY Codigo ";
  			$query = $query." ORDER BY Codigo ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
   			if ($num>0)
			{
				for ($i=0;$i<$num;$i++)
				{
					 $row = mysql_fetch_array($err);
					 if ($row[1] == 1)
					 {
						 $OCCUR=$row[1];
						$query = "SELECT Codigo, Nombres, Apellidos, Fecha__Nacimiento, Estatura, Peso, Sexo, Etnia  from densito_000001 ";
						$query = $query." where codigo = '".$row[0]."'";
						$err3 = mysql_query($query,$conex);
						$row = mysql_fetch_array($err3);
						 echo "codigo : ".$row[0]." ocurrencias : ". $OCCUR."<br>";
						 for ($j=0;$j<118;$j++)
							$D[$j]=0;
						$query = "SELECT Codigo, Secuencia, Fecha_Scan, Edad   from densito_000002 ";
						$query = $query." where codigo = '".$row[0]."'";
  						$query = $query." group BY Codigo, Secuencia, Fecha_Scan, Edad ";
  						$query = $query." order BY Codigo, Secuencia, Fecha_Scan, Edad ";
						$err1 = mysql_query($query,$conex);	
						$num1 = mysql_num_rows($err1);
						if ($num1>0)
						{
							for ($j=0;$j<$num1;$j++)
							{
								$row1 = mysql_fetch_array($err1);
								$D[0]=$row1[0];
								$D[1]=$row1[1];
								$D[2]=$row1[2];
								$D[3]=$row1[3];
								$D[4]=$row[1];
								$D[5]=$row[2];
								$D[6]=$row[3];
								$D[7]=$row[4];
								$D[8]=$row[5];
								$D[9]=$row[6];
								$D[10]=$row[7];
								$query = "SELECT Scan, Index_D, Young, Match_D, Bmd, Bmc, Area, Width_D, R_Val   from densito_000003 ";
								$query = $query." where codigo = '".$row[0]."'";
								$query = $query." and secuencia = '".$row1[1]."'";
  								$query = $query." order BY Scan desc, Index_D  ";
								$err2 = mysql_query($query,$conex);	
								$num2 = mysql_num_rows($err2);
								if ($num2>0)
								{
									for ($k=0;$k<$num2;$k++)
									{
										$row2 = mysql_fetch_array($err2);
										switch ($row2[0])
										{
											case "S":
												switch ($row2[1])
												{
													case 0:
														$D[11]=$row2[2];
														$D[12]=$row2[3];
														$D[13]=$row2[4];
														$D[14]=$row2[5];
														$D[15]=$row2[6];
														$D[16]=$row2[7];
														$D[17]=$row2[8];
													break;
													case 1:
														$D[18]=$row2[2];
														$D[19]=$row2[3];
														$D[20]=$row2[4];
														$D[21]=$row2[5];
														$D[22]=$row2[6];
														$D[23]=$row2[7];
														$D[24]=$row2[8];
													break;
													case 2:
														$D[25]=$row2[2];
														$D[26]=$row2[3];
														$D[27]=$row2[4];
														$D[28]=$row2[5];
														$D[29]=$row2[6];
														$D[30]=$row2[7];
														$D[31]=$row2[8];
													break;
													case 3:
														$D[32]=$row2[2];
														$D[33]=$row2[3];
														$D[34]=$row2[4];
														$D[35]=$row2[5];
														$D[36]=$row2[6];
														$D[37]=$row2[7];
														$D[38]=$row2[8];
													break;
													case 4:
														$D[39]=$row2[2];
														$D[40]=$row2[3];
														$D[41]=$row2[4];
														$D[42]=$row2[5];
														$D[43]=$row2[6];
														$D[44]=$row2[7];
														$D[45]=$row2[8];
													break;
													case 5:
														$D[46]=$row2[2];
														$D[47]=$row2[3];
														$D[48]=$row2[4];
														$D[49]=$row2[5];
														$D[50]=$row2[6];
														$D[51]=$row2[7];
														$D[52]=$row2[8];
													break;
													case 6:
														$D[53]=$row2[2];
														$D[54]=$row2[3];
														$D[55]=$row2[4];
														$D[56]=$row2[5];
														$D[57]=$row2[6];
														$D[58]=$row2[7];
														$D[59]=$row2[8];
													break;
													case 7:
														$D[60]=$row2[2];
														$D[61]=$row2[3];
														$D[62]=$row2[4];
														$D[63]=$row2[5];
														$D[64]=$row2[6];
														$D[65]=$row2[7];
														$D[66]=$row2[8];
													break;
													case 8:
														$D[67]=$row2[2];
														$D[68]=$row2[3];
														$D[69]=$row2[4];
														$D[70]=$row2[5];
														$D[71]=$row2[6];
														$D[72]=$row2[7];
														$D[73]=$row2[8];
													break;
													case 9:
														$D[74]=$row2[2];
														$D[75]=$row2[3];
														$D[76]=$row2[4];
														$D[77]=$row2[5];
														$D[78]=$row2[6];
														$D[79]=$row2[7];
														$D[80]=$row2[8];
													break;
												}
											case "F":
											{
												switch ($row2[1])
												{
													case 0:
														$D[81]=$row2[2];
														$D[82]=$row2[3];
														$D[83]=$row2[4];
														$D[84]=$row2[5];
														$D[85]=$row2[6];
														$D[86]=$row2[7];
														$D[87]=$row2[8];
													break;
													case 1:
														$D[88]=$row2[2];
														$D[89]=$row2[3];
														$D[90]=$row2[4];
														$D[91]=$row2[5];
														$D[92]=$row2[6];
														$D[93]=$row2[7];
														$D[94]=$row2[8];
													break;
													case 2:
														$D[95]=$row2[2];
														$D[96]=$row2[3];
														$D[97]=$row2[4];
														$D[98]=$row2[5];
														$D[99]=$row2[6];
														$D[100]=$row2[7];
														$D[101]=$row2[8];
													break;
													case 3:
														$D[102]=$row2[2];
														$D[103]=$row2[3];
														$D[104]=$row2[4];
														$D[105]=$row2[5];
														$D[106]=$row2[6];
														$D[107]=$row2[7];
														$D[108]=$row2[8];
													break;
													case 4:
														$D[109]=$row2[2];
														$D[110]=$row2[3];
														$D[111]=$row2[4];
														$D[112]=$row2[5];
														$D[113]=$row2[6];
														$D[114]=$row2[7];
														$D[115]=$row2[8];
													break;
												}
												}
											}
										}
										$fecha = date("Y-m-d");
					 					$hora = (string)date("H:i:s");
										$query = "insert densito_000004 (medico,fecha_data,hora_data,Codigo ,Secuencia, Fecha_Scan, Edad, Nombre, Apellidos, Fecha_Nacimiento, Estatura, Peso, Sexo, Etnia, L1_Young, L1_Match, L1_Bmd, L1_Bmc, L1_Area, L1_Width, L1_R_Val, L2_Young,L2_Match,L2_Bmd,L2_Bmc,L2_Area,L2_Width,L2_R_Val,L3_Young,L3_Match,L3_Bmd,L3_Bmc,L3_Area,L3_Width,L3_R_Val,L4_Young,L4_Match,L4_Bmd,L4_Bmc,L4_Area,L4_Width,L4_R_Val,L1_L2_Young,L1_L2_Match,L1_L2_Bmd,L1_L2_Bmc,L1_L2_Area,L1_L2_Width,L1_L2_R_Val,L1_L3_Young,L1_L3_Match,L1_L3_Bmd,L1_L3_Bmc,L1_L3_Area,L1_L3_Width,L1_L3_R_Val,L1_L4_Young,L1_L4_Match,L1_L4_Bmd,L1_L4_Bmc,L1_L4_Area,L1_L4_Width,L1_L4_R_Val,L2_L3_Young,L2_L3_Match,L2_L3_Bmd,L2_L3_Bmc,L2_L3_Area,L2_L3_Width,L2_L3_R_Val,L2_L4_Young,L2_L4_Match,L2_L4_Bmd,L2_L4_Bmc,L2_L4_Area,L2_L4_Width,L2_L4_R_Val,L3_L4_Young,L3_L4_Match,L3_L4_Bmd,L3_L4_Bmc,L3_L4_Area,L3_L4_Width,L3_L4_R_Val,";
										$query = $query."Cuello_Young,Cuello_Match,Cuello_Bmd,Cuello_Bmc,Cuello_Area,Cuello_Width,Cuello_R_Val,Wards_Young,Wards_Match,Wards_Bmd,Wards_Bmc,Wards_Area,Wards_Width,Wards_R_Val,Trocanter_Young,Trocanter_Match,Trocanter_Bmd,Trocanter_Bmc,Trocanter_Area,Trocanter_Width,Trocanter_R_Val,Diafisis_Young,Diafisis_Match,Diafisis_Bmd,Diafisis_Bmc,Diafisis_Area,Diafisis_Width,Diafisis_R_Val,Completo_Young,Completo_Match,Completo_Bmd,Completo_Bmc,Completo_Area,Completo_Width,Completo_R_Val,seguridad) values ('".$key."','".$fecha."','".$hora."',";
										$query = $query."'".$D[0]."',".$D[1].",'".$D[2]."',".$D[3].",'".$D[4]."','".$D[5]."','".$D[6]."',".$D[7].",".$D[8].",'".$D[9]."','".$D[10]."',";
										for ($w=11;$w<116;$w++)
											$query = $query.$D[$w].",";
										$query = $query."'C-costosyp')";
					 					$err3 = mysql_query($query,$conex);
					 					$m++;
					 					 echo " REGISTROS ACTUALIZADOS : ".$m."<br>";
				 					}
			 					}
		 					}
	 					}
 					}
				}
				 echo "NUMERO DE REGISTROS ACTUALIZADOS : ".$m."<br>";
        		}
        	}
}		
?>
</body>
</html>
