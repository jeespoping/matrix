<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");

if(isset($accion) and $accion == 'consultaObsercaciones')
{
			$data= array( 'error'=>0, 'mensaje'=>'');			
			
			$query = "select Fecha_I, Fecha_F, Codigo, Control  
						  from ".$wbasedato."_000012 
						  where Fecha_I <= '".$wfec."'
						  and Fecha_F >= '".$wfec."'
						  and Codigo = 'Todos'
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
						$data['mensaje']="No se pudo realizar la consulta a la tabla ".$wbasedato."_000021";
					}
	echo json_encode($data);
	return;
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<link rel='stylesheet' href='../../../include/root/matrix.css'/>
<title>MATRIX</title>
<link type="text/css" href="../../../include/root/ui.all.css" rel="stylesheet" />        <!-- Nucleo jquery -->
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript">
<!--

$(document).ready(function() {
	
	buscarObservaciones();
	$('input#buscarCitas').focus().quicksearch('table#tbCitas tbody tr');

});

function buscarObservaciones()
{ 
	var wbasedato=$("#wbasedato").val();
	var wfec=$("#wfec").val();
	
	
	$.post("dispMedicos.php",
	{
		accion      :"consultaObsercaciones",
		consultaAjax:'',
		wbasedato   :wbasedato,
		wfec        :wfec
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
	
//-->
</script>
<style type="text/css">
.festivo
{
	background-color:#00CCCC;
	font-weight:bold;
}

.div_error 
{
    width:500px; 
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
<?php
/*
Creacion: 2012-08-10  Este script se crea para mostrar la lista de medicos disponibles para las citas de caso 2 que son las de medicos, se muestra la lista de medicos de una fecha seleccionada en el calendario, al nombre del medico se le puede dar clic y lleva a la agenda para la asignacion de citas correspondientes a dicho medico, esta pagina se recarga cada 30 segundos para que se puedan visualizar los cambios en las citas asignadas. Viviana Rodas
Modificacion:
            2020-03-25 Arleyda Insignares. Se adiciona campo Sedcod y tabla root_000128 para adicionar sede a la asignación de las citas
			2020-01-20 Arleyda Insignares. Se adiciona input para busqueda por texto en la tabla que contiene el listado de médicos.
			se ubica 'retornar' en la parte superior
            2018-09-11 Arleyda Insignares. Se unifican dos consultas a la tabla 'prefijo_000009', con el objetivo de optimizar la 
            consulta de disponibilidad de las citas.
			2014-02-04 Se modifica la consulta a la tabla de horarios de los medicos: Se agrega union a la tabla de excepciones para que liste los medicos tiene excepciones ese dia. Viviana Rodas
			2013-11-26 Se modifica el script para mostrar las observaciones de todos los medicos creadas desde el programa de excepciones. Viviana Rodas
			2013-09-03 Se modifica el script para calcular el total de citas cuando se tienen los horarios divididos, ya sea de excepciones o de horario normal. Viviana Rodas
			2013-05-16 Se modifica el script con la variable fest que es la que valida si se asignan citas los dias festivos, si esta en on, 
							  es porque si se asignan citas los festivos en esa unidad.
			2013-03-06 Se agrega en la consulta de la tabla 10 de horarios y la tabla 14 de estructura la validacion de unihora != 0. Viviana Rodas
			2012-11-27 Se hace la verificacion de los dias festivos en la tabla root_63 para imprimir la lista de los medicos o no tambien se agregan las 
					   variables month y year al link de retornar para que el calendario vuelva a mes del cual se llamo anteriormente. Viviana Rodas
			2012-10-31 Se cambia la funcion disponibilidadMedicos para evaluar cuando en un dia del calendario no hay atencion, para que muestre ese dia en color rojo y muestre un mensaje diciendo no hay atencion, se agrega boton de cerrar y link de retornar. Viviana Rodas
			2012-10-30 Se inicializo total citas en 0 dentro del for grande y tiempo gastado en 0  despues de la consulta de hay citas.
			Viviana Rodas
              
*/
include_once("root/comun.php");


// session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{	
    $conex = obtenerConexionBD("matrix");
    $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
    // $wbasedato = strtolower( $institucion->baseDeDatos );

	echo "<center><div class='div_error' style='display:none;'><div id='observacion' style='margin:15px;' align='center'></div></div></center><br>";

	
	//************************funcion******************** 
    function disponibilidadMedicos($wfec, $nomdia, $colorDiaAnt, $wemp_pmla)
	{
				
	    global $conex;
		global $totalCitas;
		global $citasAsig;
		global $citasDisp;
		global $wbasedato;
		global $amarillo;
		global $rojo;
		global $verde;
		global $caso;
		global $fest;
		
		$amarillo=0;
		$rojo=0;
		$verde=0;
		$j=0;
		$tiempoGastado = 0;
					
		
	
	//Se consulta la tabla de festivos 
	$sql="select Fecha from root_000063 where Fecha = '".$wfec."' ";
	$errs = mysql_query($sql,$conex)or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );		
	$nums = mysql_num_rows($errs);
	if ($nums > 0 and $fest == 'off')
	{ 
		
		echo "<br>";
		echo "<br>";        
		echo "<table align='left' border='0'>";
		echo "<th colspan='2' class='encabezadotabla'><font size='4'>No se asignan citas, dia Festivo</font></th>";
		echo "</table>";
	}
	else
	{

		echo "<div align=left >".devolverFecha($wfec)."<br><br><input type='text' style='background-color:#e8eef7;font-family: Arial;width:500px;hight:200px;font-size:15px;border: 4px solid #c3d9ff;
  border-radius: 5px;font-weight:bold;' id='buscarCitas' placeholder='Digite Especialidad o Nombre a buscar...'></div>";
		echo "<br>";
		echo "<table id ='tbCitas' align='left' border='0'>";
		echo "<thead>";
				 
				 $query="Select Codigo, Descripcion, Sednom 
						  From ".$wbasedato."_000010 left join root_000128 
		                    on root_000128.Sedcod = ".$wbasedato."_000010.Sedcod 
						  Where Activo = 'A'
						    and Dia='".$nomdia."' 
						 union			  
						 SELECT a.Codigo, b.Descripcion, Sednom 
							 From ".$wbasedato."_000012 a
							 Inner join ".$wbasedato."_000010 b
							     on a.Codigo = b.Codigo
							 Left join root_000128 c
			                     on c.Sedcod = b.Sedcod 
							 WHERE a.Fecha_I <= '".$wfec."'
							   AND a.Fecha_F >= '".$wfec."'
							   AND a.Activo = 'A'
							   AND a.Uni_hora !=1
							   AND a.Codigo != 'Todos'
						  Group by Codigo, Descripcion
						  Order by Descripcion";
		
		$err = mysql_query($query,$conex) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );	
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			if ($nums>0)
			{
				echo "<tr><td colspan='2' class='festivo' align='center'>ESTE DIA ES FESTIVO</td></tr>";
			}
			echo "<tr><td colspan='2' class='encabezadotabla' align='center'>Citas del Dia</td><td class='encabezadotabla' align='center'>Sede</td></tr>";
			echo "</thead>";
			echo "<tbody>";
			
			for( $i = 0; $rows = mysql_fetch_array( $err ); $i++ )
			{
				
				$totalCitas = 0;
				$tiempoGastado = 0; //se copio de dispEquipos
				
				//Definiendo la clase por cada fila
					if( $j%2 == 0 ){
						$class = "class='fila1'";
					}
					else{
						$class = "class='fila2'";
					}
				
				
				$cod  =$rows['Codigo'];
				$des  =$rows['Descripcion'];
				$sede =$rows['Sednom'];				
				$wequ =$cod."-".$des; 
				$codigo = $cod;
								
				//consulta si tiene excepciones
			 	$query = "select Uni_hora, TIME_FORMAT( CONCAT(Hi,'00'), '%H:%i:00') as Hi, TIME_FORMAT( CONCAT(Hf,'00'), '%H:%i:00') as Hf, Consultorio  
						  from ".$wbasedato."_000012 
						  where Fecha_I <= '".$wfec."'
						  and Fecha_F >= '".$wfec."'
						  and Codigo = '".substr($wequ,0,strpos($wequ,"-"))."'
						  and Activo = 'A'
						  and Uni_hora != 1
						  and Codigo != 'Todos'
						  order by Hi";
				$err1 = mysql_query($query,$conex) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );	
				$num1 = mysql_num_rows($err1);
				if ($num1>0)  //tiene excepciones
				{		   
				 $rows1 = mysql_fetch_array($err1);
					
					 while( $rows1 ){  //mientras sea true
					
					   $exUniHora=$rows1['Uni_hora'];
					   $exHi=$rows1['Hi'];
					   $exHf=$rows1['Hf'];
					   $exConsul=$rows1['Consultorio'];
					  
						if ($exUniHora == 0)
						{
							$rows1 = mysql_fetch_array($err1);
							
							if( !$rows1 ){
							
							$j++;
							$rojo++;
							echo "<tr $class>";
							echo "<td><font size='2'>".$wequ."</font></td> ";
							echo "<td><font size='2'> No atiende este dia <font></td></tr>";
							}
							else{
								continue; 
							}
						}
						else
						{
						
							//calculo total citas 
							$hi1=strtotime($exHi);
							$hf1=strtotime($exHf);
							$horasAtencion=$hf1-$hi1;
							$totalCitas+=$horasAtencion/($exUniHora*60);
							
							//consulta las citas de esa fecha
							$query = "select Cod_equ, Cod_exa, Fecha, Hi, Hf, Cedula, Nom_pac, Nit_res, Asistida, Activo, Atendido
									  from ".$wbasedato."_000009
									  where Cod_equ= '".$codigo."'
									    and Fecha = '".$wfec."'
									    and Activo = 'A' ";
									  
									  $err4   = mysql_query($query,$conex)or die( mysql_errno()." - Error en el query $query - ".mysql_error() );	
									  $citasAsig = mysql_num_rows($err4);
									  									  
									  if ($citasAsig>0) //tiene citas para asignar
										{
											for( $i = 0; $rows4 = mysql_fetch_array($err4); $i++ )
											{
												$fechaCita=$rows4['Fecha'];
												$HiCita=$rows4['Hi'];
												$HfCita=$rows4['Hf'];
												$cedula=$rows4['Cedula'];
												
												
												//se hace el calculo del total de tiempo gastado en una cita
												$hiCitaUnix=strtotime($HiCita);
												$hfCitaUnix=strtotime($HfCita);

												if( strtotime( $exHi ) <= $hiCitaUnix && strtotime( $exHf ) >= $hfCitaUnix )

													$tiempoGastado+=($hfCitaUnix-$hiCitaUnix)/($exUniHora*60)-1;
											}															
											   
										}
	
							$rows1 = mysql_fetch_array($err1);
							
							if( !$rows1 ){
								if ($citasAsig==0)  //todas las citas disponibles
								{
									$j++;
									$verde++;
									echo "<tr $class>";
									echo "<td><font size='2'><a href='agendaMedicos.php?empresa=$wbasedato&wfec=$wfec&wequ=$wequ&nomdia=$nomdia&colorDiaAnt=$colorDiaAnt&caso=$caso&wemp_pmla=$wemp_pmla' >".$wequ."</a></font></td> ";
									echo "<td><font size='2'> Todas las citas disponibles <font></td>";
									echo "<td></td></tr>";
								}
								else
								{
													
										$citasDisp=$totalCitas-$citasAsig-$tiempoGastado;
										
										
										if ($citasDisp==0)
										{
										    $j++;
											$rojo++;
											echo "<tr $class>";
											echo "<td><font size='2'><a href='agendaMedicos.php?empresa=$wbasedato&wfec=$wfec&wequ=$wequ&nomdia=$nomdia&colorDiaAnt=$colorDiaAnt&caso=$caso&wemp_pmla=$wemp_pmla' > ".$wequ."</a></font></td> ";
											echo "<td><font size='2'>No tiene citas disponibles</font></td>";
											echo "<td><font size='2'>".$sede." </td>";
											echo "</tr>";
										
										}
										else
										{
											$j++;
											$amarillo++;
											echo "<tr $class>";
											echo "<td><font size='2'><a href='agendaMedicos.php?empresa=$wbasedato&wfec=$wfec&wequ=$wequ&nomdia=$nomdia&colorDiaAnt=$colorDiaAnt&caso=$caso&wemp_pmla=$wemp_pmla' >".$wequ."</a></font></td> ";
											 echo "<td>Total Citas: ".(floor($totalCitas-$tiempoGastado))." -";
											 echo "Asignadas: ".$citasAsig." -";
											 echo "Disponibles: ".floor($citasDisp)."</td>";
											 echo "<td><font size='2'>".$sede." </td>";
											 echo "</tr>"; //aca salia con tamaño 10pt
										}
								}
							
							}
							
							
						}
				        
					} //while excepciones  
				} //excepciones
				
				if($num1 == 0) //no tiene excepciones, se consulta si tiene registros en la tabla citascs14
				{  
					$totalCitas = 0;
						
					$query = "select Codigo,Uni_hora, Hi, Hf, Consultorio  from ".$wbasedato."_000014 ";
					$query .= "  where Codigo = '".substr($wequ,0,strpos($wequ,"-"))."'";
					$query .= "  and Fecha = '".$wfec."'";
					$query .= " and Uni_hora != 0";
					// $query .= "   ";
					$err6 = mysql_query($query,$conex)or die( mysql_errno()." - Error en el query $query - ".mysql_error() );	
					$num6 = mysql_num_rows($err6);
					 // $rows6 = mysql_fetch_array($err6);

						if ($num6>0)  //se encontraron registros en la 14
						{
								for( $l = 0; $rows6 = mysql_fetch_array($err6); $l++ )
								{ 
									$codigo=$rows6['Codigo'];
									$uniHora=$rows6['Uni_hora'];
									$hi=$rows6['Hi'];
									$hf=$rows6['Hf'];
									$consul=$rows6['Consultorio'];
									
									
									//calculo total citas 
									$hi1=strtotime($hi);
									$hf1=strtotime($hf);
									$horasAtencion=$hf1-$hi1;
									$totalCitas+=$horasAtencion/($uniHora*60);
								
								}
					 	
						}
				
						if($num6 == 0) //no tiene registros en la 14 busca en la 10
						{	
							//se consulta el horario del medico en la tabla 10 
							$query = "select Codigo, Uni_hora, TIME_FORMAT( CONCAT(Hi,'00'), '%H:%i:00') as Hi, 
									  TIME_FORMAT( CONCAT(Hf,'00'), '%H:%i:00') as Hf, Consultorio  
									  from ".$wbasedato."_000010 
									  where Codigo = '".substr($wequ,0,strpos($wequ,"-"))."'
									  and Dia = ".$nomdia."
									  and Activo = 'A'
									  and Hi != 'NO APLICA'
									  and Hf != 'NO APLICA'
									  and Uni_hora != 0";
	
							$err3 = mysql_query($query,$conex) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );	
							$num3 = mysql_num_rows($err3);
								
								if ($num3>0)
								{
									
									for( $m = 0; $rows3 = mysql_fetch_array($err3); $m++ )
									{
										$codigo=$rows3['Codigo'];
										$uniHora=$rows3['Uni_hora'];
										$hi=$rows3['Hi'];
										$hf=$rows3['Hf'];
										$consul=$rows3['Consultorio'];
										
										
										//calculo total citas 
										$hi1=strtotime($hi);
										$hf1=strtotime($hf);
										$horasAtencion=$hf1-$hi1;
										$totalCitas+=$horasAtencion/($uniHora*60);
									}	
									
								}
								else
								{
									$rojo++;
									echo "<tr $class>";
									echo "<td><font size='2'><a href='agendaMedicos.php?empresa=$wbasedato&wfec=$wfec&wequ=$wequ&nomdia=$nomdia&colorDiaAnt=$colorDiaAnt&caso=$caso&wemp_pmla=$wemp_pmla' > ".$wequ."</a></font></td> ";
									echo "<td><font size='2'> No tiene citas disponibles</font></td>";
									echo "<td><font size='2'>".$sede." </td>";
									echo "</tr>";
									$j++;
								}
							// }
							
							
						}
						
						if  (@$num3 >0 or $num6 >0)
						{
					
							//consulta las citas de esa fecha
							$query = "select Cod_equ, Cod_exa, Fecha, Hi, Hf, Cedula, Nom_pac, Nit_res, Asistida, Activo, Atendido
									  from ".$wbasedato."_000009
									  where Cod_equ= '".$codigo."'
									    and Fecha = '".$wfec."'
									    and Activo = 'A' ";
									  
									  									  
									  $err4       = mysql_query($query,$conex)or die( mysql_errno()." - Error en el query $query - ".mysql_error() );	
									  $citasAsig  = mysql_num_rows($err4);
									  
									  $tiempoGastado = 0;
									  
									  if ($citasAsig >0)  //tiene citas para asignar
										{
											for( $i = 0; $rows4 = mysql_fetch_array($err4); $i++ )
											{
												$fechaCita=$rows4['Fecha'];
												$HiCita=$rows4['Hi'];
												$HfCita=$rows4['Hf'];
												$cedula=$rows4['Cedula'];
												
												//se hace el calculo del total de tiempo gastado en una cita
												$hiCitaUnix=strtotime($HiCita);
												$hfCitaUnix=strtotime($HfCita);
												$tiempoGastado+=($hfCitaUnix-$hiCitaUnix)/($uniHora*60)-1;
												
											}
																					
											$citasDisp=$totalCitas-$citasAsig-$tiempoGastado;
											
											if ($citasDisp==0)
											{
												
												$rojo++;
												echo "<tr $class>";
												echo "<td><font size='2'><a href='agendaMedicos.php?empresa=$wbasedato&wfec=$wfec&wequ=$wequ&nomdia=$nomdia&colorDiaAnt=$colorDiaAnt&caso=$caso&wemp_pmla=$wemp_pmla' > ".$wequ."</a></font></td> ";
												echo "<td><font size='2'> No tiene citas disponibles</font></td>";
												echo "<td><font size='2'>".$sede." </td>";
												echo "</tr>";
												$j++;
											
											}
											else
											{
												
												$amarillo++;
												echo "<tr $class>";
												echo "<td><font size='2'><a href='agendaMedicos.php?empresa=$wbasedato&wfec=$wfec&wequ=$wequ&nomdia=$nomdia&colorDiaAnt=$colorDiaAnt&caso=".@$caso."&wemp_pmla=".@$wemp_pmla."' >".$wequ."</a></font></td> ";
												echo "<td><font size='2'> Total Citas: ".(floor($totalCitas-$tiempoGastado) )." -";
												echo "Asignadas: ".$citasAsig." -";
												echo "Disponibles: ".floor($citasDisp)."</font></td>";
												echo "<td><font size='2'>".$sede." </td>";
												echo "</tr>";
												$j++;
											 }
											
											
											
										}	
									  if ($citasAsig==0)  //todas las citas disponibles
										{
											
											$verde++;
											echo "<tr $class>";
											echo "<td><font size='2'><a href='agendaMedicos.php?empresa=$wbasedato&wfec=$wfec&wequ=$wequ&nomdia=$nomdia&colorDiaAnt=$colorDiaAnt&caso=".@$caso."&wemp_pmla=".@$wemp_pmla."' > ".$wequ."</a></font></td> ";
										    echo "<td><font size='2'> Todas las citas disponibles </font></td>";
										    echo "<td><font size='2'>".$sede." </td>";
										    echo "</tr>";
											$j++;
										}		
						} //if num3 o num 6	
					  
				} //no excepciones	
				 
			} //for
			echo "</tbody>";
			echo "</table>";
				 
				
		}//medicos
		else
		{
			echo "<br>";
			echo "<br>";
			echo "<table align='left' border='0'>";
			echo "<th colspan='2' class='encabezadotabla'><font size='4'>No se asignan citas este dia</font></th>";
			echo "</table>";
			
		}
	}
}//funcion

//Devuelve la fecha en formato descriptivo dia mes año
function devolverFecha($fecha)
{
	$dia=date("l", strtotime($fecha)); //date("l");
	$diaNum=date("d", strtotime($fecha));
	$mes=date("F", strtotime($fecha));
	$anio=date("Y", strtotime($fecha));

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
	  
	return "<label style='background-color:#A9F5A9;font-weight: bold;font-size: 15px;color: blue'>Fecha seleccionada: ".$dia.' '.$diaNum.' de '.$mes .' de '.$anio."</label>";
}

//***********************fin funcion*****************
	echo "<form name='disMedicos' accept-charset='UTF-8'>";
	echo "<input type='HIDDEN' name= 'wbasedato' id= 'wbasedato' value='".$wbasedato."'>";
    echo "<input type='HIDDEN' name= 'wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='HIDDEN' name= 'wfec' id= 'wfec' value='".$wfec."'>";
	echo "<input type='HIDDEN' name= 'wsw' value='".@$wsw."'>";
	echo "<input type='HIDDEN' name= 'colorDiaAnt' value='".$colorDiaAnt."'>";
	echo "<input type='HIDDEN' name= 'caso' value='".@$caso."'>";
	echo "<input type='HIDDEN' name= 'nomdia' value='".$nomdia."'>";
	echo "<input type='HIDDEN' name= 'fest' value='".$fest."'>";
	
	disponibilidadMedicos($wfec, $nomdia,$colorDiaAnt,$wemp_pmla);
	
	$fecha=explode("-",$wfec);
	$year=$fecha[0];
	$month=$fecha[1];
	
	echo "<meta content='30;URL=dispMedicos.php?wemp_pmla=$wemp_pmla&wbasedato=$wbasedato&consultaAjax=10&wfec=$wfec&wsw=".@$wsw."&colorDiaAnt=$colorDiaAnt&caso=$caso&nomdia=$nomdia&fest=$fest' http-equiv='REFRESH'> </meta>";
	
	echo "<br>";
	//echo "<center><b><A HREF='calendar.php?empresa=".$wbasedato."&wemp_pmla=$wemp_pmla&caso=".$caso."&wsw=".@$wsw."&month=".$month."&year=".$year."&fest=".$fest."&consultaAjax='>Retornar</A></b><br></center>";
	//echo "<br>";
	
	//echo "<br><br><center><input name='button' type='button' style='width:100' onclick='window.close();' value='Cerrar' />";
	echo "</form>";
	
	echo "</body>";
	echo "</html>";
	
	
} 	
?>

			