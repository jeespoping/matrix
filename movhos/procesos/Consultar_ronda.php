<html>
<head>
<title>Reporte Consulta historia por paciente
</title>
</head>
<script type="text/javascript">
	function cerrarVentana()
	 {
      top.close();		  
     }
	 function validar_campos()							//Se agrega esta funcion con la actualizacion del 26-01-2012.
	 {	
		campo1= document.getElementById("whis").value;
		campo2= document.getElementById("wing").value;
		
		if(campo1=="" || campo2=="")
		   alert("No se encontraron Resuldados")	
		else
			document.consulta.submit();
	}
</script>

<body>
<?php
include_once("conex.php");
/*
// ===========================================================================================================================================
// FECHA ACTUALIZACION :		Enero 18 de 2016 Edwin Molina
								Se tiene en cuenta al momento de anular una aplicación si fue aplicado por Ipods y mueve el saldo
// ===========================================================================================================================================
// FECHA ACTUALIZACION :		Noviembre 12 de 2013 Jonatan Lopez 
								Se filtra la consulta del detalle de articulos aplicados para que los usuarios que tienen permisos de eliminar
							    y que sean del lactario, solo les permita eliminar los articulos de ese centro de costos.
// ===========================================================================================================================================
// FECHA ACTUALIZACION :		Octubre 16 de 2013 Jonatan Lopez 
//								Se valida si el usuario se encuentra en el arreglo de usuario que pueden anular medicamentos, si es asi le  
								muestra los medicamentos que se pueden anular, los usuarios con estos permisos se encuentran en la tabla root_000051, con
								el valor UsuariosAnularMedicamentos.
// ===========================================================================================================================================
// ACTUALIZACION:				Febrero-7-2012 -Luis Zapata-se agregan variables para la consulta de historias inactivas
//============================================================================================================================================

// FECHA ACTUALIZACION :		Enero-26-2012 -Luis Haroldo Zapata Arismendy                                                                                  				 |
// DESCRIPCION:			        Se agregan los campos de historia e ingreso de un paciente en este programa, 
//							    para que pueda mirarse una historia de forma independiente del proceso de Aplicacion de Medicamentos
//							   (Aplicacion_med_y_mat).																	 					|
//                                                                                                                                          |
// TABLAS UTILIZADAS :          Movhos_000004,11,15,18,26,27----root_000036,37---cenmez_000002                                                                                                                    |
// 
// ==========================================================================================================================================
*/
$wactualiz = "Enero 19 de 2016";

//================================================================================





include_once("root/magenta.php");
include_once("root/comun.php");
 global $origen;             //Se declara esta variable para que dependiendo de por donde ingrese el usuario, retorne ya sea a consultar_ronda o a Aplicacion_med_y_mat
 global $wpac;
 global $whab;
 global $wcco;
 
$pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));    //variable que verifica cual es el usuario que ingresa a consultar y que condición cumple


if(!isset($wccoinac))
{
	$wccoinac=$wcco;		 //variable de centro de costos para las historias inactivas
}

if( !isset($wactivo) )
{
	$wactivo='A';
}
 

if(!isset($_SESSION['user']))
	exit("error session no abierta");

if(!isset($wemp_pmla))
{
	terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
}

$institucion = consultarInstitucionPorcodigo($conex, $wemp_pmla);
$wbasedato = $institucion->baseDeDatos;
$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
$wcenmez =   consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenmez" );
if(!isset($wing))
{
	$wing="";					//variables para las historias activas
}

if(!isset($whis))
{
	$whis="";
}
if(!isset($whis1))			//variables para las historias inactivas
{
	$whis1="";
}
if(!isset($wing1))
{
	$wing1="";
}


echo "<form action='Consultar_ronda.php?wemp_pmla=$wemp_pmla' name='consulta' id='consulta' method='post'>";

//Estos son los campos agregados en la actualizacion del 26 de enero

if($whis=="" || $wing=="")			
{
	Encabezado("CONSULTA DE HISTORIA POR PACIENTE", $wactualiz  ,"clinica");
	echo "<table align=center>";
	echo "<tr class=seccion1>";
	echo "<td><b>Historia</b><br><center><INPUT TYPE='text' NAME='whis' id='whis' SIZE=10></td>";
	echo "<td><b>Ingreso</b><br><center><INPUT TYPE='text' NAME='wing' id='wing' SIZE=10></td>";
	echo "</tr>";
	
	echo "</table>";
	echo "<br><br>";
		
	echo "<br><table align='center'>";
	echo  "<tr>";
	echo  "<td align='center' width='150'><INPUT type='button' value='Ver' style='width:100' name='btVer' onclick='validar_campos()'></td>";
	echo  "<td align='center' width='150'><INPUT type='button' value='Cerrar' style='width:100' onClick='javascript:top.close();'></td>";
	echo  "</tr>";
	echo  "</table>";
}
else
{	
	function buscar_cco($wccosto)
    {
	 global $wbasedato;
	 global $conex;
	 
	 //Busco si en el cco ya tiene la aplicacion por IPOD
	 $q = " SELECT ccoipd "
	     ."   FROM ".$wbasedato."_000011 "
	     ."  WHERE ccocod = '".trim($wccosto)."'"
	     ."    AND ccoest = 'on' ";
	 $res = mysql_query($q,$conex);
	 $row = mysql_fetch_row($res);
	 
	 if ($row[0]=="on")
	    return true;
	 else 
	    return false;      
	} 
	
	//Esta funcion verifica si el usuario que esta consultando es del lactario.
	function validar_usuario_lactario($wusuario)
	{
	
	global $conex;
	global $wbasedato;
	
	//Consulto el centro de costos del usuario	
	$q = "SELECT Ccostos "
	  ."    FROM usuarios "
	  ."   WHERE codigo  = '".$wusuario."'";		       
	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row = mysql_fetch_array($res);	
	
	//Segun el centro de costos, extraigo los datos del grupo de articulos y si es del lactario.
	$q_lactario = "SELECT Ccogka, Ccolac "
				 ."  FROM  ".$wbasedato."_000011"
	             ." WHERE Ccocod = '".$row['Ccostos']."'";		       
	$res_lactario = mysql_query($q_lactario,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$row_lac = mysql_fetch_array($res_lactario);
	
	//Ubico los resultados en el arreglo y retorno ese valor
	$info_array = array('ccolac'=>$row_lac['Ccolac'], 'ccogru'=>$row_lac['Ccogka']);
	
	return $info_array;
	
	}
		
	function mostrar_detalle($wccosto)
    {
	 global $wronda;   
	 global $whis;
	 global $wing;
	 global $wfecha;
	 global $conex;
	 global $wpac;
	 global $whab;
	 global $wcco;
	 global $wusu;
	 global $wborrar;
	 global $wbasedato;
	 global $wcenmez;
	 global $wemp_pmla;
	 global $wusuario;
	 	 
	 //Busco si este centro de costo solo puede anular el material medico QX
	 $wsolomaterial=buscar_cco($wccosto);
	 //============================================================================================================
	 //Se agrega esta consulta a la tabla root_000051 para que entregue los usuarios que pueden anular medicamentos 
	 $wusuarios_autorizados = consultarAliasPorAplicacion( $conex, $wemp_pmla, "UsuariosAnularMedicamentos" );
	 $wusuarios_autorizados_array = explode("-",$wusuarios_autorizados);
	 //============================================================================================================
	 
	 $datos_usuario = validar_usuario_lactario($wusuario);
	 
	 $wfiltro_articulos_lactario = array();
	 //Verifico si el usuario pertenece al centro de costos del lactario, si es asi la variable $wfiltro_articulos_lactario 
	 //es declarada con el filtro de los grupos de articulos del lactario.
	 if($datos_usuario['ccolac'] == 'on')
	 {
		//Convierto los grupos separados por comaen en un arreglo.
		 $array_grupos = explode(",", $datos_usuario['ccogru']);
		
		//Construyo el filtro que se aplicara en la consulta, segun la cantidad de grupos de articulos que tenga el arreglo $array_grupos
		 foreach($array_grupos as $key => $value){		
			$wfiltro_articulos_lactario[] = " mid(artgru,1,instr(artgru,'-')-1) = '".$value."'";
		 }		
		 
		 //Agrupo las posiciones resultantes del foreach con un OR
		 $wdato_final_filtro = implode(" OR ", $wfiltro_articulos_lactario);
		 
		 //Lo cierro entre parentesis para que el query lo lea como debe ser.
		 $wdato_final_filtro = "AND ( ".$wdato_final_filtro.")";
	 
	 }
	 else	
		{
		   //Esta consulta se da para los usuarios que no son del lactario, pero que pueden ver todos los articulos.
		   $wconsulta_union .= "  UNION ";
		   $wconsulta_union .=" SELECT ".$wbasedato."_000015."."id, aplcco, aplart, apldes, unides, aplcan, aplron ";
		   $wconsulta_union .="   FROM ".$wbasedato."_000015, ".$wcenmez."_000002, ".$wbasedato."_000027 ";
		   $wconsulta_union .="  WHERE aplron = '".$wronda."'";
		   $wconsulta_union .="    AND aplhis = '".$whis."'";
		   $wconsulta_union .="    AND apling = '".$wing."'";
		   $wconsulta_union .="    AND aplfec = '".$wfecha."'";
		   $wconsulta_union .="    AND aplusu = '".$wusu."'";
		   $wconsulta_union .="    AND aplest = 'on' ";
		   $wconsulta_union .="    AND aplart = artcod ";
		   $wconsulta_union .="    AND artuni = unicod ";
		
		}
		
	  //Se valida si el usuario no se encuentra en el arreglo $wusuarios_autorizados_array (usuarios que pueden anular medicamentos), si no esta ingresa a la 
	  //primera consulta que no muestra el detalle, en caso contrario muestra el detalle.
	  if ($wsolomaterial and (!in_array($wusuario,$wusuarios_autorizados_array )))
	   { 
		 
		  $q = "SELECT ".$wbasedato."_000015."."id, aplcco, aplart, apldes, unides, aplcan, aplron "
		     ."   FROM ".$wbasedato."_000015, ".$wbasedato."_000026, ".$wbasedato."_000027 "
		     ."  WHERE aplron 							  = '".$wronda."'"
		     ."    AND aplhis 							  = '".$whis."'"
		     ."    AND apling 							  = '".$wing."'"
		     ."    AND aplfec 							  = '".$wfecha."'"
		     ."    AND aplusu 							  = '".$wusu."'"
		     ."    AND aplest 							  = 'on' "
		     ."    AND aplart 							  = artcod "
	         ."    AND artuni 							  = unicod "
	         ."    AND mid(artgru,1,instr(artgru,'-')-1)  NOT IN (SELECT melgru FROM ".$wbasedato."_000066 WHERE melest = 'on') "			 
	         ."  UNION "
	         ." SELECT ".$wbasedato."_000015."."id, aplcco, aplart, apldes, unides, aplcan, aplron "
		     ."   FROM ".$wbasedato."_000015, ".$wbasedato."_000026, ".$wbasedato."_000027, ".$wbasedato."_000066 "
		     ."  WHERE aplron 							  = '".$wronda."'"
		     ."    AND aplhis 							  = '".$whis."'"
		     ."    AND apling 							  = '".$wing."'"
		     ."    AND aplfec 							  = '".$wfecha."'"
		     ."    AND aplusu 							  = '".$wusu."'"
		     ."    AND aplest 							  = 'on' "
		     ."    AND aplart 							  = artcod "
	         ."    AND artuni 							  = unicod "
	         ."    AND mid(artgru,1,instr(artgru,'-')-1)  = melgru "
	         ."    AND meltip                             = 'L' "			
	         ."  ORDER BY 2, 4 ";
	    }
       else
       {
	   
		$q = " SELECT ".$wbasedato."_000015."."id, aplcco, aplart, apldes, unides, aplcan, aplron "
		   ."   FROM ".$wbasedato."_000015, ".$wbasedato."_000026, ".$wbasedato."_000027 "
		   ."  WHERE aplron = '".$wronda."'"
		   ."    AND aplhis = '".$whis."'"
		   ."    AND apling = '".$wing."'"
		   ."    AND aplfec = '".$wfecha."'"
		   ."    AND aplusu = '".$wusu."'"
		   ."    AND aplest = 'on' "
		   ."    AND aplart = artcod "
		   ."    AND artuni = unicod "		   		   
		   ."    $wdato_final_filtro" //Esta variable contiene el filtro para los articulos que son del lactario, en caso de que el usuario pertenezca a ese centro de costos.
		   ."	 $wconsulta_union "   //Esta variable contiene la consulta para los usuarios que no son del lactario, pero que pueden ver todos los articulos.	
		   ."  ORDER BY 2, 4 ";
		   
	    } 	
	
		$res = mysql_query($q,$conex);
		$wnr = mysql_num_rows($res); 
		 
		 echo "<tr class=encabezadoTabla>";
		 echo "<th>Id</th>";
		 echo "<th>C.Costo Aplico</th>";
		 echo "<th>Articulo</th>";
		 echo "<th>Descripción</th>";
		 echo "<th>Presentación</th>";
		 echo "<th>Cantidad</th>";
		 echo "<th>Acción</th>";
		 echo "</tr>";
                              
		 $i=1;
		 while ($i <= $wnr)
        {
		   $row = mysql_fetch_row($res);
	  
		   if (is_integer($i/2))
			  $wclass="fila1";
			 else
				$wclass="fila2";
						
	     echo "<tr class=".$wclass.">";
	     echo "<td>".$row[0]."</td>";
	     echo "<td>".$row[1]."</td>";
	     echo "<td>".$row[2]."</td>";
	     echo "<td>".$row[3]."</td>";
	     echo "<td>".$row[4]."</td>";
	     echo "<td>".$row[5]."</td>";
		 
	     echo "<td align=center><font size=3><b><A href='Consultar_ronda.php?whis=".$whis."&wronda=".$row[6]."&wing=".$wing."&wborrar=S"."&wccoapl=".$row[1]."&warticulo=".$row[2]."&wcantidad=".$row[5]."&wpac=".$wpac."&whab=".$whab."&wcco=".$wcco."&wid=".$row[0]."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wcenmez=".$wcenmez."&wfecha=".$wfecha."&wusu=".$wusu."'>Anular</A></b></font></td>";
	     echo "</tr>";
						   
		   $i=$i+1;
        }   
	}    
	
    ///FORM ===================////////====================
		
	echo "<form action='Consultar_ronda.php' method=post>";
  
	echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
	 
	encabezado("APLICACION DE MEDICAMENTOS E INSUMOS A PACIENTES",$wactualiz, "clinica");
	
	echo "<center><table>";
	echo "<tr class=encabezadoTabla>";
	
	if (strpos($user,"-") > 0)
		$wuser = substr($user,(strpos($user,"-")+1),strlen($user)); 
	if(!isset($wborrar))
	{
		
		if($origen=='AM')     //Dependiendo de por donde ingrese retornara al origen de donde fue ingresado (Actualizacion del 26 de Enero)
		{
			if($wactivo=='A')
			{
				echo "<td align=center colspan=7><A href='Aplicacion_med_y_mat_ingreso.php?whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wpac=".$wpac."&whab=".$whab."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wcenmez=".$wcenmez."&origen=".$origen."&wactivo=".$wactivo."'><font size=3 color=ffffff>Retornar</A></font></td>";
			}
			else
			{
				echo "<td align=center colspan=7><A href='Aplicacion_med_y_mat.php?whis1=".$whis."&wing1=".$wing."whis=".$whis1."&wing=".$wing1."&wemp_pmla=".$wemp_pmla."&wcenmez=".$wcenmez."&wcco=".$wccoinac."&wactivo=".$wactivo."'><font size=3 color=ffffff>Retornar</font></A></td>";
				
			}	
		}	
		elseif($origen == '')
		{
			echo "<td align=center colspan=7><A href='Consultar_ronda.php?wemp_pmla=".$wemp_pmla."&wcenmez=".$wcenmez."&wcco=".$wcco."'><font size=3 color=ffffff><b>Retornar</b></font></A></td>";
			
		}  
	
	}
	echo "</tr>";
	echo "</table>";
		
	if (strpos($user,"-") > 0)
		$wuser = substr($user,(strpos($user,"-")+1),strlen($user)); 
			
			
	echo "<center><table>";			//Este es el query agregado para la actualizacion del 26-01-2012
									//A través de el busco la informacion del paciente y su habitacion actual o anterior
				//			0		1		2		3		4		5	  6		7		8					
			$q = "SELECT pacno1, pacno2, pacap1, pacap2, pactid, pacced,ubisac,ubihac,ubihis "
		      ."    FROM ".$wbasedato."_000018 A, root_000036 B, root_000037 C "
		      ."   WHERE A.ubihis  = '".$whis."'"
		      ."     AND A.ubihis  = C.orihis "
		      ."     AND A.ubiing  = '".$wing."' "
		      ."     AND C.oriori  = '".$wemp_pmla."'"  
		      ."     AND C.oriced  = B.pacced "
		      ."     AND B.pactid  = C.oritid "
			  ."	 ORDER BY 8 ";
		       
		  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num = mysql_num_rows($res);  
		                         
		 // $whabant = "";
		  if ($num > 0)
		   {
			  for($i=1;$i<=$num;$i++)
				{
				   $row = mysql_fetch_array($res);  	  
					  
				   if (is_integer($i/2))
	                  $wclass="fila1";
	                 else
	                    $wclass="fila2";
				   
	               $wpac = $row[0]." ".$row[1]." ".$row[2]." ".$row[3];  //Nombre del paciente
				// $wtid = $row[4];                                      //Tipo documento paciente
			    // $wdpa = $row[5];                                      //Documento del paciente
				   $wcco = $row[6];  									 //Centro de costos actual
				   $whab = $row[7];										 //Habitacion actual
				   
		        }	     
			}
			
				echo "<tr class=encabezadoTabla>";
				echo "<td align=center><font size=4 text color=FFFFFF><b>Historia : ".$whis."-".$wing."</b></font></td>";
				echo "<td align=center colspan=2><font size=4 text color=FFFFFF><b>".$wpac."</b></font></td>";
				echo "<td align=center colspan=2><font size=4 text color=FFFFFF><b>Habitación : ".$whab."</b></font></td>";
							
	///////////////////////
	if(!isset($wronda))
    {
		
	 //Coloco el foco en el campo wronda   
     ?>	    
     <script>
     function ira(){document.ingreso.wronda.focus();}
     </script>
     <?php
    
     //Aca muestro todas las rondas del paciente en el dia actual para seleccionar una
     $q = " SELECT Aplfec, Aplusu, Aplron, Aplapl "
	     ."   FROM ".$wbasedato."_000015 "
		 ."  WHERE aplhis     = '".$whis."'"
		 ."    AND apling     = '".$wing."'"
		 ."    AND aplest     = 'on' "
		 ."  GROUP BY 1, 2, 3, 4 "
		 ."  ORDER BY 1 desc, 3 desc ";
	 $res3 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 
	 echo "<tr class=encabezadoTabla>";
	 echo "<th>Fecha de Aplicación</th>";
	 echo "<th>Quien Registró</th>";
	 echo "<th>Quien Aplicó al paciente</th>";
	 echo "<th colspan=2 align=center>Hora Aplicación</th>";
	 echo "</tr>";
	 echo "<br>";	                             
	 $wnr = mysql_num_rows($res3); 
	     
	 $i=1;
	 while ($i <= $wnr)
	   {
	      $row = mysql_fetch_row($res3);
	  
	      if (is_integer($i/2))
             $wclass="fila1";
          else
			 $wclass="fila2";
				                    
	      echo "<tr class=".$wclass.">";
	      echo "<td align=center>".$row[0]."</td>";
	      	      
	      //Aca busco el nombre del usuario que registro
	      $q = " SELECT Descripcion "
	          ."   FROM usuarios "
	          ."  WHERE Codigo = '".$row[1]."'";
	      $resusu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	      $wnrusu = mysql_num_rows($resusu);  
	      
	      if ($wnrusu > 0)
			{
			  $rowusu = mysql_fetch_row($resusu);  
			   echo "<td>".$row[1]."-".$rowusu[0]."</td>";  
			} 
			else
			{
			   echo "<td>".$row[1]."-"."No se registro Usuario</td>";
			} 
	           
	      //Aca busco el nombre del usuario que aplico
	      $q = " SELECT Descripcion "
	          ."   FROM usuarios "
	          ."  WHERE Codigo = '".$row[3]."'";
	      $resusu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	      $wnrusu = mysql_num_rows($resusu);  
	      
	      if ($wnrusu > 0)
	        {
		      $rowusu = mysql_fetch_row($resusu);   
		       echo "<td>".$row[3]."-".$rowusu[0]."</td>"; 
			} 
	         else
	        {
		       echo "<td>".$row[3]."-"."No se registro Usuario</td>";
			} 
	                
			echo "<td align=right>".$row[2]."</td>";
			if($wactivo=='A')
			{
				echo "<td align=center><font size=3><b><A href='Consultar_ronda.php?whis=".$whis."&wing=".$wing."&wcenmez=".$wcenmez."&wusu=".$row[1]."&wborrar=N"."&wronda=".$row[2]."&wfecha=".$row[0]."&wpac=".$wpac."&whab=".$whab."&wcco=".$wcco."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&origen=".$origen."&wactivo=A'>Ver detalle</A></b></font></td>";
				echo "</tr>";
			}
			elseif ($wactivo=='I')
			{
				echo "<td align=center><font size=3><b><A href='Consultar_ronda.php?whis=".$whis."&wing=".$wing."&wccoinac=".$wccoinac."&wcco=".$wcco."&wcenmez=".$wcenmez."&wusu=".$row[1]."&wborrar=N"."&wronda=".$row[2]."&wfecha=".$row[0]."&wpac=".$wpac."&whab=".$whab."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wcenmez=".$wcenmez."&origen=".$origen."&wactivo=I'>Ver detalle</A></b></font></td>";
			}
		 	
			$i=$i+1;
			
	    }
		
		echo "<table align='center'>";
		echo "<br>";
		echo "<tr class=encabezadoTabla>";
			
		if (strpos($user,"-") > 0)
			$wuser = substr($user,(strpos($user,"-")+1),strlen($user)); 
		
		if($origen=='AM')     //Dependiendo de por donde ingrese retornara al origen de donde fue ingresado (Actualizacion del 26 de Enero)
		{
			if($wactivo=='A')
			{
				echo "<td align=center colspan=7><A href='Aplicacion_med_y_mat_ingreso.php?whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wpac=".$wpac."&whab=".$whab."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wcenmez=".$wcenmez."&origen=".$origen."&wactivo=A'><font size=3 color=ffffff>Retornar</A></font></td>";
			}
			else
			{	//echo $wcco;
				echo "<td align=center colspan=7><A href='Aplicacion_med_y_mat.php?whis1=".$whis."&wing1=".$wing."&whis=".$whis1."&wing=".$wing1."&wemp_pmla=".$wemp_pmla."&wcenmez=".$wcenmez."&wcco=".$wccoinac."&wactivo=I'><font size=3 color=ffffff>Retornar</font></A></td>";
			}	
		}	
		elseif($origen == '')
		{
		 echo "<td align=center colspan=7><A href='Consultar_ronda.php?wemp_pmla=".$wemp_pmla."&wcenmez=".$wcenmez."'><font size=3 color=ffffff><b>Retornar</b></font></A></td>";
		 echo "</td>";
		 echo "</tr>";
		 echo "</table>";
		}
	}
    else
    {  
	    echo "<td align=center colspan=2><b>Hora Aplicacion:".$wronda."</b></td>";
	    echo "</tr>";   
	    
	   if ($wborrar == "S")
	    {
		   //Busco si la aplicacion se hizo por aprovechamiento o no   
		   $q = " SELECT aplapv, aplsal, aplnum, apllin, aplnen, aplsal "
			   ."   FROM ".$wbasedato."_000015 "
			   ."  WHERE aplron  = '".$wronda."'"
			   ."    AND aplhis  = '".$whis."'"
			   ."    AND apling  = '".$wing."'"
			   ."    AND aplfec  = '".$wfecha."'"
			   ."    AND aplart  = '".$warticulo."'"
			   ."    AND aplest  = 'on' "
			   ."    AND id      = ".$wid
			   ."    AND aplapr != 'on' ";
		   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());    
		   $row2 = mysql_fetch_array($res);
		   $wapro=$row2[0];      //Indica si el articulo se aplico por aprovechamiento
		   $aplmuevesaldo = true;
		   // Si el articulo no tiene aplnum y apllin significa que fue aplicado por Ipods
		   // Si fue aplicado por Ipods hay que verificar si el articulo mueve el saldo
		   if( empty( $row2[ 'aplnum' ] ) && empty( $row2[ 'apllin' ] ) && $row2[ 'aplnen' ] == 'on' && $row2[ 'aplsal' ] == 'off' ){
				$aplmuevesaldo = false;
		   }
		   
		   if( $aplmuevesaldo ){
			   //Aca traigo el saldo x articulo del centro de costo que mas tenga superior o igual a la cantidad a anular
			   $q = " SELECT spacco, MAX(spausa) "
				   ."   FROM ".$wbasedato."_000004 "
				   ."  WHERE spahis  = '".$whis."'"
				   ."    AND spaing  = '".$wing."'"
				   ."    AND spaart  = '".$warticulo."'"
				   ."    AND spausa >= ".$wcantidad
				   ."    AND spacco  = '".$wccoapl."'"                          //Si tiene saldo del centro de costo en donde anulan
				   ."  GROUP BY 1 ";
			   $res = mysql_query($q,$conex);
			   $wnr = mysql_num_rows($res); 
			   if ($wnr == 0)
				{
				   $q = " SELECT spacco, MAX(spausa) "
					   ."   FROM ".$wbasedato."_000004, ".$wbasedato."_000011 "
					   ."  WHERE spahis  = '".$whis."'"
					   ."    AND spaing  = '".$wing."'"
					   ."    AND spaart  = '".$warticulo."'"
					   ."    AND spausa >= ".$wcantidad
					   ."    AND spacco  = ccocod "
					   ."    AND ccoima  = 'on' "                               //Si existe cantidad de un cc que maneja inventario
					   ."  GROUP BY 1 ";
				   $res = mysql_query($q,$conex);
				   $wnr = mysql_num_rows($res);    
				   
					if ($wnr == 0)
					{
					   $q = " SELECT spacco, MAX(spausa) "
						   ."   FROM ".$wbasedato."_000004 "
						   ."  WHERE spahis  = '".$whis."'"
						   ."    AND spaing  = '".$wing."'"
						   ."    AND spaart  = '".$warticulo."'"
						   ."    AND spausa >= ".$wcantidad                     //Si tiene saldo en un cc en donde hay cantidad para anular
						   ."  GROUP BY 1 ";
					   $res = mysql_query($q,$conex);
					   $wnr = mysql_num_rows($res);
					}
				}
		   }
	       	       
	        if ($wnr > 0 || !$aplmuevesaldo )
	        {
				if( $aplmuevesaldo ){
					   $row = mysql_fetch_row($res);
					   
					   if ($wapro!="on") 
						{              //Si entra aca es porque No fue por aprovechamiento.
						   $q= " UPDATE ".$wbasedato."_000004 "
							  ."    SET spausa = spausa - ".$wcantidad           //Salidas Unix
							  ."  WHERE spahis = '".$whis."'"
							  ."    AND spaing = '".$wing."'"
							  ."    AND spaart = '".$warticulo."'"
							  ."    AND spacco = '".$row[0]."'";
						}
					  else
						{            //Si entra aca es porque Si fue por aprovechamiento.
						 $q= " UPDATE ".$wbasedato."_000004 "
							."    SET spausa = spausa - ".$wcantidad.","    //Salidas Unix
							."        spaasa = spaasa - ".$wcantidad        //Salidas por Aprovechamiento
							."  WHERE spahis = '".$whis."'"
							."    AND spaing = '".$wing."'"
							."    AND spaart = '".$warticulo."'"
							."    AND spacco = '".$row[0]."'";
						} 
					   $resbor = mysql_query($q,$conex);
				}
					  
				   
			   //Borra o anulo la aplicacion del articulo al paciente siempre y cuando no este aprobado (por el auditor) 
			   $q = " UPDATE ".$wbasedato."_000015 "
				   ."    SET aplest  = 'off', "
				   ."        aplusu  = '".$wusuario."'"
				   ."  WHERE aplron  = '".$wronda."'"
				   ."    AND aplhis  = '".$whis."'"
				   ."    AND apling  = '".$wing."'"
				   ."    AND aplfec  = '".$wfecha."'"
				   ."    AND aplart  = '".$warticulo."'"
				   ."    AND aplest  = 'on' "
				   ."    AND id      = ".$wid
				   ."    AND aplapr != 'on' ";
			   $res = mysql_query($q,$conex);
            }
            else
            {
			 ?>	    
			 <script>
			  alert ("NO SE ANULO LA APLICACION PORQUE ESTA NO FUE HECHA EN ESTE SERVICIO");
			 </script>
			 <?php    
			} 
				 
        }   
       mostrar_detalle($wcco);
	   
       echo "<tr class=encabezadoTabla>";
	   
	   if($origen=='AM')		//Dependiendo de por donde ingrese se podrá consultar la hora de aplicacion de medicamentos
	    {
			if($wactivo=='A')
			{
			 echo "<td align=center colspan=7><A href='Consultar_ronda.php?whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wpac=".$wpac."&whab=".$whab."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wcenmez=".$wcenmez."&origen=".$origen."&wactivo=A'><b><font size=3 color=ffffff>Retornar</font></b></A></td>";
			 echo "</tr>";
			 echo "</table><br>";
			 //echo  $whis."  ".$wing." ".$wcco. " " .$wpac. " " .$whab." ".$wcenmez." ".$origen." ".$wactivo;
			}
			else
			{
			 echo "<td align=center colspan=7><A href='Consultar_ronda.php?whis=".$whis."&wing=".$wing."&wcco=".$wccoinac."&wpac=".$wpac."&whab=".$whab."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wcenmez=".$wcenmez."&origen=".$origen."&wactivo=I'><b><font size=3 color=ffffff>Retornar</font></b></A></td>";
			}
		}
		elseif ($origen == '')
		{	
		 echo "<td align=center colspan=7><A href='Consultar_ronda.php?whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wpac=".$wpac."&whab=".$whab."&wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wcenmez=".$wcenmez."&origen=".$origen."'><font size=3 color=ffffff><b>Retornar</b></font></A></td>";
		 echo "</tr>";
		 echo "</table>";
		}	
			
	}  
	
} // if de register



?>
