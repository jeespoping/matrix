<?php
// á -> &aacute;
// &eacute; -> &eacute;
// í -> &iacute;
// ó -> &oacute;
// ú -> &uacute;
// ñ -> &ntilde;

$wactualiz = '2020-04-28';
date_default_timezone_set("America/Bogota");
// include('./config/db_connect.php');
include_once("conex.php");
include_once("root/comun.php");

// // Conexi&oacute;n a Laboratorio
$connLab = mysqli_connect('131.1.18.106', '4duser', 'LavAmerikx09', '4dlab');
if (!$connLab) {
    echo 'Connection error' . mysqli_connect_error(); # code...
}

$user_session = explode('-', $_SESSION['user']);
$wuse = "C-" . $user_session[1];



$url = "/matrix/citas/procesos/listaDeEspera.php?wemp_pmla=05";
//sendToMail en common

$sql = "SELECT drvanm,drvnlb FROM citaslc_000033 where drvast='on' order by drvanm ASC";
// make query $ get result
$result = mysqli_query($conex, $sql);
// fetch the resulting rows as an aaray
$eps = mysqli_fetch_all($result, MYSQLI_ASSOC);


$sql = "SELECT Bardes,Barcod FROM root_000034 where Barmun='05001' order by Bardes ASC";
$result = mysqli_query($conex, $sql);
$barrios = mysqli_fetch_all($result, MYSQLI_ASSOC);


$sql = "SELECT c32.id,c32.Fecha_data,c32.Hora_data,
c32.drvtid,
c32.drvidp,
c32.drvnom,
c32.drvap1,
c32.drvap2,
c32.drvbdy,
c32.drvsex,
c32.drvtel,
c32.drvmov,
c32.drvdir,
c32.drvbar,
c32.drvema,
c32.drvenv,
c32.drvase,
c32.drvpol,
c32.drvcpl,
c32.drvser,
c32.drvexa,
c32.drvfsi,
c32.drvsin,
c32.drvcla,
c32.drvaut,
c32.drvvcr,
c32.drvlnk,
c32.drvpcf,
c32.drvfec,
c32.drvhor,
c32.drvsag,
c32.drvord,
c32.drvest,
c33.drvanm,
c35.drvser
FROM citaslc_000032 c32, citaslc_000035 c35 , citaslc_000033 c33
WHERE drvord='' and c32.drvest='on'  AND c32.drvser=c35.drvcse AND c32.drvase=c33.drvnlb order by c32.Fecha_data DESC, c32.Hora_data DESC;";
$result = mysqli_query($conex, $sql);
$registros = mysqli_fetch_all($result, MYSQLI_ASSOC);



$sql = "SELECT drvser, drvcse FROM citaslc_000035 where drvest='on'  order by id ASC";
$result = mysqli_query($conex, $sql);
$servicios = mysqli_fetch_all($result, MYSQLI_ASSOC);


if (isset($_POST['servicioSeleccionado']) && $_POST['servicioSeleccionado'] == 'DM') {

    $sql =
        "SELECT Codigo , CodigoCups, NombreExamen, CodTubo FROM Examenes WHERE CodigoCups<>''  and SEREALIZA=1  order by Codigo ASC";
    // make query $ get result
    $result = mysqli_query($connLab, $sql);
    // fetch the resulting rows as an aaray
    $html = "<option value='' selected><?php echo utf8_decode('Elige una opci&oacute;n') ?></option>";


    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $html .= "<option value='" . $row['CodigoCups'] . "-" . $row['NombreExamen'] . "-" . $row['Codigo'] . "'>" . $row['NombreExamen']  . " | " . $row['Codigo'] . " | " . $row['CodigoCups']  . "</option>";
    }

    mysqli_free_result($result);
    mysqli_close($connLab);

    echo $html;
    exit;
}


if (isset($_POST['asunto'])) {

    $id = $_POST['id'];
    function encode64($cadena)
    {
        return "=?UTF-8?B?" . base64_encode($cadena) . "=?=";
    }

    $subject = "Preadmisión toma de muestras - Laboratorios Las Américas Auna";

    $fromName = "Laboratorios Las Américas AUNA";

    $wremitente =  array(
        'email' => "lis@lasamericas.com.co",
        'password' => "tel3419092",
        'from' => "",
        'fromName' => encode64($fromName),
    );


    $data = sendToEmail(encode64($subject), $_POST['mensaje'], "", $wremitente, $_POST['wdestinatarios']);

    if ($data['Error'] == '1') {
        # code...
        $sql =   "UPDATE citaslc_000032 SET drvenv='on' WHERE id='$id';";
        $result = mysqli_query($conex, $sql);

        if ($result) {
            echo $data['Error'];
        } else {
            # code...
            echo 'query error' . mysqli_error($conex);
        }
    }


    exit;
}


if (isset($_GET['wemp_pmla'])) {
    # code...
    $empresa = $_GET['wemp_pmla'];

    $sql = "SELECT drvcam FROM citaslc_000034 WHERE  drvemp=" . $empresa . ";";
    $result = mysqli_query($conex, $sql);
    $configuraciones = mysqli_fetch_all($result, MYSQLI_ASSOC);
    // print_r($configuraciones);

    $nombreConfiguracion = array();
    foreach ($configuraciones as $key => $configuracion) {
        # code...
        array_push($nombreConfiguracion, $configuracion['drvcam']);
    }

    $sql = "SELECT * FROM citaslc_000034 WHERE  drvemp=" . $empresa . ";";
    $result = mysqli_query($conex, $sql);
    $todasLasConfiguraciones = mysqli_fetch_all($result, MYSQLI_ASSOC);


    $assocConfiguracion = array_combine($nombreConfiguracion, $todasLasConfiguraciones);

    $wompiKey = $assocConfiguracion['llavePublicaWompi']['drvdpl'];
    $urlAPI = $assocConfiguracion['urlAPI']['drvdpl'];


    $sql = "SELECT *  FROM root_000007 where Estado='on' order by id ASC";
    $result = mysqli_query($conex, $sql);
    $tipoDocumento = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

if (isset($_POST['consultarValorParticular'])) {
    # code...
    # code...
    $cups = $_POST['consultarValorParticular'];

    $sql =  "SELECT drvncl FROM citaslc_000031 where drvser='DT' AND drvnex=$cups;";
    $result = mysqli_query($conex, $sql);
    $valorExamen = mysqli_fetch_all($result, MYSQLI_ASSOC);

    print_r($valorExamen);
    exit;
}

if (isset($_POST['consultaExamenes'])) {

    // write query
    $id = $_POST['consultaExamenes'];
    $sql = "SELECT drvcup,
    drvnex,
    drvaut " . "FROM citaslc_000036 " . "WHERE drvcit='" . $id . "' order by Fecha_data";

    $result = mysqli_query($conex, $sql);
    $html = "";

    // $html = "";
    $index = 0;

    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $index % 2 == 0 ? $wclass = "fila1" : $wclass = "fila2";
        $html .= "<tr class=" . $wclass . "><td  id='cup-" . $id . "-" . $index . "' >" . $row["drvcup"] . "</td><td style='min-width: -moz-max-content;'>" . $row["drvnex"] . "</td> <td> <input id='auth-" . $id . "-" . $index . "' type=text value='" . $row["drvaut"] . "'>";
        $index++;
    }
    $auth_to_copy = "auth-" . $id . "-0";

    $html .= "<tr><td></td><td></td><td><button onclick='copiarAutorizaciones(`" . $auth_to_copy . "`,`" . $index . "`,`" . $id . "` )'>Copiar</button><button onclick='guardarAutorizaciones(`" . $index . "`,`" . $id . "`)'>Guardar</button></td></tr></table>";

    echo $html;
    exit; // Agregado para que no retorne todo el resto de html que sigue para abajo
}

if (isset($_POST['consultaExamenesAGrabar'])) {

    // write query
    $id = $_POST['consultaExamenesAGrabar'];

    $sql = "SELECT id, drvcup,
    drvnex,
    drvaut, drvpos " . "FROM citaslc_000036 " . "WHERE drvcit='" . $id . "' order by Fecha_data";

    $result = mysqli_query($conex, $sql);
    $examenesParaOT = mysqli_fetch_all($result, MYSQLI_ASSOC);

    echo json_encode($examenesParaOT);

    exit; // Agregado para que no retorne todo el resto de html que sigue para abajo
}

if (isset($_POST['ordenLaboratorio'])) {
    # code...
    $orden = $_POST['ordenLaboratorio'];
    $id = $_POST['id'];
    $sql =  "UPDATE citaslc_000032 SET drvord='$orden' WHERE id='$id';";
    $result = mysqli_query($conex, $sql);
    if (!$result) {
        echo ("Error description: " . mysqli_error($conex));
    }
    exit;
}

if (isset($_POST['guardarAutorizaciones'])) {
    # code...
    $autorizaciones = $_POST['guardarAutorizaciones'];
    $id = $_POST['guardarAutorizacionesId'];
    $numeroDeExamenes = count($autorizaciones);

    for ($i = 0; $i < $numeroDeExamenes; $i++) {
        # code...
        $sql = "";
        $drvcit = $autorizaciones[$i]['drvcit'];
        $drvcup = $autorizaciones[$i]['drvcup'];
        $drvaut = $autorizaciones[$i]['drvaut'];
        $sql =  "UPDATE citaslc_000036 SET drvaut='$drvaut' WHERE drvcit='$drvcit' AND drvcup='$drvcup';";

        $result = mysqli_query($conex, $sql);
        if (!$result) {
            echo ("Error description: " . mysqli_error($conex));
        }
    }
    exit;
}

if (isset($_POST['confirmarPago'])) :
    // write query
    $id = $_POST['confirmarPago'];

    $sql = "UPDATE citaslc_000032 SET drvpcf = CASE WHEN drvpcf = 'on' THEN 'off' WHEN drvpcf = 'off' THEN 'on' END WHERE id='$id'";

    // make query $ get result
    $result = mysqli_query($conex, $sql);
    $pagoConfirmado = mysqli_fetch_all($result, MYSQLI_ASSOC);

    print_r($pagoConfirmado);
    exit;
endif;

if (isset($_POST['obtenerExamenes'])) {
    # code...
    // write query
    $id = $_POST['obtenerExamenes'];

    $sql = "SELECT CodigoTarifa FROM Empresas  WHERE Nit = '" . $id . "';";
    $result = mysqli_query($connLab, $sql);
    $tarifaLab = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $codigoTarifa = $tarifaLab[0]['CodigoTarifa'];

    $sql = "SELECT CodigoExamen FROM ValorExamenes WHERE  ValorActual>=0 AND CodigoTarifa ='" . $codigoTarifa . "' AND CodigoExamen IN (SELECT Codigo FROM Examenes WHERE TipoPrueba<>'')";


    $result = mysqli_query($connLab, $sql);
    $examenesConvenio = mysqli_fetch_all($result, MYSQLI_ASSOC);

    $listaExamenes = array();

    foreach ($examenesConvenio as $key => $examen) {
        # code...
        array_push($listaExamenes, "'" . $examen['CodigoExamen'] . "'");
    }

    $examenesAConsultar = implode(",", $listaExamenes);
    // print_r($row);
    // echo $examenesAConsultar;

    $sql = "SELECT Codigo , CodigoCups, NombreExamen, CodTubo FROM Examenes WHERE  Codigo IN (" . $examenesAConsultar . ") order by Codigo ASC";
    echo $sql;
    $result = mysqli_query($connLab, $sql);
    // $exams = mysqli_fetch_all($result, MYSQLI_ASSOC);
    // print_r($exams);



    if (!$result) {
        echo ("Error description: " . mysqli_error($conex));
    }


    $html = "<option value=''>Elija al menos un examen. </option>";

    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $html .= "<option value='" . $row['CodigoCups'] . "-" . $row['NombreExamen'] . "-" . $row['Codigo'] . "'>" . $row['NombreExamen'] . " | " . $row['Codigo'] . " | " . $row['CodigoCups'] . "</option>";
    }
    // make query $ get result
    mysqli_free_result($result);
    mysqli_close($connLab);
    echo $html;

    exit;
}

if (isset($_POST['aseguradoraSeleccionada'])) {
    # code...
    // write query
    $id = $_POST['aseguradoraSeleccionada'];
    $service = $_POST['tipoServicio'];

    $sql =
        " SELECT Plades "
        . "  FROM cliame_000153 "
        . "  WHERE Plaest = 'on'"
        . "	AND Plaemp = '"
        . $id . $service . "' ";

    $html = "";

    $result = mysqli_query($conex, $sql);

    if (!$result) {
        echo ("Error description: " . mysqli_error($conex));
    }

    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $html .= "<option>" . $row["Plades"] . "</option>";
    }
    // make query $ get result
    echo $html;

    exit;
}

if (isset($_POST['consultarCodigoEmpresaLab'])) {
    $nit = $_POST['consultarCodigoEmpresaLab'];
    $sql = "SELECT Codigo FROM Empresas  WHERE Nit = '" . $nit . "';";
    $result = mysqli_query($connLab, $sql);
    $tarifaLab = mysqli_fetch_all($result, MYSQLI_ASSOC);
    $codigo = $tarifaLab[0]['Codigo'];
    mysqli_free_result($result);
    mysqli_close($connLab);
    echo $codigo;
    exit;
}

if (isset($_POST['total'])) {
    # code...
    $total = intval($_POST['total']);
    $id = $_POST['id'];

    // if (empty($_POST['total'])) {
    //     $errors['total'] = "No puede ir sin valor.";
    // } else {
    //     $total = $_POST['total'];
    //     if (!preg_match('/^[0-9]$/', $total)) {
    //         $errors['total'] = 'El valor a pagar solo debe estar compuesto por n&uacute;meros.';
    //     }
    // }


    $sql = "UPDATE citaslc_000032 SET drvvcr =$total  WHERE id='$id'";

    // make query $ get result
    $result = mysqli_query($conex, $sql);
    if (!$result) {
        echo ("Error description: " . mysqli_error($conex));
    }

    exit;
}

if (isset($_POST['linkAGuardar'])) {
    # code...
    $link = htmlspecialchars($_POST['linkAGuardar']);
    echo $link;
    $id = $_POST['id'];

    // if (empty($_POST['total'])) {
    //     $errors['total'] = "No puede ir sin valor.";
    // } else {
    //     $total = $_POST['total'];
    //     if (!preg_match('/^[0-9]$/', $total)) {
    //         $errors['total'] = 'El valor a pagar solo debe estar compuesto por n&uacute;meros.';
    //     }
    // }


    $sql = "UPDATE citaslc_000032 SET drvlnk ='$link'  WHERE id='$id'";

    // make query $ get result
    $result = mysqli_query($conex, $sql);
    if (!$result) {
        echo ("Error description: " . mysqli_error($conex));
    }

    exit;
}


if (isset($_POST['parametro'])) :
    // write query
    $parametro = $_POST['parametro'];
    $sql = "SELECT drvclb,drvvlr FROM citaslc_000031 where drvncl=$parametro order by Fecha_data";
    // make query $ get result
    $result = mysqli_query($conex, $sql);
    $html = "<option value=''><?php echo utf8_decode('Elige una opci&oacute;n') ?></option>";
    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $html .= "<option value='$row[drvvlr]'>$row[drvclb]</option>";
    }


    echo $html;
endif;


if (isset($_POST['examenesParaComentarios'])) {
    # code...

    // write query
    $id = $_POST['examenesParaComentarios'];
    $sql = "SELECT drvcup,
        drvnex,
        drvaut " . "FROM citaslc_000036 " . "WHERE drvcit='" . $id . "' order by Fecha_data";

    $result = mysqli_query($conex, $sql);
    $html = "";


    while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
        $html .=  $row["drvcup"] . "_" . $row["drvnex"] . "_" . $row["drvaut"] . "|";
    }

    echo $html;
    exit; // Agregado para que no retorne todo el resto de html que sigue para abajo

}

if (isset($_POST['cancelarRegistro'])) {
    # code...
    $id = $_POST['cancelarRegistro'];
    echo $id;
    $sql = "UPDATE citaslc_000032 SET drvest='off' WHERE id=" . $id . ";";
    echo $sql;
    $result = mysqli_query($conex, $sql);

    exit;
}

if (isset($_POST['submit'])) {


    // Validaci&oacute;n

    if (empty($_POST['doc'])) {
        $errors['doc'] = "Este campo es obligatorio.";
    } else {
        // Otra validaci&oacute;n
        $doc = $_POST['doc'];
        if (!preg_match('/^([a-zA-Z0-9]){5,}$/', $doc)) {
            $errors['doc'] = 'El documento debe ser alfanumerico. Sin espacios ni caracteres especiales. Longitud m&iacute;nima 5 caracteres.';
        }
    }
    if (empty($_POST['names'])) {
        $errors['names'] = "Este campo es obligatorio.";
    } else {
        // Otra validaci&oacute;n
        $names = $_POST['names'];
        if (!preg_match('/^[a-zA-Z\s]{2,}$/', $names)) {
            $errors['names'] = 'Los nombres solo pueden estar compuestos por letras y espacios. Longitud m&iacute;nima 2 caracteres.';
        }
    }
    if (empty($_POST['lastname1'])) {
        $errors['lastname1'] = "Este campo es obligatorio.";
    } else {
        // Otra validaci&oacute;n
        $lastname1 = $_POST['lastname1'];
        if (!preg_match('/^[a-zA-Z\s]{2,}$/', $lastname1)) {
            $errors['lastname1'] = 'Los apellidos solo pueden estar compuestos por letras y espacios. Longitud m&iacute;nima 2 caracteres.';
        }
    }


    if (empty($_POST['birthday'])) {
        $errors['birthday'] = "Este campo es obligatorio.";
    } else {
        $birthday = $_POST['birthday'];
        if ($_POST['birthday'] == "0000-00-00") {
            $errors['birthday'] = 'La fecha no puede ser 0000-00-00.';
        }
    }

    if (empty($_POST['sex'])) {
        $errors['sex'] = "Este campo es obligatorio.";
    }

    if (empty($_POST['phone']) && empty($_POST['cellphone'])) {
        $errors['phone'] = "Ingrese por lo menos un tel&eacute;fono.";
        $errors['cellphone'] = "Ingrese por lo menos un tel&eacute;fono.";
    } else {
        // Otra validaci&oacute;n
        if ($_POST['phone']) {
            # code...
            $phone = $_POST['phone'];
            if (!preg_match('/^[0-9]{7,}$/', $phone)) {
                $errors['phone'] = 'El n&uacute;mero de tel&eacute;fono solo debe estar compuesto por n&uacute;meros. Longitud m&iacute;nima 7 caracteres.';
            }
        } else {
            $cellphone = $_POST['cellphone'];
            if (!preg_match('/^[0-9]{10,}$/', $cellphone)) {
                $errors['cellphone'] = 'El n&uacute;mero de tel&eacute;fono solo debe estar compuesto por n&uacute;meros. Longitud m&iacute;nima 10 caracteres.';
            }
        }
    }

    if (empty($_POST['addr'])) {
        $errors['addr'] = "Este campo es obligatorio.";
    }
    if (empty($_POST['barrio'])) {
        $errors['barrio'] = "Este campo es obligatorio.";
    } else {
        // Otra validaci&oacute;n
    }
    // Check email
    if (empty($_POST['email'])) {
        $errors['email'] = "Correo electr&oacute;nico requerido";
        # code...
    } else {
        $email = $_POST['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Correo electr&oacute;nico debe ser " . utf8_decode('v&aacute;lido');
        }
    }




    if (empty($_POST['service'])) {
        $errors['service'] = "Este campo es obligatorio.";
    } else {
        // Otra validaci&oacute;n
    }

    if (empty($_POST['eps'])) {
        $errors['eps'] = "Este campo es obligatorio.";
    } else {
        // Otra validaci&oacute;n
    }

    if (isset($_POST['numeroExamenes']) && $_POST['numeroExamenes'] == 0) {
        # code...
        $errors['exams'] = "Debe elegir al menos un examen.";
    }

    // if (empty($_POST['plan']) && $_POST['eps'] != 'PARTICULAR') {
    //     $errors['eps'] = "Este campo es obligatorio si no es PARTICULAR.";
    // } else {
    //     // Otra validaci&oacute;n
    // }
    print_r($errors);
    if (array_filter($errors)) {
        // echo 'Errors in the form';
        echo "aqui";
        print_r($errors);
    } else {

        // [docType]
        // [doc]
        // [names]
        // [lastname1]
        // [birthday]
        // [sex]
        // [phone]
        // [cellphone]
        // [addr]
        // [barrio]
        // [email]
        // [service]
        // [eps]
        // [plan]
        // [epsNumber]
        // [numeroExamenes]


        $drvtid = mysqli_real_escape_string($conex, $_POST['docType']);
        $drvidp = mysqli_real_escape_string($conex, $_POST['doc']);
        $drvnom = mysqli_real_escape_string($conex, $_POST['names']);
        $drvap1 = mysqli_real_escape_string($conex, $_POST['lastname1']);
        $drvap2 = mysqli_real_escape_string($conex, $_POST['lastname2']);
        $drvbdy = mysqli_real_escape_string($conex, $_POST['birthday']);
        $drvsex = mysqli_real_escape_string($conex, $_POST['sex']);
        $drvtel = mysqli_real_escape_string($conex, $_POST['phone']);
        $drvmov = mysqli_real_escape_string($conex, $_POST['cellphone']);
        $drvdir = mysqli_real_escape_string($conex, $_POST['addr']);
        $drvbar = mysqli_real_escape_string($conex, $_POST['barrio']);
        $drvema = mysqli_real_escape_string($conex, $_POST['email']);
        $drvase = mysqli_real_escape_string($conex, $_POST['eps']);
        $drvpol = mysqli_real_escape_string($conex, $_POST['epsNumber']);
        $drvser = mysqli_real_escape_string($conex, $_POST['service']);
        $drvcpl = mysqli_real_escape_string($conex, $_POST['plan']);
        $drvexa = mysqli_real_escape_string($conex, $_POST['numeroExamenes']);
        $drvfsi = mysqli_real_escape_string($conex, $_POST['fechaSintomas']);
        $drvsin = mysqli_real_escape_string($conex, $_POST['sintomasPost']);
        $drvcla = mysqli_real_escape_string($conex, $_POST['clasificacionCasoPost']);

        print_r($_POST);
        // codigoTarifa

        $fecha_data = date("Y-m-d");
        $hora_data = date("H:i:s");

        // Create sql
        $sql = "INSERT INTO citaslc_000032 (medico,
            Fecha_data,
            Hora_data,
            drvtid,
            drvidp,
            drvnom,
            drvap1,
            drvap2,
            drvbdy,
            drvsex,
            drvtel,
            drvmov,
            drvdir,
            drvbar,
            drvema,
            drvase,
            drvpol,
            drvser,
            drvcpl,
            drvexa, 
            drvfsi,
            drvsin,
            drvcla,drvest, Seguridad) 
            VALUES('drvtru',
            '$fecha_data',
            '$hora_data',
            '$drvtid',
            '$drvidp',
            '$drvnom',
            '$drvap1',
            '$drvap2',
            '$drvbdy',
            '$drvsex',
            '$drvtel',
            '$drvmov',
            '$drvdir',
            '$drvbar',
            '$drvema',
            '$drvase',
            '$drvpol',
            '$drvser',
            '$drvcpl',
            '$drvexa',
            '$drvfsi',
            '$drvsin',
            '$drvcla','on','$wuse'
            )";

        if (mysqli_query($conex, $sql)) {
            // Sucess
            // The connection will be closed when the index php is loaded
            // header('Location: ' . $url);

            $fecha_data = date("Y-m-d");
            $hora_data = date("H:i:s");
            $drvcit = mysqli_insert_id($conex);

            $inserts = "";
            for ($i = 0; $i < $drvexa; $i++) {
                # code...
                $drvcup = mysqli_real_escape_string($conex, $_POST['exam-' . $i]);
                $drvnex = mysqli_real_escape_string($conex, $_POST['examName-' . $i]);
                $drvpos = $i + 1;
                $inserts = $inserts . "('drvtru','$fecha_data','$hora_data','$drvcit','$drvcup','$drvnex','$drvpos','$wuse')";
                // Se agrega coma hasta el pen&uacute;ltimo elemento
                if ($i !== $drvexa - 1) {
                    # code...
                    $inserts = $inserts . ",";
                }
            }

            $sql = "INSERT INTO citaslc_000036 (medico,Fecha_data,Hora_data,drvcit,drvcup,drvnex,drvpos,Seguridad) VALUES " . $inserts;


            if (mysqli_query($conex, $sql)) {
                header('Location: ' . $url);
            } else {
                # code...
                echo 'query error' . mysqli_error($conex);
            }
        } else {
            echo 'query error' . mysqli_error($conex);
        }
    }
}





// mysqli_close($conex);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario Drive Thruh</title>
    <style>
        .showEmailSentSuccess {
            font-size: 0.7rem;
            color: black;
        }

        .showOrderCreatedSuccess {
            font-size: 0.7rem;
            color: black;
        }

        /* 
       .verdeAUNA #C1CE0D
       }
       
       .azulAUNA  #00ACC9 
 */

        .gestion1 {
            background-color: #00ACC9;
            color: #FFFFFF !important;
            font-size: 10pt;
        }

        .gestion2 {
            background-color: #C1CE0D;
            color: #FFFFFF !important;
            font-size: 10pt;
        }

        .textoCentrado {
            text-align: center;
        }

        .gestion3 {
            background-color: #FFE699;
            color: #FFFFFF !important;
            font-size: 10pt;
        }


        .showEmailSentFailure {
            font-size: 0.7rem;
            color: #FF0000;
        }


        .red-text {
            color: red;
        }


        /* .main-content {
            padding: 20px;
            width: -moz-fit-content;
            margin-left: auto;
            margin-right: auto;
        }

    
        .form-group {
            display: flex;
            flex-direction: row;
        }

        label {
            flex: none;
            display: block;
            font-weight: bold;
            font-size: 1em;
            margin: auto 40px auto 0;
            padding: 5px 0 5px 0;
        }

        label.right-inline {
            text-align: right;
            padding-right: 8px;
            padding-left: 10px;
            width: auto;
        } */



        .input-control {

            /* flex: 1 1 auto; */
            /* display: block; */
            margin-bottom: auto;
            margin-right: 16px;
            margin-top: auto;
            font-size: 16px;
            width: 250px;
        }

        .solo-control {
            /* flex: 1 1 auto; */
            /* display: block; */
            margin-bottom: auto;
            margin-right: 16px;
            margin-top: auto;
            font-size: 16px;
            width: 515px;

        }


        .input-radio {
            /* flex: 1 1 auto; */
            /* display: block; */
            margin-bottom: 14px;
            margin-right: 8px;
            /* padding: 4px; */
            margin-top: 4px;
            font-size: 16px;
            width: 20px;
            max-width: 20px;
        }

        .input-select {
            /* /* flex: 1 1 auto; */
            /* display: block; */
            margin-bottom: 10px;
            margin-right: 8px;

            margin-top: -4px;
            font-size: 16px;
            width: 258px;
            max-width: 258px;

            /* flex: 1 1 auto; */
            /* display: block; */
            margin-bottom: auto;
            margin-right: 16px;
            margin-top: auto;
            font-size: 16px;
            width: 250px;

        }

        .solo-select {
            /* flex: 1 1 auto; */
            /* display: block; */
            margin-bottom: auto;
            margin-right: 16px;
            margin-top: auto;
            font-size: 16px;
            width: -moz-available;

        }

        .button {
            padding: 5px 15px;
            margin: 5px;
            min-width: 100px;
            background-color: #00acc9;
            color: white;
            border: #00acc9;
            border-radius: 4px;
            font-size: 1rem;
        }

        .submitDiv {
            display: flex;
            justify-content: center;
        }

        .newExams {
            margin-top: 10px;
            margin-bottom: 20px;
        }



        /* Tables
–––––––––––––––––––––––––––––––––––––––––––––––––– */
        th,
        td {
            /* padding: 12px 15px; */
            text-align: left;
            /* min-width: -moz-max-content; */
            padding: 10px;
            /* border-bottom: 1px solid #E1E1E1; */
        }

        #listaDeRegistros {
            padding-right: 50px;
        }

        .row {
            display: flex;
            margin: 15px auto 15px auto;
        }

        .columns {

            min-width: 300px;
        }

        label {

            font-weight: bolder;
        }

        /* Clearing
–––––––––––––––––––––––––––––––––––––––––––––––––– */

        /* Self Clearing Goodness */
        /* .container:after,
        .row:after,
        .u-cf {
            content: "";
            display: table;
            clear: both;
        } */
    </style>
    <!-- CSS -->
    <link type="text/css" rel="stylesheet" href="../../../include/root/jquery-ui-1.12.1/jquery-ui.css">
    <link type="text/css" rel="stylesheet" href="../../../include/root/matrix.css" />
    <!-- <link rel="stylesheet" href="https://unpkg.com/multiple-select@1.5.2/dist/multiple-select.min.css"> -->
</head>

<body>
    <?php

    // -->	ENCABEZADO

    encabezado("Lista de espera", $wactualiz, "logo_labmed");
    ?>

    <div class="container">
        <form action="<?php echo $url ?>" method="post">

            <table>
                <thead>
                    <tr class="encabezadoTabla">
                        <th colspan="6" style="text-align: center;">Informaci&oacute;n del paciente</th>
                    </tr>
                </thead>
                <tbody class="fila2">
                    <tr>
                        <td><label for="docType">Tipo de documento:</label></td>
                        <td><select name="docType" class="input-select" id="docType">
                                <?php foreach ($tipoDocumento as $tipo) : ?>
                                    <option value=<?php echo htmlspecialchars($tipo['Codigo']); ?>>
                                        <?php
                                        echo htmlspecialchars($tipo['Codigo'] . " - " . $tipo['Descripcion']);
                                        ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <b class="red-text"><?php echo $errors['docType']; ?></b>
                        </td>
                        <td><label for="doc">Documento</label></td>
                        <td><input type="text" class="input-control" name="doc" id="doc" value="<?php echo htmlspecialchars($doc) ?>">
                            <b class="red-text"><?php echo $errors['doc']; ?></b>
                        </td>
                        <td colspan="2"></td>
                    </tr>

                    <tr>
                        <td> <label for="names">Nombres</label></td>
                        <td>
                            <input type="text" class="input-control" name="names" id="names" value="<?php echo htmlspecialchars($names) ?>">
                            <b class="red-text"><?php echo $errors['names']; ?></b></td>

                        <td> <label for="lastname1">Apellido1</label></td>
                        <td>
                            <input type="text" class="input-control" name="lastname1" id="lastname1" value="<?php echo htmlspecialchars($lastname1) ?>">
                            <b class="red-text"><?php echo $errors['lastname1']; ?></b></td>

                        <td> <label for="lastname2">Apellido2</label></td>
                        <td>
                            <input type="text" class="input-control" name="lastname2" id="lastname2" value="<?php echo htmlspecialchars($lastname2) ?>">
                            <b class="red-text"><?php echo $errors['lastname2']; ?></b></td>
                    </tr>

                    <tr>
                        <td> <label for="birthday">Fecha de nacimiento</label></td>
                        <td>
                            <input class="input-control" type="date" min="1910-01-01" name="birthday" id="birthday" value="<?php echo htmlspecialchars($birthday) ?>">
                            <b class="red-text"><?php echo $errors['birthday']; ?></b></td>

                        <td><label for="sex">Sexo</label></td>
                        <td>

                            Masculino<input type="radio" name="sex" id="sexM" value="M">
                            Femenino<input type="radio" name="sex" id="sexF" value="F">


                            <b class="red-text"><?php echo $errors['sex']; ?></b>
                        </td>
                        <td colspan="2"></td>
                    </tr>

                    <tr>
                        <td> <label for="phone"><?php echo utf8_decode('Tel&eacute;fono fijo') ?></label></td>
                        <td>
                            <input type="text" class="input-control" name="phone" id="phone" value="<?php echo htmlspecialchars($phone) ?>">
                            <b class="red-text"><?php echo $errors['phone']; ?></b></td>


                        <td><label for="cellphone"><?php echo utf8_decode('Tel&eacute;fono m&oacute;vil') ?></label></td>
                        <td>
                            <input type="text" class="input-control" name="cellphone" id="cellphone" value="<?php echo htmlspecialchars($cellphone) ?>">
                            <b class="red-text"><?php echo $errors['cellphone']; ?></b></td>

                        <td> <label for="email">
                                <?php echo utf8_decode('Correo electr&oacute;nico') ?>:
                            </label></td>
                        <td>
                            <input type="email" class="input-control" name="email" value="<?php echo htmlspecialchars($email) ?>" id="email">
                            <b class="red-text"><?php echo $errors['email']; ?></b></td>
                    </tr>


                    <tr>
                        <td> <label for="barrio">Barrio</label></td>
                        <td>
                            <select class="input-select" name="barrio" id="barrio">
                                <option value="" selected><?php echo utf8_decode('Elige una opci&oacute;n') ?></option>
                                <?php foreach ($barrios as $barrio) : ?>
                                    <option value='<?php echo htmlspecialchars($barrio['Bardes']); ?>'>
                                        <?php
                                        echo htmlspecialchars($barrio['Bardes']);
                                        ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <b class="red-text"><?php echo $errors['barrio']; ?></b></td>


                        <td> <label for="addr"><?php echo utf8_decode('Direcci&oacute;n del servicio') ?></label></td>
                        <td>
                            <input class="input-control" type="text" class="input-control" name="addr" id="addr" value="<?php echo htmlspecialchars($addr) ?>">
                            <b class="red-text"><?php echo $errors['addr']; ?></b></td>
                        <td> <label for="service">Tipo de servicio</label></td>
                        <td>
                            <select name="service" class="input-select" id="service">
                                <option value="">Elija un servicio</option>
                                <?php foreach ($servicios as $servicio) : ?>
                                    <option value=<?php echo htmlspecialchars($servicio['drvcse']); ?>>
                                        <?php echo htmlspecialchars($servicio['drvser']); ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                            <b class="red-text"><?php echo $errors['service']; ?></b>
                        </td>
                    </tr>

                    <tr>
                        <td> <label for="eps">Aseguradora</label></td>
                        <td>
                            <select name="eps" class="input-select" id="eps">
                                <option value="" selected><?php echo utf8_decode('Elige una opci&oacute;n') ?></option>
                                <?php foreach ($eps as $ep) : ?>
                                    <option value=<?php echo htmlspecialchars($ep['drvnlb']); ?>>
                                        <?php
                                        echo htmlspecialchars($ep['drvanm']);
                                        ?>
                                    </option>

                                <?php endforeach ?>
                            </select>
                            <b class="red-text"><?php echo $errors['eps']; ?></b></td>

                        <td> <label for="plan">Plan</label></td>
                        <td>
                            <select name="plan" class="input-select" id="plan"></select>
                            <b class="red-text"><?php echo $errors['plan']; ?></b></td>

                        <td> <label for="epsNumber"><?php echo utf8_decode('Nro. de P&oacute;liza') ?></label></td>
                        <td>
                            <input type="text" class="input-control" name="epsNumber" id="epsNumber" value="<?php echo htmlspecialchars($epsNumber) ?>">
                            <b class="red-text"><?php echo $errors['epsNumber']; ?></b></td>

                    </tr>

                    <tr id="seccionCovid">
                        <td> <label for="fechaSintomas"><?php echo utf8_decode('Inicio de s&iacute;ntomas') ?></label></td>
                        <td>
                            <input class="input-control" type="date" min="1910-01-01" name="fechaSintomas" id="fechaSintomas" value="<?php echo htmlspecialchars($fechaSintomas); ?>">
                            <b class="red-text"><?php echo $errors['fechaSintomas']; ?></b></td>

                        <td> <label for="sintomas"><?php echo utf8_decode('S&iacute;ntomas') ?></label></td>
                        <td>
                            <input type="text" class="input-control" id="sintomas" value="<?php echo htmlspecialchars($sintomas); ?>">
                            <input type="hidden" id="sintomasPost" name="sintomasPost">
                            <b class="red-text"><?php echo $errors['sintomas']; ?></b></td>

                        <td> <label for="clasificacionCaso"><?php echo utf8_decode('Clasificacion del caso') ?></label></td>
                        <td>
                            <input type="text" class="input-control" id="clasificacionCaso" value="<?php echo htmlspecialchars($clasificacionCaso); ?>">
                            <input type="hidden" id="clasificacionCasoPost" name="clasificacionCasoPost">
                            <b class="red-text"><?php echo $errors['clasificacionCaso']; ?></b></td>
                    </tr>
                    <tr>
                        <td> <label for="exams"><?php echo utf8_decode('Ex&aacute;menes') ?></label></td>
                        <td colspan="5">
                            <select name="exams" id="exams" class="solo-select">

                            </select>
                            <b class="red-text"><?php echo $errors['exams']; ?></b></td>
                    </tr>

                    <tr id="tablaExamenes">
                        <td><label for="listaExamenes">Examenes seleccionados</label></td>
                        <td colspan="5">

                            <ul id="listaExamenes">

                            </ul>
                        </td>
                        <input type="hidden" name="numeroExamenes" id="numeroExamenes">
                    </tr>

                    <tr>
                        <td colspan="6" style="text-align: center;"><input class="button" type="submit" value="Guardar" name="submit" id="submitFormulario"></td>
                    </tr>
                </tbody>
            </table>












        </form>
    </div>

    <div>
        <table id="listaDeRegistros">
            <thead>
                <tr class="encabezadoTabla">
                    <th>ID</th>
                    <th class="textoCentrado">Fecha / Hora</th>
                    <th>Tipo de dcto.</th>
                    <th>Documento</th>
                    <th>Apellidos</th>
                    <th>Nombres</th>
                    <th><?php echo utf8_decode('Tel&eacute;fono fijo') ?></th>
                    <th><?php echo utf8_decode('Tel&eacute;fono m&oacute;vil') ?></th>
                    <th>Direcci&oacute;n</th>
                    <th>Barrio</th>
                    <th><?php echo utf8_decode('Correo electr&oacute;nico') ?></th>
                    <th>Aseguradora</th>
                    <th>Plan</th>
                    <th><?php echo utf8_decode('Nro. de P&oacute;liza') ?></th>
                    <th>Servicio</th>
                    <th><?php echo utf8_decode('Ex&aacute;menes') ?></th>
                    <th>Valor a cancelar</th>
                    <th>Link de pago</th>
                    <!-- <th>Correo de Wompi</th> -->
                    <th>Pago Confirmado</th>
                    <th>Agendar</th>

                    <th>Admisi&oacute;n en laboratorio</th>
                    <!-- <th>Cancelar</th> -->
                </tr>
            </thead>
            <tbody>
                <?php foreach ($registros as $key => $registro) : ?>


                    <tr id=<?php echo "fila" . $key; ?> class=<?php if ($key % 2 === 0) {
                                                                    echo "fila1";
                                                                } else {
                                                                    echo "fila2";
                                                                } ?>>
                        <td class="textoCentrado"><?php echo htmlspecialchars($registro['id']); ?></td>
                        <td class="textoCentrado"><?php echo htmlspecialchars($registro['Fecha_data']); ?> <?php echo htmlspecialchars($registro['Hora_data']); ?></td>
                        <td class="textoCentrado"><?php echo htmlspecialchars($registro['drvtid']); ?></td>
                        <td><?php echo htmlspecialchars($registro['drvidp']); ?></td>
                        <td><?php echo htmlspecialchars($registro['drvap1']); ?> <?php echo htmlspecialchars($registro['drvap2']); ?></td>
                        <td><?php echo htmlspecialchars($registro['drvnom']); ?></td>
                        <td><?php echo htmlspecialchars($registro['drvtel']); ?></td>
                        <td><?php echo htmlspecialchars($registro['drvmov']); ?></td>
                        <td><?php echo htmlspecialchars($registro['drvdir']); ?></td>
                        <td><?php echo htmlspecialchars($registro['drvbar']); ?></td>
                        <td><?php echo htmlspecialchars($registro['drvema']); ?></td>
                        <td><?php echo htmlspecialchars($registro['drvanm']); ?></td>
                        <td><?php echo htmlspecialchars($registro['drvcpl']); ?></td>
                        <td><?php echo htmlspecialchars($registro['drvpol']); ?></td>
                        <td><?php echo htmlspecialchars($registro['drvser']); ?></td>
                        <td><input type="button" id=<?php echo "clickExams" . $key; ?> name="clickExams" onclick="showExamsAndAuths(<?php echo $key; ?>, <?php echo $registro['id']; ?>)" value="Ver" /></td>

                        <td class=<?php if ($registro['drvvcr'] == "") {
                                        if ($key % 2 === 0) {
                                            echo "fila1";
                                        } else {
                                            echo "fila2";
                                        }
                                    } else {
                                        echo "gestion2";
                                    } ?> id=<?php echo "totalTD" . $key; ?>><input type="text" name=<?php echo "total" . $key; ?> id=<?php echo "total" . $key; ?> value='<?php echo htmlspecialchars($registro['drvvcr']); ?>'> <button type="button" onclick="guardarTotal(<?php echo $key; ?>, <?php echo $registro['id']; ?>)">Guardar</button></td>

                        <td class=<?php if ($registro['drvenv'] == 'off') {
                                        if ($key % 2 === 0) {
                                            echo "fila1";
                                        } else {
                                            echo "fila2";
                                        }
                                    } else {
                                        echo "gestion2";
                                    } ?> id=<?php echo "envioLinkTD" . $key; ?>>
                            <!-- <input type="text" name=<?php echo "link" . $key; ?> id=<?php echo "link" . $key; ?> value='<?php echo htmlspecialchars($registro['drvlnk']); ?>'> -->
                            <!-- <button type="button" onclick="guardarLink(<?php echo $key; ?>, <?php echo $registro['id']; ?>)">Guardar</button> -->
                            <button type="button" onclick="enviarLink('<?php echo $key; ?>','<?php echo htmlspecialchars($registro['drvema']); ?>','<?php echo htmlspecialchars($registro['id']); ?>','<?php echo htmlspecialchars($registro['drvnom']); ?>','<?php echo htmlspecialchars($registro['drvap1']); ?> <?php echo htmlspecialchars($registro['drvap2']); ?>','<?php echo htmlspecialchars($registro['drvvcr']); ?>','<?php echo htmlspecialchars($registro['drvlnk']); ?>','<?php echo htmlspecialchars($registro['drvser']); ?>')">Enviar</button>
                            <div><b id=<?php echo "showEmailSent" . $key; ?>></b></div>
                        </td>
                        <!-- <td> -->
                        <!-- <form action="https://checkout.wompi.co/p/" method="GET">
                                <input type="hidden" name="public-key" value="pub_test_GZlevgVBlUIA4Aq8jcYjNPJBJEnbitYV" />
                                <input type="hidden" name="currency" value="COP" />
                                <input type="hidden" name="amount-in-cents" value="100000" />
                                <input type="hidden" name="reference" value='<?php echo htmlspecialchars($registro['id']); ?>' />
                                <button type="submit" class="waybox-button">
                                    Pagar con Wompi
                                </button>
                            </form> -->
                        <!-- <button type="button" name="enviar" id="enviar" onclick="envioCorreo('<?php echo htmlspecialchars($registro['drvema']); ?>','','<?php echo htmlspecialchars($registro['drvnom']); ?>','<?php echo htmlspecialchars($registro['drvap1']); ?> <?php echo htmlspecialchars($registro['drvap2']); ?>','<?php echo htmlspecialchars($registro['drvvcr']); ?>')">Enviar</button> -->
                        <!-- </td> -->
                        <td class=<?php if ($registro['drvpcf'] === 'off') {
                                        if ($key % 2 === 0) {
                                            echo "fila1";
                                        } else {
                                            echo "fila2";
                                        }
                                    } else {
                                        echo "gestion2";
                                    } ?> style="text-align: center;" id=<?php echo "checkPagoConfirmadoTD" . $key; ?>><input type="checkbox" name=<?php echo "checkPago" . htmlspecialchars($registro['id']); ?> id=<?php echo "checkPago" . htmlspecialchars($registro['id']); ?> onchange="confirmacionPago(<?php echo htmlspecialchars($registro['id']); ?>,<?php echo $key; ?>)" <?php if (htmlspecialchars($registro['drvpcf']) === "on") {
                                                                                                                                                                                                                                                                                                                                                                                            echo "checked";
                                                                                                                                                                                                                                                                                                                                                                                        }
                                                                                                                                                                                                                                                                                                                                                                                        ?>></td>

                        <td class=<?php if ($registro['drvfec'] == '0000-00-00') {
                                        if ($key % 2 === 0) {
                                            echo "fila1";
                                        } else {
                                            echo "fila2";
                                        }
                                    } else {
                                        echo "gestion3";
                                    } ?>><input type="button" value="Agendar" name="agendar" id="agendar" onclick="abrirAgenda('<?php echo htmlspecialchars($registro['id']); ?>','<?php echo htmlspecialchars($registro['drvidp']); ?>','<?php echo htmlspecialchars($registro['drvnom']); ?>'+'-'+'<?php echo htmlspecialchars($registro['drvap1']); ?> <?php echo htmlspecialchars($registro['drvap2']); ?>','<?php echo htmlspecialchars($registro['drvtel']); ?>'+'-'+'<?php echo htmlspecialchars($registro['drvmov']); ?>','<?php echo htmlspecialchars($registro['drvema']); ?>','<?php echo htmlspecialchars($registro['drvlnk']); ?>','<?php echo htmlspecialchars($registro['drvbdy']); ?>','<?php echo htmlspecialchars($registro['drvase']); ?>')" <?php if (htmlspecialchars($registro['drvpcf']) !== "on") {
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    echo "disabled";
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                ?>></td>
                        <td class=<?php if ($registro['drvord'] == '') {
                                        if ($key % 2 === 0) {
                                            echo "fila1";
                                        } else {
                                            echo "fila2";
                                        }
                                    } else {
                                        echo "gestion3";
                                    } ?> id=<?php echo "admitirTD" . $key; ?>><input type="button" value="Admitir" name="admitir" id="admitir" onclick="admitir(<?php echo $key; ?>,
                                                                                                                                                                '<?php echo htmlspecialchars($registro['id']); ?>',
                                                                                                                                                                '<?php echo htmlspecialchars($registro['drvidp']); ?>',
                                                                                                                                                                '<?php echo htmlspecialchars($registro['drvtid']); ?>', 
                                                                                                                                                                '<?php echo htmlspecialchars($registro['drvap1']); ?>', 
                                                                                                                                                                '<?php echo htmlspecialchars($registro['drvap2']); ?>', 
                                                                                                                                                                '<?php echo htmlspecialchars($registro['drvnom']); ?>', 
                                                                                                                                                                '<?php echo htmlspecialchars($registro['drvbdy']); ?>', 
                                                                                                                                                                '<?php echo htmlspecialchars($registro['drvsex']); ?>', 
                                                                                                                                                                '<?php echo htmlspecialchars($registro['drvdir']); ?>', 
                                                                                                                                                                '<?php echo htmlspecialchars($registro['drvmov']); ?>', 
                                                                                                                                                                '<?php echo htmlspecialchars($registro['drvema']); ?>',
                                                                                                                                                                '<?php echo htmlspecialchars($registro['drvtel']); ?>', 
                                                                                                                                                                '<?php echo htmlspecialchars($registro['drvase']); ?>', 
                                                                                                                                                                '<?php echo htmlspecialchars($registro['drvcpl']); ?>',
                                                                                                                                                                '<?php echo htmlspecialchars($registro['drvpol']); ?>',
                                                                                                                                                                '<?php echo htmlspecialchars($registro['drvvcr']); ?>',
                                                                                                                                                                '<?php echo htmlspecialchars($registro['drvfsi']); ?>',
                                                                                                                                                                '<?php echo htmlspecialchars($registro['drvsin']); ?>',
                                                                                                                                                                '<?php echo htmlspecialchars($registro['drvcla']); ?>')" <?php if (htmlspecialchars($registro['drvfec']) == "0000-00-00") {
                                                                                                                                                                                                                                echo "disabled";
                                                                                                                                                                                                                            };
                                                                                                                                                                                                                            ?>>
                            <div><label id=<?php echo "orden" . $key; ?>></label></div>
                        </td>

                        <!-- <td class="textoCentrado"><button type='button' onclick="cancelarRegistro(<?php echo $registro['id']; ?>)">X</button></td> -->
                    </tr>

                <?php endforeach ?>

            </tbody>
        </table>


        <div id="dialog-confirm" title="Ex&aacute;menes y autorizaciones">
            <h2>Ingreso n&uacute;mero <b id="idDeLosExamenes"></b></h2>
            <table class="ui-widget ui-widget-content">
                <thead>
                    <tr class="encabezadoTabla">
                        <th>Examen</th>
                        <th>Nombre del examen</th>
                        <th>Autorizaci&oacute;n</th>
                    </tr>
                </thead>
                <tbody id="authTableBody">
                </tbody>
            </table>
        </div>

        <div id="dialog-cancel" title="Cancelar registro">
            <h2 style='min-width: -moz-max-content;'>Est&aacute; seguro de cancelar el registro <b id="idDelRegistroACancelar"></b> ?</h2>

        </div>
    </div>
    <br>
    <table align="center">
        <tbody>
            <tr>
                <td><input type="button" value="Cerrar" onclick="cerrarVentana();" style="width:100px"></td>
            </tr>
        </tbody>
    </table>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <!-- JS -->
    <!-- <script src="https://unpkg.com/multiple-select@1.5.2/dist/multiple-select.min.js"></script> -->
    <script>
        let tempExams = [];
        const urlMatrix = '/matrix/citas/procesos/listaDeEspera.php?wemp_pmla=05'
        const urlAPI = '<?php echo $urlAPI; ?>';
        const wompiKey = '<?php echo $wompiKey; ?>';
        // const wompiKey = "pub_prod_J2T5YcYqb8pv6QKRNVjyTJzeRcIFyJJc"

        $(document).ready(function() {

            $('#newExam').hide();
            $('#newAut').hide();
            $('#agregarExamen').hide();
            $('#tablaExamenes').hide();
            $('#showEmailSent').hide();
            $('#seccionCovid').hide()
        })

        // $('#submitFormulario').click(function() {
        //     $('#submitFormulario').attr({
        //         disabled: true,
        //     });
        // })




        var sintomas = [
            "Tos",
            "Fiebre",
            "Odinofagia",
            "Dificultad respiratoria",
            "Fatiga o adinamia"
        ];


        var clasificacionCaso = [
            "Sospechoso",
            "Probable",
            "Conf. por laboratorio",
            "Conf. por clinica",
            "Conf. por nexo epidemiologico"
        ];


        function split(val) {
            return val.split(/,\s*/);
        }

        function extractLast(term) {
            return split(term).pop();
        }

        $("#sintomas")
            // don't navigate away from the field on tab when selecting an item
            .on("keydown", function(event) {
                if (event.keyCode === $.ui.keyCode.TAB &&
                    $(this).autocomplete("instance").menu.active) {
                    event.preventDefault();
                }
            })
            .autocomplete({
                minLength: 0,
                source: function(request, response) {
                    // delegate back to autocomplete, but extract the last term
                    response($.ui.autocomplete.filter(
                        sintomas, extractLast(request.term)));
                },
                focus: function() {
                    // prevent value inserted on focus
                    return false;
                },
                select: function(event, ui) {
                    var terms = split(this.value);
                    // remove the current input
                    terms.pop();
                    // add the selected item
                    terms.push(ui.item.value);
                    // add placeholder to get the comma-and-space at the end
                    terms.push("");
                    this.value = terms.join(", ");

                    $('#sintomasPost').val(this.value)
                    return false;
                }
            });

        $("#clasificacionCaso")
            // don't navigate away from the field on tab when selecting an item
            .on("keydown", function(event) {
                if (event.keyCode === $.ui.keyCode.TAB &&
                    $(this).autocomplete("instance").menu.active) {
                    event.preventDefault();
                }
            })
            .autocomplete({
                minLength: 0,
                source: function(request, response) {
                    // delegate back to autocomplete, but extract the last term
                    response($.ui.autocomplete.filter(
                        clasificacionCaso, extractLast(request.term)));
                },
                focus: function() {
                    // prevent value inserted on focus
                    return false;
                },
                select: function(event, ui) {
                    var terms = split(this.value);
                    // remove the current input
                    terms.pop();
                    // add the selected item
                    terms.push(ui.item.value);
                    // add placeholder to get the comma-and-space at the end
                    terms.push("");
                    this.value = terms.join(", ");
                    $('#clasificacionCasoPost').val(this.value)
                    return false;
                }
            });


        $('#exams').change(function() {
            $('#tablaExamenes').show();
            let newExam = $('#exams').val();
            let cups_and_name = newExam.split(/-(.+)/, 2)

            // cups_and_name[0]
            console.log($('#valor'));

            $.ajax({
                url: urlMatrix,
                type: 'POST',
                data: {
                    "consultarValorParticular": cups_and_name[0],
                    "consultaAjax": ""
                },
                beforeSend: function() {},
                success: function(response) {
                    console.log(response);
                },
                error: function() {
                    alert("error")
                }
            })

            tempExams.push([cups_and_name[0], cups_and_name[1]])
            let html = ""
            for (let i = 0; i < tempExams.length; i++) {
                let tr = `<tr class=${i%2 ==0 ? "fila1": "fila2" }><td><label>${tempExams[i][0]}</label><input type="text" style="display:none" name="exam-${i}" id="exam-${i}" value="${tempExams[i][0]}"></td><td><label>${tempExams[i][1]}</label><input type="text" style="display:none" name="examName-${i}" id="examName-${i}" value="${tempExams[i][1]}"></td><td><button type="button" onclick="eliminarExamen(${i})">X</button></td></tr>`
                html = html + tr;
            }



            $('#numeroExamenes').val(tempExams.length) // Se usa para enviar el n&uacute;mero de ex&aacute;menes al hacer submit
            $('#listaExamenes').html(html)
        })

        function eliminarExamen(index) {
            let currentExams = tempExams
            let filteredExams = tempExams.filter(e => currentExams.indexOf(e) !== index)
            tempExams = filteredExams
            let html = ""
            for (let i = 0; i < tempExams.length; i++) {
                let tr = `<tr><td><label>${tempExams[i][0]}</label><input type="text" style="display:none" name="exam-${i}" id="exam-${i}" value="${tempExams[i][0]}"></td><td><label>${tempExams[i][1]}</label><input type="text" style="display:none" name="examName-${i}" id="examName-${i}" value="${tempExams[i][1]}"></td><td><button type="button" onclick="eliminarExamen(${i})">X</button></td></tr>`
                html = html + tr;
            }
            $('#listaExamenes').html(html)
            $('#numeroExamenes').val(tempExams.length) // Se usa para enviar el n&uacute;mero de ex&aacute;menes al hacer submit
            if (tempExams.length === 0) {
                $('#tablaExamenes').hide();
            }
        }

        function cancelarRegistro(id) {
            $('#idDelRegistroACancelar').html(id)
            $("#dialog-cancel").dialog({

                resizable: false,
                height: "auto",
                width: "auto",
                modal: true,
                buttons: {
                    "Confirmar": function() {
                        $.ajax({
                            data: {
                                "consultaAjax": "",
                                "cancelarRegistro": id
                            },
                            url: urlMatrix,
                            type: "POST",
                            beforeSend: function() {},
                            success: function(response) {
                                // console.log(response);
                                location.reload()
                            },
                            error: function() {
                                alert("error")
                            }

                        })
                    },
                    "Cerrar": function() {
                        $(this).dialog("close");
                    }
                }
            });

        }

        function abrirAgenda(id, cedula, paciente, telefono, email, url, fechaNacimiento, aseguradora) {
            let comentarios = ""
            var MILLISECONDS_IN_A_YEAR = 1000 * 60 * 60 * 24 * 365.25;
            const getAge = birthDate => Math.floor((new Date() - new Date(birthDate).getTime()) / MILLISECONDS_IN_A_YEAR)

            // const reemplazar = comments => replace(/\s/g, "_", comments)

            $.ajax({
                data: {
                    "examenesParaComentarios": id,
                    "consultaAjax": ""
                },
                url: urlMatrix,
                type: 'post',
                beforeSend: function() {},
                success: function(response) {

                    respuesta = response;
                    comentarios = respuesta.replace(/\s/g, "_")
                    let edad = getAge(fechaNacimiento)
                    window.open(`/matrix/citas/procesos/calendar.php?empresa=citaslc&wemp_pmla=01&caso=2&wsw=&fest=off&consultaAjax=&id=${id}&cedula=${cedula}&paciente=${paciente}&telefono=${telefono}&email=${email}&url=${url}&edad=${edad}&comentarios=${comentarios}&aseguradora=${aseguradora}`, "_blank")

                },
                error: function() {
                    alert("error")
                }
            })



        }

        function admitir(index, id, identificacion, tipodedocumento, apellido1, apellido2, nombres, fechaDeNacimiento, sexo, direccion, celular, email, fijo, aseguradora, plan, poliza, valorACancelar, fechaSintomas, sintomas, clasificacion) {
            // console.log(plan);
            let usuario = '<?php echo $wuse; ?>'
            console.log(usuario, "usuario")
            let date = new Date()
            let year = date.getFullYear()
            let month = date.getMonth()
            month = month < "10" ? `0${month+1}` : month
            let day = date.getDate()
            day = day < "10" ? `0${day}` : day

            let hour = date.getHours()
            hour = hour < "10" ? `0${hour}` : hour
            let minutes = date.getMinutes()
            minutes = minutes < "10" ? `0${minutes}` : minutes
            let seconds = date.getSeconds()
            seconds = seconds < "10" ? `0${seconds}` : seconds

            let aaaammddhhmmss = year + month + day + hour + minutes + seconds


            let aaaammdd = fechaDeNacimiento.replaceAll("-", "")


            $.ajax({
                data: {
                    "consultarCodigoEmpresaLab": aseguradora,
                    "consultaAjax": ""
                },
                url: urlMatrix,
                type: "POST",
                beforeSend: function() {},
                success: function(codEmpresaLab) {

                    $.ajax({
                        url: urlMatrix,
                        type: 'POST',
                        data: {
                            "consultaExamenesAGrabar": id,
                            "consultaAjax": ""
                        },
                        beforeSend: function() {},
                        success: function(response) {

                            console.log(response, "response");

                            let msh = `MSH|^~&|MATRIX|PMLA|LIS4D|LMLA|${aaaammddhhmmss}||OML^O21|DRIVE-${id}|P|2.5|`
                            let pid = `PID||${identificacion}^${tipodedocumento}|||${apellido1}&${apellido2}^${nombres}||${aaaammdd}|${sexo}|||${direccion}^^MEDELLIN^ANTIOQUIA^^COLOMBIA||${celular}^^^${email}^^^${fijo}|`
                            let in1 = `IN1||${codEmpresaLab}||||||||||||||`
                            let orc = `ORC|NW|0|DRIVE-${id}^||IP||^^^${aaaammddhhmmss}^^R|||^^^^^^^|`

                            // Donde va la autorización?
                            // hora de la cita?
                            let horaCita = "" // horadelacita hh:mm
                            let fechaCita = "" // fechadelacita aaaammdd

                            // Creación de los registros OBR 
                            let phpJSON = JSON.parse(response);
                            let obrs = []
                            let autorizacionesArray = [];
                            for (let index = 0; index < phpJSON.length; index++) {

                                let exam_labcod = phpJSON[index].drvnex
                                let examn_labcod_replaced = exam_labcod.replace("-C0", "^C0")
                                obrs.push(`OBR|||DRIVE-${id}-${phpJSON[index].drvpos}|${phpJSON[index].drvcup}^${examn_labcod_replaced}||${aaaammddhhmmss}|||||||${horaCita}||^^^^^||||||||||O|||||||||||${fechaCita}|`)
                                autorizacionesArray.push(phpJSON[index].drvaut);
                            }
                            let joinedObrs = obrs.join("<br>");
                            let autorizaciones = autorizacionesArray.join("~");
                            sintomas = sintomas.replace(",", "~")
                            clasificacion = clasificacion.replace(",", "~")
                            ////

                            let nte1 = `NTE|1|P|345^3.5^Fecha Inicio de Sintomas^${fechaSintomas}|ENCUESTA`
                            let nte2 = `NTE|2|P|345^3.6^Clasificacion del Caso^${clasificacion}|ENCUESTA`
                            let nte3 = `NTE|3|P|345^5,4^Sintomas^${sintomas}|ENCUESTA`
                            let nte4 = `NTE|4|P|Efectivo^0|PAGO`
                            let nte5 = `NTE|5|P|${usuario}|CAJERO`
                            let nte6 = `NTE|6|P|TD^${valorACancelar}^WHOMPI|PAGO`
                            let nte7 = `NTE|7|P|S015^DRIVE THRU|SEDE`
                            let nte8 = `NTE|8|P|${autorizaciones}|AUTORIZACIONES`
                            let nte9 = `NTE|9|P|${poliza}|POLIZA`

                            // console.log(obrs);
                            let mensaje = msh + "<br>" + pid + "<br>" + in1 + "<br>" + orc + "<br>" + nte1 + "<br>" + nte2 + "<br>" + nte3 + "<br>" + nte4 + "<br>" + nte5 + "<br>" + nte6 + "<br>" + nte7 + "<br>" + nte8 + "<br>" + nte9 + "<br>" + joinedObrs
                            $('#admitir')
                                .prop('disabled', true)
                            $(`#orden${index}`).html('Creando orden...');
                            $(`#orden${index}`).addClass('showOrderCreatedSuccess');
                            $.ajax({
                                data: JSON.stringify({
                                    "msg": mensaje
                                }),
                                dataType: "json",
                                contentType: "application/json; charset=utf-8",
                                headers: {
                                    "Authorization": "fab383b9-9e42-4758-a91d-10ebbf6ed68f",
                                    "Content-Type": "application/json"
                                },
                                url: urlAPI,
                                type: 'post',
                                beforeSend: function() {},
                                success: function(response) {
                                    if (response.orden !== "") {
                                        $('#admitir')
                                            .prop('disabled', true)
                                        $(`#orden${index}`).html(`Orden creada ${response.orden}`);
                                        $(`#orden${index}`).addClass('showOrderCreatedSuccess');

                                        $.ajax({
                                            url: urlMatrix,
                                            type: 'POST',
                                            data: {
                                                "consultaAjax": "",
                                                "ordenLaboratorio": response.orden,
                                                "id": id
                                            },
                                            beforeSend: function() {},
                                            success: function(result) {
                                                // Orden grabada
                                                $(`#admitirTD${index}`).removeClass('fila1')
                                                $(`#admitirTD${index}`).addClass('gestion3')


                                            },
                                            error: function() {
                                                alert("error")
                                            }
                                        })
                                    } else {
                                        $('#admitir')
                                            .prop('disabled', false)
                                        $(`#orden${index}`).html(`La orden no se pudo crear.`);
                                        $(`#orden${index}`).addClass('showOrderCreatedSuccess');
                                    }


                                },
                                error: function() {
                                    alert("error")
                                }
                            })

                        },
                        error: function() {
                            alert("error")
                        }
                    });


                },
                error: function() {
                    alert("error")
                }
            })






        }

        function copiarAutorizaciones(inputId, count, id) {

            let valorACopiar = $(`#${inputId}`).val();
            for (let index = 1; index < count; index++) {
                console.log(`#auth-${id}-${index}`, valorACopiar);
                $(`#auth-${id}-${index}`).val(valorACopiar);
            }

        }

        function guardarAutorizaciones(count, id) {

            var autorizaciones = [];

            for (let index = 0; index < count; index++) {
                let valorAGuardar = $(`#cup-${id}-${index}`).html();
                let numAutorizacion = $(`#auth-${id}-${index}`).val();
                autorizaciones[index] = {
                    drvcit: id,
                    drvcup: valorAGuardar,
                    drvaut: numAutorizacion
                };
            }


            $.ajax({
                data: {
                    "guardarAutorizaciones": autorizaciones,
                    "guardarAutorizacionesId": id,
                    "consultaAjax": ""
                },
                url: urlMatrix,
                type: 'post',
                beforeSend: function() {},
                success: function(response) {
                    console.log(response);
                    // $("#exams").html(response);
                },
                error: function() {
                    alert("error")
                }
            })
        }


        $("#eps").change(function() {
            let eps = $('#eps').val();
            $.ajax({
                data: {
                    "aseguradoraSeleccionada": eps,
                    "tipoServicio": $('#service').val(),
                    "consultaAjax": ""
                },
                url: urlMatrix,
                type: 'post',
                beforeSend: function() {},
                success: function(response) {
                    $("#plan").html(response);

                    $.ajax({
                        data: {
                            "obtenerExamenes": eps,
                            "consultaAjax": ""
                        },
                        url: urlMatrix,
                        type: "POST",
                        beforeSend: function() {},
                        success: function(response) {
                            $("#exams").html(response);
                        },
                        error: function() {
                            alert("error")
                        }
                    })


                },
                error: function() {
                    alert("error")
                }
            });
        })

        $("#service").change(function() {

            if ($('#service').val() === "DT" || $('#service').val() === "DTP") {
                $('#seccionCovid').show()
            } else {
                $('#seccionCovid').hide()
            }

            $.ajax({
                data: {

                    "servicioSeleccionado": $('#service').val(),
                    "consultaAjax": ""
                },
                url: urlMatrix,
                type: 'post',
                beforeSend: function() {},
                success: function(response) {
                    $("#exams").html(response);
                },
                error: function() {
                    alert("error")
                }
            });
        })


        function showExamsAndAuths(index, id) {


            $("#dialog-confirm").dialog({

                resizable: false,
                height: "auto",
                width: "auto",


                modal: true,
                buttons: {
                    "Cerrar": function() {
                        $(this).dialog("close");
                    }
                }
            });

            $.ajax({
                url: urlMatrix,
                type: 'POST',
                data: {
                    "consultaExamenes": id,
                    "consultaAjax": ""
                },
                beforeSend: function() {},
                success: function(response) {
                    $('#idDeLosExamenes').html(id)
                    $('#authTableBody').html(response)
                },
                error: function() {
                    alert("error")
                }
            });



        }

        function guardarTotal(index, id) {

            let valor = $(`#total${index}`).val()

            $.ajax({
                url: urlMatrix,
                type: 'POST',
                data: {
                    "total": valor,
                    "id": id,
                    "consultaAjax": ""
                },
                beforeSend: function() {},
                success: function(response) {
                    $(`#totalTD${index}`).removeClass('fila1')
                    $(`#totalTD${index}`).addClass('gestion2')
                    location.reload()
                },
                error: function() {
                    alert("error")
                }
            })
        }

        function guardarLink(index, id) {

            let valor = $(`#link${index}`).val()

            $.ajax({
                url: urlMatrix,
                type: 'POST',
                data: {
                    "linkAGuardar": valor,
                    "id": id,
                    "consultaAjax": ""
                },
                beforeSend: function() {},
                success: function(response) {
                    console.log(response);
                },
                error: function() {
                    alert("error")
                }
            })
        }


        function confirmacionPago(id, index) {


            console.log($(`#checkPago${id}`).val())

            $.ajax({
                url: urlMatrix,
                type: 'POST',
                data: {
                    "confirmarPago": id
                },
                beforeSend: function() {},
                success: function(response) {
                    console.log(response);
                    $(`#checkPagoConfirmadoTD${index}`).removeClass('fila1')
                    $(`#checkPagoConfirmadoTD${index}`).addClass('gestion2')
                    location.reload();
                },
                error: function() {
                    alert("error")
                }
            })

        }

        function enviarLink(index, email, id, nombres, apellidos, valorACancelar, urlWompi, servicio) {

            console.log(urlWompi);
            console.log($(`#link${index}`).val());

            let valorACancelarWompi = valorACancelar * 100
            let wdestinatarios = [email];
            let servicioWompi = ""
            const date = new Date();
            const yearof = date.getFullYear();
            if (servicio === "DM") {
                servicioWompi = "Domicilio"
            }
            if (servicio === "DT" || servicio === "DTP") {
                servicioWompi = "Drive Thru"
            }

            let mensaje = `<!DOCTYPE html><html lang="en" ><head><meta charset="UTF-8"></head><body><style>*:not(br):not(tr):not(html){font-family:Arial,Helvetica,sans-serif;box-sizing:border-box}body{width:100% !important;height:100%;margin:0;line-height:1.4;background-color:#F2F4F6;color:#74787E;-webkit-text-size-adjust:none}p,ul,ol,blockquote{line-height:1.4;text-align:left}a{color:#3869D4;text-decoration:none}a img{border:none}.email-wrapper{width:100%;margin:0;padding:0;-premailer-width:100%;-premailer-cellpadding:0;-premailer-cellspacing:0;background-color:#F2F4F6}.email-content{width:100%;margin:0;padding:0;-premailer-width:100%;-premailer-cellpadding:0;-premailer-cellspacing:0}.email-masthead{padding:25px 0;text-align:center}.email-masthead_logo{width:94px}.email-masthead_name{font-size:16px;font-weight:bold;color:#bbbfc3;text-decoration:none;text-shadow:0 1px 0 white}.email-body{width:100%;margin:0;padding:0;-premailer-width:100%;-premailer-cellpadding:0;-premailer-cellspacing:0;border-top:1px solid #EDEFF2;border-bottom:1px solid #EDEFF2;background-color:#FFF}.email-body_inner{width:570px;margin:0 auto;padding:0;-premailer-width:570px;-premailer-cellpadding:0;-premailer-cellspacing:0;background-color:#FFF}.email-footer{width:570px;margin:0 auto;padding:0;-premailer-width:570px;-premailer-cellpadding:0;-premailer-cellspacing:0;text-align:center}.email-footer p{color:#AEAEAE}.body-action{width:100%;margin:30px auto;padding:0;-premailer-width:100%;-premailer-cellpadding:0;-premailer-cellspacing:0;text-align:center}.body-sub{margin-top:25px;padding-top:25px;border-top:1px solid #EDEFF2}.content-cell{padding:35px}.preheader{display:none !important}.align-right{text-align:right}.align-left{text-align:left}.align-center{text-align:center}@media only screen and (max-width: 600px){.email-body_inner,.email-footer{width:100% !important}}@media only screen and (max-width: 500px){.button{width:100% !important}}.button{background-color:#0054a4;border-top:10px solid #0054a4;border-right:18px solid #0054a4;border-bottom:10px solid #0054a4;border-left:18px solid #0054a4;display:inline-block;color:#FFF;font-size:20px;text-decoration:none;border-radius:4px;box-shadow:0 2px 3px rgba(0,0,0,0.16);-webkit-text-size-adjust:none;cursor:pointer}h1{margin-top:0;color:#2F3133;font-size:19px;font-weight:bold;text-align:left}h2{margin-top:0;color:#2F3133;font-size:16px;font-weight:bold;text-align:left}h3{margin-top:0;color:#2F3133;font-size:14px;font-weight:bold;text-align:left}p{margin-top:0;color:#74787E;font-size:16px;line-height:1.5em;text-align:left}p.disclaimer{color:#8D9095}p.sub{font-size:12px}p.center{text-align:center}.waybox-button{display:inline-block;height:40px;line-height:40px;background-color:rgb(26, 69, 148);border:0px none;border-radius:4px;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol";font-weight:400;font-size:14px;padding:0px 16px;color:white;cursor:pointer}.waybox-button::before{content:"";display:inline-block;width:16px;height:16px;margin-right:8px;background-image:url("data:image/svg+xml;charset=utf8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 229.5 229.5'%3E%3Cpath fill='%23fff' d='M214.419 32.12A7.502 7.502 0 0 0 209 25.927L116.76.275a7.496 7.496 0 0 0-4.02 0L20.5 25.927a7.5 7.5 0 0 0-5.419 6.193c-.535 3.847-12.74 94.743 18.565 139.961 31.268 45.164 77.395 56.738 79.343 57.209a7.484 7.484 0 0 0 3.522 0c1.949-.471 48.076-12.045 79.343-57.209 31.305-45.217 19.1-136.113 18.565-139.961zm-40.186 53.066l-62.917 62.917c-1.464 1.464-3.384 2.197-5.303 2.197s-3.839-.732-5.303-2.197l-38.901-38.901a7.497 7.497 0 0 1 0-10.606l7.724-7.724a7.5 7.5 0 0 1 10.606 0l25.874 25.874 49.89-49.891a7.497 7.497 0 0 1 10.606 0l7.724 7.724a7.5 7.5 0 0 1 0 10.607z'/%3E%3C/svg%3E");background-size:contain;vertical-align:middle;transform:translateY(-8%)}</style><table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0"><tr><td align="center"><table class="email-content" width="100%" cellpadding="0" cellspacing="0"><tr><td class="email-masthead" style="text-align: center;"> <img width="300" alt="Laboratorios Las Am&eacute;ricas AUNA" class="rnb-logo-img" src="https://i.ibb.co/yYz1wcb/laboratorios.png"></td></tr><tr><td class="email-body" width="100%" cellpadding="0" cellspacing="0"><table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0"><tr><td class="content-cell"><p class="">Se&ntilde;or(a) ${nombres} ${apellidos}, usted solicit&oacute; previamente la asignaci&oacute;n de una cita para toma de muestra de laboratorio ${servicioWompi}. A continuaci&oacute;n, le confirmamos el valor a cancelar: ${valorACancelar}.</p><b>Instrucciones para realizar su pago en l&iacute;nea: </b><ul><li>Acceda al siguiente enlace <a href='https://checkout.wompi.co/p/?public-key=${wompiKey}&currency=COP&amount-in-cents=${valorACancelarWompi}&reference=${id}' target="_blank" >Paga con Wompi</a> que lo conducir&aacute; a nuestra pasarela de pagos en l&iacute;nea y siga las instrucciones.</li><li>Recuerde guardar su comprobante de pago.</li><li>Una vez nuestro sistema identifique la cancelaci&oacute;n de este, ser&aacute; contactado por nuestro personal para realzar la asignaci&oacute;n de su cita y admisi&oacute;n, proceso para el cual es indispensable que cuente con todos demogr&aacute;ficos del paciente.</li></ul><b>Nos transformamos para cuidarte.</b><p class="disclaimer">Recuerde que sus datos personales ser&aacute;n tratados por Auna Las Am&eacute;ricas (Promotora M&eacute;dica Las Am&eacute;ricas S.A. y sus filiales), de acuerdo con su Pol&iacute;tica de Tratamiento, para los fines relacionados con su objeto social y en especial para los siguientes fines: Atenci&oacute;n por toma de muestra del laboratorio. En todo caso, en cualquier momento y de acuerdo con la ley 1581 de 2012, puede revocar el consentimiento y ejercer su derecho a la supresi&oacute;n de los mismos.</p><p class="disclaimer"> Esperamos haber entregado la informaci&oacute;n necesaria para que tenga una buena experiencia con su atenci&oacute;n. Si tiene alguna inquietud, puede comunicarse con nosotros a trav&eacute;s de este mismo correo electr&oacute;nico o a la l&iacute;nea telef&oacute;nica 3227900. Su horario de atenci&oacute;n es de lunes a viernes de 7:00 am a 5:00 pm y s&aacute;bados 7:00 am a 1:00 pm.</p></td></tr></table></td></tr><tr><td><table class="email-footer" align="center" width="570" cellpadding="0" cellspacing="0"><tr><td class="content-cell" align="center"><p class="sub align-center">&copy; 2020 Laboratorios Las Am&eacute;ricas AUNA. Todos los derechos reservados. <br>Diagonal 75B 2A 80 Interior 140 <br>Medell&iacute;n, Colombia <br>Tel: +57 (4) 444 9092</p></td></tr></table></td></tr></table></td></tr></table></body></html>`
            let asunto = "Preadmisión toma de muestras - Laboratorios Las Américas Auna"

            $.ajax({
                url: urlMatrix,
                type: 'POST',
                data: {
                    "consultaAjax": "",
                    "id": id,
                    "asunto": asunto,
                    "mensaje": mensaje,
                    wdestinatarios

                },
                beforeSend: function() {},
                success: function(response) {

                    if (response === "1") {
                        $(`#showEmailSent${index}`).html("Correo enviado")
                        $(`#showEmailSent${index}`).addClass("showEmailSentSuccess")
                        $(`#envioLinkTD${index}`).removeClass("fila1")
                        $(`#envioLinkTD${index}`).addClass("gestion2")
                        $(`#showEmailSent${index}`).show()
                        setTimeout(() => {
                            $(`#showEmailSent${index}`).hide()
                        }, 2000);
                    } else {
                        $(`#showEmailSent${index}`).html("No se envi&oacute; el correo")
                        $(`#showEmailSent${index}`).addClass("showEmailSentFailure")
                        $(`#showEmailSent${index}`).show()
                        setTimeout(() => {
                            $(`#showEmailSent${index}`).hide()
                        }, 2000);
                    }


                },
                error: function() {
                    alert("error")
                }
            });




        }
    </script>
</body>

</html>