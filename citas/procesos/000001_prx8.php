<html>
<script type="text/javascript">
<!--
	window.onload = function(){

		var formMain = document.forms[0];

		if( formMain.antComent ){
			formMain.Coment.innerHTML = formMain.antComent.value;
		}
		
		if( formMain.elements[ 'hiInicio' ] && formMain.elements[ 'hiInicio' ].value ){

			if( formMain.elements['hiComentAnt'].innerHTML != '' ){

				formMain.Coment.parentNode.appendChild( formMain.elements['hiComentAnt'] );
//				formMain.Coment.parentNode.innerHTML = formMain.Coment.parentNode.innerHTML + "<br>"; 
				formMain.Coment.parentNode.appendChild( formMain.Coment );
				formMain.elements[ 'hiComentAnt' ].style.display = "block";

				var mqEstado = document.getElementsByTagName( "marquee" );

				if( mqEstado.length > 0 ){

					//Los datos estan ok
					if( mqEstado[0].innerHTML == "LOS DATOS ESTAN OK!!!!" ){
						formMain.Coment.innerHTML = "";
					}

					//Los datos estan incompletos
					if( mqEstado[0].innerHTML == "LOS DATOS ESTAN INCOMPLETOS -- INTENTELO NUEVAMENTE!!!!" ){
					}

					//Para turno Borrado
					if( mqEstado[0].innerHTML == "!!!! TURNO BORRADO !!!!" ){
					}
				}
			}
		}

		if( formMain.nuevo.value == 'on' ){
			formMain.Coment.innerHTML = '';
		}
	}
-->
</script>
<?php
include_once("conex.php");
/**********************************************************************************************************************  
	   PROGRAMA : 000001_prx8.php
	   Fecha de Liberación :  NO REGISTRADA
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual :  2007-11-20
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gráfica que permite grabar citas x equipos x medico.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   
	   Variables wsw
	   			X##    X-Control de equipo en la tabla Nro. 7                                  0-Sin control     1-Con control
	            #Y#    Y-Control de seleccion de cita multiple                                 0-Sin control     1-Con control
	            ##Z    Z-Control de seleccion de Examenes tabla Nro. 6                         0-Sin control     1-Con control
	   
	   .2006-03-23
	   		Se modifico en el programa para agregar a la variable wsw una tercera posicion para definir si selecciona examenes o no.
	   		
	   .2007-11-20
	   		Se modifico en el programa para mostrar en la lista de examenes solamente los que estan activos.
	   
	   .2010-12-25
	   		Se Modifica el programa para que no se puede modificar los comentarios ya grabados, sin embargo se puede adicionar
	   		mas comentarios.
	   
***********************************************************************************************************************/

function comentariosAnteriores( $cod, $fecha, $hora ){

	global $conex;
	global $empresa;
	
	$coment = '';
	
	$sql = "SELECT
				 comentarios
			FROM
				{$empresa}_000001
			WHERE
				cod_equ = '$cod'
				AND fecha = '$fecha'
				AND hi = '$hora'
				";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$coment = $rows['comentarios']; 
	}
	
	return $coment;
}


echo "<head>";
echo "<title>MATRIX  TURNOS : ".$pos9."</title>";
echo "</head>";
echo "<body BGCOLOR=''>";
echo "<BODY TEXT='#000066'>";
echo "<center>";
echo "<table border=0 align=center>";
echo "<tr><td align=center bgcolor='#cccccc'><A NAME='Arriba'><font size=5><b>Registro de Informacion del Turno ".$pos9."</b></font></a></tr></td>";
echo "<tr><td align=center bgcolor='#cccccc'><font size=2> <b> 000001_prx8.php Ver. 2007-11-20</b></font></tr></td></table>";
echo "</center>";
echo "<form action='000001_prx8.php' method=post>";


session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{
	$key = substr($user,2,strlen($user));
	

	

	
	
	$antComent = '';
	if( isset($Coment) ){
		$antComent = $Coment;
		echo "<INPUT type='hidden' name='antComent' value='$antComent'>";
	}
	
	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'wsw' value='".$wsw."'>";
	if ((isset($wpar) and substr($wsw,2,1) == "0") or (isset($wpar) and isset($ok) and substr($wsw,2,1) == "1"))
	{
		if (substr($Estado,0,1) == "A")
		{
			if(substr($wsw,1,1) == "1")
				if(strpos($Hf,"p") !== false)
					$Hf=(string)((integer)substr($Hf,0,strpos($Hf,":")) + 12).substr($Hf,strpos($Hf,":")+1,2);
				else
					$Hf=substr($Hf,0,strpos($Hf,":")).substr($Hf,strpos($Hf,":")+1,2);
			//**** VALIDACIONES ****
			$tiperr =0;
			if(isset($Fijo))
			{
				$query = "select preparacion from ".$empresa."_000006 where cod_equipo='".$pos2."' and codigo = '".substr($Codexa,0,strpos($Codexa,"-"))."'";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$row1 = mysql_fetch_array($err1);
				$Coment=$Coment.$row1[0];
			}
			if ($wpar == 2)
			{
				// Verificacion de Disponibilidad de Espacio
				$query = "select cod_med,cod_equ,cod_exa,fecha,hi,hf,nom_pac,nit_resp,telefono,edad,comentarios,usuario,activo from ".$empresa."_000001 where cod_equ = '".substr($Codequ,0,3)."'";
				$query = $query." and fecha = '".$wfec."'";
				$query = $query." and hi = '".$Hi."'";
				$query = $query." and hf = '".$Hf."'";	
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err); 
				if ($num > 0 ) 
					$tiperr = 5;
				else
					if($tiperr == 0)
						$tiperr = 0;
				// 002 Multiproposito   --   006 Especiales
				// Verificacion de Incompatibilidad entre Multiproposito y Especiales
				if (substr($Codequ,0,3) == "002" and $empresa == "radio")
				{
					$query = "select cod_med,cod_equ,cod_exa,fecha,hi,hf,nom_pac,nit_resp,telefono,edad,comentarios,usuario,activo from ".$empresa."_000001 where cod_equ = '006' ";
					$query = $query." and fecha = '".$wfec."'";
					$query = $query." and hi = '".$Hi."'";
					$query = $query." and hf = '".$Hf."'";	
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err); 
					if ($num > 0 ) 
						$tiperr = 6;
					else
						if($tiperr == 0)
							$tiperr = 0;
				}
				// Verificacion de Incompatibilidad entre Especiales y Multiproposito 
				if (substr($Codequ,0,3) == "006" and $empresa == "radio")
				{
					$query = "select cod_med,cod_equ,cod_exa,fecha,hi,hf,nom_pac,nit_resp,telefono,edad,comentarios,usuario,activo from ".$empresa."_000001 where cod_equ = '002' ";
					$query = $query." and fecha = '".$wfec."'";
					$query = $query." and hi = '".$Hi."'";
					$query = $query." and hf = '".$Hf."'";	
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err); 
					if ($num > 0 ) 
						$tiperr = 6;
					else
						if($tiperr == 0)
							$tiperr = 0;
				}
			}
			//Validacion de ".$empresa."_000006 Especiales
			$query = "select medico_k,examen,activo from ".$empresa."_000005 where examen = '".substr($Codexa,0,strpos($Codexa,"-"))."'";
			$query = $query." and activo = 'A'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err); 
			if ($num > 0 )
			{
				$query = "select medico_k,examen,activo from ".$empresa."_000005 where examen = '".substr($Codexa,0,strpos($Codexa,"-"))."' and medico_k = '".substr($Codmed,0,5)."'";
				$query = $query." and activo = 'A'";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err); 
				if ($num == 0 ) 
					$tiperr = 4;
				else
					if($tiperr == 0)
						$tiperr = 0;
			}
			else
				if($tiperr == 0)
					$tiperr = 0;
			if ($wpar == 2 or ($wpar == 1 and $Medant != "N" and $Medant !=substr($Codmed,0,5)) or ($wpar == 1 and substr($wsw,0,1) == "1"))
			{
				// Disponibilidad del Medico
				$query = "select codigo,dia,hi,hf,activo,ndia from ".$empresa."_000007 where codigo = '".substr($Codmed,0,5)."'";
				$query = $query." and dia = '".substr($Diasem,0,2)."'";
				$query = $query." and hi <= '".$Hi."'";
				$query = $query." and hf >= '".$Hf."'";
				$query = $query." and activo = 'A'";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err); 
				if ($num == 0 ) 
					$tiperr = 1;
				else
					if($tiperr == 0)
						$tiperr = 0;	
			}
			if ($wpar == 2 or ($wpar == 1 and $Medant != "N" and $Medant != substr($Codmed,0,5)))
			{
				// Verificacion de la Ocupacion del Medico
				$query = "select hi from ".$empresa."_000001,".$empresa."_000008 where cod_med = '".substr($Codmed,0,5)."'";
				$query = $query." and fecha = '".$wfec."'";
				$query = $query." and ((hi <= '".$Hi."' and hf <= '".$Hf."' and hf > '".$Hi."')";
				$query = $query."  or  (hi >= '".$Hi."' and hf >= '".$Hf."' and hi < '".$Hf."')";
				$query = $query."  or  (hi <= '".$Hi."' and hf >= '".$Hf."')";
				$query = $query."  or  (hi >= '".$Hi."' and hf <= '".$Hf."'))";
				$query = $query." and ".$empresa."_000001.activo = 'A'";
				$query = $query." and cod_med = codigo";
				$query = $query." and tipo = 'S-SIMPLE'";	
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err); 
				if ($num > 0 ) 
					$tiperr = 2;
				else
					if($tiperr == 0)
						$tiperr = 0;
			}
			//Verificacion x Equipo si las citas son Multiples
			if(substr($wsw,1,1) == "1")
			{
				$query = "update ".$empresa."_000001 set activo='I' where  cod_equ='".substr($Codequ,0,3)."' and fecha='".$wfec."' and hi='".$Hi."' ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				
				$query = "select hi,hf from ".$empresa."_000001 where cod_equ = '".substr($Codequ,0,3)."'";
				$query = $query." and fecha = '".$wfec."'";
				$query = $query." and ((hi <= '".$Hi."' and hf <= '".$Hf."' and hf > '".$Hi."')";
				$query = $query."  or  (hi >= '".$Hi."' and hf >= '".$Hf."' and hi < '".$Hf."')";
				$query = $query."  or  (hi <= '".$Hi."' and hf >= '".$Hf."')";
				$query = $query."  or  (hi >= '".$Hi."' and hf <= '".$Hf."'))";
				$query = $query." and activo = 'A'";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err); 
				if ($num > 0 ) 
					$tiperr = 7;
				else
					if($tiperr == 0)
						$tiperr = 0;
						
				$query = "update ".$empresa."_000001 set activo='A' where  cod_equ='".substr($Codequ,0,3)."' and fecha='".$wfec."' and hi='".$Hi."' ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			}
			// Verificacion de los datos de texto
			if (strlen($Nompac)==0 or strlen($Tel)==0 or $Edad==0)
				$tiperr = 3;
			else
				if($tiperr == 0)
					$tiperr = 0;
		}
		else
		{
			$tiperr = 0;
			$wpar = 3;	
		}
		if ($tiperr == 0)
		{
			$posicion = strpos($Nitres,'-');
			$Nitres = substr($Nitres,$posicion+1,strlen($Nitres));
			switch ($wpar)
			{				
				case 1:
				$Nompac = strtoupper($Nompac);
				
				if( trim($Coment) != '' ){
//					$Coment2 = $comentarios."\n\n".$Coment;
					comentariosAnteriores( $pos2, $pos4, $pos5 );
					$Coment2 = trim( comentariosAnteriores( $pos2, $pos4, $pos5 )."\n\n".$Coment );
				}
				else{
					$Coment2 = comentariosAnteriores( $pos2, $pos4, $pos5 );
				}
				
				$query = "update ".$empresa."_000001 set cod_exa='".substr($Codexa,0,strpos($Codexa,"-"))."', hf= '".$Hf."', cod_med= '".substr($Codmed,0,5)."', nom_pac='".ucwords($Nompac)."', nit_resp='".$Nitres."', telefono='".$Tel."', edad=".$Edad.", comentarios='".$Coment2."',activo='".substr($Estado,0,1)."' where  cod_equ='".$Codequ."' and fecha='".$Fecha."' and hi='".$Hi."'";
				$err = mysql_query($query,$conex);
				if ($err = 1)
				{
					//echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>LOS DATOS ESTAN OK!!!!</MARQUEE></FONT>";
					//echo "<br><br>";
					$query = "select  codigo,descripcion,uni_hora,hi,hf,activo from ".$empresa."_000003 where codigo='".substr($Codequ,0,3)."'";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					$row = mysql_fetch_array($err);				
					$query = "select fecha,equipo,uni_hora,hi,hf from ".$empresa."_000004 where fecha = '".$Fecha."' and equipo = '".substr($Codequ,0,3)."'";
					$err = mysql_query($query,$conex);	
					$num = mysql_num_rows($err);
					if ($num == 0)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000004 (medico,fecha_data,hora_data,fecha,equipo,uni_hora,hi,hf,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$Fecha."','".substr($Codequ,0,3)."',".$row[2].",'".$row[3]."','".$row[4]."','C-".$empresa."')";
						$err = mysql_query($query,$conex);	
						 if ($err != 1)
					 	{
						 	echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>ERROR EN ESTRUCTURA!!!!</MARQUEE></FONT>";
							echo "<br><br>";
						}
						else
						{
							echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>LOS DATOS ESTAN OK!!!!</MARQUEE></FONT>";
							echo "<br><br>";
						}
					}
					else
					{
						echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>LOS DATOS ESTAN OK!!!!</MARQUEE></FONT>";
						echo "<br><br>";
					}
				}
				break;
				case 2:
				$Nompac = strtoupper($Nompac);
				$Nompac = ucwords($Nompac);
				$fecha = date("Y-m-d");
				$hora = (string)date("H:i:s");
				$query = "insert ".$empresa."_000001 (medico,fecha_data,hora_data,cod_med,cod_equ,cod_exa,fecha,hi,hf,nom_pac,nit_resp,telefono,edad,comentarios,usuario,activo,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".substr($Codmed,0,5)."','".substr($Codequ,0,3)."','".substr($Codexa,0,strpos($Codexa,"-"))."','".$Fecha."','".$Hi."','".$Hf."','".ucwords($Nompac)."','".$Nitres."','".$Tel."',".$Edad.",'".$Coment."','".substr($user,2,strlen($user))."','".substr($Estado,0,1)."','C-".$empresa."')";
				$err = mysql_query($query,$conex);
				if ($err != 1)
				{
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR EN LA INSERCION DEL TURNO -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
					echo "<br><br>";
				}
				else
				{
					$query = "select  codigo,descripcion,uni_hora,hi,hf,activo from ".$empresa."_000003 where codigo='".substr($Codequ,0,3)."'";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					$row = mysql_fetch_array($err);				
					$query = "select fecha,equipo,uni_hora,hi,hf from ".$empresa."_000004 where fecha = '".$Fecha."' and equipo = '".substr($Codequ,0,3)."'";
					$err = mysql_query($query,$conex);	
					$num = mysql_num_rows($err);
					if ($num == 0)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000004 (medico,fecha_data,hora_data,fecha,equipo,uni_hora,hi,hf,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$Fecha."','".substr($Codequ,0,3)."',".$row[2].",'".$row[3]."','".$row[4]."','C-".$empresa."')";
						$err = mysql_query($query,$conex);	
						 if ($err != 1)
					 	{
						 	echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>ERROR EN ESTRUCTURA!!!!</MARQUEE></FONT>";
							echo "<br><br>";
						}
						else
						{
							echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>LOS DATOS ESTAN OK!!!!</MARQUEE></FONT>";
							echo "<br><br>";
						}
					}
					else
					{
						echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>LOS DATOS ESTAN OK!!!!</MARQUEE></FONT>";
						echo "<br><br>";
					}		
				}
				break;
				case 3:
				$query = "delete from ".$empresa."_000001 where  cod_equ= '".substr($Codequ,0,3)."' and fecha='".$Fecha."' and hi='".$Hi."'";
				$err = mysql_query($query,$conex);
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#00FF00 LOOP=-1>!!!! TURNO BORRADO !!!!</MARQUEE></FONT>";
				echo "<br><br>";
				unset($Codmed);
				unset($Codexa);
				unset($Nompac);
				unset($Nitres);
				unset($Tel);
				unset($Edad);
				unset($Coment);
				unset($Estado);
				break;
			}
		}
		else
		{
			echo "<center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			switch ($tiperr)
			{
				case 1:
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#00ffff LOOP=-1>EL MEDICO NO ESTA DISPONIBLE EN ESE HORARIO -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
				break;
				case 2:
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33cc33 LOOP=-1>EL MEDICO YA TIENE UNA CITA ASIGNADA INCOMPATIBLE CON LA QUE USTED DESEA -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
				break;
				case 3:
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CCCCFF LOOP=-1>LOS DATOS ESTAN INCOMPLETOS -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
				break;
				case 4:
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>EXAMEN ESPECIAL NO REALIZADO POR ESTE MEDICO -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
				break;
				case 5:
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>TURNO ASIGNADO EN OTRA ESTACION -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
				break;
				case 6:
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FFCC66 LOOP=-1>INCOMPATIBILIDAD DE TURNOS ENTRE ESPECIALES Y MULTIPROPOSITO -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
				break;
				case 7:
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CCCCFF LOOP=-1>INCOMPATIBILIDAD DE TURNOS EQUIPO OCUPADO -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
				break;
			}
		}
	}
	$year = (integer)substr($pos4,0,4);
	$month = (integer)substr($pos4,5,2);
	$day = (integer)substr($pos4,8,2);
	$nomdia=mktime(0,0,0,$month,$day,$year);
	$nomdia = strftime("%w",$nomdia);
	switch ($nomdia)
	{
		case 0:
			$diasem = "DOMINGO";
			break;
		case 1:
			$diasem = "LUNES";
			break;
		case 2:
			$diasem = "MARTES";
			break;
		case 3:
			$diasem = "MIERCOLES";
			break;
		case 4:
			$diasem = "JUEVES";
			break;
		case 5:
			$diasem = "VIERNES";
			break;
		case 6:
			$diasem = "SABADO";
			break;
	}
	echo "<input type='HIDDEN' name= 'pos1' value='".$pos1."'>";
	echo "<input type='HIDDEN' name= 'pos2' value='".$pos2."'>";
	echo "<input type='HIDDEN' name= 'pos3' value='".$pos3."'>";
	echo "<input type='HIDDEN' name= 'pos4' value='".$pos4."'>";
	echo "<input type='HIDDEN' name= 'pos5' value='".$pos5."'>";
	echo "<input type='HIDDEN' name= 'pos6' value='".$pos6."'>";
	echo "<input type='HIDDEN' name= 'pos7' value='".$pos7."'>";
	echo "<input type='HIDDEN' name= 'pos8' value='".$pos8."'>";
	echo "<input type='HIDDEN' name= 'pos9' value='".$pos9."'>";
	echo "<input type='HIDDEN' name= 'wtit' value='".$wtit."'>";
	echo "<input type='HIDDEN' name= 'Diasem' value='".$diasem."'>";
	$query = "select cod_med,cod_equ,cod_exa,fecha,hi,hf,nom_pac,nit_resp,telefono,edad,comentarios,usuario,activo from ".$empresa."_000001 where  cod_equ='".$pos2."' and fecha='".$pos4."' and hi='".$pos5."'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	echo "<table border=0 align=center>";
	echo "<li><A HREF='000001_prx7.php?wequ=".$pos2."-".$wtit."&wtit=".$wtit."&wfec=".$pos4."&empresa=".$empresa."&amp;wsw=".$wsw."'>Retornar</A><br>";
	if ($num > 0)
		echo "<li><A HREF='000001_prx4.php?par1=".$pos2."&par2=".$pos4."&par3=".$pos5."&empresa=".$empresa."' target = '_blank'>Imprimir</A><br>";
	if ($num > 0)
	{
		$Medant=$pos1;
		$row = mysql_fetch_array($err);
		if(!isset($wpar))
		{
			$query = "select descripcion,nit from ".$empresa."_000002 WHERE nit =  '".$row[7]."' order by descripcion ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$row[7]=$row1[0]."-".$row1[1];
			}
		}
		else
		{
			$query = "select descripcion,nit from ".$empresa."_000002 WHERE nit =  '".$Nitres."' order by descripcion ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$row[7]=$row1[0]."-".$row1[1];
			}
		}
		$pos6=$row[5];
		echo "<input type='HIDDEN' name= 'wpar' value='1'>";
	}
	else
	{
		$Medant="N";
		$row=array();
		if(!isset($Codmed))
			$row[0]="0";  			//Medico
		else
			$row[0]=$Codmed;	
		$row[1]=$pos2;			//Equipo
		if(!isset($Codexa))
			$row[2]="0";				//Examen
		else
			$row[2]=$Codexa;	
		$row[3]=$pos4;			//Fecha
		$row[4]=$pos5;			//Hi
		if(substr($wsw,1,1) == "0")
			$row[5]=$pos6;			//Hf
		if(!isset($Nompac))
			$row[6]="";					//Paciente
		else
			$row[6]=$Nompac;	
		if(!isset($Nitres))
			$row[7]="";					//Responsable
		else
			$row[7]=$Nitres;		
		if(!isset($Tel))
			$row[8]="";					//Telefono
		else
			$row[8]=$Tel;	
		if(!isset($Edad))
			$row[9]="";					//Edad
		else
			$row[9]=$Edad;
		if(!isset($Coment))
			$row[10]="";				//Cometarios
		else
			$row[10]=$Coment;	
		$row[11]="";
		$row[12]="";
		echo "<input type='HIDDEN' name= 'wpar' value='2'>";
	}
	echo "<input type='HIDDEN' name= 'Codmed' value='".$row[0]."'>";
	echo "<input type='HIDDEN' name= 'Medant' value='".$Medant."'>";
	echo "<input type='HIDDEN' name= 'Codequ' value='".$row[1]."'>";
	echo "<input type='HIDDEN' name= 'Codexa' value='".$row[2]."'>";
	echo "<input type='HIDDEN' name= 'Fecha' value='".$row[3]."'>";
	echo "<input type='HIDDEN' name= 'Hi' value='".$row[4]."'>";
	if(substr($wsw,1,1) == "0")
		echo "<input type='HIDDEN' name= 'Hf' value='".$row[5]."'>";
	echo "<input type='HIDDEN' name= 'wfec' value='".$pos4."'>";
	echo "<input type='HIDDEN' name= 'wequ' value='".$row[1]."'>";
	//echo "<input type='HIDDEN' name= 'wtit' value='".$pos9."'>";
	echo "<tr>";
	echo "<td bgcolor=#999999><b>Item</td></b>";
	echo "<td bgcolor=#999999><b>Valor</b></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Medico o Tecnico</td>";			
	echo "<td bgcolor=#cccccc>";
	if(substr($wsw,0,1) == "1")
	{
		$query = "select citasim_000007.Codigo,citasim_000008.Nombre from citasim_000007,citasim_000008 ";
		$query .= " where citasim_000007.equipo = '".$row[1]."' ";
		$query .= "      and citasim_000007.dia = '".substr($diasem,0,2)."' ";
		$query .= "      and citasim_000007.hi <= '".$row[4]."' ";
		if(!isset($row[5]))
			$query .= "      and citasim_000007.hf >= '".$pos6."' ";
		else
			$query .= "      and citasim_000007.hf >= '".$row[5]."' ";
		$query .= "      and citasim_000007.activo = 'A' ";
		$query .= "      and citasim_000007.codigo = citasim_000008.codigo  ";
		$query .= "  order by citasim_000007.codigo";
	}
	else
		$query = "select codigo,nombre,oficio,tipo,edad_pac,activo from ".$empresa."_000008 where oficio='1-MEDICO' and codigo != '0' order by codigo";
	$err1 = mysql_query($query,$conex);
	$num1 = mysql_num_rows($err1);
	echo "<select name='Codmed'>";
	for ($i=0;$i<$num1;$i++)
	{	
		$row1 = mysql_fetch_array($err1);
		if((isset($Codmed) and substr($Codmed,0,5) == $row1[0]) or (strlen($pos1) > 1 and $pos1 == $row1[0]))
			echo "<option selected>".$row1[0]."-".$row1[1]."</option>";
		else
			echo "<option>".$row1[0]."-".$row1[1]."</option>";
	}
	echo "</td></tr>";
	$query = "select codigo,descripcion,uni_hora,hi,hf,activo from ".$empresa."_000003 where codigo='".$pos2."'";
	$err1 = mysql_query($query,$conex);
	$num1 = mysql_num_rows($err1);
	$row1 = mysql_fetch_array($err1);
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Equipo</td>";			
	echo "<td bgcolor=#cccccc>".$row1[0]."-".$row1[1]."</td>";
	echo "</tr>";
	if(substr($wsw,2,1) == "1")
	{
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Criterio x Examen</td>";
		echo "<td bgcolor=#cccccc><input type='TEXT' name='Criterio' size=50 maxlength=50></td>";			
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Examen</td>";			
		echo "<td bgcolor=#cccccc>";
		echo "<select name='Codexa'>";
		if(isset($Criterio) and $Criterio != "")
			$query = "select codigo,descripcion,activo from ".$empresa."_000006 where descripcion like '%".$Criterio."%'  order by codigo";
		else
			if(isset($Codexa))
				$query = "select codigo,descripcion,activo from ".$empresa."_000006  where codigo='".substr($Codexa,0,strpos($Codexa,"-"))."' ";
			else
				$query = "select codigo,descripcion,activo from ".$empresa."_000006  where codigo='".$row[2]."' ";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		for ($i=0;$i<$num1;$i++)
		{	
			$row1 = mysql_fetch_array($err1);
			if((isset($Codexa) and substr($Codexa,0,strpos($Codexa,"-")) == $row1[0]) or ($pos3 != "0" and $pos3 == $row1[0]))
				echo "<option selected>".$row1[0]."-".$row1[1]."</option>";
			else
				echo "<option>".$row1[0]."-".$row1[1]."</option>";
		}
		echo "</td></tr>";
	}
	else
	{
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Examen</td>";			
		echo "<td bgcolor=#cccccc>";
		echo "<select name='Codexa'>";
		$query = "select codigo,descripcion,preparacion,cod_equipo,activo,especial from ".$empresa."_000006 where cod_equipo='".$pos2."' and codigo != '0' and activo = 'A' order by codigo";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		for ($i=0;$i<$num1;$i++)
		{	
			$row1 = mysql_fetch_array($err1);
			
			if((isset($Codexa) and substr($Codexa,0,strpos($Codexa,"-")) == $row1[0]) or ($pos3 != "0" and $pos3 == $row1[0]))
				echo "<option selected>".$row1[0]."-".$row1[1]."</option>";
			else
				echo "<option>".$row1[0]."-".$row1[1]."</option>";
		}
		echo "</td></tr>";
	}
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Fecha</td>";
	echo "<td bgcolor=#cccccc>".$diasem." ".$pos4."</td>";			
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Hora Inicio</td>";
	if(substr($pos5,0,2) > "12")
	{
		$hr1 ="". (string)((integer)substr($pos5,0,2) - 12).":".substr($pos5,2,2). " pm ";
		echo "<td bgcolor=#cccccc><font size=2>".$hr1."</font></td>";
	}
	else
		echo "<td bgcolor=#cccccc><font size=2>".substr($pos5,0,2).":".substr($pos5,2,2)."</font></td>";
	echo "</td>";
	echo "</tr>";
	//wsw=x.1 Citas Multiples
	if(substr($wsw,1,1) == "1")
	{
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Hora Final</td><td bgcolor=#cccccc><select name='Hf'>";
		$whi = $pos5;
		$inc = $pos9;
		$part1 = (int)substr($whi,0,2);
		$part2 = (int)substr($whi,2,2);
		$part2 = $part2 + $inc;
		while ($part2 >= 60)
		{
			$part2 = $part2 - 60;
			$part1 = $part1 + 1;
		}
		$whf = (string)$part1.(string)$part2;
		if ($part1 < 10)
			$whf = "0".$whf;
		if ($part2 < 10)
			$whf = substr($whf,0,2)."0".substr($whf,2,1);
		$whi = $whf;
		$wul = $pos7;
		$part1 = (int)substr($whi,0,2);
		$part2 = (int)substr($whi,2,2);
		$part2 = $part2 + $inc;
		while ($part2 >= 60)
		{
			$part2 = $part2 - 60;
			$part1 = $part1 + 1;
		}
		$whf = (string)$part1.(string)$part2;
		if ($part1 < 10)
			$whf = "0".$whf;
		if ($part2 < 10)
			$whf = substr($whf,0,2)."0".substr($whf,2,1);	
		while ($whi <= $wul)
		{
			if(substr($whi,0,2) > "12")
			{
				$hr1 ="". (string)((integer)substr($whi,0,2) - 12).":".substr($whi,2,2). " pm ";
				if($pos6 == $whi)
					echo "<option selected><font size=2>".$hr1."</font></option>";
				else
					echo "<option><font size=2>".$hr1."</font></option>";
			}
			else
				if($pos6 == $whi)
					echo "<option selected><font size=2>".substr($whi,0,2).":".substr($whi,2,2)."</font></option>";
				else
					echo "<option><font size=2>".substr($whi,0,2).":".substr($whi,2,2)."</font></option>";
			$whi = $whf;
			$part1 = (int)substr($whi,0,2);
			$part2 = (int)substr($whi,2,2);
			$part2 = $part2 + $inc;
			while ($part2 >= 60)
			{
				$part2 = $part2 - 60;
				$part1 = $part1 + 1;
			}
			$whf = (string)$part1.(string)$part2;
			if ($part1 < 10)
				$whf = "0".$whf;
			if ($part2 < 10)
				$whf = substr($whf,0,2)."0".substr($whf,2,1);
		}
		echo "</td>";
		echo "</tr>";
	}
	else
	{
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Hora Final</td>";
			if(substr($pos6,0,2) > "12")
		{
			$hr1 ="". (string)((integer)substr($pos6,0,2) - 12).":".substr($pos6,2,2). " pm ";
			echo "<td bgcolor=#cccccc><font size=2>".$hr1."</font></td>";
		}
		else
			echo "<td bgcolor=#cccccc><font size=2>".substr($pos6,0,2).":".substr($pos6,2,2)."</font></td>";
		echo "</td>";
		echo "</tr>";
	}
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Paciente</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Nompac' size=50 maxlength=50 value='".$row[6]."'></td>";			
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Responsable_Cuenta</td>";
	echo "<td bgcolor=#cccccc>";	
	echo "<select name='Nitres'>";
	$query = "select descripcion,nit from ".$empresa."_000002 WHERE nit !=  '0' order by descripcion ";
	$err1 = mysql_query($query,$conex);
	$num1 = mysql_num_rows($err1);
	for ($i=0;$i<$num1;$i++)
	{	
		$row1 = mysql_fetch_array($err1);
		if ($row[7] == $row1[0]."-".$row1[1])
			echo "<option selected>".$row1[0]."-".$row1[1]."</option>";
		else
			echo "<option>".$row1[0]."-".$row1[1]."</option>";
	}
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Telefono</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Tel' size=30 maxlength=30 value='".$row[8]."'></td>";			
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Edad</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Edad' size=3 maxlength=3 value='".$row[9]."'></td>";			
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Comentarios</td>";
	echo "<td bgcolor=#cccccc><textarea name='Coment' cols=60 rows=5>".$row[10]."</textarea>";			
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Insertar <br> Comentario Fijo</td>";
	echo "<td bgcolor=#cccccc><input type='checkbox' name='Fijo'>";			
	echo "</td>";
	echo "</tr>";
	if(substr($wsw,2,1) == "1")
	{
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Datos OK</td>";
		echo "<td bgcolor=#cccccc><input type='checkbox' name='ok'>";			
		echo "</td>";
		echo "</tr>";
	}
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Activo</td>";
	echo "<td bgcolor=#cccccc>";			
	echo "<select name='Estado'>";
	if ($row[12] == substr("A-Activo", 0, 1))
		echo "<option selected>A-Activo</option>";
	else
		echo "<option>A-Activo</option>";
	if ($row[12] == substr("I-Inactivo", 0, 1))
		echo "<option selected>I-Inactivo</option>";
	else
		echo "<option>I-Inactivo</option>";	
	echo "</td>";	
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc colspan=2 align=center><input type='submit' value='GRABAR'></td>";
	echo "</tr>";
	echo "</table>";
	echo "<table border=0 align=center>";
	echo "<tr><td><A HREF='#Arriba'><B>Arriba</B></A></td></tr></table>";



	if( true || $pos1 != '0' ){ 
	//	comentariosAnteriores( substr( $Codequ,0,3), $wfec, $Hi );
	
		$comentarios = comentariosAnteriores( $pos1, $pos4, $pos5 );
		$comentarios = comentariosAnteriores( $pos2, $pos4, $pos5 );
		echo "<textarea name='hiComentAnt' rows='5' cols='60' readOnly style='display:none;background:#FFFFEE'>$comentarios</textarea>";
		echo "<input type='hidden' name='hiInicio' value='on'>";
	}
	
	if( !isset($nuevo) ){
		echo "<INPUT type='hidden' name='nuevo' value='on'>";
	}
	else{
		echo "<INPUT type='hidden' name='nuevo' value='off'>";
	}
	
	
	include_once("free.php");
}

echo "</form>"
?>
</body>
</html>