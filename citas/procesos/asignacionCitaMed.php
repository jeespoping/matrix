<?php
include_once("conex.php");

/**
 * Crea los parametros extras de una url para Get
 */
function parametrosExtras( $variablesGET, $post = false ){
		
	$val = '';
	
	$superglobal = $_GET;
	if( $post ){
		$superglobal = $_POST;
	}
	
	foreach( $variablesGET as $key => $value ){
		
		if( $superglobal[ $value ] ){
			
			if( is_numeric($key) ){
				$val .= "&".$value."=".urlencode( $_GET[ $value ] );
			}
			else{
				$val .= "&".$key."=".urlencode( $_GET[ $value ] );
			}
		}
	}
	
	return $val;
}

function actualizarListaEspera( $conex, $citas_lab, $id, $fecha_cita, $hora_cita, $servicio = '' ){
	
	$val = false;
	
	// if( is_numeric( $id ) ){
		
		$sql = "UPDATE ".$citas_lab."_000032 
				   SET drvfec = '".$fecha_cita."',
					   drvhor = '".$hora_cita."00',
					   drvsag = '".$servicio."'
				 WHERE id = ".$id."
				";
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		
		if(mysql_affected_rows() !== false ){
			$val = true;
		}
	// }
	
	return $val;
}

function getParametrosDefault( $datos, $post = false ){
	
	$val = [];
	
	$superglobal = $_GET;
	if( $post ){
		$superglobal = $_POST;
	}
	
	foreach( $datos as $key => $value ){
		if( $superglobal[ $value ] ){
			array_push( $val, [ $key => $superglobal[ $value ] ] );
		}
	}
	
	return $val;
}

header("Content-Type: text/html;charset=ISO-8859-1");
include_once("citas/funcionesAgendaCitas.php");

$defaults = getParametrosDefault([
		'Cedula' => 'defaultCedula',
		'Nompac' => 'defaultNombre',
		'Nitres' => 'defaultNit',
		'Email'  => 'defaultCorreo',
		'Urlcit' => 'defaultUrl',
		'Edad' 	 => 'defaultEdad',
		'Tel' 	 => 'defaultTelefono',
		'Coment' => 'defaultComentarios',
	]);

$defaults = json_encode( $defaults );


$parametrosExtras = parametrosExtras([
										'defaultCedula',
										'defaultNombre',
										'defaultNit',
										'defaultCorreo',
										'defaultUrl',
										'defaultEdad',
										'defaultTelefono',
										'defaultComentarios',
										'idListaEspera',
								]);

if (isset($accion) and $accion == 'buscar')  
{ 
	$result = consultarCitas($conex,$wemp_pmla,$cedula,$fecha,$hin,$hfn,'1');
	
	echo $result;
	return ;
}
 
?>

<html>
<head>
<title>MATRIX</title>
<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet">
</head>

<BODY BGCOLOR='' TEXT='#000066'>

<script type="text/javascript">

    
	function validacion()
	{
		verificarCitas();
		
	}
	
	function enter()
	{	
		document.forms.Citas.submit();
	}
	
	function verificarCitas()
	{
	
		if ($("#Cedula").val()!= "")
		{
			$.post("asignacionCitaMed.php",
					{
						empresa:      $('#empresa').val(), 
						wemp_pmla:    $('#wemp_pmla').val(),
						consultaAjax: '',
						cedula:       $("#Cedula").val(),
						accion:       'buscar',
						fecha:		  $('#wfec').val(),
						hin:		  $("#Hin").val(),
						hfn:		  $("#Hf").val()
					}			
					,function(data_json) {

						if (data_json.negar == 1)
						{
							$("#divmensaje").html(data_json.mensaje2);
																
								$('#divmensaje').dialog({
								   width: 650,
								   modal : true,
						           beforeClose: function(){
						           	    $("#divmensaje").html('');

						           }
							    });
							
							$("#btngrabar").attr("disabled", "disabled");
						}else{

							if (data_json.notificacion ==1)
							{	
								if (data_json.mensaje != '')
								{							
									$("#divmensaje").html(data_json.mensaje);
																	
									$('#divmensaje').dialog({
									   width: 650,
									   modal : true,
							           beforeClose: function(){
							           	    $("#divmensaje").html('');
							                enter();  
							          }
								    });
								}							
								
							}else{
							    enter();
						    }
						

					    }
												
						return true;
													
					},
					"json"
				
			);
		}
		
		return false;
	}
	
	function setDefaults(){
		try{
			var defaults = <?= $defaults ?>;
			
			$(defaults).each(function(){
				
				var def = this;
				
				for( var x in def ){
					if( $( "[name="+x+"]" )[0].tagName.toLowerCase() != 'select' ){
						$( "[name="+x+"]" ).val( def[x] );
					}
					else{
						console.log( "[name="+x+"] > option:contains('"+def[x]+"')" );
						if( $( "[name="+x+"] > option:contains('"+def[x]+"')" ).length > 0 )
							$( "[name="+x+"] > option:contains('"+def[x]+"')" )[0].selected = true;
					}
				}
			})
		}
		catch(e){}
	}

	window.onload = function(){

		setDefaults();
		
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
		
		error = false;
		formMain = document.forms.Citas;

		//Buscando campos marquee
		var mqEstado = document.getElementsByTagName( "marquee" );
		var auxdiv = document.createElement( "div" );

		//Guardando el valor inicial del Comentario
		if( !formMain.hiComentInicial ){
			auxdiv.innerHTML = "<INPUT type='hidden' name='hiComentInicial' value ='"+formMain.Coment.value+"'>";
			formMain.appendChild( auxdiv.firstChild );
		}
		
		auxdiv.innerHTML = "<INPUT type='hidden' name='regNuevo' value='on'>";
		formMain.appendChild( auxdiv.firstChild );

		//Si existe un marquee en el formulario
		if( mqEstado.length > 0 ){

			//Los datos estan ok
			if( mqEstado[0].innerHTML == "LOS DATOS ESTAN OK!!!!" ){

				if( formMain.Coment.value != '' ){
					formMain.elements['regNuevo'].value = 'off';
					formMain.hiComentInicial.value = formMain.Coment.value;
				}
			}

			//Los datos estan incompletos
			if( mqEstado[0].innerHTML == "LOS DATOS ESTAN INCOMPLETOS -- INTENTELO NUEVAMENTE!!!!" 
				&& formMain.elements['Coment'].value != '' ){
				formMain.elements['regNuevo'].value = 'off';

				auxdiv.innerHTML = "<textarea name='taComentario' rows='5' readonly cols=60 style='background:#FFFFEE'>"+formMain.hiComentInicial.value+"</textarea>";

				formMain.Coment.innerHTML = formMain.hiComentAnt.value;

				error = true;
			}

			//Para turno Borrado
			if( mqEstado[0].innerHTML == "!!!! TURNO BORRADO !!!!" ){
				formMain.hiComentInicial.value = '';
			}
		}

		if( formMain.Coment.value == '' && formMain.elements[ 'abierto' ].value == 'on' ){
			formMain.elements['regNuevo'].value = 'on'
			
		}
		else if( formMain.Coment.value != '' && formMain.elements[ 'abierto' ].value == 'on' ){
			formMain.elements['regNuevo'].value = 'off'
		}

		if( error == false ){
			auxdiv.innerHTML = "<textarea name='taComentario' rows='5' readonly cols=60 style='background:#FFFFEE'>"+formMain.hiComentInicial.value+"</textarea>";
		}

		if( auxdiv.firstChild.value != '' ){
			if( formMain.elements['regNuevo'] &&  formMain.elements['regNuevo'].value != 'on' ){
				formMain.Coment.parentNode.innerHTML = auxdiv.innerHTML + "<br>" + formMain.Coment.parentNode.innerHTML;

				if( error == false ){
					formMain.Coment.value = "";
				}
			}
		}
		$( "#ui-datepicker-div").css("display", "none");
		
		var mqEstado = document.getElementsByTagName( "marquee" );
		
		if( mqEstado && mqEstado[0].innerHTML == "LOS DATOS ESTAN OK!!!!" && document.getElementById("ret").value=="on"){
			window.location.href=document.getElementById("retornar").href;
		}
		else{
			document.getElementById("ret").value=="off";
		}
	};
	
	
	function cambiar()
	{
	   
	 var index=document.forms.Citas.wtat.value;
	 
	 div = document.getElementById('servi');
	 //alert(index);
		if(index=='M-MEDICINA DOMICILIARIA')
		{
			div.style.display = '';
		}
		else
		{
			div.style.display = 'none';
		}
		 
	}

	function validarEmail(email)
	{
	      var res='';
	      if (email !== '')
	      {  
	          var re=/^[_a-z0-9-]+(.[_a-z0-9-]+)*@[a-z0-9-]+(.[a-z0-9-]+)*(.[a-z]{2,3})$/;
	          
	          if (!re.exec(email)) {
	              alert('El Email digitado es incorrecto');	              
	              res= '1';
	          }    
	      }
	      return res;
	}
	
	function retornar()
	{
		document.getElementById("ret").value="on";
		document.Citas.submit();
	}
-->
</script>
<style type="text/css">

</style>
<?php
/**********************************************************************************************************************  
[DOC]
	   PROGRAMA : 000001_prx6.php
	   Fecha de Liberación : 2007-05-03
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2011-01-07
	   
	   OBJETIVO GENERAL :Este programa ofrece al usuario una interface gráfica que permite grabar los  de las
	   las citas par los diferentes servicios hospitalarios ambulatorios.
	   
	   
	   REGISTRO DE MODIFICACIONES :
		2020-09-09:	Edwin Molina
					Se hacen cambios varios para recibir los datos por defecto que quedaran en la cita y vienen de la lista de espera para Drive Thru
	    2020-04-02: Arleyda Insignares C. Se adicionan parametros en la función envioEmailCita() para implementar 
	                citas de teleorientacion.
	    2020-03-25: Arleyda Insignares C. Se adiciona campo de correo electrónico para ser diligenciado en caso de dar una cita por telemedicina, solo en este caso el programa enviará un correo informando los datos de la cita y especificaciones según parámetro 'EmailAgendamiento' y 'telemedicina' en la tabla root_000051. 
	    2020-01-20: Arleyda Insignares C. se mejora la consulta de citas posteriores por identificación del paciente mediante la consulta consultarCitas() localizada en el include de citas.
	    2013-09-03: Se modifica el script para que calcule el indice de oportunidad desde la fecha actual hasta la fecha de la cita. Viviana Rodas
	    2013-08-29: Se modifica el programa para que calcule el indice de oportunidad entre las dos fechas ingresadas, la fecha solicitada y la fecha en la que se le dio la cita. Viviana Rodas
		2013-05-15: Se corrige la validacion de la fecha para que las citas no queden trocadas. Viviana Rodas
		2013-05-08: Se modifica el script en la accion buscar para que valide si la cita que se esta intentando asignar no se cruza con otra cita que ya este asignada. Viviana Rodas
	    2013-02-28: Se modifica en la accion buscar la consulta para que busque si el paciente tiene cita y esta en estado Activo y ademas la cedula no este vacia Viviana Rodas
	    2012-11-26: Se agrega la validacion para cuando se ingrese la cedula de un paciente, se verifique que este, yo tenga citas de la fecha en adelante. Viviana Rodas
		2012-10-01: Se cambian las consultas de las tablas citas.._000009 para que solo pregunta por las citas activas.Viviana Rodas
	    2012-09-18: Se agrega la opcion tipo de servicio solo si se selecciona en tipo de atencion domiciliaria. Viviana Rodas
	    2012-09-14: Se agrega la opcion de tipo de atencion ya que se separa la atencion clinica de la atencion domiciliaria. Viviana Rodas
	    2012-08-08: Se elimina el campo activo y el asiste ya que esta funcionalidad se realiza en la agenda, se modifica para que cuando se le de grabar retorne a la agenda sin darle en el link de retornar, se modifico para que solo muestre los medicos que atienden ese dia, tambien se guarda en la base de datos el indice de oportunidad y el tipo de cita en el momento donde se graba, ademas cuando se graba el campo asiste se guarda null para que cuando en la agenda se chequee asiste se le cambie el estado a on u off dependiendo. Viviana Rodas. 
	   
	   .2011-01-07
	   		Se modifica el acceso a la tabla 9 de citas con el proposito de traer por cedula la ultima informacion del
	   		paciente cuando se digita la cedula.
	   				
	   .2007-05-03
	   		Release de Versión Beta.
	   		
	   
	   		
	   		
[*DOC]   		
***********************************************************************************************************************/



function comentariosAnteriores( $id ){
	
	global $conex;
	global $empresa;
	
	$val = '';
	
	$sql = "SELECT
				 Comentario
			FROM
				{$empresa}_000009
			WHERE
				id = $id
			";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows[0];
	}
}


function sendToEmail( $wasunto, $mensaje, $altbody, $wremitente, $wdestinatarios, $ruta_archivo = "", $nombre_adjunto = "" ){
	
	include_once("root/PHPMailer/PHPMailerAutoload.php");

	$data 				= array('error'=>0,'mensError'=>"");	
	$fecha         		= date("Y-m-d");
    $hora          		= date("H:i:s");

	//Inicializando objeto Mail
	$mail 				= new PHPMailer();
	
	//Configuraciones generales del correo
	//Se asume que siempre los correos van a hacer enviados por gmail
	$mail->IsSMTP();
	$mail->SMTPDebug	= 0;
	$mail->SMTPAuth 	= true;
	$mail->SMTPSecure 	= "ssl";
	$mail->Host 		= "smtp.gmail.com";
	$mail->Port 		= 465;
	
	//Validaciones del remitente
	if( empty( $wremitente[ 'from' ] ) ){
		$wremitente[ 'from' ] = $wremitente[ 'email' ];
	}
	
	if( empty( $wremitente[ 'fromName' ] ) ){
		$wremitente[ 'fromName' ] = "";
	}
	
	//Datos del remitente
	$mail->Username 	= $wremitente[ 'email' ];
	$mail->Password 	= $wremitente[ 'password' ];
	$mail->From 		= $wremitente[ 'from' ];
	$mail->FromName 	= $wremitente[ 'fromName' ];
	 

	//Datos del correo
	$mail->Subject 		= $wasunto;
	$msghtml 			= $mensaje;
	$mail->AltBody 		= $altbody;
	$mail->MsgHTML( $msghtml );
	
	if ($ruta_archivo !== "") {
		for($i=1; $i<count($nombre_adjunto); $i++)
	    {
		    $mail->AddAttachment($ruta_archivo.$nombre_adjunto[$i],$nombre_adjunto[$i]);
	    }
	}
	
	//Correos de los destinatarios
	//Puede llevar un nombre de destino por posición y debe ser separado por - entre el correo y el nombre
	for($i=0; $i<count($wdestinatarios); $i++)
	{
		list( $wemail_destino, $wnombre_destino ) 	= explode( "-", $wdestinatarios[$i] );
		$wnombre_destino 							= empty( $wnombre_destino ) ? "": $wnombre_destino;
		$mail->AddAddress($wemail_destino, $wnombre_destino );
	}
	$mail->IsHTML(true);
	
	if(!$mail->Send()) {
		$data[ 'Error' ] 	 =  "0";
		$data[ 'mensError' ] =  "No se envió el correo";
	}
	else{
		$accion 	    	 = "envio email";
		$tabla		    	 = "";
		$descripcion    	 = "Se envio un correo Electronico a: {$wemail_destino}";
		
		$data[ 'Error' ] 	 =  "1";
		$data[ 'mensError' ] =  "Correo enviado";
	}
	
	return $data;
}

function consultarAliasPorAplicacion($conexion, $codigoInstitucion, $nombreAplicacion){
	$q = " SELECT
				Detval
			FROM
				root_000051
			WHERE
				Detemp = '".$codigoInstitucion."'
				AND Detapl = '".$nombreAplicacion."'";

	$res = mysql_query($q, $conexion) or die ("Error: " . mysql_errno() . " - en el query: " . $q . " - " . mysql_error());
	$num = mysql_num_rows($res);

	$alias = "";
	if ($num > 0)
	{
		$rs = mysql_fetch_array($res);

		$alias = $rs['Detval'];
	} else {
		terminarEjecucion("La institucion con el codigo :".$codigoInstitucion." no se encuentra, ni la aplicacion: ".$nombreAplicacion.".  Por favor verifique el valor de wemp_pmla");
	}
	return $alias;
}

echo "<center>";
echo "<table border=0 align=center>";
echo "<tr><td align=center bgcolor='#cccccc'><A NAME='Arriba'><font size=5><b>Registro de Informacion del Turno </b></font></a></tr></td>";
echo "<tr><td align=center bgcolor='#cccccc'><font size=2> <b> asignacionCitaMed.php Ver. 2013-05-15</b></font></tr></td></table>";
echo "</center>";
echo "<form  name='Citas' action='asignacionCitaMed.php' method=post>";


if( isset($hiComentInicial) ){
	echo "<INPUT type='hidden' name='hiComentInicial' value ='$hiComentInicial'>";
}

if( isset($Coment) ){
	echo "<INPUT type='hidden' name='hiComentAnt' value ='$Coment'>";
}

if( !isset($abierto) ){
	echo "<INPUT type='hidden' name='abierto' value='on'>";
}
else{
	echo "<INPUT type='hidden' name='abierto' value='off'>";
}

if( !isset($taComentario) ){
	$taComentario = '';
}

session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{		
	$key = substr($user,2,strlen($user));	
	strpos($Codequ,"-") = consultarAliasPorAplicacion($conex, $wemp_pmla, "digitosSbtrAg");

	echo "<input type='HIDDEN' id='empresa' name='empresa' value='".$empresa."'>";
    echo "<input type='hidden' id='colorDiaAnt' name='colorDiaAnt' value='".$colorDiaAnt."'>";
	echo "<input type='hidden' id='caso' name='caso' value='".$caso."'>";
	echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
	
	if( isset($idListaEspera) ){
		echo "<input type='hidden' id='idListaEspera' name='idListaEspera' value='".$idListaEspera."'>";
	}
	
	$fechaAct      = date("Y-m-d");
	$wtelemedicina = consultarAliasPorAplicacion($conex, $wemp_pmla, "telemedicina"); 
	
	if( !isset( $ret ) ){
		$ret = 'off';
	}

    
	if (isset($wpar) and !isset($ok))
	{
		//filtrar la opción examen en el menú
	    $pos = strpos(strtoupper($Codexa), $wtelemedicina);
	
		if (substr($Estado,0,1) == "A")
		{
			if(strpos($Hf,"p") !== false)
				$Hf=(string)((integer)substr($Hf,0,strpos($Hf,":")) + 12).substr($Hf,strpos($Hf,":")+1,2);
			else
				$Hf=substr($Hf,0,strpos($Hf,":")).substr($Hf,strpos($Hf,":")+1,2);
			
			//**** VALIDACIONES ****
			// Obtener nombre del profesional
			$qMedico = "select descripcion from ".$empresa."_000010 where codigo='".substr($pos2,0,strpos($pos2,"-"))."'";
			$execMed = mysql_query($qMedico,$conex);
			$rowMed  = mysql_fetch_array($execMed);
			$nommed  = $rowMed[0];

			$tiperr =0;
			if(isset($Fijo))
			{   //procedimiento dependiendo el medico
				$query = "select preparacion from ".$empresa."_000011 where cod_equipo='".substr($pos2,0,strpos($pos2,"-"))."' and codigo = '".substr($Codexa,0,strpos($Codexa,"-"))."'";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				$row1 = mysql_fetch_array($err1);
				$Coment=$Coment.$row1[0];
			}
			if($wpar == 1)
			{
				$query = "update ".$empresa."_000009 set activo='I' where  cod_equ='".$Codequ."' and fecha='".$Fecha."' and hi='".$Hi."' ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			}
			// Verificacion de la Ocupacion del Medico
			$query = "select hi from ".$empresa."_000009 where Cod_equ = '".substr($pos2,0,strpos($pos2,"-"))."'";
			$query = $query." and fecha = '".$wfec."'";
			$query = $query." and ((hi <= '".$Hi."' and hf <= '".$Hf."' and hf > '".$Hi."')";
			$query = $query."  or  (hi >= '".$Hi."' and hf >= '".$Hf."' and hi < '".$Hf."')";
			$query = $query."  or  (hi <= '".$Hi."' and hf >= '".$Hf."')";
			$query = $query."  or  (hi >= '".$Hi."' and hf <= '".$Hf."'))";
			$query = $query." and activo = 'A'";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err); 
			$errmen = '';
			if ($num > 0 ) 
				$tiperr = 2;
			else
				if($tiperr == 0)
					$tiperr = 0;

            $pos = strpos(strtoupper($Codexa), $wtelemedicina);
			// Verificacion de los datos de texto
			if (strlen($Nompac)==0 or strlen($Tel)==0 or $Edad==0 or strlen($Codexa)<=1 or strlen($Nitres)<=1)
				$tiperr = 3;
			else
                if( $Email == '' && $pos==true)
                {
                    $tiperr = 3;
                    $errmen = 'CORREO ELECTRONICO';
                }
                else
                {
					if( $tiperr == 0)
						$tiperr = 0;
			    }
			if($wpar == 1)
			{
				if($tiperr == 0)
				{
					
					
					$query = "select id from ".$empresa."_000009 where  cod_equ='".substr($Codequ,0, strpos($Codequ,"-"))."' and fecha='".$Fecha."' and hi='".$Hi."'";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$row = mysql_fetch_array($err);
					$OLDID=$row[0];
					$query = "delete from ".$empresa."_000009 where  cod_equ= '".substr($Codequ,0, strpos($Codequ,"-"))."' and fecha='".$Fecha."' and hi='".$Hi."'";
					$err = mysql_query($query,$conex);
				}
				else
				{
					$query = "update ".$empresa."_000009 set activo='A' where  cod_equ='".$Codequ."' and fecha='".$Fecha."' and hi='".$Hi."' ";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				}
			}
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
			
			if ($wtat != "M-MEDICINA DOMICILIARIA")  //para que envie el servicio en blanco cuando no sea medicina domiciliaria
			{
				$wser="";
			}
			
			switch ($wpar)
			{				
				case 1: 
					$Nompac = strtoupper($Nompac);
					$Nompac = ucwords($Nompac);
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					$Coment = trim($taComentario."\n\n".$Coment); 
					
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
						$Asistida="on";
					else
						$Asistida="off"; 
					$query = "insert ".$empresa."_000009 (medico,fecha_data,hora_data,cod_equ,cod_exa,fecha,hi,hf,Cedula,nom_pac,nit_res,telefono,urlcit,email,edad,comentario,Asistida,usuario,activo,tipoA,tipoS, tipoCita,dias_opor,fecSol,diaOporAct,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".substr($Codequ,0, strpos($Codequ,"-"))."','".substr($Codexa,0,strpos($Codexa,"-"))."','".$Fecha."','".$Hi."','".$Hf."','".ucwords($Cedula)."','".ucwords($Nompac)."','".$Nitres."','".$Tel."','".$Urlcit."','".$Email."',".$Edad.",'".$Coment."','".$Asistida."','".substr($user,2,strlen($user))."','".substr($Estado,0,1)."','".$wtat."','".$wser."','".@$tipoCita."','".@$diasOpor."','".$FecSol."','".$diasOporAct."','C-".$empresa."')";

                    if ($Email != '' && $pos==true)
                    {
					    envioEmailCita($conex,$wemp_pmla,$Fecha,$Hi,ucwords($Cedula),ucwords($Nompac),$Email,$Urlcit,$nommed,$empresa,$Codexa,1);
                    }

					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$query = "select id from ".$empresa."_000009 where  cod_equ='".substr($Codequ,0, strpos($Codequ,"-"))."' and fecha='".$Fecha."' and hi='".$Hi."'";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$row = mysql_fetch_array($err);
					$query = "update ".$empresa."_000008 set cita=".$row[0]." where  cita= ".$OLDID;
					$err = mysql_query($query,$conex);
					
					if( isset($idListaEspera) ){
						actualizarListaEspera( $conex, $empresa, $idListaEspera, $Fecha, $Hi, substr($pos2,4) );
					}
					
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>LOS DATOS ESTAN OK!!!!</MARQUEE></FONT>";
					echo "<br><br>";
				break;
				case 2:
					$Nompac = strtoupper($Nompac);
					$Nompac = ucwords($Nompac);
					$fecha = date("Y-m-d");
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
							$fecha1=strtotime($FecSol); //fecha actual en unix
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
						$Asistida="on";
					else
						$Asistida="off"; 
					 $query = "insert ".$empresa."_000009 (medico,fecha_data,hora_data,cod_equ,cod_exa,fecha,hi,hf,Cedula,nom_pac,nit_res,telefono,urlcit,email,edad,comentario,Asistida,usuario,activo,tipoA,tipoS,tipoCita,dias_opor,fecSol,diaOporAct,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".substr($Codequ,0, strpos($Codequ,"-"))."','".substr($Codexa,0,strpos($Codexa,"-"))."','".$Fecha."','".$Hi."','".$Hf."','".ucwords($Cedula)."','".ucwords($Nompac)."','".$Nitres."','".$Tel."','".$Urlcit."','".$Email."',".$Edad.",'".$Coment."','".$Asistida."','".substr($user,2,strlen($user))."','".substr($Estado,0,1)."','".$wtat."','".$wser."','".@$tipoCita."','".@$diasOpor."','".$FecSol."','".$diasOporAct."','C-".$empresa."')";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					
					if ($Email != '' && $pos==true)
					    envioEmailCita($conex,$wemp_pmla,$Fecha,$Hi,ucwords($Cedula),ucwords($Nompac),$Email,$Urlcit,$nommed,$empresa,$Codexa,1);

					if(isset($wsw1))
					{
						$query = "select id from ".$empresa."_000009 where  cod_equ='".substr($Codequ,0, strpos($Codequ,"-"))."' and fecha='".$Fecha."' and hi='".$Hi."'";
						$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
						$row = mysql_fetch_array($err);
						$query = "insert ".$empresa."_000008 (medico,fecha_data,hora_data, Fecha, Hora, Cita, Identificacion, Paciente, Edad, Sexo, Historia, Sgs, Atencion, Diagnostico1, Dx_Nuevo1, Nrosotf, Nrosatf, Nrosoto, Nrosato, Nrosotl, Nrosatl, Terapeuta, Ri_pac, Ri_ter, Alta, Control, Observaciones, seguridad) values ('".$empresa."','".$fecha."','".$hora."','".$Fecha."','".substr($Hi,0,2).":".substr($Hi,2,2).":00','".$row[0]."','".ucwords($Cedula)."','".ucwords($Nompac)."',".$Edad.",'NO APLICA',0,0,'NO APLICA','NO APLICA','NO APLICA','NO',0,0,0,0,0,0,'NO APLICA','NO APLICA','NO APLICA','NO APLICA','.','C-".$empresa."')";
						$err = mysql_query($query,$conex) or die("ERROR GRABANDO HISTORIA : ".mysql_errno().":".mysql_error());
					}
					
					if( isset($idListaEspera) ){
						actualizarListaEspera( $conex, $empresa, $idListaEspera, $Fecha, $Hi, substr($pos2,4) );
					}
					
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCFF LOOP=-1>LOS DATOS ESTAN OK!!!!</MARQUEE></FONT>";
					echo "<br><br>";
				break;
				case 3:
					$query = "select id from ".$empresa."_000009 where  cod_equ='".substr($Codequ,0, strpos($Codequ,"-"))."' and fecha='".$Fecha."' and hi='".$Hi."'";
					$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
					$row = mysql_fetch_array($err);
					$query = "delete from ".$empresa."_000008 where  cita= ".$row[0];
					$err = mysql_query($query,$conex);
					$query = "delete from ".$empresa."_000009 where  cod_equ= '".substr($Codequ,0, strpos($Codequ,"-"))."' and fecha='".$Fecha."' and hi='".$Hi."'";
					$err = mysql_query($query,$conex);
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#00FF00 LOOP=-1>!!!! TURNO BORRADO !!!!</MARQUEE></FONT>";
					echo "<br><br>";
					unset($Codmed);
					unset($Codexa);
					unset($Nompac);
					unset($Nitres);
					unset($Tel);
					unset($Email);
					unset($Urlcit);
					unset($nommed);
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
				case 2:
				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33cc33 LOOP=-1>EL MEDICO YA TIENE UNA CITA ASIGNADA INCOMPATIBLE CON LA QUE USTED DESEA -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
				echo "<br><br>";
				break;
				case 3:

				echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CCCCFF LOOP=-1>LOS DATOS ". $errmen ." ESTAN INCOMPLETOS -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
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
	if(isset($wsw1))
		echo "<input type='HIDDEN' name= 'wsw1' value='".$wsw1."'>";
	echo "<input type='HIDDEN' id='pos2' name= 'pos2' value='".$pos2."'>";
	echo "<input type='HIDDEN' id='pos3' name= 'pos3' value='".$pos3."'>";
	echo "<input type='HIDDEN' id='pos4' name= 'pos4' value='".$pos4."'>";
	echo "<input type='HIDDEN' id='pos5' name= 'pos5' value='".$pos5."'>";
	echo "<input type='HIDDEN' id='pos6' name= 'pos6' value='".$pos6."'>";
	echo "<input type='HIDDEN' id='pos7' name= 'pos7' value='".$pos7."'>";
	echo "<input type='HIDDEN' id='pos8' name= 'pos8' value='".$pos8."'>";
	echo "<input type='HIDDEN' id='pos9' name= 'pos9' value='".$pos9."'>";
	echo "<input type='HIDDEN' id= name= 'Diasem' value='".$diasem."'>"; //cambiado para que solo traiga los activos 
	
	$query = "select cod_equ,cod_exa,fecha,hi,hf,Cedula,nom_pac,nit_res,telefono,edad,comentario,Asistida,usuario,activo,id,fecSol,diaOporAct,Email,Urlcit from ".$empresa."_000009 where  cod_equ='".substr($pos2,0,strpos($pos2,"-"))."' and fecha='".$pos4."' and hi='".$pos5."' and Activo='A'";
	
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);

	
	echo "<table border=0 align=center>";  //tabla para asignar la cita
	if(isset($wsw1))
		echo "<li><A HREF='agendaMedicos.php?wequ=".$pos2."&wfec=".$pos4."&empresa=".$empresa."&wsw1=".$wsw1."&colorDiaAnt=".$colorDiaAnt."&caso=".$caso."&wemp_pmla=".$wemp_pmla."&nomdia=$nomdia{$parametrosExtras}' id='retornar'>Retornar</A><br>";
	else
		echo "<li><A HREF='agendaMedicos.php?wequ=".$pos2."&wfec=".$pos4."&empresa=".$empresa."&colorDiaAnt=".$colorDiaAnt."&caso=".$caso."&wemp_pmla=".$wemp_pmla."&nomdia=$nomdia{$parametrosExtras}' id='retornar'>Retornar</A><br>";
	//if ($num > 0)
		//echo "<li><A HREF='000001_prx4.php?par1=".$pos2."&par2=".$pos4."&par3=".$pos5."&empresa=".$empresa."' target = '_blank'>Imprimir</A><br>";
	if ($num > 0)
	{
		$row = mysql_fetch_array($err);
		$pos6=$row[4];
		$pos3=$row[1];
		if(isset($wsw1))
		{
			$query = "select fecha_data, hora_data, id from ".$empresa."_000008 where  cita=".$row[14];
			$err1 = mysql_query($query,$conex);
			$row1 = mysql_fetch_array($err1);
			echo "<li><A HREF='/matrix/det_registro.php?id=".$row1[2]."&pos1=".$empresa."&pos2=".$row1[0]."&pos3=".$row1[1]."&pos4=000008&pos5=0&pos6=".$empresa."&tipo=P&Valor=&Form=000008-".$empresa."-C-Estadisticas&call=2&key=".$empresa."&Pagina=&amp;change=1' target='_blank'>Estadisticas</a>";
		}
		echo "<input type='HIDDEN' name= 'wpar' value='1'>";
	}
	else
	{
		$row[0]=substr($pos2,0,strpos($pos2,"-"));			//Equipo
		if(!isset($Codexa))
			$row[1]="0";				//Examen
		else
			$row[1]=$Codexa;	
		$row[2]=$pos4;			//Fecha
		$row[3]=$pos5;			//Hi
		if(isset($Hf))
			$pos6=$Hf;
		//$row[4]=$pos6;			//Hf
		if(!isset($Cedula))
		{
			$row[5]="";		//Cedula
			if( empty( $defaultCedula ) )
				echo "<input type='HIDDEN' name= 'ok' value='1'>";
		}			
		else
		{
			$row[5]=$Cedula;
			if(isset($ok))
			{
				$row[5]=$Cedula;
				$query = "select Nom_pac, Nit_res, Descripcion, Telefono, Edad, Fecha, Email, Urlcit from ".$empresa."_000009,".$empresa."_000002 where  Cedula='".$Cedula."' and Nit_res=Nit order by Fecha desc ";
				$err2   = mysql_query($query,$conex);
				$row2   = mysql_fetch_array($err2);
				$Nompac = $row2[0];
				$Nitres = $row2[1];
				$Tel    = $row2[3];
				$Edad   = $row2[4];
				$Email  = $row2[6];
				$Urlcit = $row2[7];
				
			}
		}
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
		if(!isset($Asistida))
			$row[11]="off";				//Asistida
		else
			$row[11]="on";
		$row[12]="";
		$row[13]="";
		if(!isset($FecSol))
			$row[15]=$fechaAct;			//fecha para la cual se solicito la cita, para que salga la fecha actual por defecto
		else
			$row[15]=$FecSol;

		if(!isset($Email))
			$row[17]="";				//Email
		else
			$row[17]=$Email;

		if(!isset($Urlcit))
			$row[18]="";				//URL
		else
			$row[18]=$Urlcit;

		echo "<input type='HIDDEN' name= 'wpar' value='2'>";
	}
	echo "<input type='HIDDEN' name= 'Codequ' value='".$row[0]."'>";
	echo "<input type='HIDDEN' name= 'Codexa' value='".$row[1]."'>";
	echo "<input type='HIDDEN' name= 'Fecha' value='".$row[2]."'>";
	echo "<input type='HIDDEN' name= 'Hi' value='".$row[3]."'>";
	//echo "<input type='HIDDEN' name= 'Hf' value='".$row[4]."'>";
	echo "<input type='HIDDEN' name= 'wfec' id= 'wfec' value='".$pos4."'>";
	echo "<input type='HIDDEN' name= 'wequ' value='".$row[0]."'>";
	echo "<tr>";
	echo "<td bgcolor=#999999><b>Item</td></b>";
	echo "<td bgcolor=#999999><b>Valor</b></td>";
	echo "</tr>";
	$query = "select Codigo, Descripcion, Sednom  
	            From ".$empresa."_000010 
	            Left join root_000128 
		          on root_000128.Sedcod = ".$empresa."_000010.Sedcod 
		       where codigo='".substr($pos2,0,strpos($pos2,"-"))."' group by Codigo, Descripcion";

	$err1   = mysql_query($query,$conex);
	$num1   = mysql_num_rows($err1);
	$row1   = mysql_fetch_array($err1);
	$nommed = $row1[1];
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Medico</td>";			
	echo "<td bgcolor=#cccccc>".$row1[0]."-".$row1[1]."</td>";
	echo "<td> ";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Sede</td>";		
	echo "<td bgcolor=#cccccc>".$row1[2]."</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Examen</td>";			
	echo "<td bgcolor=#cccccc>";
	echo "<select name='Codexa'>";
	$query = "select codigo,descripcion,preparacion,cod_equipo,activo from ".$empresa."_000011 where cod_equipo='".substr($pos2,0,strpos($pos2,"-"))."' and codigo != '0' order by codigo";
	$err1 = mysql_query($query,$conex);
	$num1 = mysql_num_rows($err1);
	for ($i=0;$i<$num1;$i++)
	{	
		$row1 = mysql_fetch_array($err1);
		if(($pos3 != "0" and $pos3 == $row1[0]) or (isset($Codexa) and substr($Codexa,0,strpos($Codexa,"-")) == $row1[0]))
			echo "<option selected>".$row1[0]."-".$row1[1]."</option>";
		else
			echo "<option>".$row1[0]."-".$row1[1]."</option>";
	}
	echo "</td></tr>";
	
	if ($empresa == "citascs")
	{
		//** tipo de atencion
		echo "<tr>";
		echo "<td bgcolor=#cccccc>Tipo Atencion</td>";			
		echo "<td bgcolor=#cccccc>";
		$query = "SELECT Selcod, Seldes  from clisur_000105 where Seltip='02' and Selest='on' AND selcod != 'C' AND selcod != 'H' AND selcod != 'U'
		AND selcod != 'P' AND selcod != 'E' AND selcod != 'N' AND selcod != 'CP'order by Selpri desc";
		$errq1 = mysql_query($query,$conex) or die(mysql_errno()."error en el query - ".$query." ".mysql_error());
		$numq1 = mysql_num_rows($errq1);
		echo "<select name='wtat' id=wtat onchange='cambiar()'>";
		if ($numq1>0)
		{
			for ($i=0;$i<$numq1;$i++)
			{
				$row2 = mysql_fetch_array($errq1);
				if($wtat == $row2[0]."-".$row2[1])
					echo "<option selected value='".$row2[0]."-".$row2[1]."'>".$row2[0]."-".$row2[1]."</option>";
				else
					echo "<option value='".$row2[0]."-".$row2[1]."'>".$row2[0]."-".$row2[1]."</option>";
			}
		}
		echo "</select>";
		echo "</td></tr>";
	}
	
	//*************si se selecciona domiciliaria aparece este select para determinar el tipo de atencion
	if( @$wtat != 'M-MEDICINA DOMICILIARIA' ){
		echo "	<tr id='servi' style='display:none;'>";
	}
	else{
		echo "	<tr id='servi'>";              //AGREGAR PARA QUE SE MUESTRE SOLO EN LA CLINICA DEL SUR
	}
	
	//** tipo de servicio
	
	echo "<td bgcolor=#cccccc>Tipo Servicio</td>";			
	echo "<td bgcolor=#cccccc>";
	$query = "SELECT Selcod, Seldes  from clisur_000105 where Seltip='14' and Selest='on'  order by Selpri";
	$errq = mysql_query($query,$conex) or die(mysql_errno()."error en el query - ".$query." ".mysql_error());
	$numq = mysql_num_rows($errq);
	echo "<select name='wser' id=wser >";
	if ($numq>0)
	{
		for ($i=0;$i<$numq;$i++)
		{
			$row1 = mysql_fetch_array($errq);
			if($wser == $row1[0]."-".$row1[1])
				echo "<option selected value='".$row1[0]."-".$row1[1]."'>".$row1[0]."-".$row1[1]."</option>";
			else
				echo "<option value='".$row1[0]."-".$row1[1]."'>".$row1[0]."-".$row1[1]."</option>";
		}
	}
	echo "</select>";
	echo "</td></tr>";
	//******************
	
	/**/
	
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Fecha Solicitada</td>";
	echo "<td bgcolor=#cccccc><input type='text' name='FecSol' id='FecSol' value='".$row[15]."' ></td>";			
	echo "</td>";
	echo "</tr>";
	/**/
	
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Fecha Cita</td>";
	echo "<td bgcolor=#cccccc>".$diasem." ".$pos4."</td>";			
	echo "</td>";
	echo "</tr>";

	
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Hora Inicio</td>";
	echo"<input type='hidden' name='Hin' id='Hin' value='".$pos5."'>";
	if(substr($pos5,0,2) > "12")
	{
		$hr1 ="". (string)((integer)substr($pos5,0,2) - 12).":".substr($pos5,2,2). " pm ";
		echo "<td bgcolor=#cccccc><font size=2>".$hr1."</font></td>";
	}
	else
		echo "<td bgcolor=#cccccc><font size=2>".substr($pos5,0,2).":".substr($pos5,2,2)."</font></td>";
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	if(strpos($pos6,"p") !== false)
	{
		$pos6=substr($pos6,0,strpos($pos6," "));
		$pos6=(string)((integer)substr($pos6,0,2) + 12).substr($pos6,2);
	}
	if(strpos($pos6,":") !== false)
		if(substr($pos6,0,strpos($pos6,":")) < 10)
			$pos6="0".substr($pos6,0,strpos($pos6,":")).substr($pos6,strpos($pos6,":")+1);
		else
			$pos6=substr($pos6,0,strpos($pos6,":")).substr($pos6,strpos($pos6,":")+1);
	echo "<td bgcolor=#cccccc>Hora Final</td><td bgcolor=#cccccc><select name='Hf' id='Hf' onchange='validacion()'>";

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
	$weq=0;
	while ($whi <= $wul)
	{
		if($whi == $wul)
			$weq=1;
		if(substr($whi,0,2) > "12")
		{
			$hr1 ="". (string)((integer)substr($whi,0,2) - 12).":".substr($whi,2,2). " pm ";
			if($pos6 == $whi)
				echo "<option value='".$hr1."' selected><font size=2>".$hr1."</font></option>";
			else
				echo "<option value='".$hr1."'><font size=2>".$hr1."</font></option>";
		}
		else
			if($pos6 == $whi)
				echo "<option value='".substr($whi,0,2).":".substr($whi,2,2)."' selected><font size=2>".substr($whi,0,2).":".substr($whi,2,2)."</font></option>";
			else
				echo "<option value='".substr($whi,0,2).":".substr($whi,2,2)."'><font size=2>".substr($whi,0,2).":".substr($whi,2,2)."</font></option>";
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
	if($weq == 0)
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
			
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Cedula</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Cedula' id='Cedula' size=12 maxlength=15 value='".$row[5]."' onblur='validacion()'></td>";			
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Paciente</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Nompac' size=50 maxlength=50 value='".$row[6]."'></td>";			
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Responsable_Cuenta</td>";
	echo "<td bgcolor=#cccccc>";			
	echo "<select name='Nitres'>";
	$query = "select descripcion,Empresa from ".$empresa."_000013,".$empresa."_000002 ";
	$query .= "  WHERE Medico_Tratante =  '".substr($pos2,0,strpos($pos2,"-"))."' ";
	$query .= "       AND Empresa =  Nit ";
	$query .= "       AND ".$empresa."_000013.Activo =  'on' ";
	$query .= "       AND ".$empresa."_000002.Activo =  'A'  ";
	$query .= "  order by descripcion ";
	$err1 = mysql_query($query,$conex);
	$num1 = mysql_num_rows($err1);
	for ($i=0;$i<$num1;$i++)
	{	
		$row1 = mysql_fetch_array($err1);
		if ($row[7] == $row1[1])
			echo "<option selected>".$row1[0]."-".$row1[1]."</option>";
		else
			echo "<option>".$row1[0]."-".$row1[1]."</option>";
	} 
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Telefono</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Tel' size=30 maxlength=30 value='".$row[8]."'></td>";	
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Correo electr&oacutenico</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' id='Email' name='Email' size=60 maxlength=80 value='".$row[17]."' onChange='validarEmail(this.value);'></td>";	
	echo "</tr>";	
	echo "<tr>";
	echo "<td bgcolor=#cccccc>URL</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' id='Urlcit' name='Urlcit' size=80 maxlength=500 value='".$row[18]."'></td>";	
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Edad</td>";
	echo "<td bgcolor=#cccccc><input type='TEXT' name='Edad' size=3 maxlength=3 value='".$row[9]."'></td>";			
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Comentarios</td>";
	echo "<td><textarea name='Coment' cols=60 rows=5>".$row[10]."</textarea>";			
	echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Insertar <br> Comentario Fijo</td>";
	echo "<td bgcolor=#cccccc><input type='checkbox' name='Fijo'>";			
	echo "</td>";
	echo "</tr>";
	echo "<input type='hidden' name='Estado' value='A-Activo'>";
	echo "<tr>";
	echo "<input type='HIDDEN' name= 'ret' id='ret' value='$ret'>";
	echo "<td bgcolor=#cccccc colspan=2 align=center><input type='button' id='btngrabar' value='GRABAR' onclick='javascript: retornar();'></td>";
	echo "</tr>";
	echo "</table>";
	echo "<table border=0 align=center>";
	echo "<tr><td><A HREF='#Arriba'><B>Arriba</B></A></td></tr></table>";
	echo "<div id='divmensaje' class='modal' title='El paciente tiene citas asignadas' style='width:250px;display:none;'>
               <br><center><b></b></center><br>
          </div>";
	//include_once("free.php");
}
?>
</body>
</html>