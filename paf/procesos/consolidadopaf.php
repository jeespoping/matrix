<?php
$accion = $_GET['accion'];      if($accion == null){$accion = $_POST['accion'];}
$fechaIw = $_GET['fechaIw'];    $fechaFw = $_GET['fechaFw'];

if($accion == 'print')
{
    header('Content-type: application/vnd.ms-excel; charset=UTF-8');
	header("Content-disposition: attachment; filename=pacientes_PAF");
	header('Pragma: no-cache');
	header('Expires: 0');
    ?>
    <!DOCTYPE html>
    <html lang="esp" xmlns="http://www.w3.org/1999/html">
    <head>
        <meta charset="utf-8">
        <title>Reporte Pacientes Ingresados - PAF</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?php
        include_once("conex.php");
        if(!isset($_SESSION['user']))
        {
            ?>
            <div align="center">
                <label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix o inicie sesion nuevamente.</label>
            </div>
            <?php
            return;
        }
        else
        {
            $user_session = explode('-', $_SESSION['user']);
            $wuse = $user_session[1];
            include_once("root/comun.php");
            $conex = obtenerConexionBD("matrix");
        }
        $fechaiInf = $_POST['fechaiInf'];   $fechafInf = $_POST['fechafInf'];
        ?>
    </head>

    <body>
    <?php
        if($fechaIw != null and $fechaFw != null)
        {
            $query = "select a.*, b.Pacdoc from paf_000004 a, cliame_000100 b
                         where a.fecha_Ing between '$fechaIw' AND '$fechaFw'
                         and b.Pachis = a.hc
                         order by fecha_Ing asc";
            $commitQuery = mysql_query($query, $conex);
            $nroreg = mysql_num_rows($commitQuery);
            ?>
            <table border="1">
                <tr>
                <td>FECHA RONDA</td>                            <td>HABITACION</td>                             <td>HISTORIA</td>                               <td>INGRESO</td>
                <td>DOCUMENTO</td>                              <td>NOMBRE PACIENTE</td>                        <td>EDAD</td>                                   <td>SEXO</td>
                <td>FECHA DE INGRESO</td>                       <td>ES REINGRESO</td>                           <td>DIAGNOSTICO</td>                            <td>COMORBILIDAD</td>
                <td>FECHA DE EGRESO</td>                        <td>FECHA INGRESO PAF</td>                      <td>FECHA RETIRO PAF</td>                       <td>FECHA REINTEGRO PAF</td>
                <td>FECHA SEGUNDO RETIRO PAF</td>               <td>PROGRAMADO AMBULATORIO</td>                 <td>INDICACION CX</td>                          <td>FECHA CX</td>
                <td>CIRUGIA 1</td>                              <td>CIRUGIA 2</td>                              <td>CIRUGIA 3</td>                              <td>FECHA REINTERVENCION</td>
                <td>REINTERVENCION 1</td>                       <td>REINTERVENCION 2</td>                       <td>REINTERVENCION 3</td>                       <td>FECHA INGRESO UCI</td>
                <td>FECHA EGRESO UCI</td>                       <td>FECHA REINGRESO UCI</td>                    <td>FECHA SEGUNDO EGRESO UCI</td>               <td>PROGRAMADO HEMODINAMIA</td>
                <td>INDICACION HEMODINAMIA</td>                 <td>FECHA HEMODINAMIA</td>                      <td>INTERVENCION HEMODINAMIA</td>               <td>PROGRAMADO ELECTROFISIOLOGIA</td>
                <td>INDICACION ELECTROFISIOLOGIA</td>           <td>FECHA ELECTROFISIOLOGIA</td>                <td>INTERVENCION ELECTROFISIOLOGIA</td>         <td>ISO</td>
                <td>OBSERVACION</td>                            <td>NOTA</td>                                   <td>RESPONSABLE</td>                            <td>ALERTA</td>
                <td>TIPO EGRESO</td>                            <td>SEGUNDA INDICACION HEMODINAMIA</td>         <td>SEGUNDA FECHA HEMODINAMIA</td>              <td>SEGUNDA INTERVENCION HEMODINAMIA</td>
                <td>SEGUNDA INDICACION ELECTROFISIOLOGIA</td>   <td>SEGUNDA FECHA ELECTROFISIOLOGIA</td>        <td>SEGUNDA INTERVENCION ELECTROFISIOLOGIA</td> <td>IAAS</td>
                <td>AUDITOR</td>
                </tr>
                <?php
                while($resultado=mysql_fetch_array($commitQuery))
                {
                    $fechaRondas = $resultado['fecha_Ronda'];       $habitacions = $resultado['habitacion'];            $historias = $resultado['hc'];                  $ingresos = $resultado['ingreso'];
                    $documentos = $resultado['Pacdoc'];             $nompacs = $resultado['nombre_Pac'];                $edad = $resultado['servicio'];                 $sexos = $resultado['sexo'];
                    $fecingresos = $resultado['fecha_Ing'];         $resingresos = $resultado['reingreso'];             $dxs = $resultado['dx'];                        $comorbs = $resultado['comorb'];
                    $fecegresos = $resultado['fecha_Egreso'];       $fecingrepafs = $resultado['fecha_Paf'];            $fecretpafs = $resultado['retiro_Paf'];         $fec2ingpafs = $resultado['reintegro_Paf'];
                    $fec2retpafs = $resultado['retiro_Paf2'];       $progambs = $resultado['prog_Ambu'];                $indcxs = $resultado['indicacion_Cx'];          $feccxs = $resultado['fecha_Cx'];
                    $cx1s = $resultado['cx1'];                      $cx2s = $resultado['cx2'];                          $cx3s = $resultado['cx3'];                      $fecreints = $resultado['fecha_Reint'];
                    $reint1s = $resultado['reint1'];                $reint2s = $resultado['reint2'];                    $reint3s = $resultado['reint3'];                $fecingucis = $resultado['ingreso_Uci'];
                    $fecegreucis = $resultado['egreso_Uci'];        $fecreingucis = $resultado['reingreso_Uci'];        $fecegre2ucis = $resultado['egreso2_Uci'];      $proghemos = $resultado['prog_hemo'];
                    $indhemos = $resultado['indicacion_Hemod'];     $fechemos = $resultado['fecha_Hemod'];              $interhemos = $resultado['interv_Hemod'];       $progelects = $resultado['prog_electrof'];
                    $indelects = $resultado['indicacion_Electrof']; $fecelects = $resultado['fecha_Electrof'];          $interelect2 = $resultado['interv_Electrof'];   $isos = $resultado['iso'];
                    $observs = $resultado['observacion'];           $notas = $resultado['nota'];                        $resps = $resultado['responsable'];             $alertas = $resultado['alerta'];
                    $tipoegres = $resultado['tipo_egreso'];         $segindhemos = $resultado['indicacion_hemod2'];     $segfechemos = $resultado['fecha_hemod2'];      $seginterhemos = $resultado['interv_hemod2'];
                    $segindelects = $resultado['indicacion_electrof2']; $segfecelects = $resultado['fecha_electrof2'];  $seginterelects = $resultado['interv_electrof2'];   $iaass = $resultado['iaas'];
                    $audits = $resultado['Seguridad'];
                    ?>
                    <tr>
                        <td><?php echo $fechaRondas ?></td>     <td><?php echo $habitacions ?></td>     <td><?php echo $historias ?></td>       <td><?php echo $ingresos ?></td>
                        <td><?php echo $documentos ?></td>      <td><?php echo $nompacs ?></td>         <td><?php echo $edad ?></td>            <td><?php echo $sexos ?></td>
                        <td><?php echo $fecingresos ?></td>     <td><?php echo $resingresos ?></td>     <td><?php echo $dxs ?></td>             <td><?php echo $comorbs ?></td>
                        <td><?php echo $fecegresos ?></td>      <td><?php echo $fecingrepafs ?></td>    <td><?php echo $fecretpafs ?></td>      <td><?php echo $fec2ingpafs ?></td>
                        <td><?php echo $fec2retpafs ?></td>     <td><?php echo $progambs ?></td>        <td><?php echo $indcxs ?></td>          <td><?php echo $feccxs ?></td>
                        <td><?php echo $cx1s ?></td>            <td><?php echo $cx2s ?></td>            <td><?php echo $cx3s ?></td>            <td><?php echo $fecreints ?></td>
                        <td><?php echo $reint1s ?></td>         <td><?php echo $reint2s ?></td>         <td><?php echo $reint3s ?></td>         <td><?php echo $fecingucis ?></td>
                        <td><?php echo $fecegreucis ?></td>     <td><?php echo $fecreingucis ?></td>    <td><?php echo $fecegre2ucis ?></td>    <td><?php echo $proghemos ?></td>
                        <td><?php echo $indhemos ?></td>        <td><?php echo $fechemos ?></td>        <td><?php echo $interhemos ?></td>      <td><?php echo $progelects ?></td>
                        <td><?php echo $indelects ?></td>       <td><?php echo $fecelects ?></td>       <td><?php echo $interelect2 ?></td>     <td><?php echo $isos ?></td>
                        <td><?php echo $observs ?></td>         <td><?php echo $notas ?></td>           <td><?php echo $resps ?></td>           <td><?php echo $alertas ?></td>
                        <td><?php echo $tipoegres ?></td>       <td><?php echo $segindhemos ?></td>     <td><?php echo $segfechemos ?></td>     <td><?php echo $seginterhemos ?></td>
                        <td><?php echo $segindelects ?></td>    <td><?php echo $segfecelects ?></td>    <td><?php echo $seginterelects ?></td>  <td><?php echo $iaass ?></td>
                        <td><?php echo $audits ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
            <?php
        }
    ?>
    </body>
    </html>
    <?php
}
if($accion == 'informe')
{
    ?>
    <!DOCTYPE html>
    <html lang="esp" xmlns="http://www.w3.org/1999/html">
    <head>
        <meta charset="utf-8">
        <title>Consolidados - PAF</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
        <link href="botonespaf.css" rel="stylesheet" type="text/css">
        <link href="estilospaf.css" rel="stylesheet" type="text/css">
        <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
        <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
        <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /><!--Estilo para el calendario-->
        <script src="calendariopaf.js" type="text/javascript"></script>
        <script src="JsProcesospaf.js" type="text/javascript"></script>
        <script>
            $(function() {
                $( "#datepicker1" ).datepicker();
                $( "#datepicker2" ).datepicker();
            });
        </script>
        <script language="javascript">
            var miPopup1;
            function procedcx(descx1,descx2,descx3,fechacx1,tcx)
            {
                miPopup1 = window.open("procedimientospaf.php?descx1="+descx1.value+"&descx2="+descx2.value+"&descx3="+descx3.value+"&fechacx1="+fechacx1.value+"&tcx="+tcx.value,"miwin1","width=470,height=160,top=300,left=550");
                miPopup1.focus()
            }

            var miPopup2;
            function procedem(descem,fechaem,tem)
            {
                miPopup2 = window.open("procedimientospaf.php?descem="+descem.value+"&fechaem="+fechaem.value+"&tem="+tem.value,"miwin1","width=470,height=160,top=300,left=550");
                miPopup2.focus()
            }

            var miPopup3;
            function procedfis(descfis,fechafis,tef)
            {
                miPopup3 = window.open("procedimientospaf.php?descfis="+descfis.value+"&fechafis="+fechafis.value+"&tef="+tef.value,"miwin1","width=470,height=160,top=300,left=550");
                miPopup3.focus()
            }

            var miPopup4;
            function verNota(observacion,nota,fecha_Ronda)
            {
                miPopup4 = window.open("observacionespaf.php?observacion="+observacion.value+"&nota="+nota.value+"&fecha_Ronda="+fecha_Ronda.value,"miwin1","width=680,height=300,top=300,left=550");
                miPopup4.focus()
            }
        </script>
        <?php
        include_once("conex.php");
        if(!isset($_SESSION['user']))
        {
            ?>
            <div align="center">
                <label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix o inicie sesion nuevamente.</label>
            </div>
            <?php
            return;
        }
        else
        {
            $user_session = explode('-', $_SESSION['user']);
            $wuse = $user_session[1];


            include_once("root/comun.php");



            $conex = obtenerConexionBD("matrix");
        }
        include_once("paf/librarypaf.php");

        $accion = $_GET['accion'];          if($accion == null){$accion = $_POST['accion'];}
        $fechaiInf = $_POST['fechaiInf'];   $fechafInf = $_POST['fechafInf'];
        ?>
    </head>

    <body onload="window.resizeTo(800,500)" style="overflow: hidden">
    <div class="panel panel-info">
        <div class="container">
            <form method="post" name="loginform" id="loginform" class="form-horizontal" role="form" action="consolidadopaf.php">
                <h3>Generacion de Informe</h3>
                <h4>Pacientes Ingresados al PAF</h4>
                <div align="center" class="input-group" style="border: none; margin-left: -250px; width: 80%" id="fechaini">
                    <div class="input-group" style="margin: auto; border: none">
                        <span class="input-group-addon"><label for="datepicker1">Fecha Inicial</label></span>
                        <input id="datepicker1" type="text" class="form-control" style="width: 200px" name="fechaiInf" value="<?php echo $fechaiInf ?>">

                        <div class="input-group-addon" style="background-color: #ffffff; width: 10px; border: none"></div>

                        <span class="input-group-addon"><label for="datepicker2">Fecha Final</label></span>
                        <input id="datepicker2" type="text" class="form-control" style="width: 200px" name="fechafInf" value="<?php echo $fechafInf ?>">
                    </div>
                    <div class="input-group-addon" style="background-color: #ffffff; border: none">
                        <input type="hidden" id="accion" name="accion" value="informe">
                        <input type="submit" class="btn btn-info btn-sm" style="margin-top: -4px" id="bntBus" name="btnBus" value="> > >">
                    </div>
                </div>
            </form>
        </div>

        <?php
        if($fechaiInf != null and $fechafInf != null)
        {
            $query = "select a.*, b.Pacdoc from paf_000004 a, cliame_000100 b
                         where a.fecha_Ing between '$fechaiInf' AND '$fechafInf'
                         and b.Pachis = a.hc
                         order by fecha_Ing asc";
            $commitQuery = mysql_query($query, $conex);
            $nroreg = mysql_num_rows($commitQuery);

            if($nroreg > 0)
            {
                ?>
                <div align="center" style="text-align: center; width: 800px; border: none">
                    <h5>NUMERO DE REGISTROS ECONTRADOS: <?php echo ' '.$nroreg ?></h5>
                    <a href="consolidadopaf.php?accion=print&fechaIw=<?php  echo $fechaiInf ?>&fechaFw=<?php  echo $fechafInf ?>">Descargar</a>
                    <br>
                </div>
                <?php
            }
        }
        else
        {
            ?><h4 style="text-align: center; margin-left: -450px; margin-top: 100px">DEBE INGRESAR FECHA INICIAL Y FECHA FINAL</h4><?php
        }
        ?>
    </div>
    </body>
    </html>
    <?php
}
if($accion == 'consolidados')
{
    ?>
    <!DOCTYPE html>
    <html lang="esp" xmlns="http://www.w3.org/1999/html">
    <head>
        <meta charset="utf-8">
        <title>Consolidados - PAF</title>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
        <link href="botonespaf.css" rel="stylesheet" type="text/css">
        <link href="estilospaf.css" rel="stylesheet" type="text/css">
        <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
        <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
        <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /><!--Estilo para el calendario-->
        <script src="calendariopaf.js" type="text/javascript"></script>
        <script src="JsProcesospaf.js" type="text/javascript"></script>
        <script>
            $(function() {
                $( "#datepicker1" ).datepicker();
                $( "#datepicker2" ).datepicker();
            });
        </script>
        <script language="javascript">
            var miPopup1;
            function procedcx(descx1,descx2,descx3,fechacx1,tcx)
            {
                miPopup1 = window.open("procedimientospaf.php?descx1="+descx1.value+"&descx2="+descx2.value+"&descx3="+descx3.value+"&fechacx1="+fechacx1.value+"&tcx="+tcx.value,"miwin1","width=470,height=160,top=300,left=550");
                miPopup1.focus()
            }

            var miPopup2;
            function procedem(descem,fechaem,tem)
            {
                miPopup2 = window.open("procedimientospaf.php?descem="+descem.value+"&fechaem="+fechaem.value+"&tem="+tem.value,"miwin1","width=470,height=160,top=300,left=550");
                miPopup2.focus()
            }

            var miPopup3;
            function procedfis(descfis,fechafis,tef)
            {
                miPopup3 = window.open("procedimientospaf.php?descfis="+descfis.value+"&fechafis="+fechafis.value+"&tef="+tef.value,"miwin1","width=470,height=160,top=300,left=550");
                miPopup3.focus()
            }

            var miPopup4;
            function verNota(observacion,nota,fecha_Ronda)
            {
                miPopup4 = window.open("observacionespaf.php?observacion="+observacion.value+"&nota="+nota.value+"&fecha_Ronda="+fecha_Ronda.value,"miwin1","width=680,height=300,top=300,left=550");
                miPopup4.focus()
            }
        </script>
        <?php
        include_once("conex.php");
        if(!isset($_SESSION['user']))
        {
            ?>
            <div align="center">
                <label>Usuario no autenticado en el sistema.<br />Recargue la pagina principal de Matrix o inicie sesion nuevamente.</label>
            </div>
            <?php
            return;
        }
        else
        {
            $user_session = explode('-', $_SESSION['user']);
            $wuse = $user_session[1];


            include_once("root/comun.php");



            $conex = obtenerConexionBD("matrix");
        }
        include_once("paf/librarypaf.php");

        $accion = $_GET['accion'];          if($accion == null){$accion = $_POST['accion'];}
        $fechaiInf = $_POST['fechaiInf'];   $fechafInf = $_POST['fechafInf'];
        ?>
    </head>
    <body onload="mostrarop1('fechaini','historia','contenido')">
    <div id="loginbox" style="margin-top: 5px; width: 1500px">
        <div class="panel panel-info" >
            <div class="panel-heading">
                <div class="panel-title">Consolidado PAF</div>
            </div>

            <div style="padding-top:30px" class="panel-body">

                <div class="container">
                    <div class="row">
                        <div class="col-md-10 col-md-offset-1">

                            <form method="post" name="loginform" id="loginform" class="form-horizontal" role="form" action="consolidadopaf.php">
                                <table align="center">
                                    <tr>
                                        <td colspan="3" align="center">
                                            <h5 class="text-primary"><strong>Parametros de busqueda: </strong></h5>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="input-group">
                                                <label>Fecha de Ingreso &nbsp;</label><input type="radio" checked name="selparam" id="selparam" value="0" onclick="mostrarop1('fechaini','historia','contenido')">
                                            </div>
                                        </td>
                                        <td><div class="input-group-addon" style="background-color: #ffffff; width: 50px; border: none"></div></td>
                                        <td>
                                            <div class="input-group">
                                                <label>Historia e Ingreso &nbsp;</label><input type="radio" name="selparam" id="selparam" value="1" onclick="mostrarop2('historia','fechaini','contenido')">
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                                <br>
                                <div align="center" class="input-group" style="display: none; border: none; margin: auto; width: 80%" id="fechaini">
                                    <div class="input-group" style="margin: auto; border: none">
                                        <div class="input-group-addon" style="background-color: #ffffff; width: 35px; border: none"></div>
                                        <span class="input-group-addon"><label>Fecha Inicial</label></span>
                                        <input id="datepicker1" type="text" class="form-control" style="width: 200px" name="fechai" value="">

                                        <span class="input-group-addon"><label>Fecha Final</label></span>
                                        <input id="datepicker2" type="text" class="form-control" style="width: 200px" name="fechaf" value="">
                                    </div>
                                    <div class="input-group-addon" style="background-color: #ffffff; border: none">
                                        <input type="submit" class="btn btn-info btn-sm" id="bntBus" name="btnBus" value="> > >">
                                    </div>
                                </div>

                                <div align="center" class="input-group" style="display: none; border: none; margin: auto; width: 80%" id="historia">
                                    <div class="input-group" style="margin: auto; border: none">
                                        <div class="input-group-addon" style="background-color: #ffffff; width: 35px; border: none"></div>
                                        <span class="input-group-addon" style="width: 123px"><label>Historia</label></span>
                                        <input id="datepicker1" type="text" class="form-control" style="width: 200px" name="Bhistoria" value="">

                                        <span class="input-group-addon"><label>Ingreso</label></span>
                                        <input id="datepicker2" type="text" class="form-control" style="width: 200px" name="Bingreso" value="">
                                    </div>
                                    <div class="input-group-addon" style="background-color: #ffffff; border: none">
                                        <input type="submit" class="btn btn-info btn-sm" id="bntBus" name="btnBus" value="> > >">
                                    </div>
                                </div>
                            </form>

                            <br><br>

                            <div class="panel panel-default panel-table" style="width: 1450px; margin-left: -250px; margin-top: 81px" id="contenido">
                                <div class="panel-heading">
                                    <div class="row">
                                        <div class="col col-xs-6">
                                            <h3 class="panel-title">Pacientes con cirugias ordenadas PAF</h3>
                                        </div>
                                        <div class="col col-xs-6 text-right">
                                        </div>
                                    </div>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-striped table-bordered table-list">
                                        <thead>
                                        <tr>
                                            <th><em class="fa fa-cog"></em></th>
                                            <th class="hidden-xs">Habitacion</th>
                                            <th>Historia</th>
                                            <th>Ingreso</th>
                                            <th>Nombre</th>
                                            <th>Estancia Hospitalaria</th>
                                            <th>Estancia PAF</th>
                                            <th>Dias Evitados al PAF</th>
                                            <th>Oportunidad Qxca</th>
                                            <th>Dias Estancia UCI-PAF</th>
                                            <th>Numero de Procedimientos</th>
                                            <th>Numero de Reintervenciones</th>
                                            <th>Cx + Hem</th>
                                            <th>Cx + EEFF</th>
                                            <th>Cx + Hem + EEFF</th>
                                        </tr>
                                        </thead>
                                        <?php
                                        $Bfecha_ini = $_POST['fechai'];
                                        $Bfecha_fin = $_POST['fechaf'];
                                        $Bhistoria = $_POST['Bhistoria'];
                                        $Bingreso = $_POST['Bingreso'];

                                        if($Bfecha_ini != null and $Bfecha_fin != null)
                                        {
                                            $query = mysql_query("SELECT * FROM paf_000004 WHERE fecha_Ing BETWEEN '$Bfecha_ini' AND '$Bfecha_fin' GROUP BY hc ORDER BY fecha_Ronda ASC");
                                        }
                                        if($Bhistoria != null and $Bingreso != null)
                                        {
                                            $query = mysql_query("SELECT * FROM paf_000004 WHERE hc = '$Bhistoria' AND ingreso = '$Bingreso' GROUP BY hc ORDER BY fecha_Ronda ASC");
                                        }
                                        if($Bhistoria != null)
                                        {
                                            $query = mysql_query("SELECT * FROM paf_000004 WHERE hc = '$Bhistoria' GROUP BY hc ORDER BY fecha_Ronda ASC");
                                        }
                                        else
                                        {
                                            $query=mysql_query("select * from paf_000004 GROUP BY hc ORDER BY fecha_Ronda ASC LIMIT 20");
                                        }

                                        while($dato=mysql_fetch_array($query))
                                        {
                                            $habitacion=$dato['habitacion'];
                                            $historia=$dato['hc'];
                                            $ingreso=$dato['ingreso'];
                                            $nombre=$dato['nombre_Pac'];
                                            $fecha=$dato['fecha_Ing'];
                                            $fecha_egreso=$dato['fecha_Egreso'];
                                            $retiro_Paf=$dato['retiro_Paf'];
                                            $fecha_Paf=$dato['fecha_Paf'];
                                            $retiro_Paf2=$dato['retiro_Paf2'];
                                            $reintegro_Paf=$dato['reintegro_Paf'];
                                            $fecha_Cx=$dato['fecha_Cx'];
                                            $indicacion_Cx=$dato['indicacion_Cx'];
                                            $egreso_Uci=$dato['egreso_Uci'];
                                            $ingreso_Uci=$dato['ingreso_Uci'];
                                            $egreso2_Uci=$dato['egreso2_Uci'];
                                            $reingreso_Uci=$dato['reingreso_Uci'];
                                            $fecha_Ronda=$dato['fecha_Ronda'];
                                            ?>
                                            <tbody>
                                            <tr>
                                                <form method="post">
                                                    <td><?php notasPacientes($historia,$ingreso) ?></td>
                                                    <td class="hidden-xs"><?php echo $habitacion ?></td>
                                                    <td><?php echo $historia ?></td>
                                                    <td><?php echo $ingreso ?></td>
                                                    <td><?php echo $nombre ?></td>
                                                    <td title="Estancia Hospitalaria"><?php diasEstancia2($fecha,$historia) ?></td>
                                                    <td title="Estancia PAF"><?php estanciaPAF($historia) ?></td>
                                                    <td title="Dias Evitados al PAF"><?php diasEvitadosPAF($historia) ?></td>
                                                    <td title="Oportunidad Qxca"><?php oportunidadqx($historia) ?></td>
                                                    <td title="Dias Estancia UCI-PAF"><?php diasEstanciaUci($historia) ?></td>
                                                    <td title="Numero de Procedimientos"><?php nprocedimientos($historia,$ingreso) ?></td>
                                                    <td title="Numero de Reintervenciones"><?php nreinterv($historia,$ingreso) ?></td>
                                                    <td title="Cx + Hem"><?php cxem($historia,$ingreso) ?></td>
                                                    <td title="Cx + EEFF"><?php cxef($historia,$ingreso) ?></td>
                                                    <td title="Cx + Hem + EEFF"><?php cxemef($historia,$ingreso) ?></td>
                                                </form>
                                            </tr>
                                            </tbody>
                                            <?php
                                        }
                                        ?>
                                    </table>
                                </div>

                                <div class="panel-footer">
                                    <div class="row">
                                        <div class="col col-xs-4">Pagina 1 of 5</div>
                                        <div class="col col-xs-8">
                                            <ul class="pagination hidden-xs pull-right">
                                                <li><a href="#">1</a></li>
                                                <li><a href="#">2</a></li>
                                                <li><a href="#">3</a></li>
                                                <li><a href="#">4</a></li>
                                                <li><a href="#">5</a></li>
                                            </ul>
                                            <ul class="pagination visible-xs pull-right">
                                                <li><a href="#">«</a></li>
                                                <li><a href="#">»</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </body>
    </html>
    <?php

}
?>