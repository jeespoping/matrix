<head>
  <title>REPORTE APLICACION Y ADMINISTRACION DE INSUMOS Y MEDICAMENTOS</title>
</head>
<style type="text/css">
        
        .tipo3V{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3V:hover {color: #000066; background: #999999;}
		
		.campoObligatorio{
			border-style:solid;
			border-color:red;
			border-width:1px;
		}
		
    </style>
<body BGCOLOR="">
<BODY TEXT="#000000">
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>
<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>	
<script type="text/javascript">
	function cerrarVentana()
	 {
      window.close()		  
     }
	 
	 
	 function enter()
	{
	 document.forms.hoja.submit();
	}
	
	function rep_artaplixpac (filtro ,historia ,wemp ,ingreso, basedato, articulo, ret , wnomser, whabitacion , wpaciente  ,wfinal,wfegreso)
	{
		
		var tipoarticulo = $("#tipoarticulo").val();
		
		if($("#extenointerno").val()=='')
		{
			$("#extenointerno").addClass('campoObligatorio');
			//alert("");
			jAlert("<span><br>Debe seleccionar tipo de Uso</span>", "Mensaje");
			
		}
		else
		{
			window.location = "rep_artaplixpac.php?wfil="+filtro+"&whis="+historia+"&wemp="+wemp+"&wing="+ingreso+"&wbasedato="+basedato+"&warticulo="+articulo+"&ret="+ret+"&tipoarticulo="+tipoarticulo+"&externointerno="+$("#extenointerno").val()+"&nomservicio="+wnomser+"&whabitacion="+whabitacion+"&wpaciente="+wpaciente+"&wfinal="+wfinal+"&wfegreso="+wfegreso;
		}
		// href='rep_artaplixpac.php?wfil=".$wfil ."&whis=".$whis."&wemp=".$wemp_pmla."&wing=".$wing."&wbasedato=".$wbasedato."&warticulo=".$wart."&ret=1''
		
	}
	
	function ejecutar(wemp_pmla)
    {
	
	var whis = document.getElementById( "whis" ).value;
	var wing = document.getElementById( "wing" ).value;
	
	if (whis=='')
		{
		alert('Debe escribir una historia');
		return false;
		}
	if (wing =='')
		{
		alert('Debe escribir un ingreso');
		return false;
		}
	var path="/matrix/movhos/reportes/Hoja_medicamentos_auditores.php?wemp_pmla="+wemp_pmla+"&whis="+whis+"&wing="+wing;
    window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
    } 
</script>
<?php
include_once("conex.php");
  /***************************************************
	*	  REPORTE DE ADMINISTRACION DE MEDICAMENTOS  *
	*	  Y MATERIAL MEDICO QX POR SERVICIO O UNIDAD *
	*	        RESUMIDO PARA LOS AUDITORES          *
	*				CONEX, FREE => OK				 *
	**************************************************/
	
	// ==========================================================================================================================================
// M O D I F I C A C I O N E S 	 
// ==========================================================================================================================================
// Junio 10 del 2019:  Edwin MG : Se hacen cambios para mejorar la velocidad del reporte, guardando los datos en un array y luego mostrarlos
// Marzo 14 del 2019:  Arleyda I.C. : Migración realizada
// Julio 19 de 2017 :  Felipe Alvarez :
// Se hacen pequeños cambios para que el programa tenga un filtro adicional, (externo e interno) si es escogido el interno , se traen cuatro columnas mas
// si se escoge externo muestra la tabla de detalle de medicamento  mas resumida. Ademas de esto se trae mas informacion del paciente (habitacion, nombre completo)
// ==========================================================================================================================================
//Abril 17 de 2012: jerson Trujillo
//- Se optimiza el codigo en la utilizacion de los filtros de consulta para el query principal 
// ==========================================================================================================================================
// Diciembre 12 de 2011 :   Ing. Santiago Rivera Botero
// ==========================================================================================================================================
// - Se modifica el Query que trae la consulta de medicamentos aplicados ya que no estaba consultando con la tabla central de mezcla
// - Se adicionó un campo para filtar por medicamentos, insumos o ambos
// - Se eliminó el checkbox que se encontraba en la columna APROVADO  y se reemplazo por un link que nos direcciona al reporte (CONSULTA DE 	INSUMOS APLICADOS X PACIENTE)
// ==========================================================================================================================================
// Diciembre 13 de 2011 :   Ing. Santiago Rivera Botero
// ==========================================================================================================================================
// Se valido para que cuando carguen y busquen historias médicas solo muestre las de el usuario de la empresa correspondiente(validar_usuario)
// ==========================================================================================================================================         
// Febrero 07 de 2011 : Ing. Santiago Rivera Botero
// ==========================================================================================================================================
// Se corrige el campo de donde se estaba tomando la fecha de ingreso que era de root_000036 y debe ser de movhos_000016 (Linea 293) 

function validar_usuario($his, $ing)
	{
	 global $conex;
	 global $wbasedato;
	 global $wusuario;
	 global $whce;
	 global $wfecha;
	  
	 $wfecha=date("Y-m-d");
	  
	 $q = " SELECT ingres "
		 ."   FROM ".$wbasedato."_000016 "
		 ."  WHERE inghis = '".$his."'"
		 ."    AND inging = '".$ing."'";
	 $res = mysql_query($q,$conex) or die ("Error 11: ".mysql_errno()." - ".mysql_error());
	 $row = mysql_fetch_array($res);	  
		
	 $wempresa_del_paciente=$row[0];		
	
	 $q = " SELECT COUNT(*) "
	     ."   FROM ".$whce."_000020, ".$whce."_000019, ".$whce."_000025 "
		 ."  WHERE usucod     = '".$wusuario."'"
		 ."    AND usurol     = rolcod "
		 ."    AND usufve    >= '".$wfecha."'"
		 ."    AND usuest     = 'on' "
	     ."    AND (((rolemp  = empcod "
	     ."    AND   INSTR(empemp,'".$wempresa_del_paciente."') > 0 ) "
	     ."     OR rolatr     = 'on') "
	     ."     OR rolemp    in ('%','*') ) "
	     ."    AND rolest     = 'on' ";
	 $res = mysql_query($q,$conex) or die ("Error 12: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());  
	 $row = mysql_fetch_array($res);

	 if ($row[0] > 0)
		return true;
	   else
		  {
		   //Si hace join en este query es porque el usuario es un empleado
		   $q = " SELECT COUNT(*) "
			   ."   FROM usuarios, ".$wbasedato."_000011 "
			   ."  WHERE codigo  = '".$wusuario."'"
			   ."    AND ccostos = ccocod ";
		   $res = mysql_query($q,$conex) or die ("Error 1: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());  
		   $row = mysql_fetch_array($res);

		   if ($row[0] > 0)
			 return true;
			 else              
				return false;
		   }
	}

	
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
 	include_once("root/comun.php");
	
    $key = substr($user,2,strlen($user));
		
	if (strpos($user,"-") > 0)
          $wuser = substr($user,(strpos($user,"-")+1),strlen($user)); 
          
    $wusuario = $wuser;
    $wactualiz = "2017-07-22";      
           
    $wfecha=date("Y-m-d");   
    $whora = (string)date("H:i:s");	                                                           
	
    // echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
    
    //=======================================================================================================
    //=======================================================================================================
    //CON ESTO TRAIGO LA EMPRESA Y TODOS LOS CAMPOS NECESARIOS DE LA EMPRESA
    $q = " SELECT empdes "
        ."   FROM root_000050 "
        ."  WHERE empcod = '".$wemp_pmla."'"
        ."    AND empest = 'on' ";
    $res = mysql_query($q,$conex) or die ("Error 2: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $row = mysql_fetch_array($res); 
    $wnominst=$row[0];
  
    //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos    
    $q = " SELECT detapl, detval "
        ."   FROM root_000050, root_000051 "
        ."  WHERE empcod = '".$wemp_pmla."'"
        ."    AND empest = 'on' "
        ."    AND empcod = detemp "; 
    $res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num = mysql_num_rows($res); 
    if ($num > 0 )
       {
	    for ($i=1;$i<=$num;$i++)
	       {   
	        $row = mysql_fetch_array($res);
	      
	        if ($row[0] == "cenmez")
	           $wcenmez =  $row[1];
	         
	        if ($row[0] == "afinidad")
	           $wafinidad= $row[1];
	         
	        if ($row[0] == "movhos")
	           $wbasedato= $row[1];
	         
	        if ($row[0] == "tabcco")
	           $wtabcco =  $row[1];
			
			if (strtoupper($row[0]) == "HCE")
	           $whce=$row[1];
           }  
      }
     else
        echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";            
    //=======================================================================================================
    //=======================================================================================================      
	global $wfil;
	global $wtarget;	
	global $externointerno;
    echo "<form name='hoja' action='Hoja_medicamentos_auditores.php' method=post>";
	
	echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";

    if (!isset($whis) or !isset($wing))
       { 
        encabezado("APLICACION Y ADMINISTRACION DE INSUMOS Y MEDICAMENTOS", $wactualiz, 'clinica');
        
        echo "<center><table>";
        
        //Esto no lo hago porque este reporte es para los auditores y ellos podrian seleccionar cualquier historia activa o inactiva	       
	    $q = " SELECT ubihac, ubihis, ubiing, pacno1, pacno2, pacap1, pacap2, ubifad, root_000036.fecha_data, ingres "
	         ."   FROM ".$wbasedato."_000016, ".$wbasedato."_000018, root_000037, root_000036, ".$whce."_000020, ".$whce."_000019, ".$whce."_000025 "
	         ."  WHERE ubihis  = orihis "
	         ."    AND ubiing  = oriing "
	         ."    AND oriori  = '".$wemp_pmla."'"
	         ."    AND oriced  = pacced "
	         ."    AND ubiald != 'on' "              //Solo listo los que estan activos
			 ."    AND ubihis  = inghis "
			 ."    AND ubiing  = inging "
			 ."    AND usucod     = '".$wusuario."'"
			 ."    AND usurol     = rolcod "
			 ."    AND usufve    >= '".$wfecha."'"
			 ."    AND usuest     = 'on' "
			 ."    AND (((rolemp  = empcod "
			 ."    AND   INSTR(empemp,ingres) > 0 ) "
			 ."     OR rolatr     = 'on') "
	         ."     OR rolemp    in ('%','*') ) "
	         ."    AND rolest     = 'on' "
		     ."  ORDER BY 1, 4, 5 "
			 ."	";
			 
	     $res = mysql_query($q,$conex) or die ("Error 4: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	     $wnr = mysql_num_rows($res);		 
	                
	     echo "<tr class=encabezadoTabla>";
	     echo "<th>Habitacion</th>";
	     echo "<th>Historia</th>";
	     echo "<th>Ingreso</th>";
	     echo "<th>Paciente</th>";
		 echo "<th>Responsable</th>";
	     echo "</tr>";
	                               
	     $whabant = "";
		 for ($i=1;$i<=$wnr;$i++)
		     {
			  if ($i % 2 == 0)
			   $wclass = "fila1";
			  else
			     $wclass = "fila2";
			        
			  $row = mysql_fetch_row($res);
			      
			  $whab = $row[0];
		      $whis = $row[1];
		      $wing = $row[2];
		      $wpac = $row[3]." ".$row[4]." ".$row[5]." ".$row[6];
		      $wegr = $row[7];      //Fecha de Egreso
		      $wfin = $row[8];      //Fecha de ingreso
			  $wres = $row[9];      //Responsable
		            	            
		      if ($whabant != $whab)
		         {
			      echo "<tr class=".$wclass.">";
			      echo "<td align=center>".$whab."</td>";
			      echo "<td align=center>".$whis."</td>";
			      echo "<td align=center>".$wing."</td>";
			      echo "<td align=left  >".$wpac."</td>";
				  echo "<td align=center><A href='hoja_medicamentos_auditores.php?whis=".$whis."&wing=".$wing."&wpac=".$wpac."&whab=".$whab."&wegr=".$wegr."&wfin=".$wfin."&wcenmez=".$wcenmez."&wemp_pmla=".$wemp_pmla."&wbasedato=".$wbasedato."' target=_blank>Detallar</A></td>";
			      echo "</tr>";
			           
			      $whabant = $whab;
		         }
		     } 
		       
		 echo "</table>";
		 echo "<center><table>";
		 echo "<tr class=seccion1>";
		 echo "<td><b>Ingrese la Historia :</b><INPUT TYPE='text' NAME='whis' id='whis' SIZE=10></td>";
		 echo "<td><b>Nro de Ingreso :</b><INPUT TYPE='text' NAME='wing' id='wing' SIZE=10></td>";
		 echo "</tr>";
		 echo "<tr>&nbsp</tr>";
		 echo "<tr>&nbsp</tr>";
		 	
		 echo "<td align=center class=boton1 colspan=2><A HREF=# onclick='ejecutar(\"$wemp_pmla\")' class=tipo3V>Consultar</A></td></center>";
		 echo "</table>";
		 echo "<br/>";
		 echo "<center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></center>";
		 echo "<br/>";
		 
	   }	       
	else{  
	   /******************************** 
	   * TODOS LOS PARAMETROS ESTAN SET*
	   *********************************/
	    if (isset($whis) and isset($wing) and $whis != "" and $wing != "")
		   {
		   // validamos si el usuario tiene permisos para ingresar a la historia médica
			if(validar_usuario($whis, $wing))
              {			
				encabezado("APLICACION Y ADMINISTRACION DE INSUMOS Y MEDICAMENTOS", $wactualiz, 'clinica');
				
				echo "<center><table></center>";
							
				//Esto lo hago para poder hacer la actualizacion del chequeo antes de consultar los articulos de la historia
				//y asi poder mostrar los articulos si estan chequeados o no, sin tener que salirse de la pagina
				//Se hace el llamado de la pagina enviando tambien como parametro o variable oculta 'wnr' y preguntando si esta setiada
				if (isset($wnr))   
					for ($j=1;$j<=$wnr;$j++)
					   {
						if (isset($aprobado[$j]))
						   {
							$q = "   UPDATE ".$wbasedato."_000015 "
								."      SET aplapr = 'on' "
								."    WHERE aplhis = '".$whis."'"
								."      AND apling = '".$wing."'"
								."      AND aplart = '".$warticulo[$j]."'"
								."      AND apldes = '".$wdesc[$j]."'"
								."      AND aplcco = '".$wccog[$j]."'";
								
							$res4 = mysql_query($q,$conex)or die("Id Error 5: ".mysql_errno()." - En el query:".$q."  Error: ".mysql_error());
						   }
					   }         
				//SELECCIONA LA INFORMACION DEL PACIENTE POR HISTORIA E INGRESO ACTIVAS  
				
				// En caso de que utilize la tabla costosyp_000005, debe filtrar también la empresa(ccoemp)
				if ($wtabcco == 'costosyp_000005')
					{ $condivar = " AND ubisac  = ccocod AND ccoemp= '".$wemp_pmla."' "; }
				else
					{ $condivar = " AND ubisac  = ccocod "; }
				 
				$q = " SELECT ubihac, ubihis, ubiing, pacno1,pacno2, pacap1, pacap2, ubifad, ".$wbasedato."_000016.fecha_data, ubisac, cconom "
					."   FROM ".$wbasedato."_000018, root_000037, root_000036, ".$wbasedato."_000016, ".$wtabcco
					."  WHERE ubihis  = '".$whis."'"
					."    AND ubiing  = '".$wing."'"
					."    AND ubihis  = orihis "
					."    AND ubiing  = oriing "
					."    AND ubihis  = Inghis "
					."    AND ubiing  = Inging "
					."    AND oriori  = '".$wemp_pmla."'"
					."    AND oriced  = pacced "
					. $condivar 
					//."    AND ubiald != 'on' "              //Solo los que estan activos
					//."    AND ubisac  = ccocod "
					."  ORDER BY 1, 4, 5 ";

				$res = mysql_query($q,$conex) or die ("Error 6: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$wnr = mysql_num_rows($res);        
					   
				$row = mysql_fetch_row($res);
					  
				$whab = $row[0];
				
				//SI LA HISTORIA E INGRESO NO CORRESPONDEN EN LA CONSULTA ANTERIOR DEJO LOS QUE VIENEN EN EL FORMULARIO
				 if ($wnr == 0)
					{
					$whis = $whis;
					$wing = $wing;	
					
					$q = " SELECT ubihac, ubihis, ubiing, pacno1,pacno2, pacap1, pacap2, ubifad, ".$wbasedato."_000016.fecha_data, ubisac, cconom "
					."   FROM ".$wbasedato."_000018, root_000037, root_000036, ".$wbasedato."_000016, ".$wtabcco
					."  WHERE ubihis  = '".$whis."'"					
					."    AND ubihis  = orihis "					
					."    AND ubihis  = Inghis "					
					."    AND oriori  = '".$wemp_pmla."'"
					."    AND oriced  = pacced "
					//."    AND ubiald != 'on' "              //Solo los que estan activos
					//."    AND ubisac  = ccocod "
					. $condivar 
					."  ORDER BY 1, 4, 5 ";

					$res = mysql_query($q,$conex) or die ("Error 7: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());  
					$row = mysql_fetch_row($res);
					
					}
				// SI EXISTEN TRAE LOS QUE VIENEN DE LA CONSULTA ANTERIOR
				else
					{
					$whis = $row[1];
					$wing = $row[2];
					}
				$wpac = $row[3]." ".$row[4]." ".$row[5]." ".$row[6];
				$wegr = $row[7];      //Fecha de Egreso
				$wfin = $row[8];      //Fecha de ingreso
				$wser = $row[9];      //Servicio o centro de costo
				$wnomser = $row[10];  //Nombre del Servicio o centro de costo

				
				echo "<tr class=seccion1>";  
				echo "<td><b>HISTORIA N° : </b>".$whis." - ".$wing."</td>";
				echo "<td><b>SERVICIO : </b>".$wnomser."</td>";
				echo "<td><b>CAMA : </b>".$whab."</td>"; 
				echo "</tr>";  
				
				echo "<tr class=seccion1>";
				echo "<td><b>PACIENTE : </b>".$wpac."</td>";
				echo "<td><b>FECHA INGRESO: </b>".$wfin."</td>";
				echo "<td><b>FECHA EGRESO: </b>".$wegr."</td>";
				echo "</tr>"; 	
				echo "</table>";
				
				
				echo "<br>";
				echo "<center><table class=seccion1 ><tr><td colspan ='4' align='center'><b>FILTRAR POR :</b></td></tr><tr><td>Tipo de Articulo:</td><td><select id='tipoarticulo' onchange='enter()' style=font-size: 40px; font-family:";
				echo "Verdana,Arial,Helvetica,sans-serif; height: 40px;' size='1'";
				

				
				
						
				echo "name='wfil'><option>Seleccione</option>";
				
				if($wfil == "Dispositivos Medicos")
				  {
				     echo "<option value='Dispositivos Medicos' selected=selected'>Dispositivos Medicos</option>";
				  }else
				      {
					     echo "<option value='Dispositivos Medicos'>Dispositivos Medicos</option>";
				      }
				
				if($wfil == "Medicamentos")
				  {
				     echo "<option value='Medicamentos' selected=selected'>Medicamentos</option>"; 
				  }else
					   {
	                     echo "<option value='Medicamentos'>Medicamentos</option>"; 
					   }
				
				if($wfil  == "Ambos")
				   {	
					 echo "<option value='Ambos' selected=selected'>Ambos</option>"; 
				   }else
				        {
						 echo "<option value='Ambos'>Ambos</option>"; 
						}
				   
				echo "</select></td><td>Uso:</td>";
				echo "<td><select id='extenointerno'>";
					if($externointerno =='Ext')
					{
					  	
						echo"<option value=''>Seleccione...</option><option value='Ext' selected>Externo</option><option value='Int'>Interno</option>";	
					}
					if($externointerno =='Int')
					{
						echo"<option value=''>Seleccione...</option><option value='Ext' >Externo</option><option value='Int' selected>Interno</option>";	
			
					}
					if($externointerno =='')
					{
						
						echo"<option value='' selected>Seleccione...</option><option value='Ext' >Externo</option><option value='Int'>Interno</option>";	
			
					}
				echo "</select></td></tr></table></center>";
				
				echo "<br><center><table class=seccion1></center>";
				
				// preguntamos si la variable $wfil no esta definida para cargar el reporte por primera vez, si no , realizamos el filtro por 	
				//Medicamentos, Dispositivos medicos o ambos		
				if(!isset($wfil) or $wfil != "Dispositivos Medicos" or $wfil != "Medicamentos")
							{
								$q  = "(SELECT UPPER(aplart), artcom as apldes, aplcco, sum(aplcan), aplapr, artcom, artuni, unides " 
									."   FROM ".$wbasedato."_000015, ".$wbasedato."_000026 , ".$wbasedato."_000027 "
									."  WHERE aplhis  = '".$whis."'"									
									."    AND apling  = '".$wing."'"
									."    AND aplest  = 'on' "
									."    AND aplart != '999' "
									."    AND aplart  = artcod "
									."    AND artuni  = unicod "
									."  GROUP BY 1, 2, 3, 5, 6, 7, 8) "
									." UNION "
									." (SELECT UPPER(aplart), artcom as apldes, aplcco, sum(aplcan), aplapr, artcom, artuni, unides "
									."   FROM ".$wbasedato."_000015, ".$wcenmez."_000002 , ".$wbasedato."_000027 "
									."  WHERE aplhis  = '".$whis."'"
									."    AND apling  = '".$wing."'"
									."    AND aplest  = 'on' "
									."    AND aplart != '999' "
									."    AND aplart  = artcod "
									."    AND artuni  = unicod "
									."    AND artcod not in (select artcod from  ".$wbasedato."_000026 A where A.artcod = aplart  )"
									."  GROUP BY 1, 2, 3, 5, 6, 7, 8) ";						  
							}
				if ($wfil == "Dispositivos Medicos")
						   {
							 $q  = "(SELECT UPPER(Aplart), Artcom as apldes, Aplcco, sum(Aplcan), Aplapr, Artcom, Artuni, Unides "	
								  ."   FROM ".$wbasedato."_000015, ".$wbasedato."_000026 , ".$wbasedato."_000027 "
								  ."  WHERE Aplhis  = '".$whis."'"
								  ."    AND (SUBSTRING( Artgru, 1, INSTR( Artgru,'-')- 1)) not in( select Melgru from "
								  ."".	$wbasedato."_000066) " 
								  ."    AND Apling  = '".$wing."'"
								  ."    AND Aplest  = 'on' "
								  ."    AND Aplart != '999' "
								  ."    AND Aplart  = Artcod "
								  ."    AND Artuni  = Unicod "
								  ."  GROUP BY 1, 2, 3, 5, 6, 7, 8) ";
						   }
					if ($wfil == "Medicamentos")
						   {
							$q  = "(SELECT UPPER(aplart), artcom as apldes, aplcco, sum(aplcan), aplapr, artcom, artuni, unides " 
									."   FROM ".$wbasedato."_000015, ".$wbasedato."_000026 , ".$wbasedato."_000027 "
									."  WHERE aplhis  = '".$whis."'"
									."    AND SUBSTRING( Artgru, 1, INSTR( Artgru,'-')- 1) IN ( Select Melgru from ".$wbasedato."_000066) " 
									."    AND apling  = '".$wing."'"
									."    AND aplest  = 'on' "
									."    AND aplart != '999' "
									."    AND aplart  = artcod "
									."    AND artuni  = unicod "
									."  GROUP BY 1, 2, 3, 5, 6, 7, 8) "
									." UNION "
									." (SELECT UPPER(aplart), artcom as apldes, aplcco, sum(aplcan), aplapr, artcom, artuni, unides "
									."   FROM ".$wbasedato."_000015, ".$wcenmez."_000002 , ".$wbasedato."_000027 "
									."  WHERE aplhis  = '".$whis."'"
									."    AND apling  = '".$wing."'"
									."    AND aplest  = 'on' "
									."    AND aplart != '999' "
									."    AND aplart  = artcod "
									."    AND artuni  = unicod "
									."    AND artcod not in (select artcod from  ".$wbasedato."_000026 A where A.artcod = aplart  )"
									."  GROUP BY 1, 2, 3, 5, 6, 7, 8) ";
						   }
				$res1 = mysql_query($q,$conex) or die("Id Error 8: ".mysql_errno()." - En el query:".$q."  Error: ".mysql_error());
				$wnr = mysql_num_rows($res1); 
			
				echo "<br>";
				echo "<tr class=encabezadoTabla>";    
				echo "<th>CODIGO</th>";
				echo "<th>INSUMO</th>";
				echo "<th colspan=2>Unidad</th>";
				echo "<th>SERVICIO O UNIDAD</th>";
				echo "<th>CANTIDAD</th>";
				echo "<th>APROBADO</th>";
				
				$wuni="";
				$wart="";
				$iart=0; //Para controlar la cantidad de servicios por articulo y poder mostrar el total general del articulo
				$cont_serv=0;
				$arts = [];
				
				//Array que guarda el nombre del cco de costos
				$cconombres = [];
				if( $wnr > 0 )
				{
					for( $i=1; $row = mysql_fetch_row($res1); $i++ )
					{
						if ($row[3] != 0)  //Si la cantidad es diferente de cero la imprimo 
						{	
							if( !isset( $cconombres[ $row[2] ] ) )
							{
								// En caso de que utilize la tabla costosyp_000005, debe filtrar también la empresa(ccoemp)
								if ($wtabcco == 'costosyp_000005')
								{
									$condivar = " WHERE ccocod = '".$row[2]."' AND ccoemp= '".$wemp_pmla."' ";
								}
								else
								{
									$condivar = " WHERE ccocod = '".$row[2]."' ";
								}
								
								//Consultando el nombre de centro de costos
								$q =" SELECT cconom "
								   ."   FROM ".$wtabcco
								   . $condivar ;
								
								$res2 	= mysql_query($q,$conex)or die("Id Error 10: ".mysql_errno()." - En el query:".$q."  Error: ".mysql_error());
								$row2 	= mysql_fetch_row($res2);
								$cconombres[ $row[2] ] =  $row2[0];
							}
							
							// $wnomcco= $row2[0];
							$wnomcco= $cconombres[ $row[2] ];
							
							$wart =  $row[0];
							
							if( !isset( $arts[ $row[0] ] ) )
							{
								$arts[ $row[0] ] = [
														'codigo' 			=> $row[0],
														'nombreComercial' 	=> $row[1],
														'unidad' 			=> $row[6],
														'descripcionUnidad' => $row[7],
														'total'				=> 0,
														'ver'				=> "<A style='cursor:pointer' onclick='rep_artaplixpac(\"".$wfil."\" , \"".$whis."\"  , \"".$wemp_pmla."\" , \"".$wing."\" , \"".$basedato."\" , \"".$wart."\" , \"1\" ,\"".$wnomser."\" ,\"".$whab."\" ,\"".$wpac."\" ,\"".$wfin."\" ,\"".$wegr."\" )' >ver</A>",
														'ccos' 	 			=> [],
													];
							}
							
							$arts[ $row[0] ][ 'total' ] += $row[3];
							$arts[ $row[0] ]['ccos'][] = [
															'codigo' 	=> $row[2],
															'nombre' 	=> $wnomcco,
															'cantidad' 	=> round( $row[3], 2 ),
														];
														
							$warticulo[$i] = $row[0];
							$wdesc[$i]     = $row[1];
							$wccog[$i]     = $row[2];
							$wunicod[$i]   = $row[6];
							$wuninom[$i]   = $row[7];
						}
					}
				}
				
				//De aca en adelante envio todas la variables ocultas para poder refrescar la pantalla
				for ($i=1;$i<=$wnr;$i++)
				{
					 echo "<input type='HIDDEN' NAME= 'warticulo[".$i."]' value='".$warticulo[$i]."'>";
					 echo "<input type='HIDDEN' NAME= 'wdesc[".$i."]' value='".$wdesc[$i]."'>";
					 echo "<input type='HIDDEN' NAME= 'wccog[".$i."]' value='".$wccog[$i]."'>";
					 echo "<input type='HIDDEN' NAME= 'wunicod[".$i."]' value='".$wunicod[$i]."'>";
					 echo "<input type='HIDDEN' NAME= 'wuninom[".$i."]' value='".$wuninom[$i]."'>";
				}
				
				echo "<input type='HIDDEN' NAME= 'whis' value='".$whis."'>";
				echo "<input type='HIDDEN' NAME= 'wing' value='".$wing."'>";
				echo "<input type='HIDDEN' NAME= 'whab' value='".$whab."'>";
				echo "<input type='HIDDEN' NAME= 'wegr' value='".$wegr."'>";
				echo "<input type='HIDDEN' NAME= 'wnomser' value='".$wnomser."'>";
				echo "<input type='HIDDEN' NAME= 'wpac' value='".$wpac."'>";
				echo "<input type='HIDDEN' NAME= 'wfin' value='".$wfin."'>";
				echo "<input type='HIDDEN' NAME= 'wnr'  value=".$wnr.">";
				
				$j = 1;
				$ccos = [];
				//pinto el html necesario para mostrar al usuario
				foreach( $arts as $key => $art )
				{
					//El rowspan por defecto en 1
					$wnumreg = 1;
					
					//Este contiene todos los ccos para el artículo
					$ccos = $art['ccos'];
					
					//Si los centros de costos son mayor a 1 se debe imprimir el total adicionalmente
					if( count( $art['ccos'] ) > 1 )
					{
						//Este es el rowspan para la la celdas correspondientes
						$wnumreg = count( $art['ccos'] ) + 1;
						
						//Agrego el total a los centros de costos para que se muestre
						//y solo se hace si hay más de un centro de costos
						$ccos[] = [
									'codigo' 	=> '',
									'nombre' 	=> '<b>TOTAL ARTICULO:</b>',
									'cantidad' 	=> "<b>".round( $art['total'], 2 )."</b>",
								];
					}
					
					//Color de la fila
					if( $j%2 == 0 )
						$wcolor = "C3D9FF";
					else  
						$wcolor = "E8EEF7";  
					
					$i = 0;
					
					foreach( $ccos as $cco )
					{
						echo "<tr>";
						
						if( $i == 0 )
						{
							echo "<td bgcolor=".$wcolor." rowspan=".$wnumreg."><font size=2>".$art['codigo']."</font></td>";  //Articulo
							echo "<td bgcolor=".$wcolor." rowspan=".$wnumreg."><font size=2>".$art['nombreComercial']."</font></td>";  //Insumo
							echo "<td bgcolor=".$wcolor." rowspan=".$wnumreg."><font size=2>".$art['unidad']."</font></td>";  //Unidad
							echo "<td bgcolor=".$wcolor." rowspan=".$wnumreg."><font size=2>".$art['descripcionUnidad']."</font></td>";  //Unidad
							echo "<td bgcolor=".$wcolor."><font size=1>".$cco['nombre']."</font></td>";  //Unidad
							echo "<td bgcolor=".$wcolor." style='text-align:center'><font size=1>".$cco['cantidad']."</font>&nbsp;</td>";  //Unidad
							echo "<td bgcolor=".$wcolor." rowspan=".$wnumreg." style='text-align:center'>".$art['ver']."</td>";  //Unidad
						}
						else
						{
							echo "<td bgcolor=".$wcolor."><font size=1>".$cco['nombre']."</font>&nbsp;</td>";  //Unidad
							echo "<td bgcolor=".$wcolor." style='text-align:center'><font size=1>".$cco['cantidad']."</font>&nbsp;</td>";  //Unidad
						}
						
						echo "</tr>";
						
						$i++;
					}
					$j++;
				}
				
				echo "</table>";
				
				echo "<br>";
				
				echo "<center><input type='button' value=Retornar onclick='cerrarVentana()'></center>";
			
			 }else   //Septiembre 27 de 2011 
                 {
				  ?>	    
				   <script>
				     alert("No tiene PERMISO para acceder a esta Historia");
				   </script>
				  <?php
				  
				  unset($whis);
			      unset($wing);
				  echo "<meta http-equiv='refresh' content='0;url=Hoja_medicamentos_auditores.php?wemp_pmla=".$wemp_pmla."'>";
				 }
				 
	       }    
	   }
	   
	   // echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
	   
	   echo "<br>";
	   //echo "<center><A href='hoja_medicamentos_auditores.php?wemp_pmla=".$wemp_pmla."&wbasedato=".$wbasedato."'> Retornar</A></center>";
	   echo "</form>";
	   
	   echo "<center><table></center>"; 
	   //echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
	   echo "</table>";
}
include_once("free.php");
?>
