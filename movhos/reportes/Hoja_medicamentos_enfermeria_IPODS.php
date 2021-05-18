<?php
include_once("conex.php");
/**
 *
 */
 
function consultarImprimeMedicamentoOriginal($conex,$wcenmez,$codigoArticulo)
{
	$query = "SELECT Arttip
				FROM ".$wcenmez."_000002, ".$wcenmez."_000001 
			   WHERE Artcod='".$codigoArticulo."'
				 AND Tipcod=Arttip
				 AND Tipimo='on';";
	
	$res = mysql_query($query, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $query . " - " . mysql_error());		   
	$num = mysql_num_rows($res);
	
	$imprimeMedicamentoOriginal = false;
	if($num>0)
	{
		$imprimeMedicamentoOriginal = true;
	}
	
	return $imprimeMedicamentoOriginal;
} 

function esArticuloGenerico( $conex,$wbasedato,$wcenmez,$codigoArticulo )
{
	$sql = "SELECT Arkcod
			  FROM ".$wbasedato."_000068,".$wcenmez."_000002,".$wcenmez."_000001
		 	 WHERE arkcod = '".$codigoArticulo."'
			   AND artcod = arkcod
			   AND arttip = tipcod
			   AND tiptpr = arktip
			   AND artest = 'on'
			   AND arkest = 'on'
			   AND tipest = 'on';";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno(). " - Error en el query - ".mysql_errno()  );
	$numrows = mysql_num_rows( $res );
	
	$esGenerico = false;
	if( $numrows > 0 )
	{
		$esGenerico = true;
	}
	
	return $esGenerico;
}

function consultarNombreArticulo($conex,$wbasedato,$codigoArticulo)
{
	$queryArticulo = " SELECT Artcom, Artgen 
						 FROM ".$wbasedato."_000026 
						WHERE Artcod='".$codigoArticulo."';";
	
	$resArticulo = mysql_query($queryArticulo, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryArticulo . " - " . mysql_error());		   
	$numArticulo = mysql_num_rows($resArticulo);
	
	$arrayArticulo = array();
	if($numArticulo>0)
	{
		$rowArticulo = mysql_fetch_array($resArticulo);
		
		$arrayArticulo['comercial'] = $rowArticulo['Artcom'];
		$arrayArticulo['generico'] = $rowArticulo['Artgen'];
	}
	
	return $arrayArticulo;
}
function consultarMedicamentoProductoCM($conex,$wbasedato,$wcenmez,$historia,$ingreso,$codigoArticulo)
{
	// Se obtiene el artículo anterior (antes del reemplazo del producto) del primer registro donde se encuentre el producto de 
	// central de mezclas. No hay relación con el ido ya que el artículo puede estar ordenado varias veces para el mismo día 
	// (varios ido) dañando la estructura de la hoja de medicamentos.
	$queryArtOriginal = " SELECT Kadaan 
							FROM ".$wbasedato."_000054 
						   WHERE Kadhis='".$historia."' 
						     AND Kading='".$ingreso."' 
							 AND Kadart='".$codigoArticulo."' 
							 AND Kadaan!='' 
						ORDER BY Kadfec LIMIT 1;";
	
	$resArtOriginal = mysql_query($queryArtOriginal, $conex) or die ("Error: " . mysql_errno() . " - en el query: " . $queryArtOriginal . " - " . mysql_error());		   
	$numArtOriginal = mysql_num_rows($resArtOriginal);
	
	$arrayMedicamentoOriginal = array();
	if($numArtOriginal>0)
	{
		$rowArtOriginal = mysql_fetch_array($resArtOriginal);
		
		$articuloAnterior = explode(",",$rowArtOriginal['Kadaan']);
		
		$esGenerico = esArticuloGenerico($conex,$wbasedato,$wcenmez,$articuloAnterior[0]);
		
		// si no es un articulo generico debe mostrar el nombre del articulo anterior al reemplazo
		if(!$esGenerico)
		{
			$arrayMedicamentoOriginal = consultarNombreArticulo($conex,$wbasedato,$articuloAnterior[0]);
		}
	}
	
	return $arrayMedicamentoOriginal;
}

function consultarVia( $conex, $wbasedato, $cod ){
	
	$val = "";
	
	$sql = "SELECT Viades 
			  FROM ".$wbasedato."_000040 
			 WHERE Viacod =  '".$cod."'";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_error()." - Error en el query $sql - ".mysql_error() );
	
	if( $row = mysql_fetch_array( $res ) ){
		$val = $row[ 'Viades' ];
	}
	
	return $val;
}

/**
 * función sumaDiasAFecha($fecha,$dias), suma n días a partir de una fecha y hora.
 *
 * @param fecha $fecha : Fecha de la forma aaaa-mm-dd
 * @param número $dias : Cantidad de días a sumar a partir de la fecha que llega por parámetros
 * @param número $horas: Horas de la forma hh:mm:ss
 * @return fecha
 */
function sumaDiasAFecha($fecha,$dias,$accion='',$horas='')
{
    // $explode1 = explode(' ',$fecha);
    $explode1f = explode('-',$fecha);

    $explode1h = array(0=>0,1=>0,2=>0);
    if ($horas != '')
    {
        $explode1h = explode(':',$horas);
    }

    $mk = mktime($explode1h[0],$explode1h[1],$explode1h[2],$explode1f[1],$explode1f[2],$explode1f[0]);
    $nueva = ($accion == 'resta') ? ($mk-($dias * 24 * 60 * 60)) : ($mk+($dias * 24 * 60 * 60));
    $fecha_aumentada=date("Y-m-d",$nueva);
    return $fecha_aumentada;
}
/*************************************************************************************************************/

/**
 * función diasEntreFechas($fecha1, $fecha2), calcula la diferencia en días entre dos fechas
 *
 * @param fecha $fecha1 : Fecha completa, aaaa-mm-dd hh:mm:ss
 * @param fecha $fecha2 : Fecha completa, aaaa-mm-dd hh:mm:ss
 * @return número
 */
function diasEntreFechas($fecha1, $fecha2)
{
    $explode1 = explode(' ',$fecha1);
    $explode1f = explode('-',$explode1[0]);
    $explode1h = explode(':',$explode1[1]);

    $explode2 = explode(' ',$fecha2);
    $explode2f = explode('-',$explode2[0]);
    $explode2h = explode(':',$explode2[1]);

    $fecha_ingreso = mktime($explode1h[0],$explode1h[1],$explode1h[2],$explode1f[1],$explode1f[2],$explode1f[0]);
    $fecha_alta = mktime($explode2h[0],$explode2h[1],$explode2h[2],$explode2f[1],$explode2f[2],$explode2f[0]);

    $dif_fechas = abs($fecha_alta - $fecha_ingreso) / (60 * 60 * 24);
    return round($dif_fechas); //redondea hacia arriba si la fracción es mas de medio día, redondea por abajo en caso contrario.
}


if(isset($consultaAjax) && $consultaAjax=='wnueva_fecha') // 2012-05-17 - Esta parte se usa para recalcular el rango de fechas cuando un ingreso tiene más de 30 días
{
    $err = 0; // Se usa para controlar mensajes de error del lado de javascript.
    if ($wupdate == 'f1') // Si se modificó fecha ingreso para generar hoja de medicamentos, siempre modifica fecha final según el límite $dias
    {
        echo sumaDiasAFecha($wfecha, $limite, 'suma').'|'.$err;
    }
    else
    {
        // Si se modificó fecha final y supera el rango límite $dias se recalcula, si es menor al límite se deja tal cual.
        $dif_dias = diasEntreFechas($wfecha.' 00:00:00', $wffin.' 00:00:00');

        if ($dif_dias > $limite)
        {
            $err = 1;
        }
        if ($accion == 'suma' && $err == 1)
        {  echo sumaDiasAFecha($wfecha, $limite, 'suma').'|'.$err; }
        elseif ($accion == 'resta'  && $err == 1)
        {  echo sumaDiasAFecha($wfecha, $limite, 'resta').'|'.$err; }
        else
        { echo $wffin.'|'.$err;}
    }
}
else
{
    // Para controlar la opción de impresión
    /******************************ADICIONADO EL 25 DE ABRIL DE 2012 ***********************************/
    // Parámetros para abrir ventana de impresión
    $params_imiprimir = "width=100px,height=100px,toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=no,fullscreen=No";

    $prin_body = '';
    if (isset($imprimir) && $imprimir)
    {
        $prin_body = '';//'onload="ocultar_div(\'capa2\');"';
    }
    else
    {
        $imprimir = false;
        // $prin_body = 'onload="mostrar_div(\'capa2\')"';
    }
    /****************************************************************************************************/

    $width = '';//8*89;
	
	if( !isset($retornarCodigo) ){
    ?>
    <html>
    <head>
      <title> REPORTE ADMINISTRACION DE MEDICAMENTOS (HOJA DE MEDICAMENTOS) </title>
	  <script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
    </head>
    <script type="text/javascript">
    /////////////////////////
    document.onkeydown = mykeyhandler;
    function mykeyhandler(event)
    {
      //keyCode 116 = F5
      //keyCode 122 = F11
      //keyCode 8   = Backspace
      //keyCode 37  = LEFT ROW
      //keyCode 78  = N
      //keyCode 39  = RIGHT ROW
      //keyCode 67  = C
      //keyCode 86  = V
      //keyCode 85  = U
      //keyCode 45  = Insert

      //keyCode 18  =  alt
      //keyCode 19  = pause/break
      //keyCode 27  = escape
      //keyCode 32  = space bar
      //keyCode 33  = page up
      //keyCode 34  = page down
      //keyCode 35  = end
      //keyCode 40  = down row
      //keyCode 46  = delete
      //keyCode 91  = left window key
      //keyCode 92  = right window key
      //keyCode 93  = select key
      //keyCode 112 = f1
      //keyCode 113 = f2
      //keyCode 114 = f3
      //keyCode 115 = f4
      //keyCode 116 = f5
      //keyCode 117 = f6
      //keyCode 118 = f7
      //keyCode 119 = f8
      //keyCode 120 = f9
      //keyCode 121 = f10
      //keyCode 122 = F11
      //keyCode 123 = f12
      //keyCode 124 = num lock
      //keyCode 145 = scroll lock
      //keyCode 154 = print screen
      //         44 = print screen

    event = event || window.event;
    if (navigator.appName == "Netscape")
        {
         var tgt = event.target || event.srcElement;

         if ((event.ctrlKey && event.which==37) || (event.ctrlKey && event.which==39) ||
            (event.ctrlKey && event.which==78) || (event.ctrlKey && event.which==67) ||
            (event.ctrlKey && event.which==86) || (event.ctrlKey && event.which==85) ||
            (event.ctrlKey && event.which==45) || (event.ctrlKey && event.which==45))
           {
            event.cancelBubble = true;
            event.returnValue = false;
            alert("Funcion no permitida");
            return false;
           }

         if(event.which==18 && tgt.type != "text" && tgt.type != "password" && tgt.type != "textarea")
           {
            return false;
           }

         if (event.which == 8 && tgt.type != "text" && tgt.type != "password" && tgt.type != "textarea")
           {
            return false;
           }

         //if ((event.which == 116) || (event.which == 122))
         if (event.which == 122)
           {
            return false;
           }
        }
    else
       {
        var tgt = event.target || event.srcElement;
        if((event.altKey && event.keyCode==37) || (event.altKey && event.keyCode==39) ||
            (event.ctrlKey && event.keyCode==78)|| (event.ctrlKey && event.keyCode==67)||
            (event.ctrlKey && event.keyCode==86)|| (event.ctrlKey && event.keyCode==85)||
            (event.ctrlKey && event.keyCode==45)|| (event.shiftKey && event.keyCode==45))
           {
            event.cancelBubble = true;
            event.returnValue = false;
            alert("Funcion no permitida");
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

        //if ((event.keyCode == 116) || (event.keyCode == 122))
        if (event.keyCode == 122)
          {
           if (navigator.appName == "Microsoft Internet Explorer")
             {
              window.event.keyCode=0;
             }
           return false;
          }
       }

      if ((event.keyCode == 44) || (event.keyCode == 144))
          {
           //if (navigator.appName == "Microsoft Internet Explorer")
           //  {
              window.event.keyCode=0;
           ///  }
           return false;
          }

      if (event.constructor.DOM_VK_PRINTSCREEN==event.keyCode)
         {
          alert("Función no permitida");
          return false;
         }
    }
	
	function boton_imprimir(i){
		
		var historia = $("#whis_"+i).val();
		var ingreso = $("#wing_"+i).val();
		var centro_costos = $("#wcco").val();
		var wtipo = $("#wtipo_"+i).val();
		var wemp_pmla = $("#wemp_pmla").val();
		
		var parametroextra = '';
		if( $( "[name=servicioDomiciliario]" ).length > 0 )
			if( $( "[name=servicioDomiciliario]" ).val() == 'on' )
				parametroextra = '&servicioDomiciliario=on'; 
		
		location.href = 'Hoja_medicamentos_enfermeria_IPODS.php?Imprimir=ok&whis='+historia+'&wing='+ingreso+'&wcco='+centro_costos+'&wtipo='+wtipo+'&wemp_pmla='+wemp_pmla+parametroextra;
		
	}
	
    function mouseDown(e)
    {
        var ctrlPressed=0;
        var altPressed=0;
        var shiftPressed=0;
        if (parseInt(navigator.appVersion)>3)
        {
            if (navigator.appName=="Netscape")
            {
                var mString =(e.modifiers+32).toString(2).substring(3,6);
                shiftPressed=(mString.charAt(0)=="1");
                ctrlPressed =(mString.charAt(1)=="1");
                altPressed =(mString.charAt(2)=="1");
                self.status="modifiers="+e.modifiers+" ("+mString+")"
            }
            else
            {
                shiftPressed=event.shiftKey;
                altPressed =event.altKey;
                ctrlPressed =event.ctrlKey;
            }
            if (shiftPressed || altPressed || ctrlPressed)
            { alert ("Función no permitida"); }
        }
        return true;
    }

    if (parseInt(navigator.appVersion)>3)
    {
        document.onmousedown = mouseDown;
        if (navigator.appName=="Netscape")
        { document.captureEvents(Event.MOUSEDOWN); }
    }

    var message="";

    function clickIE()
    {
        if (document.all)
        {
            (message);
            return false;
        }
    }

    function clickNS(e)
    {
        if(document.layers||(document.getElementById&&!document.all))
        {
            if (e.which==2||e.which==3)
            {
                (message);return false;
            }
        }
    }

    if (document.layers)
    {
        document.captureEvents(Event.MOUSEDOWN);
        document.onmousedown=clickNS;
    }
    else
    {
        document.onmouseup=clickNS;document.oncontextmenu=clickIE;
    }

    function imprimir()
    {
        window.print();
    }

    function cerrarVentana()
    {
        window.close()
    }

    function enter()
    {
        document.forms.hojamed.submit();
    }

    //-- 2012-04-25
    // alturaPagina = 24/0.026458333;
    // alturaPagina = 32/0.026458333; //COMENTADO
    alturaPagina = 1650; //AGREGADO  si se sube tamaño se le debe subir tambien a encabezadoPag1
    encabezadoPag1 = 350;
    paginas = 0;
    restoPaginas = 0;
    // --

    function ocultar_div (id)
    {
        div = document.getElementById(id);
        div.style.display='none';
    }

    function mostrar_div (id)
    {
        div = document.getElementById(id);
        div.style.display = '';
    }


    /*********************************************************************************
    * Encuentra la posicion en X de un elemento - 2012-04-25
    *********************************************************************************/
    function paginar( campo, principal, data )
    {
        if( campo )
        {
            if( campo.tagName )
            {
                //var cabecera = document.getElementById('hiPaciente').value;
                var cabecera = "";
                switch( campo.tagName )
                {
                    case 'TABLE':
                        var aux = document.createElement( "div" );
                        aux.innerHTML = "<table border=1 cellpadding=5 width='1070px' cellspacing=0 class=tipoTABLE></table>";

                        tabla = campo.cloneNode(true);
                        // tabla = campo;

                        // var sumaAltura = 3/0.026458333; // COMENTADO Junio 01 de 2012
                        var sumaAltura = 0;
                        var formulario = "";
                        var data1 = "";

                        var columnas_encabezado = campo.rows.length;
                        var ult_fch = '';
                        var nueva_pagina = false;
                        var filas_impresas = 0;
                        var limite = campo.rows.length;
                        var sumaEnc = true; //para sumar el encabezado de la primer hora una sola vez
                        var divHeight;

                        for( var i = 0; i < limite; i++ ) // && i < 180
                        {
                            divHeight = 0;
                            var paso = 0;
                            if(campo.rows[i].cells[0].innerHTML.substring(0,2) == "F=")
                            {
                                // formulario = campo.rows[i].cells[0].innerHTML;
                                // paso=1;
                            }

                            if(campo.rows[i].cells.length > 1) //Junio 01 de 2012
                            {
                                var obj = document.getElementById(campo.rows[i].cells[1].id);
                                if(obj.offsetHeight)          {divHeight=obj.offsetHeight;}
                                else if(obj.style.pixelHeight){divHeight=obj.style.pixelHeight;}
                                // console.log(campo.rows[i].cells[1].id+' | '+divHeight);
                            }
                            else
                            {
                                var obj = document.getElementById(campo.rows[i].cells[0].id);                               
                                if(obj.offsetHeight)          {divHeight=obj.offsetHeight;}
                                else if(obj.style.pixelHeight){divHeight=obj.style.pixelHeight;}
                                // console.log(campo.rows[i].cells[0].id+' | '+divHeight+' | <<');
                            }
                            // posFila = parseInt(findPosY( campo.rows[i] )); // COMENTADO Junio 01 de 2012

                            // sumaAltura = sumaAltura + parseInt(campo.rows[i].clientHeight); //COMENTADO Junio 01 de 2012
                            sumaAltura += divHeight; // AGREGADO Junio 01 de 2012

                            // posFila = posFila+parseInt(campo.rows[i].clientHeight); //COMENTADO Junio 01 de 2012
                            if (paginas == 0 && sumaEnc == true) // AGREGADO Junio 01 de 2012
                            {
                                //console.log('pagina 1');
                                sumaAltura += encabezadoPag1;//50; // altura aproximada del encabezado en la primera página // AGREGADO Junio 01 de 2012
                                sumaEnc = false;
                            }
                            //console.log(paginas+' | '+divHeight+' | '+sumaAltura+' | '+alturaPagina);

                            if( sumaAltura > alturaPagina )
                            {
                                //console.log('nuevo alto '+divHeight);
                                // restoPaginas = restoPaginas+(alturaPagina+paginas*alturaPagina-posFila+parseInt(campo.rows[i].clientHeight) ); // COMENTADO Junio 01 de 2012
                                paginas++;

                                // sumaAltura = campo.rows[i].clientHeight; // COMENTADO Junio 01 de 2012
                                sumaAltura = divHeight; // AGREGADO Junio 01 de 2012

                                // Si paginas es 1 no pone titulo ni número de página antes de la tabla
                                // si es mayor a 1 imprime titulo y número de página.
                                if(paginas > 1)
                                {
                                    data1 = data + " " + formulario;
                                    var aux2 = document.createElement( "div" );
                                    aux2.innerHTML = "<a>PÃ¡gina: "+paginas+"<br><br></a>";

                                    aux2.innerHTML =    "<a>"
                                                        +"<table width='1070px'>"
                                                        +"  <tr>"
                                                        +"      <td width='<?php echo $width; ?>' align='center' class=tipoPac>"+"</td>"
                                                        +"      <td width='<?php echo $width; ?>' class=tipoPac1>"+"</td>"
                                                        +"  </tr>"
                                                        +"</table><br><br>"
                                                        +"</a>";
                                    principal.appendChild( aux2.firstChild );
                                }
                                else
                                {
                                    var aux2 = document.createElement( "div" );
                                    aux2.innerHTML = "<a><br><br></a>";
                                    aux2.innerHTML =    "<a>"
                                                        +"<table width='1070px'>"
                                                        +"  <tr>"
                                                        +"      <td width='<?php echo $width; ?>' align='center' class=tipoPac></td>"
                                                        +"      <td width='<?php echo $width; ?>' class=tipoPac1></td>"
                                                        +"  </tr>"
                                                        +"</table><br><br>"
                                                        +"</a>";
                                    principal.appendChild( aux2.firstChild );
                                }

                                principal.appendChild( aux.firstChild );

                                aux.innerHTML = "<div style='page-break-after: always;'></div>";

                                principal.appendChild( aux.firstChild );
                                sumaAltura += 145+72; // 145 es la suma aproximada de la altura del nuevo encabezado al momento de crear una nueva página. + 72 de la fila para mostrar página// AGREGADO

                                aux.innerHTML =     "<table id='' border=1 cellpadding=5 width='1070px' cellspacing=0 class=tipoTABLE>"
                                                    +document.hojamed.encabezado_tabla.value
                                                    +"</table>";

                                nueva_pagina = true;
                            }

                            // console.log(campo.rows[i].cells[0].id);
                            // console.log(aux.firstChild.rows);
                            var fila = aux.firstChild.insertRow( aux.firstChild.rows.length );
                            // var fila = document.createElement( "tr" );
                            var numCeldas = campo.rows[i].cells.length;

                            var f = '';
                            var add = true;
                            var fecha_col = '';
                            filas_impresas++;

                            for( var  j = 0; j < numCeldas; j++)
                            {

                                fecha_col = fecha_split(tabla.rows[i].cells[0].id,'fecha');
                                td_id = tabla.rows[i].cells[0].id;

                                /**
                                *   Creado el 2012-04-23
                                *   Cuando se cambia de página y antes había un rowspan, la columna del rowspan se pierde,
                                *   para solucionarlo se crea una nueva columna y se le asigna el valor de rowspan faltante.
                                *
                                *   Para saber si la anterior fila era rowspan se compara la fecha de la fila anterior con la
                                *   fecha de la columna que se está leyendo actualmente, si es igual se adiciona una columna.
                                */
                                var tam_texto = 10 + "px";
                                if(j==0 && nueva_pagina == true && ult_fch == fecha_col)
                                {
                                    var rwp = fecha_split(td_id,'rowspan') - (fecha_split(td_id,'fila') - 1);
                                    var td = document.createElement( "td" );
                                    td.rowSpan = rwp;
                                    td.id = td_id;
                                    td.style.fontWeight = 'bold';
                                    td.style.fontSize = tam_texto;
                                    // td.width = 8*89;
                                    td.innerHTML = fecha_col;
                                    fila.appendChild( td );

                                    tabla.rows[i].cells[0].style.fontSize = tam_texto;
                                    fila.appendChild( tabla.rows[i].cells[0] );

                                    add = false;
                                }

                                f = fecha_col;
                                if(paso == 0 && add == true)
                                {
                                    tabla.rows[i].cells[0].style.fontSize = tam_texto;
                                    fila.appendChild( tabla.rows[i].cells[0] );
                                }

                                add=true;
                                nueva_pagina = false;
                            }
                            ult_fch = f;
                            nueva_pagina = false;
                        }

                        paginas++;
                        if(paginas > 1)
                        {
                            data1 = data + " " + formulario;
                            var aux2 = document.createElement( "div" );
                            aux2.innerHTML = "<a>PÃ¡gina: "+paginas+"<br><br></a>";

                            aux2.innerHTML = "<a>"
                                            +"<table width='1070px'>"
                                            +"  <tr>"
                                            +"      <td width='<?php echo $width; ?>' align='center' class=tipoPac>"+"</td>"
                                            +"      <td width='<?php echo $width; ?>' class=tipoPac1>"+"</td>"
                                            +"  </tr>"
                                            +"</table><br><br>"
                                            +"</a>";
                            principal.appendChild( aux2.firstChild );
                        }
                        else
                        {
                            var aux2 = document.createElement( "div" );
                            aux2.innerHTML = "<a><br><br></a>";
                            aux2.innerHTML = "<a>"
                                            +"<table width='1070px'>"
                                            +"  <tr>"
                                            +"      <td width='<?php echo $width; ?>' align='center' class=tipoPac></td>"
                                            +"      <td width='<?php echo $width; ?>' class=tipoPac1></td>"
                                            +"  </tr>"
                                            +"</table><br><br>"
                                            +"</a>";
                            principal.appendChild( aux2.firstChild );
                        }

                        campo.style.display = 'none';
                        // debugger;
                        //principal.removeChild(campo);
                        principal.appendChild( aux.firstChild );

                     break;
                }
            }
        }
        // ocultar_div('capa1');
        // mostrar_div('capa2');
       
        window.print();cerrar_print(1000);
    }

    /**
     * creado en 2012-04-25
     *
     * La función fecha_split(..) recibe el id de una celda de tabla y extrae la información requerida
     * (p.e. extrae el valor de la fecha, el valor original del rowspan ó la fila que se está leyendo actualmente)
     *
     * @return valor (una fecha o un número, según la ipción que llega por parámetros).
     */
    function fecha_split(id,opcion)
    {
        var valor='';
        var arr = id.split('|');
        switch (opcion)
        {
            case 'fecha':   valor = arr[0]; // fecha de la celda
                            break;
            case 'rowspan': valor = arr[1]; // filas del rowspan
                            break;
            case 'fila':    valor = arr[2]; // consecutivo de fila
                            break;
        }

        return valor;
    }

    /************************************************************************************
     * encuentra la posicion Y de un elemento  - 2012-04-25
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

    /**
     * Esta función cierra la ventana que se abre al imprimir luego de unos segundos.
     *
     * @return unknown
     */
    function cerrar_print(time)
    {
        setTimeout(window.close,time);
    }

    /**
     * Función para controlar el rango de fechas al momento de mostrar los medicamentos
     * las fechas se recalculan al modificar una de ellas (Inicial o final) siempre conservando un rango de 30 días
     *
     * @return unknown
     */
    function validar_rango_fechas(form, fec_modif)
    {
        var parametros = "";
        var fini = form.wfechainicial.value;
        var ffin = form.wfechafinal.value;
        var limite = form.wlimite.value;

        // Si se seleccionó la fecha inicial, se actualiza la fecha final
        if(fec_modif == 1)
        {
            // var fini = form.wfechainicial.value;
            parametros = "consultaAjax=wnueva_fecha&wfecha="+fini+"&accion=suma&wffin="+ffin+"&limite="+limite+"&wupdate=f1";
            try
            {
                var ajax = nuevoAjax();
                ajax.open("POST", "Hoja_medicamentos_enfermeria_IPODS.php?wemp_pmla=<?php echo($wemp_pmla) ?>",false);
                ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                ajax.send(parametros);
                //alert(ajax.responseText);
                var data = ajax.responseText;
                var splt = data.split('|');
                if(parseInt(splt[1]) == 1)
                {

                }
                form.wfechafinal.value = splt[0];
            }catch(e){ alert(e) }

        }
        else
        {// Si se seleccionó la fecha final se actualiza la fecha inicial.
            // var ffin = form.wfechafinal.value;
            parametros = "consultaAjax=wnueva_fecha&wfecha="+fini+"&accion=suma&wffin="+ffin+"&limite="+limite+"&wupdate=f2";
            try
            {
                var ajax = nuevoAjax();
                ajax.open("POST", "Hoja_medicamentos_enfermeria_IPODS.php?wemp_pmla=<?php echo($wemp_pmla) ?>",false);
                ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                ajax.send(parametros);
                //alert(ajax.responseText);
                var data = ajax.responseText;
                var splt = data.split('|');
                if(parseInt(splt[1]) == 1)
                {
                    alert('No puede seleccionar un rango mayor a ['+limite+'] días');
                }
                form.wfechafinal.value = splt[0];
            }catch(e){ alert(e) }
        }
    }
	
	function tipoMedicamento(){
		var elements = document.getElementsByName('wtipoposcambio');
		var tipoElegido = "";
		for (var i=0; i< elements.length; i++){
			if (elements[i].checked) tipoElegido = elements[i].value;
			
		}
		
		var wtipoposprevio = document.getElementById('wtipopos');
		if( wtipoposprevio == undefined )
			wtipoposprevio='todos';
		if( wtipoposprevio == tipoElegido )
			return;			
		document.getElementById('wtipopos').value = tipoElegido;		
		enter();
	}
    </script>
    <BODY TEXT="#000000" <?php echo $prin_body; ?> >
    <?php
	}
      /***************************************************
        *             HOJA DE MEDICAMENTOS               *
        *                 POR HISOTRIA                   *
        *               CONEX, FREE => OK                *
        **************************************************/
    @session_start();
    if(!isset($_SESSION['user']) && !isset($retornarCodigo))
        echo "error";
    else
    {
        

        

        include_once("root/comun.php");

        $key = substr($user,2,strlen($user));

        if (strpos($user,"-") > 0)
              $wuser = substr($user,(strpos($user,"-")+1),strlen($user));

        $wusuario=$wuser;

        $wactualiz="2019-09-26";
		
		$horasPares = 12;
        
        //=========================================================================================================================
		// 2019-09-26 Jessica Madrid Mejía.	Para los tipos de productos de central de mezclas que estén configurados en cenpro_000001
		//		 							con el campo Tippro='on' debe mostrar el artículo que prescribió el médico (antes de 
		// 									hacer el reemplazo)
	    //=========================================================================================================================
	    // 2019-02-18 Freddy Saenz . Se filtran Q-Citostáticos y Coadyuvantes para que no incluya analgesias ni insulinas
		// tabla movhos_000091 . Se selecciona lo de la tabla movhos_000068 y se le quitan los registros que esten en la 
		// tabla movhos_000091 . movhos_000068 - movhos_000091
        //=========================================================================================================================
		// 2018-10-04 Jonatan Lopez. Se repara el listado de medicamentos para el primer paciente ya que no estaba mostrando informacion.
        //=========================================================================================================================
		//  2018-07-12 Juan Felipe Balcero. Se agrega la opción de generar la hoja filtrando por el tipo de medicamento requerido, ya sean todos, sólo                                          quimioterapia, nutricion parenteral, no esteril o dosis adaptada. 
		//=========================================================================================================================
		//  2017-05-18 Jonatan Lopez. Se agrega mensaje en la aprte superior con aclaracion sobre los medicamentos aplicados en las rondas.
		//								Se agrega la hora de aplicacion del medicamento.
		//=========================================================================================================================
        //	2017-02-15 Jonatan Lopez.  Se relaciona la consulta principal con la tabla movhos_000020 para que no muestre pacientes
		// 								que tengan devolucion de alta definitiva.
		//=========================================================================================================================//=========================================================================================================================
        //	2015-04-28 Edwin Molina.  Se agrega la via de administración de los medicamentos.
		//=========================================================================================================================
        //	2014-01-20 Frederick Aguirre.  Se ofrece la opcion de imprimir los medicamentos POS, NO POS o todos (opcion por defecto).
        //==========================================================================================================================
		
		//=========================================================================================================================
        //	2014-01-13 Frederick Aguirre.  Se modifica el código para que desde HCE se pueda llamar al programa y retornar el codigo
		//	html con la hoja de medicamentos de un paciente.
        //==========================================================================================================================
		//=========================================================================================================================
        //	Julio 10 de 2012 Viviana Rodas.  Se agrega el llamado a las funciones consultaCentrosCostos que hace la consulta de 
		//  los centros de costos de un grupo seleccionado y dibujarSelect que dibuja el select con los centros de costos obtenidos
        //	de la primera funcion.
        //==========================================================================================================================
        //Junio 01 de 2012
        //==========================================================================================================================
        // Modificaciones en javascript, en algunos casos se bloqueaba el javascript al momento de separar las tablas html para su
        // impresión, se suprime el uso de la función 'findPosY' donse genereba el bloqueo. Y se hacen modificaciones para que se
        // calcule de otra manera el alto de las columnas html.
        //==========================================================================================================================
        //Mayo 28 de 2012
        //==========================================================================================================================
        // En la consulta principal ubicada más o menos en las líneas 1400, la columna de código de articulos se complementa con la
        // instrucción UPPER de mysql, al parecer algunos códigos de medicamentos a pesar de ser los mismos diferían por tener alguna
        // minúscula, lo que hacía que se generara un mal conteo de los códigos y se imprimiera mal la hoja de medicamentos en el html.
        //==========================================================================================================================
        //Mayo 17 de 2012
        //==========================================================================================================================
        // Se crea un punto intermedio entre la lista de pacientes y la lista de medicamentos aplicados.
        // Esto ocurre solo si el tiempo de estadía durante el ingreso supera los 30 días
        // El punto intermedio muestra un rango de fechas para los cuales se quiere generar el reporte, muestra los medicamentos que
        // se aplicaron entre esas fechas, por defecto la fecha inicial es la fecha del ingreso y la fecha final es una fecha 30 días
        // despues de la fecha del ingreso.
        // Al modificar las fechas al momento de seleccionar el periodo a consultar, estas de actualizan conservando un rango de 30 Días.
        //==========================================================================================================================
        //Abril 25 de 2012
        //==========================================================================================================================
        // Se actualiza la opción de impresion con el fin de imprimir el encabezado en cada nueva página.
        // De momento se debe imprimir de forma Vertical para que no se creen páginas adicionales sin encabezado.
        //
        //==========================================================================================================================
        //Octubre 20 de 2011
        //==========================================================================================================================
        //Se acondiciona para que este reporte pueda ser visto desde la historia clinica, haciendo el enlace por medio de la cedula
        //y tipo de documento los cuales sirven para ir a buscar la historia y numero de ingreso
        //==========================================================================================================================
        //==========================================================================================================================
        //Septiembre 27 de 2011
        //==========================================================================================================================
        //Se adiciona la validación del usuario basado en la configuración de HCE para el ROL de cada usuario, es decir, que si el
        //ROL que tiene asignado el usuario en la configuración de la HCE no tiene el permiso para ver la Hoja de Medicamentos del
        //paciente que esta intentando acceder no puede; este permiso esta dado por la empresa responsable del paciente, la cual
        //debe estar incluida en las empresas que puede ver el usuario según la relación del Rol-Empresas de la tabla HCE_000019 y
        //HCE_000025.
        //==========================================================================================================================
        //==========================================================================================================================
        //Agosto 22 de 2011
        //==========================================================================================================================
        //Se muestra tanto lo aplicado como lo NO aplicado, basado en la tabla movhos_000113 y en la _000015, en donde se registran
        //los motivos de NO aplicación y lo aplicado respectivamente, además se coloca en symbolos estas dos acciones (chulo para lo
        //aplicado y una X para lo no aplicado con justificación.
        //==========================================================================================================================
        //Mayo 31 de 2011
        //==========================================================================================================================
        //Se modifco el 31 de Mayo para que tome el nombre del articulo de las tablas maestras movhos_000026 y cenpro_000002, porque
        //antes lo venia tomando del nombre que quedo en la tabla de movimiento ('apldes' de la tabla movhos_000015 y cuanod se
        //cambiaba el nombre de algún articulo entonces este reporte salia desplazado en las columnas.
        //Pero igual en el movimiento sigue quedando la descripción que tenia el articulo al momento de la aplicación.
        //==========================================================================================================================


        //=======================================================================================================
        //FUNCIONES
        //=======================================================================================================

        function tomar_ronda($wron)
          {
           $wronda=explode(":",$wron);

           if ((integer)$wronda[0] < 12)
              {
               if (isset($wronda[1]) and strpos($wronda[1],"PM") > 0)
                  {
                   return $wronda[0]+12;
                  }
                 else
                    return $wronda[0];
              }
             else
                if ((integer)$wronda[0]==12)
                   {
                    if (strpos($wronda[1],"PM") > 0)
                       return $wronda[0];                //Devuelve 12. que equivale a 12:00 PM osea del medio dia
                      else
                         return $wronda[0]-12;
                   }
                  else
                     return $wronda[0];
         }


		function consultaCentrosCostosNoDomiciliarios( $conex, $wbasedato ){
	
			$coleccion = array();
			
			$sql = "SELECT Ccocod, UPPER( Cconom )
					  FROM ".$wbasedato."_000011
					 WHERE Ccoest  = 'on' 
					   AND Ccohos  = 'on' 
					   AND ccodom != 'on'
				  ORDER BY Ccoord, Ccocod; ";
							  
			$res1 = mysql_query($sql,$conex) or die (" Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
			$num1 = mysql_num_rows($res1);

			if ($num1 > 0 )
			{
				for ($i=1;$i<=$num1;$i++)
				{
					$cco = new centroCostosDTO();
					$row1 = mysql_fetch_array($res1);

					$cco->codigo = $row1[0];
					$cco->nombre = $row1[1];

					$coleccion[] = $cco;
				}
			}
			
			return $coleccion;
		}
		
		function consultaCentrosCostosDomiciliarios( $conex, $wbasedato ){
		
			$coleccion = array();
			
			$sql = "SELECT Ccocod, UPPER( Cconom )
					  FROM ".$wbasedato."_000011
					 WHERE Ccoest  = 'on' 
					   AND Ccohos  = 'on' 
					   AND ccodom  = 'on'
				  ORDER BY Ccoord, Ccocod; ";
							  
			$res1 = mysql_query($sql,$conex) or die (" Error: " . mysql_errno() . " - en el query: " . $sql . " - " . mysql_error());
			$num1 = mysql_num_rows($res1);

			if ($num1 > 0 )
			{
				for ($i=1;$i<=$num1;$i++)
				{
					$cco = new centroCostosDTO();
					$row1 = mysql_fetch_array($res1);

					$cco->codigo = $row1[0];
					$cco->nombre = $row1[1];

					$coleccion[] = $cco;
				}
			}
			
			return $coleccion;
		}


        function elegir_centro_de_costo()
         {
          global $conex;
          global $wbasedato;
          global $wtabcco;
          global $wcco;


          global $whora_par_actual;
          global $whora_par_anterior;

          global $esServicioDomiciliario;

          echo "<center><table>";
          echo "<tr class=encabezadoTabla><td align=center><font size=7>HOJA DE MEDICAMENTOS</font></td></tr>";
          echo "</table></center>";

          echo "<br><br>";

          //Seleccionar CENTRO DE COSTOS
         		  
		  //**************** llamada a la funcion consultaCentrosCostos y dibujarSelect************
		  $cco="Ccohos";
		  $sub="on";
		  $tod="";
		  $ipod="";
		  //$cco=" ";
		  // $centrosCostos = consultaCentrosCostos($cco);
		  
		  if( $esServicioDomiciliario )
				$centrosCostos = consultaCentrosCostosDomiciliarios($conex,$wbasedato);
		  else
				$centrosCostos = consultaCentrosCostosNoDomiciliarios($conex,$wbasedato);
		  
					
		  echo "<table align='center' border=0>";		
		  $dib=dibujarSelect($centrosCostos, $sub, $tod, $ipod);
					
		  echo $dib;
		  echo "</table>"; 
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
                return false;
             else
                return true;
        }


        function convertir_a_fraccion($wart,$wcan_apl,&$wuni_fra,$wcco)
          {
           global $conex;
           global $wbasedato;

           $wdos_apl=$wcan_apl;    //Dosis

           $q = " SELECT deffra, deffru "
               ."   FROM ".$wbasedato."_000059 "
               ."  WHERE defcco in ('1050','1051')"
               ."    AND defart = '".$wart."'"
               ."    AND defest = 'on' ";
           $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
           $num = mysql_num_rows($res);
           if ($num > 0)
              {
               $row = mysql_fetch_array($res);
               //$wcan_fra = $row[0];   //Cantidad de fracciones
               $wuni_fra = $row[1];   //Unidad de la fracción

               //Si es el medicamento es del stock, no se hace la conversion, porque multiplicaria la cantidad aplicada por la fraccion de la 000059
               //if (!esdelStock($wart, $wcco))     //No es del Stock
               //   {
               //    $wdos_apl = $wcan_apl*$wcan_fra;
               //   }
               //  else
                    $wdos_apl = $wcan_apl;

               return $wdos_apl;
              }
             else
                return $wdos_apl;
          }


        function buscarSiEstaSuspendido($whis, $wing, $wart, $wfecha)
        {
         global $user;
         global $conex;
         global $wbasedato;

         $whorsus="";

         //Busco si esta suspendido
         $q = " SELECT COUNT(*)  "
             ."   FROM ".$wbasedato."_000055 A "
             ."  WHERE kauhis  = '".$whis."'"
             ."    AND kauing  = '".$wing."'"
             ."    AND kaufec  = '".$wfecha."'"
             ."    AND kaudes  = '".$wart."'"
             ."    AND kaumen  = 'Articulo suspendido' ";
         $ressus = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         $rowsus = mysql_fetch_array($ressus);

         if ($rowsus[0] > 0)
            return true;
           else
              return false; //Indica que fue Suspendido hace mas de dos horas
        }


        function buscarJustificacionNoAplicado($wart, $whis, $wing, $wron, $wfec)
         {
          global $conex;
          global $wbasedato;

          $q = " SELECT jusjus "
              ."   FROM ".$wbasedato."_000113 "
              ."  WHERE jushis = '".$whis."'"
              ."    AND jusing = '".$wing."'"
              ."    AND jusfec = '".$wfec."'"
              ."    AND jusart = '".$wart."'"
              ."    AND cast(MID(jusron,1,instr(jusron,':')-1) AS SIGNED) = '".$wron."'";
          $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
          $num = mysql_num_rows($res);

          if ($num > 0)
             {
              $row = mysql_fetch_array($res);
              return $row[0];
             }
            else
               return "";
         }
		 
		 
// Modificacion : 18 de Febrero 2019 Freddy Saenz
// Buscar grupos 
//  para reemplazar el siguiente codigo
// echo "<option value='Q-Citostáticos y Coadyuvantes'>Citostáticos y Coadyuvantes</option>";
// echo "<option value='DA-Dosis Adaptada'>Dosis Adaptada</option>";
// echo "<option value='NU-Nutricion Parenteral'>Nutrición parenteral</option>";
// echo "<option value='NE-No Esteril'>No esteril</option>";
// echo "<option value='OT-Generales'>Generales</option>";
// Se crea un campo el la tabla movhos_000099 para que tiene la informacion
// codigo-descripcion, ej : Q-Citostáticos y Coadyuvantes ,el campo se llama Targru
//SELECT Targru FROM movhos_000099 GROUP BY Targru 

		function buscarGrupos ()
		{

			global $conex;
			global $wbasedato;


			$q = " SELECT  Targru , Tarcod "
				. " FROM " . $wbasedato . "_000099 "
				. " WHERE  Targru <> 'TODOS' "
				. " ORDER BY 1 ";//Targru"; 

			$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			$num = mysql_num_rows($res);

			$vecho = "";
			if ($num > 0)
			{
				 
				$ultOpcion = "";
				$ultOpcioncod = "";
				for ($i=1;$i<=$num;$i++)
				{

					$row = mysql_fetch_array($res);
					$opcion = $row[0];//Q, N, QT  //Q-Citostáticos y Coadyuvantes
					if ($i == 1)
					{
						$ultOpcion = $row[0];//grupo correspondiente
						$ultOpcioncod =   $row[1]  ;//codigo correspondiente
					}
					if ($opcion == ""){
						$opcion = "'Sin Grupo (Targru)'";
					}
					$labelopcion = $opcion;

					if ( $i == $num ) {//si es el ultimo , crear la opcion que se lleva y ver si se debe crear otra
  
						if ( ( $ultOpcion != $opcion )  ){
						  $vecho .= "<option value='$ultOpcioncod-$ultOpcion'>$ultOpcion</option>";
						  $ultOpcion = $row[0];//grupo correspondiente
						  $ultOpcioncod =   $row[1]  ;//codigo correspondiente
						  $vecho .= "<option value='$ultOpcioncod-$ultOpcion'>$ultOpcion</option>";

						}else{
						  $ultOpcioncod = $ultOpcioncod . "," .  $row[1] ;//codigo correspondiente
						  $vecho .= "<option value='$ultOpcioncod-$ultOpcion'>$ultOpcion</option>";

						}
					}elseif( $ultOpcion != $opcion ) {
						$vecho .= "<option value='$ultOpcioncod-$ultOpcion'>$ultOpcion</option>";
						$ultOpcion = $row[0];//grupo correspondiente
						$ultOpcioncod =   $row[1]  ;//codigo correspondiente


					  }elseif( $i == 1 ) {//ya se realizo en la inicializacion.
					  }else{
						$ultOpcion = $row[0];//grupo correspondiente
						$ultOpcioncod = $ultOpcioncod . "," .  $row[1] ;//codigo correspondiente

					 }


					
					
				}//for ($i=1;$i<=$num;$i++)
       
					  
			}//if ($num > 0)
//echo '<pre>'; print_r($vecho); echo '</pre><hr>';
			return $vecho;
		}
		 
        function buscarUltimaAplicacionDia($Mrondas, $i, $j)
         {
          $j++;
          $wult="on";
          for ($k=$j; $k<= 23; $k++)
             {
              if ($Mrondas[$i][$k] > 0)
                 $wult="off";
             }
          return $wult;
         }

        //Septiembre 27 de 2011
        function validar_usuario($his, $ing)
         {
          global $conex;
          global $wbasedato;
          global $wusuario;
          global $whce;
		  
		  global $retornarCodigo; //2014-01-13
		  if( isset($retornarCodigo) ){
				return true;
		  }

          $wfecha=date("Y-m-d");

          $q = " SELECT ingres "
              ."   FROM ".$wbasedato."_000016 "
              ."  WHERE inghis = '".$his."'"
              ."    AND inging = '".$ing."'";
          $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
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
          $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
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
                $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                $row = mysql_fetch_array($res);

                if ($row[0] > 0)
                   return true;
                  else
                     return false;
               }
         }
        //=======================================================================================================

        //=======================================================================================================
        //=======================================================================================================
        //CON ESTO TRAIGO LA EMPRESA Y TODAS CAMPOS NECESARIOS DE LA EMPRESA
        $q = " SELECT empdes "
            ."   FROM root_000050 "
            ."  WHERE empcod = '".$wemp_pmla."'"
            ."    AND empest = 'on' ";
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $row = mysql_fetch_array($res);

        $wnominst=$row[0];

        //Traigo TODAS las aplicaciones de la empresa, con su respectivo nombre de Base de Datos
        $q = " SELECT detapl, detval "
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

                if (strtoupper($row[0]) == "HCE")
                   $whce=$row[1];

                if ($row[0] == "tabcco")
                   $wtabcco=$row[1];
            }
        }
        else
            echo "NO EXISTE NINGUNA APLICACION DEFINIDA PARA ESTA EMPRESA";
        //=======================================================================================================
        //=======================================================================================================
		if(!isset($retornarCodigo)){
			echo "<form NAME=hojamed action='Hoja_medicamentos_enfermeria_IPODS.php?wemp_pmla=".$wemp_pmla."' method=post>";

			echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";
			echo "<input type='HIDDEN' name='whce' value='".$whce."'>";
		}
		
		$esServicioDomiciliario = false;
		if( isset($servicioDomiciliario) && $servicioDomiciliario == 'on' ){
			$esServicioDomiciliario = true;
			echo "<input type='HIDDEN' NAME= 'servicioDomiciliario' value='".$servicioDomiciliario."'/>";
		}
		
        if (!isset($wcco))
           elegir_centro_de_costo();
        else
        {
            echo "<input type='HIDDEN' name='wcco' value='".$wcco."'>";

            //Si ya vienen definidos tanto la cedula como el tipo de documento busco con ellos la hria y el ingreso
            if (isset($wced) and isset($wtid))
            {
                $q = " SELECT orihis, oriing "
                    ."   FROM root_000036, root_000037 "
                    ."  WHERE pacced = '".$wced."'"
                    ."    AND pactid = '".$wtid."'"
                    ."    AND pacced = oriced "
                    ."    AND pactid = oritid "
                    ."    AND oriori = '".$wemp_pmla."'";
                $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                $wnr = mysql_num_rows($res);

                if ($wnr > 0)
                {
                    $row = mysql_fetch_row($res);
                    $whis = $row[0];
                    $wing = $row[1];
                }
            }

            if (!isset($whis) or !isset($wing))
            {
                if ($wcco=="*")
                $wcco="%";

                $wcco1=explode("-",$wcco);
				
				$tablaHabitaciones = consultarTablaHabitaciones( $conex, $wbasedato, $wcco1[0] );

                encabezado("ADMINISTRACION DE MEDICAMENTOS (HOJA DE MEDICAMENTOS)", $wactualiz, 'clinica');

                echo "<center><table>";

                $q = " SELECT ubihac, ubihis, ubiing, pacno1,pacno2, pacap1, pacap2, ubifad, root_000036.fecha_data, ubisac "
                    ."   FROM ".$wbasedato."_000018, ".$tablaHabitaciones.",  root_000037, root_000036 "
                    ."  WHERE ubihis  = orihis "
                    ."    AND ubiing  = oriing "
					."	  AND ubihis  = habhis "
                    ."    AND ubiing  = habing "
                    ."    AND oriori  = '".$wemp_pmla."'"
                    ."    AND oriced  = pacced "
                    ."    AND oritid  = pactid "
                    ."    AND ubiald != 'on' "              //Solo los que estan activos
                    ."    AND ubisac  LIKE '".trim($wcco1[0])."'"
                    ."  ORDER BY 1, 4, 5 ";
				//	echo $q;
                $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                $wnr = mysql_num_rows($res);

                echo "<tr class=encabezadoTabla>";
                echo "<th width='100px'>Habitacion</th>";
                echo "<th width='100px'>Historia</th>";
                echo "<th>Ingreso</th>";
                echo "<th width='300px'>Paciente</th>";
                echo "<th colspan=2>Tipo Medicamento</th>";
                echo "</tr>";

                $wclass = "fila2";
                $whabant = "";
                for ($i=1;$i<=$wnr;$i++)
                {
                    $row = mysql_fetch_row($res);
                    $whab    = $row[0];
                    $whis    = $row[1];
                    $wing    = $row[2];
                    $wpac    = $row[5]." ".$row[6]." ".$row[3]." ".$row[4];

                    if ($whabant != $whab)
                    {
                        if ($wclass == "fila1")
                            $wclass = "fila2";
                          else
                             $wclass = "fila1";
                        echo "<tr class=".$wclass.">";                        
                        echo "<td align=center>".$whab."</td>";
                        echo "<td align=center>".$whis."</td>";
                        echo "<td align=center>".$wing."</td>";
                        echo "<td align=left  >".$wpac."</td>";
                        echo "<td><select name='wtipo' id='wtipo_".$i."'>";
						
                        echo "<option value='%-Todos'>Todos</option>";
						/*
                        echo "<option value='Q-Citostáticos y Coadyuvantes'>Citostáticos y Coadyuvantes</option>";
                        echo "<option value='DA-Dosis Adaptada'>Dosis Adaptada</option>";
                        echo "<option value='NU-Nutricion Parenteral'>Nutrición parenteral</option>";
                        echo "<option value='NE-No Esteril'>No esteril</option>";											
                        echo "<option value='OT-Generales'>Generales</option>";
						*/
						//Modificacion 18 de Febrero 2019 , Freddy Saenz
						echo buscarGrupos();
						
                        echo "</select></td>";
                        echo "<input type='hidden' name='whis' id='whis_".$i."' value='".$whis."'>";
                        echo "<input type='hidden' name='wing' id='wing_".$i."' value='".$wing."'>";
                        echo "<input type='hidden' name='wcco' id='wcco' value='".$wcco."'>";
                        echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
                        echo "<td align=center><input type='button' value='Imprimir' onclick='boton_imprimir(\"".$i."\")'></td>";                       
                        echo "</tr>";

                        $whabant = $whab;
                    }
                }

                echo "</table>";
                echo "<table>";
                echo "<tr class=seccion1>";
                echo "<td><b>Ingrese la Historia :</b><INPUT TYPE='text' NAME='whis' SIZE=10></td>";
                echo "<td><b>Nro de Ingreso :</b><INPUT TYPE='text' NAME='wing' SIZE=10></td>";
                echo "<td><b>Tipo de Medicamentos :</b><select name='wtipo'>";
                
				echo "<option value='%-Todos'>Todos</option>";
				
				/*
                echo "<option value='Q-Citostáticos y Coadyuvantes'>Citostáticos y Coadyuvantes</option>";
                echo "<option value='DA-Dosis Adaptada'>Dosis Adaptada</option>";
                echo "<option value='NU-Nutricion Parenteral'>Nutrición parenteral</option>";
                echo "<option value='NE-No Esteril'>No esteril</option>";				
                echo "<option value='OT-Generales'>Generales</option>";
				*/
				//Modificacion 18 de Febrero 2019 , Freddy Saenz
				echo buscarGrupos();

                echo "</select></td>";
                echo "</tr>";
                echo "<tr> </tr>";
                echo "<tr> </tr>";
                echo "<tr class=boton1><td align=center colspan=6></b><input type='submit' value='ACEPTAR'></b></td></tr>";
                echo "</table></center>";

				if($esServicioDomiciliario)
					echo "<center><font size=3><A href='Hoja_medicamentos_enfermeria_IPODS.php?wemp_pmla=".$wemp_pmla."&servicioDomiciliario=on'> Retornar</A></font></center>";
				else
					echo "<center><font size=3><A href='Hoja_medicamentos_enfermeria_IPODS.php?wemp_pmla=".$wemp_pmla."'> Retornar</A></font></center>";
            }
            else
            {
                if (validar_usuario($whis, $wing))     //Septiembre 27 de 2011
                {
                    /*********************************
                    * TODOS LOS PARAMETROS ESTAN SET *
                    **********************************/
                    $wcco1=explode("-",$wcco);

                    /*
                    //=======================================================================================================
                    //=======================================================================================================
                    //Traer Indicador del Kardex Electronico
                    $q = " SELECT ccokar "
                        ."   FROM ".$wbasedato."_000011 "
                        ."  WHERE ccocod = '".trim($wcco1[0])."'"
                        ."    AND ccoest = 'on' ";
                    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    $row = mysql_fetch_array($res);
                    $wkar_ele = $row[0];        //Indica que tiene kardex electronico
                    //=======================================================================================================
                    */
if( !isset($retornarCodigo) ){
                    encabezado("REPORTE ADMINISTRACION DE MEDICAMENTOS A PACIENTES", $wactualiz, 'clinica');
}
                    // 2012-05-17 Paso intermedio para seleccionar rango de fechas si el ingreso supera los 30 días
                    /******/
                    $query_rango = "SELECT  Ubihis, Ubiing, Ubialp as alta_pac, Fecha_data AS fecha_ingreso, Hora_data, Ubifad AS fecha_alta, Ubihad
                                    FROM    ".$wbasedato."_000018
                                    WHERE   Ubihis = '".$whis."'
                                            AND Ubiing = '".$wing."'
                                    ORDER BY    Fecha_data";
                    $res_r = mysql_query($query_rango,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$query_rango." - ".mysql_error());
                    $numreg = mysql_num_rows($res_r);

                    $row_r = mysql_fetch_array($res_r);
                    if($numreg > 0)
                    {
                        $rango = array(
                                    'whis'          => $row_r['Ubihis'],
                                    'wing'          => $row_r['Ubiing'],
                                    'alta_pac'      => $row_r['alta_pac'],  // off: está activo, on: esta de alta, ya salió
                                    'fecha_ingreso' => $row_r['fecha_ingreso'].' '.$row_r['Hora_data'],
                                    'fecha_alta'    => $row_r['fecha_alta'].' '.$row_r['Ubihad'],
                                    'dif_fechas'    => 0
                                );

                        // Si la fecha de alta esta vacía se asigna la fecha y hora actual de servidor.
                        if ($rango['alta_pac'] == 'off' || $rango['fecha_alta'] == '0000-00-00 00:00:00') // 2012-05-17
                        {
                            $rango['fecha_alta'] = date("Y-m-d H:i:s");
                        }

                        $rango['dif_fechas'] = diasEntreFechas($rango['fecha_ingreso'],$rango['fecha_alta']);

                        // echo '<pre>';
                        // print_r($rango);
                        // echo '</pre><hr>';

                        $dLimite = consultarAliasPorAplicacion($conex, $wemp_pmla, 'dias_hoja_medicamentos');
                    }else{
						if( isset($retornarCodigo) ){
							echo "SINDATOS";
							return;
						}					
					}

                    $q = " SELECT ubihac, ubihis, ubiing, pacno1,pacno2, pacap1, pacap2, ubifad, root_000036.fecha_data, ubisac, cconom "
                            ."   FROM ".$wbasedato."_000018, root_000037, root_000036, ".$wtabcco
                            ."  WHERE ubihis  = '".$whis."'"
                            ."    AND ubiing  = '".$wing."'"
                            ."    AND ubihis  = orihis "
                            ."    AND oriori  = '".$wemp_pmla."'"
                            ."    AND oriced  = pacced "
                            ."    AND ubisac  = ccocod "
                            ."    AND oritid  = pactid "
                            ."  ORDER BY 1, 4, 5 ";
                    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    $wnr = mysql_num_rows($res);

                    $row     = mysql_fetch_row($res);
                    $whab    = $row[0];
                    $whis    = $row[1];
                    $wing    = $row[2];
                    $wpac    = $row[5]." ".$row[6]." ".$row[3]." ".$row[4];
                    $wser    = $row[9];
                    $wnomser = $row[10];

                    if ($numreg > 0 && !$imprimir && $rango['dif_fechas'] > $dLimite && !isset($wrango_fechas)) // 2012-05-17 - este condicionar el agregado para obligar al paso intermedio
                    {
                        $msj_activo = '';
                        $cols_f = '';
                        $fing = explode(' ',$rango['fecha_ingreso']);
                        $falt = explode(' ',$rango['fecha_alta']);
                        if ($rango['alta_pac'] == 'off')
                        {
                            $msj_activo = 'Paciente ACTIVO actualmente';
                            $cols_f = "
                                        <td colspan='2' align='center'><font size='4'>".$msj_activo."</font></td>";
                        }
                        else
                        {
                            $cols_f = "
                                        <td align='center'><font size='3'>".$falt[0]."</font></td>
                                        <td align='center'><font size='3'>".$falt[1]."</font></td>";
                        }

                        $info = "
                                <div align='center'>
                                <table>
                                    <tr class='encabezadoTabla'>
                                        <td id='td_lhis'><font size='4'>Historia:</font></td>
                                        <td id='td_his' class='fila1'><font size='4'>".$whis.'-'.$wing."</font></td>
                                        <td id='td_lnom'> <font size='4'>Nombre:</font></td>
                                        <td id='td_nom' class='fila1'> <font size='4'>".$wpac."</font></td>
                                    </tr>
                                </table>
                                <table width='600px'>
                                    <tr>
                                        <td align='center'>
                                            <table>
                                                <tr class='encabezadoTabla'>
                                                    <th colspan='2'><font size='4'>Fecha ingreso</font></th>
                                                </tr>
                                                <tr class='encabezadoTabla'>
                                                    <th><font size='4'>Fecha</font></th>
                                                    <th><font size='4'>Hora</font></th>
                                                </tr>
                                                <tr class='fila1'>
                                                    <td align='center'><font size='3'>".$fing[0]."</font></td>
                                                    <td align='center'><font size='3'>".$fing[1]."</font></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td align='center'>
                                            <table>
                                                <tr class='encabezadoTabla'>
                                                    <th colspan='2'><font size='4'>Fecha alta</font></th>
                                                </tr>
                                                <tr class='encabezadoTabla'>
                                                    <th><font size='3'>Fecha</font></th>
                                                    <th><font size='3'>Hora</font></th>
                                                </tr>
                                                <tr class='fila1'>
                                                    $cols_f
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>&nbsp;</td>
                                    </tr>
                                </table>
                                </div>";

                        $fechaini = explode(' ',$rango['fecha_ingreso']);

                        echo "
                        <div align='center'>
                            $info
                            <table >
                                <tr class='encabezadoTabla'>
                                    <td colspan='2' align='center'>Este reporte deja generar máximo [".$dLimite."] días de estancia</td>
                                </tr>
                                <tr class='encabezadoTabla'>
                                    <td align='center'>Fecha ingreso</td>
                                    <td align='center'>Fecha final<br>
                                </tr>
                                <tr class='fila1'>
                                    <td align='center'>
                                        <input onchange='validar_rango_fechas(document.hojamed,\"1\");' type='TEXT' name='wfechainicial' size=10 maxlength=10 id='wfechainicial' readonly='readonly' value='".$fechaini[0]."' class=tipo6>&nbsp;&nbsp;&nbsp;<IMG SRC='/matrix/images/medical/tcx/calendario.jpg' id='trigger1'>

                                        ";
                                        ?>
                                        <script type="text/javascript">//<![CDATA[
                                            Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfechainicial',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
                                        //]]>
                                        </script>
                                        <?php
                        echo "
                                    </td>
                                    <td align='center'>
                                        <input onchange='validar_rango_fechas(document.hojamed,\"2\");' type='TEXT' name='wfechafinal' size=10 maxlength=10 id='wfechafinal' readonly='readonly' value='".sumaDiasAFecha($fechaini[0],$dLimite)."' class=tipo6>&nbsp;&nbsp;&nbsp;<IMG SRC='/matrix/images/medical/tcx/calendario.jpg' id='trigger2'>";
                                        ?>
                                            <script type="text/javascript">//<![CDATA[
                                            Zapatec.Calendar.setup({weekNumbers:false,showsTime:false,timeFormat:'12',electric:false,inputField:'wfechafinal',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
                                        //]]></script>
                                        <?php
                        echo "
                                    </td>
                                </tr>
                                <tr class='boton1'>
                                    <td align='center' colspan='2'></b><input type='submit' value='ACEPTAR'></b></td>
                                    <input type='hidden' name='whis' id='whis' value='".$whis."'>
                                    <input type='hidden' name='wing' id='wing' value='".$wing."'>
                                    <input type='hidden' name='wtipo' id='wtipo' value='".$wtipo."'>
                                    <input type='hidden' name='wrango_fechas' id='wrango_fechas' value='1'>
                                    <input type='hidden' name='wlimite' id='wlimite' value='".$dLimite."'>
                                </tr>
                            </table>
                            <div align='center'>
                                <br/>
                                <br/>
                                <font size=3><a href='Hoja_medicamentos_enfermeria_IPODS.php?wemp_pmla=".$wemp_pmla."&wcco=".$wcco.( $esServicioDomiciliario ? '&servicioDomiciliario=on' : '' )."'> Retornar</a></font>
                            </div>
                        </div>";
                    /******/
                    }
                    else
                    {
                        if(!isset($retornarCodigo)) echo "<br>";
                        if (!$imprimir)
                        {
                            if (isset($wrango_fechas) && $wrango_fechas == 1) // 2012-05-17 Manda a imprimir con el rango de fechas seleccionado si es el caso.
                            {
                                $rfechas = "wfechainicial=".$wfechainicial."&wfechafinal=".$wfechafinal."&wrango_fechas=1";
                                echo "<input type='button' value='Imprimir' onClick=\"javascript:window.open('Hoja_medicamentos_enfermeria_IPODS.php?whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wtipopos=".$wtipopos."&wtipo=".$wtipo."&wemp_pmla=".$wemp_pmla."&imprimir=true&".$rfechas."', '', '$params_imiprimir')\"  >";
                            }
                            else
                            {
                                echo "<input type='button' value='Imprimir' onClick=\"javascript:window.open('Hoja_medicamentos_enfermeria_IPODS.php?whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wtipopos=".$wtipopos."&wtipo=".$wtipo."&wemp_pmla=".$wemp_pmla."&imprimir=true', '', '$params_imiprimir')\"  >";
                            }
                        }

                        if (!isset($wced) and !isset($wtid) and !$imprimir)
                           {
                            echo "<center><table>";
							if($esServicioDomiciliario)
								echo "<td><font size=3><A href='Hoja_medicamentos_enfermeria_IPODS.php?wemp_pmla=".$wemp_pmla."&wcco=".$wcco."&servicioDomiciliario=on'> Retornar</A></font></td>";
							else
								echo "<td><font size=3><A href='Hoja_medicamentos_enfermeria_IPODS.php?wemp_pmla=".$wemp_pmla."&wcco=".$wcco."'> Retornar</A></font></td>";
                            echo "</table></center>";
                           }

                        //editado 2012-04-24
                        // ENCABEZADO DE LA TABLA
                        /*echo "<br>
                                <center></center>
                                <div>";*/
						if( !isset($retornarCodigo	) && !$imprimir ){
							$checktodos=""; $checkpos=""; $checknopos = "";
							if( !isset($wtipopos) ){
								$checktodos = "checked";
							}else{
								if($wtipopos == "todos")
									$checktodos = "checked";
								else if($wtipopos == "on")
									$checkpos = "checked";
								else if( $wtipopos == 'off' )
									$checknopos = "checked";
							}
							echo "<span align='right'>
									TODOS<input type='radio' name='wtipoposcambio' value='todos' onclick='tipoMedicamento()' {$checktodos}>&nbsp;
									POS<input type='radio' name='wtipoposcambio' value='on' onclick='tipoMedicamento()' {$checkpos}/>&nbsp;
                                    NO POS<input type='radio' name='wtipoposcambio' value='off' onclick='tipoMedicamento()' {$checknopos}/></span>";
                            echo "<span style='margin-left: 10px;'>Tipo de Medicamento: </span><select name='wtipo' onchange='enter()'>";
                            $wtipo1=explode('-',$wtipo);
                            echo "<option value='".$wtipo."'checked>".$wtipo1[1]."</option>";
                            echo "<option value='%-Todos'>Todos</option>";
							
							/*
                            echo "<option value='Q-Citostáticos y Coadyuvantes'>Citostáticos y Coadyuvantes</option>";
                            echo "<option value='DA-Dosis Adaptada'>Dosis Adaptada</option>";
                            echo "<option value='NU-Nutricion Parenteral'>Nutrición parenteral</option>";
                            echo "<option value='NE-No Esteril'>No esteril</option>";
                            echo "<option value='OT-Generales'>Generales</option>";
							*/
							//Modificacion 18 de Febrero 2019 , Freddy Saenz
							echo buscarGrupos();
							
                            echo "</select>";
							echo "<input type='hidden' name='whis' id='whis' value='".$whis."'>";
                            echo "<input type='hidden' name='wing' id='wing' value='".$wing."'>";
						}
						if(!isset($retornarCodigo))
                                echo "<table id='tabla_ppal' class='tipoTABLE' width='1070px' border=1>";
                        else{
						  echo "<style type='text/css'>
									.nobreakhm {
										page-break-inside: avoid;
									}
									.tablahojamed td{
										font-size:7pt;
									}
									.encabezadoTabla {
										font-size: 8pt;
									}
								</style>";							
							echo "<table id='tabla_ppal' cellspacing=0 border=1 class='tablahojamed' bordercolor='GRAY' width='98%'>";
							
						}
						
						$colspan = $horasPares + 5;
						$encabezado_tabla = "";
						if(isset($retornarCodigo)){
							$encabezado_tabla = "
										<tr class=seccion1>
                                            <td id='td_his' colspan=".(floor($colspan*0.5))."><div class='nobreakhm'><b>SERVICIO:</b>".$wnomser."</div></td>
                                            <td id='td_cam' colspan=".(floor($colspan*0.5))." ><div class='nobreakhm'><b>CAMA:</b>".$whab."</div></td>
                                        </tr>";
						}else{
							$encabezado_tabla = "
                                        <tr class=seccion1>
                                            <td id='td_lhis' colspan=".(floor($colspan*0.2))."><div class='nobreakhm'><b>HISTORIA N°:</b>".$whis." - ".$wing."</div></td>
                                            <td id='td_his' colspan=".(ceil($colspan*0.6))."><div class='nobreakhm'><b>SERVICIO:</b>".$wnomser."</div></td>
                                            <td id='td_cam' colspan=".(floor($colspan*0.2))." ><div class='nobreakhm'><b>CAMA:</b>".$whab."</div></td>
                                        </tr>";
							
							$mensaje_hoja_med_ipod = consultarAliasPorAplicacion($conex, $wemp_pmla, 'mensaje_hoja_med_ipod');
							
							if( !isset($wtipopos) || (isset($wtipopos) && ($wtipopos == 'todos' || $wtipopos == '') )){			
								$encabezado_tabla.="<tr class=seccion1>
														<td id='td_pac' colspan=".$colspan."><div class='nobreakhm'><b>PACIENTE:</b>".$wpac."</div><div class='fondoAmarillo'><b><font size='2'>".$mensaje_hoja_med_ipod."</font></b></div></td>	
													</tr>";
							}else if( $wtipopos == 'on'){								
								$encabezado_tabla.="<tr class=seccion1>
														<td id='td_pac' colspan=".(floor($colspan*0.7))."><div class='nobreakhm'><b>PACIENTE:</b>".$wpac."</div><div class='fondoAmarillo'><b><font size='2'>".$mensaje_hoja_med_ipod."</font></b></div></td>
														<td id='td_pac2' colspan=".(ceil($colspan*0.3))."><div class='nobreakhm'><b>MEDICAMENTOS POS</div></td>
													</tr>";
							}else if( $wtipopos == 'off' ){
								$encabezado_tabla.="<tr class=seccion1>
														<td id='td_pac' colspan=".(floor($colspan*0.7))."><div class='nobreakhm'><b>PACIENTE:</b>".$wpac."</div><div class='fondoAmarillo'><b><font size='2'>".$mensaje_hoja_med_ipod."</font></b></div></td>
														<td id='td_pac2' colspan=".(ceil($colspan*0.3))."><div class='nobreakhm'><b>MEDICAMENTOS NO POS</div></td>
													</tr>";
							}
                        }
                        $wtipo1 = explode('-',$wtipo);
                        $encabezado_tabla.="<tr class=seccion1><td id='td_pac' colspan=".$colspan."><div class='nobreakhm'><b>TIPO DE MEDICAMENTOS: </b>".$wtipo1[1]."</div></td></tr>";


                        // 2012-05-17 - Si existió un paso intermedio para seleccionar un rango de fechas
                        //-- Se adiciona un filtro de fechas al query del reporte para que filtre tambien por fechas
                        $filtro_fec_query1_2 = '';
                        $filtro_fec_query3_4 = '';
                        if (isset($wrango_fechas) && $wrango_fechas == 1)
                        {
                            $filtro_fec_query1_2 = "AND aplfec BETWEEN '".$wfechainicial."' AND '".$wfechafinal."'";
                            $filtro_fec_query3_4 = "AND jusfec BETWEEN '".$wfechainicial."' AND '".$wfechafinal."'";

                            echo "  <input type='hidden' name='wfechainicial' id='wfechainicial' value='".$wfechainicial."'>
                                    <input type='hidden' name='wfechafinal' id='wfechafinal' value='".$wfechafinal."'>
                                    <input type='hidden' name='wrango_fechas' id='wrango_fechas' value='1'>";
                        }
                        // --
						
						$and_art_posnopos = "";
						if(isset($wtipopos)){
							if( $wtipopos == 'on' )
								$and_art_posnopos = "	AND Artpos = 'P' ";
							else if( $wtipopos == 'off' )
								$and_art_posnopos = "	AND Artpos != 'P' ";	
						}else
							$wtipopos = "todos";
                        echo "  <input type='hidden' name='wtipopos' id='wtipopos' value='".$wtipopos."'>";
                        
                        // 2018-07-09 - Se añade filtro de tipo de medicamento
                        
                        $and_arktip = '';
                        $and_tiptpr = '';
                        
                       // echo " grupos y codigos " . implode (" + ",$wtipo1);
                        //cambiar A,B,C por 'A','B','C' que se usara en el query de IN ('A','B','C')
                        $codgruposIN = "'" . str_replace(",", "','", $wtipo1[0] ) . "'";
                        //Modificacion : 2019 Febrero 20 Freddy Saenz
                        //Basado en la tabla movhos_000099 , ver los codigos agrupados por tipos
                        // Si $wtipo1[0] es % , no se filtra (son todos)
                        //SELECT * FROM `matrix`.`movhos_000068` WHERE Arktip IN ( $wtipo1[0] )
                        //('Q', 'QT') AND 
						
                        switch ($wtipo1[0]) {
							
                          case '%' :
                          case '*' :
						  case 'TODOS' :
						  case ''  ://27 Febrero 2019, Freddy Saenz , para que abra con todos los medicamentos desde la
						  //ventana de facturacion clinica.
						  case 'Todos':
						  case 'todos' :
						  
                            break;
                          default :
                              $and_arktip = " AND Arktip IN ($codgruposIN) " ;
                              //$and_arktip = " AND Tarcod IN ($codgruposIN) " ;//20  febrero 2019
                              $and_tiptpr = " AND Tiptpr IN ($codgruposIN) ";
                            break;

                        }
            //Modificacion 18 de Febrero 2019  Freddy Saenz
            //Quitar insulinas y analgesias , o cualquier otro medicamento que este en la tabla movhos_000091  
            //para configurar correctamente Citostáticos y Coadyuvantes
            //Arkcod not in ( SELECT Arscod FROM `matrix`.`movhos_000091` WHERE Arstip <> '' );
//Arkcod not in ( SELECT Arscod FROM `matrix`.`movhos_000091`  );

                        if ($and_arktip != ''){
                           $and_arktip .= " AND Arkcod NOT IN ( SELECT Arscod FROM " . $wbasedato . "_000091 ) ";
                         // $and_arktip .= " AND Arkcod NOT IN ( SELECT Arscod FROM " . $wbasedato . "_000091 WHERE Arstip <> '' ) ";
                        }
					
			//,".$wbasedato."_000099
      // and  Tarcod = B.Arktip                  



                       $q = " CREATE TEMPORARY TABLE if not exists TEMPO as
                                SELECT  UPPER(aplart) art, aplfec fec, aplron ron, aplcan can, artcom com, aplufr ufr, apldos dos, artgen gen, 'on' apl, '' jus, ccokar kar, aplcco cco, Aplvia, DATE_FORMAT(A.Hora_data, '%H:%i' ) as Aplhor
                                FROM    ".$wbasedato."_000015 A LEFT JOIN ".$wbasedato."_000068 B ON A.Aplart = B.Arkcod, ".$wbasedato."_000026, ".$wbasedato."_000029, ".$wbasedato."_000011

                                

                                WHERE   aplhis                                = '".$whis."'
                                        AND apling                            = '".$wing."'
                                        AND aplest                            = 'on'
                                        AND aplart                            = artcod                                        
                                        AND mid(artgru,1,instr(artgru,'-')-1) = gjugru
                                        AND gjujus                            = 'on'
                                        AND aplcco                            = ccocod

                                       

                                        $and_art_posnopos
                                        $and_arktip
                                        $filtro_fec_query1_2

                                UNION ALL

                                SELECT  UPPER(aplart) art, aplfec fec, aplron ron, aplcan can, artcom com, aplufr ufr, apldos dos, artgen gen, 'on' apl, '' jus, ccokar kar, aplcco cco, Aplvia, DATE_FORMAT(A.Hora_data, '%H:%i' ) as Aplhor
                                FROM    ".$wbasedato."_000015 A, ".$wcenmez."_000002, ".$wbasedato."_000011, ".$wcenmez."_000001
                                WHERE   aplhis      = '".$whis."'
                                        AND apling  = '".$wing."'
                                        AND aplest  = 'on'
                                        AND aplart  = artcod
                                        AND Arttip  = Tipcod
                                        AND aplart  NOT IN (SELECT artcod FROM ".$wbasedato."_000026)
                                        AND aplcco  = ccocod
                                        $and_tiptpr
                                        $filtro_fec_query1_2";

                            //Agosto 22 de 2011
                            //Aca traigo todos los registros de las rondas que NO se aplicaron


                                        

                        $q .= "
                                UNION ALL

                                SELECT  UPPER(jusart) art, jusfec fec, jusron ron, 0 can, artcom com, '' ufr, '' dos, artgen gen, 'off' apl, jusjus jus, '' kar, '' cco, '' as Aplvia, '' as Aplhor
                                FROM    ".$wbasedato."_000113 A LEFT JOIN ".$wbasedato."_000068 B ON A.jusart = B.Arkcod , ".$wbasedato."_000026, ".$wbasedato."_000029

                               

                                WHERE   jushis                            = '".$whis."'
                                        AND jusing                            = '".$wing."'
                                        AND jusart                            = artcod
                                        AND mid(artgru,1,instr(artgru,'-')-1) = gjugru
                                        AND gjujus                            = 'on'

                                         
                                        $and_art_posnopos
                                        $and_arktip
                                        $filtro_fec_query3_4

                                UNION ALL

                                SELECT  UPPER(jusart) art, jusfec fec, jusron ron, 0 can, artcom com, '' ufr, '' dos, artgen gen, 'off' apl, jusjus jus, '' kar, '' cco, '' as Aplvia, '' as Aplhor
                                FROM    ".$wbasedato."_000113, ".$wcenmez."_000002, ".$wcenmez."_000001

                                WHERE   jushis      = '".$whis."'
                                        AND jusing  = '".$wing."'
                                        AND jusart  = artcod
                                        AND Arttip  = Tipcod
                                        AND jusart  NOT IN (SELECT artcod FROM ".$wbasedato."_000026)
                                        $and_tiptpr
                                        $filtro_fec_query3_4
                                ORDER BY 2 desc, 1, 3 ";
 
                            //On
                            //	   if ($whis == "422057" )
                            //	       echo $q."<br>";

                         // echo '<pre>'; print_r($q); echo '</pre><hr>';




                        $res3 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

                        $q = " SELECT UPPER(art), fec, ron, SUM(can), com, ufr, SUM(dos), gen, apl, jus, kar, cco, Aplvia, Aplhor "
                            ."   FROM TEMPO "
                            ."  GROUP BY 1, 2, 3, 5, 6, 8, 9, 10, 11, 12, 13 "
                            ."  ORDER BY 2 desc, 1, 3 ";
                        $res3 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                        $wnr = mysql_num_rows($res3);
						
						$numret = mysql_num_rows($res3);
						if( isset($retornarCodigo) && $numret < 1){
							echo "SINDATOS";
							return;
						}					
						

                        //Inicializo la MATRIZ a donde voy a llevar todo lo que le aplicaron al paciente en la estadia
                        for ($j=0;$j<=$wnr;$j++)
                        {
                            for ($l=0;$l<=23;$l++)     //24 horas que tiene el dia
                            {
                                $Mrondas[$j][$l]=0;    //Aca almaceno las cantidades de cada articulo segun la ronda
                            }
                            $Afechas[$j]   =0;         //Aca llevo las fecha de aplicacion
                            $Aarticulos[$j]=0;         //Aca llevo el codigo del articulo de acuerdo a la hora de la ronda
                            $Adesc[$j]     ="";        //Aca llevo la descripcion del articulo de acuerdo a la hora de la ronda
                        }

                        $j=1;
                        $i=1;
                        $row = mysql_fetch_row($res3);
						$arrayMedicamentos = array();
                        while ($j <= $wnr)
                        {
                            $wfec  = $row[1];
                            $wart  = $row[0];
                            $wdesc = $row[4];

                            $Afechas[$i]    = $row[1];
                            $Aarticulos[$i] = $row[0];
                            $Adesc[$i]      = $row[4];    //Nombre Comercial
							$Avia[$i]		= consultarVia( $conex, $wbasedato, $row[12] );                    //Indica la via             Abril 28 de 2011
                            $Agen           = $row[7];    //Nombre Generico
                            $Akar           = $row[10];   //Es electronico
                            $Acco           = $row[11];   //CCo donde se aplico
							
							
							
							if(!isset($arrayMedicamentos[$Aarticulos[$i]]))
							{
								$arrayMedicamentos[$Aarticulos[$i]] = $Aarticulos[$i];
							}

                            while ($wart==$row[0] and $wfec==$row[1] and $wdesc==$row[4])
                            {
                                $wronda=(integer)tomar_ronda($row[2]);

                                /*  Nov 3 de 2011
                                if ((isset($wfrac) and $wfrac==0) or $Akar != "on")      //Si tiene Kardex Electronico (directo) en centro de costos _000011
                                {
                                 $Mrondas[$i][$wronda]=$Mrondas[$i][$wronda]+$row[3];
                                 if ($Akar == "on")                                   //Si tiene Kardex Electronico (directo) en centro de costos _000011
                                    $Mrondas[$i]["fraccion"]="Sin Fracción";
                                   else
                                      $Mrondas[$i]["fraccion"]="";
                                }
                                else //tiene fraccion   */
								
								$Avia[$i]		= ( empty( $Avia[$i] ) ) ? consultarVia( $conex, $wbasedato, $row[12] ) : $Avia[$i];

                                $Mrondas[$i][$wronda]=$Mrondas[$i][$wronda]+$row[6];    //Cantidad (este dato es la dosis que se coloco en el kardex)
                                $Mrondas[$i]["fraccion".$wronda]=$row[5];               //Fraccion que hay en la 000015 basado en la dosis del kardex
                                $Mrondas[$i]["apl".$wronda]=$row[8];                    //Indica que fue aplicado o NO             Agosto 22 de 2011
                                $Mrondas[$i]["viacod".$wronda]=$row[12];                    //Indica la via             Abril 28 de 2011
                                $Mrondas[$i]["hora_apl".$wronda]=$row[13];                    //Indica la via             Abril 28 de 2011
                                $wjus=explode("-",$row[9]);                             //Agosto 22 de 2011

                                if (isset($wjus[1]) and $wjus[1] != "")                 //                                         Agosto 22 de 2011
                                    $Mrondas[$i]["jus".$wronda]=$wjus[1];               //Grabo la justificación pero sin código   Agosto 22 de 2011
                                else                                                    //                                         Agosto 22 de 2011
                                    $Mrondas[$i]["jus".$wronda]=$row[9];                //Justificacion                            Agosto 22 de 2011

                                $row = mysql_fetch_row($res3);
                                $j++;
                            }
                            $i++;
                        }
						
						$productosCMAplicados = array();
						if(count($arrayMedicamentos)>0)
						{
							foreach($arrayMedicamentos as $keyMedicamento => $valueProducto)
							{
								// Si es un producto de central de mezclas y el tipo esta configurado como Imprimir medicamento original 
								// debe mostrar el nombre del medicamento original y no el nombre del producto
								$imprimeMedicamentoOriginal = consultarImprimeMedicamentoOriginal($conex,$wcenmez,$keyMedicamento);
								
								if($imprimeMedicamentoOriginal)
								{
									// consulta en movhos_000054 el articulo anterior al reemplazo
									$nombreMedicamento = consultarMedicamentoProductoCM($conex,$wbasedato,$wcenmez,$whis,$wing,$keyMedicamento);
									
									if(count($nombreMedicamento)>0)
									{
										$productosCMAplicados[$keyMedicamento] = $nombreMedicamento;
									}
								}
							}
						}
						
                        $t=$i;

                        $encabezado_tabla .= "
                                    <tr class=encabezadoTabla>
                                        <th id='td_lfec'>FECHA</th>
                                        <th id='td_lcod'>CODIGO</th>
                                        <th id='td_lmed'>MEDICAMENTO</th>
                                        <th id='td_lmed'>VIA</th>";

                        //echo "<th>Unidad</th>";
                        $i=0;
                        while ($i <= 23)
                        {
                            if ($i < 12)
                            { $encabezado_tabla .= "<th nowrap='nowrap' id='tda".$i."'>".$i." AM"."</th>"; }
                            else
                            { $encabezado_tabla .=  "<th nowrap='nowrap' id='tdp".$i."'>".$i." PM"."</th>"; }
                            //$i=$i+1;
                            $i=$i+(ceil(24/$horasPares));
                        }
                        $encabezado_tabla .=  "
                                        <th id='td_tot'>TOTAL</th>
                                    </tr>";

                        echo $encabezado_tabla;

                        echo '<input type="hidden" name="encabezado_tabla" id="encabezado_tabla" value="'.$encabezado_tabla.'" >';

                        $wfec="";
                        $i=1;
                        $k=1;

                        $cont_serv=0;

                        // RECORRER REGISTROS PARA ARMAR LAS FILAS DE LA TABLA
                        $fecha_ant = '';
                        $fila_por_fecha = 1;
                        $td = 0;
                        while ($i < $t)   //Recorro la matriz con cada articulo
                        {
                            $wuni=".";
                            $wnomart=$Adesc[$i];
                            //$wnomgen=$Agen[$i];
                            if ($Aarticulos[$i] != "999")
                            {
                              //$q =  " SELECT artcom, artuni, artgen "
                              $q =  " SELECT artcom, deffru, artgen "
                                   ."   FROM ".$wbasedato."_000026, ".$wbasedato."_000059 "
                                   ."  WHERE artcod = '".$Aarticulos[$i]."'"
                                   ."    AND artcod = defart ";
                              $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                              $wfilas = mysql_num_rows($res);
                              if ($wfilas==0)                                 //Si no existe en movhos lo busco en central de mezclas
                                 {
                                  //$q =  " SELECT artcom, artuni, artgen "
                                  $q =  " SELECT artcom, deffru, artgen "
                                       ."   FROM ".$wcenmez."_000002, ".$wbasedato."_000059 "
                                       ."  WHERE artcod = '".$Aarticulos[$i]."'"
                                       ."    AND artcod = defart ";
                                  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                                  $wfilas = mysql_num_rows($res);  //Nov 3 2011

                                  if ($wfilas==0)   //Nov 3 2011                              //Si no existe en movhos lo busco en central de mezclas
                                     {
                                      $q =  " SELECT artcom, artuni, artgen "
                                           ."   FROM ".$wcenmez."_000002 "
                                           ."  WHERE artcod = '".$Aarticulos[$i]."'";
                                      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                                      $wfilas = mysql_num_rows($res);

                                      if ($wfilas==0)                                 //Si no existe en movhos lo busco en central de mezclas
                                         {
                                          $q =  " SELECT artcom, artuni, artgen "
                                               ."   FROM ".$wbasedato."_000026 "
                                               ."  WHERE artcod = '".$Aarticulos[$i]."'";
                                          $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                                         } //Nov 3 2011
                                     }     //Nov 3 2011
                                 }
                              $row = mysql_fetch_array($res);

                              $wnomart = $row[0];
                              $wuni    = $row[1];
                              $wgen    = $row[2];
                            }

                            //Traigo la cantidad de articulos(distintos) por cada FECHA
                            // $q = " SELECT COUNT(DISTINCT(art)) "   //
                            $q = " SELECT COUNT(DISTINCT(art)) "
                              ."   FROM TEMPO "
                              ."  WHERE fec  = '".$Afechas[$i]."'";
                            $res4 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                            $wfilas = mysql_fetch_array($res4);

                            if (is_integer(($i+$cont_serv)/2))
                            { $wclass = "fila1"; }
                            else
                            { $wclass = "fila2"; }

							$wsaltopag = "";
							if( isset($retornarCodigo) ) $wsaltopag = "<br>";
								
                            // if (!$tag_tr)
                             echo "<tr>"; 

                            $fl_tmp = $wfilas[0];
                            $atributo_celda = "id='".$Afechas[$i]."|".$fl_tmp."|".$fila_por_fecha."|[CONT]'";
                            $atributo_f = str_replace('[CONT]','f',$atributo_celda);
							//if( !isset($retornarCodigo) ){
								if ($wfec != $Afechas[$i])
								{
									// echo "<tr><td bgcolor=DDDDDD colspan=29>&nbsp;</td></tr>";
									// echo "<tr>";
									echo "  <td $atributo_f
												class='".$wclass."'
												rowspan=".$fl_tmp."
												align=center>
													<div class='nobreakhm'>
													<font size=2>
														<b>".$Afechas[$i]."</b>
													</font>
													</div>
											</td>"; //editado 2012-04-24
									// echo "</tr>";
									$wfec = $Afechas[$i];
								}
							/*}else {
								if ($wfec != $Afechas[$i])
								{
									echo "  <td class='".$wclass."'
												align=center>
													<div class='nobreakhm'>
													<font size=2>".$wsaltopag."
														<b>".$Afechas[$i]."</b>
													</font>
													</div>
											</td>"; //editado 2012-04-24
									$wfec = $Afechas[$i];
								}else{
									echo "<td><div class='nobreakhm'>".$wsaltopag."</div></td>"; 
								}
							}*/
							
							// Si $Aarticulos[$i] es un producto de central de mezclas debe mostrar el nombre comercial y genérico 
							// del artículo previo al reemplazo
							if(isset($productosCMAplicados[$Aarticulos[$i]]))
							{
								$wnomart = $productosCMAplicados[$Aarticulos[$i]]['comercial'];
								$wgen = $productosCMAplicados[$Aarticulos[$i]]['generico'];
							}
							
                            $wsuspendido=false;
                            $wsuspendido=buscarSiEstaSuspendido($whis, $wing, $Aarticulos[$i], $Afechas[$i]);    //, &$whorsus);

                            $atributo_c = str_replace('[CONT]','c',$atributo_celda);
                            $atributo_d = str_replace('[CONT]','d',$atributo_celda);
                            echo "<td $atributo_c class='".$wclass."'><div class='nobreakhm'>".$wsaltopag."".$Aarticulos[$i]."</div></td>";                                  //Codigo Articulo
                           // echo "<td $atributo_d class='".$wclass."'>Com.: ".$wnomart."<br>Gen.: ".$wgen."</td>";                //Nombre Comercial y Generico
						    $texton = "Com.: ".$wnomart."<br>Gen.: ".$wgen;
							if( $wnomart == $wgen ) $texton = "Com. y Gen.:<br> ".$wnomart;
                            echo "<td $atributo_d class='".$wclass."'><div class='nobreakhm'>".$wsaltopag."".$texton."</div></td>";                //Nombre Comercial y Generico
                            echo "<td $atributo_d class='".$wclass."' align=center><div style='font-size:6pt'>".$Avia[$i]."</div></td>";                //Nombre Comercial y Generico
                            //echo "<td align=center>".$wuni."</td>";                               //Unidad de Medida
                            $j=0;
                            $wtotal=0;
							$widhtheightimages = " width=25 height=25 ";
							$iconoCheck = "checkmrk.ico";
							
							$hostUrl = "";
							if( isset($retornarCodigo) ){
								$hostUrl = "http://".getenv("REMOTE_ADDR");
								$iconoCheck = "checkmrk.png";
								$widhtheightimages = " width=21 height=21 ";
							}
                            while ($j <= 23)
                            {
                                    if ($j >= 12)
                                    {
                                        if ($j == 12)
                                        { $wmsg=$j." PM"; }
                                        else
                                        { $wmsg=($j-12)." PM"; }
                                    }
                                    else
                                    { $wmsg=$j." AM"; }
                                    $atributo_id = str_replace('[CONT]',$j,$atributo_celda);

                                    //Agosto 22 de 2011 - page_cross.gif   msgbox04.ico
                                    if ($Mrondas[$i][$j] == 0)
                                    {										
                                        if (isset($Mrondas[$i]["jus".$j]) and  $Mrondas[$i]["jus".$j] != '')
                                        {
                                            echo "<td $atributo_id class='".$wclass."' align=center title='".$wmsg.", No se aplicó: ".$Mrondas[$i]["jus".$j]."'><div class='nobreakhm'>".$wsaltopag."<img ".$widhtheightimages." class='small' src='".$hostUrl."/matrix/images/medical/movhos/info.png'></div></td>";
                                        }
                                        else
                                        { 
											echo "<td $atributo_id class='".$wclass."' align=center><div class='nobreakhm'>".$wsaltopag."</div></td>"; 
										}
                                        if (!isset($wultfra))
                                         $wultfra = '';   //Ultima Unidad de Medida
                                    }
                                    else
                                    {										
                                        $wultapl=buscarUltimaAplicacionDia($Mrondas, $i, $j);
                                        if ($wsuspendido and $wultapl=="on")
                                        {
                                            echo "<td $atributo_id class='".$wclass."' align=center bgcolor='FEAAA4' title='".$wmsg.", Luego de esta aplicación fue Suspendido'><div class='nobreakhm'>".$wsaltopag."<img ".$widhtheightimages." class='small' src='".$hostUrl."/matrix/images/medical/movhos/".$iconoCheck."'> ".$Mrondas[$i][$j]."<br>".$Mrondas[$i]["fraccion".$j]."<br><font size='1'>(".$Mrondas[$i]["hora_apl".$j].")</font><br></div></td>";
                                        }
                                        else
                                        { 
											echo "<td $atributo_id class='".$wclass."' align=center title='".$wmsg."'><div class='nobreakhm'>".$wsaltopag."<img ".$widhtheightimages." class='small' src='".$hostUrl."/matrix/images/medical/movhos/".$iconoCheck."'><br>".$Mrondas[$i][$j]."<br>".$Mrondas[$i]["fraccion".$j]."<br><font size='1'>(".$Mrondas[$i]["hora_apl".$j].")</font><br></div></td>"; 
										}
                                        $wtotal = $wtotal + $Mrondas[$i][$j];
                                        $wultfra= $Mrondas[$i]["fraccion".$j];   //Ultima Unidad de Medida
                                    }
                                  //  $j++;
                                    $j= $j +(ceil(24/$horasPares));
                            }

                            $atributo_t = str_replace('[CONT]','T',$atributo_celda);
                            //echo "<td align=right><b>".$wtotal." ".$Mrondas[$i]["fraccion".($j-1)]."</b></td>";
                            echo "<td $atributo_t class='".$wclass."' align='right'><div class='nobreakhm'>".$wsaltopag."<b>".$wtotal."<br>".$wultfra."</b></div></td>";
                            echo "</tr>";
                            // $tag_tr = false;
                            $i++;

                            if ($fecha_ant == $Afechas[$i] || ($fecha_ant == '' && $fila_por_fecha == 1))// || $fecha_ant == ''
                            {
                                $fila_por_fecha++;
                            }
                            else
                            {
                                $fila_por_fecha = 1;
                            }
                            // echo $fila_por_fecha.'|'.$fecha_ant.'|'.$Afechas[$i].'<hr>';
                            $fecha_ant = $Afechas[$i];
                        }
                        echo "</table>";
                        if (isset($imprimir) && $imprimir == true && !isset($retornarCodigo) )
                        {
                            echo '  <script language="Javascript">
                                        paginar(document.getElementById("tabla_ppal"),document.forms[0],"Hoja Medicamentos")
                                        //fn_imprimir();
                                    </script>';

                        }

                        if (!isset($wced) and !isset($wtid) and !$imprimir)
                           {
                            echo "<center><table>";
							if($esServicioDomiciliario)
								echo "<tr><td><font size=3><A href='Hoja_medicamentos_enfermeria_IPODS.php?wemp_pmla=".$wemp_pmla."&wcco=".$wcco."&servicioDomiciliario=on'> Retornar</A></font></td></tr>";
							else
								echo "<tr><td><font size=3><A href='Hoja_medicamentos_enfermeria_IPODS.php?wemp_pmla=".$wemp_pmla."&wcco=".$wcco."'> Retornar</A></font></td></tr>";
                            echo "</table></center>";
                           }

                        echo "<br>";
                        if (!$imprimir)
                        {
                            if (isset($wrango_fechas) && $wrango_fechas == 1) // 2012-05-17 Manda a imprimir con el rango de fechas seleccionado si es el caso.
                            {
                                $rfechas = "wfechainicial=".$wfechainicial."&wfechafinal=".$wfechafinal."&wrango_fechas=1";
                                echo "<input type='button' value='Imprimir' onClick=\"javascript:window.open('Hoja_medicamentos_enfermeria_IPODS.php?whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wemp_pmla=".$wemp_pmla."&wtipopos=".$wtipopos."&imprimir=true&".$rfechas."', '', '$params_imiprimir')\"  >"; ///onClick='window.print()'
                            }
                            else
                            {
                                echo "<input type='button' value='Imprimir' onClick=\"javascript:window.open('Hoja_medicamentos_enfermeria_IPODS.php?whis=".$whis."&wing=".$wing."&wcco=".$wcco."&wemp_pmla=".$wemp_pmla."&wtipopos=".$wtipopos."&imprimir=true', '', '$params_imiprimir')\"  >"; ///onClick='window.print()'
                            }
                        }
                    }
                  }
                  else   //Septiembre 27 de 2011
                     {
                      ?>
                       <script>
                         alert("No tiene PERMISO para acceder a esta Hístoria");
                       </script>
                      <?php

                      unset($whis);
                      unset($wing);
                      echo "<meta http-equiv='refresh' content='0;url=Hoja_medicamentos_enfermeria_IPODS.php?wemp_pmla=".$wemp_pmla."&wcco=".$wcco."'>";
                     }
               }
        }

       if (!isset($wced) and !isset($wtid) and !$imprimir)
          {
           echo "<br>";
           echo "<center><table>";
           echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
           echo "</table></center>";
          }
    }
    include_once("free.php");

    if (isset($imprimir) && $imprimir == true && !isset($retornarCodigo))
    {
    ?>
        <style type="text/css">

            @media print
            {
                .page-break { page-break-before: always; }
            }
            .saltopagina{page-break-after: always}

            table{  font-size:12px; font-family: Arial, Helvetica, sans-serif; }
            table.tipoTABLE { }
            th {  font-size:0.6886em; font-family: Arial, Helvetica, sans-serif; }
            b  { font-size: 11px; font-family: Arial, Helvetica, sans-serif; }

            img.small{width: 22px; height: 22px;}
        </style>
    <?php
    }else if(isset($retornarCodigo)){
		return;    
	}
    ?>
    </body>
    </html>
<?php
}
?>

<?php
/*
QUERY DE EJEMPLO


CREATE TEMPORARY TABLE if not exists TEMPO as
SELECT  UPPER(aplart) art, aplfec fec, aplron ron, aplcan can, artcom com, aplufr ufr, apldos dos, artgen gen, 'on' apl, '' jus, ccokar kar, aplcco cco, Aplvia, DATE_FORMAT(A.Hora_data, '%H:%i' ) as Aplhor
                                FROM    movhos_000015 A LEFT JOIN movhos_000068 B ON A.Aplart = B.Arkcod, movhos_000026, movhos_000029, movhos_000011

                                

                                WHERE   aplhis                                = '606791'
                                        AND apling                            = '9'
                                        AND aplest                            = 'on'
                                        AND aplart                            = artcod                                        
                                        AND mid(artgru,1,instr(artgru,'-')-1) = gjugru
                                        AND gjujus                            = 'on'
                                        AND aplcco                            = ccocod

                                       

                                        
                                         AND Arktip IN ('U','LQ','NE','T','LC','CS','N','CD')  AND Arkcod NOT IN ( SELECT Arscod FROM movhos_000091 ) 
                                        

                                UNION ALL

                                SELECT  UPPER(aplart) art, aplfec fec, aplron ron, aplcan can, artcom com, aplufr ufr, apldos dos, artgen gen, 'on' apl, '' jus, ccokar kar, aplcco cco, Aplvia, DATE_FORMAT(A.Hora_data, '%H:%i' ) as Aplhor
                                FROM    movhos_000015 A, cenpro_000002, movhos_000011, cenpro_000001
                                WHERE   aplhis      = '606791'
                                        AND apling  = '9'
                                        AND aplest  = 'on'
                                        AND aplart  = artcod
                                        AND Arttip  = Tipcod
                                        AND aplart  NOT IN (SELECT artcod FROM movhos_000026)
                                        AND aplcco  = ccocod
                                         AND Tiptpr IN ('U','LQ','NE','T','LC','CS','N','CD') 
                                        
                                UNION ALL

                                SELECT  UPPER(jusart) art, jusfec fec, jusron ron, 0 can, artcom com, '' ufr, '' dos, artgen gen, 'off' apl, jusjus jus, '' kar, '' cco, '' as Aplvia, '' as Aplhor
                                FROM    movhos_000113 A LEFT JOIN movhos_000068 B ON A.jusart = B.Arkcod , movhos_000026, movhos_000029

                               

                                WHERE   jushis                            = '606791'
                                        AND jusing                            = '9'
                                        AND jusart                            = artcod
                                        AND mid(artgru,1,instr(artgru,'-')-1) = gjugru
                                        AND gjujus                            = 'on'

                                         
                                        
                                         AND Arktip IN ('U','LQ','NE','T','LC','CS','N','CD')  AND Arkcod NOT IN ( SELECT Arscod FROM movhos_000091 ) 
                                        

                                UNION ALL

                                SELECT  UPPER(jusart) art, jusfec fec, jusron ron, 0 can, artcom com, '' ufr, '' dos, artgen gen, 'off' apl, jusjus jus, '' kar, '' cco, '' as Aplvia, '' as Aplhor
                                FROM    movhos_000113, cenpro_000002, cenpro_000001

                                WHERE   jushis      = '606791'
                                        AND jusing  = '9'
                                        AND jusart  = artcod
                                        AND Arttip  = Tipcod
                                        AND jusart  NOT IN (SELECT artcod FROM movhos_000026)
                                         AND Tiptpr IN ('U','LQ','NE','T','LC','CS','N','CD') 
                                        
                                ORDER BY 2 desc, 1, 3
*/

?>