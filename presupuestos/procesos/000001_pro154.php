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
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Distribucion de NITS Nuevos</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_pro154.php Ver. 2016-06-17</b></font></tr></td></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form  name='Distribucion' action='000001_pro154.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or $wper1 < 1 or $wper1 > 12 or !isset($wper2) or $wper2 < 1 or $wper2 > 12 or $wper2 < $wper1 or !isset($wcco1) or !isset($wcco2))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>DISTRIBUCION DE NITS NUEVOS</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
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
				for ($i=0;$i<=$num;$i++)
				{
					if($wdat[$i][5] != "NO")
					{
						$query = "select count(*) from ".$empresa."_000139  ";
						$query .= "   where Pfecco = '".$wdat[$i][1]."' ";
						$query .= "     and Pfeemp = '".$wemp."' ";
						$query .= "     and Pferub = '".$wdat[$i][3]."' ";
						$query .= "     and Pfenit = '".$wdat[$i][4]."' ";
						$err = mysql_query($query,$conex);
						$row = mysql_fetch_array($err);
						if($row[0] == 0)
						{
							$fecha = date("Y-m-d");
							$hora = (string)date("H:i:s");
							$query = "insert ".$empresa."_000139 (medico,fecha_data,hora_data,Pfeemp, Pfecco, Pferub, Pfenit, Pfecri, Pfecon, Pfenew, Seguridad) values ('".$empresa."";
							$query .=  "','";
							$query .=  $fecha."','";
							$query .=  $hora."','";
							$query .=  $wemp."','";
							$query .=  $wdat[$i][1]."','";
							$query .=  $wdat[$i][3]."','";
							$query .=  $wdat[$i][4]."','";
							$query .=  $wdat[$i][5]."','";
							$query .=  $wdat[$i][6]."',";
							$query .=  "'off','C-".$empresa."')";
							$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO FILTRO EXPLICACIONES : ".mysql_errno().":".mysql_error());
						}
					}
					for ($j=0;$j<10;$j++)
					{
						$w=(4*$j)+7;
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
							$query .= "     and Rcdseq = ".$wdat[$i][50];
							$err = mysql_query($query,$conex);
							$row = mysql_fetch_array($err);
							if($row[0] == 0)
							{
								if($wdat[$i][$w] != "S")
								{
									$fecha = date("Y-m-d");
									$hora = (string)date("H:i:s");
									$query = "insert ".$empresa."_000101 (medico,fecha_data,hora_data,Rcdemp, Rcdano, Rcdmes, Rcdcco, Rcdgas, Rcdsga, Rcdtip, Rcddri, Rcdpor, Rcdseq, Seguridad) values ('".$empresa."";
									$query .=  "','";
									$query .=  $fecha."','";
									$query .=  $hora."','";
									$query .=  $wemp."',";
									$query .=  $wdat[$i][0].",";
									$query .=  $wdat[$i][2].",'";
									$query .=  $wdat[$i][1]."','";
									$query .=  $wdat[$i][3]."','";
									$query .=  $wdat[$i][4]."','";
									$query .=  $wdat[$i][$w]."','";
									$query .=  $wdat[$i][$w+1]."',";
									$query .=  $wdat[$i][$w+3].",";
									$query .=  $wdat[$i][50].",";
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
										$query = "insert ".$empresa."_000101 (medico,fecha_data,hora_data,Rcdemp, Rcdano, Rcdmes, Rcdcco, Rcdgas, Rcdsga, Rcdtip, Rcddri, Rcdpor, Rcdseq, Seguridad) values ('".$empresa."";
										$query .=  "','";
										$query .=  $fecha."','";
										$query .=  $hora."','";
										$query .=  $wemp."',";
										$query .=  $wdat[$i][0].",";
										$query .=  $wdat[$i][2].",'";
										$query .=  $wdat[$i][1]."','";
										$query .=  $wdat[$i][3]."','";
										$query .=  $wdat[$i][4]."','";
										$query .=  $wdat[$i][$w]."','";
										$query .=  $wdat[$i][$w+2]."',";
										$query .=  $wdat[$i][$w+3].",";
										$query .=  $wdat[$i][50].",";
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
			echo "<tr><td align=center colspan=56><font size=2>PROMOTORA MEDICA LAS AMERICAS S.A.</font></td></tr>";
			echo "<tr><td align=center colspan=56><font size=2>DIRECCION DE INFORMATICA</font></td></tr>";
			echo "<tr><td align=center colspan=56><font size=2>DISTRIBUCION DE NITS NUEVOS</font></td></tr>";
			echo "<tr><td align=center colspan=56><font size=2>EMPRESA : ".$wempt."</font></td></tr>";
			echo "<tr><td align=center bgcolor=#999999 colspan=56><font size=2><b>A&Ntilde;O : ".$wanop." DESDE MES : ".$wper1." HASTA EL MES : ".$wper2." </b></font></td></tr>";
			echo "<tr><td align=center bgcolor=#999999 colspan=56><font size=2><b>CENTROS DE COSTOS : ".$wcco1."-".$wcco2."</b></font></td></tr>";
			echo "<tr><td id=tipo1><b>A&Ntilde;O</b></td><td id=tipo1><b>MES</b></td><td id=tipo1><b>C.C.</b></td><td id=tipo1><b>CODIGO<BR>RUBRO</b></td><td id=tipo1><b>RUBRO</b></td><td id=tipo1><b>NIT</b></td><td id=tipo1><b>DESCRIPCION NIT</b></td><td id=tipo1><b>DETALLE COSTO</b></td><td id=tipo1><b>CRITERIO</b></td><td id=tipo1><b>CONCILIADO</b></td><td id=tipo1><b>COSTO<BR>TOTAL</b></td>";
			for ($i=0;$i<10;$i++)
			{
				echo "<td id=tipo1><b>TIPO<BR>DIST.</b></td>";
				echo "<td id=tipo1><b>DRIVER</b></td>";
				echo "<td id=tipo1><b>SUBP.</b></td>";
				echo "<td id=tipo1><b>PORCENTAJE</b></td>";
			}
			echo "<td id=tipo1><b>NRO. EXPLICACION</b></td>";
			echo "</tr>";
			//                  0      1       2       3       4       5       6       7       8       9       10
			$query = "select Expano, Expcco, Expper, Expcpr, Expnit, Expnte, Expexp, Expmon, Expcue, Mganom, Expcon  from ".$empresa."_000011,".$empresa."_000028  ";
			$query .= " where Expcco between '".$wcco1."' and  '".$wcco2."'";
			$query .= "   and Expemp = '".$wemp."' ";
  			$query .= "   and Expano = ".$wanop;
            $query .= "   and Expper between ".$wper1." and ".$wper2;
            $query .= "   and Expnit not in ('1091','2034')";
            $query .= "   and Expcpr not in ('201','203','204')";
            $query .= "   and Expcpr > '199' ";
            $query .= "   and Expcpr = Mgacod ";
            $query .= "  UNION ALL ";
            $query .= " select Expano, Expcco, Expper, Expcpr, Expnit, Expnte, Expexp, Expmon, Expcue, Mganom, Expcon  from ".$empresa."_000011,".$empresa."_000028  ";
			$query .= " where Expcco = '".$wcco1."' and  '".$wcco2."'";
			$query .= "   and Expemp = '".$wemp."' ";
  			$query .= "   and Expano = ".$wanop;
            $query .= "   and Expper between ".$wper1." and ".$wper2;
            $query .= "   and Expnit != '0' ";
            $query .= "   and Expcpr in ('201','203','204')";
            $query .= "   and Expcpr = Mgacod ";
            $query .= " Order by 1,2,3,4,5";  
		
 			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			$wdat=array();
			$k=-1;
			echo "<input type='HIDDEN' name= 'wcco1' value='".$wcco1."'>";
			echo "<input type='HIDDEN' name= 'wcco2' value='".$wcco2."'>";
			echo "<input type='HIDDEN' name= 'wper1' value='".$wper1."'>";
			echo "<input type='HIDDEN' name= 'wper2' value='".$wper2."'>";
			echo "<input type='HIDDEN' name= 'wanop' value='".$wanop."'>";
			//echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
			for ($i=0;$i<$num;$i++)
			{
				$wsw=0;
				$row = mysql_fetch_array($err);
				$query = "select Pfecco, Pferub, Pfenit, Pfenew  from ".$empresa."_000139  ";
				$query .= " where Pfecco = '".$row[1]."' ";
				$query .= "   and Pfeemp = '".$wemp."' ";
	  			$query .= "   and Pferub = '".$row[3]."' ";
	            $query .= "   and Pfenit = '".$row[4]."' ";
	 			$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					if($row1[3] == "on")
					{
						$query = "select count(*) from ".$empresa."_000101  ";
						$query .= "   where Rcdano = ".$row[0]." ";
						$query .= "     and Rcdemp = '".$wemp."' ";
						$query .= "     and Rcdmes = ".$row[2]." ";
						$query .= "     and Rcdcco = '".$row[1]."' ";
						$query .= "     and Rcdgas = '".$row[3]."' ";
						$query .= "     and Rcdsga = '".$row[4]."' ";
						$query .= "     and Rcdseq = ".$row[10];
						$err2 = mysql_query($query,$conex);
						$row2 = mysql_fetch_array($err2);
						if($row2[0] == 0)
							$wsw=1;
					}
				}
				else
					$wsw=1;
				if($wsw == 1)
				{
					$k++;
					if($k % 2 == 0)
						$color="#99CCFF";
					else
						$color="#ffffff";
					echo "<input type='HIDDEN' name= 'wdat[".$k."][0]' value='".$row[0]."'>";
					echo "<input type='HIDDEN' name= 'wdat[".$k."][1]' value='".$row[1]."'>";
					echo "<input type='HIDDEN' name= 'wdat[".$k."][2]' value='".$row[2]."'>";
					echo "<input type='HIDDEN' name= 'wdat[".$k."][3]' value='".$row[3]."'>";
					echo "<input type='HIDDEN' name= 'wdat[".$k."][4]' value='".$row[4]."'>";
					echo "<input type='HIDDEN' name= 'wdat[".$k."][50]' value='".$row[10]."'>";
					if($row[7] > 0)
						$wctouni=$row[8] / $row[7];
					else
						$wctouni=0;
					echo "<tr><td bgcolor=".$color."><font size=2>".$row[0]."</font></td><td bgcolor=".$color."><font size=2>".$row[2]."</font></td><td bgcolor=".$color."><font size=2>".$row[1]."</font></td><td bgcolor=".$color."><font size=2>".$row[3]."</font></td><td bgcolor=".$color."><font size=2>".$row[9]."</font></td><td bgcolor=".$color."><font size=2>".$row[4]."</font></td><td bgcolor=".$color."><font size=2>".$row[5]."</font></td><td bgcolor=".$color." align=right><font size=2>".$row[6]."</font></td>";
					echo "<td bgcolor=".$color."><font size=2>";
					echo "<select name='wdat[".$k."][5]'>";
					echo "<option>NO</option>";
					echo "<option>on</option>";
					echo "<option>off</option>";
					echo "</select>";
					echo "</td>";
					echo "<td bgcolor=".$color."><font size=2>";
					echo "<select name='wdat[".$k."][6]'>";
					echo "<option>on</option>";
					echo "<option>off</option>";
					echo "</select>";
					echo "</td>";
					echo "<td bgcolor=".$color." align=right><font size=2>".number_format($row[7],0,'.',',')."</font></td>";
					for ($j=0;$j<10;$j++)
					{
						$w=(4*$j)+7;
						echo "<td bgcolor=".$color."><font size=2>";
						echo "<select name='wdat[".$k."][".$w."]'>";
						echo "<option>D</option>";
						echo "<option>S</option>";
						echo "</select>";
						echo "</td>";
						$w++;
						echo "<td bgcolor=".$color."><font size=2>";
						echo "<select name='wdat[".$k."][".$w."]'>";
						for ($s=0;$s<$numD;$s++)
							echo "<option>".$driver[$s]."</option>";
						echo "</td>";
						$w++;
						echo "<td bgcolor=".$color."><font size=2><input type='TEXT' name='wdat[".$k."][".$w."]' size=5 maxlength=5></font></td>";
						$w++;
						echo "<td bgcolor=".$color."><font size=2><input type='TEXT' name='wdat[".$k."][".$w."]' size=5 onkeypress='return teclado(event)' maxlength=5></font></td>";
					}
					echo "<td bgcolor=".$color." align=right><font size=2>".number_format($row[10],0,'.',',')."</font></td>";
					echo "</tr>";
				}
    		}
    		echo "<input type='HIDDEN' name= 'num' value='".$k."'>";
    		echo "<tr><td align=center colspan=56 bgcolor=#999999><b>GRABAR</b><input type='checkbox' name='ok' onclick='enter()'></td></tr>";
    		echo "</table></center>";
		}
	}
?>
</body>
</html>
