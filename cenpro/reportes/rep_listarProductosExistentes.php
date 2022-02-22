<HTML>
 <HEAD>
  <TITLE>REPORTE DE PRODUCTOS EXISTENTES</TITLE>
   <script type="text/javascript">
   </script>
 </HEAD>
 <BODY>
 <?php
include_once("conex.php");
 /*     * *******************************************************
     *              LISTADO DE PRODUCTOS EXISTENTES  			*    
     * ******************************************************* */
//==================================================================================================================================
//PROGRAMA                   : rep_listarProductosExistentes.php
//AUTOR                      : Camilo Zapata.
//FECHA CREACION             : Abril 13 de 2012
//FECHA ULTIMA ACTUALIZACION : Mayo 14 de 2012

//DESCRIPCIÓN
//========================================================================================================================================\\
//========================================================================================================================================\\
//     Programa que permite listar los productos existentes en la central de mezclas                                                      \\
//     ** Funcionamiento General:                                                                                                         \\
// Este programa inicialmente evalua cuales iniciales corresponden a los productos que se manejan en la central de mezclas, y permite al usuario
// elegir cual tipo de productos desea listar aunque tambien permite listarlos todos en un solo reporte. 
// Teniendo el tipo  de productos que se desea listar el programa busca las unidades totales existentes de cada producto además de los lotes 
// a los que corresponden. 

//====================================================ACTUALIZACIONES=============================================================================//
//========================================================================================================================================\\
//Daniel CB: (18-Noviembre-2021)-> Se realiza corrección de parametro 01 quemado.
//========================================================================================================================================\\
//================================================================================================================================================
//Camilo Zapata: (14-mayo-2012) -> Se agrego al reporte el campo costo produccion, el cual representa el costo que implicó producir una unidad del 
//								   producto en cada uno de sus lotes, para esto se consultan las tablas 7, 8, y 14 de cenpro, las cuales contienen 
//								   la informacion necesaria del inventario de los articulos ademas de los movimientos, la idea es que se busca 
//								   que articulos se utilizaron en la elaboración del producto y se calcula un precio aproximado del costo de este.
//								   Todos estos campos se hicieron en la funcion generarResultados, teniendo en encuenta la fecha de creación del lote
//								   para buscar cuanto valian los articulos utilizados en el mes inmediatamente anterior a la creación del lote.					
//========================================================================================================================================\\
//================================================================================================================================================
// Camilo Zapata: (17-Abr-2012) -> reacomodación de query, y cambio de filtro en la consulta por tipo, ya no se usa like(para los que empiecen por)
//								   sino que se buscan por código del tipo. ejm: no buscar a los que empiecen por 'DA' sino a los que tengan como 
//								   código del tipo a "03".
//=================================================================================================================================================
// Camilo Zapata: (18-Abr-2012) -> Se agrego en el código que se mostraran los lotes activos vencidos resaltandolos con fondo rojo.
//=================================================================================================================================================


//****************************************************************FUNCIONES**************************************************************************************
//función que agrega el código html necesario para la cabecera. 
 function encabezadoResultados($wtip_pr)
	{
		//encabezado de la tabla de resultados
			echo "<center><table border=0>";
			echo "<tr><td class='encabezadotabla' colspan=8 align='center'><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
			echo "<tr><td class='fila2' colspan=8 align='center'><b>LISTADO DE PRODUCTOS EXISTENTES POR: ".$wtip_pr."</b></td></tr>";
			echo "<tr><td> </td></tr>";
			echo "<tr><td> " ;
				echo "</td>";
				echo "<td> "; 	
				echo "</td>";
				echo "<td> "; 	
				echo "</td>";
				echo "<td class='fila1' colspan=2 align='center'><b>Lote no Vencido</b> "; 	
				echo "</td>";
				echo "<td class='fila2' colspan=1 align='center'><b>Lote no Vencido</b> "; 	
				echo "</td>";
				echo "<td class='fondorojo' colspan=2 align='center'><b>Lote Vencido</b>";
				echo "</td>";
			echo "</tr>";
			echo "<tr>";
			echo "<td class='encabezadotabla' align=center colspan=1 rowspan=2><b>CÓDIGO</b></td>";
			echo "<td class='encabezadotabla' align=center colspan=1 rowspan=2><b>NOMBRE</b></td>";
			//echo "<td class='encabezadotabla' align=center colspan=1 rowspan=2><b>COSTO PROMEDIO</b></td>";
			echo "<td class='encabezadotabla' align=center colspan=1 rowspan=2><b>EXISTENCIAS</b></td>";
			echo "<td class='encabezadotabla' align=center colspan=5><b>LOTES</b></td>";
			    echo"<tr>";
					echo"<td class='encabezadotabla' align=center colspan=1><b>NRO LOTE</b></td>";
					echo"<td class='encabezadotabla' align=center colspan=2><b>VENCIMIENTO<br>(FECHA-HORA)</b></td>";
					echo"<td class='encabezadotabla' align=center colspan=1><b>COSTO PRODUCCION</b></td>";
					echo"<td class='encabezadotabla' align=center colspan=1><b>SALDO</b></td>";
				 echo"</tr>";
			echo "</tr>";
	}

//función que consulta los productos existentes, teniendo en cuenta las unidades y el tipo de producto que desea consultar.	
 function generarResultados($wtip_pr, $conex, $wcenmez, $fhoy)
 {
	global $acumulado;
	
		if($wtip_pr=="Todos")
		{ 
			$wtipo = "";
			$wftipo= "";
		}else
		 {
			$wtipo=explode("-",$wtip_pr);
			$wtipo=$wtipo[0];
			$wftipo = "AND Tipcod='".$wtipo."'"; //variable que me permitirá filtrar por tipo cuando sea necesario.
		 }
	
	//consulta para traer las existencias de cada uno de los productos.
	
	$query ="SELECT Artcod, Artcom, Karexi, Karcos "
                       ." FROM ".$wcenmez."_000001, ".$wcenmez."_000002, ".$wcenmez."_000005 "
                       ."WHERE Arttip = Tipcod "
                       ."  AND Tippro = 'on' "
                       ."  AND Artest = 'on'"
                       ."  AND Karcod = Artcod"
                       ."  AND Karexi > 0 "
					   .$wftipo.""
					   ."  ORDER BY Artcom";        

					
	$rs = mysql_query($query,$conex) or die(mysql_errno().": ".mysql_error());
	$rsnum = mysql_num_rows($rs);
	
	if($rsnum > 0)
	{	
		for($i = 0; $i < $rsnum; $i++)
		{	
			$row = mysql_fetch_row($rs);
			//acá hacemos la consulta de los lotes que corresponden al producto con el código correspondiente.
			$query ="SELECT Plocod, Plosal, Plofve, Plohve, Plopro, Fecha_data, Plocin "
					." FROM ".$wcenmez."_000004 "
					."WHERE Plopro = '".$row[0]."'"
					//."  AND Plofve >= '".$fhoy."'"
					."  AND Ploest = 'on' "
					."  AND Plosal > 0 "
					."ORDER BY 4";
					
			$rs2 = mysql_query($query,$conex) or die (mysql_errno().": ".mysql_error());
			$num2 = mysql_num_rows($rs2);
					
			if (is_integer($acumulado/2))
				$wclass="fila2";
			else
				$wclass="fila1";
			
			if($num2>0)//Pregunto si hay lotes activos... ya que pueden haber existencias sin lotes... si la existencia no tiene lotes entonces no muestro el producto.
			{	
				$acumulado++;
				//$costo = number_format($row[3],2,",",".");
				echo"<tr>";
				echo"<td class='".$wclass."' rowspan=".$num2." align=center>".$row[0]."</td>";//código del producto
				echo"<td class='".$wclass."' rowspan=".$num2." align=left>".$row[1]."</td>";  //nombre del producto
				//echo"<td class='".$wclass."' rowspan=".$num2." align=center>".$costo."</td>";  // costo promedio del producto
				echo"<td class='".$wclass."' rowspan=".$num2." align=center>".$row[2]."</td>";//existencias del producto
				
				$tot=0;
				$prod2=0;
				//generar dinámicamente los <td> de los lotes.
				for($j = 0; $j < $num2; $j++)
				{	
					
					$row2 = mysql_fetch_row($rs2);			
					$fechadata=explode("-", $row2[5]);
					if($fechadata[1]=='01')
					{
						$ano = $fechadata[0]-1;
						$mes=12;
					}else
						{
							$ano = $fechadata[0];
							$mes=$fechadata[1]-1;
						}
					if(strlen($mes)==1)
					{
						$mes='0'.$mes;
					}
					//agregando
					//$tot=$tot+$row2[6];
					$query  = "SELECT Mdepre, Mdecan ";
					$query .=" from ".$wcenmez."_000007, ".$wcenmez."_000008 ";
					$query .= " where  congas = 'on' " ;
					$query .= "   and  conind = '-1' ";
					$query .= "   and  mdecon = concod ";
					$query .= "   and  mdeest = 'on' ";
					$query .= "   and  mdepre <> '' ";
					$query .= "   and  mdenlo='".$row2[0]."-".$row2[4]."' ";
					
					$err3 = mysql_query($query,$conex) or die (mysql_errno().": ".mysql_error());
					$num3 = mysql_num_rows($err3);
					
					for ($k=0;$k<$num3;$k++)
					{
						$row3 = mysql_fetch_array($err3);
						
						$query = "SELECT salvuc, salpro ";
						$query .=" from ".$wcenmez."_000014  ";
						$query .= " where  salano = ".$ano." " ;
						$query .= "   and  salmes = ".$mes." " ;
						$query .= "   and  salcod = '".$row3[0]."' " ;
						$errv = mysql_query($query,$conex) or die (mysql_errno().": ".mysql_error());
						$rowv = mysql_fetch_array($errv);
					
						$rowv[0] = (float)$rowv[0];
						$rowv[1] = (float)$rowv[1];
						$row3[1] = (float)$row3[1];
						
						if($rowv[1]>0)
						{
							
							$prod2=$prod2+@($row3[1]*$rowv[0]/$rowv[1]);
						}
					}
					//hasta acá agregando
					
					//verificamos la fecha de vencimiento del lote
					$aux = strtotime($row2[2]." ".$row2[3]);
					$aux = $aux - time();
					
					if($aux <= 0) //ponemos el texto de la fecha en rojo si el lote ya está vencido.
						$fecv = "fondorojo";
					else
						$fecv = $wclass;
					
					if($j == 0)
						{
							echo"<td class='".$fecv."' colspan=1 align=center>".$row2[0]."</td>";//código del lote
							echo"<td class='".$fecv."' colspan=1 align=center>".$row2[2]."</td>";//fecha vencimiento del lote
							echo"<td class='".$fecv."' colspan=1 align=center>".$row2[3]."</td>";//hora vencimiento del lote
							echo"<td class='".$fecv."' colspan=1 align=center>".number_format($prod2,2,",",".")."</td>";//aca va el costo promedio.
							echo"<td class='".$fecv."' colspan=1 align=center>".$row2[1]."</td>";//existencias del producto en lote
						}else
							{
								echo "<tr>";
								echo"<td class='".$fecv."' colspan=1 align=center>".$row2[0]."</td>";//código del lote
								echo"<td class='".$fecv."' colspan=1 align=center>".$row2[2]."</td>";//fecha vencimiento del lote
								echo"<td class='".$fecv."' colspan=1 align=center>".$row2[3]."</td>";//hora vencimiento del lote
								echo"<td class='".$fecv."' colspan=1 align=center>".number_format($prod2,2,",",".")."</td>";//aca va el costo promedio.
								echo"<td class='".$fecv."' colspan=1 align=center>".$row2[1]."</td>";//existencias del producto en lote
								echo "</tr>";
							}
				}
				echo"</tr>";				
			}			
		}
	}
 }

 function piePagina()
 {
	echo "</table>";
	echo "<br><table aling=center>";
	echo "<tr><td>";
	echo "<center><INPUT type='button' value='Retornar' onclick='document.forms[0].submit();'></center>";
	echo "</td></tr>";
	echo "</table>";
 }

 	$wactualiz = "2021-11-18"; /***********************
								** fecha actualizacion	
								***********************/

//Acá inicia el programa.


include_once("root/comun.php");
encabezado("LISTADO PRODUCTOS EXISTENTES",$wactualiz, "clinica");
	
 session_start();
 if(!isset($_SESSION['user']))
 echo "error";
 else
  {
	

	$wcenmez = consultarAliasPorAplicacion($conex, $wemp_pmla, "cenmez");
	$fhoy = date("Y-m-d");
	$acumulado = 0;
	
	echo "<form name='listarProdEx' action='rep_listarProductosExistentes.php?wemp_pmla=".$wemp_pmla."' method=post>";
	
		
	if(!isset($wtip_pr))
	{
		// se consulta los códigos de los tipos de productos que aplican.
		$query = "SELECT DISTINCT Tipdis, Tipdes,Tipcod 
					FROM `".$wcenmez."_000001`
				   WHERE Tippro = 'on'"
                   ."AND Tipest = 'on'";
		$rs = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		
		echo "<center><table border=0>";
		echo "<tr><td class='fila1' align=center colspan=4><b>PROMOTORA MEDICA LAS AMERICAS S.A.<b></td></tr>";
		echo "<tr><td class='fila2' align=center colspan=4>LISTADO DE PRODUCTOS EXISTENTES</td></tr>";
		echo "<tr><td class='fila1'>TIPO DE PRODUCTO</td>";
		echo "<td class='fila2'>";
		echo "<select name='wtip_pr'>";
		 echo"<option selected>Todos</option>";
		 $num = mysql_num_rows($rs);
		 for($i = 0; $i <$num; $i++)
			{
			$row = mysql_fetch_array($rs);
			echo "<option>".$row[2]." - ".$row[1]."(".$row[0].")</option>";
			}
		echo "</select></td></tr>";
		echo "<tr><td class='fila1'  colspan=4 align=center><input type='submit' value='ENTER'></td></tr></table>";
		echo "<br><table align=center>";
		echo "<td>";
		echo "<center><INPUT type='button' value='Cerrar' onclick='cerrarVentana();'></center>";
		echo "</td></tr>";
	}else
	 {
			encabezadoResultados($wtip_pr);
			generarResultados($wtip_pr, $conex, $wcenmez, $fhoy);
			piePagina();
		
	 }
	echo "</form>";
	
  }
 ?>
 </BODY>
</HTML>