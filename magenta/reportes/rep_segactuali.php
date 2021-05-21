<html>
<head>
<title>Seguimiento de actualización - magenta</title>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>  
<script src="efecto.php"></script>
<script>
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
          $(document).ready(function() {
           $("#fec1, #fec2").datepicker({
           showOn: "button",
           buttonImage: "../../images/medical/root/calendar.gif",
           buttonImageOnly: true,
           maxDate:"+1D"
        });
    });
</script>
</head>
<font face='arial'>
<script type="text/javascript">
	function enter()
	{
		document.forms.rep_segactuali.submit();
	}
</script>

<?php
include_once("conex.php");
include_once("root/comun.php");

/*******************************************************************************************************************************************
*                                                REPORTE SEGUIMIENTO DE ACTUALIZACIONES                                                    *
********************************************************************************************************************************************/

// ==========================================================================================================================================
// PROGRAMA				      : rep_segactuali.                                                                                                   |
// AUTOR				          : Ing. Gustavo Alberto Avendano Rivera.                                                                           |
// FECHA CREACION			  : NOVIEMBRE 22 DE 2007.                                                                                             |
// FECHA ULTIMA ACTUALIZACION  : 17 de Diciembre de 2007.                                                                                   |
//                              13 - Se adiciona el usuario y el ingreso de unix.                                                           |
//                              17 - Se adiciona (*) para saber cual paciente es interno cuando le graban por ayudas.                       |
// DESCRIPCION			      : Este reporte sirve para observar y hacerle un seguimiento a los pacientes afines.                               |
//                                                                                                                                          |
// TABLAS UTILIZADAS                                                                                                                        |
// aymov                       : Tabla de Movimiento de ayudas diagnosticas.                                                                |
// inpac                       : Tabla de Pacientes Activos.                                                                                |
// inpaci                      : Tabla de Pacientes Inactivos.                                                                              |
// inmegr                      : Tabla de Pacientes Egresados.                                                                              |
// aycardet                    : Tabla de Detalles de movimiento.                                                                           |
// inmegr                      : Tabla de pacientes, para saber cuando fue egresado.                                                        |
//==========================================================================================================================================
/*  Actualizaciones:
      2016-05-06 (Arleyda Insignares C.)
                 -Se cambia encabezado y titulos con ultimo formato.
===========================================================================================================================================*/

$wactualiz="2016-05-06";
$wautor="Ing. Gustavo A. Avendaño R.";

session_start();
if(!isset($_SESSION['user']))
 {
  echo "error";
 }
else
{
  include_once("root/comun.php");
  $wmovhos     = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");
  $wcostosyp   = consultarAliasPorAplicacion($conex, $wemp_pmla, "COSTOS");
  $wmagenta    = consultarAliasPorAplicacion($conex, $wemp_pmla, "afinidad");
  
  /////////////////////////////////////////////////encabezado general///////////////////////////////////
  $titulo = "SISTEMA DE COMENTARIOS Y SUGERENCIAS";
  encabezado($titulo,$wactualiz, "clinica");  

  $empresa='root';
  $conex = obtenerConexionBD("matrix");
  $fec1=date('Y-m-d');
  $fec2=date('Y-m-d');
 
 if (!isset($fec1) or $fec1 == '' or !isset($fec2) or $fec2 == '')

 {
    echo "<table border=0 align=center size=100%>";
    echo "</br><div align='center'><div align='center' class='fila1' style='width:600px;'><font size='4'><b>SEGUIMIENTO DE ACTUALIZACIONES - MAGENTA</b></font></div></div></br></br>";
    echo "</table></br></br></br>";
  	
    echo "<table align='center'>";
    echo "<tr><td align=center><font size=3>INGRESE POR FAVOR LAS FECHAS, DENTRO DEL SIGUIENTE RANGO:</font></td>";
    echo "</table></br></br>";
    echo "<fieldset align=center></br>";   	
    echo "<form name='rep_segactuali.php?wemp_pmla=".$wemp_pmla."' action='' method=post>";

  ////////////////////////////////////////////////////////////////////////////////////////////////////////////////RANGO DE FECHAS

 //Cuerpo de la pagina
  echo "<table align='center' border=0>";
	echo "<br>";
 	echo "<br>";

 	//Fecha inicial
 	echo "<tr>";
 	echo "<td class='fila1' width=190>Fecha Inicial</td>";
 	echo "<td class='fila2' align='center' width=200><input type='text' readonly='readonly' id='fec1' name='fec2' value='".$fec1."' class=tipo3 >";
 	//campoFecha("fec1");
 	echo "</td></tr>";
 		
 	//Fecha final
 	echo "<tr>";
 	echo "<td class='fila1'>Fecha Final</td>";
 	echo "<td class='fila2' align='center'><input type='text' readonly='readonly' id='fec2' name='fec2' value='".$fec2."' class=tipo3 >";
 	//campoFecha("fec2");
 	echo "</td></tr>";
   
   echo "<TABLE align=center><tr>";
   echo "<tr><td align=center colspan=14><font size='2'  align=center face='arial'><input type='submit' name='aceptar' value='GENERAR' ></td></tr>";
   echo "</TABLE>";
   echo "</td>";
   echo "</tr>";
   echo "</form>";
   echo "</fieldset>";
     
  }
 else // Cuando ya estan todos los datos escogidos
  {
   ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
  
   echo "<table border=0 align=center cellpadding='0' cellspacing='0' size=100%>";
   echo "</br><div align='center'><div align='center' class='fila1' style='width:1000px;'><font size='4'><b>SEGUIMIENTO DE ACTUALIZACIONES - MAGENTA desde $fec1 hasta $fec2</b></br>";
   //echo "<tr><td align=center ><A HREF='rep_segactuali.php'><font size=4 color='#FF00FF'>SEGUIMIENTO DE ACTUALIZACIONES - MAGENTA desde $fec1 hasta $fec2</font></a></td></tr>";
   //echo "<tr><td align=center ><A NAME='Arriba'><font size=2 color='#FF00FF'> segactuali.php</font></a></td></tr>";
   echo "</table></br>";

   echo "<center><img SRC='/MATRIX/images/medical/Magenta/AAA.gif' ><center>";
   
   echo "<center><td bgcolor='#ADD8E6'align=center><font size=2>(*) Paciente Interno</font></td><center>";
   echo "<table align=center cellpadding='0' cellspacing='0'>";
   echo "<Tr class='encabezadotabla'>";
   echo "<td align=center><font size=2>Documento</font></td>";
   echo "<td align=center width=5%><font size=2>Hist</font></td>";
   echo "<td align=center width=4%><font size=2>Ing</font></td>";
   echo "<td align=center width=4%><font size=2>UsuIng</font></td>";
   echo "<td align=center width=5%><font size=2>Nomusu</font></td>";
   echo "<td align=center width=4%><font size=2>Apellido</font></td>";
   echo "<td align=center width=8%><font size=2>Nombres</font></td>";
   echo "<td align=center width=7%><font size=2>Apellido_1</font></td>";
   echo "<td align=center width=7%><font size=2>Apellido_2</font></td>";
   echo "<td align=center width=8%><font size=2>Reconocieron</font></td>";
   echo "<td align=center width=8%><font size=2>Actualizaron</font></td>";
   echo "<td align=center width=11%><font size=2>Nombre_Ccosto</font></td>";
   echo "<td align=center width=5%><font size=2>Tipo</font></td>";
   echo "<td align=center width=8%><font size=2>Fecha</font></td>";
   echo "<td align=center width=5%><font size=2>Codigo_Usua</font></td>";
   echo "<td align=center width=14%><font size=2>Nombre_Usuario</font></td>";
   echo "<td align=center width=7%><font size=2>Tipo</font></td>";
   echo "</Tr >";
	
   $conexi=odbc_connect('facturacion','','')
	    or die("No se realizo conexión con la BD de Facturación");
	
	$query =   " SELECT * "
              ."   FROM aymov "
              ."  WHERE movfec between '".$fec1."' AND '".$fec2."' "
              ."    AND movanu = '0' "
             ." INTO TEMP aymov_tmp ";
	$err = odbc_do($conexi,$query) or die("ERROR EN QUERY tem1");
	
	$query1 =   " SELECT * "
              ."   FROM inmegr "
              ."  WHERE egring between '".$fec1."' AND '".$fec2."' "
              ." INTO TEMP inmegr_tmp ";
	$err1 = odbc_do($conexi,$query1) or die("ERROR EN QUERY tem2");
	
   $queralta = " SELECT '01' fuo,pachis his,pacnum num,pacced ced,serccocco cco,pacfec fec,pachor hor,'' tipo "
              ."   FROM inpac,insercco"
              ."  WHERE pacser = serccoser"
              ."    and pacfec <= '".$fec2."' "
              ."  GROUP BY 1,2,3,4,5,6,7,8 "
              ." UNION ALL "
              ." SELECT '01' fuo,egrhis his,egrnum num,pacced ced,serccocco cco,egring fec,egrhoi hor,'' tipo "
              ."   FROM inmegr_tmp,inpaci,insercco"
              ."  WHERE egrhis = pachis "
              ."    AND egrsin = serccoser"
              ."  GROUP BY 1,2,3,4,5,6,7,8 "
              ." UNION ALL"
              ." SELECT movfue fuo,movdoc his,0 num,movced ced,movcco cco,movfec fec,movhor hor, '' tipo"
              ."   FROM aymov_tmp"
              ."  WHERE movtip <> 'I' "
              ."  GROUP BY 1,2,3,4,5,6,7,8 "
              ." UNION ALL"
              ." SELECT movfue fuo,movdoc his,0 num,movced ced,movcco cco,movfec fec,movhor hor,'*' tipo "
              ."   FROM aymov_tmp"
              ."  WHERE movtip = 'I' "
              ."  GROUP BY 1,2,3,4,5,6,7,8 "
              ."  ORDER BY 4,5,2,3 "
              ." INTO TEMP alta1";
           
  
   $erralta = odbc_do($conexi,$queralta) or die("ERROR EN QUERY");
   
   $queralta1 = "SELECT fuo,his,num,ced,cco,fec,hor,movegruad uad,tipo "
              ."   FROM alta1,outer aymovegr"
              ."  WHERE fuo = movegrfue"
              ."    AND his = movegrdoc"
              ."    AND fuo <> '01'"
              ."  UNION ALL "
              ." SELECT fuo,his,num,ced,cco,fec,hor,pacusuusu uad,tipo "
              ."   FROM alta1,outer inpacusu"
              ."  WHERE his = pacusuhis"
              ."    AND num = pacusunum"
              ."    AND fuo = '01'"
              ." ORDER BY 8 "  
              ." INTO TEMP alta2";
              
   $erralta1 = odbc_do($conexi,$queralta1) or die("ERROR EN QUERY");
   
   $queralta2 = "SELECT fuo,his,num,ced,cco,fec,hor,'.' uad,tipo "
              ."   FROM alta2,outer spmov"
              ."  WHERE uad is null"
              ."    AND fuo = movfue"
              ."    AND his=movdoc"
              ."  UNION ALL "
              ." SELECT fuo,his,num,ced,cco,fec,hor,uad,tipo"
              ."   FROM alta2 "
              ."  WHERE uad is not null"
              ." ORDER BY 4,6,7"
             ." INTO TEMP alta3"; 
   
   $erralta2 = odbc_do($conexi,$queralta2) or die("ERROR EN QUERY");
   
   $quer1="SELECT fuo,his,num,ced,cco,fec,hor,uad,tipo"
         ."  FROM alta3"
         ."   WHERE tipo is not null "
		 ." UNION "
		 ."SELECT fuo,his,num,ced,cco,fec,hor,uad,'.' as tipo"
         ."  FROM alta3"
         ." WHERE tipo is null   ";

   $err1 = odbc_do($conexi,$quer1) or die("ERROR EN QUERY 1");
   $num1 = odbc_num_fields($err1);
   
   $contlin= 1;
   
   if ($num1 > 0)
   {
   while(odbc_fetch_row($err1))
   {   
   $row1 = array();
   	
   for ($j=1;$j<=$num1;$j++)
   {
	$row1[$j-1] = odbc_result($err1,$j);
   }
   
    $query="SELECT idenom,ideap1 "
		  ."  FROM siide "
		  ." WHERE idecod = '".odbc_result($err1,8)."'"
		  ."AND ideap1 is not null"
		  ." UNION "
		  ." SELECT idenom,'.' as ideap1 "
		  ."  FROM siide "
		  ." WHERE idecod = '".odbc_result($err1,8)."'"
		  ."AND ideap1 is null"; 
		  	 		  
		  $err_2 = odbc_do($conexi,$query);
		  $num2 = odbc_num_fields($err_2);	
		 

          $row2 = array();
		  
			if($num2>0)
			{
			   for ($j=1;$j<=$num2;$j++)
				 {
				   $row2[$j-1] = odbc_result($err_2,$j);
				   				   				   
				 } 
			}
			else
			{
				$row2[0] = "";
				$row2[1] = "";
			}

   $bort = "DROP TEMPORARY TABLE IF EXISTS temp1";
    
   $errb = mysql_query($bort, $conex) or die (" Error: " . mysql_errno() . " - en el query: " . $bort . " - " . mysql_error());
   
   $q3 = "CREATE TEMPORARY TABLE if not exists temp1  "
		."(index idx_clidoc( clidoc,orihis, oriing ))"
	    ." SELECT clidoc,clinom,cliap1,cliap2,'".$row1[1]."' as orihis,'".$row1[2]."' as oriing,'".$row1[4]."' as cco,clitip,'".$row1[7]."' as usua,'".$row2[0]."' as nomu,'".$row2[1]."' as ape1,'".$row1[8]."' as tipo  "
	    ."   FROM ".$wmagenta."_000008 LEFT JOIN root_000037 "
	    ."     ON ".$wmagenta."_000008.clidoc = root_000037.oriced" 
	    ."  WHERE ".$wmagenta."_000008.clidoc = '".$row1[3]."' "
	    ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12 "
	    ."  ORDER BY 1 "; 
   
   $err3 = mysql_query($q3, $conex) or die (" Error: " . mysql_errno() . " - en el query: " . $q3 . " - " . mysql_error());
  
   $bort = "DROP TEMPORARY TABLE IF EXISTS temp3";
   $errb = mysql_query($bort, $conex) or die("ERROR EN QUERY temporal ");
   
   $q3 = "CREATE TEMPORARY TABLE if not exists temp3 "
        ."(index idx_clidoc( clidoc,orihis, oriing ))"
	    ." SELECT clidoc,clinom,cliap1,cliap2,'".$row1[1]."' as orihis,'".$row1[2]."' as oriing,'".$row1[4]."' as cco,clitip,'".$row1[7]."' as usua,'".$row2[0]."' as nomu,'".$row2[1]."' as ape1,'".$row1[8]."' as tipo "
	    ."   FROM ".$wmagenta."_000008 LEFT JOIN root_000037 "
	    ."     ON ".$wmagenta."_000008.clidoc = root_000037.oriced" 
	    ."  WHERE ".$wmagenta."_000008.clidoc = '".$row1[3]."' "
	    ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12 "
	    ."  ORDER BY 1"; 
   $err3 = mysql_query($q3, $conex) or die (" Error: " . mysql_errno() . " - en el query_: " . $q3 . " - " . mysql_error());
  
   $bort = "DROP TEMPORARY TABLE IF EXISTS temp4";
   $errb = mysql_query($bort, $conex) or die("ERROR EN QUERY temporal ");
   
   $q3 = "CREATE TEMPORARY TABLE if not exists temp4 "
		."(index idx_clidoc( clidoc,orihis, oriing ))"
	    ." SELECT clidoc,clinom,cliap1,cliap2,'".$row1[1]."' as orihis,'".$row1[2]."' as oriing,'".$row1[4]."' as cco,clitip,'".$row1[7]."' as usua,'".$row2[0]."' as nomu,'".$row2[1]."' as ape1,'".$row1[8]."' as tipo "
	    ."   FROM ".$wmagenta."_000008 LEFT JOIN root_000037 "
	    ."     ON ".$wmagenta."_000008.clidoc = root_000037.oriced" 
	    ."  WHERE ".$wmagenta."_000008.clidoc = '".$row1[3]."' "
	    ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12 "
	    ."  ORDER BY 1 "; 
   $err3 = mysql_query($q3, $conex) or die("ERROR EN QUERY temporal ");

   $q4 = " SELECT clidoc,clinom,cliap1,cliap2,orihis,oriing,cco,clitip,usua,nomu,ape1,tipo"
	    ."   FROM temp3 " 
	    ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12 "
	    ."  ORDER BY 1 "; 
       
   $err4 = mysql_query($q4, $conex) or die("ERROR EN QUERY despues del temporal");
   $num4 = mysql_num_rows($err4);
   
   if ($num4 > 0 )
   {	
   	 
	  $bort11 = "DROP TEMPORARY TABLE IF EXISTS temp100";
    
      $errb11 = mysql_query($bort11, $conex) or die("ERROR EN QUERY temp100");
	 
										//nomu,ape1
	 
      $q11=" CREATE TEMPORARY TABLE if not exists temp100 "
		   ."(index idx_clidoc( clidoc,orihis, oriing ))"
           ." SELECT clidoc,clinom,cliap1,cliap2,cco as eyrsde,'Entrega' as eyrtip,1 as eyrnum,'".$row1[5]."' as fecha,'".$row1[6]."' as hora,orihis,oriing,clitip,usua,nomu,ape1,tipo"
	       ."   FROM temp1" 
           ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16"
	       ."  UNION "
           ." SELECT clidoc,clinom,cliap1,cliap2,eyrsde,eyrtip,eyrnum,".$wmovhos."_000017.Fecha_data as fecha,".$wmovhos."_000017.Hora_data as hora,orihis,oriing,clitip,usua,nomu,ape1,tipo"
	       ."   FROM temp4,".$wmovhos."_000017"
	       ."  WHERE temp4.orihis=".$wmovhos."_000017.eyrhis"
	       ."    AND ".$wmovhos."_000017.eyring='".$row1[2]."'"
	       ."    AND ".$wmovhos."_000017.eyrest='on'"
	       ."    AND eyrtip='Recibo'"
	       ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16";

	  $err11 = mysql_query($q11, $conex)  or die ("Error: " . mysql_errno() . " - en el query: " . $q11 . " - " . mysql_error());
	 	
	  $bort1 = "DROP TEMPORARY TABLE IF EXISTS temp2";
    
																																	//nomu,ape1
      $errb1 = mysql_query($bort1, $conex) or die("ERROR EN QUERY temporal 2");
       
	  $q1 = "CREATE TEMPORARY TABLE if not exists temp2 "
			."(index idx_clidoc( clidoc,orihis, oriing ))"
	       ." SELECT clidoc,clinom,cliap1,cliap2,actrec,actact,eyrsde,eyrtip,eyrnum,fecha,hora,".$wmagenta."_000001.Seguridad,clitip,orihis,oriing,usua,nomu,ape1,tipo"
	       ."   FROM temp100 LEFT JOIN ".$wmagenta."_000001" 
	       ."     ON temp100.clidoc = ".$wmagenta."_000001.actdoc"
	       ."    AND temp100.orihis = ".$wmagenta."_000001.acthis"
	       ."    AND ".$wmagenta."_000001.actfue='".$row1[0]."'"
	       ."    AND temp100.eyrsde = ".$wmagenta."_000001.actcco"
	       ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19";
	       
	  
	   $cedant = $row1[3];    
	  
	 
	     
	$err=mysql_query($q1,$conex) or die ("Error: " . mysql_errno() . " - en el query: " . $q1 . " - " . mysql_error());
	
	$bort2 = "DROP TEMPORARY TABLE IF EXISTS temp21";
    
    $errb2 = mysql_query($bort2, $conex) or die("ERROR EN QUERY temporal 2");
					
					//nomu,ape1
					
	$q2 ="CREATE TEMPORARY TABLE if not exists temp21 " 
		 ."(index idx_clidoc( clidoc,orihis, oriing ))"
	     ." SELECT clidoc,clinom,cliap1,cliap2,actrec,actact,eyrsde,cconom,eyrtip,eyrnum,fecha,hora,temp2.Seguridad,clitip as tip,orihis,oriing,usua,nomu,ape1,tipo"
	     ."   FROM temp2 LEFT JOIN ".$wcostosyp."_000005"
	     ."     ON eyrsde=ccocod"
	     ."  ORDER BY 10,1,2,3,4";
	
	$err2=mysql_query($q2,$conex) or die("ERROR EN QUERY q2");
	
																																//nomu,ape1
	$q21 = " SELECT clidoc,clinom,cliap1,cliap2,actrec,actact,eyrsde,cconom,eyrtip,eyrnum,fecha,hora,Seguridad,Descripcion,tip,orihis,oriing,usua,nomu,ape1,tipo"
	     ."   FROM temp21 LEFT JOIN usuarios"
	     ."     ON Mid(Seguridad, 3, instr(Seguridad, '-' ) + 7 ) = codigo"
	     ."  GROUP BY 1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21"
	     ."  ORDER BY 15,11,12,1,2,3,4";
	     
	$err21=mysql_query($q21,$conex) or die("ERROR EN QUERY q21");
	$num=mysql_num_rows($err21);
	$num4=0;
	
	for ($i=1;$i<=$num;$i++)
	 {
	  if (is_int ($contlin/2))
	   {
	   	$wcf="fila1";  // color de fondo
	   }
	  else
	   {
	   	$wcf="fila2"; // color de fondo
	   }

	  $contlin=$contlin+1; 
	  $row = mysql_fetch_array($err21);
	   
	  $rec='';
	  $act='';
	  
	  if ($row[4] == 'on')
	  {
	   $rec='RECONOCIDO';
      }
	  else 
	  {
	   $rec='NO Reconocido';
	  }
	  
	  if ($row[5] == 'on')
	  {
	   $act='ACTUALIZADO';
	  }
	  else 
	  {
	   $act='NO Actualizado';
	  }
	  
    echo "<Tr class='$wcf'>";
	  echo "<td align=center><font size=1>$row[0]</font></td>";
	  echo "<td align=center><font size=1>$row[15]</font></td>";
	  echo "<td align=center><font size=1>$row[16]</font></td>";
	  echo "<td align=center><font size=1>$row[17]</font></td>";
	  echo "<td align=center><font size=1>$row[18]</font></td>";  //nom act
    echo "<td align=center><font size=1>$row[19]</font></td>";  //ape act
	  echo "<td align=center><font size=1>$row[1]</font></td>";
	  echo "<td align=center><font size=1>$row[2]</font></td>";
	  echo "<td align=center><font size=1>$row[3]</font></td>";
	  echo "<td align=center><font size=1>$rec</font></td>";
  	echo "<td align=center><font size=1>$act</font></td>";
	  echo "<td align=center><font size=1>$row[7]</font></td>";
	  echo "<td align=center><font size=1>$row[8]</font></td>";
    echo "<td align=center><font size=1>$row[10]</font></td>";
	  echo "<td align=center><font size=1>$row[12]</font></td>";
	  echo "<td align=center><font size=1>$row[13]</font></td>";
	  echo "<td align=center><font size=1>$row[14]</font></td>";
	  echo "<td align=center><font color=\"#CC0000\" size=2>$row[20]</font></td>";
	  echo "<Tr >";
	 }
   
    }
    
   }
 
  }
  else // else de $num1>0 
  { 
   echo "<CENTER><fieldset style='border:solid;border-color:#ADD8E6; width=700' >";
   echo "<tr><td colspan='2'><font size=3  face='arial'><b>NINGUN CLIENTE DE AFINIDAD A INGRESADO DURANTE LAS FECHAS INGRESADAS</td><tr>";
   echo "</fieldset>";
  }   
 
 echo "</table>";
 
	odbc_close($conexi);
	odbc_close_all();

 }// cierre del else donde empieza la impresión

}
?>