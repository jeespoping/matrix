<html>
<head>
<title>Reporte general de contraremision</title>
</head>
<font face='arial'>

<BODY TEXT="#000000" >
<BODY size='1'>

<?php
include_once("conex.php");

/********************************************************
*     REPORTE GENERAL DE CONTRAREMISION					*
*														*
*********************************************************/

//==================================================================================================================================
//PROGRAMA						:Reporte de general de contraremision	 
//AUTOR							:Juan David Londoño
//FECHA CREACION				:SEPTIEMBRE 2006
//FECHA ULTIMA ACTUALIZACION 	:27 de Septiembre de 2006
//DESCRIPCION					:
//								 
//==================================================================================================================================
$wactualiz="Ver. 2006-09-25";


session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$empresa='urgen';
	
	

	

	echo "<form name=inf_contraremision action='' method=post>";
	
	
	

if(!isset($ent))
	
{

		echo "<table border=0 align=center >";
		echo "<tr><td align=center ><img src='/matrix/images/medical/root/clinica.jpg' ></td></tr>";
		echo "<tr><td align=center><font size='5' color=#000000><br>REPORTE GENERAL DE CONTRAREMISION</td></tr>";
		echo "</table>";
		echo "<br>";
		echo "<table border=(1) bgcolor=#CCFFFF  align=center>";

			echo "<tr><td>FECHA INICIAL:</td><td colspan=1 align=center><input type='TEXT' name='fec1' size=10 maxlength=10 ></td></tr>";
			echo "<tr><td>FECHA FINAL:</td><td colspan=1 align=center><input type='TEXT' name='fec2' size=10 maxlength=10 ></td></tr>";
			
			$query="SELECT DISTINCT Insrec
					FROM ".$empresa."_000005";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);	
			
			echo "<tr><td>INSTITUCION QUE RECIBE:</td><td><select name='insrec'>";
			if($num>0)
			{
				echo "<option>99999-TODAS</option>";
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					if($row[0]==$insrec)
					echo "<option selected>".$row[0]."</option>";
					else
					echo "<option>".$row[0]."</option>";
				}
			}
			echo "</select></td></tr>";
			 
			
			$query="SELECT DISTINCT Ent
					FROM ".$empresa."_000005";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			
			echo "<tr><td>ENTIDAD:</td><td><select name='ent'>";
			echo "<option>TODAS</option>";
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					if($row[0]==$ent)
					echo "<option selected>".$row[0]."</option>";
					else
					echo "<option>".$row[0]."</option>";
				}
			}
			echo "</select></td></tr>";

		echo"<tr><td colspan=2 align=center><input type='submit' value='ACEPTAR'></td></tr></form>";
		echo "</table>";
		
	}else{
////////////////////////////////////////////////////////////////////////////////////aca comienza la impresion
		
		if ($insrec=='99999-TODAS' and $ent!='TODAS'){
			
			$query="SELECT *
					FROM ".$empresa."_000005
					WHERE Fecha BETWEEN '".$fec1."' AND '".$fec2."'
					AND Ent='".$ent."'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
		
			
			}else if ($insrec!='99999-TODAS' and $ent=='TODAS')
			{
				$query="SELECT *
						FROM ".$empresa."_000005
						WHERE Fecha BETWEEN '".$fec1."' AND '".$fec2."'
						AND Insrec='".$insrec."'";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
			
				}else if ($insrec=='99999-TODAS' and $ent=='TODAS')
				{
				
					$query="SELECT *
							FROM ".$empresa."_000005
							WHERE Fecha BETWEEN '".$fec1."' AND '".$fec2."'";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					
					}else{
					 
						$query="SELECT *
								FROM ".$empresa."_000005
								WHERE Fecha BETWEEN '".$fec1."' AND '".$fec2."'
								AND Insrec='".$insrec."'
								AND Ent='".$ent."'";
						$err = mysql_query($query,$conex);
						$num = mysql_num_rows($err);
					}
		
			echo "<center><table border=0 WIDTH=500>";
		    echo "<tr><td align=center ><img src='/matrix/images/medical/root/clinica.jpg' ></td></tr>";
		    echo "<tr ><td align=center colspan=1 bgcolor=#FFFFFF><font text color=#000000><b>Informe general de contraremisión</b></font><br><font size=1 text color=#000000><b>".$wactualiz."</b></font></td></tr>";
			echo "</table>";
			echo "<br>";
			echo "<center><table border=1 WIDTH=500 bgcolor=#CCFFFF>";
		    $inst=explode('-',$insrec);
		    echo "<tr><td align=center colspan=1 bgcolor=#CCFFFF><font text color=#000000 ><b>Desde:</b> <i>".$fec1."</i><b> Hasta:</b><i>".$fec2."</i></font></td></tr>";
		    echo "<tr><td align=left colspan=1 bgcolor=#CCFFFF><font text color=#000000 ><b>Institucion:</b> <i>".$inst[1]."</i></font></td></tr>";
			echo "<tr><td align=left colspan=1 bgcolor=#CCFFFF><font text color=#000000><b>Entidad:</b> <i>".$ent."</i></font></td></tr>";
			echo "<tr><td align=left colspan=1 bgcolor=#CCFFFF><font text color=#000000><b>Numero total de contraremisiones:</b> <i>".$num."</i></font></td>";
			echo "</table>";
			
		
	}
}
?>	