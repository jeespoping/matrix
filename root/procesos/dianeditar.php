<!--El programa realiza la captura, consulta, edita la informacion correspondiente del root_000119 y de amedian. -->
<!--Publicacion: 2018-12-19, 
	Por: Didier Orozco Carmona. 
	-->
<!DOCTYPE html>
<html lang="esp" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->
Para que muestre eñes y tildes -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RUT</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="../../Desktop/Proyecto DIAN/estilosevent.css" rel="stylesheet" type="text/css">
    <script src="http://code.jquery.com/jquery-1.9.1.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /><!--Estilo para el calendario-->
    <script src="../../Desktop/Proyecto DIAN/calendarioevent.js" type="text/javascript"></script>
    <script src="../../Desktop/Proyecto DIAN/JsProcesosevent.js" type="text/javascript"></script>
	<link href="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="http://matrixtest.lasamericas.com.co/matrix/soporte/procesos/stylehelpDesk.css" rel="stylesheet">
    <script src="http://code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
	<script src="http://mx.lasamericas.com.co/matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
    <script>
        $(function() {
			$( "#datepicker1" ).datepicker();
            $( "#datepicker2" ).datepicker();
			$( "#datepicker3" ).datepicker();
			$( "#datepicker4" ).datepicker();
        });
    </script>
    <style>
        .alternar:hover{ background-color:#e1edf7;}
.Estilo4 {color: #000000; font-weight: bold; }
    </style>
    <script>
        function centrar() {
            iz=(screen.width-document.body.clientWidth) / 2;
            de=(screen.height-document.body.clientHeight) / 2;
            moveTo(iz,de);
        }
    </script>
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
        $conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexión con la BD de Facturación");
    }
	
	
	
	
	$dian2=$_POST['dian2'];		$dian49=$_POST['dian49'];		$dian541=$_POST['dian541'];
	$dian4=$_POST['dian4'];		$dian501=$_POST['dian501'];		$dian542=$_POST['dian542'];
	$dian_5=$_POST['dian_5'];		$dian502=$_POST['dian502'];		$dian543=$_POST['dian543'];
	$dian6=$_POST['dian6'];		$dian51=$_POST['dian51'];		$dian544=$_POST['dian544'];
	$dian12=$_POST['dian12'];	$dian52=$_POST['dian52'];		$dian545=$_POST['dian545'];
	$dian14=$_POST['dian14'];	$dian531=$_POST['dian531'];		$dian546=$_POST['dian546'];
	$dian24=$_POST['dian24'];	$dian532=$_POST['dian532'];		$dian547=$_POST['dian547'];
	$dian25=$_POST['dian25'];	$dian533=$_POST['dian533'];		$dian548=$_POST['dian548'];
	$dian26=$_POST['dian26'];	$dian534=$_POST['dian534'];		$dian549=$_POST['dian549'];
	$dian27=$_POST['dian27'];	$dian535=$_POST['dian535'];		$dian5410=$_POST['dian5410'];
	$dian28=$_POST['dian28'];	$dian536=$_POST['dian536'];		$dian5411=$_POST['dian5411'];
	$dian29=$_POST['dian29'];	$dian537=$_POST['dian537'];		$dian5412=$_POST['dian5412'];
	$dian30=$_POST['dian30'];	$dian538=$_POST['dian538'];		$dian5413=$_POST['dian5413'];
	$dian31=$_POST['dian31'];	$dian539=$_POST['dian539'];		$dian5414=$_POST['dian5414'];
	$dian32=$_POST['dian32'];	$dian5310=$_POST['dian5310'];	$dian5415=$_POST['dian5415'];
	$dian33=$_POST['dian33'];	$dian5311=$_POST['dian5311'];	$dian5416=$_POST['dian5416'];
	$dian34=$_POST['dian34'];	$dian5312=$_POST['dian5312'];	$dian5417=$_POST['dian5417'];
	$dian35=$_POST['dian35'];	$dian5313=$_POST['dian5313'];	$dian5418=$_POST['dian5418'];
	$dian36=$_POST['dian36'];	$dian5314=$_POST['dian5314'];	$dian5419=$_POST['dian5419'];
	$dian37=$_POST['dian37'];	$dian5315=$_POST['dian5315'];	$dian5420=$_POST['dian5420'];
	$dian38=$_POST['dian38'];	$dian5316=$_POST['dian5316'];	$dian55=$_POST['dian55'];
	$dian39=$_POST['dian39'];	$dian5317=$_POST['dian5317'];	$dian56=$_POST['dian56'];
	$dian40=$_POST['dian40'];	$dian5318=$_POST['dian5318'];	$dian571=$_POST['dian571'];
	$dian41=$_POST['dian41'];	$dian5319=$_POST['dian5319'];	$dian572=$_POST['dian572'];
	$dian42=$_POST['dian42'];	$dian5320=$_POST['dian5320'];	$dian573=$_POST['dian573'];
	$dian43=$_POST['dian43'];	$dian5321=$_POST['dian5321'];	$dian581=$_POST['dian581'];
	$dian44=$_POST['dian44'];	$dian5322=$_POST['dian5322'];	$dian582=$_POST['dian582'];
	$dian45=$_POST['dian45'];	$dian5323=$_POST['dian5323'];	$dian583=$_POST['dian583'];
	$dian46=$_POST['dian46'];	$dian5324=$_POST['dian5324'];	$dian59=$_POST['dian59'];
	$dian47=$_POST['dian47'];	$dian5325=$_POST['dian5325'];	$dian60=$_POST['dian60'];
	$dian48=$_POST['dian48'];	$dian5326=$_POST['dian5326'];	$dian61=$_POST['dian61'];
	$dian62=$_POST['dian62']; 	$dian63=$_POST['dian63']; 		$dian631=$_POST['dian631'];
	$dian411=$_POST['dian411']; $dian64=$_POST['dian64']; $dian65=$_POST['dian65'];
	$accion = $_POST['accion'];      $subaccion = $_POST['subaccion'];
	$dian63concat=$dian63."-".$dian631; 
    ?>
	
</head>

<body>
<div id="container">
<?php

$id=$_GET['actualizar'];


	
$id=trim($id);


//$select_amedian = "select * from amedian where dian5 = '$id'";
//$resultado=odbc_do($conex_o, $select_amedian);
$select_root = mysql_queryV("SELECT * from root_000119 WHERE dian5 = '$id'");


//SI HAY REGISTROS EN AMEDIAN, TRAER ESOS REGISTROS:
    while($resultado=mysql_fetch_array($select_root))
	//while(odbc_fetch_row($resultado))
    {
        //$dian4 = $resultado[4];
		$dian2 = $resultado[3];	$dian4 = $resultado[4];	$dian5 = $resultado[5];	$dian6 = $resultado[6];	$dian12 = $resultado[7];	$dian14 = $resultado[8];	$dian24 = $resultado[9];	
		$dian25 = $resultado[10];	$dian26 = $resultado[11];	$dian27 = $resultado[12];	$dian28 = $resultado[13];	$dian29 = $resultado[14];	$dian30 = $resultado[15];	
		$dian31 = $resultado[16];	$dian32 = $resultado[17];	$dian33 = $resultado[18];	$dian34 = $resultado[19];	$dian35 = $resultado[20];	$dian36 = $resultado[21];	
		$dian37 = $resultado[22];	$dian38 = $resultado[23];	$dian39 = $resultado[24];	$dian40 = $resultado[25];	$dian41 = $resultado[26];	$dian411 = $resultado[27];	
		$dian42 = $resultado[28];	$dian43 = $resultado[29];	$dian44 = $resultado[30];	$dian45 = $resultado[31];	$dian46 = $resultado[32];	$dian47 = $resultado[33];	
		$dian48 = $resultado[34];	$dian49 = $resultado[35];	$dian501 = $resultado[36];	$dian502 = $resultado[37];	$dian51 = $resultado[38];	$dian52 = $resultado[39];	
		$dian531 = $resultado[40];	$dian532 = $resultado[41];	$dian533 = $resultado[42];	$dian534 = $resultado[43];	$dian535 = $resultado[44];	$dian536 = $resultado[45];	
		$dian537 = $resultado[46];	$dian538 = $resultado[47];	$dian539 = $resultado[48];	$dian5310 = $resultado[49];	$dian5311 = $resultado[50];	$dian5312 = $resultado[51];	
		$dian5313 = $resultado[52];	$dian5314 = $resultado[53];	$dian5315 = $resultado[54];	$dian5316 = $resultado[55];	$dian5317 = $resultado[56];	$dian5318 = $resultado[57];	
		$dian5319 = $resultado[58];	$dian5320 = $resultado[59];	$dian5321 = $resultado[60];	$dian5322 = $resultado[61];	$dian5323 = $resultado[62];	$dian5324 = $resultado[63];	
		$dian5325 = $resultado[64];	$dian5326 = $resultado[65];	$dian541 = $resultado[66];	$dian542 = $resultado[67];	$dian543 = $resultado[68];	$dian544 = $resultado[69];	
		$dian545 = $resultado[70];	$dian546 = $resultado[71];	$dian547 = $resultado[72];	$dian548 = $resultado[73];	$dian549 = $resultado[74];	$dian5410 = $resultado[75];	
		$dian5411 = $resultado[76];	$dian5412 = $resultado[77];	$dian5413 = $resultado[78];	$dian5414 = $resultado[79];	$dian5415 = $resultado[80];	$dian5416 = $resultado[81];	
		$dian5417 = $resultado[82];	$dian5418 = $resultado[83];	$dian5419 = $resultado[84];	$dian5420 = $resultado[85];	$dian55 = $resultado[86];	$dian56 = $resultado[87];	
		$dian571 = $resultado[88];	$dian572 = $resultado[89];	$dian573 = $resultado[90];	$dian581 = $resultado[91];	$dian582 = $resultado[92];	$dian583 = $resultado[93];	
		$dian59 = $resultado[94];	$dian60 = $resultado[95];	$dian61 = $resultado[96];	$dian62 = $resultado[97];	$dian63 = $resultado[98];	$dian64 = $resultado[99];	
		$dian65 = $resultado[100];
		/*$dian2 = odbc_result($resultado,1);		$dian542 = odbc_result($resultado,64);	$dian501 = odbc_result($resultado,33);
		$dian4 = odbc_result($resultado,2);
		$dian4=trim($dian4);
		$dian543 = odbc_result($resultado,65);	$dian502 = odbc_result($resultado,34);
		$dian5 = odbc_result($resultado,3);		$dian544 = odbc_result($resultado,66);	$dian51 = odbc_result($resultado,35);
		$dian6 = odbc_result($resultado,4);		$dian545 = odbc_result($resultado,67);	$dian52 = odbc_result($resultado,36);
		$dian12 = odbc_result($resultado,5);	$dian546 = odbc_result($resultado,68);	$dian531 = odbc_result($resultado,37);
		$dian14 = odbc_result($resultado,6);	$dian547 = odbc_result($resultado,69);	$dian532 = odbc_result($resultado,38);
		$dian24 = odbc_result($resultado,7);	$dian548 = odbc_result($resultado,70);	$dian533 = odbc_result($resultado,39);
		$dian25 = odbc_result($resultado,8);	$dian549 = odbc_result($resultado,71);	$dian534 = odbc_result($resultado,40);
		$dian26 = odbc_result($resultado,9);	$dian5410 = odbc_result($resultado,72);	$dian535 = odbc_result($resultado,41);
		$dian27 = odbc_result($resultado,10);	$dian5411 = odbc_result($resultado,73);	$dian536 = odbc_result($resultado,42);
		$dian28 = odbc_result($resultado,11);	$dian5412 = odbc_result($resultado,74);	$dian537 = odbc_result($resultado,43);
		$dian29 = odbc_result($resultado,12);	$dian5413 = odbc_result($resultado,75);	$dian538 = odbc_result($resultado,44);
		$dian30 = odbc_result($resultado,13);	$dian5414 = odbc_result($resultado,76);	$dian539 = odbc_result($resultado,45);
		$dian31 = odbc_result($resultado,14);	$dian5415 = odbc_result($resultado,77);	$dian5310 = odbc_result($resultado,46);
		$dian32 = odbc_result($resultado,15);	$dian5416 = odbc_result($resultado,78);	$dian5311 = odbc_result($resultado,47);
		$dian33 = odbc_result($resultado,16);	$dian5417 = odbc_result($resultado,79);	$dian5312 = odbc_result($resultado,48);
		$dian34 = odbc_result($resultado,17);	$dian5418 = odbc_result($resultado,80);	$dian5313 = odbc_result($resultado,49);
		$dian35 = odbc_result($resultado,18);	$dian5419 = odbc_result($resultado,81);	$dian5314 = odbc_result($resultado,50);
		$dian36 = odbc_result($resultado,19);	$dian5420 = odbc_result($resultado,82);	$dian5315 = odbc_result($resultado,51);
		$dian37 = odbc_result($resultado,20);	$dian55 = odbc_result($resultado,83);	$dian5316 = odbc_result($resultado,52);
		$dian38 = odbc_result($resultado,21);	$dian56 = odbc_result($resultado,84);	$dian5317 = odbc_result($resultado,53);
		$dian39 = odbc_result($resultado,22);	$dian571 = odbc_result($resultado,85);	$dian5318 = odbc_result($resultado,54);
		$dian40 = odbc_result($resultado,23);	$dian572 = odbc_result($resultado,86);	$dian5319 = odbc_result($resultado,55);
		$dian41 = odbc_result($resultado,24);	$dian573 = odbc_result($resultado,87);	$dian5320 = odbc_result($resultado,56);
		$dian42 = odbc_result($resultado,25);	$dian581 = odbc_result($resultado,88);	$dian5321 = odbc_result($resultado,57);
		$dian43 = odbc_result($resultado,26);	$dian582 = odbc_result($resultado,89);	$dian5322 = odbc_result($resultado,58);
		$dian44 = odbc_result($resultado,27);	$dian583 = odbc_result($resultado,90);	$dian5323 = odbc_result($resultado,59);
		$dian45 = odbc_result($resultado,28);	$dian59 = odbc_result($resultado,91);	$dian5324 = odbc_result($resultado,60);
		$dian46 = odbc_result($resultado,29);	$dian60 = odbc_result($resultado,92);	$dian5325 = odbc_result($resultado,61);
		$dian47 = odbc_result($resultado,30);	$dian61 = odbc_result($resultado,93);	$dian5326 = odbc_result($resultado,62);
		$dian48 = odbc_result($resultado,31);	$dian62 = odbc_result($resultado,94);	$dian541 = odbc_result($resultado,63);
		$dian49 = odbc_result($resultado,32);	$dian63 = odbc_result($resultado,95); 	*/
		

    	
////MOSTRAR EN LA CAJA DE TEXTO DIVIDO
    list($nombre, $cargo) = explode('-', $dian63);
}
	
		

$accion = isset($_POST['accion']) ? $_POST['accion'] : "";
if($accion == 'guardar')
{
$update_amedian = "update amedian set dian2='$dian2',dian4='$dian4',dian6='$dian6',dian12='$dian12',dian14='$dian14',dian24='$dian24',dian25='$dian25',dian26='$dian26',dian27='$dian27',dian28='$dian28',dian29='$dian29',dian30='$dian30',dian31='$dian31',dian32='$dian32',dian33='$dian33',dian34='$dian34',dian35='$dian35',dian36='$dian36',dian37='$dian37',dian38='$dian38',dian39='$dian39',dian40='$dian40',dian41='$dian41',dian411='$dian411',dian42='$dian42',dian43='$dian43',dian44='$dian44',dian45='$dian45',dian46='$dian46',dian47='$dian47',dian48='$dian48',dian49='$dian49',dian501='$dian501',dian502='$dian502',dian51='$dian51',dian52='$dian52',dian531='$dian531',dian532='$dian532',dian533='$dian533',dian534='$dian534',dian535='$dian535',dian536='$dian536',dian537='$dian537',dian538='$dian538',dian539='$dian539',dian5310='$dian5310',dian5311='$dian5311',dian5312='$dian5312',dian5313='$dian5313',dian5314='$dian5314',dian5315='$dian5315',dian5316='$dian5316',dian5317='$dian5317',dian5318='$dian5318',dian5319='$dian5319',dian5320='$dian5320',dian5321='$dian5321',dian5322='$dian5322',dian5323='$dian5323',dian5324='$dian5324',dian5325='$dian5325',dian5326='$dian5326',dian541='$dian541',dian542='$dian542',dian543='$dian543',dian544='$dian544',dian545='$dian545',dian546='$dian546',dian547='$dian547',dian548='$dian548',dian549='$dian549',dian5410='$dian5410',dian5411='$dian5411',dian5412='$dian5412',dian5413='$dian5413',dian5414='$dian5414',dian5415='$dian5415',dian5416='$dian5416',dian5417='$dian5417',dian5418='$dian5418',dian5419='$dian5419',dian5420='$dian5420',dian55='$dian55',dian56='$dian56',dian571='$dian571',dian572='$dian572',dian573='$dian573',dian581='$dian581',dian582='$dian582',dian583='$dian583',dian59='$dian59',dian60='$dian60',dian61='$dian61',dian62='$dian62',dian63='$dian63concat',dian64='$dian64',dian65='$dian65' where dian5='$dian_5'";

odbc_do($conex_o, $update_amedian);

///////////////////////UNIX///////////////////////////////////
mysql_queryV("update root_000119 set dian2='$dian2',dian4='$dian4',dian6='$dian6',dian12='$dian12',dian14='$dian14',dian24='$dian24',dian25='$dian25',dian26='$dian26',dian27='$dian27',dian28='$dian28',dian29='$dian29',dian30='$dian30',dian31='$dian31',dian32='$dian32',dian33='$dian33',dian34='$dian34',dian35='$dian35',dian36='$dian36',dian37='$dian37',dian38='$dian38',dian39='$dian39',dian40='$dian40',dian41='$dian41',dian411='$dian411',dian42='$dian42',dian43='$dian43',dian44='$dian44',dian45='$dian45',dian46='$dian46',dian47='$dian47',dian48='$dian48',dian49='$dian49',dian501='$dian501',dian502='$dian502',dian51='$dian51',dian52='$dian52',dian531='$dian531',dian532='$dian532',dian533='$dian533',dian534='$dian534',dian535='$dian535',dian536='$dian536',dian537='$dian537',dian538='$dian538',dian539='$dian539',dian5310='$dian5310',dian5311='$dian5311',dian5312='$dian5312',dian5313='$dian5313',dian5314='$dian5314',dian5315='$dian5315',dian5316='$dian5316',dian5317='$dian5317',dian5318='$dian5318',dian5319='$dian5319',dian5320='$dian5320',dian5321='$dian5321',dian5322='$dian5322',dian5323='$dian5323',dian5324='$dian5324',dian5325='$dian5325',dian5326='$dian5326',dian541='$dian541',dian542='$dian542',dian543='$dian543',dian544='$dian544',dian545='$dian545',dian546='$dian546',dian547='$dian547',dian548='$dian548',dian549='$dian549',dian5410='$dian5410',dian5411='$dian5411',dian5412='$dian5412',dian5413='$dian5413',dian5414='$dian5414',dian5415='$dian5415',dian5416='$dian5416',dian5417='$dian5417',dian5418='$dian5418',dian5419='$dian5419',dian5420='$dian5420',dian55='$dian55',dian56='$dian56',dian571='$dian571',dian572='$dian572',dian573='$dian573',dian581='$dian581',dian582='$dian582',dian583='$dian583',dian59='$dian59',dian60='$dian60',dian61='$dian61',dian62='$dian62',dian63='$dian63concat',dian64='$dian64',dian65='$dian65' where dian5='$dian_5'");
?>
    <?php  
    	$mensaje = "DATOS ACTUALIZADOS CORRECTAMENTE"; 
		echo "<script>"; 
		echo "if(confirm('$mensaje'));";   
		echo "window.location = 'procesosdian.php';"; 
		echo "</script>";   
                  
              
     ?>  
	<!--<div style="margin-top: 10px;  text-align: center">
        <form method="post" action="procesosdian.php">
            <label style="color: #080808"><strong>DATOS ACTUALIZADOS CORRECTAMENTE</strong> </label>
            <br><br>
            <input type="submit" class="text-success" value="ACEPTAR"/>
        </form>
    </div>-->
    <?php
   }

      
   
    ?>
	

    <!---------------------------------------------------------------------------->

    <div id="divInforme" class="panel-body" style="padding-top: none">
      <form id="frmInforme" method="post" action="dianeditar.php">
        <script>foco()</script>
            <div style="width: 1200px" class="table-bordered">
                    <table width="1200" height="113" style="border: groove; width: 1200px">
                        <tr>
                            <td width="20%" align="center" style="border: groove; width: 20%">
                                <input type="image" id="btnVer" src="http://mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="80">
                          </td>
                            <td width="48%" align="center" style="border: groove; width: 55%">
                                <p><b>EDITAR RUT</b><strong></strong></p>
                          </td>
                            <td width="32%" style="border: groove; width: 35%">
                                <table>
                                    <tr>
                                        <td>Codigo: </td>
                                    </tr>
                                    <tr>
                                        <td>Version: 1.0 </td>
                                    </tr>
                                    <tr>
                                        <td>Pagina: 1</td>
                                    </tr>
                                    <tr>
                                        <td>Fecha de Emision: <?php echo $fechaEmision; ?></td>
                                    </tr>
                                </table>
                          </td>
                        </tr>
                  </table>

                    <table width="1200" style="border: groove; width: 1200px; border-top: none">
                      <tr style="background-color: silver">
                        <td style="border: groove" colspan="3" align="center"><b>INFORMACION INICIAL</b></td>
                      </tr>
                      <tr>
                        <td width="29%" style="border: groove; width: 50%"><b>2. Concepto
                          <label>
                              <input name="dian2" type="text" id="dian2" value="<?php echo $dian2 ?>" size="10" maxlength="2">
                            Actualizacion de oficio </label>
                        </b></td>
                        <td colspan="2" style="border: groove; width: 200%"><b>4. N&uacute;mero de formulario
                          <input name="dian4" type="text" id="dian4"  value="<?php echo $dian4 ?>" size="30" maxlength="15">
						  
                        </b></td>
                      </tr>
                      <tr>
                        <td style="border: groove"><p align="left"><b>5. N&uacute;mero de identificacion tributaria (NIT): </b> <strong>&ensp; &ensp; &ensp;6. DV</strong></p>
                            <label><b><b><b><b>
                            <input name="dian5" type="text" id="dian5" style="background-color:#EEEEEE" value="<?php echo $dian5 ?>" size="55" maxlength="11" readonly>
							<input name="dian_5" type="hidden" id="dian_5" size="30"  value="<?php echo $dian5 ?>">
                            </b></b></b>- </b>
                            <input name="dian6" type="text" id="dian6" value="<?php echo $dian6 ?>" size="3" maxlength="1" max="9" min="0">
                            <br>
                          </label></td>
                        <td width="25%" valign="top" style="border: groove"><p><b>12. Direccion seccional:</b></p>
                            <input name="dian12" type="text" id="dian12" value="<?php echo $dian12 ?>" size="4" maxlength="2"></td>
                        <td width="50%" valign="top" style="border: groove"><p><b>14. Buz&oacute;n electr&oacute;nico:</b></p>
                            <p><b><b><b>
                              <input name="dian14" type="text" id="dian14" value="<?php echo $dian14 ?>" size="25" maxlength="20">
                        </b></b></b></p></td>
                      </tr>
                    </table>
                    <table width="1200" style="border: groove; width: 1200px; border-top: none">
                      <tr style="background-color: silver">
                        <td style="border: groove" colspan="4" align="center"><strong>IDENTIFICACION</strong></td>
                      </tr>
                      <tr>
                        <td width="300" style="border: groove"><p><b>24. Tipo de contribuyente:
                      </b></p>
                          <p><b>    
                            <input name="dian24" type="text" id="dian24" value="<?php echo $dian24 ?>" size="4" maxlength="1">
                          </b></p></td>
                        <td width="248" valign="top" style="border: groove"><p><b>25. Tipo de documento:
                      </b></p>
                          <p><b>    
                            <input name="dian25" type="text" id="dian25" value="<?php echo $dian25 ?>" size="4" maxlength="2">
                          </b></p></td>
                        <td width="410" valign="top" style="border: groove"><p><b>26. Numero de Identificacion:
                      </b></p>
                          <p><b>    
                            <input name="dian26" type="text" id="dian26" value="<?php echo $dian26 ?>" size="30" maxlength="14">
                        </b></p></td>
                        <td width="568" valign="top" style="border: groove"><p><b>27. Fecha Expedicion:</b></p>
                          <p><b>
                          <input name="dian27" type="text" id="datepicker1" value="<?php echo $dian27 ?>" size="30" >
                        </b></p></td>
                      </tr>
                      <tr>
                        <td colspan="2" valign="top" style="border: groove"><p><b>Lugar de expedicion</b> &ensp; &ensp; &ensp; <strong>28</strong>. <strong>Pais</strong></p>
                          <p><b>
                            <input name="dian28" type="text" id="dian28" value="<?php echo $dian28 ?>" size="8" maxlength="3">
                          </b></p></td>
                        <td valign="top" style="border: groove"><p><strong>29. Departamento:</strong></p>
                          <p><b>
                              <input name="dian29" type="text" id="dian29" value="<?php echo $dian29 ?>" size="8" maxlength="5">
                          </b></p></td>
                        <td style="border: groove"><p><strong>30. Ciudad/Municipio:</strong></p>
                          <p><b>
                              <input name="dian30" type="text" id="dian30" value="<?php echo $dian30 ?>" size="8" maxlength="3">
                          </b></p>
                        </td>
                      </tr>
                      <tr>
                        <td style="border: groove; width: 25%"><p><b>31. Primer apellido:
                      </b></p>
                          <p><b>    
                            <input name="dian31" type="text" id="dian31" value="<?php echo $dian31 ?>" size="30" maxlength="15">
                          </b></p></td>
                        <td style="border: groove; width: 25%"><p><b>32. Segundo apellido:
                      </b></p>
                          <p><b>    
                            <input name="dian32" type="text" id="dian32" value="<?php echo $dian32 ?>" size="30" maxlength="15">
                          </b></p></td>
                        <td style="border: groove; width: 50%"><p><b>33. Primer Nombre:</b></p>
                          <p><b>
                          <input name="dian33" type="text" id="dian33" value="<?php echo $dian33 ?>" size="30" maxlength="15">
                          </b></p></td>
                        <td style="border: groove; width: 50%"><p><b>34. Otros Nombres:</b></p>
                          <p><b>
                          <input name="dian34" type="text" id="dian34" value="<?php echo $dian34 ?>" size="30" maxlength="15">
                          </b></p></td>
                      </tr>
                      <tr>
                        <td colspan="4" style="border: groove; width: 15%"><p><b>35. Razon Social:</b></p>
                          <p><b>
                            <input name="dian35" type="text" id="dian35" value="<?php echo $dian35 ?>" size="90" maxlength="35">
                          </b></p></td>
                      </tr>
                      <tr>
                        <td style="border: groove" colspan="3"><p><b>36. Nombre Comercial:</b></p>
                        <p><b>
                          <input name="dian36" type="text" id="dian36" value="<?php echo $dian36 ?>" size="40" maxlength="35">
                        </b></p></td>
                        <td style="border: groove"><p><b>37. Sigia:</b></p>
                        <p><b>
                          <input name="dian37" type="text" id="dian37" value="<?php echo $dian37 ?>" size="30" maxlength="35">
                        </b></p></td>
                      </tr>
                    </table>
                    <table width="1200" style="border: groove; width: 1200px; border-top: none">
                        <tr STYLE="background-color: silver">
                            <td style="border: groove" colspan="6" align="center">
                                <b>UBICACION</b></td>
                        </tr>
                        <tr>
                          <td colspan="2" valign="top" style="border: groove"><p><strong>3</strong><strong>8</strong>. <strong>Pais</strong></p>
                            <p><b>
                              <input name="dian38" type="text" id="dian38" value="<?php echo $dian38 ?>" size="8" maxlength="10">
                            </b></p></td>
                          <td width="600" valign="top" style="border: groove"><p><strong>39. Departamento:</strong></p>
                            <p><b>
                                <input name="dian39" type="text" id="dian39" value="<?php echo $dian39 ?>" size="8" maxlength="10">
                            </b></p></td>
                          <td width="600" style="border: groove"><p><strong>40. Ciudad/Municipio:</strong></p>
                            <p><b>
                              <input name="dian40" type="text" id="dian40" value="<?php echo $dian40 ?>" size="8" maxlength="10">
                              </b></p></td>
                        </tr>
                        <tr>
                            <td colspan="3" style="border: groove">
                                <p><b>41. Direccion principal:</b></p>
                                <p><b></b> <b>
                                  <input name="dian41" type="text" id="dian41" value="<?php echo $dian41 ?>" size="90" maxlength="30">
                                </b></p></td>
                            <td style="border: groove"><p><b>41.1. Barrio:</b></p>
                              <p><b></b> <b>
                                <input name="dian411" type="text" id="dian411" value="<?php echo $dian411 ?>" size="30" maxlength="30">
                              </b></p></td>
                        </tr>
                        <tr>
                            <td width="350" style="border: groove"><p><strong>42. Correo electronico:</strong></p>
                            <p><b>
                              <input name="dian42" type="text" id="dian42" value="<?php echo $dian42 ?>" size="30" maxlength="30">
                          </b></p></td>
                            <td width="347" style="border: groove"><p><strong>43. Codigo postal:</strong></p>
                            <p><b>
                              <input name="dian43" type="text" id="dian43" value="<?php echo $dian43 ?>" size="30" maxlength="10">
                          </b></p></td>
                            <td style="border: groove"><p><strong>44. Telefono 1:</strong></p>
                            <p><b>
                              <input name="dian44" type="text" id="dian44" value="<?php echo $dian44 ?>" size="30" maxlength="16">
                            </b></p></td>
                            <td style="border: groove"><p><strong>45. Telefono 2:</strong></p>
                            <p><b>
                              <input name="dian45" type="text" id="dian45" value="<?php echo $dian45 ?>" size="30" maxlength="16">
                            </b></p></td>
                        </tr>
                  </table>

                    <table width="1200" style="border: groove; width: 1200px; border-top: none">
                      <tr style="background-color: silver">
                        <td style="border: groove" colspan="6;" align="center"><strong>CLASIFICACION</strong></td>
                      </tr>
                      <tr>
                        <td  colspan="3" bgcolor="#CCCCCC" style="border: groove"><div align="center"><strong>Actividad Economica </strong></div></td>
                        <td width="20%" bgcolor="#CCCCCC" style="border: groove"><div align="center"><strong>Ocupacion</strong></div></td>
                        <td width="20%" style="border: groove">&nbsp;</td>
                      </tr>
                      <tr>
                        <td width="20%" valign="top" style="border: groove; width: 50%"><p align="center"><strong>Actividad Principal</strong></p>
                            <table width="368" border="1" align="left">
                              <tr>
                                <td width="93"><div align="center"><strong>46. Codigo: </strong></div></td>
                                <td width="255"><div align="left"><strong>47. Fecha inicio actividad </strong></div></td>
                              </tr>
                              <tr>
                                <td><div align="center"><b>
                                    <input name="dian46" type="text" id="dian46" value="<?php echo $dian46 ?>" size="10" maxlength="4" max="99" min="00">
                                </b></div></td>
                                <td><div align="left"><b>
                                    <input name="dian47" type="text" id="datepicker2" value="<?php echo $dian47 ?>" size="15" max="99" min="00">
                                </b></div></td>
                              </tr>
                          </table></td>
                        <td width="25%" valign="top" style="border: groove; width: 50%"><p align="center"><strong>Actividad Secundaria</strong></p>
                            <table width="408" border="1" align="left">
                              <tr>
                                <td width="79"><div align="center"><strong>48. Codigo:</strong></div></td>
                                <td width="180"><div align="left"><strong>49. Fecha inicio actividad</strong></div></td>
                              </tr>
                              <tr>
                                <td><div align="center"><b>
                                    <input name="dian48" type="text" id="dian48" value="<?php echo $dian48 ?>" size="10" maxlength="4" max="99" min="00">
                                </b></div></td>
                                <td><div align="left"><b>
                                    <input name="dian49" type="text" id="datepicker3" value="<?php echo $dian49 ?>" size="15" max="99" min="00">
                                </b></div></td>
                              </tr>
                          </table></td>
                        <td width="15%" valign="top" style="border: groove; width: 50%"><p align="left"><strong>Otras Actividades: </strong><strong>50. Codigo</strong></p>
                          <div align="left">
                            <table width="159" border="1" align="center">
                                <tr>
                                  <td width="79"><div align="left"><strong>1</strong></div></td>
                                  <td width="64"><div align="left"><strong>2</strong></div></td>
                                </tr>
                                <tr>
                                  <td><div align="center"><b>
                                      <input name="dian501" type="text" id="dian501" value="<?php echo $dian501 ?>" size="5" maxlength="4" max="99" min="00">
                                  </b></div></td>
                                  <td><div align="left"><b>
                                      <input name="dian502" type="text" id="dian502" value="<?php echo $dian502 ?>" size="5" maxlength="4" max="99" min="00">
                                  </b></div></td>
                                </tr>
                            </table>
                          </div></td>
                        <td valign="top" style="border: groove; width: 50%"><p><strong>51. Codigo</strong>.</p>
                          <p><b><input name="dian51" type="text" id="dian51" value="<?php echo $dian51 ?>" size="10" maxlength="4" max="99" min="00">
                          </b></p></td>
                        <td valign="top" style="border: groove; width: 50%"><p><strong>52. Numero </strong><strong>establecimiento. </strong></p>
                            <p><b>
                              <input name="dian52" type="text" id="dian52" value="<?php echo $dian52 ?>" size="10" maxlength="3" max="99" min="00">
                          </b></p></td>
                      </tr>
              </table>
                    <table width="1200" style="border: groove; width: 1200px; border-top: none">
                        <tr STYLE="background-color: silver">
                            <td align="center" style="border: groove"><strong>RESPONSABILIDADES, CALIDADES Y ATRIBUTOS</strong></td>
                        </tr>
                        <tr>
                            <td align="left" valign="middle" style="border: groove"><div align="left"><strong>53. Codigo</strong>
                            </div>
                              <table width="513" border="1" align="left">
                              <tr bgcolor="#CCCCCC">
                                <td><div align="center"><span class="Estilo4">1</span></div></td>
                                <td><div align="center"><span class="Estilo4">2</span></div></td>
                                <td><div align="center"><span class="Estilo4">3</span></div></td>
                                <td><div align="center"><span class="Estilo4">4</span></div></td>
                                <td><div align="center"><span class="Estilo4">5</span></div></td>
                                <td><div align="center"><span class="Estilo4">6</span></div></td>
                                <td><div align="center"><span class="Estilo4">7</span></div></td>
                                <td><div align="center"><span class="Estilo4">8</span></div></td>
                                <td><div align="center"><span class="Estilo4">9</span></div></td>
                                <td><div align="center"><span class="Estilo4">10</span></div></td>
                                <td><div align="center"><span class="Estilo4">11</span></div></td>
                                <td><div align="center"><span class="Estilo4">12</span></div></td>
                                <td><div align="center"><span class="Estilo4">13</span></div></td>
                                <td><div align="center" class="Estilo4">
                                  <div align="center">14</div>
                                </div></td>
                                <td><div align="center"><span class="Estilo4">15</span></div></td>
                                <td><div align="center"><span class="Estilo4">16</span></div></td>
                                <td><div align="center"><span class="Estilo4">17</span></div></td>
                                <td><div align="center"><span class="Estilo4">18</span></div></td>
                                <td><div align="center"><span class="Estilo4">19</span></div></td>
                                <td><div align="center"><span class="Estilo4">20</span></div></td>
                                <td><div align="center"><span class="Estilo4">21</span></div></td>
                                <td><div align="center"><span class="Estilo4">22</span></div></td>
                                <td><div align="center"><span class="Estilo4">23</span></div></td>
                                <td><div align="center"><span class="Estilo4">24</span></div></td>
                                <td><div align="center"><span class="Estilo4">25</span></div></td>
                                <td><div align="center"><span class="Estilo4">26</span></div></td>
                              </tr>
                              <tr>
                                <td><b>
                                  <input name="dian531" type="text" id="dian531" value="<?php echo $dian531 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian532" type="text" id="dian532" value="<?php echo $dian532 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian533" type="text" id="dian533" value="<?php echo $dian533 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian534" type="text" id="dian534" value="<?php echo $dian534 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian535" type="text" id="dian535" value="<?php echo $dian535 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian536" type="text" id="dian536" value="<?php echo $dian536 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian537" type="text" id="dian537" value="<?php echo $dian537 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian538" type="text" id="dian538" value="<?php echo $dian538 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian539" type="text" id="dian539" value="<?php echo $dian539 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5310" type="text" id="dian5310" value="<?php echo $dian5310 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5311" type="text" id="dian5311" value="<?php echo $dian5311 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5312" type="text" id="dian5312" value="<?php echo $dian5312 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5313" type="text" id="dian5313" value="<?php echo $dian5313 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5314" type="text" id="dian5314" value="<?php echo $dian5314 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5315" type="text" id="dian5315" value="<?php echo $dian5315 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5316" type="text" id="dian5316" value="<?php echo $dian5316 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5317" type="text" id="dian5317" value="<?php echo $dian5317 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5318" type="text" id="dian5318" value="<?php echo $dian5318 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5319" type="text" id="dian5319" value="<?php echo $dian5319 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5320" type="text" id="dian5320" value="<?php echo $dian5320 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5321" type="text" id="dian5321" value="<?php echo $dian5321 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5322" type="text" id="dian5322" value="<?php echo $dian5322 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5323" type="text" id="dian5323" value="<?php echo $dian5323 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5324" type="text" id="dian5324" value="<?php echo $dian5324 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5325" type="text" id="dian5325" value="<?php echo $dian5325 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5326" type="text" id="dian5326" value="<?php echo $dian5326 ?>" size="1" max="99" min="00" maxlength="2">
                                </b></td>
                              </tr>
                          </table></td>
                        </tr>
              </table>

                    <table width="1200" style="border: groove; width: 1200px; border-top: none">
                      <tr style="background-color: silver">
                        <td width="54%" align="center" style="border: groove"><strong>Obligados aduaneros </strong></td>
                        <td width="46%" colspan="2" align="center" style="border: groove"><strong>Exportadores</strong></td>
                      </tr>
                      <tr style="background-color: silver">
                        <td align="center" bgcolor="#FFFFFF" style="border: groove"><div align="left"><strong>54. Codigo:</strong>
                            <table width="225" border="1">
                            <tr bgcolor="#CCCCCC">
                              <td width="17"><div align="center"><strong>1</strong></div></td>
                              <td width="14"><div align="center"><strong>2</strong></div></td>
                              <td width="17"><div align="center"><strong>3</strong></div></td>
                              <td width="17"><div align="center"><strong>4</strong></div></td>
                              <td width="15"><div align="center"><strong>5</strong></div></td>
                              <td width="16"><div align="center"><strong>6</strong></div></td>
                              <td width="15"><div align="center"><strong>7</strong></div></td>
                              <td width="15"><div align="center"><strong>8</strong></div></td>
                              <td width="16"><div align="center"><strong>9</strong></div></td>
                              <td width="19"><div align="center"><strong>10</strong></div></td>
                            </tr>
                            <tr>
                              <td><b>
                                <input name="dian541" type="text" id="dian541" value="<?php echo $dian541 ?>" size="4" max="99" min="00" maxlength="3">
                              </b></td>
                              <td><b>
                                <input name="dian542" type="text" id="dian542" value="<?php echo $dian542 ?>" size="4" max="99" min="00" maxlength="3">
                              </b></td>
                              <td><b>
                                <input name="dian543" type="text" id="dian543" value="<?php echo $dian543 ?>" size="4" max="99" min="00" maxlength="3">
                              </b></td>
                              <td><b>
                                <input name="dian544" type="text" id="dian544" value="<?php echo $dian544 ?>" size="4" max="99" min="00" maxlength="3">
                              </b></td>
                              <td><b>
                                <input name="dian545" type="text" id="dian545" value="<?php echo $dian545 ?>" size="4" max="99" min="00" maxlength="3">
                              </b></td>
                              <td><b>
                                <input name="dian546" type="text" id="dian546" value="<?php echo $dian546 ?>" size="4" max="99" min="00" maxlength="3">
                              </b></td>
                              <td><b>
                                <input name="dian547" type="text" id="dian547" value="<?php echo $dian547 ?>" size="4" max="99" min="00" maxlength="3">
                              </b></td>
                              <td><b>
                                <input name="dian548" type="text" id="dian548" value="<?php echo $dian548 ?>" size="4" max="99" min="00" maxlength="3">
                              </b></td>
                              <td><b>
                                <input name="dian549" type="text" id="dian549" value="<?php echo $dian549 ?>" size="4" max="99" min="00" maxlength="3">
                              </b></td>
                              <td><div align="center"><b>
                                <input name="dian5410" type="text" id="dian5410" value="<?php echo $dian5410 ?>" size="4" max="99" min="00" maxlength="3">
                              </b></div></td>
                            </tr>
                          </table>
                            <table width="225" border="1">
                              <tr bgcolor="#CCCCCC">
                                <td width="17"><div align="center"><strong>11</strong></div></td>
                                <td width="14"><div align="center"><strong>12</strong></div></td>
                                <td width="17"><div align="center"><strong>13</strong></div></td>
                                <td width="17"><div align="center"><strong>14</strong></div></td>
                                <td width="15"><div align="center"><strong>15</strong></div></td>
                                <td width="16"><div align="center"><strong>16</strong></div></td>
                                <td width="15"><div align="center"><strong>17</strong></div></td>
                                <td width="15"><div align="center"><strong>18</strong></div></td>
                                <td width="16"><div align="center"><strong>19</strong></div></td>
                                <td width="19"><div align="center"><strong>20</strong></div></td>
                              </tr>
                              <tr>
                                <td><b>
                                  <input name="dian5411" type="text" id="dian5411" value="<?php echo $dian5411 ?>" size="4" max="99" min="00" maxlength="3">
                                </b></td>
                                <td><b>
                                  <input name="dian5412" type="text" id="dian5412" value="<?php echo $dian5412 ?>" size="4" max="99" min="00" maxlength="3">
                                </b></td>
                                <td><b>
                                  <input name="dian5413" type="text" id="dian5413" value="<?php echo $dian5413 ?>" size="4" max="99" min="00" maxlength="3">
                                </b></td>
                                <td><b>
                                  <input name="dian5414" type="text" id="dian5414" value="<?php echo $dian5414 ?>" size="4" max="99" min="00" maxlength="3">
                                </b></td>
                                <td><b>
                                  <input name="dian5415" type="text" id="dian5415" value="<?php echo $dian5415 ?>" size="4" max="99" min="00" maxlength="3">
                                </b></td>
                                <td><b>
                                  <input name="dian5416" type="text" id="dian5416" value="<?php echo $dian5416 ?>" size="4" max="99" min="00" maxlength="3">
                                </b></td>
                                <td><b>
                                  <input name="dian5417" type="text" id="dian5417" value="<?php echo $dian5417 ?>" size="4" max="99" min="00" maxlength="3">
                                </b></td>
                                <td><b>
                                  <input name="dian5418" type="text" id="dian5418" value="<?php echo $dian5418 ?>" size="4" max="99" min="00" maxlength="3">
                                </b></td>
                                <td><b>
                                  <input name="dian5419" type="text" id="dian5419" value="<?php echo $dian5419 ?>" size="4" max="99" min="00" maxlength="3">
                                </b></td>
                                <td><div align="center"><b>
                                    <input name="dian5420" type="text" id="dian5420" value="<?php echo $dian5420 ?>" size="4" max="99" min="00" maxlength="3">
                                </b></div></td>
                              </tr>
                            </table>
                          <p>&nbsp;</p>
                        </div></td>
                        <td align="center" bgcolor="#FFFFFF" style="border: groove"><div align="left">
                          <table width="172" border="1" align="center">
                            <tr bgcolor="#CCCCCC">
                              <td width="76"><strong>55. Forma </strong></td>
                              <td width="80"><strong>56. Tipo </strong></td>
                            </tr>
                            <tr>
                              <td align="center"><b>
                                <input name="dian55" type="text" id="dian55" value="<?php echo $dian55 ?>" size="4" max="99" min="00" maxlength="1">
                              </b></td>
                              <td align="center"><b>
                                <input name="dian56" type="text" id="dian56" value="<?php echo $dian56 ?>" size="4" max="99" min="00" maxlength="3">
                              </b></td>
                            </tr>
                          </table>
                            </div></td>
                        <td align="center" bgcolor="#FFFFFF" style="border: groove"><table width="200" border="1">
                          <tr align="center">
                            <td><strong>Servicio</strong></td>
                            <td><strong>1</strong></td>
                            <td><strong>2</strong></td>
                            <td><strong>3</strong></td>
                          </tr>
                          <tr>
                            <td><strong>57. Modo </strong></td>
                            <td align="center"><b>
                              <input name="dian571" type="text" id="dian571" value="<?php echo $dian571 ?>" size="4" max="99" min="00" maxlength="1">
                            </b></td>
                            <td align="center"><b>
                              <input name="dian572" type="text" id="dian572" value="<?php echo $dian572 ?>" size="4" max="99" min="00" maxlength="1">
                            </b></td>
                            <td align="center"><b>
                              <input name="dian573" type="text" id="dian573" value="<?php echo $dian573 ?>" size="4" max="99" min="00" maxlength="1">
                            </b></td>
                          </tr>
                          <tr>
                            <td><strong>58. CPC</strong></td>
                            <td align="center" valign="middle"><b>
                              <input name="dian581" type="text" id="dian581" value="<?php echo $dian581 ?>" size="8" max="99" min="00" maxlength="2">
                            </b></td>
                            <td align="center" valign="middle"><b>
                              <input name="dian582" type="text" id="dian582" value="<?php echo $dian582 ?>" size="8" max="99" min="00" maxlength="2">
                            </b></td>
                            <td align="center" valign="middle"><p><b>
                                <input name="dian583" type="text" id="dian583" value="<?php echo $dian583 ?>" size="8" max="99" min="00" maxlength="2">
                            </b></p></td>
                          </tr>
                        </table></td>
                      </tr>
                    </table>
                    <table style="border: groove; width: 1200px; border-top: none">
                        <tr STYLE="background-color: silver">
                          <td style="border: groove" align="center"><p><strong>PARA USO EXCLUSIVO DE LA DIAN</strong></p>
                            <p align="center">
                              <label><strong>59. Anexos:
                                <select name="dian59" size="1" id="dian59">
								<?php if ($dian59 == 'si'){
										?>
											<option value="si" selected="selected"> SI </option>
										<?php
									}else{
										?>
										 <option value="no" selected="selected">NO</option>
										<?php
									}
								
								?>
                                <option value="si"> SI </option> 
								<option value="no"> NO </option> 
                              </select>
                              </strong></label>
                              <strong> 60. No. de folios
                                <input name="dian60" type="text" id="dian60" value="<?php echo $dian60 ?>" size="8" max="99" min="00" maxlength="3">
                                61.Fecha:</strong> <b>
  								<input name="dian61" type="text" id="datepicker4" value="<?php echo $dian61 ?>" size="20" max="99" min="00">
								</b></p></td>
                        </tr>
                        <tr STYLE="background-color: silver">
                            <td style="border: groove" align="center"><p><strong>62. Numero de matricula mercantil de la empresa:</strong> <b>
                              <input name="dian62" type="text" id="dian62" value="<?php echo $dian62 ?>" size="40">
                            </b></p>
                            <p><strong>63. Persona de contacto y cargo: </strong><b>
                              <input name="dian63" type="text" id="dian63" value="<?php echo $nombre ?>" size="40" placeholder="Persona" maxlength="30">
                            </b>-<b>
                            <input name="dian631" type="text" id="dian631" size="40" value="<?php echo $cargo ?>" placeholder="Cargo" maxlength="30">
							
                            </b></p>
                            <p><strong><strong>64. Regimen: </strong><strong>
                              <select name="dian64" size="1" id="dian64">
							  <?php if ($dian64 == '0'){
							  		?>
									<option value="0" selected="selected"> Simplificado </option>
									<?php
								}else{
									?>
									 <option value="2" selected="selected">Comun</option>
									<?php
									}
								
								?>
							  
                                <option value="0">Simplificado</option>
                                <option value="2">Comun</option>
                              </select>
                            </strong>- 65. Tipo de persona de contacto: </strong><strong>
                            <select name="dian65" size="1" id="dian65">
							<?php if ($dian65 == '1'){
							  		?>
									<option value="1" selected="selected"> Persona de Contacto </option>
									<?php
								} elseif ($dian == '2'){
									?>
									 <option value="2" selected="selected">Contacto de Despacho</option>
									<?php
								} elseif ($dian == '3'){
									?>
									 <option value="3" selected="selected">Contabilidad</option>
									<?php
								} else {
									?>
									 <option value="4" selected="selected">Ventas</option>
									<?php
								}
								
								?>
							
                              <option value="1">Persona de Contacto</option>
                              <option value="2">Contacto de Despacho</option>
                              <option value="3">Contabilidad</option>
                              <option value="4">Ventas</option>
                            </select>
                            </strong></p></td>
                        </tr>
                    </table>
                    <table style="border: groove; width: 1200px; border-top: none">
                        <tr STYLE="background-color: silver">
                            <td style="border: groove" align="center"><label>
							  <input type="hidden" id="accion" name="accion" value="guardar" />
                              <input name="guardar" type="submit" id="guardar" value="GUARDAR">
                             <a href="procesosdian.php" title="Retornar">VOLVER</a>
                            </label></td>
                        </tr>
                    </table>
        </div>

        <div style="margin-top: 20px"></div>
      </form>
    </div>
</div>
</body>
</html>