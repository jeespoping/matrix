<html>
<head>
  <title>MATRIX</title>
  <style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	#tipo1{color:#000066;background:#DDDDDD;font-size:7pt;font-family:Arial;font-weight:bold;text-align:center;}
    	
  </style>
  <script type="text/javascript">
	<!--
	function teclado(e)  
	{ 
		var navegador = navigator.appName;
		var version = navigator.appVersion;
		if(navegador.substring(0,9) == "Microsoft")
		{
			if ((event.keyCode < 48 || event.keyCode > 57  || event.keyCode == 13) && event.keyCode != 46) event.returnValue = false;
		}
		else
		{
			//alert(e.which);
			//return ((e.which >= 48 && e.which <= 57  || e.which == 13 || e.which < 32) && e.which != 46); //Solo números
			return (e.which >= 48 && e.which <= 57  || e.which == 13 || e.which == 46); //Solo números
			//return ((e.which < 48 || e.which > 57  || e.which == 13) && e.which != 46);
		}
	}
	function enter()
	{
		document.forms.Distribucion.submit();
	}
	//-->
	</script>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Distribucion de Insumos Nuevos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro152.php Ver. 2016-09-22</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form  name='Distribucion' action='000001_pro152.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or $wper1 < 1 or $wper1 > 12 or !isset($wper2) or $wper2 < 1 or $wper2 > 12 or $wper2 < $wper1 or !isset($wcco1) or !isset($wcco2))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>DISTRIBUCION DE INSUMOS NUEVOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&Ntilde;o de Proceso</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso Final</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro Costos Inicial</td>";
			echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Centro Costos Final</td>";
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
			$wempt = $wemp;
			$wemp = substr($wemp,0,2);
			echo "<input type='HIDDEN' name= 'wemp' value='".$wemp."'>";
			echo "<input type='HIDDEN' name= 'wempt' value='".$wempt."'>";
			if(isset($ok))
			{
				$error="";
				for ($i=0;$i<$num;$i++)
				{
					if($wdat[$i][5] != "NO")
					{
						$query = "select count(*) from ".$empresa."_000130  ";
						$query .= "   where Ifacco = '".$wdat[$i][2]."' ";
						$query .= "     and ifaemp = '".$wemp."' ";
						$query .= "     and Ifains = '".$wdat[$i][4]."' ";
						$err = mysql_query($query,$conex);
						$row = mysql_fetch_array($err);
						if($row[0] == 0)
						{
							$fecha = date("Y-m-d");
							$hora = (string)date("H:i:s");
							$query = "insert ".$empresa."_000130 (medico,fecha_data,hora_data,Ifaemp, Ifacco, Ifains, Ifatip, Ifanew, Seguridad) values ('".$empresa."";
							$query .=  "','";
							$query .=  $fecha."','";
							$query .=  $hora."','";
							$query .=  $wemp."','";
							$query .=  $wdat[$i][2]."','";
							$query .=  $wdat[$i][4]."','";
							$query .=  $wdat[$i][5]."',";
							$query .=  "'on','C-".$empresa."')";
							$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO INSUMOS FACTURABLES : ".mysql_errno().":".mysql_error());
						}
					}
					for ($j=0;$j<10;$j++)
					{
						$w=$j+(3*($j+2));
						if($wdat[$i][$w+3] != "")
						{
							$query = "select count(*) from ".$empresa."_000101  ";
							$query .= "   where Rcdano = ".$wdat[$i][0]." ";
							$query .= "     and Rcdemp = '".$wemp."' ";
							$query .= "     and Rcdmes = ".$wdat[$i][1]." ";
							$query .= "     and Rcdcco = '".$wdat[$i][2]."' ";
							$query .= "     and Rcdgas = '".$wdat[$i][3]."' ";
							$query .= "     and Rcdsga = '".$wdat[$i][4]."' ";
							$query .= "     and Rcdtip = '".$wdat[$i][$w]."' ";
							$query .= "     and Rcddri = '".$wdat[$i][$w+1]."' ";
							$err = mysql_query($query,$conex);
							$row = mysql_fetch_array($err);
							if($row[0] == 0)
							{
								if($wdat[$i][$w] != "S")
								{
									$fecha = date("Y-m-d");
									$hora = (string)date("H:i:s");
									$query = "insert ".$empresa."_000101 (medico,fecha_data,hora_data,Rcdemp, Rcdano, Rcdmes, Rcdcco, Rcdgas, Rcdsga, Rcdtip, Rcddri, Rcdpor, Seguridad) values ('".$empresa."";
									$query .=  "','";
									$query .=  $fecha."','";
									$query .=  $hora."','";
									$query .=  $wemp."',";
									$query .=  $wdat[$i][0].",";
									$query .=  $wdat[$i][1].",'";
									$query .=  $wdat[$i][2]."','";
									$query .=  $wdat[$i][3]."','";
									$query .=  $wdat[$i][4]."','";
									$query .=  $wdat[$i][$w]."','";
									$query .=  $wdat[$i][$w+1]."',";
									$query .=  $wdat[$i][$w+3].",";
									$query .=  "'C-".$empresa."')";
									$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO DISTRIBUCION DE RECURSOS : ".mysql_errno().":".mysql_error());
								}
								else
								{
									$query = "select Subcod from ".$empresa."_000104  ";
									$query .= "   where Subcod = '".$wdat[$i][$w+2]."' ";
									$err = mysql_query($query,$conex);
									$num1 = mysql_num_rows($err);
									if($num1 > 0)
									{
										$fecha = date("Y-m-d");
										$hora = (string)date("H:i:s");
										$query = "insert ".$empresa."_000101 (medico,fecha_data,hora_data,Rcdemp, Rcdano, Rcdmes, Rcdcco, Rcdgas, Rcdsga, Rcdtip, Rcddri, Rcdpor, Seguridad) values ('".$empresa."";
										$query .=  "','";
										$query .=  $fecha."','";
										$query .=  $hora."','";
										$query .=  $wemp."',";
										$query .=  $wdat[$i][0].",";
										$query .=  $wdat[$i][1].",'";
										$query .=  $wdat[$i][2]."','";
										$query .=  $wdat[$i][3]."','";
										$query .=  $wdat[$i][4]."','";
										$query .=  $wdat[$i][$w]."','";
										$query .=  $wdat[$i][$w+2]."',";
										$query .=  $wdat[$i][$w+3].",";
										$query .=  "'C-".$empresa."')";
										$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO DISTRIBUCION DE RECURSOS : ".mysql_errno().":".mysql_error());
									}
									else
										$error .= $wdat[$i][4]." ".$wdat[$i][$w]." ".$wdat[$i][$w+2]." ERROR SUBPROCESO NO EXISTE REVISE SO$#&&&##!!!<br>";
								}
							}
							else
								$error .= $wdat[$i][4]." ".$wdat[$i][$w]." ".$wdat[$i][$w+2]." LA DISTRIBUCION YA EXISTE REVISE SO$#&&&##!!!<br>";
						}
					}
				}
			}
			$driver=array();
			$query = "select Dricod from ".$empresa."_000085  ";
  			$query .= "   where Driest = 'on' ";
 			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$driver[$i] = $row[0];
				}
			}
			$numD=$num;
			
			if(isset($error))
				echo $error."<br>";
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=55><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
			echo "<tr><td align=center colspan=55><font size=2>DIRECCION DE INFORMATICA</font></td></tr>";
			echo "<tr><td align=center colspan=55><font size=2>DISTRIBUCION DE INSUMOS NUEVOS</font></td></tr>";
			echo "<tr><td align=center colspan=55><font size=2>EMPRESA : ".$wempt."</font></td></tr>";
			echo "<tr><td align=center bgcolor=#999999 colspan=55><font size=2><b>A&Ntilde;O : ".$wanop." DESDE MES : ".$wper1." HASTA EL MES : ".$wper2." </b></font></td></tr>";
			echo "<tr><td align=center bgcolor=#999999 colspan=55><font size=2><b>CENTROS DE COSTOS : ".$wcco1."-".$wcco2."</b></font></td></tr>";
			echo "<tr><td id=tipo1><b>A&Ntilde;O</b></td><td id=tipo1><b>MES</b></td><td id=tipo1><b>C.C.</b></td><td id=tipo1><b>CODIGO<BR>RUBRO</b></td><td id=tipo1><b>RUBRO</b></td><td id=tipo1><b>CODIGO</b></td><td id=tipo1><b>DESCRIPCION</b></td><td id=tipo1><b>CANTIDAD</b></td><td id=tipo1><b>COSTO<BR>UNITARIO</b></td><td id=tipo1><b>COSTO<BR>TOTAL</b></td><td id=tipo1><b>TIPO<BR>INSUMO</b></td>";
			for ($i=0;$i<10;$i++)
			{
				echo "<td id=tipo1><b>TIPO<BR>DIST.</b></td>";
				echo "<td id=tipo1><b>DRIVER</b></td>";
				echo "<td id=tipo1><b>SUBP.</b></td>";
				echo "<td id=tipo1><b>PORCENTAJE</b></td>";
			}
			echo "</tr>";
			//                                 0                       1                      2                      3                     4                       5                      6                          7                           8    
			$query = "select ".$empresa."_000002.almano,".$empresa."_000002.almmes,".$empresa."_000002.almcco,".$empresa."_000028.Mganom,".$empresa."_000002.Almcod,".$empresa."_000002.Almdes,".$empresa."_000002.almcpr,sum(".$empresa."_000002.Almcan),sum(".$empresa."_000002.Almcto) from ".$empresa."_000002,".$empresa."_000028  ";
			$query .= " where ".$empresa."_000002.almcco BETWEEN '".$wcco1."' and  '".$wcco2."'";
			$query .= "   and ".$empresa."_000002.almemp = '".$wemp."'";
  			$query .= "   and ".$empresa."_000002.almano = ".$wanop;
            $query .= "   and ".$empresa."_000002.almmes BETWEEN ".$wper1." and ".$wper2;
            $query .= "   and ".$empresa."_000002.almcpr = ".$empresa."_000028.Mgacod ";
			$query .= "   and ".$empresa."_000002.Almcod not in  ";
 			$query .= "   (select ".$empresa."_000100.Procod from ".$empresa."_000100 where ".$empresa."_000100.Proemp='".$wemp."' and ".$empresa."_000100.Procco=".$empresa."_000002.almcco and (".$empresa."_000100.Protip='2' or  ".$empresa."_000100.Protip='4') ";
 			$query .= "    union  ";
 			$query .= "    select ".$empresa."_000130.Ifains from ".$empresa."_000130 where ".$empresa."_000130.Ifaemp='".$wemp."' and ".$empresa."_000130.Ifacco=".$empresa."_000002.almcco AND ".$empresa."_000130.Ifanew='on') ";
 			//$query .= "  group by ".$empresa."_000002.almano,".$empresa."_000002.almmes,".$empresa."_000002.almcco,".$empresa."_000028.Mganom,".$empresa."_000002.Almcod,".$empresa."_000002.Almdes,".$empresa."_000002.almcpr ";
 			$query .= "  group by ".$empresa."_000002.almano,".$empresa."_000002.almmes,".$empresa."_000002.almcco,".$empresa."_000028.Mganom,".$empresa."_000002.Almcod ";
 			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$wdat=array();
			echo "<input type='HIDDEN' name= 'wcco1' value='".$wcco1."'>";
			echo "<input type='HIDDEN' name= 'wcco2' value='".$wcco2."'>";
			echo "<input type='HIDDEN' name= 'wper1' value='".$wper1."'>";
			echo "<input type='HIDDEN' name= 'wper2' value='".$wper2."'>";
			echo "<input type='HIDDEN' name= 'wanop' value='".$wanop."'>";
			echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
			for ($i=0;$i<$num;$i++)
			{
				if($i % 2 == 0)
					$color="#99CCFF";
				else
					$color="#ffffff";
				$row = mysql_fetch_array($err);
				echo "<input type='HIDDEN' name= 'wdat[".$i."][0]' value='".$row[0]."'>";
				echo "<input type='HIDDEN' name= 'wdat[".$i."][1]' value='".$row[1]."'>";
				echo "<input type='HIDDEN' name= 'wdat[".$i."][2]' value='".$row[2]."'>";
				echo "<input type='HIDDEN' name= 'wdat[".$i."][3]' value='".$row[6]."'>";
				echo "<input type='HIDDEN' name= 'wdat[".$i."][4]' value='".$row[4]."'>";
				if($row[7] > 0)
					$wctouni=$row[8] / $row[7];
				else
					$wctouni=0;
				echo "<tr><td bgcolor=".$color."><font size=2>".$row[0]."</font></td><td bgcolor=".$color."><font size=2>".$row[1]."</font></td><td bgcolor=".$color."><font size=2>".$row[2]."</font></td><td bgcolor=".$color."><font size=2>".$row[6]."</font></td><td bgcolor=".$color."><font size=2>".$row[3]."</font></td><td bgcolor=".$color."><font size=2>".$row[4]."</font></td><td bgcolor=".$color."><font size=2>".$row[5]."</font></td><td bgcolor=".$color." align=right><font size=2>".number_format($row[7],0,'.',',')."</font></td><td bgcolor=".$color." align=right><font size=2>".number_format($wctouni,0,'.',',')."</font></td><td bgcolor=".$color." align=right><font size=2>".number_format($row[8],0,'.',',')."</font></td>";
				echo "<td bgcolor=".$color."><font size=2>";
				echo "<select name='wdat[".$i."][5]'>";
				echo "<option>NO</option>";
				echo "<option>D</option>";
				echo "<option>E</option>";
				echo "<option>F</option>";
				echo "</select>";
				echo "</td>";
				for ($j=0;$j<10;$j++)
				{
					$w=$j+(3*($j+2));
					echo "<td bgcolor=".$color."><font size=2>";
					echo "<select name='wdat[".$i."][".$w."]'>";
					echo "<option>D</option>";
					echo "<option>S</option>";
					echo "</select>";
					echo "</td>";
					$w++;
					echo "<td bgcolor=".$color."><font size=2>";
					echo "<select name='wdat[".$i."][".$w."]'>";
					for ($s=0;$s<$numD;$s++)
						echo "<option>".$driver[$s]."</option>";
					echo "</td>";
					$w++;
					echo "<td bgcolor=".$color."><font size=2><input type='TEXT' name='wdat[".$i."][".$w."]' size=5 maxlength=5></font></td>";
					$w++;
					echo "<td bgcolor=".$color."><font size=2><input type='TEXT' name='wdat[".$i."][".$w."]' size=5 onkeypress='return teclado(event)' maxlength=5></font></td>";
				}
				echo "</tr>";
    		}
    		echo "<tr><td align=center colspan=41 bgcolor=#999999><b>GRABAR</b><input type='checkbox' name='ok' onclick='enter()'></td></tr>";
    		echo "</table></center>";
		}
	}
?>
</body>
</html>
