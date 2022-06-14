<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
include_once("root/comun.php");

										//ACTUALIZACIONES
			//Daniel CB   28-04-2022.  Se realiza correccion de parametros quemados
function bisiesto($year)
{
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		if(isset($_REQUEST['wemp_pmla']))
		{
			$wemp_pmla = $_REQUEST['wemp_pmla'];
		}
		//else
		//{
		//	$wemp_pmla = '05';
		//}
		$wactualiz = "2022-04-28";
		$institucion = consultarInstitucionPorCodigo( $conex, $wemp_pmla );
		encabezado( "NOMINA Y PRESTACIONES SOCIALES - COLILLA DE PAGO", $wactualiz, $institucion->baseDeDatos );		

		$key = substr($user,2,strlen($user));
		$cedula = $key;
		$conex = odbc_connect('nomlab','informix','sco')
			or die("No se ralizo Conexion");
		echo "<form action='000001_rep5.php?wemp_pmla=".$wemp_pmla."' method=post>'";
		if(!isset($cedula) or !isset($ano) or !isset($mes))
		{
			$year=(integer)substr(date("Y-m-d"),0,4);
			$day=substr(date("Y-m-d"),5,2);
			echo "<center><table border=0>";
			// echo "<tr><td align=center><b>LABORATORIO MEDICO LAS AMERICAS S.A.<b></td></tr>";
			// echo "<tr><td align=center>NOMINA Y PRESTACIONES SOCIALES</td></tr>";
			// echo "<tr><td align=center>COLILLA DE PAGO</td></tr>";
			echo "<tr>";
			echo "<tr><td bgcolor=#cccccc align=center><select name='ano'>";
			
			for($i=2000;$i<2050;$i++)
			{
				if($year == $i)
            		echo "<option value='".$i."' selected>".$i;
            	else
            		echo "<option value='".$i."'>".$i;
            }

            echo "</select></tr></td>";
			$nmeses=array();
			$nmeses[0][0]="01";
			$nmeses[0][1]="Enero";
			$nmeses[1][0]="02";
			$nmeses[1][1]="Febrero";
			$nmeses[2][0]="03";
			$nmeses[2][1]="Marzo";
			$nmeses[3][0]="04";
			$nmeses[3][1]="Abril";
			$nmeses[4][0]="05";
			$nmeses[4][1]="Mayo";
			$nmeses[5][0]="06";
			$nmeses[5][1]="Junio";
			$nmeses[6][0]="07";
			$nmeses[6][1]="Julio";
			$nmeses[7][0]="08";
			$nmeses[7][1]="Agosto";
			$nmeses[8][0]="09";
			$nmeses[8][1]="Septiembre";
			$nmeses[9][0]="10";
			$nmeses[9][1]="Octubre";
			$nmeses[10][0]="11";
			$nmeses[10][1]="Noviembre";
			$nmeses[11][0]="12";
			$nmeses[11][1]="Diciembre";
			echo "<tr><td bgcolor=#cccccc align=center><select name='mes'>";
			for($i=0;$i<12;$i++)
			{
				if($day == $nmeses[$i][0])
            		echo "<option value='".$nmeses[$i][0]."' selected>".$nmeses[$i][1];
            	else
            		echo "<option value='".$nmeses[$i][0]."'>".$nmeses[$i][1];
            }
            echo "</select></tr></td>";
			
    		echo "<tr><td bgcolor=#cccccc align=center><INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 1 CHECKED> Primera Quincena  ";
    		echo "<INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 2> Segunda Quincena</td></tr>";		
			echo "<tr><td bgcolor=#cccccc align=center><input type='submit' value='IR'></td></tr></table>";
			echo "<input type='HIDDEN' name= 'cedula' value='".$cedula."'>";
		}
		else
		{   
			$cedula=strtoupper($cedula);
			$query = "Select percod,perap1,perap2,perno1,perno2,percco,cconom,perced,perofi,ofinom ";
			$query=$query."  From noper,cocco,noofi Where percod = '".substr($cedula,1,5)."'";
			$query=$query."  And percco = ccocod and perofi = oficod Group By ";
			$query=$query."  percod,perap1,perap2,perno1,perno2,percco,cconom,perced,perofi,ofinom ";
			#echo $query."<br>";
			$err = odbc_do($conex,$query);
			$campos= odbc_num_fields($err);
			if (odbc_fetch_row($err))
			{
				$row=array();
				for($i=1;$i<=$campos;$i++)
				{
					$row[$i-1]=odbc_result($err,$i);
				}
				$meses=array();
				$meses[0]=30;
				if(bisiesto($ano))
					$meses[1]=29;
				else
					$meses[1]=28;
				$meses[2]=30;
				$meses[3]=30;
				$meses[4]=30;
				$meses[5]=30;
				$meses[6]=30;
				$meses[7]=30;
				$meses[8]=30;
				$meses[9]=30;
				$meses[10]=30;
				$meses[11]=30;
				$qui = ((integer)$mes * 2);	
				if ($radio1 == 1)
					$qui = $qui-1;
				if ($qui < '10')
					$qui = "0".$qui;
				if($qui % 2 == 0)
				{
					$last=$meses[$mes - 1];
					$begin="16";
				}
				else
				{
					$last=15;	
					$begin="01";
				}
				$nombre=$row[3]." ".$row[4]." ".$row[1]." ".$row[2];	
				echo "<table border=1>";				
				// echo "<tr><td rowspan=4 align=center><IMG SRC='/matrix/images/medical/laboratorio/labmed.gif' ></td>";				
				// echo "<td colspan=4 align=center><font size=5>LABORATORIO MEDICO LAS AMERICAS S.A.</font></td></tr>";
				// echo "<tr><td colspan=4  align=center><font size=4>NIT : 800209804</font><td></tr>";
				// echo "<tr><td colspan=4  align=center><font size=4>NOMINA Y PRESTACIONES SOCIALES</font><td></tr>";
				// echo "<tr><td colspan=4 align=center><b>COLILLA DE PAGO</b></td></tr>";
				echo "<tr><td colspan=5 align=center>PERIODO : ".$ano."/".$mes." QUINCENA : ".$qui." DESDE : ".$ano."-".$mes."-".$begin." HASTA : ".$ano."-".$mes."-".$last."</td></tr>";
				echo "<tr><td bgcolor=#cccccc colspan=5 align=center>EMPLEADO : ".$row[0]."-".$nombre." CEDULA : ".$row[7]." OFICIO : ".$row[8]."-".$row[9]."</td></tr>";
				echo "<tr><td colspan=5 align=center>CENTRO DE COSTOS : ".$row[5]."-".$row[6]."</td></tr>";
				echo "<tr><td><b>Codigo Cpto</b></td><td><b>Nombre Cpto</b></td><td align=right><b>Horas</b></td><td align=right><b>Valor</b></td><td align=center><b>C. Costos</b></td></tr>";
				$query = "select pagcon,connom,paghor,pagval,pagcco from nopag,nocon ";
				$query = $query."  where pagcod = '".$row[0]."'";
				$query = $query."  And pagano = '".$ano."'";
				$query = $query."  And pagmes = '".$mes."'";
				$query = $query."  And pagtip = 'Q'";
				$query = $query."  And pagsec = '".$qui."'";
				$query = $query."  And pagcon = concod ";
				$query = $query." order by pagcon";
				#echo $query."<br>";
				$err1 = odbc_do($conex,$query);
				$campos1 = odbc_num_fields($err1);
				if (odbc_fetch_row($err1))
				{
					$wswp=0;
					$wswd=0;
					$total_p=0;
					$total_d=0;
					$total_h=0;
					do
					{
						$row1=array();
						for($i=1;$i<=$campos1;$i++)
						{
							$row1[$i-1]=odbc_result($err1,$i);
						}
						switch (substr($row1[0],0,1)) 
						{
							case 0:
								if($wswp==0)
								{
									echo "<tr><td bgcolor=#cccccc colspan=5 align=center><b>PAGOS</b></td></tr>";	
									$wswp=1;
								}
								$total_p=$total_p+$row1[3];
								$total_h=$total_h+$row1[2];
								echo "<tr><td>".$row1[0]."</td><td>".$row1[1]."</td><td align=right>".$row1[2]."</td><td align=right>".number_format((double)$row1[3],2,'.',',')."</td><td align=center>".$row1[4]."</td></tr>";
								break;
							default:
								if($wswd==0)
								{
									echo "<tr><td bgcolor=#ffffcc colspan=2><b>TOTAL HORAS : </b></td><td bgcolor=#ffffcc align=right><b>".$total_h."</b></td><td bgcolor=#99ccff><b>TOTAL PAGOS</b></td><td  bgcolor=#99ccff align=right><b>".number_format((double)$total_p,2,'.',',')."</b></td></tr>";
									echo "<tr><td bgcolor=#cccccc colspan=5 align=center><b>DEDUCCIONES</b></td></tr>";	
									$wswd=1;
								}
								$total_d=$total_d+$row1[3];
								echo "<tr><td>".$row1[0]."</td><td colspan=2>".$row1[1]."</td><td align=right>".number_format((double)$row1[3],2,'.',',')."</td><td align=center>".$row1[4]."</td></tr>";
								break;
						}
					}
					while (odbc_fetch_row($err1));
					
					$neto=$total_p-$total_d;
					echo "<tr><td bgcolor=#ccccff colspan=3><b>TOTAL DEDUCCIONES</b></td><td colspan=2 bgcolor=#ccccff align=right><b>".number_format((double)$total_d,2,'.',',')."</b></td></tr>";				
					echo "<tr><td colspan=3 bgcolor=#ccffcc><b>NETO PAGADO</b></td><td colspan=2 bgcolor=#ccffcc align=right><b>".number_format((double)$neto,2,'.',',')."</b></td></tr>";
				}
				else
				{
					echo "<center><table border=0 aling=center>";
					echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR NO EXISTE DETALLE DE PAGOS !!!!</MARQUEE></FONT>";
					echo "<br><br>";
				}
			}
			else
			{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR NO EXISTE EL EMPLEADO !!!!</MARQUEE></FONT>";
				echo "<br><br>";
			}
		}
		echo "</table>";
		
		odbc_close($conex);
		odbc_close_all();
	}
?>
</body>
</html>
