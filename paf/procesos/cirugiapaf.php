<!DOCTYPE html>
<html lang="esp" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->Para que muestre eñes y tildes -->
    <meta charset="utf-8">
    <title>Auditoría - PAF</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="estilospaf.css" rel="stylesheet">
    <link href="CssAcordeonpaf.css" rel="stylesheet">
    <script src="JsAcordeonpaf.js"></script>
    <script src="JsAcordeonpaf2.js"></script>
    <script src="JsProcesospaf.js"></script>
    <script>
        $(function() {
            $( "#accordion" ).accordion();
        });
    </script> <!--ACORDEON 1-->
    <script>
        $(function() {
            $( "#accordion2" ).accordion();
        });
    </script> <!--ACORDEON 2-->
    <script>
        $(function() {
            $( "#accordion3" ).accordion();
        });
    </script> <!--ACORDEON 3-->
    <script>
        $(function() {
            $( "#accordion4" ).accordion();
        });
    </script> <!--ACORDEON 4-->
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
    </script> <!--VERIFICACION DE BROWSER-->
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
    ?>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /> <!--Estilo para el calendario-->
    <script src="calendariopaf.js" type="text/javascript"></script>
    <script>
    $(function() {
        $( "#datepicker1" ).datepicker(); $( "#datepicker2" ).datepicker();
        $( "#datepicker3" ).datepicker(); $( "#datepicker4" ).datepicker();
        $( "#datepicker5" ).datepicker(); $( "#datepicker6" ).datepicker();
        $( "#datepicker7" ).datepicker(); $( "#datepicker8" ).datepicker();
        $( "#datepicker9" ).datepicker(); $( "#datepicker10" ).datepicker();
        $( "#datepicker11" ).datepicker(); $( "#datepicker12" ).datepicker();
        $( "#datepicker13" ).datepicker(); $( "#datepicker14" ).datepicker();
        $( "#datepicker15" ).datepicker(); $( "#datepicker16" ).datepicker();
        $( "#datepicker17" ).datepicker(); $( "#datepicker18" ).datepicker();
        $( "#datepicker19" ).datepicker(); $( "#datepicker20" ).datepicker();
        $( "#datepicker21" ).datepicker(); $( "#datepicker22" ).datepicker();
        $( "#datepicker23" ).datepicker(); $( "#datepicker24" ).datepicker();
    });
    </script> <!--Calendarios-->
    <script>
        function validarFecha()
        {
            var fecha_ingreso = document.getElementById('fecha_ingreso').value;
            var fecha_Egreso = document.getElementById('datepicker1').value;

            inicio= new Date(fecha_ingreso);
            finalq= new Date(fecha_Egreso);
            if(inicio>finalq)
            {
                alert('La fecha de Egreso no puede ser menor que la fecha de Ingreso');
                document.getElementById('datepicker1').value = '';
                document.getElementById('tipo_egreso').value = '';
                document.getElementById('tipo_egreso').disabled = true;
            }
            else
            {
                document.getElementById('tipo_egreso').disabled = false;
                document.getElementById('datepicker1').title = 'SELECCIONE FECHA';
            }
        }

        function validarFecha2()
        {
            var fecha_ingreso = document.getElementById('fecha_ingreso').value;
            var fecha_paf = document.getElementById('datepicker2').value;

            fecha1 = new Date(fecha_ingreso);
            fecha2 = new Date(fecha_paf);

            if(fecha1 > fecha2)
            {
                alert('La fecha de Ingreso al PAF no puede ser inferior a la fecha de Ingreso');
                document.getElementById('datepicker2').value = '';
                document.getElementById('datepicker3').value = '';
                document.getElementById('datepicker3').title = 'INGRESO AL PAF ES REQUERIDO';
                document.getElementById('datepicker3').disabled = true;
                document.getElementById('datepicker7').disabled = true;
            }
            else
            {
                document.getElementById('datepicker3').disabled = false;
                document.getElementById('datepicker3').title = 'SELECCIONE FECHA';
                document.getElementById('datepicker7').disabled = false;
                document.getElementById('datepicker7').readOnly = true;
                document.getElementById('datepicker7').title = 'SELECCIONE FECHA';
            }
        }

        function validarFecha3()
        {
            var fecha_paf = document.getElementById('datepicker2').value;
            var retiro_paf = document.getElementById('datepicker3').value;

            fecha1 = new Date(fecha_paf);
            fecha2 = new Date(retiro_paf);

            if(fecha1 > fecha2)
            {
                alert('La Fecha de Retiro del PAF no puede ser inferior a la Fecha de Ingreso al PAF');
                document.getElementById('datepicker3').value = '';
                document.getElementById('datepicker4').value = '';
                document.getElementById('datepicker4').title = 'RETIRO DEL PAF ES REQUERIDO';
                document.getElementById('datepicker4').disabled = true;
            }
            else
            {
                document.getElementById('datepicker4').disabled = false;
                document.getElementById('datepicker4').title = 'SELECCIONE FECHA';
            }
        }

        function validarFecha4()
        {
            var retiro_paf = document.getElementById('datepicker3').value;
            var reintegro_paf = document.getElementById('datepicker4').value;

            fecha1 = new Date(retiro_paf);
            fecha2 = new Date(reintegro_paf);

            if(fecha1 > fecha2)
            {
                alert('El Reintegro al PAF no puede ser inferior a la Fecha de Retiro del PAF');
                document.getElementById('datepicker4').value = '';
                document.getElementById('datepicker5').value = '';
                document.getElementById('datepicker5').title = 'REINTEGRO AL PAF ES REQUERIDO';
                document.getElementById('datepicker5').disabled = true;
            }
            else
            {
                document.getElementById('datepicker5').disabled = false;
                document.getElementById('datepicker5').title = 'SELECCIONE FECHA';
            }
        }

        function validarFecha5()
        {
            var reintegro_paf = document.getElementById('datepicker4').value;
            var retiro_paf2 = document.getElementById('datepicker5').value;

            fecha1 = new Date(reintegro_paf);
            fecha2 = new Date(retiro_paf2);

            if(fecha1 > fecha2)
            {
                alert('El Segundo Retiro del PAF no puede ser inferior a la Fecha de Reintegro al PAF');
                document.getElementById('datepicker5').value = '';
            }
        }

        function validarFecha6()
        {
            var fecha_ingreso = document.getElementById('fecha_ingreso').value;
            var fecha_egreso = document.getElementById('datepicker1').value;
            var indicacion_cx = document.getElementById('datepicker6').value;

            fecha1 = new Date(fecha_ingreso);
            fecha2 = new Date(fecha_egreso);
            fecha3 = new Date(indicacion_cx);

            if((fecha3 > fecha2) || (fecha3 < fecha1))
            {
                alert('La Indicacion de la cirugia no debe ser inferior a la Fecha de Ingreso ni superior a la Fecha de Egreso');
                document.getElementById('datepicker6').value = ''
            }
        }

        function validarFecha7()
        {
            var fecha_paf = document.getElementById('datepicker2').value;
            var fecha_cx = document.getElementById('datepicker7').value;

            fecha1 = new Date(fecha_paf);
            fecha2 = new Date(fecha_cx);

            if(fecha2 < fecha1)
            {
                alert('La Fecha de la Cirugia no puede ser inferior a la Fecha de Ingreso al PAF');
                document.getElementById('datepicker7').value = '';
            }
            else
            {
                document.getElementById('datepicker7').title = 'SELECCIONE FECHA';
            }
        }

        function validarFecha8()
        {
            var fecha_ingreso = document.getElementById('fecha_ingreso').value;
            var fecha_egreso = document.getElementById('datepicker1').value;
            var fecha_reint = document.getElementById('datepicker8').value;

            fecha1 = new Date(fecha_ingreso);
            fecha2 = new Date(fecha_egreso);
            fecha = new Date(fecha_reint);

            if((fecha3 > fecha2) || (fecha3 < fecha1))
            {
                alert('La Fecha de Reintervencion no debe ser inferior a la Fecha de Ingreso ni superior a la Fecha de Egreso');
                document.getElementById('datepicker8').value = '';
            }
        }

        function validarFecha9()
        {
            var fecha_ingreso = document.getElementById('fecha_ingreso').value;
            var fecha_egreso = document.getElementById('datepicker1').value;
            var ingreso_uci = document.getElementById('datepicker9').value;

            fecha1 = new Date(fecha_ingreso);
            fecha2 = new Date(fecha_egreso);
            fecha3 = new Date(ingreso_uci);

            if((fecha3 > fecha2) || (fecha3 < fecha1))
            {
                alert('La Fecha de Ingreso a UCI no debe ser inferior a la Fecha de Ingreso ni superior a la Fecha de Egreso');
                document.getElementById('datepicker9').value = '';
                document.getElementById('datepicker10').value = '';
                document.getElementById('datepicker10').disabled = true;
                document.getElementById('datepicker10').title = 'INGRESO A UCI ES REQUERIDO';
            }
            else
            {
                document.getElementById('datepicker10').disabled = false;
                document.getElementById('datepicker10').readOnly = true;
                document.getElementById('datepicker10').title = 'SELECCIONE FECHA';
            }
        }

        function validarFecha10()
        {
            var ingreso_uci = document.getElementById('datepicker9').value;
            var egreso_uci = document.getElementById('datepicker10').value;

            fecha1 = new Date(ingreso_uci);
            fecha2 = new Date(egreso_uci);

            if(fecha2 < fecha1)
            {
                alert('La Fecha de Egreso de UCI no debe ser inferior a la Fecha de Ingreso a UCI');
                document.getElementById('datepicker10').value = '';
                document.getElementById('datepicker11').value = '';
                document.getElementById('datepicker11').disabled = true;
                document.getElementById('datepicker11').title = 'EGRESO DE UCI ES REQUERIDO';
            }
            else
            {
                document.getElementById('datepicker11').disabled = false;
                document.getElementById('datepicker11').readOnly = true;
                document.getElementById('datepicker11').title = 'SELECCIONE FECHA';
            }
        }

        function validarFecha11()
        {
            var egreso_uci = document.getElementById('datepicker10').value;
            var reingreso_uci = document.getElementById('datepicker11').value;

            fecha1 = new Date(egreso_uci);
            fecha2 = new Date(reingreso_uci);

            if(fecha2 < fecha1)
            {
                alert('La Fecha de Reingreso a UCI no debe ser superior a la Fecha de Egreso de UCI');
                document.getElementById('datepicker11').value = '';
                document.getElementById('datepicker12').value = '';
                document.getElementById('datepicker12').disabled = true;
                document.getElementById('datepicker12').title = 'REINGRESO A UCI ES REQUERIDO';
            }
            else
            {
                document.getElementById('datepicker12').disabled = false;
                document.getElementById('datepicker12').readOnly = true;
                document.getElementById('datepicker12').title = 'SELECCIONE FECHA';
            }
        }

        function validarFecha12()
        {
            var reingreso_uci = document.getElementById('datepicker11').value;
            var egreso2_uci = document.getElementById('datepicker12').value;

            fecha1 = new Date(reingreso_uci);
            fecha2 = new Date(egreso2_uci);

            if(fecha2 < fecha1)
            {
                alert('La Fecha del Segundo Egreso de UCI no debe ser superior a la Fecha del Reingreso a UCI');
                document.getElementById('datepicker12').value = '';
            }
        }

        function validarFecha13()
        {
            var fecha_ingreso = document.getElementById('fecha_ingreso').value;
            var fecha_egreso = document.getElementById('datepicker1').value;
            var indicacion_hemod = document.getElementById('datepicker13').value;

            fecha1 = new Date(fecha_ingreso);
            fecha2 = new Date(fecha_egreso);
            fecha3 = new Date(indicacion_hemod);

            if((fecha3 > fecha2) || (fecha3 < fecha1))
            {
                alert('La Indicacion de Hemodinamia no debe ser inferior a la Fecha de Ingreso del paciente ni superior a la Fecha de Egreso');
                document.getElementById('datepicker13').value = '';
                document.getElementById('datepicker14').value = '';
                document.getElementById('datepicker14').disabled = true;
                document.getElementById('datepicker14').title = 'INDICACION DE HEMODINAMIA ES REQUERIDA';
            }
            else
            {
                document.getElementById('datepicker14').disabled = false;
                document.getElementById('datepicker14').readOnly = true;
                document.getElementById('datepicker14').title = 'SELECCIONE FECHA';
            }
        }

        function validarFecha14()
        {
            var indicacion_hemod = document.getElementById('datepicker13').value;
            var fecha_hemod = document.getElementById('datepicker14').value;

            fecha1 = new Date(indicacion_hemod);
            fecha2 = new Date(fecha_hemod);

            if(fecha2 < fecha1)
            {
                alert('La Fecha de realizacion del procedimiento de Hemodinamia no debe ser inferior a la Fecha de su Indicacion');
                document.getElementById('datepicker14').value = '';
                document.getElementById('datepicker15').value = '';
                document.getElementById('datepicker15').disabled = true;
                document.getElementById('datepicker15').title = 'LA FECHA DE REALIZACION DEL PRIMER PROCEDIMIENTO ES REQUERIDA'
            }
            else
            {
                document.getElementById('datepicker15').disabled = false;
                document.getElementById('datepicker15').readOnly = true;
                document.getElementById('datepicker15').title = 'SELECCIONE FECHA';
            }
        }

        function validarFecha15()
        {
            var fecha_hemod = document.getElementById('datepicker14').value;
            var indicacion_hemod2 = document.getElementById('datepicker15').value;

            fecha1 = new Date(fecha_hemod);
            fecha2 = new Date(indicacion_hemod2);

            if(fecha2 < fecha1)
            {
                alert('La Indicacion del segundo procedimiento de Hemodinamia no debe ser inferior a la Fecha de Realizacion del primer procedimiento');
                document.getElementById('datepicker15').value = '';
                document.getElementById('datepicker16').value = '';
                document.getElementById('datepicker16').disabled = true;
                document.getElementById('datepicker16').title = 'LA INDICACION DEL SEGUNDO PROCEDIMIENTO ES REQUERIDA';
            }
            else
            {
                document.getElementById('datepicker16').disabled = false;
                document.getElementById('datepicker16').readOnly = true;
                document.getElementById('datepicker16').title = 'SELECCIONE FECHA';
            }
        }

        function validarFecha16()
        {
            var indicacion_hemod2 = document.getElementById('datepicker15').value;
            var fecha_hemod2 = document.getElementById('datepicker16').value;

            fecha1 = new Date(indicacion_hemod2);
            fecha2 = new Date(fecha_hemod2);

            if(fecha2 < fecha1)
            {
                alert('La Fecha de realizacion del segundo procedimiento de Hemodinamia no debe ser inferior a la Fecha de su Indicacion ');
                document.getElementById('datepicker16').value = '';
            }
        }

        function validarFecha17()
        {
            var fecha_ingreso = document.getElementById('fecha_ingreso').value;
            var fecha_egreso = document.getElementById('datepicker1').value;
            var indicacion_electrof = document.getElementById('datepicker17').value;

            fecha1 = new Date(fecha_ingreso);
            fecha2 = new Date(fecha_egreso);
            fecha3 = new Date(indicacion_electrof);

            if((fecha3 > fecha2) || (fecha3 < fecha1))
            {
                alert('La Indicacion de Electrofisiologia no debe ser inferior a la Fecha de Ingreso del paciente ni superior a la Fecha de Egreso');
                document.getElementById('datepicker17').value = '';
                document.getElementById('datepicker18').value = '';
                document.getElementById('datepicker18').disabled = true;
                document.getElementById('datepicker18').title = 'INDICACION DE ELECTROFISIOLOGIA ES REQUERIDA';
            }
            else
            {
                document.getElementById('datepicker18').disabled = false;
                document.getElementById('datepicker18').readOnly = true;
                document.getElementById('datepicker18').title = 'SELECCIONE FECHA';
            }
        }

        function validarFecha18()
        {
            var indicacion_electrof = document.getElementById('datepicker17').value;
            var fecha_electrof = document.getElementById('datepicker18').value;

            fecha1 = new Date(indicacion_electrof);
            fecha2 = new Date(fecha_electrof);

            if(fecha2 < fecha1)
            {
                alert('La Fecha de realizacion del procedimiento de Electrofisiologia no debe ser inferior a la Fecha de su Indicacion');
                document.getElementById('datepicker18').value = '';
                document.getElementById('datepicker19').value = '';
                document.getElementById('datepicker19').disabled = true;
                document.getElementById('datepicker19').title = 'LA FECHA DE REALIZACION DEL PRIMER PROCEDIMIENTO ES REQUERIDA'
            }
            else
            {
                document.getElementById('datepicker19').disabled = false;
                document.getElementById('datepicker19').readOnly = true;
                document.getElementById('datepicker19').title = 'SELECCIONE FECHA';
            }
        }

        function validarFecha19()
        {
            var fecha_electrof = document.getElementById('datepicker18').value;
            var indicacion_electrof2 = document.getElementById('datepicker19').value;

            fecha1 = new Date(fecha_electrof);
            fecha2 = new Date(indicacion_electrof2);

            if(fecha2 < fecha1)
            {
                alert('La Indicacion del segundo procedimiento de Electrofisiologia no debe ser inferior a la Fecha de Realizacion del primer procedimiento');
                document.getElementById('datepicker19').value = '';
                document.getElementById('datepicker20').value = '';
                document.getElementById('datepicker20').disabled = true;
                document.getElementById('datepicker20').title = 'LA INDICACION DEL SEGUNDO PROCEDIMIENTO ES REQUERIDA';
            }
            else
            {
                document.getElementById('datepicker20').disabled = false;
                document.getElementById('datepicker20').readOnly = true;
                document.getElementById('datepicker20').title = 'SELECCIONE FECHA';
            }
        }

        function validarFecha20()
        {
            var indicacion_electrof2 = document.getElementById('datepicker19').value;
            var fecha_electrof2 = document.getElementById('datepicker20').value;

            fecha1 = new Date(indicacion_electrof2);
            fecha2 = new Date(fecha_electrof2);

            if(fecha2 < fecha1)
            {
                alert('La Fecha de realizacion del segundo procedimiento de Electrofisiologia no debe ser inferior a la Fecha de su Indicacion');
                document.getElementById('datepicker20').value = '';
            }
        }
    </script> <!--Validacion Fechas-->
    <script type="text/javascript">
        $(function() {
            $( "#Tabs1" ).tabs();
        });
    </script> <!--Pestañas-->
    <?php
    include ("paf/librarypaf.php");

    $habitacion = $_POST['habitacion'];
    $historia = $_POST['historia'];
    $ingreso = $_POST['ingreso'];
    $fecha_ing = $_POST['fecha_ing'];
    $nombre = $_POST['nombre'];
    $servicio = $_POST['servicio'];
    $responsable = $_POST['responsable'];
    $nom_responsable = $_POST['nom_responsable'];
    $sexo = $_POST['sexo'];
    $edad = $_POST['edad'];
    $fechaNac = $_POST['fechaNac'];

    list(
        $H_medico, $H_Fecha_data, $H_Hora_data, $H_fecha_Ronda, $H_habitacion, $H_hc, $H_ingreso, $H_nombre_Pac, $H_servicio, $H_sexo, $H_fecha_Ing,
        $H_reingreso, $H_dx, $H_comorb, $H_qx, $H_fecha_Egreso, $H_fecha_Paf, $H_retiro_Paf, $H_reintegro_Paf, $H_retiro_Paf2, $H_prog_Ambu, $H_indicacion_Cx,
        $H_fecha_Cx, $H_cx1, $H_cx2, $H_cx3, $H_fecha_Reint, $H_reint1, $H_reint2, $H_reint3, $H_ingreso_Uci, $H_egreso_Uci, $H_reingreso_Uci, $H_egreso2_Uci,
        $H_prog_Hemo, $H_indicacion_Hemod, $H_fecha_Hemod, $H_interv_Hemod, $H_prog_Electrof,$H_indicacion_Electrof, $H_fecha_Electrof, $H_interv_Electrof,
        $H_iso, $H_observacion, $H_nota, $H_responsable, $H_fechanac, $H_alerta, $H_tipo_Egreso, $H_indicacion_Hemod2, $H_fecha_Hemod2, $H_interv_Hemod2,
        $H_indicacion_Electrof2, $H_fecha_Electrof2, $H_interv_Electrof2, $H_iaas, $H_Seguridad
        ) = buscarPaciente($historia,$ingreso);

    list($E_cedula_paciente, $E_telefono_paciente, $E_direccion_paciente) = cedulaPaciente($historia);

    list($D1_dx)=mostrarDx($historia,$ingreso);
    $DX1 = str_replace("option value=P-. sca:=","", $D1_dx);
    $DX2 = str_replace(".<option value=C-.","", $DX1);
    $DX3 = str_replace("</option><","", $DX2);
    $DX4 = str_replace(".<option value=P-.","", $DX3);
    $DX5 = str_replace("DOLOR toracico=en= estudio=>C-.","", $DX4);
    $DX6 = str_replace("option value=P-.ITU?>P-.ITU?","", $DX5);
    $DX7 = str_replace("option value=C-.","", $DX6);
    $DX8 = str_replace("</option>","", $DX7);
    $DX9 = str_replace("angina= inestable=vs= im=sest=>P-. SCA:  ",", ", $DX8);
    $DX10 = str_replace(".<urticaria>C-.","", $DX9);
    $DX11 = str_replace(">P-.",", ", $DX10);
    $DX12 = str_replace("option value=P-.","", $DX11);
    $DXFinal = str_replace("TUMOR carcinoide= de= timo= mtx=>C-.","", $DX12);

    list($Fecha_egr)=mostrarFechaEgr($historia,$ingreso); //FECHA DE EGRESO TOMADA DE 'RESUMEN DE EGRESO'
    list($Fecha_proced)=mostrarFechaProced($historia,$ingreso);
    list($cx1)=mostrarCx($historia,$ingreso);
    $cx2 = str_replace("<option value=P-P-","", $cx1);
    $cx3 = str_replace(".</option>","", $cx2);
    $cx4 = str_replace("<option value=>","", $cx3);
    $cx5 = str_replace("</option>","", $cx4);
    $cx = str_replace("<option value=S-P-","", $cx5);
    list($Fecha_ingreso_Uci)=mostrarFechaIngUCI($historia,$ingreso);
    list($Q_fecha_Hemod)=mostrarFechaHemo($historia,$ingreso);
    list($q_tipo_Egreso)=mostrarTipoEgreso($historia,$ingreso);
    ?>
</head>

<body onmousedown="validar_Browser()" onload="validar_Browser()">
<div class="container" style="margin-top: -30px; margin-left: 15px">
    <div id="loginbox" style="margin-top:50px;" class="">
        <div class="panel panel-info" >
            <div class="panel-heading">
                <div class="panel-title">Auditoría - PAF</div>
            </div>

            <div style="padding-top:30px" class="panel-body" >

                <div style="display:none" id="login-alert" class="alert alert-danger col-sm-12">
                </div>

                <form id="loginform" name="loginform" class="form-horizontal" role="form" method="post" action="guardarcxpaf.php" onsubmit="return confirm('Esta seguro de guardar en este momento?')">

                    <!-- HABITACION, HISTORIA, INGRESO -->
                    <div style="margin-bottom: 10px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon"><label>HABITACION</label></span>
                            <input id="login-username" type="text" class="form-control" style="width: 100px" name="habitacion" value="<?php echo $habitacion ?>" readonly>

                            <div class="input-group-addon" style="background-color: #ffffff; width: 50px; border: none"></div>

                            <span class="input-group-addon"><label>HISTORIA CLINICA</label></span>
                            <input id="login-username" type="text" class="form-control" style="width: 210px" name="hc" value="<?php echo $historia ?>" readonly>

                            <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                            <span class="input-group-addon"><label>INGRESO</label></span>
                            <input id="login-username" type="text" class="form-control" style="width: 75px" name="ingreso" value="<?php echo $ingreso ?>" readonly>

                            <div class="input-group-addon" style="background-color: #ffffff; width: 10px; border: none"></div>
                            <span class="input-group-addon"><a href="/matrix/hce/procesos/HCE_iframes.php?empresa=hce&origen=01&wcedula=<?php echo $E_cedula_paciente ?>&wtipodoc=CC&wdbmhos=movhos" target="_blank">Ver historia</a></span>
                        </div>
                    </div>

                    <!-- NOMBRE, EDAD, SEXO -->
                    <div style="margin-bottom: 10px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon" style="width: 110px"><label>NOMBRE</label></span>
                            <input id="login-username" type="text" class="form-control" style="width: 352px" name="nombre_pac" value="<?php echo $nombre ?>" readonly>

                            <div class="input-group-addon" style="background-color: #ffffff; width: 55px; border: none"></div>

                            <span class="input-group-addon"><label>EDAD</label></span>
                            <input id="login-username" type="text" class="form-control" style="width: 85px" name="servicio" value="<?php echo $edad ?>" readonly>

                            <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                            <span class="input-group-addon" style="width: 98px"><label>SEXO</label></span>
                            <input id="login-username" type="text" class="form-control" style="width: 75px" name="sexo" value="<?php echo $sexo ?>" readonly>
                        </div>
                    </div>

                    <!-- CEDULA, TELEFONO, IPS -->
                    <div style="margin-bottom: 10px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon" style="width: 110px"><label>CEDULA</label></span>
                            <input id="login-username" type="text" class="form-control" style="width: 212px" name="cedula_pac" value="<?php echo $E_cedula_paciente ?>" readonly>

                            <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                            <span class="input-group-addon" style="width: 180px"><label>TELEFONO</label></span>
                            <input id="login-username" type="text" class="form-control" style="width: 140px" name="telefono_pac" value="<?php echo $E_telefono_paciente ?>">

                            <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                            <span class="input-group-addon" style="width: 98px"><label>IPS</label></span>
                            <input id="login-username" type="text" class="form-control" style="width: 250px" name="ips_pac" value="">
                        </div>
                    </div>

                    <!-- DIRECCION, REINGRESO -->
                    <div style="margin-bottom: 10px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon"><label>DIRECCION</label></span>
                            <input id="login-username" type="text" class="form-control" style="width: 553px" name="direccion_pac" value="<?php echo $E_direccion_paciente ?>">

                            <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                            <span class="input-group-addon" style="width: 140px"><label>REINGRESO</label></span>
                            <?php
                            if($H_reingreso != null)
                            {
                                ?>
                                <select id="login-username" class="form-control" style="width: 100px" name="reingreso">
                                    <option>SI</option>
                                    <option>NO</option>
                                    <option> </option>
                                    <option selected><?php echo $H_reingreso ?></option>
                                </select>
                                <?php
                            }
                            else
                            {
                                ?>
                                <select id="login-username" class="form-control" style="width: 100px" name="reingreso">
                                    <option>SI</option>
                                    <option>NO</option>
                                    <option> </option>
                                    <option selected> </option>
                                </select>
                                <?php
                            }
                            ?>
                        </div>
                    </div>

                    <!-- FECHA INGRESO, FECHA EGRESO, TIPO EGRESO -->
                    <div style="margin-bottom: 10px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon"><label>FECHA DE INGRESO</label></span>
                            <input id="fecha_ingreso" type="date" class="form-control" style="width: 140px" name="fecha_ingreso" value="<?php echo $fecha_ing ?>" readonly>

                            <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                            <span class="input-group-addon" style="width: 180px"><label>FECHA DE EGRESO</label></span>
                            <input id="datepicker1" type="date" class="form-control" style="width: 140px" name="fecha_egreso" value="<?php echo $H_fecha_Egreso ?>" placeholder="aaaa-mm-dd" onchange="validarFecha()" readonly>

                            <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                            <span class="input-group-addon" style="width: 140px"><label>TIPO EGRESO</label></span>
                            <?php
                            if($H_fecha_Egreso != null)
                            {
                                if($H_tipo_Egreso != null)
                                {
                                    ?>
                                    <select id="tipo_egreso" class="form-control" style="width: 209px" name="tipo_egreso">
                                        <option>VIVO</option>
                                        <option>MUERTO</option>
                                        <option>DOMICILIARIO</option>
                                        <option>REMITIDO</option>
                                        <option> </option>
                                        <option selected><?php echo $H_tipo_Egreso ?></option>
                                    </select>
                                    <?php
                                }
                                else
                                {
                                    ?>
                                    <select id="tipo_egreso" class="form-control" style="width: 209px" name="tipo_egreso">
                                        <option>VIVO</option>
                                        <option>MUERTO</option>
                                        <option>DOMICILIARIO</option>
                                        <option>REMITIDO</option>
                                        <option> </option>
                                        <option selected><?php echo $q_tipo_Egreso ?></option>
                                    </select>
                                    <?php
                                }
                            }
                            else
                            {
                                ?>
                                <select id="tipo_egreso" class="form-control" style="width: 209px" disabled name="tipo_egreso" title="FECHA DE EGRESO ES REQUERIDA">
                                    <option>VIVO</option>
                                    <option>MUERTO</option>
                                    <option>DOMICILIARIO</option>
                                    <option>REMITIDO</option>
                                    <option> </option>
                                    <option selected> </option>
                                </select>
                                <?php
                            }
                            ?>
                        </div>
                    </div>

                    <!-- INGRESO AL PAF, RETIRO DEL PAF, ASEGURADORA -->
                    <div style="margin-bottom: 10px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon" style="width: 180px"><label>INGRESO AL PAF</label></span>
                            <?php
                            if($H_fecha_Paf != null)
                            {
                                ?>
                                <input id="datepicker2" type="text" class="form-control" style="width: 140px" name="fecha_paf" placeholder="aaaa-mm-dd" value="<?php echo $H_fecha_Paf ?>" onchange="validarFecha2()">
                                <?php
                            }
                            else
                            {
                                ?>
                                <input id="datepicker2" type="text" class="form-control" style="width: 140px" name="fecha_paf" placeholder="aaaa-mm-dd" onchange="validarFecha2()">
                                <?php
                            }
                            ?>

                            <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                            <span class="input-group-addon" style="width: 180px"><label>RETIRO DEL PAF</label></span>
                            <?php
                            if($H_fecha_Paf != null)
                            {
                                if($H_retiro_Paf != null)
                                {
                                    ?>
                                    <input id="datepicker3" type="text" class="form-control" style="width: 140px" name="retiro_paf" value="<?php echo $H_retiro_Paf ?>" placeholder="aaaa-mm-dd" onchange="validarFecha3()">
                                    <?php
                                }
                                else
                                {
                                    ?>
                                    <input id="datepicker3" type="text" class="form-control" style="width: 140px" name="retiro_paf" placeholder="aaaa-mm-dd" onchange="validarFecha3()" title="SELECCIONE FECHA">
                                    <?php
                                }
                            }
                            else
                            {
                                ?>
                                <input id="datepicker3" type="text" class="form-control" style="width: 140px" disabled name="retiro_paf" placeholder="aaaa-mm-dd" title="INGRESO AL PAF ES REQUERIDO" onchange="validarFecha3()">
                                <?php
                            }
                            ?>

                            <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                            <span class="input-group-addon" style="width: 140px"><label>ASEGURADORA:</label></span>
                            <select id="responsable2" class="form-control" style="width: 200px" name="responsable2" required>
                                <?php
                                if($H_responsable != null)
                                {
                                    $responsable = $H_responsable;

                                    if($responsable == '900156264CV')
                                    {
                                        ?>
                                        <option selected>NUEVA E.P.S PROGRAMA CARDIOV</option>
                                        <option>NUEVA E.P.S</option>
                                        <?php
                                    }
                                    elseif($responsable == '800088702CV')
                                    {
                                        ?>
                                        <option selected>EPS SURA PROGRAMA CARDIOVASCUL</option>
                                        <option>EPS SURA</option>
                                        <?php
                                    }
                                    elseif($responsable == '800130907CV')
                                    {
                                        ?>
                                        <option selected>SALUD TOTAL EPS S PROG. CARDIO</option>
                                        <option>SALUD TOTAL E.P.S.</option>
                                        <?php
                                    }
                                    elseif($responsable != '900156264CV' or $responsable != '800088702CV' or $responsable != '800130907CV')
                                    {
                                        ?>
                                        <option>NUEVA E.P.S PROGRAMA CARDIOV</option>
                                        <option>EPS SURA PROGRAMA CARDIOVASCUL</option>
                                        <option>SALUD TOTAL EPS S PROG. CARDIO</option>
                                        <option selected><?php echo $H_responsable ?></option>
                                        <?php
                                    }
                                }
                                elseif($H_responsable == '')
                                {
                                    ?>
                                    <option>NUEVA E.P.S PROGRAMA CARDIOV</option>
                                    <option>EPS SURA PROGRAMA CARDIOVASCUL</option>
                                    <option>SALUD TOTAL EPS S PROG. CARDIO</option>
                                    <option selected><?php echo $nom_responsable ?></option>
                                    <?php
                                }
                                ?>
                            </select>

                        </div>
                    </div>

                    <!-- REINTEGRO AL PAF, SEGUNDO RETIRO PAF, PACIENTE CON ALERTA -->
                    <div style="margin-bottom: 15px" class="input-group">
                        <div class="input-group">
                            <span class="input-group-addon" style="width: 180px"><label>REINTEGRO AL PAF</label></span>
                            <?php
                            if($H_retiro_Paf != null)
                            {
                                if($H_reintegro_Paf != null)
                                {
                                    ?>
                                    <input id="datepicker4" type="text" class="form-control" style="width: 140px" name="reintegro_paf" value="<?php echo $H_reintegro_Paf ?>" placeholder="aaaa-mm-dd" onchange="validarFecha4()">
                                    <?php
                                }
                                else
                                {
                                    ?>
                                    <input id="datepicker4" type="text" class="form-control" style="width: 140px" name="reintegro_paf"  placeholder="aaaa-mm-dd" onchange="validarFecha4()" title="SELECCIONE FECHA">
                                    <?php
                                }
                            }
                            else
                            {
                                ?>
                                <input id="datepicker4" type="text" class="form-control" style="width: 140px" disabled name="reintegro_paf" placeholder="aaaa-mm-dd" title="RETIRO DEL PAF ES REQUERIDO" onchange="validarFecha4()">
                                <?php
                            }
                            ?>

                            <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                            <span class="input-group-addon" style="width: 180px"><label>SEGUNDO RETIRO PAF</label></span>
                            <?php
                            if($H_reintegro_Paf != null)
                            {
                                if($H_retiro_Paf2 != null)
                                {
                                    ?>
                                    <input id="datepicker5" type="text" class="form-control" style="width: 120px" name="retiro_paf2" value="<?php echo $H_retiro_Paf2 ?>" placeholder="aaaa-mm-dd" onchange="validarFecha5()">
                                    <?php
                                }
                                else
                                {
                                    ?>
                                    <input id="datepicker5" type="text" class="form-control" style="width: 120px" name="retiro_paf2" placeholder="aaaa-mm-dd" onchange="validarFecha5()" title="SELECCIONE FECHA">
                                    <?php
                                }
                            }
                            else
                            {
                                ?>
                                <input id="datepicker5" type="text" class="form-control" style="width: 120px" disabled name="retiro_paf2" placeholder="aaaa-mm-dd" title="REINTEGRO AL PAF ES REQUERIDO" onchange="validarFecha5()">
                                <?php
                            }
                            ?>

                            <div class="input-group-addon" style="background-color: #ffffff; width: 10px; border: none"></div>

                            <span class="input-group-addon" style="background-color: #ce8483"><label style="color: ivory">Paciente con alerta:</label></span>
                            <select id="login-username" class="form-control" style="width: 70px" name="alerta">
                                <option>SI</option>
                                <option>NO</option>
                                <option selected><?php echo $H_alerta ?></option>
                            </select>
                        </div>
                    </div>

                    <!-- DIAGNOSTICO -->
                    <div style="margin-bottom: 15px; width: 1070px" class="input-group">
                        <span class="input-group-addon">
                            <label>DIAGNOSTICO</label>
                            <br>
                            <?php
                            if($H_dx != null)
                            {
                                ?>
                                <textarea id="dx" name="dx" rows="2" cols="110"><?php echo $H_dx ?></textarea>
                                <?php
                            }
                            else
                            {
                                ?>
                                <textarea id="dx" name="dx" rows="2" cols="110"><?php echo $DXFinal ?></textarea>
                                <?php
                            }
                            ?>
                        </span>
                    </div>

                    <!-- COMORBILIDADES -->
                    <div style="margin-bottom: 15px; width: 1070px" class="input-group">
                        <span class="input-group-addon">
                            <label>COMORBILIDADES</label>
                            <br>
                            <textarea id="comorb" name="comorb" rows="2" cols="110"><?php echo $H_comorb ?></textarea>
                        </span>
                    </div>

                    <!------------------------------------TERMINA CAMPOS COMUNES----------------------------------------->

                    <div id="Tabs1">
                        <ul>
                            <li><a href="#tabs-1">Auditoria Medica</a></li>
                            <li><a href="#tabs-2">Auditoria Enfermeria</a></li>
                        </ul>
                        <!--------------- PESTAÑA AUDITORIA MEDICA --------------->
                        <div id="tabs-1">

                            <!-- CIRUGIA PROGRAMADA, INDICACION, FECHA -->
                            <div style="margin-bottom: 15px" class="input-group">
                                <div class="input-group">
                                    <span class="input-group-addon" style="width: 273px"><label>CIRUGIA PROGRAMADA</label></span>
                                    <select id="login-username" class="form-control" style="width: 80px" name="prog_ambu">
                                        <option>SI</option>
                                        <option>NO</option>
                                        <option> </option>
                                        <option selected><?php echo $H_prog_Ambu ?></option>
                                    </select>

                                    <div class="input-group-addon" style="background-color: #F2F5F7; width: 30px; border: none"></div>

                                    <span class="input-group-addon" style="width: 140px"><label>INDICACION</label></span>
                                    <?php
                                    if($H_indicacion_Cx != null)
                                    {
                                        ?>
                                        <input id="datepicker6" type="text" class="form-control" style="width: 150px" name="indicacion_cx" value="<?php echo $H_indicacion_Cx ?>" onchange="validarFecha6()">
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <input id="datepicker6" type="text" class="form-control" style="width: 150px" name="indicacion_cx" placeholder="aaaa-mm-dd" onchange="validarFecha6()">
                                        <?php
                                    }
                                    ?>

                                    <div class="input-group-addon" style="background-color: #F2F5F7; width: 30px; border: none"></div>

                                    <span class="input-group-addon" style="width: 140px"><label>FECHA</label></span>
                                    <?php
                                    if($H_fecha_Paf != null)
                                    {
                                        if($H_fecha_Cx != null)
                                        {
                                            ?>
                                            <input id="datepicker7" type="text" class="form-control" style="width: 150px" name="fecha_cx" value="<?php echo $H_fecha_Cx ?>" onchange="validarFecha7()">
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <input id="datepicker7" type="text" class="form-control" style="width: 150px" name="fecha_cx" placeholder="aaaa-mm-dd" onchange="validarFecha7()">
                                            <?php
                                        }
                                    }
                                    else
                                    {
                                        ?>
                                        <input id="datepicker7" type="text" class="form-control" style="width: 150px" name="fecha_cx" placeholder="aaaa-mm-dd" onchange="validarFecha7()" title="INGRESO AL PAF ES OBLIGATORIO" disabled>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>

                            <!-- CIRUGIA1, CIRUGIA2, CIRUGIA3 -->
                            <div id="accordion" style="margin-bottom: 15px; width: 1070px" class="input-group">
                                <label style="font-weight: bold; font-size: small">CIRUGIA 1</label>
                                <div style="min-height: 100px">
                                    <?php
                                    if($H_cx1 != null)
                                    {
                                        ?>
                                        <textarea id="login-username" class="form-control" rows="2" cols="110" name="cx1"><?php echo $H_cx1 ?></textarea>
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <textarea id="login-username" class="form-control" rows="2" cols="110" name="cx1"><?php echo $cx ?></textarea>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <label style="font-weight: bold; font-size: small">CIRUGIA 2</label>
                                <div style="min-height: 110px">
                                    <textarea id="login-username" class="form-control" rows="2" cols="110" name="cx2"><?php echo $H_cx2 ?></textarea>
                                </div>
                                <label style="font-weight: bold; font-size: small">CIRUGIA 3</label>
                                <div style="min-height: 110px">
                                    <textarea id="login-username" class="form-control" rows="2" cols="110" name="cx3"><?php echo $H_cx3 ?></textarea>
                                </div>
                            </div>
                            <br>
                            <!-- FECHA REINTERVENCION -->
                            <div style="margin-bottom: 15px" class="input-group">
                                <div class="input-group">
                                    <span class="input-group-addon" style="width: 270px"><label>FECHA REINTERVENCION</label></span>
                                    <input id="datepicker8" type="text" class="form-control" style="width: 150px" name="fecha_reint" value="<?php echo $H_fecha_Reint ?>" onchange="validarFecha8()" placeholder="aaaa-mm-dd" readonly>
                                </div>
                            </div>

                            <!-- REINTERVENCION, REINTERVENCION2, REINTERVENCION3 -->
                            <div id="accordion2" style="margin-bottom: 15px; width: 1070px" class="input-group">
                                <label style="font-weight: bold; font-size: small">REINTERVENCION</label>
                                <div style="min-height: 110px">
                                    <textarea id="login-username" class="form-control" rows="2" cols="110" name="reint1"><?php echo $H_reint1 ?></textarea>
                                </div>
                                <label style="font-weight: bold; font-size: small">REINTERVENCION 2</label>
                                <div style="min-height: 110px">
                                    <textarea id="login-username" class="form-control" rows="2" cols="110" name="reint2"><?php echo $H_reint2 ?></textarea>
                                </div>
                                <label style="font-weight: bold; font-size: small">REINTERVENCION 3</label>
                                <div style="min-height: 110px">
                                    <textarea id="login-username" class="form-control" rows="2" cols="110" name="reint3"><?php echo $H_reint3 ?></textarea>
                                </div>
                            </div>
                            <br>
                            <div id="accordion3" style="margin-bottom: 15px; width: 1070px" class="input-group">
                                <!-- UCI -->
                                <label style="font-weight: bold; font-size: small">UCI</label>
                                <!-- INGRESO, EGRESO, REINGRESO, EGRESO2 -->
                                <div class="input-group" style="max-height: 80px">
                                    <table style="width: 1000px">
                                        <tr>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-addon"><label>INGRESO</label></span>
                                                    <?php
                                                    if($H_ingreso_Uci != null)
                                                    {
                                                        ?>
                                                        <input id="datepicker9" type="text" class="form-control" style="width: 135px" name="ingreso_uci" value="<?php echo $H_ingreso_Uci ?>" onchange="validarFecha9()" placeholder="aaaa-mm-dd" readonly>
                                                        <?php
                                                    }
                                                    else
                                                    {
                                                        ?>
                                                        <input id="datepicker9" type="text" class="form-control" style="width: 135px" name="ingreso_uci" onchange="validarFecha9()" placeholder="aaaa-mm-dd" readonly>
                                                        <?php
                                                    }
                                                    ?>

                                                    <div class="input-group-addon" style="background-color: #F9FAFB; width: 30px; border: none"></div>

                                                    <span class="input-group-addon"><label>EGRESO</label></span>
                                                    <?php
                                                    if($H_ingreso_Uci != null)
                                                    {
                                                        if($H_egreso_Uci != null)
                                                        {
                                                            ?>
                                                            <input id="datepicker10" type="text" class="form-control" style="width: 135px" name="egreso_uci" value="<?php echo $H_egreso_Uci ?>" onchange="validarFecha10()" placeholder="aaaa-mm-dd" readonly>
                                                            <?php
                                                        }
                                                        else
                                                        {
                                                            ?>
                                                            <input id="datepicker10" type="text" class="form-control" style="width: 135px" name="egreso_uci" placeholder="aaaa-mm-dd" onchange="validarFecha10()" readonly>
                                                            <?php
                                                        }
                                                    }
                                                    else
                                                    {
                                                        ?>
                                                        <input id="datepicker10" type="text" class="form-control" style="width: 135px" name="egreso_uci" placeholder="aaaa-mm-dd" title="INGRESO A UCI ES REQUERIDO" onchange="validarFecha10()" disabled>
                                                        <?php
                                                    }
                                                    ?>

                                                    <div class="input-group-addon" style="background-color: #F9FAFB; width: 30px; border: none"></div>

                                                    <span class="input-group-addon"><label>REINGRESO</label></span>
                                                    <?php
                                                    if($H_egreso_Uci != null)
                                                    {
                                                        if($H_reingreso_Uci != null)
                                                        {
                                                            ?>
                                                            <input id="datepicker11" type="text" class="form-control" style="width: 135px" name="reingreso_uci" value="<?php echo $H_reingreso_Uci ?>" onchange="validarFecha11()" placeholder="aaaa-mm-dd" readonly>
                                                            <?php
                                                        }
                                                        else
                                                        {
                                                            ?>
                                                            <input id="datepicker11" type="text" class="form-control" style="width: 135px" name="reingreso_uci" placeholder="aaaa-mm-dd" onchange="validarFecha11()" readonly>
                                                            <?php
                                                        }
                                                    }
                                                    else
                                                    {
                                                        ?>
                                                        <input id="datepicker11" type="text" class="form-control" style="width: 135px" name="reingreso_uci" placeholder="aaaa-mm-dd" title="EGRESO DE UCI ES REQUERIDO" onchange="validarFecha11()" disabled>
                                                        <?php
                                                    }
                                                    ?>

                                                    <div class="input-group-addon" style="background-color: #F2F5F7; width: 30px; border: none"></div>

                                                    <span class="input-group-addon"><label>EGRESO 2</label></span>
                                                    <?php
                                                    if($H_reingreso_Uci != null)
                                                    {
                                                        if($H_egreso2_Uci != null)
                                                        {
                                                            ?>
                                                            <input id="datepicker12" type="text" class="form-control" style="width: 135px" name="egreso2_uci" value="<?php echo $H_egreso2_Uci ?>" onchange="validarFecha12()" placeholder="aaaa-mm-dd" readonly>
                                                            <?php
                                                        }
                                                        else
                                                        {
                                                            ?>
                                                            <input id="datepicker12" type="text" class="form-control" style="width: 135px" name="egreso2_uci" placeholder="aaaa-mm-dd" onchange="validarFecha12()" readonly>
                                                            <?php
                                                        }
                                                    }
                                                    else
                                                    {
                                                        ?>
                                                        <input id="datepicker12" type="text" class="form-control" style="width: 135px" name="egreso2_uci" placeholder="aaaa-mm-dd" title="REINGRESO A UCI ES REQUERIDO" onchange="validarFecha12()" disabled>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- HEMODINAMIA -->
                                <label style="font-weight: bold; font-size: small">HEMODINAMIA</label>
                                <!-- HEMODINAMIA PROGRAMADA, INDICACION, FECHA, INTERVENCION HEMODINAMIA, INDICACION HEMODINAMIA2, FECHA HEMODINAMIA2, INTERVENCION HEMODINAMIA2 -->
                                <div class="input-group" style="max-height: 80px">
                                    <table style="width: 1000px">
                                        <tr>
                                            <td>
                                                <div style="margin-bottom: -5px" class="input-group">
                                                    <span class="input-group-addon" style="width: 273px"><label>HEMODINAMIA PROGRAMADA</label></span>
                                                    <select id="login-username" class="form-control" style="width: 80px" name="prog_hemo">
                                                        <option>SI</option>
                                                        <option>NO</option>
                                                        <option> </option>
                                                        <option selected><?php echo $H_prog_Hemo ?></option>
                                                    </select>

                                                    <div class="input-group-addon" style="background-color: #F2F5F7; width: 30px; border: none"></div>

                                                    <span class="input-group-addon" style="width: 140px"><label>INDICACION</label></span>
                                                    <?php
                                                    if($H_indicacion_Hemod != null)
                                                    {
                                                        ?>
                                                        <input id="datepicker13" type="text" class="form-control" style="width: 150px" name="indicacion_hemod" value="<?php echo $H_indicacion_Hemod ?>" onchange="validarFecha13()" placeholder="aaaa-mm-dd" readonly>
                                                        <?php
                                                    }
                                                    else
                                                    {
                                                        ?>
                                                        <input id="datepicker13" type="text" class="form-control" style="width: 150px" name="indicacion_hemod" placeholder="aaaa-mm-dd" onchange="validarFecha13()" readonly>
                                                        <?php
                                                    }
                                                    ?>

                                                    <div class="input-group-addon" style="background-color: #F2F5F7; width: 30px; border: none"></div>

                                                    <span class="input-group-addon" style="width: 140px"><label>FECHA</label></span>
                                                    <?php
                                                    if($H_indicacion_Hemod != null)
                                                    {
                                                        if($H_fecha_Hemod != null)
                                                        {
                                                            ?>
                                                            <input id="datepicker14" type="text" class="form-control" style="width: 150px" name="fecha_hemod" value="<?php echo $H_fecha_Hemod ?>" onchange="validarFecha14()" placeholder="aaaa-mm-dd" readonly>
                                                            <?php
                                                        }
                                                        else
                                                        {
                                                            ?>
                                                            <input id="datepicker14" type="text" class="form-control" style="width: 150px" name="fecha_hemod" placeholder="aaaa-mm-dd" onchange="validarFecha14()" readonly>
                                                            <?php
                                                        }
                                                    }
                                                    else
                                                    {
                                                        ?>
                                                        <input id="datepicker14" type="text" class="form-control" style="width: 150px" name="fecha_hemod" placeholder="aaaa-mm-dd" title="INDICACION DE HEMODINAMIA ES REQUERIDA" onchange="validarFecha14()" disabled>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <br>
                                    <table style="width: 1000px">
                                        <tr>
                                            <td>
                                                <div style="margin-bottom: -20px" class="input-group">
                                                    <span class="input-group-addon" style="width: 280px"><label>INTERVENCION HEMODIMANIA</label></span>
                                                    <input id="login-username" type="text" class="form-control" style="width: 710px" name="interv_hemod" value="<?php echo $H_interv_Hemod ?>">
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <br><br>
                                    <table style="width: 1000px">
                                        <tr>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-addon" style="width: 278px"><label>INDICACION HEMODIMAMIA 2</label></span>
                                                    <?php
                                                    if($H_interv_Hemod != null)
                                                    {
                                                        if($H_indicacion_Hemod2 != null)
                                                        {
                                                            ?>
                                                            <input id="datepicker15" type="text" class="form-control" style="width: 175px" name="indicacion_hemod2" value="<?php echo $H_indicacion_Hemod2 ?>" onchange="validarFecha15()" placeholder="aaaa-mm-dd" readonly>
                                                            <?php
                                                        }
                                                        else
                                                        {
                                                            ?>
                                                            <input id="datepicker15" type="text" class="form-control" style="width: 175px" name="indicacion_hemod2" placeholder="aaaa-mm-dd" onchange="validarFecha15()" readonly>
                                                            <?php
                                                        }
                                                    }
                                                    else
                                                    {
                                                        ?>
                                                        <input id="datepicker15" type="text" class="form-control" style="width: 175px" name="indicacion_hemod2" placeholder="aaaa-mm-dd" title="FECHA DE REALIZACION DEL PRIMER PROCEDIMIENTO ES REQUERIDA" onchange="validarFecha15()" disabled>
                                                        <?php
                                                    }
                                                    ?>
                                                    <div class="input-group-addon" style="background-color: #F2F5F7; width: 50px; border: none"></div>
                                                    <span class="input-group-addon"><label>FECHA HEMODIMANIA 2</label></span>
                                                    <?php
                                                    if($H_indicacion_Hemod2 != null)
                                                    {
                                                        if($H_fecha_Hemod2 != null)
                                                        {
                                                            ?>
                                                            <input id="datepicker16" type="text" class="form-control" style="width: 175px" name="fecha_hemod2" value="<?php echo $H_fecha_Hemod2 ?>" onchange="validarFecha16()" placeholder="aaaa-mm-dd" readonly>
                                                            <?php
                                                        }
                                                        else
                                                        {
                                                            ?>
                                                            <input id="datepicker16" type="text" class="form-control" style="width: 175px" name="fecha_hemod2" placeholder="aaaa-mm-dd" onchange="validarFecha16()" readonly>
                                                            <?php
                                                        }
                                                    }
                                                    else
                                                    {
                                                        ?>
                                                        <input id="datepicker16" type="text" class="form-control" style="width: 175px" name="fecha_hemod2" placeholder="aaaa-mm-dd" title="INDICACION DE SEGUNDO PROCEDIMIENTO ES REQUERIDA" onchange="validarFecha16()" disabled>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <br>
                                    <table style="width: 1000px">
                                        <tr>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-addon" style="width: 280px"><label>INTERVENCION HEMODIMANIA 2</label></span>
                                                    <input id="login-username" type="text" class="form-control" style="width: 710px" name="interv_hemod2" value="<?php echo $H_interv_Hemod2 ?>">
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>

                                <!-- ELECTROFISIOLOGIA -->
                                <label style="font-weight: bold; font-size: small">ELECTROFISIOLOGIA</label>
                                <!-- ELECTROFIS. PROGRAMADA, INDICACION, FECHA, INTERVENCION ELECTROFIS., INDICACION ELECTROFIS.2, FECHA ELECTROFIS.2, INTERVENCION ELECTROFIS.2 -->
                                <div class="input-group" style="max-height: 80px">
                                    <table style="width: 1000px">
                                        <tr>
                                            <td>
                                                <div style="margin-bottom: -5px" class="input-group">
                                                    <span class="input-group-addon" style="width: 273px"><label>ELECTROFISIOLOGIA PROGRAMADA</label></span>
                                                    <select id="login-username" class="form-control" style="width: 80px" name="prog_electrof">
                                                        <option>SI</option>
                                                        <option>NO</option>
                                                        <option> </option>
                                                        <option selected><?php echo $H_prog_Electrof ?></option>
                                                    </select>

                                                    <div class="input-group-addon" style="background-color: #F2F5F7; width: 30px; border: none"></div>

                                                    <span class="input-group-addon" style="width: 325px"><label>INDICACION</label></span>
                                                    <?php
                                                    if($H_indicacion_Electrof != null)
                                                    {
                                                        ?>
                                                        <input id="datepicker17" type="text" class="form-control" style="width: 175px" name="indicacion_electrof" value="<?php echo $H_indicacion_Electrof ?>" onchange="validarFecha17()" placeholder="aaaa-mm-dd" readonly>
                                                        <?php
                                                    }
                                                    else
                                                    {
                                                        ?>
                                                        <input id="datepicker17" type="text" class="form-control" style="width: 175px" name="indicacion_electrof" placeholder="aaaa-mm-dd" onchange="validarFecha17()" readonly>
                                                        <?php
                                                    }
                                                    ?>
                                                    <div class="input-group-addon" style="background-color: #F2F5F7; width: 50px; border: none"></div>
                                                    <span class="input-group-addon"><label>FECHA</label></span>
                                                    <?php
                                                    if($H_indicacion_Electrof != null)
                                                    {
                                                        if($H_fecha_Electrof != null)
                                                        {
                                                            ?>
                                                            <input id="datepicker18" type="text" class="form-control" style="width: 175px" name="fecha_electrof" value="<?php echo $H_fecha_Electrof ?>" onchange="validarFecha18()" placeholder="aaaa-mm-dd" readonly>
                                                            <?php
                                                        }
                                                        else
                                                        {
                                                            ?>
                                                            <input id="datepicker18" type="text" class="form-control" style="width: 175px" name="fecha_electrof" placeholder="aaaa-mm-dd" onchange="validarFecha18()" readonly>
                                                            <?php
                                                        }
                                                    }
                                                    else
                                                    {
                                                        ?>
                                                        <input id="datepicker18" type="text" class="form-control" style="width: 175px" name="fecha_electrof" placeholder="aaaa-mm-dd" title="INDICACION DE ELECTROFISIOLOGIA ES REQUERIDA" onchange="validarFecha18()" disabled>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <br>
                                    <table style="width: 1000px">
                                        <tr>
                                            <td>
                                                <div style="margin-bottom: -20px" class="input-group">
                                                    <span class="input-group-addon"><label>INTERVENCION ELECTROFISIOLOGIA</label></span>
                                                    <input id="login-username" type="text" class="form-control" style="width: 665px" name="interv_electrof" value="<?php echo $H_interv_Electrof ?>">
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <br><br>
                                    <table style="width: 1000px">
                                        <tr>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-addon" style="width: 325px"><label>INDICACION ELECTROFISIOLOGIA 2</label></span>
                                                    <?php
                                                    if($H_interv_Electrof != null)
                                                    {
                                                        if($H_indicacion_Electrof2 != null)
                                                        {
                                                            ?>
                                                            <input id="datepicker19" type="text" class="form-control" style="width: 175px" name="indicacion_electrof2" value="<?php echo $H_indicacion_Electrof2 ?>" onchange="validarFecha19()" placeholder="aaaa-mm-dd" readonly>
                                                            <?php
                                                        }
                                                        else
                                                        {
                                                            ?>
                                                            <input id="datepicker19" type="text" class="form-control" style="width: 175px" name="indicacion_electrof2" placeholder="aaaa-mm-dd" onchange="validarFecha19()" readonly>
                                                            <?php
                                                        }
                                                    }
                                                    else
                                                    {
                                                        ?>
                                                        <input id="datepicker19" type="text" class="form-control" style="width: 175px" name="indicacion_electrof2" placeholder="aaaa-mm-dd" title="FECHA DE REALIZACION DEL PRIMER PROCEDIMIENTO ES REQUERIDA" onchange="validarFecha19()" disabled>
                                                        <?php
                                                    }
                                                    ?>
                                                    <div class="input-group-addon" style="background-color: #F2F5F7; width: 50px; border: none"></div>
                                                    <span class="input-group-addon"><label>FECHA ELECTROFISIOLOGIA 2</label></span>
                                                    <?php
                                                    if($H_indicacion_Electrof2 != null)
                                                    {
                                                        if($H_fecha_Electrof2 != null)
                                                        {
                                                            ?>
                                                            <input id="datepicker20" type="text" class="form-control" style="width: 175px" name="fecha_electrof2" value="<?php echo $H_fecha_Electrof2 ?>" onchange="validarFecha20()" placeholder="aaaa-mm-dd" readonly>
                                                            <?php
                                                        }
                                                        else
                                                        {
                                                            ?>
                                                            <input id="datepicker20" type="text" class="form-control" style="width: 175px" name="fecha_electrof2" placeholder="aaaa-mm-dd" onchange="validarFecha20()" readonly>
                                                            <?php
                                                        }
                                                    }
                                                    else
                                                    {
                                                        ?>
                                                        <input id="datepicker20" type="text" class="form-control" style="width: 175px" name="fecha_electrof2" placeholder="aaaa-mm-dd" title="INDICACION DE SEGUNDO PROCEDIMIENTO ES REQUERIDA" onchange="validarFecha20()" disabled>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                    <br>
                                    <table style="width: 1000px">
                                        <tr>
                                            <td>
                                                <div class="input-group">
                                                    <span class="input-group-addon"><label>INTERVENCION ELECTROFISIOLOGIA 2</label></span>
                                                    <input id="login-username" type="text" class="form-control" style="width: 665px" name="interv_electrof2" value="<?php echo $H_interv_Electrof2 ?>">
                                                </div>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            <br>

                            <!-- ISO, IAAS -->
                            <div style="margin-bottom: 15px" class="input-group">
                                <div class="input-group">
                                    <span class="input-group-addon" ><label>ISO</label></span>
                                    <select id="login-username" class="form-control" style="width: 80px" name="iso">
                                        <option>SI</option>
                                        <option>NO</option>
                                        <option> </option>
                                        <option selected><?php echo $H_iso ?></option>
                                    </select>
                                    <div class="input-group-addon" style="background-color: #F2F5F7; width: 50px; border: none"></div>
                                    <span class="input-group-addon" ><label>IAAS</label></span>
                                    <select id="login-username" class="form-control" style="width: 80px" name="iaas">
                                        <option>SI</option>
                                        <option>NO</option>
                                        <option> </option>
                                        <option selected><?php echo $H_iaas ?></option>
                                    </select>
                                </div>
                            </div>

                            <!-- OBSERVACIONES, NOTAS DEL AUDITOR -->
                            <div id="accordion4" style="margin-bottom: 15px; width: 1070px" class="input-group">
                                <label style="font-weight: bold; font-size: small">OBSERVACIONES</label>
                                <div class="input-group" style="max-height: 500px">
                                    <?php
                                    if($H_observacion != null)
                                    {
                                        ?>
                                        <textarea id="txtObservaciones" name="observacion" rows="6" cols="132" readonly><?php echo $H_observacion ?></textarea>
                                        <?php
                                    }
                                    ?>
                                    <br>
                                    <textarea id="txtObservaciones2" name="observacion2" rows="2" cols="132" placeholder="NUEVA OBSERVACION"></textarea>
                                </div>

                                <label style="font-weight: bold; font-size: small">NOTAS DEL AUDITOR</label>
                                <div class="input-group" style="max-height: 500px">
                                    <?php
                                    if($H_nota != null)
                                    {
                                        ?>
                                        <textarea id="txtObservaciones" name="nota" rows="6" cols="132" readonly><?php echo $H_nota ?></textarea>
                                        <?php
                                    }
                                    ?>
                                    <br>
                                    <textarea id="txtObservaciones2" name="nota2" rows="2" cols="132" placeholder="NUEVA NOTA"></textarea>
                                </div>
                            </div>

                            <div style="margin-bottom: 15px; display: none" class="input-group">
                                <div class="input-group">
                                    <span class="input-group-addon"><label>FECHA DE RONDA</label></span>
                                    <input id="login-username" type="text" class="form-control" style="width: 100px" name="fecha_ronda" value="">
                                </div>
                            </div>
                        </div>

                        <!--------------- PESTAÑA AUDITORIA ENFERMERIA ----------->
                        <div id="tabs-2">
                            <!-- CIRUGIAS -->
                            <div class="panel panel-info" >
                                <div class="panel-heading" style="background-color: #F0F7FB; padding-top:2px">
                                    <div class="panel-title" style="height: 10px">CIRUGIAS</div>
                                </div>

                                <!-- INDICACION CIRUGIA, FECHA CIRUGIA, FECHA DE SOLICITUD, NUMERO AUTORIZACION, CIRUGIA SOLICITADA -->
                                <div style="margin-bottom: 5px" class="panel-body">
                                    <div class="input-group">
                                        <h5>INDICACION CIRUGIA:</h5>
                                        <h5>
                                            <?php
                                            if($H_indicacion_Cx != null)
                                            {
                                                ?>
                                                <h5><?php echo $H_indicacion_Cx ?></h5>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <h5>No definida</h5>
                                                <?php
                                            }
                                            ?>
                                        </h5>

                                        <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                                        <h5>FECHA CIRUGIA:</h5>
                                        <h5>
                                            <?php
                                            if($H_fecha_Cx != null)
                                            {
                                                ?>
                                                <h5><?php echo $H_fecha_Cx ?></h5>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <h5>No definida</h5>
                                                <?php
                                            }
                                            ?>
                                        </h5>

                                        <div class="input-group-addon" style="background-color: #ffffff; width: 55px; border: none"></div>

                                        <h5>FECHA DE SOLICITUD</h5>
                                        <input id="datepicker21" type="text" class="form-control" style="width: 175px" name="" value="<?php// echo $H_ingreso_Uci ?>" placeholder="aaaa-mm-dd" readonly>

                                        <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                                        <h5>N° DE AUTORIZACION</h5>
                                        <input id="login-username" type="text" class="form-control" style="width: 160px" name="" value="<?php// echo $sexo ?>">
                                    </div>

                                    <div>
                                        <h5>CIRUGIA SOLICITADA:</h5>
                                        <?php
                                        if($H_cx1 != null)
                                        {
                                            ?>
                                            <textarea id="login-username" class="form-control" rows="2" cols="115" name="cx1_enf"><?php// echo $H_cx1 ?></textarea>
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <textarea id="login-username" class="form-control" rows="2" cols="115" name="cx1_enf"></textarea>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <!-- REINTERVENCIONES -->
                            <div class="panel panel-info">
                                <div class="panel-heading" style="background-color: #F0F7FB; padding-top:2px">
                                    <div class="panel-title" style="height: 10px">REINTERVENCIONES</div>
                                </div>

                                <!-- INDICACION REINTERVENCION, FECHA REINTERVENCION, FECHA DE SOLICITUD, NUMERO DE AUTORIZACION, REINTERVENCION SOLICITADA -->
                                <div style="margin-bottom: 5px" class="panel-body">
                                    <div class="input-group">
                                        <h5>INDICACION REINTERVENCION:</h5>
                                        <h5>
                                            <?php
                                            if($H_fecha_Reint != null)
                                            {
                                                ?>
                                                <h5><?php echo $H_fecha_Reint ?></h5>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <h5>No definida</h5>
                                                <?php
                                            }
                                            ?>
                                        </h5>

                                        <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                                        <h5>FECHA REINTERVENCION:</h5>
                                        <h5>
                                            <?php
                                            if($H_fecha_Reint != null)
                                            {
                                                ?>
                                                <h5><?php echo $H_fecha_Reint ?></h5>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <h5>No definida</h5>
                                                <?php
                                            }
                                            ?>
                                        </h5>

                                        <div class="input-group-addon" style="background-color: #ffffff; width: 55px; border: none"></div>

                                        <h5>FECHA DE SOLICITUD</h5>
                                        <input id="datepicker22" type="text" class="form-control" style="width: 175px" name="" value="<?php// echo $H_ingreso_Uci ?>" placeholder="aaaa-mm-dd" readonly>

                                        <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                                        <h5>N° DE AUTORIZACION</h5>
                                        <input id="login-username" type="text" class="form-control" style="width: 160px" name="" value="<?php// echo $sexo ?>">
                                    </div>

                                    <div>
                                        <h5>REINTERVENCION SOLICITADA:</h5>
                                        <?php
                                        if($H_reint1 != null)
                                        {
                                            ?>
                                            <textarea id="login-username" class="form-control" rows="2" cols="115" name="cx1_enf"><?php// echo $H_reint1 ?></textarea>
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <textarea id="login-username" class="form-control" rows="2" cols="115" name="cx1_enf"></textarea>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <!-- HEMODINAMIA -->
                            <div class="panel panel-info">
                                <div class="panel-heading" style="background-color: #F0F7FB; padding-top:2px">
                                    <div class="panel-title" style="height: 10px">HEMODINAMIA</div>
                                </div>

                                <!-- INDICACION HEMODINAMIA, FECHA HEMODINAMIA, FECHA DE SOLICITUD, NUMERO DE AUTORIZACION, HEMODINAMIA SOLICITADA -->
                                <div style="margin-bottom: 5px" class="panel-body">
                                    <div class="input-group">
                                        <h5>INDICACION HEMODINAMIA:</h5>
                                        <h5><?php
                                            if($H_indicacion_Hemod != null)
                                            {
                                                ?>
                                                <h5><?php echo $H_indicacion_Hemod ?></h5>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <h5>No definida</h5>
                                                <?php
                                            }
                                            ?>
                                        </h5>

                                        <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                                        <h5>FECHA HEMODINAMIA:</h5>
                                        <h5>
                                            <?php
                                            if($H_fecha_Hemod != null)
                                            {
                                                ?>
                                                <h5><?php echo $H_fecha_Hemod ?></h5>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <h5>No definida</h5>
                                                <?php
                                            }
                                            ?>
                                        </h5>

                                        <div class="input-group-addon" style="background-color: #ffffff; width: 55px; border: none"></div>

                                        <h5>FECHA DE SOLICITUD</h5>
                                        <input id="datepicker23" type="text" class="form-control" style="width: 175px" name="" value="<?php// echo $H_ingreso_Uci ?>" placeholder="aaaa-mm-dd" readonly>

                                        <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                                        <h5>N° DE AUTORIZACION</h5>
                                        <input id="login-username" type="text" class="form-control" style="width: 160px" name="" value="<?php// echo $sexo ?>">
                                    </div>

                                    <div>
                                        <h5>HEMODINAMIA SOLICITADA:</h5>
                                        <?php
                                        if($H_interv_Hemod != null)
                                        {
                                            ?>
                                            <textarea id="login-username" class="form-control" rows="2" cols="115" name="cx1_enf"><?php// echo $H_interv_Hemod ?></textarea>
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <textarea id="login-username" class="form-control" rows="2" cols="115" name="cx1_enf"></textarea>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <!-- ELECTROFISIOLOGIA -->
                            <div class="panel panel-info">
                                <div class="panel-heading" style="background-color: #F0F7FB; padding-top:2px">
                                    <div class="panel-title" style="height: 10px">ELECTROFISIOLOGIA</div>
                                </div>

                                <!-- INDICACION ELECTROFISIOLOGIA, FECHA ELECTROFISIOLOGIA, FECHA DE SOLICITUD, NUMERO DE AUTORIZACION, ELECTROFISIOLOGIA SOLICITADA -->
                                <div style="margin-bottom: 5px" class="panel-body">
                                    <div class="input-group">
                                        <h5>INDICACION ELECTROFISIOLOGIA:</h5>
                                        <h5><?php
                                            if($H_indicacion_Electrof != null)
                                            {
                                                ?>
                                                <h5><?php echo $H_indicacion_Electrof ?></h5>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <h5>No definida</h5>
                                                <?php
                                            }
                                            ?>
                                        </h5>

                                        <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                                        <h5>FECHA ELECTROFISIOLOGIA:</h5>
                                        <h5>
                                            <?php
                                            if($H_fecha_Electrof != null)
                                            {
                                                ?>
                                                <h5><?php echo $H_fecha_Electrof ?></h5>
                                                <?php
                                            }
                                            else
                                            {
                                                ?>
                                                <h5>No definida</h5>
                                                <?php
                                            }
                                            ?>
                                        </h5>

                                        <div class="input-group-addon" style="background-color: #ffffff; width: 55px; border: none"></div>

                                        <h5>FECHA DE SOLICITUD</h5>
                                        <input id="datepicker24" type="text" class="form-control" style="width: 175px" name="" value="<?php// echo $H_ingreso_Uci ?>" placeholder="aaaa-mm-dd" readonly>

                                        <div class="input-group-addon" style="background-color: #ffffff; width: 30px; border: none"></div>

                                        <h5>N° DE AUTORIZACION</h5>
                                        <input id="login-username" type="text" class="form-control" style="width: 160px" name="" value="<?php// echo $sexo ?>">
                                    </div>

                                    <div>
                                        <h5>ELECTROFISIOLOGIA SOLICITADA:</h5>
                                        <?php
                                        if($H_cx1 != null)
                                        {
                                            ?>
                                            <textarea id="login-username" class="form-control" rows="2" cols="115" name="cx1_enf"><?php// echo $H_cx1 ?></textarea>
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <textarea id="login-username" class="form-control" rows="2" cols="115" name="cx1_enf"></textarea>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!--------------------------------------------------------------------------------------------------->

                    <div style="margin-top:20px" class="form-group" align="center">
                        <div class="col-sm-12 controls">
                            <input type="hidden" name="responsable" value="<?php echo $responsable ?>">
                            <input type="hidden" name="fechaNac" value="<?php echo $edad ?>">
                            <input type="submit" class="btn btn-success" value="GUARDAR">
                            <br><br>
                            <a href="bitacorapaf.php"><img src="/matrix/images/medical/paf/volverPAF.png" width="30" height="30" title="Cancelar"></a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>