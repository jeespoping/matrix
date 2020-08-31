<html>
<head>
<title>Consulta de transacciones realizadas por el boton de pagos</title>
</head>

<script>

    function ira()
    {
	 document.webserverconsultatran.$wfec1.focus();
	}
</script>

<BODY  onload=ira() BGCOLOR="" TEXT="#000066">

  <!-- Loading Calendar JavaScript files -->  <!-- Loading Theme file(s) OJO: El calendario NO funciona en programas que se ubiquen en la raiz www -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>

<?php
include_once("conex.php");

//  ESTE SCRIPT FUE REALIZADO BASADO EN LA DUCUMENTACION PDF DEL ARCHIVO "Servicio Las Americas.pdf"

echo "<form action='webserverconsultatran.php' method=post >";

  echo "<center><table border=0>";
  echo "<tr><td align=center><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
  echo "<tr><td align=center>TRANSACCIONES REALIZADAS POR EL BOTON DE PAGO<b></td></tr>";
  echo "<tr><td align=Right><font text color=#003366 size=1>Version 15-03-2017 Autor: JairS</font></td></tr>";
  echo "<tr>";

  echo "<tr><td align=center bgcolor=#C0C0C0 colspan=1><b><font text color=#003366 size=2>";
  
  if (!isset($wfec1))   // Si no esta seteada entonces la inicializo en el primer dia del mes actual con formato aaaa-mm-dd
  {
    $hoy = date("Y-mm-dd");
    $wfec1=substr($hoy,0,4)."-".substr($hoy,5,2)."-01";
  }

  
    echo "<tr><td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha Inicial<br></font></b>";
   	$cal="calendario('wfec1','1')";
	echo "<input type='TEXT' name='wfec1' size=10 maxlength=10  id='wfec1'  value=".$wfec1." class=tipo3><button id='trigger1' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec1',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
	   //]]></script>
	<?php

  if (!isset($wfec2))   // Si no esta seteada entonces la inicializo con la fecha actual con formato aaaa-mm-dd
   $wfec2 = date("Y-m-d");

    echo "<tr><td align=CENTER colspan=1 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Fecha Final<br></font></b>";
   	$cal="calendario('wfec2','1')";
	echo "<input type='TEXT' name='wfec2' size=10 maxlength=10  id='wfec2' value=".$wfec2." class=tipo3><button id='trigger2' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'wfec2',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});
	   //]]></script>
	<?php
	
   
	 $a=array(1=>"Aprobadas",2=>"Rechazadas",3=>"Pendientes",4=>"Con Errores",5=>"Expiradas"); 
	 echo "<tr><td align=center bgcolor=#DDDDDD colspan=1><b><font text color=#003366 size=2>Estado:</font></b><br>";
	 echo "<select name='west'>";
	 //echo "<option></option>";                // Primera en blanco 
	 if (isset($west)) //Si esta seteada
	 {
        for ($i = 1; $i <= count($a); $i++)
        {
		 if ( $west == $i )    // ==> Ese Item es el seleccionado 
	      echo "<option SELECTED value='".$i."'>".$a[$i]."</option>";
		 else 
		  echo "<option value='".$i."'>".$a[$i]."</option>";
		}
	 
      echo "</select><br>";  	 
	 } 
	 else          //No seteada o primera vez
	 {
       for ($i = 1; $i <= count($a); $i++)
         echo "<option value='".$i."'>".$a[$i]."</option>";
	
      echo "</select><br>";   	
	 } 

     echo "<tr><td bgcolor=#cccccc align=center>";
     echo "<input type='submit' value='Generar'></td></tr>";
	 echo "</table>";

 if ( isset($west) and $west<>'' and isset($wfec1) and $wfec1<>'' and isset($wfec2) and $wfec2<>'' )          // Ya hay datos seleccionados
 {
  
  // Llamada al WebService 
  $client = new SoapClient("http://services.lasamericas.com.co/ServiceTransaccion.svc?singleWsdl");
  
  // Ajusto las fechas al formato permitido por el WebServer  Ej: 2016-09-01T00:00:00.000   2016-09-30T23:59:59.999
  
  $wfechai=substr($wfec1,0,10)."T00:00:00.000";
  $wfechaf=substr($wfec2,0,10)."T23:59:59.999";
   
  // FECHAS PARA PRUEBAS
 // $wfechai = "2017-02-09T00:00:00.000";
 // $wfechaf = "2017-02-09T23:59:59.999";
 
  switch ($west) 
  {
    case 1:
       $result = $client->ConsultarTransaccion(array("state_pol" => "4", "fecha_desde" => $wfechai, "fecha_hasta" => $wfechaf ));
        break;
    case 2:
        $result = $client->ConsultarTransaccion(array("state_pol" => "6", "fecha_desde" => $wfechai, "fecha_hasta" => $wfechaf ));
        break;
    case 3:
       $result = $client->ConsultarTransaccion(array("state_pol" => "7", "fecha_desde" => $wfechai, "fecha_hasta" => $wfechaf ));
        break;
    case 4:
        $result = $client->ConsultarTransaccion(array("state_pol" => "104", "fecha_desde" => $wfechai, "fecha_hasta" => $wfechaf ));
        break;
    case 5:
        $result = $client->ConsultarTransaccion(array("state_pol" => "5", "fecha_desde" => $wfechai, "fecha_hasta" => $wfechaf ));
        break;
  }
   
  // Como me dicen que el Servicio descrito retorna un objeto tipo <TransaccionDto> entonces lo tomo como un XML
  $xml = $result->ConsultarTransaccionResult->TransaccionDto;
  
  //Nro de transacciones
  $wnro = count($xml);
  
   echo "<center><table border=0>";
     
   echo "<tr><td align=Left bgcolor=#DDDDDD colspan=5><b><font text color=#003366 size=1><i></font></b><br>";
   
   echo "<tr>";
   echo "<td colspan=1 align=center bgcolor=#DDDDDD><font text color=#003366 size=2><b>Fecha<b></td>";
   echo "<td colspan=1 align=center bgcolor=#DDDDDD><font text color=#003366 size=2><b>Id<b></td>";
   echo "<td colspan=1 align=center bgcolor=#DDDDDD><font text color=#003366 size=2><b>Cedula<b></td>";
   echo "<td colspan=1 align=center bgcolor=#DDDDDD><font text color=#003366 size=2><b>Nombre<b></td>";
   echo "<td colspan=1 align=center bgcolor=#DDDDDD><font text color=#003366 size=2><b>Valor<b></td>";
   echo "</tr>";
   
  // Cuando devuelve mas de una transaccion crea un arreglo por lo que se muestran asi:
  if ( $wnro > 1 )
  {
	  
   for ($i = 0; $i <= $wnro-1; $i++)
   {

	 
     $wfec= $xml[$i]->processing_date;
     $wano=substr($wfec,0,4);
     $wmes=substr($wfec,5,2);
     $wdia=substr($wfec,8,2);
	 $fechatran = $wano."/".$wmes."/".$wdia;
	 
	 $wid  = $xml[$i]->id;
	 $wced = $xml[$i]->payer_document;
	 $wnom = $xml[$i]->buyer_full_name;
	 $wtel = $xml[$i]->telephone;
	 $wvlr = $xml[$i]->amount;
	 
	 if (is_int ($i/2))  // Cuando la variable $i es para coloca este color
      $wcf="DDDDDD";  
     else
      $wcf="CCFFFF";
	 
	 echo "<tr>";
	 echo "<td colspan=1 align=Left bgcolor=".$wcf."><font text color=#003366 size=2>".$wfec."</td>";
     echo "<td colspan=1 align=Left bgcolor=".$wcf."><font text color=#003366 size=2>".$wid."</td>";
     echo "<td colspan=1 align=Left bgcolor=".$wcf."><font text color=#003366 size=2>".$wced."</td>";
     echo "<td colspan=1 align=Left bgcolor=".$wcf."><font text color=#003366 size=2>".$wnom."</td>";
     echo "<td colspan=1 align=Left bgcolor=".$wcf."><font text color=#003366 size=2>".$wvlr."</td>";
	 echo "</tr>";
	
   }
   
  }
  else
  {
	if ( $wnro == 1 )
	{
	 
     $wfec= $xml->processing_date;
     $wano=substr($wfec,0,4);
     $wmes=substr($wfec,5,2);
     $wdia=substr($wfec,8,2);
	 $fechatran = $wano."/".$wmes."/".$wdia;
	 
	 $wid  = $xml->id;
	 $wced = $xml->payer_document;
	 $wnom = $xml->buyer_full_name;
	 $wtel = $xml->telephone;
	 $wvlr = $xml->amount;
	
	 echo "<tr>";
	 echo "<td colspan=1 align=Left bgcolor=DDDDDD><font text color=#003366 size=2>".$wfec."</td>";
     echo "<td colspan=1 align=Left bgcolor=DDDDDD><font text color=#003366 size=2>".$wid."</td>";
     echo "<td colspan=1 align=Left bgcolor=DDDDDD><font text color=#003366 size=2>".$wced."</td>";
     echo "<td colspan=1 align=Left bgcolor=DDDDDD><font text color=#003366 size=2>".$wnom."</td>";
     echo "<td colspan=1 align=Center bgcolor=DDDDDD><font text color=#003366 size=2>".$wvlr."</td>";	
     echo "</tr>";
	  
	 }
		
  }
  echo "<td colspan=5 align=Left bgcolor=DDDDDD><font text color=#003366 size=2>Nro de transacciones: ".$wnro."</td>";
  unset($client);	  
 }
echo "</html>";
echo "</body>";
echo "</form>";

?>