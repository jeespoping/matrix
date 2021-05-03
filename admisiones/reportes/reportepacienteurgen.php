<!--/*******************************************************************************************************************************************
*                     REPORTE DE PACIENTES QUE LLEVA MAS DE 20 HORAS URGENCIAS                                 *
********************************************************************************************************************************************/

//==========================================================================================================================================
//PROGRAMA				      : REPORTE DE PACIENTES QUE LLEVA MAS DE 20 HORAS URGENCIAS                                      |
//AUTOR				          : Ing. Didier Orozco Carmona.                                                                                       |
//FECHA CREACION			  : 2019-02-26.                                                                                             |
//FECHA ULTIMA ACTUALIZACION  : 2019-02-26.                                                                                             |
//DESCRIPCION			      : REPORTE DE PACIENTES QUE LLEVA MAS DE 20 HORAS URGENCIAS                                      |.        |
//                                                                                                                                          |
//TABLAS UTILIZADAS :                                                                                                                       |
//movhos_000018, cliame_000101, cliame_000100
//  
// 
//                                                                                                                                     |
//==========================================================================================================================================
	-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" HTTP-EQUIV="REFRESH" CONTENT="1;URL=http://132.1.18.13/matrix/admisiones/reportes/reportepacienteurgen.php?wemp_pmla=01"/>
<title>Consultar Registro</title>

<script> 
function refrescar(){
setTimeout(function(){ location.reload();  refrescar();} , 200000);

}
refrescar();
       
</script>
<?php
    if(!isset($_SESSION['user']))
    {
        ?>
<style type="text/css">
<!--
.animacion1 {            /*position: absolute;*/

            animation-name: parpadeo;
            animation-duration: 3s;
            animation-timing-function: linear;
            animation-iteration-count: infinite;

            -webkit-animation-name:parpadeo;
            -webkit-animation-duration: 3s;
            -webkit-animation-timing-function: linear;
            -webkit-animation-iteration-count: infinite;
}
-->
</style>
<div align="center">
				<label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix o inicie sesion nuevamente.</label>
</div>
        <?php
        return;
    }else
	{
        $user_session = explode('-', $_SESSION['user']);
        $wuse = $user_session[1];
        include("conex.php");
        include("root/comun.php");
        mysql_select_db("matrix");
		$wbasedatomovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
		$wbasedatocliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame"); 
		$wbasedatocencam = consultarAliasPorAplicacion($conex, $wemp_pmla, "camilleros");
		

        $conex = obtenerConexionBD("matrix");
        $conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
    }
	
?>
 <!--   <script>
        function initAnimation(){
            document.getElementById('alerta').className ='animacion';
        }
    </script> -->
    <style>
        .animacion {
            /*position: absolute;*/

            animation-name: parpadeo;
            animation-duration: 3s;
            animation-timing-function: linear;
            animation-iteration-count: infinite;

            -webkit-animation-name:parpadeo;
            -webkit-animation-duration: 3s;
            -webkit-animation-timing-function: linear;
            -webkit-animation-iteration-count: infinite;
        }

        @-moz-keyframes parpadeo{
            0% { opacity: 1.0; }
            50% { opacity: 0.0; }
            100% { opacity: 1.0; }
        }

        @-webkit-keyframes parpadeo {
            0% { opacity: 1.0; }
            50% { opacity: 0.0; }
            100% { opacity: 1.0; }
        }

        @keyframes parpadeo {
            0% { opacity: 1.0; }
            50% { opacity: 0.0; }
            100% { opacity: 1.0; }
        }

        #alerta {
            width: 100px;
            height: 100px;
            background: red;
        }
    </style>
</head>


	<body width="616" height="47">
	<table width="1000" height="113" style="border: groove; width: 1500px">
      <tr>
        <td width="29%" rowspan="2" align="center" style="border: groove; width: 20%"><input name="image" type="image" id="btnVer" src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="80" />
        </td>
        <td width="32%" rowspan="2" align="center" style="border: groove; width: 55%"><p><strong>PACIENTES CON MAS DE 20 HORAS EN URGENCIAS </strong></p></td>
        <td width="32%" colspan="2" style="border: groove; width: 35%"><table>
            <tr>
              <td>Version: 1.0 </td>
            </tr>
            <tr>
              <td>Pagina: 1</td>
            </tr>
            <tr>
              <td>Fecha de Emision: <?php echo '2019-02-27' ?></td>
            </tr>
        </table></td>
      </tr>
      <tr>
        <td style="border: 0; width: 15%"><strong>Pacientes sin solicitud de habitacion y con 24 horas cumplidas: </strong></td>
        <td style="border: 0; width: 15%" id="alerta" class="animacion">&nbsp; &nbsp; &nbsp;</td>
      </tr>
    </table>
	<p>
	  <?php
	//echo "No se ha ingresado un valor a buscar";
	//										  0      1     2       3      4      5      6      7     8       9     10     11    12     13        14           15
	$select_paciente = mysql_query("select Ubihis,Ubiing,Ubisac,Ingfei,Inghin,Pactdo,Pacdoc,Pacap1,Pacap2,Pacno1,Pacno2,motivo,Ubialp,Ubiald,Fec_asigcama,Hora_asigcama,
    TIMESTAMPDIFF(
        DAY,
        CONCAT ( Ingfei, ' ', Inghin ),
        NOW()
    ) AS 'days',
    TIME_FORMAT(
        TIMEDIFF(
            NOW(),
            CONCAT ( Ingfei, ' ', Inghin )
        ),
        '%k'
    ) AS 'hours',
    TIME_FORMAT(
        TIMEDIFF(
            NOW(),
            CONCAT ( Ingfei, ' ', Inghin )
        ),
        '%i'
    ) AS 'minutes',
    TIME_FORMAT(
        TIMEDIFF(
            NOW(),
            CONCAT ( Ingfei, ' ', Inghin )
        ),
        '%s'
    ) AS 'seconds'	
	from ".$wbasedatocliame."_000100, ".$wbasedatocliame."_000101, ".$wbasedatomovhos."_000018 m18 left join ".$wbasedatocencam."_000003 c3 on (m18.ubihis=c3.historia and c3.fecha_data between m18.fecha_data and now() and c3.motivo='SOLICITUD DE CAMA' and Fec_asigcama='0000:00:00')
	where Ubisac = '1130'
 	and  Ubiald = 'off'
 	and  ubihis = Inghis
 	and  ubiing = Ingnin
 	and  Inghis = pachis
	group by ubihis
	order by days ASC");
	?>
	</p>
	<table width="1500" height="44" border="1" align="center">
				<tr>
					<td style="background-color: #66afe9" height="18"><div align="center"><strong>HISTORIA CLINICA </strong></div></td>
					<td style="background-color: #66afe9"><div align="center"><strong>NUMERO DE INGRESO </strong></div></td>
					<td style="background-color: #66afe9"><div align="center"><strong>UBICACION </strong></div></td>
					<td style="background-color: #66afe9"><div align="center"><strong>FECHA DE INGRESO</strong></div></td>
					<td style="background-color: #66afe9"><div align="center"><strong>HORA DE INGRESO </strong></div></td>
					<td style="background-color: #66afe9"><div align="center"><strong>TIPO DE DOCUMENTO</strong></div></td>
					<td style="background-color: #66afe9"><div align="center"><strong>DOCUMENTO</strong></div></td>
					<td style="background-color: #66afe9"><div align="center"><strong>NOMBRE</strong></div></td>
					<td style="background-color: #66afe9"><div align="center"><strong>SOLICITUD DE HABITACION </strong></div></td>
					<td style="background-color: #66afe9"><div align="center"><strong>TIEMPO TRANSCURRIDO DEL PACIENTE EN URGENCIAS</strong></div></td>
					<td style="background-color: #66afe9"><div align="center"><strong>ALTA EN PROCESO </strong></div></td>
					<td style="background-color: #66afe9"><div align="center"><strong>ALTA DEFINITIVA </strong></div></td>
				</tr>
				
				
	<?php
			    while($resultado=mysql_fetch_array($select_paciente))
				{
					$Ubihis = $resultado[0];					    
					$Ubiing = $resultado[1];
					$Ubisac = $resultado[2];
					$Ingfei = $resultado[3];
					$Inghin = $resultado[4];					    
					$Pactdo = $resultado[5];
					$Pacdoc = $resultado[6];
					$Pacap1 = $resultado[7];
					$Pacap2 = $resultado[8];
					$Pacno1 = $resultado[9];
					$Pacno2 = $resultado[10];
					$nombre_completo = $Pacno1.' '.$Pacno2.' '.$Pacap1.' '.$Pacap2;
					$motivo = $resultado[11];
					$days = $resultado[16];
					$hora = $resultado[17];
					$alta_proceso = $resultado[12];
					$alta_definitiva = $resultado[13];
					$Fec_asigcama = $resultado[14];
					$Hora_asigcama = $resultado[15];
					if ($hora >=20 and $days<=31){
						
						?>
						<tr>
						<td width="244" height="18"><?php echo $Ubihis ?> <div align="center"></div></td>
						<td width="100"><?php echo $Ubiing ?></td>
						<td width="198"><?php echo $Ubisac.'-Urgencias' ?></td>
						<td width="244"><?php echo $Ingfei ?></td>
						<td width="152"><?php echo $Inghin ?></td>
						<td width="198"><?php echo $Pactdo ?></td>
						<td width="244"><?php echo $Pacdoc ?> <div align="center"></div></td>
						<td width="300"><?php echo $nombre_completo ?></td>
										<?php if ($motivo == '' and $hora>=24){
												?> 
												<td width="198" id="alerta" class="animacion"></td>
												<?php
											}else{
												?>
												<td width="198"><?php echo $motivo.'- PENDIENTE DE HABITACION'?></td>
												<?php
											   }
											   ?>
						
						<td width="244"><?php echo 'Las horas pasadas son: '.$hora.'<br>'; 	
										   echo	'Equivalente en dias es: '.$days?></td>
						<td width="152"><?php echo $alta_proceso ?></td>
						<td width="152"><?php echo $alta_definitiva ?></td>		   
						</tr>
				<?php
					}
				
					
					
	?>

				

	<?php
				}
	?>
	</table>
	

</body>
</html>