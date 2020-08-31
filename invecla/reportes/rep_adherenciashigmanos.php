<html>
<head>
<title>MATRIX - [REPORTE ADHERENCIA EN HIGIENE DE MANOS]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_adherenciashigmanos.php'; 
	}
	
	function enter()

	{
		document.forms.rep_adherenciashigmanos.submit();
	}
	
	function cerrarVentana()
	{
	 window.close()
	}
	
		function ltrim(s) {
	   return s.replace(/^\s+/, "");
	}
	
	function rtrim(s) {
	   return s.replace(/\s+$/, "");
	}


	function trim(s) {
	   //return rtrim(ltrim(s));
	   return s.replace(/^\s+|\s+$/g,"");
	}

	function addCCO( cmOrigen, cmDestino ){

		if( cmDestino.value.indexOf( "% - Todos" ) > -1  ){
			return;
		}
		
		valor = cmOrigen.options[ cmOrigen.options.selectedIndex ].text;

		if( valor != "% - Todos" ){
		
			pos = cmDestino.value.indexOf( valor );
		
			if( pos == -1 ){

				if( cmDestino.value.length > 0 ){
					cmDestino.value = cmDestino.value+"\r";
				}
			
				cmDestino.value = trim( cmDestino.value+valor );

				if( cmDestino.name == "txGrupos" ){
					gruposChange(0);
				}
			}
		}
		else{
			cmDestino.value = valor;
			if( cmDestino.name == "txGrupos" ){
				gruposChange(0);
			}
		}
	}

	function removeCCO( cmOrigen, cmDestino ){

		pos = cmDestino.value.indexOf( trim( cmOrigen.options[ cmOrigen.options.selectedIndex ].text) );

		if( pos > -1 )
		{
			valor = cmDestino.value.substring( 0, pos-1 );
			valor = trim(valor)+cmDestino.value.substring( pos+cmOrigen.options[ cmOrigen.options.selectedIndex ].text.length, cmDestino.value.length );

			cmDestino.value=valor;

			if( cmDestino.value.indexOf("\n") == 0 ){
				cmDestino.value = cmDestino.value.substring( 1, cmDestino.value.length );
			}else if( cmDestino.value.indexOf("\n") == 1 ){
				cmDestino.value = cmDestino.value.substring( 2, cmDestino.value.length );
			}
		}

		if( cmDestino.name == "txGrupos" ){
			gruposChange(0);
		}
	}

	function gruposChange( valor ){
		document.mainmenu.Menu.value = valor;
		document.mainmenu.submit();
	}
	
	function tablaRowsPan( fila, expansion ){
        var tabla = document.getElementById('tbInformacion');

        tabla.rows[ fila ].cells[0].rowSpan = expansion;
        tabla.rows[ fila ].cells[1].rowSpan = expansion;
        tabla.rows[ fila ].cells[2].rowSpan = expansion;
        tabla.rows[ fila ].cells[3].rowSpan = expansion;
        tabla.rows[ fila ].cells[4].rowSpan = expansion;
        tabla.rows[ fila ].cells[5].rowSpan = expansion;
        tabla.rows[ fila ].cells[9].rowSpan = expansion;
    }

    //verfica que un option exista con el texto exista
    function existeOption( campo, texto ){

        for( var i = 0; i < campo.options.length; i++){
            if( campo.options[ i ].text == texto ){
                return true;
            }
        }

        return false;       
    }
	  

	//agregar un option a un campo select
	//opciones debe ser un array
    function agregarOption( slCampos, opciones ){

    	if( slCampos.tagName.toLowerCase() == "select" ){
	    	//agrengando options
			for( var i = 0; i < opciones.length; i++ ){
				var auxOpt = document.createElement( "option" );
				slCampos.options.add( auxOpt, 0 );
				auxOpt.innerHTML = opciones[i];
			}
    	}
    }

	//campoDestino id del campo destino
	//campoOrigen id del campo origen
    function agregarOptionASelect( cmpOrigen, campoDestino, textoDestino ){

		var cmpDestino = document.getElementById( campoDestino );
		
        if( !existeOption( cmpDestino, cmpOrigen.options[cmpOrigen.selectedIndex].text ) ){

			if( cmpOrigen.options[ cmpOrigen.selectedIndex ].text == "% - Todos" ){
				
				var numOptions = cmpDestino.options.length;
				for( var i = 0; i <  numOptions; i++ ){
					cmpDestino.removeChild( cmpDestino.options[0] );
				}
        	}

        	if(  cmpDestino.options[ 0 ] && cmpDestino.options[ 0 ].text == "% - Todos" ){
            	return;
        	}

			agregarOption( cmpDestino, Array( cmpOrigen.options[cmpOrigen.selectedIndex].text ) );
        	addCCO( cmpOrigen, document.getElementById( textoDestino ) );
        }
    }

    function removerOption( campo, txCampo ){
		
    	removeCCO( campo, document.getElementById( txCampo ) );
    	campo.removeChild( campo.options[ campo.selectedIndex ] );
	}

    //Agrega todos las opciones de un select a otro
	function agregarTodo( slOrigen, slDestino, textoDestino ){

    	for( var i = 1; i < slOrigen.options.length; i++ ){
    		slOrigen.selectedIndex = i;
			agregarOptionASelect( slOrigen, slDestino, textoDestino );
    	}
	}

	function eliminarTodo( slOrigen, textoDestino ){

		var numOptions = slOrigen.options.length
		for( var i = 0; i < numOptions; i++ ){
			slOrigen.selectedIndex = 0;
			removerOption( slOrigen, textoDestino );
		}
	}

    //Para cargar las apciones previamente elegidos
	window.onload = function(){

		var auxTxDestino = document.getElementById( "txDestino" );
		
		if( auxTxDestino ){
			agregarOption( document.getElementById( "slCcoDestino" ), auxTxDestino.value.split("\n").reverse() );
		}

		try{
			document.getElementById( "ccodes" ).selectedIndex = -1;
			}catch(e){}
	}
	
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                   REPORTE ADHERENCIA EN HIGIENTE DE MANOS X CENTRO DE COSTOS                                             *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver las adherencias de higiene de manos por personal evaluado, momento, accion y riego.        |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : NOVIEMBRE 21 DE 2013.                                                                                       |
//FECHA ULTIMA ACTUALIZACION  : NOVIEMBRE 21 DE 2013.                                                                                       |
//DESCRIPCION			      : Este reporte sirve para observar x personas evaluadas de adherencia de higiene de manos.                    |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//cominf_000048     : Tabla adherencia de higiene de manos.                                                                                 |                                                                      |
//                                                                                                                                          |
//==========================================================================================================================================
function crearIN( $valores ){
	
	if( empty( $valores ) ){
		$in = "LIKE '%'";
		return $in;
	}
	else{
	
		$in = "IN (";
		$i = 0;

		$ccocodnam = explode( "\r", $valores );
		
		foreach( $ccocodnam as $val ){

			if( !empty($val) ){
				
				if( $val == "% - Todos" ){
					$in = "LIKE '%'";
					return $in;
				}

				//$exp = explode(" - ", $val );

				if( $i > 0 ){
					$in .= ",";
				}
			//	$in .= "'".trim($exp[0])."'";

				$in .= "'".trim($val)."'"; 
				$i++;
			}

		}
		$in .= ")";

		return $in;
	}
	
}


include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="1.0 20-Noviembre-2013";

$usuarioValidado = true;

if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else {
	if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}

session_start();
//Encabezado
encabezado("Adherencia Higiene de Manos",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
	
 $empre1='cominf';
 
 //Conexion base de datos
 
 

 
 


 //Forma
 echo "<form name='forma' action='rep_adherenciashigmanos.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '' or !isset($pp) or $pp=='' or !isset($pm) or $pa=='' or !isset($pm) or $pm=='' or !isset($pr) or $pr=='')
  {
  	echo "<form name='rep_adherenciashigmanos' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";
	
	//Centro de costos
 	echo "<tr class='encabezadotabla' align=center>
			<td width='500' colspan=2>Centro de Costos Adherencia</td>
	</tr>";
	
	echo "<tr class='fila1'><td colspan=2><SELECT id='ccodes' name='ccodes' style='width:600' onChange='javascript: agregarOptionASelect( this,\"slCcoDestino\",\"txDestino\")'>";
	echo "			<option value='%'>% - Todos</option>";

	//Generando lista de opciones de Centro de costos
	$q = "SELECT ccocod,cconom 
		  FROM movhos_000011
		  where ccocod<>'*'
		  order by 1";

	$res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);

	for( $i = 0; $i < $num; $i++ ){
		$rows = mysql_fetch_array($res);
		echo "<option>{$rows[0]}-{$rows[1]}</option>";
	}

	echo "</SELECT>";
	
	echo "<INPUT type='button' value='Añadir' onClick='javascript: addCCO( ccodes, txDestino );' style='display:none'>";
	echo "<INPUT type='button' value='Eliminar' onClick='javascript: removeCCO( ccodes, txDestino );' style='display:none'>";
	
	echo "<tr style='display:none'><td colspan=2><TEXTAREA id='txDestino' name='txDestino' style='width:100%;' value='$txDestino' Rows='3' readonly>$txDestino</TEXTAREA></td></tr>";
	
	echo "<tr>";
	echo "<td colspan=2>";
	echo "<SELECT id='slCcoDestino' name='slCcoDestino' multiple style='width:600' onDblClick='removerOption( this, \"txDestino\" )' size='5'></select>";
	echo "</td>";
	echo "</tr>";
	

	//Ingreso de fecha de consulta
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los parámetros de consulta';
 	echo "</span>";
 	echo "<br>";
 	echo "<br>";

 	//Fecha inicial
 	echo "<tr>";
 	echo "<td class='fila1' width=190>Fecha Inicial</td>";
 	echo "<td class='fila2' align='center' >";
 	campoFecha("fec1");
 	echo "</td></tr>";
 		
 	//Fecha final
 	echo "<tr>";
 	echo "<td class='fila1'>Fecha Final</td>";
 	echo "<td class='fila2' align='center'>";
 	campoFecha("fec2");
 	echo "</td></tr>";
	
   /////////////////////////////////////////////////////////////////////////// seleccion para personal evaluado adherencias
   echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Personal Evaluado:</B><br></font></b><select name='pp' id='searchinput'>";

   $query = " SELECT concat(subcodigo,'-',descripcion)"
           ."   FROM det_selecciones"
           ."  WHERE medico LIKE 'cominf'" 
           ."    AND codigo LIKE '109'"
           ."    AND activo =  'A'";
           
   $err3 = mysql_query($query,$conex);
   $num3 = mysql_num_rows($err3);
   $tpp=$pp;
   
   if ($codemp == '')
    { 
     echo "<option value='%' selected> TODOS </option>";
    }
   else 
    {
   	 echo "<option value='".$tpp[0]."'>".$tpp[0]."</option>";
    } 
   
   for ($i=1;$i<=$num3;$i++)
	{
	$row3 = mysql_fetch_array($err3);
	echo "<option>".$row3[0]."</option>";
	}
   echo "</select></td></tr>";
   
   /////////////////////////////////////////////////////////////////////////// seleccion para momento adherencias
   echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Momento:</B><br></font></b><select name='pm' id='searchinput'>";

   $query4= " SELECT concat(subcodigo,'-',descripcion)"
           ."   FROM det_selecciones"
           ."  WHERE medico LIKE 'cominf'" 
           ."    AND codigo LIKE '110'"
           ."    AND activo =  'A'";
           
   $err4 = mysql_query($query4,$conex);
   $num4 = mysql_num_rows($err4);
   $tpm=$pm;
   
   if ($codemp == '')
    { 
     echo "<option value='%' selected> TODOS </option>";
    }
   else 
    {
   	 echo "<option value='".$tpp[0]."'>".$tpp[0]."</option>";
    } 
   
   for ($i=1;$i<=$num4;$i++)
	{
	$row4 = mysql_fetch_array($err4);
	echo "<option>".$row4[0]."</option>";
	}
   echo "</select></td></tr>";
   
      /////////////////////////////////////////////////////////////////////////// seleccion para accion adherencias
   echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Accion:</B><br></font></b><select name='pa' id='searchinput'>";

   $query5= " SELECT concat(subcodigo,'-',descripcion)"
           ."   FROM det_selecciones"
           ."  WHERE medico LIKE 'cominf'" 
           ."    AND codigo LIKE '111'"
           ."    AND activo =  'A'";
           
   $err5 = mysql_query($query5,$conex);
   $num5 = mysql_num_rows($err5);
   $tpa=$pa;
   
   if ($codemp == '')
    { 
     echo "<option value='%' selected> TODOS </option>";
    }
   else 
    {
   	 echo "<option value='".$tpp[0]."'>".$tpp[0]."</option>";
    } 
   
   for ($i=1;$i<=$num5;$i++)
	{
	$row5 = mysql_fetch_array($err5);
	echo "<option>".$row5[0]."</option>";
	}
   echo "</select></td></tr>";
   
      /////////////////////////////////////////////////////////////////////////// seleccion para riesgo adherencias
   echo "<tr><td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Riesgo:</B><br></font></b><select name='pr' id='searchinput'>";

   $query6= " SELECT concat(subcodigo,'-',descripcion)"
           ."   FROM det_selecciones"
           ."  WHERE medico LIKE 'cominf'" 
           ."    AND codigo LIKE '112'"
           ."    AND activo =  'A'";
           
   $err6 = mysql_query($query6,$conex);
   $num6 = mysql_num_rows($err6);
   $tpr=$pr;
   
   if ($codemp == '')
    { 
     echo "<option value='%' selected> TODOS </option>";
    }
   else 
    {
   	 echo "<option value='".$tpp[0]."'>".$tpp[0]."</option>";
    } 
   
   for ($i=1;$i<=$num6;$i++)
	{
	$row6 = mysql_fetch_array($err6);
	echo "<option>".$row6[0]."</option>";
	}
   echo "</select></td></tr>";
   
      
   echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='GENERAR'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
   echo '</div>';
   echo '</div>';
   echo '</div>';
   
  }
 else // Cuando ya estan todos los datos escogidos
  {
   $tpp=$pp;
   $tpm=$pm;
   $tpa=$pa;
   $tpr=$pr;
   
   $ccos = crearIN( $txDestino );
  
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
	
    echo "<center><table border=1 cellspacing=0 cellpadding=0>";
    echo "<tr><td align=center colspan=4 bgcolor=#FFFFFF><font text color=#003366 size=2><b>ADHERENCIA HIGIENE DE MANOS</b></font></td></tr>";
    echo "<tr><td align=center colspan=4 bgcolor=#FFFFFF><font text color=#003366 size=2><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
	echo "</tr>";
	echo "</table>";
	
	echo "<table border=1 cellpadding='0' cellspacing='0' align=center>";
    echo "<tr>";
    echo "<td bgcolor='#006699' align=center nowrap='nowrap'><font text color=#FFFFFF size=2><b>UNIDAD</b></td>";
    echo "<td bgcolor='#006699' align=center nowrap='nowrap'><font text color=#FFFFFF size=2><b>PERSONAL EVALUADO</b></td>";
    echo "<td bgcolor='#006699' align=center nowrap='nowrap'><font text color=#FFFFFF size=2><b>MOMENTO</b></td>";
	echo "<td bgcolor='#006699' align=center nowrap='nowrap'><font text color=#FFFFFF size=2><b>ACCION</b></td>";
    echo "<td bgcolor='#006699' align=center nowrap='nowrap'><font text color=#FFFFFF size=2><b>RIESGO</b></td>";
    echo "<td bgcolor='#006699' align=center nowrap='nowrap'><font text color=#FFFFFF size=2><b>FRECUENCIA</b></td>";
    echo "</tr>";
	
	IF ($tpp=='%')
	{
	 IF ($tpm=='%')
	 {
	  IF ($tpa=='%')
	  {
	   IF ($tpr=='%')
	   {
	    $query1 = "SELECT Adhunidad, Adhperobs, Adhmomento, Adhaccion, Adhriesgo ,count(*) as cant"
                ."   FROM ".$empre1."_000048 "
                ."  WHERE adhfecha between '".$fec1."' and '".$fec2."'"
		  	    ."    AND adhunidad $ccos"
			    ."  GROUP by 1,2,3,4,5"
                ."  ORDER by 1,2,3,4,5"; 
				
	           //echo $query1."<br>"; 
				 
	    $err1 = mysql_query($query1,$conex);
        $num1 = mysql_num_rows($err1);
	
	    //echo mysql_errno() ."=". mysql_error();
	   }
	   ELSE //$tpr=='%'
	   {
	    $query1 = "SELECT Adhunidad, Adhperobs, Adhmomento, Adhaccion, Adhriesgo ,count(*) as cant"
                ."   FROM ".$empre1."_000048 "
                ."  WHERE adhfecha between '".$fec1."' and '".$fec2."'"
			    ."    AND adhunidad $ccos"
			    ."    AND Adhriesgo = '".$tpr."'" 
			    ."  GROUP by 1,2,3,4,5"
                ."  ORDER by 1,2,3,4,5"; 
				
	    //echo $query1."<br>"; 
				 
	    $err1 = mysql_query($query1,$conex);
        $num1 = mysql_num_rows($err1);
	
	    //echo mysql_errno() ."=". mysql_error();
	   }
	  }
	  ELSE // $tpa=='%'
	  {
	   IF ($tpr=='%')
	    {
		 $query1 = "SELECT Adhunidad, Adhperobs, Adhmomento, Adhaccion, Adhriesgo ,count(*) as cant"
                ."   FROM ".$empre1."_000048 "
                ."  WHERE adhfecha between '".$fec1."' and '".$fec2."'"
			    ."    AND adhunidad $ccos"
			    ."    AND Adhaccion = '".$tpa."'"
			    ."  GROUP by 1,2,3,4,5"
                ."  ORDER by 1,2,3,4,5"; 
				
	     //echo $query1."<br>"; 
				 
	     $err1 = mysql_query($query1,$conex);
         $num1 = mysql_num_rows($err1);
	
	     //echo mysql_errno() ."=". mysql_error();
		}
	   ELSE // 2do $tpr=='%'
        {
	     $query1 = "SELECT Adhunidad, Adhperobs, Adhmomento, Adhaccion, Adhriesgo ,count(*) as cant"
                 ."   FROM ".$empre1."_000048 "
                 ."  WHERE adhfecha between '".$fec1."' and '".$fec2."'"
			     ."    AND adhunidad $ccos"
			     ."    AND Adhaccion = '".$tpa."'"
				 ."    AND Adhriesgo = '".$tpr."'" 
			     ."  GROUP by 1,2,3,4,5"
                 ."  ORDER by 1,2,3,4,5"; 
				
	     //echo $query1."<br>"; 
				 
	     $err1 = mysql_query($query1,$conex);
         $num1 = mysql_num_rows($err1);
	
	     //echo mysql_errno() ."=". mysql_error();
        }	   
	  }
	 }
	 ELSE // 1er $tmp=='%'
	 {
	  IF ($tpa=='%')
	  {
	   IF ($tpr=='%')
	   {
	    $query1 = "SELECT Adhunidad, Adhperobs, Adhmomento, Adhaccion, Adhriesgo ,count(*) as cant"
                ."   FROM ".$empre1."_000048 "
                ."  WHERE adhfecha between '".$fec1."' and '".$fec2."'"
			    ."    AND adhunidad $ccos"
			    ."    AND Adhmomento = '".$tpm."'"
                ."  GROUP by 1,2,3,4,5"
                ."  ORDER by 1,2,3,4,5"; 
				
	    //echo $query1."<br>"; 
				 
	    $err1 = mysql_query($query1,$conex);
        $num1 = mysql_num_rows($err1);
	
	    //echo mysql_errno() ."=". mysql_error();
	   }
	   ELSE
	   {
	   	 $query1 = "SELECT Adhunidad, Adhperobs, Adhmomento, Adhaccion, Adhriesgo ,count(*) as cant"
                 ."   FROM ".$empre1."_000048 "
                 ."  WHERE adhfecha between '".$fec1."' and '".$fec2."'"
			     ."    AND adhunidad $ccos"
			     ."    AND Adhmomento = '".$tpm."'"
				 ."    AND Adhriesgo  = '".$tpr."'" 
                 ."  GROUP by 1,2,3,4,5"
                 ."  ORDER by 1,2,3,4,5"; 
				
	     //echo $query1."<br>"; 
				 
	     $err1 = mysql_query($query1,$conex);
         $num1 = mysql_num_rows($err1);
	
	     //echo mysql_errno() ."=". mysql_error();
	   }
	  }
      ELSE //2do $tpa=='%'
      {
       IF ($tpr=='%')
	   {
	     $query1 = "SELECT Adhunidad, Adhperobs, Adhmomento, Adhaccion, Adhriesgo ,count(*) as cant"
                 ."   FROM ".$empre1."_000048 "
                 ."  WHERE adhfecha between '".$fec1."' and '".$fec2."'"
			     ."    AND adhunidad $ccos"
				 ."    AND Adhaccion = '".$tpa."'"
			     ."    AND Adhmomento = '".$tpm."'"
				 ."  GROUP by 1,2,3,4,5"
                 ."  ORDER by 1,2,3,4,5"; 
				
	     //echo $query1."<br>"; 
				 
	     $err1 = mysql_query($query1,$conex);
         $num1 = mysql_num_rows($err1);
	
	     //echo mysql_errno() ."=". mysql_error();
	   }
	   ELSE
	   {
	    $query1 = "SELECT Adhunidad, Adhperobs, Adhmomento, Adhaccion, Adhriesgo ,count(*) as cant"
                ."   FROM ".$empre1."_000048 "
                ."  WHERE adhfecha between '".$fec1."' and '".$fec2."'"
			    ."    AND adhunidad $ccos"
			    ."    AND Adhmomento = '".$tpm."'"
                ."    AND Adhaccion  = '".$tpa."'"
                ."    AND Adhriesgo  = '".$tpr."'"  			
			    ."  GROUP by 1,2,3,4,5"
                ."  ORDER by 1,2,3,4,5"; 
				
	    //echo $query1."<br>"; 
				 
	    $err1 = mysql_query($query1,$conex);
        $num1 = mysql_num_rows($err1);
	
	    //echo mysql_errno() ."=". mysql_error();
	   }
      }	   
	 }
	}
	ELSE // 1er $tpp=='%'
	{
	 IF ($tpm=='%')
	 {
	  IF ($tpa=='%')
	  {
	   IF ($tpr=='%')
	   {
	    $query1 = "SELECT Adhunidad, Adhperobs, Adhmomento, Adhaccion, Adhriesgo ,count(*) as cant"
                ."   FROM ".$empre1."_000048 "
                ."  WHERE adhfecha between '".$fec1."' and '".$fec2."'"
		  	    ."    AND adhunidad $ccos"
				."    AND Adhperobs = '".$tpp."'" 
			    ."  GROUP by 1,2,3,4,5"
                ."  ORDER by 1,2,3,4,5"; 
				
	           //echo $query1."<br>"; 
				 
	    $err1 = mysql_query($query1,$conex);
        $num1 = mysql_num_rows($err1);
	
	    //echo mysql_errno() ."=". mysql_error();
	   }
	   ELSE //$tpr=='%'
	   {
	    $query1 = "SELECT Adhunidad, Adhperobs, Adhmomento, Adhaccion, Adhriesgo ,count(*) as cant"
                ."   FROM ".$empre1."_000048 "
                ."  WHERE adhfecha between '".$fec1."' and '".$fec2."'"
			    ."    AND adhunidad $ccos"
			    ."    AND Adhriesgo = '".$tpr."'" 
				."    AND Adhperobs = '".$tpp."'" 
			    ."  GROUP by 1,2,3,4,5"
                ."  ORDER by 1,2,3,4,5"; 
				
	    //echo $query1."<br>"; 
				 
	    $err1 = mysql_query($query1,$conex);
        $num1 = mysql_num_rows($err1);
	
	    //echo mysql_errno() ."=". mysql_error();
	   }
	  }
	  ELSE // $tpa=='%'
	  {
	   IF ($tpr=='%')
	    {
		 $query1 = "SELECT Adhunidad, Adhperobs, Adhmomento, Adhaccion, Adhriesgo ,count(*) as cant"
                ."   FROM ".$empre1."_000048 "
                ."  WHERE adhfecha between '".$fec1."' and '".$fec2."'"
			    ."    AND adhunidad $ccos"
			    ."    AND Adhaccion = '".$tpa."'"
				."    AND Adhperobs = '".$tpp."'" 
			    ."  GROUP by 1,2,3,4,5"
                ."  ORDER by 1,2,3,4,5"; 
				
	     //echo $query1."<br>"; 
				 
	     $err1 = mysql_query($query1,$conex);
         $num1 = mysql_num_rows($err1);
	
	     //echo mysql_errno() ."=". mysql_error();
		}
	   ELSE // 2do $tpr=='%'
        {
	     $query1 = "SELECT Adhunidad, Adhperobs, Adhmomento, Adhaccion, Adhriesgo ,count(*) as cant"
                 ."   FROM ".$empre1."_000048 "
                 ."  WHERE adhfecha between '".$fec1."' and '".$fec2."'"
			     ."    AND adhunidad $ccos"
			     ."    AND Adhaccion = '".$tpa."'"
				 ."    AND Adhriesgo = '".$tpr."'" 
				 ."    AND Adhperobs = '".$tpp."'" 
			     ."  GROUP by 1,2,3,4,5"
                 ."  ORDER by 1,2,3,4,5"; 
				
	     //echo $query1."<br>"; 
				 
	     $err1 = mysql_query($query1,$conex);
         $num1 = mysql_num_rows($err1);
	
	     //echo mysql_errno() ."=". mysql_error();
        }	   
	  }
	 }
	 ELSE // 1er $tmp=='%'
	 {
	  IF ($tpa=='%')
	  {
	   IF ($tpr=='%')
	   {
	    $query1 = "SELECT Adhunidad, Adhperobs, Adhmomento, Adhaccion, Adhriesgo ,count(*) as cant"
                ."   FROM ".$empre1."_000048 "
                ."  WHERE adhfecha between '".$fec1."' and '".$fec2."'"
			    ."    AND adhunidad $ccos"
			    ."    AND Adhmomento = '".$tpm."'"
				."    AND Adhperobs = '".$tpp."'" 
                ."  GROUP by 1,2,3,4,5"
                ."  ORDER by 1,2,3,4,5"; 
				
	    //echo $query1."<br>"; 
				 
	    $err1 = mysql_query($query1,$conex);
        $num1 = mysql_num_rows($err1);
	
	    //echo mysql_errno() ."=". mysql_error();
	   }
	   ELSE
	   {
	   	 $query1 = "SELECT Adhunidad, Adhperobs, Adhmomento, Adhaccion, Adhriesgo ,count(*) as cant"
                 ."   FROM ".$empre1."_000048 "
                 ."  WHERE adhfecha between '".$fec1."' and '".$fec2."'"
			     ."    AND adhunidad $ccos"
			     ."    AND Adhmomento = '".$tpm."'"
				 ."    AND Adhriesgo  = '".$tpr."'"
                 ."    AND Adhperobs = '".$tpp."'" 				 
                 ."  GROUP by 1,2,3,4,5"
                 ."  ORDER by 1,2,3,4,5"; 
				
	     //echo $query1."<br>"; 
				 
	     $err1 = mysql_query($query1,$conex);
         $num1 = mysql_num_rows($err1);
	
	     //echo mysql_errno() ."=". mysql_error();
	   }
	  }
      ELSE //2do $tpa=='%'
      {
       IF ($tpr=='%')
	   {
	     $query1 = "SELECT Adhunidad, Adhperobs, Adhmomento, Adhaccion, Adhriesgo ,count(*) as cant"
                 ."   FROM ".$empre1."_000048 "
                 ."  WHERE adhfecha between '".$fec1."' and '".$fec2."'"
			     ."    AND adhunidad $ccos"
				 ."    AND Adhaccion = '".$tpa."'"
			     ."    AND Adhmomento = '".$tpm."'"
				 ."    AND Adhperobs = '".$tpp."'" 
				 ."  GROUP by 1,2,3,4,5"
                 ."  ORDER by 1,2,3,4,5"; 
				
	     //echo $query1."<br>"; 
				 
	     $err1 = mysql_query($query1,$conex);
         $num1 = mysql_num_rows($err1);
	
	     //echo mysql_errno() ."=". mysql_error();
	   }
	   ELSE
	   {
	    $query1 = "SELECT Adhunidad, Adhperobs, Adhmomento, Adhaccion, Adhriesgo ,count(*) as cant"
                ."   FROM ".$empre1."_000048 "
                ."  WHERE adhfecha between '".$fec1."' and '".$fec2."'"
			    ."    AND adhunidad $ccos"
			    ."    AND Adhmomento = '".$tpm."'"
                ."    AND Adhaccion  = '".$tpa."'"
                ."    AND Adhriesgo  = '".$tpr."'" 
                ."    AND Adhperobs = '".$tpp."'" 				
			    ."  GROUP by 1,2,3,4,5"
                ."  ORDER by 1,2,3,4,5"; 
				
	    //echo $query1."<br>"; 
				 
	    $err1 = mysql_query($query1,$conex);
        $num1 = mysql_num_rows($err1);
	
	    //echo mysql_errno() ."=". mysql_error();
	   }
      }	   
	 }
	}

	
    $totevaf=0;
    
	for ($i=1;$i<=$num1;$i++)
	 {
	 if (is_int ($i/2))
	  {
	   $wcf="DDDDDD";  // color de fondo
	  }
	 else
	  {
	   $wcf="CCFFFF"; // color de fondo
	  }

      $row1 = mysql_fetch_array($err1);
	   
   	  
      echo "<Tr >";
      echo "<td  bgcolor=".$wcf." align=center nowrap='nowrap'><font text size=2>".$row1[0]."</font></td>";
	  echo "<td  bgcolor=".$wcf." align=center nowrap='nowrap'><font text size=2>".$row1[1]."</font></td>";
      echo "<td  bgcolor=".$wcf." align=center nowrap='nowrap'><font text size=2>".$row1[2]."</font></td>";
	  echo "<td  bgcolor=".$wcf." align=center nowrap='nowrap'><font text size=2>".$row1[3]."</font></td>";
	  echo "<td  bgcolor=".$wcf." align=center nowrap='nowrap'><font text size=2>".$row1[4]."</font></td>";
	  echo "<td  bgcolor=".$wcf." align=center nowrap='nowrap'><font text size=2>".number_format($row1[5])."</font></td>";
	  echo "</tr >";

	  $totevaf=$totevaf+$row1[5];
	    
	  
	 }

	echo "<tr><td align=center colspan=3 bgcolor=#006699><font text color=#FFFFFF size=2><b>TOTAL GENERAL ADHERENCIA HIGIENE DE MANOS : </b></font></td><td align=center colspan=3><font text size=3><b>".number_format($totevaf)."</b></td></tr>";
	
	echo "</table>"; // cierra la tabla o cuadricula de la impresión
				
  } // cierre del else donde empieza la impresión
echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>"; 
echo "<tr><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";	
echo "</table>";
}
?>