<?php
include_once("conex.php");
 /**********************************************************************************************************
 *
 * Programa				    :	Reporte_Medicamentosdecontrol.php
 * Fecha de Creación 	:	2018-04-24
 * Autor				      :	Arleyda Insignares Ceballos
 * Descripcion		    :	Reporte de prescripción electrónica de medicamentos de control especial (MCE). 
 *                      Se detalla información del paciente, Medicamento, diagnóstico, Médico y demás 
 *                      información relacionada.   
 *                      
 *                      Tener en cuenta parametros en root_000051:
 *                      -'repmedicamentocontrol' utilizado para extraer el tipo de de afiliación 
 *                      (Contributivo,Subsidiado,Particular,Otros..) en la tabla cliame_000105.
 *                      -'codigo_habilitacion' es el codigo asignado a la empresa, para reportar información
 *                      a la Seccional de Salud.
 *                      
 ***************************************   Modificaciones  *************************************************
 * Agosto 13 de 2018		Edwin MG.	Se modifica el programa para mejorar el rendimiento de las cosultas 
 *										realizadas en el reporte
 **********************************************************************************************************/
               
 $wactualiz = "2018-08-13";

 function session_is_registered($name){

     if(isset($_SESSION[$name])){ 
        $GLOBALS[$name] = &$_SESSION[$name];    
        return true;
     }else{
        return false;
     }          
 } 

 foreach ($_REQUEST as $clave => $val){
          $$clave = $val; 
 }
 
 if(!isset($_SESSION['user'])){
	  echo "<center></br></br><table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
		<tr><td>Error, inicie nuevamente</td></tr>
		</table></center>";
	  return;
 }

 header('Content-type: text/html;charset=ISO-8859-1');

  //**************************************    Inicio   ***********************************************************

  

  
  include_once("root/comun.php");
  
  $wbasedato     = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
  $wbasedatocli  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
  $wbasedatotal  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');
  $wcodigohabil  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigo_habilitacion');
  $wusuarios     = consultarUsuarios($wbasedatotal,$conex,$wemp_pmla);
  $wmedicos      = consultarMedicos($wbasedato,$conex, $wemp_pmla);
  $regimen       = consultarAliasPorAplicacion($conex, $wemp_pmla, 'repmedicamentocontrol');
  
  $wfecha        = date("Y-m-d");
  $whora         = (string)date("H:i:s");
  $pos           = strpos($user,"-");
  $wusuario      = substr($user,$pos+1,strlen($user));

  // *************************************    FUNCIONES AJAX  Y PHP  **********************************************
   
    if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarDetalle"){


          $resultado     = array("mensaje" => "", "error" => false);
          
          $arr_resultado = array();
          
          $condicion     = " ";

          $wfechafin2 = date("Y-m-d",strtotime( $wfechafin."+15 day" ));

          $wfechaini2 = date("Y-m-d",strtotime( $wfechaini."-1 day" ));


          /* Obtener la información general de la clínica*/
          $sql = " Select Empdes,Empmun,Empdir,Nombre
                   From root_000050
                   Inner join root_000006 
                         on root_000050.Empmun = root_000006.Codigo
                   Where Empcod = ".$wemp_pmla." 
                     And Empest = 'on' ";   

          $res = mysql_query($sql,$conex) or die (mysql_errno()." - en el query: ".$sql." - ".mysql_error()); 

          if ($res){
             $rowemp = mysql_fetch_assoc($res);
             $nomemp = $rowemp['Empdes'];
             $diremp = $rowemp['Empdir'];
             $munemp = $rowemp['Nombre'];
          }     


          /* Seleccionar movhos_000093, filtrando con la tabla movhos_000133 y construir un array con los usuarios que 
          entregaron el medicamento de control */

          // $arrtipmed = array();

          // $q93  = " SELECT Recure,Rechis,Recing,Recart  
                    // From ".$wbasedato."_000133 m133 
                    // Inner join ".$wbasedato."_000093 m93 on m93.Rechis  =  m133.Ctrhis  
                                                        // and m93.Recing  =  m133.Ctring
                                                        // and m93.Recart  =  m133.Ctrart
                                                        // and m93.Recfre  =  m133.Ctrfge  
                                                        // and m133.Ctrfge >= '".$wfechaini."'
                                                        // and m133.Ctrfge <= '".$wfechafin."'
                                                        // and m133.Ctrest = 'on' ";


		  // $q93  = " SELECT Recure,Rechis,Recing,Recart,Recfre  
                    // From ".$wbasedato."_000093 m93 
				   // WHERE m93.Recfre >= '".$wfechaini."'
                     // AND m93.Recfre <= '".$wfechafin."'";

          // $res93 = mysql_query($q93,$conex) or die (mysql_errno()." - en el query: ".$q93." - ".mysql_error());
          // while($row = mysql_fetch_assoc($res93)){

                 // $arrtipmed[$row['Rechis'].'-'.$row['Recing'].'-'.$row['Recart'].'-'.$row['Recfre']] = substr($row['Recure'],-5);
          // }



          /* Seleccionar movhos_000133 por historia e ingreso para relacionar con movhos_000002 y obtener 
             un array con los usuarios que dispensaron el medicamento. */
          
          // $arrmovhos2 = array();
          // $arrmovhos3 = array();

          // $qCon2  = " Select m2.Fenhis, m2.Fening, m3.Seguridad, m133.Ctrart, m133.Ctrfge, m3.Fdenum  
                        // FROM ".$wbasedato."_000133 m133, ".$wbasedato."_000002 m2,".$wbasedato."_000003 m3
                     // Where m2.Fenhis = m133.Ctrhis
                       // And m2.Fening = m133.Ctring
                       // And m133.Ctrfge BETWEEN '".$wfechaini."' AND '".$wfechafin."'
                       // AND m2.Fenfec = m133.Ctrfge
                       // And m2.Fenfue IN  (SELECT ccofca FROM ".$wbasedato."_000011 WHERE ccofac = 'on' )
                       // And m3.Fdenum = m2.Fennum
                       // And m3.Fdeart = m133.Ctrart";

      
          // $res2 = mysql_query($qCon2,$conex) or die (mysql_errno()." - en el query: ".$qCon2." - ".mysql_error());

          // while($row = mysql_fetch_assoc($res2)){

              // // if( !isset( $arrmovhos2[$row['Fenhis'].'-'.$row['Fening'].'-'.$row['Ctrart'].'-'.$row['Ctrfge']] ) )
                 // $arrmovhos2[$row['Fenhis'].'-'.$row['Fening'].'-'.$row['Ctrart'].'-'.$row['Ctrfge']] = substr($row['Seguridad'],-5);
                 // $arrmovhos3[$row['Fenhis'].'-'.$row['Fening'].'-'.$row['Ctrart'].'-'.$row['Ctrfge']] = $row['Fdenum'];
          // }



          /* Consultar la cantidad suministrada al paciente en la tabla movhos_000015. 
             la cantidad Formulada puede ser superior a la que en realidad
             se le suministra al paciente */  

          // $qcon3 = " CREATE TEMPORARY TABLE tmedi15 (select sum(Apldos) as 'canDispensada', 
                                // Aplufr,Aplhis,Apling,Aplart,Aplido,Aplfec
                     // From ".$wbasedato."_000133 m133 
                     // Inner Join ".$wbasedato."_000015 m15   on m15.Aplhis  =  m133.Ctrhis  
                                                           // and m15.Apling  =  m133.Ctring
                                                           // and m15.Aplart  =  m133.Ctrart
                                                           // and m15.Aplido  =  m133.Ctrido
                                                           // and m15.Aplfec  =  m133.Ctrfge
                                                           // and m133.Ctrfge >= '".$wfechaini."'
                                                           // and m133.Ctrfge <= '".$wfechafin."'
                                                           // and m133.Ctrest = 'on' 
                                                           // and m15.Aplest  =  'on'
                     // Group By m15.Aplhis,m15.Apling,m15.Aplart,m15.Aplido,m15.Aplfec
                     // Order By m15.Aplhis,m15.Apling,m15.Aplart,m15.Aplido,m15.Aplfec) ";


          // $res = mysql_query($qcon3,$conex) or die (mysql_errno()." - en el query: ".$qcon3." - ".mysql_error());



          /* Consultar las tablas movhos_000054, movhos_000133 para extraer todos los medicamentos
            de control formulados por el médico. */

          // $qcon1 = "CREATE TEMPORARY TABLE tmedi (select m54.Kadcfr, m54.Kadvia, m54.Kadper, 
                           // m54.Kaddma, m54.Kadido, m54.Kadhis, m54.Kading, m54.Kadart,m54.Kadfec,
                           // m133.Ctrfge, m133.Ctrcan, m133.Ctrdia, m133.Ctrcon, m133.Ctrhis,
                           // m133.Ctring, m133.Ctrmed, m133.Ctrart, m133.Ctrido, m26.Artcom,
                           // m133.Ctrcan as 'cantidad', m26.Artfar, m26.Artgen, 
                           // c100.Pactdo, c100.Pacdoc, c100.Pacap1, c100.Pacap2, c100.Pacno1, 
                           // c100.Pacno2, c100.Pacsex, c100.Pacdir, c100.Pactel, c100.Paciu, 
                           // c100.Pacdep, c100.Pacfna, c100.Pactus, c101.Ingcem,
                           // tmedi15.canDispensada, tmedi15.Aplufr
                    // From ".$wbasedato."_000133 m133 
                    // Inner Join ".$wbasedato."_000054 m54 on m133.Ctrhis =  m54.Kadhis 
                                                        // and m133.Ctring =  m54.Kading
                                                        // and m133.Ctrart =  m54.Kadart 
                                                        // and m133.Ctrido =  m54.Kadido
                                                        // and m133.Ctrfge =  m54.Kadfec
                                                        // and m133.Ctrfge >= '".$wfechaini."'
                                                        // and m133.Ctrfge <= '".$wfechafin."'
                                                        // and m133.Ctrest = 'on'
                                                        // and m54.Kadest  =  'on'                  
                    // Inner Join ".$wbasedato."_000026 m26 on m26.Artcod  =  m54.kadart
                    // Inner Join tmedi15   on tmedi15.Aplhis  =  m133.Ctrhis  
                                        // and tmedi15.Apling  =  m133.Ctring
                                        // and tmedi15.Aplart  =  m133.Ctrart
                                        // and tmedi15.Aplido  =  m133.Ctrido
                                        // and tmedi15.Aplfec  =  m133.Ctrfge
                    // Inner Join  ".$wbasedatocli."_000100 c100 on c100.Pachis = m133.Ctrhis
                    // Inner Join  ".$wbasedatocli."_000101 c101 on c101.Inghis = m133.Ctrhis 
                                                             // and c101.Ingnin = m133.Ctring                                        
                    // Group By m133.Ctrhis,m133.Ctring,m133.Ctrart,m133.Ctrido,m133.Ctrfge
                    // Order By m133.Ctrhis,m133.Ctring,m133.Ctrart,m133.Ctrido,m133.Ctrfge )";
				
			$qcon1 = " CREATE TEMPORARY TABLE tmedi (select m54.Kadcfr, m54.Kadvia, m54.Kadper, 
                           m54.Kaddma, m54.Kadido, m54.Kadhis, m54.Kading, m54.Kadart,m54.Kadfec,
                           m133.Ctrfge, m133.Ctrcan, m133.Ctrdia, m133.Ctrcon, m133.Ctrhis,
                           m133.Ctring, m133.Ctrmed, m133.Ctrart, m133.Ctrido, m26.Artcom,
                           m133.Ctrcan as 'cantidad', m26.Artfar, m26.Artgen, 
                           c100.Pactdo, c100.Pacdoc, c100.Pacap1, c100.Pacap2, c100.Pacno1, 
                           c100.Pacno2, c100.Pacsex, c100.Pacdir, c100.Pactel, c100.Paciu, 
                           c100.Pacdep, c100.Pacfna, c100.Pactus, c101.Ingcem,
                           0 as canDispensada, '' as Aplufr
                    From ".$wbasedato."_000133 m133 
                    Inner Join ".$wbasedato."_000054 m54 on m133.Ctrhis =  m54.Kadhis 
                                                        and m133.Ctring =  m54.Kading
                                                        and m133.Ctrart =  m54.Kadart 
                                                        and m133.Ctrido =  m54.Kadido
                                                        and m133.Ctrfge =  m54.Kadfec
                                                        and m133.Ctrfge >= '".$wfechaini."'
                                                        and m133.Ctrfge <= '".$wfechafin."'
                                                        and m133.Ctrest = 'on'
                                                        and m54.Kadest  =  'on'                  
                    Inner Join ".$wbasedato."_000026 m26 on m26.Artcod  =  m54.kadart
                    Inner Join  ".$wbasedatocli."_000100 c100 on c100.Pachis = m133.Ctrhis
                    Inner Join  ".$wbasedatocli."_000101 c101 on c101.Inghis = m133.Ctrhis 
                                                             and c101.Ingnin = m133.Ctring 
                    Order By m133.Ctrhis,m133.Ctring,m133.Ctrart,m133.Ctrido,m133.Ctrfge )";
          
          $res = mysql_query($qcon1,$conex) or die (mysql_errno()." - en el query: ".$qcon1." - ".mysql_error());

 

          /*Relacionar con las tablas cliame_000100 y cliame_000101 para obtener los datos del paciente e ingreso
          a la clínica, la tabla movhos_000093 se utilizar para consultar el usuario que recibe el medicamento.
          En caso de que el servicio sea 'urgencias' el usuario que entrega es el mismo que recibe.*/

          $qcon4 =  "SELECT tmedi.Kadcfr, tmedi.Kadvia, tmedi.Ctrfge, tmedi.Ctrdia, tmedi.Ctrcon, tmedi.Ctrhis,  
                            tmedi.cantidad, tmedi.Pactdo, tmedi.Pacdoc, tmedi.Pacap1, tmedi.Pacap2, tmedi.Pacno1, 
                            tmedi.Pacno2, tmedi.Pacsex, tmedi.Pacdir, tmedi.Pactel, tmedi.Paciu, tmedi.Pacdep, 
                            tmedi.Pacfna, tmedi.Pactus, tmedi.Kadido,tmedi.Ctring, tmedi.Kadart, tmedi.Ctrmed,
                            concat(m43.Percan,' ',m43.Peruni) as 'frecuencia', m40.Viades, m40.Viaele, 
                            m43.Perele, m43.Perequ, tmedi.Artgen, c105.Seldes as 'afiliacion', c24.Empnom,
                            concat(m115.Relcon+' '+m115.Reluni) as 'concentracion',m115.Relcon, m46.Ffanom,
                            m46.Ffaele, tmedi.canDispensada, tmedi.Ctrcan, tmedi.Aplufr, tmedi.Kadhis, tmedi.Kading,
                            r2.Descripcion as 'nomDepartamento', r6.Nombre as 'nomCiudad', tmedi.Ctrido
                     FROM   tmedi     
                            Left Join  ".$wbasedatocli."_000024  c24  on c24.Empcod  = tmedi.Ingcem                           
                            Left Join  ".$wbasedato."_000043 m43      on m43.Percod  = tmedi.Kadper
                            Left Join  ".$wbasedato."_000040 m40      on m40.Viacod  = tmedi.Kadvia
                            Left Join  ".$wbasedato."_000046 m46      on m46.Ffacod  = tmedi.Artfar
                            Left Join  ".$wbasedatocli."_000105 c105  on c105.Selcod = tmedi.Pactus 
                                                                     and c105.Seltip = '".$regimen."'
                            Left Join  ".$wbasedato."_000115 m115     on m115.Relart = tmedi.Kadart 
                            Left Join  root_000002 r2  on r2.Codigo = tmedi.Pacdep
                            Left Join  root_000006 r6  on r6.Codigo = tmedi.Paciu ";

          $res_con = mysql_query($qcon4,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcon4." - ".mysql_error());

          $numcon  = mysql_num_rows($res_con);

		  
		  $qCon2  = " Select m2.Fenhis, m2.Fening, m3.Seguridad, m133.Ctrart, m133.Ctrfge, m3.Fdenum  
                        FROM tmedi m133, ".$wbasedato."_000002 m2,".$wbasedato."_000003 m3
                     Where m2.Fenhis = m133.Ctrhis
                       And m2.Fening = m133.Ctring
                       AND m2.Fenfec = m133.Ctrfge
                       And m3.Fdeart = m133.Ctrart
                       And m3.Fdenum = m2.Fennum
                       And m2.Fenfue IN  (SELECT ccofca FROM ".$wbasedato."_000011 WHERE ccofac = 'on' )
					   ";

      
          $res2 = mysql_query($qCon2,$conex) or die (mysql_errno()." - en el query: ".$qCon2." - ".mysql_error());
		  
		  $arrmovhos2 = array();
          $arrmovhos3 = array();

          while($row = mysql_fetch_assoc($res2)){

              if( !isset( $arrmovhos2[$row['Fenhis'].'-'.$row['Fening'].'-'.$row['Ctrart'].'-'.$row['Ctrfge']] ) ){
                 $arrmovhos2[$row['Fenhis'].'-'.$row['Fening'].'-'.$row['Ctrart'].'-'.$row['Ctrfge']] = substr($row['Seguridad'],-5);
                 $arrmovhos3[$row['Fenhis'].'-'.$row['Fening'].'-'.$row['Ctrart'].'-'.$row['Ctrfge']] = $row['Fdenum'];
			  }
          }

          if  ($numcon > 0){
             
              while($row = mysql_fetch_assoc($res_con)){

					 /* Consultar usuario que entrega los medicamentos*/
                   $wusuarioent  = (array_key_exists($row['Kadhis'].'-'.$row['Kading'].'-'.$row['Kadart'].'-'.$row['Ctrfge'],$arrmovhos2)) ? $arrmovhos2[$row['Kadhis'].'-'.$row['Kading'].'-'.$row['Kadart'].'-'.$row['Ctrfge']]:'';
				   
                   // if ($wusuarioent != '')
                       // $datosEntrega = (array_key_exists($wusuarioent,$wusuarios)) ? $wusuarios[$wusuarioent]:'';
                   // else
                       // $datosEntrega = '';
                   
                  /* Construyo el array principal para posteriormente pintar en la tabla principal del formulario 
                     Validando que la orden tenga dispensación */
                   if( $wusuarioent != '' ){
					   
					    $query15 = "SELECT SUM( Apldos ) AS  'canDispensada', Aplufr, Aplhis, Apling, Aplart, Aplido, Aplfec
									   FROM  ".$wbasedato."_000015 
									  WHERE  Aplhis =  '".$row['Ctrhis']."'
										AND  Apling =  '".$row['Ctring']."'
										AND  Aplart =  '".$row['Kadart']."'
										AND  Aplido =  '".$row['Ctrido']."'
										AND  Aplfec =  '".$row['Ctrfge']."'
								   GROUP BY Aplhis, Apling, Aplart, Aplido, Aplfec";
						
						$resQuery15 =mysql_query($query15,$conex) or die( "Error en el query $query15" );
					
						if( $row1Query15 = mysql_fetch_assoc($resQuery15) ){
							$row['canDispensada'] 	= $row1Query15['canDispensada'];
							$row['Aplufr'] 			= $row1Query15['Aplufr'];
						}
						else{
							continue;
						}
						
						$datosEntrega = (array_key_exists($wusuarioent,$wusuarios)) ? $wusuarios[$wusuarioent]:'';
            $ideEntrega=array();
					  
            if ($datosEntrega !=''); 
						    $ideEntrega = explode('|',$datosEntrega);

						/* Seleccionar código y nombre del diagnóstico */
						$coddx = substr($row['Ctrdia'],0,4);
						$nomdx = substr($row['Ctrdia'],5,80);

						/* Seleccionar forma, frecuencia y via del Medicamento en las tablas movhos_000046, 
						movhos_000043, movhos_000040 respectivamente. */
						$formaMedicamento      = ($row['Ffaele'] == '' ? $row['Ffanom'] : $row['Ffaele'] );

						$frecuenciaMedicamento = ($row['Perele'] == '' ? $row['frecuencia'] : $row['Perele'] );

						$viaMedicamento        = ($row['Viaele'] == '' ? $row['Viades'] : $row['Viaele'] );

						// if( $row['Ctrcon'] == '123982' ) echo "::::::::::".$arrmovhos2[$row['Kadhis'].'-'.$row['Kading'].'-'.$row['Kadart'].'-'.$row['Ctrfge']]."------";		   
						/* Consultar usuario que recibe los medicamentos*/
						// $wusuariorec  = (array_key_exists($row['Kadhis'].'-'.$row['Kading'].'-'.$row['Kadart'].'-'.$row['Ctrfge'],$arrtipmed)) ? $arrtipmed[$row['Kadhis'].'-'.$row['Kading'].'-'.$row['Kadart'].'-'.$row['Ctrfge']]:''; 

						/* Consultar los datos del Medico, en el array wmedicos */
						$datosMedico  = (array_key_exists(trim($row['Ctrmed']),$wmedicos)) ? $wmedicos[trim($row['Ctrmed'])]:'';

						list($Medno1,$Medno2,$Medap1,$Medap2,$Medreg,$Medtdo,$Meddoc,$Espnom) = explode('|',$datosMedico);
						
						/* Cantidad dispensada */
					    $canDispensada = round($row['canDispensada'],2);
					   
					    $wusuariorec = '';
						$query93 = "SELECT Recure
									  FROM ".$wbasedato."_000093 
									 WHERE Rechis = '".$row['Ctrhis']."'
									   AND Recing = '".$row['Ctring']."'
									   AND Recnum = '".$arrmovhos3[$row['Kadhis'].'-'.$row['Kading'].'-'.$row['Kadart'].'-'.$row['Ctrfge']]."'
									   AND Recart = '".$row['Kadart']."'
									   ";
						
						$resQuery93 =mysql_query($query93,$conex) or die( "Error en el query $query93" );
						
						if( $row1Query93 = mysql_fetch_assoc($resQuery93) ){
							$wusuariorec = substr( $row1Query93['Recure'],-5 );
						}

						/* Consultar en la tabla talhuma_000013 los datos de identificacion y nombre*/ 
						if ($wusuariorec != '')
						   $datosRecibe  = (array_key_exists($wusuariorec,$wusuarios)) ? $wusuarios[$wusuariorec]:'';
						else
						   $datosRecibe  = '';

            $ideRecibe = array();            

            if ($datosRecibe != '')
						    $ideRecibe = explode('|',$datosRecibe);
                  
                        $arr_resultado[] = array("codigo_formula"      => $row['Ctrcon'],
                                               "nombre_institucion"    => utf8_encode($nomemp), 
                                               "codigo_institucion"    => $wcodigohabil, 
                                               "municipio_institucion" => utf8_encode($munemp),
                                               "direccion_institucion" => utf8_encode($diremp),
                                               "fecha_institucion"     => $row['Ctrfge'],
                                               "priape_paciente"       => utf8_encode($row['Pacap1']),
                                               "segape_paciente"       => utf8_encode($row['Pacap2']),
                                               "prinom_paciente"       => utf8_encode($row['Pacno1']),
                                               "segnom_paciente"       => utf8_encode($row['Pacno2']),
                                               "tipodoc_paciente"      => $row['Pactdo'],
                                               "nrodoc_paciente"       => $row['Pacdoc'],
                                               "edad_paciente"         => edad($row['Pacfna']),
                                               "genero_paciente"       => $row['Pacsex'],
                                               "direccion_paciente"    => utf8_encode($row['Pacdir']),
                                               "telefono_paciente"     => $row['Pactel'],
                                               "municipio_paciente"    => utf8_encode($row['nomCiudad']),
                                               "departa_paciente"      => utf8_encode($row['nomDepartamento']),
                                               "afiliacion_paciente"   => $row['afiliacion'],
                                               "entidad_paciente"      => utf8_encode($row['Empnom']),
                                               "identifica_recibe"     => $ideRecibe[0] == '' ? $ideEntrega[0] : $ideRecibe[0],
                                               "nombre_recibe"         => $ideRecibe[0] == '' ? utf8_encode($ideEntrega[1]) : utf8_encode($ideRecibe[1]),
                                               "identifica_entrega"    => $ideEntrega[0],
                                               "nombre_entrega"        => utf8_encode($ideEntrega[1]),
                                               "tipoprofesion_medico"  => utf8_encode($Espnom),
                                               "primerape_medico"      => utf8_encode($Medap1),
                                               "segundoape_medico"     => utf8_encode($Medap2),
                                               "primernom_medico"      => utf8_encode($Medno1),
                                               "segundonom_medico"     => utf8_encode($Medno2),
                                               "tipdocumento_medico"   => $Medtdo,
                                               "nrodocumento_medico"   => $Meddoc,
                                               "resolucion_medico"     => $Medreg,                                              
                                               "nombregen_medicamento" => utf8_encode($row['Artgen']),         
                                               "concentra_medicamento" => $row['concentracion'],
                                               "forma_medicamento"     => utf8_encode($formaMedicamento),
                                               "dosis_medicamento"     => $row['Kadcfr'].' '.$row['Aplufr'],
                                               "via_medicamento"       => utf8_encode($viaMedicamento),   
                                               "frecuencia_medicamento"=> utf8_encode($frecuenciaMedicamento),
                                               "cantidad_medicamento"  => $row['Ctrcan'],
                                               "cantidtot_medicamento" => $canDispensada.' '.$row['Aplufr'],
                                               "tiempotto_medicamento" => '1 dia',
                                               "nombre_diagnostico"    => utf8_encode($nomdx),
                                               "cie_diagnostico"       => $coddx );
                    }
                                            
              }  
            
              $respuesta['resultado'] = $arr_resultado;

          }else{
              $respuesta['error']   = true;
              $respuesta['mensaje'] = 'No se encontraron registros';

          }

          echo json_encode($respuesta);

          return;
      }


      // Consultar toda la tabla de usuarios 
      function consultarUsuarios($wbasedatotal,$conex,$wemp_pmla){     
        
          $strtipvar = array();

          $q  = " SELECT Ideuse, Ideced, concat(Ideno1,' ',Ideno2,' ',Ideap1,' ',Ideap2) as Nomemp
                  From ".$wbasedatotal."_000013 
                  Where Ideest ='on'
                        And Ideap1 !=''
                  Order by Nomemp";

          $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

          while($row = mysql_fetch_assoc($res)){
                 $usuariotal = substr($row['Ideuse'],0,5);
                 $strtipvar[$usuariotal] = $row['Ideced'].'|'.utf8_encode($row['Nomemp']);
          }

          return $strtipvar;
      }


      // Consultar toda la tabla de medicos
      function consultarMedicos($wbasedato,$conex,$wemp_pmla){     
        
          $strtipvar = array();

          $q  = " SELECT m48.Medno1, m48.Medno2, m48.Medap1, m48.Medap2, m48.Meduma,
                         m48.Medreg, m48.Medtdo, m48.Meddoc, m44.Espnom
                  From ".$wbasedato."_000048 m48
                  Left Join  ".$wbasedato."_000044 m44 
                            on m44.Espcod = m48.Medesp          
                  Where m48.Medest ='on' ";

          $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

          while($row = mysql_fetch_assoc($res)){

                 $strtipvar[$row['Meduma']] = utf8_encode($row['Medno1']).'|'.
                                              utf8_encode($row['Medno2']).'|'.
                                              utf8_encode($row['Medap1']).'|'.
                                              utf8_encode($row['Medap2']).'|'.
                                              $row['Medreg'].'|'.$row['Medtdo'].'|'.
                                              $row['Meddoc'].'|'.$row['Espnom'];
          }

          return $strtipvar;

      }
      
   
      function edad($fecha_nacimiento){
          list($y, $m, $d) = explode("-", $fecha_nacimiento);
          $y_dif = date("Y") - $y;
          $m_dif = date("m") - $m;
          $d_dif = date("d") - $d;
          if ((($d_dif < 0) && ($m_dif == 0)) || ($m_dif < 0))
              $y_dif--;
          return $y_dif;
      }


      function codificar($concepto_dec){

        return str_replace("#","nro",$concepto_dec);
      }


      function decodificar($concepto_cod){

        return str_replace("__",".",$concepto_cod);
      }


     // Consultar todos los Centros de Costos para el campo autocompletar
      function consultarCentros($wbasemovhos,$conex,$wemp_pmla){
          
          $strtipvar = array();

          $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                      FROM    ".$wbasemovhos."_000011 AS tb1                                    
                      GROUP BY    tb1.Ccocod
                      ORDER BY    tb1.Cconom";

          $res = mysql_query($query,$conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());
          
          while($row = mysql_fetch_assoc($res)){
               $strtipvar[$row['codigo']] = $row['codigo'].'-'.utf8_encode($row['nombre']);
          }

          return $strtipvar;
      }

     // *****************************************         FIN PHP         ********************************************
  ?>
  <!DOCTYPE html>
  <head>
    <title>Reporte de Medicamentos controlados</title>
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

      var celda_ant="";
      var celda_ant_clase="";
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
              maxDate:"+0D",
              buttonImage: "../../images/medical/root/calendar.gif",
              buttonImageOnly: true,
          });   

          ActivarControles(); 

      }); // Finalizar Ready()


      //Consulta principal del reporte, según filtros seleccionados
      function Consultar(){

          // Validar que el rango de fecha esté diligenciado
          if (  $("#txtfecini").val()=='' || $("#txtfecfin").val()=='' ){

                jAlert('Falta diligenciar Informaci\u00f3n');
                return;
          }      

          // Activar div que muestra el tiempo de proceso
          document.getElementById("divcargando").style.display   = "";

          $("#tblmensaje").hide();    
          $("#tbldetalle > tbody:last").children().remove(); 

          $.post("Reporte_Medicamentosdecontrol.php",
                {
                    consultaAjax:  true,
                    accion      :  'ConsultarDetalle',                    
                    wemp_pmla   :  $("#wemp_pmla").val(),
                    wfechaini   :  $("#txtfecini").val(),
                    wfechafin   :  $("#txtfecfin").val()
                }, function(data){
console.log(data);
                  // Ocultar div que muestra el tiempo de proceso
                  document.getElementById("divcargando").style.display    = "none";

                  // En caso de no encontrar registros
                  if (data.error == true){

                      $(".clsdetalle").hide();
                      jAlert(data.mensaje, "Alerta");

                  }else{                        

                      $("#tblexcel > tbody:last").children().remove(); 

                      var fila     = "fila1";
                      var stringTr = "";
                      stringEx     = "";    

                      jQuery.each(data.resultado, function(){

                            stringTr = stringTr + '<tr class="'+fila+'">'
                                       + '<td align="center">'+this.codigo_formula+'</td>'
                                       + '<td align="center">'+this.nombre_institucion+'</td>'
                                       + '<td align="center">'+this.codigo_institucion+'</td>'
                                       + '<td align="center">'+this.municipio_institucion+'</td>'
                                       + '<td align="center">'+this.direccion_institucion+'</td>'
                                       + '<td align="center">'+this.fecha_institucion+'</td>'                                       
                                       + '<td align="center">'+this.priape_paciente+'</td>'
                                       + '<td align="center">'+this.segape_paciente+'</td>'
                                       + '<td align="center">'+this.prinom_paciente+' '+this.segnom_paciente+'</td>'
                                       + '<td align="center">'+this.tipodoc_paciente+'</td>'
                                       + '<td align="center">'+this.nrodoc_paciente+'</td>'
                                       + '<td align="center">'+this.edad_paciente+'</td>'
                                       + '<td align="center">'+this.genero_paciente+'</td>'
                                       + '<td align="center">'+this.direccion_paciente+'</td>'
                                       + '<td align="center">'+this.telefono_paciente+'</td>'
                                       + '<td align="center">'+this.municipio_paciente+'</td>'
                                       + '<td align="center">'+this.departa_paciente+'</td>'
                                       + '<td align="center">'+this.afiliacion_paciente+'</td>'
                                       + '<td align="center">'+this.entidad_paciente+'</td>'
                                       + '<td align="center">'+this.nombregen_medicamento+'</td>'
                                       + '<td align="center">'+this.concentra_medicamento+'</td>'

                                       + '<td align="center">'+this.forma_medicamento+'</td>'
                                       + '<td align="center">'+this.dosis_medicamento+'</td>'
                                       + '<td align="center">'+this.via_medicamento+'</td>'
                                       + '<td align="center">'+this.frecuencia_medicamento+'</td>'
                                       + '<td align="center">'+this.cantidad_medicamento+'</td>'
                                       + '<td align="center">'+this.cantidtot_medicamento+'</td>'
                                       + '<td align="center">'+this.tiempotto_medicamento+'</td>'                                      
                                       + '<td align="center">'+this.nombre_diagnostico+'</td>'
                                       + '<td align="center">'+this.cie_diagnostico+'</td>'                    
                                       + '<td align="center">'+this.tipoprofesion_medico+'</td>'
                                       + '<td align="center">'+this.primerape_medico+'</td>'
                                       + '<td align="center">'+this.segundoape_medico+'</td>'
                                       + '<td align="center">'+this.primernom_medico+' '+this.segundonom_medico+'</td>'
                                       + '<td align="center">'+this.tipdocumento_medico+'</td>'
                                       + '<td align="center">'+this.nrodocumento_medico+'</td>'
                                       + '<td align="center">'+this.resolucion_medico+'</td>'

                                       + '<td align="center">'+this.nombre_recibe+'</td>' 
                                       + '<td align="center">CC</td>' 
                                       + '<td align="center">'+this.identifica_recibe+'</td>' 
                                       + '<td align="center">'+this.nombre_entrega+'</td>' 
                                       + '<td align="center">CC</td>' 
                                       + '<td align="center">'+this.identifica_entrega+'</td>'                                        
                                       + '<tr>';

                            //Variable Global para exportar el contenido de la tabla en formato CSV

                            stringEx = stringEx + this.codigo_formula
                                            +';'+ this.nombre_institucion
                                            +';'+ this.codigo_institucion
                                            +';'+ this.municipio_institucion
                                            +';'+ this.direccion_institucion
                                            +';'+ this.fecha_institucion
                                            +';'+ this.priape_paciente
                                            +';'+ this.segape_paciente
                                            +';'+ this.prinom_paciente+''+this.segnom_paciente
                                            +';'+ this.tipodoc_paciente
                                            +';'+ this.nrodoc_paciente
                                            +';'+ this.edad_paciente 
                                            +';'+ this.genero_paciente  
                                            +';'+ this.direccion_paciente
                                            +';'+ this.telefono_paciente
                                            +';'+ this.municipio_paciente
                                            +';'+ this.departa_paciente
                                            +';'+ this.afiliacion_paciente
                                            +';'+ this.entidad_paciente
                                            +';'+ this.nombregen_medicamento
                                            +';'+ this.concentra_medicamento
                                            +';'+ this.forma_medicamento
                                            +';'+ this.dosis_medicamento
                                            +';'+ this.via_medicamento                                  
                                            +';'+ this.frecuencia_medicamento
                                            +';'+ this.cantidad_medicamento
                                            +';'+ this.cantidtot_medicamento
                                            +';'+ this.tiempotto_medicamento                                     
                                            +';'+ this.nombre_diagnostico
                                            +';'+ this.cie_diagnostico                    
                                            +';'+ this.tipoprofesion_medico
                                            +';'+ this.primerape_medico
                                            +';'+ this.segundoape_medico
                                            +';'+ this.primernom_medico+' '+this.segundonom_medico
                                            +';'+ this.tipdocumento_medico
                                            +';'+ this.nrodocumento_medico
                                            +';'+ this.resolucion_medico
                                            +';'+ this.nombre_recibe
                                            +';'+ 'CC'
                                            +';'+ this.identifica_recibe
                                            +';'+ this.nombre_entrega 
                                            +';'+ 'CC'
                                            +';'+ this.identifica_entrega
                                            +'\r\n';
                                          
                            fila    = fila == "fila1" ? "fila2" : "fila1";        
            
                      });

                      $('#tbldetalle > tbody:last').append(stringTr);

                      $('#tblexcel > tbody:last').append(stringTr);
                      
                      $("#tblbuscar, #tbldetalle").show();

                      $('input#id_search_pacientes').quicksearch('table#tbldetalle tbody tr');

                      ActivarControles();
                  }                  

               },"json");
       }

       // Exportar el contenido del string 'stringEx' a formato delimitado por comas 
       function Exporcsv(){

            var usu = document.createElement('a');
            usu.setAttribute('href', 'data:text/csv;charset=utf-8,' + encodeURIComponent(stringEx));
            usu.setAttribute('download','Medicamentos_de_Control.csv');

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
            var tabla_div = document.getElementById('tbldetalle');
            var tabla_html = tabla_div.outerHTML.replace(/ /g, '%20');
            
            tmpElemento.href = data_type + ', ' + tabla_html;
            //Asignamos el nombre a nuestro EXCEL
            tmpElemento.download = 'Medicamentos_de_Control.xls';
            // Simulamos el click al elemento creado para descargarlo
            tmpElemento.click();

       }


       function ActivarControles()
       {

          $("#tbldetalle tbody tr").on('click',function(event) {
                $("#tbldetalle tbody tr").removeClass('row_selected');    
                $(this).addClass('row_selected');
          });

      }

      
       // Sacar un mensaje de alerta con formato predeterminado
       function alerta(txt){
         $("#textoAlerta").text( txt );
         $.blockUI({ message: $('#msjAlerta') });
           setTimeout( function(){
                   $.unblockUI();
                }, 1800 );
       }


       function cerrarVentana()
       {
          if(confirm("Esta seguro de salir?") == true){
            window.close();}
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

        .ui-multiselect { height:25px; overflow-x:hidden; padding:2px 0 2px 4px; text-align:left;font-size: 10pt; }
          
         BODY {
            font-family: verdana;
            font-size: 10pt;
            width: auto;
            height:auto;
         }

         .tbldetalle tr {
            height: 50px;
         }

         .row_selected {background-color:lightyellow};

    </style>
    </head>
    <body >
      <?php
        echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
        $wtitulo  = "REPORTE MEDICAMENTOS DE CONTROL";
        encabezado($wtitulo, $wactualiz, 'clinica');
      ?>
      <table align='center' width="30%" style='border: 1px solid gray'>
          <tr height='30px'>
            <td class='fila1'><b>Fecha Inicial</b></td>
            <td class='fila2'><input type='text' id='txtfecini' name='txtfecini' size='25' placeholder="Ingrese Fecha Inicial" readonly></td>
          </tr>
          <tr>
            <td class='fila1'><b>Fecha Final</b></td>
            <td class='fila2'><input type='text' id='txtfecfin' name='txtfecfin' size='25' placeholder="Ingrese Fecha Final" readonly></td>
          </tr>
      </table>
      <br>
      <center>
      <table> 
          <tr>
          <td>&nbsp;&nbsp;<input type='button' id='btnConsultar' name='btnConsultar' class='button' value='Consultar' onclick='Consultar()'></td>
          <td>&nbsp;&nbsp;<input type='button' id='btnExportar'  name='btnExportar'  class='button' value='Exportar'  onclick='Exporcsv()'></td>
          <td>&nbsp;&nbsp;<input type='button' id='btnSalir'     name='btnSalir'     class='button' value='Salir'     onclick='cerrarVentana()'></td>
          </tr>
      </table>
      </center>
      <div id="divcargando" name="divcargando" class='clsdetalle' style='display:none;' ><center><img width="26" height="26" border="0" src="../../images/medical/ajax-loader9.gif"></center></div>
      <br>&nbsp;&nbsp;
      <div>
      <table align='left' style='display:none;' id='tblbuscar' name='tblbuscar' class='clsdetalle'>
          <tr><td></td><td class='fila1'>&nbsp;Filtrar listado:&nbsp;&nbsp;<input id="id_search_pacientes" type="text" value="" size="20" name="id_search_pacientes" placeholder="Buscar en listado">&nbsp;&nbsp;</td>
          </tr>
          </table>
      </div>
      <br>&nbsp;
      <center>
      <table width='100%' id='tbldetalle' name='tbldetalle' class='clsdetalle' style='border: 1px solid blue;display:none;' >
          <thead>
            <tr class='encabezadotabla'>
              <th align='center' colspan='6'>INSTITUCI&Oacute;N</th>
              <th align='center' colspan='13'>PACIENTE</th>
              <th align='center' colspan='8'>MEDICAMENTO</th>
              <th align='center' colspan='3'>DIAGN&Oacute;STICO</th>
              <th align='center' colspan='7'>M&Eacute;DICO</th>
              <th align='center' colspan='3'>RECIBE <br> MEDICAMENTO</th>              
              <th align='center' colspan='3'>ENTREGA <br> MEDICAMENTO</th>
           </tr>
            <tr class='encabezadotabla'>
              <th align='center'>Codificaci&oacute;n <br> f&oacute;rmula </th>
              <th align='center'>Nombre <br> establecimiento <br>o Instituci&oacute;n</th>
              <th align='center'>C&oacute;digo <br> SIINFORMA <br> establecimiento <br> o instituci&oacute;n</th>
              <th align='center'>Municipio ubicaci&oacute;n </th>
              <th align='center'>Direcci&oacute;n establecimiento</th>
              <th align='center'>Fecha de prescripci&oacute;n</th>

              <th align='center'>Primer apellido</th>
              <th align='center'>Segundo Apellido</th>
              <th align='center'>Nombres completos</th>
              <th align='center'>Tipo documento identificaci&oacute;n</th>
              <th align='center'>N&uacute;mero de identificaci&oacute;n</th>
              <th align='center'>Edad</th>
              <th align='center'>G&eacute;nero</th>
              <th align='center'>Direcci&oacute;n Residencia</th>
              <th align='center'>Tel&eacute;fono</th>
              <th align='center'>Municipio residencia Paciente</th>
              <th align='center'>Departamento Residencia</th>
              <th align='center'>Afiliaci&oacute;n SGSSS</th>
              <th align='center'>Nombre entidad aseguradora</th>

              <th align='center'>Nombre Gen&eacute;rico</th>
              <th align='center'>Concentraci&oacute;n</th>
              <th align='center'>Forma Farmac&eacute;utica</th>
              <th align='center'>Dosis</th>
              <th align='center'>Via de administraci&oacute;n</th>
              <th align='center'>Frecuencia de administraci&oacute;n (24 horas)</th>
              <th align='center'>Cantidad total prescrita N&uacute;meros</th>
              <th align='center'>Cantidad total administrada N&uacute;meros</th>

              <th align='center'>Tiempo de tratamiento</th>
              <th align='center'>Diagn&oacute;stico</th>
              <th align='center'>C&oacute;digo CIE 10</th>

              <th align='center'>Tipo de profesi&oacute;n</th>
              <th align='center'>Primer apellido</th>
              <th align='center'>Segundo Apellido</th>
              <th align='center'>Nombres completos</th>
              <th align='center'>Tipo documento identificaci&oacute;n</th>
              <th align='center'>N&uacute;mero de identificaci&oacute;n</th>
              <th align='center'>Resoluci&oacute;n que autoriza el ejercicio de la profesi&oacute;n</th>

              <th align='center'>Nombres y apellidos completos</th>
              <th align='center'>Tipo documento identificaci&oacute;n</th>
              <th align='center'>N&uacute;mero de identificaci&oacute;n</th>

              <th align='center'>Nombres y apellidos completos</th>
              <th align='center'>Tipo documento identificaci&oacute;n</th>
              <th align='center'>N&uacute;mero de identificaci&oacute;n</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
      </table>
      <table width='1650px' id='tblexcel' name='tblexcel' class='tblexcel' style='border: 1px solid blue;display:none;' >
      <thead>
          <tr class='encabezadotabla'>
              <th align='center' colspan='6'>INSTITUCI&Oacute;N</th>
              <th align='center' colspan='13'>PACIENTE</th>
              <th align='center' colspan='8'>MEDICAMENTO</th>
              <th align='center' colspan='3'>DIAGN&Oacute;STICO</th>
              <th align='center' rowspan='7'>M&Eacute;DICO</th>
              <th align='center' colspan='3'>RECIBE_MEDICAMENTO</th>              
              <th align='center' rowspan='3'>ENTREGA_MEDICAMENTO</th>
           </tr>
            <tr class='encabezadotabla'>
              <th align='center'>Codificación_f&oacute;rmula</th>
              <th align='center'>Nombre_establecimiento_o_Instituci&oacute;n</th>
              <th align='center'>Código_SIINFORMA_establecimiento_o_instituci&oacute;n</th>
              <th align='center'>Municipio_ubicaci&oacute;n</th>
              <th align='center'>Direcci&oacute;n_establecimiento</th>
              <th align='center'>Fecha_de_prescripci&oacute;n</th>

              <th align='center'>Primer_apellido</th>
              <th align='center'>Segundo_Apellido</th>
              <th align='center'>Nombres_completos</th>
              <th align='center'>Tipo_documento_identificaci&oacute;n</th>
              <th align='center'>N&uacute;mero_de_identificaci&oacute;n</th>
              <th align='center'>Edad</th>
              <th align='center'>G&eacute;nero</th>
              <th align='center'>Direcci&oacute;n Residencia</th>
              <th align='center'>Tel&eacute;fono</th>
              <th align='center'>Municipio residencia Paciente</th>
              <th align='center'>Departamento Residencia</th>
              <th align='center'>Afiliaci&oacute;n_SGSSS</th>
              <th align='center'>Nombre_entidad_aseguradora</th>

              <th align='center'>Nombre_Gen&eacute;rico</th>
              <th align='center'>Concentraci&oacute;n</th>
              <th align='center'>Forma_Farmac&eacute;utica</th>
              <th align='center'>Dosis</th>
              <th align='center'>Via de administraci&oacute;n</th>
              <th align='center'>Frecuencia_de_administraci&oacute;n (24 horas)</th>
              <th align='center'>Cantidad_total_prescrita N&uacute;meros</th>
              <th align='center'>Cantidad_total_administrada N&uacute;meros</th>

              <th align='center'>Tiempo_de_tratamiento</th>
              <th align='center'>Diagn&oacute;stico</th>
              <th align='center'>C&oacute;digo_CIE_10</th>

              <th align='center'>Tipo_de_profesi&oacute;n</th>
              <th align='center'>Primer_apellido</th>
              <th align='center'>Segundo_Apellido</th>
              <th align='center'>Nombres_completos</th>
              <th align='center'>Tipo_documento_identificaci&oacute;n</th>
              <th align='center'>N&uacute;mero_de_identificaci&oacute;n</th>
              <th align='center'>Resoluci&oacute;n_que_autoriza_el_ejercicio_de_la_profesi&oacute;n</th>

              <th align='center'>Nombres_y_apellidos_completos</th>
              <th align='center'>Tipo_documento_identificaci&oacute;n</th>
              <th align='center'>N&uacute;mero_de_identificaci&oacute;n</th>

              <th align='center'>Nombres_y_apellidos_completos</th>
              <th align='center'>Tipo_documento_identificaci&oacute;n</th>
              <th align='center'>N&uacute;mero_de_identificaci&oacute;n</th>
            </tr>                      

      </thead>
      <tbody>
      </tbody>
      </table>
      </center>
      <center>
      <table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;display:none;'>
        <tr><td class='fila2'>No hay registros para esta consulta</td></tr>
      </table>
      </center>
      <input type="HIDDEN" name="wcodusuario" id="wcodusuario">
    </body>
    </html>