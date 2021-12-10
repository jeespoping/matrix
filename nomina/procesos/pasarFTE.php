<?php
	// 2021-11-05: Luis F Meneses. Adicionar a Laboratorio como empresa de la misma fuente
	//				de Promotora y Fundación, filtrando en Sql7 por movempresa=3
	// 2021-03-08: Luis F Meneses. Adicionar a Fundación como empresa de la misma fuente
	//				de Promotora, filtrando en Sql7 por movempresa=1 (promotora), movempresa=7 (Fundación)
?>

<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GENERACI&Oacute;N DE COMPROBANTES DE NOMINA - MATRIX</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script> <!-- INDICADOR DE CARGA DE PAGINA-->
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="http://mtx.lasamericas.com.co/matrix/soporte/procesos/stylehelpDesk2.css" rel="stylesheet">
    <link href="http://mtx.lasamericas.com.co/matrix/soporte/procesos/stylehelpDesk.css" rel="stylesheet">
    <link href="http://mtx.lasamericas.com.co/matrix/Library/Css/Bootstrap_v3.0.0.css" rel="stylesheet">
    <script src="http://mtx.lasamericas.com.co/matrix/Library/Js/bootstrap_v3.0.0.js" type="text/javascript"></script>
    <script src="http://mtx.lasamericas.com.co/matrix/Library/Js/jQuery_v1.11.1.js" type="text/javascript"></script>
    <script src="http://mtx.lasamericas.com.co/matrix/Library/Js/Bootstrap_v3.1.0.js" type="text/javascript"></script>
    <script src="http://mtx.lasamericas.com.co/matrix/Library/Js/GoogleCharts.js" type="text/javascript"></script>
    <script type="text/javascript">
        $(window).load(function() {
            $(".loader").fadeOut("slow");
        });
    </script> <!-- INDICADOR DE CARGA DE PAGINA-->
    <script>
        function verificarchked()
        {
            document.getElementById('fuente').value = document.getElementById('fuenteP').value;
            document.getElementById('documento').value = document.getElementById('documentoP').value;
            document.getElementById('anoFuente').value = document.getElementById('anoFuenteP').value;
            document.getElementById('mesFuente').value = document.getElementById('mesFuenteP').value;
        }
    </script>
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
        //$conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
    }

    $conexion = $_GET['conexion'];
    if($conexion == null){$conexion = $_POST['conexion'];}
    ?>
</head>

<body onload="window.resizeTo(725,790)" style="overflow: hidden">
<div class="loader"></div> <!-- INDICADOR DE CARGA DE PAGINA-->
<div class="container general" style="margin-left: 0">
    <div class="panel panel-info contenido">
        <div class="panel-heading encabezado">
            <div class="panel-title">Matrix - Generar Comprobante de N&oacute;mina</div>
        </div>
        <form method="post" action="pasarFTE.php" onsubmit="return confirm('Esta Seguro de General Comprobante con los Parametros Ingresados ?')">
            <div class="titulo">
                <label>CONEXI&Oacute;N:</label>
                <br>
                <?php
                switch($conexion)
                {
                    case '1':
                        ?>
                        <label for="conexion">Promotora &nbsp;</label><input type="radio" id="conexion" name="conexion" value="1" checked> <!-- &ensp;&ensp; -->
                        <label for="conexion" style="display:none;">Laboratorio &nbsp;</label><input type="radio" id="conexion" name="conexion" value="2" disabled style="display:none;"><!-- &ensp;&ensp; -->
                        <label for="conexion" style="display:none;">Patolog&iacute;a &nbsp;</label><input type="radio" id="conexion" name="conexion" value="3" disabled style="display:none;"><!-- &ensp;&ensp; -->
                        <label for="conexion" style="display:none;">Clisur &nbsp;</label><input type="radio" id="conexion" name="conexion" value="4" disabled style="display:none;"><!-- &ensp;&ensp; -->
                        <label for="conexion" style="display:none;">Fundaci&oacute;n &nbsp;</label><input type="radio" id="conexion" name="conexion" value="5" disabled style="display:none;">
                        <?php
                        break;
                    case '2':
                        ?>
                        <label for="conexion" style="display:none;">Promotora &nbsp;</label><input type="radio" id="conexion" name="conexion" value="1" disabled style="display:none;"><!-- &ensp;&ensp; -->
                        <label for="conexion">Laboratorio &nbsp;</label><input type="radio" id="conexion" name="conexion" value="2" checked><!-- &ensp;&ensp; -->
                        <label for="conexion"style="display:none;">Patologia &nbsp;</label><input type="radio" id="conexion" name="conexion" value="3" disabled style="display:none;"><!-- &ensp;&ensp; -->
                        <label for="conexion"style="display:none;">Clisur &nbsp;</label><input type="radio" id="conexion" name="conexion" value="4" disabled style="display:none;"><!-- &ensp;&ensp; -->
                        <label for="conexion"style="display:none;">Fundaci&oacute;n &nbsp;</label><input type="radio" id="conexion" name="conexion" value="5" disabled style="display:none;">
                        <?php
                        break;
                    case '3':
                        ?>
                        <label for="conexion" style="display:none;">Promotora &nbsp;</label><input type="radio" id="conexion" name="conexion" value="1" disabled style="display:none;"><!-- &ensp;&ensp; -->
                        <label for="conexion" style="display:none;">Laboratorio &nbsp;</label><input type="radio" id="conexion" name="conexion" value="2" disabled style="display:none;"><!-- &ensp;&ensp; -->
                        <label for="conexion">Patologia &nbsp;</label><input type="radio" id="conexion" name="conexion" value="3" checked><!-- &ensp;&ensp; -->
                        <label for="conexion" style="display:none;">Clisur &nbsp;</label><input type="radio" id="conexion" name="conexion" value="4" disabled style="display:none;"><!-- &ensp;&ensp; -->
                        <label for="conexion" style="display:none;">Fundaci&oacute;n &nbsp;</label><input type="radio" id="conexion" name="conexion" value="5" disabled style="display:none;">
                        <?php
                        break;
                    case '4':
                        ?>
                        <label for="conexion" style="display:none;">Promotora &nbsp;</label><input type="radio" id="conexion" name="conexion" value="1" disabled style="display:none;"><!-- &ensp;&ensp; -->
                        <label for="conexion" style="display:none;">Laboratorio &nbsp;</label><input type="radio" id="conexion" name="conexion" value="2" disabled style="display:none;"><!-- &ensp;&ensp; -->
                        <label for="conexion" style="display:none;">Patologia &nbsp;</label><input type="radio" id="conexion" name="conexion" value="3" disabled style="display:none;"><!-- &ensp;&ensp; -->
                        <label for="conexion">Clisur &nbsp;</label><input type="radio" id="conexion" name="conexion" value="4" checked><!-- &ensp;&ensp; -->
                        <label for="conexion" style="display:none;">Fundaci&oacute;n &nbsp;</label><input type="radio" id="conexion" name="conexion" value="5" disabled style="display:none;">
                        <?php
                        break;
                    case '5':
                        ?>
                        <label for="conexion" style="display:none;">Promotora &nbsp;</label><input type="radio" id="conexion" name="conexion" value="1" disabled style="display:none;"><!-- &ensp;&ensp; -->
                        <label for="conexion" style="display:none;">Laboratorio &nbsp;</label><input type="radio" id="conexion" name="conexion" value="2" disabled style="display:none;"><!-- &ensp;&ensp; -->
                        <label for="conexion" style="display:none;">Patologia &nbsp;</label><input type="radio" id="conexion" name="conexion" value="3" disabled style="display:none;"><!-- &ensp;&ensp; -->
                        <label for="conexion" style="display:none;">Clisur &nbsp;</label><input type="radio" id="conexion" name="conexion" value="4" disabled style="display:none;"><!-- &ensp;&ensp; -->
                        <label for="conexion">Fundaci&oacute;n &nbsp;</label><input type="radio" id="conexion" name="conexion" value="5" checked>
                        <?php
                        break;
                    default:
                        ?>
                        <label for="conexion">Promotora &nbsp;</label><input type="radio" id="conexion" name="conexion" value="1" disabled>&ensp;&ensp; 
                        <label for="conexion">Laboratorio &nbsp;</label><input type="radio" id="conexion" name="conexion" value="2" disabled>&ensp;&ensp; 
                        <label for="conexion">Patologia &nbsp;</label><input type="radio" id="conexion" name="conexion" value="3" disabled >&ensp;&ensp; 
                        <label for="conexion">Clisur &nbsp;</label><input type="radio" id="conexion" name="conexion" value="4" disabled>
                        <label for="conexion">Fundaci&oacute;n &nbsp;</label><input type="radio" id="conexion" name="conexion" value="5" disabled>
                        <?php
                        break;
                }
                ?>
            </div>
            <div class="datosFuente" style="margin-top: 10px">
                <table align="center" border="0">
                    <tr>
                        <td>
                            <div class="input-group">
                                <span class="input-group-addon" style="width: 120px"><label for="fuente">FUENTE:</label></span>
                                <select id="fuente" name="fuente" class="form-control form-sm" style="width: 120px">
                                    <option>06</option>
                                    <option>12</option>
                                    <option value="0" selected disabled>Seleccione...</option>
                                </select>
                                <!--<input id="fuente" name="fuente" type="text" class="form-control" style="width: 120px">-->
                            </div>
                        </td>
                        <td><div class="input-group-addon" style="background-color: #ffffff; width: 10px; border: none"></div></td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-addon" style="width: 130px"><label for="documento">DOCUMENTO:</label></span>
                                <select id="documento" name="documento" class="form-control" style="width: 120px">
                                    <option>0000001</option>
                                    <option>0000002</option>
                                    <option value="0" selected disabled>Seleccione...</option>
                                </select>
                                <!--<input id="documento" name="documento" type="text" class="form-control" style="width: 120px">-->
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="input-group" style="margin-top: 10px">
                                <span class="input-group-addon" style="width: 120px"><label for="anoFuente">A&ntilde;O:</label></span>
                                <select id="anoFuente" name="anoFuente" class="form-control form-sm" style="width: 120px">
                                    <option value="0" selected disabled>Seleccione...</option>
                                    <?php $year = date("Y"); for ($i=2018; $i<=$year; $i++){echo '<option value="'.$i.'">'.$i.'</option>';}?>
                                </select>
                            </div>
                        </td>
                        <td><div class="input-group-addon" style="background-color: #ffffff; width: 10px; border: none"></div></td>
                        <td>
                            <div class="input-group" style="margin-top: 10px">
                                <span class="input-group-addon" style="width: 130px"><label for="mesFuente">MES:</label></span>
                                <select id="mesFuente" name="mesFuente" class="form-control form-sm" style="width: 120px">
                                    <option>01</option><option>02</option><option>03</option><option>04</option>
                                    <option>05</option><option>06</option><option>07</option><option>08</option>
                                    <option>09</option><option>10</option><option>11</option><option>12</option>
                                    <option value="0" selected disabled>Seleccione...</option>
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" colspan="3">
                            <div class="input-group" style="margin-top: 10px">
                                <input type="submit" class="btn btn-info btn-sm" value="> > >" title="Proceder" style="width: 120px; margin-bottom: 10px">
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </form>
    </div>

    <section>
        <?php
        $conexion = $_POST['conexion'];     $fuente = $_POST['fuente'];         $documento = $_POST['documento'];
        $anoFuente = $_POST['anoFuente'];   $mesFuente = $_POST['mesFuente'];
        ?>
        <input type="hidden" id="conexionP" name="conexionP" value="<?php echo $conexion ?>">
        <input type="hidden" id="fuenteP" name="fuenteP" value="<?php echo $fuente ?>">
        <input type="hidden" id="documentoP" name="documentoP" value="<?php echo $documento ?>">
        <input type="hidden" id="anoFuenteP" name="anoFuenteP" value="<?php echo $anoFuente ?>">
        <input type="hidden" id="mesFuenteP" name="mesFuenteP" value="<?php echo $mesFuente ?>">
        <script>verificarchked()</script>
    </section>
    <div align="center" class="panel panel-info contenido">
        <?php
        if($conexion != null and $fuente != null and $documento != null and $anoFuente != null and $mesFuente != null)
        {
            //Promotora:
            if ($conexion == 1)
            {
				// CONEXIONES COMPARTIDAS ENTRE PROMOTORA, FUNDACIÓN Y LABORATORIO
                //$conexSql7 = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
				echo 'SELECCIONO PROMOTORA...';
                $conexSql7 = odbc_connect('queryx7','','') or die("No se realizo conexión con la BD de Promotora");
                modificarFte($conexSql7, $fuente, $documento, $anoFuente, $mesFuente, 'promotora', $wuse);
            }
            //Laboratorio:
            if ($conexion == 2)
            {
				// CONEXIONES COMPARTIDAS ENTRE PROMOTORA, FUNDACIÓN Y LABORATORIO
                // $conexSql7 = odbc_connect('queryx7LMLA','','') or die("No se realizo conexión con la BD de Laboratorio");
                echo 'SELECCIONO LABORATORIO...';				
                $conexSql7 = odbc_connect('queryx7','','') or die("No se realizo conexión con la BD de Promotora");
                modificarFte($conexSql7, $fuente, $documento, $anoFuente, $mesFuente, 'laboratorio', $wuse);
            }
            //Patologia  
            if ($conexion == 3)
            {
                echo 'SELECCIONO PATOLOGIA...';
                $conexSql7 = odbc_connect('queryx7PAT','','') or die("No se realizo conexión con la BD de Patología");
                modificarFte($conexSql7, $fuente, $documento, $anoFuente, $mesFuente, 'patologia', $wuse);
            }
            //Clisur
            if ($conexion == 4)
            {
                echo 'SELECCIONO CLISUR...';
                $conexSql7 = odbc_connect('queryx7CS','','') or die("No se realizo conexión con la BD de Clisur");
                modificarFte($conexSql7, $fuente, $documento, $anoFuente, $mesFuente, 'clisur', $wuse);
            }
            //Fundación
            if ($conexion == 5)
            {
				// CONEXIONES COMPARTIDAS ENTRE PROMOTORA, FUNDACIÓN Y LABORATORIO
                //$conexSql7 = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
				echo 'SELECCIONO FUNDACION...';
                $conexSql7 = odbc_connect('queryx7','','') or die("No se realizo conexión con la BD de Fundación");
                modificarFte($conexSql7, $fuente, $documento, $anoFuente, $mesFuente, 'fundacion', $wuse);
            }
        }
        else
        {
            ?>
            <h3>Debe ingresar todos los parametros</h3>
            <?php
        }
        ?>
    </div>
</div>

<?php
////// FUNCIONES: /////

function modificarFte($conexionSql7, $fuente, $documento, $anoFuente, $mesFuente, $empresa, $usuario)
{
	// Vista COMOVENC: Se agregó columna MOVENCEMPRESA. En el momento los valores serían 1 para Clínica y 7 para Fundación
	// Vista COMOV       : Se agregó columna MOVEMPRESA. En el momento los valores serían 1 para Clínica y 7 para Fundación
    //DEFINIR EL CONEX DE UNIX PARA CADA EMPRESA:
    switch($empresa)
    {
        case 'promotora': $conexUnix = odbc_connect('nomina','informix','sco') or die("No se realizo conexión con la BD de Promotora/Fundación"); break;
		case 'fundacion':$conexUnix = odbc_connect('cofunda','informix','sco') or die("No se realizo conexión con la BD de Promotora/Fundación"); break;
        case 'laboratorio': $conexUnix = odbc_connect('nomlab','informix','sco') or die("No se realizo conexión con la BD de Laboratorio"); break;
        case 'patologia': $conexUnix = odbc_connect('nompat','informix','sco') or die("No se realizo conexión con la BD de Patología"); break;
        case 'clisur': $conexUnix = odbc_connect('nomsur','informix','sco') or die("No se realizo conexión con la BD de Clisur"); break;        
    }
	
	// ---- QUITAR COMENTARIO PARA EFECTOS DE PRUEBAS -----------
	// contar los registros del resultado
	/*
	$sql = "SELECT Count(*) AS cantidad " 
		. " from comov where movfue = '$fuente'"
                        ."   and movdoc = '$documento'"
                        ."   and movano = '$anoFuente'"
                        ."   and movmes = '$mesFuente'";
	if ($empresa=='promotora')
	{
		$sql .= " and movempresa=1";
	}
	else if ($empresa=='fundacion')
	{
		$sql .= " and movempresa=7";
	}	
	$rs = odbc_do($conexionSql7, $sql);
		
	if (!$rs){
              echo "<br>SQL7 --> Query conteo error: " .odbc_error();
	}
	echo "<br>SQL7--> $sql <br>Registros: ";
	if (odbc_fetch_row($rs))
		echo odbc_result($rs, 1); 
	*/
	// --- HASTA AQUÍ PARA PRUEBAS  -------------------------------------------
		
	// ---- QUITAR COMENTARIO PARA EFECTOS DE PRUEBAS -----------
	// BORRAR DATOS DE ESE PERIODO EN UNIX PARA PODER PROBAR CARGA DE NUEVO
	/*
	$sql = "delete from  comov "
					. " WHERE movfue = '$fuente'"
					. "		AND movdoc = '$documento'"
					. "		AND movano = '$anoFuente'"
					. "		AND movmes = '$mesFuente'";
    $rs = odbc_do($conexUnix, $sql);
	$sql = "delete from  comovenc "
					. " WHERE movencfue = '$fuente'"
					. "		AND movencdoc = '$documento'"
					. "		AND movencano = '$anoFuente'"
					. "		AND movencmes = '$mesFuente'";
    $rs = odbc_do($conexUnix, $sql);
	*/
	// --- HASTA AQUÍ PARA PRUEBAS  -------------------------------------------


    //VERIFICAR QUE EN NUESTRAS BASES DE DATOS NO EXISTAN YA ESTOS REGISTROS:
    $queryVerificar = "select count(*) as CONTEO"
					. " from comov "
					. " WHERE movfue = '$fuente'"
					. "		AND movdoc = '$documento'"
					. "		AND movano = '$anoFuente'"
					. "		AND movmes = '$mesFuente'";
	
	// echo '<br>Verificar si reg existen...';
					
    $commitVerificar = odbc_do($conexUnix, $queryVerificar);
	$dato="xx";
	if (!$commitVerificar){
              echo "<br>Error consultando en Unix: " .odbc_error();
	}
	else {
		//echo "<br>UNIX--> $queryVerificar <br>Registros: ";
		if (odbc_fetch_row($commitVerificar))
		{
			$dato = odbc_result($commitVerificar, 1);
			// echo $dato; 	
		}
	}

    if($dato == '0')
    {
		
		echo '<br>Procesando...';
		
        //REALIZAR EL QUERY DE LA TABLA COMOV DE SQL7:
        $queryPromotora = " from comov"
                        ."   where movfue = '$fuente'"
                        ."   and movdoc = '$documento'"
                        ."   and movano = '$anoFuente'"
                        ."   and movmes = '$mesFuente'";
		if ($empresa=='promotora')
		{
			$queryPromotora .= " and movempresa=1";
		}
		else if ($empresa=='fundacion')
		{
			$queryPromotora .= " and movempresa=7";
		}
		else if ($empresa=='laboratorio')
		{
			$queryPromotora .= " and movempresa=3";
		}

        $queryPromotora = "select movfue, movdoc, movane, movano, movmes, rownum, movfec, movcue, movcco, movnit,
                           movdes, movind, movval, movcon, movbas, movfac, movuni, movcam, movbaj, movanu"
                        . $queryPromotora;

        $commitQueryPromotora = odbc_do($conexionSql7,$queryPromotora);
		

		if (!$commitQueryPromotora){
              echo "<br>Query COMOV error: " .odbc_error();
		}
				
        //ASIGNAR TODOS LOS REGISTROS DE COMOV DE SQL7:
		// echo "<br>ASIGNAR TODOS LOS REGISTROS DE COMOV DE SQL7: $queryPromotora";
		$cant = 0;
        while(odbc_fetch_row($commitQueryPromotora))
        {
			//echo "<br>movfue:";
            $movfue = odbc_result($commitQueryPromotora, 1);    
			//echo "$movfue";
			$movdoc = odbc_result($commitQueryPromotora, 2);    
			$movane = odbc_result($commitQueryPromotora, 3);
            $movano = odbc_result($commitQueryPromotora, 4);    
			$movmes = odbc_result($commitQueryPromotora, 5);    
			$movite = odbc_result($commitQueryPromotora, 6);
            $movfec = odbc_result($commitQueryPromotora, 7);    
			$movcue = odbc_result($commitQueryPromotora, 8);    
			$movcco = odbc_result($commitQueryPromotora, 9);
            $movnit = odbc_result($commitQueryPromotora, 10);   
			$movdes = odbc_result($commitQueryPromotora, 11);   
			$movind = odbc_result($commitQueryPromotora, 12);
            $movval = odbc_result($commitQueryPromotora, 13);   
			$movcon = odbc_result($commitQueryPromotora, 14);   
			$movbas = odbc_result($commitQueryPromotora, 15);
            $movfac = odbc_result($commitQueryPromotora, 16);   
			$movuni = odbc_result($commitQueryPromotora, 17);   
			$movcam = odbc_result($commitQueryPromotora, 18);
            $movbaj = odbc_result($commitQueryPromotora, 19);   
			$movanu = odbc_result($commitQueryPromotora, 20);

            //REALIZAR EL INSERT DE CADA REGISTRO EN LA TABLA COMOV DE CLINICA, SEGUN LA EMPRESA:
            $queryComov = "insert into comov(movfue, movdoc, movane, movano, movmes, movite, movfec, movcue, movcco, movnit, movdes, movind, movval,
                                         movcon, movbas, movfac, movuni, movcam, movbaj, movanu)
                                   VALUES('$movfue','$movdoc','$movane','$movano','$movmes','$movite','$movfec','$movcue','$movcco','$movnit','$movdes','$movind','$movval',
                                          '$movcon','$movbas','$movfac','$movuni','$movcam','$movbaj','$movanu')";
			$cant++;
			//if ($cant < 2) { echo "<br>$queryComov";}
			$commitQueryComov = odbc_do($conexUnix, $queryComov);				
			
        }
		//echo "<br>$cant";
        //GRABAR EL ENCABEZADO EN COMOVENC:
        $queryEncabezado = "insert into comovenc(movencano, movencmes, movencfue, movencdoc, movencusu, movencanu)
                                    VALUES('$anoFuente','$mesFuente','$fuente','$documento','$usuario','0')";
        $commitQueryEncabezado = odbc_do($conexUnix, $queryEncabezado);

        if($cant <= 0) {
            ?>
            <h3>La consulta no arroj&oacute; registros para generar</h3>
            <?php
		}
        else {
			if($commitQueryComov == true and $commitQueryEncabezado == true)
			{
				?>
					<h3>Proceso Exitoso <?php echo("($cant registros procesados)"); ?></h3>
				<?php
			}
			else
			{
				?>
					<h3>Se ha presentado un problema y el proceso no se realiz&oacute;</h3>
				<?php
			}
		}
    }
    else
    {
        ?>
        <h3>Los datos ingresados ya existen en la base de datos,<br>por favor verifique</h3>
        <?php
    }
}
?>
</body>
</html>