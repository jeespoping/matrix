<?php
include_once("conex.php");

 /***********************************************************************************************************
 *
 * Programa		       :	Reporte Empleados pendientes por Evaluación
 * Fecha de Creación :	2016-07-06
 * Autor	 		       :	Arleyda Insignares Ceballos
 * Descripcion       :	Listado de Empleados pendiente por Renovación de contratos o programación de 
 *                      Evaluaciones.
 *	
 **********************************************************************************************************/
 
  $wactualiz = "2016-07-06";
  if(!isset($_SESSION['user'])){
	    echo "<center></br></br><table id='tbllogueo' name='tbllogueo' style='border: 1px solid blue;visibility:none;'>
		  <tr><td>Error, inicie nuevamente</td></tr>
		  </table></center>";
	return;
  }

  header('Content-type: text/html;charset=ISO-8859-1');
  //********************************** Inicio  ************************************************************
	

	include_once("root/comun.php");
	

	$conex         = obtenerConexionBD("matrix");
	$wbasedato     = consultarAliasPorAplicacion($conex, $wemp_pmla, 'talhuma');
	$wfecha        = date("Y-m-d");
	$whora         = (string)date("H:i:s");
	$pos           = strpos($user,"-");
	$wusuario      = substr($user,$pos+1,strlen($user));
	$wcodusu       = '';
  $wnomusu       = '';
  $wclausu       = '';
  $usuario_exis  = '';

  // ***************************************      FUNCIONES AJAX Y PHP     *******************************************
     // ***************  Consulta de los Empleados que han ingresado y no se les ha programado primera evaluación  ***
      
      if (isset($_POST["accion"]) && $_POST["accion"] == "SeleccionarNoprogramadas"){
          
          $wcentros    = unserialize(base64_decode($arr_cen));
          $wmes2       = $wmes+1;   
          $vresultado  = '';
          $listasinpro = '';
          $vresultado  = "<tr bgcolor='silver'><td align='center'>Codigo Empleado</td>
                          <td align='center'>Nombre Empleado</td>
                          <td align='center'>Cedula</td>
                          <td align='center'>Fecha Ingreso</td>
                          <td align='center'>Nro Evaluaciones</td></tr>";
          
          if ($wcentro==''){

              $q = " SELECT A.Ideuse, A.Idefin, A.Ideced, A.Idecco, A.Ideest, B.Areper, B.Areano,
                     concat(A.Ideno1,' ', A.Ideno2, ' ', A.Ideap1, ' ', A.Ideap2) as 'nomemp', 
                     A.Idetco,DATEDIFF(curdate(),A.Idefin) as 'totaldias'
                     FROM ".$wbasedato."_000013 A 
                     LEFT JOIN ".$wbasedato."_000058 B on B.Arecdo = A.Ideuse 
                     WHERE ((CURDATE()-DATE(A.Idefin))>75) AND (YEAR(CURDATE())<=YEAR(A.Idefin)) 
                           AND (B.Areano is null) AND (A.Ideap1 is not null) AND (A.Ideest = 'on')
                           AND (A.Ideced != '') AND (A.Ideap1 != '')
                           AND (A.Idetco not in ('".$wcontraex."'))
                     ORDER BY A.Ideap1, A.Ideap2 ";
          }
          else{  
            
              $q = " SELECT A.Ideuse, A.Idefin, A.Ideced, A.Idecco, A.Ideest, B.Areper, B.Areano,
                     concat(A.Ideno1,' ', A.Ideno2, ' ', A.Ideap1, ' ', A.Ideap2) as 'nomemp' 
                     FROM ".$wbasedato."_000013 A 
                     LEFT JOIN ".$wbasedato."_000058 B on B.Arecdo = A.Ideuse 
                     WHERE ((CURDATE()-DATE(A.Idefin))>75) AND ((CURDATE()-DATE(A.Idefin))<365) 
                           AND (YEAR(CURDATE())<=YEAR(A.Idefin)) 
                           AND (B.Areano is null) AND (A.Ideap1 is not null) AND (A.Ideest = 'on')
                           AND (A.Ideced != '') AND (A.Ideap1 != '') 
                           AND (A.Idecco = ".$wcentro.")
                           AND (A.Idetco not in ('".$wcontraex."'))
                     ORDER BY A.Ideap1, A.Ideap2 ";
          }

          $cont1 = 0;
          $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
          $num = mysql_num_rows($res);
          
          $fecha       = $row['Idefin'];
          $mesfec      = substr($fecha,5,2);  
          $anofec      = substr($fecha,0,4);


          if ($num > 0)
          {   
              while($row = mysql_fetch_assoc($res)){
                    $cont1 % 2 == 0 ? $clase = "fila1" : $clase = "fila2";
                    $cont1++;         
                    $NOM = (array_key_exists($row['Idecco'],$wcentros)) ? $wcentros[$row['Idecco']]:'Sin Centro de Costos';
                    $vresultado .= '<tr class="'.$clase.'">
                                <td>'.$row["Ideuse"].'</td>
                                <td>'.$row['nomemp'].'</td>
                                <td align="center">'.$row['Ideced'].'</td>
                                <td align="center">'.$row['Idefin'].'</td>
                                <td align="center">Sin Primera Evaluacion</td></tr>';
              }
          }

          else
          { $listasinpro='S';}

        
        // ***************      Consulta de Empleados con evaluaciones pendientes por programar    *********************
          if ($wcentro==''){
          
              $q =  " SELECT count(B.Arecdo) as totaleva, DATEDIFF(curdate(),A.Idefin) as 'totaldias', 
                    A.Ideuse, A.Idefin, A.Ideced, A.Idecco, A.Ideest , A.Idetco, B.Areper, B.Areano, 
                    concat(A.Ideno1,' ', A.Ideno2, ' ', A.Ideap1, ' ', A.Ideap2) as 'nomemp'                  
                    FROM ".$wbasedato."_000013 A 
                    INNER JOIN ".$wbasedato."_000058 B on B.Arecdo = A.Ideuse 
                    WHERE (DATEDIFF(CURDATE(),A.Idefin)>=75) AND (DATEDIFF(CURDATE(),A.Idefin)<365) 
                      AND (YEAR(CURDATE())<=YEAR(A.Idefin)) 
                      AND (A.Ideap1 is not null) AND (A.Ideest = 'on') 
                      AND (A.Ideced != '') AND (A.Ideap1 != '')
                      AND (A.Idetco not in ('".$wcontraex."'))
                    GROUP BY B.Arecdo 
                    ORDER BY A.Ideap1, A.Ideap2 ";
          }
          else{

              $q =  " SELECT count(B.Arecdo) as totaleva, DATEDIFF(curdate(),A.Idefin) as 'totaldias', 
                    A.Ideuse, A.Idefin, A.Ideced, A.Idecco, A.Ideest, A.Idetco, B.Areper, B.Areano, 
                    concat(A.Ideno1,' ', A.Ideno2, ' ', A.Ideap1, ' ', A.Ideap2) as 'nomemp'                  
                    FROM ".$wbasedato."_000013 A 
                    INNER JOIN ".$wbasedato."_000058 B on B.Arecdo = A.Ideuse 
                    WHERE (DATEDIFF(CURDATE(),A.Idefin)>=75) AND (YEAR(CURDATE())<=YEAR(A.Idefin)) 
                       AND (A.Ideap1 is not null) AND (A.Ideest = 'on') AND (A.Ideced != '') 
                       AND (A.Ideap1 != '') AND (A.Idecco = ".$wcentro.")
                       AND (A.Idetco not in ('".$wcontraex."'))
                    GROUP BY B.Arecdo 
                    ORDER BY A.Ideap1, A.Ideap2 ";
          }

          $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
          $num = mysql_num_rows($res);    

          if ($num > 0)
          {   
              while($row = mysql_fetch_assoc($res)){
                     $fecha       = $row['Idefin'];
                     $dias        = $row['totaldias'];
                     $contador    = $row['totaleva'];
                     $resultado   = intval($row['totaldias']/90);
                     $identi      = $row['Ideuse'];
                     $mesfec      = substr($fecha,5,2);  
                     $anofec      = substr($fecha,0,4);
                     // Si un empleado tiene mas de un año.
                     if ($contador < $resultado && $dias <=365 && $mesfec <= $wmes && $anofec <= $wano)
                     {
                        
                        $cont1 % 2 == 0 ? $clase = "fila1" : $clase = "fila2";
                        $cont1++;         
                        $NOM = (array_key_exists($row['Idecco'],$wcentros)) ? $wcentros[$row['Idecco']]:'Sin Centro de Costos';
                        $vresultado .= '<tr class="'.$clase.'">
                                    <td>'.$row["Ideuse"].'</td>
                                    <td>'.$row['nomemp'].'</td>
                                    <td align="center">'.$row['Ideced'].'</td>
                                    <td align="center">'.$row['Idefin'].'</td>
                                    <td align="center">'.$contador.' Evaluacion</td>
                                    </tr>';
                     }
              }
          }
          else{
              if ($listasinpro=='S')
                 { $vresultado='0';}
          }
          echo $vresultado;
          return;       
      }

      // *************************  Consulta para construir el informe de Empleados con Evaluaciones pendientes por realizar ***************
      
      if (isset($_POST["accion"]) && $_POST["accion"] == "SeleccionarEmpleados"){
        $wcentros = unserialize(base64_decode($arr_cen));
        $wmes2 = $wmes+1;   
        $vresultado = '';
        $vresultado = "<tr bgcolor='silver'><td align='center'>Codigo Empleado</td>
                       <td align='center'>Nombre Empleado</td>
                       <td align='center'>Codigo Jefe</td>
                       <td align='center'>Nombre Jefe</td>
                       <td align='center'>Centro de costos</td>
                       <td align='center'>Descripcion C. de C.</td>
                       <td align='center'>Fecha Programada</td>
                       <td align='center'>Fecha de Ingreso</td></tr>";

        $periodos ='';

        $q  = " SELECT Detval From root_000051 where Detapl='contratoperiodo' ";

        $res = mysql_query($q,$conex) or die ("Error Periodos: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());        

        $num = mysql_num_rows($res);
          
        if ($num > 0)
        { 
            $rowper = mysql_fetch_assoc($res);
            $periodos = $rowper['Detval']; 
        }


        if ($wseltipo=='01') // * * *  Evaluaciones Programadas que no se han realizado
        {
            if ($wcentro==''){

                $q  =   " SELECT A.Arecdr, A.Arecdo, A.Aretem, A.Arefor, A.Areper, Areano, 
                        A.Areest, B.Idecco, B.Idetco, D.Mcaano, D.Mcaper, C.Idefin,
                        concat(B.Ideno1,' ', B.Ideno2, ' ', B.Ideap1, ' ', B.Ideap2) as 'nomemp',
                        concat(C.Ideno1,' ', C.Ideno2, ' ', C.Ideap1, ' ', C.Ideap2) as 'nomjefe'
                        FROM ".$wbasedato."_000058 A 
                        INNER JOIN ".$wbasedato."_000013 B on A.Arecdo = B.Ideuse 
                        INNER JOIN ".$wbasedato."_000013 C on A.Arecdr = C.Ideuse 
                        LEFT JOIN ".$wbasedato."_000032 D on A.Arecdr = D.Mcauco 
                        AND A.Areano = D.Mcaano AND  A.Areper = D.Mcaper 
                        WHERE A.Areper = ".$wmes." AND A.Areano = ".$wano." 
                              AND A.Aretem=".$periodos." AND B.Idetco not in ('".$wcontraex."')
                              AND D.Mcaano is null AND D.Mcaper is null";
            }
            else{
                
                $q  =   " SELECT A.Arecdr, A.Arecdo, A.Aretem, A.Arefor, A.Areper, Areano, 
                        A.Areest, B.Idecco, B.Idetco, D.Mcaano, D.Mcaper, C.Idefin,
                        concat(B.Ideno1,' ', B.Ideno2, ' ', B.Ideap1, ' ', B.Ideap2) as 'nomemp', 
                        concat(C.Ideno1,' ', C.Ideno2, ' ', C.Ideap1, ' ', C.Ideap2) as 'nomjefe' 
                        FROM ".$wbasedato."_000058 A 
                        INNER JOIN ".$wbasedato."_000013 B on A.Arecdo = B.Ideuse 
                        INNER JOIN ".$wbasedato."_000013 C on A.Arecdr = C.Ideuse 
                        LEFT JOIN ".$wbasedato."_000032 D on A.Arecdr = D.Mcauco 
                        AND A.Areano = D.Mcaano AND  A.Areper = D.Mcaper 
                        WHERE A.Areper = ".$wmes." AND A.Areano = ".$wano."
                              AND A.Aretem=".$periodos." AND B.Idecco=".$wcentro."
                              AND B.Idetco not in ('".$wcontraex."')
                              AND D.Mcaano is null AND D.Mcaper is null";

            }
        }
        else  // * * *  Evaluaciones Programadas y Cerradas 
        {

            if ($wcentro==''){

                $q  =   " SELECT A.Arecdr, A.Arecdo, A.Aretem, A.Arefor, A.Areper, Areano, 
                        A.Areest, B.Idecco, B.Idetco, D.Mcaano, D.Mcaper, C.Idefin,
                        concat(B.Ideno1,' ', B.Ideno2, ' ', B.Ideap1, ' ', B.Ideap2) as 'nomemp',
                        concat(C.Ideno1,' ', C.Ideno2, ' ', C.Ideap1, ' ', C.Ideap2) as 'nomjefe'
                        FROM ".$wbasedato."_000058 A 
                        INNER JOIN ".$wbasedato."_000013 B on A.Arecdo = B.Ideuse 
                        INNER JOIN ".$wbasedato."_000013 C on A.Arecdr = C.Ideuse 
                        INNER JOIN ".$wbasedato."_000032 D on A.Arecdr = D.Mcauco 
                        AND A.Areano = D.Mcaano AND A.Areper = D.Mcaper                        
                        WHERE A.Areper = ".$wmes." AND A.Areano = ".$wano." 
                              AND A.Aretem=".$periodos." AND B.Idetco not in ('".$wcontraex."') ";
            }
            else{
                
                $q  =   " SELECT A.Arecdr, A.Arecdo, A.Aretem, A.Arefor, A.Areper, Areano, 
                        A.Areest, B.Idecco, B.Idetco, D.Mcaano, D.Mcaper, C.Idefin,
                        concat(B.Ideno1,' ', B.Ideno2, ' ', B.Ideap1, ' ', B.Ideap2) as 'nomemp', 
                        concat(C.Ideno1,' ', C.Ideno2, ' ', C.Ideap1, ' ', C.Ideap2) as 'nomjefe' 
                        FROM ".$wbasedato."_000058 A 
                        INNER JOIN ".$wbasedato."_000013 B on A.Arecdo = B.Ideuse 
                        INNER JOIN ".$wbasedato."_000013 C on A.Arecdr = C.Ideuse 
                        INNER JOIN ".$wbasedato."_000032 D on A.Arecdr = D.Mcauco 
                        AND A.Areano = D.Mcaano AND  A.Areper = D.Mcaper 
                        WHERE A.Areper = ".$wmes." AND A.Areano = ".$wano."
                              AND B.Idetco not in ('".$wcontraex."')
                              AND A.Aretem=".$periodos." AND B.Idecco=".$wcentro;

            }
        }

        $cont1 = 0;
        $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
        $num = mysql_num_rows($res);
        if ($num > 0)
        {   
            while($row = mysql_fetch_assoc($res)){  
                  $cont1 % 2 == 0 ? $clase = "fila1" : $clase = "fila2";
                  $cont1++;         
                  $NOM = (array_key_exists($row['Idecco'],$wcentros)) ? $wcentros[$row['Idecco']]:'Sin Centro de Costos';
                  $vresultado .= '<tr class="'.$clase.'"><td>'.$row["Arecdo"].'</td>
                                 <td>'.$row['nomemp'].'</td>
                                 <td align="center">'.$row['Arecdr'].'</td>
                                 <td align="center">'.$row['nomjefe'].'</td>
                                 <td align="center">'.$row['Idecco'].'</td>
                                 <td align="center">'.$NOM.'</td>
                                 <td align="center">'.$row['Areper'].'-'.$row['Areano'].'</td>
                                 <td align="center">'.$row['Idefin'].'</tr>';
            }
        }
        else{
            $vresultado='0';
        }
        echo $vresultado;
        return;   
      }
      

      // ******************           Consultar todos los Periodos para el campo option             ****************
      function consultarPeriodos($wbasedato,$conex,$wemp_pmla,$wtema){

        // Seleccionar el tema para cargar los periodos
        $periodos ='';

        $q  = " SELECT Detval From root_000051 where Detapl='contratoperiodo' ";

        $res = mysql_query($q,$conex) or die ("Error Periodos: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());        

        $num = mysql_num_rows($res);
          
        if ($num > 0)
          { 
            $rowper = mysql_fetch_assoc($res);
            $periodos = $rowper['Detval']; 
          }
        
        $strtipvar = array();

        $q  = " SELECT Perper, Perano "
             ." FROM ".$wbasedato."_000009 "
             ." WHERE Perfor= '".$periodos."' ";

        $res = mysql_query($q,$conex) or die ("Error 3: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

        while($row = mysql_fetch_assoc($res))
             {
               $strtipvar[$row['Perano']."-".$row['Perper']] = $row['Perano']."-".$row['Perper'];
             }
        
        return $strtipvar;

      }

      
      //Cargar un string con los numeros de contrato  que aplican (origen unix)
      function consultarContratos($wbasedato,$conex,$wemp_pmla){
  
        $strcontrato = '';
        $q  = " SELECT Detval From root_000051 where Detapl='contratosorganigrama' ";

        $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
        while($row = mysql_fetch_assoc($res))
             {
               $strcontrato = utf8_encode($row['Detval']);
             }

        return $strcontrato;
      }


      // ******************         Consultar todos los Centros de Costos para el campo array       *****************
      function consultarCentros($wbasedato,$conex,$wemp_pmla){
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
                                                ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                        GROUP BY    tb1.Ccocod
                                        ORDER BY    tb1.Ccodes";
                            break;
                    case "farstore_000003":
                            $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                        FROM    farstore_000003 AS tb1
                                                INNER JOIN
                                                ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                        GROUP BY    tb1.Ccocod
                                        ORDER BY    tb1.Ccodes";
                            break;
                    case "costosyp_000005":
                            $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                        FROM    costosyp_000005 AS tb1
                                                INNER JOIN
                                                ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                        GROUP BY    tb1.Ccocod
                                        ORDER BY    tb1.Cconom";
                            break;
                    case "uvglobal_000003":
                            $query = "  SELECT  tb1.Ccocod AS codigo, tb1.Ccodes AS nombre
                                        FROM    uvglobal_000003 AS tb1
                                                INNER JOIN
                                                ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                        GROUP BY    tb1.Ccocod
                                        ORDER BY    tb1.Ccodes";
                            break;
                    default:
                            $query="    SELECT  tb1.Ccocod AS codigo, tb1.Cconom AS nombre
                                        FROM    costosyp_000005 AS tb1
                                                INNER JOIN
                                                ".$wbasedato."_000013 AS tal ON (tb1.Ccocod = tal.Idecco)
                                        GROUP BY    tb1.Ccocod
                                        ORDER BY    tb1.Cconom";
                }

        $res = mysql_query($query,$conex);
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
	<html>
	<head>
		<title>Reporte Empleados Pendientes por evaluaci&oacute;n</title>
		<meta charset="utf-8">
		<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1" />		
		<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
		<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
		<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
		<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>	
		<script type="text/javascript" src="../../../include/root/jqueryalert.js?v=<?=md5_file('../../../include/root/jqueryalert.js');?>"></script>
		<script src='../../../include/root/jquery.quicksearch.js' type='text/javascript'></script>
    <link type="text/css" href="../../../include/root/jqueryalert.css" rel="stylesheet" /></script>
		<script type="text/javascript">
            $.datepicker.regional['esp'] = {
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
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['esp']);
        $(document).ready(function() {


            // Asignar array al campo autocompletar centro de costos
            var arr_cen  = eval('(' + $('#arr_cen2').val() + ')');
            var centros = new Array();
            var index   = -1;
            for (var cod_cen in arr_cen)
                {
                    index++;
                    centros[index]            = {};
                    centros[index].value      = cod_cen;
                    centros[index].label      = cod_cen+'-'+arr_cen[cod_cen];
                    centros[index].codigo     = cod_cen;
                    centros[index].nombre     = arr_cen[cod_cen];
                }            

            $("#autcodcen").autocomplete({
            source: centros,          
            autoFocus: true,            
            select: function( event, ui ){
                    $( "#autcodcen" ).val(ui.item.nombre);
                    $( "#autcodcen" ).attr('valor', ui.item.value);
                    $( "#autcodcen" ).attr('label', ui.item.label);
                    $( "#autcodcen" ).attr("codigo",ui.item.codigo);
                    $( "#wcodcen").val(ui.item.codigo);
                    return false;
            }
            });
    });
	  
    
    // **************************************   Inicio Funciones Javascript   ************************************************
    
    function Consultar()
    {      
      var wemp_pmla  = $("#wemp_pmla").val();
      var wcentro    = ($("#autcodcen").val()=='') ? '' : $("#wcodcen").val();
      var periodo    = $("#optperiodo").val();
      var periodo2   = periodo.split('-');
      var wmes       = periodo2[1];
      var wano       = periodo2[0];
      var arr_cen    = $("#arr_cen").val();
      var wseltipo   = $("#seltipo").val();
      var contratoex = $('#arr_cont').val();

      if (wseltipo=='00')
      { 
         $.post("../reportes/empleadospendientes.php",
            {
                consultaAjax:  true,
                accion   :     'SeleccionarNoprogramadas',
                wemp_pmla:     wemp_pmla,
                wmes     :     wmes,
                wano     :     wano,
                arr_cen  :     arr_cen,
                wcentro  :     wcentro,
                wseltipo :     wseltipo,
                wcontraex:     contratoex
            }, function(respuesta){

                  if (respuesta=='0'){
                       $("#tblmensaje").show();
                       $("#tbldetalle").hide();
                      }
                  else{
                       $("#tblmensaje").hide();
                       $("#tbldetalle").show(); 
                       $("#tbldetalle tbody ").remove(0);
                       $("#tbldetalle").append(respuesta);
                }
            });
      }
      else
      {       

         $.post("../reportes/empleadospendientes.php",
            {
                consultaAjax:  true,
                accion   :    'SeleccionarEmpleados',
                wemp_pmla:     wemp_pmla,
                wmes     :     wmes,
                wano     :     wano,
                arr_cen  :     arr_cen,
                wcentro  :     wcentro,
                wseltipo :     wseltipo,
                wcontraex:     contratoex
            }, function(respuesta){

               $("#tbldetalle tbody ").remove(0);
               if (respuesta=='0'){
                  $("#tbldetalle").hide(); 
                  $("#tblmensaje").show();
                  }
               else{
                  $("#tblmensaje").hide();
                  
                  $("#tbldetalle").append(respuesta);
                  $("#tbldetalle").show();
                  }
            });
       }
    }          

    // **************************************   Fin Funciones Javascript   ********************************************
	  </script>		
 </head>
	<body>		
		  <?php 
		  echo "<input type='hidden' name='wemp_pmla' id='wemp_pmla' value='".$wemp_pmla."'>";
      echo '<input type="hidden" id="wtema" name="wtema" value="'.$wtema.'" />';
		  $wtitulo="REPORTE DE EMPLEADOS CON EVALUACIONES PENDIENTES";
		  encabezado($wtitulo, $wactualiz, 'clinica');
      $arr_cen  = consultarCentros  ($wbasedato,$conex,$wemp_pmla);
      $arr_per  = consultarPeriodos ($wbasedato,$conex,$wemp_pmla,$wtema);
      $arr_cont = consultarContratos($wbasedato,$conex,$wemp_pmla);
		  ?>
      <CENTER>       
      <table width='500px' style='border: 1px solid blue'>
      <tr class=fila1>
      <td><b>Seleccionar Periodo</b></td>
      <td><select id='optperiodo' name='optperiodo'>
      <?php
        foreach( $arr_per as $key => $val){
          echo '<option value="' . $key .'">'.$val.'</option>';
        }
      ?>
      </select>
      </td>
      <tr class=fila1>
      <td width="300px"><b>Centro de Costos : </b></td><td><input type='text' id='autcodcen' name='autcodcen' size=43 ></td>      
      </tr>
      <tr class=fila1>
      <td width="300px"><b>Tipo de Evaluaci&oacute;n : </b></td>
      <td><select id='seltipo' name='seltipo'><option selected value='01'>Evaluaciones programadas</option><option selected value='02'>Evaluaciones Cerradas</option><option selected value='00'>Evaluaciones No programadas</option></select></td>
      </tr>
      </table>
      </CENTER>
      <br><br>
	    <center><table>
	    <tr>
	    <td>&nbsp;&nbsp;<input type='submit' name='Consultar' value='Consultar' onclick='Consultar()'></td>
	    <td>&nbsp;&nbsp;<input type='submit' name='Salir' value='Salir' onclick='cerrarVentana()'></td>
	    </tr>
	    </table>
      </br></br>
      <table id='tbldetalle' name='tbldetalle' style='display:none;'>
      <thead><tr class=fila1>
      </thead>
      <tbody>
      </tbody>           
      </table>
      <table border='1' id='tblmensaje' name='tblmensaje' style='display:none;'>
      <tr><td>No hay Registros</td></tr>
      <tr></tr>
      </table>      
		  <div id='msjAlerta' style='display:none;'>
		  <br><img src='../../images/medical/root/Advertencia.png'/>
		  <br><br><div id='textoAlerta'></div><br><br>
		  </div>
		  <div style="display:none;" id="img_bus">Actualizando en Matrix.. <img width="13" height="13" border="0" src="../../images/medical/ajax-loader9.gif">
		  </div>
		  </center>
		  <input type="HIDDEN"  name="wnueusuario" id="wnueusuario">
      <input type="HIDDEN"  name="wcodcen"     id="wcodcen">
      <input type="HIDDEN"  name="arr_cen2"    id="arr_cen2" value='<?=json_encode($arr_cen)?>'>
      <input type="HIDDEN"  name="arr_cen"     id="arr_cen"  value='<?=base64_encode(serialize($arr_cen))?>'>
      <input type="HIDDEN"  name="arr_cont"    id="arr_cont" value='<?=$arr_cont?>'>
</html>
	</body>
	</html>