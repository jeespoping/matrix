<?php
include_once("conex.php");
 /************************************************************************************************************
 *
 * Programa	            :	Normograma
 * Fecha de Creación 	  :	2016-07-30
 * Autor				        :	Arleyda Insignares Ceballos
 * Descripcion          :	El normograma contiene las normas externas como leyes, decretos, acuerdos, circulares, 
 *                        resoluciones que afectan la gestión de la entidad.  Identificar las competencias, 
 *                        responsabilidades y funciones de las dependencias de la organización.
 *                      
 *                      - Tener en cuenta 6 parametros en la tabla root_000051: 
 *                      1.codigosnormograma: Contiene dos códigos, uno para definirlo como implementado, el
 *                        segundo es para definirlo como implementación parcial.                      
 *                      2.texto1normograma-texto5normograma: parametros para seleccionar los textos que componen 
 *                        el correo electrónico automático de notificación:  Asunto, contenido, firma.                    
 *                      
 *************************************    Modificaciones   ***************************************************
 * 2020-06-04  Arleyda Insignares C.
 *             - Se adiciona en el HTML <meta http-equiv="Content-type" content="text/html;charset=utf-8" />
 * 2020-04-17  Arleyda Insignares C.
 *             - Se adiciona envío de correo para tareas desde el diligenciamiento del correo.
 * 2017-04-19  Arleyda Insignares C.
 *             - Se agrega opción en la consulta de la Ficha de Evaluación para ver el archivo adjunto.
 *               El Formato permitido es: word, excel o PDF.
 * 2017-01-10  Arleyda Insignares C.
 *             1. Agregar todos los campos en la consulta ubicada en la parte superior del Formulario. 
 *             2. Identificar con colores el estado de una tarea. 
 *             3. Identificar con colores el estado de las normas. 
 *             4. Agregar en el listado tipo de documento, la opción jurisprudencia.
 *             5. Agregar las convenciones. 
 * 2017-02-07  Arleyda Insignares C.
 *             1. Cambiar campo 'Relacionado con' para no manejarlo con código, sino que sea de libre digitación
 *             2. El campo link debe abrir la url en la web al darle doble clic.
 *             3. Desencriptar la clave del correo en el momento que llama la función para enviar el correo.
 ************************************************************************************************************/


 if(!isset($_SESSION['user'])){
  	 echo "<center></br></br><table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
  		<tr><td>Error, inicie nuevamente</td></tr>
  		</table></center>";
  	 return;
 }

 header('Content-type: text/html;charset=ISO-8859-1');
 //header('Content-Type: text/html;charset=UTF-8');

  //********************************** Inicio  ***************************************************************   

  include_once("root/comun.php");

  $wbasedato     = consultarAliasPorAplicacion($conex, $wemp_pmla, 'juridica');
  $wbasetalhuma  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');  
  $arr_esi       = estimplementacion ($wbasedato,$conex,$wemp_pmla);
  $wfecha        = date("Y-m-d");
  $whora         = (string)date("H:i:s");
  $pos           = strpos($user,"-");
  $wusuario      = substr($user,$pos+1,strlen($user));
  $cod_nor       = consultarAliasPorAplicacion($conex, $wemp_pmla, 'codigosnormograma');

  list($arr_codi, $arr_codp) = explode('|', $cod_nor);    

  // ***************************************    FUNCIONES AJAX  Y PHP  *****************************************
     

      if (isset($_POST["accion"]) && $_POST["accion"] == "Cargarcon"){
        
          $resultado='';        

          $q = " SELECT A.Norcod, A.Nornum, A.Nordes, A.Norfem, A.Norfvi, A.Noreim,
                        A.Noreco, A.Norvig, A.Noruni, A.Norrel, A.Norurl, A.Nortdo,
                        A.Norest, B.Docdes, C.Eimdes, D.Nornum as 'Numrel',E.Unides,
                        F.Docdes as 'Desrel', G.Temdes, H.Emides, I.Estdes
                 From ".$wbasedato."_000008 A    
                        Inner Join ".$wbasedato."_000003 B on  A.Nortdo = B.Doccod
                        Inner Join ".$wbasedato."_000005 C on  A.Noreim = C.Eimcod
                        Inner Join root_000113 E  on A.Noruni = E.Unicod
                        Left Join  ".$wbasedato."_000008 D on  A.Norrel = D.Norcod               
                        Left Join  ".$wbasedato."_000003 F on  F.Doccod = D.Nortdo
                        Left Join  ".$wbasedato."_000004 H on  H.Emicod = A.Noreco
                        Left Join  ".$wbasedato."_000006 I on  I.Estcod = A.Norvig
                        Inner Join ".$wbasedato."_000013 G on  A.Nortem = G.Temcod
                 Where A.Norest ='on'  Order by A.Norfem desc";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        
        while($row = mysql_fetch_assoc($res)){

              $camporel = '';
              
              if ($row["Numrel"] != '')

                 $camporel = $row["Desrel"].'-'.$row["Numrel"];

              //Asignar color implementación

              switch ($row['Noreim']) {
                
                case $arr_codi:
                      $fondo = 'fondoImplementada';
                      break;
                case $arr_codp:
                      $fondo = 'fondoParcial';
                      break;
                default:
                      $fondo = 'fondoNoimplementada';
                      break;
              }  

              $resultado .= '<tr class="fila1 find" id='.$row["Norcod"].'-'.$row["Docdes"].
                        '-'.$row["Nornum"].' ondblclick="cargarnormograma(this)";>
                        <td align="center">'.$row["Docdes"].'</td>
                        <td align="center">'.$row["Nornum"].'</td>
                        <td align="center">'.$row["Norfem"].'</td>
                        <td align="center">'.$row["Norfvi"].'</td>  
                        <td align="center">'.$row["Emides"].'</td>  
                        <td>'.$row["Estdes"].'</td>
                        <td class='.$fondo.' align="center">'.$row["Eimdes"].'</td>
                        <td>'.$row["Unides"].'</td>                
                        <td>'.$row["Temdes"].'</td>
                        <td>'.$camporel.'</td>
                        <td style="display:none;">'.$row["Norcod"].'</td>
                        </tr>';

        }

        echo $resultado;
        return;
      }


      if (isset($_POST["accion"]) && $_POST["accion"] == "ValidarDocumento"){

         $busdocu = 'N'; 
         $q       = " Select Nornum From ".$wbasedato."_000008
                      WHERE  Nornum = '".$numedocu."' AND
                             Nortdo = '".$tipodocu."' AND
                             Norest = 'on' ";
         
         $res     = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         $num     = mysql_num_rows($res);
         
         if ($num > 0)
            {
             $row     = mysql_fetch_assoc($res);
             $busdocu = 'S';
            }
         
         echo $busdocu;
         return;
      }


      // Actualizar el contador superior por tipo de Tema
      if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarContador"){
          
          $contador ='';
          $j=0;

          foreach( $arr_esi as $key => $val){

              if ($wcodigo == '' || $wcodigo == 'Todos' || $wcodigo == '*')
              {   
                  $q    = "Select count(A.Noreim) as total, B.Eimdes, B.Eimcod From ".$wbasedato."_000008 A
                           Inner join ".$wbasedato."_000005 B
                           on A.Noreim = B.Eimcod
                           Where A.Norest='on' and A.Noreim = ".$key."
                           Group by A.Noreim ";  
              }
              else
              {
                  $q    = "Select count(A.Noreim) as total, B.Eimdes, B.Eimcod From ".$wbasedato."_000008 A
                           Inner join ".$wbasedato."_000005 B
                           on A.Noreim = B.Eimcod
                           Where A.Norest='on' and A.Nortem = ".$wcodigo." and A.Noreim = ".$key."
                           Group by A.Noreim ";          
              }

              $res      = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

              $num      = mysql_num_rows($res);
              
              if ($num > 0)
              {
                  while($row = mysql_fetch_assoc($res)){
                      $contador .= $row['Eimdes'].':  '.$row['total'].' & '.$row['Eimcod'].'|';
                  }
              } 
              else              
                  $contador .= $val.':  0 & '.$key.'|';
              
          }

          echo $contador;
          return;
      }


      // Actualizar la respuesta a las Tareas diligenciadas
      if (isset($_POST["accion"]) && $_POST["accion"] == "Grabarespuestanor"){
         
         $entrega = 0;
         $pendien = 0;
     
         // Recorrer el Array con las respuestas a las Tareas diligenciadas
         for ($x=1;$x<count($arrayName); $x++){ 
                 
                 list($codigo, $respu, $obser, $codnor, $tiponor) = explode('|', $arrayName[$x]);

                 if ($tiponor == 'V')
                 {
                     $q = "UPDATE ".$wbasedato."_000009
                           SET Tarres = '".$respu."',
                               Tarobr = '".$obser."',
                               Taresc = '".$westadop."',
                               Tarlei = 'on',
                               Fecha_data = '".$wfecha."',
                               Hora_data  = '".$whora."'
                           WHERE Id = '".$codigo."' " ;
                 }
                 else
                 {
                     $q = "UPDATE ".$wbasedato."_000009
                           SET Tarres = '".$respu."',
                               Tarobr = '".$obser."',
                               Tarlei = 'on',
                               Fecha_data = '".$wfecha."',
                               Hora_data  = '".$whora."'
                           WHERE Id = '".$codigo."' " ;
                 }  

                 $resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());        
         }


         // Se debe verificar todas las Tareas del Normograma para Actualizar el estado
         $q = " SELECT A.Tarcon, A.Tarcod, A.Taruni, A.Tarobs, A.Tarres, A.Tarobr, A.Taresc
                FROM ".$wbasedato."_000009 A 
                WHERE A.Tarcon = '".$codnor."' 
                  AND A.Tarest = 'on' " ;
        
         $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         
         $num = mysql_num_rows($res);
         
         if ($num > 0)
         {
             while($row = mysql_fetch_assoc($res)){
                 
                 if ($row["Taresc"] == $westadop)
                 
                    $entrega = $entrega + 1;
                 
                 else
                 
                    $pendien = $pendien + 1;
                 
             }
         }  

         // Verificar si hay entrega parcial en el Normograma
         if ($entrega > 0 && $pendien > 0)
         { 
             $q = " UPDATE ".$wbasedato."_000008
                      SET Noreim = '".$westadop."'
                    WHERE Norcod = '".$codnor."' 
                      AND Norest = 'on' " ;

             $resp1= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
             $resp .= '|02';
         }


         // Verificar cumplimiento total a las Tareas del Normograma
         if ($entrega > 0 && $pendien == 0)
         {  
             $q = " UPDATE ".$wbasedato."_000008
                      SET Noreim = '".$westadoi."'
                    WHERE Norcod = '".$codnor."'
                      AND Norest = 'on' " ;
                    
             $resp2= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

             $resp .= '|03';
         }


         // Cuando ninguna Tarea esté cumplida
         if ($entrega == 0 && $pendien > 0)
          
             $resp .= '|01';         
         
         echo $resp;     
         return;
      }

      
      //Consultar Tareas pendientes del Normograma seleccionado
      if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarTareasnor"){

          $tr_cores  = '<tr class="encabezadotabla">                        
                        <td align="center">Numero Normograma</td>
                        <td align="center">Fecha</td>
                        <td align="center">Unidad</td>
                        <td align="center">Tarea</td>
                        <td align="center">Respuesta</td>
                        <td align="center">Observacion</td>
                        <td align="center">Cumplida</td>';

          $q  = " SELECT A.Tarcon, A.Tarcod, A.Taruni, A.Tarfec, A.Tarobs, A.Tarres, A.Tarobr,
                         A.Id, A.Taresc, A.Tarlei, B.Nornum, C.Ecodes, D.Unides, D.Uniusu
                  From ".$wbasedato."_000009 A    
                        Inner Join ".$wbasedato."_000008 B on  A.Tarcon = B.Norcod
                        Inner Join ".$wbasedato."_000007 C on  A.Taresc = C.Ecocod
                        Inner Join root_000113 D on  A.Taruni = D.Unicod
                  where A.Tarest ='on' and  A.Tarcon='".$codigo_art."' and B.Norest='on' " ;
          
          $cont1    = 0;
          $fechares = '';
          
          if($res1 = mysql_query($q,$conex))
          {
            
            $num = mysql_num_rows($res1);     

            if ($num > 0){

                while($row = mysql_fetch_assoc($res1)){
                      
                      //Consultar Responsable de la Unidad
                      
                      $habilitado = ''; 
                      
                      if ($row["Taresc"] == $westadop){
                          $habilitado = 'disabled';
                      }    
                      else
                      {
                          $Conuni = "SELECT A.Tarcon, A.Taruni, A.Id, A.Taresc, A.Tarfec, B.Uniusu 
                                     From ".$wbasedato."_000009 A
                                            Inner Join root_000113 B on  A.Taruni = B.Unicod
                                     Where A.Id = '".$row["Id"]."' ";

                          $resuni = mysql_query($Conuni,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

                          $rowuni = mysql_fetch_assoc($resuni);

                          if ( $wcod_usu != $rowuni['Uniusu'] ){ 
                                $habilitado = 'disabled';
                          }
                          else{

                               // Actualizar La tarea como leida
                               $q = " UPDATE ".$wbasedato."_000009 
                                        SET Tarlei = 'on'
                                      WHERE Id     = ".$row["Id"]." 
                                        AND Tarlei = 'off' ";

                               $res2 = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());   

                          }

                          $fechares =  $wfecha - $rowuni['Tarfec'];

                      }

                      $cont1 % 2 == 0 ? $fondo = "fila1" : $fondo = "fila2";
                      $cont1++; 
                      $clase = "fila1";
                     
                      if ($row["Taresc"] == $westadop)
                      {
                         $rescumplida = '<input type="checkbox" id="chkcumplida" name="chkcumplida" checked '.$habilitado.'>';
                         $estares = 'S';
                      }
                      else 
                      {
                         $rescumplida = '<input type="checkbox" id="chkcumplida" name="chkcumplida" '.$habilitado.'>';
                         $estares = 'N';
                      }   

                      $segundos = strtotime($row["Tarfec"]) - strtotime($wfecha);
                      $difdias  = intval($segundos/60/60/24);

                      if ($row["Tarlei"] == 'off' && $row["Taresc"] !== $westadop) // Tarea no leída
                          $clase ='fondoAzul';       
                      else 
                          $clase = 'fondoVerde';   // Activa

                      if ($difdias <= 7 && $difdias >= 1 && $row["Taresc"] !== $westadop)   // Tarea proxima a vencerse 
                          $clase = "fondoAmarillo";
                      
                      if ($difdias < 1 && $row["Taresc"] !== $westadop) // Tarea Vencida
                          $clase = "fondoNaranja";

                      $tr_cores .= '<tr class="'.$fondo.'">                                    
                                    <td align="center">'.$row["Nornum"].'</td>
                                    <td class="'.$clase.'" align="center">'.$row["Tarfec"].'</td>
                                    <td align="center">'.utf8_decode($row["Unides"]).'</td>
                                    <td >'.utf8_decode($row["Tarobs"]).'</td>
                                    <td align="center">
                                    <textarea id="txtrespuesta" name"txtrespuesta" cols="40" rows="3" '.$habilitado.'>'
                                    .utf8_decode($row["Tarres"]).'</textarea></td>
                                    <td align="center">
                                    <textarea id="txtobseres" name"txtobseres" cols="40" rows="3" '.$habilitado.'>'
                                    .utf8_decode($row["Tarobr"]).'</textarea></td>
                                    <td align="center">'.$rescumplida.'</td>
                                    <td style="display:none;">'.$row["Id"].'</td>
                                    <td style="display:none;">'.$row["Tarcon"].'</td>
                                    <td style="display:none;">
                                    <input type="text" id="txtestcum" name="txtestcum" value ="'.$estares.'"></td>
                                    </tr>';
                                    
                }
                $tr_cores .= "<tr></tr><tr><td colspan='6' align='center'><input type='button' class='button' id='grarespu' name='grarespu' value='Grabar' onclick='Grabardivrespuesta();'> <input type='button' class='button' id='regrespu' name='regrespu' value='Regresar' onclick='Cerrardivrespuesta();'> </td></tr>";
            }
            else{
                $tr_cores = 'N';
            }
          }
          else
          {
             exit(mysql_errno()." - en el query: ".$q." - ".mysql_error());
             $tr_cores = 'N';
          }
          
          echo $tr_cores;
          return ;
      }


      if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarPermisos"){

         $vresultado = 'N';

         $q      = " Select Peruni,Perusu,Peradi,Pergra,Percon,Pereli From ".$wbasedato."_000011       
                     WHERE Perusu = '".$codigo_usu."' " ;
         
         $res    = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         $num    = mysql_num_rows($res);
         
         if ($num > 0)
         {
               $row        = mysql_fetch_assoc($res); 
               $vresultado = $row['Peradi'] . "|" . $row['Pergra'] . "|" . $row['Percon'] . "|" . $row['Pereli'];            
         } 
         else
         {
               $q = "  Select Unicod,Unides,Uniusu From root_000113
                       WHERE Uniusu = '".$codigo_usu."' and Uniest='on' " ;            

               $res    = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
               $num    = mysql_num_rows($res);
               if ($num > 0)
               {
                     $vresultado = 'S'; 
               }   
         }       

         echo $vresultado;
         return;
      }


      if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarFicha"){      
         
         $q   =  " Select Ficcod,Ficart,Ficdes,Ficaso,Ficarc,Ficobs,Ficest From ".$wbasedato."_000010       
                   WHERE Ficaso = '".$codigo_art."' 
                     AND Ficest = 'on' " ;
         
         $res    = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         $num    = mysql_num_rows($res);         
         $comusu = 0;

         if ($num > 0){

              $row        = mysql_fetch_assoc($res); 
              $path_info  = pathinfo($row['Ficarc']);
              $vresultado = $row['Ficcod'] . "|" . utf8_decode($row['Ficart']) . "|" . utf8_decode($row['Ficdes']) . "|" . utf8_decode($row['Ficobs']) . "|" . $row['Ficarc'] . "|" . $path_info['extension'];
         } 
         else{
              $vresultado = 'N';
         }   

         echo $vresultado;
         return; 
      }


      if (isset($_POST["accion"]) && $_POST["accion"] == "GrabarNormograma"){
         
         $fechauno  = date("Y-m-d", strtotime($wfecemi));
         $fechados  = date("Y-m-d", strtotime($wfecvig));
         $rescorreo = '';
         $unidescri = '';
         $uniemail  = '';
         $normades  = '';

         // * * * * * * * Seleccionar datos para Envio de Correo 
         $q      = " Select Temema,Temnom,Temusu,Temcla,Temres,Temcar From ".$wbasedato."_000013
                     WHERE Temcod = '".$wtemusu."' 
                       AND Temest = 'on' " ;
   
         $res    = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         $num    = mysql_num_rows($res);
         if ( $num > 0 )
         {
              $row       = mysql_fetch_assoc($res); 
              $Emailori  = $row['Temema'];
              $Emailnom  = $row['Temnom'];
              $Emailres  = $row['Temres'];
              $Emailcar  = $row['Temcar'];
              $Emailusu  = $row['Temusu'];
              $Emailcla  = base64_decode($row['Temcla']);

         }


         // * * * * * * * Seleccinar Descripcion del tipo de documento
         $q      = " Select Doccod,Docdes From ".$wbasedato."_000003
                     WHERE Doccod = '".$wtipdoc."' " ;
   
         $res    = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         $num    = mysql_num_rows($res);
         
         if ($num > 0)
         {
             $row      = mysql_fetch_assoc($res); 
             $normades = $row['Docdes'];
         }


         // * * * * * * * Seleccionar datos de la Unidad para envío del Email
         $q      = " Select Unicod,Unides,Uniema From root_000113
                     WHERE Unicod = '".$wunidad."' and Uniest='on' " ;
   
         $res    = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         $num    = mysql_num_rows($res);
         
         if ($num > 0)
         {
             $row       = mysql_fetch_assoc($res); 
             $uniemail  = $row['Uniema'];
             $unidescri = $row['Unides'];
         }

         if ($wnormoid == 0) // ***************     Grabar un nuevo Normograma    ********************
         {
             echo 'grabar nuevo paso 1';

             // * * * * * Selecionar el maximo codigo del Normograma para obtener consecutivo
             $q=" SELECT MAX(CAST(Norcod AS UNSIGNED)) as maximo From ".$wbasedato."_000008" ;
             $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
             $num = mysql_num_rows($res);

             if ($num > 0)
             {
                 $row         = mysql_fetch_assoc($res);
                 $vmaximo     = $row['maximo'];
                 $wmaxcodigo  = $vmaximo +1;
             }     


             // * * * * * Grabar el Normograma en la tabla juridica_000008
             $q = " INSERT INTO ".$wbasedato."_000008
                    (Medico,Fecha_data,Hora_data,Norcod,Nortdo,Nornum,Norfem,Norfvi,Noreco,Nordes,
                     Noreim,Norvig,Norrel,Norurl,Nortem,Norobs,Noruni,Norest,Seguridad) 
                     VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wmaxcodigo."','".$wtipdoc."'
                      ,'".$wnumnom."','".$fechauno."','".$fechados."','".$wemitido."','".utf8_encode($wdesart)."'
                      ,'".$westadoi."','".$westadon."','".utf8_encode($wcodrelacion)."','".$wurlart."','".$wtemusu."'
                      ,'".utf8_encode($wobsart)."','".$wunidad."','on','C-".$wusuario."') ";         
             
             $res2= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

             
             //************************      GRABAR DETALLE- Tareas Coresponsables       *********************
             
             foreach ($warruni as $codigo => $nombre) {

                 $arrdes = explode('|',$nombre);
                 $q      = "SELECT MAX(CAST(Tarcod AS UNSIGNED)) as maximo From ".$wbasedato."_000009" ;
                 $res    = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                 $num    = mysql_num_rows($res);

                 if ($num > 0){
                     $row         = mysql_fetch_assoc($res);
                     $vmaximo     = $row['maximo'];
                     $wmaxcodres  = $vmaximo +1;
                 }   

                 if ($arrdes[0] != ''){  
                
                     $q  = " INSERT INTO ".$wbasedato."_000009
                                (Medico,Fecha_data,Hora_data,Tarcon,Tarcod,Taruni,Tarfec,Tarobs,Taresc,Tarest,Seguridad) 
                                VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wmaxcodigo."','".$wmaxcodres."','".$arrdes[3]."',
                                '".$arrdes[1]."','".$arrdes[0]."','01','on','C-".$wusuario."') ";


                     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error()); 

                     //enviar Email tarea
                     list($descriptar, $fechatar, $ide, $codigo) = explode('|', $nombre);

                     enviarEmailTarea($conex,$codigo,$wtexto1nor,$wtexto3nor,$wtexto4nor,$wtexto5nor,$normades,$wnumnom,$Emailres,$Emailcar,$Emailori,$Emailcla,$Emailnom,$descriptar);
                 }

             }
           
             // Enviar Email con la clase phpmailer utilizando la funcion SendToEmail de la libreria comun
             if ($uniemail != '')
             {

                 $Emaildesti   =  $uniemail;
                 $Contenido    =  $wtexto1nor.' '.$normades.' - '.$wnumnom.' '.$wtexto2nor.'<br><br><br>'.$wtexto5nor.'<br><br>'. $Emailres.'<br>'. $Emailcar ;
         
                 $Nomdestino   =  $row['nomjefe'];
                 $ArrayOrigen  = array('email'    => $Emailori,
                                      'password' => $Emailcla,
                                      'from'     => '',
                                      'fromName' => $Emailnom);

                 $ArrayDestino = array($Emaildesti);
                 $wasunto      = $wtexto3nor;

                 sendToEmail(utf8_decode($wasunto),utf8_decode($Contenido),utf8_decode($Contenido),$ArrayOrigen,$ArrayDestino);
                
             } 
         }
         else //  * * * * * Modificar el Normograma
         {
             
             // ****************************      MODIFICAR NORMOGRAMA     ********************************
             $qmod  =" UPDATE ".$wbasedato."_000008
                        SET Nortdo = '".$wtipdoc."',
                            Norfem = '".$fechauno."',
                            Norfvi = '".$fechados."',
                            Noreco = '".$wemitido."',
                            Nordes = '".($wdesart)."',
                            Noreim = '".$westadoi."',
                            Norvig = '".$westadon."',
                            Norrel = '".($wcodrelacion)."',
                            Norurl = '".$wurlart."',
                            Nortem = '".$wtemusu."',
                            Norobs = '".($wobsart)."',
                            Noruni = '".$wunidad."',
                            Fecha_data = '".$wfecha."',
                            Hora_data  = '".$whora."'
                        WHERE Id = '".$wnormoid."' " ; 

             $res2= mysql_query($qmod,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                          
             // Grabar detalle- Tareas coresponsables 
             for ($x=0;$x<=count($warruni); $x++){

                 list($descriptar, $fechatar, $ide, $codigo) = explode('|', $warruni[$x]);

                 $q      = "SELECT MAX(CAST(Tarcod AS UNSIGNED)) as maximo From ".$wbasedato."_000009" ;
                 $res    = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                 $num    = mysql_num_rows($res);
                 
                 if ($num > 0)
                 {
                     $row         = mysql_fetch_assoc($res);
                     $vmaximo     = $row['maximo'];
                     $wmaxcodres  = $vmaximo +1;
                 }     

                 if ( $ide==0 && $descriptar != '' )
                 {
                       $q   = " INSERT INTO ".$wbasedato."_000009
                               (Medico,Fecha_data,Hora_data,Tarcon,Tarcod,Taruni,Tarfec,Tarobs,Taresc,Tarest,Seguridad) 
                               VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wcoddocu."','".$wmaxcodres."','".$codigo."',
                               '".$fechatar."','".$descriptar."','01','on','C-".$wusuario."') ";

                       $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                       
                       // Enviar Email de notificacion con la Tarea correspondiente
                       if ($uniemail != '')
                       {

                           enviarEmailTarea($conex,$codigo,$wtexto1nor,$wtexto3nor,$wtexto4nor,$wtexto5nor,$normades,$wnumnom,$Emailres,$Emailcar,$Emailori,$Emailcla,$Emailnom,$descriptar);

        

                       }
                }
             }    
         }
         
         echo $res2;
         return;           
      }


     // Consultar Tareas coresponsables para el Normograma seleccionado
     if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarTareas"){

         $q=" SELECT A.Tarcon, A.Tarcod, A.Taruni, A.Tarobs, A.Tarres, A.Tarobr, A.Taresc, B.Ecodes, C.Unides
              FROM ".$wbasedato."_000009 A 
                    Inner join ".$wbasedato."_000007 B on A.Taresc = B.Ecocod
                    Inner join root_000113 C on A.Taruni = C.Unicod
              WHERE A.Tarcon = '".$codigo_art."' and A.Tarest='on' " ;

         $res= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         
         $num = mysql_num_rows($res);

         if ($num > 0)
         {
              while($row = mysql_fetch_assoc($res))
              {
                 $vresultado = $row['Taruni'] . "|" . $row['Unides'] . "|" . $row['Tarobs'] . "|" 
                            .  $row['Tarres'] . "|" . $row['Tarobr'] . "|" . $row['Ecodes'];
              } 
         }

         echo $vresultado;
         return;
      }


      // Inactivar Normograma
      if (isset($_POST["accion"]) && $_POST["accion"] == "AnularNormograma"){  

         $q      = " Select Norcod, Seguridad From ".$wbasedato."_000008             
                     WHERE Norcod = '".$codigo_art."' " ;
         
         $res    = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         $num    = mysql_num_rows($res);
         $comusu = 0;

         if ( $num > 0 ){
              $row        = mysql_fetch_assoc($res); 
              $userbd     = substr($row['Seguridad'],2,10);
              
              if ($codigo_usu == $userbd)
                  $comusu = 1;
              
         }   

         if ($comusu == 0){
             echo 0;
         }         
         else{  
             $q  =" UPDATE ".$wbasedato."_000008
                  SET Norest = 'off'
                  WHERE Norcod = '".$codigo_art."' " ;
             $resp= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
             echo $resp;   
         }
         return;         
      }


     // Consultar normograma para cargar el Formulario
     if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarNormograma"){
         
         $respuesta = array();
         $vanulado  = 'off';

         $q=" SELECT A.Norcod, A.Nortdo, A.Nornum, A.Norfem, A.Norfvi, 
                     A.Noreco, B.Unides, A.Nordes,A.Noreim, A.Norurl, A.Norobs,   
                     A.Noruni, A.Norrel, A.Norest, A.Norvig, A.Nortem, A.Id 
              FROM ".$wbasedato."_000008 A 
                     Inner join root_000113 B on A.Noruni = B.Unicod
              WHERE A.Norcod = '".$codigo_art."' 
                AND A.Norest = 'on' " ;

         $res= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         
         $num = mysql_num_rows($res);

         if ($num > 0)
         {
             $row        = mysql_fetch_assoc($res);

             $vresultado =   $row['Norcod'] . "|" . $row['Nortdo'] . "|" 
                           . $row['Nornum'] . "|" . $row['Norfem'] . "|" 
                           . $row['Norfvi'] . "|" . $row['Noreco'] . "|" 
                           . ($row['Nordes']) . "|" . ($row['Noreim']) . "|" 
                           . $row['Norurl'] . "|" . ($row['Norobs']) . "|" 
                           . $row['Noruni'] . "|" . $row['Norest'] . "|" 
                           . $row['Id']. "|" . ($row['Unides']). "|"  
                           . $row['Nortem'] . "|" . $row['Norvig'] . "|"
                           . ($row['Norrel']);

             $vanulado   = $row['Norest'];            
         }

         $respuesta['vresultado'] = $vresultado;

         $arr_cores = array();

         $fechares = '';

         //Consultar info corresponsables
         $q=" SELECT A.Tarcon, A.Tarcod, A.Taruni, A.Tarfec, A.Tarobs, A.Tarres,
                     A.Tarobr, A.Taresc, A.Id, A.Tarlei, B.Unides, C.Ecodes 
              From ".$wbasedato."_000009 A 
                     Inner Join root_000113 B  on A.Taruni = B.Unicod
                     Inner join ".$wbasedato."_000007 C on A.Taresc = C.Ecocod
              Where A.Tarcon = '".$codigo_art."' 
                And A.Tarest='on' " ;
         
         $res= mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
         
         $tr_cores  = '';
         $cont1     = 0;
         while($row2 = mysql_fetch_assoc($res))
         {

            $cont1 % 2 == 0 ? $fondo = "fila1" : $fondo = "fila2";
            $cont1++; 

            $clase = "fila1";

            $segundos = strtotime($row2["Tarfec"]) - strtotime($wfecha);
            $difdias  = intval($segundos/60/60/24);

            if ($row2["Tarlei"]=='off' && $row2['Taresc'] !== $westadop)
                $clase = 'fondoAzul'; //No leida
            else 
                $clase = 'fondoVerde'; // Activa

            //Proxima a vencerse
            if ($difdias <= 7 && $difdias >= 1 && $row2['Taresc'] !== $westadop){                
                $clase = "fondoAmarillo";
            }    

            // Tarea Vencida
            if ($difdias < 1 && $row2["Taresc"] !== $westadop)
                $clase = "fondoNaranja";        
           

            $arr_cores[$row['Tarcod']] = array("cod_unidad"    => $row2['Taruni'],
                                               "nombre_unidad" => $row2['Unides'],
                                               "descripcion"   => utf8_encode($row2['Tarobs']),
                                               "fecha_pro"     => $row2['Tarfec'],
                                               "respuesta"     => utf8_encode($row2['Tarres']),
                                               "observa"       => utf8_encode($row2['Tarobr']),
                                               "estado"        => $row2['Ecodes'],
                                               "id"            => $row2['Id']);

            $tr_cores .= '<tr class="'.$fondo.'">
                          <td  align="center">' .$row2["Taruni"].'</td>
                          <td>'.$row2["Unides"].'</td>
                          <td>'.$row2["Tarobs"].'</td>
                          <td class="'.$clase.'" align="center">'.$row2["Tarfec"].'</td>
                          <td align="center">'.$row2["Tarres"].'</td>
                          <td align="center">'.$row2["Tarobr"].'</td>
                          <td align="center">'.$row2["Ecodes"].'</td>
                          <td style="display:none;">'.$row2["Id"].'</td>
                        </tr>';

         }
          
         $respuesta['arr_cores'] = json_encode($arr_cores);
         $respuesta['tr_cores']  = $tr_cores;
         $respuesta['anulado']   = $vanulado;
         echo json_encode($respuesta);
         return;            
      }

      function enviarEmailTarea($conex,$codigo,$wtexto1nor,$wtexto3nor,$wtexto4nor,$wtexto5nor,$normades,$wnumnom,$Emailres,$Emailcar,$Emailori,$Emailcla,$Emailnom,$descriptar)
      {

               echo ($descriptar);

               // * * * * * * * Seleccionar datos de la Unidad para envío del Email al coresponsable
               $q      = " Select Unicod,Unides,Uniema From root_000113
                           WHERE Unicod = '".$codigo."' and Uniest='on' " ;
         
               $res    = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
               $num    = mysql_num_rows($res);
               
               if ($num > 0)
               {
                   $row       = mysql_fetch_assoc($res); 
                   $uniematar = $row['Uniema'];
               }
               
               //* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *

               $Emaildesti   =  $uniematar;
               $Contenido    =  $wtexto1nor.' '.$normades.' - '.$wnumnom.' '.$wtexto4nor.' '.$descriptar.'<br><br><br>'.$wtexto5nor.'<br><br>'. $Emailres.'<br>'. $Emailcar;
         
               $Nomdestino   =  $row['nomjefe'];
               $ArrayOrigen  =  array('email'   => $Emailori,
                                      'password' => $Emailcla,
                                      'from'     => '',
                                      'fromName' => $Emailnom);

               $ArrayDestino =  array($Emaildesti);                            
               $wasunto      =  $wtexto3nor;

               sendToEmail(utf8_decode($wasunto),utf8_decode($Contenido),utf8_decode($Contenido),$ArrayOrigen,$ArrayDestino);
      }
 
      // Consultar los Temas Normativos
      function consultartemas($wbasedato,$conex,$wemp_pmla){
        
        $strtipvar = array();
        $q  = " SELECT Temcod, Temdes
                From ".$wbasedato."_000013 
                Where Temest ='on' ";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        
        while($row = mysql_fetch_assoc($res)){

               $strtipvar[$row['Temcod']] = $row['Temdes'];
        }

        return $strtipvar;

      }


      // Consultar los usuarios para el campo autocompletar
      function consultarUnidades($wbasedato,$conex,$wemp_pmla){
        
        $strtipvar = array();

        $q  = " SELECT Unicod, Unides
                From root_000113    
                Where Uniest ='on' ";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

        while($row = mysql_fetch_assoc($res)){

               $strtipvar[$row['Unicod']] = utf8_encode($row['Unides']);
        }

        return $strtipvar;
      }


      function consultarNormas($wbasedato,$conex,$wemp_pmla){ 
        
        $strtipvar = array();

        $q  = " SELECT A.Norcod, A.Nornum, A.Nordes, A.Nortdo, A.Noruni, B.Docdes, C.Unides 
                From ".$wbasedato."_000008 A    
                      Inner Join ".$wbasedato."_000003 B on  A.Nortdo = B.Doccod
                      Inner Join root_000113 C on  A.Noruni = C.Unicod
                where A.Norest ='on' ";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        
        while($row = mysql_fetch_assoc($res)){
             $strtipvar[$row['Norcod']] = $row['Docdes']. ' '. $row['Nornum'];
        }
        
        return $strtipvar;
      }

      //Consulta general para la busqueda rapida por una palabra clave
      function consultarNormasgral($wbasedato,$conex,$wemp_pmla,$tema){ 
        
        $arr_documentos = array();

        $q  = " SELECT A.Norcod, A.Nornum, A.Nordes, A.Norfem, A.Norfvi,
                       A.Nortdo, A.Noreco, A.Norvig, A.Noruni, A.Norrel,
                       A.Norurl, A.Norest, A.Noreim, B.Docdes, C.Eimdes, 
                       E.Unides, G.Temdes, H.Emides, I.Estdes
                From ".$wbasedato."_000008 A    
                      Inner Join ".$wbasedato."_000003 B on  A.Nortdo = B.Doccod
                      Inner Join ".$wbasedato."_000005 C on  A.Noreim = C.Eimcod
                      Inner Join root_000113 E  on A.Noruni = E.Unicod
                      Left Join  ".$wbasedato."_000004 H on  H.Emicod = A.Noreco
                      Left Join  ".$wbasedato."_000006 I on  I.Estcod = A.Norvig
                      Inner Join ".$wbasedato."_000013 G on  A.Nortem = G.Temcod
                Where A.Norest ='on' 
                Order by A.Norfem desc";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        while($row = mysql_fetch_assoc($res)){

               $arr_documentos[] = array(  "wcodigo"    => $row["Norcod"],
                                           "wtipo"      => $row["Docdes"],
                                           "wcodrel"    => $row["Norrel"],
                                           "wnumero"    => $row["Nornum"],
                                           "wfecha"     => $row["Norfem"],
                                           "wfechavi"   => $row["Norfvi"],
                                           "wemitido"   => $row["Emides"],
                                           "wunidad"    => $row["Unides"],
                                           "westadoi"   => $row["Eimdes"],
                                           "westadon"   => $row["Estdes"],
                                           "wurl"       => $row["Norurl"],
                                           "wnoreim"    => $row["Noreim"],
                                           "wtema"      => $row["Temdes"]);
        }
        
        return $arr_documentos;
      }


      function  consultarpendientes($wbasedato,$conex,$wemp_pmla,$wusuario){

        $arr_documentos = array();

        $q  = " SELECT A.Tarcon, A.Tarcod, A.Taruni, A.Tarfec, A.Tarobs, A.Tarres, A.Tarobr,
                       A.Id as 'Idetar', A.Tarlei, A.Taresc, B.Id as 'Idenor', B.Nornum, B.Norcod, 
                       B.Nortdo, C.Unides, C.Uniusu, D.Docdes
                From ".$wbasedato."_000009 A    
                       Inner Join ".$wbasedato."_000008 B on  A.Tarcon = B.Norcod
                       Inner Join root_000113 C on  A.Taruni = C.Unicod
                       Left Join ".$wbasedato."_000003 D on  D.Doccod = B.Nortdo
                where A.Tarest ='on' and A.Taresc='01' and  C.Uniusu='".$wusuario."'" ;

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $pos=0;
        while($row = mysql_fetch_assoc($res)){
              $pos++;
              $arr_documentos[] = array(   "wcodigo"   => $row["Tarcod"],
                                           "wfecha"    => $row["Tarfec"],
                                           "wnumero"   => $row["Docdes"].' '.$row["Nornum"],
                                           "wunidad"   => $row["Unides"],
                                           "wobser"    => $row["Tarobs"],
                                           "wobsres"   => $row["Tarobr"],
                                           "widetar"   => $row["Idetar"],
                                           "wtarlei"   => $row["Tarlei"],
                                           "wtarres"   => $row["Tarres"],
                                           "wtarfec"   => $row["Tarfec"],
                                           "widenor"   => $row["Norcod"] );
        }
        
        return $arr_documentos;
      }

      
      // Consultar el tema que le corresponde al usuario logueado
      function consultartemaxusuario($wbasedato,$conex,$wemp_pmla,$wusuario){

        $codtema ='';

        $q = " SELECT A.Pertem, B.Temdes From ".$wbasedato."_000011 A
               Inner Join  ".$wbasedato."_000013 B
                     on A.Pertem = B.Temcod
               Where Perusu = '".$wusuario."'";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        
        $row = mysql_fetch_assoc($res);        
        $codtema = $row['Pertem'] .'|'. $row['Temdes'];
        
        return $codtema;
      }

        
      // Consultar los usuarios para el campo autocompletar
      function consultarUsuarios($wbasedato,$conex,$wemp_pmla){
        $strtipvar = array();
        
        $q  = " SELECT codigo, descripcion
                From usuarios A 
                Where activo ='on' ";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        
        while($row = mysql_fetch_assoc($res)){

               $strtipvar[$row['codigo']] = utf8_encode($row['descripcion']);
        }

        return $strtipvar;
      }


      function tipodocumento($wbasedato,$conex,$wemp_pmla){
        
        $strtipvar = array();
       
        $q  = " SELECT Doccod, Docdes
                From ".$wbasedato."_000003 ";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        
        while($row = mysql_fetch_assoc($res)){

              $strtipvar[$row['Doccod']] = utf8_encode($row['Docdes']);
        }

        return $strtipvar;
      }


      function instituemite($wbasedato,$conex,$wemp_pmla){
        
        $strtipvar = array();
        
        $q  = " SELECT Emicod, Emides
                From ".$wbasedato."_000004 Order By Emides";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        
        while($row = mysql_fetch_assoc($res)){
               
               $strtipvar[$row['Emicod']] = $row['Emides'];
        }
        
        return $strtipvar;
      }


      function estimplementacion($wbasedato,$conex,$wemp_pmla){          
        
        $strtipvar = array();
        
        $q  = " SELECT Eimcod, Eimdes
                From ".$wbasedato."_000005 ";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        
        while($row = mysql_fetch_assoc($res)){
               
               $strtipvar[$row['Eimcod']] = utf8_encode($row['Eimdes']);
        }
        
        return $strtipvar;
      }


      function estadonormograma($wbasedato,$conex,$wemp_pmla){          
        
        $strtipvar = array();
        
        $q  = " SELECT Estcod, Estdes
                From ".$wbasedato."_000006 ";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        
        while($row = mysql_fetch_assoc($res)){
               
              $strtipvar[$row['Estcod']] = utf8_encode($row['Estdes']);
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
                     $strtipvar[$row['codigo']] = $row['nombre'];
                 }
            }

      return $strtipvar;
      }

     
     // *****************************************         FIN PHP         ********************************************

	?>
 <!DOCTYPE html>
  <html lang="es-ES">
  <head>
		<title>Normograma</title>
		<meta http-equiv="Content-type" content="text/html;charset=utf-8" />
		<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
		<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" /></script>
    <script type="text/javascript" src="../../../include/root/jqueryalert.js"></script>
    <script type='text/javascript' src='../../../include/root/jquery.quicksearch.js'></script>
		<script type="text/javascript">
		  $(document).ready(function(){

        $("#txtfecemi,#txtfecvig").datepicker({
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

         // Totalizar implementadas, no implementadas y parciales
         var wcodtema= $("#wcodtema").val();
         ConsultarContador('*');
         $("#opttemagral").val('*'); 

         // Asignar la función quicksearch y que busque por el tema predeterminado       
         $('input#id_search_normograma').quicksearch('table#tabla_documentos tbody tr');
         $('input#id_search_tema').quicksearch('table#tabla_documentos tbody tr');

         $('input#id_search_tema').keyup(function(){
            var x = ($("#opttemagral").val() == '*') ? '':$("#opttemagral>option:selected").html();
            //$('input#id_search_tema').val(x); //Activar en caso de filtro inicial por Tema
         });


      //  *****************************      Asignar busqueda Autocompletar CoResponsable    ************************
      
      $("#divficha").css("overflow", "hidden");

      $("#divficha").dialog({
          autoOpen: false,
          top: 100,
          height: "auto",
          width: 1200,
          position: ['left+20', 'top+30'],
          modal: true
      }); 

  
      $("#divAcceso").dialog({
          autoOpen: false,
          closeOnEscape: false,                
          top: 100,
          height: 200,
          width: 300,
          position: ['left+250', 'top+80'],
          modal: true
      });


      $("#divAcceso").dialog("widget").find(".ui-dialog-titlebar").hide();

      $("#divrespuesta").dialog({
          autoOpen: false,
          top: 10,
          height: 520,
          width: 1200,
          position: ['left+25', 'top+50'],
          modal: true
      }); 

      // Leer el Array de Tareas pendientes para verificar si se mostrará el div 
      var arraytar = $("#arr_pen").val();      
      
      if (arraytar.length > 2)        
         $("#divrespuesta").dialog("open");
      
      var wemp_pmla = $("#wemp_pmla").val();
      var wcod_usu  = $("#wusuario").val();
      
      $.post("normograma.php",
      {
         consultaAjax:   true,
         accion      :   'ConsultarPermisos',
         wemp_pmla   :   wemp_pmla,
         codigo_usu  :   wcod_usu
      }, function(respuesta){

            if  (respuesta == 'N'){
                $("#divAcceso").dialog("open");
            }  
            else{
                if  (respuesta == 'S'){
                    $("#wgrabar").val('off');
                    $("#weditar").val('off');
                    $("#weliminar").val('off'); 
                    $("#wconsultar").val('on');
                }
                else{  
                    // Habilitar campos
                    //$("#opttipo,#txtfecemi,#txtfecvig,#txtnumnom,#txturlnor,#autcodres,#txtcodrela,#optestadoi,#optemitido,#optestadon,#opttema,#autcoduni").attr("disabled", false);
                    //$("#opttipo,#txtfecemi,#txtfecvig,#txtnumnom,#txturlnor,#autcodres,#txtcodrela,#optestadoi,#optemitido,#optestadon,#opttema,#autcoduni").attr("readonly", false);
                    //$("textarea").attr("readonly", false);
                    //$("select").attr("disabled", false);
                    //$("textarea").attr("disabled", false);                  

                    // Habilitar opción según perfil
                    vresul = respuesta.split('|');
                    $("#wgrabar").val(vresul[1]);
                    $("#weditar").val(vresul[1]);
                    $("#weliminar").val(vresul[3]);
                    $("#wconsultar").val(vresul[2]);
                    
                    //Si no tiene acceso a la función de grabado.
                    if  (vresul[1]=='off'){
                        $("#txtnumnom,#txtfecemi,#txtfecvig,#txturlnor,#autcodres,#txtcodrela,#tardesart,#tarobsart").attr("readonly", true);
                        $("#txtnumnom,#txtfecemi,#txtfecvig,#txturlnor,#autcodres,#txtcodrela,#tardesart,#tarobsart").css("background-color", "#f0f0f0");
                        $("select").attr("disabled", true);
                        $("select").css("background-color", "#f0f0f0");                      
                    } 
                }
          }
      });

      $("#opttipo,#txtfecemi,#txtfecvig,#txtnumnom,#txturlnor,#autcodres,#txtcodrela,#optestadoi,#optemitido,#optestadon,#opttema,#autcoduni,#tardesart,#tarobsart").attr("disabled", true).css("background-color","#FFFFFF");
      $("#opttipo,#txtfecemi,#txtfecvig,#txtnumnom,#txturlnor,#autcodres,#txtcodrela,#optestadoi,#optemitido,#optestadon,#opttema,#autcoduni,#tardesart,#tarobsart").attr("disabled", true).css("color","#000000");
      
      var arr_res  = eval('(' + $('#arr_res').val() + ')');
      var responsables = new Array();
      var index   = -1;

      for (var cod_res in arr_res)
        {
            index++;
            responsables[index]                = {};
            responsables[index].value          = cod_res;
            responsables[index].label          = cod_res+'-'+arr_res[cod_res];
            responsables[index].codigo         = cod_res+'-'+arr_res[cod_res];
        }            

        $("#autcoduni").autocomplete({
        source: responsables,         
        autoFocus: true,            
        select: function( event, ui ){
                var cod_sel = ui.item.codigo;
                $("#autcoduni").attr("codigo",cod_sel);
                 SeleccionCoresponsable(cod_sel);
            }
        });  

        $('#autcoduni').on({
        focusout: function(e) {
            if($(this).val().replace(/ /gi, "") == '')
            {
                $(this).val("");
                $(this).attr("codigo","");
                $(this).attr("nombre","");
            }
            else
            {
                $(this).val("");
                $(this).attr("codigo","");
                $(this).attr("nombre","");
            }
        }
        }); 

			
	     //  *****************************      Asignar busqueda Autocompletar Responsable    ************************
       var arr_res  = eval('(' + $('#arr_res').val() + ')');
       var responsables = new Array();
       var index   = -1;
       
       for (var cod_res in arr_res){
            index++;
            responsables[index]                = {};
            responsables[index].value          = cod_res+'-'+arr_res[cod_res];
            responsables[index].label          = cod_res+'-'+arr_res[cod_res];
            responsables[index].codigo         = cod_res;
            responsables[index].nombre         = arr_res[cod_res];
        }            

        $("#autcodres").autocomplete({
        source: responsables,         
        autoFocus: true,            
        select: function( event, ui ){

                $( "#autcodres" ).val(ui.item.nombre);
                $( "#autcodres").attr("nombre",ui.item.nombre);  
                $( "#wcodrespon" ).val(ui.item.codigo);
                  
                return false;
            }
        }); 


        $('#autcodres').on({
              focusout: function(e) { 

                  if (($(this).val() !== $(this).attr("nombre")) && $(this).val() !=='')
                     $(this).val($(this).attr("nombre"));

              }
        }); 

        
        Desactivar('1');
		});  // Finalizar Ready()

	      
        // **************************************   Inicio Funciones Javascript   ************************************************
        

        // Funcion para activar y desactivar los elementos del formulario
   		  function Desactivar(param){

          if (param == '1'){
        			$("#txtnumnom,#opttipo,#txtfecemi,#txtfecvig,#optemitido,#txturlnor,#optestadon,#autcodres,#opttema,#txtcodrela,#tardesart,#tarobsart").prop('disabled', true);

        			$("#txtnumnom,#opttipo,#txtfecemi,#txtfecvig,#optemitido,#txturlnor,#optestadon,#autcodres,#opttema,#txtcodrela,#tardesart,#tarobsart").prop('readonly', true);
              
          }else{
              $("#txtnumnom,#opttipo,#txtfecemi,#txtfecvig,#optemitido,#txturlnor,#optestadon,#autcodres,#opttema,#txtcodrela,#tardesart,#tarobsart,#autcoduni").prop('disabled', false);

              $("#txtnumnom,#opttipo,#txtfecemi,#txtfecvig,#optemitido,#txturlnor,#optestadon,#autcodres,#opttema,#txtcodrela,#tardesart,#tarobsart,#autcoduni").prop('readonly', false);
          }

		    }

        // Filtrar la tabla de busqueda segun el Tema seleccionado
        function SeleccionarTema(){

           var wcodtema = $("#opttemagral").val();
           ConsultarContador(wcodtema);
           if (wcodtema == '*')
           {
             $('input#id_search_tema').val('');
             $('input#id_search_tema').keyup();
           }
           else
           {
             var wcodtema2= $("#opttemagral option:selected").html();
             $('input#id_search_tema').val(wcodtema2);
             $('input#id_search_tema').keyup();
           }
        } 
        

        // Verificar que tipo de documento y numero de documento no se repitan      
        function ValidarDocumento(){

          var tipodocu = $("#opttipo").val();
          var numedocu = $("#txtnumnom").val();
          
          $.post("normograma.php",
            {
               consultaAjax:   true,
               accion:         'ValidarDocumento',
               wemp_pmla:      $("#wemp_pmla").val(),
               tipodocu:       tipodocu,
               numedocu:       numedocu
            }, function(respuesta){

               if (respuesta == 'S')
               {
                  alerta('Tipo y Numero de Documento no pueden duplicarse');
                  $("#txtnumnom").val('');
                  $("#txtnumnom").focus();  
                  return;
               }
           });
        }


        // Mostrar los datos del Normograma cuando le den doble click en la tabla superior de consulta
        function cargarnormograma(obj) {

            var id     = $(obj).attr('id');
            var divres = id.split('-');
            $("#wdocumento").val(divres[0]);
            var obj = document.getElementById("Consultar");
            obj.click();
        }


        // Mostrar paginas web
        function DireccionarUrl(obj){
            window.open(obj);
        }


        // Adicionar centro de costos y campo para diligenciar la Tarea solicitada en la tabla 'tblconsultar'
        function SeleccionCoresponsable(codigocor)
          {

              var wemp_pmla = $("#wemp_pmla").val();
              var vencoduni = '0';
              var vcodigo   = codigocor.split('-') ;
              var today     = new Date();
              var fechoy    = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
              var totfilas  = document.getElementById('tblconsultar').rows.length;
              if (totfilas > 2)
              {
                alerta('Debe diligenciar el Registro actual');
                return;
              }

              if ($("#txtnumnom").val()=='') 
              {
                alerta('Debe seleccionar un Documento');
                return;
              }


              if (vencoduni=='0')
              {
                 $("#autcoduni").val('');
                 respuesta = '<tr class="fila1"><td width="50">'+vcodigo[0]+'</td><td width="150">'+vcodigo[1]+'</td><td width="500" align=""><textarea id="tarcodrespon" name="tarcodrespon" cols=80 rows=2></textarea></td><td align="center"><input type="text" id="txtfechact" name="txtfechact" value='+fechoy+'>&nbsp;&nbsp;<input type="button" class="button" value="Adicionar" onclick="AdicionarUnidad(this);"><input type="button" class="button" value="Cancelar" onclick="CancelarTarea(this);"></td></tr>';
                 $("#tblconsultar").show();
                 $("#tblconsultar").append(respuesta);

                 $("#txtfechact").datepicker({
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
                  $("#autcoduni").val('');
              }
              else 
              {
                 alerta('El Coresponsable ya se encuentra seleccionado');
                 $("#autcoduni").val('');
              }
        }


        // Cuando se haya diligenciadp la tarea del Coresponsable esta funcion lo adicion a la tabla 'tbllistaunidad'
        function AdicionarUnidad(obj)
        {

          if ($("#wgrabar").val()=='off')
          { 
             alerta('El Usuario no tiene acceso');
             return;
          }  

          if ($("#tarcodrespon").val()=='')
          {
             alerta('Debe ingresar Descripci\u00F3n de la Tarea Solicitada');
             return;
          }

          var table    = document.getElementById('tblconsultar');
          var codcen   = table.rows[2].cells[0].innerHTML;
          var nomcen   = table.rows[2].cells[1].innerHTML;
          var feccen   = $("#txtfechact").val();
          var activi   = $("#tarcodrespon").val();

          respuesta='<tr class="fila1"><td width="50" align="center">'+codcen+'</td><td width="100">'+nomcen+
                    '</td><td width="150">'+activi+'</td><td class="fondoAzul" width="150" align="center">'+feccen+
                    '</td><td width="150"></td><td width="150"></td><td width="150" align="center">'+
                    'Pendiente </td><td style="display:none;">0</td><td style="display:none;">'+
                    '<input type="hidden" id ="txttareaid" name="txttareaid" value="0"> </td></tr>';

          $("#autcoduni").val('');
          $("#tbllistaunidad").show();
          $("#tbllistaunidad").append(respuesta);
          $('#tblconsultar tr:last').remove();
          
        }


        //Cancelar el registro para adicionar Tarea a la Unidad (Coresponsable)
        function CancelarTarea(obj)
        {
          var row = obj.parentNode.parentNode;
          row.parentNode.removeChild(row);
        }


        //Limpiar y habilitar todos los campos
        function Iniciar(){

          if ($("#wconsultar").val()=='off'){
             alerta('El Usuario no tiene acceso');
             return;
          }

          if ($("#wgrabar").val()=='off'){
             alerta('El Usuario no tiene acceso');
             return;
          }

          $("#txtfecemi").val('');
          $("#txtfecvig").val('');
          $("#txtnumnom").val('');
          $("#txturlnor").val('');
          $("#autcodres").val('');
          $("#txtcodrela").val('');
          $("#optestadoi").val('');
          $("#optemitido").val('');
          $("#optestadon").val('');
          $("#opttema").val('');
          $("#tardesart").val(''); 
          $("#tarobsart").val('');

          $("#wedicion").val('S');
          $("#wnormoide").val(0);
          $('#txtnumnom').focus();
          $("#tblconsultar  tbody ").remove(0);
          $("#tbllistaunidad  tbody ").remove(0);
          $("#tblconsultar").show();
          $("#tbllistaunidad").hide();
          $("#tbltarea").hide();

          $("input[type=text], textarea").attr("readonly", false);
          $("input[type=text], textarea").css("background-color", "white");
          $("#txtnumnom").attr("readonly", false);
          $("select").attr("disabled", false);
          $("select").css("background-color", "white");     
          
          Desactivar('2');		  
        }


        // Consulta normograma, puede consultarse por el campo de consulta o por la tabla superior con doble click
	      function Consultar(){

           var wemp_pmla  = $("#wemp_pmla").val();
           
           if ($("#wconsultar").val()=='off'){ 
              alerta('El Usuario no tiene acceso');
              return;
           }

           // Verificar que si esta en modo Edición, no se cancele sin autorización del Usuario
           if ($("#wedicion").val() == 'S')
           {               
              jConfirm("Desea Cancelar La Edici\u00F3n del documento Activo?","Confirmar", function(respuesta){
                  if (respuesta == true) {
                      Cargar(wemp_pmla);
                      
                      if ($("#optestadoi").val() !== '03')
                          Desactivar('2');
                  }else{
                      return;
                  }
              });
           }else{
              Cargar(wemp_pmla);
           }
        }

          
        function Cargar(wemp_pmla)
        {
             var codigo_art = $("#wdocumento").val();
             $("#wedicion").val('N');
             
             $.post("normograma.php",
               {
                consultaAjax:   true,
                accion:         'ConsultarNormograma',
                wemp_pmla:      wemp_pmla,
                westadop:       $("#westadop").val(),
                westadoi:       $("#westadoi").val(),     
                codigo_art:     codigo_art
               }, function(respuesta){
                                  
                 vcampos = respuesta.vresultado.split('|');
                 
                 if (vcampos[10]=='off')
                      alerta('El Documento se encuentra Anulado');

                  $("#tbltarea").show();     
                  $("#arr_cores").val(respuesta.arr_cores);
                  $("#tbllistaunidad  tbody ").remove(0);
                  $("#tbllistaunidad").show();
                  $("#tbllistaunidad").append(respuesta.tr_cores);
                  $("#opttipo").val(vcampos[1]);
                  $("#txtnumnom").val(vcampos[2]);
                  $("#txtfecemi").val(vcampos[3]);
                  $("#txtfecvig").val(vcampos[4]);
                  $("#optemitido").val(vcampos[5]);                                    
                  $("#tardesart").val(vcampos[6]);
                  $("#optestadoi").val(vcampos[7]);
                  $("#txturlnor").val(vcampos[8]); 
                  $("#tarobsart").val(vcampos[9]);
                  $("#wcodrespon").val(vcampos[10]);
                  $("#optestadon").val(vcampos[15]); 
                  $("#autcodres").val(vcampos[13]);  
                  $("#autcodres").attr("nombre",vcampos[13]); 
                  $("#txtcodrela").val(vcampos[16]); 
                  $("#opttema").val(vcampos[14]);                      
                  $("#wnormoide").val(vcampos[12]); 

                  $("#tblconsultar").show();
                  if( vcampos[7] == $("#westadop").val() || vcampos[7] == $("#westadoi").val() )
                  { 
                      $("input[type=text], textarea").attr("readonly", true);
                      $("input[type=text], textarea").css("background-color", "#f0f0f0");
                      $("select").attr("disabled", true);                      
                      $("select").css("background-color", "#f0f0f0"); 
                      $("#autcoduni").css("background-color", "white");
                      $("#autcoduni").attr("readonly", false);
                      
                      //Activar solo los campos de busqueda
                      $("#id_search_normograma").attr("readonly", false);
                      $("#opttemagral").attr("disabled", false);

                  }
                  else
                  {
                      if ($("#weditar").val()=='on'){
                         $("input[type=text], textarea").attr("readonly", false);
                         $("input[type=text], textarea").css("background-color", "white");                         
                         $("select").attr("disabled", false);
                         $("select").css("background-color", "white");
                      }
                  }

                  if ($("#optestadoi").val() !== '03'){
                      Desactivar('2');
                  }
                 
              },"json");

        }


        // Grabar normograma y coresponsables con Tareas asignadas
        function Grabar(){

          if ($("#weditar").val()=='off')
          {
             alerta('El Usuario no tiene acceso');
             return;
          }  

          if ($("#wgrabar").val()=='off')
          { 
             alerta('El Usuario no tiene acceso');
             return;
          }          

          var wemp_pmla  = $("#wemp_pmla").val();
          var wunidad    = $("#wcodrespon").val();
          var arrunidad  = new Array();        

          var table    = document.getElementById('tbllistaunidad');
          var colCount = document.getElementById('tbllistaunidad').rows[1].cells.length; 
          var rowCount = table.rows.length;

          $("#wedicion").val('N');

          if (rowCount>=2)
          {
              for( var i = 2; i < rowCount; i++ ){
                 vcodcel = table.rows[i].cells[0].innerHTML;
                 vdescel = table.rows[i].cells[2].innerHTML;
                 vfeccel = table.rows[i].cells[3].innerHTML;
                 videnor = table.rows[i].cells[7].innerHTML;
                 arrunidad[i-2] = vdescel+'|'+vfeccel+'|'+videnor+'|'+vcodcel;
              }                
          }
          
          // Verificar que el Usuario diligencie toda la información 
          if (  $("#txtnumnom").val()==''   || $("#txtfecemi").val()==''  
              || $("#txtfecvig").val()==''  || $("#optemitido").val()=='' 
              || $("#optestadon").val()=='' || $("#optestadoi").val()=='' 
              || $("#tardesart").val()==''  || $("#autcodres").val()=='' 
              || $("#opttipo").val()==''    || $("#opttema").val()=='' 
              || $("#wcodrespon").val()=='' ){
                alerta('Falta diligenciar informaci\u00F3n');
                return;
          }

          document.getElementById("divcargando").style.display   = "";
          
          $.post("normograma.php",
            { 
              consultaAjax:  true,
              accion    :  'GrabarNormograma',
              wemp_pmla :   wemp_pmla,
              wnumnom   :   $("#txtnumnom").val(),
              wtipdoc   :   $("#opttipo").val(),
              wfecemi   :   $("#txtfecemi").val(),
              wfecvig   :   $("#txtfecvig").val(),
              wemitido  :   $("#optemitido").val(),
              wcoddocu  :   $("#wdocumento").val(),
              westadon  :   $("#optestadon").val(),
              westadoi  :   $("#optestadoi").val(),
              wdesart   :   $("#tardesart").val(),
              wurlart   :   $("#txturlnor").val(),
              wnormoid  :   $("#wnormoide").val(),
              wtemusu   :   $("#opttema").val(),
              wcodrelacion: $("#txtcodrela").val(),
              wcodres   :   $("#wcodrespon").val(),
              wunidad   :   wunidad,
              wobsart   :   $("#tarobsart").val(),
              wtexto1nor:   $("#wtexto1nor").val(),
              wtexto2nor:   $("#wtexto2nor").val(),
              wtexto3nor:   $("#wtexto3nor").val(),
              wtexto4nor:   $("#wtexto4nor").val(),
              wtexto5nor:   $("#wtexto5nor").val(),
              warruni   :   arrunidad
            }, function(respuesta){

                var respu = respuesta.split('|');
                if (respu[0] == 1)
                   alerta('Grabado Exitoso');

                document.getElementById("divcargando").style.display   = "none";
                location.reload();
            });
        }        


        function Grabarrespuesta(){

         var respuarray = new Array();
         var conres = 0;
         var conrev = 0;
         
         // Validar que las respuestas cumplidas estén diligenciadas
         $('#tabla_tareas tr:gt(0)').each(function() {
           
            var chk  = $(this).find("#chkcumplida");
           
            if (chk !== null) {                          
                
                if (chk.prop('checked')){
                    
                    var respubus = $(this).find("#txtrespuesta").val();
                
                    if (respubus == ''){
                       conrev++;
                       jAlert('Debe diligenciar las opciones cumplidas');
                       return;
                    }
                }
            }
         });


         // Recorrer la tabla de respuestas para validar y grabar
         $('#tabla_tareas tr:gt(0)').each(function() {
         var chk  = $(this).find("#chkcumplida");

         if (chk !== null) {             
             
             if (chk.prop('checked')) {
             
                  if (resputar == ''){
                     jAlert('Faltan campos por diligenciar');
                     return;
                  }
                  conres++;
                  var ideres   = $(this).eq(0).find("td:eq(6)").text();
                  var idenor   = $(this).eq(0).find("td:eq(7)").text();
                  var resputar = $(this).find("#txtrespuesta");
                  
                  var respubus = resputar.val();
                  var resputar = $(this).find("#txtobseres");
                  var obserbus = resputar.val();
                  respuarray[conres] = ideres+'|'+respubus+'|'+obserbus+'|'+idenor+'|'+'V';
             }
             else{
                   var respubus = $(this).find("#txtrespuesta").val();
                   var obserbus = $(this).find("#txtobseres").val();
                   
                   if (respubus != '' && respubus != undefined)
                   {
                      conres++;
                      var ideres   = $(this).eq(0).find("td:eq(6)").text();
                      var idenor   = $(this).eq(0).find("td:eq(7)").text();
                      respuarray[conres] = ideres+'|'+respubus+'|'+obserbus+'|'+idenor+'|'+'F';
                   }

             }
         }

         });

         if (conres==0 || conrev>0)
         {  
             jAlert('Debe diligenciar minimo una Respuesta');
         }
         else
         {
             $.post("normograma.php",
             {
               consultaAjax:   true,
               accion:         'Grabarespuestanor', 
               wemp_pmla:      $("#wemp_pmla").val(),
               westadop:       $("#westadop").val(),
               westadoi:       $("#westadoi").val(),             
               arrayName:      respuarray
             }, function(respuesta){  
                
                var respu = respuesta.split('|');
                   
                if (respu[0] == 1){
                   
                   alerta('Grabado Exitoso');
                   $("#divrespuesta").dialog("close");
                   Consultar();
                   Cargarcon();

                }
             });
         }

        }


        //Grabar Tareas diligenciadas por el boton 'Vertareas'
        function Grabardivrespuesta(){
         
         var respuarray = new Array(); 
         var conres = 0;
         var conrev = 0;
         
         // Validar que haya minimo una respuesta diligenciada
         $('#tabla_tareas tr:gt(0)').each(function() {
           
           var chk  = $(this).find("#chkcumplida");
           
           if (chk !== null) {                          
               if (chk.prop('checked')) {
                   var respubus = $(this).find("#txtrespuesta").val();
                   if (respubus == '')
                   {
                      jAlert('Debe diligenciar las opciones cumplidas');
                      return;
                   }
               }
           }
         });
        
         // Recorrer la tabla de respuestas para validar y grabar
         $('#tabla_tareas tbody tr').each(function() {
         var chkact  = $(this).find("#chkcumplida");
         var valant  = $(this).find("#txtestcum").val();       

         if (chkact !== null && (valant =='S' || valant =='N')) {

             if (chkact.prop('checked') && valant=='N') {
                  conres++;
                  var ideres   = $(this).eq(0).find("td:eq(7)").text();
                  var idenor   = $(this).eq(0).find("td:eq(8)").text();
                  var respubus = $(this).find("#txtrespuesta").val();
                  var obserbus = $(this).find("#txtobseres").val();     
                  respuarray[conres] = ideres+'|'+respubus+'|'+obserbus+'|'+idenor+'|'+'V';
                  
                  if (respubus.trim() == '') {
                     conrev++;
                     return;
                  }
             }
             else{
                   var respubus = $(this).find("#txtrespuesta").val();
                   var obserbus = $(this).find("#txtobseres").val();
                   if (respubus != '')
                   {
                      conres++;
                      var ideres   = $(this).eq(0).find("td:eq(7)").text();
                      var idenor   = $(this).eq(0).find("td:eq(8)").text();
                      respuarray[conres] = ideres+'|'+respubus+'|'+obserbus+'|'+idenor+'|'+'F';
                   }

             }
         }

         });

         if  ( conres == 0 || conrev>0)
             { 
                jAlert('Debe diligenciar minimo una Respuesta');
             }
         else
             {
                $.post("normograma.php",
                {
                     consultaAjax:   true,
                     accion:         'Grabarespuestanor',
                     wemp_pmla:      $("#wemp_pmla").val(),
                     westadop:       $("#westadop").val(),
                     westadoi:       $("#westadoi").val(),
                     arrayName:      respuarray
                }, function(respuesta){  
                   
                   var respu = respuesta.split('|');
                   
                   if (respu[0] == 1){

                      $("#optestadoi").val(respu[1]);
                      alerta('Grabado Exitoso');                                    
                      $("#divrespuesta").dialog("close");
                      Consultar();
                      Cargarcon();
                   }
                });
              }

        }


        function VerTareas(){

           if ($("#wconsultar").val()=='off')
           { 
               alerta('El Usuario no tiene acceso');
               return;
           }

           $.post("normograma.php",
               {
                 consultaAjax:   true,
                 accion:         'ConsultarTareasnor',
                 wemp_pmla:      $("#wemp_pmla").val(),
                 wcod_usu:       $("#wusuario").val(),
                 westadoi:       $("#westadoi").val(),
                 westadop:       $("#westadop").val(),
                 codigo_art:     $("#wdocumento").val(),

               }, function(respuesta){ 

                  $("#tabla_tareas tbody ").remove(0);

                  if  (respuesta == 'N'){
                      alerta('No existen Tareas asignadas al Normograma');
                  }
                  else{
                      
                      $("#tabla_tareas").append(respuesta);
                      
                      var varpendien =0;

                      //Verificar si alguna Tarea esta pendiente
                      $('#tabla_tareas tbody tr').each(function() {                         
                         var chkact  = $(this).find("#chkcumplida");
                         var valant  = $(this).find("#txtestcum").val();
                         if (chkact !== null && valant =='N') {
                             varpendien = 1;
                         }
                      });

                      if (varpendien == 1){
                          document.getElementById('grarespu').style.visibility = 'visible';
                      }
                      else{
                          document.getElementById('grarespu').style.visibility = 'hidden';
                      }

                      $("#divrespuesta").dialog("open");
                  }

               });
        }


        function VerFicha(){

           if ($("#wconsultar").val()=='off')
           { 
              alerta('El Usuario no tiene acceso');
              return;
           }

           var wemp_pmla  = $("#wemp_pmla").val();
           var codigo_art = $("#wdocumento").val(); 
           $("#txtnumfic").attr("readonly", true);
           $("#tardesfic").attr("readonly", true);
           $("#tarobserfic").attr("readonly", true);
           
           $.post("normograma.php",
             {
                 consultaAjax:   true,
                 accion:         'ConsultarFicha',
                 wemp_pmla:      wemp_pmla,
                 codigo_art:     codigo_art
             }, function(respuesta){ 
                  
                  if (respuesta =='N'){

                      alerta('No existe Ficha para el Normograma');
                  }
                  else{

                      $("#divficha").dialog("open");
                      vficha = respuesta.split('|');
                      $("#txtnumfic").val(vficha[1]);
                      $("#tardesfic").val(vficha[2]);
                      $("#tarobserfic").val(vficha[3]);
                      var vref ='../../juridica/documentos/'+vficha[4];

                      if (vficha[4] !== ''){

                          $("#resultados").html('');
                          $("tr.adjunto").show();

                          if (vficha[5] == 'pdf'){
                      
                              var object= '<br>'
                                          +'<object type="application/pdf" data="'+vref+'#toolbar=1&amp;navpanes=0&amp;scrollbar=1" width="900" height="1300">'
                                              +'<param name="src" value="resultados/resultado_laboratorio.pdf#toolbar=1&amp;navpanes=0&amp;scrollbar=1" />'
                                              +'<p style="text-align:center; width: 60%;">'
                                                  +'Adobe Reader no se encuentra o la version no es compatible, utiliza el icono para ir a la pagina de descarga <br />'
                                                  +'<a href="http://get.adobe.com/es/reader/" onclick="this.target=\'_blank\'">'
                                                      +'<img src="../../images/medical/root/prohibido.gif" alt="Descargar Adobe Reader" width="32" height="32" style="border: none;" />'
                                                  +'</a>'
                                              +'</p>'
                                          +'</object>';

                              $("#resultados").html(object);
                                          
                          }
                          else{

                              $("#resultados").html("<u><a href='"+vref+"' download>VER ARCHIVO</a></u>");
                          }

                      }
                      else
                      {
                        $("tr.adjunto").hide();
                      }
         
                  }
             });
        }


        //Cargar la consulta inicial para que traiga el nuevo estado de implementación
        function Cargarcon()
        {          
          //Llamar el Array que carga los documentos del Normograma (Tabla Superior)
          $.post("normograma.php",
                {
                    consultaAjax:   true,
                    accion:         'Cargarcon',
                    wemp_pmla:      $("#wemp_pmla").val()

                }, function(respuesta){
                   $("#tabla_documentos  tbody ").remove(0);
                   $("#tabla_documentos").append(respuesta);
                   $('input#id_search_normograma').quicksearch('table#tabla_documentos tbody tr');
                   $('input#id_search_tema').quicksearch('table#tabla_documentos tbody tr');
                   //$('input#id_search_tema').val($("#wnomtema").val());
                   //$('input#id_search_tema').keyup();
                });         
        }


        // Anular documento Normograma
        function Anular()
        {
           if ($("#wdocumento").val()=='')
           {
             alerta('Debe seleccionar un Documento');
             return;
           }

           if ($("#weliminar").val()=='off')
           { 
             alerta('El Usuario no tiene acceso');
             return;
           }

           jConfirm("Esta seguro de Anular el Documento?","Confirmar", function(respuesta){  
             if (respuesta == true)
             { 
               var wemp_pmla  = $("#wemp_pmla").val();
               var wcod_usu   = $("#wusuario").val();
               var codigo_art = $("#wdocumento").val(); 

               $.post("normograma.php",
                    {
                      consultaAjax:   true,
                      accion:         'AnularNormograma',
                      wemp_pmla:      wemp_pmla,
                      codigo_art:     codigo_art,
                      codigo_usu:     wcod_usu
                    }, function(respuesta){
                    if (respuesta == 0){
                        alerta('El Documento solo puede ser Anulado por el Usuario que lo ingres\u00F3');
                    }
                    else{ 
                        alerta('El Documento ha sido Anulado');
                    }
               });
             }
           });
       }


       function Cerrardivrespuesta(){
         $("#divrespuesta").dialog("close");
       }


       function justNumbers(e)
       {
         var keynum = window.event ? window.event.keyCode : e.which;

         if ((keynum == 8) || (keynum == 46) || (keynum == 0))
              return true;

          return /\d/.test(String.fromCharCode(keynum));
       }
	 

       // ****** FUNCION Sacar un mensaje de alerta con formato predeterminado  ******
			 function alerta(txt){
				 $("#textoAlerta").text( txt );
				 $.blockUI({ message: $('#msjAlerta') });
				 	 setTimeout( function(){
								   $.unblockUI();
								}, 1800 );
			 }


       function ConsultarContador(wcodigo)
       {

          $.post("normograma.php",
                 {
                    consultaAjax: true,
                    accion      : 'ConsultarContador',
                    wemp_pmla   : $("#wemp_pmla").val(),
                    wcodigo     : wcodigo
                 }, function(respuesta){   
                    
                    var divres = respuesta.split('|');                    

                    for( var i = 0; i < divres.length-1; i++ ){

                         var vtotal = divres[i].split('&');
                         $(".contador"+vtotal[1].trim()).text(vtotal[0]);
                    }

                 });
       }


       function cerrarVentana()
       { 
          if (confirm("Esta seguro de salir?") == true)
              window.close();
          else
              return false;
       }

       // **************************************   Fin Funciones Javascript   ********************************************

      </script>	
      
      <style type="text/css">

         body{
           width: auto;
         }

        .button{
           color:#2471A3;
           font-weight: bold;
           font-size: 12,75pt;
           width: 90px; height: 27px;
           background: rgb(240,248,252);
           background: -moz-linear-gradient(top,  rgba(240,248,252,1) 0%, rgba(236,246,254,1) 50%, rgba(219,238,251,1) 51%, rgba(220,237,254,1) 100%); 
           background: -webkit-linear-gradient(top,  rgba(240,248,252,1) 0%,rgba(236,246,254,1) 50%,rgba(219,238,251,1) 51%,rgba(220,237,254,1) 100%); 
           background: linear-gradient(to bottom,  rgba(240,248,252,1) 0%,rgba(236,246,254,1) 50%,rgba(219,238,251,1) 51%,rgba(220,237,254,1) 100%);
           filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#f0f8fc', endColorstr='#dcedfe',GradientType=0 );
           border: 1px solid #ccc; 
           border-radius: 8px;
           box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset; 
         }

        .button:hover {background-color: #3e8e41}

        .button:active {
           background-color: #3e8e41;
           box-shadow: 0 5px #666;
           transform: translateY(4px);
         }  

        .fondoAmarillo {
           background-color: #fcf3cf;
           color: #000000;
           font-size: 10pt;
         }

        .fondoVerde {
           background-color: #a9dfbf;
           font-size: 10pt;
         }

        .fondoAzul {
           background-color: #7fb3d5;
           font-size: 10pt;
         }

        .fondoNaranja {
           background-color: #fad7a0;
           font-size: 10pt;
         }   

        .fondoImplementada {
           background-color: #fad7a0;
           font-size: 10pt;
         }

        .fondoParcial {
           background-color: #fcf3cf;
           font-size: 10pt;
         }

        .fondoNoimplementada {
           background-color: #7fb3d5;
           font-size: 10pt;
         }

         .txturlnor{
           text-decoration: underline;
         }

         .titulopagina2{
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

      </style>	
 </head>
 <body width=100%>		
	<?php 
	  echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
	  $wtitulo  ="NORMOGRAMA";
    $wactualiz = "2020-04-17";
    encabezado("<div class='titulopagina2'>{$wtitulo}</div>", $wactualiz, "clinica");      
	  $arr_cen   = consultarCentros     ($wbasetalhuma,$conex,$wemp_pmla);
    $arr_tdo   = tipodocumento        ($wbasedato,$conex,$wemp_pmla);
    $arr_emi   = instituemite         ($wbasedato,$conex,$wemp_pmla);
    $arr_esn   = estadonormograma     ($wbasedato,$conex,$wemp_pmla);
    $arr_res   = consultarUnidades    ($wbasedato,$conex,$wemp_pmla);
    $arr_nor   = consultarNormas      ($wbasedato,$conex,$wemp_pmla);
    $str_tem   = consultartemaxusuario($wbasedato,$conex,$wemp_pmla,$wusuario);
    list($strcodtem, $strnomtem) = explode('|', $str_tem);
    $arr_doc   = consultarNormasgral  ($wbasedato,$conex,$wemp_pmla,$strcodtem);      
    $arr_pen   = consultarpendientes  ($wbasedato,$conex,$wemp_pmla,$wusuario);
    $arr_tem   = consultartemas       ($wbasedato,$conex,$wemp_pmla);
    $texto1nor = consultarAliasPorAplicacion($conex, $wemp_pmla, 'texto1normograma');
    $texto2nor = consultarAliasPorAplicacion($conex, $wemp_pmla, 'texto2normograma');
    $texto3nor = consultarAliasPorAplicacion($conex, $wemp_pmla, 'texto3normograma');
    $texto4nor = consultarAliasPorAplicacion($conex, $wemp_pmla, 'texto4normograma');
    $texto5nor = consultarAliasPorAplicacion($conex, $wemp_pmla, 'texto5normograma');
  
	?>

    <fieldset style="border: 0.5px solid #999999;">
        <legend align="left"><span style="font-weight:bold;font-size:9pt;" >Documentos Normograma</span></legend>
        <div style="text-align:left;">              
            <table border=0 id='tblcontadores' name='tblcontadores' style="font-size:12px;">
            <td>Filtrar listado:&nbsp;&nbsp;<input id="id_search_normograma" type="text" value="" size="20" name="id_search_normograma" placeholder="Buscar en documentos">&nbsp;&nbsp;</td>
            <td class='contador01' width=190px height=30px align='center' style="background-color:#7fb3d5; height:25px; border-radius: 5px;font-size:12px;"></td>
            <td class='contador02' width=190px height=30px align='center' style="background-color:#fcf3cf; height:25px; border-radius: 5px;font-size:12px;"></td>
            <td class='contador03' width=190px height=30px align='center' style="background-color:#fad7a0; height:25px; border-radius: 5px;font-size:12px;"></td>
            <td>&nbsp;&nbsp;Tema Normativo Corporativo <select id='opttemagral' name='opttemagral' onChange="SeleccionarTema()"><option></option>
            <?php      
              echo '<option value="*">Todos</option>';
              foreach( $arr_tem as $key => $val){
                if ($key == $strcodtem)
                   echo '<option value="'. $key .'" selected>'.$val.'</option>';
                else
                   echo '<option value="'. $key .'">'.$val.'</option>';
              }
            ?>
            </select>
            </td></table>
        </div>
        <div style="width:100%; height: 230px; overflow:auto; cursor:pointer;" >
        <table width="100%" style="border: 0px solid #999999;" id="tabla_documentos">
        <thead>
            <tr class="encabezadoTabla">
                <td align='center' width='10%'>Tipo Documento</td>
                <td align='center' width='10%'>Numero</td>
                <td align='center' width='10%'>Fecha Emisi&oacute;n</td>
                <td align='center' width='10%'>Fecha Vigencia</td>
                <td align='center' width='10%'>Emitido por</td>                
                <td align='center' width='20%'>Estado de la Norma</td>
                <td align='center' width='20%'>Estado implementaci&oacute;n</td>                
                <td align='center' width='20%'>Unidad Responsable</td>
                <td align='center' width='10%'>Tema</td>
                <td align='center' width='10%'>Relacionado con</td>
            </tr>
        </thead>
        <tbody>
        <?php
        $cont1=0;
        foreach( $arr_doc as $key => $val){
            $cont1 % 2 == 0 ? $cfila = "fila1" : $cfila = "fila2";
            $cont1++; 

            switch ($val['wnoreim']) {
                
                case $arr_codi:
                      $fondo = 'fondoImplementada';
                      break;
                case $arr_codp:
                      $fondo = 'fondoParcial';
                      break;
                default:
                      $fondo = 'fondoNoimplementada';
                      break;
            }            
            
            $deta = '<tr class='.$cfila.' id='.$val['wcodigo'].'-'.$val['wtipo'].'-'.$val['wnumero'].' ondblclick="cargarnormograma(this)">';
            $deta .= '<td align="center">'.$val['wtipo'].'</td>';
            $deta .= '<td align="center">'.$val['wnumero'].'</td>';
            $deta .= '<td align="center">'.$val['wfecha'].'</td>';
            $deta .= '<td align="center">'.$val['wfechavi'].'</td>';
            $deta .= '<td align="center">'.$val['wemitido'].'</td>';
            $deta .= '<td align="center">'.$val['westadon'].'</td>';
            $deta .= '<td class='.$fondo.' align="center">'.$val['westadoi'].'</td>';
            $deta .= '<td align="center">'.$val['wunidad'].'</td>';            
            $deta .= '<td align="center">'.$val['wtema'].'</td>';
            $deta .= '<td>'.$val['wcodrel'].'</td>';
            $deta .= '<td style="display:none;">'.$val['wcodigo'].'</td>';
            $deta .= '</tr>';           
            echo $deta;            
        }
        ?>
        </tbody>
        </table>
        </div>
    </fieldset>
    <fieldset style="border: 0.5px solid #999999;">
    <div id="divcargando" name="divcargando" style='display:none;' ><center><img width="26" height="26" border="0" src="../../images/medical/ajax-loader9.gif"></center></div>    
    </br>
    <center><table>
    <tr>
      <td>&nbsp;&nbsp;<input type='submit' id='Consultar' name='Consultar' class='button' value='Consultar'  onclick='Consultar()'></td>
      <td>&nbsp;&nbsp;<input type='submit' id='Nuevo'     name='Nuevo'     class='button' value='Nuevo'      onclick='Iniciar()'></td>
      <td>&nbsp;&nbsp;<input type='submit' id='Grabar'    name='Grabar'    class='button' value='Grabar'     onclick='Grabar()'></td>
      <td>&nbsp;&nbsp;<input type='submit' id='Anular'    name='Anular'    class='button' value='Anular'     onclick='Anular()'></td>    
      <td>&nbsp;&nbsp;<input type='submit' id='Verficha'  name='Verficha'  class='button' value='Ver Ficha'  onclick='VerFicha()'></td> 
      <td>&nbsp;&nbsp;<input type='submit' id='Vertarea'  name='Vertarea'  class='button' value='Ver Tareas' onclick='VerTareas()'></td>   
      <td>&nbsp;&nbsp;<input type='submit' id='Salir'     name='Salir'     class='button' value='Salir'      onclick='cerrarVentana()'></td>
    </tr>
    </table>
    <br><br>
    <CENTER>       
    <table class='tblprincipal' width='100%' style='border: 1px solid blue'>
      <tr class=fila1>
      		<td width="50px"><b>Tipo de Documento </b></td>
      		<td width="400px"><select id='opttipo' name='opttipo' disabled='true' onChange="ValidarDocumento()" >
          <?php      
            foreach( $arr_tdo as $key => $val){
              echo '<option value="' . $key .'">'.$val.'</option>';
            }
          ?>
          </select>
          </td>
          <td width="100px"><b>Numero de la Norma</b></td>
          <td width="400px"><input type='text' id='txtnumnom' name='txtnumnom' size=30  disabled placeholder="Ingrese Numero de la Norma" onChange="ValidarDocumento()" onkeypress="return justNumbers(event);" ></td>
      </tr>
      <tr class=fila1>
          <td ><b>Fecha de Emisi&oacute;n </b></td>
          <td ><input type='text' id='txtfecemi' name='txtfecemi' size=30 disabled placeholder="Ingrese Fecha de Emisi&oacute;n"></td>
          <td ><b>Fecha de Vigencia </b></td>
          <td ><input type='text' id='txtfecvig' name='txtfecvig' size=30 disabled placeholder="Ingrese Fecha de Vigencia" ></td>
      </tr>
      <tr class=fila1>
          <td ><b>Emitido por</b></td>
          <td ><select id='optemitido' name='optemitido' disabled='true'>
          <?php
            echo '<option ></option>';
            foreach( $arr_emi as $key => $val){
              echo '<option value="' . $key .'">'.$val.'</option>';
            }
          ?>
          </select>
          </td>
          <td><b>URL Norma </b></td>
          <td><u><input type='text' id='txturlnor' name='txturlnor' class='txturlnor' size=90 disabled placeholder="Ingrese url de ubicaci&oacute;n en la web" ondblclick="DireccionarUrl(this.value)";></u></td>
      </tr>
      <tr class=fila1>
          <td><b>Estado de la Norma</b></td>
          <td><select id='optestadon' name='optestadon' disabled='true'>
          <option></option>
          <?php
            foreach( $arr_esn as $key => $val){
              echo '<option value="' . $key .'">'.$val.'</option>';
            }
          ?>
          </select>
          </td>    
          <td><b>Estado de implementaci&oacute;n</b></td>
          <td><select id='optestadoi' name='optestadoi' disabled='true'>
          <?php 
            foreach( $arr_esi as $key => $val){
              echo '<option value="' . $key .'">'.$val.'</option>';
            }
          ?>
          </select>
          </td>
      </tr>
      <tr class=fila1>
          <td><b>Responsable </b></td>
          <td><input type='text' id='autcodres' name='autcodres' size=70 disabled='true' placeholder="Ingrese nombre Unidad responsable"></td>
          <td width="50px"><b>Tema Normativo Corporativo</b></td>
          <td width="400px"><select id='opttema' name='opttema' disabled='true'>
          <option></option>
          <?php      
            foreach( $arr_tem as $key => $val){
              echo '<option value="' . $key .'">'.$val.'</option>';
            }
          ?>
          </select>
          </td>
      </tr>
      <tr class=fila1>
          <td><b>Relacionado con </b></td>
          <td colspan=3><input type='text' id='txtcodrela' name='txtcodrela' size=80 disabled='true' maxlength='100' placeholder="Ingrese numero de documento relacionado"></td>
      </tr>
      <tr class=fila1>    
          <td align='center' colspan=4><b>Descripci&oacute;n Ep&iacute;grafe del Documento </b></td>
      </tr>
      <tr class=fila1>
          <td align='center' colspan=4 style="background-color: lightgray"><textarea id='tardesart' name="tardesart" cols=120 rows=4 class='disabledColor' disabled></textarea></td>
      </tr>
      <tr class=fila1>
          <td  align='center' colspan=4><b>Observaciones del Jur&iacute;dico </b></td>
      </tr>
      <tr>
          <td  align='center' colspan=4 style="background-color: lightgray"><textarea id='tarobsart' name="tarobsart" cols=120 rows=7 class='disabledColor' disabled></textarea></td>
      </tr>
    </table>
    <br>
    <br>     
    <table id='tbltarea' name='tbltarea' border=0 align=center style='font-size:15px;display:none;'>
        <tr><td width="150px" align='center' class='fondoVerde' style= "height:25px; border-radius: 8px;">Activa</td>
        <td width="150px" align='center' class='fondoAmarillo' style= "height:25px; border-radius: 8px;">Pr&oacute;xima a vencerse</td>
        <td width="150px" align='center' class='fondoNaranja' style= "height:25px; border-radius: 8px;">Vencida</td>
        <td width="150px" align='center' class='fondoAzul' style= "height:25px; border-radius: 8px;">No Le&iacute;da</td></tr>
    </table>
    <table border=1 width='1250px' id='tblconsultar' name='tblconsultar' style='display:none;'>
      <thead>
        <tr class=fila1 style="background-color: lightyellow;">
    		<td width="300px" align='center' colspan="3"><b>Adicionar Coresponsable</b></td><td width="400px" align='center'><b>Unidad</b> <input type='text' id='autcoduni' name='autcoduni' size='60' class='disabledColor' disabled='true'></td>
    		</tr>	   
        <tr class=encabezadotabla>
        <td width="50px" align='center'>Unidad</td><td width="150px" align='center'>Descripcion</td><td width="500px" align='center'>Actividad Solicitada</td>
        <td width="100px" align='center'>Fecha Estimada</td></tr>
      </thead>
      <tbody>
      </tbody>
		</table>
    <br>
		<table border=1 width='1250px' id='tbllistaunidad' name='tbllistaunidad' style='display:none;'>
      <thead>
        <tr class=fila1 style="background-color: encabezadotabla">
    		    <td colspan=7 align='center'><b>Lista Coresponsables</b></td>
    		</tr>		
    		<tr class=encabezadotabla>
        		<td width="50px" align="center">Unidad Responsable</td><td width="50px" align="center">Descripcion</td><td width="200px" align="center">Actividad Solicitada</td><td width="200px" align="center">Fecha Probable</td><td width="200px" align="center">Respuesta</td><td width="150px" align="center">Observacion Respuesta</td>
            <td width="50px" align="center">Estado</td>
    		</tr>
  		</thead>
  		<tbody>
  		</tbody>
		</table>		
		</br></br> 
    <div id='divficha' name='divficha' title='Ficha de Evaluaci&oacute;n' style='display:none;'>
      <table>
        <tr class=fila1>
          <td width="50px"><b>Art&iacute;culo </b></td>
          <td width="950px"><input type='text' id='txtnumfic' name='txtnumfic'  size=123 readonly></td>
        </tr>
        <tr class=fila1>
          <td ><b>Descripci&oacute;n </b></td>
          <td ><textarea id='tardesfic' name="tardesfic" cols=120 rows=4 readonly></textarea>  </td>
        </tr>
        <tr class=fila1>
          <td ><b>Observaciones del Jur&iacute;dico </b></td>
          <td ><textarea id='tarobserfic' name="tarobserfic" cols=120 rows=5 readonly></textarea>  </td>
        </tr>
        <tr class='fila1 adjunto'>
          <td ><b>Archivo adjunto </b></td>
          <td ><div id='resultados'></div></td>
          <td ><div id='mensaje_pdf'></div></td>
        </tr>
        <tr><td align='center' colspan=2>
          <input type='button' id='btnregresar' name='btnregresar' class='button' value='Regresar' onclick='$("#divficha").dialog("close");'>
          </td>
        </tr>
      </table>
    </div>       
    </fieldset>
      <div id='divrespuesta' title='Tareas Normograma' style="width:100%; height: 300px; overflow:auto;display:none;" >        
        <table border=0 align=center style=font-size:15px>
          <tr><td width="150px" align='center' class='fondoVerde' style="height:25px; border-radius: 8px;">Activa</td>
          <td width="150px" align='center' class='fondoAmarillo' style="height:25px; border-radius: 8px;">Pr&oacute;ximo a vencerse</td>
          <td width="150px" align='center' class='fondoNaranja' style="height:25px; border-radius: 8px;">Vencida</td>
          <td width="150px" align='center' class='fondoAzul' style="height:25px; border-radius: 8px;">No Le&iacute;da</td></tr> 
        </table>
        <br>
        <table width="100%" style="border: 0px solid #999999;" id="tabla_tareas" name="tabla_tareas">
            <tbody>            
            <tr class="encabezadoTabla">                
                <td align='center'>N&uacute;mero Normograma</td>
                <td align='center'>Fecha</td>
                <td align='center'>Descripci&oacute;n Tarea</td>
                <td align='center'>Respuesta</td>
                <td align='center'>Observaci&oacute;n</td>
                <td align='center'>Cumplida</td>
            </tr>
            <?php      
            $cont1=0;
            foreach( $arr_pen as $key => $val){

                $cont1 % 2 == 0 ? $fondo = "fila1" : $fondo = "fila2";
                $cont1++; 

                $segundos = strtotime($val['wtarfec']) - strtotime($wfecha);
                $difdias  = intval($segundos/60/60/24);

                if ($val['wtarlei'] == 'off') //Tareas No leidas
                    $clafil ='fondoAzul';
                
                if ($val['wtarlei'] == 'on'  ) //Tarea Activa
                    $clafil ='fondoVerde';  

                if ($difdias <= 7 && $difdias >= 1) //proxima a vencerse
                    $clafil = "fondoAmarillo"; 

                if ($difdias < 1 ) //vencida
                    $clafil = "fondoNaranja";

                echo '<tr class="'.$fondo.'" id='.$val['wcodigo'].'>';                
                echo '<td align="center">'.$val['wnumero'].'</td>';
                echo '<td class="'.$clafil.'">'.$val['wfecha'] .'</td>';
                echo '<td>'.utf8_decode($val['wobser']) .'</td>';
                echo '<td><textarea cols=50 rows=2 id="txtrespuesta" name="txtrespuesta">'.utf8_decode($val["wtarres"]).'</textarea></td>';
                echo '<td><textarea cols=50 rows=2 id="txtobseres"  name="txtobseres">'.utf8_decode($val["wobsres"]).'</textarea></td>';            
                echo '<td><input type="checkbox" id="chkcumplida" name="chkcumplida" ></td>';
                echo '<td style="display:none;">'.$val['widetar'].'</td>';
                echo '<td style="display:none;">'.$val['widenor'].'</td>';
                echo '</tr>';
            } 
            ?>
            <tr><td colspan=6 align="center"><input type="button" class="button" id="grabarrespu" name="grabarrespu" value="Grabar" onclick="Grabarrespuesta()">&nbsp;&nbsp;<input type="button" class="button" id="regrespu" name="regrespu" value="Regresar" onclick='$("#divrespuesta").dialog("close");'></td></tr>
            </tbody>
        </table> 
        <br><br>
    </div> 
 		<table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;display:none;'>
 		<tr><td>No hay usuarios con el codigo ingresado</td></tr>
 		</table>
    <div id='divAcceso' style='display:none;'>
      <br><br>
      <table align='center' id='tblmensaje2' name='tblmensaje2' style='border: 1px solid blue;'>
      <tr><td align='center'>El usuario no tiene acceso</td></tr>    
      </table>
      <br><br>
      <center><input type='button' name='btnregresar' class='button' value='Salir' onclick='window.close();'></center>  
    </div>
  	<div id='msjAlerta' style='display:none;'>
  		<br><img src='../../images/medical/root/Advertencia.png'/>
  		<br><br><div id='textoAlerta'></div><br><br>
  	</div>
  	<div style="display:none;" id="img_bus">Actualizando en Matrix.. <img width="13" height="13" border="0" src="../../images/medical/ajax-loader9.gif"></div>
		</center>
    <input type="HIDDEN" name="arr_estado"   id="arr_estado"  value='<?=json_encode($arr_esi)?>'>
    <input type="HIDDEN" name="arr_nor"      id="arr_nor"     value='<?=json_encode($arr_nor)?>'>
    <input type="HIDDEN" name="arr_res"      id="arr_res"     value='<?=json_encode($arr_res)?>'>
    <input type="HIDDEN" name="arr_cen"      id="arr_cen"     value='<?=json_encode($arr_cen)?>'>
    <input type="HIDDEN" name="arr_cores"    id="arr_cores"   value='<?=json_encode($arr_cores)?>'>
    <input type="HIDDEN" name="arr_pen"      id="arr_pen"     value='<?=json_encode($arr_pen)?>'>
    <input type="HIDDEN" name="wusuario"     id="wusuario"    value='<?=$wusuario?>'>
    <input type="HIDDEN" name="westadoi"     id="westadoi"    value='<?=$arr_codi?>'>
    <input type="HIDDEN" name="westadop"     id="westadop"    value='<?=$arr_codp?>'>
    <input type="HIDDEN" name="wtexto1nor"   id="wtexto1nor"  value='<?=$texto1nor?>'>
    <input type="HIDDEN" name="wtexto2nor"   id="wtexto2nor"  value='<?=$texto2nor?>'>
    <input type="HIDDEN" name="wtexto3nor"   id="wtexto3nor"  value='<?=$texto3nor?>'>
    <input type="HIDDEN" name="wtexto4nor"   id="wtexto4nor"  value='<?=$texto4nor?>'>
    <input type="HIDDEN" name="wtexto5nor"   id="wtexto5nor"  value='<?=$texto5nor?>'>
    <input type="HIDDEN" name="arr_cen2"     id="arr_cen2"    value='<?=base64_encode(serialize($arr_cen))?>'>
    <input type="HIDDEN" name="wcentro"      id="wcentro">
    <input type="HIDDEN" name="wideusu"      id="wideusu">
    <input type="HIDDEN" name="wdocumento"   id="wdocumento">
		<input type="HIDDEN" name="wnueusuario"  id="wnueusuario">
    <input type="HIDDEN" name="wcodrelacion" id="wcodrelacion">
    <input type="HIDDEN" name="wcodrespon"   id="wcodrespon">    
    <input type="HIDDEN" name="wnormoide"    id="wnormoide"  value=0>
    <input type="HIDDEN" name="wedicion"     id="wedicion"   value='N'>
    <input type="HIDDEN" name="wgrabar"      id="wgrabar"    value='off'>
    <input type="HIDDEN" name="weditar"      id="weditar"    value='off'>
    <input type="HIDDEN" name="weliminar"    id="weliminar"  value='off'>
    <input type="HIDDEN" name="wconsultar"   id="wconsultar" value='off'>
    <input type="HIDDEN" name="wnomtema"     id="wnomtema"   value='<?=$strnomtem?>'>
    <input type="HIDDEN" name="wcodtema"     id="wcodtema"   value='<?=$strcodtem?>'>
    <input id="id_search_tema" name="id_search_tema" type="HIDDEN" value="" size="20">
	</body>
	</html>    