<html>
<head>
  	<title>Estadisticas de pacientes x entidad y procedimientos x entidad</title>
</head>
<body  BGCOLOR="FFFFFF">
<font face='arial'>
<BODY TEXT="#000066">
<?php
include_once("conex.php");
//==================================================================================================================================
//PROGRAMA						:Estadisticas de pacientes x entidad y procedimientos x entidad 
//AUTOR							:Juan David Londoño
//FECHA CREACION				:2008-02-27
//FECHA ULTIMA ACTUALIZACION 	:
$wactualiz="2008-02-27";
//==================================================================================================================================
//ACTUALIZACIONES
//==================================================================================================================================
// xxxx						 
//==================================================================================================================================
// xxxx
//==================================================================================================================================



session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	
	

	


	echo "<form name=rep_proent action='' method=post>";
	
	if (!isset($wfec_f))
	   {
	   	$wfecha=date("Y-m-d");// esta es la fecha actual
        echo "<br><br><br>";
		echo "<center><table border=0>";
		//echo "<tr><td align=center colspan=2><font size=5><img src='/matrix/images/medical/pos/citas_".$wbasedato.".png' WIDTH=340 HEIGHT=100></font></td></tr>";
		echo "<tr><td><br></td></tr>";
		echo "<tr><td align=center colspan=2><font size=5>Estadisticas de Pacientes x Entidad y Procedimientos x Entidad</font></td></tr>";
		echo "<tr><td>&nbsp</td></tr>";
		echo "<tr><td bgcolor=#dddddd align=center><b>PACIENTES X ENTIDAD</b></td><td bgcolor=#dddddd align=left><input type='radio' name='reporte' value='1' onclick='enter()'></td></tr>";
		echo "<tr><td bgcolor=#dddddd align=center><b>PROCEDIMIENTOS X ENTIDAD</b></td><td bgcolor=#dddddd align=left><input type='radio' name='reporte' value='2' onclick='enter()'></td></tr>";
		echo "<tr><td bgcolor=#dddddd align=center><b>Fecha Inicial (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='wfec_i' value=".$wfecha." SIZE=10></td>";
	    echo "<td bgcolor=#dddddd align=center><b>Fecha Final (AAAA-MM-DD): </b><INPUT TYPE='text' NAME='wfec_f' value=".$wfecha." SIZE=10></td>";
	    echo "<tr><td bgcolor=#cccccc  colspan=2 align=center><input type='submit' value='ENTER'></td></tr></table>";
	   }
	else
	   {
	   	
	   switch ($reporte)
		{
			
			case 1: // este caso es para el reporte de PACIENTES X ENTIDAD
			{
				
				// trae las empresas
				
				$query= " SELECT Nit, Descripcion
						    FROM ".$wbasedato."_000002
				           WHERE Activo='A'
				           ORDER BY 1";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
								
				//echo mysql_errno() ."=". mysql_error();
				echo "<center><table border=0>";// este es el encabezado del resultado
			 	echo "<tr><td align=center colspan=7><font size=5>ESTADISTICAS</font></td></tr>";
			    echo "<tr><td align=center colspan=7>Desde: <b>".$wfec_i."</b> hasta <b>".$wfec_f."</b></td></tr>";
			    echo "<tr><td>&nbsp</td></tr>";
			    echo "<tr><td align=center colspan=7><font size=3>REPORTE DE PACIENTES X ENTIDAD</font></td></tr>";
	  	 	    echo "<tr><td>&nbsp</td></tr></table>";
	  	        echo "<table border=1>";
				echo "<tr bgcolor=#dddddd><td align=center><font size=2><b>EMPRESA</b></td><td align=center><font size=2><b>PACIENTES</b></td></tr>";
			 	
			 	$totan=0;
			 	for ($i=1;$i<=$num;$i++)
			      {
			      	
			      	// colores de la grilla
			      	if (is_int ($i/2))
	                $wcf="DDDDDD";
	                else
	                $wcf="CCFFFF";	
			      
				        $row=mysql_fetch_row($err);
				        $arr[$i]['nit']=$row[0];
				        $arr[$i]['emp']=$row[1];
				        
				        // citas x empresa
				        $query= " SELECT *
								    FROM ".$wbasedato."_000009
						           WHERE Fecha between '".$wfec_i."' and '".$wfec_f."'
						             AND Nit_res = '".$arr[$i]['nit']."'
						           ORDER BY 1";
						$err_emp = mysql_query($query,$conex);
						$num_emp = mysql_num_rows($err_emp);
															
						$totan=$totan+$num_emp;
				        echo "<tr bgcolor=".$wcf." border=1><td >".$arr[$i]['nit']."-". $arr[$i]['emp']."</td><td align=right>".$num_emp."</td></tr>";
			       }
			      echo "<tr bgcolor=#dddddd><td align=left ><font size=2><b>NUMERO TOTAL DE PACIENTES</b></td><td align=right><b>".$totan."</td></tr>";
				  echo "<tr bgcolor=DDDDDD border=1><td colspan=4>&nbsp</td></tr>"; 
				  echo "<tr bgcolor=#dddddd><td align=left ><font size=2><b>NUMERO TOTAL DE EMPRESAS</b></td><td align=center ><font size=2><b>".$num."</b></td></tr>";
			 	break;
			}
			case 2: // este caso es para el reporte de PROCEDIMIENTOS X ENTIDAD
			{
				
				// trae las empresas
				$query= " SELECT distinct Nit_res, Descripcion
						    FROM ".$wbasedato."_000009, ".$wbasedato."_000002
				           WHERE Fecha between '".$wfec_i."' and '".$wfec_f."' 
				           	 AND Nit_res=Nit
				           ORDER BY 1";
				$err_ent = mysql_query($query,$conex);
				$num_ent = mysql_num_rows($err_ent);
// 				echo  $query;
// 				echo  $err_ent;
				//echo mysql_errno() ."=". mysql_error();
				
				echo "<center><table border=0>";// este es el encabezado del resultado
			 	echo "<tr><td align=center colspan=7><font size=5>ESTADISTICAS</font></td></tr>";
			    echo "<tr><td align=center colspan=7>Desde: <b>".$wfec_i."</b> hasta <b>".$wfec_f."</b></td></tr>";
			    echo "<tr><td>&nbsp</td></tr>";
			    echo "<tr><td align=center colspan=7><font size=3>REPORTE DE PROCEDIMIENTOS X ENTIDAD</font></td></tr>";
	  	 	    echo "<tr><td>&nbsp</td></tr></table>";
	  	        echo "<table border=0>";
				$totot=0;
			 	for ($i=1;$i<=$num_ent;$i++)
			      {
			      	$row_ent=mysql_fetch_row($err_ent);
			        $arr[$i]['nit']=$row_ent[0];
			        $arr[$i]['emp']=$row_ent[1];
			        echo "<tr><td colspan=2>&nbsp</td></tr>";
			      	echo "<tr bgcolor=#dddddd><td align=center colspan=2><b>".$arr[$i]['nit']."-".$arr[$i]['emp']."</b></td></tr>";
			 		// trae los procedimientos
					$query= " SELECT Cod_exa, count(*), Descripcion
							    FROM ".$wbasedato."_000009,".$wbasedato."_000011
					           WHERE Fecha between '".$wfec_i."' and '".$wfec_f."'
					             AND Nit_res = '".$arr[$i]['nit']."'
					             AND Codigo=Cod_exa
					           group BY 1";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					//echo mysql_errno() ."=". mysql_error();
					/*if (isset ($totpro))
					{
						$totot=$totot+$totpro;
					}
					*/
					
					$totpro=0;
					for ($j=1;$j<=$num;$j++)
			      	{
						$row=mysql_fetch_row($err);
						echo "<tr bgcolor=CCFFFF border=1><td >".$row[0]."-".$row[2]."</td><td align=right>".$row[1]."</td></tr>";
						$totpro=$totpro+$row[1];
			      	}
			      	$totot=$totot+$totpro;
					echo "<tr bgcolor=#dddddd><td align=left><font size=2><b>TOTAL</b></td><td align=right><font size=2><b>".$totpro."</b></td></tr>";
			 		}
			 	  
			 	  echo "<tr><td>&nbsp</td></tr>";		
			      echo "<tr bgcolor=#dddddd><td align=left ><font size=2><b>NUMERO TOTAL DE PROCEDIMIENTOS</b></td><td align=right><b>".$totot."</td></tr>";
				 break;
			
			}	  
		}
	  }
}
?>
</body>
</html>