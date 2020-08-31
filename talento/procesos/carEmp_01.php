<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GESTION DE TALENTO HUMANO - CARACTERIZACION</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">
    <link href="carEmp_style.css" rel="stylesheet">
    <script src="carEmp_Js.js"></script>
    <script>
        function runModal()
        {
            $('#myModal').on('shown.bs.modal', function () {
                $('#myInput').trigger('focus')
            })
        }

        function chekSym()
        {
            str = document.getElementById('Idedir').value;
            res = str.replace("#", "N");
            document.getElementById('Idedir').value = res;
        }
    </script>
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
                alert("Tu navegador NO es Mozilla Firefox, esta aplicacion podria no funcionar correctamente, por favor, cambia de navegador")
                window.close();
            }
        }
    </script> <!-- VALIDAR BROWSER -->
    <?php
    include("conex.php");
    include("root/comun.php");

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
        mysql_select_db("matrix");
        $conex = obtenerConexionBD("matrix");
    }
    include('carEmp_Functions.php'); //publicacion local

    //OBTENER LA PESTAÑA QUE EL USUARIO TENGA ABIERTA, DESPUES DE HACER POST:
    $pestana = returnTab($conex,$wuse);

    //OBTENER LA USUARIO DE TALHUMA:
    $userTal = datUserxEmp($wuse,$conex,20);
    //if($userTal == null){$userTal = $wuse;}

    //CONSULTAR TABLA DEL USUARIO:
    //$tbInfoEmpleado = tablaUser($wuse,$conex);
    $empresaUser = getEmpresaUser($wuse,$conex);
    $useryEmpresa = $wuse.'-'.$empresaUser;
    //if($tbInfoEmpleado == 'thhonmed'){$tbInfoEmpleado = 'talhuma';}
    //if($tbInfoEmpleado == 'thsoe'){$tbInfoEmpleado = 'talhuma';}
    $tbInfoEmpleado = 'talhuma';

    //CEDULA EMPLEADO:
    $existCedula = datUserxEmp($wuse,$conex,24);

    //AÑO ACTUAL:
    $anio_actual = date('Y');
    ?>
</head>

<body onload="validar_Browser()">
<!-- Button trigger modal -->
<button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" style="position:fixed;right:10px;margin-top: 0">
    Ver Política<br>de Privacidad
</button>
<!----------------- MODAL POLITICA DE PRIVACIDAD -------------->
<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">POLITICA DE PRIVACIDAD</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                El Diagnóstico de Bienestar nos permitirá conocer tus características para diseñar nuevas oportunidades que, desde la posibilidad,
                incentiven el desarrollo y la calidad de vida de todos.<br><br>
                Los datos suministrados en la encuesta tendrán el tratamiento necesario, seguro y confidencial por parte de la gerencia de Talento
                humano con la finalidad de crear, gestionar y ejecutar mejores planes de bienestar.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Entendido</button>
            </div>
        </div>
    </div>
</div>
<!-------------------------------------->

<div class="container main">
    <div class="panel panel-info contenido">
        <div class="panel-heading encabezado">
            <div class="panel-title titulo1">GESTION DE TALENTO HUMANO</div>
        </div>
    </div>
    <div class="panel panel-info contenido">
        <div align="center" class="panel panel-info" style="margin-bottom: 10px; background-color: #305496">
            <table>
                <tr>
                    <td>
                        <div class="input-group titulo" style="background-color: transparent; text-align: left; color: white">
                            <span class="input-group-addon input-sm" style="border: none; background-color: #305496"><label><?php echo datosUsuario($wuse,$conex,1) ?></label></span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <?php
        if($existCedula == null)
        {
            ?>
            <!-- DATOS MINIMOS PREVIOS -->
            <form>
                <h4 style="text-align: center">DATOS MINIMOS PREVIOS</h4>
                <div id="idGeneral" class="input-group">
                    <table align="center" border="0" style="width: 100%">
                        <tr>
                            <!-- DATOS GENERALES -->
                            <td>
                                <div class="input-group" style="margin-left: 10px">
                                    <span class="input-group-addon input-sm spanIdGen"><label for="IdefncP">Fecha de Nacimiento:</label></span>
                                    <input type="date" id="IdefncP" name="IdefncP" class="form-control form-sm inputIdGen" style="width: 204px" required>
                                </div>
                            </td>
                            <td>
                                <div class="input-group" style="margin-left: 10px">
                                    <span class="input-group-addon input-sm spanIdGen"><label for="IdegenP">Genero:</label></span>
                                    <select id="IdegenP" name="IdegenP" class="form-control form-sm inputIdGen" style="width: 203px" required>
                                        <option value="M">Masculino</option>
                                        <option value="F">Femenino</option>
                                        <option selected disabled>Seleccione...</option>
                                    </select>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="input-group" style="margin-left: 10px">
                                    <span class="input-group-addon input-sm spanIdGen"><label for="IdecedP">Numero de Cedula:</label></span>
                                    <input type="text" id="IdecedP" name="IdecedP" class="form-control form-sm inputIdGen" required>
                                </div>
                            </td>
                            <td>
                                <div class="input-group" style="margin-left: 10px">

                                </div>
                            </td>
                        </tr>
                    </table>
                    <br>
                    <input type="hidden" id="funcionSaveBasic" name="funcionSaveBasic" value="saveBasic">
                    <input type="hidden" id="tablaEmp" name="tablaEmp" value="<?php echo $tbInfoEmpleado ?>">
                    <input type="hidden" id="userMtx" name="userMtx" value="<?php echo $useryEmpresa ?>">
                    <div align="center">
                        <button type="submit" class="btn btn-default btn-lg"
                                onclick="saveBasic(funcionSaveBasic,tablaEmp,userMtx,IdefncP,IdegenP,IdecedP)">
                            <span class="glyphicon glyphicon-floppy-save" aria-hidden="true" style="width: 50px;"></span> Guardar
                        </button>
                    </div>
                </div>
            </form>
            <?php
        }
        else
        {
            ?>
                <form>
                    <h4 style="text-align: center">I. IDENTIFICACION GENERAL</h4>
                    <div id="idGeneral" class="input-group">
                        <table align="center" border="0" style="width: 100%">
                            <tr>
                                <!-- DATOS GENERALES -->
                                <td>
                                    <div class="input-group" style="margin-left: 10px">
                                        <span class="input-group-addon input-sm spanIdGen"><label for="Idefnc">Fecha de Nacimiento:</label></span>
                                        <input type="date" id="Idefnc" name="Idefnc" class="form-control form-sm inputIdGen" value="<?php datUserxEmp($wuse,$conex,1) ?>" style="width: 204px" required>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group" style="margin-left: 10px">
                                        <span class="input-group-addon input-sm spanIdGen"><label for="Idegen">Genero:</label></span>
                                        <?php
                                        $Idegen = datUserxEmp($wuse,$conex,23); if($Idegen == 'M'){$Idegen = 'Masculino';}  else{$Idegen = 'Femenino';}
                                        if($Idegen != null)
                                        {
                                            ?>
                                            <input type="text" id="Idegen" name="Idegen" class="form-control form-sm inputIdGen" style="width: 203px" value="<?php echo $Idegen ?>" readonly>
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <select id="Idegen" name="Idegen" class="form-control form-sm inputIdGen" style="width: 203px" required>
                                                <option>Masculino</option>
                                                <option>Femenino</option>
                                                <option selected disabled>Seleccione...</option>
                                            </select>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </td>
                                <td rowspan="6">
                                    <!-- FOTO -->
                                    <div id="divFoto" style="width: 180px; margin-top: -75px; margin-right: 10px; border: none" >
                                        <img src="http://mtx.lasamericas.com.co/matrix/images/medical/tal_huma/<?php datUserxEmp($wuse,$conex,3) ?>.jpg"
                                             style="width:200px; height:200px; border-radius:110px;margin-top: 50px; margin-left: -30px">
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="input-group" style="margin-left: 10px">
                                        <span class="input-group-addon input-sm spanIdGen"><label for="Ideced">Numero de Cedula:</label></span>
                                        <input type="text" id="Ideced" name="Ideced" class="form-control form-sm inputIdGen" value="<?php datUserxEmp($wuse,$conex,3) ?>" readonly>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group" style="margin-left: 10px">
                                        <span class="input-group-addon input-sm spanIdGen"><label for="Ideuse">Codigo de Nomina:</label></span>
                                        <input type="text" id="Ideuse" name="Ideuse" class="form-control form-sm" style="width: 203px" value="<?php datUserxEmp($wuse,$conex,4) ?>" readonly>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="input-group" style="margin-left: 10px">
                                        <span class="input-group-addon input-sm spanIdGen"><label for="Idepas">Tiene Pasaporte:</label></span>
                                        <select id="Idepas" name="Idepas" class="form-control form-sm" style="width: 203px" required>
                                            <option>SI</option> <option>NO</option>
                                            <?php
                                            $Idepas = datUserxEmp($wuse,$conex,5);
                                            if($Idepas != null){?><option selected><?php echo $Idepas ?></option><?php }
                                            else{?><option selected disabled>Seleccione...</option><?php }
                                            ?>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group" style="margin-left: 10px">
                                        <span class="input-group-addon input-sm spanIdGen"><label for="Idevis">Tiene Visa:</label></span>
                                        <select id="Idevis" name="Idevis" class="form-control form-sm" style="width: 203px" required>
                                            <option>SI</option> <option>NO</option>
                                            <?php
                                            $Idevis = datUserxEmp($wuse,$conex,6);
                                            if($Idevis != null){?><option selected><?php echo $Idevis ?></option><?php }
                                            else{?><option selected disabled>Seleccione...</option><?php }
                                            ?>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="input-group" style="margin-left: 10px">
                                        <span class="input-group-addon input-sm spanIdGen"><label for="estcivUser">Estado civil:</label></span>
                                        <select id="estcivUser" name="estcivUser" class="form-control form-sm" style="width: 203px" required>
                                            <option>Casado(a)</option>  <option>Divorciado(a)</option>  <option>Separado(a)</option>
                                            <option>Soltero(a)</option> <option>Union Libre</option>    <option>Viudo(a)</option>
                                            <?php
                                            $Ideciv = datUserxEmp($wuse,$conex,9);
                                            if($Ideciv == '01'){$Ideciv = 'Soltero(a)';}    if($Ideciv == '02'){$Ideciv = 'Casado(a)';}
                                            if($Ideciv == '03'){$Ideciv = 'Union libre';}   if($Ideciv == '04'){$Ideciv = 'Separado(a)';}
                                            if($Ideciv == '05'){$Ideciv = 'Divorciado(a)';} if($Ideciv == '06'){$Ideciv = 'Viudo(a)';}
                                            if($Ideciv != null){?><option selected><?php echo $Ideciv ?></option><?php }
                                            else{?><option selected disabled>Seleccione...</option><?php }
                                            ?>
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group" style="margin-left: 10px">
                                        <span class="input-group-addon input-sm spanIdGen"><label for="estUser">Estrato:</label></span>
                                        <input type="text" id="estUser" name="estUser" class="form-control form-sm" style="width: 200px" value="<?php datUserxEmp($wuse,$conex,12) ?>" required>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="input-group" style="margin-left: 10px">
                                        <span class="input-group-addon input-sm spanIdGen"><label for="lunaUser">Lugar de Nacimiento:</label></span>
                                        <input type="text" id="lunaUser" name="lunaUser" class="form-control form-sm inputIdGen" value="<?php datUserxEmp($wuse,$conex,11) ?>" required>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group" style="margin-left: 10px">
                                        <span class="input-group-addon input-sm spanIdGen"><label for="Idedir">Direccion Vivienda:</label></span>
                                        <input type="text" id="Idedir" name="Idedir" class="form-control form-sm inputIdGen" style="width: 250px"
                                               value="<?php datUserxEmp($wuse,$conex,10) ?>" onblur="chekSym()" required>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="input-group" style="margin-left: 10px">
                                        <span class="input-group-addon input-sm"><label for="munUser">Municipio de Residencia:</label></span>
                                        <?php
                                        $codMuni = datUserxEmp($wuse,$conex,13);    //OBTENER CODIGO MUNICIPIO
                                        $nombreMuni = datosMunicipio($codMuni,$conex);  //OBTENER NOMBRE MUNICIPIO
                                        ?>
                                        <input type="text" id="munUser" name="munUser" class="form-control form-sm inputIdGen" value="<?php echo $nombreMuni ?>" list="muni" required>
                                        <datalist id="muni">
                                            <?php
                                            $QueryMuni = "select Nombre from root_000006 ORDER by Nombre ASC ";
                                            $commMuni = mysql_query($QueryMuni, $conex) or die (mysql_errno()." - en el query: ".$QueryMuni." - ".mysql_error());
                                            while($datoMuni = mysql_fetch_assoc($commMuni))
                                            {
                                                $descMuni = $datoMuni['Nombre'];
                                                ?><option value="<?php echo $descMuni ?>"></option><?php
                                            }
                                            ?>
                                        </datalist>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group" style="margin-left: 10px">
                                        <span class="input-group-addon input-sm spanIdGen"><label for="barUser">Barrio:</label></span>
                                        <?php
                                        $codBarrio = datUserxEmp($wuse,$conex,14);  //OBTENER CODIGO DEL BARRIO
                                        $nomBarrio = datosBarrio($codBarrio,$codMuni,$conex); //OBTENER NOMBRE DEL BARRIO SEGUN SU CODIGO
                                        ?>
                                        <input type="text" id="barUser" name="barUser" class="form-control form-sm inputIdGen" value="<?php echo $nomBarrio ?>" list="barr" required>
                                        <datalist id="barr">
                                            <?php
                                            $queryBarr = "select Bardes from root_000034 WHERE Barmun = '$codMuni' ORDER BY Bardes ASC ";
                                            $commBarr = mysql_query($queryBarr, $conex) or die (mysql_errno()." - en el query: ".$queryBarr." - ".mysql_error());
                                            while($datoBarr = mysql_fetch_assoc($commBarr))
                                            {
                                                $descBarr = $datoBarr['Bardes'];
                                                ?><option value="<?php echo $descBarr ?>"></option><?php
                                            }
                                            ?>
                                        </datalist>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="input-group" style="margin-left: 10px">
                                        <span class="input-group-addon input-sm spanIdGen"><label for="telUser">Numero Telefonico:</label></span>
                                        <input type="text" id="telUser" name="telUser" class="form-control form-sm inputIdGen" value="<?php datUserxEmp($wuse,$conex,15) ?>" required>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group" style="margin-left: 10px">
                                        <span class="input-group-addon input-sm spanIdGen"><label for="celUser">Celular:</label></span>
                                        <input type="text" id="celUser" name="celUser" class="form-control form-sm inputIdGen" value="<?php datUserxEmp($wuse,$conex,16) ?>" required>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="input-group" style="margin-left: 10px">
                                        <span class="input-group-addon input-sm spanIdGen"><label for="corUser">Correo Electronico:</label></span>
                                        <input type="email" id="corUser" name="corUser" class="form-control form-sm inputIdGen" value="<?php datUserxEmp($wuse,$conex,17) ?>" required>
                                    </div>
                                </td>
                                <td>
                                    <?php $tisanUser = datUserxEmp($wuse,$conex,18) ?>
                                    <div class="input-group" style="margin-left: 10px">
                                        <span class="input-group-addon input-sm spanIdGen"><label for="tisanUser">Tipo Sangre:</label></span>
                                        <select id="tisanUser" name="tisanUser" class="form-control form-sm inputIdGen" required>
                                            <option>O negativo</option>
                                            <option>O positivo</option>
                                            <option>A negativo</option>
                                            <option>A positivo</option>
                                            <option>B negativo</option>
                                            <option>B positivo</option>
                                            <option>AB negativo</option>
                                            <option>AB positivo</option>
                                            <?php
                                            if($tisanUser == null){?><option selected disabled>Seleccione...</option> <?php }
                                            else{?><option selected><?php echo $tisanUser ?></option> <?php }
                                            ?>
                                        </select>
                                        <!--<input type="text" id="tisanUser" name="tisanUser" class="form-control form-sm inputIdGen" value="<?php datUserxEmp($wuse,$conex,18) ?>" required>-->
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="input-group" style="margin-left: 10px">
                                        <span class="input-group-addon input-sm spanIdGen"><label for="extUser">Extension:</label></span>
                                        <input type="text" id="extUser" name="extUser" class="form-control form-sm inputIdGen" value="<?php datUserxEmp($wuse,$conex,19) ?>" required>
                                    </div>
                                </td>
                                <td>
                                    <div class="input-group" style="margin-left: 10px">
                                        <span class="input-group-addon input-sm spanIdGen"><label for="contEmer">Contacto de Emergencia:</label></span>
                                        <input type="text" id="contEmer" name="contEmer" class="form-control form-sm inputIdGen" value="<?php datUserxEmp($wuse,$conex,25) ?>" required>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $telEmer = datUserxEmp($wuse,$conex,27)
                                    ?>
                                    <div class="input-group" style="margin-left: 5px">
                                        <input type="text" id="telEmer" name="telEmer" class="form-control form-sm"
                                               <?php if($telEmer != null){?>value="<?php echo $telEmer ?>" <?php } else{?>placeholder="Telefono Emergencia"<?php } ?>
                                               title="Telefono de Emergencia" required>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <div class="input-group" style="margin-left: 10px">
                                        <span class="input-group-addon input-sm spanIdGen">
                                            <label for="Ideraz">Se reconoce como parte de uno de los siguientes grupos étnicos y/o raza ?</label>
                                        </span>
                                        <select id="Ideraz" name="Ideraz" class="form-control form-sm" style="width: 604px" required>
                                            <option>Blanco</option>
                                            <option>Mestizo</option>
                                            <option>Pueblo Indígena</option>
                                            <option>Población Negra o afrocolombiana</option>
                                            <option>Poblacion Raizal</option>
                                            <option>Pueblo rom (gitano)</option>
                                            <option>Pueblo palenquero</option>
                                            <option>Ninguno de los anteriores</option>
                                            <option>Otro</option>
                                            <?php
                                            $Ideraz = datUserxEmp($wuse,$conex,26);
                                            if($Ideraz != null){?><option selected><?php echo $Ideraz ?></option><?php }
                                            else{?><option selected disabled>Seleccione...</option><?php }
                                            ?>
                                        </select>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                </form>

                <div class="panel-group" id="accordion">

                <!------------------------ EDUCACION: ------------------------->
                <div class="panel panel-default">
                    <div class="panel-heading">
                    <h4 class="panel-title tabsGen">
                        <a data-toggle="collapse" data-parent="#accordion" href="#collapse2">EDUCACION</a>
                    </h4>
                </div>
                    <?php
                    if($pestana == '2'){?><div id="collapse2" class="panel-collapse collapse in"><?php }
                    else{?><div id="collapse2" class="panel-collapse collapse"><?php } ?>
                        <div class="panel-body">

                            <!-- //////////////////// NIVEL EDUCATIVO: ////////////////////////// -->

                            <h4 class="labelTitulo">Nivel educativo (Si usted tiene una educación superior, relacione todas las que tenga)</h4>
                            <?php
                            //QUERY PARA SELECCIONAR LOS REGISTROS DE EDUCACION DEL USUARIO:
                            $queryEduUser = "select * from".' '."$tbInfoEmpleado"."_000014 WHERE Eduuse = '$userTal' AND Eduest = 'on'";
                            $commitQryEduUser = mysql_query($queryEduUser, $conex) or die (mysql_errno()." - en el query: ".$queryEduUser." - ".mysql_error());
                            ?>
                            <table class="tblNivE">
                                <thead>
                                <tr>
                                    <td align="center">
                                        <a href="#" id="add" class="btn btn-info btn-xs" title="Actualizar" onclick="location.reload()">
                                            <span class="glyphicon glyphicon-refresh"></span>
                                        </a>
                                    </td>
                                    <td><label>  GRADO ESCOLAR</label></td>               <td><label>TITULO OBTENIDO</label></td>
                                    <td><label>NOMBRE DE LA INSTITUCION</label></td>    <td><label>FECHA</label></td>
                                    <td></td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                while($datoEduUser = mysql_fetch_array($commitQryEduUser))
                                {
                                    $Edugrd = $datoEduUser['Edugrd'];   $gradoEsc = datoEscolar($Edugrd,$conex);
                                    $Edutit = $datoEduUser['Edutit'];   $Eduins = $datoEduUser['Eduins'];
                                    $Eduani = $datoEduUser['Eduani'];   $idEstud = $datoEduUser['id'];
                                    ?>
                                    <tr class="alternar">
                                        <td></td>
                                        <td><label>&ensp;<?php echo $Edugrd ?></label></td>   <td><label><?php echo $Edutit ?></label></td>
                                        <td><label><?php echo $Eduins ?></label></td>           <td><label><?php echo $Eduani ?></label></td>
                                        <td align="right">
                                            <input type="hidden" id="funcion22" name="funcion22" value="modEstudio">
                                            <a href="#" id="add" class="btn btn-info btn-xs" title="Modificar"
                                               onclick="openOps2(funcion22,tablaMtx,userMtx,<?php echo $idEstud ?>,tenVivi,tipVivi,tienTerr,tienLote,estVivi,chAcue,chAlca,chAseo,
                                                   chEner,chInter,chGas,chTele,credito,chBici,chBus,chCamina,chPart,chMetro,chMoto,chOtroT,chTaxi,chContra,otroTrans,chTrae,chComBoca,
                                                   chComOtros,chCasa,chOtrosAl,otroTalmu,hobbie,lugparq,locker,chAudit,chComite,chBrigada,chOtroRol,cualRol,Idefnc,Idegen,Ideced,
                                                   Idepas,Idevis,estcivUser,Idedir,lunaUser,estUser,munUser,barUser,telUser,celUser,corUser,tisanUser,extUser,chCoCon,chGesIn,chCoDoc,
                                                   chGesTe,chCoInv,chHisTe,chCoCom,chInfInt,chCopas,chMedTra,chCrede,chMeCon,chEtiIn,chMoSe,chEtiHo,chSePac,chEvCal,chTransp,chFarTe,
                                                   chViEpi,chGesAm,contEmer,Ideraz,timeDesp,turnEmp,chTorBol,chTorPla,chTorVol,chTorBal,chTorTen,chCamin,chBaile,chYoga,chEnPare,
                                                   chCiclo,chMara,chTarHob,chGruTea,chArtPla,chManual,chGastro,chClaIng,chConPle,chTarPic,chOtrAct,diaActi,horActi,actExtra,otraExtra,
                                                   ranSal,telEmer,chViArr,chViPag,chAlime,chSerPu,chTrpte,chEdPro,chEdHij,chPaCre,chReTli,chVestu,chSalud,chPaCel,chPaTar,chCoTec,
                                                   chCuPer,chOtGas,chDeuSi,chPCohi,chDiEco,chDeMif,chHiEmb,chSeDiv,chViInt,chAdLic,chMuSer,chEnGra,chNiSit,posFam,chAbuNi,chPaMad,
                                                   chVecin,chGuIns,chEmDom,chUnFam,chQuSol,chCuOtr,subViv,ahoViv,montoHa,chInuVi,chContVi,chRiAmVi,chRiEsvi,chRiSaVi,chRiPuVi,chNoFaVi,
                                                   chMeEst,chMeMue,chMeEle,chMePis,chMePar,chMeCol,chMeHum,chMeFac,chMeTec,chMeBan,chMeCoc,chMeAmp,chMeNot,chPfCah,chPfCuc,chPfTac,
                                                   chPfCco,chPfChi,chPfCve,chPfInv,chPfSeg,chPfNin,chMcViv,chMcTec,chMcMue,chMcEle,chMcVeh,chMcSal,chMcCir,chMcTur,chMcLib,chMcGah,
                                                   chMcTac,chMcEdp,chMcEdf,chMcCem,chMcNin,chEpBan,chEpFon,chEpFmu,chEpPad,chEpFam,chEpCal,chEpCaj,chEpEla,chEpNat,chEpOtr,chEpNin,
                                                   chLcViv,chLcVeh,chLcSal,chLcCir,chLcTur,chLcEdf,chLcEdp,chLcCre,chLcMej,chLcCro,chLcLib,chLcTar,chLcNin,chIaInv,chIaBan,chIaNat,
                                                   chIaCac,chIaFem,chIaFmu,chIaFvp,chIaOtr,chIaNin,chNfCap,chNfDes,chNfRel,chNfMan,chNfFin,chNfFor,chNfIdi,chNfInf,chNfFco,chNfOtr,
                                                   chNfNot,chHoCin,chHoDep,chHoVid,chHoVte,chHoNav,chHoIce,chHoIpa,chHoIfi,chHoCex,chHoDes,chHoJar,chHoCon,chHoPin,chHoEsc,chHoFot,
                                                   chHoVmu,chHoVbi,chHoEsp,chHoDan,chHoTin,chHoCoc,chHoMan,chHoOtr,chHoNin,chQpHij,chQpAmi,chQpMas,chQpSol,chQpFam,chQpAmo,chQpPar,
                                                   chQpCom,chQpOtr,chHhCin,chHhDep,chHhVid,chHhVte,chHhNav,chHhIce,chHhIpa,chHhIfi,chHhCex,chHhDes,chHhJar,chHhCon,chHhPin,chHhEsc,
                                                   chHhFot,chHhVmu,chHhVbi,chHhEsp,chHhDan,chHhTin,chHhCoc,chHhMan,chHhOtr,chHhNin,chBuFdi,chBuNcd,chBuDap,chBuFmo,chBuNdt,chBuOtr,
                                                   chBuNin)">
                                                <span class="glyphicon glyphicon-pencil"></span>
                                            </a>

                                            <input type="hidden" id="funcion2" name="funcion2" value="supEstudio">
                                            <a href="#" id="less" class="btn btn-info btn-xs" title="Eliminar"
                                               onclick="openOps2(funcion2,tablaMtx,userMtx,<?php echo $idEstud ?>,tenVivi,tipVivi,tienTerr,tienLote,estVivi,chAcue,chAlca,chAseo,chEner,
                                                   chInter,chGas,chTele,credito,chBici,chBus,chCamina,chPart,chMetro,chMoto,chOtroT,chTaxi,chContra,otroTrans,chTrae,chComBoca,chComOtros,
                                                   chCasa,chOtrosAl,otroTalmu,hobbie,lugparq,locker,chAudit,chComite,chBrigada,chOtroRol,cualRol,Idefnc,Idegen,Ideced,Idepas,Idevis,
                                                   estcivUser,Idedir,lunaUser,estUser,munUser,barUser,telUser,celUser,corUser,tisanUser,extUser,chCoCon,chGesIn,chCoDoc,chGesTe,chCoInv,
                                                   chHisTe,chCoCom,chInfInt,chCopas,chMedTra,chCrede,chMeCon,chEtiIn,chMoSe,chEtiHo,chSePac,chEvCal,chTransp,chFarTe,chViEpi,chGesAm,
                                                   contEmer,Ideraz,timeDesp,turnEmp,chTorBol,chTorPla,chTorVol,chTorBal,chTorTen,chCamin,chBaile,chYoga,chEnPare,chCiclo,chMara,chTarHob,
                                                   chGruTea,chArtPla,chManual,chGastro,chClaIng,chConPle,chTarPic,chOtrAct,diaActi,horActi,actExtra,otraExtra,ranSal,telEmer,chViArr,chViPag,
                                                   chAlime,chSerPu,chTrpte,chEdPro,chEdHij,chPaCre,chReTli,chVestu,chSalud,chPaCel,chPaTar,chCoTec,chCuPer,chOtGas,chDeuSi,chPCohi,chDiEco,
                                                   chDeMif,chHiEmb,chSeDiv,chViInt,chAdLic,chMuSer,chEnGra,chNiSit,posFam,chAbuNi,chPaMad,chVecin,chGuIns,chEmDom,chUnFam,chQuSol,chCuOtr,
                                                   subViv,ahoViv,montoHa,chInuVi,chContVi,chRiAmVi,chRiEsvi,chRiSaVi,chRiPuVi,chNoFaVi,chMeEst,chMeMue,chMeEle,chMePis,chMePar,chMeCol,
                                                   chMeHum,chMeFac,chMeTec,chMeBan,chMeCoc,chMeAmp,chMeNot,chPfCah,chPfCuc,chPfTac,chPfCco,chPfChi,chPfCve,chPfInv,chPfSeg,chPfNin,chMcViv,
                                                   chMcTec,chMcMue,chMcEle,chMcVeh,chMcSal,chMcCir,chMcTur,chMcLib,chMcGah,chMcTac,chMcEdp,chMcEdf,chMcCem,chMcNin,chEpBan,chEpFon,chEpFmu,
                                                   chEpPad,chEpFam,chEpCal,chEpCaj,chEpEla,chEpNat,chEpOtr,chEpNin,chLcViv,chLcVeh,chLcSal,chLcCir,chLcTur,chLcEdf,chLcEdp,chLcCre,chLcMej,
                                                   chLcCro,chLcLib,chLcTar,chLcNin,chIaInv,chIaBan,chIaNat,chIaCac,chIaFem,chIaFmu,chIaFvp,chIaOtr,chIaNin,chNfCap,chNfDes,chNfRel,chNfMan,
                                                   chNfFin,chNfFor,chNfIdi,chNfInf,chNfFco,chNfOtr,chNfNot,chHoCin,chHoDep,chHoVid,chHoVte,chHoNav,chHoIce,chHoIpa,chHoIfi,chHoCex,chHoDes,
                                                   chHoJar,chHoCon,chHoPin,chHoEsc,chHoFot,chHoVmu,chHoVbi,chHoEsp,chHoDan,chHoTin,chHoCoc,chHoMan,chHoOtr,chHoNin,chQpHij,chQpAmi,chQpMas,
                                                   chQpSol,chQpFam,chQpAmo,chQpPar,chQpCom,chQpOtr,chHhCin,chHhDep,chHhVid,chHhVte,chHhNav,chHhIce,chHhIpa,chHhIfi,chHhCex,chHhDes,chHhJar,
                                                   chHhCon,chHhPin,chHhEsc,chHhFot,chHhVmu,chHhVbi,chHhEsp,chHhDan,chHhTin,chHhCoc,chHhMan,chHhOtr,chHhNin,chBuFdi,chBuNcd,chBuDap,chBuFmo,
                                                   chBuNdt,chBuOtr,chBuNin)">
                                                <span class="glyphicon glyphicon-minus"></span>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>

                            <div id="divAddEst" align="center">
                                <input type="hidden" id="funcion" name="funcion" value="addEstudio">
                                <input type="hidden" id="tablaMtx" name="tablaMtx" value="<?php echo $tbInfoEmpleado ?>">
                                <input type="hidden" id="userMtx" name="userMtx" value="<?php echo $userTal ?>">
                                <button type="button" class="btn btn-default btn-lg"
                                        onclick="openOps(funcion,tablaMtx,userMtx,tenVivi,tipVivi,tienTerr,tienLote,estVivi,chAcue,chAlca,chAseo,chEner,chInter,chGas,chTele,credito,
                                    chBici,chBus,chCamina,chPart,chMetro,chMoto,chOtroT,chTaxi,chContra,otroTrans,chTrae,chComBoca,chComOtros,chCasa,chOtrosAl,otroTalmu,hobbie,
                                    lugparq,locker,chAudit,chComite,chBrigada,chOtroRol,cualRol,Idefnc,Idegen,Ideced,Idepas,Idevis,estcivUser,Idedir,lunaUser,estUser,munUser,
                                    barUser,telUser,celUser,corUser,tisanUser,extUser,chCoCon,chGesIn,chCoDoc,chGesTe,chCoInv,chHisTe,chCoCom,chInfInt,chCopas,chMedTra,chCrede,
                                    chMeCon,chEtiIn,chMoSe,chEtiHo,chSePac,chEvCal,chTransp,chFarTe,chViEpi,chGesAm,contEmer,Ideraz,timeDesp,turnEmp,chTorBol,chTorPla,chTorVol,
                                    chTorBal,chTorTen,chCamin,chBaile,chYoga,chEnPare,chCiclo,chMara,chTarHob,chGruTea,chArtPla,chManual,chGastro,chClaIng,chConPle,chTarPic,
                                    chOtrAct,diaActi,horActi,actExtra,otraExtra,ranSal,telEmer,chViArr,chViPag,chAlime,chSerPu,chTrpte,chEdPro,chEdHij,chPaCre,chReTli,chVestu,
                                    chSalud,chPaCel,chPaTar,chCoTec,chCuPer,chOtGas,chDeuSi,chPCohi,chDiEco,chDeMif,chHiEmb,chSeDiv,chViInt,chAdLic,chMuSer,chEnGra,chNiSit,
                                    posFam,chAbuNi,chPaMad,chVecin,chGuIns,chEmDom,chUnFam,chQuSol,chCuOtr,subViv,ahoViv,montoHa,chInuVi,chContVi,chRiAmVi,chRiEsvi,chRiSaVi,
                                    chRiPuVi,chNoFaVi,chMeEst,chMeMue,chMeEle,chMePis,chMePar,chMeCol,chMeHum,chMeFac,chMeTec,chMeBan,chMeCoc,chMeAmp,chMeNot,chPfCah,chPfCuc,
                                    chPfTac,chPfCco,chPfChi,chPfCve,chPfInv,chPfSeg,chPfNin,chMcViv,chMcTec,chMcMue,chMcEle,chMcVeh,chMcSal,chMcCir,chMcTur,chMcLib,chMcGah,
                                    chMcTac,chMcEdp,chMcEdf,chMcCem,chMcNin,chEpBan,chEpFon,chEpFmu,chEpPad,chEpFam,chEpCal,chEpCaj,chEpEla,chEpNat,chEpOtr,chEpNin,chLcViv,
                                    chLcVeh,chLcSal,chLcCir,chLcTur,chLcEdf,chLcEdp,chLcCre,chLcMej,chLcCro,chLcLib,chLcTar,chLcNin,chIaInv,chIaBan,chIaNat,chIaCac,chIaFem,
                                    chIaFmu,chIaFvp,chIaOtr,chIaNin,chNfCap,chNfDes,chNfRel,chNfMan,chNfFin,chNfFor,chNfIdi,chNfInf,chNfFco,chNfOtr,chNfNot,chHoCin,chHoDep,
                                    chHoVid,chHoVte,chHoNav,chHoIce,chHoIpa,chHoIfi,chHoCex,chHoDes,chHoJar,chHoCon,chHoPin,chHoEsc,chHoFot,chHoVmu,chHoVbi,chHoEsp,chHoDan,
                                    chHoTin,chHoCoc,chHoMan,chHoOtr,chHoNin,chQpHij,chQpAmi,chQpMas,chQpSol,chQpFam,chQpAmo,chQpPar,chQpCom,chQpOtr,chHhCin,chHhDep,chHhVid,
                                    chHhVte,chHhNav,chHhIce,chHhIpa,chHhIfi,chHhCex,chHhDes,chHhJar,chHhCon,chHhPin,chHhEsc,chHhFot,chHhVmu,chHhVbi,chHhEsp,chHhDan,chHhTin,
                                    chHhCoc,chHhMan,chHhOtr,chHhNin,chBuFdi,chBuNcd,chBuDap,chBuFmo,chBuNdt,chBuOtr,chBuNin)">
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Nuevo Estudio
                                </button>
                            </div>

                            <!-- //////////////////// MANEJO DE OTROS IDIOMAS: ////////////////////////// -->

                            <h4 class="labelTitulo">Manejo de otros idiomas</h4>
                            <?php
                            //QUERY PARA SELECCIONAR LOS REGISTROS DE IDIOMAS DEL USUARIO:
                            $queryIdioma = "select * from".' '."$tbInfoEmpleado"."_000015 WHERE Idiuse = '$userTal' AND Idiest = 'on'";
                            $commitQryIdioma = mysql_query($queryIdioma, $conex) or die (mysql_errno()." - en el query: ".$queryIdioma." - ".mysql_error());
                            ?>
                            <table align="center" class="tblNivE" style="width: 80%">
                                <thead>
                                <tr>
                                    <td align="center">
                                        <a href="#" id="add" class="btn btn-info btn-xs" title="Actualizar" onclick="location.reload()">
                                            <span class="glyphicon glyphicon-refresh"></span>
                                        </a>
                                    </td>
                                    <td><label>IDIOMA</label></td>    <td><label>LO HABLA</label></td>
                                    <td><label>LO LEE</label></td>    <td><label>LO ESCRIBE</label></td>
                                    <td></td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                while($datoIdioma = mysql_fetch_array($commitQryIdioma))
                                {
                                    $IdiomaNom = $datoIdioma['Idides']; $idiomaHab = $datoIdioma['Idihab'];
                                    $idiomaLee = $datoIdioma['Idilee']; $idiomaEsc = $datoIdioma['Idiesc'];
                                    $idiomaId = $datoIdioma['id'];

                                    if($idiomaHab == 'on'){$idiomaHab = 'SI';} else{$idiomaHab = 'NO';}
                                    if($idiomaLee == 'on'){$idiomaLee = 'SI';} else{$idiomaLee = 'NO';}
                                    if($idiomaEsc == 'on'){$idiomaEsc = 'SI';} else{$idiomaEsc = 'NO';}
                                    ?>
                                    <tr class="alternar">
                                        <td></td>
                                        <td><label>&ensp;<?php echo $IdiomaNom ?></label></td>    <td><label><?php echo $idiomaHab ?></label></td>
                                        <td><label><?php echo $idiomaLee ?></label></td>    <td><label><?php echo $idiomaEsc ?></label></td>
                                        <td>
                                            <input type="hidden" id="funcion4" name="funcion4" value="supIdioma">
                                            <a href="#" id="less" class="btn btn-info btn-xs" title="Eliminar"
                                               onclick="openOps2(funcion4,tablaMtx,userMtx,<?php echo $idiomaId ?>,tenVivi,tipVivi,tienTerr,tienLote,estVivi,chAcue,chAlca,chAseo,chEner,
                                                   chInter,chGas,chTele,credito,chBici,chBus,chCamina,chPart,chMetro,chMoto,chOtroT,chTaxi,chContra,otroTrans,chTrae,chComBoca,chComOtros,
                                                   chCasa,chOtrosAl,otroTalmu,hobbie,lugparq,locker,chAudit,chComite,chBrigada,chOtroRol,cualRol,Idefnc,Idegen,Ideced,Idepas,Idevis,
                                                   estcivUser,Idedir,lunaUser,estUser,munUser,barUser,telUser,celUser,corUser,tisanUser,extUser,chCoCon,chGesIn,chCoDoc,chGesTe,chCoInv,
                                                   chHisTe,chCoCom,chInfInt,chCopas,chMedTra,chCrede,chMeCon,chEtiIn,chMoSe,chEtiHo,chSePac,chEvCal,chTransp,chFarTe,chViEpi,chGesAm,
                                                   contEmer,Ideraz,timeDesp,turnEmp,chTorBol,chTorPla,chTorVol,chTorBal,chTorTen,chCamin,chBaile,chYoga,chEnPare,chCiclo,chMara,chTarHob,
                                                   chGruTea,chArtPla,chManual,chGastro,chClaIng,chConPle,chTarPic,chOtrAct,diaActi,horActi,actExtra,otraExtra,ranSal,telEmer,chViArr,chViPag,
                                                   chAlime,chSerPu,chTrpte,chEdPro,chEdHij,chPaCre,chReTli,chVestu,chSalud,chPaCel,chPaTar,chCoTec,chCuPer,chOtGas,chDeuSi,chPCohi,chDiEco,
                                                   chDeMif,chHiEmb,chSeDiv,chViInt,chAdLic,chMuSer,chEnGra,chNiSit,posFam,chAbuNi,chPaMad,chVecin,chGuIns,chEmDom,chUnFam,chQuSol,chCuOtr,
                                                   subViv,ahoViv,montoHa,chInuVi,chContVi,chRiAmVi,chRiEsvi,chRiSaVi,chRiPuVi,chNoFaVi,chMeEst,chMeMue,chMeEle,chMePis,chMePar,chMeCol,
                                                   chMeHum,chMeFac,chMeTec,chMeBan,chMeCoc,chMeAmp,chMeNot,chPfCah,chPfCuc,chPfTac,chPfCco,chPfChi,chPfCve,chPfInv,chPfSeg,chPfNin,chMcViv,
                                                   chMcTec,chMcMue,chMcEle,chMcVeh,chMcSal,chMcCir,chMcTur,chMcLib,chMcGah,chMcTac,chMcEdp,chMcEdf,chMcCem,chMcNin,chEpBan,chEpFon,chEpFmu,
                                                   chEpPad,chEpFam,chEpCal,chEpCaj,chEpEla,chEpNat,chEpOtr,chEpNin,chLcViv,chLcVeh,chLcSal,chLcCir,chLcTur,chLcEdf,chLcEdp,chLcCre,chLcMej,
                                                   chLcCro,chLcLib,chLcTar,chLcNin,chIaInv,chIaBan,chIaNat,chIaCac,chIaFem,chIaFmu,chIaFvp,chIaOtr,chIaNin,chNfCap,chNfDes,chNfRel,chNfMan,
                                                   chNfFin,chNfFor,chNfIdi,chNfInf,chNfFco,chNfOtr,chNfNot,chHoCin,chHoDep,chHoVid,chHoVte,chHoNav,chHoIce,chHoIpa,chHoIfi,chHoCex,chHoDes,
                                                   chHoJar,chHoCon,chHoPin,chHoEsc,chHoFot,chHoVmu,chHoVbi,chHoEsp,chHoDan,chHoTin,chHoCoc,chHoMan,chHoOtr,chHoNin,chQpHij,chQpAmi,chQpMas,
                                                   chQpSol,chQpFam,chQpAmo,chQpPar,chQpCom,chQpOtr,chHhCin,chHhDep,chHhVid,chHhVte,chHhNav,chHhIce,chHhIpa,chHhIfi,chHhCex,chHhDes,chHhJar,
                                                   chHhCon,chHhPin,chHhEsc,chHhFot,chHhVmu,chHhVbi,chHhEsp,chHhDan,chHhTin,chHhCoc,chHhMan,chHhOtr,chHhNin,chBuFdi,chBuNcd,chBuDap,chBuFmo,
                                                   chBuNdt,chBuOtr,chBuNin)">
                                                <span class="glyphicon glyphicon-minus"></span>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>

                            <div id="divAddEst" align="center">
                                <input type="hidden" id="funcion3" name="funcion3" value="addIdioma">
                                <input type="hidden" id="tablaMtx" name="tablaMtx" value="<?php echo $tbInfoEmpleado ?>">
                                <input type="hidden" id="userMtx" name="userMtx" value="<?php echo $userTal ?>">
                                <button type="button" class="btn btn-default btn-lg"
                                        onclick="openOps(funcion3,tablaMtx,userMtx,tenVivi,tipVivi,tienTerr,tienLote,estVivi,chAcue,chAlca,chAseo,chEner,chInter,chGas,chTele,credito,
                                    chBici,chBus,chCamina,chPart,chMetro,chMoto,chOtroT,chTaxi,chContra,otroTrans,chTrae,chComBoca,chComOtros,chCasa,chOtrosAl,otroTalmu,hobbie,
                                    lugparq,locker,chAudit,chComite,chBrigada,chOtroRol,cualRol,Idefnc,Idegen,Ideced,Idepas,Idevis,estcivUser,Idedir,lunaUser,estUser,munUser,
                                    barUser,telUser,celUser,corUser,tisanUser,extUser,chCoCon,chGesIn,chCoDoc,chGesTe,chCoInv,chHisTe,chCoCom,chInfInt,chCopas,chMedTra,chCrede,
                                    chMeCon,chEtiIn,chMoSe,chEtiHo,chSePac,chEvCal,chTransp,chFarTe,chViEpi,chGesAm,contEmer,Ideraz,timeDesp,turnEmp,chTorBol,chTorPla,chTorVol,
                                    chTorBal,chTorTen,chCamin,chBaile,chYoga,chEnPare,chCiclo,chMara,chTarHob,chGruTea,chArtPla,chManual,chGastro,chClaIng,chConPle,chTarPic,
                                    chOtrAct,diaActi,horActi,actExtra,otraExtra,ranSal,telEmer,chViArr,chViPag,chAlime,chSerPu,chTrpte,chEdPro,chEdHij,chPaCre,chReTli,chVestu,
                                    chSalud,chPaCel,chPaTar,chCoTec,chCuPer,chOtGas,chDeuSi,chPCohi,chDiEco,chDeMif,chHiEmb,chSeDiv,chViInt,chAdLic,chMuSer,chEnGra,chNiSit,
                                    posFam,chAbuNi,chPaMad,chVecin,chGuIns,chEmDom,chUnFam,chQuSol,chCuOtr,subViv,ahoViv,montoHa,chInuVi,chContVi,chRiAmVi,chRiEsvi,chRiSaVi,
                                    chRiPuVi,chNoFaVi,chMeEst,chMeMue,chMeEle,chMePis,chMePar,chMeCol,chMeHum,chMeFac,chMeTec,chMeBan,chMeCoc,chMeAmp,chMeNot,chPfCah,chPfCuc,
                                    chPfTac,chPfCco,chPfChi,chPfCve,chPfInv,chPfSeg,chPfNin,chMcViv,chMcTec,chMcMue,chMcEle,chMcVeh,chMcSal,chMcCir,chMcTur,chMcLib,chMcGah,
                                    chMcTac,chMcEdp,chMcEdf,chMcCem,chMcNin,chEpBan,chEpFon,chEpFmu,chEpPad,chEpFam,chEpCal,chEpCaj,chEpEla,chEpNat,chEpOtr,chEpNin,chLcViv,
                                    chLcVeh,chLcSal,chLcCir,chLcTur,chLcEdf,chLcEdp,chLcCre,chLcMej,chLcCro,chLcLib,chLcTar,chLcNin,chIaInv,chIaBan,chIaNat,chIaCac,chIaFem,
                                    chIaFmu,chIaFvp,chIaOtr,chIaNin,chNfCap,chNfDes,chNfRel,chNfMan,chNfFin,chNfFor,chNfIdi,chNfInf,chNfFco,chNfOtr,chNfNot,chHoCin,chHoDep,
                                    chHoVid,chHoVte,chHoNav,chHoIce,chHoIpa,chHoIfi,chHoCex,chHoDes,chHoJar,chHoCon,chHoPin,chHoEsc,chHoFot,chHoVmu,chHoVbi,chHoEsp,chHoDan,
                                    chHoTin,chHoCoc,chHoMan,chHoOtr,chHoNin,chQpHij,chQpAmi,chQpMas,chQpSol,chQpFam,chQpAmo,chQpPar,chQpCom,chQpOtr,chHhCin,chHhDep,chHhVid,
                                    chHhVte,chHhNav,chHhIce,chHhIpa,chHhIfi,chHhCex,chHhDes,chHhJar,chHhCon,chHhPin,chHhEsc,chHhFot,chHhVmu,chHhVbi,chHhEsp,chHhDan,chHhTin,
                                    chHhCoc,chHhMan,chHhOtr,chHhNin,chBuFdi,chBuNcd,chBuDap,chBuFmo,chBuNdt,chBuOtr,chBuNin)">
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Nuevo Idioma
                                </button>
                            </div>

                            <!-- //////////////////// ESTUDIOS ACTUALES: ////////////////////////// -->

                            <h4 class="labelTitulo">Estudios actuales (Si usted estudia actualmente, relacione todos los estudios en los que esté)</h4>
                            <?php
                            //QUERY PARA SELECCIONAR LOS ESTUDIOS ACTUALES DEL USUARIO:
                            $queryEstUser = "select * from".' '."$tbInfoEmpleado"."_000016 WHERE Nesuse = '$userTal' AND Nesest = 'on'";
                            $commitQryEstu = mysql_query($queryEstUser, $conex) or die (mysql_errno()." - en el query: ".$queryEstUser." - ".mysql_error());
                            ?>
                            <table class="tblNivE">
                                <thead>
                                <tr>
                                    <td align="center">
                                        <a href="#" id="add" class="btn btn-info btn-xs" title="Actualizar" onclick="location.reload()">
                                            <span class="glyphicon glyphicon-refresh"></span>
                                        </a>
                                    </td>
                                    <td><label>QUE ESTUDIA</label></td>     <td><label>DURACION</label></td> <td><label>INSTITUCION EDUCATIVA</label></td>
                                    <td><label>NIVEL ACTUAL</label></td>    <td><label>HORARIO</label></td>
                                    <td></td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                while($datoEstudio = mysql_fetch_array($commitQryEstu))
                                {
                                    $qEstu = $datoEstudio['Nesdes'];    $durEstu = $datoEstudio['Nesdur'];  $instEstu = $datoEstudio['Nesins'];
                                    $nivEstu = $datoEstudio['Nesniv'];  $horEstu = $datoEstudio['Neshor'];  $idEstActual = $datoEstudio['id'];
                                    ?>
                                    <tr class="alternar">
                                        <td></td>
                                        <td><label>&ensp;<?php echo $qEstu ?></label></td>  <td><label><?php echo $durEstu ?></label></td>
                                        <td><label><?php echo $instEstu ?></label></td>     <td><label><?php echo $nivEstu ?></label></td>
                                        <td><label><?php echo $horEstu ?></label></td>
                                        <td align="right">
                                            <input type="hidden" id="funcion77" name="funcion77" value="modEstActual">
                                            <a href="#" id="add" class="btn btn-info btn-xs" title="Modificar"
                                               onclick="openOps2(funcion77,tablaMtx,userMtx,<?php echo $idEstActual ?>,tenVivi,tipVivi,tienTerr,tienLote,estVivi,chAcue,
                                                   chAlca,chAseo,chEner,chInter,chGas,chTele,credito,chBici,chBus,chCamina,chPart,chMetro,chMoto,chOtroT,chTaxi,
                                                   chContra,otroTrans,chTrae,chComBoca,chComOtros,chCasa,chOtrosAl,otroTalmu,hobbie,lugparq,locker,chAudit,chComite,
                                                   chBrigada,chOtroRol,cualRol,Idefnc,Idegen,Ideced,Idepas,Idevis,estcivUser,Idedir,lunaUser,estUser,munUser,barUser,
                                                   telUser,celUser,corUser,tisanUser,extUser,chCoCon,chGesIn,chCoDoc,chGesTe,chCoInv,chHisTe,chCoCom,chInfInt,chCopas,
                                                   chMedTra,chCrede,chMeCon,chEtiIn,chMoSe,chEtiHo,chSePac,chEvCal,chTransp,chFarTe,chViEpi,chGesAm,contEmer,Ideraz,
                                                   timeDesp,turnEmp,chTorBol,chTorPla,chTorVol,chTorBal,chTorTen,chCamin,chBaile,chYoga,chEnPare,chCiclo,chMara,chTarHob,
                                                   chGruTea,chArtPla,chManual,chGastro,chClaIng,chConPle,chTarPic,chOtrAct,diaActi,horActi,actExtra,otraExtra,ranSal,
                                                   telEmer,chViArr,chViPag,chAlime,chSerPu,chTrpte,chEdPro,chEdHij,chPaCre,chReTli,chVestu,chSalud,chPaCel,chPaTar,chCoTec,
                                                   chCuPer,chOtGas,chDeuSi,chPCohi,chDiEco,chDeMif,chHiEmb,chSeDiv,chViInt,chAdLic,chMuSer,chEnGra,chNiSit,posFam,chAbuNi,chPaMad,
                                                   chVecin,chGuIns,chEmDom,chUnFam,chQuSol,chCuOtr,subViv,ahoViv,montoHa,chInuVi,chContVi,chRiAmVi,chRiEsvi,chRiSaVi,chRiPuVi,chNoFaVi,
                                                   chMeEst,chMeMue,chMeEle,chMePis,chMePar,chMeCol,chMeHum,chMeFac,chMeTec,chMeBan,chMeCoc,chMeAmp,chMeNot,chPfCah,chPfCuc,chPfTac,
                                                   chPfCco,chPfChi,chPfCve,chPfInv,chPfSeg,chPfNin,chMcViv,chMcTec,chMcMue,chMcEle,chMcVeh,chMcSal,chMcCir,chMcTur,chMcLib,chMcGah,
                                                   chMcTac,chMcEdp,chMcEdf,chMcCem,chMcNin,chEpBan,chEpFon,chEpFmu,chEpPad,chEpFam,chEpCal,chEpCaj,chEpEla,chEpNat,chEpOtr,chEpNin,
                                                   chLcViv,chLcVeh,chLcSal,chLcCir,chLcTur,chLcEdf,chLcEdp,chLcCre,chLcMej,chLcCro,chLcLib,chLcTar,chLcNin,chIaInv,chIaBan,chIaNat,
                                                   chIaCac,chIaFem,chIaFmu,chIaFvp,chIaOtr,chIaNin,chNfCap,chNfDes,chNfRel,chNfMan,chNfFin,chNfFor,chNfIdi,chNfInf,chNfFco,chNfOtr,
                                                   chNfNot,chHoCin,chHoDep,chHoVid,chHoVte,chHoNav,chHoIce,chHoIpa,chHoIfi,chHoCex,chHoDes,chHoJar,chHoCon,chHoPin,chHoEsc,chHoFot,
                                                   chHoVmu,chHoVbi,chHoEsp,chHoDan,chHoTin,chHoCoc,chHoMan,chHoOtr,chHoNin,chQpHij,chQpAmi,chQpMas,chQpSol,chQpFam,chQpAmo,chQpPar,
                                                   chQpCom,chQpOtr,chHhCin,chHhDep,chHhVid,chHhVte,chHhNav,chHhIce,chHhIpa,chHhIfi,chHhCex,chHhDes,chHhJar,chHhCon,chHhPin,chHhEsc,
                                                   chHhFot,chHhVmu,chHhVbi,chHhEsp,chHhDan,chHhTin,chHhCoc,chHhMan,chHhOtr,chHhNin,chBuFdi,chBuNcd,chBuDap,chBuFmo,chBuNdt,chBuOtr,
                                                   chBuNin)">
                                                <span class="glyphicon glyphicon-pencil"></span>
                                            </a>

                                            <input type="hidden" id="funcion7" name="funcion7" value="supEstActual">
                                            <a href="#" id="less" class="btn btn-info btn-xs" title="Eliminar"
                                               onclick="openOps2(funcion7,tablaMtx,userMtx,<?php echo $idEstActual ?>,tenVivi,tipVivi,tienTerr,tienLote,estVivi,chAcue,
                                                   chAlca,chAseo,chEner,chInter,chGas,chTele,credito,chBici,chBus,chCamina,chPart,chMetro,chMoto,chOtroT,chTaxi,
                                                   chContra,otroTrans,chTrae,chComBoca,chComOtros,chCasa,chOtrosAl,otroTalmu,hobbie,lugparq,locker,chAudit,chComite,
                                                   chBrigada,chOtroRol,cualRol,Idefnc,Idegen,Ideced,Idepas,Idevis,estcivUser,Idedir,lunaUser,estUser,munUser,barUser,
                                                   telUser,celUser,corUser,tisanUser,extUser,chCoCon,chGesIn,chCoDoc,chGesTe,chCoInv,chHisTe,chCoCom,chInfInt,chCopas,
                                                   chMedTra,chCrede,chMeCon,chEtiIn,chMoSe,chEtiHo,chSePac,chEvCal,chTransp,chFarTe,chViEpi,chGesAm,contEmer,Ideraz,
                                                   timeDesp,turnEmp,chTorBol,chTorPla,chTorVol,chTorBal,chTorTen,chCamin,chBaile,chYoga,chEnPare,chCiclo,chMara,chTarHob,
                                                   chGruTea,chArtPla,chManual,chGastro,chClaIng,chConPle,chTarPic,chOtrAct,diaActi,horActi,actExtra,otraExtra,ranSal,
                                                   telEmer,chViArr,chViPag,chAlime,chSerPu,chTrpte,chEdPro,chEdHij,chPaCre,chReTli,chVestu,chSalud,chPaCel,chPaTar,chCoTec,
                                                   chCuPer,chOtGas,chDeuSi,chPCohi,chDiEco,chDeMif,chHiEmb,chSeDiv,chViInt,chAdLic,chMuSer,chEnGra,chNiSit,posFam,chAbuNi,chPaMad,
                                                   chVecin,chGuIns,chEmDom,chUnFam,chQuSol,chCuOtr,subViv,ahoViv,montoHa,chInuVi,chContVi,chRiAmVi,chRiEsvi,chRiSaVi,chRiPuVi,chNoFaVi,
                                                   chMeEst,chMeMue,chMeEle,chMePis,chMePar,chMeCol,chMeHum,chMeFac,chMeTec,chMeBan,chMeCoc,chMeAmp,chMeNot,chPfCah,chPfCuc,chPfTac,
                                                   chPfCco,chPfChi,chPfCve,chPfInv,chPfSeg,chPfNin,chMcViv,chMcTec,chMcMue,chMcEle,chMcVeh,chMcSal,chMcCir,chMcTur,chMcLib,chMcGah,
                                                   chMcTac,chMcEdp,chMcEdf,chMcCem,chMcNin,chEpBan,chEpFon,chEpFmu,chEpPad,chEpFam,chEpCal,chEpCaj,chEpEla,chEpNat,chEpOtr,chEpNin,
                                                   chLcViv,chLcVeh,chLcSal,chLcCir,chLcTur,chLcEdf,chLcEdp,chLcCre,chLcMej,chLcCro,chLcLib,chLcTar,chLcNin,chIaInv,chIaBan,chIaNat,
                                                   chIaCac,chIaFem,chIaFmu,chIaFvp,chIaOtr,chIaNin,chNfCap,chNfDes,chNfRel,chNfMan,chNfFin,chNfFor,chNfIdi,chNfInf,chNfFco,chNfOtr,
                                                   chNfNot,chHoCin,chHoDep,chHoVid,chHoVte,chHoNav,chHoIce,chHoIpa,chHoIfi,chHoCex,chHoDes,chHoJar,chHoCon,chHoPin,chHoEsc,chHoFot,
                                                   chHoVmu,chHoVbi,chHoEsp,chHoDan,chHoTin,chHoCoc,chHoMan,chHoOtr,chHoNin,chQpHij,chQpAmi,chQpMas,chQpSol,chQpFam,chQpAmo,chQpPar,
                                                   chQpCom,chQpOtr,chHhCin,chHhDep,chHhVid,chHhVte,chHhNav,chHhIce,chHhIpa,chHhIfi,chHhCex,chHhDes,chHhJar,chHhCon,chHhPin,chHhEsc,
                                                   chHhFot,chHhVmu,chHhVbi,chHhEsp,chHhDan,chHhTin,chHhCoc,chHhMan,chHhOtr,chHhNin,chBuFdi,chBuNcd,chBuDap,chBuFmo,chBuNdt,chBuOtr,
                                                   chBuNin)">
                                                <span class="glyphicon glyphicon-minus"></span>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>

                            <div id="divAddEst" align="center">
                                <input type="hidden" id="funcion6" name="funcion6" value="addEstActual">
                                <input type="hidden" id="tablaMtx" name="tablaMtx" value="<?php echo $tbInfoEmpleado ?>">
                                <input type="hidden" id="userMtx" name="userMtx" value="<?php echo $userTal ?>">
                                <button type="button" class="btn btn-default btn-lg"
                                        onclick="openOps(funcion6,tablaMtx,userMtx,tenVivi,tipVivi,tienTerr,tienLote,estVivi,chAcue,chAlca,chAseo,chEner,chInter,chGas,
                                    chTele,credito,chBici,chBus,chCamina,chPart,chMetro,chMoto,chOtroT,chTaxi,chContra,otroTrans,chTrae,chComBoca,chComOtros,chCasa,
                                    chOtrosAl,otroTalmu,hobbie,lugparq,locker,chAudit,chComite,chBrigada,chOtroRol,cualRol,Idefnc,Idegen,Ideced,Idepas,Idevis,estcivUser,
                                    Idedir,lunaUser,estUser,munUser,barUser,telUser,celUser,corUser,tisanUser,extUser,chCoCon,chGesIn,chCoDoc,chGesTe,chCoInv,chHisTe,
                                    chCoCom,chInfInt,chCopas,chMedTra,chCrede,chMeCon,chEtiIn,chMoSe,chEtiHo,chSePac,chEvCal,chTransp,chFarTe,chViEpi,chGesAm,contEmer,
                                    Ideraz,timeDesp,turnEmp,chTorBol,chTorPla,chTorVol,chTorBal,chTorTen,chCamin,chBaile,chYoga,chEnPare,chCiclo,chMara,chTarHob,chGruTea,
                                    chArtPla,chManual,chGastro,chClaIng,chConPle,chTarPic,chOtrAct,diaActi,horActi,actExtra,otraExtra,ranSal,telEmer,chViArr,chViPag,
                                    chAlime,chSerPu,chTrpte,chEdPro,chEdHij,chPaCre,chReTli,chVestu,chSalud,chPaCel,chPaTar,chCoTec,chCuPer,chOtGas,chDeuSi,chPCohi,
                                    chDiEco,chDeMif,chHiEmb,chSeDiv,chViInt,chAdLic,chMuSer,chEnGra,chNiSit,posFam,chAbuNi,chPaMad,chVecin,chGuIns,chEmDom,chUnFam,chQuSol,
                                    chCuOtr,subViv,ahoViv,montoHa,chInuVi,chContVi,chRiAmVi,chRiEsvi,chRiSaVi,chRiPuVi,chNoFaVi,chMeEst,chMeMue,chMeEle,chMePis,chMePar,
                                    chMeCol,chMeHum,chMeFac,chMeTec,chMeBan,chMeCoc,chMeAmp,chMeNot,chPfCah,chPfCuc,chPfTac,chPfCco,chPfChi,chPfCve,chPfInv,chPfSeg,chPfNin,
                                    chMcViv,chMcTec,chMcMue,chMcEle,chMcVeh,chMcSal,chMcCir,chMcTur,chMcLib,chMcGah,chMcTac,chMcEdp,chMcEdf,chMcCem,chMcNin,chEpBan,chEpFon,
                                    chEpFmu,chEpPad,chEpFam,chEpCal,chEpCaj,chEpEla,chEpNat,chEpOtr,chEpNin,chLcViv,chLcVeh,chLcSal,chLcCir,chLcTur,chLcEdf,chLcEdp,chLcCre,
                                    chLcMej,chLcCro,chLcLib,chLcTar,chLcNin,chIaInv,chIaBan,chIaNat,chIaCac,chIaFem,chIaFmu,chIaFvp,chIaOtr,chIaNin,chNfCap,chNfDes,chNfRel,
                                    chNfMan,chNfFin,chNfFor,chNfIdi,chNfInf,chNfFco,chNfOtr,chNfNot,chHoCin,chHoDep,chHoVid,chHoVte,chHoNav,chHoIce,chHoIpa,chHoIfi,chHoCex,
                                    chHoDes,chHoJar,chHoCon,chHoPin,chHoEsc,chHoFot,chHoVmu,chHoVbi,chHoEsp,chHoDan,chHoTin,chHoCoc,chHoMan,chHoOtr,chHoNin,chQpHij,chQpAmi,
                                    chQpMas,chQpSol,chQpFam,chQpAmo,chQpPar,chQpCom,chQpOtr,chHhCin,chHhDep,chHhVid,chHhVte,chHhNav,chHhIce,chHhIpa,chHhIfi,chHhCex,chHhDes,
                                    chHhJar,chHhCon,chHhPin,chHhEsc,chHhFot,chHhVmu,chHhVbi,chHhEsp,chHhDan,chHhTin,chHhCoc,chHhMan,chHhOtr,chHhNin,chBuFdi,chBuNcd,chBuDap,
                                    chBuFmo,chBuNdt,chBuOtr,chBuNin)">
                                    <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Nuevo Estudio Actual
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                    <!------------------------ INFORMACIÓN FAMILIAR: ------------------------->

                    <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title tabsGen">
                            <a data-toggle="collapse" data-parent="#accordion" href="#collapse3">INFORMACION FAMILIAR</a>
                        </h4>
                    </div>
                    <?php
                    if($pestana == '3'){?><div id="collapse3" class="panel-collapse collapse in"><?php }
                        else{?><div id="collapse3" class="panel-collapse collapse"><?php } ?>
                            <div class="panel-body" align="center">

                                <!-- //////////////////// DATOS FAMILIARES: ////////////////////////// -->

                                <h4 class="labelTitulo">Datos Familiares</h4>
                                <?php
                                //QUERY PARA SELECCIONAR LOS DATOS FAMILIARES DEL USUARIO:
                                $queryFamUser = "select * from".' '."$tbInfoEmpleado"."_000019 WHERE Famuse = '$userTal' AND Famest = 'on'";
                                $commitFamUser = mysql_query($queryFamUser, $conex) or die (mysql_errno()." - en el query: ".$queryFamUser." - ".mysql_error());
                                $datoFamUser = mysql_fetch_array($commitFamUser);
                                $Famaco = $datoFamUser['Famaco'];   $Famcab = $datoFamUser['Famcab'];   $Fammac = $datoFamUser['Fammac'];
                                $Famaac = $datoFamUser['Famaac'];   $Famtpd = $datoFamUser['Famtpd'];   $Famtms = $datoFamUser['Famtms'];
                                ?>
                                <table class="tblNivE" style="width: 30%">
                                    <thead>
                                    <tr align="center">
                                        <td>&ensp;<label>¿ CON QUIÉN VIVE USTED ?</label></td>
                                    </tr>
                                    </thead>
                                </table>
                                <div class="wrapper">
                                    <div class="toggle_radio">
                                        <?php
                                        if($Famaco == '02')
                                        {
                                            ?>
                                            <input type="radio" class="toggle_option" id="first_toggle" name="Famaco" value="02" checked
                                                   onclick="saveDatFam('saveFam',tablaUser,userMtx,this.value)">
                                            <input type="radio" class="toggle_option" id="second_toggle" name="Famaco" VALUE="01"
                                                   onclick="saveDatFam('saveFam',tablaUser,userMtx,this.value)">
                                            <input type="radio" class="toggle_option" id="tercer_toggle" name="Famaco" VALUE="03"
                                                   onclick="saveDatFam('saveFam',tablaUser,userMtx,this.value)">
                                            <?php
                                        }
                                        if($Famaco == '01')
                                        {
                                            ?>
                                            <input type="radio" class="toggle_option" id="first_toggle" name="Famaco" value="02"
                                                   onclick="saveDatFam('saveFam',tablaUser,userMtx,this.value)">
                                            <input type="radio" class="toggle_option" id="second_toggle" name="Famaco" VALUE="01" checked
                                                   onclick="saveDatFam('saveFam',tablaUser,userMtx,this.value)">
                                            <input type="radio" class="toggle_option" id="tercer_toggle" name="Famaco" VALUE="03"
                                                   onclick="saveDatFam('saveFam',tablaUser,userMtx,this.value)">
                                            <?php
                                        }
                                        if($Famaco == '03')
                                        {
                                            ?>
                                            <input type="radio" class="toggle_option" id="first_toggle" name="Famaco" value="02"
                                                   onclick="saveDatFam('saveFam',tablaUser,userMtx,this.value)">
                                            <input type="radio" class="toggle_option" id="second_toggle" name="Famaco" VALUE="01"
                                                   onclick="saveDatFam('saveFam',tablaUser,userMtx,this.value)">
                                            <input type="radio" class="toggle_option" id="tercer_toggle" name="Famaco" VALUE="03" checked
                                                   onclick="saveDatFam('saveFam',tablaUser,userMtx,this.value)">
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <input type="radio" class="toggle_option" id="first_toggle" name="toggle_option" value="02"
                                                   onclick="saveDatFam('saveFam',tablaUser,userMtx,this.value)">
                                            <input type="radio" class="toggle_option" id="second_toggle" name="toggle_option" VALUE="01"
                                                   onclick="saveDatFam('saveFam',tablaUser,userMtx,this.value)">
                                            <input type="radio" class="toggle_option" id="tercer_toggle" name="toggle_option" VALUE="03"
                                                   onclick="saveDatFam('saveFam',tablaUser,userMtx,this.value)">
                                            <?php
                                        }
                                        ?>
                                        <label for="first_toggle"><p>Con Amigos</p></label>
                                        <label for="second_toggle"><p>Con su Familia</p></label>
                                        <label for="tercer_toggle"><p>Solo</p></label>
                                        <div class="toggle_option_slider"></div>
                                    </div>
                                </div>

                                <table class="tblNivE" style="margin-top: 20px; width: 70%; margin-bottom: 20px">
                                    <thead>
                                    <tr align="center">
                                        <td>&ensp;<label for="famcab">¿ ES USTED CABEZA DE FAMILIA ?</label></td>
                                        <td><label for="fammac">NUMERO DE NIÑOS A CARGO</label></td>
                                        <td><label for="famaac">NUMERO DE ADULTOS A CARGO</label></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr align="center">
                                        <td>
                                            <label class="container2" style="margin-top: -10px; margin-left: 115px">
                                                <?php
                                                if($Famcab == 'on')
                                                {
                                                    ?>
                                                    <input type="checkbox" id="famcab" name="famcab" checked="checked" value="on"
                                                           onclick="this.value = 'off'; saveDatFam('saveFam2',tablaUser,userMtx,this.value)">
                                                    <?php
                                                }
                                                else
                                                {
                                                    ?>
                                                    <input type="checkbox" id="famcab" name="famcab" value="off"
                                                           onclick="this.value = 'on'; saveDatFam('saveFam2',tablaUser,userMtx,this.value)">
                                                    <?php
                                                }
                                                ?>
                                                <span class="checkmark"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <input type="text" id="fammac" name="fammac" min="0" class="form-control form-sm" style="width: 60px" value="<?php echo $Fammac ?>"
                                                   onblur="saveDatFam('saveFam3',tablaUser,userMtx,this.value)">
                                        </td>
                                        <td>
                                            <input type="text" id="famaac" name="famaac" min="0" class="form-control form-sm" style="width: 60px" value="<?php echo $Famaac ?>"
                                                   onblur="saveDatFam('saveFam4',tablaUser,userMtx,this.value)">
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                                <h4 class="labelTitulo">Relacione en el siguiente cuadro las personas que conforman su núcleo familiar, si tiene hijos
                                    por favor relaciónelos así no vivan con usted
                                </h4>
                                <?php
                                $queryGruFam = "select * from".' '."$tbInfoEmpleado"."_000021 WHERE Gruuse = '$userTal' AND Gruest = 'on'";
                                $commitGruFam = mysql_query($queryGruFam, $conex) or die (mysql_errno()." - en el query: ".$queryGruFam." - ".mysql_error());
                                ?>
                                <table class="tblNuFam" style="margin-top: 10px; margin-bottom: 10px">
                                    <thead>
                                    <tr align="center">
                                        <td align="center">
                                            <a href="#" id="add" class="btn btn-info btn-xs" title="Actualizar" onclick="location.reload()">
                                                <span class="glyphicon glyphicon-refresh"></span>
                                            </a>
                                        </td>
                                        <td>&ensp;<label for="famcab">NOMBRES</label></td>      <td><label for="fammac">APELLIDOS</label></td>
                                        <td><label for="famaac">GENERO</label></td>             <td><label for="famaac">PARENTESCO</label></td>
                                        <td><label for="famaac">FECHA NACIMIENTO</label></td>   <td><label for="famaac">NIVEL EDUCATIVO</label></td>
                                        <td><label for="famaac">OCUPACION</label></td>          <td><label for="famaac">VIVE CON USTED</label></td>
                                        <td></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    while($datoGruFam = mysql_fetch_array($commitGruFam))
                                    {
                                        $grunom = $datoGruFam['Grunom'];    $gruape = $datoGruFam['Gruape'];    $grugen = $datoGruFam['Grugen'];
                                        $grupar = $datoGruFam['Grupar'];    $grufna = $datoGruFam['Grufna'];    $gruesc = $datoGruFam['Gruesc'];
                                        $gruocu = $datoGruFam['Gruocu'];    $grucom = $datoGruFam['Grucom'];    $gruart = $datoGruFam['Gruart'];
                                        $idGruFam = $datoGruFam['id'];
                                        ?>
                                        <tr align="center" class="alternar">
                                            <td></td>
                                            <td><label><?php echo $grunom ?>&ensp;</label></td> <td><label><?php echo $gruape ?>&ensp;</label></td>
                                            <td><label><?php echo $grugen ?>&ensp;</label></td> <td><label><?php echo $grupar ?>&ensp;</label></td>
                                            <td><label><?php echo $grufna ?>&ensp;</label></td> <td><label><?php echo $gruesc ?>&ensp;</label></td>
                                            <td><label><?php echo $gruocu ?>&ensp;</label></td> <td><label><?php echo $grucom ?>&ensp;</label></td>
                                            <td align="right">
                                                <input type="hidden" id="funcion88" name="funcion88" value="modIntFam">
                                                <a href="#" id="add" class="btn btn-info btn-xs" title="Modificar"
                                                   onclick="openOps2(funcion88,tablaMtx,userMtx,<?php echo $idGruFam ?>,tenVivi,tipVivi,tienTerr,tienLote,estVivi,chAcue,
                                                       chAlca,chAseo,chEner,chInter,chGas,chTele,credito,chBici,chBus,chCamina,chPart,chMetro,chMoto,chOtroT,chTaxi,
                                                       chContra,otroTrans,chTrae,chComBoca,chComOtros,chCasa,chOtrosAl,otroTalmu,hobbie,lugparq,locker,chAudit,chComite,
                                                       chBrigada,chOtroRol,cualRol,Idefnc,Idegen,Ideced,Idepas,Idevis,estcivUser,Idedir,lunaUser,estUser,munUser,barUser,
                                                       telUser,celUser,corUser,tisanUser,extUser,chCoCon,chGesIn,chCoDoc,chGesTe,chCoInv,chHisTe,chCoCom,chInfInt,chCopas,
                                                       chMedTra,chCrede,chMeCon,chEtiIn,chMoSe,chEtiHo,chSePac,chEvCal,chTransp,chFarTe,chViEpi,chGesAm,contEmer,Ideraz,
                                                       timeDesp,turnEmp,chTorBol,chTorPla,chTorVol,chTorBal,chTorTen,chCamin,chBaile,chYoga,chEnPare,chCiclo,chMara,chTarHob,
                                                       chGruTea,chArtPla,chManual,chGastro,chClaIng,chConPle,chTarPic,chOtrAct,diaActi,horActi,actExtra,otraExtra,ranSal,
                                                       telEmer,chViArr,chViPag,chAlime,chSerPu,chTrpte,chEdPro,chEdHij,chPaCre,chReTli,chVestu,chSalud,chPaCel,chPaTar,chCoTec,
                                                       chCuPer,chOtGas,chDeuSi,chPCohi,chDiEco,chDeMif,chHiEmb,chSeDiv,chViInt,chAdLic,chMuSer,chEnGra,chNiSit,posFam,chAbuNi,chPaMad,
                                                       chVecin,chGuIns,chEmDom,chUnFam,chQuSol,chCuOtr,subViv,ahoViv,montoHa,chInuVi,chContVi,chRiAmVi,chRiEsvi,chRiSaVi,chRiPuVi,chNoFaVi,
                                                       chMeEst,chMeMue,chMeEle,chMePis,chMePar,chMeCol,chMeHum,chMeFac,chMeTec,chMeBan,chMeCoc,chMeAmp,chMeNot,chPfCah,chPfCuc,chPfTac,
                                                       chPfCco,chPfChi,chPfCve,chPfInv,chPfSeg,chPfNin,chMcViv,chMcTec,chMcMue,chMcEle,chMcVeh,chMcSal,chMcCir,chMcTur,chMcLib,chMcGah,
                                                       chMcTac,chMcEdp,chMcEdf,chMcCem,chMcNin,chEpBan,chEpFon,chEpFmu,chEpPad,chEpFam,chEpCal,chEpCaj,chEpEla,chEpNat,chEpOtr,chEpNin,
                                                       chLcViv,chLcVeh,chLcSal,chLcCir,chLcTur,chLcEdf,chLcEdp,chLcCre,chLcMej,chLcCro,chLcLib,chLcTar,chLcNin,chIaInv,chIaBan,chIaNat,
                                                       chIaCac,chIaFem,chIaFmu,chIaFvp,chIaOtr,chIaNin,chNfCap,chNfDes,chNfRel,chNfMan,chNfFin,chNfFor,chNfIdi,chNfInf,chNfFco,chNfOtr,
                                                       chNfNot,chHoCin,chHoDep,chHoVid,chHoVte,chHoNav,chHoIce,chHoIpa,chHoIfi,chHoCex,chHoDes,chHoJar,chHoCon,chHoPin,chHoEsc,chHoFot,
                                                       chHoVmu,chHoVbi,chHoEsp,chHoDan,chHoTin,chHoCoc,chHoMan,chHoOtr,chHoNin,chQpHij,chQpAmi,chQpMas,chQpSol,chQpFam,chQpAmo,chQpPar,
                                                       chQpCom,chQpOtr,chHhCin,chHhDep,chHhVid,chHhVte,chHhNav,chHhIce,chHhIpa,chHhIfi,chHhCex,chHhDes,chHhJar,chHhCon,chHhPin,chHhEsc,
                                                       chHhFot,chHhVmu,chHhVbi,chHhEsp,chHhDan,chHhTin,chHhCoc,chHhMan,chHhOtr,chHhNin,chBuFdi,chBuNcd,chBuDap,chBuFmo,chBuNdt,chBuOtr,
                                                       chBuNin)">
                                                    <span class="glyphicon glyphicon-pencil"></span>
                                                </a>

                                                <input type="hidden" id="funcion8" name="funcion8" value="supIntFam">
                                                <a href="#" id="less" class="btn btn-info btn-xs" title="Eliminar"
                                                   onclick="openOps2(funcion8,tablaMtx,userMtx,<?php echo $idGruFam ?>,tenVivi,tipVivi,tienTerr,tienLote,estVivi,chAcue,
                                                       chAlca,chAseo,chEner,chInter,chGas,chTele,credito,chBici,chBus,chCamina,chPart,chMetro,chMoto,chOtroT,chTaxi,
                                                       chContra,otroTrans,chTrae,chComBoca,chComOtros,chCasa,chOtrosAl,otroTalmu,hobbie,lugparq,locker,chAudit,chComite,
                                                       chBrigada,chOtroRol,cualRol,Idefnc,Idegen,Ideced,Idepas,Idevis,estcivUser,Idedir,lunaUser,estUser,munUser,barUser,
                                                       telUser,celUser,corUser,tisanUser,extUser,chCoCon,chGesIn,chCoDoc,chGesTe,chCoInv,chHisTe,chCoCom,chInfInt,chCopas,
                                                       chMedTra,chCrede,chMeCon,chEtiIn,chMoSe,chEtiHo,chSePac,chEvCal,chTransp,chFarTe,chViEpi,chGesAm,contEmer,Ideraz,
                                                       timeDesp,turnEmp,chTorBol,chTorPla,chTorVol,chTorBal,chTorTen,chCamin,chBaile,chYoga,chEnPare,chCiclo,chMara,chTarHob,
                                                       chGruTea,chArtPla,chManual,chGastro,chClaIng,chConPle,chTarPic,chOtrAct,diaActi,horActi,actExtra,otraExtra,ranSal,
                                                       telEmer,chViArr,chViPag,chAlime,chSerPu,chTrpte,chEdPro,chEdHij,chPaCre,chReTli,chVestu,chSalud,chPaCel,chPaTar,chCoTec,
                                                       chCuPer,chOtGas,chDeuSi,chPCohi,chDiEco,chDeMif,chHiEmb,chSeDiv,chViInt,chAdLic,chMuSer,chEnGra,chNiSit,posFam,chAbuNi,chPaMad,
                                                       chVecin,chGuIns,chEmDom,chUnFam,chQuSol,chCuOtr,subViv,ahoViv,montoHa,chInuVi,chContVi,chRiAmVi,chRiEsvi,chRiSaVi,chRiPuVi,chNoFaVi,
                                                       chMeEst,chMeMue,chMeEle,chMePis,chMePar,chMeCol,chMeHum,chMeFac,chMeTec,chMeBan,chMeCoc,chMeAmp,chMeNot,chPfCah,chPfCuc,chPfTac,
                                                       chPfCco,chPfChi,chPfCve,chPfInv,chPfSeg,chPfNin,chMcViv,chMcTec,chMcMue,chMcEle,chMcVeh,chMcSal,chMcCir,chMcTur,chMcLib,chMcGah,
                                                       chMcTac,chMcEdp,chMcEdf,chMcCem,chMcNin,chEpBan,chEpFon,chEpFmu,chEpPad,chEpFam,chEpCal,chEpCaj,chEpEla,chEpNat,chEpOtr,chEpNin,
                                                       chLcViv,chLcVeh,chLcSal,chLcCir,chLcTur,chLcEdf,chLcEdp,chLcCre,chLcMej,chLcCro,chLcLib,chLcTar,chLcNin,chIaInv,chIaBan,chIaNat,
                                                       chIaCac,chIaFem,chIaFmu,chIaFvp,chIaOtr,chIaNin,chNfCap,chNfDes,chNfRel,chNfMan,chNfFin,chNfFor,chNfIdi,chNfInf,chNfFco,chNfOtr,
                                                       chNfNot,chHoCin,chHoDep,chHoVid,chHoVte,chHoNav,chHoIce,chHoIpa,chHoIfi,chHoCex,chHoDes,chHoJar,chHoCon,chHoPin,chHoEsc,chHoFot,
                                                       chHoVmu,chHoVbi,chHoEsp,chHoDan,chHoTin,chHoCoc,chHoMan,chHoOtr,chHoNin,chQpHij,chQpAmi,chQpMas,chQpSol,chQpFam,chQpAmo,chQpPar,
                                                       chQpCom,chQpOtr,chHhCin,chHhDep,chHhVid,chHhVte,chHhNav,chHhIce,chHhIpa,chHhIfi,chHhCex,chHhDes,chHhJar,chHhCon,chHhPin,chHhEsc,
                                                       chHhFot,chHhVmu,chHhVbi,chHhEsp,chHhDan,chHhTin,chHhCoc,chHhMan,chHhOtr,chHhNin,chBuFdi,chBuNcd,chBuDap,chBuFmo,chBuNdt,chBuOtr,
                                                       chBuNin)">
                                                    <span class="glyphicon glyphicon-minus"></span>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                    </tbody>
                                </table>

                                <div id="divAddEst" align="center">
                                    <input type="hidden" id="funcion9" name="funcion9" value="addIntFam">
                                    <input type="hidden" id="tablaMtx" name="tablaMtx" value="<?php echo $tbInfoEmpleado ?>">
                                    <input type="hidden" id="userMtx" name="userMtx" value="<?php echo $userTal ?>">
                                    <button type="button" class="btn btn-default btn-lg"
                                            onclick="openOps(funcion9,tablaMtx,userMtx,tenVivi,tipVivi,tienTerr,tienLote,estVivi,chAcue,chAlca,chAseo,chEner,chInter,chGas,chTele,credito,
                                    chBici,chBus,chCamina,chPart,chMetro,chMoto,chOtroT,chTaxi,chContra,otroTrans,chTrae,chComBoca,chComOtros,chCasa,chOtrosAl,otroTalmu,hobbie,
                                    lugparq,locker,chAudit,chComite,chBrigada,chOtroRol,cualRol,Idefnc,Idegen,Ideced,Idepas,Idevis,estcivUser,Idedir,lunaUser,estUser,munUser,
                                    barUser,telUser,celUser,corUser,tisanUser,extUser,chCoCon,chGesIn,chCoDoc,chGesTe,chCoInv,chHisTe,chCoCom,chInfInt,chCopas,chMedTra,chCrede,
                                    chMeCon,chEtiIn,chMoSe,chEtiHo,chSePac,chEvCal,chTransp,chFarTe,chViEpi,chGesAm,contEmer,Ideraz,timeDesp,turnEmp,chTorBol,chTorPla,chTorVol,
                                    chTorBal,chTorTen,chCamin,chBaile,chYoga,chEnPare,chCiclo,chMara,chTarHob,chGruTea,chArtPla,chManual,chGastro,chClaIng,chConPle,chTarPic,
                                    chOtrAct,diaActi,horActi,actExtra,otraExtra,ranSal,telEmer,chViArr,chViPag,chAlime,chSerPu,chTrpte,chEdPro,chEdHij,chPaCre,chReTli,chVestu,
                                    chSalud,chPaCel,chPaTar,chCoTec,chCuPer,chOtGas,chDeuSi,chPCohi,chDiEco,chDeMif,chHiEmb,chSeDiv,chViInt,chAdLic,chMuSer,chEnGra,chNiSit,posFam,
                                    chAbuNi,chPaMad,chVecin,chGuIns,chEmDom,chUnFam,chQuSol,chCuOtr,subViv,ahoViv,montoHa,chInuVi,chContVi,chRiAmVi,chRiEsvi,chRiSaVi,chRiPuVi,
                                    chNoFaVi,chMeEst,chMeMue,chMeEle,chMePis,chMePar,chMeCol,chMeHum,chMeFac,chMeTec,chMeBan,chMeCoc,chMeAmp,chMeNot,chPfCah,chPfCuc,chPfTac,
                                    chPfCco,chPfChi,chPfCve,chPfInv,chPfSeg,chPfNin,chMcViv,chMcTec,chMcMue,chMcEle,chMcVeh,chMcSal,chMcCir,chMcTur,chMcLib,chMcGah,chMcTac,chMcEdp,
                                    chMcEdf,chMcCem,chMcNin,chEpBan,chEpFon,chEpFmu,chEpPad,chEpFam,chEpCal,chEpCaj,chEpEla,chEpNat,chEpOtr,chEpNin,chLcViv,chLcVeh,chLcSal,chLcCir,
                                    chLcTur,chLcEdf,chLcEdp,chLcCre,chLcMej,chLcCro,chLcLib,chLcTar,chLcNin,chIaInv,chIaBan,chIaNat,chIaCac,chIaFem,chIaFmu,chIaFvp,chIaOtr,chIaNin,
                                    chNfCap,chNfDes,chNfRel,chNfMan,chNfFin,chNfFor,chNfIdi,chNfInf,chNfFco,chNfOtr,chNfNot,chHoCin,chHoDep,chHoVid,chHoVte,chHoNav,chHoIce,chHoIpa,
                                    chHoIfi,chHoCex,chHoDes,chHoJar,chHoCon,chHoPin,chHoEsc,chHoFot,chHoVmu,chHoVbi,chHoEsp,chHoDan,chHoTin,chHoCoc,chHoMan,chHoOtr,chHoNin,chQpHij,
                                    chQpAmi,chQpMas,chQpSol,chQpFam,chQpAmo,chQpPar,chQpCom,chQpOtr,chHhCin,chHhDep,chHhVid,chHhVte,chHhNav,chHhIce,chHhIpa,chHhIfi,chHhCex,chHhDes,
                                    chHhJar,chHhCon,chHhPin,chHhEsc,chHhFot,chHhVmu,chHhVbi,chHhEsp,chHhDan,chHhTin,chHhCoc,chHhMan,chHhOtr,chHhNin,chBuFdi,chBuNcd,chBuDap,chBuFmo,
                                    chBuNdt,chBuOtr,chBuNin)">
                                        <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Nuevo Integrante Familiar
                                    </button>
                                </div>

                                <table class="tblNivE" style="margin-top: 20px; width: 70%; margin-bottom: 10px">
                                    <thead>
                                    <tr align="center">
                                        <td>&ensp;<label for="famDiscap">En su grupo familiar tiene personas con algún tipo de discapacidad</label></td>
                                        <td align="right">
                                            <select id="famDiscap" name="famDiscap" class="form-control form-sm" style="width: 160px"
                                                    onchange="saveDatFam('saveFam5',tablaUser,userMtx,this.value)">
                                                <option>SI</option>
                                                <option>NO</option>
                                                <?php
                                                if($Famtpd == 'off'){$Famtpd = 'NO';} else{$Famtpd = 'SI';}
                                                if($Famtpd != null){?><option selected disabled><?php echo $Famtpd ?></option><?php }
                                                else{?><option selected disabled>Seleccione...</option><?php }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                    </thead>
                                </table>

                                <table class="tblNivE" style="width: 70%; margin-bottom: 20px">
                                    <thead>
                                    <tr align="center">
                                        <td>&ensp;<label for="mascota">Tiene mascota, de qué tipo</label></td>
                                        <td align="right">
                                            <input type="text" id="mascota" name="mascota" class="form-control form-sm" style="width: 160px"
                                                   value="<?php if($Famtms != null){echo $Famtms;} ?>"
                                                   onblur="saveDatFam('saveFam6',tablaUser,userMtx,this.value)">
                                        </td>
                                    </tr>
                                    </thead>
                                </table>

                                <h4 class="labelTitulo">Salud</h4>

                                <table class="tblNivE" style="margin-top: 10px; width: 70%; margin-bottom: 10px">
                                    <thead>
                                    <tr align="center">
                                        <td>&ensp;<label for="epsuser">EPS actual</label></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td align="right">
                                            <select id="epsuser" name="epsuser" class="form-control form-sm" style="width: 774px"
                                                    onchange="saveDatFam('saveSal',tablaUser,userMtx,this.value)">
                                                <?php
                                                $queryEps = "select Epscod,Epsnom from root_000073 WHERE Epsest = 'on'";
                                                $commitQryEps = mysql_query($queryEps, $conex) or die (mysql_errno()." - en el query: ".$queryEps." - ".mysql_error());
                                                while($datoEps = mysql_fetch_assoc($commitQryEps))
                                                {
                                                    $codEps = $datoEps['Epscod']; $desEps = $datoEps['Epsnom'];
                                                    ?><option><?php echo $codEps.'-'.$desEps ?></option><?php
                                                }
                                                $epsAct = datUserxEmp($wuse,$conex,21);
                                                $epsActual = obtenerDatoEps($epsAct,$conex);
                                                if($epsActual != null){?><option selected><?php echo $epsActual ?></option><?php }
                                                else{?><option selected disabled>Seleccione...</option><?php }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                                <table class="tblNivE" style="width: 70%; margin-bottom: 20px">
                                    <thead>
                                    <tr align="center">
                                        <td>&ensp;<label for="polizauser">¿ Tienes algún plan de salud complementario ?</label></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td align="right">
                                            <?php $polizaUser = datUserxEmp($wuse,$conex,22); ?>
                                            <select id="polizauser" name="polizauser" class="form-control form-sm inpEst" style="width: 774px"
                                                    onchange="saveDatFam('saveSal2',tablaUser,userMtx,this.value)">
                                                <option>MEDICINA PREPAGADA</option>
                                                <option>PLAN COMPLEMENTARIO DE SALUD CON LA EPS</option>
                                                <option>POLIZA DE ASEGURAMIENTO</option>
                                                <option>OTRO</option>
                                                <option>NINGUNO</option>
                                                <?php
                                                if($polizaUser != null){?><option selected><?php echo $polizaUser ?></option><?php }
                                                else{?><option selected disabled>Seleccione</option><?php }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                                <h4 class="labelTitulo">Otros Aspectos</h4>

                                <!-- //////////////////// ASPECTOS EN LOS QUE GASTA SUS INGRESOS: ////////////////////////// -->

                                <section>
                                    <?php
                                    $usuGasto = datUserxEmp($wuse,$conex,28);    $datoGas = explode(",",$usuGasto);
                                    $varGAS1 = $datoGas[0];     $varGAS2 = $datoGas[1];     $varGAS3 = $datoGas[2];     $varGAS4 = $datoGas[3];     $varGAS5 = $datoGas[4];
                                    $varGAS6 = $datoGas[5];     $varGAS7 = $datoGas[6];     $varGAS8 = $datoGas[7];     $varGAS9 = $datoGas[8];     $varGAS10 = $datoGas[9];
                                    $varGAS11 = $datoGas[10];   $varGAS12 = $datoGas[11];   $varGAS13 = $datoGas[12];   $varGAS14 = $datoGas[13];   $varGAS15 = $datoGas[14];
                                    $varGAS16 = $datoGas[15];
                                    ?>
                                </section>

                                <table class="tblNivE" style="margin-top: 10px; width: 70%; margin-bottom: 10px">
                                    <tr>
                                        <td class="tdTblTrans" colspan="2" align="center">
                                            <label>&ensp; Aspectos principales en los que usted gasta sus ingresos: (Selecciones máximo 5 opciones)</label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 300px">
                                            <label for="chViArr" class="container12">Vivienda - Arriendo
                                                <?php
                                                if($varGAS1 == 'on'){?><input type="checkbox" id="chViArr" name="chViArr" checked="checked" value="on"
                                                                              onclick="contOp('chViArr'); sumActi2('chViArr')"><?php }
                                                else{?><input type="checkbox" id="chViArr" name="chViArr" value="off" onclick="contOp('chViArr'); sumActi2('chViArr')"><?php }
                                                ?>
                                                <span class="checkmark12"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label for="chViPag" class="container13">Vivienda - Pago cuotas crédito
                                                <?php
                                                if($varGAS2 == 'on'){?><input type="checkbox" id="chViPag" name="chViPag" checked="checked" value="on"
                                                                              onclick="contOp('chViPag'); sumActi2('chViPag')"><?php }
                                                else{?><input type="checkbox" id="chViPag" name="chViPag" value="off" onclick="contOp('chViPag'); sumActi2('chViPag')"><?php }
                                                ?>
                                                <span class="checkmark13"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="chAlime" class="container12">Alimentacion
                                                <?php
                                                if($varGAS3 == 'on'){?><input type="checkbox" id="chAlime" name="chAlime" checked="checked" value="on"
                                                                              onclick="contOp('chAlime'); sumActi2('chAlime')"><?php }
                                                else{?><input type="checkbox" id="chAlime" name="chAlime" value="off" onclick="contOp('chAlime'); sumActi2('chAlime')"><?php }
                                                ?>
                                                <span class="checkmark12"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label for="chSerPu" class="container13">Servicios públicos
                                                <?php
                                                if($varGAS4 == 'on'){?><input type="checkbox" id="chSerPu" name="chSerPu" checked="checked" value="on"
                                                                              onclick="contOp('chSerPu'); sumActi2('chSerPu')"><?php }
                                                else{?><input type="checkbox" id="chSerPu" name="chSerPu" value="off" onclick="contOp('chSerPu'); sumActi2('chSerPu')"><?php }
                                                ?>
                                                <span class="checkmark13"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="chTrpte" class="container12">Transporte
                                                <?php
                                                if($varGAS5 == 'on'){?><input type="checkbox" id="chTrpte" name="chTrpte" checked="checked" value="on"
                                                                              onclick="contOp('chTrpte'); sumActi2('chTrpte')"><?php }
                                                else{?><input type="checkbox" id="chTrpte" name="chTrpte" value="off" onclick="contOp('chTrpte'); sumActi2('chTrpte')"><?php }
                                                ?>
                                                <span class="checkmark12"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label for="chEdPro" class="container13">Educación propia
                                                <?php
                                                if($varGAS6 == 'on'){?><input type="checkbox" id="chEdPro" name="chEdPro" checked="checked" value="on"
                                                                              onclick="contOp('chEdPro'); sumActi2('chEdPro')"><?php }
                                                else{?><input type="checkbox" id="chEdPro" name="chEdPro" value="off" onclick="contOp('chEdPro'); sumActi2('chEdPro')"><?php }
                                                ?>
                                                <span class="checkmark13"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="chEdHij" class="container12">Educación de los hijos
                                                <?php
                                                if($varGAS7 == 'on'){?><input type="checkbox" id="chEdHij" name="chEdHij" checked="checked" value="on"
                                                                              onclick="contOp('chEdHij'); sumActi2('chEdHij')"><?php }
                                                else{?><input type="checkbox" id="chEdHij" name="chEdHij" value="off" onclick="contOp('chEdHij'); sumActi2('chEdHij')"><?php }
                                                ?>
                                                <span class="checkmark12"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label for="chPaCre" class="container13">Pago de crédito
                                                <?php
                                                if($varGAS8 == 'on'){?><input type="checkbox" id="chPaCre" name="chPaCre" checked="checked" value="on"
                                                                              onclick="contOp('chPaCre'); sumActi2('chPaCre')"><?php }
                                                else{?><input type="checkbox" id="chPaCre" name="chPaCre" value="off" onclick="contOp('chPaCre'); sumActi2('chPaCre')"><?php }
                                                ?>
                                                <span class="checkmark13"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="chReTli" class="container12">Recreación - tiempo libre
                                                <?php
                                                if($varGAS9 == 'on'){?><input type="checkbox" id="chReTli" name="chReTli" checked="checked" value="on"
                                                                              onclick="contOp('chReTli'); sumActi2('chReTli')"><?php }
                                                else{?><input type="checkbox" id="chReTli" name="chReTli" value="off" onclick="contOp('chReTli'); sumActi2('chReTli')"><?php }
                                                ?>
                                                <span class="checkmark12"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label for="chVestu" class="container13">Vestuario
                                                <?php
                                                if($varGAS10 == 'on'){?><input type="checkbox" id="chVestu" name="chVestu" checked="checked" value="on"
                                                                               onclick="contOp('chVestu'); sumActi2('chVestu')"><?php }
                                                else{?><input type="checkbox" id="chVestu" name="chVestu" value="off" onclick="contOp('chVestu'); sumActi2('chVestu')"><?php }
                                                ?>
                                                <span class="checkmark13"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="chSalud" class="container12">Salud
                                                <?php
                                                if($varGAS11 == 'on'){?><input type="checkbox" id="chSalud" name="chSalud" checked="checked" value="on"
                                                                               onclick="contOp('chSalud'); sumActi2('chSalud')"><?php }
                                                else{?><input type="checkbox" id="chSalud" name="chSalud" value="off" onclick="contOp('chSalud'); sumActi2('chSalud')"><?php }
                                                ?>
                                                <span class="checkmark12"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label for="chPaCel" class="container13">Pago de celular
                                                <?php
                                                if($varGAS12 == 'on'){?><input type="checkbox" id="chPaCel" name="chPaCel" checked="checked" value="on"
                                                                               onclick="contOp('chPaCel'); sumActi2('chPaCel')"><?php }
                                                else{?><input type="checkbox" id="chPaCel" name="chPaCel" value="off" onclick="contOp('chPaCel'); sumActi2('chPaCel')"><?php }
                                                ?>
                                                <span class="checkmark13"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="chPaTar" class="container12">Pago de tarjetas de crédito
                                                <?php
                                                if($varGAS13 == 'on'){?><input type="checkbox" id="chPaTar" name="chPaTar" checked="checked" value="on"
                                                                               onclick="contOp('chPaTar'); sumActi2('chPaTar')"><?php }
                                                else{?><input type="checkbox" id="chPaTar" name="chPaTar" value="off" onclick="contOp('chPaTar'); sumActi2('chPaTar')"><?php }
                                                ?>
                                                <span class="checkmark12"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label for="chCoTec" class="container13">Compra de tecnología
                                                <?php
                                                if($varGAS14 == 'on'){?><input type="checkbox" id="chCoTec" name="chCoTec" checked="checked" value="on"
                                                                               onclick="contOp('chCoTec'); sumActi2('chCoTec')"><?php }
                                                else{?><input type="checkbox" id="chCoTec" name="chCoTec" value="off" onclick="contOp('chCoTec'); sumActi2('chCoTec')"><?php }
                                                ?>
                                                <span class="checkmark13"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="chCuPer" class="container12">Cuidado personal y belleza
                                                <?php
                                                if($varGAS15 == 'on'){?><input type="checkbox" id="chCuPer" name="chCuPer" checked="checked" value="on"
                                                                               onclick="contOp('chCuPer'); sumActi2('chCuPer')"><?php }
                                                else{?><input type="checkbox" id="chCuPer" name="chCuPer" value="off" onclick="contOp('chCuPer'); sumActi2('chCuPer')"><?php }
                                                ?>
                                                <span class="checkmark12"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label for="chOtGas" class="container13">Otro
                                                <?php
                                                if($varGAS16 == 'on'){?><input type="checkbox" id="chOtGas" name="chOtGas" checked="checked" value="on"
                                                                               onclick="contOp('chOtGas'); sumActi2('chOtGas')"><?php }
                                                else{?><input type="checkbox" id="chOtGas" name="chOtGas" value="off" onclick="contOp('chOtGas'); sumActi2('chOtGas')"><?php }
                                                ?>
                                                <span class="checkmark13"></span>
                                            </label>
                                        </td>
                                    </tr>
                                </table>

                                <!-- //////////////////// SITUACIONES VIDA FAMILIAR: ////////////////////////// -->

                                <section>
                                    <?php
                                    $usuSit = datUserxEmp($wuse,$conex,29);    $datoSit = explode(",",$usuSit);
                                    $varSIT1 = $datoSit[0];     $varSIT2 = $datoSit[1];     $varSIT3 = $datoSit[2];     $varSIT4 = $datoSit[3];     $varSIT5 = $datoSit[4];
                                    $varSIT6 = $datoSit[5];     $varSIT7 = $datoSit[6];     $varSIT8 = $datoSit[7];     $varSIT9 = $datoSit[8];     $varSIT10 = $datoSit[9];
                                    $varSIT11 = $datoSit[10];
                                    ?>
                                </section>

                                <table class="tblNivE" style="margin-top: 20px; width: 96%; margin-bottom: 10px">
                                    <tr>
                                        <td class="tdTblTrans" colspan="2" align="center">
                                            <label>&ensp; ¿ Cuáles de las siguientes situaciones están presentes en su vida familiar ?</label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 300px">
                                            <label for="chDeuSi" class="container14">Deudas que superan los ingresos
                                                <?php
                                                if($varSIT1 == 'on'){?><input type="checkbox" id="chDeuSi" name="chDeuSi" checked="checked" value="on"
                                                                              onclick="contOp('chDeuSi'); sumActi2('chDeuSi')"><?php }
                                                else{?><input type="checkbox" id="chDeuSi" name="chDeuSi" value="off" onclick="contOp('chDeuSi'); sumActi2('chDeuSi')"><?php }
                                                ?>
                                                <span class="checkmark14"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label for="chPCohi" class="container15">Problemas de conducta con sus hijos
                                                <?php
                                                if($varSIT2 == 'on'){?><input type="checkbox" id="chPCohi" name="chPCohi" checked="checked" value="on"
                                                                              onclick="contOp('chPCohi'); sumActi2('chPCohi')"><?php }
                                                else{?><input type="checkbox" id="chPCohi" name="chPCohi" value="off" onclick="contOp('chPCohi'); sumActi2('chPCohi')"><?php }
                                                ?>
                                                <span class="checkmark15"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="chDiEco" class="container14">Dificultades económicas
                                                <?php
                                                if($varSIT3 == 'on'){?><input type="checkbox" id="chDiEco" name="chDiEco" checked="checked" value="on"
                                                                              onclick="contOp('chDiEco'); sumActi2('chDiEco')"><?php }
                                                else{?><input type="checkbox" id="chDiEco" name="chDiEco" value="off" onclick="contOp('chDiEco'); sumActi2('chDiEco')"><?php }
                                                ?>
                                                <span class="checkmark14"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label for="chDeMif" class="container15">Desempleo de algún miembro de su familia
                                                <?php
                                                if($varSIT4 == 'on'){?><input type="checkbox" id="chDeMif" name="chDeMif" checked="checked" value="on"
                                                                              onclick="contOp('chDeMif'); sumActi2('chDeMif')"><?php }
                                                else{?><input type="checkbox" id="chDeMif" name="chDeMif" value="off" onclick="contOp('chDeMif'); sumActi2('chDeMif')"><?php }
                                                ?>
                                                <span class="checkmark15"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="chHiEmb" class="container14">Hijos adolescentes en embarazo o con hijos
                                                <?php
                                                if($varSIT5 == 'on'){?><input type="checkbox" id="chHiEmb" name="chHiEmb" checked="checked" value="on"
                                                                              onclick="contOp('chHiEmb'); sumActi2('chHiEmb')"><?php }
                                                else{?><input type="checkbox" id="chHiEmb" name="chHiEmb" value="off" onclick="contOp('chHiEmb'); sumActi2('chHiEmb')"><?php }
                                                ?>
                                                <span class="checkmark14"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label for="chSeDiv" class="container15">Separación - divorcio
                                                <?php
                                                if($varSIT6 == 'on'){?><input type="checkbox" id="chSeDiv" name="chSeDiv" checked="checked" value="on"
                                                                              onclick="contOp('chSeDiv'); sumActi2('chSeDiv')"><?php }
                                                else{?><input type="checkbox" id="chSeDiv" name="chSeDiv" value="off" onclick="contOp('chSeDiv'); sumActi2('chSeDiv')"><?php }
                                                ?>
                                                <span class="checkmark15"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="chViInt" class="container14">Violencia intrafamiliar
                                                <?php
                                                if($varSIT7 == 'on'){?><input type="checkbox" id="chViInt" name="chViInt" checked="checked" value="on"
                                                                              onclick="contOp('chViInt'); sumActi2('chViInt')"><?php }
                                                else{?><input type="checkbox" id="chViInt" name="chViInt" value="off" onclick="contOp('chViInt'); sumActi2('chViInt')"><?php }
                                                ?>
                                                <span class="checkmark14"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label for="chAdLic" class="container15">Adicciones al licor, tabaquismo, consumo de sustancias psicoactivas
                                                <?php
                                                if($varSIT8 == 'on'){?><input type="checkbox" id="chAdLic" name="chAdLic" checked="checked" value="on"
                                                                              onclick="contOp('chAdLic'); sumActi2('chAdLic')"><?php }
                                                else{?><input type="checkbox" id="chAdLic" name="chAdLic" value="off" onclick="contOp('chAdLic'); sumActi2('chAdLic')"><?php }
                                                ?>
                                                <span class="checkmark15"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="chMuSer" class="container14">Muerte de seres queridos
                                                <?php
                                                if($varSIT9 == 'on'){?><input type="checkbox" id="chMuSer" name="chMuSer" checked="checked" value="on"
                                                                              onclick="contOp('chMuSer'); sumActi2('chMuSer')"><?php }
                                                else{?><input type="checkbox" id="chMuSer" name="chMuSer" value="off" onclick="contOp('chMuSer'); sumActi2('chMuSer')"><?php }
                                                ?>
                                                <span class="checkmark14"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label for="chEnGra" class="container15">Enfermedad grave de algún miembro de la familia
                                                <?php
                                                if($varSIT10 == 'on'){?><input type="checkbox" id="chEnGra" name="chEnGra" checked="checked" value="on"
                                                                               onclick="contOp('chEnGra'); sumActi2('chEnGra')"><?php }
                                                else{?><input type="checkbox" id="chEnGra" name="chEnGra" value="off" onclick="contOp('chEnGra'); sumActi2('chEnGra')"><?php }
                                                ?>
                                                <span class="checkmark15"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="chNiSit" class="container14">Ninguno
                                                <?php
                                                if($varSIT11 == 'on'){?><input type="checkbox" id="chNiSit" name="chNiSit" checked="checked" value="on"
                                                                               onclick="contOp('chNiSit'); sumActi2('chNiSit')"><?php }
                                                else{?><input type="checkbox" id="chNiSit" name="chNiSit" value="off" onclick="contOp('chNiSit'); sumActi2('chNiSit')"><?php }
                                                ?>
                                                <span class="checkmark14"></span>
                                            </label>
                                        </td>
                                        <td>

                                        </td>
                                    </tr>
                                </table>

                                <!-- //////////////////// POSICION EN EL GRUPO FAMILIAR: ////////////////////////// -->

                                <table class="tblNivE" style="margin-top: 20px; width: 70%; margin-bottom: 10px">
                                    <thead>
                                    <tr align="center">
                                        <td>&ensp;<label for="posFam">¿ Cúal es su posición dentro del grupo familiar ?</label></td>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td align="right">
                                            <?php
                                            $posFam = datUserxEmp($wuse,$conex,30);
                                            $posFamAct = $posFam;
                                            if($posFam == '1'){$posFam = 'PROVEEDOR PRINCIPAL DE RECURSOS ECONÓMICOS';}
                                            if($posFam == '2'){$posFam = 'COMPARTE CON SU CONYUGUE O PAREJA LAS RESPONSABILIDADES ECONOMICAS';}
                                            if($posFam == '3'){$posFam = 'CONTRIBUYE CON LOS GASTOS FAMILIARES';}
                                            if($posFam == '4'){$posFam = 'DEPENDIENTE ECONOMICAMENTE DE OTRO MIEMBRO DE LA FAMILIA';}
                                            if($posFam == '5'){$posFam = 'OTRO';}
                                            ?>
                                            <select id="posFam" name="posFam" class="form-control form-sm inpEst" style="width: 774px">
                                                <option value="1">PROVEEDOR PRINCIPAL DE RECURSOS ECONÓMICOS</option>
                                                <option value="2">COMPARTE CON SU CONYUGUE O PAREJA LAS RESPONSABILIDADES ECONOMICAS</option>
                                                <option value="3">CONTRIBUYE CON LOS GASTOS FAMILIARES</option>
                                                <option value="4">DEPENDIENTE ECONOMICAMENTE DE OTRO MIEMBRO DE LA FAMILIA</option>
                                                <option value="5">OTRO</option>
                                                <?php
                                                if($posFam != null){?><option selected value="<?php echo $posFamAct ?>"><?php echo $posFam ?></option><?php }
                                                else{?><option selected disabled>Seleccione...</option><?php }
                                                ?>
                                            </select>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                                <!-- //////////////////// QUIEN QUEDA AL CUIDADO DE LOS HIJOS: ////////////////////////// -->

                                <section>
                                    <?php
                                    $usuCuHij = datUserxEmp($wuse,$conex,31);    $datoCuh = explode(",",$usuCuHij);
                                    $varCUH1 = $datoCuh[0];     $varCUH2 = $datoCuh[1];     $varCUH3 = $datoCuh[2];     $varCUH4 = $datoCuh[3];
                                    $varCUH5 = $datoCuh[4];     $varCUH6 = $datoCuh[5];     $varCUH7 = $datoCuh[6];     $varCUH8 = $datoCuh[7];
                                    ?>
                                </section>

                                <table class="tblNivE" style="margin-top: 20px; width: 70%; margin-bottom: 10px">
                                    <tr>
                                        <td class="tdTblTrans" colspan="2" align="center">
                                            <label>&ensp; ¿ Quién queda al cuidado de tus hijos durante tu ausencia ?</label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 300px">
                                            <label for="chAbuNi" class="container16">Abuelos de los niños
                                                <?php
                                                if($varCUH1 == 'on'){?><input type="checkbox" id="chAbuNi" name="chAbuNi" checked="checked" value="on"
                                                                              onclick="contOp('chAbuNi'); sumActi2('chAbuNi')"><?php }
                                                else{?><input type="checkbox" id="chAbuNi" name="chAbuNi" value="off" onclick="contOp('chAbuNi'); sumActi2('chAbuNi')"><?php }
                                                ?>
                                                <span class="checkmark16"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label for="chPaMad" class="container17">Padre o madre de los niños
                                                <?php
                                                if($varCUH2 == 'on'){?><input type="checkbox" id="chPaMad" name="chPaMad" checked="checked" value="on"
                                                                              onclick="contOp('chPaMad'); sumActi2('chPaMad')"><?php }
                                                else{?><input type="checkbox" id="chPaMad" name="chPaMad" value="off" onclick="contOp('chPaMad'); sumActi2('chPaMad')"><?php }
                                                ?>
                                                <span class="checkmark17"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="chVecin" class="container16">Vecinos
                                                <?php
                                                if($varCUH3 == 'on'){?><input type="checkbox" id="chVecin" name="chVecin" checked="checked" value="on"
                                                                              onclick="contOp('chVecin'); sumActi2('chVecin')"><?php }
                                                else{?><input type="checkbox" id="chVecin" name="chVecin" value="off" onclick="contOp('chVecin'); sumActi2('chVecin')"><?php }
                                                ?>
                                                <span class="checkmark16"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label for="chGuIns" class="container17">Guardería o institución educativa
                                                <?php
                                                if($varCUH4 == 'on'){?><input type="checkbox" id="chGuIns" name="chGuIns" checked="checked" value="on"
                                                                              onclick="contOp('chGuIns'); sumActi2('chGuIns')"><?php }
                                                else{?><input type="checkbox" id="chGuIns" name="chGuIns" value="off" onclick="contOp('chGuIns'); sumActi2('chGuIns')"><?php }
                                                ?>
                                                <span class="checkmark17"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="chEmDom" class="container16">Empleada domestica
                                                <?php
                                                if($varCUH5 == 'on'){?><input type="checkbox" id="chEmDom" name="chEmDom" checked="checked" value="on"
                                                                              onclick="contOp('chEmDom'); sumActi2('chEmDom')"><?php }
                                                else{?><input type="checkbox" id="chEmDom" name="chEmDom" value="off" onclick="contOp('chEmDom'); sumActi2('chEmDom')"><?php }
                                                ?>
                                                <span class="checkmark16"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label for="chUnFam" class="container17">Un familiar
                                                <?php
                                                if($varCUH6 == 'on'){?><input type="checkbox" id="chUnFam" name="chUnFam" checked="checked" value="on"
                                                                              onclick="contOp('chUnFam'); sumActi2('chUnFam')"><?php }
                                                else{?><input type="checkbox" id="chUnFam" name="chUnFam" value="off" onclick="contOp('chUnFam'); sumActi2('chUnFam')"><?php }
                                                ?>
                                                <span class="checkmark17"></span>
                                            </label>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <label for="chQuSol" class="container16">Se queda solo
                                                <?php
                                                if($varCUH7 == 'on'){?><input type="checkbox" id="chQuSol" name="chQuSol" checked="checked" value="on"
                                                                              onclick="contOp('chQuSol'); sumActi2('chQuSol')"><?php }
                                                else{?><input type="checkbox" id="chQuSol" name="chQuSol" value="off" onclick="contOp('chQuSol'); sumActi2('chQuSol')"><?php }
                                                ?>
                                                <span class="checkmark16"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <label for="chCuOtr" class="container17">Otro
                                                <?php
                                                if($varCUH8 == 'on'){?><input type="checkbox" id="chCuOtr" name="chCuOtr" checked="checked" value="on"
                                                                              onclick="contOp('chCuOtr'); sumActi2('chCuOtr')"><?php }
                                                else{?><input type="checkbox" id="chCuOtr" name="chCuOtr" value="off" onclick="contOp('chCuOtr'); sumActi2('chCuOtr')"><?php }
                                                ?>
                                                <span class="checkmark17"></span>
                                            </label>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!------------------------ HABITAT Y CONSTRUCCION DE PATRIMONIO: ------------------------->

                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title tabsGen">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapse4">HABITAT Y CONSTRUCCION DE PATRIMONIO</a>
                            </h4>
                        </div>
                        <?php
                        if($pestana == '4'){?><div id="collapse4" class="panel-collapse collapse in"><?php }
                            else{?><div id="collapse4" class="panel-collapse collapse"><?php } ?>
                                <div class="panel-body" align="center">

                                    <!-- //////////////////// VIVIENDA: ////////////////////////// -->

                                    <h4 class="labelTitulo">Vivienda</h4>

                                    <section>
                                        <?php
                                        $codTenViv = datosCondVida($userTal,$conex,$tbInfoEmpleado,1);  $nomTenViv = datosVivienda($codTenViv,$conex,1);
                                        $codTipViv = datosCondVida($userTal,$conex,$tbInfoEmpleado,2);  $nomTipoViv = datosVivienda($codTipViv,$conex,2);
                                        $tienTerr = datosCondVida($userTal,$conex,$tbInfoEmpleado,3);   $tienLote = datosCondVida($userTal,$conex,$tbInfoEmpleado,4);
                                        $estadoViv = datosCondVida($userTal,$conex,$tbInfoEmpleado,5);  $nomEstViv = datosVivienda($estadoViv,$conex,3);
                                        $estadoSerp = datosCondVida($userTal,$conex,$tbInfoEmpleado,6); $codTransp = datosCondVida($userTal,$conex,$tbInfoEmpleado,7);
                                        $otroTrans = datosCondVida($userTal,$conex,$tbInfoEmpleado,8);  $otrosAlim = datosCondVida($userTal,$conex,$tbInfoEmpleado,10);
                                        $credito = datosCondVida($userTal,$conex,$tbInfoEmpleado,11);

                                        //separar los codigos de los servicios publicos del usuario (talhuma_24):
                                        $piecSer = explode(",",$estadoSerp);
                                        $var1 = $piecSer[0];    $var2 = $piecSer[1];    $var3 = $piecSer[2];    $var4 = $piecSer[3];
                                        $var5 = $piecSer[4];    $var6 = $piecSer[5];    $var7 = $piecSer[6];

                                        $datoSer = datosServicios($var1);
                                        if($datoSer == 'energia'){$energia = 'on';}  if($datoSer == 'telefono'){$telef = 'on';}   if($datoSer == 'acueducto'){$acued = 'on';}
                                        if($datoSer == 'alcantar'){$alcant = 'on';}  if($datoSer == 'aseo'){$aseo = 'on';}        if($datoSer == 'gas'){$gas = 'on';}
                                        if($datoSer == 'internet'){$intern = 'on';}
                                        $datoSer = datosServicios($var2);
                                        if($datoSer == 'energia'){$energia = 'on';}  if($datoSer == 'telefono'){$telef = 'on';}   if($datoSer == 'acueducto'){$acued = 'on';}
                                        if($datoSer == 'alcantar'){$alcant = 'on';}  if($datoSer == 'aseo'){$aseo = 'on';}        if($datoSer == 'gas'){$gas = 'on';}
                                        if($datoSer == 'internet'){$intern = 'on';}
                                        $datoSer = datosServicios($var3);
                                        if($datoSer == 'energia'){$energia = 'on';}  if($datoSer == 'telefono'){$telef = 'on';}   if($datoSer == 'acueducto'){$acued = 'on';}
                                        if($datoSer == 'alcantar'){$alcant = 'on';}  if($datoSer == 'aseo'){$aseo = 'on';}        if($datoSer == 'gas'){$gas = 'on';}
                                        if($datoSer == 'internet'){$intern = 'on';}
                                        $datoSer = datosServicios($var4);
                                        if($datoSer == 'energia'){$energia = 'on';}  if($datoSer == 'telefono'){$telef = 'on';}   if($datoSer == 'acueducto'){$acued = 'on';}
                                        if($datoSer == 'alcantar'){$alcant = 'on';}  if($datoSer == 'aseo'){$aseo = 'on';}        if($datoSer == 'gas'){$gas = 'on';}
                                        if($datoSer == 'internet'){$intern = 'on';}
                                        $datoSer = datosServicios($var5);
                                        if($datoSer == 'energia'){$energia = 'on';}  if($datoSer == 'telefono'){$telef = 'on';}   if($datoSer == 'acueducto'){$acued = 'on';}
                                        if($datoSer == 'alcantar'){$alcant = 'on';}  if($datoSer == 'aseo'){$aseo = 'on';}        if($datoSer == 'gas'){$gas = 'on';}
                                        if($datoSer == 'internet'){$intern = 'on';}
                                        $datoSer = datosServicios($var6);
                                        if($datoSer == 'energia'){$energia = 'on';}  if($datoSer == 'telefono'){$telef = 'on';}   if($datoSer == 'acueducto'){$acued = 'on';}
                                        if($datoSer == 'alcantar'){$alcant = 'on';}  if($datoSer == 'aseo'){$aseo = 'on';}        if($datoSer == 'gas'){$gas = 'on';}
                                        if($datoSer == 'internet'){$intern = 'on';}
                                        $datoSer = datosServicios($var7);
                                        if($datoSer == 'energia'){$energia = 'on';}  if($datoSer == 'telefono'){$telef = 'on';}   if($datoSer == 'acueducto'){$acued = 'on';}
                                        if($datoSer == 'alcantar'){$alcant = 'on';}  if($datoSer == 'aseo'){$aseo = 'on';}        if($datoSer == 'gas'){$gas = 'on';}
                                        if($datoSer == 'internet'){$intern = 'on';}

                                        //separar los codigos de los transportes del usuario (talhuma_24):
                                        $piecTrans = explode(",",$codTransp);
                                        $varT1 = $piecTrans[0];   $varT2 = $piecTrans[1];   $varT3 = $piecTrans[2]; $varT4 = $piecTrans[3];
                                        $varT5 = $piecTrans[4];   $varT6 = $piecTrans[5];   $varT7 = $piecTrans[6]; $varT8 = $piecTrans[7]; $varT9 = $piecTrans[8];

                                        $datoTrans = datosTrasporte($varT1);
                                        if($datoTrans == 'bus'){$bus = 'on';}   if($datoTrans == 'metro'){$metro = 'on';}   if($datoTrans == 'particular'){$parti = 'on';}
                                        if($datoTrans == 'moto'){$moto = 'on';} if($datoTrans == 'taxi'){$taxi = 'on';}     if($datoTrans == 'contratado'){$contra = 'on';}
                                        if($datoTrans == 'bici'){$bici = 'on';} if($datoTrans == 'camina'){$camina = 'on';} if($datoTrans == 'otro'){$otro = 'on';}
                                        $datoTrans = datosTrasporte($varT2);
                                        if($datoTrans == 'bus'){$bus = 'on';}   if($datoTrans == 'metro'){$metro = 'on';}   if($datoTrans == 'particular'){$parti = 'on';}
                                        if($datoTrans == 'moto'){$moto = 'on';} if($datoTrans == 'taxi'){$taxi = 'on';}     if($datoTrans == 'contratado'){$contra = 'on';}
                                        if($datoTrans == 'bici'){$bici = 'on';} if($datoTrans == 'camina'){$camina = 'on';} if($datoTrans == 'otro'){$otro = 'on';}
                                        $datoTrans = datosTrasporte($varT3);
                                        if($datoTrans == 'bus'){$bus = 'on';}   if($datoTrans == 'metro'){$metro = 'on';}   if($datoTrans == 'particular'){$parti = 'on';}
                                        if($datoTrans == 'moto'){$moto = 'on';} if($datoTrans == 'taxi'){$taxi = 'on';}     if($datoTrans == 'contratado'){$contra = 'on';}
                                        if($datoTrans == 'bici'){$bici = 'on';} if($datoTrans == 'camina'){$camina = 'on';} if($datoTrans == 'otro'){$otro = 'on';}
                                        $datoTrans = datosTrasporte($varT4);
                                        if($datoTrans == 'bus'){$bus = 'on';}   if($datoTrans == 'metro'){$metro = 'on';}   if($datoTrans == 'particular'){$parti = 'on';}
                                        if($datoTrans == 'moto'){$moto = 'on';} if($datoTrans == 'taxi'){$taxi = 'on';}     if($datoTrans == 'contratado'){$contra = 'on';}
                                        if($datoTrans == 'bici'){$bici = 'on';} if($datoTrans == 'camina'){$camina = 'on';} if($datoTrans == 'otro'){$otro = 'on';}
                                        $datoTrans = datosTrasporte($varT5);
                                        if($datoTrans == 'bus'){$bus = 'on';}   if($datoTrans == 'metro'){$metro = 'on';}   if($datoTrans == 'particular'){$parti = 'on';}
                                        if($datoTrans == 'moto'){$moto = 'on';} if($datoTrans == 'taxi'){$taxi = 'on';}     if($datoTrans == 'contratado'){$contra = 'on';}
                                        if($datoTrans == 'bici'){$bici = 'on';} if($datoTrans == 'camina'){$camina = 'on';} if($datoTrans == 'otro'){$otro = 'on';}
                                        $datoTrans = datosTrasporte($varT6);
                                        if($datoTrans == 'bus'){$bus = 'on';}   if($datoTrans == 'metro'){$metro = 'on';}   if($datoTrans == 'particular'){$parti = 'on';}
                                        if($datoTrans == 'moto'){$moto = 'on';} if($datoTrans == 'taxi'){$taxi = 'on';}     if($datoTrans == 'contratado'){$contra = 'on';}
                                        if($datoTrans == 'bici'){$bici = 'on';} if($datoTrans == 'camina'){$camina = 'on';} if($datoTrans == 'otro'){$otro = 'on';}
                                        $datoTrans = datosTrasporte($varT7);
                                        if($datoTrans == 'bus'){$bus = 'on';}   if($datoTrans == 'metro'){$metro = 'on';}   if($datoTrans == 'particular'){$parti = 'on';}
                                        if($datoTrans == 'moto'){$moto = 'on';} if($datoTrans == 'taxi'){$taxi = 'on';}     if($datoTrans == 'contratado'){$contra = 'on';}
                                        if($datoTrans == 'bici'){$bici = 'on';} if($datoTrans == 'camina'){$camina = 'on';} if($datoTrans == 'otro'){$otro = 'on';}
                                        $datoTrans = datosTrasporte($varT8);
                                        if($datoTrans == 'bus'){$bus = 'on';}   if($datoTrans == 'metro'){$metro = 'on';}   if($datoTrans == 'particular'){$parti = 'on';}
                                        if($datoTrans == 'moto'){$moto = 'on';} if($datoTrans == 'taxi'){$taxi = 'on';}     if($datoTrans == 'contratado'){$contra = 'on';}
                                        if($datoTrans == 'bici'){$bici = 'on';} if($datoTrans == 'camina'){$camina = 'on';} if($datoTrans == 'otro'){$otro = 'on';}
                                        $datoTrans = datosTrasporte($varT9);
                                        if($datoTrans == 'bus'){$bus = 'on';}   if($datoTrans == 'metro'){$metro = 'on';}   if($datoTrans == 'particular'){$parti = 'on';}
                                        if($datoTrans == 'moto'){$moto = 'on';} if($datoTrans == 'taxi'){$taxi = 'on';}     if($datoTrans == 'contratado'){$contra = 'on';}
                                        if($datoTrans == 'bici'){$bici = 'on';} if($datoTrans == 'camina'){$camina = 'on';} if($datoTrans == 'otro'){$otro = 'on';}

                                        $tienTerr = trim($tienTerr);    $tienLote = trim($tienLote);
                                        ?>
                                    </section>

                                    <table style="width: 100%; margin-bottom: 10px" align="center">
                                        <tr align="center">
                                            <td>
                                                <table class="tblNivE" style="width: 90%; margin-bottom: 10px; border-right: hidden">
                                                    <tr>
                                                        <td class="tdTblVivi">&ensp;<label for="tenVivi">Tenencia de vivenda</label></td>
                                                        <td>
                                                            <select id="tenVivi" name="tenVivi" class="form-control form-sm">
                                                                <?php
                                                                $queryTenViv = "select Tencod,Tendes from root_000068 WHERE Tenest = 'on'";
                                                                $commTenViv = mysql_query($queryTenViv, $conex) or die (mysql_errno()." - en el query: ".$queryTenViv." - ".mysql_error());
                                                                while($datoTenViv = mysql_fetch_assoc($commTenViv))
                                                                {
                                                                    $codTvivi = $datoTenViv['Tencod'];  $desTvivi = $datoTenViv['Tendes'];
                                                                    $tenVivienda = $codTvivi.'-'.$desTvivi;
                                                                    ?>
                                                                    <option><?php echo $tenVivienda ?></option>
                                                                    <?php
                                                                }
                                                                if($nomTenViv != null){?><option selected><?php echo $nomTenViv ?></option><?php }
                                                                else{?><option selected disabled>Seleccione...</option><?php }
                                                                ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="tdTblVivi">&ensp;<label for="tipVivi">Tipo de vivienda</label></td>
                                                        <td>
                                                            <select id="tipVivi" name="tipVivi" class="form-control form-sm">
                                                                <?php
                                                                $queryTipViv = "select Tpvcod,Tpvdes from root_000069 WHERE Tpvest = 'on'";
                                                                $commTipViv = mysql_query($queryTipViv, $conex) or die (mysql_errno()." - en el query: ".$queryTipViv." - ".mysql_error());
                                                                while($datoTipViv = mysql_fetch_assoc($commTipViv))
                                                                {
                                                                    $codTViv = $datoTipViv['Tpvcod']; $desTviv = $datoTipViv['Tpvdes'];
                                                                    $tipoVivienda = $codTViv.'-' .$desTviv;
                                                                    ?>
                                                                    <option><?php echo $tipoVivienda ?></option>
                                                                    <?php
                                                                }
                                                                if($nomTipoViv != null){?><option selected><?php echo $nomTipoViv ?></option><?php }
                                                                else{?><option selected disabled>Seleccione...</option><?php }
                                                                ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="tdTblVivi">&ensp;<label for="tienTerr">Tiene usted terraza propia</label></td>
                                                        <td>
                                                            <select id="tienTerr" name="tienTerr" class="form-control form-sm">
                                                                <option>SI</option>
                                                                <option>NO</option>
                                                                <?php
                                                                if($tienTerr == 'on'){?><option selected>SI</option><?php }
                                                                elseif($tienTerr == 'off'){?><option selected>NO</option><?php }
                                                                else{?><option selected>Seleccione...</option><?php }
                                                                ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="tdTblVivi">&ensp;<label for="tienLote">Tiene usted lote propio</label></td>
                                                        <td>
                                                            <select id="tienLote" name="tienLote" class="form-control form-sm">
                                                                <option>SI</option>
                                                                <option>NO</option>
                                                                <?php
                                                                if($tienLote == 'on'){?><option selected>SI</option><?php }
                                                                elseif($tienLote == 'off'){?><option selected>NO</option><?php }
                                                                else{?><option selected>Seleccione...</option><?php }
                                                                ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td class="tdTblVivi">&ensp;<label for="estVivi">Estado de la vivienda</label></td>
                                                        <td>
                                                            <select id="estVivi" name="estVivi" class="form-control form-sm">
                                                                <?php
                                                                $queryEstViv = "select Esvcod,Esvdes from root_000070 WHERE Esvest = 'on'";
                                                                $commEstViv = mysql_query($queryEstViv, $conex) or die (mysql_errno()." - en el query: ".$queryEstViv." - ".mysql_error());
                                                                while($datoEstViv = mysql_fetch_assoc($commEstViv))
                                                                {
                                                                    $codEViv = $datoEstViv['Esvcod']; $desEviv = $datoEstViv['Esvdes'];
                                                                    $estadoVivienda = $codEViv.'-' .$desEviv;
                                                                    ?>
                                                                    <option><?php echo $estadoVivienda ?></option>
                                                                    <?php
                                                                }
                                                                if($nomEstViv != null){?><option selected><?php echo $nomEstViv ?></option><?php }
                                                                else{?><option selected disabled>Seleccione...</option><?php }
                                                                ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td>
                                                <table class="tblNivE" style="width: 90%; margin-bottom: 10px">
                                                    <tr>
                                                        <td class="tdTblVivi" rowspan="7">&ensp;<label>Acceso a servicios públicos</label></td>
                                                        <td>
                                                            <label for="chAcue" class="container3">Acueducto
                                                                <?php
                                                                if($acued != null){?><input type="checkbox" id="chAcue" name="chAcue" checked="checked" value="on" onclick="this.value = 'off'"><?php }
                                                                else{?><input type="checkbox" id="chAcue" name="chAcue" value="off" onclick="this.value = 'on'"><?php }
                                                                ?>
                                                                <span class="checkmark3"></span>
                                                            </label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label for="chAlca" class="container3">Alcantarillado
                                                                <?php
                                                                if($alcant != null){?><input type="checkbox" id="chAlca" name="chAlca" checked="checked" value="on" onclick="this.value = 'off'"><?php }
                                                                else{?><input type="checkbox" id="chAlca" name="chAlca" value="off" onclick="this.value = 'on'" ><?php }
                                                                ?>
                                                                <span class="checkmark3"></span>
                                                            </label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label for="chAseo" class="container3">Aseo
                                                                <?php
                                                                if($aseo != null){?><input type="checkbox" id="chAseo" name="chAseo" checked="checked" value="on" onclick="this.value = 'off'"><?php }
                                                                else{?><input type="checkbox" id="chAseo" name="chAseo" value="off" onclick="this.value = 'on'"><?php }
                                                                ?>
                                                                <span class="checkmark3"></span>
                                                            </label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label for="chEner" class="container3">Energia
                                                                <?php
                                                                if($energia != null){?><input type="checkbox" id="chEner" name="chEner" checked="checked" value="on" onclick="this.value = 'off'"><?php }
                                                                else{?><input type="checkbox" id="chEner" name="chEner" value="off" onclick="this.value = 'on'"><?php }
                                                                ?>
                                                                <span class="checkmark3"></span>
                                                            </label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label for="chInter" class="container3">Internet
                                                                <?php
                                                                if($intern != null){?><input type="checkbox" id="chInter" name="chInter" checked="checked" value="on" onclick="this.value = 'off'"><?php }
                                                                else{?><input type="checkbox" id="chInter" name="chInter" value="off" onclick="this.value = 'on'"><?php }
                                                                ?>
                                                                <span class="checkmark3"></span>
                                                            </label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label for="chGas" class="container3">Red de gas
                                                                <?php
                                                                if($gas != null){?><input type="checkbox" id="chGas" name="chGas" checked="checked" value="on" onclick="this.value = 'off'"><?php }
                                                                else{?><input type="checkbox" id="chGas" name="chGas" value="off" onclick="this.value = 'on'"><?php }
                                                                ?>
                                                                <span class="checkmark3"></span>
                                                            </label>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <label for="chTele" class="container3">Telefono
                                                                <?php
                                                                if($telef != null){?><input type="checkbox" id="chTele" name="chTele" checked="checked" value="on" onclick="this.value = 'off'"><?php }
                                                                else{?><input type="checkbox" id="chTele" name="chTele" value="off" onclick="this.value = 'on'"><?php }
                                                                ?>
                                                                <span class="checkmark3"></span>
                                                            </label>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-------SUBSIDIO VIVIENDA: -------->

                                    <section>
                                        <?php
                                        $subVivi = datUserxEmp($wuse,$conex,32);    $ahoViv = datUserxEmp($wuse,$conex,33);
                                        $montoHa = datUserxEmp($wuse,$conex,34);    $Usfariesvi = datUserxEmp($wuse,$conex,35);
                                        $UsuMejoVi = datUserxEmp($wuse,$conex,36);
                                        $datoRISK = explode(",",$Usfariesvi);
                                        $varRISK1 = $datoRISK[0];   $varRISK2 = $datoRISK[1];   $varRISK3 = $datoRISK[2];   $varRISK4 = $datoRISK[3];
                                        $varRISK5 = $datoRISK[4];   $varRISK6 = $datoRISK[5];   $varRISK7 = $datoRISK[6];
                                        $datoMEJO = explode(",",$UsuMejoVi);
                                        $varMEJO1 = $datoMEJO[0];   $varMEJO2 = $datoMEJO[1];   $varMEJO3 = $datoMEJO[2];   $varMEJO4 = $datoMEJO[3];
                                        $varMEJO5 = $datoMEJO[4];   $varMEJO6 = $datoMEJO[5];   $varMEJO7 = $datoMEJO[6];   $varMEJO8 = $datoMEJO[7];
                                        $varMEJO9 = $datoMEJO[8];   $varMEJO10 = $datoMEJO[9];  $varMEJO11 = $datoMEJO[10]; $varMEJO12 = $datoMEJO[11];
                                        $varMEJO13 = $datoMEJO[12];
                                        ?>
                                    </section>

                                    <table class="tblNivE" style="width: 70%; margin-bottom: 20px">
                                        <thead>
                                        <tr align="center">
                                            <td>&ensp;<label for="subViv">¿ Ha sido beneficiado de algún tipo de subsidio de vivienda ?</label></td>
                                            <td align="right">
                                                <select id="subViv" name="subViv" class="form-control form-sm" style="width: 160px">
                                                    <option>SI</option>
                                                    <option>NO</option>
                                                    <?php
                                                    if($subVivi != null){?><option selected disabled><?php echo $subVivi ?></option><?php }
                                                    else{?><option selected disabled>Seleccione...</option><?php }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        </thead>
                                    </table>

                                    <!-- AHORRO PARA COMPRA DE VIVIENDA: -->

                                    <table class="tblNivE" style="width: 70%; margin-bottom: 20px">
                                        <thead>
                                        <tr align="center">
                                            <td>&ensp;<label for="ahoViv">¿ Ahorra para la compra de una vivienda propia ?</label></td>
                                            <td align="right">
                                                <select id="ahoViv" name="ahoViv" class="form-control form-sm" style="width: 160px">
                                                    <option>SI</option>
                                                    <option>NO</option>
                                                    <?php
                                                    if($ahoViv != null){?><option selected disabled><?php echo $ahoViv ?></option><?php }
                                                    else{?><option selected disabled>Seleccione...</option><?php }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        </thead>
                                    </table>

                                    <!-- AHORRO DISPONIBLE PARA COMPRA DE VIVIENDA: -->

                                    <table class="tblNivE" style="width: 50%; margin-bottom: 20px">
                                        <thead>
                                        <tr align="center">
                                            <td>&ensp;<label for="montoHa">¿ Cuánto ahorro tiene disponible para la compra de vivienda ?</label></td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td align="right">
                                                <select id="montoHa" name="montoHa" class="form-control form-sm" style="width: 770px">
                                                    <option>Menos de $100.000</option>
                                                    <option>Entre $100.000 y $1.000.000</option>
                                                    <option>Entre $1.000.000 y $3.000.000</option>
                                                    <option>Entre $3.000.000 y $5.000.000</option>
                                                    <option>Mas de $5.000.000</option>
                                                    <option>No ahorra</option>
                                                    <?php
                                                    if($montoHa != null){?><option selected disabled><?php echo $montoHa ?></option><?php }
                                                    else{?><option selected disabled>Seleccione...</option><?php }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <!-- FACTORES DE RIESGO DE LA VIVIENDA: -->

                                    <table class="tblNivE" style="width: 100%; margin-bottom: 20px">
                                        <tr>
                                            <td class="tdTblTrans" colspan="2" align="center">
                                                <label>&ensp;¿ Su vivienda presenta alguno de los siguientes factores de riesgo ?</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 350px">
                                                <label for="chInuVi" class="container18">Inundaciones
                                                    <?php
                                                    if($varRISK1 == 'on'){?><input type="checkbox" id="chInuVi" name="chInuVi" checked="checked" value="on"
                                                                                   onclick="contOp('chInuVi'); uncheck2('chNoFaVi')"><?php }
                                                    else{?><input type="checkbox" id="chInuVi" name="chInuVi" value="off" onclick="contOp('chInuVi'); uncheck2('chNoFaVi')"><?php }
                                                    ?>
                                                    <span class="checkmark18"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chContVi" class="container19">Contaminación (alta exposición a químicos, desechos de empresas, ect)
                                                    <?php
                                                    if($varRISK2 == 'on'){?><input type="checkbox" id="chContVi" name="chContVi" checked="checked" value="on"
                                                                                   onclick="contOp('chContVi'); uncheck2('chNoFaVi')"><?php }
                                                    else{?><input type="checkbox" id="chContVi" name="chContVi" value="off" onclick="contOp('chContVi'); uncheck2('chNoFaVi')"><?php }
                                                    ?>
                                                    <span class="checkmark19"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chRiAmVi" class="container18">Riesgos ambientales (deslizamientos de tierra)
                                                    <?php
                                                    if($varRISK3 == 'on'){?><input type="checkbox" id="chRiAmVi" name="chRiAmVi" checked="checked" value="on"
                                                                                   onclick="contOp('chRiAmVi'); uncheck2('chNoFaVi')"><?php }
                                                    else{?><input type="checkbox" id="chRiAmVi" name="chRiAmVi" value="off" onclick="contOp('chRiAmVi'); uncheck2('chNoFaVi')"><?php }
                                                    ?>
                                                    <span class="checkmark18"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chRiEsvi" class="container19">Riesgos estructurales (daños en la estructura de la vivienda)
                                                    <?php
                                                    if($varRISK4 == 'on'){?><input type="checkbox" id="chRiEsvi" name="chRiEsvi" checked="checked" value="on"
                                                                                   onclick="contOp('chRiEsvi'); uncheck2('chNoFaVi')"><?php }
                                                    else{?><input type="checkbox" id="chRiEsvi" name="chRiEsvi" value="off" onclick="contOp('chRiEsvi'); uncheck2('chNoFaVi')"><?php }
                                                    ?>
                                                    <span class="checkmark19"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chRiSaVi" class="container18">Riesgos sanitarios (condiciones inadecuadas de baños y cocinas)
                                                    <?php
                                                    if($varRISK5 == 'on'){?><input type="checkbox" id="chRiSaVi" name="chRiSaVi" checked="checked" value="on"
                                                                                   onclick="contOp('chRiSaVi'); uncheck2('chNoFaVi')"><?php }
                                                    else{?><input type="checkbox" id="chRiSaVi" name="chRiSaVi" value="off" onclick="contOp('chRiSaVi'); uncheck2('chNoFaVi')"><?php }
                                                    ?>
                                                    <span class="checkmark18"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chRiPuVi" class="container19">Riesgo público (condiciones de violencia u orden público)
                                                    <?php
                                                    if($varRISK6 == 'on'){?><input type="checkbox" id="chRiPuVi" name="chRiPuVi" checked="checked" value="on"
                                                                                   onclick="contOp('chRiPuVi'); uncheck2('chNoFaVi')"><?php }
                                                    else{?><input type="checkbox" id="chRiPuVi" name="chRiPuVi" value="off" onclick="contOp('chRiPuVi'); uncheck2('chNoFaVi')"><?php }
                                                    ?>
                                                    <span class="checkmark19"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chNoFaVi" class="container18">No tiene factores de riesgo
                                                    <?php
                                                    if($varRISK7 == 'on'){?><input type="checkbox" id="chNoFaVi" name="chNoFaVi" checked="checked" value="on"
                                                                                   onclick="contOp('chNoFaVi'); uncheck3()"><?php }
                                                    else{?><input type="checkbox" id="chNoFaVi" name="chNoFaVi" value="off" onclick="contOp('chNoFaVi'); uncheck3()"><?php }
                                                    ?>
                                                    <span class="checkmark18"></span>
                                                </label>
                                            </td>
                                            <td>

                                            </td>
                                        </tr>
                                    </table>

                                    <!-- NECESIDADES DE MEJORAMIENTO DE LA VIVIENDA: -->

                                    <table class="tblNivE" style="width: 70%; margin-bottom: 20px">
                                        <tr>
                                            <td class="tdTblTrans" colspan="2" align="center">
                                                <label>&ensp;¿ Cuáles son las necesidades de mejoramiento que identificas en tu vivienda ?</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 300px">
                                                <label for="chMeEst" class="container10">Estéticas (Embellecimiento de la vivienda)
                                                    <?php
                                                    if($varMEJO1 == 'on'){?><input type="checkbox" id="chMeEst" name="chMeEst" checked="checked" value="on"
                                                                                   onclick="contOp('chMeEst')"><?php }
                                                    else{?><input type="checkbox" id="chMeEst" name="chMeEst" value="off" onclick="contOp('chMeEst'); uncheck2('chMeNot')"><?php }
                                                    ?>
                                                    <span class="checkmark10"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chMeMue" class="container11">Muebles (Compra o reparación)
                                                    <?php
                                                    if($varMEJO2 == 'on'){?><input type="checkbox" id="chMeMue" name="chMeMue" checked="checked" value="on"
                                                                                   onclick="contOp('chMeMue')"><?php }
                                                    else{?><input type="checkbox" id="chMeMue" name="chMeMue" value="off" onclick="contOp('chMeMue'); uncheck2('chMeNot')"><?php }
                                                    ?>
                                                    <span class="checkmark11"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chMeEle" class="container10">Electrodomésticos (Compra o reparación)
                                                    <?php
                                                    if($varMEJO3 == 'on'){?><input type="checkbox" id="chMeEle" name="chMeEle" checked="checked" value="on"
                                                                                   onclick="contOp('chMeEle')"><?php }
                                                    else{?><input type="checkbox" id="chMeEle" name="chMeEle" value="off" onclick="contOp('chMeEle'); uncheck2('chMeNot')"><?php }
                                                    ?>
                                                    <span class="checkmark10"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chMePis" class="container11">Piso
                                                    <?php
                                                    if($varMEJO4 == 'on'){?><input type="checkbox" id="chMePis" name="chMePis" checked="checked" value="on"
                                                                                   onclick="contOp('chMePis')"><?php }
                                                    else{?><input type="checkbox" id="chMePis" name="chMePis" value="off" onclick="contOp('chMePis'); uncheck2('chMeNot')"><?php }
                                                    ?>
                                                    <span class="checkmark11"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chMePar" class="container10">Paredes
                                                    <?php
                                                    if($varMEJO5 == 'on'){?><input type="checkbox" id="chMePar" name="chMePar" checked="checked" value="on"
                                                                                   onclick="contOp('chMePar')"><?php }
                                                    else{?><input type="checkbox" id="chMePar" name="chMePar" value="off" onclick="contOp('chMePar'); uncheck2('chMeNot')"><?php }
                                                    ?>
                                                    <span class="checkmark10"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chMeCol" class="container11">Columnas
                                                    <?php
                                                    if($varMEJO6 == 'on'){?><input type="checkbox" id="chMeCol" name="chMeCol" checked="checked" value="on"
                                                                                   onclick="contOp('chMeCol')"><?php }
                                                    else{?><input type="checkbox" id="chMeCol" name="chMeCol" value="off" onclick="contOp('chMeCol'); uncheck2('chMeNot')"><?php }
                                                    ?>
                                                    <span class="checkmark11"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chMeHum" class="container10">Humedades
                                                    <?php
                                                    if($varMEJO7 == 'on'){?><input type="checkbox" id="chMeHum" name="chMeHum" checked="checked" value="on"
                                                                                   onclick="contOp('chMeHum')"><?php }
                                                    else{?><input type="checkbox" id="chMeHum" name="chMeHum" value="off" onclick="contOp('chMeHum'); uncheck2('chMeNot')"><?php }
                                                    ?>
                                                    <span class="checkmark10"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chMeFac" class="container11">Fachada
                                                    <?php
                                                    if($varMEJO8 == 'on'){?><input type="checkbox" id="chMeFac" name="chMeFac" checked="checked" value="on"
                                                                                   onclick="contOp('chMeFac')"><?php }
                                                    else{?><input type="checkbox" id="chMeFac" name="chMeFac" value="off" onclick="contOp('chMeFac'); uncheck2('chMeNot')"><?php }
                                                    ?>
                                                    <span class="checkmark11"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chMeTec" class="container10">Techo
                                                    <?php
                                                    if($varMEJO9 == 'on'){?><input type="checkbox" id="chMeTec" name="chMeTec" checked="checked" value="on"
                                                                                   onclick="contOp('chMeTec')"><?php }
                                                    else{?><input type="checkbox" id="chMeTec" name="chMeTec" value="off" onclick="contOp('chMeTec'); uncheck2('chMeNot')"><?php }
                                                    ?>
                                                    <span class="checkmark10"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chMeBan" class="container11">Baños
                                                    <?php
                                                    if($varMEJO10 == 'on'){?><input type="checkbox" id="chMeBan" name="chMeBan" checked="checked" value="on"
                                                                                    onclick="contOp('chMeBan')"><?php }
                                                    else{?><input type="checkbox" id="chMeBan" name="chMeBan" value="off" onclick="contOp('chMeBan'); uncheck2('chMeNot')"><?php }
                                                    ?>
                                                    <span class="checkmark11"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chMeCoc" class="container10">Cocina
                                                    <?php
                                                    if($varMEJO11 == 'on'){?><input type="checkbox" id="chMeCoc" name="chMeCoc" checked="checked" value="on"
                                                                                    onclick="contOp('chMeCoc')"><?php }
                                                    else{?><input type="checkbox" id="chMeCoc" name="chMeCoc" value="off" onclick="contOp('chMeCoc'); uncheck2('chMeNot')"><?php }
                                                    ?>
                                                    <span class="checkmark10"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chMeAmp" class="container11">Ampliación
                                                    <?php
                                                    if($varMEJO12 == 'on'){?><input type="checkbox" id="chMeAmp" name="chMeAmp" checked="checked" value="on"
                                                                                    onclick="contOp('chMeAmp')"><?php }
                                                    else{?><input type="checkbox" id="chMeAmp" name="chMeAmp" value="off" onclick="contOp('chMeAmp'); uncheck2('chMeNot')"><?php }
                                                    ?>
                                                    <span class="checkmark11"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chMeNot" class="container10">No tiene necesidades
                                                    <?php
                                                    if($varMEJO13 == 'on'){?><input type="checkbox" id="chMeNot" name="chMeNot" checked="checked" value="on"
                                                                                    onclick="contOp('chMeNot'); uncheck1()"><?php }
                                                    else{?><input type="checkbox" id="chMeNot" name="chMeNot" value="off" onclick="contOp('chMeNot'); uncheck1()"><?php }
                                                    ?>
                                                    <span class="checkmark10"></span>
                                                </label>
                                            </td>
                                            <td>

                                            </td>
                                        </tr>
                                    </table>


                                    <!-- //////////////////// CREDITOS: ////////////////////////// -->
                                    <section>
                                        <?php
                                        $cremot = datosCreditos($userTal,$conex,$tbInfoEmpleado,1); $creent = datosCreditos($userTal,$conex,$tbInfoEmpleado,2);
                                        $creval = datosCreditos($userTal,$conex,$tbInfoEmpleado,3); $crecuo = datosCreditos($userTal,$conex,$tbInfoEmpleado,4);
                                        $idCred = datosCreditos($userTal,$conex,$tbInfoEmpleado,5);
                                        $UsProFi = datUserxEmp($wuse,$conex,37);    $datoPROFI = explode(",",$UsProFi);
                                        $varPROFI1 = $datoPROFI[0];     $varPROFI2 = $datoPROFI[1];     $varPROFI3 = $datoPROFI[2];     $varPROFI4 = $datoPROFI[3];
                                        $varPROFI5 = $datoPROFI[4];     $varPROFI6 = $datoPROFI[5];     $varPROFI7 = $datoPROFI[6];     $varPROFI8 = $datoPROFI[7];
                                        $varPROFI9 = $datoPROFI[8];
                                        $UsMoCre = datUserxEmp($wuse,$conex,38);    $datoMOCRE = explode(",",$UsMoCre);
                                        $varMOCRE1 = $datoMOCRE[0];     $varMOCRE2 = $datoMOCRE[1];     $varMOCRE3 = $datoMOCRE[2];     $varMOCRE4 = $datoMOCRE[3];
                                        $varMOCRE5 = $datoMOCRE[4];     $varMOCRE6 = $datoMOCRE[5];     $varMOCRE7 = $datoMOCRE[6];     $varMOCRE8 = $datoMOCRE[7];
                                        $varMOCRE9 = $datoMOCRE[8];     $varMOCRE10 = $datoMOCRE[9];    $varMOCRE11 = $datoMOCRE[10];   $varMOCRE12 = $datoMOCRE[11];
                                        $varMOCRE13 = $datoMOCRE[12];   $varMOCRE14 = $datoMOCRE[13];   $varMOCRE15 = $datoMOCRE[14];
                                        $UsPeAcu = datUserxEmp($wuse,$conex,39);    $datoPEACU = explode(",",$UsPeAcu);
                                        $varPEACU1 = $datoPEACU[0];     $varPEACU2 = $datoPEACU[1];     $varPEACU3 = $datoPEACU[2];     $varPEACU4 = $datoPEACU[3];
                                        $varPEACU5 = $datoPEACU[4];     $varPEACU6 = $datoPEACU[5];     $varPEACU7 = $datoPEACU[6];     $varPEACU8 = $datoPEACU[7];
                                        $varPEACU9 = $datoPEACU[8];     $varPEACU10 = $datoPEACU[9];    $varPEACU11 = $datoPEACU[10];
                                        $UsLcInt = datUserxEmp($wuse,$conex,40);    $datoLICRE = explode(",",$UsLcInt);
                                        $varLICRE1 = $datoLICRE[0];     $varLICRE2 = $datoLICRE[1];     $varLICRE3 = $datoLICRE[2];     $varLICRE4 = $datoLICRE[3];
                                        $varLICRE5 = $datoLICRE[4];     $varLICRE6 = $datoLICRE[5];     $varLICRE7 = $datoLICRE[6];     $varLICRE8 = $datoLICRE[7];
                                        $varLICRE9 = $datoLICRE[8];     $varLICRE10 = $datoLICRE[9];    $varLICRE11 = $datoLICRE[10];   $varLICRE12 = $datoLICRE[11];
                                        $varLICRE13 = $datoLICRE[12];
                                        $UsIaAho = datUserxEmp($wuse,$conex,41);    $datoIAAHO = explode(",",$UsIaAho);
                                        $varIAAHO1 = $datoIAAHO[0];     $varIAAHO2 = $datoIAAHO[1];     $varIAAHO3 = $datoIAAHO[2];     $varIAAHO4 = $datoIAAHO[3];
                                        $varIAAHO5 = $datoIAAHO[4];     $varIAAHO6 = $datoIAAHO[5];     $varIAAHO7 = $datoIAAHO[6];     $varIAAHO8 = $datoIAAHO[7];
                                        $varIAAHO9 = $datoIAAHO[8];
                                        ?>
                                    </section>

                                    <h4 class="labelTitulo">Créditos</h4>

                                    <table class="tblNivE" style="margin-top: 10px; width: 70%; margin-bottom: 10px">
                                        <thead>
                                        <tr align="center">
                                            <td>&ensp;<label for="credito">Actualmente tiene usted algún crédito</label></td>
                                            <td align="right">
                                                <select id="credito" name="credito" class="form-control form-sm" style="width: 160px">
                                                    <option>SI</option>
                                                    <option>NO</option>
                                                    <?php
                                                    if($credito == 'off'){$credito = 'NO';} else{$credito = 'SI';}
                                                    if($credito != null){?><option selected disabled><?php echo $credito ?></option><?php }
                                                    else{?><option selected disabled>Seleccione...</option><?php }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        </thead>
                                    </table>

                                    <table class="tblNivE" style="margin-top: 10px; width: 90%; margin-bottom: 20px">
                                        <thead>
                                        <tr align="center">
                                            <td colspan="5">
                                                <label>Relacione los datos de créditos que tenga actualmente: </label>
                                            </td>
                                        </tr>
                                        <tr align="center">
                                            <td align="center">
                                                <a href="#" id="add" class="btn btn-info btn-xs" title="Actualizar" onclick="location.reload()">
                                                    <span class="glyphicon glyphicon-refresh"></span>
                                                </a>
                                            </td>
                                            <td><label for="cremot">MOTIVO</label></td>
                                            <td><label for="creent">ENTIDAD Y/U OTRO</label></td>
                                            <td><label for="creval">VALOR TOTAL DEL CREDITO</label></td>
                                            <td><label for="crecuo">CUOTA MENSUAL</label></td>
                                            <td></td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $queryCreditos = "select * from".' '."$tbInfoEmpleado"."_000025 WHERE Creuse = '$userTal'";
                                        $commCreditos = mysql_query($queryCreditos, $conex) or die (mysql_errno()." - en el query: ".$queryCreditos." - ".mysql_error());
                                        while($datoCredito = mysql_fetch_array($commCreditos))
                                        {
                                            $cremot = $datoCredito['Cremot'];   $creent = $datoCredito['Creent'];   $creval = $datoCredito['Creval'];
                                            $crecuo = $datoCredito['Crecuo'];   $idCred = $datoCredito['id'];
                                            ?>
                                            <tr align="center" class="alternar">
                                                <td></td>
                                                <td>&ensp;<label><?php echo $cremot ?></label></td>
                                                <td><label><?php echo $creent ?></label></td>
                                                <td><label><?php echo $creval ?></label></td>
                                                <td><label><?php echo $crecuo ?></label></td>
                                                <td>
                                                    <input type="hidden" id="funcion10" name="funcion10" value="supCredito">
                                                    <a href="#" id="less" class="btn btn-info btn-xs" title="Eliminar"
                                                       onclick="openOps2(funcion10,tablaMtx,userMtx,<?php echo $idCred ?>,tenVivi,tipVivi,tienTerr,tienLote,estVivi,chAcue,
                                                           chAlca,chAseo,chEner,chInter,chGas,chTele,credito,chBici,chBus,chCamina,chPart,chMetro,chMoto,chOtroT,chTaxi,
                                                           chContra,otroTrans,chTrae,chComBoca,chComOtros,chCasa,chOtrosAl,otroTalmu,hobbie,lugparq,locker,chAudit,chComite,
                                                           chBrigada,chOtroRol,cualRol,Idefnc,Idegen,Ideced,Idepas,Idevis,estcivUser,Idedir,lunaUser,estUser,munUser,barUser,
                                                           telUser,celUser,corUser,tisanUser,extUser,chCoCon,chGesIn,chCoDoc,chGesTe,chCoInv,chHisTe,chCoCom,chInfInt,chCopas,
                                                           chMedTra,chCrede,chMeCon,chEtiIn,chMoSe,chEtiHo,chSePac,chEvCal,chTransp,chFarTe,chViEpi,chGesAm,contEmer,Ideraz,
                                                           timeDesp,turnEmp,chTorBol,chTorPla,chTorVol,chTorBal,chTorTen,chCamin,chBaile,chYoga,chEnPare,chCiclo,chMara,chTarHob,
                                                           chGruTea,chArtPla,chManual,chGastro,chClaIng,chConPle,chTarPic,chOtrAct,diaActi,horActi,actExtra,otraExtra,ranSal,
                                                           telEmer,chViArr,chViPag,chAlime,chSerPu,chTrpte,chEdPro,chEdHij,chPaCre,chReTli,chVestu,
                                                           chSalud,chPaCel,chPaTar,chCoTec,chCuPer,chOtGas,chDeuSi,chPCohi,chDiEco,chDeMif,chHiEmb,chSeDiv,chViInt,chAdLic,chMuSer,chEnGra,chNiSit,posFam,
                                                           chAbuNi,chPaMad,chVecin,chGuIns,chEmDom,chUnFam,chQuSol,chCuOtr,subViv,ahoViv,montoHa,chInuVi,chContVi,chRiAmVi,chRiEsvi,chRiSaVi,chRiPuVi,
                                                           chNoFaVi,chMeEst,chMeMue,chMeEle,chMePis,chMePar,chMeCol,chMeHum,chMeFac,chMeTec,chMeBan,chMeCoc,chMeAmp,chMeNot,chPfCah,chPfCuc,chPfTac,
                                                           chPfCco,chPfChi,chPfCve,chPfInv,chPfSeg,chPfNin,chMcViv,chMcTec,chMcMue,chMcEle,chMcVeh,chMcSal,chMcCir,chMcTur,chMcLib,chMcGah,chMcTac,chMcEdp,
                                                           chMcEdf,chMcCem,chMcNin,chEpBan,chEpFon,chEpFmu,chEpPad,chEpFam,chEpCal,chEpCaj,chEpEla,chEpNat,chEpOtr,chEpNin,chLcViv,chLcVeh,chLcSal,chLcCir,
                                                           chLcTur,chLcEdf,chLcEdp,chLcCre,chLcMej,chLcCro,chLcLib,chLcTar,chLcNin,chIaInv,chIaBan,chIaNat,chIaCac,chIaFem,chIaFmu,chIaFvp,chIaOtr,chIaNin,
                                                           chNfCap,chNfDes,chNfRel,chNfMan,chNfFin,chNfFor,chNfIdi,chNfInf,chNfFco,chNfOtr,chNfNot,chHoCin,chHoDep,chHoVid,chHoVte,chHoNav,chHoIce,chHoIpa,
                                                           chHoIfi,chHoCex,chHoDes,chHoJar,chHoCon,chHoPin,chHoEsc,chHoFot,chHoVmu,chHoVbi,chHoEsp,chHoDan,chHoTin,chHoCoc,chHoMan,chHoOtr,chHoNin,chQpHij,
                                                           chQpAmi,chQpMas,chQpSol,chQpFam,chQpAmo,chQpPar,chQpCom,chQpOtr,chHhCin,chHhDep,chHhVid,chHhVte,chHhNav,chHhIce,chHhIpa,chHhIfi,chHhCex,chHhDes,
                                                           chHhJar,chHhCon,chHhPin,chHhEsc,chHhFot,chHhVmu,chHhVbi,chHhEsp,chHhDan,chHhTin,chHhCoc,chHhMan,chHhOtr,chHhNin,chBuFdi,chBuNcd,chBuDap,chBuFmo,
                                                           chBuNdt,chBuOtr,chBuNin)">
                                                        <span class="glyphicon glyphicon-minus"></span>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>

                                    <div id="divAddEst" align="center">
                                        <input type="hidden" id="funcion11" name="funcion11" value="addCredito">
                                        <input type="hidden" id="tablaMtx" name="tablaMtx" value="<?php echo $tbInfoEmpleado ?>">
                                        <input type="hidden" id="userMtx" name="userMtx" value="<?php echo $userTal ?>">
                                        <button type="button" class="btn btn-default btn-lg"
                                                onclick="openOps(funcion11,tablaMtx,userMtx,tenVivi,tipVivi,tienTerr,tienLote,estVivi,chAcue,chAlca,chAseo,chEner,chInter,chGas,
                                  chTele,credito,chBici,chBus,chCamina,chPart,chMetro,chMoto,chOtroT,chTaxi,chContra,otroTrans,chTrae,chComBoca,
                                  chComOtros,chCasa,chOtrosAl,otroTalmu,hobbie,lugparq,locker,chAudit,chComite,chBrigada,chOtroRol,cualRol,Idefnc,
                                  Idegen,Ideced,Idepas,Idevis,estcivUser,Idedir,lunaUser,estUser,munUser,barUser,telUser,celUser,corUser,tisanUser,
                                  extUser,chCoCon,chGesIn,chCoDoc,chGesTe,chCoInv,chHisTe,chCoCom,chInfInt,chCopas,chMedTra,chCrede,chMeCon,chEtiIn,
                                  chMoSe,chEtiHo,chSePac,chEvCal,chTransp,chFarTe,chViEpi,chGesAm,contEmer,Ideraz,timeDesp,turnEmp,chTorBol,chTorPla,
                                  chTorVol,chTorBal,chTorTen,chCamin,chBaile,chYoga,chEnPare,chCiclo,chMara,chTarHob,chGruTea,chArtPla,chManual,
                                  chGastro,chClaIng,chConPle,chTarPic,chOtrAct,diaActi,horActi,actExtra,otraExtra,ranSal,telEmer,chViArr,chViPag,chAlime,chSerPu,chTrpte,chEdPro,chEdHij,chPaCre,chReTli,chVestu,
                                               chSalud,chPaCel,chPaTar,chCoTec,chCuPer,chOtGas,chDeuSi,chPCohi,chDiEco,chDeMif,chHiEmb,chSeDiv,chViInt,chAdLic,chMuSer,chEnGra,chNiSit,posFam,
                                               chAbuNi,chPaMad,chVecin,chGuIns,chEmDom,chUnFam,chQuSol,chCuOtr,subViv,ahoViv,montoHa,chInuVi,chContVi,chRiAmVi,chRiEsvi,chRiSaVi,chRiPuVi,
                                               chNoFaVi,chMeEst,chMeMue,chMeEle,chMePis,chMePar,chMeCol,chMeHum,chMeFac,chMeTec,chMeBan,chMeCoc,chMeAmp,chMeNot,chPfCah,chPfCuc,chPfTac,
                                               chPfCco,chPfChi,chPfCve,chPfInv,chPfSeg,chPfNin,chMcViv,chMcTec,chMcMue,chMcEle,chMcVeh,chMcSal,chMcCir,chMcTur,chMcLib,chMcGah,chMcTac,chMcEdp,
                                               chMcEdf,chMcCem,chMcNin,chEpBan,chEpFon,chEpFmu,chEpPad,chEpFam,chEpCal,chEpCaj,chEpEla,chEpNat,chEpOtr,chEpNin,chLcViv,chLcVeh,chLcSal,chLcCir,
                                               chLcTur,chLcEdf,chLcEdp,chLcCre,chLcMej,chLcCro,chLcLib,chLcTar,chLcNin,chIaInv,chIaBan,chIaNat,chIaCac,chIaFem,chIaFmu,chIaFvp,chIaOtr,chIaNin,
                                               chNfCap,chNfDes,chNfRel,chNfMan,chNfFin,chNfFor,chNfIdi,chNfInf,chNfFco,chNfOtr,chNfNot,chHoCin,chHoDep,chHoVid,chHoVte,chHoNav,chHoIce,chHoIpa,
                                               chHoIfi,chHoCex,chHoDes,chHoJar,chHoCon,chHoPin,chHoEsc,chHoFot,chHoVmu,chHoVbi,chHoEsp,chHoDan,chHoTin,chHoCoc,chHoMan,chHoOtr,chHoNin,chQpHij,
                                               chQpAmi,chQpMas,chQpSol,chQpFam,chQpAmo,chQpPar,chQpCom,chQpOtr,chHhCin,chHhDep,chHhVid,chHhVte,chHhNav,chHhIce,chHhIpa,chHhIfi,chHhCex,chHhDes,
                                               chHhJar,chHhCon,chHhPin,chHhEsc,chHhFot,chHhVmu,chHhVbi,chHhEsp,chHhDan,chHhTin,chHhCoc,chHhMan,chHhOtr,chHhNin,chBuFdi,chBuNcd,chBuDap,chBuFmo,
                                               chBuNdt,chBuOtr,chBuNin)">
                                            <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Nuevo Crédito
                                        </button>
                                    </div>

                                    <!-- PRODUCTOS FINANCIEROS: -->

                                    <table class="tblNivE" style="width: 70%; margin-bottom: 20px">
                                        <tr>
                                            <td class="tdTblTrans" colspan="2" align="center">
                                                <label>&ensp;¿ Con cuáles de los siguientes productos financieros cuenta usted actualmente ?</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 300px">
                                                <label for="chPfCah" class="container20">Cuenta de ahorros - nómina
                                                    <?php
                                                    if($varPROFI1 == 'on'){?><input type="checkbox" id="chPfCah" name="chPfCah" checked="checked" value="on"
                                                                                    onclick="contOp('chPfCah'); uncheck2('chPfNin')"><?php }
                                                    else{?><input type="checkbox" id="chPfCah" name="chPfCah" value="off" onclick="contOp('chPfCah'); uncheck2('chPfNin')"><?php }
                                                    ?>
                                                    <span class="checkmark20"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chPfCuc" class="container21">Cuenta corriente
                                                    <?php
                                                    if($varPROFI2 == 'on'){?><input type="checkbox" id="chPfCuc" name="chPfCuc" checked="checked" value="on"
                                                                                    onclick="contOp('chPfCuc'); uncheck2('chPfNin')"><?php }
                                                    else{?><input type="checkbox" id="chPfCuc" name="chPfCuc" value="off" onclick="contOp('chPfCuc'); uncheck2('chPfNin')"><?php }
                                                    ?>
                                                    <span class="checkmark21"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chPfTac" class="container20">Tarjeta de crédito
                                                    <?php
                                                    if($varPROFI3 == 'on'){?><input type="checkbox" id="chPfTac" name="chPfTac" checked="checked" value="on"
                                                                                    onclick="contOp('chPfTac'); uncheck2('chPfNin')"><?php }
                                                    else{?><input type="checkbox" id="chPfTac" name="chPfTac" value="off" onclick="contOp('chPfTac'); uncheck2('chPfNin')"><?php }
                                                    ?>
                                                    <span class="checkmark20"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chPfCco" class="container21">Crédito de consumo/libre inversión
                                                    <?php
                                                    if($varPROFI4 == 'on'){?><input type="checkbox" id="chPfCco" name="chPfCco" checked="checked" value="on"
                                                                                    onclick="contOp('chPfCco'); uncheck2('chPfNin')"><?php }
                                                    else{?><input type="checkbox" id="chPfCco" name="chPfCco" value="off" onclick="contOp('chPfCco'); uncheck2('chPfNin')"><?php }
                                                    ?>
                                                    <span class="checkmark21"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chPfChi" class="container20">Crédito hipotecario de vivienda
                                                    <?php
                                                    if($varPROFI5 == 'on'){?><input type="checkbox" id="chPfChi" name="chPfChi" checked="checked" value="on"
                                                                                    onclick="contOp('chPfChi'); uncheck2('chPfNin')"><?php }
                                                    else{?><input type="checkbox" id="chPfChi" name="chPfChi" value="off" onclick="contOp('chPfChi'); uncheck2('chPfNin')"><?php }
                                                    ?>
                                                    <span class="checkmark20"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chPfCve" class="container21">Crédito de vehículo/carro o moto
                                                    <?php
                                                    if($varPROFI6 == 'on'){?><input type="checkbox" id="chPfCve" name="chPfCve" checked="checked" value="on"
                                                                                    onclick="contOp('chPfCve'); uncheck2('chPfNin')"><?php }
                                                    else{?><input type="checkbox" id="chPfCve" name="chPfCve" value="off" onclick="contOp('chPfCve'); uncheck2('chPfNin')"><?php }
                                                    ?>
                                                    <span class="checkmark21"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chPfInv" class="container20">Inversiones
                                                    <?php
                                                    if($varPROFI7 == 'on'){?><input type="checkbox" id="chPfInv" name="chPfInv" checked="checked" value="on"
                                                                                    onclick="contOp('chPfInv'); uncheck2('chPfNin')"><?php }
                                                    else{?><input type="checkbox" id="chPfInv" name="chPfInv" value="off" onclick="contOp('chPfInv'); uncheck2('chPfNin')"><?php }
                                                    ?>
                                                    <span class="checkmark20"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chPfSeg" class="container21">Seguros
                                                    <?php
                                                    if($varPROFI8 == 'on'){?><input type="checkbox" id="chPfSeg" name="chPfSeg" checked="checked" value="on"
                                                                                    onclick="contOp('chPfSeg'); uncheck2('chPfNin')"><?php }
                                                    else{?><input type="checkbox" id="chPfSeg" name="chPfSeg" value="off" onclick="contOp('chPfSeg'); uncheck2('chPfNin')"><?php }
                                                    ?>
                                                    <span class="checkmark21"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chPfNin" class="container20">Ninguno
                                                    <?php
                                                    if($varPROFI9 == 'on'){?><input type="checkbox" id="chPfNin" name="chPfNin" checked="checked" value="on"
                                                                                    onclick="contOp('chPfNin'); uncheck4()"><?php }
                                                    else{?><input type="checkbox" id="chPfNin" name="chPfNin" value="off" onclick="contOp('chPfNin'); uncheck4()"><?php }
                                                    ?>
                                                    <span class="checkmark20"></span>
                                                </label>
                                            </td>
                                            <td>

                                            </td>
                                        </tr>
                                    </table>

                                    <!-- MOTIVO CREDITOS: -->

                                    <table class="tblNivE" style="width: 70%; margin-bottom: 20px">
                                        <tr>
                                            <td class="tdTblTrans" colspan="2" align="center">
                                                <label>&ensp;¿ Cuál es el motivo de sus créditos actuales ?</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 200px">
                                                <label for="chMcViv" class="container22">Vivienda
                                                    <?php
                                                    if($varMOCRE1 == 'on'){?><input type="checkbox" id="chMcViv" name="chMcViv" checked="checked" value="on"
                                                                                    onclick="contOp('chMcViv')"><?php }
                                                    else{?><input type="checkbox" id="chMcViv" name="chMcViv" value="off" onclick="contOp('chMcViv'); uncheck2('chMcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark22"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chMcTec" class="container23">Tecnología (celulares, computadores, tablets, entre otros)
                                                    <?php
                                                    if($varMOCRE2 == 'on'){?><input type="checkbox" id="chMcTec" name="chMcTec" checked="checked" value="on"
                                                                                    onclick="contOp('chMcTec')"><?php }
                                                    else{?><input type="checkbox" id="chMcTec" name="chMcTec" value="off" onclick="contOp('chMcTec'); uncheck2('chMcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark23"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chMcMue" class="container22">Muebles
                                                    <?php
                                                    if($varMOCRE3 == 'on'){?><input type="checkbox" id="chMcMue" name="chMcMue" checked="checked" value="on"
                                                                                    onclick="contOp('chMcMue')"><?php }
                                                    else{?><input type="checkbox" id="chMcMue" name="chMcMue" value="off" onclick="contOp('chMcMue'); uncheck2('chMcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark22"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chMcEle" class="container23">Electrodomésticos (nevera, lavadora, estufa, televisor, etc)
                                                    <?php
                                                    if($varMOCRE4 == 'on'){?><input type="checkbox" id="chMcEle" name="chMcEle" checked="checked" value="on"
                                                                                    onclick="contOp('chMcEle')"><?php }
                                                    else{?><input type="checkbox" id="chMcEle" name="chMcEle" value="off" onclick="contOp('chMcEle'); uncheck2('chMcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark23"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chMcVeh" class="container22">Vehículo/carro/moto
                                                    <?php
                                                    if($varMOCRE5 == 'on'){?><input type="checkbox" id="chMcVeh" name="chMcVeh" checked="checked" value="on"
                                                                                    onclick="contOp('chMcVeh')"><?php }
                                                    else{?><input type="checkbox" id="chMcVeh" name="chMcVeh" value="off" onclick="contOp('chMcVeh'); uncheck2('chMcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark22"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chMcSal" class="container23">Salud
                                                    <?php
                                                    if($varMOCRE6 == 'on'){?><input type="checkbox" id="chMcSal" name="chMcSal" checked="checked" value="on"
                                                                                    onclick="contOp('chMcSal')"><?php }
                                                    else{?><input type="checkbox" id="chMcSal" name="chMcSal" value="off" onclick="contOp('chMcSal'); uncheck2('chMcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark23"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chMcCir" class="container22">Cirugías estéticas
                                                    <?php
                                                    if($varMOCRE7 == 'on'){?><input type="checkbox" id="chMcCir" name="chMcCir" checked="checked" value="on"
                                                                                    onclick="contOp('chMcCir')"><?php }
                                                    else{?><input type="checkbox" id="chMcCir" name="chMcCir" value="off" onclick="contOp('chMcCir'); uncheck2('chMcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark22"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chMcTur" class="container23">Turismo
                                                    <?php
                                                    if($varMOCRE8 == 'on'){?><input type="checkbox" id="chMcTur" name="chMcTur" checked="checked" value="on"
                                                                                    onclick="contOp('chMcTur')"><?php }
                                                    else{?><input type="checkbox" id="chMcTur" name="chMcTur" value="off" onclick="contOp('chMcTur'); uncheck2('chMcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark23"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chMcLib" class="container22">Libre inversión
                                                    <?php
                                                    if($varMOCRE9 == 'on'){?><input type="checkbox" id="chMcLib" name="chMcLib" checked="checked" value="on"
                                                                                    onclick="contOp('chMcLib')"><?php }
                                                    else{?><input type="checkbox" id="chMcLib" name="chMcLib" value="off" onclick="contOp('chMcLib'); uncheck2('chMcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark22"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chMcGah" class="container23">Gastos del hogar
                                                    <?php
                                                    if($varMOCRE10 == 'on'){?><input type="checkbox" id="chMcGah" name="chMcGah" checked="checked" value="on"
                                                                                     onclick="contOp('chMcGah')"><?php }
                                                    else{?><input type="checkbox" id="chMcGah" name="chMcGah" value="off" onclick="contOp('chMcGah'); uncheck2('chMcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark23"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chMcTac" class="container22">Tarjeta de crédito
                                                    <?php
                                                    if($varMOCRE11 == 'on'){?><input type="checkbox" id="chMcTac" name="chMcTac" checked="checked" value="on"
                                                                                     onclick="contOp('chMcTac')"><?php }
                                                    else{?><input type="checkbox" id="chMcTac" name="chMcTac" value="off" onclick="contOp('chMcTac'); uncheck2('chMcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark22"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chMcEdp" class="container23">Educación propia
                                                    <?php
                                                    if($varMOCRE12 == 'on'){?><input type="checkbox" id="chMcEdp" name="chMcEdp" checked="checked" value="on"
                                                                                     onclick="contOp('chMcEdp')"><?php }
                                                    else{?><input type="checkbox" id="chMcEdp" name="chMcEdp" value="off" onclick="contOp('chMcEdp'); uncheck2('chMcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark23"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chMcEdf" class="container22">Educación familia
                                                    <?php
                                                    if($varMOCRE13 == 'on'){?><input type="checkbox" id="chMcEdf" name="chMcEdf" checked="checked" value="on"
                                                                                     onclick="contOp('chMcEdf')"><?php }
                                                    else{?><input type="checkbox" id="chMcEdf" name="chMcEdf" value="off" onclick="contOp('chMcEdf'); uncheck2('chMcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark22"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chMcCem" class="container23">Créditos empresariales o para emprendimientos
                                                    <?php
                                                    if($varMOCRE14 == 'on'){?><input type="checkbox" id="chMcCem" name="chMcCem" checked="checked" value="on"
                                                                                     onclick="contOp('chMcCem')"><?php }
                                                    else{?><input type="checkbox" id="chMcCem" name="chMcCem" value="off" onclick="contOp('chMcCem'); uncheck2('chMcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark23"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chMcNin" class="container22">Ninguno
                                                    <?php
                                                    if($varMOCRE15 == 'on'){?><input type="checkbox" id="chMcNin" name="chMcNin" checked="checked" value="on"
                                                                                     onclick="contOp('chMcNin'); uncheck5()"><?php }
                                                    else{?><input type="checkbox" id="chMcNin" name="chMcNin" value="off" onclick="contOp('chMcNin'); uncheck5()"><?php }
                                                    ?>
                                                    <span class="checkmark22"></span>
                                                </label>
                                            </td>
                                            <td>
                                            </td>
                                        </tr>
                                    </table>

                                    <!-- A QUIEN ACUDE PARA ACCEDER A CREDITOS: -->

                                    <table class="tblNivE" style="width: 70%; margin-bottom: 20px">
                                        <tr>
                                            <td class="tdTblTrans" colspan="2" align="center">
                                                <label>&ensp;¿ A qué entidades o personas acude para accder a créditos ? (Máximo 3 opciones)</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 300px">
                                                <label for="chEpBan" class="container10">Bancos Cooperativas de ahorro y crédito
                                                    <?php
                                                    if($varPEACU1 == 'on'){?><input type="checkbox" id="chEpBan" name="chEpBan" checked="checked" value="on"
                                                                                    onclick="contOp('chEpBan'); sumActi3('chEpBan'); uncheck2('chEpNin')"><?php }
                                                    else{?><input type="checkbox" id="chEpBan" name="chEpBan" value="off" onclick="contOp('chEpBan'); sumActi3('chEpBan'); uncheck2('chEpNin')"><?php }
                                                    ?>
                                                    <span class="checkmark10"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chEpFon" class="container11">Fondos de empleados
                                                    <?php
                                                    if($varPEACU2 == 'on'){?><input type="checkbox" id="chEpFon" name="chEpFon" checked="checked" value="on"
                                                                                    onclick="contOp('chEpFon'); sumActi3('chEpFon'); uncheck2('chEpNin')"><?php }
                                                    else{?><input type="checkbox" id="chEpFon" name="chEpFon" value="off" onclick="contOp('chEpFon'); sumActi3('chEpFon'); uncheck2('chEpNin')"><?php }
                                                    ?>
                                                    <span class="checkmark11"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chEpFmu" class="container10">Fondo mutuo
                                                    <?php
                                                    if($varPEACU3 == 'on'){?><input type="checkbox" id="chEpFmu" name="chEpFmu" checked="checked" value="on"
                                                                                    onclick="contOp('chEpFmu'); sumActi3('chEpFmu'); uncheck2('chEpNin')"><?php }
                                                    else{?><input type="checkbox" id="chEpFmu" name="chEpFmu" value="off" onclick="contOp('chEpFmu'); sumActi3('chEpFmu'); uncheck2('chEpNin')"><?php }
                                                    ?>
                                                    <span class="checkmark10"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chEpPad" class="container11">Paga diario o gota a gota
                                                    <?php
                                                    if($varPEACU4 == 'on'){?><input type="checkbox" id="chEpPad" name="chEpPad" checked="checked" value="on"
                                                                                    onclick="contOp('chEpPad'); sumActi3('chEpPad'); uncheck2('chEpNin')"><?php }
                                                    else{?><input type="checkbox" id="chEpPad" name="chEpPad" value="off" onclick="contOp('chEpPad'); sumActi3('chEpPad'); uncheck2('chEpNin')"><?php }
                                                    ?>
                                                    <span class="checkmark11"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chEpFam" class="container10">Familiares o amigos
                                                    <?php
                                                    if($varPEACU5 == 'on'){?><input type="checkbox" id="chEpFam" name="chEpFam" checked="checked" value="on"
                                                                                    onclick="contOp('chEpFam'); sumActi3('chEpFam'); uncheck2('chEpNin')"><?php }
                                                    else{?><input type="checkbox" id="chEpFam" name="chEpFam" value="off" onclick="contOp('chEpFam'); sumActi3('chEpFam'); uncheck2('chEpNin')"><?php }
                                                    ?>
                                                    <span class="checkmark10"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chEpCal" class="container11">Créditos en almacenes
                                                    <?php
                                                    if($varPEACU6 == 'on'){?><input type="checkbox" id="chEpCal" name="chEpCal" checked="checked" value="on"
                                                                                    onclick="contOp('chEpCal'); sumActi3('chEpCal'); uncheck2('chEpNin')"><?php }
                                                    else{?><input type="checkbox" id="chEpCal" name="chEpCal" value="off" onclick="contOp('chEpCal'); sumActi3('chEpCal'); uncheck2('chEpNin')"><?php }
                                                    ?>
                                                    <span class="checkmark11"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chEpCaj" class="container10">Cajas de Compensación
                                                    <?php
                                                    if($varPEACU7 == 'on'){?><input type="checkbox" id="chEpCaj" name="chEpCaj" checked="checked" value="on"
                                                                                    onclick="contOp('chEpCaj'); sumActi3('chEpCaj'); uncheck2('chEpNin')"><?php }
                                                    else{?><input type="checkbox" id="chEpCaj" name="chEpCaj" value="off" onclick="contOp('chEpCaj'); sumActi3('chEpCaj'); uncheck2('chEpNin')"><?php }
                                                    ?>
                                                    <span class="checkmark10"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chEpEla" class="container11">Empresa en la que labora
                                                    <?php
                                                    if($varPEACU8 == 'on'){?><input type="checkbox" id="chEpEla" name="chEpEla" checked="checked" value="on"
                                                                                    onclick="contOp('chEpEla'); sumActi3('chEpEla'); uncheck2('chEpNin')"><?php }
                                                    else{?><input type="checkbox" id="chEpEla" name="chEpEla" value="off" onclick="contOp('chEpEla'); sumActi3('chEpEla'); uncheck2('chEpNin')"><?php }
                                                    ?>
                                                    <span class="checkmark11"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chEpNat" class="container10">Natillera con amigos o familia
                                                    <?php
                                                    if($varPEACU9 == 'on'){?><input type="checkbox" id="chEpNat" name="chEpNat" checked="checked" value="on"
                                                                                    onclick="contOp('chEpNat'); sumActi3('chEpNat'); uncheck2('chEpNin')"><?php }
                                                    else{?><input type="checkbox" id="chEpNat" name="chEpNat" value="off" onclick="contOp('chEpNat'); sumActi3('chEpNat'); uncheck2('chEpNin')"><?php }
                                                    ?>
                                                    <span class="checkmark10"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chEpOtr" class="container11">Otro
                                                    <?php
                                                    if($varPEACU10 == 'on'){?><input type="checkbox" id="chEpOtr" name="chEpOtr" checked="checked" value="on"
                                                                                     onclick="contOp('chEpOtr'); sumActi3('chEpOtr'); uncheck2('chEpNin')"><?php }
                                                    else{?><input type="checkbox" id="chEpOtr" name="chEpOtr" value="off" onclick="contOp('chEpOtr'); sumActi3('chEpOtr'); uncheck2('chEpNin')"><?php }
                                                    ?>
                                                    <span class="checkmark11"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chEpNin" class="container10">Ninguno
                                                    <?php
                                                    if($varPEACU11 == 'on'){?><input type="checkbox" id="chEpNin" name="chEpNin" checked="checked" value="on"
                                                                                     onclick="contOp('chEpNin'); uncheck6()"><?php }
                                                    else{?><input type="checkbox" id="chEpNin" name="chEpNin" value="off" onclick="contOp('chEpNin'); uncheck6()"><?php }
                                                    ?>
                                                    <span class="checkmark10"></span>
                                                </label>
                                            </td>
                                            <td>

                                            </td>
                                        </tr>
                                    </table>

                                    <!-- LINEAS DE CREDITO DE INTERES: -->

                                    <table class="tblNivE" style="width: 70%; margin-bottom: 20px">
                                        <tr>
                                            <td class="tdTblTrans" colspan="2" align="center">
                                                <label>&ensp;De las siguientes líneas de crédito/préstamo de dinero, selecciones las que sean de su interés<br>(Máximo 2 opciones)</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 300px">
                                                <label for="chLcViv" class="container24">Vivienda
                                                    <?php
                                                    if($varLICRE1 == 'on'){?><input type="checkbox" id="chLcViv" name="chLcViv" checked="checked" value="on"
                                                                                    onclick="contOp('chLcViv'); sumActi4('chLcViv'); uncheck2('chLcNin')"><?php }
                                                    else{?><input type="checkbox" id="chLcViv" name="chLcViv" value="off" onclick="contOp('chLcViv'); sumActi4('chLcViv'); uncheck2('chLcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark24"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chLcVeh" class="container25">Vehículo/carro/moto
                                                    <?php
                                                    if($varLICRE2 == 'on'){?><input type="checkbox" id="chLcVeh" name="chLcVeh" checked="checked" value="on"
                                                                                    onclick="contOp('chLcVeh'); sumActi4('chLcVeh'); uncheck2('chLcNin')"><?php }
                                                    else{?><input type="checkbox" id="chLcVeh" name="chLcVeh" value="off" onclick="contOp('chLcVeh'); sumActi4('chLcVeh'); uncheck2('chLcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark25"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chLcSal" class="container24">Salud
                                                    <?php
                                                    if($varLICRE3 == 'on'){?><input type="checkbox" id="chLcSal" name="chLcSal" checked="checked" value="on"
                                                                                    onclick="contOp('chLcSal'); sumActi4('chLcSal'); uncheck2('chLcNin')"><?php }
                                                    else{?><input type="checkbox" id="chLcSal" name="chLcSal" value="off" onclick="contOp('chLcSal'); sumActi4('chLcSal'); uncheck2('chLcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark24"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chLcCir" class="container25">Cirugías estéticas
                                                    <?php
                                                    if($varLICRE4 == 'on'){?><input type="checkbox" id="chLcCir" name="chLcCir" checked="checked" value="on"
                                                                                    onclick="contOp('chLcCir'); sumActi4('chLcCir'); uncheck2('chLcNin')"><?php }
                                                    else{?><input type="checkbox" id="chLcCir" name="chLcCir" value="off" onclick="contOp('chLcCir'); sumActi4('chLcCir'); uncheck2('chLcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark25"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chLcTur" class="container24">Turismo
                                                    <?php
                                                    if($varLICRE5 == 'on'){?><input type="checkbox" id="chLcTur" name="chLcTur" checked="checked" value="on"
                                                                                    onclick="contOp('chLcTur'); sumActi4('chLcTur'); uncheck2('chLcNin')"><?php }
                                                    else{?><input type="checkbox" id="chLcTur" name="chLcTur" value="off" onclick="contOp('chLcTur'); sumActi4('chLcTur'); uncheck2('chLcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark24"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chLcEdf" class="container25">Educación de la familia
                                                    <?php
                                                    if($varLICRE6 == 'on'){?><input type="checkbox" id="chLcEdf" name="chLcEdf" checked="checked" value="on"
                                                                                    onclick="contOp('chLcEdf'); sumActi4('chLcEdf'); uncheck2('chLcNin')"><?php }
                                                    else{?><input type="checkbox" id="chLcEdf" name="chLcEdf" value="off" onclick="contOp('chLcEdf'); sumActi4('chLcEdf'); uncheck2('chLcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark25"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chLcEdp" class="container24">Educación propia
                                                    <?php
                                                    if($varLICRE7 == 'on'){?><input type="checkbox" id="chLcEdp" name="chLcEdp" checked="checked" value="on"
                                                                                    onclick="contOp('chLcEdp'); sumActi4('chLcEdp'); uncheck2('chLcNin')"><?php }
                                                    else{?><input type="checkbox" id="chLcEdp" name="chLcEdp" value="off" onclick="contOp('chLcEdp'); sumActi4('chLcEdp'); uncheck2('chLcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark24"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chLcCre" class="container25">Créditos empresariales o para emprendimientos
                                                    <?php
                                                    if($varLICRE8 == 'on'){?><input type="checkbox" id="chLcCre" name="chLcCre" checked="checked" value="on"
                                                                                    onclick="contOp('chLcCre'); sumActi4('chLcCre'); uncheck2('chLcNin')"><?php }
                                                    else{?><input type="checkbox" id="chLcCre" name="chLcCre" value="off" onclick="contOp('chLcCre'); sumActi4('chLcCre'); uncheck2('chLcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark25"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chLcMej" class="container24">Mejoramiento de vivienda
                                                    <?php
                                                    if($varLICRE9 == 'on'){?><input type="checkbox" id="chLcMej" name="chLcMej" checked="checked" value="on"
                                                                                    onclick="contOp('chLcMej'); sumActi4('chLcMej'); uncheck2('chLcNin')"><?php }
                                                    else{?><input type="checkbox" id="chLcMej" name="chLcMej" value="off" onclick="contOp('chLcMej'); sumActi4('chLcMej'); uncheck2('chLcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark24"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chLcCro" class="container25">Crédito rotativo
                                                    <?php
                                                    if($varLICRE10 == 'on'){?><input type="checkbox" id="chLcCro" name="chLcCro" checked="checked" value="on"
                                                                                     onclick="contOp('chLcCro'); sumActi4('chLcCro'); uncheck2('chLcNin')"><?php }
                                                    else{?><input type="checkbox" id="chLcCro" name="chLcCro" value="off" onclick="contOp('chLcCro'); sumActi4('chLcCro'); uncheck2('chLcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark25"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chLcLib" class="container24">Libre Inversion
                                                    <?php
                                                    if($varLICRE11 == 'on'){?><input type="checkbox" id="chLcLib" name="chLcLib" checked="checked" value="on"
                                                                                     onclick="contOp('chLcLib'); sumActi4('chLcLib'); uncheck2('chLcNin')"><?php }
                                                    else{?><input type="checkbox" id="chLcLib" name="chLcLib" value="off" onclick="contOp('chLcLib'); sumActi4('chLcLib'); uncheck2('chLcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark24"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chLcTar" class="container25">Tarjeta de crédito
                                                    <?php
                                                    if($varLICRE12 == 'on'){?><input type="checkbox" id="chLcTar" name="chLcTar" checked="checked" value="on"
                                                                                     onclick="contOp('chLcTar'); sumActi4('chLcTar'); uncheck2('chLcNin')"><?php }
                                                    else{?><input type="checkbox" id="chLcTar" name="chLcTar" value="off" onclick="contOp('chLcTar'); sumActi4('chLcTar'); uncheck2('chLcNin')"><?php }
                                                    ?>
                                                    <span class="checkmark25"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chLcNin" class="container24">Ninguno
                                                    <?php
                                                    if($varLICRE13 == 'on'){?><input type="checkbox" id="chLcNin" name="chLcNin" checked="checked" value="on"
                                                                                     onclick="contOp('chLcNin'); uncheck7()"><?php }
                                                    else{?><input type="checkbox" id="chLcNin" name="chLcNin" value="off" onclick="contOp('chLcNin'); uncheck7()"><?php }
                                                    ?>
                                                    <span class="checkmark24"></span>
                                                </label>
                                            </td>
                                            <td>

                                            </td>
                                        </tr>
                                    </table>

                                    <!-- A TRAVES DE QUE INSTITUCIONES AHORRA: -->

                                    <table class="tblNivE" style="width: 70%; margin-bottom: 20px">
                                        <tr>
                                            <td class="tdTblTrans" colspan="2" align="center">
                                                <label>&ensp;¿ A través de qué instituciones ahorras ?</label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="width: 300px">
                                                <label for="chIaInv" class="container20">Inversiones
                                                    <?php
                                                    if($varIAAHO1 == 'on'){?><input type="checkbox" id="chIaInv" name="chIaInv" checked="checked" value="on"
                                                                                    onclick="contOp('chIaInv'); uncheck2('chIaNin')"><?php }
                                                    else{?><input type="checkbox" id="chIaInv" name="chIaInv" value="off" onclick="contOp('chIaInv'); uncheck2('chIaNin')"><?php }
                                                    ?>
                                                    <span class="checkmark20"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chIaBan" class="container21">Bancos
                                                    <?php
                                                    if($varIAAHO2 == 'on'){?><input type="checkbox" id="chIaBan" name="chIaBan" checked="checked" value="on"
                                                                                    onclick="contOp('chIaBan'); uncheck2('chIaNin')"><?php }
                                                    else{?><input type="checkbox" id="chIaBan" name="chIaBan" value="off" onclick="contOp('chIaBan'); uncheck2('chIaNin')"><?php }
                                                    ?>
                                                    <span class="checkmark21"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chIaNat" class="container20">Natilleras
                                                    <?php
                                                    if($varIAAHO3 == 'on'){?><input type="checkbox" id="chIaNat" name="chIaNat" checked="checked" value="on"
                                                                                    onclick="contOp('chIaNat'); uncheck2('chIaNin')"><?php }
                                                    else{?><input type="checkbox" id="chIaNat" name="chIaNat" value="off" onclick="contOp('chIaNat'); uncheck2('chIaNin')"><?php }
                                                    ?>
                                                    <span class="checkmark20"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chIaCac" class="container21">Cooperativas de ahorro y crédito
                                                    <?php
                                                    if($varIAAHO4 == 'on'){?><input type="checkbox" id="chIaCac" name="chIaCac" checked="checked" value="on"
                                                                                    onclick="contOp('chIaCac'); uncheck2('chIaNin')"><?php }
                                                    else{?><input type="checkbox" id="chIaCac" name="chIaCac" value="off" onclick="contOp('chIaCac'); uncheck2('chIaNin')"><?php }
                                                    ?>
                                                    <span class="checkmark21"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chIaFem" class="container20">Fondos de empleados
                                                    <?php
                                                    if($varIAAHO5 == 'on'){?><input type="checkbox" id="chIaFem" name="chIaFem" checked="checked" value="on"
                                                                                    onclick="contOp('chIaFem'); uncheck2('chIaNin')"><?php }
                                                    else{?><input type="checkbox" id="chIaFem" name="chIaFem" value="off" onclick="contOp('chIaFem'); uncheck2('chIaNin')"><?php }
                                                    ?>
                                                    <span class="checkmark20"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chIaFmu" class="container21">Fondo mutuo
                                                    <?php
                                                    if($varIAAHO6 == 'on'){?><input type="checkbox" id="chIaFmu" name="chIaFmu" checked="checked" value="on"
                                                                                    onclick="contOp('chIaFmu'); uncheck2('chIaNin')"><?php }
                                                    else{?><input type="checkbox" id="chIaFmu" name="chIaFmu" value="off" onclick="contOp('chIaFmu'); uncheck2('chIaNin')"><?php }
                                                    ?>
                                                    <span class="checkmark21"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chIaFvp" class="container20">Fondo voluntario de pensiones
                                                    <?php
                                                    if($varIAAHO7 == 'on'){?><input type="checkbox" id="chIaFvp" name="chIaFvp" checked="checked" value="on"
                                                                                    onclick="contOp('chIaFvp'); uncheck2('chIaNin')"><?php }
                                                    else{?><input type="checkbox" id="chIaFvp" name="chIaFvp" value="off" onclick="contOp('chIaFvp'); uncheck2('chIaNin')"><?php }
                                                    ?>
                                                    <span class="checkmark20"></span>
                                                </label>
                                            </td>
                                            <td>
                                                <label for="chIaOtr" class="container21">Otro
                                                    <?php
                                                    if($varIAAHO8 == 'on'){?><input type="checkbox" id="chIaOtr" name="chIaOtr" checked="checked" value="on"
                                                                                    onclick="contOp('chIaOtr'); uncheck2('chIaNin')"><?php }
                                                    else{?><input type="checkbox" id="chIaOtr" name="chIaOtr" value="off" onclick="contOp('chIaOtr'); uncheck2('chIaNin')"><?php }
                                                    ?>
                                                    <span class="checkmark21"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chIaNin" class="container20">No ahorra
                                                    <?php
                                                    if($varIAAHO9 == 'on'){?><input type="checkbox" id="chIaNin" name="chIaNin" checked="checked" value="on"
                                                                                    onclick="contOp('chIaNin'); uncheck8()"><?php }
                                                    else{?><input type="checkbox" id="chIaNin" name="chIaNin" value="off" onclick="contOp('chIaNin'); uncheck8()"><?php }
                                                    ?>
                                                    <span class="checkmark20"></span>
                                                </label>
                                            </td>
                                            <td>

                                            </td>
                                        </tr>
                                    </table>


                                    <!-- //////////////////// TRANSPORTE: ////////////////////////// -->

                                    <h4 class="labelTitulo">Transporte</h4>

                                    <table class="tblNivE" style="width: 50%; margin-bottom: 10px">
                                        <tr>
                                            <td class="tdTblTrans" rowspan="9">
                                                <label>&ensp;El transporte habitual que usted utiliza para ir a su lugar de trabajo es</label>
                                            </td>
                                            <td>
                                                <label for="chBici" class="container4">Bicicleta
                                                    <?php
                                                    if($bici != null){?><input type="checkbox" id="chBici" name="chBici" checked="checked" value="on" onclick="this.value = 'off'"><?php }
                                                    else{?><input type="checkbox" id="chBici" name="chBici" value="off" onclick="this.value = 'on'"><?php }
                                                    ?>
                                                    <span class="checkmark4"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chBus" class="container4">Bus
                                                    <?php
                                                    if($bus != null){?><input type="checkbox" id="chBus" name="chBus" checked="checked" value="on" onclick="this.value = 'off'"><?php }
                                                    else{?><input type="checkbox" id="chBus" name="chBus" value="off" onclick="this.value = 'on'"><?php }
                                                    ?>
                                                    <span class="checkmark4"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chCamina" class="container4">Caminando
                                                    <?php
                                                    if($camina != null){?><input type="checkbox" id="chCamina" name="chCamina" checked="checked" value="on" onclick="this.value = 'off'"><?php }
                                                    else{?><input type="checkbox" id="chCamina" name="chCamina" value="off" onclick="this.value = 'on'"><?php }
                                                    ?>
                                                    <span class="checkmark4"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chPart" class="container4">Carro Particular
                                                    <?php
                                                    if($parti != null){?><input type="checkbox" id="chPart" name="chPart" checked="checked" value="on"
                                                                                onclick="this.value = 'off'; chValues(this.checked,'lugparq')"><?php }
                                                    else{?><input type="checkbox" id="chPart" name="chPart" value="off"
                                                                  onclick="this.value = 'on'; chValues(this.checked,'lugparq')"><?php }
                                                    ?>
                                                    <span class="checkmark4"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chMetro" class="container4">Metro
                                                    <?php
                                                    if($metro != null){?><input type="checkbox" id="chMetro" name="chMetro" checked="checked" value="on" onclick="this.value = 'off'"><?php }
                                                    else{?><input type="checkbox" id="chMetro" name="chMetro" value="off" onclick="this.value = 'on'"><?php }
                                                    ?>
                                                    <span class="checkmark4"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chMoto" class="container4">Moto
                                                    <?php
                                                    if($moto != null){?><input type="checkbox" id="chMoto" name="chMoto" checked="checked" value="on"
                                                                               onclick="this.value = 'off'; chValues(this.checked,'lugparq')"><?php }
                                                    else{?><input type="checkbox" id="chMoto" name="chMoto" value="off"
                                                                  onclick="this.value = 'on'; chValues(this.checked,'lugparq')"><?php }
                                                    ?>
                                                    <span class="checkmark4"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chOtroT" class="container4">Otro
                                                    <?php
                                                    if($otro != null){?><input type="checkbox" id="chOtroT" name="chOtroT" checked="checked" value="on"
                                                                               onclick="this.value = 'off'; chValues(this.checked,'otroTrans')"><?php }
                                                    else{?><input type="checkbox" id="chOtroT" name="chOtroT" value="off"
                                                                  onclick="this.value = 'on'; chValues(this.checked,'otroTrans')"><?php }
                                                    ?>
                                                    <span class="checkmark4"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chTaxi" class="container4">Taxi
                                                    <?php
                                                    if($taxi != null){?><input type="checkbox" id="chTaxi" name="chTaxi" checked="checked" value="on" onclick="this.value = 'off'"><?php }
                                                    else{?><input type="checkbox" id="chTaxi" name="chTaxi" value="off" onclick="this.value = 'on'"><?php }
                                                    ?>
                                                    <span class="checkmark4"></span>
                                                </label>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="chContra" class="container4">Transporte contratado
                                                    <?php
                                                    if($contra != null){?><input type="checkbox" id="chContra" name="chContra" checked="checked" value="on" onclick="this.value = 'off'"><?php }
                                                    else{?><input type="checkbox" id="chContra" name="chContra" value="off" onclick="this.value = 'on'"><?php }
                                                    ?>
                                                    <span class="checkmark4"></span>
                                                </label>
                                            </td>
                                        </tr>
                                    </table>

                                    <table class="tblNivE" style="width: 50%; margin-bottom: 10px">
                                        <thead>
                                        <tr align="center">
                                            <td>&ensp;<label for="otroTrans">Otro Transporte, cuál ?</label></td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td align="right">
                                                <?php
                                                if($otro == 'on')
                                                {
                                                    ?>
                                                    <input type="text" id="otroTrans" name="otroTrans" class="form-control form-sm inpEst" style="width: 774px" value="<?php echo $otroTrans ?>" >
                                                    <?php
                                                }
                                                if($otro != 'on')
                                                {
                                                    ?>
                                                    <input type="text" id="otroTrans" name="otroTrans" class="form-control form-sm inpEst" style="width: 774px" readonly>
                                                    <?php
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <section>
                                        <?php
                                        $lugParquea = datosOtros($userTal,$conex,1);    $locker = datosOtros($userTal,$conex,2);
                                        $actRecre = datosOtros($userTal,$conex,3);      $horActRecre = datosOtros($userTal,$conex,4);
                                        $actCult = datosOtros($userTal,$conex,5);       $horActCult = datosOtros($userTal,$conex,6);
                                        $roles = datosOtros($userTal,$conex,6);         $otroRol = datosOtros($userTal,$conex,7);
                                        $despTime = datosOtros($userTal,$conex,10);     $turnEmp = datosOtros($userTal,$conex,11);
                                        ?>
                                    </section>

                                    <!-- SI VIENE EN TRANSPORTE PARTICULAR...-->
                                    <table class="tblNivE" style="width: 50%; margin-bottom: 10px">
                                        <thead>
                                        <tr align="center">
                                            <td>&ensp;<label for="lugparq">Si viene en trasporte particular o moto, en que lugar parquea ?</label></td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td align="right">
                                                <?php
                                                if($parti == 'on' or $moto == 'on')
                                                {
                                                    ?>
                                                    <input type="text" id="lugparq" name="lugparq" class="form-control form-sm inpEst" style="width: 774px" value="<?php echo $lugParquea ?>">
                                                    <?php
                                                }
                                                if($parti != 'on' and $moto != 'on')
                                                {
                                                    ?>
                                                    <input type="text" id="lugparq" name="lugparq" class="form-control form-sm inpEst" style="width: 774px" readonly>
                                                    <?php
                                                }
                                                ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <!-- CUANTO TIEMPO EN PORMEDIO SE DEMORA...-->
                                    <table class="tblNivE" style="width: 50%; margin-bottom: 10px">
                                        <thead>
                                        <tr align="center">
                                            <td>&ensp;<label for="timeDesp">Cuánto tiempo en promedio se demora en cada desplazamiento a la empresa ?</label></td>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <tr>
                                            <td align="right">
                                                <select id="timeDesp" name="timeDesp" class="form-control form-sm" style="width: 770px" >
                                                    <option value="1">5 - 30 minutos</option>
                                                    <option value="2">31 - 60 minutos (1 hora)</option>
                                                    <option value="3">61 - 180 minutos (1,5 horas)</option>
                                                    <option value="4">mas de 180 minutos</option>
                                                    <?php
                                                    if($despTime == 1){?><option selected value="1">5 - 30 minutos</option><?php }
                                                    if($despTime == 2){?><option selected value="2">31 - 60 minutos (1 hora)</option><?php }
                                                    if($despTime == 3){?><option selected value="3">61 - 180 minutos (1,5 horas)</option><?php }
                                                    if($despTime == 4){?><option selected value="4">mas de 180 minutos</option><?php }
                                                    if($despTime == null){?><option value="0" selected>Seleccione...</option><?php }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <!-- TURNO HABITUAL DE TRABAJO -->
                                    <table class="tblNivE" style="width: 70%; margin-bottom: 20px">
                                        <thead>
                                        <tr align="center">
                                            <td>&ensp;<label for="turnEmp">Seleccione su turno habitual de trabajo</label></td>
                                            <td align="right">
                                                <select id="turnEmp" name="turnEmp" class="form-control form-sm" style="width: 160px">
                                                    <option value="D">DIURNO</option>
                                                    <option value="N">NOCTURNO</option>
                                                    <option value="M">MIXTO (Diurno y nocturno)</option>
                                                    <?php
                                                    if($turnEmp == 'D'){?><option selected value="D">DIURNO</option><?php }
                                                    if($turnEmp == 'N'){?><option selected value="N">NOCTURNO</option><?php }
                                                    if($turnEmp == 'M'){?><option selected value="M">MIXTO (Diurno y nocturno)</option><?php }
                                                    if($turnEmp == null){?><option selected disabled>Seleccione...</option><?php }
                                                    ?>
                                                </select>
                                            </td>
                                        </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!------------------------ DATOS CALIDAD DE VIDA: ------------------------->

                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <h4 class="panel-title tabsGen">
                                    <a data-toggle="collapse" data-parent="#accordion" href="#collapse5">CALIDAD DE VIDA</a>
                                </h4>
                            </div>
                            <?php
                            if($pestana == '5'){?><div id="collapse5" class="panel-collapse collapse in"><?php }
                                else{?><div id="collapse5" class="panel-collapse collapse"><?php } ?>
                                    <div class="panel-body" align="center">

                                        <section>
                                            <?php
                                            $actExtra = datosOtros($userTal,$conex,12); $otraExtra = datosOtros($userTal,$conex,13);
                                            $ranSal = datosOtros($userTal,$conex,14);
                                            $minimo = obtenerSal($conex,1); $minimo = number_format($minimo,0);     $minimo2 = obtenerSal($conex,2); $minimo2 = number_format($minimo2,0);
                                            $minimo4 = obtenerSal($conex,3); $minimo4 = number_format($minimo4,0);  $minimo6 = obtenerSal($conex,4); $minimo6 = number_format($minimo6,0);
                                            //NECESIDAD DE FORMACION:
                                            $UsNefor = datUserxEmp($wuse,$conex,42);    $datoNEFOR = explode(",",$UsNefor);
                                            $varNEFOR1 = $datoNEFOR[0];     $varNEFOR2 = $datoNEFOR[1];     $varNEFOR3 = $datoNEFOR[2];     $varNEFOR4 = $datoNEFOR[3];
                                            $varNEFOR5 = $datoNEFOR[4];     $varNEFOR6 = $datoNEFOR[5];     $varNEFOR7 = $datoNEFOR[6];     $varNEFOR8 = $datoNEFOR[7];
                                            $varNEFOR9 = $datoNEFOR[8];     $varNEFOR10 = $datoNEFOR[9];    $varNEFOR11 = $datoNEFOR[10];
                                            ?>
                                        </section>

                                        <!-- ACTIVIDADES EXTRALABORALES -->
                                        <table class="tblNivE" style="width: 50%; margin-bottom: 10px">
                                            <thead>
                                            <tr align="center">
                                                <td>&ensp;<label for="actExtra">¿ Realiza usted alguna de estas actividades en su tiempo extralaboral ?</label></td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td align="right">
                                                    <select id="actExtra" name="actExtra" class="form-control form-sm" style="width: 770px" onchange="activarCampo()" >
                                                        <option value="1">Trabaja en otra empresa</option>
                                                        <option value="2">Es docente</option>
                                                        <option value="3">Es asesor</option>
                                                        <option value="4">Es cuidador doméstico</option>
                                                        <option value="5">Otras, cúales</option>
                                                        <option value="6">Ninguna</option>
                                                        <?php
                                                        if($actExtra == 1){?><option selected value="1">Trabaja en otra empresa</option><?php }
                                                        if($actExtra == 2){?><option selected value="2">Es docente</option><?php }
                                                        if($actExtra == 3){?><option selected value="3">Es asesor</option><?php }
                                                        if($actExtra == 4){?><option selected value="4">Es cuidador doméstico</option><?php }
                                                        if($actExtra == 5){?><option selected value="5">Otras, cúales</option><?php }
                                                        if($actExtra == 6){?><option selected value="6">Ninguna</option><?php }
                                                        if($actExtra == null){?><option value="0" selected>Seleccione...</option><?php }
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <!-- OTRA ACTIVIDAD EXTRALABORAL -->
                                        <table class="tblNivE" style="width: 50%; margin-bottom: 10px">
                                            <thead>
                                            <tr align="center">
                                                <td>&ensp;<label for="otraExtra">Otra Actividad, cuál ?</label></td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td align="right">
                                                    <?php
                                                    if($actExtra == 5)
                                                    {
                                                        ?>
                                                        <input type="text" id="otraExtra" name="otraExtra" class="form-control form-sm inpEst" style="width: 774px" value="<?php echo $otraExtra ?>" >
                                                        <?php
                                                    }
                                                    if($actExtra != 5)
                                                    {
                                                        ?>
                                                        <input type="text" id="otraExtra" name="otraExtra" class="form-control form-sm inpEst" style="width: 774px" readonly>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <!-- RANGO SALARIAL -->
                                        <table class="tblNivE" style="width: 70%; margin-bottom: 20px">
                                            <thead>
                                            <tr align="center">
                                                <td>&ensp;<label for="ranSal">Escoja por favor el rango salarial en que se encuentra, teniendo en cuenta que el SMMLV
                                                        para <?php echo $anio_actual ?> es $<?php echo $minimo ?> <?php ?></label></td>
                                                <td align="right">
                                                    <select id="ranSal" name="ranSal" class="form-control form-sm" style="width: 420px">
                                                        <option value="1">Menos de 1 SMMLV</option>
                                                        <option value="2">1 a 2 SMMLV (Entre $<?php echo $minimo ?> hasta $<?php echo $minimo2 ?>)</option>
                                                        <option value="3">Hasta 4 SMMLV (Entre $<?php echo $minimo2 ?> hasta $<?php echo $minimo4 ?>)</option>
                                                        <option value="4">Hasta 6 SMMLV (Entre $<?php echo $minimo4 ?> hasta $<?php echo $minimo6 ?>)</option>
                                                        <option value="5">Más de $<?php echo $minimo6 ?></option>
                                                        <?php
                                                        if($ranSal == '1'){?><option selected value="1">Menos de 1 SMMLV</option><?php }
                                                        if($ranSal == '2'){?><option selected value="2">1 a 2 SMMLV (Entre $<?php echo $minimo ?> hasta $<?php echo $minimo2 ?>)</option><?php }
                                                        if($ranSal == '3'){?><option selected value="3">Hasta 4 SMMLV (Entre $<?php echo $minimo2 ?> hasta $<?php echo $minimo4 ?>)</option><?php }
                                                        if($ranSal == '4'){?><option selected value="4">Hasta 6 SMMLV (Entre $<?php echo $minimo4 ?> hasta $<?php echo $minimo6 ?>)</option><?php }
                                                        if($ranSal == '5'){?><option selected value="5">Más de $<?php echo $minimo6 ?></option><?php }
                                                        if($ranSal == null){?><option selected disabled>Seleccione...</option><?php }
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            </thead>
                                        </table>

                                        <h4 class="labelTitulo">Formación</h4>

                                        <!-- NECESIDADES DE FORMACION O CAPACITACION -->

                                        <table class="tblNivE" style="width: 100%; margin-bottom: 20px">
                                            <tr>
                                                <td class="tdTblTrans" colspan="2" align="center">
                                                    <label>&ensp;¿ Cuál de las siguientes necesidades de formación o capacitación, es para usted la más prioritaria ?</label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 350px">
                                                    <label for="chNfCap" class="container18">Capacitación empresarial
                                                        <?php
                                                        if($varNEFOR1 == 'on'){?><input type="checkbox" id="chNfCap" name="chNfCap" checked="checked" value="on"
                                                                                        onclick="contOp('chNfCap'); uncheck2('chNfNot')"><?php }
                                                        else{?><input type="checkbox" id="chNfCap" name="chNfCap" value="off" onclick="contOp('chNfCap'); uncheck2('chNfNot')"><?php }
                                                        ?>
                                                        <span class="checkmark18"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chNfDes" class="container19">Desarrollo de competencias (liderazgo, trabajo en equipo, etc)
                                                        <?php
                                                        if($varNEFOR2 == 'on'){?><input type="checkbox" id="chNfDes" name="chNfDes" checked="checked" value="on"
                                                                                        onclick="contOp('chNfDes'); uncheck2('chNfNot')"><?php }
                                                        else{?><input type="checkbox" id="chNfDes" name="chNfDes" value="off" onclick="contOp('chNfDes'); uncheck2('chNfNot')"><?php }
                                                        ?>
                                                        <span class="checkmark19"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chNfRel" class="container18">Relaciones familiares
                                                        <?php
                                                        if($varNEFOR3 == 'on'){?><input type="checkbox" id="chNfRel" name="chNfRel" checked="checked" value="on"
                                                                                        onclick="contOp('chNfRel'); uncheck2('chNfNot')"><?php }
                                                        else{?><input type="checkbox" id="chNfRel" name="chNfRel" value="off" onclick="contOp('chNfRel'); uncheck2('chNfNot')"><?php }
                                                        ?>
                                                        <span class="checkmark18"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chNfMan" class="container19">Manejo de conflictos
                                                        <?php
                                                        if($varNEFOR4 == 'on'){?><input type="checkbox" id="chNfMan" name="chNfMan" checked="checked" value="on"
                                                                                        onclick="contOp('chNfMan'); uncheck2('chNfNot')"><?php }
                                                        else{?><input type="checkbox" id="chNfMan" name="chNfMan" value="off" onclick="contOp('chNfMan'); uncheck2('chNfNot')"><?php }
                                                        ?>
                                                        <span class="checkmark19"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chNfFin" class="container18">Finanzas personales
                                                        <?php
                                                        if($varNEFOR5 == 'on'){?><input type="checkbox" id="chNfFin" name="chNfFin" checked="checked" value="on"
                                                                                        onclick="contOp('chNfFin'); uncheck2('chNfNot')"><?php }
                                                        else{?><input type="checkbox" id="chNfFin" name="chNfFin" value="off" onclick="contOp('chNfFin'); uncheck2('chNfNot')"><?php }
                                                        ?>
                                                        <span class="checkmark18"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chNfFor" class="container19">Formación técnica para el trabajo
                                                        <?php
                                                        if($varNEFOR6 == 'on'){?><input type="checkbox" id="chNfFor" name="chNfFor" checked="checked" value="on"
                                                                                        onclick="contOp('chNfFor'); uncheck2('chNfNot')"><?php }
                                                        else{?><input type="checkbox" id="chNfFor" name="chNfFor" value="off" onclick="contOp('chNfFor'); uncheck2('chNfNot')"><?php }
                                                        ?>
                                                        <span class="checkmark19"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chNfIdi" class="container18">Idiomas
                                                        <?php
                                                        if($varNEFOR7 == 'on'){?><input type="checkbox" id="chNfIdi" name="chNfIdi" checked="checked" value="on"
                                                                                        onclick="contOp('chNfIdi'); uncheck2('chNfNot')"><?php }
                                                        else{?><input type="checkbox" id="chNfIdi" name="chNfIdi" value="off" onclick="contOp('chNfIdi'); uncheck2('chNfNot')"><?php }
                                                        ?>
                                                        <span class="checkmark18"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chNfInf" class="container19">Informática y nuevas tecnologías
                                                        <?php
                                                        if($varNEFOR8 == 'on'){?><input type="checkbox" id="chNfInf" name="chNfInf" checked="checked" value="on"
                                                                                        onclick="contOp('chNfInf'); uncheck2('chNfNot')"><?php }
                                                        else{?><input type="checkbox" id="chNfInf" name="chNfInf" value="off" onclick="contOp('chNfInf'); uncheck2('chNfNot')"><?php }
                                                        ?>
                                                        <span class="checkmark19"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chNfFco" class="container18">Formación en conocimientos relacionados<br>con su profesión
                                                        <?php
                                                        if($varNEFOR9 == 'on'){?><input type="checkbox" id="chNfFco" name="chNfFco" checked="checked" value="on"
                                                                                        onclick="contOp('chNfFco'); uncheck2('chNfNot')"><?php }
                                                        else{?><input type="checkbox" id="chNfFco" name="chNfFco" value="off" onclick="contOp('chNfFco'); uncheck2('chNfNot')"><?php }
                                                        ?>
                                                        <span class="checkmark18"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chNfOtr" class="container19">Otro
                                                        <?php
                                                        if($varNEFOR10 == 'on'){?><input type="checkbox" id="chNfOtr" name="chNfOtr" checked="checked" value="on"
                                                                                         onclick="contOp('chNfOtr'); uncheck2('chNfNot')"><?php }
                                                        else{?><input type="checkbox" id="chNfOtr" name="chNfOtr" value="off" onclick="contOp('chNfOtr'); uncheck2('chNfNot')"><?php }
                                                        ?>
                                                        <span class="checkmark19"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chNfNot" class="container18">No tiene necesidades
                                                        <?php
                                                        if($varNEFOR11 == 'on'){?><input type="checkbox" id="chNfNot" name="chNfNot" checked="checked" value="on"
                                                                                         onclick="contOp('chNfNot'); uncheck9()"><?php }
                                                        else{?><input type="checkbox" id="chNfNot" name="chNfNot" value="off" onclick="contOp('chNfNot'); uncheck9()"><?php }
                                                        ?>
                                                        <span class="checkmark18"></span>
                                                    </label>
                                                </td>
                                                <td>

                                                </td>
                                            </tr>
                                        </table>

                                        <!-- //////////////////// INTERES GENERAL: ////////////////////////// -->

                                        <h4 class="labelTitulo">Interés general</h4>

                                        <section>
                                            <?php
                                            $codAlmuerzo = datosCondVida($userTal,$conex,$tbInfoEmpleado,9);

                                            //separar los codigos de los tipos de almuerzo (talhuma_24):
                                            $piecAlmu = explode(",",$codAlmuerzo);
                                            $varA1 = $piecAlmu[0];   $varA2 = $piecAlmu[1];   $varA3 = $piecAlmu[2]; $varA4 = $piecAlmu[3]; $varA5 = $piecAlmu[4];

                                            $datoAlmu = datosAlimentacion($varA1);
                                            if($datoAlmu == 'trae'){$trae = 'on';}  if($datoAlmu == 'comBoca'){$comBoca = 'on';}   if($datoAlmu == 'comOtros'){$comOtros = 'on';}
                                            if($datoAlmu == 'casa'){$casa = 'on';} if($datoAlmu == 'otros'){$otrosAl = 'on';}
                                            $datoAlmu = datosAlimentacion($varA2);
                                            if($datoAlmu == 'trae'){$trae = 'on';}  if($datoAlmu == 'comBoca'){$comBoca = 'on';}   if($datoAlmu == 'comOtros'){$comOtros = 'on';}
                                            if($datoAlmu == 'casa'){$casa = 'on';} if($datoAlmu == 'otros'){$otrosAl = 'on';}
                                            $datoAlmu = datosAlimentacion($varA3);
                                            if($datoAlmu == 'trae'){$trae = 'on';}  if($datoAlmu == 'comBoca'){$comBoca = 'on';}   if($datoAlmu == 'comOtros'){$comOtros = 'on';}
                                            if($datoAlmu == 'casa'){$casa = 'on';} if($datoAlmu == 'otros'){$otrosAl = 'on';}
                                            $datoAlmu = datosAlimentacion($varA4);
                                            if($datoAlmu == 'trae'){$trae = 'on';}  if($datoAlmu == 'comBoca'){$comBoca = 'on';}   if($datoAlmu == 'comOtros'){$comOtros = 'on';}
                                            if($datoAlmu == 'casa'){$casa = 'on';} if($datoAlmu == 'otros'){$otrosAl = 'on';}
                                            $datoAlmu = datosAlimentacion($varA5);
                                            if($datoAlmu == 'trae'){$trae = 'on';}  if($datoAlmu == 'comBoca'){$comBoca = 'on';}   if($datoAlmu == 'comOtros'){$comOtros = 'on';}
                                            if($datoAlmu == 'casa'){$casa = 'on';} if($datoAlmu == 'otros'){$otrosAl = 'on';}
                                            ?>
                                        </section>

                                        <table class="tblNivE" style="width: 60%; margin-bottom: 20px">
                                            <tr>
                                                <td class="tdTblTrans" rowspan="5">
                                                    <label>&ensp;Usted habitualmente a la hora del almuerzo</label>
                                                </td>
                                                <td>
                                                    <label for="chTrae" class="container5">Trae sus alimentos de la casa
                                                        <?php
                                                        if($trae != null){?><input type="checkbox" id="chTrae" name="chTrae" checked="checked" value="on" onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chTrae" name="chTrae" value="off" onclick="this.value = 'on'"><?php }
                                                        ?>
                                                        <span class="checkmark5"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chComBoca" class="container5">Compra en la cafeteria Bocatos
                                                        <?php
                                                        if($comBoca != null){?><input type="checkbox" id="chComBoca" name="chComBoca" checked="checked" value="on" onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chComBoca" name="chComBoca" value="off" onclick="this.value = 'on'"><?php }
                                                        ?>
                                                        <span class="checkmark5"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chComOtros" class="container5">Compra en otros lugares
                                                        <?php
                                                        if($comOtros != null){?><input type="checkbox" id="chComOtros" name="chComOtros" checked="checked" value="on" onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chComOtros" name="chComOtros" value="off" onclick="this.value = 'on'"><?php }
                                                        ?>
                                                        <span class="checkmark5"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chCasa" class="container5">Va a su casa
                                                        <?php
                                                        if($casa != null){?><input type="checkbox" id="chCasa" name="chCasa" checked="checked" value="on" onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chCasa" name="chCasa" value="off" onclick="this.value = 'on'"><?php }
                                                        ?>
                                                        <span class="checkmark5"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chOtrosAl" class="container5">Otros
                                                        <?php
                                                        if($otrosAl != null){?><input type="checkbox" id="chOtrosAl" name="chOtrosAl" checked="checked" value="on"
                                                                                      onclick="this.value = 'off'; chValues(this.checked,'otroTalmu')"><?php }
                                                        else{?><input type="checkbox" id="chOtrosAl" name="chOtrosAl" value="off"
                                                                      onclick="this.value = 'on'; chValues(this.checked,'otroTalmu')"><?php }
                                                        ?>
                                                        <span class="checkmark5"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                        </table>

                                        <table class="tblNivE" style="width: 50%; margin-bottom: 20px">
                                            <thead>
                                            <tr align="center">
                                                <td>&ensp;<label for="otroTalmu">Otros, cuáles ?</label></td>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td align="right">
                                                    <?php
                                                    if($otrosAl == 'on')
                                                    {
                                                        ?>
                                                        <input type="text" id="otroTalmu" name="otroTalmu" class="form-control form-sm inpEst" style="width: 774px"
                                                               value="<?php echo $otrosAlim ?>" >
                                                        <?php
                                                    }
                                                    if($otrosAl != 'on')
                                                    {
                                                        ?>
                                                        <input type="text" id="otroTalmu" name="otroTalmu" class="form-control form-sm inpEst" style="width: 774px" readonly>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <input type="hidden" id="locker" name="locker" class="form-control form-sm" style="width: 160px" value="<?php echo $locker ?>">

                                        <!-- //////////////////// ACTIVIDADES RECREATIVAS: ////////////////////////// -->

                                        <section>
                                            <?php
                                            $actives = datosOtros($userTal,$conex,3);   $datoAct = explode(",",$actives);
                                            $varAC1 = $datoAct[0];      $varAC2 = $datoAct[1];      $varAC3 = $datoAct[2];      $varAC4 = $datoAct[3];      $varAC5 = $datoAct[4];
                                            $varAC6 = $datoAct[5];      $varAC7 = $datoAct[6];      $varAC8 = $datoAct[7];      $varAC9 = $datoAct[8];      $varAC10 = $datoAct[9];
                                            $varAC11 = $datoAct[10];    $varAC12 = $datoAct[11];    $varAC13 = $datoAct[12];    $varAC14 = $datoAct[13];    $varAC15 = $datoAct[14];
                                            $varAC16 = $datoAct[15];    $varAC17 = $datoAct[16];    $varAC18 = $datoAct[17];    $varAC19 = $datoAct[18];    $varAC20 = $datoAct[19];
                                            $diaActi = datosOtros($userTal,$conex,4);
                                            $horActi = datosOtros($userTal,$conex,5);
                                            //HOBBIES:
                                            $UsHobie = datUserxEmp($wuse,$conex,43);    $datoHOBIE = explode(",",$UsHobie);
                                            $varHOBIE1 = $datoHOBIE[0];     $varHOBIE2 = $datoHOBIE[1];     $varHOBIE3 = $datoHOBIE[2];     $varHOBIE4 = $datoHOBIE[3];     $varHOBIE5 = $datoHOBIE[4];
                                            $varHOBIE6 = $datoHOBIE[5];     $varHOBIE7 = $datoHOBIE[6];     $varHOBIE8 = $datoHOBIE[7];     $varHOBIE9 = $datoHOBIE[8];     $varHOBIE10 = $datoHOBIE[9];
                                            $varHOBIE11 = $datoHOBIE[10];   $varHOBIE12 = $datoHOBIE[11];   $varHOBIE13 = $datoHOBIE[12];   $varHOBIE14 = $datoHOBIE[13];   $varHOBIE15 = $datoHOBIE[14];
                                            $varHOBIE16 = $datoHOBIE[15];   $varHOBIE17 = $datoHOBIE[16];   $varHOBIE18 = $datoHOBIE[17];   $varHOBIE19 = $datoHOBIE[18];   $varHOBIE20 = $datoHOBIE[19];
                                            $varHOBIE21 = $datoHOBIE[20];   $varHOBIE22 = $datoHOBIE[21];   $varHOBIE23 = $datoHOBIE[22];   $varHOBIE24 = $datoHOBIE[23];
                                            //CON QUIEN PASA TIEMPO ESPARCIMIENTO:
                                            $UsQpTie = datUserxEmp($wuse,$conex,44);    $datoPASTI = explode(",",$UsQpTie);
                                            $varPASTI1 = $datoPASTI['0'];   $varPASTI2 = $datoPASTI['1'];   $varPASTI3 = $datoPASTI['2'];   $varPASTI4 = $datoPASTI['3'];   $varPASTI5 = $datoPASTI['4'];
                                            $varPASTI6 = $datoPASTI['5'];   $varPASTI7 = $datoPASTI['6'];   $varPASTI8 = $datoPASTI['7'];   $varPASTI9 = $datoPASTI['8'];
                                            //QUE HACEN HIJOS EN TIEMPO DE ESPARCIMIENTO:
                                            $UsHhTli = datUserxEmp($wuse,$conex,45);    $datoHHTES = explode(",",$UsHhTli);
                                            $varHHTES1 = $datoHHTES[0];     $varHHTES2 = $datoHHTES[1];     $varHHTES3 = $datoHHTES[2];     $varHHTES4 = $datoHHTES[3];     $varHHTES5 = $datoHHTES[4];
                                            $varHHTES6 = $datoHHTES[5];     $varHHTES7 = $datoHHTES[6];     $varHHTES8 = $datoHHTES[7];     $varHHTES9 = $datoHHTES[8];     $varHHTES10 = $datoHHTES[9];
                                            $varHHTES11 = $datoHHTES[10];   $varHHTES12 = $datoHHTES[11];   $varHHTES13 = $datoHHTES[12];   $varHHTES14 = $datoHHTES[13];   $varHHTES15 = $datoHHTES[14];
                                            $varHHTES16 = $datoHHTES[15];   $varHHTES17 = $datoHHTES[16];   $varHHTES18 = $datoHHTES[17];   $varHHTES19 = $datoHHTES[18];   $varHHTES20 = $datoHHTES[19];
                                            $varHHTES21 = $datoHHTES[20];   $varHHTES22 = $datoHHTES[21];   $varHHTES23 = $datoHHTES[22];   $varHHTES24 = $datoHHTES[23];
                                            //BARRERAS USO DEL TIEMPO LIBRE:
                                            $UsBuTli = datUserxEmp($wuse,$conex,46);    $datoBUTLI = explode(",",$UsBuTli);
                                            $varBUTLI1 = $datoBUTLI[0];     $varBUTLI2 = $datoBUTLI[1];     $varBUTLI3 = $datoBUTLI[2];     $varBUTLI4 = $datoBUTLI[3];     $varBUTLI5 = $datoBUTLI[4];
                                            $varBUTLI6 = $datoBUTLI[5];     $varBUTLI7 = $datoBUTLI[6];
                                            ?>
                                        </section>

                                        <table class="tblNivE" style="width: 70%; margin-bottom: 20px">
                                            <tr>
                                                <td class="tdTblTrans" colspan="2" align="center">
                                                    <label>&ensp;¿ En cuáles actividades participaría activamente ? (Elija solo 3)</label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 300px">
                                                    <label for="chTorBol" class="container10">Torneo de bolos
                                                        <?php
                                                        if($varAC1 == '01'){?><input type="checkbox" id="chTorBol" name="chTorBol" checked="checked" value="on"
                                                                                     onclick="contOp('chTorBol'); sumActi('chTorBol')"><?php }
                                                        else{?><input type="checkbox" id="chTorBol" name="chTorBol" value="off" onclick="contOp('chTorBol'); sumActi('chTorBol')"><?php }
                                                        ?>
                                                        <span class="checkmark10"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chTorPla" class="container11">Torneo de PlayStation
                                                        <?php
                                                        if($varAC2 == '02'){?><input type="checkbox" id="chTorPla" name="chTorPla" checked="checked" value="on"
                                                                                     onclick="contOp('chTorPla'); sumActi('chTorPla')"><?php }
                                                        else{?><input type="checkbox" id="chTorPla" name="chTorPla" value="off" onclick="contOp('chTorPla'); sumActi('chTorPla')"><?php }
                                                        ?>
                                                        <span class="checkmark11"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chTorVol" class="container10">Torneo de Voleibol mixto
                                                        <?php
                                                        if($varAC3 == '03'){?><input type="checkbox" id="chTorVol" name="chTorVol" checked="checked" value="on"
                                                                                     onclick="contOp('chTorVol'); sumActi('chTorVol')"><?php }
                                                        else{?><input type="checkbox" id="chTorVol" name="chTorVol" value="off" onclick="contOp('chTorVol'); sumActi('chTorVol')"><?php }
                                                        ?>
                                                        <span class="checkmark10"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chTorBal" class="container11">Torneo de Baloncesto mixto
                                                        <?php
                                                        if($varAC4 == '04'){?><input type="checkbox" id="chTorBal" name="chTorBal" checked="checked" value="on"
                                                                                     onclick="contOp('chTorBal'); sumActi('chTorBal')"><?php }
                                                        else{?><input type="checkbox" id="chTorBal" name="chTorBal" value="off" onclick="contOp('chTorBal'); sumActi('chTorBal')"><?php }
                                                        ?>
                                                        <span class="checkmark11"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chTorTen" class="container10">Torneo de tenis de campo
                                                        <?php
                                                        if($varAC5 == '05'){?><input type="checkbox" id="chTorTen" name="chTorTen" checked="checked" value="on"
                                                                                     onclick="contOp('chTorTen'); sumActi('chTorTen')"><?php }
                                                        else{?><input type="checkbox" id="chTorTen" name="chTorTen" value="off" onclick="contOp('chTorTen'); sumActi('chTorTen')"><?php }
                                                        ?>
                                                        <span class="checkmark10"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chCamin" class="container11">Caminatas
                                                        <?php
                                                        if($varAC6 == '06'){?><input type="checkbox" id="chCamin" name="chCamin" checked="checked" value="on"
                                                                                     onclick="contOp('chCamin'); sumActi('chCamin')"><?php }
                                                        else{?><input type="checkbox" id="chCamin" name="chCamin" value="off" onclick="contOp('chCamin'); sumActi('chCamin')"><?php }
                                                        ?>
                                                        <span class="checkmark11"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chBaile" class="container10">Baile
                                                        <?php
                                                        if($varAC7 == '07'){?><input type="checkbox" id="chBaile" name="chBaile" checked="checked" value="on"
                                                                                     onclick="contOp('chBaile'); sumActi('chBaile')"><?php }
                                                        else{?><input type="checkbox" id="chBaile" name="chBaile" value="off" onclick="contOp('chBaile'); sumActi('chBaile')"><?php }
                                                        ?>
                                                        <span class="checkmark10"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chYoga" class="container11">Yoga
                                                        <?php
                                                        if($varAC8 == '08'){?><input type="checkbox" id="chYoga" name="chYoga" checked="checked" value="on"
                                                                                     onclick="contOp('chYoga'); sumActi('chYoga')"><?php }
                                                        else{?><input type="checkbox" id="chYoga" name="chYoga" value="off" onclick="contOp('chYoga'); sumActi('chYoga')"><?php }
                                                        ?>
                                                        <span class="checkmark11"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chEnPare" class="container10">Encuentro de parejas
                                                        <?php
                                                        if($varAC9 == '09'){?><input type="checkbox" id="chEnPare" name="chEnPare" checked="checked" value="on"
                                                                                     onclick="contOp('chEnPare'); sumActi('chEnPare')"><?php }
                                                        else{?><input type="checkbox" id="chEnPare" name="chEnPare" value="off" onclick="contOp('chEnPare'); sumActi('chEnPare')"><?php }
                                                        ?>
                                                        <span class="checkmark10"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chCiclo" class="container11">Ciclo paseos
                                                        <?php
                                                        if($varAC10 == '10'){?><input type="checkbox" id="chCiclo" name="chCiclo" checked="checked" value="on"
                                                                                      onclick="contOp('chCiclo'); sumActi('chCiclo')"><?php }
                                                        else{?><input type="checkbox" id="chCiclo" name="chCiclo" value="off" onclick="contOp('chCiclo'); sumActi('chCiclo')"><?php }
                                                        ?>
                                                        <span class="checkmark11"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chMara" class="container10">Maratones
                                                        <?php
                                                        if($varAC11 == '11'){?><input type="checkbox" id="chMara" name="chMara" checked="checked" value="on"
                                                                                      onclick="contOp('chMara'); sumActi('chMara')"><?php }
                                                        else{?><input type="checkbox" id="chMara" name="chMara" value="off" onclick="contOp('chMara'); sumActi('chMara')"><?php }
                                                        ?>
                                                        <span class="checkmark10"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chTarHob" class="container11">Tarde de hobbies
                                                        <?php
                                                        if($varAC12 == '12'){?><input type="checkbox" id="chTarHob" name="chTarHob" checked="checked" value="on"
                                                                                      onclick="contOp('chTarHob'); sumActi('chTarHob')"><?php }
                                                        else{?><input type="checkbox" id="chTarHob" name="chTarHob" value="off" onclick="contOp('chTarHob'); sumActi('chTarHob')"><?php }
                                                        ?>
                                                        <span class="checkmark11"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chGruTea" class="container10">Grupo de teatro
                                                        <?php
                                                        if($varAC13 == '13'){?><input type="checkbox" id="chGruTea" name="chGruTea" checked="checked" value="on"
                                                                                      onclick="contOp('chGruTea'); sumActi('chGruTea')"><?php }
                                                        else{?><input type="checkbox" id="chGruTea" name="chGruTea" value="off" onclick="contOp('chGruTea'); sumActi('chGruTea')"><?php }
                                                        ?>
                                                        <span class="checkmark10"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chArtPla" class="container11">Artes plasticas
                                                        <?php
                                                        if($varAC14 == '14'){?><input type="checkbox" id="chArtPla" name="chArtPla" checked="checked" value="on"
                                                                                      onclick="contOp('chArtPla'); sumActi('chArtPla')"><?php }
                                                        else{?><input type="checkbox" id="chArtPla" name="chArtPla" value="off" onclick="contOp('chArtPla'); sumActi('chArtPla')"><?php }
                                                        ?>
                                                        <span class="checkmark11"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chManual" class="container10">Manualidades
                                                        <?php
                                                        if($varAC15 == '15'){?><input type="checkbox" id="chManual" name="chManual" checked="checked" value="on"
                                                                                      onclick="contOp('chManual'); sumActi('chManual')"><?php }
                                                        else{?><input type="checkbox" id="chManual" name="chManual" value="off" onclick="contOp('chManual'); sumActi('chManual')"><?php }
                                                        ?>
                                                        <span class="checkmark10"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chGastro" class="container11">Gastronomía
                                                        <?php
                                                        if($varAC16 == '16'){?><input type="checkbox" id="chGastro" name="chGastro" checked="checked" value="on"
                                                                                      onclick="contOp('chGastro'); sumActi('chGastro')"><?php }
                                                        else{?><input type="checkbox" id="chGastro" name="chGastro" value="off" onclick="contOp('chGastro'); sumActi('chGastro')"><?php }
                                                        ?>
                                                        <span class="checkmark11"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chClaIng" class="container10">Clases de inglés
                                                        <?php
                                                        if($varAC17 == '17'){?><input type="checkbox" id="chClaIng" name="chClaIng" checked="checked" value="on"
                                                                                      onclick="contOp('chClaIng'); sumActi('chClaIng')"><?php }
                                                        else{?><input type="checkbox" id="chClaIng" name="chClaIng" value="off" onclick="contOp('chClaIng'); sumActi('chClaIng')"><?php }
                                                        ?>
                                                        <span class="checkmark10"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chConPle" class="container11">Conciencia plena (Mindfulnes)
                                                        <?php
                                                        if($varAC18 == '18'){?><input type="checkbox" id="chConPle" name="chConPle" checked="checked" value="on"
                                                                                      onclick="contOp('chConPle'); sumActi('chConPle')"><?php }
                                                        else{?><input type="checkbox" id="chConPle" name="chConPle" value="off" onclick="contOp('chConPle'); sumActi('chConPle')"><?php }
                                                        ?>
                                                        <span class="checkmark11"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chTarPic" class="container10">Tardes de picnic
                                                        <?php
                                                        if($varAC19 == '19'){?><input type="checkbox" id="chTarPic" name="chTarPic" checked="checked" value="on"
                                                                                      onclick="contOp('chTarPic'); sumActi('chTarPic')"><?php }
                                                        else{?><input type="checkbox" id="chTarPic" name="chTarPic" value="off" onclick="contOp('chTarPic'); sumActi('chTarPic')"><?php }
                                                        ?>
                                                        <span class="checkmark10"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chOtrAct" class="container11">Otros
                                                        <?php
                                                        if($varAC20 == '20'){?><input type="checkbox" id="chOtrAct" name="chOtrAct" checked="checked" value="on"
                                                                                      onclick="contOp('chOtrAct'); sumActi('chOtrAct')"><?php }
                                                        else{?><input type="checkbox" id="chOtrAct" name="chOtrAct" value="off" onclick="contOp('chOtrAct'); sumActi('chOtrAct')"><?php }
                                                        ?>
                                                        <span class="checkmark11"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- DIAS DE PARTICIPACION EN ACTIVIDADES -->
                                        <table class="tblNivE" style="width: 70%; margin-bottom: 20px">
                                            <thead>
                                            <tr align="center">
                                                <td>&ensp;<label for="diaActi">¿ Qué dias de la semana le sería posible<br>participar de estas actividades ?</label></td>
                                                <td align="right">
                                                    <select id="diaActi" name="diaActi" class="form-control form-sm" style="width: 220px">
                                                        <option value="LV">De lunes a viernes</option>
                                                        <option value="S">Sábados</option>
                                                        <option value="D">Domingos</option>
                                                        <?php
                                                        if($diaActi == 'LV'){?><option selected value="LV">De lunes a viernes</option><?php }
                                                        if($diaActi == 'S'){?><option selected value="S">Sábados</option><?php }
                                                        if($diaActi == 'D'){?><option selected value="D">Domingos</option><?php }
                                                        if($diaActi == null){?><option selected disabled>Seleccione...</option><?php }
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            </thead>
                                        </table>

                                        <!-- HORARIO PARTICIPACION EN ACTIVIDADES -->
                                        <table class="tblNivE" style="width: 70%; margin-bottom: 20px">
                                            <thead>
                                            <tr align="center">
                                                <td>&ensp;<label for="horActi">¿ En qué horario le será posible participar de estas actividades ?</label></td>
                                                <td align="right">
                                                    <select id="horActi" name="horActi" class="form-control form-sm" style="width: 220px">
                                                        <option value="67">06:00 am a 07:00 am</option>
                                                        <option value="78">07:00 am a 08:00 am</option>
                                                        <option value="89">08:00 am a 09:00 am</option>
                                                        <option value="910">09:00 am a 10:00 am</option>
                                                        <option value="1011">10:00 am a 11:00 am</option>
                                                        <option value="1112">11:00 am a 12:00 pm</option>
                                                        <option value="1213">12:00 pm a 01:00 pm</option>
                                                        <option value="1415">02:00 pm a 03:00 pm</option>
                                                        <option value="1516">03:00 pm a 04:00 pm</option>
                                                        <option value="1617">04:00 pm a 05:00 pm</option>
                                                        <option value="1718">05:00 pm a 06:00 pm</option>
                                                        <option value="1819">06:00 pm a 07:00 pm</option>
                                                        <option value="1920">07:00 pm a 08:00 pm</option>
                                                        <?php
                                                        if($horActi == '67'){?><option selected value="67">06:00 am a 07:00 am</option><?php }
                                                        if($horActi == '78'){?><option selected value="78">07:00 am a 08:00 am</option><?php }
                                                        if($horActi == '89'){?><option selected value="89">08:00 am a 09:00 am</option><?php }
                                                        if($horActi == '910'){?><option selected value="89">09:00 am a 10:00 am</option><?php }
                                                        if($horActi == '1011'){?><option selected value="89">10:00 am a 11:00 am</option><?php }
                                                        if($horActi == '1112'){?><option selected value="89">11:00 am a 12:00 pm</option><?php }
                                                        if($horActi == '1213'){?><option selected value="89">12:00 pm a 01:00 pm</option><?php }
                                                        if($horActi == '1415'){?><option selected value="89">02:00 pm a 03:00 pm</option><?php }
                                                        if($horActi == '1516'){?><option selected value="89">03:00 pm a 04:00 pm</option><?php }
                                                        if($horActi == '1617'){?><option selected value="89">04:00 pm a 05:00 pm</option><?php }
                                                        if($horActi == '1718'){?><option selected value="89">05:00 pm a 06:00 pm</option><?php }
                                                        if($horActi == '1819'){?><option selected value="89">06:00 pm a 07:00 pm</option><?php }
                                                        if($horActi == '1920'){?><option selected value="89">07:00 pm a 08:00 pm</option><?php }
                                                        if($horActi == null){?><option selected disabled>Seleccione...</option><?php }
                                                        ?>
                                                    </select>
                                                </td>
                                            </tr>
                                            </thead>
                                        </table>


                                        <!-- //////////////////// ACTIVIDADES Y HOBBIES: ////////////////////////// -->

                                        <input type="hidden" id="hobbie" name="hobbie" class="form-control form-sm inpEst" style="width: 774px" value="off" >

                                        <!-- QUE HACE EN SU TIEMPO LIBRE: -->

                                        <table class="tblNivE" style="width: 70%; margin-bottom: 20px">
                                            <tr>
                                                <td class="tdTblTrans" colspan="2" align="center">
                                                    <label>&ensp;¿ Qué hace en su tiempo libre ? (Seleccione máximo 5 opciones)</label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 300px">
                                                    <label for="chHoCin" class="container26">Cine
                                                        <?php
                                                        if($varHOBIE1 == 'on'){?><input type="checkbox" id="chHoCin" name="chHoCin" checked="checked" value="on"
                                                                                        onclick="contOp('chHoCin'); sumActi5('chHoCin'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoCin" name="chHoCin" value="off" onclick="contOp('chHoCin'); sumActi5('chHoCin'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHoDep" class="container27">Deporte
                                                        <?php
                                                        if($varHOBIE2 == 'on'){?><input type="checkbox" id="chHoDep" name="chHoDep" checked="checked" value="on"
                                                                                        onclick="contOp('chHoDep'); sumActi5('chHoDep'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoDep" name="chHoDep" value="off" onclick="contOp('chHoDep'); sumActi5('chHoDep'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHoVid" class="container26">Video juegos
                                                        <?php
                                                        if($varHOBIE3 == 'on'){?><input type="checkbox" id="chHoVid" name="chHoVid" checked="checked" value="on"
                                                                                        onclick="contOp('chHoVid'); sumActi5('chHoVid'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoVid" name="chHoVid" value="off" onclick="contOp('chHoVid'); sumActi5('chHoVid'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHoVte" class="container27">Ver televisión
                                                        <?php
                                                        if($varHOBIE4 == 'on'){?><input type="checkbox" id="chHoVte" name="chHoVte" checked="checked" value="on"
                                                                                        onclick="contOp('chHoVte'); sumActi5('chHoVte'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoVte" name="chHoVte" value="off" onclick="contOp('chHoVte'); sumActi5('chHoVte'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHoNav" class="container26">Navegar en internet
                                                        <?php
                                                        if($varHOBIE5 == 'on'){?><input type="checkbox" id="chHoNav" name="chHoNav" checked="checked" value="on"
                                                                                        onclick="contOp('chHoNav'); sumActi5('chHoNav'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoNav" name="chHoNav" value="off" onclick="contOp('chHoNav'); sumActi5('chHoNav'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHoIce" class="container27">Ir a un centro comercial
                                                        <?php
                                                        if($varHOBIE6 == 'on'){?><input type="checkbox" id="chHoIce" name="chHoIce" checked="checked" value="on"
                                                                                        onclick="contOp('chHoIce'); sumActi5('chHoIce'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoIce" name="chHoIce" value="off" onclick="contOp('chHoIce'); sumActi5('chHoIce'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHoIpa" class="container26">Ir a un parque
                                                        <?php
                                                        if($varHOBIE7 == 'on'){?><input type="checkbox" id="chHoIpa" name="chHoIpa" checked="checked" value="on"
                                                                                        onclick="contOp('chHoIpa'); sumActi5('chHoIpa'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoIpa" name="chHoIpa" value="off" onclick="contOp('chHoIpa'); sumActi5('chHoIpa'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHoIfi" class="container27">Ir a fiestas con sus amigos
                                                        <?php
                                                        if($varHOBIE8 == 'on'){?><input type="checkbox" id="chHoIfi" name="chHoIfi" checked="checked" value="on"
                                                                                        onclick="contOp('chHoIfi'); sumActi5('chHoIfi'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoIfi" name="chHoIfi" value="off" onclick="contOp('chHoIfi'); sumActi5('chHoIfi'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHoCex" class="container26">Clases extracurriculares
                                                        <?php
                                                        if($varHOBIE9 == 'on'){?><input type="checkbox" id="chHoCex" name="chHoCex" checked="checked" value="on"
                                                                                        onclick="contOp('chHoCex'); sumActi5('chHoCex'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoCex" name="chHoCex" value="off" onclick="contOp('chHoCex'); sumActi5('chHoCex'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHoDes" class="container27">Descansar/ dormir
                                                        <?php
                                                        if($varHOBIE10 == 'on'){?><input type="checkbox" id="chHoDes" name="chHoDes" checked="checked" value="on"
                                                                                         onclick="contOp('chHoDes'); sumActi5('chHoDes'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoDes" name="chHoDes" value="off" onclick="contOp('chHoDes'); sumActi5('chHoDes'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHoJar" class="container26">Jardinería
                                                        <?php
                                                        if($varHOBIE11 == 'on'){?><input type="checkbox" id="chHoJar" name="chHoJar" checked="checked" value="on"
                                                                                         onclick="contOp('chHoJar'); sumActi5('chHoJar'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoJar" name="chHoJar" value="off" onclick="contOp('chHoJar'); sumActi5('chHoJar'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHoCon" class="container27">Conciertos
                                                        <?php
                                                        if($varHOBIE12 == 'on'){?><input type="checkbox" id="chHoCon" name="chHoCon" checked="checked" value="on"
                                                                                         onclick="contOp('chHoCon'); sumActi5('chHoCon'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoCon" name="chHoCon" value="off" onclick="contOp('chHoCon'); sumActi5('chHoCon'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHoPin" class="container26">Pintura
                                                        <?php
                                                        if($varHOBIE13 == 'on'){?><input type="checkbox" id="chHoPin" name="chHoPin" checked="checked" value="on"
                                                                                         onclick="contOp('chHoPin'); sumActi5('chHoPin'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoPin" name="chHoPin" value="off" onclick="contOp('chHoPin'); sumActi5('chHoPin'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHoEsc" class="container27">Escultura
                                                        <?php
                                                        if($varHOBIE14 == 'on'){?><input type="checkbox" id="chHoEsc" name="chHoEsc" checked="checked" value="on"
                                                                                         onclick="contOp('chHoEsc'); sumActi5('chHoEsc'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoEsc" name="chHoEsc" value="off" onclick="contOp('chHoEsc'); sumActi5('chHoEsc'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHoFot" class="container26">Fotografía
                                                        <?php
                                                        if($varHOBIE15 == 'on'){?><input type="checkbox" id="chHoFot" name="chHoFot" checked="checked" value="on"
                                                                                         onclick="contOp('chHoFot'); sumActi5('chHoFot'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoFot" name="chHoFot" value="off" onclick="contOp('chHoFot'); sumActi5('chHoFot'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHoVmu" class="container27">Visita a museos
                                                        <?php
                                                        if($varHOBIE16 == 'on'){?><input type="checkbox" id="chHoVmu" name="chHoVmu" checked="checked" value="on"
                                                                                         onclick="contOp('chHoVmu'); sumActi5('chHoVmu')"><?php }
                                                        else{?><input type="checkbox" id="chHoVmu" name="chHoVmu" value="off" onclick="contOp('chHoVmu'); sumActi5('chHoVmu'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHoVbi" class="container26">Visita a bibliotecas
                                                        <?php
                                                        if($varHOBIE17 == 'on'){?><input type="checkbox" id="chHoVbi" name="chHoVbi" checked="checked" value="on"
                                                                                         onclick="contOp('chHoVbi'); sumActi5('chHoVbi'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoVbi" name="chHoVbi" value="off" onclick="contOp('chHoVbi'); sumActi5('chHoVbi'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHoEsp" class="container27">Espectáculos artísiticos y culturales
                                                        <?php
                                                        if($varHOBIE18 == 'on'){?><input type="checkbox" id="chHoEsp" name="chHoEsp" checked="checked" value="on"
                                                                                         onclick="contOp('chHoEsp'); sumActi5('chHoEsp'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoEsp" name="chHoEsp" value="off" onclick="contOp('chHoEsp'); sumActi5('chHoEsp'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHoDan" class="container26">Danzas
                                                        <?php
                                                        if($varHOBIE19 == 'on'){?><input type="checkbox" id="chHoDan" name="chHoDan" checked="checked" value="on"
                                                                                         onclick="contOp('chHoDan'); sumActi5('chHoDan'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoDan" name="chHoDan" value="off" onclick="contOp('chHoDan'); sumActi5('chHoDan'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHoTin" class="container27">Tocar un instrumento musical
                                                        <?php
                                                        if($varHOBIE20 == 'on'){?><input type="checkbox" id="chHoTin" name="chHoTin" checked="checked" value="on"
                                                                                         onclick="contOp('chHoTin'); sumActi5('chHoTin'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoTin" name="chHoTin" value="off" onclick="contOp('chHoTin'); sumActi5('chHoTin'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHoCoc" class="container26">Cocina
                                                        <?php
                                                        if($varHOBIE21 == 'on'){?><input type="checkbox" id="chHoCoc" name="chHoCoc" checked="checked" value="on"
                                                                                         onclick="contOp('chHoCoc'); sumActi5('chHoCoc'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoCoc" name="chHoCoc" value="off" onclick="contOp('chHoCoc'); sumActi5('chHoCoc'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHoMan" class="container27">Manualidades
                                                        <?php
                                                        if($varHOBIE22 == 'on'){?><input type="checkbox" id="chHoMan" name="chHoMan" checked="checked" value="on"
                                                                                         onclick="contOp('chHoMan'); sumActi5('chHoMan'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoMan" name="chHoMan" value="off" onclick="contOp('chHoMan'); sumActi5('chHoMan'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHoOtr" class="container26">Otro
                                                        <?php
                                                        if($varHOBIE23 == 'on'){?><input type="checkbox" id="chHoOtr" name="chHoOtr" checked="checked" value="on"
                                                                                         onclick="contOp('chHoOtr'); sumActi5('chHoOtr'); uncheck2('chHoNin')"><?php }
                                                        else{?><input type="checkbox" id="chHoOtr" name="chHoOtr" value="off" onclick="contOp('chHoOtr'); sumActi5('chHoOtr'); uncheck2('chHoNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHoNin" class="container27">Ninguno
                                                        <?php
                                                        if($varHOBIE24 == 'on'){?><input type="checkbox" id="chHoNin" name="chHoNin" checked="checked" value="on"
                                                                                         onclick="contOp('chHoNin'); uncheck10()"><?php }
                                                        else{?><input type="checkbox" id="chHoNin" name="chHoNin" value="off" onclick="contOp('chHoNin'); uncheck10()"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- CON QUIEN PASA SU TIEMPO DE ESPARCIMIENTO: -->

                                        <table class="tblNivE" style="width: 70%; margin-bottom: 20px">
                                            <tr>
                                                <td class="tdTblTrans" colspan="2" align="center">
                                                    <label>&ensp;¿ Con quién pasas la mayor parte de tu tiempo de esparcimiento/ tiempo por fuera de tus actividades laborales ?
                                                        (Seleccione máximo 3 opciones)
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 300px">
                                                    <label for="chQpHij" class="container28">Hijos / hijas
                                                        <?php
                                                        if($varPASTI1 == 'on'){?><input type="checkbox" id="chQpHij" name="chQpHij" checked="checked" value="on"
                                                                                        onclick="contOp('chQpHij'); sumActi6('chQpHij')"><?php }
                                                        else{?><input type="checkbox" id="chQpHij" name="chQpHij" value="off" onclick="contOp('chQpHij'); sumActi6('chQpHij')"><?php }
                                                        ?>
                                                        <span class="checkmark28"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chQpAmi" class="container29">Amigos / amigas
                                                        <?php
                                                        if($varPASTI2 == 'on'){?><input type="checkbox" id="chQpAmi" name="chQpAmi" checked="checked" value="on"
                                                                                        onclick="contOp('chQpAmi'); sumActi6('chQpAmi')"><?php }
                                                        else{?><input type="checkbox" id="chQpAmi" name="chQpAmi" value="off" onclick="contOp('chQpAmi'); sumActi6('chQpAmi')"><?php }
                                                        ?>
                                                        <span class="checkmark29"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chQpMas" class="container28">Mascotas
                                                        <?php
                                                        if($varPASTI3 == 'on'){?><input type="checkbox" id="chQpMas" name="chQpMas" checked="checked" value="on"
                                                                                        onclick="contOp('chQpMas'); uncheck2('chPfNin'); sumActi6('chQpMas')"><?php }
                                                        else{?><input type="checkbox" id="chQpMas" name="chQpMas" value="off" onclick="contOp('chQpMas'); sumActi6('chQpMas')"><?php }
                                                        ?>
                                                        <span class="checkmark28"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chQpSol" class="container29">Solo
                                                        <?php
                                                        if($varPASTI4 == 'on'){?><input type="checkbox" id="chQpSol" name="chQpSol" checked="checked" value="on"
                                                                                        onclick="contOp('chQpSol'); sumActi6('chQpSol')"><?php }
                                                        else{?><input type="checkbox" id="chQpSol" name="chQpSol" value="off" onclick="contOp('chQpSol'); sumActi6('chQpSol')"><?php }
                                                        ?>
                                                        <span class="checkmark29"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chQpFam" class="container28">Familia
                                                        <?php
                                                        if($varPASTI5 == 'on'){?><input type="checkbox" id="chQpFam" name="chQpFam" checked="checked" value="on"
                                                                                        onclick="contOp('chQpFam'); sumActi6('chQpFam')"><?php }
                                                        else{?><input type="checkbox" id="chQpFam" name="chQpFam" value="off" onclick="contOp('chQpFam'); sumActi6('chQpFam')"><?php }
                                                        ?>
                                                        <span class="checkmark28"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chQpAmo" class="container29">Amigos o amigas online
                                                        <?php
                                                        if($varPASTI6 == 'on'){?><input type="checkbox" id="chQpAmo" name="chQpAmo" checked="checked" value="on"
                                                                                        onclick="contOp('chQpAmo'); sumActi6('chQpAmo')"><?php }
                                                        else{?><input type="checkbox" id="chQpAmo" name="chQpAmo" value="off" onclick="contOp('chQpAmo'); sumActi6('chQpAmo')"><?php }
                                                        ?>
                                                        <span class="checkmark29"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chQpPar" class="container28">Pareja(novio o cónyuge)
                                                        <?php
                                                        if($varPASTI7 == 'on'){?><input type="checkbox" id="chQpPar" name="chQpPar" checked="checked" value="on"
                                                                                        onclick="contOp('chQpPar'); sumActi6('chQpPar')"><?php }
                                                        else{?><input type="checkbox" id="chQpPar" name="chQpPar" value="off" onclick="contOp('chQpPar'); sumActi6('chQpPar')"><?php }
                                                        ?>
                                                        <span class="checkmark28"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chQpCom" class="container29">Compañeros de trabajo
                                                        <?php
                                                        if($varPASTI8 == 'on'){?><input type="checkbox" id="chQpCom" name="chQpCom" checked="checked" value="on"
                                                                                        onclick="contOp('chQpCom'); sumActi6('chQpCom')"><?php }
                                                        else{?><input type="checkbox" id="chQpCom" name="chQpCom" value="off" onclick="contOp('chQpCom'); sumActi6('chQpCom')"><?php }
                                                        ?>
                                                        <span class="checkmark29"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chQpOtr" class="container28">Otro
                                                        <?php
                                                        if($varPASTI9 == 'on'){?><input type="checkbox" id="chQpOtr" name="chQpOtr" checked="checked" value="on"
                                                                                        onclick="contOp('chQpOtr'); sumActi6('chQpOtr')"><?php }
                                                        else{?><input type="checkbox" id="chQpOtr" name="chQpOtr" value="off" onclick="contOp('chQpOtr'); sumActi6('chQpOtr')"><?php }
                                                        ?>
                                                        <span class="checkmark28"></span>
                                                    </label>
                                                </td>
                                                <td>

                                                </td>
                                            </tr>
                                        </table>

                                        <!-- QUE HACEN SUS HIJOS EN EL TIEMPO LIBRE: -->

                                        <table class="tblNivE" style="width: 70%; margin-bottom: 20px">
                                            <tr>
                                                <td class="tdTblTrans" colspan="2" align="center">
                                                    <label>&ensp;¿ Qué actividades prefieren realizar sus hijos en el tiempo libre ? (Seleccione máximo 5 opciones)</label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 300px">
                                                    <label for="chHhCin" class="container26">Cine
                                                        <?php
                                                        if($varHHTES1 == 'on'){?><input type="checkbox" id="chHhCin" name="chHhCin" checked="checked" value="on"
                                                                                        onclick="contOp('chHhCin'); sumActi7('chHhCin'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhCin" name="chHhCin" value="off" onclick="contOp('chHhCin'); sumActi7('chHhCin'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHhDep" class="container27">Deporte
                                                        <?php
                                                        if($varHHTES2 == 'on'){?><input type="checkbox" id="chHhDep" name="chHhDep" checked="checked" value="on"
                                                                                        onclick="contOp('chHhDep'); sumActi7('chHhDep'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhDep" name="chHhDep" value="off" onclick="contOp('chHhDep'); sumActi7('chHhDep'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHhVid" class="container26">Video juegos
                                                        <?php
                                                        if($varHHTES3 == 'on'){?><input type="checkbox" id="chHhVid" name="chHhVid" checked="checked" value="on"
                                                                                        onclick="contOp('chHhVid'); sumActi7('chHhVid'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhVid" name="chHhVid" value="off" onclick="contOp('chHhVid'); sumActi7('chHhVid'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHhVte" class="container27">Ver televisión
                                                        <?php
                                                        if($varHHTES4 == 'on'){?><input type="checkbox" id="chHhVte" name="chHhVte" checked="checked" value="on"
                                                                                        onclick="contOp('chHhVte'); sumActi7('chHhVte'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhVte" name="chHhVte" value="off" onclick="contOp('chHhVte'); sumActi7('chHhVte'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHhNav" class="container26">Navegar en internet
                                                        <?php
                                                        if($varHHTES5 == 'on'){?><input type="checkbox" id="chHhNav" name="chHhNav" checked="checked" value="on"
                                                                                        onclick="contOp('chHhNav'); sumActi7('chHhNav'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhNav" name="chHhNav" value="off" onclick="contOp('chHhNav'); sumActi7('chHhNav'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHhIce" class="container27">Ir a un centro comercial
                                                        <?php
                                                        if($varHHTES6 == 'on'){?><input type="checkbox" id="chHhIce" name="chHhIce" checked="checked" value="on"
                                                                                        onclick="contOp('chHhIce'); sumActi7('chHhIce'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhIce" name="chHhIce" value="off" onclick="contOp('chHhIce'); sumActi7('chHhIce'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHhIpa" class="container26">Ir a un parque
                                                        <?php
                                                        if($varHHTES7 == 'on'){?><input type="checkbox" id="chHhIpa" name="chHhIpa" checked="checked" value="on"
                                                                                        onclick="contOp('chHhIpa'); sumActi7('chHhIpa'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhIpa" name="chHhIpa" value="off" onclick="contOp('chHhIpa'); sumActi7('chHhIpa'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHhIfi" class="container27">Ir a fiestas con sus amigos
                                                        <?php
                                                        if($varHHTES8 == 'on'){?><input type="checkbox" id="chHhIfi" name="chHhIfi" checked="checked" value="on"
                                                                                        onclick="contOp('chHhIfi'); sumActi7('chHhIfi'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhIfi" name="chHhIfi" value="off" onclick="contOp('chHhIfi'); sumActi7('chHhIfi'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHhCex" class="container26">Clases extracurriculares
                                                        <?php
                                                        if($varHHTES9 == 'on'){?><input type="checkbox" id="chHhCex" name="chHhCex" checked="checked" value="on"
                                                                                        onclick="contOp('chHhCex'); sumActi7('chHhCex'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhCex" name="chHhCex" value="off" onclick="contOp('chHhCex'); sumActi7('chHhCex'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHhDes" class="container27">Descansar/ dormir
                                                        <?php
                                                        if($varHHTES10 == 'on'){?><input type="checkbox" id="chHhDes" name="chHhDes" checked="checked" value="on"
                                                                                         onclick="contOp('chHhDes'); sumActi7('chHhDes'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhDes" name="chHhDes" value="off" onclick="contOp('chHhDes'); sumActi7('chHhDes'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHhJar" class="container26">Jardinería
                                                        <?php
                                                        if($varHHTES11 == 'on'){?><input type="checkbox" id="chHhJar" name="chHhJar" checked="checked" value="on"
                                                                                         onclick="contOp('chHhJar'); sumActi7('chHhJar'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhJar" name="chHhJar" value="off" onclick="contOp('chHhJar'); sumActi7('chHhJar'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHhCon" class="container27">Conciertos
                                                        <?php
                                                        if($varHHTES12 == 'on'){?><input type="checkbox" id="chHhCon" name="chHhCon" checked="checked" value="on"
                                                                                         onclick="contOp('chHhCon'); sumActi7('chHhCon'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhCon" name="chHhCon" value="off" onclick="contOp('chHhCon'); sumActi7('chHhCon'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHhPin" class="container26">Pintura
                                                        <?php
                                                        if($varHHTES13 == 'on'){?><input type="checkbox" id="chHhPin" name="chHhPin" checked="checked" value="on"
                                                                                         onclick="contOp('chHhPin'); sumActi7('chHhPin'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhPin" name="chHhPin" value="off" onclick="contOp('chHhPin'); sumActi7('chHhPin'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHhEsc" class="container27">Escultura
                                                        <?php
                                                        if($varHHTES14 == 'on'){?><input type="checkbox" id="chHhEsc" name="chHhEsc" checked="checked" value="on"
                                                                                         onclick="contOp('chHhEsc'); sumActi7('chHhEsc'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhEsc" name="chHhEsc" value="off" onclick="contOp('chHhEsc'); sumActi7('chHhEsc'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHhFot" class="container26">Fotografía
                                                        <?php
                                                        if($varHHTES15 == 'on'){?><input type="checkbox" id="chHhFot" name="chHhFot" checked="checked" value="on"
                                                                                         onclick="contOp('chHhFot'); sumActi7('chHhFot'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhFot" name="chHhFot" value="off" onclick="contOp('chHhFot'); sumActi7('chHhFot'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHhVmu" class="container27">Visita a museos
                                                        <?php
                                                        if($varHHTES16 == 'on'){?><input type="checkbox" id="chHhVmu" name="chHhVmu" checked="checked" value="on"
                                                                                         onclick="contOp('chHhVmu'); sumActi7('chHhVmu'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhVmu" name="chHhVmu" value="off" onclick="contOp('chHhVmu'); sumActi7('chHhVmu'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHhVbi" class="container26">Visita a bibliotecas
                                                        <?php
                                                        if($varHHTES17 == 'on'){?><input type="checkbox" id="chHhVbi" name="chHhVbi" checked="checked" value="on"
                                                                                         onclick="contOp('chHhVbi'); sumActi7('chHhVbi'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhVbi" name="chHhVbi" value="off" onclick="contOp('chHhVbi'); sumActi7('chHhVbi'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHhEsp" class="container27">Espectáculos artísiticos y culturales
                                                        <?php
                                                        if($varHHTES18 == 'on'){?><input type="checkbox" id="chHhEsp" name="chHhEsp" checked="checked" value="on"
                                                                                         onclick="contOp('chHhEsp'); sumActi7('chHhEsp'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhEsp" name="chHhEsp" value="off" onclick="contOp('chHhEsp'); sumActi7('chHhEsp'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHhDan" class="container26">Danzas
                                                        <?php
                                                        if($varHHTES19 == 'on'){?><input type="checkbox" id="chHhDan" name="chHhDan" checked="checked" value="on"
                                                                                         onclick="contOp('chHhDan'); sumActi7('chHhDan'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhDan" name="chHhDan" value="off" onclick="contOp('chHhDan'); sumActi7('chHhDan'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHhTin" class="container27">Tocar un instrumento musical
                                                        <?php
                                                        if($varHHTES20 == 'on'){?><input type="checkbox" id="chHhTin" name="chHhTin" checked="checked" value="on"
                                                                                         onclick="contOp('chHhTin'); sumActi7('chHhTin'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhTin" name="chHhTin" value="off" onclick="contOp('chHhTin'); sumActi7('chHhTin'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHhCoc" class="container26">Cocina
                                                        <?php
                                                        if($varHHTES21 == 'on'){?><input type="checkbox" id="chHhCoc" name="chHhCoc" checked="checked" value="on"
                                                                                         onclick="contOp('chHhCoc'); sumActi7('chHhCoc'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhCoc" name="chHhCoc" value="off" onclick="contOp('chHhCoc'); sumActi7('chHhCoc'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHhMan" class="container27">Manualidades
                                                        <?php
                                                        if($varHHTES22 == 'on'){?><input type="checkbox" id="chHhMan" name="chHhMan" checked="checked" value="on"
                                                                                         onclick="contOp('chHhMan'); sumActi7('chHhMan'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhMan" name="chHhMan" value="off" onclick="contOp('chHhMan'); sumActi7('chHhMan'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chHhOtr" class="container26">Otro
                                                        <?php
                                                        if($varHHTES23 == 'on'){?><input type="checkbox" id="chHhOtr" name="chHhOtr" checked="checked" value="on"
                                                                                         onclick="contOp('chHhOtr'); sumActi7('chHhOtr'); uncheck2('chHhNin')"><?php }
                                                        else{?><input type="checkbox" id="chHhOtr" name="chHhOtr" value="off" onclick="contOp('chHhOtr'); sumActi7('chHhOtr'); uncheck2('chHhNin')"><?php }
                                                        ?>
                                                        <span class="checkmark26"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHhNin" class="container27">Ninguno
                                                        <?php
                                                        if($varHHTES24 == 'on'){?><input type="checkbox" id="chHhNin" name="chHhNin" checked="checked" value="on"
                                                                                         onclick="contOp('chHhNin'); uncheck11()"><?php }
                                                        else{?><input type="checkbox" id="chHhNin" name="chHhNin" value="off" onclick="contOp('chHhNin'); uncheck11()"><?php }
                                                        ?>
                                                        <span class="checkmark27"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- BARRERAS USO TIEMPO LIBRE: -->

                                        <table class="tblNivE" style="width: 70%; margin-bottom: 10px">
                                            <tr>
                                                <td class="tdTblTrans" rowspan="7">
                                                    <label>&ensp; ¿Cuáles son las barreras que se le presentan generalmente en el uso del tiempo libre?<br>(Seleccione máximo 3 opciones)</label>
                                                </td>
                                                <td>
                                                    <label for="chBuFdi" class="container7">Falta de dinero
                                                        <?php
                                                        if($varBUTLI1 == 'on'){?><input type="checkbox" id="chBuFdi" name="chBuFdi" checked="checked" value="on"
                                                                                        onclick="contOp('chBuFdi'); sumActi8('chBuFdi'); uncheck2('chBuNin')"><?php }
                                                        else{?><input type="checkbox" id="chBuFdi" name="chBuFdi" value="off" onclick="contOp('chBuFdi'); sumActi8('chBuFdi'); uncheck2('chBuNin')"><?php }
                                                        ?>
                                                        <span class="checkmark7"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chBuNcd" class="container7">No coincidir la disponibilidad del tiempo<br>libre con la familia
                                                        <?php
                                                        if($varBUTLI2 == 'on'){?><input type="checkbox" id="chBuNcd" name="chBuNcd" checked="checked" value="on"
                                                                                        onclick="contOp('chBuNcd'); sumActi8('chBuNcd'); uncheck2('chBuNin')"><?php }
                                                        else{?><input type="checkbox" id="chBuNcd" name="chBuNcd" value="off" onclick="contOp('chBuNcd'); sumActi8('chBuNcd'); uncheck2('chBuNin')"><?php }
                                                        ?>
                                                        <span class="checkmark7"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chBuDap" class="container7">Desconocimiento de actividades<br>y programas
                                                        <?php
                                                        if($varBUTLI3 == 'on'){?><input type="checkbox" id="chBuDap" name="chBuDap" checked="checked" value="on"
                                                                                        onclick="contOp('chBuDap'); sumActi8('chBuDap'); uncheck2('chBuNin')"><?php }
                                                        else{?><input type="checkbox" id="chBuDap" name="chBuDap" value="off" onclick="contOp('chBuDap'); sumActi8('chBuDap'); uncheck2('chBuNin')"><?php }
                                                        ?>
                                                        <span class="checkmark7"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chBuFmo" class="container7">Falta motivación para salir
                                                        <?php
                                                        if($varBUTLI4 == 'on'){?><input type="checkbox" id="chBuFmo" name="chBuFmo" checked="checked" value="on"
                                                                                        onclick="contOp('chBuFmo'); sumActi8('chBuFmo'); uncheck2('chBuNin')"><?php }
                                                        else{?><input type="checkbox" id="chBuFmo" name="chBuFmo" value="off" onclick="contOp('chBuFmo'); sumActi8('chBuFmo'); uncheck2('chBuNin')"><?php }
                                                        ?>
                                                        <span class="checkmark7"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chBuNdt" class="container7">No disponer de tiempo libre
                                                        <?php
                                                        if($varBUTLI5 == 'on'){?><input type="checkbox" id="chBuNdt" name="chBuNdt" checked="checked" value="on"
                                                                                        onclick="contOp('chBuNdt'); sumActi8('chBuNdt'); uncheck2('chBuNin')"><?php }
                                                        else{?><input type="checkbox" id="chBuNdt" name="chBuNdt" value="off" onclick="contOp('chBuNdt'); sumActi8('chBuNdt'); uncheck2('chBuNin')"><?php }
                                                        ?>
                                                        <span class="checkmark7"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chBuOtr" class="container7">Otro
                                                        <?php
                                                        if($varBUTLI6 == 'on'){?><input type="checkbox" id="chBuOtr" name="chBuOtr" checked="checked" value="on"
                                                                                        onclick="contOp('chBuOtr'); sumActi8('chBuOtr'); uncheck2('chBuNin')"><?php }
                                                        else{?><input type="checkbox" id="chBuOtr" name="chBuOtr" value="off" onclick="contOp('chBuOtr'); sumActi8('chBuOtr'); uncheck2('chBuNin')"><?php }
                                                        ?>
                                                        <span class="checkmark7"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chBuNin" class="container7">Ninguno
                                                        <?php
                                                        if($varBUTLI7 == 'on'){?><input type="checkbox" id="chBuNin" name="chBuNin" checked="checked" value="on"
                                                                                        onclick="contOp('chBuNin'); uncheck12()"><?php }
                                                        else{?><input type="checkbox" id="chBuNin" name="chBuNin" value="off" onclick="contOp('chBuNin'); uncheck12()"><?php }
                                                        ?>
                                                        <span class="checkmark7"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                        </table>

                                        <!-- //////////////////// ROL EMPLEADO: ////////////////////////// -->

                                        <section>
                                            <?php
                                            $roles = datosOtros($userTal,$conex,7); $otrRol = datosOtros($userTal,$conex,8);
                                            $datoRol = explode(",",$roles);
                                            $varR1 = $datoRol[0];   $varR2 = $datoRol[1];   $varR3 = $datoRol[2]; $varR4 = $datoRol[3];
                                            if($varR1=='01'){$rAudi = 'true';} if($varR1=='02'){$rComi = 'true';} if($varR1=='03'){$rBrig = 'true';} if($varR1=='04'){$rOtro = 'true';}
                                            if($varR2=='01'){$rAudi = 'true';} if($varR2=='02'){$rComi = 'true';} if($varR2=='03'){$rBrig = 'true';} if($varR2=='04'){$rOtro = 'true';}
                                            if($varR3=='01'){$rAudi = 'true';} if($varR3=='02'){$rComi = 'true';} if($varR3=='03'){$rBrig = 'true';} if($varR3=='04'){$rOtro = 'true';}
                                            if($varR4=='01'){$rAudi = 'true';} if($varR4=='02'){$rComi = 'true';} if($varR4=='03'){$rBrig = 'true';} if($varR4=='04'){$rOtro = 'true';}
                                            ?>
                                        </section>

                                        <table class="tblNivE" style="width: 70%; margin-bottom: 10px">
                                            <tr>
                                                <td class="tdTblTrans" rowspan="4">
                                                    <label>&ensp; Rol que desempeña en la institución adicional a su cargo</label>
                                                </td>
                                                <td>
                                                    <label for="chAudit" class="container7">Es auditor interno de calidad
                                                        <?php
                                                        if($rAudi == 'true'){?><input type="checkbox" id="chAudit" name="chAudit" checked="checked" value="on"
                                                                                      onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chAudit" name="chAudit" value="off" onclick="this.value = 'on'"><?php }
                                                        ?>
                                                        <span class="checkmark7"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <!--
                            <tr>
                                <td>
                                    <label for="chComite" class="container7">Es integrante de algún comité
                                        <?php
                                            if($rComi == 'true'){?><input type="checkbox" id="chComite" name="chComite" checked="checked" value="on"
                                                                      onclick="this.value = 'off'"><?php }
                                            else{?><input type="checkbox" id="chComite" name="chComite" value="off" onclick="this.value = 'on'"><?php }
                                            ?>
                                        <span class="checkmark7"></span>
                                    </label>
                                </td>
                            </tr>
                            -->
                                            <tr>
                                                <td>
                                                    <label for="chBrigada" class="container7">Hace parte de la Brigada de Emergencia
                                                        <?php
                                                        if($rBrig == 'true'){?><input type="checkbox" id="chBrigada" name="chBrigada" checked="checked" value="on" onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chBrigada" name="chBrigada" value="off" onclick="this.value = 'on'"><?php }
                                                        ?>
                                                        <span class="checkmark7"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chOtroRol" class="container7">Otros
                                                        <?php
                                                        if($rOtro == 'true'){?><input type="checkbox" id="chOtroRol" name="chOtroRol" checked="checked" value="on"
                                                                                      onclick="this.value = 'off'; chValues(this.checked,'cualRol')"><?php }
                                                        else{?><input type="checkbox" id="chOtroRol" name="chOtroRol" value="off"
                                                                      onclick="this.value = 'on'; chValues(this.checked,'cualRol')"><?php }
                                                        ?>
                                                        <span class="checkmark7"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <input type="hidden" id="chComite" name="chComite" value="off">
                                        </table>

                                        <table class="tblNivE" style="width: 70%; margin-bottom: 20px">
                                            <thead>
                                            <tr align="center">
                                                <td>&ensp;<label for="cualRol">Otros, Cuáles ?</label></td>
                                                <td align="right">
                                                    <?php
                                                    if($rOtro == 'true')
                                                    {
                                                        ?>
                                                        <input type="text" id="cualRol" name="cualRol" class="form-control form-sm inpEst" style="width: 500px"
                                                               value="<?php echo $otrRol ?>">
                                                        <?php
                                                    }
                                                    if($rOtro != 'true')
                                                    {
                                                        ?>
                                                        <input type="text" id="cualRol" name="cualRol" class="form-control form-sm inpEst" style="width: 500px" readonly>
                                                        <?php
                                                    }
                                                    ?>
                                                </td>
                                            </tr>
                                            </thead>
                                        </table>

                                        <!-- //////////////////// PERTENENCIA A COMITES: ////////////////////////// -->

                                        <section>
                                            <?php
                                            $comites = datosOtros($userTal,$conex,9);
                                            $datCom = explode(",",$comites);
                                            $varC1 = $datCom[0];    $varC2 = $datCom[1];    $varC3 = $datCom[2];    $varC4 = $datCom[3];    $varC5 = $datCom[4];
                                            $varC6 = $datCom[5];    $varC7 = $datCom[6];    $varC8 = $datCom[7];    $varC9 = $datCom[8];    $varC10 = $datCom[9];
                                            $varC11 = $datCom[10];  $varC12 = $datCom[11];  $varC13 = $datCom[12];  $varC14 = $datCom[13];  $varC15 = $datCom[14];
                                            $varC16 = $datCom[15];  $varC17 = $datCom[16];  $varC18 = $datCom[17];  $varC19 = $datCom[18];  $varC20 = $datCom[19];
                                            $varC21 = $datCom[20];
                                            if($varC1=='01'){$coCon='true';}    if($varC2=='02'){$coGesIn='true';}   if($varC3=='03'){$coDoc='true';}    if($varC4=='04'){$coGesTe='true';}
                                            if($varC5=='05'){$coInv='true';}    if($varC6=='06'){$coHisTe='true';}   if($varC7=='07'){$coCom='true';}    if($varC8=='08'){$coInfInt='true';}
                                            if($varC9=='09'){$coCopa='true';}   if($varC10=='10'){$coMedTra='true';} if($varC11=='11'){$coCrede='true';} if($varC12=='12'){$coMeCon='true';}
                                            if($varC13=='13'){$coEtiIn='true';} if($varC14=='14'){$coMoSe='true';}   if($varC15=='15'){$coEtiHo='true';} if($varC16=='16'){$coSePac='true';}
                                            if($varC17=='17'){$coEvCal='true';} if($varC18=='18'){$coTransp='true';} if($varC19=='19'){$coFarTe='true';} if($varC20=='20'){$coViEpi='true';}
                                            if($varC21=='21'){$coGesAm='true';}
                                            ?>
                                        </section>

                                        <table class="tblNivE" style="width: 70%; margin-bottom: 10px">
                                            <tr>
                                                <td class="tdTblTrans" colspan="2" align="center">
                                                    <label>&ensp;¿ Pertenece a alguno de los siguientes comités ?</label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="width: 300px">
                                                    <label for="chCoCon" class="container8">Comité de Convivencia
                                                        <?php
                                                        if($coCon == 'true'){?><input type="checkbox" id="chCoCon" name="chCoCon" checked="checked" value="on"
                                                                                      onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chCoCon" name="chCoCon" value="off" onclick="this.value = 'on'"><?php }
                                                        ?>
                                                        <span class="checkmark8"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chGesIn" class="container9">Gestión de la Información
                                                        <?php
                                                        if($coGesIn == 'true'){?><input type="checkbox" id="chGesIn" name="chGesIn" checked="checked" value="on"
                                                                                        onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chGesIn" name="chGesIn" value="off" onclick="this.value = 'on'"><?php }
                                                        ?>
                                                        <span class="checkmark9"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chCoDoc" class="container8">Comité de Docencia
                                                        <?php
                                                        if($coDoc == 'true'){?><input type="checkbox" id="chCoDoc" name="chCoDoc" checked="checked" value="on"
                                                                                      onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chCoDoc" name="chCoDoc" value="off" onclick="this.value = 'on'"><?php }
                                                        ?>
                                                        <span class="checkmark8"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chGesTe" class="container9">Gestión de la Tecnología
                                                        <?php
                                                        if($coGesTe == 'true'){?><input type="checkbox" id="chGesTe" name="chGesTe" checked="checked" value="on"
                                                                                        onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chGesTe" name="chGesTe" value="off" onclick="this.value = 'on'"><?php }
                                                        ?>
                                                        <span class="checkmark9"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chCoInv" class="container8">Comité de Investigaciones
                                                        <?php
                                                        if($coInv == 'true'){?><input type="checkbox" id="chCoInv" name="chCoInv" checked="checked" value="on"
                                                                                      onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chCoInv" name="chCoInv" value="off" onclick="this.value = 'on'"><?php }
                                                        ?>
                                                        <span class="checkmark8"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chHisTe" class="container9">Historia Clínica y Auditoría
                                                        <?php
                                                        if($coHisTe == 'true'){?><input type="checkbox" id="chHisTe" name="chHisTe" checked="checked" value="on"
                                                                                        onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chHisTe" name="chHisTe" value="off" onclick="this.value = 'on'"><?php }
                                                        ?>
                                                        <span class="checkmark9"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chCoCom" class="container8">Compras
                                                        <?php
                                                        if($coCom == 'true'){?><input type="checkbox" id="chCoCom" name="chCoCom" checked="checked" value="on"
                                                                                      onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chCoCom" name="chCoCom" value="off"
                                                                      onclick="this.value = 'on'"><?php }
                                                        ?>
                                                        <span class="checkmark8"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chInfInt" class="container9">Infecciones Intrahospitalarias
                                                        <?php
                                                        if($coInfInt == 'true'){?><input type="checkbox" id="chInfInt" name="chInfInt" checked="checked" value="on"
                                                                                         onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chInfInt" name="chInfInt" value="off"
                                                                      onclick="this.value = 'on'"><?php }
                                                        ?>
                                                        <span class="checkmark9"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chCopas" class="container8">COPASST
                                                        <?php
                                                        if($coCopa == 'true'){?><input type="checkbox" id="chCopas" name="chCopas" checked="checked" value="on"
                                                                                       onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chCopas" name="chCopas" value="off"
                                                                      onclick="this.value = 'on'"><?php }
                                                        ?>
                                                        <span class="checkmark8"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chMedTra" class="container9">Medicina Transfusional
                                                        <?php
                                                        if($coMedTra == 'true'){?><input type="checkbox" id="chMedTra" name="chMedTra" checked="checked" value="on"
                                                                                         onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chMedTra" name="chMedTra" value="off"
                                                                      onclick="this.value = 'on'; chValues(this.checked,'cualRol')"><?php }
                                                        ?>
                                                        <span class="checkmark9"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chCrede" class="container8">Credencialización
                                                        <?php
                                                        if($coCrede == 'true'){?><input type="checkbox" id="chCrede" name="chCrede" checked="checked" value="on"
                                                                                        onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chCrede" name="chCrede" value="off"
                                                                      onclick="this.value = 'on'; chValues(this.checked,'cualRol')"><?php }
                                                        ?>
                                                        <span class="checkmark8"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chMeCon" class="container9">Mejoramiento Continuo
                                                        <?php
                                                        if($coMeCon == 'true'){?><input type="checkbox" id="chMeCon" name="chMeCon" checked="checked" value="on"
                                                                                        onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chMeCon" name="chMeCon" value="off"
                                                                      onclick="this.value = 'on'; chValues(this.checked,'cualRol')"><?php }
                                                        ?>
                                                        <span class="checkmark9"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chEtiIn" class="container8">Etica en Investigación
                                                        <?php
                                                        if($coEtiIn == 'true'){?><input type="checkbox" id="chEtiIn" name="chEtiIn" checked="checked" value="on"
                                                                                        onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chEtiIn" name="chEtiIn" value="off"
                                                                      onclick="this.value = 'on'; chValues(this.checked,'cualRol')"><?php }
                                                        ?>
                                                        <span class="checkmark8"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chMoSe" class="container9">Movilidad y Seguridad Sostenible
                                                        <?php
                                                        if($coMoSe == 'true'){?><input type="checkbox" id="chMoSe" name="chMoSe" checked="checked" value="on"
                                                                                       onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chMoSe" name="chMoSe" value="off"
                                                                      onclick="this.value = 'on'; chValues(this.checked,'cualRol')"><?php }
                                                        ?>
                                                        <span class="checkmark9"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chEtiHo" class="container8">Etica Hospitalaria
                                                        <?php
                                                        if($coEtiHo == 'true'){?><input type="checkbox" id="chEtiHo" name="chEtiHo" checked="checked" value="on"
                                                                                        onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chEtiHo" name="chEtiHo" value="off"
                                                                      onclick="this.value = 'on'; chValues(this.checked,'cualRol')"><?php }
                                                        ?>
                                                        <span class="checkmark8"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chSePac" class="container9">Seguridad del Paciente
                                                        <?php
                                                        if($coSePac == 'true'){?><input type="checkbox" id="chSePac" name="chSePac" checked="checked" value="on"
                                                                                        onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chSePac" name="chSePac" value="off"
                                                                      onclick="this.value = 'on'; chValues(this.checked,'cualRol')"><?php }
                                                        ?>
                                                        <span class="checkmark9"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chEvCal" class="container8">Evaluación de la Calidad en la Atención Médica
                                                        <?php
                                                        if($coEvCal == 'true'){?><input type="checkbox" id="chEvCal" name="chEvCal" checked="checked" value="on"
                                                                                        onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chEvCal" name="chEvCal" value="off"
                                                                      onclick="this.value = 'on'; chValues(this.checked,'cualRol')"><?php }
                                                        ?>
                                                        <span class="checkmark8"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chTransp" class="container9">Transplantes
                                                        <?php
                                                        if($coTransp == 'true'){?><input type="checkbox" id="chTransp" name="chTransp" checked="checked" value="on"
                                                                                         onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chTransp" name="chTransp" value="off"
                                                                      onclick="this.value = 'on'; chValues(this.checked,'cualRol')"><?php }
                                                        ?>
                                                        <span class="checkmark9"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chFarTe" class="container8">Farmacia y Terapéutica
                                                        <?php
                                                        if($coFarTe == 'true'){?><input type="checkbox" id="chFarTe" name="chFarTe" checked="checked" value="on"
                                                                                        onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chFarTe" name="chFarTe" value="off"
                                                                      onclick="this.value = 'on'; chValues(this.checked,'cualRol')"><?php }
                                                        ?>
                                                        <span class="checkmark8"></span>
                                                    </label>
                                                </td>
                                                <td>
                                                    <label for="chViEpi" class="container9">Vigilancia Epidemiológica
                                                        <?php
                                                        if($coViEpi == 'true'){?><input type="checkbox" id="chViEpi" name="chViEpi" checked="checked" value="on"
                                                                                        onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chViEpi" name="chViEpi" value="off"
                                                                      onclick="this.value = 'on'; chValues(this.checked,'cualRol')"><?php }
                                                        ?>
                                                        <span class="checkmark9"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <label for="chGesAm" class="container8">Gestión Ambiental
                                                        <?php
                                                        if($coGesAm == 'true'){?><input type="checkbox" id="chGesAm" name="chGesAm" checked="checked" value="on"
                                                                                        onclick="this.value = 'off'"><?php }
                                                        else{?><input type="checkbox" id="chGesAm" name="chGesAm" value="off"
                                                                      onclick="this.value = 'on'; chValues(this.checked,'cualRol')"><?php }
                                                        ?>
                                                        <span class="checkmark8"></span>
                                                    </label>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!------------------------ GUARDADO GENERAL: ------------------------->

                        <div id="divSaveGen" align="center">
                            <input type="hidden" id="funcion20" name="funcion20" value="saveGeneral">
                            <input type="hidden" id="funcion21" name="funcion21" value="saveVivienda">
                            <input type="hidden" id="funcion22" name="funcion22" value="saveOtros">
                            <input type="hidden" id="tablaUser" name="tablaUser" value="<?php echo $tbInfoEmpleado?>">
                            <input type="hidden" id="userMtx" name="userMtx" value="<?php echo $userTal ?>">
                            <button type="submit" class="btn btn-default btn-lg"
                                    onclick="saveVivi(funcion21,tablaUser,userMtx,tenVivi,tipVivi,tienTerr,tienLote,estVivi,chAcue,chAlca,chAseo,chEner,chInter,chGas,
                                  chTele,credito,chBici,chBus,chCamina,chPart,chMetro,chMoto,chOtroT,chTaxi,chContra,otroTrans,chTrae,chComBoca,
                                  chComOtros,chCasa,chOtrosAl,otroTalmu,hobbie,lugparq,locker,chAudit,chComite,chBrigada,chOtroRol,cualRol,Idefnc,
                                  Idegen,Ideced,Idepas,Idevis,estcivUser,Idedir,lunaUser,estUser,munUser,barUser,telUser,celUser,corUser,tisanUser,
                                  extUser,chCoCon,chGesIn,chCoDoc,chGesTe,chCoInv,chHisTe,chCoCom,chInfInt,chCopas,chMedTra,chCrede,chMeCon,chEtiIn,
                                  chMoSe,chEtiHo,chSePac,chEvCal,chTransp,chFarTe,chViEpi,chGesAm,contEmer,Ideraz,timeDesp,turnEmp,chTorBol,chTorPla,
                                  chTorVol,chTorBal,chTorTen,chCamin,chBaile,chYoga,chEnPare,chCiclo,chMara,chTarHob,chGruTea,chArtPla,chManual,
                                  chGastro,chClaIng,chConPle,chTarPic,chOtrAct,diaActi,horActi,actExtra,otraExtra,ranSal,telEmer,chViArr,chViPag,
                                  chAlime,chSerPu,chTrpte,chEdPro,chEdHij,chPaCre,chReTli,chVestu,chSalud,chPaCel,chPaTar,chCoTec,chCuPer,chOtGas,
                                  chDeuSi,chPCohi,chDiEco,chDeMif,chHiEmb,chSeDiv,chViInt,chAdLic,chMuSer,chEnGra,chNiSit,posFam,chAbuNi,chPaMad,
                                  chVecin,chGuIns,chEmDom,chUnFam,chQuSol,chCuOtr,subViv,ahoViv,montoHa,chInuVi,chContVi,chRiAmVi,chRiEsvi,chRiSaVi,
                                  chRiPuVi,chNoFaVi,chMeEst,chMeMue,chMeEle,chMePis,chMePar,chMeCol,chMeHum,chMeFac,chMeTec,chMeBan,chMeCoc,chMeAmp,
                                  chMeNot,chPfCah,chPfCuc,chPfTac,chPfCco,chPfChi,chPfCve,chPfInv,chPfSeg,chPfNin,chMcViv,chMcTec,chMcMue,chMcEle,
                                  chMcVeh,chMcSal,chMcCir,chMcTur,chMcLib,chMcGah,chMcTac,chMcEdp,chMcEdf,chMcCem,chMcNin,chEpBan,chEpFon,chEpFmu,
                                  chEpPad,chEpFam,chEpCal,chEpCaj,chEpEla,chEpNat,chEpOtr,chEpNin,chLcViv,chLcVeh,chLcSal,chLcCir,chLcTur,chLcEdf,
                                  chLcEdp,chLcCre,chLcMej,chLcCro,chLcLib,chLcTar,chLcNin,chIaInv,chIaBan,chIaNat,chIaCac,chIaFem,chIaFmu,chIaFvp,
                                  chIaOtr,chIaNin,chNfCap,chNfDes,chNfRel,chNfMan,chNfFin,chNfFor,chNfIdi,chNfInf,chNfFco,chNfOtr,chNfNot,chHoCin,
                                  chHoDep,chHoVid,chHoVte,chHoNav,chHoIce,chHoIpa,chHoIfi,chHoCex,chHoDes,chHoJar,chHoCon,chHoPin,chHoEsc,chHoFot,
                                  chHoVmu,chHoVbi,chHoEsp,chHoDan,chHoTin,chHoCoc,chHoMan,chHoOtr,chHoNin,chQpHij,chQpAmi,chQpMas,chQpSol,chQpFam,
                                  chQpAmo,chQpPar,chQpCom,chQpOtr,chHhCin,chHhDep,chHhVid,chHhVte,chHhNav,chHhIce,chHhIpa,chHhIfi,chHhCex,chHhDes,
                                  chHhJar,chHhCon,chHhPin,chHhEsc,chHhFot,chHhVmu,chHhVbi,chHhEsp,chHhDan,chHhTin,chHhCoc,chHhMan,chHhOtr,chHhNin,
                                  chBuFdi,chBuNcd,chBuDap,chBuFmo,chBuNdt,chBuOtr,chBuNin)">
                                <span class="glyphicon glyphicon-floppy-save" aria-hidden="true" style="width: 50px;"></span> Guardar
                            </button>
                        </div>
                    </div>
                </div>
                    <input type="hidden" id="codNewUser" name="codNewUser" value="<?php echo $wuse ?>">
                    <script>
                        //if(Ideced == null){basicData('addNewUser',codNewUser)}
                    </script>
            <?php
        }
        ?>
</body>
</html>
