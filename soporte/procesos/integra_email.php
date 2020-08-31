<?
include("PHPMailer.php");
include("class.smtp.php");
require_once 'PHPMailerAutoload.php';
?>

<?
///////////////////////// ENVIO DEL EMAIL : ///////////////////////////////////

function sendToEmail1($Contenido)
{
    $fecha_data = date('Y-m-d H:i');

    $mail = new PHPMailer(true);                                // Passing `true` enables exceptions
    try
    {
        //Server settings
        $mail->SMTPDebug = 1;                                       // Enable verbose debug output
        $mail->isSMTP();                                            // Set mailer to use SMTP
        $mail->Host = 'localhost';
        $mail->Host = 'smtp.gmail.com';                             // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                                     // Enable SMTP authentication
        $mail->Username = 'informatica.clinica@lasamericas.com.co'; // SMTP username
        $mail->Password = 'Informatica2020';                               // SMTP password
        $mail->SMTPSecure = 'ssl';                                  // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465;                                          // TCP port to connect to

        //Recipients
        $mail->setFrom('informatica.clinica@lasamericas.com.co', 'Mensaje del Integrador');
        $mail->addAddress('william.atehortua@lasamericas.com.co', 'Informatica');   // Add a recipient
        $mail->addAddress('informatica.clinica@lasamericas.com.co', 'Informatica');     // Add a recipient
        $mail->addCC('registrosmed@lasamericas.com.co');
        $mail->addCC('facturacion@lasamericas.com.co');
        $mail->addCC('serviciofarmaceutico@lasamericas.com.co');
        $mail->addCC('urgenciasfac@dos.correolasamericas.com');
        //$mail->addCC('willat02@gmail.com');

        //Content
        $mail->isHTML(true);                                        // Set email format to HTML
        $mail->Subject = 'Informe de Problemas con el Integrador';
        $mail->Body = $Contenido;
        //echo $Contenido.' Fecha_data :'.$fecha_data;              //Habilitar esta linea si se quiere visualizar el contenido del mensaje en el navegador
        $mail->send();                                              //Comentar para que no envie correos mientras se hacen pruebas.
        //echo 'El mensaje se ha enviado correctamente';            //Descomentar si se desea un mensaje de confirmacion en el browser
    }
    catch (Exception $e)
    {
        //echo 'El mensaje no pudo ser enviado: ', $mail->ErrorInfo;
    }
}


function sendToEmail2($Contenido2)
{
    $fecha_data = date('Y-m-d H:i');

    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    try
    {
        //Server settings
        $mail->SMTPDebug = 1;                                 // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'localhost';
        $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'informatica.clinica@lasamericas.com.co';               // SMTP username
        $mail->Password = 'Informatica2020';                      // SMTP password
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465;                                    // TCP port to connect to

        //Recipients
        $mail->setFrom('informatica.clinica@lasamericas.com.co', 'Mensaje del Integrador');
        $mail->addAddress('william.atehortua@lasamericas.com.co', 'Informatica');     // Add a recipient
        $mail->addAddress('informatica.clinica@lasamericas.com.co', 'Informatica');     // Add a recipient
        $mail->addCC('serviciofarmaceutico@lasamericas.com.co');

        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Informe de Problemas con el Integrador';
        $mail->Body = $Contenido2;
        //echo $Contenido2.' Fecha_data :'.$fecha_data;
        $mail->send();    //esta comentado para que no envíe correos mientras termino las pruebas.
        //echo 'El mensaje se ha enviado correctamente';
    }
    catch (Exception $e)
    {
        //echo 'El mensaje no pudo ser enviado: ', $mail->ErrorInfo;
    }
}

function sendToEmail3($Contenido3)
{
    $fecha_data = date('Y-m-d H:i');

    $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
    try
    {
        //Server settings
        $mail->SMTPDebug = 1;                                 // Enable verbose debug output
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = 'localhost';
        $mail->Host = 'smtp.gmail.com';                       // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = 'informatica.clinica@lasamericas.com.co';               // SMTP username
        $mail->Password = 'Informatica2020';                      // SMTP password
        $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted
        $mail->Port = 465;                                    // TCP port to connect to

        //Recipients
        $mail->setFrom('informatica.clinica@lasamericas.com.co', 'Mensaje del Integrador');
        $mail->addAddress('afijosum@dos.correolasamericas.com', 'Activos Fijos y Suministros');     // Add a recipient
        $mail->addCC('william.atehortua@lasamericas.com.co', 'Informatica');     // Add a recipient

        //Content
        $mail->isHTML(true);                                  // Set email format to HTML
        $mail->Subject = 'Informe de Problemas con el Integrador';
        $mail->Body = $Contenido3;
        //echo $Contenido2.' Fecha_data :'.$fecha_data;
        $mail->send();    //esta comentado para que no envíe correos mientras termino las pruebas.
        //echo 'El mensaje se ha enviado correctamente';
    }
    catch (Exception $e)
    {
        //echo 'El mensaje no pudo ser enviado: ', $mail->ErrorInfo;
    }
}
?>