<?php
include_once("conex.php"); header("Content-Type: text/html;charset=ISO-8859-1"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Programa de comentarios y sugerencias</title>
<!-- UTF-8 is the recommended encoding for your pages -->
    <meta http-equiv="content-type" content="text/xml; charset=utf-8" />
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>	
<script>
       // Configuracion del datepicker (Funcion jquery) para colocar el calendario al lado de las fechas
        $.datepicker.regional['esp'] = {
            closeText: 'Cerrar',
            prevText: 'Antes',
            nextText: 'Despues',
            monthNames: ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
            'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr','May','Jun',
            'Jul','Ago','Sep','Oct','Nov','Dic'],
            dayNames: ['Domingo','Lunes','Martes','Miercoles','Jueves','Viernes','Sabado'],
            dayNamesShort: ['Dom','Lun','Mar','Mie','Jue','Vie','Sab'],
            dayNamesMin: ['D','L','M','M','J','V','S'],
            weekHeader: 'Sem.',
            dateFormat: 'yy-mm-dd',
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['esp']);
</script>

<script type="text/javascript">
$(document).ready(function() {

	$("#fecIni, #fecFin").datepicker({
       showOn: "button",
       buttonImage: "../../images/medical/root/calendar.gif",
       buttonImageOnly: true,
       maxDate:"+1D"
    });
});

function cerrarVentana()
{
	window.close();	
}

//-->

</script>  
</head>
<body>
<?php 

/**
 * 	LISTA DE COMENTARIOS DEL MES ASIGNADOS A UNA UNIDAD ESPCIFICA
 * 
 * Este programa permite realizar la busqueda de comentarios por mes Y por unidad,
 * 
 * @name  matrix\magenta\procesos\coordinador.php
 * @author Carolina Castaño Portilla.
 * 
 * @created 2006-04-27
 * @version 2008-08-04
 * 
 * @modified 2011-05-04  Se cambiaron los query para que tome como base la fecha de envio (Cmofenv) en la tabla 18 de magenta y no la fecha de origen (Ccofori) en tabla 17 de magenta. También se puso el encabezado PHP para que muestre correctamente los caracteres especiales, con tildes y/o Ñ
 * @modified 2008-09-16  Se devolvio el programa a la version anterior debido que lo habia solicitado modificar y posteriormente lo volvieron a cambiar(Ver requerimiento en matrix de Monica 2008-07-24)
 * @modified 2008-08-04  Se realiza cambiaron 2 querys para que trajera la unidad correctamente (Juan David Londoño)
 * @modified 2007-01-09  Se realiza documentacion del programa y calendario
 * 
 * @table magenta_000017, select, 
 * @table magenta_000018, select, 
 * @table magenta_000019, select
 * @table magenta_000020, select
 * @table magenta_000021, select
 * @table magenta_000023, select
 * 
 *  @wvar $a agrado, vector de comentarios de agrado
 *  @wvar $area, area escogida para el reporte, compuesta por codigo y nombre
 *  @wvar $areNomS vector con lista de areas para el drop down
 *  @wvar $aut, usuario
 *  @wvar $bandera1, indica si es la primera vez que se ingresa al programa, es decir se muestra la primera hoja
 *  @wvar $cerrados,  vector que guarda los comentarios cerrados
 *  @wvar $contado1 sieve como contador temporal del tamaño de los vectores de comentarios para cda estado
 *  @wvar $contado2 sieve como contador temporal del tamaño de los vectores de comentarios para cda estado
 *  @wvar $contado3 sieve como contador temporal del tamaño de los vectores de comentarios para cda estado
 *  @wvar $fecIni fecha incial de l busqueda de comentarios
 *  @wvar $fecFin fecha final de l busqueda de comentario
 *  @wvar $idArea id del area seleccionada en tabla 000019
 *  @wvar $idUsuarios, usuario si es de recurso humano o desarrollo institucional
 *  @wvar $inicial, vector utilizado en explodes
 *  @wvar $investigados, vector de comentarios investigados en las fechas dadas
 *  @wvar $senal, indica que sucede, primera vez en entrar, indica si se encontraron comentarios en las fechas dadas, etc
*/
/************************************************************************************************************************* 
  Actualizaciones:
  			2017-03-28  (Arleyda Insignares C.)
  			            -Se agrega el campo estado (ingresado,asignado,investigado,cerrado) adicionando una columna a la
  			             derecha en la tabla que carga la consulta. También se adicionan los registros con estado asignado
  			             debido a que estaban por fuera del informe.
  			             
 			2016-05-4 	(Arleyda Insignares C.)
 						-Se Modifican los campos de calendario fecha inicial y fecha final con utilidad jquery y se elimina
						 uso de Zapatec Calendar por errores en la clase.
						-Se cambia encabezado y titulo con ultimo diseño 
*************************************************************************************************************************/

$wautor="Carolina Castano P.";
$wversion='2011-05-04';
//=================================================================================================================================

/********************************funciones************************************/

/*Si la variable tiene como valor dato no encontrado  retorna vacío*/
function  DisplayError()
{
	echo '<script language="Javascript">';
	echo 'alert ("Error al conectarse con la base de datos, intente más tarde")';
	echo '</script>';

}


/****************************PROGRAMA************************************************/

include_once("root/comun.php");

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{

	// Datos de encabezado
	$wactualiz = "2017-03-29";
	$titulo    = "SISTEMA DE COMENTARIOS Y SUGERENCIAS";

    // Se muestra el encabezado del programa
    encabezado($titulo,$wactualiz, "clinica");  

	/////////////////////////////////////////////////inicializacion de variables///////////////////////////////////
	
 	// calcula el tiempo que se han demorado en investigar el comentario, para color de semaforizacion
 	
	include_once("magenta/semaforo.php");
	
	// conexion a Matrix
	 
	

	


	$empresa='magenta';
	$senal='0';

	/////////////////////////////////////////////////inicializacion de variables//////////////////////////
	/////////////////////////////////////////////////acciones concretas///////////////////////////////////

	//Busco en base de datos el area de interes segun el codigo de la persona que esta accediendo a matrix
	//si el codigo no está o es magenta se preguntara el codigo del area de interes
	// o si el coordinador tiene varias áreas se traerá la lista de areas que se requieran

	$inicial=strpos($user,"-");
	$aut=substr($user, $inicial+1, strlen($user));

	//el usuario puede ser desarrollo organizaciona en cuyo caso ve las verificaciones de las acciones o
	//el usuario puede ser talento hjumano aquien se le muestran es los implicados
	//busco quien es entonces el usuario
	$query ="SELECT A.crecod, B.id_area, C.carcod, C.carnom FROM ".$empresa."_000021 A, ".$empresa."_000020 B, ".$empresa."_000019 C  where A.crecod='".$aut."' and B.id_responsable=A.crecod and C.carcod=B.id_area ";
	$err=mysql_query($query,$conex);
	$num=mysql_num_rows($err);
	//echo $query;
	if  ($num >0) //se llena los valores y un vector con los resultados
	{
		for($i=0;$i<$num;$i++)
		{
			$row=mysql_fetch_row($err);
			if ($row[2]=='40' or $row[2]=='42')
			{
				$idUsuarios=40;
				$usuarios='Direccion de talento humano';
			}
			if ($row[2]=='47')
			{
				$idUsuarios=$row[2];
				$usuarios=$row[3];
			}
		}

	}else //si ese usuario no esta asignado a ninguno de los dos casos
	{
		$senal = 2;
	}

	if (!isset ($idUsuarios))
	$senal = 2;

	if (!isset ($bandera1)) //primera vez que se ingresa se prepara drop down de areas para el reporte
	{
		$query ="SELECT carcod, carnom FROM ".$empresa."_000019 order by carnom";
		$err=mysql_query($query,$conex);
		$num=mysql_num_rows($err);
		//echo $query;
		if  ($num >0) //se llena los valores y un vector con los resultados
		{
			$areNomS="<option selected>00-Todas</option>";
			if ($num >1)//forma para seleccionar el área de una lista de resultados
			{
				$senal=1;
				for($i=0;$i<$num;$i++)
				{
					$row=mysql_fetch_row($err);
					$areNomS=$areNomS."<option>".$row[0]."-".$row[1]."</option>";
				}

			}else
			{
				$row=mysql_fetch_row($err);
				$idArea=$row[1];
				$area=$row[2].'-'.$row[3];
			}
		}else //no existen areas para ser desplegadas
		{
			$senal = 2;
		}

	} else if (!isset ($idArea) and isset ($area)) //me manda el area hay que hacerle explode
	{
		$inicial = explode ('-', $area);
		if ($area!='todas')
		{
				$idArea=$inicial[0];
		}

	}

	//inicializacion de fechas para el reporte
	
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////RANGO DE FECHAS
   /*
   echo '<div id="page" align="center">';
   echo '<table align=center cellspacing="10" >';
    
   echo "<Tr >";
   echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Fecha Inicial &nbsp<i><br></font></td>";
   echo "<td align=center bgcolor=#DDDDDD><b><font text color=#003366 size=2><i>Fecha Final &nbsp<i><br></font></b></td>";
   echo "</Tr >";
   
  
  $hoy=date("Y-m-d");
  if (!isset($fecIni))
        $fecIni=$hoy;
   	$cal="calendario('fecIni','1')";
   	echo "<tr>";
	echo "<td bgcolor='#dddddd' align=center><input type='TEXT' name='fecIni' size=10 maxlength=10  id='fecIni' readonly='readonly' value=".$fecIni." class=tipo3><button id='trigger1' onclick=".$cal.">...</button></td>";
	?>
	  <script type="text/javascript">//<![CDATA[
	   Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'fecIni',button:'trigger1',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	   //]]></script>
	<?php
   		

   if (!isset($fecFin))
       $fecFin=$hoy;
   	  $cal="calendario('fecFin','1')";
	  echo "<td bgcolor='#dddddd' align=center><input type='TEXT' name='fecFin' size=10 maxlength=10  id='fecFin' readonly='readonly' value=".$fecFin." class=tipo3><button id='trigger2' onclick=".$cal.">...</button></td>";
	  ?>
	    <script type="text/javascript">//<![CDATA[
	       Zapatec.Calendar.setup({weekNumbers:false,showsTime:true,timeFormat:'12',electric:false,inputField:'fecFin',button:'trigger2',ifFormat:'%Y-%m-%d',daFormat:'%Y/%m/%d'});	
	    //]]></script>
	  <?php	  
	  echo "</tr>";
	*/
   
	if (!isset($fecIni))
	{
		$pass= intval(date('m'));

		if ($pass<10)
		$pass='0'.$pass;

		$fecIni=date('Y')."-".$pass."-01";
		$fecFin=date('Y')."-".$pass."-31";

		if ($pass==0)
		{
			$pass= intval (date('Y'));
			$fecIni=$pass.'-12-1';
			$fecFin=$pass.'-12-31';

		}
	}
   
	  
	If ($senal==0 and isset($bandera1)) //ya se ha seleccionado un areas
	{
		// Busco los motivos del area y los voy guardando en vectores

		//inicializo el tamaño de los vectores
		$investigados[0]['id']=0;
		$cerrados[0]['id']=0;
		$agrado[0]['id']=0;
		$otros[0]['id']=0;

		if ($idUsuarios=='40') //consulta de talento humano
		{
			// Cambio de 2011-05-04
			$query ="SELECT A.id_Comentario, A.cmonum, A.cmonimp, A.cmotip, A.cmocla, A.cmoest, A.cmofret, A.id_comentario, A.Cmofenv, B.ccoori, C.carnom, E.crenom, B.cconum, F.impnom, G.ccanom";
			//$query ="SELECT A.id_Comentario, A.cmonum, A.cmonimp, A.cmotip, A.cmocla, A.cmoest, A.cmofret, A.id_comentario, B.ccofori, B.ccoori, C.carnom, E.crenom, B.cconum, F.impnom, G.ccanom ";
			$query= $query. " FROM " .$empresa."_000018 A, " .$empresa."_000017 B,   " .$empresa."_000019 C,  " .$empresa."_000020 D,  " .$empresa."_000021 E,  " .$empresa."_000027 F,  " .$empresa."_000022 G ";
			
			

			if (isset($idArea) and $idArea!='00')
			{
				// Cambio de 2011-05-04
				$query= $query. "where A.id_area='".$idArea."' and A.Cmofenv between '".$fecIni."'  and '".$fecFin."'  and B.id =A.id_comentario ";
				$query= $query. "and  C.carcod='".$idArea."' and D.id_Area='".$idArea."' and D.carniv='1' and  E.crecod=D.id_responsable ";
				$query= $query. "and  F.impnco=A.id_comentario and F.impnmo=A.cmonum and F.impcca=G.ccacod and F.impnom<>'- -' and  F.impnom<>'' ORDER BY A.cmofenv";				
				//2008-09-16
				//$query= $query. "where A.id_area='".$idArea."' and B.ccofori between '".$fecIni."'  and '".$fecFin."'  and B.id =A.id_comentario ";
				//$query= $query. "where B.ccoori='".$area."' and B.ccofori between '".$fecIni."'  and '".$fecFin."'  and B.id =A.id_comentario ";
				
			}
			else
			{
				// Cambio de 2011-05-04
				$query= $query. "where A.Cmofenv between '".$fecIni."'  and '".$fecFin."'  and B.id =A.id_comentario ";

				//$query= $query. "where B.ccofori between '".$fecIni."'  and '".$fecFin."'  and B.id =A.id_comentario ";
				$query= $query. "and  C.carcod=A.id_area and D.id_Area=A.id_area and D.carniv='1' and  E.crecod=D.id_responsable ";
				$query= $query. "and  F.impnco=A.id_comentario and F.impnmo=A.cmonum and F.impcca=G.ccacod and F.impnom<>'- -' and  F.impnom<>'' ORDER BY A.cmofenv";
				
				
			}

		}else
		{
			// Cambio de 2011-05-04
			$query ="SELECT A.id_Comentario, A.cmonum, A.cmonimp, A.cmotip, A.cmocla, A.cmoest, A.cmofret, A.id_comentario, A.Cmofenv, B.ccoori, C.carnom, E.crenom, B.cconum, '', '' ";

			//$query ="SELECT A.id_Comentario, A.cmonum, A.cmonimp, A.cmotip, A.cmocla, A.cmoest, A.cmofret, A.id_comentario, B.ccofori, B.ccoori, C.carnom, E.crenom, B.cconum, '', '' ";
			$query= $query. " FROM " .$empresa."_000018 A, " .$empresa."_000017 B,   " .$empresa."_000019 C,  " .$empresa."_000020 D,  " .$empresa."_000021 E ";

			if (isset($idArea) and $idArea!='00')
			{
				//2008-09-16
				$query= $query. "where A.id_area='".$idArea."' and A.cmofenv between '".$fecIni."'  and '".$fecFin."'  and B.id =A.id_comentario ";
				//$query= $query. "where B.ccoori='".$area."' and B.ccofori between '".$fecIni."'  and '".$fecFin."'  and B.id =A.id_comentario ";
				$query= $query. "and  C.carcod='".$idArea."' and D.id_Area='".$idArea."' and D.carniv='1' and  E.crecod=D.id_responsable ORDER BY A.cmofenv";
				
			}
			else
			{
				$query= $query. "where A.cmofenv between '".$fecIni."'  and '".$fecFin."'  and B.id =A.id_comentario ";
				$query= $query. "and  C.carcod=A.id_area and D.id_Area=A.id_area and D.carniv='1' and  E.crecod=D.id_responsable ORDER BY A.cmofenv";
			    
			}
		}

		$err=mysql_query($query,$conex);
		$num=mysql_num_rows($err);
		$tamano = $num;

		if  ($num >0) //se llenan los vectores según el estado del comentario
		{
			$contado1=0;
			$contado2=0;
			$contado3=0;
			$contado4=0;

			for($i=0;$i<$num;$i++)
			{
				$row=mysql_fetch_row($err);
				
				/*echo "<br> ".$cmotip=$row[0]." ".$cmotip=$row[1]." ".$cmotip=$row[2]." ".$cmotip=$row[3]." ".$cmotip=$row[4]." ".$cmotip=$row[5]." ".$cmotip=$row[6]." ".$cmotip=$row[7]." ".$cmotip=$row[8]." ".$cmotip=$row[9]." ".$cmotip=$row[10]." ".$cmotip=$row[11]." ".$cmotip=$row[12]." ".$cmotip=$row[13]." ".$cmotip=$row[14]."<br>";*/

				if ( trim( $row[3] )=='Agrado' )
				{				
				    //and ($row[5]=='INVESTIGADO' or $row[5]=='CERRADO') Se retira filtro para que tome cualquier estado
				    
					$agrado[$contado1]['id']    =$row[0];
					$agrado[$contado1]['motNum']=$row[1];
					$agrado[$contado1]['motEnv']=$row[13];
					$agrado[$contado1]['motTip']=$row[3];
					$agrado[$contado1]['motCla']=$row[4];
					$agrado[$contado1]['motRet']=$row[6];
					$agrado[$contado1]['idCom'] =$row[7];
					$agrado[$contado1]['fecCom']=$row[8];
					$agrado[$contado1]['lugCom']=$row[9];
					$agrado[$contado1]['num']   =$row[12];
					$agrado[$contado1]['carNom']=$row[14];
					$agrado[$contado1]['cmoest']=$row[5];
					$contado1++;

				}else
				{
					
					
					switch ($row[5])
					{

						case 'INVESTIGADO':
							$investigados[$contado2]['id']    =$row[0];
							$investigados[$contado2]['motNum']=$row[1];
							$investigados[$contado2]['motEnv']=$row[13];
							$investigados[$contado2]['motTip']=$row[3];
							$investigados[$contado2]['motCla']=$row[4];
							$investigados[$contado2]['motSem']=$row[4];
							$investigados[$contado2]['motRet']=$row[6];
							$investigados[$contado2]['idCom'] =$row[7];
							$investigados[$contado2]['fecCom']=$row[8];
							$investigados[$contado2]['lugCom']=$row[9];
							$investigados[$contado2]['motAre']=$row[10];
							$investigados[$contado2]['motCoo']=$row[11];
							$investigados[$contado2]['num']   =$row[12];
							$investigados[$contado2]['carNom']=$row[14];
							$investigados[$contado2]['cmoest']=$row[5];
							$contado2++;

							break;

						case 'CERRADO':

							$cerrados[$contado3]['id']    =$row[0];
							$cerrados[$contado3]['motNum']=$row[1];
							$cerrados[$contado3]['motEnv']=$row[13];
							$cerrados[$contado3]['motTip']=$row[3];
							$cerrados[$contado3]['motCla']=$row[4];
							$cerrados[$contado3]['motSem']=$row[4];
							$cerrados[$contado3]['motRet']=$row[6];
							$cerrados[$contado3]['idCom'] =$row[7];
							$cerrados[$contado3]['fecCom']=$row[8];
							$cerrados[$contado3]['lugCom']=$row[9];
							$cerrados[$contado3]['motAre']=$row[10];
							$cerrados[$contado3]['motCoo']=$row[11];
							$cerrados[$contado3]['num']   =$row[12];
							$cerrados[$contado3]['carNom']=$row[14];
							$cerrados[$contado3]['cmoest']=$row[5];
							$contado3++;

							break;

						default:

							$otros[$contado4]['id']    =$row[0];
							$otros[$contado4]['motNum']=$row[1];
							$otros[$contado4]['motEnv']=$row[13];
							$otros[$contado4]['motTip']=$row[3];
							$otros[$contado4]['motCla']=$row[4];
							$otros[$contado4]['motSem']=$row[4];
							$otros[$contado4]['motRet']=$row[6];
							$otros[$contado4]['idCom'] =$row[7];
							$otros[$contado4]['fecCom']=$row[8];
							$otros[$contado4]['lugCom']=$row[9];
							$otros[$contado4]['motAre']=$row[10];
							$otros[$contado4]['motCoo']=$row[11];
							$otros[$contado4]['num']   =$row[12];
							$otros[$contado4]['carNom']=$row[14];
							$otros[$contado4]['cmoest']=$row[5];
							$contado4++;

							break;
					}
				}
			}

		}else
		{
			$senal='3'; //no se encontraron comentario para la unidad
		}

	}



	/////////////////////////////////////////////////encabezado personal///////////////////////////////////

	echo "<div align='center' class='tituloPagina'><font size='4'><A HREF='auditoria.php'>CONSULTA Y RESPUESTA A COMENTARIOS</a></font></div><br><br>" ;
	/////////////////////////////////////////////////encabezado personal///////////////////////////////////
	/////////////////////////////////////////////////presentacion general///////////////////////////////////

	if ($senal ==1)//apenas se va a presentar el dropdown
	{
		
		echo "<div align='center'><div align='center' class='fila1' style='width:380px;'><b>Seleccione la unidad a consultar</b></div></div></BR>";

		echo "<form NAME='ingreso' ACTION='auditoria.php' METHOD='POST'>";
		echo "<table align='center'>";
		echo "<tr class='fila2'>";

		echo "<td align=center width='100'><b>UNIDAD:&nbsp;</b></td>";
		echo "<td align='center' colspan='2'><select name='area'>$areNomS</select></td>";
		echo "<input type='hidden' name='bandera1' value='1' />";

		echo "</td>";
		echo "</tr>";
		
		echo "<tr>";
		echo "<td height='14'>&nbsp</td>";
		echo "</tr>";
		echo "</table>";

		echo "<table align='center' width='380'>";
        echo "<tr class='fila2'>";
        echo "<td align=center><b>Fecha Inicial &nbsp;</b></td>";
        echo "<td align=center><b>Fecha Final &nbsp;</b></td>";
        echo "</tr>";   
  
       $hoy=date("Y-m-d");
       if (!isset($fecIni))
        $fecIni=$hoy;
   	    $cal="calendario('fecIni','1')";
   	    echo "<tr class='fila2'>";
	    echo "<td align=center><input type='text' readonly='readonly' id='fecIni' name='fecIni' value='".date("Y-m")."-01' class=tipo3 ></td>";
  		
      if (!isset($fecFin))
        $fecFin=$hoy;
   	    $cal="calendario('fecFin','1')";
	    echo "<td align=center><input type='text' readonly='readonly' id='fecFin' name='fecFin' value='".date("Y-m-d")."' class=tipo3 ></td>";

	    echo "</tr>";
	    echo "</tr></TABLE></br>";
  	    
	    
		echo "<TABLE align=center><tr>";
		echo "<tr><td align=center colspan=14 width=125><font size='2'  align=center face='arial'><input type='submit' name='aceptar' value='Aceptar' style='width:100' >";
		echo "<td align=center width=125><INPUT type='button' onClick='cerrarVentana()' value='Cerrar' style='width:100'></td></tr>";
		echo "</TABLE>";
		echo "</td>";
		echo "</tr>";
		echo "</form>";
	}
	if ($senal ==2)
	{
		echo "<CENTER>";
		echo "<table align='center' border=0 bordercolor=#000080 style='border:solid;'>";
		echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>NO EXISTE NINGUNA UNIDAD ASIGNADA PARA EL USUARIO</td><tr>";
		echo "</table></center></br></br>";
		echo "<form action='auditoria.php' method=post>
				<table align=center>
					<tr>
						<td><INPUT type='submit' value='Retornar' style='width:100'></td>
						<td><INPUT type='button' onClick='cerrarVentana()' value='Cerrar' style='width:100'>
					</tr>
				</table
			 </form>";
	}
	if ($senal ==3)
	{
		echo "<CENTER>";
		echo "<table align='center' border=0 bordercolor=#000080 style='border:solid;'>";
		echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>LA UNIDAD ".$area." NO TIENE NINGUN COMENTARIO REGISTRADO EN ESTAS FECHAS</td><tr>";
		echo "</table></center></br></br>";

		echo "<form action='auditoria.php' method=post>
				<table align=center>
					<tr>
						<td><INPUT type='submit' value='Retornar' style='width:100'></td>
						<td><INPUT type='button' onClick='cerrarVentana()' value='Cerrar' style='width:100'>
					</tr>
				</table
			 </form>";
	}

	if ($senal ==0)
	{
		echo "<div align='center'><div class='fila1' align='center' style='width:610px'><B>UNIDAD: ".$area." </B></div></div></BR></BR>";
	}
	////////////////////////////////////COMENTARIOS DE AGRADO /////////////////////////////////////////////////////

	if ($senal==0)
	{

		echo "<form action='auditoria.php' method=post>
				<table align=center>
					<tr>
						<td><INPUT type='submit' value='Retornar' style='width:100'></td>
						<td><INPUT type='button' onClick='cerrarVentana()' value='Cerrar' style='width:100'>
					</tr>
				</table
			 </form><br><br>";

		if ($idUsuarios=='40') //presento informe para talento humano
		{
			echo "<div align='center'><div class='encabezadoTabla' align='center' style='width:1000px;'><b>COMENTARIOS DE AGRADO</b></div></div></br>";
			if ($contado1 > 0) // Si el paciente tiene comentarios en estado ingresados se buscan los motivos para mostrar
			{

				echo "<table border=0 cellspacing=2 cellpadding=0 align=center width ='1000'>";
				echo "<tr class='encabezadoTabla'>";
				echo "<td width='8%' align='center'>N Comentario</td>";
				echo "	<td width='8%' align='center'>Fecha de envío</td>";
				echo "<td width='8%' align='center'>Lugar de origen</td>";
				echo "<td width='10%' align='center'>Implicado</td>";
				echo "<td width='10%' align='center'>Aquí va el cargo</td>";
				echo "<td width='10%' align='center'>Tipo</td>";
				echo "<td width='10%' align='center'>Estado</td></tr>";

				for($i=0;$i<$contado1;$i++)
				{
					if (is_int ($i/2))
					   $wcf="fila1";  // color de fondo de la fila
					else
					   $wcf="fila2"; // color de fondo de la fila
				
					echo "<tr class=".$wcf.">";
					echo "<td width='8%' align='center'><a href='investigacion.php?idMot=".$agrado[$i]['motNum']."&idCom=".$agrado[$i]['idCom']."&usuario=".$usuarios."'  align='right'  target='new'>".$agrado[$i]['num']."-".$agrado[$i]['motNum']."<A/></td>";
					echo "<td width='8%' align='center'>".$agrado[$i]['fecCom']."</td>";
					echo "<td width='8%' align='center'>".$agrado[$i]['lugCom']."</td>";
					echo "<td width='10%' align='center'>".$agrado[$i]['motEnv']."</td>";
					echo "<td width='10%' align='center'>{$agrado[$i]['carNom']}</td>";
					echo "<td width='10%' align='center'>".$agrado[$i]['motCla']."</td>";
					echo "<td width='10%' align='center'>".$agrado[$i]['cmoest']."</td>";
					echo "</tr>";

				}
				echo "</table></br></br>";

			}else
			{
				echo "<CENTER>";
				echo "<table align='center' border=0 bordercolor=#000080 style='border:solid;'>";
				echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b> NO SE HAN INGRESADO COMENTARIOS DE AGRADO</td><tr>";
				echo "</table></center></br></br>";
			}

			//////////////////////////////////// COMENTARIOS DESAGRADO /////////////////////////////////////////////////////

			echo "<div align='center'><div class='encabezadoTabla' align='center' style='width:1000px;'><b>COMENTARIOS DE DESAGRADO</b></div></div></br>";
			$k=0;
			if ($contado2 > 0 or $contado3 > 0 or $contado4 > 0) // Si el paciente tiene comentarios en estado ingresados se buscan las acciones para mostrar
			{

				echo "<table border=0 cellspacing=2 cellpadding=0 align=center width ='1000'>";
				echo "<tr class='encabezadoTabla'>";
				echo "<td width='8%' align='center'>N Comentario</td>";
				echo "<td width='8%' align='center'>Fecha de envío</td>";
				echo "<td width='8%' align='center'>Lugar de origen</td>";
				echo "<td width='10%' align='center'>Implicado</td>";
				echo "<td width='10%' align='center'>Cargo</td>";
				echo "<td width='10%' align='center'>Clasificación</td>";
				echo "<td width='10%' align='center'>Estado</td>";
				echo "</tr>";

				for($i=0;$i<$contado2;$i++)
				{
					if (is_int ($i/2))
					   $wcf="fila1";  // color de fondo de la fila
					else
					   $wcf="fila2"; // color de fondo de la fila
				
					echo "<tr class=".$wcf.">";
					echo "<td width='8%' align='center'><a href='investigacion.php?idMot=".$investigados[$i]['motNum']."&idCom=".$investigados[$i]['idCom']."&usuario=".$usuarios."'  align='right' target='new'>".$investigados[$i]['num']."-".$investigados[$i]['motNum']." <A/></td>";
					echo "<td width='8%' align='center'>".$investigados[$i]['fecCom']."</td>";
					echo "<td width='8%' align='center'>".$investigados[$i]['lugCom']."</td>";
					echo "<td width='10%' align='center'>".$investigados[$i]['motEnv']."</td>";
					echo "<td width='10%' align='center'>{$investigados[$i]['carNom']}</td>";
					echo "<td width='10%' align='center'>".$investigados[$i]['motCla']."</td>";
					echo "<td width='10%' align='center'>".$investigados[$i]['cmoest']."</td>";
					echo "</tr>";
					$k++;
				}


				for($i=0;$i<$contado3;$i++)
				{
					if (is_int ($i/2))
					   $wcf="fila1";  // color de fondo de la fila
					else
					   $wcf="fila2"; // color de fondo de la fila
				
					echo "<tr class=".$wcf.">";
					echo "<td width='8%' align='center'><a href='investigacion.php?idMot=".$cerrados[$i]['motNum']."&idCom=".$cerrados[$i]['idCom']."&usuario=".$usuarios."'  align='right' target='new'>".$cerrados[$i]['num']."-".$cerrados[$i]['motNum']." <A/></td>";
					echo "<td width='8%' align='center'>".$cerrados[$i]['fecCom']."</td>";
					echo "<td width='8%' align='center'>".$cerrados[$i]['lugCom']."</td>";
					echo "<td width='10%' align='center'>".$cerrados[$i]['motEnv']."</td>";
					echo "<td width='10%' align='center'>{$cerrados[$i]['carNom']}</td>";
					echo "<td width='10%' align='center'>".$cerrados[$i]['motCla']."</td>";
					echo "<td width='10%' align='center'>".$cerrados[$i]['cmoest']."</td>";
					echo "</tr>";
					$k++;
				}

				for($i=0;$i<$contado4;$i++)
				{
					if (is_int ($i/2))
					   $wcf="fila1";  // color de fondo de la fila
					else
					   $wcf="fila2"; // color de fondo de la fila
				
					echo "<tr class=".$wcf.">";
					echo "<td width='8%' align='center'><a href='investigacion.php?idMot=".$otros[$i]['motNum']."&idCom=".$otros[$i]['idCom']."&usuario=".$usuarios."'  align='right' target='new'>".$otros[$i]['num']."-".$otros[$i]['motNum']." <A/></td>";
					echo "<td width='8%' align='center'>".$otros[$i]['fecCom']."</td>";
					echo "<td width='8%' align='center'>".$otros[$i]['lugCom']."</td>";
					echo "<td width='10%' align='center'>".$otros[$i]['motEnv']."</td>";
					echo "<td width='10%' align='center'>{$otros[$i]['carNom']}</td>";
					echo "<td width='10%' align='center'>".$otros[$i]['motCla']."</td>";
					echo "<td width='10%' align='center'>".$otros[$i]['cmoest']."</td>";
					echo "</tr>";
					$k++;
				}
				echo '</table></br></br>';
			}

			if ($k==0)
			{
				echo "<CENTER>";
				echo "<table align='center' border=0 bordercolor=#000080 style='border:solid;'>";
				echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>NO SE HAN INGRESADO COMENTARIOS ACTITUDINALES DE DESAGRADO </td><tr>";
				echo "</table></center></br></br>";
			}
		}
		////////////////////////////////////    ACCIONES POR IMPLEMENTAR     /////////////////////////////////////////////////////
		if ($idUsuarios=='47') //presento informe para desarrollo organizacional
		{
			echo "<div align='center'><div class='encabezadoTabla' align='center' style='width:1000px;'><b>ACCIONES POR IMPLEMENTAR</b></div></div></br>";
			$k=0;
			if ($contado2> 0 or $contado3> 0 or $contado4> 0) // Si el paciente tiene comentarios en estado ingresados se buscan las acciones para mostrar
			{
				for($i=0;$i<$contado2;$i++)
				{
					$query =" SELECT cacnum, cacres, cacfver FROM " .$empresa."_000023 where id_motivo=".$investigados[$i]['motNum']." and caccon=".$investigados[$i]['id']." and cacest='INGRESADO' and cacfver > '".date('Y-m-d')."' ";
					$err=mysql_query($query,$conex);
					$num=mysql_num_rows($err);
					//echo $query;


					if  ($num >0) //se llenan los vectores según el estado del comentario
					{
						echo "<table border=0 cellspacing=2 cellpadding=0 align=center width='1000'>";
						echo "<tr class='encabezadoTabla'>";
						echo "<td width='15%' ><a href='investigacion.php?idMot=".$investigados[$i]['motNum']."&idCom=".$investigados[$i]['idCom']."&usuario=".$usuarios."' align='right'>N Comentario: ".$investigados[$i]['num']."-".$investigados[$i]['motNum']."</a></td>";
						echo "<td  width='25%'  colspan='2'>Unidad: ".$investigados[$i]['motAre']."&nbsp;</td>";
						echo "<td  width='30%' >Implicado: ".$investigados[$i]['motEnv']." </td>";
						echo "<td width='25%' >Coordinador: ".$investigados[$i]['motCoo']."</td>";
						echo "</tr>";
						echo "<tr class='encabezadoTabla'>";
						echo "<td><b><font size='3'  align='center'>&nbsp;</td>";
						echo "<td align='center' width='25%'>N Accion</td>";
						echo "<td  align='center' colspan='2'>Responsable</td>";
						echo "<td align='center' colspan='2'>Fecha de implementacion</td>";
						echo "</tr>";

						for($j=0;$j<$num;$j++)
						{
							$row=mysql_fetch_row($err);

							echo "<tr>";
							echo "<td>&nbsp;</td>";
							echo "<td class='fila2' align='center' width='25%'>$row[0]</td>";
							echo "<td class='fila2' align='center' colspan='2'>$row[1]</td>";
							echo "<td class='fila2' align='center' colspan='2'>$row[2]</td>";
							echo "</tr>";
							$k++;

						}
						echo "</table>";
					}
				}

				for($i=0;$i<$contado3;$i++)
				{

					$query =" SELECT cacnum, cacres, cacfver FROM " .$empresa."_000023 where id_motivo=".$cerrados[$i]['motNum']." and caccon=".$cerrados[$i]['id']." and cacest='INGRESADO' and cacfver > '".date('Y-m-d')."' ";
					$err=mysql_query($query,$conex);
					$num=mysql_num_rows($err);

					if  ($num >0) //se llenan los vectores según el estado del comentario
					{
						echo "<table border=0 cellspacing=2 cellpadding=0 align=center width='1000'>";
						echo "<tr class='encabezadoTabla'>";
						echo "<tdwidth='15%' ><a href='investigacion.php?idMot=".$cerrados[$i]['motNum']."&idCom=".$cerrados[$i]['idCom']."&usuario=".$usuarios."'  align='right'>N Comentario: ".$cerrados[$i]['num']."-".$cerrados[$i]['motNum']."</a></td>";
						echo "<td  width='25%'  colspan='2'>Unidad: ".$cerrados[$i]['motAre']."&nbsp;</td>";
						echo "<td  width='30%' >Implicado: ".$cerrados[$i]['motEnv']." </td>";
						echo "<tdwidth='25%' >Coordinador: ".$cerrados[$i]['motCoo']."</td>";
						echo "</tr>";
						echo "<tr class='encabezadoTabla'>";
						echo "<td><b><font size='3'  align='center'>&nbsp;</td>";
						echo "<td align='center' width='25%'>N Accion</td>";
						echo "<td  align='center' colspan='2'>Responsable</td>";
						echo "<td align='center' colspan='2'>Fecha implementacion</td>";
						echo "</tr>";

						for($j=0;$j<$num;$j++)
						{
							$k++;
							$row=mysql_fetch_row($err);


							echo "<tr>";
							echo "<td>&nbsp;</td>";
							echo "<td class='fila2' align='center' width='25%'>$row[0]</td>";
							echo "<td class='fila2' align='center' colspan='2'>$row[1]</td>";
							echo "<td class='fila2' align='center' colspan='2'>$row[2]</td>";
							echo "</tr>";

						}
						echo "</table>";
					}
				}


				for($i=0;$i<$contado4;$i++)
				{

					$query =" SELECT cacnum, cacres, cacfver FROM " .$empresa."_000023 where id_motivo=".$otros[$i]['motNum']." and caccon=".$otros[$i]['id']." and cacest='INGRESADO' and cacfver > '".date('Y-m-d')."' ";
					$err=mysql_query($query,$conex);
					$num=mysql_num_rows($err);

					if  ($num >0) //se llenan los vectores según el estado del comentario
					{
						echo "<table border=0 cellspacing=2 cellpadding=0 align=center width='1000'>";
						echo "<tr class='encabezadoTabla'>";
						echo "<tdwidth='15%' ><a href='investigacion.php?idMot=".$otros[$i]['motNum']."&idCom=".$otros[$i]['idCom']."&usuario=".$usuarios."'  align='right'>N Comentario: ".$otros[$i]['num']."-".$otros[$i]['motNum']."</a></td>";
						echo "<td  width='25%'  colspan='2'>Unidad: ".$otros[$i]['motAre']."&nbsp;</td>";
						echo "<td  width='30%' >Implicado: ".$otros[$i]['motEnv']." </td>";
						echo "<tdwidth='25%' >Coordinador: ".$otros[$i]['motCoo']."</td>";
						echo "</tr>";
						echo "<tr class='encabezadoTabla'>";
						echo "<td><b><font size='3'  align='center'>&nbsp;</td>";
						echo "<td align='center' width='25%'>N Accion</td>";
						echo "<td  align='center' colspan='2'>Responsable</td>";
						echo "<td align='center' colspan='2'>Fecha implementacion</td>";
						echo "</tr>";

						for($j=0;$j<$num;$j++)
						{
							$k++;
							$row=mysql_fetch_row($err);


							echo "<tr>";
							echo "<td>&nbsp;</td>";
							echo "<td class='fila2' align='center' width='25%'>$row[0]</td>";
							echo "<td class='fila2' align='center' colspan='2'>$row[1]</td>";
							echo "<td class='fila2' align='center' colspan='2'>$row[2]</td>";
							echo "</tr>";

						}
						echo "</table>";
					}
				}



			}

			if ($k==0)
			{
				echo "<CENTER>";
				echo "<table align='center' border=0 bordercolor=#000080 style='border:solid;'>";
				echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>NO SE HAN INGRESADO ACCIONES PARA IMPLEMENTAR</td><tr>";
				echo "</table></center></br></br>";
			}

			////////////////////////////////////    COMENTARIOS CERRADOS    /////////////////////////////////////////////////////
			echo "</br></br><div align='center'><div class='encabezadoTabla' align='center' style='width:1000px;'><b>ACCIONES IMPLEMENTADAS</b></div></div></br>";
			$k=0;
			if ($contado2 > 0 or $contado3 > 0) // Si el paciente tiene comentarios en estado ingresados se buscan las acciones para mostrar
			{

				for($i=0;$i<$contado2;$i++)
				{
					$query =" SELECT cacnum, cacres, cacfver FROM " .$empresa."_000023 where id_motivo=".$investigados[$i]['motNum']." and caccon=".$investigados[$i]['id']." and cacfver <= '".date('Y-m-d')."' ";
					$err=mysql_query($query,$conex);
					$num=mysql_num_rows($err);
					//echo $query;

					if  ($num >0) //se llenan los vectores según el estado del comentario
					{
						echo "<table border=0 cellspacing=2 cellpadding=0 align=center width='1000'>";
						echo "<tr class='encabezadoTabla'>";
						echo "<td width='15%' ><a href='investigacion.php?idMot=".$investigados[$i]['motNum']."&idCom=".$investigados[$i]['idCom']."&usuario=".$usuarios."'  align='right'>N Comentario: ".$investigados[$i]['num']."-".$investigados[$i]['motNum']."</font ></a></td>";
						echo "<td  width='25%'  colspan='2'>Unidad: ".$investigados[$i]['motAre']."&nbsp;</font ></td>";
						echo "<td  width='30%' >Implicado: ".$investigados[$i]['motEnv']." </font ></td>";
						echo "<td width='25%' >Coordinador: ".$investigados[$i]['motCoo']."</font ></td>";
						echo "</tr>";
						echo "<tr class='encabezadoTabla'>";
						echo "<td>&nbsp;</td>";
						echo "<td align='center' width='25%'>N Accion</td>";
						echo "<td  align='center' colspan='2'>Responsable</td>";
						echo "<td align='center' colspan='2'>Fecha de Implementacion</td>";
						echo "</tr>";

						for($j=0;$j<$num;$j++)
						{
							$k++;
							$row=mysql_fetch_row($err);

							echo "<tr>";
							echo "<td>&nbsp;</td>";
							echo "<td class='fila2' align='center' width='15%'>$row[0]</td>";
							echo "<td class='fila2' align='center' colspan='2'>$row[1]</td>";
							echo "<td class='fila2' align='center' colspan='2'>$row[2]</td>";
							echo "</tr>";

						}
						echo "</table></br></br>";
					}
				}

				for($i=0;$i<$contado3;$i++)
				{
					$query =" SELECT cacnum, cacres, cacfver FROM " .$empresa."_000023 where id_motivo=".$cerrados[$i]['motNum']." and caccon=".$cerrados[$i]['id']." and cacfver <= '".date('Y-m-d')."' ";
					$err=mysql_query($query,$conex);
					$num=mysql_num_rows($err);
					//echo $query;

					if  ($num >0) //se llenan los vectores según el estado del comentario
					{
						echo "<table align='center' border='1'>";
						echo "<tr class='encabezadoTabla'>";
						echo "<td width='15%' ><a href='investigacion.php?idMot=".$cerrados[$i]['motNum']."&idCom=".$cerrados[$i]['idCom']."&usuario=".$usuarios."'  align='right'>N Comentario: ".$cerrados[$i]['num']."-".$cerrados[$i]['motNum']."</a></td>";
						echo "<td  width='25%'  colspan='2'>Unidad: ".$cerrados[$i]['motAre']."&nbsp;</td>";
						echo "<td width='30%' >Implicado: ".$cerrados[$i]['motEnv']." </td>";
						echo "<td width='25%' >Coordinador: ".$cerrados[$i]['motCoo']."</td>";
						echo "</tr>";
						echo "<tr class='encabezadoTabla'>";
						echo "<td>&nbsp;</td>";
						echo "<td align='center' width='25%' >N Accion</td>";
						echo "<td  align='center' colspan='2' >Responsable</td>";
						echo "<td align='center' colspan='2' >Fecha de Implementacion</td>";
						echo "</tr>";

						for($j=0;$j<$num;$j++)
						{
							$k++;
							$row=mysql_fetch_row($err);


							echo "<tr>";
							echo "<td>&nbsp;</td>";
							echo "<td class='fila2' align='center' width='15%'>$row[0]</td>";
							echo "<td class='fila2'align='center' colspan='2'>$row[1]</td>";
							echo "<td class='fila2' align='center' colspan='2'>$row[2]</td>";
							echo "</tr>";

						}
						echo "</table></br></br>";
					}
				}



			}
			if ($k==0)
			{
				echo "<CENTER>";
				echo "<table align='center' border=0 bordercolor=#000080 style='border:solid;'>";
				echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>NO EXISTEN ACCIONES IMPLEMENTADAS </td><tr>";
				echo "</table></center></br></br>";
			}
		}

		if ($idUsuarios=='40' or $idUsuarios=='47' )
		{
			echo "<form action='auditoria.php' method=post>
					<table align=center>
						<tr>
							<td><INPUT type='submit' value='Retornar' style='width:100'></td>
							<td><INPUT type='button' onClick='cerrarVentana()' value='Cerrar' style='width:100'>
						</tr>
					</table
				 </form>";
		}
		else
		{
			echo "<CENTER>";
			echo "<table align='center' border=0 bordercolor=#000080 style='border:solid;'>";
			echo "<tr><td colspan='2' align='center'><font size=3 color='#000080' face='arial'><b>SU AREA NO TIENE ACCESO A LA INFORMACION  </td><tr>";
			echo "</table></center></br></br>";

			echo "<form action='auditoria.php' method=post>
					<table align=center>
						<tr>
							<td><INPUT type='submit' value='Retornar' style='width:100'></td>
							<td><INPUT type='button' onClick='cerrarVentana()' value='Cerrar' style='width:100'>
						</tr>
					</table
				 </form>";
		}
	}
}

?>

</body>

</html>







