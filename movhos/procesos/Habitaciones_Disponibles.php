<?php
include_once("conex.php");
session_start();

if (!isset($consultaAjax))
{
?>
<html lang="es-ES">
<head>
  <title>HABITACIONES DISPONIBLES</title>
</head>
<body >
<BODY TEXT="#000000">
<style>
	.enlinea{
		display:inline-block;
		margin-left:10px;
		vertical-align: top;
		display: -moz-inline-stack; /* FF2*/
		zoom: 1; /* IE7 (hasLayout)*/
		*display: inline; /* IE */
	}
</style>
<script type="text/javascript" src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js"></script>
	<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
	<script type="text/javascript" src="../../../include/root/jquery.blockUI.js"></script>

	<script type="text/javascript" src="../../../include/root/jquery.dimensions.js"></script>
	<script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>

    <link type="text/css" href="../../../include/root/jatt.css" rel="stylesheet" />
	<script type="text/javascript" src="../../../include/root/jquery.jatt.js"></script>

<script type="text/javascript">

	function cambiar_ocupacion(wemp_pmla,wbasedato,whab,wtipo, posicion, selectsede){

     $.blockUI({ message:	'<img src="../../images/medical/ajax-loader.gif" >',
						css: 	{
									width: 	'auto',
									height: 'auto'
								}
				 });

    $.post("Habitaciones_Disponibles.php",
            {
                    consultaAjax:   	'cambiar_ocupacion',
					wemp_pmla:      	wemp_pmla,
                    $selectsede:        selectsede,
					whab :      		whab,
					wbasedato:			wbasedato,
                    wtipo :         	wtipo


            }
            ,function(data_json) {

                if (data_json.error == 1)
                {

                }
                else
                {
					if(wtipo == 'ocupar'){
					$('#td_wocupada_'+posicion).html('<font color="ffffff"><b>ASIGNADA</b></font>');
					$('#td_wocupada_'+posicion).css('background-color','5959AB');
					}else{
					$('#td_wocupada_'+posicion).removeAttr('bgcolor');
					$('#td_wocupada_'+posicion).removeAttr('background-color');
					$('#td_wocupada_'+posicion).removeAttr('style');
					$('#td_wocupada_'+posicion).html("<input id=wocupada_"+posicion+" type=radio onclick=cambiar_ocupacion('"+wemp_pmla+"','"+wbasedato+"','"+whab+"','ocupar','"+posicion+"', '"+selectsede+"')>");
					$('#wcancela_'+posicion).attr('checked', false);
					}

                    $.unblockUI();
                }

			},
			"json"
		);
	}

	function enter()
	 {
	   document.forms.habitaciones.submit();
     }

	function cerrarVentana()
	 {
      window.close()
     }


    $(document).on('change','#selectsede',function(){
        window.location.href = "Habitaciones_Disponibles.php?wbasedato="+$('#wbasedato').val()+"&wemp_pmla="+$('#wemp_pmla').val()+"&selectsede="+$('#selectsede').val()
    });

</script>
<?php
}
   /**************************************************
	*	         HABITACIONES DISPONIBLES            *
	*				CONEX, FREE => OK				 *
	**************************************************/
//=================================================================================================================================
// Enero 28 de 2020 (Jerson trujillo): Se agrega texto para informar "Cantidad de Habitaciones Disponibles:  | En alistamiento:  | TOTAL"
//=================================================================================================================================
//Febrero 8 de 2017 (Arleyda Insignares)
//Se modifica consulta que usa la tabla costosyp_000005 para que filtre el campo ccoemp con la variable wamp_pmla.
//=================================================================================================================================
//Noviembre 13 de 2014 (Camilo zapata)
//Se modifican los querys para que usen la tabla movhos_000011 para los centros de costos y que es omita todo lo que provenga del centro de costos
//de urgencias
//=================================================================================================================================
//Enero 16 de 2014 (Jonatan Lopez)
//Se usa jquery para la accion de ocupar y desocupar las camas, se inhabilita el radio button de cancelar ocupacion si la habitacion esta ocupada,
//pero no le han marcado cumplimiento.
//=================================================================================================================================
//Diciembre 19 de 2012 (Frederick Aguirre S.)
//Se agrega una nueva tabla que muestre las diferencias de las habitaciones entre Unix y Matrix
//=================================================================================================================================
//Diciembre 17 de 2012 Jonatan Lopez
//Se agrega una consulta unida a la consulta principal de habitacion para que muestre las habitacion que estan ocupadas pero sin llegada y cumplimiento
//en la central de solicitud de camas .

//=================================================================================================================================
//Diciembre 10 de 2012 Jonatan Lopez
//Se habilita de nuevo el rdio buton y el cancelar proceso de ocupacion ya que en la anterior modificacion se habian deshabilitado.
//=================================================================================================================================
//Diciembre 04 de 2012 Jonatan Lopez
//Se inactiva el radio buton de ocupar habitacion y se quita la columna de cancelar ocupacion.
//=================================================================================================================================

if(!isset($_SESSION['user']) and !isset($user)) //Se activa para presentaciones con Diapositivas
   echo "error usuario no registrado";
else
   {  
    include_once("root/comun.php");

    header('Content-type: text/html;charset=ISO-8859-1');

    if (is_null($selectsede)){
       $selectsede = consultarsedeFiltro();
    }

    $wactualiz="Febrero 11 de 2022";

  	$key = substr($user,2,strlen($user));

  	if (strpos($user,"-") > 0)
        $wusuario = substr($user,(strpos($user,"-")+1),strlen($user));

    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// ACA SE COLOCA EL HORARIO DE ATENCION EN CENTRAL DE CAMILLEROS POR MATRIX
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	$whora_atencion="LAS 24 HORAS DE LUNES A DOMINGO";
	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	//Esta funcion permite el cambio de estado de la habitacion, la marca como ocupada o desocupada.
	function cambiar_ocupacion($wbasedato, $whab, $wtipo){

		global $wemp_pmla;
		global $conex;

		 $datamensaje = array('mensaje'=>'', 'error'=>0);

		if ($wtipo == 'ocupar'){ //Marco la habitacion en proceso de ocupacion

			$q = " UPDATE ".$wbasedato."_000020 "
		        ."    SET habpro  = 'on' "
		        ."  WHERE habcod  = '".$whab."'"
		        ."    AND habpro != 'on' ";
		    $res1 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			$wactualizado = mysql_affected_rows();

			if($wactualizado == 0){
			$datamensaje['error'] = 1;
			$datamensaje['mensaje'] = "La habitacion no se marco como ocupada";
			}
		}else{  //Desmarco la habitacion en proceso de ocupacion
			$q = " UPDATE ".$wbasedato."_000020 "
		        ."    SET habpro = 'off' "
		        ."  WHERE habcod = '".$whab."'"
		        ."    AND habpro = 'on' ";
		    $res1 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
			$wactualizado = mysql_affected_rows();

			if($wactualizado == 0){
			$datamensaje['error'] = 1;
			$datamensaje['mensaje'] = "La habitacion no se marco como ocupada";
			}
		}

		echo json_encode($datamensaje);

	}


	function pacientesenTraslado()
	 {
	    global $wemp_pmla;
		global $wbasedato;
		global $conex;
		global $whora;
		global $selectsede;

		$sFiltrarSede = consultarAliasPorAplicacion($conex, $wemp_pmla, "filtrarSede");
		$querySede = ( $sFiltrarSede == 'on' && $selectsede != '' ) ? " AND Ccosed = '{$selectsede}' " : "";


	    //===================================================================================================================================================
		// ACA TRAIGO LAS HABITACIONES QUE ESTEN EN PROCESO DE ALTA
		//===================================================================================================================================================
		$q = "  SELECT ubisan, Ubihan, Habhis, Habing, pacno1, pacno2, pacap1, pacap2, ubihac "
			."    FROM ".$wbasedato."_000018, ".$wbasedato."_000020, root_000036, root_000037, ".$wbasedato."_000011 "
			."   WHERE habest  = 'on' "
			."     AND habhis  = ubihis "
			."     AND habing  = ubiing "
			."     AND habhis  = orihis "
			."     AND oriori  = '".$wemp_pmla."'"
			."     AND oriced  = pacced "
			."     AND oritid  = pactid "
			."     AND habcco  = ccocod "
            . $querySede
			."     AND ccohos  = 'on' "
			."     AND ccourg != 'on' "
			."     AND ubiptr  = 'on' "
			."     AND ubiald != 'on' "
			."   ORDER BY 9 ";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($res);

		//echo "<br><br>";
		echo "<table border=0 class='enlinea'>";
		echo "<tr><td align=center colspan=13 bgcolor=#cccccc><font size=2 text color=#CC0000>&nbsp</font></td></tr>";
		echo "<tr class=seccion1><td align=center colspan=13><font size=5><b>PACIENTES EN PROCESO DE TRASLADO</b></font></td></tr>";
		echo "<tr><td align=center colspan=13 bgcolor=#cccccc><font size=2 text color=#CC0000>&nbsp</font></td></tr>";


		echo "<tr><td align=left colspan=3><font size=2><b>Hora: ".$whora."</b></font></td><td align=left colspan=10><font size=2><b>Cantidad de traslados: ".$num."</b></font></td></tr>";

		echo "<tr class=encabezadoTabla>";
		echo "<th><b>Unidad<br>Origen</b></th>";
		echo "<th><b>Habitaci&oacute;n<br>Origen</b></th>";
		echo "<th><b>Historia</b></th>";
		echo "<th><b>Ingreso</b></th>";
		echo "<th><b>Paciente</b></th>";
		echo "<th><b>Habitaci&oacute;n<br>Destino</b></th>";
		echo "</tr>";

		$wclass="fila1";
		for ($i=1;$i<=$num;$i++){
			$wcolor="";
			$row = mysql_fetch_array($res);

			if ($wclass=="fila1")
			   $wclass="fila2";
			  else
				 $wclass="fila1";

			echo "<tr class=".$wclass.">";
			echo "<td align=center>".$row[0]."</td>";                  					  //Codigo Centro de Costo
			echo "<td align=center><b>".$row[1]."</b></td>";            				  //Habitacion Anterior
			echo "<td align=center >".$row[2]."</td>";                  				  //Historia
			echo "<td align=center>".$row[3]."</td>";      					              //Ingreso
			echo "<td align=left>".utf8_decode($row[4])." ".utf8_decode($row[5])." ".utf8_decode($row[6])." ".utf8_decode($row[7])."</td>";   //Paciente
			echo "<td align=center><b>".$row[8]."</b></td>";            				  //Habitacion Destino
			echo "</tr>";
		   }
		echo "</table>";
	  }




	function pacientesdeAlta()
	  {
	    global $wemp_pmla;
		global $wbasedato;
		global $conex;
		global $whora;
		global $selectsede;

        $sFiltrarSede = consultarAliasPorAplicacion($conex, $wemp_pmla, "filtrarSede");
        $querySede = ( $sFiltrarSede == 'on' && $selectsede != '' ) ? " AND Ccosed = '{$selectsede}' " : "";


	    //===================================================================================================================================================
		// ACA TRAIGO LAS HABITACIONES QUE ESTEN EN PROCESO DE ALTA
		//===================================================================================================================================================
		$q = "  SELECT Habcco, Habcod, Habhis, Habing, pacno1, pacno2, pacap1, pacap2 "
			."    FROM ".$wbasedato."_000018, ".$wbasedato."_000020, root_000036, root_000037, ".$wbasedato."_000011 "
			."   WHERE habest  = 'on' "
			."     AND habhis  = ubihis "
			."     AND habing  = ubiing "
			."     AND habhis  = orihis "
			."     AND oriori  = '".$wemp_pmla."'"
			."     AND oriced  = pacced "
			."     AND oritid  = pactid "
			."     AND habcco  = ccocod "
            . $querySede
			."     AND ccohos  = 'on' "
			."     AND ccourg != 'on' "
			."     AND ubialp  = 'on' "
			."     AND ubiald != 'on' "
			."   ORDER BY 2 ";
		$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($res);

		//echo "<br><br>";
		echo "<table border=0 class='enlinea'>";
		echo "<tr><td align=center colspan=13 bgcolor=#cccccc><font size=2 text color=#CC0000>&nbsp</font></td></tr>";
		echo "<tr class=seccion1><td align=center colspan=13><font size=5><b>PACIENTES EN PROCESO DE ALTA</b></font></td></tr>";
		echo "<tr><td align=center colspan=13 bgcolor=#cccccc><font size=2 text color=#CC0000>&nbsp</font></td></tr>";


		echo "<tr><td align=left colspan=3><font size=2><b>Hora: ".$whora."</b></font></td><td align=left colspan=10><font size=2><b>Cantidad : ".$num."</b></font></td></tr>";

		echo "<tr class=encabezadoTabla>";
		echo "<th><b>Unidad</b></th>";
		echo "<th><b>Habitaci&oacute;n</b></th>";
		echo "<th><b>Historia</b></th>";
		echo "<th><b>Ingreso</b></th>";
		echo "<th><b>Paciente</b></th>";
		echo "</tr>";

		$wclass="fila1";
		for ($i=1;$i<=$num;$i++)
		{
			$wcolor="";

			$row = mysql_fetch_array($res);

			if ($wclass=="fila1")
			   $wclass="fila2";
			  else
				 $wclass="fila1";

			echo "<tr class=".$wclass.">";
			echo "<td align=center>".$row[0]."</td>";                  					  //Codigo Centro de Costo
			echo "<td align=center><b>".$row[1]."</b></td>";            				  //Habitacion
			echo "<td align=center >".$row[2]."</td>";                  				  //Historia
			echo "<td align=center>".$row[3]."</td>";      					              //Ingreso
			echo "<td align=left>".utf8_decode($row[4])." ".utf8_decode($row[5])." ".utf8_decode($row[6])." ".utf8_decode($row[7])."</td>";   //Paciente
			echo "</tr>";
		}
		echo "</table>";

	  }

	//19 Diciembre 2012
	function buscarPaciente($whis){
		global $conex;
		global $wemp_pmla;

		$query_info = "
			SELECT  datos_paciente.Pactid ,pacientes_id.Oriced,"
				."  datos_paciente.Pacno1, datos_paciente.Pacno2,"
				."  datos_paciente.Pacap1, datos_paciente.Pacap2"
		  ."  FROM  root_000037 as pacientes_id, root_000036 as datos_paciente"
		  ." WHERE  pacientes_id.Orihis = '".$whis."'"
		  ."   AND  pacientes_id.Oriori =  '".$wemp_pmla."'"
		  ."   AND 	pacientes_id.Oriced = datos_paciente.Pacced"
		  ."   AND 	Oritid = Pactid";

		$res_info = mysql_query($query_info, $conex);
		$row_datos = mysql_fetch_array($res_info);
		$nombres_pac = trim($row_datos['Pacno1'].' '.$row_datos['Pacno2'].' '.$row_datos['Pacap1'].' '.$row_datos['Pacap2']);
		return $nombres_pac;
	}

	//Este segmento interactua con los llamados ajax

//Si la variable $consultaAjax tiene datos entonces busca la funcion que trae la variable.
if (isset($consultaAjax))
            {
            switch($consultaAjax)
                {

                    case 'cambiar_ocupacion':
                        {
                            echo cambiar_ocupacion($wbasedato, $whab, $wtipo);
                        }
                    break;

                    default : break;
                }
            return;
            }

if (!isset($consultaAjax))
{
    //===================================================================================================================================================
    //COMIENZA LA FORMA
    echo "<form name=habitaciones action='Habitaciones_Disponibles.php' method=post>";

    echo "<input type='HIDDEN' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
    echo "<input type='HIDDEN' id='wbasedato' name='wbasedato' value='".$wbasedato."'>";
    echo "<input type='HIDDEN' id='sede' name='selectsede' value='".$selectsede."'>";
    if (isset($wini)) echo "<input type='HIDDEN' name='wini' value='".$wini."'>";

    $wfecha=date("Y-m-d");
    $whora = (string)date("H:i:s");

    echo "<HR align=center></hr>";

    //--> centro de costos de urgencias
    $centro_costoUr = consultarCentrocoUrgencias('',$selectsede);
//    $q  = " select Ccocod from {$wbasedato}_000011 where ccourg = 'on' and Ccoest = 'on'";
//    $rs = mysql_query( $q, $conex );
//    while( $row = mysql_fetch_assoc( $rs ) ){
//        $ccourg = $row['Ccocod'];
//    }
    $ccourg = $centro_costoUr->codigo;

    //Traigo la tabla del Centro de Costo a partir de la base de datos y del usuario
    $q = " SELECT Emptcc "
        ."   FROM root_000050 "
        ."  WHERE empcod = '".$wemp_pmla."'"
        ."    AND empest = 'on' ";
    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
    $num = mysql_num_rows($res);
    $row = mysql_fetch_array($res);

    $wtabcco=$row[0];

	//===================================================================================================================================================
    // QUERY PRINCIPAL
    //===================================================================================================================================================
    // ACA TRAIGO TODAS LAS HABITACIONES DISPONIBLES
    //===================================================================================================================================================

    $wcencam = consultarAliasPorAplicacion($conex, $wemp_pmla, 'Camilleros');
    $wcentral_camas = consultarAliasPorAplicacion($conex, $wemp_pmla, 'CentralCamas');

    $sFiltrarSede = consultarAliasPorAplicacion($conex, $wemp_pmla, "filtrarSede");
    $querySede = ( $sFiltrarSede == 'on' && $selectsede != '' ) ? " AND Ccosed = '{$selectsede}' " : "";
	
	$sqlAlis = "  
	SELECT count(*) as c 
	  FROM ".$wbasedato."_000020, ".$wbasedato."_000011 
	 WHERE Habest = 'on' 
	   AND habali = 'on' 
	   {$querySede}
	   AND Habcco = Ccocod 
	   AND Ccourg != 'on' ";
	   
	$resAlis 	= mysql_query($sqlAlis, $conex) or die (mysql_errno()." - ".mysql_error());
	$rowAlis 	= mysql_fetch_array($resAlis);
	$habEnAlis 	= $rowAlis['c']; 
			

    $q = "  SELECT Habcco, Cconom, Habcod, Habpro, '1' as tipo "
        ."    FROM ".$wbasedato."_000020, ".$wbasedato."_000011 B "
	    ."   WHERE habali = 'off' "
	    ."     AND habdis = 'on' "
	    ."     AND habest = 'on' "
        . $querySede
	    ."     AND habcco = ccocod "
	    ."     AND ccourg = 'off'"
        ."   UNION "
        ."  SELECT Habcco, Cconom, Habcod, Habpro, '2' as tipo "
        ."    FROM ".$wbasedato."_000020,".$wbasedato."_000018, ".$wcencam."_000003,".$wcencam."_000010 , ".$wbasedato."_000011 B "
	    ."   WHERE habali = 'off' "
	    ."     AND habdis = 'off' "
        ."     AND habpro = 'on' "
        ."     AND habhis = ubihis"
        ."     AND habing = ubiing"
        . $querySede
        ."     AND habcco = ubisac"
	    ."     AND habcco = ccocod "
	    ."     AND ccourg = 'off'"
        ."     AND ubiptr != 'on' "
        ."     AND historia = habhis "
        ."     AND Hab_asignada = habcod "
        ."     AND Fecha_Cumplimiento = '0000-00-00' "
		."     AND Hora_Cumplimiento = '00:00:00' "
        ."     AND ".$wcencam."_000003.id = ".$wcencam."_000010.acaids "
        ."     AND Acarea = 'off' "
        ."     AND Central = '".$wcentral_camas."' "
        ."     AND Anulada='No' "
        ."GROUP BY Habcod"
	    ."   ORDER BY 3,1 ";
	$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($res);

	encabezado("HABITACIONES DISPONIBLES",$wactualiz, "clinica", true);

    echo "<center><table border=0>";
    echo "<tr><td colspan=3><font size=2><b>Hora: ".$whora."</b></font></td><td align=left bgcolor=#fffffff colspan=10><font size=2><b>Cantidad de Habitaciones Disponibles:</b> ".$num." | <b>En alistamiento:</b> ".$habEnAlis." | <b>TOTAL : </b>".((int)$num+(int)$habEnAlis)."</b></td></tr>";

    echo "<tr class=encabezadoTabla>";
    echo "<th colspan=2><b>Unidad</b></th>";
    echo "<th><b>Habitacion</b></th>";
    echo "<th><b>En Proceso de Ocupaci&oacute;n</b></th>";
    echo "<th><b>Cancelar Proceso<br>de Ocupaci&oacute;n</b></th>";
    echo "</tr>";

    for ($i=1;$i<=$num;$i++)
    {
	    $row = mysql_fetch_array($res);

	    if (is_integer($i/2))
           $wclass="fila1";
          else
             $wclass="fila2";

	    echo "<tr class=".$wclass.">";
	    echo "<td align=center>".$row[0]."</td>";
	    echo "<td align=left >".$row[1]."</td>";
	    echo "<td align=center ><b>".$row[2]."</b></td>";
	    $disabled = '';
	    if ($row[3]=='on')
			//Habitacion asignada
             if ($row[4] == '1')
             {
	         echo "<td id='td_wocupada_$i' align=center bgcolor=5959AB><font color=ffffff><b>ASIGNADA</b></font></td>";
             }
			 //Habitacion ocupada sin llegada y cumplimiento en la central de camas
             else
             {
              echo "<td id='td_wocupada_$i' align=center bgcolor=5959AB><font color=YELLOW><b>Paciente recibido favor marcar <br> cumplimiento en la central <br> de solicitud de camas</b></font></td>";
			  $disabled = 'disabled';
             }
	      else{
	         echo "<td id='td_wocupada_$i' align=center><INPUT TYPE='radio' id='wocupada_$i' onclick='cambiar_ocupacion(\"$wemp_pmla\",\"$wbasedato\",\"".trim($row['Habcod'])."\",\"ocupar\", \"$i\", \"$selectsede\");'></td>";
			 }

	       echo "<td id='td_wcancela_$i' align=center><INPUT TYPE='radio' id='wcancela_$i' $disabled onclick='cambiar_ocupacion(\"$wemp_pmla\",\"$wbasedato\",\"".trim($row['Habcod'])."\",\"desocupar\", \"$i\", \"$selectsede\");'></td>";

	    echo "</tr>";
	}
	echo "</table>";
    echo "</form>";


    if (!isset($wocu))
       $wocu=" ";

	$wini="N";
	echo "<input type='HIDDEN' name='wini' value='".$wini."'>";


	//===================================================================================================================================================
    // ACA TRAIGO LAS HABITACIONES QUE ESTAN EN LA CENTRAL DE HABITACIONES
    //===================================================================================================================================================
    if ($wtabcco == 'costosyp_000005'){

    	$q = "  SELECT Habcco, A.Cconom, Habcod, Habpro, Habhal, Ccoase, Habprg "
	        ."    FROM ".$wbasedato."_000020, ".$wtabcco." A, ".$wbasedato."_000011 B "
		    ."   WHERE habest = 'on' "
		    ."     AND habcco = A.ccocod "
		    ."     AND habali = 'on' "
            . $querySede
		    ."     AND habcco = B.ccocod "
		    ."	   AND ccourg != 'on'"
		    ."     AND A.ccoemp = '".$wemp_pmla."' "
		    ."   ORDER BY 3, 1 ";
	}	    
    else{ 

	    $q = "  SELECT Habcco, A.Cconom, Habcod, Habpro, Habhal, Ccoase, Habprg "
	        ."    FROM ".$wbasedato."_000020, ".$wtabcco." A, ".$wbasedato."_000011 B "
		    ."   WHERE habest = 'on' "
		    ."     AND habcco = A.ccocod "
		    ."     AND habali = 'on' "
            . $querySede
		    ."     AND habcco = B.ccocod "
		    ."	   AND ccourg != 'on'"
		    ."   ORDER BY 3, 1 ";
	}
	$res = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	$num = mysql_num_rows($res);

	echo "<br><br>";
	echo "<table border=0>";
	echo "<tr><td align=center colspan=13 bgcolor=#cccccc><font size=2 text color=#CC0000>&nbsp</font></td></tr>";
	echo "<tr class=seccion1><td align=center colspan=13><font size=5><b>HABITACIONES EN CENTRAL DE HABITACIONES</b></font></td></tr>";
	echo "<tr><td align=center colspan=13 bgcolor=#cccccc><font size=2 text color=#CC0000>&nbsp</font></td></tr>";


	echo "<tr><td align=left colspan=3><font size=2><b>Hora: ".$whora."</b></font></td><td align=left colspan=10><font size=2><b>Cantidad de Habitaciones en la Central de Habitaciones: ".$num."</b></font></td></tr>";

	echo "<tr class=encabezadoTabla>";
	echo "<th colspan=2><b>Unidad</b></th>";
    echo "<th><b>Habitaci&oacute;n</b></th>";
    echo "<th><b>Entro a Central</b></th>";
    echo "<th><b>Empleado Asignado</b></th>";
    echo "<th><b>Hora de Asignaci&oacute;n</b></th>";
    echo "<th><b>Observaciones</b></th>";
    echo "</tr>";

	for ($i=1;$i<=$num;$i++)
    {
	    $wcolor="";

	    $row = mysql_fetch_array($res);

	    if (is_integer($i/2))
           $wclass="fila1";
        else
           $wclass="fila2";

        if ($row[5] == "on")
           $wcolor="FFFF99";

        if ($row[6] == "on")    //Si la habitacion estaba programada cambio el color del fondo a rojo
           $wcolor="FF9966";


        $q = " SELECT movemp, sgenom, movhem, movobs, movhal "
	        ."   FROM ".$wbasedato."_000025, ".$wbasedato."_000024 "
	        ."  WHERE movhab = '".$row[2]."'"
	        ."    AND movhdi = '00:00:00' "
	        ."    AND movemp = sgecod ";
	    $res1 = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	    $num1 = mysql_num_rows($res1);

	    echo "<tr class=".$wclass.">";
	    echo "<td align=center bgcolor=".$wcolor.">".$row[0]."</td>";                        //Codigo Centro de Costo
	    echo "<td align=left bgcolor=".$wcolor.">".utf8_decode($row[1])."</td>";                          //Nombre Centro de Costo
	    echo "<td align=center bgcolor=".$wcolor."><b>".$row[2]."</b></td>";                 //Habitacion

	    if ($num1 > 0)
	       {
		    $row1 = mysql_fetch_array($res1);
		    echo "<td align=center bgcolor=".$wcolor.">".$row1[4]."</td>";                   //Hora en que entro a la CENTRAL
		    echo "<td align=left bgcolor=".$wcolor.">".utf8_decode($row1[0]." - ".$row1[1])."</td>";      //Empleado Asignado
		    echo "<td align=center bgcolor=".$wcolor.">".$row1[2]."</td>";                   //Hora de Asignacion Empleado

		    echo "<td align=left bgcolor=".$wcolor."><TEXTAREA rows=2 cols=40 READONLY>".utf8_decode($row1[3])."</TEXTAREA></td>";                     //Observaciones
	       }
	      else
	         {
		      echo "<td align=center bgcolor=".$wcolor.">".$row[4]."</td>";
	          echo "<td align=center bgcolor=".$wcolor.">&nbsp</td>";
	          echo "<td align=center bgcolor=".$wcolor.">&nbsp</td>";
	          echo "<td align=center bgcolor=".$wcolor.">&nbsp</td>";
             }
	    echo "</tr>";
	   }
	echo "</table>";
	echo "<br><br>";

	/*
	//Convenciones
    echo "<table border=1 align=center>";
    echo "<caption bgcolor=#ffcc66><b>Convenciones</b></caption>";
    echo "<tr><td colspan=3 bgcolor="."FF9966"."><font size=2 color='"."000000"."'>&nbsp Habitaci? programada</font></td>";   //Rojo
    echo "<td colspan=3 bgcolor="."FFFF99"."><font size=2 color='"."000000"."'>&nbsp Con personal de Aseo</font></td>";    //Amarillo
    echo "<td colspan=3 class=fila1><font size=2 color='"."000000"."'>&nbsp Atenci? Normal</font></td>";         //Verde Claro
    echo "<td colspan=3 class=fila2><font size=2 color='"."000000"."'>&nbsp Atenci? Normal</font></td></tr>";         //Azul
    echo "</table>";
	*/

	pacientesdeAlta();
	pacientesenTraslado();



    echo "<br>";
    echo "<table align=center>";
    echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
    echo "</table>";
	echo "<br><br><br>";


    echo "<meta http-equiv='refresh' content='60;url=Habitaciones_Disponibles.php?wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."&wini=".$wini."&selectsede=".$selectsede."'>";
   }
include_once("free.php");
}
?>
