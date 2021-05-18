<html>
<head>
<title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066" onload=ira()>
<!-- Estas 5 lineas es para que funcione el Calendar al capturar fechas -->
    <link rel="stylesheet" href="../../zpcal/themes/winter.css" />
    <script type="text/javascript" src="../../zpcal/src/utils.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar.js"></script>
    <script type="text/javascript" src="../../zpcal/src/calendar-setup.js"></script>
    <script type="text/javascript" src="../../zpcal/lang/calendar-sp.js"></script> 
  
<center>
<?php
include_once("conex.php");
include_once("root/comun.php");
$wmovhos = consultarAliasPorAplicacion($conex, $wemp_pmla, "movhos");

/*****************************************
*     REPORTE de COTIZACION DE           *
*    MEDICAMENTOS Y DISPOSITIVOS MEDICOS *
*    SERVICIOS FARMACEUTICOS             *	                             *
*****************************************/
//=======================================================
//AUTOR			:Gabriel Agudelo Zapata
$wautor="Gabriel Agudelo Zapata";
//FECHA CREACION :Abril 13 de 2012
//FECHA ULTIMA ACTUALIZACION 	:
$wactualiz="(Versión 2012-01-23)";
/*DESCRIPCION	:Trae el reporte entre POR AÑO DE COTIZACION DE MEDICAMENTOS Y DISPOSITIVOS MEDICOS*/

 session_start();
 if (!isset($_SESSION['user']))
    echo "error";
   else
	   { 
		$key = substr($user,2,strlen($user));
		

		

		$wfecha=date("Y-m-d");

		echo "<form action='repcotfarxano.php?wemp_pmla=".$wemp_pmla."' name=repcotfarxano method=post>";
		echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
		
			if (!isset($wfec1) or $wfec1=='')
			 {

				//Cuerpo de la pagina
			 	echo "<table align='center' border=1>";
			    echo "<tr>";
				echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>COTIZACION DE MEDICAMENTOS Y DISPOSITIVOS MEDICOS POR AÑO<br></font></b>";   
				echo "</tr>";

				
			  if (!isset($wfec1))   // Si no esta seteada entonces la inicializo con el año actual
			    $wfec1=date('Y'); 
			    echo "<tr><td align=CENTER colspan=4 bgcolor=#DDDDDD><b><font text color=#003366 size=2>Año de Cotizacion<br></font></b>";   
			  	echo "<input type='TEXT' name='wfec1' size=4 maxlength=4  id='wfec1'  value=".$wfec1." class=tipo3></td>";
				echo "<tr><td align=center bgcolor=#cccccc colspan=4><input type='submit' value='Generar'></td>";          //submit osea el boton de Generar o Aceptar
			    echo "</tr>";
			    echo "</table>";
			   
			 }	
		
		  else
		     {
			   	
                $query = "   Select Cotcod,Cotmar,Cotpre,Cotreg,Cotvec,Cotcum,Cotcot,Cotiva,Artcom,Artuni,Artgru,Usucod,Usunit,Descripcion "
               		   ."    from cotizaci_000003,".$wmovhos."_000026,cotizaci_000005,usuarios "
				       ."    where cotano = '".$wfec1."' "
				       ."    and cotcod = artcod "
				       ."    and cotnit = usunit "
				       ."    and usucod = codigo "
					   ."    and Activo = 'A' " 
					   ."    union " 
					   ."    Select Cotcod,Cotmar,Cotpre,Cotreg,Cotvec,Cotcla,Cotcot,Cotiva,Artcom,Artuni,Artgru,Usucod,Usunit,Descripcion "
               		   ."    from cotizaci_000004,".$wmovhos."_000026,cotizaci_000005,usuarios "
				       ."    where cotano = '".$wfec1."' "
				       ."    and cotcod = artcod "
				       ."    and cotnit = usunit "
				       ."    and usucod = codigo "
					   ."    and Activo = 'A' " 
					   ."    union " 
					   ."    Select Cotcod,Cotmar,Cotpre,Cotreg,Cotvec,Cotcla,Cotcot,Cotiva,Artcom,Artuni,Artgru,Usucod,Usunit,Descripcion "
               		   ."    from cotizaci_000007,".$wmovhos."_000026,cotizaci_000005,usuarios "
				       ."    where cotano = '".$wfec1."' "
				       ."    and cotcod = artcod "
				       ."    and cotnit = usunit "
				       ."    and usucod = codigo "
					   ."    and Activo = 'A' " ;
		       
	       		$err = mysql_query($query,$conex) or die (mysql_errno());
		   		$num = mysql_num_rows($err);
				

				echo "<table border=1 align=center>";
				echo "<tr><td colspan=14 align=center bgcolor=#DBDFF8><b>SERVICIOS FARMACEUTICOS</b></td></tr>";
				echo "<tr><td colspan=14 align=center bgcolor=#DBDFF8><b>COTIZACION DE MEDICAMENTOS Y DISPOSITIVOS MEDICOS</b></td></tr>";
				echo "<tr>";
				echo "<td colspan=14 align=center bgcolor=#DBDFF8><b>AÑO: ".$wfec1." </b></td>";
				echo "</tr>"; 	
		
				echo "<tr>";
				echo "<td align=center bgcolor=#DBDFF8><b>CODIGO</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>MARCA</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>PRESENTACION</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>REG.INVIMA</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>VENCIMIENTO</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>CODIGO CUM</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>VALOR</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>IVA</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>DESCRIPCION</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>UNIDAD</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>GRUPO</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>CODIGO PROVEEDOR</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>NIT</b></td>";
				echo "<td align=center bgcolor=#DBDFF8><b>DESCRIPCION</b></td>";
				echo "</tr>";
			
				$cont = 0;
				for ($i=0;$i<$num;$i++)
				   {	
					// color de fondo 
				
					if (is_int ($cont/2))  // Cuando la variable cont es par coloca este color
					  $wcf="DDDDDD";
					else
					  $wcf="CCFFFF";           
					echo "<tr bgcolor=".$wcf.">";
					
					$cont++;
					$row = mysql_fetch_array($err);
					
					 $query1 = "   Select count(*) from cotizaci_000011 where modano = '".$wfec1."' and modusr = '1-".$row[11]."' and modcod = '".$row[0]."' "; 
               		 $err1 = mysql_query($query1,$conex) or die (mysql_errno());
					 $num1 = mysql_num_rows($err1);
					 for ($j=0;$j<$num1;$j++)
					     $row1 = mysql_fetch_array($err1);
						
						echo "<tr>";
						echo "<td align=center color=#FFFFFF bgcolor=".$wcf.">".$row[0]."</td>";
						echo "<td align=center color=#FFFFFF bgcolor=".$wcf.">".$row[1]."</td>";
						echo "<td align=center color=#FFFFFF bgcolor=".$wcf.">".$row[2]."</td>";
						echo "<td align=center color=#FFFFFF bgcolor=".$wcf.">".$row[3]."</td>";
						echo "<td align=center color=#FFFFFF bgcolor=".$wcf.">".$row[4]."</td>";
						echo "<td align=center color=#FFFFFF bgcolor=".$wcf.">".$row[5]."</td>";
						//aca pregunto si en la tabla cotizaci_000011 a habido modificaciones para este articulo con el mismo año y codigo de usuario
	
						if ($row1[0] > 0)
						{
							//echo "<td align=center color=#FFFFFF bgcolor = #3FC541><A HREF='repcotfarxdet.php?wmodcod=".$row[0]."&wmodusr=".$row[11]."&wmodano=".$wfec1."&wmoddes=".$row[8]."&wmodpro=".$row[13]."' target='_blank' >".$row[6]." (".$row1[0].") </A></td>";
							echo "<td align=center color=#FFFFFF bgcolor = #3FC541><A HREF='repcotfarxdet.php?wmodcod=".$row[0]."&wmodusr=".$row[11]."&wmodano=".$wfec1."&wmoddes=".$row[8]."&wmodpro=".$row[13]."' target='repcotfarxdet.php' onclick=\"window.open(this.href, this.target, ' width=800, height=500, menubar=no,toolbar=yes');return false;\" >".$row[6]." (".$row1[0].") </A></td>";
						}
						else 
						{	
							echo "<td align=center color=#FFFFFF bgcolor=".$wcf.">".$row[6]."</td>";
						}
						
						echo "<td align=center color=#FFFFFF bgcolor=".$wcf.">".$row[7]."</td>";
						echo "<td align=center color=#FFFFFF bgcolor=".$wcf.">".$row[8]."</td>";
						echo "<td align=center color=#FFFFFF bgcolor=".$wcf.">".$row[9]."</td>";
						echo "<td align=center color=#FFFFFF bgcolor=".$wcf.">".$row[10]."</td>";
						echo "<td align=center color=#FFFFFF bgcolor=".$wcf.">".$row[11]."</td>";
						echo "<td align=center color=#FFFFFF bgcolor=".$wcf.">".$row[12]."</td>";
						echo "<td align=center color=#FFFFFF bgcolor=".$wcf.">".$row[13]."</td>";
					echo "</tr>";
				   			    
				   }
			   echo "</table>"; 
		     }
	   }
?>
</body>
</html>
