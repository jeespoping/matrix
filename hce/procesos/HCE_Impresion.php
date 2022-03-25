<html>
<head>
  	<title>MATRIX Programa de Impresion de Historia Clinica Hospitalaria HCE</title>
	<link type='text/css' href='HCE.css' rel='stylesheet'> 
<!--	<link rel="stylesheet" type="text/css" media="print" href="HCE.css" /> -->
<!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
<!-- Loading Calendar JavaScript files -->

    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <!-- Loading language definition file -->
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>
    <script type='text/javascript' src='../../../include/root/jquery-1.3.2.min.js'></script>
	<script type='text/javascript' src='../../../include/root/ui.core.min.js'></script>
	<script type='text/javascript' src='../../../include/root/jquery.blockUI.min.js'></script>
    <script type='text/javascript' src='HCE_Seguridad.js' ></script> 
    <script type="text/javascript">
    
	alturaPagina = 24/0.026458333;
    paginas = 0;
    restoPaginas = 0;
      
    /*********************************************************************************
     * Encuentra la posicion en X de un elemento
     *********************************************************************************/
     function paginar( campo, principal, data )
     {
          if( campo ){
               if( campo.tagName ){

                   //var cabecera = document.getElementById('hiPaciente').value;
                   var cabecera = "";

                    switch( campo.tagName )
                    {
                    
                        case 'TABLE':
                            var aux = document.createElement( "div" );
                            aux.innerHTML = "<table border=1 cellpadding=5 width='712' cellspacing=0 class=tipoTABLE></table>";

                            tabla = campo.cloneNode(true);
                            tabla = campo;

                            var sumaAltura = 3/0.026458333;
                            var formulario = "";
                            var data1 = "";
							
                            for( var i = 0; i < campo.rows.length; i++ )
                            {
								var paso = 0;
								
								if(campo.rows[i].cells[0].innerHTML.substring(0,2) == "F=")
								{
									//alert(campo.rows[i].cells[0].innerHTML);
									formulario = campo.rows[i].cells[0].innerHTML;
									paso=1;
								}
								posFila = findPosY( campo.rows[i] );

								sumaAltura = sumaAltura + campo.rows[i].clientHeight;
								
								posFila = posFila+campo.rows[i].clientHeight;

								if( sumaAltura > alturaPagina ){

									restoPaginas = restoPaginas+(alturaPagina+paginas*alturaPagina-posFila+campo.rows[i].clientHeight );
									paginas++;

									sumaAltura = campo.rows[i].clientHeight;
									if(paginas > 1)
									{
										data1 = data + " " + formulario;
										var aux2 = document.createElement( "div" );
										aux2.innerHTML = "<a>Página: "+paginas+"<br><br></a>";
										aux2.innerHTML = "<a><table width='712'><tr><td align='center' class=tipoPac>"+data1+"</td><td class=tipoPac1>P&aacute;gina: "+parseInt( paginas )+"</td></tr></table><br><br></a>"
										principal.appendChild( aux2.firstChild );
									}
									else
									{
										var aux2 = document.createElement( "div" );
										aux2.innerHTML = "<a><br><br></a>";
										aux2.innerHTML = "<a><table width='712'><tr><td align='center' class=tipoPac></td><td class=tipoPac1></td></tr></table><br><br></a>"
										principal.appendChild( aux2.firstChild );
									}
									
									principal.appendChild( aux.firstChild );

									aux.innerHTML = "<div class='saltopagina'></div>";
									principal.appendChild( aux.firstChild );

									aux.innerHTML = "<table border=1 cellpadding=5 width='712' cellspacing=0 class=tipoTABLE></table>";
								}
								
                                var fila = aux.firstChild.insertRow( aux.firstChild.rows.length );
                                var numCeldas = campo.rows[i].cells.length
                                for( var  j = 0; j < numCeldas ; j++){
									if(paso == 0)
									{
										fila.appendChild( tabla.rows[ i ].cells[0] );
									}
                                }
                            }

                            paginas++;
                            if(paginas > 1)
							{
								data1 = data + " " + formulario;
								var aux2 = document.createElement( "div" );
								aux2.innerHTML = "<a>Página: "+paginas+"<br><br></a>";
								aux2.innerHTML = "<a><table width='712'><tr><td align='center' class=tipoPac>"+data1+"</td><td class=tipoPac1>P&aacute;gina: "+parseInt( paginas )+"</td></tr></table><br><br></a>"
								principal.appendChild( aux2.firstChild );
							}
							else
							{
								var aux2 = document.createElement( "div" );
								aux2.innerHTML = "<a><br><br></a>";
								aux2.innerHTML = "<a><table width='712'><tr><td align='center' class=tipoPac></td><td class=tipoPac1></td></tr></table><br><br></a>"
								principal.appendChild( aux2.firstChild );
							}

                            campo.style.display = 'none';
                            //debugger;
                            //principal.removeChild(campo);
                            principal.appendChild( aux.firstChild );
                            
                         break;
                    }
               }
          }
     }
     
    function findPosX(obj)
      {
        var curleft = 0;
        if(obj.offsetParent)
            while(1)
            {
              curleft += obj.offsetLeft;
              if(!obj.offsetParent)
                break;
              obj = obj.offsetParent;
            }
        else if(obj.x)
            curleft += obj.x;
        return curleft;
      }

    /************************************************************************************
     * encuentra la posicion Y de un elemento
     ************************************************************************************/
    function findPosY(obj)
    {
        var curtop = 0;
        if(obj.offsetParent)
            while(1)
            {
              curtop += obj.offsetTop;
              if(!obj.offsetParent)
                break;
              obj = obj.offsetParent;
            }
        else if(obj.y)
            curtop += obj.y;
        return curtop;
      }

      function posdivs(auxdivpix,X,Y,An,Al,ID,grafica)
      {
	    X=parseInt(X);
	    Y=parseInt(Y);
		auxdivpix.style.position = "absolute";
		auxdivpix.style.zIndex = "200";
		auxdivpix.style.top = parseInt( Y + findPosY(document.getElementById(grafica)))+"px";
		auxdivpix.style.left = parseInt( X + findPosX(document.getElementById(grafica)))+"px";
		auxdivpix.style.width = An+"px";
		auxdivpix.style.height = Al+"px";
		auxdivpix.style.border='solid';
		auxdivpix.innerHTML="<table><tr><td bgcolor=white><font size=2em><b>"+ID+"</b></font></td></tr></table>";
		
      }

      function pintardivs()
      {
		//alert("entre a pintar divs");
		//alert("valores"+document.getElementById('Hgraficas').value);
		var elements = document.getElementsByTagName('img'); 
		
		if(document.getElementById('Hgraficas'))
		{
			var G = document.getElementById('Hgraficas').value;
			GT = G.split('|');
			nimg = elements.length -1;
			for(var x = 0; x < GT.length; x++)
			{ 
				var textG = "";
			    var varable = GT[x];
			    nximg = 0;
			    wsw = -1;
			    while (nximg <= nimg)
			    {
					if(elements[nximg].id.substring(0,1) == "G" && elements[nximg].id.substring(1) == x.toString())
					{
						wsw = nximg;
						//alert("Encontro : "+nximg+" "+x+" "+elements[nximg].id.substring(0,1)+" "+elements[nximg].id.substring(1));
					}
					nximg++;
				};
			    var ID = 1;
			    if(GT[x] != "" && wsw != -1)
			    {
					if(varable.length > 0 && elements[wsw].id.substring(0,1) == "G")
					{
						frag1 = varable.split('^');
						div=document.createElement('div');
						for (i=1;i<frag1.length;i++)
						{
							var div=document.createElement('div');
							frag2 = frag1[i].split('~');  
							div.id=frag2[0];
							document.HCE_Impresion.appendChild(div);
							posdivs(div,frag2[1],frag2[2],frag2[3],frag2[4],frag2[0],elements[wsw].id);
							textG = textG + frag2[0]+". "+frag2[5]+"<br>";
						}
					}
				}
		    }
	    }
      }
	  
	  
	function consultarHtmlPorProgramaAnexo(cadenaProgramasAnexos,cadenaPosicionesProgramasAnexos)
	{
		var historia = $("#whistoriaPac").val();
		var ingreso = $("#wingresoPac").val();
		var wemp_pmla = $("#wemp_pmla").val();
		
		programasAnexos = cadenaProgramasAnexos.split("|");
		posicionProgramasAnexos = cadenaPosicionesProgramasAnexos.split("|");
		
		// hacer ajax a cada programa anexo para obtener el html utlizado en la construccion del pdf
		htmlProgramasAnexos = "";
		for(var i=0;i<(programasAnexos.length)-1;i++)
		{
			if($("input[name=imp["+posicionProgramasAnexos[i]+"]]").attr('checked'))
			{
				programaAnexo = programasAnexos[i];
				programaAnexo = programaAnexo.split("?");
				programaAnexo = programaAnexo[0];
				
				programaAnexo = "../../../"+programaAnexo;
				
				$.ajax({
					url:programaAnexo,
					type: "POST",
					async: false,
					data: 	{
								consultaAjax 	: '',
								action			: 'consultarHtmlImpresionHCE',
								wemp_pmla		: wemp_pmla,
								historia		: historia,
								ingreso			: ingreso
							} ,
					success: function(data) {
						
						htmlProgramasAnexos += data.html +"|||||-----*****";
					},
					dataType: "json"
				});
			}
		}
		
		return htmlProgramasAnexos;
	}

    </script>
</head>
<body onLoad= 'pintardivs();' BGCOLOR="FFFFFF" oncontextmenu = "return true" onselectstart = "return true" ondragstart = "return false">
<script type="text/javascript">
<!--
	function enter()
	{
		document.forms.HCE_Impresion.submit();
	}
	
	function enterOK()
	{
		var htmlProgramasAnexos = consultarHtmlPorProgramaAnexo($("#cadenaProgramasAnexos").val(),$("#cadenaPosicionesProgramasAnexos").val())
		$("#htmlProgramasAnexos").val(htmlProgramasAnexos);
		
		document.getElementById('ok').checked=true;
		document.forms.HCE_Impresion.submit();
	}
	
	function activarModalIframe(titulo,nombre,url,alto,ancho)
	{
		var Sialto="no";
		var Siancho="no";
		var Sboton="si";
		if(alto == '-1')
		{
			alto='0';
			Sboton="no";
		}
		if(alto == '0')
		{
			Sialto="si";
			alto=screen.availHeight;
		}
		if(ancho == '0')
		{
			Siancho="si";
			ancho=screen.availWidth;
		}
		if(Sboton == "si")
		{
			var html = "" +
			"<table cellpadding=1 cellspacing=1 width='100%' style='cursor:default'>" +
			"<tr height='10' class='encabezadoTabla'>" +
			"<td >" +
			"<b>"+titulo+"</b>" +
			"</td>"+    
			"<td align='center'>" +
			"<img src='../../images/medical/HCE/button.gif' title='Cerrar' onclick='javascript:cerrarModal();' style='cursor:hand; cursor: pointer;'>" +
			"</td></tr>" +    
			"<tr><td colspan=2 class='textoNormal'>";
		}
		else
		{
			var html = "" +
			"<table cellpadding=1 cellspacing=1 width='100%' style='cursor:default'>" +
			"<tr height='10' class='encabezadoTabla'>" +
			"<td >" +
			"<b>"+titulo+"</b>" +
			"</td>"+    
			"<td align='center'>" +
			"" +
			"</td></tr>" +    
			"<tr><td colspan=2 class='textoNormal'>";
		}
		if(Sialto == 'si' && Siancho == 'si')
		{
			html = html + "<iframe name='" + nombre + "' src='" + url + "' height='" + (parseInt(alto,10) - 70) + "' width='100%' scrolling=yes frameborder='0'></iframe>";
		}
		else
		{
	    	html = html + "<iframe name='" + nombre + "' src='" + url + "' width='100%' height='" + (parseInt(alto,10) - 30) + "' width='" + ancho + "' frameborder='0'></iframe>";
    	}
	    
	    html = html + "</td></tr></table>";
	    
	   
	    //var pare = window.parent.parent;
	    var pare = window.parent;
	    
	    if(Sialto == 'si' && Siancho == 'si')
	    {
			$.blockUI({ message: html, css: { width: ancho + 'px',left: '0px',top: '0px'  },centerX: false,centerY: false});	
	    }
	    else
	    {
			$.blockUI({ message: html, css: { width: ancho + 'px',left: '20px',top: '20px'  },centerX: false,centerY: false});	
		}
	}
	
	function cerrarModal()
	{
		$.unblockUI();
	}
	
	function salto1(titulo)
	{
		window.print();
	}
	
	
	function enviarPdf(wemp_pmla,historia, ingreso, rutaArchivo, nombreArchivo, nombrePaciente, nombreEmpresa, wdbmhos, usuario, nombreEntidad)
	{
		document.getElementById("btnEnviarPdf").disabled = true;
		$("#msjEspere").show();
		
		const asunto = "";
		const mensaje = "";
		
		$.ajax({
			url: "envioCorreoHCEOrdenes.php",
			type: "POST",
			dataType: "json",
			data:{
				consultaAjax 	: '',
				accion			: 'enviarPdf',
				wemp_pmla		: wemp_pmla,
				historia		: historia,
				ingreso			: ingreso,
				email			: $('#emailEnviarCorreo').val(),
				rutaArchivo		: rutaArchivo,
				nombreArchivo	: nombreArchivo,
				asunto			: asunto,
				mensaje			: mensaje,
				prefijo			: 'HC',
				wbasedatoMovhos	: wdbmhos,
				usuario			: usuario,
				envioPaciente	: $("#envioPaciente").val(),
				nombrePaciente	: nombrePaciente,
				nombreEntidad	: nombreEntidad,
				nombreEmpresa	: nombreEmpresa
				},
				async: false,
				success:function(respuesta) {
					
					document.getElementById("btnEnviarPdf").disabled = false;
					$("#msjEspere").hide();

					alert(respuesta);
					
				}
		});
	}
	
//-->
</script>
<BODY TEXT="#000066">
<?php
if(isset($_REQUEST['origen']) && !isset($_REQUEST['wemp_pmla'])){
	$wemp_pmla=$_REQUEST['origen'];
}
elseif(isset($_REQUEST['wemp_pmla'])){
	$wemp_pmla = $_REQUEST['wemp_pmla'];
}
else{
	die('Falta parametro wemp_pmla...');
}
include_once("conex.php");
include_once("hce/funcionesHCE.php");

function bi($d,$n,$k)
{
	$n--;
	if($n > 0)
	{
		$li=0;
		$ls=$n;
		while ($ls - $li > 1)
		{
			$lm=(integer)(($li + $ls) / 2);
			$val=strncmp(strtoupper($k),strtoupper($d[$lm]),20);
			if($val == 0)
				return $lm;
			elseif($val < 0)
					$ls=$lm;
				else
					$li=$lm;
		}
		if(strtoupper($k) == strtoupper($d[$li]))
			return $li;
		elseif(strtoupper($k) == strtoupper($d[$ls]))
					return $ls;
				else
					return -1;
	}
	else
		return -1;
}


function validar_formulario($clave,$num,&$data)
{
	$numero=0;
	for ($i=0;$i<$num;$i++)
	{
		if(isset($data[$i][0]) and $clave == $data[$i][0] and $data[$i][5] == 0)
		{
			$numero++;
			$data[$i][5] = 1;
		}
	}
	if($numero > 0)
		return true;
	else
		return false;
}


function consultarUsuarioHabilitado($conex,$origenConsulta,$key)
{
	$queryHCE = "SELECT Detval 
				   FROM root_000051 
				  WHERE Detapl='hce' 
					AND Detemp='".$origenConsulta."';";
					
	$resHCE = mysql_query($queryHCE, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryHCE . " - " . mysql_error());
	$numHCE = mysql_num_rows($resHCE);
	
	$usuarioHabilitado = false;
	if($numHCE>0)
	{
		$rowHCE = mysql_fetch_array($resHCE);
		$wbasedatoHCE = $rowHCE['Detval'];
		
		$queryRol = " SELECT Usurol
						FROM ".$wbasedatoHCE."_000020, ".$wbasedatoHCE."_000019 
					   WHERE Usucod='".$key."'
						 AND Rolcod=Usurol
						 AND (Rolmed='on' 
						  OR  Rolenf='on');";
						  
	    $resRol = mysql_query($queryRol, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryRol . " - " . mysql_error());
		$numRol = mysql_num_rows($resRol);
		
		if($numRol>0)
		{
			$usuarioHabilitado = true;
		}
	}

	return $usuarioHabilitado;	
}

include_once("hce/HCE_print_function.php");

/**********************************************************************************************************************  
	   PROGRAMA : HCE_Impresion.php
	   Fecha de Liberación : 2010-06-01
	   Autor : Ing. Pedro Ortiz Tamayo
	   Version Actual : 2020-05-04
	   
	   OBJETIVO GENERAL : 
	   Este programa ofrece al usuario una interface gráfica que permite generar una impresion parametrizada de los registros
	   de la Historia Clinica Electronica HCE.
	   
	   
	   REGISTRO DE MODIFICACIONES 
	     11/03/2022 - Brigith Lagares: Se realiza estadarización del wemp_pmla y se actualiza encabezado
	   	
		.2020-10-29
			Se cambia CREATE TABLE por CREATE TEMPORARY
		.2020-05-04
			Para el envío de HCE por correo se modifica en la función enviarPdf() el mensaje y asunto como vacíos ya que el 
			mensaje se construirá dinamicamente desde envioCorreoHCEOrdenes.php con los parámetros de root_000051 
			mensajeCorreoEnvioHcePaciente y mensajeCorreoEnvioHceEntidad
		.2020-04-02
			Se adiciona la opción de envío por correo del pdf con la historia clínica, se habilita la opción si se recibe el 
			parámetro enviarCorreo en on y se recibe el email en el parámetro emailEnviarCorreo
	    .2019-10-09
			Se modifica el texto Edad por Edad actual en la información demográfica.
	    .2019-09-16
			Se modifica la fecha final por defecto para los pacientes que tienen registros en HCE despues de la fecha de egreso 
			y de esta forma muestre la fecha del último formulario diligenciado.
			Se agrega el parámetro origenConsulta que trae por url la empresa donde se esta realizando la consulta si $origenConsulta 
			es diferente a $origen se valida si el usuario es médico o enfermera (rol configurado en hce_000019) y les permite visualizar
			todos los formularios de la historia clínica que el paciente tenga diligenciados.
	    .2019-08-16
			Se modifica el query que obtiene el número de ingresos del paciente para que los ordene por fecha y hora descendiente
			y no por ingreso ya que los mostraba en desorden.
	    .2019-08-13
			Se agrega el include a funcionesHCE.php con la función calcularEdadPaciente() y se reemplaza en el script el cálculo 
			de la edad del paciente por dicha función, ya que el cálculo se realizaba con 360 días, es decir, no se tenían en cuenta 
			los meses de 31 días y para los pacientes neonatos este dato es fundamental.
	    .2019-07-12
			Se modifica el query que obtiene el número de ingresos del paciente para que muestre todos los ingresos así no se 
			haya diligenciado ningún formulario (Si no tiene formulario en hce_000036 se muestra el mensaje: "El paciente no tiene 
			formularios diligenciados."), se realiza la modificación para garantizar que el rango de fechas corresponda al ingreso.
	    .2017-01-25
			Se agrega la impresion de reportes configurados como programas anexos en hce_000023 y hce_000024, teniendo en cuenta que
			primero se debe validar que dicho reporte aplique para el paciente con esa historia e ingreso. Si es consulta se muestran 
			los reportes en iframes y si es impresion (construccion de pdf) se hace una consulta Ajax a cada reporte que retorna un html 
			con el que se forma una cadena que es enviada a la funcion construirPDF en (HCE_print_function.php)
	    .2015-01-15
			Se cambia el query para para validar las distintas opciones de Detvim (A-Ambos,C-Consulta,I-Impresion,N-Ninguno).
			Se valida qie el campo Firfir de la tabla 36 este en "on" para la impresión.
			
	    .2014-04-16
			Se arregla la consulta de de formularios diligenciados ya era muy ineficiente.
			
	    .2014-01-30
			Se arregla la consulta de ingresos que estaba fija solo para tablas con nombre hce.
			
	    .2014-01-09
			Se arregla el borrado de la tabla temporal TESPECIAL.
			
	    .2013-12-12
			Se cambia la busqueda de datos demograficos para tener en cuenta ingresos anteriores al ultimo.
			
	    .2013-11-05
              Se ponen dinamicas las tablas de hce y movhos al igual que la empresa origen.
              Solo se muestran los formularios efectivamente diligenciados.
              La impresion se hace a traves de la rutina del centro de impresion.
              
	    .2013-07-30
			Se cambia el query de consulta a la base de datos en la tabla HCE 2 para traer los campos activos e inactivos.
	    
	    .2013-05-02
			La funcion de impresion se saca del programa para colocarla en un script indepnediente que pueda ser utilizado
			en otros programas.
	    
	    .2013-04-15
			Al programa se le adicionan las siguientes funcionalidades:
					1. Se habilita el programa de impresion para que sirva para la consulta.
					2. Se cambia al metodo de seleccion de formularios para imprimir o consultar.
					3. Se adiciona la opcion de impresion de paquetes de formularios segun la tabla 43.
					4. Se coloco la logica de imprension en una funcion parametrizada.
					
	    .2013-02-10
			Se modifica el programa para corregir la lista de seleccion de formularios a imprimir, ya que no estaba mostrando el
			Ultimo formulario.
			
	    .2012-12-17
			Al programa se le adicionan las siguientes funcionalidades:
					1. Impresion ordenada de campos Seleccion tipo tabla M1 para una mejor comprension de la informacion.
					2. Impresion de campos GRID en forma de tabla
					3. Impresion de Imagenes de firma digitalizada
					4. Se coloco la seguridad contra copia en el archivo HCE_Seguridad.js
			
	    .2012-09-13
			Se modifica la validacion de la firma para NO tener en cuenta la clave. Esto por si cambia de clave en el tiempo.
			
	    .2012-04-24
			Se modifico la impresion de la firma y las notas, ya que la informacion	de las ultimas se estaba montando en la firma.
					
	    .2011-11-11
			Se modifico la consultas de las Notas para incluir la tabla de usuarios ya la impresion no estaba mostrando el 
			usuario que la diligencio.
			
	    .2011-11-01
			Se modifico el programa para incluir dentro de la impresion de las notas la informacion del usuario que la realizo.
			
	    .2011-10-13
			Se modifico el programa para tener en cuenta las opciones validando el servicio que viene en la variable de
			ambiente. Se adiaciona el documento y el tipo de documento al encabezado de impresion de la historia.
	    
	    .2011-07-19
			Se adiciona el acceso a la tabla 16 de movhos para inicializar la fecha de ingreso del paciente.
			
	    .2011-07-01
	   		Se cambia la impresion de la firma electronica al incluir el titulo porfesion o especialidad.
	   		
	    .2011-06-30
	   		Se cambia la impresion de la firma electronica al incluir identificacion registro medico y especialidad.
	   		
	    .2011-06-08
			Se modifico la impresion en los siguientes items:
				1. Los campos de Titulos, Subtitulos y Label se imprimen segun el criterio del menor mayor con el proposito de
				   no imprimir estos campos cuando no sea necesario y ahorar espacio y mejorar la presentación.
				2. Las columnas x campos varian de tamaños de 1 u 8  a desde 1 a 8 segun el tamaño en caracteres de lo que se 
				    vaya a imprimir. Esto mejora la presentación y el ahorro de espacio.
			
		.2011-05-10
			Se modifico la impresion del campo seleccion modo tabla ya que cuando contenia el caracter (-) la impresion salia
			incorrecta.
			
	    .2011-04-25
			Se corrige la impresion de los campos seleccion. Estaba saliendo erronea.
			Se condiciona la impresion de campos Titulo, Subtitulo y Label al sexo.
			Se corrige la Impresion de Titulos, Subtitulos y Label.
			
	    .2011-04-13
			Se modifica la impresion de los campos de Label, subtitulo y Titulo que no estaban saliendo correctamente. 
			
	    .2011-04-12
			Se modifica la impresion de los campos de Label y subtitulo que no estaban saliendo. 
			
	    .2011-04-11
			Se modifica el orden de impresion basandose en el campo Encoim de la tabla HCE(1). 
			No imprime formularios que no esten firmados x el usuario.
			Se imprimen Label y Subtitulos que tengan el campo de impresion continua prendido.
			
	    .2011-03-17
			Ultima Version Beta.
			
	    .2011-02-24
			Ultima Version Beta.
			
	    .2011-02-22
			Ultima Version Beta.
			
	    .2011-02-14
			Ultima Version Beta.
			
	   	.2010-06-01
	   		Release de Versión Beta. 
	   
***********************************************************************************************************************/
@session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	echo "<form name='HCE_Impresion' action='HCE_Impresion.php' method=post>";
	

	echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
	echo "<input type='HIDDEN' name= 'wemp_pmla' value='".$wemp_pmla."'>";
	echo "<input type='HIDDEN' name= 'wdbmhos' value='".$wdbmhos."'>";
	echo "<input type='HIDDEN' name= 'protocolos' value='".$protocolos."'>";
	echo "<input type='HIDDEN' name= 'CLASE' value='".$CLASE."'>";
	if(isset($BC))
		echo "<input type='HIDDEN' name= 'BC' value='".$BC."'>";
	if(isset($wing))
		echo "<input type='HIDDEN' name= 'wing' value='".$wing."'>";
	if(isset($wservicio))
		echo "<input type='HIDDEN' name= 'wservicio' value='".$wservicio."'>";
	
	echo "<input type='HIDDEN' name= 'origenConsulta' value='".$origenConsulta."'>";
	echo "<input type='HIDDEN' name= 'noCentrar' value='".$noCentrar."'>";
	echo "<input type='HIDDEN' id='enviarCorreo' name= 'enviarCorreo' value='".$enviarCorreo."'>";
	echo "<input type='HIDDEN' id='emailEnviarCorreo' name= 'emailEnviarCorreo' value='".$emailEnviarCorreo."'>";
	echo "<input type='HIDDEN' id='envioPaciente' name= 'envioPaciente' value='".$envioPaciente."'>";
	
	$query = "select count(*) from root_000037 ";
	$query .= " where oriced = '".$wcedula."'";
	$query .= "   and oritid = '".$wtipodoc."'";
	$query .= "   and oriori = '".$wemp_pmla."'";
	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$row = mysql_fetch_array($err);
	if($row[0] == 0)
	{
		echo "<center><table border=0>";
		echo "<tr><td id=tipoL09 colspan=".$span."><IMG SRC='/matrix/images/medical/HCE/Triste.png' style='vertical-align:middle;'>NO EXISTE INFORMACION EN LA HCE PARA ESTE PACIENTE</td></tr>";
		echo "</table></center>";
	}
	elseif(!isset($ok))
	{
		//                 0      1      2      3      4      5      6      7      8      9      10     11
		$query = "select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing,Ingnre,Ubisac,Ubihac,Cconom from root_000036,root_000037,".$wdbmhos."_000016,".$wdbmhos."_000018,".$wdbmhos."_000011 ";
		$query .= " where pacced = '".$wcedula."'";
		$query .= "   and pactid = '".$wtipodoc."'";
		$query .= "   and pacced = oriced ";
		$query .= "   and pactid = oritid ";
		$query .= "   and oriori = '".$wemp_pmla."'";
		$query .= "   and inghis = orihis ";
		if(!isset($wing))
			$query .= "   and inging = oriing ";
		else
			$query .= "   and inging = '".$wing."' ";
		$query .= "   and ubihis = inghis "; 
		$query .= "   and ubiing = inging ";
		$query .= "   and ccocod = ubisac ";
		$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		$row = mysql_fetch_array($err);
		$sexo="MASCULINO";
		if($row[5] == "F")
			$sexo="FEMENINO";
		// $ann=(integer)substr($row[4],0,4)*360 +(integer)substr($row[4],5,2)*30 + (integer)substr($row[4],8,2);
		// $aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
		// $ann1=($aa - $ann)/360;
		// $meses=(($aa - $ann) % 360)/30;
		// if ($ann1<1)
		// {
			// $dias1=(($aa - $ann) % 360) % 30;
			// $wedad=(string)(integer)$meses." Meses ".(string)$dias1." Dias";	
		// }
		// else
		// {
			// $dias1=(($aa - $ann) % 360) % 30;
			// $wedad=(string)(integer)$ann1." A&ntilde;os ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
		// }
		$wedad = calcularEdadPaciente($row[4]);
		$wpac = $wtipodoc." ".$wcedula."<br>".$row[0]." ".$row[1]." ".$row[2]." ".$row[3];
		$nombrePaciente = $row[0]." ".$row[1]." ".$row[2]." ".$row[3];
		$nombreEntidad = $row[8];
		if(!isset($wing))
			$wing=$row[7];
		if(!isset($whis))
			$whis=$row[6];
		$color="#dddddd";
		$color1="#C3D9FF";
		$color2="#E8EEF7";
		$color3="#CC99FF";
		$color4="#99CCFF";
		if(isset($BC))
			echo "<IMG SRC='/matrix/images/medical/HCE/button.gif' onclick='javascript:top.close();'><br><br>";
		echo "<center><table border=1 width='712' class=tipoTABLE1>";
		echo "<tr><td rowspan=3 align=center><IMG SRC='/MATRIX/images/medical/root/HCE".$wemp_pmla.".jpg' id='logo'></td>";	
		echo "<td id=tipoL01C>Paciente</td><td colspan=5 id=tipoL04>".$wpac."</td></tr>";
		if($CLASE == "I")
		{
			echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$row[6]."-".$wing."</td><td id=tipoL01>Edad actual</td><td id=tipoL02C>".$wedad."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".$sexo."</td></tr>";
			echo "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row[11]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$row[10]."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$row[8]."</td></tr>";
			echo "</table></center><br>";
		}
		else
		{
			echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$row[6]."-";
			// $query = "select cast(Firing as UNSIGNED) from ".$empresa."_000036 ";
			// $query .= "  where ".$empresa."_000036.Firhis='".$row[6]."'";
			// $query .= " group by 1 ";
			// $query .= " order by 1 desc ";
			
			$query = "SELECT Ubiing 
						FROM ".$wdbmhos."_000018 
					   WHERE Ubihis='".$row[6]."' 
					ORDER BY Fecha_data DESC,Hora_data DESC;";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				echo "<select name='wing' id='tipoL02S' OnChange='enter()'>";
				for ($j=0;$j<$num1;$j++)
				{
					$row1 = mysql_fetch_array($err1);
					if(isset($wing) and $wing == $row1[0])
						echo "<option selected>".$row1[0]."</option>";
					else
						echo "<option>".$row1[0]."</option>";
				}
				echo "</select>";
			}
			else
			{
				$wing=$row[7];
				echo $wing;
				echo "<input type='hidden' id='wing' value='".$wing."'>";
			}
			echo "</td><td id=tipoL01>Edad actual</td><td id=tipoL02C>".$wedad."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".$sexo."</td></tr>";
			echo "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row[11]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$row[10]."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$row[8]."</td></tr>";
			echo "</table></center><br>";
		}
		
		if(!isset($wing))
			$wing=$row[7];
		$query = "select Fecha_data, Ubifad from ".$wdbmhos."_000018 ";
		$query .= "  where Ubihis='".$row[6]."'";
		$query .= "    and Ubiing='".$wing."'";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		if($num1 > 0)
		{
			$row1 = mysql_fetch_array($err1);
			$wfechai=$row1[0];
			if($row1[1] != "0000-00-00")
				$wfechaf=$row1[1];
			else
				$wfechaf=date("Y-m-d");
		}
		else
		{
			$wfechai=date("Y-m-d");
			$wfechaf=date("Y-m-d");
		}
		// $query = "select fecha_data from ".$empresa."_000036 where firhis='".$row[6]."' and firing=".$wing."  order by 1 desc ";
		$query = "select fecha_data from ".$empresa."_000036 where firhis='".$row[6]."' and firing=".$wing."  order by 1 desc  LIMIT 1;";
		$err1 = mysql_query($query,$conex);
		$num1 = mysql_num_rows($err1);
		if($num1 > 0)
		{
			$row1 = mysql_fetch_array($err1);
			$wfecultR = $row1[0];
			if($wfecultR > $wfechaf)
				$wfechaf = $wfecultR;
				// $wfecultR = $wfechaf;
		}
		if($protocolos == "0")
		{
			echo "<table border=0 align=center>";
			if($CLASE == "I")
				echo "<tr><td id=tipoTI01 colspan=8>IMPRESION HISTORIA CLINICA ELECTRONICA Ver. 2022-03-11<td></tr>";
			else
				echo "<tr><td id=tipoTI01 colspan=8>CONSULTA HISTORIA CLINICA ELECTRONICA Ver. 2022-03-11<td></tr>";
			echo "<tr><td id=tipoTI05 colspan=8>Fecha Inicial <input type='TEXT' name='wfechai' size=10 maxlength=10 id='wfechai' readonly='readonly' value=".$wfechai." class=tipo6>&nbsp;&nbsp;&nbsp;<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1'>";
			?>
			<script type="text/javascript">//<![CDATA[
				Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfechai',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
			//]]></script>
			<?php
			echo "&nbsp;&nbsp;&nbsp;Fecha Final <input type='TEXT' name='wfechaf' size=10 maxlength=10 id='wfechaf' readonly='readonly' value=".$wfechaf." class=tipo6>&nbsp;&nbsp;&nbsp;<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger2'></td>";
			?>
			<script type="text/javascript">//<![CDATA[
				Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfechaf',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
			//]]></script>
			<?php
			echo "</td></tr>";
			echo "<tr><td id=tipoTI05 colspan=8>ESPECIALIDADES ";
			echo "<select name='wespecial' id='tipoL02SL'>";
			echo "<option>TODAS</option>";
			$query = "select Espcod, Espnom from ".$empresa."_000036, ".$wdbmhos."_000048, ".$wdbmhos."_000044 ";
			$query .= "  where Firhis = '".$row[6]."'";
			$query .= "	and Firing = '".$wing."'";
			$query .= "	and Firusu = Meduma ";
			$query .= "	and Medesp = Espcod ";
			$query .= " group by 1,2 ";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			if($num1 > 0)
			{
				for ($j=0;$j<$num1;$j++)
				{
					$row1 = mysql_fetch_array($err1);
					if(isset($wespecial) and $row1[0]."-".$row1[1] == $wespecial)
						echo "<option selected>".$row1[0]."-".$row1[1]."</option>";
					else
						echo "<option>".$row1[0]."-".$row1[1]."</option>";
				}
			}
			echo "</select></td></tr>";
			echo "<tr><td id=tipoTI02>SELECCION</td><td id=tipoTI02>DESCIPCION</td><td id=tipoTI02>SELECCION</td><td id=tipoTI02>DESCIPCION</td><td id=tipoTI02>SELECCION</td><td id=tipoTI02>DESCIPCION</td><td id=tipoTI02>SELECCION</td><td id=tipoTI02>DESCIPCION</td></tr>";
			echo "<tr><td id=tipoTI05 colspan=8>Marcar Todos<input type='checkbox' name='all'  onclick='enter()'>&nbsp;&nbsp;Desmarcar Todos<input type='checkbox' name='noall'  onclick='enter()'></td></tr>";
			if(!isset($dta))
			{
				if(!isset($wservicio))
					$wservicio="*";
				$vistas=array();
				$numvistas=0;
				$query  = "select ".$empresa."_000021.Rararb from ".$empresa."_000020,".$empresa."_000021,".$empresa."_000009,".$empresa."_000037 ";
				$query .= "   where ".$empresa."_000020.Usucod = '".$key."' ";
				$query .= " 	and ".$empresa."_000020.Usurol = ".$empresa."_000021.Rarcod ";
				$query .= " 	and ".$empresa."_000021.Rararb = ".$empresa."_000009.precod "; 
				$query .= "	    and ".$empresa."_000009.precod = ".$empresa."_000037.Forcod ";
				$query .= "	    and ".$empresa."_000037.Forser = '".$wservicio."' ";
				$query .= "   order by 1";
				
				$err = mysql_query($query,$conex) or die("aca ".mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if ($num>0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$vistas[$i] = $row[0];
					}
				}
				$numvistas=$num;
				
				$win = "(";
				if($CLASE == "I")
					$query = "select Firpro from ".$empresa."_000036 WHERE Firhis = '".$whis."' and Firing = '".$wing."' and Firfir = 'on' group BY Firpro ";
				else
					$query = "select Firpro from ".$empresa."_000036 WHERE Firhis = '".$whis."' and Firing = '".$wing."' group BY Firpro ";
				$err = mysql_query($query,$conex) or die("aqui ".mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						if($i > 0)
							$win .= ",".chr(34).$row[0].chr(34);
						else
							$win .= chr(34).$row[0].chr(34);
					}
					$win .= ")";
				}
				else
					$win .= "'')";
				

				$dta=0;
				
				$query = "select ".$empresa."_000009.Precod,".$empresa."_000009.Preurl,".$empresa."_000009.Predes,".$empresa."_000009.prenod from ".$empresa."_000009 "; 
				$query .= " where ".$empresa."_000009.prenod = 'on' ";
				$query .= "   and ".$empresa."_000009.preest = 'on' ";
				$query .= " union all ";
				$query .= " select ".$empresa."_000009.Precod,".$empresa."_000009.Preurl,".$empresa."_000009.Predes,".$empresa."_000009.prenod from ".$empresa."_000009,".$empresa."_000020,".$empresa."_000021,".$empresa."_000037,".$empresa."_000001 "; 
				$query .= " where ".$empresa."_000009.prenod = 'off' ";
				$query .= "   and mid(".$empresa."_000009.Preurl,1,1) = 'F' ";
				$query .= "   and Preurl = CONCAT( 'F=', Encpro ) ";
				$query .= "   and ".$empresa."_000009.preest = 'on' ";
				$query .= "   and ".$empresa."_000020.Usucod = '".$key."' ";
				$query .= "   and ".$empresa."_000020.Usurol = ".$empresa."_000021.Rarcod ";
				if($CLASE == "I")
					$query .= "   and ".$empresa."_000021.Rarimp = 'on' ";
				else
					$query .= "   and ".$empresa."_000021.Rarcon = 'on' ";
				$query .= "   and ".$empresa."_000021.Rararb = ".$empresa."_000009.precod "; 
				$query .= "	  and ".$empresa."_000009.precod = ".$empresa."_000037.Forcod ";
				$query .= "	  and ".$empresa."_000037.Forser = '".$wservicio."' ";
				$query .= "	  and Encpro IN ".$win." ";
				$query .= " order by 1 ";
				
				
				// si el usuario es medico o enfermera en donde se está realizando la consulta no es necesario tener en cuenta el rol (query anterior)
				$usuarioHabilitado = false;
				if(isset($origenConsulta) && ($origenConsulta != $wemp_pmla))
				{
					// Consulta en donde si el usuario es médico o enfermera en hce_000019 para determinar si esta habilitado para consultar la historia clínica
					$usuarioHabilitado = consultarUsuarioHabilitado($conex,$origenConsulta,$key);
				}
				
				if($usuarioHabilitado)
				{
					$query = "select ".$empresa."_000009.Precod,".$empresa."_000009.Preurl,".$empresa."_000009.Predes,".$empresa."_000009.prenod from ".$empresa."_000009 "; 
					$query .= " where ".$empresa."_000009.prenod = 'on' ";
					$query .= "   and ".$empresa."_000009.preest = 'on' ";
					$query .= " union all ";
					$query .= " select ".$empresa."_000009.Precod,".$empresa."_000009.Preurl,".$empresa."_000009.Predes,".$empresa."_000009.prenod from ".$empresa."_000009,".$empresa."_000001,".$empresa."_000037 "; 
					$query .= " where ".$empresa."_000009.prenod = 'off' ";
					$query .= "   and mid(".$empresa."_000009.Preurl,1,1) = 'F' ";
					$query .= "   and Preurl = CONCAT( 'F=', Encpro ) ";
					$query .= "   and ".$empresa."_000009.preest = 'on' ";
					$query .= "	  and ".$empresa."_000009.precod = ".$empresa."_000037.Forcod ";
					$query .= "	  and ".$empresa."_000037.Forser = '".$wservicio."' ";
					$query .= "	  and Encpro IN ".$win." ";
					$query .= " order by 1 ";
				}
				
				// echo "<pre>".print_r($query,true)."</pre>";
				$err = mysql_query($query,$conex) or die("aqui ".mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);
				include_once("hce/especial.php");
				
				if($num > 0)
				{
					$fil=ceil($num / 4);
					$data=array();
					$dta=1;
					for ($i=0;$i<$num;$i++)
					{
						$row = mysql_fetch_array($err);
						$data[$i][0]=$row[1];
						$data[$i][1]=$row[2];
						$data[$i][2]=$row[3];
						$data[$i][5]= 0;
						$pos=bi($vistas,$numvistas,$row[0]);
						if($pos != -1)
							$data[$i][3]=1;
						else
							$data[$i][3]=0;
						$data[$i][4]=$row[0];
						
						// si el usuario es medico o enfermera en donde se está realizando la consulta no es necesario tener en cuenta el rol (query hce_000021)
						if($usuarioHabilitado)
						{
							$data[$i][3]=1;
						}
					}
					
					for ($i=0;$i<$num;$i++)
					{
						$wb = $num - ($i + 1);
						$wbaux = $wb;
						if($data[$wb][2] == "on")
						{
							while($data[$wbaux][2] == "on" and $wbaux < ($num - 1))
								$wbaux++;
							if(($wbaux < ($num - 1) and strpos(substr($data[$wbaux][4],0,strlen($data[$wb][4])),$data[$wb][4]) === false) or $wbaux == ($num - 1))
								$data[$wb][0] = "NO";
						}
					}
					$numFinal=-1;
					$dataaux=array();
					for ($i=0;$i<$num;$i++)
					{
						if($data[$i][0] != "NO")
						{
							$numFinal++;
							$dataaux[$numFinal][0]=$data[$i][0];
							$dataaux[$numFinal][1]=$data[$i][1];
							$dataaux[$numFinal][2]=$data[$i][2];
							$dataaux[$numFinal][3]=$data[$i][3];
							$dataaux[$numFinal][4]=$data[$i][4];
							$dataaux[$numFinal][5]=$data[$i][5];
						}
					}
					$fil=ceil(($numFinal+1) / 4);
					$data=array();
					for ($i=0;$i<=$numFinal;$i++)
					{
						$data[$i][0]=$dataaux[$i][0];
						$data[$i][1]=$dataaux[$i][1];
						$data[$i][2]=$dataaux[$i][2];
						$data[$i][3]=$dataaux[$i][3];
						$data[$i][4]=$dataaux[$i][4];
						$data[$i][5]=$dataaux[$i][5];
					}
				}
			}
			
			$programasAnexos = consultarScripts($conex,$empresa,$whis,$wing);
			$cadenaProgramasAnexos="";
			if(count($programasAnexos)>0)
			{
				for($i=0;$i<count($programasAnexos);$i++)
				{
					if($programasAnexos[$i][0]!="")
					{
						$cadenaProgramasAnexos .= $programasAnexos[$i][0]."|";
					}
				}
			}
			
			if(count($programasAnexos)>0)
			{
				$dta = 1;
				$data = array_merge($data, $programasAnexos);
				$numFinal = $numFinal+count($programasAnexos);
				$fil=ceil(($numFinal+1) / 4);
			}
			
			$cadenaPosicionesProgramasAnexos = "";
			if($dta == 1)
			{
				if($fil>0)
				{
					for ($i=0;$i<$fil;$i++)
					{
						echo "<tr>";
						for ($j=0;$j<4;$j++)
						{
							$exp=$i+($fil*$j);
							if(isset($data[$exp][0]))
							{
								if($data[$exp][2] == "off")
								{
									$color="tipoTI04";
									if($data[$exp][3] == 1)
									{
										if((isset($imp[$exp]) or isset($all)) and !isset($noall))
											echo "<td id=".$color."><input type='checkbox' name='imp[".$exp."]' checked></td>";
										else
											echo "<td id=".$color."><input type='checkbox' name='imp[".$exp."]'></td>";
										echo "<td id=".$color.">".$data[$exp][1]."</td>";
										
										if(strpos($cadenaProgramasAnexos, $data[$exp][0])!==false)
										{
											$cadenaPosicionesProgramasAnexos .= $exp."|";
										}
										
									}
									else
									{
										echo "<td id=".$color."></td>";
										echo "<td id=".$color."></td>";
									}
									echo "<input type='HIDDEN' name= 'data[".$exp."][0]' value=".$data[$exp][0].">";
									echo "<input type='HIDDEN' name= 'data[".$exp."][1]' value=".$data[$exp][1].">";
									echo "<input type='HIDDEN' name= 'data[".$exp."][5]' value=".$data[$exp][5].">";
								}
								else
								{
									$color="tipoTI03";
									echo "<td id=".$color."></td>";
									echo "<td id=".$color.">".$data[$exp][1]."</td>";
								}
							}
							else
							{
								$color="tipoTI04";
								echo "<td id=".$color."></td>";
								echo "<td id=".$color."></td>";
							}
						}
						echo "</tr>";
					}
				}
				else
				{
					$color="tipoTI03";
					echo "<td id=".$color." colspan='8' align='center'>El paciente no tiene formularios diligenciados.</td>";
				}
				
				echo "<input type='HIDDEN' name= 'num' value=".$num.">";
				echo "<input type='HIDDEN' name= 'wcedula' value=".$wcedula.">";
				echo "<input type='HIDDEN' name= 'wtipodoc' value=".$wtipodoc.">";
				echo "<input type='HIDDEN' id= 'cadenaProgramasAnexos' name= 'cadenaProgramasAnexos' value=".$cadenaProgramasAnexos.">";
				echo "<input type='HIDDEN' id= 'cadenaPosicionesProgramasAnexos' name= 'cadenaPosicionesProgramasAnexos' value=".$cadenaPosicionesProgramasAnexos.">";
				echo "<input type='HIDDEN' id= 'whistoriaPac' name= 'whistoriaPac' value=".$whis.">";
				echo "<input type='HIDDEN' id= 'wingresoPac' name= 'wingresoPac' value=".$wing.">";
				echo "<input type='HIDDEN' id= 'wemp_pmla' name= 'wemp_pmla' value=".$wemp_pmla.">";
				echo "<input type='HIDDEN' id= 'htmlProgramasAnexos' name= 'htmlProgramasAnexos' value=''>";
				echo "<tr><td class=tipo3GRID colspan=8>CONSULTAR<input type='checkbox' id='ok' name='ok'></font></td></tr>";
				echo "<tr><td id=tipoTI01 colspan=8><IMG SRC='/matrix/images/medical/hce/consultar.png' id='logook' style='vertical-align:middle;' OnClick='enterOK()'></td>";
				echo "</table><br><br>"; 
			}
		}
		else
		{
			$color="tipoTI04";
			$color1="tipoTI06";
			$color2="tipoTI03";
			$data=array();
			$query  = "select Pdicod,Pdides,Pdifor from ".$empresa."_000043 ";
			$query .= "   where Pdiest= 'on' ";
			$query .= "   order by 1";
			$err = mysql_query($query,$conex) or die("aca ".mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);
			if ($num>0)
			{
				echo "<table border=0 align=center>";
				echo "<tr><td id=tipoTI01 colspan=2>PAQUETES DE IMPRESION HISTORIA CLINICA ELECTRONICA Ver. 2022-03-11<td></tr>";
				echo "<tr><td id=tipoTI05 colspan=2>Fecha Inicial <input type='TEXT' name='wfechai' size=10 maxlength=10 id='wfechai' readonly='readonly' value=".$wfechai." class=tipo6>&nbsp;&nbsp;&nbsp;<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger1'>";
				?>
				<script type="text/javascript">//<![CDATA[
					Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfechai',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
				//]]></script>
				<?php
				echo "&nbsp;&nbsp;&nbsp;Fecha Final <input type='TEXT' name='wfechaf' size=10 maxlength=10 id='wfechaf' readonly='readonly' value=".$wfechaf." class=tipo6>&nbsp;&nbsp;&nbsp;<IMG SRC='/matrix/images/medical/TCX/calendario.jpg' id='trigger2'></td>";
				?>
				<script type="text/javascript">//<![CDATA[
					Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfechaf',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
				//]]></script>
				<?php
				echo "</td></tr>";
				echo "<tr><td id=tipoTI02>SELECCION</td><td id=tipoTI02>DESCIPCION</td></tr>";
				for ($i=0;$i<$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$data[$i][0] = $row[0];
					$data[$i][1] = $row[2];
					echo "<input type='HIDDEN' name= 'data[".$i."][0]' value=".$data[$i][0].">";
					echo "<input type='HIDDEN' name= 'data[".$i."][1]' value=".$data[$i][1].">";
					$formas = explode("-",$row[2]);
					$en = "";
					for ($j=0;$j<count($formas);$j++)
					{
						if($j > 0)
							$en .= ",";
						$en .= "'".$formas[$j]."'";
					}
					$query  = "select Encpro,Encdes from ".$empresa."_000001 ";
					$query .= "   where Encpro in (".$en.") ";
					$query .= "   order by 1";
					$err1 = mysql_query($query,$conex) or die("aca ".mysql_errno().":".mysql_error());
					$num1 = mysql_num_rows($err1);
					echo "<tr><td id=".$color."><input type='RADIO' name='paq' value=".$i." onclick='enter()'></td><td id=".$color.">".$row[1]."</td></tr>";
					for ($j=0;$j<$num1;$j++)
					{
						$row1 = mysql_fetch_array($err1);
						$w = $j + 1;
						echo "<tr><td id=".$color2.">".$w."</td><td id=".$color1.">".$row1[1]."</td></tr>";
					}
				}
				echo "<tr><td id=tipoTI07 colspan=2><td></tr>";
				echo "</table><br><br>"; 
				echo "<input type='HIDDEN' name= 'wcedula' value=".$wcedula.">";
				echo "<input type='HIDDEN' name= 'wtipodoc' value=".$wtipodoc.">";	
				echo "<input type='HIDDEN' name= 'ok' value='1'>";		
			}
		}
	}
	else
	{
		if($protocolos == "0")
		{
			//                 0      1      2      3      4      5      6      7      8      9      10     11
			$query = "select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing,Ingnre,Ubisac,Ubihac,Cconom from root_000036,root_000037,".$wdbmhos."_000016,".$wdbmhos."_000018,".$wdbmhos."_000011 ";
			$query .= " where pacced = '".$wcedula."'";
			$query .= "   and pactid = '".$wtipodoc."'";
			$query .= "   and pacced = oriced ";
			$query .= "   and pactid = oritid ";
			$query .= "   and oriori = '".$wemp_pmla."'";
			$query .= "   and inghis = orihis ";
			if(!isset($wing))
				$query .= "   and inging = oriing ";
			else
				$query .= "   and inging = '".$wing."' ";
			$query .= "   and ubihis = inghis "; 
			$query .= "   and ubiing = inging ";
			$query .= "   and ccocod = ubisac ";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			$row = mysql_fetch_array($err);
			$wsex="M";
			$sexo="MASCULINO";
			if($row[5] == "F")
			{
				$sexo="FEMENINO";
				$wsex="F";
			}
			// $ann=(integer)substr($row[4],0,4)*360 +(integer)substr($row[4],5,2)*30 + (integer)substr($row[4],8,2);
			// $aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
			// $ann1=($aa - $ann)/360;
			// $meses=(($aa - $ann) % 360)/30;
			// if ($ann1<1)
			// {
				// $dias1=(($aa - $ann) % 360) % 30;
				// $wedad=(string)(integer)$meses." Meses ".(string)$dias1." Dias";	
			// }
			// else
			// {
				// $dias1=(($aa - $ann) % 360) % 30;
				// $wedad=(string)(integer)$ann1." A&ntilde;os ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
			// }
			$wedad = calcularEdadPaciente($row[4]);
			$wpac = $wtipodoc." ".$wcedula."<br>".$row[0]." ".$row[1]." ".$row[2]." ".$row[3];
			$nombrePaciente = $row[0]." ".$row[1]." ".$row[2]." ".$row[3];
			$nombreEntidad = $row[8];
			$whis=$row[6];
			if(!isset($wing))
				$wing=$row[7];
			$dia=array();
			$dia["Mon"]="Lun";
			$dia["Tue"]="Mar";
			$dia["Wed"]="Mie";
			$dia["Thu"]="Jue";
			$dia["Fri"]="Vie";
			$dia["Sat"]="Sab";
			$dia["Sun"]="Dom";
			$mes["Jan"]="Ene";
			$mes["Feb"]="Feb";
			$mes["Mar"]="Mar";
			$mes["Apr"]="Abr";
			$mes["May"]="May";
			$mes["Jun"]="Jun";
			$mes["Jul"]="Jul";
			$mes["Aug"]="Ago";
			$mes["Sep"]="Sep";
			$mes["Oct"]="Oct";
			$mes["Nov"]="Nov";
			$mes["Dec"]="Dic";
			$fechal=strftime("%a %d de %b del %Y");
			$fechal=$dia[substr($fechal,0,3)].substr($fechal,3);
			$fechal=substr($fechal,0,10).$mes[substr($fechal,10,3)].substr($fechal,13);
			$color="#dddddd";
			$color1="#C3D9FF";
			$color2="#E8EEF7";
			$color3="#CC99FF";
			$color4="#99CCFF";
			if(!isset($wing))
				$wintitulo="Historia:".$row[6]." Ingreso:".$row[7]." Paciente:".$wpac;
			else
				$wintitulo="Historia:".$row[6]." Ingreso:".$wing." Paciente:".$wpac;
			$Hgraficas=" |";
			echo "<input type='HIDDEN' name= 'wcedula' value=".$wcedula.">";
			echo "<input type='HIDDEN' name= 'wtipodoc' value=".$wtipodoc.">";
			echo "<input type='submit' value='RETORNAR' onClick='enter()'>";
			if($noCentrar=="" || $noCentrar=="false")
			{
				echo "<center>";
			}
			echo "<table border=1 width='712' class=tipoTABLE1>";
			echo "<tr><td rowspan=3 align=center><IMG SRC='/MATRIX/images/medical/root/HCE".$wemp_pmla.".jpg' id='logo'></td>";	
			echo "<td id=tipoL01C>Paciente</td><td colspan=4 id=tipoL04>".$wpac."</td><td id=tipoL04A>P&aacute;gina 1</td></tr>";
			echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$whis."-".$wing."</td><td id=tipoL01>Edad actual</td><td id=tipoL02C>".$wedad."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".$sexo."</td></tr>";
			echo "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row[11]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$row[10]."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$row[8]."</td></tr>";
			echo "</table><br>";
			if($noCentrar=="" || $noCentrar=="false")
			{
				echo "</center>";
			}
			$en="";
			$queryI="";
			$nrofor=-1;
			$cadenaProgramasAnexos = "";
			if($wespecial != "TODAS")
			{
				$query = "DROP TABLE IF EXISTS TESPECIAL".$key.";";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				// $query = "CREATE TABLE if not exists TESPECIAL".$key." as ";
				//2020-10-29
				$query = "CREATE TABLE if not exists TESPECIAL".$key." as ";
				$query .= " select Firusu as usuario from ".$empresa."_000036, ".$wdbmhos."_000048  ";
				$query .= "   where Firhis = '".$whis."' ";
				$query .= " 	and Firing = '".$wing."' ";
				$query .= " 	and Firusu = Meduma ";
				$query .= " 	and Medesp = '".substr($wespecial,0,strpos($wespecial,"-"))."'";
				$query .= "  group by 1 ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				
				$query = "CREATE UNIQUE INDEX claveE on TESPECIAL".$key." (usuario(8))";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
			}
			for ($i=0;$i<$num;$i++)
			{
				if(isset($imp[$i]))
				{
					if(substr($data[$i][0],0,2)=="F=")
					{
						if(validar_formulario($data[$i][0],$num,$data))
						{
							$nrofor++;
							if($nrofor > 0)
								$en .= ",";
							$en .= "'".substr($data[$i][0],2)."'";
							if($nrofor > 0)
								$queryI .= " UNION ALL ";
							if($wespecial == "TODAS")
							{
								//                                        0                                              1                          2                                                3                                                  4                           5                          6                           7                          8                          9                          10                       11                                              12                         13                         14                         15                         16                         17                         18                         19
								$queryI .= " select ".$empresa."_000002.Detdes,".$empresa."_".substr($data[$i][0],2).".movdat,".$empresa."_000002.Detorp,".$empresa."_".substr($data[$i][0],2).".fecha_data,".$empresa."_".substr($data[$i][0],2).".Hora_data,".$empresa."_000002.Dettip,".$empresa."_000002.Detcon,".$empresa."_000002.Detpro,".$empresa."_000001.Encdes,".$empresa."_000002.Detarc,".$empresa."_000002.Detume,".$empresa."_000002.Detimp,".$empresa."_".substr($data[$i][0],2).".movusu,".$empresa."_000001.Encsca,".$empresa."_000001.Encoim,".$empresa."_000002.Dettta,".$empresa."_000002.Detfor,".$empresa."_000001.Encfir,".$empresa."_000002.Detimc,".$empresa."_000002.Detccu from ".$empresa."_".substr($data[$i][0],2).",".$empresa."_000002,".$empresa."_000001 ";
								$queryI .= " where ".$empresa."_".substr($data[$i][0],2).".movpro='".substr($data[$i][0],2)."' "; 
								$queryI .= "   and ".$empresa."_".substr($data[$i][0],2).".movhis='".$whis."' ";
								$queryI .= "   and ".$empresa."_".substr($data[$i][0],2).".moving='".$wing."' ";
								$queryI .= "   and ".$empresa."_".substr($data[$i][0],2).".fecha_data between '".$wfechai."' and '".$wfechaf."' "; 
								$queryI .= "   and ".$empresa."_".substr($data[$i][0],2).".movpro=".$empresa."_000002.detpro ";
								$queryI .= "   and ".$empresa."_".substr($data[$i][0],2).".movcon = ".$empresa."_000002.detcon ";
								//$queryI .= "   and ".$empresa."_000002.detest='on' "; 
								if($CLASE == "C")
									$queryI .= "   and ".$empresa."_000002.detvim in ('A','C') "; 
								else
									$queryI .= "   and ".$empresa."_000002.detvim in ('A','I') "; 
								$queryI .= "   and ".$empresa."_000002.Dettip != 'Titulo' "; 
								$queryI .= "   and ".$empresa."_000002.Detpro = ".$empresa."_000001.Encpro "; 
							}
							else
							{
								//                                        0                                              1                          2                                                3                                                  4                           5                          6                           7                          8                          9                          10                       11                                              12                         13                         14                         15                         16                         17                         18                        19
								$queryI .= " select ".$empresa."_000002.Detdes,".$empresa."_".substr($data[$i][0],2).".movdat,".$empresa."_000002.Detorp,".$empresa."_".substr($data[$i][0],2).".fecha_data,".$empresa."_".substr($data[$i][0],2).".Hora_data,".$empresa."_000002.Dettip,".$empresa."_000002.Detcon,".$empresa."_000002.Detpro,".$empresa."_000001.Encdes,".$empresa."_000002.Detarc,".$empresa."_000002.Detume,".$empresa."_000002.Detimp,".$empresa."_".substr($data[$i][0],2).".movusu,".$empresa."_000001.Encsca,".$empresa."_000001.Encoim,".$empresa."_000002.Dettta,".$empresa."_000002.Detfor,".$empresa."_000001.Encfir,".$empresa."_000002.Detimc,".$empresa."_000002.Detccu from ".$empresa."_".substr($data[$i][0],2).",".$empresa."_000002,".$empresa."_000001,TESPECIAL".$key;
								$queryI .= " where ".$empresa."_".substr($data[$i][0],2).".movpro='".substr($data[$i][0],2)."' "; 
								$queryI .= "   and ".$empresa."_".substr($data[$i][0],2).".movhis='".$whis."' ";
								$queryI .= "   and ".$empresa."_".substr($data[$i][0],2).".moving='".$wing."' ";
								$queryI .= "   and ".$empresa."_".substr($data[$i][0],2).".fecha_data between '".$wfechai."' and '".$wfechaf."' "; 
								$queryI .= "   and ".$empresa."_".substr($data[$i][0],2).".movpro=".$empresa."_000002.detpro ";
								$queryI .= "   and ".$empresa."_".substr($data[$i][0],2).".movcon = ".$empresa."_000002.detcon ";
								//$queryI .= "   and ".$empresa."_000002.detest='on' "; 
								if($CLASE == "C")
									$queryI .= "   and ".$empresa."_000002.detvim in ('A','C') "; 
								else
									$queryI .= "   and ".$empresa."_000002.detvim in ('A','I') ";  
								$queryI .= "   and ".$empresa."_000002.Dettip != 'Titulo' "; 
								$queryI .= "   and ".$empresa."_000002.Detpro = ".$empresa."_000001.Encpro "; 
								$queryI .= "   and ".$empresa."_".substr($data[$i][0],2).".movusu = TESPECIAL".$key.".usuario ";
							}
						}
					}
					else
					{
						$cadenaProgramasAnexos .= $data[$i][0]."|";
					}
						
					
				}
			}
			if($CLASE == "C")
			{
				// imprimir($conex,&$empresa,$wdbmhos,$origen,&$queryI,&$whis,&$wing,&$key,&$en,&$wintitulo,&$Hgraficas,&$CLASE,&$wsex,0);
				
				if($cadenaProgramasAnexos!="" && $nrofor==-1)
				{
					imprimirProgramasAnexos($cadenaProgramasAnexos,$whis,$wing,$wemp_pmla);
				}
				elseif($cadenaProgramasAnexos!="")
				{
					imprimir($conex,$empresa,$wdbmhos,$wemp_pmla,$queryI,$whis,$wing,$key,$en,$wintitulo,$Hgraficas,$CLASE,$wsex,0);
					imprimirProgramasAnexos($cadenaProgramasAnexos,$whis,$wing,$wemp_pmla);
				}
				else
				{
					imprimir($conex,$empresa,$wdbmhos,$wemp_pmla,$queryI,$whis,$wing,$key,$en,$wintitulo,$Hgraficas,$CLASE,$wsex,0);
				}
			}
			else
			{
				$whtml=0;
				//$CLASE = "I"; 					//	Para que entre otras cosas, imprima los colores en el pdf
				$wnombrePDF=$key.$whis.$wing; 		//  El archivo pdf se llamara pdfconsulta.pdf, estara ubicado en matrix/hce/reportes/cenimp/
				$wllevaTapa = "on"; 				// 'on' 'off' ==> En la primera pagina se imprime informacion del paciente y el usuario que solicito el pdf
				$wllevaLogo = "on"; 				// 'on' 'off' ==> Muestra el logotipo de la clinica en el encabezado de la primera pagina
				$mostrarObjectPdf="on";			// 'on' 'off' ==>Para que haga echo a un object con el pdf generado 
				$wseparaFormularios = "on";			//Salto de pagina al cambiar el formulario
				
				if(isset($enviarCorreo) && $enviarCorreo=="on")
				{
					$nombreEmpresa = consultarAliasPorAplicacionHCE($conex,$wemp_pmla,"nombreEmpresa");
					$dir = "../reportes/cenimp";
					$archivoPdf = $dir."/".$wnombrePDF.".pdf";
					
					echo "	<p align='center'><input type='button' id='btnEnviarPdf' onclick='enviarPdf(\"".$origen."\",\"".$whis."\",\"".$wing."\",\"".$dir."\",\"".$wnombrePDF.".pdf"."\",\"".$nombrePaciente."\",\"".$nombreEmpresa."\",\"".$wdbmhos."\",\"".$key."\",\"".$nombreEntidad."\");' value='Enviar PDF'></p>";
					echo "	<div id='msjEspere' align='center' style='display:none;'>
								<img src='../../images/medical/ajax-loader5.gif'/>Por favor espere un momento...<br><br>
							</div>";
				}
				
				// $respuesta = construirPDF($conex,&$empresa,&$wdbmhos,&$origen,&$queryI,&$whis,&$wing,&$key,&$en,&$wintitulo,&$Hgraficas,&$CLASE,&$wsex,$whtml,$wnombrePDF, $wllevaTapa, $wllevaLogo,$mostrarObjectPdf);
				$respuesta = construirPDF($conex,$empresa,$wdbmhos,$wemp_pmla,$queryI,$whis,$wing,$key,$en,$wintitulo,$Hgraficas,$CLASE,$wsex,$whtml,$wnombrePDF, $wllevaTapa, $wllevaLogo,$mostrarObjectPdf,$wseparaFormularios,"","",$htmlProgramasAnexos);
			}
			$query = "DROP TABLE IF EXISTS TESPECIAL".$key.";";
			$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
		}
		else
		{
			$paquetes = explode("-",$data[$paq][1]);
			for ($i=0;$i<count($paquetes);$i++)
			{
					//                 0      1      2      3      4      5      6      7      8      9      10     11
				$query = "select Pacno1,Pacno2,Pacap1,Pacap2,Pacnac,Pacsex,Orihis,Oriing,Ingnre,Ubisac,Ubihac,Cconom from root_000036,root_000037,".$wdbmhos."_000016,".$wdbmhos."_000018,".$wdbmhos."_000011 ";
				$query .= " where pacced = '".$wcedula."'";
				$query .= "   and pactid = '".$wtipodoc."'";
				$query .= "   and  pacced = oriced ";
				$query .= "   and  pactid = oritid ";
				$query .= "   and oriori = '".$wemp_pmla."'";
				$query .= "   and inghis = orihis ";
				$query .= "   and  inging = oriing ";
				$query .= "   and ubihis = inghis "; 
				$query .= "   and ubiing = inging ";
				$query .= "   and ccocod = ubisac ";
				$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
				$row = mysql_fetch_array($err);
				$wsex="M";
				$sexo="MASCULINO";
				if($row[5] == "F")
				{
					$sexo="FEMENINO";
					$wsex="F";
				}
				// $ann=(integer)substr($row[4],0,4)*360 +(integer)substr($row[4],5,2)*30 + (integer)substr($row[4],8,2);
				// $aa=(integer)date("Y")*360 +(integer)date("m")*30 + (integer)date("d");
				// $ann1=($aa - $ann)/360;
				// $meses=(($aa - $ann) % 360)/30;
				// if ($ann1<1)
				// {
					// $dias1=(($aa - $ann) % 360) % 30;
					// $wedad=(string)(integer)$meses." Meses ".(string)$dias1." Dias";	
				// }
				// else
				// {
					// $dias1=(($aa - $ann) % 360) % 30;
					// $wedad=(string)(integer)$ann1." A&ntilde;os ".(string)(integer)$meses." Meses ".(string)$dias1." Dias";
				// }
				$wedad = calcularEdadPaciente($row[4]);
				$wpac = $wtipodoc." ".$wcedula."<br>".$row[0]." ".$row[1]." ".$row[2]." ".$row[3];
				$whis=$row[6];
				if(!isset($wing))
					$wing=$row[7];
				$dia=array();
				$dia["Mon"]="Lun";
				$dia["Tue"]="Mar";
				$dia["Wed"]="Mie";
				$dia["Thu"]="Jue";
				$dia["Fri"]="Vie";
				$dia["Sat"]="Sab";
				$dia["Sun"]="Dom";
				$mes["Jan"]="Ene";
				$mes["Feb"]="Feb";
				$mes["Mar"]="Mar";
				$mes["Apr"]="Abr";
				$mes["May"]="May";
				$mes["Jun"]="Jun";
				$mes["Jul"]="Jul";
				$mes["Aug"]="Ago";
				$mes["Sep"]="Sep";
				$mes["Oct"]="Oct";
				$mes["Nov"]="Nov";
				$mes["Dec"]="Dic";
				$fechal=strftime("%a %d de %b del %Y");
				$fechal=$dia[substr($fechal,0,3)].substr($fechal,3);
				$fechal=substr($fechal,0,10).$mes[substr($fechal,10,3)].substr($fechal,13);
				$color="#dddddd";
				$color1="#C3D9FF";
				$color2="#E8EEF7";
				$color3="#CC99FF";
				$color4="#99CCFF";
				if(!isset($wing))
					$wintitulo="Historia:".$row[6]." Ingreso:".$row[7]." Paciente:".$wpac;
				else
					$wintitulo="Historia:".$row[6]." Ingreso:".$wing." Paciente:".$wpac;
				$Hgraficas=" |";
					//                        0 
				$queryI  = " select count(*) from ".$empresa."_".$paquetes[$i]." ";
				$queryI .= " where ".$empresa."_".$paquetes[$i].".movpro='".$paquetes[$i]."' "; 
				$queryI .= "   and ".$empresa."_".$paquetes[$i].".movhis='".$whis."' ";
				$queryI .= "   and ".$empresa."_".$paquetes[$i].".moving='".$wing."' ";
				$queryI .= "   and ".$empresa."_".$paquetes[$i].".fecha_data between '".$wfechai."' and '".$wfechaf."' "; 
				$err1 = mysql_query($queryI,$conex) or die(mysql_errno().":".mysql_error());
				$row1 = mysql_fetch_array($err1);
				if($row1[0] > 0)
				{
					echo "<table border=1 width='712' class=tipoTABLE1>";
					echo "<tr><td rowspan=3 align=center><IMG SRC='/MATRIX/images/medical/root/HCE".$wemp_pmla.".jpg' id='logo'></td>";	
					echo "<td id=tipoL01C>Paciente</td><td colspan=4 id=tipoL04>".$wpac."</td><td id=tipoL04A>P&aacute;gina 1</td></tr>";
					echo "<tr><td id=tipoL01C>Historia Clinica</td><td id=tipoL02C>".$whis."-".$wing."</td><td id=tipoL01>Edad actual</td><td id=tipoL02C>".$wedad."</td><td id=tipoL01C>Sexo</td><td id=tipoL02C>".$sexo."</td></tr>";
					echo "<tr><td id=tipoL01C>Servicio</td><td id=tipoL02C>".$row[11]."</td><td id=tipoL01C>Habitacion</td><td id=tipoL02C>".$row[10]."</td><td id=tipoL01C>Entidad</td><td id=tipoL02C>".$row[8]."</td></tr>";
					echo "</table>";
					$en="";
					$en .= "'".$paquetes[$i]."'";
				
					//                                        0                                     1                          2                                                3                                                  4                           5                          6                           7                          8                          9                          10                       11                                              12                         13                         14                         15                         16                         17
					$queryI  = " select ".$empresa."_000002.Detdes,".$empresa."_".$paquetes[$i].".movdat,".$empresa."_000002.Detorp,".$empresa."_".$paquetes[$i].".fecha_data,".$empresa."_".$paquetes[$i].".Hora_data,".$empresa."_000002.Dettip,".$empresa."_000002.Detcon,".$empresa."_000002.Detpro,".$empresa."_000001.Encdes,".$empresa."_000002.Detarc,".$empresa."_000002.Detume,".$empresa."_000002.Detimp,".$empresa."_".$paquetes[$i].".movusu,".$empresa."_000001.Encsca,".$empresa."_000001.Encoim,".$empresa."_000002.Dettta,".$empresa."_000002.Detfor,".$empresa."_000001.Encfir from ".$empresa."_".$paquetes[$i].",".$empresa."_000002,".$empresa."_000001 ";
					$queryI .= " where ".$empresa."_".$paquetes[$i].".movpro='".$paquetes[$i]."' "; 
					$queryI .= "   and ".$empresa."_".$paquetes[$i].".movhis='".$whis."' ";
					$queryI .= "   and ".$empresa."_".$paquetes[$i].".moving='".$wing."' ";
					$queryI .= "   and ".$empresa."_".$paquetes[$i].".fecha_data between '".$wfechai."' and '".$wfechaf."' "; 
					$queryI .= "   and ".$empresa."_".$paquetes[$i].".movpro=".$empresa."_000002.detpro ";
					$queryI .= "   and ".$empresa."_".$paquetes[$i].".movcon = ".$empresa."_000002.detcon ";
					//$queryI .= "   and ".$empresa."_000002.detest='on' "; 
					$queryI .= "   and ".$empresa."_000002.detvim='on' "; 
					$queryI .= "   and ".$empresa."_000002.Dettip != 'Titulo' "; 
					$queryI .= "   and ".$empresa."_000002.Detpro = ".$empresa."_000001.Encpro "; 
					
					imprimir($conex,$empresa,$wdbmhos,$wemp_pmla,$queryI,$whis,$wing,$key,$en,$wintitulo,$Hgraficas,$CLASE,$wsex,0);
					echo "<div class='saltopagina'></div>";
				}
			}
		}
	}
	
	mysql_close( $conex );
}
?>
