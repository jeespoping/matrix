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


if (isset($accion) and $accion == 'actualizar')
 { 

	$sql = "UPDATE
						".$wemp_pmla."_000009
					SET
						Asistida = '".$est."'
						
					WHERE
						id = '$id'";
			 
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	return;
}
else if (isset($accion) and $accion == 'cancelar')
   { 	
	
	$sql = "UPDATE
						".$wemp_pmla."_000009
					SET
						Activo = '".$est."',
						Causa = '".$causa."'
						
					WHERE
						id = '$id'";
			 
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	return;
}

if(isset($accion) and $accion == 'consultaObsercaciones')
{
			$data= array( 'error'=>0, 'mensaje'=>'');     

			
			$wequ=explode("-",$wequ);
			$wequ=$wequ[0];
			// echo $empresa."-".$wfec."-".$wequ;
			
			$query = "select Fecha_I, Fecha_F, Codigo, Control  
						  from ".$empresa."_000012 
						  where Fecha_I <= '".$wfec."'
						  and Fecha_F >= '".$wfec."'
						  and Codigo = '".$wequ."'
						  and Activo = 'A'
						  and Uni_hora = 1
						  ";
					$err1 = mysql_query($query,$conex) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );	
					$num1 = mysql_num_rows($err1);
					
					if ($err1)
					{
						if ($num1 > 0)
						{
							for($i=0;$rows=mysql_fetch_array($err1);$i++)
							{
								if($i==0)
								{
									$data['mensaje'].=utf8_encode($rows['Control']);
								}
								else
								{
									$data['mensaje'].=utf8_encode("<br>".$rows['Control']);
								}
							}
														
						}
					}
					else
					{
						$data['mensaje']="No se pudo realizar la consulta a la tabla ".$empresa."_000021";
					}
	echo json_encode($data);
	return;
}
	
?>
<html>
<head>
<title>MATRIX</title>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.accordion.js"></script>         
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<link type="text/css" href="../../../include/root/ui.all.css" rel="stylesheet" />        <!-- Nucleo jquery -->
	<script type="text/javascript">
	<!--
$(document).ready(function() {
	 
	
	buscarObservaciones()
});

function buscarObservaciones()
{ 
	var empresa=$("#empresa").val();
	var wfec=$("#wfec").val();
	var wequ=$("#wequ").val();
	
	
	$.post("agendaMedicos.php",
	{
		accion      :"consultaObsercaciones",
		consultaAjax:'',
		empresa     :empresa,
		wfec        :wfec,
		wequ		:wequ
	},
	function(data){
	
		if (data.error == 1)
		{
			if (data.mensaje != "")
			{
				alert(data.mensaje);
			}
		}
		else
		{ 
			if (data.mensaje != "")
			{ 
				//$("#observacion").css("display", "");
				$("#observacion").html(data.mensaje);
				$("#observacion").parent().show('blind', {}, 500);				
				$("#observacion").effect("pulsate", {}, 10000);
			}
			else
			{
				$("#observacion").parent().hide();
			}
		}
	},
	"json"
	
	);
	
}
		
		function enter()
		{
			document.forms.citas.submit();
		}
		
		function asiste(id_checkbox, id)
		{	
			
			var valora = 'off';
			if ($("#"+id_checkbox).is(':checked'))
			{ 
				valora = 'on';
			}
			//alert(valora);
			$.post("agendaMedicos.php",
				{
					wemp_pmla:      $('#empresa').val(),               
					consultaAjax:   '',
					id:          	id,
					accion:         'actualizar',
					est: 		valora	
				}			
				,function(data) {
							
					if(data.error == 1)
					{
						
					}
					else
					{
						//alert("Cambio realizado con exito"); // update Ok.
					}
			});
			
			
		}
		
		
		
		
		function cancela(id_radio1, id)
		{	
		     
			var valorc = $('[name="'+id_radio1+'"]:checked').val();
			if (valorc)
			{ 
				valorc = 'I';
			}
			
			mes_confirm = 'Confirma que desea cancelar la cita?';
			if(confirm(mes_confirm))
			{ 
			
				//$.blockUI({ message: $('#causa_cancelacion') });

				$.blockUI({ message:  $('#causa_cancelacion'),
                        css: {  left:   '20%',
                            top:    '20%',
                            width:  '60%',
                            height: 'auto'
                        }
                    });
				
				idRadio = id;
				accion = 'cancelar';
				est = valorc;
				func = respuestaAjaxCancela;
				
				//Busco el select de causa para el div correspondiente
				var contenedorCancela = document.getElementById( "causa_cancelacion" );
				
				campoCancela = document.getElementById( "causa_cancelacion" ).getElementsByTagName( "select" );
			
				
				
			}
			else
			{
				$("#"+id_radio1).removeAttr("checked");
				//return false; 
			}
		}
		
		
		function llamarAjax()
		{
			
			
			//Busco el select de causa para el div correspondiente
			// var contenedorCancela = document.getElementById( "causa_cancelacion" );
			
			// var campoCancela = document.getElementById( "causa_cancelacion" ).getElementsByTagName( "select" );;
			
			//Asigno el valor seleccionado de la causa
			tipo = campoCancela[0].options[ campoCancela[0].selectedIndex ].text;
			
			if( idRadio != '' && accion != ''  && est != '' && func != '' && tipo !='' ){
			
				$.post("agendaMedicos.php",
						{
							wemp_pmla:      $('#empresa').val(),               
							consultaAjax:   '',
							id:          	idRadio,
							accion:         accion,
							est: 			est,
							caso:			$('#caso').val(),
							causa:			tipo
						}			
						,func           
					);
			}
			
			idRadio = '';
			accion = '';
			est = '';
			func = '';
			campoCancela = '';
			
			// var contenedorCancela = document.getElementById( "causa_cancelacion" );
				
			// var campoCancela = document.getElementById( "causa_cancelacion" ).getElementsByTagName( "select" );;
			
			campoCancela.selectedIndex = 0;
		}
		
		function respuestaAjaxCancela(data){
							
			if(data.error == 1)
			{
				alert("No se pudo realizar la cancelacion");
			}
			else
			{						
				alert("Cita Cancelada"); // update Ok.
				document.location.reload(true);
			}
		}   
		

        //funcion para que saque un mensaje cuando se intente ingresar una cita a una fecha anterior a la actual.
		function estado(fechaAct, wfec) 
		{
			if (wfec < fechaAct)
			{
				alert("Esta intentando asignar una cita con una fecha pasada a la fecha actual");
				$(':checkbox').attr('disabled',true);
				$('.desactivar').click(function () {return false;});

			}
			
			
		}		

		
		//-->
	</script>
	<style type="text/css">
	.div_error 
	{   width:500px; 
		text-align: center; 
		align:center; 
		border: 2px solid orange; 
		background:#FFFFCC;
	    font-size:15px;
	    /*height:40px;*/
	    width:400px;	
	    background-color:lightyellow;
		color:red;
		/*para Firefox*/
	    -moz-border-radius: 15px 15px 15px 15px;
	   /*para Safari y Chrome*/
	   -webkit-border-radius: 15px 15px 15px 15px;
	   /* para Opera */
	   border-radius: 15px 15px 15px 15px;
	   font-family: verdana;
	   font-weight:bold;
	   
	}

</style>
</head>
<?php
/*
Modificacion:
2020-09-09 Edwin Molina
		   Se hacen cambios varios para recibir los datos por defecto que quedaran en la cita y vienen de la lista de espera para Drive Thru
2020-04-01 Arleyda Insignares Ceballos
           El llamado del presente script queda desde el iframe de calendar.php adicionalmente se coloca el link retornar en la parte superior.
2013-11-26 Se hace la modificacion para mostrar las observaciones creadas para cada medico desde el programa de excepciones. Viviana Rodas
2013-09-03 Se agrega order by a las consultas de excepciones, estructura y horarios de los medicos
2013-04-05 Se modifica el programa sacando del while externo que imprime la agenda, la variable wk porque puede traer horario de cualquiera de las tablas, 
		   ya sea de la citas_000012, citas_000014 o de la citas_000010
2013-04-01 Se modifica el programa para que solo se consulte una vez las citas para la fecha seleccionada, sacando la consulta a la tabla 000009 del
			while externo. Viviana Rodas
2013-03-27 Se modifica el programa para que liste el horario del medico cuando atienda por la mañana y/o por la tarde
2013-02-28 Se organiza la consulta consulta de la accion cancela, se cambia el estado A por on y se cierra el select que lista las causas. Viviana Rodas
2012-11-25 Se organiza la funcion cancela ya que estaba sacando error y no guardaba la causa de la cancelacion bien.
2012-11-08 Se agrega la funcion de causas para guardar esta, cuando el paciente cancela la cita.
2012-08-03 Este script se utiliza para hacer la asignacion de citas del caso 2 que son las citas de medicos, se muestra la agenda de
 un medico seleccionado en la lista de medicos, con sus correspondiente horario, se agregan las columnas asiste y cancela para que se puedan marcar
 asistida y cancela directamente en la agenda sin tener que entrar en el editar de cada cita para cancelarla o marcar el asiste, se hace la validacion 
 que solo se puedan asignar citas de la fecha actual o posterior si se intenta lo contrario sale un mensaje diciendo que la fecha es inferior a la 
 fecha actual y solo permite visualizacion de la cita, se desactivan las columnas asiste, cancela y la columna editar no se muestra, tambien cuando 
 se le da cancelar no se borra el registro se le cambio el estado a incativo. Viviana Rodas
*/

echo "<center>";
if (!isset($wfec))
{
	$wfec=date("Y-m-d");
}

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

//funciones
function causas($tipo)
	{
		global $conex;
		$sql5="select Caucod, Caudes, Cautip, Cauest  from root_000086 where Cautip = '".$tipo."' and Cauest ='on'   group by Caudes";
		  //$sql5="select Caucod, Caudes, Cautip, Cauest from root_000086 where Cautip = 'cancelacion' and Cauest ='A' group by Caudes";
				$res5 = mysql_query( $sql5, $conex ) or die( mysql_errno()." - Error en el query $sql5 - ".mysql_error() );
				
				echo "<table>";
				echo "<tr class='encabezadotabla'  align=center>";
				echo "<td width='100%' colspan='2'>Seleccione la causa:</td>";
				echo "</tr> ";
				
				echo "<tr>";
				echo "<td align=center><select name='causa' onchange='javascript: llamarAjax();'>";
				echo "<option></option>";
				
				for( $i = 0; $rows5 = mysql_fetch_array( $res5 ); $i++ )
				{ 
					
					if( $causa != trim( $rows5['Caucod'] )." - ".trim( $rows5['Caudes'] ) )
					{
						echo "<option>".$rows5['Caucod']." - ".$rows5['Caudes']."</option>";
					}
					else
					{
						echo "<option selected>".$rows5['Caucod']." - ".$rows5['Caudes']."</option>";
					}
					
					
					
				}
				echo "</select>";
				echo "</td></tr>";
				echo "</table>";
				
				//return;
	}	
//fin funciones

// session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{

echo "
			<input type='hidden' id='empresa' name='empresa' value='".$empresa."'>
			<input type='hidden' id='wfec' name='wfec' value='".$wfec."'>
			<input type='hidden' id='wequ' name='wequ' value='".$wequ."'>
			<input type='hidden' id='nomdia' name='nomdia' value='".@$nomdia."'>
			<input type='hidden' id='colorDiaAnt' name='colorDiaAnt' value='".$colorDiaAnt."'>
			<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>
			<input type='hidden' id='caso' name='caso' value='".$caso."'>
			
		";	

 $fechaAct=date("Y-m-d");
echo "<BODY TEXT='#000066' onLoad='javascript:estado(\"".$fechaAct."\",\"".$wfec."\")'>";	
echo "<table border=0 align=center>";
echo "<tr><td align=center bgcolor='#cccccc'><A NAME='Arriba'><font size=6><b>Citas Medicos</b></font></a></tr></td>";
echo "<tr><td align=center bgcolor='#cccccc'><font size=2> <b> agendaMedicos.php Ver. 2013-02-28</b></font></tr></td></table>";
echo "</center>";
echo "<form name='citas' action='agendaMedicos.php' method=post>";



	$key = substr($user,2,strlen($user));
	

	

	//echo "<input type='HIDDEN' name= 'empresa' id= 'empresa' value='".$empresa."'>";
	
	if(isset($wsw1))
		echo "<input type='HIDDEN' name= 'wsw1' value='".$wsw1."'>";
	
		
	echo "<table align='center'>";
	echo "<tr>";
	echo "<td bgcolor='#cccccc'><b><h3>Medico: ".$wequ."</h3></b></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td align='center'><b><h3>Fecha: ".$wfec."</h3></b></td>";
	echo "</tr>";
	echo "</table>";
	
	echo "<tr><td><center><b><A HREF='dispMedicos.php?wemp_pmla=".@$wemp_pmla."&wbasedato=$empresa&consultaAjax=10&wfec=$wfec&wsw=".@$wsw."&colorDiaAnt=$colorDiaAnt&caso=$caso&nomdia=".@$nomdia.$parametrosExtras."'>Retornar</A></b><br></center></td></tr>";

	echo "<center><div class='div_error' style='display:none;'><div id='observacion' style='margin:15px;' align='center'></div></div></center><br>";

	
	if(isset($wequ))
	{  
		if ($wfec > date("Y-m-d"))
		{
		//antes de insertar en estructura se borra por si trae modificaciones del horario
			$query1="Delete From ".$empresa."_000014 Where Fecha='".$wfec."' and Codigo='".substr($wequ,0,strpos($wequ,"-"))."'";
			$err3 = mysql_query($query1,$conex) or die(mysql_errno()." error en el query $query1:".mysql_error());
		}
		//pregunta si tiene excepciones
	    $query = "select Uni_hora, Hi, Hf, Consultorio  from ".$empresa."_000012 ";
		$query .= "  where Fecha_I <= '".$wfec."'";
		$query .= "       and Fecha_F >= '".$wfec."'";
		$query .= "       and Codigo = '".substr($wequ,0,strpos($wequ,"-"))."'";
		$query .= " and Uni_hora != 1
				    and Codigo != 'Todos'";
    	$query .= "       and Activo = 'A' order by Hi "; //se agrego order by
		$err2 = mysql_query($query,$conex);	
		$num = mysql_num_rows($err2);
		$wk=0;
		if ($num > 0) //si tiene excepciones
		{
			$wk=1;
			$row = mysql_fetch_array($err2); 
		}
		if($num == 0)  //no tiene excepciones, consulta si tiene extructura
		{
			$query = "select Uni_hora, Hi, Hf, Consultorio  from ".$empresa."_000014 ";
			$query .= "  where Codigo = '".substr($wequ,0,strpos($wequ,"-"))."'";
			$query .= "       and Fecha = '".$wfec."' order by Hi";//se agrego order by
		   // $query .= "  Group by Codigo ";
			$err2 = mysql_query($query,$conex);	
			$num = mysql_num_rows($err2);
			if( $num == 0)
			{    //no tiene extructura consulto el horario del medico
				$wk=2;
				$query = "select Uni_hora, Hi, Hf, Consultorio  from ".$empresa."_000010 ";
				$query .= "  where Codigo = '".substr($wequ,0,strpos($wequ,"-"))."'";
				$query .= "       and Dia = ".$nomdia;
				$query .= "       and Activo = 'A' order by Hi";//se agrego order by
			    //$query .= "  Group by Codigo "; //si tiene varios solo toma el primero
				$err2 = mysql_query($query,$conex);	
				$num = mysql_num_rows($err2);
			}
		}
		if($wk == 1 and $row[0] == 0){ //tiene excepcion y la unidad de hora es 0
			$num=0;
		}
		elseif( $wk == 1 )
		{
			mysql_data_seek( $err2, 0 );
		}
		if ($num > 0) //atiende ese dia ya sea de la tabla 14 o de la tabla 10
		{

			$color="#999999";
			echo "<table border=0 align=center>";
			echo "<tr><td bgcolor=".$color." colspan=11 align=center><font size=3><b>CONSULTORIO : ".$row[3]."</b></font></td></tr>";
			echo "<tr><td bgcolor=".$color."><font size=2><b>Hora Inicio</b></font></td>";
			echo "<td bgcolor=".$color."><font size=2><b>Hora Final</b></font></td>"; 
			echo "<td bgcolor=".$color."><font size=2><b>Examen</b></font></td>";
			echo "<td bgcolor=".$color."><font size=2><b>Paciente</b></font></td>";	
			echo "<td bgcolor=".$color."><font size=2><b>Responsable</b></font></td>";	
			echo "<td bgcolor=".$color."><font size=2><b>Telefono</b></font></td>";
			echo "<td bgcolor=".$color."><font size=2><b>Edad</b></font></td>";
			echo "<td bgcolor=".$color."><font size=2><b>Estado</b></font></td>";
			echo "<td bgcolor=".$color."><font size=2><b>Asiste</b></font></td>";
			echo "<td bgcolor=".$color."><font size=2><b>Cancelar</b></font></td>"; 
			
			if ($wfec >= $fechaAct)
			{
				echo "<td bgcolor=".$color."><font size=2><b>Seleccion</b></font></td>"; 
			}
			
			echo "</tr>";
			//se coloca consulta a la tabla de citas
			//se agrego a la consulta que el estado sea activo = 'A'
			$query = "select cod_equ,cod_exa,fecha,hi,hf,nom_pac,nit_res,telefono,edad,comentario,usuario,activo,Asistida,id from ".$empresa."_000009 where fecha='".$wfec."' and cod_equ='".substr($wequ,0,strpos($wequ,"-"))."' and Activo='A' order by hi";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num > 0)
				$row = mysql_fetch_array($err); //( $wk==0 or $wk==2 ) and
			while(  ( $row1 = mysql_fetch_array($err2) ) ){ //si tiene excepcion o atiende ese dia segun la tabla 10 o tiene estructura
			
				if($wk==2)  //si tiene horario segun la tabla 10, inserta en extructura
				{
					$fecha = date("Y-m-d");
					$hora = (string)date("H:i:s");
					
					
					$query = "insert ".$empresa."_000014 (medico,fecha_data,hora_data, Codigo, Fecha, Uni_hora, Hi, Hf, Consultorio ,seguridad) values ('".$empresa."','".$fecha."','".$hora."','".substr($wequ,0,strpos($wequ,"-"))."','".$wfec."',".$row1[0].",'".$row1[1]."','".$row1[2]."','".$row1[3]."','C-".$empresa."')";
					$err3 = mysql_query($query,$conex) or die(mysql_errno()."error en el query $query:".mysql_error());
				} 
				$whi = $row1[1]; //hora inicial que viene de la tabla 12 o de la 10 
				$wul = $row1[2]; //hora final que viene de la tabla 12 o de la 10
				$inc = $row1[0]; //unidad de hora que viene de la tabla 12 o de la 10
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
				
 			
				$r = 0;
				$i = 0;
				$fila = 0;  
				 
				
				while ($whi < $wul)
				{ 
					$r = $i/2;
					if ($r*2 === $i)
						$color="#CCCCCC";
					else
						$color="#999999";
					if(strlen($row[0]) == 1 and $row[0] == "0"and $num > 0 and $row[3] == $whi)
							$color="#99ccff";
					echo "<tr>";
					if(substr($whi,0,2) > "12")
					{
						$hr1 ="". (string)((integer)substr($whi,0,2) - 12).":".substr($whi,2,2). " pm ";
						echo "<td bgcolor=".$color." align=center><font size=2>".$hr1."</font></td>";
					}
					else
						echo "<td bgcolor=".$color." align=center><font size=2>".substr($whi,0,2).":".substr($whi,2,2)."</font></td>";
					if ($num > 0 and $row[3] == $whi)
						$whf=$row[4];
					if(substr($whf,0,2) > "12")
					{
						$hr2 ="". (string)((integer)substr($whf,0,2) - 12).":".substr($whf,2,2). " pm ";
						echo "<td bgcolor=".$color." align=center><font size=2>".$hr2."</font></td>";
					}
					else
						echo "<td bgcolor=".$color." align=center><font size=2>".substr($whf,0,2).":".substr($whf,2,2)."</font></td>";
					
					if ($num > 0 and $row[3] == $whi)
					{
						$query = "select codigo,descripcion,preparacion,cod_equipo,activo from ".$empresa."_000011 where codigo='".$row[1]."'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						$row2 = mysql_fetch_array($err1);
						echo "<td bgcolor=".$color."><font size=2>".$row2[1]."</font></td>";
						echo "<td bgcolor=".$color."><font size=2>".$row[5]."</font></td>";
						$query = "select nit,descripcion from ".$empresa."_000002 where nit='".$row[6]."'";
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						$row3 = mysql_fetch_array($err1);
						echo "<td bgcolor=".$color."><font size=2>".$row3[0]."-".$row3[1]."</font></td>";
						echo "<td bgcolor=".$color."><font size=2>".$row[7]."</font></td>";
						echo "<td bgcolor=".$color."><font size=2>".$row[8]."</font></td>";
						switch ($row[11])
						{
							case "A":
								if($row[12] == "on")
									echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/asistida1.gif' ></td>";
								else
									echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/activo.gif' ></td>";
							break;
							case "I":
								echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/inactivo.gif' ></td>";
							break;
							default:
								echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/indefinido.gif' ></td>";
							break;
						}
						//echo $row[13];
						
						if ($row[12]=="on")
						{
						//se agrega el checkbox para asiste
						echo "<td bgcolor=".$color." align='center'><input type='checkbox' name='chkasiste".$row[13]."' id='chkasiste".$row[13]."' onclick='asiste(\"chkasiste".$row[13]."\",\"".$row[13]."\")' checked></td>";
						}
						else
						{
							//se agrega el checkbox para asiste
						echo "<td bgcolor=".$color." align='center'><input type='checkbox' name='chkasiste".$row[13]."' id='chkasiste".$row[13]."' onclick='asiste(\"chkasiste".$row[13]."\",\"".$row[13]."\")'></td>";
						}
						
						//*******************************
						
						//se agrega el checkbox para cancelar
						echo "<td bgcolor=".$color." align='center'><input type='checkbox' name='chkcancela".$row[13]."' id='chkcancela".$row[13]."' onclick='cancela(\"chkcancela".$row[13]."\",\"".$row[13]."\")' value='I'=></td>";
						
											
						if(isset($wsw1))
						{   
							if ($wfec >= $fechaAct)
							{
								echo "<td bgcolor=".$color." align=center><font size=2><A HREF='asignacionCitaMed.php?pos2=".$wequ."&amp;pos3=".$row2[0]."&amp;pos4=".$wfec."&amp;pos5=".$row[3]."&amp;pos6=".$row[4]."&amp;pos7=".$wul."&amp;pos8=".$row[8]."&amp;pos9=".$inc."&amp;empresa=".$empresa."&amp;wsw1=".$wsw1."&amp;colorDiaAnt=".$colorDiaAnt."&amp;wbasedato=clisur&wemp_pmla=".$wemp_pmla."&caso=".$caso."&nomdia=$nomdia' class='desactivar'>Editar</font></td>";
							}
						}
						else
						{
							if ($wfec >= $fechaAct)
							{
								echo "<td bgcolor=".$color." align=center><font size=2><A HREF='asignacionCitaMed.php?pos2=".$wequ."&amp;pos3=".$row2[0]."&amp;pos4=".$wfec."&amp;pos5=".$row[3]."&amp;pos6=".$row[4]."&amp;pos7=".$wul."&amp;pos8=".$row[8]."&amp;pos9=".$inc."&amp;colorDiaAnt=".$colorDiaAnt."&amp;empresa=".$empresa."&amp;wbasedato=clisur&wemp_pmla=".$wemp_pmla."&caso=".$caso."&nomdia=$nomdia' class='desactivar'>Editar</font></td></tr>";
							}
						}
						$row = mysql_fetch_array($err); //para que pase al segundo registro
						$fila = $fila + 1;
					}
					else
					{
						$wsw=0;
						echo "<td bgcolor=".$color."></td>";
						echo "<td bgcolor=".$color."></td>";
						echo "<td bgcolor=".$color."></td>";
						echo "<td bgcolor=".$color."></td>";
						echo "<td bgcolor=".$color."></td>";
						echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/indefinido.gif' ></td>";
						//***************
						
						//para que salgan los checkbox de las citas en blanco
						if (@$row[12]=="on")
						{
						//se agrega el checkbox para asiste
						echo "<td bgcolor=".$color." align='center'><input type='checkbox' name='chkasiste' id='chkasiste' onclick='asiste(this,\"".'--'."\")'></td>";
						}
						else
						{
							//se agrega el checkbox para asiste
						echo "<td bgcolor=".$color." align='center'><input type='checkbox' name='chkasiste' id='chkasiste' onclick='asiste(this,\"".'--'."\")'></td>";
						 }
						
						//************************
						//se agrega el checkbox para cancelar
						echo "<td bgcolor=".$color." align='center'><input type='checkbox' name='chkcancela".'--'."' id='chkcancela".'--'."' onclick='cancela(this,\"".'--'."\")' ></td>";
						
						if(isset($wsw1))
						{   
							if ($wfec >= $fechaAct)
							{
								echo "<td bgcolor=".$color." align=center><font size=2><A HREF='asignacionCitaMed.php?pos2=".$wequ."&amp;pos3=0&amp;pos4=".$wfec."&amp;pos5=".$whi."&amp;pos6=".$whf."&amp;pos7=".$wul."&amp;pos8=0&amp;pos9=".$inc."&amp;empresa=".$empresa."&amp;colorDiaAnt=".$colorDiaAnt."&amp;wsw1=".$wsw1."&wemp_pmla=".$wemp_pmla."&caso=".$caso."&nomdia=$nomdia{$parametrosExtras}' class='desactivar'>Editar</font></td></tr>";
							}
						}
						else
						{
							if ($wfec >= $fechaAct)
							{
								echo "<td bgcolor=".$color." align=center><font size=2><A HREF='asignacionCitaMed.php?pos2=".$wequ."&amp;pos3=0&amp;pos4=".$wfec."&amp;pos5=".$whi."&amp;pos6=".$whf."&amp;pos7=".$wul."&amp;pos8=0&amp;pos9=".$inc."&amp;colorDiaAnt=".$colorDiaAnt."&amp;empresa=".$empresa."&wemp_pmla=".$wemp_pmla."&caso=".$caso."&nomdia=".@$nomdia."{$parametrosExtras}' class='desactivar'>Editar</font></td></tr>";
							}
						}
							 
					}
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
					$i = $i + 1;
				}	
			}	//while
			echo "</table>";
		}
		// else
		// {
			// echo "<center><table border=0 aling=center>";
			// echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
			// echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#00ffff LOOP=-1>EL MEDICO NO ESTA DISPONIBLE ESE DIA DE LA SEMANA -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
			// echo "<br><br>";
		// }
	}
	echo "<table border=0 align=center>";
	echo "<tr><td align='center'><A HREF='#Arriba'><B>Arriba</B></A></td></tr>";
	echo "<br>";
	echo "<tr><td><center><b><A HREF='dispMedicos.php?wemp_pmla=".@$wemp_pmla."&wbasedato=$empresa&consultaAjax=10&wfec=$wfec&wsw=".@$wsw."&colorDiaAnt=$colorDiaAnt&caso=$caso&nomdia=".@$nomdia.$parametrosExtras."'>Retornar</A></b><br></center></td></tr>";
	echo "<tr><td align='center'><br><br><input name='button' type='button' style='width:100' onclick='window.close();' value='Cerrar' /></td></tr>";
	echo "</table>";
	
	//div causa cancelacion
	echo "<div id='causa_cancelacion' style='display:none;width:200px;'>";
	//div para sacar las causas	
	echo "<center>";
	$tipo = "C";
	causas($tipo);
	
	echo "<br><input type='button' value='Cerrar' style='width:100' onclick='$.unblockUI();'><br>";
	
	echo "</center>";
	//echo $cau;
	echo "</div>";
	//include_once("free.php");
}
?>
</body>
</html>