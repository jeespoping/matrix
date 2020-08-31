<html>
<head>
<title>REPORTE PACIENTES EGRESADOS Y ACTIVOS</title>
<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
<script type="text/javascript">

function retornar(wemp_pmla,wfecha_i,wfecha_f,bandera,wcco0)
	{
		location.href = "rep_pruebaEgreso.php?wemp_pmla="+wemp_pmla+"&wfecha_i="+wfecha_i+"&wfecha_f="+wfecha_f+"&bandera="+bandera+"&wcco0="+wcco0;
    }

function cerrar_ventana(cant_inic)
	{
		window.close();
    }

function enter()
	{
	 document.historias.submit();
	}	
	
	
function ejecutar(path)
	{
		window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=1,scrollbars=1,titlebar=0');
	}	

</script>


<?php
include_once("conex.php");
/***************************************************
 PROGRAMA                   : rep_pruebaEgreso.php
 AUTOR                      : Frederick Aguirre.
 FECHA CREACION             : 22 de octubre de 2012

 DESCRIPCION:
 Muestra los pacientes que ingresan o egresan para un servicio seleccionado o todos.

 CAMBIOS:
		2013-12-09: (Frederick Aguirre)  Se actualiza la URL para ver la historia clinica electrónica
		2013-02-21: (Frederick Aguirre)  Para la consulta de egresos se agrega la condicion si es 1179 que no cuente los ambulatorios (ubihac == '')
*////////////////////

if(!isset($_SESSION['user'])){
echo "error";
return;
}




include_once("root/comun.php");


$wactualiz = "2014-07-08";
$conex = obtenerConexionBD("matrix");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
$whce =  consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
echo "</head>";
echo '<body BGCOLOR="" TEXT="#000000">';
 

function ConsultaEgreso2($wfecha_i,$wfecha_f,$wcco0){
	global $wemp_pmla;
	global $conex;
	global $wbasedato;
	global $whce;
	
	$centros_de_costo = array();
	
	$wtablacliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	
	$query_cco = "SELECT ccocod, cconom FROM ".$wbasedato."_000011 ";
	$rescco= mysql_query($query_cco, $conex);
	while ($row_cco = mysql_fetch_assoc($rescco)){
		if ( array_key_exists( $row_cco['ccocod'], $centros_de_costo) == false ) 
					$centros_de_costo[ $row_cco['ccocod'] ] = $row_cco['cconom'];
	}				
	
	echo "<center class=titulo >PACIENTES EGRESADOS </center>";
	echo "<br/>";
	$wcco1 = explode("-",$wcco0);
		
	$q = " SELECT 	A.Pacno1, A.Pacno2, A.Pacap1, A.Pacap2, A.Pactid, A.Pacced, F.servicio as ccocod, G.cconom,"
		."          F.Historia_clinica as historia, F.Num_ingreso as ingreso, F.Fecha_egre_serv as fecha_egreso,"
		."          F.Hora_egr_serv as hora_egreso, F.Tipo_egre_serv as tipo_egreso, F.Num_ing_serv "	
		."			,UB.Fecha_data as fecha_ingreso, UB.Hora_data as hora_ingreso, UB.Ubisac, UB.Ubihac "
		."   FROM  	".$wbasedato."_000033 F, ".$wbasedato."_000011 G,  root_000036 A, root_000037 B, ".$wbasedato."_000018 UB "
		."  WHERE 	F.Historia_clinica = B.Orihis " 
		."    AND 	F.Fecha_egre_serv BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' "
		."    AND   F.Historia_clinica = UB.Ubihis "
		."    AND   F.Num_ingreso = UB.Ubiing "	
		."    AND 	F.Servicio = G.Ccocod "
		."    AND 	A.Pacced = B.Oriced "
	    ."	  AND 	A.Pactid = B.Oritid "         
	    ."    AND 	B.Oriori = '".$wemp_pmla."'";	
				
	if($wcco0 != "todos") {
		$q.=" AND 	( F.Servicio = '".trim($wcco1[0])."'  ";
		$q.=" OR 	F.Tipo_egre_serv = '".trim($wcco1[0])."'  )";		
	}
	if( $wcco0 != "todos" && trim($wcco1[0]) == '1179' ){
		$q.= " AND   UB.Ubihac != '' ";
	}
		
	$q.=" ORDER BY ccocod, fecha_egreso, hora_egreso";
	
	echo "<br/>";
	echo "<table align=center>";

	$cco_mostrado = "";
	$i=0;
	$wtotal = 0;
	$wgtotal = 0;	
	$res= mysql_query($q, $conex);

	$pacientes_repetidos = array();
	 
	while($row = mysql_fetch_assoc($res)) {
		$seguir = true;
		
		if( $row['ccocod'] == '1179'  && $row['Ubihac'] == '' ){
			continue;
		}
		
		if( trim($row['ccocod']) != trim($wcco1[0]) and is_numeric($row['tipo_egreso']) == true  and trim($wcco0) != "todos"){
			array_push( $pacientes_repetidos, $row['tipo_egreso']."-".$row['historia']."-".$row['ingreso']."-".$row['ccocod']."-".$row['Num_ing_serv']);
			$seguir = false;
		}
		
		if(trim($wcco0) == "todos" and is_numeric($row['tipo_egreso']) == true ){
			array_push( $pacientes_repetidos, $row['tipo_egreso']."-".$row['historia']."-".$row['ingreso']."-".$row['ccocod']."-".$row['Num_ing_serv']);
			$seguir = true;
		}

		if( $seguir == true ){
			if($cco_mostrado != $row['ccocod'] ){
				if( $i != 0){
					echo "<tr class='encabezadoTabla'><td colspan='10' align=right> Total Servicios : ".$wtotal."</td></tr>";	 
					echo "<tr><td colspan=10>&nbsp;</td></tr>";
				}
				echo "<tr class=titulo><td colspan=10>";
				echo $row['ccocod']." - ".$row["cconom"];
				echo "</td></tr>";
				echo "<tr class=encabezadoTabla>";
				echo "<td align=center>Historia</td>";
				echo "<td align=center>Ingreso</td>";
				echo "<td align=center>Paciente</td>";
				echo "<td align=center>Servicio <br/> Ingreso</td>";
				echo "<td align=center>Fecha de<br/> Ingreso</td>";
				echo "<td align=center>Hora<br/>de Ingreso</td>";
				echo "<td align=center>Fecha<br/>de Egreso</td>";
				echo "<td align=center>Hora <br/>de Egreso</td>";
				echo "<td align=center>Motivo Egreso</td>";
				echo "<td align=center>&nbsp;</td>";
				echo "</tr>";
				$cco_mostrado = $row['ccocod'];
				$wtotal = 0;
			}
			
			$and_ing = "";
			$indi = 0;
			$indi2 = 0;
			foreach ($pacientes_repetidos as $clave){
				if( preg_match("/^".$row['ccocod']."-".$row['historia']."-".$row['ingreso']."/", $clave) ){
					$datos = explode("-",$clave);
					$and_ing.=" AND Num_ing_serv = ".$datos[4]." AND Procedencia='".$datos[3]."'";
					$indi2 = $indi;
				}
				$indi++;
			}		
			$query_ingreso_32 = "  SELECT 	A.Fecha_ing as fecha_ingreso, A.Hora_ing as hora_ingreso, B.Cconom as cco_ingreso"	 	
								."   FROM  	".$wbasedato."_000032 A, ".$wbasedato."_000011 B"
								."  WHERE 	A.Historia_clinica = '".$row['historia']."'"
								." 	  AND	A.Num_ingreso = '".$row['ingreso']."'"
								."    AND 	A.Servicio = '".$row['ccocod']."'"
								."    AND 	A.Procedencia = B.Ccocod ";
								
			if ( ! empty( $and_ing ) ){
				$pacientes_repetidos[$indi2] = "";
				$query_ingreso_32.=$and_ing;
			}
								
			$res32= mysql_query($query_ingreso_32, $conex);
			$num_ing = mysql_num_rows($res32);
			if( $num_ing > 0 ){
				$row_ing = mysql_fetch_assoc($res32);
			}else{
				$row_ing['fecha_ingreso'] = $row['fecha_ingreso'];
				$row_ing['hora_ingreso'] = $row['hora_ingreso'];
				$row_ing['cco_ingreso'] = $centros_de_costo[ $row['Ubisac'] ];
			}
			
			
			if( is_numeric( $row["tipo_egreso"] )){
				$row["tipo_egreso"] = $centros_de_costo[ $row["tipo_egreso"] ];
			}
			
			if($i % 2 == 0)
				$wclass="fila1";
			else
				$wclass="fila2";
				
			$wtotal++;
			$wgtotal++;
			$i++;
			
			$row_ing['hora_ingreso']= substr_replace( $row_ing['hora_ingreso'] ,"",-3 );
			$row['hora_egreso']= substr_replace( $row['hora_egreso'] ,"",-3 );

			echo "<tr class=".$wclass.">";
			echo "<td align=center>".$row["historia"]."</td>"; //historia
			echo "<td align=center>".$row["ingreso"]."</td>"; //ingreso
			echo "<td align=left nowrap='nowrap'>".$row["Pacno1"]." ".$row["Pacno2"]." ".$row["Pacap1"]." ".$row["Pacap2"]."</td>"; //paciente
			echo "<td align=center>".$row_ing['cco_ingreso']."</td>"; //servicio ingreso
			echo "<td align=center nowrap='nowrap'>".$row_ing['fecha_ingreso']."</td>"; //fecha ingreso
			echo "<td align=center>".$row_ing['hora_ingreso']."</td>"; //hora ingreso
			
			echo "<td align=center nowrap='nowrap'><b>".$row["fecha_egreso"]."</b></td>";	//fecha egreso
			echo "<td align=center><b>".$row["hora_egreso"]."</b></td>";	//hora egreso
			echo "<td align=center nowrap='nowrap'>".$row["tipo_egreso"]."</td>";	//tipo egreso
			
			$wpos_Alta   = stripos($row["tipo_egreso"],"ALTA");
			$wpos_Muerte = stripos($row["tipo_egreso"],"MUERTE");

			$title_btn = "";
			$color = "";
			if ($wpos_Alta === false and $wpos_Muerte === false){
				$title_btn = "Ver HCE";
				$path = "/matrix/HCE/procesos/HCE_iFrames.php?accion=M&ok=0&empresa=".$whce."&wcedula=".$row["Pacced"]."&wtipodoc=".$row["Pactid"]."&origen=".$wemp_pmla."&wdbmhos=".$wbasedato;
			}else {
				//$path = "/matrix/hce/procesos/TableroAnt.php?empresa=".$wbasedato."&codemp=".$wemp_pmla."&historia=hce&accion=I&whis=".$row["historia"];
				$path = "/matrix/admisiones/procesos/egreso_erp.php?wemp_pmla=".$wemp_pmla."&documento=".$row['Pacced']."&wtipodoc=".$row['Pactid']."&historia=".$row['historia']."&ingreso=".$row['ingreso'];
				$title_btn = "Egresar";
				//Si ya existe el egreso, cambia el path
				$qEg = "SELECT id
						  FROM ".$wtablacliame."_000108
						 WHERE Egrhis='".$row["historia"]."'
						   AND Egring='".$row["ingreso"]."'
						   AND Egract='on'";
				$res2 = mysql_query( $qEg, $conex );
				if( $res2 ){
					$num2 = mysql_num_rows( $res2 );
					if( $num2 > 0 ){
						$color="color:gray;";
						$title_btn = "Egresado";
						$path = "/matrix/admisiones/procesos/egreso_erp.php?c_param=1&wemp_pmla=".$wemp_pmla."&documento=".$row['Pacced']."&wtipodoc=".$row['Pactid']."&historia=".$row['historia']."&ingreso=".$row['ingreso'];
					}
				}
			}
			
			echo "<td><A style='cursor:pointer; ".$color."' onClick='ejecutar(\"".$path."\")'><b>".$title_btn."</b></A></td>"; //enlace hce
			echo "</tr>";
		}
	 }
	 echo "<tr class='encabezadoTabla'><td colspan='10' align=right> Total Servicios : ".$wtotal."</td></tr>";	 
	 echo "<tr><td colspan=10>&nbsp;</td></tr>";
	 if($wgtotal == 0)
		$wgtotal=$wtotal;
	 echo "<tr class='encabezadoTabla'><td colspan='10' align=right>Gran Total de Servicios : ".$wgtotal."</td></tr>";	 		
	 echo "</table>";
	 echo "<br/>";	
}

function ConsultaIngreso2($wfecha_i,$wfecha_f,$wcco0){
	global $wemp_pmla;
	global $conex;
	global $wbasedato;
	global $whce;
	
	$wtablacliame = consultarAliasPorAplicacion($conex, $wemp_pmla, 'facturacion');
	
	echo "<center class=titulo >PACIENTES INGRESADOS </center>";
	echo "<br/>";
	$wcco1 = explode("-",$wcco0);
	
	$q = " SELECT 	A.Pacno1, A.Pacno2, A.Pacap1, A.Pacap2, A.Pactid, A.Pacced, F.servicio as ccocod, G.cconom,"
		."          F.Historia_clinica as historia, F.Num_ingreso as ingreso, F.Fecha_ing as fecha_ingreso,"
		."          F.Hora_ing as hora_ingreso, F.Procedencia"	 	
		."   FROM  	".$wbasedato."_000032 F, ".$wbasedato."_000011 G,  root_000036 A, root_000037 B "  
		."  WHERE 	F.Historia_clinica = B.Orihis " 
		."    AND 	F.Fecha_ing BETWEEN '".$wfecha_i."' AND '".$wfecha_f."' "
		."    AND 	F.Servicio = G.Ccocod "
		."    AND 	A.Pacced = B.Oriced "
	    ."	  AND 	A.Pactid = B.Oritid "         
	    ."    AND 	B.Oriori = '".$wemp_pmla."'";
		
		if($wcco0!= "todos") {
			$q.=" AND 	F.Servicio = '".$wcco1[0]."'  ";
		}
		
		$q.=" ORDER BY ccocod, fecha_ingreso, hora_ingreso";
		echo "<br/>";
	 //IMPRIMIMOS LA TABLA POR CENTRO DE COSTO DE LOS PACIENTES EGRESADOS
	 echo "<table align=center>";

	$cco_mostrado = "";
	 $i=0;
	 $wtotal = 0;
	 $wgtotal = 0;	
	 $res= mysql_query($q, $conex);
	 
	 
	while($row = mysql_fetch_assoc($res)) {

		if($cco_mostrado != $row['ccocod'] ){
			if( $i != 0){
				echo "<tr class='encabezadoTabla'><td colspan='10' align=right> Total Servicios : ".$wtotal."</td></tr>";	 
				echo "<tr><td colspan=10>&nbsp;</td></tr>";
			}
			echo "<tr class=titulo><td colspan=7>";
			echo $row['ccocod']." - ".$row["cconom"];
			echo "</td></tr>";
			echo "<tr class=encabezadoTabla>";
			echo "<td align=center>Historia</td>";
			echo "<td align=center>Ingreso</td>";
			echo "<td align=center>Paciente</td>";
			echo "<td align=center>Servicio <br/> Ingreso</td>";
			echo "<td align=center >Fecha de<br/> Ingreso</td>";
			echo "<td align=center >Hora<br/>de Ingreso</td>";
			echo "<td align=center>&nbsp;</td>";
			echo "</tr>"; 
			$cco_mostrado = $row['ccocod'];
			$wtotal = 0;
		}
		

		if($i % 2 == 0)
			$wclass="fila1";
		else
			$wclass="fila2";
			
		$wtotal++;
		$wgtotal++;
		$i++;
		
		$query_cco = "SELECT cconom FROM ".$wbasedato."_000011 WHERE Ccocod = '".$row["Procedencia"]."'";
		$rescco= mysql_query($query_cco, $conex);
		$row_cco = mysql_fetch_assoc($rescco);
		$row["procede"] = $row_cco['cconom'];
			
		
		$row['hora_ingreso']= substr_replace( $row['hora_ingreso'] ,"",-3 );

		echo "<tr class=".$wclass.">";
		echo "<td align=center>".$row["historia"]."</td>"; //historia
		echo "<td align=center>".$row["ingreso"]."</td>"; //ingreso
		echo "<td align=left nowrap='nowrap'>".$row["Pacno1"]." ".$row["Pacno2"]." ".$row["Pacap1"]." ".$row["Pacap2"]."</td>"; //paciente
		echo "<td align=center>".$row["procede"]."</td>"; //servicio ingreso
		echo "<td align=center nowrap='nowrap'>".$row['fecha_ingreso']."</td>"; //fecha ingreso
		echo "<td align=center>".$row['hora_ingreso']."</td>"; //hora ingreso

		// $path = "/matrix/HCE/procesos/HCE_iFrames.php?accion=M&ok=0&empresa=".$whce."&wcedula=".$row["Pacced"]."&wtipodoc=".$row["Pactid"];
		//."&documento=".$row['Pacced']."&wtipodoc=".$row['Pactid']."&historia=".$row['historia']."&ingreso=".$row['ingreso']
		/*$path = "/matrix/admisiones/procesos/egreso_erp.php?wemp_pmla=".$wemp_pmla."&documento=".$row['Pacced']."&wtipodoc=".$row['Pactid']."&historia=".$row['historia']."&ingreso=".$row['ingreso'];
		echo "<td><A onClick='ejecutar(\"".$path."\")'><b>Egresar</b></A></td>"; //enlace hce
		echo "</tr>";*/

		$title_btn = "Ver HCE";
		$path = "/matrix/HCE/procesos/HCE_iFrames.php?accion=M&ok=0&empresa=".$whce."&wcedula=".$row["Pacced"]."&wtipodoc=".$row["Pactid"]."&origen=".$wemp_pmla."&wdbmhos=".$wbasedato;
		
		echo "<td><A style='cursor:pointer;' onClick='ejecutar(\"".$path."\")'><b>".$title_btn."</b></A></td>"; //enlace hce
		echo "</tr>";
		
	 }
	 echo "<tr class='encabezadoTabla'><td colspan='10' align=right> Total Servicios : ".$wtotal."</td></tr>";	 
	 echo "<tr><td colspan=10>&nbsp;</td></tr>";
	 if($wgtotal == 0)
		$wgtotal=$wtotal;
	 echo "<tr class='encabezadoTabla'><td colspan='10' align=right>Gran Total de Servicios : ".$wgtotal."</td></tr>";	 		
	 echo "</table>";
	 echo "<br/>";	
}

 
function vistaInicial(){

	global $conex;
	global $whce;
	global $wbasedato;
	global $wemp_pmla;
	global $wactualiz;
	global $wfecha_i;
	global $wfecha_f;
	global $wcco0;
	global $bandera;
	global $tipo;
	global $conex;
	 $titulo = "Reporte Pacientes Egresados y Activos";
	 
	 encabezado($titulo, $wactualiz, "clinica");  
	 echo "<br/>";

	 echo "<form action='rep_pruebaEgreso.php' name='historias' method='post'>";
     if(!isset($wfecha_i ) or !isset($wfecha_i ) or !isset($wcco0) or isset($bandera) )
		{
			if(!isset($wfecha_i ) && !isset($wfecha_i ))
			   {
					$wfecha_i = date("Y-m-d");
					$wfecha_f = date("Y-m-d");
			   }
		
			$q = "( SELECT Ccocod, Cconom"
				."    FROM ".$wbasedato."_000011 G "
				."   WHERE Ccohos = 'on' ) "
				." UNION "
				."( SELECT Ccocod, Cconom"
				."    FROM ".$wbasedato."_000011 G "
				."   WHERE Ccoing = 'on' ) ";
			
			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
			$num = mysql_num_rows($res);
			
			echo "<center><table border=0>";
			echo "<tr><td class=fila1 align=center><b>Fecha Inicial</b></td>";
			echo "<td class=fila1 align=center colspan=3>";
			campoFechaDefecto("wfecha_i", $wfecha_i);
			echo "</td></tr>";
			echo "<tr><td class=fila1 align=center><b>Fecha Final</b></td>";
			echo "<td class=fila1 align=center colspan=3>";
			campoFechaDefecto("wfecha_f", $wfecha_f);
			echo "</td></tr>";
			echo "<tr><td colspan=3 class=fila1 align=center><b> Servicio</b></td></tr>";	
			echo "<tr><td colspan= 3 align =center class=fila1>";
			echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
			echo "<select name='wcco0' id='wcco0'>";
			echo "<option value ='todos'>todos</option>";
		
			for($i = 1; $i <= $num; $i++) 
			{
			 $row = mysql_fetch_array($res);
			 
			 if(isset($wcco0) && $row[0]==$wcco0)
				echo "<option selected value='" . $row[0] . " - " . $row[1] . "'>" . $row[0] . " - " . $row[1] . "</option>";
			 else
				echo "<option value='" . $row[0] . " - " . $row[1] . "'>" . $row[0] . " - " . $row[1] . "</option>";
			}
			
			echo "</select>";
			echo "</td></tr>";
			echo "<tr><td class = fila1  align= center><input type=radio name=tipo value=ingreso onclick='enter()' /> <b>Ingreso</b></td> <td class = fila1  align= center> <input type=radio name=tipo value=egreso onclick='enter()' /> <b>Egreso</b> </td></tr>";   
			echo "</table>";
			echo "</center>";
			echo "</form>";
			echo "<center><input type=button name ='btn_cerrar2' value='Cerrar Ventana' onclick='cerrarVentana()'></center>";	
		
		}
		else
		{
		
			echo "<center>";
			echo "<table border=0>";
			echo "<tr class=encabezadoTabla>";
			echo "<td align=center >Centro Costo </td>";
			echo "<td  class=fila1 align='left'>".$wcco0."</td>";
			echo "</tr>";
			echo "<tr class=encabezadoTabla>";
			echo "<td align=center >Fecha Inicial </td>";
			echo "<td  class=fila1 align='left'>".$wfecha_i."</td>";
			echo "</tr>";
			echo "<tr class=encabezadoTabla	>";
			echo "<td align=center >Fecha Final </td>";
			echo "<td  class=fila1 align='left'>".$wfecha_f."</td>";
			echo "</tr>";
			echo "</table>";
			echo "</center>";
			echo "<br/>";
			
			$wcco1 = explode("-",$wcco0);
			$bandera=1; 
			echo "<center><input type='button' name='btn_retornar2' value='Retornar' align='left' onclick='retornar(\"".$wemp_pmla."\",\"".$wfecha_i."\",\"".$wfecha_f."\",\"".$bandera."\", \"".$wcco1[0]."\")'/>&nbsp; &nbsp; <input type='button' align='right' name='btn_cerrar2' value='Cerrar' onclick='cerrar_ventana()'/></center>";
			echo "<br/>";
			 
			 if($tipo == "egreso")
				{
					ConsultaEgreso2($wfecha_i,$wfecha_f,$wcco0);
					
				}
			else 
				{
					ConsultaIngreso2($wfecha_i,$wfecha_f,$wcco0);
				}
			
			echo "<center><input type='button' name='btn_retornar2' value='Retornar' align='left' onclick='retornar(\"".$wemp_pmla."\",\"".$wfecha_i."\",\"".$wfecha_f."\",\"".$bandera."\", \"".$wcco1[0]."\")'/>&nbsp; &nbsp; <input type='button' align='right' name='btn_cerrar2' value='Cerrar' onclick='cerrar_ventana()'/></center>";
	
        }
}

vistaInicial();
?>
</body>
</html>
