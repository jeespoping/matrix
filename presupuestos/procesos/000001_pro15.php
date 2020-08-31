<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Calculo de Indicadores Financieros (IF)</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro15.php Ver. 2015-09-25</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_pro15.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wmes)  or !isset($wgru) or !isset($wemp) or $wemp == "Seleccione")
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>APLICACION DE PRESUPUESTOS</td></tr>";
			echo "<tr><td align=center colspan=2>CALCULO DE INDICADORES FINANCIEROS (IF)</td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wmes' size=2 maxlength=2></td></tr>";
			echo "<td bgcolor=#cccccc align=center>Forma de Agrupacion</td>";
			echo "<td bgcolor=#cccccc align=center><select name='wgru'>";
			$query = "SELECT grucod ,grudes  from ".$empresa."_000013 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			for ($j=0;$j<$num;$j++)
			{	
				$row = mysql_fetch_array($err);
				echo "<option>".$row[0]."-".$row[1]."</option>";
			}	
			echo "</td></tr>";
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
			#echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wgru' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			$wemp = substr($wemp,0,2);
			$ini=strpos($wgru,"-");
			$wgru=substr($wgru,0,$ini);
			$k=0;
			$wmesi = 0;
			$wmesf = 0;
			$query = "SELECT Grumei ,Grumef  from ".$empresa."_000013 ";
			$query = $query." WHERE grucod = '".$wgru."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
   			if ($num > 0)
   			{
	   			$row = mysql_fetch_array($err);
    			$wmesi =(integer) $row[0];
    			$wmesf =(integer) $row[1];
    		}
			if ($wmesi == $wmesf And $wmesi != 0 And $wmesf != 0)
			{
    			$wmesi =(integer) $wmes;
    			$wmesf = (integer)$wmes;
    		}
			Else
   				 $wmes = "13";
			if ($wmesi != 0 And $wmesf != 0) 
			{
				$wanopa=$wanop-1;
				$query = "DELETE from ".$empresa."_000016 where infmes = ".$wmes;
				$query = $query."   and infgru = '".$wgru."'";
				$query = $query."   and infano = ".$wanop;
				$query = $query."   and infemp = '".$wemp."' ";
				$err = mysql_query($query,$conex);
				$query = "SELECT ccocod from ".$empresa."_000005 ";
				$query = $query." WHERE ccoclas = 'PR'";
				$query = $query."   and ccoemp = '".$wemp."' ";
				$query = $query." order by ccocod ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
   				if ($num>0)
   				{
	   				$wanopa=$wanop -1 ;
	   				for ($i=0;$i<$num;$i++)
					{
	   					$row = mysql_fetch_array($err);
	   					 #PROCESOS PARA EL AÑO ACTUAL
        				$wsuma1 = 0;
        				echo "PROCESANDO EL CENTRO DE COSTOS : ".$row[0]."<br>";
       					$query = "SELECT sum(mecval) as suma1 from ".$empresa."_000026,".$empresa."_000047 ";
        				$query = $query." where meccco = '".$row[0]."'";
        				$query = $query."   and mecano = ".$wanop;
        				$query = $query."   and mecemp = '".$wemp."' ";
        				$query = $query."   and mecmes between ".$wmesi." and ".$wmesf;
        				$query = $query."   and mecemp = vblemp ";
        				$query = $query."   and meccco = vblcco ";
        				$query = $query."   and mecano = vblano ";
        				$query = $query."   and meccpr = vblccv ";
        				$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
        				if ($num1>0)
        				{
	        				$row1 = mysql_fetch_array($err1);
        					$wsuma1 = $row1[0];
        				}
        				$query = "SELECT vblcpc  from ".$empresa."_000047 ";
        				$query = $query." where vblcco = '".$row[0]."'";
        				$query = $query."   and vblano = ".$wanop;
        				$query = $query."   and vblemp = '".$wemp."' ";
        				$query = $query." group by vblcpc";
        				$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						$wsuma2 = 0;
        				if ($num1>0)
        				{
       						for ($j=0;$j<$num1;$j++)
							{
	   							$row1 = mysql_fetch_array($err1);
           						$query = "SELECT sum(mecval) as suma2 from ".$empresa."_000026 ";
            					$query = $query." where meccco = '".$row[0]."'";
            					$query = $query."   and mecano = ".$wanop;
            					$query = $query."   and mecemp = '".$wemp."' ";
            					$query = $query."   and mecmes between ".$wmesi." and ".$wmesf;
            					$query = $query."   and meccpr = '".$row1[0]."'";
								$err2 = mysql_query($query,$conex);
								$num2 = mysql_num_rows($err2);
        						if ($num2>0)
        						{
	        						$row2 = mysql_fetch_array($err2);
            						$wsuma2 = $wsuma2 + $row2[0];
            					}
            				}
            			}
        				#CALCULO PCV PROPROCION DE COSTOS VARIABLES
        				$wmon=0;
        				if($wsuma2 != 0)
        					$wmon= $wsuma1 / $wsuma2 * 100;
        				$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$k++;
        				$query = "insert ".$empresa."_000016 (medico,fecha_data,hora_data,infemp,infcco,infano,infmes,infcod,infgru,infmon,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."',".$wanop.",".$wmes.",'PCV','".$wgru."',".$wmon.",'C-".$empresa."')";
        				$err2 = mysql_query($query,$conex);
        				
        				#CALCULO MAC MARGEN DE CONTRIBUCION
        				if($wsuma2 != 0)
        					$wmon = (1 - ($wsuma1 / $wsuma2)) * 100;
        				$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$k++;
        				$query = "insert ".$empresa."_000016 (medico,fecha_data,hora_data,infemp,infcco,infano,infmes,infcod,infgru,infmon,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."',".$wanop.",".$wmes.",'MAC','".$wgru."',".$wmon.",'C-".$empresa."')";
        				$err2 = mysql_query($query,$conex);
        				
        				#CALCULO ROI RENTIBILIDAD OPERACIONAL DE LOS INGRESOS
        				$query = "SELECT sum(orumon) as suma1 from ".$empresa."_000037 ";
       					$query = $query." where orucco = '".$row[0]."'";
       	 				$query = $query."   and orumes between ".$wmesi." and ".$wmesf;
        				$query = $query."   and oruano = ".$wanop;
        				$query = $query."   and oruemp = '".$wemp."' ";
        				$query = $query."   and orucod = 'UU'";
        				$err2 = mysql_query($query,$conex);
        				$num2 = mysql_num_rows($err2);
        				if ($num2 > 0 and $wsuma2 != 0)
        				{
	        				$row2 = mysql_fetch_array($err2);
            				$wmon = $row2[0] / $wsuma2 * 100;
            				$wutilidad = $row2[0];
            			}
            			else
            			{
            				$wmon=0;
            				$wutilidad=0;
            			}
            			$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$k++;
        				$query = "insert ".$empresa."_000016 (medico,fecha_data,hora_data,infemp,infcco,infano,infmes,infcod,infgru,infmon,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."',".$wanop.",".$wmes.",'ROI','".$wgru."',".$wmon.",'C-".$empresa."')";
        				$err2 = mysql_query($query,$conex);
        				
						 #'CALCULO ROA MARGEN DE RENTABILIDAD DE LOS ACTIVOS
       					$query = "SELECT SUM(orumon) as suma3 from ".$empresa."_000037 ";
       					$query = $query." where orucco =  '".$row[0]."'";
        				$query = $query."   and orumes = ".$wmesf;
        				$query = $query."   and oruano = ".$wanop;
        				$query = $query."   and oruemp = '".$wemp."' ";
        				$query = $query."   and (orucod = 'AU'";
        				$query = $query."    or  orucod = 'INV'";
        				$query = $query."    or  orucod = 'CT')";
        				$err2 = mysql_query($query,$conex);
        				$num2 = mysql_num_rows($err2);
        				$row2 = mysql_fetch_array($err2);
        				if ($num2 > 0 and $row2[0] != 0)
        				{
            				$wmon = $wutilidad / $row2[0] * 100;
            			}
            			else
            				$wmon=0;
            			$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$k++;
            			$query = "insert ".$empresa."_000016 (medico,fecha_data,hora_data,infemp,infcco,infano,infmes,infcod,infgru,infmon,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."',".$wanop.",".$wmes.",'ROA','".$wgru."',".$wmon.",'C-".$empresa."')";
        				$err2 = mysql_query($query,$conex);
        				
        				#CALCULO PIP PROPORCION INGRESOS PROPIOS
        				$query = "SELECT sum(mecval) as suma3 from ".$empresa."_000026 ";
        				$query = $query." where meccco =  '".$row[0]."'";
        				$query = $query."   and mecano = ".$wanop;
        				$query = $query."   and mecemp = '".$wemp."' ";
        				$query = $query."   and mecmes between ".$wmesi." and ".$wmesf;
        				$query = $query."   and meccpr = '900'";
        				$err2 = mysql_query($query,$conex);
        				$num2 = mysql_num_rows($err2);
        				$row2 = mysql_fetch_array($err2);
        				if ($num2 > 0 and $wsuma2+$row2[0] != 0)
        				{
            				$wmon =$wsuma2 / ($wsuma2 + $row2[0]) * 100;
            			}
            			else
            				$wmon=0;
            			$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$k++;
            			$query = "insert ".$empresa."_000016 (medico,fecha_data,hora_data,infemp,infcco,infano,infmes,infcod,infgru,infmon,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."',".$wanop.",".$wmes.",'PIP','".$wgru."',".$wmon.",'C-".$empresa."')";
        				$err2 = mysql_query($query,$conex);	
      				
        				 #CALCULO PIT PROPORCION DE INGRESOS DE LA UNIDAD
        				$query = "SELECT sum(mecval) as suma3 from ".$empresa."_000026 ";
        				$query = $query." where mecmes between ".$wmesi." and ".$wmesf;
        				$query = $query."   and mecano = ".$wanop;
        				$query = $query."   and mecemp = '".$wemp."' ";
        				$query = $query."   and meccpr < '200'";
        				$err2 = mysql_query($query,$conex);
        				$num2 = mysql_num_rows($err2);
        				$row2 = mysql_fetch_array($err2);
        				if ($num2 > 0 and $row2[0] != 0)
        				{
            				$wmon =$wsuma2 / ($row2[0]) * 100;
            			}
            			else
            				$wmon=0;
            			$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$k++;
            			$query = "insert ".$empresa."_000016 (medico,fecha_data,hora_data,infemp,infcco,infano,infmes,infcod,infgru,infmon,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."','".$wemp."',".$wanop.",".$wmes.",'PIT','".$wgru."',".$wmon.",'C-".$empresa."')";
        				$err2 = mysql_query($query,$conex);	
        				
        				 #CALCULO RID ROTACION DE INVENTARIOS EN DIAS
        				$winvac = 0;
        				$winvan = 0;
        				$query = "SELECT sum(orumon) from ".$empresa."_000037 ";
        				$query = $query." where orucco =  '".$row[0]."'";
        				$query = $query."   and orumes between ".$wmesi." and ".$wmesf;
        				$query = $query."   and oruano = ".$wanop;
        				$query = $query."   and oruemp = '".$wemp."' ";
        				$query = $query."   and orucod = 'INV'";
        				$err2 = mysql_query($query,$conex);
        				$num2 = mysql_num_rows($err2);
        				if ($num2 > 0)
        				{
	        				$row2 = mysql_fetch_array($err2);
            				$winvac =$row2[0];
            			}
        				$query = "SELECT sum(mecval) as suma3 from ".$empresa."_000026 ";
       					$query = $query." where meccco = '".$row[0]."'";
       					$query = $query."   and mecano = ".$wanop;
       					$query = $query."   and mecemp = '".$wemp."' ";
        				$query = $query."   and mecmes between ".$wmesi." and ".$wmesf;
        				$query = $query."   and meccpr = '200'";
        				$err2 = mysql_query($query,$conex);
        				$num2 = mysql_num_rows($err2);
        				$row2 = mysql_fetch_array($err2);
        				if (($winvac + $winvan) > 0 and $row2[0] != 0 )
        				{
	        				$wvalrot=$row2[0]/($wmesf-$wmesi+1);
            				$wmon =  30 / ($wvalrot/($winvac/($wmesf-$wmesi+1)));
            			}
            			else
            				$wmon=0;
            			$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$k++;
            			$query = "insert ".$empresa."_000016 (medico,fecha_data,hora_data,infemp,infcco,infano,infmes,infcod,infgru,infmon,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."',".$wanop.",".$wmes.",'RID','".$wgru."',".$wmon.",'C-".$empresa."')";
        				$err2 = mysql_query($query,$conex);
        				
        				#CALCULO RCA CALCULO DE ROTACION DE CARTERA
        				$winvac = 0;
        				$winvan = 0;
        				$query = "SELECT sum(orumon) from ".$empresa."_000037 ";
        				$query = $query." where orucco =  '".$row[0]."'";
        				$query = $query."   and orumes  between ".$wmesi." and ".$wmesf;
        				$query = $query."   and oruano = ".$wanop;
        				$query = $query."   and oruemp = '".$wemp."' ";
        				$query = $query."   and orucod = 'CT'";
        				$err2 = mysql_query($query,$conex);
        				$num2 = mysql_num_rows($err2);
        				if ($num2 > 0)
        				{
	        				$row2 = mysql_fetch_array($err2);
            				$winvac =$row2[0];
            			}
        				$query = "SELECT sum(mecval) as suma3 from ".$empresa."_000026 ";
       					$query = $query." where meccco = '".$row[0]."'";
       					$query = $query."   and mecano = ".$wanop;
       					$query = $query."   and mecemp = '".$wemp."' ";
        				$query = $query."   and mecmes  between ".$wmesi." and ".$wmesf;
        				$query = $query."   and meccpr = '100'";
        				$err2 = mysql_query($query,$conex);
        				$num2 = mysql_num_rows($err2);
        				$row2 = mysql_fetch_array($err2);
        				if (($winvac + $winvan) > 0 and $row2[0] != 0 )
        				{
	        				$wvalrot=$row2[0]/($wmesf-$wmesi+1);
            				$wmon = 30 / ($wvalrot/($winvac/($wmesf-$wmesi+1)));
            			}
            			else
            				$wmon=0;
            			$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$k++;
            			$query = "insert ".$empresa."_000016 (medico,fecha_data,hora_data,infemp,infcco,infano,infmes,infcod,infgru,infmon,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."',".$wanop.",".$wmes.",'RCA','".$wgru."',".$wmon.",'C-".$empresa."')";
        				$err2 = mysql_query($query,$conex);
        				
        				#CALCULO PCM PROPORCION DE COSTOS DE MMQ Y MED
        				$wpcm1 = 0;
        				$query = "SELECT sum(Mecval) as suma1 from ".$empresa."_000026 ";
        				$query = $query." where Meccco = '".$row[0]."'";
        				$query = $query."   and Mecmes between ".$wmesi." and ".$wmesf;
        				$query = $query."   and Mecano = ".$wanop;
        				$query = $query."   and mecemp = '".$wemp."' ";
        				$query = $query."   and Meccpr = '200'";
        				$err2 = mysql_query($query,$conex);
        				$num2 = mysql_num_rows($err2);
        				if ($num2 > 0)
        				{
        					$row2 = mysql_fetch_array($err2);
        					$wpcm1 =$row2[0];
        				}
        				$wpcm2 = 0;
        				$query = "SELECT sum(Mioinp) as suma1 from ".$empresa."_000063,".$empresa."_000060 ";
        				$query = $query." where Miocco = '".$row[0]."'";
        				$query = $query."   and Mioano = ".$wanop;
        				$query = $query."   and Miomes between ".$wmesi." and ".$wmesf;
        				$query = $query."   and Mioemp = '".$wemp."' ";
        				$query = $query."   and Mioemp = Cfaemp ";
        				$query = $query."   and Miocfa = Cfacod ";
        				$query = $query."   and Cfaclas = '06' ";
        				$err2 = mysql_query($query,$conex);
        				$num2 = mysql_num_rows($err2);
        				if ($num2 > 0)
        				{
        					$row2 = mysql_fetch_array($err2);
        					$wpcm2 =$row2[0];
        				}
        				if ($wpcm2 != 0)
            				$wmon = $wpcm1 / $wpcm2 * 100;
            			else
            				$wmon=0;
            			$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$k++;
            			$query = "insert ".$empresa."_000016 (medico,fecha_data,hora_data,infemp,infcco,infano,infmes,infcod,infgru,infmon,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."',".$wanop.",".$wmes.",'PCM','".$wgru."',".$wmon.",'C-".$empresa."')";
        				$err2 = mysql_query($query,$conex);	
        				        				
						 #CALCULO MARGEN EBITDA
        				$webitda1 = 0;
        				$query = "SELECT sum(orumon) as suma1 from ".$empresa."_000037 ";
        				$query = $query." where orucco = '".$row[0]."'";
        				$query = $query."   and orumes between ".$wmesi." and ".$wmesf;
        				$query = $query."   and oruano = ".$wanop;
        				$query = $query."   and oruemp = '".$wemp."' ";
        				$query = $query."   and orucod = 'UU'";
        				$err2 = mysql_query($query,$conex);
        				$num2 = mysql_num_rows($err2);
        				if ($num2 > 0)
        				{
        					$row2 = mysql_fetch_array($err2);
        					$webitda1 =$row2[0];
        				}
        				$webitda2 = 0;
        				$query = "SELECT sum(mecval) as suma1 from ".$empresa."_000026 ";
        				$query = $query." where meccco = '".$row[0]."'";
        				$query = $query."   and mecano = ".$wanop;
        				$query = $query."   and mecmes between ".$wmesi." and ".$wmesf;
        				$query = $query."   and mecemp = '".$wemp."' ";
        				$query = $query."   and (meccpr = '203'";
        				$query = $query."    or  meccpr = '204')";
        				$err2 = mysql_query($query,$conex);
        				$num2 = mysql_num_rows($err2);
        				if ($num2 > 0)
        				{
        					$row2 = mysql_fetch_array($err2);
        					$webitda2 =$row2[0];
        				}
        				$webitda3 = 0;
        				$query = "SELECT sum(mecval) as suma1 from ".$empresa."_000026 ";
        				$query = $query." where meccco = '".$row[0]."'";
        				$query = $query."   and mecano = ".$wanop;
        				$query = $query."   and mecmes between ".$wmesi." and ".$wmesf;
        				$query = $query."   and mecemp = '".$wemp."' ";
        				$query = $query."   and meccpr < '200'";
        				$err2 = mysql_query($query,$conex);
        				$num2 = mysql_num_rows($err2);
        				$row2 = mysql_fetch_array($err2);
        				if ($num2 > 0)
        					$webitda3 =$row2[0];
        				if ($num2 > 0 and $webitda3 != 0)
        				{
            				$wmon =($webitda1 + $webitda2) / $webitda3 * 100;
            			}
            			else
            				$wmon=0;
            			$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$k++;
            			$query = "insert ".$empresa."_000016 (medico,fecha_data,hora_data,infemp,infcco,infano,infmes,infcod,infgru,infmon,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."',".$wanop.",".$wmes.",'EBI','".$wgru."',".$wmon.",'C-".$empresa."')";
        				$err2 = mysql_query($query,$conex);	
        				        				
        				 #***** PROCESOS ENTRE AÑOS *****
       					#'CALCULO VUO VARIACION ANUAL DE LA UTILIDAD OPERACIONAL
        				$winvac = 0;
        				$query = "SELECT sum(orumon) as suma1 from ".$empresa."_000037 ";
        				$query = $query." where orucco = '".$row[0]."'";
        				$query = $query."   and orumes between ".$wmesi." and ".$wmesf;
        				$query = $query."   and oruano = ".$wanop;
        				$query = $query."   and oruemp = '".$wemp."' ";
        				$query = $query."   and orucod = 'UU'";
        				$err2 = mysql_query($query,$conex);
        				$num2 = mysql_num_rows($err2);
        				if ($num2 > 0)
        				{
        					$row2 = mysql_fetch_array($err2);
        					$winvac =$row2[0];
        				}
        				$winvan = 0;
        				$query = "SELECT sum(orumon) as suma1 from ".$empresa."_000037 ";
        				$query = $query." where orucco = '".$row[0]."'";
        				$query = $query."   and orumes between ".$wmesi." and ".$wmesf;
        				$query = $query."   and oruano = ".$wanopa;
        				$query = $query."   and oruemp = '".$wemp."' ";
        				$query = $query."   and orucod = 'UU'";
        				$err2 = mysql_query($query,$conex);
        				$num2 = mysql_num_rows($err2);
        				if ($num2 > 0)
        				{
        					$row2 = mysql_fetch_array($err2);
        					$winvan =$row2[0];
        				}
						if($winvan != 0)
							$wmon= (($winvac / $winvan) - 1) * 100;
						else
							$wmon=0;
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$k++;
						$query = "insert ".$empresa."_000016 (medico,fecha_data,hora_data,infemp,infcco,infano,infmes,infcod,infgru,infmon,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."',".$wanop.",".$wmes.",'VUO','".$wgru."',".$wmon.",'C-".$empresa."')";
        				$err2 = mysql_query($query,$conex);	

        			#CALCULO VIP VARIACION ANUAL DE LOS INGRESOS PROPIOS
        			$winvac = 0;
        			$query = "SELECT sum(mecval) as suma3 from ".$empresa."_000026 ";
        			$query = $query." where meccco = '".$row[0]."'";
        			$query = $query."   and mecano = ".$wanop;
        			$query = $query."   and mecmes between ".$wmesi." and ".$wmesf;
        			$query = $query."   and mecemp = '".$wemp."' ";
        			$query = $query."   and meccpr < '200'";
        			$err2 = mysql_query($query,$conex);
        			$num2 = mysql_num_rows($err2);
        			if ($num2 > 0)
        			{
        				$row2 = mysql_fetch_array($err2);
        				$winvac =$row2[0];
        			}
        			$winvan = 0;
        			$query = "SELECT sum(mecval) as suma3 from ".$empresa."_000026 ";
        			$query = $query." where meccco = '".$row[0]."'";
        			$query = $query."   and mecano = ".$wanopa;
        			$query = $query."   and mecmes between ".$wmesi." and ".$wmesf;
        			$query = $query."   and mecemp = '".$wemp."' ";
        			$query = $query."   and meccpr < '200'";
        			$err2 = mysql_query($query,$conex);
        			$num2 = mysql_num_rows($err2);
        			if ($num2 > 0)
        			{
        				$row2 = mysql_fetch_array($err2);
        				$winvan =$row2[0];
        			}
        			if($winvan != 0)
						$wmon= (($winvac / $winvan) - 1) * 100;
					else
						$wmon=0;
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$k++;
					$query = "insert ".$empresa."_000016 (medico,fecha_data,hora_data,infemp,infcco,infano,infmes,infcod,infgru,infmon,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$wemp."','".$row[0]."',".$wanop.",".$wmes.",'VIP','".$wgru."',".$wmon.",'C-".$empresa."')";
        			$err2 = mysql_query($query,$conex);	

        		}
        		 echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k."</b><br>";
        	}
        	else
				 echo "<b>NUMERO DE REGISTROS INSERTADOS : ".$k."</b><br>";
        }
     }
}		
?>
</body>
</html>
