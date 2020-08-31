<?php
include_once("conex.php");




include_once("root/comun.php");

include_once("funciones_talhuma.php");
$wbasedato = consultarPrefijo($conex, $wemp_pmla, $wtema);

// para evaluaciones de desempeño
if(isset($grabarfechayhora) AND $grabarfechayhora=='si')
{
$q = " DELETE FROM ".$wbasedato."_000056 "
   . "  WHERE Fecfor= '".$wformato."' ";
   
$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

$fecha =date("Y-m-d");
$q = "  INSERT INTO ".$wbasedato."_000056 "
			  ."            (Fecfor,Fecper,Fecano,Fecfin,Fecffi,Fechin,Fechfi,Fecest,Fecha_data,Medico,Seguridad)"
			  ."     VALUES ('".$wformato."','".$wnper."', '".$wnano."', '".$fechaini."','".$fechafin."','".$horaini."','".$horafin."','on','".$fecha."','".$wbasedato."','C-".$wbasedato."')";
$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());		
echo $q;	
return;
}
if( $cargarselecperiodo2 == 'si')
{
$q = "  SELECT Perper, Perano "
					."	FROM ".$wbasedato."_000009 "
				   . "  WHERE Perfor= '".$codigotema."' "
				   . "  AND  Perest='off' "  ;
				$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
echo "<option value='seleccione'>seleccione</option>";
while($row =mysql_fetch_array($res)) // se pintan todos los periodos de ese tema con la notas correspondientes
{
	echo"<option value='".$row['Perano']."-".$row['Perper']."'>".$row['Perano']."-".$row['Perper']."</option>";
}	

return;
}
if (isset($woperacion) AND $woperacion=='cargartablahoras')
{
$q = "SELECT Perper, Perano "
   . "  FROM ".$wbasedato."_000009 "
   . " WHERE Perfor = '".$wtemaformato."' "
   . "   AND Perest ='on' ";
 
$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
$row = mysql_fetch_array($res);

$wperiodo = $row['Perper'];
$wano = $row['Perano'];

$q = "SELECT Fecfor,Fecper,Fecano,Fecfin, Fechin,Fecffi,Fechfi"
	."  FROM ".$wbasedato."_000056 "
	." WHERE Fecfor = '".$wformato."' "
	."   AND Fecper = '".$wperiodo."' "
	."   AND Fecano = '".$wano."' ";
	
$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
			
			echo"<table>";
			echo"<tr class='encabezadoTabla'><td colspan='4'>Rango de tiempo en el cual se abre la proxima evaluacion</td></tr>";
			echo"<tr class='encabezadoTabla'>";
						/*<td align='Center'>Formato
						</td>
						<td align='Center' >Periodo
						</td>
						<td align='Center' >Año*/
						echo"</td>
						<td align='Center' >Fecha inicio
						</td>
						<td align='Center' >Hora inicio
						</td>
						<td align='Center' >Fecha Fin
						</td>
						<td align='Center' >Hora Fin
						</td>
					</tr>";
			while($row =mysql_fetch_array($res))
			{	
			  echo "<tr class='fila1'>";
			 /* echo"<td >".$row['Fecfor']."
			       </td>";
			  echo"<td  >".$row['Fecper']."
				  </td>";
			  echo"<td >".$row['Fecano']."
				  </td>";*/
			  echo "<td id='tdfechainicio'>".$row['Fecfin']."
				 </td>";
			  echo "<td id='tdhorainicio' >".$row['Fechin']."
			     </td>";
			  echo "<td id='tdfechafin'>".$row['Fecffi']."
			     </td>";
			  echo "<td id='tdhorafin' >".$row['Fechfi']."
			   </td>";
			  echo "</tr>"; 
			} 
			echo"</table>";
			return;

}
if ($sicopiadeesquema=='si')
{
$q= "INSERT INTO ".$wbasedato."_000058  (Medico,Fecha_data,Hora_data,Arecdr,Arecdo,Aretem,Arefor,Areper,Areano) 
								SELECT  t1.Medico,t1.Fecha_data,t1.Hora_data,t1.Arecdr,t1.Arecdo,t1.Aretem,t1.Arefor,'".$wperiodo."','".$wano."'  
								  FROM talhuma_000058 t1  
							 LEFT JOIN talhuma_000058 t2  ON  ( t1.Arecdo  = t2.Arecdo AND t2.Areper='".$wperiodo."'  AND t2.Areano='".$wano."' ) 
							INNER JOIN talhuma_000013 ON(t1.Arecdo = Ideuse)
							     WHERE t1.Areper='".$wperiodocopiar."' 
								   AND t1.Areano='".$wanoacopiar."'
								   AND t1.Aretem = '".$wtemaacopiar."'
								   AND t2.id IS NULL";
								   							   
echo $q;
$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
return;
}

if (isset($wtema) AND $wtema2=='si')
{
			$q = "   SELECT Perano, Perper,Calmax, Calmin , Calmal ,Calbue ,Calsob, Perest ,Perdes"
			   . "     FROM ".$wbasedato."_000009 , ".$wbasedato."_000034 "
			   . "    WHERE  Perano = Calano "
			   . "      AND  Perper = Calper"
			   . "      AND  Perfor = Calfor"
			   . "      AND  Perfor = '".$wtemaformato."' "
			   . " ORDER BY  Perest DESC ,Perano DESC , (Perper + 0) DESC " ;
			   
					
			$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$numrowcom = mysql_num_rows($res);

			echo"
				<br><br><table width='1000'>
				<tr width='1000' ><td align='Left'><a href='#null'  onclick='verSeccion(\"divppal\")'><font style='text-transform: uppercase;'>CONFIGURACI&Oacute;N DE PERIODOS DE  ".utf8_encode($wtemanombre)."</font></a></td></tr>
				</table>
				<table width='1000'>
				<tr width='1000' ><td>
				<div id='divppal' width='1000' align='center' class='borderDiv displ'>
				<div id='contendedorperiodo'>";
			echo'
			   
					<table id="nuevoperiodo" >
					<tr ><td colspan="7"><input type="button" id="botonagregar" value="Agregar Periodo" onclick="agregarPeriodo(\''.$wtema.'\')"/></td></tr>
					</table>
					
					<table>
					<tr><td><div id="nperiodo"></div></td></tr>
					</table>
					
					<table align="center">
					
					<tr align="center"><td>
					
					<table widht= >
					<tr class="encabezadoTabla">
						<td width="100" style="font-size:11pt;text-align:center" >A&ntilde;o</td>
						<td width="100" style="font-size:11pt;text-align:center" >Periodo</td>';
						/*<td width="100" style="font-size:11pt;text-align:center">Calfici&oacute;n <br>Maxima</td>
						<td width="100" style="font-size:11pt;text-align:center">Calfici&oacute;n <br>Minima</td>
						<td width="130" style="font-size:11pt;text-align:center">Calfici&oacute;n <br>Deficiente</td>
						<td width="120" style="font-size:11pt;text-align:center">Calfici&oacute;n <br> Buena</td>
						<td width="170" style="font-size:11pt;text-align:center">Calfici&oacute;n <br> Sobresaliente</td>*/
						echo'<td width="100" style="font-size:11pt;text-align:center">Descripcion</td>
						     <td width="100" style="font-size:11pt;text-align:center">Activo</td>
							
						<td width="100" style="font-size:11pt;text-align:center">Copiar Esquema</td>
					</tr>';
			$m=0;
			while($row =mysql_fetch_array($res))
				{
				   if ($row['Perest']=='off')
					   {
						$habilitado="disabled";  // color de fondo de la fila
					   }
					else
					   {
						$habilitado=""; // color de fondo de la fila
					   }
				  
				  if (is_int ($m/2))
				  {
					$wcf="fila1";  // color de fondo de la fila
				  }
				  else
				  {
					$wcf="fila2"; // color de fondo de la fila
				  }
				  echo' <tr >
							<td width=""  align= "center" class='.$wcf.' ><input '.$habilitado.' type="text" id="wano-'.$row['Perest'].'"  value="'.$row['Perano'].'"  maxlength="4" size="3" style="text-align:center" onblur="cambiadatos();"/></td>
							<td width="" align= "center" class='.$wcf.' ><input '.$habilitado.' type="text" id="wperiodo-'.$row['Perest'].'" value="'.$row['Perper'].'" maxlength="4" size="3" style="text-align:center" onblur="cambiadatos();"/></td>
							<td width="" align= "center" class='.$wcf.' >'.$row['Perdes'].'</td>';
							
							
							/*<td width="" align= "center" class='.$wcf.' ><input '.$habilitado.' type="text" id="wcalmax-'.$row['Perest'].'" value="'.$row['Calmax'].'" maxlength="3" size="3" style="text-align:center" onblur="cambiadatos();"/></td>
							<td width="" align= "center" class='.$wcf.' ><input '.$habilitado.' type="text" id="wcalmin-'.$row['Perest'].'" value="'.$row['Calmin'].'" maxlength="3" size="3" style="text-align:center" onblur="cambiadatos();"/></td>
							<td width="" align= "center" class='.$wcf.' >Menor a <input '.$habilitado.' type="text" id="wcalmal-'.$row['Perest'].'" value="'.$row['Calmal'].'" maxlength="3" style="text-align:center" size="3" onblur="cambiadatos();"/></td>
							<td width="" align= "center" class='.$wcf.' >Mayor a <input '.$habilitado.' type="text" id="wcalbue-'.$row['Perest'].'" value="'.$row['Calbue'].'" maxlength="3" style="text-align:center" size="3" onblur="cambiadatos();"/></td>
							<td width="" align= "center" class='.$wcf.' ><input  '.$habilitado.' type="text" id="wcalsob-'.$row['Perest'].'" value="'.$row['Calsob'].'" maxlength="3" size="3" style="text-align:center" onblur="cambiadatos();"/></td>*/
							echo'<td width="" align= "center" class='.$wcf.' >';
				  
				  if($row['Perest']=='off')
					{
							echo'<input type="checkbox"  id="checkbox-'.$row['Perano'].'-'.$row['Perper'].'-'.$row['Perest'].'"   '.$habilitado.' />';
					}
					else
					{
							echo'<input type="checkbox"  id="checkbox-'.$row['Perano'].'-'.$row['Perper'].'"  onclick="cambiarEstadoPeriodo(this);" checked '.$habilitado.' />';
					}
				 echo'  </td>';
				   if($row['Perest']=='on')
					{
						echo"<td><input type='button' Value = 'Copiar Esquema' onclick='fnMostrar(\"divcopiaesquema\")'></td>";
					}
					else
					{
					   echo"<td></td>";
					}
					echo"</tr>";
				
				
				  $m++;
				}	
			echo'</table></tr>
				</table></div></div></td></tr></table><br>';	
			echo  "<br><table align='center' width='1000'  >
					<tr><td align='Left'><a href='#null'  onclick='verSeccion(\"divEva\")'><font style='text-transform: uppercase;'>ABRIR FORMATOS PARA ".utf8_encode($wtemanombre)." POR PERIODO ACTIVO</font></a> </td></tr></table>
					
					<table  width='1000'><tr ><td><div id='divEva' width='1000' align='center' class='borderDiv displ'>";		
			$q = "   SELECT Forcod,Fordes, Forabr "
			   . "     FROM ".$wbasedato."_000002  "
			   . "    WHERE  Fortip = '".$wtemaformato."' "
			   . " ORDER BY  Forcod" ;
					
			$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
		
			echo'<table align="center" width="1000" >
					<tr  class="encabezadoTabla" ><td width="75">Estado</td>';
					if ($wtipoevaluacion=='04')
							{
								echo'<td class="encabezadoTabla" colspan="2">Formato</td></tr>';	
							}else
							{
								echo'<td class="encabezadoTabla" >Formato</td></tr>';
							}
					
					
			$m=0;
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
					
					echo'<tr class='.$wcf.'>';
					  if ($row['Forabr'] =='on')
						{		  
							echo'<td align="center" ><input type="checkbox"  id="'.$row['Forcod'].'"  onclick="cambiarEstado(this,\''.$wtema.'\' , \''.$wtipoevaluacion.'\' )" checked /></td>';
							
						}
					   else
						{		   
							echo'<td align="center" ><input type="checkbox"  id="'.$row['Forcod'].'" onclick="cambiarEstado(this, \''.$wtema.'\',\''.$wtipoevaluacion.'\' );" /></td>';
							
						}
							echo'<td align="Left">'.($row['Forcod']).' - '.($row['Fordes']).'</td>';
							if ($wtipoevaluacion=='04')
							{
								echo '<td><a style="float : right; cursor:pointer" onclick="cambiarfechasabiesto(\''.$row['Forcod'].'\',\''.$wtema.'\' , \''.$wtipoevaluacion.'\' )">Asignar fecha y hora para realizar evaluacion</a></td>';
							}
						echo'</tr>';
				 $m++;
				}	
			echo'</table></div></td></tr></table>';	
return;
}
// Se utiliza para agregar la estructura de ingreso de nuevo periodo
if(isset($nuevoperiodo) && $nuevoperiodo == 'si')
{
echo'
		<table id="snuevoperiodo">
		<tr class="encabezadoTabla">
			<td width="100" style="font-size:11pt;text-align:center" >A&ntilde;o</td>
			<td width="100" style="font-size:11pt;text-align:center" >Periodo</td>
			<td width="100" style="font-size:11pt;text-align:center" >Descripcion</td>';
			/*<td width="100" style="font-size:11pt;text-align:center">Calfici&oacute;n <br>Maxima</td>
			<td width="100" style="font-size:11pt;text-align:center">Calfici&oacute;n <br>Minima</td>
			<td width="130" style="font-size:11pt;text-align:center">Calfici&oacute;n <br>Deficiente</td>
			<td width="120" style="font-size:11pt;text-align:center">Calfici&oacute;n <br>Buena</td>
			<td width="170" style="font-size:11pt;text-align:center">Calfici&oacute;n <br>Sobresaliente</td>*/				
		echo'</tr>
		<tr class="encabezadoTabla">
			<td align="center" width="100" ><input type="text" id="nuevoano" maxlength="4" size="4"   value=""/></td>
			<td align="center" width="100" ><input type="text" id="nuevoperiod"  maxlength="4" size="4" value=""/></td>
			<td align="center" width="100" ><input type="text" id="nuevodes"  maxlength="50" size="50" value=""/></td>';
			/*<td align="center" width="100" ><input type="text" id="nuevocalmax" maxlength="3" size="4"  value=""/></td>
			<td align="center" width="100" ><input type="text" id="nuevocalmin" maxlength="3" size="4"  value="" /></td>
			<td align="center" width="130" >Menor a <input type="text" id="nuevocalmal" maxlength="3" size="3" value="" /></td>
			<td align="center" width="120" >Mayor a <input type="text" id="nuevocalbue" maxlength="3" size="3" value="" /></td>
			<td align="center" width="170" ><input type="text" id="nuevocalsob" maxlength="3" size="3"  value=""/></td>	*/
		echo'</tr>
		<tr >
			<td aling="center"><input type="button"  value="Grabar" onclick="Confirmarcopiadeesquemadeperiodo(\''.$wtema.'\');"/></td>
			<td aling="center"><input type="button"  value="Cancelar" onclick="CancelarPeriodo();"/></td>
		</tr>
		</table>
';		
return;
}
if(isset($copiaresquema) && $copiaresquema == 'si')
{
	
		$q = "  SELECT Perper, Perano "
			."	FROM ".$wbasedato."_000009 "
		   . "  WHERE Perfor= '".$wtemaformato."' ";
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	
	echo"<table align='center'>";
	echo "<tr>";
	echo "<td><img src='../../images/medical/root/alerta.gif'  width='40' height='40'></td>";
	echo "<td class='fila1'>Desea copiar el esquema de Arbol de Evaluacion de un periodo Anterior?</td>";
	echo "<td class='fila1' >Periodo<Select id='selectperiodoanterior'>";
	echo "<option value='seleccione'>seleccione</option>";
	while($row =mysql_fetch_array($res)) // se pintan todos los periodos de ese tema con la notas correspondientes
	{
		echo"<option value='".$row['Perano']."-".$row['Perper']."'>".$row['Perano']."-".$row['Perper']."</option>";
	}			
	echo "</select></td><tr></tr><td align='center' colspan='3'><input type='button' value='Copiar' onclick='sicopiar( \"".$wnano."\" ,\"".$wnper."\" ,\"".$wncalmax."\" ,\"".$wncalmin."\" ,\"".$wncalmal."\" ,\"".$wncalbue."\" ,\"".$wncalsob."\" ,\"".$wtemaformato."\" ,\"".$wtema."\",\"".$wndescripcion."\")'><input type='button' value='cancelar' onclick='nocopiar( \"".$wnano."\" ,\"".$wnper."\" ,\"".$wncalmax."\" ,\"".$wncalmin."\" ,\"".$wncalmal."\" ,\"".$wncalbue."\" ,\"".$wncalsob."\" ,\"".$wtemaformato."\" ,\"".$wtema."\",\"".$wndescripcion."\")'></td>";
	echo"</tr>";
	echo"</table>";
	return;
}
if(isset($grabaperiodo) && $grabaperiodo == 'si')
{	
	$wnfecha =date("Y-m-d");

	$q = " SELECT * "
	   ."    FROM  ".$wbasedato."_000009 "
	   . "  WHERE  Perano='".$wnano."' "
	   . "    AND  Perper= '".$wnper."' "
       . "    AND  Perfor= '".$wtemaformato."'	";
	   
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$numrowcom = mysql_num_rows($res);
	
	if ($numrowcom !=0)
	{
	  echo "Periodo ya esta grabado, Porfavor verifique sus datos";
	}
	else
	{
	
		// pone a los periodos de ese tema en off
		$q = " UPDATE  ".$wbasedato."_000009 "
		   . "    SET Perest='off' "
		   . "  WHERE Perfor= '".$wtemaformato."' ";
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		
		// pone a las calificaciones del tema  en off 
		$q = " UPDATE  ".$wbasedato."_000034 "
		   . "    SET Calest='off' "
		   . "  WHERE Calfor= '".$wtemaformato."' ";
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	
		// se inserta en la tabla de periodos el nuevo periodo  
		$q = "  INSERT INTO ".$wbasedato."_000009 "
			  ."            (Perano,Perper,Perfor,Fecha_data,Medico,Seguridad,Perdes)"
			  ."     VALUES ('".$wnano."','".$wnper."','".$wtemaformato."','".$wnfecha."','".$wbasedato."','C-".$wuse."','".$wndescripcion."')";
		
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	
		// se inserta en la tabla 34 las notas nuevas
		$q = "  INSERT INTO ".$wbasedato."_000034 "
			  ."            (Calmax,Calmin,Calmal,Calbue,Calsob,Calano,Calper,Calfor,Fecha_data,Medico,Seguridad)"
			  ."     VALUES ('".$wncalmax."','".$wncalmin."','".$wncalmal."','".$wncalbue."','".$wncalsob."','".$wnano."','".$wnper."','".$wtemaformato."',  '".$wnfecha."','".$wbasedato."','C-".$wuse."')";
		
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
		
		// se inserta en la tabla 48 el ano el periodo y el tema 
		$q = "  INSERT INTO ".$wbasedato."_000048 "
			  ."            (Nxtano,Nxtper,Nxttem,Fecha_data,Medico,Seguridad,Nxtgno)"
			  ."     VALUES ('".$wnano."','".$wnper."','".$wtemaformato."',  '".$wnfecha."','".$wbasedato."','C-".$wuse."','1')";
		
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
	}	
	   $q = "    SELECT Perano, Perper,Calmax, Calmin , Calmal ,Calbue ,Calsob ,Perest,Perdes"
	   . "         FROM ".$wbasedato."_000009 , ".$wbasedato."_000034 "
	   . "        WHERE  Perano = Calano "
	   . "          AND  Perper = Calper"
	   . "          AND  Perfor = Calfor"
	   . "          AND  Perfor = '".$wtemaformato."' "
	   . "     ORDER BY  Perest DESC, Perano DESC, (Perper + 0)  DESC" ;
	   	
		$res=  mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numrowcom = mysql_num_rows($res);
	
		if($numrowcom !=0)
		{
			echo'
			<table id="nuevoperiodo" >
			<tr ><td colspan="7"><input type="button" id="botonagregar" value="Agregar Periodo" onclick="agregarPeriodo(\''.$wtema.'\');"/></td></tr>
			</table>
			<table>
			<tr><td><div id="nperiodo"></div></td></tr>
			</table>
			<table align="center">
			<tr align="center"><td>
			<table widht= >
			<tr class="encabezadoTabla">
				<td width="100" style="font-size:11pt;text-align:center" >A&ntilde;o</td>
				<td width="100" style="font-size:11pt;text-align:center" >Periodo</td>';
				/*<td width="100" style="font-size:11pt;text-align:center">Calfici&oacute;n <br>Maxima</td>
				<td width="100" style="font-size:11pt;text-align:center">Calfici&oacute;n <br>Minima</td>
				<td width="130" style="font-size:11pt;text-align:center">Calfici&oacute;n <br>Deficiente</td>
				<td width="120" style="font-size:11pt;text-align:center">Calfici&oacute;n <br>Buena</td>
				<td width="170" style="font-size:11pt;text-align:center">Calfici&oacute;n <br> Sobresaliente</td>*/
				echo'<td width="100" style="font-size:11pt;text-align:center">Descripcion</td>
					 <td width="100" style="font-size:11pt;text-align:center">Activo</td>
					 
				<td width="100" style="font-size:11pt;text-align:center">Copiar Esquema</td>
			</tr>';
			$m=0;
			while($row =mysql_fetch_array($res)) // se pintan todos los periodos de ese tema con la notas correspondientes
			{
			   if ($row['Perest']=='off')
				   {
					$habilitado="disabled";  // color de fondo de la fila
				   }
				else
				   {
					$habilitado=""; // color de fondo de la fila
				   }
			  
			  if (is_int ($m/2))
			  {
				$wcf="fila1";  // color de fondo de la fila
			  }
			  else
			  {
				$wcf="fila2"; // color de fondo de la fila
			  }
			  echo' <tr >
						<td width=""  align= "center" class='.$wcf.' ><input '.$habilitado.' type="text" id="wano-'.$row['Perest'].'"  value="'.$row['Perano'].'"  maxlength="4" size="4" style="text-align:center" onblur="cambiadatos();"/></td>
						<td width="" align= "center" class='.$wcf.' ><input '.$habilitado.' type="text" id="wperiodo-'.$row['Perest'].'" value="'.$row['Perper'].'" maxlength="4" size="4" style="text-align:center" onblur="cambiadatos();"/></td>
						<td width="" align= "center" class='.$wcf.' >'.$row['Perdes'].'</td>';
					
						/*<td width="" align= "center" class='.$wcf.' ><input '.$habilitado.' type="text" id="wcalmax-'.$row['Perest'].'" value="'.$row['Calmax'].'" maxlength="3" size="3" style="text-align:center" onblur="cambiadatos();"/></td>
						<td width="" align= "center" class='.$wcf.' ><input '.$habilitado.' type="text" id="wcalmin-'.$row['Perest'].'" value="'.$row['Calmin'].'" maxlength="3" size="3" style="text-align:center" onblur="cambiadatos();"/></td>
						<td width="" align= "center" class='.$wcf.' >Menor a <input '.$habilitado.' type="text" id="wcalmal-'.$row['Perest'].'" value="'.$row['Calmal'].'" maxlength="3" style="text-align:center" size="3" onblur="cambiadatos();"/></td>
						<td width="" align= "center" class='.$wcf.' >Mayor a <input '.$habilitado.' type="text" id="wcalbue-'.$row['Perest'].'" value="'.$row['Calbue'].'" maxlength="3"  style="text-align:center" size="3" onblur="cambiadatos();"/></td>
						<td width="" align= "center" class='.$wcf.' ><input  '.$habilitado.' type="text" id="wcalsob-'.$row['Perest'].'" value="'.$row['Calsob'].'" maxlength="3" size="3"  style="text-align:center" onblur="cambiadatos();"/></td>*/
						echo'<td width="" align= "center" class='.$wcf.' >';
			  
			  if($row['Perest']=='off')
				{
						echo'<input type="checkbox"  id="checkbox-'.$row['Perano'].'-'.$row['Perper'].'-'.$row['Perest'].'"   '.$habilitado.' />';
				}
				else
				{
						echo'<input type="checkbox"  id="checkbox-'.$row['Perano'].'-'.$row['Perper'].'"  onclick="cambiarEstadoPeriodo(this);" checked '.$habilitado.' />';
				}
				
				
			 echo'  </td>';
			 
			 if($row['Perest']=='on')
					{
						echo"<td><input type='button' Value = 'Copiar Esquema' onclick='fnMostrar(\"divcopiaesquema\")'></td>";
					}
					else
					{
					   echo"<td></td>";
					}
					echo'</tr>';
			 $m++;
			}
echo'</table></td></tr>
	</table><br>';						
}
return;
}

if(isset($cambiaDatos) && $cambiaDatos == 'si')
{
	$q = " SELECT * "
	   . "   FROM  ".$wbasedato."_000009 "
	   . "  WHERE  Perano='".$wnano."' "
	   . "    AND  Perper= '".$wnper."' "
       . "    AND  Perfor= '".$wtemaformato."'	"
	   . "    AND  Perest='off' ";
	
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$numrowcom = mysql_num_rows($res);
	
	if ($numrowcom !=0)
	{
	  echo "Periodo ya esta grabado, Porfavor verifique sus datos";
	}
	else
	{
		$q =     " UPDATE  ".$wbasedato."_000009 "
			   . "    SET Perper= '".$wnper."', "
			   . "        Perano= '".$wnano."' "
			   . "  WHERE Perfor= '".$wtemaformato."' "
			   ."     AND Perest= 'on' ";
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		
		$q =     " UPDATE  ".$wbasedato."_000034 "
			   . "    SET Calper= '".$wnper."', "
			   . "        Calano= '".$wnano."' ,"
			   . "        Calmax= '".$wncalmax."' ,"
			   . "        Calmin= '".$wncalmin."' ,"
			   . "        Calmal= '".$wncalmal."' ,"
			   . "        Calbue= '".$wncalbue."' ,"
			   . "        Calsob= '".$wncalsob."' "
			   . "  WHERE Calfor= '".$wtemaformato."' "
			   ."     AND Calest= 'on' ";
		$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		
	
	}
	return;
}
if(isset($cambiarestado) && $cambiarestado == 'si')
{	
$q = "     SELECT Forabr "
	   ."    FROM  ".$wbasedato."_000002 "
	   . "  WHERE  Forcod='".$wformato."' ";
	  
	
	$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row =mysql_fetch_array($res);
	
	if($row['Forabr'] == 'on')
	{
	 $q = "UPDATE ".$wbasedato."_000002 "
	   . "    SET Forabr='off'"
	   . "  WHERE  Forcod='".$wformato."' ";
	 $res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	
	}
	else
	{
	 $q = "UPDATE ".$wbasedato."_000002 "
	   . "    SET Forabr='on'"
	   . "  WHERE  Forcod='".$wformato."' ";
	 $res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	}
}

if(isset($cambiarestadoperiodo) && $cambiarestadoperiodo == 'si')
{	
$q = " SELECT * "
   ."    FROM  ".$wbasedato."_000009 "
   . "  WHERE  Perano='".$wnano."' "
   . "    AND  Perper= '".$wnper."' "
   . "    AND  Perfor= '".$wtemaformato."'	"
   . "    AND  Perest= 'on'	"; 
	
$res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
$numrowcom = mysql_num_rows($res);
	
if ($numrowcom !=0)
 {
	 $q = "UPDATE ".$wbasedato."_000009 "
	   . "    SET Perest='off'"
	   . "  WHERE  Perano='".$wano."' "
	   ."     AND  Perper='".$wperiodo."'";
	 $res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
 }
else 
{
	 $q = "UPDATE ".$wbasedato."_000009 "
	   . "    SET Perest='on'"
	   . "  WHERE  Perano='".$wano."' "
	   ."     AND  Perper='".$wperiodo."'";
	 $res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
 }

}
//----------------------------------------------------------------------------------------------
?>
<head>
  <title>Configuracion de  Periodos</title>
  
</head>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/smoothness/jquery-ui-1.7.2.custom.css" rel="stylesheet"/>

<script type="text/javascript">

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
	var params = 'configuracionperiodos.php?consultaAjax=&wemp_pmla='+empleado+'&grabarfechayhora=si&wnano='+nano+'&wnper='+nperiodo+'&wformato='+formato+'&fechaini='+fechaini+'&fechafin='+fechafin+'&horaini='+horaini+'&horafin='+horafin+'&wtema='+tema;
		$.get(params, function(data) {
		$("#tdfechainicio").text(fechaini);
		$("#tdhorainicio").text(horaini);
		$("#tdfechafin").text(fechafin);
		$("#tdhorafin").text(horafin);
		
		});
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
	
	var empleado=document.getElementById('wemp_pmla').value;
	document.getElementById("wtemaformato").value = codigotema; 
	var params = 'configuracionperiodos.php?consultaAjax=&wemp_pmla='+empleado+'&wtemaformato='+codigotema+'&wtema2=si&wtemanombre='+nombretema+'&wtema='+tema+'&wtipoevaluacion='+tipoevaluacion;
    $.get(params, function(data) {
	$('#contendedorppal').html(data);
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

function fnMostrar( celda )
{

		$.post("configuracionperiodos.php",
            {
                consultaAjax: '',
                wemp_pmla   : $("#wemp_pmla").val(),
                wtema       : $("#wtema").val(),
				cargarselecperiodo2 : 'si',
				codigotema : document.getElementById("wtemaformato").value,
				cano	: document.getElementById('wano-on').value,
				cperiodo : document.getElementById('wperiodo-on').value
				 
			},function(data) {
				$('#selectperiodoanterior2').html(data);
			});
			
		
		
		if( $('#'+celda ) ){
			$.blockUI({ message: $('#'+celda ), 
							css: { left: ( $(window).width() - 800 )/2 +'px', 
								  top: '200px',
								  width: '800px'
								 } 
					  });	
		}
		
}

function cambiadatos ()
{
	var empleado=document.getElementById('wemp_pmla').value;
	var nano=document.getElementById('wano-on').value;
	var nperiodo=document.getElementById('wperiodo-on').value;
	var ncalmax=0;
	var ncalmin=0;
	var ncalmal=0;
	var ncalbue=0;
	var ncalsob=0;
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
function sicopiar(nano ,nperiodo,ncalmax,ncalmin,ncalmal,ncalbue,ncalsob,codtema,tema,nombre)
{

var empleado=document.getElementById('wemp_pmla').value;
var params = 'configuracionperiodos.php?consultaAjax=&wemp_pmla='+empleado+'&grabaperiodo=si&wnano='+nano+'&wnper='+nperiodo+'&wncalmax='+ncalmax+'&wncalmin='+ncalmin+'&wncalmal='+ncalmal+'&wncalbue='+ncalbue+'&wncalsob='+ncalsob+'&wtemaformato='+codtema+'&wtema='+tema+'&wndescripcion='+nombre;
		$.post(params, function(data) {
		$('#contendedorperiodo').html(data);
		});

var anoacopiar = $("#selectperiodoanterior").val();
    anoacopiar = anoacopiar.split("-"); 
var anocopia = anoacopiar[0];
var percopia = anoacopiar[1];
	$.get("configuracionperiodos.php",
				{
					consultaAjax 	: '',
					sicopiadeesquema : 'si',
					wemp_pmla		: $('#wemp_pmla').val(),
					wtema           : $('#wtema').val(),
					wuse			: $('#wuse').val(),
					wperiodocopiar : percopia, 
					wanoacopiar:     anocopia,
					wtemaacopiar:    codtema,
					wtema : tema,
					wano : nano,
					wperiodo : nperiodo
				});
		
		
}
function  sicopiar2(tema)
{

if($('#selectperiodoanterior2').val()=='seleccione'){
alert("Debe seleccionar algun periodo");
}
else
{

var nano=document.getElementById('wano-on').value;
var nperiodo=document.getElementById('wperiodo-on').value;
var anoacopiar = $("#selectperiodoanterior2").val();
    anoacopiar = anoacopiar.split("-"); 
var anocopia = anoacopiar[0];
var percopia = anoacopiar[1];
	$.get("configuracionperiodos.php",
				{
					consultaAjax 	: '',
					sicopiadeesquema : 'si',
					wemp_pmla		: $('#wemp_pmla').val(),
					wtema           : $('#wtema').val(),
					wuse			: $('#wuse').val(),
					wperiodocopiar : percopia, 
					wanoacopiar:     anocopia,
					wtemaacopiar:    document.getElementById("wtemaformato").value,
					wtema : tema,
					wano : nano,
					wperiodo : nperiodo
				});
}

}

function nocopiar(nano ,nperiodo,ncalmax,ncalmin,ncalmal,ncalbue,ncalsob,codtema,tema,nombre)
{
var empleado=document.getElementById('wemp_pmla').value;
var params = 'configuracionperiodos.php?consultaAjax=&wemp_pmla='+empleado+'&grabaperiodo=si&wnano='+nano+'&wnper='+nperiodo+'&wncalmax='+ncalmax+'&wncalmin='+ncalmin+'&wncalmal='+ncalmal+'&wncalbue='+ncalbue+'&wncalsob='+ncalsob+'&wtemaformato='+codtema+'&wtema='+tema+'&wndescripcion='+nombre;
		$.post(params, function(data) {
		$('#contendedorperiodo').html(data);
		});
		
		
}

function Confirmarcopiadeesquemadeperiodo(tema)
{

	var empleado=document.getElementById('wemp_pmla').value;
	var nano=document.getElementById('nuevoano').value;
	var nperiodo=document.getElementById('nuevoperiod').value;
	var ncalmax= 0;
	var ncalmin= 0;
	var ncalmal= 0;
	var ncalbue= 0;
	var ncalsob= 0;
	var codtema = document.getElementById("wtemaformato").value
	var ndescripcion=document.getElementById('nuevodes').value;
	

	var params = 'configuracionperiodos.php?consultaAjax=&wemp_pmla='+empleado+'&copiaresquema=si&wnano='+nano+'&wnper='+nperiodo+'&wncalmax='+ncalmax+'&wncalmin='+ncalmin+'&wncalmal='+ncalmal+'&wncalbue='+ncalbue+'&wncalsob='+ncalsob+'&wtemaformato='+codtema+'&wtema='+tema+'&wndescripcion='+ndescripcion;
		$.post(params, function(data) {
		$('#contendedorperiodo').html(data);
		});
		
}


function GrabarPeriodo(tema)
{
	var empleado=document.getElementById('wemp_pmla').value;
	var nano=document.getElementById('nuevoano').value;
	var nperiodo=document.getElementById('nuevoperiod').value;
	
	
	
	var ncalmax=0;
	var ncalmin=0;
	var ncalmal=0;
	var ncalbue=0;
	var ncalsob=0;
	var codtema = document.getElementById("wtemaformato").value
	var params = 'configuracionperiodos.php?consultaAjax=&wemp_pmla='+empleado+'&grabaperiodo=si&wnano='+nano+'&wnper='+nperiodo+'&wncalmax='+ncalmax+'&wncalmin='+ncalmin+'&wncalmal='+ncalmal+'&wncalbue='+ncalbue+'&wncalsob='+ncalsob+'&wtemaformato='+codtema+'&wtema='+tema+'&wndescripcion='+ndescripcion;
		$.post(params, function(data) {
		$('#contendedorperiodo').html(data);
		});
}

function cambiarEstado(elemento,tema,tipoevaluacion)
{

var empleado=document.getElementById('wemp_pmla').value;
var params = 'configuracionperiodos.php?consultaAjax=&wemp_pmla='+empleado+'&cambiarestado=si&wformato='+elemento.id+'&wtema='+tema;
    $.get(params, function(data) {  
    });
}


function cambiarfechasabiesto(elemento,tema,tipoevaluacion)
{

var empleado=document.getElementById('wemp_pmla').value;
var formato=elemento;
var codtema = document.getElementById("wtemaformato").value	
	agregarabrir = 'agregarabrir';
	var params = 'configuracionperiodos.php?consultaAjax=&wemp_pmla='+empleado+'&wtema='+tema+'&wtemaformato='+codtema+'&woperacion=cargartablahoras&wformato='+formato;
    $.post(params, function(data) {
	$('#divhoras').html(data);
    });
	fnMostrar2 (agregarabrir);
	$('#formatoabierto').val(elemento);


var empleado=document.getElementById('wemp_pmla').value;
var params = 'configuracionperiodos.php?consultaAjax=&wemp_pmla='+empleado+'&cambiarestado=si&wformato='+elemento+'&wtema='+tema;
    $.get(params, function(data) {  
    });

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
  /*********************************************************
   *         CONFIGURACION DE PERIODOS Y CALIFICACIONES    *
   *                                                       *
   *     				                        		   *
   *********************************************************/
//==================================================================================================================================
//PROGRAMA                   : arbolrelaciones.php
//AUTOR                      : Felipe Alvarez Sanchez
//
//FECHA CREACION             : Julio 27 de 2012
//FECHA ULTIMA ACTUALIZACION :
 
//DESCRIPCION
//========================================================================================================================================\\
//========================================================================================================================================\\
//     Programa usado para configurar los periodos y las notas correspondientes a ellos           \\        
//========================================================================================================================================\\
//========================================================================================================================================\\
//                                                                           \\
//========================================================================================================================================\\
echo '<input type="hidden" id="wemp_pmla" name="wemp_pmla" value="'.$wemp_pmla.'" />';
echo '<input type="hidden" id="wtemaformato" name="wtemaformato" value="'.$wtemaformato.'" />';
echo '<input type="hidden" id="wtipoevaluacion" name="wtipoevaluaciondivEva" value="'.$wtipoevaluacion.'" />';
echo '<input type="hidden" id="wtema" name="wtema" value="'.$wtema.'" />';

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
	echo"<table width='400'>
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
echo"<div id='contendedorppal'></div>";
echo "<div id='agregarabrir' class='fila2' align='middle'  style='display:none;width:100%;cursor:default' >";
			  $vectorhoras ='';
			  $e=6;
			  $i=1;
			  while ($e < 20)
			  {
				   if($e<10)
				   {
					$vectorhoras[$i]="0".$e.":00:00";
				   }else
				   {
					$vectorhoras[$i]=$e.":00:00";
				   }
				   $e++;
				   $i++;
			  }
			  echo "<br><br>
			  <input type='hidden' value='' id='formatoabierto'>";
			  echo"<div id='divhoras'>";
			  echo"</div>";
			  echo "<br><br>";
			  echo"<table style='border:#2A5DB0 1px solid'>";
			  echo"<tr>";
			  echo"<td colspan='4' class='encabezadoTabla' align='center'>CAMBIAR FECHA DE APERTURA Y CIERRE";
			  echo"</td>";
			  echo"</tr>";
			 
			  echo"<tr class='encabezadoTabla'>
						<td align='Center' >Fecha inicio
						</td>
						<td align='Center' >Hora inicio
						</td>
						<td align='Center' >Fecha Fin
						</td>
						<td align='Center' >Hora Fin
						</td>
					</tr>";
			  echo "<tr>";
			  echo "<td>";
			  echo"<div id='idatepicker'></div>";
			  echo "</td>";
			  echo "<td>";
			  echo "<select id='hidatepicker'>";
			  $h=1;
			  while($h<=count($vectorhoras))
			  {
				 echo "<option value='".$vectorhoras[$h]."'> ".$vectorhoras[$h]."</option>";
				 $h++;
			  }
			  echo "</select>";
			  echo "</td>";
			  echo "<td><div id='fdatepicker'></div>";
			  echo "</td>";
			  echo "<td>";
			  echo "<select id='hfdatepicker'>";
			  $h=1;
			  while($h<=count($vectorhoras))
			  {
				
				 echo "<option value='".$vectorhoras[$h]."'> ".$vectorhoras[$h]."</option>";
				  $h++;
			  }	 
			  echo "</select>";
			  echo "</td>";
			  echo "</tr>"; 
			  echo"<tr>";
			  echo"<td colspan='7' align='center'>";
			  echo"<input type='button' value='Grabar' onClick='grabarfechayhora(\"".$wtema."\")'>";
			  echo"<input type='button' value='Cerrar' onclick='$.unblockUI(); removepicker();'>";
			  echo"</td>";
			  echo "</tr>";
		      echo"</table >";
	          echo"<br><br>";
			  echo"</div>";	
			  
			  echo "<div id='divcopiaesquema' class='fila2' align='middle'  style='display:none;width:100%;cursor:default' >";
			  // pone a los periodos de ese tema en off
				
				
			    echo"<br><br>";
				echo"<br><br>";
				echo"<table align='center'>";
				echo"<tr>";
				echo "<td class='encabezadoTabla' align='center' colspan='2'>Copia de esquema</td>";
				echo "</tr>";
				echo"<tr>";
				echo "<td class='fila1'>Desea copiar el esquema de Arbol de Evaluacion de un periodo Anterior?</td>";
				echo "<td class='fila1' >Periodo<Select id='selectperiodoanterior2'>";		
				echo "</select></td>";
				echo"</tr>";
				//<td><input type='button' value='Copiar' onclick='sicopiar( \"".$wnano."\" ,\"".$wnper."\" ,\"".$wncalmax."\" ,\"".$wncalmin."\" ,\"".$wncalmal."\" ,\"".$wncalbue."\" ,\"".$wncalsob."\" ,\"".$wtemaformato."\" ,\"".$wtema."\")'></td><td><input type='button' value='cancelar' onclick='nocopiar( \"".$wnano."\" ,\"".$wnper."\" ,\"".$wncalmax."\" ,\"".$wncalmin."\" ,\"".$wncalmal."\" ,\"".$wncalbue."\" ,\"".$wncalsob."\" ,\"".$wtemaformato."\" ,\"".$wtema."\")'></td>
			    echo"<tr>";
			    echo"<td colspan='7' align='center'>";
			    echo"<input type='button' value='Grabar' onClick='sicopiar2(\"".$wtema."\"); $.unblockUI();'>";
			    echo"<input type='button' value='Cerrar' onclick='$.unblockUI()'>";
			    echo"</td>";
			    echo "</tr>";
		        echo"</table >";
	            echo"<br><br>";
			    echo"</div>";	
?>
</body>