<html>
<head>
<title>Historia Odontologica del Paciente</title>
</head>
<body >
<font face='arial'>
<BODY TEXT="#000066">

<script type="text/javascript">
function enter()
{
	document.forms.datos.submit();
}

</script>
<?php
include_once("conex.php");

/********************************************************
*    HISTORIA ODONTOLOGICA DEL PACIENTE					*
*														*
*********************************************************/

//==================================================================================================================================
//PROGRAMA						:Historia Odontologica del Paciente
//AUTOR							:Juan David Londoño
//FECHA CREACION				:9 de Marzo de 2009
//FECHA ULTIMA ACTUALIZACION 	:9 de Marzo de 2009
//DESCRIPCION					:Este es el reporte de la historia clinica odontologica de SOE
//
//ACTUALIZACIONES: xxxx-xx-xx: xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx.
//				   		
//==================================================================================================================================

function array_s($item,&$actividad,$k,$ubicacion)
{
	$it1=substr($item,0,strpos($item," "))." Real.";
	for ($j=0;$j<=$k;$j++)
	{
		if($it1 == $actividad[$j][3] and $actividad[$j][7] == 0 and $actividad[$j][4] == $ubicacion)
		{
			$actividad[$j][7]=1;
			return true;
		}
	}
	return false;
}
	

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{

	

	


	if(!isset($ing))// este es el encabezado donde pregunta los datos del paciente
	{

		echo "<form name='datos' form action='' method=post>";


		echo "<table border=0 align=center >";
		echo "<tr><td align=center><img src='/matrix/images/medical/pos/logo_".$empresa.".png' width='242' height='133'></td>";
		echo "<tr><td align=center><font size='5'><br>HISTORIA ODONTOLOGICA DEL PACIENTE<br><font size='1'>Ver. 2009-03-09</td></tr>";
		echo "</table>";
		echo "<br>";
		echo "<table border=(1) align=center>";

		if(!isset($numhis)and (!isset($cod)))// oculta el textbox cuando pasa del nombre
		{
			echo "<tr><td>PACIENTE:</td><td colspan=1 align=center><input type='TEXT' name='numhis'size=30 maxlength=30 ></td></tr>";

		}
		if(isset($numhis))// pregunto por la cedula, nombre o numero de historia

		{
			$query="select Pacdoc, Pacno1, Pacno2, Pacap1, Pacap2, Pachis 
					  from ".$empresa."_000100 
					 where Pacdoc like '%".$numhis."%' 
					 	or Pacno1 like '%".$numhis."%'
					    or Pacno2 like '%".$numhis."%' 
					    or Pacap1 like '%".$numhis."%' 
					    or Pacap2 like '%".$numhis."%'
					    or Pachis like '%".$numhis."%' 
					 order by Pacdoc";

			echo "<tr><td>PACIENTE:</td><td><select name='cod'  onchange='enter()'>";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					if($row[0]==$pac)
					echo "<option selected>".$row[0]."</option>";
					else
					echo "<option>".$row[0]."-".$row[1]."-".$row[2]."-".$row[3]."-".$row[4]."-".$row[5]."</option>";
				}
			}
			echo "</select></td></tr>";
		}
		
		if(isset($cod))// pregunto por el numero de ingreso
		{

			echo "<tr><td>PACIENTE:</td><td><b><i>$cod</td></tr>";// nombre del paciente
			echo "<input type=hidden name=cod value='".$cod."'>";
			$hist=explode('-',$cod);// trae numero de historia
			$query="select Ingnin 
					  from ".$empresa."_000101  
					 where Inghis = '".$hist[5]."'";
			echo "<tr><td>INGRESO:</td><td><select name='ing' onchange='enter()'>";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo $num;
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					if($row[0]==$odon)
					echo "<option selected>".$row[0]."</option>";
					else
					echo "<option>".$row[0]."</option>";
				}
			}
			echo "</select></td></tr>";
		}
	
		
		echo"<tr><td colspan=2 align=center><input type='submit' value='ACEPTAR'></td></tr></form>";
		echo "</table>";
		
	}
	else
	{ 
		
	////////////////////////////////////////////////// ACA EMPIEZA LA IMPRESION //////////////////////////////////////////////////////////////////	
	
	$exp=explode('-',$cod);// los datos del paciente
	
	//datos demograficos
	$query="select Pacsex, Pacfna, Seldes, Pacdir, Pacofi, Pactel, Pacnoa, Pacpaa, Pacdia, Pactea   
			  from ".$empresa."_000100, ".$empresa."_000105 
			 where Pachis = '".$exp[5]."'
			   and Seltip='04'
			   and Pacest=Selcod";
	$err = mysql_query($query,$conex);
	$row = mysql_fetch_array($err);
	//echo mysql_errno() ."=". mysql_error();
	
	//para la profesion
	$query="select Descripcion    
		      from root_000003 
		     where Codigo = '".$row[4]."'";
	$err = mysql_query($query,$conex);
	$ofi = mysql_fetch_array($err);
	//echo mysql_errno() ."=". mysql_error();
	
	//datos del ingreso
	$query="select Ingfei  
			  from ".$empresa."_000101 
			 where Inghis = '".$exp[5]."'
			 and Ingnin ='".$ing."'";
	$err = mysql_query($query,$conex);
	$in = mysql_fetch_array($err);
	
	// para el motivo de consulta
	$query="select Mdxobs, Fecha_data  
			  from ".$empresa."_000132  
			 where Mdxhis = '".$exp[5]."' 
			  and Mdxing = '".$ing."'  
			  and Mdxpro = 'MC01'";
	$err = mysql_query($query,$conex);		
	$cons = mysql_fetch_array($err);
	
	$edad=$in[0]-$row[1]; //para la edad
	
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////	
		echo "<table border=1 align=center width='750' bgcolor=#DDDDDD>";
		echo "<tr><td align=left><img src='/matrix/images/medical/pos/logo_".$empresa.".png' width='182' height='83'><td></td>
			 <td align=center><font size='3'><b>HISTORIA <br>ODONTOLOGICA<br><font size='1'>Ver. 2009-03-09</td>";
		echo "<td></td><td><table border=1 align=center bgcolor=#FFFFFF>
			  <tr><td><B><font size='2'>NUMERO DE HISTORIA:</td><td><font size='2'>".$exp[5]."-".$ing."</td></tr>
			  <tr><td><B><font size='2'>FECHA DE INGRESO:</td><td><font size='2'>".$in[0]."</td></tr>
			  <tr><td><B><font size='2'>DOCUMENTO DE IDENTIDAD:</td><td><font size='2'>".$exp[0]."</td></tr>
			  </table></td>";
		echo "</table>";
		echo "<br>";
		echo "<table border=1 align=center width='750'>";
		echo "<tr><td><font size='2'><B>NOMBRES Y APELLIDOS:</td><td><font size='2'>".$exp[1]."&nbsp".$exp[2]."&nbsp".$exp[3]."&nbsp".$exp[4]."</td>
			  <td><font size='2'><B>EDAD:</td><td>".$edad."</td>
			  <td><font size='2'><B>SEXO:</td><td><font size='2'>".$row[0]."</td><tr>";
		echo "<tr><td ><font size='2'><B>FECHA DE NACIMIENTO:</td><td><font size='2'>".$row[1]."</td>
		      <td COLSPAN=2><B><font size='2'>ESTADO CIVIL:</td><td COLSPAN=2><font size='2'>".$row[2]."</td></tr>";
		echo "<tr><td ><font size='2'><B>DIRECCION:</td><td><font size='2'>".$row[3]."</td>
		      <td COLSPAN=2><B><font size='2'>OCUPACION:</td><td COLSPAN=2><font size='2'>".$ofi[0]."</td></tr>";
		echo "<tr><td ><font size='2'><B>TELEFONOS:</td><td COLSPAN=5><font size='2'>".$row[5]."</td></tr>";
		echo "<tr><td ><font size='2'><B>ACUDIENTE:</td><td><font size='2'>".$row[6]."</td>
		      <td COLSPAN=2><B><font size='2'>PARENTESCO:</td><td COLSPAN=2><font size='2'>".$row[7]."</td></tr>";
		echo "<tr><td ><font size='2'><B>DIRECCION ACUDIENTE:</td><td><font size='2'>".$row[8]."</td>
		      <td COLSPAN=2><B><font size='2'>TEL. ACUDIENTE:</td><td COLSPAN=2><font size='2'>".$row[9]."</td></tr>";
		echo "<tr><td ><font size='2'><B>ACOMPAÑANTE:</td><td><font size='2'>".$row[6]."</td>
		      <td COLSPAN=2><B><font size='2'>PARENTESCO DEL ACOMPAÑANTE:</td><td COLSPAN=2><font size='2'>".$row[7]."</td></tr>";
		echo "<tr><td ><font size='2'><B>MOTIVO DE CONSULTA:</td><td COLSPAN=5><font size='2'>".$cons[0]."</td></tr>";
		echo "<tr><td COLSPAN=6 align=center bgcolor=#969696><font size='2'><font size='2'><B>ANTECEDENTES MEDICOS PERSONALES</td></tr>";
		
		//PARA PINTAR LOS ANTECEDENTES MEDICOS PERSONALES
		$n[5]= "Tratamiento Medico";
		$n[6]= "Observacion";
		$n[7]= "Hospitalizado";
		$n[8]= "Observacion";
		$n[9]= "Cirugia";
		$n[10]= "Observacion";
		$n[11]= "Fuma";
		$n[12]= "Observacion";
		$n[13]= "Reacciones Alergicas";
		$n[14]= "Observacion";
		$n[15]= "Fiebre Reumatica";
		$n[16]= "Observacion";
		$n[17]= "Hipertension";
		$n[18]= "Observacion";
		$n[19]= "Hipotension";
		$n[20]= "Observacion";
		$n[21]= "Enfermedades Cardiovasculares";
		$n[22]= "Observacion";
		$n[23]= "Enfermedades Respiratorias";
		$n[24]= "Observacion";
		$n[25]= "Trastornos Hemorragicos";
		$n[26]= "Observacion";
		$n[27]= "Anemia";
		$n[28]= "Observacion";
		$n[29]= "Convulsiones o Epilepsia";
		$n[30]= "Observacion";
		$n[31]= "Enfermedades de la Tiroides";
		$n[32]= "Observacion";
		$n[33]= "Enfermedades Renales";
		$n[34]= "Observacion";
		$n[35]= "Enfermedades Hepaticas";
		$n[36]= "Observacion";
		$n[37]= "Enfermedades Infectocontagiosas";
		$n[38]= "Observacion";
		$n[39]= "Trastornos Inmunologicos";
		$n[40]= "Observacion";
		$n[41]= "Gastritis";
		$n[42]= "Observacion";
		$n[43]= "Diabetes";
		$n[44]= "Observacion";
		$n[45]= "Tumores";
		$n[46]= "Observacion";
		$n[47]= "Artritis";
		$n[48]= "Observacion";
		$n[49]= "Otras Enfermedades";
		$n[50]= "Observacion";
		$n[51]= "Esta Tomando Medicamentos";
		$n[52]= "Observacion";
		$n[53]= "Esta en Embarazo";
		$n[54]= "Observacion";
		
		$query="select *   
			      from ".$empresa."_000128 
			     where Hiscli = '".$exp[5]."'";
		$err = mysql_query($query,$conex);		
		$row = mysql_fetch_row($err);
			
		for ($j=5;$j<55;$j++)
			{
					if ($row[$j]=='on')
					{
						$row[$j]="SI";
						
						if ($row[$j+1]=='NO APLICA')
						$row[$j+1]="NINGUNA";
							echo "<tr><td COLSPAN=2 ><font size='2'><B>".$n[$j]."</td><td COLSPAN=4><font size='2'>".$row[$j]."</td></tr>";
							echo "<tr bgcolor=#DDDDDD><td COLSPAN=2 ><font size='2'><B>".$n[$j+1]."</td><td COLSPAN=4><font size='2'>".$row[$j+1]."</td></tr>";
					}else if ($row[$j]=='off')
					
						{
						$row[$j]="NO";
						echo "<tr><td COLSPAN=2 ><B><font size='2'>".$n[$j]."</td><td COLSPAN=4><font size='2'>".$row[$j]."</td></tr>";
						}
					
				}
		
			echo "<tr><td COLSPAN=6 align=center bgcolor=#969696><font size='2'><B>ANTECEDENTES MEDICOS FAMILIARES</td></tr>";
			//PARA PINTAR LOS ANTECEDENTES MEDICOS FAMILIARES	
			echo "<tr><td COLSPAN=2 ><font size='2'><B>Observacion</td><td COLSPAN=4><font size='2'>".$row[55]."</td></tr>";
			echo "<tr><td COLSPAN=6 align=center bgcolor=#969696><font size='2'><B>ANTECEDENTES MEDICOS ODONTOLOGICOS</td></tr>";	
			
			//PARA PINTAR LOS ANTECEDENTES MEDICOS ODONTOLOGICOS
			$n[56]= "Operatorio";
			$n[57]= "Observacion";
			$n[58]= "Endodoncia";
			$n[59]= "Observacion";
			$n[60]= "Periodoncia";
			$n[61]= "Observacion";
			$n[62]= "Cirugia odontonlogica";
			$n[63]= "Observacion";
			$n[64]= "Protesis";
			$n[65]= "Observacion";
			$n[66]= "Ortodoncia";
			$n[67]= "Observacion";
			$n[68]= "Urgencias Odontologicas";
			$n[69]= "Observacion";
			$n[70]= "Otros";
			$n[71]= "Observacion";
			$n[72]= "Anestesia Local";
			$n[73]= "Observacion";
			$n[74]= "Blanqueamiento";
			$n[75]= "Observacion";
			

			for ($j=56;$j<76;$j++)
			{
					if ($row[$j]=='on')
					{
					$row[$j]="SI";
					
					if ($row[$j+1]=='NO APLICA')
					$row[$j+1]="NINGUNA";	
						echo "<tr><td COLSPAN=2 ><B><font size='2'>".$n[$j]."</td><td COLSPAN=4><font size='2'>".$row[$j]."</td></tr>";
						echo "<tr bgcolor=#DDDDDD><td COLSPAN=2 ><B><font size='2'>".$n[$j+1]."</td><td COLSPAN=4><font size='2'>".$row[$j+1]."</td></tr>";
						
					}else if ($row[$j]=='off')
					
						{
						$row[$j]="NO";
						echo "<tr><td COLSPAN=2 ><B><font size='2'>".$n[$j]."</td><td COLSPAN=4><font size='2'>".$row[$j]."</td></tr>";
						}
					
				}
				
			echo "<tr><td COLSPAN=6 align=center bgcolor=#969696><font size='2'><B>EXAMEN ESTOMATOLOGICO</td></tr>";
		
			//PARA PINTAR EL EXAMEN ESTOMATOLOGICO
			$n[76]= "Labios";
			$n[77]= "Observacion";
			$n[78]= "Carrillos";
			$n[79]= "Observacion";
			$n[80]= "Frenillos";
			$n[81]= "Observacion";
			$n[82]= "Paladar Blando";
			$n[83]= "Observacion";
			$n[84]= "Paladar Duro";
			$n[85]= "Observacion";
			$n[86]= "Senos Paranasales";
			$n[87]= "Observacion";
			$n[88]= "Orofaringe";
			$n[89]= "Observacion";
			$n[90]= "Piso Boca";
			$n[91]= "Observacion";
			$n[92]= "Glandulas Salivales";
			$n[93]= "Observacion";
			$n[94]= "Lengua";
			$n[95]= "Observacion";
			$n[96]= "Atm";
			$n[97]= "Observacion";
			
			
			
			for ($j=76;$j<98;$j++)
			{
					if ($row[$j]=='on')
					{
					$row[$j]="ANORMAL";	
					
					if ($row[$j+1]=='NO APLICA')
					$row[$j+1]="NINGUNA";	
						echo "<tr><td COLSPAN=2 ><font size='2'><B>".$n[$j]."</td><td COLSPAN=4><font size='2'>".$row[$j]."</td></tr>";
						echo "<tr bgcolor=#DDDDDD><td COLSPAN=2 ><B><font size='2'>".$n[$j+1]."</td><td COLSPAN=4><font size='2'>".$row[$j+1]."</td></tr>";
					
					}else if ($row[$j]=='off')
					
						{
						$row[$j]="NORMAL";
						echo "<tr><td COLSPAN=2 ><B><font size='2'>".$n[$j]."</td><td COLSPAN=4><font size='2'>".$row[$j]."</td></tr>";
						}
					
				}
			
			echo "</table>";
			
	//////ODONTOGRAMA///////////////////////////////////////////////////////////////////////////////////////////////////////		
			$paciente=$exp[0];
			include_once("soe/actividades_inc.php");
	
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	
	// para el diagnostico
	$query="select Mdxpro, Mdxdes , Mdxobs  
			  from ".$empresa."_000132  
			 where Mdxhis = '".$exp[5]."' 
			  and Mdxing = '".$ing."'  
			  and Mdxcon = '02'";
	$err = mysql_query($query,$conex);		
	$num = mysql_num_rows($err);
	echo "<table border=1 align=center  width='750'>";
	echo "<tr ><td COLSPAN=3 align=center><B><font size='3'><B>DIAGNOSTICO</td></tr>";
	for ($i=0;$i<$num;$i++)
				{
					$diag = mysql_fetch_array($err);
					echo "<tr><td font size='1'>".$diag[0]."</td><td font size='1'>".$diag[1]."</td><td font size='1'>".$diag[2]."</td></tr>";
					
					}
	echo "</table>";			
	echo "<br>";
	// para el presupuesto
	//conceptos
	$query="select Ptocpt, Ptoncp   
			  from ".$empresa."_000131  
			 where Ptohis = '".$exp[5]."' 
			  and Ptoing = '".$ing."'
			  group by Ptocpt";
	$err = mysql_query($query,$conex);		
	$num = mysql_num_rows($err);
	echo "<table border=1 align=center  width='750'>";
	echo "<tr bgcolor=#DDDDDD><td COLSPAN=3 align=center><B><font size='3'><B>PRESUPUESTO</td></tr>";
	echo "<tr ><td align=center><B><font size='2'>&nbsp</td><td align=center><B><font size='2'><B>PRESUPUESTADO</td><td align=center><B><font size='2'><B>FACTURADO</td></tr>";
	$totpr=0;
	$totfc=0;
	for ($i=0;$i<$num;$i++)
				{
					$diag = mysql_fetch_array($err);
					//codigos
					$query="select Ptopro, Ptonpr, (Ptocan*Ptoval), (Ptocan*Ptoval)    
				      		  from ".$empresa."_000131  
			 				 where Ptohis = '".$exp[5]."' 
			  				   and Ptoing = '".$ing."'
			  				   and Ptocpt = '".$diag[0]."'";
					$err1 = mysql_query($query,$conex);		
					$numc = mysql_num_rows($err1);
					//echo mysql_errno() ."=". mysql_error();
					echo "<tr bgcolor=#DDDDDD><td COLSPAN=1> <font size='2'><B>".$diag[0]."-".$diag[1]."</td><td COLSPAN=2>&nbsp</td></tr>";
					for ($j=0;$j<$numc;$j++)
					{
						$cod = mysql_fetch_array($err1);
						echo "<tr><td font size='1'><font size='2'>".$cod[0]."-".$cod[1]."</td><td font size='1' align=right>".number_format($cod[2],0,'.',',')."</td><td font size='1' align=right>".number_format($cod[3],0,'.',',')."</td></tr>";
						$totpr=$totpr+$cod[2];
						$totfc=$totfc+$cod[3];
					}
					
					
				}
				
	echo "<tr bgcolor=#DDDDDD><td align=center><B><font size='2'>TOTAL</td><td align=right><B><B>".number_format($totpr,0,'.',',')."</td><td align=right ><B><B>".number_format($totfc,0,'.',',')."</td></tr>";			
	echo "</table>";
	
	
	
		}

	


}
?>	