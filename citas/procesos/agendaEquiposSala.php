<?php
include_once("conex.php");
if (isset($accion) and $accion == 'actualizar')
 { //$fp = fopen('verquery.txt',"w+");
	// fwrite($fp, $est);
	// fclose($fp); 
	

	

	
	$sql = "UPDATE
						".$wemp_pmla."_000001
					SET
						Asistida = '".$est."',
						Atendido = '".$est."'
						
					WHERE
						id = '$id'";
			 
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	return;
}
else if (isset($accion) and $accion == 'cancelar')
   {

   // $fp = fopen('verquery.txt',"w+");
	 // fwrite($fp, $est);
	 // fclose($fp); 
	

	

	
	
	 $sql = "UPDATE
						".$wemp_pmla."_000001
					SET
						Activo = '".$est."',
						Causa = '".$causa."'
						
						
					WHERE
						id = '$id'";
			 
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	//Causa = '".$causa."'
	return;
}

if(isset($accion) and $accion == 'consultaObsercaciones')
{
			$data= array( 'error'=>0, 'mensaje'=>'');
			
			

	        

			
			$wequ=explode("-",$wequ);
			$wequ=$wequ[0];
			// echo $empresa."-".$wfec."-".$wequ;
			
			$query = "select Fecha_I, Fecha_F, Codigo, Control  
						  from ".$empresa."_000021 
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
<title>Asignacion de citas</title>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.accordion.js"></script>         
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<link type="text/css" href="../../../include/root/ui.all.css" rel="stylesheet" />        <!-- Nucleo jquery -->




<?php
	if(isset($wequ))
		echo "<title>MATRIX - ".$wequ."</title>";
	else
		echo "<title>MATRIX</title>";
?>


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
	
	
	$.post("agendaEquiposSala.php",
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
				//$("#observacion").parent().css("display", "none");
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
			$.post("agendaEquiposSala.php",
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
						document.location.reload(true);
					}
			});
			
			/*$.ajax({
			  type: "POST",
			  url: "000001_prx5.php",
			  data: { 	wemp_pmla:      $('#empresa').val(),               
						consultaAjax:   '',
						id:          	id,
						accion:         'actualizar',
						est: 			valora	 
					}
			}).done(function( msg ) {
			  //alert( "Data Saved: " + msg );
			});*/
		}
		
		// function cancela(id_checkbox1, id)
		// {	
			// var valorc = 'A';
			// if ($("#"+id_checkbox1).is(':checked'))
			// { 
			
				// valorc = 'I';
				
				// mes_confirm = 'Confirma que desea cancelar la cita?';
				// if(confirm(mes_confirm))
				// { 
				
					// $.post("agendaEquiposSala.php",
						// {
							// wemp_pmla:      $('#empresa').val(),               
							// consultaAjax:   '',
							// id:          	id,
							// accion:         'cancelar',
							// est: 		valorc
							
						// }			
						// ,function(data) {
									
							// if(data.error == 1)
							// {
								
							// }
							// else
							// {						
								// alert("Cita Cancelada"); // update Ok.
								// document.location.reload(true);
							// }
						// }           
					// );
				// }
				// else
				// {
					// $("#"+id_checkbox1).removeAttr("checked");
					/*return false; */
				// }
			// }		
		// }
		
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
			
				$.blockUI({ message: $('#causa_cancelacion') });
				
				idRadio = id;
				accion = 'cancelar';
				est = valorc;
				func = respuestaAjaxCancela;
				
				//Busco el select de causa para el div correspondiente
				var contenedorCancela = document.getElementById( "causa_cancelacion" );
				
				campoCancela = document.getElementById( "causa_cancelacion" ).getElementsByTagName( "select" );
				
				
				
				// $.post("pantallaAdmision.php",
					// {
						// wemp_pmla:      $('#solucionCitas').val(),               
						// consultaAjax:   '',
						// id:          	id,
						// accion:         'cancelar',
						// est: 			valorc,
						// caso:			$('#caso').val()
						
					// }			
					// ,function(data) {
								
						// if(data.error == 1)
						// {
							// alert("No se pudo realizar la cancelacion");
						// }
						// else
						// {						
							// alert("Cita Cancelada"); // update Ok.
							// document.location.reload(true);
						// }
					// }           
				// );
			}
			else
			{
				$("#"+id_radio1).removeAttr("checked");
				//return false; 
			}
		}
		
		
		function llamarAjax()
		{
			//alert("llamarajax "+idRadio+" est "+est+" causa "+tipo);
			//alert();
			
			//Busco el select de causa para el div correspondiente $('#caso').val()
			// var contenedorCancela = document.getElementById( "causa_cancelacion" );
			
			// var campoCancela = document.getElementById( "causa_cancelacion" ).getElementsByTagName( "select" );;
			
			//Asigno el valor seleccionado de la causa
			tipo = campoCancela[0].options[ campoCancela[0].selectedIndex ].text;
			
			
			if( idRadio != '' && accion != ''  && est != '' && func != '' && tipo !='' ){
			
				$.post("agendaEquiposSala.php",
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
				alert("Esta intentando asignar una Hora Sala con una fecha pasada a la fecha actual");
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
 2013-11-26 Se hace la modificacion para mostrar las observaciones creadas para cada equipo desde el programa de excepciones. Viviana Rodas
 2013-07-03  Se hace la modificacion del script para que se muestre el horario cuando tiene varias excepciones para un dia Viviana Rodas 
 2012-11-27  Se hace la modificacion para que las consultas que haga a la tabla de citas citas.._000001 se haga siempre sobre los activos, tambien se
 hace la validacion para que pregunte primero si tiene excepciones en la tabla citas.._000021.
 2012-11-09 Se agrega la funcion causas para que haga la consulta a la tabla root_000086 para que cuando el paciente cancele la cita, o no asista
 se guarde la causa por la cual cancelo o no asistio.
 2012-10-19. Este script se unifica con el script 000001_prx7 por su funcionamiento similar, se agregan las columnas asiste y cancelar
 para que la persona que asigna las citas pueda cancelar y marcar asiste ditectamente en la agenda sin entrar en el editar de cada cita, cuando se 
 intenta ingresar en la agenda con una fecha menor a la fecha actual se muestra un mensaje que esta intentando asignar citas a una fecha pasada a la
 actual, se permite la visualizacion de las citas pero no se permite ninguna funcionalidad ya que se desactivan los campos de asiste y cancela y la
 columna editar no se muestra, 
tambien cuando se le da cancelar no se borra el registro se le cambio el estado a incativo. Viviana Rodas.
*/
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
				echo "<td align=center><select id='causa' name='causa' onchange='javascript: llamarAjax();'>";
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

	echo "<center>";
	if (!isset($wfec))
		{
			$wfec=date("Y-m-d");
		}

	$fechaAct=date("Y-m-d");
	
	echo "<BODY TEXT='#000066' onLoad='javascript:estado(\"".$fechaAct."\",\"".$wfec."\")'>";


	echo "<table border=0 align=center>";
	echo "<tr><td align=center bgcolor='#cccccc'><A NAME='Arriba'><font size=6><b>Turnos x Sala</b></font></a></tr></td>";
	echo "<tr><td align=center bgcolor='#cccccc'><font size=2> <b> agendaEquiposSala.php Ver. 2013-07-03</b></font></tr></td></table>";
	echo "</center>";
	echo "<form  name='citas' action='agendaEquiposSala.php' method=post>";
	
	$key = substr($user,2,strlen($user));
	

	

	echo "<input type='HIDDEN' name= 'empresa' id='empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'wsw' id= 'wsw' value='".$wsw."'>";
	echo "<input type='HIDDEN' name= 'caso' id= 'caso' value='".$caso."'>";
	echo "<input type='HIDDEN' name= 'wemp_pmla' id= 'wemp_pmla' value='".@$wemp_pmla."'>";
	echo "<input type='HIDDEN' name= 'wequ' id= 'wequ' value='".$wequ."'>";
	echo "<input type='HIDDEN' name= 'wfec' id= 'wfec' value='".$wfec."'>";
	$query = "select prioridad from usuarios where codigo = '".substr($user,2,strlen($user))."'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	$row = mysql_fetch_array($err);
	$prioridad = $row[0];
			
	echo "<table align='center'>";
	echo "<tr>";
	echo "<td bgcolor='#cccccc'><b><h3>Sala: ".$wequ."</h3></b></td>";
	echo "</tr>";
	
	$dia=date("l", strtotime( $wfec ) );
	$diaNum=date("d",strtotime( $wfec ));
	$mes=date("F",strtotime( $wfec ));
	$anio=date("Y",strtotime( $wfec ));
	
	// Obtenemos y traducimos el nombre del día
	if ($dia=="Monday") $dia="Lunes";
	if ($dia=="Tuesday") $dia="Martes";
	if ($dia=="Wednesday") $dia="Miércoles";
	if ($dia=="Thursday") $dia="Jueves";
	if ($dia=="Friday") $dia="Viernes";
	if ($dia=="Saturday") $dia="Sabado";
	if ($dia=="Sunday") $dia="Domingo";

	// Obtenemos y traducimos el nombre del mes
	if ($mes=="January") $mes="Enero";
	if ($mes=="February") $mes="Febrero";
	if ($mes=="March") $mes="Marzo";
	if ($mes=="April") $mes="Abril";
	if ($mes=="May") $mes="Mayo";
	if ($mes=="June") $mes="Junio";
	if ($mes=="July") $mes="Julio";
	if ($mes=="August") $mes="Agosto";
	if ($mes=="September") $mes="Septiembre";
	if ($mes=="October") $mes="Octubre";
	if ($mes=="November") $mes="Noviembre";
	if ($mes=="December") $mes="Diciembre";
	echo "<tr>";
	echo "<td align='center'><b><h3>Fecha: ".$dia." ".$diaNum." de ".$mes ." de ".$anio."</h3></b></td>";
	echo "</tr>";
	echo "</table>";
	
	echo"<br>";
	echo "<center><div class='div_error' style='display:none;'><div id='observacion' style='margin:15px;' align='center'></div></div></center><br>";
	
	$fecha=explode("-",$wfec);
	$Ano=$fecha[0];
	$Mes=$fecha[1];
	$Dia=$fecha[2];
			
			

			//COMIENZA LA UNION
			
		if( $caso == 3){
			$query = "select * from ".$empresa."_000009 where Ano = ".$Ano." and Mes=".$Mes." and Dia=".$Dia;
			$err = mysql_query($query,$conex);	
			$num = mysql_num_rows($err);
		}

		
	if ( ( $caso == 3 && $num == 0 ) || $caso == 1 )
	{
		if(isset($wequ))
		{
				$wtit=substr($wequ,strpos($wequ,"-")+1);
				echo "<input type='HIDDEN' name= 'wfec' value='".$wfec."'>";
				//echo "<input type='HIDDEN' name= 'wequ' value='".$wequ."'>";
				echo "<input type='HIDDEN' name= 'wtit' value='".$wtit."'>";
				
				//para definir el horario se consulta primero si tiene excepciones
				$query = "select Codigo, Fecha_I, Uni_hora, Hi, Hf  
						  from ".$empresa."_000021 
						  where Fecha_I <= '".$wfec."'
						  and Fecha_F >= '".$wfec."'
						  and Codigo = '".substr($wequ,0,strpos($wequ,"-"))."'
						  and Activo = 'A'
						  and Uni_hora != 1
						  and Codigo != 'Todos'
						  order by Hi
						  ";
				$err4 = mysql_query($query,$conex);
				$num = mysql_num_rows($err4);
				if ($num == 0)
				{
				
					$query = "select fecha,equipo,Uni_hora,hi,hf from ".$empresa."_000004 where fecha = '".$wfec."' and equipo = '".substr($wequ,0,strpos($wequ,"-"))."'";
					$err4 = mysql_query($query,$conex);	
					$num = mysql_num_rows($err4);
					if ($num == 0)
					{
						//echo "no";
						$query = "select codigo,descripcion,Uni_hora,hi,hf,activo from ".$empresa."_000003 where codigo='".substr($wequ,0,strpos($wequ,"-"))."'";
						$err4 = mysql_query($query,$conex);
						$num = mysql_num_rows($err4);
						// $row = mysql_fetch_array($err);
				
					}
					
				
				} 
				
				
			if( $num > 0 )
			{
				echo "<table border=0 align=center>";
				$color="#999999";
				if($caso==3)
				{
				echo "<tr>";
				echo "<td bgcolor=".$color."><font size=2><b>Hora Inicio</b></font></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Hora Final</b></font></td>"; 
				//echo "<td bgcolor=".$color."><font size=2><b>Administrador</b></font></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Solicitud</b></font></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Codigo</b></font></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Nombre</b></font></td>";	
				//echo "<td bgcolor=".$color."><font size=2><b>C.Costos</b></font></td>";	
				echo "<td bgcolor=".$color."><font size=2><b>Celular</b></font></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Personas</b></font></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Estado</b></font></td>";
			//	echo "<td bgcolor=".$color."><font size=2><b>Asiste</b></font></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Cancelar</b></font></td>";
				if ($wfec >= $fechaAct)
				{
					echo "<td bgcolor=".$color."><font size=2><b>Seleccion</b></font></td>"; 
				}
				
				echo "</tr>";	
				}
				else
				{
				echo "<tr>";
				echo "<td bgcolor=".$color."><font size=2><b>Hora Inicio</b></font></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Hora Final</b></font></td>"; 
				echo "<td bgcolor=".$color."><font size=2><b>Administrador</b></font></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Solicitud</b></font></td>";
				
				echo "<td bgcolor=".$color."><font size=2><b>Nombre</b></font></td>";	
				echo "<td bgcolor=".$color."><font size=2><b>C.Costos</b></font></td>";	
				echo "<td bgcolor=".$color."><font size=2><b>Telefono</b></font></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Personas</b></font></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Estado</b></font></td>";
				echo "<td bgcolor=".$color."><font size=2><b>Asiste</b></font></td>";
			    echo "<td bgcolor=".$color."><font size=2><b>Cancelar</b></font></td>";
				if ($wfec >= $fechaAct)
				{
					echo "<td bgcolor=".$color."><font size=2><b>Seleccion</b></font></td>"; 
				}
				echo "</tr>";	
				}
				
				for( $j = 0; $row1 = mysql_fetch_array($err4); $j++ )
				{  
					if( $row1['Uni_hora'] == 0 ) continue;
							$numdia=date("N",strtotime($wfec));
						
							//se agrega consulta para validar que el medico y el equipo tengan el mismo horario
							$query2 = "select hi, hf from ".$empresa."_000007 ";
							$query2 .= " where ".$empresa."_000007.Ndia = '".$numdia."' ";  //numero del dia
							
							if($caso == 3){
								$query2 .= " and ".$empresa."_000007.equipo = '".substr($wequ,0,strpos($wequ,"-"))."' ";  //codigo equipo
							}
							
							$query2 .= "      and ".$empresa."_000007.activo = 'A' ";
							$query2 .= "  order by ".$empresa."_000007.codigo";
							
							$err2 = mysql_query($query2,$conex)or die( mysql_errno()." - Error en el query $query2 - ".mysql_error() );
							$num2 = mysql_num_rows($err2);
							
							if($num2 > 0 ){
							
								$auxmin = "2400";
								$auxmax = "0";
								
								for( $i = 0; $row2 = mysql_fetch_array($err2); $i++ ){
									
									//Minima hora de atencio del medico
									if( $auxmin > $row2['hi'] ){
										$auxmin = $row2['hi'];
									}
									
									//Maxima hora de atencion del medico
									if( $auxmax < $row2['hf'] ){
										$auxmax = $row2['hf'];
									}
								}
								
								if( $row1[3] < $auxmin ){
									$whi = $auxmin;
								}
								else{
									$whi = $row1[3];
								}
								
								if( $row1[4] > $auxmax ){
									$wul = $auxmax;
								}
								else{
									$wul = $row1[4];
								}
							}
							else{
								$whi = 0;
								$wul = 0;
							}
						
						
						
						$inc = $row1[2];
						
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

						 //se coloca la modificacion hi >= '".$whi."' para que busque las citas a partir de la hora en que vaya en el horario
							    //                0       1      2      3    4  5    6        7        8      9       10        11     12     13      14    15       
							 $query = "select cod_med,cod_equ,cod_exa,fecha,hi,hf,nom_pac,nit_resp,telefono,edad,comentarios,usuario,activo,cedula,Asistida,id from ".$empresa."_000001 where fecha='".$wfec."' and cod_equ='".substr($wequ,0,strpos($wequ,"-"))."' and hi >= '".$whi."'  and Activo='A' order by hi";	
						
						
						$err = mysql_query($query,$conex);
						$num = mysql_num_rows($err);
						if ($num > 0)
						{
							$row = mysql_fetch_array($err);
						}
						
						while ($whi < $wul)
						{ 
							//consulta del horario del medico medico para que solo pinte las filas en las que haya medico y equipo
							
							$query3 = "select ".$empresa."_000007.Codigo from ".$empresa."_000007 ";
							$query3 .= " where ".$empresa."_000007.Ndia = '".$numdia."' ";
							if($caso == 3){
								$query3 .= "      and ".$empresa."_000007.equipo = '".substr($wequ,0,strpos($wequ,"-"))."' ";
							}
							$query3 .= "      and ".$empresa."_000007.hi <= '".$whi."' ";
							$query3 .= "      and ".$empresa."_000007.hf >= '".$whf."' ";
							$query3 .= "      and ".$empresa."_000007.activo = 'A' ";
							$query3 .= "  order by ".$empresa."_000007.codigo";
						
							$err3 = mysql_query($query3,$conex)or die( mysql_errno()." - Error en el query $query3 - ".mysql_error() );
							$num3 = mysql_num_rows($err3);
							
							if ( $num3 >0 )
							{
									
									$r = $i/2;
									if ($r*2 === $i)
										$color="#CCCCCC";
									else
										$color="#999999";
									if(strlen($row[0]) == 1 and $row[0] == "0"and $num > 0 and $row[4] == $whi)
											$color="#99ccff";
									echo "<tr>";
									if(substr($whi,0,2) > "12")
									{
										$hr1 ="". (string)((integer)substr($whi,0,2) - 12).":".substr($whi,2,2). " pm ";
										echo "<td bgcolor=".$color." align=center><font size=2>".$hr1."</font></td>";
									}
									else
										echo "<td bgcolor=".$color." align=center><font size=2>".substr($whi,0,2).":".substr($whi,2,2)."</font></td>";
									if ($num > 0 and $row[4] == $whi and substr($wsw,1,1) == "1")
										$whf=$row[5];
									if(substr($whf,0,2) > "12")
									{
										$hr2 ="". (string)((integer)substr($whf,0,2) - 12).":".substr($whf,2,2). " pm ";
										echo "<td bgcolor=".$color." align=center><font size=2>".$hr2."</font></td>";
									}
									else
										echo "<td bgcolor=".$color." align=center><font size=2>".substr($whf,0,2).":".substr($whf,2,2)."</font></td>";
									if ($num > 0 and $row[4] == $whi)
									{	
										$query = "select codigo,nombre,oficio,tipo,edad_pac,activo from ".$empresa."_000008 where codigo='".$row[0]."'";
										$err1 = mysql_query($query,$conex);
										$num1 = mysql_num_rows($err1);
										$row1 = mysql_fetch_array($err1);	
									//	echo "<td bgcolor=".$color."><font size=2>".$row1[1]."</font></td>";
										$query = "select codigo,descripcion,preparacion,cod_equipo,activo,especial from ".$empresa."_000006 where codigo='".$row[2]."'";
										$err1 = mysql_query($query,$conex);
										$num1 = mysql_num_rows($err1);
										$row2 = mysql_fetch_array($err1);
										echo "<td bgcolor=".$color."><font size=2>".$row2[1]."</font></td>";
										
										if ($caso==3)
										{
											echo "<td bgcolor=".$color."><font size=2>".$row[13]."</font></td>";
										}
										echo "<td bgcolor=".$color."><font size=2>".$row[6]."</font></td>";
										/*$query = "select nit,descripcion from ".$empresa."_000002 where nit='".$row[7]."'";
										$err1 = mysql_query($query,$conex);
										$num1 = mysql_num_rows($err1);
										$row3 = mysql_fetch_array($err1);
										echo "<td bgcolor=".$color."><font size=2>".$row3[0]."-".$row3[1]."</font></td>";*/
										echo "<td bgcolor=".$color."><font size=2>".$row[8]."</font></td>";
										echo "<td bgcolor=".$color."><font size=2>".$row[9]."</font></td>";
										switch ($row[12])
										{
											case "A":

											// if ($caso==3)
											// {
												if($row[14] == "on")
													echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/asistida1.gif' ></td>";
												else
													echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/activo.gif' ></td>";
											// }
											// else
											// {
												// echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/activo.gif' ></td>";
											// }	
												break;
											case "I":
												echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/inactivo.gif' ></td>";
												break;
											default:
												echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/indefinido.gif' ></td>";
												break;
										}
										
											//agregado para el checkbox asiste
										if ($prioridad > 2 or $row['usuario'] == $key)
										{
												/*if ($row[14]=="on")
												{
													//se agrega el checkbox para asiste
													echo "<td bgcolor=".$color." align='center'><input type='checkbox' name='chkasiste".$row[15]."' id='chkasiste".$row[15]."' onclick='asiste(\"chkasiste".$row[15]."\",\"".$row[15]."\")' checked></td>";
												}
												else
												{
													//se agrega el checkbox para asiste
													echo "<td bgcolor=".$color." align='center'><input type='checkbox' name='chkasiste".$row[15]."' id='chkasiste".$row[15]."' onclick='asiste(\"chkasiste".$row[15]."\",\"".$row[15]."\")'></td>";
												}*/
												
												//agregado para el checkbox cancela
												//se agrega el checkbox para cancelar
												echo "<td bgcolor=".$color." align='center'><input type='checkbox' name='chkcancela".$row[15]."' id='chkcancela".$row[15]."' onclick='cancela(\"chkcancela".$row[15]."\",\"".$row[15]."\")' value='I'></td>";
										}		
									
										if ($prioridad > 2 or $row['usuario'] == $key)
										{
												if ($caso==3)
													{	
														if ($wfec >= $fechaAct)
														{
															echo "<td bgcolor=".$color." align=center><font size=2><A HREF='asignacionCitaEquSala.php?pos1=".$row1[0]."&amp;pos2=".substr($wequ,0,strpos($wequ,"-"))."&amp;pos3=".$row2[0]."&amp;pos4=".$wfec."&amp;pos5=".$whi."&amp;pos6=".$whf."&amp;pos7=".$wul."&amp;pos8=".$row[9]."&amp;pos9=".$inc."&amp;empresa=".$empresa."&amp;wsw=".$wsw."&amp;wtit=".$wtit."&amp;colorDiaAnt=".$colorDiaAnt."&wfec=".$wfec."&caso=".$caso."&wemp_pmla=".$wemp_pmla."' class='desactivar'>Editar</font></td>";
														}
													}
												else
													{
														if ($wfec >= $fechaAct)
														{
															echo "<td bgcolor=".$color." align=center><font size=2><A HREF='asignacionCitaEquSala.php?pos1=".$row1[0]."&amp;pos2=".substr($wequ,0,strpos($wequ,"-"))."&amp;pos3=".$row2[0]."&amp;pos4=".$wfec."&amp;pos5=".$whi."&amp;pos6=".$whf."&amp;pos7=".$wul."&amp;pos8=".$row[9]."&amp;pos9=".$inc."&amp;empresa=".$empresa."&amp;wsw=".$wsw."&amp;wtit=".$wtit."&amp;colorDiaAnt=".$colorDiaAnt."&wfec=".$wfec."&caso=".$caso."&wemp_pmla=".$wemp_pmla."' class='desactivar'>Editar</font></td>";
														}
													}
										}
										else
										{
											//echo "<td bgcolor=".$color." align=center><font size=2>Sin Edicion</font></td>";
											echo "<td bgcolor=".$color." align=center><font size=2>Sin Edicion</font></td>";
											echo "<td bgcolor=".$color." align=center><font size=2>Sin Edicion</font></td>";
										}
										echo "</tr>";
										$row = mysql_fetch_array($err);
										$fila = $fila + 1;
									}
									else
									{
										//echo "<td bgcolor=".$color."></td>";
										//echo "<td bgcolor=".$color."></td>";
										echo "<td bgcolor=".$color."></td>";
										echo "<td bgcolor=".$color."></td>";
										echo "<td bgcolor=".$color."></td>";
										echo "<td bgcolor=".$color."></td>";
										if ($caso==3)
										{
										echo "<td bgcolor=".$color."></td>";
										}

										echo "<td bgcolor=".$color." align=center><IMG SRC='/matrix/images/medical/root/indefinido.gif' ></td>";
										
										//para que salgan los checkbox de las citas en blanco
										/*if (@$row[14]=="on")
										{
											//se agrega el checkbox para asiste
											echo "<td bgcolor=".$color." align='center'><input type='checkbox' name='chkasiste' id='chkasiste' onclick='asiste(this,\"".'--'."\")'></td>";
										}
										else
										{
											//se agrega el checkbox para asiste
											echo "<td bgcolor=".$color." align='center'><input type='checkbox' name='chkasiste' id='chkasiste' onclick='asiste(this,\"".'--'."\")'></td>";
										 }*/
										
										//************************
										//se agrega el checkbox para cancelar
										echo "<td bgcolor=".$color." align='center'><input type='checkbox' name='chkcancela".'--'."' id='chkcancela".'--'."' onclick='cancela(this,\"".'--'."\")' ></td>";
										
										if ($prioridad > 0)
										{
												if ($caso==3)
												{
													if ($wfec >= $fechaAct)
													{
														echo "<td bgcolor=".$color." align=center><font size=2><A HREF='asignacionCitaEquSala.php?pos1=0&amp;pos2=".substr($wequ,0,strpos($wequ,"-"))."&amp;pos3=0&amp;pos4=".$wfec."&amp;pos5=".$whi."&amp;pos6=".$whf."&amp;pos7=".$wul."&amp;pos8=0&&amp;pos9=".$inc."&amp;empresa=".$empresa."&amp;wsw=".$wsw."&amp;wtit=".$wtit."&amp;colorDiaAnt=".$colorDiaAnt."&wfec=".$wfec."&caso=".$caso."&wemp_pmla=".$wemp_pmla."' class='desactivar'>Editar</font></td>";
													}
												}
												else
												{
													if ($wfec >= $fechaAct)
													{
														echo "<td bgcolor=".$color." align=center><font size=2><A HREF='asignacionCitaEquSala.php?pos1=0&amp;pos2=".substr($wequ,0,strpos($wequ,"-"))."&amp;pos3=0&amp;pos4=".$wfec."&amp;pos5=".$whi."&amp;pos6=".$whf."&amp;pos7=".$wul."&amp;pos8=0&&amp;pos9=".$inc."&amp;empresa=".$empresa."&amp;wsw=".$wsw."&amp;wtit=".$wtit."&amp;colorDiaAnt=".$colorDiaAnt."&wfec=".$wfec."&caso=".$caso."&wemp_pmla=".$wemp_pmla."' class='desactivar'>Editar</font></td>";
													}
												}
										}	
										else
											echo "<td bgcolor=".$color." align=center><font size=2>Sin Edicion</font></td>";
										echo "</tr>";
									}
									
							
							
							} //num3>0  
							
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
				}
			}
				echo "</table>";
		}
	} //SI CASO = 3 NUM = 0
	else
	{
		echo "<center><table border=0 aling=center>";
		echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
		echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#00ffff LOOP=-1>ESTE DIA DEL CALENDARIO ES FESTIVO -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
		echo "<br><br>";
	}
		echo "<table border=0 align=center>";
		echo "<tr><td><A HREF='#Arriba'><B>Arriba</B></A></td></tr></table>";
		
		echo "<center><b><A HREF='dispEquiposSala.php?wemp_pmla=".@$wemp_pmla."&wbasedato=$empresa&consultaAjax=10&wfec=$wfec&wsw=$wsw&colorDiaAnt=$colorDiaAnt&caso=$caso'>Retornar</A></b><br></center>";
		
		echo "<meta content='30;URL=agendaEquiposSala.php?wemp_pmla=$wemp_pmla&empresa=$empresa&wequ=$wequ&consultaAjax=10&wfec=$wfec&wsw=".@$wsw."&colorDiaAnt=$colorDiaAnt&caso=$caso' http-equiv='REFRESH'> </meta>";
		
		//div causa cancelacion
		echo "<div id='causa_cancelacion' style='display:none'>";
		//div para sacar las causas	
		echo "<center>";
		$tipo = "CI";
		causas($tipo);
		
		echo "<br><input type='button' value='Cerrar' style='width:100' onclick='$.unblockUI();'><br>";
		
		echo "</center>";
		//echo $cau;
		echo "</div>";
		//<a href='agendaEquiposSala.php?empresa=$wbasedato&wequ=$wequ&wsw=$wsw&colorDiaAnt=$colorDiaAnt&wfec=$wfec&caso=$caso&wemp_pmla=$wemp_pmla' >"
		
		echo "</body>";
		echo "</html>";
			
}
