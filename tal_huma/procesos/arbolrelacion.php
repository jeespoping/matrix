<?php
include_once("conex.php");


include_once("root/comun.php");


global $wemp_pmla;

/**
 PROGRAMA                   : arbolrelacion.php
 AUTOR                      : Felipe Alvarez Sanchez.
 FECHA CREACION             : 28 Mayo de 2012

 DESCRIPCION:
 Arbol de relaciones: es un programa que sirver para parametrizar una jerarquia de empleador-empleado y de esta manera utilizarla
 en diferentes ambitos de Promotora Las Americas. El uso particular que se le va a dar es la evaluación de competencias.
 
 Modificaciones:
 
 Septiembre 29 2016: Arleyda Insignares C.
 Se adiciona boton 'Ver organigrama' para accesar 'consultaorganigrama.php' (Organigrama grafico).

 Enero 27 2014 : Juan C. Hdez 
 Se adiciona el campo Idecec en la tabla _000013 el cual indica si se tienen congeladas las cesantias o no ( Idecec:cesantias congeladas),
 es decir si el campo esta en "on" indica que si las tiene congeladas y en "off" o nulo que no.

**/
$wactualiz = "2016-09-29";


if($woperacion =='quitarrelacion')
{
$wbasedato = consultarAliasPorAplicacion ($conex, $wemp_pmla, 'talhuma');
$q   =   "DELETE "
		 .  "  FROM ".$wbasedato."_000008 "
		 .   " WHERE Ajeucr= '".$wcalificador."' "
		 .   "   AND Ajeuco= '".$wcalificado."' ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

return;
}

if ($operacion=='inicioprograma')
{
	include_once("../procesos/funciones_talhuma.php");
	

	
 
	cargar_hiddens_para_autocomplete();
	return;
}

function cargar_hiddens_para_autocomplete()
{
	// --> CCOS
	global $conex;
	global $wbasedato;
	$caracter_ok = array("a","e","i","o","u","A","E","I","O","U","n","N","u","U","-","-","-","a","e","i","o","u","A","E","I","O","U","A","S"," ","","N","N", "U", "");
	$caracter_ma = array("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","ü","Ü",",","/","à","è","ì","ò","ù","À","È","Ì","Ò","Ù","Â","§","®","'","?æ","??", "?£", "°");

	
	$arr_cco = array();
	
	$q_cco = " SELECT Ccocod AS codigo, Cconom AS nombre 
				 FROM costosyp_000005
				WHERE Ccoest = 'on' 	
				ORDER BY nombre ";
			
	$r_cco = mysql_query($q_cco,$conex) or die("Error en el query: ".$q_cco."<br>Tipo Error:".mysql_error());

	while($row_cco = mysql_fetch_array($r_cco))
	{
		$row_cco['nombre'] = str_replace($caracter_ma, $caracter_ok, $row_cco['nombre']);
		$arr_cco[trim($row_cco['codigo'])] = trim($row_cco['nombre']);
	}
	
	echo json_encode($arr_cco);
	
}


if($woperacion =='muestrarelexistentes')
{

$wbasedato = consultarAliasPorAplicacion ($conex, $wemp_pmla, 'talhuma');
$q = 	" SELECT Ajeucr , Ideno1,Ideno2,Ideap1,Ideap2,Ideuse "
		."  FROM ".$wbasedato."_000008, talhuma_000013 "
		." WHERE Ajeuco = '".$wcalificado."' "
		."   AND Ajeucr != '".$wcalificador."' "
		."   AND Ideest = 'on' "
		."   AND Ajeucr = Ideuse ";
		
$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
$numrowcom = mysql_num_rows($res);
if($numrowcom > 0)
{
echo "<br>";
echo "<br>";
echo "<table align='center'><tr><td><b>Usuario ya tiene relacionados los siguientes jefes<b></td></tr></table>";
echo "<br>";
echo "<br>";
echo "<table id ='frederick' align='center'>";
echo "<tr class='fila2'><td></td><td>Nombre</td></tr>";
while($row = mysql_fetch_array($res))
{
	echo "<tr id='tr-".$row['Ideuse']."' class='fila1'>";
	echo "<td ><input type='checkbox' id='".$row['Ideuse']."' value='".$wcalificado."' checked onclick='quitarrelacion(\"".$row['Ideuse']."\",\"".$wcalificado."\")'></td><td>".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."</td>";
	echo "</tr>";
}
echo "</table>";
}
return;
}

if ($woperacion =='eliminacordinador')
{
$wbasedato = consultarAliasPorAplicacion ($conex, $wemp_pmla, 'talhuma');
	
	$q =  "    UPDATE   ".$wbasedato."_000008 "
			 ."	  SET  Ajeccc = '',"
			 ."		   Ajecoo = 'off' "
			 ." WHERE   Ajeucr ='".$wcodcordinador."' ";

$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

}
if ($woperacion =='mostrarccocordinador')
{
	$wbasedato = consultarAliasPorAplicacion ($conex, $wemp_pmla, 'talhuma');
	$q = "SELECT   Ajeccc  "
		."  FROM   ".$wbasedato."_000008 " 
		." WHERE   Ajeucr = '".$wcodcordinador."' " ;
	
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	
	$row = mysql_fetch_array($res);
	$wccos = $row['Ajeccc'];
	$wccos1 = $wccos;

	$wccos = explode(",", $wccos);
	$i =0;
	while(($i) < count($wccos))
	{
		 if ($wccos[$i]!='')
		 {
			$q = "  SELECT  Ccocod,Cconom
					  FROM  costosyp_000005
					 WHERE  Ccocod='".$wccos[$i]."'";
			$res1 = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			
			$row1 = mysql_fetch_array($res1);
				
		
			echo '<div id="div_chk_divccocordinador_'.$wccos[$i].'" style="margin-top:2px;" class="fila1">';
			echo '<table><tr>';
			echo '<td><input type="checkbox" checked="checked" id="chk_divccocordinador_'.$wccos[$i].'" name="chk_divccocordinador_'.$wccos[$i].'" value="'.$wccos[$i].'" onclick="desmarcarOpcion(this);"></td>';
			echo '<td>'.$wccos[$i].'-'.$row1['Cconom'].'</td>';
			echo '</tr></table>';
			echo '</div>';
			
		 }
		 $i++;
	}
return;	
}
if($woperacion =='eliminarccocordinador')
{
	$wbasedato = consultarAliasPorAplicacion ($conex, $wemp_pmla, 'talhuma');
	$q = "SELECT   Ajeccc  "
		."  FROM   ".$wbasedato."_000008 " 
		." WHERE   Ajeucr = '".$wcodcordinador."' " ;
	
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	

	
	$row = mysql_fetch_array($res);
	
	$cadenacco = $row['Ajeccc'];
	$cadenacco = str_replace(",".$wccocordinador,"",$cadenacco);
	$cadenacco = str_replace($wccocordinador.",","",$cadenacco);
	$cadenacco = str_replace($wccocordinador,"",$cadenacco);
	
	
	$q =  "    UPDATE   ".$wbasedato."_000008 "
			 ."	  SET  Ajeccc = '".$cadenacco."'"
			 ." WHERE   Ajeucr ='".$wcodcordinador."' ";
	
	
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	
	echo $q;
	
	if($cadenacco=='')
	{
		$q =  " UPDATE   ".$wbasedato."_000008 "
			 ."	   SET   Ajecoo = 'off'"
			 ."  WHERE   Ajeucr ='".$wcodcordinador."' ";
			 
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	}
	
	return;
	
}

if($woperacion =='guardacordinador')
{
	$fecha= date("Y-m-d");
	$hora = date("H:i:s");  
	$wbasedato = consultarAliasPorAplicacion ($conex, $wemp_pmla, 'talhuma');
	
	$q = "SELECT    Ajeccc  "
		."  FROM   ".$wbasedato."_000008 " 
		." WHERE   Ajeucr = '".$wcodcordinador."' " ;
	
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$numrow = mysql_num_rows($res);
	$row = mysql_fetch_array($res);
	$siesta = $row['Ajeccc'];
	$auxsiesta = $row['Ajeccc'];	
	$siesta = strpos($siesta,$wccocordinador );

	if($siesta === false)
	{
		
		if ($numrow!=0)
		{
			if($auxsiesta !='')
			{
				$q =  "UPDATE   ".$wbasedato."_000008 "
					 ."   SET   Ajecoo = 'on' ,"
					 ."	        Ajeccc = CONCAT( Ajeccc ,',".trim($wccocordinador)."' )"
					 ." WHERE   Ajeucr ='".$wcodcordinador."' ";
			}
			else
			{
				$q =  "UPDATE   ".$wbasedato."_000008 "
					 ."   SET   Ajecoo = 'on' ,"
					 ."	        Ajeccc = '".trim($wccocordinador)."' "
					 ." WHERE   Ajeucr ='".$wcodcordinador."' ";
			}
		}
		else
		{
			$q = "INSERT INTO  ".$wbasedato."_000008 "	
				."     		  ( Medico,
								Fecha_data,
								Hora_data,
								Ajeucr,
								Ajecoo,
								Ajeccc,
								Seguridad) "
				."     VALUES ( '".$wbasedato."',
								'".$fecha."',
								'".$hora."',
								'".$wcodcordinador."',
								'on',
								'". trim($wccocordinador)."',
								'C-".$wbasedato."')";
		}
	echo q;
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	}
	
	
 	
	
	

	return;
}

if($woperacion=='puedegenerarcertificado')
  {
   $q= "UPDATE talhuma_000013 SET Idecer='".$westado."'  WHERE Ideuse='".$wcodigoempleado."'";
   $res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
   return;
  }

if($woperacion=='cesantiascongeladas')
  {
   $q= "UPDATE talhuma_000013 SET Idecec='".$westado."'  WHERE Ideuse='".$wcodigoempleado."'";
   $res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
   return;
  }

if(isset($accion) && $accion == 'nuevo_cco')
{

$data = array('datos'=>'', 'mensaje'=>'', 'error'=>0);
$wbasedato = consultarAliasPorAplicacion ($conex, $wemp_pmla, 'talhuma');

$q = "  SELECT  Ideno1, Ideno2, Ideap1, Ideap2,Ideuse,Idecco
		  FROM  ".$wbasedato."_000013
		 WHERE  Idecco='".$wcco."'
		   AND  Ideest = 'on' ";

$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
$numrow = mysql_num_rows($res);

if ($numrow==0)
{
	$data['error'] = 1;
	$data['mensaje'] = 'No hay personas en el centro de costos';
}
else
{
$respuesta = '';
$respuesta .= "<div id='ref_".$wcco."' align='center'>
                <table width='900' border='0' cellspacing='0' cellpadding='0'>
                    <tr class='encabezadoTabla'>
			        </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                </table>
                <table width='900' border='0' cellspacing='0' cellpadding='0'>
                    <tr>
					<td width='850' align='left' ><a href='#null'  onclick='verSeccion(\"div_cco-".$wcco."\")'>CCO: ".$wnncco."</a></td>
					<td width='50' align= 'left' ><a href='#null' onclick='eliminarSeccion(\"div_cco-".$wcco."\",\"ref_".$wcco."\",\"".$wcalificador1."\",\"".$wcco."\")' >Eliminar</a></td>
                    </tr>
                </table>
    </div>";

$respuesta .= "	<div id='div_cco-".$wcco."' width='900' align='center' class='borderDiv displ'>

		<table width='900'>
		<tr class='encabezadoTabla'>
		<td width='546' rowspan='2'><strong>Nombre</strong></td>
		<td width='71'><strong>Subordinado</strong></td>
		<td align='center' rowspan='2'>Certificado Laboral</td>
		<td align='center' rowspan='2'>Cesantias Congeladas</td></tr>
	
		<tr class='encabezadoTabla'>
		<td width='71'>Todos<input type='checkbox' id='calificado".$wcco."' name='calificado'  value='".$wcco."' onclick='Seleccionartodos(this,\"".$numrow."\",\"".$wcalificador1."\",\"".$wcco."\")'  /></td>
		</tr>";
$j=0;
while ($row = mysql_fetch_array($res))
		{


			if($row['Ideuse']!=$wcalificador1)
			{
			    //Alternar colores de las filas
				if (is_int ($r/2))
				{
					$wcf="fila1";  // color de fondo de la fila
				}
				else
				{
					$wcf="fila2"; // color de fondo de la fila
				}
				$r++;
				$respuesta .= "	<tr  class='".$wcf."'>


									<td align='left'> ".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."</td>";

									$query= " SELECT Carfor, Ajefor"
										."  FROM  ".$wbasedato."_000008, ".$wbasedato."_000013 , root_000079 "
										." WHERE Ajeucr = '".$wcalificador1."' "
										."   AND Ajeuco = '".$row['Ideuse']."'"
										."   AND Ajeuco = Ideuse "
										."   AND Ideccg = Carcod";
									
									
									
									
									$resquery = mysql_query($query,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
									$numrowcom = mysql_num_rows($resquery);
									$row2 = mysql_fetch_array($resquery);


				$respuesta .= "				<td align='center'>";


				if($numrowcom!='0')
				{
					$respuesta .= 				"<input type='checkbox' id='calificado|".$row['Idecco']."|".$j."' name='calificado'  value='".$row['Ideuse']."|".$row['Idecco']."' onclick='verificarelacion(this,\"".$wcalificador1."\")'  checked='true'/>";
				}
				else
				{
					$respuesta .= 				"<input type='checkbox' id='calificado|".$row['Idecco']."|".$j."' name='calificado'  value='".$row['Ideuse']."|".$row['Idecco']."' onclick='verificarelacion(this,\"".$wcalificador1."\")'   />";
				}
							$respuesta .= "</td>";

				$query= " SELECT Idecer "
						."  FROM  talhuma_000013 "
						." WHERE Ideuse = '".$row['Ideuse']."' ";
				
				$resquery = mysql_query($query,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				$numrowcom = mysql_num_rows($resquery);
				$row2 = mysql_fetch_array($resquery);
						
                //Certificado Laboral						
				if ($row2['Idecer']=='on')
					$respuesta .= "<td align='center'><input type='checkbox' value='ok' id='certificado-".$row['Ideuse']."' onclick='puedegenerarcertificado(\"".$row['Ideuse']."\")'  checked></td>";
				else
					$respuesta .= "<td align='center'><input type='checkbox' value='ok' id='certificado-".$row['Ideuse']."' onclick='puedegenerarcertificado(\"".$row['Ideuse']."\")' ></td>";
					
				$query= " SELECT Idecec "
						."  FROM  talhuma_000013 "
						." WHERE Ideuse = '".$row['Ideuse']."' ";
				
				$resquery = mysql_query($query,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
				$numrowcom = mysql_num_rows($resquery);
				$row2 = mysql_fetch_array($resquery);
									
				//Cesantias Congeladas					
				if ($row2['Idecec']=='on')
					$respuesta .= "<td align='center'><input type='checkbox' value='ok' id='cesantias-".$row['Ideuse']."' onclick='cesantiascongeladas(\"".$row['Ideuse']."\")'  checked></td>";
				else
					$respuesta .= "<td align='center'><input type='checkbox' value='ok' id='cesantias-".$row['Ideuse']."' onclick='cesantiascongeladas(\"".$row['Ideuse']."\")' ></td>";	
				
					

			}
			$respuesta .= "</tr>";

		 $j++;

		}
		$respuesta .= "			</table>";

}
	$data['datos'] = $respuesta;
	echo json_encode($data);
	return;

}

?>
<div id="arbolrelacion" >
<html>
<head>
<title>Arbol de relaciones</title>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
		<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
		<script type="text/javascript" src="../../../include/root/jquery.ui.timepicker.js"></script>
		<link type="text/css" href="../../../include/root/jquery.ui.timepicker.css" rel="stylesheet"/>
		<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
		<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>

<script type='text/javascript'>
// funcion para mostrar los ya asignados
function mostrarccocordinador ()
{
var wcodcordina = $('#wcalificador2').val().split('|||');
wcodcordina = wcodcordina[0];
$.post("arbolrelacion.php",
	{
		consultaAjax: '',
		woperacion	: 'mostrarccocordinador',
		wcodcordinador: wcodcordina,
		wemp_pmla:	$('#wemp_pmla').val()
		

	}, function(data) {
		
		
		if(data=='')
		{
		}	
		else
			$('#escordinador').attr('checked', true);
		
		$("#divccocordinador").append(data);	
			
	}); 

}
$(document).ready(function() {

  $.post("../procesos/arbolrelacion.php",
		{
			consultaAjax 	: '',
			inicial			: 'no',
			operacion		: 'inicioprograma',
			wemp_pmla		: $('#wemp_pmla').val(),
			wtema           : $('#wtema').val(),
			wuse			: $('#wuse').val()
		}
		, function(data) {
			
			cargar_cco (eval('(' + data + ')'));
		});

});

function cargar_cco (ArrayValores)
{
	var ccos	= new Array();
	var index		= -1;
	var tr ;
	var n = 1;
	tr ="";
	var wfc ;
	for (var cod_ccos in ArrayValores)
	{
		index++;
		ccos[index] = {};
		ccos[index].value  = cod_ccos;
		ccos[index].label  = cod_ccos+'-'+ArrayValores[cod_ccos];
		ccos[index].nombre = ArrayValores[cod_ccos];
	}
	
	$( "#buscador_cco" ).autocomplete({
		
		minLength: 	0,
		source: 	ccos,
		select: 	function( event, ui ){					
			$( "#buscador_cco" ).val(ui.item.nombre);
			$( "#buscador_cco" ).attr('valor', ui.item.value);
			$( "#buscador_cco" ).attr('label', ui.item.label);
			seleccioncco();
			return false;
		}
		
		
		
	});	
	
}


//

// funcion para adicionar checkbox a lista de cco que manejara el cordinador  
function adicionarALista(campo, lista, tema)
{
	
	if( $('#escordinador:checked').val()=='off')
	{

		var codigo_l = $("#"+campo.id).val();
		codigo_l =  codigo_l.split('-');
		codigo_l = codigo_l[0];
		var descr_l = $('#'+campo.id+' option:selected').text();
		
		if(codigo_l != 'seleccione' && $('#chk_'+lista+'_'+codigo_l).length == 0)
		{
			
			var input_chk =     '<div id="div_chk_'+lista+'_'+codigo_l+'" style="margin-top:2px;" class="fila1">'
								+'  <table> <tr>'
								+'        <td><input type="checkbox" checked="checked" id="chk_'+lista+'_'+codigo_l+'" name="chk_'+lista+'_'+codigo_l+'" value="'+codigo_l+'" onclick="desmarcarOpcion(this);"></td>'
								+'        <td>'+descr_l+'</td>'
								+'   </tr> </table>'
								+'</div>';
			$("#"+lista).append(input_chk);
		}
		$("#"+campo.id).val('');
	}
}
//

//funcion que elimina de la lista el cco que maneja el cordinador
function desmarcarOpcion(ele)
{
   if(!$("#"+ele.id).is(":checked"))
   {
	   $("#div_"+ele.id).remove();
	   eliminarccocordinador(ele.id);
   }
}
function eliminarccocordinador(cco)
{

cco = cco.split("_");
cco = cco[2];
var wcodcordina = $('#wcalificador2').val().split('|||');
wcodcordina = wcodcordina[0];

$.post("arbolrelacion.php",
	{
		consultaAjax: '',
		woperacion	: 'eliminarccocordinador',
		wccocordinador: cco,
		wcodcordinador: wcodcordina,
		wemp_pmla:	$('#wemp_pmla').val()
		

	}, function(data) {
			
	}); 

}

// funcion para asignar centro de costo a coordinar 


function grabarccocordinador()
{
	if( $('#escordinador:checked').val()=='off')
		{


			if($('#wcalificador2').val() == 'ninguno' )
			{
				
			}else
			{
				if ($('#selectccocordinador').val() !='seleccione')
				{
					var wcodcordina = $('#wcalificador2').val().split('|||');
					wcodcordina = wcodcordina[0];
					var wccocod = $('#selectccocordinador').val().split('-');
					wccocod = wccocod[0];
					$.post("arbolrelacion.php",
					{
						
						consultaAjax: '',
						woperacion	: 'guardacordinador',
						wescordinador: $('#escordinador').val(),
						wccocordinador: wccocod,
						wcodcordinador: wcodcordina,
						wemp_pmla:	$('#wemp_pmla').val()

					}, function(data) {
							
					
					}); 
				}
			}
		}else
		{
			alert("Para grabar debe estar chequeada la casilla de cordinador");
			$('#selectccocordinador').val('seleccione');
		}
	
	
}




//  funcion  para establecer si es coordinador de unidad o no
function escordinador ()
{
	if( $('#escordinador:checked').val()=='off')
	{
		if($('#selectccocordinador').val() != 'seleccione' )
		{
			grabarccocordinador ();
		}
		else
		{
			alert("Seleccione un valor valido de centro de costos a coordinar");
		}
		}
	else{
	
				var wccocod = $('#selectccocordinador').val().split('-');
					wccocod = wccocod[0];
				var wcodcordina = $('#wcalificador2').val().split('|||');
					wcodcordina = wcodcordina[0];
					
					var res = confirm("Quiere que esta persona ya no sea un coordinador?");
					if( res == true )
					{

						$.post("arbolrelacion.php",
						{
							consultaAjax: '',
							woperacion	: 'eliminacordinador',
							wcodcordinador: wcodcordina,
							wemp_pmla:	$('#wemp_pmla').val()

						}, function(data) {
								
								$('#divccocordinador').html('');
						
						}); 
					}else{
					
					 
					
					}
	
	
		}

		
	
}

function verRelaciones(emp_pmla,wcco)
{

path= "consultaarbolrelaciones.php?wemp_pmla="+emp_pmla+"&wcco="+wcco+"&pintatabla=pintar";
window.open(path,'','fullscreen=no, width=1200,height=1200,status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes');
}

function verOrganigrama(emp_pmla,wcco)
{
path= "consultaorganigrama.php?wemp_pmla="+emp_pmla+"";
window.open(path,'','fullscreen=no, width=1200,height=1200,status=no, menubar=no, toolbar=no, directories=no, resizable=yes, scrollbars=yes,titlebar=yes');
}

//Esta funcion se ejecuta cuando se selecciona un centro de costos, haciendo una carga de la pagina con el parametro wcco
function seleccioncco()
{

var ccoppal= $("#buscador_cco").attr('valor');
var nccoppal = $("#buscador_cco").val();
var emp_pmla = $("#wemp_pmla").val();
division=document.getElementById('div_cco-'+ccoppal);


var params = 'arbolrelacion.php?consultaAjax=&wemp_pmla='+emp_pmla+'&wcco='+ccoppal+'&wncco='+nccoppal;
		$.get(params, function(data) {
			$('#arbolrelacion').html(data);
			$("#buscador_cco").val(nccoppal);
			$("#buscador_cco").attr("valor",ccoppal);
		});

}
// Esta funcion carga denuevo la pagina con las variables wcco, wcalificador seteadas
function seleccionarcdor(elemento,emp_pmla,wcco, wncco)
{

var ccoppal= $("#buscador_cco").attr('valor');
var nccoppal = $("#buscador_cco").val();
var emp_pmla = $("#wemp_pmla").val();
var campo = elemento.value.split("|||");
var calificador = campo[0];

 var contenedor=(document.getElementById('wcontenedor').value*1);
	document.getElementById('wcontenedor').value= (document.getElementById('wcontenedor').value*1) + 1 ;
	numcontenedor=document.getElementById('wcontenedor').value;


 var params = 'arbolrelacion.php?consultaAjax=&wemp_pmla='+emp_pmla+'&wcco='+wcco+'&wcalificador1='+calificador+'&wncco='+wncco;
		$.get(params, function(data) {
			$('#arbolrelacion').html(data);
			$("#buscador_cco").val(nccoppal);
			$("#buscador_cco").attr("valor",ccoppal);
			$('#trid').show();
			mostrarccocordinador();
			for (i=1;i<campo.length;i++)
			 {
				//alert(campo[i]);
				carganuevocco2(campo[i],emp_pmla,campo[0]);
				
			 }
		});

}
function carganuevocco (elemento,emp_pmla)
{
var campo = elemento.value.split("*|*");

$('#buttoncc').focus();

var ncco= campo[0];
var nncco= campo[1];
var calificador=document.getElementById('wcalificador1').value;
var division = document.getElementById('div_cco-'+ncco);


if (division==null)
{
	var params = 'arbolrelacion.php?consultaAjax=&wemp_pmla='+emp_pmla+'&wcco='+ncco+'&accion=nuevo_cco&wcalificador1='+calificador+'&wnumcontenedor='+numcontenedor+'&wnncco='+nncco;
	$.post(	params,
			function(data) {
				if(data['error'] == 1)
				{
					alert(data['mensaje']);
				}
				else
				{
					$('#contenedor0').append(data['datos']);
				}
			},
			"json"
		);

	$('#div_newcco').hide('slow');
}
else
{
alert("Ya esta seleccionado este Centro de Costos");
}

}

function carganuevocco2 (elemento,emp_pmla,calificador)
{

var campo = elemento.split("**");

var ncco= campo[0];
var nncco= campo[1];

	var params = 'arbolrelacion.php?consultaAjax=&wemp_pmla='+emp_pmla+'&wcco='+ncco+'&accion=nuevo_cco&wcalificador1='+calificador+'&wnumcontenedor='+numcontenedor+'&wnncco='+nncco;
	$.post(	params,
			function(data) {
				if(data['error'] == 1)
				{

					alert(data['mensaje']);
				}
				else
				{

					$('#contenedor0').append(data['datos']);
				}
			},
			"json"
		);

}

 function verSeccion(id){
        $("#"+id).toggle("normal");
    }

function eliminarSeccion(id,id2,calificador,wcco)
{
 $("#"+id).remove();
 $("#"+id2).remove();
 var operacion='eliminoSeccion';

 grabadato(operacion,id,calificador,wcco,1);
}
function Seleccionartodos(elemento,numero,calificador,cco)
{

var operacion = "insercion_desde_checkbox";

for (i=0;i<numero; i++){

var calificado="calificado|"+elemento.value+"|"+i;
calificado="calificado|"+elemento.value+"|"+i;
calificado=  document.getElementById(calificado).value;
calificado=calificado.split("|");
calificado= calificado[0];
var formulario = "formevaluacion|"+calificado+"|"+cco+"|"+i;
var evaluacion =document.getElementById(formulario).value;

 if($("#calificado"+cco).is(':checked')) {

 if($("#calificado|"+elemento.value+"|"+i).is(':checked')) {


        } else {
           $("#calificado|"+elemento.value+"|"+i).attr('checked', true);
			grabadato(operacion,calificador,calificado,cco,evaluacion);

        }

 }
else

{

  if($("#calificado|"+elemento.value+"|"+i).is(':checked')) {

            $("#calificado|"+elemento.value+"|"+i).attr('checked', false);
			grabadato(operacion,calificador,calificado,cco,evaluacion);
		} else {

        }
}
}
}

function quitarrelacion (jefe,subordinado)
{
	$.post("arbolrelacion.php",
            {
                consultaAjax: '',
                wemp_pmla   : $("#wemp_pmla").val(),
                wtema       : $("#wtema").val(),
				woperacion 	: "quitarrelacion",
				wcalificador : jefe,
				wcalificado  : subordinado
				
				 
			},function(data) {
			$('#tr-'+jefe).remove();
			if( $('#frederick tbody').find('tr').length == 1 ){
				//Cerrar ventana
				$.unblockUI();
			}
			
			});
			

}
//sirve para mostrar relaciones ya existentes
function verificarelacion (elemento,calificador)
{
if( calificador == "")
 {
	alert("Debe seleccionar un calificador");
 }
else
 {
	 var calificado1 = elemento.value;
	 var calificado = calificado1.split('|');
	 calificado = calificado[0];
	 $.post("arbolrelacion.php",
            {
                consultaAjax: '',
                wemp_pmla   : $("#wemp_pmla").val(),
                wtema       : $("#wtema").val(),
				woperacion 	: "muestrarelexistentes",
				wcalificador : calificador,
				wcalificado  : calificado
				
				 
			},function(data) {
				$('#ventanarelacionescont').html(data);
				if(data!='')
				{
				 fnMostrar();
				}
				 seleccionarcdo(elemento,calificador)
			});
			
	
	
 }

}
//---------------------
function fnMostrar(  )
{

		
			
		
		
		if( $('#ventanarelaciones') ){
			$.blockUI({ message: $('#ventanarelaciones' ), 
							css: { left: ( $(window).width() - 800 )/2 +'px', 
								  top: '200px',
								  width: '500px'
								 } 
					  });	
		}
		
}
// funcion que se encargar de verificar si la variable calificador esta lleno, si sí, llama a grabadato
// esta funcion se llama cuando se chequea un checkbox calificado.
function seleccionarcdo(elemento,calificador)
{
// verificarelacion (elemento,calificador);
 //alert("valor"+elemento.value);
 //alert("id"+elemento.id);
 if( calificador == "")
 {
	alert("Debe seleccionar un calificador");
 }
 else
 {
 var calificado1 = elemento.value;
 var calificado = calificado1.split('|');
 var id = elemento.id;
 id= id.split("|");
 id = id[2];

 var operacion = "insercion_desde_checkbox";
 var aux="formevaluacion|"+calificado[0]+"|"+calificado[1]+"|"+id;
 //alert(aux);
 var evaluacion="1";

 //llamado a la funcion grabadato
 
 grabadato(operacion,calificador,calificado[0],calificado[1],evaluacion);
 }
}

// Cuando se cambia el select que contiene las evaluaciones se hace la funcion seleccionaevaluacion
function seleccionaevaluacion (elemento)
{
calificado = elemento.id;
aux = calificado.split("|");
calificado = aux[1];
cco_calificado=aux[2];
checkbox= "calificado|"+aux[2]+"|"+aux[3];
//alert(checkbox);

//verifica si el checkbox correspondiente al calificado esta activo, si esta llama a la funcion grabardato
if (document.getElementById(checkbox).checked)
	{

		operacion="actualizo_desde_select";
		calificador=document.getElementById('wcalificador1').value;
		evaluacion= elemento.value;
		grabadato(operacion,calificador,calificado,cco_calificado,evaluacion);

	}
else
	{
		alert("debe chequear a esta persona como calificado");
	}

}

function puedegenerarcertificado(codigoempleado)
{
	var estado = 'off';
    if(  $('#certificado-'+codigoempleado).is(':checked') )
		estado= 'on';
	
	$.post("arbolrelacion.php",
				{
					consultaAjax: '',
					woperacion	: 'puedegenerarcertificado',
					wcodigoempleado : codigoempleado,
					westado			: estado
					
				}); 
				
				
}


function cesantiascongeladas(codigoempleado)
{
	var estado = 'off';
    if(  $('#cesantias-'+codigoempleado).is(':checked') )
		estado= 'on';
	
	$.post("arbolrelacion.php",
				{
					consultaAjax: '',
					woperacion	: 'cesantiascongeladas',
					wcodigoempleado : codigoempleado,
					westado			: estado
				}); 
}




// funcion que va a la bd por medio de ajax, esta funcion recibe un parametro (operacion) el cual estipula que debe hacer (update,insert)
function grabadato(operacion,calificador,calificado,cco_calificado, evaluacion)
{

	var empleado=document.getElementById('wemp_pmla').value;
	var cco = document.getElementById('wcco').value;

	if(operacion=='insercion_desde_checkbox')
	{
		
		parametros = "consultaAjax=guardarelacion&calificador="+calificador+"&calificado="+calificado+"&wemp_pmla="+empleado+"&wcco_calificado="+cco_calificado+"&wcco_calificador="+cco+"&wevaluacion="+evaluacion;

	}

	if(operacion=='actualizo_desde_select')
	{

		parametros = "consultaAjax=actualizorelacion&calificador="+calificador+"&calificado="+calificado+"&wemp_pmla="+empleado+"&wcco_calificado="+cco_calificado+"&wcco_calificador="+cco+"&wevaluacion="+evaluacion;

	}

	if (operacion== 'eliminoSeccion')
	{
		parametros = "consultaAjax=eliminotodoccocalificador&calificador="+calificado+"&wcco_calificado="+cco_calificado+"&wemp_pmla="+empleado;
	}

			   try
			  {

				var ajax = nuevoAjax();
				ajax.open("POST", "arbolrelacion.php",false);
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send(parametros);
				//alert(ajax.responseText);
			  }catch(e){ alert(e) }
}
</script>

<style type="text/css">
    .displ{
        display:block;
    }
    .borderDiv {
        border: 2px solid #2A5DB0;
        padding: 5px;
    }
    .resalto{
        font-weight:bold;
    }
    .parrafo1{
        color: #676767;
        font-family: verdana;
    }
</style>

</head>
<body>

<?php



echo '<input type="hidden" id="wemp_pmla" name="wemp_pmla" value="'.$wemp_pmla.'" />';
echo '<input type="hidden" id="wcco" name="wcco" value="'.$wcco.'" />';
echo '<input type="hidden" id="wcalificador1" name="wcalificador1" value="'.$wcalificador1.'" />';
echo '<input type="hidden" id="wcalificado" name="wcalificado" value="" />';
echo '<input type="hidden" id="wcontenedor" name="wcontenedor" value="'.$wcontenedor.'" />';
echo '<input type="hidden" id="wncco" name="wncco" value="'.$wncco.'" />';

$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');

/****************************************************************************************************************
*  FUNCIONES PHP >>
****************************************************************************************************************/

if(!isset($consultaAjax) || $consultaAjax=='')

{
	// consulta de centro de costos
	$q = "  SELECT  Ccocod,Cconom
			  FROM  costosyp_000005
			  ORDER BY Ccocod";

	$res1 = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	// consulta de empleados por centro de costos
	$q = "  SELECT  Ideno1, Ideno2, Ideap1, Ideap2,Ideuse,Idecco
			  FROM  ".$wbasedato."_000013
			 WHERE  Idecco='".$wcco."'
			   AND  Ideest = 'on' 
		  ORDER BY  Ideno1, Ideno2, Ideap1, Ideap2 " ;
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

	echo"<table width='900' align='center'>
			<tr>
				<td>
				<div id='ref_tbeva' align='center'>
                <table width='900' border='0' cellspacing='0' cellpadding='0'>
                    <tr class='encabezadoTabla'>
			        </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                </table>
                <table width='900' border='0' cellspacing='0' cellpadding='0'>
                    <tr>
                        <td align ='left' width='850'><a href='#null'  onclick='verSeccion(\"divppal\")'>Seleccionar Jefe</a></td>
						 <td align='left' width='50'><input type='button'  value='Ver Relaciones' onclick='verRelaciones(\"".$wemp_pmla."\",\"".$wcco."\")'/></td>
						 <td align='left' width='50'><input type='button'  value='Ver Organigrama' onclick='verOrganigrama(\"".$wemp_pmla."\",\"".$wcco."\")'/></td>
                    </tr>
                </table>
			</div>

			<div id='divppal' width='900' align='center' class='borderDiv displ'>
					<table width='900'>
						<tr align='left' class='fila1'>
							<td width='134'>Centro de costos:</td>
							<td width='134'>";
							
	echo"
			<input type='text'  id='buscador_cco' size='40' >
			<img width='12'  border='0' height='12' title='Busque el nombre o parte del nombre del centro de costo' src='../../images/medical/HCE/lupa.PNG'>
		";
							
							
							
	echo"					</td>
						</tr>
						
						<tr align='left'  class='fila1'>
						<td width='134'>Jefe:</td>
							<td width='134'><select  name='wcalificador2' id='wcalificador2' onchange='seleccionarcdor(this,\"".$wemp_pmla."\",\"".$wcco."\",\"".$wncco."\")' >
								<option value='ninguno' >--Seleccione--</option>";
	$vec_cco= array();
	While($row = mysql_fetch_array($res))
		{

		   	$query= "SELECT DISTINCT(Ajecco) , Cconom  "
			      . "  FROM  ".$wbasedato."_000008, costosyp_000005 "
				  ."  WHERE  Ajeucr= '".$row['Ideuse']."' "
				  ."    AND  Ajecco = Ccocod "
				  ."    AND  Ajecco != '".$wcco."' ";

			$resp = mysql_query($query,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());


			While($rowp = mysql_fetch_array($resp))
			{
			$vec_cco[$row['Ideuse']] = $vec_cco[$row['Ideuse']] ."|||".$rowp[0]."**".$rowp[1];
			}


			if(isset($wcalificador1) AND $wcalificador1==$row['Ideuse'])
				{
						echo	  "<option value='".$row['Ideuse']."".$vec_cco[$row['Ideuse']]."' selected>".utf8_encode($row['Ideno1'])." ".utf8_encode($row['Ideno2'])." ".utf8_encode($row['Ideap1'])." ".utf8_encode($row['Ideap2'])."</option>";
				}
				else
				{
						echo	  "<option value='".$row['Ideuse']."".$vec_cco[$row['Ideuse']]."'>".utf8_encode($row['Ideno1'])." ".utf8_encode($row['Ideno2'])." ".utf8_encode($row['Ideap1'])." ".utf8_encode($row['Ideap2'])."</option>";
				}
		}
	echo"				</td>
						</tr>";
	//Query para el select de los centros de costo  para ser asignados al coordinador					
	$q = "  SELECT  Ccocod,Cconom
			  FROM  costosyp_000005
			  ORDER BY Ccocod";

	$res1 = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());					
	//
	
	echo"<tr id='trid'  style='display:none' align='left'>";
	echo"<td class='fila1'>Coordinador: <input type='checkbox' id='escordinador'  value='off' onclick='escordinador()' ></td>";
	echo"<td class='fila1'><div class='encabezadoTabla' style='text-align: center'><b>Centro de costos<b> ";
	echo"<br><select id='selectccocordinador' onchange='grabarccocordinador(); adicionarALista(this,\"divccocordinador\")' >";
	echo"<option value='seleccione' >-seleccione-</option>";
	While($row = mysql_fetch_array($res1))
	{
		echo"<option value='".$row['Ccocod']."-".$row['Cconom']."' >".$row['Ccocod']."-".utf8_encode($row['Cconom'])."</option>";
	}
	
	echo"</select></div>";
	echo"<div id='divccocordinador'></div>";
	echo"</td></tr>";
	
	
	
	
	 echo"</table>
					</div>
				</td>
			</tr>";
	if (isset($wcco) && isset($wcalificador1))
	{

		$q = "SELECT Ajeuco "
		   . "  FROM  ".$wbasedato."_000008 "
		   . " WHERE  Ajecco = '".$wcco."' "
		   . "   AND  Ajeccr = '".$wcco."' "
		   . "   AND  Ajeucr != '".$wcalificador1."'";

		$resvector = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		$vector_conrelacion=array();
		while ($rowvector = mysql_fetch_array($resvector))
		{
			$vector_conrelacion[$rowvector['Ajeuco']]= $rowvector['Ajeuco'] ;
		}

		echo	"<tr>
					<td>

		<div id='ref_".$wcco."' align='center'>
                <table width='900' border='0' cellspacing='0' cellpadding='0'>
                    <tr class='encabezadoTabla'>
			        </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                </table>
                <table width='900' border='0' cellspacing='0' cellpadding='0'>
                    <tr>
                        <td width='850' align='left'><a href='#null'  onclick='verSeccion(\"div_cco-".$wcco."\")'>CCO: ".$wncco."</a></td>
						<td width='50' align= 'left' ><a href='#null'  onclick='eliminarSeccion(\"div_cco-".$wcco."\",\"ref_".$wcco."\",\"".$wcalificador1."\",\"".$wcco."\")' >Eliminar</a></td>
                    </tr>
                </table>
			</div>";
		$r=0;
		mysql_data_seek($res,0);
		$j=0;
		$numrowcom = mysql_num_rows($res);
		$numvector = $vector_conrelacion.lenght;
		$numeroveces = (int)$numrowcom - (int)$numvector;

		echo	"<div id='div_cco-".$wcco."' width='900' align='center' class='borderDiv displ'>
						<table width='900' >
							<tr class='encabezadoTabla'>
								<td width='546' rowspan='2'><strong>Nombre</strong></td>
								<td width='71'><strong>Subordinado</strong></td>
								<td rowspan='2' align='center'>Certificado Laboral</td>
								<td rowspan='2' align='center'>Cesantias Congeladas</td>";
							echo"</tr>
							<tr class='encabezadoTabla'>
							<td width='71'>Todos<input type='checkbox' id='calificado".$wcco."' name='calificado'  value='".$wcco."' onclick='Seleccionartodos(this,\"".$numeroveces."\",\"".$wcalificador1."\",\"".$wcco."\")'   /></td>
							</tr>";

		while ($row = mysql_fetch_array($res))
		{

		  if(!array_key_exists($row['Ideuse'],$vector_conrelacion))
		  {
			if($row['Ideuse']!=$wcalificador1)
			{
			    //Alternar colores de las filas
				if (is_int ($r/2))
					{
						$wcf="fila1";  // color de fondo de la fila
					}
				else
					{
						$wcf="fila2"; // color de fondo de la fila
					}
				$r++;
				echo"	<tr class='".$wcf."'>


									<td align='left'>".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."</td>";



									$query= " SELECT Carfor, Ajefor"
											."  FROM  ".$wbasedato."_000008, ".$wbasedato."_000013 , root_000079 "
											." WHERE Ajeucr = '".$wcalificador1."' "
											."   AND Ajeuco = '".$row['Ideuse']."'"
											."   AND Ajeuco = Ideuse "
											."   AND Ideccg = Carcod";


									$resquery = mysql_query($query,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
									$numrowcom = mysql_num_rows($resquery);
									$row2 = mysql_fetch_array($resquery);



				echo"				<td align='center'>";
				if($numrowcom !='0')
				{
					echo				"<input type='checkbox' id='calificado|".$row['Idecco']."|".$j."' name='calificado'  value='".$row['Ideuse']."|".$row['Idecco']."' onclick='verificarelacion(this,\"".$wcalificador1."\")'  checked='true'/>";

				}
				else
				{
					echo				"<input type='checkbox' id='calificado|".$row['Idecco']."|".$j."' name='calificado'  value='".$row['Ideuse']."|".$row['Idecco']."' onclick='verificarelacion(this,\"".$wcalificador1."\")'  />";
				}
				echo	"</td>";
				// check  de sacar certificado
				$query= " SELECT Idecer"
					   ."   FROM  ".$wbasedato."_000013  "
					   ."  WHERE Ideuse = '".$row['Ideuse']."' ";
					
					$resquery = mysql_query($query,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
					$numrowcom = mysql_num_rows($resquery);
					$row2 = mysql_fetch_array($resquery);
				if ($row2['Idecer']=='on')
					echo "<td align='center'><input type='checkbox' value='ok' id='certificado-".$row['Ideuse']."' onclick='puedegenerarcertificado(\"".$row['Ideuse']."\")'  checked></td>";
				else
					echo "<td align='center'><input type='checkbox' value='ok' id='certificado-".$row['Ideuse']."' onclick='puedegenerarcertificado(\"".$row['Ideuse']."\")' ></td>";
				
				// check de Cesantias Congeladas
				$query= " SELECT Idecec"
					   ."   FROM  ".$wbasedato."_000013  "
					   ."  WHERE Ideuse = '".$row['Ideuse']."' ";
					
					$resquery = mysql_query($query,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
					$numrowcom = mysql_num_rows($resquery);
					$row2 = mysql_fetch_array($resquery);
				if ($row2['Idecec']=='on')
					echo "<td align='center'><input type='checkbox' value='ok' id='cesantias-".$row['Ideuse']."' onclick='cesantiascongeladas(\"".$row['Ideuse']."\")'  checked></td>";
				else
					echo "<td align='center'><input type='checkbox' value='ok' id='cesantias-".$row['Ideuse']."' onclick='cesantiascongeladas(\"".$row['Ideuse']."\")' ></td>";
				
			}
			echo"			</tr>";

		 }
		}
		echo"			</table>
					  </div>
					</td>
				</tr>";
	}
	echo"<tr>
		<td>
		<div id='contenedor0'>

		</div>
		</td>
		</tr>";

if (isset($wcco) && isset($wcalificador1))
	{
	echo"<tr>
		<td align='left'><input type='button' id='buttoncc' value='+ C.Costos' onclick='verSeccion(\"div_newcco\")'/><td>
		</tr>
		<tr><td>
		<div id='div_newcco' width='900' align='center'  style='display:none' class='borderDiv displ'>
        <table width='900' >
		<tr class='fila1'>
		<td align='left'><select name='wcentrocostos2' id='wcentrocostos2' onchange=carganuevocco(this,\"".$wemp_pmla."\") >";

	mysql_data_seek($res1,0);
	While($row = mysql_fetch_array($res1))
	{
    echo"<option value='".$row[0]."*|*".utf8_encode($row[1])."'>".utf8_encode($row[0])." - ".utf8_encode($row[1])."</option>";
	}
	echo"</td>
		</tr>";

	echo"<table width='900' >
		</div>
	</td></tr>";
	}
echo"</table>";
}
function guardaRelacion($calificador,$calificado,$wemp_pmla,$wcco_calificado,$wcco_calificador,$wevaluacion)
{
    

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');


	$q=     "SELECT * "
			." FROM ".$wbasedato."_000008  "
			."WHERE Ajeucr = '".$calificador."'"
			."  AND Ajeuco = '".$calificado."' ";
	

	$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$numrowcom = mysql_num_rows($res);

	if ($numrowcom==0)
	{
	$fecha= date("Y-m-d");
	$hora = date("H:i:s"); 
		echo "inserta";
		
		$q= 	 " INSERT INTO ".$wbasedato."_000008 (Ajeucr,Ajeuco,Ajecco,Ajefor,Ajeccr,Medico,Fecha_data,Hora_data,Seguridad)"
					 ." VALUES ('".$calificador."',
								'".$calificado."',
								'".$wcco_calificado."',
								'".$wevaluacion."',
								'".$wcco_calificador."',
								'".$wbasedato."',
								'".$fecha."',
								'".$hora."',
								'C-".$wbasedato."') ";
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	}
	else
	{
	    echo "borro";
		$q   =   "DELETE "
		     .  "  FROM ".$wbasedato."_000008 "
		     .   " WHERE Ajeucr= '".$calificador."' "
			 .   "   AND Ajeuco= '".$calificado."' ";
		echo $q;
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	}
}
function actualizoRelacion ($calificador,$calificado,$wemp_pmla,$wcco_calificado,$wcco_calificador,$wevaluacion)
{
	

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');

	$q =          "UPDATE ".$wbasedato."_000008 "
                . "   SET Ajefor= '".$wevaluacion."'  "
                . " WHERE Ajeucr = '".$calificador."' "
                . "   AND Ajeuco = '".$calificado."'";

    $res = mysql_query($q,$conex)or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

}
function eliminarSeccion($calificador,$wemp_pmla,$wcco_calificado)
{

	

	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');

	$q   =   "DELETE "
		 .  "  FROM ".$wbasedato."_000008 "
		 .   " WHERE Ajeucr= '".$calificador."' "
		 .   "   AND Ajecco= ".$wcco_calificado." ";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

}
if (isset($consultaAjax) &&  $consultaAjax !='')
{
	if ($consultaAjax == "guardarelacion")
    {
      guardaRelacion($calificador,$calificado,$wemp_pmla,$wcco_calificado,$wcco_calificador,$wevaluacion);
    }
	if ($consultaAjax == "actualizorelacion")
	{
	 actualizoRelacion($calificador,$calificado,$wemp_pmla,$wcco_calificado,$wcco_calificador,$wevaluacion);
	}
	if ($consultaAjax == "eliminotodoccocalificador")
	{
	 eliminarSeccion($calificador,$wemp_pmla,$wcco_calificado);
	}
}
echo "<div id='ventanarelaciones' style='display:none;width:100%;cursor:default'>";
echo "<div id='ventanarelacionescont' ></div>";
echo"<INPUT TYPE='button' value='Salir' onClick='$.unblockUI();' style='width:100'>";
echo "</div>";
?>

</body>
</html>
</div>