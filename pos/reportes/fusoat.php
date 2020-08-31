<html>
<head>
<title>formualrio de accidente</title>
</head>
<body >
<?php
include_once("conex.php");

/********************************************************
*     PROGRAMA FRONT END DE REPORTE DE ACCIDENTES	*
*														*
*********************************************************/

//==================================================================================================================================
//PROGRAMA						:Reporte accidentes Fusoat
//AUTOR							:Carolina Castaño
//FECHA CREACION				:ENERO 2006
//FECHA ULTIMA ACTUALIZACION 	:18 de Enero de 2006
//DESCRIPCION					:primero pinta un formulario que pide el centro responsable del reporte y la historia clínica, luego un formulario para seleccionar el 
//								 accidente y por último consulta todos los datos del accidente en tablas de UNIX.
//==================================================================================================================================



session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	//se consultan datos de empresa responsable por el formulario
					//$empresa="farstore";
   //******AGREGADO*****					
  include_once("../../../include/root/comun.php");

  $conex = obtenerConexionBD("matrix");

  conexionOdbc($conex, 'movhos', &$conexUnix, 'facturacion');
  
  	
	if (!isset ($historia))
		{
			//FORMA QUE PIDE LA HISTORIA CLÍNICA Y LA EMPRESA RELACIONADA CON EL REPORTE
			if (!isset ($estado))
			$empresa=$empresa."_000064";
			

			

			$query ="SELECT nombre FROM $empresa  where estado='on'";
			$result = mysql_query($query);
	
			echo "<table border=0 align=center>";
			echo "<tr><td align=center bgcolor='#cccccc'><A NAME='Arriba'><font size=5>FORMULARIO DE RECLAMACIÓN DE ACCIDENTES</font></a></tr></td>";
			echo "</table></br>";
			
			echo "<table border=0 align=center>";
			echo "<tr><td align=center bgcolor='#cccccc'><font size=4>INGRESE POR FAVOR LOS SIGUIENTES DATOS:</font></td>";
			echo "</table>";
			echo "<table border=0 align=center>";
			echo "<tr><td align=center bgcolor='#cccccc'></td></tr>";
			echo "<form NAME='ingreso' ACTION='' METHOD='POST'>";
			echo "<tr><td align=center bgcolor='#cccccc'><font size=2  face='arial'>Centro asistencial responsable:</b>&nbsp;</td>";
			echo "<td align=center bgcolor='#cccccc' ><font size='2'  align=center face='arial'><select name='centro'>";
			
			
			While ( $resulta = mysql_fetch_row($result) )
  			{ 
				$option = "<option value='$resulta[0]'";
				echo $option.">".$resulta[0]."</option>";
   			} 
   			echo "</select>";
			
   			echo "</td></tr>";
			echo "<tr><td align=center bgcolor='#cccccc'><font size=2  face='arial'>Identificación del paciente:</b>&nbsp</td>";
			echo "<td align=center bgcolor='#cccccc' ><font size='2'  align=center face='arial'><input type='text' name='historia'></td></tr>";
			echo "<tr><td align=center bgcolor='#cccccc'><font size=2  face='arial'>Tipo de Identificación:</b>&nbsp</td>";
			echo "<td align=center bgcolor='#cccccc' ><font size='2'  align=center face='arial'><input type='radio' name='group1' value='0' checked> Identificación personal";
			echo "&nbsp;<input type='radio' name='group1' value='1'>Historia clínica<br></td></tr>";
			echo "<input type='hidden' name='empresa' value='$empresa'>";
			echo "<tr><td align=center bgcolor='#cccccc' colspan=2><font size='2'  align=center face='arial'><input type='submit' name='aceptar' value='INGRESAR' ></td></tr></form>";			
			include_once("free.php");
   
		}
		
		else
		{
			if (!isset ($accidente))
			{
				//forma que pide seleccionar el accidente
				
				$bd='facturacion';
				include_once("socket.php");
				
				if ($group1==0)
				{
					$query="select  pachis from inpac";
					$query= $query." where pacced='$historia'";
					
					
					$err_o = odbc_exec($conex_o,$query);
					
					if (odbc_fetch_row($err_o))
						{
							$historia=odbc_result($err_o,1);
					
						}else
						{
							$query="select  pachis from inpaci";
							$query= $query." where pacced='$historia'";
							$err_1 = odbc_exec($conex_o,$query);
							if (odbc_fetch_row($err_1))
							{
								$historia=odbc_result($err_1,1);
							}else
							{
								$historia='';
							}

						}	
				}
			
				$query="select accacc, accfec from inacc";
				$query= $query." where acchis='$historia' and accind='P'";
				$err_o = odbc_exec($conex_o,$query);
			
				$contador=0;
			
				echo "<table border=0 align=center>";
				echo "<tr><td align=center bgcolor='#cccccc'><A NAME='Arriba'><font size=5>FORMULARIO DE RECLAMACIÓN DE ACCIDENTES</font></a></tr></td>";
				echo "</table></br></BR></BR>";
			
				$color=1;
				
				while (odbc_fetch_row($err_o))
				{
					if ($contador==0)
					{
						echo "<table  align=center>";
						$centro2=strtoupper($centro);
						echo "<tr><td align=center bgcolor='#cccccc'><font size=4>$centro2</font></td>";
						echo "<tr><td align=center bgcolor='#cccccc'><font size=4>NUMERO DE HISTORIA: $historia</font></td>";
						echo "<tr><td align=center bgcolor='#cccccc'><font size=4>SELECCIONE EL ACCIDENTE POR FAVOR</font></td>";
						echo "</table> </BR></BR>";
						echo "<font size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<A href='fusoat.php?empresa=$empresa&estado=1'>VOLVER</A></font>";
						echo "<table border=1 align=center width=50%>";			
					}
					
					$contador++;
					$err_1=odbc_result($err_o,1);
					$err_2=odbc_result($err_o,2);
				
					
					if ($color==0)
					{
					echo "<tr><td bgcolor='#cccccc' align=center width=15% style='color: black; border-left: .75pt solid silver; border-right: .75pt solid silver; border-top: .75pt solid silver; border-bottom: .75pt solid silver'><font size=2>$err_1. ACCIDENTE</font></td>";
					echo "<td bgcolor='#cccccc' align=center width=20% style='color: black; border-left: .75pt solid silver; border-right: .75pt solid silver; border-top: .75pt solid silver; border-bottom: .75pt solid silver'><font size=2>FECHA: $err_2</font></td>";
					echo "<td bgcolor='#cccccc' align=center width=15% style='color: black; border-left: .75pt solid silver; border-right: .75pt solid silver; border-top: .75pt solid silver; border-bottom: .75pt solid silver'><font size=2><A href='fusoat.php?historia=$historia&accidente=$err_1&fechaAccidente=$err_2&centro=$centro2&empresa=$empresa'>IMPRIMIR</A></font></td>";
					echo "</tr>";
					
					}
					if ($color==1)
					{
					echo "<tr><td bgcolor='#EOFFFF' align=center width=15% style='color: black; border-left: .75pt solid silver; border-right: .75pt solid silver; border-top: .75pt solid silver; border-bottom: .75pt solid silver'><font size=2>$err_1. ACCIDENTE</font></td>";
					echo "<td bgcolor='#EOFFFF' align=center width=20% style='color: black; border-left: .75pt solid silver; border-right: .75pt solid silver; border-top: .75pt solid silver; border-bottom: .75pt solid silver'><font size=2>FECHA: $err_2</font></td>";
					echo "<td bgcolor='#EOFFFF' align=center width=15% style='color: black; border-left: .75pt solid silver; border-right: .75pt solid silver; border-top: .75pt solid silver; border-bottom: .75pt solid silver'><font size=2><A href='fusoat.php?historia=$historia&accidente=$err_1&fechaAccidente=$err_2&centro=$centro2&empresa=$empresa'>IMPRIMIR</A></font></td>";
					echo "</tr>";
					$color=0;
					}
					else
					{
						$color=1;
		
					}
				
			 	
				}
				echo "</table>";
				
			
				if ($contador==0)
				{ 
					//si no se encuentran accidentes para la historia
			
			
					echo "<table border=0 align=center>";
					echo "<tr><td align=center bgcolor='#cccccc'></td></tr>";
					echo "<form NAME='rechazo' ACTION='' METHOD='POST'>";
					echo "<tr><td align=center bgcolor='#cccccc'><font size=2  face='arial'>No existen accidentes reportados para el paciente ingresado, intente nuevamente</td><tr>";
					echo "<tr><td align=center bgcolor='#cccccc' ><font size='2'  align=center face='arial'><input type='submit' name='regresar' value='ACEPTAR' ></td></tr></form>";			
				}
			}
			else
			{
				// Impresión del formulario
			
				$bd='facturacion';
				include_once("socket.php");
			
				//Busco detalles de accidente
				$query="select A.accdethor, A.accdetmun, A.accdetlug,  A.accdetasn, A.accdetpol, A.accdetmar, A.accdetpla, ";
				$query= $query." A.accdettip, A.accdetnom, A.accdetced, A.accdetdir, A.accdettel, A.accdetfin, A.accdetffi, A.accdetob1, ";
				$query= $query." A.accdetob2, A.accdetocu, A.accdetzon, A.accdetase, A.accdetmuc, B.munnom, B.mundep, C.depnom ";
				$query= $query." from inaccdet A, inmun B, indep C where A.accdethis='$historia' and A.accdetacc='$accidente'";
				$query= $query." and B.muncod=A.accdetmun and C.depcod=B.mundep";
				$err_o = odbc_exec($conex_o,$query);

				if (odbc_fetch_row($err_o))
				{
	
					for($i=1;$i<=odbc_num_fields($err_o);$i++)
					{
						$array[odbc_field_name($err_o,$i)]=odbc_result($err_o,$i);
					}
					$hora=$array['accdethor'];
					$Amun=$array['munnom'];
					$Adep=$array['depnom'];
					$Alug=$array['accdetlug'];
					$aseguradora=$array['accdetasn'];
					$poliza=$array['accdetpol'];
					$marca=$array['accdetmar'];
					$placa=$array['accdetpla'];
					$Atipo=$array['accdettip'];
					$Cnombre=$array['accdetnom'];
					$Cdoc=$array['accdetced'];
					$Cdir=$array['accdetdir'];
					$Ctel=$array['accdettel'];
					$Pinicio=$array['accdetfin'];
					$Pven=$array['accdetffi'];
					$informe1=$array['accdetob1'];
					$informe2=$array['accdetob2'];
					$ocupante=$array['accdetocu'];
					$Azon=$array['accdetzon'];
					$Aase=$array['accdetase'];
					$Amuncod=$array['accdetmuc'];
					
				
					$query="select munnom from inmun where muncod='$Amuncod'";
					$err_o = odbc_exec($conex_o,$query);
					if (odbc_fetch_row($err_o))
					{
						$Cconductor=odbc_result($err_o,1);
					}
					else
					{
						$Cconductor='';
					}
				
				}
			
				//busco máximo ingreso
				$query="select max(accnum) from inacc";
				$query= $query." where acchis='$historia' and accacc='$accidente'";
				$err_o = odbc_exec($conex_o,$query);
				$maximoingreso=odbc_result($err_o,1);
		
				//Busco si todavía está hospitalizado está en inpac
				$query="select A.pacced, A.pactid, A.pacap1, A.pacap2, A.pacnom, A.pacsex, A.paclug, A.pacnac, A.pacdir, ";
				$query= $query."  A.pactel, A.pacmun, A.pachor, A.pacdin, A.pacfec, B.munnom, C.diades from inpac A, OUTER inmun B, OUTER india C where A.pachis='$historia' and A.pacnum='$maximoingreso'";
				$query= $query." and B.muncod=A.paclug and C.diacod=A.pacdin";
				$err_o = odbc_exec($conex_o,$query);
				if (odbc_fetch_row($err_o))
				{
					for($i=1;$i<=odbc_num_fields($err_o);$i++)
						{
		
							$array[odbc_field_name($err_o,$i)]=odbc_result($err_o,$i);

						}
					$doc=$array['pacced'];
					$tipo=$array['pactid'];
					$apellido1=$array['pacap1'];
					$apellido2=$array['pacap2'];
					$nombre=$array['pacnom'];
					$sexo=$array['pacsex'];
					$lugar=$array['munnom'];
					$fechanac=$array['pacnac'];
					$direccion=$array['pacdir'];
					$telefono=$array['pactel'];
					$municipio=$array['pacmun'];
					$fechaing=$array['pacfec'];
					$horaing=$array['pachor'];
					$diaing=$array['diades'];
					$diadef='';
					$fechaegr='';
				
				}
				else
				{
					//si no esta en hospital Busco en inpaci datos de del paciente y en inmgr datos del ingreso
					$query="select A.pacced, A.pactid, A.pacap1, A.pacap2, A.pacnom, A.pacsex, A.paclug, A.pacnac, A.pacdir, ";
					$query= $query."  A.pactel, A.pacmun, B.egring, B.egrhoi, B.egregr, B.egrdia, B.egrdin, C.munnom, D.diades from inpaci A, inmegr B, OUTER inmun C, OUTER india D where A.pachis='$historia'";
					$query= $query."  and B.egrhis='$historia' and B.egrnum='$maximoingreso' and C.muncod=A.paclug and D.diacod=B.egrdia";
					$err_o = odbc_exec($conex_o,$query);
					if (odbc_fetch_row($err_o))
					{
	
						for($i=1;$i<=odbc_num_fields($err_o);$i++)
						{
		
							$array[odbc_field_name($err_o,$i)]=odbc_result($err_o,$i);

						}
						$doc=$array['pacced'];
						$tipo=$array['pactid'];
						$apellido1=$array['pacap1'];
						$apellido2=$array['pacap2'];
						$nombre=$array['pacnom'];
						$sexo=$array['pacsex'];
						$lugar=$array['munnom'];
						$fechanac=$array['pacnac'];
						$direccion=$array['pacdir'];
						$telefono=$array['pactel'];
						$municipio=$array['pacmun'];
						$fechaing=$array['egring'];
						$fechaegr=$array['egregr'];
						$horaing=$array['egrhoi'];
						$diaing=$array['diades'];
						$diacod=$array['egrdin'];
						
						$query="select D.diades from india D where D.diacod='$diacod'";
					
						$err_o = odbc_exec($conex_o,$query);
						if (odbc_fetch_row($err_o))
						{
	
								$diadef=odbc_result($err_o,1);
					
						}	
					}
					else
					{
						//no se pueden encontrar los datos de esa historia
						echo "<table border=0 align=center>";
						echo "<tr><td align=center bgcolor='#cccccc'><A NAME='Arriba'><font size=5>FORMULARIO DE RECLAMACIÓN DE ACCIDENTES</font></a></tr></td>";
						echo "</table></br></BR></BR>";
						echo "<table border=0 align=center>";
						echo "<tr><td align=center bgcolor='#cccccc'></td></tr>";
						echo "<form NAME='rechazo' ACTION='' METHOD='POST'>";
						echo "<tr><td align=center bgcolor='#cccccc'><font size=2  face='arial'>No existen datos de ingreso asociados a este accidente</td><tr>";
						echo "<tr><td align=center bgcolor='#cccccc' ><font size='2'  align=center face='arial'><input type='submit' name='regresar2' value='ACEPTAR' ></td></tr></form>";	
					 	$paro =1;
					}
					
					
					
				}
			
				if (!isset ($paro))
				{
					//se encontraron los dato se pasan a mostrar
					
					
					

					

					$query ="SELECT nit, direccion, ciudad, telefono FROM $empresa where nombre='$centro'";
					$err=mysql_query($query,$conex);
					$num=mysql_num_rows($err);
					if($num >=1)
					{
						$row=mysql_fetch_array($err);
						$nit=$row['nit'];
						$direccion2=$row['direccion'];
						$ciudad=strtoupper($row['ciudad']);
						$telefono2=$row['telefono'];
	
					}
		//echo $centro;
		//echo $maximoingreso;
echo "<center> <B>FORMULARIO UNICO DE RECLAMACION DE LAS ENTIDADES HOSPITALARIAS </B></center>";
	echo "<table align=right width=80% >";
		echo "<tr>";
			echo "<td align=center width=70% ><B>POR EL SEGURO OBLIGATORIO</B></td>";
			echo "<td align=left width=20%>Pag. </td>";
			echo "<td align=left width=10%>1</td>";
		echo "</tr>";
	echo "</table>";
	echo "</BR></BR>";

	echo "<font size=3><B>1. Datos del centro asistencial</B></font></BR></BR>";
	

	echo "<font size=2>&nbsp;&nbsp;&nbsp;Nombre del centro: $centro &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<font size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "&nbsp;&nbsp;&nbsp;NIT: $nit</font></BR>";
	
	echo "<font size=2>&nbsp;&nbsp;&nbsp;Dirección: $direccion2 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<font size=2>&nbsp;&nbsp;&nbsp;Ciudad: $ciudad &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "&nbsp;TEL: $telefono2</font></BR>";
	
	echo "<HR>";
	
	echo "<font size=3><B>2. Datos del accidentado</B></font></BR></BR>";
	
	echo "<table align=RIGHT width=99% cellpadding=1 cellspacing=1>";
		echo "<tr>";
			echo "<td align=left width=62% ><font size=2><b>2.1 Información del accidentado</b></font></td>";
			echo "<td align=left width=8%><font size=2>Años</font></td>";
			echo "<td align=left width=6%><font size=2>M/F</font></td>";
			echo "<td align=left width=12%><font size=2>Documento $tipo</font></td>";
			echo "<td align=left width=11%><font size=2>N': $doc</font></td>";
		echo "</tr>";
	echo "</table></BR></BR>";
	echo "<table align=RIGHT width=98% cellpadding=1 cellspacing=1>";
		echo "<tr>";
			echo "<td align=left width=58%><font size=2>Apellidos y nombres: $apellido1 $apellido2 $nombre</font></td>";
			//saber si la fecha de actualización ha sido más de un año
			$edad=mktime(0,0,0,substr($fechanac,5,2),substr($fechanac,8,2),substr($fechanac,0,4))-mktime(0,0,0,date('m'),date('d'),date('Y'));
			$edad=intval ($edad=((((($edad/60)/60)/24)/30.4)/12)*(-1));
			echo "<td align=left width=9%><font size=2>Edad:$edad</td>";
			
			echo "<td align=left width=8%><font size=2>Sexo:$sexo</font></td>";
			echo "<td align=left width=12%><font size=2>Identidad:</font></td>";
			echo "<td align=left width=11%><font size=2>De:</font></td>";
		echo "</tr>";
	echo "</table></BR></BR>";
	echo "<table align=RIGHT width=98% cellpadding=1 cellspacing=1>";
		echo "<tr>";
			echo "<td align=left width=38%><font size=2>Dirección: $direccion</font></td>";
			echo "<td align=left width=30%><font size=2>Ciudad:$lugar</font></td>";
			echo "<td align=left width=30%><font size=2>TEL: $telefono</font></td>";
		echo "</tr>";
	echo "</table></BR></BR>";
	echo "<table align=RIGHT width=98% cellpadding=1 cellspacing=1>";
		echo "<tr>";
			echo "<td align=left width=32%><font size=2>Cindiciones del accidentado:</font></td>";
			if (strcmp ($ocupante,"O")==0)
			{
					
			echo "<td align=left width=15%><font size=2>Ocupante:X</font></td>";
			echo "<td align=left width=50%><font size=2>Peatón:</font></td>";
			}
			if (strcmp ($ocupante,"P")==0)
			{	
			echo "<td align=left width=15%><font size=2>Ocupante:</font></td>";
			echo "<td align=left width=50%><font size=2>Peatón:X</font></td>";
			}
		echo "</tr>";
	echo "</table></BR></BR>";
	echo "<table align=RIGHT width=99% cellpadding=1 cellspacing=1>";
		echo "<tr>";
			echo "<td align=left width=60% ><font size=2><b>2.2 Identificación del accidente</b></font></td>";
			echo "<td align=left width=39%><font size=2>Año/Mes/Día</font></td>";
		echo "</tr>";
	echo "</table></BR></BR>";
	echo "<table align=RIGHT width=98% cellpadding=1 cellspacing=1>";
		echo "<tr>";
			echo "<td align=left width=57%><font size=2>Lugar del accidente: $Alug</font></td>";
			echo "<td align=left width=16%><font size=2>Fecha:$fechaAccidente</font></td>";
			echo "<td align=left width=15%><font size=2>Hora:$hora</font></td>";
			if ($hora<12)
			{	
			echo "<td align=left width=5%><font size=2>AM:X</font></td>";
			echo "<td align=left width=5%><font size=2>PM:</font></td>";
			}
			else
			{	
			echo "<td align=left width=5%><font size=2>AM:</font></td>";
			echo "<td align=left width=5%><font size=2>PM:X</font></td>";
			}
		echo "</tr>";
	echo "</table></BR></BR>";
	echo "<table align=RIGHT width=98% cellpadding=1 cellspacing=1>";
		echo "<tr>";
			echo "<td align=left width=38%><font size=2>Municipio: $Amun</font></td>";
			echo "<td align=left width=30%><font size=2>Departamento: $Adep</font></td>";
			if (strcmp ($Azon,"U")==0)
			{	
			echo "<td align=left width=20%><font size=2>Zona Urbana:X </font></td>";
				echo "<td align=left width=20%><font size=2>Rural:</font> </td>";
			}
			if (strcmp ($Azon,"R")==0)
			{	
				echo "<td align=left width=20%><font size=2>Zona Urbana: </font></td>";
				echo "<td align=left width=20%><font size=2>Rural:X </font></td>";
			}
		echo "</tr>";
	echo "</table></BR></BR>";
	echo "<table align=RIGHT width=98% cellpadding=1 cellspacing=1>";
		echo "<tr>";
			echo "<td align=left width=98% ><font size=2>Informe del accidente:</font></td>";
		echo "</tr>";
	echo "</table></BR></BR>";
	echo "<table align=RIGHT width=90% cellpadding=1 cellspacing=1>";
		echo "<tr>";
			echo "<td align=left width=90% ><font size=2>(Relato de los hechos): $informe1 $informe2</font></td>";
		echo "</tr>";
	echo "</table></BR></BR>";
	
	echo "<HR>";
	echo "<table align=RIGHT width=99% cellpadding=1 cellspacing=1>";
		echo "<tr>";
			echo "<td align=left width=99% ><font size=2><b>2.3 Información del vehículo:<b></font></td>";
		echo "</tr>";
	echo "</table></BR></BR>";
	echo "<table align=RIGHT width=98% cellpadding=1 cellspacing=1>";
	echo "	<tr>";
			echo "<td align=left width=33%><font size=2>Marca: $marca</font></td>";
			echo "<td align=left width=30%><font size=2>Placa: $placa </font></td>";
			echo "<td align=left width=35%><font size=2>Clase de vehículo: $Atipo</font></td>";
		echo "</tr>";
	echo "</table></BR></BR>";
	echo "<table align=RIGHT width=98% cellpadding=1 cellspacing=1>";
		echo "<tr>";
			echo "<td align=left width=63% ><font size=2>Nombre aseguradora: $aseguradora</font></td>";
			echo "<td align=left width=35%><font size=2>Sucursal o agencia: </font></td>";
		echo "</tr>";
	echo "</table></BR></BR>";
	echo "<table align=RIGHT width=90% cellpadding=1 cellspacing=1>";
		echo "<tr>";
			echo "<td align=left width=49% ><font size=2>SI / NO / FANT</font></td>";
			echo "<td align=left width=24%><font size=2>Año/Mes/Día</font></td>";
			echo "<td align=left width=17%><font size=2>Año/Mes/Día</font></td>";
		echo "</tr>";
	echo "</table></BR></BR>";
	echo "<table align=RIGHT width=98% cellpadding=1 cellspacing=1>";
		echo "<tr>";
		if (strcmp ($Aase,"A")==0)
			{	
			echo "<td align=left width=16%><font size=2>Asegurado: SI</font></td>";
			}
			if (strcmp ($Azon,"N")==0)
			{	
					echo "<td align=left width=16%><font size=2>Asegurado: NO</font></td>";
			}
			if (strcmp ($Azon,"F")==0)
			{	
					echo "<td align=left width=16%><font size=2>Asegurado: FANT</font></td>";
			}
		
			echo "<td align=left width=35%><font size=2>Poliza SOAT Nº:$poliza</font></td>";
			echo "<td align=left width=28%><font size=2>Vigencia desde: $Pinicio</font></td>";
			echo "<td align=left width=19%><font size=2>Hasta: $Pven</font></td>";
		echo "</tr>";
	echo "</table></BR></BR>";
	echo "<table align=RIGHT width=30% cellpadding=1 cellspacing=1>";
		echo "<tr>";
			echo "<td align=left width=10% ><font size=2>Documento</font></td>";
			echo "<td align=left width=20%><font size=2>Nro. $Cdoc</font></td>";
		echo "</tr>";
	echo "</table></BR></BR>";
	echo "<table align=RIGHT width=98% cellpadding=1 cellspacing=1>";
		echo "<tr>";
			echo "<td align=left width=69% ><font size=2>Apellidos y nombre del conductor: $Cnombre</font></td>";
			echo "<td align=left width=10%><font size=2>Identidad:</font></td>";
			echo "<td align=left width=19%><font size=2>De: </font></td>";
		echo "</tr>";
	echo "</table></BR></BR>";
	echo "<table align=RIGHT width=98% cellpadding=1 cellspacing=1>";
		echo "<tr>";
			echo "<td align=left width=38%><font size=2>Dirección: $Cdir</font></td>";
			echo "<td align=left width=30%><font size=2>Ciudad: $Cconductor </font></td>";
			echo "<td align=left width=30%><font size=2>TEL: $Ctel</font></td>";
		echo "</tr>";
	echo "</table></BR></BR>";

	echo "<HR>";
	
	echo "<font size=3><B>3. Datos sobre la atención del accidente</B></font> </BR> </BR>";
	echo "<table align=RIGHT width=99% cellpadding=1 cellspacing=1>";
		echo "<tr>";
		echo "	<td align=left width=99% ><font size=2><B>3.1 En centro asistencial:</B></font></td>";
		echo "</tr>";
	echo "</table></BR></BR>";
	echo "<table align=RIGHT width=88% cellpadding=1 cellspacing=1>";
		echo "<tr>";
		echo "	<td align=left width=88% ><font size=2>Año/Mes/Día</font></td>";
		echo "</tr>";
	echo "</table></BR></BR>";
	echo "<table align=RIGHT width=98% cellpadding=1 cellspacing=1>";
	echo "	<tr>";
			echo "<td align=left width=29%><font size=2>Fecha ingreso: $fechaing </font></td>";
		echo "	<td align=left width=20%><font size=2>Hora de ingreso: $horaing </font></td>";
		if ($horaing<12)
			{	
			echo "<td align=left width=5%><font size=2>AM:X</font></td>";
			echo "<td align=left width=5%><font size=2>PM:</font></td>";
			}
			else
			{	
			echo "<td align=left width=5%><font size=2>AM:</font></td>";
			echo "<td align=left width=15%><font size=2>PM:X</font></td>";
			}
		echo "	<td align=left width=28%><font size=2>Historia clínica Nº: $historia</font></td>";
		echo "</tr>";
	echo "</table></BR></BR>";
	echo "<table align=RIGHT width=98% cellpadding=1 cellspacing=1>";
		echo "<tr>";
		echo "	<td align=left width=98% ><font size=2>Fecha egreso: $fechaegr</font></td>";
		echo "</tr>";
	echo "</table></BR></BR>";
	echo "<table align=RIGHT width=93% cellpadding=1 cellspacing=1>";
		echo "<tr>";
		echo "	<td align=left width=21%><font size=2>Dias de estancia:</font></td>";
		echo "	<td align=left width=18%><font size=2>Tratamiento:</font></td>";
		echo "	<td align=left width=18%><font size=2>Observación:</font></td>";
		echo "	<td align=left width=18%><font size=2>Ambulatorio:</font></td>";
		echo "	<td align=left width=18%><font size=2>Hospitalario:</font></td>";
	echo "	</tr>";
	echo "</table></BR></BR>";
	
	echo "<font size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Diagnóstico(s) de ingreso: $diaing</font></br>";
	echo "<font size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Diagnóstico definitivo: $diadef</font></br></br>";
	echo "<font size=2><b>&nbsp; &nbsp;3.2 Remisión</font></b></br>";
	
	echo "<font size=2>&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Año/Mes/Día</font></BR>";
	
	
	echo "<font size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Persona remitida de:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<font size=2>&nbsp;&nbsp;&nbsp;Ciudad: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "&nbsp;&nbsp;&nbsp;Fecha:</font></BR>";
	
	echo "<font size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Persona remitida a:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "<font size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ciudad: &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
	echo "&nbsp;&nbsp;&nbsp;Fecha:</font></BR>";
		
	echo "<hr>";

	echo "<font size=3><B>4. Datos sobre el accidentado.... (estos datos no tienen valor legal)</B></font></BR>";
	
	echo "<table align=RIGHT width=98% cellpadding=1 cellspacing=1>";
	echo "	<tr>";
	echo "		<td align=left width=98% ><font size=2>Causa inmediate de muerte:</font></td>";
	echo "	</tr>";
	echo "</table></BR>";
	echo "<table align=RIGHT width=98% cellpadding=1 cellspacing=1>";
		echo "<tr>";
		echo "	<td align=left width=28%><font size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha de muerte:</font></td>";
		echo "	<td align=left width=14%><font size=2>Año:</font></td>";
		echo "	<td align=left width=14%><font size=2>Mes:</font></td>";
		echo "	<td align=left width=14%><font size=2>Dís:</font></td>";
		echo "	<td align=left width=14%><font size=2>Hora:</font></td>";
		echo "	<td align=left width=7%><font size=2>AM:</font></td>";
		echo "	<td align=left width=7%><font size=2>PM:</font></td>";
	echo "	</tr>";
	echo "</table></BR>";
	
	
	echo "<table align=RIGHT width=98% cellpadding=1 cellspacing=1>";
	echo "	<tr>";
		echo "	<td align=left width=68% ><font size=2>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Apellidosy nombre(s)del médico que firmo el certificado de defunción:</font></td>";
		echo "	<td align=left width=30% ><font size=2>Registro médico Nº: </font></td>";
		
		echo "</tr>";
	echo "</table></BR></BR>";	
	
	echo "<HR>";
	echo "</B><font size=2>*DELACRACION DEL CENTRO ASISTENCIAL: En representación del centro asistencial en mencíon, declaro bajo la gravedad de juramento, que ";
	echo " la información diligenciada en este documento es cierta y puede ser verificada por la compañía de seguros y/o FISALUD antes de ";
	echo " los (30) días de la fecha de presentación; de no ser así, acepto todas las consecuencias legales que produzca esta situación.</font>";
	echo " </br></br>";
	echo " <center>________________________________________<center></br>";
	echo " <center>FIRMA Y SELLO AUTORIZADO<center>";
include_once("free.php");
}
}
}

}

	
?>
</body >
</html>
