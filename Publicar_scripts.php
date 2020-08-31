<?php
include_once("conex.php");
//=========================================================================================================================================\\
//        PUBLICAR SCRIPTS         
//=========================================================================================================================================\\
//DESCRIPCION:
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION: 	
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='05-Octubre-2017';
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
//                
//
//--------------------------------------------------------------------------------------------------------------------------------------------


//--------------------------------------------------------------------------------------------------------------------------------------------                                        
// 	EJECUCION DEL SCRIPT
//--------------------------------------------------------------------------------------------------------------------------------------------


if(!isset($_SESSION['user']) && !isset($accion))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
	header('Content-type: text/html;charset=ISO-8859-1');
	$user_session = explode('-',$_SESSION['user']);
	$wuse = $user_session[1];
	

	include_once("root/comun.php");
	

	$conex = obtenerConexionBD("matrix");
	//$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'root');
	$wbasedato = 'root';
	$wfecha=date("Y-m-d");   
    $whora = date("H:i:s");


//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	//-----------------------------------------------------------------------------------------
	//  	! ! !  N O T A  ! ! !
	//-----------------------------------------------------------------------------------------
	// --> Si estamos en el servidor de test, cambio el conex, y algunos parametros, para que 
	//	   La informacion se mueva pero en el servidor de produccion 
	//--------------------------------------------------------------------------------------------------
	function validar_conex_test(&$cod_est_produccion)
	{
		global $conex;
		if ($_SERVER['SERVER_ADDR'] == consultarAliasPorAplicacion($conex, '01', 'IpServidorTest'))
		{
			$ip_produccion = consultarAliasPorAplicacion($conex, '01', 'IpServidorProd');
			// --> Este es el nuevo conex al servidor de produccion
			$conex = @mysql_connect($ip_produccion,'root','q6@nt6m');	
			if(!$conex)
			{
				echo "No se realizo Conexion con mysql en Producción<br>se registrara el movimiento en mysql Test.<br>";
				$conex = mysql_connect('localhost','root','q6@nt6m');	
			}
			else
			{
				// --> Consultar el codigo correspondiente para el estado test.
				$cod_est_produccion = consultarAliasPorAplicacion($conex, '01', 'CodEstadoScriptTest');
			}
			

		}
	}
	//--------------------------------------------------------------------------------------------------
	//	Nombre: 		Validar clave
	//	Descripcion:	Función que valida si la clave de publicacion es la correcta
	//	Entradas:		clave encriptada
	//	Salidas:		return, false
	//--------------------------------------------------------------------------------------------------
	function validar_clave($clave, $conex)
	{
		global $wbasedato;
		global $wuse;
		
		$q_clave = "SELECT count(*) as cant
					  FROM ".$wbasedato."_000060 
					 WHERE Usuario = '".$wuse."'
					   AND Clave = '".$clave."'
					";
		$res_clave = mysql_query($q_clave, $conex) or die("Query (validar clave): ".$q_clave."<br>Error: ".mysql_error());
		$row_clave = mysql_fetch_array($res_clave);
		if ($row_clave['cant'] > 0)
		{
			return true;
		}
		else
		{
			echo "<b>Clave incorrecta<b>";
			return false;
		}
	}
	//--------------------------------------------------------------------------------------------------
	//	Nombre: 		Consultar codigo del desarrollador
	//	Descripcion:	Funcion que el desarrollador si exista, y consulta su correspondiente codigo 
	//	Entradas:		Nombre del desarrollador
	//	Salidas:		Codigo del desarrollador
	//--------------------------------------------------------------------------------------------------
	function codigo_desarrollador($desarrollador, &$nomDesa)
	{
		global $wbasedato;
		global $conex;
		$q_cod_desarroll = "SELECT Codigo, Descripcion
							  FROM usuarios
							 WHERE Descripcion = '".$desarrollador."' 
							";
		$res_cod_desarroll = mysql_query($q_cod_desarroll,$conex) or die("Query (Consultar desarrollador): ".$q_cod_desarroll."<br>Error: ".mysql_error());
		
		if ($row_cod_desarroll = mysql_fetch_array($res_cod_desarroll))
		{
			$nomDesa = $row_cod_desarroll['Descripcion'];
			return $row_cod_desarroll['Codigo'];
		}
		else
			return "No existe";
	}
	//--------------------------------------------------------------------------------------------------
	//	Nombre: 		descargar script
	//	Descripcion:	Funcion que realiza la descarga de un script, actualiza el estado del script, y
	//					genera el log de descargas. 
	//	Entradas:
	//	Salidas:
	//--------------------------------------------------------------------------------------------------
	function descargar_script($script, $desarrollador)
	{
		global $wbasedato;
		global $conex;
		global $wfecha;   
		global $whora;
		global $wuse;
		
		// --> Si el codigo del script viene con parentesis, es porque dentro de los parentesis viene el codigo del grupo
		//	   Esto ocurre cuando el script es de tipo algoritmico
		$parent = array('(', ')');
		$arr_script = str_replace($parent, '|', $script);
		$arr_script = explode('|', $arr_script);
		$script     = $arr_script[0];
		$grupo		= $arr_script[1];
		
		// --> Consultar el codigo correspondiente para el estado mantenimiento.
		$cod_est_mantenimiento = consultarAliasPorAplicacion($conex, '01', 'CodEstadoScriptMantenimiento');
		// --> Consultar el codigo correspondiente para el estado De Baja.
		$cod_est_DeBaja = consultarAliasPorAplicacion($conex, '01', 'CodEstadoScriptDebaja');
		
		// --> Consultar ruta del script
			$q_ruta ="SELECT Matgru, Matcar, Matest, Matfud, Mathud, Matuud
						FROM ".$wbasedato."_000087
					   WHERE Matcod = '".$script."'
	".(($grupo != '') ? " AND Matgru = '".$grupo."'" : "")." ";
		$res_ruta = mysql_query($q_ruta,$conex) or die("Query (Consultar ruta): ".$q_ruta."<br>Error: ".mysql_error());
		if($row_ruta = mysql_fetch_array($res_ruta))
		{
			// --> Si el script esta en mantenimiento o de baja.
			if($row_ruta['Matest'] == $cod_est_mantenimiento || $row_ruta['Matest'] == $cod_est_DeBaja)
			{
				// --> Si esta de baja no se puede descargar
				if($row_ruta['Matest'] == $cod_est_DeBaja)
				{
					echo "<b>El script se encuentra de baja.</b>";
					$descargar = 'NO';
				}
				else
				{
					// --> Si esta en mantenimiento, solo lo puede descargar el usuario que ya lo descargo
					if($row_ruta['Matuud'] == $wuse)
					{
						$descargar = 'SI';
					}
					else
					{
						// --> Si el script esta en mantenimiento (MA), debo mostrar mensaje de alerta de que ha sido
						//	   descargado por otro desarrollador.
						
						// --> Consultar nombre del usuario que realizo la descarga
						$q_cod_usu_des = "SELECT Descripcion
											FROM usuarios
										   WHERE Codigo = '".$row_ruta['Matuud']."' 
											";
						$res_cod_usu_des = mysql_query($q_cod_usu_des,$conex) or die("Query (Consultar desarrollador): ".$q_cod_usu_des."<br>Error: ".mysql_error());
						$row_cod_usu_des = mysql_fetch_array($res_cod_usu_des);
						
						echo '<b>Este script ya se encuentra en mantenimiento</b><br>
							<b>Ultima descarga:</b> '.$row_ruta['Matfud'].' '.$row_ruta['Mathud'].'<br>
							<b>Usuario:</b> '.$row_cod_usu_des['Descripcion'];
						$descargar = 'NO';
					}
				}	
			}
			// --> Si el script no este ni en mantenimiento ni de baja se puede descargar.
			else
			{
				$descargar = 'SI';
			}
			
			if($descargar == 'SI')
			{
				$cod_desarrollador = $wuse;
				
				// --> Armar las rutas de los directorios
				switch ($row_ruta['Matcar'])
				{
					case 'include':
					{
						$ruta_script 		= '../'.$row_ruta['Matcar'].'/'.$row_ruta['Matgru'].'/'.$script;
						break;
					}
					case 'algoritmico':
					{
						$ruta_script 		= '../include/'.$row_ruta['Matgru'].'/'.$script;
						break;
					}
					case 'procesos':
					case 'reportes':
					case 'manuales':
					{
						$ruta_script 		= $row_ruta['Matgru'].'/'.$row_ruta['Matcar'].'/'.$script;
						break;
					}
					case 'images':
					{
						$ruta_script 		= 'images/medical/'.$row_ruta['Matgru'].'/'.$script;
						break;
					}
					case 'general':
					{
						$ruta_script 		= $script;
						break;
					}
				}
				
				if(file_exists($ruta_script))
				{
					// --> Copio el script a la carpeta planos con extención .zip
					$script_temp 	   = explode('.', $script); 
					$ruta_script_zip   =  'planos/'.$script_temp[0].'.zip';
					copy ($ruta_script, $ruta_script_zip);
									
					// --> Respuesta para jquery, imprimo el link para la descarga
					echo '<b>Descarga Exitosa</b>|'.$ruta_script_zip.'|'.$script_temp[0].'.zip';
					// <-- Fin respuesta
					
					// --> Registrar log de descarga
					$q_inser_log = " INSERT INTO ".$wbasedato."_000089
												( 	Medico, 		Fecha_data,   	Hora_data, 	  Dypscr, 			Dypusu, 	  		  				Dypest, 			Seguridad)
										 VALUES ('".$wbasedato."', '".$wfecha."', '".$whora."', '".$script."', '".$cod_desarrollador."', 	'".$cod_est_mantenimiento."', 'C-".$wuse."' )
									";
					mysql_query($q_inser_log,$conex) or die("Query (Insertar log de descarga): ".$q_inser_log."<br>Error: ".mysql_error());
					// <-- Fin Registrar log 
					
					// --> Actualizar el estado actual del script (registrar estado mantenimiento MA); 
					//	   y la fecha, hora y usuario de ultima descarga
					$q_act_estado = "UPDATE ".$wbasedato."_000087
										SET Matest = '".$cod_est_mantenimiento."',
											Matfud = '".$wfecha."',
											Mathud = '".$whora."',
											Matuud = '".$cod_desarrollador."'
									  WHERE Matcod = '".$script."'
									    AND Matgru = '".$row_ruta['Matgru']."'
									";
					mysql_query($q_act_estado,$conex) or die("Query (Actualizar estado actual): ".$q_act_estado."<br>Error: ".mysql_error());
					// --> Fin actualizar el estado actua
				}
				else
				{
					echo "<b>La ruta del script no existe</b>";
				}
			}
		}
		else
		{
			echo "<b>No se ha encontrado la matricula del script</b>";
		}
	}
	//--------------------------------------------------------------------------------------------------
	//	Nombre: 		Publicar script
	//	Descripcion:	Funcion que realiza la publicacion de un script y registra esta accion en el log. 
	//	Entradas:
	//	Salidas:
	//--------------------------------------------------------------------------------------------------
	function publicar_script()
	{
		global $wbasedato;
		global $wfecha;   
		global $whora;
		global $wuse;
		global $conex;
		
		// --> variables recibidas
		$script			=  $_FILES['file_script']['name'];				//Nombre del script
		$tamano 		=  $_FILES['file_script']['size'];				//Tamaño del archivo en Kb
		$error 			=  $_FILES['file_script']['error'];				//Si apareció algún error en la subida
		$script_temp	=  $_FILES['file_script']['tmp_name'];			//Nombre temporal que se le asigna al archivo cuando sube
		$desarrollador	=  $_POST['fdesarrollador'];
		$textDesc		=  $_POST['textDesc'];
		$justificacion	=  $_POST['fjustificacion'];
		$clave			=  $_POST['fclave'];
		if(isset($_POST['fgrupo']))
			$grupo		=  $_POST['fgrupo'];
		else
			$grupo		=  '';
		
		// --> Consultar el codigo correspondiente para el estado produccion.
		$cod_est_produccion = consultarAliasPorAplicacion($conex, '01', 'CodEstadoScriptProduccion');
		
		// --> Validar si estamos trabajando en el servidor de test, para cambiar el conex al servidor de produccion
		//validar_conex_test($cod_est_produccion);
			
		if(validar_clave($clave, $conex))
		{
			// --> Consultar estados a los cuales se les permite publicar
			$q_estados = "SELECT Estcod
							FROM ".$wbasedato."_000088
						   WHERE Estppu = 'on'
						";
			$res_estados = mysql_query($q_estados,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar estados): ".$q_estados." - ".mysql_error());
			$arr_estados = array();
			while($row_estados = mysql_fetch_array($res_estados))
			{
				$arr_estados[] = $row_estados['Estcod'];
			}
			
			// --> Consultar informacion del script
				$q_info = "SELECT Matgru, Matcar, Matest, Matuud, Estnom
							 FROM ".$wbasedato."_000087, ".$wbasedato."_000088
							WHERE Matcod = '".$script."'
		".(($grupo != '') ? " AND Matgru = '".$grupo."'" : "")."
							  AND Matest = Estcod
						";
			$res_info = mysql_query($q_info,$conex) or die("Query (Consultar info): ".$q_info."<br>Error: ".mysql_error());
			if($row_info = mysql_fetch_array($res_info))
			{
				// --> consultar codigo del desarrollador y validar que si exista
				$nomDesa = '';
				$cod_desarrollador = codigo_desarrollador($desarrollador, $nomDesa);
				if ($cod_desarrollador != 'No existe')
				{
					// --> Validar que el estado actual del script sea el permitido para publicar
					if (in_array($row_info['Matest'], $arr_estados))
					{
						// --> Armar las rutas de los directorios
						switch ($row_info['Matcar'])
						{
							case 'include':
							{
								$ruta_grupo 		= '../'.$row_info['Matcar'].'/';
								$ruta_grupo_carpeta = '../'.$row_info['Matcar'].'/'.$row_info['Matgru'].'/';
								$ruta_script 		= '../'.$row_info['Matcar'].'/'.$row_info['Matgru'].'/'.$script;
								$ruta_script_copia 	= '../'.$row_info['Matcar'].'/'.$row_info['Matgru'].'/copia/';
								break;
							}
							case 'algoritmico':
							{
								$ruta_grupo 		= '../include/';
								$ruta_grupo_carpeta = '../include/'.$row_info['Matgru'].'/';
								$ruta_script 		= '../include/'.$row_info['Matgru'].'/'.$script;
								$ruta_script_copia 	= '../include/'.$row_info['Matgru'].'/copia/';
								break;
							}
							case 'procesos':
							case 'reportes':
							case 'manuales':
							{
								$ruta_grupo 		= $row_info['Matgru'].'/';
								$ruta_grupo_carpeta = $row_info['Matgru'].'/'.$row_info['Matcar'].'/';
								$ruta_script 		= $row_info['Matgru'].'/'.$row_info['Matcar'].'/'.$script;
								$ruta_script_copia 	= $row_info['Matgru'].'/'.$row_info['Matcar'].'/copia/';
								break;
							}
							case 'images':
							{
								$ruta_grupo 		= 'images/medical/'.$row_info['Matgru'].'/';
								$ruta_grupo_carpeta = '';
								$ruta_script 		= 'images/medical/'.$row_info['Matgru'].'/'.$script;
								$ruta_script_copia 	= 'images/medical/'.$row_info['Matgru'].'/copia/';
								break;
							}
							case 'general':
							{
								$ruta_grupo 		= '';
								$ruta_grupo_carpeta = '';
								$ruta_script 		= $script;
								$ruta_script_copia 	= 'copia/';
								break;
							}
						}
						// --> Validar la existencia de los diferentes directorios
						// --> Si no existe el grupo, entonces la creo con todos los permisos
						if( $ruta_grupo != '' && !file_exists($ruta_grupo))
						{
							if (!mkdir($ruta_grupo, 0777))
									echo "Nota: la carpeta ".$ruta_grupo." no existe <br>y no se ha podido crear.<br>";
						}
						
						// --> Si no existe la carpeta dentro del grupo, entonces la creo con todos los permisos
						if( $ruta_grupo_carpeta != '' && !file_exists($ruta_grupo_carpeta))
						{
							if (!mkdir($ruta_grupo_carpeta, 0777))
									echo "Nota: la carpeta ".$ruta_grupo_carpeta." no existe <br>y no se ha podido crear.<br>";
						}
							
						// --> Si la carpeta de copia no existe, entonces la creo con todos los permisos
						if($ruta_script_copia != '' && !file_exists($ruta_script_copia))
						{
							if (!mkdir($ruta_script_copia, 0777))
								echo "Nota: la carpeta ".$ruta_script_copia." no existe <br>y no se ha podido crear.<br>";
							
						}
						
						// --> Realizo el Backup de la version anterior
						if (file_exists($ruta_script))
						{
							if(@!copy($ruta_script, $ruta_script_copia.$script))
							{
								echo "la copia de respaldo no pudo hacerse.<br>"; 
							}
						}
						
						// --> Ubico el archivo en su correspondiente carpeta
						if( move_uploaded_file($script_temp, $ruta_script))
						{
							$textDesc = utf8_decode($textDesc);
							// --> Registrar log de la publicacion
							$q_inser_log = " INSERT INTO ".$wbasedato."_000089
														( 	Medico, 		Fecha_data,   	Hora_data, 	  Dypscr, 			Dypusu, 	  		  			Dypest,					Dypjus,				Dypdes, 		  	Seguridad		)
												 VALUES ('".$wbasedato."', '".$wfecha."', '".$whora."', '".$script."', '".$cod_desarrollador."', 	'".$cod_est_produccion."', '".$justificacion."',	'".$textDesc."', 	'C-".$wuse."' 	)
											";
							mysql_query($q_inser_log,$conex) or die("Query (Insertar log de descarga): ".$q_inser_log."<br>Error: ".mysql_error());
							// <-- Fin Registrar log 
							
							// --> Actualizar el estado actual del script (registrar estado produccion PR); 
							//	   y la fecha, hora y usuario de ultima publicacion
							$q_act_estado = "UPDATE ".$wbasedato."_000087
												SET Matest = '".$cod_est_produccion."',
													Matfup = '".$wfecha."',
													Mathup = '".$whora."',
													Matuup = '".$cod_desarrollador."'
											  WHERE Matcod = '".$script."'
						  ".(($grupo != '') ? " AND Matgru = '".$row_info['Matgru']."'" : "")."  
											";
							mysql_query($q_act_estado,$conex) or die("Query (Actualizar estado actual): ".$q_act_estado."<br>Error: ".mysql_error());
							// --> Fin actualizar el estado actual
							
							// --> Mensaje
							echo "<b>Publicación exitosa</b><br><b>Ruta:</b>".$ruta_script."<br><b>Tamaño del archivo:</b> ".number_format(($tamano/1024),1,',','.')." KB";
							echo "|limpiar";		//Bandera para el jquery para limpiar el formulario
							
							// --> Enviar correo informando la publicacion
							$wcorreopmla 				= consultarAliasPorAplicacion( $conex, "01", "emailpmla");
							$wcorreopmla 				= explode("--", $wcorreopmla );
							$wpassword   				= $wcorreopmla[1];
							$wremitente  				= $wcorreopmla[0];
							$datos_remitente 			= array();
							$datos_remitente['email']	= $wremitente;
							$datos_remitente['password']= $wpassword;
							$datos_remitente['from'] 	= $wremitente;
							$datos_remitente['fromName']= "Desarrollo de sofware, PMLA";

							$wdestinatarios	= consultarAliasPorAplicacion( $conex, "01", "emailParaInformarPublicaciones");
							$wdestinatarios = explode(",",$wdestinatarios);
							$wasunto 		= "Nueva publicación de script: ".$script;
							$mensaje		= "
							<html>
							<body>
							<table>
								<tr>
									<td align='center' style='background-color:#2a5db0;color:#ffffff;font-size:10pt;padding:1px;font-family:verdana;' colspan='2'>Se ha realizado una nueva publicación</td>
								</tr>
								<tr>
									<td style='background-color:#C3D9FF;color:#000000;font-size:9pt;padding:1px;font-family:verdana;'>Script</td>
									<td style='background-color:#E8EEF7;color:#000000;font-size:9pt;padding:1px;font-family:verdana;'>".$ruta_script."</td></tr>
								<tr>
									<td style='background-color:#C3D9FF;color:#000000;font-size:9pt;padding:1px;font-family:verdana;'>Responsable</td>
									<td style='background-color:#E8EEF7;color:#000000;font-size:9pt;padding:1px;font-family:verdana;''>".$nomDesa."</td></tr>
								<tr>
									<td style='background-color:#C3D9FF;color:#000000;font-size:9pt;padding:1px;font-family:verdana;'>Fecha/Hora</td>
									<td style='background-color:#E8EEF7;color:#000000;font-size:9pt;padding:1px;font-family:verdana;'>".date("Y-m-d H:i:s")."</td>
								</tr>
								<tr>
									<td style='background-color:#C3D9FF;color:#000000;font-size:9pt;padding:1px;font-family:verdana;'>Detalle</td>
									<td style='background-color:#E8EEF7;color:#000000;font-size:9pt;padding:1px;font-family:verdana;'>".$textDesc."</td>
								</tr>
							</table>
							</body>
							</html>							
							";
							
							$altbody 		= "";
							sendToEmail($wasunto,$mensaje,$altbody, $datos_remitente, $wdestinatarios );
						}
						else
						{ 
							echo "Error al subir el archivo. Inténtelo nuevamente."; 
						}
					}
					else
					{
						echo "El estado actual del script (<b>".$row_info['Estnom']."</b>),<br> 
							no permite que se realice la publicación.";
					}
				}
				else
				{
					echo "Desarrollador no válido";				
				}
			}
			else
			{
				echo "<b>No se ha encontrado la matricula del script</b>";
			}
		}
	}
	//--------------------------------------------------------------------------------------------------
	//	Nombre: 		devolver script
	//	Descripcion:	Funcion que devuelve un script (de la carpeta copia a su carpeta original) y
	//					registra esta accion en el log. 
	//	Entradas:
	//	Salidas:
	//--------------------------------------------------------------------------------------------------
	function devolver_script($script, $desarrollador, $clave)
	{
		global $wbasedato;
		global $conex;
		global $wfecha;   
		global $whora;
		global $wuse;
		
		// --> Si el codigo del script viene con parentesis, es porque dentro de los parentesis viene el codigo del grupo
		//	   Esto ocurre cuando el script es de tipo algoritmico
		$parent = array('(', ')');
		$arr_script = str_replace($parent, '|', $script);
		$arr_script = explode('|', $arr_script);
		$script     = $arr_script[0];
		$grupo		= $arr_script[1];
		
		// --> Consultar el codigo correspondiente para el estado devolucion.
		$cod_est_devolucion = consultarAliasPorAplicacion($conex, '01', 'CodEstadoScriptDevuelto');
		
		// --> Consultar ruta del script
			$q_ruta = "SELECT Matgru, Matcar, Matest, Matfud, Mathud, Matuud
						 FROM ".$wbasedato."_000087
						WHERE Matcod = '".$script."'
	".(($grupo != '') ? " AND Matgru = '".$grupo."'" : "")." ";
	
		$res_ruta = mysql_query($q_ruta,$conex) or die("Query (Consultar ruta): ".$q_ruta."<br>Error: ".mysql_error());
		if($row_ruta = mysql_fetch_array($res_ruta))
		{
			// --> consultar codigo del desarrollador y validar que si exista
			$nomDesa = '';
			$cod_desarrollador = codigo_desarrollador($desarrollador, $nomDesa);
			if ($cod_desarrollador != 'No existe')
			{
				// --> Armar las rutas de los directorios
				switch ($row_ruta['Matcar'])
				{
					case 'include':
					{
						$ruta_script 		= '../'.$row_ruta['Matcar'].'/'.$row_ruta['Matgru'].'/'.$script;
						$ruta_script_copia 	= '../'.$row_ruta['Matcar'].'/'.$row_ruta['Matgru'].'/copia/'.$script;
						break;
					}
					case 'algoritmico':
					{
						$ruta_script 		= '../include/'.$row_ruta['Matgru'].'/'.$script;
						$ruta_script_copia 	= '../include/'.$row_ruta['Matgru'].'/copia/'.$script;
						break;
					}
					case 'procesos':
					case 'reportes':
					case 'manuales':
					{
						$ruta_script 		= $row_ruta['Matgru'].'/'.$row_ruta['Matcar'].'/'.$script;
						$ruta_script_copia 	= $row_ruta['Matgru'].'/'.$row_ruta['Matcar'].'/copia/'.$script;
						break;
					}
					case 'images':
					{
						$ruta_script 		= 'images/medical/'.$row_ruta['Matgru'].'/'.$script;
						$ruta_script_copia 	= 'images/medical/'.$row_ruta['Matgru'].'/copia/'.$script;
						break;
					}
					case 'general':
					{
						$ruta_script 		= $script;
						$ruta_script_copia 	= 'copia/'.$script;
						break;
					}
				}
				
				if(file_exists($ruta_script))
				{
					if(file_exists($ruta_script_copia))
					{
						// --> Copiar el script de la carpeta copia a la original
						copy ($ruta_script_copia, $ruta_script);
						
						// --> Registrar log de la devolucion
						$q_inser_log = " INSERT INTO ".$wbasedato."_000089
													( 	Medico, 		Fecha_data,   	Hora_data, 	  Dypscr, 			Dypusu, 	  		  			Dypest, 				Seguridad)
											 VALUES ('".$wbasedato."', '".$wfecha."', '".$whora."', '".$script."', '".$cod_desarrollador."', 	'".$cod_est_devolucion."', 	'C-".$wuse."' )
										";
						mysql_query($q_inser_log,$conex) or die("Query (Insertar log de descarga): ".$q_inser_log."<br>Error: ".mysql_error());
						// <-- Fin Registrar log 
						
						// --> Actualizar el estado actual del script (Registrar estado devuelto DE)
						$q_act_estado = "UPDATE ".$wbasedato."_000087
											SET Matest = '".$cod_est_devolucion."'
										  WHERE Matcod = '".$script."'
										    AND Matgru = '".$row_ruta['Matgru']."'
										";
						mysql_query($q_act_estado,$conex) or die("Query (Actualizar estado actual): ".$q_act_estado."<br>Error: ".mysql_error());
						// --> Fin actualizar el estado actua
						
						// --> Enviar correo informando la devolucion
						$wcorreopmla 				= consultarAliasPorAplicacion( $conex, "01", "emailpmla");
						$wcorreopmla 				= explode("--", $wcorreopmla );
						$wpassword   				= $wcorreopmla[1];
						$wremitente  				= $wcorreopmla[0];
						$datos_remitente 			= array();
						$datos_remitente['email']	= $wremitente;
						$datos_remitente['password']= $wpassword;
						$datos_remitente['from'] 	= $wremitente;
						$datos_remitente['fromName']= "Desarrollo de sofware, PMLA";

						$wdestinatarios	= consultarAliasPorAplicacion( $conex, "01", "emailParaInformarPublicaciones");
						$wdestinatarios = explode(",",$wdestinatarios);
						$wasunto 		= "Nueva devolución de script";
						$mensaje		= "
						<html>
						<body>
						<table>
							<tr>
								<td align='center' style='background-color:#2a5db0;color:#ffffff;font-size:10pt;padding:1px;font-family:verdana;' colspan='2'>Se ha realizado una nueva devolución</td>
							</tr>
							<tr>
								<td style='background-color:#C3D9FF;color:#000000;font-size:9pt;padding:1px;font-family:verdana;'>Script</td>
								<td style='background-color:#E8EEF7;color:#000000;font-size:9pt;padding:1px;font-family:verdana;'>".$ruta_script."</td></tr>
							<tr>
								<td style='background-color:#C3D9FF;color:#000000;font-size:9pt;padding:1px;font-family:verdana;'>Responsable</td>
								<td style='background-color:#E8EEF7;color:#000000;font-size:9pt;padding:1px;font-family:verdana;''>".$nomDesa."</td></tr>
							<tr>
								<td style='background-color:#C3D9FF;color:#000000;font-size:9pt;padding:1px;font-family:verdana;'>Fecha/Hora</td>
								<td style='background-color:#E8EEF7;color:#000000;font-size:9pt;padding:1px;font-family:verdana;'>".date("Y-m-d H:i:s")."</td>
							</tr>
						</table>
						</body>
						</html>							
						";
						
						$altbody 		= "";
						sendToEmail($wasunto,$mensaje,$altbody, $datos_remitente, $wdestinatarios );
						
						// --> Mensaje
						echo "<b>Script devuelto con exito</b>";
					}
					else
					{
						echo "<b>La ruta de copia del script no existe</b>";
					}
				}
				else
				{
					echo "<b>La ruta del script no existe</b>";
				}
			}
			else
			{
				echo "Desarrollador no válido";				
			}
		}
		else
		{
			echo "<b>No se ha encontrado la matricula del script</b>";
		}
	}
	
	//-------------------------------------------------------------------------------------------------
	//	Nombre: 		Pintar formulario descargas o devoluciones
	//	Descripcion:	Funcion que pinta el formulario para las descargas o las devoluciones
	//	Entradas:
	//	Salidas:
	//-------------------------------------------------------------------------------------------------
	function pintar_formulario_des_dev($tipo_accion)
	{
		echo"
		<div align='center' class='bordeAzul' style='margin-top:10px;'>
			<br>
			<table width='85%' align='center'>";
			
		//	-->	Selección de script
		echo"<tr class='fila2'>
				<td class='encabezadoTabla' style='width :30%'>Nombre del Script:&nbsp;</td>	
				<td align='center' style='padding:3px;'>
					<input type='text' size=60 name='fscript' id='fscript' onmouseover='validar(this);' onblur='validar(this)'>
					<div align='right' id='div_fscript' style='display:none;'></div>
				</td>
			</tr>";
		
		if($tipo_accion == 'pintar_devolver')
		{
			//	-->	Desarrollador
			echo"<tr class='fila2'>
					<td class='encabezadoTabla' style='width :30%'>Desarrollador:&nbsp;</td>	
					<td align='center' style='padding:3px;'>
						<input type='text' size=60 name='fdesarrollador' id='fdesarrollador' onmouseover='validar(this);' onblur='validar(this)'>
						<div align='right' id='div_fdesarrollador' style='display:none;'></div>
					</td>
				</tr>";
			
			//	-->	Clave de publicación
			echo"<tr class='fila2'>
					<td class='encabezadoTabla' style='width :30%'>Clave de publicación:&nbsp;</td>	
					<td align='center' style='padding:3px;'>
						<input type='password' size=15 name='fclave' id='fclave' onmouseover='validar(this);' onblur='validar(this)'>
						<div align='right' id='div_fclave' style='display:none;'></div>
					</td>
				</tr>";
		}
		// --> Boton OK
		echo"<tr>
				<td align='center' colspan=2 style='font-weight: bold;font-family: verdana;	font-size: 10pt;'><br>
					<button>OK</button> 
					<div align='center' id='div_fguardar' style='display:none;' ></div>
					<br>
				</td>
			</tr>";
		//--> Hidden de la accion
		echo "<input type='hidden' id='tipo_accion' value='".$tipo_accion."' >";
		echo"
			</table>	
		</div>";
	}
	//-------------------------------------------------------------------------------------------------
	//	Nombre: 		Pintar formulario para publicacion
	//	Descripcion:	Funcion que pinta el formulario para la publicación
	//	Entradas:
	//	Salidas:
	//-------------------------------------------------------------------------------------------------
	function pintar_formulario_publicar()
	{
		echo"
		<div align='center' class='bordeAzul' style='margin-top:10px;'>
			<br>
			<form id='form_publicar'  method='post' enctype='multipart/form-data'>
			
			<table width='85%' align='center'>";
			
		//	-->	Selección de script
		echo"<tr class='fila2'>
				<td class='encabezadoTabla' style='width :30%'>Nombre del Script:&nbsp;</td>	
				<td align='center' style='padding:3px;'>
					<input type='file' size=60 name='file_script' id='file_script' onmouseover='validar(this);' onblur='validar(this);' onchange='obtener_ultimo_usu_des();'>
					<div align='right' id='div_file_script' style='display:none;'></div>
				</td>
			</tr>";
			
		//	-->	Grupo (si se requiere)
		echo"<tr class='fila2' style='display:none;' id='tr_grupo' style='display:none'>
				
			</tr>";
		
		//	-->	Desarrollador
		echo"<tr class='fila2'>
				<td class='encabezadoTabla' style='width :30%'>Desarrollador:&nbsp;</td>	
				<td align='center' style='padding:3px;'>
					<input type='hidden' id='ultimo_usu_descargo' value=''>
					<input type='text' size=60 name='fdesarrollador' id='fdesarrollador' onmouseover='validar(this);' onblur='validar(this);usu_desarroll_vs_usu_descargo();'>
					<div align='right' id='div_fdesarrollador' style='display:none;'></div>
				</td>
			</tr>";
			
		//	-->	Justificacion
		echo"<tr class='fila2' style='display:none;' id='tr_justificacion'>
				<td class='encabezadoTabla' style='width :30%'>Justificación:&nbsp;
				</td>	
				<td align='center' style='padding:3px;'>
					<textarea id='fjustificacion' name='fjustificacion' rows='3' cols='50' onmouseover='validar(this);' onblur='validar(this);'></textarea>
					<div align='right' id='div_fjustificacion' style='display:none;'></div>
					<div id='mensaje_justificacion'>
					</div>
				</td>
			</tr>";
		
		//	-->	Descripcion
		echo"<tr class='fila2'>
				<td class='encabezadoTabla' style='width :30%'>Descripción:&nbsp;
				</td>	
				<td align='center' style='padding:3px;'>
					<textarea id='textDesc' name='textDesc' rows='3' cols='50' onmouseover='validar(this);' onblur='validar(this);'></textarea>
					<div align='right' id='div_textDesc' style='display:none;'></div>
					<div id='mensaje_textDesc'>
					</div>
				</td>
			</tr>";
			
		//	-->	Clave de publicación
		echo"<tr class='fila2'>
				<td class='encabezadoTabla' style='width :30%'>Clave de publicación:&nbsp;</td>	
				<td align='center' style='padding:3px;'>
					<input type='password' size=8 name='fclave' id='fclave' onmouseover='validar(this);' onblur='validar(this)'>
					<div align='right' id='div_fclave' style='display:none;'></div>
				</td>
			</tr>";
			
		// --> Boton OK
		echo"<tr>
				<td align='center' colspan=2 style='font-weight: bold;font-family: verdana;	font-size: 10pt;'><br>
					<div id='td_boton'>
					</div>
					<div id='msj_calculando' style='display:none'>
						Espere un momento 
						<img style='cursor:pointer;' width='23' height='23' title='Subiendo archivo...' src='images/medical/ajax-loader11.gif'>
					</div>
				</td>
			</tr>";
		//--> Hidden de la accion
		echo "<input type='hidden' id='tipo_accion' value='pintar_publicar' >";
		echo"
			</table>		
			</form>
		</div>";
	}
	
	//-------------------------------------------------------------------------------------------------
	//	Nombre: 		Crar hiddens
	//	Descripcion:	Funcion que consulta el maestro de scripts, para luego crear un json 
	//					con los resultados de la consulta y posteriormente se crea una variable hidden
	//					con dicho json.
	//	Entradas:
	//	Salidas:
	//-------------------------------------------------------------------------------------------------
	function crear_hiddens()
	{
		global $wbasedato;
		global $conex;
		
		// --> Consultar el codigo correspondiente para el estado de baja.
		$cod_est_debaja = consultarAliasPorAplicacion($conex, '01', 'CodEstadoScriptDebaja');
		
		$q_scripts = "SELECT Matcod, Matgru, Carpdu
					    FROM ".$wbasedato."_000087 AS A INNER JOIN ".$wbasedato."_000090 AS B ON A.Matcar = B.Carcod
					   WHERE Matest != '".$cod_est_debaja."'
					   ORDER BY Matcod
					";
		$res_scripts = mysql_query($q_scripts,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar scripts): ".$q_scripts." - ".mysql_error());
		
		$arr_scripts = array();
		while($row_scripts = mysql_fetch_array($res_scripts))
		{
			// --> Si es un script de tipo algoritmico, coloco el nombre del script seguido del grupo, ya que los 
			//	   script que son algoritmicos se puden publicar duplicados, es decir puede que en la matricula este
			// 	   el mismo nombre del script, pero se diferencian por el grupo.
			if($row_scripts['Carpdu'] == 'on')
				$arr_scripts[] = $row_scripts['Matcod'].'('.$row_scripts['Matgru'].')';
			else
				$arr_scripts[] = $row_scripts['Matcod'];
		}
		echo "<input type='hidden' id='hidden_scripts' name='hidden_scripts' value='".json_encode($arr_scripts)."'>";
	
		
		//Crar hidden de los desarrolladores
		$q_desarro = "SELECT Perusu, Descripcion 
						FROM ".$wbasedato."_000042, usuarios
					   WHERE perest	=	'on' 
						 AND Percco = 	'(01)1710'
						 AND Pertip IN('03', '02')  
						 AND Codigo = 	Perusu      
					";
		$res_desarro = mysql_query($q_desarro,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar desarrolladores): ".$q_desarro." - ".mysql_error());
		
		$arr_desarro = array();
		while($row_desarro = mysql_fetch_array($res_desarro))
		{
			$arr_desarro[] = $row_desarro['Descripcion'];
		}
		echo "<input type='hidden' id='hidden_desarrolladores' name='hidden_desarrolladores' value='".json_encode($arr_desarro)."'>";
	}

//=======================================================================================================================================================	
//	F I N  F U N C I O N E S  P H P
//=======================================================================================================================================================	

//=======================================================================================================================================================
//	F I L T R O S  D E  L L A M A D O S  P O R  J Q U E R Y  O  A J A X
//=======================================================================================================================================================
if(isset($accion)) 
{
	switch($accion)
	{
		case 'pintar_formulario':
		{
			if($tipo_accion=='pintar_publicar')
				pintar_formulario_publicar();
			else
				pintar_formulario_des_dev($tipo_accion);
			
			break;
			return;
		}
		case 'descargar_script':
		{	
			descargar_script($fscript, $fdesarrollador);
			break;
			return;
		}
		case 'devolver_script':
		{	
			devolver_script($fscript, $fdesarrollador, $fclave);
			break;
			return;
		}
		case 'obtener_ultimo_descargador':
		{
			$row_ruta = explode('\\', $fscript);
			$nom_script = end($row_ruta);
			// --> Consulto el nombre del ultimo usuario que descargo 
			$q_usu_nom = " SELECT Descripcion
							 FROM ".$wbasedato."_000087, usuarios
							WHERE Matcod = '".$nom_script."'
							  AND Matuud != ''
							  AND Matuud = Codigo
						";
			$res_usu_nom = mysql_query($q_usu_nom,$conex) or die("Query (Consultar nombre): ".$q_usu_nom."<br>Error: ".mysql_error());
			if ($row_usu_nom = mysql_fetch_array($res_usu_nom))
				echo $row_usu_nom['Descripcion'];
			else
				echo '';
			
			// --> Consultar si el script es algoritmico, si es asi debo pintar un seleccionador con los posibles grupos donde
			//	   esta hubicado el archivo. 
			$q_algorit = " SELECT Matgru
							 FROM ".$wbasedato."_000087 AS A INNER JOIN ".$wbasedato."_000090 AS B ON A.Matcar = B.Carcod
							WHERE Matcod = '".$nom_script."'
							  AND Carpdu = 'on'
						 ORDER BY Matgru
						";
			$res_algorit = mysql_query($q_algorit,$conex) or die("Query (Consultar si es algoritmico): ".$q_algorit."<br>Error: ".mysql_error());
			$primera_entrada = 'si';
			while($row_algorit = mysql_fetch_array($res_algorit))
			{
				if($primera_entrada == 'si')
				{
					echo '|'.$row_algorit['Matgru'];
					$primera_entrada = 'no';
				}
				else
					echo ','.$row_algorit['Matgru'];
				
			}
			if(mysql_num_rows($res_algorit) == 0)
				echo '|';
				
			break;
			return;
		}
		case 'publicar_script':
		{
			if ($campos_completos == 'si')
				publicar_script();
			else
				echo "<b>Campos incompletos</b>";
			break;
			return;
		}
	}
}
//=======================================================================================================================================================
//	F I N   F I L T R O S   A J A X 
//=======================================================================================================================================================	


//=======================================================================================================================================================
//	I N I C I O   E J E C U C I O N   N O R M A L   D E L   P R O G R A M A
//=======================================================================================================================================================
else 	
{
	echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='hidden' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
	echo "<input type='hidden' id='wuse' name='wuse' value='".$wuse."'>";
	?>
	<html>
	<head>
	  <title>Publicación Scripts</title>
	</head>
	<link type="text/css" href="../include/root/jquery_1_7_2/css/demo_page.css" rel="stylesheet"/>
	<link type="text/css" href="../include/root/jquery_1_7_2/css/demo_table.css" rel="stylesheet"/>
	<link type="text/css" href="../include/root/jquery_1_7_2/css/demo_validation.css" rel="stylesheet"/>
	<link type="text/css" href="../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
	<link type="text/css" href="../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet"/>

	<script src="../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../include/root/jquery_1_7_2/js/jquery.jeditable.js" type="text/javascript"></script>
	<script src="../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
	<script src="../include/root/jquery.blockUI.min.js" type="text/javascript" ></script>
	<script src="../include/root/jquery_1_7_2/js/jquery.validate.js" type="text/javascript"></script>
	<script src="../include/root/jquery_1_7_2/js/jquery.jeditable.checkbox.js" type="text/javascript"></script>
	<script src="../include/root/jquery.form.js" type="text/javascript"></script>


	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
	
	$(document).ready(function() {
		cargar_buttons();
		cargar_autocomplete();	
		$( "#radio" ).buttonset();
		if (browser=="Microsoft Internet Explorer" || browser=="Netscape")
		{
			setInterval( "parpadeo()",600 );
		}
		pintar_formulario('pintar_publicar');
	});
	//---------------------------------------------------------------------------------------------
	//	Nombre:			Usuario desarrollador vs Usuario ultima descarga
	//	Descripcion:	Comparar que el usuario que hay en el campo desarrollador si corresponda
	// 					Con el ultimo usuario que descargo el script, si no es asi se requiere 
	//					justificacion.
	//	Entradas:		
	//	Salidas:		
	//----------------------------------------------------------------------------------------------
	function usu_desarroll_vs_usu_descargo()
	{
		if ($('#fdesarrollador').val() != '' && $('#ultimo_usu_descargo').val() != '') 
		{
			if ($('#fdesarrollador').val() != $('#ultimo_usu_descargo').val())
			{
				$('#mensaje_justificacion').html('<b>NOTA: </b>El ultimo desarrollador que descargo el script (<b>'+$('#ultimo_usu_descargo').val()+'</b>), es diferente al desarrollador responsable de esta publicación. Justifique dicho cambio.');
				// --> Pintar el campo de justificacion
				$('#tr_justificacion').show();
			}
			else
				$('#tr_justificacion').hide();
		}
		else
		{
			$('#tr_justificacion').hide();
		}
	}
	//---------------------------------------------------------------------------------------------
	//	Nombre:			Obtener ultimo usuario que descargo el script
	//	Descripcion:	Hace el llamado a la funcion php que consulta el ultimo usuario que descargo 
	//					el script, y luego lo inserta en el campo de desarrollador del formulario
	//	Entradas:		
	//	Salidas:		
	//----------------------------------------------------------------------------------------------
	function obtener_ultimo_usu_des()
	{
		if($('#file_script').val() != '')
		{
			$.post("Publicar_scripts.php",
			{
				consultaAjax:   		'',
				wemp_pmla:      		$('#wemp_pmla').val(),
				wuse:           		$('#wuse').val(),
				wbasedato:				$('#wbasedato').val(),
				fscript:				$('#file_script').val(),
				accion:					'obtener_ultimo_descargador'
			}
			,function(respuesta) {
				var respuestas = respuesta.split('|');
				
				$('#fdesarrollador').val(respuestas[0]);
				$('#ultimo_usu_descargo').val(respuestas[0]);
				
				// --> Indica que el script que se quiere publicar es algoritmico lo que implica que se debe 
				//     pintar seleccionador del grupo
				if(respuestas[1] != '')
				{
					var html_grupo = "<td class='encabezadoTabla' style='width :30%'>Grupo:&nbsp;</td>";	
						html_grupo+= "<td align='center' style='padding:3px;'>";
						html_grupo+= "<select id='fgrupo' name='fgrupo' style='width:300px;font-family: verdana; font-size: 15px;'>";
										
					var arr_grupos = respuestas[1].split(',');
					$.each( arr_grupos, function( key, value ){
					
						html_grupo+= "<option>"+value+"</option>";				
					});
					html_grupo+= "</select>"; 	
						
					$('#tr_grupo').html(html_grupo+'</td>');
					$('#tr_grupo').show();
				}
				else
				{
					$('#tr_grupo').html('');
					$('#tr_grupo').hide();
				}
			});
		}
	}
	//---------------------------------------------------------------------------------------------
	//	Nombre:			Validar guardar
	//	Descripcion:	Valida que un elemento no este vacio.
	//	Entradas:		1- el objeto, 2- variable bandera 
	//	Salidas:		variable bandera, indicando si es null o no
	//----------------------------------------------------------------------------------------------
	function validar_guardar(elemento, guardar)
	{
		if 	(document.getElementById(elemento).value =='')
		{
			$("#"+elemento).css("border","1px dotted red");
			$("#div_"+elemento)
				.text(' * Campo Obligatorio')
				.css({"color":"red", "opacity":" 0.4","fontSize":"12px"})
				.show();
			return guardar = 'no';
		}
			return guardar;
	}
	//---------------------------------------------------------------------------------
	//	Nombre:			parpadeo
	//	Descripcion:	Realiza el blink de un elemento
	//	Entradas:		
	//	Salidas:
	//----------------------------------------------------------------------------------
	function parpadeo()
	{
		try {
			var blink = document.all.tags("BLINK");
			
			for (var i=0; i < blink.length; i++){
				blink[i].style.visibility = blink[i].style.visibility == "" ? "hidden" : "";
			}
		}
		catch(e){
		}
	}
	//---------------------------------------------------------------------------------
	//	Nombre:			Encriptar
	//	Descripcion:	Funciones para realizar la encriptación de la clave
	//	Entradas:		
	//	Salidas:
	//----------------------------------------------------------------------------------
	var hexcase=0;var b64pad="";
	function hex_sha1(a){return rstr2hex(rstr_sha1(str2rstr_utf8(a)))}
	function hex_hmac_sha1(a,b){return rstr2hex(rstr_hmac_sha1(str2rstr_utf8(a),str2rstr_utf8(b)))}
	function sha1_vm_test(){return hex_sha1("abc").toLowerCase()=="a9993e364706816aba3e25717850c26c9cd0d89d"}
	function rstr_sha1(a){return binb2rstr(binb_sha1(rstr2binb(a),a.length*8))}
	function rstr_hmac_sha1(c,f){var e=rstr2binb(c);if(e.length>16){e=binb_sha1(e,c.length*8)}var a=Array(16),d=Array(16);for(var b=0;b<16;b++){a[b]=e[b]^909522486;d[b]=e[b]^1549556828}var g=binb_sha1(a.concat(rstr2binb(f)),512+f.length*8);return binb2rstr(binb_sha1(d.concat(g),512+160))}
	function rstr2hex(c){try{hexcase}catch(g){hexcase=0}var f=hexcase?"0123456789ABCDEF":"0123456789abcdef";var b="";var a;for(var d=0;d<c.length;d++){a=c.charCodeAt(d);b+=f.charAt((a>>>4)&15)+f.charAt(a&15)}return b}
	function str2rstr_utf8(c){var b="";var d=-1;var a,e;while(++d<c.length){a=c.charCodeAt(d);e=d+1<c.length?c.charCodeAt(d+1):0;if(55296<=a&&a<=56319&&56320<=e&&e<=57343){a=65536+((a&1023)<<10)+(e&1023);d++}if(a<=127){b+=String.fromCharCode(a)}else{if(a<=2047){b+=String.fromCharCode(192|((a>>>6)&31),128|(a&63))}else{if(a<=65535){b+=String.fromCharCode(224|((a>>>12)&15),128|((a>>>6)&63),128|(a&63))}else{if(a<=2097151){b+=String.fromCharCode(240|((a>>>18)&7),128|((a>>>12)&63),128|((a>>>6)&63),128|(a&63))}}}}}return b}
	function rstr2binb(b){var a=Array(b.length>>2);for(var c=0;c<a.length;c++){a[c]=0}for(var c=0;c<b.length*8;c+=8){a[c>>5]|=(b.charCodeAt(c/8)&255)<<(24-c%32)}return a}
	function binb2rstr(b){var a="";for(var c=0;c<b.length*32;c+=8){a+=String.fromCharCode((b[c>>5]>>>(24-c%32))&255)}return a}function binb_sha1(v,o){v[o>>5]|=128<<(24-o%32);v[((o+64>>9)<<4)+15]=o;var y=Array(80);var u=1732584193;var s=-271733879;var r=-1732584194;var q=271733878;var p=-1009589776;for(var l=0;l<v.length;l+=16){var n=u;var m=s;var k=r;var h=q;var f=p;for(var g=0;g<80;g++){if(g<16){y[g]=v[l+g]}else{y[g]=bit_rol(y[g-3]^y[g-8]^y[g-14]^y[g-16],1)}var z=safe_add(safe_add(bit_rol(u,5),sha1_ft(g,s,r,q)),safe_add(safe_add(p,y[g]),sha1_kt(g)));p=q;q=r;r=bit_rol(s,30);s=u;u=z}u=safe_add(u,n);s=safe_add(s,m);r=safe_add(r,k);q=safe_add(q,h);p=safe_add(p,f)}return Array(u,s,r,q,p)}
	function sha1_ft(e,a,g,f){if(e<20){return(a&g)|((~a)&f)}if(e<40){return a^g^f}if(e<60){return(a&g)|(a&f)|(g&f)}return a^g^f}function sha1_kt(a){return(a<20)?1518500249:(a<40)?1859775393:(a<60)?-1894007588:-899497514}
	function safe_add(a,d){var c=(a&65535)+(d&65535);var b=(a>>16)+(d>>16)+(c>>16);return(b<<16)|(c&65535)}
	function bit_rol(a,b){return(a<<b)|(a>>>(32-b))};
	
	function encriptar(clave)
	{
		var clave_encriptada = hex_sha1(clave);
		return clave_encriptada;
	}
	//---------------------------------------------------------------------------------
	//	Nombre:			Descargar scripts
	//	Descripcion:	Realiza el llamado a la funcion php que realiza la descarga
	//	Entradas:		
	//	Salidas:
	//----------------------------------------------------------------------------------
	function descargar_script()
	{
		var guardar='si';										//variable semaforo que me inidcara si se puede guardar o no
		guardar = validar_guardar('fscript', guardar);
		
		if(guardar == 'si')
		{
			$.post("Publicar_scripts.php",
			{
				consultaAjax:   		'',
				wemp_pmla:      		$('#wemp_pmla').val(),
				wuse:           		$('#wuse').val(),
				wbasedato:				$('#wbasedato').val(),
				fscript:				$('#fscript').val(),
				accion:					'descargar_script'
			}
			,function(respuesta) {
					var array_respuesta = respuesta.split("|");
					
					if(array_respuesta[0] == '<b>Descarga Exitosa</b>')
					{	
						$('#fscript').val('');
						mostrar_mensaje(array_respuesta[0]+'<br><a href="'+array_respuesta[1]+'" id="link_dd" name="link_dd" value="111">'+array_respuesta[2]+'</a>');
					}
					else
					{
						mostrar_mensaje(array_respuesta[0]);
					}
				});
		}
		else
		{
			mostrar_mensaje('<b>Campos incompletos</b>');
		}
	}
	//---------------------------------------------------------------------------------
	//	Nombre:			Descargar scripts
	//	Descripcion:	Realiza el llamado a la funcion php que hace la devolucion de
	//					un script, es decir coloca el de la carpeta copia en su carpeta 
	//					orginal.
	//	Entradas:		
	//	Salidas:
	//----------------------------------------------------------------------------------
	function devolver_script()
	{
		var guardar='si';										//variable semaforo que me inidcara si se puede guardar o no
		guardar = validar_guardar('fscript', guardar);
		guardar = validar_guardar('fdesarrollador', guardar);
		guardar = validar_guardar('fclave', guardar);
		
		if(guardar == 'si')
		{
			$.post("Publicar_scripts.php",
			{
				consultaAjax:   		'',
				wemp_pmla:      		$('#wemp_pmla').val(),
				wuse:           		$('#wuse').val(),
				wbasedato:				$('#wbasedato').val(),
				fscript:				$('#fscript').val(),
				fdesarrollador:			$('#fdesarrollador').val(),
				fclave:					encriptar($('#fclave').val()),
				accion:					'devolver_script'
			}
			,function(respuesta) {
				mostrar_mensaje(respuesta);
				if(respuesta == '<b>Script devuelto con exito</b>')
				{
					$('#fscript').val('');
					$('#fclave').val('');
					$('#fdesarrollador').val('');
				}	
			});
		}
		else
		{
			mostrar_mensaje('<b>Campos incompletos</b>');
		}
	}
	//---------------------------------------------------------------------------------
	//	Nombre:			Publicar scripts
	//	Descripcion:	Realiza el llamado a la funcion php que hace la publicacion de
	//					un script.
	//	Entradas:		
	//	Salidas:
	//----------------------------------------------------------------------------------
	function publicar_script()
	{
		$('#msj_calculando').show(600);
		$('#td_boton').hide();
		
		var guardar='si';										//variable semaforo que me inidcara si se puede publicar o no
		guardar = validar_guardar('file_script', guardar);
		guardar = validar_guardar('fdesarrollador', guardar);
		guardar = validar_guardar('fclave', guardar);
		guardar = validar_guardar('textDesc', guardar);
		
		if ($("#tr_justificacion").css("display") != 'none') 
			guardar = validar_guardar('fjustificacion', guardar);
		else
			$("#fjustificacion").val('');
		
		if(guardar == 'si')
		{
			$('#fclave').val(encriptar($('#fclave').val()));
			
			$('#form_publicar').ajaxForm({
				url:      'Publicar_scripts.php?accion=publicar_script&consultaAjax=&campos_completos=si',
				complete: function(respuesta) {
					respuesta = respuesta.responseText;
					$('#fclave').val('');
					
					var array_respuesta = respuesta.split("|");
					if(array_respuesta[1] == 'limpiar')	
					{
						$('#file_script').val('');
						$('#fdesarrollador').val('');
						$('#textDesc').val('');
						$("#fjustificacion").val('');
						$("#tr_grupo").hide();
					}
					mostrar_mensaje(array_respuesta[0]);					
					$('#msj_calculando').hide();
					$('#td_boton').show();
				}
			}); 
		}
		else
		{
			$('#form_publicar').ajaxForm({
				url:      'Publicar_scripts.php?accion=publicar_script&consultaAjax=&campos_completos=no', 
				complete: function(respuesta) {
				mostrar_mensaje(respuesta.responseText);
				}
			}); 
								
			$('#msj_calculando').hide();
			$('#td_boton').show();
		}
	}
	//-----------------------------------------------------------------------
	//	Nombre:			Validar elemento
	//	Descripcion:	Funcion para validar campos obligatorios
	//	Entradas:		
	//	Salidas:
	//-----------------------------------------------------------------------
	function validar(elemento)
	{	  
		var valor = $("#"+elemento.id).val();
		if(valor == '')															//si el campo esta vacio
		{
			//Pinto el borde del elemento de color rojo 
			$("#"+elemento.id).css({
								"border":"1px dotted red",
								"border-opacity":"0.4"
								});												
			//Muestro mensaje de "* Capo oblgatorio"
			$("#div_"+elemento.id)												
					.text(' * Campo Obligatorio')
					.css({"color":"red", "opacity":" 0.4","fontSize":"12px"})
					.show();
		}
		else
		{														//sí existe valor entonces,
			$("#"+elemento.id).css("border","");				//quito el borde rojo
			$("#div_"+elemento.id).css("display", "none");		//oculto el mensaje
		}
	}
	//---------------------------------------------------------------------------------
	//	Nombre:			mostrar mensaje
	//	Descripcion:	Pinta un mensaje en el div correspondiente para los mensajes
	//	Entradas:		
	//	Salidas:
	//----------------------------------------------------------------------------------
	function mostrar_mensaje(mensaje)
	{
		$("#div_mensajes").html("<BLINK><img width='15' height='15' src='images/medical/root/info.png' /></BLINK>&nbsp;"+mensaje);
		$("#div_mensajes").css({"opacity":" 0.6","fontSize":"11px"});
		$("#div_mensajes").hide();
		$("#div_mensajes").show(500);	
	}
	//---------------------------------------------------------------------------------
	//	Nombre:			Autocomplete scripts
	//	Descripcion:	Activa el plug-in jquery para cargar los autocomplete
	//	Entradas:		
	//	Salidas:
	//----------------------------------------------------------------------------------
	function cargar_autocomplete()
	{
		//Capturo el hidden de los scripts (json) para crear arrays.
		var arr_scripts = eval('(' + $('#hidden_scripts').val() + ')');
		var arr_desarrolladores = eval('(' + $('#hidden_desarrolladores').val() + ')');
		
		//Cargar autocomplete de los grupos y desarrolladores
		$( "#fscript" ).autocomplete({
			source: arr_scripts
		});
		$( "#fdesarrollador" ).autocomplete({
			source: arr_desarrolladores
		});
	}
	
	//-------------------------------------------------------------------------------
	//	Nombre:			Pintar formulario
	//	Descripcion:	Pinta el formulario para publicar o descargar un script
	//	Entradas:		tipo_accion: Accion a realizar (pintar_publicar, pintar_descargar, pintar_devolver)
	//	Salidas:
	//--------------------------------------------------------------------------------
	function pintar_formulario(tipo_accion)
	{
		$.post("Publicar_scripts.php",
		{
			consultaAjax:   		'',
			wemp_pmla:      		$('#wemp_pmla').val(),
			wuse:           		$('#wuse').val(),
			wbasedato:				$('#wbasedato').val(),
			tipo_accion:         	tipo_accion,
			accion:					'pintar_formulario'
		}
		,function(data) {
			$("#formulario").hide();			//Ocultar Div
			$("#formulario").html(data);		//Inserto la respuesta
			$("#formulario").show(); 			//Animacion para que despliegue
			$("#div_mensajes").hide();
			
			if(tipo_accion == 'pintar_publicar')
			{
				if(browser == "Microsoft Internet Explorer" )
					$('#td_boton').html("<input type='submit' style='cursor:pointer;border: 1px solid #888888;color: #888888;width:70px;font-size: 12pt;' value='OK' onClick='publicar_script();'><br>");
				else
					$('#td_boton').html("<button>OK</button><br>");
			}
			
			cargar_buttons();
			cargar_autocomplete();	
			$( "#radio" ).buttonset();
		}
		);
	}
	//-------------------------------------------------------------------------------
	//	Nombre:			Cargar buttons
	//	Descripcion:	Activa el plug-in jquery para la visualizacion de los button
	//	Entradas:		
	//	Salidas:
	//--------------------------------------------------------------------------------
	function cargar_buttons()
	{
		$( "button" ).each(function( index ) {
			
			switch ($(this).text())
			{
				case 'OK':
						switch ($('#tipo_accion').val())
						{
							case 'pintar_publicar':
									$(this)
									.button({icons:	{
													primary: "ui-icon ui-icon-arrowreturnthick-1-n"
													}
											})
									.click(function( event ){
												publicar_script();
									});
									break;
							case 'pintar_descargar':
									$(this)
									.button({icons:	{
													primary: "ui-icon ui-icon-arrowreturnthick-1-s"
													}
											})
									.click(function( event ){
												descargar_script();
											});
									break;
							case 'pintar_devolver':
									$(this)
									.button({icons:	{
													primary: "ui-icon ui-icon-transferthick-e-w"
													}
											})
									.click(function( event ){
												devolver_script();
											});
									break;
							
						}
						break;
				case 'Cerrar':
						$(this)
						.button({icons:	{
										primary: "ui-icon ui-icon-circle-close"
										}
								})
						.click(function( event ){
									console.log('cerrar');
									cerrarVentana();
								});
						break;
			}
		});
	}
//=======================================================================================================================================================	
//	F I N  F U N C I O N E S  J A V A S C R I P T 
//=======================================================================================================================================================	
	</script>
	

	
	
<!--=====================================================================================================================================================================     
	E S T I L O S 
=====================================================================================================================================================================-->
	<style type="text/css">
		#format{
			font-weight: bold; 
			font-family: verdana;
			font-size: 10pt;
		}
		#radio{
			font-weight: bold; 
			font-family: verdana;
			font-size: 14pt;
		}
		.Titulo_azul{
			color:#000066; 
			font-weight: bold; 
			font-family: verdana;
			font-size: 11pt;
		}
		.borderDiv2{
			border: 2px solid #2A5DB0;
			padding: 15px;
		}
		.borderDiv{
			border: 1px solid #2A5DB0;
			padding: 5px;
		}
		.bordeAzul{
			border: 1px solid #2A5DB0;
		}
		.Titulo_azul{
			color:#000066; 
			font-weight: bold; 
			font-family: verdana;
			font-size: 11pt;
		}
	</style>
<!--=====================================================================================================================================================================     
	F I N   E S T I L O S 
=====================================================================================================================================================================-->



<!--=====================================================================================================================================================================     
	I N I C I O   B O D Y
=====================================================================================================================================================================-->	
	<body>
	<?php
	//Crear hidden para la lectura por jquery y poder activar los autocomplete
	crear_hiddens();
	
	// -->	ENCABEZADO
	//encabezado2("Publicación scripts", $wactualiz, 'clinica');
	echo "
	<br><br><br><br><br>
	<div align='center'>
		<table width='75%' border='0' cellpadding='3' cellspacing='3'>
			<tr>
				<td align='left'>
					<div align='center' class='borderDiv2' id='div_contenedor' >						
						<div style='font-size: 7pt;' align='right'>
							<a style='cursor:pointer;color:#2A5DB0;font-weight:bold;text-decoration:none;' onclick='window.open(this.href);return false' href='Matricula_scripts.tec.pdf'>Manual Técnico</a>
						</div>
						<div class='borderDiv Titulo_azul' align=center>
						PUBLICACION Y DESCARGA DE SCRIPTS
						</div><br>
						<div style='padding: 20px;align='left'>
							<table width='92%' ><tr>
							<td align='left'>
								<div id='radio'>
									<input type='radio' id='Publicar' value='Publicar' name='radio' onClick='pintar_formulario(\"pintar_publicar\");'/>
									<label class='bordeAzul' for='Publicar'>Publicar</label>
									<input type='radio' id='Descargar' value='Descargar' name='radio' onClick='pintar_formulario(\"pintar_descargar\");'/>
									<label class='bordeAzul' for='Descargar'>Descargar</label>
									<input type='radio' id='Devolver' value='Devolver' name='radio' onClick='pintar_formulario(\"pintar_devolver\");'/>
									<label class='bordeAzul' for='Devolver'>Devolver</label>
								</div>
							</td>
							<td align='right'>
								<div id='div_mensajes' class='borderDiv FondoAmarillo' style='display:none;' align='center'></div>
							</td>
							</tr></table>
							<div id='formulario'>
							</div>
						</div><br>
					</div>
				</td>
			</tr>
		</table><br>
		<button>Cerrar</button> 
	</div>
	";
	?>
	</BODY>
<!--=====================================================================================================================================================================     
	F I N   B O D Y
=====================================================================================================================================================================-->	
	</HTML>
	<?php
//=======================================================================================================================================================
//	F I N  E J E C U C I O N   N O R M A L   
//=======================================================================================================================================================
}

}//Fin de session
?>
