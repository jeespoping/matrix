<head>
  <title>CONTINGENCIA KARDEX DE ENFERMERIA</title>
  
  <style type="text/css">
    	
    	A	{text-decoration: none;color: #000066;}
    	.tipo3V{color:#000066;background:#dddddd;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3V:hover {color: #000066; background: #999999;}
        .tipo4V{color:#000066;background:#dddddd;font-size:15pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo4V:hover {color: #000066; background: #999999;}
        .tipo3VTurno{color:#000066;background:#FFFFCC;font-size:12pt;font-family:Arial;font-weight:bold;text-align:center;border-style:outset;height:1.5em;cursor: hand;cursor: pointer;padding-right:5px;padding-left:5px}
        .tipo3VTurno:hover {color: #000066; background: #999999;}
        .tipoTA{color:#000066;background:#FFFFCC;font-size:10pt;font-family:Arial;font-weight:bold;text-align:left;}
        .tipoMx{color:#000066;background:#FFFFCC;font-size:10pt;font-family:Arial;font-weight:bold;text-align:center;}
        <!--.tipoTA:hover {color: #000066; background: #999999;} -->
    	
    </style>
  
</head>

<style>
	BODY            
	{
		font-family: verdana;
		font-size: 10pt;
		height: 1024px;
		width: 1280px;
	}
	.encabezadoTabla                                 
	{
		 background-color: #2A5DB0;
		 color: #FFFFFF;
		 font-size: 10pt;
		 font-weight: bold;
	}
	.fila1                                
	{
		 background-color: #C3D9FF;
		 color: #000000;
		 font-size: 10pt;
	}
	.fila2                                
	{
		 background-color: #E8EEF7;
		 color: #000000;
		 font-size: 10pt;
	}
	
	.tituloPagina                     
{
	 font-family: verdana;
	 font-size: 18pt;
	 overflow: hidden;
	 text-transform: uppercase;
	 font-weight: bold;
	 height: 30px;
	 border-top-color: #2A5DB0;
	 border-top-width: 1px;
	 border-left-color: #2A5DB0;
	 border-left-width: 1px;
	 border-right-color: #2A5DB0;
	 border-bottom-color: #2A5DB0;
	 border-bottom-width: 1px;
	 margin: 2pt;
}
</style>

<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>

<script type="text/javascript">

document.onkeydown = mykeyhandler; 

function mykeyhandler(event) 
   {
      //keyCode 116 = F5
	  //keyCode 122 = F11
	  //keyCode 8 = Backspace
	  //keyCode 37 = LEFT ROW
	  //keyCode 78 = N
	  //keyCode 39 = RIGHT ROW
	  //keyCode 67 = C
	  //keyCode 86 = V
	  //keyCode 85 = U
	  //keyCode 45 = Insert
	 
	  event = event || window.event;
	  var tgt = event.target || event.srcElement;
	  if((event.altKey && event.keyCode==37) || (event.altKey && event.keyCode==39) ||
	  (event.ctrlKey && event.keyCode==78)|| (event.ctrlKey && event.keyCode==67)||
	  (event.ctrlKey && event.keyCode==86)|| (event.ctrlKey && event.keyCode==85)||
	  (event.ctrlKey && event.keyCode==45)|| (event.shiftKey && event.keyCode==45)){
	  event.cancelBubble = true;
	  event.returnValue = false;
	  alert("Funci?n no permitida");
	  return false;
	  }
	 
	  if(event.keyCode==18 && tgt.type != "text" && tgt.type != "password" && tgt.type != "textarea")
	    {
	     return false;
	    }
	 
	  if (event.keyCode == 8 && tgt.type != "text" && tgt.type != "password" && tgt.type != "textarea")
	    {
	     return false;
	    }
	 
	  if ((event.keyCode == 116) || (event.keyCode == 122)) 
	     {
	      if (navigator.appName == "Microsoft Internet Explorer")
	        {
	         window.event.keyCode=0;
	        }
	      return false;
	    }
   } 

function enter()
	{
	 document.forms.contingencia.submit();
	}
	
function cerrarVentana()
	 {
      window.close()		  
     }
     
function recarga()   
     {
	  var dvAux = document.createElement( "div" );

	  dvAux.innerHTML = "<INPUT type='hidden' name='foto'>";
      dvAux.firstChild.value = document.getElementById( "entregaTurno" ).innerHTML;
      document.forms[0].appendChild( dvAux.firstChild );
      document.forms[0].submit();    
     }   

//Descargar contigencia
function go_saveas(wcco, raiz_url, wemp_pmla, wfecha, whora, wminutos, wsegundos)
{

	document.location.href='descargar_contingencia.php?wraiz_url='+raiz_url+'&wcco='+wcco+'&wemp_pmla='+wemp_pmla+'&wfecha='+wfecha+'&whora='+whora+'&wminutos='+wminutos+'&wsegundos='+wsegundos;
  
}	 
     
</script>

<body>

<?php
include_once("conex.php");
  /*********************************************************
   *               ENTREGA DE TURNO ENFERMERIA             *
   *    EN LA UNIDAD EN DONDE SE ENCUENTRE EL PACIENTE     *
   *     				CONEX, FREE => OK				   *
   *********************************************************/
//==================================================================================================================================
//PROGRAMA                   : Entrega_de_Turno_Enfermeria.php
//AUTOR                      : Juan Carlos Hern?ndez M.
//$wautor="Juan C. Hernandez M. ";
//FECHA CREACION             : Agosto 30 de 2010
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="(Enero 29 de 2015)"; 
//DESCRIPCION 
//========================================================================================================================================\\
//========================================================================================================================================\\
//     Programa usado para la entrega de turno de enfermer?a.                                                                             \\
//     ** Funcionamiento General:                                                                                                         \\
//     Se selecciona el servicio en el que se esta, se despliega la lista de paciente en el servicio y se va ingresando a cada uno de     \\
//     ellos y dando click en checkbox 'Grabar' y luego enter, cuando se terminen de grabar todos los pacientes, al final de la lista de  \\
//     los pacientes se ingresa el c?digo de la enfermera que recibe y si es valido se da click en checkbos 'Grabar' y queda entrega el   \\
//     turno de todos los pacientes.                                                                                                      \\
//     Si se ingresa con una fecha diferente a la actual o en un turno ya entrega no da la posibilidad de modificar o grabar nada         \\
//     Tabla(s) : movhos_000096                                                                                                           \\
//         * En esta tabla se graba c/u de los pacientes con la "foto" de lo que tenia el kardex en el momento de la entrega.             \\        
//========================================================================================================================================\\
//========================================================================================================================================\\
//Enero 29 de 2015 (Jonatan Lopez)	   :   Se agregan los examenes registrados desde las ordenes electronicas.
//Nov. 26 de 2014 (Juan C. Hern?ndez)  :   Se adecua este programa para que salgan la contingencia de Urgecias debido a que se implementan\\
//                                         las salas y lo cubiculos.                                                                      \\
//Mayo 15 de 2014 (Juan C. Hern?ndez)  :   Se modifica para que el orden de las habitaciones o de los registros del SQL salgan de acuerdo \\
//                                         al campo "habord" (orden) de la tabla movhos_000020 que es la tabla de habitaciones.           \\
//Diciembre 05 de 2013:					   Se renueva la opcion de descargar contingencia para que funcione en todos los navegadores.     \\
//Septiembre 05 de 2013 Edwin Molina G :   Se corrige la muestra de observaciones, esto debido a que el kardex guarda el historico de las \\
//										   de las observaciones en formato html.                                                          \\
//========================================================================================================================================\\
//Noviemnbre 06 de 2012 Jonatan Lopez  :   Se corrige el ciclo para que muestre todos los liquidos endodenosos.                           \\
//========================================================================================================================================\\
//Enero 24 de 2011 : Se adiciona el campo de alertas que se registra en el Kardex                                                         \\
//========================================================================================================================================\\
session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
		
if(!isset($_SESSION['user']))
	echo "error";
else
{	            
 	
  

  include_once("root/comun.php");
  include_once("movhos/movhos.inc.php");
  

  
  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));
  		
 
  echo "<br>";				
  echo "<br>";
  
  //*******************************************************************************************************************************************
  //F U N C I O N E S
  //===========================================================================================================================================
  function dameURL()
  {
	$url="http://".$_SERVER['HTTP_HOST'];
	return $url;	
  }  
  
  function mostrar_empresa($wemp_pmla)
     {  
	  global $user;   
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
	      
	  encabezado("Contingencia Kardex de Enfermer?a",$wactualiz, "clinica");  
     }
   
  /*
  function traer_diagnostico($whis, $wing, $wfecha)
     {
	  global $conex;
	  global $wbasedato;   
	     
	  $q = " SELECT kardia "
          ."   FROM ".$wbasedato."_000053 A"
          ."  WHERE karhis       = '".$whis."'"
          ."    AND karing       = '".$wing."'"
          ."    AND karest       = 'on' "
          ."    AND A.fecha_data = '".$wfecha."'";  
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $wnum = mysql_num_rows($res);
	  
	  if ($wnum > 0)
	     {
		  $row = mysql_fetch_array($res);   
		  
		  return $row[0];   
	     }
	    else
	      return "Sin Diagnostico";     
	 }  
   */     
	 
  function traer_medico_tte($whis, $wing, $wfecha, &$i)
     {
	  global $conex;
	  global $wbasedato;
	     
	  $q = " SELECT Medno1, Medno2, Medap1, Medap2  "
          ."   FROM ".$wbasedato."_000047, ".$wbasedato."_000048 "
          ."  WHERE methis = '".$whis."'"
          ."    AND meting = '".$wing."'"
          ."    AND metest = 'on' "
          ."    AND metfek = '".$wfecha."'"
          ."    AND mettdo = medtdo "
          ."    AND metdoc = meddoc ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $wnum = mysql_num_rows($res);
	  
	  if ($wnum > 0)
	     {
		  $wmed="";   
		  for ($i=1; $i <= $wnum;$i++)
		     {
			  $row = mysql_fetch_array($res);    
			  
			  if ($i < $wnum)
		         $wmed = $wmed.$row[0]." ".$row[1]." ".$row[2]." ".$row[3]."<br>";
		        else
		           $wmed = $wmed.$row[0]." ".$row[1]." ".$row[2]." ".$row[3];
	         } 
		  return $wmed;   
	     }
	    else
	      return "Sin M?dico";     
	 }       	 
	 
  function traer_LEV($whis, $wing, $wfecha, &$wnum)
     {
	  global $conex;
	  global $wbasedato;   
	  
	  global $wlev_des;
	  global $wlev_obs;
	     
	  $q = " SELECT inkdes, inkobs "
          ."   FROM ".$wbasedato."_000051 A"
          ."  WHERE inkhis = '".$whis."'"
          ."    AND inking = '".$wing."'"
          ."    AND inkfec = '".$wfecha."'";  
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $wnum = mysql_num_rows($res);
	  
	   if ($wnum > 0)
        {
            for ($i=0; $i <= $wnum;$i++)
            {
                
            $row = mysql_fetch_array($res);
       
            if($row[0] == "")
                {
                $wlev_des[$i] = '';  
                $wlev_obs[$i]=$row[1];
                }
            else
                {
                $wlev=str_replace(";","<br>",$row[0]);
                $wlev[$i]=str_replace("\n","<br>",$wlev);                
                $wlev_des[$i]=$wlev;
                $wlev_obs[$i]=$row[1];
                }
            }
        }
        else
        {
            $wlev_des="";
            $wlev_obs="";
        }
	 }
	 
  function traer_medicamentos($whis, $wing, $wfecha, &$i)
     {
	  global $conex;
	  global $wbasedato;
	  global $wcenmez;
	  
	  global $wartic;   
	  global $wdosis;
	  global $wfrecu;
	  global $wfecin;
	  global $whorai;
	  global $wcondi;
	  global $wfecgra;
	  global $wobserv;
	  global $wfrenum;
	  
	  
	  //Traigo los Kardex GENERADOS con articulos de DISPENSACION 
	  $q = " SELECT B.artcom, A.kadcfr, A.kadper, A.kadfin, A.kadhin, C.percan, C.peruni, A.kadufr, A.kadcnd, A.fecha_data, A.kadobs "
	      ."   FROM ".$wbasedato."_000054 A, ".$wbasedato."_000026 B, ".$wbasedato."_000043 C,".$wbasedato."_000053 "
	      ."  WHERE kadhis  = '".$whis."'"
	      ."    AND kading  = '".$wing."'"
	      ."    AND kadfec  = '".$wfecha."'"  
	      ."    AND kadest  = 'on' "
	      ."    AND kadart  = artcod "
	      ."    AND kadori  = 'SF' "
	      ."    AND kadper  = percod "
	      ."    AND kadhis  = karhis "
	      ."    AND kading  = karing "
	      ."    AND karcon  = 'on' "
	      ."    AND karcco  = kadcco "
	      ."    AND kadsus != 'on' "
	      ." UNION "
	      //Traigo los Kardex GENERADOS con articulos de CENTRAL DE MEZCLAS
	      ." SELECT B.artcom, A.kadcfr, A.kadper, A.kadfin, A.kadhin, C.percan, C.peruni, A.kadufr, A.kadcnd, A.fecha_data, A.kadobs "
	      ."   FROM ".$wbasedato."_000054 A, ".$wcenmez."_000002 B, ".$wbasedato."_000043 C,".$wbasedato."_000053 D "
	      ."  WHERE kadhis  = '".$whis."'"
	      ."    AND kading  = '".$wing."'"
	      ."    AND kadfec  = '".$wfecha."'"  
	      ."    AND kadest  = 'on' "
	      ."    AND kadart  = artcod "
	      ."    AND kadori  = 'CM' "
	      ."    AND kadper  = percod "
	      ."    AND kadhis  = karhis "
	      ."    AND kading  = karing "
	      ."    AND karcon  = 'on' "
	      ."    AND karcco  = kadcco "
	      ."    AND kadsus != 'on' "
	      ." UNION "
	      //Traigo los Kardex en TEMPORAL (000060) con articulos de DISPENSACION 
	      ." SELECT B.artcom, A.kadcfr, A.kadper, A.kadfin, A.kadhin, C.percan, C.peruni, A.kadufr, A.kadcnd, A.fecha_data, A.kadobs "
	      ."   FROM ".$wbasedato."_000060 A, ".$wbasedato."_000026 B, ".$wbasedato."_000043 C,".$wbasedato."_000053 D "
	      ."  WHERE kadhis  = '".$whis."'"
	      ."    AND kading  = '".$wing."'"
	      ."    AND kadfec  = '".$wfecha."'"  
	      ."    AND kadest  = 'on' "
	      ."    AND kadart  = artcod "
	      ."    AND kadori  = 'SF' "
	      ."    AND kadper  = percod "
	      ."    AND kadhis  = karhis "
	      ."    AND kading  = karing "
	      ."    AND karcon  = 'on' "
	      ."    AND karcco  = kadcco "
	      ."    AND kadsus != 'on' "
	      ." UNION "
	      //Traigo los Kardex en TEMPORAL (000060) con articulos de CENTRAL DE MEZCLAS
	      ." SELECT B.artcom, A.kadcfr, A.kadper, A.kadfin, A.kadhin, C.percan, C.peruni, A.kadufr, A.kadcnd, A.fecha_data, A.kadobs "
	      ."   FROM ".$wbasedato."_000060 A, ".$wcenmez."_000002 B, ".$wbasedato."_000043 C,".$wbasedato."_000053 D "
	      ."  WHERE kadhis  = '".$whis."'"
	      ."    AND kading  = '".$wing."'"
	      ."    AND kadfec  = '".$wfecha."'"  
	      ."    AND kadest  = 'on' "
	      ."    AND kadart  = artcod "
	      ."    AND kadori  = 'CM' "
	      ."    AND kadper  = percod "
	      ."    AND kadhis  = karhis "
	      ."    AND kading  = karing "
	      ."    AND karcon  = 'on' "
	      ."    AND karcco  = kadcco "
	      ."    AND kadsus != 'on' ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $wnum = mysql_num_rows($res);
	  
	  if ($wnum > 0)
	     {
		  for ($i=1; $i <= $wnum;$i++)
		     {
			  $row = mysql_fetch_array($res);    
			  
			  $wartic[$i]   = $row[0];                                 //Medicamento
		      $wdosis[$i]   = $row[1]." ".$row[7];                     //Dosis y fracciones de la dosis
		      if ($row[5]   > 1)
			     $wfrecu[$i]="Cada ".$row[5]."&nbsp;".$row[6]."S";     //Descripcion de la FRECUENCIA
			    else
			       $wfrecu[$i]="Cada ".$row[5]."&nbsp;".$row[6];       //Descripcion de la FRECUENCIA 
			  $wfrenum[$i]  = $row[5];                                 //Frecuencia numerica
		      $wfecin[$i]   = $row[3];                                 //Fecha de Inicio
		      $whorai[$i]   = $row[4];                                 //Hora de Inicio
			  $wfecgra[$i]  = $row[9];                                 //Fecha de grabacion del articulo
		      
		      if (trim($row[8]) != "")                                 //Tiene Condicion
		         {
			      $q = " SELECT condes "
			          ."   FROM ".$wbasedato."_000042 "
			          ."  WHERE concod = '".$row[8]."'";
			      $rescon = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			      
			      $row1 = mysql_fetch_array($rescon);
			      
			      $wcondi[$i] = $row1[0];                               //Condicion
		         }
		        else
		           $wcondi[$i]=""; 
				   
			  $wobserv[$i]  = $row[10];                                 //Observaciones
	         } 
		 }
	    else
	      return "Sin Medicamentos";
	 }	 	 
	
// FUNCION QUE TRAE LAS OBSERVACIONES DEL DIA DE HOY Y LOS IMPRIME EN EL TEXTAREA DE CADA EXAMEN
        function traer_nombre_examen($wcodexam) {

            global $conex;
			global $wemp_pmla;
			
            $whce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'HCE');


            $query =   "SELECT Codigo, Descripcion "
                   ."   FROM ".$whce."_000047"
                   ."  WHERE Codigo = '".$wcodexam."'"
                   ."    AND Estado = 'on'";

            $res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
            $num = mysql_num_rows($res);
			
			if($num > 0){
				
				$row = mysql_fetch_array($res);
				$nombre_examen = $row['Descripcion'];
				
			}else{
				
				$query =    " SELECT Codigo, Descripcion "
						   ."   FROM ".$whce."_000017"
						   ."  WHERE Codigo = '".$wcodexam."'"
						   ."    AND Estado = 'on'";
				$res = mysql_query($query, $conex) or die("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				
				$row = mysql_fetch_array($res);
				$nombre_examen = $row['Descripcion'];
				
				
			}
            
            return $nombre_examen;

        }	
	
	
  function traer_examenes($whis, $wing, $wfecha, &$i)
     {
	  global $conex;
	  global $wbasedato;  
	  global $wemp_pmla;
	  global $wser; 
	  global $wexa;
	  global $wfes;
	  
	  $whce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'HCE');
	  
	  $q = " SELECT cconom, ekaobs, ekafes "
          ."   FROM ".$wbasedato."_000050, ".$wbasedato."_000011 "
          ."  WHERE ekahis = '".$whis."'"
          ."    AND ekaing = '".$wing."'"
          ."    AND ekafec = '".$wfecha."'"
          ."    AND ekaest = 'P' "              //Solo traigo los pendientes
          ."    AND ekacod = ccocod "
          ."  ORDER BY 1, 2, 3 ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $wnum = mysql_num_rows($res);
	  
	  if ($wnum > 0)
	     {
		  for ($i=1; $i <= $wnum;$i++)
		     {
			  $row = mysql_fetch_array($res);    
			  
			  $wser[$i] = $row[0];   
		      $wexa[$i] = $row[1];
		      $wfes[$i] = $row[2];
	         } 
			 
	     }
	 }
	 
	 
	function traer_examenes_ordenes($whis, $wing, $wfecha, &$i)
     {
	  global $conex;
	  global $wbasedato;  
	  global $wemp_pmla;
	  global $wser; 
	  global $wexa;
	  global $wfes;
	  
	  $whce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'HCE');
	  
	  $query1 =" SELECT Dettor, Detcod, Detjus, Detfec
				  FROM ".$whce."_000027 A, ".$whce."_000028 B, ".$wbasedato."_000045 C
				 WHERE Ordtor = Dettor
				   AND Ordnro = Detnro
				   AND A.Ordhis = '".$whis."'
				   AND A.Ording = '".$wing."'
				   AND B.Detesi = C.Eexcod
				   AND C.Eexpen = 'on'
				   AND B.Detest = 'on'
			  ORDER BY B.Detfec DESC";
	   $res1 = mysql_query($query1, $conex) or die(mysql_errno()." - Error en el query $sql - ".mysql_error());
	   $numord = mysql_num_rows($res1);
		
	   for ($i=1; $i <= $numord;$i++){
		   
			$roword = mysql_fetch_array($res1);  
			
			//Consulta el tipo de orden.
			$sql_tip_ord = "SELECT Codigo, Descripcion
							  FROM ".$whce."_000015
							 WHERE estado = 'on'
							   AND Codigo = '".$roword['Dettor']."'";	
			$res_tip_ord = mysql_query( $sql_tip_ord, $conex ) or die( mysql_errno()." - Error en el query $sql_tip_ord - ".mysql_error() );	
			$row_tip_ord = mysql_fetch_array( $res_tip_ord );
			
		   $wnombre_examen = traer_nombre_examen($roword['Detcod']);	
	   
	   
		   $wser[$i] = "<u>Tipo de Orden:</u><b> ".$row_tip_ord['Descripcion']."</b><br>".$wnombre_examen;   
		   $wexa[$i] = $roword['Detjus'];
		   $wfes[$i] = $roword['Detfec'];
		   
	   }
			 
			
	 }

  function traer_nombre_usuario($wusuario)
     {
	  global $conex;
	  
	  $q = " SELECT descripcion "
	      ."   FROM usuarios "
	      ."  WHERE codigo = '".$wusuario."'"
	      ."    AND activo = 'A' ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $wnum = mysql_num_rows($res);
	  
	  if ($wnum > 0)
	    {
	     $row = mysql_fetch_array($res);
	     return $row[0]; 
        }
       else
          return "";  
	 }
     
  function traer_dietas($whis, $wing, $wfecha, &$i)
     {
	  global $conex;
	  global $wbasedato;   
	  
	  global $wdie;
	     
	  $q = " SELECT diedes "
          ."   FROM ".$wbasedato."_000052, ".$wbasedato."_000041 "
          ."  WHERE dikhis = '".$whis."'"
          ."    AND diking = '".$wing."'"
          ."    AND dikfec = '".$wfecha."'"
          ."    AND dikest = 'on' "
          ."    AND dikcod = diecod ";  
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $wnum = mysql_num_rows($res);
	  
	  if ($wnum > 0)
	     {
		  for ($i=1; $i <= $wnum;$i++)
		     {
			  $row = mysql_fetch_array($res);    
			  
			  $wdie[$i] = $row[0];   
		     } 
	     }  
	 }	 
	 
	 
  function traer_dextrometer($whis, $wing, $wfecha, &$i)
     {
	  global $conex;
	  global $wbasedato;  
	  
	  global $wime; 
	  global $wima;
	  global $wdos;
	  global $wuni; 
	  global $wobs;
	  global $wvia;
	  global $wart;
	  global $wfre;
	  
	  
	  //Traigo los intervalos del dextrometer, con la v?a, el articulo (insulina) y la condici?n (horario)
	  //000071: Dextrometer por historia
	  //000027: Maestro de Unidades
	  //000040: Vias de admon
	  //000070: Informacion unica por Kardex; similar al encabezado del Kardex
	  //000042: Condiciones de suministro de medicamento "I": indica que son Insulinas
	  //000026: Maestro de Articulos (medicamentos).
	  
	  //Busco en la tabla del encabezado de dextrometer si tiene
      $q = " SELECT infade, inffde, infcde "
          ."   FROM ".$wbasedato."_000070 "
          ."  WHERE infhis = '".$whis."'"
          ."    AND infing = '".$wing."'"
          ."    AND inffec = '".$wfecha."'";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $wnum = mysql_num_rows($res);
	  
	  if ($wnum > 0)
	     {
		  $row = mysql_fetch_array($res);
		     
		  //Traigo el nombre del articulo
		  $q= " SELECT artcom FROM ".$wbasedato."_000026 WHERE artcod = '".$row[0]."' ";
	      $resart = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	      $rowart = mysql_fetch_array($resart);
	      
	      $wart = $rowart[0];        //Nombre del Articulo (insulina)
	      
	      //Traigo la descripcion de la frecuencia
	      $q= " SELECT condes FROM ".$wbasedato."_000042 WHERE concod = '".$row[1]."' AND contip = 'I' ";
	      $rescon = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	      $rowcon = mysql_fetch_array($rescon);
	      $wfre = $rowcon[0];        //Descripcion Frecuencia   
		   
	      //Query para traer el esquema, si es que lo tiene  
		  $q = " SELECT indime, indima, inddos, unides, indobs, viades "
	          ."   FROM ".$wbasedato."_000071, ".$wbasedato."_000027, ".$wbasedato."_000040 "
	          ."  WHERE indhis = '".$whis."'"
	          ."    AND inding = '".$wing."'"
	          ."    AND indfec = '".$wfecha."'"
	          ."    AND indudo = unicod "
	          ."    AND indvia = viacod ";
	      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	      $wnum = mysql_num_rows($res);
	     
	      if ($wnum > 0)
			  for ($i=1; $i <= $wnum;$i++)
			     {
				  $row = mysql_fetch_array($res);    
				  
				  $wime[$i] = $row[0];   
			      $wima[$i] = $row[1];
			      $wdos[$i] = $row[2];
			      $wuni[$i] = $row[3];   
			      $wobs[$i] = $row[4];
			      $wvia[$i] = $row[5];
			     }
		 }
	 }	 	 
	 
	 
  function elegir_cco()   
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
	  global $weda;
	  
	  global $wfec;
	  global $wfecha;
	  
	  global $wmed;
	  global $wdiag;
	  
	  global $whora_par_actual;
	  
	  $wcco1=explode("-",$wcco);
	  
	  
	  echo "<center><table>";
	  echo "<tr>";
      echo "<td align=right  class=fila1><b>Fecha : </b></td>";
      echo "<td align=center class=fila2>";
      
      if (!isset($wfec))
         campofechaSubmit("wfec",$wfecha);
        else 
          campofechaSubmit("wfec",$wfec);
          
      echo "</td>";
      echo "</tr>";
      echo "</table>";
	  
      //Seleccionar CENTRO DE COSTOS
	  echo "<center><table>";
      $q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom "
          ."   FROM ".$wtabcco.", ".$wbasedato."_000011 "
          ."  WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod "
          ."    AND (".$wbasedato."_000011.ccohos = 'on' "
		  ."     OR  ".$wbasedato."_000011.ccourg = 'on' "      //Nov. 26 2014
		  ."     OR  ".$wbasedato."_000011.ccocir = 'on') ";    //Nov. 26 2014
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $num = mysql_num_rows($res);
		
      
	  echo "<tr class=fila1><td align=center><font size=4>Seleccione la Unidad : </font></td>";
	  echo "<td align=center><select name='wcco' onchange='enter()'>";
	  if (isset($wcco)) 
	     echo "<option selected>".$wcco."</option>";
	    else 
	     echo "<option>&nbsp</option>";    
	  for ($i=1;$i<=$num;$i++)
	     {
	      $row = mysql_fetch_array($res); 
	      echo "<option>".$row[0]." - ".$row[1]."</option>";
         }
      echo "</select></td></tr>";
      echo "</table>";
	 }    	 
	 
  function mostrar_foto($whis, $wing, $wturno, $wfecha)
    {
	 global $conex;
	 global $wbasedato;
	 global $wcenmez;
	 global $wemp_pmla;   
	    
	 $q = " SELECT etufot, etuuse, etuusr "
	     ."   FROM ".$wbasedato."_000096 A, usuarios"
	     ."  WHERE etuhis = '".$whis."'"
	     ."    AND etuing = '".$wing."'"
	     ."    AND etutur = '".$wturno."'"
	     ."    AND etufec = '".$wfecha."'"
	     ;
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());   
	 $num = mysql_num_rows($res);
	 
	 if ($num > 0)
	    {
		 $row = mysql_fetch_array($res);  
		 echo $row[0];
		 
		 $wnom_ent=traer_nombre_usuario($row[1]);
		 $wnom_rec=traer_nombre_usuario($row[2]);
		 
		 echo "<center><table>";
		 echo "<tr class=encabezadoTabla>";
		 echo "<th>Entrega el Turno</th>";
		 echo "<th colspan=2>Recibe el Turno</th>";
		 echo "</tr>";
		 echo "<tr class=fila1>";
		 echo "<td>".$row[1]." <b>".$wnom_ent."</b></td>";
		 echo "<td>".$row[2]." <b>".$wnom_rec."</b></td>";
		 echo "</tr>";   
		 echo "</table>";
		}    
	}    
  
  function query_kardex($whis, $wing, $wfec, &$res)
    {
	 global $conex;
	 global $wbasedato;
	 global $wcenmez;
	 global $wemp_pmla;   
	    
	 $q = " SELECT karobs, kardia, kartal, karpes, karale, karcui, karter, karson, karcur, "
	     ."        karint, kardie, karmez, kardem, karcip, kartef, karrec, karanp, karais "
	     ."   FROM ".$wbasedato."_000053 A "
	     ."  WHERE karhis = '".$whis."'"
	     ."    AND karing = '".$wing."'"
	     ."    AND karest = 'on' "
	     ."    AND A.fecha_data = '".$wfec."'";
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());    
	}    

  function query_existe_entrega(&$res)
    {
	 global $conex;
	 global $wbasedato;
	 global $wcenmez;
	 global $wemp_pmla;
	 global $wturno;
	 global $whis;
	 global $wing;
	 global $wturno;
	 global $wfec;
	 
	 	    
	 $q = " SELECT etucco, etuhab, etuuse, etuusr, etuobs, etuobc, etufot "
	     ."   FROM ".$wbasedato."_000096 A "
	     ."  WHERE etuhis = '".$whis."'"
	     ."    AND etuing = '".$wing."'"
	     ."    AND etufec = '".$wfec."'"
	     ."    AND etutur = '".$wturno."'";
	 $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());  
	 $num = mysql_num_rows($res);
	 
	 if ($num > 0)
	    return true;
	   else
	      return false; 
	}     		
	
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
	
function horas_aplicacion($wfecgra, $wfecini, $whorini, $wfrec)
{
	global $wfecha;
	$wregleta="";

	//                                                   Fecha Actual Articulo, fecha Inicio aplicacion, hora inicio aplicacion, frecuencia
	$arrAplicacion = obtenerVectorAplicacionMedicamentos($wfecha              , $wfecini               , $whorini              , $wfrec);

	for ($i=1; $i <= 24; $i++)
	{
		//var_dump($arrAplicacion);
		if (isset($arrAplicacion[$i]) and $arrAplicacion[$i] == "*")
		{
			if (!isset($wregleta) or trim($wregleta)=="")
				$wregleta=$i;
			else
				$wregleta=$wregleta."-".$i; 
		}
	}
	return $wregleta;
}
  //===========================================================================================================================================  
  //*******************************************************************************************************************************************
  
  
  
  //===========================================================================================================================================
  //===========================================================================================================================================
  // P R I N C I P A L 
  //===========================================================================================================================================
  //===========================================================================================================================================
  echo "<form name='kardex' action='HCE_KardexEnf.php' method=post>";
    
  if (!isset($wfecha)) $wfecha = date("Y-m-d");
  $whora  = (string)date("H:i:s");
  
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
  echo "<input type='HIDDEN' name='wwhis' value='".$wwhis."'>";
  echo "<input type='HIDDEN' name='wwing' value='".$wwing."'>";
  
  mostrar_empresa($wemp_pmla);
  
  if ( date( "H" ) > "7" and date( "H" ) < "19" )
      $wtur_grabar="MA?ANA";
	else
	   $wtur_grabar="NOCHE";
   
	if(isset($wwhis) and isset($wwing))
	{
		$wfecha = date('Y-m-d');
		$wfec = "2015-08-24";
		$whora = date('H');
		$wminutos = date('i');
		$wsegundos = date('s');	
		
		//Le asigno variables a la funcion go_saveas para que permita la descarga del archivo desde el programa descargar_contingencia.
		//$contenido_html .= "<a href='#1' onClick='go_saveas(\"$wcco_inf\", \"$url\", \"$wemp_pmla\", \"$wfecha\", \"$whora\", \"$wminutos\", \"$wsegundos\"); return false'>Descargar Archivo de Contingencia</a><br><br>";
				
																								   // Nov 26 2014
		$q = " SELECT habcpa, habhis, habing, pacno1, pacno2, pacap1, pacap2, pacnac, pactid, pacced, habzon "
			."   FROM ".$wbasedato."_000020, ".$wbasedato."_000018, root_000036, root_000037 "
			//."  WHERE habcco  = '".$wcco1[0]."'"
			."  WHERE habhis  = '".$wwhis."'"
			."    AND habing  = '".$wwing."'"
			."    AND habali != 'on' "            //Que no este para alistar
			."    AND habdis != 'on' "            //Que no este disponible, osea que este ocupada
			."    AND ubihis  = '".$wwhis."' "
			."    AND ubiing  = '".$wwing."' "
			."    AND ubihis  = orihis "
			."    AND ubiing  = oriing "
			."    AND ubiald != 'on' "
			//."    AND ubisac  = '".$wcco1[0]."'"
			."    AND oriori  = '".$wemp_pmla."'"  //Empresa Origen de la historia 
			."    AND oriced  = pacced "
			."    AND oritid  = pactid "
			."    AND habhis  = ubihis "
			."    AND habing  = ubiing "
			."  GROUP BY 1,2,3,4,5,6,7 "
			."  ORDER BY habord, habcod "; 
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res); 
	   
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);
			
			$whab = $row[0];
			$whis = $row[1];
			$wing = $row[2];
			$wpac = $row[3]." ".$row[4]." ".$row[5]." ".$row[6];
			$wnac = $row[7];
			$wtid = $row[8];
			$wdpa = $row[9];
			$wzon = $row[10];  //Nov. 26 2014
			
			//Nov 26 2014
			//====================================
			if (strtoupper($wzon) == "NO APLICA")
			   $wzon="";
			//====================================
			
			//Calculo la edad
			$wfnac=(integer)substr($wnac,0,4)*365 +(integer)substr($wnac,5,2)*30 + (integer)substr($wnac,8,2);
			$wfhoy=(integer)date("Y")*365 +(integer)date("m")*30 + (integer)date("d");
			$weda=(($wfhoy - $wfnac)/365);
			if ($weda < 1)
			   $weda = number_format(($weda*12),0,'.',',')."<b> Meses</b>";
			else
				 $weda=number_format($weda,0,'.',',')." A?os";
			
			$wdiag=traer_diagnostico($whis, $wing, $wfec);
			if ($wdiag=="Sin Diagnostico")    //Si no lo encontro en el Kardex actual, lo busco en el Kardex de d?a anterior
			{
				$dia = time()-(1*24*60*60);   //Resta un dia (2*24*60*60) Resta dos y //asi...
				$wayer = date('Y-m-d', $dia); //Formatea dia 
				$wdiag=traer_diagnostico($whis, $wing, $wayer);
			} 
			$wmed=traer_medico_tte($whis, $wing, $wfec, $j);
			if ($wmed=="Sin M?dico")         //Si no lo encontro en el Kardex actual, lo busco en el Kardex de d?a anterior
			{
				$dia = time()-(1*24*60*60);   //Resta un dia (2*24*60*60) Resta dos y //asi...
				$wayer = date('Y-m-d', $dia); //Formatea dia
				$wmed=traer_medico_tte($whis, $wing, $wayer, $j);
			} 
			$wfec_con=$wfec;                              //Fecha a consultar para todas los datos del kardex 
			$wmensaje="Kardex Actualizado a la fecha";
			
			query_kardex($whis, $wing, $wfec_con, $res1);
			$num1 = mysql_num_rows($res1);
			
			if ($num1 == 0)                                //Si no se encuentra Kardex Confirmado en la fecha actual, traigo kardex del dia anterior
			{
				$dia = time()-(1*24*60*60);               //Resta un dia (2*24*60*60) Resta dos y //asi...
				$wayer = date('Y-m-d', $dia);             //Formatea dia 
				
				$wfec_con=$wayer;                         //Fecha a consultar para todas los datos del kardex
				$wmensaje="Kardex SIN Actulizar a la fecha";
				
				query_kardex($whis, $wing, $wfec_con, $res1);
				$num1 = mysql_num_rows($res1);
			} 
			   
			if ($num1 > 0)
			{
				$row1 = mysql_fetch_array($res1);
				
				$contenido_html .= "<div id='entregaTurno'>";
				
				$contenido_html .= "<center><table>"; 
				$contenido_html .= "<tr class=encabezadoTabla>";
				$contenido_html .= "<th><font size=6 color='FFFF33'><b>".$wfec."&nbsp;&nbsp;&nbsp;".$wturno."</b></font></th>";
				$contenido_html .= "</tr>";
				$contenido_html .= "<tr class=encabezadoTabla>";
				$contenido_html .= "<th><font size=3 color='FFFF33'><b>".$wmensaje." ".$wfec_con."</b></font></th>";
				$contenido_html .= "</tr>";
				
				$contenido_html .= "</table>";
								
				$contenido_html .= "<center><table>"; 
				$contenido_html .= "<tr class=fila1>";
				$contenido_html .= "<th><font size=3>Habitaci?n "."</font></th>";
				$contenido_html .= "<th><font size=3>Documento</font></th>";
				$contenido_html .= "<th><font size=3>Historia</font></th>";
				$contenido_html .= "<th><font size=3>Nombre</font></th>";
				$contenido_html .= "<th><font size=3>Edad</font></th>";
				$contenido_html .= "<th><font size=3>Talla</font></th>";
				$contenido_html .= "<th><font size=3>Peso</font></th>";
				$contenido_html .= "</tr>";
				$contenido_html .= "<tr class=fila2>";                                             //Nov. 26 2014
				$contenido_html .= "<td bgcolor=333399 align=center><b><font size=5 color='00FF00'>".$wzon." ".$whab."</font></b></td>";
				$contenido_html .= "<td align=center>".$wdpa."</td>";
				$contenido_html .= "<td align=center>".$whis."</td>";
				$contenido_html .= "<td align=center><font size=4><b>".$wpac."&nbsp&nbsp</b></font></td>";
				$contenido_html .= "<td align=center><font size=4><b>".$weda."</b></td>";
				$contenido_html .= "<td align=center>".$row1["kartal"]."</td>";
				$contenido_html .= "<td align=center>".$row1["karpes"]." Kg</td>";
				$contenido_html .= "</tr>";
				$contenido_html .= "</table>";
				
				$contenido_html .= "<br>";
				$contenido_html .= "<center><table>"; 
				
				//Diagnostico y Medico tratante
				$contenido_html .= "<tr class=encabezadoTabla>";
				$contenido_html .= "<td align=center colspan=4>Diagnostico(s)</td>";
				$contenido_html .= "<td align=center colspan=4>M?dico(s) Tratantes</td>";
				$contenido_html .= "</tr>";
				$contenido_html .= "<tr class=fila2>";
				$contenido_html .= "<td align=center colspan=4><textarea rows=3 cols=60 readonly class=tipoTA>".$wdiag."</textarea></td>";
				$contenido_html .= "<td align=center colspan=4 class=tipoMx>".$wmed."</td>";
				$contenido_html .= "</tr>";
				
				//Antecedentes Personales
				if (trim($row1["karanp"]) != "" or trim($row1["karale"]) != "")
				{
					$contenido_html .= "<tr class=encabezadoTabla>";
					$contenido_html .= "<td colspan=4 align=center><b>ANTECEDENTES PERSONALES</b></td>";
					$contenido_html .= "<td colspan=4 align=center><b>ANTECEDENTES ALERGICOS</b></td>";
					$contenido_html .= "</tr><tr class=fila2>";
					$contenido_html .= "<td align=center colspan=4><textarea rows=3 cols=60 readonly class=tipoTA>".$row1["karanp"]."</textarea></td>";
					$contenido_html .= "<td align=center colspan=4><textarea rows=3 cols=60 readonly class=tipoTA>".$row1["karale"]."</textarea></td>";
					$contenido_html .= "</tr><tr class=fila2>";
					$contenido_html .= "</tr>";
				} 
				$j=0; 
				
				traer_examenes($whis, $wing, $wfec_con, $j);   //Esto lo hago aca arriba porque necesito saber si tiene examenes para sacar o no el titulo de CONTROLES
				
				traer_LEV($whis, $wing, $wfec_con, $wnum);     //Informacion sobre liquidos endovenosos.
				   
				if ($j > 0 or $wnum > 0)
				{ 
					//Controles ********************
					$contenido_html .= "<tr class=encabezadoTabla>";
					$contenido_html .= "<td colspan=8 align=center><font size=4>CONTROLES</font></td>";
					$contenido_html .= "</tr>";
					
					$contenido_html .= "<tr class=fila1>";
					$contenido_html .= "<td colspan=8 align=center><b>LIQUIDOS ENDOVENOSOS</b></td>";
					$contenido_html .= "</tr>";
					$contenido_html .= "<tr class=fila1>";
					$contenido_html .= "<td align=center colspan=3><b>Componentes</b></td>";
					$contenido_html .= "<td align=center colspan=5><b>Observaciones</b></td>";
					$contenido_html .= "</tr>";

					for ($k=0; $k < $wnum; $k++)
					{
						$contenido_html .= "<tr class=fila2>";
						$contenido_html .= "<td colspan=3>".$wlev_des[$k]."</td>";                                    
						$contenido_html .= "<td colspan=5 align=center><textarea rows=3 cols=60 readonly class=tipoTA>".$wlev_obs[$k]."</textarea></td>";
						$contenido_html .= "</tr>";
					}
					  
					
					//Examenes
					if ($j > 0)
					{
						$contenido_html .= "<tr class=encabezadoTabla>";
						$contenido_html .= "<td colspan=8 align=center><b>EXAMENES y PROCEDIMIENTOS</b></td>";
						$contenido_html .= "</tr>";
						$contenido_html .= "<tr class=fila1>";
						$contenido_html .= "<td align=center><b>Examen</b></td>";
						$contenido_html .= "<td align=center colspan=3><b>Observaciones</b></td>";
						$contenido_html .= "<td align=center><b>Fecha</b></td>";
						$contenido_html .= "<td align=center colspan=3><b>Estado</b></td>";
						$contenido_html .= "</tr>";
						
						if ($j > 0)
						{
							for ($k=1; $k < $j; $k++)
							{
								$contenido_html .= "<tr class=fila2>";
								$contenido_html .= "<td>".$wser[$k]."</td>";
								$contenido_html .= "<td colspan=3><textarea rows=2 cols=60 readonly class=tipoTA>".$wexa[$k]."</textarea></td>";
								$contenido_html .= "<td align=center>".$wfes[$k]."</td>";
								$contenido_html .= "<td align=center colspan=3>Pendiente</td>";
								$contenido_html .= "</tr>";
							}
						}
					} 
				}
				  
				//Muestra los examenes creados desde ordenes.  
				traer_examenes_ordenes($whis, $wing, $wfec_con, $f);
				
				if($j == 0 and $f > 0)
				{
					$contenido_html .= "<tr class=encabezadoTabla>";
					$contenido_html .= "<td colspan=8 align=center><b>EXAMENES Y PROCEDIMIENTOS</b></td>";
					$contenido_html .= "</tr>";
					$contenido_html .= "<tr class=fila1>";
					$contenido_html .= "<td align=center><b>Examen</b></td>";
					$contenido_html .= "<td align=center colspan=3><b>Observaciones</b></td>";
					$contenido_html .= "<td align=center><b>Fecha</b></td>";
					$contenido_html .= "<td align=center colspan=3><b>Estado</b></td>";
					$contenido_html .= "</tr>";
				}
				
				if ($f > 0)
				{
					for ($e=1; $e < $f; $e++)
					{
						$contenido_html .= "<tr class=fila2>";
						$contenido_html .= "<td>".$wser[$e]."</td>";
						$contenido_html .= "<td colspan=3><textarea rows=2 cols=60 readonly class=tipoTA>".$wexa[$e]."</textarea></td>";
						$contenido_html .= "<td align=center>".$wfes[$e]."</td>";
						$contenido_html .= "<td align=center colspan=3>Pendiente</td>";
						$contenido_html .= "</tr>";
					}
				}
				
				//Pendientes ********************
				$contenido_html .= "<tr class=encabezadoTabla>";
				$contenido_html .= "<td colspan=8 align=center><font size=4>PENDIENTES</font></td>";
				$contenido_html .= "</tr>";
				
				$j=0;
				//Dietas
				traer_dietas($whis, $wing, $wfec_con, $j);
				if ($j > 0)
				{
					$contenido_html .= "<tr class=fila1>";
					$contenido_html .= "<td><font size=4><b>DIETA: </b></font></td>";
					$contenido_html .= "<td><table>";   
					for ($k=1; $k<$j; $k++)
					{
						$contenido_html .= "<tr>";
						$contenido_html .= "<td>** ".$wdie[$k]." **</td>";
						$contenido_html .= "</tr>";
					}
					$contenido_html .= "</table>";    
					$contenido_html .= "</td>";
					$contenido_html .= "<td align=left colspan=6><textarea rows=2 cols=60 readonly class=tipoTA>".$row1["kardie"]."</textarea></td>";
					$contenido_html .= "</tr>";    
				}
				
				//Sondas y Curaciones
				if (trim($row1["karson"]) != "" or trim($row1["karcur"]) != "")
				{
					$contenido_html .= "<tr class=fila1>";
					$contenido_html .= "<td colspan=4 align=center><b>SONDAS</b></td>";
					$contenido_html .= "<td colspan=4 align=center><b>CURACIONES</b></td>";
					$contenido_html .= "</tr>";
					$contenido_html .= "<tr class=fila2>";
					$contenido_html .= "<td align=center colspan=4><textarea rows=3 cols=60 readonly class=tipoTA>".$row1["karson"]."</textarea></td>";
					$contenido_html .= "<td align=center colspan=4><textarea rows=3 cols=60 readonly class=tipoTA>".$row1["karcur"]."</textarea></td>";
					$contenido_html .= "</tr>";
				} 
				
				//Cuidados de Enfermeria y Aislamientos
				if (trim($row1["karcui"]) != "" or trim($row1["karais"]) != "")
				{
					$contenido_html .= "<tr class=fila1>";
					$contenido_html .= "<td colspan=4 align=center><b>CUIDADOS DE ENFERMERIA</b></td>";
					$contenido_html .= "<td colspan=4 align=center><b>AISLAMIENTOS</b></td>";
					$contenido_html .= "</tr>";
					$contenido_html .= "<tr class=fila2>";
					//Cuidados de Enfermeria
					$contenido_html .= "<td align=center colspan=4><textarea rows=7 cols=60 readonly class=tipoTA align=left>".$row1["karcui"]."</textarea></td>";
					//Aislamientos
					$contenido_html .= "<td align=center colspan=4><textarea rows=7 cols=60 readonly class=tipoTA align=left>".$row1["karais"]."</textarea></td>";
					$contenido_html .= "</tr>";
				}
				 
					 
				$j=0; 
				$wart="";
				$wfre="";
				//Dextrometer
				traer_Dextrometer($whis, $wing, $wfec_con, $j);  
				
				//Mezclas y Dextrometer
				if (trim($wart) != "" or trim($wfre) != "" or $j > 0 or trim($row1["kardem"])!="")
				{
					$contenido_html .= "<tr class=fila1>";
					$contenido_html .= "<td colspan=4 align=center><b>MEZCLAS</b></td>";
					$contenido_html .= "<td colspan=4 align=center><b>DEXTROMETER</b></td>";
					$contenido_html .= "</tr>";
					
					if ($wart != "" or $wfre != "")
					{
						$contenido_html .= "<tr class=fila1>";
						$contenido_html .= "<td colspan=3 align=center><b>&nbsp</b></td>";
						$contenido_html .= "<td colspan=3 align=center><b>Insulina: </b><br>".$wart."</td>";
						$contenido_html .= "<td colspan=2 align=center><b>Frecuencia: </b><br>".$wfre."</td>";
						$contenido_html .= "</tr>";
					}
					$contenido_html .= "<tr class=fila2>";   
					$contenido_html .= "<td align=left colspan=4>".$row1["karmez"]."</td>";
					
					//Dextrometer
					$contenido_html .= "<td colspan=4>";
					
					$contenido_html .= "<center><table>";
					if ($j > 0)
					{   
						$contenido_html .= "<tr class=fila1>";
						$contenido_html .= "<td align=center><b>Int.Menor</b></td>";
						$contenido_html .= "<td align=center><b>Int.Mayor</b></td>";
						$contenido_html .= "<td align=center><b>Dosis</b></td>";
						$contenido_html .= "<td align=center><b>Unidad</b></td>";
						$contenido_html .= "<td align=center><b>Observaci?n</b></td>";
						$contenido_html .= "<td align=center><b>V?a Adm?n</b></td>";
						$contenido_html .= "</tr>";
												
						for ($k=1; $k < $j; $k++)
						{
							if (is_int ($k / 2))
							   $wclass = "fila1"; 
							else
								$wclass = "fila2";   
							$contenido_html .= "<tr class=".$wclass.">";
							$contenido_html .= "<td align=center>".$wime[$k]."</td>";
							$contenido_html .= "<td align=center>".$wima[$k]."</td>";
							$contenido_html .= "<td align=center>".$wdos[$k]."</td>";
							$contenido_html .= "<td align=center>".$wuni[$k]."</td>";
							$contenido_html .= "<td align=center>".$wobs[$k]."</td>";
							$contenido_html .= "<td align=center>".$wvia[$k]."</td>";
							$contenido_html .= "</tr>";
						}
					}
					   
					if (trim($row1["kardem"]) != "")
					{  
						$contenido_html .= "<tr>";   
						$contenido_html .= "<td align=center colspan=8><textarea rows=5 cols=60 readonly class=tipoTA>".$row1["kardem"]."</textarea></td>";   
						$contenido_html .= "</tr>";
					}
					$contenido_html .= "</table>";
					
					$contenido_html .= "</td>";
					$contenido_html .= "</tr>";   
				} 
				   
				//Cirugias e Interconsultas
				if (trim($row1["karcip"]) != "" or trim($row1["karint"]) != "")
				{
					$contenido_html .= "<tr class=fila1>";
					$contenido_html .= "<td colspan=4 align=center><b>CIRUGIAS</b></td>";
					$contenido_html .= "<td colspan=4 align=center><b>INTERCONSULTAS</b></td>";
					$contenido_html .= "</tr>";
					$contenido_html .= "<tr class=fila2>";
					$contenido_html .= "<td align=center colspan=4><textarea rows=3 cols=60 readonly class=tipoTA>".$row1["karcip"]."</textarea></td>";
					$contenido_html .= "<td align=center colspan=4><textarea rows=3 cols=60 readonly class=tipoTA>".$row1["karint"]."</textarea></td>";
					$contenido_html .= "</tr>";
				} 
				
				//Rehabilitacion Cardiaca y Antecedentes Personales
				if (trim($row1["karter"]) != "" or trim($row1["karrec"]) != "" or trim($row1["kartef"]) != "")
				{
					$contenido_html .= "<tr class=fila1>";
					$contenido_html .= "<td colspan=2 align=center><b>TERAPIA RESPIRATORIA</b></td>";
					$contenido_html .= "<td colspan=3 align=center><b>REHABILITACION CARDIACA</b></td>";
					$contenido_html .= "<td colspan=3 align=center><b>TERAPIA FISICA</b></td>";
					
					$wterres=str_replace("\n","<br>",htmlentities($row1["karter"],ENT_QUOTES));
					$wreacar=str_replace("\n","<br>",htmlentities($row1["karrec"],ENT_QUOTES));
					$wterfis=str_replace("\n","<br>",htmlentities($row1["kartef"],ENT_QUOTES));
					
					$contenido_html .= "</tr>";
					$contenido_html .= "<tr class=fila2>";
					$contenido_html .= "<td align=left colspan=2>".$wterres."</td>";
					$contenido_html .= "<td align=left colspan=3>".$wreacar."</td>";
					$contenido_html .= "<td align=left colspan=3>".$wterfis."</td>";
					$contenido_html .= "</tr>";
				} 
				  
				$j=0;    
				//Medicamentos
				traer_medicamentos($whis, $wing, $wfec_con, $j);
				$contenido_html .= "<tr class=encabezadoTabla>";
				$contenido_html .= "<td colspan=8 align=center><font size=4><b>MEDICAMENTOS</b></font></td>";
				$contenido_html .= "</tr>";
				$contenido_html .= "<tr class=fila1>";
				$contenido_html .= "<td align=center><b>Medicamento</b></td>";
				$contenido_html .= "<td align=center><b>Dosis</b></td>";
				$contenido_html .= "<td align=center><b>Frecuencia</b></td>";
				$contenido_html .= "<td align=center><b>Fecha Inicial</b></td>";
				$contenido_html .= "<td align=center><b>Hora de Inicio</b></td>";
				$contenido_html .= "<td align=center><b>Rondas de Aplicaci?n</b></td>";
				$contenido_html .= "<td align=center><b>Condici?n</b></td>";
				$contenido_html .= "<td align=center><b>Observaciones</b></td>";
				$contenido_html .= "</tr>";
				
				if ($j > 0)
				{
					for ($k=1; $k < $j; $k++)
					{
						$whora1 = explode(":",$whorai[$k]);           //Para solo mostrar el numero de la hora, sin los ceros (00:00)
				
						if (is_int ($k / 2))
						   $wclass = "fila1"; 
						else
							$wclass = "fila2";   
						$contenido_html .= "<tr class=".$wclass.">"; 
						$contenido_html .= "<td>".$wartic[$k]."</td>";               //Articulo
						$contenido_html .= "<td align=center>".$wdosis[$k]."</td>";  //Dosis
						$contenido_html .= "<td align=center>".$wfrecu[$k]."</td>";  //Frecuencia
						$contenido_html .= "<td align=center>".$wfecin[$k]."</td>";  //Fecha de Inicio
						$contenido_html .= "<td align=center>".$whora1[0]."</td>";   //Hora de Inicio
						$contenido_html .= "<td align=center>".horas_aplicacion($wfecgra[$k], $wfecin[$k], $whora1[0], $wfrenum[$k])."</td>";   //Regleta de aplicaci?n
						$contenido_html .= "<td align=center>".$wcondi[$k]."</td>";  //Condicion
						// $contenido_html .= "<td align=center><textarea row=3 col=30 readonly>".$wobserv[$k]."</textarea></td>";                //Observacion
						$contenido_html .= "<td style='width:300'><div style='overflow:auto;font-size:10pt;height:80px'>".$wobserv[$k]."</div></td>";                //Observacion	//Septiembre 05 de 2013
						$contenido_html .= "</tr>";
					}
				}   
				   
				//Observaciones Generales
				if (trim($row1["karobs"]) != "")
				{
					$contenido_html .= "<tr class=fila1>";
					$contenido_html .= "<td colspan=8 align=center><b>OBSERVACIONES GENERALES</b></td>";
					
					$contenido_html .= "</tr>";
					$contenido_html .= "<tr class=fila2>";
					$contenido_html .= "<td align=center colspan=8><textarea rows=3 cols=120 readonly class=tipoTA>".$row1["karobs"]."</textarea></td>";
					$contenido_html .= "</tr>";
				}    
				$contenido_html .= "<tr class=fila1>";
				$contenido_html .= "<td colspan=8><b>&nbsp</b></td>";
				$contenido_html .= "</tr>";  
				$contenido_html .= "</table>";
				$contenido_html .= "<br><br><br><br><br>";
			}
			else
				echo "<H1>SIN KARDEX DE ENFERMERIA</H1>";
		} 
	}
	$contenido_html .= "</form>";

	//Se imprime la variable con toda la informacion
	echo $contenido_html;

} // if de register

?>
