<?php
include_once("conex.php");
/**********************************************************************************************************************  
[DOC]
	   PROGRAMA : HCE.php
	   Fecha de Liberacion : 2009-07-09
	   Autor : Direccion de Informatica PMLA
	   Version Inicial : 2009-07-09
	   Version actual  : 2011-04-04
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface grafica que permite registrar los datos clinicos
	   de un paciente, en distintos formularios segun la estructura logica definida en la metadata de la HCE.
	   
	   REGISTRO DE MODIFICACIONES :
	   .2011-04-04
			Se modifico el programa para que mostrara todos los mensajes de error arriba y abajo, adicionalmente en javascript
			se modifico la pregunta para mostrar o no el icono de grabar, para que solo lo desaparezca en el momento en el
			que el programa grabe con el proposito de impedir la doble grabacion.
			
	   .2011-03-18
			Se valida que el numero de Historia e Ingreso existan o no esten en nulo.
			
	   .2011-02-14
			Ultimo release Beta.
			
	   .2011-02-07
			Ultimo release Beta.
	   		
	   .2009-07-09
	   		Release de Version Beta.
[*DOC]   		
***********************************************************************************************************************/
function bi($d,$n,$k)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			$val=strncmp(strtoupper($k),strtoupper($d[$lm]),20);
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls]))
					return $ls;
				else
					return -1;
	}
	elseif($d[0] == $k)
			return 0;
		else
			return -1;
}
function buscarID($a,$i)
{
	for ($w=0;$w<count($a);$w++)
	{
		if(substr($a[$w],1) == $i)
			return true;
	}
	return false;
}
function buscartitulo($t,$ord,$n)
{
	for ($w=0;$w<$n;$w++)
	{
		if($t[$w][0] < $ord and ($ord - $t[$w][0]) == 1)
			return $w;
	}
	return -1;
}

function calcularspan($s)
{
	if(strlen($s) < 51)
		return 1;
	else
		return 8;
}

function BuscarAlertas($max,$iten,$arreglo)
{
	$k=0;
	for($i=0;$i<=$max;$i++)
	{
		if(strtoupper($arreglo[$i]) == strtoupper($iten))
			$k++;
	}
	if($k > 1)
		return false;
	return true;
}

function formula($wfor)
{
	$wforf="";
	$wsw=0;
	for ($w=0;$w<strlen($wfor);$w++)
	{
		if(strtoupper(substr($wfor,$w,1)) == "R")
		{
			$wforf .= chr(36)."registro[";
			$wsw=1;
		}
		elseif(is_numeric(substr($wfor,$w,1)))
			{
				$wforf .= substr($wfor,$w,1);
			}
			elseif(!is_numeric(substr($wfor,$w,1)) and $wsw == 1)
				{
					$wforf .= "][0]".substr($wfor,$w,1);
					$wsw=0;
				}
				else
				{
					$wforf .= substr($wfor,$w,1);
				}
	}
	$formula=$wforf;
	return $formula;
}

function formula1($wfor)
{
	$wforf="";
	$wsw=0;
	for ($w=0;$w<strlen($wfor);$w++)
	{
		if(strtoupper(substr($wfor,$w,1)) == "C")
		{
			$wforf .= chr(32).substr($wfor,$w,1);
			$wsw=1;
		}
		elseif(is_numeric(substr($wfor,$w,1)))
			{
				$wforf .= substr($wfor,$w,1);
			}
			elseif(!is_numeric(substr($wfor,$w,1)) and $wsw == 1)
				{
					$wforf .= chr(32).substr($wfor,$w,1);
					$wsw=0;
				}
				else
				{
					$wforf .= substr($wfor,$w,1);
				}
	}
	$formula=$wforf;
	return $formula;
}

function bisiesto($year)
{
	//si es multiplo de 4 y no es multiplo de 100 o es multiplo de 400*/
	return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
}

function ver($chain)
{
	if(strpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strpos($chain,"-"));
}
function ver1($chain)
{
	if(strrpos($chain,"-") === false)
		return $chain;
	else
		return substr($chain,0,strrpos($chain,"-"));
}
function validar1($chain)
{
	// Funcion que permite validar la estructura de un numero Real
	$decimal ="^(\+|-)?([[:digit:]]+)\.([[:digit:]]+)$";
	if (ereg($decimal,$chain,$occur))
		if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			return false;
		else
			return true;
	else
		return false;
}
function validar2($chain)
{
	// Funcion que permite validar la estructura de un numero Entero
	$regular="^(\+|-)?([[:digit:]]+)$";
	if (ereg($regular,$chain,$occur))
		if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
			return false;
		else
			return true;
	else
		return false;
}
function validar3($chain)
{
	// Funcion que permite validar la estructura de una fecha
	$fecha="^([[:digit:]]{4})-([[:digit:]]{1,2})-([[:digit:]]{1,2})$";
	if(ereg($fecha,$chain,$occur))
	{
		if($occur[2] < 0 or $occur[2] > 12)
			return false;
		if(($occur[3] < 0 or $occur[3] > 31) or 
		  ($occur[2] == 4 and  $occur[3] > 30) or 
		  ($occur[2] == 6 and  $occur[3] > 30) or 
		  ($occur[2] == 9 and  $occur[3] > 30) or 
		  ($occur[2] == 11 and $occur[3] > 30) or 
		  ($occur[2] == 2 and  $occur[3] > 29 and bisiesto($occur[1])) or 
		  ($occur[2] == 2 and  $occur[3] > 28 and !bisiesto($occur[1])))
			return false;
		return true;
	}
	else
		return false;
}
function validar4($chain)
{
	// Funcion que permite validar la estructura de un dato alfanumerico
	$regular="^([=a-zA-Z0-9' '��@?#-.:;_<>])+$";
	return (ereg($regular,$chain));
}
function validar5($chain)
{
	// Funcion que permite validar la estructura de un dato numerico
	$regular="^([0-9:])+$";
	return (ereg($regular,$chain));
}
function validar6($chain)
{
	// Funcion que permite validar la estructura de un campo Hora
	$hora="^([[:digit:]]{1,2}):([[:digit:]]{1,2}):([[:digit:]]{1,2})$";
	if(ereg($hora,$chain,$occur))
		if($occur[1] < 0 or $occur[1] >23 or $occur[2]<0 or $occur[2]>59)
			return false;
		else
			return true;
	else
		return false;
}
function validar7($chain)
{
	// Funcion que permite validar la estructura de un campo Hora Especial
	$hora="^([[:digit:]]{1,2}):([[:digit:]]{1,2})$";
	if(ereg($hora,$chain,$occur))
		if($occur[1] < 0 or $occur[1] >24 or ($occur[2]!=0 and $occur[2]!=30))
			return false;
		else
			return true;
	else
		return false;
}

@session_start();
if(!isset($_SESSION['user']))
{
	echo "<html>";
	echo "<head>";
	echo "<title>MATRIX HCE-Historia Clinica Electronica</title>";
	echo "</head>";
	echo "<BODY TEXT='#000000' FACE='ARIAL'>";
	echo "Su sesion ha expirado.  Por favor cierre esta ventana y recargue la pagina nuevamente.";
	echo "</BODY>";
	echo "</html>";
}
else
{
	echo "<html>";
	echo "<head>";
  	echo "<title>MATRIX HCE-Historia Clinica Electronica</title>";
  	echo "<link type='text/css' href='HCE.css' rel='stylesheet'>"; 
  	
  	

	
 	
  			
	$key = substr($user,2,strlen($user));
	$queryCPU = "SELECT Msedes  from ".$empresa."_000014 ";
	$queryCPU .= " where Msetab = 'CPU' ";
	$errCPU = mysql_query($queryCPU,$conex) or die(mysql_errno().":".mysql_error());
	$numCPU = mysql_num_rows($errCPU);
	if ($numCPU > 0)
	{
		$rowCPU = mysql_fetch_array($errCPU);
		$CPU=$rowCPU[0];
		switch($accion)
		{
			case "T":
				echo "</head>";
				echo "<body style='background-color:#E8EEF7' FACE='ARIAL' LINK='BLACK'>";
				echo "<div id='1'>";
				echo "<form name='HCE1' action='HCE.php' method=post>";
				
				echo "<table border=0 CELLSPACING=0>";
				echo "<tr><td align=center id=tipoT01><IMG SRC='/matrix/images/medical/root/lmatrix.jpg'></td>";
				echo "<td id=tipoT02>&nbsp;CLINICA LAS AMERICAS<BR>&nbsp;HISTORIA CLINICA ELECTRONICA HCE&nbsp;&nbsp;<A HREF='/MATRIX/root/Reportes/DOC.php?files=../../HCE/procesos/HCE.php' target='_blank'>Version 2011-04-04</A></td></tr>";
				echo "<tr><td id=tipoT03 colspan=2></td></tr>";
				echo "</table>";
				echo"</form>";
			break;
			
			case "U": 
				echo "</head>";
	  			echo "<BODY TEXT='#000000' FACE='ARIAL'>";
	  			echo "<div id='1'>";
				$key = substr($user,2,strlen($user));
				echo "<form name='HCE2' action='HCE.php' method=post>";
				$query = "select descripcion from usuarios where codigo = '".$key."'";
				$err = mysql_query($query,$conex);
				$row = mysql_fetch_array($err);
				$wuser=$row[0];
				echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
				echo "<center><input type='HIDDEN' name= 'accion' value='".$accion."'>";
				$color="#dddddd";
				$color1="#C3D9FF";
				$color2="#E8EEF7";
				$color3="#CC99FF";
				$color4="#99CCFF";
				echo "<body style='background-color:#C3D9FF' FACE='ARIAL'>";
				echo "<table border=0 CELLSPACING=0>";
				echo "<tr><td id=tipoL05>USUARIO : </td></tr>";
				echo "<tr><td id=tipoL05>".$wuser."</td></tr>";
				echo "</table>";
				echo"</form>";
			break;
			
			case "A": 
				echo "<link type='text/css' href='../../../include/root/jquery.tooltip.css' rel='stylesheet'>";				    
				echo "<script type='text/javascript' src='../../../include/root/jquery-1.3.2.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/ui.core.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>";
				echo "<script type='text/javascript' src='HCE.js' ></script>";
				echo "</head>";
	  			echo "<BODY TEXT='#000000' FACE='ARIAL'>";
	  			echo "<div id='29'>";
				$key = substr($user,2,strlen($user));
				echo "<form name='HCE3' action='HCE.php' method=post>";
				echo "<meta http-equiv='refresh' content='240;url=/matrix/HCE/procesos/HCE.php?accion=A&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."' target='alergias'>";
				echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
				echo "<center><input type='HIDDEN' name= 'accion' value='".$accion."'>";

				echo "<center><input type='HIDDEN' name= 'wcedula' value='".$wcedula."'>";
				echo "<center><input type='HIDDEN' name= 'wtipodoc' value='".$wtipodoc."'>";
				$color="#dddddd";
				$color1="#C3D9FF";
				$color2="#E8EEF7";
				$color3="#CC99FF";
				$color4="#99CCFF";
				echo "<body style='background-color:#FFDDDD' FACE='ARIAL'>";
				if(isset($okA))
				{
					$query =  " update ".$empresa."_".$wformulario." set movdat = 'UNCHECKED' ";
					$query .=  "  where fecha_data='".$wfecha."' and hora_data='".$whora."' and movpro='".$wformulario."' and movcon='6' and movhis='".$whis."' and moving='".$wing."'";
					$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO DATOS DE HISTORIA CLINICA EN ALERTAS : ".mysql_errno().":".mysql_error());
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$query  = " select ".$empresa."_".$wformulario.".movdat from ".$empresa."_".$wformulario." ";
					$query .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
					$query .= "   and ".$empresa."_".$wformulario.".fecha_data = '".$wfecha."' "; 
					$query .= "   and ".$empresa."_".$wformulario.".hora_data = '".$whora."' ";
					$query .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
					$query .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
					$query .= "   and ".$empresa."_".$wformulario.".movcon = 7 ";
					$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$row1 = mysql_fetch_array($err1);
					$walerta = $row1[0]." Alerta desactivada por ".$key." en ".$fecha."  a las ".$hora;
					$query =  " update ".$empresa."_".$wformulario." set movdat = '".$walerta."' ";
					$query .=  "  where fecha_data='".$wfecha."' and hora_data='".$whora."' and movpro='".$wformulario."' and movcon='7' and movhis='".$whis."' and moving='".$wing."'";
					$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO DATOS DE HISTORIA CLINICA EN ALERTAS : ".mysql_errno().":".mysql_error());
				}
				$query = "select Orihis,Oriing,Pacsex from root_000036,root_000037 ";
				$query .= " where pacced = '".$wcedula."'";
				$query .= "   and pactid = '".$wtipodoc."'";
				$query .= "   and  pacced = oriced ";
				$query .= "   and  pactid = oritid ";
				$query .= "   and oriori = '01' ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$row = mysql_fetch_array($err);
				$whis=$row[0];
				$wing=$row[1];
				$wsex=$row[2];
				
				$query = "SELECT Encpro  from ".$empresa."_000001 ";
				$query .= " where Encale = 'on' ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if ($num > 0)
				{
					$knumw=-1;
					$matrixA=array();
					$checkA=array();
					$ALERTAS=array();
					$row = mysql_fetch_array($err);
					$wformulario=$row[0];
					$alto=0;
					$ancho=0;
					$width=0;
					echo "<center><table border=0>";
					echo "<tr><td id=tipoH00A colspan=4 onClick='javascript:activarModalIframe(\"".htmlentities("ALERTAS")."\",\"nombreIframe\",\"http://".$CPU."/matrix/HCE/Procesos/HCE.php?accion=W1&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wformulario=".$wformulario."&whis=".$whis."&wing=".$wing."&wsex=".$wsex."&wtitframe=no&width=".$width."\",\"".$alto."\",\"".$ancho."\");'>ALERTAS</td></tr>";
					//echo "<tr><td id=tipoH01A>FECHA</td><td id=tipoH01A>HORA</td><td id=tipoH01A>ALERTA</td><td id=tipoH01A><IMG SRC='/MATRIX/images/medical/HCE/cancel.png'></td></tr>";
					echo "<tr><td id=tipoH01A>DESCRIPCION</td><td id=tipoH01A><IMG SRC='/MATRIX/images/medical/HCE/cancel.png'></td></tr>";
					$query  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".hora_data,max(".$empresa."_".$wformulario.".movcon) as a from ".$empresa."_".$wformulario." "; 
					$query .= " where ".$empresa."_".$wformulario.".movhis='".$whis."' ";
					$query .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
					//$query .= "   and  ".$empresa."_".$wformulario.".movusu='".$key."' "; 
					$query .= " group by 1,2  ";
					$query .= " having a = 1000 ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							$query  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_000002.Detorp,".$empresa."_".$wformulario.".movdat,".$empresa."_".$wformulario.".movtip from ".$empresa."_".$wformulario.",".$empresa."_000002 ";
							$query .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
							$query .= "   and ".$empresa."_".$wformulario.".fecha_data = '".$row[0]."' "; 
							$query .= "   and ".$empresa."_".$wformulario.".hora_data = '".$row[1]."' ";
							$query .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
							$query .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
							$query .= "   and ".$empresa."_".$wformulario.".movtip in ('Texto','Booleano') ";
							$query .= "   and ".$empresa."_".$wformulario.".movpro = ".$empresa."_000002.Detpro ";
							$query .= "   and ".$empresa."_".$wformulario.".movcon = ".$empresa."_000002.Detcon ";
							$query .= "  order by 3 ";
							$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
							$num1 = mysql_num_rows($err1);
							if ($num1>0)
							{
								$knumw++;
								for ($j=0;$j<$num1;$j++)
								{
									$row1 = mysql_fetch_array($err1);
									$matrixA[$knumw][0]=$row1[0];
									$matrixA[$knumw][1]=$row1[1];
									$matrixA[$knumw][$j+2]=$row1[3];
									$ALERTAS[$knumw]=$row1[3];
								}
							}
						}
					}
					$ia=-1;
					for ($i=0;$i<=$knumw;$i++)
					{
						if($matrixA[$i][2] == "CHECKED" and BuscarAlertas($i,$ALERTAS[$i],$ALERTAS))
						{
							$ia = $ia + 1;
							if($ia % 2 == 0)
							{
								$color="tipoH03A";
								$colorA="tipoL02M";
							}
							else
							{
								$color="tipoH02A";
								$colorA="tipoL02MW";
							}
							$walertas=$ALERTAS[$i];
							echo "<tr id='ALERT[".$i."]' title='FECHA : ".$matrixA[$i][0]." HORA : ".$matrixA[$i][1]."' onMouseMove='tooltipAlertas(".$i.")'>";
							echo "<td id=".$color.">".$ALERTAS[$i]."</td>";
							$id="ajaxalert('29','A','".$empresa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$matrixA[$i][0]."','".$matrixA[$i][1]."','1')";
							echo "<td id=".$color."><input type='checkbox' name='alert[".$i."]' id='C".$i."' class=".$colorA." OnClick=".$id."></td>";
							echo "</tr>";
						}
						else
						{
							if($matrixA[$i][2] == "CHECKED")
							{
								$query =  " update ".$empresa."_".$wformulario." set movdat = 'UNCHECKED' ";
								$query .=  "  where fecha_data='".$matrixA[$i][0]."' and hora_data='".$matrixA[$i][1]."' and movpro='".$wformulario."' and movcon='6' and movhis='".$whis."' and moving='".$wing."'";
								$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO DATOS DE HISTORIA CLINICA EN ALERTAS : ".mysql_errno().":".mysql_error());
								$fecha = date("Y-m-d");
								$hora = (string)date("H:i:s");
								$query  = " select ".$empresa."_".$wformulario.".movdat from ".$empresa."_".$wformulario." ";
								$query .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
								$query .= "   and ".$empresa."_".$wformulario.".fecha_data = '".$matrixA[$i][0]."' "; 
								$query .= "   and ".$empresa."_".$wformulario.".hora_data = '".$matrixA[$i][1]."' ";
								$query .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
								$query .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
								$query .= "   and ".$empresa."_".$wformulario.".movcon = 7 ";
								$err1 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
								$row1 = mysql_fetch_array($err1);
								$walerta = $row1[0]." Alerta desactivada por ".$key." en ".$fecha."  a las ".$hora;
								$query =  " update ".$empresa."_".$wformulario." set movdat = '".$walerta."' ";
								$query .=  "  where fecha_data='".$matrixA[$i][0]."' and hora_data='".$matrixA[$i][1]."' and movpro='".$wformulario."' and movcon='7' and movhis='".$whis."' and moving='".$wing."'";
								$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO DATOS DE HISTORIA CLINICA EN ALERTAS : ".mysql_errno().":".mysql_error());
							}
						}
					}
					echo"</table>";
				}
				echo"</form>";
			break;
			
			case "F": 		
				echo "<script type='text/javascript' src='HCE.js' ></script>";	
	  			echo "</head>";
	  			echo "<BODY TEXT='#000000' FACE='ARIAL'>";
	  			echo "<div id='1'>";
				function Deep($menu,$limit,$nivel,$exp,$empresa,$swiches,$wcedula,$wtipodoc,$mutiplo)
				{
					global $key;
					for($i=1;$i<=$limit;$i++)
					{
						if($exp % 2 == 0)
							$tipM="tipoM01";
						else
							$tipM="tipoM02";
						$blank="";
						for($j=1;$j<=$exp*2;$j++)
							$blank .= "&nbsp;&nbsp;&nbsp;";
						$root=($nivel*$mutiplo)+$i;
						$itemx=explode(".",$swiches);
						$itemy=array();
						for ($j=1;$j<count($itemx);$j++)
							$itemy[(integer)substr($itemx[$j],0,strpos($itemx[$j],"-"))]=substr($itemx[$j],strpos($itemx[$j],"-")+1);
						if($menu[$nivel*$mutiplo+$i][0] > 0 and $itemy[$nivel*$mutiplo+$i] == 1)
						{
							if($menu[$nivel*$mutiplo+$i][3] == $key)
							{
								$id="ajaxquery('1','".$root."','".$swiches."','".$empresa."',0,'F','".$wcedula."','".$wtipodoc."',0)";
								echo "<tr><td nowrap id=".$tipM." OnClick=".$id.">".$blank."<IMG SRC='/matrix/images/medical/HCE/menos.png'>&nbsp;".htmlentities($menu[$nivel*$mutiplo+$i][1])."</td></tr>";
								Deep(&$menu,$menu[$nivel*$mutiplo+$i][0],$nivel*$mutiplo+$i,$exp+1,$empresa,$swiches,$wcedula,$wtipodoc,$mutiplo);
							}
						}
						else
							if($menu[$nivel*$mutiplo+$i][0] > 0)
							{
								if($menu[$nivel*$mutiplo+$i][3] == $key)
								{
									$id="ajaxquery('1','".$root."','".$swiches."','".$empresa."',0,'F','".$wcedula."','".$wtipodoc."',0)";
									echo "<tr><td nowrap id=".$tipM." OnClick=".$id.">".$blank."<IMG SRC='/matrix/images/medical/HCE/mas.png'>&nbsp;".htmlentities($menu[$nivel*$mutiplo+$i][1])."</td></tr>";
								}
							}
							else
							{
								$blank .= "&nbsp;&nbsp;";
								$users=explode("-",$menu[$nivel*$mutiplo+$i][3]);
								$wswu=0;
								for($u=0;$u < count($users);$u++)
									if($users[$u] == $key)
									{
										$wswu=1;
										$u=count($users);
									}
								if($wswu == 1)
								{
									$id="ajaxquery('1','".$root."','".$swiches."','".$empresa."',0,'F','".$wcedula."','".$wtipodoc."',0)";
									if($menu[$nivel*$mutiplo+$i][6] == "off")
										echo "<tr><td nowrap id=".$tipM." OnClick=".$id.">".$blank."<A HREF='javascript:recargaIframes(\"".$menu[$nivel*$mutiplo+$i][2]."\",\"".$menu[$nivel*$mutiplo+$i][5]."\")'>".htmlentities($menu[$nivel*$mutiplo+$i][1])."</td></tr>";
									else
									{
										$alto=0;
										$ancho=0;
										if($menu[$nivel*$mutiplo+$i][7] == "on")
											$alto=-1;
										echo "<tr><td nowrap id=".$tipM." OnClick=".$id.">".$blank."<A HREF='javascript:activarModalIframe(\"".$menu[$nivel*$mutiplo+$i][1]."\",\"nombreIframe\",\"".$menu[$nivel*$mutiplo+$i][2]."\",\"".$alto."\",\"".$ancho."\");'>".htmlentities($menu[$nivel*$mutiplo+$i][1])."</td></tr>";
									}
								}
								else
								{
									if($menu[$nivel*$mutiplo+$i][3] == $key)
									{
										$id="ajaxquery('1','".$root."','".$swiches."','".$empresa."',0,'F','".$wcedula."','".$wtipodoc."',1)";
										echo "<tr><td nowrap id=".$tipM." OnClick=".$id.">".$blank.htmlentities($menu[$nivel*$mutiplo+$i][1])."</td></tr>";
									}
								}
							}
					}
				}
				
				// NUMERO DE DIGITOS PARA OPCIONES DEL MENU  
				$digitos=2;
				$mutiplo=pow(10, $digitos);
				$key = substr($user,2,strlen($user));
				echo "<form name='HCE4' action='HCE.php' method=post>";
				echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
				echo "<center><input type='HIDDEN' name= 'accion' value='".$accion."'>";
				echo "<center><input type='HIDDEN' name= 'wcedula' value='".$wcedula."'>";
				echo "<center><input type='HIDDEN' name= 'wtipodoc' value='".$wtipodoc."'>";
				$color="#dddddd";
				$color1="#C3D9FF";
				$color2="#E8EEF7";
				$color3="#CC99FF";
				$color4="#99CCFF";
				
				echo "<B>FORMULARIOS</B><br>";
				$id="ajaxquery('1','1','','".$empresa."',0,'F','".$wcedula."','".$wtipodoc."',0)";
				echo "<IMG SRC='/matrix/images/medical/HCE/lupa.png'><input type='TEXT' name='bfor' size=30 maxlength=30 id='w01' class=tipo3 Onblur=".$id.">";
				echo "<body style='background-color:#E8EEF7;color:#000000;' FACE='ARIAL'>";
				
				$vistas1=array();
				$numvistas=-1;
				$query  = "select hce_000021.Rararb,1 from hce_000020,hce_000021,hce_000009 ";
				$query .= "   where hce_000020.Usucod = '".$key."' ";
				$query .= " 	and hce_000020.Usurol = hce_000021.rarcod ";
				$query .= " 	and hce_000021.Rargra = 'on'";
				$query .= " 	and hce_000021.rararb = hce_000009.precod "; 
				$query .= " union all  "; 
				$query .= " select hce_000009.precod,0  from hce_000009  "; 
				$query .= "  where hce_000009.Prenod = 'on'"; 
				$query .= "   order by 1";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$vistas1[$i][0] = $row[0];
						$vistas1[$i][1] = $row[1];
					}
					for ($i=0;$i<$num;$i++)
					{
						$j=$num -1 -$i;
						if($j < ($num -1) and  $vistas1[$j+1][1] == 1 and strlen($vistas1[$j][0]) < strlen($vistas1[$j+1][0]) and $vistas1[$j][0] == substr($vistas1[$j+1][0],0,strlen($vistas1[$j][0])))
							$vistas1[$j][1]=1;
					}
					$vistas=array();
					for ($i=0;$i<$num;$i++)
					{
						if($vistas1[$i][1] == 1)
						{
							$numvistas = $numvistas + 1;
							$vistas[$numvistas]=$vistas1[$i][0];
						}
					}
				}
				$numvistas++;
						
				$menu=array();
				$esquema=array();
				//                 0       1       2       3       4       5
				$query = "SELECT Precod, Predes, Preurl, Prenod, Premod, Preext from ".$empresa."_000009 ";
				$query .= " where Preest = 'on' ";
				$query .= " Order by Precod ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$menu[$row[0]][0]=0;
						$menu[$row[0]][1]=$row[1];
						if(strtoupper(substr($row[2],0,2)) == "F=")
						{
							$menu[$row[0]][2]="HCE.php?accion=M&ok=0&empresa=hce&wformulario=".substr($row[2],2,6)."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."";
							$menu[$row[0]][5]="HCE.php?accion=H&ok=0&empresa=hce&wformulario=".substr($row[2],2,6)."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."";
						}
						else
						{
							if(strpos($row[2],"CED") !== false)
							{
								$row[2]=str_replace("CED",$wcedula,$row[2]);
								$row[2]=str_replace("TDO",$wtipodoc,$row[2]);
							}
							$menu[$row[0]][2]=$row[2];
							$menu[$row[0]][5]=$row[2];
						}
						if($numvistas != 0)
							$pos=bi($vistas,$numvistas,$row[0]);
						else
							$pos = -1;
						if($pos != -1)
							$menu[$row[0]][3]=$key;
						else
							$menu[$row[0]][3]="NO";
						$menu[$row[0]][4]=$row[3];
						$menu[$row[0]][6]=$row[4];
						$menu[$row[0]][7]=$row[5];
						$esquema[$i]=$row[0];
					}
				}
				else
				{
					$menu[$row[0]][0]=0;
					$menu[$row[0]][1]="Inicio";
				}
				for ($i=0;$i<$num;$i++)
				{
					for ($j=$i;$j<$num;$j++)
					{
						if(strlen($esquema[$j]) == (strlen($esquema[$i]) + $digitos) and substr($esquema[$j],0,strlen($esquema[$i])) == $esquema[$i])
							$menu[$esquema[$i]][0]++;
					}
				}
				if(isset($bfor) and $bfor != "")
				{
					$paths=array();
					$kp=-1;
					for ($i=0;$i<$num;$i++)
					{
						if(substr_count(strtolower($menu[$esquema[$i]][1]),strtolower($bfor)) > 0 and $menu[$esquema[$i]][4] == "off" and $menu[$esquema[$i]][3] != "NO")
						{
							$kp = $kp + 1;
							$paths[$kp][0] = $esquema[$i];
							$paths[$kp][1] = $menu[$esquema[$i]][2];
							$paths[$kp][2] = $menu[$esquema[$i]][4];
						}
					}
					if($kp > -1)
					{
						echo "<table border=0 align=center cellspacing=0 id=tipoM00>";
						for ($i=0;$i<=$kp;$i++)
						{
							$ruta="";
							$j=1;
							$knum=0;
							while($knum<strlen($paths[$i][0]))
							{
								$knum=($digitos * $j) - ($digitos -1);
								$ruta .= $menu[substr($paths[$i][0],0,$knum)][1]."/";
								$j++;
							}
							if($paths[$i][2] == "off")
								echo "<tr><td id=tipoM03><A HREF='".$paths[$i][1]."' target='principal'>".$ruta."</A></tr></td>";
							else
								echo "<tr><td id=tipoM03>".$ruta."</tr></td>";
						}
						echo "</table>";
					}
					else
					{
						echo "<table border=0 align=center id=tipoM00>";
						echo "<tr><td id=tipoM03>SIN OCURRENCIAS</tr></td>";
						echo "</table>";
					}
				}
				
				if(!isset($swiches) or $root == 1)
				{
					$swiches = "";
					for ($i=0;$i<$num;$i++)
						if($i == 0)
							$swiches .= ".".$esquema[$i]."-1";
						else
							$swiches .= ".".$esquema[$i]."-0";
				}
				if(isset($root))
				{
					$itemx=explode(".",$swiches);
					$itemy=array();
					for ($i=1;$i<count($itemx);$i++)
						$itemy[(integer)substr($itemx[$i],0,strpos($itemx[$i],"-"))]=substr($itemx[$i],strpos($itemx[$i],"-")+1);
					if($itemy[$root] == 0)
						$itemy[$root]=1;
					else
						$itemy[$root]=0;
					$swiches = "";
					for ($i=1;$i<count($itemx);$i++)
						$swiches .= ".".substr($itemx[$i],0,strpos($itemx[$i],"-"))."-".$itemy[(integer)substr($itemx[$i],0,strpos($itemx[$i],"-"))];
				}
				else
					$root=1;
				
				if(!isset($blank))
					$blank = "&nbsp;&nbsp;";
				//echo "<script> alert('Ancho : '+screen.availHeight+' Alto : '+screen.availWidth)</script>";
				
				echo "<table border=0 align=center cellspacing=0 id=tipoM00>";
				$id="ajaxquery('1','1','".$swiches."','".$empresa."',0,'F','".$wcedula."','".$wtipodoc."',0)";
				echo "<tr><td nowrap id=tipoM01 OnClick=".$id."><IMG SRC='/matrix/images/medical/HCE/HCE.png'><A HREF='/MATRIX/HCE/Procesos/HCE.php?accion=M&ok=0&empresa=".$empresa."' target='principal'>".$blank.htmlentities($menu['1'][1])."</A></td></tr>";
				Deep(&$menu,$menu[1][0],1,1,$empresa,$swiches,$wcedula,$wtipodoc,$mutiplo);
				echo "</table>";
				echo "<br><IMG SRC='/matrix/images/medical/HCE/button.gif' onclick='javascript:top.close();'><br>";
				echo"</form>";
				
			break;
			
			case "D": 
				echo "<link type='text/css' href='../../../include/root/jquery.tooltip.css' rel='stylesheet'>";				    
				echo "<script type='text/javascript' src='../../../include/root/jquery-1.3.2.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/ui.core.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>";
				echo "<script type='text/javascript' src='HCE.js' ></script>";
				echo "<script type='text/javascript'>";
				echo "function mueveReloj(){";
				echo "momentoActual = new Date();";
				echo "hora = momentoActual.getHours();";
				echo "minuto = momentoActual.getMinutes();";
				echo "segundo = momentoActual.getSeconds();";
				
				echo "thora = hora.toString();";
				echo "if(thora.length == 1){";
				echo "thora = '0' + thora;";
				echo "}";
				echo "tminuto = minuto.toString();";
				echo "if(tminuto.length == 1){";
				echo "tminuto = '0' + tminuto;";
				echo "}";
		   		echo "tsegundo = segundo.toString();";
				echo "if(tsegundo.length == 1){";
				echo "tsegundo = '0' + tsegundo;";
				echo "}";
				echo "horaImprimible = thora + \" : \" + tminuto + \" : \" + tsegundo;";
				echo "document.HCE5.reloj.value = horaImprimible;";
				echo "setTimeout('mueveReloj()',1000);";
	   			echo "}";
				echo "</script>";

				echo "</head>";
	  			echo "<BODY TEXT='#000000' FACE='ARIAL'>";
	  			echo "<div id='1'>";
				$key = substr($user,2,strlen($user));
				echo "<form name='HCE5' action='HCE.php' method=post>";
				echo "<body style='background-color:#FFFFFF'  onload='mueveReloj()' FACE='ARIAL'>";
				echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
				echo "<center><input type='HIDDEN' name= 'accion' value='".$accion."'>";
				//                 0      1      2      3      4      5      6      7      8      9      10     11                12
				$query = "select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing,Ingnre,Ubisac,Ubihac,Cconom,movhos_000016.fecha_data from root_000036,root_000037,movhos_000016,movhos_000018,movhos_000011 ";
				$query .= " where pacced = '".$wcedula."'";
				$query .= "   and pactid = '".$wtipodoc."'";
				$query .= "   and  pacced = oriced ";
				$query .= "   and  pactid = oritid ";
				$query .= "   and oriori = '01' ";
				$query .= "   and inghis = orihis ";
				$query .= "   and  inging = oriing ";
				$query .= "   and ubihis = inghis "; 
				$query .= "   and ubiing = inging ";
				$query .= "   and ccocod = ubisac ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$row = mysql_fetch_array($err);
				$sexo="MASCULINO";
				if($row[5] == "F")
					$sexo="FEMENINO";
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
				$wpac = $row[0]." ".$row[1]." ".$row[2]." ".$row[3];
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
				$index=1001;
				$ancho=1250;
				$alto=330;
				echo "<table border=0>";
				echo "<tr><td id=tipoL01C>Paciente</td><td colspan=4 id=tipoL04>".$wpac."</td><td id=tipoL04A>".$fechal."<input type='text' name='reloj' size='10' readonly='readonly' class=tipo3R></td></tr>";
				echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$row[6]."-".$row[7]."</td><td id=tipoL01>Edad</td><td id=tipoL02C>".$wedad."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".$sexo."</td></tr>";
				echo "<tr><td id=tipoL01C>Fecha Ingreso</td><td id=tipoL02C>".$row[12]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$row[10]."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$row[8]."</td></tr>";
				if(!isset($wtitulo))
					$wtitulo="";
				//echo "<input type='HIDDEN' name='txtformulario' id='txtformulario'>";
				$path1="http://".$CPU."/matrix/HCE/procesos/HCE_Impresion.php?empresa=hce&wcedula=".$wcedula."&wtipodoc=".$wtipodoc;
				$path2="http://".$CPU."/matrix/HCE/procesos/HCE_Notas.php?empresa=hce&wcedula=".$wcedula."&wtipodoc=".$wtipodoc;
				$path3="http://".$CPU."/matrix/HCE/procesos/HCE_Historico.php?empresa=hce&wcedula=".$wcedula."&wtipodoc=".$wtipodoc;
				$path4="http://".$CPU."/matrix/HCE/procesos/HCE_Consulta.php?empresa=hce&wcedula=".$wcedula."&wtipodoc=".$wtipodoc;
				echo "<tr><td colspan=5 id=tipoL04C><input type='TEXT' name='txtformulario' id='TXTFORMULARIO' size=1 value='' readonly='readonly' class=tipo3TW><input type='TEXT' name='txttitulo' id='TXTTITULO' size=35 value='".$wtitulo."' readonly='readonly' class=tipo3T></td><td colspan=5 id=tipoL03C><A HREF='#' id='btnModal".$index."' name='btnModal".$index."'  onClick='javascript:mostrarFlotante(\"\",\"nombreIframe\",\"http://".$CPU."/matrix/HCE/Procesos/HCE.php?accion=W2&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&whis=".$row[6]."&wing=".$row[7]."&wtitframe=no\",\"".$alto."\",\"".$ancho."\");'><IMG SRC='/matrix/images/medical/HCE/hceR.jpg' id='ICONOS[1]' title='Otros Registros Asociados'  onMouseMove='tooltipIconos(1)'></A>&nbsp;&nbsp;<A HREF='#' id='IMPRESION' onclick='javascript:activarModalIframe(\"IMPRESION\",\"nombreIframe\",\"".$path1."\",\"0\",\"0\");'><IMG SRC='/matrix/images/medical/HCE/hceP.jpg' id='ICONOS[2]' title='Impresion'  onMouseMove='tooltipIconos(2)'></A>&nbsp;&nbsp;<A HREF='#' id='NOTAS' onclick='javascript:activarModalIframe(\"NOTAS\",\"nombreIframe\",\"".$path2."\",\"0\",\"0\");'><IMG SRC='/matrix/images/medical/HCE/hceN.jpg' id='ICONOS[3]' title='Notas Complementarias'  onMouseMove='tooltipIconos(3)'></A>&nbsp;&nbsp;<A HREF='#' id='GRAFICAS'  onclick='javascript:activarModalIframe(\"GRAFICAS\",\"nombreIframe\",\"".$path3."\",\"0\",\"0\");'><IMG SRC='/matrix/images/medical/HCE/hceG.jpg' id='ICONOS[4]' title='Graficos de Registros Numericos'  onMouseMove='tooltipIconos(4)'></A>&nbsp;&nbsp;<A HREF='#' id='CONSULTAS'  onclick='javascript:activarModalIframe(\"CONSULTAS\",\"nombreIframe\",\"".$path4."\",\"0\",\"0\");'><IMG SRC='/matrix/images/medical/HCE/hceB.jpg' id='ICONOS[5]' title='Consultas'  onMouseMove='tooltipIconos(5)'></A></td></tr>";
				echo "</table>";
				echo"</form>";
			break;
			
			case "M": 			
				echo "</head>";
				echo "<BODY TEXT='#000000' FACE='ARIAL'>";
				echo "<div id='1'>";

				if(isset($wformulario))
				{
					$query = "select Orihis,Oriing,Pacsex from root_000036,root_000037 ";
					$query .= " where pacced = '".$wcedula."'";
					$query .= "   and pactid = '".$wtipodoc."'";
					$query .= "   and  pacced = oriced ";
					$query .= "   and  pactid = oritid ";
					$query .= "   and oriori = '01' ";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$row = mysql_fetch_array($err);
					$whis=$row[0];
					$wing=$row[1];
					$wsex=$row[2];
					
					$query = "SELECT Enccol, Encnfi, Encnco   from ".$empresa."_000001 ";
					$query .= " where Encpro = '".$wformulario."' ";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$row = mysql_fetch_array($err);
					$wcolf=$row[0];
					$span1=$row[2];
					$span2=$row[1];
					echo "<iframe src='HCE.php?accion=W1&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wformulario=".$wformulario."&whis=".$whis."&wing=".$wing."&wsex=".$wsex."&width=".$span1."' name='titulos' marginwidth=0 scrolling='no' framespacing='0' frameborder='0' border='0'  border='0' height='".$span2."' width='".$span1."'  marginheiht=0>";
					echo "</iframe>";
				}
				else
				{
					echo "<table border=0>";
					echo "<tr><td id=tipoL03>BIENVENIDO AL PROGRAMA DE HISTORIA CLINICA ELECTRONICA (HCE)</td></tr>";
					echo "</table>";
					echo '<script language="Javascript">';
					//echo "     debugger;";
					echo '		var obj = parent.parent.demograficos.document.all.txttitulo;';
					echo '		if(obj)';
					echo '		{';
					echo "			obj.value = '';";
					echo '		} ';
					echo '		var obj1 = parent.parent.demograficos.document.all.txtformulario;';
					echo '		if(obj1)';
					echo '		{';
					echo "			obj1.value = '';";
					echo '		} ';
					echo '</script>';
				}
			break;
			
			case "UT": 
				echo "</head>";
				echo "<body style='background-color:#E8EEF7;color:#000000;' FACE='ARIAL'>";
				echo "<div id='1'>";
				echo "<center><table border=0>";
				echo "<tr><td align=center id=tipoUT1>AYUDAS</td></tr>";
				echo "<tr><td align=center id=tipoUT1></td></tr>";
				echo "<tr><td align=center id=tipoUT2><IMG SRC='/matrix/images/medical/HCE/HCE.png' alt='CONSULTA DE GUIAS'></td></tr>";
				echo "<tr><td align=center id=tipoUT1></td></tr>";
				echo "<tr><td align=center id=tipoUT2><A HREF='http://".$CPU."/matrix/def/index.htm' target='_blank'><IMG SRC='/matrix/images/medical/HCE/MED.png' alt='VADEMECUM' width='50%'></A></td></tr>";
				echo "<tr><td align=center id=tipoUT1></td></tr>";
				echo "<tr><td align=center id=tipoUT2><IMG SRC='/matrix/images/medical/HCE/LUPA.png' alt='BIBLIOTECAS MEDICAS' width='50%'></td></tr>";
				echo "</table></center>";
			break;
			
			case "W1": 
				echo "<!-- BEGIN: load jqplot -->";
				echo "<script language='javascript' type='text/javascript' src='../../../include/root/Tipmage.js'></script>";
				echo "<link rel='stylesheet' type='text/css' href='../../../include/root/Tipmage.css' />";
				echo "<!-- END: load jqplot -->";
				echo "<link type='text/css' href='../../../include/root/ui.core.css' rel='stylesheet'>";
				echo "<link type='text/css' href='../../../include/root/ui.theme.css' rel='stylesheet'>";
				echo "<link type='text/css' href='../../../include/root/ui.datepicker.css' rel='stylesheet'>";
				
				echo "<link type='text/css' href='../../../include/root/jquery.tooltip.css' rel='stylesheet'>";
				echo "<link type='text/css' href='../../../include/root/jquery.autocomplete.css' rel='stylesheet'>";
				echo "<link type='text/css' href='../../../include/root/jquery.jTPS.css' rel='stylesheet'>";
				    
				echo "<script type='text/javascript' src='../../../include/root/jquery-1.3.2.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/ui.core.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/ui.draggable.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>";

				echo "<script type='text/javascript' src='../../../include/root/ui.datepicker.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/ui.accordion.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.dimensions.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.ajaxQueue.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.bgiframe.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.autocomplete.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.maskedinput.js' ></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.jTPS.js'></script>";
				
				echo "<script type='text/javascript' src='HCE.js' ></script>";
				
				echo "<script type='text/javascript'>";
				echo "$(document).ready(function(){  init_jquery();  });";
				echo "</script>";
				
				echo "</head>";
				echo "<BODY TEXT='#000000' onLoad='pintardivs()' FACE='ARIAL'>";
			    
				echo "<div id='19'>";
				$key = substr($user,2,strlen($user));
				echo "<form name='HCE6' action='HCE.php' method=post>";
				echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />";
				echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
				echo "<center><input type='HIDDEN' name= 'accion' value='".$accion."'>";
				echo "<center><input type='HIDDEN' name= 'wformulario' value='".$wformulario."'>";
				echo "<center><input type='HIDDEN' name= 'whis' value='".$whis."'>";
				echo "<center><input type='HIDDEN' name= 'wing' value='".$wing."'>";
				if(isset($wsex))
					echo "<input type='HIDDEN' name= 'wsex' value='".$wsex."' id='wsex'>";
				else
				{
					$query = "select Pacsex from root_000036 ";
					$query .= " where pacced = '".$wcedula."'";
					$query .= "   and pactid = '".$wtipodoc."'";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$row = mysql_fetch_array($err);
					$wsex=$row[0];
					echo "<input type='HIDDEN' name= 'wsex' value='".$wsex."' id='wsex'>";
				}
			
				
				
				$color="#dddddd";
				$color1="#C3D9FF";
				$color2="#E8EEF7";
				$color3="#CC99FF";
				$color4="#99CCFF";
				
				
				//echo "<B>HISTORIA CLINICA ELECTRONICA</B>";
				echo "<body style='background-color:#ffffff' FACE='ARIAL'>";
				//if(isset($Hgrafica))
				//	echo "Valor de Graficas es : ".$Hgrafica."<br>";
				
				if($whis != "" and $wing != "")
				{
					if(isset($ok) AND $ok == "CHECKED")
					{
						$query = "SELECT Detcon, Dettip, Detarc, Detobl, Detdes, Detase from ".$empresa."_000002 ";
						$query .= " where Detpro = '".$wformulario."' ";
						$query .= "   and Detest = 'on' ";
						$query .= " Order by Detorp ";
						$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$numW = mysql_num_rows($err);
						if ($numW>0)
						{
							if(!isset($DATA))
								$DATA=array();
							for ($i=0;$i<$numW;$i++)
							{
								$row = mysql_fetch_array($err);
								$DATA[$i][0]=$row[0];
								$DATA[$i][1]=$row[1];
								$DATA[$i][2]=$row[2];
								$DATA[$i][3]=$row[3];
								$DATA[$i][4]=$row[4];
								$DATA[$i][5]=$row[5];
							}
						}
						if(substr($firma,0,3) == "HCE")
						{
							$wswfirma=0;
							$query = "SELECT count(*)  from ".$empresa."_000020 ";
							$query .= " where Usucod = '".$key."' ";
							$query .= "   and Usucla = '".substr($firma,4)."' ";
							$query .= "   and Usuest = 'on' ";
							$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
							$row = mysql_fetch_array($err);
							if ($row[0] > 0)
								$wswfirma=1;
							else
								$wswfirma=3;
						}
						else
						{
							$wswfirma=2;
						}
						
						if($wswfirma == 1 or($wswfirma == 2 and $WTIPO == 3))
						{
							$NOFILL="";
							for ($i=0;$i<$num;$i++)
							{
								if($DATA[$i][1] != "Titulo" and $DATA[$i][1] != "Subtitulo" and $DATA[$i][1] != "Label" and $DATA[$i][1] != "Link" and ($DATA[$i][5] == "A"  or ($DATA[$i][5] != "A" and $DATA[$i][5] == $wsex)))
								{
									if($DATA[$i][3] == "on" and ((strlen($registro[$i][0]) <= 1 and $DATA[$i][1] == "Tabla") or (strlen($registro[$i][0]) == 0 and $DATA[$i][1] != "Seleccion") or ($DATA[$i][1] == "Seleccion" and $registro[$i][0] == "undefined")))
									{
										if($WTIPO != 3)
											$wswfirma=4;
										else
											$wswfirma=5;
										$NOFILL .= $DATA[$i][4]."-";
									}
								}
							}
							$NOFILL=substr($NOFILL,0,strlen($NOFILL)-1);
						}
						if($wswfirma == 1 or $wswfirma == 2)
						{
							$SF=explode("-",$WSF);
							
							$query = "lock table ".$empresa."_".$wformulario." LOW_PRIORITY WRITE ";
							$err1 = mysql_query($query,$conex) or die("ERROR BLOQUEANDO ARCHIVO DE DATOS DE HISTORIA CLINICA : ".mysql_errno().":".mysql_error());
							if ($wsinfirma == 1)
							{
								$fecha = $wfechareg;
								$hora = $whorareg;
							}
							else
							{
								$fecha = date("Y-m-d");
								$hora = (string)date("H:i:s");
							}
							for ($i=0;$i<$num;$i++)
							{	
								if($DATA[$i][1] != "Titulo" and $DATA[$i][1] != "Subtitulo" and ($DATA[$i][5] == "A"  or ($DATA[$i][5] != "A" and $DATA[$i][5] == $wsex)))
								{
									if($DATA[$i][1] == "Label" or $DATA[$i][1] == "Link")
										$registro[$i][0] = "";
									if($DATA[$i][1] == "Imagen")
									{
										$registro[$i][0] = $DATA[$i][2];
									}
									if($wsinfirma == 0 or ($wsinfirma == 1 and !buscarID($SF,$i)))
									{
										if(strlen($registro[$i][0]) > 0)
										{
											$query = "insert ".$empresa."_".$wformulario." (medico, fecha_data, hora_data, movpro, movcon, movhis, moving, movtip, movdat, movusu, Seguridad) values ('";
											$query .=  $empresa."','";
											$query .=  $fecha."','";
											$query .=  $hora."','";
											$query .=  $wformulario."',";
											$query .=  $DATA[$i][0].",'";
											$query .=  $whis."','";
											$query .=  $wing."','";
											$query .=  $DATA[$i][1]."','";
											if($DATA[$i][1] == "Imagen")
												$query .=  $Hgrafica."','";
											elseif($DATA[$i][1] == "Tabla")
													$query .=  $registro[$i][0]."','";
												else
													$query .=  utf8_decode($registro[$i][0])."','";
											$query .=  $key."',";
											$query .=  "'C-".$empresa."')";
											//echo $i." ".$DATA[$i][1]." ".$registro[$i][0]."<br>";
											$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO DATOS DE HISTORIA CLINICA : ".mysql_errno().":".mysql_error());
										}
									}
									else
									{
										if($DATA[$i][1] == "Imagen")
											$query =  " update ".$empresa."_".$wformulario." set movdat = '".$Hgrafica."' ";
										else
											$query =  " update ".$empresa."_".$wformulario." set movdat = '".utf8_decode($registro[$i][0])."' ";
										$query .=  "  where fecha_data='".$wfechareg."' and hora_data='".$whorareg."' and movpro='".$wformulario."' and movcon='".$DATA[$i][0]."' and movhis='".$whis."' and moving='".$wing."'";
										$err1 = mysql_query($query,$conex) or die("ERROR ACTUALIZANDO DATOS DE HISTORIA CLINICA : ".mysql_errno().":".mysql_error());
									}
									$GOK=1;
									unset($wsa);
								}
							}
							if($wswfirma == 1)
							{
								$GOK=1;
								echo "<input type='HIDDEN' name= 'GOK' value='".$GOK."' id='GOK'>";
								$query = "insert ".$empresa."_".$wformulario." (medico, fecha_data, hora_data, movpro, movcon, movhis, moving, movtip, movdat, movusu, Seguridad) values ('";
								$query .=  $empresa."','";
								$query .=  $fecha."','";
								$query .=  $hora."','";
								$query .=  $wformulario."',";
								$query .=  "1000,'";
								$query .=  $whis."','";
								$query .=  $wing."','";
								$query .=  "Firma','";
								$query .=  substr($firma,4)."','";
								$query .=  $key."',";
								$query .=  "'C-".$empresa."')";
								//echo $i." ".$DATA[$i][1]." ".$registro[$i][0]."<br>";
								$err1 = mysql_query($query,$conex) or die("ERROR GRABANDO DATOS DE HISTORIA CLINICA (FIRMA) : ".mysql_errno().":".mysql_error());
								$firma="";
							}
							else
							{
								$GOK=2;
								echo "<input type='HIDDEN' name= 'GOK' value='".$GOK."' id='GOK'>";
							}
							$query = " UNLOCK TABLES";
							$err1 = mysql_query($query,$conex) or die("ERROR DESBLOQUEANDO TABLAS : ".mysql_errno().":".mysql_error());	
						}
					}
					
					if(!isset($wsa))
					{
						$wsinfirma=0;
						$wfechareg="";
						$whorareg="";
						$queryW = "SELECT Enctfo  from ".$empresa."_000001 ";
						$queryW .= " where Encpro = '".$wformulario."' ";
						$queryW .= "   and Encest = 'on' ";
						$err2 = mysql_query($queryW,$conex) or die(mysql_errno().":".mysql_error());
						$row2 = mysql_fetch_array($err2);
						$WTIPO=substr($row2[0],0,1);
						
						//QUERYS A LA BASE DE DATOS PARA DATOS HISTORICOS
						if($WTIPO == 1)
						{
							$queryJ  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".movdat from ".$empresa."_".$wformulario." ";
							$queryJ .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
							$queryJ .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
							$queryJ .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
							$queryJ .= "   and ".$empresa."_".$wformulario.".movcon = 1000 ";
							$queryJ .= " order by id desc ";
							$err = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
							$num = mysql_num_rows($err);
							if($num == 0)
							{
								$winga=(string)((integer)$wing - 1);
								$queryJ  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".movdat from ".$empresa."_".$wformulario." ";
								$queryJ .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
								$queryJ .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
								$queryJ .= "   and ".$empresa."_".$wformulario.".moving='".$winga."' ";
								$queryJ .= "   and ".$empresa."_".$wformulario.".movcon = 1000 ";
								$queryJ .= " order by id desc ";
								$err = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
								$num = mysql_num_rows($err);
								if($num > 0)
								{
									$CRONOS=array();
									$row = mysql_fetch_array($err);
									$whorareg=$row[1];
									$queryJ  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".movdat,".$empresa."_".$wformulario.".movusu from ".$empresa."_".$wformulario." ";
									$queryJ .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
									$queryJ .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
									$queryJ .= "   and ".$empresa."_".$wformulario.".moving='".$winga."' ";
									$queryJ .= "   and ".$empresa."_".$wformulario.".fecha_data='".$row[0]."' ";
									$queryJ .= "   and ".$empresa."_".$wformulario.".Hora_data='".$row[1]."' ";
									$queryJ .= " order by id  ";
									$err1 = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
									$num = mysql_num_rows($err1);
									for ($i=0;$i<$num;$i++)
									{
										$row1 = mysql_fetch_array($err1);
										$CRONOS[$row1[2]]=$row1[3];
									}
								}
							}
						}
						
						if($WTIPO != 3)
						{
							$queryJ  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".movdat from ".$empresa."_".$wformulario." ";
							$queryJ .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
							$queryJ .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
							$queryJ .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
							//$queryJ .= "   and ".$empresa."_".$wformulario.".movusu='".$key."' ";
							$queryJ .= "   and ".$empresa."_".$wformulario.".movcon<= 1000 ";
							$queryJ .= " order by id desc ";
							$err = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
							$num = mysql_num_rows($err);
							if($num > 0)
							{
								$SF=array();
								$row = mysql_fetch_array($err);
								if($row[2] < 1000 and $WTIPO != 1)
								{
									$wfechareg=$row[0];
									$whorareg=$row[1];
									$queryJ  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".movdat from ".$empresa."_".$wformulario." ";
									$queryJ .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
									$queryJ .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
									$queryJ .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
									$queryJ .= "   and ".$empresa."_".$wformulario.".movusu='".$key."' ";
									$queryJ .= "   and ".$empresa."_".$wformulario.".fecha_data='".$row[0]."' ";
									$queryJ .= "   and ".$empresa."_".$wformulario.".Hora_data='".$row[1]."' ";
									$queryJ .= " order by id  ";
									$err1 = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
									$num = mysql_num_rows($err1);
									$wsinfirma=1;
									$completo=0;
									for ($i=0;$i<$num;$i++)
									{
										$row1 = mysql_fetch_array($err1);
										$SF[$row1[2]][0]=1;
										$SF[$row1[2]][1]=$row1[3];
										if($row1[2] == 1000)
											$firma=$row1[3];
									}
								}
								else
								{
									if($WTIPO == 1)
									{
										$wfechareg=$row[0];
										$whorareg=$row[1];
										$queryJ  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_".$wformulario.".movcon,".$empresa."_".$wformulario.".movdat,".$empresa."_".$wformulario.".movusu from ".$empresa."_".$wformulario." ";
										$queryJ .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
										$queryJ .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
										$queryJ .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
										//$queryJ .= "   and ".$empresa."_".$wformulario.".movusu='".$key."' ";
										$queryJ .= "   and ".$empresa."_".$wformulario.".fecha_data='".$row[0]."' ";
										$queryJ .= "   and ".$empresa."_".$wformulario.".Hora_data='".$row[1]."' ";
										$queryJ .= " order by id  ";
										$err1 = mysql_query($queryJ,$conex) or die(mysql_errno().":".mysql_error());
										$num = mysql_num_rows($err1);
										$wsinfirma=1;
										$completo=0;
										if($row[2] >= 1000)
											$completo=1;
										$wuserG="";
										for ($i=0;$i<$num;$i++)
										{
											$row1 = mysql_fetch_array($err1);
											$SF[$row1[2]][0]=1;
											$SF[$row1[2]][1]=$row1[3];
											$wuserG=$row1[4];
											if($row1[2] == 1000)
												$firma=$row1[3];
										}
									}
								}
							}
						}
						else
						{
							$query  = " select ".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".hora_data,max(".$empresa."_".$wformulario.".movcon) as a from ".$empresa."_".$wformulario." "; 
							$query .= " where ".$empresa."_".$wformulario.".movhis='".$whis."' ";
							$query .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
							$query .= "   and  ".$empresa."_".$wformulario.".movusu='".$key."' "; 
							$query .= " group by 1,2  ";
							$query .= " having a < 1000 ";
							$err = mysql_query($query,$conex);
							$num = mysql_num_rows($err);
							if ($num>0)
							{
								$mensajes="<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CC99FF LOOP=-1>REGISTROS DE ESTE TIPO DE FORMULARIO SE ENCUENTRAN SIN FIRMA. RECUERDE REALIZAR ESTE PROCESO !!!! <b>GRACIAS</b></MARQUEE></FONT>";
								echo "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CC99FF LOOP=-1>REGISTROS DE ESTE TIPO DE FORMULARIO SE ENCUENTRAN SIN FIRMA. RECUERDE REALIZAR ESTE PROCESO !!!! <b>GRACIAS</b></MARQUEE></FONT>";
							}
						}
					}
					//                 0        1       2       3      4        5       6      7       8       9       10      11     12       13     14      15      16       17      18      19     20       21      22      23      24      25      26      27      28      29      30      31      32      33      34      35     36       37      38      39      40      41      42      43      44      45               
					$query = "SELECT Detpro, Detcon, Detorp, Dettip, Detdes, Detarc, Detcav, Detvde, Detnpa, Detvim, Detume, Detcol, Dethl7, Detjco, Detsiv, Detase, Detved, Detimp, Detimc, Detvco, Detvcr, Detobl, Detdep, Detcde, Deturl, Detfor, Detcco, Detcac, Detnse, Detfac, Enccol, Detcoa, Encdes, Detprs, Detalm, Detanm, Detlrb, Detdde, Encnco, Detcbu, Dettta, Detnbu, Detcua, Detccu, Detdpl, Detcro  from ".$empresa."_000001,".$empresa."_000002 ";
					$query .= " where Encpro = '".$wformulario."' ";
					$query .= "   and Encpro = Detpro ";
					$query .= "   and Detest = 'on' ";
					$query .= " Order by Detorp ";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$num = mysql_num_rows($err);
					if ($num>0)
					{
						if(!isset($registro))
							$registro=array();
						$orden=array();
						$items="";
						$pos=-1;
						if(!isset($wsa))
							$WSF="";
						$WSS="";
						for ($i=0;$i<$num;$i++)
						{
							$row = mysql_fetch_array($err);
							$Span=$row[30];
							$orden[$i][0]=chr(32)."C".$row[1].chr(32);
							$orden[$i][1]=chr(32)."R".$i.chr(32);
							$wtitulo = $row[32];
							$width = $row[38];
							$pos=$pos + 1;
							if(!isset($wsa))
							{
								$wfechaT = date("Y-m-d");
								$whoraT = date("H:i:s");
								if($wsinfirma == 1)
								{
									if(isset($SF[$row[1]][0]))
									{
										switch($row[3])
										{
											case "Texto":
												if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
													$WSF .= "W".$pos."-";
											break;
											case "Referencia":
												if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
													$WSF .= "T".$pos."-";
											break;
											case "Tabla":
												if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
													$WSF .= "M".$pos."-";
											break;
											case "Numero":
												if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
													$WSF .= "T".$pos."-";
											break;
											case "Formula":
												if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
													$WSF .= "T".$pos."-";
											break;
											case "Booleano":
												if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
													$WSF .= "C".$pos."-";
											break;
											case "Memo":
												if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
													$WSF .= "W".$pos."-";
											break;
											case "Password":
												if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
													$WSF .= "T".$pos."-";
											break;
											case "Fecha":
												if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
													$WSF .= "F".$pos."-";
											break;
											case "Hora":
												if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
													$WSF .= "H".$pos."-";
											break;
											case "Imagen":
												if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
												{
													$WSF .= "G".$pos."-";
													$Hgrafica=$SF[$row[1]][1];
												}
											break;
											case "Seleccion":
												if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
													if($row[33] == "off")
														$WSF .= "S".$pos."-";
													else
														$WSF .= "R".$pos."-";
											break;
											default:
												$WSF .= "O".$pos."-";
										}
										$registro[$i][0]=$SF[$row[1]][1];
									}
									else
									{
										$registro[$i][0]=$row[7];
									}
								}
								else
								{
									if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
									{
										if($row[3] != "Booleano" or ($row[3] == "Booleano" and $row[7] == "CHECKED"))
											$registro[$i][0]=$row[7];
										if($row[3] == "Fecha")
										{
											if($row[28] == 2)
												$WSF .= "F".$pos."-";
											$registro[$i][0]=date("Y-m-d");
										}
										if($row[3] == "Hora")
											$registro[$i][0]=date("H:i:s");
										if($row[3] == "Imagen")
											$Hgrafica="";
									}
									if($row[45] == "on" and $wing > "1" and $WTIPO == 1 and isset($CRONOS[$row[1]]))
									{
										$registro[$i][0]=$CRONOS[$row[1]];
									}
								}
							}
							else
							{
								if($row[3] == "Booleano" and isset($registro[$i][0]) and $registro[$i][0] != "CHECKED")
									unset($registro[$i][0]);
							}

							switch($row[3])
							{
								case "Texto":
									if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
									{
										$items .= "W".$pos."-";
										$WSS .= $row[28]."-";
									}
								break;
								case "Referencia":
									if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
									{
										$items .= "T".$pos."-";
										$WSS .= $row[28]."-";
									}
								break;
								case "Tabla":
									if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
									{
										$items .= "M".$pos."-";
										$WSS .= $row[28]."-";
									}
								break;
								case "Numero":
									if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
									{
										$items .= "T".$pos."-";
										$WSS .= $row[28]."-";
									}
								break;
								case "Formula":
									if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
									{
										$items .= "T".$pos."-";
										$WSS .= $row[28]."-";
									}
								break;
								case "Booleano":
									if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
									{
										$items .= "C".$pos."-";
										$WSS .= $row[28]."-";
										if(isset($registro[$i][0]) and $registro[$i][0] != "CHECKED")
											unset($registro[$i][0]);
									}
								break;
								case "Memo":
									if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
									{
										$items .= "W".$pos."-";
										$WSS .= $row[28]."-";
									}
								break;
								case "Password":
									if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
									{
										$items .= "T".$pos."-";
										$WSS .= $row[28]."-";
									}
								break;
								case "Fecha":
									if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
									{
										$items .= "F".$pos."-";
										$WSS .= $row[28]."-";
									}
								break;
								case "Hora":
									if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
									{
										$items .= "H".$pos."-";
										$WSS .= $row[28]."-";
									}
								break;
								case "Imagen":
									if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
									{
										$items .= "G".$pos."-";
										$WSS .= $row[28]."-";
									}
								break;
								case "Seleccion":
									if($row[15] == "A" or ($row[15] != "A" and $row[15] == $wsex))
									{
										if($row[33] == "off")
										{
											$items .= "S".$pos."-";
											$WSS .= $row[28]."-";
										}
										else
										{
											$items .= "R".$pos."-";
											$WSS .= $row[28]."-";
										}
									}
								break;
							}
							for ($j=1;$j<=46;$j++)
							{
								$registro[$i][$j]=$row[$j-1];
								if($j == 32 and $registro[$i][32] < 1)
									$registro[$i][32] = 1;
								if($j == 37 and $registro[$i][37] < 1)
									$registro[$i][37] = 1;
							}
						}
						if(!isset($Hgrafica))
							$Hgrafica="";
						if(!isset($wsa))
							$WSF .= "FIN";
						$items .= "FIN";
						$WSS .= "FIN";
					}
					//echo $items."<br>";
					if($num > 0)
					{
						if(isset($ok))
						{
							//echo "Valor de ok : ".$ok."<br>";
							//echo "Valor de Fecha : ".$wfechaT."<br>";
							//echo "Valor de Hora : ".$whoraT."<br>";
							unset($ok);
						}
						if(isset($position))
							$position=substr($items,0,2);
						$wsa=1;
						echo "<input type='HIDDEN' name= 'wsa' value='".$wsa."'>";
						echo "<input type='HIDDEN' name= 'wcedula' value='".$wcedula."'>";
						echo "<input type='HIDDEN' name= 'wtipodoc' value='".$wtipodoc."'>";
						echo "<input type='HIDDEN' name= 'wfechaT' value='".$wfechaT."'>";
						echo "<input type='HIDDEN' name= 'whoraT' value='".$whoraT."'>";
						echo "<input type='HIDDEN' name= 'width' value='".$width."'>";
						echo "<input type='HIDDEN' name= 'num' value='".$num."'>";
						echo "<input type='HIDDEN' name= 'WSF' id='WSF' value='".$WSF."'>";
						echo "<input type='HIDDEN' name= 'WSS' id='WSS' value='".$WSS."'>";
						echo "<input type='HIDDEN' name= 'WTIPO' id='WTIPO' value='".$WTIPO."'>";
						echo "<input type='HIDDEN' name= 'Hgrafica' value='".$Hgrafica."' id='Hgrafica'>";
							
						
						$n=$registro[0][31];
						
						// INICIALIZACION DEL TITULO DEL FORMULARIO
						if(!isset($wtitframe))
						{
							echo '<script language="Javascript">';
							//echo "     debugger;";
							echo '		var obj = parent.parent.demograficos.document.all.txttitulo;';
							echo '		if(obj)';
							echo '		{';
							echo "			var divAux = document.createElement( 'div');";
							echo "			divAux.innerHTML = '".htmlentities($wtitulo)."';";
							echo "			obj.value = divAux.innerHTML.toUpperCase();";
							echo '		} ';
							echo '		var obj1 = parent.parent.demograficos.document.all.txtformulario;';
							echo '		if(obj1)';
							echo '		{';
							echo "			var divAux = document.createElement( 'div');";
							echo "			divAux.innerHTML = '".htmlentities($wformulario)."';";
							echo "			obj1.value = divAux.innerHTML.toUpperCase();";
							echo '		} ';
							echo '</script>';
						}
						else
							echo "<input type='HIDDEN' name= 'wtitframe' value='".$wtitframe."'>";
						//if(isset($wswfirma) and isset($ok))
							//echo "valor de ok : ".$ok. " ".$wswfirma."<br>";
						$mensajes="";
						if(isset($wswfirma ) and $wswfirma == 3)
						{
							$completo = 0;
							$mensajes .= "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FEAAA4 LOOP=-1>SE INTENTO GRABAR UN FORMULARIO CON FIRMA ERRONEA INTENTELO NUEVAMENTE!!!! <b>GRACIAS</b></MARQUEE></FONT><br>";
							echo "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FEAAA4 LOOP=-1>SE INTENTO GRABAR UN FORMULARIO CON FIRMA ERRONEA INTENTELO NUEVAMENTE!!!! <b>GRACIAS</b></MARQUEE></FONT>";
						}
						if(isset($wswfirma ) and $wswfirma == 4)
						{
							$completo = 0;
							$mensajes .= "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FFFF66 LOOP=-1>SE INTENTO GRABAR UN FORMULARIO CON FIRMA CORRECTA Y CAMPOS OBLIGATORIOS INCOMPLETOS : (".htmlentities($NOFILL).") INTENTELO NUEVAMENTE!!!! <b>GRACIAS</b></MARQUEE></FONT><br>";
							echo "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FFFF66 LOOP=-1>SE INTENTO GRABAR UN FORMULARIO CON FIRMA CORRECTA Y CAMPOS OBLIGATORIOS INCOMPLETOS : (".htmlentities($NOFILL).") INTENTELO NUEVAMENTE!!!! <b>GRACIAS</b></MARQUEE></FONT>";
						}
						if(isset($wswfirma ) and $wswfirma == 5)
						{
							$completo = 0;
							$mensajes .= "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99FFFF LOOP=-1>SE INTENTO GRABAR UN FORMULARIO TIPO 3, CAMPOS OBLIGATORIOS INCOMPLETOS : (".htmlentities($NOFILL).") INTENTELO NUEVAMENTE!!!! <b>GRACIAS</b></MARQUEE></FONT><br>";
							echo "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99FFFF LOOP=-1>SE INTENTO GRABAR UN FORMULARIO TIPO 3, CAMPOS OBLIGATORIOS INCOMPLETOS : (".htmlentities($NOFILL).") INTENTELO NUEVAMENTE!!!! <b>GRACIAS</b></MARQUEE></FONT>";
						}
						if(isset($wswfirma ))
							echo "<input type='HIDDEN' id='wswfirma' name='wswfirma' value='".$wswfirma."'>";
						else
						{
							$wswfirma=-1;
							echo "<input type='HIDDEN' id='wswfirma' name='wswfirma' value='".$wswfirma."'>";
						}
						if($wsinfirma == 1)
						{
							if(isset($completo) and $completo == 1)
							{
								$mensajes .= "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#00FF66 LOOP=-1>EL FORMULARIO SE HA GRABAD0 CON FIRMA DIGITAL - ESTE REGISTRO SE DILIGENCIA SOLAMENTE UNA VEZ POR HISTORIA - INGRESO !!!! <b>GRACIAS</b></MARQUEE></FONT><br>";
								echo "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#00FF66 LOOP=-1>EL FORMULARIO SE HA GRABAD0 CON FIRMA DIGITAL - ESTE REGISTRO SE DILIGENCIA SOLAMENTE UNA VEZ POR HISTORIA - INGRESO !!!! <b>GRACIAS</b></MARQUEE></FONT>";
							}
							else
							{
								if(!isset($wuserG))
								{
									$mensajes .= "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FFCC66 LOOP=-1>EL FORMULARIO SE GRABO SIN FIRMA DIGITAL - DILIGENCIELO Y FIRMELO ANTES DE GRABAR OTRO REGISTRO !!!! <b>GRACIAS</b></MARQUEE></FONT><br>";
									echo "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FFCC66 LOOP=-1>EL FORMULARIO SE GRABO O SE HABIA GRABADO SIN FIRMA DIGITAL - DILIGENCIELO Y FIRMELO ANTES DE GRABAR OTRO REGISTRO !!!! <b>GRACIAS</b></MARQUEE></FONT>";
								}
								elseif(isset($wuserG) and $wuserG != $key)
								{
									$query = "SELECT Descripcion from usuarios ";
									$query .= " where codigo = '".$wuserG."' ";
									$err2 = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
									$num2 = mysql_num_rows($err2);
									if ($num2>0)
									{
										$row2 = mysql_fetch_array($err2);
										$wmedico = $row2[0];
									}
									else
										$wmedico = "NO ESPECIFICO";
									$mensajes .= "<font size=4><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FEAAA4 LOOP=-1>EL FORMULARIO SE GRABO SIN FIRMA DIGITAL POR ".$wmedico." - CONTACTELO PARA TERMINAR EL REGISTRO !!!! <b>GRACIAS</b></MARQUEE></FONT><br>";
									echo $mensajes;
								}
							}
							//echo $WSF."<br>";
							$SF=explode("-",$WSF);
							$SF1=array();
							for ($h=0;$h<count($SF);$h++)
								$SF1[$SF[$h]]=1;
						}
						echo "<table border=0 cellspacing=0>";
						echo "<tr>";
						$rwidth=(integer)($width/$Span);
						for ($i=0;$i<$Span;$i++)
						{
							echo "<td id=tiporegla width=".$rwidth." colspan=1>&nbsp;</td>";
						}
						echo "</tr>";
						//$index=1000;
						//$ancho=1200;
						//$alto=250;
						//echo "<tr><td id=tipoL02V colspan=".$Span." align=center><A HREF='#' id='btnModal".$index."' name='btnModal".$index."' class=tipo3V onClick='javascript:mostrarFlotante(\"\",\"nombreIframe\",\"http://".$CPU."/matrix/HCE/Procesos/HCE.php?accion=W2&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wformulario=".$row[0]."&whis=".$whis."&wing=".$wing."&wtitframe=no\",\"".$alto."\",\"".$ancho."\");'>Vistas Asociadas</A></td></tr>";
						$campos=0;
						$totdiv=0;
						$pasdiv=0;
						if($registro[0][4] == "Titulo" and $registro[0][32] == $n)
						{
							$totdiv++;
							echo "<tr OnClick='toggleDisplay(div".$totdiv.")'>";
						}
						else
							echo "<tr>";
					}
					for ($i=0;$i<$num;$i++)
					{
						if($registro[$i][19] === "on")
							$salto=" ";
						else
							$salto="<br>";
						if($campos >= $n)
						{
							echo "</tr>";
							if($totdiv > 0 and $registro[$i][4] == "Titulo")
								echo "</table></td></tr>";
							if($i > 0 and $registro[$i-1][4] == "Titulo" and $registro[$i-1][32] >= $n)
							{
								$pasdiv++;
								//echo "<TBODY id=div".$totdiv."><tr>";
								echo "<tr id=div".$totdiv."><td colspan=".$Span."><table width='100%' border=0 cellspacing=1 cellpadding=0>";
							}
							//else
								//echo "<tr>";
							if($registro[$i][4] == "Titulo" and $registro[$i][32] >= $n)
							{
								$totdiv++;
								echo "<tr OnClick='toggleDisplay(div".$totdiv.")'>";
							}
							else
								echo "<tr>";
							$campos=0;
						}
						if($registro[$i][22] == "on")
						{
							$OBL="O";
							$CHECK="check";
						}
						else
						{
							$OBL="";
							$CHECK="";
						}
						if($registro[$i][11] == "" or $registro[$i][11] == "NO APLICA")
							$UDM="";
						else
							$UDM=" ".htmlentities($registro[$i][11]);
							
						if($registro[$i][4] != "Titulo" and $registro[$i][16] != "A" and $registro[$i][16] != $wsex)
							$registro[$i][4]="Noaplica";
							
						switch($registro[$i][4])
						{
							case "Noaplica":
								if($registro[$i][32]>$n)
									$registro[$i][32]=$n;
								$faltan=$n - $campos;
								if($registro[$i][32] <= $faltan and $faltan > 0)
								{
									$alto=$registro[$i][35];
									$ancho=$registro[$i][36];
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									$col=($span * 80) / ($n * 2);
									echo "<td colspan=".$span." width=".$rwidth." id=tipoL02></td>";
									$campos += $registro[$i][32];
								}
								else
								{
									for ($w=0;$w<$faltan;$w++)
										echo "<td id=tipoL02></td>";
									echo "</tr>";
									$campos = 0;
									$alto=$registro[$i][35];
									$ancho=$registro[$i][36];
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									$col=($span * 80) / ($n * 2);
									echo "<tr><td colspan=".$span." width=".$rwidth." id=tipoL02></td>";
									$campos += $registro[$i][32];	
								}
							break;
							case "Texto":
								if($registro[$i][32]>$n)
									$registro[$i][32]=$n;
								$faltan=$n - $campos;
								if($registro[$i][32] <= $faltan and $faltan > 0)
								{
									$alto=$registro[$i][35];
									$ancho=$registro[$i][36];
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									$col=($span * 80) / ($n * 2);
									//echo "<td colspan=".$span." width=".$rwidth." id=tipoL02>".htmlentities($registro[$i][9]).$salto."<input type='TEXT' name='registro[".$i."][0]' size=".$ancho." maxlength=80 id='W".$i."' value='".htmlentities(utf8_decode($registro[$i][0]))."' class=tipo3".$OBL.">".$UDM."</td>";
									echo "<td colspan=".$span." width=".$rwidth." id=tipoL02>".htmlentities($registro[$i][9]).$salto."<input type='TEXT' name='registro[".$i."][0]' size=".$ancho." maxlength=80 id='W".$i."' value='".htmlentities($registro[$i][0])."' class=tipo3".$OBL.">".$UDM."</td>";
									$campos += $registro[$i][32];
								}
								else
								{
									for ($w=0;$w<$faltan;$w++)
										echo "<td id=tipoL02></td>";
									echo "</tr>";
									$campos = 0;
									$alto=$registro[$i][35];
									$ancho=$registro[$i][36];
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									$col=($span * 80) / ($n * 2);
									//echo "<tr><td colspan=".$span." width=".$rwidth." id=tipoL02>".htmlentities($registro[$i][9]).$salto."<input type='TEXT' name='registro[".$i."][0]' size=".$ancho." maxlength=80 id='W".$i."' value='".htmlentities(utf8_decode($registro[$i][0]))."' class=tipo3".$OBL.">".$UDM."</td>";
									echo "<tr><td colspan=".$span." width=".$rwidth." id=tipoL02>".htmlentities($registro[$i][9]).$salto."<input type='TEXT' name='registro[".$i."][0]' size=".$ancho." maxlength=80 id='W".$i."' value='".htmlentities($registro[$i][0])."' class=tipo3".$OBL.">".$UDM."</td>";
									$campos += $registro[$i][32];	
								}
								//if(isset($SF1["T".$i]))
								if(isset($completo) and $completo == 1)
									echo "<script> soloLectura(document.getElementById('"."W".$i."'))</script>";
									
							break;
							case "Referencia":
								if($registro[$i][32]>$n)
									$registro[$i][32]=$n;
								$faltan=$n - $campos;
								if($registro[$i][32] <= $faltan and $faltan > 0)
								{
									$alto=$registro[$i][35];
									$ancho=$registro[$i][36];
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									$col=($span * 80) / ($n * 2);
									$query = "SELECT Movdat from ".$empresa."_".$registro[$i][6]." where movpro='".$registro[$i][6]."' and movcon='".$registro[$i][7]."' and movhis='".$whis."' and moving='".$wing."' order by id desc";
									$err1 = mysql_query($query,$conex);
									$num1 = mysql_num_rows($err1);
									if ($num1>0)
									{
										$row1 = mysql_fetch_array($err1);
										$registro[$i][0]=$row1[0];
									}
									elseif ($registro[$i][46] == "on" and $wing > "1")
										{
											$winga=(string)((integer)$wing - 1);
											$query = "SELECT Movdat from ".$empresa."_".$registro[$i][6]." where movpro='".$registro[$i][6]."' and movcon='".$registro[$i][7]."' and movhis='".$whis."' and moving='".$winga."' order by id desc";
											$err1 = mysql_query($query,$conex);
											$num1 = mysql_num_rows($err1);
											if ($num1>0)
											{
												$row1 = mysql_fetch_array($err1);
												$registro[$i][0]=$row1[0];
											}
										}
										else
										{
											$registro[$i][0]="";
										}
									$position="T".$i;
									$id="ajaxview('19','".$empresa."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','')";
									echo "<td colspan=".$span." width=".$rwidth." id=tipoL02>".htmlentities($registro[$i][9]).$salto."<input type='TEXT' name='registro[".$i."][0]' size=".$ancho." maxlength=80 id='T".$i."' value='".htmlentities($registro[$i][0])."' onkeypress='tecladonull()' class=tipo3".$OBL." onClick=".$id.">".$UDM."</td>";
									$campos += $registro[$i][32];
								}
								else
								{
									for ($w=0;$w<$faltan;$w++)
										echo "<td id=tipoL02></td>";
									echo "</tr>";
									$campos = 0;
									$alto=$registro[$i][35];
									$ancho=$registro[$i][36];
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									$col=($span * 80) / ($n * 2);
									$query = "SELECT Movdat from ".$empresa."_".$registro[$i][6]." where movpro='".$registro[$i][6]."' and movcon='".$registro[$i][7]."' and movhis='".$whis."' and moving='".$wing."' order by id desc";
									$err1 = mysql_query($query,$conex);
									$num1 = mysql_num_rows($err1);
									if ($num1>0)
									{
										$row1 = mysql_fetch_array($err1);
										$registro[$i][0]=$row1[0];
									}
									elseif ($registro[$i][46] == "on" and $wing > "1")
										{
											$winga=(string)((integer)$wing - 1);
											$query = "SELECT Movdat from ".$empresa."_".$registro[$i][6]." where movpro='".$registro[$i][6]."' and movcon='".$registro[$i][7]."' and movhis='".$whis."' and moving='".$winga."' order by id desc";
											$err1 = mysql_query($query,$conex);
											$num1 = mysql_num_rows($err1);
											if ($num1>0)
											{
												$row1 = mysql_fetch_array($err1);
												$registro[$i][0]=$row1[0];
											}
										}
										else
										{
											$registro[$i][0]="";
										}
									$position="T".$i;
									$id="ajaxview('19','".$empresa."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','')";
									echo "<tr><td colspan=".$span." width=".$rwidth." id=tipoL02>".htmlentities($registro[$i][9]).$salto."<input type='TEXT' name='registro[".$i."][0]' size=".$ancho." maxlength=80 id='T".$i."' value='".htmlentities($registro[$i][0])."' onkeypress='tecladonull()' class=tipo3".$OBL." onClick=".$id.">".$UDM."</td>";
									$campos += $registro[$i][32];	
								}
								//if(isset($SF1["T".$i]))
								if(isset($completo) and $completo == 1)
									echo "<script> soloLectura(document.getElementById('"."T".$i."'))</script>";
							break;
							case "Numero":
								if($registro[$i][32]>$n)
									$registro[$i][32]=$n;
								$faltan=$n - $campos;
								if($registro[$i][32] <= $faltan and $faltan > 0)
								{
									$alto=$registro[$i][35];
									$ancho=$registro[$i][36];
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									$col=($span * 30) / ($n * 2);
									echo "<td colspan=".$span." width=".$rwidth." id=tipoL02 align=center>".htmlentities($registro[$i][9]).$salto."<input type='TEXT' name='registro[".$i."][0]' size=".$ancho." maxlength=30 id='T".$i."' value='".htmlentities($registro[$i][0])."' onkeypress='return teclado(event)' class=tipo3".$OBL.">".$UDM."</td>";
									$campos += $registro[$i][32];
								}
								else
								{
									for ($w=0;$w<$faltan;$w++)
										echo "<td id=tipoL02></td>";
									echo "</tr>";
									$campos = 0;
									$alto=$registro[$i][35];
									$ancho=$registro[$i][36];
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									
									echo "<tr><td colspan=".$span." width=".$rwidth." id=tipoL02 align=center>".htmlentities($registro[$i][9]).$salto."<input type='TEXT' name='registro[".$i."][0]' size=".$ancho." maxlength=30 id='T".$i."' value='".htmlentities($registro[$i][0])."' onkeypress='return teclado(event)' class=tipo3".$OBL.">".$UDM."</td>";
									$campos += $registro[$i][32];	
								}
								//if(isset($SF1["T".$i]))
								if(isset($completo) and $completo == 1)
									echo "<script> soloLectura(document.getElementById('"."T".$i."'))</script>";
							break;
							case "Formula":
								$registro[$i][26]=strtoupper($registro[$i][26]);
								$registro[$i][26]=formula1($registro[$i][26]);
								for ($w=0;$w<$num;$w++)
								{
									$registro[$i][26]=str_replace($orden[$w][0],$orden[$w][1],$registro[$i][26]);
								}
								$registro[$i][26]=formula($registro[$i][26]);
								@eval($registro[$i][26]);
								if($registro[$i][32]>$n)
									$registro[$i][32]=$n;
								$faltan=$n - $campos;
								if($registro[$i][32] <= $faltan and $faltan > 0)
								{
									$alto=$registro[$i][35];
									$ancho=$registro[$i][36];
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									$col=($span * 120) / ($n * 2);
									$position="T".$i;
									$id="ajaxview('19','".$empresa."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','')";
									//echo "<td colspan=".$span." id=tipoL02 align=center>".$registro[$i][9]."<br><input type='TEXT' name='registro[".$i."][0]' size=".$col." maxlength=80 id='T".$i."' value='".$registro[$i][0]."' readonly='readonly' class=tipo3 OnClick='enter1()'></td>";
									echo "<td colspan=".$span." width=".$rwidth." id=tipoL02 align=center>".htmlentities($registro[$i][9]).$salto."<input type='TEXT' name='registro[".$i."][0]' size=".$ancho." maxlength=80 id='T".$i."' value='".htmlentities($registro[$i][0])."'  class=tipo3".$OBL." onClick=".$id.">".$UDM."</td>";
									$campos += $registro[$i][32];
								}
								else
								{
									for ($w=0;$w<$faltan;$w++)
										echo "<td id=tipoL02></td>";
									echo "</tr>";
									$campos = 0;
									$alto=$registro[$i][35];
									$ancho=$registro[$i][36];
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									$col=($span * 120) / ($n * 2);
									$position="T".$i;
									$id="ajaxview('19','".$empresa."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','')";
									//echo "<tr><td colspan=".$span." id=tipoL02 align=center>".$registro[$i][9]."<br><input type='TEXT' name='registro[".$i."][0]' size=".$col." maxlength=80 id='T".$i."' value='".$registro[$i][0]."' readonly='readonly' class=tipo3 OnClick='enter1()'></td>";
									echo "<tr><td colspan=".$span." width=".$rwidth." id=tipoL02 align=center>".htmlentities($registro[$i][9]).$salto."<input type='TEXT' name='registro[".$i."][0]' size=".$ancho." maxlength=80 id='T".$i."' value='".htmlentities($registro[$i][0])."'  class=tipo3".$OBL." onClick=".$id.">".$UDM."</td>";
									$campos += $registro[$i][32];	
								}
								//if(isset($SF1["T".$i]))
								if(isset($completo) and $completo == 1)
									echo "<script> soloLectura(document.getElementById('"."T".$i."'))</script>";
							break;
							case "Booleano":
								if($registro[$i][32]>$n)
									$registro[$i][32]=$n;
								$faltan=$n - $campos;
								if($registro[$i][32] <= $faltan and $faltan > 0)
								{
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									if(isset($registro[$i][0]))
										echo "<td colspan=".$span." width=".$rwidth." id=tipoL02P".$CHECK."><input type='checkbox' name='registro[".$i."][0]' id='C".$i."' class=tipoL02M".$OBL." checked>".htmlentities($registro[$i][9]).$salto."</td>";
									else
										echo "<td colspan=".$span." width=".$rwidth." id=tipoL02P".$CHECK."><input type='checkbox' name='registro[".$i."][0]' id='C".$i."' class=tipoL02M".$OBL.">".htmlentities($registro[$i][9]).$salto."</td>";
									$campos += $registro[$i][32];
								}
								else
								{
									for ($w=0;$w<$faltan;$w++)
										echo "<td id=tipoL02></td>";
									echo "</tr>";
									$campos = 0;
									$span=$registro[$i][32] ;
									$rwidth=(integer)(($width * $span)/$Span);
									if(isset($registro[$i][0]))
										echo "<tr><td colspan=".$span." width=".$rwidth." id=tipoL02P".$CHECK."><input type='checkbox' name='registro[".$i."][0]' id='C".$i."' class=tipoL02M".$OBL." checked>".htmlentities($registro[$i][9]).$salto."</td>";
									else
										echo "<tr><td colspan=".$span." width=".$rwidth." id=tipoL02P".$CHECK."><input type='checkbox' name='registro[".$i."][0]' id='C".$i."' class=tipoL02M".$OBL.">".htmlentities($registro[$i][9]).$salto."</td>";
									$campos += $registro[$i][32];	
								}
								//if(isset($SF1["C".$i]))
								if(isset($completo) and $completo == 1)
									echo "<script> soloLectura(document.getElementById('"."C".$i."'))</script>";
							break;
							case "Titulo":
								if($registro[$i][32]>$n)
									$registro[$i][32]=$n;
								$faltan=$n - $campos;
								if($registro[$i][32] <= $faltan and $faltan > 0)
								{
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									if($registro[$i][32] == $n)
										echo "<td id=tipoL06 colspan=".$span.">".htmlentities($registro[$i][9])."</td>";
									else
										echo "<td id=tipoL06 colspan=".$span." width=".$rwidth.">".htmlentities($registro[$i][9])."</td>";
									$campos += $registro[$i][32];
								}
								else
								{
									for ($w=0;$w<$faltan;$w++)
										echo "<td id=tipoL02></td>";
									echo "</tr>";
									if($totdiv > 0)
										echo "</table></td></tr>";
									$campos = 0;
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									if($registro[$i][32] == $n)
									{
										$totdiv++;
										echo "<tr OnClick='toggleDisplay(div".$totdiv.")'><td id=tipoL06 colspan=".$span.">".htmlentities($registro[$i][9])."</td>";
									}
									else
										echo "<tr><td id=tipoL06 colspan=".$span." width=".$rwidth.">".htmlentities($registro[$i][9])."</td>";
									$campos += $registro[$i][32];	
								}
							break;
							case "Subtitulo":
								if($registro[$i][32]>$n)
									$registro[$i][32]=$n;
								if($registro[$i][32] == $n)
									$css="tipoL07";
								else
									$css="tipoL07B";
								$faltan=$n - $campos;
								if($registro[$i][32] <= $faltan and $faltan > 0)
								{
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									echo "<td id=".$css." colspan=".$span." width=".$rwidth.">&nbsp;&nbsp;&nbsp;".htmlentities($registro[$i][9])."</td>";
									$campos += $registro[$i][32];
								}
								else
								{
									for ($w=0;$w<$faltan;$w++)
										echo "<td id=tipoL02></td>";
									echo "</tr>";
									$campos = 0;
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									echo "<tr><td id=".$css." colspan=".$span." width=".$rwidth.">&nbsp;&nbsp;&nbsp;".htmlentities($registro[$i][9])."</td>";
									$campos += $registro[$i][32];	
								}
							break;
							case "Label":
								if($registro[$i][32]>$n)
									$registro[$i][32]=$n;
								$faltan=$n - $campos;
								if($registro[$i][32] <= $faltan and $faltan > 0)
								{
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									echo "<td id=tipoL07A colspan=".$span." width=".$rwidth.">&nbsp;&nbsp;&nbsp;".htmlentities($registro[$i][9])."</td>";
									$campos += $registro[$i][32];
								}
								else
								{
									for ($w=0;$w<$faltan;$w++)
										echo "<td id=tipoL02></td>";
									echo "</tr>";
									$campos = 0;
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									echo "<tr><td id=tipoL07A colspan=".$span." width=".$rwidth.">&nbsp;&nbsp;&nbsp;".htmlentities($registro[$i][9])."</td>";
									$campos += $registro[$i][32];	
								}
							break;
							case "Memo":
								if($registro[$i][32]>$n)
									$registro[$i][32]=$n;
								$faltan=$n - $campos;
								if($registro[$i][32] <= $faltan and $faltan > 0)
								{
									$alto=$registro[$i][35];
									$ancho=$registro[$i][36];
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									$col=($span * 150) / ($n * 2);
									//echo "<td bgcolor=#E8EEF7 colspan=".$span." width=".$rwidth." align=center><font face='Arial' size=2 color=#000066><b>".htmlentities($registro[$i][9])."</b></font>".$salto."<textarea name='registro[".$i."][0]' cols=".$ancho." rows=".$alto." id='W".$i."' class=tipo3".$OBL.">".htmlentities(utf8_decode($registro[$i][0]))."</textarea></td>";
									echo "<td bgcolor=#E8EEF7 colspan=".$span." width=".$rwidth." align=center><font face='Arial' size=2 color=#000066><b>".htmlentities($registro[$i][9])."</b></font>".$salto."<textarea name='registro[".$i."][0]' cols=".$ancho." rows=".$alto." id='W".$i."' class=tipo3".$OBL.">".htmlentities($registro[$i][0])."</textarea></td>";
									$campos += $registro[$i][32];
								}
								else
								{
									for ($w=0;$w<$faltan;$w++)
										echo "<td id=tipoL02></td>";
									echo "</tr>";
									$campos = 0;
									$alto=$registro[$i][35];
									$ancho=$registro[$i][36];
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									$col=($span * 150) / ($n * 2);
									echo "<tr><td bgcolor=#E8EEF7 colspan=".$span." width=".$rwidth." align=center><font face='Arial' size=2 color=#000066><b>".htmlentities($registro[$i][9])."</b></font>".$salto."<textarea name='registro[".$i."][0]' cols=".$ancho." rows=".$alto." id='W".$i."' class=tipo3".$OBL.">".htmlentities($registro[$i][0])."</textarea></td>";
									$campos += $registro[$i][32];	
								}
								//if(isset($SF1["W".$i]))
								if(isset($completo) and $completo == 1)
								{
									echo "<script> soloLectura(document.getElementById('"."W".$i."'))</script>";
								}
							break;
							case "Imagen":
								if($registro[$i][32]>$n)
									$registro[$i][32]=$n;
								$faltan=$n - $campos;
								
								if($registro[$i][32] <= $faltan and $faltan > 0)
								{
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									echo "<td bgcolor=#E8EEF7 colspan=".$span." width=".$rwidth." align=left><font face='Arial' size=2 color=#000066><b>".htmlentities($registro[$i][9])."</b> <br> Haga Click en la Imagen Para Ver los Recuadros</font>".$salto."<IMG SRC='/matrix/images/medical/HCE/".$registro[$i][6]."' id='mainImage' onClick='pintardivs()' /></td>";
									echo "<script type='text/javascript'>";
									echo "  var tipmage = new Tipmage('mainImage', true);";
									echo "  tipmage.startup();";
									echo "  varable = document.getElementById('Hgrafica').value;";
									echo "  var ID = 1;";
									echo "  if(varable.length > 0)";
									echo "  {";
									echo "    frag1 = varable.split('^');";
									echo " 	  for (i=1;i<frag1.length;i++)";
									echo " 	  {";
									echo " 		frag2 = frag1[i].split('~');";  
									echo "      tipmage.setTooltip(frag2[1],frag2[2],frag2[3],frag2[4],frag2[5],frag2[0]);";
									echo "    }";
									echo "  };";
									echo "</script>";
									echo "<script type='text/javascript'>";
									echo "  tipmage.onInsert = function (identifier,posx,posy,width,height,text) ";
									echo "  {";
									echo "    document.getElementById('Hgrafica').value = document.getElementById('Hgrafica').value + '^' + parseInt(identifier)+'~'+posx+'~'+posy+'~'+width+'~'+height+'~'+text;";
									//echo "    alert(document.getElementById('Hgrafica').value);";
									echo "  };";
									echo "</script>";
									echo "<script type='text/javascript'>";
									echo "  tipmage.onUpdate = function (identifier,posx,posy,width,height,text) ";
									echo "  {";
									echo "    final = '';";
									echo "    arreglo = document.getElementById('Hgrafica').value;";
									echo "    frag1 = arreglo.split('^');";
									echo " 	  for (i=1;i<frag1.length;i++)";
									echo " 	  {";
									echo " 		frag2 = frag1[i].split('~');";
									echo "      if(frag2[0] == identifier)";
									echo "		{";
									echo "			frag2[1]=posx;";
									echo "			frag2[2]=posy;";
									echo "			frag2[3]=width;";
									echo "			frag2[4]=height;";
									echo "			frag2[5]=text;";
									echo "          frag1[i]=frag2[0]+'~'+frag2[1]+'~'+frag2[2]+'~'+frag2[3]+'~'+frag2[4]+'~'+frag2[5];";
									echo "		}";
									echo " 		final += '^' + frag1[i];";
									echo "    }";
									echo "    document.getElementById('Hgrafica').value = final;";
									//echo "    alert(document.getElementById('Hgrafica').value);";
									echo "  };";
									echo "</script>";
									echo "<script type='text/javascript'>";
									echo "  tipmage.onDelete = function (identifier,posx,posy,width,height,text) ";
									echo "  {";
									echo "    final = '';";
									echo "    arreglo = document.getElementById('Hgrafica').value;";
									echo "    frag1 = arreglo.split('^');";
									echo " 	  for (i=1;i<frag1.length;i++)";
									echo " 	  {";
									echo " 		frag2 = frag1[i].split('~');";
									echo "      if(frag2[0] != identifier)";
									echo "		{";
									echo "          alert(frag1[i]);";
									echo "			final = final + '^' + frag1[i];";
									echo "		}";
									echo "    }";
									echo "    document.getElementById('Hgrafica').value = final;";
									//echo " pintardivs();";
									//echo "    alert(document.getElementById('Hgrafica').value);";
									echo "  };";
									echo "</script>";
									$campos += $registro[$i][32];
								}
								else
								{
									for ($w=0;$w<$faltan;$w++)
										echo "<td id=tipoL02></td>";
									echo "</tr>";
									$campos = 0;
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									echo "<tr><td bgcolor=#E8EEF7 colspan=".$span." width=".$rwidth." align=left><font face='Arial' size=2 color=#000066><b>".htmlentities($registro[$i][9])."</b> <br> Haga Click en la Imagen Para Ver los Recuadros</font>".$salto."<IMG SRC='/matrix/images/medical/HCE/".$registro[$i][6]."' id='mainImage'  onClick='pintardivs()' /></td>";
									echo "<script type='text/javascript'>";
									echo "  var tipmage = new Tipmage('mainImage', true);";
									echo "  tipmage.startup();";
									echo "  varable = document.getElementById('Hgrafica').value;";
									echo "  var ID = 1;";
									echo "  if(varable.length > 0)";
									echo "  {";
									echo "    frag1 = varable.split('^');";
									echo " 	  for (i=1;i<frag1.length;i++)";
									echo " 	  {";
									echo " 		frag2 = frag1[i].split('~');";  
									echo "      tipmage.setTooltip(frag2[1],frag2[2],frag2[3],frag2[4],frag2[5],frag2[0]);";
									echo "    }";
									echo "  };";
									echo "</script>";
									echo "<script type='text/javascript'>";
									echo "  tipmage.onInsert = function (identifier,posx,posy,width,height,text) ";
									echo "  {";
									echo "    document.getElementById('Hgrafica').value = document.getElementById('Hgrafica').value + '^' + parseInt(identifier)+'~'+posx+'~'+posy+'~'+width+'~'+height+'~'+text;";
									echo "    alert(document.getElementById('Hgrafica').value);";
									echo "  };";
									echo "</script>";
									echo "<script type='text/javascript'>";
									echo "  tipmage.onUpdate = function (identifier,posx,posy,width,height,text) ";
									echo "  {";
									echo "    final = '';";
									echo "    arreglo = document.getElementById('Hgrafica').value;";
									echo "    frag1 = arreglo.split('^');";
									echo " 	  for (i=1;i<frag1.length;i++)";
									echo " 	  {";
									echo " 		frag2 = frag1[i].split('~');";
									echo "      if(frag2[0] == identifier)";
									echo "		{";
									echo "			frag2[1]=posx;";
									echo "			frag2[2]=posy;";
									echo "			frag2[3]=width;";
									echo "			frag2[4]=height;";
									echo "			frag2[5]=text;";
									echo "          frag1[i]=frag2[0]+'~'+frag2[1]+'~'+frag2[2]+'~'+frag2[3]+'~'+frag2[4]+'~'+frag2[5];";
									echo "		}";
									echo " 		final += '^' + frag1[i];";
									echo "    }";
									echo "    document.getElementById('Hgrafica').value = final;";
									echo "    alert(document.getElementById('Hgrafica').value);";
									echo "  };";
									echo "</script>";
									echo "<script type='text/javascript'>";
									echo "  tipmage.onDelete = function (identifier,posx,posy,width,height,text) ";
									echo "  {";
									echo "    final = '';";
									echo "    arreglo = document.getElementById('Hgrafica').value;";
									echo "    frag1 = arreglo.split('^');";
									echo " 	  for (i=1;i<frag1.length;i++)";
									echo " 	  {";
									echo " 		frag2 = frag1[i].split('~');";
									echo "      if(frag2[0] != identifier)";
									echo "		{";
									echo "          alert(frag1[i]);";
									echo "			final = final + '^' + frag1[i];";
									echo "		}";
									echo "    }";
									echo "    document.getElementById('Hgrafica').value = final;";
									echo "    alert(document.getElementById('Hgrafica').value);";
									echo "  };";
									echo "</script>";
									$campos += $registro[$i][32];	
								}
							break;
							case "Link":
								if($registro[$i][32]>$n)
									$registro[$i][32]=$n;
								$faltan=$n - $campos;
								if($registro[$i][32] <= $faltan and $faltan > 0)
								{
									$alto=$registro[$i][35];
									$ancho=$registro[$i][36];
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									//echo "<td colspan=".$span." id=tipoL02 align=center>".$registro[$i][9].$salto."<A HREF='".$registro[$i][25]."' target='_blank'>HIPERVINCULO</A></td>";
									if(is_numeric($registro[$i][25]))
										echo "<td colspan=".$span." width=".$rwidth." id=tipoL02 align=center>".$salto."<input type='button' id='btnModal".$i."' name='btnModal".$i."' value='".htmlentities($registro[$i][9])."' onClick='javascript:activarModalIframe(\"".htmlentities($registro[$i][9])."\",\"nombreIframe\",\"http://".$CPU."/matrix/HCE/Procesos/HCE.php?accion=W1&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wformulario=".$registro[$i][25]."&whis=".$whis."&wing=".$wing."&wsex=".$wsex."&wtitframe=no&width=".$width."\",\"".$alto."\",\"".$ancho."\");'></td>";
									else
										echo "<td colspan=".$span." width=".$rwidth." id=tipoL02 align=center>".$salto."<input type='button' id='btnModal".$i."' name='btnModal".$i."' value='".htmlentities($registro[$i][9])."' onClick='javascript:activarModalIframe(\"".htmlentities($registro[$i][9])."\",\"nombreIframe\",\"".$registro[$i][25]."\",\"".$alto."\",\"".$ancho."\");'></td>";
									$campos += $registro[$i][32];
								}
								else
								{
									for ($w=0;$w<$faltan;$w++)
										echo "<td id=tipoL02></td>";
									echo "</tr>";
									$campos = 0;
									$alto=$registro[$i][35];
									$ancho=$registro[$i][36];
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									if(is_numeric($registro[$i][25]))
										echo "<tr><td colspan=".$span." width=".$rwidth." id=tipoL02 align=center>".$salto."<input type='button' id='btnModal".$i."' name='btnModal".$i."' value='".htmlentities($registro[$i][9])."' onClick='javascript:activarModalIframe(\"".htmlentities($registro[$i][9])."\",\"nombreIframe\",\"http://".$CPU."/matrix/HCE/Procesos/HCE.php?accion=W1&ok=0&empresa=".$empresa."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wformulario=".$registro[$i][25]."&whis=".$whis."&wing=".$wing."&wsex=".$wsex."&wtitframe=no&width=".$width."\",\"".$alto."\",\"".$ancho."\");'></td>";
									else
										echo "<tr><td colspan=".$span." width=".$rwidth." id=tipoL02 align=center>".$salto."<input type='button' id='btnModal".$i."' name='btnModal".$i."' value='".htmlentities($registro[$i][9])."' onClick='javascript:activarModalIframe(\"".htmlentities($registro[$i][9])."\",\"nombreIframe\",\"".$registro[$i][25]."\",\"".$alto."\",\"".$ancho."\");'></td>";
									$campos += $registro[$i][32];	
								}
							break;
							case "Password":
								if($registro[$i][32]>$n)
									$registro[$i][32]=$n;
								$faltan=$n - $campos;
								if($registro[$i][32] <= $faltan and $faltan > 0)
								{
									$alto=$registro[$i][35];
									$ancho=$registro[$i][36];
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									echo "<td bgcolor=#E8EEF7 colspan=".$span." width=".$rwidth." id=tipoL02 align=center>".htmlentities($registro[$i][9]).$salto."<input type='password' name='registro[".$i."][0]' size=".$ancho." maxlength=80 id='T".$i."' value='".htmlentities($registro[$i][0])."' class=tipo3".$OBL."></td>";
									$campos += $registro[$i][32];
								}
								else
								{
									for ($w=0;$w<$faltan;$w++)
										echo "<td id=tipoL02></td>";
									echo "</tr>";
									$campos = 0;
									$alto=$registro[$i][35];
									$ancho=$registro[$i][36];
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									echo "<tr><td bgcolor=#E8EEF7 colspan=".$span." width=".$rwidth." id=tipoL02 align=center>".htmlentities($registro[$i][9]).$salto."<input type='password' name='registro[".$i."][0]' size=".$ancho." maxlength=80 id='T".$i."' value='".htmlentities($registro[$i][0])."' class=tipo3".$OBL."></td>";
									$campos += $registro[$i][32];	
								}
								//if(isset($SF1["T".$i]))
								if(isset($completo) and $completo == 1)
									echo "<script> soloLectura(document.getElementById('"."T".$i."'))</script>";
							break;
							case "Fecha":
								if($registro[$i][32]>$n)
									$registro[$i][32]=$n;
								$faltan=$n - $campos;
								if($registro[$i][32] <= $faltan and $faltan > 0)
								{
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									echo "<td id=tipoL02 colspan=".$span." width=".$rwidth.">".htmlentities($registro[$i][9]).$salto."<input id='F".$i."' type='text' size='12' maxlength='12' NAME='registro[".$i."][0]' class=tipo3".$OBL." value='".$registro[$i][0]."'></td>";
									//echo "<td id=tipoL02 colspan=".$span.">".$registro[$i][9]."<br><INPUT TYPE='text' NAME='registro[".$i."][0]' id='F$i' SIZE=10 readonly class='tipo3' value='".$registro[$i][0]."'>";
									//echo "<button id='btn_$i' onclick='javascript:calendario($i);'>...</button></td>";
									$campos += $registro[$i][32];
								}
								else
								{
									for ($w=0;$w<$faltan;$w++)
										echo "<td id=tipoL02></td>";
									echo "</tr>";
									$campos = 0;
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									echo "<tr><td id=tipoL02 colspan=".$span." width=".$rwidth.">".htmlentities($registro[$i][9]).$salto."<input id='F".$i."' type='text' size='12' maxlength='12' NAME='registro[".$i."][0]' class=tipo3".$OBL." value='".$registro[$i][0]."'></td>";
									//echo "<tr><td id=tipoL02 colspan=".$span.">".$registro[$i][9]."<br><INPUT TYPE='text' NAME='registro[".$i."][0]' id='F$i' SIZE=10 readonly class='tipo3' value='".$registro[$i][0]."'>";
									//echo "<button id='btn_$i' onclick='javascript:calendario($i);'>...</button></td>";
									$campos += $registro[$i][32];	
								}
								//if(isset($SF1["F".$i]) or $registro[$i][29] == "2")
								if((isset($completo) and $completo == 1) or $registro[$i][29] == "2")
									echo "<script> soloLectura(document.getElementById('"."F".$i."'))</script>";
							break;
							case "Tabla":
								if($registro[$i][32]>$n)
									$registro[$i][32]=$n;
								$faltan=$n - $campos;
								echo "<input type='hidden' id='cual".$i."' value='".$registro[$i][43]."'>";
								echo "<input type='hidden' id='Tcual".$i."' value='".htmlentities($registro[$i][44])."'>";
								echo "<input type='hidden' id='SimMul".$i."' value='".$registro[$i][41]."'>";
								//echo "Archivo : ".$registro[$i][6]." campos : ".$registro[$i][40]." tipo : ".$registro[$i][41]." nombres : ".$registro[$i][42]." cualificacion : ".$registro[$i][43]." caracteres : ".$registro[$i][44]."<br>";
								$wcampos=explode(",",$registro[$i][40]);
								$wncampos=explode(",",$registro[$i][42]);
								if($registro[$i][32] <= $faltan and $faltan > 0)
								{
									//echo "Valor : ".$registro[$i][0]."<br>";
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									echo "<td id=tipoL02 colspan=".$span." width=".$rwidth.">".htmlentities($registro[$i][9]).$salto."";
										$position="M".$i;
										$id="ajaxview('19','".$empresa."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','')";
										if($registro[$i][45] != "on")
										{
											echo "<select name='Tselect[".$i."]' id='TS".$i."' OnChange=".$id." class=tipo3>";
												if(isset($Tselect[$i]))
												{
													if(strcmp($Tselect[$i],$wncampos[0]) == 0)
													{
														echo "<option value='".$wncampos[0]."' selected>".$wncampos[0]."</option>";
														echo "<option value='".$wncampos[1]."'>".$wncampos[1]."</option>";
														$sp=0;
													}
													else
													{
														echo "<option value='".$wncampos[0]."'>".$wncampos[0]."</option>";
														echo "<option value='".$wncampos[1]."' selected>".$wncampos[1]."</option>";
														$sp=1;
													}
												}
												else
												{
													echo "<option value='".$wncampos[0]."'>".$wncampos[0]."</option>";
													echo "<option value='".$wncampos[1]."'>".$wncampos[1]."</option>";
													$sp=0;
												}
											echo "</select><br>";
										}
										if($registro[$i][45] != "on")
											$query="1SELECT ".$registro[$i][40]." FROM ".$registro[$i][6]." WHERE ".$wcampos[$sp]." LIKE var ORDER BY ".$wcampos[$sp].";";
										else
										{
											$sp=0;
											$query="2SELECT ".$registro[$i][40]." FROM ".$registro[$i][6]." WHERE ".$wcampos[$sp]." LIKE var ORDER BY ".$wcampos[$sp].";";
										}
										echo "<input type='hidden' id='query".$i."' value='".$query."'>";
										
										echo "<input type='text' id='M".$i."' onFocus='javascript:limpiarCampo(this);' size=30/>";
										echo "<br>";
										if($registro[$i][43] == "on")
										{
											$cualif=explode(",",$registro[$i][44]);
											if(count($cualif) > 2)
											{
												$varRT="RT".$i;
												if(isset($varRT) and $varRT == substr($cualif[2],0,1))
												{
													echo "<input type='RADIO' name='RT".$i."' id='RT".$i."' value=".htmlentities(substr($cualif[2],0,1))." checked>".htmlentities($cualif[2]);
													echo "<input type='RADIO' name='RT".$i."' id='RT".$i."' value=".htmlentities(substr($cualif[3],0,1)).">".htmlentities($cualif[3]);
												}
												else
												{
													echo "<input type='RADIO' name='RT".$i."' id='RT".$i."' value=".htmlentities(substr($cualif[2],0,1)).">".htmlentities($cualif[2]);
													echo "<input type='RADIO' name='RT".$i."' id='RT".$i."' value=".htmlentities(substr($cualif[3],0,1))." checked>".htmlentities($cualif[3]);
												}
												echo "<br>";
											}
										}
										echo "<select name='registro[".$i."][0]' id='selAuto".$i."' multiple=multiple size=".$registro[$i][35]." class=tipoTAB".$OBL." onDblClick='javascript:quitarComponente(\"$i\");'>";
										echo $registro[$i][0];
										echo "</select>";
										echo '<script language="Javascript">';
										echo "document.getElementById('selAuto".$i."').style.width=".$registro[$i][36].";";
										echo '</script>';
										echo "<input type='hidden' id='registro[".$i."][36]' value='".$registro[$i][36]."'>";
									echo "</td>";
									$campos += $registro[$i][32];
								}
								else
								{
									//echo "Valor : ".$registro[$i][0]."<br>";
									for ($w=0;$w<$faltan;$w++)
										echo "<td id=tipoL02></td>";
									echo "</tr>";
									$campos = 0;
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									echo "<tr><td id=tipoL02 colspan=".$span." width=".$rwidth.">".$OBL.htmlentities($registro[$i][9]).$salto."";
										$position="M".$i;
										$id="ajaxview('19','".$empresa."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','')";
										if($registro[$i][45] != "on")
										{
											echo "<select name='Tselect[".$i."]' id='TS".$i."' OnChange=".$id." class=tipo3>";
												if(isset($Tselect[$i]))
												{
													if(strcmp($Tselect[$i],$wncampos[0]) == 0)
													{
														echo "<option value='".$wncampos[0]."' selected>".$wncampos[0]."</option>";
														echo "<option value='".$wncampos[1]."'>".$wncampos[1]."</option>";
														$sp=0;
													}
													else
													{
														echo "<option value='".$wncampos[0]."'>".$wncampos[0]."</option>";
														echo "<option value='".$wncampos[1]."' selected>".$wncampos[1]."</option>";
														$sp=1;
													}
												}
												else
												{
													echo "<option value='".$wncampos[0]."'>".$wncampos[0]."</option>";
													echo "<option value='".$wncampos[1]."'>".$wncampos[1]."</option>";
													$sp=0;
												}
											echo "</select><br>";
										}
										if($registro[$i][45] != "on")
											$query="1SELECT ".$registro[$i][40]." FROM ".$registro[$i][6]." WHERE ".$wcampos[$sp]." LIKE var ORDER BY ".$wcampos[$sp].";";
										else
										{
											$sp=0;
											$query="2SELECT ".$registro[$i][40]." FROM ".$registro[$i][6]." WHERE ".$wcampos[$sp]." LIKE var ORDER BY ".$wcampos[$sp].";";
										}
										echo "<input type='hidden' id='query".$i."' value='".$query."'>";
										
										echo "<input type='text' id='M".$i."' size=30/>";
										echo "<br>";
										if($registro[$i][43] == "on")
										{
											$cualif=explode(",",$registro[$i][44]);
											if(count($cualif) > 2)
											{
												$varRT="RT".$i;
												if(isset($varRT) and $varRT == substr($cualif[2],0,1))
												{
													echo "<input type='RADIO' name='RT".$i."' id='RT".$i."' value=".htmlentities(substr($cualif[2],0,1))." checked>".htmlentities($cualif[2]);
													echo "<input type='RADIO' name='RT".$i."' id='RT".$i."' value=".htmlentities(substr($cualif[3],0,1)).">".htmlentities($cualif[3]);
												}
												else
												{
													echo "<input type='RADIO' name='RT".$i."' id='RT".$i."' value=".htmlentities(substr($cualif[2],0,1)).">".htmlentities($cualif[2]);
													echo "<input type='RADIO' name='RT".$i."' id='RT".$i."' value=".htmlentities(substr($cualif[3],0,1))." checked>".htmlentities($cualif[3]);
												}
												echo "<br>";
											}
										}
										echo "<select name='registro[".$i."][0]' id='selAuto".$i."' multiple=multiple size=".$registro[$i][35]." class=tipoTAB".$OBL." onDblClick='javascript:quitarComponente(\"$i\");'>";
										echo $registro[$i][0];
										echo "</select>";
										echo '<script language="Javascript">';
										echo "document.getElementById('selAuto".$i."').style.width=".$registro[$i][36].";";
										echo '</script>';
										echo "<input type='hidden' id='registro[".$i."][36]' value='".$registro[$i][36]."'>";
									echo "</td>";
									$campos += $registro[$i][32];	
								}
								//if(isset($SF1["M".$i]))
								if(isset($completo) and $completo == 1)
									echo "<script> soloLectura(document.getElementById('"."M".$i."'))</script>";
							break;
							case "Seleccion":
								if($registro[$i][34] == "off")
								{
									if($registro[$i][32]>$n)
										$registro[$i][32]=$n;
									$faltan=$n - $campos;
									if($registro[$i][32] <= $faltan and $faltan > 0)
									{
										$span=$registro[$i][32];
										$rwidth=(integer)(($width * $span)/$Span);
										echo "<td id=tipoL02".$CHECK." colspan=".$span." width=".$rwidth.">".htmlentities($registro[$i][9]).$salto."";
										$query = "SELECT Selcda, Selnda from ".$empresa."_".$registro[$i][6]." where Seltab='".$registro[$i][7]."' and Selest='on' order by Selnda";
										$err1 = mysql_query($query,$conex);
										$num1 = mysql_num_rows($err1);
										echo "<select name='registro[".$i."][0]' id='S".$i."' class=tipo3".$OBL.">";
										if ($num1>0)
										{
											for ($j=0;$j<$num1;$j++)
											{
												$row1 = mysql_fetch_array($err1);
												$registro[$i][0]=ver($registro[$i][0]);
												if($registro[$i][0] == $row1[0])
													echo "<option selected value='".$row1[0]."-".$row1[1]."'>".htmlentities($row1[0])."-".htmlentities($row1[1])."</option>";
												else
													echo "<option value='".$row1[0]."-".$row1[1]."'>".htmlentities($row1[0])."-".htmlentities($row1[1])."</option>";
											}
										}
										echo "</select>";
										echo "</td>";
										$campos += $registro[$i][32];
									}
									else
									{
										for ($w=0;$w<$faltan;$w++)
											echo "<td id=tipoL02".$CHECK."></td>";
										echo "</tr>";
										$campos = 0;
										$span=$registro[$i][32];
										$rwidth=(integer)(($width * $span)/$Span);
										echo "<tr><td id=tipoL02".$CHECK." colspan=".$span." width=".$rwidth.">".htmlentities($registro[$i][9]).$salto."";
										$query = "SELECT Selcda, Selnda from ".$empresa."_".$registro[$i][6]." where Seltab='".$registro[$i][7]."' and Selest='on' order by Selcda";
										$err1 = mysql_query($query,$conex);
										$num1 = mysql_num_rows($err1);
										echo "<select name='registro[".$i."][0]' id='S".$i."' class=tipo3".$OBL.">";
										if ($num1>0)
										{
											for ($j=0;$j<$num1;$j++)
											{
												$row1 = mysql_fetch_array($err1);
												$registro[$i][0]=ver($registro[$i][0]);
												if($registro[$i][0] == $row1[0])
													echo "<option selected value='".$row1[0]."-".$row1[1]."'>".htmlentities($row1[0])."-".htmlentities($row1[1])."</option>";
												else
													echo "<option value='".$row1[0]."-".$row1[1]."'>".htmlentities($row1[0])."-".htmlentities($row1[1])."</option>";
											}
										}
										echo "</select>";
										echo "</td>";
										$campos += $registro[$i][32];	
									}
									//if(isset($SF1["S".$i]))
									if(isset($completo) and $completo == 1)
										echo "<script> soloLectura(document.getElementById('"."S".$i."'))</script>";	
								}
								else
								{
									if($registro[$i][32]>$n)
										$registro[$i][32]=$n;
									$faltan=$n - $campos;
									if($registro[$i][32] <= $faltan and $faltan > 0)
									{
										$span=$registro[$i][32];
										$rwidth=(integer)(($width * $span)/$Span);
										echo "<td id=tipoL02".$CHECK." colspan=".$span." width=".$rwidth.">".htmlentities($registro[$i][9]).$salto."";
										$query = "SELECT Selcda, Selnda from ".$empresa."_".$registro[$i][6]." where Seltab='".$registro[$i][7]."' and Selest='on' order by Selcda";
										$err1 = mysql_query($query,$conex);
										$num1 = mysql_num_rows($err1);
										if ($num1>0)
										{
											$filasR=$registro[$i][37];
											$rb=0;
											echo "<table border=0  CELLSPACING=0 align=center>";
											echo "<tr>";
											for ($j=0;$j<$num1;$j++)
											{
												if($rb >= $filasR)
												{
													echo "</tr><tr>";
													$rb=0;
												}
												$row1 = mysql_fetch_array($err1);
												//$registro[$i][0]=ver($registro[$i][0]);
												//echo "valor de i : ".$i."<br>";
												//if(isset($registro[$i][0]) and $registro[$i][0] == $row1[0])
												if($registro[$i][0] == $row1[0]."-".$row1[1])
												{
													if($rb <= $filasR)
														echo "<td id=tipoRB".$CHECK."><input type='RADIO' name='R".$i."' id='R".$i."' value='".$row1[0]."-".$row1[1]."' class=tipoL02M".$OBL." checked>".htmlentities($row1[1])."</td>";
													else
														echo "<tr><td id=tipoRB".$CHECK."><input type='RADIO' name='R".$i."' id='R".$i."' value='".$row1[0]."-".$row1[1]."' class=tipoL02M".$OBL." checked>".htmlentities($row1[1])."</td>";
													$rb++;
												}
												else
												{
													if($rb <= $filasR)
														echo "<td id=tipoRB".$CHECK."><input type='RADIO' name='R".$i."' id='R".$i."' class=tipoL02M".$OBL." value='".$row1[0]."-".$row1[1]."'>".htmlentities($row1[1])."</td>";
													else
														echo "<tr><td id=tipoRB".$CHECK."><input type='RADIO' name='R".$i."' id='R".$i."' class=tipoL02M".$OBL." value='".$row1[0]."-".$row1[1]."'>".htmlentities($row1[1])."</td>";
													$rb++;
												}
											}
											echo "</table>";
										}
										echo "</td>";
										$campos += $registro[$i][32];
									}
									else
									{
										for ($w=0;$w<$faltan;$w++)
											echo "<td id=tipoL02".$CHECK."></td>";
										echo "</tr>";
										$campos = 0;
										$span=$registro[$i][32];
										$rwidth=(integer)(($width * $span)/$Span);
										echo "<tr><td id=tipoL02 colspan=".$span." width=".$rwidth.">".htmlentities($registro[$i][9]).$salto."";
										$query = "SELECT Selcda, Selnda from ".$empresa."_".$registro[$i][6]." where Seltab='".$registro[$i][7]."' and Selest='on' order by Selnda";
										$err1 = mysql_query($query,$conex);
										$num1 = mysql_num_rows($err1);
										if ($num1>0)
										{
											$filasR=$registro[$i][37];
											$rb=0;
											echo "<table border=0  CELLSPACING=0 align=center>";
											echo "<tr>";
											for ($j=0;$j<$num1;$j++)
											{
												if($rb >= $filasR)
												{
													echo "</tr><tr>";
													$rb=0;
												}
												$row1 = mysql_fetch_array($err1);
												//$registro[$i][0]=ver($registro[$i][0]);
												if($registro[$i][0] == $row1[0]."-".$row1[1])
												{
													if($rb <= $filasR)
														echo "<td id=tipoRB".$CHECK."><input type='RADIO' name='R".$i."' id='R".$i."' value='".$row1[0]."-".$row1[1]."' class=tipoL02M".$OBL." checked>".htmlentities($row1[1])."</td>";
													else
														echo "<tr><td id=tipoRB".$CHECK."><input type='RADIO' name='R".$i."' id='R".$i."' value='".$row1[0]."-".$row1[1]."' class=tipoL02M".$OBL." checked>".htmlentities($row1[1])."</td>";
													$rb++;
												}
												else
												{
													if($rb <= $filasR)
														echo "<td id=tipoRB".$CHECK."><input type='RADIO' name='R".$i."' id='R".$i."' class=tipoL02M".$OBL." value='".$row1[0]."-".$row1[1]."'>".htmlentities($row1[1])."</td>";
													else
														echo "<tr><td id=tipoRB".$CHECK."><input type='RADIO' name='R".$i."' id='R".$i."' class=tipoL02M".$OBL." value='".$row1[0]."-".$row1[1]."'>".htmlentities($row1[1])."</td>";
													$rb++;
												}
											}
											echo "</table>";
										}
										echo "</td>";
										$campos += $registro[$i][32];	
									}
									//if(isset($SF1["R".$i]))
									if(isset($completo) and $completo == 1)
										echo "<script> soloLectura(document.getElementById('"."R".$i."'))</script>";
								}
							break;
							case "Hora":
								//@eval($registro[$i][26]);
								//if(!isset($SF1["H".$i]) and $registro[$i][29] != "2")
									//$registro[$i][0] = date("H:i:s");
								if($registro[$i][32]>$n)
									$registro[$i][32]=$n;
								$faltan=$n - $campos;
								if($registro[$i][32] <= $faltan and $faltan > 0)
								{
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									$col=($span * 120) / ($n * 2);
									$position="H".$i;
									if(!isset($SF1["H".$i]) and $registro[$i][29] != "2")
									{
										//$id="ajaxview('19','".$empresa."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','')";
										echo "<td colspan=".$span." width=".$rwidth." id=tipoL02 align=center>".htmlentities($registro[$i][9]).$salto."<input type='TEXT' name='registro[".$i."][0]' size=8 maxlength=8 id='H".$i."' value='".$registro[$i][0]."'  class=tipo3".$OBL.">&nbsp;";

										echo "<select name='Horas[".$i."]' id=HO".$i." class=tipoHora onChange='limpiarHora(".$i.")'>";
										for ($j=0;$j<24;$j++)
										{
											if($j < 10)
												$jh = "0".$j;
											else
												$jh = $j;
											echo "<option value='".$jh."'>".$jh."</option>";
										}
										echo "</select>&nbsp;";


										echo "<select name='Minutos[".$i."]' id=MI".$i." class=tipoHora onChange='mostrarHora(".$i.")'>";
										echo "<option value=''></option>";
										for ($j=0;$j<4;$j++)
										{
											$jh=(15 * $j);
											if($jh < 15)
												$jh = "0".$jh;
											echo "<option value='".$jh."'>".$jh."</option>";
										}
										echo "</select>";

										echo "</td>";
									}
									else
										echo "<td colspan=".$span." width=".$rwidth." id=tipoL02 align=center>".htmlentities($registro[$i][9]).$salto."<input type='TEXT' name='registro[".$i."][0]' size=8 maxlength=8 id='H".$i."' value='".$registro[$i][0]."'  class=tipo3".$OBL."></td>";
									$campos += $registro[$i][32];
								}
								else
								{
									for ($w=0;$w<$faltan;$w++)
										echo "<td id=tipoL02></td>";
									echo "</tr>";
									$campos = 0;
									$span=$registro[$i][32];
									$rwidth=(integer)(($width * $span)/$Span);
									$col=($span * 120) / ($n * 2);
									$position="H".$i;
									if(!isset($SF1["H".$i]) and $registro[$i][29] != "2")
									{
										//$id="ajaxview('19','".$empresa."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','')";
										echo "<tr><td colspan=".$span." width=".$rwidth." id=tipoL02 align=center>".htmlentities($registro[$i][9]).$salto."<input type='TEXT' name='registro[".$i."][0]' size=8 maxlength=8 id='H".$i."' value='".$registro[$i][0]."'  class=tipo3".$OBL.">";

										echo "<select name='Horas[".$i."]' id=HO".$i." class=tipoHora onChange='limpiarHora(".$i.")'>";
										for ($j=0;$j<24;$j++)
										{
											if($j < 10)
												$jh = "0".$j;
											else
												$jh = $j;
											echo "<option value='".$jh."'>".$jh."</option>";
										}
										echo "</select>&nbsp;";


										echo "<select name='Minutos[".$i."]' id=MI".$i." class=tipoHora onChange='mostrarHora(".$i.")'>";
										for ($j=0;$j<4;$j++)
										{
											$jh=(15 * $j);
											if($jh < 15)
												$jh = "0".$jh;
											echo "<option value='".$jh."'>".$jh."</option>";
										}
										echo "</select>";

										echo "</td>";
									}
									else
										echo "<tr><td colspan=".$span." width=".$rwidth." id=tipoL02 align=center>".htmlentities($registro[$i][9]).$salto."<input type='TEXT' name='registro[".$i."][0]' size=8 maxlength=8 id='H".$i."' value='".$registro[$i][0]."'  class=tipo3".$OBL."></td>";
									$campos += $registro[$i][32];	
								}
								//if(isset($SF1["H".$i]) or $registro[$i][29] == "2")
								if((isset($completo) and $completo == 1) or $registro[$i][29] == "2")
									echo "<script>soloLectura(document.getElementById('"."H".$i."'))</script>";
							break;
						}
					}
					if($num > 0)
					{
						$faltan=$n - $campos;
						for ($w=0;$w<$faltan;$w++)
							echo "<td id=tipoL02></td>";
						if($pasdiv < $totdiv)
						{
							$pasdiv++;
							echo "<tr id=div".$totdiv."><td><table width=".$width." border=0 cellspacing=0>";
						}
						if($totdiv > 0)
							echo "</table></td></tr>";
						$span=$n;
						$position="FIN";
						if(!isset($firma))
							$firma="";
						if(!isset($completo) or $completo == 0)
						{
							if($WTIPO == 3)
							{
								$path4="http://".$CPU."/matrix/HCE/procesos/HCE_Tipo3.php?empresa=hce&wformulario=".$wformulario."&wcedula=".$wcedula."&wtipodoc=".$wtipodoc."&wtitulo=".$wtitulo;
								echo "<tr><td id=tipoL06 colspan=".$span."><A HREF='#' id='FIRMA' class=tipo3V onclick='javascript:activarModalIframe(\"FIRMA DIGITAL\",\"nombreIframe\",\"".$path4."\",\"0\",\"0\");'>FIRMA DIGITAL</A></td></tr>";
							}
							else
							{
								$id="ajaxview('19','".$empresa."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','')";
								echo "<tr><td id=tipoL06 colspan=".$span.">Firma Digital : <input type='password' name='firma' size=40 maxlength=80 id='firma' value='".$firma."' class=tipo3 OnBlur=".$id."></td></tr>";
							}
						}
						else
							echo "<tr><td id=tipoL06 colspan=".$span.">Firma Digital : Documento Con Firma Digital</td></tr>";
						$position="ok";
						if(((!isset($completo) or $completo == 0) and (!isset($wuserG) or $wuserG == $key)) or (isset($wswfirma) and $wswfirma == 4))
						{
							$id="ajaxview('19','".$empresa."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','1')";
							//echo "<tr><td id=tipoL09 colspan=".$span."><IMG SRC='/matrix/images/medical/HCE/ok.png' id='logook' style='vertical-align:middle;' OnClick=".$id.">&nbsp;&nbsp;<input type='checkbox' name='ok' id='ok' OnClick=".$id."></td>";
							echo "<tr><td id=tipoL09 colspan=".$span."><IMG SRC='/matrix/images/medical/HCE/ok.png' id='logook' style='vertical-align:middle;' OnClick=".$id."></td>";
						}
						echo "<input type='HIDDEN' name= 'position' value='".$position."' id='position'>";
						if(isset($GOK))
						{
							echo "<tr><td colspan=".$Span." id=tipoLGOK><IMG SRC='/matrix/images/medical/root/felizH.png'> DATOS GRABADOS OK!!!!</td></tr>";
						}
						if(isset($mensajes))
							echo "<tr><td colspan=".$Span." id=tipoL09A>".$mensajes."</td><tr>";
						echo "</div></table></center>";
						//$id="ajaxview('19','".$empresa."','W1','".$items."','".$wsa."','".$wformulario."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$position."','".$wfechaT."','".$whoraT."','".$width."','".$num."','".$WSF."','".$wsinfirma."','".$wfechareg."','".$whorareg."','".$WTIPO."','".$WSS."','')";
						//echo "<tr><td id=tipoL08 colspan=".$span."><input type='button' name='ENTER' value='GRABAR' class=tipo3A id='FIN' OnClick=".$id."></td></tr></div></table></center>";
					}
					else
					{
						echo "<center>";
						// INICIALIZACION DEL TITULO DEL FORMULARIO
						echo '<script language="Javascript">';
						//echo "     debugger;";
						echo '		var obj = parent.parent.demograficos.document.all.txttitulo;';
						echo '		if(obj)';
						echo '		{';
						echo "			var divAux = document.createElement( 'div');";
						echo "			divAux.innerHTML = '".htmlentities($wtitulo)."';";
						echo "			obj.value = divAux.innerHTML.toUpperCase();";
						echo '		} ';
						echo '		var obj1 = parent.parent.demograficos.document.all.txtformulario;';
						echo '		if(obj1)';
						echo '		{';
						echo "			var divAux = document.createElement( 'div');";
						echo "			divAux.innerHTML = '".htmlentities($wformulario)."';";
						echo "			obj1.value = divAux.innerHTML.toUpperCase();";
						echo '		} ';
						echo '</script>';
						echo "<H2><b><font color=#000066 FACE='Arial'>Protocolo NO Definido</font></b></H2>";
						echo "</center>";
					}
				}
				else
				{
					echo "<center><table border=0 aling=center>";
					echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>ESTE PACIENTE NO EXISTE. POR FAVOR LLAME A INFORMATICA Y CONSULTE.  GRACIAS</MARQUEE></FONT>";
					echo "<br><br>";
				}
				echo "</form>";
			break;
					
			case "W2": 		
				$formularios=array();
				$query = "select Vaspas, Vasnom from ".$empresa."_000005 where Vaspro='".$wformulario."' and Vasest='on' ";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$numfor = $num1;
				if ($num1>0)
				{
					for ($i=0;$i<$numfor;$i++)
					{
						$row1 = mysql_fetch_array($err1);
						$formularios[$i][0]=$row1[0];
						$formularios[$i][1]=$row1[1];
					}
				}		
				echo "<link type='text/css' href='../../../include/root/ui.core.css' rel='stylesheet'>";
				echo "<link type='text/css' href='../../../include/root/ui.theme.css' rel='stylesheet'>";
				echo "<link type='text/css' href='../../../include/root/ui.datepicker.css' rel='stylesheet'>";
				echo "<link type='text/css' href='../../../include/root/ui.tabs.css' rel='stylesheet'>";
				echo "<link type='text/css' href='../../../include/root/jquery.jTPS.css' rel='stylesheet'>"; 			
				
				echo "<script type='text/javascript' src='HCE.js' ></script>";
				
				echo "<script type='text/javascript' src='../../../include/root/jquery-1.3.2.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/ui.core.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.jTPS.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/ui.tabs.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/ui.datepicker.js'></script>";
				
				
				echo "<script type='text/javascript'>";
				echo "$(document).ready(function(){  init_calendar(); ";
				echo "$('#tabs').tabs(); ";
				echo "});";
				echo "</script>";			
				
				echo "</head>";
				
				echo "<BODY TEXT='#000000' FACE='ARIAL'>";
				echo "<div id='1' height=50px>";
				
				$key = substr($user,2,strlen($user));
				echo "<form name='HCE7' action='HCE.php' method=post>";
							
				echo "<div id='tabs'>";		
				echo "<ul>";
				for ($i=0;$i<$numfor;$i++)
				{
					$nfor=$i + 1;
					echo "<li><a href='#fragment-".$nfor."'><span>".$formularios[$i][1]."</span></a></li>";
				}
				echo "</ul>";
				
				$FI=array();
				$FF=array();
				for ($i=0;$i<$numfor;$i++)
				{
					$nfor1=$i + 1;
					$nfor2=$i + 20;
					$FI[$nfor1]=date("Y-m-d");
					$FF[$nfor1]=date("Y-m-d");
					echo "<div id='fragment-".$nfor1."' style='overflow:scroll;height:315px' >";
					echo "<center><input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
					echo "<center><input type='HIDDEN' name= 'accion' value='".$accion."'>";
					echo "<center><input type='HIDDEN' name= 'wformulario' value='".$wformulario."'>";
					echo "<center><input type='HIDDEN' name= 'whis' value='".$whis."'>";
					echo "<center><input type='HIDDEN' name= 'wing' wing='".$wing."'>";
					echo "<center><input type='HIDDEN' name= 'wcedula' wing='".$wcedula."'>";
					echo "<center><input type='HIDDEN' name= 'wtipodoc' wing='".$wtipodoc."'>";
					echo "<table border=0 id=tipoT00>";
					echo "<tr><td align=center colspan=2 id=tipoL08>CRITERIO DE BUSQUEDA</td></tr>";
					$inicial="I".$nfor1;
					$final="F".$nfor1;
					echo "<tr><td align=left id=tipoL02>Fecha Inicial : </td><td align=center id=tipoL02><input id='FI".$nfor1."' type='text' size='12' maxlength='12' NAME='FI[".$nfor1."]' value='".$FI[$nfor1]."'></td></tr>";
					echo "<tr><td align=left id=tipoL02>Fecha Final : </td><td align=center id=tipoL02><input id='FF".$nfor1."' type='text' size='12' maxlength='12' NAME='FF[".$nfor1."]' value='".$FF[$nfor1]."'></td></tr>";				
					$id="ajaxtable('".$nfor2."','".$empresa."','W3','".$formularios[$i][0]."','".$wcedula."','".$wtipodoc."','".$whis."','".$wing."','".$FI[$nfor1]."','".$FF[$nfor1]."','".$nfor1."')";
					echo "<tr><td  id=tipoL08 colspan=2><input type='button' name='ENTER' value='CONSULTAR' class=tipo3A id='SQL".$nfor1."' OnClick=".$id."></td></tr>";
					echo "<tr><td align=center colspan=2>";
					echo "<div id='".$nfor2."'>";
					echo "</div>";
					echo "</td></tr>";
					echo "</table>";
					echo "</div>";
				}
				echo"</form>";
			break;
			
			case "W3": 
				echo "<link type='text/css' href='../../../include/root/ui.core.css' rel='stylesheet'>";
				echo "<link type='text/css' href='../../../include/root/ui.theme.css' rel='stylesheet'>";
				
				echo "<script type='text/javascript' src='../../../include/root/jquery-1.3.2.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/ui.core.min.js'></script>";
				echo "<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>";
				
				echo "<script type='text/javascript' src='HCE.js' ></script>";
				
				$alto=600;
				$ancho=550;
				$titulos=array();
				$query  = "select ".$empresa."_000002.Detorp,".$empresa."_000002.Detdes from ".$empresa."_000002 ";
				$query .= " where ".$empresa."_000002.detpro='".$wformulario."' ";
				$query .= "   and ".$empresa."_000002.dettip='Titulo' "; 
				$query .= "   and ".$empresa."_000002.detest='on' ";  
				$query .= "  order by 1 ";
				$err1 = mysql_query($query,$conex);
				$numt = mysql_num_rows($err1);
				if ($numt>0)
				{
					for ($j=0;$j<$numt;$j++)
					{
						$row1 = mysql_fetch_array($err1);
						$titulos[$j][0]=$row1[0];
						$titulos[$j][1]=$row1[1];
					}
				}
				$filanterior=0;
				$spana=0;
				$kcolor=0;
				$tcolor=1;
				echo "<table border=0 id=tipoT00 cellpadding=5>";
				//                                      0                                    1                           2                                      3                                       4                           5                          6    
				$query  = "select ".$empresa."_000002.Detdes,".$empresa."_".$wformulario.".movdat,".$empresa."_000002.Detorp,".$empresa."_".$wformulario.".fecha_data,".$empresa."_".$wformulario.".Hora_data,".$empresa."_000002.Dettip,".$empresa."_000002.Detcon from ".$empresa."_".$wformulario.",".$empresa."_000002 ";
				$query .= " where ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
				$query .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
				$query .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
				$query .= "   and ".$empresa."_".$wformulario.".fecha_data between '".$wfechai."' and '".$wfechaf."' "; 
				$query .= "   and ".$empresa."_".$wformulario.".movpro=".$empresa."_000002.detpro ";
				$query .= "   and ".$empresa."_".$wformulario.".movcon = ".$empresa."_000002.detcon ";
				$query .= "   and ".$empresa."_000002.detest='on' "; 
				$query .= "   and ".$empresa."_000002.Dettip not in ('Titulo','Subtitulo') "; 
				$query .= "  order by 4,5,3 ";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if ($num1>0)
				{
					for ($j=0;$j<$num1;$j++)
					{
						$row1 = mysql_fetch_array($err1);
						if($filanterior > $row1[2])
						{
							if($spana < 8)
							{
								$spanf=8 - $spana;
								echo "<td colspan=".$spanf." id=tipoL02J".$tcolor."></td>";
							}
							echo "<tr><td id=tipoL02Y colspan=8><b>Siguiente Registro</b></td></tr>";
							$spana = 0;
						}
						$sit=buscartitulo($titulos,$row1[2],$numt);
						if($sit > -1)
						{
							if($spana < 8)
							{
								$spanf=8 - $spana;
								echo "<td colspan=".$spanf." id=tipoL02J".$tcolor."></td>";
							}
							echo "<tr><td id=tipoL02Z colspan=8><b>".htmlentities($titulos[$sit][1])."</b></td></tr>";
							if($kcolor % 2 == 0)
								$tcolor="1";
							else
								$tcolor="2";
							$kcolor++;
							$spana = 0;
						}
						$filanterior=$row1[2];
						if(strlen($row1[2]) > 0 and $row1[5] != "Label" and $row1[5] != "Link")
						{
							if( $row1[5] == "Formula")
								$wsstring = "<b>".htmlentities($row1[0])."</b> : ".number_format((double)$row1[1],2,'.',',')." ";
							else
								$wsstring = "<b>".htmlentities($row1[0])."</b> : ".htmlentities($row1[1])." ";
							$spanw=calcularspan($wsstring);
							if($spanw == 1)
								if( $row1[5] == "Formula")
									$wsstring = "".htmlentities($row1[0])." : <b>".number_format((double)$row1[1],2,'.',',')."</b> ";
								else
									$wsstring = "".htmlentities($row1[0])." :<b> ".htmlentities($row1[1])."</b> ";
							if(($spana + $spanw) < 9)
							{
								if($spana == 0)
								{
									if($spanw == 1)
										echo "<tr><td colspan=".$spanw." id=tipoL02J".$tcolor.">".$wsstring."</td>";
									else
									{
										echo "<tr><td colspan=".$spanw." id=tipoL02W".$tcolor." onClick='javascript:activarModalIframe(\"".$row1[0]."\",\"nombreIframe\",\"http://".$CPU."/matrix/HCE/Procesos/HCE.php?accion=W4&ok=0&empresa=".$empresa."&wfecha=".$row1[3]."&whora=".$row1[4]."&wformulario=".$wformulario."&whis=".$whis."&wing=".$wing."&wcon=".$row1[6]."\",\"".$alto."\",\"".$ancho."\");'>".$wsstring."</td>";
									}
								}
								else
								{
									if($spanw == 1)
										echo "<td colspan=".$spanw." id=tipoL02J".$tcolor.">".$wsstring."</td>";
									else
									{
										echo "<tr><td colspan=".$spanw." id=tipoL02W".$tcolor." onClick='javascript:activarModalIframe(\"".$row1[0]."\",\"nombreIframe\",\"http://".$CPU."/matrix/HCE/Procesos/HCE.php?accion=W4&ok=0&empresa=".$empresa."&wfecha=".$row1[3]."&whora=".$row1[4]."&wformulario=".$wformulario."&whis=".$whis."&wing=".$wing."&wcon=".$row1[6]."\",\"".$alto."\",\"".$ancho."\");'>".$wsstring."</td>";
									}
								}
								$spana += $spanw;
							}
							else
							{
								if($spana == 0)
								{
									if($spanw == 1)
										echo "<tr><td colspan=".$spanw." id=tipoL02J".$tcolor.">".$wsstring."</td>";
									else
									{
										echo "<tr><td colspan=".$spanw." id=tipoL02W".$tcolor." onClick='javascript:activarModalIframe(\"".$row1[0]."\",\"nombreIframe\",\"http://".$CPU."/matrix/HCE/Procesos/HCE.php?accion=W4&ok=0&empresa=".$empresa."&wfecha=".$row1[3]."&whora=".$row1[4]."&wformulario=".$wformulario."&whis=".$whis."&wing=".$wing."&wcon=".$row1[6]."\",\"".$alto."\",\"".$ancho."\");'>".$wsstring."</td>";
									}
								}
								else
								{
									if($spana < 8)
									{
										$spanf=8 - $spana;
										echo "<td colspan=".$spanf." id=tipoL02J".$tcolor."></td>";
									}
									if($kcolor % 2 == 0)
										$tcolor="1";
									else
										$tcolor="2";
									$kcolor++;
									if($spanw == 1)
										echo "</tr><tr><td colspan=".$spanw." id=tipoL02J".$tcolor.">".$wsstring."</td>";
									else
									{
										echo "<tr><td colspan=".$spanw." id=tipoL02W".$tcolor." onClick='javascript:activarModalIframe(\"".$row1[0]."\",\"nombreIframe\",\"http://".$CPU."/matrix/HCE/Procesos/HCE.php?accion=W4&ok=0&empresa=".$empresa."&wfecha=".$row1[3]."&whora=".$row1[4]."&wformulario=".$wformulario."&whis=".$whis."&wing=".$wing."&wcon=".$row1[6]."\",\"".$alto."\",\"".$ancho."\");'>".$wsstring."</td>";
									}
								}	
								$spana = $spanw;
							}
						}
					}
					if($spana < 8)
					{
						$spanf=8 - $spana;
						echo "<td colspan=".$spanf." id=tipoL02J".$tcolor."></td>";
					}
				}
				echo "</table>";
			break;	
			
			case "W4": 	
				echo "<script type='text/javascript' src='HCE.js' ></script>";
				
				$query  = "select ".$empresa."_".$wformulario.".movdat from ".$empresa."_".$wformulario;
				$query .= " where ".$empresa."_".$wformulario.".fecha_data = '".$wfecha."' "; 
				$query .= "   and ".$empresa."_".$wformulario.".Hora_data='".$whora."' ";
				$query .= "   and ".$empresa."_".$wformulario.".movpro='".$wformulario."' ";
				$query .= "   and ".$empresa."_".$wformulario.".movcon='".$wcon."' ";
				$query .= "   and ".$empresa."_".$wformulario.".movhis='".$whis."' ";
				$query .= "   and ".$empresa."_".$wformulario.".moving='".$wing."' ";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				if ($num1 > 0)
				{
					$row1 = mysql_fetch_array($err1);
					$wtexto = $row1[0];
				}
				else
					$wtexto = "";
				
				$wtexto=htmlentities($wtexto);
				echo "<table border=0 id=tipoT00 cellpadding=5>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/AumentarTexto.gif' onClick='javascript:increaseFontSize();'><IMG SRC='/matrix/images/medical/root/DisminuirTexto.gif' onClick='javascript:decreaseFontSize();'></td></tr>";
				echo "<tr><td id=tipoL02X><div id='wtex'>".$wtexto."</div></td></tr>";
				echo "</table>";
			break;	
		}
	}
	else
	{
		echo "<center><table border=0 aling=center>";
		echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
		echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CCCCFF LOOP=-1>NO SE HA CONFIGURADO ADECUADAMENTE EL SERVIDOR DE LA APLICACION EN LA TABLA 14 DEL GRUPO HCE.  LLAME A SISTEMAS!!!!</MARQUEE></FONT>";
		echo "<br><br>";	
	}
	//Cierre de la estructura head y html inicial
	echo "</div>";
	echo "</body>";
	echo "</html>";
}
?>
