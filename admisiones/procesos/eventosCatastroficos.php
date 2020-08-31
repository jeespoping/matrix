<?php
include_once("conex.php");
/*************************************************************************
 * Cuantos hay por fila
 * $tipo		tipo de evento
 * $porFila 	Cuantos eventos del tipo dado se van a mostrar por fila
 *************************************************************************/
function pintarEventosCatastroficos( $tipo, $porFila ){

	global $conex;
	global $wbasedato;

	$sql = "SELECT
				*
			FROM
				{$wbasedato}_000154
			WHERE
				Evncla = '$tipo'
				AND Evnest = 'on'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
	
		for( $i = 0, $j = 1; $rows = mysql_fetch_array( $res ); $i++, $j++ ){
		
			$class = "fila".($j%2+1);
			
			if( $i == 0 ){
				echo "<tr>";
				
				if( $tipo == 'CT' ){
					echo "<th class='fila1' rowspan='".ceil( $num/$porFila )."'>Evento natural</th>";
				}
				else{
					echo "<th class='fila1' rowspan='".ceil( $num/$porFila )."'>Evento catastr&oacute;fico</th>";
				}
			}
			elseif( $i%$porFila == 0 ){
				echo "<tr>";
			}
			
			echo "<td colspan='4' align='right' class='$class'>".$rows[ 'Evndes' ]."</td>"; 
			echo "<td align='rigth' style='width:40' class='$class'>";
			echo "<INPUT type='radio' name='det_Catevento' value='".$rows[ 'Evncod' ]."' onClick='asignarTipoEvento( \"$tipo\" );' msgError>";
			echo "</td>";
			
			if( $i%$porFila == 3 ){
				echo "</tr>";
			}
		}
	}
}

echo "<div id='eventosCatastroficos' style='display:none'>";
echo "<form id='frEvento'>";
echo "<div acordeon1>";
echo "<h3>EVENTOS CATASTROFICOS</h3>";

echo "<div>";

echo "<div id='hiOcultos'>";
echo "<INPUT type='hidden' name='dat_Acchis' value=''>";
echo "<INPUT type='hidden' name='dat_Accing' value=''>";
echo "<INPUT type='hidden' name='det_ux_evccec' value=''>";
echo "</div>";

echo "<center>";

echo "<table>";

/********************************************************************************************************************************************
 * NATURALEZA DEL EVENTO
 ********************************************************************************************************************************************/
echo "<tr class='encabezadotabla'>";
echo "<td colspan='21' align='center'>NATURALEZA DEL EVENTO</td>";
echo "</tr>";

pintarEventosCatastroficos( "CT", 4 );
echo "<tr></tr>";	//Tr en blanco para que se note el espacio de división entre eventos naturales y catastróficos
pintarEventosCatastroficos( "ET", 4 );
/*********************************************************************************************************************************************///Fin Eventos

echo "<tr class='encabezadotabla'>";
echo "<td colspan='21' align='center'>DATOS DEL SITIO DONDE OCURRIO EL EVENTO</td>";
echo "</tr>";

echo "<tr class='fila2'>";

echo "<td class='fila1'>Fecha y hora del evento</td>";
echo "<td colspan='2'>";
echo "<INPUT type='text' name='det_Catfac_ux_evcfec' fecha msgError='YYYY-MM-DD'>";
echo "</td>";
echo "<td colspan='18'>";
echo "<INPUT type='text' name='det_Cathac_ux_evchor' hora style='width:70'  msgError='HH:MM:SS'>";
echo "</td>";

echo "</tr>";

echo "<tr>";
echo "<td class='fila1' colspan='2'>Direcci&oacute;n</td>";
echo "<td class='fila1' colspan='9'>Detalle de la direcci&oacute;n</td>";
echo "<td class='fila1' colspan='4'>Departamento</td>";
echo "<td class='fila1' colspan='4'>Municipio</td>";
echo "<td class='fila1' colspan='2'>Zona</td>";
echo "</tr>";

echo "<tr  class='fila2'>";
echo "<td colspan='2'>";
echo "<INPUT type='text' id='dirEvento' name='det_Catdir_ux_evcdir' style='width:100%' msgError='Digite la dirección' depend='detDirEvento'>";
echo "</td>";
echo "<td colspan='9'>";
echo "<textarea name='det_Catded_ux_evcdir' style='width:100%' msgError='Digite el Detalle de la dirección' id='detDirEvento' depend='dirEvento'></textarea>";
echo "</td>";
echo "<td colspan='4'>";
echo "<INPUT type='text' name='Catdep' id='Catdep' style='width:100%' msgError='Digite el Departamento'>";
echo "<INPUT type='hidden' name='det_Catdep_ux_evcdep' id='det_Catdep_ux_evcdep'>";
echo "</td>";
echo "<td colspan='4'>";
echo "<INPUT type='text' name='Catmun' id='Catmun' style='width:100%' srcDep='det_Catdep_ux_evcdep' msgError='Digite el Municipio'>";
echo "<INPUT type='hidden' name='det_Catmun_ux_evcmun' id='det_Catmun_ux_evcmun'>";
echo "</td>";
echo "<td colspan='2'>";
$res = consultaMaestros( "000105", "Selcod, Seldes", "Seltip = '05'", "", "", 0 );
crearSelectHTMLAcc(  $res, "", "det_Catzon_ux_evczon", "msgError"  );
echo "</td>";
echo "</tr>";

echo "<tr>";
echo "<td class='fila1' colspan='21'>Descripci&oacute;n breve</td>";
echo "</tr>";

echo "<tr class='fila2'>";
echo "<td colspan='21'>";
echo "<textarea name='det_Catdes_ux_evcdes' style='width:100%' msgError='Digite una descripción breve del accidente'></textarea>";
echo "</td>";
echo "</tr>";

echo "</table>";

echo "</center>";

echo "</div>";

echo "</div>";

/******************************************************************************************
 * Center
 ******************************************************************************************/
echo "<center class='fondoamarillo'>"; 
echo "<table>";
echo "<tr>";
echo "<td>"; 
echo "<INPUT type='button' value='Guardar' id='btnGuardarEventosCatastroficos' onClick='guardarEventosCatastrofios(); quitarRelacion();' style='width:100'>";
// echo "<INPUT type='button' value='reset' onClick='resetearEventosCatastroficos();' style='width:100'>";
echo "</td>";
echo "<td>"; 
echo "<INPUT type='button' value='Salir sin guardar' id='btnCerrarEventosCatastroficos' onClick='cerrarEventosCatastroficos();' style='width:150'>";
echo "</td>";
echo "<td>"; 
echo "<INPUT type='button' value='Lista de Eventos' id='btnListaEventosCatastroficos' onClick='listarEventosCatastroficos();' style='width:150'>";
echo "</td>";
echo "</tr>";
echo "</table>";
echo "</center>";
/******************************************************************************************/
echo "</form>";
echo "</div>";
?>