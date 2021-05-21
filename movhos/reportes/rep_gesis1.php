<?php
if(!isset($_SESSION['user']))
{
    echo '  <div style="color: #676767;font-family: verdana;background-color: #E4E4E4;" >
                [?] Usuario no autenticado en el sistema.<br />Recargue la p&aacute;gina principal de Matrix &oacute; Inicie sesi&oacute;n nuevamente.
            </div>';
    return;
}
else
{
include("root/comun.php");
$conex = obtenerConexionBD("matrix");
$wemp_pmla=$_REQUEST['wemp_pmla'];
?>

<!DOCTYPE html>
<html lang="esp" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre eñes y tildes -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GENERADOR CONSULTAS - PAGINA PRINCIPAL</title>
    <link href="estilos.css" rel="stylesheet">
	
</head>
		
<body>
    <div class="container" style="margin-top: -30px; margin-left: 10px">
        <div id="loginbox" style="margin-top:50px; width: 580px">
            <div class="panel panel-info" >
                <div class="panel-heading">
                    <div class="panel-title" align="center" >GENERADOR DE CONSULTAS</div>
                </div>
                <div style="padding-top:30px" class="panel-body" >
                    
					<FORM CLASS="borde" ACTION="rep_gesis1form.php" METHOD="POST">
					<input type='HIDDEN' NAME= 'wemp_pmla' value="<?php echo($wemp_pmla)?>">
					    <select name="consulta" value="-Any-" >
                            <option>- Selecciona la Informacion a Buscar -</option>
                            <option value="camilleros">Camilleros</option>
                            <option value="altas">Altas</option>
                            <option value="ordenes">Ordenes</option>
							<option value="eventos">Eventos Adversos</option>
							<option value="vm">Ventilacion Mecanica</option>
							<option value="hce76">HCE 000076</option>
							<option value="hce77">HCE 000077</option>
							<option value="hce122">HCE 000122</option>
							<option value="hce134">HCE 000134</option>
							<option value="hce139">HCE 000139</option>
							<option value="hce152">HCE 000152</option>
							<option value="hce360u">HCE 000360 Urgencias</option>
							<option value="hce432">HCE 000432</option>
							<option value="hce36">HCE 000036 (Filtrado por Firpro 000367 y 000328 Esp. Nutricion)</option>
							<option value="tcx11">Programacion Turnos de Cirugia (tcx 000011)</option>
							<option value="movhos178">Asignacion Turnos Urgencias (Movhos 000178)</option>
							<option value="movhos204">Asignacion Historias temporales,triage (Movhos 000204)</option>
							<option value="med_custodia">Medicamentos en Custodia</option>
							<option value="pac_cardio">Pacientes Rotulados Cardiologia</option>
							<option value="pac_egrhos">Pacientes Egresados Hospitalizacion</option>
							<option value="movhos67">Historial Ocupacion Habitaciones (movhos 000067)</option>
							<option value="movpachos">Movimiento Pacientes en Hospitalizacion</option>
							<option value="movcirliq">Cirugias Liquidadas</option>
                        </select>
						<hr>
						<?php
							//=================================
							// SELECCIONAR FECHAS A CONSULTAR
							//=================================
							echo "<tr class='Fila1'>
								<td align=center><b>FECHA INICIAL: </b>";  
								if(isset($wfec_i) && isset($wfec_f))
								{
									campoFechaDefecto("wfec_i", $wfec_i);
								}
								else
								{
									campoFechaDefecto("wfec_i", date("Y-m-d"));
								}
								echo "</td>";
								echo "<td align=center><b>FECHA FINAL: </b>"; 
								if(isset($wfec_i) && isset($wfec_f))
								{
									campoFechaDefecto("wfec_f", $wfec_f);
								}
								else
								{
									campoFechaDefecto("wfec_f", date("Y-m-d"));
								}
								echo "</td>";
							echo "</tr>";
						?>
						 <hr>
                  					
					<P><INPUT TYPE="SUBMIT" NAME="buscar" VALUE="Enviar"></P>

					</FORM>


                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
}
?>