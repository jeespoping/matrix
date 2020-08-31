<html>
<head>
<title>MATRIX - [REPORTE DETALLE DE PROCESOS PRIORITARIOS CLINICA]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_detpropricli.php'; 
	}
	
	function enter()
	{
		document.forms.rep_detpropricli.submit();
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
*                                      REPORTE DETALLE DE PROCESOS PRIORITARIOS	CLINICA                                                    *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      :Reporte para ver los procesos prioritarios < al 100%.                                                        |
//AUTOR				          :Ing. Gustavo Alberto Avendano Rivera.                                                                        |
//FECHA CREACION			  :JULIO 26 DE 2012.                                                                                            |
//FECHA ULTIMA ACTUALIZACION  :26 de Julio de 2012.                                                                                         |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//cominf_000045     : Tabla del plan anual por proceso.                                                                                     |
//cominf_000046     : Tabla de procesos prioritarios de clinica                                                                             |
//                                                                                                                                          |
// Variables Principales                                                                                                                    |
//                                                                                                                                          |
//$ccodes:					Centro de costos                                                                                                |
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

$wactualiz="1.0 26-Julio-2012";

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
encabezado("Detalle de Proceso Prioritario Clinica",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
 $empresa='cominf';
 
 //Forma
 echo "<form name='rep_detpropricli' action='' method=post>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
 if (!isset($pp) or $pp=='-' or !isset($fec1) or !isset($fec2) or empty($txDestino))
  {
  	echo "<form name='rep_detpropricli' action='' method=post>";
  
	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Ingreso de fecha de consulta
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los parámetros de consulta';
 	echo "</span>";
 	echo "<br>";
 	echo "<br>";

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
  	
   $query = " SELECT ppcperso1,ppccargo1,ppcfecha,ppccco,ppcproce,ppccrite1,ppccrite2,ppccrite3,ppccrite4,ppccrite5,ppccrite6,ppccrite7,ppccrite8,ppccrite9,ppccrite10,ppccrite11,ppccrite12,ppccrite13,ppccrite14,ppccrite15,ppccrite16,ppccrite17,ppccrite18,ppccrite19,ppcobserva,ppcporce"
           ."   FROM ".$empresa."_000046"
           ."  WHERE ppcproce = '".$tpp."'" 
           ."    AND ppcporce < 100"
           ."    AND ppcfecha between '".$fec1."' and '".$fec2."'"
           ."    AND ppccco $ccos"
           ."  ORDER BY ppccco,ppcfecha,ppcperso1";
           
   $err1 = mysql_query($query,$conex);
   $num1 = mysql_num_rows($err1);
   
   //echo $query."<br>";

	for ($i=1;$i<=$num1;$i++)
	 {
	  $row2 = mysql_fetch_array($err1);

	  echo "<table border=0>";	
      echo "<tr>";
      echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>PERSONA EVALUADA:</b></font></td>";  
      echo "<td  bgcolor=#FFFFFF ><font size=1>&nbsp;$row2[0]</font></td>";
      echo "<td align=LEFT bgcolor=#FFFFFF ><font size='1' color='#000000'><b>CARGO PERSONA EVALUADA:</b></font></td>";  
      echo "<td  bgcolor=#FFFFFF align=center ><font size=1>&nbsp;$row2[1]</font></td>";
      echo "<td align=LEFT bgcolor=#FFFFFF width=120><font size='1' color='#000000'><b>FECHA EVALUACION:</b></font></td>"; 
      echo "<td  bgcolor=#FFFFFF align=center width=70><font size=1>&nbsp;$row2[2]</font></td>";
      echo "</tr>";
      echo "</table>";
      
      echo "<table border=0 size=80%>";
      echo "<Tr >";
      echo "<td  bgcolor=#FFFFFF ><font size=1 color='#000000'><b>CENTRO DE COSTOS: </b></font></td>";
      echo "<td  bgcolor=#FFFFFF ><font size=1><b>&nbsp;$row2[3]</b></font></td>";
      echo "</tr>";
      echo "</table>";
	  
      IF ($row2[5]=='on')
      {
      	$row2[5]='';
      } 	
      IF ($row2[6]=='on')
      {
      	$row2[6]='';
      } 	
      IF ($row2[7]=='on')
      {
      	$row2[7]='';
      } 	
	  IF ($row2[8]=='on')
      {
      	$row2[8]='';
      }
	  IF ($row2[9]=='on')
      {
      	$row2[9]='';
      }
	  IF ($row2[10]=='on')
      {
      	$row2[10]='';
      }
	  IF ($row2[11]=='on')
      {
      	$row2[11]='';
      }
	  IF ($row2[12]=='on')
      {
      	$row2[12]='';
      }
	  IF ($row2[13]=='on')
      {
      	$row2[13]='';
      }
      IF ($row2[14]=='on')
      {
      	$row2[14]='';
      }
	  IF ($row2[15]=='on')
      {
      	$row2[15]='';
      }
	  IF ($row2[16]=='on')
      {
      	$row2[16]='';
      }
	  IF ($row2[17]=='on')
      {
      	$row2[17]='';
      }
	  IF ($row2[18]=='on')
      {
      	$row2[18]='';
      }
	  IF ($row2[19]=='on')
      {
      	$row2[19]='';
      }
	  IF ($row2[20]=='on')
      {
      	$row2[20]='';
      }
	  IF ($row2[21]=='on')
      {
      	$row2[21]='';
      }
	  IF ($row2[22]=='on')
      {
      	$row2[22]='';
      }
	  IF ($row2[23]=='on')
      {
      	$row2[23]='';
      }
	  
      
      switch ($tp1[0])
      {
      	case '01':
      	 $row2[14]='';
      	 $row2[15]='';	
         $row2[16]='';
      	 $row2[17]='';
      	 $row2[18]='';	
         $row2[19]='';
      	 $row2[20]='';
      	 $row2[21]='';
      	 $row2[22]='';
      	 $row2[23]='';
      	break;
      	case '02':
      	 $row2[15]='';	
         $row2[16]='';
      	 $row2[17]='';
      	 $row2[18]='';	
         $row2[19]='';
      	 $row2[20]='';
      	 $row2[21]='';
      	 $row2[22]='';
      	 $row2[23]='';	
      	break;
      	case '03':
      	 $row2[15]='';	
         $row2[16]='';
      	 $row2[17]='';
      	 $row2[18]='';	
         $row2[19]='';
      	 $row2[20]='';
      	 $row2[21]='';
      	 $row2[22]='';
      	 $row2[23]='';	
      	break;	 
      	case '04':
       	 $row2[15]='';	
         $row2[16]='';
      	 $row2[17]='';
      	 $row2[18]='';	
         $row2[19]='';
      	 $row2[20]='';
      	 $row2[21]='';
      	 $row2[22]='';
      	 $row2[23]='';			
      	break;	
      	case '05':
      	 $row2[16]='';
      	 $row2[17]='';
      	 $row2[18]='';	
         $row2[19]='';
      	 $row2[20]='';
      	 $row2[21]='';
      	 $row2[22]='';
      	 $row2[23]='';	
      	break;	
      	case '06':
      	 $row2[15]='';	
         $row2[16]='';
      	 $row2[17]='';
      	 $row2[18]='';	
         $row2[19]='';
      	 $row2[20]='';
      	 $row2[21]='';
      	 $row2[22]='';
      	 $row2[23]='';	
      	break;	
      	case '07':
      	 $row2[17]='';
      	 $row2[18]='';	
         $row2[19]='';
      	 $row2[20]='';
      	 $row2[21]='';
      	 $row2[22]='';
      	 $row2[23]='';	
      	break;	
      	case '08':
         $row2[17]='';
      	 $row2[18]='';	
         $row2[19]='';
      	 $row2[20]='';
      	 $row2[21]='';
      	 $row2[22]='';
      	 $row2[23]='';	
      	break;	
      	case '09':
         $row2[17]='';
      	 $row2[18]='';	
         $row2[19]='';
      	 $row2[20]='';
      	 $row2[21]='';
      	 $row2[22]='';
      	 $row2[23]='';	      	
      	break;	
      	case '10':
         $row2[19]='';
      	 $row2[20]='';
      	 $row2[21]='';
      	 $row2[22]='';
      	 $row2[23]='';
      	break;	
      	case '11':
      	 $row2[23]='';
      	break;	
       	case '13':
      	 $row2[19]='';
      	 $row2[20]='';
      	 $row2[21]='';
      	 $row2[22]='';
      	 $row2[23]='';
      	break;	
      	case '14':
      	 $row2[17]='';
      	 $row2[18]='';	
         $row2[19]='';
      	 $row2[20]='';
      	 $row2[21]='';
      	 $row2[22]='';
      	 $row2[23]='';
      	break;	
      
      }	 
      
      echo "<table border=1 cellpadding='0' cellspacing='0' size='602'>";
      echo "<tr>";
      echo "<td bgcolor='#FFFFFF'align=center width=2><font text color=#000000 size=1></td>";
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
      
      echo "<Tr >";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>VALOR</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[5]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[6]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[7]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[8]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[9]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[10]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[11]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[12]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[13]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[14]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[15]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[16]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[17]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[18]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[19]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[20]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[21]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[22]</b></font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1><b>$row2[23]</b></font></td>";
      echo "</tr >";
      echo "</table>";
      
      echo "<table border=0>";	
      echo "<tr>";
      echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>OBSERVACIONES:</b></font></td>"; 
      echo "</tr >";
      echo "</table>";
      
      echo "<table border=0 >";
      echo "<Tr >";
      echo "<td  bgcolor=#FFFFFF align=left width=100%><font size=1>&nbsp;$row2[24]</font></td>";
      echo "</tr >"; 
      echo "</table>";
      
      
      echo "<table border=0 >";
      echo "<Tr >";
      echo "<td align=LEFT bgcolor=#FFFFFF><font size='1' color='#000000'><b>PORCENTAJE: </b></font></td>"; 
      echo "<td  bgcolor=#FFFFFF align=left width=65%><font size=2 color=#FF0000>&nbsp;<b>".number_format($row2[25])."%</b></font></td>";
      echo "</tr >"; 
      echo "</table>";
      
      echo "<table border=0 size=100%>";
      echo "<Tr >";
      echo "<tr><td align=LEFT bgcolor=#FFFFFF><font size='1' color='#0000FF'><b>------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------</b></font></tr>";
      echo "<tr><td>&nbsp;</td></tr>";
      echo "</Tr >"; 
      echo "</table>";
	
  } // cierre del for
  
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
  
 }// cierre del else donde empieza la impresión

}

?>