<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<script type="text/javascript">
<!--
	function ejecutar(path)
	{
		window.open(path,'','width=1024,height=500,status=0,menubar=0,scrollbars=1,toolbar=0,directories=0,resizable=0');
	}
//-->
</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Protocolo de Un  Procedimiento Valorado Para Un A&ntilde;o Mes (CP)</font></a></td></tr>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> 000001_rc183.php Ver. 2017-08-17</b></font></td></tr></table>
</center>

<?php
include_once("conex.php");
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
		

		

		echo "<form action='000001_rc183.php' method=post>";
		echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wanop) or !isset($wemp) or $wemp == "Seleccione" or !isset($wper1) or !isset($wserv) or (strtoupper ($wserv) != "S" and strtoupper ($wserv) != "N")  or !isset($wcco1) or !isset($wgru) or !isset($wcon) or !isset($wpro) or $wper1 < 1 or $wper1 > 12)
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td align=center colspan=2>PROTOCOLO DE UN  PROCEDIMIENTO VALORADO PARA UN A&Ntilde;O MES (CP)</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>A&ntilde;o de Proceso</td>";
			if(isset($wanop))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' value=".$wanop." size=4 maxlength=4></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wanop' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Mes de Proceso</td>";
			if(isset($wper1))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' value=".$wper1." size=2 maxlength=2></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wper1' size=2 maxlength=2></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Unidad  de Proceso</td>";
			if(isset($wcco1))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' value=".$wcco1." size=4 maxlength=4></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wcco1' size=4 maxlength=4></td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center>Protocolo a Analizar</td>";
			if(isset($wpro))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpro' value=".$wpro." size=10 maxlength=10></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wpro' size=10 maxlength=10></td></tr>";
			if(isset($wcco1) and isset($wpro))
			{
				echo "<tr><td bgcolor=#cccccc align=center>Grupo</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Mprgru from ".$empresa."_000095 ";
			    $query = $query." where mprcco = '".$wcco1."'";
			    $query = $query."   and mprpro = '".$wpro."'";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wgru'>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if(isset($wgru) and $wgru == $row[0])
							echo "<option selected>".$row[0]."</option>";
						else
							echo "<option>".$row[0]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
			}
			if(isset($wcco1) and isset($wpro))
			{
				echo "<tr><td bgcolor=#cccccc align=center>Concepto</td><td bgcolor=#cccccc align=center>";
				$query = "SELECT Mprcon from ".$empresa."_000095 ";
			    $query = $query." where mprcco = '".$wcco1."'";
			    $query = $query."   and mprpro = '".$wpro."'";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					echo "<select name='wcon'>";
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if(isset($wcon) and $wcon == $row[0])
							echo "<option selected>".$row[0]."</option>";
						else
							echo "<option>".$row[0]."</option>";
					}
					echo "</select>";
				}
				echo "</td></tr>";
			}
			echo "<tr><td bgcolor=#cccccc align=center>Valorado ? (S/N)</td>";
			if(isset($wserv))
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wserv' value=".$wserv." size=1 maxlength=1></td></tr>";
			else
				echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wserv' size=1 maxlength=1></td></tr>";
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
			$wpro=strtoupper ($wpro);
			$wserv=strtoupper ($wserv);
			$query = "SELECT mprnom,mprpro,mprnot,mprpor from ".$empresa."_000095 ";
			$query = $query." where Mpremp = '".$wemp."' ";
		    $query = $query."   and mprcco = '".$wcco1."'";
		    $query = $query."   and mprpro = '".$wpro."'";
		    $query = $query."   and Mprgru = '".$wgru."'";
		    $query = $query."   and Mprcon = '".$wcon."'";
		    //echo $query."<br>";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num > 0)
			{
				$wtotg=0;
				$row = mysql_fetch_array($err);
				echo "<table border=1>";
				echo "<tr><td colspan=6 align=center>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
				echo "<tr><td colspan=6 align=center>DIRECCION DE INFORMATICA</td></tr>";
				echo "<tr><td colspan=6 align=center>PROTOCOLO DE UN  PROCEDIMIENTO VALORADO PARA UN A&Ntilde;O MES (CP)</td></tr>";
				echo "<tr><td colspan=6 align=center>EMPRESA : ".$wempt."</td></tr>";
				echo "<tr><td colspan=6 align=center>PERIODO  : ".$wper1. " A&Ntilde;O : ".$wanop."</td></tr>";
				echo "<tr><td colspan=6 align=center>UNIDAD  : ".$wcco1."</td></tr>";
				echo "<tr><td colspan=6 align=center>PROTOCOLO  : ".$row[1]. " - ".$row[0]."</td></tr>";
				echo "<tr><td colspan=6 align=center>GRUPO  : ".$wgru."</td></tr>";
				echo "<tr><td colspan=6 align=center>CONCEPTO  : ".$wcon."</td></tr>";
				$query = "SELECT procod,subdes,procan,cxppro,proccp from ".$empresa."_000100,".$empresa."_000154,".$empresa."_000104 ";
				$query = $query." where Proemp = '".$wemp."' ";
			    $query = $query."   and procco = '".$wcco1."'";
			    $query = $query."   and propro = '".$wpro."'";
			    $query = $query."   and Progru = '".$wgru."'";
			    $query = $query."   and Procon = '".$wcon."'";
			    $query = $query."   and protip = '1' ";
			    $query = $query."   and procod = cxpsub ";
			    $query = $query."   and Proemp = cxpemp ";
			    $query = $query."   and cxpano = ".$wanop;
			    $query = $query."   and cxpmes = ".$wper1;
			    $query = $query."   and cxpcco = proccp";
			    $query = $query."   and subcod = cxpsub ";	
	    		$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$wsub=0;
				if($num1 > 0)
				{
					if($wserv == "S")
						echo "<tr><td bgcolor=#cccccc ><b>CODIGO</b></td><td  bgcolor=#cccccc ><b>DESCRIPCION</b></td><td  bgcolor=#cccccc ><b>CC</b></td><td align=right bgcolor=#cccccc ><b>CANTIDAD</b></td><td align=right bgcolor=#cccccc ><b>VLR. UNITARIO</b></td><td align=right bgcolor=#cccccc ><b>TOTAL</b></td></tr>";
					else
						echo "<tr><td bgcolor=#cccccc ><b>CODIGO</b></td><td  bgcolor=#cccccc ><b>DESCRIPCION</b></td><td  bgcolor=#cccccc ><b>CC</b></td><td align=right bgcolor=#cccccc ><b>CANTIDAD</b></td></tr>";
					for ($i=0;$i<$num1;$i++)
					{
						$row1 = mysql_fetch_array($err1);
						$wtot=$row1[2] * $row1[3];
						$wsub+=$wtot;
						$path="/matrix/presupuestos/reportes/000001_rc87.php?wanop=".$wanop."&wper1=1"."&wper2=".$wper1."&wcco1=".$row1[4]."&wcco2=".$row1[4]."&empresa=".$empresa."&wemp=".$wempt;
						if($wserv == "S")
							echo "<tr><td>".$row1[0]."</td><td>".$row1[1]."</td><td>".$row1[4]."</td><td align=right>".number_format((double)$row1[2],2,'.',',')."</td><td align=right>".number_format((double)$row1[3],0,'.',',')."</td><td align=right onclick='ejecutar(".chr(34).$path.chr(34).")'>".number_format((double)$wtot,0,'.',',')."</td></tr>";
						else
							echo "<tr><td>".$row1[0]."</td><td>".$row1[1]."</td><td>".$row1[4]."</td><td align=right>".number_format((double)$row1[2],2,'.',',')."</td></tr>";
					}
					$wtotg+=$wsub;
					if($wserv == "S")
						echo "<tr><td colspan=5 bgcolor=#99CCFF ><b>SUBTOTAL COSTOS X ACTIVIDAD</b></td><td align=right bgcolor=#99CCFF ><b>".number_format((double)$wsub,0,'.',',')."</b></td></tr>";
				}
				$query = "SELECT inscod,insdes,insuni,procan,minpro from ".$empresa."_000100,".$empresa."_000093,".$empresa."_000089 ";
				$query = $query." where Proemp = '".$wemp."' ";
			    $query = $query."   and procco = '".$wcco1."'";
			    $query = $query."   and propro = '".$wpro."'";
			    $query = $query."   and Progru = '".$wgru."'";
			    $query = $query."   and Procon = '".$wcon."'";
			    $query = $query."   and protip = '2' ";
			    $query = $query."   and procod = mincod ";
			    $query = $query."   and Proemp = minemp ";
			    $query = $query."   and minano = ".$wanop;
			    $query = $query."   and minmes = ".$wper1;
			    $query = $query."   and mincod = inscod ";
			    $query = $query."   and minemp = insemp ";
			    $query = $query." UNION ALL ";
				$query = $query." SELECT inscod,insdes,insuni,procan,0 from ".$empresa."_000100,".$empresa."_000089 ";
				$query = $query."  where Proemp = '".$wemp."' ";
				$query = $query."    and procco = '".$wcco1."'";
				$query = $query."    and propro = '".$wpro."'";
				$query = $query."    and Progru = '".$wgru."'";
				$query = $query."    and Procon = '".$wcon."'";
				$query = $query."    and protip = '2' "; 
				$query = $query."    and procod  NOT IN ( ";
				$query = $query." SELECT inscod from ".$empresa."_000100,".$empresa."_000093,".$empresa."_000089 ";
				$query = $query." where Proemp = '".$wemp."' ";
			    $query = $query."   and procco = '".$wcco1."'";
			    $query = $query."   and propro = '".$wpro."'";
			    $query = $query."   and Progru = '".$wgru."'";
			    $query = $query."   and Procon = '".$wcon."'";
			    $query = $query."   and protip = '2' ";
			    $query = $query."   and procod = mincod ";
			    $query = $query."   and Proemp = minemp ";
			    $query = $query."   and minano = ".$wanop;
			    $query = $query."   and minmes = ".$wper1;
			    $query = $query."   and mincod = inscod ";
			    $query = $query."   and minemp = insemp) ";
				$query = $query."   and procod = inscod ";	
				$query = $query."   and Proemp = insemp ";
	    		$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num1 = mysql_num_rows($err1);
				$wsub=0;
				if($num1 > 0)
				{
					if($wserv == "S")
						echo "<tr><td bgcolor=#cccccc ><b>CODIGO</b></td><td bgcolor=#cccccc ><b>DESCRIPCION</b></td><td bgcolor=#cccccc ><b>UNIDAD</b></td><td align=right bgcolor=#cccccc ><b>CANTIDAD</b></td><td align=right bgcolor=#cccccc ><b>VLR. UNITARIO</b></td><td align=right bgcolor=#cccccc ><b>TOTAL</b></td></tr>";
					else
						echo "<tr><td bgcolor=#cccccc ><b>CODIGO</b></td><td bgcolor=#cccccc ><b>DESCRIPCION</b></td><td bgcolor=#cccccc ><b>UNIDAD</b></td><td align=right bgcolor=#cccccc ><b>CANTIDAD</b></td></tr>";
					for ($i=0;$i<$num1;$i++)
					{
						$row1 = mysql_fetch_array($err1);
						$wtot=$row1[3] * $row1[4];
						$wsub+=$wtot;
						if($wserv == "S")
							echo "<tr><td>".$row1[0]."</td><td>".$row1[1]."</td><td>".$row1[2]."</td><td align=right>".number_format((double)$row1[3],4,'.',',')."</td><td align=right>".number_format((double)$row1[4],0,'.',',')."</td><td align=right>".number_format((double)$wtot,0,'.',',')."</td></tr>";
						else
							echo "<tr><td>".$row1[0]."</td><td>".$row1[1]."</td><td>".$row1[2]."</td><td align=right>".number_format((double)$row1[3],4,'.',',')."</td></tr>";
					}
					$wtotg+=$wsub;
					if($wserv == "S")
						echo "<tr><td colspan=5 bgcolor=#99CCFF ><b>SUBTOTAL COSTOS INSUMOS</b></td><td align=right bgcolor=#99CCFF ><b>".number_format((double)$wsub,0,'.',',')."</b></td></tr>";
				}
				$query = "SELECT procod,fijdes,procan,fijmon from ".$empresa."_000100,".$empresa."_000086 ";
				$query = $query." where Proemp = '".$wemp."' ";
			    $query = $query."   and procco = '".$wcco1."'";
			    $query = $query."   and propro = '".$wpro."'";
			    $query = $query."   and Progru = '".$wgru."'";
			    $query = $query."   and Procon = '".$wcon."'";
			    $query = $query."   and protip = '4' ";
			    $query = $query."   and Proemp = fijemp ";
			    $query = $query."   and proccp = fijcco ";
			    $query = $query."   and procod = fijcod ";	
			    $query = $query."   and Fijano = ".$wanop;
	    		$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$wsub=0;
				if($num1 > 0)
				{
					if($wserv == "S")
						echo "<tr><td bgcolor=#cccccc ><b>CODIGO</b></td><td bgcolor=#cccccc ><b>DESCRIPCION</b></td><td bgcolor=#cccccc ><b>UNIDAD</b></td><td align=right bgcolor=#cccccc ><b>CANTIDAD</b></td><td align=right bgcolor=#cccccc ><b>VLR. UNITARIO</b></td><td align=right bgcolor=#cccccc ><b>TOTAL</b></td></tr>";
					else
						echo "<tr><td bgcolor=#cccccc ><b>CODIGO</b></td><td bgcolor=#cccccc ><b>DESCRIPCION</b></td><td bgcolor=#cccccc ><b>UNIDAD</b></td><td align=right bgcolor=#cccccc ><b>CANTIDAD</b></td></tr>";
					for ($i=0;$i<$num1;$i++)
					{
						$row1 = mysql_fetch_array($err1);
						$wtot=$row1[2] * $row1[3];
						$wsub+=$wtot;
						if($wserv == "S")
							echo "<tr><td>".$row1[0]."</td><td colspan=2>".$row1[1]."</td><td align=right>".number_format((double)$row1[2],4,'.',',')."</td><td align=right>".number_format((double)$row1[3],0,'.',',')."</td><td align=right>".number_format((double)$wtot,0,'.',',')."</td></tr>";
						else
							echo "<tr><td>".$row1[0]."</td><td colspan=2>".$row1[1]."</td><td align=right>".number_format((double)$row1[2],4,'.',',')."</td></tr>";
					}
					$wtotg+=$wsub;
					if($wserv == "S")
						echo "<tr><td colspan=5 bgcolor=#99CCFF ><b>SUBTOTAL COSTOS PROCEDIMIENTOS DE TERCEROS</b></td><td align=right bgcolor=#99CCFF ><b>".number_format((double)$wsub,0,'.',',')."</b></td></tr>";
				}
				$wprot=array();
				$wpent=-1;
				$query = "SELECT pcacod,mprnom,procan,pcactp from ".$empresa."_000100,".$empresa."_000097,".$empresa."_000095 ";
				$query = $query." where Proemp = '".$wemp."' ";
				$query = $query."   and procco = '".$wcco1."'";
				$query = $query."   and propro = '".$wpro."'";
				$query = $query."   and protip = '3' ";
				$query = $query."   and Progru = '".$wgru."'";
				$query = $query."   and Procon = '".$wcon."'";
				$query = $query."   and Proemp = pcaemp ";
				$query = $query."   and proccp = pcacco ";
				$query = $query."   and procod = pcacod ";
				$query = $query."   and Progrp = pcagru ";
				$query = $query."   and Procoa = pcacon ";
				$query = $query."   and pcaano = ".$wanop;
	    		$query = $query."   and pcames = ".$wper1;
	    		$query = $query."   and Proemp = mpremp ";
				$query = $query."   and proccp = mprcco ";
				$query = $query."   and procod = mprpro ";
				$query = $query."   and progrp = Mprgru ";
				$query = $query."   and Procoa = Mprcon ";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1 > 0)
				{
					for ($j=0;$j<$num1;$j++)
					{
						$row1 = mysql_fetch_array($err1);
						$wpent++;
						$wprot[$wpent][0]=$row1[0];
						$wprot[$wpent][1]=$row1[1];
						$wprot[$wpent][2]=$row1[2];
						$wprot[$wpent][3]=$row1[3];
					}
				}
				
				if($wpent != (-1))
				{
					if($wserv == "S")
						echo "<tr><td bgcolor=#cccccc ><b>CODIGO</b></td><td bgcolor=#cccccc ><b>DESCRIPCION</b></td><td bgcolor=#cccccc ><b>UNIDAD</b></td><td align=right bgcolor=#cccccc ><b>CANTIDAD</b></td><td align=right bgcolor=#cccccc ><b>VLR. UNITARIO</b></td><td align=right bgcolor=#cccccc ><b>TOTAL</b></td></tr>";
					else
						echo "<tr><td bgcolor=#cccccc ><b>CODIGO</b></td><td bgcolor=#cccccc ><b>DESCRIPCION</b></td><td bgcolor=#cccccc ><b>UNIDAD</b></td><td align=right bgcolor=#cccccc ><b>CANTIDAD</b></td></tr>";
					$wsub=0;
					for ($k=0;$k<=$wpent;$k++)
					{
						$row1 = mysql_fetch_array($err1);
						$wtot=$wprot[$k][2]* $wprot[$k][3];
						$wsub+=$wtot;
						if($wserv == "S")
							echo "<tr><td>".$wprot[$k][0]."</td><td colspan=2>".$wprot[$k][1]."</td><td align=right>".number_format((double)$wprot[$k][2],2,'.',',')."</td><td align=right>".number_format((double)$wprot[$k][3],0,'.',',')."</td><td align=right>".number_format((double)$wtot,0,'.',',')."</td></tr>";
						else
							echo "<tr><td>".$wprot[$k][0]."</td><td colspan=2>".$wprot[$k][1]."</td><td align=right>".number_format((double)$wprot[$k][2],2,'.',',')."</td></tr>";
					}
					$wtotg+=$wsub;
					if($wserv == "S")
						echo "<tr><td colspan=5 bgcolor=#99CCFF ><b>SUBTOTAL COSTOS PROTOCOLOS</b></td><td align=right bgcolor=#99CCFF ><b>".number_format((double)$wsub,0,'.',',')."</b></td></tr>";
				}
				if($wserv == "S")
				{
					echo "<tr><td colspan=5><b>COSTO TOTAL PROPIO</b></td><td align=right><b>".number_format((double)$wtotg,0,'.',',')."</b></td></tr>";
					$wpor=$row[3] * 100;
					echo "<tr><td colspan=5><b>PORCENTAJE TERCERO</b></td><td align=right><b>".number_format((double)$wpor,2,'.',',')."%</b></td></tr>";
					$wtotg=$wtotg/(1 - $row[3]);
					echo "<tr><td colspan=5 bgcolor=#FFCC66 ><b>TARIFA MINIMA DE NEGOCIACION</b></td><td align=right bgcolor=#FFCC66 ><b>".number_format((double)$wtotg,0,'.',',')."</b></td></tr>";
				}
				echo "<tr><td colspan=6 bgcolor=#99CCFF ><b>NOTA : </b></td></tr>";
				echo "<tr><td colspan=6><b>".$row[2]."</b></td></tr></TABLE>";
			}
		}
	}
?>
</body>
</html>
