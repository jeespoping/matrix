<html>
<head>
<title>MATRIX</title>
	<style type="text/css">
		#tipo1{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:normal;text-align:left;}
		#tipo2{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:normal;text-align:left;}
		#tipo3{color:#000066;background:#CCCCCC;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
		#tipo4{color:#000066;background:#FFFFFF;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
		#tipo5{color:#000066;background:#C3D9FF;font-size:9pt;font-family:Arial;font-weight:normal;text-align:right;}
		#tipo6{color:#000066;background:#E8EEF7;font-size:9pt;font-family:Arial;font-weight:normal;text-align:right;}
		#tipo7{color:#000066;background:#CCCCCC;font-size:10pt;font-family:Arial;font-weight:bold;text-align:right;}
	</style>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
	<script type="text/javascript">
		function enter()
		{
			document.forms.Infact.submit();
		}
	</script>
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=2>Informe de Actividades de HCE 2017-10-30</font></a></tr></td>
</center>
<?php
include_once("conex.php");
 @session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 { 
	$key = substr($user,2,strlen($user));
	

	

	echo "<form name='Infact' action='HCE_Infact.php' method=post>";
	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'wdbmhos' value='".$wdbmhos."'>";
	echo "<input type='HIDDEN' name= 'origen' value='".$origen."'>";
	if(!isset($v0) or !isset($v1) or !isset($wesp) or !isset($wmed))
	{
		echo "<center><table border=0>";
		echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td colspan=2 align=center><b>INFORME DE ACTIVIDADES DE HCE</b></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Especialidad</td><td bgcolor=#cccccc align=center>";
		$query = "SELECT Espcod,Espnom from ".$wdbmhos."_000044 order by 2 ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($num>0)
		{
			echo "<select name='wesp' onChange='enter()'>";
			echo "<option>----</option>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if(isset($wesp) and $wesp == $row[0]."-".$row[1])
					echo "<option selected>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option>".$row[0]."-".$row[1]."</option>";
			}
			echo "</select>";
		} 
		echo "</td></tr>";
		if(isset($wesp))
		{
			echo "<tr><td bgcolor=#cccccc align=center>Medico Tratante</td><td bgcolor=#cccccc align=center>";
			$query = "SELECT Meduma, Medap1, Medap2, Medno1, Medno2 from ".$wdbmhos."_000048 where Medesp ='".substr($wesp,0,strpos($wesp,"-"))."'  and Medest='on' order by 2,3,4,5 ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<select name='wmed'>";
				echo "<option>*-Todos</option>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$nmed = $row[1]." ".$row[2]." ".$row[3]." ".$row[4];
					echo "<option>".$row[0]."-".$nmed."</option>";
				}
				echo "</select>";
			} 
		}
		echo "</td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial Inicial</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='v0' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Fecha Inicial Final</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='v1' size=10 maxlength=10></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		echo "<center><table border=0>";
		if($wmed != "*-Todos")
			$ncol = 7;
		else
			$ncol = 8;
		echo "<tr><td colspan=".$ncol." id='tipo4'>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
		echo "<tr><td colspan=".$ncol." id='tipo4'>DIRECCION DE INFORMATICA</td></tr>";
		echo "<tr><td colspan=".$ncol." id='tipo4'>INFORME DE ACTIVIDADES DE HCE</td></tr>";
		echo "<tr><td colspan=".$ncol." id='tipo4'>Medico : ".$wmed."</td></tr>";
		echo "<tr><td colspan=".$ncol." id='tipo4'>Desde : ".$v0." Hasta ".$v1."</td></tr>";
		if($wmed != "*-Todos")
		{
			//                                      0                         1                          2                  3                  4                  5                  6                  7                   8     9
			$query = "select ".$empresa."_000036.Firhis,".$empresa."_000036.Firing,".$empresa."_000036.Firusu,root_000036.Pactid,root_000036.Pacced,root_000036.Pacno1,root_000036.Pacno2,root_000036.Pacap1,root_000036.Pacap2,count(*) ";
		}
		else
		{
			//                                      0                         1                           2                  3                  4                  5                  6                  7                   8               9          10
			$query = "select ".$empresa."_000036.Firhis,".$empresa."_000036.Firing,".$empresa."_000036.Firusu,root_000036.Pactid,root_000036.Pacced,root_000036.Pacno1,root_000036.Pacno2,root_000036.Pacap1,root_000036.Pacap2,usuarios.Descripcion,count(*) ";
		}
		if($wmed != "*-Todos")
			$query .= "  from ".$empresa."_000036,root_000037,root_000036 ";
		else
			$query .= "  from ".$empresa."_000036,root_000037,root_000036,usuarios ";
		$query .= "  where ".$empresa."_000036.Fecha_data between '".$v0."' and '".$v1."' ";
		if($wmed != "*-Todos")
			$query .= "    and ".$empresa."_000036.firusu = '".substr($wmed,0,strpos($wmed,"-"))."' "; 
		$query .= "    and ".$empresa."_000036.firpro IN ('000051','000069','000085','000088','000278','000274','000280','000282','000277','000275','000283','000298','000300','000302','000304','000315','000317','000321') "; 
		if($wmed == "*-Todos")
			$query .= "    and ".$empresa."_000036.firrol = '".substr($wesp,0,strpos($wesp,"-"))."'";
		$query .= "    and ".$empresa."_000036.firhis = root_000037.orihis ";
		$query .= "    and root_000037.oriori  = '".$origen."' "; 
		$query .= "    and root_000037.Oritid  = root_000036.Pactid  "; 
		$query .= "    and root_000037.Oriced  = root_000036.Pacced "; 
		if($wmed == "*-Todos")
			$query .= "    and ".$empresa."_000036.firusu  = usuarios.codigo "; 
		$query .= "  group by 1,2,3 ";  
		$query .= "  order by 1,2,3  ";  
		//echo $query."<br>";
		$err = mysql_query($query ,$conex);
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			
			echo "<tr>";
			echo "<td id='tipo3'>Historia</td>";
			echo "<td id='tipo3'>Ingreso</td>";
			echo "<td id='tipo3'>Tipo<br>Identificacion</td>";
			echo "<td id='tipo3'>Identificacion</td>";
			echo "<td id='tipo3'>Paciente</td>";
			echo "<td id='tipo3'>Numero de<br> Actividades</td>";
			echo "<td id='tipo3'>Fecha<br> HCE Ingreso</td>";
			if($wmed == "*-Todos")
				echo "<td id='tipo3'>Profesional</td>";
			echo "</tr>"; 
			$tot=0;
			for ($i=0;$i<$num;$i++)
			{
				if($i % 2 == 0)
				{
					$tipo = "tipo1";
					$tipo = "tipo5";
				}
				else
				{
					$tipo = "tipo2";
					$tipo = "tipo6";
				}
				$row = mysql_fetch_array($err);
				$query = "select ".$empresa."_000036.Fecha_data ";
				$query .= "  from ".$empresa."_000036 ";
				$query .= "  where ".$empresa."_000036.Firpro = '000051' "; 
				$query .= "    and ".$empresa."_000036.Firhis = '".$row[0]."' ";
				$query .= "    and ".$empresa."_000036.Firing = '".$row[1]."' ";
				$query .= "    and ".$empresa."_000036.Firusu = '".substr($wmed,0,strpos($wmed,"-"))."' ";   
				$err1 = mysql_query($query ,$conex);
				$num1 = mysql_num_rows($err1);
				if ($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$wfecha = $row1[0];
				}
				else
				{
					$wfecha = "SIN DATO";
				}
				if($wmed != "*-Todos")
					$tot += $row[9];
				else
					$tot += $row[10];
				$nom = $row[5]." ".$row[6]." ".$row[7]." ".$row[8];
				echo "<tr>";
				echo "<td id=".$tipo.">".$row[0]."</td>";
				echo "<td id=".$tipo.">".$row[1]."</td>";
				echo "<td id=".$tipo.">".$row[3]."</td>";
				echo "<td id=".$tipo.">".$row[4]."</td>";
				echo "<td id=".$tipo.">".$nom."</td>";
				if($wmed != "*-Todos")
				{
					echo "<td id=".$tipo.">".$row[9]."</td>";
					echo "<td id=".$tipo.">".$wfecha."</td>";
				}
				else
				{
					echo "<td id=".$tipo.">".$row[10]."</td>";
					echo "<td id=".$tipo.">".$wfecha."</td>";
					echo "<td id=".$tipo.">".$row[9]."</td>";
				}
				echo "</tr>"; 
			}
			unset($wmed);
			unset($wesp);
			echo "<tr>";
			echo "<td id='tipo3' colspan=4>TOTAL PACIENTES/ACTIVIDADES</td>";
			echo "<td id='tipo3'>".$num."</td>";
			echo "<td id='tipo7'>".$tot."</td>";
			if($wmed == "*-Todos")
				echo "<td id='tipo7'></td>";
			else
				echo "<td id='tipo7' colspan=2></td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td id='tipo4' colspan=7><buttom onClick='enter()'>RETORNAR</buttom></td>";
			echo "</tr>";
			echo "</table></enter>"; 
		}
		else
		{
			echo "<tr><td colspan=7 id='tipo3'>SIN REGISTROS PARA ESTE MEDICO</td></tr>";
			echo "<tr>";
			echo "<td id='tipo4' colspan=7><buttom onClick='enter()'>RETORNAR</buttom></td>";
			echo "</tr>";
			echo "</table></enter>"; 
		}
	}
}
?>
</body>
</html>
