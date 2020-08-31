<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5><b>Verificacion de Cupo de Endeudamiento x Nomina</b></font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> Cupos.php Ver. 2009-04-14</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
/**********************************************************************************************************************  
	   PROGRAMA : Cupos.php
	   Fecha de Liberación : 2008-11-04
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2009-04-14
	   
	   OBJETIVO GENERAL :Este programa permite evaluar la capacidad de pago de un usuario para hacer prestamos de farmacia
	   a traves de la nomina.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   .2009-04-14
	   		Se corrigio el programa para los codigo de mas de 5 digitos.
	   		
	   .2008-11-04
	   		Se agregaron los conceptos 0022, 0085, 0089, 0099, 5245 y 5247 en el calculo de los ingresos debido a la implementacion del Salario
			Flexible.
	
	   .2008-11-04
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
	

	

	echo "<form action='Cupos.php' method=post>";
	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	if(!isset($codigo) or !isset($monto))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td align=center colspan=2>VERIFICACION DE CUPO DE ENDEUDAMIENTO X NOMINA</td></tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc align=center>Identificacion del Usuario</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='codigo' size=9 maxlength=9></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Empresa</td>";
		$query = "SELECT Nomnom, Nomdes    from ".$empresa."_000036 where Nomusu='".$empresa."' order by Nomnom ";
		$err = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
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
		echo "</td></tr>";	
		echo "<td bgcolor=#cccccc align=center>Monto de la Compra</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='monto' size=12 maxlength=12></td></tr>";
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
		$query=$query."  Where percod = '".$codigo."'";
		$query=$query."  And peretr ='A'";
		$query=$query."  And percco = ccocod ";
		$query=$query."  And perofi = oficod  ";
		$err = odbc_do($conex_o,$query);
		$campos= odbc_num_fields($err);
		$wsw=0;
		if(odbc_fetch_row($err))
			$wsw=1;
		if ($wsw==1)
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
			echo "<tr><td rowspan=4 align=center><IMG SRC='/MATRIX/images/medical/root/matrix6.gif' ></td>";				
			echo "<td colspan=3 align=center><font size=5><b>PROMOTORA MEDICA LAS AMERICAS S.A.</b></font></td></tr>";
			echo "<tr><td colspan=3  align=center><font size=4>VERIFICACION DE LA CAPACIDAD DE ENDEUDAMIENTO X NOMINA Ver. 2008-11-04</font><td></tr>";
			echo "<tr><td bgcolor=#cccccc colspan=3 align=center>EMPLEADO : ".$row[0]."-".$nombre." CEDULA : ".$row[7]." OFICIO : ".$row[8]."-".$row[9]."</td></tr>";
			echo "<tr><td colspan=3 align=center>CENTRO DE COSTOS : ".$row[5]."-".$row[6]."</td></tr>";
			echo "<tr><td align=right><b>MONTO DEL PRESTAMO</b></td><td align=right><b>NRo. DE CUOTAS</b></td><td align=right><b>VALOR CUOTA</b></td><td><b>OBSERVACION</b></td></tr>";
			$query = "select sum(pagval) from nopag ";
			$query = $query."  where pagcod = '".$row[0]."'";
			$query = $query."  And pagano = '".$ano."'";
			$query = $query."  And pagmes = '".$mes."'";
			$query = $query."  And pagtip = 'Q'";
			$query = $query."  And pagsec = '".$qui."'";
			$query = $query."  And pagcon >= '5000' ";
			$query = $query."  And pagcon not in ( '5300','5182','5110','5111','5113','5131','5214') ";
			//echo $query."<br>";
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
			$query = $query."  And pagcon in ('0001','0002','0003','0004','0005','0006','0007','0008','0014','0015','0016','0017','0020','0022','0023','0026','0030','0084','0085','0088','0089','0096','0097','0098','0099','5245','5247') ";
			//echo $query."<br>";
			$err1 = odbc_do($conex_o,$query);
			$b=0;
			if(odbc_fetch_row($err1))
				$b=odbc_result($err1,1);
			$query = "SELECT sum(Pnoval/ Pnocuo) from ".$empresa."_000046 ";
			$query .= " where  Pnocod = '".$row[0]."'";
			$query .= "     and Pnocuo= Pnocup ";
			$query .= "     and Pnoest= 'on' ";
			$err1 = mysql_query($query,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($err1);
			if($num > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$d += $row1[0];
			}
			//echo "Ingresos : ".$b." Deducciones : ".$d."<br>";
			$DMax=$b *  0.4;
			$Cap=$DMax - $d;
			$obser="CUPO DISPONIBLE";
			$color = "#009900";
			for ($cuotas=1;$cuotas<5;$cuotas++)
			{
				$dT = $d + ($monto / $cuotas);
				$por=(($dT / $b) * 100);
				if((($dT / $b) * 100) <= 40)
				{
					$color = "#009900";
					$obser="TIENE CUPO";
				}
				else
				{
					$color = "#FF0000";
					$obser="NO TIENE CUPO";
				}
				$valcuo=$monto / $cuotas;
				echo"<tr><td  align=right>".number_format((double)$monto,2,'.',',')."</td><td  align=right>".number_format((double)$cuotas,2,'.',',')."</td><td  align=right>".number_format((double)$valcuo,2,'.',',')."</td><td bgcolor=".$color.">".$obser."</td></tr>";
			}
			echo "</table></center><br><br><br>";
			echo "<center><table border=1>";	
			echo "<tr><td align=center colspan=2 bgcolor=#999999><b>MAXIMA CUOTA X MES : ".number_format((double)$Cap,2,'.',',')."</b></td></tr>";
			echo "<tr><td align=center colspan=2 bgcolor=#999999><b>CORRESPONDE A PRESTAMOS DE :</b></td></tr>";
			echo "<tr><td align=right bgcolor=#DDDDDD><b>MONTO</b></td><td align=right bgcolor=#DDDDDD><b>CUOTAS</b></td></tr>";
			for ($cuotas=1;$cuotas<5;$cuotas++)
			{
				$monto=$Cap * $cuotas;
				$valcuo=$monto / $cuotas;
				echo"<tr><td align=right>".number_format((double)$monto,2,'.',',')."</td><td align=right>".number_format((double)$cuotas,2,'.',',')."</td></tr>";
			}
			echo "<tr><td bgcolor=#cccccc align=center colspan=4><input type='submit' value='Continuar'></td></tr></table></center>";
		}
		else
		{
			echo "<br><br><center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR NO EXISTE EL EMPLEADO  !!!!</MARQUEE></FONT>";
			echo "<input type='submit' value='Continuar'></center>";
			echo "<br><br>";
		}
	}
}
?>
</body>
</html>
