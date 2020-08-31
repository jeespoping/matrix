<html>
<head>
  <title>MATRIX Impresion de Votos Para Asamblea de Accionistas / Copropietarios 2009-12-09</title>
  <style>
	  #tipo1{color:#000000;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:bold;text-align:center;}
	  #tipo2{color:#000000;background:#DDDDDD;font-size:10pt;font-family:Tahoma;font-weight:normal;text-align:center;}
	  .tipo3{color:#000000;background:#FFFFFF;font-size:25pt;font-family:'3 of 9 Barcode';font-weight:normal;text-align:center;}
	  #tipo4{color:#000000;background:#FFFFFF;font-size:10pt;font-family:Tahoma;font-weight:normal;text-align:left;}
  </style>
</head>
<body BGCOLOR="">
<BODY TEXT="#000000">
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		echo "<form action='votos.php' method=post>";
		

		

		echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wemp) or !isset($wtip) or !isset($wpar) or !isset($ci) or !isset($cf) or !isset($wtiv) or !isset($wfec))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>IMPRESION DE VOTOS ASAMBLEAS DE LAS AMERICAS Ver. 2009-03-09</b></td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc>Empresa</td>";			
			echo "<td bgcolor=#cccccc>";
			echo "<select name='wemp' onchange='submit();'>";
			if(!isset($wemp))
			{
				echo "<option>Seleccione la empresa</option>";
			}
			$query = "select Empcod, Empdes from ".$empresa."_000004 order by Empcod";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			for ($i=0;$i<$num1;$i++)
			{	
				$row1 = mysql_fetch_array($err1);
				if(isset($wemp) and $wemp == $row1[0]."-".$row1[1])
					echo "<option>".$row1[0]."-".$row1[1]."</option>";
					// echo "<option selected>".$row1[0]."-".$row1[1]."</option>";
				else
					echo "<option>".$row1[0]."-".$row1[1]."</option>";
			}
			echo "</td></tr>";
			if(isset($wemp))
			{
				echo "<td bgcolor=#cccccc>Tipo de Voto</td>";			
				echo "<td bgcolor=#cccccc>";
				$query = "select Parcod, Pardes from ".$empresa."_000002 where Paremp = '".substr($wemp,0,2)."' order by Parcod";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				echo "<select name='wtiv'>";
				for ($i=0;$i<$num1;$i++)
				{	
					$row1 = mysql_fetch_array($err1);
					echo "<option>".$row1[0]."-".$row1[1]."</option>";
				}
				echo "</td></tr>";
			}
			
			echo "<tr><td bgcolor=#cccccc align=center>Fecha de la Asamblea</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wfec' size=10 maxlength=10 value='".$wfec."'></td></tr>";
			
			echo "<tr><td bgcolor=#cccccc>TIPO DE ASAMBLEA</td>";			
			if(isset($wtip))
				if($wtip == "0")
					echo "<td bgcolor=#cccccc><input type='RADIO' name='wtip' value='0' checked> ORDINARIA <input type='RADIO' name='wtip' value='1'> EXTRAORDINARIA <input type='RADIO' name='wtip' value='2' > FIRMANTES DEL ACUERDO  </td></tr>";
				elseif($wtip == "1")
						echo "<td bgcolor=#cccccc><input type='RADIO' name='wtip' value='0'> ORDINARIA <input type='RADIO' name='wtip' value='1' checked> EXTRAORDINARIA <input type='RADIO' name='wtip' value='2'> FIRMANTES DEL ACUERDO  </td></tr>";
					else
						echo "<td bgcolor=#cccccc><input type='RADIO' name='wtip' value='0'> ORDINARIA <input type='RADIO' name='wtip' value='1'> EXTRAORDINARIA <input type='RADIO' name='wtip' value='2' checked> FIRMANTES DEL ACUERDO  </td></tr>";
			else
				echo "<td bgcolor=#cccccc><input type='RADIO' name='wtip' value='0' checked> ORDINARIA <input type='RADIO' name='wtip' value='1'> EXTRAORDINARIA <input type='RADIO' name='wtip' value='2'> FIRMANTES DEL ACUERDO </td></tr>";
			echo "<tr><td bgcolor=#cccccc>TIPO DE PARTICIPACION</td>";	
			if(isset($wpar))
				if($wpar == "0")
					echo "<td bgcolor=#cccccc><input type='RADIO' name='wpar' value='0' checked> ACCIONES <input type='RADIO' name='wpar' value='1'> % COPROPIEDAD </td></tr>";
				else
					echo "<td bgcolor=#cccccc><input type='RADIO' name='wpar' value='0'> ACCIONES <input type='RADIO' name='wpar' value='1' checked> % COPROPIEDAD </td></tr>";
			else
				echo "<td bgcolor=#cccccc><input type='RADIO' name='wpar' value='0' checked> ACCIONES <input type='RADIO' name='wpar' value='1'> % COPROPIEDAD </td></tr>";
			if(isset($ci))
				echo "<tr><td bgcolor=#cccccc align=center>Codigo Inicial</td><td bgcolor=#cccccc align=center><input type='TEXT' name='ci' size=12 maxlength=12 value='".$ci."' ></td></tr>";
			else
				echo "<tr><td bgcolor=#cccccc align=center>Codigo Inicial</td><td bgcolor=#cccccc align=center><input type='TEXT' name='ci' size=12 maxlength=12></td></tr>";
			if(isset($cf))
				echo "<tr><td bgcolor=#cccccc align=center>Codigo Final</td><td bgcolor=#cccccc align=center><input type='TEXT' name='cf' size=12 maxlength=12 value='".$cf."'></td></tr>";
			else
				echo "<tr><td bgcolor=#cccccc align=center>Codigo Final</td><td bgcolor=#cccccc align=center><input type='TEXT' name='cf' size=12 maxlength=12></td></tr>";
			
			// echo "<tr>";
			// echo "<td bgcolor=#cccccc>Pregunta</td>";			
			// echo "<td bgcolor=#cccccc>";
			// echo "<select name='wemp'>";
			// $query = "select Empcod, Empdes from ".$empresa."_000004 order by Empcod";
			// $err1 = mysql_query($query,$conex);
			// $num1 = mysql_num_rows($err1);
			// for ($i=0;$i<$num1;$i++)
			// {	
				// $row1 = mysql_fetch_array($err1);
				// if(isset($wemp) and $wemp == $row1[0]."-".$row1[1])
					// echo "<option selected>".$row1[0]."-".$row1[1]."</option>";
				// else
					// echo "<option>".$row1[0]."-".$row1[1]."</option>";
			// }
			// echo "</td></tr>";
			
			 // value='".$cf."'
			 // var_dump($wtipv);
			echo "<tr><td bgcolor=#cccccc>TIPO DE IMPRESION</td>";
			// // echo "<td bgcolor=#cccccc><input type='RADIO' name='wtipv' value='0' checked>OPCION<input type='RADIO' name='wtipv' value='1'>DE SI/NO<input type='RADIO' name='wtipv' value='2'>PLANCHA</td></tr>";
			if($wtipv=="1")
			{
				echo "<td bgcolor=#cccccc><input type='RADIO' name='wtipv' value='0'>OPCION<input type='RADIO' name='wtipv' value='1' checked>DE SI/NO<input type='RADIO' name='wtipv' value='2'>PLANCHA</td></tr>";
			}
			elseif($wtipv=="2")
			{
				echo "<td bgcolor=#cccccc><input type='RADIO' name='wtipv' value='0'>OPCION<input type='RADIO' name='wtipv' value='1'>DE SI/NO<input type='RADIO' name='wtipv' value='2' checked>PLANCHA</td></tr>";
			}
			else
			{
				echo "<td bgcolor=#cccccc><input type='RADIO' name='wtipv' value='0' checked>OPCION<input type='RADIO' name='wtipv' value='1'>DE SI/NO<input type='RADIO' name='wtipv' value='2'>PLANCHA</td></tr>";
			}
			
			
			
			echo "<tr><td bgcolor=#cccccc>ORDEN DE IMPRESION</td>";
			// echo "<td bgcolor=#cccccc><input type='RADIO' name='word' value='0' checked>ALFABETICO<input type='RADIO' name='word' value='1'>DE INSCRIPCION</td></tr>";
			if($word=="1")
			{
				echo "<td bgcolor=#cccccc><input type='RADIO' name='word' value='0'>ALFABETICO<input type='RADIO' name='word' value='1' checked>DE INSCRIPCION</td></tr>";
			}
			else
			{
				echo "<td bgcolor=#cccccc><input type='RADIO' name='word' value='0' checked>ALFABETICO<input type='RADIO' name='word' value='1'>DE INSCRIPCION</td></tr>";
			}
			
			
			echo "<tr><td bgcolor=#cccccc>TIPO DE ASAMBLEA</td>";
			// echo "<td bgcolor=#cccccc><input type='RADIO' name='wtipa' value='0' checked>GENERAL<input type='RADIO' name='wtipa' value='1'>ACUERDO DE ACCIONISTAS</td></tr>";
			if($wtipa=="1")
			{
				echo "<td bgcolor=#cccccc><input type='RADIO' name='wtipa' value='0'>GENERAL<input type='RADIO' name='wtipa' value='1' checked>ACUERDO DE ACCIONISTAS</td></tr>";
			}
			else
			{
				echo "<td bgcolor=#cccccc><input type='RADIO' name='wtipa' value='0' checked>GENERAL<input type='RADIO' name='wtipa' value='1'>ACUERDO DE ACCIONISTAS</td></tr>";
			}
			echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='submit' value='IR'></td></tr></table>";
		}
		else
		{
			switch ($wtip)
			{
				case "0":
					$wtit="ORDINARIA";
				break;
				case "1":
					$wtit="EXTRAORDINARIA";
				break;
				case "2":
					$wtit="DE ACCIONISTAS FIRMANTES DEL ACUERDO DE SOCIOS";
				break;
			}
			switch ($wpar)
			{
				case "0":
					$wdec=0;
					$wtit2="ACCIONES";
				break;
				case "1":
					$wdec=4;
					$wtit2="% COPROPIEDAD";
				break;
			}
			$query = "select Pardes from ".$empresa."_000002 where Paremp = '".substr($wemp,0,2)."' and Parcod = '".substr($wtiv,0,strpos($wtiv,"-"))."' order by Parcod";
			$err1 = mysql_query($query,$conex);
			$row1 = mysql_fetch_array($err1);
			// if($word == "0")
				// if($wtipa == "0")
					// $query = "SELECT Socced, CONCAT(Socap1,' ',Socap2,' ',Socnom), Soctac from socios_000001  where Socact='A' and Socced between '".$ci."' and '".$cf."' order by 2 ";
				// else
					// $query = "SELECT Socced, CONCAT(Socap1,' ',Socap2,' ',Socnom), Soctac from socios_000001  where Socact='A' and Socfir='S' and Socced between '".$ci."' and '".$cf."' order by 2 ";
			// else
				// if($wtipa == "0")
					// $query = "SELECT Socced, CONCAT(Socap1,' ',Socap2,' ',Socnom), Soctac from socios_000001  where Socact='A' and Socced between '".$ci."' and '".$cf."' order by id ";
				// else
					// $query = "SELECT Socced, CONCAT(Socap1,' ',Socap2,' ',Socnom), Soctac from socios_000001  where Socact='A' and Socfir='S' and Socced between '".$ci."' and '".$cf."' order by id ";
				
			$codigoEmpresa = substr($wemp,0,2);
			
			$order = "id";	
			if($word == "0")
			{
				$order = "2";	
			}
			$query = "SELECT Acccod,Accnom,Accva3 
						FROM ".$empresa."_000001 
					   WHERE Acccod BETWEEN '".$ci."' AND '".$cf."' 
						 AND Accact='on' 
						 AND Accemp='".$codigoEmpresa."' 
					ORDER BY ".$order.";";
			
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			$k=0;
			$soc=0;
			if($num > 0)
			{
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$k++;
					$soc++;
					echo "<center><table border=0>";
					echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					echo "<tr><td id='tipo1' colspan=3>".substr($wemp,strpos($wemp,"-")+1)."</td></tr>";
					echo "<tr><td id='tipo1' colspan=3>ASAMBLEA ".$wtit."</td></tr>";
					echo "<tr><td id='tipo1' colspan=3>".$wfec."</td></tr>";
					echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					echo "<tr><td id='tipo1'>SOCIO</td><td id='tipo1'>IDENTIFICACION</td><td id='tipo1'>".$wtit2."</td></tr>";
					echo "<tr><td id='tipo2'>".$row[1]."</td><td id='tipo2'>".$row[0]."</td><td id='tipo2'>".number_format((double)$row[2],$wdec,'.',',')."</td></tr>";
					//echo "<tr><td align=center colspan=3><font size=13 face='3 of 9 Barcode'>*".$row[0]."*</font></td></tr>";
					// echo "<tr><td id='tipo3' class='tipo3' colspan=3>*".$row[0]."*</td></tr>";
					$codigoBarras = "<img src='../../../include/root/clsGenerarCodigoBarras.php?width=250&height=115&barcode=".$row[0]."'>";		
					// $codigoBarras = "<img src='../clsGenerarCodigoBarras.php?width=250&height=115&barcode=".$row[0]."'>";		
					echo "<tr><td id='tipo3' class='tipo3' colspan=3>*".$codigoBarras."*</td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center colspan=3><b>&nbsp;</b></td></tr>";
					echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					//echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					//echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					if($wtipv == 0)
					{
						//echo "<tr><td align=left colspan=2><font size=5>".substr($wtiv,strpos($wtiv,"-")+1)."</font></td><td align=left colspan=2><font size=5>&nbsp&nbsp&nbsp&nbspOpcion Nro. [&nbsp&nbsp&nbsp&nbsp]</font></td></tr>";
						echo "<tr><td id='tipo4' colspan=2>".$row1[0]."</td><td id='tipo4' colspan=2>&nbsp;&nbsp;&nbsp;&nbsp;Opcion Nro. [&nbsp;&nbsp;&nbsp;&nbsp;]</td></tr>";
					}
					elseif($wtipv == 1)
						{
							//echo "<tr><td align=left colspan=2><font size=5>".substr($wtiv,strpos($wtiv,"-")+1)."</font></td><td align=left colspan=2><font size=5>&nbsp&nbsp&nbsp&nbspSI[&nbsp&nbsp]&nbsp&nbsp&nbsp&nbspNO[&nbsp&nbsp]&nbsp&nbsp&nbsp&nbspBLANCO[&nbsp&nbsp]</font></td></tr>";
							echo "<tr><td id='tipo4' colspan=2>".$row1[0]."</td><td id='tipo4' colspan=2>&nbsp;&nbsp;&nbsp;&nbsp;SI[&nbsp;&nbsp;]&nbsp;&nbsp;&nbsp;&nbsp;NO[&nbsp;&nbsp;]&nbsp;&nbsp;&nbsp;&nbsp;BLANCO[&nbsp;&nbsp;]</td></tr>";
						}
						else
						{
							//echo "<tr><td align=left colspan=2><font size=5>".substr($wtiv,strpos($wtiv,"-")+1)."</font></td><td align=left colspan=2><font size=5>&nbsp&nbsp&nbsp&nbspOpcion Nro. [&nbsp&nbsp&nbsp&nbsp]</font></td></tr>";
							echo "<tr><td id='tipo4' colspan=2>".$row1[0]."</td><td id='tipo4' colspan=2>&nbsp;&nbsp;&nbsp;&nbsp;Plancha Nro. [&nbsp;&nbsp;&nbsp;&nbsp;]</td></tr>";
						}
					echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					//echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					//echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					// echo "<tr><td id='tipo1' colspan=3><b>------------------------------------------------------------</b></td></tr>";
					// echo "<tr><td id='tipo1' colspan=3><b>FIRMA</b></td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center colspan=3><b>&nbsp;</b></td></tr></table>";
					if($k%2 == 0)
					{
						echo "</div>";
						echo "<div style='page-break-before: always'>";	
					}
					else
					{
						echo "<br>";
						//echo "<br><br><br>";
						// echo "----------------------------------------------------------------------------".$i."----------------------------------------------------------------------------<br><br><br><br>";
						echo "--------------------------------------------------------------------------------------------------------------------------------------------------------<br><br><br><br>";
					}
				}
				echo "<div style='page-break-before: always'>";
				echo "<br><br><br>Nro de Votos Impresos : ".$soc."<br>";
			}
		}
	}
?>
</body>
</html>
