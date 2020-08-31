<!DOCTYPE html>
<html lang="esp">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre eñes y tildes -->
    <meta charset="utf-8">
    <title>Registro de Actividades</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/Estilos.css" rel="stylesheet">
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
    <script src="regact/regact09.js" type="text/javascript"></script>
    <style type="text/css">
        .filterable {
            margin-top: 15px;
        }
        .filterable .panel-heading .pull-right {
            margin-top: -20px;
        }
        .filterable .filters input[disabled] {
            background-color: transparent;
            border: none;
            cursor: auto;
            box-shadow: none;
            padding: 0;
            height: auto;
        }
        .filterable .filters input[disabled]::-webkit-input-placeholder {
            color: #333;
        }
        .filterable .filters input[disabled]::-moz-placeholder {
            color: #333;
        }
        .filterable .filters input[disabled]:-ms-input-placeholder {
            color: #333;
        }
    </style>
    <script>
        var miPopup2;
        function borrarArchivo(idA,id_Registro)
        {
            miPopup1 = window.open("regact06.php?id="+idA.value+"&accion="+'1'+"idCaso="+id_Registro.value,"miwin1","width=500,height=300,status=0,toolbar=0");
            miPopup1.focus()
        }
    </script> <!-- ELIMINAR REGISTRO -->
    <script>
        var miPopup2;
        function borrarFile(idCaso)
        {
            question = confirm("Realmente desea eliminar este registro?");

            if(question != "0")
            {
                miPopup1 = window.open("regact06.php?id_File="+idCaso.value+"&accion="+'2',"miwin1","width=500,height=300,status=0,toolbar=0");
                miPopup1.focus();
            }
        }
    </script> <!-- ELIMINAR IMAGEN -->
    <script>
        function mostrarop1(fechaini,historia,responsable)
        {
            document.getElementById(fechaini).style.display = 'block';
            document.getElementById(historia).style.display = 'none';
            document.getElementById(responsable).style.display = 'none';
        }

        function mostrarop2(historia,fechaini,responsable)
        {
            document.getElementById(fechaini).style.display = 'none';
            document.getElementById(historia).style.display = 'block';
            document.getElementById(responsable).style.display = 'none';
        }

        function mostrarop3(historia,fechaini,responsable)
        {
            document.getElementById(fechaini).style.display = 'none';
            document.getElementById(historia).style.display = 'none';
            document.getElementById(responsable).style.display = 'block';
        }
    </script> <!-- MOSTRAR Y OCULTAR PARAMETROS DE BUSQUEDA -->
    <script>
        function copiaValor(responsable,responsableRol)
        {
            document.getElementById(responsableRol).value = document.getElementById(responsable).value;
        }
    </script> <!-- MOSTRAR COMO RESPONSABLE EL USUARIO QUE INICIO SESION EN MATRIX -->
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
                alert("Tu navegador NO es Mozilla Firefox, esta aplicación podria no funcionar correctamente, por favor, cambie de navegador")
            }
        }
    </script>
    <?php
include_once("conex.php");
    include_once("regact/regact02.php");

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

    $idCasoviene = $_POST['idCaso'];
    $casoBuscado = $_GET['casoB'];
    if($casoBuscado == null){$casoBuscado = $_POST['casoB'];}
    $parametro = $_POST['selparam'];
    ?>
</head>

<body onload="validar_Browser(); mostrarop1('fechaini','historia')">
<div class="container" style="margin-top: -30px; margin-left: 15px">
    <div id="loginbox" style="margin-top:30px" class="">
        <div class="panel panel-info" >
            <div class="panel-heading" style="background-color: #F14AA4">
                <div class="panel-title" style="color: #ffffff; font-weight: bold">Registro de Actividades - Clinica Las Americas</div>
            </div>

            <div style="padding-top:10px; position: relative; min-height: 1000px" class="panel-body" >

                <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12">
                </div>
                <form id="loginform" name="buscar" class="form-horizontal" role="form" method="post" action="regact01.php">
                    <div style="margin-top:1px" class="form-group" align="center">
                        <?php
                        $queryUser = mysql_query("select * from regact_000003 WHERE Codigo = '$wuse'");
                        $datoUser = mysql_fetch_array($queryUser);
                        $descUser = $datoUser['Descripcion'];
                        $rolUser = $datoUser['Rol'];
                        if($descUser == null)
                        {
                            ?>
                            <div align="center">
                                <label>Usuario no registrado en tabla de roles (regact_000003),</label><br><label>pongase en contacto con el area de Informatica</label>
                            </div>
                            <?php
                        }
                        /*
                        else
                        {
                            ?>
                            <label>RESPONSABLE: </label>
                            <label><?php echo $descUser ?></label>
                            <?php
                        }
                        */
                        ?>
                    </div>
                </form>

                <!-- FORMULARIO DE BUSQUEDA POR DIA DEL MES O PALABRA CLAVE -->
                <form id="loginform" name="buscar" class="form-horizontal" role="form" method="post" action="regact01.php">
                    <table align="center">
                        <tr>
                            <td colspan="5" align="center">
                                <h5 class="text-primary"><strong>Parametros de busqueda: </strong></h5>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="input-group">
                                    <label>Dia del mes &nbsp;</label><input type="radio" checked name="selparam" id="selparam" value="0" onclick="mostrarop1('fechaini','historia','responsable')">
                                </div>
                            </td>
                            <td><div class="input-group-addon" style="background-color: #ffffff; width: 50px; border: none"></div></td>
                            <td>
                                <div class="input-group">
                                    <label>Palabra clave &nbsp;</label><input type="radio" name="selparam" id="selparam" value="1" onclick="mostrarop2('historia','fechaini','responsable')">
                                </div>
                            </td>
                            <td><div class="input-group-addon" style="background-color: #ffffff; width: 50px; border: none"></div></td>
                            <td>
                                <div class="input-group">
                                    <label>Responsable Actividad &nbsp;</label><input type="radio" name="selparam" id="selparam" value="2" onclick="mostrarop3('historia','fechaini','responsable')">
                                </div>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <div align="center" class="input-group" style="display: none; border: none; margin: auto; width: 40%" id="fechaini">
                        <div class="input-group" style="margin-left: 60px ;border: none">
                            <div class="input-group-addon" style="background-color: #ffffff; width: 35px; border: none"></div>
                            <span class="input-group-addon"><label>Dia</label></span>
                            <select class="form-control" style="width: 200px" name="dia">
                                <option>1</option>
                                <option>2</option>
                                <option>3</option>
                                <option>4</option>
                                <option>5</option>
                                <option>6</option>
                                <option>7</option>
                                <option>8</option>
                                <option>9</option>
                                <option>10</option>
                                <option>11</option>
                                <option>12</option>
                                <option>13</option>
                                <option>14</option>
                                <option>15</option>
                                <option>16</option>
                                <option>17</option>
                                <option>18</option>
                                <option>19</option>
                                <option>20</option>
                                <option>21</option>
                                <option>22</option>
                                <option>23</option>
                                <option>24</option>
                                <option>25</option>
                                <option>26</option>
                                <option>27</option>
                                <option>28</option>
                                <option>29</option>
                                <option>30</option>
                                <option>31</option>
                                <option selected disabled>Seleccione...</option>
                            </select>
                        </div>
                        <div class="input-group-addon" style="background-color: #ffffff; border: none">
                            <input type="submit" class="btn btn-info btn-sm" id="bntBus" name="btnBuscar" value="> > >">
                        </div>
                    </div>

                    <div align="center" class="input-group" style="display: none; border: none; margin: auto; width: 80%" id="historia">
                        <div class="input-group" style="margin-left: -40px; border: none">
                            <div class="input-group-addon" style="background-color: #ffffff; width: 35px; border: none"></div>
                            <span class="input-group-addon" style="width: 123px"><label>Palabra</label></span>
                            <input id="historia" type="text" class="form-control" style="width: 200px" name="buscar" value="">
                        </div>
                        <div class="input-group-addon" style="background-color: #ffffff; border: none">
                            <input type="submit" class="btn btn-info btn-sm" id="btnBuscar" name="btnBuscar" value="> > >">
                        </div>
                    </div>

                    <div align="center" class="input-group" style="display: none; border: none; margin: auto; width: 80%" id="responsable">
                        <div class="input-group" style="margin-left: -40px; border: none">
                            <div class="input-group-addon" style="background-color: #ffffff; width: 35px; border: none"></div>
                            <span class="input-group-addon" style="width: 123px"><label>Responsable</label></span>
                            <select id="historia" class="form-control" style="width: 200px" name="responsable">
                                <?php
                                $consrol = mysql_query("select Codrol,Descripcion from regact_000004");
                                while($datorol = mysql_fetch_array($consrol))
                                {
                                    echo "<option value='".$datorol['Descripcion']."'>".$datorol['Descripcion']."</option>";
                                    $responsable=$datorol['Codrol'];
                                }
                                ?>
                            </select>
                            <!--
                            <input id="historia" type="text" class="form-control" style="width: 200px" name="responsable" value="">
                            -->
                        </div>
                        <div class="input-group-addon" style="background-color: #ffffff; border: none">
                            <input type="submit" class="btn btn-info btn-sm" id="btnBuscar" name="btnBuscar" value="> > >">
                        </div>
                    </div>
                </form>
                <?php

                ?>

                <!--
                MUESTRA TODOS LOS CASOS CORRESPONDIENTES AL ROL DEL USUARIO LOGUEADO EN MATRIX
                SI SE PRESIONA EL BOTON BUSCAR SE MUESTRAN LOS CASOS CUYO TITULO COINCIDA CON EL TEXTO DIGITADO
                -->
                <?php
                if($descUser != null)
                {
                    ?>
                    <div class="container" style="width: 500px; margin-left: 20px; margin-top: 5px">
                        <?php
                        $parametro = $_POST['selparam']; //parametro seleccionado: 'por dia' o 'por palabra clave'
                        if($parametro == ''){$parametro = $_GET['parametro'];}
                        $dia = $_POST['dia']; //lo que viene del campo dia
                        if($dia == ''){$dia = $_GET['dia'];}

                        $caso = $_POST['buscar']; //lo que manda el campo buscar (palabra clave)
                        if($caso == null){$cas = $casoBuscado;}
                        if($caso == null){$caso = $_GET['caso'];}

                        $responsable = $_POST['responsable']; //lo que manda el campo buscar (responsable)
                        if($responsable == ''){$responsable = $_GET['responsable'];}

                        $consrol = mysql_query("select Codrol from regact_000004 WHERE Descripcion = '$responsable'");
                        $datorol = mysql_fetch_array($consrol);
                        $respon=$datorol['Codrol'];

                        //if($parametro == null){$parametro = '0';}
                        if($dia == null){$dia = '1';}
                        ?>
                        <div class="form-horizontal; position: absolute" align="center">
                            <table align="center">
                                <tr>
                                    <td>
                                        <?php
                                        if($dia != '' and $parametro == '0'){?><label>Dia Seleccionado : <?php echo $dia ?></label> <?php }
                                        if($caso != '' and $parametro == '1'){?><label>Palabra Clave : <?php echo $caso ?></label> <?php }
                                        if($responsable != '' and $parametro == '2'){?><label>Responsable de la Actividad : <?php echo $responsable ?></label> <?php }
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="row">
                        <?php
                        if($parametro != null)
                        //if($dia != null or $caso != null or $responsable != null)
                        {
                            ?>
                            <div class="panel panel-primary filterable">
                                <table class="table">
                                    <thead>
                                    <tr class="filters">
                                        <th><input type="text" class="form-control" placeholder="Actividades" disabled></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if ($parametro == '0') //parametro seleccionado = 'por dia'
                                    {
                                        $query3 = mysql_query("SELECT * FROM regact_000001 WHERE dia = '$dia' AND Estado = 'on' ORDER BY Titulo ASC ");

                                        while ($dato3 = mysql_fetch_array($query3))
                                        {
                                            $idregistroF = $dato3['id'];
                                            $casoF = $dato3['Caso'];
                                            $tituloCasoF = $dato3['Titulo'];
                                            ?>
                                            <form name="2" method="post" action="regact01.php">
                                                <input type="hidden" id="casoB" name="casoB" value="<?php echo $casoF ?>">
                                                <tr>
                                                    <td>
                                                        <label id="lblTitulo"><a href="#" onclick="document.location='regact01.php?idCaso=<?php echo $idregistroF ?>&dia=<?php echo $dia ?>&parametro=<?php echo $parametro ?>'"><?php echo $tituloCasoF; ?></a></label>
                                                        <input type="hidden" name="idCaso" id="idCaso" value="<?php echo $idregistroF ?>">
                                                        <input type="hidden" name="dia" value="<?php echo $dia ?>">
                                                        <input type="hidden" name="buscar" value="<?php echo $caso ?>">
                                                        <input type="hidden" name="responsable" value="<?php echo $responsable ?>">
                                                        <input type="hidden" name="selparam" value="<?php echo $parametro ?>">
                                                        <input type="image" id="idFile" name="idFile" src="/matrix/images/medical/nomina/imageEFR.png" width="15" height="15" onclick="borrarFile(idCaso)" align="right" title="Eliminar Actividad">
                                                    </td>
                                                </tr>
                                            </form>
                                            <?php
                                        }
                                    }
                                    elseif ($parametro == '1') //parametro seleccionado = 'por palabra clave'
                                    {
                                        $query3 = mysql_query("SELECT * FROM regact_000001 WHERE Titulo LIKE '%$caso%' AND Estado = 'on' ORDER BY Titulo ASC ");

                                        while ($dato3 = mysql_fetch_array($query3))
                                        {
                                            $idregistroF = $dato3['id'];
                                            $tituloCasoF = $dato3['Titulo'];
                                            ?>
                                            <form name="2" method="post" action="regact01.php">
                                                <tr>
                                                    <td>
                                                        <label id="lblTitulo"><a href="#" onclick="document.location='regact01.php?idCaso=<?php echo $idregistroF ?>&parametro=<?php echo $parametro ?>&caso=<?php echo $caso ?>'"><?php echo $tituloCasoF; ?></a></label>
                                                        <input type="hidden" name="idCaso" id="idCaso" value="<?php echo $idregistroF ?>">
                                                        <input type="hidden" name="buscar" value="<?php echo $caso ?>">
                                                        <input type="hidden" name="responsable" value="<?php echo $responsable ?>">
                                                        <input type="hidden" name="selparam" value="<?php echo $parametro ?>">
                                                        <input type="image" id="idFile" name="idFile" src="/matrix/images/medical/nomina/imageEFR.png" width="15" height="15" onclick="borrarFile(idCaso)" align="right" title="Eliminar Actividad">
                                                    </td>
                                                </tr>
                                            </form>
                                            <?php
                                        }
                                    }
                                    elseif ($parametro == '2') //parametro seleccionado = 'por responsable de actividad'
                                    {
                                        $query3 = mysql_query("SELECT * FROM regact_000001 WHERE Rol = '$respon' AND Estado = 'on' ORDER BY Titulo ASC ");

                                        while ($dato3 = mysql_fetch_array($query3))
                                        {
                                            $idregistroF = $dato3['id'];
                                            $tituloCasoF = $dato3['Titulo'];
                                            ?>
                                            <form name="2" method="post" action="regact01.php">
                                                <tr>
                                                    <td>
                                                        <label id="lblTitulo"><a href="#" onclick="document.location='regact01.php?idCaso=<?php echo $idregistroF ?>&parametro=<?php echo $parametro ?>&responsable=<?php echo $responsable ?>'"><?php echo $tituloCasoF; ?></a></label>
                                                        <input type="hidden" name="idCaso" id="idCaso" value="<?php echo $idregistroF ?>">
                                                        <input type="hidden" name="responsable" value="<?php echo $responsable ?>">
                                                        <input type="hidden" name="buscar" value="<?php echo $caso ?>">
                                                        <input type="hidden" name="selparam" value="<?php echo $parametro ?>">
                                                        <input type="image" id="idFile" name="idFile" src="/matrix/images/medical/nomina/imageEFR.png" width="15" height="15" onclick="borrarFile(idCaso)" align="right" title="Eliminar Actividad">
                                                    </td>
                                                </tr>
                                            </form>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                        }
                        ?>
                        </div>
                    </div>

                    <?php
                    if($parametro != null)
                    //if($dia != null or $caso != null or $responsable != null)
                    {
                        ?>
                        <!-- PANEL DE DESCRIPCION: -->
                        <div class="container" style="width: 550px; margin-left: 550px; position: absolute; top: 210px">
                            <?php
                            $idregistroFinded = $_GET['idCaso'];
                            if($idregistroFinded == null)
                            {
                            ?>
                            <form id="registro" name="registro" class="form-horizontal" role="form" method="post" action="regact04.php"> <!-- guardar -->
                            <?php
                            }
                            else
                            {
                            ?>
                            <form id="loginform" name="registro" class="form-horizontal" role="form" method="post" action="regact07.php"> <!-- actualizar -->
                            <?php
                            }
                            $idCaso = $_GET['idCaso'];
                            if($idCaso == ''){$idCaso = $idCasoviene;}
                            $cas = $_GET['casoB'];
                            ?>
                            <div class="row">
                                <div class="panel panel-primary filterable">
                                    <table class="table">
                                        <thead>
                                        <tr class="filters">
                                            <th><input type="text" class="form-control" placeholder="Descripcion" disabled></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $query2 = mysql_query("select * from regact_000001 WHERE id = '$idCaso'");
                                        while($dato2 = mysql_fetch_array($query2))
                                        {
                                            $idCasoFinal = $dato2['id'];
                                            $tituloCasoFinal = $dato2['Titulo'];
                                            $casoFinal = $dato2['Caso'];
                                            $diaFinal = $dato2['Dia'];
                                            $responsable = $dato2['Rol'];

                                            $queryRol = mysql_query("select Codrol,Descripcion from regact_000004 WHERE Codrol = '$responsable'");
                                            while($datoRol = mysql_fetch_array($queryRol))
                                            {
                                                $codRol = $datoRol['Codrol'];
                                                $descResponsable = $datoRol['Descripcion'];
                                            }
                                            ?>
                                            <input type="hidden" name="id_Registro" id="id_Registro" value="<?php echo $idCasoFinal ?>">
                                            <input type="hidden" name="casoB" value="<?php echo $cas ?>">
                                            <tr>
                                                <td>
                                                    <input type="text" class="form-control" style="border: none; font-weight: bold" id="titulo" name="titulo" value="<?php echo trim($tituloCasoFinal) ?>">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <textarea id="login-username" class="form-control" cols="30" rows="12" name="descripcion"><?php echo $casoFinal; ?></textarea>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="input-group" style="border: none">
                                                        <span class="input-group-addon" style="width: 100px"><label>Dia de la actividad</label></span>
                                                        <select class="form-control" style="width: 150px" name="dia_Registro">
                                                            <option>1</option>
                                                            <option>2</option>
                                                            <option>3</option>
                                                            <option>4</option>
                                                            <option>5</option>
                                                            <option>6</option>
                                                            <option>7</option>
                                                            <option>8</option>
                                                            <option>9</option>
                                                            <option>10</option>
                                                            <option>11</option>
                                                            <option>12</option>
                                                            <option>13</option>
                                                            <option>14</option>
                                                            <option>15</option>
                                                            <option>16</option>
                                                            <option>17</option>
                                                            <option>18</option>
                                                            <option>19</option>
                                                            <option>20</option>
                                                            <option>21</option>
                                                            <option>22</option>
                                                            <option>23</option>
                                                            <option>24</option>
                                                            <option>25</option>
                                                            <option>26</option>
                                                            <option>27</option>
                                                            <option>28</option>
                                                            <option>29</option>
                                                            <option>30</option>
                                                            <option>31</option>
                                                            <option selected><?php echo $diaFinal ?></option>
                                                        </select>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <div class="input-group" style="border: none">
                                                        <span class="input-group-addon" style="width: 100px"><label>Responsable de la actividad</label></span>
                                                        <select class="form-control" style="width: 150px" id="responsable" name="responsable" onchange="copiaValor('responsable','responsableRol')">
                                                            <?php
                                                            $consrol = mysql_query("select Codrol,Descripcion from regact_000004");
                                                            while($datorol = mysql_fetch_array($consrol))
                                                            {
                                                                echo "<option value='".$datorol['Descripcion']."'>".$datorol['Descripcion']."</option>";
                                                                $responsable=$datorol['Codrol'];
                                                            }
                                                            ?>
                                                            <option selected="selected"><?php echo $descResponsable ?></option>
                                                        </select>
                                                    </div>
                                                    <input type="hidden" name="rol" value="<?php echo $rolUser ?>">
                                                    <input type="hidden" name="seguridad" value="<?php echo $wuse ?>">
                                                    <!--
                                                    <input type="text" id="responsableRol" name="responsableRol" value="<?php// echo $descResponsable ?>">
                                                    -->
                                                    <input type="hidden" name="buscar" value="<?php echo $caso ?>">
                                                    <input type="hidden" name="selparam" value="<?php echo $parametro ?>">
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php
                            if($idCaso != null)
                            {
                                ?>
                                <div style="margin-top:10px" class="form-group" align="center">
                                    <div class="col-sm-12 controls">
                                        <input type="submit" class="btn btn-success" value="ACTUALIZAR">
                                        <br><br>
                                        <a href="regact04.php"><img src="/matrix/images/medical/regact/regact_nuevo.png" width="30" height="30" title="Registrar Nueva Actividad"></a>
                                    </div>
                                </div>
                                <?php
                            }
                            else
                            {
                                ?>
                                <div style="margin-top:20px" class="form-group" align="center">
                                    <div class="col-sm-12 controls">
                                        <a href="regact04.php"><img src="/matrix/images/medical/regact/regact_nuevo.png" width="30" height="30" title="Registrar Nueva Actividad"></a>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                            </form>

                            <!-- LISTAR ARCHIVOS ALMACENADOS -->
                            <?php
                            $qry = "SELECT id, Nombre_archivo, Contenido, Tipo FROM regact_000002 WHERE Id_registro = '$idCasoFinal' AND estado = 'on'";
                            $res = mysql_query($qry);
                            //$idArchivo = $res['id'];
                            ?>
                            <input type='hidden' name='idArchivo' id='idArchivo' value='<?php $res['id'] ?>'>
                            <?php

                            if( $idCasoFinal != null)
                            {
                                ?>
                                <div class="container" style="width: 530px">
                                    <div class="row">
                                        <div class="panel panel-primary filterable">
                                            <table class="table">
                                                <thead>
                                                <tr class="filters">
                                                    <th>Archivos Relacionados:</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                while ($fila = mysql_fetch_array($res))
                                                {
                                                    ?>

                                                    <tr>
                                                        <td>
                                                            <form>
                                                                <a href="regact08.php?id=<?php echo $fila['id'] ?>" target="_blank"><?php echo $fila['Nombre_archivo'] ?></a>
                                                                <?php $idArchivo = $fila['id'];?>
                                                                <input type="hidden" name="idA" id="idA" value="<?php echo $idArchivo ?>">
                                                                <input type="image" id="idArchivo" name="idArchivo" src="/matrix/images/medical/nomina/imageEFR.png" width="20" height="20" onclick="borrarArchivo(idA,id_Registro)" align="right" title="Eliminar Archivo">
                                                            </form>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- ALMACENAR ARCHIVOS -->
                                    <form enctype="multipart/form-data" action="regact06.php" method="post">
                                        <input type="hidden" name="id_Registro" value="<?php echo $idCasoFinal; ?>">
                                        <br>
                                        <table align="center">
                                            <tr>
                                                <td colspan="2"></td>
                                            </tr>
                                            <tr>
                                                <td><input type="file" name="files" class="texto1"></td>
                                                <td><input type="submit" value="Guardar"></td>
                                            </tr>
                                        </table>
                                    </form>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('.filterable .btn-filter').click(function(){
            var $panel = $(this).parents('.filterable'),
                $filters = $panel.find('.filters input'),
                $tbody = $panel.find('.table tbody');
            if ($filters.prop('disabled') == true) {
                $filters.prop('disabled', false);
                $filters.first().focus();
            } else {
                $filters.val('').prop('disabled', true);
                $tbody.find('.no-result').remove();
                $tbody.find('tr').show();
            }
        });

        $('.filterable .filters input').keyup(function(e){
            /* Ignore tab key */
            var code = e.keyCode || e.which;
            if (code == '9') return;
            /* Useful DOM data and selectors */
            var $input = $(this),
                inputContent = $input.val().toLowerCase(),
                $panel = $input.parents('.filterable'),
                column = $panel.find('.filters th').index($input.parents('th')),
                $table = $panel.find('.table'),
                $rows = $table.find('tbody tr');
            /* Dirtiest filter function ever ;) */
            var $filteredRows = $rows.filter(function(){
                var value = $(this).find('td').eq(column).text().toLowerCase();
                return value.indexOf(inputContent) === -1;
            });
            /* Clean previous no-result if exist */
            $table.find('tbody .no-result').remove();
            /* Show all rows, hide filtered ones (never do that outside of a demo ! xD) */
            $rows.show();
            $filteredRows.hide();
            /* Prepend no-result row if all rows are filtered */
            if ($filteredRows.length === $rows.length) {
                $table.find('tbody').prepend($('<tr class="no-result text-center"><td colspan="'+ $table.find('.filters th').length +'">No se encontraron resultados</td></tr>'));
            }
        });
    });
</script>
</body>