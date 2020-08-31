<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Venta de Medicamentos x Nomina</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Prestamos.php Ver. 2009-04-14</b></font></td></tr></table>
</center>
<?php
include_once("conex.php");
/**********************************************************************************************************************  
	   PROGRAMA : Prestamos.php
	   Fecha de Liberación : 2006-04-08
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2009-04-14
	   
	   OBJETIVO GENERAL :Este programa permite evaluar la capacidad de pago de un usuario para hacer prestamos de farmacia
	   a traves de la nomina.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   .2009-04-14
	   		Se corrigio el programa para los codigo de mas de 5 digitos.
	   		
	   .2008-10-29
	   		Se agregaron los conceptos 0022, 0085, 0089, 0099, 5245 y 5247 en el calculo de los ingresos debido a la implementacion del Salario
			Flexible.
			
	   .2008-05-19
	   		Se agregaron los conceptos 0088,0096,0097 y 0098 en el calculo de los ingresos debido a la implementacion del Salario
			Flexible.

	   .2007-12-26
	   		Su suma a los conceptos de ingresos el concepto 0023 (Prima de Servicios)
	   		
	   .2006-05-26
	   		Su suma a los conceptos de ingresos el concepto 0030 (Bonificacion)
		
	   .2006-04-08
	   		Release de Versión Beta.
	   
***********************************************************************************************************************/
function bisiesto($year)
{
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	echo "<form action='Prestamos.php' method=post>";
	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if($wpagook == 0)
		if(isset($ok))
		{
			$query = "lock table numeracion LOW_PRIORITY WRITE"; 
			$err1 = mysql_query($query,$conex);
			$query =  " update numeracion set secuencia = secuencia + 1 where medico='".$empresa."' and formulario='000046' and campo='0001' ";
			$err2 = mysql_query($query,$conex);
			$query = "select * from numeracion where medico='".$empresa."' and formulario='000046' and campo='0001' ";
			$err3 = mysql_query($query,$conex);
			$row = mysql_fetch_array($err3);
			$con=$row[3];
			$query = " UNLOCK TABLES";												
			$err4 = mysql_query($query,$conex);		
			if ($err1 == 1 and $err2 == 1 and $err4 == 1)
			{
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "insert ".$empresa."_000046 (medico,fecha_data,hora_data, Pnocon, Pnofec, Pnoemp, Pnocod, Pnonom, Pnocco, Pnoval, Pnocuo, Pnocup, Pnoest, seguridad) values ('".$empresa."','".$fecha."','".$hora."',".$con.",'".$fecha."','".$emp."','".$cod."','".$nom."','".$cco."',".$val.",".$cuo.",".$cup.",'off','C-".$empresa."')";
				$err2 = mysql_query($query,$conex);
				if ($err2 != 1)
					echo mysql_errno().":".mysql_error()."<br>";
				else
				{
					echo "<center><b>PRESTAMO NRo : ".$con." GRABADO</b></center> <br><br>";
					$wprestamo=$con;
				}
				$wpagook=1;
			}
		}
		else
			if(!isset($codigo) or !isset($monto) or !isset($cuotas))
			{
				echo "<input type='HIDDEN' name= 'wempaux' value=".$wemp.">";
				echo "<center><table border=0>";
				echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
				echo "<tr><td align=center colspan=2>VENTA DE MEDICAMENTOS X NOMINA</td></tr>";
				echo "<tr>";
				echo "<td bgcolor=#cccccc align=center>Identificacion del Usuario</td>";
				if(isset($codigo))
				{
					echo "<td bgcolor=#cccccc align=center>".$codigo."</td></tr>";
					echo "<input type='HIDDEN' name= 'codigo' value=".$codigo.">";
					echo "<input type='HIDDEN' name= 'radio1' value=1>";
				}
				else
				{
					echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='codigo' size=9 maxlength=9></td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center colspan=2><INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 1 CHECKED> Codigo Nomina  ";
					echo "<INPUT TYPE = 'Radio' NAME = 'radio1' VALUE = 2> Cedula</td></tr>";	
				}
				echo "<tr><td bgcolor=#cccccc align=center>Empresa</td>";
				if(isset($wemp))
				{
					$query = "SELECT Nomnom, Nomdes    from ".$empresa."_000036 where Nomemp='".$wemp."' order by Nomnom ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						$row = mysql_fetch_array($err);
						echo "<td bgcolor=#cccccc align=center>".$row[0]."-".$row[1]."</td></tr>";
						echo "<input type='HIDDEN' name= 'wemp' value=".$row[0]."-".$row[1].">";
					}
				}
				else
				{
					$query = "SELECT Nomnom, Nomdes    from ".$empresa."_000036 where Nomusu='".$empresa."' order by Nomnom ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						echo "<td bgcolor=#cccccc align=center><select name='wemp'>";
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							echo "<option>".$row[0]."-".$row[1]."</option>";
						}
						echo "</select>";
					}
				}
				echo "</td></tr>";	
				echo "<td bgcolor=#cccccc align=center>Monto de la Compra</td>";
				if(isset($monto))
				{
					echo "<td bgcolor=#cccccc align=center>".$monto."</td></tr>";
					echo "<input type='HIDDEN' name= 'monto' value=".$monto.">";
				}
				else
					echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='monto' size=12 maxlength=12></td></tr>";
				echo "<td bgcolor=#cccccc align=center>Nro. de Cuotas</td>";
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='cuotas' size=1 maxlength=1></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='submit' value='IR'></td></tr></table>";
			}
			else
			{   
				if(strlen($codigo) > 5)
					$codigo=substr($codigo,2);
				$ano=substr(date("Y-m-d"),0,4);
				$mes=substr(date("Y-m-d"),5,2);
				$dia=substr(date("Y-m-d"),8,2);
				$conex_o = odbc_connect(substr($wemp,0,strpos($wemp,"-")),'','') or die("No se ralizo Conexion");
				$query = "Select percod,perap1,perap2,perno1,perno2,percco,cconom,perced,perofi,ofinom From noper,cocco,noofi  ";
				if($radio1 == 1)
					$query=$query."  Where percod = '".$codigo."'";
				else
					$query=$query."  Where perced = '".$codigo."'";
				$query=$query."  And peretr ='A'";
				$query=$query."  And percco = ccocod ";
				$query=$query."  And perofi = oficod  ";
				$err = odbc_do($conex_o,$query);
				$campos= odbc_num_fields($err);
				$wsw=0;
				if(odbc_fetch_row($err))
					$wsw=1;
				if ($wsw==1 and $monto > 4999 and $cuotas > 0 and $cuotas < 5 and $monto/$cuotas > 4999)
				{
					$row=array();
					for($i=1;$i<=$campos;$i++)
					{
						$row[$i-1]=odbc_result($err,$i);
					}
					if($dia < 16)
					{
						$qui = (((integer)$mes - 1) * 2);	
						$mes= (integer)$mes - 1;	
						if($mes < 1)
						{
							$ano = $ano - 1;
							$mes= 12;
							$qui=24;
						}
					}
					else
						$qui = ((integer)$mes * 2) - 1;	
					if ($qui < '10')
						$qui = "0".$qui;
					if (strlen($mes) < 2)
						$mes = "0".$mes;
					$nombre=$row[3]." ".$row[4]." ".$row[1]." ".$row[2];	
					echo "<center><table border=1>";				
					echo "<tr><td rowspan=4 align=center><IMG SRC='/MATRIX/images/medical/root/americas10.jpg' ></td>";				
					echo "<td colspan=3 align=center><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
					echo "<tr><td colspan=3  align=center><font size=4>VERIFICACION DE LA CAPACIDAD DE ENDEUDAMIENTO X NOMINA Ver. 2008-10-29</font><td></tr>";
					echo "<tr><td bgcolor=#cccccc colspan=3 align=center>EMPLEADO : ".$row[0]."-".$nombre." CEDULA : ".$row[7]." OFICIO : ".$row[8]."-".$row[9]."</td></tr>";
					echo "<tr><td colspan=3 align=center>CENTRO DE COSTOS : ".$row[5]."-".$row[6]."</td></tr>";
					echo "<tr><td align=right><b>MONTO</b></td><td align=right><b>NRo. DE CUOTAS</b></td><td align=right><b>VALOR CUOTA</b></td><td><b>OBSERVACION</b></td></tr>";
					$query = "select sum(pagval) from nopag ";
					$query = $query."  where pagcod = '".$row[0]."'";
					$query = $query."  And pagano = '".$ano."'";
					$query = $query."  And pagmes = '".$mes."'";
					$query = $query."  And pagtip = 'Q'";
					$query = $query."  And pagsec = '".$qui."'";
					$query = $query."  And pagcon >= '5000' ";
					$query = $query."  And pagcon not in ( '5300','5182','5110','5111','5113','5131','5214') ";
					$err1 = odbc_do($conex_o,$query);
					$d=0;
					if(odbc_fetch_row($err1))
						$d=odbc_result($err1,1);
					$query = "select sum(pagval) from nopag ";
					$query = $query."  where pagcod = '".$row[0]."'";
					$query = $query."  And pagano = '".$ano."'";
					$query = $query."  And pagmes = '".$mes."'";
					$query = $query."  And pagtip = 'Q'";
					$query = $query."  And pagsec = '".$qui."'";
					$query = $query."  And pagcon in ('0001','0002','0003','0004','0005','0006','0007','0008','0014','0015','0016','0017','0020','0022','0023','0026','0030','0084','0085','0088','0089','0096','0097','0098','0099','5245','5247','0199') ";
					$err1 = odbc_do($conex_o,$query);
					$b=0;
					if(odbc_fetch_row($err1))
						$b=odbc_result($err1,1);	
					$query = "SELECT sum(Pnoval/ Pnocuo) from ".$empresa."_000046 ";
					$query .= " where  Pnocod = '".$row[0]."'";
					$query .= "     and Pnocuo= Pnocup ";
					$query .= "     and Pnoest= 'on' ";
					$err1 = mysql_query($query,$conex);
					$num = mysql_num_rows($err1);
					if($num > 0)
					{
						$row1 = mysql_fetch_array($err1);
						$d += $row1[0];
					}
					$d += ($monto / $cuotas);
					$por=(($d / $b) * 100);
					echo "<font size=3><b>DEDUCCIONES : $".number_format((double)$d,0,'.',',')." &nbsp&nbsp&nbspENDEUDAMIENTO : ".number_format((double)$por,2,'.',',')."%</b></font><br><br>";
					if((($d / $b) * 100) <= 40)
					{
						$color = "#009900";
						$obser="TIENE CUPO";
						
						echo "<input type='HIDDEN' name= 'emp' value='".substr($wemp,0,strpos($wemp,"-"))."'>";
						echo "<input type='HIDDEN' name= 'cod' value='".$row[0]."'>";
						echo "<input type='HIDDEN' name= 'nom' value='".$nombre."'>";
						echo "<input type='HIDDEN' name= 'cco' value='".$row[5]."'>";
						echo "<input type='HIDDEN' name= 'val' value=".$monto.">";
						echo "<input type='HIDDEN' name= 'cuo' value=".$cuotas.">";
						echo "<input type='HIDDEN' name= 'cup' value=".$cuotas.">";
					}
					else
					{
						$color = "#FF0000";
						$obser="NO TIENE CUPO";
					}
					$valcuo=$monto / $cuotas;
					echo "<input type='HIDDEN' name= 'codigo' value=".$codigo.">";
					echo "<input type='HIDDEN' name= 'wemp' value=".$wemp.">";
					echo "<input type='HIDDEN' name= 'radio1' value=1>";
					echo "<input type='HIDDEN' name= 'cuotas' value=".$cuotas.">";
					echo "<input type='HIDDEN' name= 'monto' value=".$monto.">";
					echo"<tr><td  align=right>".number_format((double)$monto,2,'.',',')."</td><td  align=right>".number_format((double)$cuotas,2,'.',',')."</td><td  align=right>".number_format((double)$valcuo,2,'.',',')."</td><td bgcolor=".$color.">".$obser."</td></tr>";
					if($obser == "TIENE CUPO")
						echo "<tr><td bgcolor=#dddddd align=center colspan=4><b>CONFIRMAR</b><input type='checkbox' name='ok'></td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center colspan=4><input type='submit' value='Continuar'></td></tr></table></center>";
				}
				else
				{
					echo "<input type='HIDDEN' name= 'codigo' value=".$codigo.">";
					echo "<input type='HIDDEN' name= 'wemp' value=".$wempaux.">";
					echo "<input type='HIDDEN' name= 'radio1' value=1>";
					echo "<input type='HIDDEN' name= 'monto' value=".$monto.">";
					if ($wsw == 0)
					{
						echo "<br><br><center><table border=0 aling=center>";
						echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table>";
						echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR NO EXISTE EL EMPLEADO  !!!!</MARQUEE></FONT>";
						echo "<input type='submit' value='Continuar'></center>";
						echo "<br><br>";
					}
					if ($monto < 5000)
					{
						echo "<br><br><center><table border=0 aling=center>";
						echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table>";
						echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR MONTO MENOR A $5.000 !!!!</MARQUEE></FONT>";
						echo "<input type='submit' value='Continuar'></center>";
						echo "<br><br>";
					}
					if ( $cuotas <= 0 or $cuotas > 4)
					{
						echo "<br><br><center><table border=0 aling=center>";
						echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table>";
						echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR LAS CUOTAS ESTAN INCORRECTAS MENOR DE 1 O MAYOR QUE 4 !!!!</MARQUEE></FONT>";
						echo "<input type='submit' value='Continuar'></center>";
						echo "<br><br>";
					}
					if ($monto/$cuotas < 5000)
					{
						echo "<br><br><center><table border=0 aling=center>";
						echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table>";
						echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR LAS CUOTAS SON MENORES DE $5.000 !!!!</MARQUEE></FONT>";
						echo "<input type='submit' value='Continuar'></center>";
						echo "<br><br>";
					}
				}
				odbc_close($conex_o);
				odbc_close_all();
			}
	else
	{
		echo "<br><br><center><table border=0 aling=center>";
		echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table>";
		echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR EL PRESTAMO YA FUE GENERADO</MARQUEE></FONT>";
		echo "<input type='submit' value='Continuar'></center>";
		echo "<br><br>";
	}
}
?>
</body>
</html>