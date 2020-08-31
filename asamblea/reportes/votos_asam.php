<html>
<head>
  <title>MATRIX Impresion de Votos Para Asamblea de Accionistas / Copropietarios 2009-12-09</title>
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
		echo "<form action='votos_asam.php' method=post>";
		

		

		echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wemp) or !isset($wtip) or !isset($wpar) or !isset($ci) or !isset($cf) or !isset($wtiv) or !isset($wfec))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>IMPRESION DE VOTOS ASAMBLEAS DE LAS AMERICAS Ver. 2009-03-09</b></td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc>Empresa</td>";			
			echo "<td bgcolor=#cccccc>";
			echo "<select name='wemp'>";
			$query = "select Empcod, Empdes from ".$empresa."_000004 order by Empcod";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			for ($i=0;$i<$num1;$i++)
			{	
				$row1 = mysql_fetch_array($err1);
				if(isset($wemp) and $wemp == $row1[0]."-".$row1[1])
					echo "<option selected>".$row1[0]."-".$row1[1]."</option>";
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
				echo "<tr><td bgcolor=#cccccc align=center>Fecha de la Asamblea</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wfec' size=10 maxlength=10></td></tr>";
			}
			
			echo "<tr><td bgcolor=#cccccc>TIPO DE ASAMBLEA</td>";			
			if(isset($wtip))
				if($wtip == "0")
					echo "<td bgcolor=#cccccc><input type='RADIO' name='wtip' value='0' checked> ORDINARIA <input type='RADIO' name='wtip' value='1'> EXTRAORDINARIA </td></tr>";
				else
					echo "<td bgcolor=#cccccc><input type='RADIO' name='wtip' value='0'> ORDINARIA <input type='RADIO' name='wtip' value='1' checked> EXTRAORDINARIA </td></tr>";
			else
				echo "<td bgcolor=#cccccc><input type='RADIO' name='wtip' value='0' checked> ORDINARIA <input type='RADIO' name='wtip' value='1'> EXTRAORDINARIA </td></tr>";
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
			echo "<tr><td bgcolor=#cccccc>TIPO DE IMPRESION</td>";
			echo "<td bgcolor=#cccccc><input type='RADIO' name='wtipv' value='0' checked>NORMAL<input type='RADIO' name='wtipv' value='1'>DE SI/NO</td></tr>";
			echo "<tr><td bgcolor=#cccccc>ORDEN DE IMPRESION</td>";
			echo "<td bgcolor=#cccccc><input type='RADIO' name='word' value='0' checked>ALFABETICO<input type='RADIO' name='word' value='1'>DE INSCRIPCION</td></tr>";
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
			if($word == "0")
				$query = "SELECT  Acccod, Accnom, Accvap from ".$empresa."_000001  where Accact='on' and Accemp='".substr($wemp,0,2)."' and Acccod between '".$ci."' and '".$cf."' order by Accnom";
			else
				$query = "SELECT  Acccod, Accnom, Accvap from ".$empresa."_000001  where Accact='on' and Accemp='".substr($wemp,0,2)."' and Acccod between '".$ci."' and '".$cf."' order by id";
			$err = mysql_query($query,$conex);
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
					echo "<tr><td align=center colspan=3><b>".substr($wemp,strpos($wemp,"-")+1)."</b></td></tr>";
					echo "<tr><td align=center colspan=3><b>ASAMBLEA ".$wtit."</b></td></tr>";
					echo "<tr><td align=center colspan=3><b>".$wfec."</b></td></tr>";
					echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					echo "<tr><td align=center><b>SOCIO</b></td><td align=center><b>IDENTIFICACION</b></td><td align=center><b>".$wtit2."</b></td></tr>";
					echo "<tr><td align=center bgcolor=#dddddd>".$row[1]."</td><td align=center bgcolor=#dddddd>".$row[0]."</td><td align=center bgcolor=#dddddd>".number_format((double)$row[2],$wdec,'.',',')."</td></tr>";
					echo "<tr><td align=center colspan=3><font size=13 face='3 of 9 barcode'>*".$row[0]."*</font></td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center colspan=3><b>&nbsp</b></td></tr>";
					echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					if($wtipv == 0)
					{
						//echo "<tr><td align=left colspan=2><font size=5>".substr($wtiv,strpos($wtiv,"-")+1)."</font></td><td align=left colspan=2><font size=5>&nbsp&nbsp&nbsp&nbspOpcion Nro. [&nbsp&nbsp&nbsp&nbsp]</font></td></tr>";
						echo "<tr><td align=left colspan=2><font size=5>".$row1[0]."</font></td><td align=left colspan=2><font size=5>&nbsp&nbsp&nbsp&nbspOpcion Nro. [&nbsp&nbsp&nbsp&nbsp]</font></td></tr>";
					}
					else
					{
						//echo "<tr><td align=left colspan=2><font size=5>".substr($wtiv,strpos($wtiv,"-")+1)."</font></td><td align=left colspan=2><font size=5>&nbsp&nbsp&nbsp&nbspSI[&nbsp&nbsp]&nbsp&nbsp&nbsp&nbspNO[&nbsp&nbsp]&nbsp&nbsp&nbsp&nbspBLANCO[&nbsp&nbsp]</font></td></tr>";
						echo "<tr><td align=left colspan=2><font size=5>".$row1[0]."</font></td><td align=left colspan=2><font size=5>&nbsp&nbsp&nbsp&nbspSI[&nbsp&nbsp]&nbsp&nbsp&nbsp&nbspNO[&nbsp&nbsp]&nbsp&nbsp&nbsp&nbspBLANCO[&nbsp&nbsp]</font></td></tr>";
					}
					echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					echo "<tr><td align=center colspan=3><b>&nbsp</b></td></tr>";
					echo "<tr><td align=center colspan=3><b>------------------------------------------------------------</b></td></tr>";
					echo "<tr><td align=center colspan=3><b>FIRMA</b></td></tr>";
					echo "<tr><td bgcolor=#cccccc align=center colspan=3><b>&nbsp</b></td></tr></table>";
					if($k%2 == 0)
					{
						echo "</div>";
						echo "<div style='page-break-before: always'>";	
					}
					else
					{
						echo "<br><br><br>";
						echo "----------------------------------------------------------------------------------------------------------------------------------------------------------<br><br><br><br>";
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