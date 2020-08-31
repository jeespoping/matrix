<head>
  <title>FACTURAR IPS</title>
</head>
<body>
<script type="text/javascript">
    function enter()
	{
     document.forms.facturar.submit();
	}

	function ModificarCuenta()
	{
     var wbiniciar = document.getElementById("winiciar");
     var wcgrabar = document.getElementById("wgrabar");

	 wbiniciar.disabled = false;
	 wcgrabar.disabled = true;
	}

	function upperCase(x)
	{
	 var y=x.value.toUpperCase();
	 x.value = y;
	}

	function cerrarVentana()
	 {
      top.close()
     }

</script>

<?php
include_once("conex.php");
  /************************************************
   *     PROGRAMA PARA LA GRABACION DE CARGOS     *
   *           DE PACIENTES CLINICA               *
   ************************************************/

//==================================================================================================================================
//PROGRAMA                   : cargos_ips.php
//AUTOR                      : Juan Carlos Hernández M.
  $wautor="Juan C. Hernandez M.";
//FECHA CREACION             : Mayo 9 de 2005
//FECHA ULTIMA ACTUALIZACION :
  $wactualiz="Diciembre 24 de 2013";
//DESCRIPCION
//====================================================================================================================================\\
//Este programa se hace con el objetivo de registrar los cargos de los pacientes de la clinica del sur, basandose en el ingreso por   \\
//admisiones, el programa para la grabacion del cargo se basa en el responsable e ingreso de la historia clinica, teniendo en cuenta  \\
//las tarifas de dicha empresa, además debe permitir grabar desde diferentes unidades de la clinica y los siguientes tipos de cargos: \\
//Cargos de pacientes procedimientos, consultas, cirugia, ayudas diagnosticas, material medico quirurgico, medicamentos, honorarios,  \\
//anestesiologia, etc.                                                                                                                \\
//====================================================================================================================================\\


//========================================================================================================================================\\
//========================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                         \\
//========================================================================================================================================\\
//-------------------------------------------------------------------------------------------------------------------------------------------
//  --> 2017-10-11, Camilo Zapata
//      se modifica el texto de la resoluciòn de la dian
//-------------------------------------------------------------------------------------------------------------------------------------------
////-------------------------------------------------------------------------------------------------------------------------------------------
//	-->	2013-12-24, Jerson trujillo.
//		El maestro de conceptos que actualmente es la tabla 000004, para cuando entre en funcionamiento la nueva facturacion del proyecto
//		del ERP, esta tabla cambiara por la 000200; Entonces se adapta el programa para que depediendo del paramatro de root_000051
//		'NuevaFacturacionActiva' realice este cambio automaticamente.
//-------------------------------------------------------------------------------------------------------------------------------------------
//A B R I L  2  DE  2013:                                                                                                            \\
//________________________________________________________________________________________________________________________________________\\
//Se crea indicador en root_000051 con nombre de aplicacion facturaRadicadaParticular para indicar si al generar una factura para	un 	  \\
//paciente particular la factura quede en estado radicada (RD) o generdada (GE). La factura quedará RD si el valor de la aplicacion es on \\
//en caso contrario el estado de la factura quedará en generada (GE)																	  \\
//________________________________________________________________________________________________________________________________________\\                                                                                                                                       \\
//M A R Z O  5  DE  2013:                                                                                                            \\
//________________________________________________________________________________________________________________________________________\\
//Se modifico el programa para que cuando se genera una factura para un particular se genere siempre en estado RADICADA (RD)		      \\
//																																		  \\
//________________________________________________________________________________________________________________________________________\\
//J U N I O  1  DE 2007:                                                                                                                  \\
//________________________________________________________________________________________________________________________________________\\
//Se modifico el programa apara que cuando hay combinaciones de descuento. lo tenga en cuenta, esto se dio porque para las empresas       \\
//Particulares en el concepto 6405 se tiene un descuento del 15%, pero además cunado se fue a facturar un paciente que tenia este concepto\\
//se dio un 10% de descuento en la pantalla de facturación, porque el programa se aplico el descuento a los conceptos propios del 10%,pero\\
//en la factura dio los dos descuentos, esto corrigio, para que tome los dos descuentos independientemente sin acumularlos, es decir los  \\
//aplica individualmente, a los conceptos propios les aplica el 10% y al concepto 6405 le aplica el 15%.                                  \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//M A Y O  11  DE 2007:                                                                                                                   \\
//________________________________________________________________________________________________________________________________________\\
//Se modifico el calculo del descuento para que cuando el descuento sea dado a la empresa en general o sea que este definido en el maestro\\
//de Empresas, el calculo se haga sobre toda la cuenta del paciente sin importar que el concepto sea propio o compartido.                 \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//F E B R E R O  2  DE 2007:                                                                                                              \\
//________________________________________________________________________________________________________________________________________\\
//Se modifico la grabación en la tabla 000066 en donde se adiciono el campo rcftip, para poder identificar si ese valor se facturo por    \\
//Excedente o por Reconocido y asi ante una posible anulación de la factura se pueda hacer facil la identificación.                       \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//E N E R O  23 DE 2007:                                                                                                                  \\
//________________________________________________________________________________________________________________________________________\\
//Se modifico el programa para que al momento de facturar mostrara una columna con el usuario que grabo el cargo.                         \\
//costo y el usuario, porque al parecer se desconecta la sesión y cuando se va a grabar se conecta pero no con estos datos.               \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//E N E R O  19 DE 2007:                                                                                                                  \\
//________________________________________________________________________________________________________________________________________\\
//Se hace una nueva validación para que no se vuelvan a grabar facturas sin número, validando antes de grabar que exista el centro de     \\
//costo y el usuario, porque al parecer se desconecta la sesión y cuando se va a grabar se conecta pero no con estos datos.               \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//D I C I E M B R E  15 DE 2006:                                                                                                          \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para que permita generar facturas negativas cuando el valor del abono es superior al valor de los conceptos,    \\
//para este se plantea que se le debe devolver el dinero al usuario con una nota debito que cancela o vuelva el valor de la factura y el  \\
//saldo cero.                                                                                                                             \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//D I C I E M B R E  15 DE 2006:                                                                                                          \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para que se valide que existe informacion en los campos de documento y nombre del segundo paciente o usuario    \\
//responsable de la cuenta del paciente, el programa por defecto coloca los datos del paciente, pero si por algun motivo lo cambian queda \\
//validado que si le hallan ingresado informacion, tambien se corrige que al cambiar de historia se cambie la informacion del encabezado. \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//N O V I E M B R E  30 DE 2006:                                                                                                          \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica la generacion de facturas para que actualice en la 000021 creando la relacion con el o los abonos.                          \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//N O V I E M B R E  29 DE 2006:                                                                                                          \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica la generacion de facturas a otros responsables diferentes al principal para que tenga en cuenta los abonos en estas.        \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//N O V I E M B R E  1 DE 2006:                                                                                                           \\
//________________________________________________________________________________________________________________________________________\\
//Se corrige la grabacion de la factura en la tabla 000065 para que queden bien calculados los saldos de los conceptos.                   \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//O C T U B R E   25 DE 2006:                                                                                                             \\
//________________________________________________________________________________________________________________________________________\\
//Se actualiza el programa para que tenga en cuenta los cargos que han sido facturados y cuya factura se anulo, sobre todo en los casos   \\
//en que el cargo quedo facturado en mas de una factura, entonces puede que se anule una factura pero la(s) otra(s) no; porque en esta    \\
//modificación se tuvo en cuenta eso, que trajera solo parte pendiente de facturar del cargo correspondiente a la factura que se anulo.   \\
//Se tomo como el valor total del cargo menos los valores facturados de excedente y responsable y se adiciono un query para que traiga    \\
//los cargos sin facturar pero que no este facturado totalmente.                                                                          \\
//                                                                                                                                        \\                                                                                                                                       \\
//________________________________________________________________________________________________________________________________________\\
//O C T U B R E   23 DE 2006:                                                                                                             \\
//________________________________________________________________________________________________________________________________________\\
//Se modifica el programa para tenga en cuenta los descuentos por concepto que se le hagan a las empresas, solo estaba teniendo en cuenta \\
//los descuentos que se le hacian al paciente al momento de facturar. Es decir los descuentos quedan funcionado de la siguiente manera:   \\
//Para los pacientes o para la cuenta del paciente solo aplica al momento de facturar y por el porcentaje que se coloque en el encabezado \\
//de este programa (facturar historias) y solo se le aplica el descuento a los conceptos propios, este porcentaje solo aplica para la     \\
//cuenta del paciente; Para los descuentos a empresas se debe haber grabado o definido en la tabla _000117 y el descuento aplica para     \\
//para cada concepto y en el porcentaje que se halla definido es esta tabla.                                                              \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//X X X X X X X X X  ## DE 2006:                                                                                                          \\
//________________________________________________________________________________________________________________________________________\\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\
//xxxxxxxxxxxxxxxxxxx.                                                                                                                    \\
//                                                                                                                                        \\
//________________________________________________________________________________________________________________________________________\\
//X X X X X X X X X  ## DE 2006:                                                                                                          \\
//________________________________________________________________________________________________________________________________________\\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx     \\
//xxxxxxxxxxxxxxxxxxx.                                                                                                                    \\
//                                                                                                                                        \\
//                                                                                                                                        \\
//========================================================================================================================================\\
//========================================================================================================================================\\

session_start();

/*
if (!isset($user))
	{
	 if(!isset($_SESSION['user']))
		session_register("user");
	}
*/

if(!isset($_SESSION['user']))
	echo "error";
else
{
  session_register("wpagook");
  session_register("wprestamo");

  //

  //		      or die("No se ralizo Conexion");

  

  include_once("root/comun.php");
  


  $wbasedato = consultarAliasPorAplicacion( $conex, $wemp_pmla, "facturacion" );	//Abril 1 de 2013

  //$conexunix = odbc_pconnect('facturacion','infadm','1201')
  //					    or die("No se ralizo Conexion con el Unix");

	//---------------------------------------------------------------------------------------------
	// --> 	Consultar si esta en funcionamiento la nueva facturacion
	//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
	//---------------------------------------------------------------------------------------------
	$nuevaFacturacion = consultarAliasPorAplicacion($conex, $wemp_pmla, 'NuevaFacturacionActiva');
	//---------------------------------------------------------------------------------------------
	// --> 	MAESTRO DE CONCEPTOS:
	//		- Antigua facturacion 	--> 000004
	//		- Nueva facturacion 	--> 000200
	//		Para la nueva facturacion cuando esta entre en funcionamiento el maestro
	//		de conceptos cambiara por la tabla 000200.
	//		Fecha cambio: 2013-12-24	Autor: Jerson trujillo.
	//----------------------------------------------------------------------------------------------
	$tablaConceptos = $wbasedato.(($nuevaFacturacion == 'on') ? '_000200' : '_000004');
	//----------------------------------------------------------------------------------------------


  $pos = strpos($user,"-");
  $wusuario = substr($user,$pos+1,strlen($user));

  $wfecha=date("Y-m-d");
  $hora = (string)date("H:i:s");

  echo "<form name='facturar' action='Facturar_ips.php' method=post>";

  echo "<input type='HIDDEN' name='wbasedato' value='".$wbasedato."'>";
  echo "<input type='HIDDEN' name='wemp_pmla' value='".$wemp_pmla."'>";	//Abril 1 de 2013
  echo "<input type='HIDDEN' name='wrecalcular' value='off' id='wrecalcular'>";


  //ACA TRAIGO LAS VARIABLES DE SUCURSAL, CAJA Y TIPO INGRESO QUE REALIZA CADA CAJERO
  $q =  " SELECT cjecco, cjecaj, cjetin, cjetem "
       ."   FROM ".$wbasedato."_000030 "
       ."  WHERE cjeusu = '".$wusuario."'"
       ."    AND cjeest = 'on' ";
  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
  $num = mysql_num_rows($res);

  if ($num > 0)
     {
      $row = mysql_fetch_array($res);

      $pos = strpos($row[0],"-");
      $wcco = substr($row[0],0,$pos);
      $wnomcco = substr($row[0],$pos+1,strlen($row[0]));

      $pos = strpos($row[1],"-");
      $wcaja = substr($row[1],0,$pos);
      $wnomcaj = substr($row[1],$pos+1,strlen($row[1]));

      $wtiping = $row[2];
      if (!isset($wtipcli)) $wtipcli = $row[3];
     }
    else
       echo "EL USUARIO ESTA INACTIVO O NO TIENE PERMISO PARA FACTURAR";

  $wcol=6;  //Numero de columnas que se tienen o se muestran en pantalla



  //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
  // funcion para Buscar si un concepto es de ** abono ** y de que tipo
  //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
  function busca_abono($wccon, $wffac, $wnfac, $wvcon, $waccion, $wreg)
	{
     global $wabono;
     global $wvalabo;
     global $wbasedato;
     global $wregistro;
     global $conex;
	 global $tablaConceptos;


	 //ACA BUSCO SI EL CONCEPTO ES DE ABONO
     $q= "SELECT gruabo, abogen, abocmo, abocop, abofra, abotiq "
        ."  FROM ".$tablaConceptos.", ".$wbasedato."_000116"
        ." WHERE grucod = '".$wccon."'"
        ."   AND grutab = abocod ";
     $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
     $row_con = mysql_fetch_array($res2);

     if ($row_con[0] == "on")  //Entra si el concepto es de abono
       {
	    $wabono="on";
	    $wvcon=abs($wvcon);

	    //===================================================================================================
        //ACA BUSCO TODOS LOS TIPOS DE ABONOS PARA ACTUALIZAR LA TABLA _000018 EN SUS CAMPOS CORRESPONDIENTES
        if ($waccion=="on")          //*** Si debe actualizar ***
           {
	        if ($row_con[1]=="on")                          //Abono general
	            $q = " UPDATE ".$wbasedato."_000018 "
	                ."    SET fenabo = fenabo + ".$wvcon
	                ."  WHERE fenffa = '".$wffac."'"
	                ."    AND fenfac = '".$wnfac."'";
	          elseif ($row_con[2]=="on")                    //Abono Cuota moderadora
                     $q = " UPDATE ".$wbasedato."_000018 "
	                     ."    SET fencmo = fencmo + ".$wvcon
	                     ."  WHERE fenffa = '".$wffac."'"
	                     ."    AND fenfac = '".$wnfac."'";
                   elseif ($row_con[3]=="on")               //Abono Copagos
			              $q = " UPDATE ".$wbasedato."_000018 "
	                           ."    SET fencop = fencop + ".$wvcon
	                           ."  WHERE fenffa = '".$wffac."'"
	                           ."    AND fenfac = '".$wnfac."'";
			             elseif ($row_con[4]=="on")         //Abono Franquicia
				                $q = " UPDATE ".$wbasedato."_000018 "
	                                ."    SET fencop = fencop + ".$wvcon
	                                ."  WHERE fenffa = '".$wffac."'"
	                                ."    AND fenfac = '".$wnfac."'";
				              elseif ($row_con[5]=="on")    //Abono Tiquetes
					                 $q = " UPDATE ".$wbasedato."_000018 "
	                                     ."    SET fenabo = fenabo + ".$wvcon
	                                     ."  WHERE fenffa = '".$wffac."'"
	                                     ."    AND fenfac = '".$wnfac."'";
			$res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			if ($wreg != "")
			   {
				$q = " UPDATE ".$wbasedato."_000021 "
	                ."    SET rdeffa = '".$wffac."', "
	                ."        rdefac = '".$wnfac."'"
	                ."  WHERE rdereg = '".$wreg."'"
	                ."    AND rdeest = 'on' ";
	            $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
               }
           }
	   }
	  else
	     $wabono="off";
    }
  //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&


  //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
  // funcion para ordenar una matriz con usort
  //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
  function ordenar($vec1,$vec2)
	{
		if($vec1[30] > $vec2[30])
			return 1;
		elseif ($vec1[30] < $vec2[30])
					return -1;
				else
					return 0;
	}
  //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&

  //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&
  function bisiesto($year)
	{
     return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
	}


  function validar_fecha($dato)
	{
     $fecha="^([[:digit:]]{4})-([[:digit:]]{1,2})-([[:digit:]]{1,2})$";
	 if(ereg($fecha,$dato,$occur))
	   {
	    if($occur[2] < 0 or $occur[2] > 12)
	      return false;
	    if(($occur[3] < 0   or  $occur[3] > 31) or
	       ($occur[2] == 4  and $occur[3] > 30) or
	       ($occur[2] == 6  and $occur[3] > 30) or
		   ($occur[2] == 9  and $occur[3] > 30) or
		   ($occur[2] == 11 and $occur[3] > 30) or
		   ($occur[2] == 2  and $occur[3] > 29 and bisiesto($occur[1])) or
		   ($occur[2] == 2  and $occur[3] > 28 and !bisiesto($occur[1])))
		    return false;
		 return true;
	   }
	  else
	     return false;
	}
  //&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&&

  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FUNCION PARA VERIFICAR QUE TODOS LOS CARGOS DE UN PAQUETE HALLAN SIDO GRABADOS
  function verifica_paquete($wphis, $wping, $wppaq)
       {
	    global $wbasedato;
	    global $conex;
	    global $wfalta_paq;

	    $q = " SELECT paqdetcon "
	        ."   FROM ".$wbasedato."_000114 "
	        ."  WHERE paqdetcod = '".$wppaq."'"
	        ."    AND paqdetest ='on' ";
	    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	    $num = mysql_num_rows($res);

	    if ($num > 0)
	       {
		    for ($i=1;$i<=$num;$i++)
		        {
	             $row = mysql_fetch_array($res);

	             $q = " SELECT count(*) "
	                 ."   FROM ".$wbasedato."_000115 "
	                 ."  WHERE movpaqcod = '".$wppaq."'"
	                 ."    AND movpaqcon = '".$row[0]."'"
	                 ."    AND movpaqest = 'on' ";
	             $respaq = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	             $rowpaq = mysql_fetch_array($respaq);

	             if ($rowpaq[0] == 0)
	                $wfalta_paq="on";
                }
           }
	   }


  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FUNCION PARA CALCULAR LOS TOPES DE CADA RESPONSABLE
  function tope1($j,$k)
    {
	 global $wvalemp;
	 global $wtopemp1;
	 global $wtopemp;
	 global $wcanres;
	 global $wresp;
	 global $wvalemp;
	 global $wvalpac;


	 $wtot_res=0;
     for ($h=1;$h<=$j;$h++)
         $wtot_res=$wtot_res+$wvalemp[$h];

     if ($k == 1)   //Para un solo responsable principal
        {
	     if (($wtot_res > $wtopemp1))
	        {
		     if ($wcanres > 1)    //Si tiene mas de un responsable, paso el los valores superiores al tope del responsable ppal, al sgte responsable 2dario
		        {
			     if (isset($wresp[$j][$k+1]) and $wresp[$j][$k+1] == 0)
			        {
				     $wresp[$j][$k+1]=$wresp[$j][$k+1] + ($wtot_res-$wtopemp1);
			         $wvalemp[$j]=$wvalemp[$j] - ($wtot_res-$wtopemp1);
		             echo "<input type='HIDDEN' name='wresp[".$j."][$k+1]' value='".$wresp[$j][$k+1]."'>";
			        }
				}
		       else
		          {
			       $wvalpac[$j]=$wvalpac[$j] + ($wtot_res-$wtopemp1);
			       $wvalemp[$j]=$wvalemp[$j] - ($wtot_res-$wtopemp1);
			      }

	         echo "<input type='HIDDEN' name='wvalpac[".$j."]' value='".$wvalpac[$j]."'>";
		     echo "<input type='HIDDEN' name='wvalemp[".$j."]' value='".$wvalemp[$j]."'>";
			}
	    }
	   else
	     {  //Entra por aca cuando es mas de un responsable
		  $wtot_res=0;
		  for ($h=1;$h<=$j;$h++)
		      $wtot_res=$wtot_res+$wresp[$h][$k];

		  if (($wtot_res > $wtopemp[$k]))
		    {
			 if ($wcanres > $k)   //Entra aca siempre y cuando la cantidad de responsables sea mayor al responsable que se esta cuadrando el tope
			    {
			     if ($wresp[$j][$k+1] == 0)
				    {
					 $wresp[$j][$k+1]=$wresp[$j][$k+1] + ($wtot_res-$wtopemp[$k]);
				     $wresp[$j][$k]  =$wresp[$j][$k]   - ($wtot_res-$wtopemp[$k]);

				     echo "<input type='HIDDEN' name='wresp[".$j."][$k+1]' value='".$wresp[$j][$k+1]."'>";
				     echo "<input type='HIDDEN' name='wresp[".$j."][$k]' value='".$wresp[$j][$k]."'>";
				    }
			    }
			   else   //Entra por aca solo si se esta cuadrando el ultimo responsable y este tiene tambien tope, si queda algo lo pasa al paciente
			        {
				     $wvalpac[$j]  =$wvalpac[$j]   + ($wtot_res-$wtopemp[$k]);
				     $wresp[$j][$k]=$wresp[$j][$k] - ($wtot_res-$wtopemp[$k]);

				     echo "<input type='HIDDEN' name='wresp[".$j."][$k]' value='".$wresp[$j][$k]."'>";
				     echo "<input type='HIDDEN' name='wvalpac[".$j."]' value='".$wvalpac[$j]."'>";
			        }
			}
         }
	}


  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //Para buscar si existen cargos anteriores al ingreso actual sin facturar
  function cargos_anteriores()
       {
	    global $conex;
        global $wbasedato;

        global $wcajadm;

        global $whistoria;
        global $wing;

	    $q =  " SELECT tcaring "
	         ."   FROM ".$wbasedato."_000106 "
	         ."  WHERE tcarhis = '".$whistoria."'"
	         ."    AND tcaring <= '".(intval($wing)-1)."'+0"
	         ."    AND tcarest = 'on' "
	         ."    AND tcarvto <> (tcarfex+tcarfre) "    //Trae los cargos con valores negativos o positivos
	         ."    AND tcarfac = 'S' "
	         ."  GROUP BY 1 " ;
	    $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	    $num = mysql_num_rows($res);

	    if ($num > 0)
	       {
		    $wingresos = "";
		    for ($i=1;$i<=$num;$i++)
		       {
			    $row = mysql_fetch_array($res);
			    $wingresos= $wingresos.$row[0].", ";
		       }

		       $wmensaje="HAY CARGOS PENDIENTES DE FACTURAR DEL(OS) SIGUIENTE(S) INGRESO(S): ".$wingresos;

		       echo '<script language="javascript">';
			   echo 'alert ("'.$wmensaje.'")';
			   echo '</script>';
		   }
	   }


  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FUNCION PARA MOSTRAR LOS ARTICULOS SELECCIONADOS PARA LA VENTA
  function mostrar()
       {
	     global $whistoria;
	     global $wing;
	     global $wno1;
	     global $wno2;
	     global $wap1;
         global $wap2;
         global $wdoc;
         global $wnitemp;
         global $wnomemp;
         global $wfec;
         global $wser;
         global $wcodcon;
         global $wnomcon;
         global $wprocod;
         global $wpronom;
         global $wcodter;
         global $wnomter;
         global $wporter;
         global $wcantidad;
         global $wvaltar;
         global $wrecexc;
         global $wfacturable;

         global $conex;
         global $wbasedato;
         global $wusuario;
         global $wcco;
         global $wnomcco;

         global $wcanres;
         global $wvalpac;
         global $wvalemp;
         global $wvaldto;
         global $wvaldesemp;
         global $wresp;


         global $wcf;
         global $wcf2;
         global $wclfa;
         global $wclfg;

         global $wtotpac;
         global $wtotemp;
         global $wtotres;
         global $wtotgenres;


         global $wdscto;
         global $wdesc_gral;    //M A Y O  11  DE 2007
         global $wcanres;
         global $wrecemp1;
         global $wtopemp1;
         global $wrecemp;
         global $wtopemp;

         global $wok;
         global $winiciar;
         global $wreiniciar;

         global $wregistro;
         global $wreg_matriz;
         global $wtotal_columnas_matriz;

         global $wfact;
         global $wreg;
         global $wno_facturar;    //En esta variable almaceno todos los id que se seleccionaron en pantalla para no facturarlos
         global $wno_incluir;

         global $whay_registros;  //Indica si hay registros para facturar o no

         global $wfac;
         global $wrec;

         global $wcargos_sin_facturar;
		 global $tablaConceptos;


         if (!isset($wcargos_sin_facturar)) // and $wcargos_sin_facturar=="")
            {
	         cargos_anteriores();
	         //$wcargos_sin_facturar="ok";
	        }
	     echo "<input type='HIDDEN' name='wcargos_sin_facturar' value='".$wcargos_sin_facturar."'>";

         ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		 //// OJO OJO OJO OJO OJO OJO OJO OJOOJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO ////
		 //// OJO CUANDO SE CAMBIE ALGO EN ESTE QUERY, SE DEBE CAMBIAR TAMBIEN EN EL MISMO QUERY ESTA ABAJO EN LA GRABACION DEL DETALLE  ////
		 //// DE LAS FACTURAS.                                                                                                           ////
		 //// OJO OJO OJO OJO OJO OJO OJO OJOOJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO OJO ////
		 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

		 if ($wno_incluir=="")
			$wno_incluir="0";

		 $q = " SELECT id, tcarfac, tcarrec "
		     ."   FROM ".$wbasedato."_000106 "
		     ."  WHERE tcarhis              = '".$whistoria."'"
	         ."    AND tcaring              = '".$wing."'"
	         ."    AND tcarest              = 'on' "
	         ."    AND tcarcan              > 0 "
	         ."    AND tcarfac              = 'S' "
	         ."    AND id                   not in (".$wno_incluir.")"
	         ."    AND abs(tcarfex+tcarfre) < abs(tcarvto) ";
	     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	     $wcanreg=mysql_num_rows($res);

		 for ($i=1;$i<=$wcanreg;$i++)
		    {
			 $row = mysql_fetch_array($res);

			 //On
			 //echo "Facturable: ".$wfac[$i]."<br>";
			 //echo "Cantidad: ".$wcanreg."<br>";

			 //On
			 //echo "Reconocido: ".$wrec[$i]."<br>";

			 if ($row[0]==$wreg[$i] and $row[1]!=$wfac[$i])   //Pregunto de cada linea si ha cambiado el Facturable
			    {
			     $q= " UPDATE ".$wbasedato."_000106 "
			        ."    SET tcarfac='".$wfac[$i]."'"
			        ."  WHERE tcarhis  = '".$whistoria."'"
	         		."    AND tcaring  = '".$wing."'"
	         		."    AND tcarest  = 'on' "
	         		."    AND id       = ".$row[0];
			     $resupd = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		        }

		      if ($row[0]==$wreg[$i] and $row[2]!=$wrec[$i])   //Pregunto de cada linea si ha cambiado el Reconocido
			    {
			     $q= " UPDATE ".$wbasedato."_000106 "
			        ."    SET tcarrec='".$wrec[$i]."'"
			        ."  WHERE tcarhis  = '".$whistoria."'"
	         		."    AND tcaring  = '".$wing."'"
	         		."    AND tcarest  = 'on' "
	         		."    AND id       = ".$row[0];
			     $resupd = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		        }
			}


	     for ($i=1;$i<=$wcanreg;$i++)
	        {
	         if (isset($wfact[$i]) and $wfact[$i] != "")
	            {
		         $wno_incluir=$wno_incluir.",".$wreg[$i];
		         unset($wfact[$i]);
	            }
		    }


	     echo "<input type='HIDDEN' name='wno_incluir' value='".$wno_incluir."'>";




		 ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	     //ACA TRAIGO TODOS LOS CARGOS GRABADOS
	     //CON ESTE PRIMER QUERY TRAIGO LOS CARGOS QUE SE HAN FACTURADO Y QUE LUEGO LA FACTURA SE ANULO
	     $q = " SELECT tcarusu, tcarhis, tcaring, tcarfec, tcarsin, tcarres, tcarno1, tcarno2, tcarap1, tcarap1,tcardoc, tcarser, "
	         ."        tcarconcod, tcarconnom, tcarprocod, tcarpronom, tcartercod, tcarternom, tcarterpor, tcarcan, tcarvun, tcarvto-tcarfex-tcarfre, "
	         ."        tcarrec, tcarfac, tcarfec,id, tcartfa, tcarfex, tcarfre, tcarfac, tcarrec "
	         ."   FROM ".$wbasedato."_000106 "
	         ."  WHERE tcarhis  = '".$whistoria."'"
	         ."    AND tcaring  = '".$wing."'"
	         ."    AND tcarest  = 'on' "
	         ."    AND tcarcan  > 0 "
	         ."    AND tcarfac  = 'S' "
	         ."    AND id      not in (".$wno_incluir.")"
	         ."    AND abs(tcarfex+tcarfre) < abs(tcarvto) ";  //Con esto me aseguro de traer todos los cargos de abonos
	     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	     $num = mysql_num_rows($res);

	     $wreg_matriz=$num;

	     if ($num > 0)
	        {
		     $wtotcta=0;
		     $wtotpac=0;
	         $wtotemp=0;
	         $wvaldto=0;
	         if ($wcanres > 2)
	            for ($j=2;$j<=$wcanres;$j++)
			        $wtotres[$j] = 0;

	         echo "</table>";
	         echo "<center><table >";

	         echo "<br>";

	         echo "<tr><td align=center colspan=16 class='encabezadotabla'><font size=5><b>D E T A L L E &nbsp&nbsp D E &nbsp&nbsp L A &nbsp&nbsp C U E N T A</b></font></td></tr>";

	         //***************************************************************************
	         //TITULOS *******************************************************************
		     echo "<tr>";
		     echo "<th class='encabezadotabla'><font text size=3>No se<br>Factura</font></th>";
		     echo "<th class='encabezadotabla'><font text size=3>Reg.</font></th>";
		     echo "<th class='encabezadotabla'><font text size=3>Usuario</font></th>";
		     echo "<th class='encabezadotabla'><font text size=3>Fact.<br>(S/N)</font></th>";
		     echo "<th class='encabezadotabla'><font text size=3>R/E</font></th>";
		     echo "<th class='encabezadotabla'><font text size=3>Fecha</font></th>";
		     echo "<th class='encabezadotabla'><font text size=3>Concepto</font></th>";
			 echo "<th class='encabezadotabla'><font text size=3>Procedimiento</font></th>";
			 echo "<th class='encabezadotabla' colspan=2><font text size=3>Tercero</font></th>";
			 echo "<th class='encabezadotabla'><font text size=3>Cantidad</th></font>";
			 echo "<th class='encabezadotabla'><font text size=3>V/r Total</font></th>";
			 echo "<th class='encabezadotabla'><font text size=3>V/r Pac.</font></th>";
			 echo "<th class='encabezadotabla'><font text size=3>V/r Resp.</font></th>";
			 if ($wcanres > 1 and !isset($wreiniciar))
				for ($j=2;$j<=$wcanres;$j++)
				    {
				     echo "<th class='encabezadotabla'><font text size=3>V/r Resp.".$j."</font></th>";
				     $wtotres[$j]=0;
				    }
			 echo "</tr>";


			 //ACA HAGO LOS CALCULOS DE PORCENTAJES PARA TODOS LOS RESPONSABLES
			 $wporpac=(100-$wrecemp1);

			 if (isset($wcanres) and $wcanres > 1)
			    for ($j=2;$j<=$wcanres;$j++)
			        $wporpac = $wporpac-$wrecemp[$j];

			 $wsaltop1=$wtopemp1;

			 $wvaldesemp=0;    //Valor descuento Empresa, se acumula cuando el descuento es por concepto
			 $wvalrecemp=0;    //Valor recargo Empresa, se acumula cuando el recargo es por concepto

			 $wpasodescgral="off";  //Indica que ya calculo el descuento gral por empresa, es decir, ya no se puede calcular otro descuento

			 //*****************************************
			 //MUESTRA LA CUENTA
			 //Con este for se recorren todos los cargos
			 for ($i=1;$i<=$num;$i++)
	            {
		         $row = mysql_fetch_array($res);

	             $wregistro[$i][1]=$row[0];    //Usuario
		         $wregistro[$i][2]=$row[1];    //Historia
		         $wregistro[$i][3]=$row[2];    //Ingreso
		         $wregistro[$i][4]=$row[3];    //Fecha
		         $wregistro[$i][5]=$row[4];    //Serv. ingreso
		         $wregistro[$i][6]=$row[5];    //Emp. Responsable
		         $wregistro[$i][7]=$row[6];    //Nombre 1
		         $wregistro[$i][8]=$row[7];    //Nombre 2
		         $wregistro[$i][9]=$row[8];    //Apellido 1
		         $wregistro[$i][10]=$row[9];   //Apellido 2
		         $wregistro[$i][11]=$row[10];  //Documento
		         $wregistro[$i][12]=$row[11];  //Servicio de Grabacion
		         $wregistro[$i][13]=$row[12];  //Codigo Concepto
		         $wregistro[$i][14]=$row[13];  //Nombre Concepto
		         $wregistro[$i][15]=$row[14];  //Codigo Proced.
		         $wregistro[$i][16]=$row[15];  //Nombre Proced.
		         $wregistro[$i][17]=$row[16];  //Codigo Tercero
		         $wregistro[$i][18]=$row[17];  //Nombre Tercero
		         $wregistro[$i][19]=$row[18];  //Porcentaje Tercero
		         $wregistro[$i][20]=$row[19];  //Cantidad
		         $wregistro[$i][21]=$row[20];  //Valor Unitario
		         $wregistro[$i][22]=$row[21];  //Valor total
		         $wregistro[$i][23]=$row[22];  //Reconocido o Excedente
		         $wregistro[$i][24]=$row[23];  //Facturable S o N
		         $wregistro[$i][25]=$row[24];  //Fecha del Cargo
		         $wregistro[$i][26]=$row[25];  //Nro Registro
		         $wregistro[$i][27]=$row[26];  //Tipo de Facturacion
		         $wregistro[$i][28]=$row[27];  //Facturado Excedente
		         $wregistro[$i][29]=$row[28];  //Facturado Reconocido
		         $wregistro[$i][30]=$row[11].$row[12].$row[16];  //CCosto + Concepto + Tercero


		         $q= " SELECT grufpa "
		            ."   FROM ".$tablaConceptos." "
		            ."  WHERE grucod = '".$row[12]."'";
		         $rescon = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		         $rowcon = mysql_fetch_array($rescon);

		         $wfacparcial = $rowcon[0];  //Esta variable me indica si el concepto se puede facturar parcialmente o No.

		         if (trim($wfacparcial) != "on")
		            $wfacparcial="off";

		         if ($i%2==0)
	                $wcolor="fila2";
	               else
	                  $wcolor="";

	             echo "<tr>";


	             //==================================================================================================================
	             //SE FACTURA O NO
	             //==================================================================================================================
	             if (!isset($wfact[$i]))
			        echo "<td align=CENTER class='".$wcolor."'><INPUT TYPE='checkbox' NAME='wfact[".$i."]' onchange='this.value=this.value.toUpperCase(); ModificarCuenta();'></td>";
			       else
			          echo "<td align=CENTER class='".$wcolor."'><INPUT TYPE='checkbox' NAME='wfact[".$i."]' checked onchange='this.value=this.value.toUpperCase(); ModificarCuenta();'></td>";
			     //==================================================================================================================

			     echo "<td align=RIGHT class='".$wcolor."'><font size=2><b><INPUT TYPE='text' NAME='wreg[".$i."]' VALUE='".$row[25]."' size=8 readonly='readonly'></b></font></td>";     //Registro o Id
		         echo "<td align=LEFT  class='".$wcolor."'><font size=2>".$row[0]."</font></td>";             //Usuario que Grabo

		         echo "<td align=LEFT  class='".$wcolor."'><font size=2><b><INPUT TYPE='text' NAME='wfac[".$i."]' VALUE='".$row[29]."' size=1 maxlength=1 onchange='this.value=this.value.toUpperCase(); ModificarCuenta();' onkeypress='if (event.keyCode!=115 & event.keyCode!=83 & event.keyCode!=110 & event.keyCode!=78) event.returnValue=false'></b></font></td>";     //Si es Facturable (S/N)
		         echo "<td align=LEFT  class='".$wcolor."'><font size=2><b><INPUT TYPE='text' NAME='wrec[".$i."]' VALUE='".$row[30]."' size=1 maxlength=1 onchange='this.value=this.value.toUpperCase(); ModificarCuenta();' onkeypress='if (event.keyCode!=114 & event.keyCode!=82 & event.keyCode!=101 & event.keyCode!=69) event.returnValue=false'></b></font></td>";     //Si es Reconocido o Excedente (R/N)
			     echo "<td align=LEFT  class='".$wcolor."'><font size=2>".$row[24]."</font></td>";            //Fecha de Grabacion
			     echo "<td align=LEFT  class='".$wcolor."'><font size=2>".$row[13]."</font></td>";            //Concepto
			     if ($row[15] != "")
			        echo "<td align=LEFT   class='".$wcolor."'><font size=2>".$row[15]."</font></td>";        //Procedimiento
			       else
	                  echo "<td align=LEFT class='".$wcolor."'><font size=2>&nbsp</font></td>";
	             if ($row[16] != "")
	                echo "<td align=LEFT class='".$wcolor."'><font size=2>".$row[16]."</font></td>";          //Nit Tercero
	               else
	                  echo "<td align=LEFT class='".$wcolor."'><font size=2>&nbsp</font></td>";               //Nit Tercero
	             if ($row[17] != "")
	                echo "<td align=LEFT class='".$wcolor."'><font size=2>".$row[17]."</font></td>";          //Nombre Tercero
	               else
	                  echo "<td align=LEFT class='".$wcolor."'><font size=2>&nbsp</font></td>";               //Nombre Tercero
	             echo "<td align=center  class='encabezadotabla'><font size=3><b>".number_format($row[19],0,'.',',')."</b></font></td>"; //Cantidad
			     echo "<td align=RIGHT  class='encabezadotabla'><font size=3><b>".number_format($row[21],0,'.',',')."</b></font></td>";  //Valor total

			     $wcolor='encabezadotabla';

			     $wsumotr=0;
	             if ($wcanres >= 1) //Columnas de los demas responsables
	                {
				     //===============================================================================================================================
				     //*** ACA EVALUO LOS *** TOPES *** DE TODOS LOS RESPONSABLES ***
				     //===============================================================================================================================
				     if ($wtopemp1 > 0)               //Tope del responsable principal
				        tope1($i,1);

				     for ($j=2;$j<=$wcanres;$j++)     //Topes de los demas responsables
				         if (isset($wtopemp[$j]) and  $wtopemp[$j] > 0)
				            tope1($i,$j);

				     for ($k=2;$k<=$wcanres;$k++)
				         if (isset($wresp[$i][$k])) $wsumotr=$wsumotr+$wresp[$i][$k];
			        }

			     if (!isset($wreiniciar))
			        {
				     //Excedente
				     if ($row[22]=="E")  //Columna del paciente
				        {
					     if (!isset($wvalpac[$i]))         //Columna Paciente
					        {
					         echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalpac[".$i."]' VALUE='".($row[21])."' size=8 onchange='ModificarCuenta();'> </font></td>";
					         $wtotpac = $wtotpac+($row[21]);
					         $wvalpac[$i] = ($row[21]);
				            }
			               else
				              {
					           if (isset($wfac_parcial) and $wfac_parcial != "on")
					              {
						           if (($row[21]) != ($wvalpac[$i]+$wvalemp[$i]+$wsumotr))
								      {
									   $wcolor="#GGGGGG";
								       $wok="off";
								      }
								      {
									   echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalpac[".$i."]' VALUE='".($row[21])."' size=8 onchange='ModificarCuenta();'> </font></td>";
								       $wtotpac = $wtotpac+($row[21]);
								       $wvalpac[$i] = ($row[21]);
							          }
						          }
						         else
						            {
							         if (($row[21]) >= ($wvalpac[$i]+$wvalemp[$i]+$wsumotr))
							            {
						                 echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalpac[".$i."]' VALUE='".$wvalpac[$i]."' size=8 onchange='ModificarCuenta();'> </font></td>"; //Valor excedente paciente
					                     $wtotpac = $wtotpac+$wvalpac[$i];
				                        }
				                       else
				                          {
										   $wcolor="#GGGGGG";
									       $wok="off";
									      }
				                    }
							  }

				         if (!isset($wvalemp[$i]))         //Columna Responsable Ppal
				            echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalemp[".$i."]' VALUE='0' size=8 onchange='ModificarCuenta();'> </font></td>";
				           else
				              {
					           if (isset($wfac_parcial) and $wfac_parcial != "on")
					              {
						           if (($row[21]) != ($wvalpac[$i]+$wvalemp[$i]+$wsumotr))
								     {
									  $wcolor="#GGGGGG";
								      $wok="off";              //Indica que no se puede hacer la grabacion hasta que no se corrijan los valores
								     }

								   if ($wvalemp[$i] != 0)
								      echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalemp[".$i."]' VALUE='".$wvalemp[$i]."' size=8 onchange='ModificarCuenta();'> </font></td>";
								     else
								        echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalemp[".$i."]' VALUE='0' size=8 onchange='ModificarCuenta();'> </font></td>";
								  }
								 else
								   {
								    if (($row[21]) >= ($wvalpac[$i]+$wvalemp[$i]+$wsumotr))
							           echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalemp[".$i."]' VALUE='".$wvalemp[$i]."' size=8 onchange='ModificarCuenta();'> </font></td>";
							          else
				                          {
										   $wcolor="#GGGGGG";
									       $wok="off";
									      }
							       }
					          }

						 if (isset($wvalpac[$i])) $wregistro[$i][31]=$wvalpac[$i];  //Facturado al Paciente
						 if (isset($wvalemp[$i])) $wregistro[$i][32]=$wvalemp[$i];  //Facturado al Responsable Ppal

						 if ($wvalpac[$i]==" ")
				            {
					         $wcolor="#GGGGGG";
							 $wok="off";
					        }
						}


			         //Reconocido Responsable Ppal
				     if ($row[22]=="R") //Columna del responsable principal
				        {
					     if ($wtopemp1 == 0)
					       {
					        if ((!isset($wvalpac[$i]) or $wrecemp1 != (100)))   //Columna Paciente
					           {
						        echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalpac[".$i."]' VALUE='".round(($row[21])*($wporpac/100))."' size=8 onchange='ModificarCuenta();'></font></td>"; //".number_format(($row[19]*$row[20]),0,'.',',')."</font></td>";               //Valor excedente paciente
						        $wtotpac=$wtotpac+round(($row[21])*($wporpac/100));
						        $wvalpac[$i] = round(($row[21])*($wporpac/100));
					           }
					          else
					             {
						          if ($wfacparcial != "on")
					                 {
							          if (($row[21]) != ($wvalpac[$i]+$wvalemp[$i]+$wsumotr))
								         {
									      $wcolor="#GGGGGG";
								          $wok="off";                              //Indica que no se puede hacer la grabacion hasta que no se corrijan los valores
								         }
							           else
							              {
								           echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalpac[".$i."]' VALUE='".$wvalpac[$i]."' size=8 onchange='ModificarCuenta();'> </font></td>"; //Valor excedente paciente
							               $wtotpac = $wtotpac+$wvalpac[$i];
						                  }
					                 }
					                else
					                   {
						                //Esto se hace con abs() para que se pueda modificar o facturar parcialmente tambien los valores negativos
						                //Mayo 7 de 2009
						                if (abs($row[21]) >= abs($wvalpac[$i]+$wvalemp[$i]+$wsumotr))
						                   {
							                echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalpac[".$i."]' VALUE='".$wvalpac[$i]."' size=8 onchange='ModificarCuenta();'> </font></td>"; //Valor excedente paciente
						                    $wtotpac = $wtotpac+$wvalpac[$i];
					                       }
					                      else
					                         {
											  $wcolor="#GGGGGG";
										      $wok="off";
										     }
					                   }
						         }

					        if ((!isset($wvalemp[$i])) or $wrecemp1 != (100)) //Columna Responsable Ppal
					           {
						        if (!isset($wvalemp[$i]))
						           {
					                echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalemp[".$i."]' VALUE='".round((($row[21])*($wrecemp1/100)))."' size=8 onchange='ModificarCuenta();'> </font></td>";
					                $wtotemp    = $wtotemp+round((($row[21])*($wrecemp1/100)));
					                $wvalemp[$i]= round((($row[21])*($wrecemp1/100)));
					               }
				                  else
				                     {
					                  if ($wrecemp1 > 0)
				                        {
					                     echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalemp[".$i."]' VALUE='".round((($row[21])*($wrecemp1/100)))."' size=8 onchange='ModificarCuenta();'> </font></td>";
					                     $wtotemp    =$wtotemp+round((($row[21])*($wrecemp1/100)));
					                     $wvalemp[$i]=round((($row[21])*($wrecemp1/100)));
				                        }
			                           else
			                              {
				                           echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalemp[".$i."]' VALUE='".$wvalemp[$i]."' size=8 onchange='ModificarCuenta();'> </font></td>";
				                           $wtotemp = $wtotemp+$wvalemp[$i];
			                              }
					                 }
				               }
					          else
					             {
						          if ($wfacparcial != "on")
					                 {
							          if (($row[21]) != ($wvalpac[$i]+$wvalemp[$i]+$wsumotr))
								        {
									     $wcolor="#GGGGGG";
									     $wok="off";                             //Indica que no se puede hacer la grabacion hasta que no se corrijan los valores
									    }
									  if ($wvalpac[$i] == 0 and ($wsumotr == 0))
									     {
									      echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalemp[".$i."]' VALUE='".($row[21])."' size=8 onchange='ModificarCuenta();'> </font></td>";               //Valor excedente paciente
									      $wtotemp    = $wtotemp+round($row[21]);
									      $wvalemp[$i]= round($row[21]);
							             }
									    else
								           {
						                    echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalemp[".$i."]' VALUE='".$wvalemp[$i]."' size=8 onchange='ModificarCuenta();'> </font></td>";
						                    $wtotemp = $wtotemp+$wvalemp[$i];
					                       }
				                     }
				                    else
							           {
								        if (abs($row[21]) >= abs($wvalpac[$i]+$wvalemp[$i]+$wsumotr))  //Se puede facturar parcial, pero verifico que el valor sea igual o menor al total del cargo
						                   {
								            echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalemp[".$i."]' VALUE='".$wvalemp[$i]."' size=8 onchange='ModificarCuenta();'> </font></td>";
					                        $wtotemp = $wtotemp+$wvalemp[$i];
				                           }
				                          else
					                         {
											  $wcolor="#GGGGGG";
										      $wok="off";
										     }
				                       }
				                 }
			               }
			              else
			                 {  //Entra por aca si tiene TOPE el responsable ppal
			                  if ((!isset($wvalpac[$i]) or $wrecemp1 != (100)))   //Columna Paciente
			                     {
						          echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalpac[".$i."]' VALUE='".round(($row[21])*($wporpac/100))."' size=8 onchange='ModificarCuenta();'> </font></td>"; //".number_format(($row[19]*$row[20]),0,'.',',')."</font></td>";               //Valor excedente paciente
						          $wtotpac    =$wtotpac+round(($row[21])*($wporpac/100));
						          $wvalpac[$i]=round(($row[21])*($wporpac/100));
						         }
						        else
						           {
							        if ($wfacparcial != "on")
					                   {
								        if (($row[21]) != ($wvalpac[$i]+$wvalemp[$i]+$wsumotr))
								          {
									       $wcolor="#GGGGGG";
									       $wok="off";                                //Indica que no se puede hacer la grabacion hasta que no se corrijan los valores
									      }
									    if (!isset($wreiniciar) or $wreiniciar=="")
									       {
										    echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalpac[".$i."]' VALUE='".$wvalpac[$i]."' size=8 onchange='ModificarCuenta();'> </font></td>"; //Valor excedente paciente
									        $wtotpac=$wtotpac+$wvalpac[$i];
								           }
							           }
							          else
							             {
								          if (($row[21]) >= ($wvalpac[$i]+$wvalemp[$i]+$wsumotr))  //Se puede facturar parcial, pero verifico que el valor sea igual o menor al total del cargo
						                     {
								              echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalpac[".$i."]' VALUE='".$wvalpac[$i]."' size=8 onchange='ModificarCuenta();'> </font></td>"; //Valor excedente paciente
									          $wtotpac=$wtotpac+$wvalpac[$i];
								             }
								            else
					                           {
											    $wcolor="#GGGGGG";
										        $wok="off";
										       }
								         }

							       }

						        if ((!isset($wvalemp[$i])) or $wrecemp1 != (100)) //Columna Responsable Ppal
						           {
						            echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalemp[".$i."]' VALUE='".round(($row[21])*($wrecemp1/100))."' size=8 onchange='ModificarCuenta();'> </font></td>";
						            $wtotemp    =$wtotemp+round((($row[21])*($wrecemp1/100)));
						            $wvalemp[$i]=round(($row[21])*($wrecemp1/100));
						           }
						          else
						             {
							          if ($wfacparcial != "on")
					                     {
								          if (($row[21]) != ($wvalpac[$i]+$wvalemp[$i]+$wsumotr))
									        {
										     $wcolor="#GGGGGG";
										     $wok="off";                              //Indica que no se puede hacer la grabacion hasta que no se corrijan los valores
										    }
										  if (($wvalpac[$i] == 0 and $wsumotr == 0))
										     {
											  echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalemp[".$i."]' VALUE='".$wvalemp[$i]."' size=8 onchange='ModificarCuenta();'> </font></td>";               //Valor excedente paciente
										      $wtotemp=$wtotemp+$wvalemp[$i];
										     }
									        else
									           if (!isset($wreiniciar))
									              {
							                       echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalemp[".$i."]' VALUE='".$wvalemp[$i]."' size=8 onchange='ModificarCuenta();'> </font></td>";
							                       $wtotemp = $wtotemp+$wvalemp[$i];
							                      }
						                 }
						                else
						                   {
							                if (($row[21]) >= ($wvalpac[$i]+$wvalemp[$i]+$wsumotr))  //Se puede facturar parcial, pero verifico que el valor sea igual o menor al total del cargo
						                       {
					                            echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalemp[".$i."]' VALUE='".$wvalemp[$i]."' size=8 onchange='ModificarCuenta();'> </font></td>";
					                            $wtotemp = $wtotemp+$wvalemp[$i];
				                               }
				                              else
					                             {
											      $wcolor="#GGGGGG";
										          $wok="off";
										         }
					                       }
						             }
						       //===============================================================================
						     }
				         $wregistro[$i][31]=$wvalpac[$i];  //Facturado al Paciente
						 $wregistro[$i][32]=$wvalemp[$i];  //Facturado al Responsable Ppal
						}


			         //Otros Responsables
				     if ($wcanres > 1) //Columnas de los demas responsables
					    for ($j=2;$j<=$wcanres;$j++)
					      {
						   if (isset($wtopemp[$j]) and $wtopemp[$j] == 0)
						     {
							   if (!isset($wresp[$i][$j]) or ($wsumotr != round(($row[21])*($wrecemp[$j]/100)) and $row[22]=="R"))
							       {
								   if ($wrecemp[$j] <= 0)
								      {
							           if (isset($wresp[$i][$j])) $wval_celda=$wresp[$i][$j];
						              }
							         else
							            $wval_celda=round(($row[21])*($wrecemp[$j]/100));

							       if (trim($wval_celda)=="") $wval_celda=0;
							       echo "<td align=RIGHT class='".$wcolor."'><INPUT TYPE='text' NAME='wresp[".$i."][".$j."]' VALUE='".$wval_celda."' size=8 onchange='ModificarCuenta();'></td>";
							       $wtotres[$j]  = $wtotres[$j]+$wval_celda;
							       $wresp[$i][$j]= $wval_celda;
						          }
						         else
						           {
							        if ($wfacparcial != "on")
							           {
								        if (($row[21]) != ($wvalpac[$i]+$wvalemp[$i]+$wsumotr) and ($wresp[$i][$j] > 0))
									       {
										    $wcolor="#GGGGGG";
										    $wok="off";          //Indica que no se puede hacer la grabacion hasta que no se corrijan los valores
										   }
										  else
										     $wresp[$i][$j]=round(($row[21])*($wrecemp[$j]/100));

									    echo "<td align=RIGHT class='".$wcolor."'><INPUT TYPE='text' NAME='wresp[".$i."][".$j."]' VALUE='".$wresp[$i][$j]."' size=8 onchange='ModificarCuenta();'></td>";
									    $wtotres[$j] = $wtotres[$j]+$wresp[$i][$j];
								       }
								      else
								         {
									      if (($row[21]) >= ($wvalpac[$i]+$wvalemp[$i]+$wsumotr))  //Se puede facturar parcial, pero verifico que el valor sea igual o menor al total del cargo
						                     {
									          echo "<td align=RIGHT class='".$wcolor."'><INPUT TYPE='text' NAME='wresp[".$i."][".$j."]' VALUE='".$wresp[$i][$j]."' size=8 onchange='ModificarCuenta();'></td>";
									          $wtotres[$j] = $wtotres[$j]+$wresp[$i][$j];
								             }
								            else
					                           {
											    $wcolor="#GGGGGG";
										        $wok="off";
										       }
								         }
								   }
						     }
						    else
						       {
							    if (!isset($wresp[$i][$j]) or ($wsumotr != round(($row[21])*($wrecemp[$j]/100)) and $row[22]=="R"))
								   {
									if ($wrecemp[$j] <= 0)
								       {
									    if (isset($wresp[$i][$j]))
								           $wval_celda=$wresp[$i][$j];
								          else
								             $wval_celda=0;
							           }
								      else
								         $wval_celda=round(($row[21])*($wrecemp[$j]/100));
							        echo "<td align=RIGHT class='".$wcolor."'><INPUT TYPE='text' NAME='wresp[".$i."][".$j."]' VALUE='".$wval_celda."' size=8 onchange='ModificarCuenta();'></td>";
							        $wtotres[$j]  =$wtotres[$j]+$wval_celda;
							        $wresp[$i][$j]=$wval_celda;
						           }
							      else
							         {
								      if ($wfacparcial != "on")
					                     {
									      if (($row[21]) != ($wvalpac[$i]+$wvalemp[$i]+$wsumotr))
									        {
										     $wcolor="#GGGGGG";
										     $wok="off";          //Indica que no se puede hacer la grabacion hasta que no se corrijan los valores
										    }
										  echo "<td align=RIGHT class='".$wcolor."'><INPUT TYPE='text' NAME='wresp[".$i."][".$j."]' VALUE='".$wresp[$i][$j]."' size=8 onchange='ModificarCuenta();'></td>";
										  $wtotres[$j]=$wtotres[$j] + $wresp[$i][$j];
									     }
									    else
									       {
										    if (($row[21]) >= ($wvalpac[$i]+$wvalemp[$i]+$wsumotr))  //Se puede facturar parcial, pero verifico que el valor sea igual o menor al total del cargo
						                      {
									           echo "<td align=RIGHT class='".$wcolor."'><INPUT TYPE='text' NAME='wresp[".$i."][".$j."]' VALUE='".$wresp[$i][$j]."' size=8 onchange='ModificarCuenta();'></td>";
										       $wtotres[$j]=$wtotres[$j] + $wresp[$i][$j];
									          }
									         else
					                            {
											     $wcolor="#GGGGGG";
										         $wok="off";
										        }
									       }
									 }
							   }

						   if (isset($wresp[$i][$j]))
						       $wregistro[$i][32+($j-1)]=$wresp[$i][$j];  //Facturado a c/u de los demas responsables
					      }
			        } //Fin del then de !isset($wreiniciar)

				 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				 if (isset($wreiniciar))  // *** INICIAR (Muestra la cuenta sin ningun cambio - tal como se grabo - ***
			        {
				     $wcanres=1;
				     echo "<input type='HIDDEN' name='wcanres' value='".$wcanres."'>";
				     $wrecemp1=100;
				     echo "<input type='HIDDEN' name='wrecemp1' value='".$wrecemp1."'>";


				     if ($row[22]=="E")
				        {
					     $wvalemp[$i]=0;
					     $wvalpac[$i]=($row[21]);
				         echo "<input type='HIDDEN' name='wvalpac[".$i."]' value='".$wvalpac[$i]."'>";
				         echo "<input type='HIDDEN' name='wvalemp[".$i."]' value='".$wvalemp[$i]."'>";
				         echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalpac[".$i."]' VALUE='".$wvalpac[$i]."' size=8 onchange='ModificarCuenta();'> </font></td>";
				         echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalemp[".$i."]' VALUE='".$wvalemp[$i]."' size=8 onchange='ModificarCuenta();'> </font></td>";
				         $wtotpac = $wtotpac+$wvalpac[$i];
				        }
				     if ($row[22]=="R")
				        {
					     $wvalpac[$i]=0;
					     $wvalemp[$i]=($row[21]);
					     echo "<input type='HIDDEN' name='wvalpac[".$i."]' value='".$wvalpac[$i]."'>";
				         echo "<input type='HIDDEN' name='wvalemp[".$i."]' value='".$wvalemp[$i]."'>";
				         echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalpac[".$i."]' VALUE='".$wvalpac[$i]."' size=8 onchange='ModificarCuenta();'> </font></td>";
				         echo "<td align=RIGHT class='".$wcolor."'><font size=2><INPUT TYPE='text' NAME='wvalemp[".$i."]' VALUE='".$wvalemp[$i]."' size=8 onchange='ModificarCuenta();'> </font></td>";
				         $wtotemp = $wtotemp+$wvalemp[$i];
				         $wtopemp1=0;
				         echo "<input type='HIDDEN' name='wtopemp1' value='".$wtopemp1."'>";
				        }
				     $wtopemp=0;
				     if ($wcanres > 1)
				        for ($j=2;$j<=$wcanres;$j++)
				           {
				            echo "<td align=RIGHT class='".$wcolor."'><INPUT TYPE='text' NAME='wresp[".$i."][".$j."]' VALUE='0' size=8 onchange='ModificarCuenta();'></td>";
				            $wresp[$i][$j]=0;

				            echo "<input type='HIDDEN' name='wresp[".$i."][".$j."]' value='".$wresp[$i][$j]."'>";
				            echo "<input type='HIDDEN' name='wtopemp[".$j."]' value='0'>";
				           }
			        }
			     /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
				 /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			     if (isset($j))
			        $wtotal_columnas_matriz=32+($j-1);
			       else
			          $wtotal_columnas_matriz=32;

			     echo "</tr>";

			     $wtotcta=$wtotcta+($row[21]);

			     //ACA AVERIGUO SI EL CONCEPTO ES PROPIO O COMPARTIDO, PORQUE LOS DESCUENTOS GENERALES (DEL ENCABEZADO) SOLO SE APLICAN SOBRE CONCEPTOS PROPIOS
				 $q= " SELECT grutip "
				    ."   FROM ".$tablaConceptos." "
				    ."  WHERE grucod = '".$wregistro[$i][13]."'";
				 $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	             $num1 = mysql_num_rows($res1);
	             if ($num1 > 0)
	                {
	                 $row1 = mysql_fetch_array($res1);
	                 $wtipcon = $row1[0];
                    }


                 //para el descuento en la columna particular                           //M A Y O  11  DE 2007
                 if ((isset($wvalpac[$i]) and $wvalpac[$i] > 0 and $wtipcon == "P" ) or $wdesc_gral=="on")		   //Que tenga valor y sea propio y sea de la cuenta del *** paciente *** o que sea de empresa con descuento por empresa
                    {
	                 if (isset($wvaldesemp))
	                    {
                         $wvaldesemp = $wvaldesemp+(abs($wvalemp[$i])*($wdscto/100));    //sumo los descuentos
	                     $wpasodescgral="on";
                        }
	                }

	             $wcodemp=explode("-",$wregistro[$i][6]);

	             //AVERIGUO SI EL TIPO DE EMPRESA ES PARTICULAR
	             $q = " SELECT emptem "
	                 ."   FROM ".$wbasedato."_000024 "
	                 ."  WHERE empcod = '".$wcodemp[0]."'"
	                 ."    AND empest = 'on' ";
	             $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	             $num1 = mysql_num_rows($res1);
	             $row1 = mysql_fetch_array($res1);

	             //Esto es para el descuento en la columna de responsable cuando es Particular el reponsable principal. //M A Y O  11  DE 2007
	             if (((isset($wvalemp[$i]) and $wvalemp[$i] > 0 and $wtipcon == "P" and $row1[0] == "01-PARTICULAR") or $wdesc_gral=="on") and $wpasodescgral=="off")     //Que tenga valor y sea propio y sea de la cuenta del *** paciente ***  o que sea de empresa con descuento por empresa
	                if (isset($wvaldesemp)) $wvaldesemp = $wvaldesemp+(abs($wvalemp[$i])*($wdscto/100));  //sumo los descuentos

                 //=====================================================================================
	             //ACA AVERIGUO SI EL CONCEPTO TIENE DESCUENTO PARA LA ** EMPRESA ** QUE ESTA FACTURANDO
				 $q= " SELECT conempdes, conemprec "
				    ."   FROM ".$wbasedato."_000117 "
				    ."  WHERE conempcon = '".$wregistro[$i][13]."'"
				    ."    AND conempemp = '".$wcodemp[0]."'"
				    ."    AND conempest = 'on' ";
				 $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	             $num1 = mysql_num_rows($res1);

	             if ($num1 > 0)
	                {
	                 $row1 = mysql_fetch_array($res1);
	                 $wcon_dcto = ($row1[0]/100);
	                 $wcon_reca = ($row1[0]/100);

	                 $wvaldesemp=$wvaldesemp+($wvalemp[$i]*($row1[0]/100));
	                }
                }

              //LINEA DE SUBTOTALES
			  echo "<tr>";
	          echo "<td align=RIGHT class=fila1 colspan=11><font size=4><b>SubTotales</b></font></td>";
	          echo "<td align=RIGHT class=fila1 colspan=1><b><font size=4>".number_format($wtotcta,0,'.',',')."</font></b></td>";
	          echo "<td align=RIGHT class=fila1 colspan=1><b><font size=4>".number_format($wtotpac,0,'.',',')."</font></b></td>";
	          echo "<td align=RIGHT class=fila1 colspan=1><b><font size=4>".number_format($wtotemp,0,'.',',')."</font></b></td>";
	          if ($wcanres > 1)
	             {
		          for ($j=2;$j<=$wcanres;$j++)
		             {
		              echo "<td align=RIGHT class=fila1 colspan=1><b><font size=4>".number_format($wtotres[$j],0,'.',',')."</font></b></td>";
		              $wtotgenres=$wtotres[$j]; //Aca sumo los totales de todos los responsables adicionales
	                 }
                 }
		     	else
		     	   $wtotgenres=0;
		      echo "</tr>";

		      //LINEA DE DESCUENTO
		      echo "<tr>";
	          echo "<td align=RIGHT class=fila1 colspan=11><font size=4><b>Descuento</b></font></td>";
	          echo "<td align=RIGHT class=fila1 colspan=1><b><font size=4>".number_format($wvaldto+$wvaldesemp,0,'.',',')."</font></b></td>";
	          echo "<td align=RIGHT class=fila1 colspan=1><b><font size=4>".number_format($wvaldto,0,'.',',')."</font></b></td>";
	          echo "<td align=RIGHT class=fila1 colspan=1><b><font size=4>".number_format($wvaldesemp,0,'.',',')."</font></b></td>";
	          //echo "<td align=RIGHT class=fila1 colspan=1>&nbsp</td>";
	          if ($wcanres > 1)
	             for ($j=2;$j<=$wcanres;$j++)
		             echo "<td align=RIGHT class=fila1 colspan=1>&nbsp</td>";
		      echo "</tr>";

		      //LINEA DE TOTALES
			  echo "<tr>";
	          echo "<td align=RIGHT class=fila1 colspan=11><font size=4><b>Totales</b></font></td>";
	          echo "<td align=RIGHT class=fila1 colspan=1><b><font size=4>".number_format($wtotcta-$wvaldto-$wvaldesemp,0,'.',',')."</font></b></td>";
	          echo "<td align=RIGHT class=fila1 colspan=1><b><font size=4>".number_format($wtotpac-$wvaldto,0,'.',',')."</font></b></td>";
	          echo "<td align=RIGHT class=fila1 colspan=1><b><font size=4>".number_format($wtotemp-$wvaldesemp,0,'.',',')."</font></b></td>";
	          if ($wcanres > 1)
	             {
		          for ($j=2;$j<=$wcanres;$j++)
		             {
		              echo "<td align=RIGHT class=fila1 colspan=1><b><font size=4>".number_format($wtotres[$j],0,'.',',')."</font></b></td>";
		              $wtotgenres=$wtotres[$j]; //Aca sumo los totales de todos los responsables adicionales
	                 }
                 }
		     	else
		     	   $wtotgenres=0;
		      echo "</tr>";
		    }
		   ELSE
		     //INICIALIZAR LA FORMA
		     {
			  echo "</table>";
			  echo "<table>";
			  $whay_registros="N";
			  echo "<tr><td colspan=6><font size=4><b>NO SE ENCONTRARON RESGISTROS PARA FACTURAR PARA ESTE NUMERO DE HISTORIA Y NUMERO DE INGRESO</b></font></td></tr>";
		     }
	   }


  //===========================================================================================================================================
  //===========================================================================================================================================
  //===========================================================================================================================================
  //INICIO DEL PROGRAMA
  //===========================================================================================================================================
  //===========================================================================================================================================
  //===========================================================================================================================================

  $whay_registros="S";

  $wok="on";
  $wfalta_paq="off";

  $wno_facturar = array();
  //$wcuadre="S";

  $wcf="fila2";   //COLOR DEL FONDO    -- Gris claro
  $wcf2="encabezadotabla";  //COLOR DEL FONDO 2  -- Azul claro
  $wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
  $wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro

  encabezado("F A C T U R A C I O N &nbsp&nbsp D E &nbsp&nbsp H I S T O R I A S",$wactualiz, "clinica");

  //echo "<p align=right><font size=1><b>Version: ".$wactualiz." &nbsp&nbsp&nbsp Autor: ".$wautor."</b></font></p>";
  //===========================================================================================================================================
  //ACA COMIENZA EL ENCABEZADO DE LA VENTA
  echo "<center><table >";
  // echo "<tr><td align=center rowspan=2 colspan=1><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=300 HEIGHT=100></td></tr>";
  // echo "<tr><td align=center colspan=6 class=".$wcf2."><font size=6 text color=#FFFFFF><b>F A C T U R A C I O N &nbsp&nbsp D E &nbsp&nbsp H I S T O R I A S</b></font></td></tr>";

  if (isset($whis))
     {
      $whistoria=$whis;
      echo "<input type='HIDDEN' name='whistoria' value='".$whistoria."'>";
     }

  if (!isset($whisant))
     $whisant="";

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //HISTORIA CLINICA
  //*************************************************************************************************************************************************************************************************************************************************************************************
  if (isset($whistoria)) //Si ya fue digitado el documento del cliente
     {
	  if ($whistoria != "")
         {
	      $q= "SELECT MAX(ingnin+0) "
             ."  FROM ".$wbasedato."_000100, ".$wbasedato."_000101 "
             ." WHERE pachis = '".$whistoria."'"
             ."   AND pachis = inghis ";
	      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	      $num1 = mysql_num_rows($res1);
	      $row1 = mysql_fetch_array($res1);
	      $wwing=$row1[0];

	      if (isset($wwing) and $wwing != "")
	         {
		      if (isset($wing) and $wing!="" and $wing!=$wwing)
		         $wwing=$wing;

		      $q= "SELECT pachis, pacno1, pacno2, pacap1, pacap2, pacdoc, ingcem, empnit, ingent, ingfei, ingsei, ingnin, empres, emptem, emptar, emppdt "
		         ."  FROM ".$wbasedato."_000100, ".$wbasedato."_000101, ".$wbasedato."_000024 "
		         ." WHERE pachis = '".$whistoria."'"
		         ."   AND pachis = inghis "
		         ."   AND ingnin = ".$wwing
		         ."   AND ingcem = empcod ";
		      $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		      $num1 = mysql_num_rows($res1);

		      if ($num1 > 0)
		         {
			      $row1 = mysql_fetch_array($res1);
		          $whis =$row1[0];
		          if (!isset($wing) or ($wing == ""))
		             $wing=$row1[11];
		          $wno1 =$row1[1];
		          $wno2 =$row1[2];
		          $wap1 =$row1[3];
		          $wap2 =$row1[4];
		          $wdoc =$row1[5];
		          $wcodemp=$row1[6];
		          $wnitemp=$row1[7];
		          $wnomemp=$row1[8];
		          $wfec=$row1[9];
		          $wser=$row1[10];
		          $wempres=$row1[12];
		          $wtipcli=$row1[13];
		          $wtar=$row1[14];



		          //================================================================================================================================
		          //M A Y O  11  DE 2007
		          //================================================================================================================================
		          $wdesc_gral="off";   //Indica que el descuento que se muestra en pantalla viene del maestro de empresas, por la tanto el descuento
		                               //es general osea que se aplica para conceptos propios y compartidos.
		          if ($row1[15]>0)
		             {
		              $wdscto=$row1[15];
		              $wdesc_gral="on";
	                 }
		          //================================================================================================================================

		          if (isset($whisant) and ($whisant != $whis))
		             {
	                  $whisant=$whistoria;
	                  $wdoc_otro = $wdoc;
	                  $wnom_otro = $wno1." ".$wno2." ".$wap1." ".$wap2;
                     }
                 }
		     }
            else
               {
		        $whis="";
		        $wing="";
		        $wno1="";
		        $wno2="";
		        $wap1="";
		        $wap2="";
		        $wdoc="";
		        $wcodemp="";
		        $wnitemp="";
		        $wnomemp="";
		        $wfec="";
		        $wser="";
		        $wempres="";
		        $wtipcli="";
		        $wtar="";
	           }
	      echo "<input type='HIDDEN' name='whisant' value='".$whisant."'>";
	      echo "<input type='HIDDEN' name='wing' value='".$wing."'>";
          echo "<input type='HIDDEN' name='wno1' value='".$wno1."'>";
          echo "<input type='HIDDEN' name='wno2' value='".$wno2."'>";
          echo "<input type='HIDDEN' name='wap1' value='".$wap1."'>";
          echo "<input type='HIDDEN' name='wap2' value='".$wap2."'>";
          echo "<input type='HIDDEN' name='wdoc' value='".$wdoc."'>";
          echo "<input type='HIDDEN' name='wnitemp' value='".$wnitemp."'>";
          echo "<input type='HIDDEN' name='wnomemp' value='".$wnomemp."'>";
          echo "<input type='HIDDEN' name='wfec' value='".$wfec."'>";
          echo "<input type='HIDDEN' name='wser' value='".$wser."'>";
	      echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg."> Historia:<br> </font></b><INPUT TYPE='text' NAME='whistoria' VALUE='".$whistoria."' onchange='enter()'></td>";   //whistoria
	     }
        else
           echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg."> Historia:<br> </font></b><INPUT TYPE='text' NAME='whistoria' VALUE='".$whistoria."' onchange='enter()'></td>";  //whistoria
     }
    else
       echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg."> Historia:<br> </font></b><INPUT TYPE='text' NAME='whistoria' onchange='enter()'></td>";                           //whistoria

  //*************************************************************************************************************************************************************************************************************************************************************************************

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //INGRESO NRO:
  if (isset($wing))
     {
	  $q = " SELECT count(*) "
	      ."   FROM ".$wbasedato."_000101 "
	      ."  WHERE inghis = '".$whistoria."'"
	      ."    AND ingnin = '".$wing."'";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $row = mysql_fetch_array($res);
      if ($row[0] == 0)
         {
	      ?>
		    <script>
		      alert ("LA HISTORIA CON ESTE NUMERO DE INGRESO NO EXISTE");
              function ira(){document.cargos.wing.focus();}
		    </script>
		  <?php

		 echo "<td align=left class=".$wcf."><b><font text color=".$wclfg.">Ingreso Nro:<br> </font><INPUT TYPE='text' NAME='wing' value='' onchange='enter()'></b></td>";
         }
        else
	      echo "<td align=left class=".$wcf."><b><font text color=".$wclfg.">Ingreso Nro:<br> </font></b><INPUT TYPE='text' NAME='wing' VALUE='".$wing."' onchange='enter()'></td>";
     }
    else
       echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg."> Ingreso Nro:<br> </font></b><INPUT TYPE='text' NAME='wing' onchange='enter()'></td>";                           //whistoria
       //echo "<td align=left class=".$wcf."><b><font text color=".$wclfg.">Ingreso Nro:<br> </font></b>&nbsp</td>";
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //FECHA_ING:
  if (isset($wfec))
     echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">Fecha Ing:<br> </font>".$wfec."</b></td>";
    else
       echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">Fecha Ing:<br> </font></b>&nbsp</td>";
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //SERVICIO DE INGRESO
  if (isset($wser) and $wser !='')
     {
	  $q= " SELECT ccodes FROM ".$wbasedato."_000003  WHERE ccocod = '".$wser."'";
	  $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num1 = mysql_num_rows($res1);
	  if ($num1 > 0)
	     {
	      $row1 = mysql_fetch_array($res1);
	      $wsernom=$row1[0];
         }
	  if (isset($wsernom)) echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">Servicio de Ingreso:<br> </font>".$wsernom."</b></td>";
     }
    else
       echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">Servicio de Ingreso:<br> </font></b>&nbsp</td>";

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //PACIENTE:
  if (isset($wno1))
     echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">Paciente:<br> </font>".$wno1."&nbsp".$wno2."&nbsp".$wap1."&nbsp".$wap2."&nbsp"."</b></td>";
    else
       echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">Paciente:<br> </font></b>&nbsp</td>";

  echo "</tr>";

  echo "<tr>";

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //DOCUMENTO:
  if (isset($wdoc))
     echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">Documento: </font>".$wdoc."</b></td>";
    else
       echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">Documento: </font></b>&nbsp</td>";

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //SERVICIO ACTUAL:
  if (isset($wservicio))
     echo "<td align=left class=".$wcf." colspan=2><b><font text color=".$wclfg.">Servicio: </font>".$wnomcco."</b></td>";
    else
       echo "<td align=left class=".$wcf." colspan=2><b><font text color=".$wclfg.">Servicio: </font>".$wnomcco."</b></td>";

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //CANTIDAD DE RESPONSABLES:
  if (isset($wcanres) and $wcanres > 1 and !isset($wreiniciar))
     echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg."> Cant.Resp.: </font></b><INPUT TYPE='text' NAME='wcanres' VALUE='".$wcanres."' onchange='enter()'></td>";   //whistoria
    else
       echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">Cant.Resp.: </font></b><INPUT TYPE='text' NAME='wcanres' VALUE='1' onchange='enter()'></td>";

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //DESCUENTO:
  if (isset($wdscto) and $wdscto <= 100)
     echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">% de Descuento: </font></b><INPUT TYPE='text' NAME='wdscto' VALUE='".$wdscto."' onchange='enter()'></td>";
    else
       echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">% de Descuento: </font></b><INPUT TYPE='text' NAME='wdscto' VALUE='0' onchange='enter()'></td>";

  echo "</tr>";

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //OTRO RESPONSABLE:
  echo "<tr>";
  if (isset($wdoc_otro))
     echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">Dcto Otro Responsable:</font><INPUT TYPE='text' NAME='wdoc_otro' VALUE='".$wdoc_otro."'></b></td>";
    else
       if (isset($wdoc)) echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">Dcto Otro Responsable:</font></b><INPUT TYPE='text' NAME='wdoc_otro' VALUE='".$wdoc."'></td>";

  if (isset($wnom_otro))
     echo "<td align=left class=".$wcf." colspan=4><b><font text color=".$wclfg.">Nombre Otro Responsable:</font><INPUT TYPE='text' NAME='wnom_otro' VALUE='".$wnom_otro."' size=100></b></td>";
    else
       if (isset($wno1) and isset($wno2) and isset($wap1) and isset($wap2)) echo "<td align=left class=".$wcf." colspan=4><b><font text color=".$wclfg.">Nombre Otro Responsable:</font></b><INPUT TYPE='text' NAME='wnom_otro' VALUE='".$wno1." ".$wno2." ".$wap1." ".$wap2."' size=100></td>";
  echo "</tr>";

  echo "<tr>";
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //RESPONSABLE:
  if (isset($wnitemp) and isset($wcodemp) and $wcodemp != "")
     {
      echo "<td align=left class=".$wcf." colspan=2><b><font text color=".$wclfg.">Responsable : </font>".$wcodemp."-".$wnitemp."-".$wnomemp."</b></td>";
      $q = "SELECT tarcod, tardes "
          ."  FROM ".$wbasedato."_000024, ".$wbasedato."_000025 "
          ." WHERE empcod                            = '".$wcodemp."'"
          ."   AND mid(emptar,1,instr(emptar,'-')-1) = tarcod ";
      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
      $row = mysql_fetch_array($res);
      echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">Tarifa : </font><font size=2>".$row[0]."-".$row[1]."</font></b></td>";
     }
    else
       echo "<td align=left class=".$wcf." colspan=3><b><font text color=".$wclfg.">Responsable : </font></b>&nbsp</td>";

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //% RECONOCIDO EMPRESA 1:
  if (isset($wrecemp1) and $wrecemp1 <= 100)
     echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">% Reconocido: </font></b><INPUT TYPE='text' NAME='wrecemp1' VALUE='".$wrecemp1."' onchange='enter()'></td>";
    else
       echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">% Reconocido: </font></b><INPUT TYPE='text' NAME='wrecemp1' VALUE='100' onchange='enter()'></td>";

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //TOPE EMPRESA 1:
  if (isset($wtopemp1))
     echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">Tope: </font></b><INPUT TYPE='text' NAME='wtopemp1' VALUE='".$wtopemp1."' onchange='enter()'></td>";
    else
       echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">Tope: </font></b><INPUT TYPE='text' NAME='wtopemp1' VALUE='0' onchange='enter()'></td>";

  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //OTROS RESPONSABLES
  if (isset($wcanres) and $wcanres > 1 and !isset($wreiniciar))
     for ($j=2;$j<=$wcanres;$j++)
	     {
	      echo "<tr>";
	      $q =  " SELECT empcod, empnit, empnom "
	           ."   FROM ".$wbasedato."_000024, ".$wbasedato."_000029 "
		       ."  WHERE empest = 'on' "
			   ."    AND mid(emptem,1,instr(emptem,'-')-1) = temcod "
			   ."    AND temche = 'off' "
			   ."  ORDER BY empcod ";
	      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
		  $num = mysql_num_rows($res);

		  echo "<td align=left class=".$wcf." colspan=2><b><font text color=".$wclfg.">Responsable ".$j.":</font></b><select name='wrespon[".$j."]' onchange='enter()' >";

		  if (isset($wrespon[$j]))
		     echo "<option selected>".$wrespon[$j]."</option>";
		    else
		       echo "<option selected>01-".$wdoc."-".$wno1."&nbsp".$wno2."&nbsp".$wap1."&nbsp".$wap2."</option>";

		  for ($i=1;$i<=$num;$i++)
		     {
		      $row = mysql_fetch_array($res);
		      echo "<option>".$row[0]." - ".$row[1]." - ".$row[2]."</option>";
		     }
		  echo "</select></td>";

		  if (isset($wrespon[$j]))
		     {
			  $wcodres=explode("-",$wrespon[$j]);

			  $q = "SELECT tarcod, tardes "
	              ."  FROM ".$wbasedato."_000024, ".$wbasedato."_000025 "
	              ." WHERE empcod                            = '".$wcodres[0]."'"
	              ."   AND mid(emptar,1,instr(emptar,'-')-1) = tarcod ";
	          $res_tar = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	          $row_tar = mysql_fetch_array($res_tar);
	          if ($row_tar > 0)
	             echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg." >Tarifa : </font> <font size=2>".$row_tar[0]."-".$row_tar[1]."</font></b></td>";
	            else
	               echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg." >Tarifa : </font> <font size=2>&nbsp</font></b></td>";
             }

		  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		  //% RECONOCIDO OTRAS EMPRESAS:
		  if (isset($wrecemp[$j]) and ($wrecemp[$j] <= 100))
		     echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">% Reconocido: </font></b><INPUT TYPE='text' NAME='wrecemp[".$j."]' VALUE='".$wrecemp[$j]."' onchange='enter()'></td>";
	        else
		       {
			    $wrecemp[$j]=0;
		        echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">% Reconocido: </font></b><INPUT TYPE='text' NAME='wrecemp[".$j."]' VALUE='0' onchange='enter()'></td>";
	           }

		  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		  //TOPE OTRAS EMPRESAS:
		  if (isset($wtopemp[$j]))
		     echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">Tope: </font></b><INPUT TYPE='text' NAME='wtopemp[".$j."]' VALUE='".$wtopemp[$j]."' onchange='enter()'></td>";
		    else
		       echo "<td align=left class=".$wcf." colspan=1><b><font text color=".$wclfg.">Tope: </font></b><INPUT TYPE='text' NAME='wtopemp[".$j."]' VALUE='0' onchange='enter()'></td>";

		  echo "</tr>";
	     }

  echo "</tr>";
  echo "</table>";
  echo "<br>";


  /////////////////////////////////////////////////
  //Aca busco si esta historia tiene algun paquete
  if (isset($whistoria) and isset($wing) and $wing != "")
     {
	  $q = "SELECT movpaqcod "
	      ."  FROM ".$wbasedato."_000115 "
	      ." WHERE movpaqhis = ".$whistoria
	      ."   AND movpaqing = ".$wing
	      ."   AND movpaqest = 'on' ";
	  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
	  $num = mysql_num_rows($res);

	  if ($num > 0)
	     {
	      $row = mysql_fetch_array($res);
	      verifica_paquete($whistoria, $wing, $row[0]);

	      if ($wfalta_paq=="on")
	         {
			  ?>
			   <script>
			     alert ("** ALERTA ** FALTAN CARGOS DEL PAQUETE POR GRABAR");
	           </script>
			  <?php
		     }
	     }
     }

  if (isset($whistoria)) mostrar();


  if (!isset($wdoc_otro) or !isset($wnom_otro) or trim($wdoc_otro) == "" or trim($wnom_otro) == "")
	$wok="off";


  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //==================================================================================================================================
  ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //ACA SE GRABAN LAS FACTURAS
  //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  if (isset($wgrabar))
     {
	  if (isset($wok) and $wok=="on" and $wfalta_paq=="off" and trim($wcco) != "" and trim($wusuario) != "")
	     {
		  $q = "SELECT ccopfa, ccoffa, ccofai "
				   ."  FROM ".$wbasedato."_000003 "
				   ." WHERE ccocod='".$wcco."'";
		  $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

		  $row = mysql_fetch_array($err);
		  $wprefac   =$row[0];             //Prefijo
		  $wfueffa   =$row[1];             //Fuente
		  $wnrofac   =$row[2];             //Factura con el consecutivo

		  //$wgenfac="N";

		  if (is_numeric($wnrofac) and ($wprefac!="") and strtoupper($wprefac)!="NO APLICA" and ($wfueffa!="") and strtoupper($wfueffa)!="NO APLICA")
		     {
			  $wgenfac="S"; //Indica que si se puede generar facturas para el centro de costos

			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  //ACA TRAIGO LOS DATOS NECESARIOS PARA IMPRIMIR LA FACTURA DESDE LA TABLA DE CONFIGURACION
			  $q = " SELECT cfgnit, cfgnom, cfgtre, cfgtel, cfgdir, cfgfran, cfgfian, cfgffan, cfgran, cfgfcar, cfgfrac, cfgfiac, cfgffac, cfgrac, cfgpin, cfgmai, cfgdom "
			      ."   FROM ".$wbasedato."_000049 "
			      ."  WHERE cfgcco = '".$wcco."'";
			  $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
			  $row = mysql_fetch_array($res);

			  $wnit_pos  =$row[0];
			  $wnomemppos=$row[1];
			  $wtipregiva=$row[2];
			  $wtel_pos  =$row[3];
			  $wdir_pos  =$row[4];

			  if ($row[9] > $wfecha)
			     {
			      $wnrores=$row[8];  //Nro Resolucion Anterior
			      $wfecres=$row[5];  //Fecha Resolucion Anterior
			      $wfacini=$row[6];  //Factura Inicial Anterior
			      $wfacfin=$row[7];  //Factura Final Anterior
			     }
			    else
			       {
			        $wnrores=$row[13];  //Nro Resolucion Actual
				    $wfecres=$row[10];  //Fecha Resolucion Actual
				    $wfacini=$row[11];  //Factura Inicial Anterior
				    $wfacfin=$row[12];  //Factura Final Anterior
				   }
			  $wpagintern=$row[14];
			  $wemail_pos=$row[15];
			  $wteldompos=$row[16];

			  /*
			  $wresolucion= "Resolución Nro: ".$wnrores." del ".$wfecres.", <BR>"
		                   ."factura ".$wfacini." a la factura ".$wfacfin."<BR>"
		                   ."Esta factura cambiaria de compraventa se asimila en <BR>"
		                   ."todos sus efectos a una letra de cambio, Art. 621 y <BR>"
		                   ."SS, 671 y SS 772, 773, 770 y SS del código de comercio.<BR>"
		                   ."Factura impresa por computador cumpliendo con los<BR>"
		                   ."requisitos del Art. 617 del E.T.<BR>";
		      */

		      $wresolucion= "Documento oficial de autorización de numeración: ".$wnrores." del ".$wfecres.", "
		                   ."factura ".$wfacini." a la factura ".$wfacfin."."
		                   ."Esta factura cambiaria de compraventa se asimila en "
		                   ."todos sus efectos a una letra de cambio, Art. 621 y "
		                   ."SS, 671 y SS 772, 773, 770 y SS del código de comercio.<BR>"
		                   ."Factura impresa por computador cumpliendo con los "
		                   ."requisitos del Art. 617 del E.T.<BR>";

		      /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	   		  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
			  /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

			  //=====================================================
		      ///////////////////////////////////////////////////////
		      // ** EN EL SIGUIENTE PROCEDIMIENTO ORDENA LA MATRIZ **
		      //=====================================================
		      @usort($wregistro,"ordenar");

			  for ($i=0;$i<$wreg_matriz;$i++)
			     {
			      $h=$i+1;
			      for ($k=1;$k<=$wtotal_columnas_matriz;$k++)
			         {
				      if (isset($wregistro[$i][$k]))
			             $wregistro_1[$h][$k]=$wregistro[$i][$k];
		             }
			     }

			  $wtiene_cargos_pac="off";   //Estas variables indican si la cuenta del paciente tiene cargos por facturar sin importar que
			  $wtiene_cargos_emp="off";   //genera facturas en cero, debido a algun abono
			  for ($i=1;$i<=$wreg_matriz;$i++)
			     {
			      for ($k=1;$k<=$wtotal_columnas_matriz;$k++)
			         {
			          if (isset($wregistro_1[$i][$k]))
			             $wregistro[$i][$k]=$wregistro_1[$i][$k];

			          if (isset($wregistro[$i][31]) and $wregistro[$i][31] <> 0)    //Valor del cargo
			             $wtiene_cargos_pac="on";
			          if (isset($wregistro[$i][32]) and $wregistro[$i][32] <> 0)    //Valor del cargo
			             $wtiene_cargos_emp="on";
			         }
			     }

			  for ($j=0;$j<=$wcanres;$j++)
			    {
				 //===================================================================================================================================
				 //Aca vuelvo a evaluar la numeración, porque cuando no existe cuenta para el paciente, se debe colocar la variable $wgenfac="N", para
				 //que no trate de grabar la factura mas abajo, entonces debo evaluar por cada responsable para volver a habilitar $wgenfac o no.
				 $q = "SELECT ccopfa, ccoffa, ccofai "
				   ."  FROM ".$wbasedato."_000003 "
				   ." WHERE ccocod='".$wcco."'";
				 $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				 $row = mysql_fetch_array($err);
				 $wprefac   =$row[0];             //Prefijo
				 $wfueffa   =$row[1];             //Fuente
				 $wnrofac   =$row[2];             //Factura con el consecutivo

				 $wgenfac="N";

				 if (is_numeric($wnrofac) and ($wprefac!="") and strtoupper($wprefac)!="NO APLICA" and ($wfueffa!="") and strtoupper($wfueffa)!="NO APLICA")
				    $wgenfac="S";
				 //===================================================================================================================================

				 //ENTRO SI LA CUENTA DEL PACIENTE ES MAYOR O IGUAL CERO (0)
			     if ($j==0 and $wtiene_cargos_pac=="on" and $wgenfac=="S")           // FACTURA PARTICULAR (DEL PACIENTE) //
				    {
					 //===================================================================================================================================
				     //ACTUALIZO LA NUMERACION
					 $q = "lock table ".$wbasedato."_000003 LOW_PRIORITY WRITE";
					 $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					 $wnrofac="";

					 $q =  " UPDATE ".$wbasedato."_000003 "
					      ."    SET ccofai = ccofai + 1 "          //Consecutivo Factura Automatica Inicial
					      ."  WHERE ccocod = '".$wcco."'"
					      ."    AND ccofai < ccofaf ";
					 $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					 $q = "SELECT ccopfa, ccoffa, ccofai "         //Prefijo Factura Automatica, Fuenta Factura Automatica, Consecutivo Factura Automatica,
					     ."  FROM ".$wbasedato."_000003 "
					     ." WHERE ccocod='".$wcco."'";
					 $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					 $row = mysql_fetch_array($err);
					 $wnrofac   =$row[0]."-".$row[2];              //Prefijo Factura con el consecutivo
					 $wfueffa   =$row[1];                          //Fuente Factura

					 $q = " UNLOCK TABLES";
					 $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					 //TRAIGO EL TIPO DE EMPRESA PARA LA EMPRESA PARTICULAR
					 $q = "  SELECT empcod, empnit, empnom, empres, emptem "
					     ."    FROM ".$wbasedato."_000024 "
					     ."   WHERE empnom = 'PARTICULAR'";
					 $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					 $row = mysql_fetch_array($err);


					 $wwtipcli   = $row[4];
				     $wwnitemp   = $row[1];
				     $wwcodemp   = $row[0];
				     $wwempres   = $row[3];
				     $wwtotal    = $wtotpac-$wvaldto;
				     $wwiva      = 0;
				     $wwcopago   = 0;
				     $wwcuotamod = 0;
				     $wwvaldto   = $wvaldto;
				     $wwabonos   = 0;
				     $wwnd       = 0;
				     $wwnc       = 0;
				     $wwsaldo    = $wtotpac-$wvaldto;
				     $wwdscto    = $wdscto;     //% de descuento
				     $wwrecono   = 0;
				     $wwtope     = $wtopemp1;
				     $wwdocpac   = strtoupper($wdoc_otro);
				     $wwnompac   = strtoupper($wnom_otro);

				     $wfactura[$j]=$wfueffa."-".$wnrofac;
				    }
				   else
				      if ($wtiene_cargos_pac=="off" and $j==0) $wgenfac="N";


				if ($j==1 and $wtiene_cargos_emp=="on" and $wgenfac=="S")            //CUENTA DEL RESPONSABLE PRINCIPAL
				   {
					//===================================================================================================================================
				    //ACTUALIZO LA NUMERACION
				    $q = "lock table ".$wbasedato."_000003 LOW_PRIORITY WRITE";
					$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					$wnrofac="";

					$q =  " UPDATE ".$wbasedato."_000003 "
					     ."    SET ccofai = ccofai + 1 "         //Consecutivo Factura Automatica Inicial
					     ."  WHERE ccocod = '".$wcco."'"
					     ."    AND ccofai < ccofaf ";
					$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					$q = "SELECT ccopfa, ccoffa, ccofai "
					   ."  FROM ".$wbasedato."_000003 "
					   ." WHERE ccocod='".$wcco."'";
					$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					$row = mysql_fetch_array($err);
					$wnrofac   =$row[0]."-".$row[2];             //Prefijo Factura con el consecutivo
					$wfueffa   =$row[1];                         //Fuente Factura

					$q = " UNLOCK TABLES";
					$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					$wwtipcli    = $wtipcli;
				    $wwnitemp    = $wnitemp;
				    $wwcodemp    = $wcodemp;
				    $wwempres    = $wempres;
				    $wwtotal     = $wtotemp-$wvaldesemp;
				    $wwiva		 = 0;
				    $wwcopago    = 0;
				    $wwcuotamod  = 0;
				    $wwvaldto    = $wvaldesemp;
				    $wwabonos    = 0;
				    $wwnd        = 0;
				    $wwnc        = 0;
				    $wwsaldo     = $wtotemp-$wvaldesemp;
				    $wwdscto     = $wdscto;             //% de descuento
				    $wwrecono    = $wrecemp1;
				    $wwtope      = $wtopemp1;
				    $wwdocpac    = strtoupper($wdoc_otro);
				    $wwnompac    = strtoupper($wnom_otro);

				    $wfactura[$j]=$wfueffa."-".$wnrofac;
				   }
				  else
				     if ($wtiene_cargos_emp=="off" and $j==1) $wgenfac="N";


				if (($j >= 2) and (isset($wtotres[$j])) and $wgenfac=="S")
				   {
				    //===================================================================================================================================
				    //ACTUALIZO LA NUMERACION
				    $q = "lock table ".$wbasedato."_000003 LOW_PRIORITY WRITE";
					$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					$wnrofac="";

					$q =  " UPDATE ".$wbasedato."_000003 "
					     ."    SET ccofai = ccofai + 1 "         //Consecutivo Factura Automatica Inicial
					     ."  WHERE ccocod = '".$wcco."'"
					     ."    AND ccofai < ccofaf ";
					$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					$q = "SELECT ccopfa, ccoffa, ccofai "         //Prefijo Factura Automatica, Fuenta Factura Automatica, Consecutivo Factura Automatica,
					   ."  FROM ".$wbasedato."_000003 "
					   ." WHERE ccocod='".$wcco."'";
					$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					$row = mysql_fetch_array($err);
					$wnrofac   =$row[0]."-".$row[2];              //Prefijo Factura con el consecutivo
					$wfueffa   =$row[1];                          //Fuente Factura

					$q = " UNLOCK TABLES";
					$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

					$wcodres=explode("-",$wrespon[$j]);

					//TRAIGO EL TIPO DE EMPRESA
					$q = "  SELECT empcod, empnit, empres, emptem "
					    ."    FROM ".$wbasedato."_000024 "
					    ."   WHERE empcod = '".$wcodres[0]."'";
					$err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					$row = mysql_fetch_array($err);

					$wwtipcli      = $row[3];
				    $wwnitemp      = $row[1];
				    $wwcodemp      = $row[0];
				    $wwempres      = $row[2];
				    $wwtotal       = $wtotres[$j];
				    $wwiva		   = 0;
				    $wwcopago      = 0;
				    $wwcuotamod    = 0;
				    $wwvaldto      = 0;
				    $wwabonos      = 0;
				    $wwnd          = 0;
				    $wwnc          = 0;
				    $wwsaldo       = $wtotres[$j];
				    $wwdscto       = $wdscto;     //% de descuento
				    $wwrecono      = $wrecemp[$j];
				    $wwtope        = $wtopemp[$j];
				    $wwdocpac      = strtoupper($wdoc_otro);
				    $wwnompac      = strtoupper($wnom_otro);


				    $wfactura[$j]=$wfueffa."-".$wnrofac;
				   }
				  else
				     if (isset($wtotres[$j]) and $j>=2) $wgenfac="N";


				//Abril 2 de 2013
				//Se busca si se puede generar para un particular una factura radicada
				$esCrearFacturaGenerada = consultarAliasPorAplicacion( $conex, $wemp_pmla, "facturaRadicadaParticular" );
				$esCrearFacturaGenerada = strtolower( $esCrearFacturaGenerada ) == 'on' ? true: false;

				//Marzo 5 de 2013
				//Si es particular la factura se genera como radicada (RD)
				//de lo contrario se genera como generada (GE)
				if( $esCrearFacturaGenerada && $wwcodemp == $wwempres && $wwempres == '01' ){
					$westFactura = 'RD';
				}
				else{
					$westFactura = 'GE';
				}

				if ($wgenfac=="S")
			       {
			        //************************************************************************************
			        //ACA GRABO EN LAS TABLAS DE FACTURACION DE ACUERDO A COMO QUEDO DISTRIBUIDA LA CUENTA
			        //************************************************************************************

			        //ENCABEZADO DE LAS FACTURAS
			        $q= " INSERT INTO ".$wbasedato."_000018 (Medico    ,"
			           ."                                    Fecha_data,"
			           ."                                    Hora_data ,"
			           ."                                    fenano    ,"                                	//Año
			           ."                                    fenmes    ,"  							  	    //Mes
			           ."                                    fenfec    ," 								  	//Fecha
			           ."                                    fenffa    ,"   							  	//Fuente Factura
			           ."                                    fenfac    ,"   							  	//Nro Factura
			           ."                                    fentip    ,"   							  	//Tipo de factura
			           ."                                    fennit    ,"								  	//Nit Empresa
			           ."                                    fencod    ,"   							  	//Codigo Empresa
			           ."                                    fenres    ,"  							  	    //Codigo Empresa Responsable Cartera
			           ."                                    fenval    ,"								  	//Valor Neto sin IVA y ya incluye Dscto y abonos (Copa,Cuota Mod, Fran, Tiquete)
			           ."                                    fenviv    ," 								  	//Valor IVA
			           ."                                    fencop    ," 								  	//Copago
			           ."                                    fencmo    ,"  							  	    //Cuota Moderadora
			           ."                                    fendes    ,"								  	//Valor  Descuento
			           ."                                    fenabo    ," 								  	//Valor Abonos
			           ."                                    fenvnd    ," 								  	//Valor Notas Debito
			           ."                                    fenvnc    ,"								  	//Valor Notas Credito
			           ."                                    fensal    ," 								  	//Valor Saldo
			           ."                                    fenest    ," 								  	//Estado del registro
			           ."                                    fencre    ,"								  	//Cantidad de Responsables
			           ."                                    fenpde    ,"								  	//porcentaje de Descuento
			           ."                                    fenrec    ,"								  	//% Reconocido
			           ."                                    fentop    ,"								  	//Valor Tope
			           ."                                    fenhis    ,"								  	//Historia
			           ."                                    fening    ,"								  	//Nro Ingreso
			           ."                                    fenesf    ,"								  	//Estado Factura
			           ."                                    fenrln    ,"								  	//Resolucion DIAN
			           ."                                    fencco    ,"                                   //Centro de costo que factura
			           ."                                    fendpa    ,"                                   //Documento del paciente responsable
			           ."                                    fennpa    ,"                                   //Nombre del paciente responsable
			           ."                                    Seguridad) "

				       ."                         VALUES ('".$wbasedato                          ."','"
				       .                                     $wfecha                             ."','"
				       .                                     $hora                               ."','"
				       .                                     date("Y")                           ."','"	    //Año
				       .                                     date("m")                           ."','"	    //Mes
				       .                                     $wfecha                             ."','"	    //Fecha
				       .                                     $wfueffa                            ."','"	    //Fuente Factura
				       .                                     $wnrofac                            ."','"	    //Nro Factura
				       .                                     $wwtipcli                           ."','"	    //Tipo de Factura
				       .                                     $wwnitemp                           ."','"	    //Nit Empresa
				       .                                     $wwcodemp                           ."','"	    //Codigo Empresa
				       .                                     $wwempres                           ."',"	    //Codigo Empresa Responsable Cartera
				       .                                     number_format($wwtotal,0,'.','')    .","       //Valor Neto sin IVA y sin Dscto
				       .                                     number_format($wwiva,0,'.','')      .","		//Valor IVA
				       .                                     number_format($wwcopago,0,'.','')   .","	    //Copago
				       .                                     number_format($wwcuotamod,0,'.','') .","	    //Cuota Moderadora
				       .                                     number_format($wwvaldto,0,'.','')   .","		//Valor  Descuento
				       .                                     number_format($wwabonos,0,'.','')   .","		//Valor  Abonos
				       .                                     number_format($wwnd,0,'.','')       .","	    //Valor Notas Debito
				       .                                     number_format($wwnc,0,'.','')       .","       //Valor Notas Credito
				       .                                     number_format($wwtotal,0,'.','')    .","	    //Valor Saldo
				       ."                                    'on'                                  ,"		//Estado del registro
				       .                                     $wcanres                            .","		//Cantidad de Responsables
				       .                                     $wwdscto                            .","		//porcentaje de Descuento
				       .                                     $wwrecono                           .","		//% Reconocido
				       .                                     $wwtope                             .","       //Valor Tope
				       .                                     $whistoria                          .","		//Historia
				       .                                     $wing                               .","		//Nro Ingreso
				       ."'".                                 $westFactura						 ."','"		//Estado Factura
				       .                                     $wresolucion                        ."','"     //Resolucion DIAN
				       .                                     $wcco                               ."','"     //Centro de costo que factura
				       .                                     $wwdocpac                           ."','"     //Documento del paciente responsable
				       .                                     $wwnompac                           ."',"      //Nombre del paciente responsable
				       ."                                    'C-".$wusuario."')";

				     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

				     //**************************
			         //Aca grabo la auditoria
			         //**************************
			         $q= " INSERT INTO ".$wbasedato."_000107 (   Medico       ,   Fecha_data,   Hora_data,   audhis       ,   auding  ,   audreg     , audacc ,   audusu      , Seguridad) "
			            ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$whistoria."','".$wing."','".$wnrofac."', 'Grabo','".$wusuario."', 'C-".$wusuario."')";
			         $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

			         //**ATENCION ** LA MATRIX registro se llena en la funcion mostrar()

			         if ($wreg_matriz > 0)
				        {
					     //===================================================================================================================================================
					     ////if ($j==0 and $wtotpac >= 0 and $wtiene_cargos_pac=="on")       // ** Factura del PACIENTE ** //
					     if ($j==0 and $wtiene_cargos_pac=="on")                             //                            //
			                {
				             $wvalabo=0;                                                     //----------------------------//
				             for ($i=1;$i<=$wreg_matriz;$i++)
				                {
					             //          Concepto          , fuente  , Nro Fact, Valor Concepto    , Actualiza, Registro
					             busca_abono($wregistro[$i][13], $wfueffa, $wnrofac, $wregistro[$i][31], "on"     , $wregistro[$i][26]);

					             if ($wregistro[$i][31] <> 0 or $wabono == "on")  //Entra si el valor es diferente a cero o si el concepto es de abono
					               {
						            //===================================================================================================
						            //ACA BUSCO TODOS LOS TIPOS DE ABONOS PARA ACTUALIZAR LA TABLA _000018 EN SUS CAMPOS CORRESPONDIENTES
						            //if ($row_con[0] == "on")                      //*** Si es Abono ***
						            if ($wregistro[$i][31]=="")
									   $wregistro[$i][31]=0;

						            if ($wabono == "on")                            //*** Si es Abono ***
						               $wvalabo = $wvalabo + $wregistro[$i][31];

						            //***************************************************************************************************//
						            //Aca actualizo la tabla de cargos colocando en el campo de excedente el valor que se facturo de este//
						            //***************************************************************************************************//
						            $q = "   UPDATE ".$wbasedato."_000106"
						                ."      SET tcarfex = tcarfex + ".$wregistro[$i][31]
						                ."    WHERE id      = ".$wregistro[$i][26];
						            $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

						            //*****************************************************//
						            //Aca grabo la relacion cargos con factura del PACIENTE//
						            //*****************************************************//
						            if ($wregistro[$i][31] != 0)
						               {
							            $q= " INSERT INTO ".$wbasedato."_000066 (   Medico       ,   Fecha_data,   Hora_data,   rcfffa     ,   rcffac     ,   rcfreg               ,  rcfval              , rcfest, rcftip, Seguridad) "
							               ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$wfueffa."','".$wnrofac."','".$wregistro[$i][26]."',".$wregistro[$i][31].", 'on'  , 'E'   , 'C-".$wusuario."')";
							            $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						               }
						           }
						        }

						     //GRABO EN LA TABLA ** FACTURAS POR CONCEPTO ** CON ROMPIMIENTOS DE CONTROL
					         $k=1;
					         while ($k<=$wreg_matriz)
							     {
								  $wccosto=$wregistro[$k][12];
								  while (($k<=$wreg_matriz) and $wccosto==$wregistro[$k][12])
								       {
									    $wccon=$wregistro[$k][13];
									    while (($k<=$wreg_matriz) and ($wccosto==$wregistro[$k][12]) and ($wccon==$wregistro[$k][13]))
									         {
										      $wterc=$wregistro[$k][17];
										      $wvalor_p =0;
										      while (($k<=$wreg_matriz) and ($wccosto==$wregistro[$k][12]) and ($wccon==$wregistro[$k][13]) and ($wterc==$wregistro[$k][17]))
										           {
											        $wvalor_p   =$wvalor_p +$wregistro[$k][31];
											        $wporc_terc =$wregistro[$k][19];
											        $k++;
											       }

											  //          Concepto, fuente  , Nro Fact, Valor Concepto, Actualiza
					             			  busca_abono($wccon  , $wfueffa, $wnrofac, $wvalor_p     , "off", '');

					             			  //======================================================================================================================
										      //ACA TRAIGO EL VALOR BRUTO DE LA FACTURA EL CUAL VA A SERVIR PARA CALCULAR EL SALDO DE LOS CONCEPTOS CUANDO HAY ABONOS
										      //======================================================================================================================

										      $q = " SELECT fenval + fenabo + fencmo + fencop "
										          ."   FROM ".$wbasedato."_000018 "
										          ."  WHERE fenffa = '".$wfueffa."'"
										          ."    AND fenfac = '".$wnrofac."'";
										      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
										      $row = mysql_fetch_array($res);
										      $wwtotal = $row[0];

										      //ACA DEBO GRABAR EN LA TABLA DE FACTURAS DETALLADAS POR CONCEPTO, CCO Y TERCERO
										      if ($wvalor_p > 0 or $wabono == "on")
									            {
										         //Aca calculo el descuento del concepto si no es un abono
										         if ($wwvaldto > 0 and $wwdscto == 0 and $wabono != "on" )
										            $wvaldscto=round($wvalor_p*($wwdscto/100));
										           else
										              if ($wwvaldto > 0 and $wwdscto > 0 and $wabono != "on")
					                                     {
					                                      $q = "SELECT grutip "
				                                              ."  FROM ".$tablaConceptos." "
				                                              ." WHERE grucod = '".$wccon."'";
				                                          $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					                                      $num = mysql_num_rows($err);

					                                      if ($num > 0)
					                                         {
						                                      //Aca busco si el concepto tiene descuento configurado para esta empresa
						                                      $q= " SELECT conempdes "
						                                         ."   FROM ".$wbasedato."_000117 "
						                                         ."  WHERE conempcon = '".$wccon."'"
						                                         ."    AND conempemp = '".$wcodemp."'"
						                                         ."    AND conempdes > 0 "
						                                         ."    AND conempest = 'on' ";
						                                      $errdes = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						                                      $wdescon = mysql_fetch_array($errdes);  //J U N I O  1  DE 2007

											                  $row = mysql_fetch_array($err);         //M A Y O  11  DE 2007
											                  if (strtoupper(trim($row[0])) == "P" or $wdesc_gral=="on" or $wdescon[0]>0)  //Si el concepto es Propio aplico el descuento o si el descuento es general o por empresa o si el concepto para esta empresa tiene descuento
											                     {
												                  if ($wdescon[0]>0)  //Si el descuento es por concepto lo coloco como el descuento a aplicar, porque de pronto tambien tiene descuento dado por pantalla y puede ser diferente
												                     $wwdscto=$wdescon[0];
											                      $wvaldscto=round($wvalor_p*($wwdscto/100))+($wvalor_p-(round($wvalor_p*($wwdscto/100))+round($wvalor_p*((100-$wwdscto)/100))));
										                         }
											                    else
				                                                   $wvaldscto=0;
				                                             }
			                                                else
			                                                   $wvaldscto=0;
				                                         }
				                                        else
					                                       $wvaldscto=0;

										         //*** ACA DESGLOSO ***
										         if ($wabono != "on" and abs($wvalabo) > 0)   //Si el concepto es diferente de Abono y la cuenta tuvo abonos lo desgloso
										             $wvalor_s=round(($wvalor_p-$wvaldscto)/$wwtotal*abs($wvalabo));
										           else
										              $wvalor_s=0;


									             $q= " INSERT INTO ".$wbasedato."_000065 (   Medico       ,   Fecha_data,   Hora_data,   fdefue     ,   fdedoc     ,   fdecco     ,   fdecon   , fdevco      ,   fdeter   ,   fdepte        , fdeest,  fdevde      ,   fdesal                           ,   fdeffa     ,   fdefac     , Seguridad) "
						               			    ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$wfueffa."','".$wnrofac."','".$wccosto."','".$wccon."',".$wvalor_p.",'".$wterc."','".$wporc_terc."', 'on'  ,".$wvaldscto.",".($wvalor_p-$wvalor_s-$wvaldscto).",'".$wfueffa."','".$wnrofac."', 'C-".$wusuario."')";
						            		     $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					            		        }
										     }
									   }
								 }
						    }

						 //===================================================================================================================================================
					     //if ($j==1 and $wtotemp >= 0 and $wtiene_cargos_emp=="on")      // ** Factura del Responsable PRINCIPAL **//
					     if ($j==1 and $wtiene_cargos_emp=="on")                          //                                        //
					        {
						     $wvalabo=0;                                                  //----------------------------------------//
						     for ($i=1;$i<=$wreg_matriz;$i++)
						        {
						         //          Concepto          , fuente  , Nro Fact, Valor Concepto    , Actualiza, Registro
					             busca_abono($wregistro[$i][13], $wfueffa, $wnrofac, $wregistro[$i][32], "on"     , $wregistro[$i][26]);

					             //======================================================================================================================
							     //ACA TRAIGO EL VALOR BRUTO DE LA FACTURA EL CUAL VA A SERVIR PARA CALCULAR EL SALDO DE LOS CONCEPTOS CUANDO HAY ABONOS
							     //======================================================================================================================
							     $q = " SELECT fenval + fenabo + fencmo + fencop "
							         ."   FROM ".$wbasedato."_000018 "
							         ."  WHERE fenffa = '".$wfueffa."'"
							         ."    AND fenfac = '".$wnrofac."'";
							     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							     $row = mysql_fetch_array($res);
							     $wwtotal = $row[0];

							     if ($wabono == "on")                            //*** Si es Abono ***
						            $wvalabo = $wvalabo + $wregistro[$i][32];

					             if ($wregistro[$i][32] != 0 or $wabono == "on")
					                {
						             //*******************************************************************************************************//
						             //Aca actualizo la tabla de cargos colocando en el campo valor reconocido el valor que se facturo de este//
						             //*******************************************************************************************************//
						             if ($wregistro[$i][32]=="")
									    $wregistro[$i][32]=0;

						             $q = "   UPDATE ".$wbasedato."_000106 "
						                 ."      SET tcarfre = tcarfre + ".$wregistro[$i][32]
						                 ."    WHERE id      = ".$wregistro[$i][26];
						             $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

						             //*************************************************************//
						             //Aca grabo la relacion cargos con factura del RESPONSABLE PPAL//
						             //*************************************************************//
						             if ($wregistro[$i][32] != 0)
						                {
						                 $q= " INSERT INTO ".$wbasedato."_000066 (   Medico       ,   Fecha_data,   Hora_data,   rcfffa     ,   rcffac     ,  rcfreg               ,  rcfval              , rcfest, rcftip, Seguridad) "
						                    ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$wfueffa."','".$wnrofac."','".$wregistro[$i][26]."',".$wregistro[$i][32].", 'on'  , 'R'   , 'C-".$wusuario."')";
						                 $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					                    }
						            }
					            }
						     //GRABO EN LA TABLA ** FACTURAS POR CONCEPTO ** CONTROLANDO CON ROMPIMIENTOS DE CONTROL
					         $k=1;
					         while ($k<=$wreg_matriz)
							     {
								  $wccosto=$wregistro[$k][12];
								  while (($k<=$wreg_matriz) and $wccosto==$wregistro[$k][12])
								       {
									    $wccon=$wregistro[$k][13];
									    while (($k<=$wreg_matriz) and ($wccosto==$wregistro[$k][12]) and ($wccon==$wregistro[$k][13]))
									         {
										      $wterc=$wregistro[$k][17];
										      $wvalor_ppal =0;
										      while (($k<=$wreg_matriz) and ($wccosto==$wregistro[$k][12]) and ($wccon==$wregistro[$k][13]) and ($wterc==$wregistro[$k][17]))
										           {
											        $wvalor_ppal =$wvalor_ppal +$wregistro[$k][32];
											        $wporc_terc =$wregistro[$k][19];
											        $k++;
											       }
											  //          Concepto, fuente  , Nro Fact, Valor Concepto, Actualiza
					             			  busca_abono($wccon  , $wfueffa, $wnrofac, $wvalor_ppal  , "off", '');

					             			  //======================================================================================================================
										      //ACA TRAIGO EL VALOR BRUTO DE LA FACTURA EL CUAL VA A SERVIR PARA CALCULAR EL SALDO DE LOS CONCEPTOS CUANDO HAY ABONOS
										      //======================================================================================================================

										      $q = " SELECT fenval + fenabo + fencmo + fencop "
										          ."   FROM ".$wbasedato."_000018 "
										          ."  WHERE fenffa = '".$wfueffa."'"
										          ."    AND fenfac = '".$wnrofac."'";
										      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
										      $row = mysql_fetch_array($res);
										      $wwtotal = $row[0];

										      if ($wwvaldto > 0 and $wwdscto == 0 and $wabono != "on")  //Si entra aca es porque tiene descuento por concepto
									            {
										         $q= " SELECT conempdes "
										            ."   FROM ".$wbasedato."_000117 "
										            ."  WHERE conempemp = '".$wwcodemp."'"
										            ."    AND conempcon = '".$wccon."'"
										            ."    AND conempest = 'on' ";
										         $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				                                 $num = mysql_num_rows($err);

				                                 if ($num > 0)
				                                    {
										             $row = mysql_fetch_array($err);
										             $wvaldscto=round($wvalor_ppal*($row[0]/100))+($wvalor_ppal-(round($wvalor_ppal*($row[0]/100))+round($wvalor_ppal*((100-$row[0])/100))));
			                                        }
			                                       else
			                                          $wvaldscto=0;
				                                }
				                               else
				                                  if ($wwvaldto > 0 and $wwdscto > 0 and $wabono != "on")
				                                     {
				                                      $q = "SELECT grutip "
			                                              ."  FROM ".$tablaConceptos." "
			                                              ." WHERE grucod = '".$wccon."'";
			                                          $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
				                                      $num = mysql_num_rows($err);

				                                      if ($num > 0)
				                                         {
										                  $row = mysql_fetch_array($err);

										                  //Aca busco si el concepto tiene descuento configurado para esta empresa
					                                      $q= " SELECT conempdes "
					                                         ."   FROM ".$wbasedato."_000117 "
					                                         ."  WHERE conempcon = '".$wccon."'"
					                                         ."    AND conempemp = '".$wcodemp."'"
					                                         ."    AND conempdes > 0 "
					                                         ."    AND conempest = 'on' ";
					                                      $errdes = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					                                      $wdescon = mysql_fetch_array($errdes);  //J U N I O  1  DE 2007

										                                                          //M A Y O  11  DE 2007
										                  if (strtoupper(trim($row[0])) == "P" or $wdesc_gral=="on" or $wdescon[0]>0)  //Si el concepto es Propio aplico el descuento o si el descuento es general o por empresa
										                     {
											                  if ($wdescon[0]>0)  //Si el descuento es por concepto lo coloco como el descuento a aplicar, porque de pronto tambien tiene descuento dado por pantalla y puede ser diferente
												                 $wwdscto=$wdescon[0];
										                      //M A Y O  11  DE 2007:
										                      $wvaldscto=round($wvalor_ppal*($wwdscto/100))+($wvalor_ppal-(round($wvalor_ppal*($wwdscto/100))+round($wvalor_ppal*((100-$wwdscto)/100))));
									                         }
										                    else
			                                                   $wvaldscto=0;
			                                             }
		                                                else
		                                                   $wvaldscto=0;
			                                         }
			                                        else
				                                       $wvaldscto=0;

					             			  //ACA DEBO GRABAR EN LA TABLA DE FACTURAS DETALLADAS POR CONCEPTO, CCO Y TERCERO
										      if ($wvalor_ppal > 0 or $wabono == "on")
									            {
										         //*** ACA DESGLOSO ***
										         if ($wabono != "on" and abs($wvalabo) > 0)   //Si el concepto es diferente de Abono y la cuenta tuvo abonos lo desgloso
										            $wvalor_s=round(($wvalor_ppal-$wvaldscto)/$wwtotal*abs($wvalabo));
										           else
										              $wvalor_s=0;

										         $q= " INSERT INTO ".$wbasedato."_000065 (   Medico       ,   Fecha_data,   Hora_data,   fdefue     ,   fdedoc     ,   fdecco     ,   fdecon   ,  fdevco        ,   fdeter   ,   fdepte        , fdeest,   fdevde      ,    fdesal                              ,   fdeffa     ,   fdefac     , Seguridad) "
						               			    ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$wfueffa."','".$wnrofac."','".$wccosto."','".$wccon."',".$wvalor_ppal.",'".$wterc."','".$wporc_terc."', 'on'  , ".$wvaldscto.", ".($wvalor_ppal-$wvalor_s-$wvaldscto).",'".$wfueffa."','".$wnrofac."', 'C-".$wusuario."')";
						            		     $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
					            		        }
									         }
									   }
								 }
					        }


					     //===================================================================================================================================================
					     if (($j >= 2))                                             // ** Factura(s) del (los) otro(s) RESPONSABLE(S)//
					        {                                                       //-----------------------------------------------//
					          $wvalabo=0;
					          if ($wcanres > 1)                                     // Columnas de los demas responsables
					            {
						         if ($j <= $wcanres)
						            {
							 	     $k=$j;
							 	     for ($i=1;$i<=$wreg_matriz;$i++)
							 	        {
								 	     if (isset($wregistro[$i][32+($k-1)]) and $wregistro[$i][32+($k-1)] != 0 and $wregistro[$i][32+($k-1)] != "")
							 	            {
								 	         //          Concepto          , fuente  , Nro Fact, Valor Concepto           , accion, Registro
								             busca_abono($wregistro[$i][13], $wfueffa, $wnrofac, $wregistro[$i][32+($k-1)], "on"  , $wregistro[$i][26] );

								             //======================================================================================================================
										     //ACA TRAIGO EL VALOR BRUTO DE LA FACTURA EL CUAL VA A SERVIR PARA CALCULAR EL SALDO DE LOS CONCEPTOS CUANDO HAY ABONOS
										     //======================================================================================================================

										     $q = " SELECT fenval + fenabo + fencmo + fencop "
										         ."   FROM ".$wbasedato."_000018 "
										         ."  WHERE fenffa = '".$wfueffa."'"
										         ."    AND fenfac = '".$wnrofac."'";
										     $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
										     $row = mysql_fetch_array($res);
										     $wwtotal = $row[0];

										     if ($wabono == "on")                            //*** Si es Abono ***
									            $wvalabo = $wvalabo + $wregistro[$i][32+($k-1)];

								             if ($wregistro[$i][32+($k-1)] != 0 or $wabono == "on")
								                {
									             //*******************************************************************************************************//
									             //Aca actualizo la tabla de cargos colocando en el campo valor reconocido el valor que se facturo de este//
									             //*******************************************************************************************************//
									             if ($wregistro[$i][32+($k-1)]=="")
									                $wregistro[$i][32+($k-1)]=0;

									             $q = "   UPDATE ".$wbasedato."_000106"
									                 ."      SET tcarfre = tcarfre + ".$wregistro[$i][32+($k-1)]
									                 ."    WHERE id      = ".$wregistro[$i][26];
									             $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());

									             //*************************************************************//
									             //Aca grabo la relacion cargos con factura del RESPONSABLE PPAL//
									             //*************************************************************//
									             if ($wregistro[$i][32+($k-1)] != 0)
									                {
										             $q= " INSERT INTO ".$wbasedato."_000066 (  Medico       ,   Fecha_data,   Hora_data,   rcfffa     ,   rcffac     ,  rcfreg               ,  rcfval                     , rcfest, rcftip, Seguridad) "
										               ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$wfueffa."','".$wnrofac."','".$wregistro[$i][26]."',".$wregistro[$i][32+($k-1)].", 'on' , 'E'   , 'C-".$wusuario."')";
										             $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
									                }
									            }
								 	        }
								        }

									 //GRABO EN LA TABLA ** FACTURAS POR CONCEPTO ** CONTROLANDO CON ROMPIMIENTOS DE CONTROL
							         $k=1;
							         while ($k<=$wreg_matriz)
									     {
										  $wccosto=$wregistro[$k][12];
										  while (($k<=$wreg_matriz) and $wccosto==$wregistro[$k][12])
										       {
											    $wccon=$wregistro[$k][13];
											    while (($k<=$wreg_matriz) and ($wccosto==$wregistro[$k][12]) and ($wccon==$wregistro[$k][13]))
											         {
												      $wterc=$wregistro[$k][17];
												      $wvalor_otr[$j] =0;
												      while (($k<=$wreg_matriz) and ($wccosto==$wregistro[$k][12]) and ($wccon==$wregistro[$k][13]) and ($wterc==$wregistro[$k][17]))
												           {
													        $wvalor_otr[$j] =$wvalor_otr[$j] +$wregistro[$k][32+($j-1)];
													        $wporc_terc =$wregistro[$k][19];
													        $k++;
												           }

												      //==============================================
												      //==============================================
												      //          Concepto, fuente  , Nro Fact, Valor Concepto , Actualiza
							             			  busca_abono($wccon  , $wfueffa, $wnrofac, $wvalor_otr[$j], "off", '');

							             			  //======================================================================================================================
												      //ACA TRAIGO EL VALOR BRUTO DE LA FACTURA EL CUAL VA A SERVIR PARA CALCULAR EL SALDO DE LOS CONCEPTOS CUANDO HAY ABONOS
												      //======================================================================================================================

												      $q = " SELECT fenval + fenabo + fencmo + fencop "
												          ."   FROM ".$wbasedato."_000018 "
												          ."  WHERE fenffa = '".$wfueffa."'"
												          ."    AND fenfac = '".$wnrofac."'";
												      $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
												      $row = mysql_fetch_array($res);
												      $wwtotal = $row[0];

												      if ($wwvaldto > 0 and $wwdscto == 0 and $wabono != "on")  //Si entra aca es porque tiene descuento por concepto
											            {
												         $q= " SELECT conempdes "
												            ."   FROM ".$wbasedato."_000117 "
												            ."  WHERE conempemp = '".$wwcodemp."'"
												            ."    AND conempcon = '".$wccon."'"
												            ."    AND conempest = 'on' ";
												         $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						                                 $num = mysql_num_rows($err);

						                                 if ($num > 0)
						                                    {
												             $row = mysql_fetch_array($err);
												             $wvaldscto=round($wvalor_otr[$j]*($row[0]/100));
					                                        }
					                                       else
					                                          $wvaldscto=0;
					                                    }
						                               else
						                                  if ($wwvaldto > 0 and $wwdscto > 0 and $wabono != "on")
						                                     {
						                                      $q = "SELECT grutip "
					                                              ."  FROM ".$tablaConceptos." "
					                                              ." WHERE grucod = '".$wccon."'";
					                                          $err = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
						                                      $num = mysql_num_rows($err);

						                                      if ($num > 0)
						                                         {
												                  $row = mysql_fetch_array($err);

												                  //Aca busco si el concepto tiene descuento configurado para esta empresa
							                                      $q= " SELECT conempdes "
							                                         ."   FROM ".$wbasedato."_000117 "
							                                         ."  WHERE conempcon = '".$wccon."'"
							                                         ."    AND conempemp = '".$wcodemp."'"
							                                         ."    AND conempdes > 0 "
							                                         ."    AND conempest = 'on' ";
							                                      $errdes = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							                                      $wdescon = mysql_fetch_array($errdes);  //J U N I O  1  DE 2007

												                                                          //M A Y O  11  DE 2007
												                  if (strtoupper(trim($row[0])) == "P" or $wdesc_gral=="on" or $wdescon[0]>0)  //Si el concepto es Propio aplico el descuento o si el descuento es general o por empresa
												                     {
													                  if ($wdescon[0]>0)  //Si el descuento es por concepto lo coloco como el descuento a aplicar, porque de pronto tambien tiene descuento dado por pantalla y puede ser diferente
												                         $wwdscto=$wdescon[0];
												                      $wvaldscto=round($wvalor_otr*($wwdscto/100));
											                         }
												                    else
					                                                   $wvaldscto=0;
				                                                 }
				                                                else
				                                                   $wvaldscto=0;
					                                         }
					                                        else
						                                       $wvaldscto=0;

							             			  //ACA DEBO GRABAR EN LA TABLA DE FACTURAS DETALLADAS POR CONCEPTO, CCO Y TERCERO
												      if ($wvalor_otr[$j] > 0 or $wabono == "on")
											            {
												         //*** ACA DESGLOSO ***
												         if ($wabono != "on" and abs($wvalabo) > 0)   //Si el concepto es diferente de Abono y la cuenta tuvo abonos lo desgloso
												            $wvalor_s=round(($wvalor_otr[$j]-$wvaldscto)/$wwtotal*abs($wvalabo));
												           else
												              $wvalor_s=0;

												         $q= " INSERT INTO ".$wbasedato."_000065 (   Medico       ,   Fecha_data,   Hora_data,   fdefue     ,   fdedoc     ,   fdecco     ,   fdecon   ,  fdevco           ,   fdeter   ,   fdepte        , fdeest,   fdevde      ,    fdesal                                 ,   fdeffa     ,   fdefac     ,  Seguridad) "
								               			    ."                            VALUES ('".$wbasedato."','".$wfecha."','".$hora."' ,'".$wfueffa."','".$wnrofac."','".$wccosto."','".$wccon."',".$wvalor_otr[$j].",'".$wterc."','".$wporc_terc."', 'on'  , ".$wvaldscto.", ".($wvalor_otr[$j]-$wvalor_s-$wvaldscto).",'".$wfueffa."','".$wnrofac."', 'C-".$wusuario."')";
								            		     $res2 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
							            		        }
												     }
											   }
										 }
								     }
						        }
							}
				        }
				   }
				}  //Fin del for de wcanres
			  if ($wgenfac=="S")
			     {
			      echo "<tr>";
				  echo "<td class='encabezadotabla' colspan=10 align=right><b><font size=4>Factura(s)</font></b></td>";
				  for ($k=0;$k<=$wcanres;$k++)
				     if (isset($wfactura[$k]) and $wgenfac=="S")
				       {
					    $wimpfac="<A href='/matrix/ips/reportes/r003-imp_factura.php?wfactura=".$wfactura[$k]."&amp;wbasedato=".$wbasedato."&wemp_pmla=".$wemp_pmla."' TARGET='_blank' style=color:FFFFFF> ";
					    echo "<td class='encabezadotabla' colspan=1 align=center><b><font size=2>".$wimpfac.$wfactura[$k]."</font></b></td>";
				       }
				      else
				         echo "<td class='encabezadotabla' colspan=1 align=center></td>";
				  echo "</tr>";
			     }
	         } //Fin del then del if is_numeric($wnrofac)
	        else
	           {
				?>
				 <script>
				   alert ("** ALERTA ** EL USUARIO TIENE ASIGNADO UN CENTRO DE COSTO QUE NO POSEE NUMERACION PARA FACTURAS");
		         </script>
				<?php
			   }
	     }  //Fin del if wok==on and $wfalta_paq
        else
           {
	        if ($wfalta_paq=="on")
	           {
			    ?>
			     <script>
			       alert ("** ALERTA ** FALTAN CARGOS DEL PAQUETE POR GRABAR");
	             </script>
			    <?php
		       }
		   }

	    } //Fin del then if (isset($grabar))
	   else
	      if (isset($wok) and $wok=="off")
	         {
		      if (isset($whistoria) and isset($wdoc_otro) and isset($wnom_otro))
			      if (trim($wdoc_otro) == "" or trim($wnom_otro) == "")
			         {
				      ?>
				       <script>
				        alert ("!!!!FALTAN LOS DATOS DEL PACIENTE RESPONSABLE POR INGRESAR!!!!");
		               </script>
				      <?php
				     }
			        else
			           echo "!!!! NO SE PUEDE FACTURAR LA CUENTA PORQUE EXISTEN CONCEPTOS DESCUADRADOS CON RESPECTO A LO QUE PAGA CADA RESPONSABLE o FALTAN DATOS POR INGRESAR!!!!";
             }
            /*else
               {
	            if ($wreiniciar==true)
	               {
				    ?>
				     <script>
				       alert ("!!!!NO SE PUEDA FACTURAR HASTA QUE NO RECALCULE LA CUENTA!!!!");
		             </script>
				    <?php
				   }
		       } */

  //==================================================================================================================================
  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

  if (isset($wcanres))
     {
	  if ($whay_registros=="S")
	     {
	      echo "<td class=".$wcf2." colspan=16 align=center><b><font text color=".$wclfa." size=4></font></b><input type='button' value='ReCalcular Cuenta' onclick='enter()' id='winiciar' disabled ></td>";
	      echo "<tr><td class=".$wcf2." colspan=8 align=center><b><font text color=".$wclfa." size=5>Traer Valores tal como se Grabaron</font></b><input type='checkbox' name='wreiniciar' onclick='ModificarCuenta();'></td>";
	      echo "<td class=".$wcf2." colspan=8 align=center><b><font text color=".$wclfa." size=5>Facturar</font></b><input type='checkbox' name='wgrabar' SIZE=2 id='wgrabar'></td></tr>";
	     }
	  echo "<tr><td align=center class=fila1 colspan=16><input type='submit' value='OK'></td></tr>";
     }
  echo "<tr><td align=left colspan=15><input type=button value='Cerrar Ventana' onclick='cerrarVentana()'></td></tr>";
  echo "</table>";
}
?>
