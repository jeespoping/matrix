<?php
include_once("conex.php");
session_start();
if(isset($consultaAjax) && $consultaAjax=='actualizarLog')//Actualiza la tabla de auditoria cuando visualizan una nueva alerta, 
															//para marcarla como ya leida y asi no volverla a mostrar en las alertas
{
	

	

	
	$leyo_id=explode("|",$id_alertas);
	echo date("Y-m-d");
	echo date("h:i:s");
	echo $usuario;
	foreach($leyo_id as $valor)
	{
		if ($valor!='' && $valor!=' ')
		{
			$q="UPDATE ".$wbasedato."_000078 
				   SET Audfle = '".date("Y-m-d")."'
				       Audhle = '".date("h:i:s")."'
					   Audule = '".$usuario."' 
				 WHERE id 	  = '".$valor."' 	 
			";
			echo $resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		}
	}
	return;
}
?>
<html>
<head>
  <title>MONITOR SERVICIO DE ALIMENTACION</title>
</head>
<body>
<script type="text/javascript" src="../../../include/movhos/mensajeriaDietas.js"></script>
<script type="text/javascript" src="../../../include/root/jquery-1.3.2.js"></script>
<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.core.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.tabs.min.js"></script>
<script type="text/javascript" src="../../../include/root/ui.draggable.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery-ui.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.simple.tree.js"></script>

<script type="text/javascript">
    arCco = new Array();
	arCcoPat = new Array();
    function enter()
	{
        document.forms.mondietas.submit();
	}
    
    window.onload=function()
	{	
		reload = setTimeout("enter()",60000);		
	};
	function isset(variable_name) 
	{
		try {
			 if (typeof(eval(variable_name)) != 'undefined')
			 if (eval(variable_name) != null)
			 return true;
		 } catch(e) { }
		return false;
    }	
    
    function reactivar(id_alertas)
	{
		reload = setTimeout("enter()",60000);
		if(id_alertas !='')
		{
			var wbasedato = document.getElementById("wbasedato").value;
			var usuario=document.getElementById("usuario").value;
			var wemp_pmla=document.getElementById("wemp_pmla").value;
			var wcco=document.getElementById("wcco").value;
			var wser=document.getElementById("wser").value;
			
			//actulizar la tabla de auditoria
			var parametros = "consultaAjax=actualizarLog&wbasedato="+wbasedato+"&usuario="+usuario+"&id_alertas="+id_alertas+""; 
			var resp = consultasAjax( "POST", "Monitor_dietas.php", parametros, false );
	
			enter();
		}
		
	}
    
	//setInterval("enter()",5000);
	function cerrarVentana()
	{
        window.close();		  
    }
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
    
    
    function fnMostrar(td)
    {
        var objetotd = document.getElementById(td);
        clearTimeout(reload);   //detener el refresh
		if( $("div", objetotd ).eq(0) )
        {
			$.blockUI({ message: $("div", objetotd ).eq(0), 
							css: {  left: '2%', 
									top:'10%',
								    width: '96%',
									height: 'auto',
									position:'absolute'									
								 } 
					  });
		}
	} 
	function fnMostrar2(td)
    {
        var objetotd = document.getElementById(td);
        clearTimeout(reload);   //detener el refresh
		if( $("div", objetotd ).eq(0) )
        {
			$.blockUI({ message: $("div", objetotd ).eq(0), 
							css: { left: ( $(window).width() - 600 )/2 +'px', 
								    top: ( $(window).height() - $("div", td ).height() )/2 +'px',
								  width: '600px'
								 }
					  });
			
		}
	}
    
    //mostrar los title de los nombres de los patrones
    $(document).ready(function()
	 {
		var cont1 = 1;
	    while(document.getElementById("wdie"+cont1))
	      {
	    	 $('#wdie'+cont1).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
	    	 $('#wcol'+cont1).tooltip({track: true, delay: 0, showURL: false, showBody: ' - ', opacity: 0.95, left: -50 });
	    	 cont1++;
          }; 
          
        var cont1 = 1;
	  }
	  );
     //fin mostrar titles
    
    //Mensajeria
    
    /************************************************************************************
	 * Muestra un mensaje debajo de un elemento
	 ************************************************************************************/
	function mostrar( campo, id ){
		return;
		try{
			clearInterval( interval );
		}
		catch(e){}

		var divTitle = document.getElementById( id );
		
		//divTitle.innerHTML = campo.title;

		divTitle.style.display = '';
		divTitle.style.position = 'absolute';
		divTitle.style.top = parseInt( findPosY(campo) ) + parseInt( campo.offsetHeight ) + 10;
		divTitle.style.left = findPosX( campo );
		divTitle.style.background = "#FFFFDF";
		divTitle.style.borderStyle = "solid";
		divTitle.style.borderWidth = "1px";
	}

	/************************************************************************************
	 * Actualiza los mensjaes sin leer cuando se actualiza la mensajeria
	 ************************************************************************************/
	function alActualizarMensajeria(Cco){
       
            
            var campo = document.getElementById( "sinLeer"+Cco );	
            campo.innerHTML = mensajeriaSinLeer;
			if(mensajeriaSinLeer>0)
			{
				var campo2 = document.getElementById( "sinLeer2"+Cco );	
				campo2.innerHTML = mensajeriaSinLeer;
			}
			else
			{
				var campo2 = document.getElementById( "sinLeer2"+Cco );	
				campo2.innerHTML = '';
			}
			
	}

	/**********************************************************************
	 * 
	 **********************************************************************/
    
     function enviandoMensaje(id, centro_costos)
     {
        var textarea = document.getElementById('detalle'+id).getElementsByTagName('textarea')[0];     
        
        if( document.getElementsByTagName('textarea')[0] != '' )
            {
            enviarMensaje2( textarea.value, document.getElementById( 'mensajeriaPrograma' ).value,document.getElementById( 'centro_costos'+id).value, document.forms.mondietas.servicio.value, document.getElementById( "usuario" ).value, document.getElementById( "wbasedato" ).value );
            }
        textarea.value='';
     }


	/**********************************************************************
	 * 
	 **********************************************************************/
	function marcarLeido( campo, id, cco ){
			
		//campo es una tabla que tiene toda la informacion que se muestra
		//Con dos fila
		//La primera fila tiene dos celdas y la segunda 1
		
		marcandoLeido( document.getElementById( "wbasedato" ).value, id, document.getElementById( "usuario" ).value );
		document.getElementById( "sinLeer"+cco ).innerHTML = document.getElementById( "sinLeer"+cco ).innerHTML-1;
		//quitando blinks
		for( var i = 0; i < campo.rows.length; i++ ){
			
			fila = campo.rows[i];
			
			for( var j = 0; j < fila.cells.length; j++ ){
				
				celda = fila.cells[j];
				
				if( celda.firstChild.tagName.toLowerCase() == "blink" ){
				
					var aux = celda.firstChild.innerHTML;
					
					celda.innerHTML = aux;
				}
			}
		}
	}

	/************************************************************************************************
	 *
	 * campo
	 ************************************************************************************************/
	function marcarPrioridad( campo ){

		//celda en la que se encuentra el boton de guardar
		var celda = 0;
		
		//Campo es el checkbox de prioridad
		fila = campo.parentNode.parentNode;	//Busco la fila en la que se encuentra el checkbox
		
		eval( fila.cells[ celda ].firstChild.href );	//Click en boton guardar
	}
	
	/************************************************************************************************/

	/****************************************************************************************************
	 * 
	 ****************************************************************************************************/
	function mostrarMensajeConfirmarKardex(){
		return;	//Septiembre 19 de 2011, Se deshabilita mostrar el mensaje para, esto por que viene tal cual el dia anterior
		var msjConfirmarKardex = document.getElementById( 'mostrarConfirmarKardex' );
		
		if(  msjConfirmarKardex && msjConfirmarKardex.value == 'on' ){
			//$.( '#txConfKar' ).blink();
			$.blockUI({ message: $('#msjConfirmarKardex') });		
		}
	}	
	
	/*****************************************************************************************************************************
    * Inicializa jquery
    ******************************************************************************************************************************/
    function inicializarJquery()
    {
        if (browser=="Microsoft Internet Explorer" || browser=="Netscape")
		{
			setInterval( "parpadeo()", 500 );
		}
        mostrarMensajeConfirmarKardex();	

        mensajeriaActualizarSinLeer = alActualizarMensajeria; 
        for( i=0; i < arCco.length; i++ )
        {
            consultarHistoricoTextoProcesado( document.getElementById( "wbasedato" ).value, document.getElementById( "wemp_pmla" ).value, document.getElementById( 'centro_costos'+arCco[i] ).value, document.forms.mondietas.servicio.value, document.getElementById( 'mensajeriaPrograma' ).value, document.getElementById( 'historicoMensajeria'+arCco[i] ), arCco[i] );	//Octubre 11 de 2011
            
            mensajeriaTiempoRecarga = consultasAjax( "POST", "../../../include/movhos/mensajeriaDietas.php", "consultaAjax=4&wemp="+document.getElementById( "wemp_pmla" ).value, false );	
            mensajeriaTiempoRecarga = mensajeriaTiempoRecarga*6000;	//El tiempo que se consulta esta en minutos
            
        }
        setInterval( "ciclo( document.getElementById( 'wbasedato' ).value, document.getElementById( 'wemp_pmla' ).value, arCco, document.forms.mondietas.servicio.value, document.getElementById( 'mensajeriaPrograma' ).value, document.getElementById( 'historicoMensajeria'+arCco[i] ), arCco );", mensajeriaTiempoRecarga );
        

        //setInterval( "mensajeriaActualizar()", mensajeriaTiempoRecarga );
    }
	//--------------------------------------------
	//	Avanzar o retroceder una columna
	//--------------------------------------------
	function mostrar_atras_adelante(mover)
	{
		var columna_patron = '';
		var posicion;
		var nombre;
		array_visibles = new Array();
		array_todos = new Array();
		
		$('.td_patron').each(
			function(index) 
			{
				posicion = $(this).attr("pos");
				posicion = posicion*1;
				nombre = $(this).attr("rel");
				if($(this).is(':visible'))
				{
					array_visibles[nombre] = posicion;
				}
				array_todos[posicion] = nombre;
			}
		);
		
		var maximo_visible;
		var minimo_visible;
		var nom_max;
		var nom_min;
		var temporal;
		var pat_pintar = '';
		var pat_ocultar = '';
		var primera_vez = 'si';
		
		for (var x in array_visibles)
		{
			temporal = array_visibles[x];
			if(temporal < minimo_visible || primera_vez == 'si')
			{
				minimo_visible=temporal;
				nom_min = x;
			}
			if(temporal > maximo_visible || primera_vez == 'si')
			{
				maximo_visible = temporal;
				nom_max = x;
			}
			primera_vez = 'no';
		}
		
		if(mover == 'adelante')
		{
			pat_pintar = array_todos[((maximo_visible+1)*1)];
			pat_ocultar = array_todos[minimo_visible];
			
			if(pat_pintar != undefined)
			{
				$('td[rel='+pat_pintar+']').show();
				$('td[rel='+pat_ocultar+']').hide();
				maximo_visible = maximo_visible+1;
				minimo_visible = minimo_visible+1;
				$('#hidden_minimo').html('<input type="hidden" id="minimo_visible" name="minimo_visible" value="'+minimo_visible+'">');
				
			}
		}
		else
		{
			if(mover == 'atras')
			{
				pat_pintar = array_todos[((minimo_visible-1)*1)];
				pat_ocultar = array_todos[maximo_visible];
				
				if(pat_pintar != undefined)
				{
					$('td[rel='+pat_pintar+']').show();
					$('td[rel='+pat_ocultar+']').hide();
					maximo_visible = maximo_visible-1;
					minimo_visible = minimo_visible-1;
					$('#hidden_minimo').html('<input type="hidden" id="minimo_visible" name="minimo_visible" value="'+minimo_visible+'">');
				}
			}
		}
		
		if(minimo_visible > 0)
		{
			$('#atras').html('<blink><</blink>');
		}
		else
		{
			$('#atras').html('<');
		}
		
		var longitud = array_todos.length;
		if(maximo_visible < ((longitud*1)-1))
		{
			$('#adelante').html('<blink>></blink>');
		}
		else
		{
			$('#adelante').html('>');
		}
	}
	//-------------------------------
	//	Al cargar la pagina
	//-------------------------------
    $(document).ready(function()
		{		
			inicializarJquery();
			mostrar_atras_adelante ('');
		}
	);
</script>
<style type="text/css">
	#tooltip{color: #2A5DB0;font-family: Arial,Helvetica,sans-serif;position:absolute;z-index:3000;border:1px solid #2A5DB0;background-color:#FFFFFF;padding:5px;opacity:1;}
	#tooltip h3, #tooltip div{margin:0; width:auto}
	.parrafo_text{
			background-color: #666666;
			color: #FFFFFF;
			font-family: verdana;
			font-size: 10pt;
			font-weight: bold;
		}
</style>	
<?php
  /***********************************************
   *      MONITOR SERVICIO DE ALIMENTACION       *         *
   ***********************************************/
	
if(!isset($_SESSION['user']))
	echo "error";
else
{	            
  include_once("root/magenta.php");
  include_once("root/comun.php");
  include_once("movhos/movhos.inc.php");
  $conex = obtenerConexionBD("matrix");
  global $array_alertas;
  $array_alertas = array();
  
  $wactualiz="(Diciembre 12 de 2012)";                      
            
//=========================================================================================================================================\\
//                  MONITOR SERVICIO DE ALIMENTACION 
//=========================================================================================================================================\\
//DESCRIPCION:Con este programa se monitorea todos los Pedidos, Modificacion y Cancelaciones de los servicios que de 
// alimentacion se requieran desde el servicio de hospitalizacion de la clinica.
//AUTOR: Jerson Trujillo 
//=========================================================================================================================================\\
//                  ACTUALIZACIONES                                                                                                                          \\
//=========================================================================================================================================\\
//Junio 07 de 2012: Se adapto el programa para mostrar primeramente los patrones agrupados por centro de costos, y cuando el usuario
//                  de clik en alguno de ellos visualizar el detalle.                                                                                                                                           
//					Se realizaron cambios en la logica del programa para permitir visualizar el detalle en una ventana modal jquery. 
//					Se grego funcionalidad para el manejo del chat de mensajeria. 
//                  Se agrego funcionalidad para manejar el sistema de alertas.
//                  Jerson Trujillo.					                                                           
//                                        
//=========================================================================================================================================\\
	               
  $wfecha=date("Y-m-d");   
  $whora = (string)date("H:i:s");	                                                           
                                                                                        
  $q = " SELECT empdes "
      ."   FROM root_000050 "
      ."  WHERE empcod = '".$wemp_pmla."'"
      ."    AND empest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $row = mysql_fetch_array($res); 
  
  $wnominst=$row[0];
  
  $wtabcco = consultarAliasPorAplicacion($conex, $wemp_pmla, 'tabcco');
  $wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
  $whce = consultarAliasPorAplicacion($conex, $wemp_pmla, 'HCE');
  $wafinidad = consultarAliasPorAplicacion($conex, $wemp_pmla, 'afinidad');
  $wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cenmez');   
  

	//=====================================================================================================================================================================     
	// F U N C I O N E S
	//=====================================================================================================================================================================
      
	function query_principal($wcco, $wser)
    {
		global $wfec_i;
		global $wbasedato;
		global $conex;
		global $wemp_pmla;
		
		$q= " SELECT dieord, movcco, movser, movhis, moving, movdie, movpam, movcan, movhab, movpco, movest, movobs, movint, movpqu, movimp, diecod, diedes, Cconom, pacno1, pacno2, "
			." pacap1, pacap2, pacced, pactid, pacnac, ubihan, ubiptr, ubihac, ubisac, ubisan, ingtip, ROUND(TIMESTAMPDIFF(HOUR,".$wbasedato."_000016.fecha_data,now())/24,0) as dias "
			." FROM ".$wbasedato."_000077, ".$wbasedato."_000041, ".$wbasedato."_000011, root_000036, root_000037, ".$wbasedato."_000018, ".$wbasedato."_000016  "
			." WHERE movfec = '".$wfec_i."'"
			." AND movcco LIKE '%".trim($wcco)."%' "
			." AND movser = '".$wser."' "
			." AND movpco = diecod "
			." AND movcco = Ccocod"
			." AND movhis = orihis"
			." AND moving = oriing"
			." AND oriori = '".$wemp_pmla."'"
			." AND oriced = pacced "
			." AND oritid = pactid"
			." AND movhis = ubihis "
			." AND moving = ubiing "
			." AND movhis  = inghis "
			." AND moving  = inging "
			." UNION "	//Este union es para mostrar los pacientes que tiene observaciones e intolerancias pero que no tienen dietas programadas 
			." SELECT '', movcco, movser, movhis, moving, movdie, movpam, movcan, movhab, movpco, movest, movobs, movint, '','','', '', '', '', '', "
			." '', '', '', '', '', '', '', '', '', '', '', '' "
			." FROM ".$wbasedato."_000077 "
			." WHERE movfec = '".$wfec_i."'"
			." AND movcco LIKE '%".trim($wcco)."%' "
			." AND movser = '".$wser."' "
			." AND movdie = '' "
			." ORDER BY dieord, movcco, movser "
			."";
		
        $resultado = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        return $resultado;
    }
	  
	//FUNCION DE ALERTAS
	function guardar_alertas($cco, $accion, $leido, $whis, &$main_pacientes, $id)
	{
		global $array_alertas;
		
		if ($leido =='0000-00-00' || $leido==NULL)// si no se ha leido
		{
			if(!isset($array_alertas[$cco][$accion]))
				$array_alertas[$cco][$accion]=1; 	//este array me permite almacenar el numero de las alertas sin leer
			else
				$array_alertas[$cco][$accion]++;
			
			if (!isset($main_pacientes[$whis]['alertas']))
				$main_pacientes[$whis]['alertas']=$id; //este nuevo campo (alertas) del array principal ($main_pacientes), es para saber a cuales historias hacerles el blink cuando se abra el detalle
		}		
	}
	//FIN
  
  //Funcion que maneja el sistema de mensajeria (chat)
	function mensajeria($id, $wcco)
	{
		echo "<INPUT type='hidden' id='mensajeriaPrograma' value='cpa'>";
		 
		echo "<table style='width:80%;font-size:10pt' align='center'>";
		echo "<tr><td class='encabezadotabla' align='center' colspan='3'>Mensajer&iacute;a Dietas</td></tr>";
		echo "<tr>";
		//Area para escribir
		echo "<td style='width:45%;' rowspan='2'>";
		// echo "<textarea id='mensajeriaKardex' onKeyPress='return validarEntradaAlfabetica(event);' style='width:100%;height:80px'></textarea>";
		echo "<textarea id='mensajeriaKardex".$id."' style='width:100%;height:80px'></textarea>";
		echo "</td>";
		//Boton Enviar mensaje
		echo "<td align='center' style='width:10%'>";
		echo "<input type='button' onClick='enviandoMensaje(".$id.",".$wcco.")' value='Enviar' style='width:100px'>";
		echo "</td>";
		//Mensajes
		echo "<td style='width:45%' rowspan='2'>";
		echo "<div id='historicoMensajeria".$id."' style='overflow:auto;font-size:10pt;height:80px'>";
		echo "</div>";
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align='center'><b>Mensajes sin leer: </b><div id='sinLeer".$id."'></div></td>";
		echo "</tr>";
		echo "</table>";
	}
	
	function resumenXproductos($cco_detalle, $pk_wccocod, $filtrar, $pk_nom_patron )
	{
		if (isset($cco_detalle))
		{	
			echo "<td align=center>";//$cco_detalle[$wccocod][$patron_principal][$nombre_producto]
				foreach ($cco_detalle  as $clave_nom_cco => $array_patro_pro)
				{
					if($clave_nom_cco==$pk_wccocod)
					{
						$ya_pinto_tabla='no';
						$wclass4="fila1";
						foreach ($array_patro_pro as $nom_patro_pro => $array_productos)
						{
							$pintar_resumen='no';
							if ($filtrar=='no')
								$pintar_resumen='si';
							elseif($nom_patro_pro==$pk_nom_patron)
								$pintar_resumen='si';
							
							if ($pintar_resumen=='si')
							{
								if($ya_pinto_tabla=='no')
								{
									echo "<table >";
									echo "<tr align=center class='encabezadoTabla'><td colspan=2>Resumen x Productos</td></tr>";
									echo "<tr align=center class='encabezadoTabla'><td>Nombre</td><td>Cantidad</td></tr>";
								}
								$ya_pinto_tabla='si';
								foreach ($array_productos as $nom_producto => $cantidad_prod)
								{
									if ($wclass4=="fila1")
										$wclass4="fila2";
									else
										$wclass4="fila1";
									echo '<tr class="'.$wclass4.'" align=center><td>'.$nom_producto.'</td>';
									echo '<td>'.$cantidad_prod.'</td></tr>';
								}
							}
						}
						if($ya_pinto_tabla=='si')
						{
							echo "</table><br>";
						}	
					}
				}
			echo "</td>";
		}
	}
	
	function convenciones()
	{
		echo "
		<table  Width=100% height=100% border=1 align=center class=fila1>        
			<caption class=fila2><b>CONVENCIONES</b></caption>
			<tr><td align=center class='Fila1'><font size=1><b>&nbsp PEDIDO</b></font></td><td align=center class='Fila2'><font size=1><b>&nbsp PEDIDO</b></font></td><td align=center bgcolor='E9C2A6'><font size=1><b>P O S</b></font></td><td align=center bgcolor='#FA5858'><font size=1><b>MEDIA <BR> PORCIÓN</b></font></td></tr>       
			<tr><td align=center bgcolor='FF7F00'><font size=1><b>SERVICIO INDIVIDUAL</b></font></td><td align=center bgcolor='3299CC'><font size=1><b>TRASLADADO</b></font></td><td align=center bgcolor='FFFF99' colspan=2><font size=1><b>&nbsp EN PROCESO DE ALTA</b></font></td></tr> 
			<tr><td align=center bgcolor='FFCC00'><font size=1><b>CANCELADO</b></font></td><td bgcolor='007FFF' align=center><font size=1><b>MODIFICADO</b></font></td><td bgcolor='70DB93' align=center colspan=2 WIDTH=40%><font size=1><b>&nbsp A D I C I O N</b></font></td></tr>
		</table>
		";
	}
	
  
    //funcion que pinta el detalle de los pacientes, en una ventana emergente en jquery
    function mostrar_detalle($nom_cco, $pk_nom_patron='', $valor2='', $arr_patrones, $main_pacientes, $filtrar, $pk_wccocod, $increment , $id_alertas, $wser_pac, $orden_habitaciones, $wfec_i, $cco_detalle)
	{
		global $whora;
		if (isset($cco_detalle))
				$colspan=12;
			else
				$colspan=13;
				
		echo "<table  Width=100%>";
		  echo "<tr class='fondoamarillo'>";
		  echo "<td align=center colspan='".$colspan."' ><br><font size=3><b>CENTRO DE COSTOS:  </b>".$nom_cco."<br><b>SERVICIO:  </b>".$wser_pac."</font>";
		  //chat
		  
			echo "<table width=80% >";
			echo "<tr class='fondoamarillo'><td align=left colspan='".$colspan."'><font size=2 text color=#CC0000><b>Hora: ".$whora."</b></font></td></tr>";
			echo "</table>";
			$id=$increment.$pk_wccocod;
			mensajeria($id, $pk_wccocod);
			echo "<script>
			arCco[ arCco.length ] = $id;
			</script>";  
			echo "<br><input type='HIDDEN' id='centro_costos".$id."' name='centro_costos' value='".trim($pk_wccocod)."'>";
		  //fin chat
		  if($filtrar=='si')  
			echo"<font size=2><b>PATRON </b>: ".$pk_nom_patron."-".$valor2."</font><br></td>";
		  else
			{
				echo "</td>";
			}
			
		  resumenXproductos($cco_detalle, $pk_wccocod, $filtrar, $pk_nom_patron );
		  
		  //=============================================================================================================
		  // C U A D R O   D E   C O N V E N C I O N E S
		  //=============================================================================================================
		  echo "<td colspan=3>";
				convenciones();
		  echo "</td>";
		  //=============================================================================================================
		  //echo "</tr>";
		  //echo "<tr class='fondoamarillo'>";
		  echo "</tr>";
		  echo "<tr class='encabezadoTabla'>";
		  echo "<th>Habitacion</th>";
		  echo "<th>Días</th>";
		  echo "<th>Edad</th>";
		  echo "<th>Historia</th>";
		  echo "<th>Paciente</th>";
		  echo "<th>Patrónes<br>Pedidos</th>";
		  echo "<th>Patrón<br>Principal</th>";
		  echo "<th>Cant.</th>";
		  echo "<th>Detalle</th>";
		  echo "<th>Estado</th>";
		  echo "<th>Afinidad</th>";
		  echo "<th>Diagnostico</th>";
		  echo "<th>Observaciones de <br>la estancia</th>";
		  echo "<th>Traslados</th>";
		  echo "<th>Intolerancias</th>";
		  echo "<th>Hora<br>Solicitud</th>";
		  echo "</tr>";
		  $wclass2="fila1";
		
		//este primer foreach recorre el array de habitaciones ordenadas, lo utilizo solo con el fin de que los registros me salgan ordenados por habitacion
		//si el orden de las habitaciones no interesa, se puede quitar este foreach y no interfiere en el procedimiento 	
		foreach ($orden_habitaciones as $historia_orden => $clave_ord) 
		{
			foreach($arr_patrones as $main_nom_patr => $array_historias )
			{
				//si filtrar es igual a 'no', indica que quieren ver el detalle de todo el centro de costos 
				//osea que dieron click en el nombre del centro del costos, lo que implica que no se filtrara por patron
				$pintar='no';
				if($filtrar=='no')
					$pintar='si';
				else
				{
					if ($main_nom_patr==$pk_nom_patron)
						$pintar='si';
				}
			  
				if($pintar=='si')
				{
					foreach ($array_historias as $historia_pac => $valor)
					{
						if ($historia_pac==$historia_orden) // si esta historia corresponde a la historia del primer foreach; esto es para mostrar los registros ordenados por habitacion
						{
							if($filtrar=='no')  
							{
								if(isset($paci_ya_pintados) && array_key_exists($historia_pac, $paci_ya_pintados))// esto es para no pintar pacientes repetidos
								continue;
							}
						
							$paci_ya_pintados[$historia_pac]='';
							if ($wclass2=="fila1")
								$wclass2="fila2";
							else
								$wclass2="fila1";
							//-----------------------------------------------------------------------------
							//Construir array para mostrar al final las observaciones y la intolerancias.
							//-----------------------------------------------------------------------------
							$habitacion	=	$main_pacientes[$historia_pac]['whab'];
							$color_habi	=	$main_pacientes[$historia_pac]['wcolor'];
							$observacio	=	$main_pacientes[$historia_pac]['observ_textarea'];
							$intoleranc	=	$main_pacientes[$historia_pac]['histor_intole'];
							
							$observa_intoleran[$habitacion]	=	$color_habi."|".$observacio."|".$intoleranc;
							//---------------------------
							//Fin array observaciones
							//---------------------------
							
							if($main_pacientes[$historia_pac]['rowdie']!=null && $main_pacientes[$historia_pac]['rowdie']!='') // si el patron esta en null no lo muestro 
							{
								echo "<tr align=center class='".$wclass2."'>";
									echo "<td bgcolor=".$main_pacientes[$historia_pac]['wcolor'].">".$main_pacientes[$historia_pac]['whab']."</td>";          //**HABITACION
									echo "<td>".$main_pacientes[$historia_pac]['dias']."</td>"; 
									echo "<td>".htmlentities($main_pacientes[$historia_pac]['wedad'])."</td>";                                                //**Edad
									echo "<td>".$main_pacientes[$historia_pac]['whis-wing']."</td>";                                                          //** Historia
									echo "<td align='left'>".$main_pacientes[$historia_pac]['wpac']."</td>";                                                               //** Paciente
									echo "<td>".$main_pacientes[$historia_pac]['rowdie']."</td>";                                                             //** Patrones
									echo "<td>".$main_pacientes[$historia_pac]['pat_prin']."</td>";                                                             //** Patrone principal
									if ($main_pacientes[$historia_pac]['cant_ped'] == "0.5") 
										$color_can = "#FA5858";
									else
										$color_can = "";
									echo "<td bgcolor='".$color_can."'>".$main_pacientes[$historia_pac]['cant_ped']."</td>";                                                           //** cantidad pedida
									echo "<td bgcolor=".$main_pacientes[$historia_pac]['wcolor_det'].">".$main_pacientes[$historia_pac]['wdetalle']."</td>";  //** DETALLE 
								 
								if ($main_pacientes[$historia_pac]['alertas']) //si tiene alertas le hago blink 
								{
									echo "<td bgcolor=".$main_pacientes[$historia_pac]['color_estado'].">";
										echo "<div><blink>";
											echo $main_pacientes[$historia_pac]['estado'];					//** Acciones
											if ($main_pacientes[$historia_pac]['estado']=='MODIFICO PEDIDO' || $main_pacientes[$historia_pac]['estado']=='MODIFICO ADICION')
											{
												echo '<br><b>Patrones Anteriores:</b><br>'.$main_pacientes[$historia_pac]['patr_anter'];		//patrones anteriores
											}
										echo "</blink></div>";
									echo "</td>";  
									$id_alertas=$id_alertas.$main_pacientes[$historia_pac]['alertas'].'|'; //variable que se enviara por ajax para actualizar el log de auditoria como alertas ya leias
								}
								else
									echo "<td bgcolor=".$main_pacientes[$historia_pac]['color_estado'].">".$main_pacientes[$historia_pac]['estado']."</td>";  //** Acciones
									
								echo "<td align=center><font color=".$main_pacientes[$historia_pac]['color_afin']."><b>".$main_pacientes[$historia_pac]['wtpa']."<b></font></td>"; //**Afinidad
								//inicio consultar el diagnostico del paciente segun el kardex 
								$wing=$main_pacientes[$historia_pac]['wing'];
								$historia_consultar=$main_pacientes[$historia_pac]['whistoria'];
								$wdiag=traer_diagnostico($historia_consultar, $wing, $wfec_i);
								   if ($wdiag=="Sin Diagnostico")    //Si no lo encontro en el Kardex actual, lo busco en el Kardex de día anterior
									{
										$dia = time()-(1*24*60*60);   //Resta un dia (2*24*60*60) Resta dos y //asi...
										$wayer = date('Y-m-d', $dia); //Formatea dia
										   
										$wdiag=traer_diagnostico($historia_consultar, $wing, $wayer);
									} 
								echo "<td><TEXTAREA rows=2 cols=30 READONLY>".$wdiag."</TEXTAREA></td>";       											 //** Diagnostico
								//fin diagnostico
								echo "<td><TEXTAREA rows=2 cols=30 READONLY>".$main_pacientes[$historia_pac]['observ_textarea']."</TEXTAREA></td>";        //** Observaciones
								if ($main_pacientes[$historia_pac]['alertas']) //si tiene alertas le hago blink 
								{
									echo "<td><blink><b>".$main_pacientes[$historia_pac]['wmensaje']."</b></blink></td>";                                      //**Traslados 
								}
								else
								{
									echo "<td><b>".$main_pacientes[$historia_pac]['wmensaje']."</b></td>";                                      //**Traslados 
								}
								echo "<td>".$main_pacientes[$historia_pac]['histor_intole']."</td>";                                                       //** Intolerancias
								echo "<td>".@$main_pacientes[$historia_pac]['hora_solicit']."</td>";                                                       //**Hora de Ultima Accion
								echo "</tr>";
							}
						}	
					  //if($filtrar=='si')
					  //break;
					}
				}
			}
		}	
		echo "</table><br>";
		
		//===========================================================    
        //     PINTAR INTOLERANCIAS Y OBSERVACIONES
        //===========================================================  
		$wclass4="fila1";
		echo "<br><br>";
		echo "<table Width=40%>";
			echo "<tr class='encabezadoTabla'>";
				echo "<th colspan='3'><FONT SIZE=3>Observaciones e Intolerancias<FONT></th>";
			echo "</tr>";
			echo "<tr class='encabezadoTabla'>";
					echo "<td align=center >Habitación</th>";
					echo "<td align=center >Observación</th>";
					echo "<td align=center >Intolerancia</th>";
			echo "</tr>";
		//$observa_intoleran[$habitacion]	=	$color_habi."|".$observacio."|".$intoleranc;
		foreach($observa_intoleran as $hab => $valores)	
		{	
			if ($wclass4=="fila1")
				$wclass4="fila2";
			else
				$wclass4="fila1";
			
			$valores = explode("|", $valores); // Explicacion: $valores[0] = Color de habitacion; $valores[1] = Observacion; $valores[2] = Intolerancia; 
			if ( ($valores[1] !=null && $valores[1] !=' ' && $valores[1] !='.') || ($valores[2] !=null && $valores[2] !=' ' && $valores[2] !='.') )
			{
				echo "<tr  class='".$wclass4."'>";
					echo "<td align='center' Width=20% bgcolor=".$valores[0].">".$hab."</td>";          							//**HABITACION
					echo "<td align='center' Width=30%><TEXTAREA rows=2 cols=28 READONLY>".$valores[1]."</TEXTAREA></td>";       	//** Observaciones
					echo "<td align='center' Width=30%>".$valores[2]."</td>";                                                      	//** Intolerancias
				echo "</tr>";
			}
		}
		echo "</table>";
		unset ($observa_intoleran);
		//----------------------------------
		//FIN INTOLERANCIAS Y OBSERVACIONES
		//----------------------------------
		echo "<br><INPUT TYPE='button' value='Cerrar' onClick=' $.unblockUI(); reactivar(\"".$id_alertas."\");' style='width:100'><br><br>";
	}
	
	
	// Esta funcion es para mostrar el detalle pero de los productos individuales.
	// La parte grafica es igual a la funcion mostrar_detalle pero su funcionalidad es muy diferente,
	// Es por eso que se decide hacer esta funcion aparte, para no aumentar la complejidad de la primera.
	function mostrar_detalle_productos($productos_pacientes,$clave_nombre, $main_pacientes, $wser_pac)
	{
		global $whora;
		echo "<table  Width=100%>";
		echo "<tr class='fondoamarillo'>";
		echo "<td align=center colspan=13 ><br><font size=3><b>PRODUCTO:  </b>". strtoupper($clave_nombre)."<b><br>SERVICIO:  </b>".$wser_pac."</font></td>";
		//=============================================================================================================
		// C U A D R O   D E   C O N V E N C I O N E S
		//=============================================================================================================
		  echo "<td colspan=3>";        
			convenciones();
		  echo "</td>";
		  //=============================================================================================================
		  echo "</tr>";
		  echo "<tr class='fondoamarillo'>";
		  echo "<tr>";
		  echo "<tr class='encabezadoTabla'>";
		  echo "<th>Habitacion</th>";
		  echo "<th>Días</th>";
		  echo "<th>Edad</th>";
		  echo "<th>Historia</th>";
		  echo "<th>Paciente</th>";
		  echo "<th>Patrónes<br>Pedidos</th>";
		  echo "<th>Patrón<br>Principal</th>";
		  echo "<th>Cant.</th>";
		  echo "<th>Detalle</th>";
		  echo "<th>Estado</th>";
		  echo "<th>Afinidad</th>";
		  echo "<th>Diagnostico</th>";
		  echo "<th>Observaciones de <br>la estancia</th>";
		  echo "<th>Traslados</th>";
		  echo "<th>Intolerancias</th>";
		  echo "<th>Hora<br>Solicitud</th>";
		  echo "</tr>";
		  $wclass2="fila1";
		  
		foreach ($productos_pacientes as $key_producto => $array_productos) 
		{
			if ($key_producto==$clave_nombre)
			{
				foreach($array_productos as $historia_pac => $key)
				{
					if($wclass2=="fila2")
						$wclass2="fila1";
					else
						$wclass2="fila2";
					echo "<tr align=center class='".$wclass2."'>";
					echo "<td bgcolor=".$main_pacientes[$historia_pac]['wcolor'].">".$main_pacientes[$historia_pac]['whab']."</td>";          						//**Habitacion
					echo "<td>".$main_pacientes[$historia_pac]['dias']."</td>"; 																					//**Dias
					echo "<td>".htmlentities($main_pacientes[$historia_pac]['wedad'])."</td>";                                               						//**Edad
					echo "<td>".$main_pacientes[$historia_pac]['whis-wing']."</td>";                                                          						//**Historia
					echo "<td align='left'>".$main_pacientes[$historia_pac]['wpac']."</td>";                                                               						//**Paciente
					echo "<td>".$main_pacientes[$historia_pac]['rowdie']."</td>";                                                             						//**Patrones
					echo "<td>".$main_pacientes[$historia_pac]['pat_prin']."</td>";                                                             					//**Patrone principal
					echo "<td>".$main_pacientes[$historia_pac]['cant_ped']."</td>";                                                           						//**cantidad pedida
					echo "<td bgcolor=".$main_pacientes[$historia_pac]['wcolor_det'].">".$main_pacientes[$historia_pac]['wdetalle']."</td>";  						//**Detalle 
					echo "<td bgcolor=".$main_pacientes[$historia_pac]['color_estado'].">".$main_pacientes[$historia_pac]['estado'];								//**Acciones
						if ($main_pacientes[$historia_pac]['estado']=='MODIFICO PEDIDO' || $main_pacientes[$historia_pac]['estado']=='MODIFICO ADICION')
						{
							echo '<br><b>Patrones Anteriores:</b><br>'.$main_pacientes[$historia_pac]['patr_anter'];												//**Patrones anteriores
						}
					echo "</td>";
					echo "<td align=center><font color=".$main_pacientes[$historia_pac]['color_afin']."><b>".$main_pacientes[$historia_pac]['wtpa']."<b></font></td>";//**Afinidad
					//inicio consultar el diagnostico del paciente segun el kardex 
						$wing=$main_pacientes[$historia_pac]['wing'];
						$historia_consultar=$main_pacientes[$historia_pac]['whistoria'];
						$wdiag=traer_diagnostico($historia_consultar, $wing, $wfec_i);
						   if ($wdiag=="Sin Diagnostico")    //Si no lo encontro en el Kardex actual, lo busco en el Kardex de día anterior
							  {
							   $dia = time()-(1*24*60*60);   //Resta un dia (2*24*60*60) Resta dos y //asi...
							   $wayer = date('Y-m-d', $dia); //Formatea dia
							   
							   $wdiag=traer_diagnostico($historia_consultar, $wing, $wayer);
							  } 
					echo "<td><TEXTAREA rows=2 cols=30 READONLY>".$wdiag."</TEXTAREA></td>";       											 						//**Diagnostico
					//fin diagnostico
					echo "<td><TEXTAREA rows=2 cols=30 READONLY>".$main_pacientes[$historia_pac]['observ_textarea']."</TEXTAREA></td>";        						//**Observaciones
					echo "<td><b>".$main_pacientes[$historia_pac]['wmensaje']."</b></td>";                                      									//**Traslados 
					echo "<td>".$main_pacientes[$historia_pac]['histor_intole']."</td>";                                                       						//** Intolerancias
					echo "<td>".@$main_pacientes[$historia_pac]['hora_solicit']."</td>";                                                       						//**Hora de Ultima Accion
					
					echo "</tr>";
				}
			}		
		}	
		echo "</table><br>";
	}
  
	//FUNCION QUE ME RETORNA EL NOMBRE DE UN PATRON
	function nombre_patron($valor_patr)
    {
        global $wbasedato;
        global $conex;
        $query_nom_pat="SELECT Diedes, Dieord 
						  FROM ".$wbasedato."_000041 
						 WHERE Diecod='".$valor_patr."' 
						";
        $resnp = mysql_query($query_nom_pat, $conex) or die ("Error: ".mysql_errno()." - en el query: ".$query_nom_pat." - ".mysql_error());
        $rownp=mysql_fetch_array($resnp);
        return $rownp; 
    }
   
	//Conocer si el paciente esta en proceso de alta o traslado
	function estado_del_paciente($whis,$wing,&$walta,&$wtraslado)
    {
		global $wbasedato;
		global $conex;
	  
		$walta="off";
		$wtraslado="off";
		//En proceso de alta
		$q= " SELECT COUNT(*) 
				FROM ".$wbasedato."_000018 
			   WHERE ubihis  = '".$whis."'
				 AND ubiing  = '".$wing."'
				 AND ubialp  = 'on' 
				 AND ubiald != 'on' ";
		$res = mysql_query($q, $conex) or die("ERROR EN QUERY");
		$wnum = mysql_fetch_array($res); 
      
		if ($wnum[0] > 0)         //Si es mayor a cero es porque esta en proceso de alta
		{
			$walta="on";
        }
         
		//En proceso de traslado
		$q= " SELECT COUNT(*) 
				FROM ".$wbasedato."_000018 
			   WHERE ubihis  = '".$whis."'
			     AND ubiing  = '".$wing."'
				 AND ubiptr  = 'on' 
				 AND ubiald != 'on' 
			";
		$res = mysql_query($q, $conex) or die("ERROR EN QUERY");
		$wnum = mysql_fetch_array($res); 
      
		if ($wnum[0] > 0)         //Si es mayor a cero es porque esta en proceso de alta
		{
			$wtraslado="on";
		}        
	}
	//----------------------------------------
	//	Funciones para calcular edad
	//----------------------------------------
	function tiempo_transcurrido($fecha_nacimiento, $fecha_control)
	{
		$fecha_actual = $fecha_control;
	   
		if(!strlen($fecha_actual))
		{
			$fecha_actual = date('d/m/Y');
		}

		// separamos en partes las fechas 
		$array_nacimiento = explode ( "/", $fecha_nacimiento ); 
	    $array_actual = explode ( "/", $fecha_actual ); 

	    $anos =  $array_actual[2] - $array_nacimiento[2]; // calculamos años 
	    $meses = $array_actual[1] - $array_nacimiento[1]; // calculamos meses 
	    $dias =  $array_actual[0] - $array_nacimiento[0]; // calculamos días 

	    //ajuste de posible negativo en $días 
	    if ($dias < 0) 
	    { 
		    --$meses;

		    //ahora hay que sumar a $dias los dias que tiene el mes anterior de la fecha actual 
		    switch ($array_actual[1]) 
			{ 
				case 1: 
					$dias_mes_anterior=31;
					break; 
				case 2:     
					$dias_mes_anterior=31;
					break; 
				case 3:  
					if (bisiesto($array_actual[2])) 
					{ 
						$dias_mes_anterior=29;
						break; 
					} 
					else 
					{ 
						$dias_mes_anterior=28;
						break; 
					} 
				case 4:
					$dias_mes_anterior=31;
					break; 
				case 5:
					$dias_mes_anterior=30;
					break; 
				case 6:
					$dias_mes_anterior=31;
					break; 
				case 7:
					$dias_mes_anterior=30;
					break; 
				case 8:
					$dias_mes_anterior=31;
					break; 
				case 9:
					$dias_mes_anterior=31;
					break; 
				case 10:
					$dias_mes_anterior=30;
					break; 
				case 11:
					$dias_mes_anterior=31;
					break; 
				case 12:
					$dias_mes_anterior=30;
				break; 
			}	 

		$dias=$dias + $dias_mes_anterior;

			if ($dias < 0)
			{
				--$meses;
				if($dias == -1)
				{
					$dias = 30;
				}
				if($dias == -2)
				{
					$dias = 29;
				}
			}
		}

		//ajuste de posible negativo en $meses 
		if ($meses < 0) 
		{ 
			--$anos; 
			$meses=$meses + 12; 
		}

		$tiempo[0] = $anos;
		$tiempo[1] = $meses;
		$tiempo[2] = $dias;

		return $tiempo;
	}

	function bisiesto($anio_actual)
	{ 
		$bisiesto=false; 
		//probamos si el mes de febrero del año actual tiene 29 días 
		if (checkdate(2,29,$anio_actual)) 
		{ 
			$bisiesto=true; 
		} 
		return $bisiesto; 
	}
	//----------------------------------------
	//	Fin calcular edad
	//----------------------------------------
	
	//================================================================ 
	//    FIN FUNCIONES
	//================================================================
	//================================================================ 
	//    ENCABEZADO
	//================================================================
		encabezado("Monitor Servicio de Alimentación", $wactualiz, 'clinica');

	
	//================================================================
	//	FORMA 
	//================================================================
	echo "<form name='mondietas' action='' method=post>";
	echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";     
	if (strpos($user,"-") > 0)
		$wusuario = substr($user,(strpos($user,"-")+1),strlen($user)); 
	 
	//======================================================================================================================================
	//	ACA COMIENZA EL MAIN DEL PROGRAMA   
	//======================================================================================================================================
	  
		//=================================
		// SELECCIONAR CENTRO DE COSTOS
		//=================================
		echo "<center><table>";
		echo "<tr class=titulo>";
		//Traigo los centros de costos
		$q = " SELECT ".$wtabcco.".ccocod, ".$wtabcco.".cconom 
				FROM ".$wtabcco.", ".$wbasedato."_000011
			   WHERE ".$wtabcco.".ccocod = ".$wbasedato."_000011.ccocod 
			     AND ccohos  = 'on' ";
		  
		$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$num = mysql_num_rows($res);
		echo "</tr>";
		echo "<tr class=seccion1>";
		echo "<td align=center colspan=5><b>SELECCIONE EL CENTRO DE COSTOS: </b></td>";
		echo "<td align=center colspan=5><SELECT name='wcco' id='wcco' >";
		if (isset($wcco))
	    {
			echo "<OPTION SELECTED>".$wcco."</OPTION>";
        } 
		echo "<option>% - TODOS</option>";
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res); 
			echo "<OPTION>".$row[0]." - ".$row[1]."</OPTION>";
	    }
		echo "</SELECT></td>";
		//=================================
		// SELECCIONAR EL SERVICIO
		//=================================
		if (isset($wser))
		{
			$q1 = " SELECT sernom 
					  FROM ".$wbasedato."_000076 
					 WHERE sercod = '".$wser."' ";
			$resser1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());
			$nom_ser_selec = mysql_fetch_array($resser1);
		}
		//Consultar los servicios del maestro
		$q = " SELECT sernom, serhin, serhfi, sercod 
				 FROM ".$wbasedato."_000076 
				WHERE serest = 'on' ";
		$resser = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		$numser = mysql_num_rows($resser);
      
		echo "<tr class='seccion1'>";
		echo "<td align=center colspan=5><b>SELECCIONE EL SERVICIO DE ALIMENTACION: </b></td>";
		echo "<td align=center colspan=5><SELECT name='wser' id='wser'>";
		if (isset($wser))
			echo "<OPTION SELECTED value=".$wser.">".$nom_ser_selec[0]."</OPTION>";
	  
		for ($i=1;$i<=$numser;$i++)
		{
			$rowser = mysql_fetch_array($resser); 
			echo "<OPTION value=".$rowser[3].">".$rowser[0]."</OPTION>";
	    } 
		echo "</SELECT></td>";
		echo "</tr>";
		//=================================
		// SELECCIONAR FECHAS A CONSULTAR
		//=================================
		echo "<tr class='seccion1'><td colspan=10 align=center><b>FECHA INICIAL: </b>";  
		//echo "<td colspan=2 align=center >";
		if(isset($wfec_i))
		{
			campoFechaDefecto("wfec_i", $wfec_i);
		}
		else
		{
			campoFechaDefecto("wfec_i", date("Y-m-d"));
		}
		echo "</td>";
		echo "</tr>";
		echo "<tr>";
		echo "<td align=center colspan=10 bgcolor=cccccc><b><input type='submit' value='CONSULTAR'></b></td>";
		echo "</tr>";
		echo "</table><br>";
	  
    //=====================================================================================
	// Aca comienzo a consultar y procesar la informacion para luego mostrarla
	//=====================================================================================
	if (isset($wcco) && isset($wser) && isset($wfec_i) ) // si ya seleccionaron los parametros para consultar
	{
		$wccoaux=explode("-",$wcco);
		$res= query_principal($wccoaux[0], $wser);
		$num = mysql_num_rows($res);
		$wccoant="";
		if ($num > 0)
	    {
			for ($i=0;$i<$num;$i++) //for principal
			{
				$row = mysql_fetch_array($res);
				$hay_resultados='si';
			  
				$whab      = $row['movhab'];   				//Habitacion desde donde se relizo el pedido
				$whis      = $row['movhis'];				//Historia
				$his_hab	 = $row['movhis'].'-'.$row['movhab'].'-'.$row['movpco'];
				$wing      = $row['moving'];				//Ingreso
				$wptr      = $row['ubiptr'];   				//Indica si es un traslado
				$whab_tras = $row['ubihac'];   				//Habitacion de traslado, si hay traslado.
				$wccocod   = trim($row['movcco']);   		//Centro de Costo
				$wcconom   = $row['Cconom'];   				//Nombre del Centro de Costos
				$wtpo      = $row['ingtip'];  				//Indica si el tipo de paciente es POS o No
				$wdias_est = $row['dias'];  				//Dias de estancia      
				$wpac      = $row['pacno1']." ".$row['pacno2']." ".$row['pacap1']." ".$row['pacap2']; //Nombre completo
				$wdpa 	 = $row['pacced'];     				//Cedula del paciente
				$wtid 	 = $row['pactid'];					// Tipo de identificacion
				$wnac 	 = $row['pacnac'];					//Fecha de nacimiento
				$wser_pac  = $row['movser'];				//Servicio
				$pos_quiru = $row['movpqu'];				//Indica si se pidio posqirurgico o no 
				$hab_anter = $row['ubihan'];				//Habitacion anterior
				$cco_actual= $row['ubisac'];				//CCO Actual
				$cco_anter = $row['ubisan'];				//CCO anterior
				$pendi_imp = $row['movimp'];				//Si esta pendiente de impresion

				$nom_cco[$wccocod]=$wcconom; //Arreglo para guardar los nombres de los CC
			  
				//-----------------
				// Calculo la edad
				//-----------------
				$row_fecha_nacimiento = explode('-', $wnac);
				$fecha_nacimiento = $row_fecha_nacimiento[2].'/'.$row_fecha_nacimiento[1].'/'.$row_fecha_nacimiento[0]; // pasar fecha de formato 2012-10-09 a formato 09/10/2012
				$wedad = tiempo_transcurrido($fecha_nacimiento, date('d/m/Y'));
				$wedad = $wedad[0].' Años '.$wedad[1].' Meses';
		      
				//-----------------------------------------------
				// Si esta en proceso de traslado colocar mensaje
				//-----------------------------------------------
				$wmensaje="";
				if ($wptr == "on")// si es un traslado
		        {
					$wmensaje="Paciente que va para la habitación : <b>".$whab_tras."</b>";
		        }
				//$rowdie[4];
				//--------------------------------
				// Consultar si el paciento es POS
				//--------------------------------
				$q = " SELECT COUNT(*) 
						 FROM ".$wbasedato."_000076 
						WHERE Sertpo LIKE '%".$wtpo."%'
						  AND Sercod = '".$wser_pac."'
						  AND Serest = 'on' ";
				$restpo = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$rowtpo=mysql_fetch_array($restpo);
				if ($rowtpo[0] > 0)
					$wcolor="E9C2A6";
				else
					$wcolor="";
				 
				//================================================================================================================
				//    ACA COMIENZO A GUARDAR LOS DATOS EN UN ARRAY PARA PINTARLOS AL FINAL 
				//        $main_pacientes[HISTORIA DEL PACIENTE][NOMBRE DEL CAMPO]= VALOR
				//================================================================================================================
			  
				$main_pacientes[$his_hab]['wcolor']=$wcolor;                     	//** Color
				$main_pacientes[$his_hab]['whab']=$whab;                         	//** Habitacion
				$main_pacientes[$his_hab]['wedad']=$wedad;                       	//** Edad
				$main_pacientes[$his_hab]['whistoria']=$whis;                    	//** Historia
				$main_pacientes[$his_hab]['whis-wing']=$whis.'-'.$wing;          	//** Historia e ingreso    
				$main_pacientes[$his_hab]['wing']=$wing;          			   		//** ingreso    
				$main_pacientes[$his_hab]['wpac']=$wpac;                         	//** Paciente
				$main_pacientes[$his_hab]['dias']=$wdias_est;                    	//** Dias de estancia
				$main_pacientes[$his_hab]['rowdie']=$row['movdie'];              	//** Patrones
				$main_pacientes[$his_hab]['patr_anter']=$row['movpam'];          	//** Patrones Anteriores, en caso de que se hayan realizado modficaciones
				$main_pacientes[$his_hab]['cant_ped']=$row['movcan'];            	//** cantidad pedida
				$main_pacientes[$his_hab]['pat_prin']=$row['movpco'];            	//** patron principal
			  
				$orden_habitaciones[$his_hab]=$whab;								//** aca almaceno todas las habitaciones que se mostraran, para luego ordenarlas  
				$patron_principal=$row['movpco'];									//** patron prinicpal
				$cantidad_pat=$row['movcan'];										//** cantidad pedida 
				$cantidad_pat=$cantidad_pat*1;										//** Para que trabaje como un entero
				$patron_con_productos='';											//** Esta variable me indica si debo consular detalle de productos
				$patrones_pac=$row['movdie']; 										//** Esta variable la utilizo en las alertas para saber si el paciente tiene dietas
				//=================================================================================================================
			  
				/*if (strpos($row['movdie'],",")) // si el paciente tiene mas de un patron programado para el servicio actual
				{
					$wpatron=explode(",",$row['movdie']);
					foreach($wpatron as $valor_patr)	//recorro todos los patrones
					{
						//===============================================================================
						//  En este array guardo los patrones e historias pertenecientes a cada CC
						//  $main_patrones[Centro de costos][Patron][Historia]='este valor no interesa' 
						//================================================================================
							$main_patrones[$wccocod][$valor_patr][$his_hab]=1;     
						//================================================================================
						
						// Si dentro de la cadena de patrones existe alguno que sea de servicio individual le debo consultar el detalle.
						// Por ejemplo si es la combinacion 'L,SI' el patron principal es L pero igual en el monitor debo mostrar el detalle 
						// Porque existe un servicio individual en el pedido
						if (servicio_individual($valor_patr))
							$patron_con_productos=$valor_patr;
						
						
						if($pos_quiru=='on')
							$valor_patr_temp=$patron_principal;
						else
							$valor_patr_temp=$valor_patr;
							
						// 1= $rowdie[6]=='off', indica que el patron ha sido cancelado, y solo debo contabilizar los activos
						// 2= $valor_patr_temp==$patron_principal, de todos los patrones programados solo debo contabilizar el principal que es el mismo que el cobrado (77, Movpco)
						if($row['movest']!='off' && $valor_patr_temp==$patron_principal)            
						{
							$nom_patron=nombre_patron($valor_patr);         	//funcion que retorna el nombre del patron
							$todos_patrones[$valor_patr]=$nom_patron[0];       	//array para conocer todos los patrones existentes en el reporte
							$orden_patrones[$valor_patr]=$nom_patron[1];	   	// Array para almacenar el orden de los patrones en el sistema 
						   
							if(!isset($num_patrones[$wccocod][$valor_patr]))
							{
								$num_patrones[$wccocod][$valor_patr]=$cantidad_pat;       //array para almacenar la cantidad del patron
							}
							else
							{
								$num_patrones[$wccocod][$valor_patr]+=$cantidad_pat;
							}
						}
					}
				}*/
				$valor_patr=$row['movdie'];
				//===============================================================================
				//  En este array guardo los patrones e historias pertenecientes a cada CC
				//  $main_patrones[Centro de costos][Patron][Historia]='este valor no interesa' 
				//================================================================================
					$main_patrones[$wccocod][$valor_patr][$his_hab]=1;     
				//================================================================================
				unset($arr_patrones );
				if (strpos($valor_patr,","))
				{
					$arr_patrones = explode(",",$valor_patr);
					foreach($arr_patrones as $valor_pat)	//recorro todos los patrones
					{
						if($valor_pat !='')
						{	
							if (servicio_individual($valor_pat))
								$patron_con_productos=$valor_pat;
						}
					}
				}
				else
				{
					$arr_patrones[0] = $valor_patr;
					if (servicio_individual($valor_patr))
						$patron_con_productos=$valor_patr;
				}
				
				// 1 = si el patron es null no lo debo contabilizar en la matriz principal y solo lo debo mostrar en la tabla de 'observaciones e intolerancias' 
				// 2= $rowdie[6]=='off', indica que el patron ha sido cancelado, y solo debo contabilizar los patrones activos
				if($valor_patr!='' && $valor_patr!=NULL && $row['movest']!='off')
				{
					$nom_patron = '';
					foreach($arr_patrones as $valor_pat)	//recorro todos los patrones
					{
						if($valor_pat !='')
						{
							$vector_nom_posi = nombre_patron($valor_pat);
							if(count($arr_patrones)>1)
								$pos_patron = 100; //Le pongo 100 para que los que tengan mas de un patron queden de ultimos
							else
								$pos_patron = $vector_nom_posi[1];
								
							if($nom_patron == '')
								$nom_patron = $vector_nom_posi[0];
							else
								$nom_patron.='-'.$vector_nom_posi[0];;
						}
					}
					
					$todos_patrones[$valor_patr]=$nom_patron;                  // Array para conocer todos los patrones existentes en el reporte
					$orden_patrones[$valor_patr]=$pos_patron;				  // Array para almacenar el orden de los patrones en el sistema 
					
					if(!isset($num_patrones[$wccocod][$valor_patr]))
					{
						$num_patrones[$wccocod][$valor_patr]=$cantidad_pat;
					}    
					else
					{
						$num_patrones[$wccocod][$valor_patr]+=$cantidad_pat;
					} 
				}
				
				//--------------------------------------------------------------
				// Consultar el Detalle: 
				// Solo si existe algun servicio individual dentro del pedido
				//--------------------------------------------------------------
				if (isset($patron_con_productos) && $patron_con_productos!='')
				{
					$q = " SELECT Prodes, Detcan "
					  ."   FROM ".$wbasedato."_000084, ".$wbasedato."_000082 "
					  ."  WHERE detfec = '".$wfec_i."'"
					  ."    AND dethis = '".$whis."'"
					  ."    AND deting = '".$wing."'"
					  ."    AND detser = '".$row['movser']."'"
					  ."    AND detpat = '".$patron_con_productos."'";
					  //."	AND detcco = '".$cco_actual."'"
					if ($row['movest']=='on')
					{
					$q.="    AND detest = 'on' ";
					}
					else
					{
					$q.="   AND detest = 'off' 
							AND detcal = 'on'	";
					}
					  $q.="    AND Procod = detpro";
					$rescbi = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$numcbi = mysql_num_rows($rescbi);
					$wdetalle="";
					$wcolor_detalle="";
					
					
					for ($k=1;$k<=$numcbi;$k++)
					{
						$wcolor_detalle="FF7F00";  //Naranja
						$rowcbi=mysql_fetch_array($rescbi);
						$nombre_producto=$rowcbi[0];
						$cantidad=$rowcbi[1];
						
						if (trim($wdetalle) != "")
							$wdetalle=$wdetalle."<br> ".$nombre_producto." ".$cantidad;
						else
							$wdetalle=$nombre_producto." ".$cantidad;
						
						//Este array es para agrupar pacientes por producto	
							$productos_pacientes[$nombre_producto][$his_hab]='';
							
						if ($row['movest']=='on') // Solo muestro los cuadro resumen si el patron no esta cancelado
						{	
							//Este array lo uso para mostrar el cuadro 'resumen x productos' que se pinta dentro del detalle o ventana emergente	
							if (!isset($cco_detalle[$wccocod][$patron_con_productos][$nombre_producto]))
								$cco_detalle[$wccocod][$patron_con_productos][$nombre_producto]=$cantidad;
							else
								$cco_detalle[$wccocod][$patron_con_productos][$nombre_producto]+=$cantidad;	
							
							//Este arreglo es para mostrar el cuadro, RESUMEN POR PRODUCTO DE PATRONES INDIVIDUALES
							if (!isset($resumenXproducto[$nombre_producto]))
							{
								$resumenXproducto[$nombre_producto]=$cantidad;		
							}
							else
							{
								$resumenXproducto[$nombre_producto]+=$cantidad;						
							}
						}
					}
					$main_pacientes[$his_hab]['wcolor_det']=$wcolor_detalle;  
					$main_pacientes[$his_hab]['wdetalle']=$wdetalle;
					//$main_pacientes[$his_hab]['serv_indivi']=$patron_con_productos;            //** patron de servicio individual
				}
				else
				{
					$main_pacientes[$his_hab]['wcolor_det']='';  
					$main_pacientes[$his_hab]['wdetalle']='';
					//$main_pacientes[$his_hab]['serv_indivi']='';
				} 
				//-------------------
				//	Fin detalle
				//-------------------
				//-------------------------------------------------
				//	Si esta pendiente de impresion, generar alerta  
				//-------------------------------------------------
					if($pendi_imp != 'on' && $row['movdie']!='' && $row['movest']=='on')
					{	
						if (isset($array_alertas[$wccocod]['impresiones']))
							$array_alertas[$wccocod]['impresiones']++;
						else
							$array_alertas[$wccocod]['impresiones']=1;
					}
				//----------------------------------------------------------
				// Consultar el estado del patron y gestionar las alertas
				//----------------------------------------------------------
				
				$q = " SELECT hora_data, Audacc, Audfle, MAX(id) as id "
					."  FROM ".$wbasedato."_000078 "
					." WHERE audhis = '".$whis."'"
					."   AND auding = '".$wing."'"
					."   AND audser = '".$wser_pac."'"
					."   AND fecha_data = '".$wfec_i."'"
					." GROUP BY id "
					." ORDER BY id DESC ";
						
				$resaud = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				$numaud = mysql_num_rows($resaud);
				
			  
				if ($numaud > 0)
				{
					$rowaud = mysql_fetch_array($resaud);
					$wcolor="";
				  
					//----------------------------------------------------------------------------------------------------------------------------
					// ! ! !  NOTA ¡ ¡ ¡ = Existen unos casos donde el ultimo movimiento registrado en la tabla de auditoria no es el 
					//					 correspondiente al registro del pedido, por eso utilizo estas excepciones, si el registro 
					//					 cumple con alguna de ellas entonces le consulto su estado real 
					//---------------------------------------------------------------------------------------------------------------------------------------
					// EXCEPCION 1: Si no esta en proceso de traslado && el servicio esta cancelado && el cc anterior es igual al cc de donde hicieron el pedido. 
					// Esto lo hago porque cuando se realiza un traslado el ultimo movimiento en la tabla de auditoria sera un 'pedido' y el pedido del
					// centro de costos antes del traslado debe salir con el estado 'cancelado por traslado' no como 'pedido'
					//---------------------------------------------------------------------------------------------------------------------------------------
					if ($wptr == "off" && $row['movest']=='off' && $cco_anter==$wccocod  ) 
					{
						$q2 = "SELECT Audfle, id, Audacc 
								 FROM ".$wbasedato."_000078 
								WHERE audhis = '".$whis."'
								  AND auding = '".$wing."'
								  AND audser = '".$wser_pac."'
								  AND fecha_data = '".$wfec_i."'
								  AND Audacc = 'CANCELADO POR TRASLADO' 
							";
						
						$resaud2 = mysql_query($q2,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q2." - ".mysql_error());
						$rowaud2 = mysql_fetch_array($resaud2);
						$num_rowaud2 = mysql_num_rows($resaud2);
						if ($num_rowaud2>0)
						{
							$rowaud[1]= $rowaud2[2];	
							$rowaud[2]= $rowaud2[0];
							$rowaud[3]= $rowaud2[1];
						}	  
					}
				  
					//---------------------------------------------------------------------------------------------------------------------------------------
					// EXCEPCION 2: Si el pedido esta cancelado y el ultimo movimiento es una modificacion de intolerancia u observacion;
					// Esto lo hago porque cuando cancelan un pedido y luego insertan una observacion o intolerancia el ultimo registro en la tabla de auditoria 
					// es 'Modifico Observacion' o 'intolerancia', entonces el patron saldra con este estado y no se visualizara el 'Cancelado', 
					// que es lo que realmente se necesita visualizar.
					//---------------------------------------------------------------------------------------------------------------------------------------		  
					if ( $row['movest']=='off' and ($rowaud[1]=='MODIFICO OBSERVACION' or $rowaud[1]=='MODIFICO INTOLERANCIA' or $rowaud[1]=='MODIFICO OBSERVACION Y MODIFICO INTOLERANCIAS')) 
					{
						//Valido que exista un registro de 'cancelado' en la auditoria
						$q3 ="SELECT Audfle, id, Audacc 
								FROM ".$wbasedato."_000078 
							   WHERE audhis = '".$whis."'
								 AND auding = '".$wing."'
								 AND audser = '".$wser_pac."'
								 AND fecha_data = '".$wfec_i."'
								 AND Audacc = 'CANCELADO' ";
						
						$resaud3 = mysql_query($q3,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q3." - ".mysql_error());
						$rowaud3 = mysql_fetch_array($resaud3);
						$num_rowaud3 = mysql_num_rows($resaud3);
						if ($num_rowaud3>0)
						{
							$rowaud[1]	=	$rowaud3[2];	
							$rowaud[2]	=	$rowaud3[0];
							$rowaud[3]	=	$rowaud3[1];
						}
					}
				  
					//Consultar el color del estado 
					$q_color= "	SELECT  Estcol
								  FROM  ".$wbasedato."_000129
								 WHERE  Estdes = '".$rowaud[1]."'
								   AND	Estest = 'on'
							";
					$res_color = mysql_query($q_color,$conex) or die ("Error: ".mysql_errno()." - en el query:(consultar color) ".$q_color." - ".mysql_error());
					$row_color = mysql_fetch_array($res_color);
					$num_color = mysql_num_rows($res_color);
					if ($num_color>0)
					{
						$wcolor=$row_color['Estcol'];
					}
				  
					$maneja_alerta='si';
					if ($rowaud[1]=='ALTA - SERVICIO SIN CANCELAR' || $rowaud[1]=='PROCESO DE ALTA' || $rowaud[1]=='MUERTE - SERVICIO SIN CANCELAR' || $rowaud[1]=='PEDIDO')
						$maneja_alerta='no';
				  
					// Si el estado de la dieta maneja alerta y el paciente tiene dieta programada.
					if ($maneja_alerta=='si' && $patrones_pac!='' && $patrones_pac!=NULL)
					{
						guardar_alertas($wccocod, $wcolor, $rowaud[2], $his_hab, &$main_pacientes, $rowaud[3]);
						if($rowaud[1]=='PEDIDO POR TRASLADO') // Este estado maneja mensaje
						{
							$wmensaje="Habitación Origen : <b>".$hab_anter."</b>";
						}
					}
					
					//Consultar si esta en proceso de alta o de traslado
					estado_del_paciente($whis,$wing,&$walta,&$wtraslado);
					//Indica que esta en proceso de alta
					if ($walta=="on" && $rowaud[1]!='CANCELADO POR TRASLADO')
						$wcolor="FFFF99";
			             
					//Indica que el paciente esta siendo trasladado
					if ($wtraslado=="on")
					{
						$wcolor="3299CC";
						if ($patrones_pac!='' && $patrones_pac!=NULL)
							guardar_alertas($wccocod, $wcolor, '0000-00-00', $his_hab, &$main_pacientes, ' ');
					}
					    
				    //Guardo en el array principal el estado con su correspondiente color
				    $main_pacientes[$his_hab]['color_estado']=$wcolor;  
				    $main_pacientes[$his_hab]['estado']=$rowaud[1];
							
				}    
				else
				{
					$main_pacientes[$his_hab]['color_estado']="";  
					$main_pacientes[$his_hab]['estado']=" "; 
				}
                //Fin estado
				
				//=========================================================
				// Consultar si el paciente es AFIN o no, y de que tipo
				//=========================================================
				$wafin=clienteMagenta($wdpa,$wtid,&$wtpa,&$wcolorpac);
				if ($wafin)
				{ 
					$main_pacientes[$his_hab]['color_afin']=$wcolorpac;
					$main_pacientes[$his_hab]['wtpa']=$wtpa;
				}
				else
				{
					$main_pacientes[$his_hab]['color_afin']="";
					$main_pacientes[$his_hab]['wtpa']="";
				}
				$main_pacientes[$his_hab]['observ_textarea']=$row['movobs'];
				$main_pacientes[$his_hab]['wmensaje']=$wmensaje;
				$main_pacientes[$his_hab]['hora_solicit']=@$rowaud[0]; 
			  
				//======================================================================
				// Intolerancia: Busco si hay alguna en cualquier ingreso del paciente
				//======================================================================	
				$q = " SELECT MAX(CONCAT(fecha_data,hora_data)), movint 
						 FROM ".$wbasedato."_000077 
						WHERE movhis = '".$whis."'
						  AND movint != '' 
						GROUP BY 2 
						ORDER BY 1 DESC 
					";
				$res_mov = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query (Buscar intolerancia): ".$q." - ".mysql_error());
				$rowint=mysql_fetch_array($res_mov);
				
				$main_pacientes[$his_hab]['histor_intole']=$rowint['movint'];    
	            
            }//Fin del for principal
            
            
            //================================================================================================================
            //      PINTAR MATRIZ PRINCIPAL
            //      $main_patrones[$wccocod]            [$valor_patr]   [$whis]     ='';
            //      $main_patrones[Centro de costos]    [Patron]        [Historia]  =''
            //================================================================================================================
           
			if (isset($hay_resultados)&& isset($todos_patrones)) // si hay resultados para pintar
		    {
				@asort($orden_habitaciones);
				 
				//CONSULTAR EL NOMBRE DEL SERVICIO PARA MOSTRARLO EN EL DETALLE
				$q1 = " SELECT sernom 
						  FROM ".$wbasedato."_000076
						 WHERE sercod = '".$wser."' ";
				$resser1 = mysql_query($q1,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q1." - ".mysql_error());
				$nom_servicio = mysql_fetch_array($resser1);
				//FIN COSULTAR
				
				
                echo "<br><center>";
                //========================
                //tabla principal
                //========================
                echo"<table width=95%>"; 
                //========================
                
                echo "<tr class=encabezadoTabla>"; 
					echo "<td colspan='7' align='center' width='21%'><FONT SIZE=3><b>ALERTAS</b></FONT></td>";
                    echo "<td rowspan='2' align='center' width='19%' ><FONT SIZE=3><b>CENTRO DE COSTOS</b></FONT></td>";
                    $colspan=count($todos_patrones); 
                    echo "<td colspan='".(($colspan >= 17) ? 17 : $colspan)."' align='center' width='55%' >
							<table width='100%'>
								<tr>
									<td id='atras' width='5%'  align='center' style='cursor:pointer;Font-size:12pt;' class='parrafo_text' onClick='mostrar_atras_adelante(\"atras\")'><</td>
									<td width='90%' align='center'  style='Font-size:12pt;' class='Encabezadotabla'>RESUMEN POR PATRONES</td>
									<td id='adelante' width='5%' align='center'  style='cursor:pointer;Font-size:12pt;' class='parrafo_text' onClick='mostrar_atras_adelante(\"adelante\")'>></td>
								</tr>
							</table>
						</td>";
                    echo "<td rowspan='2' align='center'  width='5%'><FONT SIZE=3><b>TOTAL<b></FONT></td>";
                echo "</tr>";
                $i=1;
				echo "<tr class=encabezadoTabla>";
				echo "<td align='center'><span id='wdie".$i++."' title='IMPRESIONES PENDIENTES' ><font size=2>Imp.</font></span></td>";
				echo "<td align='center'><span id='wdie".$i++."' title='MENSAJES<BR>SIN LEER' ><font size=2>Men.</font></span></td>";
				echo "<td align='center'><span id='wdie".$i++."' title='ADICIONES' ><font size=2>Adi.</font></span></td>";
				echo "<td align='center'><span id='wdie".$i++."' title='MODIFICACIONES' ><font size=2>Mod.</font></span></td>";
				echo "<td align='center'><span id='wdie".$i++."' title='CANCELACIONES' ><font size=2>Can.</font></span></td>";
				echo "<td align='center'><span id='wdie".$i++."' title='TRASLADOS' ><font size=2>Tra.</font></span></td>";
				echo "<td align='center'><span id='wdie".$i++."' title='POSTQUIRÚRGICOS' ><font size=2>Pqx.</font></span></td>";
				
				//ORDENAR EL ARRAY $todos_patrones SEGUN EL ORDEN DE UBICACION EN EL SISTEMA 
				@asort($orden_patrones);
				foreach ($orden_patrones as $clave_pat => $valor_pat)
				{
					$orden_patrones[$clave_pat]=$todos_patrones[$clave_pat];
				}
				$todos_patrones=$orden_patrones;
				//FIN ORDENAR
				
				//PINTAR EL NOMBRE DE LOS PATRONES A MOSTRAR
				$primera_entrada = 'si';
				$cant_pat_pint = 0;
				$posicion = 0;
				foreach($todos_patrones as $nomb_patr => $valor ) 
				{
					if( (isset($minimo_visible) && $minimo_visible > $posicion) || $cant_pat_pint > 16)
						$display = 'none';
					else
					{
						$display = '';
						$cant_pat_pint++;
					}
					
					if (strpos($nomb_patr,","))
					{
						$vect_nomb_patr = explode(',', $nomb_patr);
						$nom_pintar = '';
						foreach($vect_nomb_patr as $valor_nom_patr)
						{
							if ($nom_pintar == '')
								$nom_pintar = $valor_nom_patr;
							else 
								$nom_pintar.= ',<br>'.$valor_nom_patr;
						}
					}
					else
						$nom_pintar = $nomb_patr;
						
					echo "<td class = 'td_patron' rel='".$nomb_patr."' pos='".$posicion."' align='center' style='display:".$display."'><span id='wdie".$i."' title='".$valor."' ><font size=3>".$nom_pintar."</font></span></td>";
					$i++;
					$posicion++;
				}
				//echo "<td name='adelante' id='adelante' style='width:8px;cursor:pointer;background-color: #FFFFCC;color: #000000;font-size: 5pt;font-weight: bold;' align='center' rowspan='".(count($main_patrones)+1)."' onClick='mostrar_atras_adelante(\"adelante\")'>>></td>";
                echo "</tr>";
				//FIN PINTAR NOMBRE
				
                $wccoant="";
                $wclass=="fila1";
                $total_todos_cco=0;
                $increment=1;
                foreach($main_patrones as $pk_wccocod => $arr_patrones) //recorre por centro de costos
                {
					if ($wclass=="fila1")
						$wclass="fila2";
					else
						$wclass="fila1";
                    
					echo "<tr class='".$wclass."'>";//pintar nombre del centro de costos
                                                    
					$total_cco=0;       
					//recorrer el array = $num_patrones[$wccocod][$valor_patr], se recorre por CC 
					foreach($num_patrones as $pk_wccocod2 => $nom_patron)     
                    {
                        if($pk_wccocod == $pk_wccocod2)                 
                        {
 							//PINTAR ALERTAS
							echo "<td width='3%' align=center onClick='window.open(\"../reportes/Rep_lista_dietas.php?wemp_pmla=".$wemp_pmla."&wcco=".$pk_wccocod."-".$nom_cco[$pk_wccocod]."&wser=".$wser."&activo=on&wfec_i=".date("Y-m-d")."&wfec_f=".date("Y-m-d")."&impresas=on&wtipo=\", \"\", \"\")' style='cursor: pointer'><b><blink><div id='impresion".$pk_wccocod."' > &nbsp;".$array_alertas[$pk_wccocod]['impresiones']."</div><blink></b></td>";//Impresiones
							echo "<td width='3%' align=center><b><blink><div id='sinLeer2".$increment.$pk_wccocod."'></div><blink></b></td>";//Mensajes sin leer
							echo "<td width='3%' align=center><b><blink><div id='adicion".$pk_wccocod."'>".$array_alertas[$pk_wccocod]['70DB93']."</div><blink></b></td>";//Adiciones
							echo "<td width='3%' align=center><b><blink><div id='modificacion".$pk_wccocod."'>".$array_alertas[$pk_wccocod]['007FFF']."</div><blink></b></td>";//Modificaciones
							echo "<td width='3%' align=center><b><blink><div id='cancelacion".$pk_wccocod."'>".$array_alertas[$pk_wccocod]['FFCC00']."</div><blink></b></td>";//Cancelaciones
							echo "<td width='3%' align=center><b><blink><div id='traslados".$pk_wccocod."'>".$array_alertas[$pk_wccocod]['3299CC']."</div><blink></b></td>";//Traslados
							echo "<td width='3%' align=center><b><blink><div id='posq".$pk_wccocod."'>".$array_alertas[$pk_wccocod]['']."</div><blink></b></td>";//Pos quirurjicos
							//FIN PINTAR ALERTAS
							
							//mostrar el detalle de todos los pacientes asociados al centro de costo
                            echo "<td width='19%' nowrap id='".$pk_wccocod."' align='center' style='cursor:pointer;font-size: 8pt;' onClick='fnMostrar(\"".$pk_wccocod."\")'>
								<b>".$nom_cco[$pk_wccocod]."</font></b>"; 
							echo "<div align='center' id='detalle".$increment.$pk_wccocod."' style='display:none;cursor:default;background:none repeat scroll 0 0; "
                                ."position:relative;width:100 %;height:710px;overflow:auto;'><center><br>";
                            $id_alertas='';
							mostrar_detalle($nom_cco[$pk_wccocod], '', '', $arr_patrones, $main_pacientes, 'no', $pk_wccocod, $increment, &$id_alertas, $nom_servicio[0], $orden_habitaciones, $wfec_i, $cco_detalle);
							$increment++;
                            echo "</center></div></td>";
                            //fin mostrar todos
							
                            //mostrar detallado por patron
							$cant_pat_pint = 0; 
							$posicion = 0;
                            foreach($todos_patrones as $pk_nom_patron =>$valor2)    //se recorre por patrones
                            {
								if( (isset($minimo_visible) && $minimo_visible > $posicion) || $cant_pat_pint > 16)
									$display = 'none';
								else
								{
									$display = '';
									$cant_pat_pint++;
								}
									
                                if(array_key_exists($pk_nom_patron, $nom_patron))
                                {
                                    echo "<td rel='".$pk_nom_patron."' id='".$pk_wccocod.$pk_nom_patron."' align='center' style='display:".$display.";cursor:pointer;' onClick='fnMostrar(\"".$pk_wccocod.$pk_nom_patron."\")'>";
                                    echo $nom_patron[$pk_nom_patron];                       //cantidad del patron
                                    $total_cco=$total_cco+$nom_patron[$pk_nom_patron];
                                    echo "<div id='detalle".$increment.$pk_wccocod."' align='center' style='display:none;cursor:default;background:none repeat scroll 0 0; "
										."position:relative;width:100 %;height:710px;overflow:auto;'><center><br>";
                                    $id_alertas='';
									mostrar_detalle($nom_cco[$pk_wccocod], $pk_nom_patron, $valor2, $arr_patrones, $main_pacientes, 'si', $pk_wccocod, &$increment, &$id_alertas, $nom_servicio[0], $orden_habitaciones, $wfec_i, $cco_detalle);
									$increment++;
                                    echo "</center></div></td>";
                                    //acumular el total de los patrones
                                    if (!isset ($total_patrones[$pk_nom_patron]))
                                        $total_patrones[$pk_nom_patron]=$nom_patron[$pk_nom_patron];
                                    else
                                        $total_patrones[$pk_nom_patron]=$total_patrones[$pk_nom_patron]+$nom_patron[$pk_nom_patron];
                                    //fin acumular
                                }
                                else
                                    echo "<td  rel='".$pk_nom_patron."' align='center' style='display:".$display.";'> - </td>";
								
								$posicion++;
                            }
							
													
                            //total centro de costos
                            echo "<td  align='center' ><b>".$total_cco."</b></td>";
                            $total_todos_cco=$total_cco+$total_todos_cco;
                            break;
                        }
                    
                    }
                    echo "</tr>";
                    
                }
                //PINTAR EL TOTAL DE LOS PATRONES
                echo "<tr>";
					echo "<td colspan='7'></td>";
                    echo "<td class=encabezadoTabla align='center'>";
                        echo "TOTAL PATRONES";
                    echo "</td>";
					
					$cant_pat_pint = 0;
					$posicion = 0;
                    foreach($todos_patrones as $pk_nom_patron =>$valor2)    //rrecorrer el nombre de todos los patrones que aparecen en el reporte
                    {
						if( (isset($minimo_visible) && $minimo_visible > $posicion) || $cant_pat_pint > 16)
							$display = 'none';
						else
						{
							$display = '';
							$cant_pat_pint++;
						}
							
						if(array_key_exists($pk_nom_patron, $total_patrones))
                        {
							echo "<td rel='".$pk_nom_patron."' style='display:".$display."' class=encabezadoTabla align='center'>";
							echo $total_patrones[$pk_nom_patron];
                            echo "</td>";
                        }
                        else
                            echo "<td rel='".$pk_nom_patron."' style='display:".$display."' align='center' > - </td>";
						
						$posicion++;
                    }
                echo "<td class=encabezadoTabla align='center'>";
                echo $total_todos_cco;
                echo "</td>";
                echo "</tr>";
                //FIN TOTAL PATRONES
                echo "</table></center>"; //cierro tabla principal
            //===========================================================
            //     FIN  MATRIZ PRINCIPAL
            //===========================================================
			
			//===========================================================
            //     PINTAR RESUMEN X PRODUCTO DE PATRONES INDIVIDUALES
			//		$resumenXproducto[$nombre_producto]=$cantidad;	
            //===========================================================
				
				if (isset($resumenXproducto))
				{
					$wclass4="fila1";
					echo "<br><br><table width= 40%>";
					echo "<tr align=center class='encabezadoTabla'><td colspan=2><font SIZE=3>RESUMEN POR PRODUCTO DE PATRONES INDIVIDUALES</font></td></tr>";
					echo "<tr align=center class='encabezadoTabla'><td>Nombre</td><td>Cantidad</td></tr>";
					foreach ($resumenXproducto  as $clave_nombre => $valor_cantidad)
					{
						if ($wclass4=="fila1")
							$wclass4="fila2";
						else
							$wclass4="fila1";
						echo "<tr class='".$wclass4."'  style='cursor:pointer;' id='".$clave_nombre."' align=center onClick='fnMostrar(\"".$clave_nombre."\")'>";
							echo '<td align="left">'.$clave_nombre.'</td>';
							echo '<td>'.$valor_cantidad;	//style='display:none;cursor:default;background:none repeat scroll 0 0;position:absolute;height:100%;overflow:auto;'
								echo "<div align='center' style='display:none;cursor:default;background:none repeat scroll 0 0;position:relative;width:100 %;height:710px;overflow:auto;'><center><br>";
									mostrar_detalle_productos($productos_pacientes,$clave_nombre, $main_pacientes, $nom_servicio[0]);
								echo "<br><INPUT TYPE='button' value='Cerrar' onClick=' $.unblockUI();' style='width:100'><br><br>";
								echo "</center></div>";
							echo'</td>';
						echo'</tr>';
					}
					echo "</table><br>";
				}    
            //===========================================================
            //     FIN PINTAR RESUMEN X PRODUCTO
            //===========================================================
			
            }   //FIN SI HAY RESULTADOS
            else
                echo "<BR>NO SE ENCONTRARON RESULTADOS"; 
             
		} 
		 else
		    echo "NO SE ENCONTRARON SOLICITUDES PROGRAMADAS";
            
	 //echo "<meta id='refrescar' http-equiv='refresh' content='5;url=Monitor_dietas.php?wemp_pmla=".$wemp_pmla."&wuser=".$user."&wcco=".$wcco."&wser=".$wser."'>";   		    
	}
	    	    
	echo "</table>"; 
	echo "<div name='hidden_minimo' id='hidden_minimo'></div>";
	
	echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='hidden' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
	echo "<input type='HIDDEN' id='servicio' name='servicio' value='".$wser."'>";
	echo "<input type='HIDDEN' id='usuario' name='usuario' value='".$wusuario."'>";
	echo "<br><br>";
	echo "<table>";
	echo "<tr>";  
	echo "<td align=center colspan=7><A href='Monitor_dietas.php?wemp_pmla=".$wemp_pmla."&wuser=".$user."'><b>Retornar</b></A></td>"; 
	echo "</tr>";
	echo "</table>";
 
  //FIN FORMA
  echo "</form>";
	  
    echo "<table>"; 
    echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    echo "</table>";
    
} // if de register
?>