<html>
<head>
<title>MATRIX - [REPORTE PROCESOS PRIORITARIOS CLINICA]</title>

<script type="text/javascript">
	function inicio()
	{ 
	 document.location.href='rep_procepriocli.php'; 
	}
	
	function enter()

	{
		document.forms.rep_procepriocli.submit();
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
*                                             REPORTE PROCESOS PRIORITARIOS CLINICA                                                        *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : Reporte para ver en general los procesos prioritarios de clinica                                            |
//                            Esta basado en el Programa: rep_relPacientesUsoAntibiotico.php  creado por Edwin Molina Grisales              |
//AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                       |
//FECHA CREACION			  : JUNIO 4 DE 2012.                                                                                            |
//FECHA ULTIMA ACTUALIZACION  : JUNIO 4 DE 2012.                                                                                            |
//DESCRIPCION			      : Este reporte sirve para observar en general los procesos generados por unidad por año mes.                  |
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
				if ($val == "*-TODOS LOS CENTROS DE COSTO"){
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

$wactualiz="1.0 05-Junio-2012";

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
encabezado("Procesos Prioritarios clinica",$wactualiz,"clinica");

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
 echo "<form name='forma' action='rep_procepriocli.php?wemp_pmla=$wemp_pmla' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";
 
if (!isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '' or empty($txDestino))
  {
  	echo "<form name='rep_procepriocli?wemp_pmla=$wemp_pmla' action='' method=post>";
  
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
 	echo "<td class='fila1'>Fecha Inicial</td>";
 	echo "<td class='fila2' align='center'>";
 	campoFecha("fec1");
 	echo "</td></tr>";
 	
 		
 	//Fecha final
 	echo "<tr>";
 	echo "<td class='fila1'>Fecha Final</td>";
 	echo "<td class='fila2' align='center'>";
 	campoFecha("fec2");
 	echo "</td></tr>";

   echo "<tr><td align=center colspan=4><input type='submit' id='searchsubmit' value='GENERAR'></td></tr>";          //submit osea el boton de OK o Aceptar
   echo "</table>";
   echo '</div>';
   echo '</div>';
   echo '</div>';
   
  }
 else // Cuando ya estan todos los datos escogidos
  {
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION

    $ccos = crearIN( $txDestino );  	
    
    echo "<center><table border=1 cellspacing=0 cellpadding=0>";
    echo "<tr><td align=center colspan=4 bgcolor=#FFFFFF><font text color=#003366 size=2><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></font></b></font></td></tr>";
    echo "<tr><td align=center bgcolor=#006699><font text color=#FFFFFF size=2><b>PROCESO PRIORITARIO</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF size=2><b>EVALUADOS</b></font></td><td align=center bgcolor=#006699><font text color=#FFFFFF size=2><b>PROGRAMADOS</b></font></td>" .
			 "<td align=center bgcolor=#006699><font text color=#FFFFFF size=2><b>CUMPLIMIENTO %</b></font></td></tr>";

    $mesi=SUBSTR(".$fec1.",6,2);
    $mesf=SUBSTR(".$fec2.",6,2);
    
    $quer1 = "CREATE TEMPORARY TABLE if not exists tempora1 as "
            ."SELECT pames as mes,pacco as cco,papp as pp,Ppcproce as proc,patotal as total"
            ."  FROM ".$empre1."_000045 left join ".$empre1."_000046"
            ."    ON papp=ppcproce"
            ."   AND pames=SUBSTRING(Ppcfecha,6,2)" 
            ."   AND paano=SUBSTRING(Ppcfecha,1,4)"
            ."   AND Pacco $ccos"
            ."   AND pacco=ppccco"
            ." WHERE pames between '".$mesi."' and '".$mesf."'"
            ."   AND paano = SUBSTRING('".$fec2."',1,4)"
            ."   AND Pacco $ccos"
            ." GROUP BY 1,2,3,4,5"
            ." ORDER by 1,2,3";  
    
   // echo $quer1."<br>";         
            
    $err4 = mysql_query($quer1, $conex) or die("ERROR EN QUERY");
    
	$query1 = "SELECT SUBSTRING(Ppcfecha,6,2) as mes,ppccco as cco,ppcproce as pp,count(*) as cant,patotal as total"
            ."   FROM ".$empre1."_000046 left join ".$empre1."_000045"
            ."     ON ppcproce=papp" 
            ." AND SUBSTRING(ppcfecha,6,2)=pames" 
            ." AND SUBSTRING(ppcfecha,1,4)=paano"
            ." AND Ppccco $ccos" 
            ." AND Ppccco=pacco"
            ." WHERE ppcfecha between '".$fec1."' and '".$fec2."'"
            ."   AND Ppccco $ccos"
            ." GROUP by 1,2,3,5"
            ." UNION ALL"
            ." SELECT mes,cco,pp,0 as cant,total"
            ."   FROM tempora1"
            ."  WHERE proc IS NULL"
            ." ORDER by 1,2,3"; 
				
	//echo $query1."<br>"; 
				 
	$err1 = mysql_query($query1,$conex);
    $num1 = mysql_num_rows($err1);
	
	//echo mysql_errno() ."=". mysql_error();

    $swtitulo='SI';
    $swtitulo2='SI';
    $tmesant='';
    
    $ccoant='';
    $proceant=0;
    $mesant='';
    $ppant='';
    $evaant=0;
    $paant=0;
    $porcant=0;
    $porcenf=0;
    
	$toteva=0;
	$totpro=0;
	$totevacc=0;
	$totprocc=0;
	
	$totevaf=0;
	$totprogf=0;

    $wcfant='';
	
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
	   
   	if ($swtitulo=='SI')
	  {
       $tmesant = $row1[0];

	   echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>MES PROCESO : </b></font></td><td align=center colspan=3><font text size=2>".$tmesant."</td></tr>"; 
	   $swtitulo='NO';
 	  
	  }

	 if ($swtitulo2=='SI')
	  {
       $tmesant = $row1[0];
       $ccoant  = $row1[1];
	   echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>CENTRO DE COSTOS : </b></font></td><td align=center colspan=3><font text size=2>".$ccoant."</td></tr>"; 
	   $swtitulo2='NO';
 	   
	   if ($proceant<>0)
	    {
	    if ($evaant==0)
	    {
	     $porcant=0;
	    }
	    else
	    {
	     if ($paant=='')
	   	 {
	   	  $paant=0;
	      $porcant=100;
	   	 }
	   	 else
	   	 {
	   	  IF ($paant==0)
	   	  {
	   	   $paant=1;	
	   	  }
	   	 $porcant=($evaant/$paant)*100;		
	   	 }
	    }	
	     echo "<tr  bgcolor=".$wcfant."><td align=center><font text size=2>".$ppant."</td><td align=center><font text size=2>".number_format($evaant)."</td><td align=center><font text size=2>".number_format($paant)."</td></td><td align=center><font text size=2>".number_format($porcant)."</td></tr>"; 
	     $proceant=0;
		 
		 $toteva=$toteva+$evaant;
	     $totpro=$totpro+$paant;
	   
	     $totevacc=$totevacc+$evaant;
	     $totprocc=$totprocc+$paant;
	   
	     $totevaf=$totevaf+$evaant;
	     $totprogf=$totprogf+$paant;
		
	    }
	  }
	 if ($tmesant==$row1[0] )
	 {
	  	
	  IF ($ccoant==$row1[1])
	  {	
	 	 	
	  if ($row1[3]==0)
	  {
	  	$porcen=0;
	  }
	  else
	  {		
	   if ($row1[4]==0)
	   {
	   	$row1[4]=1;
	    $porcen=100;
	    $row1[4]=0;
	   }
	   else
	   {
	   	if ($row1[4]=='')
	   	{
	   	 $row1[4]=0;
	     $porcen=100;
	   	}
	   	else
	   	{
	   	$porcen=($row1[3]/$row1[4])*100;	
	   	}	   	
	   }
	  }	 
	   echo "<tr  bgcolor=".$wcf."><td align=center><font text size=2>".$row1[2]."</td><td align=center><font text size=2>".number_format($row1[3])."</td><td align=center><font text size=2>".number_format($row1[4])."</td><td align=center><font text size=2>".number_format($porcen)."</td></tr>"; 
	   $toteva=$toteva+$row1[3];
	   $totpro=$totpro+$row1[4];
	   
	   $totevacc=$totevacc+$row1[3];
	   $totprocc=$totprocc+$row1[4];
	   
	   $totevaf=$totevaf+$row1[3];
	   $totprogf=$totprogf+$row1[4];
	   
	  }
	  ELSE  //$ccoant==$row1[1]
	  {
       if ($totprocc==0)
	   {
	   	$porcenm=100;
	   }	
	   else
	   {	
	   $porcenm=($totevacc/$totprocc)*100;
	   }
	  	
	   echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>TOTAL CENTRO DE COSTO : </b></font></td><td align=center><font text size=2>".number_format($totevacc)."</td><td align=center><font text size=2>".number_format($totprocc)."</td><td align=center><font text size=2>".number_format($porcenm)."</td></tr>";
	   echo "<tr><td alinn=center colspan=6 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";

	   $swtitulo2='SI';
	   
	   $totevacc=0;
	   $totprocc=0;
	   
	   $ppant=$row1[2];
	   $evaant=$row1[3];
	   $proceant=1;
	   $paant=$row1[4];
	   $wcfant=$wcf;
	   $toteva=$toteva+$row1[3];
	   $totpro=$totpro+$row1[4];
	   
	   $totevacc=0;
	   $totprocc=0;
	   
	   $totevaf=$totevaf+$row1[3];
	   $totprogf=$totprogf+$row1[4];
	  	
	  }
	 }
	 else 
	  {
	   if ($totpro==0)
	   {
	   	$porcenm=100;
	   }	
	   else
	   {	
	   $porcenm=($toteva/$totpro)*100;
	   }
	  	
	   echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>TOTAL MES : </b></font></td><td align=center><font text size=2>".number_format($toteva)."</td><td align=center><font text size=2>".number_format($totpro)."</td><td align=center><font text size=2>".number_format($porcenm)."</td></tr>";
	   echo "<tr><td alinn=center colspan=6 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
	   $toteva=0;
	   $totpro=0;
	   $swtitulo='SI';
	   $swtitulo2='SI';
	   $ppant=$row1[2];
	   $evaant=$row1[3];
	   $proceant=1;
	   $paant=$row1[4];
	   $wcfant=$wcf;
	   $toteva=$toteva+$row1[3];
	   $totpro=$totpro+$row1[4];
	   $totevaf=$totevaf+$row1[3];
	   $totprogf=$totprogf+$row1[4];
	  }
	} //fin for
	
	if ($totprocc==0)
	   {
	   	$porcenm=100;
	   }	
	   else
	   {	
	   $porcenm=($totevacc/$totprocc)*100;
	   }
	  	
	   echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>TOTAL CENTRO DE COSTO : </b></font></td><td align=center><font text size=2>".number_format($totevacc)."</td><td align=center><font text size=2>".number_format($totprocc)."</td><td align=center><font text size=2>".number_format($porcenm)."</td></tr>";
	   echo "<tr><td alinn=center colspan=6 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>";
	
	if ($totpro==0)
	{
	$porcenm=100;	
	$porcenf=100;
	}	
	else
	{ 	
	$porcenm=($toteva/$totpro)*100;
	$porcenf=($totevaf/$totprogf)*100;
	}
	echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>TOTAL MES : </b></font></td><td align=center><font text size=2>".number_format($toteva)."</td><td align=center><font text size=2>".number_format($totpro)."</td><td align=center><font text size=2>".number_format($porcenm)."</td></tr>";
	echo "<tr><td alinn=center colspan=6 bgcolor=#FFFFFF><b>&nbsp;</b></td></tr>"; 
	echo "<tr><td align=center colspan=1 bgcolor=#006699><font text color=#FFFFFF size=2><b>TOTAL GENERAL MESES : </b></font></td><td align=center colspan=1><font text size=2>".number_format($totevaf)."</td><td align=center><font text size=2>".number_format($totprogf)."</td><td align=center><font text size=2>".number_format($porcenf)."</td></tr>";
	
	echo "</table>"; // cierra la tabla o cuadricula de la impresión
				
  } // cierre del else donde empieza la impresión
echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>"; 
echo "<tr><td align=center><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";	
echo "</table>";
}
?>