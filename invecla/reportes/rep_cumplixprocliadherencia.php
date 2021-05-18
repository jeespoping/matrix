<html>
<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>
<head>
<title>MATRIX - [REPORTE CUMPLIMIENTO - ADHERENCIAS]</title>


<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/LeerTablaAmericas.js" type="text/javascript"></script>
<script src="../../../include/root/amcharts/amcharts.js" type="text/javascript"></script>


<script type="text/javascript">
	function enter()
	{
		document.forms.rep_cumplixprocliadherencia.submit();
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

   //para graficar
	function pintarGrafica ()
    {
		$('#tablareporte').LeerTablaAmericas({
				empezardesdefila: 1,
				dimension : '3d' ,
				titulo : 'Cumplimiento x Proceso Prioritario-Tecnica' ,
				tituloy: 'Cumplimiento %',
				filaencabezado : [0,1],
				datosadicionales : 'todo',
				columnaencabezadoenx: 0,
				columnadatos: 1
			});

    }
</script>

<?php
include_once("conex.php");

/*******************************************************************************************************************************************
*                                             REPORTE PROCESOS PRIORITARIOS ADHERENCIA %CUMPLIMIENTO                                       *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver promedio de cumplimiento por criterio deacuerdo al proceso prioritario                     |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : NOVIEMBRE 20 DE 2013.                                                                                       |
//FECHA ULTIMA ACTUALIZACION  : NOVIEMBRE 20 DE 2013.                                                                                       |
//DESCRIPCION			      : Este reporte sirve para observar por proceso prioritarios cumplimiento por criterio adherencias             |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//cominf_000050     : Tabla de Procesos Prioritarios clinica-ADHERENCIA.                                                                    |
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
encabezado("Cumplimiento x Proceso Prioritario-Tecnica",$wactualiz,"clinica");

if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{

$user_session = explode('-', $_SESSION['user']);
        $wuse = $user_session[1];
        mysql_select_db("matrix");
		$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
		$wcominf = consultarAliasPorAplicacion($conex, $wemp_pmla, "invecla"); 


 //Forma
 echo "<form name='forma' action='rep_cumplixprocliadherencia.php?wemp_pmla=".$wemp_pmla."' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";

 if (!isset($pp) or $pp=='-' or !isset($fec1) or !isset($fec2))
  {
  	echo "<form name='rep_cumplixprocliadherencia' action='' method=post>";

	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Centro de costos
 	echo "<tr class='encabezadotabla' align=center>
			<td width='500' colspan=2>Centro de costos de Procesos Prioritarios-tecnica</td>
	</tr>";

	echo "<tr class='fila1'><td colspan=2><SELECT id='ccodes' name='ccodes' style='width:600' onChange='javascript: agregarOptionASelect( this,\"slCcoDestino\",\"txDestino\")'>";
	echo "			<option value='%'>% - Todos</option>";

	//Generando lista de opciones de Centro de costos
	$q = "SELECT ccocod,cconom
		  FROM ".$wmovhos."_000011
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

   $query = " SELECT concat(subcodigo,'-',descripcion)
              FROM det_selecciones
             WHERE medico LIKE 'cominf'
               AND codigo LIKE '113'
               AND activo =  'A'";

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
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='3' text color=#003366><b>PROCESO PRIORITARIO - ADHERENCIAS</b></font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366>$tpp</font></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<td align=center colspan='1' bgcolor=#FFFFFF><font size='2' text color=#003366><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td>";
   echo "</tr>";
   echo "</table>";

   $query = " SELECT ppacco,ppaproce,ppacrite1,ppacrite2,ppacrite3,ppacrite4,ppacrite15,ppacrite6,ppacrite7,ppacrite8
              FROM ".$wcominf."_000050
             WHERE ppaproce = '".$tpp."'
               AND ppafecha between '".$fec1."' and '".$fec2."'
		       AND ppacco $ccos
             ORDER BY ppacco,ppaproce";

    $err1 = mysql_query($query,$conex);
    $num1 = mysql_num_rows($err1);

    //echo $query."<br>".mysql_errno().'='.mysql_error();

    $arrecrit=Array();

    $query2 = "SELECT ppacco
               FROM ".$wcominf."_000050
              WHERE ppaproce = '".$tpp."'
                AND ppafecha between '".$fec1."' and '".$fec2."'
		        AND ppacco $ccos
              GROUP BY ppacco
		      ORDER BY ppacco";

    $err2 = mysql_query($query2,$conex);
    $num2 = mysql_num_rows($err2);

	//echo $query2."<br>".mysql_errno().'='.mysql_error();

	echo "<table border=1 cellpadding='0' cellspacing='0' align=center size='400' id='tablareporte'>";
    echo "<tr>";
	echo "<td bgcolor='#FFFFFF'align=center nowrap='nowrap'><font text color=#000000 size=1><b>CENTRO DE COSTOS</b></td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>CRITERIO_1</b></td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>CRITERIO_2</b></td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>CRITERIO_3</b></td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>CRITERIO_4</b></td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>CRITERIO_5</b></td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>CRITERIO_6</b></td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>CRITERIO_7</b></td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>CRITERIO_8</b></td>";
    echo "<td bgcolor='#FFFFFF'align=center width=24><font text color=#000000 size=1><b>CANTIDAD</b></td>";
	echo "</tr>";

    $swtitulo='SI';
	$ccoant='';
	$canti=0;

    for ($j=1; $j <=8; $j++)
    {
     $arrecrit[$j]=0;
	}

    for ($i=1;$i<=$num1;$i++)
	{
	 $row2 = mysql_fetch_array($err1);

	 IF ($swtitulo=='SI')
	  {
	   IF ($canti==0)
	   {
	    $ccoant   = $row2[0];
	    $swtitulo='NO';
	   }
       ELSE
	   {
        IF ($ccoant<>$row2[0])
	    {
		  echo "<Tr >";
	      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".$ccoant."</font></td>";
          echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrita[1]/$canti)*100)."%</font></td>";
          echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrita[2]/$canti)*100)."%</font></td>";
          echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrita[3]/$canti)*100)."%</font></td>";
          echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrita[4]/$canti)*100)."%</font></td>";
          echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrita[5]/$canti)*100)."%</font></td>";
          echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrita[6]/$canti)*100)."%</font></td>";
          echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrita[7]/$canti)*100)."%</font></td>";
          echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrita[8]/$canti)*100)."%</font></td>";
	      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($canti)."</font></td>";
          echo "</tr >";

	      $canti=0;
	      for ($j=1; $j <=8; $j++)
          {
           $arrecrit[$j]=0;
		   $arrecrita[$j]=0;
          }

		  $ccoant   = $row2[0];
        }
		ELSE
		{
		  $ccoant   = $row2[0];
		}

	   }

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

	  $canti=$canti+1;

	 }
	 ELSE
	 {

	  echo "<Tr >";
	  echo "<td  bgcolor=#FFFFFF align=center><font size=1>".$ccoant."</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[1]/$canti)*100)."%</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[2]/$canti)*100)."%</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[3]/$canti)*100)."%</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[4]/$canti)*100)."%</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[5]/$canti)*100)."%</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[6]/$canti)*100)."%</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[7]/$canti)*100)."%</font></td>";
      echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[8]/$canti)*100)."%</font></td>";
	  echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($canti)."</font></td>";
      echo "</tr >";

	  $canti=0;
	  for ($j=1; $j <=8; $j++)
      {
        $arrecrit[$j]=0;
		$arrecrita[$j]=0;
      }

	  IF ($row2[2]=='on')
      {
      	$arrecrit[1]=$arrecrit[1]+1;
		$arrecrita[1]=$arrecrita[1]+1;
      }
      IF ($row2[3]=='on')
      {
      	$arrecrit[2]=$arrecrit[2]+1;
		$arrecrita[2]=$arrecrita[2]+1;
      }
      IF ($row2[4]=='on')
      {
      	$arrecrit[3]=$arrecrit[3]+1;
		$arrecrita[3]=$arrecrita[3]+1;
      }
	  IF ($row2[5]=='on')
      {
      	$arrecrit[4]=$arrecrit[4]+1;
        $arrecrita[4]=$arrecrita[4]+1;
	  }
	  IF ($row2[6]=='on')
      {
      	$arrecrit[5]=$arrecrit[5]+1;
		$arrecrita[5]=$arrecrita[5]+1;
      }
	  IF ($row2[7]=='on')
      {
      	$arrecrit[6]=$arrecrit[6]+1;
		$arrecrita[6]=$arrecrita[6]+1;
      }
	  IF ($row2[8]=='on')
      {
      	$arrecrit[7]=$arrecrit[7]+1;
        $arrecrita[7]=$arrecrita[7]+1;
	  }
	  IF ($row2[9]=='on')
      {
      	$arrecrit[8]=$arrecrit[8]+1;
        $arrecrita[8]=$arrecrita[8]+1;
	  }

	  $canti=$canti+1;
	  $swtitulo='SI';
	  $ccoant   = $row2[0];

	 }

  } // cierre del for

  IF ($canti==0)
   {
     echo "</table>";
   }
  ELSE
  {
    echo "<Tr >";
	echo "<td  bgcolor=#FFFFFF align=center><font size=1>".$row2[0]."</b></font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[1]/$canti)*100)."%</font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[2]/$canti)*100)."%</font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[3]/$canti)*100)."%</font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[4]/$canti)*100)."%</font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[5]/$canti)*100)."%</font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[6]/$canti)*100)."%</font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[7]/$canti)*100)."%</font></td>";
    echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format(($arrecrit[8]/$canti)*100)."%</font></td>";
	echo "<td  bgcolor=#FFFFFF align=center><font size=1>".number_format($canti)."</font></td>";
    echo "</tr >";

	echo "</table>";
  }

    echo "<table border=0 align=CENTER size=100%>";
    echo "<Tr >";
    echo "<td align=CENTER bgcolor=#FFFFFF ><font size=2 color=#000000><b>TOTAL DEL PROCESO POR TODOS LOS CENTROS DE COSTOS: </b></font></td>";
    echo "<td align=CENTER bgcolor=#FFFFFF ><font size=2 color=#000000>&nbsp;<b>$num1</b></font></td>";
    echo "</tr >";
    echo "</table>";

   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "<tr><td align=center><input type=button value='Volver Atrás' onclick='VolverAtras()'>&nbsp;|&nbsp;<input type=button value='Cerrar ventana' onclick='javascript:cerrarVentana();'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";


   echo "<br>";
   echo "<br>";
   echo "<table>";
   echo "<tr><td align='center'><input type='button' value='Graficar' onclick='pintarGrafica()'></td></tr>";
   echo "<tr>
			<td align='center'>
				<div id='amchart1' style='width:1000px; height:600px;' align='center'></div>
			</td>
		</tr>";
   echo "</table>";

 }// cierre del else donde empieza la impresión

}
?>