<?php
 include_once("conex.php");
 include_once("root/comun.php");

 /*********************************************************************************************************************************
 *
 * Programa           : visualizarTurneroxpiso.php
 * Fecha de Creación  : 2020-02-17
 * Autor              : Arleyda Insignares Ceballos
 * Descripcion        : Programa para llamar los turneros por piso.
 *                      Consulta en root_000051 el parámetro 'urlTurneros', el cual contiene las url por piso y tema.
 * 
 **********************************************************************************************************************************/
 
  $wactualiza = "2020-02-21";

  header('Content-type: text/html;charset=ISO-8859-1');
  //*************************************   Inicio  ***********************************************************

  $conex        = obtenerConexionBD("matrix"); 
  $wbasehce     = consultarAliasPorAplicacion($conex, $wemp_pmla, 'hce');
  $wbasecliame  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');
  $wbasemovhos  = consultarAliasPorAplicacion($conex, $wemp_pmla, 'movhos');
  $wfecha       = date("Y-m-d");
  $whora        = (string)date("H:i:s");
  $pos          = strpos($user,"-");
  $wusuario     = substr($user,$pos+1,strlen($user)); 
  
  // *****************************************        FIN PHP        ********************************************
  ?>
  <!DOCTYPE html>
  <html lang="es-ES">
  <head>
    <title>Turnero</title>
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
            document.oncontextmenu = function(){return false}
            //document.onkeydown  = lector;
      
            document.addEventListener("click", function(e) { 
              toggleFullScreen();   
          }, false);

        });

      function ejecutar(path)
      {
          $("#ifr_turnero").attr("src", path);
          $('#modalTurnero').show();
      }

      function toggleFullScreen() {
        videoElement = document.getElementById("bodyPrincipal1");
        if (!document.mozFullScreen && !document.webkitFullScreen) {
          if (videoElement.mozRequestFullScreen) {
            videoElement.mozRequestFullScreen();
          } else {
            videoElement.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
          }
        }
      }
    </script>
    <style type="text/css">
        #divencabezado
        {
            border-radius: 10px;
            border-collapse: collapse;
            height:100%;
            width:100%;
        }
        .form-control
        {
            height:30px;
            font-size: 12px;
        }
        body 
        {
            width: 100%;
        }
        .fondoAzul
        {
            height:230px;
            width:600px;
            font-size: 60px;
            color:white;
            cursor: pointer;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            display: table-cell;
            border-radius: 7px;
            border: 1px solid white;
            background-color: #099fc4;
        }
        .fondoVerde
        {
            height:230px;
            width:600px;
            font-size: 60px;
            color:white;
            cursor: pointer;
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
            display: table-cell;
            border-radius: 7px;
            border: 1px solid white;
            background-color: #b2ce00;
        }
        /* make the video stretch to fill the screen in WebKit */
        :-webkit-full-screen #bodyPrincipal1 {
          width:    100%;
          height:   100%;
          background-color: white;
        }
        #modalTurnero{
          max-height: 100%;
          overflow-y: hidden;
          overflow-x: hidden;
        }
     </style>
  </head>
  <body  id="bodyPrincipal1" style='background-color: white'>
      <input type='hidden' name='wemp_pmla'   id='wemp_pmla'   value='<?=$wemp_pmla?>'>
      <?php
          $wTurneros    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'urlTurneros');
          $wbasedato    = consultarAliasPorAplicacion($conex, $wemp_pmla, 'cliame');          
          $arrTurneros  = explode('|', $wTurneros);   
          $nomlogo      = "barra_centromedico.jpg"; 
      ?>
      <div id='divencabezado' align='center' style='background-color: white'>
           <?php
              $class     ='fondoAzul';
              $classfont ='#b2ce00';
              $cont = 0;
              echo '<br><br><br><br><br>';
              foreach( $arrTurneros as $val)
              {
                        $wselturnero = explode('#',$val);
                        //seleccionar servicios por tema
                        $sqlServicios = "
                                SELECT Sercod, Sernom, Serord
                                  FROM ".$wbasedato."_000298
                                   WHERE Sertem = '".$wselturnero[0]."'
                                   AND Serest = 'on'
                                 ORDER BY Serord
                                ";
                        $resServicios = mysql_query($sqlServicios, $conex) or die("<b>ERROR EN QUERY MATRIX(sqlServicios):</b><br>".mysql_error());                       

                       echo "<div class='container'>";
                       if ($cont > 0)
                           echo '<br>';
                       echo "<div class='".$class."' onclick='ejecutar(".chr(34).$wselturnero[2].chr(34).")' style='float: left;padding: 50px;'>".$wselturnero[1]."</div>";
                       
                       echo "<div style='width: 45%;float: right; font-weight: bold;text-align: center; font-size: 20px;color: ".$classfont."'>";

                       while($rowServicios = mysql_fetch_array($resServicios))
                             echo ($rowServicios['Sernom'])."<br>";

                       echo "</div><br>"; 
                       echo "</div>";
                       $class     == "fondoAzul" ? $class = "fondoVerde" : $class = "fondoAzul";
                       $classfont == "#099fc4"   ? $classfont = "#b2ce00" : $classfont = "#099fc4";
                       $cont++;

              }
              echo "<br><br><br><br><div><img width='80%' heigth='70%' src='../../images/medical/root/".$nomlogo."' ></div>";
              echo "<div class='modal' id='modalTurnero' role='dialog'>              
                   <iframe id='ifr_turnero' src='' height='100%' width='100%' style='border:none;'></iframe></div>"; ?>
      </div> 
    </body>   
    </html>