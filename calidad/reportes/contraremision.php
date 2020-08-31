<html>
<head>
<title>Reporte de contraremision</title>
</head>
<font face='arial'>

<BODY TEXT="#000000" >
<BODY size='1'>

<?php
include_once("conex.php");

/********************************************************
*     REPORTE DE CONTRAREMISION							*
*														*
*********************************************************/

//==================================================================================================================================
//PROGRAMA						:Reporte de contraremision	 
//AUTOR							:Juan David Londoño
//FECHA CREACION				:SEPTIEMBRE 2006
//FECHA ULTIMA ACTUALIZACION 	:14 de Noviembre de 2006
//DESCRIPCION					:Este es un reporte que imprime la contraremision de los pacientes
//
//2006-11-14: Se cambia el orden de la HC y el Num de Ing.
//			  Se agrega el campo de persona que diligencia la contrarermision.
//			  El medico se pone que sea de manera automatica y no diligenciado a mano								 
//==================================================================================================================================
$wactualiz="Ver. 2006-09-25";


session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$empresa='urgen';
	
	

	

	echo "<form name=contraremision action='' method=post>";

if(!isset($cod))
	{

		
		echo "<table border=0  align=center >";
		echo "<tr><td align=center ><img src='/matrix/images/medical/root/clinica.jpg' ></td></tr>";
		echo "<tr><td align=center ><font size='5' color=#000000 ><br>CONTRAREMISION</td></tr>";
		echo "</table>";
		echo "<br>";
		echo "<table border=1 bgcolor=#CCFFFF align=center>";

		if(!isset($numhis)and (!isset($pac)))
		{
			echo "<tr><td>NUMERO DE HISTORIA:</td><td colspan=1 align=center><input type='TEXT' name='numhis'size=15 maxlength=15 ></td></tr>";

		}if(isset($numhis))

		{
			$query="SELECT DISTINCT Numcont
				FROM ".$empresa."_000005
				WHERE Hiscli='".$numhis."'";

			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			
			echo "<tr><td>HISTORIA CLINICA:</td><td><b><i>$numhis</td></tr>";
			echo "<input type='hidden' name='numhis' value='".$numhis."'>";
			 
			echo "<tr><td>NUMERO DE CONTRAREMISION:</td><td><select name='cod'>";
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					if($row[0]==$cod)
					echo "<option selected>".$row[0]."</option>";
					else
					echo "<option>".$row[0]."</option>";
				}
			}
			echo "</select></td></tr>";

		}
		if (isset($cod))
		echo "<input type='hidden' name='cod' value='".$cod."'>";
		
		echo"<tr><td colspan=2 align=center><input type='submit' value='ACEPTAR'></td></tr></form>";
		echo "</table>";
		
	}else{
		
		$query="SELECT *
				FROM ".$empresa."_000005
				WHERE Hiscli='".$numhis."'
				AND Numcont='".$cod."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);	
		$row = mysql_fetch_array($err);
		
			echo "<center><table border=1 WIDTH=760>";
		    echo "<tr><td align=center  rowspan=2 ><img src='/matrix/images/medical/root/clinica.jpg' ></td></tr>";
		    echo "<tr ><td align=center colspan=1 bgcolor=#FFFFFF><font text color=#000000><b>Reporte de Contraremisión</b></font><br><font size=1 text color=#000000><b>".$wactualiz."</b></font></td></tr>";
			echo "<tr><td align=left colspan=1 bgcolor=#FFFFFF><font text color=#000000 ><b>Historia clínica:</b> <i>".$numhis."-".$row[4]."</i></font></td>";
			echo "<td align=left colspan=1 bgcolor=#FFFFFF><font text color=#000000><b>Documento de identificación:</b> <i>".$row[7]."</i></font></td>";
			echo "<tr><td align=left colspan=2 bgcolor=#FFFFFF><font text color=#000000><b>Nombre completo:</b> <i>".$row[6]."</i></font></td></tr>";
			echo "</table>";
			echo "<br>";
			echo "<center><table border=1 WIDTH=760>";
		    echo "<tr><td><b>Fecha de la Contraremisión: </b><br>".$row[9]."</td><td colspan=2><b>Hora: </b><br>".$row[10]."</td></tr>";
		    echo "<tr><td><b>Institución que contraremite: </b><br>CLINICA LAS AMERICAS</td><td><b>Nivel: </b><br>3</td><td><b>Municipio: </b><br>MEDELLIN</td></tr>";
		    echo "<td align=left colspan=3 bgcolor=#FFFFFF><font text color=#000000><b>Entidad:</b> ".$row[8]."</font></td></tr>";
		    echo "<tr><td colspan=3><b>Persona que autoriza en la Entidad: </b> ".$row[11]."</td></tr>";
		    echo "<tr><td colspan=3><b>Código de autorizacion de la Entidad: </b> ".$row[12]."</td></tr>";
		    
		    $key = substr($user,2,strlen($user));
		    /////////////////////////////////////////////////query para traer el nombre de la persona que tramita
		    $query="SELECT Descripcion
					FROM usuarios
					WHERE Codigo='".$key."'";
			$err = mysql_query($query,$conex);	
			$tra = mysql_fetch_array($err);
		    echo "<tr><td colspan=3><b>Tramitado por: </b> ".$tra[0]."</td></tr>";
		    echo "</table>";
		    echo "<br>";
		    echo "<center><table border=1 WIDTH=760>";
		   		 $entrec=explode('-',$row[13]);
		   		 $niv=explode('-',$row[14]);
		   		 $mun=explode('-',$row[15]);
		    echo "<tr><td><b>Institución que recibe: </b><br>".$entrec[1]."</td><td><b>Nivel: </b><br>".$niv[1]."</td><td><b>Municipio: </b><br>".$mun[1]."</td></tr>";
		    echo "<tr><td colspan=2><b>Persona que recibe: </b> ".$row[16]."</td><td colspan=1><b>Cargo: </b> ".$row[17]."</td></tr>";
		    echo "</table>";
		    echo "<br>";
		    echo "<center><table border=1 WIDTH=760>";
		    echo "<tr><td><b>Procedimiento o tratamiento realizado:<BR><BR><BR><BR><BR></tr>";
		    echo "</table>";
		    echo "<br>";
		    echo "<center><table border=1 WIDTH=760>";
		    echo "<tr><td><b>Motivo por el cual se contraremite:<BR><BR><BR><BR><BR></td></tr>";
		    echo "</table>";
		    echo "<br>";
		    echo "<center><table border=1 WIDTH=760>";
		    echo "<tr><td><b>Condición clínica del paciente:<BR></td><td>PA:_______/_______</td><td>FC:_______x'</td><td>Tº:_______</td><td>SaO2:_______</td><td>FR:_______x'</td></tr>
		    	  <tr><td colspan=6><b><BR><BR><BR><BR><BR><BR><BR><BR></td></tr>";
		    echo "</table>";
		    echo "<br>";
		    echo "<center><table border=1 WIDTH=760>";
		    echo "<tr><td><b>Médico que contraremite:</b> ".$row[18]."</td><td><b>Registro Medico:<BR><BR><BR></td><td><b>Sello:<BR><BR><BR></td></tr>";
		    echo "</table>";
		
		
	}
}
?>	