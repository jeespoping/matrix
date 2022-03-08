<html>
<head>
<title>MATRIX</title>
<link type='text/css' href='../procesos/HCE2.css' rel='stylesheet'> 
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
<!--<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Informe de Pacientes Polimedicados 2014-08-05</font></a></tr></td>
</center>-->

<?php
/****************************************************************************
 ACTUALIZACIÓN
 --------------------------------------------------------------------------
 08/03/2022-Brigith Lagares : Se estandariza wemp_pmla  

*****************************************************************************/
include_once("conex.php");
include_once("root/comun.php");
$wactualiz = '2022-02-25';
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato1 = strtolower( $institucion->baseDeDatos );
encabezado("INFORME DE PACIENTES POLIMEDICADOS ",$wactualiz, $wbasedato1);

function pintar_grid($data,$struc)
{
	$wsgrid="";
	if($data != "0*")
	{
		$Gridseg=explode("*",$struc);
		$Gridtit=explode("|",$Gridseg[0]);
		$Gridtip=explode("|",$Gridseg[1]);
		$wsgrid .= "<table align=center border=1 class='tipoTABLEGRID'>";
		$wsgrid .= "<tr>";
		$wsgrid .= "<td id='tipoAL06GRID'><div class='nobreak'>ITEM</div></td>";
		for ($g=0;$g<count($Gridtit);$g++)
		{
			$wsgrid .= "<td id='tipoAL06GRID'><div class='nobreak'>".$Gridtit[$g]."</div></td>";
		}
		$wsgrid .= "</tr>";
		$Gdataseg=explode("*",$data);
		for ($g=1;$g<=$Gdataseg[0];$g++)
		{
			if($g % 2 == 0)
				$gridcolor="tipoAL02GRID1";
			else
				$gridcolor="tipoAL02GRID2";
			$Gdatadata=explode("|",$Gdataseg[$g]);
			$wsgrid .= "<tr>";
			$wsgrid .= "<td class='".$gridcolor."'><div class='nobreak'>".$g."</div></td>";
			for ($g1=0;$g1<count($Gdatadata);$g1++)
			{
				$unimed="";
				if(substr($Gridtip[$g1],0,1) == "N")
				{
					$Gridnum=explode("(",$Gridtip[$g1]);
					$unimed=substr($Gridnum[1],0,strlen($Gridnum[1])-1);
				}
				if(substr($Gridtip[$g1],0,1) == "S")
					if(strpos($Gdatadata[$g1],"-") !== false)
						$Gdatadata[$g1] = substr($Gdatadata[$g1],strpos($Gdatadata[$g1],"-")+1);
				$wsgrid .= "<td class=".$gridcolor.">".$Gdatadata[$g1]." ".$unimed."</td>";
			}
			$wsgrid .= "</tr>";
		}
		$wsgrid .= "</table>";
	}
	return $wsgrid;
}
 @session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
 { 
	$key = substr($user,2,strlen($user));
	

	

	echo "<form name='Infact' action='HCE_Ipolimed.php' method=post>";
	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'wdbmhos' value='".$wdbmhos."'>";
	echo "<input type='HIDDEN' name= 'wemp_pmla' value='".$wemp_pmla."'>";

	echo "<center><table border=0>";
	//echo "<tr><td colspan=9 id='tipo4'>PROMOTORA MEDICA LAS AMERICAS S.A.</td></tr>";
	//echo "<tr><td colspan=9 id='tipo4'>DIRECCION DE INFORMATICA</td></tr>";
	//echo "<tr><td colspan=9 id='tipo4'>INFORME DEPACIENTES POLIMEDICADOS</td></tr>";

	//                  0      1     2      3      4      5      6      7      8      9
	$query = "select movhis,moving,Pactid,Pacced,Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex from ".$wdbmhos."_000018,".$empresa."_000051,root_000037,root_000036 ";
	$query .= " where Ubiald = 'off'";
	$query .= "   and Ubihis = movhis";
	$query .= "   and Ubiing = moving";
	$query .= "   and movcon = 280";
	$query .= "   and movdat = 'CHECKED'";
	$query .= "   and Ubihis = Orihis";
	$query .= "   and Ubiing = Oriing";
	$query .= "   and Oriori = '".$wemp_pmla."' "; 
	$query .= "   and Oritid = Pactid";
	$query .= "   and Oriced = Pacced";
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
		echo "<td id='tipo3'>Edad</td>";
		echo "<td id='tipo3'>Sexo</td>";
		echo "<td id='tipo3'>Diagnostico Ingreso</td>";
		echo "<td id='tipo3'>Medicamentos</td>";
		echo "</tr>"; 
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
			$wdxi = "";
			$wmed = "";
			$query = "select movcon,movdat,Detfor from ".$empresa."_000051,".$empresa."_000002 ";
			$query .= " where movhis = '".$row[0]."'";
			$query .= "   and moving = '".$row[1]."'";
			$query .= "   and movcon in (156,254)";
			$query .= "   and movpro = Detpro ";
			$query .= "   and movcon = Detcon ";
			$err1 = mysql_query($query ,$conex);
			$num1 = mysql_num_rows($err1);
			if ($num > 0)
			{
				for ($j=0;$j<$num1;$j++)
				{
					$row1 = mysql_fetch_array($err1);
					if($row1[0] == 156)
						$wdxi = $row1[1];
					else
						$wmed = pintar_grid($row1[1],$row1[2]);
				}
			}
			
			$nom = $row[4]." ".$row[5]." ".$row[6]." ".$row[7];
			echo "<tr>";
			echo "<td id=".$tipo.">".$row[0]."</td>";
			echo "<td id=".$tipo.">".$row[1]."</td>";
			echo "<td id=".$tipo.">".$row[2]."</td>";
			echo "<td id=".$tipo.">".$row[3]."</td>";
			echo "<td id=".$tipo.">".$nom."</td>";
			$ann=(integer)substr($row[8],0,4)*360 +(integer)substr($row[8],5,2)*30 + (integer)substr($row[8],8,2);
			$aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
			$ann1=($aa - $ann)/360;
			$meses=(($aa - $ann) % 360)/30;
			if ($ann1<1)
			{
				$dias1=(($aa - $ann) % 360) % 30;
				$wedad=(string)(integer)$meses." Meses ".(string)$dias1." Dias";	
			}
			else
			{
				$dias1=(($aa - $ann) % 360) % 30;
				$wedad=(string)(integer)$ann1." A&ntilde;os ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
			}
			echo "<td id=".$tipo.">".$wedad."</td>";
			if($row[9] == "M")
				$wsex = "MASCULINO";
			else
				$wsex = "FEMENINO";
			echo "<td id=".$tipo.">".$wsex."</td>";
			echo "<td id=".$tipo.">".$wdxi."</td>";
			echo "<td id=".$tipo.">".$wmed."</td>";
			echo "</tr>"; 
		}
		echo "<tr>";
		echo "<td id='tipo3' colspan=8>TOTAL PACIENTES/ACTIVIDADES</td>";
		echo "<td id='tipo7'>".$num."</td>";
		echo "</tr>";

		echo "</table></enter>"; 
	}
	else
	{
		echo "<tr><td colspan=9 id='tipo3'>SIN REGISTROS</td></tr>";
		echo "</table></enter>"; 
	}
}
?>
</body>
</html>
