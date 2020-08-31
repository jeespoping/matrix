<html>
<head>
<title>Reporte para hacer mercadeo</title>
</head>

<?php
include_once("conex.php");
/*
 * Created on 20/02/2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /********************************************************
*     REPORTE PARA MERCADEO								*
*														*
*********************************************************/

//==================================================================================================================================
//PROGRAMA						:Reporte para mercadeo 
//AUTOR							:Juan David Londoño
//FECHA CREACION				:FEBRERO 2007
//FECHA ULTIMA ACTUALIZACION 	:
//DESCRIPCION					:
//								 
//==================================================================================================================================
$wactualiz="Ver. 2007-02-19";


session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$empresa='oir';
	
	

	


	echo "<form name=Rep_mercadeo action='' method=post>";
	
	echo "<center><table border=1>";
	
	if (!isset($texam))
	{	
		 //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////RANGO DE FECHAS
	    
	    if (isset($fec1))
	    $fec1=$fec1;
	    else
	    $fec1='';
	
	    if (isset($fec2))
	    $fec2=$fec2;
	    else
	    $fec2='';
	    
	    echo "<tr><td align=center bgcolor=#DDDDDD colspan=3><b><font text color=#003366 size=3> Fecha Inicial&nbsp(AAAA-MM-DD):</font></b><INPUT TYPE='text' NAME='fec1' VALUE='".$fec1."'>
			  <b><font text color=#003366 size=3>Fecha Final&nbsp(AAAA-MM-DD):<INPUT TYPE='text' NAME='fec2' VALUE='".$fec2."'></td></tr>";
				 
	        
		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////TIPO DE EXAMEN
		echo "<tr><td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=3> Tipo de examen: <br></font></b><select name='texam'>";
		
		echo "<option>01-AUDIOMETRIA TONAL</option>";
		echo "<option>02-BEPADI</option>";
		echo "<option>03-AUDIOMETRIA VOCAL DIAGNOSTICA</option>";
		echo "<option>04-AUDIOMETRIA VOCAL GENERAL</option>";
		echo "<option>05-INMITANCIA ACUSTICA</option>";
		echo "<option>06-FUNCION TUBARICA</option>";
		echo "<option>07-ACUFENOMETRIA</option>";
	 	echo "</select></td>";
		
		echo "<tr><td align=center bgcolor=#cccccc colspan=3><input type='submit' value='OK'></td>";                            //submit
        echo "</tr>";
        echo "</table>";
        
	}else
		{
			if ($texam=='01-AUDIOMETRIA TONAL')
			{
				$table= $empresa."_000002"; 
			}
			
			if ($texam=='02-BEPADI')
			{
				$table= $empresa."_000004"; 
			}
			
			if ($texam=='03-AUDIOMETRIA VOCAL DIAGNOSTICA')
			{
				$table= $empresa."_000007"; 
			}
			
			if ($texam=='04-AUDIOMETRIA VOCAL GENERAL')
			{
				$table= $empresa."_000008"; 
			}
			
			if ($texam=='05-INMITANCIA ACUSTICA')
			{
				$table= $empresa."_000009"; 
			}
			
			if ($texam=='06-FUNCION TUBARICA')
			{
				$table= $empresa."_000010"; 
			}
			
			if ($texam=='07-ACUFENOMETRIA')
			{
				$table= $empresa."_000013"; 
			}
			
			$query =  " SELECT Nombre, Fecha_Examen, Telefono
				        FROM ".$empresa."_000001, $table
				        WHERE Fecha_Examen between '".$fec1."' and '".$fec2."'
				        AND Nombre = (mid(paciente,1,instr(paciente,'-')-1))
				        ORDER by Fecha_Examen";
				        $err = mysql_query($query,$conex);
				        //echo mysql_errno() ."=". mysql_error();
				        $num = mysql_num_rows($err);
				  
				  
				echo "<br>";        
				echo "<center><table border>";
			    echo "<tr><td bgcolor=#ffcc66 colspan=3 align=center><b>DETALLE DE LOS PACIENTES (".$texam.")</b></td></tr>";
			    echo "<tr><td bgcolor=#ffcc66 colspan=1 align=center><b>Nombre</b></td><td bgcolor=#ffcc66 colspan=1 align=center><b>Fecha del Examen</b></td><td bgcolor=#ffcc66 colspan=1 align=center><b>Telefono</b></td></tr>";    
			    
			    for ($i=1;$i<=$num;$i++)
				{
					 $row = mysql_fetch_array($err);
					     		
					 if (is_int ($i/2))
			         $wcf="DDDDDD";
			         else
			         $wcf="CCFFFF";
			         
			         echo "<tr  bgcolor=".$wcf."><td align=center>".$row[0]."</td><td align=center>".$row[1]."</td><td align=center>".$row[2]."</td></tr>";    
				}
				
				echo "<tr><td bgcolor=#ffcc66 colspan=2 align=center><b>Numero total de pacientes:</b></td><td bgcolor=#ffcc66 colspan=1 align=center><b>".$num."</b></td></tr>";
				      	        
		}
} 
?>






















