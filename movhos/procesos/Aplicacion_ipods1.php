<head>
  <title>REGISTRO APLICACION MEDICAMENTOS - IPOD'S</title>
  
  <link type="text/css" href="../../../include/root/ui.core.css" rel="stylesheet"/>
  <link type="text/css" href="../../../include/root/ui.theme.css" rel="stylesheet"/>
  <link type="text/css" href="../../../include/root/ui.tabs.css" rel="stylesheet"/>
  <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
  
  <script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
  <script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
  <script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
  
  <style type="text/css">
    	
    	A	{text-decoration: none;color: #000066;}
    	.tipo3V{color:#000066;background:#dddddd;font-size:40pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3V:hover {color: #000066; background: #999999;}
        .tipo4V{color:#000066;background:#dddddd;font-size:30pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo4V:hover {color: #000066; background: #999999;}
    	
  </style>
  
  
</head>

<script type="text/javascript">

function parpadear() {
  var blink = document.all.tags("BLINK")
  for (var i=0; i < blink.length; i++)
    blink[i].style.visibility = blink[i].style.visibility == "" ? "hidden" : "" 
}

function empezar() {
    setInterval("parpadear()",500)
}
window.onload = empezar;


function Falta_aplicar()
  {
	alert ("EN LAS HABITACIONES QUE SE MUESTRAN, NO SE HA TERMINADO DE APLICAR LA RONDA ANTERIOR");
	//window.close(); 
  } 

function validar_browser()
  {
	var browserName=navigator.appName; 
	var code=navigator.appCodeName;
	var agente=navigator.userAgent;
	
	var pos_brow = agente.indexOf("Safari");
	var pos_movi = agente.indexOf("Mobile");
	
	if (pos_brow <= 0 || pos_movi <= 0)
	   {
	    alert ("ESTE PROGRAMA SOLO PUEDER USUARSE DESDE LOS ** IPOD's **");
	    window.close(); 
       } 
  } 

function enter()
	{
	 document.forms.apl_ipods.submit();
	}
	
function cerrarVentana()
	 {
      window.close()		  
     }
     
</script>

<body>

<?php
include_once("conex.php");
  /*********************************************************
   *               APLICACION DE MEDICAMENTOS              *
   *    EN LA UNIDAD EN DONDE SE ENCUENTRE EL PACIENTE     *
   *     				CONEX, FREE => OK				   *
   *********************************************************/
   
//==================================================================================================================================
//PROGRAMA                   : Aplicacion_ipods1.php
//AUTOR                      : Juan Carlos Hernández M.
//FECHA CREACION             : Agosto 25 de 2010
//FECHA ULTIMA ACTUALIZACION : Ene 25 de 2012 "; 
//DESCRIPCION
//=============================================================================================================================================\\
//=============================================================================================================================================\\
//     Programa para el registro de la aplicación de medicamentos, basando siempre en las frecuencias definidas por la enfremera en el         \\
//     Kardex electronico y posteriormente basado en la Orden Medica electronica hecha por el médico.                                          \\
//     ** Funcionamiento General:                                                                                                              \\
//     Se selecciona el servicio en el que se esta, se despliega la lista de paciente en el servicio y se va ingresando a cada uno de          \\
//     ellos y dando click en el botón APLICAR de cada uno de los medicamentos si se lo aplico.                                                \\
//     Para ANULAR se da click en el botón ANULAR.                                                                                             \\
//                                                                                                                                             \\
//     ** Restricciones:                                                                                                                       \\
//        * El programa solo puede ser y solo se dejará usar, en los IPOD's.                                                                   \\
//        * NO deja aplicar Rondas posteriores a la que este sucediendo en ese momento (esto lo hace automaticamente)       //        * Se puede aplicar o desatrazar máximo la ronda anterior a la que este en curso.                                                     \\
//                                                                                                                                             \\
//     Tabla(s) : movhos_000015 mov_000004, movhos_000053, movhos_000054, movhos_000060                                                        \\
//         * 000015: Se registra o anula la aplicación                                                                                         \\
//         * 000004: Se actualiza el saldo del medicamento para el paciente                                                                    \\        
//         * 000053: Se consulta el kardex del día si existe, si no, se busca el del día anterior                                              \\
//         * 000054: Se consulta el detalle de mediamentos y que la frecuencia coincida con la Ronda                                           \\
//         * 000060: En esta tabla se consulta lo mismo que en la anterior, solo cuando el kardex esta abierto por otro usuario                \\
//=============================================================================================================================================\\
//=============================================================================================================================================\\

//=============================================================================================================================================\\
//M O D I F I C A C I O N E S                                                                                                                  \\
//=============================================================================================================================================\\
//=============================================================================================================================================\\
//Febrero 7 de 2012                                                                                                                             \\
//Se adiciona la restriccion de que si no se ha terminado la ronda anterior no deje aplicar nada de la ronda actual, para esto se creo un campo\\                            \\
//en la tabla de Centros de costo (000011) llamadado "ccores" que cuando esta en 'on' hace la verificacion y cuando este en 'off', tambien se  \\
//creo la funcion estaAplicadoCentodeCostosporRonda() y esta su vez si encuentra un medicamento obligatorio sin aplicar muestra las habitaciones\
//que faltan y devuelve un 'false' que a su vez llama la funcion JavScript 'Falta_Aplicar()' la cual muestra un MSJ y cierra la ventana.       \\
//=============================================================================================================================================\\
//Noviembre 17 de 2011                                                                                                                         \\
//Se adiciona el campo "ccoipo" en la tabla movhos_000011, que indica si este cco aplica los medicamentos con IPODs o no, con esto el programa \\
//valida si este programa se puede utilizar o no desde un computador portatil o de escritorio.                                                 \\
//=============================================================================================================================================\\
//Julio 26 de 2011                                                                                                                             \\
//Se adiciona un campo de justificaion por cada medicamento en el caso de que no se aplique, es decir, poder colocar una justificacion de      \\
//porque no se aplico; para esto se creo la tabla _000113 en la cual se guarda por cada articulo que tenga justificacion un registro, el cual  \\
//esta compuesto por historia, ingreso, articulo, fecha, ronda y justificacion, en el caso que luego de colocar la justificacion se aplique, el\\
//programa automaticamente borra el registro de la tabla _000113, además una vez aplicado el medicamento ya no aparece la opcion de poder      \\
//colocar una justificacion, si se anula la aplicacion vuelve y aparece el campo de justificacion.                                             \\
//=============================================================================================================================================\\
//Julio 21 de 2011                                                                                                                             \\
//Si no se ha generado kardex en el día actual, el programa se basa entonces en el del día anterior, para esto se creo una columna en la lista \\
//de pacientes del IPOD que indica si el kardex esta actualizado 'Hoy' o 'Ayer', esto sirve como ayuda.                                        \\      
//=============================================================================================================================================\\
//Julio 12 de 2011                                                                                                                             \\
//Se modifica para que solo salgan las historias que tengan medicamentos para la RONDA solicitada o que sean A NECESIDAD                       \\      
//=============================================================================================================================================\\
//Mayo 27 de 2011                                                                                                                              \\
//Se creo el campo 'ccozon' en la tabla _000011, en el cual se colocan todas las zonas en que se divide el Cco separadas por coma. También se  \\      
//el campo 'habzon' en la tabla _000020, en el cual se debe colocar la zona del Cco en la que esta la cama, debe ser con el mismo nombre se    \\
//coloco en la 000011. Ej: Norte,Sur,Oriente,A,B, etc. en la '_000011' y 'Norte' en la Habitacion '710' del Cco=1186 en la tabla '_000020'.     \\
//=============================================================================================================================================\\
//=============================================================================================================================================\\
//Mayo 25 de 2011                                                                                                                              \\
//Se crea un campo en la tabla 000059 que indica si se aplica o no con IPOD el articulo, si esta en 'on' indica que no se aplica con el IPOD,  \\      
//aunque este aparezca en el Kardex; esto es porque hay articulos del stock que pueden aparecer en el kardex pero al grabarlos o facturarlos   \\
//por la auxiliar de enfermería quedan de una vez aplicados.                                                                                   \\
//=============================================================================================================================================\\
//=============================================================================================================================================\\
//Abril 18 de 2011                                                                                                                             \\
//Se crean los campos 'aplufr' y 'apldos', en done se graban la unidad de la fraccion y la dosis exacta aplicada en unidades de fraccion       \\      
//=============================================================================================================================================\\
//Abril 12 de 2011                                                                                                                             \\
//Se devuelve la modificacion anterior, pero se corrige en la funcion Hora_par(), que cuando la resta de la hora actual le de cero, coloque    \\      
//hora_par_anterior = 24                                                                                                                       \\
//=============================================================================================================================================\\
//Abril 11 de 2011                                                                                                                             \\
//Se modifica para que la hora de busqueda cuando sean las 24 busque con 00:00 que es con la ronda que queda aplicada, esto evita un doble reg.\\
//============================================================================================================================================ \\
//Marzo 17 de 2011                                                                                                                             \\
//=============================================================================================================================================\\
//Se coloca un blockUI al APLICAR y al ANULAR para que no pueda registrar mas de una vez cuando el sistema este lento.                         \\
//=============================================================================================================================================\\
//Marzo 7 de 2011                                                                                                                              \\
//=============================================================================================================================================\\
//Se modifica para que no deje registras o aplicar cuando se desconecte la session de Matrix, eso al parecer estaba generando descuadres en    \\
//los saldos.                                                                                                                                  \\
//=============================================================================================================================================\\
//Febrero 10 de 2011                                                                                                                           \\
//=============================================================================================================================================\\
//Se mejora el programa para que permita aplicar los medicamentos que tienen 'NO ENVIAR' en el kardex y que tambien deje aplicar aquellos      \\
//medicamentos que son del STOCK, esto implica hacer el registro en la tabla movhos_000015 pero no en la _000004, permitiendo anular tambien.  \\
//La modificación se realizó en muchas partes por eso no se coloca la fecha en la líneas afectadas.                                            \\
//=============================================================================================================================================\\
//Enero 24 de 2011                                                                                                                             \\
//=============================================================================================================================================\\
//Febrero 8 de 2011                                                                                                                            \\
//=============================================================================================================================================\\
//Se modifica para que deje aplicar los medicamentos del STOCK y los que digan NO ENVIAR en el kardex                                          \\
//Enero 24 de 2011                                                                                                                             \\
//=============================================================================================================================================\\
//Se corrige la cantidad de unidades o fracciones a aplicar, basado en la tabla movhos_000008 que también es base para la grabacion de cargos  \\
//=============================================================================================================================================\\
//Octubre 6 de 2010                                                                                                                            \\
//=============================================================================================================================================\\
//Se adiciona el diagnostico, basado en el campo 'kardia' del kardex movhos_000053                                                             \\
//=============================================================================================================================================\\

   
session_start();
if (!isset($user))
	if(!isset($_SESSION['user']))
	  {
	   session_register("user");
	  } 
	
		
if(!isset($_SESSION['user']))
	echo "error";
else
{	            
 	
  

  include_once("root/comun.php");
  include_once("movhos/movhos.inc1.php");
  

  
  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));
  
  $wuser1=explode("-",$user);
  $wusuario=trim($wuser1[1]);
  
     	                                         // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
  $wactualiz="Febrero 21 de 2012";               // Aca se coloca la ultima fecha de actualizacion de este programa //
	                                             // =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*= //
          
  encabezado("APLICACION DE MEDICAMENTOS",$wactualiz, "clinica"); 		  
  //echo "<br>";				
  //echo "<br>";
  
  //*******************************************************************************************************************************************
  //F U N C I O N E S
  //===========================================================================================================================================
  function mostrar_empresa($wemp_pmla)
     {  
	  global $conex;
	  global $wcenmez;
	  global $wafinidad;
	  global $wbasedato;
	  global $wtabcco;
	  global $winstitucion;
	  global $wactualiz;   
	     
	  
	  
      //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos    
	  $q = " SELECT detapl, detval, empdes "
	      ."   FROM root_000050, root_000051 "
	      ."  WHERE empcod = '".$wemp_pmla."'"
	      ."    AND empest = 'on' "
	      ."    AND empcod = detemp "; 
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res); 
	  
	  if ($num > 0 )
	     {
		  for ($i=1;$i<=$num;$i++)
		     {   
		      $row = mysql_fetch_array($res);
		      
		      if ($row[0] == "cenmez")
		         $wcenmez=$row[1];
		         
		      if ($row[0] == "afinidad")
		         $wafinidad=$row[1];
		         
		      if ($row[0] == "movhos")
		         $wbasedato=$row[1];
		         
		      if ($row[0] == "tabcco")
		         $wtabcco=$row[1];
	         }  
	     }
	    else
	       echo "NO EXISTE NINGUNA APLICACION DEFINIDAD PARA ESTA EMPRESA";
	  
	  $winstitucion=$row[2];
	      
	  ///encabezado("Aplicación Medicamentos IPOD´s ",$wactualiz, "clinica");  
     }
     
  function elegir_centro_de_costo()   
     {
	  global $conex;
	  global $wcenmez;
	  global $wafinidad;
	  global $wbasedato;
	  global $wtabcco;
	  global $winstitucion;
	  global $wactualiz; 
	  global $wcco;  
	  
	  
	  global $whora_par_actual;
	  global $whora_par_anterior;
	  
	  global $wfecha_actual;
	  
	  echo "<center><table>";
      echo "<tr class=encabezadoTabla><td align=center><font size=20>APLICACION DE MEDICAMENTOS</font></td></tr>";
	  echo "</table>";
	  
	  echo "<br><br>";
	  
	  //Seleccionar RONDA
	  echo "<center><table>";
      echo "<tr class=fila1><td align=center><font size=20>Seleccione Ronda : </font></td></tr>";
	  echo "</table>";
	  
	  echo "<center><table>";
	  echo "<tr><td align=rigth><select name='whora_par_actual' size='1' style=' font-size:50px; font-family:Verdana, Arial, Helvetica, sans-serif; height:50px'>";
	  echo "<option selected>".$whora_par_actual."</option>";    
	  echo "<option>".$whora_par_anterior."</option>";    
	  echo "</select></td></tr>";
	  echo "</table>"; 
	  
	  echo "<br><br><br>";
	  	  
	  //Seleccionar CENTRO DE COSTOS
	  echo "<center><table>";
      $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
          ."   FROM ".$wtabcco.", ".$wbasedato."_000011"
          ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod "
          ."    AND ".$wbasedato."_000011.ccohos = 'on' ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
	
      
	  echo "<tr class=fila1><td align=center><font size=30>Seleccione la Unidad : </font></td></tr>";
	  echo "</table>";
	  echo "<br><br><br>";
	  echo "<center><table>";
	  echo "<tr><td align=center><select name='wcco' size='1' style=' font-size:40px; font-family:Verdana, Arial, Helvetica, sans-serif; height:40px' onchange='enter()'>";
	  echo "<option>&nbsp</option>";    
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]." - ".$row[1]."</option>";
         }
      echo "</select></td></tr>";
      echo "</table>";
    }
	
  //============================================================================================================================	
  //Mayo 26 de 2011	
  //============================================================================================================================
  function CcoTieneZonas()      
     {
      global $conex;
	  global $wbasedato;
	  global $wcco;
	  
	  $wcco1 = explode("-",$wcco);
	  
	  $q = " SELECT ccozon "
	      ."   FROM ".$wbasedato."_000011 "
		  ."  WHERE ccocod = '".trim($wcco1[0])."'";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $row = mysql_fetch_array($res);
	  $wcan = COUNT(EXPLODE(",",$row[0]));   //Si el explode devuelve algo es porque hay zonas para el Cco
	  
	  if ($wcan > 1)
	     return true;      //Tiene Zonas
		else
          return false;	   //No tiene Zonas
	 }	 

  function SeleccionarZona()    
     {
      global $conex;
	  global $wbasedato;
	  global $wcco;
	  global $wzona;
	  
	  $wcco1 = explode("-",$wcco);
	  
	  /////////////
	  echo "<center><table>";
      $q = " SELECT ccozon "
	      ."   FROM ".$wbasedato."_000011 "
		  ."  WHERE ccocod = '".trim($wcco1[0])."'";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $row = mysql_fetch_array($res);
	
	  $wzon = explode(",",$row[0]);   //Devuelve las zonas
      
	  echo "<tr class=fila1><td align=center><font size=30>Seleccione la Zona : </font></td></tr>";
	  echo "</table>";
	  echo "<br><br><br>";
	  echo "<center><table>";
	  echo "<tr><td align=center><select name='wzona' size='10' style=' font-size:40px; font-family:Verdana, Arial, Helvetica, sans-serif; height:80px' onchange='enter()'>";
	  echo "<option>&nbsp</option>";    
	  for ($i=0;$i<=(count($wzon)-1);$i++)
	     {
	      echo "<option>".$wzon[$i]."</option>";
         }
      echo "</select></td></tr>";
      echo "</table>";
	}	 
  //============================================================================================================================
  //Termina la modificacion del Mayo 26 de 2011		
  //============================================================================================================================
	 
  function elegir_historia($wzona)   
     {
	  global $conex;
	  global $wcenmez;
	  global $wafinidad;
	  global $wbasedato;
	  global $wtabcco;
	  global $winstitucion;
	  global $wactualiz; 
	  global $wemp_pmla;
	  
	  global $wcco; 
	  global $wnomcco;
	  
	  global $whab;
	  global $whis;
	  global $wing;
	  global $wpac;
	  global $wtid;                                      //Tipo documento paciente
	  global $wdpa;
	  global $wfecha;
	  
	  global $whora_par_actual;
	  global $wfecha_actual;
	  
	  $wcco1=explode("-",$wcco);
	  
	  if ($wzona == "")
	     $wzona = "%";
	  
	  //Selecciono todos los pacientes del servicio seleccionado
	  $q = " SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2, ubialp " //, pactid, pacced "
	      ."   FROM ".$wbasedato."_000020, ".$wbasedato."_000018, root_000036, root_000037 "
	      ."  WHERE habcco        = '".$wcco1[0]."'"
	      ."    AND habali       != 'on' "            //Que no este para alistar
	      ."    AND habdis       != 'on' "            //Que no este disponible, osea que este ocupada
	      ."    AND habcod        = ubihac "
	      ."    AND ubihis        = orihis "
	      ."    AND ubiing        = oriing "
	      ."    AND ubiald       != 'on' "
	      ."    AND ubiptr       != 'on' "
	      ."    AND ubisac        = '".$wcco1[0]."'"
	      ."    AND oriori        = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
	      ."    AND oriced        = pacced "
		  ."    AND oritid        = pactid "
	      ."    AND habhis     	  = ubihis "
	      ."    AND habing        = ubiing "
		  ."    AND UPPER(habzon) LIKE '".$wzona."'"
		  //."    AND ubihis        = '264521' "
		  ."  GROUP BY 1,2,3,4,5,6,7 "
	      ."  ORDER BY 1 "; 
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);  
	  
	  echo "<center><table>";
	  
	  echo "<tr class=titulo>";
	  echo "<td colspan=5 align=center><font size=6><b>Servicio o Unidad: ".$wcco."</b></font></td>";
	  echo "</tr>";
	  
	  echo "<tr class=encabezadoTabla>";
      echo "<th colspan=5><font size=6>Fecha Actual: </font><font size=6 color='00FF00'>".$wfecha_actual."&nbsp;&nbsp;&nbsp;&nbsp;</font><font size=6>Ronda a Aplicar: </font><font size=6 color='00FF00'>".$whora_par_actual.":00</font></th>";
      echo "</tr>";
	  
	  echo "<tr class=encabezadoTabla>";
	  echo "<th><font size=6>Habitacion</font></th>";
	  echo "<th><font size=6>Historia</font></th>";
	  echo "<th><font size=6>Paciente</font></th>";
	  echo "<th><font size=6>Acción</font></th>";
	  echo "<th><font size=6>Kardex<br>Actualizado</font></th>";    //Julio 21 de 2011
	  echo "</tr>";
		                       
	  $whabant = "";
	  if ($num > 0)
	     {
		  $wclass="fila2";
		  for($i=1;$i<=$num;$i++)
			 {
			   $row = mysql_fetch_array($res);  	  
				  
			   if ($wclass=="fila1")
			      $wclass="fila2"; 
                 else
                    $wclass="fila1";
			   
               $whab = $row[0];
			   $whis = $row[1];
			   $wing = $row[2];
			   $wpac = $row[3]." ".$row[4]." ".$row[5]." ".$row[6];
			   //$wtid = $row[7];                                      //Tipo documento paciente
			   //$wdpa = $row[8];                                      //Documento del paciente
			   $walp = $row[7];                                        //Indicador de Alta en Proceso
			   
			   
			   //Valido si tiene medicamentos para la RONDA o A NECESIDAD
			   query_articulos($whis, $wing, $wfecha_actual, &$res1);
			   $num1 = mysql_num_rows($res1);
			   $wreg[0]=0;
			   $wkardex_Actualizado="Hoy";                             //Julio 21 de 2011
			   
			   if ($num1==0)   //Si entra aca es porque NO hay articulos en fecha_actual para la RONDA indicada (hora_par_actual) o no hay Kardex generado
                  {
				   //Verifico si NO hay Kardex en esta fecha
                   $q = " SELECT COUNT(*) "
				       ."   FROM ".$wbasedato."_000053 "
					   ."  WHERE karhis = '".$whis."'"
					   ."    AND karing = '".$wing."'"
					   ."    AND fecha_data = '".$wfecha_actual."'"
					   ."    AND karcon = 'on' "
					   ."    AND karcco = '*' ";
				   $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	               $wreg = mysql_fetch_array($res1);
                  }	

			   //Si no hay articulos para esa RONDA y NO HAY Kardex generado, traigo kardex del dia anterior, porque puede ser que
			   //en el Kardex del dia de hoy no hallan articulos para la RONDA actual, pero en el kardex del dia anterior si habia.
			   if ($num1 == 0 and $wreg[0]==0)     //wreg[0]==0: Indica que NO hay kardex de la fecha_actual
			      {
				   $dia = time()-(1*24*60*60);   //Te resta un dia. (2*24*60*60) te resta dos y //asi...
				   $wayer = date('Y-m-d', $dia); //Formatea dia 
					
				   query_articulos($whis, $wing, $wayer, &$res1);
				   $num1 = mysql_num_rows($res1);
				   
				   $wkardex_Actualizado="Ayer";                       //Julio 21 de 2011
				  }
			   
			   if ($num1 > 0)
                  {			   
				   echo "<tr class=".$wclass.">";
				   echo "<td align=center><font size=6><b>".$whab."</b></font></td>";
				   echo "<td align=center><font size=6><b>".$whis."</b></font></td>";
				   echo "<td align=left  ><font size=6><b>".$wpac."</b></font></td>";
				   
				   echo "<td align=center><A HREF='Aplicacion_ipods1.php?wemp_pmla=".$wemp_pmla."&wfecha_actual=".$wfecha_actual."&whora_par_actual=".$whora_par_actual."&whis=".$whis."&wing=".$wing."&wcco=".$wcco."&whab=".$whab."&wpac=".$wpac."&walp=".$walp."&wzona=".$wzona."' class=tipo3V>Ver</A></td>";
				   
				   //Julio 21 de 2011 - Se queitan las palabras 'Hoy' o 'Ayer'
				   //Asi estaba antes:  echo "<td align=center bgcolor=FFFF99><font size=6><b>".$wkardex_Actualizado."</b></font></td>";
				   if ($wkardex_Actualizado=="Ayer")
				      echo "<td align=center bgcolor=FFFF99><font size=6><b> </b></font></td>";
					  else
					     echo "<td align=center><font size=6><b> </b></font></td>";
				   //======================================================================================================
				   //En este procedimiento pregunto si el paciente es cliente AFIN o no, y de que tipo
				   /* $wafin=clienteMagenta($wdpa,$wtid,&$wtpa,&$wcolorpac);
				   if ($wafin)
					 {
					  echo "<td align=center><font color=".$wcolorpac."><b>".$wtpa."<b></font></td>";
					 }
					else
					   echo "<td>&nbsp</td>";
						*/
				   //======================================================================================================
				   
				   echo "</tr>";
				   echo "<tr><td colspan=4>&nbsp;</td></tr>";
				  }
			 }	     
		  }
		 else
		    echo "NO HAY HABITACIONES OCUPADAS";  
	  echo "</table>"; 
	 }    	 
	 
	
  //La funcion comentada es para mostrar las habitaciones como TIPO BOTON, la anterior muestra como tipo lista y con los datos de los pacientes	 
	 
  /*
  function elegir_historia()   
     {
	  global $user;
	  global $conex;
	  global $wcenmez;
	  global $wafinidad;
	  global $wbasedato;
	  global $wtabcco;
	  global $winstitucion;
	  global $wactualiz; 
	  global $wemp_pmla;
	  
	  global $wcco; 
	  global $wnomcco;
	  
	  global $whab;
	  global $whis;
	  global $wing;
	  global $wpac;
	  global $wtid;                                      //Tipo documento paciente
	  global $wdpa;
	  
	  global $whora_par_actual;
	  
	  $wcco1=explode("-",$wcco);
	  
	  //Selecciono todos los pacientes del servicio seleccionado
	  $q = " SELECT habcod, habhis, habing, pacno1, pacno2, pacap1, pacap2 " //, pactid, pacced "
	      ."   FROM ".$wbasedato."_000020, ".$wbasedato."_000018, root_000036, root_000037 "
	      ."  WHERE habcco  = '".$wcco1[0]."'"
	      ."    AND habali != 'on' "            //Que no este para alistar
	      ."    AND habdis != 'on' "            //Que no este disponible, osea que este ocupada
	      ."    AND habcod  = ubihac "
	      ."    AND ubihis  = orihis "
	      ."    AND ubiing  = oriing "
	      ."    AND ubiald != 'on' "
	      ."    AND ubiptr != 'on' "
	      ."    AND ubisac  = '".$wcco1[0]."'"
	      ."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia, 
	      ."    AND oriced  = pacced "
		  ."    AND oritid  = pactid "
	      ."    AND habhis  = ubihis "
	      ."    AND habing  = ubiing "
	      ."  GROUP BY 1,2,3,4,5,6,7 "
	      ."  ORDER BY 1 "; 
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);  
	  	     
	  echo "<center><table>";
	  
	  echo "<tr class=fila1>";
	  echo "<td colspan=12 align=center><font size=6><b>Servicio o Unidad: ".$wcco."</b></font></td>";
	  echo "</tr>";
	  
	  echo "<tr class=encabezadoTabla>";
      echo "<th colspan=12 align=center><font size=6>Hora Aplicación: </font><font size=6 color='00FF00'>".$whora_par_actual.":00</font></th>";
      echo "</tr>";
	  
	  $whabant = "";
	  if ($num > 0)
	     {
		  $row = mysql_fetch_array($res);    
		  for($i=1;$i<=$num;$i++)
			 {
			   if (is_integer($i/2))
                  $wclass="fila1";
                 else
                    $wclass="fila2";
			   
               echo "<tr><td colspan=12>&nbsp;</td></tr>";
               echo "<tr class=".$wclass.">";
			   
			   $j=1;
			   while ($j <= 6 and $i <= $num)
			      {
				   //echo "<td align=center><font size=8><b>".$whab."</b></font></td>";
			       //echo "<td align=center><font size=6><b>".$whis."</b></font></td>";
			       //echo "<td align=left  ><font size=6><b>".$wpac."</b></font></td>";
			       
			       $whab = $row[0];
			       $whis = $row[1];
			       $wing = $row[2];
			       $wpac = $row[3]." ".$row[4]." ".$row[5]." ".$row[6];
			       
			       echo "<td align=center><A HREF='Aplicacion_ipods1.php?wemp_pmla=".$wemp_pmla."&user=".$user."&whis=".$whis."&wing=".$wing."&wcco=".$wcco."&whora_par_actual=".$whora_par_actual."&whab=".$whab."&wpac=".$wpac."' class=tipo3V>".$whab."</A></td>";
			       $row = mysql_fetch_array($res); 
			       $j++;
			       $i++;
	              }
		       echo "</tr>";
		       echo "<tr><td colspan=7>&nbsp;</td></tr>";
			 }	     
		  }
		 else
		    echo "NO HAY HABITACIONES OCUPADAS";  
	  echo "</table>"; 
	 }	
  */	
	 
function obtenerVectorAplicacionMedicamentos($fechaActual, $fechaInicioSuministro, $horaInicioSuministro, $horasPeriodicidad)
   {
	$arrAplicacion = array();
	
	$horaPivote = 1;

	$caracterMarca = "*";
	
	$vecHoraInicioSuministro   = explode(":",$horaInicioSuministro);
	$vecFechaInicioSuministro  = explode("-",$fechaInicioSuministro);
	
	$vecFechaActual			   = explode("-",$fechaActual);

	$fechaActualGrafica 	= mktime($horaPivote, 0, 0, date($vecFechaActual[1]), date($vecFechaActual[2]), date($vecFechaActual[0]));
	$fechaSuministroGrafica = mktime(intval($vecHoraInicioSuministro[0]), 0, 0, date($vecFechaInicioSuministro[1]), date($vecFechaInicioSuministro[2]), date($vecFechaInicioSuministro[0]));

	$horasDiferenciaHoyFechaSuministro = ROUND(($fechaActualGrafica - $fechaSuministroGrafica)/(60*60));
	
	if($horasDiferenciaHoyFechaSuministro <= 0 && abs($horasDiferenciaHoyFechaSuministro) >= 24)
	  {
	   $caracterMarca = "";	
	  }
	
	/************************************************************************************************************************************************
	 * Febrero 22 de 2011
	 ************************************************************************************************************************************************/
	if( date( "Y-m-d", $fechaActualGrafica+(24*3600) ) == date( "Y-m-d", $fechaSuministroGrafica ) && $vecHoraInicioSuministro[0] == "00" ){
		$caracterMarca = "";
	}
	/************************************************************************************************************************************************/
	
	if ($horasPeriodicidad <= 0)
	  {
	   $horasPeriodicidad = 1;
	  }			
	
	$horaUltimaAplicacion = abs($horasDiferenciaHoyFechaSuministro) % $horasPeriodicidad;
	
	$cont1 = 1;   //Desplazamiento de 24 horas
	$cont2 = 0;   //Desplazamiento desde la hora inicial

	$inicio = false;	//Guia de marca de hora inicial
	
	if ($fechaActual == $fechaInicioSuministro)
	   {
		$cont1 = intval($vecHoraInicioSuministro[0]);
		$arrAplicacion[$cont1] = $caracterMarca;
			
		while($cont1 <= 24)
		  {
			$out = "-";
			if ($cont2 % $horasPeriodicidad == 0)
			   {
			    $out = $caracterMarca;
			   }
			$cont2++;

			$arrAplicacion[$cont1] = $out;
			$cont1++;
		  }
	   }
	  else 
	    {
		 while ($cont1 <= 24)
		   {
		    $out = "-";
			//Hasta llegar a la aplicacion
			if ($cont1 == abs($horaPivote+$horasPeriodicidad-$horaUltimaAplicacion) || ($cont1==1 && $horaUltimaAplicacion == 0)) 
			  {
			   $out = $caracterMarca;
			   $inicio = true;
			  }

			if ($inicio)
			  {
			   if($cont2 % $horasPeriodicidad == 0)
			     {
				  $out = $caracterMarca;
				 }
			   $cont2++;
		      }
			$arrAplicacion[$cont1] = $out;
			$cont1++;
		   }
	    }
	return $arrAplicacion;
   }	 
   
  //Se pregunta por cada medicamento si ya fue presionado el boton de APLICAR, para llevarlo a una variable unica de Aplicados y poderla enviar en el HREF 
  function aplicados($num)
    {
	 global $waplicados;
	 global $wapl;
	 
	 for ($i=1;$i<=$num;$i++)
	   {
		if (isset($wapl[$i]))
		   $waplicados=$waplicados."&wapl[".$i."]=".$wapl[$i];
	   }	          
	}
         
  //Se consigue la hora PAR anterior a la hora actual(si es hora impar) si no se deja la hora PAR actual
  function hora_par()
    {
	 global $whora_par_actual;
	 global $whora_par_anterior;
	 global $wfecha;
	 global $wfecha_actual;
	    
	 
	 $whora_Actual=date("H");
	 $whora_Act=($whora_Actual/2);
	 
	 $wfecha_actual=date("Y-m-d");
	 
	 if (!is_int($whora_Act))   //Si no es par le resto una hora
	    {
		 $whora_par_actual=$whora_Actual-1;
	     if ($whora_par_actual=="00" or $whora_par_actual=="0")    //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	         $whora_par_actual="24";
	    } 
	   else
	     {
		  if ($whora_Actual=="00" or $whora_Actual=="0")    //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	         $whora_par_actual="24";
			else
		       $whora_par_actual=$whora_Actual; 
	     }
	     
	  if ($whora_Actual=="02" or $whora_Actual=="2")        //Esto se hace porque el Kardex o el metodo que calcula las horas de aplicacion reconoce es las 24 horas y no las 00
	     $whora_par_anterior="24";
	    else
	      {
		   if (($whora_par_actual-2) == "00")               //Abril 12 de 2011
		      $whora_par_anterior="24";
		     else
	            $whora_par_anterior = $whora_par_actual-2;
		  }
	      
	  if (strlen($whora_par_anterior) == 1)
	     $whora_par_anterior="0".$whora_par_anterior;
	     
	  if (strlen($whora_par_actual) == 1)
	     $whora_par_actual="0".$whora_par_actual;  
	}
    
    
  function buscoSiYaFueAplicado($whis, $wing, $wart, $wcco, $wdosis, $whora_par_actual, $wfecha_actual, &$wjustificacion, $wido)
    {
	 global $user;
	 global $conex;
	 global $wcenmez;
	 global $wafinidad;
	 global $wbasedato;
	 global $wtabcco;
	 global $winstitucion;
	 global $wactualiz; 
	 global $wemp_pmla;
	 global $wfecha;
	 
	 //===============================================================
	 //Paso la hora a formato de 12 horas
	 //===============================================================
	 if ($whora_par_actual < 12)
	    $whora_a_buscar=trim($whora_par_actual).":00 - AM";
	   else
	     if ($whora_par_actual == 12)
	        $whora_a_buscar=trim($whora_par_actual).":00 - PM";
	       else 
	          if ($whora_par_actual == "24")                           //Abril 11 de 2011
	             $whora_a_buscar=($whora_par_actual-12).":00 - AM";    //El registro se hace con 00:00 y no con 12:00 AM por eso resto 24, antes restaba 12
	            else 
	               $whora_a_buscar=($whora_par_actual-12).":00 - PM";
	 //===============================================================
	 
	 
	 //===============================================================
	 $q = " SELECT COUNT(*) "
		     ."   FROM ".$wbasedato."_000015 "
		     ."  WHERE aplhis = '".$whis."'"
		     ."    AND apling = '".$wing."'"
		     ."    AND aplfec = '".$wfecha_actual."'"
	         ."    AND aplron like '".trim($whora_a_buscar)."'"
		     ."    AND aplart = '".$wart."'"
		     ."    AND aplest = 'on' "
			 ."    AND aplido = ".$wido;
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $row = mysql_fetch_array($res);     
	    
	 if ($row[0] > 0)
	    return true;
	   else   //Si NO tiene aplicacion busco si tiene Justificacion de porque NO se aplico
	      {
		   //===============================================================
		   //Busco si tiene Justificacion
		   //===============================================================
		   $q = " SELECT jusjus "
			   ."   FROM ".$wbasedato."_000113 "
			   ."  WHERE jushis = '".$whis."'"
			   ."    AND jusing = '".$wing."'"
			   ."    AND jusfec = '".$wfecha_actual."'"
			   ."    AND jusron LIKE '".trim($whora_a_buscar)."'"
			   ."    AND jusart = '".$wart."'"
			   ."    AND jusido = ".$wido;
		   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		   $num = mysql_num_rows($res);
		 
		   if ($num > 0)
			  {
			   $row = mysql_fetch_array($res);
			   $wjustificacion = $row[0];
			  } 
		     else
			    $wjustificacion="";
		  
		   return false;     //Indica que no esta aplicado
		  } 
    }    
    
  function esANecesidad($wcond)
    {
	 global $conex;
	 global $wbasedato;
	 global $wfecha;
	 
	 $q = " SELECT contip "
	     ."   FROM ".$wbasedato."_000042 "      //Tabla condiciones de administracion
	     ."  WHERE concod = '".$wcond."'";
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $row = mysql_fetch_array($res);
	 
	 if ($row[0] == "AN")      //'AN' : significa que es A NECESIDAD y es un tipo
	    return true;
       else
	      return false; 
    }      
    
  function buscarSiEstaSuspendido($whis, $wing, $wart, $whora, $wfecha_actual, $wido)
    {
	 global $conex;
	 global $wbasedato;
	    
	    
	 $q = " SELECT COUNT(*)  "
	     ."   FROM ".$wbasedato."_000055 A "
	     ."  WHERE kauhis  = '".$whis."'"
	     ."    AND kauing  = '".$wing."'"
	     ."    AND kaufec  = '".$wfecha_actual."'"
	     ."    AND kaudes  = '".$wart."'"
	     ."    AND kaumen  = 'Articulo suspendido' "
	     ."    AND hora_data BETWEEN '".($whora-2).":00:00' AND '".($whora+2).":00:00'"   //Si la hora de suspensión esta entre la RONDA anterior y la actual se puede aplicar (No se toma como suspendido)
		 ."    AND kauido  = ".$wido;
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $row = mysql_fetch_array($res); 
	 
	 if ($row[0] > 0)  
	    return "off";  //Indica que el articulo fue suspendido hace menos de dos horas, es decir que se puede aplicar, asi este suspendido
	   else
	      return "on"; //Indica que fue Suspendido hace mas de dos horas
	}     
    
  function esdelStock($wart, $wcco)
    {
	 global $conex;
	 global $wbasedato;  
	    
	 //=======================================================================================================
	 //Busco si el articulo hace parte del stock     Febrero 8 de 2011
	 //=======================================================================================================
	 $q = " SELECT COUNT(*) "
	     ."   FROM ".$wbasedato."_000091 "
	     ."  WHERE Arscco = '".trim($wcco)."' "
	     ."    AND Arscod = '".$wart."'"
	     ."    AND Arsest = 'on' ";
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $row = mysql_fetch_array($res);
	 //=======================================================================================================   
	  
	 if ($row[0] == 0)
	    return "off";
	   else
	      return "on"; 
	}    	
	
  function noAplicaConIPOD($wart, $wcco)             // Mayo 25 de 2011
    {
	 global $conex;
	 global $wbasedato;  
	
	 //=======================================================================================================
	 //Busco si el articulo hace parte del stock     Febrero 8 de 2011
	 //=======================================================================================================
	 $q = " SELECT COUNT(*) "
	     ."   FROM ".$wbasedato."_000059 "
	     ."  WHERE (defcco = '".trim($wcco)."' "
		 ."     OR  defcco = '1050') "
	     ."    AND  defart = '".$wart."'"
	     ."    AND  defest = 'on' "
		 ."    AND  defipo = 'on' ";
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $row = mysql_fetch_array($res);
	 //=======================================================================================================   
	  
	 if ($row[0] == 0)
	    return "off";     //Indica que se aplica con el IPOD
	   else
	      return "on";    //Indica que NO se aplica con el IPOD
	
	}
	
	   
  function validar_aplicacion($whis, $wing, $wart, $wcco, $wdosis, &$wcant_aplicar, $wuniman, $warticulodelStock, $noenviar)
    {
	 global $wusuario;
	 global $conex;
	 global $wcenmez;
	 global $wafinidad;
	 global $wbasedato;
	 global $wtabcco;
	 global $winstitucion;
	 global $wactualiz; 
	 global $wemp_pmla;  
	 
	 global $wmensaje;  
	 
	 //Si se perdio la session NO DEJO GRABAR
	 if (isset($wusuario) and trim($wusuario)!="")
	    {
		 $wsalart = 0;
		 $wsalsre = 0;
		 
		 //=======================================================================================================
		 //Traigo el saldo del articulo y el Cco
		 //=======================================================================================================
		 $q = " SELECT SUM(spauen-spausa), spacco "
			 ."   FROM ".$wbasedato."_000004, ".$wbasedato."_000011 "
			 ."  WHERE spahis  = '".$whis."'"
			 ."    AND spaing  = '".$wing."'"
			 ."    AND spaart  = '".$wart."'"
			 ."    AND ((spacco = '".trim($wcco)."' "
			 ."    AND  spacco = ccocod ) "
			 ."     OR (spacco  = ccocod "
			 ."    AND  ccotra  = 'on' "             //Permite hacer traslados
			 ."    AND  ccofac  = 'on')) "            //Puede facturar (cargos)
			 ."  GROUP BY 2 "
			 ."  ORDER BY 1 DESC ";
		 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		 $row = mysql_fetch_array($res);
		 
		 if ($row[0] > 0)  
			{
			 $wsalart=$row[0];   //Saldo del articulo 
			 $wccoapl=$row[1];   //C. Costo que grabo  
			}
		 //=======================================================================================================
		 
		 
		 //=======================================================================================================
		 //Busco si el articulo tiene equivalencias o fracciones en la tabla 000008
		 //=======================================================================================================
		 $q = " SELECT arecde "
			 ."   FROM ".$wbasedato."_000008, ".$wbasedato."_000011 "                   // Enero 24 de 2011 
			 ."  WHERE  arecod  = '".$wart."'"
			 ."    AND ((arecco  = '".trim($wcco)."'"
			 ."    AND   arecco  = ccocod ) "
			 ."     OR  (arecco  = ccocod "
			 ."    AND   ccotra  = 'on' "             //Permite hacer traslados
			 ."    AND   ccofac  = 'on')) "           //Puede facturar (cargos);
			 ."    AND   arecco  = ccocod "
			 ."    AND   arecod  = areces ";
		 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		 $num = mysql_num_rows($res);
		 
		 if ($num > 0)
			{
			 $row = mysql_fetch_array($res);
			 $wartfra=$row[0];   //Fracción del articulo
			}
		   else
			 $wartfra=1;  
		 
		 $wcant_aplicar = ($wdosis/$wuniman)*$wartfra;
		 
		 if ($warticulodelStock=="on")
			{
			 $wcant_aplicar=$wdosis;        //Si es del stock o sea que no se factura por las PDA's entonces aplico la cantidad que dice en el Kardex
			} 
		 //=======================================================================================================
	 
		 if ($warticulodelStock=="off")   //Si entra es porque el articulo NO es del STOCK  ojo
			{
			 //Traigo la cantidad SIN RECIBIR de este articulo 
			 $q = " SELECT COUNT(*) "
				 ."   FROM ".$wbasedato."_000002, ".$wbasedato."_000003, ".$wbasedato."_000011 "
				 ."  WHERE fenhis = '".$whis."'"
				 ."    AND fening = '".$wing."'"
				 ."    AND ((fencco = '".trim($wcco)."'"
				 ."    AND   fencco = ccocod ) "
				 ."     OR  (fencco = ccocod "
				 ."    AND   ccotra = 'on' "
				 ."    AND   ccofac = 'on')) "
				 ."    AND   fennum = fdenum "
				 ."    AND   fdeart = '".$wart."'"
				 ."    AND   fdedis = 'on' "
				 ."    AND   fdeest = 'on' ";
			 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			 $row = mysql_fetch_array($res);
			 
			 $wsalsre = $row[0];  //Cantidad SIN RECIBIR		
				
			 if ($wsalart >= $wcant_aplicar and $wsalart > 0)
				{
				 if ($wsalart > $wsalsre)                   //Saldo del articulo es mayor a la cantidad que falta por recibir PUEDE APLICARSE maximo la diferencia
					 {
					  if (($wsalart-$wsalsre) >= $wcant_aplicar)   //Si la diferencia es mayor o igual a la dosis PUEDE APLICARSE
						 {
						  return true;
						 } 
						else
						   {
							if ($wsalsre > 0) 
							   $wmensaje = "No hay SALDO suficiente para aplicar. El saldo es: <b>".number_format($wsalart,2,'.',',')."</b> y pendiente por recibir: <b>".number_format($wsalsre,2,'.',',')."</b>";   
							  else
								 $wmensaje = "No hay SALDO suficiente para aplicar";
								 
							return false;
						   } 
					 }
					else
					   {
						if ($wsalsre > 0) 
						   $wmensaje = "No hay SALDO suficiente para aplicar. El saldo es: <b>".number_format($wsalart,2,'.',',')."</b> y pendiente por recibir: <b>".number_format($wsalsre,2,'.',',')."</b>";   
						  else
							 {
							  if ($wsalart > 0)   
								 $wmensaje = "No hay DOSIS pendientes de aplicar<br>o es del Stock";
							 }
						return false;
					   } 
				}
			   else  
				 {
				  //Si no tiene saldo suficiente, pero dice NO ENVIAR dejo de todas maneras registrar la aplicación.
				  if ($noenviar=="on")      //Febrero 8 de 2011
					 {
					  return true;
					 } 
					else 
					  {
					   $wmensaje = "No hay SALDO suficiente para aplicar. El saldo es: <b>".number_format($wsalart,2,'.',',')."</b> y pendiente por recibir: <b>".number_format($wsalsre,2,'.',',')."</b>";   
					   return false;
					  }
				 }
			}
		   else     //Si entra es porque ES del STOCK
			  {
			   // ==============================================================================
			   // Mayo 25 de 2011
			   // ==============================================================================
			   $wNoIpod = noAplicaConIPOD($wart, trim($wcco));    ///// No Aplica con Ipod /////   
			   
			   if ($wNoIpod == "off")
				  return true;                                    //Antes del Mayo 25 de 2011 solo estaba esta linea en el else
				 else
					{
					 $wmensaje = "Del Stock, al Facturarlo queda aplicado";
					 return false;				 
					}
			   // ==============================================================================
			  }
		}
	  else
	     {
		  ?>	    
		    <script>
			  alert("1. Debe cerrar esta ventana, Actualizar la pantalla (simbolo de flecha circular)  y volver a ingresar a la opción ** Aplicacion Ipods **");
		    </script>
		  <?php  
		 }
	}      
    
    
  //En esta funcion se busca si la el articulo ya ha sido aplicado en la ronda, para que no queden duplicados
  //porque la tabla 000015 no tiene indice unico y ya es muy dificil crearselo
  function buscarsiExiste($wfecha, $whora_a_grabar, $whis, $wing, $wart, $wido)  
    {
	 global $wbasedato;
	 global $conex;      
	 
	 $q = " SELECT count(*) "
	     ."   FROM ".$wbasedato."_000015 "
	     ."  WHERE aplhis = '".$whis."'"
	     ."    AND apling = '".$wing."'"
	     ."    AND aplfec = '".$wfecha."'"
	     ."    AND aplron = '".$whora_a_grabar."'"
	     ."    AND aplart = '".$wart."'"
	     ."    AND aplest = 'on' "
		 ."    AND aplido = ".$wido;
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	 $row = mysql_fetch_array($res);
	 
	 if ($row[0] > 0)
	    return true;       //Si devuelve este valor es porque si existe 
	   else
	      return false;    //No existe, entonces si se puede grabar  
	}    
  	
	
  function anular($whis, $wing, $wwart, $wcco, $wwdosis, $wwcanfra, $whora_par_actual, $wfecha_actual, $wwnoenviar, $wwarticulodelStock, $wido)
   {
	global $wbasedato;
	global $conex;
	global $wusuario;
	   
	if (isset($wusuario) and trim($wusuario)!="")
	   {   
	    //Marzo 17 de 2011 
		echo "<script language='Javascript'> $.blockUI({ menssage: 'Un momento por Favor'}) </script>";
	   
	    $wcco1=explode("-",$wcco);   
		   
		if ($whora_par_actual < 12)
			$whora_buscar=trim($whora_par_actual).":00 - AM";
		   else
			 if ($whora_par_actual == 12)
				$whora_buscar=trim($whora_par_actual).":00 - PM";
			   else 
				  if ($whora_par_actual == "24")
					 $whora_buscar=($whora_par_actual-12).":00 - AM";
					else 
					   $whora_buscar=($whora_par_actual-12).":00 - PM";
		
		//Traigo la cantidad aplicada, porque puede ser que desde el momento de la aplicación hasta la anulación hallan cambiado las fracciones
		$q= " SELECT aplcan, aplnen "
		   ."   FROM ".$wbasedato."_000015 "
		   ."  WHERE aplhis = '".$whis."'"
		   ."    AND apling = '".$wing."'"
		   ."    AND aplart = '".$wwart."'"
		   ."    AND aplcco = '".trim($wcco1[0])."'"
		   ."    AND aplron = '".$whora_buscar."'"
		   ."    AND aplest = 'on' "
		   ."    AND aplfec = '".$wfecha_actual."'"
		   ."    AND aplido = ".$wido;
		$resanu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($resanu);
		if ($num > 0)
		   {
			$rowcan = mysql_fetch_array($resanu);     //Este es la cantidad aplicada en esa ronda y es la cantidad que se debe restar en la salida de la tabla 000004              
			$wcanapl=$rowcan[0];
			$wnoenviado=$rowcan[1];
		   }
		  else
		     {
			  $wcanapl=0; 
			  $wnoenviado="off";
			 }
			 
		if ($wwnoenviar == "off" and $wwarticulodelStock=="off")
		   {     
			//Aca traigo el saldo del articulo para el paciente, de acuerdo con la prioridad del centro de costo
			$q = " SELECT min(ccopap), spacco "  //Tomo la prioridad MAXIMA del centro costo que tenga saldo del articulo
				."   FROM ".$wbasedato."_000004, ".$wbasedato."_000011 "
				."  WHERE spahis = ".$whis
				."    AND spaing = ".$wing
				."    AND spaart = '".strtoupper($wwart)."'"
				."    AND spacco = ccocod "
				."    AND spausa > 0 "					  //Solo los que tengan el articulo aplicado
				."  GROUP BY 2 ";
			$resanu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());    
			$rowanu = mysql_fetch_array($resanu);
			$wccoapl=$rowanu[1];     
				 
			//Traigo la cantidad de salidas, porque si la aplicacción a anular es mayor a lo que hay en salidas no se deja hacer la anulación
			$q= " SELECT spausa "
			   ."   FROM ".$wbasedato."_000004 "                                     //Salidas Unix 
			   ."  WHERE spahis = '".$whis."'"
			   ."    AND spaing = '".$wing."'"
			   ."    AND spaart = '".$wwart."'"
			   ."    AND spacco = '".trim($wccoapl)."'"; 
			$resusa = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error()); 
			$num = mysql_num_rows($resusa);
			
			if ($num > 0)
			   {
				$rowusa = mysql_fetch_array($resusa);     //Este es la cantidad aplicada en esa ronda y es la cantidad que se debe restar en la salida de la tabla 000004              
				$wcanusa=$rowusa[0];
			   }
			  else
				 $wcanusa=0;
				 
		   }
		  else
			{
			 $wcanusa=0;
			 $wccoapl=$wcco1[0];
			}
		
		if (($wcanapl > 0 and $wcanusa >= $wcanapl) or $wwnoenviar == "on")
		   {
		    //Anulo la aplicación
			$q= " UPDATE ".$wbasedato."_000015 "
			   ."    SET aplest = 'off' "
			   ."  WHERE aplhis = '".$whis."'"
			   ."    AND apling = '".$wing."'"
			   ."    AND aplart = '".$wwart."'"
			   ."    AND aplcco = '".trim($wcco1[0])."'"
			   ."    AND aplron = '".$whora_buscar."'"
			   ."    AND aplest = 'on' "
			   ."    AND aplfec = '".$wfecha_actual."'"
			   ."    AND aplido = ".$wido;
			$resanu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			
			if ($wnoenviado!="on")   //Solo si el articulo realmente no fue enviado
			   {
			    //Esto se hace porque puede que el articulo diga no enviar en el Kardex, pero antes si se habia enviado, es decir,
                //esto sirve para controlar que solo se afecte el saldo de las aplicaciones enviadas
				$q= " UPDATE ".$wbasedato."_000004 "
				   ."    SET spausa = spausa - ".round($wcanapl,3)                       //Salidas Unix 
				   ."  WHERE spahis = '".$whis."'"
				   ."    AND spaing = '".$wing."'"
				   ."    AND spaart = '".$wwart."'"
				   ."    AND spacco = '".trim($wccoapl)."'";
				$resanu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			   }
		   }
		  else
			 {
			  if ($wwarticulodelStock=="off")
				 {
				  ?>	    
				   <script>
					  alert("No se puede ANULAR porque la cantidad aplicada es Mayor al Saldo en salidas");
				   </script>
				  <?php 
				 }
				else
				  {
				   $q= " UPDATE ".$wbasedato."_000015 "
					  ."    SET aplest = 'off' "
					  ."  WHERE aplhis = '".$whis."'"
					  ."    AND apling = '".$wing."'"
					  ."    AND aplart = '".$wwart."'"
					  ."    AND aplcco = '".trim($wcco1[0])."'"
					  ."    AND aplron = '".$whora_buscar."'"
					  ."    AND aplest = 'on' "
					  ."    AND aplfec = '".$wfecha_actual."'"
					  ."    AND aplido = ".$wido;
				   $resanu = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error()); 
				  }  
			 }
		//Marzo 17 de 2011 
		echo "<script>$.unblockUI()</script>"; 
	   }
	  else
		 {
		  ?>	    
			<script>
			  alert("2. Debe cerrar esta ventana, Actualizar la pantalla (simbolo de flecha circular)  y volver a ingresar a la opción ** Aplicacion Ipods **");
			</script>
		  <?php  
		 }		 
   }     
    
	
  function justificacionNoAplicacion($i, $wjustificacion, $waplicado, $whis, $wing, $wfecron, $wronda, $wart, $wido)
   {
    global $conex;
	global $wbasedato;
	
	global $wjus;

	if ($waplicado != "on")
	   {
		//Seleccionar JUSTIFICACIONES
		$q = " SELECT juscod, jusdes "
			."   FROM ".$wbasedato."_000023"
			."  WHERE justip = 'A' "       //A: Aplicacion en Ipod
			."    AND jusest = 'on' ";
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);

		echo "<tr>";
		echo "<td colspan=3 bgcolor=FFFF99><font size=5><b>Seleccione la Justificacion de NO Aplicar: </b></font></td>";
		echo "<td align=center colspan=3 bgcolor=FFFF99><select name='wjus[".$i."]' size='1' style=' font-size:20px; font-family:Verdana, Arial, Helvetica, sans-serif; height:25px' onchange='enter()'>";
		  
		echo "<option selected>".$wjustificacion."</option>";	
		for ($j=1;$j<=$num;$j++)
		   {
			$row = mysql_fetch_array($res); 
			echo "<option>".$row[0]." - ".$row[1]."</option>";
		   }
		echo "<option> </option>";   
		echo "</select></td></tr>";
	   }
	  else
         {
		  //Si ya se habia colocado una justificacion, pero luego se aplico el medicamento
		  //entonces elimino la justificaion que tenia, siempre lo hago por descarte, asi no 
		  //se hubiese colocado justificacion
          $q = " DELETE FROM ".$wbasedato."_000113 "
		      ."  WHERE jushis = '".$whis."'"
			  ."    AND jusing = '".$wing."'"
			  ."    AND jusart = '".$wart."'"
			  ."    AND jusfec = '".$wfecron."'"
			  ."    AND jusron = '".$wronda."'"
			  ."    AND jusido = ".$wido;
		  $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         }		 
   }   
   
   
function grabar_justificacion($wart, $wjust, $whis, $wing, $wfecron, $wronda, $wido)
   {
    global $wusuario;
    global $wbasedato;
	global $conex;
	global $wfecha;
	global $whora;
	
	$wjust=trim($wjust);
	
	if (!empty($wjust))   //Si hay justificacion
	   {
	    $q = " DELETE FROM ".$wbasedato."_000113 "
			."  WHERE jushis = '".$whis."'"
			."    AND jusing = '".$wing."'"
			."    AND jusart = '".$wart."'"
			."    AND jusfec = '".$wfecron."'"
			."    AND jusron = '".$wronda."'"
			."    AND jusido = ".$wido;
	     $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	   
	    $q = " INSERT INTO ".$wbasedato."_000113(   Medico       ,   Fecha_data ,   Hora_data ,   jushis  ,   jusing  , jusart    ,   jusfec     ,    jusron    ,   jusjus   ,  jusido , Seguridad        ) "
		    ."                            VALUES('".$wbasedato."','".$wfecha."' ,'".$whora."' ,'".$whis."','".$wing."','".$wart."','".$wfecron."', '".$wronda."','".$wjust."',".$wido.", 'C-".$wusuario."') ";
			
		$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());	
	   }
	  else
	    {
         $q = " DELETE FROM ".$wbasedato."_000113 "
			."  WHERE jushis = '".$whis."'"
			."    AND jusing = '".$wing."'"
			."    AND jusart = '".$wart."'"
			."    AND jusfec = '".$wfecron."'"
			."    AND jusron = '".$wronda."'"
			."    AND jusido = ".$wido;
	     $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		}
   }
   
   
//=========================================================================================================
// Noviembre 17 de 2011
//=========================================================================================================
function validar_ipods($wcco)
  {
   global $conex;
   global $wbasedato;
   global $wemp_pmla;
   
   global $whora_par_actual;
   global $wfecha_actual;
   
   $wcc = explode("-",$wcco);
   
   $q = " SELECT ccoipo, ccores "
       ."   FROM ".$wbasedato."_000011 "
	   ."  WHERE ccocod = '".trim($wcc[0])."'";
   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());   
 
   $row = mysql_fetch_array($res);
   
   if ($row[0] == "on")
      {
	   //On
	   ?>	    
	   <script>
		// validar_browser();
	   </script>  
	   <?php 
	  }
	  
	$whora_aux=$whora_par_actual;  
	$wrondaAnterior=($whora_par_actual-2);
	
	if ($whora_par_actual>="14" and $whora_par_actual<="22" and (string)date("H") < "12")
	 {
	  $dia = time()-(1*24*60*60); //Te resta un dia
	  $wayer = date('Y-m-d', $dia); //Formatea dia 
	  $wfecha_actual=$wayer;   
	 }
	  
	//En el sgte if se evalua que NO haya quedado sin aplicar algún medicamento en la ronda anterior
	//si no es asi, entonces se va por el else y muestra de q habitaciones quedo faltando y hasta
	//q esto no sea solucionado no deje aplicar nada de la ronda actual
	//Tambien se evalua que el Centro de Costos tenga predido el indicador de Aplicacion con Restriccion (ccores)
	if ($whora_par_actual == "24")
	   {
	    $dia = time()-(1*24*60*60); //Te resta un dia
	    $wayer = date('Y-m-d', $dia); //Formatea dia 
	    $wfec_act=$wayer;
	   }
       else
          $wfec_act=$wfecha_actual;	   
	
	$wok=estaAplicadoCcoPorRonda( trim($wcc[0]), $wfec_act, $wrondaAnterior, &$habitacionesFaltantes );
	
	if ($row[1] == "on" and !$wok)
	   {
	    $wtodas_hab="";
	    //Muestro las habitaciones
		for ($i=0; $i < count($habitacionesFaltantes); $i++)
		   {
		    $wtodas_hab=$wtodas_hab.$habitacionesFaltantes[$i].", ";
		   }
		
		$wmsj = "*** A T E N C I O N ***  EN LAS HABITACIONES: ( ".$wtodas_hab." ) NO SE HA TERMINADO DE APLICAR LA RONDA DE LAS: ( ".$wrondaAnterior." ), DEBE APLICAR O JUSTIFICAR SU NO APLICACION";
		
		echo " <script>
               alert ('$wmsj');
	           
			   window.location.href='Aplicacion_ipods1.php?wemp_pmla=".$wemp_pmla."'
	           </script> ";
		
	   }
	$whora_par_actual=$whora_aux; 
  }  
//=========================================================================================================

//===========================================================================================================================================  
//*******************************************************************************************************************************************
  
  
  
  //===========================================================================================================================================
  //===========================================================================================================================================
  // P R I N C I P A L 
  //===========================================================================================================================================
  //===========================================================================================================================================
  echo "<form name='apl_ipods' action='Aplicacion_ipods1.php' method=post>";
  
  if (!isset($wfecha)) $wfecha = date("Y-m-d");
  $whora  = (string)date("H:i:s");
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  
  mostrar_empresa($wemp_pmla);
  
  
  if (!isset($wcco))
     {
      hora_par();
	  
	  elegir_centro_de_costo();
            
      echo "<input type='HIDDEN' name='wfecha_actual' value='".$wfecha_actual."'>";   
      
      echo "<br><br><br>";
  	  echo "<table>";   
  	  echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()' class=tipo4V></td></tr>";
  	  echo "</table>";
     } 
	else
       {
	    //=====================
	    // Noviembre 17 de 2011
		//=====================
	    validar_ipods($wcco);
		//=====================
		
	    if ($whora_par_actual=="22" and (string)date("H") < "12")
	     {
		  $dia = time()-(1*24*60*60); //Te resta un dia
          $wayer = date('Y-m-d', $dia); //Formatea dia 
		  $wfecha_actual=$wayer;   
		 }
	    else
	       $wfecha = date("Y-m-d");   
	       
	    echo "<input type='HIDDEN' name='wcco' value='".$wcco."'>";
		if (isset($wzona)) 
		   echo "<input type='HIDDEN' name='wzona' value='".$wzona."'>";
		  else
             $wzona="";
			 
		echo "<input type='HIDDEN' name='wfecha' value='".$wfecha."'>";
	    echo "<input type='HIDDEN' name='whora_par_actual' value='".$whora_par_actual."'>";
	    echo "<input type='HIDDEN' name='wfecha_actual' value='".$wfecha_actual."'>";   
	    
		if (isset($wusuario) and TRIM($wusuario))
		   {
			if (isset($whis))
			   {
				echo "<input type='HIDDEN' name='whis' value='".$whis."'>";
				echo "<input type='HIDDEN' name='wing' value='".$wing."'>";
				echo "<input type='HIDDEN' name='whab' value='".$whab."'>";
				echo "<input type='HIDDEN' name='wpac' value='".$wpac."'>";
				
				query_articulos($whis, $wing, $wfecha_actual, &$res);
				$num = mysql_num_rows($res);
				
				if ($num == 0)  //Si no se encuentra Kardex Confirmado en la fecha actual, traigo kardex del dia anterior
				   {
					$dia = time()-(1*24*60*60);   //Te resta un dia. (2*24*60*60) te resta dos y //asi...
					$wayer = date('Y-m-d', $dia); //Formatea dia 
					
					query_articulos($whis, $wing, $wayer, &$res);
					$num = mysql_num_rows($res);
				   }
				   
				echo "<center><table>"; 
				echo "<tr class=encabezadoTabla>";
				echo "<th><font size=6>Habitación: "."</font><font size=9 color='00FF00'>".$whab."&nbsp;&nbsp;&nbsp;&nbsp;</font><font size=6>Historía: ".$whis."</font></th>";
				echo "</tr>";
				echo "<tr class=encabezadoTabla>";
				echo "<th><font size=6>Paciente: </font><font size=6 color='00FF00'>".$wpac."</font></th>";
				echo "</tr>";
				echo "<tr class=encabezadoTabla>";
				echo "<th><font size=6>Fecha Actual: </font><font size=6 color='00FF00'>".$wfecha_actual."&nbsp;&nbsp;&nbsp;&nbsp;</font><font size=6>Ronda a Aplicar: </font><font size=6 color='00FF00'>".$whora_par_actual.":00</font></th>";
				echo "</tr>";
				
				if (isset($walp) and $walp == "on")
				   {
					echo "<tr>";
					echo "<th bgcolor='FFCC66'><font size=6><b><blink id=blink>EN PROCESO DE ALTA</blink></b></font></th>";  
					echo "</tr>";   
				   }    
				echo "</table>";
				
				echo "<br>";
				
				//Octubre 6 de 2010 
				//=================================================================================================================
				//Traigo el Diagnostico   
				$q = " SELECT kardia, fecha_data "
					."   FROM ".$wbasedato."_000053 "
					."  WHERE karhis  = '".$whis."'"
					."    AND karing  = '".$wing."'"
					."    AND karcco  = '*' "
					."    AND karcon  = 'on' "
					."  ORDER BY 2 desc ";
				$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$row1 = mysql_fetch_array($res1);
					
				echo "<center><table>";
				echo "<tr><td bgcolor='FFFF99'><font size=6><b>Diagnostico  : </b>".$row1[0]."</font></td></tr>";        //Diagnostico
				echo "</table>";
				//=================================================================================================================
				
				echo "<br>";	
				
				echo "<center><table>"; 
				echo "<tr class=encabezadoTabla>";
				echo "<th colspan=2><font size=5>Medicamento</font></th>";
				echo "<th><font size=5>Dosis</font></th>";
				echo "<th><font size=5>Vía</font></th>";
				echo "<th><font size=5>Condición</font></th>";
				echo "<th><font size=5>Frecuencia</font></th>";
				echo "<th colspan=2><font size=5>Acción</font></th>";
				
				$waplicados="";
				aplicados($num);   //Por cada submit hago esto, es decir, llevo a una variable todos los medicamentos aplicados para cuando regrese el submit
				
				$j=1;
				for ($i=1;$i<=$num;$i++)   //Recorre cada uno de los medicamentos
					{
					$row = mysql_fetch_array($res);
					 
					if (isset($wanular[$i]) and $wanular[$i]=="on" and $row[1]==$wart[$i])
					   {
						anular($whis, $wing, $wart[$i], $wcco, $wdosis[$i], $wcanfra[$i], $whora_par_actual, $wfecha_actual, $wnoenviar[$i], $wStock[$i], $wido[$i]);
					   } 
						
					//                                                 Fecha Actual Articulo, fecha Inicio aplicacion, hora inicio aplicacion, frecuencia
					$arrAplicacion = obtenerVectorAplicacionMedicamentos($wfecha_actual        , $row[4]                ,  $row[5]              , $row[6]);
					$horaArranque = 0;
					
					$caracterMarca = "*";
					 
					//======================================================================   
					if ($row[16]=="on")
					   $wnoenviar[$i]="on";
					  else
					  $wnoenviar[$i]="off";  //Indica si el medicamento se Envia o No 
					//======================================================================      
					
					$wufr  =$row[8];         //Cantidad de fracciones que tiene la presentacion del medicamento
					$wdosis=$row[7];         //Dosis a plicar, viene del Kardex u Ordenes
					
					$wok=false;              //Indicador para verificar si el articulo se puede aplicar o no, según la validaciones de la función 'validar_aplicacion()'
					$waplicado="off";
							  
					//========================================================================================================================================================================  
					$wanecesidad=false;
					$wcond="";
					$wfrec="";              //Frecuencia o periodicidad
					
					//Pregunto si es una condicion a NECESIDAD
					if ($row[10]!="")       //Indica que puede ser un medicamento a Necesidad
					   {
						//Traigo la descripcion de la CONDICION   
						$q = " SELECT condes "
							."   FROM ".$wbasedato."_000042 "
							."  WHERE concod = '".$row[10]."'";
						$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						$row1 = mysql_fetch_array($res1);
						
						$wcond=$row1[0];   //Descripcion de la condicion
		 
						//           Condición   
						$wanecesidad=esANecesidad($row[10]);
						$wok=true;   //Indica que si esta validada la aplicación, osea que se puede aplicar
					   } 
					//======================================================================================================================================================================= 
					
					//Traigo la descripcion de la FRECUENCIA
					$q = " SELECT percan, peruni "
						."   FROM ".$wbasedato."_000043 "
						."  WHERE percod = '".$row[15]."'";
					$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row1 = mysql_fetch_array($res1);
					
					if ($row1[0] > 1)
					   $wfrec="Cada ".$row1[0]."&nbsp;".$row1[1]."S";   //Descripcion de la FRECUENCIA
					  else
						 $wfrec="Cada ".$row1[0]."&nbsp;".$row1[1];     //Descripcion de la FRECUENCIA 
										   
					$wcco1=explode("-",$wcco);
					
					//===============================================================
					//Paso la hora a formato de 12 horas
					//===============================================================
					if ($whora_par_actual < 12)
						$whora_a_grabar=trim($whora_par_actual).":00 - AM";
					   else
						 if ($whora_par_actual == 12)
							$whora_a_grabar=trim($whora_par_actual).":00 - PM";
						   else 
							  if ($whora_par_actual == "24")
								 $whora_a_grabar=($whora_par_actual-12).":00 - AM";
								else 
								   $whora_a_grabar=($whora_par_actual-12).":00 - PM";
					//=============================================================== 
							
					//Traigo la descripcion de la VIA   
					$q = " SELECT viades "
						."   FROM ".$wbasedato."_000040 "
						."  WHERE viacod = '".$row[11]."'";
					$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row1 = mysql_fetch_array($res1);
								
					$wvia=$row1[0];   //Descripcion de la condicion
							
					//Grabo la justificacion siempre, por cada articulo
					if (isset($wjus[$i]))    //Articulo          Justific   His    Ing    Fecha Actual    Hora ronda       Kadido (id original en kardex)
					   grabar_justificacion(strtoupper($row[1]), $wjus[$i], $whis, $wing, $wfecha_actual, $whora_a_grabar, $row[22]);
					
					//Explicación del IF ****************************************************************************************************************************************************
					//Entra a este if cuando : Cuando el ('arrAplicacion[$cont1]' esta setiado desde la funcion 'obtenerVectorAplicacionMedicamentos' y esa posición del arreglo
					//tiene el caracter '*' y cont1 corresponda con la hora de aplicación actual) O (cuando el articulo sea 'a necesidad' y el contador este en la hora de aplicación actual)
					if ((isset($arrAplicacion[(int)$whora_par_actual]) && $arrAplicacion[(int)$whora_par_actual] == $caracterMarca) or $wanecesidad )
					   {
					    if (is_integer($j/2))
						   $wclass="fila1";
						  else
							 $wclass="fila2";   
						$j++;     
						                       // Articulo      Cco
						$wStock[$i]  = esdelStock($row[1], trim($wcco1[0]));         ///// STOCK /////
						
						$wsuspendido="off";
						if ($row[9]=="on")   //Si esta suspendido verifico que no halla sido dentro la ronda actual
						   { 
															 // Hist   Ing    Articulo, hora              , Fecha actual  , kadido (id original del kardex)
							$wsuspendido=buscarSiEstaSuspendido($whis, $wing, $row[1] ,  $whora_par_actual, $wfecha_actual, $row[22]);
						   } 
						 
						if ($wsuspendido=="off")   //No esta suspendido
						   {
						    echo "<tr class=".$wclass.">";   
							echo "<td><font size=5>".$row[1]."</font></td>";                           //Codigo Medicamento
							echo "<td><font size=6>".$row[2]."</font></td>";                           //Nombre Medicamento
							echo "<td align=right><font size=6>".$row[7]."  ".$row[8]."</font></td>";  //Dosis
							
							                     //  his    ing    articulo      cco         dosis    ronda              fecha           justificacion     kadido (id original kardex)
							if (buscoSiYaFueAplicado($whis, $wing, $row[1], trim($wcco1[0]), $row[7], $whora_par_actual, $wfecha_actual, &$wjustificacion, $row[22]))
							   {
							    $waplicado="on"; 
							   }
							  else
								{
								 if (!isset($wgrabar) or $wgrabar != "on")   
									{
									 //                                      Articulo Cco              Dosis                     Unidad de Manejo, si es del stock, noenviar   
									 $wok = validar_aplicacion($whis , $wing, $row[1], trim($wcco1[0]), $row[7], &$wcant_aplicar, $row[12]        , $wStock[$i]    , $row[16]);	   
									} 
								}
							
							echo "<td align=center><font size=5>".$wvia."</font></td>";    
							echo "<td align=center><font size=5>".$wcond."</font></td>";
							echo "<td align=center><font size=5>".$wfrec."</font></td>";
							   
							if (isset($wapl[$i]) and $wapl[$i]=="on" and $wok==true and $waplicado=="off")
							   {
								if (!isset($wanular) or $wanular != "on")       //Grabar o Aplicar
								   {
								    //Marzo 17 de 2011 
								    echo "<script language='Javascript'> $.blockUI({ menssage: 'Un momento por Favor'}) </script>";
																								  //Cod.Articulo kadido
									$wexiste=buscarsiExiste($wfecha, $whora_a_grabar, $whis, $wing, $row[1],     $row[22]);   
									   
									if ($wexiste == false)   //Esto se hace para controlar la duplicidad de registros
									   {
									    //Verifico que no este desconectada la SESSION
										if (isset($wusuario) and trim($wusuario)!="")
										   {
										    $q= " INSERT INTO ".$wbasedato."_000015 (   Medico       ,   Fecha_data ,   Hora_data ,   aplron            ,   aplhis  ,   apling  ,   aplcco      ,   aplart                ,    apldes    ,  aplcan                     ,   aplusu      , aplapr , aplest , aplapv,   aplfec           ,   aplapl      ,   aplnen           ,    aplufr  ,    apldos    ,   aplido    , Seguridad        ) "
											  ."                             VALUES ('".$wbasedato."','".$wfecha."' ,'".$whora."' ,'".$whora_a_grabar."','".$whis."','".$wing."','".$wcco1[0]."','".strtoupper($row[1])."', '".$row[2]."',".round(($wcant_aplicar),3).",'".$wusuario."', 'off'  , 'on'   , 'off' ,'".$wfecha_actual."','".$wusuario."','".$wnoenviar[$i]."', '".$wufr."', '".$wdosis."', ".$row[22].", 'C-".$wusuario."') ";
											$resgra = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
											
											//if ($wnoenviar[$i]=="off" and $wStock[$i]=="off")     //Si entra aca es porque si se ENVIA el medicamento y NO es del STOCK
											if ($wStock[$i]=="off")     //Si entra aca es porque NO es del STOCK, puede que se envie o no pero si tiene saldo lo descuenta
											    {
											    //Aca traigo el centro de costo que tenga saldo y cuya prioridad sea mayor
												$q = " SELECT spacco "            //Tomo la prioridad MAXIMA del centro costo que tenga saldo del articulo
													."   FROM ".$wbasedato."_000004, ".$wbasedato."_000011 "
													."  WHERE spahis                   = '".$whis."'"
													."    AND spaing                   = '".$wing."'"
													."    AND spaart                   = '".strtoupper($row[1])."'"
													."    AND spacco                   = ccocod "
													."    AND ccotra                   = 'on' "
													."    AND ROUND((spauen-spausa),3) > 0 ";                      //Con esto traigo solo los registros que tengan saldo, se coloca la palabra round para que haga redondeo del saldo en forma exacta
												$res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());    
												$num1 = mysql_num_rows($res1);  
												if ($num1 > 0)
												   {
													$row1 = mysql_fetch_array($res1);   
													$wccoapl=$row1[0];   //Aca defino de que centro de costos se va aplicar el insumo
												   } 
												  else
													 {
													  $wccoapl=$wcco;   //Aca defino de que centro de costos se va aplicar el insumo
													 }
												 
												//Aca actualizo el saldo del paciente para el articulo y cco
												$q= " UPDATE ".$wbasedato."_000004 "
												   ."    SET spausa = spausa + ".round($wcant_aplicar,3)                        //Salidas Unix : Dosis a aplicar dividido cantidad de Fracciones por Unidad de Manejo
												   ."  WHERE spahis = '".$whis."'"
												   ."    AND spaing = '".$wing."'"
												   ."    AND spaart = '".strtoupper($row[1])."'"
												   ."    AND spacco = '".$wccoapl."'";
												$resgra = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
											   }
										   }
										else
										   {
											?>	    
											 <script>
											   alert("3. Debe cerrar esta ventana, Actualizar la pantalla (simbolo de flecha circular)  y volver a ingresar a la opción ** Aplicacion Ipods **");
											 </script>
											<?php  
										   }   
									   }   
									   $waplicado="on";  //Esto se porque si wexiste==on es porque ya esta aplicado
									   
									echo "<td align=center rowspan=4 bgcolor='00FF00'><font size=6>Aplicado</font></td>";            
									echo "<td align=center rowspan=4><A HREF='Aplicacion_ipods1.php?wemp_pmla=".$wemp_pmla.$waplicados."&wcco=".$wcco."&whis=".$whis."&wing=".$wing."&wfecha_actual=".$wfecha_actual."&whora_par_actual=".$whora_par_actual."&whab=".$whab."&wpac=".$wpac."&wanular[".$i."]=on&wapl[".$i."]=off&wart[".$i."]=".$row[1]."&wdosis[".$i."]=".$row[7]."&wcanfra[".$i."]=".$row[12]."&wnoenviar[".$i."]=".$wnoenviar[$i]."&wStock[".$i."]=".$wStock[$i]."&wido[".$i."]=".$row[22]."' class=tipo3V>Anular</A></td>";
									
									//Marzo 17 de 2011 
									echo "<script>$.unblockUI()</script>";
								   }
								  else 
									 {
									  echo "<td align=center rowspan=4 colspan=2><A HREF='Aplicacion_ipods1.php?wemp_pmla=".$wemp_pmla.$waplicados."&wcco=".$wcco."&whis=".$whis."&wing=".$wing."&wfecha_actual=".$wfecha_actual."&whora_par_actual=".$whora_par_actual."&whab=".$whab."&wpac=".$wpac."&wido[".$i."]=".$row[22]."&wapl[".$i."]=on ' class=tipo3V>Aplicar</A></td>";
									 }
							   } 
							  else 
								 {
								  if ($wok == true and $waplicado=="off")   
									 {
									  if ($wStock[$i]=="on")   
										 echo "<td align=center rowspan=4 colspan=2><A HREF='Aplicacion_ipods1.php?wemp_pmla=".$wemp_pmla.$waplicados."&wcco=".$wcco."&whis=".$whis."&wing=".$wing."&wfecha_actual=".$wfecha_actual."&whora_par_actual=".$whora_par_actual."&whab=".$whab."&wpac=".$wpac."&wido[".$i."]=".$row[22]."&wapl[".$i."]=on ' class=tipo3V>Aplicar (Stock)</A></td>";
										else
										   echo "<td align=center rowspan=4 colspan=2><A HREF='Aplicacion_ipods1.php?wemp_pmla=".$wemp_pmla.$waplicados."&wcco=".$wcco."&whis=".$whis."&wing=".$wing."&wfecha_actual=".$wfecha_actual."&whora_par_actual=".$whora_par_actual."&whab=".$whab."&wpac=".$wpac."&wido[".$i."]=".$row[22]."&wapl[".$i."]=on ' class=tipo3V>Aplicar</A></td>"; 
									 }
									else
									   if ($waplicado != "on")
										  {
										   echo "<td align=center rowspan=4 bgcolor='00FF00' colspan=2><font size=4>".$wmensaje."</font></td>";
										  }
										 else
										   {
										    echo "<td align=center rowspan=4 bgcolor='00FF00'><font size=6>Aplicado</font></td>"; 
											echo "<td align=center rowspan=4><A HREF='Aplicacion_ipods1.php?wemp_pmla=".$wemp_pmla.$waplicados."&wcco=".$wcco."&whis=".$whis."&wing=".$wing."&wfecha_actual=".$wfecha_actual."&whora_par_actual=".$whora_par_actual."&whab=".$whab."&wpac=".$wpac."&wanular[".$i."]=on&wapl[".$i."]=off&wart[".$i."]=".$row[1]."&wdosis[".$i."]=".$row[7]."&wcanfra[".$i."]=".$row[12]."&wnoenviar[".$i."]=".$wnoenviar[$i]."&wStock[".$i."]=".$wStock[$i]."&wido[".$i."]=".$row[22]."' class=tipo3V>Anular</A></td>";
										   } 
								 } 
								  
							echo "</tr>";
							
							echo "<tr class=".$wclass."><td colspan=6 bgcolor='FFFF99'><font size=5><b>Observaciones: </b>".$row[13]."</font></td></tr>";         //Observaciones
							echo "<tr class=".$wclass."><td colspan=6 bgcolor='FFCC66'><font size=5><b>Alertas      : </b>".$row[14]."</font></td></tr>";         //Alertas
							                    
												                                                                           //         Artciulo kadido (id original kardex)
							justificacionNoAplicacion($i, $wjustificacion, $waplicado, $whis, $wing, $wfecha_actual, $whora_a_grabar, $row[1], $row[22]);
							echo "<tr><td colspan=4>&nbsp;</td></tr>";
							echo "<tr><td colspan=4>&nbsp;</td></tr>";
						   }
					   }
					}
				echo "</table>";    
				   
				echo "<br><br>";
				echo "<table>";
				echo "<tr><td><A HREF='Aplicacion_ipods1.php?wemp_pmla=".$wemp_pmla."&wcco=".$wcco."&wfecha_actual=".$wfecha_actual."&whora_par_actual=".$whora_par_actual."&wzona=".$wzona."' class=tipo3V>Retornar</A></td></tr>";
				echo "</table>";	  
			   }
			  else
				 {
				  //Mayo 26 de 2011
				  if (CcoTieneZonas())
				     {
					  if (!isset($wzona) or trim($wzona) == "")
					     {
						  $wzona=strtoupper(SeleccionarZona());
					      echo "<br><br><br><br>";
						 }
						else
						   elegir_historia($wzona); 
					 }
					else                             //Termina modificacion de Mayo 26 de 2011
				       elegir_historia("");  
				  
				  echo "<br><br>";
				  echo "<table>";
				  echo "<tr><td><A HREF='Aplicacion_ipods1.php?wemp_pmla=".$wemp_pmla."&wfecha_actual=".$wfecha_actual."&whora_par_actual=".$whora_par_actual."&wzona=".$wzona."' class=tipo3V>Retornar</A></td></tr>";
				  echo "</table>";	  
				 }
		 }
		else
           {
            ?>	    
			  <script>
			    alert("¡ATENCION!. Debe cerrar esta ventana, Luego actualizar la pantalla (presionando el simbolo de flecha circular arriba)  y volver a ingresar a la opción ** Aplicacion Ipods **");
			  </script>
		    <?php  
           }		   
       }	          

} // if de register

?>