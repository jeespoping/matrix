<?php
include_once("conex.php");
/************************************************************************************************************
 * Programa		:	Configuracion Tipos de Datos
 * Fecha		:	2016-04-20
 * Por			:	Arleyda Insignares Ceballos
 * Descripcion	:	Script que adiciona la pestaña 'Tipo de Datos' en la configuracion de la
 * 					Historia Electronica. Su funcion en actualizar toda la tabla y/o adicionar las variables 
 * 					como activadas,	desactivadas, solo lectura, y obligatorias. 
 * 					Configura tambien y/o adiciona los tipos de datos.
 *	
 **********************************************************************************************************/
 
$wactualiz = "2016-05-16";

// ***************** Para que las respuestas ajax acepten tildes y caracteres especiales ******************
header('Content-type: text/html;charset=ISO-8859-1');

    //********************************** Inicio************************************************************
	

	include_once("root/comun.php");
	

	$conex = obtenerConexionBD("matrix");
	$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
	$wfecha=date("Y-m-d");
	$whora = (string)date("H:i:s");
	$pos = strpos($user,"-");
	$wusuario = substr($user,$pos+1,strlen($user));

	// **********************************TODAS LAS FUNCIONES DE PHP*******************************************
	// ***************** Funcion que muestra la Vista Inicial en el Formulario Configuracion *****************
	
	function IniciarVista()
	{
		global $wemp_pmla;
		global $wactualiz;
		global $wbasedato;
		global $conex;
		
		$vtipvar   = '';
		$listavar  = array();
		$vtipdat   = '';
		$vtipoide  = '';
		$tipodato  = array();
		$tipoide   = array();
		$vdescam   = array();
		$vdescam2  = array();
		$vtitpinta = array();
		
		//********************* Mostrar Titulo con logotipo y nombre *********************************************
		
		echo "<input type='hidden' id ='wemp_pmla' value='".$wemp_pmla."'/>";
		echo "<input type='hidden' id ='wtipo_gra' value='0'/>";
		echo "<input type='hidden' id ='wvariable_gra' value='0'/>";
		echo "<input type='hidden' id ='wfiltro' name='wfiltro' value='0'/>";
		echo "<input type='hidden' id ='wpagina' name='wpagina' value='0'/>";
		encabezado("CONFIGURACION TIPOS DE DATOS", $wactualiz, "clinica");

	    //***************** Consultar la tabla hce_000010 para recorrer el campo tipvar y llevarlo a un array

	     $q       = " SELECT tipvar,tipdat,id FROM ".$wbasedato."_000010 order by id";
		 $res     = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		 $contvar  = 1;
		 
		 while($row = mysql_fetch_assoc($res)){
		 	   if ($contvar==1)
	              {$vtipvar = $row['tipvar'];}
	           $vtipdat  .= $row['tipdat'].'-';
	           $vtipoide .= $row['id'].'-';
	           $contvar++;
		 }
		 $listavar= explode("-",$vtipvar);
		 $tipodato= explode("-",$vtipdat);
		 $tipoide = explode("-",$vtipoide);
		 $contador= count($tipodato);
	 
	    //*****************  Cargar Arrays con Titulos y Descripcion desde tablas de configuracion ************************ 

		$width_sel = " width: 95%; ";
		$u_agent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/MSIE/i',$u_agent))
			$width_sel = "";
		
		//Cargar la lista de variables
		$litipvari ="<option value=''></option>";
	    for ($x=0;$x<count($listavar); $x++)
	      {
	        $litipvari .= '<option value="'.$listavar[$x].'">'.$listavar[$x].'</option>';
		  }

        // *********************************    Cargar Descripcion de las variables     ************************************
        
		$q    = " SELECT A.descripcion,A.campo,B.dic_descripcion FROM det_formulario A inner join root_000030 B ".
 		        "  on A.campo = B.dic_campo and A.medico = B.dic_usuario and A.codigo = B.dic_formulario where A.medico='".$wbasedato."' and codigo='000002' ";       
		$res  = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

        $vpos =0;
 		while($row = mysql_fetch_assoc($res)){
	           $vdescam[$vpos]  = $row['dic_descripcion'];
	           $vdescam2[$vpos] = $row['descripcion'];
	           $vpos++;
			 }
     
	    //******************************** Pintar los titulos para toda la Tabla Matriz ***********************************

		
		echo "<div class='enlinea' style='width: 25%;'>";
		$max_paginas = 4;
		echo "<input type='hidden' id ='paginas' value='".$max_paginas."' />";
		echo "<input type='hidden' id ='pagina_visible' value='1' />";
		echo "<input type='hidden' id ='numpag_anterior' name='numpag_anterior' value='0' />";
		echo "<input type='hidden' id ='numpag_siguiente' name='numpag_siguiente' value='0' />";
		echo "<input type='hidden' id ='numpag_actual' name='numpag_actual' value='1' />";
		echo "</div>";
		echo "<div>";
		// Tabla Buscar para elaborar filtros por columna
		echo "<table align='center'>";
		echo "<tr>";
		echo '<td class="encabezadotabla" width="180px" align="center">Buscar</td>';
		echo "<td class='fila1' align='center'>";
		echo "<input type='text' id='buscador'  style='".$width_sel." margin:6px;' onChange='filtrarVariable(this.value)' />";
		echo "</td>";	
		echo "</tr>";
		echo "</table>";
        // Tabla de Paginacion
		echo '<table class="enlinea"  align="left" id="tabla_paginacion">';
		echo '<tr class="encabezadotabla">';
		echo '<td align="center" id="td_pagina" colspan="5">Pagina 1 de '.$max_paginas.'</td>';
		echo '</tr>';
		echo '<tr>';
		echo "<td onclick=\"cambiarPagina(1);\" title='Primera' style='font_size:8pt; width:20px; cursor:pointer; border-right: 4px #ffffff solid;' class='encabezadoTabla'>";
		echo '<font style="font-weight:bold">&nbsp;&nbsp;<<&nbsp;&nbsp;</font>';
		echo '</td>';
		echo "<td onclick=\"cambiarPagina(2);\" title='Anterior' style='font_size:8pt; width:20px; cursor:pointer; border-right: 4px #ffffff solid;' class='encabezadoTabla'>";
		echo '<font style="font-weight:bold">&nbsp;<&nbsp;</font>';
		echo '</td>';
		echo '<td>';
		echo "<select onchange=\"cambiarPagina(5);\" id='paginaMostrada' name='paginaMostrada'>";
		$i=1;
		while ( $i <= $max_paginas ){
			echo '<option value="'.$i.'">'.$i.'</option>';
			$i++;
		}
		echo '<option value="0">T</option>';
		echo '</select>';
		echo '</td>';
		echo "<td onclick=\"cambiarPagina(3);\" title='Siguiente' style='font_size:8pt; width:20px; cursor:pointer; border-right: 4px #ffffff solid;' class='encabezadoTabla'>";
		echo '<font style="font-weight:bold">&nbsp;>&nbsp;</font>';
		echo '</td>';
		echo "<td onclick=\"cambiarPagina(4);\" title='Ultima' style='font_size:8pt; width:20px; cursor:pointer; border-right: 4px #ffffff solid;' class='encabezadoTabla'>";
		echo '<font style="font-weight:bold">&nbsp;&nbsp;>>&nbsp;&nbsp;</font>';
		echo '</td>';
		echo '</tr>';
		echo '</table>';   
		echo "</br></br></br>";          
        echo "<table align='center'>";		      
        echo "<tr>";		
		echo "<td></td><td class='fila1' align=center colspan='".$contitulo."'><input type='button' id='adiciontipo' name='adiciontipo' value='Nuevo Tipo Dato'  onclick='NuevoTipodato()' />";
		echo "&nbsp;&nbsp;";
		echo "<input type='button' id='grabardat' name='grabardat' value='Actualizar'  onclick='GrabarTipodato()' />";
		echo "&nbsp;&nbsp;";
		echo "<input type='button' id='adicionvar' name='adicionvar' value='Nueva Variable' onclick='NuevaVariable()' />";
		echo "&nbsp;&nbsp;";
		echo "<input type='button' id='abrirventana' name='abrirventana' value='Ampliar' onclick='Ampliarventana()' />";		
		echo "&nbsp;&nbsp;";
		echo "<input type='button' id='cerrarventana' name='cerrarventana' value='Cerrar' onclick='Cerrarventana()' />";				
		echo "</td>";
		echo "</tr>";
		echo '</table>'; 	
		echo "<table>";
		echo "<tr><td align='left'><font size=2>";
		echo "<span style='align:left; color: blue;' />Activado : A=Activado   D=Desactivado   R=Solo Lectura / Obligatorio: S=Si N=No</span>";		
		echo "</font></td>";	
		echo "</tr>";  
        echo "</table>";
		echo "</div>";
	
        // ************************************************    Tabla Principal - Detalle  **********************************************
                                                                                                                                                         
        $Titulotot = count($listavar)+1; 
        echo '<div id="general" align="left" style="width: 100%;">';
		echo '<div id="divcolumnafija" style="float: left; width: 100px;">';
		echo '<table  border=1 bordercolor="white"  id="tblcolumnafija" name="tblcolumnafija" >';		
		echo '<tr style="font_size:4pt;background-color:#A9D0F5"><td width="20px" height="25px"><font size=4 color="A9D0F5"><b>L...................</b></font></td></tr>';
		echo "<tr style='font-size:12px'> <td width='20px' height='179px' align='left' style='font_size:4pt;background-color:#A9D0F5' class='fila1' rowspan='3'>Tipo de Dato</td>";
		echo '<tbody>';
		$varlin=0;
		for ($x=0;$x<count($tipodato)-1; $x++)
	     { 
	            $vfila1     = 'tipodatorow'.$x;
		        $vcolumna1  = 'tipodatocol0';
                $vfila2     = 'tipo2datorow'.$x;
                $vcolumna2  = 'tipo2datocol0';
                $vfilatr    = 'tipofila'.$x;
                $vcolumnatr = 'tipocolumna0';
			    $wcf        = "fila1";

     	    	echo "<tr  class='".$wcf."' onmouseover='ilumina(this,\"".$wcf."\",$x)'>";
				echo "<td width='20px' height='25px' fila='".$vfila1."' columna='".$vcolumna1."' >";
		     	echo $tipodato[$x];			     	
	     		echo "</td>";
	     		echo "<td width='0%' fila='".$vfila2."' columna='".$vcolumna2."' style='display:none'>";
		     	echo $tipoide[$x];	
	     		echo "</td>";	
	     		echo "</tr>";     	
	     }	       
        echo '<tr class="fila1" height="20px"><td><font size=4 color="A9D0F5"><b>L</b></font></td></tr>';
        echo '</tbody>';
        echo '</table>';
        echo '</div>';

		echo '<div id="divdetalle"  style="float:left;overflow-x:auto;overflow-y:hidden;width:1060px;height:auto">'; 
		echo '<table  border=1 bordercolor="white"  align="left" id="tbldetallevariable" name="tbldetallevariable" >';		
		echo '<thead>';
		echo "<tr class='encabezadotabla'><th colspan=".$Titulotot." align='center'><font size=4 color=white><b>LISTA TIPOS DE DATOS</b></font></th></tr>";
        
        // ***************************************** Cargar titulos de las variables ******************************************************
		
		echo "<tr style='font-size:12px'> <td width='5px' align='center' style='display:none;background-color:#A9D0F5' class='colfija' rowspan='3'>Tipo de Dato</td>";
		$varclsstd   = 0;
   		for ($y=0;$y<count($listavar); $y++)
    	    {		 	
    	       $varclsstd++ ;	     	     
    	       $varcelcal = 'pagina'. $varclsstd; 
    	       $vtitpinta = $listavar[$y];
    	       $vartitulo = $listavar[$y];
    	       echo '<td width="250px" height="30px" filtitulo="'.$vartitulo.'" style="font_size:4pt;background-color:#A9D0F5" align="center" class2="fila1" class="'.$varcelcal.' ocultar">"'.$listavar[$y].'"</td>';
    	    }
		echo "</tr>"; 		

		// **************************************** Cargar Descripcion de las variables **************************************************
		
		echo "<tr style='font-size:12px'>";
		$varclsstd   = 0;
        for ($y=0;$y<count($listavar); $y++)
    	    {	
    	       $vartitulo = $listavar[$y];		
               for($z=0;$z<count($vdescam2); $z++)
               {                                           
                 if (substr($listavar[$y],1,6) == strtolower($vdescam2[$z]))
                 {
                 	$varclsstd++ ;
                 	$varcelcal = 'pagina'.$varclsstd;
                    echo '<td height="100px" filtitulo="'.$vartitulo.'" style="font_size:4pt;background-color:#A9D0F5" align="center" class2="fila1" class="'.$varcelcal.' ocultar">"'.$vdescam[$z].'"</td>';  	
                 }
               }
            }		
		echo "</tr>"; 		

		// *************************** Cargar dos columnas Activado y obligatorio para toda la tabla **********************************
		
		echo "<tr style='font-size:10px'>";
		$varclsstd   = 0;
		$contitulo   =count($listavar); 
		for ($y=0;$y<count($listavar); $y++)
		    {
		       $vartitulo = $listavar[$y];
		       $varclsstd++ ;
		       $varcelcal = 'pagina'.$varclsstd;  

               if ($y>=0 and $y <=12)
               	 {  echo '<td align="center" width="5px" height="45px" filtitulo="'.$vartitulo.'" style="font_size:4pt;background-color:#A9D0F5" class2="fila1" class="'.$varcelcal.' ocultitulo1" >Activado(A/D/R)   Obligatorio(S/N)</td>';}
		         
               if ($y>=13 and $y <=24)
               	 { echo '<td align="center" width="5px" height="45px" filtitulo="'.$vartitulo.'" style="font_size:4pt;background-color:#A9D0F5" class2="fila1" class="'.$varcelcal.' ocultitulo2" >Activado(A/D/R)   Obligatorio(S/N)</td>';}

               if ($y>=25 and $y <=36)
              	 { echo '<td align="center" width="5px" height="45px" filtitulo="'.$vartitulo.'" style="font_size:4pt;background-color:#A9D0F5" class2="fila1" class="'.$varcelcal.' ocultitulo3" >Activado(A/D/R)   Obligatorio(S/N)</td>'; }

               if ($y>=37 and $y <$contitulo)
               	 { echo '<td align="center" width="5px" height="45px" filtitulo="'.$vartitulo.'" style="font_size:4pt;background-color:#A9D0F5" class2="fila1" class="'.$varcelcal.' ocultitulo4" >Activado(A/D/R)   Obligatorio(S/N)</td>';} 		        		      
			}

        echo "</tr>"; 		
		echo '</thead>';
        
        // *************************************** Pintar Contenido (detalle) de la Tabla Matriz *****************************************

		echo '<tbody>';
		$varlin=0;
		for ($x=0;$x<count($tipodato)-1; $x++)
		     { 
		            $vfila1     = 'tipodatorow'.$x;
			        $vcolumna1  = 'tipodatocol0';
                    $vfila2     = 'tipo2datorow'.$x;
                    $vcolumna2  = 'tipo2datocol0';
                    $vfilatr    = 'tipofila'.$x;
                    $vcolumnatr = 'tipocolumna0';

		     	    if (is_int ($jx/2))
					   $wcf="fila1";  // color de fondo de la fila
					else
					   $wcf="fila2"; // color de fondo de la fila
                   
	     	    	echo "<tr  class='".$wcf."' onmouseover='ilumina(this,\"".$wcf."\",$x)'>";
					echo "<td width='15%' height='25px' fila='".$vfila1."' columna='".$vcolumna1."' style='display:none' class='colfija'>";
			     	echo $tipodato[$x];			     	
		     		echo "</td>";
		     		echo "<td width='0%' height='25px' fila='".$vfila2."' columna='".$vcolumna2."' style='display:none'>";
			     	echo $tipoide[$x];			     	
		     		echo "</td>";		     		
                    
    		         // ******************* Consultar en la tabla hce_000010 filtrando por el campo tipvar *************
				     $q       = " SELECT tipvar,tipdat,tippan,tipobl FROM ".$wbasedato."_000010 where tipdat like '". $tipodato[$x] ."'";
					 $res     = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
					 $contvar = 0;
			 		 while($row = mysql_fetch_assoc($res))
		 			 {
		 			 	   $strtippan = $row['tippan'];
					 	   $strtipobl = $row['tipobl'];
				           $strtipvar = $row['tipvar'];
				           $strtipdat = $row['tipdat'];
			               
						   $arraytipvar = explode("-",$strtipvar);
						   $arraytippan = explode("-",$strtippan);
						   $arraytipobl = explode("-",$strtipobl);

						   $varposicion = array_search($wtipo_var,$arraytipvar);			   
			               $vartippan   = $arraytippan[$varposicion];
			               $vartipobl   = $arraytipobl[$varposicion];
			               $varclsstd   = 0;
			     		   for ($y=0;$y<count($arraytippan); $y++)
			     	       {		
			     	           $vfilasel1    = 'seldatorow'.$x;
						       $vcolumnasel1 = 'seldatocol'.$y;
			                   $vfilasel2    = 'sel2datorow'.$x;
			                   $vcolumnasel2 = 'sel2datocol'.$y; 
			                   $vfilacell    = 'filacell'.$y;
			                   $vcolumnacell = 'columnacell'.$y;
			     	       	   $varclsstd++ ;
			     	       	   $varcelcal    = 'pagina'.$varclsstd;
			     	       	   $vartitulo    = $arraytipvar[$y];
					     	   echo '<td height="25px" align=center filtitulo="'.$vartitulo.'" filacel="'.$vfilacell.'" columnacel="'.$vcolumnacell.'" onmouseover ="iluminacolumna(this,\''.$varcelcal.'\');" class="'.$varcelcal.' ocultar">';
				               // *************** Concatenar el option select de la variable tippan para mostrar en la tabla detalle
							   
							   $lisactivar2 =  '<select id="lisactivar[]" name="lisactivar" fila="'.$vfilasel1.'" columna="'.$vcolumnasel1.'" onChange="Revisarlisobligar(this,\''.$vfilasel2.'\',\''.$vcolumnasel2.'\')">';
				               if ($arraytippan[$y]=='E')
				                	{$variableE="<option selected value='E'>A</option>";}
				               else
				                	{$variableE="<option value='E'>A</option>";}
				               if ($arraytippan[$y]=='D')
				                	{$variableD="<option selected value='D'>D</option>";}
				               else
				                	{$variableD="<option value='D'>D</option>";}
				               if ($arraytippan[$y]=='R')
				                	{$variableR="<option selected value='R'>R</option>";}
				               else
								    {$variableR="<option value='R'>R</option>";}
										
							   $lisactivar2 .= $variableE . $variableD . $variableR ."</select>";

							   // ************** Concatenar el option select de la variable tipobl para mostrar en la tabla detalle
								 
				               $lisonoff = '<select  id="lisobligar[]" name="lisobligar" fila="'.$vfilasel2.'" columna="'.$vcolumnasel2.'" onChange="Revisarlisobligar(this,\''.$vfilasel1.'\',\''.$vcolumnasel1.'\')">';
				               if ($arraytipobl[$y]=='on')
				               		{$variableOn="<option selected value='on'>S</option>";}
				           	   else
				           	   		{$variableOn="<option value='on'>S</option>";}
				           	   if ($arraytipobl[$y]=='off')
				           	   		{$variableOff="<option selected value='off'>N</option>";}
				           	   else
				           	   		{$variableOff="<option value='off'>N</option>";}

				           	   $lisonoff .= $variableOn. $variableOff . "</select>";	

					     		echo $lisactivar2;
					     		echo $lisonoff;

							    echo "</td>";				     		
					        }
						 echo "</tr>";
		     		 }	
		     		 // ********************************** Fin consulta hce_000010 *************************************
		     	}
		echo "</tbody>";
		echo "<tr>";		
		echo "<td width='180px' class='fila1' align=left colspan='".$contitulo."'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' id='adiciontipo' name='adiciontipo' value='Nuevo Tipo Dato'  onclick='NuevoTipodato()' />";
		echo "&nbsp;&nbsp;";
		echo "<input type='button' id='grabardat' name='grabardat' value='Actualizar'  onclick='GrabarTipodato()' />";
		echo "&nbsp;&nbsp;";
		echo "<input type='button' id='adicionvar' name='adicionvar' value='Nueva Variable' onclick='NuevaVariable()' />";
		echo "&nbsp;&nbsp;";
		echo "<input type='button' id='abrirventana' name='abrirventana' value='Ampliar' onclick='Ampliarventana()' />";		
		echo "&nbsp;&nbsp;";
		echo "<input type='button' id='cerrarventana' name='cerrarventana' value='Cerrar' onclick='Cerrarventana()' />";				
		echo "</tr>";
		echo "</table>";
		echo "</div>";
		echo "<br></br>";	    				
		echo "<br></br>";
		//Mensaje de alertas
		echo "<div id='msjAlerta' style='display:none;'>";
		echo '<br>';
		echo "<img src='../../images/medical/root/Advertencia.png'/>";
		echo "<br><br><div id='textoAlerta'></div><br><br>";
		echo '</div>';
		echo "</div>";
		echo "<br></br>";
		echo "<div>";
		echo "<p><span style='color: blue;' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Activado : A=Activado D=Desactivado R=Solo Lectura   |   Obligatorio: S=Si N=No</span></p>";			
		echo "</div>";
}

// *****************************************         FIN PHP         *****************************************************
if( isset($consultaAjax) == false ){

	?>
	<html>
		<head>
		<title>Configuracion Tipos de Datos</title>
		<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>	
		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
    	<link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" />
        </script>
		<script type="text/javascript">
		  $(document).ready(function(){
	        $( "#Cerrarventana" ).prop( "disabled", true );    
	      });  
	
	// **************************************   Funciones Javascript   ************************************************

	var celda_ant;
	celda_ant="";
	celda_ant_clase=""; 
		
	// *************** FUNCION Valida que el option obligatorio no sea seleccionado para campos desactivados o de solo lectura *****
	
	function Revisarlisobligar(obj,param1,param2)
	{
       var vsigparam    = $("select[fila="+param1+"][columna="+param2+"]").find("option:selected").val();

       if ((obj.value=='D' || obj.value=='R') && (vsigparam=='on'))
          { alerta("Un Campo obligatorio no puede estar inactivo o solo lectura");
            obj.value='E';
          }     
       if ((vsigparam=='D' || vsigparam=='R') && (obj.value=='on'))
          { alerta("Un Campo obligatorio no puede estar inactivo o solo lectura");
            obj.value='off';
          }  
     }

	// ******************* FUNCION Adiciona una variable a la tabla (000010) para agregar una variable *********************
	
	function NuevaVariable()
	{   
	    document.getElementById('adiciontipo').disabled=true;
	    document.getElementById('adicionvar').disabled=true;

        var wemp_pmla =  $("#wemp_pmla").val();
        $("#wvariable_gra").val('1'); 
 		var table    = document.getElementById('tbldetallevariable');

  		// Adicionar Titulo en la segunda fila a la derecha, Se le adiciona un input para el usuario
	    var colCount2 = document.getElementById('tbldetallevariable').rows[1].cells.length; 
		var row    = table.rows[1];
		var cell1  = row.insertCell(colCount2);
		cell1.style.backgroundColor  = '#A9D0F5';
		cell1.style.textAlign = "center";
    	cell1.innerHTML  = "<input type='text' align='center' style='text-align:center;' id='txtnuevavar' name='txtnuevavar' />";


		// Adicionar Titulo en la tercera fila a la derecha
	    var colCount2 = document.getElementById('tbldetallevariable').rows[3].cells.length; 
		var row    = table.rows[3];
		var cell1  = row.insertCell(colCount2);
		cell1.style.fontsize   = "6px";
		cell1.style.backgroundColor  = '#A9D0F5';
    	cell1.innerHTML  = ' Activado(A/D/R) Obligatorio(S/N) ';


        // Adicionar lista de celdas a la derecha 
	    var totfilas = document.getElementById('tbldetallevariable').rows.length;  
	    var colCount = document.getElementById('tbldetallevariable').rows[4].cells.length; 
        var rowCount = table.rows.length;
        for(var i=4; i<rowCount-1; i++){      
              var row    = table.rows[i];
              var cell1    = row.insertCell(colCount);
              // Crear los dos select option activado y obligatorio
              vfilasel1    = 'vfil1variable'+i;
              vcolumnasel1 = 'vcol1variable'+i;
              vfilasel2    = 'vfil2variable'+i;
              vcolumnasel2 = 'vcol2variable'+i;  
              lisactivar =  '<select id="lisactivar[]" name="lisactivar" fila="'+ vfilasel1 +'" columna="'+ vcolumnasel1 +'" >'+
							"<option value='E'>A</option>"+
							"<option value='D'>D</option>"+
						    "<option value='R'>R</option>"+
							"</select>";

		      lisobligar =  '<select  id="lisobligar[]" name="lisobligar" fila="'+ vfilasel2 +'" columna="'+ vcolumnasel2 +'" >'+
							"<option value='on'>S</option>"+
							"<option value='off'>N</option>"+
							"</select>";

			  cell1.style.textAlign = 'center' ;
              cell1.innerHTML  =lisactivar+lisobligar;
       		}		
	}


    function Ampliarventana()
    { 
      window.open(window.location.href, "TIPO","width=2000,height=1000,scrollbars=YES") ;
      $( "#Cerrarventana" ).prop( "disabled", false );    
    }

    function Cerrarventana()
    {

      window.close(window.location.href);
    }

    // ************************** FUNCION Adiciona un registro a la tabla (000010) para agregar un tipo de dato ********************
	function NuevoTipodato()
	{    
		document.getElementById('adicionvar').disabled=true;
		document.getElementById('adiciontipo').disabled=true;

	    $( "#grabardat" ).prop( "disabled", false );    
		var wemp_pmla =  $("#wemp_pmla").val();
		$("#wtipo_gra").val('1'); 
		var totcolumna = document.getElementById('tbldetallevariable').rows[3].cells.length;        
	    $("td.colfija").show();  

		$.post("Tipodatos.php",
		{
			consultaAjax:   true,
			accion:         'obtenerRegistro',
			wemp_pmla:      wemp_pmla,
			totcolumna:     totcolumna
			}, function(respuesta){
				 $("#tbldetallevariable").append(respuesta);					
			});        
	}

    // ************************** FUNCION Grabar la ultima fila de la tabla (tipo de dato)  ****************************************
    function GrabarTipodato()
    {
    	 var wemp_pmla =  $("#wemp_pmla").val();

	     if ($("#wtipo_gra").val()=='1') // grabar nuevo tipo de datos
	     {
	     	 if ($("#addregistro").val()=='')
	     	 {
              alerta('Debe ingresar Tipo de Dato');
              return;
	     	 }
		     var vstringlis1  = '';
		     var vstringlis2  = ''; 
		     var vnuetipdat   = $("#addregistro").val();

			 var totcolumna  = document.getElementById('tbldetallevariable').rows[4].cells.length;  
		     for($x=1;$x<totcolumna-1;$x++)
		        {
		          vfila1  = 'addlista1row'+$x;
		          vcolum1 = 'addlista1col'+$x;	
		          vfila2  = 'addlista2row'+$x;
		          vcolum2 = 'addlista2col'+$x;

		          var vsellis1    = $("select[fila="+vfila1+"][columna="+vcolum1+"]").find("option:selected").val();
		          var vsellis2    = $("select[fila="+vfila2+"][columna="+vcolum2+"]").find("option:selected").val();
		          if ($x==1)
		           {
			          vstringlis1  = vsellis1;
			          vstringlis2  = vsellis2;
		           }
		           else
		           {
			          vstringlis1  = vstringlis1 + '-' + vsellis1;
			          vstringlis2  = vstringlis2 + '-' + vsellis2;
			       }    
		        }

	        // **************************   Llamada por Ajax para grabar registro de nuevo tipo de dato **************************
	        $.post("Tipodatos.php",
			  {
				consultaAjax:   true,
                async :         false,			
				accion:         'grabarTipodato',
				wemp_pmla:      wemp_pmla,
				vnuetipdat:     vnuetipdat,
				vstringlis1:    vstringlis1,
				vstringlis2:    vstringlis2
				}, function(respuesta){
					 if(respuesta==1)
					 {
					 	var alerta = 'Registro Grabado';
						jAlert(alerta, "Mensaje");
					 }
				});

	         $("#wtipo_gra").val('0');
	         document.getElementById('adicionvar').disabled=false;
	         document.getElementById('adiciontipo').disabled=false;
             window.close(window.location.href);

         }
         else  // ****************************** Actualizar toda la Matriz en la tabla 000010 *************************************
         {

         	if ($("#wvariable_gra").val()=='1') // grabar nueva variable
         	{
              if($("#txtnuevavar").val()=='')
              {
	              alerta('Debe ingresar Nueva Variable');
	              return;
              }
	
              vtxtvariable = $("#txtnuevavar").val();
              var varrayvar1  = [];
         	  var varrayvar2  = []; 
         	  var varrayide   = []; 
         	  var totfilas    = document.getElementById('tbldetallevariable').rows.length;

         	  for(x=0;x<=totfilas-6;x++)    
		        {
 				  vfila2  = 'tipo2datorow'+x;
				  vcolum2 = 'tipo2datocol0';

		          var vsellis1 = $("td[fila='"+vfila2+"'][columna='"+vcolum2+"']").html();
				  varrayide[x]  = vsellis1;

                  conultimo = x + 4;
         	      vfila1  = 'vfil1variable'+conultimo;
		          vcolum1 = 'vcol1variable'+conultimo;	
		          vfila2  = 'vfil2variable'+conultimo;
		          vcolum2 = 'vcol2variable'+conultimo;
                  
		          var vsellis2    = $("select[fila="+vfila1+"][columna="+vcolum1+"]").find("option:selected").val();
		          var vsellis3    = $("select[fila="+vfila2+"][columna="+vcolum2+"]").find("option:selected").val();

		          if (vsellis3 =='on' && (vsellis2 =='D' || vsellis2 =='R'))
		          {
		          	alerta('Hay campos Obligatorios y Desactivados, Verifique');
					//jAlert(alerta, "Mensaje");
		          	return;
		          }

		          varrayvar1[x]   = vsellis2;
		          varrayvar2[x]   = vsellis3;
         		}

         		$.post("Tipodatos.php",
				   {
					consultaAjax:  true,
					accion:        'grabarVariable',
					wemp_pmla:     wemp_pmla,
					vtxtvariable:  vtxtvariable,
					varrayide:     varrayide,
					varraylis1:    varrayvar1,
					varraylis2:    varrayvar2				
					}, function(respuesta){
						  if(respuesta==1)
						 {
						 	var alerta = 'Registro grabado';
							jAlert(alerta, "Mensaje");
						 }
					});
         		   $("#wvariable_gra").val('0');
         		   document.getElementById('adiciontipo').disabled=false;
         		   document.getElementById('adicionvar').disabled=false;
       			   window.close(window.location.href);
         	}	            

         	else // Grabar toda la tabla
         	{
         	 var varraytipo  = [];
         	 var varraylis1  = [];
         	 var varraylis2  = [];
         	 var varrayide   = [];
			 var wemp_pmla   = $("#wemp_pmla").val();
			 var totcolumna = document.getElementById('tbldetallevariable').rows[4].cells.length; 
			 var totfilas   = document.getElementById('tbldetallevariable').rows.length;  
             totfilas2      = totfilas-5

			 for(x=0;x<totfilas2;x++)
		        {
		                 vfila1  = 'tipodatorow'+x;
				         vcolum1 = 'tipodatocol0';	
				         vfila2  = 'tipo2datorow'+x;
				         vcolum2 = 'tipo2datocol0';

				         var vsellis3  = $("td[fila='"+vfila1+"'][columna='"+vcolum1+"']").html();
				         var vsellis4  = $("td[fila='"+vfila2+"'][columna='"+vcolum2+"']").html();

					     varraytipo[x] = vsellis3;
					     varrayide[x]  = vsellis4;

		                 vseleccion1   = '';
		                 vseleccion2   = '';
					     
					     for(y=0;y<totcolumna-2;y++)
					        {
					          vfila1  = 'seldatorow'+x;
					          vcolum1 = 'seldatocol'+y;	
					          vfila2  = 'sel2datorow'+x;
					          vcolum2 = 'sel2datocol'+y;
                              
					          var vsellis1    = $("select[fila="+vfila1+"][columna="+vcolum1+"]").find("option:selected").val();
					          var vsellis2    = $("select[fila="+vfila2+"][columna="+vcolum2+"]").find("option:selected").val();
                                                            
					          if (y==0)
					           {
						          vstringsel1  = vsellis1;
						          vstringsel2  = vsellis2;
					           }
					           else
					           {
						          vstringsel1  = vstringsel1 + '-' + vsellis1;
						          vstringsel2  = vstringsel2 + '-' + vsellis2;
						       }    					          						      
					        }
					        varraylis1[x] = vstringsel1;
					        varraylis2[x] = vstringsel2;
				  }      	

				$.post("Tipodatos.php",
				  {
					consultaAjax:  true,
					accion:        'grabarMatriz',
					wemp_pmla:     wemp_pmla,
					varraytipo:    varraytipo,
					varrayide:     varrayide,
					varraylis1:    varraylis1,
					varraylis2:    varraylis2				
					}, function(respuesta){
						  if(respuesta==1)
						 {
	                        var alerta = 'Registro Grabado';
							jAlert(alerta, "Mensaje");
						 }
					});
			}

         }
	}

    // ***********************************   FUNCION Ilumina la fila donde se ubique el mouse de la tabla detalle  ****************
	function ilumina(celda,clase,posi){

        // Iluminar toda la fila        
		if (celda_ant=="")
		{
			celda_ant = celda;
			celda_ant_clase = clase;
		}

		celda_ant.className = celda_ant_clase;
		celda.className = 'fondoAmarillo';
		celda_ant = celda;
		celda_ant_clase = clase;
	}

    // **************************************** FUNCION Ilumina toda la columna donde se ubique el mouse *************************
    function iluminacolumna(celda,columna)
    {
    	$("td.fondoAmarillo").removeClass('fondoAmarillo');
      	$("."+columna).addClass("fondoAmarillo");      	
    }

    // ********************** FUNCION para filtrar columnas segun la variable digitada por el usuario en el cuadro buscar  *******
	function filtrarVariable(campo)
	{
		if($("td[filtitulo="+campo+"]").length > 0)
		{			
		    document.getElementById('adiciontipo').disabled=true;
			$('#wfiltro').val('1');
			$("td.ocultar").hide();
			$("td.ocultitulo1").hide();  
			$("td.ocultitulo2").hide();  
			$("td.ocultitulo3").hide();  
			$("td.ocultitulo4").hide();  
			$("td[filtitulo="+campo+"]").show();	
		}
		else
		{
			document.getElementById('adiciontipo').disabled=false;
			$('#wfiltro').val('0');
			$('#wpagina').val('0');
			$("td.ocultar").show();
			$("td.ocultitulo1").show();  
			$("td.ocultitulo2").show();  
			$("td.ocultitulo3").show();  
			$("td.ocultitulo4").show();  
		}
	}

    // ***************************************  FUNCION muestra Pagina de 1 a 4 mostrando 12 columnas aprox por pagina ******************
    function cambiarPagina(opcion)
    {
      //Desactivar opcion nuevo tipo de dato	
      document.getElementById('adiciontipo').disabled=true;
      $('#wpagina').val('1');

      // Contar el numero de columnas de la tabla     
      var colCount = document.getElementById('tbldetallevariable').rows[4].cells.length; 

      // Ocultar las 5 zonas que componen la tabla
      $("td.ocultar").hide();  
      $("td.ocultitulo1").hide();  
      $("td.ocultitulo2").hide();  
      $("td.ocultitulo3").hide();  
      $("td.ocultitulo4").hide();  

      if (opcion==5)
        { 
         opcion = parseInt($('#paginaMostrada').val());
        }	

      else
      {
	      // Configurar numero de pagina
	      if(($('#numpag_actual').val()==1 || $('#numpag_actual').val()==3) && opcion == 3)
	      {
	        varsum=parseInt($('#numpag_actual').val())+1;
	        $('#numpag_actual').val(varsum);
	        opcion=varsum;   
	        if($('#numpag_actual').val() >= 4)
	        	{opcion = 4;}             
	      }
	      else
	      {
		  if(($('#numpag_actual').val()==4 || $('#numpag_actual').val()==2) && opcion == 2)
		      {
		        varsum=parseInt($('#numpag_actual').val())-1;
		        $('#numpag_actual').val(varsum);
		        opcion=varsum;
		        if($('#numpag_actual').val() <= 1)
		        	{opcion = 1;}  
		      }
	      }

	      if($('#numpag_actual').val()==4 && opcion == 3)
	      {
	        opcion=4;}

	      if($('#numpag_actual').val()==1 && opcion == 2)
	      {
	        opcion=1;}
	      // Fin configuracion numero de pagina
	  }
  
	  // Seleccionar pagina a mostrar
      switch(opcion) {
        case 1:
	         varini=1;
	         varfin=13;
	         $("td.ocultitulo1").show();  
	         vpagina=parseInt($('#numpag_anterior').val())+1;
	         $('#numpag_anterior').val(vpagina);
	         break;

        case 2:
             vpagina=parseInt($('#numpag_anterior').val())+1;
             $('#numpag_anterior').val(vpagina);
             $('#numpag_siguiente').val(0);
			 varini=14;
	         varfin=25;
	         $("td.ocultitulo2").show();  
	         break;

        case 3:
             vpagina=parseInt($('#numpag_siguiente').val())+1;
             $('#numpag_siguiente').val(vpagina);
             $('#numpag_anterior').val(0);
			 varini=26;
	         varfin=37;
	         $("td.ocultitulo3").show();  
	         break;

        case 4:
			 varini=38;
	         varfin=colCount+2;
	         $("td.ocultitulo4").show();  
	         vpagina=parseInt($('#numpag_siguiente').val())+1;
	         $('#numpag_siguiente').val(vpagina);
	         break;
        
        case 0:
             document.getElementById('adiciontipo').disabled=false;
             $('#wfiltro').val('0');
             $('#wpagina').val('0');
             $('#buscador').val('');
			 $("td.ocultar").show();
			 $("td.ocultitulo1").show();  
			 $("td.ocultitulo2").show();  
			 $("td.ocultitulo3").show();  
			 $("td.ocultitulo4").show();  
             break;
      }

	  $('#paginaMostrada').val(opcion);
      $('#numpag_actual').val(opcion);

      // Mostrar las columnas segun la pagina seleccionada
      for (x=varini;x<=varfin;x++)
        {
       	  columna='pagina'+x;
          $("."+columna).show();
        }

    }

    // ********************************  FUNCION Sacar un mensaje de alerta con formato predeterminado  *************************
	function alerta(txt){
		$("#textoAlerta").text( txt );
		$.blockUI({ message: $('#msjAlerta') });
			setTimeout( function(){
						   $.unblockUI();
						}, 1800 );
	}
    // ****************************************    FIN Funciones Javascript    ********************************************
	</script> 

	</head>
	<body>		
		<?php 
		  echo "<input type='hidden' value='".$wemp_pmla."'>";
		  IniciarVista();  
		?>
	</body>
	</html>
	<?php
	
} else{

	// *******************************    CUANDO SE HACE EL LLAMADO POR AJAX  **********************************************************
	if (isset($_POST["accion"]) && $_POST["accion"] == "obtenerRegistro"){
            
        // ******************* Buscar contenido para los campos select option adicionando un registro en la ultima posicion de la tabla
 
	           $body .= "<tr class='fila2'>";
	           $body .= "<td align=left >";
	           $body .= "<input type='text' size='14' id='addregistro' name 'addregistro'/>";
	           $body .= "</td>";

               for ($x=1;$x<$totcolumna+1; $x++)
	      	   {
			      // Llenar las opciones de activado y obligatorio tabla detalle
			      $vfila    = 'addlista1row'.$x;
			      $vcolumna = 'addlista1col'.$x;
			      $vfila2   = 'addlista2row'.$x;
			      $vcolumna2= 'addlista2col'.$x;  
			      $lisactivar =  '<select fila="'.$vfila.'" columna="'.$vcolumna.'" id="lisactivar[]" name="lisactivar" onChange="Revisarlisobligar(this,\''.$vfila2.'\',\''.$vcolumna2.'\')">
								<option value="E">A</option>
								<option value="D">D</option>
								<option value="R">R</option>
								</select>';

				  $lisobligar =  '<select fila="'.$vfila2.'" columna="'.$vcolumna2.'" id="lisobligar[]" name="lisobligar" onChange="Revisarlisobligar(this,\''.$vfila.'\',\''.$vcolumna.'\')">
								<option value="on">S</option>
								<option value="off">N</option>
								</select>';

		           $body .= "<td align=center id='celnueva' name='celnueva'>";
		           $body .= $lisactivar;
		           $body .= $lisobligar;		           
		           $body .= "</td>";
	           }
	           $body .= "</td>";
	           $body .= "</tr>";

			   echo $body;
	}

	if (isset($_POST["accion"]) && $_POST["accion"] == "grabarTipodato"){

        // ***********************************   Grabar ultimo registro de la tabla que contiene nuevo tipo de dato    ***************************
        $q       = " SELECT tipvar,tipdat,tippan,tipobl FROM ".$wbasedato."_000010";
		$res     = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
		while($row = mysql_fetch_assoc($res))
		     {
		 	     $strtipvar = $row['tipvar'];
		 	 }

	    $q =   " INSERT INTO ".$wbasedato."_000010 "  
	                  ." (Medico,Fecha_data,Hora_data,Tipdat,Tippan,Tipobl,Tipvar,Seguridad) "
					  ." VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$vnuetipdat."','".$vstringlis1."','".$vstringlis2."','".$strtipvar."','C-".$wusuario."') ";
				$res_graba = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    
        echo $res_graba;
		}

        // ***********************************   Grabar toda la tabla realizando update ***********************************************************
	    if (isset($_POST["accion"]) && $_POST["accion"] == "grabarMatriz"){

           for ($x=0;$x<=count($varrayide);$x++) 
           {
           	    if($varrayide[$x]>0)
           	    {
	           	 	$q = " UPDATE ".$wbasedato."_000010 "
						." 	  SET Fecha_data='".$wfecha."',Hora_data='".$whora."',Tippan='".$varraylis1[$x]."',Tipobl='".$varraylis2[$x]."' "
						."  WHERE id = '".$varrayide[$x]."' ";
					$res_actualiza = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				}
           }
           echo $res_actualiza;                 
	    }

	    // ************************************   Grabar nueva variable       *********************************************************************
	    if (isset($_POST["accion"]) && $_POST["accion"] == "grabarVariable"){
 
           for ($x=0;$x<=count($varrayide);$x++)
           {                
				if($varrayide[$x]>0)
                {
                	//Seleccionar Campos para concatenar con la nueva variable
					$q       = " SELECT tipvar,tipdat,tippan,tipobl FROM ".$wbasedato."_000010  where id='".$varrayide[$x]."' ";
					$res     = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
					while($row = mysql_fetch_assoc($res))
					     {
					 	     $strtippan = $row['tippan'].'-'.$varraylis1[$x];
					 	     $strtipobl = $row['tipobl'].'-'.$varraylis2[$x];
					 	     $strtipvar = $row['tipvar'].'-'.$vtxtvariable;
					     }

	                // Actualizar campos para adicionar variable
           	 		$q  = " UPDATE ".$wbasedato."_000010 "
						." 	SET Fecha_data='".$wfecha."',Hora_data='".$whora."',Tippan='".$strtippan."',Tipobl='".$strtipobl."',Tipvar='".$strtipvar."' "
						."  WHERE id = '".$varrayide[$x]."' ";
				}
				$res_actualiza = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
           }      
           echo $res_actualiza;
	    }
}
?>