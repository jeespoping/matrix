<?php
include_once("conex.php");
header("Content-Type: text/html;charset=ISO-8859-1");
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>MATRIX</title>
	
</head>
<BODY>
<script type="text/javascript">
<!--
	
//-->
</script>
<?php

/*Creacion: 2012-08-19  Este script se crea para mostrar la lista de medicos disponibles para las citas de caso 2 que son las de medicos, se muestra la lista de medicos cuando se pasa sobre una fecha en el calendario. Viviana Rodas
Modificacion:
            2018-09-11 Arleyda Insignares. Se unifican dos consultas a la tabla 'prefijo_000009', con el objetivo de optimizar la consulta de disponibilidad de las citas.
			2014-02-04 Se modifica la consulta a la tabla de horarios de los medicos: Se agrega union a la tabla de excepciones para que liste los medicos tiene excepciones ese dia. Viviana Rodas
			2013-11-26 Se modifica el script en la consulta de excepciones para que no filtre las observaciones creadas desde el programa de excepciones. Viviana Rodas
			2013-09-03 Se modifica el script para calcular el total de citas cuando se tienen los horarios divididos, ya sea de excepciones o de horario normal. Viviana Rodas
			2013-08-29 Se modifica el script para corregir un error de un contador que tenia un echo. Viviana Rodas
			2013-05-16 Se modifica el script con la variable fest que es la que valida si se asignan citas los dias festivos, si esta en on, es porque si se asignan citas los festivos en esa unidad.
			2013-04-04 Se agrega el mensaje de "no atiende este dia" para cuando un medico tiene una excepcion de no disponibilidad. Viviana Rodas
		    2013-03-06 Se agrega en la consulta de la tabla 10 de horarios y la tabla 14 de estructura la validacion de unihora != 0. Viviana Rodas
			2012-11-27 Se agrega la verificacion de los dias festivos para dependiendo de ese mostrar la lista de medicos o no. Viviana Rodas
			2012-10-31 Se cambia la funcion disponibilidadMedicos para evaluar cuando en un dia del calendario no hay atencion, para que muestre ese dia en color rojo y muestre un mensaje diciendo no hay atencion. Viviana Rodas
			2012-10-30 Se inicializo total citas en 0 dentro del for grande y tiempo gastado en 0  despues de la consulta de hay citas. 
			  Viviana Rodas
			  
*/
include_once("root/comun.php");


session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{	
    $conex = obtenerConexionBD("matrix");
    $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
    // $wbasedato = strtolower( $institucion->baseDeDatos );
		
	//************************funcion******************** 
    function disponibilidadMedicos($wfec, $nomdia)
	{
	
	
	//echo "fecha ".$wfec." dia ". $nomdia;
	    global $conex;
		global $totalCitas;
		global $citasAsig;
		global $citasDisp;
		global $wbasedato;
		global $amarillo;
		global $rojo;
		global $verde;
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
		echo "<table align='center' border=1 cellspacing=0 cellspadding=0 style='font-size:15px;'>";
		echo "<th colspan='2'><font color='#ffffff'><font size='4'>No se asignan citas, dia Festivo</font></th>";
		echo "</table>";
	}
	else
	{
		
		echo "<br>";
		echo "<table align='center' border=1 cellspacing=0 cellspadding=0 style='font-size:15px;'>";
		//echo "<th colspan='2'><font color='#ffffff'>Citas del Dia</font></th>";
		//consulta los medicos 
		/*select Codigo, Descripcion 
				  from ".$wbasedato."_000010 
				  where Activo = 'A'
				  and Dia='".$nomdia."'
				  Group by Codigo, Descripcion 
				  Order by Descripcion
		*/
		$query = "select Codigo, Descripcion 
						  from ".$wbasedato."_000010 
						  where Activo = 'A'
						  and Dia='".$nomdia."' 
					union			  
					SELECT a.Codigo, b.Descripcion
						FROM ".$wbasedato."_000012 a, ".$wbasedato."_000010 b
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
			if ($nums>0)
			{
				echo "<tr><td colspan='2' align='center'><font color='##99FF00'><b>ESTE DIA ES FESTIVO</b></font></td></tr>";
			}
			echo "<tr><td colspan='2' align='center'><font color='#ffffff'>Citas del Dia</font></td></tr>";
			
			for( $i = 0; $rows = mysql_fetch_array( $err ); $i++ )
			{
				$totalCitas = 0;
				$tiempoGastado = 0; //se copio de dispEquipos
				
				$cod=$rows['Codigo'];
				$des=$rows['Descripcion'];
				$wequ=$cod."-".$des; 
				$codigo = $cod;
				//echo "<br>".$wequ." "; //imprime todos
				
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
							echo "<td><font color='#ffffff'>".$wequ."</font></td> ";
							echo "<td><font color='#ffffff'> No atiende este dia <font></td></tr>";
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
										and Activo = 'A'  ";
									  
									  $err4      = mysql_query($query,$conex)or die( mysql_errno()." - Error en el query $query - ".mysql_error() );	
									  $citasAsig = mysql_num_rows($err4);
									  
									  // $tiempoGastado = 0;
									  
									  if ($citasAsig>0)//tiene citas para asignar
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
												// $tiempoGastado+=($hfCitaUnix-$hiCitaUnix)/($exUniHora*60)-1;
												
												//echo "<br> cita: fecha: ".$fechaCita." hora ".$HiCita." fin ".$HfCita." paciente ".$cedula;
											}
											
											//calculo de cuantas disponibles
											// $query = "select count(*) 
													  // from ".$wbasedato."_000009 
													  // where Cod_equ= '".$codigo."'
													  // and Fecha = '".$wfec."' 
													  // and Activo = 'A'";
											
											// $err5 = mysql_query($query,$conex)or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
											
											// $row5=mysql_fetch_array($err5);
											// $citasAsig=$row5[0];
											// $citasDisp=$totalCitas-$citasAsig-$tiempoGastado;
											
											// if ($citasDisp==0)
											// {
											    // $j++;
												// $rojo++;
												// echo "<tr>";
												// echo "<td><font color='#ffffff'> ".$wequ."</font></td> ";
												// echo "<td><font color='#ffffff'>No tiene citas disponibles</font></td>";
												// echo "</tr>";
											
											// }
											// else
											// {
												// $j++;
												
												// $amarillo++;
												// echo "<tr>";
												// echo "<td><font color='#ffffff'>".$wequ."<font></td> ";
												 // echo " <td><font color='#ffffff'>Citas Disponibles: ".floor($citasDisp)."</font></td>";
												 // echo "</tr>";
											// }
											
											   
										}
										// if ($num4==0)
										// {
											// $j++;
											// $verde++;
											// echo "<tr>";
											// echo "<td><font color='#ffffff'>".$wequ."</font></td> ";
										    // echo "<td><font color='#ffffff'> Todas las citas disponibles </font></td></tr>";
										// }
								$rows1 = mysql_fetch_array($err1);

								if( !$rows1 ){
									if ($citasAsig==0)  //todas las citas disponibles
									{
										$j++;
										$verde++;
										echo "<tr>";
										echo "<td><font color='#ffffff'>".$wequ."</font></td> ";
										echo "<td><font color='#ffffff'> Todas las citas disponibles </font></td></tr>";
									}
									else
									{
										    //calculo de cuantas disponibles
										    /* 	$query = "select count(*) 
													  from ".$wbasedato."_000009 
													  where Cod_equ= '".$codigo."'
													  and Fecha = '".$wfec."' 
													  and Activo = 'A'";
											
											$err5 = mysql_query($query,$conex)or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
											
											$row5=mysql_fetch_array($err5);
											$citasAsig=$row5[0]; */
											
											$citasDisp=$totalCitas-$citasAsig-$tiempoGastado;
																					
											if ($citasDisp==0)
											{
											    $j++;
												$rojo++;
												echo "<tr>";
												echo "<td><font color='#ffffff'> ".$wequ."</font></td> ";
												echo "<td><font color='#ffffff'>No tiene citas disponibles</font></td>";
												echo "</tr>";
											
											}
											else
											{
												$j++;
												$amarillo++;
												echo "<tr>";
												echo "<td><font color='#ffffff'>".$wequ."<font></td> ";
												 echo " <td><font color='#ffffff'>Citas Disponibles: ".floor($citasDisp)."</font></td>";
												 echo "</tr>";
											}		
									}
								}
				        
				   //echo " ".$exUniHora." ".$exHi." ".$exHf." ".$exConsul;
					  
						}
					}//while excepciones
				}
				
				if($num1 == 0) //no tiene excepciones, se consulta si tiene registros en la tabla citascs14
				{  
					$totalCitas = 0;
					
					$query = "select Codigo,Uni_hora, Hi, Hf, Consultorio  from ".$wbasedato."_000014 ";
					$query .= "  where Codigo = '".substr($wequ,0,strpos($wequ,"-"))."'";
					$query .= "  and Fecha = '".$wfec."'";
					$query .= "  and Uni_hora != 0";
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
									  // Group by Codigo 
							$err3 = mysql_query($query,$conex) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );	
							$num3 = mysql_num_rows($err3);
							// $rows3 = mysql_fetch_array($err3);
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
								echo "<tr>";
								echo "<td><font color='#ffffff'>".$wequ."</font></td> ";
								echo "<td><font color='#ffffff'> No tiene citas disponibles</font></td>";
								echo "</tr>";
								$j++;
							}	
						}	

						if  (@$num3 >0 or $num6 >0)
						{
							//consulta las citas de esa fecha
							$query = "select Cod_equ, Cod_exa, Fecha, Hi, Hf, Cedula, Nom_pac, Nit_res, Asistida, Activo, Atendido
									  from ".$wbasedato."_000009
									  where Cod_equ= '".$codigo."'
									    and Fecha = '".$wfec."'
									    and Activo = 'A' ";
									  
									  $err4      = mysql_query($query,$conex)or die( mysql_errno()." - Error en el query $query - ".mysql_error() );	
									  $citasAsig = mysql_num_rows($err4);
									  
									  $tiempoGastado = 0;
									  
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
												$tiempoGastado+=($hfCitaUnix-$hiCitaUnix)/($uniHora*60)-1;
												
												//echo "<br> cita: fecha: ".$fechaCita." hora ".$HiCita." fin ".$HfCita." paciente ".$cedula;
											}
											//*** 2018-08-24 Desactivador para optimizar el código y darle mayor rendimiento
											//calculo de cuantas disponibles
											/*  $query = "select count(*) 
													  from ".$wbasedato."_000009 
													  where Cod_equ= '".$codigo."'
													  and Fecha = '".$wfec."' 
													  and Activo = 'A' ";
											
											$err5 = mysql_query($query,$conex)or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
											
											$row5=mysql_fetch_array($err5);
											$citasAsig=$row5[0]; */
											
											$citasDisp=$totalCitas-$citasAsig-$tiempoGastado;
											
											if ($citasDisp==0)
											{
												
												$rojo++;
												echo "<tr>";
												echo "<td><font color='#ffffff'>".$wequ."</font></td> ";
												echo "<td><font color='#ffffff'> No tiene citas disponibles</font></td>";
												echo "</tr>";
												$j++;
											
											}
											else
											{
												
												$amarillo++;
												echo "<tr>";
												echo "<td><font color='#ffffff'>".$wequ."</font></td> ";
												echo " <td><font color='#ffffff'>Citas Disponibles: ".floor($citasDisp)."</font></td>";
												echo "</tr>";
												$j++;
											 }
											
											
											
										}

									  if ($citasAsig==0)  //todas las citas disponibles
										{
											
											$verde++;
											echo "<tr>";
											echo "<td><font color='#ffffff'>".$wequ."</font></td> ";
										    echo "<td><font color='#ffffff'> Todas las citas disponibles </font></td></tr>";
											$j++;
										}
						} //if num3 o num 6						
				} //no excepciones
			} //for   
			
			echo "</table>";
					
		} //medicos
		else
		{
			echo "<table align='center' border=1 cellspacing=0 cellspadding=0 style='font-size:15px;'>";
			echo "<th colspan='2'><font color='#ffffff'><font size='4'>No se asignan citas este dia</font></th>";
			echo "</table>";
			
		}
		
	}	   
 } //funcion

//***********************fin funcion*****************
	echo "<form name='disMedicos' accept-charset='UTF-8'>";
	disponibilidadMedicos($wfec, $nomdia);
	echo "</form>";
	
	echo "</body>";
	echo "</html>";
		
} 	
?>

			