<html>
<head>
<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form name='analisis' action='000001_rc190.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		echo "<center><input type='HIDDEN' name= 'call' value='".$call."'>";
		if(!isset($wccoi) or !isset($wemp) or $wemp == "Seleccione" or !isset($wccof) or !isset($wgru) or !isset($wanoi) or !isset($wperi1)  or !isset($wperi2) or $wperi1 < 1 or $wperi1 > 12 or $wperi2 < 1 or $wperi2 > 12 or $wperi1 > $wperi2 or !isset($wanof) or !isset($wperf1)  or !isset($wperf2) or $wperf1 < 1 or $wperf1 > 12 or $wperf2 < 1 or $wperf2 > 12 or $wperf1 > $wperf2)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>ANALISIS DE UNIDADES Ver. 2017-11-08</td></tr>";
			echo "<tr><td bgcolor=#999999 align=center colspan=2>DATOS INICIALES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o</td>";
			if(isset($wanoi))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanoi' value='".$wanoi."' size=4 maxlength=4></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanoi' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial</td>";
			if(isset($wperi1))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperi1' value='".$wperi1."' size=2 maxlength=2></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperi1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final</td>";
			if(isset($wperi2))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperi2' value='".$wperi2."' size=2 maxlength=2></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperi2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#999999 align=center colspan=2>DATOS FINALES</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o</td>";
			if(isset($wanof))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanof' value='".$wanof."' size=4 maxlength=4></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanof' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Inicial</td>";
			if(isset($wperf1))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperf1' value='".$wperf1."' size=2 maxlength=2></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperf1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes Final</td>";
			if(isset($wperf2))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperf2' value='".$wperf2."' size=2 maxlength=2></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wperf2' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#999999 align=center colspan=2>CENTROS DE COSTOS</td></tr>";
			if($key=="costosyp" or (isset($call) and $call == "SIG") or (isset($call) and $call == "SIC"))
			{
				echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Inicial</td>";
				if(isset($wccoi))
					echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccoi' value='".$wccoi."' size=4 maxlength=4></td></tr>";
				else
					echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccoi' size=4 maxlength=4></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Final</td>";
				if(isset($wccof))
					echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccof' value='".$wccof."' size=4 maxlength=4></td></tr>";
				else
					echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wccof' size=4 maxlength=4></td></tr>";
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
			}
			else
			{
				echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Inicial</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000005 where empleado ='".$key."'  and cc=ccocod group by 1 order by Cc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wccoi'>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."-".$row[1]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center>Centro de Costos Final</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Cc,cconom  from ".$empresa."_000125,".$empresa."_000005 where empleado ='".$key."'  and cc=ccocod group by 1 order by Cc";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wccof'>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."-".$row[1]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center>Empresa de  Proceso</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Empcod,Empdes  from ".$empresa."_000153,".$empresa."_000125 where empleado = '".$key."' and empresa = Empcod group by 1 order by Empcod";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wemp' OnChange='enter()'>";
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
			}
			echo "<tr><td bgcolor=#cccccc align=center>Grupo de Unidades</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT ccouni  from ".$empresa."_000005 group by 1 order by 1";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wgru'>";
				echo "<option>Todos</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."</option>";
				}
				echo "</select>";
			}
			echo "</td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr>";
			echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='checkbox' name='TEC1'>Ingresos<input type='checkbox' name='TEC2'>Resultados<input type='checkbox' name='TEC3'>Indicadores</td></tr></table>";
		}
		else
		{
			$wempw = substr($wemp,0,2);
			$wrango = "(";
			if(isset($call) and $call == "SIF")
			{
				$query = "SELECT Cc  from ".$empresa."_000125 ";
				$query = $query."  where empleado = '".$key."'";
				$query = $query."    and empresa = '".$wempw."'";
				$query = $query."    and Cc between '".substr($wccoi,0,4)."' and '".substr($wccof,0,4)."'";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if($i == 0)
							$wrango .= $row[0];
						else
							$wrango .= ",".$row[0];
					}
				}
			}
			$wrango .= ")";
			echo "  <frameset rows='60,*'>";	
				echo "    <frame src='000001_rc191.php?empresa=".$empresa."&wanoi=".$wanoi."&wperi1=".$wperi1."&wperi2=".$wperi2."&wanof=".$wanof."&wperf1=".$wperf1."&wperf2=".$wperf2."&wccoi=".$wccoi."&wccof=".$wccof."&wgru=".$wgru."&wemp=".$wemp."&call=".$call."&wrango=".$wrango."' name='Seleccion'>";
			if(isset($TEC1) and !isset($TEC2) and !isset($TEC3))
			{
				echo "<frameset>";
				echo "    <frame src='000001_rc189.php?empresa=".$empresa."&wanoi=".$wanoi."&wperi1=".$wperi1."&wperi2=".$wperi2."&wanof=".$wanof."&wperf1=".$wperf1."&wperf2=".$wperf2."&wccoi=".$wccoi."&wccof=".$wccof."&wgru=".$wgru."&wemp=".$wemp."&call=".$call."&wrango=".$wrango."' name='Ingresos' marginwidth=0 marginheiht=0>";
				echo "</frameset>";
			}
			elseif(isset($TEC1) and isset($TEC2) and !isset($TEC3))
				{
					echo "<frameset cols='50%,*'>";
					echo "    <frame src='000001_rc189.php?empresa=".$empresa."&wanoi=".$wanoi."&wperi1=".$wperi1."&wperi2=".$wperi2."&wanof=".$wanof."&wperf1=".$wperf1."&wperf2=".$wperf2."&wccoi=".$wccoi."&wccof=".$wccof."&wgru=".$wgru."&wemp=".$wemp."&call=".$call."&wrango=".$wrango."' name='Ingresos' marginwidth=0 marginheiht=0>";
					echo "    <frame src='000001_rc192.php?empresa=".$empresa."&wanoi=".$wanoi."&wperi1=".$wperi1."&wperi2=".$wperi2."&wanof=".$wanof."&wperf1=".$wperf1."&wperf2=".$wperf2."&wccoi=".$wccoi."&wccof=".$wccof."&wgru=".$wgru."&wemp=".$wemp."&call=".$call."&wrango=".$wrango."' name='Resultados' marginwidth=0 marginheiht=0>";
					echo "</frameset>";
				}
				elseif(isset($TEC1) and !isset($TEC2) and isset($TEC3))
					{
						echo "<frameset cols='50%, 50%'>";
						echo "    <frame src='000001_rc189.php?empresa=".$empresa."&wanoi=".$wanoi."&wperi1=".$wperi1."&wperi2=".$wperi2."&wanof=".$wanof."&wperf1=".$wperf1."&wperf2=".$wperf2."&wccoi=".$wccoi."&wccof=".$wccof."&wgru=".$wgru."&wemp=".$wemp."&call=".$call."&wrango=".$wrango."' name='Ingresos' marginwidth=0 marginheiht=0>";
						echo "    <frame src='000001_rc193.php?empresa=".$empresa."&wanoi=".$wanoi."&wperi1=".$wperi1."&wperi2=".$wperi2."&wanof=".$wanof."&wperf1=".$wperf1."&wperf2=".$wperf2."&wccoi=".$wccoi."&wccof=".$wccof."&wgru=".$wgru."&wemp=".$wemp."&call=".$call."&wrango=".$wrango."' name='Indicadores' marginwidth=0 marginheiht=0>";
						echo "</frameset>";
					}
					elseif(isset($TEC1) and isset($TEC2) and isset($TEC3))
						{
							echo "<frameset cols='34%, 33%, 33%'>";
							echo "    <frame src='000001_rc189.php?empresa=".$empresa."&wanoi=".$wanoi."&wperi1=".$wperi1."&wperi2=".$wperi2."&wanof=".$wanof."&wperf1=".$wperf1."&wperf2=".$wperf2."&wccoi=".$wccoi."&wccof=".$wccof."&wgru=".$wgru."&wemp=".$wemp."&call=".$call."&wrango=".$wrango."' name='Ingresos' marginwidth=0 marginheiht=0>";
							echo "    <frame src='000001_rc192.php?empresa=".$empresa."&wanoi=".$wanoi."&wperi1=".$wperi1."&wperi2=".$wperi2."&wanof=".$wanof."&wperf1=".$wperf1."&wperf2=".$wperf2."&wccoi=".$wccoi."&wccof=".$wccof."&wgru=".$wgru."&wemp=".$wemp."&call=".$call."&wrango=".$wrango."' name='Resultados' marginwidth=0 marginheiht=0>";
							echo "    <frame src='000001_rc193.php?empresa=".$empresa."&wanoi=".$wanoi."&wperi1=".$wperi1."&wperi2=".$wperi2."&wanof=".$wanof."&wperf1=".$wperf1."&wperf2=".$wperf2."&wccoi=".$wccoi."&wccof=".$wccof."&wgru=".$wgru."&wemp=".$wemp."&call=".$call."&wrango=".$wrango."' name='Indicadores' marginwidth=0 marginheiht=0>";
							echo "</frameset>";
						}
						elseif(!isset($TEC1) and isset($TEC2) and !isset($TEC3))
							{
								echo "<frameset>";
								echo "    <frame src='000001_rc192.php?empresa=".$empresa."&wanoi=".$wanoi."&wperi1=".$wperi1."&wperi2=".$wperi2."&wanof=".$wanof."&wperf1=".$wperf1."&wperf2=".$wperf2."&wccoi=".$wccoi."&wccof=".$wccof."&wgru=".$wgru."&wemp=".$wemp."&call=".$call."&wrango=".$wrango."' name='Resultados' marginwidth=0 marginheiht=0>";
								echo "</frameset>";
							}
							elseif(!isset($TEC1) and isset($TEC2) and isset($TEC3))
								{
									echo "<frameset cols='50%, 50%'>";
									echo "    <frame src='000001_rc192.php?empresa=".$empresa."&wanoi=".$wanoi."&wperi1=".$wperi1."&wperi2=".$wperi2."&wanof=".$wanof."&wperf1=".$wperf1."&wperf2=".$wperf2."&wccoi=".$wccoi."&wccof=".$wccof."&wgru=".$wgru."&wemp=".$wemp."&call=".$call."&wrango=".$wrango."' name='Resultados' marginwidth=0 marginheiht=0>";
									echo "    <frame src='000001_rc193.php?empresa=".$empresa."&wanoi=".$wanoi."&wperi1=".$wperi1."&wperi2=".$wperi2."&wanof=".$wanof."&wperf1=".$wperf1."&wperf2=".$wperf2."&wccoi=".$wccoi."&wccof=".$wccof."&wgru=".$wgru."&wemp=".$wemp."&call=".$call."&wrango=".$wrango."' name='Indicadores' marginwidth=0 marginheiht=0>";
									echo "</frameset>";
								}
								elseif(!isset($TEC1) and !isset($TEC2) and isset($TEC3))
									{
										echo "<frameset>";
										echo "    <frame src='000001_rc193.php?empresa=".$empresa."&wanoi=".$wanoi."&wperi1=".$wperi1."&wperi2=".$wperi2."&wanof=".$wanof."&wperf1=".$wperf1."&wperf2=".$wperf2."&wccoi=".$wccoi."&wccof=".$wccof."&wgru=".$wgru."&wemp=".$wemp."&call=".$call."&wrango=".$wrango."' name='Indicadores' marginwidth=0 marginheiht=0>";
										echo "</frameset>";
									}
			echo "</frameset>";
		}
	}		
?>
</body>
</html>


