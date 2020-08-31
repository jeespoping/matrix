<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");

if(isset($accion) and $accion == 'consultaObsercaciones')
{
			$data= array( 'error'=>0, 'mensaje'=>'');				
			
			$query = "select Fecha_I, Fecha_F, Codigo, Control  
						  from ".$wbasedato."_000021 
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
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
<script type="text/javascript">

<!--
$(document).ready(function() {
	 
	
	buscarObservaciones()
	$('input#buscarCitas').focus().quicksearch('table#tbCitas tbody tr');
});

function buscarObservaciones()
{ 
	var wbasedato=$("#wbasedato").val();
	var wfec=$("#wfec").val();
	
	
	$.post("dispEquipos.php",
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
</head>
<BODY>
<?php
/*
Creacion: 2012-09-19  Este script se crea para mostrar la lista de equipos disponibles para las citas de caso 1 y caso 3(1 modificado) que son las de equipos, se muestra la lista de equipos de una fecha seleccionada en el calendario, al nombre del equipo se le puede dar clic y lleva a la agenda para la asignacion de citas correspondientes a dicho equipo, esta pagina se recarga cada 30 segundos para que se puedan visualizar los cambios en las citas asignadas. Viviana Rodas
Modificacion:
            2020-01-30 Arleyda Insignares C. Se adiciona campo sedcod para visualizar la sede
            2020-01-20 Arleyda Insignares C. Se adiciona input para búsqueda por texto en la tabla que contiene el listado de médicos.
					   Se ubica 'retornar' en la parte superior
			2013-11-26 Se modifica el script para que muestre las observaciones creadas para todos los equipos. Viviana Rodas
			2013-07-03 Se modifica el script para que calcule los horarios cuando se encuentren varias excepciones Viviana Rodas
			2013-05-16 Se modifica el script con la variable fest que es la que valida si se asignan citas los dias festivos, si esta en on, 
					   es porque si se asignan citas los festivos en esa unidad.
			
			2012-11-27 Se agrega la verificacion de los dias festivos para dependiendo de ese mostrar la lista de equipo o no, tambien se agregan las 
					   variables month y year al link de retornar para que el calendario vuelva a mes del cual se llamo anteriormente. Viviana Rodas
			2012-10-31 Se cambia la funcion disponibilidadEquipos para evaluar cuando en un dia del calendario no hay atencion, para que muestre
					   ese dia en color rojo y muestre un mensaje diciendo no hay atencion, Se agrega boton de cerrar y link de retornar. Viviana Rodas
			2012-10-30 Se inicializo total citas en 0 dentro del for grande y tiempo gastado en 0  despues de la consulta de hay citas. Viviana Rodas			 

*/

// session_start();

if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{
	include_once("root/comun.php");
	

	$conex = obtenerConexionBD("matrix");
    $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
	
	echo"<br>";
	echo "<center><div class='div_error' style='display:none;'><div id='observacion' style='margin:15px;' align='center'></div></div></center><br>";
	

/*fin funcion para consultar las obsercaciones de los equipos*/
	
//****funcion para consultar los medicos de los equipos
function consultaMedicosEquipos()
{
	
	global $wbasedato;
	global $caso;
	global $conex;
	
	global $horarioMedicosEqu;
	

	if ($caso!=3)
	{
	 $query2 = "select '000' as equipo, hi, hf, ndia, codigo 
			   from ".$wbasedato."_000007
			   where
	           activo = 'A' 
			   order by 1,3,5"; 
	}
	else
	{
	 $query2 = "select equipo, a.hi, a.hf, ndia, a.codigo
			   from ".$wbasedato."_000007 a, ".$wbasedato."_000003 b
			   where
	           a.activo = 'A'
			   and equipo = b.codigo
			   order by 1,3,5";
	}
	 
	$err2 = mysql_query($query2,$conex)or die( mysql_errno()." - Error en el query $query2 - ".mysql_error() );
	$num2 = mysql_num_rows($err2);
	
	for($i=0; $rows2 = mysql_fetch_array($err2); $i++)
	{
		/*se crea un array por equipo, dia y medico la hora inicial (hi) y hora final (hf) de atencion de cada medico 
		para consulta posterior en la funcion que pinta los colores de cada dia
		*/
		$horarioMedicosEqu[ $rows2['equipo'] ][ $rows2['ndia'] ][ $rows2['codigo'] ][ 'hi' ] = $rows2['hi'];
		$horarioMedicosEqu[ $rows2['equipo'] ][ $rows2['ndia'] ][ $rows2['codigo'] ][ 'hf' ] = $rows2['hf'];
	}
	
}
	
	function disponibilidadEquipos($wfec,$wbasedato,$colorDiaAnt, $wemp_pmla)
	{
	
		global $conex;
		global $wsw;
		global $caso;
		global $fest;
		global $horarioMedicosEqu;
		$rojo= 0;
		$verde=0;
		$amarillo=0;
		$j=0;
		$totalCitas=0;
		$num2=0;
		$numdia=date("N",strtotime($wfec));
		
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
		
		echo "<div align=left>".devolverFecha($wfec)."<br><br><input type='text' style='background-color:#e8eef7;font-family: Arial;width:470px;hight:200px;font-size:15px;border: 4px solid #c3d9ff;
        border-radius: 5px;font-weight:bold;' id='buscarCitas' placeholder='Digite Especialidad o Nombre a buscar...'></div>";

		echo "<table id ='tbCitas' align='left' border=0>";
		if ($nums>0)
		{
			echo "<tr><td colspan='2' class='festivo' align='center'>ESTE DIA ES FESTIVO</td></tr>";
		}
		echo "<tr><td colspan='2' class='encabezadotabla' align='center'>Equipos del Dia</font></td><td class='encabezadotabla' align='center'> Sede </td></tr>";
		 
		$query = "select Codigo, Descripcion, Sednom 
		          from ".$wbasedato."_000003 left join root_000128 
		                 on root_000128.Sedcod = ".$wbasedato."_000003.Sedcod
		          where  Activo = 'A' Group by Codigo, Descripcion Order by Codigo";

		$err = mysql_query($query,$conex)or die( mysql_errno()." - Error en el query $query - ".mysql_error() );		
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
		
			for( $i = 0; $rows = mysql_fetch_array( $err ); $i++ )
				{
					$totalCitas = 0;
					
					//Definiendo la clase por cada fila
					if( $j%2 == 0 )

						$class = "class='fila1'";
					else

						$class = "class='fila2'";

					
					$cod =$rows['Codigo'];
					$des =$rows['Descripcion'];
					$sede=$rows['Sednom']; 
					$wequ=$cod."-".$des;					
					
					//consulta si tiene excepciones
					$query = "select Uni_hora, TIME_FORMAT( CONCAT(Hi,'00'), '%H:%i:00') as Hi, TIME_FORMAT( CONCAT(Hf,'00'), '%H:%i:00') as Hf  
						  from ".$wbasedato."_000021 
						  where Fecha_I <= '".$wfec."'
						  and Fecha_F >= '".$wfec."'
						  and Codigo = '".substr($wequ,0,strpos($wequ,"-"))."'
						  and Activo = 'A'
						  and Uni_hora != 1
						  and Codigo != 'Todos'
						  order by Uni_hora asc
						  
						  ";
					$err1 = mysql_query($query,$conex) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );	
					$num1 = mysql_num_rows($err1);
					
					
					if ($num1>0)  //tiene excepciones
					{	
						
					   $rows1 = mysql_fetch_array($err1);
					   
					   $tiempoGastado = 0;
					   
					   while( $rows1 ){  //mientras sea true
						
						   $exUniHora=$rows1['Uni_hora'];
						   $exHi=$rows1['Hi'];
						   $exHf=$rows1['Hf'];
						   
						   if ($exUniHora == 0)
							{
								$rows1 = mysql_fetch_array($err1);
								
								if( !$rows1 ){
									$j++;
									$rojo++;
									echo "<tr $class>";
									echo "<td><font size='2'>".$wequ."</font></td> ";
									echo "<td><font size='2'> No hay atencion este dia</font></td>";
									echo "</tr>";
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
							
							/*se hace un for para evaluar cita a cita con el horario del equipo y el horario del medico 
							si trae registros se le suma 1 al total de citas
							*/
						
							for ($i=$hi1; $i< $hf1; $i += $exUniHora*60 )
							{
								if( $caso == 3 ){
										$codEquhm = substr($wequ,0,strpos($wequ,"-"));
									}
									else{
										$codEquhm = '000';
									}
									
									//Verifico que halla horario para el medico en un día
									if( !empty($horarioMedicosEqu[ $codEquhm ][$numdia] ) ){
									
										foreach( $horarioMedicosEqu[ $codEquhm ][$numdia] as $keyHM => $valueHM ){
											if( $valueHM['hi'] <= date( "Hi", $i ) && $valueHM['hf'] >= date( "Hi", $i + $uni_hora*60 ) ){
												$totalCitas++;
												break;
											}
										}
									}
								
								
							} //for
								
							$numdia=date("N",strtotime($wfec));
							//se agrega consulta para validar que el medico y el equipo tengan el mismo horario
							$query2 = "select hi, hf from ".$wbasedato."_000007 ";
							$query2 .= " where ".$wbasedato."_000007.Ndia = '".$numdia."' ";  //numero del dia
							if ($caso == 3)
							{
								$query2 .= " and ".$wbasedato."_000007.equipo = '".substr($wequ,0,strpos($wequ,"-"))."' ";  //codigo equipo
							}
							
							$query2 .= "      and ".$wbasedato."_000007.activo = 'A' ";
							$query2 .= "  order by ".$wbasedato."_000007.codigo";
						
							$err2 = mysql_query($query2,$conex)or die( mysql_errno()." - Error en el query $query2 - ".mysql_error() );
							$num2 = mysql_num_rows($err2);
						
						
						if ($num2 > 0)
						{
						
								//consulta las citas de esa fecha
								$query = "select cod_med,cod_equ,cod_exa,fecha,hi,hf,nom_pac,nit_resp,telefono,edad,comentarios,usuario,activo,cedula,Asistida from ".$wbasedato."_000001 where fecha='".$wfec."' and cod_equ='".substr($wequ,0,strpos($wequ,"-"))."' and Activo='A' order by hi";
								$err4 = mysql_query($query,$conex)or die( mysql_errno()." - Error en el query $query - ".mysql_error() );	
								$num4 = mysql_num_rows($err4);
								
								// $tiempoGastado = 0;
								
								if ($num4>0)  //tiene citas para asignar
								{
									
									for( $i = 0; $rows4 = mysql_fetch_array($err4); $i++ )
									{
										$fechaCita=$rows4['fecha'];
										$HiCita=$rows4['hi'];
										$HfCita=$rows4['hf'];
										$cedula=$rows4['cedula'];
										$cod_equ=$rows4['cod_equ'];
										
										//se hace el calculo del total de tiempo gastado en una cita
										$hiCitaUnix=strtotime($HiCita);
										$hfCitaUnix=strtotime($HfCita);
										$tiempoGastado+=($hfCitaUnix-$hiCitaUnix)/($exUniHora*60)-1;

									}
									
								}
																
						}						
						
						$rows1 = mysql_fetch_array($err1);
						
						if( !$rows1 ){
						
							if ($num4==0)  //todas las citas disponibles
							{
								
								$verde++;
								echo "<tr $class>";
								echo "<td><font size='2'><a href='agendaEquipos.php?empresa=$wbasedato&wequ=$wequ&wsw=$wsw&colorDiaAnt=$colorDiaAnt&wfec=$wfec&caso=$caso&wemp_pmla=$wemp_pmla' > ".$wequ."</a></font></td> ";
								echo "<td><font size='2'> Todas las citas disponibles </font></td>";
								echo "<td><font size='2'>".$sede."</td><tr>";
								$j++;
							}
							elseif( $num2 == 0 ){
								$rojo++;
								echo "<tr $class align=left>";
								echo "<td><font size='2'> ".$wequ."</font></td> ";
								echo "<td><font size='2'> No se asignan citas este dia</font></td>";
								echo "<td><font size='2'>".$sede." </td><tr>";
								echo "</tr>";
								$j++;
							}
							else{
							
								//calculo de cuantas citas disponibles
								$query = "select count(*) 
										  from ".$wbasedato."_000001 
										  where Cod_equ= '".$cod_equ."'
										  and Fecha = '".$wfec."' 
										  and Activo = 'A'";
								
								$err5 = mysql_query($query,$conex)or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
								
								$row5=mysql_fetch_array($err5);
								$citasAsig=$row5[0];
								$citasDisp=$totalCitas-$citasAsig-$tiempoGastado;
							
								if ($citasDisp==0)
								{	
									$rojo++;
									echo "<tr $class>";
									echo "<td><font size='2'><a href='agendaEquipos.php?empresa=$wbasedato&wequ=$wequ&wsw=$wsw&colorDiaAnt=$colorDiaAnt&wfec=$wfec&caso=$caso&wemp_pmla=$wemp_pmla' > ".$wequ."</a></font></td> ";
									echo "<td><font size='2'> No tiene citas disponibles</font></td>";
									echo "<td><font size='2'>".$sede." </td><tr>";
									echo "</tr>";
									$j++;
								
								}
								else
								{	
									$amarillo++;
									echo "<tr $class>";
									echo "<td><font size='2'><a href='agendaEquipos.php?empresa=$wbasedato&wequ=$wequ&wsw=$wsw&colorDiaAnt=$colorDiaAnt&wfec=$wfec&caso=$caso&wemp_pmla=$wemp_pmla' >".$wequ."</a></font></td> ";
									echo "<td><font size='2'> Total Citas: ".($totalCitas-$tiempoGastado)." -";
									echo "Asignadas: ".$citasAsig." -";
									echo "Disponibles: ".$citasDisp."</font></td>";
									echo "<td><font size='2'>".$sede." </td><tr>";
									echo "</tr>";
									$j++;
								 }
							}
						}
					}
						
				} //fin de while	
							
							
					
			} //fin tiene excepciones
					
				if ($num1==0) /******no tiene excepciones******/
				{
						
					//se consulta la tabla 4
					$query = "select fecha,equipo,uni_hora,hi,hf from ".$wbasedato."_000004 where fecha = '".$wfec."' and equipo = '".substr($wequ,0,strpos($wequ,"-"))."'";
					$err6 = mysql_query($query,$conex)or die( mysql_errno()." - Error en el query $query - ".mysql_error() );		
					$num6 = mysql_num_rows($err6);
					$row6 = mysql_fetch_array($err6);
					
					if ($num6>0)
					{
						$fecha=$row6['fecha'];
						$codigo=$row6['equipo']; //equipo
						$uni_hora=$row6['uni_hora'];
						$hi=$row6['hi'];  //hora inicial equipo
						$hf=$row6['hf'];  //hora final equipo
						
						//calculo total citas 
						$hi1=strtotime($hi);
						$hf1=strtotime($hf);
						$horasAtencion=$hf1-$hi1;
						
						/*se hace un for para evaluar cita a cita con el horario del equipo y el horario del medico 
						  si trae registros se le suma 1 al total de citas
						*/
						
							for ($i=$hi1; $i< $hf1; $i += $uni_hora*60 )
							{
								if( $caso == 3 ){
											$codEquhm = substr($wequ,0,strpos($wequ,"-"));
								}
								else{
									$codEquhm = '000';
								}
								
								//Verifico que halla horario para el medico en un día
								if( !empty($horarioMedicosEqu[ $codEquhm ][$numdia] ) ){
								
									foreach( $horarioMedicosEqu[ $codEquhm ][$numdia] as $keyHM => $valueHM ){
										if( $valueHM['hi'] <= date( "Hi", $i ) && $valueHM['hf'] >= date( "Hi", $i + $uni_hora*60 ) ){
											$totalCitas++;
											break;
										}
									}
								}
								
								
							} //for
					}//$num6>0	
						// else
						// {
							// $totalCitas=$horasAtencion/($uni_hora*60);
						// }
					
					
					if ($num6 == 0) //no tiene registros en la tabla 4 se busca en la tabla 3
					{
						
						$query7 = "select codigo,descripcion,uni_hora,hi,hf,activo from ".$wbasedato."_000003 where codigo='".substr($wequ,0,strpos($wequ,"-"))."'";
						$err7 = mysql_query($query7,$conex)or die( mysql_errno()." - Error en el query $query7 - ".mysql_error() );	
						$num7 = mysql_num_rows($err7);
						$row7 = mysql_fetch_array($err7);
						
						if ($num7>0) //si tiene registros en la tabla 3
						{
							$codigo=$row7['codigo'];
							$descripcion=$row7['descripcion'];
							$uni_hora=$row7['uni_hora'];
							$hi=$row7['hi'];
							$hf=$row7['hf'];
							
							//calculo total citas 
							$hi1=strtotime($hi);
							$hf1=strtotime($hf);
							$horasAtencion=$hf1-$hi1;
							
							/*se hace un for para evaluar cita a cita con el horario del equipo y el horario del medico 
						  si trae registros se le suma 1 al total de citas
						*/
							
								for ($i=$hi1; $i<$hf1; $i += $uni_hora*60 )
								{
									if( $caso == 3 ){
											$codEquhm = substr($wequ,0,strpos($wequ,"-"));
										}
										else{
											$codEquhm = '000';
										}
										
										//Verifico que halla horario para el medico en un día
										if( !empty($horarioMedicosEqu[ $codEquhm ][$numdia] ) ){
										
											foreach( $horarioMedicosEqu[ $codEquhm ][$numdia] as $keyHM => $valueHM ){
												if( $valueHM['hi'] <= date( "Hi", $i ) && $valueHM['hf'] >= date( "Hi", $i + $uni_hora*60 ) ){
													$totalCitas++;
													break;
												}
											}
										}
									
									
								} //for
							
							// else
							// {
								// $totalCitas=$horasAtencion/($uni_hora*60);
							// }
						} //num7>0
						
				
					} //$num6=0
					
					//validacion que estaba afuera
					$numdia=date("N",strtotime($wfec));
					//se agrega consulta para validar que el medico y el equipo tengan el mismo horario
					
					
						$query2 = "select hi, hf from ".$wbasedato."_000007 ";
						$query2 .= "      where ".$wbasedato."_000007.Ndia = '".$numdia."' ";  //numero del dia
						if ($caso == 3)
						{
							$query2 .= " and ".$wbasedato."_000007.equipo = '".substr($wequ,0,strpos($wequ,"-"))."' ";  //codigo equipo
						}
						
						$query2 .= "   and ".$wbasedato."_000007.activo = 'A' ";
						$query2 .= "  order by ".$wbasedato."_000007.codigo";
					
						$err2 = mysql_query($query2,$conex)or die( mysql_errno()." - Error en el query $query2 - ".mysql_error() );
						$num2 = mysql_num_rows($err2);
					
					
					if ($num2 > 0 )
					{
						
								//consulta las citas de esa fecha
								$query = "select cod_med,cod_equ,cod_exa,fecha,hi,hf,nom_pac,nit_resp,telefono,edad,comentarios,usuario,activo,cedula,Asistida from ".$wbasedato."_000001 where fecha='".$wfec."' and cod_equ='".substr($wequ,0,strpos($wequ,"-"))."' and Activo='A' order by hi";
								$err4 = mysql_query($query,$conex)or die( mysql_errno()." - Error en el query $query - ".mysql_error() );	
								$num4 = mysql_num_rows($err4);
								
								$tiempoGastado = 0;
								
								if ($num4>0)  //tiene citas para asignar
								{
									
									for( $i = 0; $rows4 = mysql_fetch_array($err4); $i++ )
									{
										$fechaCita=$rows4['fecha'];
										$HiCita=$rows4['hi'];
										$HfCita=$rows4['hf'];
										$cedula=$rows4['cedula'];
										$cod_equ=$rows4['cod_equ'];
										
										//se hace el calculo del total de tiempo gastado en una cita
										$hiCitaUnix=strtotime($HiCita);
										$hfCitaUnix=strtotime($HfCita);
										$tiempoGastado+=($hfCitaUnix-$hiCitaUnix)/($uni_hora*60)-1;
										
									}
									
									//calculo de cuantas citas disponibles
									$query = "select count(*) 
											  from ".$wbasedato."_000001 
											  where Cod_equ= '".$cod_equ."'
											  and Fecha = '".$wfec."' 
											  and Activo = 'A'";
									
									$err5 = mysql_query($query,$conex)or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
									
									$row5=mysql_fetch_array($err5);
									
									$citasAsig=$row5[0];
									$citasDisp=$totalCitas-$citasAsig-$tiempoGastado;
									
									if ($citasDisp==0)
									{
										
										$rojo++;
										echo "<tr $class>";
										echo "<td><font size='2'><a href='agendaEquipos.php?empresa=$wbasedato&wequ=$wequ&wsw=$wsw&colorDiaAnt=$colorDiaAnt&wfec=$wfec&caso=$caso&wemp_pmla=$wemp_pmla' > ".$wequ."</a></font></td> ";
										echo "<td><font size='2'> No tiene citas disponibles</font></td>";
										echo "<td><font size='2'>".$sede." </td>";
										echo "</tr>";
										$j++;
									
									}
									else
									{
										
										$amarillo++;
										echo "<tr $class>";
										echo "<td><font size='2'><a href='agendaEquipos.php?empresa=$wbasedato&wequ=$wequ&wsw=$wsw&colorDiaAnt=$colorDiaAnt&wfec=$wfec&caso=$caso&wemp_pmla=$wemp_pmla' >".$wequ."</a></font></td> ";
										echo "<td><font size='2'> Total Citas: ".($totalCitas-$tiempoGastado)." -";
										echo "Asignadas: ".$citasAsig." -";
										echo "Disponibles: ".$citasDisp."</font></td>";
										echo "<td><font size='2'>".$sede." </td>";
										echo "</tr>";
										$j++;
									 }	
								}
								
									if ($num4==0)  //todas las citas disponibles
										{
											
											$verde++;
											echo "<tr $class>";
											echo "<td><font size='2'><a href='agendaEquipos.php?empresa=$wbasedato&wequ=$wequ&wsw=$wsw&colorDiaAnt=$colorDiaAnt&wfec=$wfec&caso=$caso&wemp_pmla=$wemp_pmla' > ".$wequ."</a></font></td> ";
											echo "<td><font size='2'> Todas las citas disponibles </font></td>";
											echo "<td><font size='2'>".$sede." </td></tr>";
											$j++;
										}
					}
					else
					{
						$rojo++;
						echo "<tr $class align left>";
						echo "<td><font size='2'> ".$wequ."</font></td> ";
						echo "<td><font size='2'> No se asignan citas este dia</font></td>";
						echo "<td><font size='2'>".$sede." </td>";
						echo "</tr>";
						$j++;
					}
				} //iria aca la llave de no excepciones
		} //for
	}//num>0
		echo "</table>";
  }
	
} //funcion

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
	

	echo "<form name='disMedicos' accept-charset='UTF-8'>";
	echo "<input type='HIDDEN' name= 'wbasedato' id='wbasedato' value='".$wbasedato."'>";
    echo "<input type='HIDDEN' name= 'wemp_pmla' id= 'wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='HIDDEN' name= 'wsw'  id= 'wsw' value='".$wsw."'>";
	// echo "<input type='HIDDEN' name= 'wfec' value='".@$wfec1."'>";
	// echo "<input type='HIDDEN' name= 'nomdia' value='".@$nomdia."'>";
	// echo "<input type='HIDDEN' name= 'colorDiaAnt' value='".$colorDiaAnt."'>";
	echo "<input type='HIDDEN' name= 'caso' id= 'caso' value='".$caso."'>";
	echo "<input type='HIDDEN' name= 'fest' id= 'fest' value='".$fest."'>";
	echo "<input type='HIDDEN' name= 'wfec' id= 'wfec' value='".$wfec."'>";
	
	
	consultaMedicosEquipos();
	disponibilidadEquipos(@$wfec,$wbasedato, $colorDiaAnt,$wemp_pmla);
	
	
	$fecha=explode("-",$wfec);
	$year=$fecha[0];
	$month=$fecha[1];
	echo "<meta content='30;URL=dispEquipos.php?wemp_pmla=$wemp_pmla&wbasedato=$wbasedato&consultaAjax=10&wfec=$wfec&wsw=$wsw&colorDiaAnt=$colorDiaAnt&caso=$caso&fest=$fest' http-equiv='REFRESH'> </meta>";
	
	echo "<br>";
	echo "<center><b><A HREF='calendar.php?empresa=".$wbasedato."&wemp_pmla=$wemp_pmla&caso=".$caso."&wsw=".@$wsw."&month=".$month."&year=".$year."&fest=".$fest."&consultaAjax='>Retornar</A></b><br></center>";
	echo"<br>";
	
	echo "<br><br><center><input name='button' type='button' style='width:100' onclick='window.close();' value='Cerrar' />";
	echo "</form>";
	
	echo "</body>";
	echo "</html>";
}
?>