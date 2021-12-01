<!--El programa realiza la captura, consulta, edita la informacion correspondiente del root_000119 y de amedian. -->
<!--Publicacion: 2018-12-19, 
	Por: Didier Orozco Carmona. 
	-->
<!DOCTYPE html>
<html lang="esp" xmlns="http://www.w3.org/1999/html">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1"/> <!--ISO-8859-1 ->
Para que muestre e�es y tildes -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RUT</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="estilosevent.css" rel="stylesheet" type="text/css">
    <script src="//code.jquery.com/jquery-1.9.1.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
    <script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
    <link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" /><!--Estilo para el calendario-->
    <script src="calendarioevent.js" type="text/javascript"></script>
    <script src="JsProcesosevent.js" type="text/javascript"></script>
	<link href="//netdna.bootstrapcdn.com/bootstrap/3.1.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="//matrixtest.lasamericas.com.co/matrix/soporte/procesos/stylehelpDesk.css" rel="stylesheet">
    <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.1.0/js/bootstrap.min.js"></script>
	<script src="//mx.lasamericas.com.co/matrix/paf/procesos/calendariopaf.js" type="text/javascript"></script>
    <script>
        $(function() {
            $( "#datepicker1" ).datepicker();
            $( "#datepicker2" ).datepicker();
			$( "#datepicker3" ).datepicker();
			$( "#datepicker4" ).datepicker();
        });
    </script>
	
	<script>
		function mensaje (dian5) {
			var validacion = null;
			ancho = 300;    alto = 120;
            var winl = (screen.width - ancho) / 2;
            var wint = 250;
			settings2 = 'height=' + alto + ',width=' + ancho + ',top=' + wint + ',left=' + winl + ', scrollbars=yes, toolbar=no';
			validacion = window.open ("validarExiste.php?dian5="+dian5.value,"miwin",settings2);
			validacion.focus();
		}
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
		//include ("validarExiste.php");	
        $user_session = explode('-', $_SESSION['user']);
        $wuse = $user_session[1];
        include("conex.php");
        include("root/comun.php");
        mysql_select_db("matrix");

        $conex = obtenerConexionBD("matrix");
        $conex_o = odbc_connect('facturacion','','')  or die("No se realizo conexi�n con la BD de Facturaci�n");
    }
	$dian2=$_POST['dian2'];		$dian49=$_POST['dian49'];		$dian541=$_POST['dian541'];
	$dian4=$_POST['dian4'];		$dian501=$_POST['dian501'];		$dian542=$_POST['dian542'];
	$dian5=$_POST['dian5'];		$dian502=$_POST['dian502'];		$dian543=$_POST['dian543'];
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
	$dian41=$_POST['dian41'];
	$dian411=$_POST['dian411'];	$dian5319=$_POST['dian5319'];	$dian572=$_POST['dian572'];
	$dian42=$_POST['dian42'];	$dian5320=$_POST['dian5320'];	$dian573=$_POST['dian573'];
	$dian43=$_POST['dian43'];	$dian5321=$_POST['dian5321'];	$dian581=$_POST['dian581'];
	$dian44=$_POST['dian44'];	$dian5322=$_POST['dian5322'];	$dian582=$_POST['dian582'];
	$dian45=$_POST['dian45'];	$dian5323=$_POST['dian5323'];	$dian583=$_POST['dian583'];
	$dian46=$_POST['dian46'];	$dian5324=$_POST['dian5324'];	$dian59=$_POST['dian59'];
	$dian47=$_POST['dian47'];	$dian5325=$_POST['dian5325'];	$dian60=$_POST['dian60'];
	$dian48=$_POST['dian48'];	$dian5326=$_POST['dian5326'];	$dian61=$_POST['dian61'];
	$dian62=$_POST['dian62']; $dian63=$_POST['dian63']; $dian631=$_POST['dian631'];
	$dian64=$_POST['dian64']; $dian65=$_POST['dian65'];
	$dian63concat=$dian63."-".$dian631; //concatenat 2 variables para almacenar
	$accion = $_POST['accion'];      $subaccion = $_POST['subaccion']; 
	//capturar fecha de hoy
	$Fecha_data = date('Y-m-d');
    $Hora_data = date('H:i:s');
    
	
	
	?>
	
</head>

<body>

<?php
$accion = isset($_POST['accion']) ? $_POST['accion'] : "";
if($accion == 'guardar')
{
$existe_root = mysql_queryV("select * from root_000119 where dian5 = '$dian5'");
$resultado = mysql_fetch_array($existe_root);

//$resultado=odbc_do($conex_o, $existe_amedian);
if ($resultado > 0){
	?>
    <div style="text-align: center" class="row">
        <form method="post" action="procesosdian.php">
            <label style="color: #080808"><strong>EL DATO YA EXISTE</strong> </label>
            <strong>POR FAVOR DIGITAR EL NIT EN LA CONSULTA O CORREGIRLO </strong>
            <br>
            <br>
            <input type="submit" class="text-success" value="CONSULTA"/>
        </form>
    </div>
    <?php
	
}else{

///// QUERY INSERTAR EN UNIX/////
 $insert_amedian = "INSERT INTO amedian 	(dian2,dian4,dian5,dian6,dian12,dian14,dian24,dian25,dian26,dian27,dian28,dian29,dian30,dian31,dian32,dian33,dian34,dian35,dian36,dian37,dian38,dian39,dian40,dian41,dian411,dian42,dian43,dian44,dian45,dian46,dian47,dian48,dian49,dian501,dian502,dian51,dian52,dian531,dian532,dian533,dian534,dian535,dian536,dian537,dian538,dian539,dian5310,dian5311,dian5312,dian5313,dian5314,dian5315,dian5316,dian5317,dian5318,dian5319,dian5320,dian5321,dian5322,dian5323,dian5324,dian5325,dian5326,dian541,dian542,dian543,dian544,dian545,dian546,dian547,dian548,dian549,dian5410,dian5411,dian5412,dian5413,dian5414,dian5415,dian5416,dian5417,dian5418,dian5419,dian5420,dian55,dian56,dian571,dian572,dian573,dian581,dian582,dian583,dian59,dian60,dian61,dian62,dian63,dian64,dian65) 
	VALUES
('$dian2','$dian4','$dian5','$dian6','$dian12','$dian14','$dian24','$dian25','$dian26','$dian27','$dian28','$dian29','$dian30','$dian31','$dian32','$dian33','$dian34','$dian35','$dian36','$dian37','$dian38','$dian39','$dian40','$dian41','$dian411','$dian42','$dian43','$dian44','$dian45','$dian46','$dian47','$dian48','$dian49','$dian501','$dian502','$dian51','$dian52','$dian531','$dian532','$dian533','$dian534','$dian535','$dian536','$dian537','$dian538','$dian539','$dian5310','$dian5311','$dian5312','$dian5313','$dian5314','$dian5315','$dian5316','$dian5317','$dian5318','$dian5319','$dian5320','$dian5321','$dian5322','$dian5323','$dian5324','$dian5325','$dian5326','$dian541','$dian542','$dian543','$dian544','$dian545','$dian546','$dian547','$dian548','$dian549','$dian5410','$dian5411','$dian5412','$dian5413','$dian5414','$dian5415','$dian5416','$dian5417','$dian5418','$dian5419','$dian5420','$dian55','$dian56','$dian571','$dian572','$dian573','$dian581','$dian582','$dian583','$dian59','$dian60','$dian61','$dian62','$dian63concat','$dian64','$dian65')";
odbc_do($conex_o, $insert_amedian);
/////QUERY INSERTAR EN MATRIX/////
mysql_queryV("INSERT INTO root_000119 (Medico,Fecha_data,Hora_data,dian2,dian4,dian5,dian6,dian12,dian14,dian24,dian25,dian26,dian27,dian28,dian29,dian30,dian31,dian32,dian33,dian34,dian35,dian36,dian37,dian38,dian39,dian40,dian41,dian411,dian42,dian43,dian44,dian45,dian46,dian47,dian48,dian49,dian501,dian502,dian51,dian52,dian531,dian532,dian533,dian534,dian535,dian536,dian537,dian538,dian539,dian5310,dian5311,dian5312,dian5313,dian5314,dian5315,dian5316,dian5317,dian5318,dian5319,dian5320,dian5321,dian5322,dian5323,dian5324,dian5325,dian5326,dian541,dian542,dian543,dian544,dian545,dian546,dian547,dian548,dian549,dian5410,dian5411,dian5412,dian5413,dian5414,dian5415,dian5416,dian5417,dian5418,dian5419,dian5420,dian55,dian56,dian571,dian572,dian573,dian581,dian582,dian583,dian59,dian60,dian61,dian62,dian63,dian64,dian65,Seguridad,id)		VALUES 
	('root','$Fecha_data','$Hora_data','$dian2','$dian4','$dian5','$dian6','$dian12','$dian14','$dian24','$dian25','$dian26','$dian27','$dian28','$dian29','$dian30','$dian31','$dian32','$dian33','$dian34','$dian35','$dian36','$dian37','$dian38','$dian39','$dian40','$dian41','$dian411','$dian42','$dian43','$dian44','$dian45','$dian46','$dian47','$dian48','$dian49','$dian501','$dian502','$dian51','$dian52','$dian531','$dian532','$dian533','$dian534','$dian535','$dian536','$dian537','$dian538','$dian539','$dian5310','$dian5311','$dian5312','$dian5313','$dian5314','$dian5315','$dian5316','$dian5317','$dian5318','$dian5319','$dian5320','$dian5321','$dian5322','$dian5323','$dian5324','$dian5325','$dian5326','$dian541','$dian542','$dian543','$dian544','$dian545','$dian546','$dian547','$dian548','$dian549','$dian5410','$dian5411','$dian5412','$dian5413','$dian5414','$dian5415','$dian5416','$dian5417','$dian5418','$dian5419','$dian5420','$dian55','$dian56','$dian571','$dian572','$dian573','$dian581','$dian582','$dian583','$dian59','$dian60','$dian61','$dian62','$dian63concat','$dian64','$dian65','root','')");
 ?>
    
	 <?php  
    	$mensaje1 = "DATOS ALMACENADOS CORRECTAMENTE"; 
		echo "<script>"; 
		echo "if(confirm('$mensaje1'));";   
		echo "window.location = 'procesosdian.php';"; 
		echo "</script>";   
                  
              
     ?>  
	<!-- <div style="margin-top: 10px;  text-align: center" class="row">
        <form method="post" action="procesosdian.php">
            <label style="color: #080808"><strong>DATOS ALMACENADOS CORRECTAMENTE</strong> </label>
            <br><br>
            <input type="submit" class="text-success" value="ACEPTAR"/>
        </form>
    </div> -->
    <?php
   }
   }
    ?>
	

    <!---------------------------------------------------------------------------->

    <div style="margin-left:0" id="" class="container">
      <form id="" method="post" action="dian.php">
        <script>foco()</script>
            <div style="width: 1200px" class="table-bordered">
                    <table class: "table-responsive" width="1200" height="113" style="border: groove; width: 1200px" align="center">
                        <tr>
                            <td width="20%" align="center" style="border: groove; width: 20%">
                                <input type="image" id="btnVer" src="//mx.lasamericas.com.co/matrix/images/medical/paf/logo.png" width="140" height="80">
                          </td>
                            <td width="48%" align="center" style="border: groove; width: 55%">
                                <p><b>RUT</b><strong></strong></p>
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
                                        <td>Fecha de Emision: <? echo $fechaEmision; ?></td>
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
                              <input name="dian2" type="text" id="dian2" size="10" maxlength="2">
                            Actualizacion de oficio </label>
                        </b></td>
                        <td colspan="2" style="border: groove; width: 200%"><b>4. N&uacute;mero de formulario
                          <input name="dian4" type="text" id="dian4" size="30" maxlength="15">
                        </b></td>
                      </tr>
                      <tr>
                        <td style="border: groove"><p align="left"><b>5. N&uacute;mero de identificacion tributaria (NIT): </b> <strong>&ensp; &ensp; &ensp;6. DV</strong></p>
                            <label><b><b><b><b>
                            <input name="dian5" type="text" id="dian5" size="55" onBlur="mensaje(dian5)" maxlength="11">
                            </b></b></b>- </b>
                            <input name="dian6" type="text" max="9" min="0" id="dian6" size="3" maxlength="1">
                            <br>
                          </label></td>
                        <td width="25%" valign="top" style="border: groove"><p><b>12. Direccion seccional:</b></p>
                            <input name="dian12" type="text" id="dian12" size="4" maxlength="2"></td>
                        <td width="50%" valign="top" style="border: groove"><p><b>14. Buz&oacute;n electr&oacute;nico:</b></p>
                            <p><b><b><b>
                              <input name="dian14" type="text" id="dian14" size="25" maxlength="20">
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
                            <input name="dian24" type="text" id="dian24" size="4" maxlength="1">
                          </b></p></td>
                        <td width="248" valign="top" style="border: groove"><p><b>25. Tipo de documento:
                      </b></p>
                          <p><b>    
                            <input name="dian25" type="text" id="dian25" size="4" maxlength="2">
                          </b></p></td>
                        <td width="410" valign="top" style="border: groove"><p><b>26. Numero de Identificacion:
                      </b></p>
                          <p><b>    
                            <input name="dian26" type="text" id="dian26" size="30" maxlength="14">
                        </b></p></td>
                        <td width="568" valign="top" style="border: groove"><p><b>27. Fecha Expedicion:</b></p>
                          <p><b>
                          <input name="dian27" type="text" id="datepicker1" size="30">
                        </b></p></td>
                      </tr>
                      <tr>
                        <td colspan="2" valign="top" style="border: groove"><p><b>Lugar de expedicion</b> &ensp; &ensp; &ensp; <strong>28</strong>. <strong>Pais</strong></p>
                          <p><b>
                            <input name="dian28" type="text" id="dian28" size="8" maxlength="3">
                          </b></p></td>
                        <td valign="top" style="border: groove"><p><strong>29. Departamento:</strong></p>
                          <p><b>
                              <input name="dian29" type="text" id="dian29" size="8" maxlength="5">
                          </b></p></td>
                        <td style="border: groove"><p><strong>30. Ciudad/Municipio:</strong></p>
                          <p><b>
                              <input name="dian30" type="text" id="dian30" size="8" maxlength="3">
                          </b></p>
                        </td>
                      </tr>
                      <tr>
                        <td style="border: groove; width: 25%"><p><b>31. Primer apellido:
                      </b></p>
                          <p><b>    
                            <input name="dian31" type="text" id="dian31" size="30" maxlength="15">
                          </b></p></td>
                        <td style="border: groove; width: 25%"><p><b>32. Segundo apellido:
                      </b></p>
                          <p><b>    
                            <input name="dian32" type="text" id="dian32" size="30" maxlength="15">
                          </b></p></td>
                        <td style="border: groove; width: 50%"><p><b>33. Primer Nombre:</b></p>
                          <p><b>
                          <input name="dian33" type="text" id="dian33" size="30" maxlength="15">
                          </b></p></td>
                        <td style="border: groove; width: 50%"><p><b>34. Otros Nombres:</b></p>
                          <p><b>
                          <input name="dian34" type="text" id="dian34" size="30" maxlength="15">
                          </b></p></td>
                      </tr>
                      <tr>
                        <td colspan="4" style="border: groove; width: 15%"><p><b>35. Razon Social:</b></p>
                          <p><b>
                            <input name="dian35" type="text" id="dian35" size="40" maxlength="35">
                          </b></p></td>
                      </tr>
                      <tr>
                        <td style="border: groove" colspan="3"><p><b>36. Nombre Comercial:</b></p>
                        <p><b>
                          <input name="dian36" type="text" id="dian36" size="40" maxlength="35">
                        </b></p></td>
                        <td style="border: groove"><p><b>37. Sigia:</b></p>
                        <p><b>
                          <input name="dian37" type="text" id="dian37" size="30" maxlength="35">
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
                              <input name="dian38" type="text" id="dian38" size="8" maxlength="10">
                            </b></p></td>
                          <td width="600" valign="top" style="border: groove"><p><strong>39. Departamento:</strong></p>
                            <p><b>
                                <input name="dian39" type="text" id="dian39" size="8" maxlength="10">
                            </b></p></td>
                          <td width="600" style="border: groove"><p><strong>40. Ciudad/Municipio:</strong></p>
                            <p><b>
                              <input name="dian40" type="text" id="dian40" size="8" maxlength="10">
                              </b></p></td>
                        </tr>
                        <tr>
                            <td colspan="3" style="border: groove">
                                <p><b>41. Direccion principal:</b></p>
                                <p><b></b> <b>
                                  <input name="dian41" type="text" id="dian41" size="50" maxlength="30">
                                </b></p></td>
                            <td style="border: groove"><p><b>41. Barrio:</b></p>
                              <p><b></b> <b>
                                <input name="dian411" type="text" id="dian411" size="30" maxlength="30">
                              </b></p></td>
                        </tr>
                        <tr>
                            <td width="350" style="border: groove"><p><strong>42. Correo electronico:</strong></p>
                            <p><b>
                              <input name="dian42" type="text" id="dian42" size="30" maxlength="30">
                          </b></p></td>
                            <td width="347" style="border: groove"><p><strong>43. Codigo postal:</strong></p>
                            <p><b>
                              <input name="dian43" type="text" id="dian43" size="30" maxlength="10">
                          </b></p></td>
                            <td style="border: groove"><p><strong>44. Telefono 1:</strong></p>
                            <p><b>
                              <input name="dian44" type="text" id="dian44" size="30" maxlength="16">
                            </b></p></td>
                            <td style="border: groove"><p><strong>45. Telefono 2:</strong></p>
                            <p><b>
                              <input name="dian45" type="text" id="dian45" size="30" maxlength="16">
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
                                    <input name="dian46" type="text" max="99" min="00" id="dian46" size="10" maxlength="4">
                                </b></div></td>
                                <td><div align="left"><b>
                                    <input name="dian47" type="text" max="99" min="00" id="datepicker2" size="15">
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
                                    <input name="dian48" type="text" max="99" min="00" id="dian48" size="10" maxlength="4">
                                </b></div></td>
                                <td><div align="left"><b>
                                    <input name="dian49" type="text" max="99" min="00" id="datepicker3" size="15">
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
                                      <input name="dian501" type="text" max="99" min="00" id="dian501" size="5" maxlength="4">
                                  </b></div></td>
                                  <td><div align="left"><b>
                                      <input name="dian502" type="text" max="99" min="00" id="dian502" size="5" maxlength="4">
                                  </b></div></td>
                                </tr>
                            </table>
                          </div></td>
                        <td valign="top" style="border: groove; width: 50%"><p><strong>51. Codigo</strong>.</p>
                          <p><b><input name="dian51" type="text" max="99" min="00" id="dian51" size="10" maxlength="4">
                          </b></p></td>
                        <td valign="top" style="border: groove; width: 50%"><p><strong>52. Numero </strong><strong>establecimiento. </strong></p>
                            <p><b>
                              <input name="dian52" type="text" max="99" min="00" id="dian52" size="10" maxlength="3">
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
                              <table border="1" align="left">
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
                                  <input name="dian531" type="text" max="99" min="00" id="dian531" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian532" type="text" max="99" min="00" id="dian532" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian533" type="text" max="99" min="00" id="dian533" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian534" type="text" max="99" min="00" id="dian534" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian535" type="text" max="99" min="00" id="dian535" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian536" type="text" max="99" min="00" id="dian536" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian537" type="text" max="99" min="00" id="dian537" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian538" type="text" max="99" min="00" id="dian538" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian539" type="text" max="99" min="00" id="dian539" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5310" type="text" max="99" min="00" id="dian5310" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5311" type="text" max="99" min="00" id="dian5311" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5312" type="text" max="99" min="00" id="dian5312" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5313" type="text" max="99" min="00" id="dian5313" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5314" type="text" max="99" min="00" id="dian5314" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5315" type="text" max="99" min="00" id="dian5315" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5316" type="text" max="99" min="00" id="dian5316" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5317" type="text" max="99" min="00" id="dian5317" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5318" type="text" max="99" min="00" id="dian5318" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5319" type="text" max="99" min="00" id="dian5319" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5320" type="text" max="99" min="00" id="dian5320" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5321" type="text" max="99" min="00" id="dian5321" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5322" type="text" max="99" min="00" id="dian5322" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5323" type="text" max="99" min="00" id="dian5323" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5324" type="text" max="99" min="00" id="dian5324" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5325" type="text" max="99" min="00" id="dian5325" size="1" maxlength="2">
                                </b></td>
                                <td><b>
                                  <input name="dian5326" type="text" max="99" min="00" id="dian5326" size="1" maxlength="2">
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
                                <input name="dian541" type="text" max="99" min="00" id="dian541" size="4" maxlength="3">
                              </b></td>
                              <td><b>
                                <input name="dian542" type="text" max="99" min="00" id="dian542" size="4" maxlength="3">
                              </b></td>
                              <td><b>
                                <input name="dian543" type="text" max="99" min="00" id="dian543" size="4" maxlength="3">
                              </b></td>
                              <td><b>
                                <input name="dian544" type="text" max="99" min="00" id="dian544" size="4" maxlength="3">
                              </b></td>
                              <td><b>
                                <input name="dian545" type="text" max="99" min="00" id="dian545" size="4" maxlength="3"> 
                              </b></td>
                              <td><b>
                                <input name="dian546" type="text" max="99" min="00" id="dian546" size="4" maxlength="3">
                              </b></td>
                              <td><b>
                                <input name="dian547" type="text" max="99" min="00" id="dian547" size="4" maxlength="3">
                              </b></td>
                              <td><b>
                                <input name="dian548" type="text" max="99" min="00" id="dian548" size="4" maxlength="3">
                              </b></td>
                              <td><b>
                                <input name="dian549" type="text" max="99" min="00" id="dian549" size="4" maxlength="3">
                              </b></td>
                              <td><div align="center"><b>
                                <input name="dian5410" type="text" max="99" min="00" id="dian5410" size="4" maxlength="3">
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
                                  <input name="dian5411" type="text" max="99" min="00" id="dian5411" size="4" maxlength="3">
                                </b></td>
                                <td><b>
                                  <input name="dian5412" type="text" max="99" min="00" id="dian5412" size="4" maxlength="3">
                                </b></td>
                                <td><b>
                                  <input name="dian5413" type="text" max="99" min="00" id="dian5413" size="4" maxlength="3">
                                </b></td>
                                <td><b>
                                  <input name="dian5414" type="text" max="99" min="00" id="dian5414" size="4" maxlength="3">
                                </b></td>
                                <td><b>
                                  <input name="dian5415" type="text" max="99" min="00" id="dian5415" size="4" maxlength="3">
                                </b></td>
                                <td><b>
                                  <input name="dian5416" type="text" max="99" min="00" id="dian5416" size="4" maxlength="3">
                                </b></td>
                                <td><b>
                                  <input name="dian5417" type="text" max="99" min="00" id="dian5417" size="4" maxlength="3">
                                </b></td>
                                <td><b>
                                  <input name="dian5418" type="text" max="99" min="00" id="dian5418" size="4" maxlength="3">
                                </b></td>
                                <td><b>
                                  <input name="dian5419" type="text" max="99" min="00" id="dian5419" size="4" maxlength="3">
                                </b></td>
                                <td><div align="center"><b>
                                    <input name="dian5420" type="text" max="99" min="00" id="dian5420" size="4" maxlength="3">
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
                                <input name="dian55" type="text" max="99" min="00" id="dian55" size="4" maxlength="1">
                              </b></td>
                              <td align="center"><b>
                                <input name="dian56" type="text" max="99" min="00" id="dian56" size="4" maxlength="1">
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
                              <input name="dian571" type="text" max="99" min="00" id="dian571" size="4" maxlength="1">
                            </b></td>
                            <td align="center"><b>
                              <input name="dian572" type="text" max="99" min="00" id="dian572" size="4" maxlength="1">
                            </b></td>
                            <td align="center"><b>
                              <input name="dian573" type="text" max="99" min="00" id="dian573" size="4" maxlength="1">
                            </b></td>
                          </tr>
                          <tr>
                            <td><strong>58. CPC</strong></td>
                            <td align="center" valign="middle"><b>
                              <input name="dian581" type="text" max="99" min="00" id="dian581" size="8" maxlength="2">
                            </b></td>
                            <td align="center" valign="middle"><b>
                              <input name="dian582" type="text" max="99" min="00" id="dian582" size="8" maxlength="2">
                            </b></td>
                            <td align="center" valign="middle"><p><b>
                                <input name="dian583" type="text" max="99" min="00" id="dian583" size="8" maxlength="2">
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
                                <option value="si" selected>SI</option>
                                <option value="no">NO</option>
                              </select>
                              </strong></label>
                              <strong> 60. No. de folios
                                <input name="dian60" type="text" max="99" min="00" id="dian60" size="8" maxlength="3">
                                61.Fecha:</strong> <b>
  								<input name="dian61" type="text" max="99" min="00" id="datepicker4" size="20">
						</b></p></td>
                        </tr>
                        <tr STYLE="background-color: silver">
                            <td style="border: groove" align="center"><p><strong>62. Numero de matricula mercantil de la empresa:</strong> <b>
                              <input name="dian62" type="text" id="dian62" size="40" maxlength="30">
                            </b></p>
                            <p><strong>63. Persona de contacto y cargo: </strong><b>
                              <input name="dian63" type="text" id="dian63" size="40" placeholder="Persona" maxlength="30">
                            </b>-<b>
                            <input name="dian631" type="text" id="dian631" size="40" placeholder="Cargo" maxlength="30">
                            </b></p>
                            <p><strong> <strong>64. Regimen: </strong><strong>
                            <select name="dian64" size="1" id="dian64">
                              <option value="0">Simplificado</option>
                              <option value="2">Comun</option>
                            </select>
                            </strong>- 65. Tipo de persona de contacto: </strong><strong>
                            <select name="dian65" size="1" id="dian65">
                              <option value="1">Persona de Contacto</option>
                              <option value="2">Contacto de Despacho</option>
                              <option value="3">Contabilidad</option>
                              <option value="4">Ventas</option>
                            </select>
                            </strong></p>
                            </td>
                        </tr>
                    </table>
                    <table style="border: groove; width: 1200px; border-top: none">
                        <tr STYLE="background-color: silver">
                            <td style="border: groove" align="center"><label>
							  <input type="hidden" id="accion" name="accion" value="guardar" />
                              <input name="guardar" type="submit" id="guardar" value="GUARDAR">
							  <a href="procesosdian.php" title="Retornar">Volver</a>
            </div>
      </form>
    </div>
</div>
</body>
</html>