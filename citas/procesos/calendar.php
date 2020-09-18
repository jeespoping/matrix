
<!--<link rel='stylesheet' href='../../../include/root/matrix.css'/>-->
<script type="text/javascript" src="../../../include/root/jquery-1.3.2.js"></script>    
<link type="text/css" href="../../../include/root/jatt.css" rel="stylesheet" />
<script type="text/javascript" src="../../../include/root/jquery.jatt.js"></script>  

<script>
$(function(){
	$.jatt();
});

$(function() {

	$('#info').tooltip({
		delay: 0,
		showURL: false,
		bodyHandler: function() {
		  return $("<img/>").attr("src", this.src);
		}
	});

});

function cargarIframe(url,obj)
{
	
	$('#ifcitas').attr('src', url);
	var idAnterior = $('#dayActual').val();
	var id  = $(obj).attr('id');	

    //Asigno el color en el td seleccionado
	$('#'+id).css("background-color", "lightgreen");
	
	//Retirar el color en el td anterior
	if (idAnterior != '')
	    $('#'+idAnterior).css("background-color", "");
	
	$('#dayActual').val($(obj).attr('id'));

}

</script>

<?php
include_once("conex.php");

/****************************************************************************************************************************************
*Fecha de creacion: 2012-09-08 Este calendario permite agilizar la asignacion de citas en las unidades de la clinica como en 
* la clinica del sur, este muestra el calendario del mes actual, permitiendo navegar por los distintos meses del año o de cualquier año,
* dependiendo del color del dia muestra si tiene citas disponibles, si tiene todas las citas disponibles o no tiene citas disponibles, 
* al pasar el mouse sobre el dia se muestra un tooltip donde se muestran los medicos o equipos que atienden ese dia y cuantas citas tienen
* disponibles, al darle clic sobre el dia lleva a una pagina donde muestra tambien la lista de medicos o equipos ahi se selecciona uno e 
* inmediatamente lleva a la agenda para la asignacion de la cita. Viviana Rodas
*****************************************************************************************************************************************
* Modificiaciones:
*				   2020-09-09:	Edwin Molina. Se hacen cambios varios para recibir los datos por defecto que quedaran en la cita y vienen de la lista de espera para Drive Thru
*                  2020-05-07 Arleyda Insignares. Se amplia el tamaño del iframe principal (width)
*                  2020-01-20 Arleyda Insignares. Se unifica el script calendar.php con el script 
*                  asignacionCitaMed.php y asignacionCitaEqu.php mediante un iframe.
                   2018-09-11 Arleyda Insignares. Se unifican dos consultas a la tabla 'prefijo_000009', con el objetivo de optimizar la consulta de disponibilidad de las citas.
				   2014-02-04 Se modifica la consulta a la tabla de horarios de los medicos: Se agrega union a la tabla de excepciones para que liste los medicos tiene excepciones ese dia. Viviana Rodas
				   2013-11-26 Se modifica el script en la consulta a las excepciones para que no filtre las observaciones creadas desde el programa de excepciones. Viviana Rodas
				   2013-09-09 Se modifica el script para corregir un error: en la funcion dispColores faltaba una validacion para cuando la unihora en excepciones es 0 y no se hacen calculos. Viviana Rodas
				   2013-09-03 Se modifica el script para que corregir un error en el calculo del total de citas cuando el horario esta dividido ya sea excepcion u horario normal en las citas de medicos en la funcion de medicos. Viviana Rodas
				   2013-08-29 Se modifica el script se inicializa $tiempoGastado = 0 en la funcion dispColores en el for que hace por cada medico. Viviana Rodas.
				   2013-07-03 Se modifica el script para que consulte las excepciones y haga los calculos cuando se encuentren varias por dia Viviana Rodas
				   2013-05-23 Se modifica el script para minimizar el numero de consultas cuando se compara el horario del medico con el del equipo. Viviana Rodas
				   2013-05-16 Se modifica el script con la variable fest que es la que valida si se asignan citas los dias festivos, si esta en on, 
							  es porque si se asignan citas los festivos en esa unidad.
				   2013-04-24 Se agrega al link de busqueda la variable wemp_pmla. Viviana Rodas
				   2013-03-27 Se organiza un error en el mes de abril con una variable month1 Viviana rodas
*                  2012-11-29 Se cambia la validacion donde se pregunta si el dia anterior es rojo y la fecha que se esta pintando en el calendario
							  es mayor a la actual, se le quito el >= debido a que se calculaba mal el indice de oportunidad. Viviana Rodas 
				   2012-11-27 Se hace la verificacion si un dia del mes que se esta pintando es festivo, consultando la tabla root_000063 para
							  que salga rojo. Viviana Rodas
				   2012-11-26 Se hace la validacion para que cuando se haga la recarga del calendario tome el mes en el cual esta el calendario, 
							  sin importar que no sea el mes actual.
				   2012-10-31 Se cambia la funcion dispColores para evaluar cuando en un dia del calendario no hay atencion, para que muestre ese 	dia en color rojo y muestre un mensaje diciendo no hay atencion. Viviana Rodas
				   2012-09-10 Se agrega el tooltip para mostrar la lista de los medicos o equipos
				   2012-09-11 Se agregan nuevos estilos para el calendario ya que inicalmente era todo de color gris ademas se pone el 
				   calendario en castellano porque era en ingles.
				   2012-09-18 Se agregan las funciones para calcular los coleres de los dias y la lista de los medicos o equipos
				   2012-09-26 Se agregan las validaciones para determinar si las citas a asignar son de un caso u otro. Viviana Rodas
*****************************************************************************************************************************************/

	/**
	 * Crea los parametros extras de una url para Get
	 */
	function inputExtras( $variablesGET, $post = false ){
		
		$val = [];
		
		$superglobal = $_GET;
		if( $post ){
			$superglobal = $_POST;
		}
		
		foreach( $variablesGET as $key => $value ){
			
			if( $superglobal[ $value ] ){
				
				$val[] = "<input type='hidden' name='$value' value='".$superglobal[ $value ]."'>";
			}
		}

		return $val;
	}
	
	
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
	

include_once("root/comun.php");

$parametrosExtras = '';

$parametrosExtras = parametrosExtras( [
						'defaultCedula' 	=> 'cedula',
						'defaultNombre' 	=> 'paciente',
						'defaultNit' 		=> 'aseguradora',
						'defaultCorreo' 	=> 'email',
						'defaultUrl' 		=> 'url',
						'defaultEdad' 		=> 'edad',
						'defaultTelefono' 	=> 'telefono',
						'defaultComentarios'=> 'comentarios',
						'idListaEspera'		=> 'id',
					]);

$parametrosExtrasOri = parametrosExtras( [
						'cedula' 		=> 'cedula',
						'paciente' 		=> 'paciente',
						'aseguradora' 	=> 'aseguradora',
						'email' 		=> 'email',
						'url' 			=> 'url',
						'edad' 			=> 'edad',
						'telefono' 		=> 'telefono',
						'comentarios'	=> 'comentarios',
						'id'			=> 'id',
					]);

$inputExtras = inputExtras([
						'cedula',
						'paciente',
						'aseguradora',
						'email',
						'url',
						'edad',
						'telefono',
						'comentarios',
					]);

if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{
  $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
  $wbasedato = strtolower( $institucion->baseDeDatos );  
    
  echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
  echo "<input type='HIDDEN' name= 'wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name= 'wsw' value='".@$wsw."'>";
  echo "<input type='HIDDEN' name= 'caso' value='".@$caso."'>";
  echo "<input type='HIDDEN' name= 'fest' value='".@$fest."'>";
  echo "<input type='HIDDEN' id='dayColorant' name= 'dayColorant'>";
  echo "<input type='HIDDEN' id='dayActual' name= 'dayColorant'>";
  
  $solucionCitas=$empresa;

  $esFestivo=0;
  
   require "layout.inc.php"; 
   
	
//****funcion para consultar los medicos de los equipos
function consultaMedicosEquipos()
{
	
	global $solucionCitas;
	global $caso;
	global $conex;
	
	global $horarioMedicosEqu;
	
	if ($caso!=3)
	{
	 $query2 = "select '000' as equipo, hi, hf, ndia, codigo 
			   from ".$solucionCitas."_000007
			   where
	           activo = 'A' 
			   order by 1,3,5"; 
	}
	else
	{
	 $query2 = "select equipo, a.hi, a.hf, ndia, a.codigo
			   from ".$solucionCitas."_000007 a, ".$solucionCitas."_000003 b
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

//**************funcion para los colores******************
function dispColores($wfec, $nomdia)
{ 
	
		global $conex;
		global $totalCitas;
		global $citasAsig;
		global $citasDisp;
		global $solucionCitas;
		global $amarillo;
		global $rojo;
		global $verde;
		global $fest;
		global $esFestivo;
		
		$amarillo=0;
		$rojo=0;
		$verde=0;
		$j=0;
	    $tiempoGastado = 0;
		
	//Se consulta la tabla de festivos 
	$sql="select Fecha from root_000063 where Fecha = '".$wfec."' ";
	$errs = mysql_query($sql,$conex)or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );		
	$nums = mysql_num_rows($errs);
	$esFestivo=$nums;
	
	if ($nums > 0 and $fest == 'off')
	{ 
			return "rojo";
	}
	else
	{
		
		//consulta los medicos 
		$query = "select Codigo, Descripcion 
						  from ".$solucionCitas."_000010 
						  where Activo = 'A'
						  and Dia='".$nomdia."' 
					union			  
					SELECT a.Codigo, b.Descripcion
						FROM ".$solucionCitas."_000012 a, ".$solucionCitas."_000010 b
						WHERE a.Fecha_I <= '".$wfec."'
						AND a.Fecha_F >= '".$wfec."'
						AND a.Activo = 'A'
						AND a.Uni_hora !=1
						AND a.Codigo != 'Todos'
						AND a.Codigo =b.Codigo
					Group by Codigo, Descripcion
					Order by Descripcion";
		$err = mysql_query($query,$conex) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );	
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			for( $i = 0; $rows = mysql_fetch_array( $err ); $i++ )
			{
				$totalCitas = 0;
				$tiempoGastado = 0;
				//Definiendo la clase por cada fila
					if( $j%2 == 0 ){
						$class = "class='fila1'";
					}
					else{
						$class = "class='fila2'";
					}
				
				
				$cod=$rows['Codigo'];
				$des=$rows['Descripcion'];
				$wequ=$cod."-".$des; 
				$codigo = $cod;
				
				
				 //consulta si tiene excepciones
				 $query = "select Uni_hora, TIME_FORMAT( CONCAT(Hi,'00'), '%H:%i:00') as Hi, TIME_FORMAT( CONCAT(Hf,'00'), '%H:%i:00') as Hf, Consultorio  
						  from ".$solucionCitas."_000012 
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
									  from ".$solucionCitas."_000009
									  where Cod_equ= '".$codigo."'
									  and Fecha = '".$wfec."'
									  and Activo = 'A' ";
									  
									  $err4 = mysql_query($query,$conex)or die( mysql_errno()." - Error en el query $query - ".mysql_error() );	
									  $citasAsig = mysql_num_rows($err4);
									  
									  if ($citasAsig>0)  //si tiene citas en esa fecha
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
												{
													$tiempoGastado+=($hfCitaUnix-$hiCitaUnix)/($exUniHora*60)-1;
												}

											}
																																		
										}

									
				       $rows1 = mysql_fetch_array($err1);
					   
					   if( !$rows1 ){
								if ($citasAsig==0)  //todas las citas disponibles
								{
									$j++;
									$verde++;
								}
								else
								{
										
										// Variable que contiene la disponibilidad en citas
										$citasDisp=$totalCitas-$citasAsig-$tiempoGastado;
										
										if ($citasDisp==0)
										{
											$j++;
											$rojo++;
											
										
										}
										else
										{
											$j++;
											
											$amarillo++;
											
										}
								} //else cuantas disponibles
							}
						}
				    }//while
				} //fin excepciones
				
				if($num1 == 0) //no tiene excepciones, se consulta si tiene registros en la tabla citascs14
				{  
					
					$totalCitas = 0;
					
					$query = "select Codigo,Uni_hora, Hi, Hf, Consultorio  from ".$solucionCitas."_000014 ";
					$query .= "  where Codigo = '".substr($wequ,0,strpos($wequ,"-"))."'";
					$query .= "  and Fecha = '".$wfec."'";
					$query .= " and Uni_hora != 0";
					// $query .= "  Group by Codigo ";
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
						
							$query = "select Codigo, Uni_hora, TIME_FORMAT( CONCAT(Hi,'00'), '%H:%i:00') as Hi, 
									  TIME_FORMAT( CONCAT(Hf,'00'), '%H:%i:00') as Hf, Consultorio  
									  from ".$solucionCitas."_000010 
									  where Codigo = '".substr($wequ,0,strpos($wequ,"-"))."'
									  and Dia = ".$nomdia."
									  and Activo = 'A'
									  and Hi != 'NO APLICA'
									  and Hf != 'NO APLICA'
									  and Uni_hora != 0 ";
									  // Group by Codigo 
							$err3 = mysql_query($query,$conex) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );	
							$num3 = mysql_num_rows($err3);
							$rows3 = mysql_fetch_array($err3);
							if ($num3>0)  //si tiene registros
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
							else  //no tiene registros
							{
								$rojo++;
											
								$j++;
								
							}
														
						}
						if  (@$num3 >0 or $num6 >0)
						{
						
							//consulta las citas de esa fecha
							$query = "select Cod_equ, Cod_exa, Fecha, Hi, Hf, Cedula, Nom_pac, Nit_res, Asistida, Activo, Atendido
									  from ".$solucionCitas."_000009
									  where Cod_equ= '".@$codigo."'
									  and Fecha = '".$wfec."'
									  and Activo = 'A' ";
									  
									  $err4      = mysql_query($query,$conex)or die( mysql_errno()." - Error en el query $query - ".mysql_error() );	
									  $citasAsig = mysql_num_rows($err4);
									 
									  if ($citasAsig>0)  //tiene citas para asignar
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
												@$tiempoGastado+=($hfCitaUnix-$hiCitaUnix)/($uniHora*60)-1;
											}
											
												
											// Variable que contiene la disponibilidad en citas
											$citasDisp=$totalCitas-$citasAsig-$tiempoGastado;
											
											if ($citasDisp==0)
											{
												
												$rojo++;												
												$j++;
											
											}
											else
											{
												
												$amarillo++;												
												$j++;
											}
											
										}	
									  if ($citasAsig==0)  //todas las citas disponibles
									  {
											
											$verde++;
											
											$j++;
									  }
						
						} //if num3 o num 6			  
				}//no excepciones
							
			} //for
			
			
		} //medicos
		
	}	
	
	 if ($amarillo>0) 
	 {	
		 $color="amarillo"; 
	 }
	 
	 else if ($verde>0)
	 {
		$color="calendar";
	 }
	 
	 else if ($rojo>0)
	 { 
		$color="rojo";
	 }	
		
	 return $color;		
		
} //funcion
	
    //**************fin funcion colores***********************
	
	//**********funcion encabezado***************
	function encabezado1($wtitulo, $wversion, $wlogemp){
	echo "<table border=0>";
	
	echo "<tr>";
    echo "<td width='10%' rowspan=2>&nbsp;";                        
    
    echo "<img src='../../images/medical/root/".$wlogemp.".jpg' width=120 heigth=76>";
    echo "</td>";
    echo "<td width='90%' class='fila1'>";
    echo "<div class='titulopagina' align='center'>";

    echo $wtitulo;
    
    echo "</div>";
    echo "</td>";
    echo "<td width='10%' rowspan=2>&nbsp;";
    echo "<img src='../../images/medical/root/fmatrix.jpg' width=120 heigth=76>";
    echo "</td>";
    echo "</tr>";
    
    echo "<tr>";
    echo "<td colspan='1' align='right' class='fila2'>";
    echo "<span class='version'>Versi&oacute;n: $wversion</span>";
    echo "</td>";
    echo "</tr>";
    
    echo "</table>";
    
    }
	//**********fin funcion encabezado***********
	
	//**********funcion colores caso 3 y caso 1 de citas *************
	function coloresMSN($wfec)
	{	
	
		global $conex;
		global $wsw;
		global $solucionCitas;
		global $caso;
		global $fest;
		global $esFestivo;
		
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
	$esFestivo=$nums;
	
	if ($nums > 0 and $fest == 'off')
	{ 
		$rojo++;
	}
	else
	{
			
			//turnos de cada equipo
			$query = "select Codigo, Descripcion from ".$solucionCitas."_000003 where Activo = 'A' Group by Codigo, Descripcion Order by Codigo";
			$err = mysql_query($query,$conex)or die( mysql_errno()." - Error en el query $query - ".mysql_error() );		
			$num = mysql_num_rows($err);
			if ($num>0)
			{
			
				for( $i = 0; $rows = mysql_fetch_array($err); $i++ )
					{
						$totalCitas = 0;
						
						$cod=$rows['Codigo'];
						$des=$rows['Descripcion'];
						$wequ=$cod."-".$des; 
						
						
						//consulta si tiene excepciones
						$query = "select Uni_hora, TIME_FORMAT( CONCAT(Hi,'00'), '%H:%i:00') as Hi, TIME_FORMAT( CONCAT(Hf,'00'), '%H:%i:00') as Hf  
							  from ".$solucionCitas."_000021 
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
											if( $valueHM['hi'] <= date( "Hi", $i ) && $valueHM['hf'] >= date( "Hi", $i + $exUniHora*60 ) ){
												$totalCitas++;
												break;
											}
										}
									}
									
								} //for
								
							$numdia=date("N",strtotime($wfec));
							//se agrega consulta para validar que el medico y el equipo tengan el mismo horario
							$query2 = "select hi, hf from ".$solucionCitas."_000007 ";
							$query2 .= " where ".$solucionCitas."_000007.Ndia = '".$numdia."' ";  //numero del dia
							if ($caso == 3)
							{
								$query2 .= " and ".$solucionCitas."_000007.equipo = '".substr($wequ,0,strpos($wequ,"-"))."' ";  //codigo equipo
							}
							
							$query2 .= "      and ".$solucionCitas."_000007.activo = 'A' ";
							$query2 .= "  order by ".$solucionCitas."_000007.codigo";
						
							$err2 = mysql_query($query2,$conex)or die( mysql_errno()." - Error en el query $query2 - ".mysql_error() );
							$num2 = mysql_num_rows($err2);
						
						
						if ($num2 > 0)
						{
						
								//consulta las citas de esa fecha
								$query = "select cod_med,cod_equ,cod_exa,fecha,hi,hf,nom_pac,nit_resp,telefono,edad,comentarios,usuario,activo,cedula,Asistida from ".$solucionCitas."_000001 where fecha='".$wfec."' and cod_equ='".substr($wequ,0,strpos($wequ,"-"))."' and Activo='A' order by hi";
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
								
								$j++;
							}
							elseif( $num2 == 0 ){
								$rojo++;
								
								$j++;
							}
							else{
							
								//calculo de cuantas citas disponibles
								$query = "select count(*) 
											   from ".$solucionCitas."_000001 
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
									
									$j++;
								
								}
								else
								{	
									$amarillo++;
									
									$j++;
								}
							}
						}
								
					}	
				}//fin de while			
			} //fin tiene excepciones
						
					if ($num1==0) /******no tiene excepciones******/
					{						
						
						//se consulta la tabla 4 la tabla de estructura - historico de uso del equipo
						$query = "select fecha,equipo,uni_hora,hi,hf from ".$solucionCitas."_000004 where fecha = '".$wfec."' and equipo = '".substr($wequ,0,strpos($wequ,"-"))."'";
						$err6 = mysql_query($query,$conex)or die( mysql_errno()." - Error en el query $query - ".mysql_error() );		
						$num6 = mysql_num_rows($err6);
						$row6 = mysql_fetch_array($err6);
						
						if ($num6>0)
						{
							$fecha=$row6['fecha'];
							$codigo=$row6['equipo']; //equipo
							$uni_hora=$row6['uni_hora'];
							$hi=$row6['hi'];
							$hf=$row6['hf'];
							
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
									
									
								}//for
						}						
						
						if ($num6 == 0) //no tiene registros en la tabla 4 se busca en la tabla 3 pero ya el horario de cada equipo
						{
							
							$query7 = "select codigo,descripcion,uni_hora,hi,hf,activo from ".$solucionCitas."_000003 where codigo='".substr($wequ,0,strpos($wequ,"-"))."'";
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
										
										
									}//for
									
							} //num7>0
												
						} //$num6=0
						
						//validacion que estaba afuera
							$numdia=date("N",strtotime($wfec));
							//se agrega consulta para validar que el medico y el equipo tengan el mismo horario
							$query2 = "select hi, hf from ".$solucionCitas."_000007 ";
							$query2 .= "      where ".$solucionCitas."_000007.Ndia = '".$numdia."' ";  //numero del dia
							if ($caso == 3)
							{
								$query2 .= " and ".$solucionCitas."_000007.equipo = '".substr($wequ,0,strpos($wequ,"-"))."' ";  //codigo equipo
							}
							
							$query2 .= "      and ".$solucionCitas."_000007.activo = 'A' ";
							$query2 .= "  order by ".$solucionCitas."_000007.codigo";
						
							$err2 = mysql_query($query2,$conex)or die( mysql_errno()." - Error en el query $query2 - ".mysql_error() );
							$num2 = mysql_num_rows($err2);
						
						
						if ($num2 > 0 )
						{
						
								//consulta las citas de esa fecha
								$query = "select cod_med,cod_equ,cod_exa,fecha,hi,hf,nom_pac,nit_resp,telefono,edad,comentarios,usuario,activo,cedula,Asistida from ".$solucionCitas."_000001 where fecha='".$wfec."' and cod_equ='".substr($wequ,0,strpos($wequ,"-"))."' and Activo='A' order by hi";
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
									
									//calculo de cuantas citas hay
									$query = "select count(*) 
											  from ".$solucionCitas."_000001 
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
										
										$j++;
									
									}
									else
									{
										
										$amarillo++;
										
										$j++;
									 }	
								}
								
									if ($num4==0)  //todas las citas disponibles
										{
											
											$verde++;
											
											$j++;
										}
						}
						else
						{
						
							$rojo++;
							
							$j++;
						}
					} //iria aca la llave de no excepciones
			} //for
		} //num>0
	}	
		if ($amarillo>0) 
		 {	
			 $color="amarillo"; 
		 }
		 
		 else if ($verde>0)
		 {
			$color="calendar";
		 }
		 
		 else if ($rojo>0)
		 {
			$color="rojo";
		 }	
		
		return $color;
	}
	//**********fin funcion colores inst de la mujer, inst del sueño y neumologia**********
	
   
   if (!isset($month) || $month == "" || $month > 12 || $month < 1)
   {
		$month = date("m");
		
	  
   }
   if (!isset($year) || $year == "" || $year < 1972 || $year > 2036)
   {
      $year = date("Y");
   }

   $timestamp = mktime(0, 0, 0, $month, 1, $year);
   
   $mes=date("F", $timestamp);
   $anio=date("Y", $timestamp);
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
	$current = $mes.' '.$anio;

   if ($month < 2)
   {
      $prevmonth = 12;
      $prevyear = $year - 1;
   }
   else
   {
      $prevmonth = $month - 1;
      $prevyear = $year;
   }

   if ($month > 11)
   {
      $nextmonth = 1;
      $nextyear = $year + 1;
   }
   else
   {
      $nextmonth = $month + 1;
      $nextyear = $year;
   }

   
   $mesAnt = date("F", mktime(0, 0, 0, $prevmonth, 1, $prevyear));
   $anioAnt= date("Y", mktime(0, 0, 0, $prevmonth, 1, $prevyear));

   if ($mesAnt=="January") $mesAnt="Enero";
   if ($mesAnt=="February") $mesAnt="Febrero";
   if ($mesAnt=="March") $mesAnt="Marzo";
   if ($mesAnt=="April") $mesAnt="Abril";
   if ($mesAnt=="May") $mesAnt="Mayo";
   if ($mesAnt=="June") $mesAnt="Junio";
   if ($mesAnt=="July") $mesAnt="Julio";
   if ($mesAnt=="August") $mesAnt="Agosto";
   if ($mesAnt=="September") $mesAnt="Septiembre";
   if ($mesAnt=="October") $mesAnt="Octubre";
   if ($mesAnt=="November") $mesAnt="Noviembre";
   if ($mesAnt=="December") $mesAnt="Diciembre";
   
   
   $mesDes = date("F", mktime(0, 0, 0, $nextmonth, 1, $nextyear));
   $anioDes= date("Y", mktime(0, 0, 0, $nextmonth, 1, $nextyear));

   if ($mesDes=="January") $mesDes="Enero";
   if ($mesDes=="February") $mesDes="Febrero";
   if ($mesDes=="March") $mesDes="Marzo";
   if ($mesDes=="April") $mesDes="Abril";
   if ($mesDes=="May") $mesDes="Mayo";
   if ($mesDes=="June") $mesDes="Junio";
   if ($mesDes=="July") $mesDes="Julio";
   if ($mesDes=="August") $mesDes="Agosto";
   if ($mesDes=="September") $mesDes="Septiembre";
   if ($mesDes=="October") $mesDes="Octubre";
   if ($mesDes=="November") $mesDes="Noviembre";
   if ($mesDes=="December") $mesDes="Diciembre";
   
   $backward = ($mesAnt.' '.$anioAnt);
   $forward = ($mesDes.' '.$anioDes);

   $first = date("w", mktime(0, 0, 0, $month, 1, $year));
   
   $lastday = 28;
   
   for ($i=$lastday;$i<32;$i++)
   {
      if (checkdate($month, $i, $year))
      {
         $lastday = $i;
      }
   }
   
   function AddDay($fday, $fmonth, $fyear)
   {
      global $wemp_pmla;
	  global $solucionCitas;
	  global $wsw;
	  global $caso;
	  global $colorDiaAnt; 
	  global $fest;
	  global $esFestivo;
	  
	  global $parametrosExtras;
	  // $colorDiaAnt="rojo";
	 
	 
	 //Se le agrega un cero si el tamaño de la variable es igual a 1, para el dia y para el mes. 
	 if(strlen($fday) == 1)
		  {
		  $fday='0'.$fday;
		  }
		  else
		  {
		  $fday=$fday;
		  }
		 
		  if(strlen($fmonth) == 1)
		  {
		  $fmonth='0'.$fmonth;
		  }
		else
		  {
		  $fmonth=$fmonth;
		  }
		
		$wfec=$fyear.'-'.$fmonth.'-'.$fday;  //fecha seleccionada para todos las dias		
		$wfec1=date( "Y-m-d" );
		
		
		
      if (!isset($fday) || $fday == "")
      {
         echo '	<TD class="calendar" align="left" valign="top" width=90 height=70>
		&nbsp;
';
      }
      else
      {
	  
		 //dia actual
         if (date("m") == $fmonth && date("Y") == $fyear && date("d") == $fday)
         {													//faltan las otras que son caso 1 modificado (3)
			 if ($caso==3)
			 {
							 
				//calculos para encontrar el dia anterior
				$wfecAnt=strtotime($wfec); //se pasa a formato unix la fecha
				$diaAnt=($wfecAnt-(24*3600)); //le resto un dia a la fecha
				$diaAnt=date("Y-m-d",$diaAnt);  //la pasa a formato fecha
				//$numDiaAnt=date("N",strtotime($diaAnt)); //valor numerico del dia anterior
				
				if ($colorDiaAnt == "rojo" and $wfec>$wfec1)
				{
					$colorDiaAnt=coloresMSN($diaAnt);	//funcion para saber el color del dia anterior
                }					
				 
				//Dia de hoy
				$color1=coloresMSN($wfec);  //funcion para colocar el color de fondo al dia
			    if ($esFestivo>0 and $fest=='on')
				{
					$class="esFestivo";
				}
				else
				{
					$class="";
				}
				
				$schurl = 'dispEquipos.php?wemp_pmla='.$wemp_pmla.'&consultaAjax=10&wfec='.$wfec1.'&wsw='.@$wsw.'&colorDiaAnt='.$colorDiaAnt.'&wbasedato='.$solucionCitas.'&caso='.$caso.'&fest='.$fest.'';
				
				/*echo '	<TD ID="day'.$fday.'" class="'.$color1.' '.$class.'" style="cursor: hand;" align="left" valign="top" width=90 height=70 onMouseOver="tdmouseover(\'day'.$fday.'\')"; onMouseOut="tdcurmouseout(\'day'.$fday.'\')"; 
				onClick="window.open(\''.$schurl.'\', \'schedule\', \'width=534,height=400,scrollbars=yes,resizable=yes\')">
                ';*/

                echo '	<TD ID="day'.$fday.'" class="'.$color1.' '.$class.'" style="cursor: hand;" align="left" valign="top" width=90 height=80	onMouseOver="tdmouseover(\'day'.$fday.'\')"; onMouseOut="tdcurmouseout(\'day'.$fday.'\')"; onClick="cargarIframe(\''.$schurl.$parametrosExtras.'\',this)">';

			 }
			 else if ($caso==2) 
			 {
				
				$numDia=date("N",strtotime($wfec1)); //valor numerico del dia
				 
				//calculos para encontrar el dia anterior
				$wfecAnt=strtotime($wfec); //se pasa a formato unix la fecha
				$diaAnt=($wfecAnt-(24*3600)); //le resto un dia a la fecha
				$diaAnt=date("Y-m-d",$diaAnt);  //la pasa a formato fecha
				$numDiaAnt=date("N",strtotime($diaAnt)); //valor numerico del dia anterior
				
				if ($colorDiaAnt == "rojo" and $wfec>$wfec1)
				{				
					$colorDiaAnt=dispColores($diaAnt,$numDiaAnt);	//funcion para saber el color del dia anterior	 
				}
				
				//Dia de hoy
				$color1=dispColores($wfec, $numDia);  //funcion para colocar el color de fondo al dia
				if ($esFestivo>0 and $fest=='on')
				{
					$class="esFestivo";
				}
				else
				{
					$class="";
				}
				
				$schurl = 'dispMedicos.php?wemp_pmla='.$wemp_pmla.'&consultaAjax=10&wfec='.$wfec1.'&nomdia='.$numDia.'&colorDiaAnt='.$colorDiaAnt.'&wbasedato='.$solucionCitas.'&caso='.$caso.'&fest='.$fest.'';
				
				/*echo '	<TD ID="day'.$fday.'" class="'.$color1.' '.$class.'" style="cursor: hand;" align="left" valign="top" width=90 height=70 
				onMouseOver="tdmouseover(\'day'.$fday.'\')"; onMouseOut="tdcurmouseout(\'day'.$fday.'\')"; 
				onClick="window.open(\''.$schurl.'\', \'schedule\', \'width=534,height=400,scrollbars=yes,resizable=yes\')">
';*/
                echo '	<TD ID="day'.$fday.'" class="'.$color1.' '.$class.'" style="cursor: hand;" align="left" valign="top" width=90 height=70 
				onMouseOver="tdmouseover(\'day'.$fday.'\')"; onMouseOut="tdcurmouseout(\'day'.$fday.'\')"; 
				onClick="cargarIframe(\''.$schurl.$parametrosExtras.'\',this)">';
			 }                                            
			 else if($caso==1) 
			 {
				 
				//calculos para encontrar el dia anterior
				$wfecAnt=strtotime($wfec); //se pasa a formato unix la fecha
				$diaAnt=($wfecAnt-(24*3600)); //le resto un dia a la fecha
				$diaAnt=date("Y-m-d",$diaAnt);  //la pasa a formato fecha
				
				if ($colorDiaAnt == "rojo" and $wfec>$wfec1)
				{ 
					$colorDiaAnt=coloresMSN($diaAnt);	//funcion para saber el color del dia anterior
				}
				 
				//Dia de hoy
				$color1=coloresMSN($wfec);  //funcion para colocar el color de fondo al dia
			    
				if ($esFestivo>0 and $fest=='on')
				{
					$class="esFestivo";
				}
				else
				{
					$class="";
				}
				
				$schurl = 'dispEquipos.php?wemp_pmla='.$wemp_pmla.'&consultaAjax=10&wfec='.$wfec1.'&wsw='.@$wsw.'&colorDiaAnt='.$colorDiaAnt.'&wbasedato='.$solucionCitas.'&caso='.$caso.'&fest='.$fest.'';
				
				/*echo '	<TD ID="day'.$fday.'" class="'.$color1.' '.$class.'" style="cursor: hand;" align="left" valign="top" width=90 height=70 onMouseOver="tdmouseover(\'day'.$fday.'\')"; onMouseOut="tdcurmouseout(\'day'.$fday.'\')"; 
				onClick="window.open(\''.$schurl.'\', \'schedule\', \'width=534,height=400,scrollbars=yes,resizable=yes\')">
                ';*/

                echo ' <TD ID="day'.$fday.'" class="'.$color1.' '.$class.'" style="cursor: hand;" align="left" valign="top" width=90 height=70 onMouseOver="tdmouseover(\'day'.$fday.'\')"; onMouseOut="tdcurmouseout(\'day'.$fday.'\')"; onClick="cargarIframe(\''.$schurl.$parametrosExtras.'\',this)">';
				
			 }
		 
            
         }
         else  //todos los dias
         {                                            //faltan las otras citas
				 if ($caso==3) 
				 {
				 				 
				 //calculos para encontrar el dia anterior
				 $wfecAnt=strtotime($wfec); //se pasa a formato unix la fecha
				 $diaAnt=($wfecAnt-(24*3600)); //le resto un dia a la fecha
				 $diaAnt=date("Y-m-d",$diaAnt);  //la pasa a formato fecha
				 //$numDiaAnt=date("N",strtotime($diaAnt)); //valor numerico del dia anterior
				 
				if ($colorDiaAnt == "rojo" and $wfec>$wfec1)
				{
					$colorDiaAnt=coloresMSN($diaAnt);	//funcion para saber el color del dia anterior
				}

				 
				 //Todos los dias
				  
				 $color1=coloresMSN($wfec);
				 if ($esFestivo>0 and $fest=='on')
				 {
					$class="esFestivo";
				 }
				 else
				 {
					$class="";
				 }
				 
					$schurl = 'dispEquipos.php?wemp_pmla='.$wemp_pmla.'&consultaAjax=10&wfec='.$wfec.'&wsw='.@$wsw.'&colorDiaAnt='.$colorDiaAnt.'&wbasedato='.$solucionCitas.'&caso='.$caso.'&fest='.$fest.'';

					/*echo '	<TD ID="day'.$fday.'" class="'.$color1.' '.$class.'" style="cursor: hand" align="center" valign="top" width=90 height=70 
					onMouseOver="tdmouseover(\'day'.$fday.'\')"; onMouseOut="tdmouseout(\'day'.$fday.'\')"; 
					onClick="window.open(\''.$schurl.'\', \'schedule\', \'width=534,height=400,scrollbars=yes,resizable=yes\')">';*/

					echo '	<TD ID="day'.$fday.'" class="'.$color1.' '.$class.'" style="cursor: hand;" align="left" valign="top" width=90 height=70 onMouseOver="tdmouseover(\'day'.$fday.'\')"; onMouseOut="tdcurmouseout(\'day'.$fday.'\')";	onClick="cargarIframe(\''.$schurl.$parametrosExtras.'\',this)">';

				 }
				 else if ($caso==2) 
				 {

				 $numDia=date("N",strtotime($wfec));  //valor numerico del dia
				 
				 //calculos para encontrar el dia anterior
				 $wfecAnt=strtotime($wfec); //se pasa a formato unix la fecha
				 $diaAnt=($wfecAnt-(24*3600)); //le resto un dia a la fecha
				 $diaAnt=date("Y-m-d",$diaAnt);  //la pasa a formato fecha
				 $numDiaAnt=date("N",strtotime($diaAnt)); //valor numerico del dia anterior
				 
				 if ($colorDiaAnt == "rojo" and $wfec>$wfec1)
				 {
					$colorDiaAnt=dispColores($diaAnt,$numDiaAnt);	//funcion para saber el color del dia anterior	
				 }
				 
				 //Todos los dias
				  
				 $color1=dispColores($wfec, $numDia);
				 
				 if ($esFestivo>0 and $fest=='on')
				 {
					$class="esFestivo";
				 }
				 else
				 {
					$class="";
				 }

				 /*$schurl = 'dispMedicos.php?wemp_pmla='.$wemp_pmla.'&consultaAjax=10&wfec='.$wfec.'&nomdia='.$numDia.'&colorDiaAnt='.$colorDiaAnt.'&wbasedato='.$solucionCitas.'&caso='.$caso.'&fest='.$fest.'';
					echo '	<TD ID="day'.$fday.'" class="'.$color1.' '.$class.'" style="cursor: hand" align="center" valign="top" width=90 height=70 
				onMouseOver="tdmouseover(\'day'.$fday.'\')"; onMouseOut="tdmouseout(\'day'.$fday.'\')"; 
				onClick="window.open(\''.$schurl.'\', \'schedule\', \'width=534,height=400,scrollbars=yes,resizable=yes\')">';*/

				$schurl = 'dispMedicos.php?wemp_pmla='.$wemp_pmla.'&consultaAjax=10&wfec='.$wfec.'&nomdia='.$numDia.'&colorDiaAnt='.$colorDiaAnt.'&wbasedato='.$solucionCitas.'&caso='.$caso.'&fest='.$fest.'';
				
				echo '	<TD ID="day'.$fday.'" class="'.$color1.' '.$class.'" style="cursor: hand" align="center" valign="top" width=90 height=70 onMouseOver="tdmouseover(\'day'.$fday.'\')"; onMouseOut="tdmouseout(\'day'.$fday.'\')"; onClick="cargarIframe(\''.$schurl.$parametrosExtras.'\',this)">';

				 }
														
				 else if($caso==1) 
				 { 
					 
					 //calculos para encontrar el dia anterior
					 $wfecAnt=strtotime($wfec); //se pasa a formato unix la fecha
					 $diaAnt=($wfecAnt-(24*3600)); //le resto un dia a la fecha
					 $diaAnt=date("Y-m-d",$diaAnt);  //la pasa a formato fecha
					 //$numDiaAnt=date("N",strtotime($diaAnt)); //valor numerico del dia anterior
					
					 if ($colorDiaAnt == "rojo" and $wfec>$wfec1)
					{
					 $colorDiaAnt=coloresMSN($diaAnt);	//funcion para saber el color del dia anterior	
					}
					 
					 //Todos los dias
					  
					 $color1=coloresMSN($wfec);
					 
					 if ($esFestivo>0 and $fest=='on')
				     {
						$class="esFestivo";
				     }
				     else
				     {
					    $class="";
				     }
					 
						$schurl = 'dispEquipos.php?wemp_pmla='.$wemp_pmla.'&consultaAjax=10&wfec='.$wfec.'&wsw='.@$wsw.'&colorDiaAnt='.$colorDiaAnt.'&wbasedato='.$solucionCitas.'&caso='.$caso.'&fest='.$fest.'';
						
						/*echo '	<TD ID="day'.$fday.'" class="'.$color1.' '.$class.'" style="cursor: hand" align="center" valign="top" width=90 height=70 
						onMouseOver="tdmouseover(\'day'.$fday.'\')"; onMouseOut="tdmouseout(\'day'.$fday.'\')"; 
						onClick="window.open(\''.$schurl.'\', \'schedule\', \'width=534,height=400,scrollbars=yes,resizable=yes\')">';*/

						echo '	<TD ID="day'.$fday.'" class="'.$color1.' '.$class.'" style="cursor: hand;" align="left" valign="top" width=90 height=70	onMouseOver="tdmouseover(\'day'.$fday.'\')"; onMouseOut="tdcurmouseout(\'day'.$fday.'\')"; onClick="cargarIframe(\''.$schurl.$parametrosExtras.'\',this)">';

				 }
		 
         }
		   
		 // Muestra el tooltip
		 // 2020-01-17 Se desactiva Tooltip retirandolo de la clase para evitar su visualización
		 // class='tooltip preload {direction:ne;width:400px;}'
		 if ($caso==3) 
		 {
			$dato = "<a href='../procesos/dispToolEquipos.php?wemp_pmla=".$wemp_pmla."&consultaAjax=10&wfec=".$wfec."&wbasedato=".$solucionCitas."&caso=".$caso."&fest=".$fest."' onclick='return false;' ><font size=7><b>".$fday."</b></font></a>";
		 }
		 else if ($caso==2) 
		 {
			$dato = "<a href='../procesos/dispTool.php?wemp_pmla=".$wemp_pmla."&consultaAjax=10&wfec=".$wfec."&nomdia=".$numDia."&wbasedato=".$solucionCitas."&caso=".$caso."&fest=".$fest."' onclick='return false;' ><font size=7><b>".$fday."</b></font></a>";
		 }
		 else if($caso==1) 
		 {
			$dato = "<a href='../procesos/dispToolEquipos.php?wemp_pmla=".$wemp_pmla."&consultaAjax=10&wfec=".$wfec."&wbasedato=".$solucionCitas."&caso=".$caso."&fest=".$fest."' onclick='return false;' ><font size=7><b>".$fday."</b></font></a>";
		 }

         echo '<b>'.@$dato.'</b></a><br>';

         if (isset($fvar) && $fvar != "")
         {
            echo '		<A class=\'calendar\' style="cursor: hand" onClick="javascript:window.open(\''.$schurl.'\', 
		\'schedule\', \'width=534,height=400,scrollbars=yes,resizable=yes\')">
';
            echo '		'.$fvar.'
		</A>';
         }
      }
      echo '	</TD>
';
   }

  

   echo '<HTML>
<HEAD>
   <TITLE>'.$calender_title.'</TITLE>
   <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=ISO-8859-1">';
   echo '<STYLE TYPE="text/css">
	<!--
		BODY {background-color: #'.$background_color.'; border-style: none; border-width: 0px; color: #'.$plain_text_color.'; 
			font-family: Arial; font-size: 15px; font-style: normal; margin: 0px; padding: 4px;
			text-align: center; text-decoration: none; text-indent: 0px}
		A {border-style: none; border-width: 0px; color: #'.$link_color.'; font-family: Arial; font-size: 25px; 
			font-style: normal; margin: 0px; padding: 0px; text-align: left; text-decoration: none;
			text-indent: 0px}
		A.normal {font-size: 12px; text-decoration: underline}
		A.calendar {color: #'.$calendar_link_color.'; font-size:10px}
		A.bottom {color: #'.$link_color.'; font-size:10px}
		P {font-size: 10px; text-align:center; color: #'.$link_color,'}
		IMG {border-style: none; border-width: 0px; margin: 0px; padding: 0px}
		TABLE.externa { margin: 0px; padding: 0px; border-width: none; font-size: 12px; text-indent: 0px;
			font-weight: normal; width: 50%; height: 50%; background-color: #'.$calendar_bg_color.';  color: #'.$plain_text_color.'}
		TABLE.top {width: 100%; height: 60px}
		TABLE.form {width: 100%; height: 30px; text-align: center; border-style:none; border-width: 0px; 
			background-color: #'.$background_color.'; color: #'.$plain_text_color.'}
		TR {border-style: none; border-width: 0px; margin: 0px; padding: 0px}
		TD.top {padding: 4px; font-size: 16px; height: 60px; text-align:center; font-weight: bold; 
			border-style: none; border-width: 0px}
		TD.ends {padding: 10px; text-align:center; color: #0466F4}
		TD.form {padding: 0px; font-size: 12px; border-style: none; border-width: 0px; 
			background-color: #'.$background_color.'; color: #'.$plain_text_color.'}
		TD.days {padding: 2px; font-size: 22px; width: 90px; height: 40px; text-align:center; background-color: #38B0DE ; color: #FFFFFF; font-weight: bold}
		TD.curday {width: 90px; text-align: left; font-size: 10px; height: 70px; color:#0466F4; background-color: #'.$current_day_color.'}
		TD.calendar {width: 90px; text-align: left; font-size: 10px; height: 70px; color: #000000;}
		TD.hoy {padding: 2px; font-size: 25px; text-align:center; background-color: #38B0DE ; color: #FFFFFF; font-weight: bold}
		TD.titulo {padding: 2px; font-size: 25px; text-align:center; background-color: #38B0DE ; color: #FFFFFF; font-weight: bold}
		TABLE.buscar {background-color: #ffffff ; color: #000000; text-align: center;}
		TD.buscar{background-color: #ffffff}
		TD.fila1{background-color: #C3D9FF; color: #000000; font-size: 10pt}
		TD.fila2{background-color: #E8EEF7; color: #000000; font-size: 10pt}
		div.titulopagina{font-family: verdana; font-size: 18pt; overflow: hidden; text-transform: uppercase; font-weight: bold;
          height: 30px; border-top-color: #2A5DB0; border-top-width: 1px; border-left-color: #2A5DB0; border-left-width: 1px;
          border-right-color: #2A5DB0; border-bottom-color: #2A5DB0; border-bottom-width: 1px; margin: 2pt;}
		SPAN.version{font-family: verdana; font-size: 8px;}
		TD.amarillo{background-color: #FFFFCC; width: 90px; text-align: left; font-size: 10px; height: 70px; color:#000000;}
		TD.rojo{background-color:#FFCCCC; width: 90px; text-align: left; font-size: 10px; height: 70px; color:#000000;}
		div.derecha{float: right;}
		.esFestivo {border:2px solid red;}
	-->
	</STYLE>
';
	

	if ($wemp_pmla == 01)
	{
		encabezado1("ASIGNACI&Oacute;N DE CITAS", "2020-01-20", $wbasedato );
	}
	else
	{
		encabezado1("ASIGNACI&Oacute;N DE CITAS", "2020-01-20", "logo_".$wbasedato );
	}
	
	
	echo "<BODY TOPMARGIN=0 LEFTMARGIN=0 MARGINHEIGHT=0 MARGINWIDTH=0 >
	<CENTER>";
	
	
	echo "<table border=0 width='1550' align=center>";  //tabla para ubicacion
	echo "<tr>";
	echo "<td width=650>";
	
	echo "<div id='marco' style='width:90%;'>"; //externo
	
	echo '<TABLE cellspacing=0 cellpadding=0 width=560 border=0 class="externa">
	<TR>
	<TD class="form" align="center" valign="bottom" width="100%" COLSPAN=7>
		<FORM METHOD="post" ACTION="calendar.php?empresa='.$solucionCitas.'&wemp_pmla='.$wemp_pmla.'&fest='.$fest.'&consultaAjax='.$parametrosExtrasOri.'">
		<TABLE class="form" cellspacing=0 cellpadding=0 width="100%" border=0>
		<TR>
		<TD class="form" align="center" valign="bottom">
			<b>Mes:</b> <select name="month">
';
			for ($j=1;$j<=12;$j++)
			{
			   echo'<option value='.$j;
			   if ($month == $j)
			   {
			      echo ' selected';
			   }
			   $mes=date("F", mktime(0, 0, 0, $j, 1, 0));
			   
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
				echo '>'.$mes.'
			   ';
			}
			
		    echo"<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
			echo"<input type='hidden' id='solucionCitas' name='solucionCitas' value='".$solucionCitas."'>";
			if ($caso == 3 or $caso == 1)
			{
				echo"<input type='hidden' id='wsw' name='wsw' value='".$wsw."'>";
			}
			
			echo"<input type='hidden' id='caso' name='caso' value='".$caso."'>";
			
			echo '			</select>			
		         &nbsp;&nbsp;<b>A&ntilde;o:</b> <select name="year">
';
			for ($j=1972;$j<=2036;$j++)
			{
			   echo'<option value='.$j;
			   if ($year == $j)
			   {
			      echo ' selected';
			   }
			   echo '>'.$j.'
			   ';
			}
			echo '			</select>
			 &nbsp;&nbsp;<input type="submit" value="Enviar">			
		</TD>
		</TR>
		
		</TABLE>
		</FORM>
	</TD>	
	</TR>
	<TR>
	<TD align="center" valign="middle" height=60 COLSPAN=7>
		<TABLE class="top" cellspacing=0 cellpadding=0 width=560 border=0>
		<TR>';
		
		if ($caso == 3 or $caso ==1)
		{
			echo '<TD class="ends" nowrap align="center" valign="bottom" >
				<b><A HREF="calendar.php?month='.$prevmonth.'&year='.$prevyear.'&empresa='.$solucionCitas.'&wemp_pmla='.$wemp_pmla.'&caso='.$caso.'&wsw='.$wsw.'&fest='.$fest.'&consultaAjax='.$parametrosExtrasOri.'"><< '.$backward.'</a></b>
			</TD>';
		}
		else
		{
				echo '<TD class="ends" nowrap align="center" valign="bottom" >
				<b><A HREF="calendar.php?month='.$prevmonth.'&year='.$prevyear.'&empresa='.$solucionCitas.'&wemp_pmla='.$wemp_pmla.'&caso='.$caso.'&fest='.$fest.'&consultaAjax='.$parametrosExtrasOri.'"><< '.$backward.'</a></b>
			</TD>';
		}
		
	echo'<TD class="titulo" nowrap align="center" valign="middle" width=350>
';
   if (isset($calender_title_image) && $calender_title_image != '')
   {
      echo '			<img src="'.$calender_title_image.'">';
   }
   else
   {
      echo '<font size="6"><b>'.$calender_title."</b></font>";
   }
   echo '<br><div style="background:#2A5DB0">'.$current.'</div>
		
		</TD>';
	
		if ($caso == 3 or $caso == 1)
		{
			echo '<TD class="ends" nowrap align="center" valign="bottom">
				<b><A HREF="calendar.php?month='.$nextmonth.'&year='.$nextyear.'&empresa='.$solucionCitas.'&wemp_pmla='.$wemp_pmla.'&caso='.$caso.'&wsw='.$wsw.'&fest='.$fest.'&consultaAjax='.$parametrosExtrasOri.'">'.$forward.' >></a></b>
			</TD>';
		}
		else
		{
			echo '<TD class="ends" nowrap align="center" valign="bottom">
				<b><A HREF="calendar.php?month='.$nextmonth.'&year='.$nextyear.'&empresa='.$solucionCitas.'&wemp_pmla='.$wemp_pmla.'&caso='.$caso.'&fest='.$fest.'&consultaAjax='.$parametrosExtrasOri.'">'.$forward.' >></a></b>
			</TD>';
		}
	echo'	</TR>
		</TABLE>
	</TD>
	</TR>
	<TR>';
   if (isset($start_day) && $start_day <= 6 && $start_day >= 0)
   {
      $n = $start_day;
   }
   else
   {
      $n = 0;
   } 
   for ($i=0;$i<7;$i++)
   {
      if ($n > 6)
      {
         $n = 0;
      }
      if ($n == 0)
      {
         echo '	<TD class="days" nowrap align="center" valign="middle" width=90 height=40>
		Domingo
	</TD>';
      }
      if ($n == 1)
      {
         echo '	<TD class="days" nowrap align="center" valign="middle" width=90 height=40>
		Lunes
	</TD>';
      }
      if ($n == 2)
      {
         echo '	<TD class="days" nowrap align="center" valign="middle" width=90 height=40>
		Martes
	</TD>';
      }
      if ($n == 3)
      {
         echo '	<TD class="days" nowrap align="center" valign="middle" width=90 height=40>
		Miercoles
	</TD>';
      }
      if ($n == 4)
      {
         echo '	<TD class="days" nowrap align="center" valign="middle" width=90 height=40>
		Jueves
	</TD>';
      }
      if ($n == 5)
      {
         echo '	<TD class="days" nowrap align="center" valign="middle" width=90 height=40>
		Viernes
	</TD>';
      }
      if ($n == 6)
      {
         echo '	<TD class="days" nowrap align="center" valign="middle" width=90 height=40>
		Sabado
	</TD>';
      }
      $n++;
   }
   echo'	</TR>
';
   $calday = 1;
   $colorDiaAnt="rojo";
   if ($caso==1 || $caso==3)
   {
		consultaMedicosEquipos();
   }
   while ($calday <= $lastday)
   {
/* Alternate beginning day of the week for calendar view was created by Marion Heider of clixworx.net. */
      echo '<TR>';
      for ($j=0;$j<7;$j++)
      {
         if ($j == 0)
         {
            $n = $start_day;
         }
         else
         {
            if ($n < 6)
            {
               $n = $n + 1;
            }
            else
            {
               $n = 0;
            }
         }
         if ($calday == 1)
         {
            if ($first == $n)
            {              
			
            AddDay($calday, $month, $year);
               $calday++;
            }
            else
            {
               AddDay('', '', '');
            }
         }
         else
         {
            if ($calday > $lastday)
            {
               AddDay('', '', '');
            }
            else
            {
               //$info = FillDay($db, $n, $calday, $month, $year);
               AddDay($calday, $month, $year);
               $calday++;
            }
         }
      } 
      echo '</TR>';
	 
   }
    echo '<TR>';
	$dia=date("l");
	$diaNum=date("d");
	$mes=date("F");
	$anio=date("Y");
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
	  echo ' <TD class="hoy" colspan="7" align="center" valign="bottom"><b>Hoy:</b> '.$dia.' '.$diaNum.' de '.$mes .' de '.$anio.'
		</TD>';
	  echo '</TR>';
	  echo "<TR>";
	  echo "<TD colspan='7' aling='center' class='buscar'>";
	  
	  echo "<div align=center>";
	  echo "<table border=1 align=center >";
	  echo "<tr>";
	  echo "<td bgcolor='#FFFFCC'>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>Hay citas disponibles</td>";
	  echo "<td bgcolor='#C4E2FF'>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>Todas las citas disponibles</td>";
	  echo "<td bgcolor='#FFCCCC'>&nbsp;&nbsp;&nbsp;&nbsp;</td><td>No hay citas disponibles</td>";
	  echo "<tr>";
	  echo "</table>";
	  echo "</div>";
      echo "<br>";
	  echo "<div>";
	  echo "<table align='center' border='0'>"; 
	  if ($caso == 1 or $caso == 3)
	  {
		echo "<tr>";
		echo "<td class='buscar' align=center><b>BUSQUEDA : </b><A HREF='/MATRIX/Citas/Reportes/busqPacientesEqu.php?empresa=".$empresa."&wemp_pmla=".$wemp_pmla."' target='_blank'><IMG SRC='/MATRIX/images/medical/Citas/find.gif'></A></td></tr>";
	  }
	  else
	  {
		echo "<tr>";
		echo "<td class='buscar' align=center><b>BUSQUEDA : </b><A HREF='/MATRIX/Citas/Reportes/busqPacientesMed.php?empresa=".$empresa."&wemp_pmla=".$wemp_pmla."' target='_blank'><IMG SRC='/MATRIX/images/medical/Citas/find.gif'></A></td></tr>";
	  }
	  echo "<tr>";
	  echo "<td class='buscar' align='center'><br><input name='button' type='button' style='width:100' onclick='window.close();' value='Cerrar' /></td>"; 
	  echo "</tr>";
	  echo "</table>";
	  echo "</div>";
	  
	  echo "</TD>";
	  echo "</TR>";
   echo '	</TABLE>
	</CENTER>';
	echo "</div>";  //externo
	
	echo "</td>";
	echo "<td >"; //para ubicacion
	$wfec1=date( "Y-m-d" );
	$numDia=date("N",strtotime($wfec1));

	if ($caso == 1 or $caso == 3)
        $pageUrl = '../../citas/procesos/dispEquipos.php?wemp_pmla='.$wemp_pmla.'&consultaAjax=10&wfec='.$wfec1.'&wsw='.@$wsw.'&colorDiaAnt='.$colorDiaAnt.'&wbasedato='.$solucionCitas.'&caso='.$caso.'&fest='.$fest.''.$parametrosExtras;
	else
		$pageUrl = '../../citas/procesos/dispMedicos.php?wemp_pmla='.$wemp_pmla.'&consultaAjax=10&wfec='.$wfec1.'&nomdia='.$numDia.'&colorDiaAnt='.$colorDiaAnt.'&wbasedato='.$solucionCitas.'&caso='.$caso.'&fest='.$fest.''.$parametrosExtras;

	echo "<div width='100%' height='100%'>";
	echo "<iframe id='ifcitas' src=".$pageUrl." width='100%' height='600px' frameborder='0'  border: 1em solid gray;></iframe>";
	echo "</div>";
	
	echo "</td>";
	echo "</tr>";
	echo "</table>";  //tabla para ubicacion

			
	$current1=explode(" ",$current);													
	$month1 = $current1[0];
	if ($month1=="Enero") $month1="January";
	if ($month1=="Febrero") $month1="February";
	if ($month1=="Marzo") $month1="March";
	if ($month1=="Abril") $month1="April";
	if ($month1=="Mayo") $month1="May";
	if ($month1=="Junio") $month1="June";
	if ($month1=="Julio") $month1="July";
	if ($month1=="Agosto") $month1="August";
	if ($month1=="Septiembre") $month1="September";
	if ($month1=="Octubre") $month1="October";
	if ($month1=="Noviembre") $month1="November";
	if ($month1=="Diciembre") $month1="December";
	
	$month2=date("m", strtotime( $month1 ) );
	$year1 = $current1[1];
	//echo "<meta name='met' id='met' http-equiv='refresh' content='60;url=calendar.php?empresa=".$solucionCitas."&wemp_pmla=$wemp_pmla&caso=".$caso."&wsw=".@$wsw."&month=".$month2."&year=".$year1."&fest=".$fest."&consultaAjax='>";
	
	echo '	
	</BODY>
	</HTML>';
}
?>
