<?php
include_once("conex.php");

		

		


		define("NOMBRE_BORRAR",'Eliminar');
		define("NOMBRE_ADICIONAR",'Adicionar');

		if(isset($accion))
		{
			global $wbasedato;
			
			
			$data = array('error'=>0,'html'=>'','mensaje'=>'');
			switch($accion)
			{
				case 'adicionar_fila':
				
					//se traen las causas 
							 	$sql = "select ".$wbasedato."_000002.Caucod,".$wbasedato."_000002.Cautip,Caneid,".$wbasedato."_000002.id, root_000091.Caudes 
											from ".$wbasedato."_000002,root_000091 
											where ".$wbasedato."_000002.Cautip='".$evento."' 
											and Caneid='".$id_evento."'
											and ".$wbasedato."_000002.Caucod=root_000091.Caucod
											and ".$wbasedato."_000002.Cautip=root_000091.Cautip
											";		
									$res = mysql_query( $sql ,$conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() ); 
				
						$data['html'] = '
										<tr id="'.$id_fila.'" class="fila2">
											<td>
											<input type="hidden" id="'.$id_fila.'_bd" name="'.$id_fila.'_bd" value="" >
											<SELECT NAME="'.$id_fila.'_causa" id="'.$id_fila.'_causa" onchange="">';
											$data['html'] .= '<option value="">Seleccione..</option>';
											for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
											{
												
												if( $clasificacion != trim( $rows['Caucod'] ) )
												{
													$data['html'] .= '
																	<option value="'.$rows['id'].'-'.$rows['Caucod'].'">'.utf8_encode($rows['Caudes']).'</option>';
												}
												else
												{
													$data['html'].= '
																	<option value="'.$rows['id'].'-'.$rows['Caucod'].'" selected>'.utf8_encode($rows['Caudes']).'</option>';
												}
											}			
											$data['html'] .= '</SELECT>';
											
											$data['html'] .= '</td>
											<td>
												<input type="hidden" id="'.$id_fila.'_id" name="'.$id_fila.'_id" value="" >
												<TEXTAREA name="'.$id_fila.'_accion" id="'.$id_fila.'_accion" rows="3" cols="50"></TEXTAREA>
											</td>
											<td><input type="text" id="'.$id_fila.'_fechar" name="'.$id_fila.'_fechar" value="" size="10">
												<BUTTON id="'.$id_fila.'_btnfechar" onclick="calendario(\'\',\'\',\''.$id_fila.'_fechar\',\''.$id_fila.'_btnfechar\');">...</BUTTON>
											</td>
											<td><input type="text" id="'.$id_fila.'_fechapr" name="'.$id_fila.'_fechapr" value="" size="10">
												<BUTTON id="'.$id_fila.'_btnfechapr" onclick="calendario(\'\',\'\',\''.$id_fila.'_fechapr\',\''.$id_fila.'_btnfechapr\');">...</BUTTON>
											</td>
											<td>
												
												<TEXTAREA name="'.$id_fila.'_seguimiento" id="'.$id_fila.'_seguimiento" rows="3" cols="50"></TEXTAREA>
											</td>
											<td>';
											//se listan los coordinadores
											$query="SELECT ajeucr, Ideno1, Ideno2, Ideap1, Ideap2
													FROM  ".$datos."_000008, ".$datos."_000013 
													WHERE ajecoo = 'on'
													AND Ideuse = Ajeucr
													AND Ideest='on'
													GROUP BY ajeucr
													order by Ideno1";
											$res2 = mysql_query($query) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
											
											$data['html'] .="<SELECT id='".$id_fila."_responsable' id='".$id_fila."_responsable' onchange=''>"; 
											$data['html'] .="<option value=''>Seleccione..</option>";
												for( $i = 0; $rows = mysql_fetch_array( $res2 ); $i++ )
												{
												   $nom=$rows['Ideno1']." ".$rows['Ideno2']." ".$rows['Ideap1']." ".$rows['Ideap2'];	
													if( $coordinadores != trim($nom) )
													{
														$data['html'] .= "
																		<option value='".$nom."'>".utf8_encode($nom)."</option>";
													}
													else
													{
														$data['html'] .= "
																		<option value='".$nom."' selected>".utf8_encode($nom)."</option>";
													}
												}
															
											$data['html'] .="</SELECT>";
											$data['html'] .='</td>';
											
											//se traen los estados   \"".$nom."\"
											$sql1 = "select Estcod,Estdes 
											from root_000093 
											where Esttip='NCA' 
											and Estest='on'
											
											";		
											$res1 = mysql_query( $sql1 ) or die( mysql_errno()." - Error en el query $sql1 - ".mysql_error() );
											$data['html'] .='<td>';			
											$data['html'] .=' <SELECT NAME="'.$id_fila.'_estado" id="'.$id_fila.'_estado" onchange="">';
											$data['html'] .='<option value="">Seleccione..</option>';
											for( $i = 0; $rows1 = mysql_fetch_array( $res1 ); $i++ )
											{
												
												if( $clasificacion != trim( $rows1['Estcod'] ) )
												{
													$data['html'] .='
																	<option value="'.$rows1['Estcod'].'">'.utf8_encode($rows1['Estdes']).'</option>';
												}
												else
												{
													$data['html'] .='
																	<option value="'.$rows1['Estcod'].'" selected>'.utf8_encode($rows1['Estdes']).'</option>';
												}
											}			
											$data['html'] .='</SELECT>'; 
											$data['html'] .='</td>
											<td align="center"><span class="efecto_boton" onclick="removerFila(\''.$id_fila.'\');">'.NOMBRE_BORRAR.'</span></td>
										</tr>';
										$data['mensaje']=$sql;
						echo json_encode($data);
					break;

				case 'guardar_planes':
						
						$filas = explode("|-|",$array_campos);
						$arr_inserts = array();
						$arr_update = array();
						$error_update = false;
						$qUpdate = '';
						foreach($filas as $key1 => $valores_campos)
						{
							//id_accion+"|"+id_plan+"|"+accion_val+"|"+fechar+"|"+fechapr+"|"+seguimiento+"|"+responsable+"|"+estado
							$campos = explode("|",$valores_campos); 
							// print_r($campos);
							$id_accion 	= $campos[0]; // Si esta en on es porque ya existe en base de datos
							$id_registro= $campos[1]; //id causa
							$accion_plan= utf8_decode($campos[2]);
							$fechar     = $campos[3];
							$fechapr    = $campos[4];
							$seguimiento= $campos[5];
							$responsable= $campos[6];
							$estado     = $campos[7];
							
							//se hace una consulta a la tabla usuarios para traer el nombre
							$queryU = "select Descripcion
									   from usuarios
									   where Codigo = '".$key."'";
							$resU = mysql_query($queryU) or die( mysql_errno()." - Error en el query $queryU - ".mysql_error() );
							if (mysql_num_rows($resU)>0)
							{
								$rowsU = mysql_fetch_array($resU);
								$nombre=$rowsU['Descripcion'];
								
							}
							
							/* concatenar seguimiento*/
							if ($seguimiento != "")
							{
								$fecha=date("Y-m-d");
								$hora=date("H:i:s");
								$pieComen="--------------------------------------";
								$cadena=utf8_decode( $nombre )." ".$fecha." ".$hora."\n".utf8_decode($seguimiento)."\n".$pieComen;
								$seguimiento=$cadena;
							}
							/* fin concatenar seguimiento*/
							
							
							if($id_accion != '')
							{
								//Actualiza la informacion del registro dependiendo del nuevo estado del seguimiento.
								$qUpdate = " UPDATE ".$wbasedato."_000003 
												SET Accdes='".$accion_plan."',
													Accfre='".$fechar."',
													Accfpr='".$fechapr."',													
													Accres='".$responsable."',
													Accest='".$estado."' 								
											  WHERE id='".$id_accion."'";
								if($res = mysql_query( $qUpdate ,$conex))
								{ }
								else
								{
									$error_update = true; //si no se ejecuta la consulta
								}
								
								if($seguimiento != ''){
								
								//Registro de seguimientos de forma individual. Jonatan Lopez
								$q_seg = "INSERT INTO ".$wbasedato."_000013 (     Medico,         Fecha_Data,        Hora_data,           Seguid,          Segfpr    ,       Segtex,      Segest,    Seguridad)
										                             VALUES ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s")."', ".$id_accion.", '".$fechapr."','".$seguimiento."','on','C-".$wbasedato."')";
								$res_seg = mysql_query( $q_seg ,$conex);
								
								}
								
							}
							else
							{
								//INSERT
								//id_accion+"|"+id_plan+"|"+accion_val+"|"+fechar+"|"+fechapr+"|"+seguimiento+"|"+responsable+"|"+estado
								$arr_inserts[] = "('".$wbasedato."','".date("Y-m-d")."', '".date("H:i:s")."','".$accion_plan."','".$fechar."','".$fechapr."','".$seguimiento."','".$responsable."','".$estado."','".$id_registro."','on','C-".$wbasedato."')";
								
							}
						}
						
						if($error_update)
						{
							$data['mensaje'] .= utf8_encode("\nNo se actualizaron algunos registros.");
							$data['error'] = 1;
						}
						
						if(count($arr_inserts) > 0)
						{
							$qInsert = "INSERT 	INTO ".$wbasedato."_000003
												(Medico,Fecha_Data,Hora_data,Accdes,Accfre,Accfpr,Accseg,Accres,Accest,Accaid,Accesa,Seguridad)
										VALUES	".implode(",",$arr_inserts).';';
										
							// $res = mysql_query( $qInsert ,$conex) or die( mysql_errno()." - Error en el query $qInsert - ".mysql_error() ); 
							if($res = mysql_query( $qInsert ,$conex))
							{ }
							else
							{
								$data['mensaje'] .= utf8_encode("\nNo se insertaron registros nuevos"); //si no se ejecuta la consulta
								$data['error'] = 1;							
							}							
							
							if($seguimiento != ''){
							
							//Registro de seguimientos de forma individual. Jonatan Lopez
							$q_seg = "INSERT INTO ".$wbasedato."_000013 (     Medico,         Fecha_Data,        Hora_data,           Seguid,          Segfpr    ,     Segtex       , Segest,  Seguridad)
																 VALUES ('".$wbasedato."','".date("Y-m-d")."','".date("H:i:s").", '".$id_evento."','".$fechapr."','".$seguimiento."',  'on' ,'C-".$wbasedato."')";
							$res_seg = mysql_query( $q_seg ,$conex);
							
							}
						}
						
						if ($data['error'] == 0)
						{ $data['mensaje'] .= utf8_encode("Se guardó correctamente "); }
						echo json_encode($data);
					break;
					
				case 'eliminar_planes':
				
					//se actualiza el estado(activo) de la accion a off para que no lo muestre 
						$sql = "UPDATE ".$wbasedato."_000003 
							SET  Accesa = 'off'  
							WHERE Id='".$id_eliminar."' ";
													
						$res = mysql_query( $sql ,$conex) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() ); 
				
						//$data['html'] = '';
						//$data['mensaje']=$sql;
						echo json_encode($data);
					break;

				default:
					break;
			}

			return;
		}

		
		
		?>
		<html>
		<head>
		<title>Acciones correctivas</title>
			<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
			<script type="text/javascript">
			function addFila(tabla_referencia,key,wbasedato,datos,wemp_pmla,cco,id_evento,evento)
			{
				
				// para saber si la tabla tiene filas o no
				trs = $("#"+tabla_referencia).find('tr[id$=tr_'+tabla_referencia+']').length;
				var value_id = 0;

				//busca consecutivo mayor
				if(trs > 0)
				{ 
					id_mayor = 0;
					// buscar los tr que terminen en el mismo nombre de su tabla contenedora, recorrerlos y recuperar el valor mayor
					$("#"+tabla_referencia).find('tr[id$=tr_'+tabla_referencia+']').each(function() {
						id_ = $(this).attr('id');
						id_splt = id_.split('_');
						id_this = (id_splt[0])*1; 
						if(id_this >= id_mayor)
						{
							id_mayor = id_this; 
						}
					});
					id_mayor++;
					value_id = id_mayor+'_tr_'+tabla_referencia;
					
				}
				else
				{ value_id = '1_tr_'+tabla_referencia; }

				$.post("acciones.php",
					{
						accion      :'adicionar_fila',
						consultaAjax:'',
						id_fila     :value_id, 
						key			:key,
						wbasedato	:wbasedato,
						datos		:datos,
						wemp_pmla   :wemp_pmla,
						cco         :cco,
						id_evento    :id_evento,
						evento		:evento
					},
					function(data){
						if(data.error == 1)
						{
							alert(data.mensaje);
						}
						else
						{
							
							$("#tabla_planes > tbody").append(data.html);
							// var fechar = $("#"+id_fila+"_fechar").val();
							// var btnfechar = $("#"+id_fila+"_btnfechar").val();
							// var fechapr = $("#"+id_fila+"_fechapr").val();
							// var btnfechapr = $("#"+id_fila+"_btnfechapr").val();
							var f = new Date();
							$("#"+value_id+"_fechar").val(f.getFullYear() + "-" + (getMonth(f.getMonth()+1)) + "-" + getMonth(f.getDate()));	
							$("#"+value_id+"_fechapr").val(f.getFullYear() + "-" + (getMonth(f.getMonth()+1)) + "-" + getMonth(f.getDate()));
							//calendario("","","","");
							//calendario("","", fechapr, btnfechapr);
							//alert(data.mensaje);
						}
					},
					"json"
				);
			}

			function removerFila(id_fila,wbasedato)
			{							  
				 
				var id_eliminar = $( "#"+id_fila+"_bd" ).val( );
				//alert(id_eliminar+" entro");
				acc_confirm = 'Confirma que desea eliminar?';
				if(confirm(acc_confirm))
				{ 
					if(id_eliminar != '')
					{
						$.post("acciones.php",
							{
								accion      : 'eliminar_planes',
								consultaAjax: '',
								id_eliminar : id_eliminar,
								wbasedato 	: wbasedato
							},
							function(data){
								if(data.error == 1)
								{
									alert(data.mensaje);
								}
								else
								{
									alert(data.mensaje);
								}
							},
							"json"
						);
					}
					$("#"+id_fila).empty();
					$("#"+id_fila).remove();
				}
			}
					
			function guardarPlanes(tabla_referencia,key,wbasedato,datos,wemp_pmla,cco,evento)
			{
				
				trs = $("#"+tabla_referencia).find('tr[id$=tr_'+tabla_referencia+']').length;
				var validacion = true;
				//busca consecutivo mayor
				if(trs > 0)
				{
					var campos = '';
					var separador_bloque = '';
					$("#"+tabla_referencia).find('tr[id$=tr_'+tabla_referencia+']').each(function() {
						var id_tr = $(this).attr('id'); // El this es el id de cada fila de la tabla
						
						var id_accion  = $("#"+id_tr+"_bd").val(); //id de la accion que trae la bd
						var accion_val  = $("#"+id_tr+"_accion").val();
						var fechar      = $("#"+id_tr+"_fechar").val();
						var fechapr     = $("#"+id_tr+"_fechapr").val();
						var seguimiento = $("#"+id_tr+"_seguimiento").val();
						var responsable = $("#"+id_tr+"_responsable").val();
						var estado = $("#"+id_tr+"_estado").val();
						
						var causa = $("#"+id_tr+"_causa").val();
						var ex = causa.split("-");
						var id_plan = ex[0];
						//var causa_cod = ex[1];  //si se necesita mandar el codigo de la causa

						campos = campos+separador_bloque+id_accion+"|"+id_plan+"|"+accion_val+"|"+fechar+"|"+fechapr+"|"+seguimiento+"|"+responsable+"|"+estado;
						separador_bloque = '|-|';
						
						
						if(accion_val == "" || fechar == "" || fechapr == "" || responsable == "" || estado == "" || causa == "")
						{ validacion = false; }
						
					});

					if(validacion)
					{
						$.post("acciones.php",
							{
								accion      : 'guardar_planes',
								consultaAjax: '',
								array_campos: campos,
								wbasedato 	: wbasedato,
								wemp_pmla 	: wemp_pmla,
								evento 		: evento,
								key			: key
							},
							function(data){
								if(data.error == 1)
								{
									alert(data.mensaje);
								}
								else
								{
									alert(data.mensaje);
									document.location.reload(true);
									//hacer un refresh para que carguen los ids con los que se guardó;
								}
							},
							"json"
						);
					}
					else
					{
						alert("Fantan campos por llenar");
					}
				}
			}

function getMonth(month) {
		//var month = date.getMonth();
		return month < 10 ? '0' + month : month; 
	}
			
window.onload = function()
	{
		
		var f = new Date();
		var num_fila = $("#num_fila").val();
		// alert(num_fila);
		if ( !num_fila ) //si existe
		{
		 $("#1_tr_tabla_planes_fechar").val(f.getFullYear() + "-" + (getMonth(f.getMonth()+1)) + "-" + getMonth(f.getDate()));	
		 $("#1_tr_tabla_planes_fechapr").val(f.getFullYear() + "-" + (getMonth(f.getMonth()+1)) + "-" + getMonth(f.getDate()));	
		
		}
		
	}	
			
/***************************************************************************************************************************
* Invocación generica del calendario para fecha inicial de suministro
****************************************************************************************************************************/
// function calendario(idx,tipoProtocolo, fechar, btnfechar){
       // Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'24',electric:false,inputField:fechar+tipoProtocolo+idx,button:btnfechar+tipoProtocolo+idx,ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d',timeInterval:1,dateStatusFunc:'',onSelect:alSeleccionarFecha});                
// }
function calendario(idx,tipoProtocolo,fechar,btnfechar){
       Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'24',electric:false,inputField:fechar+tipoProtocolo+idx,button:btnfechar+tipoProtocolo+idx,ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d',timeInterval:1,dateStatusFunc:'',onSelect:alSeleccionarFecha});                
}

function alSeleccionarFecha( date, stringFecha ){
//%Y-%m-%d a las:%H:00:00
	if( date.dateClicked ){
	   date.params.inputField.value = date.currentDate.print(date.params.ifFormat);
	   //document.getElementById( "hora" ).value = date.currentDate.print( "%H:%M:00" );
	   if( date.params.inputField.onchange ){
		   date.params.inputField.onchange();
	   }
	   date.callCloseHandler();
   }
   else{
	   date.params.inputField.ultimaFechaSeleccionada = date.currentDate;
   }
}
			</script>

			<style type="text/css">
				.efecto_boton{
					cursor:pointer;
					border-bottom: 1px solid orange;
					color:orange;
					font-weight:bold;
				}
			</style>

		</head>
		<body>
<?php

/********************************************************************************************************
 * INICIO DEL PROGRAMA
 *******************************************************************************************************/

include_once("root/comun.php");
//

//

	
$conex = obtenerConexionBD("matrix");

$wactualiz="(2013-05-02)";


if( !isset($wemp_pmla) ){
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}
//$key = substr($user, 2, strlen($user)); //04760-admi   04910-magda  04851-alejo 22701 johana gil 05970 coor cardiologia
//santiago-33985  luis mercadeo-03500
//$key ='05970';

$conex = obtenerConexionBD("matrix");

//$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

//$wbasedato = strtolower( $institucion->baseDeDatos );
$wentidad = $institucion->nombre;

//$datos= consultarAliasPorAplicacion($conex, $wemp_pmla, "talhuma");
//$cco= consultarAliasPorAplicacion($conex, $wemp_pmla, "tabcco");
//$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, "gescal");
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$wbasedato1 = strtolower( $institucion->baseDeDatos );


$wfec=date("Y-m-d");

session_start();  
//el usuario se encuentra registrado
if(!isset($_SESSION['user']))
    echo "error";
else
{

/* Modificaciones:
Mayo 05 de 2014 Jonatan
Se seperan los seguimiento a una accion correctiva y se registran en la tabla gescal_000013.
******************************************************************************
23 de diembre de 2013 
Se hace la modificacion de la consulta de los roles de usuarios que administran el software porque se creo una tabla nueva de roles gescal_000007
y la que se tenia antes ahora es la union de los roles con los usuarios. Viviana Rodas.

*/

/**********funciones*********/
//se consulta si el usuario tiene rol para poder mostrar el boton de guardar acciones y los links
function consultarRoles()
		{
			global $wbasedato;
			global $key;
			global $evento;
			
			 $q="select Rnccod,Roldes
			FROM ".$wbasedato."_000007,".$wbasedato."_000004
			WHERE Rnctip ='".$evento."'
			AND Rncest ='on'
			AND Rnccod = Rolcod
			AND Rolest ='on'
			AND Roltip =Rnctip
			AND Roldes = '".$key."'";
			
			if ($evento == 'NC')
			{
				$q.=" AND Rncanc='on'";
			}
			else
			{
				$q.=" AND Rncaea='on'";	
			}
			
			$res = mysql_query($q) or die( mysql_errno()." - Error en el query $q - ".mysql_error() );
			$num = mysql_num_rows($res);
			return $num;
		}
//funcion que lista los coordinadores de las unidades		
function listarCoordinadores($id_fila)
{
	global $datos;
	global $key;
	global $evento;
	global $resp;
	
	$query="SELECT ajeucr, Ideno1, Ideno2, Ideap1, Ideap2
			FROM  ".$datos."_000008, ".$datos."_000013 
			WHERE ajecoo = 'on'
			AND Ideuse = Ajeucr
			AND Ideest='on'
			GROUP BY ajeucr
			order by Ideno1";
	$res = mysql_query($query) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
	
	echo"<SELECT id='".$id_fila."_responsable' id='".$id_fila."_responsable' onchange=''>"; 
	echo"<option value=''>Seleccione..</option>";
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
		{
		   $nom=$rows['Ideno1']." ".$rows['Ideno2']." ".$rows['Ideap1']." ".$rows['Ideap2'];	
			if( $resp == trim($nom) )
			{
				echo "<option value='".$nom."' selected>".utf8_encode($nom)."</option>";
			}
			else
			{
				echo "<option value='".$nom."'>".utf8_encode($nom)."</option>";
			}
		}
					
	echo"</SELECT>";
	
	
	
	
}
/**********fin funciones*****/

encabezado("ACCIONES CORRECTIVAS Y/O OPORTUNIDADES DE MEJORAMIENTO ",$wactualiz, "".$wbasedato1);
$num1=consultarRoles();
if ($num1>0)
{
	$habilitar="";
}
else
{
	$habilitar='style="display:none"';
}

// echo $evento."-".$id_evento."-".$est;
// $coor=listarCoordinadores(1);	
// echo "coor".$coor."";	
		echo"<table id='tabla_planes' style='width:132%;' border='0'>";
			echo"<tr class='encabezadoTabla'>";
				echo"<td width='10%'>Causa</td>";
				echo"<td width='25%'>Acci&oacute;n</td>";
				echo"<td width='15%'>Fecha</td>"; //width='100%'
				echo"<td width='15%'>Fecha prox.</td>";
				echo"<td width='25%'>Seguimiento</td>";
				echo"<td width='10%'>Responsable</td>";
				echo"<td width='8%'>Estado</td>";
				echo"<td $habilitar><span onclick=\"addFila('tabla_planes','".$key."','".$wbasedato."','".$datos."','".$wemp_pmla."','".$cco."','".$id_evento."','".$evento."');\" class='efecto_boton' >".NOMBRE_ADICIONAR."</span></td>";
				
				//se traen las causas 
				 $sql = "select ".$wbasedato."_000002.Caucod,".$wbasedato."_000002.Cautip,Caneid,".$wbasedato."_000002.id AS id_causa, root_000091.Caudes 
							from ".$wbasedato."_000002,root_000091 
							where ".$wbasedato."_000002.Cautip='".$evento."' 
							and Caneid='".$id_evento."'
							and ".$wbasedato."_000002.Caucod=root_000091.Caucod
							and ".$wbasedato."_000002.Cautip=root_000091.Cautip";		
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() ); 
				
				//se traen los estados 
				$sql1 = "select Estcod,Estdes 
							from root_000093 
							where Esttip='NCA' 
							and Estest='on'";		
				$res1 = mysql_query( $sql1, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
					
				//se consultan todas las acciones
			     $sql2="
						SELECT 	".$wbasedato."_000002.Caucod,".$wbasedato."_000002.Cautip,Caneid,".$wbasedato."_000002.id AS id_causa, root_000091.Caudes,
								Accdes, Accfre, Accfpr, Accseg, Accres, Accest, Accaid,".$wbasedato."_000003.id AS id_solucion
						FROM 	".$wbasedato."_000002,root_000091, ".$wbasedato."_000003
						WHERE 	".$wbasedato."_000002.Cautip='".$evento."' 
								AND Caneid='".$id_evento."'
								AND ".$wbasedato."_000002.Caucod=root_000091.Caucod
								AND ".$wbasedato."_000002.Cautip=root_000091.Cautip
								AND Accaid = ".$wbasedato."_000002.id
								AND ".$wbasedato."_000003.Accesa='on'
								order by ".$wbasedato."_000003.Fecha_data";
				$res2 = mysql_query( $sql2, $conex ) or die( mysql_errno()." - Error en el query $sql2 - ".mysql_error() );
				
				
				//Imprime todas las filas leídas desde la base de datos
				$cont = 1;  //para incrementar las filas
				if(mysql_num_rows($res2)>0)  
				{
					for( $i = 0; $rows2 = mysql_fetch_array( $res2 ); $i++ )
					{
						$resp=$rows2['Accres'];
						$id_fila = $cont."_tr_tabla_planes";
						
						echo '
						<tr id="'.$id_fila.'" class="fila2">
							<td>
								<input type="hidden" id="'.$id_fila.'_bd" name="'.$id_fila.'_bd" value="'.$rows2['id_solucion'].'" >';								
								echo'<SELECT NAME="'.$id_fila.'_causa" id="'.$id_fila.'_causa" onchange="">';
								echo'<option value="">Seleccione..</option>';
										
										for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
										{
											if($rows2['Accaid'] == $rows['id_causa'])
											{
												echo '
														<option value="'.$rows['id_causa'].'-'.$rows['Caucod'].'" selected="selected">'.utf8_encode($rows['Caudes']).'</option>';
											}
											else
											{
												echo '
														<option value="'.$rows['id'].'-'.$rows['Caucod'].'">'.utf8_encode($rows['Caudes']).'</option>';
											}
										}			
								echo'</SELECT>';   
						
							echo'</td>';
							echo'<td>';
							if($rows2['Accseg'] != "")
							{
								echo'<TEXTAREA name="'.$id_fila.'_accion" id="'.$id_fila.'_accion" rows="7" cols="50">'.$rows2['Accdes'].'</TEXTAREA>';
							}
							else
							{
								echo'<TEXTAREA name="'.$id_fila.'_accion" id="'.$id_fila.'_accion" rows="3" cols="50">'.$rows2['Accdes'].'</TEXTAREA>';
							}
							echo'</td>'; 
							echo'<td ><input type="text" id="'.$id_fila.'_fechar" name="'.$id_fila.'_fechar" value="'.$rows2['Accfre'].'" size="10">
									<BUTTON id="'.$id_fila.'_btnfechar" onclick="calendario(\'\',\'\',\''.$id_fila.'_fechar\',\''.$id_fila.'_btnfechar\');">...</BUTTON>
								</td>
								<td ><input type="text" id="'.$id_fila.'_fechapr" name="'.$id_fila.'_fechapr" value="'.$rows2['Accfpr'].'" size="10">
									<BUTTON id="'.$id_fila.'_btnfechapr" onclick="calendario(\'\',\'\',\''.$id_fila.'_fechapr\',\''.$id_fila.'_btnfechapr\');">...</BUTTON>
								</td>';
							
							$sql_text = " SELECT Segtex
										    FROM ".$wbasedato."_000013
										   WHERE Seguid = '".$rows2['id_solucion']."'
										     AND Segest='on'
									    ORDER BY Fecha_data";
							$res_text = mysql_query( $sql_text, $conex ) or die( mysql_errno()." - Error en el query $sql_text - ".mysql_error() );
							$wseguimiento = '';
							while($row_text = mysql_fetch_array($res_text)){
							
									$wseguimiento .= "\n".$row_text['Segtex']."\n";
							}
							
							echo '<td>';
							
							if($rows2['Accseg'] != "" or $wseguimiento != '')
							{
								echo'<TEXTAREA name="'.$id_fila.'_seguimiento_ant" id="'.$id_fila.'_seguimiento_ant" rows="3" cols="50" readonly>'.$rows2['Accseg'].$wseguimiento.'</TEXTAREA><br>';
							}
							echo'<TEXTAREA name="'.$id_fila.'_seguimiento" id="'.$id_fila.'_seguimiento" rows="3" cols="50"></TEXTAREA>
							</td>';
							echo'<td>';					
							listarCoordinadores($id_fila);							
							echo'</td>';
							
							echo'<td>';
							echo'<SELECT NAME="'.$id_fila.'_estado" id="'.$id_fila.'_estado" onchange="">';
							echo'<option value="">Seleccione..</option>';
										for( $i = 0; $rows1 = mysql_fetch_array( $res1 ); $i++ )
										{
											if($rows2['Accest'] == $rows1['Estcod'])
											{
												echo '
														<option value="'.$rows1['Estcod'].'" selected="selected">'.utf8_encode($rows1['Estdes']).'</option>';
											}
											else
											{
												echo '
														<option value="'.$rows1['Estcod'].'">'.utf8_encode($rows1['Estdes']).'</option>';
											}
										}			
							echo'</SELECT>';
							echo"</td>
							<td $habilitar align='center' ><span class='efecto_boton' onclick='removerFila(\"".$id_fila."\",\"".$wbasedato."\");'>".NOMBRE_BORRAR."</span></td>
						</tr>";
						$cont++;
						mysql_data_seek($res, 0);
						mysql_data_seek($res1, 0);
						
					}//for 
					echo'<input type="hidden" id="num_fila" name="num_fila" value="'.$cont.'" >';
				}
				else  //si no tiene registros en la tabla gescal_03 acciones
				{
					$id_fila = "1_tr_tabla_planes";
														
					echo '
					<tr id="'.$id_fila.'" class="fila2">
					
						<td>	
							<input type="hidden" id="'.$id_fila.'_bd" name="'.$id_fila.'_bd" value="" >
							<SELECT NAME="'.$id_fila.'_causa" id="'.$id_fila.'_causa" onchange="">';
								echo'<option value="" selected>Seleccione..</option>';
											for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ )
											{
												echo '
																<option value="'.$rows['id_causa'].'-'.$rows['Caucod'].'" >'.utf8_encode($rows['Caudes']).'</option>';												
											}			
								echo'</SELECT>';
						echo'</td>
						<td>
							<TEXTAREA name="'.$id_fila.'_accion" id="'.$id_fila.'_accion" rows="3" cols="50"></TEXTAREA>
						</td>
						<td><input type="text" id="'.$id_fila.'_fechar" name="'.$id_fila.'_fechar" value="" size="10">
							<BUTTON id="'.$id_fila.'_btnfechar" onclick="calendario(\'\',\'\',\''.$id_fila.'_fechar\',\''.$id_fila.'_btnfechar\');">...</BUTTON>
						</td>
						<td><input type="text" id="'.$id_fila.'_fechapr" name="'.$id_fila.'_fechapr" value="" size="10">
							<BUTTON id="'.$id_fila.'_btnfechapr" onclick="calendario(\'\',\'\',\''.$id_fila.'_fechapr\',\''.$id_fila.'_btnfechapr\');">...</BUTTON>
						</td>
						<td><TEXTAREA name="'.$id_fila.'_seguimiento" id="'.$id_fila.'_seguimiento" rows="3" cols="50"></TEXTAREA></td>';
						echo'<td>';
						listarCoordinadores($id_fila);
						echo'</td>';
						
						//se traen los estados 
							 	$sql1 = "select Estcod,Estdes 
											from root_000093 
											where Esttip='NCA' 
											and Estest='on'
											
											";		
									$res1 = mysql_query( $sql1, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
						echo'<td>';			
						
						echo'<SELECT NAME="'.$id_fila.'_estado" id="'.$id_fila.'_estado" onchange="">';
								echo'<option value="">Seleccione..</option>';
											for( $i = 0; $rows1 = mysql_fetch_array( $res1 ); $i++ )
											{
												
												if( $clasificacion != trim( $rows1['Estcod'] ) )
												{
													echo '
																	<option value="'.$rows1['Estcod'].'">'.utf8_encode($rows1['Estdes']).'</option>';
												}
												else
												{
													echo '
																	<option value="'.$rows1['Estcod'].'" selected>'.utf8_encode($rows1['Estdes']).'</option>';
												}
											}			
								echo'</SELECT>';
						echo'</td>
						<td align="center"><span class="efecto_boton" onclick="removerFila(\''.$id_fila.'\',\''.$wbasedato.'\');">'.NOMBRE_BORRAR.'</span></td>
					</tr>';
				}
				
			echo"</tr>";
		echo"</table>";
		
		
		
		$num=consultarRoles(); 
		if ($num>0)
		{
			if ($est != '06')
			{
				echo'<input type="button" id="guardar" name="guardar" value="Guardar acciones" onclick="guardarPlanes(\'tabla_planes\',\''.$key.'\',\''.$wbasedato.'\',\''.$datos.'\',\''.$wemp_pmla.'\',\''.$cco.'\',\''.$id_evento.'\',\''.$evento.'\');">';
			}
		}
		
		
		echo"<br><br><br><center><input name='button' type='button' style='width:100' onclick='window.close();' value='Cerrar' /></center>";
		echo"</body>";
		echo"</html>";
}
?>