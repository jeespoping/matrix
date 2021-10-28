<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <title>BASE DE DATOS - PAF</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="estilospaf.css" rel="stylesheet">
    <script src="JsProcesospaf.js"></script>
    <script language="javascript">
        function abreVentana1(parametro)
        {
            miPopup = window.open("consolidadopaf.php?accion="+parametro,"miwin","width=1500,height=800,scrollbars=yes");
            miPopup.focus()
        }

        function abreVentana2()
        {
            miPopup2 = window.open("analisispaf.php","miwin2","width=1300,height=800,scrollbars=yes");
            miPopup2.focus()
        }

        function abreVentana3()
        {
            miPopup3 = window.open("historicopaf.php","miwin3","width=1300,height=850,scrollbars=yes");
            miPopup3.focus()
        }
    </script> <!-- ABRIR VENTANAS DE FUNCIONES -->
    <script>
        function validar_Browser()
        {
            var BrowserDetect = {
                init: function () {
                    this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
                    this.version = this.searchVersion(navigator.userAgent)
                        || this.searchVersion(navigator.appVersion)
                        || "an unknown version";
                    this.OS = this.searchString(this.dataOS) || "an unknown OS";
                },
                searchString: function (data)
                {
                    for (var i=0;i<data.length;i++)	{
                        var dataString = data[i].string;
                        var dataProp = data[i].prop;
                        this.versionSearchString = data[i].versionSearch || data[i].identity;
                        if (dataString) {
                            if (dataString.indexOf(data[i].subString) != -1)
                                return data[i].identity;
                        }
                        else if (dataProp)
                            return data[i].identity;
                    }
                },
                searchVersion: function (dataString)
                {
                    var index = dataString.indexOf(this.versionSearchString);
                    if (index == -1) return;
                    return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
                },
                dataBrowser: [
                    { string: navigator.userAgent,
                        subString: "OmniWeb",
                        versionSearch: "OmniWeb/",
                        identity: "OmniWeb"
                    },
                    {
                        string: navigator.vendor,
                        subString: "Apple",
                        identity: "Safari"
                    },
                    {
                        prop: window.opera,
                        identity: "Opera"
                    },
                    {
                        string: navigator.vendor,
                        subString: "iCab",
                        identity: "iCab"
                    },
                    {
                        string: navigator.vendor,
                        subString: "KDE",
                        identity: "Konqueror"
                    },
                    {
                        string: navigator.userAgent,
                        subString: "Firefox",
                        identity: "Firefox"
                    },
                    {
                        string: navigator.vendor,
                        subString: "Camino",
                        identity: "Camino"
                    },
                    {	// for newer Netscapes (6+)
                        string: navigator.userAgent,
                        subString: "Netscape",
                        identity: "Netscape"
                    },
                    {
                        string: navigator.userAgent,
                        subString: "MSIE",
                        identity: "Explorer",
                        versionSearch: "MSIE"
                    },
                    {
                        string: navigator.userAgent,
                        subString: "Gecko",
                        identity: "Mozilla",
                        versionSearch: "rv"
                    },
                    { // for older Netscapes (4-)
                        string: navigator.userAgent,
                        subString: "Mozilla",
                        identity: "Netscape",
                        versionSearch: "Mozilla"
                    }
                ],
                dataOS : [
                    {
                        string: navigator.platform,
                        subString: "Win",
                        identity: "Windows"
                    },
                    {
                        string: navigator.platform,
                        subString: "Mac",
                        identity: "Mac"
                    },
                    {
                        string: navigator.platform,
                        subString: "Linux",
                        identity: "Linux"
                    }
                ]

            };
            BrowserDetect.init();

            //script para poner estilos distintos para cada navegador
            if (BrowserDetect.browser != "Firefox") {
                alert("Tu navegador NO es Mozilla Firefox, esta aplicacion podria no funcionar correctamente, por favor, cambie de navegador")
            }
        }
    </script> <!-- VALIDAR BROWSER -->
    <?php
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
        include("conex.php");
        include("root/comun.php");
        mysql_select_db("matrix");

        $conex = obtenerConexionBD("matrix");
    }
    include("paf/librarypaf.php");
    ?>
</head>

<body onload="validar_Browser()">

<div class="container" style="margin-top: -30px; margin-left: 10px">
    <div id="loginbox" style="margin-top:50px; width: 1300px">
        <div class="panel panel-info" >
            <div class="panel-heading">
                <div class="panel-title">BASE DE DATOS - PAF</div>
            </div>
            <div style="padding-top:30px" class="panel-body" >
                <div id="divServicio" class="form-horizontal">
                    <form style="margin-left: -60px" name="bitacorapaf" method="post" action="bitacorapaf.php">
                        <table align="center" width="400">
                            <tr align="center">
                                <td>&nbsp;&nbsp;&nbsp;</td>
                                <td><label>Habitacion</label></td>
                                <td>&nbsp;</td>
                                <td><label>Historia Clinica</label></td>
                                <td>&nbsp;</td>
                                <td><label>Paciente Inactivo</label></td>
                            </tr>
                            <tr align="center">
                                <td>&nbsp;&nbsp;&nbsp;</td>
                                <td><input type="radio" id="radioHabitacion" name="radio" value="1" onclick="habitacion('divServicio','divEspecialista')"/></td>
                                <td>&nbsp;</td>
                                <td><input type="radio" id="radioHistoria" name="radio" value="2" onclick="historia('divServicio','divEspecialista')"/></td>
                                <td>&nbsp;</td>
                                <td><input type="radio" id="radioInactivos" name="radio" value="3"/></td>
                            </tr>
                        </table>
                        <br>
                        <table align="center">
                            <tr>
                                <td>
                                    <div style="margin-bottom: 25px; margin-left: 70px" class="input-group">
                                        <div class="input-group">
                                            <input id="login-username" type="search" class="form-control" style="width: 300px" name="buscar" value="">
                                            <input type="submit" style="margin-left: 10px" name="btnbuscar" class="btn btn-success" value="Filtrar">
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </form>

                    <?php
                    $valorRadio = $_POST['radio'];
                    $busca = $_POST['buscar'];
                    //$buscado = '%'.$busca.'%';
                    $buscado = $busca;
                    $fecha_actual = date('Y-m-d');
                    $fechaanterior = strtotime ( '-30 day' , strtotime ( $fecha_actual ) ) ; // Mostrar desde 30 dias antes de la fecha actual //
                    $fechaanterior = date ( 'Y-m-d' , $fechaanterior );

                    if($valorRadio == 0)
                    {
                        $query = mysql_queryV("SELECT a.Inghis, a.Inging, a.Ingnre, a.Ingres, f.Ingfei, b.Habcod, c.Pacno1, c.Pacno2, c.Pacap1, c.Pacap2, c.Pacsex, c.Pacfna, d.Ubisac
                                                FROM movhos_000016 a
                                                             left join
                                                     movhos_000020 b on (a.Inghis=b.Habhis and a.Inging=b.Habing),cliame_000100 c,movhos_000018 d,root_000037 e,cliame_000101 f
                                                WHERE a.Fecha_data BETWEEN '$fechaanterior' AND '$fecha_actual'
                                                       AND a.Ingres in ('800088702CV','800088702CS','900156264CV','900156264CS','800130907CV','800130907CS')
                                                       AND f.Ingsei not in('1290','1251','1033','1250','1320','1075')
                                                       AND f.Ingdes in ('2','6') /*QUE EL DESTINO EN LA ADMISION SEA HOSPITALIZACION, URGENCIAS*/
                                                       AND f.Inghis = a.Inghis
                                                       AND f.Ingnin = a.Inging
                                                       AND a.Inghis=c.Pachis
                                                       AND a.Inghis=d.Ubihis
                                                       AND a.Inging=d.Ubiing
                                                       AND a.Inghis=e.Orihis
                                                       AND a.Inging=e.Oriing
                                                       AND e.Oriori = '01'
                                                ORDER BY f.Ingfei ASC");
                    }
                    if($valorRadio == 1) //HABITACION
                    {
                        $query = mysql_queryV("SELECT a.Inghis, a.Inging, a.Ingnre, a.Ingres, a.Fecha_data, b.Habcod, c.Pacno1, c.Pacno2, c.Pacap1, c.Pacap2, c.Pacsex, c.Pacfna, d.Ubisac
                                                FROM movhos_000020 b, movhos_000016 a, cliame_000100 c, movhos_000018 d
                                                WHERE b.Habcod LIKE '$buscado%'
                                                AND b.Habhis = a.Inghis
                                                AND b.Habing = a.Inging
                                                AND a.Inghis = c.Pachis
                                                AND a.Inghis = d.Ubihis
                                                AND a.Inging = d.Ubiing
                                                ORDER BY a.Fecha_data ASC");
                    }
                    if($valorRadio == 2) //HISTORIA
                    {
                        $query = mysql_queryV("SELECT Pachis, Ingnin, Ingfei, Pacno1, Pacno2, Pacap1, Pacap2, Ingsei, Ingent, Pacfna, Pacsex
                                                FROM cliame_000100, cliame_000101
                                                WHERE Pachis = '$buscado'
                                                AND Pachis = Inghis
                                                ORDER BY ingfei ASC");
                    }
                    if($valorRadio == 3)
                    {
                        if($buscado != '')
                        {
                            $query = mysql_queryV("SELECT a.Ubihis, a.Ubiing, e.Ingfei, b.Pacno1, b.Pacno2, b.Pacap1,b.Pacap2, a.Ubisac, c.sexo, d.Ingnre, b.Pacfna
                                                    FROM paf_000004 c, movhos_000018 a, movhos_000016 d, cliame_000100 b, cliame_000101 e
                                                    WHERE c.hc = '$buscado'
                                                    AND c.hc = a.Ubihis
                                                    AND e.Inghis = a.Ubihis
                                                    AND e.Ingnin = a.Ubiing
                                                    AND a.Ubihis  = d.Inghis
                                                    AND a.Ubiing  = d.Inging
                                                    AND a.Ubihis  = b.Pachis
                                                    GROUP BY a.Ubiing
                                                    ORDER BY e.Ingfei ASC");
                        }
                    }
                    ?>

                    <div align="center" style="margin-top: 20px">

                        <div class="container" style="margin-left: 60px">
                            <div class="row">
                                <div>
                                    <a href="#" class="btn btn-primary" onclick="abreVentana1('consolidados')" style="width: 150px">Consolidados</a>
                                    <a href="#" class="btn btn-primary" onclick="abreVentana2()" style="width: 150px">Analisis</a>
                                    <a href="#" class="btn btn-primary" onclick="abreVentana3()" style="width: 150px">Historico</a>
                                    <a href="#" class="btn btn-primary" onclick="abreVentana1('informe')" style="width: 150px">Generar Informe</a>
                                </div>
                            </div>
                        </div>

                        <div style="border: 1px solid #aaa; height:900px; overflow:auto; color:#135; margin-top: 25px">
                            <table class="table table-striped table-hover" style="margin-top: 10px;font-size:small;margin-bottom: 25px">
                                <thead style="font-size: smaller">
                                <tr>
                                    <th>HAB</th>
                                    <th>HC</th>
                                    <th>ING</th>
                                    <th>FECHA ING</th>
                                    <th>NOMBRE</th>
                                    <th>SERVICIO</th>
                                    <th>RESPONSABLE</th>
                                    <th>DIAS ESTANCIA</th>
                                    <th>FECHA RONDA</th>
                                    <th class="text-center">Accion</th>
                                </tr>
                                </thead>
                                <?php
                                while($dato = mysql_fetch_array($query))
                                {
                                    $habitacion=$dato['Habcod'];
                                    $historia=$dato['Inghis']; if($historia == ''){$historia=$dato['Ubihis'];} if($historia == ''){$historia=$dato['Pachis'];}
                                    $ingreso=$dato['Inging']; if($ingreso == ''){$ingreso=$dato['Ubiing'];} if($ingreso == ''){$ingreso=$dato['Ingnin'];}
                                    $fecha_ing=$dato['Ingfei']; if($fecha_ing == ''){$fecha_ing=$dato['Fecha_data'];}
                                    $nombre=$dato['Pacno1'].' '.$dato['Pacno2'].' '.$dato['Pacap1'].' '.$dato['Pacap2'];
                                    $responsable=$dato['Ingnre']; if($responsable == ''){$responsable=$dato['responsable'];} if($responsable == ''){$responsable=$dato['Ingent'];}
                                    $cod_responsable=$dato['Ingres']; if($cod_responsable == ''){$cod_responsable=$dato['Ingcem'];}
                                    $servicio=$dato['Ubisac']; if($servicio == ''){$servicio=$dato['Ingsei'];}
                                    $queryServicio = "select Cconom from movhos_000011 where Ccocod = '$servicio'";
                                    $commQryServicio = mysql_query($queryServicio) or die (mysql_errno()." - en el query: ".$queryServicio." - ".mysql_error());
                                    $datoServicio = mysql_fetch_array($commQryServicio);
                                    $nombreServicio = $datoServicio[0];
                                    $sexo=$dato['Pacsex']; if($sexo == ''){$sexo=$dato['sexo'];}
                                    $fecha=$dato['Pacfna']; //FECHA DE NACIMIENTO

                                    //$query2=mysql_queryV("select * from paf_000004 WHERE hc = '$historia' AND ingreso = '$ingreso' ORDER BY fecha_Ronda DESC LIMIT 1");
                                    $query2=mysql_queryV("select * from paf_000004 WHERE hc = '$historia' AND ingreso = '$ingreso' ORDER BY fecha_Ronda DESC LIMIT 1");
                                    $dato2=mysql_fetch_array($query2);
                                    $hc2=$dato2['hc'];          $ingreso2=$dato2['ingreso'];            $fecha_ronda=$dato2['fecha_Ronda'];
                                    $alerta=$dato2['alerta'];   $retiroPaf04 = $dato2['retiro_Paf'];    $reintegroPaf04 = $dato2['reintegro_Paf'];

                                    if($hc2 == $historia and $ingreso2 == $ingreso)  //SI YA TIENE RONDA DE AUDITORIA REGISTRADA EN PAF_000004
                                    {
                                        if($valorRadio == null)
                                        {
                                            if($retiroPaf04 == null and $reintegroPaf04 == null)
                                            {
                                                ?>
                                                <tbody>
                                                <tr>
                                                    <form method="post" action="cirugiapaf.php">
                                                        <td><label style="color: #3CB248"><?php echo $habitacion ?></label></td>
                                                        <td><label style="color: #3CB248"><?php echo $historia ?></label></td>
                                                        <td><label style="color: #3CB248"><?php echo $ingreso ?></label></td>
                                                        <td><label style="color: #3CB248"><?php echo $fecha_ing ?></label></td>
                                                        <td><label style="color: #3CB248"><?php echo $nombre ?></label></td>
                                                        <td title="<?php echo $nombreServicio ?>"><label style="color: #3CB248"><?php echo $servicio ?></label></td>
                                                        <td><label style="color: #3CB248"><?php echo $responsable ?></label></td>
                                                        <td><label style="color: #3CB248"><?php diasEstancia($fecha_ing) ?></label></td>
                                                        <td><label style="color: #3CB248"><?php echo $fecha_ronda ?></label></td>
                                                        <input type="hidden" name="habitacion" value="<?php echo $habitacion ?>">
                                                        <input type="hidden" name="historia" value="<?php echo $historia ?>">
                                                        <input type="hidden" name="ingreso" value="<?php echo $ingreso ?>">
                                                        <input type="hidden" name="fecha_ing" value="<?php echo $fecha_ing ?>">
                                                        <input type="hidden" name="nombre" value="<?php echo $nombre ?>">
                                                        <input type="hidden" name="servicio" value="<?php echo $servicio ?>">
                                                        <input type="hidden" name="responsable" value="<?php echo $cod_responsable ?>">
                                                        <input type="hidden" name="nom_responsable" value="<?php echo $responsable ?>">
                                                        <input type="hidden" name="sexo" value="<?php echo $sexo ?>">
                                                        <input type="hidden" name="edad" value="<?php edad($fecha) ?>">
                                                        <input type="hidden" name="fechaNac" value="<?php echo $fecha ?>">
                                                        <?php
                                                        if($alerta == '' or $alerta == 'NO')
                                                        {
                                                            ?>
                                                            <td><button class="btn btn-default fa fa-eraser" style="width: 20px; height: 20px; background-color: #F0FFF0; border-color: #3CB248" title="Registrar"></button></td>
                                                            <?php
                                                        }
                                                        elseif($alerta == 'SI')
                                                        {
                                                            ?>
                                                            <td><button class="btn btn-default fa fa-eraser" style="width: 20px; height: 20px; background-color: #cd0a0a; border-color: #843534" title="Registrar - Paciente con alerta"></button></td>
                                                            <?php
                                                        }
                                                        ?>
                                                    </form>
                                                </tr>
                                                </tbody>
                                                <?php
                                            }
                                            elseif($retiroPaf04 != null and $reintegroPaf04 != null)
                                            {
                                                ?>
                                                <tbody>
                                                <tr>
                                                    <form method="post" action="cirugiapaf.php">
                                                        <td><label style="color: #3CB248"><?php echo $habitacion ?></label></td>
                                                        <td><label style="color: #3CB248"><?php echo $historia ?></label></td>
                                                        <td><label style="color: #3CB248"><?php echo $ingreso ?></label></td>
                                                        <td><label style="color: #3CB248"><?php echo $fecha_ing ?></label></td>
                                                        <td><label style="color: #3CB248"><?php echo $nombre ?></label></td>
                                                        <td title="<?php echo $nombreServicio ?>"><label style="color: #3CB248"><?php echo $servicio ?></label></td>
                                                        <td><label style="color: #3CB248"><?php echo $responsable ?></label></td>
                                                        <td><label style="color: #3CB248"><?php diasEstancia($fecha_ing) ?></label></td>
                                                        <td><label style="color: #3CB248"><?php echo $fecha_ronda ?></label></td>
                                                        <input type="hidden" name="habitacion" value="<?php echo $habitacion ?>">
                                                        <input type="hidden" name="historia" value="<?php echo $historia ?>">
                                                        <input type="hidden" name="ingreso" value="<?php echo $ingreso ?>">
                                                        <input type="hidden" name="fecha_ing" value="<?php echo $fecha_ing ?>">
                                                        <input type="hidden" name="nombre" value="<?php echo $nombre ?>">
                                                        <input type="hidden" name="servicio" value="<?php echo $servicio ?>">
                                                        <input type="hidden" name="responsable" value="<?php echo $cod_responsable ?>">
                                                        <input type="hidden" name="nom_responsable" value="<?php echo $responsable ?>">
                                                        <input type="hidden" name="sexo" value="<?php echo $sexo ?>">
                                                        <input type="hidden" name="edad" value="<?php edad($fecha) ?>">
                                                        <input type="hidden" name="fechaNac" value="<?php echo $fecha ?>">
                                                        <?php
                                                        if($alerta == '' or $alerta == 'NO')
                                                        {
                                                            ?>
                                                            <td><button class="btn btn-default fa fa-eraser" style="width: 20px; height: 20px; background-color: #F0FFF0; border-color: #3CB248" title="Registrar"></button></td>
                                                            <?php
                                                        }
                                                        elseif($alerta == 'SI')
                                                        {
                                                            ?>
                                                            <td><button class="btn btn-default fa fa-eraser" style="width: 20px; height: 20px; background-color: #cd0a0a; border-color: #843534" title="Registrar - Paciente con alerta"></button></td>
                                                            <?php
                                                        }
                                                        ?>
                                                    </form>
                                                </tr>
                                                </tbody>
                                                <?php
                                            }
                                        }
                                        else
                                        {
                                            ?>
                                            <tbody>
                                            <tr>
                                                <form method="post" action="cirugiapaf.php">
                                                    <td><label style="color: #3CB248"><?php echo $habitacion ?></label></td>
                                                    <td><label style="color: #3CB248"><?php echo $historia ?></label></td>
                                                    <td><label style="color: #3CB248"><?php echo $ingreso ?></label></td>
                                                    <td><label style="color: #3CB248"><?php echo $fecha_ing ?></label></td>
                                                    <td><label style="color: #3CB248"><?php echo $nombre ?></label></td>
                                                    <td title="<?php echo $nombreServicio ?>"><label style="color: #3CB248"><?php echo $servicio ?></label></td>
                                                    <td><label style="color: #3CB248"><?php echo $responsable ?></label></td>
                                                    <td><label style="color: #3CB248"><?php diasEstancia($fecha_ing) ?></label></td>
                                                    <td><label style="color: #3CB248"><?php echo $fecha_ronda ?></label></td>
                                                    <input type="hidden" name="habitacion" value="<?php echo $habitacion ?>">
                                                    <input type="hidden" name="historia" value="<?php echo $historia ?>">
                                                    <input type="hidden" name="ingreso" value="<?php echo $ingreso ?>">
                                                    <input type="hidden" name="fecha_ing" value="<?php echo $fecha_ing ?>">
                                                    <input type="hidden" name="nombre" value="<?php echo $nombre ?>">
                                                    <input type="hidden" name="servicio" value="<?php echo $servicio ?>">
                                                    <input type="hidden" name="responsable" value="<?php echo $cod_responsable ?>">
                                                    <input type="hidden" name="nom_responsable" value="<?php echo $responsable ?>">
                                                    <input type="hidden" name="sexo" value="<?php echo $sexo ?>">
                                                    <input type="hidden" name="edad" value="<?php edad($fecha) ?>">
                                                    <input type="hidden" name="fechaNac" value="<?php echo $fecha ?>">
                                                    <?php
                                                    if($alerta == '' or $alerta == 'NO')
                                                    {
                                                        ?>
                                                        <td><button class="btn btn-default fa fa-eraser" style="width: 20px; height: 20px; background-color: #F0FFF0; border-color: #3CB248" title="Registrar"></button></td>
                                                        <?php
                                                    }
                                                    elseif($alerta == 'SI')
                                                    {
                                                        ?>
                                                        <td><button class="btn btn-default fa fa-eraser" style="width: 20px; height: 20px; background-color: #cd0a0a; border-color: #843534" title="Registrar - Paciente con alerta"></button></td>
                                                        <?php
                                                    }
                                                    ?>
                                                </form>
                                            </tr>
                                            </tbody>
                                            <?php
                                        }
                                    }
                                    else
                                    {
                                        ?>
                                        <tbody>
                                        <tr>
                                            <form method="post" action="cirugiapaf.php">
                                                <td><?php echo $habitacion ?></td>
                                                <td><?php echo $historia ?></td>
                                                <td><?php echo $ingreso ?></td>
                                                <td><?php echo $fecha_ing ?></td>
                                                <td><?php echo $nombre ?></td>
                                                <td title="<?php echo $nombreServicio ?>"><?php echo $servicio ?></td>
                                                <td><?php echo $responsable ?></td>
                                                <td><?php diasEstancia($fecha_ing) ?></td>
                                                <td><?php echo $fecha_ronda ?></td>
                                                <input type="hidden" name="habitacion" value="<?php echo $habitacion ?>">
                                                <input type="hidden" name="historia" value="<?php echo $historia ?>">
                                                <input type="hidden" name="ingreso" value="<?php echo $ingreso ?>">
                                                <input type="hidden" name="fecha_ing" value="<?php echo $fecha_ing ?>">
                                                <input type="hidden" name="nombre" value="<?php echo $nombre ?>">
                                                <input type="hidden" name="servicio" value="<?php echo $servicio ?>">
                                                <input type="hidden" name="responsable" value="<?php echo $cod_responsable ?>">
                                                <input type="hidden" name="nom_responsable" value="<?php echo $responsable ?>">
                                                <input type="hidden" name="sexo" value="<?php echo $sexo ?>">
                                                <input type="hidden" name="edad" value="<?php edad($fecha) ?>">
                                                <input type="hidden" name="fechaNac" value="<?php echo $fecha ?>">
                                                <td><button class="btn btn-default fa fa-eraser" style="width: 20px; height: 20px" title="Registrar"></button></td>
                                            </form>
                                        </tr>
                                        </tbody>
                                        <?php
                                    }
                                }
                                ?>
                            </table>
                            <script type="text/javascript"></script>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>