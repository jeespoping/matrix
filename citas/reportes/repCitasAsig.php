<?php
include_once("conex.php");

include_once("root/comun.php");
// $conex = obtenerConexionBD("matrix");

//funcion para seleccionar los datos de configuracion de campos 
function SeleccionarDatos($wbasedato){
	
	$datosS = [];
	
	 $sql = "SELECT Concam
			  FROM root_000132 
			 WHERE Consol = '".$wbasedato."' AND  Conest = 'on'";
	$res = mysql_query( $sql );
	$datos = mysql_fetch_assoc($res);
	$Concam= $datos["Concam"];
	$datosS = explode('-',$Concam);
	return $datosS;
	
}


//array con los datos de root132 y se divide para hacer la consulta 
$listaDatos = SeleccionarDatos($wbasedato);
$numcampos = count($listaDatos);
$numcampos;
$datosAdicc = "'".implode("','",$listaDatos)."'";
$datosAdicc;  
$datosA = implode(",",$listaDatos);
$datosA;

/*
	$var = '';
	for($i = 0; $i < count($listaDatos); $i++){
		if($i == 0){
			$var = $var.$listaDatos[$i];
		}else{
			$var = $var.",".$listaDatos[$i];
		}	
	}
*/

//consulta para combinacion de los datos 
function Ordendatos($datosAdicc){
	
	$NombresC12 = '';
	
		 $sql = "SELECT descripcion,Dic_Descripcion
				   FROM root_000030 AS r30
			 INNER JOIN det_formulario AS det 
			         ON (r30.Dic_Usuario = det.medico 
					AND r30.Dic_Formulario = det.codigo 
					AND r30.Dic_Campo = det.campo)
				  WHERE det.descripcion  IN (".$datosAdicc.")
				  ORDER BY Dic_Descripcion";

	$Nombrecampos = mysql_query( $sql );
	$num = mysql_num_rows($Nombrecampos);
	if ($num > 0)
	{	
		$var=0;
		while($Nombres1 = mysql_fetch_assoc($Nombrecampos)){
			if($var == 0)
			{
			 $NombresC12 .=$Nombres1["descripcion"];
			 $var=$var+1;
			}else{
			 $NombresC12 .=",".$Nombres1["descripcion"];
			 //"<td>".$Nombres["Dic_Descripcion"]."</td>";
			
			}
		}
	}
	return $NombresC12;
}


$OC = Ordendatos($datosAdicc);
$OC;



//funcion para seleccionar el nombre de los campos para asi adicionarlos 
function AdiccionarCitas($datosAdicc)
{
	//$NombresC1 =  "<td width='19%'></td>";
	
	     $sql = "SELECT Dic_Descripcion
				   FROM root_000030 AS r30
			 INNER JOIN det_formulario AS det 
			         ON (r30.Dic_Usuario = det.medico 
					AND r30.Dic_Formulario = det.codigo 
					AND r30.Dic_Campo = det.campo)
				  WHERE det.descripcion  IN (".$datosAdicc.")
				  ORDER BY Dic_Descripcion";

	$Nombrecampos = mysql_query( $sql );
	$num = mysql_num_rows($Nombrecampos);
	if ($num > 0)
	{	
		while($Nombres = mysql_fetch_assoc($Nombrecampos)){
			
			 $NombresC1 .="<td width='19%'>".$Nombres["Dic_Descripcion"]."</td>";
			 
			 //"<td>".$Nombres["Dic_Descripcion"]."</td>";
			
		}
	}
	return $NombresC1;

}

//$NC = AdiccionarCitas($datosAdicc);
//$NC;



//seleccionar tablas citaslc
function seleccionarT($wbasedato){
	
	$datosT = '';
	
	$sq2= "SELECT Contab
		     FROM root_000132
			WHERE Consol = '".$wbasedato."' AND  Conest = 'on'";
	if($rest = mysql_query($sq2 ))
	{
		if($row = mysql_num_rows($rest)>0)
		{
			if ($datost = mysql_fetch_assoc($rest))
			{
				$datosT = $datost["Contab"];
			}
		}
	}
	return $datosT;
	
}

$tb = seleccionarT($wbasedato);
// se realiza explode para separar el nombre tabla(tb) que hay en la base de datos para realizar la comparacion y agregar los datos.

$tbNombre = explode('_',$tb);
$tbwbasedato=$tbNombre[0];
$tbwbasedatonum=$tbNombre[1];

//seleccionar el id de los pacientes


function SeleccionaCitaslce($OC,$tb,$cedula,$fecha,$hora){
	
	$Rsel1= '';
	
	if($OC!= '' && $tb != '')
	{
			  $sq3= "SELECT  id
				      FROM ".$tb." 
			 	     WHERE drvidp = '".$cedula."'
					   AND drvfec = '".$fecha."'
					   AND drvhor = '".$hora."'"; 
		$rest = mysql_query( $sq3 );
		$num = mysql_num_rows($rest);
		if ($num > 0)
		{	
			$Rsel1= '';
			if($TabR= mysql_fetch_assoc($rest)){
				$Rsel1 = $TabR["id"];
			}
		}
	}
	return $Rsel1;
	
}

//$id =SeleccionaCitaslce($OC,$tb,$cedula,$fecha,$hora);





//seleccionar los campos requeridos por cedula hora y fecha
function SeleccionaCitaslc($OC,$tb,$cedula,$fecha,$hora){
	
	global $tb;
	global $tbwbasedato;
	global $tbwbasedatonum;
	global $numcampos;
	
	$Rsel ="";
	
	for ($i=1; $i<=$numcampos; $i++) {
		
	$agregartd = "<td width='19%'></td>";
	
	$Rsel .= $agregartd;
			
			
	}
	
	
	if($OC!= '' && $tb != ''){
			
			  $sq3= "SELECT ".$OC." 
				      FROM ".$tb." 
			 	     WHERE drvidp = '".$cedula."'
					   AND drvfec = '".$fecha."'
					   AND drvhor = '".$hora."'"; 
					   
		if($rest = mysql_query( $sq3 )){
		
			$num = mysql_num_rows($rest);
			if ($num > 0)
			{	
				
				
				while($TabR= mysql_fetch_assoc($rest)){
					
					$Rsel= '';

					foreach($TabR as $key => $value){
						
						
						$comentariosDetF = comentariosDetF($tbwbasedato,$tbwbasedatonum,$key);
						$Tab_ase = $comentariosDetF[1];
						$campo1= $comentariosDetF[2];
						$campo2 = $comentariosDetF[3];
							  
						$Detase = seleccionarCdet($campo1,$Tab_ase,$tbwbasedato);
						$Detase2 = seleccionarCdet2($campo2,$Tab_ase,$tbwbasedato);
						$Detase = $Detase.",";
						$Detase2 = ",".$Detase2;	
						$DatosAseguradora = $Detase."'-'".$Detase2;
						$DatosAseguradora;
						
						$Tipocampo10 = TipoPago($tbwbasedato,$tbwbasedatonum,$key);
						
						if($Tipocampo10)
						{
							if($value == 'on')
							{
								$Si = "Si";
								$Rsel .="<td width='19%' align='center'>".$Si."</td>";
								
							}else{
								
								$No = "No";
								$Rsel .="<td width='19%'align='center'>".$No."</td>";
								
							}
							
						}else if(count($comentariosDetF) > 0 ){
							
							$mostrarAse = seleccionarAse($DatosAseguradora,$Tab_ase,$value);
							$Rsel .="<td width='19%' align='center'>".$mostrarAse."</td>";
						
						}else{
							
							$Rsel .="<td width='19%' align='center'>".$value."</td>";
							//$Rsel .="<td width='10%'>".$value."</td>";
						}
					}
				}
			}
		}	
	}
	
	return $Rsel;
	
}



//$ResultadoTabla = SeleccionaCitaslc($OC,$tb,$cedula,$fecha,$hora);
//var_dump($ResultadoTabla);


// seleccionar campo especial 
function seleccionarEsp($wbasedato){
	
	$conesp = [];
	
				$sql = "SELECT Conesp
				FROM root_000132 
				WHERE Consol = '".$wbasedato."' AND  Conest = 'on'";
				
				$resp = mysql_query( $sql );
					if($esp = mysql_fetch_assoc($resp))
					{
						$Conesp= $esp["Conesp"];
					}
				//$datosE = explode('-',$Conesp);

	return $Conesp;
	
}

$listaDatosE = SeleccionarEsp($wbasedato);
//$listaDatosE;
$datosE = '';

list($exa,$datosE) = explode("@",$listaDatosE);
$NombreCE = "'".$exa."'";
//examen  
$NombreCE;
//consulta select del campo Conesp
$datosE;




function especial($consExamen){
		
		$RE = '';
		$sql = $consExamen;
		$resp = mysql_query( $sql );
		$num = mysql_num_rows($resp);
		if ($num > 0)
		$RE = '';
		{	
			while($esp = mysql_fetch_row($resp)){
				$RE = $esp[0];
			}
		}
	return $RE;
	
}

//$examen1 = especial($consExamen);


function AdiccionarCitasEspeciales($NombreCE)
{
	$NombresCE = '';
	
	     $sql = "SELECT Dic_Descripcion
				   FROM root_000030 AS r30
			 INNER JOIN det_formulario AS det 
			         ON (r30.Dic_Usuario = det.medico 
					AND r30.Dic_Formulario = det.codigo 
					AND r30.Dic_Campo = det.campo)
				  WHERE det.descripcion  IN (".$NombreCE.")
				  ORDER BY Dic_Descripcion";

	$NombrecamposE = mysql_query( $sql );
	$num = mysql_num_rows($NombrecamposE);
	if ($num > 0)
	{	
		while($NombresE = mysql_fetch_assoc($NombrecamposE)){
			
			 $NombresCE .="<td width='19%'>".$NombresE["Dic_Descripcion"]."</td>";
			 
			 //"<td>".$Nombres["Dic_Descripcion"]."</td>";
			
		}
	}
	return $NombresCE;

}



function comentariosDetF($tbwbasedato,$tbwbasedatonum,$campov)
{
	$datosAse = [];
	
		  $sql = "SELECT comentarios
		           FROM det_formulario 
		          WHERE medico = '".$tbwbasedato."'
				    AND codigo = '".$tbwbasedatonum."'
			        AND descripcion = '".$campov."'
			        AND tipo = '18'";
					
	
	$sqlase = mysql_query( $sql );
	$num = mysql_num_rows($sqlase);
	if($num > 0)
	{
		while($resultado = mysql_fetch_assoc($sqlase)){
			
			$ase = $resultado["comentarios"];
			$datosAse = explode('-',$ase);
			
		}
	}
	return $datosAse; 
	
}

/*
 $comentariosDetF = comentariosDetF($tbwbasedato,$tbwbasedatonum,$key);
					 $Tab_ase = $comentariosDetF[1];
					 $campo1= $comentariosDetF[2];
					 $campo2 = $comentariosDetF[3];
*/


function seleccionarCdet($campo1,$Tab_ase,$tbwbasedato){
	
	$NCA = '';
	 $sql = "SELECT descripcion
			  FROM det_formulario
			  WHERE medico = '".$tbwbasedato."'
			  AND codigo = '".$Tab_ase."'
			  AND campo = '".$campo1."'";
			  //AND campo = '".$campo2."'";
			  
	$sqlaseC = mysql_query($sql);
	$num = mysql_num_rows($sqlaseC);
	if($num > 0)
	{
		while($resultado = mysql_fetch_assoc($sqlaseC)){
				$NCA = $resultado["descripcion"];
		}		
	}
	return $NCA;
	
}

//$Detase = seleccionarCdet($campo1,$Tab_ase,$tbwbasedato);
//$Detase;


function seleccionarCdet2($campo2,$Tab_ase,$tbwbasedato){
	
	$NCA2 = '';
	 $sql2 = "SELECT descripcion
			  FROM det_formulario
			  WHERE medico = '".$tbwbasedato."'
			  AND codigo = '".$Tab_ase."'
			  AND campo = '".$campo2."'";
			  
	$sqlaseC = mysql_query($sql2);
	$num = mysql_num_rows($sqlaseC);
	if($num > 0)
	{
		while($resultado = mysql_fetch_assoc($sqlaseC)){
			$NCA2 = $resultado["descripcion"];
		}
	}
	return $NCA2;
	
}

//$Detase2 = seleccionarCdet2($campo2,$Tab_ase,$tbwbasedato);
//$Detase2;


//$DatosAseguradora = $Detase.",".$Detase2;
//$DatosAseguradora;





function seleccionarAse($DatosAseguradora,$Tab_ase,$var){
	
	$aseguradora = '';
	  $sql = "	 SELECT CONCAT(".$DatosAseguradora.")
		           FROM citaslc_".$Tab_ase." 
		          WHERE drvncl = '".$var."'";
				   
	$sqlase = mysql_query($sql);
	$num = mysql_num_rows($sqlase);
	
		if($num > 0)
		{
			$aseguradora = '';
			
			while($resultado = mysql_fetch_assoc($sqlase)){
				
				foreach($resultado as $key => $value){
	
					$aseguradora .= $value;
					
				}	
			}
		}	
	
return $aseguradora;
	
	//SELECT concat( drvncl,'-', drvanm ) FROM `citaslc_000033` WHERE drvncl = '099'
	
	
}

//$mostrarAse = seleccionarAse($DatosAseguradora,$Tab_ase,'099');
//$mostrarAse;
	



function TipoPago($tbwbasedato,$tbwbasedatonum,$campov)
{
	$val = false;
	
			$sql = "SELECT descripcion
		           FROM det_formulario 
		          WHERE medico = '".$tbwbasedato."'
				    AND codigo = '".$tbwbasedatonum."'
			        AND descripcion = '".$campov."'
			        AND tipo = '10'";
					
	
	$sqlase = mysql_query( $sql );
	$num = mysql_num_rows($sqlase);
	if($num > 0)
	{
		$val = true;
		
	}
	return $val; 
	
}




if (isset($accion) && $accion == 'listar')
{
	
	$columnas_titulo = 8;	
	
	$dato = array();
	$resp = "<div align='center' id='tabla' >";
	$filtroCedula = '%';

	if(isset($documento) && $documento != '')
	{
		$filtroCedula = $documento;
	}
	
	//se consulta las citas dentro de un rango
	if ($caso == 1 or $caso == 3)
	{
		$query = "select Fecha_data, Cod_equ, Cod_exa, fecha, nom_pac, cedula, usuario, Comentarios as Comentario, id as id, TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as hi
				  from ".$wbasedato."_000001 
				  where Fecha between '".@$wfecini."' and '".@$wfecfin."' 
				  and cod_equ like '".$filtro."'
				  AND Cedula LIKE '".$filtroCedula."'
				  and activo = 'A'  
				  Order by Fecha,hi";

	}
	else if ($caso == 2 and $valCitas!='on') 
	{
		$query = "select Fecha_data, Cod_equ, Cod_exa, fecha, nom_pac, cedula, usuario, Comentario, id as id, TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as hi 
				  from ".$wbasedato."_000009 
				  where Fecha between '".@$wfecini."' and '".@$wfecfin."'
				  and cod_equ like '".$filtro."'
				  AND Cedula LIKE '".$filtroCedula."'
				  AND activo = 'A'
				  Order by Fecha,hi";
	
	}
	else if ($caso == 2 and $valCitas=='on') 
	{
		$query = "select a.Fecha_data, Cod_equ, Cod_exa, fecha, nom_pac, cedula, usuario, Comentario, a.id as id, TIME_FORMAT( CONCAT(hi,'00'), '%H:%i') as hi 
				  from ".$wbasedato."_000009 a ,".$wbasedato1."_000051 b
				  where a.Fecha between '".@$wfecini."' and '".@$wfecfin."'
				  and b.medcod like '".$filtro."'
				  AND Cedula LIKE '".$filtroCedula."'
				  and b.medcid = a.cod_equ
				  AND a.activo = 'A'
				  Order by a.Fecha,hi";
	}
	
	$res = mysql_query($query,$conex)or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
	$num = mysql_num_rows($res);
	
	$trs = '';
	$i = 0;
	if ($num > 0)
	{	

		while($row = mysql_fetch_assoc($res)){
				$colorf = '';
				$i % 2 == 0 ? $colorf = "fila1" : $colorf = "fila2";
				$fecha_data =$row['Fecha_data'];
				$cod_exa    =$row['Cod_exa'];
				$fecha      =$row['fecha'];
				$nom_pac    =utf8_encode($row['nom_pac']);
				$cedula     =$row['cedula'];
				$usuario    =$row['usuario'];
				$cod_equ    =$row['Cod_equ']; //medico
				$comentario =utf8_encode($row['Comentario']);
				$hora       =$row['hi'];
				$id         =$row['id'];
				
				$titulo_comentario = '';
				//Si la variable $mostrar_obser == true, es porque se selecciono el cajon de mostrar comentarios, entonces mostrara una columna adicional con el dato.
				if($mostrar_obser == 'true' and trim($comentario) != ''){					
					
					//Se imprime un div para cada comentario, asi al seleccionar Ver se mostrara el que este relacionado.
					$div_comentario = "<div class='modal_comentario_".$id."' style='display:none;' title='Comentario en la cita de ".$nom_pac." - ".$cedula."'>		
										<table border='0' width=400px>
										  <tbody>
											<tr>
											  <td align=left>".$comentario."</td>								  
										  </tbody>
										</table>					
									  </div>";	
					$dato_comentario = "<td align=center><a href='javascript:' onclick='ver_comentario(\"$id\");' class='tooltip' title='".$comentario."'>Ver ".$div_comentario."</a></td>";
					
				}else{
				//Si no hay comentarios imprime vacio.
				$dato_comentario = "<td align='center'></td>";
				}
				
				//se consulta la descripcion del examen y el medico o el equipo
				if ($caso == 1 or $caso == 3)
				{
					$query1 = "select a.Descripcion as examen, b.Descripcion as equipo
							   from ".$wbasedato."_000006 a, ".$wbasedato."_000003 b 
						       where a.Codigo = '".$cod_exa."' 
							   and b.Codigo = '".$cod_equ."' 
							   and a.Cod_equipo = b.Codigo";
				    $med="Equipo";			   
				}
				else
				{
					$query1 = "select a.Descripcion as examen, b.Descripcion as equipo
							   from ".$wbasedato."_000011 a, ".$wbasedato."_000010 b 
							   where a.Codigo = '".$cod_exa."' 
							   and b.Codigo = '".$cod_equ."' 
							   and a.Cod_equipo = b.Codigo";
				    $med="Medico"; 
				}
				$err1 = mysql_query($query1,$conex)or die( mysql_errno()." - Error en el query $query1 - ".mysql_error() );
				$row1 = mysql_fetch_array($err1);
				$especialidad=$row1['examen'];
				$equiMed=$row1['equipo'];
				 
				
				 
				
				 if($tbwbasedato == $wbasedato && $mostrar_obser == 'true')
				 {
					$columnas_titulo = 18;
					
					$ResultadoTabla = SeleccionaCitaslc($OC,$tb,$cedula,$fecha,$hora);
					$NC = AdiccionarCitas($datosAdicc);
					$NCE = AdiccionarCitasEspeciales($NombreCE);
					$id =SeleccionaCitaslce($OC,$tb,$cedula,$fecha,$hora);
					
					
					if($id != ''){
						
					$consExamen = str_replace("abcd",$id,$datosE);	
					$espe = especial($consExamen);
					$Ex ="	<table border='0' width=400px>
								<tbody>
									<td width='19%' align='center'>".$espe."</td>							  
								</tbody>
							</table>";
					}
					
					
					
					
					$trs .=  "
						<tr class='".$colorf."' >
						<td align=center>".$fecha_data."</td>
						<td align=center>".utf8_encode($equiMed)."</td>
						<td align=center>".utf8_encode($cod_exa)."-".utf8_encode($especialidad)."</td>
						<td align=center>".$hora."</td>
						<td align=center>".$fecha."</td>
						<td>".utf8_encode($nom_pac)."</td>
						<td>".utf8_encode($cedula)."</td>
						<td>".utf8_encode($usuario)."</td>
						".utf8_encode($ResultadoTabla)."
						<td >".utf8_encode($Ex)."</td>
						".utf8_encode($dato_comentario)."
						</tr>";						
					 

				 }elseif($tbwbasedato == $wbasedato)
				 {
					 
					 $ResultadoTabla = SeleccionaCitaslc($OC,$tb,$cedula,$fecha,$hora);
					 $NCE = AdiccionarCitasEspeciales($NombreCE);
					 $NC = AdiccionarCitas($datosAdicc);
					 $id =SeleccionaCitaslce($OC,$tb,$cedula,$fecha,$hora);

					if($id != ''){
					
					
					$consExamen = str_replace("abcd",$id,$datosE);	
					$espe = especial($consExamen);
					$Ex ="	<table border='0' width=400px>
								<tbody>
									<td width='19%' align='center'>".$espe."</td>							  
								</tbody>
							</table>";
					}
					 $columnas_titulo = 18;
					 $trs .=  "
						<tr class='".$colorf."' >
						<td align=center>".$fecha_data."</td>
						<td align=center>".utf8_encode($equiMed)."</td>
						<td align=center>".utf8_encode($cod_exa)."-".utf8_encode($especialidad)."</td>
						<td align=center>".$hora."</td>
						<td align=center>".$fecha."</td>
						<td>".utf8_encode($nom_pac)."</td>
						<td>".utf8_encode($cedula)."</td>
						<td>".utf8_encode($usuario)."</td>					
						".utf8_encode($ResultadoTabla)."
						<td width = 200px >".utf8_encode($Ex)."</td>
						</tr>";		
					 
				 }
				 
				 
				if ($mostrar_obser == 'true' && $tbwbasedato != $wbasedato){
		        
					$trs .=  "
						<tr class='".$colorf."' >
						<td align=center>".$fecha_data."</td>
						<td align=center>".utf8_encode($equiMed)."</td>
						<td align=center>".utf8_encode($cod_exa)."-".utf8_encode($especialidad)."</td>
						<td align=center>".$hora."</td>
						<td align=center>".$fecha."</td>
						<td>".utf8_encode($nom_pac)."</td>
						<td>".utf8_encode($cedula)."</td>
						<td>".utf8_encode($usuario)."</td>							
						".utf8_encode($dato_comentario)."
						</tr>";
				}
				elseif($tbwbasedato != $wbasedato){

						$trs .=  "
						<tr class='".$colorf."' >
						<td align=center>".$fecha_data."</td>
						<td align=center>".utf8_encode($equiMed)."</td>
						<td align=center>".utf8_encode($cod_exa)."-".utf8_encode($especialidad)."</td>
						<td align=center>".$hora."</td>
						<td align=center>".$fecha."</td>
						<td>".utf8_encode($nom_pac)."</td>
						<td>".utf8_encode($cedula)."</td>
						<td>".utf8_encode($usuario)."</td>
						</tr>";					
				}		
							
		}
	 }else
	 {
		$trs = "<tr><td colspan='8%' align='center' class='fila2'>No se encontraron registros en ese rango de fechas</td></tr>";
	 }
		 
	$resp .= "<table border='0' align='center'>";
	if ($num>0)
	{
        // Cuando escoga ver comentarios se agrega una columna para las tablas diferentes que hay en root_000132
		if ($mostrar_obser == 'true' && $tbwbasedato != $wbasedato)
		{
			$columnas_titulo = 9; //Si no selecciona el cajon de mostrar comentarios seran solamente 8 columnas en el encabezado.
			$titulo_comentario = "<td width='8%'>Comentarios</td>";
			
		}elseif($tbwbasedato == $wbasedato && $mostrar_obser == 'true')
		{
			//Si no selecciona el cajon de mostrar comentarios seran solamente 8 para las tabals diferentes que hay en root_000132
			$columnas_titulo = 18;//pero con la condicion se hacen los 13 campos 
			$titulo_comentario = "<td width='10%'>Comentarios</td>";
		
		}
		
		
        // Adicionar Titulo
		$resp .= "<th class='encabezadotabla' colspan='".$columnas_titulo."'>Citas asignadas entre:".@$wfecini." y ".@$wfecfin."</th>";
		$resp .= "<tr class='encabezadotabla' align='center'>";
		$resp .= "<td width='15%' >Fecha asignacion cita</td><td width='10%'>".$med."</td><td width='10%'>Especialidad</td><td width='4%'>Hora</td><td width='8%'>Fecha cita</td><td width='18%'>Nombre Paciente</td><td width='4%'>Cedula</td><td width='8%'>Usuario</td>".utf8_encode($NC)."".$NCE."</td>.$titulo_comentario";
	}
	$resp .= $trs;	
	$resp .= "</table>";
	$resp .= "</div>";
	

	$dato['div'] = $resp;
	echo json_encode($dato);				
	return;
}



?>
<html>
<head>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/modernizr.custom.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type='text/javascript' src='../../../include/root/jquery.tooltip.js'></script>
<script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
<link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
<title>Reporte de Citas Asignadas</title>
<style type="text/css">
BODY{
	width: 100%!important;
	margin: 0;
}
</style>
</head>
<BODY>
<script type="text/javascript">

 function ver_comentario(id){
 
 $(".modal_comentario_"+id).dialog({
				show: {
				effect: "blind",
				duration: 100
				},
				hide: {
				effect: "blind",
				duration: 100
				},
				autoOpen: false,
				// maxHeight:600,
				height:'auto',				
				width: 'auto',
				dialogClass: 'fixed-dialog',
				modal: true,
				title: "Comentario"/*,
				open:function(){
				var s = $('#cont_dlle_modal').height();
				var s2 = $(this).dialog( "option", "maxHeight" );
				if(s < s2){
				$(this).height(s);
				}
				}*/
				});
				
 $(".modal_comentario_"+id).dialog( "open" );
 
 }

 function enviar()
 {
 
	$.blockUI({ message:	'Espere...',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });
	
	var mostrar_obser = $('#mostrar_obser').is(':checked');
	
	$.post("repCitasAsig.php",
		{
			wemp_pmla:      $('#wemp_pmla').val(),
			consultaAjax:   '',
			wfecini:		$('#wfecini').val(),
			wfecfin:		$('#wfecfin').val(),
			wbasedato:      $('#wbasedato').val(),
			caso:      		$('#caso').val(),
			valCitas:       $('#valCitas').val(),
			filtro:         $('#slDoctor').val(),
			wbasedato1:     $('#wbasedato1').val(),
			documento:		$('#identificacion').val(),
			mostrar_obser:	mostrar_obser,
			accion:			'listar'
		}
		,function(data) {
			    //alert(data.div);
				$("#tabla").html(data.div);
				document.getElementById('tabla').style.visibility = 'visible';
				$('.tooltip').tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });			
				
			$.unblockUI();
		},"json"
	).done(function(){	 });
	
		
 }


<!--

	
//-->
</script>
<?php
/*****************************************************************************************************************
Modificaciones:
2020-11-5 David Henao Hernandez - Se modifica el reporte de citas asignadas para citas de laboratorio , creando una nueva tabla root_000132 
para dicho manejo. 
-Se agregaron al reporte los campos de aseguradora, pago confirmado , examenes , direccion, con esta nueva tabla el reporte 
se hizo dinamico y ya para cualquier area es posible agregarle campos a su reporte de citas.

* 2018-07-12 (Juan Felipe Balcero): Se agrega un filtro para realizar búsquedas por número de documento
* 2014-03-05 (Jonatan Lopez): Se agrega la columna de comentarios, si el usuario 
* selecciona el cajon para verlas, estas se podran ver en un tooltip y en una ventana modal.

* 2012-09-20 (Viviana Rodas): Se crea el reporte de citas asignadas, 
* para obtener la informacion de citas asignadas con sus respectivas fechas de
* asignacion, especialidad, fecha para la que se asigno la cita, nombre del paciente,
* cedula y el usuario que asigno la cita.

 Modificaciones:
 2020-02-12 Arleyda Insignares C. Se modifica el query de citas para que el rango de fecha filtre la fecha de la cita.
 2013-04-03 Se modifica el programa para que se pueda utilizar en todas las unidades de citas.Viviana Rodas
 2012-09-25 Se agrega la consulta a la tabla citascs_000011 para colocar la descripcion del examen.Viviana Rodas
********************************************************************************************************************/

/*Funcion para el select de medicos o equipos*/
function selecMedEqu()
{
	global $caso;
	global $valCitas;
	global $wbasedato;
	global $wbasedato1;
	
	if ($caso == 2 and $valCitas=="on")
	{
		$sql = "SELECT Mednom, Medcod
				FROM ".$wbasedato1."_000051
				WHERE Medcid != ''
			    AND Medest = 'on'
		        ORDER BY Mednom";
	}
	else if ($caso == 3 or $caso == 1)
	{
		$sql = "select codigo,descripcion,uni_hora,hi,hf,activo 
				from ".$wbasedato."_000003 
				where activo='A' ";
	}
	
	else if ($caso == 2 and $valCitas!="on")
	{
		$sql = "select codigo,descripcion,uni_hora,hi,hf,activo 
				from ".$wbasedato."_000010 
				where activo='A' 
				group by descripcion 
				order by descripcion";
	}
				
	$res1 = mysql_query( $sql ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
 	//$rows = mysql_fetch_array( $res1 );
	return $res1;
}

 
session_start();
if(!isset($_SESSION['user']))
	echo "Error Usuario NO Registrado";
else
{	
echo "<form name='reporte' method=post>";  
    $institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
    $wbasedato1 = strtolower( $institucion->baseDeDatos );
	
	//Buscando el doctor por el que fue filtrado
	if( !isset( $slDoctor ) ){
		$nmFiltro = "% - Todos";
		$filtro = '%';
		$slDoctor = "% - Todos";
	}
	else{
		$nmFiltro = $slDoctor;
		$exp = explode( " - ", $slDoctor);
		$filtro = $exp[0];
	}
	if (!isset($valCitas))
	{
		$valCitas = "off";
	}
	
	echo "<input type='hidden' id='wemp_pmla name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='hidden' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
	echo "<input type='hidden' id='caso' name='caso' value='".$caso."'>";
	echo "<input type='hidden' id='valCitas' name='valCitas' value='".$valCitas."'>";
	echo "<input type='hidden' id='wbasedato1' name='wbasedato1' value='".$wbasedato1."'>";

	$wactualiz="2020-02-12";
	 
	if ($wemp_pmla == 01)
	{
		encabezado("REPORTE DE CITAS ASIGNADAS", $wactualiz, $wbasedato1 );
	}
	else
	{
		encabezado("REPORTE DE CITAS ASIGNADAS", $wactualiz, "logo_".$wbasedato1 );
	}
	
	 //rango de fecha para mirar el reporte de citas asignadas***
	echo "<div align='center' id='fecha'><br />";
	echo "<form name='reporte'  method='post' action='' >";
	echo "<table>";
	echo "<tr>";
	echo "<th colspan='2' class='encabezadotabla' align=center valign='top'>Seleccione el rango de fechas</th>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='fila1' align=center valign='top'>Fecha Inicial</td>";
	echo "<td class='fila2' align='left'>";
		if(isset($wfecini) && !empty($wfecini))
		{
			campoFechaDefecto("wfecini",$wfecini);
		} else 
		{
			campoFecha("wfecini");
		}
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td class='fila1' align=center valign='top'>Fecha Final</td>";
	echo "<td class='fila2' align='left'>";
		if(isset($wfecfin) && !empty($wfecfin))
		{
			campoFechaDefecto("wfecfin",$wfecfin);
		} else 
		{
			campoFecha("wfecfin");
		}
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
	if ($caso == 2)
	{		
		echo "	<td class='fila1' align=center>Filtro por Profesional</td>";
	}
	else
	{
		echo "	<td class='fila1' align=center>Filtro por Equipo</td>";
	}
	$res1=selecMedEqu();	
	echo "	<td class='fila2'><select name='slDoctor' id='slDoctor' onchange=''>";
	echo "	<option value='%'>% - Todos</option>";
	
	for( $i = 0; $rows = mysql_fetch_array( $res1 ); $i++ ){
	
		if ($caso == 2 and $valCitas=="on")
		{

			$rows['Medcod'] = trim( $rows['Medcod'] );
			$rows['Mednom'] = trim( $rows['Mednom'] );
			
			if( $slDoctor != trim( $rows['Medcod'] )." - ".trim( $rows['Mednom'] ) )
			{
				echo "<option value='{$rows['Medcod']}'>{$rows['Medcod']} - {$rows['Mednom']}</option>";
			}
			else
			{
				echo "<option value='{$rows['Medcod']}' selected>{$rows['Medcod']} - {$rows['Mednom']}</option>";
			}
		}
		else if ($caso == 1 or $caso == 3)
		{
			$rows['codigo'] = trim( $rows['codigo'] );
			$rows['descripcion'] = trim( $rows['descripcion'] );
			
			if( $slDoctor != trim( $rows['codigo'] )." - ".trim( $rows['descripcion'] ) )
			{
				echo "<option value='{$rows['codigo']}'>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
			else
			{
				echo "<option value='{$rows['codigo']}' selected>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
		}
		else if ($caso == 2 and $valCitas!= "on")
		{
			
			$rows['codigo'] = trim( $rows['codigo'] );
			$rows['descripcion'] = trim( $rows['descripcion'] );
			
			if( $slDoctor != trim( $rows['codigo'] )." - ".trim( $rows['descripcion'] ) )
			
			{
				echo "<option value='{$rows['codigo']}'>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
			else
			{
				echo "<option value='{$rows['codigo']}' selected>{$rows['codigo']} - {$rows['descripcion']}</option>";
			}
		}
		
	}//for
	
	echo "</select>";
	echo"</td>";		
	echo "</tr>";
	echo "<tr><td class=fila1 align=center>Filtro por documento</td><td class=fila2><input type='text'  id='identificacion' name='documento'></td></tr>";
	echo "<tr><td class=fila1>¿Desea ver los comentarios?:</td><td class=fila2><input type=checkbox id='mostrar_obser'></td></tr>";
	echo "<tr>";
	echo "<td colspan='2' align='center' class='fila2'><input type='button' value='Enviar' style='width:100' onclick='enviar();'></td>";
	echo "</tr>";
	echo "</table>";	
	echo "</div>";
	echo "<br><br>";
		
	echo "<div id='tabla' name='tabla'>";
	echo "</div>";
	echo "<br><br><center><input name='button' type='button' style='width:100' onclick='window.close();' value='Cerrar' />";
	echo "</form>";
	echo "</body>";
	echo "</html>";
		
}
?>