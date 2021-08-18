<?php
 include_once("conex.php");
 include_once("root/comun.php");

 /*********************************************************************************************************************************
 *
 * Programa           : gestionar_postegreso.php
 * Fecha de Creación  : 2019-12-13
 * Autor              : Arleyda Insignares Ceballos
 * Descripcion        : Programa para gestionar las ordenes mediante un proceso de Post-Atención, indicando numero de autorizacion,
 *                      estado de la gestión y observaciones que son grabadas como vitacora de cada procedimiento.
 *               Modificaciones
 *   2020-02-13  Arleyda Insignares C. Se adiciona control para verificar saldos y determinar si se puede egresar o no.  
 *               Los insumos se verifican en movhos_000227 y medicamentos en movhos_000004.
 * 
 **********************************************************************************************************************************/
 
  $wactualiza = "2020-03-10";
  $consultaAjax='';

  if(!isset($_SESSION['user'])){
     echo "<center></br></br><table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
      <tr><td>Error, inicie nuevamente</td></tr>
      </table></center>";
     return;
  }

  header('Content-type: text/html;charset=ISO-8859-1');
  //*************************************   Inicio  ***********************************************************

  $conex         = obtenerConexionBD("matrix"); 
  $wbasehce      = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
  $wbasecliame   = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
  $wbasemovhos   = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
  $wfecha        = date("Y-m-d");
  $whora         = (string)date("H:i:s");
  $pos           = strpos($user,"-");
  $wusuario      = substr($user,$pos+1,strlen($user));

  // ***************************************    FUNCIONES AJAX   **********************************************

    // Case funciones AJAX
    if(isset($accion))
    {
       switch($accion)
       {
             case 'consultarOrdenes':
             {
                  $data = consultarOrdenes($conex, $wemp_pmla, $wbasehce, $wbasemovhos, $wbasecliame, $whistoria, $wingreso, $wingres);
        
                  echo json_encode($data);
                  break;
             }

             case 'grabarGestion':
             {
                  $data = grabarGestion($conex, $wemp_pmla, $wbasehce, $wbasemovhos, $wbasecliame, $whistoria, $wingreso, $objgestion, $wusuario,$watencion);
        
                  echo json_encode($data);
                  break;
             }     
       }

       return;
    }        

    ///////////////////////////////////////////////////////////////////
    //Grabar o actualizar la gestión por cada orden en cliame_000333 //
    ///////////////////////////////////////////////////////////////////
    function grabarGestion($conex, $wemp_pmla, $wbasehce, $wbasemovhos, $wbasecliame, $whistoria, $wingreso, $objgestion, $user,$watencion)
    {

          $arrgestion = array();
          $wsaldo = '';

          foreach( $objgestion as $keyTipo => $datosTipo )
          {

                $strcambios='';
             
                // Extraigo todos los campos, atributo del objeto principal, 
                // cada uno deberá contener un subindice nuevo y anterior (nue y ant)
                foreach( $datosTipo as $keySubtipo => $datosSubtipo ){
                         if ( strlen($keySubtipo) == 9 )
                              $arrgestion[substr($keySubtipo,0,6)][substr($keySubtipo,-3)] = $datosSubtipo;
                }

                foreach( $arrgestion as $keySubtipo => $datosSubtipo ){
                    
                    if ( ($datosSubtipo['ant'] !== $datosSubtipo['nue'])){

                        if( $datosSubtipo['nue'] !==''){

                            if (isset($datosSubtipo['obs']))
                                $nombrecampo = $datosSubtipo['obs'];
                            else
                                $nombrecampo = $keySubtipo;


                            // Cambiar el codigo por la descripción en el maestro cliame_000335
                            $strcampo = $datosSubtipo['nue'];

                            if (strlen($datosSubtipo['nue']) == 2)
                            {
                                $sqlEstado = "SELECT Eaudes
                                                FROM ".$wbasecliame."_000335
                                               WHERE Eaucod = '".$datosSubtipo['nue']."' 
                                                 AND Eauest = 'on' ";

                                $resEstado = mysqli_query($conex, $sqlEstado) or die("<b>ERROR EN QUERY MATRIX():</b><br>".mysqli_error());

                                if( $rowges = mysqli_fetch_assoc($resEstado) )
                                     $strcampo = $rowges['Eaudes'];
                            }

                            if ($datosSubtipo['ant'] == ''){
                                
                                    $strcambios .= ' '.$nombrecampo.'= '.$strcampo ;
                            }                            
                            else{

                                 if ($strcambios == '')
                                     $strcambios .= '\n Se modifica: '.$nombrecampo.'= '.$strcampo ;
                                 else
                                     $strcambios .= ' '.$nombrecampo.'= '.$strcampo ;  
                            }
                        }
                        else{
                            if ($datosSubtipo['nue'] != null && $datosSubtipo['ant'] != null )
                                $strcambios .= '\n Se retira informacion : '.$nombrecampo ;                            
                        }

                    }
                } 

                ////////////////////////////////////////////////////////////////////////////////////////
                // Consultar estado de la gestión para actualizar el estado de la orden en hce_000028 //
                ////////////////////////////////////////////////////////////////////////////////////////
                $sqlEstadoaut = "SELECT Eaucod,Eaudes,Eauaut,Eauter,Eaucau
                                FROM ".$wbasecliame."_000335
                                WHERE Eauest = 'on' 
                                  AND Eaucau != '' 
                                  AND Eaucod = '".$datosTipo['Estautnue']."' ";

                $resEstadoaut = mysqli_query($conex, $sqlEstadoaut) or die("<b>ERROR EN QUERY MATRIX():</b><br>".mysqli_error());

                $estadoAct = '';
                if( $rowAut = mysqli_fetch_assoc($resEstadoaut) ){
                    $estadoAct = $rowAut['Eaucau'];
                }

                ///////////////////////////////////////////////////////////////////////
                //Consultar si ya existe en la tabla de gestión                      //
                ///////////////////////////////////////////////////////////////////////
                $sqlgestion = " SELECT Gestel, Gescor, Gesesa, Gesrec, Gesesp, Gesite,
                                       Gesmot, Gesobs, Gesusu, Gesest, Gescoa, Id
                                 FROM ".$wbasecliame."_000333 cli333
                                 WHERE cli333.Geshis = '".$whistoria."'
                                   AND cli333.Gesing = '".$wingreso."'
                                   AND cli333.Gesnro = '".$datosTipo['Orden']."' 
                                   AND cli333.Gesite = '".$datosTipo['Nroite']."' 
                                   AND cli333.Gestor = '".$datosTipo['Tipord']."' ";
            
                $respon = mysqli_query($conex,$sqlgestion) or die("ERROR EN QUERY MATRIX()<br>".mysqli_error());  

              
                if( $respon && $respon->num_rows > 0){

                    if( $rowRes = mysqli_fetch_assoc($respon) ){
                     
                        $idactual = $rowRes['Id'];
                        
                        // Registro existente, diligenciar UPDATE
                        if ($watencion=='on'){
                            $sql= "UPDATE ".$wbasecliame."_000333
                                      SET Gestel = '".$datosTipo['Telresnue']."', 
                                          Gescor = '".$datosTipo['Mairesnue']."', 
                                          Gesesa = '".$datosTipo['Estautnue']."',
                                          Gesrec = '".$datosTipo['Fecrecnue']."',
                                          Gesesp = '".$datosTipo['Estespnue']."',
                                          Gesmot = '".$datosTipo['Gesmotnue']."',
                                          Gescoa = '".$datosTipo['Gescoanue']."',
                                          Gesate = '".$watencion."'
                                  WHERE Id = ".$idactual;

                            $resEjecact = mysqli_query($conex, $sql) or die("ERROR EN QUERY MATRIX()<br>".mysqli_error());
                        }
                            
                    }
                }
                else
                {

                    /////////////////////////////////////////////////////////////
                    // Grabar en la tabla principal de gestión 'cliame_000333' //
                    /////////////////////////////////////////////////////////////
                    $sqlges = "INSERT INTO ".$wbasecliame."_000333
                            (Medico, Fecha_data, Hora_data,  Geshis, Gesing, Gesnro, Gesite, Gestor, Gespro, Gestel, 
                             Gescor, Gesesa, Gesrec, Gesesp, Gesmot, Gesobs, Gesusu, Gescoa, Gesate, Gesest, Seguridad)
                            VALUES
                            ('".$wbasecliame."','".date("Y-m-d") ."','".date("H:i:s")."','".$whistoria."','".$wingreso."','".$datosTipo['Orden']."','".$datosTipo['Nroite']."','".$datosTipo['Tipord']."','".$datosTipo['Procedi']."','".$datosTipo['Telresnue']."','".$datosTipo['Mairesnue']."','".$datosTipo['Estautnue']."','".$datosTipo['Fecrecnue']."','".$datosTipo['Estespnue']."','".$datosTipo['Gesmotnue']."','".$datosTipo['Gesobsnue']."','".$user."','".$datosTipo['Gescoanue']."','".$watencion."','on','C-".$wbasecliame."')";

                    $resEjec = mysqli_query($conex, $sqlges) or die("ERROR EN QUERY MATRIX()<br>".mysqli_error());
                   
                }

                if ($watencion=='on')
                {
                    //En caso de que la orden se encuentre pendiente, actualizar el
                    //estado de la orden en hce_000028, campo 'Detesi'
                    if ($estadoAct != '')
                    {
                        //Realizar update en hce_000028 y en else también
                        $sqlAct= "UPDATE ".$wbasehce."_000028
                                     SET Detesi = '".$estadoAct."'
                                   WHERE Dettor = '".$datosTipo['Tipord']."' 
                                     AND Detnro = '".$datosTipo['Orden']."'
                                     AND Detite = '".$datosTipo['Nroite']."' ";

                        $resAct = mysqli_query($conex, $sqlAct) or die("ERROR EN QUERY MATRIX()<br>".mysqli_error());

                    }

                    //Adicionar observacion a Bitacora, tabla cliame_000334
                    if ($datosTipo['Gesobsnue'] !=='' || $strcambios !=='' )
                    {
                        $sqlbit = "INSERT INTO ".$wbasecliame."_000334
                                (Medico, Fecha_data, Hora_data,  Bithis, Biting, Bitnro, Bitite, Bittor, Bitpro, Bitusu, Bitobs, Bitest, Seguridad)
                                VALUES
                                ('".$wbasecliame."','".date("Y-m-d") ."','".date("H:i:s")."','".$whistoria."','".$wingreso."','".$datosTipo['Orden']."','".$datosTipo['Nroite']."','".$datosTipo['Tipord']."','".$datosTipo['Procedi']."','".$user."','".$strcambios."','on','C-".$wbasecliame."')";

                        $resEjecbit = mysqli_query($conex, $sqlbit) or die("ERROR EN QUERY MATRIX()<br>".mysqli_error());

                    }
                }
          } //Fin primer foreach

          /////////////////////////////////////////
          //Consulto código de Cierre de gestión //
          /////////////////////////////////////////
          $sqlEstado = "SELECT Eaucod,Eauter
                          FROM ".$wbasecliame."_000335
                          WHERE Eauest = 'on' 
                            AND Eauter = 'on' ";

          $resEstado = mysqli_query($conex, $sqlEstado) or die("<b>ERROR EN QUERY MATRIX():</b><br>".mysqli_error());

          $codCierre = '';
          if( $rowAut = mysqli_fetch_assoc($resEstado) ){
              $codCierre = $rowAut['Eaucod'];
          }

          /////////////////////////////////////////////////////////////////
          // * * * Proceso para definir Alta definitiva en movhos_000018 //
          /////////////////////////////////////////////////////////////////
          $fecha = date("Y-m-d");
          $hora = date("H:i:s");

          // Verificar la actualización del Alta                
          $sqlGestion = " SELECT Gestel, Gescor
                           FROM ".$wbasecliame."_000333 cli333
                           WHERE cli333.Geshis = '".$whistoria."'
                             AND cli333.Gesing = '".$wingreso."' 
                             AND cli333.Gesesp != '".$codCierre."' ";
      
          $resEstado = mysqli_query($conex,$sqlGestion) or die("ERROR EN QUERY MATRIX()<br>".mysqli_error());  

          if( $resEstado && $resEstado->num_rows == 0){

              ///////////////////////////////
              //Consultar saldo en insumos //
              ///////////////////////////////
              $qInsumos =  " SELECT Carins, SUM(Carcca - Carcap - Carcde) as saldo_insumos 
                              FROM ".$wbasemovhos."_000227 
                              WHERE Carhis = '". $whistoria ."'
                                AND Caring = '". $wingreso ."'
                                AND Carcca - Carcap - Carcde > 0
                                AND Carest = 'on' ";


              $resInsumos = mysqli_query($conex,$qInsumos) or die("ERROR EN QUERY MATRIX()<br>".mysqli_error());

              if( $resInsumos && $resInsumos->num_rows > 0)
              {
                  if( $rowInsumos = mysqli_fetch_assoc($resInsumos) ){
                      if ($rowInsumos['saldo_insumos'] > 0)
                          $wsaldo = 'Insumos';
                  }

              }    

              $qMedicam = " SELECT SUM(spauen-spausa) as saldo_medicamentos
                            FROM ".$wbasemovhos."_000004 
                            WHERE spahis  = '". $whistoria ."'
                              AND spaing  = '". $wingreso ."'
                              AND ROUND((spauen-spausa),3) > 0 ";

              $resMedicamento = mysqli_query($conex,$qMedicam) or die("ERROR EN QUERY MATRIX()<br>".mysqli_error());

              if( $resMedicamento && $resMedicamento->num_rows > 0)
              {           
                  if( $rowInsumos = mysqli_fetch_assoc($resMedicamento) ){
                      if ($rowInsumos['saldo_medicamentos'] > 0)
                          $wsaldo = 'Medicamentos';
                  }

              }


              if( $wsaldo == '')
              {              
                  
                  /////////////////////////////////////////
                  // Actualizar el Alta en movhos_000018 //
                  /////////////////////////////////////////
                  $sqlAlta = " UPDATE ".$wbasemovhos."_000018 
                                  SET Ubiald = 'on', 
                                      Ubifad='".$fecha."',
                                      Ubihad='".$hora."'
                                WHERE Ubihis = '".$whistoria."'
                                  AND Ubiing = '".$wingreso."' ";


                  $resAlta = mysqli_query($conex,$sqlAlta) or die("ERROR EN QUERY MATRIX()<br>".mysqli_error());  
              }
          }

          return $wsaldo;
    }


    // Consultar las Ordenes pertenecientes a la historia e ingreso del paciente
    function consultarOrdenes($conex, $wemp_pmla, $wbasehce, $wbasemovhos, $wbasecliame, $whistoria, $wingreso, $wingres)
    {

            $resultado = array("nroregistro"=>0, "html"=>array(), "estadopro" => "<option>Pendiente</option>", "centros" => "", "codcierre" => "");
            
            //////////////////////////////////////////////////////////////////////////////////////////////////
            // Construir array de estados de autorización consultando en la tabla de maestros cliame_000335 //
            //////////////////////////////////////////////////////////////////////////////////////////////////
            
            $sqlEstados = "SELECT Eaucod,Eaudes,Eauaut,Eaunop,Eauaci,Eauopc,Eauter
                            FROM ".$wbasecliame."_000335
                           WHERE Eauest = 'on'";

            $resEstado = mysqli_query($conex, $sqlEstados) or die("<b>ERROR EN QUERY MATRIX():</b><br>".mysqli_error());

            $arEstados = array();

            while($rowEstado = mysqli_fetch_assoc($resEstado))
            {
                  $arEstados[$rowEstado['Eaucod']]= array("Eaucod" => $rowEstado['Eaucod'], "Eaudes" => $rowEstado['Eaudes'], 
                                                          "Eauaut" => $rowEstado['Eauaut'], "Eaunop" => $rowEstado['Eaunop'],
                                                          "Eauaci" => $rowEstado['Eauaci'], "Eauopc" => $rowEstado['Eauopc'],
                                                          "Eauter" => $rowEstado['Eauter'] );
                  if($rowEstado['Eauter'] == 'on')
                     $resultado['codcierre'] = $rowEstado['Eaucod'];
            }

  
            ///////////////////////////////////////////////
            // Construir el listado de centros de costos //
            ///////////////////////////////////////////////
            $q_centrocos =  " SELECT Ccocod,Cconom 
                              FROM ".$wbasemovhos."_000011                        
                             ORDER BY Cconom ";

            $res_cen  =  mysqli_query($conex,$q_centrocos) or die ("Error: en el query:  - ".mysqli_error());
            
            $resultado['centros']= "<option value='%'></option>";   

            if( $res_cen && $res_cen->num_rows>0){
                 
                 while( $row = mysql_fetch_assoc($res_cen)) {

                       $resultado['centros'].= "<option value='".$row['Ccocod']."'>".utf8_encode($row['Cconom'])."</option>";
                 }
            }           


            ////////////////////////////////////////////////////////////////////////////////
            //Consultar los datos de la institucion responsable en el ingreso             //
            ////////////////////////////////////////////////////////////////////////////////
            $sqlRespon = " SELECT Empnit AS nit_responsable, Empnom AS ent_responsable, 
                                  Emptel, Empmai, Emprec, Emppla, Empcon 
                           FROM ".$wbasecliame."_000024 cli24
                           WHERE cli24.Empcod = '".$wingres."'";
            $respon = mysqli_query($conex,$sqlRespon) or die("ERROR EN QUERY MATRIX()<br>".mysqli_error());
            
            $Telres = '';
            $Maires = '';
            $Recres = '';
            $Plares = '';
            $Fecrec = date("Y-m-d");
            $Conres = '';

            
            if( $rowRespon = mysqli_fetch_assoc($respon) )
            {                          
                $Telres = $rowRespon['Emptel'];
                $Maires = $rowRespon['Empmai'];
                $Recres = $rowRespon['Emprec'];
                $Plares = "<a href=# onclick='ejecutar(".chr(34).$rowRespon['Emppla'].chr(34).")'><u><b>".$rowRespon['Emppla']."</b></u></a>";
                $Conres = $rowRespon['Empcon'];
                
                if ( $rowRespon['Emprec'] !== '' )
                {
                     $dias = ($rowRespon['Emprec']*1).' day';
                     $wfecproI= date("Y-m-d", strtotime($wfecproF."+1 day"));
                     $Fecrec  = date("Y-m-d", strtotime(date('Y-m-d') +$dias));
                } 

            }

            //Construir campo estados del proceso
            $camEstados = "<option>Pendiente</option>";
            $camEstados .= "<option>Cerrado</option>";
            $resultado['estadopro'] =$camEstados;

            /////////////////////////////////////////////
            //Consultar ordenes por historia e ingreso //
            /////////////////////////////////////////////
            $query ="  SELECT h27.Medico, h27.Fecha_data, h27.Hora_data, h27.Ordfec, h27.Ordhor,
                              h27.Ordhis, h27.Ording, h27.Ordtor, h27.Ordnro, h27.Ordobs, h27.Ordesp, 
                              h27.Ordest, h27.Ordusu, h27.Ordfir, h27.Seguridad, h28.id as id_encabezado, 
                              h28.Medico, h28.Fecha_data, h28.Hora_data, h28.Dettor,h28.Detnro,
                              h28.Detcod, h28.Detesi, h28.Detrdo, h28.Detfec, h28.Detjus, h28.Detest, 
                              h28.Detite, h28.Detusu, h28.Detfir, h28.Deture, h28.Seguridad, 
                              h28.id as id_detalle, h28.Detpri
                        FROM ".$wbasehce."_000027 h27, ".$wbasehce."_000028 h28, ".$wbasemovhos."_000045 m45
                       WHERE   h27.Ordtor = h28.Dettor
                           AND h27.Ordnro = h28.Detnro
                           AND h27.Ordhis = '".$whistoria."'
                           AND h27.Ording = '".$wingreso."'
                           AND h28.Detesi = m45.Eexcod
                           AND m45.Eexaut = 'on'
                           AND h27.Ordest = 'on'
                           AND h28.Detest = 'on'
                       ORDER BY h28.Detfec DESC";
              
            $ordenes = mysqli_query($conex,$query) or die(mysqli_errno()." - Error en el query  - ".mysqli_error());

            if( $ordenes && $ordenes->num_rows>0){

                $resultado['nroregistro'] = $ordenes->num_rows;

                while($row = mysqli_fetch_assoc($ordenes)){

                      ///////////////////////////////////////////////////////////////////////
                      //          Consultar si ya existe en la tabla de gestión            //
                      ///////////////////////////////////////////////////////////////////////
                      $sqlRespon = " SELECT Gestel, Gescor, Gesesa, Gesrec, Gesesp,
                                            Gesmot, Gesobs, Gesusu, Gesest, Gescoa
                                     FROM ".$wbasecliame."_000333 cli333
                                     WHERE cli333.Geshis = '".$whistoria."'
                                       AND cli333.Gesing = '".$wingreso."'
                                       AND cli333.Gesnro = '".$row['Detnro']."' 
                                       AND cli333.Gesite = '".$row['Detite']."'                                        
                                       AND cli333.Gestor = '".$row['Dettor']."' ";
                      
                      $resgest = mysqli_query($conex,$sqlRespon) or die("ERROR EN QUERY MATRIX()<br>".mysqli_error());

                      $Gesesa = '00';
                      $Gesmot = '';
                      $Gesesp = '09';
                      $Gesest = '';
                      $Gesobs = ''; 
                      $Gescoa = '';
                      $Telant = ''; 
                      $Maiant = ''; 
                      $Fecant = '';

                      if( $resgest && $resgest->num_rows > 0)
                      {
                          if( $rowges = mysqli_fetch_assoc($resgest) )
                          {  
                              
                              $Telres = $rowges['Gestel'];
                              $Maires = utf8_encode($rowges['Gescor']);
                              $Recres = $rowges['Gesrec'];
                              $Fecrec = $rowges['Gesrec'];
                              $Gesesa = $rowges['Gesesa'];
                              $Gesmot = $rowges['Gesmot'];
                              $Gesesp = $rowges['Gesesp'];
                              $Gesest = $rowges['Gesest'];
                              $Gesobs = $rowges['Gesobs'];
                              $Gescoa = $rowges['Gescoa'];
                              $Telant = $rowges['Gestel'];
                              $Maiant = utf8_encode($rowges['Gescor']);
                              $Fecant = $rowges['Gesrec'];
                          }
                      }

                      
                      //Diligenciar el menu de estados de autorizacion obteniendo las opciones de cliame_000335
                      $optionEstados = "";//<option value='00' name='off' codautorizado='off' asignarcita='off'>Pendiente</option>
                      $optionMotivo  = "";
                      $optionProceso = "";

                      foreach( $arEstados as $key => $value )
                      {
                          switch($value['Eauopc'])
                          {
                            case '1':
                              if($Gesesa !== '' && $value['Eaucod'] == $Gesesa)
                                 $optionEstados.= "<option value='".$value['Eaucod']."' codautorizado='".$value['Eauaut']."' asignarcita='".$value['Eauaci']."' noprogramado='".$value['Eaunop']."' SELECTED>".utf8_encode($value['Eaudes'])."</option>";
                              else    
                                 $optionEstados.= "<option value='".$value['Eaucod']."' codautorizado='".$value['Eauaut']."' asignarcita='".$value['Eauaci']."' noprogramado='".$value['Eaunop']."'>".utf8_encode($value['Eaudes'])."</option>";
                              break; 

                            case '2':
                              if($Gesmot !== '' && $value['Eaucod'] == $Gesmot)
                                 $optionMotivo.= "<option value='".$value['Eaucod']."' SELECTED>".utf8_encode($value['Eaudes'])."</option>";
                              else    
                                 $optionMotivo.= "<option value='".$value['Eaucod']."' >".utf8_encode($value['Eaudes'])."</option>";
                              break;

                            case '3':

                              if($Gesesp !== '' && $value['Eaucod'] == $Gesesp){
                                 $optionProceso.= "<option value='".$value['Eaucod']."' SELECTED>".utf8_encode($value['Eaudes'])."
                                 </option>";                                
                              }
                              else    
                                 $optionProceso.= "<option value='".$value['Eaucod']."' >".utf8_encode($value['Eaudes'])."</option>";
                              break;
                          }
                      }

                      $bitobs   = '';
                      $bitobsex = '';

                      ////////////////////////////////////////////////////////////
                      // Seleccionar bitacora de observaciones en cliame_000334 //
                      ////////////////////////////////////////////////////////////
                      $sqlbit = " SELECT cli334.Fecha_data, cli334.Hora_data, cli334.Bitusu, cli334.Bitobs, usuarios.Descripcion as nomusu
                                     FROM ".$wbasecliame."_000334 cli334
                                     INNER JOIN usuarios  on (usuarios.codigo = cli334.Bitusu )
                                     WHERE cli334.Bithis = '".$whistoria."'
                                       AND cli334.Biting = '".$wingreso."'
                                       AND cli334.Bitnro = '".$row['Detnro']."' 
                                       AND cli334.Bitite = '".$row['Detite']."'
                                       AND cli334.Bittor = '".$row['Dettor']."' ";
                      
                      $resbit = mysqli_query($conex,$sqlbit) or die("ERROR EN QUERY MATRIX()<br>".mysqli_error());

                      if( $resbit && $resbit->num_rows>0){

                          while($rowbit = mysqli_fetch_assoc($resbit)){
                                $bitobs   .= '<b>'.$rowbit['Fecha_data'].' - '.$rowbit['nomusu'].'</b><br>'.($rowbit['Bitobs']).'<b><hr></b>';
                                $bitobsex .= $rowbit['Fecha_data'].' - '.($rowbit['nomusu']).' '.($rowbit['Bitobs'])."\n"."--------------"."\n";
                          }
                      }

                      //Seleccionar nombre procedimiento ordenado
                      $sqlCups = "  SELECT * FROM 
                                  ( SELECT Codcups, Descripcion, Servicio, Cconom, Ccocip, Ccourl
                                        FROM ".$wbasehce."_000017,".$wbasemovhos."_000011
                                       WHERE Codigo = '".$row['Detcod']."'
                                         AND Servicio = Ccocod
                                         AND Nuevo = 'on'
                                       UNION
                                      SELECT Codcups, Descripcion, Servicio, Cconom, Ccocip, Ccourl
                                        FROM ".$wbasehce."_000047,".$wbasemovhos."_000011                           
                                       WHERE Codigo = '".$row['Detcod']."'
                                         AND Servicio = Ccocod) as t ";
                      
                      $resCups = mysqli_query($conex,$sqlCups) or die("ERROR EN QUERY MATRIX()<br>".mysqli_error());
                      
                      $uniCups    = '';
                      $servicio   = '';
                      $prefijocit = '';
                      
                      if( $rowCups = mysqli_fetch_assoc($resCups) ){
                          
                          $uniCups    = utf8_encode($rowCups['Cconom']);
                          $desCups    = utf8_encode($rowCups['Descripcion']);
                          $servicio   = utf8_encode($rowCups['Servicio']);
                          $prefijocit = utf8_encode($rowCups['Ccocip']);
                          $urlcitas   = "<a href=# onclick='ejecutar(".chr(34).$rowCups['Ccourl'].chr(34).")'><u><b>Asignar Cita</b></u></a>";
                      }    

                      if( ( strtotime( date('Y-m-d') ) - strtotime( $Fecrec ) )  > 0 )
                          $Retfec = 'S';
                      else
                          $Retfec = 'N';   

                      $resultado['html'][] = array("Ordfec" => $row["Ordfec"], 
                                                   "Ordhor" => $row["Ordhor"], 
                                                   "Orduni" => $uniCups, 
                                                   "Orddes" => utf8_encode($desCups), 
                                                   "Ordser" => utf8_encode($servicio), 
                                                   "Detcod" => $row["Detcod"], 
                                                   "Ordjus" => utf8_encode($row["Detjus"]),
                                                   "Telres" => utf8_encode($Telres),
                                                   "Maires" => utf8_encode($Maires),
                                                   "Recres" => utf8_encode($Recres),
                                                   "Gesesa" => utf8_encode($Gesesa),
                                                   "Gesest" => utf8_encode($Gesest),
                                                   "Gescoa" => utf8_encode($Gescoa),
                                                   "Gesesp" => utf8_encode($Gesesp),
                                                   "Gesmot" => utf8_encode($Gesmot),
                                                   "Telant" => utf8_encode($Telant), 
                                                   "Maiant" => utf8_encode($Maiant), 
                                                   "Fecant" => utf8_encode($Fecant),
                                                   "Plares" => utf8_encode($Plares),
                                                   "Retfec" => utf8_encode($Retfec),
                                                   "Telobs" => 'Telefono',    
                                                   "Maiobs" => 'Email',
                                                   "Fecobs" => 'Fecha recordacion',
                                                   "Gesobsobs" => 'Observacion',   
                                                   "Estautobs" => 'Estado autorizacion',
                                                   "Gesmotobs" => 'Motivo noprogramado',   
                                                   "Gesespobs" => 'Estado gestion', 
                                                   "Gescoaobs" => 'Codigo autorizacion',   
                                                   "Ordtor" => $row["Ordtor"],
                                                   "Ordnro" => $row["Ordnro"],
                                                   "Dettor" => $row["Dettor"],
                                                   "Detnro" => $row["Detnro"],
                                                   "Detfec" => $row["Detfec"],
                                                   "Detite" => $row["Detite"],
                                                   "Prefijo"=> utf8_encode($prefijocit),
                                                   "Urlcit" => utf8_encode($urlcitas),
                                                   "Conres" => utf8_encode($Conres),
                                                   "Fecrec" => $Fecrec, 
                                                   "bitobex"=> utf8_encode($bitobsex),     
                                                   "bitobs" => utf8_encode($bitobs),                                              
                                                   "Estaut" => $optionEstados,
                                                   "Estmot" => utf8_encode($optionMotivo),
                                                   "Estesp" => utf8_encode($optionProceso) );
                }                               
                
                mysqli_free_result($ordenes);
            }

            return $resultado;
    }


    //Consultar plan según código de la Entidad
    function consultarPlan($conex, $wbasecliame, $wbasedato, $wemp_pmla,  $wcodenti)
    {

            // -->  Obtener los planes relacionados a la entidad responsable
            $optionsSelect = "";
            $sqlPlanes = "SELECT Placod, Plades
                            FROM ".$wbasecliame."_000153
                           WHERE (Plaemp = '".$wcodenti."' OR Plaemp = '*')
                             AND Plaest = 'on'
            ";
            $resPlanes = mysql_query($sqlPlanes, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPlanes):</b><br>".mysql_error());
            while($rowPlanes = mysql_fetch_array($resPlanes))
            {
                $optionsSelect.= "<option ".(($planActualPac == $rowPlanes['Placod']) ? "selected=selected" : "")." value='".$rowPlanes['Placod']."'>".$rowPlanes['Plades']."</option>";
            }

            return $optionsSelect;
    }


    //Consultar los estados del campo lista de autorizacion
    function consultarAutorizacion($conex, $wbasecliame, $wemp_pmla)
    {

            $optionsSelect = "";
            $sqlPlanes = "SELECT Eaucod,Eaudes
                            FROM ".$wbasecliame."_000335
                           WHERE Eauopc = '2'
                             AND Eauest = 'on'
            ";
            $res = mysql_query($sqlPlanes, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlPlanes):</b><br>".mysql_error());
            while($row = mysql_fetch_array($res))
            {
                $optionsSelect.= "<option value='".$row['Eaucod']."'>".$row['Eaudes']."</option>";
            }

            return $optionsSelect;
    }


  // *****************************************        FIN PHP        ********************************************
  ?>
  <!DOCTYPE html>
  <html lang="es-ES">
  <head>
    <title>Gestor Autorizaciones Post-Egreso</title>
    <meta charset="utf-8">
    <meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />    
    <script src="../../../include/root/jquery.min.js"></script> 
    <link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>    
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>    
    <script src="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
    <link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet"/>
    <link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.theme.min.css" rel="stylesheet"/>
    <link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.structure.min.css" rel="stylesheet"/>
    <script src='../../../include/root/bootstrap4/js/bootstrap.min.js'></script>
    <link rel='stylesheet' href='../../../include/root/bootstrap4/css/bootstrap.min.css'>
    <link rel='stylesheet' href='../../../include/gentelella/vendors/font-awesome/css/font-awesome.min.css'>
    <script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
    <script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
    <script type="text/javascript">
      
       $(document).ready(function(){

          iniciarAtencion();

       });

       //Crea objeto principal con la información anterior en caso de que el registro exista,
       //y las nuevas modificaciones ingresadas por el usuario
       var  objgestion = new Object();   



       //Traer información de los maestros y grabar en la tabla cliame_000333 con el campo de atencion (Gesate)=off
       //ya que la atención no ha sido finalizada
       function iniciarAtencion()
       {     

          consultarOrdenesJS();

          //Recorrer todo los campos con atributo 'nomgrabar' para diligenciar el objeto 'objgestion'
          $("select,textarea,input[nomgrabar^=grutexto]").each(function(){
            
              if ( $(this).attr('name') != undefined ){
                   var wkey         = $(this).attr('key');
                   var wnuatributo  = $(this).attr('stratri')+'nue';
                   objgestion[wkey][wnuatributo] = $(this).val(); 
                   //console.log(wkey);                  
              }   
          });

          $.ajax({
                  url: "gestionar_postegreso.php",
                  type: "POST",
                  data:{
                        wemp_pmla      : $("#wemp_pmla").val(),
                        accion         : 'grabarGestion',
                        whistoria      : $("#whistoria").val(),
                        wingreso       : $("#wingreso").val(),
                        watencion      : 'off',
                        consultaAjax   : true,
                        objgestion     : objgestion
                  },
                  dataType: "json",
                  async: false,
                  success:function(data_json) {

                        if (data_json !== '' ){
                            $('#modalMensaje').html('<center>El paciente NO PUEDE ser egresado. Tiene '+data_json+' pendientes</center');
                            $('#tituloMensaje').html('<h5 class="modal-title"><font color="red"><center>MENSAJE...</center></font></h5>');
                            $('#modalAlerta').show();
                        }
                      
                  }
          });

       }


  
       function consultarOrdenesJS()
       {

          var whistoria = $("#whistoria").val();
          var wingreso  = $("#wingreso").val();

          $.ajax({
              url: "gestionar_postegreso.php",
              type: "POST",
              data:{
                    wemp_pmla      : $("#wemp_pmla").val(),
                    accion         : 'consultarOrdenes',
                    consultaAjax   : true,
                    whistoria      : whistoria,
                    wingreso       : wingreso,
                    wingres        : $("#wingres").val(),
              },
              dataType: "json",
              async: false,
              success:function(data_json) {

                      if (data_json.nroregistro > 0){

                          var fila      = "fila2";
                          var stringTr  = '';
                          var stringBody= '';
                          var estadopro = data_json.estadopro;
                          var codcierre = data_json.codcierre;

                          $("#wcodcierre").val(codcierre);
                          $("#tbldetalle").show();
                          $("#divmensaje").hide();

                          jQuery.each(data_json.html, function(){  

                              var indice =  this.Detnro+'_'+this.Detcod+'_'+this.Detite; 

                              //Adicionar la informacion al objeto principal que posteriormente generará grabado y bitacora
                              objgestion[indice] = new Object();
                              objgestion[indice].Procedi   = this.Detcod;
                              objgestion[indice].Orden     = this.Detnro;
                              objgestion[indice].Nroite    = this.Detite;
                              objgestion[indice].Tipord    = this.Dettor;
                              objgestion[indice].Telresant = this.Telant;
                              objgestion[indice].Telresobs = this.Telobs;
                              objgestion[indice].Mairesant = this.Maiant;
                              objgestion[indice].Mairesobs = this.Maiobs;
                              objgestion[indice].Fecrecant = this.Fecant;
                              objgestion[indice].Fecrecobs = this.Fecobs;
                              objgestion[indice].Estautant = this.Gesesa;
                              objgestion[indice].Estautobs = this.Estautobs;                              
                              objgestion[indice].Estespant = this.Gesesp;
                              objgestion[indice].Estespobs = this.Gesespobs;
                              objgestion[indice].Gescoaant = this.Gescoa;
                              objgestion[indice].Gescoaobs = this.Gescoaobs;
                              objgestion[indice].Gesobsobs = this.Gesobsobs;
                              objgestion[indice].Gesmotant = this.Gesmot;
                              objgestion[indice].Gesmotobs = this.Gesmotobs;


                              // Asignar justificacion de la orden
                              if(this.Ordjus != '')
                                 varjustificacion = "<br><hr>Justificaci&oacute;n: "+this.Ordjus;                              
                              else
                                 varjustificacion = '';
                             
                              // En caso de que el campo 'ccocip' de la tabla 'movhos_000011' esté diligenciado con el prefijo, 
                              // deberá mostrar un link al programa de citas                         
                              if (this.Prefijo != '')
                                  var varasignar = '<br><div id="dvAsignarcita'+this.Detcod+'" style="display:none">'+this.Urlcit+'</div>';
                              else
                                  var varasignar = '<br><div id="dvAsignarcita'+this.Detcod+'" style="display:none"></div>';

 
                              // Mostrar campo 'codigo de autorizacion solo en caso en que lo tenga diligenciado'
                              if (this.Gescoa != '')
                                  var codautoriz = "";
                              else  
                                  var codautoriz = "style=display:none;";

                              // Cambiar color para las filas con diligenciamiento cerrado
                              if (this.Gesesp == codcierre){
                                  var colcierre = 'style="background-color: lightyellow "';
                                  var colenabled = 'disabled';
                              }    
                              else{
                                  var colcierre = 'style="background-color: #fae5d3 "';
                                  var colenabled = '';
                              }    

                              // Cambiar color para las filas con retraso
                              if (this.Retfec == 'S' && this.Gesesp != codcierre){
                                  var colcierre = 'style="background-color: #d1eacf "';
                                  var atrblink  = 'class=blink';
                              }
                              else
								                  var atrblink  = '';

                              if (this.Plares != '')
                                  var plataforma = '<div>Plataforma <br>'+this.Plares+'</div></td>';
                              else   
                                  var plataforma = '';

                              

                              stringTr =  stringTr + '<tr id="'+this.Ordfec+'" class="'+fila+'">'
                                         + '<td align="center" width=4%;>'+this.Ordfec+'  '+this.Ordhor+'</td>'
                                         + '<td align="center" width=4%;>'+this.Detfec+'</td>'
                                         + '<td align="center" width=9%; title="Numero de orden: '+this.Ordnro+'">'+this.Orduni+'</td>'
                                         + '<td align="center" width=17%; title="Numero de orden: '+this.Ordnro+'"><b '+atrblink+'>'+this.Orddes+'</b>'+varjustificacion+'</td>'
                                         + '<td align="center" width=15%; title="Contacto: '+this.Conres+'" ><div>Telefono<input type="text" id="txtelefonoaut" name="txtelefonoaut" class="form-control" nomgrabar="grutexto" key="'+indice+'" stratri="Telres" value="'+this.Telres+'" '+colenabled+'></div>'
                                         + '<div>Correo<input type="text" id="txtcorreoaut"  name="txtcorreoaut" class="form-control" nomgrabar="grutexto" key="'+indice+'" stratri="Maires" value="'+this.Maires+'" '+colenabled+'></div>'
                                         + plataforma
                                         + '<td align="center" width=12% ><select id="selectEstadoautoriz'+indice+'" name="selectEstadoautoriz" class="form-control" nomgrabar="grutexto" nomvalidar="on" key="'+indice+'" stratri="Estaut" procedi="'+indice+'" motivo="'+this.Gesmot+'" onchange="mostrarModal(this);" '+colenabled+'>'+this.Estaut+'</select>'
                                         + varasignar 
                                         + '<br><div id="dvAutorizacion'+indice+'" '+codautoriz+'> C\u00f3digo de autorizaci\u00f3n<br><input type="text" id="codautorizado'+indice+'" name="codautorizado" class="form-control" nomgrabar="grutexto" '+colenabled+' key="'+indice+'" stratri="Gescoa" value="'+this.Gescoa+'" > </div> </td>'
                                         + '<td align="center" width=5% '+colcierre+'><input type="text" id="txtfecharec" name="txtfecharec" class="form-control" nomgrabar="grutexto" key="'+indice+'" stratri="Fecrec" value="'+this.Fecrec+'" disabled></td>'
                                         + '<td align="center" width=5% '+colcierre+'>'
                                         + '<select id="selectEstado" name="selectEstado" class="form-control" nomgrabar="grutexto" key="'+indice+'" stratri="Estesp" onchange="asignarEstado(this.value);" '+colenabled+'>'+this.Estesp+'</select></td>'
                                         + '<td align="center" width=10%>'
                                         + '<textarea id="txobservacion" name="txobservacion" class="form-control" nomgrabar="grutexto" key="'+indice+'" stratri="Gesobs" cols=50 rows=5></textarea></td>'
                                         + '<td align="left" width=12%>'
                                         + '<textarea id="txbitacora" name="txbitacora" class="form-control" nomgrabar="desactivar" key="'+indice+'"  cols=50 rows=5 readonly>'+this.bitobex+'</textarea></td>'
                                         + '<td align="center" width=25%>'
                                         + '<div class="encabezadoTabla" style="height:32px;width:62px;border-radius: 5px;"><input type="checkbox" id="imprimirCheckuni" name="ordenes[]" value="'+this.Dettor+'|'+this.Detnro+'|'+this.Detcod+'_'+this.Detite+'|'+this.Detfec+'|'+whistoria+'|'+wingreso+'" style="vertical-align: middle;margin-left:2px;margin-top:5px;">&nbsp&nbsp'
                                         + '<i class="fa fa-print fa-lg fa-fw" style="font-size:25px;color:white;vertical-align: middle;margin-top:5px;" valimp="'+this.Dettor+'|'+this.Detnro+'|'+this.Detcod+'_'+this.Detite+'|'+this.Detfec+'|'+whistoria+'|'+wingreso+'" onclick="imprimirOrdenes('+whistoria+','+wingreso+',3,this)"></i></div>'
                                         + '<input type="hidden" id="txmotivo'+indice+'" name="txtmotivo" nomgrabar="grutexto"  key="'+indice+'" stratri="Gesmot" value='+this.Gesmot+' ></td>'
                                         + '</tr>';  

                              stringBody =  stringBody + '<tr class="'+fila+'" height="40px">'
                                         + '<td align="center" width=30% style="border: 1px solid white;">'+this.Orddes+'</td>'
                                         + '<td align="left" width=70% style="border: 1px solid white;">'+this.bitobs+'</td>'
                                         + '</tr>'; 

                              fila    = fila == "fila1" ? "fila2" : "fila1";

                          });

                          
                          $("#tbldetalle > tbody").html("");

                          $('#tbldetalle > tbody:last').append(stringTr);

                          $('#tblBitacora > tbody').html("");

                          $('#tblBitacora > tbody:last').append(stringBody);

                          $('#wnroregistro').val(data_json.nroregistro);

                          //Adicionar el objeto datepicker a todos los campos fecha de la columna 'Recordación'
              						$("input[id^=txtfecharec]").each(function(){
              							  $(this).datepicker({
	                               dateFormat: "yy-mm-dd"
	                            }).datepicker();
                          });

                      }else{

                          $("#tbldetalle").hide();
                          $("#divmensaje").show();
                      }
              }

          });
      }

      function asignarEstado(valor)
      {
          if (valor == $("#wcodcierre").val())
              $("#wconcierre").val("1");
      }

      function ejecutar(path)
      {
          window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
      } 


      function extractContent(s) 
      {
          var span = document.createElement('span');
          span.innerHTML = s;
          return span.textContent || span.innerText;
      }


      function mostrarModal(obj)
      {          
          var wproce   = $(obj).attr('key');
          var codproce = $("#selectEstadoautoriz"+wproce).attr('procedi');
          var valproce = $("#selectEstadoautoriz"+wproce).attr('motivo');
          var noprogra = $("#selectEstadoautoriz"+wproce).find(':selected').attr('noprogramado');
          var autoriz  = $("#selectEstadoautoriz"+wproce).find(':selected').attr('codautorizado');
          var asignar  = $("#selectEstadoautoriz"+wproce).find(':selected').attr('asignarcita');
          $("#wprocemotivo").val(wproce);

          if  (noprogra == 'on'){
              $("#selectMotivoPaciente").find("option[value='"+valproce+"']").attr("selected", true);
              $('#modalAutorizacion').show();
          }            

          if  (autoriz == 'on'){  
              $('#dvAutorizacion'+wproce).show();
              $('#codautorizado'+wproce).focus();
          }
          else{
              $('#dvAutorizacion'+wproce).hide();
          }

          if  (asignar == 'on')
              $('#dvAsignarcita'+wproce).show();
          else
              $('#dvAsignarcita'+wproce).hide();

      }


      function mostrarBitacora()
      {          
          $('#modalBitacora').show();
      }


      function grabarMotivo(obj)
      {         
         $wproce = $("#wprocemotivo").val();        
         $("#txmotivo"+$wproce).val($("#selectMotivoPaciente").find(':selected').val());
         $('#modalAutorizacion').hide();
      }


      function grabarOrdenes()
      {           

        var whistoria = $("#whistoria").val();
        var wingreso  = $("#wingreso").val();
        var validar   = 0;

        //Recorrer todo los campos con atributo 'nomgrabar' para diligenciar el objeto 'objgestion' y realizar un solo 
        //grabado para todos los registros
        $("select,textarea,input[nomgrabar^=grutexto]").each(function(){
            
            if ( $(this).attr('name') != undefined ){
                 var wkey         = $(this).attr('key');
                 var watributo    = $(this).attr('stratri');
                 var wnuatributo  = $(this).attr('stratri')+'nue';
                 objgestion[wkey][wnuatributo] = $(this).val();
                 
                 if ( $(this).attr('nomvalidar') == 'on' ){
                     
                     var wsolauto = $("#selectEstadoautoriz"+wkey).find(':selected').attr('codautorizado');
                     if ( wsolauto == 'on' && $("#codautorizado"+wkey).val() == ''){
                          $('#modalMensaje').html('<center>Falta diligenciar C\u00F3digo de autorizaci\u00F3n</center');
                          $('#tituloMensaje').html('<h5 class="modal-title"><font color="red"><center>ALERTA...</center></font></h5>');
                          $('#modalAlerta').show();
                          validar = 1;
                     }     
                     
                 }
            }   
        });


        if (validar == 0){
            $.ajax({
                  url: "gestionar_postegreso.php",
                  type: "POST",
                  data:{
                        wemp_pmla      : $("#wemp_pmla").val(),
                        accion         : 'grabarGestion',
                        whistoria      : whistoria,
                        wingreso       : wingreso,
                        consultaAjax   : true,
                        watencion      : 'on',
                        objgestion     : objgestion
                  },
                  dataType: "json",
                  async: false,
                  success:function(data_json) {
 
                        if (data_json == '' ){
                            $('#modalMensaje').html('<center>La gesti\u00F3n ha sido grabada</center');
                            $('#tituloMensaje').html('<h5 class="modal-title"><font color="red"><center>MENSAJE...</center></font></h5>');
                            $('#modalAlerta').show();
                        }
                        else
                        {
                            $('#modalMensaje').html('<center>El paciente NO PUEDE ser egresado. Tiene '+data_json+' pendientes</center');
                            $('#tituloMensaje').html('<h5 class="modal-title"><font color="red"><center>MENSAJE...</center></font></h5>');
                            $('#modalAlerta').show();
                        }
                        
                        consultarOrdenesJS();
                  }
            });
        }         
      }


      function marcarImprimir(obj)
      {
          if (obj.checked==true){
              $("input[id='imprimirCheckuni']").prop("checked", true);
          }
          else{
              $("input[id='imprimirCheckuni']").prop("checked", false);
          }
      }

 
      function imprimirOrdenes(whistoria,wingreso,opc,obj)
      {
          var checkboxOrdenes = "";
          if (opc==2){
              // Consultar las ordenes seleccionadas para imprimir
              $('input[name="ordenes[]"]:checked').each(function() {
                  var wtipo = $(this).val().split("|");
                  checkboxOrdenes += wtipo[0] + ",";
              });
          }
          else{
              var wvalpar = $(obj).attr('valimp');
              var wtipo   = wvalpar.split("|");
              checkboxOrdenes = wtipo[0] + ",";
          }
          
          if (checkboxOrdenes== "" && opc != 3){
              $('#modalMensaje').html('Debe seleccionar impresi\u00F3n de ordenes');
              $('#tituloMensaje').html('<h5 class="modal-title"><font color="red"><center>ALERTA...</center></font></h5>');
              $('#modalAlerta').show();
          }
          else
          {
              //eliminamos la última coma.
              checkboxOrdenes = checkboxOrdenes.substring(0, checkboxOrdenes.length-1);

              var path = "/matrix/hce/procesos/ordenes_imp.php?wemp_pmla="+ $("#wemp_pmla").val() +"&whistoria=" + whistoria + "&wingreso="+wingreso+"&tipoimp=imp&alt=off&pacEps=off&wtodos_ordenes=on&orden=asc&origen=&arrOrden="+checkboxOrdenes+"&desdeImpOrden=on";

              window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=1,scrollbars=1,titlebar=0');
          }
      }

    </script>
    <style type="text/css">
        #divencabezado
        {
            border-radius: 10px;
            border-collapse: collapse;
        }
        #tblencabezado
        {
            border: 1px solid #ccc;
            border-radius: 10px;
            width: 80%;
            font-size: 13px;
        }
        #tbldetalle
        {
            border: 2px solid white;
            width: 97%;
            font-size: 13px;
        }
        #tbldetalle tr td 
        {
            border-width: 1px;
            border-style:solid;
            border-color: white;
        }
        #tblbitacora
        {
            border-radius: 10px;
            width: 90%;
            height:10px;
            font-size: 13px;
            overflow-y: scroll;
        }
        .form-control
        {
            height:30px;
            font-size: 12px;
        }
        #selectEstadoautoriz
        {
            width:250px;
            font-size: 12px;
        }
        #txtfecharec
        {
            width: 100px;
        }
        body 
        {
            width: 100%;
        }
        input[type="checkbox"] {
            cursor: pointer;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            outline: 0;
            background: lightgray;
            height: 16px;
            width: 16px;
            border: 2px solid white;
            border-radius: 5px;
        }

        input[type="checkbox"]:checked {
            background: #67a8ee;
        }

        input[type="checkbox"]:hover {
            filter: brightness(90%);
        }

        input[type="checkbox"]:disabled {
            background: #e6e6e6;
            opacity: 0.6;
            pointer-events: none;
        }

        input[type="checkbox"]:after {
            content: '';
            position: relative;
            left: 40%;
            top: 20%;
            width: 15%;
            height: 40%;
            border: solid #fff;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
            display: none;
        }

        input[type="checkbox"]:checked:after {
            display: block;
        }

        input[type="checkbox"]:disabled:after {
            border-color: #7b7b7b;
        }
        #modalAutorizacion 
        {
            display: none; 
            position: fixed; 
            z-index: 1; 
            left: 35%;
            top: 25%;
            width: 30%; 
            height: 500px; 
            overflow: auto;             
            border-radius: 2%;

        }
        #modalBitacora
        {
            position: fixed; 
            border-radius: 10px;
            z-index: 1; 
            left: 2%;
            top: 25%;
            width: 95%; 
            height: 500px; 
            overflow: auto; 
        }
        .modal-header 
        {
          height: 50px; 
          font-size: 10px;
        }
        #modalGestion
        {
            z-index: 1; 
            left: 20%;
            top: 25%;
            width: 60%; 
            height: 500px; 
            overflow: auto;    
        }
        .tooltip 
        {
            background-color:#7094db;
            border-radius: 10px;
        }
        textarea 
        {
            overflow-y: scroll;
            height: 100px;
            resize: none; /* Remove this if you want the user to resize the textarea */
        }
        #modalAlerta 
        {   
            position: fixed; 
            top:30%; 
            display: none; 
            z-index: 1; 
            left: 0;
            width: 100%; 
            height: 100%; 
            overflow: auto; 
            background-color: rgb(0,0,0); 
            background-color: rgba(0,0,0,0.4); 
        }    
        #tblconvencion
        {
            float:right;
            width: 25%;
            margin-right:25px
        }
        .fondoPendiente
        {
            height:35px;
            width:200px;
            border-radius: 7px;
            border: 1px solid white;
            background-color: #fae5d3;
        }
        .fondoCerrada
        {
            height:35px;
            width:200px;
            border-radius: 7px;
            border: 1px solid white;
            background-color: lightyellow;
        }
        .fondoAtraso
        {
            height:35px;
            width:200px;
            border-radius: 7px;
            border: 1px solid white;
            background-color: #d1eacf;
        }
 /*   		@keyframes blink {
    		  50% {
    		    opacity: 0.0;
    		  }
    		}
    		@-webkit-keyframes blink {
    		  50% {
    		    opacity: 0.0;
    		  }
    		}*/
    		.blink {
    		  animation: blink 1s step-start 0s infinite;
    		  -webkit-animation: blink 1s step-start 0s infinite;
    		}

     </style>
  </head>
  <body width=100%>
      <input type='hidden' name='wemp_pmla'   id='wemp_pmla'   value='<?=$wemp_pmla?>'>
      <input type='hidden' name='whistoria'   id='whistoria'   value='<?=$whis?>'>
      <input type='hidden' name='wingreso'    id='wingreso'    value='<?=$wing?>'>
      <input type='hidden' name='wingres'     id='wingres'     value='<?=$wingres?>'>
      <input type='hidden' name='wbasemovhos' id='wbasemovhos' value='<?=$wbasemovhos?>'>
      <?php
          $wtitulo   = "GESTOR AUTORIZACIONES POST-EGRESO";
          encabezado("<div class='titulopagina2'>{$wtitulo}</div>", $wactualiza, "clinica");
          $listaplan = consultarPlan($conex, $wbasecliame, $wbasehce, $wemp_pmla, $wcodenti);        
          $listaAutorizacion = consultarAutorizacion($conex, $wbasecliame, $wemp_pmla);  
          $path_hce = "../../hce/procesos/solimp.php?wemp_pmla=".$wemp_pmla."&whis=".$whis."&wing=".$wing."&wservicio=*";      
      ?>
      <div id='divencabezado' align='center'>
                <table id='tblencabezado' >
                    <tr class='fila1'>
                        <th ><center>Documento <br> de Identidad</center></th>
                        <th ><center>Historia + Ingreso</center></th>
                        <th ><center>Nombres y apellidos</center></th>
                        <th ><center>Servicio origen</center></th>
                        <th ><center>M&eacute;dico tratante</center></th>
                        <th ><center>Entidad responsable</center></th>
                        <th ><center>Plan</th>
                    </tr>
                    <tr style='background-color: white;border: 1px solid lightgray;'>
                        <td align='center'><font size='2'><?=$wtid."-".$wdpa?></font></td>
                        <td align='center' style='cursor: pointer;' 
                        onclick='ejecutar("../../hce/procesos/solimp.php?wemp_pmla=<?=$wemp_pmla?>&whis=<?=$whis?>&wing=<?=$wing?>&wservicio=*")'><font size='2'><?=$whis."-".$wing?></font></td>
                        <td align='center'><font size='2'><?=$wpac?></font></td>
                        <td align='center'><font size='2'><?=$wserorigen?></font></td>
                        <td align='center'><font size='2'><?=$wmed?></font></td>
                        <td align='center'><font size='2'><?=$wentidad?></font></td>
                        <td align='center'>
                          <select id='selectPlanPaciente' class='form-control' style='font-size:12px'><?=$listaplan?></select>
                        </td>    
                    </tr>
                </table>
      </div>
      <br>
      <table align=center>
      <tr><td ><input type=button class='btn btn-secondary btn-sm btn-rounded' value='Grabar' onclick='grabarOrdenes()'>&nbsp&nbsp</td>
      </td>&nbsp<td><input type=button class='btn btn-secondary btn-sm btn-rounded' value='Retornar' onclick='cerrarVentana()'></td></tr>
      </table><br>
      <table id='tblconvencion'>
        <tr >  
        <td class='fondoPendiente' align='center'>Orden Pendiente</td><td class='fondoAtraso' align='center'>Retraso</td><td class='fondoCerrada' align='center'>Orden Cerrada</td>
        </tr>
      </table>
      <div id='divdetalle' align='center' >
                <table id='tbldetalle'>
                  <thead>
                    <tr class='encabezadoTabla'>
                      <th colspan=4 style="border: 1px solid white;"><center>Ordenamiento</center></th>       
                      <th colspan=7 style="border: 1px solid white;"><center>Gesti&oacute;n</center></th>       
                    </tr>
                    <tr class='encabezadoTabla'>
                        <th widht=40px><center>Fecha Hora Admisi&oacute;n</center></th>
                        <th style="border: 1px solid white;"><center>Fecha a realizar</center></th>
                        <th style="border: 1px solid white;"><center>Unidad que realiza</center></th>
                        <th style="border: 1px solid white;"><center>Procedimiento</center></th>                        
                        <th style="border: 1px solid white;"><center>Gesti&oacute;n de autorizaci&oacute;n </center></th>
                        <th style="border: 1px solid white;"><center>Estado de la autorizaci&oacute;n</center></th>
                        <th style="border: 1px solid white;"><center>Recordaci&oacute;n</center></th>
                        <th style="border: 1px solid white;"><center>Estado del proceso</th>
                        <th style="border: 1px solid white;"><center>Observaciones</th>
                        <th style="border: 1px solid white;"><center>Bitacora <i class="fa fa-search fa-lg fa-fw" onclick="mostrarBitacora();" style="font-size:20px;color:white"></i> </th>
                        <th style="width:60px;"><center>
                              <input type="checkbox"  id="imprimirCheck" name="imprimirCheck" class="clscheck" value='<?=$whis."-".$wing?>' onclick="marcarImprimir(this);">
                              <label >Todos</label><i class="fa fa-print fa-lg fa-fw" style="font-size:25px;color:white" onclick='imprimirOrdenes(<?=$whis?>,<?=$wing?>,2,this)'></i></center>
                        </th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
      </div>
      <div id='divmensaje' style='display:none;'>
        <br><center><b>NO HAY INFORMACION</b></center><br>
      </div> 

    <!-- Modal para seleccionar estado de autorización -->
    <div class='modal' id='modalAutorizacion' name='modalAutorizacion'  align='center'>
        <div class="modal-content">
            <div class="encabezadoTabla">
                  <p class="modal-title text-center" id="alertModalLabel" style='font-size:15px;vertical-align: middle;height: 30px'>Diligencie el motivo</p>
            </div>
            <div class="modal-body" >
              <div class="form-group">
                  <br><label class="control-label">Favor seleccionar</label><br>
                  <select id='selectMotivoPaciente' class='form-control' style='font-size:12px'><?=$listaAutorizacion?></select>
                  <br>
              </div>
            </div>
            <br><br>
            <div class="modal-footer">
               <button type="button" id="btngrabar" class="btn btn-primary" style="background-color:#2a5db0;" onclick="grabarMotivo(this)">Grabar</button>
               <button type="button" id="btncerrar" class="btn btn-secondary" data-dismiss="modal" onclick="$('#modalAutorizacion').hide();">Cerrar</button>
            </div>
        </div>
    </div>  

    <!-- Modal para seleccionar Gestión de autorización -->
    <div class='modal' id='modalGestion' name='modalGestion' align='center'>
        <div class="modal-content">
            <div class="modal-header encabezadoTabla" style="max-height: 80vh;">
                  <h4 class="modal-title" id="alertModalLabel">Diligencie la siguiente informacion</h4>
            </div>
            <div class="modal-body" >
              <div class="form-group">
                  <table>
                  <td align="center" width=15%;><div>Telefono<input type="text" id="txtelefonoautmod" class="form-control"></div>
                  <div>Correo<input type="text" id="txtcorreoautmod" class="form-control"></div>
                  <div>Plataforma<input type="text" id="txtplataformamod" class="form-control"></div>
                  </td>
                  </table>
              </div>
            </div>
            <br><br>
            <div class="modal-footer">
               <button type="button" id="btncerrar" class="btn btn-secondary" data-dismiss="modal" onclick="cerrarModalGestion;">Cerrar</button>
            </div>
        </div>
    </div>      

    <!-- Modal informativa -->
    <div class='modal' id='modalAlerta' name='modalAlerta' role='dialog'>
      <div class='modal-dialog' role='document'>
        <div class='modal-content'>
          <div class='modal-header fila1' id='tituloMensaje'>
            <h5 class='modal-title'><font color='red'><center>INFORMACION...</center></font></h5>
          </div>
          <div class='modal-body' id='modalMensaje'>  
            <center>          
            La gesti&oacute;n ha sido grabada<br><br>
            </center>
          </div>
          <div class='modal-footer'>
            <button type='button' id='btncerrar' class='btn btn-secondary' data-dismiss='modal' onclick=" $('#modalAlerta').hide();">Cerrar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal para mostrar la Bitacora de manera maximizada -->
    <div class='modal' align='center' id="modalBitacora" style="">
      <div class="modal-content" style="background-color: white;">     
        <label class='encabezadoTabla' height="60px"> Historial </label><br><br>
        <table width=100% id="tblBitacora" > 
          <thead id="encBitacora"><tr><th>&nbsp</th></tr>
            <tr class="encabezadoTabla" height="40px">
              <th style="border: 1px solid white;">
                <center>Ordenamiento</center>
              </th>
              <th style="border: 1px solid white;">
                <center>Bitacora</center>
              </th>
            </tr>
          </thead>
          <tbody id="detBitacora">

          </tbody>
        </table>
        <div class="modal-footer" style="background-color: white
;">
           <button type="button" id="btncerrar" class="btn btn-secondary" data-dismiss="modal" onclick="$('#modalBitacora').hide();">Cerrar</button>
        </div>
      </div>
    </div>    
      <input type="hidden" id="wprocemotivo" name="wprocemotivo" value="">
      <input type="hidden" id="wnroregistro" name="wnroregistro" value="">
      <input type="hidden" id="wcodcierre"   name="wcodcierre"   value="">
      <input type="hidden" id="wconcierre"   name="wconcierre"   value="0">
      <input type="hidden" id="wtipoidenti"  name="wtipoidenti"  value="<?=$wtid?>">
      <input type="hidden" id="widentifica"  name="widentifica"  value="<?=$wdpa?>">
    </body>   
    </html>