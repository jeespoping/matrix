<html>
<head>
<title>Reporte de tarjeta de puntos</title>
</head>
<font face='arial'>
<BODY TEXT="#000066">
<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>
<script type="text/javascript">
veces=0;
function enter()
{
    document.forms.Rep_enrade.submit();
}
function mostrarDetalleReenvios()
{
	$.blockUI({ message: $("#divDetalleRenv"), 
							css: { left: '15%', 
								    top: '15%',
								  width: '60%',
								  height: '65%'
								 }
							 } 
					 );
}

function cerrarDetalleReenv()
{
	$.unblockUI();
	//document.forms.Rep_enrade.submit();	
}
function agregarAdetalle(trFactura)
{
	alert(trFactura);
	var tabla = $("#tblDetalleRenv");
	veces++;	
	if(veces<2){
	alert("entró");
	}
	tabla.append(trFactura);
	
}
</script>
<?php
include_once("conex.php");

/********************************************************
*     REPORTE DE ENVIO, RADICACION Y DEVOLUCION			*
*														*
*********************************************************/

//==================================================================================================================================
//PROGRAMA						:Reporte envio, radicacion y devolucion	 
//AUTOR							:Juan David Londoño
//FECHA CREACION				:AGOSTO 2006
//FECHA ULTIMA ACTUALIZACION 	:2009-11-03
//DESCRIPCION					:
//	
//MODIFICACIONES:
//2012-10-03 Camilo Zapata: se crea la opcion de ver el detalle de las facturas reenviadas.
//2012-07-17 Camilo Zapata: se modificaron los querys con el objetivo de que busque los valores de las facturas que han sido enviadas previamente, es decir que se han enviado mas de una vez.
//			 				el programa tiene en cuenta que al traer las facturas, debe filtrar tambien por el responsable, para así evitar datos erróneos.
//			     			al final el programa muestra la cantidad de facturas que fueron reenviadas y el valor(suma de saldos) de estas.
//2012-07-16 Camilo Zapata: se cambio el aspecto del programa, dandole el estilo estandar de los demás programas de matrix.
//======================================================================================================================================
//2009-11-03 Se quita toda la relacion que tenga con la tabla 100 ya que el nombre del usuario se encuentra en la factura.
//2007-04-26 Se agrego en el query para seleccionar la fuente, la fuente de glosas.	
//2008-02-20 Se agrego en el reporte que mostrara el usuario que hace el movimiento.						 
//==================================================================================================================================

$wactualiz="Ver. 2012-10-03";
include_once("root/comun.php");

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	if(!isset($wemp_pmla)){
		terminarEjecucion($MSJ_ERROR_FALTA_PARAMETRO."wemp_pmla");
	}
	
	$conex = obtenerConexionBD("matrix");

	$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);

	$wbasedato = $institucion->baseDeDatos;
	$wentidad = $institucion->nombre;

	echo "<form name=Rep_enrade action='' method=post>";
	
	echo "<input type='HIDDEN' NAME= 'wemp_pmla' value='".$wemp_pmla."'>";
	
	encabezado("ENVIO, RADICACION, DEVOLUCION Y GLOSAS", $wactualiz, "logo_".$wbasedato );

if (!isset($opc))
{	
	echo "<center><table border=1>";
   /* echo "<tr><td align=center rowspan=2 colspan=2><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
    echo "<tr><td align=center colspan=1 bgcolor=#006699><font size=6 text color=#FFFFFF><b>ENVIO, RADICACION, DEVOLUCION Y GLOSAS</b></font><br><font size=3 text color=#FFFFFF><b>".$wactualiz."</b></font></td></tr>";*/

	
	 if (!isset($fuente))//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////FUENTE
    {
        echo "<tr class='fila2'><td align=left ><b><font size=3> Fuente: <br></font></b><select name='fuente' onchange='enter()'>";
		 // 2007-04-26
        $query =  " SELECT carfue, cardes
                FROM ".$wbasedato."_000040 
              	WHERE Carenv='on' 
              	OR Carrad='on'
              	OR Cardev='on'
                OR Carglo='on'
             	ORDER BY carfue ";
        $err = mysql_query($query,$conex);
        $num = mysql_num_rows($err);
        echo "<option></option>";
        for ($i=1;$i<=$num;$i++)
        {
            $row = mysql_fetch_array($err);
            echo "<option>".$row[0]."-".$row[1]."</option>";
        }
        echo "</select></td>";
    }else{

        echo "<tr class='fila2'><td align=left><b><font size=3> Fuente: <br></font></b><select name='fuente' onchange='enter()'>";
        echo "<option>".$fuente."</option>";

        $query =  " SELECT carfue, cardes
                FROM ".$wbasedato."_000040 
              	WHERE (Carenv='on' 
              	OR Carrad='on'
              	OR Cardev='on')
              	AND Carfue !=  (mid('".$fuente."',1,instr('".$fuente."','-')-1))
             	ORDER BY carfue ";

        $err = mysql_query($query,$conex);
        $num = mysql_num_rows($err);
        for ($i=1;$i<=$num;$i++)
        {
            $row = mysql_fetch_array($err);
            echo "<option>".$row[0]."-".$row[1]."</option>";
        }
        echo "</select></td>";

    }
    
    if (!isset($responsable))//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////RESPONSABLE
    {

        echo "<td align=left colspan=2><b><font size=3> Responsable: <br></font></b><select name='responsable' onchange='enter()'>";
		
        $query =  " SELECT empcod, empnit, empnom
                FROM ".$wbasedato."_000024 
              	WHERE Empcod=Empres
             	ORDER BY empcod ";
        $err = mysql_query($query,$conex);
        $num = mysql_num_rows($err);
        echo "<option></option>";
        for ($i=1;$i<=$num;$i++)
        {
            $row = mysql_fetch_array($err);
            echo "<option>".$row[0]."-".$row[1]."-".$row[2]."</option>";
        }
        echo "</select><br><input type='radio' name='todas' value='empresas'><b>Todas las empresas</td>";
    }else{
        echo "<td align=left colspan=2><b><font text size=3> Responsable: <br></font></b><select name='responsable' onchange='enter()'>";
        echo "<option>".$responsable."</option>";

        $query =  " SELECT empcod, empnit, empnom
                FROM ".$wbasedato."_000024 
              	WHERE Empcod=Empres
              	AND empcod != (mid('".$responsable."',1,instr('".$responsable."','-')-1))
             	ORDER BY empcod ";
        $err = mysql_query($query,$conex);
        $num = mysql_num_rows($err);
        for ($i=1;$i<=$num;$i++)

        {
            $row = mysql_fetch_array($err);
            echo "<option>".$row[0]."-".$row[1]."-".$row[2]."</option>";
        }
        echo "</select><br><input type='radio' name='todas' value='empresas'><b>Todas las empresas</td></tr>";
    }
    
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////RANGO DE FECHAS
    
    if (isset($fec1))
    $fec1=$fec1;
    else
    $fec1='';

    if (isset($fec2))
    $fec2=$fec2;
    else
    $fec2='';
    
		//para los centros de costos
		$q =  " SELECT ccocod, ccodes "
			."   FROM ".$wbasedato."_000003 "
			."	 WHERE ccoest='on'"
			."  ORDER BY 1 ";
		$res = mysql_query($q,$conex);
		$num = mysql_num_rows($res);
        echo "<tr class='fila1'>";
		echo "<td>";
		echo "<select name='wcco'>";
		echo "<option value='Todos'>Todos</option>";
		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($res);
			echo "<option value=".$row[0]."-".$row[1].">".$row[0]."-".$row[1]."</option>";
		}
		echo "</select></td>";
		echo "</td>";
		
		echo"<td align=center colspan=3><b>Fecha Inicial&nbsp:</b>";campoFechaDefecto("fec1", $fec1);
		echo "<b> Fecha Final&nbsp:"; campoFechaDefecto("fec2", $fec2);
		echo "</tr>";
        
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////OPCIONES    

     if (isset($opc) and ($opc=='ac')) {
                $chk1="checked";
            }else
            {
                $chk1="";
            }
            if (isset($opc) and ($opc=='an')) {
                $chk2="checked";
            }else
            {
                $chk2="";
            }
            if (isset($opc) and ($opc=='ab')) {
                $chk3="checked";
            }else
            {
                $chk3="";
            }
    
    	echo "<tr class='fila2'><td align=center colspan=3><input type='radio' name='opc' value='ac' $chk1><b>Activos&nbsp&nbsp&nbsp
			<input type='radio' name='opc' value='an' $chk2>Anulados&nbsp&nbsp&nbsp
			<input type='radio' name='opc' value='ab' $chk3 >Ambos&nbsp&nbsp&nbsp</tr>";
            echo "<tr class='fila1'><td align=center colspan=3><input type='submit' value='OK'></td>";                            //submit
            echo "</tr>";
            echo "</table>";
            echo "<br>";
            
}else{           
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA IMPRESION
   $facturasReenviadas=array();
   $facturasXempresa=array();
   //fuente para envios
    $qenv =  " SELECT carfue 
                FROM ".$wbasedato."_000040 
              	WHERE Carenv='on' 
             	ORDER BY carfue ";
    $rsenv = mysql_query($qenv,$conex);
	$renv=mysql_fetch_array($rsenv);
	$env=$renv[0];
	
	//fuente para radicacion
    $qrad =  " SELECT carfue 
                FROM ".$wbasedato."_000040 
              	WHERE Carrad='on'
             	ORDER BY carfue ";
    $rsrad = mysql_query($qrad,$conex);
	$rrad=mysql_fetch_array($rsrad);
	$rad=$rrad[0];
	
	//fuente para dev
    $qdev =  " SELECT carfue 
                FROM ".$wbasedato."_000040 
              	WHERE Cardev='on'
             	ORDER BY carfue ";
    $rsdev = mysql_query($qdev,$conex);
	$rdev=mysql_fetch_array($rsdev);
	$dev=$rdev[0];
	
	$rela=explode('-',$fuente);
	echo "<center><table border=1>";
    echo "<tr><td align=center ><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=400 HEIGHT=100></td></tr>";
    echo "<tr class='encabezadotabla'><td align=center colspan=1><b>REPORTE RELACION DE: ".$rela[1]."</b><br><b>".$wactualiz."</b></td></tr>";
	echo "<tr class='fila1'><td align=center colspan=1><b>FECHA INICIAL: <i>".$fec1."</i>&nbsp&nbsp&nbspFECHA FINAL: <i>".$fec2."</i></b></b></font></td></tr>";

	if (isset($fuente) and isset($responsable) and $responsable!=''  and isset($fec1) and isset($fec2) and isset($opc) and !isset($todas))
		{
		
		 if ($opc=='ac')
		 $est="AND Renest='on'";
		
		 if ($opc=='an')
		 $est="AND Renest='off'";
		
		 if ($opc=='ab')
		 $est="";
		
		 $condicco='';
		
		 $cco=explode("-", $wcco);
		 if($wcco!='Todos')
			$condicco=" AND rencco='".$cco[0]."'";
			
		 //2008-02-20
			$query =  " SELECT Renfec, Renfue, Rennum, Renvca, Renest, Rennom, Renusu, Rencod
	                FROM ".$wbasedato."_000020 
	              	WHERE Renfue=(mid('".$fuente."',1,instr('".$fuente."','-')-1))
	              	AND Rencod = (mid('".$responsable."',1,instr('".$responsable."','-')-1)) 
	              	AND Renfec between '".$fec1."' and '".$fec2."'
	              	".$condicco."
					".$est."";
					

	        $err = mysql_query($query,$conex);
	        //echo mysql_errno() ."=". mysql_error();
	        $num = mysql_num_rows($err);	
	        
			$nit1=explode('-',$responsable);
		    $nit=$nit1[1]."-".$nit1[2];
		
			if (isset($nit))
			{
				 echo "<center><table border>";
			                echo "<tr class='encabezadotabla'><td colspan=5 align=center><b>DETALLE DE LOS DOCUMENTOS CONSULTADOS</b></td></tr>";
			                echo "<tr class='fila1'><td colspan=5 align=left><b>EMPRESA: ".$nit."</b></td></tr>";
			                echo "<tr class='encabezadotabla'><td><b>Fecha</td><td><b>Fuente</td><td><b>No del Documento</td><td><b>Valor del Documento</td><td><b>Usuario</td></tr>";
			}

		   $total=0;      
			$facsRevisadas=array(); //este array tiene como objetivo guardar las facturas a medida que se van revisando, con el fin de que si la misma factura está en distintos envios 
									//solo se sumen una vez.
			$totReenv=0;
			$docReenv=0;
			$totfacs=0;
		     for ($i=1;$i<=$num;$i++)
		     {
			 
		     	if (is_int ($i/2))
                    $wcf="fila2";
                    else
                    $wcf="fila1";
                    
		     	$row = mysql_fetch_array($err);
				
				//sección que busca el valor reenviado.
				if($row[1]==$env)//estos cálculos se hacen solo si se está reportando envios.
				{
					$q = "SELECT rdeffa, rdefac, fencod, empnom"//traemos las facturas que están contenidas en cada envio.
						."  FROM ".$wbasedato."_000021, ".$wbasedato."_000018, ".$wbasedato."_000024, ".$wbasedato."_000020"
						." WHERE rdefue= '".$row[1]."'"
						."   AND rdenum= '".$row[2]."'"
						."   AND renfue= rdefue"
						."   AND rennum= rdenum"
						."	 AND rdeffa= fenffa"
						."	 AND rdefac= fenfac"
						."	 AND rencod=fencod"
						."	 AND fennit=empnit";
						//."   AND rdecco= '".$row[10]."'";
					$rsf = mysql_query($q,$conex) or die(mysql_error());
					$nfacs=mysql_num_rows($rsf);
					$totfacs+=$nfacs;
					$envio=false;
					for($k=0; $k<$nfacs; $k++)
					{
						$row2=mysql_fetch_array($rsf);					
						$tr='';
						if(!array_key_exists($row2[0]."-".$row2[1], $facsRevisadas))//la key del array se compone de: fuente_de_la_factura-factura-centro_de_costos
						{
							//acá se consulta la cantidad de envios asociados.  SOLO SE DEBEN TRAER LAS FACTURAS QUE TIENEN EL MISMO RESPONSABLE DEL NÚMERO DE ENVIO
							$qenvs="SELECT COUNT(*)"
								  ."  FROM ".$wbasedato."_000021 , ".$wbasedato."_000020"
								  ." WHERE rdefue='".$row[1]."'"
								  ."   AND rdeffa='".$row2[0]."'"
								  ."   AND rdefac='".$row2[1]."'"
								  ."   AND rennum=rdenum"
								  ."   AND renfue=rdefue"
								  ."   AND rencod='".$row2[2]."'"
								  ."   AND rdeest='on'";
							$rsenvs = mysql_query($qenvs, $conex);
							$rowenvs= mysql_fetch_array($rsenvs);
								  
							if($rowenvs[0]>1)// si esa factura está asociada a mas de un documento de envios se busca el valor reenviado
							{
								$qvalf="SELECT fensal"
								  ."  FROM ".$wbasedato."_000018"
								  ." WHERE fenffa='".$row2[0]."'"
								  ."   AND fenfac='".$row2[1]."'"
								  ."   AND fencod='".$row2[2]."'";
								$rsvalf = mysql_query($qvalf,$conex);
								$rowvalf= mysql_fetch_array($rsvalf);
								$totReenv+=$rowvalf[0];   //valor reenviado.
								$docReenv++;	//cantidad de facturas reenviadas.
								if(is_int($docReenv/2))
									$wclass='fila1';
									else
										$wclass='fila2';
								$facsRevisadas[$row2[0]."-".$row2[1]]='s';
								$facturasXempresa[trim($row2['empnom'])][$row2[0]."-".$row2[1]]=$rowvalf[0];
							}
							$facsRevisadas[$row2[0]."-".$row2[1]]='n';					
						}
					}
				}
				$fuente=$row[1];
				//fin sección de valor reenviado.
		     	
		     	if ($row[4]=='off')
		     	{
		     		$es='(*)';
			     	}else
			     	{
			     		$es='';
			     	}
		     	
		     	echo "<tr class=".$wcf."><td align=center>".$es."".$row[0]."</td><td align=center>".$row[1]."</td><td align=center>".$row[2]."</td><td align=right>".number_format($row[3],0,'.',',')."</td><td align=center>".$row[6]."</td></tr>";
		     
		     	if ($row[4]=='on' or $opc=='an')
		     	 $total=$total+$row[3];
		     }
			
		     echo "<tr class='fila2'><td colspan=2><b>TOTAL</td><td align=right><b>No Dctos: ".$num."</td><td align=right><b>Val: ".number_format($total,0,'.',',')."</td><td>&nbsp</td></tr>";
			 echo "<br>";
			 if ($opc=='ab')
			 {
			  	 echo "<tr class='fila1' ><td colspan=5><b><font color=##FF0000> EL VALOR TOTAL NO INCLUYE EL VALOR DE LOS DOCUMENTOS ANULADOS </font></td></tr>";
			  	 echo "<tr class='fila2' ><td colspan=5><b><font color=##FF0000> (*) DOCUMENTOS ANULADOS </font></td></tr>";
				 if($fuente==$env)
				 echo "<tr class='fila2'><td colspan=2><b>TOTAL REENVIOS</td><td align=right><b>No FACTURAS: ".$docReenv."</td><td align=right><b>VALOR: ".number_format($totReenv,0,'.',',')."</td><td style='cursor:pointer;' onclick='mostrarDetalleReenvios()'><font color='blue'>Ver detalle</font></td></tr>";
			 }
			 echo "</table>";
			 echo "<br>";
			 
		}if (isset($fuente) and isset($todas) and $todas=='empresas' and isset($fec1) and isset($fec2) and isset($opc))////////para todas la empresas
				{
					if ($opc=='ac')
					$est="AND Renest='on' ORDER by Rencod";
					
					if ($opc=='an')
					$est="AND Renest='off' ORDER by Rencod";
					
					if ($opc=='ab')
					$est=" ORDER by Rencod";
					
					$condicco='';
					$cco=explode("-", $wcco);
						if($wcco!='Todos')
					$condicco=" AND rencco='".$cco[0]."'";
					
					//2008-02-20
						$query =  " SELECT Renfec, Renfue, Rennum, Renvca, Renest, Rennom, Empnit, Empnom, Empcod, Renusu, Rencco
				                FROM ".$wbasedato."_000020, ".$wbasedato."_000024 
				              	WHERE Renfue=(mid('".$fuente."',1,instr('".$fuente."','-')-1))
				              	AND Empcod = Rencod
				              	AND Renfec between '".$fec1."' and '".$fec2."'
								".$condicco."
				              	".$est."";
				        $err = mysql_query($query,$conex);
				        //echo mysql_errno() ."=". mysql_error();
				        $num = mysql_num_rows($err);
				      
				        
				     echo "<center><table border>";
			                echo "<tr class='encabezadotabla'><td colspan=5 align=center><b>DETALLE DE LOS DOCUMENTOS CONSULTADOS</b></td></tr>";    
			          
					  $facsRevisadas=array(); //este array tiene como objetivo guardar las facturas a medida que se van revisando, con el fin de que si la misma factura está en distintos envios 
											 //solo se sumen una vez.
					  $totReenv=0;
					  $docReenv=0;
					  $totfacs=0;
			          $a='';
				      $total=0; 
				      $tot1=0;
				      $j=1;
			          for ($i=1;$i<=$num;$i++)
					     {
					     		
					     	if (is_int ($i/2))
			                    $wcf="fila2";
			                    else
			                    $wcf="fila1";
			                    
					     	$row = mysql_fetch_array($err);
							
							//sección que busca el valor reenviado.
								if($row[1]==$env)//estos cálculos se hacen solo si se está reportando envios.
								{	
									$q = "SELECT rdeffa, rdefac, fencod, empnom"//traemos las facturas que están contenidas en cada envio.
										."  FROM ".$wbasedato."_000021, ".$wbasedato."_000018, ".$wbasedato."_000024"
										." WHERE rdefue= '".$row[1]."'"
										."   AND rdenum= '".$row[2]."'"
										."	 AND rdeffa= fenffa"
										."	 AND rdefac= fenfac"
										."	 AND fennit= empnit"
										."	 AND empcod= '".$row[8]."'"
										."   AND rdecco= '".$row[10]."'";
									$rsf = mysql_query($q,$conex) or die(mysql_error());
									$nfacs=mysql_num_rows($rsf);
									
									$totfacs+=$nfacs;
									for($k=0; $k<$nfacs; $k++)
									{
										$row2=mysql_fetch_array($rsf);
										if(!array_key_exists($row2[0]."-".$row2[1], $facsRevisadas))//la key del array se compone de: fuente_de_la_factura-factura-centro_de_costos
											{
												//acá se consulta la cantidad de envios asociados.
												$qenvs="SELECT COUNT(*)"
													  ."  FROM ".$wbasedato."_000021 , ".$wbasedato."_000020"
													  ." WHERE rdefue='".$row[1]."'"
													  ."   AND rdeffa='".$row2[0]."'"
													  ."   AND rdefac='".$row2[1]."'"
													  ."   AND rennum=rdenum"
													  ."   AND renfue=rdefue"
													  ."   AND rencod='".$row2[2]."'"
													  ."   AND rdeest='on'";
												$rsenvs = mysql_query($qenvs, $conex);
												$rowenvs= mysql_fetch_array($rsenvs);
												$tr='';
												if($rowenvs[0]>1)// si esa factura está asociada a mas de un documento de envios se busca el valor reenviado
												{
													$qvalf="SELECT fensal"
														  ."  FROM ".$wbasedato."_000018"
														  ." WHERE fenffa='".$row2[0]."'"
														  ."   AND fenfac='".$row2[1]."'"
														  ."   AND fencod='".$row2[2]."'";
													$rsvalf = mysql_query($qvalf,$conex);
													$rowvalf= mysql_fetch_row($rsvalf);
													$totReenv+=$rowvalf[0];   //valor reenviado.
													$docReenv++;	//cantidad de facturas reenviadas.
													if(is_int($docReenv/2))
														$wclass='fila1';
														else
															$wclass='fila2';
													$facsRevisadas[$row2[0]."-".$row2[1]]='s';
													$facturasXempresa[trim($row2['empnom'])][$row2[0]."-".$row2[1]]=$rowvalf[0];
												}
												$facsRevisadas[$row2[0]."-".$row2[1]]='n';
											}
									}
					     	 	}
								//fin sección de valor reenviado.
								$fuente=$row[1];					     	 
					     	if ($a != $row[6])
						     	{
						     			
						     		if ($i!=1)
						     		{
						     			
						     			echo "<tr class='encabezadotabla'><td colspan=2><b>TOTAL</td><td align=right><b>No Dctos: ".$j."</td><td align=right><b>Val: ".number_format($tot1,0,'.',',')."</td><td>&nbsp</td></tr>";
						     			$j=1;
						     			
						     		}
						     		if ($row[4]=='on' or $opc=='an'){
											     		$tot1=$row[3];
						     		}
						     		echo "<tr class='fila1'><td colspan=5 align=left><b>EMPRESA: ".$row[6]."-".$row[7]."</b></td></tr>";
						     		echo "<tr class='encabezadotabla'><td><b>Fecha</td><td><b>Fuente</td><td><b>No del Documento</td><td><b>Valor del Documento</td><td><b>Usuario</td></tr>";
							     	$a=$row[6];
							     	
						     	}else if ($row[4]=='on' or $opc=='an') {
						     			$tot1=$tot1+$row[3];
						     			$j++;
						     		}
						     		
						     		if ($row[4]=='off')
							     	{
							     		$es='(*)';
								     	}else
								     	{
								     		$es='';
								     	}
					     	echo "<tr  class=".$wcf."><td align=center>".$es."".$row[0]."</td><td align=center>".$row[1]."</td><td align=center>".$row[2]."</td><td align=right>".number_format($row[3],0,'.',',')."</td><td align=center>".$row[9]."</td></tr>";
	
					     	
					     	
					     	 	if ($i==$num)
						     		{
						     			echo "<tr class='encabezadotabla'><td colspan=2><b>TOTAL</td><td align=right><b>No Dctos: ".$j."</td><td align=right><b>Val: ".number_format($tot1,0,'.',',')."</td><td>&nbsp</td></tr>";
										
						     		}
					     	
					     	
						   
					     	
						    if ($row[4]=='on' or $opc=='an')//////gran total
					     	 $total=$total+$row[3];
	
					     }
					     
						 switch($fuente)
						 {
							case $env:
								$docs='Envios';
								break;
							case $rad:
								$docs='Radicaciones';
								break;
							case $dev:
								$docs='Devoluciones';
								break;
						 }
						 
					     	echo "<tr class='fila2'><td colspan=2><b>TOTAL DE TODAS LAS EMPRESAS</td><td align=right><b>No ".$docs.": ".$num.""; 
							if($fuente==$env)
							echo "<br>Total Facturas: ".$totfacs;
							echo"</td><td align=right><b>Val: ".number_format($total,0,'.',',')."</td><td>&nbsp</td></tr>";
							if($fuente==$env)
							echo "<tr class='fila2'><td colspan=2><b>TOTAL REENVIOS</td><td align=right><b>No FACTURAS: ".$docReenv."</td><td align=right><b>VALOR: ".number_format($totReenv,0,'.',',')."</td><td style='cursor:pointer;' onclick='mostrarDetalleReenvios()'><font color='blue'>Ver detalle</font></td></tr>";
					     
					     echo "<br>";
						 if ($opc=='ab')
						 {
						  	 echo "<tr class='fila1' ><td colspan=5><b><font color=##FF0000> EL VALOR TOTAL NO INCLUYE EL VALOR DE LOS DOCUMENTOS ANULADOS </font></td></tr>";
						     echo "<tr class='fila2' ><td colspan=5><b><font color=##FF0000> (*) DOCUMENTOS ANULADOS </font></td></tr>"; 
						 }    
					     echo "</table>";
					     echo "<br>";  	
				}
				$hyper="<A HREF='/matrix/ips/reportes/Rep_enrade.php?wemp_pmla=".$wemp_pmla."&amp;fec1=".$fec1."&amp;fec2=".$fec2."&amp;fuente=".$fuente."'>VOLVER</a>";
            	echo "<tr><td  colspan= '4' align=center><font size=3 face='arial' >$hyper</td></tr>";
				///////div para el detalle de los reenvios
				echo "<div id='divDetalleRenv' align='center' style='display:none; cursor:default; background:none repeat scroll 0 0; position:relative; width:98%; height:98%; overflow:auto;'>";
					echo "<center><table id='tblDetalleRenv' name='tablaReenvios'>";
					echo "<tr class='encabezadotabla'><td colspan=3 align='center'>DETALLE DE REENVIOS</td></tr>";
						if(count($facturasXempresa)>0)//se llena la tabla
						{
							$totalFacturas=0;
							$total=0;
							foreach($facturasXempresa as $keyEmpresa=>$facturas)
							{
								echo "<tr class='encabezadotabla'><td colspan='3'>EMPRESA: {$keyEmpresa}</td></tr>";
								echo "<tr class='encabezadotabla'>";
									echo "<td>FUENTE</td>";
									echo "<td>FACTURA</td>";
									echo "<td>VALOR</td>";
								echo "</tr>";
								$i=0;
								foreach($facturas as $keyFactura=>$saldo)
								{
									$facAux=explode("-",$keyFactura);
									if(is_int($i/2))
										$wclass='fila1';
										else
											$wclass='fila2';
										
									echo "<tr class={$wclass}>";
										echo "<td>{$facAux[0]}</td>";
										echo "<td>".$facAux[1]."-".$facAux[2]."</td>";
										echo "<td>{$saldo}</td>";
									echo "</tr>";
									$totalFacturas++;
									$total+=($saldo*1);
									$i++;
								}
							}
							echo "<tr><td colspan=3>&nbsp</td></tr>";
							echo "<tr class='encabezadotabla'>";
								echo "<td>TOTAL DE REENVIOS:</td>";
								echo "<td>{$totalFacturas}</td>";
								echo "<td>".number_format($total,0,'.',',')."</td>";
							echo "</tr>";
						}
					echo "</table></center>";
					echo "<center><table>";
						echo "<tr>";
							echo "<td>&nbsp</td>";
							echo "<td>&nbsp</td>";
							echo "<td><input type=button value='Cerrar' onclick='cerrarDetalleReenv();'/></td>";
						echo"</tr>";
					echo "</table></center>";
				echo "</div>";
	}
}
liberarConexionBD($conex);
?>	
