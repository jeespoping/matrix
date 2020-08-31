<?php
include_once("conex.php");
/**
 PROGRAMA                   : pruebas_ws_cerrar_mes_mantenimiento.php
 AUTOR                      : Edwar Jaramillo.
 FECHA CREACION             : 11 Julio de 2013

 DESCRIPCION: .

 */ $wactualiz = "(Julio 12 de 2018)"; /*
 ACTUALIZACIONES:

 *  Julio 12 de 2018
    Jonatan Lopez    : Fecha de la creación del cliente WS.
	Interfaz que permite seleccionar un año y mes para que sean cargadas las tablas manto_000001 (Encabezado mov. mantenimiento) y manto_000002 (Detalle movimientos mantenimiento), los cuales tienen origen en el servicio web ubicado 
	en http://132.1.18.15/amservice/AMWebService.asmx?WSDL&op= y que se encuentra en el script incluido ws_cliente_mantenimiento.php
**/





include_once("root/comun.php");
include_once("./ws_cliente_mantenimiento.php");
$wbasedato = consultarAliasPorAplicacion($conex, $wemp_pmla, 'mantenimiento');

if(!isset($ano) and !isset($mes)){

	encabezado("<div class='titulopagina2'>Servicio web mantenimiento</div>", $wactualiza, "clinica");

	$year = date("Y");
	 for ($i=2018;$i<=2030;$i++) { 

		$datos_anos .= '<option VALUE="'.$i.'">'.$i.'</option>';

		 }


	echo '<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>';

	echo '<div align="center">
			<form action="cargar_movimientos_mantenimiento.php" name="cargar_movimientos_mantenimiento">
			<input type="hidden" value="'.$wemp_pmla.'" name="wemp_pmla">
			<input type="hidden" value="'.date("m").'" id="mes_actual">
			  <table style="text-align: left; width: 225px; height: 88px;" border="0" cellpadding="2" cellspacing="2">
				<tbody>
				  <tr>
					<td style="width: 67px;" class="encabezadoTabla">A&ntilde;o:</td>
					<td style="width: 138px;" class="fila2"><select name="ano">'.$datos_anos.'</select></td>
				  </tr>
				  <tr>
					<td style="width: 67px;" class="encabezadoTabla">Mes:</td>
					<td style="width: 138px;" class="fila2">
					<select id="mes" name="mes">
						<option value="01">Enero</option>
						<option value="02">Febrero</option>
						<option value="03">Marzo</option>
						<option value="04">Abril</option>
						<option value="05">Mayo</option>
						<option value="06">Junio</option>
						<option value="07">Julio</option>
						<option value="08">Agosto</option>
						<option value="09">Septiembre</option>
						<option value="10">Octubre</option>
						<option value="11">Noviembre</option>
						<option value="12">Diciembre</option>
					</select></td>
				  </tr>
				  <tr>
					<td style="width: 138px;" colspan="2" rowspan="1" align="center"><input type="submit" value="Generar"></td>
				  </tr>
				</tbody>
			  </table>
			  <br>
			</form>
		</div>';

		echo "
		<table align='center'>
			<tr><td align='center' colspan='9'><input type='button' value='Cerrar Ventana' onclick='window.close();'></td></tr>
		</table>";

		echo '<script type="text/javascript">
		

			$(document).ready(function()
			{
				var mes_actual = $("#mes_actual").val();
				$("#mes").val(mes_actual);			
			});
			</script>
		
		';

}
			
if(isset($ano) and isset($mes)){
	
	generarCierreMantenimiento($conex, $wbasedato, $ano, $mes, $desde, $hasta);

}


?>