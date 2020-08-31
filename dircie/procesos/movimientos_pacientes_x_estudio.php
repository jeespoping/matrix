<?php
include_once("conex.php");
   /*******************************************************************************************************************
   *                                 MOVIMIENTOS DE PACIENTES POR ESTUDIO                                             *
   *                                                                                                                  *
   ***********************************        DESCRIPCIÓN       *******************************************************
   * Muestra una cuadrícula con la lista de conceptos (filas) y frecuencias (columnas) del estudio                    *
   * de modo que se pueda grabar la cantidad de frecuencias por concepto y paciente. También se                       *
   * puede chequear si se ha realizado ese concepto en esa frecuencia para determinado paciente.                      *
   ********************************************************************************************************************
   * Autor: John M. Cadavid. G.                                                                                       *
   * Fecha creacion: 2011-12-27                                                                                       *
   * **********************************      Modificaciones    ********************************************************
   * 2017-06-09 - Arleyda Insignares Ceballos. Se adicionan decimales, y se cambia el orden de las columnas en la     *
   *              captura de movimientos por estudio para que ordene por frecuencia.                                  *      
   *                                                                                                                  *
   * 2017-03-21 - Arleyda Insignares Ceballos. Se adiciona tipo de movimiento (Facturar), el cual graba una factura   *
   *              en la tabla dircie_000014 con Número de factura, fecha probable de pago y estado. En la tabla       *
   *              dircie_000008 queda grabado los movimientos que pertenecen al detalle de la factura. Tener en       *    
   *              cuenta parámetro en root_000051 'porcentajeiva_dircie'.                                             *
   *                                                                                                                  *
   * 2016-12-06 - Arleyda Insignares Ceballos. Digitar las Frecuencias según el estudio y el Laboratorio seleccionado *
   *              Se Adiciona un numero de visitas, tipo de visita y Periodo para cada estudio.                       *
   *                                                                                                                  *
   * 2012-02-17 - Se implementó la actualización automática  de los totales cuando se cambia el valor de un campo     *
   * igualmente se implementó la grabación de datos en las tablas por medio de AJAX, cuando el usuario cambia un      *
   * valor, este automáticamente queda grabado. Estas dos acciones se hacen medaiante la función cambiar_valores de   *
   * javascript. Se adicionó el botón Imprimir proyectado o realizado según el tipo de movimiento que se esté         * 
   * haciendo - Mario Cadavid                                                                                         *
   * 2012-01-17 - Se adicionó filtro por paciente, se pueden ver los pacientes a los cuales se le han grabado datos   *
   * y a los que no, con la posibilidad de seleccionar uno para editarlo. Se adicionó también un evento para resaltar *
   * la fila en la que el usuario se posiciona a editar o grabar de modo que le sea fácil saber en que concepto está  *
   * y qué va a grabar - Mario Cadavid                                                                                *
   ********************************************************************************************************************/
 
  $wactualiz = "2017-06-09";  

  if(!isset($_SESSION['user'])){
    echo "<center></br></br><table id='tblmensaje' name='tblmensaje' style='border: 1px solid blue;visibility:none;'>
    <tr><td>Error, inicie nuevamente</td></tr>
    </table></center>";
    return;
  }

  header('Content-type: text/html;charset=ISO-8859-1');

  //*************************************      Inicio    ***********************************************************
  
  

  include_once("root/comun.php");
  
  $wporceniva  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'porcentajeiva_dircie');
  $wbasedato   = consultarAliasPorAplicacion($conex, $wemp_pmla, "dircie");
  $wbasecliame = consultarAliasPorAplicacion($conex, $wemp_pmla, "cliame");
  $wbasemovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
  $whce        = consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
  $wfecha      = date("Y-m-d");
  $whora       = (string)date("H:i:s");
  $pos         = strpos($user,"-");
  $wusuario    = substr($user,$pos+1,strlen($user));

  // ***************************************    FUNCIONES AJAX  Y PHP  **********************************************
   
         if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarHistoria"){

              list($tipoide, $numeroide) = explode('-', $wpaciente);

              // Consulto si exiten datos de movimiento
              $q =  " SELECT Paehis, Paeing, Paefec
                            FROM ".$wbasedato."_000007                           
                      WHERE Paetid = '".$tipoide."' 
                        AND Paeide = '".$numeroide."' ";

              $res_consulta = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

              $num_consulta = mysql_num_rows($res_consulta);

              $detalle = 'N';

              $vfila = 0;

              if ($num_consulta > 0){

                  $detalle = "<table style='width:1000px;border: 1px solid gray;'>
                              <thead><tr class='encabezadoTabla'>                    
                              <th align='center' width='50px'> NUMERO DE <br> HISTORIA</th>
                              <th align='center' width='50px'> NUMERO DE <br> INGRESO </th>
                              <th align='center' width='50px'> FECHA DE <br> INGRESO </th>
                              <th align='center' width='50px'> </th>
                              </tr></thead>";

                  // Consultar todos los Pacientes asignados al Estudio
                  while($row = mysql_fetch_assoc($res_consulta))
                  { 
                    
                        $vfila++;
                        
                        if (is_int ($vfila/2))
                            $wcolor="fila1";
                        else
                            $wcolor="fila2";

                        $path="../../hce/procesos/HCE_iFrames.php?empresa=".$whce."&origen=01&wdbmhos=".$wbasemovhos."&whis=".$row['Paehis']."&wing=".$row['Paeing']."&accion=F&ok=0&wcedula=".$numeroide."&wtipodoc=".$tipoide;  

                        $detalle .= "<tr class='".$wcolor."'>
                                     <td align='center'>".$row['Paehis']."</td>
                                     <td align='center'>".$row['Paeing']."</td>
                                     <td align='center'>".$row['Paefec']."</td>
                                     <td nowrap='nowrap' align=center style='cursor: pointer' onclick='ejecutar(".chr(34).$path.chr(34).")'>Ver</td></tr>"; 

                  }

                  $detalle .= "</table><br><center><input type='button' id='btnconsultar' name='btnconsultar' class='button' value='Regresar' onclick='CerrarModalhistoria()'></center>";

              }      

              echo $detalle;

              return ;       
         }


         if (isset($_POST["accion"]) && $_POST["accion"] == "GrabarEstudio"){

                list($tipoide, $numeroide) = explode('-', $wcodigopa);
                
                $res_graba = 0;

                if ($wtipo == 'Mopfac' ){  // Grabar Encabezado factura y actualizar detalle

                             //Adicionarle el iva                            
                             $wvaliva = round(($wcosfac * $wporceniva) /100);

                             // Grabar Nueva Factura
                             $q = " INSERT INTO ".$wbasedato."_000014
                                    (Medico,Fecha_data,Hora_data,Facnum,Facfec,Faccol,Faccoe,Faccos,Faciva,Facpag,Facfpa,Facest,Seguridad)
                                    VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".$wnumfac."','".$wfecha."','".$wcodigola."',
                                    '".trim($wcodigoes)."','".$wcosfac."','".$wvaliva."','".$wpagada."','".$wfecfac."','on','C-".$wusuario."') ";

                             $res_graba = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

                             if ($res_graba == 1)
                             {

                                 for ($x=1;$x<count($warrgrabar); $x++){  
                         
                                     list($costouni, $frecuencia, $visita, $codiconce, $tipoide, $numeroide, $cantidad ,$tipvisita) = explode('|', $warrgrabar[$x]);
                           
                                     $q = " UPDATE ".$wbasedato."_000008 
                                              SET Fecha_data='".$wfecha."',Hora_data='".$whora."',Mopfac='".trim($wnumfac)."',Seguridad='C-".$wusuario."' 
                                            WHERE Mopcol = '".trim($wcodigola)."' 
                                              AND Mopcoe = '".trim($wcodigoes)."' 
                                              AND Mopide = '".trim($numeroide)."' 
                                              AND Moptid = '".trim($tipoide)."' 
                                              AND Mopcoc = '".trim($codiconce)."' 
                                              AND Mopnvi = '".$visita."' 
                                              AND Moptvi = '".trim($tipvisita)."' 
                                              AND Mopvfr = '".$frecuencia."'
                                              AND Mopest = 'on' ";

                                     $rescon = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

                                 }            
                             }               

                } // Fin opcion MOPFAC

                else{ // Cuando tipo de movimiento es = 'Mopcan' o 'Moprea'

                    for ($x=1;$x<count($warrdetalle); $x++){
                         
                        list($contenido, $frecuencia, $visita, $concepto, $tipvisita) = explode('|', $warrdetalle[$x]);

                        list($codiconce,$nomconce) = explode('-', $concepto);

                        // Consulto si exiten datos de movimiento
                        $q =  " SELECT Mopcan, Moprea 
                                    FROM ".$wbasedato."_000008 
                                WHERE  Mopcol  = '".trim($wcodigola)."' 
                                    AND Mopcoe = '".trim($wcodigoes)."' 
                                    AND Mopide = '".trim($numeroide)."' 
                                    AND Moptid = '".trim($tipoide)."' 
                                    AND Mopcoc = '".trim($codiconce)."' 
                                    AND Mopvfr = '".trim($frecuencia)."' 
                                    AND Moptvi = '".trim($tipvisita)."' 
                                    AND Mopnvi = '".$visita."' 
                                    AND Mopest = 'on' ";

                        $res_consulta = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

                        $num_consulta = mysql_num_rows($res_consulta);

                        if ($num_consulta>0){

                            switch($wtipo){

                                case  'Moprea':
                                  
                                      $q = " UPDATE ".$wbasedato."_000008 
                                                SET Fecha_data='".$wfecha."',Hora_data='".$whora."',Moprea='".$contenido."',Seguridad='C-".$wusuario."' 
                                             WHERE Mopcol  = '".trim($wcodigola)."' 
                                                AND Mopcoe = '".trim($wcodigoes)."' 
                                                AND Mopide = '".trim($numeroide)."' 
                                                AND Moptid = '".trim($tipoide)."' 
                                                AND Mopcoc = '".trim($codiconce)."' 
                                                AND Mopnvi = '".$visita."' 
                                                AND Mopvfr = '".trim($frecuencia)."' 
                                                AND Moptvi = '".trim($tipvisita)."' 
                                                AND Mopest = 'on' ";
                                      break;

                                case 'Mopcan':

                                      $q = " UPDATE ".$wbasedato."_000008 
                                                SET Fecha_data='".$wfecha."',Hora_data='".$whora."',Mopcan='".$contenido."',Seguridad='C-".$wusuario."' 
                                             WHERE Mopcol  = '".trim($wcodigola)."' 
                                                AND Mopcoe = '".trim($wcodigoes)."' 
                                                AND Mopide = '".trim($numeroide)."' 
                                                AND Moptid = '".trim($tipoide)."' 
                                                AND Mopcoc = '".trim($codiconce)."' 
                                                AND Mopnvi = '".$visita."'                                   
                                                AND Mopvfr = '".trim($frecuencia)."' 
                                                AND Moptvi = '".trim($tipvisita)."' 
                                                AND Mopest = 'on' ";

                                      break;

                          
                            }
                            $res_graba = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                            
                        }    
                        else  { // Si es un estudio nuevo
                            
                            if ($wtipo=='Mopcan'){
                                
                                if (strlen(trim($contenido)) > 0 ){

                                    $realizado = 'off';

                                    $q = " INSERT INTO ".$wbasedato."_000008 
                                             (Medico,Fecha_data,Hora_data,Mopcol,Mopcoe,Mopcoc,Mopide,Moptid,Mopvfr,Moprea,Mopcan,Mopest,Mopnvi,Moptvi,Seguridad)
                                           VALUES ('".$wbasedato."','".$wfecha."','".$whora."','".trim($wcodigola)."','".trim($wcodigoes)."','".trim($codiconce)."',
                                           '".trim($numeroide)."','".trim($tipoide)."','".trim($frecuencia)."','".$realizado."',".$contenido.",'on',".$visita.",'".trim($tipvisita)."','C-".$wusuario."') ";

                                    $res_graba = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
                                  
                                }
                            }

                        } //Fin estudio nuevo 
                  }
              } // fin else
                
              echo $res_graba;
              return;                
         }


         // Validar si la factura ya existe en el sistema
         if (isset($_POST["accion"]) && $_POST["accion"] == "ValidarFactura"){

              $facencon = 'N';

              $qcon =  "  SELECT Facnum
                          FROM ".$wbasedato."_000014 
                          WHERE Facnum = '".$wfactura."' ";      

              $rescon  = mysql_query($qcon,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcon." - ".mysql_error());
              $numcon  = mysql_num_rows($rescon);

              if ($numcon > 0){
                 $facencon = 'S';

              }

              echo $facencon;
              return;

         }


        //Consultar todo el Detalle del Estudio para mostrarlo y que el usuario pueda ingresar los datos
         if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarDetalle"){

              $respuesta   = array();
              $totales     = array();
              $facturado   = array(); 
              $movimientos = array();        

               // * * * Consulta de datos del ESTUDIO
              $qest = "  SELECT Estcod, Estnom, Cestmo 
                         FROM ".$wbasedato."_000002, ".$wbasedato."_000004 
                         WHERE Cescoe = '".$wcodigoes."' 
                             AND Cescol = '".$wcodigola."' 
                             AND Cescoe = Estcod 
                             AND Estest = 'on' 
                             AND Cesest = 'on' ";

              $res_est = mysql_query($qest,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qest." - ".mysql_error());
              $row_est = mysql_fetch_array($res_est);
                    

              // * * * Consulta de CONCEPTOS
              $qcon =  "  SELECT IF(Lecval=0,Conval,Lecval) valorcon , Concod, Condes, Contip 
                          FROM ".$wbasedato."_000003, ".$wbasedato."_000005 
                          WHERE Leccol = '".$wcodigola."' 
                             AND Leccoe = '".$wcodigoes."' 
                             AND Leccoc = Concod 
                             AND Conest = 'on' 
                             AND Lecest = 'on' 
                          ORDER BY Concod, Condes ";
        
              $res_con = mysql_query($qcon,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qcon." - ".mysql_error());
              $numcon  = mysql_num_rows($res_con);


              // * * * Consulta de FRECUENCIAS
              $q = "  SELECT Lefvfr, Leftvi, Lefper, Lefnum, Tvides 
                             FROM ".$wbasedato."_000006 A
                             Left join ".$wbasedato."_000013 B on A.Leftvi = B.Tvicod
                      WHERE Lefcol = '".$wcodigola."' 
                             AND Lefcoe = '".$wcodigoes."' AND Lefest = 'on' 
                      ORDER BY Lefvfr ";

              $res_fre = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
              $numfre  = mysql_num_rows($res_fre);


              // Llenar Registro de Frecuencias para el encabezado
              $data2  = '';
              $data3  = '';
              $frecuencias = array(); 

              for ($i=1;$i<=$numfre;$i++)
              {                
                  $row_fre = mysql_fetch_assoc($res_fre); 
                  // Asignar al título las frecuencias que se hayan ingresado
                  
                  $perfre = $row_fre['Lefper'] == 'M' ?  'M' : "D";
                  $tipovi = $row_fre['Tvides'];
                  $frecuencias[$i] = $row_fre['Lefvfr'];
                  $tiposvisita[$i] = $row_fre['Leftvi'];

                  $numvis = $row_fre['Lefnum'];
                  $data3 .= "<th align='center' width='31px'> ".$tipovi."/".$perfre." </th>";
                  $data2 .= "<th align='center' width='40px'> ".$row_fre['Lefvfr']." </th>";
                  $datvi .= "<th align='center' width='40px'> ".$row_fre['Leftvi']." </th>";
              }


              // * * * * * * * * * * * * * * * * * * Seleccionar según el Tipo de Movimiento * * * * * * * * * * * * * * * * * * * 
              if ($wtipo == 'Mopfac'){
 
				          //Seleccionar todos los pacientes que pertenezcan al Estudio y Laboratorio.
                  $q  = " SELECT Paecol, Paecoe, B.Pactid, B.Pacced, concat (B.Pactid,'-',B.Pacced) as Idepac, 
                                 concat (B.Pacno1,' ',B.Pacno2,' ',B.Pacap1,' ',B.Pacap2) as Nompac
                          From ".$wbasedato."_000007 A
                          Inner Join ".$wbasedato."_000011 B on A.Paeide = B.Pacced AND A.Paetid = B.Pactid
                          Where A.Paecol='".$wcodigola."'
                            AND A.Paecoe='".$wcodigoes."' 
                            AND B.Pacest= 'on' "  ;

                  $res_gen = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
                  $num = mysql_num_rows($res_gen);
                 
                  if  ($num > 0)
                  {

                      // Definición del número de columnas de la tabla
                      $numcol     = $numfre+20;
                      $resultset2 = array();
                      $num_pac    = 0;

                      // Recorrer Consulta de Pacientes asignados al Estudio
                      while($row_pac = mysql_fetch_assoc($res_gen))
                      { 
                            $itempac = $row_pac['Pacced'];

                            // Consulta de los movimientos realizados para el estudio del Paciente Seleccionado
                            $qlis = " SELECT Concod, Condes, Conval, Mopvfr, Moprea, Mopcan, Mopnvi, Mopfac, Moptvi
                                      FROM ".$wbasedato."_000003, ".$wbasedato."_000008
                                      WHERE Mopcol = '".trim($wcodigola)."'
                                        AND Mopcoe = '".trim($wcodigoes)."' 
                                        AND Mopide = '".trim($row_pac['Pacced'])."'
                                        AND Moptid = '".trim($row_pac['Pactid'])."' 
                                        AND Mopcoc = Concod
                                        AND Moprea = 'on'
                                        AND Conest = 'on'
                                        AND Mopest = 'on'
                                      ORDER BY Concod, Condes, Mopvfr" ;
 

                            $reslis = mysql_query($qlis,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qlis." - ".mysql_error());
                            $numlis = mysql_num_rows($reslis);
                            
                            if ($numlis>0){

                               //Llevar a un array la consulta de movimientos por Estudio                                                        
                               while ($row = mysql_fetch_assoc($reslis)) 
                                      $resultset2[] = $row;
     

                            $dataenc  = "";

                            // Consultar datos del Paciente
                            $q   = "SELECT A.Pactid, A.Pacced,concat (A.Pacno1,' ',A.Pacno2,' ',A.Pacap1,' ',A.Pacap2) as Nompac
                                    From ".$wbasedato."_000011 A 
                                    Where A.Pactid = '".$row_pac['Pactid']."' AND A.Pacced = '".$row_pac['Pacced']."'";

                            $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
                            $num = mysql_num_rows($res);

                            // Mostrar fila con datos del paciente
                            $row   = mysql_fetch_assoc($res);
                            $paciengra = $row['Pactid']."|".$row['Pacced'];
                            $data .= "<div class='accordionFiltros' align='center'>";
                            $data .= "<h1 style='font-size: 11pt;' align='left'>". $row['Nompac']." | ".$row['Pactid'].$row['Pacced']. "</h1>";
                            $data .= "<div class='ui-tabs ui-widget ui-widget-content ui-corner-all'>";
                            $data .= "<table id='tbl_".$row['Pacced']."'>";
                                                          
                            
                            // Encabezado con título de las columnas
                            $data .= "<tr class='encabezadoTabla'>";
                            $data .= "<td align='center' rowspan='3' width='50%'> CONCEPTOS </td>";
                            for ($j=1;$j<=$numvis;$j++)
                                 $data .= $data3;
                           

                            // Columna donde se mostrará la frecuencia del concepto
                            $data .= "<td align='center' rowspan='3'> Total <br> Eventos </td>";
                            // Columna donde se mostrará el costo del concepto
                            $data .= "<td align='center' rowspan='3'> Costo en <br> ".$row_est['Cestmo']." </td>";
                            // Columna donde se mostrará el total de costo x frecuencia
                            $data .= "<td align='center' rowspan='3'> Total <br> Costo </td>";
                            $data .= "</tr>";

                            //Agregar la Frecuencia
                            $data .= "<tr class='encabezadoTabla'>";

                            for ($j=1;$j<=$numvis;$j++)                            
                                $data .= $data2;  
                            
                            $data .= "</tr>";


                            // Agregar el tipo de visita
                            $data .= "<tr class='encabezadoTabla'>";

                            for ($j=1;$j<=$numvis;$j++)                            
                                $data .= $datvi;  
                            
                            $data .= "</tr>";
                            
                            if ($numcon > 0){

                               $j=0;

                               //Recorrer consulta de conceptos 
                               while($row_con = mysql_fetch_assoc($res_con))
                               {
                                    
                                    $j++;
                                    $realizado   = array(); 
                                    $cont_frecuencia = 0;

                                   //**************************************************************************************
                                
                                    //Seleccionar el color del fondo de la fila
                                    if (is_int ($j/2))
                                        $wcf="fila1";
                                    else
                                        $wcf="fila2";

                                    $aux_concepto = $row_con['Concod'];          
                                    $data .= "<tr class='".$wcf."' onclick='ilumina(this,\"".$wcf."\")'>";

                                    // Celda con el concepto para cada recorrido
                                    $data .= "<td width='80px'>".$row_con['Concod']." - ".$row_con['Condes']."</td>";
                                  
                                    $cont_frecuencia =0;
                                    
                                    foreach ($resultset2 as $rowlis)
                                    {

                                        $frecuencia = $rowlis['Mopvfr'];
                                        $numerovisi = $rowlis['Mopnvi'];
                                        $tipovisita = $rowlis['Moptvi'];

                                        if ($rowlis['Concod'] == $aux_concepto){ 
                                            
                                             $movimientos[$frecuencia][$numerovisi][$tipovisita][$aux_concepto]  = $rowlis['Mopcan'];
                                             $realizado[$frecuencia][$numerovisi][$tipovisita][$aux_concepto]    = $rowlis['Moprea'];
                                             $facturado[$frecuencia][$numerovisi][$tipovisita][$aux_concepto]    = $rowlis['Mopfac'];  
                                            
                                             if ($rowlis['Moprea']=='on')
                                             {     
                                                                             
                                                $totales[$frecuencia][$numerovisi][$tipovisita]   += $rowlis['Mopcan'];
                                                $cont_frecuencia += $rowlis['Mopcan'];
                                             } 
                                        }                                        
                                    }

                                 
                                    // Ciclo que imprime los datos de la fila 
                                    // Con base en los arreglos $frecuencias y $movimientos
                                    $cfil=0;
                                    $numcol=0;
                                    for ($k=1;$k<=$numvis;$k++)
                                    {
                                        for ($i=1;$i<=$numfre;$i++)
                                        {                                                                            
                                            $cfil ++;
                                            $numcol ++;
                                            $varcelcal  = 'columna'.$numcol;
                                            $frecuencia = $frecuencias[$i];
                                            $tipovisita = $tiposvisita[$i];
                                            $nfactur    = $facturado[$frecuencia][$k][$tipovisita][$aux_concepto];
                                            
                                            if (intval($nfactur) > 0)

                                                $vartitle   = 'Frecuencia '.$frecuencia.' ,Factura: '.$nfactur;
                                            else 
                                                $vartitle   = 'Frecuencia '.$frecuencia;

                                            // Confirmar si se encuentra Facturado
                                            if (isset($facturado[$frecuencia][$k][$tipovisita][$aux_concepto]) && (intval($facturado[$frecuencia][$k][$tipovisita][$aux_concepto]) >0)){
                                                $numfactura = $facturado[$frecuencia][$k][$tipovisita];
                                                $estfactura = 'disabled';
                                            }    
                                            else{ 
                                                $numfactura = ''; 
                                                $estfactura = ''; 
                                            }     
                                            
                                            $concegra = $row_con['Concod']; 

                                            $data .= '<td align="center" width="30px" class="'.$varcelcal.'"  class="mostrarentidad" title="'.$vartitle.'" Onclick ="iluminacolumna(this,\''.$varcelcal.'\');" nowrap >';
                                            
                                            if (isset($movimientos[$frecuencia][$k][$tipovisita][$aux_concepto]) && intval($movimientos[$frecuencia][$k][$tipovisita][$aux_concepto])>0 )
                                            {

                                                $data   .= $movimientos[$frecuencia][$k][$tipovisita][$aux_concepto]." ";
                                                $itemcos = $movimientos[$frecuencia][$k][$tipovisita][$aux_concepto]*$row_con['valorcon'];
                                                $itemval = $movimientos[$frecuencia][$k][$tipovisita][$aux_concepto];                                                                                               
                                                $data .= " <input type='checkbox' id='".$itempac."-cfv-".$j."-".$cfil."' name='cfv-".$j."-".$cfil."' value='".$itemcos."|".$frecuencia."|".$k."|".$concegra."|". $paciengra."|".$itemval."|".$tipovisita."' onclick='cambiar_valores(this)' ".$estfactura." /> ";
                                              
                                            }
                                          
                                            $data .= "</td>";
                                        }
                                    }


                                    // Frecuencia del concepto
                                    $data .= "<td align='right'><div id='".$itempac."-eventos-".$j."'> ".number_format($cont_frecuencia,0,'.',',')."</div> </td>";
                                    $totales['cantidad']['1']  += $cont_frecuencia;   // Total de frecuencias para el concepto

                                    // Costo unitario del concepto
                                    $data .="<td align='right'><div id='".$itempac."-costo-".$j."'>".number_format($row_con['valorcon'],2,'.',',')."</div> </td>";
                                    $totales['costo']['1']  += $row_con['valorcon'];   // Total de costos unitarios de los conceptos

                                    // Costo total de el valor del concepto X las frecuencias de éste
                                    //$total_concepto = $row_con['valorcon']*$cont_frecuencia;
                                    $total_concepto = 0;
                                    $data .= "<td align='right'><div id='".$itempac."-total-".$j."'> ".number_format($total_concepto,2,'.',',')."</div> </td>";
                                    $totales['total']['1']  += $total_concepto;   // Total conceptos X frecuencias para el paciente

                                    $data .= "</tr>";
                                //*******************************************************************************************

                           } // Fin while conceptos

                           mysql_data_seek ( $res_con , 0 );
                            
                            // Pie de la tabla para mostrar los totales
                            $data .= "<tr class='encabezadoTabla'>";
                            $data .= "<td align='center'>";
                            $data .= "TOTALES";
                            $data .= "</td>";
                            
                            // Ciclo para llenar el pie de página de la tabla con los totales de las frecuencias
                            $varcoltot = 0;
                            for ($k=1;$k<=$numvis;$k++){
                              
                                  for ($i=1;$i<=count($frecuencias);$i++){
                                      $varcoltot++;
                                      $indice_frecuencia = $frecuencias[$i];
                                      $tipos_visita      = $tiposvisita[$i]; 
                                      $numero_visita     = $k;

                                      if(isset($totales[$indice_frecuencia][$numero_visita][$tipos_visita]))
                                        $total_frecuencia = $totales[$indice_frecuencia][$numero_visita][$tipos_visita];
                                      else
                                        $total_frecuencia = 0;
                                        
                                      $data .= "<td align='center'><div id='".$itempac."-frecuencia-".$varcoltot."'>".number_format($total_frecuencia,0,'.',',')."</div></td>";
                                  }
                              }
                        
                            // Celda donde se muestra el total de la sumatoria de la cantidad de frecuencias
                            $data .= "<td align='right'><div id='".$itempac."-eventostot' name='".$itempac."-eventostot'> ".number_format($totales['cantidad']['1'],0,'.',',')."</div> </td>";

                            // Celda donde se muestra el total de la sumatoria del costo unitario de los conceptos
                            $data .= "<td align='center'><div id='".$itempac."-costotot' name='".$itempac."-costotot' > ".number_format($totales['costo']['1'],2,'.',',')."</div> </td>";

                            // Celda donde se muestra el total de la sumatoria de costos unitario de los conceptos X las frecuencias
                            $data .= "<td align='center'><div id='".$itempac."-totaltot' name='".$itempac."-totaltot' > ".number_format($totales['total']['1'],2,'.',',')."</div> </td>";
                            
                            $data .= "</tr>";

                            // Sumo al costo total del estudio
                            $total_estudio += $totales['total']['1'];
                            
                            // Espacio entre las filas con los datos y la fila con el total general
                            $data .= "<tr><td colspan='".$numcol."' height='20'></td></tr>";
                            $data .= "</table>";
                            $data .= "</div>";
                            $data .= "</div>";

                            unset($totales);
                            unset($movimientos);
                            unset($realizado);
                            unset($resultset2);
                        }// Fin num conceptos >0

                       }//Fin si el paciente tiene movimiento 
                 
                      } //fin while pacientes

                      
      				  } //Fin num pacientes asignados al estudio
      			  } // Fin if 'Mopfac'

              else{ // * * * * * * * * * * * * * * * * * * * OPCION 'Mopcan' o 'Moprea'

                    list($tipoide, $numeroide) = explode('-', $wcodigopa);
                    $data = '';
        
                    // Definición del número de columnas de la tabla
                    $numcol  = $numfre+20;
                    $num_pac = 0;

                    // Consulta de los movimientos realizados para el estudio
                    $qlis = " SELECT Concod, Condes, Conval, Mopvfr, Moprea, Mopcan, Mopnvi, Mopfac, Moptvi
              							  FROM ".$wbasedato."_000003, ".$wbasedato."_000008
              							  WHERE  Mopcol = '".$wcodigola."'
              									 AND Mopcoe = '".$wcodigoes."' 
              									 AND Mopide = '".$numeroide."'
              									 AND Moptid = '".$tipoide."' 
              									 AND Mopcoc = Concod
              									 AND Conest = 'on'
              									 AND Mopest = 'on'
              							  ORDER BY Concod, Condes, Mopvfr";

                    $reslis = mysql_query($qlis,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$qlis." - ".mysql_error());
                    $numlis = mysql_num_rows($reslis);
                    

                    //Llevar a un array la consulta de movimientos por Estudio
                    $resultset2 = array();
                    
                    while ($row = mysql_fetch_assoc($reslis)) 
                           $resultset2[] = $row;

                    $dataenc    = "";


                    // Consultar datos del Paciente
                    $q  = " SELECT A.Pactid, A.Pacced, concat (A.Pacno1,' ',A.Pacno2,' ',A.Pacap1,' ',A.Pacap2) as Nompac
                            From ".$wbasedato."_000011 A 
                            Where A.Pactid = '".$tipoide."' 
                                  AND A.Pacced = '".$numeroide."'";

                    $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
                    $num = mysql_num_rows($res);

                    //Mostrar fila con datos del paciente
                    if  ($num > 0){

                        $row = mysql_fetch_assoc($res);
                        $dataenc .= "<tr><th colspan='".$numcol."' class=fila1><b>".$row['Nompac']." | ".$row['Pactid'].$row['Pacced']."</b></th></tr>";
                    } 

                    // Variable que contendrá el encabezado de las cuadrículas
                    $data1 .= "<tr class='encabezadoTabla'>";
                    $data4 .= "<tr class='encabezadoTabla'>";

                    for ($j=1;$j<=$numvis;$j++)
                    {
                        $data1 .= $data2;
                        $data4 .= $datvi;
                    }

                    $data1 .= "</tr>";
                    $data4 .= "</tr>";
                    $dataenc .= "<tr class='encabezadoTabla'> <th align='center' rowspan='3' width='150px'> CONCEPTOS </th>"; 
                    
                    //Adiciona encabezado de frecuencias
                    for ($j=1;$j<=$numvis;$j++)
                    {
                        $dataenc .= $data3; 
                    }
                  
                    // Columna donde se mostrará la frecuencia del concepto
                    $dataenc .= "<th align='center' rowspan='3' width='40px'> Total <br> Eventos </th>";
                    // Columna donde se mostrará el costo del concepto
                    $dataenc .= "<th align='center' rowspan='3' width='80px'> Costo en <br> ".$row_est['Cestmo']." </th>";
                    // Columna donde se mostrará el total de costo x frecuencia
                    $dataenc .= "<th align='center' rowspan='3' width='70px'> Total <br> Costo </th>";
                    $dataenc .= "</tr>";

                    $dataenc .= $data1;
                    $dataenc .= $data4;

                    if  ($numcon > 0)
                        {
                          
                          $j=1;

                          // Recorrer la consulta de conceptos
                          while($row_con = mysql_fetch_assoc($res_con))
                          {
              								
                             //Seleccionar el color del fondo de la fila
              							if (is_int ($j/2))
              									 $wcf="fila1";
              							else
              									 $wcf="fila2";

              							$aux_concepto = $row_con['Concod'];
              							$data .= "<tr class='".$wcf."' onclick='ilumina(this,\"".$wcf."\")'>";

              							// Celda con el concepto para cada recorrido
              							$data .= "<td width='80px'>".$row_con['Concod']." - ".$row_con['Condes']."</td>";
                         
            								///////////////////// INICIO
            								switch ($wtipo){

            								case 'Mopcan':  
            								
            									$cont_frecuencia =0;

                              foreach ($resultset2 as $rowlis)
                              {

                                  $frecuencia = $rowlis['Mopvfr'];
                                  $numerovisi = $rowlis['Mopnvi'];
                                  $tipovisita = $rowlis['Moptvi'];

                                  if ($rowlis['Concod'] == $aux_concepto){                                       

                                       $movimientos[$frecuencia][$numerovisi][$tipovisita][$aux_concepto] = $rowlis['Mopcan'];
                                       $totales[$frecuencia][$numerovisi][$tipovisita]                   += $rowlis['Mopcan']; 
                                       $facturado[$frecuencia][$numerovisi][$tipovisita][$aux_concepto]   = $rowlis['Mopfac'];
                                       $cont_frecuencia += $rowlis['Mopcan'];  
                                  }

                              }
            									          									
            									// Ciclo que imprime los datos de la fila 
            									// Con base en los arreglos $frecuencias y $movimientos
            									$cfil   = 0;
            									$numcol = 0;

            									for ($k=1;$k<=$numvis;$k++)
            									{
              										for ($i=1;$i<=$numfre;$i++)
              										{
                										  $cfil ++;
                										  $numcol ++;
														          $varcelcal  = 'columna'.$numcol;
                										  $frecuencia = $frecuencias[$i];
                                      $tipovisita = $tiposvisita[$i];
														          $nfactur    = $facturado[$frecuencia][$k][$tipovisita][$aux_concepto];
														  
        														  if (intval($nfactur) > 0)
        															   $vartitle   = 'Frecuencia '.$frecuencia.' ,Factura: '.$nfactur;
        														  else 
        															   $vartitle   = 'Frecuencia '.$frecuencia;

        														  // Confirmar si se encuentra Facturado
        														  if (isset($facturado[$frecuencia][$k][$tipovisita][$aux_concepto]) && (intval($facturado[$frecuencia][$k][$tipovisita][$aux_concepto]) >0)){
        															  $numfactura = $facturado[$frecuencia][$k][$tipovisita][$aux_concepto];
        															  $estfactura = 'disabled';
        														  }    
        														  else{ 
        															  $numfactura = ''; 
        															  $estfactura = ''; 
        														  }    
                											
                											$data .= '<td align="center" width="30px" class="'.$varcelcal.'" class="mostrarentidad" title="'.$vartitle.'" Onclick ="iluminacolumna(this,\''.$varcelcal.'\');" nowrap >';  


                											if (isset($movimientos[$frecuencia][$k][$tipovisita][$aux_concepto]) && $movimientos[$frecuencia][$k][$tipovisita][$aux_concepto]){

                											   $data .= " <input type='text' id='cfv-".$j."-".$cfil."' name='cfv-".$j."-".$cfil."' size='2' value='".$movimientos[$frecuencia][$k][$tipovisita][$aux_concepto]."' onchange='cambiar_valores(this)' onkeypress='return justNumbers(event);' ".$estfactura."/> ";
                                       }
                											else{

                											   $data .= " <input type='text' id='cfv-".$j."-".$cfil."' name='cfv-".$j."-".$cfil."' size='2' value='' onchange='cambiar_valores(this)'  onkeypress='return justNumbers(event);' ".$estfactura."/> ";
                                      }
                												
                											$data .= "</td>";
              										}
            									}
            									break;
            		
            								
            								case 'Moprea':
            								
            									$cont_frecuencia =0;
            									// Ciclo para asignar los datos de la fila al arreglo
                              
                              foreach ($resultset2 as $rowlis)
                              {

                                  $frecuencia = $rowlis['Mopvfr'];
                                  $numerovisi = $rowlis['Mopnvi'];
                                  $tipovisita = $rowlis['Moptvi'];

                                  if ($rowlis['Concod'] == $aux_concepto){                                       

                                       $movimientos[$frecuencia][$numerovisi][$tipovisita][$aux_concepto] = $rowlis['Mopcan'];
                                       $realizado[$frecuencia][$numerovisi][$tipovisita][$aux_concepto]   = $rowlis['Moprea'];
                                       $facturado[$frecuencia][$numerovisi][$tipovisita][$aux_concepto]   = $rowlis['Mopfac']; 
                                       if($rowlis['Moprea']=='on')
                                       {
                                         $totales[$frecuencia][$numerovisi][$tipovisita]   += $rowlis['Mopcan'];                                       
                                         $cont_frecuencia += $rowlis['Mopcan'];  
                                       }

                                  }
                                  
                              }
            									
            									// Ciclo que imprime los datos de la fila 
            									// Con base en los arreglos $frecuencias y $movimientos
            									$cfil=0;
            									$numcol=0;
              								for ($k=1;$k<=$numvis;$k++)
              									{
                									  for ($i=1;$i<=$numfre;$i++)
                									  {                                                                            
                    										$cfil ++;
                    										$numcol ++;
                    										$varcelcal  = 'columna'.$numcol;
                    										$frecuencia = $frecuencias[$i];
                                        $tipovisita = $tiposvisita[$i];
															          $nfactur    = $facturado[$frecuencia][$k][$tipovisita][$aux_concepto];
															
          															if (intval($nfactur) > 0)
          																  $vartitle   = 'Frecuencia '.$frecuencia.' ,Factura: '.$nfactur;
          															else 
          																  $vartitle   = 'Frecuencia '.$frecuencia;

          															// Confirmar si se encuentra Facturado
          															if (isset($facturado[$frecuencia][$k][$tipovisita][$aux_concepto]) && (intval($facturado[$frecuencia][$k][$tipovisita][$aux_concepto] ) >0)){
          																  $numfactura = $facturado[$frecuencia][$k][$tipovisita][$aux_concepto];
          																  $estfactura = 'disabled';
          															}    
          															else{ 
          																  $numfactura = ''; 
          																  $estfactura = ''; 
          															}                         										

                    										$data .= '<td align="center" width="30px" class="'.$varcelcal.'"  class="mostrarentidad" title="'.$vartitle.'" Onclick ="iluminacolumna(this,\''.$varcelcal.'\');" nowrap >';
                    										
                    										if (isset($movimientos[$frecuencia][$k][$tipovisita][$aux_concepto] ) && $movimientos[$frecuencia][$k][$tipovisita][$aux_concepto] )
                    										{

                      										  $data .=  $movimientos[$frecuencia][$k][$tipovisita][$aux_concepto] ." ";
                      										  
                      										  if(isset($realizado[$frecuencia][$k][$tipovisita][$aux_concepto] ) && $realizado[$frecuencia][$k][$tipovisita][$aux_concepto] =='on')
                      											$data .= " <input type='checkbox' id='cfv-".$j."-".$cfil."' name='cfv-".$j."-".$cfil."' value='".$movimientos[$frecuencia][$k][$tipovisita][$aux_concepto] ."' onclick='cambiar_valores(this)' checked ".$estfactura."/> ";
                      										  else
                      											$data .= " <input type='checkbox' id='cfv-".$j."-".$cfil."' name='cfv-".$j."-".$cfil."' value='".$movimientos[$frecuencia][$k][$tipovisita][$aux_concepto] ."' onclick='cambiar_valores(this)' ".$estfactura." /> ";
                    											
                    										}
                											
                										    $data .= "</td>";
                									  }
              									}
            									break;
								
							  }
						  
								/********************************************************
								******* DATOS POR PACIENTE ******************************
								********************************************************/
								
								// Total eventos
								$data .=  "<td align='right' width='25px'> <div id='eventos-".$j."'>".$cont_frecuencia."</div> </td>";
								$totales['cantidad']['1'] += $cont_frecuencia;   // Total de frecuencias para el concepto

								// Costo unitario del concepto
								$data .=  "<td align='right' width='50px'> <div id='costo-".$j."'>".number_format($row_con['valorcon'],2,'.',',')."</div> </td>";
								$totales['costo']['1'] += $row_con['valorcon'];  // Total de costos unitarios de los conceptos

								// Costo total de el valor del concepto X las frecuencias de éste
								$total_concepto = $row_con['valorcon'] * $cont_frecuencia;

								$data .=  "<td align='right' width='60px'> <div id='total-".$j."'>".number_format($total_concepto,2,'.',',')."</div> </td>";
								$totales['total']['1'] += $total_concepto;   // Total conceptos X frecuencias para el paciente

								/*******************************************************/
								
								$data .=  "</tr>";

								// Se reinicia el arreglo para la nueva fila
								unset($movimientos);
								unset($realizado);
								$j++;
							} // Fin de recorrido de conceptos   
           
              }
           
              // Pie de la tabla para mostrar los totales
              $data .=  "<tr class='encabezadoTabla'>";
              $data .=  "<td align='center'>";
              $data .=  "<div align='left'> TOTALES </div>";
              $data .=  "</td>";

              // Ciclo para llenar el pie de página de la tabla con los totales de las frecuencias
              $varcoltot = 0;
              for ($k=1;$k<=$numvis;$k++)
              {
                  for ($i=1;$i<=count($frecuencias);$i++)
                  {
                      $varcoltot++;
                      $indice_frecuencia = $frecuencias[$i];
                      $tipos_visita      = $tiposvisita[$i]; 
                      $numero_visita     = $k;

                      if (isset($totales[$indice_frecuencia][$numero_visita][$tipos_visita]))
                         $total_frecuencia = $totales[$indice_frecuencia][$numero_visita][$tipos_visita];
                      else
                         $total_frecuencia = 0;
                        
                      $data .=  "<td align='center'>";
                      $data .=  "<div id='frecuencia-".$varcoltot."'>".$total_frecuencia."</div> ";
                      $data .=  "</td>";
                  }
              }

              
              // Celda donde se muestra el total de la sumatoria de la cantidad de frecuencias
              $data .=  "<td align='right' width='25px'> <div id='eventostot' name='eventostot'>".number_format($totales['cantidad']['1'],0,'.',',')."</div> </td>";

              // Celda donde se muestra el total de la sumatoria del costo unitario de los conceptos
              $data .=  "<td align='right' width='50px'> <div id='costotot' name='costotot' >".number_format($totales['costo']['1'],2,'.',',')."</div> </td>";

              // Celda donde se muestra el total de la sumatoria de costos unitario de los conceptos X las frecuencias
              $data .=  "<td align='right' width='60px'> <div id='totaltot' name='totaltot' >".number_format($totales['total']['1'],2,'.',',')."</div> </td>";
              
              $data .=  "</tr>";

              } // fin else (solo Mopcan y Moprea)
              
              if ($numlis>0)
                 {$respuesta['nuevo'] = 'N';}
              
              $respuesta['numvis']  = $numvis;
              $respuesta['numfre']  = $numfre;
              $respuesta['detalle'] = $data;
              $respuesta['encabez'] = $dataenc;
              
              echo json_encode($respuesta);
              return;
         }

         
      
        //Consultar los pacientes asignado al Estudio seleccionado
        if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarPacientes"){

            $data = array('error'=>0,'html'=>'','mensaje'=>'');

            $q  = " SELECT Paecol,Paecoe, concat (B.Pactid,'-',B.Pacced) as Idepac, 
                           concat (B.Pacno1,' ',B.Pacno2,' ',B.Pacap1,' ',B.Pacap2) as Nompac
                    From ".$wbasedato."_000007 A
                           Inner Join ".$wbasedato."_000011 B on  A.Paeide = B.Pacced
                           AND A.Paetid = B.Pactid
                    Where A.Paecol='".$wcodigolab."'
                           AND A.Paecoe='".$wcodigoes."' "  ;

            $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

            $num = mysql_num_rows($res);

            if  ($num == 0)
                {
                    $data['error']   = 1;
                    $data['mensaje'] = 'NO HAY PACIENTES ASIGNADOS AL ESTUDIO SELECCIONADO';
                }
            else 
                {
                    $data['html'] .= '<option selected></option>';
                    while($row = mysql_fetch_assoc($res))
                    {
                      $data['html'] .= "<option value='".$row['Idepac']."'>".$row['Nompac']."</option>";
                    }
                }  

            echo json_encode($data);
            return;
     }


     //Consultar los estudios según el laboratorio seleccionado 
     if (isset($_POST["accion"]) && $_POST["accion"] == "ConsultarEstudios"){

          $data = array('error'=>0,'html'=>'','mensaje'=>'');

          $q  = " SELECT Cescoe
                  From ".$wbasedato."_000004 
                  Where Cescol='".$wcodigola."'"  ;

          $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());

          $num = mysql_num_rows($res);

          if ($num == 0)
              {
                  $data['error'] = 1;
                  $data['mensaje'] = 'NO HAY ESTUDIOS ASIGNADOS AL LABORATORIO SELECCIONADO';
              }
          else 
              {
                  while($row = mysql_fetch_assoc($res))
                  {
                    $data['html'] .= "<option value='".$row['Cescoe']."'>".$row['Cescoe']."</option>";
                  }
              }  

          echo json_encode($data);
          return;
     }


     //Consulta si un centro de costos es de urgencias
     function esUrgencias($conex, $servicio){

        global $wbasedato;

        $es = false;

        $q = "SELECT Ccourg
            FROM ".$wbasedato."_000011
             WHERE Ccocod = '".$servicio."' ";

        $err = mysql_query($q,$conex);
        $num = mysql_num_rows($err);

        if($num>0)
        {
          $rs = mysql_fetch_array($err);

          ($rs['Ccourg'] == 'on') ? $es = true : $es = false;
        }

        return $es;
     }

    
     function codificar($concepto_dec)
     {
        return str_replace(".","__",$concepto_dec);
     }


     function decodificar($concepto_cod)
     {
        return str_replace("__",".",$concepto_cod);
     }


     function consultarLaboratorios($wbasedato,$conex,$wemp_pmla){ 

        $strtipvar = array();
        $q  = " SELECT Labnit, Labnom"
             ."  From ".$wbasedato."_000001 ";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        while($row = mysql_fetch_assoc($res))
             {
               $strtipvar[$row['Labnit']] = utf8_encode($row['Labnom']);
             }
        return $strtipvar;

     }

     function consultarEstudios($wbasedato,$conex,$wemp_pmla){ 
        
        $arr_documentos = array();

        $q  = " SELECT A.Paefec, A.Paecol, A.Paecoe, A.Paehis, A.Paetid, A.id, 
                       A.Paeide, B.Labnom, concat (C.Pacno1,' ',C.Pacno2,' ',C.Pacap1,' ',C.Pacap2) as Nompac
                FROM ".$wbasedato."_000007 A    
                      Inner Join ".$wbasedato."_000001 B on  A.Paecol = B.Labnit
                      Inner Join ".$wbasedato."_000011 C on  A.Paeide = C.Pacced
                            AND A.Paetid = C.Pactid
                WHERE A.Paeest ='on' Order by A.Paefec desc";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        while($row = mysql_fetch_assoc($res)){

              $arr_documentos[] = array( "wpaefec"  => $row["Paefec"],
                                         "wpaecol"  => $row["Paecol"],
                                         "wlabnom"  => $row["Labnom"],
                                         "wpaecoe"  => $row["Paecoe"],
                                         "wpaehis"  => $row["Paehis"],
                                         "wpaetid"  => $row["Paetid"].' - '.$row["Paeide"],
                                         "wnompac"  => $row["Nompac"],
                                         "wid"      => $row["id"],
                                         "widenti"  => $row["Paeide"],
                                         "wtipoid"  => $row["Paetid"]);
        }
        
        return $arr_documentos;
      }

         
     // *****************************************         FIN PHP         ********************************************

  ?>
  <html>
  <head>
    <title>Pacientes por estudio</title>
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
    <script type='text/javascript' src='../../../include/root/jquery.stickytableheaders.js'></script>      

    <!--< Plugin para el select con buscador > -->

    <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.css" />
    <link rel="stylesheet" type="text/css" href="../../../include/root/jquery.multiselect.filter.css" />
    <script type="text/javascript" src="../../../include/root/jquery.multiselect.js"></script>
    <script type="text/javascript" src="../../../include/root/jquery.multiselect.filter.js"></script>
    <script type="text/javascript" src="../../../include/root/prettify.js"></script>
    <script src="../../../include/root/jquery-ui-timepicker-addon.js" type="text/javascript" ></script>
    <script src="../../../include/root/jquery-ui-sliderAccess.js" type="text/javascript" ></script>       
    <script type="text/javascript">

      var celda_ant  ="";
      var celda_ant_clase="";
      var arrgrabar  = new Array();
      var contgrabar = 0;

      $(document).ready(function(){

          $('input#id_search_estudios').quicksearch('table#tabla_documentos tbody tr');     

          $("#tblpaciente").stickyTableHeaders();

          $(".mostrartooltip").tooltip({track: true, delay: 0, showURL: false, opacity: 0.95, left: 0 });

          $("#txtfecpag").datepicker({
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

          // Configurar campos multiselect
          $('#optlaboratorio,#optestudio,#optpaciente').multiselect({
              numberDisplayed: 1,
              selectedList:1,
              multiple:false
          }).multiselectfilter();

          // 
          $("#divhistoria").dialog({
              autoOpen: false,
              height: 400,
              width: 1050,
              position: ['left+50', 'top+30']
          });

                                        
       });

       

       //Clase que agrega el scroll en la parte superior del div
       function DoubleScroll(element) {
            var scrollbar= document.createElement('div');
            scrollbar.appendChild(document.createElement('div'));
            scrollbar.style.overflow= 'auto';
            scrollbar.style.overflowY= 'auto';
            scrollbar.firstChild.style.width= '4000px'; //element.scrollWidth+
            scrollbar.firstChild.style.paddingTop= '0px';
            scrollbar.firstChild.appendChild(document.createTextNode('\xA0'));
            scrollbar.onscroll= function() {
                element.scrollLeft= scrollbar.scrollLeft;
            };
            element.onscroll= function() {
                scrollbar.scrollLeft= element.scrollLeft;
            };
            element.parentNode.insertBefore(scrollbar, element);
       }


       //FUNCION QUE PERMITE GENERAR UNA VENTANA EMERGENTE CON UN PATH ESPECIFICO
       function ejecutar(path)
       {
      
           $.blockUI({ message:  '<img src="../../images/medical/ajax-loader.gif" >',
                  css:  {
                        width:  'auto',
                        height: 'auto'
                      }
           });
               
          window.open(path,'','fullscreen=1,status=0,menubar=0,toolbar=0,location=0,directories=0,resizable=0,scrollbars=1,titlebar=0');
        
          setTimeout(function(){
              $.unblockUI();
          }, 3000);
        
       }


       function ValidarFactura(valfac){


               $.post("movimientos_pacientes_x_estudio.php",
               {
                  consultaAjax:   true,
                  async    :      false,
                  accion   :      'ValidarFactura',
                  wemp_pmla:      $("#wemp_pmla").val(),
                  wfactura :      valfac
               }, function(result){

                    if (result=='S'){                        
                        jAlert('La factura ya se encuentra en el sistema');
                        return;
                    }
                      
               });
       }


       function Mostrardetalle(obj){   

            $( "#tblbotones" ).hide();

            if  (obj == 'Mopfac'){

                $(".verpaciente").hide();
                $( "#tblpaciente" ).hide();
                $( "#divfacturar" ).show();
                $( "#divfacturar" ).animate({
                  width: "100%",
                  marginLeft: "0in",
                  borderWidth: "1px"
                }, 1000);
            }
            else{

                $( ".verpaciente" ).show(); 
                $( "#divfacturar" ).hide();                
            }
       }


       // Consulta los Pacientes asignados a un Estudio específico y lo agrega al campo optpaciente
       function cargarselpaciente(valor) {
          
           var wemp_pmla = $("#wemp_pmla").val();
           var wlaborato = $("#optlaboratorio").val();

           $.post("movimientos_pacientes_x_estudio.php",
               {
                  consultaAjax:   true,
                  accion:         'ConsultarPacientes',
                  wemp_pmla:      wemp_pmla,
                  wcodigoes:      valor.value,
                  wcodigolab:     wlaborato.toString()
               }, function(data_json){
                
                if (data_json.error == 1)
                {
                   jAlert(data_json.mensaje, 'Alerta');
                }
                else
                {         
                    $('#optpaciente').html(data_json.html);
                    $('#optpaciente').multiselect({
                                   position: {
                                    my: 'left bottom',
                                    at: 'left top'                                
                                   },
                                   
                    }).multiselectfilter();
                                          
                    $('#optpaciente').multiselect("refresh");
                }

           },"json");
       }


       // Consulta los Estudios segun el laboratorio seleccionado y lo agrega al campo optestudio 
       function cargarselestudio(valor,opcion){
          
           var wemp_pmla = $("#wemp_pmla").val();
           
           if  (opcion =='1'){
               var wlabor  = valor.value;
           }
           else{
               var wlabor  = valor;
           }

           $.post("movimientos_pacientes_x_estudio.php",
               {
                  consultaAjax:   true,
                  accion:         'ConsultarEstudios',
                  wemp_pmla:      wemp_pmla,
                  wcodigola:      wlabor
               }, function(data_json){
                
                if (data_json.error == 1)
                {
                   jAlert(data_json.mensaje, 'Alerta');
                }
                else
                {         
                   $('#optestudio').html(data_json.html);
                   $('#optestudio').multiselect({
                               position: {
                                 my: 'left bottom',
                                 at: 'left top'                                
                               },
                                 
                   }).multiselectfilter();
                                        
                   $('#optestudio').multiselect("refresh");
                }

           },"json");

       }    


       function cargarestudio(obj){

         var wemp_pmla = $("#wemp_pmla").val();
         var vid       = $(obj).attr('id');
         var vpaci     = vid.split('|');
         selectedlab   = vpaci[0];       
         selectedest   = vpaci[1];   
         selectedlabn  = vpaci[4];  
         selectedpac   = vpaci[2]+'-'+vpaci[3];       
        
         $pos = 0;

         $('#optlaboratorio option').each(function (){
                var option_val = this.value;               
                if(selectedlab == option_val){
                   document.getElementById('optlaboratorio').selectedIndex = $pos;
                   document.getElementById('optlaboratorio').selectedText  = selectedlabn;
                   document.getElementById("optlaboratorio").options.value = selectedlab;                                                   
                }
                $pos++;
         });
       
         Consultar(selectedest,selectedpac,'2');

       }


       function cambiar_valores(objeto){

            var ide       = $(objeto).attr('id');
            var wnumfrecu = $("#wnumefre").val();
            var wtipo     = $("#wtipo").val();

            switch(wtipo) {

                case 'Mopfac':

                      var vpaci      =  objeto.value.split('|');
                      var selecpaci  =  vpaci[5];  
                      vtabla         =  'tbl_'+selecpaci;
                      vtabpac        =  selecpaci;
                      var totfilas   =  document.getElementById(vtabla).rows.length-4;
                      var totcolumna =  document.getElementById(vtabla).rows[4].cells.length-4;  

                      if (document.getElementById(ide).checked==true){

                          contgrabar ++;
                                              
                          var columna1    =  totcolumna+2;
                          var vconta      =  0;
                          var pactotal1   =  0;
                          var pactotal2   =  0;
                          var pactotal3   =  0;
                          var suma        =  0;
                          var valoreve    =  0;
         
                          // Llenar el array que contiene el detalle de la factura
                          arrgrabar[contgrabar] = objeto.value;

                          // Tomar el Costo del Concepto y llevarlo al costo total del Estudio (datos factura)
                          var valorcos  =  objeto.value.split('|');
                          var valoract  =  $("#txtvalfra").val() ;

                          //Acumular el valor de la Factura (datos factura)
                          var numvalor = parseInt(valoract*1)+parseInt(valorcos[0]) ;
                          $("#txtvalfra").val(numvalor);  

                      }
                      else{

                           for ( var h = 1; h <= arrgrabar.length; h++ ){

                                if (arrgrabar[h]==objeto.value){
                                   delete arrgrabar[h] ;     
                                }
                           }
                                  
                           var valorcos  =  objeto.value.split('|');
                           var valoract  =  $("#txtvalfra").val() ;

                           //Acumular el valor de la Factura (datos factura)
                          var numvalor = parseInt(valoract*1)-parseInt(valorcos[0]) ;
                          $("#txtvalfra").val(numvalor);

                      }

                      SumarTabla('3',vtabpac,totcolumna,totfilas);

                      break;

                case 'Moprea':

                      contgrabar ++;
                      var table       =  document.getElementById('tblpaciente');
                      var totfilas    =  document.getElementById('tblpaciente').rows.length-9;                  
                      var totcolumna  =  $("#wnumevis").val() * $("#wnumefre").val();
                      var columna1    =  totcolumna+2;

                      SumarTabla('1','tblpaciente',totcolumna,totfilas);

                      break;

                case 'Mopcan':

                      contgrabar ++;
                      var table       =  document.getElementById('tblpaciente');
                      var totfilas    =  document.getElementById('tblpaciente').rows.length-9;                  
                      var totcolumna  =  $("#wnumevis").val() * $("#wnumefre").val();
                      var columna1    =  totcolumna+2;

                      SumarTabla('2','tblpaciente',totcolumna,totfilas);

                      break;
            }
       }


       function SumarTabla(vtipo,vtabla,vtotcolumna,vtotfilas){

                  pactotal1 = 0;
                  pactotal2 = 0;
                  pactotal3 = 0;

                  if (vtipo != '3' )
                      vtotfilas = (vtotfilas*1)+1;

                  for ( var j = 1; j <= vtotcolumna; j++ ){

                       if (vtipo=='3')

                          document.getElementById(vtabla+'-frecuencia-'+j).innerHTML = 0;
                       else

                          document.getElementById('frecuencia-'+j).innerHTML = 0;
                  }


                  for (  var i = 1; i < vtotfilas; i++ ){ 

                      var vconcel    = 0;
                      var valcolumna = 0;

                      for ( var j = 1; j <= vtotcolumna; j++ ){

                           valcolumna ++;
                           valoreve  = 0;
                           
                           //Si el movimiento es por facturacion o realizacion
                           switch(vtipo) {

                                  case '1': // opcion sumar cantidades realizadas

                                        vcampo    = "cfv-"+i.toString()+"-"+j.toString();

                                        var myElem = document.getElementById(vcampo);

                                        if  (myElem !== null){                  

                                            if  (document.getElementById(vcampo).checked==true){

                                                 valoreve = document.getElementById(vcampo).value;

                                            }     
                                            else{
                                                 valoreve = 0;
                                            }
                                        }  

                                        break;


                                  case '2':// opcion sumar cantidades

                                        vcampo    = "cfv-"+i.toString()+"-"+j.toString();
                                        valoreve = document.getElementById(vcampo).value;
                                        break;


                                  case '3':// opcion sumar item facturados

                                        var vcamfac = vtabla+"-cfv-"+i.toString()+"-"+j.toString(); 

                                        var myElem  = document.getElementById(vcamfac);

                                        if  (myElem !== null){                  

                                            if  (document.getElementById(vcamfac).checked==true){

                                                 valorpri      = document.getElementById(vcamfac).value;
                                                 var vsumar    = valorpri.split('|');                                                 
                                                 var valoreve  = vsumar[6];

                                            }     
                                            else{

                                                 valoreve = 0;
                                            }
                                        }  

                                        break;

                           }           

                           vconcel += valoreve*1 ;    

                           if (vtipo=='3')

                              var nomfre = vtabla+'-frecuencia'+'-'+j ;

                           else 
                              
                              var nomfre = 'frecuencia'+'-'+j ;

                           var aRemplazar=document.getElementById(nomfre).innerHTML;
                           var remplazado=aRemplazar.replace(".", ""); 
                           var remplazadof=remplazado.replace(",", "");                                                 

                           valorcol = (remplazado*1) + (valoreve*1);

                           document.getElementById(nomfre).innerHTML = number_format(valorcol, 0, '.', ',');                     
                    
                    }

                    if (vtipo=='3'){

                         var nomeve = vtabla+'-eventos'+'-'+i;
                         var nomcos = vtabla+'-costo'+'-'+i;
                         var nomtot = vtabla+'-total'+'-'+i;

                    }
                    else{

                         var nomeve = 'eventos'+'-'+i;
                         var nomcos = 'costo'+'-'+i;
                         var nomtot = 'total'+'-'+i;
                    }

                    document.getElementById(nomeve).innerHTML = number_format(vconcel, 0, '.', ',');
                    var eventos = document.getElementById(nomeve).innerHTML;
                    var costo   = document.getElementById(nomcos).innerHTML;                    
                    var costo1  = costo.replace(/,/g , "");
                    
                    var total   = eventos*parseFloat(costo1.replace(',','.').replace(' ',''));

                    if (costo.length >= 5)
                        total = total * 1;

                    document.getElementById(nomtot).innerHTML = number_format(total, 2, '.', ',');

                    pactotal1 += vconcel*1;
                    pactotal2 += costo*1;
                    pactotal3 += total*1;

              }

              if (vtipo=='3'){
                  
                  var nomeve = vtabla+'-eventostot';
                  var nomcos = vtabla+'-costotot';
                  var nomtot = vtabla+'-totaltot';

              }    
              else{ 

                  var nomeve = 'eventostot';
                  var nomcos = 'costotot';
                  var nomtot = 'totaltot';
                  
              }                    

              document.getElementById(nomeve).innerHTML = number_format(pactotal1, 0, '.', ',');
              document.getElementById(nomcos).innerHTML = number_format(pactotal2, 2, '.', ',');
              document.getElementById(nomtot).innerHTML = number_format(pactotal3, 2, '.', ',');

       }

       
       function Grabar(){

         if ( $("#wtipo").val() == 'Mopfac' && ($("#txtnrofra").val() == '' || $("#txtfecpag").val() == '' || $("#txtvalfra").val() == '') ){
              jAlert('Falta diligenciar la informaci\u00f3n de Facturaci\u00f3n');
              return;
         }    

         if ($("#wedicion").val()=='N'){
              jAlert('Falta diligenciar el detalle del Estudio');
              return;
         }

         if ( $("#wtipo").val() == 'Mopfac'){


             $.post("movimientos_pacientes_x_estudio.php",
                   {
                      consultaAjax:   true,
                      async    :      false,
                      accion   :      'ValidarFactura',
                      wemp_pmla:      $("#wemp_pmla").val(),
                      wfactura :      $("#txtnrofra").val()
                   }, function(result){

                        if (result=='S'){

                            jAlert('La factura ya se encuentra en el sistema');
                            return;
                        }
                        else{

                            if ($("#wedicion").val()=='S')
                                GrabarFactura();
                            else  
                                jAlert('Falta consultar informaci\u00f3n');

                        }
                          
                   });
          }
          else{

               if ($("#wedicion").val()=='S')
                   GrabarFactura();

               else  
                   jAlert('Falta consultar informaci\u00f3n');
          }

        } 


      
        function  GrabarFactura(){      
                 
          var warrdetalle = new Array();
          var wemp_pmla   = $("#wemp_pmla").val();
          var wlaborato   = $("#optlaboratorio").val(); 
          var westudio    = $("#optestudio").val();
          var wpaciente   = $("#optpaciente").val();
          var wedicion    = $("#wedicion").val();
          var wtipo       = $("#wtipo").val();
          var wnumfac     = $("#txtnrofra").val(); 
          var wfecfac     = $("#txtfecpag").val();
          var wcosfac     = $("#txtvalfra").val();
          var wnumfrecu   = $("#wnumefre").val();
          var totcolumna  = $("#wnumevis").val()*$("#wnumefre").val();
          var table       = document.getElementById('tblpaciente');
          var totfilas    = document.getElementById('tblpaciente').rows.length;
          var wcondato    = 0;
          var wcolumna    = 0;

          if  (document.getElementById('chkpagfac').checked==true){
              var wpagada = 'on';
          }    
          else{
              var wpagada = 'off';
          }    


          if ( $("#wtipo").val() == 'Mopfac'){

              var str1  = $("#warrgrabar").val() ;
          }
          else{
          
              //Seleccionar cada celda: contenido, numero de visita y frecuencia
              if (totfilas>3){

                   for( var i = 1; i < totfilas-8; i++ ){
                      var nvisita = 1;
                      var vconcep = table.rows[i+7].cells[0].innerHTML;

                      for( var j = 1; j <= totcolumna; j++ ){   
                         
                         wcondato++;
                         wcolumna++;
                         
                         if (wtipo=='Moprea'){

                            vcampo     = "cfv-"+i.toString()+"-"+j.toString();                         
                            var myElem = document.getElementById(vcampo);

                            if (myElem !== null){

                               if (document.getElementById(vcampo).checked == false && document.getElementById(vcampo).checked!=null){
                                  
                                  vconcel = $('input:checkbox[name="'+vcampo+'"]:checked').val();
                                  vfrecel = table.rows[2].cells[j-1].innerHTML;
                                  vfretvi = table.rows[3].cells[j-1].innerHTML;
                                  vnumcel = Math.round(j/wnumfrecu);
                                  warrdetalle[wcondato] = 'off'+' | '+vfrecel+' | '+nvisita+' | '+vconcep+' | '+vfretvi;
                               } 

                               if (document.getElementById(vcampo).checked == true && document.getElementById(vcampo).checked!=null){
                                  
                                  vconcel = $('input:checkbox[name="'+vcampo+'"]:checked').val();
                                  vfrecel = table.rows[2].cells[j-1].innerHTML;
                                  vfretvi = table.rows[3].cells[j-1].innerHTML;
                                  vnumcel = Math.round(j/wnumfrecu);
                                  warrdetalle[wcondato] = 'on'+' | '+vfrecel+' | '+nvisita+' | '+vconcep+' | '+vfretvi;
                               }
                            }
                            
                         }

                         if (wtipo=='Mopcan'){
                              
                              vconcel = document.getElementById("cfv-"+i.toString()+"-"+j.toString()).value;
                              vfrecel = table.rows[2].cells[j-1].innerHTML;
                              vfretvi = table.rows[3].cells[j-1].innerHTML;
                              vnumcel = Math.round(j/wnumfrecu);
                              warrdetalle[wcondato] = vconcel+' | '+vfrecel+' | '+nvisita+' | '+vconcep+' | '+vfretvi;

                         }                                      
                         
                         if (j % wnumfrecu == 0)
                            nvisita ++ ;

                      }
                   }                
               }
          }

          
          $.post("movimientos_pacientes_x_estudio.php",
                {
                    consultaAjax:  true,
                    accion      :  'GrabarEstudio',
                    wemp_pmla   :  wemp_pmla,
                    wcodigoes   :  westudio.toString(),
                    wcodigola   :  wlaborato.toString(),
                    wcodigopa   :  wpaciente.toString(),
                    wedicion    :  wedicion,
                    warrdetalle :  warrdetalle,
                    wtipo       :  wtipo,
                    wnumfac     :  wnumfac,
                    wfecfac     :  wfecfac,
                    wcosfac     :  wcosfac,
                    wpagada     :  wpagada,
                    warrgrabar  :  arrgrabar

                 }, function(respuesta){
                    
                    if (respuesta==1){

                       jAlert('El Movimiento ha sido Grabado');

                       if (wtipo=='Mopfac')
                       {
                          $("#txtnrofra").val('');
                          $("#txtvalfra").val('');
                          $("#txtfecpag").val('');
                       }
                    }   
                      
                 });

       }


       function Consultar(valor1,valor2,opcion){

          var wemp_pmla = $("#wemp_pmla").val();
          var wlaborato = $("#optlaboratorio").val();
          var wtipo     = $("#wtipo").val();'".$visita."' 
          
          if  (wtipo !== 'Mopfac' && opcion == '1' && ($("#optlaboratorio").val()==null || $("#optestudio").val()==null || $("#optpaciente").val()=='' || $("#optpaciente").val()==null) ){
              jAlert('Falta seleccionar informaci\u00f3n');
              return;
          }

          if  (wtipo == 'Mopfac' && opcion == '1' && ($("#optlaboratorio").val()==null || $("#optestudio").val()==null ) ){
              jAlert('Falta seleccionar informaci\u00f3n');
              return;
          }
          

          if  (opcion == '1'){

              var westudio  = $("#optestudio").val();
              
              if  ($("#optpaciente").val() !== null)
                  var wpaciente = $("#optpaciente").val();
              else
                  var wpaciente = '';
              
          }    
          else{

              var westudio  = valor1;
              var wpaciente = valor2;
          }   

          // Verificar si el usuario eligió la opción para facturar          
          if  ($("#wtipo").val() == "Mopfac"){
              Mostrardetalle('Mopfac');
              wpaciente='';
          }    
          else{
              $("#divfacturar").hide();
          }    


          //Llamado ajax para consultar el detalle de la tabla 
          $.post("movimientos_pacientes_x_estudio.php",
                 {
                    consultaAjax:   true,
                    accion:         'ConsultarDetalle',
                    wemp_pmla:      wemp_pmla,
                    wcodigoes:      westudio.toString(),
                    wcodigola:      wlaborato.toString(),
                    wcodigopa:      wpaciente.toString(),
                    wtipo:          wtipo
                 }, function(data){

                    $("#wedicion").val('S');
                    document.getElementById("tblpaciente").style.display  = "";
                    document.getElementById("tblbotones").style.display   = "";
                    $('.tableFloatingHeaderOriginal').removeAttr('style'); 

                    
                    if  ($("#wtipo").val() == "Mopfac"){

                        $("#tblpaciente").hide();
                        $("#divdetalle").show();
                        $("#divdetalle").empty();
                        $("#divdetalle").append(data.detalle);

                    }
                    else{

                        $("#divdetalle").hide();
                        $("#tblpaciente> thead").empty();                  
                        $("#tblpaciente> tbody").empty();
                        $("#tblpaciente thead").append(data.encabez);
                        $("#tblpaciente tbody").append(data.detalle);
                        
                        $("#edicion").val(data.nuevo);
                        $("#wnumevis").val(data.numvis);
                        $("#wnumefre").val(data.numfre);
                    }

                    // agregar la clase que convierte un div en un formato tipo acordión
                    $(".accordionFiltros").accordion({
                        collapsible: true,
                        heightStyle: "content"
                    });

                 },"json");
       }


       function Verhistoria(){

         var wemp_pmla    = $("#wemp_pmla").val();

         if  ($("#optpaciente").val() !== null)
             var wpaciente = $("#optpaciente").val();
         else
             var wpaciente = '';

         //Llamado ajax para consultar la historia clínica
         $.post("movimientos_pacientes_x_estudio.php",
         {
              consultaAjax:   true,
              accion:         'ConsultarHistoria',
              wemp_pmla:      wemp_pmla,
              wpaciente:      wpaciente.toString()
         }, function(respuesta){

            if  (respuesta=='N'){

                $("#tblmensaje").show();
            }
            else{  

                $("#tblmensaje").hide();
                $("#divhistoria").show();
                $("#divhistoria").empty();
                $("#divhistoria").append(respuesta);
                $("#divhistoria").dialog("open");
            }

          });
          
       }


       function Imprimir(){
          
          var wemp_pmla    = $("#wemp_pmla").val();
          var wlaboratorio = $("#optlaboratorio").val();
          var westudio     = $("#optestudio").val();
          var wpaciente    = $("#optpaciente").val();
          var wtipo        = $("#wtipo").val();

          enlace = "../reportes/rep_mov_pac_x_est.php?wemp_pmla="+wemp_pmla+"&wlaboratorio="+wlaboratorio+"&westudio="+westudio+"&wtipo="+wtipo+"&wpaciente="+wpaciente+"&wdircie=1&wenvio=1";
          window.open(enlace, '_blank');

       }


       function justNumbers(e){
           var keynum = window.event ? window.event.keyCode : e.which;

           if ((keynum == 8) || (keynum == 46) || (keynum == 0))
                return true;

           return /\d/.test(String.fromCharCode(keynum));
       }


       function mostrarTooltip( celda ){

          if( !celda.tieneTooltip ){
            $( "*", celda ).tooltip();
            celda.tieneTooltip = 1;
          }
        }


       function alerta(txt){

         $("#textoAlerta").text( txt );
         $.blockUI({ message: $('#msjAlerta') });
           setTimeout( function(){
                   $.unblockUI();
           }, 1800 );
       }


       function cerrarVentana(){ 

          if(confirm("Esta seguro de salir?") == true)
             window.close();
          else
             return false;
       }


       function CerrarModalhistoria(){

       	  $("#divhistoria").dialog("close");
       }


       function ilumina(celda,clase){
          if (celda_ant=="")
          {
             celda_ant = celda;
             celda_ant_clase = clase;
          }
          celda_ant.className = celda_ant_clase;
          celda.className = 'fondoAmarillo';
          celda_ant = celda;
          celda_ant_clase = clase;
      }


      // FUNCION Ilumina toda la columna donde se ubique el mouse 
      function iluminacolumna(celda,columna)
      {
         $("td.fondoAmarillo").removeClass('fondoAmarillo');
         $("."+columna).addClass("fondoAmarillo");       
      }
      

      // Validación para movimientos con factor 1
      function tomar_valor_actual(campo)
      {
         // Totales por concepto
         document.getElementById('valor_foco').value = campo.value;
      }


      function number_format (number, decimals, dec_point, thousands_sep) 
      {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),        sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);            return '' + Math.round(n * k) / k;
            };
        // Fix for IE parseFloat(0.55).toFixed(0) = 0;
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');    }
        return s.join(dec);
      }


       // **************************************   Fin Funciones Javascript   ********************************************

      </script> 
      <style type="text/css">

          .divfacturar {            
	          width: 100px;
	          border: 1px blue;
	          font-size: small;
          }

         .button{
	          color: #1b2631;
	          font-weight: normal;
	          font-size: 12,75pt;
	          width: 100px; height: 27px;
	          background: rgb(199,199,199);
	          background: -moz-linear-gradient(top,  rgba(199,199,199,1) 0%, rgba(193,193,193,1) 50%, rgba(184,184,184,1) 51%, rgba(224,224,224,1) 100%);
	          background: -webkit-linear-gradient(top,  rgba(199,199,199,1) 0%,rgba(193,193,193,1) 50%,rgba(184,184,184,1) 51%,rgba(224,224,224,1) 100%);
	          background: linear-gradient(to bottom,  rgba(199,199,199,1) 0%,rgba(193,193,193,1) 50%,rgba(184,184,184,1) 51%,rgba(224,224,224,1) 100%);
	          filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#c7c7c7', endColorstr='#e0e0e0',GradientType=0 );
	          border: 1px solid #ccc;
	          border-radius: 8px;
	          box-shadow: 0 1px 1px rgba(0, 0, 0, 0.075) inset;
         }

        .button:hover {background-color: #3e8e41;}

        .button:active {
	          background-color: rgb(169,169,169);
	          box-shadow: 0 5px #666;
	          transform: translateY(4px);
         }

        .ui-multiselect { height:25px; overflow-x:hidden; padding:2px 0 2px 4px; text-align:left;font-size: 10pt; } 
        
        .color1{
          background-color:#5F7E6F;
         }

        .color2{
          background-color:#2471a3;
         }

      </style>  
 </head>
  <body >   
    <?php 
      echo "<input type='HIDDEN' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
      $wtitulo  ="MOVIMIENTOS POR ESTUDIO";
      encabezado($wtitulo, $wactualiz, 'clinica');
      $arr_doc   = consultarEstudios  ($wbasedato,$conex,$wemp_pmla);
      $arr_lab   = consultarLaboratorios ($wbasedato,$conex,$wemp_pmla);
    ?>

    <fieldset style="border: 0.5px solid #999999;">
        <legend align="right"><span style="font-weight:bold;font-size:9pt;" >Listado Estudios</span></legend>
        <div  style="text-align:left;">
            Filtrar listado:<input id="id_search_estudios" name="id_search_estudios" type="text" value="" size="20" placeholder="Buscar en Estudios">
        </div>
        <div style="width:100%; height: 180px; overflow:auto; cursor:pointer;" >
        <table width="100%" style="border: 0px solid #999999;" id="tabla_documentos">
        <thead>
            <tr class="encabezadoTabla">
                <td align='center' width='10%'>Fecha</td>
                <td align='center' width='10%'>Nit Laboratorio</td>
                <td align='center' width='20%'>Laboratorio</td>
                <td align='center' width='10%'>Estudio</td>
                <td align='center' width='10%'>Numero de historia</td>
                <td align='center' width='20%'>Identificaci&oacute;n</td>
                <td align='center' width='30%'>Nombre Paciente</td>
            </tr>
        </thead>
        <tbody>    
        <?php
        $j=0;
        foreach( $arr_doc as $key => $val){
              $j++;
              
              if (is_int ($j/2))
                  $wcf="fila1";
              else
                  $wcf="fila2";

              echo '<tr class="'.$wcf.'" find" codigo="'.utf8_encode($val['wlabnom']). '" id="'.$val['wpaecol'].'|'.$val['wpaecoe'].'|'.$val['wtipoid'].'|'.$val['widenti'].'|'.$val['wlabnom'].'" ondblclick="cargarestudio(this)";>'; 
              echo '<td align="center">'.$val['wpaefec'].'</td>';
              echo '<td align="center">'.$val['wpaecol'].'</td>';
              echo '<td align="center">'.$val['wlabnom'].'</td>';
              echo '<td align="center">'.$val['wpaecoe'].'</tde>';
              echo '<td align="center">'.$val['wpaehis'].'</td>';
              echo '<td align="center">'.$val['wpaetid'].'</td>';
              echo '<td align="center">'.$val['wnompac'].'</td>';
              echo '<td style="display:none;">'.$val['wtipoid'].'</td>';
              echo '<td style="display:none;">'.$val['widenti'].'</td>';
              echo '</tr>';
        }
        ?>
        </tbody>
        </table>
        </div>
    </fieldset>
    <br>
    </center>   
    <fieldset style="border: 0.5px solid #999999;font-size:8pt">
    <center>
    <table>
     <tr>
       <td ><font size="2">Tipo de Movimiento</font> </td>
       <td ><select name='wtipo' id='wtipo'  onchange="Mostrardetalle(this.value)">
       <option value='Mopcan'>INGRESO DE DATOS</option>
       <option value='Moprea'>VERIFICACION DE CUMPLIMIENTO</option>
       <option value='Mopfac'>FACTURAR</option>
       </select>
       </td>
       <td ><font size="2">&nbsp;&nbsp;Laboratorio</font></td>
       <td ><select id='optlaboratorio' name='optlaboratorio' multiple='multiple' onchange="cargarselestudio(this,'1')" style='width: 100px;'>
       <?php
          echo '<option ></option>';
          foreach( $arr_lab as $key => $val){
                echo '<option value="' . $key .'">'.$val.'</option>';
          }
       ?>
       </select>
       </td>
     </tr>
     <tr> 
      <td ><font size="2">Estudio</font> </td>
      <td ><select id='optestudio' name='optestudio' multiple='multiple' onchange="cargarselpaciente(this)" style='width: 100px;'></select>
      </td>
      <td class='verpaciente'><font size="2">&nbsp;&nbsp;Paciente</font> </td>
      <td class='verpaciente'><select id='optpaciente' name='optpaciente' multiple='multiple' style='width: 100px;'></select></td>     
     </tr>
    </table>
    </center>
    </br>
    <br>
    <center><table>
    <tr>
    <td>&nbsp;&nbsp;<input type='submit' id='Consultar'   name='Consultar'   class='button' value='Consultar'    onclick='Consultar(this,this,"1")'></td>
    <td>&nbsp;&nbsp;<input type='submit' id='Grabar'      name='Grabar'      class='button' value='Grabar'       onclick='Grabar()'></td>
    <td>&nbsp;&nbsp;<input type='submit' id='Verhistoria' name='Verhistoria' class='button' value='Ver Historia' onclick='Verhistoria()'></td>
    <td>&nbsp;&nbsp;<input type='submit' id='Salir'       name='Salir'       class='button' value='Salir'        onclick='cerrarVentana()'></td>
    </tr>
    </table>
    </fieldset>
    <CENTER>
    <div id='divfacturar' name='divfacturar' class='divfacturar fila1' style='border: 1px solid blue;display:none;'>
      <table id='tblfacturar' name='tblfacturar'>
      <tr>
      <td><font size="2">Numero de Factura</font></td>
      <td><input type='text' id='txtnrofra' name='txtnrofra' onblur='ValidarFactura(this.value);'></td>
      </tr>
      <tr>
      <td><font size="2">Fecha Probable de Pago</font></td>
      <td><input type='text' id='txtfecpag' name='txtfecpag' ></td>      
      </tr>
      <tr>
      <td><font size="2">Factura pagada</font></td>
      <td><input type='checkbox' id='chkpagfac' name='chkpagfac'></td>
      </tr>
      <tr>
      <td><font size="2">Valor de la Factura</font></td>
      <td><input type='text' id='txtvalfra' name='txtvalfra' readonly></td>
      </tr>
      </table>
    </div>
    <br>
    <div id='divdetalle' name='divdetalle' style='border: 1px solid gray;display:none;'>

    </div> 
    <table id='tblpaciente' name='tblpaciente' style='border: 1px solid blue;display:none;'>
      <thead>
      </thead>
      <tbody>
      </tbody>
    </table> 
    <br><br>
    <center><table id='tblbotones' name='tblbotones' style='display:none;'>
    <tr>
    <td>&nbsp;&nbsp;<input type='submit' id='Consultar' name='Consultar' class='button' value='Consultar'  onclick='Consultar(this,this,"1")'></td>
    <td>&nbsp;&nbsp;<input type='submit' id='Grabar'    name='Grabar'    class='button' value='Grabar'     onclick='Grabar()'></td>
    <td>&nbsp;&nbsp;<input type='submit' id='Salir'     name='Salir'     class='button' value='Salir'      onclick='cerrarVentana()'></td>
    </tr>
    </table>    
    <CENTER>
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
    <div style="display:none;" id="img_bus">Actualizando en Matrix.. <img width="13" height="13" border="0" src="../../images/medical/ajax-loader9.gif">
    </div>
    <div id='divhistoria' name='divhistoria' style="width:100%; height: 180px; overflow:auto; cursor:pointer;display:none;" title='Ver Historia'>    
    </div>
    <div id="divcargando" name="divcargando" style='display:none;' ><center><img width="26" height="26" border="0" src="../../images/medical/ajax-loader9.gif"></center></div>  
    <input type="HIDDEN" name="wnormoide"  id="wnormoide"  value=0>
    <input type="HIDDEN" name="wedicion"   id="wedicion"   value='N'>
    <input type="HIDDEN" name="wnumevis"   id="wnumevis"   value='0'>
    <input type="HIDDEN" name="wnumefre"   id="wnumefre"   value='0'>
    <input type="HIDDEN" name="wscroll"    id="wscroll"    value='0'>
    <input type="HIDDEN" name="wgrabar"    id="wgrabar"    value='off'>
    <input type="HIDDEN" name="wvalidar"   id="wvalidar"   value='0'>
    <input type="HIDDEN" name="weliminar"  id="weliminar"  value='off'>
    <input type="HIDDEN" name="wconsultar" id="wconsultar" value='off'>
    <input type="HIDDEN" name="warrgrabar" id="warrgrabar" >
    <input id="id_search_tema" name="id_search_tema" type="HIDDEN" value="" size="20">
  </body>
  </html>