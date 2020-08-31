<HTML LANG="es">

<HEAD>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre eñes y tildes -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <TITLE>RESULTADOS DE LA BUSQUEDA</TITLE>
    <link href="estilos.css" rel="stylesheet">
</HEAD>

<BODY>
<div class="container" style="margin-top: -30px; margin-left: 10px">
        <div id="loginbox" style="margin-top:50px; width: 1500px">
            <div class="panel panel-info" >
                <div class="panel-heading">
                    <div class="panel-title">Resultados de la busqueda</div>
                </div>
                <div style="padding-top:30px" class="panel-body" >

<P>Estos son los datos introducidos:</P>

<?php
include_once("conex.php");
 session_start();

if (!isset($user))
	if(!isset($_SESSION['user']))
	  session_register("user");
	
if(!isset($_SESSION['user']))
	echo "error";
else
{	            
  include_once("root/magenta.php");
  include_once("root/comun.php");
  include_once("movhos/movhos.inc.php");
  
  $conex = obtenerConexionBD("matrix");
  $archivo = fopen("plano01.txt","w"); 
   $resultado = $_POST;
   $consulta = $_POST['consulta'];
   $fecha1 = $_REQUEST['wfec_i'];
   $fecha2 = $_REQUEST['wfec_f'];
   echo "FECHA INICIAL:".$fecha1."<BR>";
   echo "FECHA FINAL:".$fecha2."<BR>";
   $dias = (abs(strtotime($fecha2) - strtotime($fecha1)))/86400;   //CONVIERTE LAS FECHAS A ENTEROS 
   $dias = $dias + 1;
   echo "dias consultados:".$dias."<BR>";
   //Se realiza esta condicion para que le permita realizar la consulta mayor a 31 dias a este reporte
   if ($consulta ==  'med_custodia')
	   $dias = 1;

   if ($dias <= 31){
	   if(isset($_POST['consulta'])){
			 //$consulta = $_POST['consulta'];
			 switch ($consulta) {
			   case 'camilleros':
				   echo "Query Camilleros";
				   echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
				   fwrite($archivo, "Fecha data|Hora data|Origen|Motivo|Habitacion|Observacion|Destino|Solicito|Ccosto|Camillero|Fecha respuesta|Hora respuesta|Fecha llegada|Hora llegada|Fecha Cumplimiento|Hora Cumplimiento|Anulada|Observ central|Central|Usu central|Historia|Hab asignada|Fec asigcama|Hora asigcama|Usu anula|Fecha anula|Hora anula|Usuario recibe|Tramiteok|Usutramitepok|Fechatramiteok|Horatramiteok|Seguridad" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center width='50'>";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha data</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Hora data</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Origen</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Motivo</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Habitacion</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Observacion</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Destino</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Solicito</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Ccosto</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Camillero</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha respuesta</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Hora respuesta</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha llegada</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Hora llegada</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha Cumplimiento</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Hora Cumplimiento</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Anulada</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Observ central</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Central</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Usu central</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Historia</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Hab asignada</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fec asigcama</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Hora asigcama</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Usu anula</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha anula</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Hora anula</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Usuario recibe</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Tramiteok</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Usutramitepok</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fechatramiteok</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Horatramiteok</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Seguridad</b></td>";
				   echo "</tr>";
				   
				   $query = "select Fecha_data,Hora_data,Origen,Motivo,Habitacion,Observacion,Destino,Solicito,Ccosto,Camillero,
								Fecha_respuesta,Hora_respuesta,Fecha_llegada,Hora_llegada,Fecha_Cumplimiento,Hora_cumplimiento,
								Anulada,Observ_central,Central,Usu_central,Historia,Hab_asignada,Fec_asigcama,Hora_asigcama,
								Usu_anula,Fecha_anula,Hora_anula,Usuario_recibe,Tramiteok,Usutramitepok,Fechatramiteok,Horatramiteok,Seguridad
							 from cencam_000003
							 Where Fecha_data between '".$fecha1."' and '".$fecha2."' and Anulada = 'No' "; 
								   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[7]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[8]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[9]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[10]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[11]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[12]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[13]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[14]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[15]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[16]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[17]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[18]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[19]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[20]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[21]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[22]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[23]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[24]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[25]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[26]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[27]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[28]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[29]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[30]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[31]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[32]."</td>";
					   echo "</tr>";
					   // Eliminar caracter especial y ocultos en el dato
					   $LineaDatos1 = $registroB[0]."|".$registroB[1]."|".$registroB[2]."|".$registroB[3]."|".$registroB[4]."|".$registroB[5]."|".$registroB[6]."|".$registroB[7]."|".$registroB[8]."|".$registroB[9]."|".$registroB[10]."|".$registroB[11]."|".$registroB[12]."|".$registroB[13]."|".$registroB[14]."|".$registroB[15]."|".$registroB[16]."|".$registroB[17]."|".$registroB[18]."|".$registroB[19]."|".$registroB[20]."|".$registroB[21]."|".$registroB[22]."|".$registroB[23]."|".$registroB[24]."|".$registroB[25]."|".$registroB[26]."|".$registroB[27]."|".$registroB[28]."|".$registroB[29]."|".$registroB[30]."|".$registroB[31]."|".$registroB[32];
					   $LineaDatos1 = str_replace(chr(13).chr(10) , ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("\n", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<br>", ' ', $LineaDatos1);
					   $LineaDatos1 = str_replace("</br>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("</b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = trim($LineaDatos1);
					  //Grabo en el archivo
					  fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;
			   
			   case 'altas':
					echo "Query Altas";
					echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
				   fwrite($archivo, "HISTORIA|INGRESO|CCOSTOS|HAB|FECHA ALTA PROCESO|HORA ALTA EN PROCESO|FECHA ALTA DEF|HORA ALTA DEF|FECHA FACTURA|HORA FACTURA|FECHA CAJA|HORA CAJA|TIPO PAC|NIT|DESCRIPCION|DOC MEDICO EGRESO|ESPECIALIDAD" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center >";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HISTORIA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>INGRESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>CCOSTOS</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HAB</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA ALTA PROCESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HORA ALTA EN PROCESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA ALTA DEF</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HORA ALTA DEF</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA FACTURA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HORA FACTURA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA CAJA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HORA CAJA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>TIPO PAC</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>NIT</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DESCRIPCION</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DOC MEDICO EGRESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>ESPECIALIDAD</b></td>";
				   echo "</tr>";
				   
					$query = "select Ubihis,Ubiing,Ubisac,Ubihac,Ubifap,Ubihap,Ubifad,Ubihad,
									Cueffa,Cuehfa,Cuefpa,Cuehpa,Ingtpa,Ingcem,Ingent,Diamed,Diaesm     
							 from movhos_000018 a 
								 left join 
								 movhos_000022 b on (Ubihis = Cuehis and  Ubiing = Cueing ), 
								 cliame_000101 d 
								 left join 
								 cliame_000109 c109 on ( Inghis = Diahis and  Ingnin = Diaing and Diatip = 'P')
							 where Ubifap between '".$fecha1."' and '".$fecha2."' 
							  and  Ubisac in ('1182','1183','1184','1020','1021','1187','1180',
											  '1185','1186','1188','1190','1189','1286',
											  '1282','1281','1283','1284','1285','1179') 
							  and  Ubihis = Inghis
							  and  Ubiing = Ingnin  "; 
								   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[7]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[8]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[9]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[10]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[11]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[12]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[13]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[14]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[15]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[16]."</td>";
					   echo "</tr>";
					   $LineaDatos1 = $registroB[0]."|".$registroB[1]."|".$registroB[2]."|".$registroB[3]."|".$registroB[4]."|".$registroB[5]."|".$registroB[6]."|".$registroB[7]."|".$registroB[8]."|".$registroB[9]."|".$registroB[10]."|".$registroB[11]."|".$registroB[12]."|".$registroB[13]."|".$registroB[14]."|".$registroB[15]."|".$registroB[16];
					   fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;
			   case 'ordenes':
					echo "Query Ordenes";
					echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
				   fwrite($archivo, "HISTORIA|INGRESO|FECHA ORDEN|HORA ORDEN|TIPO ORDEN|COD USUARIO|DESCRIPCION|COD EXAMEN O CUPS|DESCRIPCION" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center >";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HISTORIA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>INGRESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA ORDEN</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HORA ORDEN</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>TIPO ORDEN</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>COD USUARIO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DESCRIPCION</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>COD EXAMEN O CUPS</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DESCRIPCION</b></td>";
				   echo "</tr>";

					$query = "select Ordhis,Ording,Ordfec,Ordhor,Ordtor,Ordusu,u.Descripcion,Detcod,h47.descripcion
								from  hce_000027,hce_000028,usuarios u,hce_000047 h47
								where Ordtor in ('A22','P02','P03') 
								 and  Ordfec between '".$fecha1."' and '".$fecha2."' 
							  and  Ordest = 'on'
							  and  Ordusu = u.Codigo  
							  and  Ordtor = Dettor 
							  and  Ordnro = Detnro 
							  and  detcod in ('1009225', '1014065','1013058','1013091','890605','1014176',
							                  '1009160','1009306','1013192','1009165','1009706','1014007',
											  'S11101','S12101','S12102') 
							  and  Detest = 'on' 
							  and  Detcod =  h47.codigo  "; 
								   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[7]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[8]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[9]."</td>";
					   echo "</tr>";
					   $LineaDatos1 = $registroB[0]."|".$registroB[1]."|".$registroB[2]."|".$registroB[3]."|".$registroB[4]."|".$registroB[5]."|".$registroB[6]."|".$registroB[7]."|".$registroB[8];
					   fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;
					
				case 'eventos':
				   echo "Query Eventos Adversos ";
				   echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
		                              								
				   fwrite($archivo, "Fecha data|Centro de costos detecto|Descripcion|Usuario detecto|Nombre|Profesion|Fecha que detecto|Hora que detecto|Centro de costo que genero|Descripcion|Descripcion no conformidad o evento adverso|Nombre Afectado|Edad Afectado|Identificacion Afectado|Historia Afectado|Acciones Inseguras|Entidad Responsable|Numero consecutivo" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center width='50'>";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha data</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Centro de costos detecto</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Descripcion</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Usuario detecto</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Nombre</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Profesion</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha que detecto</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Hora que detecto</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Centro de costo que genero</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Descripcion</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Descripcion no conformidad o evento adverso</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Nombre Afectado</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Edad Afectado</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Identificacion Afectado</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Historia Afectado</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Acciones Inseguras</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Entidad Responsable</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Numero consecutivo</b></td>";
				   echo "</tr>";
				   
     			   $query = "select g1.Fecha_data,Nceccd,m11a.Cconom,Ncecpd,u.descripcion,m44.Espnom,Ncefed,Ncehod,Nceccg,m11b.Cconom,Ncedes,
						            Ncenaf,Nceeaf,Nceiaf,Ncehca,Nceaci,Nceres,Ncenum
							 from gescal_000001 g1 
								  left join 
								  movhos_000048 m48 on (Ncecpd = m48.Meduma)
								  left join 
								  movhos_000044 m44 on (m48.Medesp = m44.Espcod )
								  left join
								  usuarios u on (Ncecpd = u.codigo )
								  ,costosyp_000005 m11a,costosyp_000005 m11b
							where g1.Fecha_data between '".$fecha1."' and '".$fecha2."'  
									 and  Ncecne = 'EA' 
									 and  Nceccd = m11a.Ccocod 
									 and  m11a.Ccoemp = '01' 
									 and  Nceccg = m11b.Ccocod 
									 and  m11b.Ccoemp = '01'  "; 
								   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[7]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[8]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[9]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[10]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[11]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[12]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[13]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[14]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[15]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[16]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[17]."</td>";
					   echo "</tr>";
					   // Eliminar caracter especial y ocultos en el dato
					   $LineaDatos1 = $registroB[0]."|".$registroB[1]."|".$registroB[2]."|".$registroB[3]."|".$registroB[4]."|".$registroB[5]."|".$registroB[6]."|".$registroB[7]."|".$registroB[8]."|".$registroB[9]."|".$registroB[10]."|".$registroB[11]."|".$registroB[12]."|".$registroB[13]."|".$registroB[14]."|".$registroB[15]."|".$registroB[16]."|".$registroB[17];
					   $LineaDatos1 = str_replace(chr(13).chr(10) , ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("\n", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<br>", ' ', $LineaDatos1);
					   $LineaDatos1 = str_replace("</br>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("</b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = trim($LineaDatos1);
					  //Grabo en el archivo
					  fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;
					
				case 'vm':
				   echo "Query Ventilacion Mecanica ";
				   echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
									   
				   fwrite($archivo, "HISTORIA|INGRESO|UBICACION ACTUAL|CCOSTOS|DESCRIPCION|FECHA INGRESO|FECHA FORMULARIO|HORA FORMULARIO|FORMULARIO|MOVCON|DATO" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center width='50'>";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HISTORIA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>INGRESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>UBICACION ACTUAL</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>CCOSTOS</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DESCRIPCION</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA INGRESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA FORMULARIO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HORA FORMULARIO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FORMULARIO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>MOVCON</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DATO</b></td>";
				   echo "</tr>";
    			   
				   $query = "SELECT a.Habhis,a.Habing,a.Habcod,a.Habcco,m.cconom,d.Ingfei,hc.fecha_data,hc.hora_data,hc.movpro,hc.movcon,hc.movdat 
							FROM movhos_000020 a 
								 inner join hce_000112 hc on (a.Habhis=hc.movhis and a.Habing=hc.moving and hc.movcon in (12,16)), 
								 cliame_000101 d, movhos_000011 m 
							WHERE a.Habhis = d.Inghis
							 AND  a.Habing = d.Ingnin
							 AND  a.Habcco = m.ccocod   "; 
								   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[7]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[8]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[9]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[10]."</td>";
					   echo "</tr>";
					   // Eliminar caracter especial y ocultos en el dato
					   $LineaDatos1 = $registroB[0]."|".$registroB[1]."|".$registroB[2]."|".$registroB[3]."|".$registroB[4]."|".$registroB[5]."|".$registroB[6]."|".$registroB[7]."|".$registroB[8]."|".$registroB[9]."|".$registroB[10];
					   $LineaDatos1 = str_replace(chr(13).chr(10) , ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("\n", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<br>", ' ', $LineaDatos1);
					   $LineaDatos1 = str_replace("</br>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("</b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = trim($LineaDatos1);
					  //Grabo en el archivo
					  fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;
			  
			 case 'hce76':
				   echo "Query HCE Tabla 000076";
				   echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
				    
				   fwrite($archivo, "FECHA DATA&HORA DATA&HISTORIA&INGRESO&MOVCON&USUARIO&DATO" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center width='50'>";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HORA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HISTORIA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>INGRESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>MOVCON</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>USUARIO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DATO</b></td>";
				   echo "</tr>";
    			   
				   $query = "select Fecha_data,Hora_data,movhis,moving,movcon,movusu,movdat 
							 from hce_000076 
							 where Fecha_data Between '".$fecha1."' and '".$fecha2."'  
								and  movcon in (4,7,18,95,99,101,103,105,9,20,30,50) 
							 order by movhis,moving  "; 
								   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "</tr>";
					   // Eliminar caracter especial y ocultos en el dato
					   $LineaDatos1 = $registroB[0]."&".$registroB[1]."&".$registroB[2]."&".$registroB[3]."&".$registroB[4]."&".$registroB[5]."&".$registroB[6];
					   $LineaDatos1 = str_replace(chr(13).chr(10) , ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("\n", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<br>", ' ', $LineaDatos1);
					   $LineaDatos1 = str_replace("</br>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("</b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = trim($LineaDatos1);
					  //Grabo en el archivo
					  fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...(separador de columnas <b>&</b>)</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;
					
			case 'hce77':
				   echo "Query HCE Tabla 000077";
				   echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
				    
				   fwrite($archivo, "FECHA DATA&HORA DATA&HISTORIA&INGRESO&MOVCON&USUARIO&DATO" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center width='50'>";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HORA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HISTORIA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>INGRESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>MOVCON</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>USUARIO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DATO</b></td>";
				   echo "</tr>";
    			   
				   $query = "select Fecha_data,Hora_data,movhis,moving,movcon,movusu,movdat 
							 from hce_000077 
							 where Fecha_data Between '".$fecha1."' and '".$fecha2."'  
							   and movcon in (2,22,41,69,81,79,77,70,71,3,23,4,5,6,7,11,12,50,59,52,99,92,47,57) 
							 order by movhis,moving  "; 
								   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "</tr>";
					   // Eliminar caracter especial y ocultos en el dato
					   $LineaDatos1 = $registroB[0]."&".$registroB[1]."&".$registroB[2]."&".$registroB[3]."&".$registroB[4]."&".$registroB[5]."&".$registroB[6];
					   $LineaDatos1 = str_replace(chr(13).chr(10) , ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("\n", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<br>", ' ', $LineaDatos1);
					   $LineaDatos1 = str_replace("</br>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("</b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = trim($LineaDatos1);
					  //Grabo en el archivo
					  fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...(separador de columnas <b>&</b>)</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;

			case 'hce122':
				   echo "Query HCE Tabla 000122";
				   echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
				    
				   fwrite($archivo, "FECHA DATA&HORA DATA&HISTORIA&INGRESO&MOVCON&USUARIO&DATO" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center width='50'>";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HORA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HISTORIA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>INGRESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>MOVCON</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>USUARIO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DATO</b></td>";
				   echo "</tr>";
    			   
				   $query = "select Fecha_Data,Hora_Data,Movhis,Moving,Movcon,movusu,Movdat
							 from hce_000122 
							 where Fecha_Data between '".$fecha1."' and '".$fecha2."'  
							 order by movhis,moving  "; 
								   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "</tr>";
					   // Eliminar caracter especial y ocultos en el dato
					   $LineaDatos1 = $registroB[0]."&".$registroB[1]."&".$registroB[2]."&".$registroB[3]."&".$registroB[4]."&".$registroB[5]."&".$registroB[6];
					   $LineaDatos1 = str_replace(chr(13).chr(10) , ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("\n", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<br>", ' ', $LineaDatos1);
					   $LineaDatos1 = str_replace("</br>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("</b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = trim($LineaDatos1);
					  //Grabo en el archivo
					  fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...(separador de columnas <b>&</b>)</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;

			case 'hce134':
				   echo "Query HCE Tabla 000134";
				   echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
				    
				   fwrite($archivo, "FECHA DATA&HORA DATA&HISTORIA&INGRESO&MOVCON&USUARIO&DATO" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center width='50'>";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HORA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HISTORIA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>INGRESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>MOVCON</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>USUARIO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DATO</b></td>";
				   echo "</tr>";
    			   
				   $query = "select Fecha_Data,Hora_Data,Movhis,Moving,Movcon,movusu,Movdat
							 from hce_000134 
							 where Fecha_Data between '".$fecha1."' and '".$fecha2."'  
							 order by movhis,moving  "; 
								   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "</tr>";
					   // Eliminar caracter especial y ocultos en el dato
					   $LineaDatos1 = $registroB[0]."&".$registroB[1]."&".$registroB[2]."&".$registroB[3]."&".$registroB[4]."&".$registroB[5]."&".$registroB[6];
					   $LineaDatos1 = str_replace(chr(13).chr(10) , ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("\n", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<br>", ' ', $LineaDatos1);
					   $LineaDatos1 = str_replace("</br>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("</b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = trim($LineaDatos1);
					  //Grabo en el archivo
					  fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...(separador de columnas <b>&</b>)</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;			

			case 'hce432':
				   echo "Query HCE Tabla 000432";
				   echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
				    
				   fwrite($archivo, "FECHA DATA&HORA DATA&HISTORIA&INGRESO&MOVCON&USUARIO&DATO" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center width='50'>";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HORA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HISTORIA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>INGRESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>MOVCON</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>USUARIO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DATO</b></td>";
				   echo "</tr>";
    			   
				   $query = "select Fecha_Data,Hora_Data,Movhis,Moving,Movcon,movusu,Movdat
							 from hce_000432 
							 where Fecha_Data between '".$fecha1."' and '".$fecha2."'  
							 order by movhis,moving  "; 
								   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "</tr>";
					   // Eliminar caracter especial y ocultos en el dato
					   $LineaDatos1 = $registroB[0]."&".$registroB[1]."&".$registroB[2]."&".$registroB[3]."&".$registroB[4]."&".$registroB[5]."&".$registroB[6];
					   $LineaDatos1 = str_replace(chr(13).chr(10) , ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("\n", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<br>", ' ', $LineaDatos1);
					   $LineaDatos1 = str_replace("</br>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("</b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = trim($LineaDatos1);
					  //Grabo en el archivo
					  fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...(separador de columnas <b>&</b>)</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;
					
			case 'hce36':
				   echo "Query HCE 000036 (Filtrado por Firpro 000367 y 000328 Esp. Nutricion)";
				   echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
				   
				   fwrite($archivo, "FECHA DATA|HORA DATA|FORMULARIO|HISTORIA|INGRESO|USUARIO|CCOSTOS" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center width='50'>";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HORA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FORMULARIO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HISTORIA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>INGRESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>USUARIO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>CCOSTOS</b></td>";
				   echo "</tr>";
    			   
				   $query = "select  Fecha_data,Hora_data,Firpro,Firhis,Firing,Firusu,Fircco 
							 from hce_000036 
							 where Fecha_data Between '".$fecha1."' and '".$fecha2."'  
							   and  Firpro in ('000367','000328')  
							   and  Firrol = '100099' "; 
								   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "</tr>";
					   // Eliminar caracter especial y ocultos en el dato
					   $LineaDatos1 = $registroB[0]."|".$registroB[1]."|".$registroB[2]."|".$registroB[3]."|".$registroB[4]."|".$registroB[5]."|".$registroB[6];
					   $LineaDatos1 = str_replace(chr(13).chr(10) , ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("\n", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<br>", ' ', $LineaDatos1);
					   $LineaDatos1 = str_replace("</br>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("</b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = trim($LineaDatos1);
					  //Grabo en el archivo
					  fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...(separador de columnas <b>|</b>)</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;
			
			case 'tcx11':
				   echo "Query Programacion Turnos de Cirugia (tcx 000011)";
				   echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
				   
				   fwrite($archivo, "Fecha_data|Hora_data|Turtur|Turqui|Turhin|Turhfi|Turfec|Turndt|Turtdo|Turdoc|Turhis|Turnin|
									Turnom|Turfna|Tursex|Turins|Turtcx|Turtip|Turtan|Tureps|Turuci|Turbio|Turinf|Turmat|Turmok|
									Turban|Turbok|Turpre|Turpan|Turpes|Turpep|Turpeq|Turper|Turpea|Turubi|Turmdo|Turtel|Turord|
									Turcom|Turcir|Turmed|Turequ|Turusg|Turusm|Turcup|Turmaa|Turspa|Turrcu|Turest|Turaud|Turapi|
									Turfhi|Turusi|Turfhf|Turusf|Turepc|Turcdi|Turcdt|Turcdr" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center width='50'>";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HORA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Codigo Turno</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Quirofano</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Hora Inicio</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Hora Finalizacion</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha Cirugia</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha Solicitud Turno</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Tipo de Documento</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Identificacion</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Historia</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Ingreso</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Nombre</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha Nacimiento</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Sexo</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Instrumentadora</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Tipo Cirugia</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Tipo programacion</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Tipo Anestesia</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Responsable</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Uci</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Biopsia por Congelacion</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Infectada</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Material</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Material Gestionado</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Banco de Sangre</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Gestion de Banco Sangre</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Preadmision</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Preanestesia</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Paciente en Sala</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Paciente en Preparacion</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Paciente en Quirofano</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Paciente en Recuperacion</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Paciente en Alta</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Ubicacion</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Modificado despues de Orden</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Telefono</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Ordenes</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Comentarios</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Cirugias</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Medicos</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Equipos</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Usuario que Grabo</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fje</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Codigos Cups Autorizados</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Material Autorizado</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Seguimiento Paciente Ambulatorio</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Turrcu</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Estado</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Auditado</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Aplicar Insumos</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha Hora Real Inicio Ciruguia</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Usuario dio inicio Cirugia</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha Hora Real Fin de Cirugia</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Usuario dio Fin de Cirugia</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Estado Proceso Cx (P=Proceso T=Terminada)</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Causa Demora Inicio Cirugia</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Causa Demora Terminar Cx</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Causa Reprogramacion</b></td>";
				   echo "</tr>";
    			   
				   $query = "select Fecha_data,Hora_data,Turtur,Turqui,Turhin,Turhfi,Turfec,Turndt,Turtdo,Turdoc,Turhis,Turnin,
									Turnom,Turfna,Tursex,Turins,Turtcx,Turtip,Turtan,Tureps,Turuci,Turbio,Turinf,Turmat,Turmok,
									Turban,Turbok,Turpre,Turpan,Turpes,Turpep,Turpeq,Turper,Turpea,Turubi,Turmdo,Turtel,Turord,
									Turcom,Turcir,Turmed,Turequ,Turusg,Turusm,Turcup,Turmaa,Turspa,Turrcu,Turest,Turaud,Turapi,
									Turfhi,Turusi,Turfhf,Turusf,Turepc,Turcdi,Turcdt,Turcdr
							 From tcx_000011 
							 where Turfec Between '".$fecha1."' and '".$fecha2."'  "; 
								   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[7]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[8]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[9]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[10]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[11]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[12]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[13]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[14]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[15]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[16]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[17]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[18]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[19]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[20]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[21]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[22]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[23]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[24]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[25]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[26]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[27]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[28]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[29]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[30]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[31]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[32]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[33]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[34]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[35]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[36]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[37]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[38]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[39]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[40]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[41]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[42]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[43]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[44]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[45]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[46]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[47]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[48]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[49]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[50]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[51]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[52]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[53]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[54]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[55]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[56]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[57]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[58]."</td>";
					   echo "</tr>";
					   // Eliminar caracter especial y ocultos en el dato
					   $LineaDatos1 = $registroB[0]."|".$registroB[1]."|".$registroB[2]."|".$registroB[3]."|".$registroB[4]."|".$registroB[5]."|".$registroB[6]."|".$registroB[7]."|".$registroB[8]."|".$registroB[9]."|".$registroB[10]."|".$registroB[11]."|".$registroB[12]."|".
					                  $registroB[13]."|".$registroB[14]."|".$registroB[15]."|".$registroB[16]."|".$registroB[17]."|".$registroB[18]."|".$registroB[19]."|".$registroB[20]."|".$registroB[21]."|".$registroB[22]."|".$registroB[23]."|".$registroB[24]."|".$registroB[25]."|".
									  $registroB[26]."|".$registroB[27]."|".$registroB[28]."|".$registroB[29]."|".$registroB[30]."|".$registroB[31]."|".$registroB[32]."|".$registroB[33]."|".$registroB[34]."|".$registroB[35]."|".$registroB[36]."|".$registroB[37]."|".$registroB[38]."|".
									  $registroB[39]."|".$registroB[40]."|".$registroB[41]."|".$registroB[42]."|".$registroB[43]."|".$registroB[44]."|".$registroB[45]."|".$registroB[46]."|".$registroB[47]."|".$registroB[48]."|".$registroB[49]."|".$registroB[50]."|".$registroB[51]."|".
					                  $registroB[52]."|".$registroB[53]."|".$registroB[54]."|".$registroB[55]."|".$registroB[56]."|".$registroB[57]."|".$registroB[58];
					   $LineaDatos1 = str_replace(chr(13).chr(10) , ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("\n", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<br>", ' ', $LineaDatos1);
					   $LineaDatos1 = str_replace("</br>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("</b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = trim($LineaDatos1);
					  //Grabo en el archivo
					  fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...(separador de columnas <b>|</b>)</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;
			
			case 'movhos178':
				   echo "Query Asignacion Turnos Urgencias (Movhos 000178)";
				   echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
			     
				   fwrite($archivo,"FECHA DATA|HORA DATA|Turno|Documento|Tipo de documento|Estado del registro|Alerta de llamado ventanilla|
				                      Fecha llamado ventanilla|Hora llamado ventanilla|Usuario llamado ventanilla|Ventanilla|En proceso de admision|Admitido|Fecha admitido|
									  Hora admitido|Sala de espera asignada|Alerta de llamado a consulta|Fecha de llamado a consulta|Hora de llamado a consulta|
									  Usuario que llamo a consulta|Consultorio|Clasificacion de atencion (movhos_246)|Nombre|Edad|Tipo edad (A=Años M=Meses D=Dias)|
									  Categoria paciente (movhos_207)|Alerta de llamado a triage|Fecha y hora llamado a triage|Usuario que llamo a triage|
									  Fecha y hora de inicio triage|Con triage asignado|En triage|Fecha y hora de asignacion de triage|Alta o remitido|
									  Alerta de reclasificacion triage|Fecha y hora reclasificacion a triage|Usuario llamo a reclasificacion|Prioridad (movhos_206)|
									  Consultorio triage" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center width='50'>";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HORA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Turno</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Documento</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Tipo de documento</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Estado del registro</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Alerta de llamado ventanilla</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha llamado ventanilla</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Hora llamado ventanilla</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Usuario llamado ventanilla</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Ventanilla</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>En proceso de admision</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Admitido</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha admitido</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Hora admitido</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Sala de espera asignada</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Alerta de llamado a consulta</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha de llamado a consulta</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Hora de llamado a consulta</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Usuario que llamo a consulta</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Consultorio</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Clasificacion de atencion (movhos_246)</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Nombre</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Edad</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Tipo edad (A=Años M=Meses D=Dias)</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Categoria paciente (movhos_207)</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Alerta de llamado a triage</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha y hora llamado a triage</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Usuario que llamo a triage</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha y hora de inicio triage</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Con triage asignado</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>En triage</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha y hora de asignacion de triage</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Alta o remitido</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Alerta de reclasificacion triage</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha y hora reclasificacion a triage</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Usuario llamo a reclasificacion</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Prioridad (movhos_206)</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Consultorio triage</b></td>";
				   echo "</tr>";
    			   
				   $query = "select Fecha_data,Hora_data,Atutur,Atudoc,Atutdo,Atuest,Atullv,Atufll,Atuhll,Atuusu,Atuven,Atupad,Atuadm,
								Atufad,Atuhad,Atusea,Atullc,Atuflc,Atuhlc,Atuulc,Atucon,Atucla,Atunom,Atueda,Atuted,Atuten,Atullt,
								Atufht,Atuutr,Atufit,Atucta,Atuetr,Atufat,Atuaor,Atuart,Atufhr,Atuurt,Atupri,Atuctl  
							 from movhos_000178 
							 where Fecha_Data between '".$fecha1."' and '".$fecha2."'  "; 
								   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[7]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[8]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[9]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[10]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[11]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[12]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[13]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[14]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[15]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[16]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[17]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[18]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[19]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[20]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[21]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[22]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[23]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[24]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[25]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[26]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[27]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[28]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[29]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[30]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[31]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[32]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[33]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[34]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[35]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[36]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[37]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[38]."</td>";
					   echo "</tr>";
					   // Eliminar caracter especial y ocultos en el dato
					   $LineaDatos1 = $registroB[0]."|".$registroB[1]."|".$registroB[2]."|".$registroB[3]."|".$registroB[4]."|".$registroB[5]."|".$registroB[6]."|".
									  $registroB[7]."|".$registroB[8]."|".$registroB[9]."|".$registroB[10]."|".$registroB[11]."|".$registroB[12]."|".$registroB[13]."|".
									  $registroB[14]."|".$registroB[15]."|".$registroB[16]."|".$registroB[17]."|".$registroB[18]."|".$registroB[19]."|".$registroB[20]."|".
									  $registroB[21]."|".$registroB[22]."|".$registroB[23]."|".$registroB[24]."|".$registroB[25]."|".$registroB[26]."|".$registroB[27]."|".
									  $registroB[28]."|".$registroB[29]."|".$registroB[30]."|".$registroB[31]."|".$registroB[32]."|".$registroB[33]."|".$registroB[34]."|".
									  $registroB[35]."|".$registroB[36]."|".$registroB[37]."|".$registroB[38];
					   $LineaDatos1 = str_replace(chr(13).chr(10) , ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("\n", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<br>", ' ', $LineaDatos1);
					   $LineaDatos1 = str_replace("</br>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("</b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = trim($LineaDatos1);
					  //Grabo en el archivo
					  fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...(separador de columnas <b>|</b>)</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;
			
			case 'movhos204':
					echo "Query Asignacion Historias temporales,triage (Movhos 000204)";
					echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
				   
     			   fwrite($archivo, "FECHA DATA|HORA DATA|DOCUMENTO|TIPO DE DOCUMENTO|HISTORIA TEMPORAL|TURNO|NOMBRE|EDAD|
				                     TIPO EDAD (A=Años M=Meses D=Dias)|TIPO DE ENTIDAD (cliame_000246)|ESTADO|ACTUALIZADO EN HISTORIA CLINICA|
									 HISTORIA HCE|INGRESO HCE|ORIGEN HISTORIA TEMPORAL|CCOSTOS DESTINO" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center >";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HORA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DOCUMENTO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>TIPO DE DOCUMENTO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HISTORIA TEMPORAL</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>TURNO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>NOMBRE</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>EDAD</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>TIPO EDAD (A=Años M=Meses D=Dias)</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>TIPO DE ENTIDAD (cliame_000246)</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>ESTADO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>ACTUALIZADO EN HISTORIA CLINICA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HISTORIA HCE</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>INGRESO HCE</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>ORIGEN HISTORIA TEMPORAL</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>CCOSTOS DESTINO</b></td>";
				   echo "</tr>";
				   
					$query = "select Fecha_data,Hora_data,Ahtdoc,Ahttdo,Ahthte,Ahttur,Ahtnom,Ahteda,Ahtted,Ahtten,Ahtest,
					                 Ahtahc,Ahthis,Ahting,Ahtori,Ahtccd 
							 from movhos_000204  
     						 where Fecha_data between '".$fecha1."' and '".$fecha2."' "; 
								   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[7]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[8]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[9]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[10]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[11]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[12]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[13]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[14]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[15]."</td>";
					   echo "</tr>";
					   $LineaDatos1 = $registroB[0]."|".$registroB[1]."|".$registroB[2]."|".$registroB[3]."|".$registroB[4]."|".$registroB[5]."|".$registroB[6]."|".$registroB[7]."|".$registroB[8]."|".$registroB[9]."|".$registroB[10]."|".$registroB[11]."|".$registroB[12]."|".$registroB[13]."|".$registroB[14]."|".$registroB[15];
					   fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;
			
			case 'hce139':
				   echo "Query HCE Tabla 000139";
				   echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
				    
				   fwrite($archivo, "FECHA DATA&HORA DATA&HISTORIA&INGRESO&MOVCON&USUARIO&DATO" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center width='50'>";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HORA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HISTORIA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>INGRESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>MOVCON</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>USUARIO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DATO</b></td>";
				   echo "</tr>";
    			   
				   $query = "select Fecha_Data,Hora_Data,Movhis,Moving,Movcon,movusu,Movdat
							 from hce_000139  
							 where Fecha_Data between '".$fecha1."' and '".$fecha2."'  
							 and movcon = 13
							 order by movhis,moving  "; 
								   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "</tr>";
					   // Eliminar caracter especial y ocultos en el dato
					   $LineaDatos1 = $registroB[0]."&".$registroB[1]."&".$registroB[2]."&".$registroB[3]."&".$registroB[4]."&".$registroB[5]."&".$registroB[6];
					   $LineaDatos1 = str_replace(chr(13).chr(10) , ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("\n", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<br>", ' ', $LineaDatos1);
					   $LineaDatos1 = str_replace("</br>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("</b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = trim($LineaDatos1);
					  //Grabo en el archivo
					  fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...(separador de columnas <b>&</b>)</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;
			
			case 'hce360u':
				   echo "Query HCE Tabla 000360";
				   echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
				    
				   fwrite($archivo, "FECHA DATA&HORA DATA&HISTORIA&INGRESO&MOVCON&USUARIO&DATO" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center width='50'>";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HORA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HISTORIA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>INGRESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>MOVCON</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>USUARIO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DATO</b></td>";
				   echo "</tr>";
    			   
				    $queryt1 = "Truncate table tempohis4";//Borro la tabla temporal tempohis4
					$resultadot1 = mysql_query($queryt1);            // Ejecuto el query 
					
					$queryt2 = "insert into tempohis4
								select 1,Movhis,Moving,Fecha_Data 
								from hce_000360
								where Fecha_Data between '".$fecha1."' and '".$fecha2."' 
								 and movcon = 4  
								 and movdat like '%1130-URGENCIASHab.%'  ";
					$resultadot2 = mysql_query($queryt2);            // Ejecuto el query 
				   
				    $query = "select Fecha_Data,Hora_Data,Movhis,Moving,Movcon,Movdat,movusu 
							 from tempohis4,hce_000360
							 where historia = Movhis 
							  and  ingreso =  Moving 
							  and  Fecha_Data between '".$fecha1."' and '".$fecha2."'  
							  and movcon = 68  
							 order by movhis,moving  "; 
								   
				   
				   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "</tr>";
					   // Eliminar caracter especial y ocultos en el dato
					   $LineaDatos1 = $registroB[0]."&".$registroB[1]."&".$registroB[2]."&".$registroB[3]."&".$registroB[4]."&".$registroB[5]."&".$registroB[6]."&".$registroB[7];
					   $LineaDatos1 = str_replace(chr(13).chr(10) , ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("\n", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<br>", ' ', $LineaDatos1);
					   $LineaDatos1 = str_replace("</br>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("</b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = trim($LineaDatos1);
					  //Grabo en el archivo
					  fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...(separador de columnas <b>&</b>)</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;
					
				case 'hce152':
				   echo "Query HCE Tabla 000152";
				   echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
				    
				   fwrite($archivo, "FECHA DATA&HORA DATA&HISTORIA&INGRESO&MOVCON&DATO&USUARIO&DESCRIPCION" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center width='50'>";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HORA DATA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HISTORIA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>INGRESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>MOVCON</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DATO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>USUARIO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DESCRIPCION</b></td>";
				   echo "</tr>";
    			   
    			    $query = "select Fecha_data,Hora_data,Movhis,Moving,Movcon,Movdat,Movusu,Descripcion 
							  from hce_000152 h152,usuarios 
						      where h152.Fecha_data between '".$fecha1."' and '".$fecha2."'  
							   and  movcon in (1,2,86,104,105) 
							   and  Movusu = Codigo 
							  order by Movhis,Moving   "; 
			   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[7]."</td>";
					   echo "</tr>";
					   // Eliminar caracter especial y ocultos en el dato
					   $LineaDatos1 = $registroB[0]."&".$registroB[1]."&".$registroB[2]."&".$registroB[3]."&".$registroB[4]."&".$registroB[5]."&".$registroB[6]."&".$registroB[7];
					   $LineaDatos1 = str_replace(chr(13).chr(10) , ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("\n", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<br>", ' ', $LineaDatos1);
					   $LineaDatos1 = str_replace("</br>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("<b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = str_replace("</b>", ' ', $LineaDatos1); 
					   $LineaDatos1 = trim($LineaDatos1);
					  //Grabo en el archivo
					  fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...(separador de columnas <b>&</b>)</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;
					
				case 'med_custodia':
					echo "Medicamentos en Custodia";
					echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
				   fwrite($archivo, "HISTORIA|INGRESO|FECHA INICIAL CUSTODIA|TIPO DOC|DOCUMENTO|PACIENTE|FECHA NACIMIENTO|SEXO|FECHA ALTA DEFINITIVA" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center >";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HISTORIA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>INGRESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA INICIAL CUSTODIA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>TIPO DOC</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DOCUMENTO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>PACIENTE</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA NACIMIENTO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>SEXO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA ALTA DEFINITIVA</b></td>";
				   echo "</tr>";
				   
					$query = "select c.movhis,c.Moving,c.Fecha_data,Pactdo,Pacdoc,
									 concat(Pacap1,' ',Pacap2,' ',Pacno1,' ',Pacno2),Pacfna,Pacsex,ubifad
							  from (select  a.movhis, a.moving, min( id ) id 
										from hce_000458 a
										where a.Fecha_data between '".$fecha1."' and '".$fecha2."' 
										and a.movcon = 8
										group by 1, 2 ) a, hce_000458 c,cliame_000100 d,movhos_000018 e
							 where c.id = a.id 
							 and  c.movhis = d.pachis 
							 and  c.movhis = e.ubihis
							 and  c.moving = e.ubiing    "; 
								   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[7]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[8]."</td>";
					   echo "</tr>";
					   $LineaDatos1 = $registroB[0]."|".$registroB[1]."|".$registroB[2]."|".$registroB[3]."|".$registroB[4]."|".$registroB[5]."|".$registroB[6]."|".$registroB[7]."|".$registroB[8];
					   fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;
					
				case 'pac_cardio':
					echo "Query Pacientes Rotulados a la Especialidad Cardiologia";
					echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
				   fwrite($archivo, "CCOSTOS|DESCRIPCION|NUMERO DE PACIENTES" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center >";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>CCOSTOS</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DESCRIPCION</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>NUMERO DE PACIENTES</b></td>";
				   echo "</tr>";
				   
					$query = "SELECT Ubisac,cconom,Count(*)   
							  FROM   movhos_000047 a,movhos_000018 b,movhos_000011 c
        					  WHERE  Metfek between '".$fecha1."' and '".$fecha2."' 
							    and  Metesp = '100135' 
								and  Methis = Ubihis 
								and  Meting = Ubiing 
								and  Ubisac = ccocod 
								group by 1,2  "; 
								   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "</tr>";
					   $LineaDatos1 = $registroB[0]."|".$registroB[1]."|".$registroB[2];
					   fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;
					
			   	case 'pac_egrhos':
					echo "Query Pacientes Egresados Hospitalizacion";
					echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
				   fwrite($archivo, "HISTORIA|INGRESO|FECHA INGRESO|FECHA EGRESO|TIPO DE EGRESO|DX PPAL|DESCRIPCION DX|COD MEDICO DX PPAL|NOMBRE MEDICO|ESPECIALIDAD DX PPAL MEDICO|DESCRIPCION ESPECIALIDAD|COD MEDICO TRATANTE|MEDICO|ESPECILIDAD MEDICO TRATANTE|DESCRIPCION ESPECIALIDAD|SERVICIO EGRESO|DESCRIPCION" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center >";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>HISTORIA</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>INGRESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA INGRESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>FECHA EGRESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>TIPO DE EGRESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DX PPAL</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DESCRIPCION DX</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>COD MEDICO DX PPAL</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>NOMBRE MEDICO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>ESPECIALIDAD DX PPAL MEDICO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DESCRIPCION ESPECIALIDAD</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>COD MEDICO TRATANTE</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>MEDICO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>ESPECILIDAD MEDICO TRATANTE</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DESCRIPCION ESPECIALIDAD</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>SERVICIO EGRESO</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>DESCRIPCION</b></td>";
				   echo "</tr>";
				   
					$query = "select Inghis,Ingnin,Ingfei,Egrfee,Egrcae,Diacod,r11.Descripcion,Diamed,t1.nombre,
									 Diaesm,m44.Espnom,Espmed,t2.nombre,d.Espcod,m.Espnom,Servicio,cconom 
							   from cliame_000101 a,movhos_000033 f
									left join
									movhos_000011 g on (Servicio = ccocod  )
									,cliame_000108 b,cliame_000109 c
									left join
									tempomovhos48 t1 on (Diamed = t1.Meddoc )
									left join 
									root_000011 r11 on ( c.Diacod = r11.Codigo    )
									left join 
									movhos_000044 m44 on (c.Diaesm = m44.Espcod   ),
									cliame_000111 d
									left join 
									tempomovhos48 t2 on (Espmed = t2.Meddoc )
									left join 
									movhos_000044 m on (d.Espcod = m.Espcod   )
							  where f.Fecha_data Between '".$fecha1."' and '".$fecha2."' 
							 		 and  Servicio IN ('1021','1020','1179','1180','1182','1183','1184','1185','1186','1189','1190','1281','1282','1283','1284','1285') 
									 and  Tipo_egre_serv in ('alta','MUERTE MENOR A 48 HORAS','MUERTE MAYOR A 48 HORAS')
									 and  Historia_clinica = Inghis
									 and  Num_ingreso = Ingnin    
									 and  Historia_clinica = Egrhis
									 and  Num_ingreso = Egring 
									 and  Egrhis = Diahis 
									 and  Egring = Diaing 
									 and  Diatip = 'P' 
									 and  Diahis = Esphis 
									 and  Diaing = Esping 
									 and  Esptip = 'P'   "; 
								   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[7]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[8]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[9]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[10]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[11]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[12]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[13]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[14]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[15]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[16]."</td>";
					   echo "</tr>";
					   $LineaDatos1 = $registroB[0]."|".$registroB[1]."|".$registroB[2]."|".$registroB[3]."|".$registroB[4]."|".$registroB[5]."|".$registroB[6]."|".$registroB[7]."|".$registroB[8]."|".$registroB[9]."|".$registroB[10]."|".$registroB[11]."|".$registroB[12]."|".$registroB[13]."|".$registroB[14]."|".$registroB[15]."|".$registroB[16];
					   fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;
					
				case 'movhos67':
					echo "Query Historial Ocupacion Habitaciones (movhos 000067)";
					echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
				   
				   fwrite($archivo, "Fecha Data|Hora Data|Codigo habitacion|Centro de costos|Historia clinica|Ingreso|Alistamiento|Disponible|Estado|Habpro|Fecha de alistamiento|Hora alistamiento|Habprg|Temporal|Tipo de Habitacion-Clinico|Tipo de Habitacion-facturacion" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center >";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha Data</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Hora Data</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Codigo habitacion</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Centro de costos</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Historia clinica</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Ingreso</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Alistamiento</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Disponible</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Estado</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Habpro</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha de alistamiento</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Hora alistamiento</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Habprg</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Temporal</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Tipo de Habitacion-Clinico</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Tipo de Habitacion-facturacion</b></td>";
				   echo "</tr>";
				   
					$query = "select Fecha_data,Hora_data,Habcod,Habcco,Habhis,Habing,Habali,Habdis,Habest,Habpro,Habfal,Habhal,Habprg,Habtmp,Habtip,Habtfa
							  from movhos_000067
							  where Fecha_data Between '".$fecha1."' and '".$fecha2."'    "; 
								   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[7]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[8]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[9]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[10]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[11]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[12]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[13]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[14]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[15]."</td>";
					   echo "</tr>";
					   $LineaDatos1 = $registroB[0]."|".$registroB[1]."|".$registroB[2]."|".$registroB[3]."|".$registroB[4]."|".$registroB[5]."|".$registroB[6]."|".$registroB[7]."|".$registroB[8]."|".$registroB[9]."|".$registroB[10]."|".$registroB[11]."|".$registroB[12]."|".$registroB[13]."|".$registroB[14]."|".$registroB[15];
					   fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;
					
				case 'movpachos':
					echo "Query Movimiento Pacientes en Hospitalizacion";
					echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
				   fwrite($archivo, "Historia Clinica|Numero Ingreso|Procedencia|Servicio|Fecha Ingreso|Hora Ingreso|Fecha Egreso|Hora Egreso|Tipo Egreso|Fecha Ingreso Clinica" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center >";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Historia Clinica</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Numero Ingreso</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Procedencia</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Servicio</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha Ingreso</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Hora Ingreso</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha Egreso</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Hora Egreso</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Tipo Egreso</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha Ingreso Clinica</b></td>";
				   echo "</tr>";
				   
				  	$query = "select a.Historia_clinica,a.Num_ingreso,a.procedencia,a.Servicio,a.Fecha_ing,a.Hora_ing,
						             c.Fecha_egre_serv,c.Hora_egr_serv,c.Tipo_egre_serv,b.Ingfei
							  from movhos_000032 a
									 left join 
									 cliame_000101 b on (a.Historia_clinica = b.Inghis and  a.Num_ingreso = b.Ingnin )
									 left join 
									 movhos_000033 c on (a.Historia_clinica = c.Historia_clinica and a.Num_ingreso = c.Num_ingreso 
											             and  a.Servicio = c.Servicio and a.Fecha_ing <= c.Fecha_egre_serv )
							  where c.Fecha_data Between '".$fecha1."' and '".$fecha2."'  
							   and  c.Servicio in ('1021','1020','1179','1180','1182','1183','1184','1185','1186','1189','1190','1281','1282','1283','1284','1285')
							  group by a.Historia_clinica,a.Num_ingreso,a.Fecha_ing
							  order by a.Historia_clinica,a.Num_ingreso,a.Fecha_ing,c.Fecha_egre_serv "; 
								   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[7]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[8]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[9]."</td>";
	     			   echo "</tr>";
					   $LineaDatos1 = $registroB[0]."|".$registroB[1]."|".$registroB[2]."|".$registroB[3]."|".$registroB[4]."|".$registroB[5]."|".$registroB[6]."|".$registroB[7]."|".$registroB[8]."|".$registroB[9];
					   fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;
					
				case 'movcirliq':
					echo "Cirugias Liquidadas";
					echo "<hr>";
				   // Detalle o titulos de los campos de la tabla
				   fwrite($archivo, "Fecha Liquidacion|Turno|Historia|Ingreso|Tipo Doc|Documento|Nombre|Fecha Cirugia|Tipo|Procedimiento|Nit|Descripcion|Valor" ); 
				   fwrite($archivo, chr(13).chr(10) );   
				   echo "<table border=1 align=center >";
				   echo "<tr>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha Liquidacion</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Turno</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Historia</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Ingreso</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Tipo Doc</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Documento</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Nombre</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Fecha Cirugia</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Tipo</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Procedimiento</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Nit</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Descripcion</b></td>";
				   echo "<td bgcolor=#006699 align=center><font text color=#FFFFFF size=1><b>Valor</b></td>";
				   echo "</tr>";
				   
				  	$query = "select a.Fecha_data,Liqtur,Liqhis,Liqing,Turtdo,Turdoc,Turnom,Turfec,Turtcx,Turcir,Tureps,Empnom,SUM(Liqvlf) 
							  from   cliame_000198 a
									 inner join 
									 tcx_000011 b on (a.Liqtur = b.Turtur)
									 left join 
									 cliame_000024 c on (b.Tureps = c.Empcod)
							  where  a.Fecha_data Between '".$fecha1."' and '".$fecha2."'  
							    and  a.Liqest = 'on' 
								and  a.Liqfac = 'S'
							  group by a.Fecha_data,Liqtur,Liqhis,Liqing,Turtdo,Turdoc,Turnom,Turfec,Turtcx,Turcir,Tureps,Empnom "; 
								   
				   $resultadoB = mysql_query($query);            // Ejecuto el query 
				   $nroreg = mysql_num_rows($resultadoB);
							   
				   for ($i=1;$i<=$nroreg;$i++)
					{
					   $registroB = mysql_fetch_array($resultadoB);  
					   if (is_int ($i/2))
						  {
						   $wcf="DDDDDD";  // color de fondo
						  }
					   else
						  {
						   $wcf="CCFFFF"; // color de fondo
						  }
					   echo "<Tr bgcolor='".$wcf."'>";
					   echo "<td align=center><font   size=1>".$registroB[0]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[1]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[2]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[3]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[4]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[5]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[6]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[7]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[8]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[9]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[10]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[11]."</td>";
					   echo "<td align=center><font   size=1>".$registroB[12]."</td>";
	     			   echo "</tr>";
					   $LineaDatos1 = $registroB[0]."|".$registroB[1]."|".$registroB[2]."|".$registroB[3]."|".$registroB[4]."|".$registroB[5]."|".$registroB[6]."|".$registroB[7]."|".$registroB[8]."|".$registroB[9]."|".$registroB[10]."|".$registroB[11]."|".$registroB[12];
					   fwrite($archivo, $LineaDatos1.chr(13).chr(10) );
					}
					echo "</table>";
					fclose($archivo);
					echo "<li><A href='plano01.txt'>Presione Clic Derecho Para Bajar El Archivo ...</A>";
					echo "<br>";
					echo "<li>Registros generados: ".$nroreg;
					break;
					
					default:
					echo "Debes seleccionar el Query a Ejecutar";
					echo "<A HREF='rep_gesis1.php'>Pagina Principal</A>";
					break;
					
			}
			 
		}
     echo "<hr>";		
   }
   else //este es el contro de no generar querys con fechas mayores a 31 dias
   {
	   echo "NO PERMITE CONSULTAS MAYORES A 31 DIAS LLAME A SISTEMAS PARA QUE LE GENEREN EL QUERY!!!";
	   
   }
   
}
?>

[ <A HREF='rep_gesis1.php'>Pagina Principal</A> ]
			  </div>
		</div>
	</div>
</div>
</BODY>
</HTML>
