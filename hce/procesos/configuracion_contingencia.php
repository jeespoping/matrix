<?php
include_once("conex.php");
 /*********************************************************************************************************************************
 *
 * Programa           : configuracion_contingencia.php
 * Fecha de Creación  : 2017-12-14
 * Autor              : Arleyda Insignares Ceballos
 * Descripcion        : Programa para configurar los formularios, campos y reportes que deberá mostrar el proceso de contingencia.                   
 *                                                                
 **********************************************************************************************************************************/
 
 $wactualiza = "2017-12-14";
 $consultaAjax='';

 /*if(!isset($_SESSION['user'])){
   echo "<center></br></br><table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
    <tr><td>Error2, inicie nuevamente</td></tr>
    </table></center>";
   return;
 }*/

 header('Content-type: text/html;charset=ISO-8859-1');
  //***********************************   Inicio  ***********************************************************

  include_once("root/comun.php");  

  $conex  = obtenerConexionBD("matrix");
  
  $wbasedato       = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
  $wbasemovhos     = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
  $wfecha          = date("Y-m-d");
  $whora           = (string)date("H:i:s");
  $pos             = strpos($user,"-");
  $wusuario        = substr($user,$pos+1,strlen($user));

  // ***************************************    FUNCIONES AJAX  Y PHP  **********************************************


        // Consultar si el orden ya existe en la tabla seleccionada
        if (isset($_POST["accion"]) && $_POST["accion"] == "VerificarOrden"){

            $num = 0;

            switch ($wopcion) {

              case 1:
                    $q  = " SELECT concog,Condes,Conord
                            From ".$wbasemovhos."_000252
                            Where Conord = ".$worden." And Conest != 'off' " ;

                    break;
              case 2:
                    $q  = " SELECT Fordes,Forord
                            From ".$wbasemovhos."_000253
                            Where Forcog =".$wgrupo." 
                              And Forord =".$worden."
                              And Forest != 'off' " ; 

                    break;
              case 3:
                    $q  = " SELECT Repdes,Repord
                            From ".$wbasemovhos."_000254
                            Where Repcog =".$wgrupo." 
                              And Repord =".$worden." 
                              And Repest != 'off' " ;

                    break;
              
            }
           
    
            $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

            $num = mysql_num_rows($res);

            echo $num;

            return;
        }


        
        // Consultar consecutivo (orden del formulario)
        if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultaOrden"){

            $num = 0;

            switch ($wopcion) {

                    case 1:
                          $q  = " SELECT MAX(Conord) as maxorden
                                  From ".$wbasemovhos."_000252
                                  Where Conest != 'off' " ;

                          break;
                    case 2:
                          $q  = " SELECT MAX(Forord) as maxorden
                                  From ".$wbasemovhos."_000253
                                  Where Forest != 'off' " ; 

                          break;
                    case 3:
                          $q  = " SELECT MAX(Repord) as maxorden
                                  From ".$wbasemovhos."_000254
                                  Where Repest != 'off' " ;

                          break;
              
            }           
    
            $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

            $num = mysql_num_rows($res);

            if ($num > 0){
                $row = mysql_fetch_assoc($res);
                $resultado = $row['maxorden'];
            }
            else
                $resultado = 1;  

            echo $resultado;

            return;
        }



        // Consultar todos los Formularios para actualizar table 'tblformulario'
        if (isset($_POST["accion"]) && $_POST["accion"] == "ActualizarFormulario"){

            $arr_detalle = array();

            $q  = " SELECT Fordes,Forcog,Forord,Forcof,Forcoc,A.Id,Condes
                    From ".$wbasemovhos."_000253 A
                    Inner join ".$wbasemovhos."_000252 B
                       on A.Forcog = B.Concog
                    Where Forest != 'off' 
                    Order by Forord"  ;

            $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

            $num = mysql_num_rows($res);

            if  ($num > 0)
            {
                  
                  // Consultar todos los empleados asignados al lustro
                  while($row = mysql_fetch_assoc($res))
                  { 

                       $arr_detalle[] = array(  "orden_formulario"   => $row['Forord'],
                                                "descrip_formulario" => utf8_encode($row['Fordes']),
                                                "codigru_formulario" => utf8_encode($row['Forcog']),
                                                "grupo_formulario"   => utf8_encode($row['Condes']),
                                                "codigo_formulario"  => $row['Forcof'],
                                                "campo_formulario"   => $row['Forcoc'],
                                                "id_formulario"      => $row['Id'] );
                  }
            }
            else
                  $arr_detalle[]  = 'N';

            
            echo json_encode($arr_detalle);

            return;
      }



      // Consultar todos los Formularios para actualizar table 'tblformulario'
      if (isset($_POST["accion"]) && $_POST["accion"] == "ActualizarReporte"){

            $arr_detalle = array();

            $q  = " SELECT Repdes,Repcog,Repord,Repurl,A.Id,Condes
                    From ".$wbasemovhos."_000254 A
                    Inner join ".$wbasemovhos."_000252 B
                       on A.Repcog = B.Concog
                    Where Repest != 'off' 
                    Order by Repord"  ;

            $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

            $num = mysql_num_rows($res);

            if  ($num > 0)
            {

                  // Consultar todos los empleados asignados al lustro
                  while($row = mysql_fetch_assoc($res))
                  { 

                        $arr_detalle[] = array( "orden_reporte"   => $row['Repord'],
                                                "descrip_reporte" => utf8_encode($row['Repdes']),
                                                "codigru_reporte" => utf8_encode($row['Repcog']),
                                                "grupo_reporte"   => utf8_encode($row['Condes']),
                                                "url_reporte"     => $row['Repurl'],
                                                "id_reporte"      => $row['Id'] );

                  }
            }
            else
                  $arr_detalle[]  = 'N';

            
            echo json_encode($arr_detalle);

            return;

      }



      // Consultar todos los Formularios para actualizar table 'tblgrupo'
      if(isset($_POST["accion"]) && $_POST["accion"] == "ActualizarGrupos"){

            $arrdetalle = array();

            $q  = " SELECT Concog,Condes,Conord,Connuc,Contip,Congru,Id
                    From ".$wbasemovhos."_000252
                    Where Conest != 'off' 
                    Order by Conord "  ;

            $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

            $num = mysql_num_rows($res);

            if  ($num > 0)
            {

                  // Consultar todos los empleados asignados al lustro
                  while($row = mysql_fetch_assoc($res))
                  { 

                       if( $row['Contip']=='F')
                           $tipo  = 'Formulario';
                       else
                           $tipo  = 'Reporte';  

                       if( $row['Congru']=='G')
                           $grupo = 'Grupo';
                       else
                           $grupo = 'Subgrupo';  


                       $arrdetalle[] = array(  "orden_grupo"     => $row['Conord'],
                                               "descrip_grupo"   => utf8_encode($row['Condes']),
                                               "columnas_grupo"  => utf8_encode($row['Connuc']),
                                               "tipo_grupo"      => $tipo,
                                               "clasi_grupo"     => $grupo,
                                               "codigo_grupo"    => $row['Concog'],
                                               "id_grupo"        => $row['Id'] );
                  }
            }
            else
                  $arrdetalle[]  = 'N';

            
            echo json_encode($arrdetalle);

            return;

      }


      // Consultar los formularios activos en la tabla hce_000001
      if ( isset($_POST["accion"]) && $_POST["accion"] == "ConsultaArrayFormularios" ){

          $sqlpro = "SELECT Encpro, Encdes  
              FROM ". $wbasedato ."_000001  
              WHERE Encest = 'on'
              ORDER BY Encdes ";

          $resfor = mysql_query($sqlpro,$conex) or die (mysql_errno()." - en el query: ".$sqlpro." - ".mysql_error());
           
          $arrListfor = array();
          
          while($rowfor = mysql_fetch_assoc($resfor)){
              
              $arrListfor[$rowfor["Encpro"]] = utf8_encode($rowfor["Encdes"]);
          }

          echo json_encode($arrListfor);

          return;
      }


      // Consultar los formularios activos en la tabla hce_000002
      if ( isset($_POST["accion"]) && $_POST["accion"] == "ConsultaArrayCampos" ){

          $sqlpro = "SELECT Detcon, Detdes  
                      FROM ". $wbasedato ."_000002
                      WHERE Detest = 'on'
                        AND Detpro = '".$wformulario."'
                      ORDER BY Detdes ";

          $resfor = mysql_query($sqlpro,$conex) or die (mysql_errno()." - en el query: ".$sqlpro." - ".mysql_error());
           
          $arrListfor = array();
          
          while($rowfor = mysql_fetch_assoc($resfor)){
              
              $arrListfor[$rowfor["Detcon"]] = utf8_encode($rowfor["Detdes"]);
          }

          echo json_encode($arrListfor);

          return;
      }


      // Consultar los datos del id consultado en el detalle 
      if ( isset($_POST["accion"]) && $_POST["accion"] == "GrabarGrupo" ){

          if ( $widegrupo == 0 ){

               // * * * * * Selecionar el maximo codigo del Normograma para obtener consecutivo
               $q=" SELECT MAX(CAST(Concog AS UNSIGNED)) as maximo From ".$wbasemovhos."_000252" ;
               $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
               $num = mysql_num_rows($res);

               if ( $num > 0 ){
                    $row         = mysql_fetch_assoc($res);
                    $vmaximo     = $row['maximo'];
                    $wmaxcodigo  = $vmaximo +1;
               }
               else
                    $wmaxcodigo  = 1;
               

               $q = " INSERT INTO ".$wbasemovhos."_000252
                        (Medico,Fecha_data,Hora_data,Concog,Condes,Conord,Connuc,Contip,Congru,Conest,Seguridad) 
                        VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wmaxcodigo."','".utf8_decode($wdescripcion)."','".$worden."','".$wnumcolumna."','".$wtipo."','".$wgrupo."','on','C-".$wusuario."') ";

          }
          else{
            
               // Actualizar grupos         
               $q = " UPDATE ".$wbasemovhos."_000252
                       SET    Condes  = '".utf8_decode($wdescripcion)."',
                              Conord  = '".$worden."',
                              Connuc  = '".$wnumcolumna."',
                              Contip  = '".$wtipo."',
                              Congru  = '".$wgrupo."'                              
                       WHERE Id  = ".$widegrupo;

          }

          $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error()); 
          
          echo $res;

          return;

      }


      // Consultar los datos del id consultado en el detalle 
      if ( isset($_POST["accion"]) && $_POST["accion"] == "GrabarFormularioRep" ){


          if ($wopcionfor == 1){

              if ( $wideformulario == 0 ){

                    $q = " INSERT INTO ".$wbasemovhos."_000253
                            (Medico,Fecha_data,Hora_data,Fordes,Forcog,Forord,Forcof,Forcoc,Forest,Seguridad) 
                            VALUES ('".$wbasemovhos."','".$wfecha."','".$whora."','".utf8_decode($warraycampos[0])."','".$warraycampos[1]."',".$warraycampos[2].",'".$warraycampos[3]."','".$warraycampos[4]."','on','C-".$wusuario."') ";

              }
              else{
                    
                    $q = " UPDATE ".$wbasemovhos."_000253
                           SET    Fordes  = '".utf8_decode($warraycampos[0])."',
                                  Forcog  = '".$warraycampos[1]."',
                                  Forord  = '".$warraycampos[2]."',
                                  Forcof  = '".$warraycampos[3]."',
                                  Forcoc  = '".$warraycampos[4]."'                              
                           WHERE Id  = ".$wideformulario;
              }

          }
          else{

              if ( $widereporte == 0 ){
                  
                    $q = " INSERT INTO ".$wbasemovhos."_000254
                            (Medico,Fecha_data,Hora_data,Repdes,Repcog,Repord,Repurl,Repest,Seguridad) 
                            VALUES ('".$wbasemovhos."','".$wfecha."','".$whora."','".utf8_decode($warraycampos[0])."','".$warraycampos[1]."',".$warraycampos[2].",'".$warraycampos[3]."','on','C-".$wusuario."') ";
              }
              else{

                    $q = " UPDATE ".$wbasemovhos."_000254
	                       SET    Repdes  = '".utf8_decode($warraycampos[0])."',
	                              Repcog  = '".$warraycampos[1]."',
	                              Repord  = '".$warraycampos[2]."',
	                              Repurl  = '".$warraycampos[3]."'                              
	                       WHERE Id  = ".$widereporte;
              }        

          } 

          $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

          echo $res;

          return;

      }



      // Consultar los datos del registro seleccionado (tabla movhos_000252)
      if ( isset($_POST["accion"]) && $_POST["accion"] == "EditarGrupo" ){

            $q  = " SELECT Concog,Condes,Conord,Connuc,Contip,Congru,Id
                    From ".$wbasemovhos."_000252
                    Where Id = '".$wgrupoide."' ";

            $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

            $num = mysql_num_rows($res);

            if  ($num > 0)
            {
                  // Consultar todos los empleados asignados al lustro
                  while($row = mysql_fetch_assoc($res))
                  { 

                       $detalle = $row['Id'].'|'.
                                  $row['Concog'].'|'.
                                  $row['Conord'].'|'.
                                  $row['Condes'].'|'.
                                  $row['Connuc'].'|'.
                                  $row['Contip'].'|'.
                                  $row['Congru'];
                  }
            }
                 
            echo $detalle;

            return;

      }


      // Consultar los datos del registro seleccionado (tabla hce_000253) 
      if ( isset($_POST["accion"]) && $_POST["accion"] == "EditarFormulario" ){

            $q  = " SELECT Fordes,Forcog,Forord,Forcof,Forcoc,Id
                    From ".$wbasemovhos."_000253
                    Where Id = '".$wgrupoide."' ";

            $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

            $num = mysql_num_rows($res);

            if  ($num > 0)
            {

                  // Consultar todos los empleados asignados al lustro
                  while($row = mysql_fetch_assoc($res))
                  { 

                       $detalle = $row['Id'].'|'.
                                  $row['Forord'].'|'.
                                  $row['Fordes'].'|'.
                                  $row['Forcog'].'|'.
                                  $row['Forcof'].'|'.
                                  $row['Forcoc'];
                  }
            }
                 
            echo $detalle;

            return;
      }


      // Consultar los datos del registro seleccionado (tabla hce_000254) 
      if ( isset($_POST["accion"]) && $_POST["accion"] == "EditarReporte" ){

            $q  = " SELECT Repdes,Repcog,Repord,Repurl,Id
                    From ".$wbasemovhos."_000254
                    Where Id = '".$wgrupoide."' ";

            $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

            $num = mysql_num_rows($res);

            if  ($num > 0)
            {

                  // Consultar todos los empleados asignados al lustro
                  while($row = mysql_fetch_assoc($res))
                  { 

                       $detalle = $row['Id'].'|'.
                                  $row['Repord'].'|'.
                                  $row['Repdes'].'|'.
                                  $row['Repcog'].'|'.
                                  $row['Repurl'];
                  }
            }
                 
            echo $detalle;

            return;

      }


      // Desactivar uno o varios registros seleccionados 
      if ( isset($_POST["accion"]) && $_POST["accion"] == "EliminarGeneral" ){

         $arreliminar = explode('|', $wcodeliminar);

         $respuesta = 0;

         switch ($wopcion) {

               case 1:
                   $wtabla = '000252';
                   $wcampo = 'Conest';
                   break;
               
               case 2:
                   $wtabla = '000253';
                   $wcampo = 'Forest';
                   break;

               case 3:
                   $wtabla = '000254';
                   $wcampo = 'Repest';
                   break;
         }

  
         for ($x=0; $x<count($arreliminar)-1; $x++){

              if ( $wopcion == 1 ){

                    $num1=0;

                    $num2=0;

                    // Buscar si el código existe en la tabla de Formularios
                    $q1  = " SELECT Fordes,Forcog
                             From ".$wbasemovhos."_000253 
                             Where Forcog = ".$arreliminar[$x]." 
                               And Forest != 'off' 
                             Order by Forord " ;

                    $res1 = mysql_query($q1,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

                    $num1 = mysql_num_rows($res1);


                    // Buscar si el código existe en la tabla de Reportes                    
                    $q2  = " SELECT Repdes,Repcog
                             From ".$wbasemovhos."_000254 
                             Where Repcog = ".$arreliminar[$x]." 
                               And Repest != 'off' 
                             Order by Repord " ;

                    $res2 = mysql_query($q2,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

                    $num2 = mysql_num_rows($res2);


                    // Verificar si encontró el código del grupo seleccionado
                    if ( $num1 >0 or $num2 >0 ){

                         $respuesta = 2;

                         break;
                    }
                    else{

                         $q = " UPDATE  ".$wbasemovhos."_".$wtabla."
                                SET ".$wcampo." = 'off'
                                WHERE Id = ".$arreliminar[$x];
                     
                         $rescon = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());   

                         $respuesta = 1;
                    }     

              }
              else{

                    // Borrar todos los empleados asignados al coordinador
                    $q = " UPDATE  ".$wbasemovhos."_".$wtabla."
                           SET ".$wcampo." = 'off'
                           WHERE Id = ".$arreliminar[$x];
                     
                    $rescon = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());   

                    $respuesta = 1;

              } 
   
         }
   
         echo $respuesta;

         return;
      }


      function ConsultarGruposgral($wbasemovhos,$conex,$wemp_pmla)
      {
            $arr_detalle = array();

            $q  = " SELECT Concog,Condes,Conord,Connuc,Contip,Congru,Id
                    From ".$wbasemovhos."_000252
                    Where Conest != 'off' 
                    Order by Conord"  ;

            $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

            $num = mysql_num_rows($res);

            if  ($num > 0)
            {

                  // Consultar todos los empleados asignados al lustro
                  while($row = mysql_fetch_assoc($res))
                  { 

                       if( $row['Contip']=='F')
                           $tipo = 'Formulario';
                       else
                           $tipo = 'Tipo';  

                       if( $row['Congru']=='G')
                           $grupo = 'Grupo';
                       else
                           $grupo = 'Subgrupo';  


                       $arr_detalle[] = array(  "orden_grupo"     => $row['Conord'],
                                                "descrip_grupo"   => ($row['Condes']),
                                                "columnas_grupo"  => $row['Connuc'],
                                                "tipo_grupo"      => $tipo,
                                                "clasi_grupo"     => $grupo,
                                                "codigo_grupo"    => $row['Concog'],
                                                "id_grupo"        => $row['Id'] );
                  }
            }
            else

                  $arr_detalle[]  = 'N';
             
            return $arr_detalle;

      }



      function ConsultarFormulariogral($wbasemovhos,$conex,$wemp_pmla)
      {
            $arr_detalle = array();

            $q  = " SELECT Fordes,Forcog,Forord,Forcof,Forcoc,A.Id,Condes
                    From ".$wbasemovhos."_000253 A
                    Inner join ".$wbasemovhos."_000252 B
                       on A.Forcog = B.Concog
                    Where Forest != 'off' 
                    Order by Forord"  ;

            $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

            $num = mysql_num_rows($res);

            if  ($num > 0)
            {

                  // Consultar todos los empleados asignados al lustro
                  while($row = mysql_fetch_assoc($res))
                  { 
 
                       $arr_detalle[] = array(  "orden_formulario"   => $row['Forord'],
                                                "descrip_formulario" => $row['Fordes'],
                                                "codigru_formulario" => $row['Forcog'],
                                                "grupo_formulario"   => $row['Condes'],
                                                "codigo_formulario"  => $row['Forcof'],
                                                "campo_formulario"   => $row['Forcoc'],
                                                "id_formulario"      => $row['Id'] );
                  }
            }
            else

                  $arr_detalle[]  = 'N';

            
            return $arr_detalle;

      }


      
     function ConsultarReportegral($wbasemovhos,$conex,$wemp_pmla)
     {

            $arr_detalle = array();

            $q  = " SELECT Repdes,Repurl,Repcog,Repord,A.Id,Condes
                    From ".$wbasemovhos."_000254 A
                    Inner join ".$wbasemovhos."_000252 B
                       on A.Repcog = B.Concog
                    Where Repest != 'off' 
                    Order by Repord"  ;

            $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

            $num = mysql_num_rows($res);

            if  ($num > 0)
            {

                  // Consultar todos los empleados asignados al lustro
                  while($row = mysql_fetch_assoc($res))
                  { 

                       $arr_detalle[] = array(  "orden_reporte"   => $row['Repord'],
                                                "descrip_reporte" => ($row['Repdes']),
                                                "codigru_reporte" => $row['Repcog'],
                                                "grupo_reporte"   => $row['Condes'],
                                                "url_reporte"     => $row['Repurl'],
                                                "id_reporte"      => $row['Id'] );
                  }
            }
            else

                  $arr_detalle[]  = 'N';

            
            return $arr_detalle;

     }



      // Consultar todos los Centros de Costos para el campo autocompletar
      function ConsultarCentros($wbasemovhos,$conex,$wemp_pmla){
          $strtipvar = array();

          $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                      FROM    ".$wbasemovhos."_000011 AS tb1                                    
                      GROUP BY    tb1.Ccocod
                      ORDER BY    tb1.Cconom";

          $res = mysql_query($query,$conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());
          while($row = mysql_fetch_assoc($res))
          {
               $strtipvar[$row['codigo']] = $row['codigo'].'-'.utf8_encode($row['nombre']);
          }

         return $strtipvar;
      }



      // Consultar todos los Formularios y enviarlos en un array
      function ConsultarFormularios($wbasemovhos,$conex,$wemp_pmla){
          
          $strtipvar = array();

          $query = "  SELECT   Concog,Condes
                      FROM     ".$wbasemovhos."_000252                                    
                      WHERE    Contip='F' And Conest != 'off'
                      ORDER BY Condes";

          $res = mysql_query($query,$conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());

          while($row = mysql_fetch_assoc($res))
          {
               $strtipvar[$row['Concog']] = ($row['Condes']);
          }

          return $strtipvar;

      }



      // Consultar todos los Reportes
      function ConsultarReportes($wbasemovhos,$conex,$wemp_pmla){
          $strtipvar = array();

          $query = "  SELECT  Repcog,Repdes
                      FROM    ".$wbasemovhos."_000254                                    
                      WHERE   Repest != 'off'
                      ORDER BY Repdes";

          $res = mysql_query($query,$conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());

          while($row = mysql_fetch_assoc($res))
          {
               $strtipvar[$row['Repcog']] = ($row['Repdes']);
          }

          return $strtipvar;
      }


  // *****************************************         FIN PHP         ********************************************
  ?>
  <!DOCTYPE html>
  <html lang="es-ES">
  <head>
    <title>Configuracion Contingencia</title>
    <meta charset="utf-8">
    <meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />    
    <script src="../../../include/root/jquery.min.js"></script> 
    <script src="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.js" type="text/javascript"></script>
    <link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.min.css" rel="stylesheet"/>
    <link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.theme.min.css" rel="stylesheet"/>
    <link type="text/css" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.structure.min.css" rel="stylesheet"/>

    <script src="../../../include/root/bootstrap.min.js"></script> 
    <script src="../../../include/root/bootbox.min.js"></script> 
    <link rel="stylesheet" href="../../../include/root/bootstrap.min.css">

<!--     <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
    <script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery-sortable.js" type="text/javascript"></script> -->

    <script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
    <script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>

<!--<link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.css" />
    <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.filter.css" />
    <script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
    <script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script> -->   
    
    <script type="text/javascript">

       var stringEx  = ""; 
       var celda_ant = "";
       var celda_ant_clase= "";
       var arrformularios = new Array();
       var arrcampos      = new Array();
      
       $(document).ready(function(){
          
            $('#btngrupo').click();

            ActivarControles();

            CargarFormularios();

       });

      
      //Cargar todos los formularios activos de la tabla hce_000001
      function CargarFormularios(){
        
            // Cargar Array Procedimientos
           $.post("configuracion_contingencia.php",
                 { 
                   consultaAjax   :    true,
                   accion         :    'ConsultaArrayFormularios',  
                   wemp_pmla      :    $("#wemp_pmla").val()
                 }, function(respuesta){
                    var index         = -1;

                    for (var cod_for in respuesta){
                          index++;
                          arrformularios[index]          = {};
                          arrformularios[index].value    = cod_for;
                          arrformularios[index].label    = cod_for +'-'+respuesta[cod_for];
                          arrformularios[index].codigo   = cod_for;
                    }

                },"json");

               
               $("#txtcodigoformulario").autocomplete({
                  source: arrformularios,          
                  autoFocus: true,           
                  select:     function( event, ui ){
                          var cod_sel = ui.item.codigo;
                          $(this).val(cod_sel);
                          $(this).attr("codigo",cod_sel);
                  }
               });     

      }

      
      // Seleccionar los campos según el formulario seleccionado. Tabla hce_000002
      function SeleccionarCampos(wformulario){

           // Cargar Array Procedimientos
           $.post("configuracion_contingencia.php",
                 { 
                    consultaAjax   :   true,
                    accion         :   'ConsultaArrayCampos',  
                    wemp_pmla      :   $("#wemp_pmla").val(),
                    wformulario    :   wformulario
                 }, function(respuesta){
                    var index         = -1;

                    for (var cod_cam in respuesta){
                          index++;

                          arrcampos[index]          = {};
                          arrcampos[index].value    = cod_cam;
                          arrcampos[index].label    = cod_cam +'-'+respuesta[cod_cam];
                          arrcampos[index].codigo   = cod_cam;
                    }

                },"json");

               
               $("#txtcampoformulario").autocomplete({
                  source: arrcampos,          
                  autoFocus: true,           
                  select: function( event, ui ){
                            var cod_sel = ui.item.codigo;
                            $(this).val(cod_sel);
                            $(this).attr("codigo",cod_sel);
                  }
               });   
      }


      function InicializarGeneral(param){

            var vedicion = '';

            switch(param) {
            
                case 1:
                  vedicion = $("#wedicion").val(); 
                  break;
                case 2:
                  vedicion = $("#wediformulario").val();
                  break;
                case 3:
                  vedicion = $("#wedireporte").val();
                  break; 
            }             

            if (vedicion == 'S'){

                  bootbox.confirm({
                      title: "Confirmar",
                      message: "Desea cancelar la Edicion del registro?",
                      buttons: {
                          confirm: {
                              label: "Cancelar",
                              className: "btn-dafault"
                          },
                          cancel: {
                              label: "Aceptar",
                              className: "btn-primary"
                          }
                      },
                      callback: function(result) {
                          if (result==false) {
                              AdicionarGeneral(param);
                          } else {
                              return;
                          }
                      }
                  });
                
            }
            else
                AdicionarGeneral(param);
      
       }


       //Activa y limpia todos los campos, según el formulario seleccionado
       function AdicionarGeneral(opcion){

            $("input[class^=form-control]").each(function(){
                $(this).val('');
            }); 

            // Consultar consecutivo campo orden
            var vorden = '';
            $.post("configuracion_contingencia.php",
                 { 
                    consultaAjax   :   true,
                    accion         :   'ConsultaOrden',  
                    wemp_pmla      :   $("#wemp_pmla").val(),
                    wopcion        :   opcion
                 }, function(respuesta){
         
                    vorden=(respuesta*1)+1;  
              
                    //Limpiar campos
                    switch(opcion) {
                    
                        case 1:

                               $("#txtdescripcion").attr("readonly",false);
                               $("#txtorden").attr("readonly",false);
                               $("#txtnumcolumna").attr("readonly",false);
                               $("#seltipo").attr("readonly",false);
                               $("#selgrupo").attr("readonly",false);                               
                               $("#widegrupo").val('0');
                               $("#wedicion").val('S');
                               $("#txtorden").val(vorden);
                               $("#txtdescripcion").focus();

                               $('#btnGrabar').removeClass('button2'); 
                               $('#btnGrabar').addClass('button');
                               $('#btnGrabar').removeAttr('disabled');

                               $('input#id_search_grupos').val('');
                               $('input#id_search_grupos').keyup();
                                  
                               break;

                        case 2:
                               $("#wideformulario").val('0');                       
                               $("#wediformulario").val('S');
                               $("#txtordenformulario").val(vorden);
                               $('#btnGrabarFor').removeClass('button2'); 
                               $('#btnGrabarFor').addClass('button');
                               $('#btnGrabarFor').removeAttr('disabled');  
                               $("#txtdescriformulario").attr("readonly",false);
                               $("#selformulario").attr("readonly",false);
                               $("#txtordenformulario").attr("readonly",false);
                               $("#txtcodigoformulario").attr("readonly",false);
                               $("#txtcampoformulario").attr("readonly",false);                      
                               $('input#id_search_formularios').val('');
                               $('input#id_search_formularios').keyup();
                               break;

                        case 3:
                               $("#widereporte").val('0');
                               $("#wedireporte").val('S');
                               $("#txtordenreporte").val(vorden);
                               $("#txtdescrireporte").focus();
                               $('#btnGrabarRep').removeClass('button2');
                               $('#btnGrabarRep').addClass('button');
                               $('#btnGrabarRep').removeAttr('disabled'); 
                               $("#txtdescrireporte").attr("readonly",false);
                               $("#selreporte").attr("readonly",false);
                               $("#txtordenreporte").attr("readonly",false);
                               $("#txturlreporte").attr("readonly",false);   
                               break;
                    }

            });

       }


       function ActivarControles()
       {

            $('input#id_search_grupos').quicksearch('table#tblgrupo tbody tr');

            $('input#id_search_formularios').quicksearch('table#tblformulario tbody tr');

            $("#tblformulario tbody tr").on('dblclick',function(event) {
                $("#tblformulario tbody tr").removeClass('row_selected');    
                $(this).addClass('row_selected');
            });


            $("#tblgrupo tbody tr").on('dblclick',function(event) {
                $("#tblgrupo tbody tr").removeClass('row_selected');    
                $(this).addClass('row_selected');
            });

            $('#tblformulario,#tblreporte').sortable({
                containerSelector: 'table',
                itemPath: '> tbody',
                itemSelector: 'tr',
                placeholder: '<tr class="placeholder"/>'
            });

            $('#tblgrupo').sortable({
                containerSelector: 'table',
                itemPath: '> tbody',
                itemSelector: 'tr',
                placeholder: '<tr class="placeholder"/>'
            });

       }


       function ConsultarGrupo()
       {

              $.post("configuracion_contingencia.php",
              {
                  consultaAjax :  true,
                  accion       :  'ActualizarGrupos',
                  wemp_pmla    :  $("#wemp_pmla").val()

              }, function(respuesta){


                        var fila = "fila2";
                        var stringTr = '';

                        $("#tblgrupo tbody").empty();

                        jQuery.each(respuesta, function(){  

                               stringTr =  stringTr + '<tr id="'+this.id_grupo+'" class="'+fila+' item" ondblclick="EditarGeneral(this,1,'+this.id_grupo+')">'
                                                    + '<td align="center">'+this.orden_grupo+'</td>'
                                                    + '<td align="center">'+this.descrip_grupo+'</td>'
                                                    + '<td align="center">'+this.columnas_grupo+'</td>'
                                                    + '<td align="center">'+this.tipo_grupo+'</td>'
                                                    + '<td align="center">'+this.clasi_grupo+'</td>'
                                                    + '<td align="center"><input type="checkbox" id="chkseleccion1" class="chkseleccion1" align="left" value="'+this.id_grupo+'" ></td></tr>';

                        });

                        $('#tblgrupo > tbody:last').append(stringTr);

                        ActivarControles();                     

               }, 'json');
       }



       function ConsultarSelect()
       {

              $.post("configuracion_contingencia.php",
              {
                      consultaAjax :  true,
                      accion       :  'ActualizarGrupos',
                      wemp_pmla    :  $("#wemp_pmla").val()

              }, function(respuesta){

                      var stringTr = '';

                      // Actualizar el select del contenedor de Formularios
                      
                      $("#selformulario").html('');

                      jQuery.each(respuesta, function(){

                            if (this.tipo_grupo == 'Formulario')
                                stringTr += '<option value='+this.codigo_grupo+'>'+this.descrip_grupo+'</option>';

                      });

                      $('#selformulario').append(stringTr);


                      // Actualizar el select del contenedor de Reportes
                       
                      var stringTr = '';
                      
                      $("#selreporte").html('');

                      jQuery.each(respuesta, function(){ 

                            if (this.tipo_grupo == 'Reporte')
                                 stringTr += '<option value='+this.codigo_grupo+'>'+this.descrip_grupo+'</option>';

                      });

                      $('#selreporte').append(stringTr);
    

              }, 'json');

       }


       function ConsultarFormulario()
       {

              $.post("configuracion_contingencia.php",
              {
                  consultaAjax :  true,
                  accion       :  'ActualizarFormulario',
                  wemp_pmla    :  $("#wemp_pmla").val()

              }, function(respuesta){

                        var fila = "fila2";
                        var stringTr = '';

                        $("#tblformulario tbody").empty();

                        jQuery.each(respuesta, function(){  

                                stringTr =  stringTr + '<tr id="'+this.id_formulario+'" class="'+fila+'" ondblclick="EditarGeneral(this,2,'+this.id_formulario+')">'
                                                     + '<td align="center">'+this.orden_formulario+'</td>'
                                                     + '<td align="center">'+this.descrip_formulario+'</td>'
                                                     + '<td align="center">'+this.grupo_formulario+'</td>'
                                                     + '<td align="center">'+this.codigo_formulario+'</td>'
                                                     + '<td align="center">'+this.campo_formulario+'</td>'
                                                     + '<td align="center"><input type="checkbox" id="chkseleccion2" class="chkseleccion2" align="left" value="'+this.id_formulario+'" ></td></tr>';

                        });

                        $('#tblformulario > tbody:last').append(stringTr);

                        ActivarControles();

               }, 'json');
       }


       function ConsultarReporte()
       {

              $.post("configuracion_contingencia.php",
              {
                  consultaAjax :  true,
                  accion       :  'ActualizarReporte',
                  wemp_pmla    :  $("#wemp_pmla").val()

              }, function(respuesta){

                        var fila = "fila2";
                        var stringTr = '';

                        $("#tblreporte tbody").empty();

                        jQuery.each(respuesta, function(){  


                                stringTr =  stringTr + '<tr id="'+this.id_reporte+'" class="'+fila+'" ondblclick="EditarGeneral(this,3,'+this.id_reporte+')">'
                                                     + '<td align="center">'+this.orden_reporte+'</td>'
                                                     + '<td align="center">'+this.descrip_reporte+'</td>'
                                                     + '<td align="center">'+this.grupo_reporte+'</td>'
                                                     + '<td align="center">'+this.url_reporte+'</td>'
                                                     + '<td align="center"><input type="checkbox" id="chkseleccion3" class="chkseleccion3" align="left" value="'+this.id_reporte+'" ></td></tr>';


                        });

                        $('#tblreporte > tbody:last').append(stringTr);

                        ActivarControles();

               }, 'json');

       }


       function VerificarOrden(obj,opcion)
       {

          var wgrupo ='';

          var id  = $(obj).attr('id');

          if (opcion==2)
              wgrupo = $("#selformulario").val();

          if (opcion==3)  
              wgrupo  = $("#selreporte").val();

         
          $.post("configuracion_contingencia.php",
                {
                     consultaAjax :  true,
                     accion       :  'VerificarOrden',
                     wopcion      :  opcion,
                     worden       :  obj.value,
                     wgrupo       :  wgrupo,
                     wemp_pmla    :  $("#wemp_pmla").val()

                }, function(respuesta){
                  
                   if (respuesta > 0){                       
                       jAlert('El orden ya se encuentra asignado','Mensaje');
                       $("#"+id).focus();
                   }
                   
                });

       }



       function EditarGeneral(obj,opc,Ide)
       {
    
                var wconedi = 0;

                if ($("#wedicion").val() == 'S' || $("#wediformulario").val() == 'S' || $("#wedireporte").val() == 'S'){

                  bootbox.confirm({
                      title: "Confirmar",
                      message: "Desea cancelar la Edicion del registro?",
                      buttons: {
                          confirm: {
                              label: "Cancelar",
                              className: "btn-default"
                          },
                          cancel: {
                              label: "Aceptar",
                              className: "btn-primary"
                          }
                      },
                      callback: function(result) {
                          if (result==false) {
                              VerEdicion(opc,Ide);
                          } else {
                              return;
                          }
                      }
                  });
                    
     
                }
                else
                    VerEdicion(opc,Ide);
  
       }


       // Mostrar los campos del Registro seleccionado
       function VerEdicion(opc,Ide) 
       {

            switch(opc){
               
                  case 1:

                        $.post("configuracion_contingencia.php",
                        {
                               consultaAjax :  true,
                               accion       :  'EditarGrupo',
                               wgrupoide    :  Ide,
                               wemp_pmla    :  $("#wemp_pmla").val()

                        }, function(respuesta){
                           
                               var result = respuesta.split('|');

                               $("#txtdescripcion").val(result[3]);
                               $("#txtorden").val(result[2]);
                               $("#txtnumcolumna").val(result[4]);
                               $("#seltipo").val(result[5]);
                               $("#selgrupo").val(result[6]);
                               $("#widegrupo").val(result[0]);
                               $("#wedicion").val('S');
                               $('#btnGrabar').removeClass('button2'); 
                               $('#btnGrabar').addClass('button');
                               $('#btnGrabar').removeAttr('disabled'); 
							   $('.frmgrupo').attr('readonly',false);

                        });
                        break;

                  case 2:
                        $.post("configuracion_contingencia.php",
                        {
                               consultaAjax :  true,
                               accion       :  'EditarFormulario',
                               wgrupoide    :  Ide,
                               wemp_pmla    :  $("#wemp_pmla").val()

                        }, function(respuesta){
                     
                               var result = respuesta.split('|');

                               $("#txtdescriformulario").val(result[2]);
                               $("#txtordenformulario").val(result[1]);
                               $("#selformulario").val(result[3]);
                               $("#txtcodigoformulario").val(result[4]);
                               $("#txtcampoformulario").val(result[5]);
                               $("#wideformulario").val(result[0]);
                               $("#wediformulario").val('S');
                               $('#btnGrabarFor').removeClass('button2'); 
                               $('#btnGrabarFor').addClass('button');
                               $('#btnGrabarFor').removeAttr('disabled'); 
							   $("#txtdescriformulario").attr("readonly",false);
                               $("#selformulario").attr("readonly",false);
                               $("#txtordenformulario").attr("readonly",false);
                               $("#txtcodigoformulario").attr("readonly",false);
                               $("#txtcampoformulario").attr("readonly",false); 

                        });
                        break;

                  case 3:
                        $.post("configuracion_contingencia.php",
                        {
                               consultaAjax :  true,
                               accion       :  'EditarReporte',
                               wgrupoide    :  Ide,
                               wemp_pmla    :  $("#wemp_pmla").val()

                        }, function(respuesta){
                           
                               var result = respuesta.split('|');

                               $("#txtdescrireporte").val(result[2]);
                               $("#txtordenreporte").val(result[1]);
                               $("#selreporte").val(result[3]);
                               $("#txturlreporte").val(result[4]);
                               $("#widereporte").val(result[0]);
                               $("#wedireporte").val('S');
                               $('#btnGrabarRep').removeClass('button2');
                               $('#btnGrabarRep').addClass('button');
                               $('#btnGrabarRep').removeAttr('disabled');
                               $("#txtdescrireporte").attr("readonly",false);
                               $("#selreporte").attr("readonly",false);
                               $("#txtordenreporte").attr("readonly",false);
                               $("#txturlreporte").attr("readonly",false); 
                        });

                        break;
            }
       }



       function GrabarGrupo()
       {

            cont = 0;

            //Verificar que todos los campos estén diligenciados
            $("input[valida^=val1]").each(function(){

                if ($(this).val() == "")
                    cont++;                
            });

            if ($("#txtdescripcion").val() == '' || $("#txtnumcolumna").val() == 0 ||  $("#seltipo option:selected" ).val() == '' ||  $("#selgrupo option:selected" ).val() == '')
                cont++;

            if (cont>0){

                jAlert('Falta informacion','Mensaje');
                return;
            }
            else{

                $.post("configuracion_contingencia.php",{
                         consultaAjax :  true,
                         accion       :  'GrabarGrupo',
                         widegrupo    :  $("#widegrupo").val(),
                         wdescripcion :  $("#txtdescripcion").val(),
                         worden       :  $("#txtorden").val(),
                         wnumcolumna  :  $("#txtnumcolumna").val(),
                         wtipo        :  $("#seltipo").val(),
                         wgrupo       :  $("#selgrupo").val(),
                         wemp_pmla    :  $("#wemp_pmla").val()

                }, function(respuesta){
                     
                     if (respuesta==1){
                         jAlert('La informacion ha sido grabada','Mensaje');
                         ConsultarGrupo();
                         ConsultarSelect();
                         $('#btnGrabar').removeClass('button'); 
                         $('#btnGrabar').addClass('button2');
                         $('#btnGrabar').attr('disabled',true); 
						 $('.frmgrupo').attr('readonly',true);
                         $('#wedicion').val('N');                        
                     }

                });

            }
       }


       // Graba Formularios y Reportes
       function GrabarFormularioRep(opcfor,seleccion)
       {

            var cont   = 0;
            var indice = 0;
            var arraycampos =[];

            $("input[valida^=dos]").each(function(){
                if ($(this).val() == "")
                    cont++;
                
            });
                       
            $(opcfor).find('input:text, input[type="number"], select').each(function() {
                  arraycampos[indice] = $(this).val();
                  indice ++;
            });


            if (cont>0){

                jAlert('Falta informacion','Mensaje');
                return;
            }
            else{

                $.post("configuracion_contingencia.php",{
                         consultaAjax   :  true,
                         accion         :  'GrabarFormularioRep',  
                         warraycampos   :  arraycampos,
                         wopcionfor     :  seleccion,
                         wideformulario :  $("#wideformulario").val(),
                         widereporte    :  $("#widereporte").val(),
                         wemp_pmla      :  $("#wemp_pmla").val()

                }, function(respuesta){

                     if (respuesta==1){
                         jAlert('La informacion ha sido grabada','Mensaje');
                         
                         if ( seleccion==1 ){
                              ConsultarFormulario();
                              $('#btnGrabarFor').removeClass('button'); 
                              $('#btnGrabarFor').addClass('button2');
                              $('#btnGrabarFor').attr('disabled',true); 
                              $('#wediformulario').val('N');  
                         }
                         else{
                              ConsultarReporte();
                              $('#btnGrabarRep').removeClass('button'); 
                              $('#btnGrabarRep').addClass('button2');
                              $('#btnGrabarRep').attr('disabled',true);
                              $('#wedireporte').val('N');
                         }

                     }

                });

            }

       }


       function EliminarGeneral(opc)
       {

          if ($("#wedicion").val() == 'S'){
                bootbox.confirm("Desea cancelar la Edicion del registro?", function(result){

                if (result == true)
                    EjecutarEliminar(opc);
                else  
                    return;

               });
          }
          else
              EjecutarEliminar(opc);    
          
       }


       function EjecutarEliminar(opc){

              cont = 0;
              wcodeliminar = '';

              $("input[class^=chkseleccion"+opc+"]").each(function(){
                        
                        if ( $(this).is(':checked') ) {

                            wcodeliminar += this.value+'|';
                            cont++;
                        }

              });

              if (cont==0){

                        jAlert('No hay filas seleccionadas','Mensaje');
                        return;
              } 
              else{

                  bootbox.confirm("Esta seguro de eliminar el(los) registros?", function(result){
                    
                  if (result == true){

                        $.post("configuracion_contingencia.php",
                        {
                                consultaAjax :  true,
                                accion       :  'EliminarGeneral',
                                wcodeliminar :  wcodeliminar,
                                wopcion      :  opc,
                                wemp_pmla    :  $("#wemp_pmla").val()

                        }, function(respuesta){
                           
                           if (respuesta==1){

                                jAlert('La informacion ha sido eliminada','Mensaje');

                                switch(opc){
                               
                                  case 1:
                                      ConsultarGrupo();
                                      $("#wedicion").val('N');
                                      break;

                                  case 2:
                                      ConsultarFormulario();
                                      $("#wediformulario").val('N');
                                      break;

                                  case 3:
                                      ConsultarReporte();
                                      $("#wedireporte").val('N');
                                      break;

                                }

                                $("input[class^=form-control]").each(function(){
                                   $(this).val('');
                                }); 
                           }

                      });

                  }
                  else

                      $("input[class^=chkseleccion"+opc+"]").attr('checked',false);


                  });
     
              }
       }



       function openTab(evt, cityName) {

          var i, tabcontent, tablinks;
          tabcontent = document.getElementsByClassName("tabcontent");
          
          for (i = 0; i < tabcontent.length; i++) {
              tabcontent[i].style.display = "none";
          }

          tablinks = document.getElementsByClassName("tablinks");
          
          for (i = 0; i < tablinks.length; i++) {
              tablinks[i].className = tablinks[i].className.replace(" active", "");
          }

          document.getElementById(cityName).style.display = "block";
       }


       function justNumbers(e){
           var keynum = window.event ? window.event.keyCode : e.which;

           if ((keynum == 8) || (keynum == 46) || (keynum == 0))
                return true;

           return /\d/.test(String.fromCharCode(keynum));
       }

  
       function CerrarVentana()
       {
            bootbox.confirm("Esta seguro de salir?", function(result){ 
              
                if (result == true)
                    window.close();

            });
       }


       /**
       * [jAlert Simula el JAlert usado en las anteriores versiones de JQuery]
       * @param  {[type]} html   [description]
       * @param  {[type]} titulo [description]
       * @return {[type]}        [description]
       */
      
     function jAlert(html,titulo){
          if($("#jAlert").length == 0)
          {
            var div_jAlert =  '<!-- Modal jAlert -->'
                              +'<div class="modal fade" id="jAlert" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="false">'
                              +'  <div class="modal-dialog" role="document">'
                              +'    <div class="modal-content">'
                              +'      <div class="modal-header">'
                              +'        <h4 class="modal-title" id="alertModalLabel">Modal title</h4>'
                              +'      </div>'
                              +'      <div class="modal-body" >'
                              +'        ...'
                              +'      </div>'
                              +'      <div class="modal-footer">'
                              +'        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>'
                              +'      </div>'
                              +'    </div>'
                              +'  </div>'
                              +'</div>';
              $("body").append(div_jAlert);
          }

          $("#jAlert").find(".modal-header").removeClass("bg-danger");

          $("#jAlert").find("#alertModalLabel").html(titulo);
          $("#jAlert").find(".modal-body").html(html);
          var bg = (titulo.toLowerCase() == 'alerta') ? 'bg-danger': 'bg-primary';
          $("#jAlert").find(".modal-header").addClass(bg);
          $("#jAlert").modal({ backdrop: 'static',
                               keyboard: false}).css("z-index", 2030);
          if((titulo.toLowerCase() == 'alerta')) { $("#jAlert").css("z-index", 2030); }
    }

       </script>

      <style type="text/css">

        .fila1 {
            background-color:   #C3D9FF;
            color:              #000000;
            font-size:          8pt;
            padding:            1px;
            font-family:        verdana;
        }

        .fila2 {
            background-color:   #E8EEF7;
            color:              #000000;
            font-size:          8pt;
            padding:            1px;
            font-family:        verdana;
        }

        .encabezadoTabla {
            background-color : #2a5db0;
            color            : #ffffff;
            font-size        : 9pt;
            font-weight      : bold;
            padding          : 1px;
            font-family      : verdana;
        }

        .titulopagina2
        {
            border-bottom-width: 1px;
            border-left-width: 1px;
            border-top-width: 1px;
            font-family: verdana;
            font-size: 18pt;
            font-weight: bold;
            height: 30px;
            margin: 2pt;
            overflow: hidden;
            text-transform: uppercase;
        }

         #tblgrupo tr td{
            font-size: 10pt;
            border-width: 1px;
            border-style:solid;
            border-color: white;
         }

         #tblformulario tr td{
            font-size: 10pt;
            border-width: 1px;
            border-style:solid;
            border-color: white;
         }

         #tblreporte tr td{
            font-size: 10pt;
            border-width: 1px;
            border-style:solid;
            border-color: white;
         }

        .button{
            color: #1b2631;
            font-weight: normal;
            font-size: 10pt;
            width: 85px; height: 22px;
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

         .button2{
            color: gray;
            font-weight: normal;
            font-size: 10pt;
            width: 85px; height: 22px;
            background: rgb(190,190,190);
            background: -moz-linear-gradient(top,  rgba(199,199,199,1) 0%, rgba(193,193,193,1) 50%, rgba(184,184,184,1) 51%, rgba(224,224,224,1) 100%);
            background: -webkit-linear-gradient(top,  rgba(199,199,199,1) 0%,rgba(193,193,193,1) 50%,rgba(184,184,184,1) 51%,rgba(224,224,224,1) 100%);
            background: linear-gradient(to bottom,  rgba(199,199,199,1) 0%,rgba(193,193,193,1) 50%,rgba(184,184,184,1) 51%,rgba(224,224,224,1) 100%);
            filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#c7c7c7', endColorstr='#e0e0e0',GradientType=0 );
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
        }

        .ui-multiselect { background:white; background-color:white; color: gray; font-weight: normal; font-family: verdana; border-color: gray; border: 3px; height:20px; width:450px; overflow-x:hidden; text-align:left;font-size: 10pt;border-radius: 6px;}

        .ui-multiselect-menu { background:white; background-color:white; color: gray; font-weight: normal; font-size: 10pt;}

        .ui-multiselect-header { background:white; background-color:lightgray; color: gray;font-weight: normal;}


        body {font-family: Arial;margin: 0;}

        /* Style the tab */
        div.tab {
            width: 1250px;
            overflow: hidden;
            border: 1px solid #ccc;
            background-color: #C3D9FF;

        }

        /* Style the buttons inside the tab */
        div.tab button {
            background-color: inherit;
            float: left;
            border: none;
            outline: none;
            cursor: pointer;
            padding: 14px 16px;
            transition: 0.3s;
            font-size: 15px;
        }

        /* Change background color of buttons on hover */
        div.tab button:hover {
            background-color: #ddd;
        }

        /* Create an active/current tablink class */
        div.tab button.active {
            background-color: #ccc;
        }

        /* Style the tab content */
        .tabcontent {
            display: none;
            width: 1250px;
            height: 570px;
            padding: 6px 32px;
            border: 1px solid #ccc;
            border-top: none;
        }

        .row_selected {background-color:lightyellow};

        .modal-body {
            text-align: center;
        }

        .modal-dialog {
            text-align: center;
            margin-top: 200px;
        }

        #tblencagrupo{
            line-height: 1.5px;
        }

        .form-control{
            height: 20px;
            padding: 1px 5px;
            font-size: 12px;
            line-x: 0px;
        }

        .table-curved {
            border-collapse: separate;
        }
        .table-curved {
            border: solid #ccc 1px;
            border-radius: 6px;
            border-left:0px;
        }
        .table-curved td, .table-curved th {
            border-left: 1px solid #ccc;
            border-top: 1px solid #ccc;
        }
        .table-curved th {
            border-top: none;
        }
        .table-hover tbody tr:hover td {
            background: lightyellow;  
        }

    </style>
    </head>
    <body width=100%>
      <input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='<?=$wemp_pmla?>'>
      <?php
          $arr_genformulario = ConsultarFormulariogral($wbasemovhos,$conex,$wemp_pmla);
          $arr_genreporte    = ConsultarReportegral($wbasemovhos,$conex,$wemp_pmla);
          $arr_servicio      = ConsultarCentros($wbasemovhos,$conex,$wemp_pmla);
          $arr_Formulario    = ConsultarFormularios($wbasemovhos,$conex,$wemp_pmla);
          $arr_Reporte       = ConsultarReportes($wbasemovhos,$conex,$wemp_pmla); 
          $arr_grupos        = ConsultarGruposgral($wbasemovhos,$conex,$wemp_pmla);       
          $wtitulo           = "CONFIGURACION CONTINGENCIA HISTORIA CLINICA";
          encabezado("<div class='titulopagina2'>{$wtitulo}</div>", $wactualiza, "clinica");
      ?>
      <br>
      <center>
      <div class="tab" width=90px >
        <button id='btngrupo'      class="tablinks" onclick="openTab(event, 'TABG')">GRUPOS</button>
        <button id='btnformulario' class="tablinks" onclick="openTab(event, 'TABF')">FORMULARIOS</button>
      </div>
      
      <div id="TABG" class="tabcontent">
        <br>
        <fieldset>    
          <table id='tblencagrupo' width='900px'>
            <tr class='encabezadoTabla'>
            <td colspan=2 align='center' height="20px">GRUPOS</td>
            </tr>
            <tr >
                <td class='fila1'><b>Descripci&oacute;n</b></td>
                <td class='fila2'>&nbsp;&nbsp;               
                     <input id='txtdescripcion' name='txtdescripcion' class='form-control frmgrupo' valida='val1' size='80px' readonly>
                </td>
            </tr>
            <tr>
                <td class='fila1'><b>Orden</b></td>
                <td class='fila2'>&nbsp;&nbsp;               
                     <input id='txtorden' name='txtorden' type="number" class='form-control frmgrupo' valida='val1' value='1' min="1" readonly style='width: 60px;' onkeypress='return justNumbers(event);'>
                </td>
            </tr>
            <tr>
                <td class='fila1'><b>Numero de Columnas</b></td>
                <td class='fila2'>&nbsp;&nbsp;               
                     <input id='txtnumcolumna' name='txtnumcolumna' type="number" class='form-control frmgrupo' valida='prim' value='1' readonly onkeypress='return justNumbers(event);' style='width: 60px;'>
                </td>
            </tr>
            <tr>
                <td class='fila1'><b>Tipo</b></td>
                <td class='fila2'>&nbsp;&nbsp;
                     <select id='seltipo' name='seltipo' class='form-control frmgrupo'  valida='val1' readonly style='width: 200px;'>
                          <option value=''></option>
                          <option value='F'>Formulario</option>
                          <option value='R'>Reporte</option>
                     </select>
                </td>
            </tr>
            <tr>
                <td class='fila1'><b>Grupo</b></td>
                <td class='fila2'>&nbsp;&nbsp;
                     <select id='selgrupo' name='selgrupo' class='form-control frmgrupo'  valida='val1' readonly style='width: 200px;'>
                          <option value=''></option>
                          <option value='G'>Grupo</option>
                          <option value='S'>Subgrupo</option>
                     </select>
                </td>
            </tr>
          </table>
          <br>
          <input type='button' id='btnIniciar'  name='btnIniciar'   class='button'   value='Iniciar'   onclick='InicializarGeneral(1)'>
           &nbsp;&nbsp;<input type='button' id='btnGrabar'   name='btnGrabar'    class='button2' value='Grabar'    onclick='GrabarGrupo()' disabled>   
          &nbsp;&nbsp;<input type='button' id='btnEliminar'  name='btnEliminar'  class='button'   value='Eliminar' onclick='EliminarGeneral(1)'>     
          <br>
          <table align='left' id='tblbuscar' name='tblbuscar'>
              <tr><td><font size=2>Filtrar listado:&nbsp;</font></td><td><input id="id_search_grupos" name="id_search_grupos" type="text" class='form-control' value="" size="25"  placeholder="Buscar en listado"></td>
              </tr>
          </table>
          <br><br>         
          <div style="max-height: 280px">
             <table class="table table-condensed table-curved table-hover" id="tblgrupo" class="tblgrupo" width="100%" cellspacing="0">
             <thead>
                <tr class='encabezadoTabla'>
                    <th style="display:none;">Fila</th>
                    <th style="text-align:center;">Orden</th>
                    <th style="text-align:center;">Descripcion</th>
                    <th style="text-align:center;">Nro columnas</th>
                    <th style="text-align:center;">Tipo</th>
                    <th style="text-align:center;">Grupo</th>
                    <th style="text-align:center;">Seleccionar</th>
                </tr>
             </thead>
             <tbody>               
             <?php
                $cfila='fila1';
                $item =' item';

                foreach( $arr_grupos as $key => $val){
                   
                      $cfila == 'fila1' ? $cfila = "fila2" : $cfila = "fila2";                      
                      
                      $deta  = "<tr id=".$val["id_grupo"]." class='".$cfila. $item."' ondblclick='EditarGeneral(this,1,\"".$val['id_grupo']."\")'>";
                      $deta .= '<td align="center">'.$val['orden_grupo'].'</td>';
                      $deta .= '<td align="center">'.$val['descrip_grupo'].'</td>';
                      $deta .= '<td align="center">'.$val['columnas_grupo'].'</td>';
                      $deta .= '<td align="center">'.$val['tipo_grupo'].'</td>';
                      $deta .= '<td align="center">'.$val['clasi_grupo'].'</td>';
                      $deta .= "<td align='center'><input type='checkbox' id='chkseleccion1' class='chkseleccion1' value='".$val['id_grupo']."' ></td>";
                      $deta .= '</tr>';

                      echo $deta;
                }
              ?>
             </tbody>
           </table>
       </div>
       </fieldset>
      </div>

      <div id="TABF" class="tabcontent">
        <br>
        <fieldset>   
          <table  id='tblencagrupo' width='800px' height='20px' >
            <tr class='encabezadoTabla'>
                <td colspan=2 align='center' height="20px">FORMULARIOS</td>
            </tr>
            <tr>
                <td class='fila1'><b>Descripci&oacute;n</b></td>
                <td class='fila2'>&nbsp;               
                     <input id='txtdescriformulario' name='txtdescriformulario' class='form-control frmgrupo' valida='valf2' readonly style='width: 600px;' >
                </td>
            </tr>
            <tr>
                <td class='fila1'><b>Grupo</b></td>
                <td class='fila2'>&nbsp;
                     <select id='selformulario' name='selformulario' class='form-control frmgrupo' valida='valf2' readonly style='width: 600px;'>
                        <option></option>
                        <?php
                            foreach( $arr_Formulario as $key => $val){
                              echo '<option value="' . $key .'">'.$val.'</option>';
                            }
                        ?>
                     </select>
                </td>
            </tr>
            <tr>
                <td class='fila1'><b>Orden</b></td>
                <td class='fila2'>&nbsp;&nbsp;               
                     <input id='txtordenformulario' name='txtordenformulario' type="number" class='form-control frmgrupo' valida='uno' value='1' readonly style='width: 60px;' onkeypress='return justNumbers(event);' onchange='VerificarOrden(this,2)'>
                </td>
            </tr>
            <tr>
                <td class='fila1'><b>Formulario</b></td>
                <td class='fila2'>&nbsp;               
                     <input id='txtcodigoformulario' name='txtcodigoformulario' class='form-control frmgrupo' valida='valf2'  readonly style='width: 100px;' onchange='SeleccionarCampos(this.value)'>
                </td>
            </tr>   
            <tr>
                <td class='fila1'><b>Campo</b></td>
                <td class='fila2'>&nbsp;               
                     <input id='txtcampoformulario' name='txtcampoformulario' class='form-control frmgrupo' valida='valf2' readonly style='width: 100px;'>
                </td>
            </tr>
          </table>
          <br>
          <input type='button' id='btnIniciarfor'  name='btnIniciar'   class='button'   value='Iniciar'   onclick='InicializarGeneral(2)'>
           &nbsp;&nbsp;<input type='button' id='btnGrabarFor'   name='btnGrabarFor'   class='button2'   value='Grabar'   onclick='GrabarFormularioRep("#TABF",1)'>
           &nbsp;&nbsp;<input type='button' id='btnEliminarFor'  name='btnEliminarFor'   class='button'   value='Eliminar'   onclick='EliminarGeneral(2)'>  
            <br>        
              <table align='left' id='tblbuscar' name='tblbuscar' >
                  <tr><td><font size=2>Filtrar listado:&nbsp;</font></td><td><input id="id_search_formularios" type="text" value="" class='form-control' size="20" name="id_search_formularios" placeholder="Buscar en listado"></td>
                  </tr>
              </table>
              <br><br>
              <div style="overflow-y: auto;max-height: 320px">
              <table class="table table-condensed table-curved table-hover" id='tblformulario' width=100% >
                <thead>
                  <tr class='encabezadoTabla'>
                     <td style="text-align:center;">Orden</td>
                     <td style="text-align:center;">Descripci&oacute;n</td>
                     <td style="text-align:center;">Grupo</td>
                     <td style="text-align:center;">Formulario</td>
                     <td style="text-align:center;">Campo</td>
                     <td style="text-align:center;">Seleccionar</td>
                  </tr>
                </thead>
                <tbody>               
                 <?php
                    $cfila='fila1';

                    foreach( $arr_genformulario as $key => $val){
                       
                          $cfila == 'fila1' ? $cfila = "fila2" : $cfila = "fila2";
                          
                          $deta  = "<tr id=".$val["id_formulario"]." class=".$cfila." ondblclick='EditarGeneral(this,2,\"".$val['id_formulario']."\")'>";
                          $deta .= '<td align="center">'.$val['orden_formulario'].'</td>';
                          $deta .= '<td align="center">'.($val['descrip_formulario']).'</td>';
                          $deta .= '<td align="center">'.$val['grupo_formulario'].'</td>';
                          $deta .= '<td align="center">'.$val['codigo_formulario'].'</td>';
                          $deta .= '<td align="center">'.$val['campo_formulario'].'</td>';
                          $deta .= "<td align='center'><input type='checkbox' id='chkseleccion2' class='chkseleccion2' value='".$val['id_formulario']."' ></td>";
                          $deta .= '</tr>';
                          
                          echo $deta;
                    }
                  ?>
              </tbody>
              </table>
           </div>   
        </fieldset>
      </div>

      <div id="TABR" class="tabcontent" style='display:none'>
        <br>
        <fieldset>    
          <table  id='tblencareporte' style='height: 120px;display:none'>
            <tr class='encabezadoTabla'>
                <td colspan=2 align='center' height="20px">REPORTES</td>
            </tr>
            <tr>
                <td class='fila1'><b>Descripci&oacute;n</b></td>
                <td class='fila2'>           
                     <input id='txtdescrireporte' name='txtdescrireporte' class='form-control' valida='valr3' readonly style='width: 800px;'>
                </td>
            </tr>
            <tr>
                <td class='fila1'><b>Grupo</b></td>
                <td class='fila2'>             
                     <select id='selreporte' name='selreporte' class='form-control' valida='valr3' readonly style='width: 200px;' >
                        <option></option>
                        <?php
                            foreach( $arr_Reporte as $key => $val){
                              echo '<option value="' . $key .'">'.$val.'</option>';
                            }
                        ?>
                     </select>
                </td>
            </tr>
            <tr>
                <td class='fila1'><b>Orden</b></td>
                <td class='fila2'>              
                     <input id='txtordenreporte' name='txtordenreporte' type="number" class='form-control' valida='uno' value='1' readonly style='width: 100px;' onkeypress='return justNumbers(event);' onchange='VerificarOrden(this,3)'>
                </td>
            </tr>
            <tr>
                <td class='fila1'><b>URL</b></td>
                <td class='fila2'>        
                     <input id='txturlreporte' name='txturlreporte'  class='form-control' valida='valr3' readonly style='width: 800px;'>
                </td>
            </tr>
          </table>
          <br>
          <input type='button' id='btnIniciarep'  name='btnIniciar'   class='button'   value='Iniciar'   onclick='InicializarGeneral(3)'>
           &nbsp;&nbsp;
          <input type='button' id='btnGrabarRep'   name='btnGrabarRep'   class='button2'   value='Grabar'   onclick='GrabarFormularioRep("#TABR",2)'>
           &nbsp;&nbsp;<input type='button' id='btnEliminaRep'  name='btnEliminaRep'   class='button'   value='Eliminar' onclick='EliminarGeneral(3)'>
          <br><br>          
          <table align='left' id='tblbuscar' name='tblbuscar'>
              <tr><td><font size=2>Filtrar listado:&nbsp;</font></td><td><input id="id_search_reportes" type="text" value="" size="20" class='form-control' name="id_search_reportes" placeholder="Buscar en listado"></td>
              </tr>
          </table>
          <br><br>
          <div style="overflow-y: auto;max-height: 320px">
          <table class="table table-condensed table-curved table-hover"  id='tblreporte' width=100%  cellspacing="0">
            <thead>
              <tr class='encabezadoTabla'>
                 <td style="text-align:center;">Orden</td>
                 <td style="text-align:center;">Descripcion</td>
                 <td style="text-align:center;">Grupo</td>
                 <td style="text-align:center;">URL</td>
                 <td style="text-align:center;">Seleccionar</td>
              </tr>
            </thead>
            <tbody>               
             <?php
                $cfila='fila1';

                foreach( $arr_genreporte as $key => $val){
                   
                      $cfila == 'fila1' ? $cfila = "fila2" : $cfila = "fila2";
                      
                      $deta  = "<tr id=".$val["id_reporte"]." class=".$cfila." ondblclick='EditarGeneral(this,3,\"".$val['id_reporte']."\")'>";
                      $deta .= '<td align="center">'.$val['orden_reporte'].'</td>';
                      $deta .= '<td align="center">'.($val['descrip_reporte']).'</td>';
                      $deta .= '<td align="center">'.$val['grupo_reporte'].'</td>';
                      $deta .= '<td align="center">'.$val['url_reporte'].'</td>';
                      $deta .= "<td align='center'><input type='checkbox' id='chkseleccion3' class='chkseleccion3' value='".$val['id_reporte']."' ></td>";
                      $deta .= '</tr>';
                      
                      echo $deta;
                }
             ?>
            </tbody>
          </table>
          </div>
        </fieldset>
      </div>
      <br> 
      <table>
        <tr>
            <td><input type='button' id='btnSalir'   name='btnSalir'   class='button'   value='Salir'   onclick='CerrarVentana()'></td>
        </tr>
      </table>
      </center>
      <table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;display:none;'>
        <tr><td class='fila2'>No hay registros para esta consulta</td></tr>
      </table>
      <div class="alert alert-info collapse" id="myAlert">
        <strong>Alerta</strong> Falta informaci&oacute;n
      </div>
      <input type="HIDDEN" name="widegrupo"      id="widegrupo"      value="0">
      <input type="HIDDEN" name="wideformulario" id="wideformulario" value="0">
      <input type="HIDDEN" name="widereporte"    id="widereporte"    value="0">
      <input type="HIDDEN" name="wedicion"       id="wedicion"       value="N">
      <input type="HIDDEN" name="wediformulario" id="wediformulario" value="N">
      <input type="HIDDEN" name="wedireporte"    id="wedireporte"    value="N">
    </body>   
    </html>