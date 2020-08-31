<?php
include_once("conex.php");
include_once("root/comun.php");

$conex = obtenerConexionBD("matrix");

$wactualiz="PROGRAMA: deterioro.php Ver. 2015-05-06   Ing. Gustavo Alberto Avendaño Rivera";

$usuarioValidado = true;

if (!isset($user) || !isset($_SESSION['user'])){
	$usuarioValidado = false;
}else {
	if (strpos($user, "-") > 0)
	$wuser = substr($user, (strpos($user, "-") + 1), strlen($user));
}
if(empty($wuser) || $wuser == ""){
	$usuarioValidado = false;
}

session_start();
//Conexion base de datos
$conexi=odbc_connect('facturacion','','') or die("No se realizo conexión con la BD de Facturación");


	// --> Aca entra cuando se haga el llamado jquery  Esto me lo ayudo el ingeniero Jerson 2015-05-06
	if(isset($accion) && $accion == 'ejecutarDeteriorar')
	{
		$fecha = date("Y-m-d");
		$query = "UPDATE amedeterioro SET estado = 'R' WHERE fue='".$fue."' AND fac='".$fac."' AND estado='P'";
		$resultado = odbc_do($conexi,$query);            // Ejecuto el query
		//echo "query1: ",$query;

		$query = "INSERT INTO amedeterioro VALUES ('".$ano."','".$mes."','".$fue."','".$fac."','".$ccosto."','".$fecfac."','".$cod_resp."',
													'".$nom_resp."','".$vlr_fact."','".$vlr_saldo."','".$porc."','".$vlrdet."','".$texto."','".$fecha."','P','N')";

		$resultado = odbc_do($conexi,$query);            // Ejecuto el query
		//echo "query2: ".$query;
	}
	else
	{
?>
<html>
<head>
<title>Deterioro de Cartera</title>
</head>

<script>
    function ira()
    {
	 //document.deterioro.went.focus();
	}
</script>

<BODY  onload=ira() BGCOLOR="" TEXT="#000066">

  <!-- Loading Calendar JavaScript files -->  <!-- Loading Theme file(s) -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script>

	<!--Librerias jquery  jerson -->  
	<link type="text/css" href="../../../include/root/jquery_1_7_2/css/themes/base/jquery-ui.css" rel="stylesheet"/>
	<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="../../../include/root/jquery_1_7_2/js/jquery-ui.js" type="text/javascript"></script>

<script type="text/javascript">

	// --> Funcion que realiza el llamado jquery para ejecutar el deterioro --- jerson
	function ejecutarDeteriorar()
	{
		cantDet = 0;

		$("[name=verificar]:checked").each(function(){
			consecutivo = $(this).val();
			fue 		= $("#valores"+consecutivo).attr("fue");
			fac 		= $("#valores"+consecutivo).attr("fac");
			ccosto 		= $("#valores"+consecutivo).attr("ccosto");
			fecfac 		= $("#valores"+consecutivo).attr("fecfac");
			cod_resp 	= $("#valores"+consecutivo).attr("cod_resp");
			nom_resp 	= $("#valores"+consecutivo).attr("nom_resp");
			vlr_fact 	= $("#valores"+consecutivo).attr("vlr_fact");
			vlr_saldo 	= $("#valores"+consecutivo).attr("vlr_saldo");
			porcentaje 	= (($("#porcentaje"+consecutivo).attr("id") == undefined) ? 100 : $("#porcentaje"+consecutivo).val());
			vlrdet 		= (vlr_saldo*porcentaje)/100;

			if($("#texto"+consecutivo).attr("id") == undefined)
				texto = $("#textoGeneral").val();
			else
				texto = $("#texto"+consecutivo).val();


			if(porcentaje != "")
			{
				cantDet++;
				var tr = $(this).parent().parent();
				$.post("deterioro.php",
				{
					consultaAjax:  	'',
					accion	:   'ejecutarDeteriorar',
					ano	:   $("#perAno").val(),
					mes	:   $("#perMes").val(),
					fue :	fue,
					fac :	fac,
					ccosto :	ccosto,
					fecfac :	fecfac,
					cod_resp :	cod_resp,
					nom_resp :	nom_resp,
					vlr_fact :	vlr_fact,
					vlr_saldo :	vlr_saldo,
					porc :	porcentaje,
					vlrdet :	vlrdet,
					texto :	texto

				}, function(respuesta){
				tr.remove();
				});
			}
		});

		alert("Facturas deterioradas: "+cantDet);
	}

	function calcularTotal(consecutivo)
	{
		porcentaje = $("#porcentaje"+consecutivo).val();
		valorSaldo = $("#valores"+consecutivo).attr("vlr_saldo");

		valorDet = (valorSaldo*porcentaje)/100;

		$("#valorDet"+consecutivo).text(valorDet);

		$("#verificar"+consecutivo).attr("checked", "checked");

		$("#texto"+consecutivo).focus();

	}

	function cerrarVentana()
	{
	 window.close()
	}


 	// Fn que solo deja digitar los nros del 0 al 9, el . y el enter
	function teclado(elemento)
	{
		elemento = $(elemento);
		if (elemento.val() !="")
		{
			elemento.val(elemento.val().replace(/[^0-9\.]/g, ""));
		}
	}


 function limita(elemento, elEvento, maximoCaracteres) {
   //var elemento = document.getElementById("texto");

  // Obtener la tecla pulsada
   var evento = elEvento || window.event;
   var codigoCaracter = evento.charCode || evento.keyCode;
  // Permitir utilizar las teclas con flecha horizontal
   if(codigoCaracter == 37 || codigoCaracter == 39) {
    return true;
   }

  // Permitir borrar con la tecla Backspace y con la tecla Supr.
   if(codigoCaracter == 8 || codigoCaracter == 46) {
    return true;
   }
   else if(elemento.value.length >= maximoCaracteres ) {
    return false;
   }
   else {
    return true;
   }
 }

 function actualizaInfo(maximoCaracteres) {
   var elemento = document.getElementById("texto");
   var info = document.getElementById("info");

   if(elemento.value.length >= maximoCaracteres ) {
     info.innerHTML = "M?ximo "+maximoCaracteres+" caracteres";
   }
   else {
     info.innerHTML = "Puedes escribir hasta "+(maximoCaracteres-elemento.value.length)+" caracteres adicionales";
   }
 }
</script>

<?php

//==========================================================================================================================================
//PROGRAMA				      :Deterioro de La cartera                                                                                     |
//AUTOR				          :Gustavo Alberto Avendaño Rivera.                                                                            |
//FECHA CREACION			  :Noviembre 14 de 2014																						   |
//FECHA ULTIMA ACTUALIZACION  :Abril 28 de 2015.	   				 																       |
//                                                                                                                                         |
// Modificación               : Abril 28 de 2015, se solicita que el deterioro pida año-mes a deterioriar y guarde el año-mes              |
//                            : Mayo 6 de 2015, se cambia para las librerias jquery, gracias a la ayuda de jerson                          |
//==========================================================================================================================================



if (!$usuarioValidado)
{
	echo '<span class="subtituloPagina2" align="center">';
	echo 'Error: Usuario no autenticado';
	echo "</span><br><br>";

	terminarEjecucion("Por favor cierre esta ventana e ingrese a matrix nuevamente.");
}
else
{
	       //Forma
 echo "<form name='forma' action='deterioro.php' method='post'>";
 echo "<input type='HIDDEN' NAME= 'usuario' value='".$wuser."'/>";

if (!isset($carg) or $carg == '' or !isset($tp) or $tp == '' )
  {

	//Cuerpo de la pagina
 	echo "<table align='center' border=0>";

	//Ingreso de fecha de consulta
 	echo '<span class="subtituloPagina2">';
 	echo 'Ingrese los parámetros de consulta';
 	echo "</span>";
 	echo "<br>";
 	echo "<br>";

	echo "<tr><td align=center colspan=2><b>PROMOTORA MEDICA LAS AMERICAS </b></td></tr>";
	echo "<tr><td align=center colspan=2>DETERIORO DE LA CARTERA - VER 2015-05-06</td></tr>";

   //NIT O CODIGO A BUSCAR O TODOS
 	echo"<tr><td align='CENTER' bgcolor=#cccccc >Año a Deteriorar:</td><td align='CENTER' bgcolor=#cccccc ><input type='input' maxlength=4 size='4' name='ano'></td></tr>";
	echo"<tr><td align='CENTER' bgcolor=#cccccc >Mes a Deteriorar:</td><td align='CENTER' bgcolor=#cccccc ><input type='input' maxlength=2 size='2' name='mes'></td></tr>";
	echo"<tr><td align='CENTER' bgcolor=#cccccc >Digite el CODIGO a Deteriorar:</td><td align='CENTER' bgcolor=#cccccc ><input type='input' maxlength=12 size='12' name='carg'></td></tr>";
	//echo"<tr><td align='CENTER' bgcolor=#cccccc >(T)otal o (P)arcial:</td><td align='CENTER' bgcolor=#cccccc ><input type='input' maxlength=1 size='1' name='tp'></td></tr>";
	echo"<tr><td align='CENTER' bgcolor=#cccccc >Deterioro:</td><td align='CENTER' bgcolor=#cccccc ><select name='tp'><option value='T'>Total</option><option value='P'>Parcial</option></select></td></tr>";

	echo "<tr><td align='CENTER' colspan=4><input type='submit' id='searchsubmit' value='GENERAR'></td></tr>"; //submit osea el boton de GENERAR o Aceptar
    echo "</table>";

    echo '</div>';
    echo '</div>';
    echo '</div>';

  }

  else
  {
  $carg = strtoupper($carg);
    $tp = strtoupper($tp);
  echo "<input type='hidden' name='carg' value='$carg'>";  // esto es para que las variables de trabajo no se pierdan, las que se capturan por pantalla.
  echo "<input type='hidden' name='tp' value='$tp'>";
  echo "<input type='hidden' id='perAno' name='ano' value='$ano'>";  // esto es para que las variables de trabajo no se pierdan, las que se capturan por pantalla.
  echo "<input type='hidden' id='perMes' name='mes' value='$mes'>";

  IF ($tp=='T')
     {
	 echo "<center><table border=1>";
	  echo "<tr><td align=center><b>Justificación del Deterioro Total</b></td></tr>";
	  echo "<td><textarea id='textoGeneral' name='texto' onkeypress='return limita(this, event, 300);' onkeyup='actualizaInfo(100)' rows='4' cols='60'>";
      echo $texto;
	  echo "</textarea></td>";
	 echo "</table>";
     }

  //-------------------------

echo "<center><table border=1>";
echo "<td align=center colspan=12 bgcolor=#99CCCC><font size='3' text color=#FF0000><b>Facturas a Deteriorar</b></font></tr>";

 IF ($tp=='T')
  {
    echo "<tr>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Fte</b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Factura</b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Ccosto</b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Fecha_Factura</b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Codigo_Resp</b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Responsable</b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Vlr Factura</b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Vlr Saldo</b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>% a Deteriorar</b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Vlr Deteriorado</b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Elegir</b></td>";
    echo "</tr>";
  }
  ELSE
  {
    echo "<tr>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Fte</b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Factura</b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Ccosto</b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Fecha_Factura</b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Codigo_Resp</b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Responsable</b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Vlr Factura</b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Vlr Saldo</b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>% a Deteriorar</b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Vlr Deteriorado</b></td>";
    echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Elegir</b></td>";
	echo "<td colspan=1 align=center bgcolor=#DDDDDD><b>Justificación</b></td>";
    echo "</tr>";
  }


  $query=" SELECT COUNT(*) FROM amedeterioro"
        ."  WHERE ced='".$carg."'";

  $resultado1 = odbc_do($conexi,$query);

  //$cantidad = odbc_result($resultado1,1);//CANTIDAD
  //echo "cantidad ",$cantidad;
  
  IF (odbc_result( $resultado1, 1 )==0)
  {
  
    echo "paso1";
	
   $query="SELECT salfue,saldoc,salcco,salfec,salced,salres,carval salfac,salval"
          ." FROM casal,cacar "
	     ." WHERE salced='".$carg."'"
		 ."   AND salfue in ('20','21','22')"
		 ."   AND salano='".$ano."'"
		 ."   AND salmes='".$mes."'"
		 ."   AND salfue=carfue"
		 ."   AND saldoc=cardoc"
	     ." ORDER BY 4,5,1,2";

    $resultado = odbc_do($conexi,$query);            // Ejecuto el query
  }
  ELSE // $odbc_result
  {
   // SI EL CODIGO DE RESPONSABLE YA FUE DETERIORADO ALGUNA VEZ, TRAE MAX DE FECHA
   $query="SELECT ced,max(fecdete) fecdet"
         ."  FROM amedeterioro"
		 ." WHERE ced='".$carg."'"
		 ."   AND porc=100"
		 ."   AND estado='P'"
		 ." GROUP BY 1"
		 ." ORDER BY 1"
		 ."  INTO temp tmpdete";

   $resultado = odbc_do($conexi,$query);            // Ejecuto el query

   // SI EL CODIGO TIENE FACTURAS PARCIALES, TRAERLAS.
   $query1="SELECT fue,fac"
         ."  FROM amedeterioro"
		 ." WHERE ced='".$carg."'"
		 ."   AND porc<>100"
		 ."   AND estado='P'"
		 ." ORDER BY 1,2"
		 ."  INTO temp tmpparcial";

   $resultado1 = odbc_do($conexi,$query1);            // Ejecuto el query

   // PREGUNTAR SI EL TEMP tmpdete TIENE DATOS
   $query2="SELECT COUNT(*)"
          ."  FROM tmpdete";

   $resultado2 = odbc_do($conexi,$query2);            // Ejecuto el query

   $canti      = odbc_result($resultado2,1);//CANTIDAD


   IF ($canti==0)
   {
    $query="SELECT salfue,saldoc,salcco,salfec,salced,salres,carval salfac,salval"
           ." FROM casal,cacar "
	      ." WHERE salced='".$carg."'"
		  ."   AND salfue in ('20','21','22')"
		  ."   AND salano='".$ano."'"
		  ."   AND salmes='".$mes."'"
		  ."   AND salfue=carfue"
		  ."   AND saldoc=cardoc"
		 ." UNION ALL"
		 ." SELECT salfue,saldoc,salcco,salfec,salced,salres,carval salfac,salval"
         ."   FROM casal,cacar,tmpparcial "
	     ."  WHERE salced='".$carg."'"
		 ."    AND salfue in ('20','21','22')"
		 ."    AND salfue=fue"
		 ."    AND saldoc=fac"
		 ."    AND salano='".$ano."'"
		 ."    AND salmes='".$mes."'"
		 ."    AND salfue=carfue"
		 ."    AND saldoc=cardoc"
	     ." ORDER BY 4,1,2";

    $resultado = odbc_do($conexi,$query);            // Ejecuto el query
   }
   ELSE
   {
    $query="SELECT salfue,saldoc,salcco,salfec,salced,salres,carval salfac,salval"
           ." FROM casal,cacar,tmpdete "
	      ." WHERE salced='".$carg."'"
		  ."   AND salfue in ('20','21','22')"
		  ."   AND salced=ced"
		  ."   AND salfec > fecdet"
		  ."   AND salano='".$ano."'"
		 ."    AND salmes='".$mes."'"
		 ."    AND salfue=carfue"
		 ."    AND saldoc=cardoc"
		 ." UNION ALL"
		 ." SELECT salfue,saldoc,salcco,salfec,salced,salres,carval salfac,salval"
         ."   FROM casal,cacar,tmpparcial "
	     ."  WHERE salced='".$carg."'"
		 ."    AND salfue in ('20','21','22')"
		 ."    AND salfue=fue"
		 ."    AND saldoc=fac"
		 ."    AND salano='".$ano."'"
		 ."    AND salmes='".$mes."'"
		 ."    AND salfue=carfue"
		 ."    AND saldoc=cardoc"
         ." INTO TEMP tmpfactu";

    $resultado = odbc_do($conexi,$query);            // Ejecuto el query

	$query="SELECT salfue,saldoc,salcco,salfec,salced,salres,salfac,salval"
	      ."  FROM tmpfactu"
		  ." GROUP BY 1,2,3,4,5,6,7,8"
		  ." ORDER BY 4,1,2";

    $resultado = odbc_do($conexi,$query);            // Ejecuto el query

   }
  }
        $i = 1;
		$ult_valor_ingresado = "";

		while( odbc_fetch_row( $resultado ))
		{
				 // color de fondo
				 if (is_int ($i/2))  // Cuando la variable $i es par coloca este color
				  $wcf="DDDDDD";
				 else
				  $wcf="CCFFFF";

				$fue[$i]      = odbc_result($resultado,1);//FTE
				$fac[$i]      = odbc_result($resultado,2);//FACTURA
				$ccosto[$i]   = odbc_result($resultado,3);//CENTRO DE COSTOS
				$fecfac[$i]   = odbc_result($resultado,4);//FECHA_FACTURA
				$cod_resp[$i] = odbc_result($resultado,5);//CODIGO_RESPONSABLE
				$nom_resp[$i] = odbc_result($resultado,6);//NOMBRE_RESPONSABLE
				$vlr_fact[$i] = odbc_result($resultado,7);//VALOR DE LA FACTURA
				$vlr_saldo[$i]= odbc_result($resultado,8);//VALOR SALDO

				echo "<input type='hidden' id='valores".$i."' fue='".$fue[$i]."' fac='".$fac[$i]."' ccosto='".$ccosto[$i]."' fecfac='".$fecfac[$i]."' cod_resp='".$cod_resp[$i]."' nom_resp='".$nom_resp[$i]."' vlr_fact='".$vlr_fact[$i]."' vlr_saldo='".$vlr_saldo[$i]."'>";

				 echo "<td colspan=1 align=center bgcolor=".$wcf."><font text color=#003366 size=2>".$fue[$i]."</td>";
				 echo "<td colspan=1 align=center bgcolor=".$wcf."><font text color=#003366 size=2>".$fac[$i]."</td>";
				 echo "<td colspan=1 align=center bgcolor=".$wcf."><font text color=#003366 size=2>".$ccosto[$i]."</td>";
				 echo "<td colspan=1 align=center bgcolor=".$wcf."><font text color=#003366 size=2>".$fecfac[$i]."</td>";
				 echo "<td colspan=1 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=2>".$cod_resp[$i]."</td>";
				 echo "<td colspan=1 align=LEFT   bgcolor=".$wcf."><font text color=#003366 size=2>".$nom_resp[$i]."</td>";
				 echo "<td colspan=1 align=RIGHT  bgcolor=".$wcf."><font text color=#003366 size=2>".number_format($vlr_fact[$i],0,'.',',')."</td>";
				 echo "<td colspan=1 align=RIGHT  bgcolor=".$wcf."><font text color=#003366 size=2>".number_format($vlr_saldo[$i],0,'.',',')."</td>";

				IF ($tp=='T')
				{
				 $porc[$i]=100;
				 echo "<td colspan=1 align=center   bgcolor=".$wcf."><font text color=#003366 size=2>".$porc[$i]."</td>";

				 $vlrdet[$i]=$vlr_saldo[$i];

				 echo "<td colspan=1 align=RIGHT  bgcolor=".$wcf."><font text color=#003366 size=2>".number_format($vlrdet[$i],0,'.',',')."</td>";



						echo "<td colspan=1 align=Center   bgcolor=".$wcf."><input type=checkbox name='verificar' id='verificar".$i."' value='".$i."' checked></td>";

				}
				ELSE
				{
				if( $porc[$i] != '' ){
					$ult_valor_ingresado = $i;
				}

				 echo "<td colspan=1 align=center bgcolor=".$wcf."><font text color=#003366 size=2><input type='text' id='porcentaje".$i."' maxlength=3 size=3 name='porc[".$i."]' value='".$porc[$i]."' onkeyup='teclado(this)' OnBlur='calcularTotal(\"".$i."\")'></td>";

				 //$vlrdet[$i]=($vlr_saldo[$i]*$porc[$i])/100;

				 echo "<td colspan=1 align=RIGHT  bgcolor=".$wcf."><font id='valorDet".$i."' text color=#003366 size=2>".number_format($vlrdet[$i],2,'.',',')."</td>";

				 IF ($porc[$i]<>'' or $porc[$i]<>0)
				 {
				   echo "<td colspan=1 align=Center   bgcolor=".$wcf."><input type=checkbox name='verificar' id='verificar".$i."' value='".$i."' checked ></td>";
				 }
				 ELSE
				 {
				   echo "<td colspan=1 align=Center   bgcolor=".$wcf."><input type=checkbox name='verificar' id='verificar".$i."' value='".$i."' ></td>";
				 }

				  echo "<td><textarea id='texto".$i."' name='texto[".$i."]' onkeypress='return limita(this, event, 300);' onkeyup='actualizaInfo(100)' rows='4' cols='60'>";
				  echo $texto[$i];
				  echo "</textarea></td>";

				}

				 echo "</tr>";
				 $i++;

		}


        $wnrocue=$i-1;
        echo "<tr><td align=center colspan=12 bgcolor=#99CCCC><font size=3 text color=#003366>Nro de Facturas Para Deteriorar: ".$wnrocue."</font></td></tr>";


    echo "<center><table border=1>";
   	echo "<tr><td align=center colspan=12 bgcolor=#C0C0C0>";
   	echo "<input type='button' value='Deteriorar' onclick='ejecutarDeteriorar();'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

   	echo "<input type=checkbox name=conf>Confirmar&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

   	echo "<input type='button' name='BReset' onclick ='cerrarVentana()' value='Cerrar ventana' id='BReset'>";

   	echo "<tr><td align=center colspan=12 bgcolor=#C0C0C0>";
   	echo "</td></tr>";
    echo "</center></table>";
	
	echo "<div id='errores'></div>";



   echo "<table border=1>";
   echo "<tr><td align=center colspan=100 bgcolor=#99CCCC>";
   echo "<font size=3 text color=#33FFFF><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#99CCCC LOOP=-1>ERROR, EN LOS DATOS DIGITADOS!!!! o NO HAY FACTURAS A DETERIORAR</MARQUEE></font>";
   echo "</td></tr></table><br><br>";

  echo "</center></table>";




echo "<script>";
echo "document.getElementById('texto[".$ult_valor_ingresado."]').focus();";
echo "</script>";


// para cerrar la conexion con UNIX.
  liberarConexionOdbc( $conexi );
  odbc_close_all();
 }


echo "</Form></BODY>
</html>";
	}
	}

?>