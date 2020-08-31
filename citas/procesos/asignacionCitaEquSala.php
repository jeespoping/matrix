<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");
?>
<?php
//echo $tipoa.'----';
if (isset($accion) and $accion == 'validar')  
 {  
	

	

	
	$datacitas = array('mensaje'=>'', 'notificacion'=>0);
	$nom_pac="";
	
	$sql = "select Fecha, Cedula, Nom_pac, TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as Hi, Cod_equ
			from ".$empresa."_000001
			where Cedula = '".utf8_decode($cedula)."'
			and Fecha >= '".$fecha."'
			and Activo ='A'
			and Cedula != '' ";   

						 
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows($res);
	if ($num >0)
	{
		$row=mysql_fetch_array($res);
		$fecha=$row['Fecha'];
		$cedula=$row['Cedula'];
		$nom_pac=$row['Nom_pac'];
		$hi=$row['Hi'];
		$cod_equ=$row['Cod_equ'];
		
		$sql1 = "select Descripcion
			from ".$empresa."_000003
			where Codigo ='".$cod_equ."'";   
						 
	$res1 = mysql_query( $sql1, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num1 = mysql_num_rows($res1);
		if ($num1 >0)
		{
			$row1=mysql_fetch_array($res1);
			$equipo=$row1['Descripcion'];
		}	
		$datacitas['notificacion'] = 1;
	}
	
	$datacitas['mensaje']="El Usuario ".utf8_encode($nom_pac)." con codigo ".utf8_encode($cedula)." tiene asignada un horario para la ".$fecha." a las ".$hi." en la Sala ".utf8_encode($equipo)."";
	
	echo json_encode($datacitas);
	return ;
}

$Codmed = '00001';  //se queman estos datos ya que no lo necesitan en este tipo de agenda sala
$Nitres = '1710';   //se queman estos datos ya que no lo necesitan en este tipo de agenda sala


//para llenar el select de examenes
if (isset($accion) and $accion == 'buscar')
 { //$fp = fopen('verquery.txt',"w+");
	// fwrite($fp, $est);
	// fclose($fp); 
	

	

	
	$val=dibSelect($buscar, $Codexa, $cod_examen,$empresa);
	// echo $val;
	return ;
}

function dibSelect($buscar, $Codexa, $cod_examen, $empresa )
{
	global $conex;
	
	if(isset($buscar) and $buscar != "")
		{
			$query = "select codigo,descripcion,activo from ".$empresa."_000006 where descripcion like '%".$buscar."%'  order by codigo";
		}
		else
		{
			if(isset($Codexa)) 
			{
				 $query = "select codigo,descripcion,activo from ".$empresa."_000006  where codigo='".substr($Codexa,0,strpos($Codexa,"-"))."' ";
			}
			else
			{
				 $query = "select codigo,descripcion,activo from ".$empresa."_000006  where codigo='".$cod_examen."' ";
			}
		}  
		$err1 = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
		$num1 = mysql_num_rows($err1);

		$opciones='';
		for ($i=0;$i<$num1;$i++)
		{	
			$row1 = mysql_fetch_array($err1);
			if((isset($Codexa) and substr($Codexa,0,strpos($Codexa,"-")) == $row1[0]) or ($pos3 != "0" and $pos3 == $row1[0]))
				echo $opciones= "<option selected>".utf8_encode($row1[0])."-".utf8_encode($row1[1])."</option>";
			else
				echo $opciones= "<option>".utf8_encode($row1[0])."-".utf8_encode($row1[1])."</option>";
		}
		
	return;
}


?>
<html>
<html>
<head>
<title>MATRIX</title>
<script src="../../../include/root/jquery-1.3.2.min.js" type="text/javascript"></script>
<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet">

</head>
<body BGCOLOR='' TEXT='#000066'>

<script type="text/javascript">

	function enter()
	{
		document.forms.Citas.submit();
	}

	function enviar(evt)
	{
	   var charCode = (evt.which) ? evt.which : event.keyCode

	   if(charCode == 13){ document.forms.Citas.submit();}
	   else
		   {return true}

	}
	
	function validarEnter(event,criterio)
	{
		var charCode = (event.which) ? event.which : event.keyCode
		
		if(charCode==13) { $("#"+criterio).focus(); }
		else { return true; }
		return false;
	}
	
	function llenarSelect(origen,destino)
	{
		
		var valorOrigen = $('#'+origen).val();
		
		// if( valorOrigen.length < 3 ){
			// alert( "Debe ingresar al menos tres caracteres");
			// return;
		// }
		
		//$.blockUI({ message: $('#msjEspere') });
		$.post("asignacionCitaEquSala.php",
				{
					empresa:      $('#empresa').val(),               
					consultaAjax:   '',
					buscar:          valorOrigen,
					accion:         'buscar',
				    cod_examen:     $('#cod_examen').val(),
					Codexa:         $('#Codexa').val(),
				    pos3:           $('#pos3').val()	
				}			
				,function(data) {
					//$.unblockUI();
					
					$('#'+destino).html(data);
					
			});
	}
	
	function validacion()
	{
		verificarCitas();
		
	}
	
	function verificarCitas()
	{
		var cedula = $("#Cedula").val();
		//verificarCitas(cedula);
		
		$.post("asignacionCitaEquSala.php",
				{
					empresa:      $('#empresa').val(),               
					consultaAjax: '',
					cedula:       cedula,
					accion:       'validar',
					fecha:		  $('#wfec').val()
				}			
				,function(data_json) {
							
					if (data_json.notificacion >0)
					{				
						alert(data_json.mensaje);
						// $datacitas['fecha']=$row['Fecha'];
						// $datacitas['cedula']=$row['Cedula'];
						// $datacitas['nom_pac']=$row['Nom_pac'];
						// $datacitas['hi']=$row['Hi'];
						// $datacitas['cod_equ']=$row['Cod_equ'];
					}
					
					enter();
					
					return true;
				},
				"json"
			
		);
		
		return false;
	}
	
	
	
	//esta funcion es del script 8
	window.onload = function(){
	
	
		//para el calendario en la fecha que solicto inicialmente la cita
		$("#FecSol").datepicker({
			dateFormat:"yy-mm-dd",
			fontFamily: "verdana",
			dayNames: [ "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado", "Domingo" ],
			monthNames: [ "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre" ],
			dayNamesMin: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
			dayNamesShort: [ "Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa" ],
			monthNamesShort: [ "Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic" ],
			changeMonth: true,
			changeYear: true,
			yearRange: "c-100:c+100"
		});
	
    
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
		
		$( "#ui-datepicker-div").css("display", "none");
		
		var mqEstado = document.getElementsByTagName( "marquee" );
		
		if( mqEstado && mqEstado[0].innerHTML == "LOS DATOS ESTAN OK!!!!" && document.getElementById("ret").value=="on"){
			window.location.href=document.getElementById("retornar").href;
		}
		else{
			document.getElementById("ret").value=="off";
		}
		
			
	}
	
	
	function retornar()
	{
		document.getElementById("ret").value="on";
		document.Citas.submit();
	}
	
</script>
<?php

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
				AND Activo='A'
				";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$coment = $rows['comentarios']; 
	}
	
	return $coment;
}

/**********************************************************************************************************************  
	   PROGRAMA : 000001_prx10.php
	   Fecha de Liberación :  NO REGISTRADA
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual :  2007-04-03
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gráfica que permite grabar citas x equipos x medico.
	   
	   
	   REGISTRO DE MODIFICACIONES :
	   
	   Variables wsw
	   			X##    X-Control de equipo en la tabla Nro. 7                                  0-Sin control     1-Con control
	            #Y#    Y-Control de seleccion de cita multiple                                 0-Sin control     1-Con control
	            ##Z    Z-Control de seleccion de Examenes tabla Nro. 6                         0-Sin control     1-Con control
				
	   2013-09-03:  Se modifica el script para que calcule tambien el indice de oportunidad desde la fecha actual hasta la fecha de la cita. Viviana Rodas
       2013-08-29:  Se modifica el programa para que calcule el indice de oportunidad entre las dos fechas ingresadas, la fecha solicitada y la fecha en la que se le dio la cita. Viviana Rodas
	   2013-02-28:  Se agrego en la consulta de la accion validar que verifique si el paciente ya tiene citas solo si el estado de la cia es activo y la cedula
					sea diferente de vacio, ya que si la cita se habia cancelado igual salia el mensaje de que ya tenia una cita. Viviana Rodas
	   2013-01-17:  Se cambio la consulta para que solo liste las entidades responsables que esten activas. Viviana Rodas
	   2012-12-24:  Se agrega validacion en el campo tipoa para que cuando se edite una cita traiga el tipoA que ya esta en la base de datos. Viviana Rodas
	   2012-12-21:  Se organiza la parte del los comentarios que cuando se ingresaba al campo de la cedula para editar la cita se borraba el comentario.
	   2012-12-18:  Se agrega la fecha de la cita y el equipo de la fecha en el mensaje que se saca cuando la persona a la que se le esta asignando la cita, ya tenia cita. Viviana Rodas
	   2012-12-17:  Se agrega la variable tipoAtencion para que cuando sea cardiologia se muestre para seleccionar el tipo de ingreso Interno o ambulatorio Viviana Rodas
	   2012-11-28:  Se agrega una consulta para que cuando ingresen la cedula de un paciente verifique si tiene tiene citas asignadas de la fecha en adelante. Viviana Rodas
	   2012-10-25:  Se agregan consultas para validar que solo se puedan asignar citas cuando el horario del medico y del equipo sean iguales. Viviana Rodas
	   2012-10-19:  Se unifico el este script con el 000001_prx08 ya que tenian funcionamiento similar, se quito el campo de activo e inactivo porque para cancelar la cita se hace desde la agenda directamente, se modifico la forma de hacer el criterio de busqueda de los examenes para que este no hiciera submit, solo se hace submit cuando se ingresa la cedula, tambien se modifico la parte donde se graba la cita para que redireccione a la agenda de citas sin darle en retonar siempre y cuando la informacion sea este ok, ademas se modifico para que solo muestre los medicos que atienden ese dia, tambien se guarda en la base de datos el indice de oportunidad y el tipo de cita en el momento donde se graba, ademas cuando se graba el campo asiste se guarda null para que cuando en la agenda se chequee asiste se le cambie el estado a on u off dependiendo. Viviana Rodas
	   
	   .2006-03-23
	   		Se modifico en el programa para agregar a la variable wsw una tercera posicion para definir si selecciona examenes o no.
	   
***********************************************************************************************************************/
echo "<center>";
echo "<table border=0 align=center>";
echo "<tr><td align=center bgcolor='#cccccc'><A NAME='Arriba'><font size=5><b>Registro de Informacion SALAS</b></font></a></tr></td>";
echo "<tr><td align=center bgcolor='#cccccc'><font size=2> <b> asignacionCitaEquSala.php Ver. 2013-02-28</b></font></tr></td></table>";
echo "</center>";
echo "<form  name='Citas' action='asignacionCitaEquSala.php' method=post>";
session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{		
	$key = substr($user,2,strlen($user));
	

	

	
	$Cedula = $key;
	$queryusu = "select descripcion from usuarios where codigo ='".$key."'";
	$errusu = mysql_query($queryusu,$conex) or die(mysql_errno().": Error en el queryusu: $query ".mysql_error());
	$numusu = mysql_num_rows($errusu);
	$rowusu = mysql_fetch_array($errusu);		
	$Nompac = $rowusu[0];
	
	echo "<input type='HIDDEN' name= 'empresa'  id= 'empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'wsw' id= 'wsw' value='".$wsw."'>";
	echo "<input type='HIDDEN' name= 'caso'  id= 'caso' value='".$caso."'>";
	echo "<input type='HIDDEN' name= 'colorDiaAnt' id= 'colorDiaAnt' value='".$colorDiaAnt."'>";
	echo "<input type='HIDDEN' name= 'wemp_pmla' id= 'wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='HIDDEN' name= 'nomdia' id= 'nomdia' value='".@$nomdia."'>";
	 
	// echo "<div id='msjEspere' style='display:none;'>";
	// echo '<br>';
	// echo "<img src='../../images/medical/ajax-loader5.gif'/>";
	// echo "<br><br> Por favor espere un momento ... <br><br>";
	// echo '</div>';
	//echo "colordiant: ".$colorDiaAnt;
	$fechaAct=date("Y-m-d");
	if ($caso ==1 and $empresa=='citasca')
	{
		$tipoAtencion='on';
	}
	else
	{
		$tipoAtencion='off';
	}
	
	if( !isset( $ret ) ){
		$ret = 'off';
	}
	
	// if(!isset($tipoa))
	// {
		// $tipoa="Ambulatorio";
	// }
	
	if( $caso == 3)
	{
		if (((isset($wpar) and substr($wsw,2,1) == "0") or (isset($wpar)  and substr($wsw,2,1) == "1")) and !isset($ok))
		{
			$a = true;
		}
		else
		{
			$a = false;
		}
	}
	else
	{
		if ((isset($wpar) and substr($wsw,2,1) == "0") or (isset($wpar) and isset($ok) and substr($wsw,2,1) == "1"))
		{
			$a = true;
		}
		else
		{
			$a = false;
		}
	}

	if ($a)
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
				$err1 = mysql_query($query,$conex)or die(mysql_errno().": Error en el query: $query ".mysql_error());
				$num1 = mysql_num_rows($err1);
				$row1 = mysql_fetch_array($err1);
				//$Coment=$Coment.$row1[0];
				$Coment=$Coment; //cambiado
			}
			if ($wpar == 2)
			{
				// Verificacion de Disponibilidad de Espacio
				$query = "select cod_med,cod_equ,cod_exa,fecha,hi,hf,nom_pac,nit_resp,telefono,edad,comentarios,usuario,activo from ".$empresa."_000001 where cod_equ = '".substr($Codequ,0,3)."'";
				$query = $query." and fecha = '".$wfec."'";
				$query = $query." and hi = '".$Hi."'";
				$query = $query." and hf = '".$Hf."'";
				$query = $query." and activo = 'A'";	//AGREGADO
				$err = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
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
                    $query = $query." and activo = 'A'";	//AGREGADO					
					$err = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
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
					$query = $query." and activo = 'A'";	//AGREGADO
					$err = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
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
			$err = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
			$num = mysql_num_rows($err); 
			if ($num > 0 )
			{
				$query = "select medico_k,examen,activo from ".$empresa."_000005 where examen = '".substr($Codexa,0,strpos($Codexa,"-"))."' and medico_k = '".substr($Codmed,0,5)."'";
				$query = $query." and activo = 'A'";
				$err = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
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
				$err = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
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
				$err = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
				$num = mysql_num_rows($err); 
				if ($num > 0 ) 
					$tiperr = 2;
				else
					if($tiperr == 0)
						$tiperr = 0;
						
			}
			
			
					
			//Verificacion x Equipo si las citas son Multiples
			if(substr($wsw,1,1) == "1")
			{   //agregado para saber el id de la cita que va a preguntar
				$query = "SELECT  id FROM ".$empresa."_000001 WHERE  cod_equ='".substr($Codequ,0,3)."' and fecha='".$wfec."' and hi='".$Hi."' and activo = 'A' ";
				$err = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
				
				if( $rowId = mysql_fetch_array( $err ) ){
			
					// $query = "update ".$empresa."_000001 set activo='I' where  cod_equ='".substr($Codequ,0,3)."' and fecha='".$wfec."' and hi='".$Hi."' ";
					$query = "update ".$empresa."_000001 set activo='I' where  id={$rowId['id']}";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					
					$query = "select hi,hf from ".$empresa."_000001 where cod_equ = '".substr($Codequ,0,3)."'";
					$query = $query." and fecha = '".$wfec."'";
					$query = $query." and ((hi <= '".$Hi."' and hf <= '".$Hf."' and hf > '".$Hi."')";
					$query = $query."  or  (hi >= '".$Hi."' and hf >= '".$Hf."' and hi < '".$Hf."')";
					$query = $query."  or  (hi <= '".$Hi."' and hf >= '".$Hf."')";
					$query = $query."  or  (hi >= '".$Hi."' and hf <= '".$Hf."'))";
					$query = $query." and activo = 'A'";
					$err = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
					$num = mysql_num_rows($err); 
					if ($num > 0 ) 
						$tiperr = 7;
					else
						if($tiperr == 0)
							$tiperr = 0;
					//*********************************************		
					//HACER MAS PRUEBAS
						// $query = "update ".$empresa."_000001 set activo='A' where  cod_equ='".substr($Codequ,0,3)."' and fecha='".$wfec."' and hi='".$Hi."' ";
						 $query = "update ".$empresa."_000001 set activo='A' where  id={$rowId['id']}";
						$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				}
				else{
					if($tiperr == 0)
							$tiperr = 0;
				}
				
			}
			// Verificacion de los datos de texto
			if (strlen($Nompac)==0 or strlen($Tel)==0 or $Edad==0 or strlen($Codexa)<=1 or strlen($Nitres)<=1)
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
		$tiperr = 0;
		if ($tiperr == 0)
		{
			$posicion = strpos($Nitres,'-');
			$Nitres = substr($Nitres,$posicion+1,strlen($Nitres));
			switch ($wpar)
			{				
				case 1:
				$Nompac = strtoupper($Nompac);
				$fecha = date("Y-m-d");
				if( trim($Coment) != '' ){ 
//					$Coment2 = $comentarios."\n\n".$Coment;
					comentariosAnteriores( $pos2, $pos4, $pos5 );
					echo $Coment = trim( comentariosAnteriores( $pos2, $pos4, $pos5 )."\n\n".$Coment );   //cambio 2012-12-21
				}
				else{
					$Coment = comentariosAnteriores( $pos2, $pos4, $pos5 );    //cambio 2012-12-21
				}
				
				// FecSol fecha para la cual se solicito la cita
				//calculo de indice de oportunidad en dias desde la fecha solicitada
					if ($wfec > $FecSol)
					{
						//se calcula el tipo de cita dependiendo del color del dia anterior
						if ($colorDiaAnt!="rojo")
						{
							$tipoCita="Solicitada";
							$diasOpor=0;
						}
						else
						{
							$tipoCita="Asignada";
							$wfec1=strtotime($wfec); //fecha de la cita en unix
							//$wfec1=($wfec1+(24*3600)); //se le suma 1 dia
							$fecha1=strtotime($FecSol); //fecha para la cual fue solicitada la cita
							$resta=$wfec1-$fecha1;
							$diasOpor=($resta/(24*3600));
						}
					}
					else if ($wfec == $FecSol)
					{	
						$diasOpor=0;
						$tipoCita="Asignada"; //si la cita es el dia actual la cita fue asignada
					}
					/****/
					//calculo de indice de oportunidad en dias desde el dia actual
					if ($wfec > $fecha)
					{
						//se calcula el tipo de cita dependiendo del color del dia anterior
						if ($colorDiaAnt!="rojo")
						{
							// $tipoCita="Solicitada";
							$diasOporAct=0;
						}
						else
						{
							// $tipoCita="Asignada";
							$wfec1=strtotime($wfec); //fecha de la cita en unix
							//$wfec1=($wfec1+(24*3600)); //se le suma 1 dia
							$fecha1=strtotime($fecha); //fecha para la cual fue solicitada la cita
							$resta=$wfec1-$fecha1;
							$diasOporAct=($resta/(24*3600));
						}
					}
					else if ($wfec == $fecha)
					{	
						$diasOporAct=0;
						// $tipoCita="Asignada"; //si la cita es el dia actual la cita fue asignada
					}
					/****/
				
				if(isset($Asistida))
				{
					$Asistida="on";
				}
				else
				{	
					$Asistida="off";
				}
					
						  $query = "update ".$empresa."_000001 set cod_exa='".substr($Codexa,0,strpos($Codexa,"-"))."', hf= '".$Hf."', Cedula='".$Cedula."', cod_med= '".substr($Codmed,0,5)."', nom_pac='".ucwords($Nompac)."', nit_resp='".$Nitres."', telefono='".$Tel."', edad=".$Edad.", comentarios='".$Coment."', Asistida='".$Asistida."', activo='".substr($Estado,0,1)."', tipoA='".$tipoa."',tipoCita='".$tipoCita."',dias_opor='".$diasOpor."',fecSol='".$FecSol."',diaOporAct='".$diasOporAct."' where  cod_equ='".$Codequ."' and fecha='".$Fecha."' and hi='".$Hi."' and activo='A' ";
					
					
				$err = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
				if ($err = 1)
				{
					//echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>LOS DATOS ESTAN OK!!!!</MARQUEE></FONT>";
					//echo "<br><br>";
					$query = "select  codigo,descripcion,uni_hora,hi,hf,activo from ".$empresa."_000003 where codigo='".substr($Codequ,0,3)."'";
					$err = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
					$num = mysql_num_rows($err);
					$row = mysql_fetch_array($err);				
					$query = "select fecha,equipo,uni_hora,hi,hf from ".$empresa."_000004 where fecha = '".$Fecha."' and equipo = '".substr($Codequ,0,3)."'";
					$err = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());	
					$num = mysql_num_rows($err);
					if ($num == 0)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000004 (medico,fecha_data,hora_data,fecha,equipo,uni_hora,hi,hf,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$Fecha."','".substr($Codequ,0,3)."',".$row[2].",'".$row[3]."','".$row[4]."','C-".$empresa."')";
						$err = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());	
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
				$fecha = date("Y-m-d"); //fecha actual
				$hora = (string)date("H:i:s"); 
				// FecSol fecha para la cual se solicito la cita
				
				//calculo de indice de oportunidad en dias desde la fecha solicitada
					if ($wfec > $FecSol)
					{
						//se calcula el tipo de cita dependiendo del color del dia anterior
						if ($colorDiaAnt!="rojo")
						{
							$tipoCita="Solicitada";
							$diasOpor=0;
						}
						else
						{
							$tipoCita="Asignada";
							$wfec1=strtotime($wfec); //fecha de la cita en unix
							//$wfec1=($wfec1+(24*3600)); //se le suma 1 dia
							$fecha1=strtotime($FecSol); //fecha para la cual se solicito la cita
							$resta=$wfec1-$fecha1;
							$diasOpor=($resta/(24*3600));
							
						}
					}
					else if ($wfec == $FecSol)
					{	
						$diasOpor=0;
						$tipoCita="Asignada"; //si la cita es el dia actual la cita fue asignada
					}
					
					/****/
					//calculo de indice de oportunidad en dias desde el dia actual
					if ($wfec > $fecha)
					{
						//se calcula el tipo de cita dependiendo del color del dia anterior
						if ($colorDiaAnt!="rojo")
						{
							// $tipoCita="Solicitada";
							$diasOporAct=0;
						}
						else
						{
							// $tipoCita="Asignada";
							$wfec1=strtotime($wfec); //fecha de la cita en unix
							//$wfec1=($wfec1+(24*3600)); //se le suma 1 dia
							$fecha1=strtotime($fecha); //fecha para la cual fue solicitada la cita
							$resta=$wfec1-$fecha1;
							$diasOporAct=($resta/(24*3600));
						}
					}
					else if ($wfec == $fecha)
					{	
						$diasOporAct=0;
						// $tipoCita="Asignada"; //si la cita es el dia actual la cita fue asignada
					}
					/****/
				
				
				if(isset($Asistida))
				{
					$Asistida="on";
				}
				else
				{
					$Asistida="off";
				}
					//en la consulta se cambio la fecha de la cita($fecha) por $wfec que es la fecha que trae desde el calendario 
					/* $query = "insert ".$empresa."_000001 (medico,fecha_data,hora_data,cod_med,cod_equ,cod_exa,fecha,hi,hf,Cedula,nom_pac,nit_resp,telefono,edad,comentarios,Asistida,usuario,activo, tipoA ,tipoCita, dias_opor, fecSol, diaOporAct,seguridad) 
							   values ('".$empresa."','".$fecha."','".$hora."','".substr($Codmed,0,5)."','".substr($Codequ,0,3)."','".substr($Codexa,0,strpos($Codexa,"-"))."','".$wfec."','".$Hi."','".$Hf."','".$Cedula."','".ucwords($Nompac)."','".$Nitres."','".$Tel."',".$Edad.",'".$Coment."','".$Asistida."','".substr($user,2,strlen($user))."','".substr($Estado,0,1)."','".$tipoa."','".$tipoCita."','".$diasOpor."','".$FecSol."','".$diasOporAct."','C-".$empresa."')";*/
					
					$Codmed = '00001';  //se queman estos datos ya que no lo necesitan en este tipo de agenda sala
					$Nitres = '1710';   //se queman estos datos ya que no lo necesitan en este tipo de agenda sala
					$Cedula = $key;
					$queryusu = "select descripcion from usuarios where codigo ='".$key."'";
					$errusu = mysql_query($queryusu,$conex) or die(mysql_errno().": Error en el queryusu: $query ".mysql_error());
					$numusu = mysql_num_rows($errusu);
					$rowusu = mysql_fetch_array($errusu);		
					$Nompac = $rowusu[0];
					
					$query = "insert ".$empresa."_000001 (medico,fecha_data,hora_data,cod_med,cod_equ,cod_exa,fecha,hi,hf,Cedula,nom_pac,nit_resp,telefono,edad,comentarios,Asistida,usuario,activo, tipoA ,tipoCita, dias_opor, fecSol, diaOporAct,seguridad) 
							   values ('".$empresa."','".$fecha."','".$hora."','".$Codmed."','".substr($Codequ,0,3)."','".substr($Codexa,0,strpos($Codexa,"-"))."','".$wfec."','".$Hi."','".$Hf."','".$Cedula."','".ucwords($Nompac)."','".$Nitres."','".$Tel."',".$Edad.",'".$Coment."','".$Asistida."','".substr($user,2,strlen($user))."','".substr($Estado,0,1)."','".$tipoa."','".$tipoCita."','".$diasOpor."','".$FecSol."','".$diasOporAct."','C-".$empresa."')";		   
				//$err= 1;
				$err = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
				if ($err != 1)
				{
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR EN LA INSERCION DEL TURNO -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
					echo "<br><br>";
				}
				else
				{
					$query = "select codigo,descripcion,uni_hora,hi,hf,activo from ".$empresa."_000003 where codigo='".substr($Codequ,0,3)."'";
					$err = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
					$num = mysql_num_rows($err);
					$row = mysql_fetch_array($err);				
					$query = "select fecha,equipo,uni_hora,hi,hf from ".$empresa."_000004 where fecha = '".$Fecha."' and equipo = '".substr($Codequ,0,3)."'";
					$err = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());	
					$num = mysql_num_rows($err);
					if ($num == 0)
					{
						$fecha = date("Y-m-d");
						$hora = (string)date("H:i:s");
						$query = "insert ".$empresa."_000004 (medico,fecha_data,hora_data,fecha,equipo,uni_hora,hi,hf,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$Fecha."','".substr($Codequ,0,3)."',".$row[2].",'".$row[3]."','".$row[4]."','C-".$empresa."')";
						$err = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());	
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
				$err = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
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
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#00ffff LOOP=-1>EL ADMINISTRADOR NO ESTA DISPONIBLE EN ESE HORARIO -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
				break;
				case 2:
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33cc33 LOOP=-1>EL ADMINISTRADOR YA TIENE UNA CITA ASIGNADA INCOMPATIBLE CON LA QUE USTED DESEA -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
				break;
				case 3:
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CCCCFF LOOP=-1>LOS DATOS ESTAN INCOMPLETOS -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
				break;
				case 4:
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>SOLICITUD ESPECIAL NO REALIZADO POR ESTE ADMINISTRADOR -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
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
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CCCCFF LOOP=-1>INCOMPATIBILIDAD DE TURNOS SALA OCUPADA -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
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
	echo "<input type='HIDDEN' name= 'pos3' id= 'pos3' value='".$pos3."'>";
	echo "<input type='HIDDEN' name= 'pos4' value='".$pos4."'>";
	echo "<input type='HIDDEN' name= 'pos5' value='".$pos5."'>";
	echo "<input type='HIDDEN' name= 'pos6' value='".$pos6."'>";
	echo "<input type='HIDDEN' name= 'pos7' value='".$pos7."'>";
	echo "<input type='HIDDEN' name= 'pos8' value='".$pos8."'>";
	echo "<input type='HIDDEN' name= 'pos9' value='".$pos9."'>";
	echo "<input type='HIDDEN' name= 'wtit' value='".$wtit."'>";
	echo "<input type='HIDDEN' name= 'Diasem' value='".$diasem."'>";

					//      0       1       2     3     4  5    6       7         8      9     10        11      12     13    14         15        16       17    18    19
		$query = "select cod_med,cod_equ,cod_exa,fecha,hi,hf,nom_pac,nit_resp,telefono,edad,comentarios,usuario,activo,Cedula,Asistida,tipoCita,dias_opor,tipoA,fecSol,diaOporAct from ".$empresa."_000001 where  cod_equ='".$pos2."' and fecha='".$pos4."' and hi='".$pos5."' and Activo='A'";
		
	$err = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
	$num = mysql_num_rows($err);
	echo "<table border=0 align=center>";
	echo "<li><A HREF='agendaEquiposSala.php?wequ=".$pos2."-".$wtit."&wtit=".$wtit."&wfec=".$pos4."&empresa=".$empresa."&amp;wsw=".$wsw."&amp;colorDiaAnt=".$colorDiaAnt."&caso=".$caso."&wemp_pmla=".$wemp_pmla."&nomdia=$nomdia' id='retornar'>Retornar</A><br>";
	//if ($num > 0)
		//echo "<li><A HREF='000001_prx4Sala.php?par1=".$pos2."&par2=".$pos4."&par3=".$pos5."&empresa=".$empresa."' target = '_blank'>Imprimir</A><br>";
	if ($num > 0)
	{
		$Medant=$pos1;
		$row = mysql_fetch_array($err);
		// $cod_examen=$row['cod_exa'];
		// echo "<input type='HIDDEN' name='cod_examen' id='cod_examen' value='".$cod_examen."'>";
		
		/*if(!isset($wpar))
		{
			$query = "select descripcion,nit from ".$empresa."_000002 WHERE nit =  '".$row[7]."' order by descripcion ";
			$err1 = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
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
			$err1 = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				$row1 = mysql_fetch_array($err1);
				$row[7]=$row1[0]."-".$row1[1];
			}
		}*/
		$pos6=$row[5];
		echo "<input type='HIDDEN' name= 'wpar' id= 'wpar' value='1'>";
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
		
		if(!isset($Cedula))
		{
			$row[13]="";		//Cedula
			echo "<input type='HIDDEN' name= 'ok' value='1'>";
		}			
		else
		{
			$row[13]=$Cedula;
			if(isset($ok))
			{
				$row[13]=$Cedula;
				$query = "select Nom_pac, Nit_resp, Descripcion, Telefono, Edad, ".$empresa."_000001.id, Cedula from ".$empresa."_000001,".$empresa."_000002 where  Cedula='".$Cedula."' and Nit_resp=Nit and ".$empresa."_000001.activo= 'A' order by 6 desc ";
				$err2 = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
				$row2 = mysql_fetch_array($err2);
				$Nompac=$row2[0];
				$Nitres=$row2[1];
				$Tel=$row2[3];
				$Edad=$row2[4];
				
			}
		}
			
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
		
		
		if(!isset($Asistida))
			$row[14]="off";				//Asistida
		else
			$row[14]="on";
		if(!isset($FecSol))
			$row[18]=$fechaAct;				//fecha para la cual se solicito la cita, para que salga la fecha actual por defecto
		else
			$row[18]=$FecSol;
		echo "<input type='HIDDEN' name= 'wpar' value='2'>";
	}
	
	echo "<input type='HIDDEN' name= 'Codmed' id= 'Codmed' value='".$row[0]."'>";
	echo "<input type='HIDDEN' name= 'Medant' id= 'Medant' value='".$Medant."'>";
	echo "<input type='HIDDEN' name= 'Codequ' id= 'Codequ' value='".$row[1]."'>";
	echo "<input type='HIDDEN' name= 'codexa' id= 'codexamen' value='".$row[2]."'>";
	echo "<input type='HIDDEN' name= 'Fecha' id= 'Fecha' value='".$row[3]."'>";
	echo "<input type='HIDDEN' name= 'Hi' id= 'Hi' value='".$row[4]."'>";
	if(substr($wsw,1,1) == "0")
		echo "<input type='HIDDEN' name= 'Hf' id= 'Hf' value='".$row[5]."'>";
	echo "<input type='HIDDEN' name= 'wfec' id= 'wfec' value='".$pos4."'>";
	echo "<input type='HIDDEN' name= 'wequ' id= 'wequ' value='".$row[1]."'>";
	// echo "<input type='HIDDEN' name= 'Cedula' value='".$row[13]."'>";
	// echo "<input type='HIDDEN' name= 'Asistida' value='".$row[14]."'>";
	// echo "<input type='HIDDEN' name= 'tipoCita' value='".$row[15]."'>";
	// echo "<input type='HIDDEN' name= 'diasOpor' value='".$row[16]."'>";
	//echo "<input type='HIDDEN' name= 'wtit' value='".$pos9."'>";
	echo "<tr>";
	echo "<td bgcolor=#999999><b>Item</td></b>";
	echo "<td bgcolor=#999999><b>Valor</b></td>";
	echo "</tr>";
	echo "<tr>";
	//echo "<td bgcolor=#cccccc>Administrador</td>";			
	echo "<td bgcolor=#cccccc>";
	if(substr($wsw,0,1) == "1")
	{
	    	
			$query = "select ".$empresa."_000007.Codigo,".$empresa."_000008.Nombre from ".$empresa."_000007,".$empresa."_000008 ";
			$query .= " where ".$empresa."_000007.equipo = '".$row[1]."' ";
			$query .= "      and ".$empresa."_000007.dia = '".substr($diasem,0,2)."' ";
			$query .= "      and ".$empresa."_000007.hi <= '".$row[4]."' ";
			if(!isset($row[5]))
			$query .= "      and ".$empresa."_000007.hf >= '".$pos6."' ";
			else
			$query .= "      and ".$empresa."_000007.hf >= '".$row[5]."' ";
			$query .= "      and ".$empresa."_000007.activo = 'A' ";
			$query .= "      and ".$empresa."_000007.codigo = ".$empresa."_000008.codigo  ";
			$query .= "  group by ".$empresa."_000007.codigo order by ".$empresa."_000007.codigo ";
		
	}
	else
		//echo $query = "select codigo,nombre,oficio,tipo,edad_pac,activo from ".$empresa."_000008 where oficio='1-MEDICO' and codigo != '0' order by codigo";
		 $query = "SELECT ".$empresa."_000008.codigo, ".$empresa."_000008.nombre, ".$empresa."_000008.oficio, ".$empresa."_000008.tipo, ".$empresa."_000008.edad_pac, ".$empresa."_000008.activo
				  FROM ".$empresa."_000008, ".$empresa."_000007
				  WHERE oficio IN ( '1-MEDICO', '3-TECNICO' )
                  AND ".$empresa."_000008.codigo != '0'
                  AND ".$empresa."_000008.codigo = ".$empresa."_000007.codigo
                  AND ".$empresa."_000007.dia = '".substr($diasem,0,2)."'
				  AND ".$empresa."_000007.activo = 'A' 
                  group by ".$empresa."_000008.codigo ORDER BY ".$empresa."_000008.codigo ";   //se cambio la consulta para que liste los medicos que esten disponibles ese dia
	
	$err1 = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
	$num1 = mysql_num_rows($err1);
	/*echo "<select name='Codmed'>";
	for ($i=0;$i<$num1;$i++)
	{	
		$row1 = mysql_fetch_array($err1);
		if((isset($Codmed) and substr($Codmed,0,5) == $row1[0]) or (strlen($pos1) > 1 and $pos1 == $row1[0]))
			echo "<option selected>".$row1[0]."-".$row1[1]."</option>";
		else
			echo "<option>".$row1[0]."-".$row1[1]."</option>";
	}*/
	echo "</td></tr>";
	$query = "select codigo,descripcion,uni_hora,hi,hf,activo from ".$empresa."_000003 where codigo='".$pos2."'";
	$err1 = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
	$num1 = mysql_num_rows($err1);
	$row1 = mysql_fetch_array($err1);
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Sala</td>";			
	echo "<td bgcolor=#cccccc>".$row1[0]."-".$row1[1]."</td>";
	echo "</tr>";
	
	if(substr($wsw,2,1) == "1")
	{
	
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Criterio x Solicitud</td>";
		echo "<td bgcolor=#cccccc><input type='TEXT' name='Criterio' id='criterio' size=50 maxlength=50 onkeypress='return validarEnter(event,\"Codexa\");' onBlur='llenarSelect(\"criterio\",\"Codexa\");' ></td>";			
		//echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Solicitud</td>";			
		echo "<td bgcolor=#cccccc>";
		
		
		echo "<select name='Codexa' id='Codexa'>";
		
		$val1=dibSelect($buscar, $Codexa, $cod_examen,$empresa);
		// echo $val1;
		echo "</select>";
		echo "</td></tr>";
		//echo "<input type='HIDDEN' name= 'Codexa' id= 'Codexa' value='".$Codexa."'>";
	}
	else
	{
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Solicitud</td>";			
		echo "<td bgcolor=#cccccc>";
		echo "<select name='Codexa'>";
		
		
		$query = "select codigo,descripcion,preparacion,cod_equipo,activo,especial from ".$empresa."_000006 where cod_equipo='".$pos2."' and codigo != '0' and activo = 'A' order by codigo";
		
		
		$err1 = mysql_query($query,$conex) or die(mysql_errno().": Error en el query: $query ".mysql_error());
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
	$tipoa = (isset($tipoa) && ($tipoa == 'Interno' || $tipoa == 'Ambulatorio' )) ? $tipoa: '';
	$tipoa = ($row['tipoA'] != '') ? $row['tipoA'] : $tipoa;

	if ($tipoAtencion=='on') 
	{
	
		// $tipoa=$row['tipoA'];
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Tipo Atencion</td>";
		echo "<td bgcolor=#cccccc>";
		if (isset($tipoa) and $tipoa=='Interno')
		{
			echo "<input type='radio' name='tipoa' id='interno' value='Interno' checked>Interno";
			echo "<input type='radio' name='tipoa' id='ambulatorio' value='Ambulatorio'>Ambulatorio</td>";
		}
		else if (isset($tipoa) and $tipoa=='Ambulatorio')
		{
			echo "<input type='radio' name='tipoa' id='interno' value='Interno'>Interno";
			echo "<input type='radio' name='tipoa' id='ambulatorio' value='Ambulatorio' checked>Ambulatorio</td>";
		}
		else
		{
			echo "<input type='radio' name='tipoa' id='interno' value='Interno'>Interno";
			echo "<input type='radio' name='tipoa' id='ambulatorio' value='Ambulatorio'>Ambulatorio</td>";
		}
		echo "</td>";
		echo "</tr>";
	}
	else
	{
		$tipoa="";
	}
	
	/**/
	
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Fecha Solicitada</td>";
	echo "<td bgcolor=#cccccc><input type='text' name='FecSol' id='FecSol' value='".$row[18]."' ></td>";			
	echo "</td>";
	echo "</tr>";
	/**/
	
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Fecha Turno</td>";
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
	
	/*echo "<tr>";
	echo "<td bgcolor=#cccccc>Cedula</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Cedula' id='Cedula' size=12 maxlength=15 value='".$row[13]."' onblur='validacion()'></td>";			
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Nombre</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Nompac' size=50 maxlength=50 value='".$row[6]."'></td>";			
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Centro de Costos</td>";
	echo "<td bgcolor=#cccccc>";	
	echo "<select name='Nitres'>";
	$query = "select descripcion,nit from ".$empresa."_000002 WHERE nit !=  '0' and activo='A' order by descripcion ";
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
	echo "</tr>";*/
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Celular</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Tel' size=30 maxlength=30 value='".$row[8]."'></td>";			
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Cantidad de Personas</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Edad' size=3 maxlength=3 value='".$row[9]."'></td>";			
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Comentarios</td>";
	echo "<td bgcolor=#cccccc><textarea name='Coment' cols=60 rows=5>".$row[10]."</textarea>";			
	echo "</td>";
	echo "</tr>";
	/*echo "<tr>";
	echo "<td bgcolor=#cccccc>Insertar <br> Comentario Fijo</td>";
	echo "<td bgcolor=#cccccc><input type='checkbox' name='Fijo'>";			
	echo "</td>";
	echo "</tr>";*/
	if(substr($wsw,2,1) == "1")
	{
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Datos OK</td>";
		echo "<td bgcolor=#cccccc><input type='checkbox' name='ok'>";			
		echo "</td>";
		echo "</tr>";
	}
	// echo "<tr>";
	// echo "<td bgcolor=#cccccc>Asistio</td>";
	// if($row[15] == "on")
		// echo "<td bgcolor=#cccccc><input type='checkbox' name='Asistida' checked>";	
	// else
		// echo "<td bgcolor=#cccccc><input type='checkbox' name='Asistida'>";			
	// echo "</td>";
	// echo "</tr>";
	echo "<input type='hidden' name='Estado' value='A-Activo'>";
	// echo "<tr>";
	// echo "<td bgcolor=#cccccc>Activo</td>";
	// echo "<td bgcolor=#cccccc>";			
	// echo "<select name='Estado'>";
	// if ($row[12] == substr("A-Activo", 0, 1))
		// echo "<option selected>A-Activo</option>";
	// else
		// echo "<option>A-Activo</option>";
	// if ($row[12] == substr("I-Inactivo", 0, 1))
		// echo "<option selected>I-Inactivo</option>";
	// else
		// echo "<option>I-Inactivo</option>";	
	// echo "</td>";	
	// echo "</tr>";
	echo "<tr>";
	echo "<input type='HIDDEN' name= 'ret' id='ret' value='$ret'>";
	echo "<td bgcolor=#cccccc colspan=2 align=center><input type='button' value='GRABAR' onclick='javascript: retornar();'></td>";
	
	
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
	//include_once("free.php");
}
?>
</body>
</html>