<?php
include_once("conex.php");
 /**********************************************************************************************************
 *
 * Programa				: rep_vacacionesgral.php
 * Fecha de Creación 	: 2017-09-14
 * Autor				: Arleyda Insignares Ceballos
 * Descripcion			: Reporte Vacaciones: listado de vacaciones disfrutadas y pendientes.
 *                        Tiene opción para exportar en formato CSV.
 *                        Filtro: centro de costos.
 *
 **********************************************************************************************************
 *                       Modificaciones
 *
 *  2017-11-02 - Arleyda Insignares C. Se modifica para generar información de todos los empleados de la
 *               clínica y que el filtro por centro de costos sea opcional .
 **********************************************************************************************************/

 $wactualiz = "2017-11-02";
 ini_set('max_execution_time', 300);

 if(!isset($_SESSION['user'])){
	 echo "<center></br></br><table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
		<tr><td>Error, inicie nuevamente</td></tr>
		</table></center>";
	 return;
 }

 header('Content-type: text/html;charset=ISO-8859-1');

  //***********************************   Inicio  ***********************************************************



  include_once("root/comun.php");


  $conex     = obtenerConexionBD("matrix");
  $conexunix = odbc_connect('queryx7','','') or die("No se realizó Conexion con SQL Software");

  $minimo_dias_vacaciones = consultarAliasPorAplicacion($conex, $wemp_pmla, 'minimo_dias_vacaciones');
  $WMAXIMO_DIAS           = 15;
  $WMINIMO_DIAS           = $minimo_dias_vacaciones;
  $wbasedato              = consultarAliasPorAplicacion($conex, $wemp_pmla, 'nomina');
  $wbasetalhuma           = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');
  $wcontratos             = consultarAliasPorAplicacion($conex, $wemp_pmla, 'contratosorganigrama');
  $wbasecentro            = consultarAliasPorAplicacion($conex, $wemp_pmla, 'COSTOS');
  $arr_servicio           = consultarCentros ($wbasetalhuma,$conex,$wemp_pmla);
  $whora         	      = (string)date("H:i:s");
  $pos           	      = strpos($user,"-");
  $wusuario      		  = substr($user,$pos+1,strlen($user));
  $arr_estados_respuesta  = array("APROBADO"=>"Aprobado", "RECHAZADO"=>"Rechazado");
  define( "diasRiesgo", 30 );
  define( "diasSinRiesgo", 15 );


  // ***************************************    FUNCIONES AJAX  Y PHP  **********************************************

      if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarDetalle")
      {

      	   $arr_disfrutadas = array();

      	   $arr_resultado   = array();

	       // Traer historico SQL Software
		   $q = "   SELECT  pvadetcod, pvadetfci, pvadetfcf, pvadetdia, pvafin, pvaffi, pvadia, pvadetddin
		            FROM    nopvadet, nopva
		            WHERE   pvadetcod = pvacod
		              AND   pvadetsec = pvasec
		            ORDER BY pvadetcod ";

		   $resodbc = odbc_do($conexunix,$q);

		   // Crear una tabla temporal con la información de SQL Software
      	   $maketemp = "
					    CREATE TEMPORARY TABLE TEMP_UNIX (
						      pvadetcod  VARCHAR(10),
						      pvadetfci  VARCHAR(20),
						      pvadetfcf  VARCHAR(20),
						      pvadetdia  VARCHAR(20),
						      pvafin     VARCHAR(20),
						      pvaffi     VARCHAR(20),
						      pvadia     VARCHAR(10),
						      pvadetddin VARCHAR(10)
					    )
					   ";

		   mysql_query($maketemp, $conex) or die (mysql_errno()." - en el query: ".$maketemp." - ".mysql_error());

		   $inserttemp = "
				    INSERT INTO TEMP_UNIX
					      (pvadetcod,pvadetfci,pvadetfcf,pvadetdia,pvafin,pvaffi,pvadia,pvadetddin) values
					";

	  	    // recorrer tabla unix para grabar en tabla temporal
            while( odbc_fetch_row($resodbc) ){

                 $pvadetcod  = utf8_encode(trim(odbc_result($resodbc,'pvadetcod')));
                 $pvadetfci  = utf8_encode(trim(odbc_result($resodbc,'pvadetfci')));
                 $pvadetfcf  = utf8_encode(trim(odbc_result($resodbc,'pvadetfcf')));
                 $pvadetdia  = utf8_encode(trim(odbc_result($resodbc,'pvadetdia')));
                 $pvafin     = utf8_encode(trim(odbc_result($resodbc,'pvafin')));
                 $pvaffi     = utf8_encode(trim(odbc_result($resodbc,'pvaffi')));
                 $pvadia     = utf8_encode(trim(odbc_result($resodbc,'pvadia')));
                 $pvadetddin = utf8_encode(trim(odbc_result($resodbc,'pvadetddin')));

           	     $inserttemp .= "('".$pvadetcod ."','". $pvadetfci ."','". $pvadetfcf ."','". $pvadetdia ."','". $pvafin ."','". $pvaffi ."','". $pvadia ."','". $pvadetddin ."'),";

		    }

		    $inserttemp = substr ($inserttemp, 0, strlen($inserttemp) - 1);

            $resmat = mysql_query($inserttemp,$conex) or die (mysql_errno()." - en el query: ".$inserttemp." - ".mysql_error());


	    // Hago la consulta con la tabla temporal y talhuma_000013
	    $condicion = '';

	    if ($wservicio !='')
	    	$condicion = " And B.Idecco = '".$wservicio."' ";


        $con = "SELECT  concat(B.Ideno1,' ',B.Ideno2,' ',B.Ideap1,' ',B.Ideap2) as Nomemp,
                        B.Ideuse, B.Ideced, B.Idecco, A.pvadetcod, A.pvadetfci, A.pvadetfcf,
                        A.pvadetdia, A.pvafin, A.pvaffi, A.pvadia, A.pvadetddin
               FROM     TEMP_UNIX A
                        Inner join ".$wbasetalhuma."_000013 B on substr(B.Ideuse,1,5) = A.pvadetcod
               WHERE    B.Ideest = 'on' ".$condicion."
               ORDER BY B.Ideuse, A.pvadetfci, A.pvadetfcf";

        $res = mysql_query($con,$conex) or die (mysql_errno()." - en el query: ".$con." - ".mysql_error());

        $num = mysql_num_rows($res);

        if  ($num > 0)
        {

			    $i                         = 1;
			    $wfcumI                    = "";
			    $wfcumF                    = "";
			    $html_disfrutadas          = '';
			    $contcss                   = 0;
			    $wtotdias                  = 0;
			    $control                   = 0;

			    $pila_dias_pendientes      = array();
			    $diasDisfrutadosPeriodo    = array();
			    $liquidaciones             = 0;
			    $cambioPeriodo             = false;
			    $fecha_anterior            = "";
			    $fecha_ahora               = "";
			    $diasLicenciaAnterior      = 0;
			    $diasPendientesPorLiquidar = 0;
			    $dia_despues               = date("Y-m-d", strtotime(date('Y-m-d')."+1 day"));
			    $codigo_anterior           = '';

			    while($row = mysql_fetch_assoc($res))
			    {

			    	$codigo_empleado  =  $row['Ideuse'];
			    	$nombre_empleado  =  $row['Nomemp'];
			    	$cedula_empleado  =  $row['Ideced'];
			    	$counix_empleado  =  $row['pvadetcod'];
			    	$cencos_empleado  =  $row['Idecco'];
			        $pvadetfci        =  substr($row['pvadetfci'],0,10);
			        $pvadetfcf        =  substr($row['pvadetfcf'],0,10);
			        $pvafin           =  substr($row['pvafin'],0,10);
			        $pvaffi           =  substr($row['pvaffi'],0,10);

			        $fecini_cumplido  =  $pvadetfci;
			        $fecfin_cumplido  =  $pvadetfcf;
			        $fecini_disfruta  =  $pvafin;
			        $fecfin_disfruta  =  $pvaffi;

			        $dias_total       = ( ($row['pvadetdia']*1) + ($row['pvadetddin']*1) );
			        $dias_totcal      = ( ($row['pvadia']*1));
			        $dias_totdin      = ( ($row['pvadetddin']*1) );

			        $centro_empleado = (array_key_exists($row['Idecco'],$arr_servicio)) ? $arr_servicio[$row['Idecco']]:'vacio';

			        if ($codigo_empleado == $codigo_anterior){

			        	$codigo_emprep = '';
			        	$nombre_emprep = '';
			        	$centro_emprep = '';
			        }
			        else{
			        	$codigo_emprep = $codigo_empleado;
			        	$nombre_emprep = $nombre_empleado;
			        	$centro_emprep = $centro_empleado;
			        }

			        $arr_disfrutadas[] = array( "codigo_empleado"  => $codigo_emprep,
                                                "nombre_empleado"  => $nombre_emprep,
                                                "centro_empleado"  => $centro_emprep,
                                                "fecini_cumplido"  => $fecini_cumplido,
                                                "fecfin_cumplido"  => $fecfin_cumplido,
                                                "dias_total"       => $dias_total,
                                                "fecini_disfruta"  => $fecini_disfruta,
                                                "fecfin_disfruta"  => $fecfin_disfruta,
                                                "dias_totcal"      => $dias_totcal,
                                                "dias_totdin"      => $dias_totdin );

			        $codigo_anterior = $codigo_empleado;


			      }
	      }

	      $arr_resultado['resultado'] = $arr_disfrutadas;

	      $arr_resultado['total']     = $num;

	      echo json_encode($arr_resultado);

          return;

      }


/****************************  CONSULTAR EMPLEADOS QUE PERTENECEN A UN CENTRO DE COSTOS  ***************************/


     if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarEmpleados"){

     	$arr_empleados = array();
     	$arr_resultado = array();

     	$cont = 0;

     	if ($wservicio !='')
	    	$condicion = " And Idecco = '".$wservicio."' ";

        $con = "SELECT concat(Ideno1,' ',Ideno2,' ',Ideap1,' ',Ideap2) as Nomemp,
                       Ideuse, Ideced, Idecco, Cconom
                FROM   ".$wbasetalhuma."_000013
                INNER JOIN ".$wbasecentro."_000005
                       on Idecco = ccocod
                WHERE  Ideest = 'on'
                       And Idetco != ''
                       And Ideap1 != ''
                       And Idefre = '0000-00-00'
                       And Ccoemp = ".$wemp_pmla."
                       And Idetco not in (".$wcontratos.") ".$condicion."
                ORDER BY Cconom";

        $res = mysql_query($con,$conex) or die (mysql_errno()." - en el query: ".$con." - ".mysql_error());

        $num = mysql_num_rows($res);

        if  ($num > 0)
        {
			    while($row = mysql_fetch_assoc($res))
			    {
			    	$cont++;

			        $arr_empleados[] = array( "codigo"  => $row['Ideuse'],
			                                  "nombre"  => utf8_encode($row['Nomemp']),
			                                  "cedula"  => $row['Ideced'],
			                                  "centro"  => $row['Idecco'],
			                                  "cconom"  => utf8_encode($row['Cconom']));

			    }
	    }

        $arr_resultado['detalle']= $arr_empleados;
        $arr_resultado['total']  = $cont;

	    echo json_encode($arr_resultado);
        return;

     }


     /************************* CONSULTAR VACACIONES PENDIENTES POR CADA EMPLEADO ********************************/

     if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarPendientes2") {


		        $minimo_dias_vacaciones    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'minimo_dias_vacaciones');
		        $WMAXIMO_DIAS              = 15;
		        $WMINIMO_DIAS              = $minimo_dias_vacaciones;
		        $wfecha        		       = date("Y-m-d");

			    $html_tr                   = array();
			    $wtotdias                  = 0;
			    $liquidaciones             = 0;//--> cantidad de iteraciones, liquidaciones presentadas en pantalla
			    $cambioPeriodo             = false;
			    $fecha_anterior            = "";
			    $fecha_ahora               = "";
			    $diasLicenciaAnterior      = 0;

			    $dia_despues               = date("Y-m-d", strtotime(date('Y-m-d')."+1 day"));
			    $diasDisfrutadosPeriodo    = array();
			    $codigo_anterior           = 'INI';
	    	    $pila_dias_pendientes      = array();
	    	    $diasPendientesPorLiquidar = 0;
	    	    $haDisfrutadoVacaciones    = false;

	    	    $codigo_anterior           = substr($wempleado,0,5);
				$codigo_empleado    	   = trim($wempleado);
				$nombre_empleado    	   = $wnombre;
				$cedula_empleado    	   = $wcedula;
				$counix_empleado    	   = substr($wempleado,0,5);
				$cencos_empleado    	   = $wcentro;
				$nomcen_empleado           = $wcconom;


			    $q = "   SELECT   pvadetdia, pvadia, pvadetddin, pvadetcod, max(pvadetfci) as pvadetfci,
			                      max(pvadetfcf) as pvadetfcf
			              FROM    nopvadet, nopva
			             WHERE    pvadetcod = pvacod
			               AND    pvadetsec = pvasec
			               AND    pvadetcod = '".substr($wempleado,0,5)."'
			               AND    pvadetdia >= 15
			             GROUP BY pvadetdia, pvadia, pvadetddin, pvadetcod
			             UNION ALL
			             SELECT   sum(pvadetdia) as pvadetdia, sum(pvadia) as pvadia,
			                      sum(pvadetddin) as pvadetddin, pvadetcod, pvadetfci, pvadetfcf
			              FROM    nopvadet, nopva
			             WHERE    pvadetcod = pvacod
			               AND    pvadetsec = pvasec
			               AND    pvadetcod = '".substr($wempleado,0,5)."'
			               AND    pvadetdia < 15
			             GROUP BY pvadetcod, pvadetfci, pvadetfcf
			             ORDER BY pvadetfci";

		        $resodbc = odbc_do($conexunix,$q);

			    $arrayPeriodosRiesgo      = buscarPeriodosEnRiesgo( $wempleado );

			    $poseeLicencia            = diasLicenciaNoRemuneradagral( $counix_empleado );

                while( odbc_fetch_row($resodbc) )
				{

			    	    $codigo_anterior          =  substr($wempleado,0,5);
				    	$codigo_empleado    	  =  $wempleado;
				    	$nombre_empleado    	  =  $wnombre;
				    	$cedula_empleado    	  =  $wcedula;
				    	$counix_empleado    	  =  substr($wempleado,0,5);
				    	$cencos_empleado    	  =  $wcentro;

				        $pvadetfci          	  =  substr(trim(odbc_result($resodbc,'pvadetfci')),0,10);
				        $pvadetfcf                =  substr(trim(odbc_result($resodbc,'pvadetfcf')),0,10);
				        $wfec_i                   =  $pvadetfci;
	         			$wfec_f                   =  $pvadetfcf;
	         			$haDisfrutadoVacaciones   =  true;

	         			$dias_total               = ((odbc_result($resodbc,'pvadetdia'))*1) + ((odbc_result($resodbc,'pvadetddin'))*1);
				        $dias_totcal              = (odbc_result($resodbc,'pvadia'))*1;
				        $dias_totdin              = (odbc_result($resodbc,'pvadetddin')) ;

	                    $wdiasUltimoPeriodo       =  (((strtotime($wfec_f)-strtotime($wfec_i))/86400)+1);

                        if ( $poseeLicencia ==0 ){

                        	$diasLicencia = 0;

                        	$datosDiasLicencia['diasNoContados'] = 0;
                        }
                        else{

	                        $datosDiasLicencia     = diasLicenciaNoRemunerada( $pvadetfci, $pvadetfcf, $counix_empleado );//mas uno porque se incluye el dia final

		                    $diasLicencia          = $datosDiasLicencia['diasLicencia'];//mas uno porque se incluye el dia final
                        }


	                    $aux                      =  diasDisponiblesPeriodo( $pvadetfci, $pvadetfcf, $cedula_empleado, $arrayPeriodosRiesgo );//dias a los que se tiene


	                    $auxDUP = $wdiasUltimoPeriodo;
				        $auxDUP = $auxDUP  - $diasLicencia;

				        if( ($wdiasUltimoPeriodo == 366 and $diasLicencia == 0 ) or ( $auxDUP == 366 and $diasLicencia > 0 ) ){//--> posiblemente sea un año bisiesto 2016-02-25
				            $wdiasUltimoPeriodo = 365;
				        }

				        $wtotdiasSesgo        = $wdiasUltimoPeriodo - 365;
				        //--> 2016-01-18 esto se hace para que no se sume el doble de los dias de licencia, es decir, no acomodar la fecha si ya estaba liquidada incluyendo el movimiento de fechas en SQL Software
				        if( ( $wtotdiasSesgo > 0 ) ){
				            $diasLicencia = $diasLicencia - $wtotdiasSesgo;
				        }

				        $resdia  = $diasLicencia < 0  ? $diasLicencia .' day' : "+ " . $diasLicencia  . " day";

				        $wfec_f  = date("Y-m-d",strtotime($wfec_f. $resdia));

				        $wfechaFinRealPeriodo = $wfec_f;


				        if( $liquidaciones > 0 ){
				            if( $fecha_anterior != $pvadetfci ){//--> si hubo cambio de fecha de inicio desde la iteración anterior a esta.
				                if( $diasLicenciaAnterior*1 > 0 ){//--> si en la iteración anterior hubo dias de licencia no remunerada, pudo haber generado desplazamiento

				                }else{//--> sino se desplazo el periodo a causa de licencias no remuneradas o suspenciones.
				                    //--> en caso de que haya algo en la pila de dias pendientes, entonces se debe adicionar a lo que no se liquidó y debió liquidarse
				                    if( count($pila_dias_pendientes) >0 ){
				                        //echo "<br> ---> <pre>".print_r( $pila_dias_pendientes, true )."</pre>";
				                        $diasPendientesPorLiquidar += $pila_dias_pendientes[count($pila_dias_pendientes)-1]['diasFaltantesPorDisfrutar'];
				                        array_pop($pila_dias_pendientes);
				                    }
				                }
				            }

				        }
				        $liquidaciones++;
				        $fecha_anterior       = $pvadetfci;
				        $diasLicenciaAnterior = $diasLicencia;

				        $diasDisponibles   = 0;

				        if( !isset( $diasDisfrutadosPeriodo[$counix_empleado."->".$pvadetfci."->".$pvadetfcf] ) ){
				            $diasDisfrutadosPeriodo[$counix_empleado."->".$pvadetfci."->".$pvadetfcf] = 0;
				        }

				        // Dias pendientes de un periodo disfrutado parcialmente

				        $diasDisponiblesReales = 0;

				        foreach ($aux as $keyTipo => $dias ) {

					            $diasDisponiblesPeriodo = calcularDiasGanados( $keyTipo, $dias );
					            $diasLicenciaPeriodo    = calcularDiasGanados( $keyTipo, $datosDiasLicencia['diasNoContados'][$keyTipo] );
					            $diasDisponiblesPeriodo = $diasDisponiblesPeriodo - $diasLicenciaPeriodo;
					            $diasDisponiblesPeriodo = round($diasDisponiblesPeriodo);
					            $diasLicenciaPeriodo    = round($diasLicenciaPeriodo);
					            $diasDisponibles        += ( $diasDisponiblesPeriodo - $diasLicenciaPeriodo );
					            $diasDisponiblesReales  += ( $diasDisponiblesPeriodo - $diasLicenciaPeriodo );
					            $diasDisponibles        =  round( $diasDisponibles );

				        }


				        $diasPendientesDisfrutar    =  $dias_total - $diasDisponibles;
					    $diasDisfrutadosPeriodo[$counix_empleado."->".$pvadetfci."->".$pvadetfcf] += $dias_total;

					    (isset( $diasDisfrutadosPeriodo[$counix_empleado."->".$wfec_i."->".$wfec_f] ) ) ? $diasDisfrutadosHoy = $diasDisfrutadosPeriodo[$counix_empleado."->".$wfec_i."->".$wfec_f] : $diasDisfrutadosHoy =0;


				        if( $diasPendientesDisfrutar < 0 ){

					            if( count( $pila_dias_pendientes ) > 0 ){

					                $diasDisfrutadosEnPeriodo = $dias_total;
					                while( $diasPendientesDisfrutar < 0 and count( $pila_dias_pendientes ) > 0 ){

					                    $diasAcumuladosDisfrutados =  $pila_dias_pendientes[count($pila_dias_pendientes)-1]['diasDisfrutados'];
					                    $diasDisfrutadosEnPeriodo  += $diasAcumuladosDisfrutados;
					                    $diasPendientesDisfrutar   += $diasAcumuladosDisfrutados;
					                    $diasFaltantesPorDisfrutar =  $diasDisponibles - $diasDisfrutadosEnPeriodo;

					                    array_pop($pila_dias_pendientes);
					                }

					                if( $diasPendientesDisfrutar < 0 ){
					                    $aux = array( 'periodo'=>$pvadetfci."  -->  ".$pvadetfcf, "diasDisfrutados"=>$diasDisfrutadosEnPeriodo, "diasFaltantesPorDisfrutar"=>$diasFaltantesPorDisfrutar );
					                    array_push( $pila_dias_pendientes, $aux );
					                }

					            }else{

					                $aux = array( 'periodo'=>$pvadetfci."  -->  ".$pvadetfcf, "diasDisfrutados"=>$dias_total );
					                array_push( $pila_dias_pendientes, $aux );
					            }

					    }


                   } // FIN WHILE EMPLEADO


		        $fecini_cumplido  = $pvadetfci;
		        $fecfin_cumplido  = $pvadetfcf;

		        $fecha_inicio     = $pvadetfci;
                $fecha_fin        = $pvadetfcf;

                $wtotdias         = $diasDisfrutadosPeriodo[$counix_empleado."->".$wfec_i."->".$wfec_f];

		        $dias_disponibles = number_format(($diasDisponibles-$wtotdias),0);


		        if ( $wtotdias < $diasDisponibles && $wtotdias > 0)
                {

					$html_tr[] = array( "codigo_empleado"  => $codigo_empleado,
						                "cedula_empleado"  => $cedula_empleado,
				                        "nombre_empleado"  => $nombre_empleado,
				                        "nomcen_empleado"  => $nomcen_empleado,
				                        "fecha_inicial"    => substr($wfec_i,0,10),
				                        "fecha_final"      => substr($wfec_f,0,10),
				                        "dias_licencia"    => $diasLicencia,
				                        "dias_disponibles" => number_format(($diasDisponibles-$wtotdias),0));
				}


			    if( !$haDisfrutadoVacaciones ){

                    // se consulta el cargo  del empleado
					$cons = "SELECT Idefin
							  FROM  ".$wbasetalhuma."_000013
						     WHERE Ideuse = '".$codigo_empleado."' ";

					$resemp = mysql_query($cons,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$cons." - ".mysql_error());

					while( $row = mysql_fetch_array($resemp) ){

                          $wfec_f = $row['Idefin'];
	                }
			    }


				// *************** EMPIEZO LA BUSQUEDA DE LOS PERIODOS QUE NO SE HAN EMPEZADO A DISFRUTAR *********************

				//----> acá se miran cuantos periodos hay entre las fechas, y así se empiezan a recorrer buscando lo disfrutado
				$wfecproF  = $wfec_f;

				$wdias     = (((strtotime($wfecha)-strtotime($wfecproF))/86400)+1);

				$wperiodos = ceil($wdias/365);

				for ( $i = 1; $i <= $wperiodos; $i++ ){

					//--> se mueve hacia el siguiente periodo
					$wfecproI  = date("Y-m-d", strtotime($wfecproF."+1 day"));
					$wfecproF  = date("Y-m-d", strtotime($wfecproF."+1 year"));

					$wfecproM  = $wfecproF;
					$wdias2    = (((strtotime($wfecha)-strtotime($wfecproF))/86400)+1);

					if( $wdias2 < 0 ){//--> fecha final no se ha cumplido, así que se pone igual al dia de hoy
						$wfecproF = $wfecha;
					}


					if ( $poseeLicencia == 0 ){

                       	 $diasLicencia = 0;

                         $datosDiasLicencia['diasNoContados'] = 0;
                    }
                    else{

	                     $datosDiasLicencia   = diasLicenciaNoRemunerada( $wfecproI, $wfecproF, $counix_empleado ); //mas uno porque se incluye el dia final

		                 $diasLicencia        = $datosDiasLicencia['diasLicencia']; //mas uno porque se incluye el dia final
                    }

					$resdia  = $diasLicencia < 0  ? $diasLicencia .' day' : "+ " . $diasLicencia  . " day";

					$wfecproF          = date("Y-m-d", strtotime($wfecproF.$resdia)); //se mueve  la Fecha Final próximo período la cantidad de dias que se han dado de licencia
					$diasDisponibles   = 0;
					$aux               = diasDisponiblesPeriodo( $wfecproI, $wfecproF, $cedula_empleado, $arrayPeriodosRiesgo );//dias a los que se tiene derecho en el último periodo disfrutado

					$detalleDias           = "";
					$diasDisponiblesReales = 0;

					foreach ($aux as $keyTipo => $dias ) {

						$diasDisponiblesPeriodo = calcularDiasGanados( $keyTipo, $dias );
						$diasLicenciaPeriodo    = calcularDiasGanados( $keyTipo, $datosDiasLicencia['diasNoContados'][$keyTipo] );

						$diasDisponiblesPeriodo = $diasDisponiblesPeriodo - $diasLicenciaPeriodo;
						$diasDisponiblesPeriodo = round($diasDisponiblesPeriodo);
						$diasLicenciaPeriodo    = round($diasLicenciaPeriodo);

						$diasDisponibles       +=  ( $diasDisponiblesPeriodo - $diasLicenciaPeriodo );
						$diasDisponiblesReales +=  ($diasDisponiblesPeriodo - $diasLicenciaPeriodo);
						( $diasDisponibles*1 >= 5 ) ? $diasDisponibles = round( $diasDisponibles ) : $diasDisponibles = 0;
					}

					$html_tr[] = array(  "codigo_empleado"  => $codigo_empleado,
										 "cedula_empleado"  => $cedula_empleado,
				                         "nombre_empleado"  => $nombre_empleado,
				                         "nomcen_empleado"  => $nomcen_empleado,
				                         "fecha_inicial"    => substr($wfecproI,0,10),
				                         "fecha_final"      => substr($wfecproM,0,10),
				                         "dias_licencia"    => $diasLicencia,
				                         "dias_disponibles" => $diasDisponiblesReales);

		    	}// fin for

			echo json_encode($html_tr);
			return;

     }


function diasLicenciaNoRemuneradagral($codigo){

    global $conexunix, $conex;
    global $arrayPeriodosRiesgo;

    $diasLicencia       = 0;
    $tuvoLicencia       = false;
    $datosLicencia      = array();
    $conceptosLicencias = array();

    $query  = " SELECT Detval
                  FROM root_000051
                WHERE Detapl = 'conceptosLicenciasNoRemuneradas'";

    $res    = mysql_query( $query, $conex );

    $num    = mysql_num_rows($res);

    if  ($num > 0){

	    while( $row = mysql_fetch_assoc( $res ) ){

	        $datos = explode( ",", $row['Detval'] );
	        foreach ($datos as $key => $dato) {
	            array_push( $conceptosLicencias, "'".$dato."'" );
	        }

        }
    }

    $conceptos = implode( ",", $conceptosLicencias );

    $query  = " SELECT count(inccod) as total
                  FROM noinc
                 WHERE inccod = '{$codigo}'
                   AND inccon in ( $conceptos )";


    $rs     = odbc_do( $conexunix,$query );

    $totcon = 0;

    while ( odbc_fetch_row($rs) ){

    	$totcon = odbc_result($rs,'total');

    }

    return $totcon;
}


function diasLicenciaNoRemunerada( $wfechaInicio, $wfechaFinal, $codigo ){


    global $conexunix, $conex;
    global $arrayPeriodosRiesgo;

    $diasLicencia       = 0;
    $tuvoLicencia       = false;
    $datosLicencia      = array();
    $conceptosLicencias = array();

    $query  = " SELECT Detval
                  FROM root_000051
                WHERE Detapl = 'conceptosLicenciasNoRemuneradas'";

    $res    = mysql_query( $query, $conex );

    $num    = mysql_num_rows($res);

    if  ($num > 0){

	    while( $row = mysql_fetch_assoc( $res ) ){
	        $datos = explode( ",", $row['Detval'] );
	        foreach ($datos as $key => $dato) {
	            array_push( $conceptosLicencias, "'".$dato."'" );
	        }

        }
    }

    $conceptos = implode( ",", $conceptosLicencias );

    //echo "\r\n".' funcion localizada antes consulta incapacidad'.date("H:i:s");

    $query  = " SELECT inccod, incrfi, incrff
                  FROM noinc
                 WHERE inccod = '{$codigo}'
                   AND incrff >= '{$wfechaInicio}'
                   AND incrfi <= '{$wfechaFinal}'
                   AND inccon in ( $conceptos )
                 GROUP BY inccod, incrfi, incrff";


    $rs     = odbc_do( $conexunix,$query );


	    while ( odbc_fetch_row($rs) ){

	        $fecLimiteInferior  = ( strtotime(odbc_result($rs,2)) <= strtotime($wfechaInicio) ) ? $wfechaInicio : odbc_result($rs,2);
	        $fecLimiteSuperior  = ( strtotime(odbc_result($rs,3)) >= strtotime($wfechaFinal) ) ? $wfechaFinal : odbc_result($rs,3);

	        $tuvoLicencia  = true;
	        $diasLicencia += (((strtotime($fecLimiteInferior)-strtotime($fecLimiteSuperior))/86400)+1);

	        //diasEntreFechas( $fecLimiteInferior, $fecLimiteSuperior );
	        $datosLicencia['diasLicencia']    += (((strtotime($fecLimiteInferior)-strtotime($fecLimiteSuperior))/86400)+1);
	        if( !isset($datosLicencia['diasNoContados'])){
	            $datosLicencia['diasNoContados'] = array();
	        }
	        $datosLicencia['diasNoContados']  += diasDisponiblesPeriodo( $fecLimiteInferior, $fecLimiteSuperior, $cedulaUsuario, $arrayPeriodosRiesgo ); //2016-01-18
	    }

	    $datosLicencia['diasLicencia']++;

	    //echo "\r\n".' funcion localizada fin'.date("H:i:s");

	    if( !$tuvoLicencia ){
	        $diasLicencia = 0;
	        $datosLicencia['diasLicencia'] = 0;
	    }else{
	        $diasLicencia = $diasLicencia + 1;
	    }


    return $datosLicencia;
}


function periodosSolicitados($conex, $wemp_pmla, $wbasedato, $wusuario)
{

    $arr_solicitudEnviada = array();

    $sql = "SELECT  n12.Dvause AS wusuario_solicitud, n12.Dvapfi AS fecha_inicio_pendiente, n12.Dvapff AS fecha_fin_pendiente
                    , n12.Dvadpe AS dias_disponibles, n12.Dvaerc AS respuesta_coordinador, n12.Dvaern AS respuesta_nomina
                    , n12.Dvadso AS dias_solicitados, n12.Dvafid AS fecha_inicio_solicitud, n12.Dvaffd AS fecha_fin_solicitud
                    , n12.id as id_solicitud, n12.Dvacco as wcentro_costo_empleado, 'pendiente' estadoSolicitud, fecha_data fecha_Creacion_Solicitud
            FROM  {$wbasedato}_000012 AS n12
            WHERE n12.Dvause = '{$wusuario}'
              AND n12.Dvaest = 'on'
              AND n12.Dvafid >= '".date('Y-m-d')."'
            UNION
            SELECT n12.Dvause AS wusuario_solicitud, n12.Dvapfi AS fecha_inicio_pendiente, n12.Dvapff AS fecha_fin_pendiente
                    , n12.Dvadpe AS dias_disponibles, n12.Dvaerc AS respuesta_coordinador, n12.Dvaern AS respuesta_nomina
                    , n12.Dvadso AS dias_solicitados, n12.Dvafid AS fecha_inicio_solicitud, n12.Dvaffd AS fecha_fin_solicitud
                    , n12.id as id_solicitud, n12.Dvacco as wcentro_costo_empleado, 'vencida' estadoSolicitud, fecha_data fecha_Creacion_Solicitud
            FROM  {$wbasedato}_000012 AS n12
            WHERE n12.Dvause = '{$wusuario}'
              AND n12.Dvaest = 'on'
              AND n12.Dvafid < '".date('Y-m-d')."'
             ORDER  BY id_solicitud";

    if($result = mysql_query($sql, $conex))
    {
        while ($row = mysql_fetch_array($result))
        {

            if( $row['estadoSolicitud'] == "vencida" && $row['respuesta_nomina'] == "APROBADO" && ( strtotime(date('Y-m-d'))  - strtotime($row['fecha_fin_solicitud']) >= 0 ) ){//--> ya se disfrutó

            }else{

                $idx_solicitud = crearIndiceUnico($row['wusuario_solicitud'], $row['fecha_inicio_pendiente'], $row['fecha_fin_pendiente'], $row['dias_disponibles']);
                if(!array_key_exists($idx_solicitud, $arr_solicitudEnviada))
                {
                    $arr_solicitudEnviada[$idx_solicitud] = array();
                }

                $arr_solicitudEnviada[$idx_solicitud] =
                            crearArrayDatosSolicitud($row['wusuario_solicitud'], $row['fecha_inicio_pendiente'], $row['fecha_fin_pendiente'], $row['dias_disponibles']
                                                    , $row['respuesta_coordinador'], $row['respuesta_nomina'], $row['dias_solicitados'], $row['fecha_inicio_solicitud']
                                                    , $row['fecha_fin_solicitud'], $row['wcentro_costo_empleado'], $row['id_solicitud'], $row['estadoSolicitud'], $row['fecha_Creacion_Solicitud'], $row['fecha_Creacion_Solicitud']);
            }

        }
    }
    else
    {
        echo "Error ".mysql_error()." => ".$sql;
    }

    return $arr_solicitudEnviada;
}


function buscarPeriodosEnRiesgo( $wusuario_solicitud ){
    global $wbasedato, $conex;
    $arrayPeriodosRiesgo = array();
    $query = " SELECT Rieusu usuario, Riefei fechaIngreso, Riefes fechaSalida, Rieest estado
                 FROM {$wbasedato}_000013
                WHERE Rieusu = '{$wusuario_solicitud}'
                  AND Rieest = 'on'
                ORDER BY id";

    $rs   = mysql_query( $query, $conex );
    while( $row = mysql_fetch_assoc( $rs ) ){
        if( $row['fechaSalida'] == "0000-00-00" )
            $row['fechaSalida'] = date('Y-m-d');

        $aux['fechaIngreso'] = $row['fechaIngreso'];
        $aux['fechaSalida']  = $row['fechaSalida'];
        $aux['estado']       = $row['estado'];
        $aux['diasRiesgo']   = (((strtotime($row['fechaSalida'])-strtotime($row['fechaIngreso']))/86400)+1);
        //diasEntreFechas( $row['fechaIngreso'], $row['fechaSalida'] );
        array_push( $arrayPeriodosRiesgo, $aux );
    }
    return( $arrayPeriodosRiesgo );
}


function calcularDiasGanados( $tipoRiesgo, $diasEnTipo ){
    if( $tipoRiesgo == "riesgo" ){
        $diasGanados = ( diasRiesgo*$diasEnTipo)/365;
    }else{
        $diasGanados = ( diasSinRiesgo*$diasEnTipo)/365;
    }
    return( $diasGanados);
}


function diasDisponiblesPeriodo( $fecha_inicial, $fecha_final, $wcedula, $arrayPeriodosRiesgo ){

    global $conexunix;
    $inicioPeriodoEfectivoDeRiesgo = "";
    $finalPeriodoEfectivoDeRiesgo  = "";
    $meses['riesgo']    = 0;
    $meses['sinRiesgo'] = (((strtotime($fecha_final)-strtotime($fecha_inicial))/86400)+1);


    if (count($arrayPeriodosRiesgo) >0 )
    {
	    foreach ( $arrayPeriodosRiesgo as $i => $periodoEnRiesgo ) {

	        $omitirPeriodoRiesgo =  false;

	        $diasValidacionInicial = (((strtotime($periodoEnRiesgo['fechaIngreso'])-strtotime($fecha_final))/86400)+1);
	        //diasEntreFechas( $fecha_final, $periodoEnRiesgo['fechaIngreso'] );
	        if( $diasValidacionInicial > 0 ){//-->este periodo termina antes de que inicie este periodo en riesgo
	            $omitirPeriodoRiesgo = true;
	        }

	        if( $periodoEnRiesgo['fechaSalida'] != "0000-00-00" and !$omitirPeriodoRiesgo ){


	            $diasValidacionInicial = (((strtotime($periodoEnRiesgo['fechaSalida'])-strtotime($fecha_inicial))/86400)+1);//--> dia inicial del periodo buscado y dia de salida del dia del periodo en riesgo
	            //diasEntreFechas( $fecha_inicial, $periodoEnRiesgo['fechaSalida'] );
	            if( $diasValidacionInicial < 0  or $omitirPeriodoRiesgo ){//-->este periodo empieza posteriormente a este periodo en riesgo
	                //echo "<br> edb 2: $fecha_inicial, {$periodoEnRiesgo['fechaSalida']}  diferencia: $diasValidacionInicial";
	                $omitirPeriodoRiesgo = true;
	            }

	        }

	        if( !$omitirPeriodoRiesgo ){

	            $diferenciaEnDias = (((strtotime($periodoEnRiesgo['fechaIngreso'])-strtotime($fecha_inicial))/86400)+1);//--> diferencia entre el inicio del periodo consultado y el inicio del periodo en riesgo
	            //diasEntreFechas( $fecha_inicial, $periodoEnRiesgo['fechaIngreso'] );
	            if( $diferenciaEnDias <= 0 ){//--> quiere decir que el periodo de ingreso al riesgo es anterior al periodo consultado
	                $inicioPeriodoEfectivoDeRiesgo = $fecha_inicial;
	            }else{
	                $inicioPeriodoEfectivoDeRiesgo = $periodoEnRiesgo['fechaIngreso'];
	            }

	            if( $periodoEnRiesgo['fechaSalida'] == "0000-00-00"  and $periodoEnRiesgo['esado'] == "on" ){
	                $finalPeriodoEfectivoDeRiesgo = $fecha_final;
	            }

	            $diferenciaEnDias = (((strtotime($periodoEnRiesgo['fechaSalida'])-strtotime($fecha_final))/86400)+1);
	            //diasEntreFechas( $fecha_final, $periodoEnRiesgo['fechaSalida'] );
	            if( $diferenciaEnDias <= 0 ){//--> quiere decir que el periodo de ingreso al riesgo es anterior al periodo consultado
	                $finalPeriodoEfectivoDeRiesgo = $periodoEnRiesgo['fechaSalida'];
	            }else{
	                $finalPeriodoEfectivoDeRiesgo = $fecha_final;
	            }
	            $meses['riesgo'] = (((strtotime($finalPeriodoEfectivoDeRiesgo)-strtotime($inicioPeriodoEfectivoDeRiesgo))/86400)+1);
	            //diasEntreFechas( $inicioPeriodoEfectivoDeRiesgo, $finalPeriodoEfectivoDeRiesgo );
	        }

	    }
	    $meses['sinRiesgo'] = $meses['sinRiesgo'] - $meses['riesgo'];
    }
    return( $meses );
}


function diasEntreFechas($fechainicio, $fechafin)
{

    return (((strtotime($fechafin)-strtotime($fechainicio))/86400)+1);
}


function crearIndiceUnico($wusuario_solicitud, $fecha_inicio_pendiente, $fecha_fin_pendiente, $dias_disponibles)
{
    $wusuario_solicitud = str_replace("-", "_", $wusuario_solicitud);
    $fecha_inicio_pendiente = str_replace("/", "_", $fecha_inicio_pendiente);
    $fecha_fin_pendiente    = str_replace("/", "_", $fecha_fin_pendiente);
    return $wusuario_solicitud.'__'.str_replace("-", "_", $fecha_inicio_pendiente).'__'.str_replace("-", "_", $fecha_fin_pendiente).'__';
}


function crearArrayDatosSolicitud($wusuario_solicitud, $fecha_inicio_pendiente, $fecha_fin_pendiente, $dias_disponibles, $respuesta_coordinador, $respuesta_nomina, $dias_solicitados, $fecha_inicio_solicitud, $fecha_fin_solicitud, $wcentro_costo_empleado, $id_solicitud, $estadoSolicitud='', $fecha_Creacion_Solicitud='')
{
    return array(   "wusuario_solicitud"     => $wusuario_solicitud,
                    "fecha_inicio_pendiente" => $fecha_inicio_pendiente,
                    "fecha_fin_pendiente"    => $fecha_fin_pendiente,
                    "dias_disponibles"       => $dias_disponibles,
                    "respuesta_coordinador"  => $respuesta_coordinador,
                    "respuesta_nomina"       => $respuesta_nomina,
                    "dias_solicitados"       => $dias_solicitados,
                    "fecha_inicio_solicitud" => $fecha_inicio_solicitud,
                    "fecha_fin_solicitud"    => $fecha_fin_solicitud,
                    "id_solicitud"           => $id_solicitud,
                    "wcentro_costo_empleado" => $wcentro_costo_empleado,
                    "estadoSolicitud"        => $estadoSolicitud,
                    "fecha_Creacion_Solicitud"=> $fecha_Creacion_Solicitud
                    );
}


 // Consultar los usuarios para el campo autocompletar
 function consultarUsuarios($wbasetalhuma,$conex,$wemp_pmla){

        $strtipvar = array();

        $q  = " SELECT Ideuse, concat(Ideno1,' ',Ideno2,' ',Ideap1,' ',Ideap2) as Nomemp
                From ".$wbasetalhuma."_000013
                Where Ideest ='on'
                      And Ideap1 !=''
                Order by Nomemp";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

        while($row = mysql_fetch_assoc($res))
        {
               $strtipvar[$row['Ideuse']] = $row['Ideuse'].'-'.utf8_encode($row['Nomemp']);
        }

        return $strtipvar;
 }


 // Consultar todos los Centros de Costos para el campo autocompletar
  function consultarCentros($wbasetalhuma,$conex,$wemp_pmla){
    $strtipvar = array();

        $q = "  SELECT  Empdes,Emptcc
                FROM    root_000050
                WHERE   Empcod = '".$wemp_pmla."'";
        $res = mysql_query($q,$conex);

        if($row = mysql_fetch_array($res))
        {
            $tabla_CCO = $row['Emptcc'];
            switch ($tabla_CCO)
            {
                case "clisur_000003":
                        $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                    FROM    clisur_000003 AS tb1
                                            INNER JOIN
                                            ".$wbasetalhuma."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                    GROUP BY    tb1.Ccocod
                                    ORDER BY    tb1.Ccodes";
                        break;
                case "farstore_000003":
                        $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                    FROM    farstore_000003 AS tb1
                                            INNER JOIN
                                            ".$wbasetalhuma."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                    GROUP BY    tb1.Ccocod
                                    ORDER BY    tb1.Ccodes";
                        break;
                case "costosyp_000005":
                        $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                    FROM    costosyp_000005 AS tb1
                                            INNER JOIN
                                            ".$wbasetalhuma."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                    GROUP BY    tb1.Ccocod
                                    ORDER BY    tb1.Cconom";
                        break;
                case "uvglobal_000003":
                        $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                    FROM    uvglobal_000003 AS tb1
                                            INNER JOIN
                                            ".$wbasetalhuma."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                    GROUP BY    tb1.Ccocod
                                    ORDER BY    tb1.Ccodes";
                        break;
                default:
                        $query="    SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                    FROM    costosyp_000005 AS tb1
                                            INNER JOIN
                                            ".$wbasetalhuma."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                    GROUP BY    tb1.Ccocod
                                    ORDER BY    tb1.Cconom";
            }

            $res = mysql_query($query,$conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());
            while($row = mysql_fetch_assoc($res))
            {
                 $strtipvar[$row['codigo']] = utf8_encode($row['nombre']);
            }
        }

  return $strtipvar;
  }

     // *****************************************         FIN PHP         ********************************************
  ?>
  <html>
  <head>
    <title>Reporte Vacaciones disfrutadas y pendientes</title>
    <meta charset="utf-8">
    <meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" /></script>
    <script type="text/javascript" src="../../../include/root/jqueryalert.js"></script>
    <script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
    <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.css" />
    <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.filter.css" />
    <script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
    <script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script>
    <link type="text/css" href="../../../include/root/jquery.tooltip.css" rel="stylesheet" />
    <script type="text/javascript" src="../../../include/root/jquery.tooltip.js"></script>
    <script type="text/javascript">

      var stringEx = "";

      $(document).ready(function(){

          $("#txtfecini,#txtfecfin").datepicker({
              closeText: 'Cerrar',
              prevText: 'Antes',
              nextText: 'Despues',
              monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
              'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
              monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
              'Jul','Ago','Sep','Oct','Nov','Dic'],
              dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
              dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
              dayNamesMin: ['D','L','M','M','J','V','S'],
              weekHeader: 'Sem.',
              dateFormat: 'yy-mm-dd',
              yearSuffix: '',
              showOn: "button",
              buttonImage: "../../images/medical/root/calendar.gif",
              buttonImageOnly: true,
          });

          $('#optservicio,#optempleado').multiselect({
              numberDisplayed: 1,
              selectedList:1,
              multiple:false
          }).multiselectfilter();


      }); // Finalizar Ready()



       /* Generar el reporte segun filtros seleccionados */
       function Consultar(opcion){

          /* Validar que el rango de fecha esté diligenciado */
          if ( $("#txtfecini").val() == '' || $("#txtfecfin").val() == '' ){

              jAlert('Falta diligenciar Rango de fecha');
              return;
          }

          stringEx       ='';
          var wemp_pmla  =  $("#wemp_pmla").val();
          var wtipo      =  $("input:radio[name=radtipo]:checked").val();

          /* Validar el campo multiselect para centros de costos */
          if ( $("#optservicio").val() == null || $("#optservicio").val()=='')

               var wservicio = '';
          else
               var wservicio = $("#optservicio").val();


          /* Validar que el usuario sea diligenciado en caso de estar elegida la opcion
           periodos pendientes */

          if ( $("#optempleado").val() == null || $("#optempleado").val() == '')
          	   var wempleado = '';

          else
               var wempleado = $("#optempleado").val();


          /* Activar div que muestra el tiempo de proceso */
          document.getElementById("myProgress").style.display   = "";


          /* Consultar historico de SQL Software  */
          if (wtipo == 'D'){

   			  $("#tbldisfrutadas > tbody:last").children().remove();

   			  var elem     = document.getElementById("myBar");

   			  var width    = 1;

	          elem.style.width = width + '%';

			  document.getElementById("label").innerHTML = width  + '%';


              $.post("rep_vacacionesgral.php",
              {
	                consultaAjax:  true,
	                accion      :  'ConsultarDetalle',
	                wemp_pmla   :  wemp_pmla,
	                wservicio   :  wservicio.toString(),
	                wempleado   :  wempleado.toString(),
	                wtipo       :  wtipo

              }, function(respuesta){

                   /* En caso de no encontrar registros */
                  if (respuesta.total == 0){

	                    $("#tblbuscar").hide();
	                    $("#tblmensaje").show();
                  }
                  /* Mostrar tabla con registros consultados */
                  else{

              	        var cont     = 0;
                  	    var cont2    = 0;
                        var fila     = "fila1";
                        var filanom  = "fila1";
                        var stringTr = "";

                  	    jQuery.each(respuesta.resultado, function(){

                  	            if (this.codigo_empleado !== ''){

		                           filanom = filanom == "fila1" ? "fila2" : "fila1";
		                           cont2++;
		                        }

		                        stringTr = stringTr + '<tr class="'+fila+'">'
		                                            + '<td class="'+filanom+'" align="center">'+this.codigo_empleado+'</td>'
		                                            + '<td class="'+filanom+'" >'+this.nombre_empleado+'</td>'
		                                            + '<td class="'+filanom+'" >'+this.centro_empleado+'</td>'
		                                            + '<td align="center">'+this.fecini_cumplido+'</td>'
		                         					+ '<td align="center">'+this.fecfin_cumplido+'</td>'
		                         					+ '<td align="center">'+this.dias_total+'</td>'
		                         					+ '<td align="center">'+this.fecini_disfruta+'</td>'
		                         					+ '<td align="center">'+this.fecfin_disfruta+'</td>'
		                         					+ '<td align="center">'+this.dias_totcal+'</td>'
		                         					+ '<td align="center">'+this.dias_totdin+'</td></tr>';

		                        fila    = fila == "fila1" ? "fila2" : "fila1";

		                        var codigosql = this.codigo_empleado.split("-");

		                        stringEx = stringEx + codigosql[0]
	                         					    +';'+this.nombre_empleado
	                         					    +';'+this.centro_empleado
	                         					    +';'+this.fecini_cumplido
	                         					    +';'+this.fecfin_cumplido
	                         					    +';'+this.dias_total
	                         					    +';'+this.fecini_disfruta
	                         					    +';'+this.fecfin_disfruta
	                         					    +';'+this.dias_totcal
	                         					    +';'+this.dias_totdin+'\r\n';

		                        cont ++;

		                        width = (width*1) + 1;

						        if (width > 100)

							        width = 100;

	    					    elem.style.width = width + '%';

							    document.getElementById("label").innerHTML = width  + '%';

                        });

	                    $("#tblmensaje,#tblpendientes,#tblbuscarp").hide();

	                    $('#tbldisfrutadas > tbody:last').append(stringTr);

	                    $("#tbldisfrutadas,#tblbuscar").show();

                        $('input#id_search_pacientes').quicksearch('table#tbldisfrutadas tbody tr');

                    }

                },"json");

          }
          else{

          	  /* Consultar los empleados para realizar un odbc por empleado, mediante barra de seguimiento */

          	   var stringTr = '';
          	   var cont     = 0;
               var fila     = "fila1";
               var filanom  = "fila1";
               var elem     = document.getElementById("myBar");
   			   var width    = 1;
   			   var vcont    = 1;

	           elem.style.width = width + '%';

			   document.getElementById("label").innerHTML = width  + '%';

               $("#tblpendientes > tbody:last").children().remove();

          	   $.post("rep_vacacionesgral.php",
               {
	                 consultaAjax:  true,
	                 accion      :  'ConsultarEmpleados',
	                 wemp_pmla   :  wemp_pmla,
	                 async       :  true,
	                 wservicio   :  wservicio.toString(),
	                 wempleado   :  wempleado.toString()

               }, function(respuesta){

                  /* En caso de no encontrar registros */
                  if (respuesta.total == 0){

	                   $("#tblbuscar").hide();
	                   $("#tblmensaje").show();
                  }
                  else{

                  	    document.getElementById("myProgress").style.display   = "";

                  	    if (wservicio == '')

                  	    	var porcen = 1;
                  	    else
                  	    	var porcen = Math.round(100/(respuesta.total*1));

                  	    var elem = document.getElementById("myBar");

						var width = 2;

						var filpar ='fila1';

                  	    // Realizar recorrido por cada empleado

                  	    jQuery.each(respuesta.detalle, function(){

                  	        cont++;

	                  	    /* Consulta que realiza una conexion odbc por empleado, con el objetivo de ejecutar
	                  	       varios ajax de manera simultanea y así lograr mayor rendimiento */
			          	    $.post("rep_vacacionesgral.php",
			                {
				                  consultaAjax : true,
				                  accion       : 'ConsultarPendientes2',
				                  wemp_pmla    : wemp_pmla,
				                  wempleado    : this.codigo,
				                  wnombre      : this.nombre,
				                  wcedula      : this.cedula,
				                  wcentro      : this.centro,
				                  wcconom      : this.cconom

			                }, function(resemp){

			                	  stringTr ='';

			                	  //Recorrer las vacaciones pendientes del empleado consultado

								  $.each(resemp, function(index){


 										// Generar un string que contenga en formato html la tabla detalle del reporte
 										stringTr = stringTr + '<tr class="'+fila+'">'
				                                            + '<td align="center">'+resemp[index].codigo_empleado+'</td>'
				                                            + '<td align="center">'+resemp[index].cedula_empleado+'</td>'
				                                            + '<td >'+resemp[index].nombre_empleado+'</td>'
				                                            + '<td >'+resemp[index].nomcen_empleado+'</td>'
				                                            + '<td align="center">'+resemp[index].fecha_inicial+' <---> '+resemp[index].fecha_final+'</td>'
				                         					+ '<td align="center">'+resemp[index].dias_licencia+'</td>'
				                         					+ '<td align="center">'+resemp[index].dias_disponibles+'</td></tr>';


		                            	var codigosql = resemp[index].codigo_empleado.split("-");

		                            	// Generar un string para exportar en formato CSV
		                            	stringEx = stringEx + codigosql[0]
			                                                +';'+resemp[index].cedula_empleado
			                         					    +';'+resemp[index].nombre_empleado
			                         					    +';'+resemp[index].fecha_inicial
			                         					    +';'+resemp[index].fecha_final
			                         					    +';'+resemp[index].dias_disponibles+'\r\n';


				                        fila = fila == "fila1" ? "fila2" : "fila1";

                                  });

                                  $('#tblpendientes > tbody:last').append(stringTr);

                                  if  (wservicio==''){

                                      if  (vcont==14){
                                      	   width = width + porcen;
                                      	   vcont=1;
                                      }
                                  }
                                  else{
	                                  if(respuesta.total>=50){

	                              	       width = width + porcen;
	                                  }
	                                  else{
	                                  	   if (width < 100)
							                   width = width + porcen + 1;
	                                  }
                                  }


							      if (width >= 100)

							      	  width = 100;

	    					      elem.style.width = width + '%';

							      document.getElementById("label").innerHTML = width * 1  + '%';

							      vcont++;

                            },"json");

                            $("#tbldisfrutadas,#tblmensaje,#tblbuscar").hide();
							$("#tblpendientes,#tblbuscarp").show();
							$('input#id_search_pacientep').quicksearch('table#tblpendientes tbody tr');

	                   });

                  }

                  },"json");

              }
       }


       // Exportar el contenido del string 'stringEx' a formato delimitado por comas
       function Exporcsv()
       {
		   	var usu = document.createElement('a');
		    usu.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(stringEx));
			usu.setAttribute('download','vacaciones_pendientes.csv');

			if (document.createEvent) {
				var event = document.createEvent('MouseEvents');
				event.initEvent('click', true, true);
				usu.dispatchEvent(event);
			}
			else {
				usu.click();
			}

       }


       // Función para exportar la tabla 'tblexcel'
       function Exportar(){

            //Creamos un Elemento Temporal en forma de enlace
            var tmpElemento = document.createElement('a');
            var data_type = 'data:application/vnd.ms-excel'; //Formato anterior xls

            // Obtenemos la información de la tabla
            var wtipo  =  $("input:radio[name=radtipo]:checked").val();

	        if (wtipo == 'D')

	              wtabla  =  'tbldisfrutadas';
	        else
	          	  wtabla  =  'tblpendientes';

            var tabla_div = document.getElementById(wtabla);
            var tabla_html = tabla_div.outerHTML.replace(/ /g, '%20');

            tmpElemento.href = data_type + ', ' + tabla_html;

            // Asignamos el nombre al archivo en formato xls
            tmpElemento.download = 'listado_vacaciones.xls';

            // Simulamos el click al elemento creado para descargarlo
            tmpElemento.click();

       }



       function cerrarVentana()
       {
          if(confirm("Esta seguro de salir?") == true)
            window.close();

          else
            return false;
       }

      </script>

      <style type="text/css">
        .button{
           color: #1b2631;
           font-weight: normal;
           font-size: 12,75pt;
           width: 90px; height: 27px;
           background: rgb(199,199,199);
           background: -moz-linear-gradient(top,  rgba(199,199,199,1) 0%, rgba(193,193,193,1) 50%, rgba(184,184,184,1) 51%, rgba(224,224,224,1) 100%);
           background: -webkit-linear-gradient(top,  rgba(199,199,199,1) 0%,rgba(193,193,193,1) 50%,rgba(184,184,184,1) 51%,rgba(224,224,224,1) 100%);
           background: linear-gradient(to bottom,  rgba(199,199,199,1) 0%,rgba(193,193,193,1) 50%,rgba(184,184,184,1) 51%,rgba(224,224,224,1) 100%);
           filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#c7c7c7', endColorstr='#e0e0e0',GradientType=0 );
           border: 1px solid #ccc;
           border-radius: 8px;
           box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
         }

        .button:hover {background-color: #3e8e41}

        .button:active {
           background-color: rgb(169,169,169);
           box-shadow: 0 5px #666;
           transform: translateY(4px);
         }

        .ui-multiselect { height:20px; width:350px; overflow-x:hidden; text-align:left;font-size: 10pt;}

         BODY {
            font-family: verdana;
            font-size: 10pt;
            width: auto;
            height:auto;
         }

		#myProgress {
		  position: relative;
		  width: 30%;
		  height: 25px;
		  align: center;
		  background-color: #ddd;
		}

		#myBar {
		  position: absolute;
		  width: 1%;
		  height: 100%;
		  background-color: lightblue;
		}

		#label {
		  text-align: center;
		  line-height: 30px;
		  color: white;
		}

    </style>
    </head>
    <body >
      <?php
        echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
        $wtitulo  = "REPORTE VACACIONES DISFRUTADAS Y PENDIENTES";
        encabezado($wtitulo, $wactualiz, 'clinica');
        $arr_usu  = consultarUsuarios ($wbasetalhuma,$conex,$wemp_pmla);
      ?>
      <table align='center' width='550px' >
          <tr  height='30px' width='200px' >
            <td class='fila1'><b>Tipo</b></td>
            <td class='fila2' align='left'>&nbsp;
	            <input type='Radio' id='radtipo' name='radtipo' value='D' checked >Disfrutadas
	            <br>&nbsp;&nbsp;<input type='Radio' id='radtipo' name='radtipo' value='P' >Pendientes x Disfrutar
            </td>
          </tr>
          <tr height='30px'>
            <td class='fila1'><b>Servicio</b></td>
            <td class='fila2'>&nbsp;&nbsp;
            <select id='optservicio' name='optservicio' multiple='multiple'>
            <?php
              echo '<option ></option>';
              foreach( $arr_servicio as $key => $val){
                  echo '<option value="' . $key .'">'.$val.'</option>';
              }
            ?>
            </select>
            </td>
          </tr>
      </table>
      </br>
      <center>
      <table>
        <tr>
	        <td>&nbsp;&nbsp;<input type='button' id='btnConsultar' name='btnConsultar' class='button' value='Consultar'     onclick='Consultar(1)'></td>
	        <td>&nbsp;&nbsp;<input type='button' id='btnExportar1' name='btnExportar1' class='button' value='Exportar XLS'  onclick='Exportar()'></td>
	        <td>&nbsp;&nbsp;<input type='button' id='btnExportar2' name='btnExportar2' class='button' value='Exportar CSV'  onclick='Exporcsv()'></td>
	        <td>&nbsp;&nbsp;<input type='button' id='btnSalir'     name='btnSalir'     class='button' value='Salir'         onclick='cerrarVentana()'></td>
        </tr>
      </table>
      <div id="divcargando" name="divcargando" style='display:none;' ><center><img width="26" height="26" border="0" src="../../images/medical/ajax-loader9.gif"></center>
      </div>
      <br>
	  <div id="myProgress" style='display:none'>
		  <div id="myBar">
		    <div id="label">5%</div>
		  </div>
   	  </div>
      <br>
      <br>
      <center>
      <div align=center id="disfrutadas">
      	    <table align='left' width='300px' style='display:none;' id='tblbuscar' name='tblbuscar'>
	        <tr><td></td><td class='fila1'>Filtrar listado:&nbsp;&nbsp;<input id="id_search_pacientes" type="text" value="" size="20" name="id_search_pacientes" placeholder="Buscar en listado">&nbsp;&nbsp;</td></tr>
	        </table>
            <table id="tbldisfrutadas" width='100%' style='display:none;border:0px;'>
	            <thead>
	            <tr align="center" class="encabezadoTabla">
	                <td align="center">C&oacute;digo Unix</td>
	                <td align="center">Nombre empleado</td>
	                <td align="center">Centro de Costos</td>
	                <td colspan="2" align="center" width="200px">Per&iacute;odo Cumplido</td>
	                <td align="center">D&iacute;as Causados<br>Disfrutados</td>
	                <td colspan="2" align="center" width="200px">Fecha disfrutado</td>
	                <td align="center">D&iacute;as Calendario<br>Disfrutados</td>
	                <td align="center">D&iacute;as Calendario<br>Disfrutados en dinero</td>
	            </tr>
	            </thead>
	            <tbody>
	            </tbody>
            </table>
      </div>
      <div align=center id="dispendientes">
           <table align='left' width='300px' style='display:none' id='tblbuscarp' name='tblbuscarp'>
	        <tr><td></td><td class='fila1'>Filtrar listado:&nbsp;&nbsp;<input id="id_search_pacientep" type="text" value="" size="20" name="id_search_pacientep" placeholder="Buscar en listado">&nbsp;&nbsp;</td></tr>
	        </table>
            <table id="tblpendientes" width='100%' style='display:none;'>
	            <thead>
	            <tr align="center" class="encabezadoTabla">
	                <td align="center">C&oacute;digo Unix</td>
	                <td align="center">C&eacute;dula</td>
	                <td align="center">Nombre empleado</td>
	                <td align="center">Servicio</td>
	                <td align="center" width="300px">Per&iacute;odo Cumplido</td>
	                <td align="center">D&iacute;as de Licencia</td>
	                <td align="center">D&iacute;as pendientes causados</td>
	            </tr>
	            </thead>
	            <tbody>
	            </tbody>
            </table>
      </div>
      </center>
      <table id='tblexcel' name='tblexcel' style='display:none;'>
      </table>
      <table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;display:none;'>
        <tr><td class='fila2'>No hay registros para esta consulta</td></tr>
      </table>
      <input type="HIDDEN" name="arr_pais"     id="arr_pais"     value='<?=base64_encode(serialize($arr_pais))?>'>
      <input type="HIDDEN" name="wscroll"      id="wscroll"      value='0'>
      <input type="HIDDEN" name="wdocumenrep"  id="wdocumenrep"  value='<?=$documentosrep?>'>
    </body>
    </html>




