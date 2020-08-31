<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");
?>
<script src="../../../include/root/jquery-1.3.2.js" type="text/javascript"></script>
<script>
function estado(){ 
    if (document.pantalla.chktodos.checked) 
     //alert("Marcado"); 
	 seleccionar_todo();
    else 
     //alert("Desmarcado");
     deseleccionar_todo();	 
   } 

function seleccionar_todo(){
   for (i=0;i<document.pantalla.elements.length;i++)
      if(document.pantalla.elements[i].type == "checkbox")
         document.pantalla.elements[i].checked=1
} 

function deseleccionar_todo(){
   for (i=0;i<document.pantalla.elements.length;i++)
      if(document.pantalla.elements[i].type == "checkbox")
         document.pantalla.elements[i].checked=0
} 

function enviar(){
		document.forms["fecha"].submit();	
}


//function confirmar(ban){
function confirmar(){
		
	if( haySeleccionados() ){
		if (confirm("Se cambiara el medico")) {
			document.getElementById('ban').value=1;
			document.forms["pantalla"].submit();
		}
	}
	else{
	document.getElementById('ban').value=0;
	document.forms["pantalla"].submit();
	
	}
}

function evaluar(){

	if( document.getElementById( "slDoctorNuevo" ).selectedIndex > 0 ){

		if (!haySeleccionados()){
			alert("Seleccione un paciente");
			confirmar();
		}
		else{
			confirmar();
		}
	}
	else{
		alert( "Debe seleccionar el medico nuevo" );
	}
}


function mensaje(men){
   alert(men);
}

function haySeleccionados(){
   for (i=0;i<document.pantalla.elements.length;i++)
      if(document.pantalla.elements[i].type == "checkbox")
         if( document.pantalla.elements[i].checked == true )
			return true;
			
	return false;
}

function deshabilitarCheckboxs(){
	//alert("entro deshabilitar");
   for (i=0;i<document.pantalla.elements.length;i++)
      if(document.pantalla.elements[i].type == "checkbox")
         // if( document.pantalla.elements[i].disabled == false )
			// document.pantalla.elements[i].disabled == true;
			
			
	return false;
}

function habilitarCheckboxs(){
//alert("entro habilitar");
   for (i=0;i<document.pantalla.elements.length;i++)
      if(document.pantalla.elements[i].type == "checkbox")
        // if( document.pantalla.elements[i].disabled == true )
		//	document.pantalla.elements[i].disabled == false;
		
			
	return false;
}

function cargar(){
   if( document.getElementById( "slDoctorNuevo" ).selectedIndex > 0 )
   {
		//alert("if");
		habilitarCheckboxs();
   }
   else
   {	
		//alert("else");
		//deshabilitarCheckboxs();
		$(':checkbox').attr('disabled',true);
   }
}

</script>
				
<?php 
		 

/**
 * Programa:	cambioMedico.php
 * Por:			Maria Viviana Rodas
 * Fecha:		2012-07-23
 * Descripcion:	Este programa permite al director medico, ya sea de la clinica o de la atencion domiciliaria
 *              hacer cambio de medico para los pacientes, en un caso dado que el medico no los pueda atender, asignales uno nuevo.
 *              Inicialmente se listan todos los pacientes del medico que se seleccione, si se desea cambiar el medico en
 *              un checkbox se selecciona el paciente, tambien se pueden seleccionar todos y en un combobox se puede seleccionar el medico nuevo, se 
 *              le da clic en el boton cambiar, generando la nueva lista con los datos 
 *              actualizados.
 
 Modificaciones:
 2012-11-17: Se revisa la validacion del horario desde la hora de la cita hasta la fecha incial.
 2012-08-23: Se agrega la funcion evaluar que hace la validacion de si esta seleccionado el medico nuevo, si es asi hace una
			 llamando a la funcion si hay checkbox selccionados y permite seguir el proceso, si no es asi muestra un mensaje de que debe 
			 seleccionar un medico. Viviana Rodas
 2012-08-18: Se agrega la funcion haySeleccionados que evalua si hay al menos un checkbox chequeado. Viviana Rodas
 2012-08-14: Se agrega la funcion cargar que evalua si el medico nuevo esta seleccioado si es asi habilita los checkbox. Viviana Rodas   
 2012-08-08: Se agregan las funciones deshabilitarCheckboxs y habilitarCheckbox. Viviana Rodas
 2012-08-02: Se agregan las funciones estado y enviar, la primera evalua el estado de los checkbox, seleccionandolos o deseleccionandolos,
							 la segunda envia el rango de fechas para la cual se va hacer la consulta. Viviana Rodas
 2012-07-27: Se agregan las funciones de seleccionar_todo y deselecccionar todo para los checkbox que acompañan 
							 cada paciente que tiene cita.Viviana Rodas
                 
				 
				 
 */

/**
 * Variables del sistema
 * 
 * $add			Campos adicionales para crear el link a la historia clinica
 * $pos			Posicion del campo nro de documento de la historia clinica
 */

/********************************************************************************************************
 * FUNCIONES
 *******************************************************************************************************/
 //Busca la solucion correspondientes de citas para la empresa dada
function solucionCitas( $codEmp ){
	
	global $conex;
	global $solucionCitas;
	
	$solucionCitas = '';
	
	$sql = "SELECT
				detval
			FROM
				root_000051
			WHERE
				detapl = 'citas'
				AND detemp = '$codEmp'
			";
	
	$res = mysql_query( $sql, $conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$solucionCitas = $rows[0];
	}
	
	return $solucionCitas;
}


//funcion para hallar la cita anterior 
			function calcular($horaInicioA, $horaCita, $uni_horaSeg)
			{
				global $totalCitas;
				// global $citaAnterior;
				global $citaSiguiente;
				
				if($horaInicioA>$horaCita)
				{
					$citaSiguiente=$horaInicioA;
				}
				
				if( $horaCita-$horaInicioA >= 0 ){
					$totalCitas=floor(($horaCita-$horaInicioA)/$uni_horaSeg);
					$citaAnterior=(($totalCitas*$uni_horaSeg)+$horaInicioA);
				}
				else{
					$citaAnterior=$horaInicioA;
				}
				
				$citaSiguiente=($citaAnterior+$uni_horaSeg);
				
				return $citaAnterior;
			}
			
			

//Funcion que actualiza el medico para uno o varios pacientes seleccionados
                    
function actualizar($paciente, $med, $solucionCitas, $conex, $fechaCita, $horaCita, $horaFinCita, $medAnt, $cedula, $med1, $filtro, $key, $wfecini, $wfecfin)
{
	global $mensaje;
	global $wbasedato;
	
	$totalCitas="";
	$citaAnterior="";
	$citaSiguiente="";
	$horaNueva="";
	$horaNuevaFinal="";
	$pacienteColor="";
	
	$val = false; 
		 //extraer el valor numerico del dia
		$numDia=date("N",strtotime($fechaCita));
		//echo "dia ".$numDia;
		
		// med: cod_equ tabla citas_09 y medcid de la tabla clisur_51
		// fechaCita: fecha de la cita
		// horaCita:  hora cita
		// horaFinCita: hora finalizacion cita
		// medAnt: medcod medico anterior
		// med1: medcod medico nuevo
		// filtro: medcod medico viejo
		
		
	        //consultar si atiende ese dia, la unidad de hora, el tiempo de atencion del medico de la tabla citascs_10 con cada dia dentro del rango
			$sql7= "select uni_hora, hi, hf from citascs_000010 where codigo='".$med."' and dia='".$numDia."' and activo != 'I'";
			
			$res7 = mysql_query($sql7,$conex) or die( mysql_errno()." - Error en el query $sql7 - ".mysql_error() );
			$num7 = mysql_num_rows( $res7 );
			if ($num7>0) //si atiende ese dia y esta activo
			{
				$rows7 = mysql_fetch_array( $res7 );
				$uni_hora=$rows7['uni_hora'];
				$hi1=$rows7['hi'];
				$hf1=$rows7['hf'];
				
				$horaFinAtencion=$hf1;
				$horaFinAtencion=strtotime($fechaCita." ".$hf1);
				
				//pasar la fecha cita, horacita, horafincita y uni_hora a segundos.
				$horaInicioA=strtotime($fechaCita." ".$hi1);
				$horaCita=strtotime($fechaCita." ".$horaCita);
				$uni_horaSeg=($uni_hora*60);
				
				//funcion para hallar la cita anterior y la cita siguiente
				 $citaAnterior = calcular($horaInicioA, $horaCita, $uni_horaSeg);
				 
				 //si la hora cita es mayor que la hora finalizacion atencion medico no se puede asignar
				 $ultimaCita=$horaFinAtencion-$uni_horaSeg;
				 
			}
			else
			{
				$mensaje=3;
				 // ?><script>
				   // var men="El medico no atiende en la fecha:<?php echo $fechaCita; ?>";
				   // mensaje(men);</script><?php
				   return false;
				
				
			}
			 if ( ($horaCita > $horaFinAtencion && $horaFinAtencion > 0 ) or $horaCita >= $ultimaCita)
			 {
				 $mensaje=4;
				 $val = true;
				 //$horaCita=date("H:i:s",$horaCita)
				// ?><script>
				   // var men="La hora: <?php echo $horaCita; ?> de la cita excede el tiempo de atencion del medico";
				   // mensaje(men);</script><?php
				    //return false;				 
					   
			 }
			 
			//se recorre el horario del medico desde la citaanterior hasta la horafinatencion incrementando en la unidad de hora
			for( $i = $citaAnterior; $i < strtotime( $fechaCita." ".$hf1 );  $i += $uni_horaSeg ){
			
			 $sql4 = "SELECT cod_equ, fecha, TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as hi, hf
						  FROM {$solucionCitas}_000009
						  WHERE cod_equ = '".$med."'
						  AND fecha = '".date( $fechaCita, $i )."'
						  AND hi = '".date( "Hi", $i )."' ";
		
				$res4 = mysql_query($sql4,$conex) or die( mysql_errno()." - Error en el query $sql4 - ".mysql_error() );					
				$num = mysql_num_rows( $res4 );
				
				//si no encuentra registros se le asigna la cita porque tiene el espacio
				if( $num == 0 ){
					$horaNueva=date( "Hi", $i );
					$horaNuevaFinal=date( "Hi", $i+$uni_horaSeg );
					$fechaNueva=date($fechaCita, $i );
					break;
				}
				
				
			} //for
		     
			if( isset($num) && $num == 0 ) 
			{
			  
	         //actualizacion del medico consulta de actualizacion de la tabla citascs_000009
				
				$sql5 = "UPDATE {$solucionCitas}_000009 
					  SET cod_equ = '".$med."',
					  hi = '".$horaNueva."',
					  hf = '".$horaNuevaFinal."',
					  fecha = '".$fechaNueva."'
					  WHERE id = ".$paciente." ";
		
				$res5 = mysql_query($sql5, $conex) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );
			   
				if( mysql_affected_rows() > 0 ){
				$val = false;
				$mensaje=2;
				
				$horaCita1=date("H:i:s",$horaCita);
				$sql6 = "INSERT INTO {$solucionCitas}_000022 (Medico,Fecha_data, Hora_data, Cedula, Fecha_cita, Hora_cita, Medico_ant, Medico_act, Usuario_camb, Fecha_camb, Hora_camb, Seguridad) 
						 VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$cedula."','".$fechaCita."','".$horaCita1."','".$filtro."','".$med1."','".$key."','".date( "Y-m-d" )."','".date( "H:i:s" )."', 'C-".$wbasedato."') ";
				
				$res6 = mysql_query($sql6, $conex) or die( mysql_errno()." - Error en el query $sql6 - ".mysql_error() );
			   
				}
				else
				{
				 echo "NO Actualizo";
				}	        
				
			}
		
				//si $num es mayor que cero, se busca en el horario del medico de la hora de la cita, hacia atras
			   if ( $val or !isset($num) or $num > 0)
			   {
			   
				//se recorre el horario del medico desde la hora de la cita hasta la cita incial decrementando en la unidad de hora
				//se encuentra la cita siguiente para hacer el calculo hacia atras
				
				
					for( $i = $horaFinAtencion-$uni_horaSeg; $i >= $horaInicioA;  $i -= $uni_horaSeg )
					{
					
					 $sql4 = "SELECT cod_equ, fecha, TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as hi, hf
								  FROM {$solucionCitas}_000009
								  WHERE cod_equ = '".$med."'
								  AND fecha = '".date( $fechaCita, $i )."'
								  AND hi = '".date( "Hi", $i )."' ";
				
						$res4 = mysql_query($sql4,$conex) or die( mysql_errno()." - Error en el query $sql4 - ".mysql_error() );					
						$num1 = mysql_num_rows( $res4 );
						
						//si no encuentra registros se le asigna la cita porque tiene el espacio de la cita hacia atras
						if( $num1 == 0 ){
							$horaNueva=date( "Hi", $i );
							$horaNuevaFinal=date( "Hi", $i+$uni_horaSeg );
							$fechaNueva=date($fechaCita, $i );
							break;
						}
						
						
					} //for
					
				   
			   }
			   
			   if( isset($num) && @$num1 == 0 )  //verificar validacion se agrego el isset
				{
			  
						//actualizacion del medico consulta de actualizacion de la tabla citascs_000009
						
						$sql5 = "UPDATE {$solucionCitas}_000009 
							  SET cod_equ = '".$med."',
							  hi = '".$horaNueva."',
							  hf = '".$horaNuevaFinal."',
							  fecha = '".@$fechaNueva."'
							  WHERE id = ".$paciente." ";
				
						$res5 = mysql_query($sql5, $conex) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );
					   
						if( mysql_affected_rows() > 0 ){
						$mensaje=2;
						
						$horaCita=date("H:i:s",$horaCita);
						$sql6 = "INSERT INTO {$solucionCitas}_000022 (Medico, Fecha_data, Hora_data, Cedula, Fecha_cita, Hora_cita, Medico_ant, Medico_act, Usuario_camb, Fecha_camb, Hora_camb, Seguridad) 
								 VALUES ('".$wbasedato."', '".date("Y-m-d")."', '".date("H:i:s")."', '".$cedula."','".$fechaCita."','".$horaCita."','".$filtro."','".$med1."','".$key."','".date( "Y-m-d" )."','".date( "H:i:s" )."', 'C-".$wbasedato."') ";
						
						$res6 = mysql_query($sql6, $conex) or die( mysql_errno()." - Error en el query $sql6 - ".mysql_error() );
					   
						}
						// else
						// {
						 // echo "NO Actualizo 1";
						// }	        
						
				}
			
			   
			   if (@$num1>0)
			   {
					$mensaje=5;
				   // ?><script>
				   // var men="El medico no tiene citas disponibles, Horario lleno";
				   // mensaje(men);</script><?php
				   return false;
				}   
   //return $horaCita;
	
	      
   
}
 
 /********************************************************************************************************
 * INICIO DEL PROGRAMA
 *******************************************************************************************************/

include_once("root/comun.php");
$wactualiz="(2012-11-17)";

if( !isset($wemp_pmla) ){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}
$key = substr($user, 2, strlen($user));


$conex = obtenerConexionBD("matrix");

$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

$wbasedato = strtolower( $institucion->baseDeDatos );
$wentidad = $institucion->nombre;


session_start();  
//el usuario se encuentra registrado
if(!isset($_SESSION['user']))
    echo "error";
else
{
encabezado("Cambio de Medico Director Medico ",$wactualiz, "logo_".$wbasedato);

$mensaje=0;
$paciente="";

    echo "<html>";
	echo "<head>";
	echo "</head>";
    echo "<body onload='cargar()'>";
	  
  //rango de fecha para cambiar el medico***
	echo "<div align='center'><br />";
	echo "<form name='pantalla'  method=post>";
	echo "<table>";
	echo "<tr>";
	echo "<th colspan='4' class='encabezadotabla' align=center valign='top'>Seleccione el rango de fechas</th>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='fila1' align=center valign='top'>Fecha Inicial</td>";
	echo "<td class='fila2' align='center'>";
		if(isset($wfecini) && !empty($wfecini))
		{
			campoFechaDefecto("wfecini",$wfecini);
		} else 
		{
			campoFecha("wfecini");
		}
		echo "</td>";
	echo "<td class='fila1' align=center valign='top'>Fecha Final</td>";
	echo "<td class='fila2' align='center'>";
		if(isset($wfecfin) && !empty($wfecfin))
		{
			campoFechaDefecto("wfecfin",$wfecfin);
		} else 
		{
			campoFecha("wfecfin");
		}
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
	//echo "<td colspan='4' align='center'><input type='button' value='Enviar' style='width:100' onclick='javascript: enviar();'></td>";
	echo "</tr>";
	echo "</table>";
	//echo "</form>";
	 // echo "fecha inicial ".$wfecini." ";
	 // echo "fecha final ".$wfecfin;
	 
	echo "</div>";
	//***********


$solucionCitas = solucionCitas( $wemp_pmla );
//Buscando el doctor por el que fue filtrado
	if( isset( $slDoctor ) ){
		
		$nmFiltro = $slDoctor;
		$exp = explode( " - ", $slDoctor);
		$filtro = $exp[0];
	}
	else{
	   $filtro = "";
	}
	
	
	if ( isset($slDoctorNuevo) && $slDoctorNuevo != "Seleccione...")
	{
		//medcid que es el codigo que une la tabla citascs_000010 y la clisur_000051, este se toma de la seleccion del medico nuevo
				  $mednuevo=explode(" - ",$slDoctorNuevo);
				  @$med=$mednuevo[2];
				  $med1=$mednuevo[0];
				  // echo "med ".$med;
				  // echo " med1 ".$med1."";
	}
	
	if (isset ($chkpaciente))
	{
	     foreach ($chkpaciente as $paciente)
				{

					$sql3 = "SELECT Cod_equ, Fecha, Hi, Hf, Cedula
					         FROM {$solucionCitas}_000009
							 WHERE id = ".$paciente." ";
							 
					$res3 = mysql_query( $sql3, $conex ) or die( mysql_errno()." - Error en el query $sql3 - ".mysql_error() );
					for( $j = 0; $rows1 = mysql_fetch_array( $res3 ); $j++ )
					{
					   $medAnt=$rows1['Cod_equ'];
					   $fechaCita=$rows1['Fecha'];
					   $horaCita=$rows1['Hi'];
					   $horaFinCita=$rows1['Hf'];
					   $cedula=$rows1['Cedula'];
					   
					}
					
					actualizar($paciente, $med, $solucionCitas, $conex, $fechaCita, $horaCita, $horaFinCita, $medAnt, $cedula, $med1, $filtro, $key, $wfecini, $wfecfin); 
								
	            }             
	}
	
	
		if ($mensaje==2)
		{
				?><script>
			        var men="Medico actualizado con exito";
			        mensaje(men);
			    </script><?php
				
		}
		if ($mensaje==3)
		{
				?><script>
				   var men="El medico no atiende en la fecha:<?php echo $fechaCita; ?>";
				   mensaje(men);</script><?php
				   
		}
		if ($mensaje==4)
		{
		?><script>
				   var men="La hora(s) <?php //echo $horaCita; ?> de la cita(s) excede el tiempo de atencion del medico";
				   mensaje(men);</script><?php
		}	
        if ($mensaje==5)
		{
			?><script>
				   var men="El medico no tiene citas disponibles, Horario lleno";
				   mensaje(men);</script><?php		
		}
	
	
	//echo "<form name='pantalla' method=post>";
	echo "<br><br>";
	
	$sql = "SELECT
				Mednom, Medcod, Medcid
			FROM
				{$wbasedato}_000051
			WHERE
				Medcid != ''
				AND Medest = 'on'
			ORDER BY Mednom";
			
		
				
	$res = mysql_query( $sql,$conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	
	/************************Filtro por doctor asignado*************************/
	
	echo "<table align=center border=0 with='100'>"; //exterior
	echo "<tr><td valign='top'>";
	
	echo "<table align=left border=0> ";  //tabla externa
	echo "<tr>";
	echo "<td valign='top'>";
	
	echo "<table align=center>";
	echo "<tr>";
	echo "<td class='encabezadotabla' align=center valign='top'>Seleccione Medico Asignado</td>";
	echo "</tr>";
	echo "<tr>";
	$ban=0;
	echo "<input type='hidden' name='ban' value='".$ban."' id='ban'>";
	// $ban;
	echo "<td class='fila1'><select name='slDoctor' id='slDoctor' onchange='javascript: confirmar();'>";
	echo "<option>Seleccione...</option>";
	
	for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
	{
		if( $slDoctor != "{$rows['Medcod']} - {$rows['Mednom']}" )
		{
			echo "<option>{$rows['Medcod']} - {$rows['Mednom']}</option>";
		}
		else
		{
			echo "<option selected>{$rows['Medcod']} - {$rows['Mednom']}</option>";
		}
	}
	
	echo "</select></td>";
	echo "</tr>";
	echo "</table>";
	
	
	
    echo "</td>";  //tabla externa
    echo "</tr>";
    echo "<tr>";
    echo "<td>";	
	
   if ( isset($slDoctor))
	{
	$slDoctor1 = explode(" - ",$slDoctor);
	$slDoctor = $slDoctor1[0];
	@$nomDoctor = $slDoctor1[1];
	
	echo "<table align='center'><th class='fila1' align='center'>".$nomDoctor."</th></table>";
	}
					
	/*****************lista de pacientes con cita********************/
	
	//Buscando los pacientes que tienen cita
	//y no van para interconsulta
	$sql2 = "SELECT
				fecha, 
				cod_equ, 
				TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as hi, 
				hf, 
				nom_pac, 
				mednom, 
				b.id
			FROM
				{$wbasedato}_000051 a, 
				{$solucionCitas}_000009 b
			WHERE
				medcid = cod_equ
				AND medcod like '$filtro'
				AND fecha BETWEEN '".@$wfecini."' AND '".@$wfecfin."'
				AND asistida != 'on'
			ORDER BY fecha, hi, mednom, nom_pac
			";
  //falta agregar atendido
	//AND fecha = '".date("Y-m-d")."'
	$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql2 - ".mysql_error() );
	$num = mysql_num_rows( $res2 );
	
	echo "<br><br>";
	echo "<table align='left'>";
	
	if( $num > 0 ){
		
		for( $i = 0; $rows1 = mysql_fetch_array( $res2 ); $i++ ){
			
			//Definiendo la clase por cada fila
			if( $i%2 == 0 ){
				$class = "class='fila1'";
			}
			else{
				$class = "class='fila2'";
			}
			
			if( $i == 0 ){
				echo "<tr class='encabezadotabla'  align=center>";
				echo "<td style='width:90'><input type='checkbox' name='chktodos' onclick='javascript:estado()'></td>";
				echo "<td style='width:90'>Fecha</td>";
				echo "<td style='width:50'>Hora</td>";
				echo "<td>Nombre del Paciente</td>";
				echo "</tr>";
			}
			
			//primera columna con checkbox para los pacientes a los que se les va a cambiar el medico.
			echo "<tr $class>";
			echo "<td align=center><input type='checkbox' name='chkpaciente[]' value='{$rows1['id']}'></td>";
			echo "<td align=center>{$rows1['fecha']}</td>";
			echo "<td align=center>{$rows1['hi']}</td>";
			echo "<td>{$rows1['nom_pac']}</td>";
			//echo "<td>{$rows1['id']}</td>";
			echo "</tr>";
		
		    			
		} //for
			   
		
	}
	else{
		echo "<center>NO HAY CITAS ASIGNADAS PARA HOY</center>";
	}
	
	echo "</table>";
	
	echo "</td>";
	echo "</tr>";
	echo "</table>";  //externa
	
	echo "</td>";  //exterior
	echo "<td valign='top'>";  //exterior  BOTON
	
	//tabla interna de botones
	echo "<table align=center border=0>";
	echo "<tr>";
	$ban=1;
	echo "<input type='hidden' name='ban' value='".$ban."' id='ban'>";
	//echo $ban;
	echo "<td><center><input type='button' value='Cambiar' style='width:100' onclick='javascript: evaluar();'></td>";
	echo "</tr>";
	echo "</table>";
	
	echo "</td>";    //exterior
	echo "<td valign='top'>";  //exterior
	
	
	/************************Filtro por doctor nuevo*************************/
	
	echo "<table align=left border=0> ";  //tabla externa
	echo "<tr>";
	echo "<td valign='top'>";
	
	echo "<table align=center>";
	echo "<tr>";
	echo "<td class='encabezadotabla' align=center valign='top'>Seleccione Nuevo Medico</td>";
	echo "</tr>";
	echo "<tr>";
	$ban=0;
	echo "<input type='hidden' name='ban' value='".$ban."' id='ban'>";
	//echo $ban;
	echo "<td class='fila1'><select name='slDoctorNuevo' id='slDoctorNuevo' onchange='javascript: confirmar();'>";
	echo "<option>Seleccione...</option>";
	
	//mysql_data_seek($res1,0);  //para volver el puntero del array a la posicion 0, para que salgan todos los medicos.
	
	$sql1 = "SELECT
				Mednom, Medcod, Medcid
			FROM
				{$wbasedato}_000051
			WHERE
				Medcid != ''
				AND Medest = 'on'
				AND Medcid != 'NO APLICA'
				AND Medcid != ' '
			ORDER BY Mednom";
			
				
	$res1 = mysql_query( $sql1,$conex ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );
	
	for( $i = 0; $rows = mysql_fetch_array( $res1 ); $i++ )
	{
		if( $slDoctorNuevo != "{$rows['Medcod']} - {$rows['Mednom']} - {$rows['Medcid']}" )
		{
			echo "<option value='{$rows['Medcod']} - {$rows['Mednom']} - {$rows['Medcid']}'>{$rows['Medcod']} - {$rows['Mednom']}</option>";
		}
		else
		{
			echo "<option selected value='{$rows['Medcod']} - {$rows['Mednom']} - {$rows['Medcid']}'>{$rows['Medcod']} - {$rows['Mednom']}</option>";
		}
	}
	
	echo "</select></td>";
	echo "</tr>";
	echo "</table>";
	
	echo "</td>";  //tabla externa
    echo "</tr>";
    echo "<tr>";
    echo "<td>";
	
	if ( isset($slDoctorNuevo) && $slDoctorNuevo != "Seleccione...")
	// if ( isset($slDoctorNuevo) )
	{
	$slDoctorNuevo1 = explode(" - ",$slDoctorNuevo);
	$slDoctorNuevo = $slDoctorNuevo1[0];
	$nomDoctorNuevo = $slDoctorNuevo1[1];
	@$filtro=$slDoctorNuevo;
	
	//echo "filtro ".$filtro;
	echo "<table align='center'><th class='fila1' align='center'>".$nomDoctorNuevo."</th></table>";
	}
	else{
	$filtro = '';
	}
	
	/*****************lista de pacientes con cita********************/
	
	//Buscando los pacientes que tienen cita
	//y no van para interconsulta
	$sql2 = "SELECT
				fecha, 
				cod_equ, 
				TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as hi, 
				hf, 
				nom_pac, 
				mednom, 
				b.id
			FROM
				{$wbasedato}_000051 a, 
				{$solucionCitas}_000009 b
			WHERE
				medcid = cod_equ
				AND medcod like '$filtro'
				AND fecha BETWEEN '".@$wfecini."' AND '".@$wfecfin."'
				AND asistida != 'on'
			ORDER BY fecha, hi, mednom, nom_pac
			";

	
	$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql2 - ".mysql_error() );
	$num = mysql_num_rows( $res2 );
	
	echo "<br><br>";
	echo "<table align='left'>";
	
	if( $num > 0 ){
		
		for( $i = 0; $rows = mysql_fetch_array( $res2 ); $i++ ){
			
			//Definiendo la clase por cada fila
			if( $i%2 == 0 ){
				$class = "class='fila1'";
			}
			else{
				$class = "class='fila2'";
			}
			
			if( $i == 0 ){
				echo "<tr class='encabezadotabla'  align=center>";
				echo "<td style='width:90'>Fecha</td>";
				echo "<td style='width:50'>Hora</td>";
				echo "<td>Nombre del Paciente</td>";
				echo "</tr>";
			}
			
			$aux=0;
			if (isset ($chkpaciente))
			{
				foreach ($chkpaciente as $paciente)
				{
					//echo $paciente." ";
					//if ($rows['id'] == $paciente && $mensaje==2)
					if ($rows['id'] == $paciente)
					{
						$aux=1;
						break;
					}					
				}
			}
			// echo "<br>rows ".$rows['id'];
			 //echo "<br>aux ".$aux;
			//se busca el paciente cambiado y se coloca el fondo de otro color
			if ($aux==1)
			{
				//lista de pacientes
				echo "<tr bgcolor='#66ffcc'>";
				echo "<td align=center>{$rows['fecha']}</td>";
				echo "<td align=center>{$rows['hi']}</td>";
				echo "<td>{$rows['nom_pac']}</td>";
				//echo "<td>{$rows['id']}</td>";
				echo "</tr>";
			}
			else
			{
				
				//lista de pacientes
				echo "<tr $class>";
				echo "<td align=center>{$rows['fecha']}</td>";
				echo "<td align=center>{$rows['hi']}</td>";
				echo "<td>{$rows['nom_pac']}</td>";
				//echo "<td>{$rows['id']}</td>";
				echo "</tr>";
			}
			
				    			
		} //for
			   
		
	}
	
	else{
		echo "<center>NO HAY CITAS ASIGNADAS PARA HOY</center>";
	}
	
	echo "</table>";
	
	echo "</td>";
	echo "</tr>";
	echo "</table>";  //externa
	
	echo "</td>"; //externa
	echo "</table>"; //externa
	
	echo "<div align='center'><br /><br /><input type='button' value='Cerrar' style='width:100' onclick='javascript: cerrarVentana();'></div>";
	echo "</form>";
	echo "</body>";
	echo "</html>";
} //usuario registrado

?>