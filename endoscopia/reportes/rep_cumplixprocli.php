<html>
<head>
<title>MATRIX - [REPORTE PROCESOS PRIORITARIOS]</title>


<script type="text/javascript">
	function enter()
	{
		document.forms.rep_cumplixprocli.submit();
	}
	
	function cerrarVentana()
	{
	 window.close()
	}
	
	function VolverAtras()
	{
	 history.back(1)
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
*                                             REPORTE PROCESOS PRIORITARIOS %CUMPLIMIENTO                                                  *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver promedio de cumplimiento por criterio deacuerdo al proceso prioritario                     |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : FEBRERO 05 DE 2013.                                                                                         |
//FECHA ULTIMA ACTUALIZACION  : FEBRERO 05 DE 2013.                                                                                         |
//DESCRIPCION			      : Este reporte sirve para observar por proceso prioritarios cumplimiento por criterio                         |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//cominf_000046     : Tabla de Procesos Prioritarios clinica.                                                                               |
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

$wactualiz="1.0 15-Febrero-2013";

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
encabezado("Cumplimiento x Proceso Prioritario",$wactualiz,"clinica");

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

 

 


 //Forma
 echo "<form name='forma' action='rep_cumplixprocli.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
 if (!isset($pp) or $pp=='-' or !isset($fec1) or !isset($fec2))
  {
  	echo "<form name='rep_cumplixprocli' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";
	
	//Centro de costos
 	echo "<tr class='encabezadotabla' align=center>
			<td width='500' colspan=2>Centro de costos de Procesos Prioritarios</td>
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

   /////////////////////////////////////////////////////////////////////////// seleccion para los procesos prioritarios
   echo "<td align=CENTER colspan=2 bgcolor=#DDDDDD><b><font text color=#003366 size=3><B>Proceso Prioritario:</B><br></font></b><select name='pp' id='searchinput'>";

   $query = " SELECT concat(subcodigo,'-',descripcion)"
           ."   FROM det_selecciones"
           ."  WHERE medico LIKE 'cominf'" 
           ."    AND codigo LIKE '106'"
           ."    AND activo =  'A'";
           
   $err3 = mysql_query($query,$conex);
   $num3 = mysql_num_rows($err3);
   $tpp=$pp;
   
   if ($codemp == '')
    { 
     echo "<option></option>";
    }
   else 
    {
   	 echo "<option>".$tpp[0]."</option>";
    } 
   
   for ($i=1;$i<=$num3;$i++)
	{
	$row3 = mysql_fetch_array($err3);
	echo "<option>".$row3[0]."</option>";
	}
   echo "</select></td>";
   
   echo "<br>";   
   echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='GENERAR'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
   echo '</div>';
   echo '</div>';
   echo '</div>';
   
  }
 else // Cuando ya estan todos los datos escogidos
  {
   $tpp=$pp;
   $tp1=explode('-',$pp);
   $ccos = crearIN( $txDestino );
   
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
   echo "<table border=0 cellspacing=0 cellpadding=0 align=center size='200'>";  //border=0 no muestra la cuadricula en 1 si.
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>PROCESO PRIORITARIO</b></font></td>";
   echo "</tr>";
   echo "<tr>";   
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366>$tpp</font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
   echo "</tr>";
   echo "</table>";
    
	$mesi=SUBSTR(".$fec1.",6,2);
    $mesf=SUBSTR(".$fec2.",6,2);  
  
  $query = " SELECT ppccco,ppcproce,ppccrite1,ppccrite2,ppccrite3,ppccrite4,ppccrite5,ppccrite6,ppccrite7,ppccrite8,ppccrite9,ppccrite10,ppccrite11,ppccrite12,ppccrite13,ppccrite14,ppccrite15,ppccrite16,ppccrite17,ppccrite18,ppccrite19,Patotal"
           ."   FROM ".$empre1."_000046 left join ".$empre1."_000045"
		   	."    ON papp=ppcproce"
            ."   AND pames=SUBSTRING(Ppcfecha,6,2)" 
            ."   AND paano=SUBSTRING(Ppcfecha,1,4)"
            ."   AND Pacco $ccos"
            ."   AND pacco=ppccco"
           ."  WHERE ppcproce = '".$tpp."'" 
           ."    AND ppcfecha between '".$fec1."' and '".$fec2."'"
		   ."    AND ppccco $ccos"
           ."  ORDER BY ppccco,ppcproce";
   
    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);
   
   
   /// traer tambien de la tabla 000045 la meta por el mes de la fec2 y traer la meta del procedimiento x ccosto.
   
    $arrecrit=Array();
   
   
	echo "<table border=1 cellpadding='0' cellspacing='0' size='642'>";
    echo "<tr>";
    echo "<td bgcolor='#FFFFFF'align=center width=2><font text color=#000000 size=1></td>";
	echo "<td bgcolor='#FFFFFF'align=center nowrap='nowrap'><font text color=#000000 size=1>CENTRO DE COSTOS</td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR1</td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR2</td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR3</td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR4</td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR5</td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR6</td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR7</td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR8</td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR9</td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR10</td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR11</td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR12</td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR13</td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR14</td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR15</td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR16</td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR17</td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR18</td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1>CR19</td>";
    echo "</tr>";
   
    $swtitulo='SI';
	$ccoant='';
	$canti=0;
	$total=0;
   
    for ($j=1; $j <=19; $j++)
    {
     $arrecrit[$j]=0;
    }
   
    for ($i=1;$i<=$num1;$i++)
	{
	 $row2 = mysql_fetch_array($err1);

	 if ($swtitulo=='SI')
	  {
	   $ccoant  = $row2[0];
	   $swtitulo='NO';
	  }
	  	  
	  IF ($ccoant==$row2[0])
	  {	
	  
	  IF ($row2[2]=='on')
      {
      	$arrecrit[1]=$arrecrit[1]+1;
      } 	
      IF ($row2[3]=='on')
      {
      	$arrecrit[2]=$arrecrit[2]+1;
      } 	
      IF ($row2[4]=='on')
      {
      	$arrecrit[3]=$arrecrit[3]+1;
      }
	  IF ($row2[5]=='on')
      {
      	$arrecrit[4]=$arrecrit[4]+1;
      }
	  IF ($row2[6]=='on')
      {
      	$arrecrit[5]=$arrecrit[5]+1;
      }
	  IF ($row2[7]=='on')
      {
      	$arrecrit[6]=$arrecrit[6]+1;
      }
	  IF ($row2[8]=='on')
      {
      	$arrecrit[7]=$arrecrit[7]+1;
      }
	  IF ($row2[9]=='on')
      {
      	$arrecrit[8]=$arrecrit[8]+1;
      }
      IF ($row2[10]=='on')
      {
      	$arrecrit[9]=$arrecrit[9]+1;
      }
	  IF ($row2[11]=='on')
      {
      	$arrecrit[10]=$arrecrit[10]+1;
      }
	  IF ($row2[12]=='on')
      {
      	$arrecrit[11]=$arrecrit[11]+1;
      }
	  IF ($row2[13]=='on')
      {
      	$arrecrit[12]=$arrecrit[12]+1;
      }
	  IF ($row2[14]=='on')
      {
      	$arrecrit[13]=$arrecrit[13]+1;
      }
	  IF ($row2[15]=='on')
      {
      	$arrecrit[14]=$arrecrit[14]+1;
      }
	  IF ($row2[16]=='on')
      {
      	$arrecrit[15]=$arrecrit[15]+1;
      }
	  IF ($row2[17]=='on')
      {
      	$arrecrit[16]=$arrecrit[16]+1;
      }
	  IF ($row2[18]=='on')
      {
      	$arrecrit[17]=$arrecrit[17]+1;
      }
	  IF ($row2[19]=='on')
      {
      	$arrecrit[18]=$arrecrit[18]+1;
      }
	  IF ($row2[20]=='on')
      {
      	$arrecrit[19]=$arrecrit[19]+1;
      }

	  $canti=$canti+1;
	  $total=$row2[21];
	 
	 }
	 ELSE
	 {
	  
	  echo "<Tr >";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>CUMPLIMIENTO</font></td>";
	  echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".$ccoant."</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[1]/$canti)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[2]/$canti)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[3]/$canti)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[4]/$canti)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[5]/$canti)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[6]/$canti)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[7]/$canti)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[8]/$canti)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[9]/$canti)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[10]/$canti)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[11]/$canti)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[12]/$canti)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[13]/$canti)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[14]/$canti)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[15]/$canti)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[16]/$canti)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[17]/$canti)*100)."%</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[18]/$canti)*100)."%</b></font></td>";
	  echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[19]/$canti)*100)."%</b></font></td>";
      echo "</tr >";
	  echo "</table>";
	  
	  echo "<table border=0 size=100%>";
      echo "<Tr >";
      echo "<td align=LEFT bgcolor=#FFFFFF ><font size=2 color=#000000><b>TOTAL DEL PROCESO CENTRO DE COSTO: </b></font></td>"; 
      echo "<td align=left bgcolor=#FFFFFF ><font size=2 color=#000000>&nbsp;<b>$canti</b></font></td>";
	  echo "<td align=left bgcolor=#FFFFFF ><font size=2 color=#000000>&nbsp;<b>$total</b></font></td>";
      echo "</tr >"; 
      echo "</table>";
	  
	  $canti=0;
	  $total=0;
	  for ($j=1; $j <=19; $j++)
      {
        $arrecrit[$j]=0;
      }
	  
	  IF ($row2[2]=='on')
      {
      	$arrecrit[1]=$arrecrit[1]+1;
      } 	
      IF ($row2[3]=='on')
      {
      	$arrecrit[2]=$arrecrit[2]+1;
      } 	
      IF ($row2[4]=='on')
      {
      	$arrecrit[3]=$arrecrit[3]+1;
      }
	  IF ($row2[5]=='on')
      {
      	$arrecrit[4]=$arrecrit[4]+1;
      }
	  IF ($row2[6]=='on')
      {
      	$arrecrit[5]=$arrecrit[5]+1;
      }
	  IF ($row2[7]=='on')
      {
      	$arrecrit[6]=$arrecrit[6]+1;
      }
	  IF ($row2[8]=='on')
      {
      	$arrecrit[7]=$arrecrit[7]+1;
      }
	  IF ($row2[9]=='on')
      {
      	$arrecrit[8]=$arrecrit[8]+1;
      }
      IF ($row2[10]=='on')
      {
      	$arrecrit[9]=$arrecrit[9]+1;
      }
	  IF ($row2[11]=='on')
      {
      	$arrecrit[10]=$arrecrit[10]+1;
      }
	  IF ($row2[12]=='on')
      {
      	$arrecrit[11]=$arrecrit[11]+1;
      }
	  IF ($row2[13]=='on')
      {
      	$arrecrit[12]=$arrecrit[12]+1;
      }
	  IF ($row2[14]=='on')
      {
      	$arrecrit[13]=$arrecrit[13]+1;
      }
	  IF ($row2[15]=='on')
      {
      	$arrecrit[14]=$arrecrit[14]+1;
      }
	  IF ($row2[16]=='on')
      {
      	$arrecrit[15]=$arrecrit[15]+1;
      }
	  IF ($row2[17]=='on')
      {
      	$arrecrit[16]=$arrecrit[16]+1;
      }
	  IF ($row2[18]=='on')
      {
      	$arrecrit[17]=$arrecrit[17]+1;
      }
	  IF ($row2[19]=='on')
      {
      	$arrecrit[18]=$arrecrit[18]+1;
      }
	  IF ($row2[20]=='on')
      {
      	$arrecrit[19]=$arrecrit[19]+1;
      }

	  $canti=$canti+1;
	  $total=$row2[21];
	  $swtitulo='SI';
	  
	 }		
		
  } // cierre del for  

  IF ($canti==0)
   {  
     echo "</table>";
   }
  ELSE
  {
    echo "<Tr >";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>CUMPLIMIENTO</font></td>";
	echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".$ccoant."</b></font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[1]/$canti)*100)."%</b></font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[2]/$canti)*100)."%</b></font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[3]/$canti)*100)."%</b></font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[4]/$canti)*100)."%</b></font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[5]/$canti)*100)."%</b></font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[6]/$canti)*100)."%</b></font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[7]/$canti)*100)."%</b></font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[8]/$canti)*100)."%</b></font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[9]/$canti)*100)."%</b></font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[10]/$canti)*100)."%</b></font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[11]/$canti)*100)."%</b></font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[12]/$canti)*100)."%</b></font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[13]/$canti)*100)."%</b></font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[14]/$canti)*100)."%</b></font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[15]/$canti)*100)."%</b></font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[16]/$canti)*100)."%</b></font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[17]/$canti)*100)."%</b></font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[18]/$canti)*100)."%</b></font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>".number_format(($arrecrit[19]/$canti)*100)."%</b></font></td>";
    echo "</tr >";

	echo "</table>";
	
	echo "<table border=0 size=100%>";
    echo "<Tr >";
    echo "<td align=LEFT bgcolor=#FFFFFF ><font size=2 color=#000000><b>TOTAL DEL PROCESO CENTRO DE COSTO: </b></font></td>"; 
    echo "<td align=left bgcolor=#FFFFFF ><font size=2 color=#000000>&nbsp;<b>$canti</b></font></td>";
	echo "<td align=left bgcolor=#FFFFFF ><font size=2 color=#000000>&nbsp;<b>$total</b></font></td>";
    echo "</tr >"; 
    echo "</table>";
  }
	
    echo "<table border=0 size=100%>";
    echo "<Tr >";
    echo "<td align=LEFT bgcolor=#FFFFFF ><font size=2 color=#000000><b>TOTAL DEL PROCESO POR TODOS LOS CENTROS DE COSTOS: </b></font></td>"; 
    echo "<td align=left bgcolor=#FFFFFF ><font size=2 color=#000000>&nbsp;<b>$num1</b></font></td>";
    echo "</tr >"; 
    echo "</table>";
   
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
 }// cierre del else donde empieza la impresión

}
?>