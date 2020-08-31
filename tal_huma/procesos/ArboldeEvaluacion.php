<?php
include_once("conex.php");
include_once("root/comun.php");
include_once("funciones_talhuma.php");
$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);
$fecha =date("Y-m-d");
$hora = date("H:i:s"); 

if(isset($accion) && $accion == 'nuevo_cco')
{
$data = array('datos'=>'', 'mensaje'=>'', 'error'=>0);
$wbasedato = consultarAliasPorAplicacion ($conex, $wemp_pmla, 'talhuma');

// $q = "  SELECT  Ideno1, Ideno2, Ideap1, Ideap2,Ideuse,Idecco
		  // FROM  ".$wbasedato."_000013
		 // WHERE  Idecco='".$wcco."'
		   // AND  Ideest = 'on' ";
		   
//--
$q = "  SELECT Ideno1, Ideno2, Ideap1, Ideap2,Ideuse,Idecco,".$wbasedato."_000058.* "
	   ."  FROM talhuma_000013 LEFT JOIN talhuma_000058 ON (Ideuse = Arecdo AND Arecdr='".$wcalificador1."' AND Areper='".$wvperiodo."' AND Areano='".$wvano."') "
	   ." WHERE Idecco='".$wcco."' "
	   ."   AND Ideest = 'on' "
	   ."   AND Arecdr IS NULL "; 
//--
		   
// $q2 =" 	SELECT  Ideno1, Ideno2, Ideap1, Ideap2,Ideuse,Idecco
		  // FROM  ".$wbasedato."_000058 , ".$wbasedato."_000013
		 // WHERE  Idecco='".$wcco."'
		   // AND  Ideest = 'on' 
		   // AND  Arecdr = '".."'
		   // AND  Ideuse  = Arecdo 
		   // AND  Aretem = '".."' 
		   // AND  Areper = 'Arefo'";	   

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
					<td width='50' align= 'left' ><a style='float : right; cursor: pointer' onclick='cerrarseccion(\"div_cco-".$wcco."\",\"ref_".$wcco."\")'>Cerrar</a></td>
                    </tr>
                </table>
    </div>";

$respuesta .= "	<div id='div_cco-".$wcco."' width='900' align='center'>

		<table width='900'>
		<tr class='encabezadoTabla'>
		<td width='546'>Nombre</td>
		<td width='179' >Formato</td>
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
				$respuesta .= "	<tr  class='fila2'>

									<td align='left'><input type='checkbox' id='check2-".$row['Ideuse']."' value='".$row['Ideuse']."' onClick='guardarEvaluacion(\"".$row['Ideuse']."\")'>".$row['Ideuse']." ".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."</td>";

									$query= " SELECT Ajefor "
											."  FROM  ".$wbasedato."_000008 "
											." WHERE Ajeucr = '".$wcalificador1."' "
											."   AND Ajeuco = '".$row['Ideuse']."'";
									$resquery = mysql_query($query,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$query." - ".mysql_error());
									$numrowcom = mysql_num_rows($resquery);
									$row2 = mysql_fetch_array($resquery);


		
							$respuesta .= "<td>
							
										 <Select onchange='cambiarformato(\"".$row['Ideuse']."\")' id='selectformatosportema-".$row['Ideuse']."'>";

					$q = "  SELECT  Forcod,Fordes
							  FROM  ".$wbasedato."_000002
							 WHERE Fortip= '".$wtemaevaluacion."'";

					$res2 = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					While($row = mysql_fetch_array($res2))
						{

							if($numrowcom!='0')
							  {
							    if($row2['Ajefor']==$row['Forcod'])
								{
									$respuesta .= 		 "<option value='".$row['Forcod']."' selected>".$row['Forcod']."-".utf8_encode($row['Fordes'])."</option>";
								}
								else
								{
									$respuesta .= 		 "<option value='".$row['Forcod']."'>".$row['Forcod']."-".utf8_encode($row['Fordes'])."</option>";
								}
							  }
							  else
							  {
								$respuesta .= 		      "<option value='".$row['Forcod']."'>".$row['Forcod']."-".utf8_encode($row['Fordes'])."</option>";
							  }
						}

					$respuesta .= "				 </select>";

			}
			$respuesta .= "			</tr>";

		 $j++;

		}
		$respuesta .= "			</table>";




}
	$data['datos'] = $respuesta;
	echo json_encode($data);
	return;

} 
if($woperacion=='cambiarformato')
{
$q  =	"UPDATE ".$wbasedato."_000058   "
			   ."   SET Arefor = '".$wformatoevaluacion."' "
			   ." WHERE Arecdr = '".$wcalificador."' "
			   ."   AND Arecdo = '".$wcalificado."' "
			   ."   AND Aretem = '".$wtemaevaluacion."' "
			   ."   AND Areper = '".$wperiodo."' "
			   ."   AND Areano = '".$wano."' ";
			   
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error()); 
	


}
//--
if($woperacion =='seleccioncco')
{
	// consulta de empleados por centro de costos
	$q = "  SELECT  Ideno1, Ideno2, Ideap1, Ideap2,Ideuse,Idecco
			  FROM  ".$wbasedato."_000013
			 WHERE  Idecco='".$wcco."'
			   AND  Ideest = 'on' 
			   ORDER BY Ideno1, Ideno2, Ideap1, Ideap2";
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	echo" <option value='ninguno'  >--Seleccione-Calificador--</option>";
	While($row = mysql_fetch_array($res))
	{
		echo "<option value='".$row['Ideuse']."'>".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']." </option>";
	}
return;
}	


//--
if($woperacion =='guardarevaluacion')
{
$q = "SELECT COUNT(*) "
	."  FROM  ".$wbasedato."_000058 " 
	." WHERE Arecdr = '".$wcodigocalificador."' "
	."   AND Arecdo = '".$wcodigo."' "
	."   AND Aretem = '".$wtemaevaluacion."' "
	."   AND Areper = '".$wperiodo."' "
	."   AND Areano = '".$wano."' ";
	

$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
$row = mysql_fetch_array($res);
if($row[0]==0)
{
	//---Inserta el registro evaluador evaluado periodo tema y formato
	$q 	= 	"INSERT INTO ".$wbasedato."_000058 "
			."      (
						Arecdr,
						Arecdo,
						Aretem,
						Arefor,
						Areper,
						Areano,
						Areest,
						Seguridad,
						Medico,
						Hora_data,
						Fecha_data
					)
					  VALUES (
						'".$wcodigocalificador."',
						'".$wcodigo."',
						'".$wtemaevaluacion."',
						'".$wformatoevaluacion."',
						'".$wperiodo."',
						'".$wano."',
						'on',
						'C-".$wbasedato."',
						'".$wbasedato."',
						'".$hora."',
						'".$fecha."'
				)";
			
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
}else
{
	$q = " DELETE "
		."   FROM ".$wbasedato."_000058 "
		." WHERE Arecdr = '".$wcodigocalificador."' "
		."   AND Arecdo = '".$wcodigo."' "
		."   AND Aretem = '".$wtemaevaluacion."' "
		."   AND Areper = '".$wperiodo."' "
		."   AND Areano = '".$wano."' ";
		
	$res = mysql_query($q,$conex)or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
}
return;
}

if($woperacion == 'obtenerperiodo')
{
  	$q = "SELECT  Perano,Perper ,Perest"
			."  FROM ".$wbasedato."_000009 "
			."  WHERE Perfor='".$wtemaevaluacion."'";
			
	$res = mysql_query($q,$conex) or die ("Error Periodo: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	while($row =mysql_fetch_array($res))
	{
		if($row['Perest']=='on')
		{			
			$selectperiodo=$row['Perper'].'|'.$row['Perano'];
		}
	}
    echo $selectperiodo;
	return ;
}

if($woperacion == 'traearbolrelacionperiodoseleccionado')
{
if($westado=='on')
{


	$q = "SELECT Ajeuco , Ideno1,Ideno2,Ideap1,Ideap2"
			."	FROM ".$wbasedato."_000008 , talhuma_000013"
			." WHERE Ajeucr = '".$wcodigo."' "
			."	 AND Ajeuco = Ideuse ";
		
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numrow = mysql_num_rows($res);
	
	
	$vectorrelacionados = array();
	while($row =mysql_fetch_array($res))
	{
		$vectorrelacionados[$row['Ajeuco']]= $row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2'];
	}


	if ($numrow != 0)
	{
		// consulta de formatos por tema
		$q = "SELECT  Forcod,Fordes "
			."  FROM ".$wbasedato."_000002 "
			."  WHERE Fortip='".$wtemaevaluacion."'";

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		//-----------

		$select = "";

		while($row =mysql_fetch_array($res))
		{
			
			$select .= "<option value='".$row['Forcod']."'>".$row['Fordes']."</option>";
		}

		$select .= "</select>";
		
		// necesita periodo  y tema
		$q = "SELECT Arecdo,Arefor , Ideno1,Ideno2,Ideap1,Ideap2"
			."	FROM ".$wbasedato."_000058 ,".$wbasedato."_000013"
			." WHERE Arecdr = '".$wcodigocalificador."' "
			."	 AND Areper = '".$wperiodo."' "
			."   AND Areano = '".$wano."' "
			."   AND Aretem = '".$wtemaevaluacion." ' " 
			."   AND Arecdo = Ideuse ";
		
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		
		$vectoryaguardados = array();
		$vectoryaguardadosnom = array();
		while($row =mysql_fetch_array($res))
		{
			$vectoryaguardados[$row['Arecdo']]=$row['Arefor'];
			if(!(array_key_exists( $row['Arecdo'] ,$vectorrelacionados )) )
			$vectorrelacionados[$row['Arecdo']]= $row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2'];
		}
		echo "<table width='800' align='center'>";
		echo"<tr class='encabezadoTabla'><td nowrap='nowrap' width='400'>Nombre</td><td width='400'>Formato</td><tr>";	
	
		foreach($vectorrelacionados as $codigousuario => $nombre  )
		{
			if ( $vectoryaguardados[$codigousuario] == '')
				echo "<tr class='fila2'><td><input type='checkbox'  id='check2-".$codigousuario."' value='".$codigousuario."' onClick='guardarEvaluacion(\"".$codigousuario."\")'> ".$codigousuario." ".$nombre." </td><td><Select onchange='cambiarformato(\"".$codigousuario."\")' id='selectformatosportema-".$codigousuario."'>".$select."</td></tr>";
			else
			{		
				$select = str_replace("selected"," ",$select);
				$select = str_replace("value='".$vectoryaguardados[$codigousuario]."'" , "value='".$vectoryaguardados[$codigousuario]."' selected",$select);
				echo "<tr class='fila2'><td><input type='checkbox'  checked id='check2-".$codigousuario."' value='".$codigousuario."' onClick='guardarEvaluacion(\"".$codigousuario."\")'> ".$codigousuario." ".$nombre." </td><td><Select id='selectformatosportema-".$codigousuario."' onchange='cambiarformato( \"".$codigousuario."\")'>".$select."</td></tr>";
			}
			
		}
		
		echo "</table>";
		
		echo "<br>";
		echo "<br>";
		echo "<table>";
		echo"<tr>";
		echo"<td align='left'><input type='button' id='buttoncc' value='+ C.Costos' onclick='verSeccion(\"div_newcco\")'/><td>";
		echo"</tr>";
		echo"</table>";
		echo"<div id='div_newcco' width='400' align='center'  style='display:none' >";
        echo"<table width='400' >
		<tr class='fila1'><td align='left'>";
		
		$arraycentro = array();
		
		// consulta de centro de costos
		$q = "  SELECT  Ccocod,Cconom
				FROM  costosyp_000005
				WHERE Ccoest = 'on'  
				     and Ccoemp = '".$wemp_pmla."'
				ORDER BY Ccocod";

		$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	    
	    while ($row = mysql_fetch_assoc($res1))
	          {
	             $arraycentro[$row['Ccocod']] = utf8_encode($row['Cconom']);
	          }		  

        echo "<input type='hidden' id='arr_wccohospitalario2' value='".json_encode($arraycentro)."'>";
		echo "<input type='text'   id='wcentrocostos2' codigo='' nombre='' size='80' >";
        echo "<input type='hidden' id='wccohospitalario2' name='wccohospitalario2' size=80>";
		echo "</td></tr>";
		echo "</table></div>";
		echo "<div id='contenedor0'>

		</div>";
		//--
		echo "</div>";
		
		/*	<select name='wcentrocostos2' id='wcentrocostos2' onchange=carganuevocco(this,\"".$wemp_pmla."\",\"".$wperiodo."\",\"".$wano."\") >";

		$q = "  SELECT  Ccocod,Cconom
				  FROM  costosyp_000005
				  ORDER BY Ccocod";

		$res1 = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		While($row = mysql_fetch_array($res1))
		{
		echo"<option value='".$row[0]."*|*".utf8_encode($row[1])."'>".utf8_encode($row[0])." - ".utf8_encode($row[1])."</option>";
		}*/
	
	}

	
}
else
{
	
	echo "<table width='800'>";	
	echo"<tr class='encabezadoTabla'><td  width='400' nowrap='nowrap' >Nombre</td><td width='400'>Formato</td><tr>";	
		

		// necesita periodo  y tema
		$q = "SELECT Arecdo,Arefor,Ideno1,Ideno2,Ideap1,Ideap2,Fordes "
			."	FROM ".$wbasedato."_000058 , ".$wbasedato."_000013, ".$wbasedato."_000002"
			." WHERE Arecdr = '".$wcodigocalificador."' "
			."	 AND Areper = '".$wperiodo."' "
			."   AND Areano = '".$wano."' "
			."   AND Aretem = '".$wtemaevaluacion." ' " 
			."   AND Arefor = Forcod"
			."   AND Ideuse = Arecdo ";
		
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		
		$vectoryaguardados = array();
		$vectoryaguardados2 = array();
		while($row =mysql_fetch_array($res))
		{
			$vectoryaguardados[$row['Arecdo']]=$row['Fordes'];
			$vectoryaguardados2[$row['Arecdo']]=$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2'];
		}
		
		foreach($vectoryaguardados as $codigousuario => $formulario  )
		{
				echo "<tr class='fila2'><td><input type='checkbox'  disabled checked id='check2-".$codigousuario."' value='".$codigousuario."' onClick='guardarEvaluacion(\"".$codigousuario."\")'> ".$codigousuario."  ".$vectoryaguardados2[$codigousuario]."   </td><td>".$formulario."</td></tr>";
		}
		
		
	}
echo"</table>";
		

return;
}
//---
if($woperacion == 'traearbolrelacion')
{
$q = "      SELECT Ajeuco , Ideno1,Ideno2,Ideap1,Ideap2"
		."	  FROM ".$wbasedato."_000008 , talhuma_000013"
		."   WHERE Ajeucr = '".$wcodigo."' "
		." 	   AND Ajeuco = Ideuse "
		."ORDER BY Ideno1,Ideno2,Ideap1,Ideap2 ";
	
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$numrow = mysql_num_rows($res);
	
	
$vectorrelacionados = array();
while($row =mysql_fetch_array($res))
{
	$vectorrelacionados[$row['Ajeuco']]= $row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2'];
}



	echo "<table width='800' align='center'><tr class='encabezadoTabla'><td colspan='2'  align='center'>Empleados Relacionados </td></tr>";
	echo "<tr class='fila1'><td width='400' ><div id='divcalificador'></div></td><td width='400'  ><div id='divperiodosportema' style='float : right'>Periodos :";
	
	
		echo "<Select id='selectperiodosportema' onchange='seleccionevaluacionempleados()'>";

		$q = "SELECT  Perano,Perper ,Perest"
			."  FROM ".$wbasedato."_000009 "
			."  WHERE Perfor='".$wtemaevaluacion."'";
			
			//agregar nuevo 

		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		while($row =mysql_fetch_array($res))
		{
			if($row['Perest']=='on')
			{
				echo "<option value='".$row['Perano']."||".$row['Perper']."||".$row['Perest']."' selected>".$row['Perano']."-".$row['Perper']."</option>";
				$selectperiodo=$row['Perper'];
				$selectano=$row['Perano'];
			}
			else
			{
				echo "<option value='".$row['Perano']."||".$row['Perper']."||".$row['Perest']."' >".$row['Perano']."-".$row['Perper']."</option>";
			}
			
		}
		echo "</select>";
	
	
	
	echo "</div></td></tr>";
	echo "<tr><td colspan='2'></td></tr>";
	echo "<tr><td colspan='2'></td></tr>";
	echo "</table>";

	
	// consulta de formatos por tema
	$q = "SELECT  Forcod,Fordes "
		."  FROM ".$wbasedato."_000002 "
		."  WHERE Fortip='".$wtemaevaluacion."'";

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	//-----------

	$select = "";

	while($row =mysql_fetch_array($res))
	{
		
		$select .= "<option value='".$row['Forcod']."'>". utf8_encode($row['Fordes'])."</option>";
	}

	$select .= "</select>";

	echo "<div width='800' id='divempleadosrelacionados' align='center'>";
	echo "<table width='800' align='center'>";
	echo"<tr class='encabezadoTabla'><td  width='400' nowrap='nowrap' >Nombre</td><td width='400'>Formato</td><tr>";	
		
		
		
		
		// necesita periodo  y tema
		$q = "SELECT Arecdo,Arefor,Ideno1,Ideno2,Ideap1,Ideap2 "
			."	FROM ".$wbasedato."_000058,".$wbasedato."_000013"
			." WHERE Arecdr = '".$wcodigocalificador."' "
			."	 AND Areper = '".$selectperiodo."' "
			."   AND Areano = '".$selectano."' "
			."   AND Aretem = '".$wtemaevaluacion." ' " 
			."   AND Arecdo = Ideuse ";
		
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		
		$vectoryaguardados = array();
		$vectoryaguardadosnom = array();
		while($row =mysql_fetch_array($res))
		{
			$vectoryaguardados[$row['Arecdo']]=$row['Arefor'];
			if(!(array_key_exists( $row['Arecdo'] ,$vectorrelacionados )) )
			$vectorrelacionados[$row['Arecdo']]= $row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2'];
		}
		
		while($row =mysql_fetch_array($res))
{
	$vectorrelacionados[$row['Ajeuco']]= $row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2'];
}
		foreach($vectorrelacionados as $codigousuario => $nombre  )
		{
			if ( $vectoryaguardados[$codigousuario] == '')
				echo "<tr class='fila2'><td><input type='checkbox'  id='check2-".$codigousuario."' value='".$codigousuario."' onClick='guardarEvaluacion(\"".$codigousuario."\")'> ".$codigousuario." ".$nombre." </td><td><Select onchange='cambiarformato(\"".$codigousuario."\")' id='selectformatosportema-".$codigousuario."'>".$select."</td></tr>";
			else
			{		
				$select = str_replace("selected"," ",$select);
				$select = str_replace("value='".$vectoryaguardados[$codigousuario]."'" , "value='".$vectoryaguardados[$codigousuario]."' selected",$select);
				echo "<tr class='fila2'><td><input type='checkbox'  checked id='check2-".$codigousuario."' value='".$codigousuario."' onClick='guardarEvaluacion(\"".$codigousuario."\")'> ".$codigousuario." ".$nombre." </td><td><Select id='selectformatosportema-".$codigousuario."' onchange='cambiarformato( \"".$codigousuario."\")'>".$select."</td></tr>";
			}
			
		}
		
		echo "</table>";
		//--
		echo "<br>";
		echo "<br>";
		echo "<table>";
		echo"<tr>";
		echo"<td align='left'><input type='button' id='buttoncc' value='+ C.Costos' onclick='verSeccion(\"div_newcco\")'/><td>";
		echo"</tr>";
		echo"</table>";
		echo"<div id='div_newcco' width='400' align='center'  style='display:none' >";
        echo"<table width='400' >
		<tr class='fila1'><td align='left'>";
        
        $arraycentro = array();

		// consulta de centro de costos
		$q = "  SELECT  Ccocod,Cconom
				 FROM  costosyp_000005
				 WHERE Ccoest = 'on'  
				     and Ccoemp = '".$wemp_pmla."'
				 ORDER BY Ccocod";

		$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	    
	    while ($row = mysql_fetch_assoc($res1))
	          {
	             $arraycentro[$row['Ccocod']] = utf8_encode($row['Cconom']);
	          }		  


        echo '<input type="hidden" id="arr_wccohospitalario2" value=\''.json_encode($arraycentro).'\' >';
		echo '<input type="text" id="wcentrocostos2" codigo="" nombre="" size="80" >';
        echo "<input type='hidden' id='wccohospitalario2' name='wccohospitalario2'size=80>";

		/*<select name='wcentrocostos2' id='wcentrocostos2' onchange=carganuevocco(this,\"".$wemp_pmla."\",\"".$selectperiodo."\",\"".$selectano."\") >";

		$q = "  SELECT  Ccocod,Cconom
				  FROM  costosyp_000005
				  ORDER BY Ccocod";

		$res1 = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		While($row = mysql_fetch_array($res1))
		{
		echo"<option value='".$row[0]."*|*".utf8_encode($row[1])."'>".utf8_encode($row[0])." - ".utf8_encode($row[1])."</option>";
		}*/
		echo"</td></tr>";
		echo"</table></div>";
		echo"<div id='contenedor0'>


		</div>";
		//--
		echo "</div>";

	return;
}
if($woperacion=='seleccionarempleado')
{
	$arraycentro = array();

	// consulta de centro de costos
	$q = "  SELECT Ccocod,Cconom
			 FROM  costosyp_000005
			 WHERE Ccoest = 'on'  
				   and Ccoemp = '".$wemp_pmla."'
			 ORDER BY Ccocod";

	$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    
    while ($row = mysql_fetch_assoc($res1))
          {
             $arraycentro[$row['Ccocod']] = utf8_encode($row['Cconom']);
          }		  

	echo"<table width='900' align='center'>
			<tr>
				<td>
				<div id='ref_tbeva' align='center'>
                <table width='900' border='0' cellspacing='0' cellpadding='0'>
                    <tr class='encabezadoTabla'></tr>
                    <tr><td>&nbsp;</td></tr>
                </table>
                <table width='900' border='0' cellspacing='0' cellpadding='0'>
                    <tr>
                        <td align ='left' width='850' colspan='2'><a href='#null'  onclick='verSeccion(\"divppal\")'>Seleccionar Calificador</a></td>
                    </tr>
                </table>
				</div>

				<div id='divppal' width='900' align='center' class='borderDiv displ'>
					<table width='900'>
						<tr class='fila1'>
							<td width='134'>Centro de costos:</td>
							<td width='134'>

							<input type='hidden' id='arr_wccohospitalario' value='".json_encode($arraycentro)."'>
							<input type='text' id='wcentrocostos' codigo='' nombre='' size=80 >
                            <input type='hidden' id='wccohospitalario' name='wccohospitalario'size=80>

			</td>
		</tr>
						<tr class='fila1'>
						<td width='134'>Calificador: </td>
							<td width='134'><select  name='wcalificador2' id='wcalificador2' onchange='seleccionevaluacion()' >
								<option value='ninguno' >--Seleccione-Calificador--</option></select>
							</td>
						</tr>
					 </table>
					</div>
				</td>
			</tr>
		</table>";
	return;

    // Campo Centro de Costos anterior
    
/*							<select  name='wcentrocostos' id='wcentrocostos' onchange='seleccioncco()'>;
							
							While($row = mysql_fetch_array($res1))
								{

									if(isset($wcco) AND $wcco==$row['Ccocod'])
										{
												echo	  "<option value='".$row['Ccocod']."' selected>".$row['Ccocod']."-".utf8_encode($row['Cconom'])."</option>";
										}
									else
										{
												echo	  "<option value='".$row['Ccocod']."' >".$row['Ccocod']."-".utf8_encode($row['Cconom'])."</option>";
										}
								}</select>*/

}

if (isset($wlistaempleados) AND  $wlistaempleados=='si')
{
	
	$q  = "SELECT  Empcod, Empeva"
		. "  FROM ".$wbasedato."_000055 " 
		."  WHERE  Empeve='pendiente' ";
		
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	
	while($row =mysql_fetch_array($res))
	{
		$pacientesprogramados[$row['Empcod']] =  $row['Empeva'];
	}
	

	
	echo '<div id="div_titulo" align="center" style="font-weight:bold; text-align: Left; ">LISTA DE EMPLEADOS</div>';
	echo '<div id="div_empleados" align="center" class="borderDiv displ" >';
	echo"<table  id='tabemple'  align='center'>";

	$q  = "SELECT Ideuse,Ideno1,Ideno2,Ideap1,Ideap2"
		. "  FROM talhuma_000013 "
		. " WHERE Ideest = 'on' "
		. "   AND Idecco = '".$wcco."'"
		. " ORDER BY TRIM(Ideno1), TRIM(Ideno2), TRIM(Ideap1), TRIM(Ideap2)" ;

	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
	
	$i = 0;	
	echo "<tr class='encabezadoTabla'>";
	echo "<td></td>";
	echo "<td>Codigo</td>";
	echo "<td>Nombre</td>";
	echo "<td>Cargo</td>";
	echo "</tr>";
	while($row =mysql_fetch_array($res))
	{
		
		
		if (($i%2)==0)
	    {
			$wcf="fila1";  // color de fondo de la fila
	    }
		else
	    {
			$wcf="fila2"; // color de fondo de la fila
	    }
			echo"<tr>";
			echo"<td class='".$wcf."' align='center'><input type='checkbox'  id='check-".$row['Ideuse']."' value='".$row['Ideuse']."' onClick='seleccionevaluacion(this,\"".$row['Ideuse']."\")'></td>";
			echo"<td class='".$wcf."' >".$row['Ideuse']."</td>";
			echo"<td class='".$wcf."' >".$row['Ideno1']." ".$row['Ideno2']." ".$row['Ideap1']." ".$row['Ideap2']."</td>";
			echo"<td class='".$wcf."'>".$row['Cardes']."</td>";
			echo"</tr>";
			$i++;
	}	
	echo"</table>";
	echo '</div >';
	return;
}
?>
<head>
  <title>Configuracion de  Periodos</title>
  
</head>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet"/>

<script type="text/javascript">


//--
function carganuevocco(emp_pmla,vperiodo,vano){

var campo = $("#wcentrocostos2").val().split("-");
$('#buttoncc').focus();
var ncco= campo[0];
var nncco= campo[1];
var calificador=document.getElementById('wcalificador2').value;
var division = document.getElementById('div_cco-'+ncco);

if (division==null)
{
	var params = 'ArboldeEvaluacion.php?consultaAjax=&wemp_pmla='+emp_pmla+'&wcco='+ncco+'&accion=nuevo_cco&wcalificador1='+calificador+'&wnncco='+nncco+'&wtema='+$("#wtema").val()+'&wvano='+vano+'&wvperiodo='+vperiodo+'&wtemaevaluacion='+$('#wtemaformato').val();
	$.post(	params,
			function(data) {
				if (data['error'] == 1)
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

function seleccioncco()
{
    var wcentroc = $('#wcentrocostos').val().split('-');

	$.post("ArboldeEvaluacion.php",
            {
                consultaAjax    : '',
                wemp_pmla       : $("#wemp_pmla").val(),
                wtema           : $("#wtema").val(),
				woperacion	    : "seleccioncco",
				wtemaevaluacion : $('#wtemaformato').val(),
				wcco			: wcentroc[0]
            }
            ,function(data) {
               $("#wcalificador2").html(data);
            });
}

function cambiarformato(codigousuario)
{
	if($('#check2-'+codigousuario).is(':checked') )
	{
				var anoperiodo =$('#selectperiodosportema').val();
				anoperiodo =anoperiodo.split("||"); 

				var ano = anoperiodo[0];
				var periodo = anoperiodo[1];
				
			
	
				
				 $.post("ArboldeEvaluacion.php",
				 {
					consultaAjax: '',
					wemp_pmla   : $("#wemp_pmla").val(),
					wtema       : $("#wtema").val(),
					woperacion	: "cambiarformato",
					wtemaevaluacion : $('#wtemaformato').val(),
					wano			: ano,
					wperiodo		: periodo,
					wcalificador	: $("#wcalificador2").val(),
					wcalificado 	: codigousuario,
					wformatoevaluacion : 	$("#selectformatosportema-"+codigousuario).val()

				}
				,function(data) {
				  
				});
	}
	else
	{
		alert ("Debe primero seleccionar la casilla");
	}

}
function verSeccion(id){
        $("#"+id).toggle("normal");
}
function cerrarseccion(id,id2){
$("#"+id).remove();
$("#"+id2).remove();
}	
function seleccionevaluacionempleados()
{


	var anoperiodo =$('#selectperiodosportema').val();
	anoperiodo =anoperiodo.split("||"); 

	var ano = anoperiodo[0];
	var periodo = anoperiodo[1];
	var estado = anoperiodo[2];

	var textoseleccionado = $("#wcalificador2 option:selected").text();
	$.post("ArboldeEvaluacion.php",
            {
                consultaAjax: '',
                wemp_pmla   : $("#wemp_pmla").val(),
                wtema       : $("#wtema").val(),
                wcodigo		: $("#wcalificador2").val(),
				woperacion	: "traearbolrelacionperiodoseleccionado",
				wtemaevaluacion : $('#wtemaformato').val(),
				wcodigocalificador  : $("#wcalificador2").val(),
				wano 				: ano,
				wperiodo			: periodo,
				westado				: estado
				
            }
            ,function(data) {
               $("#divempleadosrelacionados").html(data);
            }
        ).done(function(){
        
        var datos = eval('(' + $("#arr_wccohospitalario2").val() + ')');
        var arr_datos = new Array();
        var index = -1;
        for (var CodVal in datos)
        {
        	index++;
            arr_datos[index] = {};
            arr_datos[index].value  = CodVal+'-'+datos[CodVal];
            arr_datos[index].label  = CodVal+'-'+datos[CodVal];
            arr_datos[index].codigo = CodVal;
            arr_datos[index].nombre = CodVal+'-'+datos[CodVal];           
        }

        $("#wcentrocostos2").autocomplete({
                source: arr_datos, minLength : 0,
                select: function( event, ui ) {
                            var cod_sel = ui.item.codigo;
                            var nom_sel = ui.item.nombre;
                            $("#wcentrocostos2").attr("codigo",cod_sel);
                            $("#wcentrocostos2").attr("nombre",nom_sel);
                            $("#wccohospitalario2" ).val(ui.item.codigo);  

                        },
				change: function (event, ui) { carganuevocco($("#wemp_pmla").val(),periodo,ano); },                        
                close: function( event, ui ) {
                    
                }
        });

/*        $('#wcentrocostos2').on({
                focusout: function(e) {
                    if($(this).val().replace(/ /gi, "") == '')
                    {
                        $(this).val("");
                        $(this).attr("codigo","");
                        $(this).attr("nombre","");
                    }
                    else
                    {
                        $(this).val($(this).attr("nombre"));
                    }
                }
            });*/
    });

}

function seleccionevaluacion()
{
	var vperiodo = '';
	var vano     = '';

	$.post("ArboldeEvaluacion.php",
	     {
            consultaAjax       : '',
            wemp_pmla          : $("#wemp_pmla").val(),
            wtema              : $("#wtema").val(),
			woperacion	       : "obtenerperiodo",
			wtemaevaluacion    : $('#wtemaformato').val()
	     }
        ,function(data) {

           var resultado=data.split('|');	
           vperiodo= resultado[0];
           vano    = resultado[1];
        });

	var textoseleccionado = $("#wcalificador2 option:selected").text();

	$.post("ArboldeEvaluacion.php",
            {
                consultaAjax       : '',
                wemp_pmla          : $("#wemp_pmla").val(),
                wtema              : $("#wtema").val(),
                wcodigo		       : $("#wcalificador2").val(),
				woperacion	       : "traearbolrelacion",
				wtemaevaluacion    : $('#wtemaformato').val(),
				wcodigocalificador : $("#wcalificador2").val()
				
            }
            ,function(data) {
               $("#divarbolevaluacion").html(data);
			   $('#wcodigoevaluador').val( $("#wcalificador2").val());
			   $('#divcalificador').text('Calificador: '+textoseleccionado);
            }
        ).done(function(){
        
        var datos = eval('(' + $("#arr_wccohospitalario2").val() + ')');
        var arr_datos = new Array();
        var index = -1;
        for (var CodVal in datos)
        {
        	index++;
            arr_datos[index] = {};
            arr_datos[index].value  = CodVal+'-'+datos[CodVal];
            arr_datos[index].label  = CodVal+'-'+datos[CodVal];
            arr_datos[index].codigo = CodVal;
            arr_datos[index].nombre = CodVal+'-'+datos[CodVal];           
        }

        $("#wcentrocostos2").autocomplete({
                source: arr_datos, minLength : 0,
                select: function( event, ui ) {
                            var cod_sel = ui.item.codigo;
                            var nom_sel = ui.item.nombre;
                            $("#wcentrocostos2").attr("codigo",cod_sel);
                            $("#wcentrocostos2").attr("nombre",nom_sel);
                            $("#wccohospitalario2" ).val(ui.item.codigo);  
                        },
                change: function (event, ui) { carganuevocco($("#wemp_pmla").val(),vperiodo,vano); }
        });

        $('#wcentrocostos2').on({
                focusout: function(e) {
                    if($(this).val().replace(/ /gi, "") == '')
                    {
                        $(this).val("");
                        $(this).attr("codigo","");
                        $(this).attr("nombre","");
                    }
                    else
                    {
                        $(this).val($(this).attr("nombre"));
                    }
                }
            });
    });
}

function guardarEvaluacion (codigo)
{
	var anoperiodo =$('#selectperiodosportema').val();
	anoperiodo =anoperiodo.split("||"); 
	
	var ano = anoperiodo[0];
	var periodo = anoperiodo[1];	
	
	$.post("ArboldeEvaluacion.php",
            {
                consultaAjax: '',
                wemp_pmla   : $("#wemp_pmla").val(),
                wtema       : $("#wtema").val(),
                wcodigo		: codigo,
				woperacion	: "guardarevaluacion",
				wtemaevaluacion : $('#wtemaformato').val(),
				wformatoevaluacion: $("#selectformatosportema-"+codigo).val(),
				wperiodo	: periodo,
				wano		: ano,
				wcodigocalificador : $('#wcodigoevaluador').val()
				
            }
            ,function(data) {
              
            }
        );
}

function traelistaempleado(cco)
{
var centrocostos = cco.value;
var  emp_pmla = document.getElementById('wemp_pmla').value;
var tema =  document.getElementById('wtema').value;

	var params   = 'ArboldeEvaluacion.php?consultaAjax=&wlistaempleados=si&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wcco='+centrocostos;
	
	$.get(params, function(data) {
	
		$('#divlista').html(data);
		
	});
}


function cambioImagen(img1, img2)
{
	$('#'+img1).hide(1000);
	$('#'+img2).show(1000);
}

function enterBuscar(ele,hijo,op,form,e)
{
	tecla = (document.all) ? e.keyCode : e.which;
	if(tecla==13) { $("#"+hijo).focus(); }
	else { return true; }
	return false;
}

function recargarLista(id_padre, id_hijo, form)
{
	val = $("#"+id_padre).val();
	if(val != '*')
	{
		$('#'+id_hijo).load(
				"gestion_perfiles.php",
				{
					consultaAjax:   '',
					wemp_pmla:  $("#wemp_pmla").val(),
					wtema:      $("#wtema").val(),
					temaselect: $('#temaselect').val(),
					accion:     'load',
					id_padre:   val,
					form:       form
				});
	}
}

function ConsultarLista ()
{

var emp_pmla = document.getElementById('wemp_pmla').value;
var tema =  document.getElementById('wtema').value;
var cco = document.getElementById('wcco').value;
var fecha_i = document.getElementById('wfecha_i').value;
var fecha_f = document.getElementById('wfecha_f').value;



var params   = 'ArboldeEvaluacion.php?consultaAjax=&woperacion=resultadosLista&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wfecha_i='+fecha_i+'&wfecha_f='+fecha_f+'&wcco='+cco;

	$.get(params, function(data) {
	$('#divlista').html(data);
	});


}

function  guardarenLista (pacno1,pacno2,pacap1,pacap2,pacced,pactid,Habcod,Orihis,Oriing,ccocod,fecha,contenedor,elemento,telefono,edad,nre,wtpa)
{
	var emp_pmla = document.getElementById('wemp_pmla').value;
	var tema =  document.getElementById('wtema').value;
	var cco = document.getElementById('wcco').value;
	var ultimaencuesta = document.getElementById('ultimaencuesta').value;
		
	if (ultimaencuesta.length == 0)
	{
		ultimaencuesta = 0;
	}
	if (elemento.checked)
	{
		if (selectencuestas != null)
		{
			$('#'+contenedor).html(selectencuestas);
			if ( ultimaencuesta != 0){
				$('#'+contenedor+" select").val(ultimaencuesta);
			}
			guardarpaciente(pacno1,pacno2,pacap1,pacap2,pacced,pactid,Habcod,Orihis,Oriing,ccocod,fecha,contenedor,elemento,ultimaencuesta,telefono,edad,nre,wtpa);
		}
		else
		{
			var params   = 'ArboldeEvaluacion.php?consultaAjax=&woperacion=agregarencuesta&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wultimaencuesta='+ultimaencuesta;
		
				$.get(params, function(data) {
				$('#'+contenedor).html(data);
				selectencuestas=data;
				ultimaencuesta = $("#"+contenedor+" select").val();
				guardarpaciente(pacno1,pacno2,pacap1,pacap2,pacced,pactid,Habcod,Orihis,Oriing,ccocod,fecha,contenedor,elemento,ultimaencuesta,telefono,edad,nre,wtpa);
				});		
		} 
	}	
	else
	{	
		$('#'+contenedor).html('');
		var params   = 'ArboldeEvaluacion.php?consultaAjax=&woperacion=eliminarPacienteEncuesta&wemp_pmla='+emp_pmla+'&wtema='+tema+'&wced='+pacced+'&wno1='+pacno1+'&wno2='+pacno2+'&wap1='+pacap1+'&wap2='+pacap2+'&wtid='+pactid+'&whcod='+Habcod+'&wing='+Oriing+'&wcco='+cco+'&wfechaing='+fecha+'&whis='+Orihis;
		$.get(params, function(data) {
	 
		});
	}	
}

function grabarfechayhora(tema)
{
	var empleado=document.getElementById('wemp_pmla').value;
	var nano=document.getElementById('wano-on').value;
	var nperiodo=document.getElementById('wperiodo-on').value;
	var formato =$('#formatoabierto').val();
	var fechaini = $('#ifecha').val();
	var fechafin = $('#ffecha').val();
	var horaini = $('#hidatepicker').val();
	var horafin = $('#hfdatepicker').val();
	var params = 'ArboldeEvaluacion.php?consultaAjax=&wemp_pmla='+empleado+'&grabarfechayhora=si&wnano='+nano+'&wnper='+nperiodo+'&wformato='+formato+'&fechaini='+fechaini+'&fechafin='+fechafin+'&horaini='+horaini+'&horafin='+horafin+'&wtema='+tema;
		$.get(params, function(data) {});
}
function  CancelarPeriodo ()
{
 $("#snuevoperiodo").remove();
 document.getElementById('botonagregar').disabled=false;
}
function removepicker()
{
	$('#ifecha').remove();
	$('#ffecha').remove();
}
function seleccionTema (codigotema, nombretema,tema,tipoevaluacion) 
{
	$('tr[id^=tdc-]').css({'background-color':''});
	$("#tdc-"+codigotema).css({'background-color':'yellow'});
	
	var  emp_pmla = document.getElementById('wemp_pmla').value;
	var tema =  document.getElementById('wtema').value;

	var params   = 'ArboldeEvaluacion.php?consultaAjax=&woperacion=seleccionarempleado&wemp_pmla='+emp_pmla+'&wtema='+tema;
	$.get(params, function(data) {
		$('#divcco').html(data);
		$('#wtemaformato').val(codigotema);
		$('#divarbolevaluacion').html('');
	}).done(function(){
        
        var datos = eval('(' + $("#arr_wccohospitalario").val() + ')');
        var arr_datos = new Array();
        var index = -1;
        for (var CodVal in datos)
        {
        	index++;
            arr_datos[index] = {};
            arr_datos[index].value  = CodVal+'-'+datos[CodVal];
            arr_datos[index].label  = CodVal+'-'+datos[CodVal];
            arr_datos[index].codigo = CodVal;
            arr_datos[index].nombre = CodVal+'-'+datos[CodVal];           
        }

        $("#wcentrocostos").autocomplete({
                source: arr_datos, minLength : 0,
                autoFocus: true,
                select: function( event, ui ) {
                            var cod_sel = ui.item.codigo;
                            var nom_sel = ui.item.nombre;
                            $("#wcentrocostos").attr("codigo",cod_sel);
                            $("#wcentrocostos").attr("nombre",nom_sel);
                            $("#wccohospitalario" ).val(ui.item.codigo);  
                        },
                change: function (event, ui) { seleccioncco(); },
                close: function( event, ui ) {                }
                
        });

        $('#wcentrocostos').on({
                focusout: function(e) {
                    if($(this).val().replace(/ /gi, "") == '')
                    {
                        $(this).val("");
                        $(this).attr("codigo","");
                        $(this).attr("nombre","");
                    }
                    else
                    {
                        $(this).val($(this).attr("nombre"));
                    }
                }
            });
    });   

}
function fnMostrar2( celda )
{
		if( $('#'+celda ) ){
			$.blockUI({ message: $('#'+celda ), 
							css: { left: ( $(window).width() - 800 )/2 +'px', 
								  top: '200px',
								  width: '800px'
								 } 
					  });	
		}
		var picker1="ifecha";
		var picker2="ffecha";				
		var pickera = $("<input size='10' id='"+picker1+"' onchange='grabadato(this)' />" ).datepicker({
			monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
			dayNamesMin: ['Dom','Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
			nextText: 'Siguiente',
			prevText: 'Anterior',
			closeText: 'Cancelar',
			currentText: 'Hoy',
			changeMonth: true,
			changeYear: true,
			showButtonPanel: false,
			dateFormat: 'yy-mm-dd'
		});
		
			var pickerb = $("<input size='10' id='"+picker2+"' onchange='grabadato(this)' />" ).datepicker({
			monthNamesShort: ['Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
			dayNamesMin: ['Dom','Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab'],
			nextText: 'Siguiente',
			prevText: 'Anterior',
			closeText: 'Cancelar',
			currentText: 'Hoy',
			changeMonth: true,
			changeYear: true,
			showButtonPanel: false,
			dateFormat: 'yy-mm-dd'
		});
		$('#idatepicker').append(pickera);
		$('#fdatepicker').append(pickerb);	
}

function cambiadatos ()
{
	var empleado=document.getElementById('wemp_pmla').value;
	var nano=document.getElementById('wano-on').value;
	var nperiodo=document.getElementById('wperiodo-on').value;
	var ncalmax=document.getElementById('wcalmax-on').value;
	var ncalmin=document.getElementById('wcalmin-on').value;
	var ncalmal=document.getElementById('wcalmal-on').value;
	var ncalbue=document.getElementById('wcalbue-on').value;
	var ncalsob=document.getElementById('wcalsob-on').value;
	var params = 'configuracionperiodos.php?consultaAjax=&wemp_pmla='+empleado+'&cambiaDatos=si&wnano='+nano+'&wnper='+nperiodo+'&wncalmax='+ncalmax+'&wncalmin='+ncalmin+'&wncalmal='+ncalmal+'&wncalbue='+ncalbue+'&wncalsob='+ncalsob;
		$.get(params, function(data) {});

}

function agregarPeriodo (tema)
{
	var empleado=document.getElementById('wemp_pmla').value;
	var params = 'configuracionperiodos.php?consultaAjax=&wemp_pmla='+empleado+'&nuevoperiodo=si&wtema='+tema;
		$.get(params, function(data) {
			  $('#nperiodo').append(data);
			  document.getElementById('botonagregar').disabled=true;
		});
}

function GrabarPeriodo(tema)
{
var empleado=document.getElementById('wemp_pmla').value;
var nano=document.getElementById('nuevoano').value;
var nperiodo=document.getElementById('nuevoperiod').value;
var ncalmax=document.getElementById('nuevocalmax').value;
var ncalmin=document.getElementById('nuevocalmin').value;
var ncalmal=document.getElementById('nuevocalmal').value;
var ncalbue=document.getElementById('nuevocalbue').value;
var ncalsob=document.getElementById('nuevocalsob').value;
var codtema = document.getElementById("wtemaformato").value
var params = 'configuracionperiodos.php?consultaAjax=&wemp_pmla='+empleado+'&grabaperiodo=si&wnano='+nano+'&wnper='+nperiodo+'&wncalmax='+ncalmax+'&wncalmin='+ncalmin+'&wncalmal='+ncalmal+'&wncalbue='+ncalbue+'&wncalsob='+ncalsob+'&wtemaformato='+codtema+'&wtema='+tema;
    $.post(params, function(data) {
	$('#contendedorperiodo').html(data);
    });
}

function cambiarEstado(elemento,tema,tipoevaluacion)
{
if(tipoevaluacion=='04')
{
var empleado=document.getElementById('wemp_pmla').value;
var formato=elemento.id;
var codtema = document.getElementById("wtemaformato").value	
	agregarabrir = 'agregarabrir';
	var params = 'configuracionperiodos.php?consultaAjax=&wemp_pmla='+empleado+'&wtema='+tema+'&wtemaformato='+codtema+'&woperacion=cargartablahoras&wformato='+formato;
    $.post(params, function(data) {
	$('#divhoras').html(data);
    });
	fnMostrar2 (agregarabrir);
	$('#formatoabierto').val(elemento.id);
}
else
{
var empleado=document.getElementById('wemp_pmla').value;
var params = 'configuracionperiodos.php?consultaAjax=&wemp_pmla='+empleado+'&cambiarestado=si&wformato='+elemento.id+'&wtema='+tema;
    $.get(params, function(data) {  
    });
}
}

function cambiarEstadoPeriodo(elemento)
{

var aux=elemento.id.split('-');
var ano=aux[1];
var periodo=aux[2];
var empleado=document.getElementById('wemp_pmla').value;
var params = 'configuracionperiodos.php?consultaAjax=&wemp_pmla='+empleado+'&cambiarestadoperiodo=si&wano='+ano+'&wperiodo='+periodo;
    $.get(params, function(data) {
	if(document.getElementById('wano-on').disabled==false)
	{
		document.getElementById('wano-on').disabled=true;
		document.getElementById('wperiodo-on').disabled=true;
		document.getElementById('wcalmax-on').disabled=true;
		document.getElementById('wcalmin-on').disabled=true;
		document.getElementById('wcalmal-on').disabled=true;
		document.getElementById('wcalbue-on').disabled=true;
		document.getElementById('wcalsob-on').disabled=true;
	}
	else
	{
		document.getElementById('wano-on').disabled=false;
		document.getElementById('wperiodo-on').disabled=false;
		document.getElementById('wcalmax-on').disabled=false;
		document.getElementById('wcalmin-on').disabled=false;
		document.getElementById('wcalmal-on').disabled=false;
		document.getElementById('wcalbue-on').disabled=false;
		document.getElementById('wcalsob-on').disabled=false;
	}
    });

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
<script type="text/javascript">
    function verSeccion(id){
        $("#"+id).toggle("normal");
    }

</script>
<body>

<?php

echo '<input type="hidden" id="wemp_pmla" name="wemp_pmla" value="'.$wemp_pmla.'" />';
echo '<input type="hidden" id="wtemaformato" name="wtemaformato" value="'.$wtemaformato.'" />';
echo '<input type="hidden" id="wtipoevaluacion" name="wtipoevaluaciondivEva" value="'.$wtipoevaluacion.'" />';
echo "<input type='hidden' id='wtema' value='".$wtema."'>";
echo "<input type='hidden' id='wcodigoevaluador' value='nada'>";

// se consultan todos los temas 	
	$q=  " SELECT  Forcod, Fordes,Fortip "
		."   FROM ".$wbasedato."_000042 " ;
	
	$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$numrowcom = mysql_num_rows($res);

$m=0;
if($numrowcom==0)
{
	echo "No es posible configurar periodos pues no hay temas agregados";
}
else
{
	echo"<table align='Center' width='400'>
		 <tr class ='encabezadoTabla' align='Left' width='400' >
		 <td>TEMAS</td>
		 </tr>";

	while($row =mysql_fetch_array($res))
	{	
		if (is_int ($m/2))
		{
		  $wcf="fila1";  // color de fondo de la fila
		}
		else
		{
		  $wcf="fila2"; // color de fondo de la fila
		}
		echo"<tr id='tdc-".$row['Forcod']."'  style='cursor:pointer;' onclick='seleccionTema(\"".$row['Forcod']."\",\"".utf8_encode($row['Fordes'])."\",\"".$wtema."\",\"".$row['Fortip']."\" )' class=".$wcf." align='Left' width='400' >
			 <td>".utf8_encode($row['Fordes'])."</td>
			 </tr>";
		$m++;
	}
	echo"</table>";
}	
	echo"<div id='divcco'></div>";
	echo "<br><br>";
	echo"<div id='divarbolevaluacion'></div>";

	

?>
</body>