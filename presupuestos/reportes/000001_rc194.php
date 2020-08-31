<html>
<head>
  <title>MATRIX</title>
  <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#FFFFFF;font-size:7pt;font-family:Tahoma;font-weight:bold;}
    	.tipoTABLEGRID{font-family:Arial;border-style:solid;border-collapse:collapse;}
    	.tipotot{color:#000066;background:#C3D9FF;font-size:11pt;font-family:Arial;font-weight:bold;}
    	.tipotit{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;}
    	.tipouti{color:#000066;background:#81F781;font-size:12pt;font-family:Arial;font-weight:bold;}
    	.tiposub{color:#000066;background:#E8EEF7;font-size:12pt;font-family:Arial;font-weight:bold;}
    </style>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function enter()	
	{
		document.forms.estado.submit();
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Estado de Resultados  NIIF Cuentas en Participaci&oacute;n</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc194.php Ver. 2018-04-16</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strpos($chain,"-"));
}
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form name='estado' action='000001_rc194.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";

		if(!isset($wcco) or !isset($wemp) or $wemp == "Seleccione" or !isset($wpar) or $wpar == "Seleccione" or !isset($wano) or !isset($wperi)  or !isset($wperf) or $wperi< 1 or $wperi > 12 or $wperf < 1 or $wperf > 12 or $wperi > $wperf)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>ESTADO DE RESULTADOS NIIF CUENTAS EN PARTICIPACI&Oacute;N</td></tr>";
			echo "<tr><td bgcolor=#999999 align=center colspan=2>DATOS INICIALES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o</td>";
			if(isset($wano))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano' value='".$wano."' size=4 maxlength=4></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wano' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial</td>";
			if(isset($wperi))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperi' value='".$wperi."' size=2 maxlength=2></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperi' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final</td>";
			if(isset($wperf))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperf' value='".$wperf."' size=2 maxlength=2></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperf' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#999999 align=center colspan=2>CENTROS DE COSTOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad Proceso</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT Cepcco,cconom  from ".$empresa."_000167,".$empresa."_000005 where Cepcco=Ccocod  group by 1 order by 1";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wcco' OnChange='enter()'>";
				echo "<option>Seleccione</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					if(isset($wcco) and substr($wcco,0,strpos($wcco,"-")) == $row[0])
						echo "<option selected>".$row[0]."-".$row[1]."</option>";
					else
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Participe</td><td bgcolor=#cccccc align=center>";
			if(isset($wcco))
			{
				$query = "SELECT Cepnti,Cepnom  from ".$empresa."_000167 where Cepcco ='".substr($wcco,0,strpos($wcco,"-"))."' order by Cepnom";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wpar'>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if(isset($wpar) and substr($wpar,0,strpos($wpar,"-")) == $row[0])
							echo "<option selected>".$row[0]."-".$row[1]."</option>";
						else
							echo "<option>".$row[0]."-".$row[1]."</option>";
					}
					echo "</select>";
				}
			}
			else
			{
				echo "<select name='wpar'>";
				echo "<option>Seleccione</option>";
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<center><input type='HIDDEN' name= 'wcco' value='".$wcco."'>";
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
					if(isset($wemp) and substr($wemp,0,strpos($wemp,"-")) == $row[0])
						echo "<option selected>".$row[0]."-".$row[1]."</option>";
					else
						echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
		}
		else
		{
			echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);

			//                  0     1           2      
			$query = "SELECT Ceppro  from ".$empresa."_000167 ";
			$query = $query."  where Cepcco = '".substr($wcco,0,strpos($wcco,"-"))."'";
			$query = $query."    and Cepemp = '".$wemp."' ";
			$query = $query."    and Cepnti = '".substr($wpar,0,strpos($wpar,"-"))."'";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$wpor = $row[0] / 100;

			//                  0     1           2      
			$query = "SELECT rvpcpr,mganom,sum(rvpvre)  from ".$empresa."_000044,".$empresa."_000028,".$empresa."_000005 ";
			$query = $query."  where rvpano = ".$wano;
			$query = $query."    and rvpemp = '".$wemp."' ";
			$query = $query."    and rvpcco = '".substr($wcco,0,strpos($wcco,"-"))."'";
			$query = $query."    and rvpcco = Ccocod";
			$query = $query."    and ccoemp = '".$wemp."' ";
			$query = $query."    and rvpper between ".$wperi." and ".$wperf;
			$query = $query."    and rvpcpr = mgacod ";
			$query = $query."   group by rvpcpr,mganom";
			$query = $query."   order by rvpcpr";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);

			echo "<table border=1 class=tipoTABLEGRID>";
			echo "<tr><td colspan=11 align=center><A HREF='/matrix/presupuestos/Reportes/000001_rc194.php?empresa=".$empresa."&wperi=".$wperi."&wperf=".$wperf."&wcco=".$wcco."&wano=".$wano."&wemp=".$wempt."'>RETORNAR</A></td></tr>";
			echo "<tr><td colspan=11 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
			echo "<tr><td colspan=11 align=center>DIRECCION DE INFORMATICA</td></tr>";
			echo "<tr><td colspan=11 align=center>ESTADO DE RESULTADOS NIIF CUENTAS EN PARTICIPACI&Oacute;N</td></tr>";
			echo "<tr><td colspan=11 align=center>Datos Iniciales</td></tr>";
			echo "<tr><td colspan=11 align=center>A&Ntilde;O : ".$wano." MES INICIAL : ".$wperi. " MES FINAL : ".$wperf."</td></tr>";
			echo "<tr><td colspan=11 align=center>UNIDAD INICIAL : ".$wcco. "</td></tr>";
			echo "<tr><td colspan=11 align=center>EMPRESA : ".$wempt."</td></tr>";
			echo "<tr><td colspan=11 align=center>PARTICIPE : ".$wpar."</td></tr>";
			echo "<tr class='tipotit'><td><b>CODIGO</b></td><td><b>RUBRO</b></td><td><b>REAL : ".$wano."/".$wperi."-".$wperf."</b></td></tr>";
			$wdata=array();
			$k1=0;
			$num=-1;
			if ($num1 ==  0)
			{
				$k1++;
				$row1[0]='zzz';
				$row1[1]="";
				$row1[2]=0;
				$row1[3]=0;
			}

			for ($i=0;$i<$num1;$i++)
			{
				$row1 = mysql_fetch_array($err1);
				$num++;
				$wdata[$i][0]=$row1[0];
				$wdata[$i][1]=$row1[1];
				$wdata[$i][2]=$row1[2] * $wpor;
			}

			$wtotal=array();
			$ita=0;
			echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>INGRESOS DE OPERACIONES ORDINARIAS</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				if($wdata[$i][0] >= "100" and $wdata[$i][0] <= "129")
					$it=(integer)substr($wdata[$i][0],0,1);
				else 
					$it=0;
				if($it == 1)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					if((integer)$wdata[$i][0] == 100)
					{
						$wtotal[100][0] += $wdata[$i][2];
					}
					echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td></tr>";
				}
			}
			$it=1;
			echo"<tr class='tipotot'><td colspan=2><b>TOTAL INGRESOS DE OPERACIONES ORDINARIAS</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td></tr>";
			
			echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>COSTOS DE OPERACION</B></td></tr>";

			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)substr($wdata[$i][0],0,1);
				if($it == 2)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td></tr>";
				}
			}
			$it=2;
			echo"<tr class='tipotot'><td colspan=2><b>TOTAL COSTOS DE OPERACION</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td></tr>";

			echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>GASTOS DE ADMINISTRACION</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)substr($wdata[$i][0],0,1);
				if($it == 3)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td></tr>";
				}
			}
			$it=3;
			echo"<tr class='tipotot'><td colspan=2><b>TOTAL GASTOS DE ADMINISTRACION</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td></tr>";

			echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>GASTOS DE VENTAS</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)substr($wdata[$i][0],0,1);
				if($it == 8)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td></tr>";
				}
			}
			$it=8;
			echo"<tr class='tipotot'><td colspan=2><b>TOTAL GASTOS DE VENTAS</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td></tr>";

			echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>OTROS INGRESOS</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				if($wdata[$i][0] >= "130" and $wdata[$i][0] <= "199")
					$it=99;
				else 
					$it=0;
				if($it == 99)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					if((integer)$wdata[$i][0] == 100)
					{
						$wtotal[100][0] += $wdata[$i][2];
					}
					echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td></tr>";
				}
			}
			$it=99;
			echo"<tr class='tipotot'><td colspan=2><b>TOTAL OTROS INGRESOS</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td></tr>";
			
			echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>OTROS GASTOS DE OPERACION</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)substr($wdata[$i][0],0,1);
				if($it == 5)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td></tr>";
				}
			}
			$it=5;
			echo"<tr class='tipotot'><td colspan=2><b>TOTAL OTROS GASTOS DE OPERACION</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td></tr>";
			
			$wtotal[10][0] = $wtotal[1][0] - $wtotal[2][0] - $wtotal[3][0] - $wtotal[8][0] - $wtotal[5][0] + $wtotal[99][0];
			echo"<tr class='tipouti'><td colspan=2><b>RESULTADOS DE ACTIVIDADES DE LA OPERACION</b></td><td align=right>".number_format((double)$wtotal[10][0],0,'.',',')."</td></tr>";
			echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>INGRESO FINANCIERO</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)substr($wdata[$i][0],0,1);
				if($it == 4)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td></tr>";
				}
			}
			$it=4;
			echo"<tr class='tipotot'><td colspan=2><b>TOTAL INGRESO FINANCIERO</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td></tr>";

			echo "<tr><td bgcolor='#E8EEF7' colspan=11><b>GASTO FINANCIERO</B></td></tr>";
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)substr($wdata[$i][0],0,1);
				if($it == 6)
				{
					$wtotal[$it][0] += $wdata[$i][2];
					echo"<tr><td>".$wdata[$i][0]."</td><td>".$wdata[$i][1]."</td><td align=right>".number_format((double)$wdata[$i][2],0,'.',',')."</td></tr>";
				}
			}
			$it=6;
			echo"<tr class='tipotot'><td colspan=2><b>TOTAL GASTO FINANCIERO</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td></tr>";

			$wtotal[11][0] = $wtotal[4][0] - $wtotal[6][0];
			echo"<tr class='tiposub'><td colspan=2><b>COSTO FINANCIERO NETO</b></td><td align=right>".number_format((double)$wtotal[11][0],0,'.',',')."</td></tr>";

			$wtotal[12][0] = $wtotal[10][0] + $wtotal[11][0];
			echo"<tr class='tipouti'><td colspan=2><b>GANANCIAS ANTES DE IMPUESTOS</b></td><td align=right>".number_format((double)$wtotal[12][0],0,'.',',')."</td></tr>";
			
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)$wdata[$i][0];
				if($it == 760)
				{
					$wtotal[$it][0] += $wdata[$i][2];
				}
			}
			$it=760;
			echo"<tr><td colspan=2><b>PROVISION IMPUESTO DE RENTA Y CREE</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td></tr>";
			
			for ($i=0;$i<=$num;$i++)
			{
				$it=(integer)$wdata[$i][0];
				if($it == 770)
				{
					$wtotal[$it][0] += $wdata[$i][2];
				}
			}
			$it=770;
			echo"<tr><td colspan=2><b>IMPUESTO RENTA DIFERIDO</b></td><td align=right>".number_format((double)$wtotal[$it][0],0,'.',',')."</td></tr>";
			$wtotal[13][0] = $wtotal[12][0] - $wtotal[760][0] - $wtotal[770][0];
			echo"<tr class='tipouti'><td colspan=2><font size=2.5><b>RESULTADOS PROCEDENTES DE OPERACIONES CONTINUADAS</b></font></td><td align=right>".number_format((double)$wtotal[13][0],0,'.',',')."</td></tr>";
            echo "</tabla>";
		}
	}
?>
</body>
</html>
