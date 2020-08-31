<?php
include_once("conex.php");

/**********************************************************************************************************************************************************
 * Actualizaciones
 **********************************************************************************************************************************************************
 * Febrero 5de 2018			Edwin MG	- Se agrega campo nuevo NO DISPENSABLE, que indica que los medicamentos de un protocolo no es dispensable en piso
 *										  cuando se le realiza la orden
**********************************************************************************************************************************************************
 * Septiembre 12 de 2016	Edwin MG	- Se corrige para que los articulos de reemplazo en protocolos de articulos CTC se carguen y eliminen correctamente
 **********************************************************************************************************************************************************
 * Abril 26 de 2016		Edwin MG	- Se corrige para que las ordenes de HCE se muestren aunque el campo este inactivo
 *									- Se corrige error que no permitía que los items de HCE no se borraran en la bd
 **********************************************************************************************************************************************************
 * Noviembre 27 de 2015	Edwin MG	Se muestra mensaje para procedimientos no homologados
 **********************************************************************************************************************************************************
 * Julio 13 de 2015		Edwin MG	Se organiza query para que tenga en cuenta el cco de costo correspondiente en la función consultarArticulo
 **********************************************************************************************************************************************************
 * Abril 08 de 2015		Edwin MG	Se hacen cambios varios para que se muestren alertas de la configuración
 **********************************************************************************************************************************************************
 * Junio 13 de 2014 (Jonatan Lopez): Se corrige la edicion de los protocolos, ya que se estaba perdiendo el codigo.
 **********************************************************************************************************************************************************
 * Mayo 26 de 2014 (Jonatan Lopez): Se repara el ingreso de datos de texto a la base de datos especialmente con los saltos de linea, para que sean reconocidos
 * 									de forma correcta.
 **********************************************************************************************************************************************************
 * Mayo 22 de 2014 (Jonatan Lopez): Se agrega el campo de existe opcion pos si y existe opcion pos no, cuando se quiere registrar un CtcProcedimientos.
 **********************************************************************************************************************************************************
 * Septiembre 24 de 2013	Edwin MG	Se corrige el tipo de protocolo al momento de actualizar los protocolos en el js.
 **********************************************************************************************************************************************************/

/**********************************************************************************************************************************************************
 * Crear plantillas para los programas de ordenes y HCE, (en ordenes para articulos, ctc y procedimientos) y HCE cualquier tabla y campo
 * que al abrir el programa se carguen automáticamente los campos, de acuerdo a los siguiente:
 *
 * - Diagnóstico
 * - Tratamiento
 * - Especialidad del médico
 * - Médico
 * - Por cco
 * - Para todos
 *
 * Nota: Este programa no contempla la carga de los datos automaticamente. Solo guarda los valores por defecto de la tabla y el valor a cargar.
 *		 - Solo el superusuario puede crear una plantilla por Dx, Especialidad, cco, o para todos.
 */

/**
 Tabla de configuracion

 Conprg			Nombre del programa
 Conspr			Subprograma
 Consol			Solucion
 Contab			Numero de la tabla de la solucion
 Concam			Campo de la tabla
 Contip			Tipo de dato (alfanumerico, numerico, real, fecha, hora, etc )
 Contre			Tablas de relacion. Es un query que muestra que datos mostrar.
 Concol			Cantidad de columnas en que se mostrará.


 Tabla de grabacion de datos

 Pladia			bool que indica si es de diganostico
 Platra			bool que indica si es de tratamiento
 Plaesp			bool que indica si es de especialidad
 Plamed			bool que indica si es de medico
 Placco			bool que indica si es de cco
 Platod			bool que indica si es de todos
 Placod			Alfanumerico. Codigo segun lo que se escriba. (codigo de usuario si es usuario, codigo de la especialida, codigo cie 10 si es diagnostico, codigo del cco si es cco, no defenido para todos )
 Plades			Descripcion de la plantilla. Nombre que le puede poner el usuario a la plantilla para diferenciarlo.
 Placon			Numerico. Número de la plantilla que se crea.
 Plaprg			Alfanumerico. Programa del cual se quiere cargar los datos por defecto.
 Plaspr			Alfanumerico. Subprograma.
 Plasol			Alfanumerico. solucion al que tiene valor por defecto ( ejemplo de soluciones: movhos, cenpro, hce, etc )
 Platab			Alfanumerico. tabla de la solucion	(ejem: 000001, 000002, 000003, etc )
 Placam			Alfanumerico. Indica el campo de la tabla
 Plaval			Texto. Valor por defecto. en texto por que puede guardar cualquier valor.
 Plaest			Bool. Estado del registro.


 Para guardar metodos masivos

 toda variable debe comenzar con dat_ seguido del nombre del campo a guardar. Ejem: dat_Detcod
 */

/**************************************************************************************************************
 *												FUNCIONES
 **************************************************************************************************************/

 if(!isset($_SESSION['user'])){
	echo "Sin sesion";
	return;
}

header('Content-type: text/html;charset=ISO-8859-1');

function eliminarEncabezadoProtocolo( $conex, $wbasedato, $id ){

	$val == false;

	$sql = "UPDATE {$wbasedato}_000137
	           SET Proest = 'off'
			 WHERE id = '$id'
			";

	$res = mysql_query( $sql,$conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	if( mysql_affected_rows() > 0 ){
		$val = true;
	}
	
	return $val;
}


 /******************************************************************************************
 * Consultar los posibles valores que puede tener un campo de una tabla (Para protocolos de HCE)
 ******************************************************************************************/
function consultarValorDeCampo( $conex, $whce, $wtabla, $wcampo ){

				 // AND Detest = 'on'
	$query = "SELECT Detpro, Detcon, Detorp, Dettip, Detdes, Detarc, Detcav, Detvde, Detnpa, Detvim, Detume, Detcol, Dethl7, Detjco, Detsiv, Detase, Detved, Detimp, Detimc, Detvco, Detvcr, Detobl, Detdep, Detcde, Deturl, Detfor, Detcco, Detcac, Detnse, Detfac, Enccol, Detcoa, Encdes, Detprs, Detalm, Detanm, Detlrb, Detdde, Encnco, Detcbu, Dettta, Detnbu, Detcua, Detccu, Detdpl, Detcro, Detvmi, Detvma, Dettii
				FROM ".$whce."_000001,".$whce."_000002
			   WHERE Encpro = '".$wtabla."'
				 AND Encpro = Detpro
				 AND Detcon = '".$wcampo."'
			ORDER BY Detorp";

	$err = mysql_query($query,$conex) or die(mysql_errno().":".mysql_error());
	$num = mysql_num_rows($err);
	if ($num>0){
		//'Memo', 'Numero', 'Texto', 'Grid', 'Booleano', 'Fecha', 'Tabla', 'Seleccion', 'Hora'
		$datos = array();
		while( $rowx = mysql_fetch_assoc($err) ){
			array_push($datos, $rowx );
		}
		$i=0;
		foreach($datos as $row){
			switch($row['Dettip']){
				case "Memo":
					if($row['Dettta'] == "M"){
						$valorM = "";
						$row['Detfor']=str_replace("HIS",$whis,$row['Detfor']);
						$row['Detfor']=str_replace("ING",$wing,$row['Detfor']);
						$query = $row['Detfor'];
						$conex_o = odbc_connect($row['Detarc'],'','');
						$err_o = odbc_do($conex_o,$query);
						if (odbc_fetch_row($err_o)){
							if($valorM == $row['Detvde'] or $row['Dettii'] == "BLOCK")
								$valorM = odbc_result($err_o, 1);
						}else{
							if($valorM == $row['Detvde'])
								$valorM="";
						}
						echo "<textarea name='dat_Detval'>".htmlentities($valorM)."</textarea>";						
							
					}elseif($row['Dettta'] == "I"){
						$valorI = "";
						$row['Detfor']=str_replace("HIS",$whis,$row['Detfor']);
						$row['Detfor']=str_replace("ING",$wing,$row['Detfor']);
						if(strpos($row['Detfor'],"REGISTRO") !== false)
						{
							/*$posx = (integer)substr($row['Detfor'],0,strpos($row['Detfor'],"s"))
							$row['Detfor']=str_replace("REGISTRO",$registro[$posx][0],$row['Detfor']);
							$row['Detfor']=substr($row['Detfor'],strpos($row['Detfor'],"s"));*/
						}
						$query = $row['Detfor'];
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						if ($num1 > 0){
							$row1 = mysql_fetch_array($err1);
							if($valorI == $row['Detvde'] or $row['Dettii'] == "BLOCK"){
								$temporal=$row1[0];
								if(strpos($temporal,"</") > 0){
									$row1[0]=option($temporal);
								}
								$valorI=$row1[0];
							}
						}
						else{
							if($valorI == $row['Detvde'])
								$valorI="";
						}
						echo "<textarea name='dat_Detval'>".htmlentities($valorI)."</textarea>";		
							
					}elseif($row['Dettta'] == "R"){
						@eval($row['Detfor']);						
						echo "<textarea name='dat_Detval'></textarea>";
					}else{
						echo "<textarea name='dat_Detval'></textarea>";						
					}
				break;
				case "Numero":
					echo "<input type='text' id='c".$wtabla."_".$wcampo."' name='dat_Detval' maxlength=30 value=''>";
					echo "<script>";
					//Para que reemplace todo lo que no sea numero por un vacio
					echo "	$('#c{$wtabla}_{$wcampo}').keyup(function(){
								if ($(this).val() != '')
									$(this).val($(this).val().replace(/[^0-9]/g, ''));
							});";
					//Para validar valor minimo y maximo
					if( is_numeric($row['Detvmi']) || is_numeric($row['Detvma']) ){
						echo "	$('#c{$wtabla}_{$wcampo}').blur(function(){
									if(parseInt($(this).val()) < parseInt(".$row['Detvmi'].") || parseInt($(this).val()) > parseInt(".$row['Detvma'].")){
										$(this).val('');
										alert('ESTE CAMPO TIENE UN MINIMO DE '+".$row['Detvmi']."+' Y UN MAXIMO DE '+".$row['Detvma'].");
									}
								});";
					}
					echo "</script>";
				break;
				case "Texto":
					echo "<input type='text' name='dat_Detval' maxlength=80 value=''>";
				break;
				case "Grid":
				
					echo "<textarea name='dat_Detval'>".htmlentities($valorM)."</textarea>";
						// $valorG = "";
						// $Gridseg=explode("*",$row['Detfor']);
						// $Gridtit=explode("|",$Gridseg[0]);
						// $Gridtip=explode("|",$Gridseg[1]);
						// $Gridobl=explode("|",$Gridseg[2]);
						// if(count($Gridseg) == 4){
							// if($row['Dettii'] == "Q"){
								// $Gridseg[3]=str_replace("HIS",$whis,$Gridseg[3]);
								// $Gridseg[3]=str_replace("ING",$wing,$Gridseg[3]);
								// $query = $Gridseg[3];
								// $err1 = mysql_query($query,$conex);
								// $num1 = mysql_num_rows($err1);
								// if ($num1 > 0){
									// $row1 = mysql_fetch_array($err1);
									// if($valorG == $row['Detvde']){
										// $valorG=$row1[0];
									// }
								// }
							// }
						// }
						// echo "<textarea name='GRID[".$i."]'>".$row['Detfor']."</textarea>";
						// echo "<textarea name='registro[".$i."][0]'>".htmlentities($valorG)."</textarea><br>";
						// echo "<table align=center border=1>";
						// echo "<tr>";
						// echo "<td>ITEM</td>";
						// for ($g=0;$g<count($Gridtit);$g++){
							// echo "<td>".htmlentities($Gridtit[$g])."</td>";
						// }
						// echo "<td colspan=4>OPERACION</td></tr>";
						// echo "<tr>";
						// echo "<td><input type='TEXT' name='WGRIDITEM' size=3 maxlength=6 id='WGRIDITEM' readonly=readonly value='0' class=tipo3></td>";
						// $GRID=array();
						// for ($g=0;$g<count($Gridtip);$g++){
							// if($Gridobl[$g] == "R"){
								// $OBLGRID = "O";
								// $OBLSGRID = "1";
							// }
							// else{
								// $OBLGRID = "";
								// $OBLSGRID = "0";
							// }
							// switch(substr($Gridtip[$g],0,1)){
								// case "F":
									// if(!isset($GRID[$g]))
										// $GRID[$g]="";
									// echo "<td><input type='text' id='FGRID".$i.$g."'  size='10' maxlength='10' NAME='GRID[".$g."]' value='".$GRID[$g]."'></td>";
								// break;
								// case "H":
									// if(!isset($GRID[$g]))
										// $GRID[$g]="";
									// echo "<td><input type='TEXT' name='GRID[".$g."]' size=5 maxlength=5 value='".$GRID[$g]."'>&nbsp;";
									// echo "<select name='Horas[".$g."]' id=HOG".$i.$g." onChange='limpiarHoraG(".$g.",".$i.")'>";
									// for ($j=0;$j<24;$j++){
										// if($j < 10)
											// $jh = "0".$j;
										// else
											// $jh = $j;
										// echo "<option value='".$jh."'>".$jh."</option>";
									// }
									// echo "</select>&nbsp;";
									
									// echo "<select name='Minutos[".$g."]' id=MIG".$i.$g." onChange='mostrarHoraG(".$g.",".$i.")'>";
									// echo "<option value=''></option>";
									// for ($j=0;$j<4;$j++){
										// $jh=(15 * $j);
										// if($jh < 15)
											// $jh = "0".$jh;
										// echo "<option value='".$jh."'>".$jh."</option>";
									// }
									// echo "</select>";

									// echo "</td>";
								// break;
								// case "S":
									// $TGRID=substr($Gridtip[$g],1,6);
									// $CGRID=substr($Gridtip[$g],7);
									// echo "<td>";
									// $query = "SELECT Selcda, Selnda from ".$whce."_".$TGRID." where Seltab='".$CGRID."' and Selest='on' order by Selnda";
									// $err1 = mysql_query($query,$conex);
									// $num1 = mysql_num_rows($err1);
									// echo "<select name='GRID[".$g."]' id='SGRID".$i.$g."'>";
									// echo "<option value=''>Seleccione</option>";
									// if ($num1>0){
										// for ($j=0;$j<$num1;$j++){
											// $row1 = mysql_fetch_array($err1);
											// echo "<option value='".$row1[0]."'>".htmlentities($row1[1])."</option>";
										// }
									// }
									// echo "</select>";
									// echo "</td>";
								// break;
								// case "T":
									// if(!isset($GRID[$g]))
										// $GRID[$g]="";
									// $ancho=substr($Gridtip[$g],1);
									// $idgrid="WGRID".$i.$g;
									// echo "<td><input type='TEXT' name='GRID[".$g."]' size=".$ancho." maxlength=255 id='WGRID".$i.$g."' value='".$GRID[$g]."' class=tipo3".$OBLGRID."></td>";
								// break;
								// case "N":
									// if(!isset($GRID[$g]))
										// $GRID[$g]="";
									// $Gridnum=explode("(",$Gridtip[$g]);
									// $ancho=substr($Gridnum[0],1);
									// $unimed=substr($Gridnum[1],0,strlen($Gridnum[1])-1);
									// $minmax=substr($Gridnum[2],0,strlen($Gridnum[2])-1);
									// $Gridminmax=explode(",",$minmax);
									// echo "<td><input type='TEXT' name='GRID[".$g."]' maxlength=30 id='TGRID".$i.$g."' value='".$GRID[$g]."'>".$unimed."</td>";
									// echo "<script>";
									// //Para que reemplace todo lo que no sea numero por un vacio
									// echo "	$('#TGRID".$i.$g."').keyup(function(){
												// if ($(this).val() != '')
													// $(this).val($(this).val().replace(/[^0-9]/g, ''));
											// });";
									// //Para validar valor minimo y maximo
									// if( is_numeric($Gridminmax[0]) || is_numeric($Gridminmax[1]) ){
										// echo "	$('#TGRID".$i.$g."').blur(function(){
													// if(parseInt($(this).val()) < parseInt(".$Gridminmax[0].") || parseInt($(this).val()) > parseInt(".$Gridminmax[1].")){
														// $(this).val('');
														// alert('ESTE CAMPO TIENE UN MINIMO DE '+".$Gridminmax[0]."+' Y UN MAXIMO DE '+".$Gridminmax[1].");
													// }
												// });";
									// }
									// echo "</script>";
								// break;
							// }
						// }
						// $Gdataseg=explode("*",$valorG);
						// for ($g=1;$g<=$Gdataseg[0];$g++){
							// if($g % 2 == 0)
								// $gridcolor="tipoL02GRID1";
							// else
								// $gridcolor="tipoL02GRID2";
							// $Gdatadata=explode("|",$Gdataseg[$g]);
							// echo "<tr>";
							// echo "<td class=".$gridcolor.">".$g."</td>";
							// for ($g1=0;$g1<count($Gdatadata);$g1++){
								// echo "<td class=".$gridcolor.">".$Gdatadata[$g1]."</td>";
							// }
							// echo "<td class=".$gridcolor." colspan=4><input type='RADIO' name='RGRID".$g."' id='WRGRID".$g."' value=".$g."></td>";
							// echo "</tr>";
						// }
						// echo "</table><br>";		
				break;
				case "Booleano":
					echo "<input type='checkbox' name='dat_Detval'>";
				break;
				case "Fecha":
					echo "<input name='dat_Detval' id='c".$wtabla."_".$wcampo."' size=10 maxlength='10' value='' type='text' />";
					echo "	<script>
							$('#c{$wtabla}_{$wcampo}').datepicker({
								showOn: 'button',
								buttonImage: '../../images/medical/root/calendar.gif',
								changeMonth: true,
								changeYear: true
							});
							</script>";
				break;
				case "Tabla": //FALTA
					$valorG = "";
					echo "<input type='hidden' id='cual".$i."' value='".$row['Detcua']."'>";
					echo "<input type='hidden' id='Tcual".$i."' value='".htmlentities($row['Detccu'])."'>";
					echo "<input type='hidden' id='SimMul".$i."' value='".$row['Dettta']."'>";
					if($row['Dettii'] == "Q"){
						$Q=str_replace("HIS",$whis,$row['Detfor']);
						$Q=str_replace("ING",$wing,$row['Detfor']);
						$query = $Q;
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						if ($num1 > 0){
							$row1 = mysql_fetch_array($err1);
							if($valorG == $row['Detvde']){
								$valorG=$row1[0];
							}
						}
					}
					$wcampos=explode(",",$row['Detcbu']);
					$wncampos=explode(",",$row['Detnbu']);

					$position="M".$i;
					if($registro[$i][45] != "on"){
						echo "<select name='Tselect[".$i."]' id='TS".$i."' class=tipo3>";
							if(isset($Tselect[$i])){
								if(strcmp($Tselect[$i],$wncampos[0]) == 0){
									echo "<option value='".$wncampos[0]."' selected>".$wncampos[0]."</option>";
									echo "<option value='".$wncampos[1]."'>".$wncampos[1]."</option>";
									$sp=0;
								}
								else{
									echo "<option value='".$wncampos[0]."'>".$wncampos[0]."</option>";
									echo "<option value='".$wncampos[1]."' selected>".$wncampos[1]."</option>";
									$sp=1;
								}
							}
							else{
								echo "<option value='".$wncampos[0]."'>".$wncampos[0]."</option>";
								echo "<option value='".$wncampos[1]."'>".$wncampos[1]."</option>";
								$sp=0;
							}
						echo "</select><br>";
					}
					if($registro[$i][45] != "on"){
						if($row['Dettii'] == "INV"){
							$tabtem=array();
							$tt=count($wcampos)-1;
							for ($z=0;$z<count($wcampos);$z++)
								$tabtem[$z] = $wcampos[$tt - $z];
							$row['Detcbu']=implode(",", $tabtem);
						}
						$query="1SELECT ".$row['Detcbu']." FROM ".$row['Detarc']." WHERE ".$wcampos[$sp]." LIKE var ORDER BY ".$wcampos[$sp].";";
					}
					else{
						$sp=0;
						$query="2SELECT ".$row['Detcbu']." FROM ".$row['Detarc']." WHERE ".$wcampos[$sp]." LIKE var ORDER BY ".$wcampos[$sp].";";
					}
					echo "<input type='hidden' id='query".$i."' value='".$query."'>";
					
					echo "<input type='text' id='M".$i."' onFocus='javascript:limpiarCampo(this);' size=30/>";
					echo "<br>";
					if($row['Detcua'] == "on"){
						$cualif=explode(",",$row['Detccu']);
						if(count($cualif) > 2){
							for ($k=2;$k<count($cualif);$k++){
								$varRT="RT".$i;
								if($k == 2){
									echo "<input type='RADIO' name='RT".$i."' id='RT".$i."' value=".htmlentities(substr($cualif[$k],0,1))." checked>".htmlentities($cualif[$k]);
								}
								else{
									echo "<input type='RADIO' name='RT".$i."' id='RT".$i."' value=".htmlentities(substr($cualif[$k],0,1)).">".htmlentities($cualif[$k]);
								}
							}
							echo "<br>";
						}
					}
					echo "<select name='registro[".$i."][0]' id='selAuto".$i."' multiple='multiple' size=".$row['Detalm'].">";
					echo $valorG;
					echo "</select>";
					echo "<input type='hidden' id='registro[".$i."][36]' value='".$registro[$i][36]."'>";

				break;
				case "Seleccion":
					if($row['Dettta'] != "M" and $row['Dettta'] != "M1"){
						if($row['Detprs'] == "off"){
							if($row['Dettii'] == "SMART"){
								$row['Detfori']=strtoupper($row['Detfori']);
								$row['Detfori']=formula1($row['Detfori']);
								for ($w=0;$w<$num;$w++){
									$row['Detfori']=str_replace($orden[$w][0],$orden[$w][1],$row['Detfori']);
								}
								$row['Detfori']=formula($row['Detfori']);
								$row['Detfori']=str_replace("HIS",$whis,$row['Detfori']);
								$row['Detfori']=str_replace("ING",$wing,$row['Detfori']);
								$row['Detfori'] = strtolower($row['Detfori']);
								eval($row['Detfori']);
								$err1 = mysql_query($query,$conex);
								$num1 = mysql_num_rows($err1);
								echo "<select name='dat_Detval'>";
								echo "<option value=''>Seleccione</option>";
								if ($num1>0){
									for ($j=0;$j<$num1;$j++){
										$row1 = mysql_fetch_array($err1);
										echo "<option value='".$row1[0]."'>".htmlentities($row1[0])."</option>";
									}
								}
								echo "</select>";
							}
							else{
								$query = "SELECT Selcda, Selnda from ".$whce."_".$row['Detarc']." where Seltab='".$row['Detcav']."' and Selest='on' order by Selcda";
								$err1 = mysql_query($query,$conex);
								$num1 = mysql_num_rows($err1);
								echo "<select name='dat_Detval'>";
								echo "<option value=''>Seleccione</option>";
								if ($num1>0){
									for ($j=0;$j<$num1;$j++){
										$row1 = mysql_fetch_array($err1);
										echo "<option value='".$row1[0]."'>".htmlentities($row1[1])."</option>";									
									}
								}
								echo "</select>";
							}							
						}
						else{
							$query = "SELECT Selcda, Selnda from ".$whce."_".$row['Detarc']." where Seltab='".$row['Detcav']."' and Selest='on' order by Selcda";
							$err1 = mysql_query($query,$conex);
							$num1 = mysql_num_rows($err1);
							if ($num1>0){
								$filasR=$row['Detlrb'];
								$rb=0;
								echo "<table border=0  CELLSPACING=0 align='center'>";
								echo "<tr>";
								for ($j=0;$j<$num1;$j++){
									if($rb >= $filasR){
										echo "</tr><tr>";
										$rb=0;
									}
									$row1 = mysql_fetch_array($err1);									
									if($rb <= $filasR)
										echo "<td><input type='RADIO' class='dat_Detval' name='dat_DetvalR".$wtabla."_".$wcampo."' value='".$row1[0]."'>".htmlentities($row1[1])."</td>";
									else
										echo "<tr><td><input type='RADIO' class='dat_Detval' name='dat_DetvalR".$wtabla."_".$wcampo."' value='".$row1[0]."'>".htmlentities($row1[1])."</td>";
									$rb++;									
								}
								echo "</table>";
							}		
						}
					}
					else{
						// NUEVO TIPO DE CAMPO PARA DIAGNOSTICO
						echo "<input type='text' name='dat_Detval' size=50/>";
						echo "<br>";
						$cualif=explode(",",$row['Detccu']);
						for($w=0;$w<count($cualif);$w++)
								echo "<input type='RADIO' name='AT".$wtabla."_".$wcampo."' value=".htmlentities(substr($cualif[$w],0,1)).">".htmlentities($cualif[$w]);
						echo "<br>";
						echo "<select multiple='multiple' style='width:300px'>";
						echo "";
						echo "</select>";
					}
				break;
				case "Hora":
						echo "<div align='center'><input type='TEXT' id='c".$wtabla."_".$wcampo."' name='dat_Detval' size=8 maxlength=8 id='c".$wtabla."_".$wcampo."' value='00:00:00' disabled></div>";
						echo "Hora: <div id='H".$wtabla."_".$wcampo."'></div>";
						echo "Minuto: <div id='M".$wtabla."_".$wcampo."'></div>";						
						echo "<script>
						try{
						$( '#H{$wtabla}_{$wcampo}' ).slider( 'destroy' );
						$( '#M{$wtabla}_{$wcampo}' ).slider( 'destroy' );						
						}catch(e){}
						$( '#H{$wtabla}_{$wcampo}' ).slider({
							range: 'max',
							min: 0,
							max: 23,
							value: 0,
							slide: function( event, ui ) {
								var valu = ui.value;
								var res = $('#c{$wtabla}_{$wcampo}').val();
								var hora = res.split(':');								
								if( ('0'+valu).length == 2 )
									valu= '0'+valu;
								hora[0] = valu;
								$('#c{$wtabla}_{$wcampo}').val(hora[0]+':'+hora[1]+':'+hora[2]);
							}
						});
						
						$( '#M{$wtabla}_{$wcampo}' ).slider({
							range: 'max',
							min: 0,
							max: 59,
							value: 0,
							slide: function( event, ui ) {
								var valu = ui.value;
								var res = $('#c{$wtabla}_{$wcampo}').val();
								var hora = res.split(':');
								if( ('0'+valu).length == 2 )
									valu= '0'+valu;
								hora[1] = valu;
								$('#c{$wtabla}_{$wcampo}').val(hora[0]+':'+hora[1]+':'+hora[2]);
							}
						});
						</script>
						";				
				break;				
			}
			$i++;
		}
	}
}

/******************************************************************************************
 * Consultar por nombre o codigo medicamentos POS
 ******************************************************************************************/
function consultarArticuloPOS( $conex, $wbasedato, $wcenmez, $art ){

	$val = 'No se encontraron datos';

	$sql = "SELECT
				Artcod, Artcom, Artgen, Artuni, Defvia, Artpos, Deffru
			FROM
				{$wbasedato}_000026 a, {$wbasedato}_000059 b, {$wbasedato}_000115 c, {$wbasedato}_000114 d
			WHERE
				defart = artcod
				AND ( artcod LIKE '%$art%'
					  OR artcom LIKE '%$art%'
					  OR artgen LIKE '%$art%'
					)
				AND artest = 'on'
				AND defest = 'on'
				AND artpos != 'N'
				AND defart = relart
				AND relest = 'on'
				AND famcod = relfam
				AND famest = 'on'
			UNION
			SELECT
				Artcod, Artcom, Artgen, Artuni, Defvia, 'N' as Artpos, Deffru
			FROM
				{$wcenmez}_000002 a, {$wcenmez}_000001 b, {$wbasedato}_000059 c, {$wbasedato}_000115 d, {$wbasedato}_000114 e
			WHERE
				defart = artcod
				AND ( artcod LIKE '%$art%'
					  OR artcom LIKE '%$art%'
					  OR artgen LIKE '%$art%'
					)
				AND artest = 'on'
				AND defest = 'on'
				AND arttip = tipcod
				AND tipcdo != 'on'
				AND defart = relart
				AND relest = 'on'
				AND famcod = relfam
				AND famest = 'on'
			";
			
	$sql = "SELECT
				Artcod, Artcom, Artgen, Artuni, Defvia, Artpos, Deffru
			FROM
				{$wbasedato}_000026 a, {$wbasedato}_000059 b
			WHERE
				defart = artcod
				AND ( artcod LIKE '%$art%'
					  OR artcom LIKE '%$art%'
					  OR artgen LIKE '%$art%'
					)
				AND artest = 'on'
				AND defest = 'on'
				AND artpos != 'N'
			UNION
			SELECT
				Artcod, Artcom, Artgen, Artuni, Defvia, 'N' as Artpos, Deffru
			FROM
				{$wcenmez}_000002 a, {$wcenmez}_000001 b, {$wbasedato}_000059 c
			WHERE
				defart = artcod
				AND ( artcod LIKE '%$art%'
					  OR artcom LIKE '%$art%'
					  OR artgen LIKE '%$art%'
					)
				AND artest = 'on'
				AND defest = 'on'
				AND arttip = tipcod
				AND tipcdo != 'on'
			";
	
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
	
		$val = '';
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		
			if( strtoupper( $rows[ 'Artpos' ] ) != 'N' || !esProductoNoPOSCM( $conex, $wbasedato, $wcenmez, $rows[ 'Artcod' ] ) ){
			
				//Creo el resultado como un json
				//Primero creo un array con los valores necesarios
				$datArticulos[ 'value' ] = Array( "cod"=> $rows[ 'Artcod' ],
												  "com"=> trim( $rows[ 'Artcom' ] ),
												  "gen"=> trim( $rows[ 'Artgen' ] ),
												  "vias"=> $rows[ 'Defvia' ],
												  "uni"=> $rows[ 'Deffru' ] );	//Este es el dato a procesar en javascript
				
				$datArticulos[ 'label' ] = "{$rows[ 'Artcod' ]}-".trim( $rows[ 'Artcom' ] )."-".trim( $rows[ 'Artgen' ] );	//Este es el que ve el usuario
				$dat = Array();
				$dat[] = $datArticulos;
				
				$val .= json_encode( $dat )."\n";
			}
		}
		
		//$val = substr( $val, 1 );
	}
	
	return $val;
}

/******************************************************************************************
 * Consultar por nombre o codigo del medicamento
 ******************************************************************************************/
function consultarArticuloReemplazo( $conex, $wbasedato, $wcenmez, $pro ){

	$val = 'No se encontraron datos';

	$sql = "SELECT
				Relpro, Relart, Relpos, Relpre, Relddi, Relcan, Reltto, Relest, a.id as id, Artcod, Artcom, Artgen
			FROM
				{$wbasedato}_000152 a LEFT JOIN {$wbasedato}_000026 b ON artcod = relart
			WHERE
				Relpro = '$pro'
			";
	
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
	
		$val = '';
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		
			//Si el estado es off dejo todos los campos en vacío exceptuando el id
			//Esto por que se supone que no tiene nada el registro
			if( $rows[ 'Relest' ] == 'off' ){
				
				foreach( $rows as $keyRows => $valueRows  ){
					if( $keyRows != 'id' ){
						$rows[ $keyRows ] = '';
					}
				}
			}
			
			//Creo el resultado como un json
			//Primero creo un array con los valores necesarios
			$datArticulos[ 'value' ] = Array(  "cod" => $rows[ 'Artcod' ],
											   "com" => trim( $rows[ 'Artcom' ] ),
											   "gen" => trim( $rows[ 'Artgen' ] ),
											   "uni" => $rows[ 'Relpre' ] ,
											   "pos" => trim( $rows[ 'Relpos' ] ),
											   "pre" => trim( $rows[ 'Relpre' ] ),
											   "ddi" => trim( $rows[ 'Relddi' ] ),
											   "can" => trim( $rows[ 'Relcan' ] ),
											   "dto" => trim( $rows[ 'Reltto' ] ),
											   "id"  => trim( $rows[ 'id' ] ) );	//Este es el dato a procesar en javascript
			
			$datArticulos[ 'label' ] = "{$rows[ 'Artcod' ]}-".trim( $rows[ 'Artcom' ] )."-".trim( $rows[ 'Artgen' ] );	//Este es el que ve el usuario
			$dat = Array();
			$dat[] = $datArticulos;
			
			$val .= json_encode( $dat )."\n";
		}
	}
	
	return $val;
}

/****************************************************************************************
 * Crear el detalle para los Medicamentos No POS DEL PROTOCOLO
 ****************************************************************************************/
function crearDetalleMedicamentosReemplazo( $conex, $wbasedato, &$wencabezados, $wdetalle ){

	$val = false;
	$arr_insertados = array();
	
	foreach($wencabezados as &$encabezado){
		foreach($wdetalle as $detalle){
			if( !isset($detalle['Relest']) ) continue; //Si no esta definido Relest, no es detalle de medicamentosreemplazo
			
			//El mismo detalle aplica para los diferentes encabezados
			
			$detalle['Relpro'] = $encabezado['Procod'];	
			if( isset($encabezado['nuevo']) && $encabezado['nuevo'] == 'on' ) //Si el encabezado es nuevo, se limpia el id para que lo inserte como nuevo para este encabezado
				$detalle['id'] = "";
				
			//$datos['Relpro'] = buscarRegistroEncCtcPro( $conex, $wbasedato );			
			if( ( $detalle['Relpro'] && $detalle['Dprpro'] != -1 )  || !empty( $detalle['id'] ) ){				
				//Si no tiene codigo del item (codigo del articulo o del procedimiento) no permito guardar
				if( !empty( $detalle['Relart'] ) || !empty( $detalle['id'] ) ){					
					if( empty( $detalle['id'] ) ){								
						//Creo el string de insert para el encabezado
						$sql = crearStringInsert( "{$wbasedato}_000152", $detalle );
					}
					else{
						//Quito el codigo del protocolo, este nunca se debe mover
						unset( $detalle['Relpro'] );
						//Si viene en estado on creo el string
						if( $detalle['Relest'] == 'on' && !empty( $detalle['Relart'] ) ){
							//Actualizo el registro
							$sql = crearStringUpdate( "{$wbasedato}_000152", $detalle );
						}
						else{
							//Si el estado es off solo desactivo el registro
							$sql = "UPDATE {$wbasedato}_000152
									   SET Relest = 'off'
									 WHERE id = '{$detalle['id']}'
									";
						}
					}
					//echo "<br>DET--->".$sql;
					$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
					
					if( mysql_affected_rows() > 0 ){						
						if( empty( $detalle['id'] ) ){
							//echo $val =  mysql_insert_id();
							$detalle['id'] =  mysql_insert_id();
							array_push( $arr_insertados, $detalle['id'] );
						}
					}
				}
			}
		}
	}
	
	return $arr_insertados;
}

/******************************************************************************************
 * Consulta el diagnostico respecto al CIE-0
 * Marzo 19 de 2013
 ******************************************************************************************/
function consultarDiagnosticosCIO( $conex, $basedatos, $dx, $porCodigo = true ){

	$val = '';

	if( $porCodigo ){
	
		//Diagnostico
		$sql = "(SELECT
					Ciocod as Codigo, Ciodes as Descripcion
				FROM
					root_000096 a
				WHERE
					Ciocod = '$dx'
				)
				UNION
				(SELECT
					Codigo, Descripcion
				FROM
					( SELECT '*' as Codigo, ' Todos' as Descripcion ) AS a
				WHERE
					Codigo = '$dx'
				)
				ORDER BY
					Descripcion
				";
	}
	else{
	
		//Diagnostico
		$sql = "(SELECT
					Ciocod as Codigo, Ciodes as Descripcion
				FROM
					root_000096 a
				WHERE
					Ciocod LIKE '%$dx%'
					OR Ciodes LIKE '%$dx%' )
				UNION
				(SELECT
					Codigo, Descripcion
				FROM
					( SELECT '*' as Codigo, ' Todos' as Descripcion ) AS a
				)
				ORDER BY
					Descripcion
				";
	}
			
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
			
	if( $num > 0 ){
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		
			if( !$soloNoPos || ( $soloNoPos && strtolower( $rows[ 'NoPos' ] ) == 'on' ) ){
			
				$rows[ 'Descripcion' ] = trim( $rows[ 'Descripcion' ] );
			
				//Creo el resultado como un json
				//Primero creo un array con los valores necesarios
				$datArticulos[ 'value' ] = Array( "cod"=> $rows[ 'Codigo' ], "des"=> $rows[ 'Descripcion' ] );	//Este es el dato a procesar en javascript
				$datArticulos[ 'label' ] = "{$rows[ 'Codigo' ]}-{$rows[ 'Descripcion' ]}";	//Este es el que ve el usuario
				$dat = Array();
				$dat[] = $datArticulos;
				
				$val .= json_encode( $dat )."\n";
			}
		}
	}
	
	return $val;
}

/****************************************************************************************
 * Consulta la especialidad por codigo
 ****************************************************************************************/
function consultarEspecialidad( $conex, $wbasedato, $esp ){

	$val = "";

	//Consulto los diferentes cco
	$sql = "SELECT
				Espcod, Espnom
			FROM
				{$wbasedato}_000044 a
			WHERE
				Espcod = '$esp'
			";
	
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );	
	
	if( $rows = mysql_fetch_array( $res ) ){
		$val = $rows[ 'Espcod' ]." - ".$rows[ 'Espnom' ];
	}
	
	return $val;
}

/****************************************************************************************
 * Dibuja el buscador de protocolos avanzado
 ****************************************************************************************/
function buscadorProtocolosAvanzado(){

	global $conex;
	global $wbasedato;
	global $hce;

	//Boton buscar
	// echo "<INPUT type='button' onClick='moBuscador()' value='Mostrar buscador' id='btBuscador'>";
	// echo "<a href='#' onClick='moBuscador()' id='btBuscador'>[+]Mostrar buscador</a>";
	
	echo "<div id='bcProtocolosAvanzado' style='display:none'>";
	echo "<br>";
	echo "<table align='center'>";
	
	echo "<tr align='center'>";
	echo "<th class='fila1' colspan='9' style='font-size:14pt'>";
	echo "BUSCADOR DE MEDICAMENTOS";
	echo "</th>";
	echo "</tr>";
	
	echo "<tr class='encabezadotabla' align=center>";
	
	echo "<th>Tipo de protocolo</th>";
	echo "<th>Nombre de protocolo</th>";
	echo "<th>Especialista de<br>la salud</th>";
	echo "<th>Especialidad</th>";
	echo "<th>Diagn&oacute;stico</th>";
	echo "<th>Tratamiento</th>";
	echo "<th style='display:none'>Procedimiento o<br>examen</th>";
	echo "<th>Cco</th>";
	echo "<th>CIE-O</th>";
	echo "<th>Pedi&aacute;trico</th>";
	echo "<th>Reca&iacute;da</th>";
	echo "</tr>";
	
	echo "<tr class='fila1'>";
	
	echo "<td>";
	echo "<SELECT name='slTipPro' onChange='alCambiarSearch( this );'>";
	echo "<option></option>";
	echo "<option value='%'>Cualquiera</option>";
	echo "<option value='prOrdenes'>Ordenes</option>";
	echo "<option value='prHCE'>HCE</option>";
	echo "<option value='ctcArts'>Ctc Articulos</option>";
	echo "<option value='ctcProcs'>Ctc Procedimiento</option>";
	echo "</SELECT>";
	echo "</td>";
	
	echo "<td>";
	echo "<input name='dat_Encnom' type='text' style='width:100%' onKeyup='alCambiarSearch( this );'/>";
	echo "</td>";
	
	//Medico
	echo "<td>";
	echo "<input name='dat_Encmed' type='text' style='width:100%' onKeyup='alCambiarSearch( this );'/><div></div>";
	echo "</td>";
	
	//Consulto las diferentes especialidades
	echo "<td>";
	//echo "<input name='dat_Encesp' type='text' /><div></div>";
	
	//Consulto los diferentes cco
	$sql = "SELECT
				Espcod, Espnom
			FROM
				{$wbasedato}_000044 a
			ORDER BY
				Espnom
			";
			
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	echo "<SELECT name='dat_Encesp' type='text' onChange='alCambiarSearch( this );'/>";
	echo "<option></option>";
	echo "<option value='%'>Cualquiera</option>";
	echo "<option value='*'>Todos</option>";
	
	for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		echo "<option value='{$rows[ 'Espcod' ]}'>{$rows[ 'Espcod' ]}-{$rows[ 'Espnom' ]}</option>";
	}
	
	echo "</SELECT>";
	
	echo "</td>";
	
	//Diagnósticos
	echo "<td>";
	echo "<input name='dat_Encdia' type='text' style='width:100' onKeyup='alCambiarSearch( this );'/><div></div>";
	echo "</td>";
	
	//Tratamiento
	echo "<td>";
	echo "<input name='dat_Enctra' type='text' style='width:200' onKeyup='alCambiarSearch( this );'/><div></div>";
	echo "</td>";
	
	//Mostrando los cco
	echo "<td>";

	//Consulto los diferentes cco
	$sql = "SELECT Ccocod, Cconom
			  FROM {$wbasedato}_000011 c
			 WHERE Ccoest = 'on'
			   AND ccohos = 'on'
			    OR Ccourg = 'on'
			 UNION
			SELECT Ccocod, Cconom
			  FROM {$wbasedato}_000011 c
			 WHERE Ccoest = 'on'
			   AND ccoayu = 'on'
			";			
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
		
	echo "<SELECT name='dat_Enccco' onChange='alCambiarSearch( this );'/>";
	echo "<option></option>";
	echo "<option value='%'>Cualquiera</option>";
	echo "<option value='*'>Todos</option>";
	
	for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		echo "<option value='{$rows[ 'Ccocod' ]}'>{$rows[ 'Ccocod' ]}-{$rows[ 'Cconom' ]}</option>";
	}
	echo "</SELECT>";
	echo "</td>";
	
	//Busqueda por CIE-O
	echo "<td>";
	echo "<input name='dat_Enccio' type='text' style='width:100' onKeyup='alCambiarSearch( this );'/><div></div>";
	echo "</td>";
	
	//Busqueda por Pediátrico
	echo "<td align='center'>";
	// echo "<INPUT type='checkbox' name='dat_Encped' onClick='alCambiarSearch( this );'>";
	echo "<SELECT name='dat_Encped' onChange='alCambiarSearch( this );'/>";
	echo "<option></option>";
	echo "<option value='%'>Cualquiera</option>";
	echo "<option value='on'>S&iacute;</option>";
	echo "<option value='off'>No</option>";
	echo "</select>";
	echo "</td>";
	
	//Busqueda por Recaída
	echo "<td align='center'>";
	// echo "<INPUT type='checkbox' name='dat_Encrec' onClick='alCambiarSearch( this );'>";
	echo "<SELECT name='dat_Encrec' onChange='alCambiarSearch( this );'/>";
	echo "<option></option>";
	echo "<option value='%'>Cualquiera</option>";
	echo "<option value='on'>S&iacute;</option>";
	echo "<option value='off'>No</option>";
	echo "</select>";
	echo "</td>";
	
	echo "</tr>";
	
	echo "</table>";
	
	echo "<div id='resultSearch'>";
	echo "</div>";
	
	echo "</div>";
}

/********************************************************************************************************
 * Consulta un encabezado de protocolo
 *
 * $nom			Nombre del protocolo
 * $med			Medico
 * $esp			Especialidad
 * $dia			Codigo o nombre de Diagnóstico
 * $tra			Codigo tratamiento
 * $cco			Centro de costos
 ********************************************************************************************************/
function consultarBuscadorEncProtocolo( $conex, $wbasedato, $hce, $nom, $med, $esp, $dia, $tra, $cco, $slTipPro, $cio, $ped, $rec ){

	$val = '';
	$dat = Array();
	$dat1 = Array();
	
	if( empty( $ped ) ){
		$ped = "%";
	}
	
	if( empty( $rec ) ){
		$rec = "%";
	}
	
	//Consulta el medico
	$medOri = $med;
	$inMed = '';
	if( !empty( $med ) && $med != '%' ){
		
		$med = trim( consultarMedico( $conex, $wbasedato, $med, false ) );
		
		//Creo string IN para la consulta
		if( !empty( $med ) ){
			
			$med = explode( "\n", trim( $med ) );
			
			if( count( $med ) > 0 ){
				$inMed .= " IN ( ";
				$i = true;
				foreach( $med as $keyMed => $valueMed  ){
					
					$medJson = json_decode( $valueMed );
					
					if( $medJson[0]->value->cod != '*' || !( strpos( strtolower( $medJson[0]->value->des ), strtolower( $medOri  ) ) === FALSE ) ){
						if( $i )
							$inMed .= "'".$medJson[0]->value->cod."' ";
						else
							$inMed .= ",'".$medJson[0]->value->cod."' ";
						$i = false;
					}
					
					$medInfo[ $medJson[0]->value->cod ] = $medJson[0]->label;
				}
				$inMed .= " ) ";
				
				$inMed = " AND Promed $inMed ";
				
				if( $i ){	//Si no pasa por el for se deja en blanco
					$inMed = " AND Promed IN( '' )";
				}
			}
		}
	}
	
	
	//Consulta el diagnostico
	$diaOri = $dia;
	
	$inDia = '';
	if( !empty( $dia ) && $dia != '%' ){
		
		$dia = trim( consultarDiagnosticos( $conex, $dia, false ) );
		
		//Creo string IN para la consulta
		if( !empty( $dia ) ){
		
			$dia = explode( "\n", trim( $dia ) );
		
			$inDia .= " IN ( ";
			$i = true;
			foreach( $dia as $keyDia => $valueDia  ){
			
				$diaJson = json_decode( $valueDia );
				
				if( $diaJson[0]->value->cod != '*' || !( strpos( strtolower( $diaJson[0]->value->des ), strtolower( $diaOri  ) ) === false ) ){
					if( $i )
						$inDia .= "'".$diaJson[0]->value->cod."' ";
					else
						$inDia .= ",'".$diaJson[0]->value->cod."' ";
						
					$i = false;
				}
				
				$diaInfo[ $diaJson[0]->value->cod ] = $diaJson[0]->label;
			}
			$inDia .= " ) ";
			
			$inDia = " AND Prodia $inDia ";
			
			if( $i ){
				$inDia = "AND Prodia IN ( '' ) ";
			}
		}
	}


	//Consulta el tratamiento
	$traOri = $tra;
	
	$inTra = '';
	if( !empty( $tra ) && $tra != '%' ){
		
		$tra = trim( consultarTratamientos( $conex, $wbasedato, $tra, false ) );
		
		//Creo string IN para la consulta
		if( !empty( $tra ) ){
		
			$tra = explode( "\n", trim( $tra ) );
		
			$inTra .= " IN ( ";
			$i = true;
			foreach( $tra as $keyTra => $valueTra  ){
			
				$traJson = json_decode( $valueTra );
				
				if( $traJson[0]->value->cod != '*' || !( strpos( strtolower( $traJson[0]->value->des ), strtolower( $traOri  ) ) === false ) ){
					if( $i )
						$inTra .= "'".$traJson[0]->value->cod."' ";
					else
						$inTra .= ",'".$traJson[0]->value->cod."' ";
						
					$i = false;
				}
				
				$traInfo[ $traJson[0]->value->cod ] = $traJson[0]->label;
			}
			$inTra .= " ) ";
			
			$inTra = " AND Protra $inTra ";
			
			if( $i ){
				$inTra = "AND Protra IN ( '' ) ";
			}
		}
	}

	
	$inCio = '';
	if( !empty( $cio ) && $cio != '%' ){
		
		$cio = trim( consultarDiagnosticosCIO( $conex, $wbasedato, $cio, false ) );
		
		//Creo string IN para la consulta
		if( !empty( $cio ) ){
		
			$cio = explode( "\n", trim( $cio ) );
		
			$inCio .= " IN ( ";
			$i = true;
			foreach( $cio as $keyCio => $valueCio  ){
			
				$diaJson = json_decode( $valueCio );
				
				if( $diaJson[0]->value->cod != '*' || !( strpos( strtolower( $diaJson[0]->value->des ), strtolower( $diaOri  ) ) === false ) ){
					if( $i )
						$inCio .= "'".$diaJson[0]->value->cod."' ";
					else
						$inCio .= ",'".$diaJson[0]->value->cod."' ";
						
					$i = false;
				}
				
				$diaInfo[ $diaJson[0]->value->cod ] = $diaJson[0]->label;
			}
			$inCio .= " ) ";
			
			$inCio = " AND Procio $inCio ";
			
			if( $i ){
				$inCio = "AND Procio IN ( '' ) ";
			}
		}
	}
	
	
	$inTipOrdHce = "";
	$inTipArt = "";
	$inTipPro = "";
	switch( $slTipPro ){
	
		case '%': {
			$inTipOrdHce = "";
			$inTipArt = "";
			$inTipPro = "";
		}
		break;
		
		case 'prHCE':
		case 'prOrdenes':{
			$inTipOrdHce = " AND Protip = '".substr( $slTipPro, 2 )."' ";
			$inTipArt = " AND 1 = 0 ";
			$inTipPro = " AND 1 = 0 ";
		}
		break;
		
		case 'ctcArts':{
			$inTipOrdHce = " AND Protip = '' ";
			$inTipArt = " AND 1 = 1 ";
			$inTipPro = " AND 1 = 0 ";
		} break;
		
		case 'ctcProcs':{
			$inTipOrdHce = " AND Protip = '' ";
			$inTipArt = " AND 1 = 0 ";
			$inTipPro = " AND 1 = 1 ";
		}
		break;
		
		default: break;
	}
	
	//Consulto el nombre de los protocolos con las siguientes reglas
	// - Que el nombre no sea el codigo de un medicamento o procedimiento no pos
	// - Si el nombre a buscar es un medicamento no pos, que sea el nombre generico del medicamento
	@$sql = "SELECT
				Procod, Pronom, Protip, Promed, Proesp, Prodia, Procco, Proest, a.id as Proid, Procio, Proped, Prorec, Protra
			FROM
				{$wbasedato}_000137 a
			WHERE
				pronom LIKE '%$nom%'
				AND proest = 'on'
				$inMed
				AND Proesp LIKE '%$esp%'
				$inDia
				$inTra
				AND Proped LIKE '$ped'
				AND Prorec LIKE '$rec'
				$inCio
				AND Procco LIKE '%$cco%'
				$inTipOrdHce
				AND pronom NOT IN ( SELECT artcod
									   FROM {$wbasedato}_000026 b
									  WHERE
										Artcod = a.pronom
										AND Artpos = 'N'
									 UNION
									 SELECT Codigo
									   FROM {$hce}_000047 c
									  WHERE
										Codigo = a.pronom
										AND NoPos = 'on'
										AND nuevo != 'on'
									)
			UNION
			SELECT
				Procod, artgen as Pronom, 'Ctc Medicamentos' as Protip, Promed, Proesp, Prodia, Procco, Proest, a.id as Proid, Procio, Proped, Prorec, Protra
			FROM
				{$wbasedato}_000137 a, {$wbasedato}_000026 b
			WHERE
				artgen LIKE '%$nom%'
				AND proest = 'on'
				$inMed
				AND Proesp LIKE '%$esp%'
				$inDia
				$inTra
				AND Proped LIKE '$ped'
				AND Prorec LIKE '$rec'
				$inCio
				AND Procco LIKE '%$cco%'
				AND artpos = 'N'
				AND Pronom = artcod
				$inTipArt
			UNION
			SELECT
				Procod, Descripcion as Pronom, 'Ctc Procedimientos' as Protip, Promed, Proesp, Prodia, Procco, Proest, a.id as Proid, Procio, Proped, Prorec, Protra
			FROM
				{$wbasedato}_000137 a, {$hce}_000047 b
			WHERE
				Descripcion LIKE '%$nom%'
				AND proest = 'on'
				$inMed
				AND Proesp LIKE '%$esp%'
				$inDia
				$inTra
				AND Proped LIKE '$ped'
				AND Prorec LIKE '$rec'
				$inCio
				AND Procco LIKE '%$cco%'
				AND NoPos = 'on'
				AND Pronom = Codigo
				$inTipPro
				AND nuevo != 'on'
			ORDER BY
				Protip, pronom
			";
			
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
	
		$val .= "<br>";
		$val .= "<center><b>RESULTADO DE PROTOCOLOS</b></center>";
		$val .= "<br>";
		$val .= "<table align='center'>";
		
		$val .= "<tr class='encabezadotabla'>";
		$val .= "<th>Tipo de protocolo</th>";
		$val .= "<th>Nombre de protocolo</th>";
		$val .= "<th>Especialista de<br>la salud</th>";
		$val .= "<th>Especialidad</th>";
		$val .= "<th>Diagn&oacute;stico</th>";
		$val .= "<th>Tratamiento</th>";
		$val .= "<th>Cco</th>";
		$val .= "<th>CIE-O</th>";
		$val .= "<th>Pedi&aacute;trico</th>";
		$val .= "<th>Reca&iacute;da</th>";
		$val .= "<th></th>";
		$val .= "</tr>";
		
		$espInfo[ "*" ] = "* - Todos";
		$ccoInfo[ "*" ] = "* - Todos";
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		
			//Consulto el nombre del medico
			//Solo si con anterioridad no se ha encontrado
			if( !isset( $medInfo[ $rows[ 'Promed' ] ] ) ){
				$medInfo[ $rows[ 'Promed' ] ] = consultarMedico( $conex, $wbasedato, $rows[ 'Promed' ], true );
				
				$medInfo[ $rows[ 'Promed' ] ] = json_decode( trim( $medInfo[ $rows[ 'Promed' ] ] ) );
				
				$medInfo[ $rows[ 'Promed' ] ] = $medInfo[ $rows[ 'Promed' ] ][0]->label;
			}
			
			//Consulto el diagnostico
			//Solo si con anterioridad no se ha encontrado
			if( !isset( $diaInfo[ $rows[ 'Prodia' ] ] ) ){
				
				$diaInfo[ $rows[ 'Prodia' ] ] = consultarDiagnosticos( $conex, $rows[ 'Prodia' ], true );
				
				$diaInfo[ $rows[ 'Prodia' ] ] = json_decode( trim( $diaInfo[ $rows[ 'Prodia' ] ] ) );
				
				$diaInfo[ $rows[ 'Prodia' ] ] = $diaInfo[ $rows[ 'Prodia' ] ][0]->label;
			}
			
			//Consulto el tratamiento
			//Solo si con anterioridad no se ha encontrado
			if( !isset( $traInfo[ $rows[ 'Protra' ] ] ) ){
				
				$traInfo[ $rows[ 'Protra' ] ] = consultarTratamientos( $conex, $wbasedato, $rows[ 'Protra' ], true );
				
				$traInfo[ $rows[ 'Protra' ] ] = json_decode( trim( $traInfo[ $rows[ 'Protra' ] ] ) );
				
				$traInfo[ $rows[ 'Protra' ] ] = $traInfo[ $rows[ 'Protra' ] ][0]->label;
			}

			//Consulto el diagnóstico CIO
			if( !isset( $cioInfo[ $rows[ 'Procio' ] ] ) ){
				
				$cioInfo[ $rows[ 'Procio' ] ] = consultarDiagnosticosCIO( $conex, $wbasedato, $rows[ 'Procio' ], true );
				
				$cioInfo[ $rows[ 'Procio' ] ] = json_decode( trim( $cioInfo[ $rows[ 'Procio' ] ] ) );
				
				$cioInfo[ $rows[ 'Procio' ] ] = $cioInfo[ $rows[ 'Procio' ] ][0]->label;
			}
			
			//Consulto la especialidad del medico
			//Solo si no se ha consultado con anterioridad
			if( !isset( $espInfo[ $rows[ 'Proesp' ] ] ) ){
				$espInfo[ $rows[ 'Proesp' ] ] = consultarEspecialidad( $conex, $wbasedato, $rows[ 'Proesp' ] );
			}
			
			//Consulto el codigo del cco solo si no se ha encontrado
			if( !isset( $ccoInfo[ $rows[ 'Procco' ] ] ) ){
				$ccoInfo[ $rows[ 'Procco' ] ] = consultarCcos( $conex, $wbasedato, $rows[ 'Procco' ] );
				
				$ccoInfo[ $rows[ 'Procco' ] ] = json_decode( trim( $ccoInfo[ $rows[ 'Procco' ] ] ) );
				
				$ccoInfo[ $rows[ 'Procco' ] ] = $ccoInfo[ $rows[ 'Procco' ] ][0]->label;
			}
			
			$val .= "<tr class='fila".($i%2+1)."'>";
			
			$val .= "<td>".$rows[ 'Protip' ]."</td>";
			$val .= "<td>".$rows[ 'Pronom' ]."</td>";
			$val .= "<td>".$medInfo[ $rows[ 'Promed' ] ]."</td>";
			$val .= "<td>".$espInfo[ $rows[ 'Proesp' ] ]."</td>";
			$val .= "<td>".$diaInfo[ $rows[ 'Prodia' ] ]."</td>";
			$val .= "<td>".$traInfo[ $rows[ 'Protra' ] ]."</td>";
			$val .= "<td>".$ccoInfo[ $rows[ 'Procco' ] ]."</td>";
			$val .= "<td>".$cioInfo[ $rows[ 'Procio' ] ]."</td>";
			$val .= "<td align='center'>".$rows[ 'Proped' ]."</td>";
			$val .= "<td align='center'>".$rows[ 'Prorec' ]."</td>";
			$val .= "<td><a href='#' onClick='cargarProtocolos( {$rows[ 'Proid' ]} );'>cargar</a></td>";
			
			$val .= "</tr>";
		}
		
		$val .= "</table>";
	}
	else{
		$val .= "<br><center><b>NO SE ENCONTRARON PROTOCOLOS</b></center>";
	}
	
	return $val;
}


function buscarRegistroEncCtcPro( $conex, $wbasedato, $registro='' ){

	$val = '';
	if( $registro == '' )
		$registro = $_POST['Encid'];
	
	$sql = "SELECT Procod
			  FROM {$wbasedato}_000137
			 WHERE id = '{$registro}'";	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){	
		$rows = mysql_fetch_array( $res );		
		$val = $rows[0];
	}	
	return $val;
}


/****************************************************************************************************************
 * Para los Ctc, el encabezado es Unico
 ****************************************************************************************************************/
function buscarRegistroCtc( $conex, $wbasedato, $whce, $wcenmez, $wencabezado='', $validando=false ){

	$val = '';
	if( $wencabezado == "" ){
		$wencabezado = array(
					'Protip' => $_POST['dat_Enctip'],
					'Promed' => $_POST['dat_Encmed'],
					'Proesp' => $_POST['dat_Encesp'],
					'Prodia' => $_POST['dat_Encdia'],
					'Protra' => $_POST['dat_Enctra'],
					'Procco' => $_POST['dat_Enccco'],
					'Pronom' => $_POST['dat_Encnom'],
					'Procio' => $_POST['dat_Enccio'],
					'Proped' => $_POST['dat_Encped'],
					'Prorec' => $_POST['dat_Encrec']
				);
	}
	
	$sql = "SELECT  *
			  FROM  {$wbasedato}_000137
			 WHERE	Protip = '".$wencabezado['Protip']."'
					AND Promed = '".$wencabezado['Promed']."'
					AND Proesp = '".$wencabezado['Proesp']."'
					AND Prodia = '".$wencabezado['Prodia']."'
					AND Protra = '".$wencabezado['Protra']."'
					AND Procco = '".$wencabezado['Procco']."'
					AND Pronom = '".$wencabezado['Pronom']."'
					AND Procio = '".$wencabezado['Procio']."'
					AND Proped = '".$wencabezado['Proped']."'
					AND Prorec = '".$wencabezado['Prorec']."'
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		$rows = mysql_fetch_array( $res );
		//Si encuentra el encabezado traigo todos los datos del protocolo para cargarlos
		if( $validando==false)
			$val = consultarProtocoloID( $conex, $wbasedato, $whce, $wcenmez, $rows[ 'id' ] );
		else
			$val=true;
	}	
	return $val;
}

function consultarProtocoloID( $conex, $wbasedato, $whce, $wcenmez, $protocolo){
	
	$val = '';
			
	$sql = "(SELECT
				Procod, Pronom, Protip, Promed, Proesp, Prodia, Procco, Proest, a.id as Proid, Dprpro, Dprpes, Dprcod, Dprfre, Dprvia, Dprcnd, Dprdos, Dprobs, Dprjus, Dprete, Dprese, Dprest, b.id as Dprid, '' as Dprcmp, '' as Dprval, 'Ordenes' as prg, Procio, Proped, Prorec, Dprtre, Dprbib, Protra, Dprops, Dpropn, Dpriin, Prondi
			FROM
				{$wbasedato}_000137 a, {$wbasedato}_000138 b
			WHERE
				Dprpro = Procod
				AND Dprest = 'on'
				AND Proest = 'on'
				AND a.id = '$protocolo' )
			UNION
			(SELECT
				Procod, Pronom, Protip, Promed, Proesp, Prodia, Procco, Proest, a.id as Proid, Dprpro, '' as Dprpes, Dprcod, '' as Dprfre, '' as Dprvia, '' as Dprcnd, '' as Dprdos, '' as Dprobs, '' as Dprjus, '' as Dprete, '' as Dprese, Dprest, b.id as Dprid, Dprcmp, Dprval, 'HCE' as prg, Procio, Proped, Prorec, '' as Dprtre, '' as Dprbib, Protra, '' as Dprops, '' as Dpropn, '' as Dpriin, Prondi
			FROM
				{$wbasedato}_000137 a, {$wbasedato}_000139 b
			WHERE
				Dprpro = Procod
				AND Dprest = 'on'
				AND Proest = 'on'
				AND a.id = '$protocolo' )
			ORDER BY
				Procod, Dprpes
			";

	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
			
	if( $num > 0 ){
	
		$val = '';
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			$wcodigo_aux = $rows[ 'Dprcod' ];
			//Creo el encabezado del examen
			if( $i == 0 ){
			
				$datArticulos[ 'value' ] = Array( "cod"=> $rows[ 'Procod' ],
											      "cod_oculto"=> $wcodigo_aux,
												  "nom"=> $rows[ 'Pronom' ],
												  "tip"=> $rows[ 'Protip' ],
												  "med"=> $rows[ 'Promed' ],
												  "esp"=> $rows[ 'Proesp' ],
												  "dia"=> $rows[ 'Prodia' ],
												  "tra"=> $rows[ 'Protra' ],
												  "pro"=> "",
												  "cco"=> $rows[ 'Procco' ],
												  "cio"=> $rows[ 'Procio' ],
												  "ped"=> $rows[ 'Proped' ],
												  "rec"=> $rows[ 'Prorec' ],
												  "id" => $rows[ 'Proid' ],
												  "ndi"=> $rows[ 'Prondi' ],
												  "rno"=> "", //nombre de reemplazo, solo para ctcs
										   );	//Este es el dato a procesar en javascript
				
				$datArticulos[ 'label' ] = "{$rows[ 'Pronom' ]}";	//Este es el que ve el usuario
				
				//Consulto la informacion necesaria segun los casos especiales que son:
				//Especialista de la salud (médico), Diagnostico y procedimiento
				
				//Cosulto la información del médico
				if( trim( $rows[ 'Promed' ] ) != '' ){
					$detMedJson = consultarMedico( $conex, $wbasedato, $rows[ 'Promed' ] );	//La informacion devuelta es un objeto Json
					$datArticulos[ 'value' ][ 'med' ] = json_decode( $detMedJson );	//Por ser ya un objeto json no se codifica
				}				
				
				//Cosulto la información del diagnostico
				if( trim( $rows[ 'Prodia' ] ) != '' ){
					$detDiaJson = consultarDiagnosticos( $conex, $rows[ 'Prodia' ] );	//La informacion devuelta es un objeto Json
					$datArticulos[ 'value' ][ 'dia' ] = json_decode( $detDiaJson );	//Por ser ya un objeto json no se codifica
				}
				
				//Cosulto la información del tratamiento
				if( trim( $rows[ 'Protra' ] ) != '' ){
					$detTraJson = consultarTratamientos( $conex, $wbasedato, $rows[ 'Protra' ] );	//La informacion devuelta es un objeto Json
					$datArticulos[ 'value' ][ 'tra' ] = json_decode( $detTraJson );	//Por ser ya un objeto json no se codifica
				}

				//Cosulto la información del diagnostico segun el CIO
				if( trim( $rows[ 'Procio' ] ) != '' ){
					$detCioJson = consultarDiagnosticosCIO( $conex, $wbasedato, $rows[ 'Procio' ] );	//La informacion devuelta es un objeto Json
					$datArticulos[ 'value' ][ 'cio' ] = json_decode( $detCioJson );	//Por ser ya un objeto json no se codifica
				}
				
			}
		
			//Busco la informacion del protocolo
			if( $rows[ 'prg' ] != 'HCE' ){
			
				//Consulto la informaciónNecesaria segun el caso, si es medicamento o procedimiento
				switch( $rows[ 'Dprpes' ] ){
				
					case 'Medicamentos':
						$rows[ 'Dprcod' ] = json_decode( consultarArticulo( $conex, $wbasedato, $wcenmez, $rows[ 'Dprcod' ], false, true ) );
					break;
					
					case 'CtcMedicamentos':
						$rows[ 'Medrem' ] = json_decode( consultarArticuloReemplazo( $conex, $wbasedato, $wcenmez, $rows[ 'Dprpro' ] ) );	//Medicamneto de reemplazo
						$rows[ 'Dprcod' ] = json_decode( consultarArticulo( $conex, $wbasedato, $wcenmez, $rows[ 'Dprcod' ], true, true ) );
					break;
					
					case 'Procedimientos':
						 $rows[ 'Dprcod' ] = json_decode( consultarProcedimiento_aux( $conex, $wbasedato, $whce, $rows[ 'Dprcod' ], false, true ));
					break;
					
					case 'CtcProcedimientos':						
						 $rows[ 'Dprcod' ] = json_decode( consultarProcedimiento_aux( $conex, $wbasedato, $whce, $rows[ 'Dprcod' ], true, true ));
					
					break;
					
					default: break;
				}
				
				$datArticulos[ 'pes' ][ $rows[ 'Dprpes' ] ][] = Array( "cod"=> $rows[ 'Dprcod' ],					//Codigo del item( articulo o procedimiento)
																	   "cod_oculto"=> $wcodigo_aux,
																	   "fre"=> $rows[ 'Dprfre' ],					//Codigo de la frecuencia
																	   "via"=> $rows[ 'Dprvia' ],					//via de administracion de la frecuencia
																	   "cnd"=> $rows[ 'Dprcnd' ],					//Condicion
																	   "dos"=> $rows[ 'Dprdos' ], 					//Dosis
																	   "obs"=> utf8_encode( $rows[ 'Dprobs' ] ),	//Observaciones
																	   "jus"=> utf8_encode( $rows[ 'Dprjus' ] ),	//Justifiacion, valido para Ctc articulo
																	   "ops"=> utf8_encode( $rows[ 'Dprops' ] ), 	//¿Existen opciones POS? (Si)
																	   "opn"=> utf8_encode( $rows[ 'Dpropn' ] ),	//¿Existen opciones POS? (No)
																	   "ete"=> utf8_encode( $rows[ 'Dprete' ] ),	//Efecto terapeutico para ctc
																	   "ese"=> utf8_encode( $rows[ 'Dprese' ] ), 	//Efectos secundarios para ctc
																	   "est"=> $rows[ 'Dprest' ],					//Estado del registro
																	   "tre"=> utf8_encode( $rows[ 'Dprtre' ] ),	//Tiempo de respuesta esperado
																	   "bib"=> utf8_encode( $rows[ 'Dprbib' ] ),	//Bibliografía
																	   "id" => $rows[ 'Dprid' ], 					//Estado del registro
																	   "rem"=> $rows[ 'Medrem' ],					//Información del medicamento de reemplazo
																	   "iin"=> utf8_encode( $rows[ 'Dpriin' ] )		//Indicaciones invima
																	);
			
				//Creo el resultado como un json
				//Primero creo un array con los valores necesarios
				// $datArticulos[ 'value' ] = Array( "cod"=> $rows[ 'Meduma' ], "des"=> $rows[ 'Descripcion' ] );	//Este es el dato a procesar en javascript
				// $datArticulos[ 'label' ] = "{$rows[ 'Meduma' ]}-{$rows[ 'Descripcion' ]}";	//Este es el que ve el usuario
				$dat = Array();
				$dat[] = $datArticulos;
				
				
			}
			else{
				
				$rows[ 'Dprcod' ] = json_decode( consultarFormulariosHCE( $conex, $whce, $rows[ 'Dprcod' ] ) );

				$datArticulos[ 'hce' ][] = Array(  "cod"=> $rows[ 'Dprcod' ],	//Codigo del item( articulo o procedimiento)
												   "cmp"=> $rows[ 'Dprcmp' ],	//Consecutivo del campo
												   "val"=> utf8_encode( $rows[ 'Dprval' ] ),	//via de administracion de la frecuencia
												   "est"=> $rows[ 'Dprest' ],	//Estado del registro
												   "id" => $rows[ 'Dprid' ]		//id del registro
												);			
				$dat = Array();
				$dat[] = $datArticulos;
			}
		}
		$val .= json_encode( $dat )."\n";
	}
	
	return $val;
}

/********************************************************************************************************
 * Consulta el nombre de un protocolo
 ********************************************************************************************************/
function consultarBuscadorProtocolo( $conex, $wbasedato, $hce, $protocolo ){

	$val = '';
	$dat = Array();
	$dat1 = Array();

	//Consulto el nombre de los protocolos con las siguientes reglas
	// - Que el nombre no sea el codigo de un medicamento o procedimiento no pos
	// - Si el nombre a buscar es un medicamento no pos, que sea el nombre generico del medicamento
	$sql = "SELECT
				Procod, Pronom, Protip, Promed, Proesp, Prodia, Procco, Proest, a.id as Proid, Protra
			FROM
				{$wbasedato}_000137 a
			WHERE
				pronom LIKE '%$protocolo%'
				AND  pronom NOT IN ( SELECT artcod
									   FROM {$wbasedato}_000026 b
									  WHERE
										Artcod = a.pronom
										AND Artpos = 'N'
									 UNION
									 SELECT Codigo
									   FROM {$hce}_000047 c
									  WHERE
										Codigo = a.pronom
										AND NoPos = 'on'
										AND nuevo != 'on'
									)
			UNION
			SELECT
				Procod, artgen as Pronom, Protip, Promed, Proesp, Prodia, Procco, Proest, a.id as Proid, Protra
			FROM
				{$wbasedato}_000137 a, {$wbasedato}_000026 b
			WHERE
				artgen LIKE '%$protocolo%'
				AND artpos = 'N'
				AND Pronom = artcod
			UNION
			SELECT
				Procod, Descripcion as Pronom, Protip, Promed, Proesp, Prodia, Procco, Proest, a.id as Proid, Protra
			FROM
				{$wbasedato}_000137 a, {$hce}_000047 b
			WHERE
				Descripcion LIKE '%$protocolo%'
				AND NoPos = 'on'
				AND Pronom = Codigo
				AND nuevo != 'on'
			";
			
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			//echo $rows[ 'Pronom' ]."\n";
			$dat = Array();
			$dat[] = Array( "label" => $rows[ 'Pronom' ],
							 "value" => Array( "vid"=>$rows[ 'Proid' ],
											  "nom"=> $rows[ 'Pronom' ]
											)
						   );
			
			$val .= json_encode( $dat )."\n";
		}
	}
	
	return $val;
}


function pintarPrgHCE( $conex, $wbasedato, $whce, $wcenmez ){

	echo "<table id='tabHCE' align='center'>";
	
	echo "<tr class='encabezadotabla filappal' align='center'>";
	echo "<th>Acciones</th>";
	echo "<th>Tabla HCE</th>";
	echo "<th>Nombre tabla</th>";
	echo "<th>Campo</th>";
	echo "<th>Tipo dato</th>";
	echo "<th>Valor</th>";
	echo "</tr>";
	
	echo "<tr style='display:none' class='fila1 filappal'>";
	
	//Eliminar
	echo "<td align='center'><img border='0' src='../../images/medical/root/borrar.png' onClick='eliminarFila( this );'></td>";
	
	//Busqueda de la tabla
	echo "<td>";
	echo "<INPUT type='text' name='dat_Detcod'>";
	echo "<INPUT type='hidden' name='dat_Detest' value='on'>";
	echo "<INPUT type='hidden' name='dat_Detid'>";
	echo "</td>";
	
	echo "<td>";
	echo "</td>";
	
	echo "<td>";
	echo "</td>";
	
	//Este campo es para llenar el tipo de dato
	echo "<td align='center'></td>";
	
	echo "<td>";
	//echo "<INPUT type='text' name='dat_Detval'>";
	echo "<TEXTAREA name='dat_Detval' style='width:250px;height:55px'></TEXTAREA>";
	echo "</td>";
	
	echo "</tr>";
	
	echo "</table>";
	
	//Boton de agregar tabla
	echo "<br>";
	echo "<center>";
	echo "<input type='button' name='btAgregarMed' value='Agregar item' style='width:100' onClick='agregarCampoHCE();'/>";
	echo "</center>";
	echo "<br>";
}

/****************************************************************************************
 *
 ****************************************************************************************/
function consultarFormulariosHCE( $conex, $whce, $tab ){

	$val = '';

				// AND Detest = 'on'
				// AND Encest = 'on'
	$sql = "SELECT
				Encpro, Encdes, Encest
			FROM
				{$whce}_000001, {$whce}_000002
			WHERE
				(Encpro LIKE '%$tab%'
				OR Encdes LIKE '%$tab%' )
				AND Encpro = Detpro
				AND Dettip IN ( 'Memo', 'Numero', 'Texto', 'Booleano', 'Fecha', 'Seleccion', 'Hora', 'Grid' )
			GROUP BY
				Encpro, Encdes
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		
		for( $i = 0; $rows = mysql_fetch_array( $res ) ; $i++ ){
		
			$cmp = consultarCampoTablaHCE( $conex, $whce, $rows[ 'Encpro' ] );

			//Creo el resultado como un json
			//Primero creo un array con los valores necesarios
			$datArticulos[ 'value' ] = Array( "cod" => $rows[ 'Encpro' ],
											  "des" => htmlentities( $rows[ 'Encdes' ] ),
											  "cmp" => $cmp,
											);	//Este es el dato a procesar en javascript
											
			$datArticulos[ 'label' ] = "{$rows[ 'Encpro' ]}-".htmlentities( $rows[ 'Encdes' ] );	//Este es el que ve el usuario
			
			$datArticulos[ 'msg' ] = "";
			
			if( $rows[ 'Encest' ] != 'on' ){
				$datArticulos[ 'msg' ] .= "La tabla <b>".htmlentities( $rows[ 'Encdes' ] )."</b> se encuentra <b>INACTIVA</b>";
			}
			
			$dat = Array();
			
			$dat[] = $datArticulos;
			
			$val .= json_encode( $dat )."\n";
		}
	}
	
	return $val;	
}

/****************************************************************************************
 *
 ****************************************************************************************/
function consultarCampoTablaHCE( $conex, $whce, $tabla ){

	$val = array();

				// AND Detest = 'on'
	$sql = "SELECT
				Detpro, Detcon, Detdes, Detnpa, Dettip, Detest
			FROM
				{$whce}_000002
			WHERE
				Detpro = '$tabla'
				AND Dettip IN ( 'Memo', 'Numero', 'Texto', 'Booleano', 'Fecha', 'Seleccion', 'Hora', 'Grid')
			ORDER BY
					Detorp, Detdes
			";
		
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			
			$msg = "";
			
			if( $rows[ 'Detest' ] != 'on' ){
				$msg = "El campo <b>".htmlentities( $rows[ 'Detdes' ]." (".$rows[ 'Dettip' ].")" )."</b> se encuentra <b>INACTIVO<b></b>";
			}
			
			// $val .= "<option value={$rows['Detcon']}>".htmlspecialchars( $rows['Detdes'] )."</option>";
			$val[] = Array( "cod" => $rows[ 'Detcon' ],
					 	    "des" => htmlentities( $rows[ 'Detdes' ]." (".$rows[ 'Dettip' ].")" ),
							"tip" => htmlentities( $rows[ 'Dettip' ] ),
							"con" => htmlentities( $rows[ 'Detcon' ] ),
							"est" => htmlentities( $rows[ 'Detest' ] ),
							"msg" => $msg,
					);	//Este es el dato a procesar en javascript
		}
	}
	
	return $val;
}

/************************************************************************************************************
 * Consulta un protocolo
 ************************************************************************************************************/
function consultarProtocolo( $conex, $wbasedato, $whce, $wcenmez, $protocolo){

	$val = '';

	$sql = "SELECT
				Procod, Pronom, Protip, Promed, Proesp, Prodia, Procco, Proest, a.id as Proid, Dprpro, Dprpes, Dprcod, Dprfre, Dprvia, Dprcnd, Dprdos, Dprobs, Dprjus, Dprete, Dprese, Dprest, b.id as Dprid, Protra, Dprops, Dpropn, Dpriin
			FROM
				{$wbasedato}_000137 a, {$wbasedato}_000138 b
			WHERE
				Dprpro = Procod
				AND Dprest = 'on'
				AND Proest = 'on'
				AND Pronom = '$protocolo'
			ORDER BY
				Procod, Dprpes
			";
			
	$sql = "(SELECT
				Procod, Pronom, Protip, Promed, Proesp, Prodia, Procco, Proest, a.id as Proid, Dprpro, Dprpes, Dprcod, Dprfre, Dprvia, Dprcnd, Dprdos, Dprobs, Dprjus, Dprete, Dprese, Dprest, b.id as Dprid, '' as Dprcmp, '' as Dprval, 'Ordenes' as prg, Protra, Dprops, Dpropn, Dpriin
			FROM
				{$wbasedato}_000137 a, {$wbasedato}_000138 b
			WHERE
				Dprpro = Procod
				AND Dprest = 'on'
				AND Proest = 'on'
				AND Pronom = '$protocolo' )
			UNION
			(SELECT
				Procod, Pronom, Protip, Promed, Proesp, Prodia, Procco, Proest, a.id as Proid, Dprpro, '' as Dprpes, Dprcod, '' as Dprfre, '' as Dprvia, '' as Dprcnd, '' as Dprdos, '' as Dprobs, '' as Dprjus, '' as Dprete, '' as Dprese, Dprest, b.id as Dprid, Dprcmp, Dprval, 'HCE' as prg, Protra, '' as Dprops, '' as Dpropn, '' as Dpriin
			FROM
				{$wbasedato}_000137 a, {$wbasedato}_000139 b
			WHERE
				Dprpro = Procod
				AND Dprest = 'on'
				AND Proest = 'on'
				AND Pronom = '$protocolo' )
			ORDER BY
				Procod, Dprpes
			";
			
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
			
	if( $num > 0 ){
	
		$val = '';
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			
			$wcodigo_aux = $rows[ 'Dprcod' ];
			//Creo el encabezado del examen
			if( $i == 0 ){
			
				$datArticulos[ 'value' ] = Array( "cod"=> $rows[ 'Procod' ],
												  "nom"=> $rows[ 'Pronom' ],
												  "tip"=> $rows[ 'Protip' ],
												  "med"=> $rows[ 'Promed' ],
												  "esp"=> $rows[ 'Proesp' ],
												  "dia"=> $rows[ 'Prodia' ],
												  "tra"=> $rows[ 'Protra' ],
												  "pro"=> '',
												  "cco"=> $rows[ 'Procco' ],
												  "id"=> $rows[ 'Proid' ]
										   );	//Este es el dato a procesar en javascript
				
				$datArticulos[ 'label' ] = "{$rows[ 'Pronom' ]}";	//Este es el que ve el usuario
				
				//Consulto la informacion necesaria segun los casos especiales que son:
				//Especialista de la salud (médico), Diagnostico y procedimiento
				
				//Cosulto la información del médico
				if( trim( $rows[ 'Promed' ] ) != '' ){
					$detMedJson = consultarMedico( $conex, $wbasedato, $rows[ 'Promed' ] );	//La informacion devuelta es un objeto Json
					$datArticulos[ 'value' ][ 'med' ] = json_decode( $detMedJson );	//Por ser ya un objeto json no se codifica
				}
				
				//Cosulto la información del diagnostico
				if( trim( $rows[ 'Prodia' ] ) != '' ){
					$detDiaJson = consultarDiagnosticos( $conex, $rows[ 'Prodia' ] );	//La informacion devuelta es un objeto Json
					$datArticulos[ 'value' ][ 'dia' ] = json_decode( $detDiaJson );	//Por ser ya un objeto json no se codifica
				}
				
				//Cosulto la información del tratamiento
				if( trim( $rows[ 'Protra' ] ) != '' ){
					$detTraJson = consultarTratamientos( $conex, $wbasedato, $rows[ 'Protra' ] );	//La informacion devuelta es un objeto Json
					$datArticulos[ 'value' ][ 'tra' ] = json_decode( $detTraJson );	//Por ser ya un objeto json no se codifica
				}
			}
		
			//Busco la informacion del protocolo
			if( $rows[ 'prg' ] != 'HCE' ){
			
				//Consulto la informaciónNecesaria segun el caso, si es medicamento o procedimiento
				switch( $rows[ 'Dprpes' ] ){
				
					case 'Medicamentos':
						$rows[ 'Dprcod' ] = json_decode( consultarArticulo( $conex, $wbasedato, $wcenmez, $rows[ 'Dprcod' ] ) );
					break;
					
					case 'CtcMedicamentos':
						$rows[ 'Dprcod' ] = json_decode( consultarArticulo( $conex, $wbasedato, $wcenmez, $rows[ 'Dprcod' ], true ) );
					break;
					
					case 'Procedimientos':
						 $rows[ 'Dprcod' ] = json_decode( consultarProcedimiento( $conex, $wbasedato, $whce, $rows[ 'Dprcod' ] ) );
					break;
					
					case 'CtcProcedimientos':
						 $rows[ 'Dprcod' ] = json_decode( consultarProcedimiento( $conex, $wbasedato, $whce, $rows[ 'Dprcod' ], true ) );
					break;
					
					default: break;
				}
			
				$datArticulos[ 'pes' ][ $rows[ 'Dprpes' ] ][] = Array( "cod"=> $rows[ 'Dprcod' ],	//Codigo del item( articulo o procedimiento)
																	 "fre"=> $rows[ 'Dprfre' ],	//Codigo de la frecuencia
																	 "via"=> $rows[ 'Dprvia' ],	//via de administracion de la frecuencia
																	 "cnd"=> $rows[ 'Dprcnd' ],	//Condicion
																	 "dos"=> $rows[ 'Dprdos' ], //Dosis
																	 "obs"=> utf8_encode( $rows[ 'Dprobs' ] ),	//Observaciones
																	 "cod_oculto"=> $wcodigo_aux,	//Codigo oculto
																	 "jus"=> utf8_encode( $rows[ 'Dprjus' ] ),	//Justifiacion, valido para Ctc articulo
																	 "ops"=> utf8_encode( $rows[ 'Dprops' ] ), 	//¿Existen opciones POS? (Si)
																	 "opn"=> utf8_encode( $rows[ 'Dpropn' ] ),	//¿Existen opciones POS? (No)
																	 "ete"=> utf8_encode( $rows[ 'Dprete' ] ),	//Efecto terapeutico para ctc
																	 "ese"=> utf8_encode( $rows[ 'Dprese' ] ), //Efectos secundarios para ctc
																	 "est"=> $rows[ 'Dprest' ],	//Estado del registro
																	 "id" => $rows[ 'Dprid' ],	//Estado del registro
																	 "iin" => utf8_encode( $rows[ 'Dpriin' ] )	//Indicaciones invima
															    );
			
				//Creo el resultado como un json
				//Primero creo un array con los valores necesarios
				// $datArticulos[ 'value' ] = Array( "cod"=> $rows[ 'Meduma' ], "des"=> $rows[ 'Descripcion' ] );	//Este es el dato a procesar en javascript
				// $datArticulos[ 'label' ] = "{$rows[ 'Meduma' ]}-{$rows[ 'Descripcion' ]}";	//Este es el que ve el usuario
				$dat = Array();
				$dat[] = $datArticulos;
				
				
			}
			else{
				
				$rows[ 'Dprcod' ] = json_decode( consultarFormulariosHCE( $conex, $whce, $rows[ 'Dprcod' ] ) );
				
				$datArticulos[ 'hce' ][] = Array(  "cod"=> $rows[ 'Dprcod' ],	//Codigo del item( articulo o procedimiento)
												   "cmp"=> $rows[ 'Dprcmp' ],	//Consecutivo del campo
												   "val"=> utf8_encode( $rows[ 'Dprval' ] ),	//via de administracion de la frecuencia
												   "est"=> $rows[ 'Dprest' ],	//Estado del registro
												   "id" => $rows[ 'Dprid' ]		//id del registro
												);
			
				$dat = Array();
				$dat[] = $datArticulos;
			}
		}
		$val .= json_encode( $dat )."\n";
	}
	
	return $val;
}

/********************************************************************************
 * Consulto las especialidades por médico
 ********************************************************************************/
function consultarEspecialidadesPorMedico( $conex, $wbasedato, $tid, $med ){

	$val = Array();
	
	$sql = "SELECT
				Esmcod, Esmtdo, Esmndo
			FROM
				{$wbasedato}_000065
			WHERE
				Esmndo = '$med'
			";
			
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
			
	if( $num > 0 ){
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			$val[] = $rows[ 'Esmcod' ];
		}
	}
	
	return $val;
}

/********************************************************************************
 * Consulta el especialista
 ********************************************************************************/
function consultarMedico( $conex, $wbasedato, $med, $porCodigo = true ){

	$val = '';	
	
	if( $porCodigo ){
	
		//Consulta el medico por codigo
		$sql = "(SELECT
					Meduma, Descripcion, Meddoc, Medtdo
				FROM
					{$wbasedato}_000048 a, usuarios
				WHERE
					meduma = '$med'
					AND activo = 'A'
					AND meduma = codigo )
				UNION
				(SELECT Meduma, Descripcion, '' as Meddoc, '' as Medtdo
					FROM ( SELECT '*' AS Meduma,  'Todos' AS Descripcion ) AS a
				WHERE
					Meduma = '$med' )
				ORDER BY
					Descripcion
				";
	}
	else{
		
		//Consulta los medicos segun el parametro med, ya sea que esten por codigo o por descripción
		$sql = "(SELECT Meduma, Descripcion, '' as Meddoc, '' as Medtdo
					FROM ( SELECT '*' AS Meduma,  ' Todos' AS Descripcion ) AS a
				)
				UNION
				(SELECT
					Meduma, Descripcion, Meddoc, Medtdo
				FROM
					{$wbasedato}_000048 a, usuarios
				WHERE
					( Descripcion LIKE '%$med%'
					OR meduma LIKE '%$med%' )
					AND activo = 'A'
					AND meduma = codigo )
				ORDER BY
					Descripcion
				";
	}
			
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
			
	if( $num > 0 ){
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			
			if( !$soloNoPos || ( $soloNoPos && strtolower( $rows[ 'NoPos' ] ) == 'on' ) ){
			
				$rows[ 'Descripcion' ] = trim( $rows[ 'Descripcion' ] );
			
				//Creo el resultado como un json
				//Primero creo un array con los valores necesarios
				$datArticulos[ 'value' ] = Array( "cod"=> $rows[ 'Meduma' ],
												  "des"=> $rows[ 'Descripcion' ],
												  "esp"=> consultarEspecialidadesPorMedico( $conex, $wbasedato, $rows[ 'Medtdo' ], $rows[ 'Meddoc' ] )
											);	//Este es el dato a procesar en javascript
											
				$datArticulos[ 'label' ] = "{$rows[ 'Meduma' ]}-{$rows[ 'Descripcion' ]}";	//Este es el que ve el usuario
				$dat = Array();
				$dat[] = $datArticulos;
				
				$val .= json_encode( $dat )."\n";
			}
		}
	}
	
	return $val;
}

/****************************************************************************************
 * Consulta los diagnosticos segun el CIE10
 ****************************************************************************************/
function consultarDiagnosticos( $conex, $dx, $porCodigo = true ){

	$val = '';

	// //Para dejar la opcion de todos siempre disponible
	// $datArticulos[ 'value' ] = Array( "cod"=> "*", "des"=> "Todos" );	//Este es el dato a procesar en javascript
	// $datArticulos[ 'label' ] = "Todos";	//Este es el que ve el usuario
	// $dat = Array();
	// $dat[] = $datArticulos;
	
	// $val .= json_encode( $dat )."\n";

	//Diagnostico
	$sql = "SELECT
				Codigo, Descripcion
			FROM
				root_000011 a
			WHERE
				Codigo LIKE '%$dx%'
				OR Descripcion LIKE '%$dx%'
			ORDER BY
				Descripcion
			";
	if( $porCodigo ){
	
		//Diagnostico
		$sql = "(SELECT
					Codigo, Descripcion
				FROM
					root_000011 a
				WHERE
					Codigo = '$dx'
				)
				UNION
				(SELECT
					Codigo, Descripcion
				FROM
					( SELECT '*' as Codigo, ' Todos' as Descripcion ) AS a
				WHERE
					Codigo = '$dx'
				)
				ORDER BY
					Descripcion
				";
	}
	else{
	
		//Diagnostico
		$sql = "(SELECT
					Codigo, Descripcion
				FROM
					root_000011 a
				WHERE
					Codigo LIKE '%$dx%'
					OR Descripcion LIKE '%$dx%' )
				UNION
				(SELECT
					Codigo, Descripcion
				FROM
					( SELECT '*' as Codigo, ' Todos' as Descripcion ) AS a
				)
				ORDER BY
					Descripcion
				";
	}
			
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
			
	if( $num > 0 ){
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		
			if( !$soloNoPos || ( $soloNoPos && strtolower( $rows[ 'NoPos' ] ) == 'on' ) ){
			
				$rows[ 'Descripcion' ] = trim( $rows[ 'Descripcion' ] );
			
				//Creo el resultado como un json
				//Primero creo un array con los valores necesarios
				$datArticulos[ 'value' ] = Array( "cod"=> $rows[ 'Codigo' ], "des"=> $rows[ 'Descripcion' ] );	//Este es el dato a procesar en javascript
				$datArticulos[ 'label' ] = "{$rows[ 'Codigo' ]}-{$rows[ 'Descripcion' ]}";	//Este es el que ve el usuario
				$dat = Array();
				$dat[] = $datArticulos;
				
				$val .= json_encode( $dat )."\n";
			}
		}
	}
	
	return $val;
}

/****************************************************************************************
 * Consulta los tratamientos
 ****************************************************************************************/
function consultarTratamientos( $conex, $wbasedato, $dx, $porCodigo = true ){

	$val = '';

	// //Para dejar la opcion de todos siempre disponible
	// $datArticulos[ 'value' ] = Array( "cod"=> "*", "des"=> "Todos" );	//Este es el dato a procesar en javascript
	// $datArticulos[ 'label' ] = "Todos";	//Este es el que ve el usuario
	// $dat = Array();
	// $dat[] = $datArticulos;
	
	// $val .= json_encode( $dat )."\n";

	//Diagnostico
	$sql = "SELECT
				Tracod, Trades
			FROM
				".$wbasedato."_000164 a
			WHERE
				Tracod LIKE '%$dx%'
				OR Trades LIKE '%$dx%'
			ORDER BY
				Trades
			";
	if( $porCodigo ){
	
		//Diagnostico
		$sql = "(SELECT
					Tracod, Trades
				FROM
					".$wbasedato."_000164 a
				WHERE
					Tracod = '$dx'
				)
				UNION
				(SELECT
					Tracod, Trades
				FROM
					( SELECT '*' as Tracod, ' Todos' as Trades ) AS a
				WHERE
					Tracod = '$dx'
				)
				ORDER BY
					Trades
				";
	}
	else{
	
		//Diagnostico
		$sql = "(SELECT
					Tracod, Trades
				FROM
					".$wbasedato."_000164 a
				WHERE
					Tracod LIKE '%$dx%'
					OR Trades LIKE '%$dx%' )
				UNION
				(SELECT
					Tracod, Trades
				FROM
					( SELECT '*' as Tracod, ' Todos' as Trades ) AS a
				)
				ORDER BY
					Trades
				";
	}
			
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
			
	if( $num > 0 ){
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		
			if( !$soloNoPos || ( $soloNoPos && strtolower( $rows[ 'NoPos' ] ) == 'on' ) ){
			
				$rows[ 'Trades' ] = trim( $rows[ 'Trades' ] );
			
				//Creo el resultado como un json
				//Primero creo un array con los valores necesarios
				$datArticulos[ 'value' ] = Array( "cod"=> $rows[ 'Tracod' ], "des"=> $rows[ 'Trades' ] );	//Este es el dato a procesar en javascript
				$datArticulos[ 'label' ] = "{$rows[ 'Tracod' ]}-{$rows[ 'Trades' ]}";	//Este es el que ve el usuario
				$dat = Array();
				$dat[] = $datArticulos;
				
				$val .= json_encode( $dat )."\n";
			}
		}
	}
	
	return $val;
}


/**********************************************************************
 * Consulta los diferentes cco
 **********************************************************************/
function consultarCcos( $conex, $wbasedato, $cco ){

	$val = 'No se encontraron datos';

	$sql = "SELECT
				Ccocod, Cconom
			FROM
				{$wbasedato}_000011 c
			WHERE
				Ccoest = 'on'
				AND (Ccocod LIKE '%$cco%'
					OR Cconom LIKE '%$cco%'
					)
			";
			
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
			
	if( $num > 0 ){
	
		$val = '';
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		
			if( !$soloNoPos || ( $soloNoPos && strtolower( $rows[ 'NoPos' ] ) == 'on' ) ){
			
				//Creo el resultado como un json
				//Primero creo un array con los valores necesarios
				$datArticulos[ 'value' ] = Array( "cod"=> $rows[ 'Ccocod' ], "nom"=> $rows[ 'Cconom' ] );	//Este es el dato a procesar en javascript
				$datArticulos[ 'label' ] = "{$rows[ 'Ccocod' ]}-{$rows[ 'Cconom' ]}";	//Este es el que ve el usuario
				$dat = Array();
				$dat[] = $datArticulos;
				
				$val .= json_encode( $dat )."\n";
			}
		}
	}
	
	return $val;
}

/**************************************************************************************************************
 * Consulta los diferentes examenes o procedimientos que hay guardados
 **************************************************************************************************************/
function consultarProcedimiento( $conex, $wbasedato, $whce, $pro, $soloNoPos = false ){

	$val = 'No se encontraron datos';

	$sql = "SELECT
				b.Codigo, b.Descripcion, b.NoPos
			FROM
				{$whce}_000015 a, {$whce}_000047 b, {$wbasedato}_000011 c
			WHERE
				a.codigo = tipoestudio
				AND servicio = Ccocod
				AND a.estado = 'on'
				AND b.estado = 'on'
				AND Ccoest = 'on'
				AND ( b.Codigo LIKE '%$pro%'
					OR b.Descripcion LIKE '%$pro%' )
			ORDER BY
				b.Descripcion
			";
	
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
	
		$val = '';
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			
			if( !$soloNoPos || ( $soloNoPos && strtolower( $rows[ 'NoPos' ] ) == 'on' ) ){
			
				//Creo el resultado como un json
				//Primero creo un array con los valores necesarios
				$datArticulos[ 'value' ] = Array( "cod"=> $rows[ 'Codigo' ], "des"=> trim( utf8_encode($rows[ 'Descripcion' ]) ) );	//Este es el dato a procesar en javascript
				$datArticulos[ 'label' ] = "{$rows[ 'Codigo' ]}-".trim( utf8_encode($rows[ 'Descripcion' ]) );	//Este es el que ve el usuario
				$dat = Array();
				$dat[] = $datArticulos;
				
				$val .= json_encode( $dat )."\n";
			}
		}
		
		//$val = substr( $val, 1 );
	}

	return $val;
}


/*************************************************************************************************************************
 * parametros: 
 * $soloPos:		Indica si debe buscar solo los procedimiento no Pos
 * $ignorarEstado:	Indica si no debe tener en cuenta los estados del registro. Esto se hace para menejar mensajes a mostrar
 *					al usuario de acuerdo a la configuración de los registros 
 *************************************************************************************************************************/
function consultarProcedimiento_aux( $conex, $wbasedato, $whce, $pro, $soloNoPos = false, $ignorarEstado = false ){

	$val = 'No se encontraron datos';
	
	if( !$ignorarEstado ){
		$estados = "AND a.estado = 'on'
					AND b.estado = 'on'
					AND Ccoest = 'on'";
	}

	$sql = "SELECT
				b.Codigo, b.Descripcion, b.NoPos, a.estado as estTip, b.estado as estPro, Ccoest, a.Descripcion as Tipdes, Cconom, Ccocod, a.Codigo as Tipcod
			FROM
				{$whce}_000015 a, {$whce}_000047 b, {$wbasedato}_000011 c
			WHERE
				a.codigo = tipoestudio
				AND servicio = Ccocod
				AND b.Codigo = '$pro'
				$estados
			ORDER BY
				b.Descripcion
			";
	
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
	
		$val = '';
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			
			if( !$soloNoPos || ( $soloNoPos && strtolower( $rows[ 'NoPos' ] ) == 'on' ) ){
			
				//Creo el resultado como un json
				//Primero creo un array con los valores necesarios
				$datArticulos[ 'value' ] = Array( "cod"=> $rows[ 'Codigo' ], "des"=> trim( utf8_encode($rows[ 'Descripcion' ]) ) );	//Este es el dato a procesar en javascript
				$datArticulos[ 'label' ] = "{$rows[ 'Codigo' ]}-".trim( utf8_encode($rows[ 'Descripcion' ]) );	//Este es el que ve el usuario
				$datArticulos[ 'msg' ] = "";
				
				if( $rows[ 'estPro' ] == 'off' ){
					$datArticulos[ 'msg' ] .= utf8_encode( "- El Procedimiento o examen <b>".$rows[ 'Codigo' ]."-".utf8_encode($rows[ 'Descripcion' ])."</b> se encuentra <b>INACTIVO</b> en el sistema.<br>" );
				}
				
				if( $rows[ 'estTip' ] == 'off' ){
					$datArticulos[ 'msg' ] .= utf8_encode( "- El tipo de orden <b>".$rows[ 'Tipcod' ]."-".utf8_encode($rows[ 'Tipdes' ])."</b> asociado al procedimiento ".$rows[ 'Codigo' ]."-".utf8_encode($rows[ 'Descripcion' ])." se encuentra <b>INACTIVO</b> en el sistema.<br>" );
				}
				
				if( $rows[ 'Ccoest' ] == 'off' ){
					$datArticulos[ 'msg' ] .= utf8_encode( "- El servicio <b>".$rows[ 'Ccocod' ]."-".$rows[ 'Cconom' ]."</b> asociado al procedimiento ".$rows[ 'Codigo' ]."-".utf8_encode($rows[ 'Descripcion' ])." se encuentra <b>INACTIVO</b>.<br>" );
				}
				
				$dat = Array();
				$dat[] = $datArticulos;
				
				$val .= json_encode( $dat )."\n";
			}
		}
	}
	else{
		
		$sql = "SELECT
					Descripcion, Estado, Nuevo
				FROM
					{$whce}_000017 b
				WHERE b.Codigo = '$pro'
			";
	
		$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		
		if( $rows = mysql_fetch_array( $res ) ){
			
			//Creo el resultado como un json
			//Primero creo un array con los valores necesarios
			$datArticulos[ 'value' ] = Array( "cod"=> $pro, "des"=> trim( utf8_encode($rows['Descripcion']) ) );	//Este es el dato a procesar en javascript
			$datArticulos[ 'label' ] = "{$rows[ 'Codigo' ]}-".trim( utf8_encode($rows[ 'Descripcion' ]) );	//Este es el que ve el usuario
			
			$datArticulos[ 'msg' ] = "";
			if( $rows[ 'Nuevo' ] == 'on' )
				$datArticulos[ 'msg' ] .= "El procedimiento con <b>CODIGO: $pro</b> no se encuentra homologado.";
			else
				$datArticulos[ 'msg' ] .= "El procedimiento con <b>CODIGO: $pro</b> se encuentra elminado del lenguage americas.";
			
			if( $rows[ 'Estado' ] != 'on' ){
				if( $datArticulos[ 'msg' ] == "" )
					$datArticulos[ 'msg' ] .= "El procedimiento con <b>CODIGO: $pro</b> se encuentra <B>INACTIVO</B>.";
				else
					$datArticulos[ 'msg' ] .= "<br>El procedimiento con <b>CODIGO: $pro</b> se encuentra <B>INACTIVO</B>.";
			}
			
			$dat = Array();
			$dat[] = $datArticulos;
		}
		else{
			//Creo el resultado como un json
			//Primero creo un array con los valores necesarios
			$datArticulos[ 'value' ] = Array( "cod"=> $pro, "des"=> trim( utf8_encode("No está homologado en el sistema") ) );	//Este es el dato a procesar en javascript
			$datArticulos[ 'label' ] = "$pro".trim( utf8_encode("No está homologado en el sistema") );	//Este es el que ve el usuario
			$datArticulos[ 'msg' ] = "El procedimiento con <b>CODIGO: $pro</b> no se encuentra en el sistema.";
			
			$dat = Array();
			$dat[] = $datArticulos;
		}
		
		$val = json_encode( $dat )."\n";
	}

	return $val;
}


/****************************************************************************************************
 * Indica si un producto es No POS
 ****************************************************************************************************/
function esProductoNoPOSCM( $conex, $wbasedato, $wcenmez, $producto ){

	$val = false;
	
	$sql = "SELECT
				*
			FROM
				{$wcenmez}_000003 a, {$wcenmez}_000009 b, {$wbasedato}_000026 c
			WHERE
				pdepro='$producto'
				AND pdeins = appcod
				AND pdeest = 'on'
				AND apppre = artcod
				AND artpos = 'N'
				AND artest = 'on' ";
				
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		$val = true;
	}
	
	return $val;
}


/******************************************************************************************
 * Consultar por nombre o codigo del medicamento
 ******************************************************************************************/
function consultarArticulo( $conex, $wbasedato, $wcenmez, $art, $soloNoPos = false, $ignorarEstado = false ){

	$val = 'No se encontraron datos';
	
	if( !$ignorarEstado ){
		$estados = "AND artest = 'on'
					AND defest = 'on'";
	}
			
	$sql = "SELECT
				Artcod, Artcom, Artgen, Artuni, Defvia, Artpos, Deffru, Artest, Defest
			FROM
				{$wbasedato}_000026 a, {$wbasedato}_000059 b, {$wbasedato}_000011 c
			WHERE
				defart = artcod
				AND ( artcod LIKE '%$art%'
					  OR artcom LIKE '%$art%'
					  OR artgen LIKE '%$art%'
					)
				AND defcco = ccocod
				AND ccotra = 'on'
				AND ccoima = 'off'
				AND ccofac = 'on'
				$estados
			UNION
			SELECT
				Artcod, Artcom, Artgen, Artuni, Defvia, '' as Artpos, Deffru, Artest, Defest
			FROM
				{$wcenmez}_000002 a, {$wcenmez}_000001 b, {$wbasedato}_000059 c
			WHERE
				defart = artcod
				AND ( artcod LIKE '%$art%'
					  OR artcom LIKE '%$art%'
					  OR artgen LIKE '%$art%'
					)
				AND arttip = tipcod
				AND tipcdo != 'on'
				$estados
			";
	
	$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
	
		$val = '';
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
		
			if( !$soloNoPos || ( $soloNoPos &&  ( strtoupper( $rows[ 'Artpos' ] ) == 'N' || esProductoNoPOSCM( $conex, $wbasedato, $wcenmez, $rows[ 'Artcod' ] ) ) ) ){
			
				//Creo el resultado como un json
				//Primero creo un array con los valores necesarios
				$datArticulos[ 'value' ] = Array( "cod"=> $rows[ 'Artcod' ],
												  "com"=> trim( $rows[ 'Artcom' ] ),
												  "gen"=> trim( $rows[ 'Artgen' ] ),
												  "vias"=> $rows[ 'Defvia' ],
												  "uni"=> $rows[ 'Deffru' ] );	//Este es el dato a procesar en javascript
				
				$datArticulos[ 'label' ] = "{$rows[ 'Artcod' ]}-".trim( $rows[ 'Artcom' ] )."-".trim( $rows[ 'Artgen' ] );	//Este es el que ve el usuario
				$datArticulos[ 'msg' ] = "";
				
				if( $rows[ 'Artest' ] != 'on' ){
					$datArticulos[ 'msg' ] = "El articulo <b>{$rows['Artcod']}-{$rows['Artgen']}</b> se encuentra <b>INACTIVO</b> en el sistema.";
				}
				
				if( $rows[ 'Defest' ] != 'on' ){
					$datArticulos[ 'msg' ] = "La fraccion del articulo <b>{$rows['Artcod']}-{$rows['Artgen']}</b> se encuentra <b>INACTIVO</b> en el sistema.";
				}
				
				$dat = Array();
				$dat[] = $datArticulos;
				
				$val .= json_encode( $dat )."\n";
			}
		}
		
		//$val = substr( $val, 1 );
	}
	
	return $val;
}

/****************************************************************************************************
 * Consulta el codigo del protoco segun el nombre de dicho protocolo
 ****************************************************************************************************/
function consultarCodigoProtocolo( $conex, $wbasedato, $nombreProtocolo ){

	$val = false;

	$sql = "SELECT
				Procod
			FROM
				{$wbasedato}_000137
			WHERE
				Pronom = '$nombreProtocolo'
			";
			
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	$num = mysql_num_rows( $res );
	
	if( $num > 0 ){
		
		$rows = mysql_fetch_array( $res );
		
		$val = $rows[ 'Procod' ];
	}
	
	return $val;
}

/****************************************************************************************
 * Crea un detalle de de protocolo
 ****************************************************************************************/
function crearDetalleProtocolo( $conex, $wbasedato, &$wencabezados, $wdetalle ){

	$val = false;
	$arr_insertados = array();
	foreach($wencabezados as &$encabezado){
		foreach($wdetalle as $detalle){
			
			if( isset($detalle['Relest']) ) continue; //Siesta definido Relest, es detalle de medicamentosreemplazo
			
			
			$detalle['Dprpro'] = $encabezado['Procod'];	
			if( isset($encabezado['nuevo']) && $encabezado['nuevo'] == 'on' ) //Si el encabezado es nuevo, se limpia el id para que lo inserte como nuevo para este encabezado
				$detalle['id'] = "";			
	
			if( ( $detalle['Dprpro'] && $detalle['Dprpro'] != -1 ) || !empty( $detalle['id'] ) ){
				
				//Si no tiene codigo del item (codigo del articulo o del procedimiento) no permito guardar
				if( !empty( $detalle['Dprcod'] ) || !empty( $detalle['id'] ) ){
					
					if( empty( $detalle['id'] ) ){
						//Creo el string de insert para el encabezado
						$sql = crearStringInsert( "{$wbasedato}_000138", $detalle );
					}
					else{
						//Quito el codigo del protocolo, este nunca se debe mover
						unset( $detalle['Dprpro'] );
					
						//Actualizo el registro
						$sql = crearStringUpdate( "{$wbasedato}_000138", $detalle );
					}
					
					//echo "<br>---Det: ".$sql;
					$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
					
					if( mysql_affected_rows() > 0 ){
						if( empty( $detalle['id'] ) ){
							//echo $val =  mysql_insert_id();
							$detalle['id'] =  mysql_insert_id();
							array_push( $arr_insertados, $detalle['id'] );
						}
					}
				}
			}
		}
	}
	
	return $arr_insertados;
}


/****************************************************************************************
 * Crea un detalle de de protocolo para HCE
 ****************************************************************************************/
function crearDetalleProtocoloHCE( $conex, $wbasedato, &$wencabezados, $wdetalle ){

	$val = false;
	$arr_insertados = array();
	
	foreach($wencabezados as &$encabezado){
		foreach($wdetalle as $detalle){
			$detalle['Dprpro'] = $encabezado['Procod'];
			
			if( isset($encabezado['nuevo']) && $encabezado['nuevo'] == 'on' ) //Si el encabezado es nuevo, se limpia el id para que lo inserte como nuevo para este encabezado
				$detalle['id'] = "";
			
			if( ($detalle['Dprpro'] && $detalle['Dprpro'] != -1 ) || !empty($detalle['id'])  ){
				
				//Si no tiene codigo del item (codigo del articulo o del procedimiento) no permito guardar
				if( !empty($detalle['Dprcod']) || !empty($detalle['id']) ){
					
					if( empty($detalle['id']) ){
						//Creo el string de insert para el encabezado
						$sql = crearStringInsert( "{$wbasedato}_000139", $detalle );
					}else{
						//Quito el codigo del protocolo, este nunca se debe mover si es una actualizacion
						unset( $detalle['Dprpro'] );
						//Actualizo el registro
						$sql = crearStringUpdate( "{$wbasedato}_000139", $detalle );
					}					
					//echo "<br>Det--------".$sql;
					$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
					
					if( mysql_affected_rows() > 0 ){
						if( empty( $detalle[ 'id' ] ) ){
							//echo $val =  mysql_insert_id();
							$detalle['id'] =  mysql_insert_id();
							array_push( $arr_insertados, $detalle['id'] );
						}
					}
				}
			}
		}	
	}
	return $arr_insertados;
}

/****************************************************************************************
 * Valida que se pueda crear el encabezado de un protocolo
 *
 * Nota: No se permite crear encabezado si hay un nombre con el mismo encabezado o
 *		  no haya nombre escrito
 ****************************************************************************************/
function validarCrearEncabezado( $conex, $wbasedato, $wcenmez, $whce, $wencabezado, $slTipPro ){

	$val = false;
	$sltippto = $wencabezado['slTipPro'];
	
	if( substr( trim($sltippto), 0, 3 ) != 'ctc' ){
		$val = !buscarRegistroCtc( $conex, $wbasedato, $whce, $wcenmez, $wencabezado, true );
		// $val = true;
		/*if( $nombre != '' ){
			//No permito crear plantilla con un nombre ya existente
			$sql = "SELECT *
					  FROM {$wbasedato}_000137
					 WHERE pronom = '$nombre'
					   AND protip = '$tipo'
					";			
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$num = mysql_num_rows( $res );
			
			if( $num == 0 )
				$val = true;
		}*/
	}else{
		$val = buscarRegistroCtc( $conex, $wbasedato, $whce, $wcenmez, $wencabezado, true );
		
		if( $val != '' ){
			$val = false;
		}else{
			$val = true;
		}
	}
	
	return $val;
}

/************************************************************************
 * Crea el encabezado del protoclo
 ************************************************************************/
function crearEncabezadoProtocoloFRE( $conex, $wbasedato, &$wdatos ){

	$val = false;
	$arr_inserts = array();
	
	foreach($wdatos as &$encabezado){
		if( empty($encabezado['id']) ){	//Si tiene id significa que es modificacion
			$valido = validarCrearEncabezado( $conex, $wbasedato, $wcenmez, $whce, $encabezado, '' );
		}else{
			$valido = true;
		}
		
		if( $valido ){
			$slTipPro = $encabezado['slTipPro'];
			$procod = $encabezado[ 'Procod' ];
			unset( $encabezado['slTipPro'] ); //Se quita para que crearStringInsert funcione
			
			if( empty($encabezado[ 'id' ]) ){
				//Creo el codigo del protocolo, es un numero y es siempre uno mayor al ultimo encontrado
				$sql = "SELECT Procod*1 as Procod
						  FROM {$wbasedato}_000137
					  ORDER BY Procod desc";				
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				$num = mysql_num_rows( $res );
				
				//Creo el campo faltante para el codigo del protocolo
				if( $num == 0 ){
					$encabezado[ 'Procod' ] = $num+1;
				}else{
					$rows = mysql_fetch_array( $res );					
					$encabezado['Procod'] = $rows['Procod']+1;
				}
				$procod = $encabezado[ 'Procod' ];
				//Creo campo faltante, el estado del registro que siempre es on al crear el encabezado
				//$datos[ 'Proest' ] = 'on';				
				//Creo el string de insert para el encabezado
				$sql = crearStringInsert( "{$wbasedato}_000137", $encabezado );
			}else{
				//Quito el codigo del protocolo, este nunca se debe mover
				unset( $encabezado[ 'Procod' ] );
				
				$sql = crearStringUpdate( "{$wbasedato}_000137", $encabezado );
			}
			
			if( empty($encabezado['id']) ){
				$encabezado['nuevo'] = 'on'; //Indica que el encabezado se va a crear
			}
			//echo "<br>Enc----".$sql;
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			
			if( mysql_affected_rows() > 0 ){
				$val = true;
				if( empty($encabezado['id']) ){
					//echo $val =  mysql_insert_id();
					$encabezado['id'] = mysql_insert_id();
					array_push($arr_inserts, $encabezado['id']);
				}
			}			
			$encabezado['slTipPro'] = $slTipPro; //Vuelve y se pone la clave porque es necesaria más adelante
			if( $procod == '' )
				$encabezado['Procod'] = buscarRegistroEncCtcPro($conex, $wbasedato, $encabezado['id']);
			else
				$encabezado['Procod'] = $procod;
		}
		else{
			//echo "El Nombre del protocolo ya existe";
			$encabezado['id'] = -1;
		}
	}	
	return $arr_inserts;
}

/************************************************************************
 * Crea el encabezado del protoclo
 ************************************************************************/
function crearEncabezadoProtocolo( $conex, $wbasedato, $wcenmez, $whce ){

	$val = false;
	
	//Creo el array necesario para crear la consulta insert para el encabezado del protocolo
	$datos = crearArrayDatos( $wbasedato );
	
	if( empty( $datos[ 'id' ] ) ){	//Si tiene id significa que es modificacion
		$valido = validarCrearEncabezado( $conex, $wbasedato, $wcenmez, $whce, $datos[ 'Pronom' ], $datos[ 'Protip' ] );
	}
	else{
		$valido = true;
	}
	
	if( $valido ){
		
		if( empty( $datos[ 'id' ] ) ){
		
			//Creo el codigo del protocolo, es un numero y es siempre uno mayor al ultimo encontrado
			$sql = "SELECT
						Procod*1 as Procod
					FROM
						{$wbasedato}_000137
					ORDER BY
						Procod desc
					";
			
			$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
			$num = mysql_num_rows( $res );
			
			//Creo el campo faltanta pare el codigo del protoco
			if( $num == 0 ){
				$datos[ 'Procod' ] = $num+1;
			}
			else{
				$rows = mysql_fetch_array( $res );
				
				$datos[ 'Procod' ] = $rows[ 'Procod' ]+1;
			}

			//Creo campo faltante, el estado del registro que siempre es on al crear el encabezado
			//$datos[ 'Proest' ] = 'on';
			
			//Creo el string de insert para el encabezado
			$sql = crearStringInsert( "{$wbasedato}_000137", $datos );
		}
		else{
			//Quito el codigo del protocolo, este nunca se debe mover
			unset( $datos[ 'Procod' ] );
		
			$sql = crearStringUpdate( "{$wbasedato}_000137", $datos );
		}
		
		$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );

		if( mysql_affected_rows() > 0 ){
			$val = true;
			
			if( empty( $datos[ 'id' ] ) ){
				echo $val =  mysql_insert_id();
			}
		}
	}
	else{
		echo "El Nombre del protocolo ya existe";
	}
	
	return $val;
}

/************************************************************************************************
 * Crea un array de datos que hace los siguiente.
 *
 * Toma todas las variables enviadas por Post, y las convierte en un array
 *
 * Explicacion:
 * En el formulario HTML, toda variable que se quiera mandar por POST y que sirva para un proceso ajax
 * su nombre comienza con dat_. Ahora si, una variable comienza con dat_Enc, quiere decir que se va a crear
 * un encabezado de protocolo y si por el contrario comienza con dat_Det, quiere decir que se va a grabar un
 * detalle del protocolo. Estas variables son sensitivas a mayusculas y minisculas.
 * El array que se crea es el requerido para que pueda ser procesado por la funcion crearStringInsert.
 ************************************************************************************************/
function crearArrayDatos( $wbasedato ){

	$val = Array();
	
	$crearDatosExtras = false;
	
	foreach( $_POST as $keyPost => $valuePost ){

		switch( substr( $keyPost, 0,7 ) ){
			
			case 'dat_Enc':
				if( substr( $keyPost, 7, 3 ) != 'id' ){
					$val[ 'Pro'.substr( $keyPost, 7,3 ) ] = utf8_decode( $valuePost );
					$crearDatosExtras = true;
				}
				else{
					$val[ substr( $keyPost, 7, 3 ) ] = utf8_decode( $valuePost );
					$crearDatosExtras = true;
				}
			break;
			
			case 'dat_Det':
				if( substr( $keyPost, 7, 3 ) != 'id' ){
					$val[ 'Dpr'.substr( $keyPost, 7, 3 ) ] = utf8_decode( $valuePost );
					$crearDatosExtras = true;
				}
				else{
					$val[ substr( $keyPost, 7, 3 ) ] = utf8_decode( $valuePost );
					$crearDatosExtras = true;
				}
			break;
			
			case 'dat_Mre':
				if( substr( $keyPost, 7, 3 ) != 'id' ){
					$val[ 'Rel'.substr( $keyPost, 7, 3 ) ] = utf8_decode( $valuePost );
					$crearDatosExtras = true;
				}
				else{
					$val[ substr( $keyPost, 7, 3 ) ] = utf8_decode( $valuePost );
					$crearDatosExtras = true;
				}
			break;
			
			default: break;
		}
	}
	
	if( $crearDatosExtras ){
		$val[ 'Medico' ] = $wbasedato;
		$val[ 'Fecha_data' ] = date( "Y-m-d" );
		$val[ 'Hora_data' ] = date( "H:i:s" );
		$val[ 'Seguridad' ] = "C-$wbasedato";
	}
		
	return $val;
}

/***************************************************************************************
 * inserta los datos a la tabla
 *
 * $datos	Array que tiene como clave el nombre del campo y valor el valor a insertar
 * $tabla 	Nombre de la tabla a la que se va a insertar los datos
 ***************************************************************************************/
function crearStringInsert( $tabla, $datos ){
	
	$stPartInsert = "";
	$stPartValues = "";
	
	foreach( $datos as $keyDatos => $valueDatos ){
		
		$valueDatos = str_replace("<br>", "\n", $valueDatos );
		$valueDatos = str_replace("'", " ", $valueDatos );
		$stPartInsert .= ",$keyDatos";
		$stPartValues .= ",'$valueDatos'";
	}
	
	$stPartInsert = "INSERT INTO $tabla(".substr( $stPartInsert, 1 ).")";	//quito la coma inicial
	$stPartValues = " VALUES (".substr( $stPartValues, 1 ).")";
	
	return $stPartInsert.$stPartValues;
}

/***************************************************************************************
 * Crea un string que corresponde a un UPDATE valido
 *
 * $datos	Array que tiene como clave el nombre del campo y valor el valor a insertar
 * $tabla 	Nombre de la tabla a la que se va a insertar los datos
 ***************************************************************************************/
function crearStringUpdate( $tabla, $datos ){

	$stPartInsert = "";
	$stPartValues = "";
	
	//campos que no se actualizan
	$prohibidos[ "Medico" ] = true;
	$prohibidos[ "Fecha_data" ] = true;
	$prohibidos[ "Hora_data" ] = true;
	$prohibidos[ "Seguridad" ] = true;
	$prohibidos[ "id" ] = true;
	
	foreach( $datos as $keyDatos => $valueDatos ){
		$valueDatos = str_replace("<br>", "\n", $valueDatos );
		$valueDatos = str_replace('"', " ", $valueDatos );
		$valueDatos = str_replace("'", " ", $valueDatos );
		$valueDatos = str_replace("&quot;", "", $valueDatos );
		if( !isset( $prohibidos[ $keyDatos ] ) ){
			$stPartInsert .= ",$keyDatos = '$valueDatos' ";
		}
	}
	
	$stPartInsert = "UPDATE $tabla SET ".substr( $stPartInsert, 1 );	//quito la coma inicial
	$stPartValues = " WHERE id = '{$datos[ 'id' ]}'";
	
	return $stPartInsert.$stPartValues;

	//UPDATE  `matrix`.`movhos_000138` SET  `Dprest` =  'off' WHERE  `movhos_000138`.`id` =82;
}

/******************************************************************************************
 * Consultar maestro de frecuencias
 ******************************************************************************************/
function consultarMaestroCondicion( $conex, $wbasedato ){

	$sql = "SELECT *
			FROM {$wbasedato}_000042
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	return $res;
}

/******************************************************************************************
 * Consultar maestro de frecuencias
 ******************************************************************************************/
function consultarMaestroVias( $conex, $wbasedato ){

	$sql = "SELECT *
			FROM {$wbasedato}_000040
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	return $res;
}

/******************************************************************************************
 * Consultar maestro de frecuencias
 ******************************************************************************************/
function consultarMaestroFrecuencias( $conex, $wbasedato ){

	$sql = "SELECT *
			FROM {$wbasedato}_000043
			";
	
	$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
	
	return $res;
}

/********************************************************************************
 * Consulta ajax=1
 *
 * Dibuja la fila que se debe agregar para el movimiento de medicamentos
 ********************************************************************************/
function agregarFilaArticulo( $conex, $wbasedato ){

	echo "<table align='center' id='tabPesMedicamentos'>";
	echo "<tr align='center' class='encabezadotabla'>";
	echo "<td>Acciones</td>";
	echo "<td>Articulo</td>";
	echo "<td>Nombre del articulo</td>";
	echo "<td>Dosis</td>";
	echo "<td>Frecuencia</td>";
	echo "<td>Via de<br>administración</td>";
	echo "<td>Condici&oacute;n</td>";
	echo "<td>Observaciones</td>";
	echo "</tr>";
	
	echo "<tr class='fila1' style='display:none'>";
	
	//Eliminar
	echo "<td align='center'><img border='0' src='../../images/medical/root/borrar.png' onClick='desactivarFila( this );'></td>";
	
	//Codigo del articulo
	echo "<td>";
	echo "<INPUT type='text' name='dat_Detcod' style='width:70'>";
	echo "<INPUT type='hidden' name='dat_Detest' value='on'>";
	echo "<INPUT type='hidden' name='dat_Detid'>";
	echo "<INPUT type='hidden' name='dat_Detpes' value='Medicamentos'>";
	echo "</td>";
	
	//Información adicional del articulo
	echo "<td>";
	echo "</td>";
	
	//Cantidad de fraccion
	echo "<td>";
	echo "<input type='text' name='dat_Detdos' style='width:70' onKeypress='return soloEnteros(this,event)'><div></div>";
	echo "</td>";
	
	//Frecuencia
	echo "<td>";
	echo "<select name='dat_Detfre'>";
	
	$resFre = consultarMaestroFrecuencias( $conex, $wbasedato );
	echo "<option ></option>";
	while( $rows = mysql_fetch_array( $resFre ) ){
		echo "<option value='{$rows['Percod']}'>{$rows['Peruni']} - {$rows['Perequ']}</option>";
	}
	
	echo "</select>";
	echo "</td>";
	
	//Via
	echo "<td>";
	echo "<select name='dat_Detvia' style='width:180'>";
	
	$resVia = consultarMaestroVias( $conex, $wbasedato );
	echo "<option ></option>";
	while( $rows = mysql_fetch_array( $resVia ) ){
		echo "<option value='{$rows['Viacod']}'>{$rows['Viades']}</option>";
	}
	
	echo "</select>";
	echo "</td>";
	
	//Condición
	echo "<td>";
	echo "<select name='dat_Detcnd'>";
	
	$resCon = consultarMaestroCondicion( $conex, $wbasedato );
	echo "<option ></option>";
	while( $rows = mysql_fetch_array( $resCon ) ){
		echo "<option value='{$rows['Concod']}'>{$rows['Condes']}</option>";
	}
	
	echo "</select>";
	echo "</td>";
	
	//Observaciones
	echo "<td style='width:200'>";
	echo "<textarea name='dat_Detobs' cols='25' rows='3'></textarea>";
	echo "</td>";
	
	echo "</tr>";
	
	echo "</table>";
}

/************************************************************************************************
 * Pinta el contenido de la pestaña de Medicamentos para el programa de ordenes
 ************************************************************************************************/
function pintarPestanaOrdenesMed( $conex, $wbasedato ){

	/********************************************************************************
	 * Inicio de la pestaña Articulo para el programa de Ordenes
	 ********************************************************************************/
	echo "<div id='pesArticulos'>";
	agregarFilaArticulo( $conex, $wbasedato );
	echo "<br>";
	echo "<center>";
	echo "<input type='button' name='btAgregarMed' value='Agregar item' style='width:100' onClick='agregarArticulo();'/>";
	echo "</center>";
	echo "<br>";
	echo "</div>"; //Fin programa Ordnes pestaña Articulos
}

/************************************************************************************************
 * Pinta el contenido de la pestaña de CTC Articulos para el programa de ordenes
 ************************************************************************************************/
function pintarPestanaOrdenesCtcArts( $conex, $wbasedato, $whce ){

	/********************************************************************************
	 * Inicio de la pestaña ctc de articulo para el programa de Ordenes
	 ********************************************************************************/	
	echo "<div id='pesCtcArticulos'>";
	
	//Pinto la tabla que contiene la informacion a llenar para el protocolo de ctc
	echo "<table id='tabPesCtcMedicamentos' align='center'>";
	
	echo "<tr class='encabezadotabla'>";
	echo "<th style='display:none'>Eliminar</td>";
	echo "<th>Medicamento</th>";
	echo "<th>Información del medicamento</th>";
	echo "<th>Efecto terap&eacute;utico deseado al tratamiento</th>";
	echo "<th>Efectos secundarios y posibles<br>riesgos al tratamiento</th>";
	echo "<th>Tiempo de respuesta esperada</th>";
	echo "<th>Bibliograf&iacute;a</th>";
	echo "<th>Indicaciones invima</th>";
	echo "</tr>";
	
	echo "<tr class='fila1' style='display:none'>";
	
	//Eliminar
	echo "<td align='center' style='display:none'><img border='0' src='../../images/medical/root/borrar.png' onClick='desactivarFila( this );'></td>";
	
	//Codigo del articulo
	echo "<td>";
	echo "<INPUT type='text' name='dat_Detcod'>";
	echo "<INPUT type='hidden' name='dat_Detest' value='on'>";
	echo "<INPUT type='hidden' name='dat_Detid'>";
	echo "<INPUT type='hidden' name='dat_Detpes' value='CtcMedicamentos'>";
	echo "</td>";
	
	//Para poner informacion del medicamentos
	echo "<td>&nbsp;</td>";
	
	//Efecto terapeutico deseado al tratamiento
	echo "<td style='width:200'>";
	echo "<textarea name='dat_Detete' cols='25' rows='3'></textarea>";
	echo "</td>";
	
	//Efecto secundarios y posibles riesgos al tratamiento
	echo "<td style='width:200'>";
	echo "<textarea name='dat_Detese' cols='25' rows='3'></textarea>";
	echo "</td>";
	
	
	//Tiempo de respuesta esperado
	echo "<td style='width:200'>";
	echo "<textarea name='dat_Dettre' cols='25' rows='3'></textarea>";
	echo "</td>";
	
	
	//Bibliografía
	echo "<td style='width:200'>";
	echo "<textarea name='dat_Detbib' cols='25' rows='3'></textarea>";
	echo "</td>";
	
	//Bibliografía
	echo "<td style='width:200'>";
	echo "<textarea name='dat_Detiin' cols='25' rows='3'></textarea>";
	echo "</td>";
	
	echo "</tr>";
	
	echo "</table>";
	
	
	
	/************************************************************************************
	 * Aquí agrego lo necesario para los medicamentos de reemplazo
	 ************************************************************************************/
	echo "<br>";
	echo "<table align='center' class='encabezadotabla'>";
	echo "<tr>";
	echo "<td>MEDICAMENTO EN EL PLAN OBLIGATORIO DE SALUD DEL MISMO GRUPO TERAPEUTICO QUE REEMPLAZA O SUSTITUYE EL MEDICAMENTO NO POS SOLICITADO</td>";
	echo "</tr>";
	echo "</table>";
	
	
	echo "<table align='center' id='tabCTCReemplazp'>";
	
	echo "<tr class='encabezadotabla' align='center'>";	
	echo "<td>Medicamento POS</td>";
	echo "<td>Nombre gen&eacute;rico</td>";
	echo "<td>Posolog&iacute;a</td>";
	echo "<td>Dosis por día</td>";
	echo "<td>Cantidad</td>";	
	echo "<td>D&iacute;as de<br>tratamiento</td>";
	echo "<td>Acciones</td>";
	echo "</tr>";
	
	echo "<tr class='fila1' style='display:none'>";
	
	//Articulo
	echo "<td>";
	echo "<INPUT type='text' name='dat_Mreart'>";
	echo "<INPUT type='hidden' name='dat_Mreest' value='on'>";
	echo "<INPUT type='hidden' name='dat_Mreid'>";
	echo "<INPUT type='hidden' name='dat_Mrepre'>";
	echo "</td>";
	
	//Vacio
	echo "<td style='display:none'></td>";
	
	//Nombre genérico
	echo "<td>";
	echo "<div></div>";
	echo "</td>";
	
	//Posología
	echo "<td>";
	echo "<INPUT type='text' name='dat_Mrepos'><div></div>";
	echo "</td>";
	
	//Dosis por día
	echo "<td>";
	echo "<INPUT type='text' name='dat_Mreddi'>";
	echo "</td>";
	
	//Cantidad
	echo "<td>";
	echo "<INPUT type='text' name='dat_Mrecan'>";
	echo "</td>";
	
	//Días de Tratamiento
	echo "<td>";
	echo "<INPUT type='text' name='dat_Mretto'>";
	echo "</td>";
	
	//Acciones
	echo "<td align='center'>";
	echo "<img border='0' src='../../images/medical/root/borrar.png' onClick='eliminarArtReemplazo( this );'>";
	echo "</td>";
	
	echo "</tr>";
	
	echo "</table>";
	/************************************************************************************/
	
	
	
	
	echo "</div>"; //Fin de la pestaña ctc articulos
}

function pintarPestanaOrdenesProc( $conex, $wbasedato, $whce ){

	/********************************************************************************
	 * Inicio de la pestaña ctc de articulo para el programa de Ordenes
	 ********************************************************************************/		
	echo "<div id='pesProcedimientos'>";
	
	//Pinto la tabla que contiene la informacion a llenar para el protocolo de ctc
	echo "<table id='tabPesProcedimientos' align='center'>";
	
	echo "<tr class='encabezadotabla'>";
	echo "<th>Eliminar</td>";
	echo "<th>Procedimiento</th>";
	echo "<th>Nombre del procedimiento</th>";
	echo "<th>Justificaci&oacute;n</th>";
	echo "<th>Opciones POS(Si)</th>";
	echo "<th>Opciones POS(No)</th>";
	echo "</tr>";
	
	echo "<tr class='fila1' style='display:none'>";
	
	//Eliminar
	echo "<td align='center'><img border='0' src='../../images/medical/root/borrar.png' onClick='desactivarFila( this );'></td>";
	
	//Codigo del articulo
	echo "<td>";
	echo "<INPUT type='text' name='dat_Detcod'>";
	echo "<INPUT type='hidden' name='dat_Detest' value='on'>";
	echo "<INPUT type='hidden' name='dat_Detid'>";
	echo "<INPUT type='hidden' name='dat_Detpes' value='Procedimientos'>";
	echo "</td>";
	
	//Para poner informacion del medicamentos
	echo "<td>&nbsp;</td>";
	
	//Efecto terapeutico deseado al tratamiento
	echo "<td style='width:200'>";
	echo "<textarea name='dat_Detjus' cols='25' rows='3'></textarea>";
	echo "</td>";	
	
	echo "<td style='width:200'>";
	echo "<textarea name='dat_Detops' cols='25' rows='3'></textarea>";
	echo "</td>";
	
	echo "<td style='width:200'>";
	echo "<textarea name='dat_Detopn' cols='25' rows='3'></textarea>";
	echo "</td>";
	
	echo "</tr>";
	
	echo "</table>";
	
	//Boton de agregar tabla
	echo "<br>";
	echo "<center>";
	echo "<input type='button' name='btAgregarMed' value='Agregar item' style='width:100' onClick='agregarProcedimiento();'/>";
	echo "</center>";
	echo "<br>";
	
	echo "</div>";	//Fin pestaña ctc de articulo
}

function pintarPestanaOrdenesCtcProcs( $conex, $wbasedato, $whce ){

	/********************************************************************************
	 * Inicio de la pestaña ctc de articulo para el programa de Ordenes
	 ********************************************************************************/		
	echo "<div id='pesCtcProcedimientos'>";
	
	//Pinto la tabla que contiene la informacion a llenar para el protocolo de ctc
	echo "<table id='tabPesCtcProcedimientos' align='center'>";
	
	echo "<tr class='encabezadotabla'>";
	echo "<th style='display:none'>Eliminar</td>";
	echo "<th>Procedimiento</th>";
	echo "<th>Nombre del procedimiento</th>";
	echo "<th>Justificaci&oacute;n</th>";
	echo "<th>Opciones POS(Si)</th>";
	echo "<th>Opciones POS(No)</th>";
	echo "</tr>";
	
	echo "<tr class='fila1' style='display:none'>";
	
	//Eliminar
	echo "<td align='center' style='display:none'><img border='0' src='../../images/medical/root/borrar.png' onClick='desactivarFila( this );'></td>";
	
	//Codigo del articulo
	echo "<td>";
	echo "<INPUT type='text' name='dat_Detcod'>";
	echo "<INPUT type='hidden' name='dat_Detest' value='on'>";
	echo "<INPUT type='hidden' name='dat_Detid'>";
	echo "<INPUT type='hidden' name='dat_Detpes' value='CtcProcedimientos'>";
	echo "</td>";
	
	//Para poner informacion del medicamentos
	echo "<td>&nbsp;</td>";
	
	//Efecto terapeutico deseado al tratamiento
	echo "<td style='width:200'>";
	echo "<textarea name='dat_Detjus' cols='25' rows='3'></textarea>";
	echo "</td>";	
	
	echo "<td style='width:200'>";
	echo "<textarea name='dat_Detops' cols='25' rows='3'></textarea>";
	echo "</td>";
	
	echo "<td style='width:200'>";
	echo "<textarea name='dat_Detopn' cols='25' rows='3'></textarea>";
	echo "</td>";
	
	echo "</tr>";
	
	echo "</table>";
	
	//Boton de agregar tabla
	echo "<br>";
	// echo "<center>";
	// echo "<input type='button' name='btAgregarMed' value='Agregar' style='width:100' onClick='agregarCtcProcedimiento();'/>";
	// echo "</center>";
	echo "<br>";
	
	echo "</div>";	//Fin pestaña ctc de articulo
}

function procesarGuardar($conex, $wbasedato, $wdatos, $wnombre, $wtipo){

	$wdatos = str_replace("\\t", "<br>", $wdatos); //Se reemplaza por <br> para que al crear el insert sera cambiado por \n y haga el salto de linea.
	$wdatos = str_replace("\\n", "<br>", $wdatos); //Se reemplaza por <br> para que al crear el insert sera cambiado por \n y haga el salto de linea.
	$wdatos = str_replace("\\", "", $wdatos);
	$wdatos = str_replace("\"[", "[", $wdatos);
	$wdatos = str_replace("]\"", "]", $wdatos);			
	$wdatos = json_decode( $wdatos, true );
	
	//echo "Guardar el protocolo: ".$wtipo." - ".$wnombre;
	
	//echo "<br>ENCABEZADOS:<BR>";	
	//Agregar valores faltantes y reemplazar las claves por nombres que correspondan a los campos de las tablas.
	$arr_aux = array();
	$in = 0;
	foreach($wdatos['encabezados'] as &$encabezado){
		$encabezado[ 'Medico' ] = $wbasedato;
		$encabezado[ 'Fecha_data' ] = date( "Y-m-d" );
		$encabezado[ 'Hora_data' ] = date( "H:i:s" );
		$encabezado[ 'Seguridad' ] = "C-$wbasedato";
		
		foreach($encabezado as $claveEnc=>$valEnc ){
		
			$claveEnc = str_replace("dat_Enc", "Pro", $claveEnc);			
			if( $claveEnc == "Proid" ) $claveEnc = "id";			
			$arr_aux[$in][ $claveEnc ] = $valEnc;
			//echo "<br>".$claveEnc." => ".$valEnc;
		}
		$in++;
	}
		
	//Se recorre de  nuevo el array para que la posicion Pronom sea igual al Procodigo, esto para los CtcProcedimientos y CtcMedicamentos (ctcArts), luego se elimina
	//del arreglo la posicion Procodigo.
		
	foreach($arr_aux as $key => $value){
	
		if($key == "slTipPro" and ($arr_aux[$key]["slTipPro"] == "ctcProcs" or $arr_aux[$key]['slTipPro'] == "ctcArts"))	{		
			
			$arr_aux[$key]['Pronom'] = $arr_aux[$key]['Procodigo'];
			
		}
		
		unset($arr_aux[$key]['Procodigo']);
	}
	
	$wdatos['encabezados'] = $arr_aux;
	
	$arr_inserts_enc = crearEncabezadoProtocoloFRE( $conex, $wbasedato, $wdatos['encabezados'] );
	
		
	//echo "<br>DETALLE:<BR>";
	//Agregar valores faltantes y reemplazar las claves por nombres que correspondan a los campos de las tablas.
	$arr_aux = array();
	$in = 0;
	foreach($wdatos['detalle'] as $detalle){
		//echo "<br>-----------------";
		if( (isset($detalle['dat_Detcod']) && $detalle['dat_Detcod'] != '') || (isset($detalle['dat_Mreart']) && ( $detalle['dat_Mreart'] != '' || $detalle['dat_Mreid'] != '' ) ) ){
			$detalle[ 'Medico' ] = $wbasedato;
			$detalle[ 'Fecha_data' ] = date( "Y-m-d" );
			$detalle[ 'Hora_data' ] = date( "H:i:s" );
			$detalle[ 'Seguridad' ] = "C-$wbasedato";
			foreach($detalle as $claveDet=>$valDet ){
				$claveDet = str_replace("dat_Det", "Dpr", $claveDet);
				$claveDet = str_replace("dat_Mre", "Rel", $claveDet);
				if( $claveDet == "Dprid" || $claveDet == "Relid" ) $claveDet = "id";
				$arr_aux[$in][ $claveDet ] = utf8_decode($valDet);
				//echo "<br>".$claveDet." => ".$valDet;
			}
			$in++;
		}
	}
	$wdatos['detalle'] = $arr_aux;
	
	if( $wtipo == "HCE" ){
		$arr_inserts_det = crearDetalleProtocoloHCE( $conex, $wbasedato, $wdatos['encabezados'], $wdatos['detalle'] );
	}else{
		$arr_inserts_det = crearDetalleProtocolo($conex, $wbasedato, $wdatos['encabezados'], $wdatos['detalle'] );
		$arr_inserts_detm = crearDetalleMedicamentosReemplazo($conex, $wbasedato, $wdatos['encabezados'], $wdatos['detalle'] );
	}
	
	// echo "....<pre>"; var_dump( $wdatos[ 'encabezados' ] ); echo "</pre>";
	
	//Busco que encabezados no fueron guardados
	$errors = array();
	foreach( $wdatos[ 'encabezados' ] as $key => $value ){
		
		if( true || $value['id'] == -1 ){
			$errors[] = $value['id'];
		}
	}
	
	if( count($errors) > 0 ){
		echo json_encode( $errors );
	}
}

/**************************************************************************************************************
 *											FIN DE FUNCIONES
 **************************************************************************************************************/

include_once( "conex.php" );

if( !empty( $ajax ) ){	

	mysql_select_db( "matrix" );

	switch( $ajax ){
		
		case 1:
			//crearEncabezadoProtocolo( $conex, $wbasedato, $wcenmez, $whce, $wdatos );
			//2014-01-27: Se recibe un json con todos los encabezados y todo el detalle. Se procede a guardarlo.
			procesarGuardar($conex, $wbasedato, $_REQUEST['wdatos'], $_REQUEST['wnombre'], $_REQUEST['wtipo'] );			
		break;
		
		case 2:				
			crearDetalleProtocolo( $conex, $wbasedato );
		break;
		
		case 3:
		
			if( !isset( $q ) ){
				if(isset($_GET)){
					$q = strtolower($_GET["q"]);
				}else{
					$q = strtolower($HTTP_GET_VARS["q"]);
				}
			}
		
			$ar = consultarArticulo( $conex, $wbasedato, $wcenmez, $q );
			
			echo $ar;
		break;
		
		case 4:
			if( !isset( $q ) ){
				if(isset($_GET)){
					$q = strtolower($_GET["q"]);
				}else{
					$q = strtolower($HTTP_GET_VARS["q"]);
				}
			}
		
			$json = consultarArticulo( $conex, $wbasedato, $wcenmez, $q, true );
			
			echo $json;
		break;
		
		case 5:
		
			if( !isset( $q ) ){
				if(isset($_GET)){
					$q = strtolower($_GET["q"]);
				}else{
					$q = strtolower($HTTP_GET_VARS["q"]);
				}
			}
			
			$json = consultarProcedimiento( $conex, $wbasedato, $whce, $q );
			
			echo $json;
		break;
		
		case 6:
		
			if( !isset( $q ) ){
				if(isset($_GET)){
					$q = strtolower($_GET["q"]);
				}else{
					$q = strtolower($HTTP_GET_VARS["q"]);
				}
			}
		
			$json = consultarProcedimiento( $conex, $wbasedato, $whce, $q, true );
			
			echo $json;
		break;
		
		case 7:
		
			if( !isset( $q ) ){
				if(isset($_GET)){
					$q = strtolower($_GET["q"]);
				}else{
					$q = strtolower($HTTP_GET_VARS["q"]);
				}
			}
			
			$json = consultarCcos( $conex, $wbasedato, $q );
			
			echo $json;
		break;
		
		case 8:
		
			if( !isset( $q ) ){
				if(isset($_GET)){
					$q = strtolower($_GET["q"]);
				}else{
					$q = strtolower($HTTP_GET_VARS["q"]);
				}
			}
			
			$json = consultarDiagnosticos( $conex, $q, false );
			
			echo $json;
		break;
		
		case 9:
		
			if( !isset( $q ) ){
				if(isset($_GET)){
					$q = strtolower($_GET["q"]);
				}else{
					$q = strtolower($HTTP_GET_VARS["q"]);
				}
			}
		
			$json = consultarMedico( $conex, $wbasedato, $q, false );
			
			echo $json;
		break;
		
		case 10:		
			if( !isset( $q ) ){
				if(isset($_GET)){
					$q = strtolower($_GET["q"]);
				}else{
					$q = strtolower($HTTP_GET_VARS["q"]);
				}
			}		
			//echo consultarProtocolo( $conex, $wbasedato, $whce, $wcenmez, $protocolo);
			echo consultarProtocoloID( $conex, $wbasedato, $whce, $wcenmez, $protocolo);
		break;
		
		case 11:
		
			if( !isset( $q ) ){
				if(isset($_GET)){
					$q = strtolower($_GET["q"]);
				}else{
					$q = strtolower($HTTP_GET_VARS["q"]);
				}
			}
			
			$json = consultarFormulariosHCE( $conex, $whce, $q );
			
			echo $json;
		break;
		
		case 12:
			crearDetalleProtocoloHCE( $conex, $wbasedato );
		break;
		
		case 13:
		
			if( !isset( $q ) ){
				if(isset($_GET)){
					$q = strtolower($_GET["q"]);
				}else{
					$q = strtolower($HTTP_GET_VARS["q"]);
				}
			}
		
			echo consultarBuscadorProtocolo( $conex, $wbasedato, "hce", $q );
		break;
		
		case 14:
			echo buscarRegistroCtc(  $conex, $wbasedato, $whce, $wcenmez );
		break;
		
		case 15:
			echo @consultarBuscadorEncProtocolo( $conex, $wbasedato, $whce, $dat_Encnom, $dat_Encmed, $dat_Encesp, $dat_Encdia, $dat_Enctra, $dat_Enccco, $slTipPro, $dat_Enccio, $dat_Encped, $dat_Encrec );
		break;
		
		case 16:
		
			if( !isset( $q ) ){
				if(isset($_GET)){
					$q = strtolower($_GET["q"]);
				}else{
					$q = strtolower($HTTP_GET_VARS["q"]);
				}
			}
		
			$json = consultarDiagnosticosCIO( $conex, $basedatos, $q, false );
			
			echo $json;
			
		break;
		
		case 17:
		
			crearDetalleMedicamentosReemplazo( $conex, $wbasedato );
		break;
		
		case 18:
		
			if( !isset( $q ) ){
				if(isset($_GET)){
					$q = strtolower($_GET["q"]);
				}else{
					$q = strtolower($HTTP_GET_VARS["q"]);
				}
			}
		
			echo consultarArticuloPOS( $conex, $wbasedato, $wcenmez, $q );
		break;

		case 19:
		
			if( !isset( $q ) ){
				if(isset($_GET)){
					$q = strtolower($_GET["q"]);
				}else{
					$q = strtolower($HTTP_GET_VARS["q"]);
				}
			}
			
			$json = consultarTratamientos( $conex, $wbasedato, $q, false );
			
			echo $json;
		break;
		
		case 20:
			consultarValorDeCampo( $conex, $whce, $wtabla, $wcampo );		
		break;		
		
		case 21:
			eliminarEncabezadoProtocolo( $conex, $wbasedato, $id );
		break;
		
		default: break;
	}
}
else{
	?>
	<html>
	<head>
	<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
	<title>PROTOCOLOS</title>
	
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
	<script src="../../../include/root/jquery.tooltip.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript" ></script>
	
	<link type="text/css" href="../../../include/root/jquery.autocomplete.css" rel="stylesheet" /> <!-- todos los autocomplete estan con esta version -->
	<script type='text/javascript' src='../../../include/root/jquery.autocomplete.js'></script> <!-- todos los autocomplete estan con esta version -->
	<script type="text/javascript" src="protocolosOrdenesHCE.js"></script>
	<script src="../../../include/root/toJson.js" type="text/javascript"></script>
	<style>
		/* CORRECCION DE BUG PARA EL DATEPICKER Y CONFIGURACION DEL TAMAÑO  */
		.ui-datepicker {font-size:12px;}
		/* IE6 IFRAME FIX (taken FROM datepicker 1.5.3 */
		.ui-datepicker-cover {
			display: none; /*sorry for IE5*/
			display/**/: block; /*sorry for IE5*/
			position: absolute; /*must have*/
			z-index: -1; /*must have*/
			filter: mask(); /*must have*/
			top: -4px; /*must have*/
			left: -4px; /*must have*/
			width: 200px; /*must have*/
			height: 200px; /*must have*/
		}
	</style>
	</head><body>
	<?php
	include_once( "root/comun.php" );
	
	mysql_select_db( "matrix" );
	
	if( false ){
	
	
		
	
	
	}
	else{
		$wactualiz = "Julio 13 de 2015";
		encabezado("PROTOCOLOS PARA ORDENES Y HCE",$wactualiz, "clinica");
		
		$wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "movhos" );
		$whce = consultarAliasPorAplicacion( $conex, $wemp_pmla, "hce" );
		$wcenmez = consultarAliasPorAplicacion( $conex, $wemp_pmla, "cenmez" );
		
		echo "<form>";
		
		echo "<INPUT type='hidden' id='wemp_pmla' value='$wemp_pmla'>";
		echo "<INPUT type='hidden' id='wbasedato' value='$wbasedato'>";
		echo "<INPUT type='hidden' id='wcenmez' value='$wcenmez'>";
		echo "<INPUT type='hidden' id='whce' value='$whce'>";
		echo "<INPUT type='hidden' id='ordenes' value='ordenes'>";
		
		//Para cargar un protocolo
		echo "<INPUT type='text' id='idCargarProtocolos' style='display:none'>";
		echo "<INPUT type='button' onClick='cargarProtocolos()' value='Cargar protocolo' style='display:none'>";
		// echo "<INPUT type='button' id='btNuevoProtocolo' onClick='nuevoPrgOrdenes()' value='Nuevo'>";
		
		/*echo "<table id='tablatabs'>";
		echo "<tr class='fila2' align='center'><td style='font-size:10pt;width:180px;height:28'>";
		echo "<a href='#' onClick='moBuscador()' id='btBuscador'>Buscar protocolos</a>";
		echo "</td>";
		echo "<td style='font-size:10pt;width:100px' id='btNuevoProtocolo' class='fila1'>";
		echo "<a href='#' onClick='nuevoPrgOrdenes()'>Nuevo</a>";
		echo "</td></tr>";
		echo "</table>";*/
		echo "<div id='tabsprincipal'>";
		echo "<ul>
				<li><a href='#dvEditorProtocolos'>EDITOR</a></li>
				<li><a href='#bcProtocolosAvanzado'>BUSCADOR</a></li>
			  </ul>";		
		
		//Buscador de protocolos avanzado
		buscadorProtocolosAvanzado();		//Aqui se encuentra el div con el contenido de la pestaña "bcProtocolosAvanzado"
		
		echo "<div id='dvEditorProtocolos'>";	

		echo "<div id='encProTipDes'>"; //Este div contiene el tipo y la descripcion de los protocolos
		echo "<center><div id='dvMsg' class='bordeCurvo fondoAmarillo' style='width: 90%;display:none;border: 1px solid #2A5DB0;padding: 5px;' align='center'></div></center><br>";
		echo "<table align='center'>";
		
		echo "<tr class='encabezadotabla' align=center align='center'>";		
		echo "<th colspan='2' style='font-size:14pt'>";
		echo "TIPO Y DESCRIPCION DEL PROTOCOLO";
		echo "</th>";
		echo "</tr>";
		
		echo "<tr class='encabezadotabla' align=center>";
		echo "<th>Tipo</th>";
		echo "<th>Nombre</th>";		
		echo "</tr>";
		
		echo "<tr class='fila1'>";		
		echo "<td>";
		echo "<SELECT name='slTipPro' onChange='setBuscadorPorTipoProtocolo( this )'>";
		echo "<option></option>";
		echo "<option value='prOrdenes'>Ordenes</option>";
		echo "<option value='prHCE'>HCE</option>";
		echo "<option value='ctcArts'>Ctc Articulos</option>";
		echo "<option value='ctcProcs'>Ctc Procedimiento</option>";
		echo "</SELECT>";
		echo "</td>";
		
		echo "<td>";
		echo "<input name='dat_Encnom' type='text' style='width:350px'/>";		
		echo "</td>";
		echo "</tr>";
		echo "</table>";		
		echo "</div>";
		
		echo "<div id='encProtocolo'>";	//Este div contiene el encabezado de los protocolos
		
		//Doy la elección de elegir el nombre del protocolo
		echo "<br>";
		echo "<div><input type='button' style='float:right' value='+' onclick='agregarFilaEnc()' /></div>";
		echo "<table align='center' id='tabla_editor'>";
		
		echo "<tr class='encabezadotabla' align=center align='center'>";		
		echo "<th colspan='9' style='font-size:14pt'>";
		echo "EDITOR DE PROTOCOLOS";
		echo "</th>";
		echo "</tr>";
		
		echo "<tr class='encabezadotabla' align='center'>";
		//echo "<th>Tipo de protocolo</th>";
		//echo "<th>Nombre de protocolo</th>";
		echo "<th>Especialista de<br>la salud</th>";
		echo "<th>Especialidad</th>";
		echo "<th>Diagn&oacute;stico</th>";
		echo "<th>Tratamiento</th>";
		echo "<th style='display:none'>Procedimiento o<br>examen</th>";
		echo "<th>Cco</th>";
		echo "<th>CIE-O</th>";
		echo "<th>Pedi&aacute;trico</th>";
		echo "<th>Reca&iacute;da</th>";
		echo "<th>No dispensable</th>";
		echo "<th>&nbsp;</th>";
		echo "</tr>";
		
		echo "<tr class='fila1 filaEnc'>";		
		/*echo "<td>";
		echo "<SELECT name='slTipPro' onChange='setBuscadorPorTipoProtocolo( this )'>";
		echo "<option></option>";
		echo "<option value='prOrdenes'>Ordenes</option>";
		echo "<option value='prHCE'>HCE</option>";
		echo "<option value='ctcArts'>Ctc Articulos</option>";
		echo "<option value='ctcProcs'>Ctc Procedimiento</option>";
		echo "</SELECT>";
		echo "</td>";*/
		
		echo "<td style='display:none'>";
		//echo "<input name='dat_Encnom' type='text' style='width:100%'/>";
		echo "<input name='dat_Encest' type='hidden' value='on'/>";
		echo "<input name='dat_Encid' type='hidden'/>";
		echo "</td>";
		
		//Medico
		echo "<td>";
		echo "<INPUT type='checkbox' onClick='setTodosMed(this)'>";
		echo "<input name='dat_Encmed' type='text' style='width:70' onChange='alCambiar( this );'/><div></div>";
		echo "</td>";
		
		//Consulto las diferentes especialidades
		echo "<td>";
		
		//Consulto los diferentes cco
		$sql = "SELECT
					Espcod, Espnom
				FROM
					{$wbasedato}_000044 a
				ORDER BY
					Espnom
				";
				
		$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		
		echo "<SELECT name='dat_Encesp' type='text' onChange='alCambiar( this );' onBlur='guardarUltimoValue( this )'/>";
		echo "<option></option>";
		echo "<option value='*'>Todos</option>";
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			echo "<option value='{$rows[ 'Espcod' ]}'>{$rows[ 'Espcod' ]}-{$rows[ 'Espnom' ]}</option>";
		}
		
		echo "</SELECT>";
		
		echo "</td>";
		
		//Diagnosticos
		echo "<td>";
		echo "<INPUT type='checkbox' onClick='setTodosEsp(this)'>";
		echo "<input name='dat_Encdia' type='text' style='width:100' onChange='alCambiar( this );'/><div></div>";
		echo "<input type='hidden' name='dat_Enccodigo' id='dat_Enccodigo'  value='' />";
		echo "</td>";
		
		//Tratamientos
		echo "<td>";
		echo "<INPUT type='checkbox' onClick='setTodosTra(this)'>";
		echo "<input name='dat_Enctra' type='text' style='width:100' onChange='alCambiar( this );'/><div></div>";
		echo "</td>";

		//Pcedimientos
		// echo "<td  style='display:none'>";
		// echo "<input name='dat_Encpro' type='text' /><div></div>";
		// echo "</td>";
		
		//Mostrando los cco
		echo "<td>";

		//Consulto los diferentes cco
		$sql = "SELECT Ccocod, Cconom
				  FROM {$wbasedato}_000011 c
				 WHERE Ccoest = 'on'
				   AND ccohos = 'on'
				   OR Ccourg = 'on'
				 UNION
				SELECT Ccocod, Cconom
				  FROM {$wbasedato}_000011 c
				 WHERE Ccoest = 'on'
				   AND ccoayu = 'on'";				
		$res = mysql_query( $sql, $conex )  or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
		$num = mysql_num_rows( $res );
		
		
		echo "<SELECT name='dat_Enccco' onChange='alCambiar( this );' onBlur='guardarUltimoValue( this )'/>";
		echo "<option></option>";
		echo "<option value='*'>Todos</option>";
		
		for( $i = 0; $rows = mysql_fetch_array( $res ); $i++ ){
			echo "<option value='{$rows[ 'Ccocod' ]}'>{$rows[ 'Ccocod' ]}-{$rows[ 'Cconom' ]}</option>";
		}
		echo "</select>";
		echo "</td>";
		
		//CIE-O
		echo "<td>";
		// echo "<SELECT name='dat_Enccio' onChange='alCambiar( this );'/>";
		// echo "<option></option>";
		// echo "<option value='*'>Todos</option>";
		// echo "</SELECT>";
		echo "<INPUT type='checkbox' onClick='setTodosCcio(this)'>";
		echo "<input name='dat_Enccio' type='text' style='width:100' onChange='alCambiar( this );'/><div></div>";
		echo "</td>";
		
		//Pediátrico
		echo "<td align='center'>";
		echo "<INPUT type='checkbox' name='dat_Encped' onClick='alCambiar( this );'>";
		echo "</td>";		
		
		//Recaída
		echo "<td align='center'>";
		echo "<INPUT type='checkbox' name='dat_Encrec' onClick='alCambiar( this );'>";
		echo "</td>";
		
		//No dispensable
		echo "<td align='center'>";
		echo "<INPUT type='checkbox' name='dat_Encndi' onClick='alCambiar( this );'>";
		echo "</td>";
		
		//Eliminar fila
		echo "<td align='center'>";
		echo "<INPUT type='button' value='Eliminar' onClick='eliminarEnc( this );'>";
		echo "</td>";
		
		echo "</tr>";
		
		echo "</table>";
			
		echo '<center>';
		echo "<br><a class='enlace_retornar' onclick='nuevoPrgOrdenes()' href='#' >RETORNAR</a>";
		echo "</center>";
		
		echo "<input type='hidden' name='dat_Enctip' value='Ordenes' />";
		
		echo "</div>";	//Fin del contenedor de encProtocolos
		
		echo "<div id='programasGenerales'>";

		/************************************************************************
		 * Creando los tabs de programas
		 ************************************************************************/
		echo "<ul>";
		echo "<li id='prOrdenes' style='display:none'>";
		echo "<a href='#prgOrdenes'>Ordenes</a>";
		echo "</li>";
		echo "<li id='prHCE' style='display:none'>";
		echo "<a href='#prgHCE'>HCE</a>";
		echo "</li>";
		echo "<li id='ctcArts' style='display:none'>";
		echo "<a href='#pesCtcArticulos'>Ctc Articulos</a></li>";
		echo "<li id='ctcProcs' style='display:none'>";
		echo "<a href='#pesCtcProcedimientos'>Ctc procedimientos</a></li>";
		echo "</ul>";
		/************************************************************************/

		//div que contiene el programa de protocolos para Ordenes, pestaña articulos
		echo "<div id='prgOrdenes'>";

		echo "<br>";
		

		//Creo los diferentes tabs para las pestañas de articulos
		echo "<ul>";
		echo "<li>";
		echo "<a href='#pesArticulos'>Articulos</a></li>";		
		echo "<li>";
		echo "<a href='#pesProcedimientos'>Procedimientos</a></li>";
		echo "</ul>";
		
		//Pinto el contenido de la pestana CTC Ordenes
		pintarPestanaOrdenesMed( $conex, $wbasedato );
		
		//Pinto el contenido de la pestaña Procedimientos del programa de ordenes
		pintarPestanaOrdenesProc( $conex, $wbasedato, $whce );
		
		echo "</div>";	//Fin div de programa de articulos
		
		//Pinto la pestaña de Procedimientos para el programa de ordenes
		pintarPestanaOrdenesCtcProcs( $conex, $wbasedato, $whce );
		
		//Pinto el contenido de la pestana CTC Ordenes		
		pintarPestanaOrdenesCtcArts( $conex, $wbasedato, $whce );

		/********************************************************************************
		 * Este es el div que contiene los protocolos de HCE
		 ********************************************************************************/
		echo "<div id='prgHCE'>";
		
		echo "<br>";
		
		pintarPrgHCE( $conex, $wbasedato, $whce, $wcenmez );
		
		echo "</div>";	//Fin del programa de protocolos de HCE
		
		//Boton de guardar
		echo "<center><input type='button' value='Guardar' style='width:100' onClick='guardarProtocoloOrdenes();'/></center>";
		
		echo "</div>";	//Fin de div con id=programasGenerales
		
		/*echo '<center>';
		echo "<br><a class='enlace_retornar' onclick='nuevoPrgOrdenes()' href='#' >RETORNAR</a>";
		echo "</center>";	*/	
		
		echo "</div>"; //Fin dvEditorProtocolos
		
		echo "</div>"; //Fin tabs principal
		
		
		//Mensaje de cargando protocolo
		echo "<div id='msgCargaProtocolo' style='display:none'>";
		echo "<b>Cargando Protocolo.....</b>";
		echo "</div>";
		
		//Mensaje de guardando protocolo
		echo "<div id='msgGuardaProtocolo' style='display:none'>";
		echo "Guardando Protocolo.....";
		echo "</div>";
		echo "<br>";
		echo "<br>";
		echo "<center><INPUT type='button' value='Cerrar' style='width:100' onClick='cerrarVentana();'/></center>";
		
		//echo "<center><input type='button' value='cargarCTC' style='width:100' onClick='validarDatosCTC();'/></center>";
		
		echo "</form>";
	}
	?>
	</body>
	<?php
}
?>
