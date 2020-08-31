<?php
include_once("conex.php");
//=========================================================================================================================================\\
// 			MATRICULA DE SCRIPTS MATRIX         
//=========================================================================================================================================\\
//DESCRIPCION:			
//AUTOR:				Jerson Andres Trujillo
//FECHA DE CREACION: 	2013-01-11
//--------------------------------------------------------------------------------------------------------------------------------------------
//                  ACTUALIZACIONES   
//--------------------------------------------------------------------------------------------------------------------------------------------                                                                                                                       \\
			$wactualiz='15-Marzo-2013';
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
	$wbasedato = 'root';
	$wfecha=date("Y-m-d");   
    $whora = date("H:i:s");


//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S    P H P
//=====================================================================================================================================================================
	
	//-------------------------------------------------------------------------------------------------
	//	Nombre: 		Validar si el script existe
	//	Descripcion:	Funcion que valida si el script ya existe en la matricula, para no tener duplicados
	//	Entradas:		Nombre del script
	//	Salidas:		TRUE = Existe, FALSE = No existe
	//-------------------------------------------------------------------------------------------------
	function validar_existe_script($script)
	{
		global $wbasedato;
		global $conex;	
		
		$q_existe = " SELECT count(*) as numero
					    FROM ".$wbasedato."_000087
					   WHERE Matcod = '".$script."'
					";
		$res_existe = mysql_query($q_existe,$conex) or die("Error: " . mysql_errno() . " - en el query (Validar existencia script): ".$q_existe." - ".mysql_error());
		$row_existe = mysql_fetch_array($res_existe);
		if($row_existe['numero'] > 0)
			return true;
		else
			return false;
	}
	//-------------------------------------------------------------------------------------------------
	//	Nombre: 		Obtener maestro de carpetas
	//	Descripcion:	Funcion que consulta el maestro de las carpetas y el resultado lo retorna en un
	//					array.
	//	Entradas:
	//	Salidas:
	//-------------------------------------------------------------------------------------------------
	function obtener_maestro_carpetas()
	{
		global $wbasedato;
		global $conex;	
		$q_carpetas = "SELECT Carcod, Cardes, id, Carpdu
						 FROM ".$wbasedato."_000090
						WHERE Carest = 'on'
						ORDER BY id
					";
		$res_carpetas = mysql_query($q_carpetas,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar carpetas): ".$q_carpetas." - ".mysql_error());
		
		$arr_carpetas = array();
		while($row_carpetas = mysql_fetch_array($res_carpetas))
		{
			$arr_carpetas[$row_carpetas['Carcod']] = $row_carpetas['Cardes'].'|'.$row_carpetas['Carpdu'];
		}
		return $arr_carpetas;
	}
	//-------------------------------------------------------------------------------------------------
	//	Nombre: 		Obtener maestro de estados
	//	Descripcion:	Funcion que consulta el maestro de los estados y el resultado lo retorna en un
	//					array.
	//	Entradas:
	//	Salidas:
	//-------------------------------------------------------------------------------------------------
	function obtener_maestro_estados()
	{
		global $wbasedato;
		global $conex;	
		$q_estados = " SELECT Estcod, Estnom
						 FROM ".$wbasedato."_000088
						WHERE Estest = 'on'
						ORDER BY Estnom
					";
		$res_estados = mysql_query($q_estados,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar estados): ".$q_estados." - ".mysql_error());
		
		$arr_estados = array();
		while($row_estados = mysql_fetch_array($res_estados))
		{
			$arr_estados[$row_estados['Estcod']] = $row_estados['Estnom'];
		}
		return $arr_estados;
	}
	//-------------------------------------------------------------------------------------------------
	//	Nombre: 		Crar hidden de los scripts
	//	Descripcion:	Funcion que consulta los scripts existente en la matricula, para luego crear un json 
	//					con los resultados de la consulta y posteriormente se crea una variable hidden
	//					con dicho json.
	//	Entradas:
	//	Salidas:
	//-------------------------------------------------------------------------------------------------
	function crear_hidden_scripts()
	{
		global $wbasedato;
		global $conex;
		
		// --> Consultar el codigo correspondiente para el estado de baja.
		$cod_est_debaja = consultarAliasPorAplicacion($conex, '01', 'CodEstadoScriptDebaja');
		
		$q_scripts = "SELECT Matcod
					    FROM ".$wbasedato."_000087
					   WHERE Matest != '".$cod_est_debaja."'
					   ORDER BY Matcod
					";
		$res_scripts = mysql_query($q_scripts,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar scripts): ".$q_scripts." - ".mysql_error());
		
		$arr_scripts = array();
		while($row_scripts = mysql_fetch_array($res_scripts))
		{
			$arr_scripts[] = $row_scripts['Matcod'];
		}
		echo "<input type='hidden' id='hidden_scripts' name='hidden_scripts' value='".json_encode($arr_scripts)."'>";
	}
	//-------------------------------------------------------------------------------------------------
	//	Nombre: 		Crar hidden de los grupos
	//	Descripcion:	Funcion que consulta los grupos existente en matrix, para luego crear un json 
	//					con los resultados de la consulta y posteriormente se crea una variable hidden
	//					con dicho json.
	//	Entradas:
	//	Salidas:
	//-------------------------------------------------------------------------------------------------
	function crear_hidden_grupo()
	{
		global $wbasedato;
		global $conex;
		
		$q_grupos = "SELECT descripcion
					   FROM det_selecciones
					  WHERE codigo='grupos'
					  ORDER BY descripcion
					";
		$res_grupo = mysql_query($q_grupos,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar grupos): ".$q_grupos." - ".mysql_error());
		
		$arr_grupos = array();
		while($row_grupos = mysql_fetch_array($res_grupo))
		{
			$arr_grupos[] = $row_grupos['descripcion'];
		}
		echo "<input type='hidden' id='hidden_grupos' name='hidden_grupos' value='".json_encode($arr_grupos)."'>";	
	}
	//-------------------------------------------------------------------------------------------------
	//	Nombre: 		Crar hidden de los desarrolladores
	//	Descripcion:	Funcion que consulta los desarrolladores existente en matrix, para luego crear un json 
	//					con los resultados de la consulta y posteriormente se crea una variable hidden
	//					con dicho json.
	//	Entradas:
	//	Salidas:
	//-------------------------------------------------------------------------------------------------
	function crear_hidden_desarrolladores()
	{
		global $wbasedato;
		global $conex;
		
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
	//--------------------------------------------------------------------------------------------------
	//	Nombre: 		Consultar nombre del desarrollador
	//	Descripcion:	Funcion para validar que el desarrollador si exista, y consulta su correspondiente codigo 
	//	Entradas:		Nombre del desarrollador
	//	Salidas:		Codigo del desarrollador
	//--------------------------------------------------------------------------------------------------
	function nombre_desarrollador($codigo)
	{
		global $wbasedato;
		global $conex;
		$q_nom_desarroll = "SELECT Descripcion
							  FROM usuarios
							 WHERE Codigo = '".$codigo."' 
							";
		$res_nom_desarroll = mysql_query($q_nom_desarroll,$conex) or die("Query (Consultar desarrollador): ".$q_nom_desarroll."<br>Error: ".mysql_error());
		
		if ($row_nom_desarroll = mysql_fetch_array($res_nom_desarroll))
			return $row_nom_desarroll['Descripcion'];
		else
			return "";
	}
	//-------------------------------------------------------------------------------------------------
	//	Nombre: 		Formulario de matricula
	//	Descripcion:	Funcion que pinta el formulario para matricular un nuevo script.
	//	Entradas:
	//	Salidas:
	//-------------------------------------------------------------------------------------------------
	function formulario_matricula($script='')
	{
		global $wbasedato;
		global $conex;
		global $wuse;
		
		// --> Obtener los usuarios que aprueban scripts
		$usuarios_aprueban = consultarAliasPorAplicacion($conex, '01', 'UsuarioMatriculaScripts');
		$arr_usu_apru = explode(',' ,$usuarios_aprueban);
		
		// --> Consultar informacion del script
		if($script != '')
		{
			$arr_script = explode('|', $script);
			$cod_script = $arr_script[0];
			$cod_grupo	= $arr_script[1];
			
			$q_info_script = " SELECT Matcod, Matdes, Matgru, Matcar, Descripcion, Matfup, Mathup, Matuup, Matfud, Mathud, Matuud, Estcod, Estnom 
								 FROM ".$wbasedato."_000087 as A LEFT JOIN usuarios AS B on A.Matcre = B.Codigo, ".$wbasedato."_000088 
								WHERE Matcod = '".$cod_script."'
								  AND Matgru = '".$cod_grupo."'
								  AND Matest = Estcod
							";
			$res_info_script = mysql_query($q_info_script,$conex) or die("Error en el query (INFORMACION DEL SCRIPT): ".$q_info_script." <BR> ".mysql_error());
			$row_info_script = mysql_fetch_array($res_info_script); 
		}
		// <-- Fin consultar informacion
		
		echo "	<br>
		<div name='form_matricula' id='form_matricula' align='left' class='borderDiv2' style='margin: 10px;'>
				<div align='right'>
					<button>Cerrar</button>
				</div><br>";
		echo"	<div class='borderDiv Titulo_azul' align=center>
					".(($script!='') ? $row_info_script['Matcod'] : 'MATRICULAR NUEVO SCRIPT')."
				</div><br>";
		echo'
		<div align="center" class="borderDiv ">
			<br>
			<table width="92%" align="center" >';
		//	-->	Nombre del script
		echo 	"<tr class='fila2'>
					<td class='encabezadoTabla' style='width :20%'>Nombre Script:&nbsp;</td>	
					<td align='center' style='padding:3px;'>
						<input type='text' size=80 name='fnombre' id='fnombre' ".(($script!='') ? 'value=\''.$row_info_script['Matcod'].'\' disabled=\'disabled\' ' : '')." onmouseover='validar(this);' onblur='validar(this);validar_existe_script();'>
						<div align='right' id='div_fnombre' style='display:none;'></div>
					</td>
				</tr>";
		//	-->	Descripcion
		echo 	"<tr class='fila1'>
					<td class='encabezadoTabla' >Descripción:</td>	
					<td align='center' style='padding:3px;'>
						<textarea rows=2 cols=60 name='fdescripcion' id='fdescripcion' onmouseover='validar(this);' onblur='validar(this)'>".(($script!='') ? $row_info_script['Matdes'] : '')."</textarea>
						<div align='right' id='div_fdescripcion' style='display:none;' ></div>
					</td>
				</tr>";
		
		//  --> Validar si permito actualizar el grupo y el tipo
		$Cod_est_regis = consultarAliasPorAplicacion($conex, '01', 'CodEstadoScriptRegistrado');
		$Cod_est_cread = consultarAliasPorAplicacion($conex, '01', 'CodEstadoScriptCreado');
		if($script!='' &&  ( $row_info_script['Estcod'] != $Cod_est_regis && $row_info_script['Estcod'] != $Cod_est_cread) )
		{
			$deshabilitar = 'disabled';
		}
		else
		{
			$deshabilitar = '';
		}
		
		//	-->	Grupo
		echo 	"<tr class='fila2' id='tr_grupo' ".(($script!='' && $row_info_script['Matgru']=='') ? 'style=\'display:none;\' ' : '').">
					<td class='encabezadoTabla' >Grupo:</td>	
					<td align='center' style='padding:3px;'>
						<input type='text' size=40 name='fgrupo' id='fgrupo' ".(($script!='') ? 'value=\''.$row_info_script['Matgru'].'\' ' : '')." ".$deshabilitar." onmouseover='validar(this);' onblur='validar(this)'>";
						
						// --> Si el usuario logeado es un usuario que aprueba scripts, entonces activo la opcion de crear grupos
						if (in_array($wuse, $arr_usu_apru))
						{		
							echo "&nbsp;&nbsp;<img src='images/medical/HCE/mas.PNG' width='10' height='10' id='agregar_grupo' title='Agregar un nuevo grupo' style='cursor:pointer;' onClick='agregar_nuevo_grupo();'>";
						}
		echo	"		<div align='right' id='div_fgrupo' style='display:none;' ></div>
					</td>
				</tr>";
		//	-->	Tipo
		echo 	"<tr class='fila1'>
					<td class='encabezadoTabla' >Tipo:</td>	
					<td align='center' style='padding:3px;' >";
					$array_carpetas = obtener_maestro_carpetas ();
					foreach ($array_carpetas as $indice => $valor)
					{
						$valor = explode('|', $valor);
						$nombre_car = $valor[0];
						$permi_dupli= $valor[1];

						if($indice == 'general')
						{
							echo "<input type='radio' id='".$indice."' value='".$indice."' ".$deshabilitar." permite_dup='".$permi_dupli."' name='radio' ".(( $script!='' && $row_info_script['Matcar'] == $indice ) ? 'checked=checked' : '' )." onClick='$(\"#tr_grupo\").hide();validar_existe_script();'/>";
						}
						else
						{
							echo "<input type='radio' id='".$indice."' value='".$indice."' ".$deshabilitar." permite_dup='".$permi_dupli."' name='radio' ".(( $script!='' && $row_info_script['Matcar'] == $indice ) ? 'checked=checked' : '' )." onClick='$(\"#tr_grupo\").show();validar_existe_script();'/>";
						}
						echo "<b>".ucfirst($nombre_car)."</b>&nbsp;";
					}
					
		echo	"	<div align='right' id='div_ftipo' style='display:none;' ></div>
					</td>
				</tr>";
		//	-->	Desarrollador
		echo 	"<tr class='fila2'>
					<td class='encabezadoTabla' >Desarrollado por:</td>	
					<td align='center' style='padding:3px;'>
						<input type='text' size=40 name='fdesarrollador' id='fdesarrollador' ".(($script!='') ? 'value=\''.$row_info_script['Descripcion'].'\' ' : '')." onmouseover='validar(this);' onblur='validar(this)'>
						<div align='right' id='div_fdesarrollador' style='display:none;' ></div>
					</td>
				</tr>";
		if($script != '')
		{
			//	-->	Ultima descarga
			echo 	"<tr class='fila1'>
						<td class='encabezadoTabla' >Ultima descarga:</td>	
						<td align='center' style='padding:3px;'>";
						if($row_info_script['Matfud'] != '0000-00-00')
						{
							echo "
							<b>Fecha:</b> ".$row_info_script['Matfud']."&nbsp;&nbsp;
							<b>Hora:</b> ".$row_info_script['Mathud']."&nbsp;&nbsp;
							<b>Responsable:</b> ".nombre_desarrollador($row_info_script['Matuud'])."";
						}
						else
							echo "<b>No registra descargas</b>";
			echo	"	</td>
					</tr>";
			//	-->	Ultima publicacion
			echo 	"<tr class='fila2'>
						<td class='encabezadoTabla' >Ultima publicación:</td>	
						<td align='center' style='padding:3px;'>";
						if($row_info_script['Matfup'] != '0000-00-00')
						{
							echo"
							<b>Fecha:</b> ".$row_info_script['Matfup']."&nbsp;&nbsp;
							<b>Hora:</b> ".$row_info_script['Mathup']."&nbsp;&nbsp;
							<b>Responsable:</b> " .nombre_desarrollador($row_info_script['Matuup'])."";
						}
						else
							echo "<b>No registra publicaciones</b>";
			echo 	"	</td>
					</tr>";
			//	-->	Estado actual
			echo 	"<tr class='fila1'>
						<td class='encabezadoTabla' >Estado actual:</td>
						<td align='center' style='padding:3px;'>
							<b>".$row_info_script['Estnom']."</b>
						</td>
					</tr>";
		}
		// --> Guardar
		echo "	<tr>
					<td align='center' colspan='2'><br>
						";
						
					if ($script!='' )
					{
						// --> Si el usuario del sistema es un usuario que aprueba
						if (in_array($wuse, $arr_usu_apru) && $row_info_script['Estcod'] == consultarAliasPorAplicacion($conex, '01', 'CodEstadoScriptCreado'))
							$nom_boton = 'Aprobar Registro';
						else
							$nom_boton = 'Actualizar';
							
						echo "<button>".$nom_boton."</button> <input type='hidden' id='nombre_script' value='".$row_info_script['Matcod']."'>";
					}
					else
					{
						// --> Si el usuario del sistema es un usuario que aprueba
						if (in_array($wuse, $arr_usu_apru))
							$nom_boton = 'Registrar';					
						else
							$nom_boton = 'Crear';
							
						echo "<button>".$nom_boton."</button> <input type='hidden' id='nombre_script' value='nuevo'>";
					}
		echo "			<br><br>
						<div id='div_mensajes' class='borderDiv FondoAmarillo' style='display:none;' align='center'>
							<BLINK><img width='15' height='15' src='images/medical/root/info.png' /></BLINK>
						</div>
					</td>
				</tr>";	
		echo'</table>
		</div>
		</div>';
	}
	//-------------------------------------------------------------------------------------------------
	//	Nombre: 		Listar script
	//	Descripcion:	Funcion que pinta el contenedor para la lista de script, y hace el respectivo 
	//					llamado a la funcion de pintar.
	//	Entradas:
	//	Salidas:
	//-------------------------------------------------------------------------------------------------
	function listar_scripts($script='')
	{
		global $wbasedato;
		global $conex;
		
		echo "	
			<table width='97%' border='0' align='center' cellspacing='1' cellpadding='2' name='lista_scripts' id='lista_scripts'>	
				<tr>
					<td colspan=5>
						<table width='100%'>
							<tr>
								<td id='filtros2' align='left' width='13%'>
									<button>Buscar</button>
								</td>
								<td id='filtros' width='74%'></td>
								<td align='right' width='13%'>
									<button>Nuevo</button>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr>
					<td colspan=5 align='center'>
					</td>
				</tr>
				<tr>
					<td><br>
						<div id='div_lista'>";
						echo pintar_lista($script);
		echo"			</div>
					</td>
				</tr>
			</table>";

	}
	//-------------------------------------------------------------------------------------------------
	//	Nombre: 		Pintar encabezado
	//	Descripcion:	
	//	Entradas:
	//	Salidas:
	//-------------------------------------------------------------------------------------------------
	function encabezado2($wtitulo, $wversion, $wlogemp)
	{
		echo "<table border=0>";
		
		echo "<tr>";
		echo "<td width='10%' rowspan=2>&nbsp;"; 
		
		echo "<img src='images/medical/root/".$wlogemp.".jpg' width=120 heigth=76>";
		echo "</td>";
		echo "<td width='90%' class='fila1'>";
		echo "<div class='titulopagina' align='center'>";

		echo $wtitulo;
		
		echo "</div>";
		echo "</td>";
		echo "<td width='10%' rowspan=2>&nbsp;";
		echo "<img src='images/medical/root/fmatrix.jpg' width=120 heigth=76>";
		echo "</td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td colspan='1' align='right' class='fila2'>";
		echo "<span class='version'>Versi&oacute;n: $wversion<br>
				<a style='cursor:pointer;font-weight:bold;text-decoration:none;' onclick='window.open(this.href);return false' href='Matricula_scripts.tec.pdf'>Manual Técnico</a>
			</span>";
		echo "</td>";
		echo "</tr>";
		
		echo "</table>";
		echo "<br>";
	}
	//-------------------------------------------------------------------------------------------------
	//	Nombre: 		Pintar lista
	//	Descripcion:	Funcion que pinta la lista de todos los scripts existentes en la root_000087
	//	Entradas:
	//	Salidas:
	//-------------------------------------------------------------------------------------------------
	function pintar_lista($script='', $busc_script= '%', $busc_grupo='%', $busc_carpetas='%', $busc_estados='%')
	{
		global $wbasedato;
		global $conex;
		
		if($busc_carpetas == 'Todos')
			$busc_carpetas = '%';
		if($busc_estados == 'Todos')
			$busc_estados = '%';
			
		// --> Crear un array con la lista de desarrolladores
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
			$arr_desarro[$row_desarro['Perusu']] = $row_desarro['Descripcion'];
		}
		// <-- Fin crear array
		
		$q_matriculas=" SELECT C.Fecha_data, Matcod, Matgru, Matcar, Matdes, Estnom, Matcre
						  FROM ".$wbasedato."_000087 as C, ".$wbasedato."_000088
						 WHERE Matest	= Estcod	";
						if($script != '')
		$q_matriculas.="   AND Matcod   = '".$script."'	";
						else
		$q_matriculas.="   AND Matcod   LIKE '%".$busc_script."%'	";				
		
		$q_matriculas.="   AND Matgru	LIKE '%".$busc_grupo."%'
						   AND Matcar	LIKE '%".$busc_carpetas."%'
						   AND Estcod	LIKE '%".$busc_estados."%'
						 ORDER BY Matcod
						";
						
		$res_matriculas = mysql_query($q_matriculas,$conex) or die("Error: " . mysql_errno() . " - en el query (Consultar matriculas): ".$q_matriculas." - ".mysql_error());
		$num_scripts 	= mysql_num_rows ($res_matriculas);
		
		$fila_lista ="Fila1";
		echo '<div style="color: #000000;font-size: 8pt;font-weight: bold;"> Registros: '.$num_scripts.'</div>';		
		echo"
			<table width='100%' id='table_lista'>
				<tr class='encabezadoTabla' align='center'>
					<td>Script</td><td>Grupo</td><td>Tipo</td><td>Descripción</td><td>Desarrollado por</td><td>Creación</td><td>Estado</td>
				</tr>";
		if ($num_scripts > 0)
		{
			while ($row_scripts = mysql_fetch_array($res_matriculas))
			{
				if ($fila_lista=='Fila2')
					$fila_lista = "Fila1";
				else
					$fila_lista = "Fila2";
				
				echo "
					<tr class=".$fila_lista." style='cursor:pointer;' onClick='nuevo_script(\"".$row_scripts['Matcod']."|".$row_scripts['Matgru']."\")'>
						<td>".$row_scripts['Matcod']."</td>
						<td>".$row_scripts['Matgru']."</td>
						<td>".$row_scripts['Matcar']."</td>
						<td>".$row_scripts['Matdes']."</td>
						<td>".((array_key_exists($row_scripts['Matcre'], $arr_desarro)) ? $arr_desarro[$row_scripts['Matcre']] : '' )."</td>
						<td>".$row_scripts['Fecha_data']."</td>
						<td align='center'>".$row_scripts['Estnom']."</td>
					</tr>";
			}
		}
		else
		{
			echo'	<tr class="fila2" >
						<td colspan=7 align=center><b>No se encontraron resultados.</b></td>
					</tr>';
		}
		echo "</table>";
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
			formulario_matricula ($script);
			break;
			return;
		}
		case 'listar_scripts':
		{
			listar_scripts($script);
			break;
			return;
		}
		case 'filtrar_lista':
		{
			pintar_lista('', $busc_script, $busc_grupo, $busc_carpetas, $busc_estados);
			break;
			return;
		}
		case 'grabar_nuevo_grupo':
		{
			// --> Validar que el grupo no exista
			$q_exist_gru = " SELECT count(*) as cantidad
							   FROM det_selecciones
							  WHERE medico = 'root'
							    AND codigo = 'grupos'
								AND descripcion = '".$fnombre_gru."'
						";
			$res_exist_gru = mysql_query($q_exist_gru,$conex) or die ("Error: ".mysql_errno()." - en el query (Validar Existe grupo): ".$q_exist_gru." - ".mysql_error());
			$row_exist_gru = mysql_fetch_array($res_exist_gru);
			if($row_exist_gru['cantidad'] > 0)
			{
				echo "El grupo ya existe";
			}
			else
			{
				// --> Obtener el subcodigo
				$q_subcodigo = " SELECT MAX(subcodigo) as subcodigo
								   FROM det_selecciones
								  WHERE medico = 'root'
									AND codigo = 'grupos'
							";
				$res_subcodigo = mysql_query($q_subcodigo,$conex) or die ("Error: ".mysql_errno()." - en el query (Obtener subcodigo): ".$q_subcodigo." - ".mysql_error());
				$row_subcodigo = mysql_fetch_array($res_subcodigo);
				$subcodigo = $row_subcodigo['subcodigo']+1;
				$subcodigo = '0'.$subcodigo;
				
				// --> Insertar el nuevo grupo
				$q_guardar="	INSERT INTO det_selecciones
											( medico, codigo, 		subcodigo, 		descripcion, 	activo)
									VALUES  ('root', 'grupos', '".$subcodigo."', '".$fnombre_gru."', 'A' )
										";
				mysql_query($q_guardar,$conex) or die ("Error: ".mysql_errno()." - en el query (Grabar grupo): ".$q_guardar." - ".mysql_error());
				
				echo "Grupo creado";
			}
			break;
			return;
		}
		case 'validar_si_existe_script':
		{
			if (validar_existe_script($fnombre))
				echo "Nombre del script ya existe.<br>No se permiten scripts con nombres duplicados.";
			else
				echo "ok";
			break;
			return;
		}
		case 'registrar_script':
		{
			// --> Consultar el codigo del desarrollador
			$q_cod_desa = "SELECT Codigo
							 FROM usuarios
							WHERE Descripcion = '".$fdesarrollador."'
						";
			$res_cod_desa = mysql_query($q_cod_desa,$conex) or die ("Error: ".mysql_errno()." - en el query (Consultar codigo desarrollador): ".$q_cod_desa." - ".mysql_error());
			// <-- Fin consultar
			
			// --> Obtener los usuarios que aprueban scripts
			$usuarios_aprueban = consultarAliasPorAplicacion($conex, '01', 'UsuarioMatriculaScripts');
			$arr_usu_apru = explode(',' ,$usuarios_aprueban);
			// --> Obtener los codigos para los estados Registrado y Creado 
			$Cod_est_regis = consultarAliasPorAplicacion($conex, '01', 'CodEstadoScriptRegistrado');
			$Cod_est_cread = consultarAliasPorAplicacion($conex, '01', 'CodEstadoScriptCreado');
			
			// --> Si el usuario del sistema es un usuario que aprueba, el estado queda en REgistrado, sino queda en CReado
			if (in_array($wuse, $arr_usu_apru))
				$estado = $Cod_est_regis;					
			else
				$estado = $Cod_est_cread;
				
			// --> Validar el desarrollador
			if($cod_fdesarrollador = mysql_fetch_array($res_cod_desa))	
			{
				// --> Consultar si el grupo existe
				$q_cod_gru = " 	 SELECT descripcion
								   FROM det_selecciones
								  WHERE codigo='grupos'
								    AND descripcion = '".$fgrupo."'
							";
				$res_cod_gru = mysql_query($q_cod_gru,$conex) or die ("Error: ".mysql_errno()." - en el query (Validar grupo): ".$q_cod_gru." - ".mysql_error());
				// <-- Fin consultar
				
				// --> Validar el grupo
				if(mysql_fetch_array($res_cod_gru) || $fgrupo=='')
				{
					// --> Insertar el script
					if($nombre_script=='nuevo')
					{
						$q_guardar="	INSERT INTO ".$wbasedato."_000087
										( Medico, 		  Fecha_data,  	Hora_data, 		Matcod, 		Matgru, 	Matcar, 		Matdes, 	 		Matest, 		Matcre,			   				Seguridad, id )
								VALUES  ('".$wbasedato."','".$wfecha."','".$whora."','".$fnombre."','".$fgrupo."','".$ftipo."','".$fdescripcion."', '".$estado."', '".$cod_fdesarrollador['Codigo']."', 'C-".$wuse."','' )
									";
						mysql_query($q_guardar,$conex) or die ("Error: ".mysql_errno()." - en el query (Guardar script): ".$q_guardar." - ".mysql_error());
						
						if ($estado == $Cod_est_regis)
							echo 'Script registrado';
						else 
							echo '<b>Script creado</b>.<br>Nota: Esta matricula queda pendiente de la<br>aprobación por parte del coordinador<br>para ser registrado.';
					}
					// <-- Fin Insertar el script
					// --> Actualizar el script
					else
					{
						// --> Consultar el estado actual del script
						$q_estado_act = "SELECT Matest
										   FROM ".$wbasedato."_000087
										  WHERE Matcod = '".$nombre_script."'
										    AND Matgru = '".$fgrupo."'
										";
						$res_estado_act = mysql_query($q_estado_act, $conex) or die ("Error: ".mysql_errno()." - en el query (Consultar estado actual): ".$q_estado_act." - ".mysql_error());
						$arr_estado_act = mysql_fetch_array($res_estado_act); 
						
						$q_actualizar="	UPDATE ".$wbasedato."_000087
										   SET Fecha_data 	= '".$wfecha."',
											   Hora_data  	= '".$whora."',
											   Matgru		= '".$fgrupo."',
											   Matcar		= '".$ftipo."',
											   Matdes		= '".$fdescripcion."',";
									if($arr_estado_act['Matest'] == $Cod_est_cread && $estado == $Cod_est_regis)
						$q_actualizar.="	   Matest		= '".$estado."',		";				
											   
						$q_actualizar.="	   Matcre		= '".$cod_fdesarrollador['Codigo']."'
										WHERE  Matcod		= '".$nombre_script."'
										  AND  Matgru		= '".$fgrupo."'
									";
						mysql_query($q_actualizar,$conex) or die ("Error: ".mysql_errno()." - en el query (Actualizar script): ".$q_actualizar." - ".mysql_error());
						
						if($arr_estado_act['Matest'] == $Cod_est_cread && $estado == $Cod_est_regis)
							echo "Script Registrado";
						else
							echo 'Script Actualizado';
					}
					// --> Fin actualizar el script
				}
				else
				{
					echo "Grupo no válido";
				}
			}
			else
			{
				echo "Desarrollador no válido";
			}
			
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
	  <title>Matricula de scripts</title>
	</head>
	<!--<link type="text/css" href="../../../include/root/jquery_1_7_2/css/demo_page.css" rel="stylesheet"/>-->
	<!--<link type="text/css" href="../../../include/root/jquery_1_7_2/css/demo_table.css" rel="stylesheet"/>-->
	<!--<link type="text/css" href="../../../include/root/jquery_1_7_2/css/demo_validation.css" rel="stylesheet"/>-->
	<link type="text/css" href="../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
	<link type="text/css" href="../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet"/>
	<link type="text/css" href="../include/root/jquery.tooltip.css" rel="stylesheet" />
			
	<script src="../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<!--<script src="../../../include/root/jquery_1_7_2/js/jquery.DataTables.min.js" type="text/javascript"></script>-->
	<!--<script src="../../../include/root/jquery_1_7_2/js/jquery.jeditable.js" type="text/javascript"></script>-->
	<script src="../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
	<script src="../include/root/jquery_1_7_2/js/jquery.validate.js" type="text/javascript"></script>
	<!--<script src="../../../include/root/jquery_1_7_2/js/jquery.DataTables.editable.js" type="text/javascript"></script>-->
	<!--<script src="../../../include/root/jquery_1_7_2/js/jquery.jeditable.checkbox.js" type="text/javascript"></script>-->
	<!--<script src="../../../include/root/jquery_1_7_2/js/jquery.jeditable.datapicker.js" type="text/javascript"></script>-->
	<script src="../include/root/jquery.tooltip.js" type="text/javascript"></script>
	<!--<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>-->

	<script type="text/javascript">
//=====================================================================================================================================================================     
// 		F U N C I O N E S	 G E N E R A L E S   J A V A S C R I P T 
//=====================================================================================================================================================================
	
	$(document).ready(function() {
		cargar_buttons(1);
		ajustar_tamaño(500);
		//Cargar autocomplete de los grupos en los filtros
		var arr_grupos = eval('(' + $('#hidden_grupos').val() + ')');
		$( "#busc_grupo" ).autocomplete({
		  source: arr_grupos
		});
		
		//Cargar autocomplete de los grupos y desarrolladores
		var arr_scripts = eval('(' + $('#hidden_scripts').val() + ')');
		$( "#busc_script" ).autocomplete({
			source: arr_scripts
		});
		//Cargar el blink para explorer
		if (browser=="Microsoft Internet Explorer" || browser=="Netscape")
		{
			setInterval( "parpadeo()",600 );
		}
	});

	//---------------------------------------------------------------------------------
	//	Nombre:			permitir grabar
	//	Descripcion:	Muestra el boton de grabar si es que esta oculto
	//	Entradas:		
	//	Salidas:
	//----------------------------------------------------------------------------------
	function permitir_grabar()
	{
		$("#div_mensajes").hide();
		//Muestro el boton de registrar
		$( "button" ).each(function( index ) {
			if($(this).text() == 'Registrar' || $(this).text() == 'Crear')
			{
				$(this).show();
			}
		});
	}
	//---------------------------------------------------------------------------------
	//	Nombre:			validar si ya existe el script
	//	Descripcion:	valida si el script que se esta ingresando ya existe en la matricula
	//	Entradas:		
	//	Salidas:
	//----------------------------------------------------------------------------------
	function validar_existe_script()
	{
		if($('#fnombre').val() != '')
		{
			// --> Primero valido que el script tenga alguna extencion
			var extencion = $('#fnombre').val().indexOf(".");
			if (extencion == -1)
			{
				mostrar_mensaje('El nombre del script debe contener la extención.');
				//Oculto el boton de registrar
				$( "button" ).each(function( index ) {
					if($(this).text() == 'Registrar' || $(this).text() == 'Crear')
					{
						$(this).hide();
					}
				});
				return;
			}
			else
			{
				$("#div_mensajes").hide();
				//Muestro el boton de registrar
				$( "button" ).each(function( index ) {
					if($(this).text() == 'Registrar' || $(this).text() == 'Crear')
					{
						$(this).show();
					}
				});
			}
		
			// --> Realizo el llamado a la funcion php para validar que el script no este duplicado
			$.post("Matricula_scripts.php",
				{
					consultaAjax:   '',
					wemp_pmla:      $('#wemp_pmla').val(),
					wuse:           $('#wuse').val(),
					wbasedato:		$('#wbasedato').val(),
					accion:         'validar_si_existe_script',
					fnombre:    	$('#fnombre').val()
					
				}
				,function(respuesta) {
					if(respuesta != 'ok')
					{
						// --> Revisar si el tipo que esta checkeado permite duplicados
						var permite_dup = 'false';
						$('[type=radio]').each(function( index ) {
							if ($(this).is(':checked') && $(this).attr('permite_dup') == 'on')
							{
								permite_dup = 'true';
							}
						});
						
						if (permite_dup == 'false')
						{
							mostrar_mensaje(respuesta);
							//Oculto el boton de registrar
							$( "button" ).each(function( index ) {
								if($(this).text() == 'Registrar' || $(this).text() == 'Crear')
								{
									$(this).hide();
								}
							});
						}
					}
					else
					{
						$("#div_mensajes").hide();
						//Muestro el boton de registrar
						$( "button" ).each(function( index ) {
							if($(this).text() == 'Registrar' || $(this).text() == 'Crear')
							{
								$(this).show();
							}
						});
					}
				}
			);
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
		$("#div_mensajes").css({"width":"300","opacity":" 0.6","fontSize":"11px"});
		$("#div_mensajes").hide();
		$("#div_mensajes").show(500);	
	}
	//---------------------------------------------------------------------------------
	//	Nombre:			Graba un nuevo grupo matrix
	//	Descripcion:	Funcion que graba un nuevo grupo en matrix
	//	Entradas:		
	//	Salidas:
	//----------------------------------------------------------------------------------
	function grabar_nuevo_grupo()
	{
		if ($('#nom_nue_gru').val() == '')
			return;
		else
		{
			$.post("Matricula_scripts.php",
				{
					consultaAjax:   '',
					wemp_pmla:      $('#wemp_pmla').val(),
					wuse:           $('#wuse').val(),
					wbasedato:		$('#wbasedato').val(),
					accion:         'grabar_nuevo_grupo',
					fnombre_gru:    $('#nom_nue_gru').val()
				}
				,function(respuesta) {
					if(respuesta == 'El grupo ya existe')
					{
						$('#mensaje_grupo').html('<input type=button value="Grabar" onClick="grabar_nuevo_grupo()"><br><b>'+respuesta+'</b>');
					}
					else
					{
						$('#mensaje_grupo').html('<b>'+respuesta+'</b>');
						$('#fgrupo').val($('#nom_nue_gru').val());
						$('#nom_nue_gru').val('');
					}
				}
			);
		}
		
	}
	//---------------------------------------------------------------------------------
	//	Nombre:			Agregar un nuevo grupo matrix
	//	Descripcion:	Funcion que despliega el formulario flotante para agregar el nuevo grupo
	//	Entradas:		
	//	Salidas:
	//----------------------------------------------------------------------------------
	function agregar_nuevo_grupo(mensaje)
	{
		var posicion = $('#agregar_grupo').offset();
		$('#div_nuevo_grupo').css({'position':'absolute','left':posicion.left+160,'top':posicion.top-50});
		$('#div_nuevo_grupo').toggle("normal");
		$('#mensaje_grupo').html('<input type=button value="Grabar" onClick="grabar_nuevo_grupo()">');
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
	//---------------------------------------------------------------------------------------------
	//	Nombre:			Registrar scripts
	//	Descripcion:	Valida todos los campos del formulario y envia los variables para guardar
	//					la matricula de un nuevo script.
	//	Entradas:		
	//	Salidas:
	//----------------------------------------------------------------------------------------------
	function registrar_script()
	{
		var guardar='si';											//variable semaforo que me inidcara si se puede guardar o no
		
		guardar = validar_guardar('fnombre', guardar);				
		guardar = validar_guardar('fdescripcion', guardar);
		guardar = validar_guardar('fdesarrollador', guardar);
		
		if ($("#tr_grupo").css("display") != 'none') 
			guardar = validar_guardar('fgrupo', guardar);
		else
			$('#fgrupo').val('');
		
		// --> Validar que hayan seleccionado el tipo (Procesos, Reportes, Include, ...)
		var tipo = '';
		$('[type=radio]').each(function( index ) {
			if ($(this).is(':checked'))
				tipo = $(this).val();
		});
		
		if 	(tipo =='')
		{
			$("#div_ftipo")
				.text(' * Campo Obligatorio')
				.css({"color":"red", "opacity":" 0.4","fontSize":"12px"})
				.show();
			guardar = 'no';
		}
		else
		{
			$("#div_ftipo").css("display", "none");
		}
		// <-- Fin validar tipos
		
		// --> Enviar variables
		if (guardar=='si')
		{
			$.post("Matricula_scripts.php",
				{
					consultaAjax:   '',
					wemp_pmla:      $('#wemp_pmla').val(),
					wuse:           $('#wuse').val(),
					wbasedato:		$('#wbasedato').val(),
					accion:         'registrar_script',
					fnombre:    	$('#fnombre').val(),
					fdescripcion: 	$('#fdescripcion').val(),
					fgrupo: 		$('#fgrupo').val(),
					fdesarrollador: $('#fdesarrollador').val(),
					ftipo:			tipo,
					nombre_script:	$('#nombre_script').val()
					
				}
				,function(data) {
					if (data == 'Desarrollador no válido' || data == 'Grupo no válido')
					{
						mostrar_mensaje('<b>'+data+'</b>');
					}
					else
					{
						filtrar_lista('null');
						nuevo_script($('#fnombre').val()+'|'+$('#fgrupo').val(), 'no', data);
					}
				}
			);
		}
		else
		{
			mostrar_mensaje('<b>Datos incompletos</b>');
		}
		// <-- Fin Enviar variables
	}
	//-------------------------------------------------------------------------------
	//	Nombre:			Cargar buttons
	//	Descripcion:	Activa el plug-in jquery para la visualizacion de los button
	//	Entradas:		
	//	Salidas:
	//--------------------------------------------------------------------------------
	function cargar_buttons(llamado)
	{
		$( "button" ).each(function( index ) {
			
			switch ($(this).text())
			{
				case 'Buscar':
						if(llamado == 1)
						{
							$(this)
								.button({icons:	{
												primary: "ui-icon-search"
												}
										})
								.click(function( event ){
											pintar_filtros();
										});
						}
						break;
				case 'Nuevo':
						if(llamado == 1)
						{
							$(this)
								.button({icons: {
												primary: "ui-icon-plus"
												}
										})
								.click(function( event ){
											nuevo_script('');
										});
						}
						break;
				case 'Cerrar':
						if(llamado == 2)
						{
							$(this)
								.button({icons: {
												primary: "ui-icon-close"
												},
										text: false
										})
										
								.click(function( event ){
											removerElemento("form_matricula");
										});
						}
						break;
				case 'Registrar':
				case 'Crear':
						if(llamado == 2)
						{
							$(this)
								.button({icons: {
												primary: "ui-icon-disk"
												}
										})
										
								.click(function( event ){
											registrar_script();
										});
						}
						break;
				case 'Actualizar':
				case 'Aprobar Registro':
						if(llamado == 2)
						{
							$(this)
								.button({icons: {
												primary: "ui-icon-refresh"
												}
										})
										
								.click(function( event ){
											registrar_script();
										});
							break;
						}
			}
		});
	}
	//-----------------------------------------------------------------------
	//	Nombre:			Pintar filtros
	//	Descripcion:	Pinta la caja flotante para los filtros
	//	Entradas:		
	//	Salidas:
	//-----------------------------------------------------------------------
	function pintar_filtros()
	{
		var posicion = $('#filtros').offset();
		
		$('#caja_flotante').css({'position':'absolute','left':posicion.left,'top':posicion.top});
		$('#caja_flotante').toggle("normal");
		ajustar_tamaño(500);
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
	//-----------------------------------------------------------------------
	//	Nombre:			Filtrar lista
	//	Descripcion:	Realiza la busqueda de scripts dependiendo de los 
	//					parametros de consulta.
	//	Entradas:		
	//	Salidas:
	//-----------------------------------------------------------------------
	function filtrar_lista(script)
	{
		var busc_script;
		var busc_grupo 	  = $('#busc_grupo').val();
		var busc_carpetas = $('#busc_carpetas').val();
		var busc_estados  = $('#busc_estados').val();
		
		if(script == 'null' || script == '')
		{
			busc_script   = $('#busc_script').val();
		}
		else
		{
			if(script == 'nuevo')
			{
				//ajustar_tamaño(50);
				busc_script   = $('#busc_script').val();
			}
			else
			{
				script = script.split('|');
				busc_script = script[0];
				busc_grupo  = script[1];
			}
		}
		
		$.post("Matricula_scripts.php",
			{
				consultaAjax:   '',
				wemp_pmla:      $('#wemp_pmla').val(),
				wuse:           $('#wuse').val(),
				wbasedato:		$('#wbasedato').val(),
				accion:         'filtrar_lista', 
				busc_script: 	busc_script,
				busc_grupo:		busc_grupo,
				busc_carpetas:	busc_carpetas,
				busc_estados:	busc_estados
			}
			,function(data) {
				$('#div_lista').hide();
				$('#div_lista').html(data);
				$('#div_lista').show(0, function() 
					{
						if(script != 'null' && script != '')
						{
							ajustar_tamaño(60);
						}
						else
						{
							var altura_div = $("#table_lista").height();
							if(altura_div > 500)
							{
								$('#div_lista').css(
									{
										'height': 500, 
										'overflow': 'auto',
										'background': 'none repeat scroll 0 0'
									}
								);
							}
							else
							{
								$('#div_lista').css(
									{
										'height': altura_div+15
									}
								);
							}
						}
					}
				);
			}
		);
	}
	//-----------------------------------------------------------------------
	//	Nombre:			Remover elemento
	//	Descripcion:	Oculta un elemento html
	//	Entradas:		
	//	Salidas:
	//-----------------------------------------------------------------------
	function removerElemento(elemento)
	{
		$('#'+elemento).hide(600);
		filtrar_lista('null');
	}
	
	//---------------------------------------------------------------------------
	//	Nombre:			Ajustar tamaño
	//	Descripcion:	Ajusta el tamaño del div principal para colocarle scroll
	//	Entradas:		
	//	Salidas:
	//---------------------------------------------------------------------------
	function ajustar_tamaño(altura)
	{
		var altura_div = $("#div_lista").height();
		//alert(altura_div);
		if(altura_div > altura)
		{
			$('#div_lista').css(
				{
					'height': altura, 
					'overflow': 'auto',
					'background': 'none repeat scroll 0 0'
				}
			);
		}
		else
		{
			$('#div_lista').css(
				{
					'height': altura_div+15
				}
			);
		}
	}
	//-----------------------------------------------------------------------
	//	Nombre:			Nuevo script
	//	Descripcion:	Pinta el formulario para matricular o actualizar un script
	//	Entradas:		Nombre del script (ejm: archivo.php), si es vacio indica que se pintara el formulario 
	//					para matricular uno nuevo
	//	Salidas:
	//-----------------------------------------------------------------------
	function nuevo_script(script, animacion, mensaje)
	{
		$.post("Matricula_scripts.php",
			{
				consultaAjax:   		'',
				wemp_pmla:      		$('#wemp_pmla').val(),
				wuse:           		$('#wuse').val(),
				wbasedato:				$('#wbasedato').val(),
				script:					script,
				accion:         		'pintar_formulario'
			}
			,function(data) {
				//Capturo el hidden de grupos (json) para crear un array.
				var arr_grupos = eval('(' + $('#hidden_grupos').val() + ')');
				//Capturo el hidden de desarrolladores (json) para crear un array.
				var arr_desarrolladores = eval('(' + $('#hidden_desarrolladores').val() + ')');
				
				$("#nuevo_sript").hide();			//Ocultar Div
				$("#nuevo_sript").html(data);		//Inserto la respuesta
				
				if (animacion == 'no')
				{
					$("#nuevo_sript").show(); 		
					mostrar_mensaje('<b>'+mensaje+'<b>');
				}
				else
					$("#nuevo_sript").show(600); 	//Animacion para que despliegue		
				
				//Cargar autocomplete de los grupos y desarrolladores
				$( "#fgrupo" ).autocomplete({
				  source: arr_grupos
				});
				$( "#fdesarrollador" ).autocomplete({
				  source: arr_desarrolladores
				});
				
				$( "#radio" ).buttonset();
				filtrar_lista(script);				//En la lista solo dejo el script visualizado
				
				//Cargar la visualizacion de los botones
				cargar_buttons(2);
				//cargar tooltip
				$('#agregar_grupo').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
				//ajustar_tamaño(50);
			}
		);
	}
//=======================================================================================================================================================	
//	F I N  F U N C I O N E S  J A V A S C R I P T 
//=======================================================================================================================================================	
	</script>
	

	
	
<!--=====================================================================================================================================================================     
	E S T I L O S 
=====================================================================================================================================================================-->
	<style type="text/css">
		#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:4px;opacity:1;}
		#tooltip h6, #tooltip div{margin:0; width:auto}
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
		.Titulo_azul{
			color:#000066; 
			font-weight: bold; 
			font-family: verdana;
			font-size: 11pt;
		}
		.parrafo_text{
		background-color: #666666;
		color: #FFFFFF;
		font-family: verdana;
		font-size: 10pt;
		font-weight: bold;
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
	//----------------------------------------------
	// --> Div para la caja flotante de los filtros
	//----------------------------------------------
	echo'
		<div id="caja_flotante" style="display:none;z-index:3000;background-color: #FFFFFF;opacity:1;">
			<div id="cont_caja_flotante" style="border:solid 1px orange;background:none repeat scroll 0 0;height:55px;width:700px;overflow:auto;">
				<table width="100%">
					<tr class="EncabezadoTabla" align="center">
						<td width="35%">Script</td><td>Grupo</td><td>Tipo</td><td>Estado</td>
					</tr>
					<tr class="fila2" align="center">
						<td><input type="text" id="busc_script" size="30" onBlur="filtrar_lista(\'null\');"/></td>
						<td><input type="text" id="busc_grupo"  size=25 onBlur="filtrar_lista(\'null\');"></td>
						<td width="15%">
							<select id="busc_carpetas" onChange="filtrar_lista(\'null\');">
								<option>Todos</option>';
							$array_carpetas = obtener_maestro_carpetas ();
							foreach ($array_carpetas as $cod => $des)
							{
								$des = explode('|', $des);
								$des = $des[0];
								echo "<option value='".$cod."'>".$des."</option>";
							}
	echo'					</select>
						</td>
						<td>
							<select id="busc_estados" onChange="filtrar_lista(\'null\');">
								<option>Todos</option>';
							$array_estados = obtener_maestro_estados();
							foreach ($array_estados as $cod => $des)
							{
								echo "<option value='".$cod."'>".$des."</option>";
							}
	echo'					</select>
						</td>
					</tr>	
				</table>
			</div>
		</div>
	';
	//------------------------------------------------------------
	// --> Div con la caja flotante para agregar un nuevo grupo
	//------------------------------------------------------------
	echo'
		<div id="div_nuevo_grupo" style="display:none;background-color: #FFFFFF;opacity:1;">
			<div id="cont_div_nuevo_grupo" style="opacity:1;border:solid 1px orange;background:none repeat scroll 0 0;height:115px;width:160px;overflow:auto;">
				<table width="100%" >
					<tr align="right"><td><img style="cursor:pointer;" onClick="$(\'#div_nuevo_grupo\').hide(500);" src="images/medical/eliminar1.png" title="Cerrar" ></tr></td>
					<tr class="EncabezadoTabla" align="center">
						<td>
							Nuevo grupo:
						</td>
					</tr>
					<tr class="Fila2" align="center">
						<td><input type="text" id="nom_nue_gru" size="18"></td>
					</tr>
					<tr align="center"><td id="mensaje_grupo" style="color: #000000;font-size: 10pt;"></td></tr>
				</table>
			</div>
		</div>';
	
	
	// -->	ENCABEZADO
	encabezado2("Matricula de scripts", $wactualiz, 'clinica');
	
	// --> Crear un hidden de los grupos, scripts y desarrolladores, para que funcionen los autocomplete 
	crear_hidden_scripts();
	crear_hidden_grupo();
	crear_hidden_desarrolladores();
	
	echo "
	<div align='center'>
		<table width='81%' border='0' cellpadding='3' cellspacing='3'>
			<tr>
				<td align='left'>
					<div align='left' class='Titulo_azul'>
						Lista de scripts
					</div>
					<div align='center' class='borderDiv2' id='div_contenedor' >";
						listar_scripts();
	echo "			</div>
					<div id='nuevo_sript'>
					</div>
				</td>
			</tr>
		</table>
		<div align=center>
			<input type=button value='Cerrar Ventana' onclick='cerrarVentana()'>
		</div><br>
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
