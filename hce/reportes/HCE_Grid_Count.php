<html>
<head>
  	<title>MATRIX Conteo de items incluidos en un Campo Grid</title>
	<link type='text/css' href='../procesos/HCE2.css' rel='stylesheet'> 
	<style type="text/css">
		#tipo0{vertical-align:top;text-align:center;}
	</style>
	
</head>
<body BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return false">
<BODY TEXT="#000066">
<?php

include_once("conex.php");
include_once("root/comun.php");

$wemp_pmla = $_REQUEST['wemp_pmla'];
$wactualiz = '2022-02-25';
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato1 = strtolower( $institucion->baseDeDatos );
encabezado("CONTEO DE ITEMS INCLUIDOS EN UN CAMPO GRID ",$wactualiz, $wbasedato1);

function count_grid($data,&$info)
{
	$wsgrid="";
	if($data != "0*")
	{
		$k = -1;
		$Gdataseg=explode("*",$data);
		for ($g=1;$g<=$Gdataseg[0];$g++)
		{
			$Gdatadata=explode("|",$Gdataseg[$g]);
			$k++;
			$info[$k][0] = strtolower(trim($Gdatadata[0]));
			$info[$k][1] = settype($Gdatadata[1], "integer");
		}
		// var_dump($info);
	}
}
function comparacion($vec1,$vec2)
{
	if($vec1[0] < $vec2[0])
		return -1;
	elseif ($vec1[0] > $vec2[0])
				return 1;
			else
				return 0;
}

/***************************************************************************************************************************  
	   PROGRAMA : HCE_Grid_Count.php
	   Fecha de Liberación : 2015-01-07
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2015-01-07
	   
	   OBJETIVO GENERAL : 
	   Este programa ofrece al usuario una interface gráfica que permite generar un reporte de conteos de items almacenados
	   en un campo tipo Grid
	   
	   REGISTRO DE MODIFICACIONES 
	   	.2015-01-07
	   		Release de Versión Beta. 
	   
***************************************************************************************************************************/
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='HCE_Grid_Count' action='HCE_Grid_Count.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='HIDDEN' name= 'wdbmhos' value='".$wdbmhos."'>";
	echo "<input type='HIDDEN' name= 'wformulario' value='".$wformulario."'>";
	echo "<input type='HIDDEN' name= 'wdbfac' value='".$wdbfac."'>";
	if(!isset($whis) or !isset($wing))
	{
		echo  "<center><table border=0>";
		//echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		//echo "<tr><td colspan=2 align=center><b>CONTEO DE ITEMS INCLUIDOS EN UN CAMPO GRID</b></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Historia</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='whis' size=12 maxlength=12></td></tr>";
		echo "<tr><td bgcolor=#cccccc align=center>Nro. Ingreso</td>";
		echo "<td bgcolor=#cccccc align=center><input type='TEXT' name='wing' size=12 maxlength=12></td></tr>";
		echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	}
	else
	{
		$query = "select Oriced, Oritid from root_000037 ";
		$query .= " where Orihis = '".$whis."' ";
		$query .= "   and Oriori = '".$wemp_pmla."' ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row = mysql_fetch_array($err);
		$wcedula = $row[0];
		$wtipodoc = $row[1];
		
		//                 0      1      2      3      4      5      6      7      8      9      10     11
		$query = "select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing,Ingnre,Ubisac,Ubihac,Cconom from root_000036,root_000037,".$wdbmhos."_000016,".$wdbmhos."_000018,".$wdbmhos."_000011 ";
		$query .= " where pacced = '".$wcedula."'";
		$query .= "   and pactid = '".$wtipodoc."'";
		$query .= "   and pacced = oriced ";
		$query .= "   and pactid = oritid ";
		$query .= "   and oriori = '".$wemp_pmla."'";
		$query .= "   and inghis = orihis ";
		$query .= "   and inging = '".$wing."' ";
		$query .= "   and ubihis = inghis "; 
		$query .= "   and ubiing = inging ";
		$query .= "   and ccocod = ubisac ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row = mysql_fetch_array($err);
		$wsex="M";
		$sexo="MASCULINO";
		if($row[5] == "F")
		{
			$sexo="FEMENINO";
			$wsex="F";
		}
		$ann=(integer)substr($row[4],0,4)*360 +(integer)substr($row[4],5,2)*30 + (integer)substr($row[4],8,2);
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
		$wpac = $wtipodoc." ".$wcedula."<br>".$row[0]." ".$row[1]." ".$row[2]." ".$row[3];
		$whis=$row[6];
		if(!isset($wing))
			$wing=$row[7];
		$dia=array();
		$dia["Mon"]="Lun";
		$dia["Tue"]="Mar";
		$dia["Wed"]="Mie";
		$dia["Thu"]="Jue";
		$dia["Fri"]="Vie";
		$dia["Sat"]="Sab";
		$dia["Sun"]="Dom";
		$mes["Jan"]="Ene";
		$mes["Feb"]="Feb";
		$mes["Mar"]="Mar";
		$mes["Apr"]="Abr";
		$mes["May"]="May";
		$mes["Jun"]="Jun";
		$mes["Jul"]="Jul";
		$mes["Aug"]="Ago";
		$mes["Sep"]="Sep";
		$mes["Oct"]="Oct";
		$mes["Nov"]="Nov";
		$mes["Dec"]="Dic";
		$fechal=strftime("%a %d de %b del %Y");
		$fechal=$dia[substr($fechal,0,3)].substr($fechal,3);
		$fechal=substr($fechal,0,10).$mes[substr($fechal,10,3)].substr($fechal,13);
		$color="#dddddd";
		$color1="#C3D9FF";
		$color2="#E8EEF7";
		$color3="#CC99FF";
		$color4="#99CCFF";
		if(!isset($wing))
			$wintitulo="Historia:".$row[6]." Ingreso:".$row[7]." Paciente:".$wpac;
		else
			$wintitulo="Historia:".$row[6]." Ingreso:".$wing." Paciente:".$wpac;
		$Hgraficas=" |";
		echo "<input type='HIDDEN' name= 'wcedula' value=".$wcedula.">";
		echo "<input type='HIDDEN' name= 'wtipodoc' value=".$wtipodoc.">";
		echo "<center><table border=1 width='712' class=tipoTABLE1>";
		echo "<tr><td rowspan=3 align=center><IMG SRC='/MATRIX/images/medical/root/HCE".$wemp_pmla.".jpg' id='logo'></td>";	
		echo "<td id=tipoL01C>Paciente</td><td colspan=4 id=tipoL04>".$wpac."</td><td id=tipoL04A>P&aacute;gina 1</td></tr>";
		echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$whis."-".$wing."</td><td id=tipoL01>Edad</td><td id=tipoL02C>".$wedad."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".$sexo."</td></tr>";
		echo "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row[11]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$row[10]."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$row[8]."</td></tr>";
		echo "</table>";
				
		//                                                  0                                       1                                      2                                   3                                      4                          5    
		$query  = " select ".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movdat,".$empresa."_".$wformulario.".moving,".$empresa."_000002.Detfor from ".$empresa."_".$wformulario.",".$empresa."_000002 ";
		$query .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' "; 
		$query .= "   and ".$empresa."_".$wformulario.".movcon IN (7,9,11)";
		$query .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
		$query .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
		$query .= "   and ".$empresa."_".$wformulario.".movpro=".$empresa."_000002.detpro ";
		$query .= "   and ".$empresa."_".$wformulario.".movcon = ".$empresa."_000002.detcon ";
		$query .= "   and ".$empresa."_000002.Dettip = 'Grid' "; 
		$query .= "   Order by 1, 2, 3 "; 
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			$dmed1=array();
			$dmed2=array();
			$dmat1=array();
			$dmat2=array();
			$dsue1=array();
			$dsue2=array();
			$kmed = -1;
			$kmat = -1;
			$ksue = -1;
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[3]!= "0*")
				{
					$data=array();
					echo count_grid($row[3],$info);
					switch($row[0])
					{
						case 7:
							for ($j=0;$j<count($info);$j++)
							{
								$pos=array_search($info[$j][0],$dmed1);
								if($pos === false)
								{
									$kmed++;
									$dmed1[$kmed]  = $info[$j][0];
									$dmed2[$kmed] += $info[$j][1];
								}
								else
									$dmed2[$pos] += $info[$j][1];
							}
						break;
						case 9:
							for ($j=0;$j<count($info);$j++)
							{
								$pos=array_search($info[$j][0],$dmat1);
								if($pos === false)
								{
									$kmat++;
									$dmat1[$kmat]  = $info[$j][0];
									$dmat2[$kmat] += $info[$j][1];
								}
								else
									$dmat2[$pos] += $info[$j][1];
							}
						break;
						case 11:
							for ($j=0;$j<count($info);$j++)
							{
								$pos=array_search($info[$j][0],$dsue1);
								if($pos === false)
								{
									$ksue++;
									$dsue1[$ksue]  = $info[$j][0];
									$dsue2[$ksue] += $info[$j][1];
								}
								else
									$dsue2[$pos] += $info[$j][1];
							}
						break;
					}
				}
			}
			// usort($dmed1,'comparacion');
			// usort($dmat1,'comparacion');
			// usort($dsue1,'comparacion');
			echo "<br><table align=center border=0>";
			echo "<tr><td id='tipo0'><table align=center border=1 class='tipoTABLEGRID'>";
			echo "<tr><td id='tipoAL05GRID' colspan=2>CONTEO DE MEDICAMENTOS</td></tr>";
			echo "<tr><td id='tipoAL06GRID'>ITEM</td><td id='tipoAL06GRID'>CANTIDAD</td></tr>";
			for ($i=0;$i<count($dmed1);$i++)
			{
				if($i % 2 == 0)
					$gridcolor="tipoAL02GRID1";
				else
					$gridcolor="tipoAL02GRID2";
				echo "<tr><td class='".$gridcolor."'>".$dmed1[$i]."</td><td class='".$gridcolor."'>".$dmed2[$i]."</td></tr>";
			}
			echo "</table></td>";
			echo "<td id='tipo0'><table align=center border=1 class='tipoTABLEGRID'>";
			echo "<tr><td id='tipoAL05GRID' colspan=2>CONTEO DE MATERIALES</td></tr>";
			echo "<tr><td id='tipoAL06GRID'>ITEM</td><td id='tipoAL06GRID'>CANTIDAD</td></tr>";
			for ($i=0;$i<count($dmat1);$i++)
			{
				if($i % 2 == 0)
					$gridcolor="tipoAL02GRID1";
				else
					$gridcolor="tipoAL02GRID2";
				echo "<tr><td class='".$gridcolor."'>".$dmat1[$i]."</td><td class='".$gridcolor."'>".$dmat2[$i]."</td></tr>";
			}
			echo "</table></td>";
			echo "<td id='tipo0'><table align=center border=1 class='tipoTABLEGRID'>";
			echo "<tr><td id='tipoAL05GRID' colspan=2>CONTEO DE SUEROS</td></tr>";
			echo "<tr><td id='tipoAL06GRID'>ITEM</td><td id='tipoAL06GRID'>CANTIDAD</td></tr>";
			for ($i=0;$i<count($dsue1);$i++)
			{
				if($i % 2 == 0)
					$gridcolor="tipoAL02GRID1";
				else
					$gridcolor="tipoAL02GRID2";
				echo "<tr><td class='".$gridcolor."'>".$dsue1[$i]."</td><td class='".$gridcolor."'>".$dsue2[$i]."</td></tr>";
			}
			echo "</table></td></tr></table>";
			
		}
		else
			echo "<h3 class=tipo3G>SIN REGISTROS PARA ESTE PACIENTE</h3>";
		//                     0            1             2 
		$query  = " select Tcarprocod, Tcarpronom, sum(Tcarcan) from ".$wdbfac."_000106 ";
		$query .= " where Tcarconcod IN ('7104','7204') ";
		$query .= "   and Tcarhis = '".$whis."' ";
		$query .= "   and Tcaring = '".$wing."' ";
		$query .= "   and Tcarest = 'on' "; 
		$query .= "  Group by 1 "; 
		$query .= "  Order by 2 "; 
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			echo "<br><table align=center border=1 class='tipoTABLEGRID'>";
			echo "<tr><td id='tipoAL05GRID' colspan=3>MEDICAMENTOS E INSUMOS CARGADOS EN FACTURACION</td></tr>";
			echo "<tr><td id='tipoAL06GRID'>CODIGO</td><td id='tipoAL06GRID'>ITEM</td><td id='tipoAL06GRID'>CANTIDAD</td></tr>";
			for ($i=0;$i<$num;$i++)
			{
				$row = mysql_fetch_array($err);
				If($i % 2 == 0)
					$gridcolor="tipoAL02GRID1";
				else
					$gridcolor="tipoAL02GRID2";
				echo "<tr><td class='".$gridcolor."'>".$row[0]."</td><td class='".$gridcolor."'>".$row[1]."</td><td class='".$gridcolor."'>".$row[2]."</td></tr>";
			}
			echo "</table></center>";
		}
	}
}
?>
