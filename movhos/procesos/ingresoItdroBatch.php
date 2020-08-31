<html>
<head>
<title>Ingreso a Itdro</title>
<style type="text/css">
    	//body{background:white url(portal.gif) transparent center no-repeat scroll;}
    	<!--Fondo Azul no muy oscuro y letra blanca -->
    	.tituloSup{color:#006699;background:#FFFFF;font-family:Arial;font-weight:bold;text-align:center;}
    	.tituloSup1{color:#57C8D5;background:#FFFFF;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo1{color:#FFFFFF;background:#006699;font-size:14pt;font-family:Arial;font-weight:bold;text-align:center;}
    	<!-- -->
    	.titulo2{color:#003366;background:#57C8D5;font-size:14pt;font-family:Arial;font-weight:bold;text-align:center;}
    	.titulo3{color:#003366;background:#A4E1E8;font-size:12pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.texto{color:#006699;background:#CCFFFF;font-size:9pt;font-family:Tahoma;text-align:center;}
    	<!-- .acumulado1{color:#003366;background:#FFCC66;font-size:11pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.acumulado2{color:#003366;background:#FFDBA8;font-size:11pt;font-family:Tahoma;font-weight:bold;text-align:center;}
    	.errorTitulo{color:#FF0000;font-size:11pt;font-family:Arial;font-weight:bold;text-align:center;}
    	-->
    	.alert{background:#FFFFAA;color:#FF9900;font-size:11pt;font-family:Arial;text-align:center;}
    	.warning{background:#FFCC99;color:#FF6600;font-size:11pt;font-family:Arial;text-align:center;}
    	.error{background:#FFAAAA;color:#FF0000;font-size:11pt;font-family:Arial;text-align:center;}
    	    	
    </style>
</head>
<body>
<?php
include_once("conex.php");
/**
 * Enter description here...
 * 
 * @table 000002 SELECT, UPDATE
 * @table 000003 SELECT, UPDATE
 * @table itdro SELECT
 * 
 * @modified 2007-12-10 Cuando se cambia de fecha y unos registros no pasan se anulan y se graban en un nuevo dronum
 * @modified 2007-09-27 En vista de que ya no exixten 000004.Spamen y 000004.Spamsa debe desaparecer la lógica, el query, en donde se sumaban los saldos por fuera de unix(itdro) alos que ya estan en unix(itdro)
 * @modified 2007-09-27 Desaparece el llamado a la funcion pacienteDeAlta(), pues esta no existe, y se empieza a usar al variable $pac['ald'] que indica si el paciente ya tiene el alta definitiva de la institución.
 * @modified 2007-09-27 Ya no hay necesidad de cabiar los registros con ingreso=0, pues el programa de cargos graba con el el ingreso que haya en matrix. Así que desaparecela lógica que hacia este cambio.
 * 
 * @return unknown
 */
function ingresoItdroBatch()
{

	global $conex;
	global $conex_o;
	global $conex_f;
	global $bd;
	global $emp;


	/**
	 * Buscar todos los registros activos que no esten 'P' (procesados), es decir que su estado sea  
	 * 'S': sin procesar, nunca se han revisado en UNIX
	 * 'I': Inconsistentes, alguna de sus líneas tiene una incosnsistencia
	 * 'T': en transición, algunos de sus líneas hanm pasado pero no se ha verificado que todas.
	 */
	$q = " SELECT * "
	."   FROM ".$bd."_000002 "
	."  WHERE Fenues IN ('M', 'I', 'T')"
	."    AND Fenest =  'on' "
	." ORDER BY Fencco, Fenhis, Fecha_data ";
	$err=mysql_query($q,$conex);
	$Connum=mysql_num_rows($err);

	if($Connum > 0)
	{
		$s = "";//string en donde se van a almacenar los id de de los encabezados que deben pasar a estado (fenues) 'S'
		$t = "";//string en donde se van a almacenar los id de de los encabezados que deben pasar a estado (fenues) 'T'
		for($k=0;$k<$Connum;$k++)
		{
			$conex_f = odbc_connect('facturacion','','');

			$row=mysql_fetch_array($err);
			$array[$k]['id']=$row['id'];
			$array[$k]['num']=$row['Fennum'];
			$array[$k]['fue']=$row['Fenfue'];
			$array[$k]['fec']=$row['Fenfec'];
			$array[$k]['hor']=$row['Hora_data'];
			$array[$k]['ues']=$row['Fenues'];
			$array[$k]['usu']=$row['Seguridad'];
			$droest='US';
			$array[$k]['mod']=false;//Es necesario modificar el estado en uix del registro
			$array[$k]['set']="";
			$fecha=$array[$k]['fec'];

			$array[$k]['pac']['his']=$row['Fenhis'];
			$array[$k]['pac']['ing']=$row['Fening'];
			$ingreso=$row['Fening']; //se guarda el ingreso que habia, por que al hacer la validación de historia va a asignar uno y hay que compararlos.

			$tipTrans=substr($row['Fentip'],0,1);
			if(substr($row['Fentip'],1,1) == "A")
			{
				$aprov=true;
			}
			else
			{
				$aprov=false;
			}

			$array[$k]['cco']['cod']=$row['Fencco'];
			getCco(&$array[$k]['cco'],$tipTrans, $emp);

			$array[$k]['pac']['act']=HistoriaMatrix($array[$k]['cco'], &$array[$k]['pac'], &$warning, &$array[$k]['pac']['err']);

			if($array[$k]['pac']['act'])
			{
				$array[$k]['pac']['act']=ValidacionHistoriaUnix(&$array[$k]['pac'], &$warning,&$array[$k]['pac']['err']);
				if($array[$k]['pac']['act'])
				{
					/**
					 * Si el paciente esta de alta $pac['ald'] == true, lo que significa que hay no le pueden cargar artículos.
					 */			
					if($array[$k]['pac']['ald'])
					{
						$warning = "EL PACIENTE CON HISTORIA:".$array[$k]['pac']['his']." FUE DADO DE ALTA DE LA INSTIRUCIÓN";
						$error['codInt']='0009';
						$error['codSis']=".";
						$error['descSis']=".";
						$error['clas']="#ff0000";
						$error['ok']="NO PASO, PACIENTE DE ALTA";
					}
				}
			}

			if(!$array[$k]['pac']['act'])
			{
				$array[$k]['cla']='error';
				$array[$k]['com']=$array[$k]['pac']['err']['ok'];

			}
			else
			{
				/**
				 * $fn: fecha del encabezado
				 * $fi: fecha del ingreso activo de esa historia
				 * 
				 * Si $fn es menor que $fi quiere decir que es una fecha u hora anterior que el momento en que se hizo el ingreso
				 * osea que el documento pertenece a un ingreso anterior. Osea que es un ingreso que ya no esta activo y no se 
				 * puede ingresar a itdro.
				 */
				$fn=mktime(substr($array[$k]['hor'],0,2),substr($array[$k]['hor'],3,2), substr($array[$k]['hor'],6,2),substr($array[$k]['fec'],5,2),substr($array[$k]['fec'],8,2),substr($array[$k]['fec'],0,4));
				$fi=mktime(substr($array[$k]['pac']['hor'],0,2),substr($array[$k]['pac']['hor'],3,2), substr($array[$k]['pac']['hor'],6,2),substr($array[$k]['pac']['fec'],5,2),substr($array[$k]['pac']['fec'],8,2),substr($array[$k]['pac']['fec'],0,4));
				if($fn < $fi or $ingreso != $array[$k]['pac']['ing'])
				{
					/**
					 * Lo que hay grabado no corresponde al ingreso activo
					 * No se pueden pasar los articulos.
					 */
					$array[$k]['com']="EL INGRESO AL QUE SE CARGARON ESTOS ARTICULOS NO ESTA ACTIVO";
					$array[$k]['cla']="error";
					$array[$k]['pac']['ing']=$ingreso;
					$array[$k]['pac']['act']=false;
				}
				else
				{
					/**
					 * La histori esta bien del todo
					 */
					$array[$k]['cla']='titulo3';
					$array[$k]['com']='';
					$array[$k]['pac']['act']=true;
				}
			}

			$q=" SELECT * "
			."   FROM ".$bd."_000003  "
			."  WHERE Fdenum = ".$row['Fennum']." "
			."    AND Fdeubi = 'M' "
			."    AND Fdeest = 'on' "
			." ORDER BY Fdelin ASC";
			$err1=mysql_query($q,$conex);
			$array[$k]['numLin']=mysql_num_rows($err1);
			//echo "<br><br><b>array[$k]['numLin']:".$array[$k]['numLin']."</b><br>";
			if($array[$k]['numLin'] > 0)
			{
				$cambio=0;
				for($j=0;$j<$array[$k]['numLin'];$j++)
				{
					$row1=mysql_fetch_array($err1);
					echo mysql_error();

					$array[$k][$j]['lin']=$row1['Fdelin'];
					$array[$k][$j]['ubi']=$row1['Fdeubi'];
					$array[$k][$j]['id']=$row1['id'];

					$array[$k][$j]['art']['cod']=$row1['Fdeart'];
					$array[$k][$j]['art']['nom']="";
					$array[$k][$j]['art']['can']=$row1['Fdecan'];
					$array[$k][$j]['art']['lot']=$row1['Fdelot'];
					$array[$k][$j]['art']['ser']=$row1['Fdeser'];
					$array[$k][$j]['art']['dis']=$row1['Fdedis'];
					$array[$k][$j]['art']['uni']="";
					/*		echo "array[$k][$j]['lin']=".$array[$k][$j]['lin']."<br>";
					echo "array[$k][$j]['ubi']=".$array[$k][$j]['ubi']."<br>";
					echo "array[$k][$j]['art']['cod']=".$array[$k][$j]['art']['cod']."<br>";
					echo "array[$k][$j]['art']['nom']=".$array[$k][$j]['art']['nom']."<br>";
					echo "array[$k][$j]['art']['can']=".$array[$k][$j]['art']['can']."<br>";

					/**
					* En Fdeari se guarda el código que ingresa el usuario originalmente,
					* ya que este no tiene que corresponder al código de facturación  si no un codigo de proveedor o un código especial,
					* adicionalmente para las transacciones por fuera de UNIX se almacena como primera letra si el
					* producto permite negativos'P' o no 'N' al momento del registro, y en la segunda se pone un '*'
					*/
					IF(substr($row1['Fdeari'],1,1) == "*")
					{
						$n=substr($row1['Fdeari'],0,1);
						$array[$k][$j]['art']['ini']=substr($row1['Fdeari'],2);
						if($n == 'P')
						{
							$array[$k][$j]['art']['neg']=true;
						}
						else//if  ($n == 'N')
						{
							$array[$k][$j]['art']['neg']=false;
						}
					}
					else
					{
						$array[$k][$j]['art']['ini']=$row1['Fdeari'];
						$array[$k][$j]['art']['neg']=false;
					}

					$array[$k][$j]['ini']=substr($array[$k][$j]['id'],2);
					$array[$k][$j]['artVal'] = ArticuloExiste(&$array[$k][$j]['art'], &$array[$k][$j]['err']);
					if($array[$k]['pac']['act'])
					{
						if($array[$k][$j]['artVal'])
						{
							$array[$k][$j]['artVal']=TarifaSaldo($array[$k][$j]['art'],$array[$k]['cco'], $tipTrans, $aprov, &$array[$k][$j]['err']);
							if($array[$k][$j]['artVal'])
							{
								/**
 								 * Buscar el registro en UNIX
 								 */
								$q = "SELECT drofue, drofec, droser, drohis, droart, drocan "
								."      FROM itdro "
								."     WHERE dronum = ".$array[$k]['num']." "
								."       AND drolin = ".$array[$k][$j]['lin']." ";
								$err_o= odbc_do($conex_o,$q);
								if(odbc_fetch_row($err_o))
								{
									if(!(
									$array[$k]['fue'] == odbc_result($err_o,1)
									and str_replace("-","/",$array[$k]['fec']) == odbc_result($err_o,2)
									and $array[$k]['cco']['cod'] == odbc_result($err_o,3)
									and $array[$k]['pac']['his'] == odbc_result($err_o,4)
									and $array[$k][$j]['art']['cod'] == odbc_result($err_o,5)
									and $array[$k][$j]['art']['can'] == odbc_result($err_o,6)
									))
									{
										/**
										 * Ya existen datos en itdro y son iguales a los de MATRIX.
										 * Se cambia el estado al registro y listo, todo esta bien
										 */
										$array[$k][$j]['artVal']=true;
										$array[$k][$j]['clas']='texto';
										$array[$k][$j]['err']['ok']="PASO A ITDRO, YA ESTABA EN ITDRO";
									}
									else
									{
										/**
										 * Ya hay datos en itdro y no corresponden a los que hay en MATRIX
										 */
										$array[$k][$j]['clas']='error';
										$array[$k][$j]['err']['ok']='NO CONCORDANCIA ENTRE MATRIX E ITDRO';
										$array[$k][$j]['err']['codInt']="1012";
										$array[$k][$j]['err']['codSis']="Inconcistencia entre Matrix e itdro";
										$array[$k][$j]['err']['descSis']="Inconcistencia entre Matrix e itdro";
									}
								}
								else
								{
									$exp=explode('-', $array[$k]['fec']);
									if ($exp[1]<date('m') or $exp[0]<date('Y') )
									{
										$array[$k]['fec']=date('Y-m-d');
									}

									if ($fecha==$array[$k]['fec'] or $array[$k]['ues']=='M')
									{
										$array[$k][$j]['artVal']=registrarItdro($array[$k]['num'],$array[$k][$j]['lin'], $array[$k]['fue'],$array[$k]['fec'], $array[$k]['cco'],$array[$k]['pac'],$array[$k][$j]['art'], &$error);
										if(!$array[$k][$j]['artVal'])
										{
											/**
										 * No se pudo ingresar el dato a itdro por un motivo diferente a error en indices.
										 * Se puede volver a intentar.
										 */
											$array[$k][$j]['clas']='error';
										}
									}
									else
									{
										//se debe inactivar el registro y grabar uno nuevo con nueva documentacion para el paciente
										$q="UPDATE ".$bd."_000003 "
										."     SET Fdeest ='off' "
										."   WHERE Fdenum =".$array[$k]['num']." "
										."   AND Fdelin =".$array[$k][$j]['lin']." ";

										$res=mysql_query($q,$conex);
										if(mysql_affected_rows()<0)
										{
											$array[$k][$j]['clas']='error';
											$array[$k][$j]['artVal']=false;
										}
										else
										{
											//creamos un nuevo registro
											$cns='';
											$dronum='';
											$drolin='';
											$resultado=Numeracion($array[$k]['pac'], $array[$k]['fue'],$tipTrans, $aprov, $array[$k]['cco'], &$array[$k]['fec'], &$cns, &$dronum, &$drolin, false, $array[$k]['usu'], &$error);
											if($resultado)
											{
												$array[$k][$j]['art']['ubi']='M';
												$resultado=registrarDetalleCargo ($array[$k]['fec'], $dronum, $drolin, $array[$k][$j]['art'], $array[$k]['usu'], &$error);
												if($resultado)
												{
													$array[$k][$j]['artVal']=registrarItdro($dronum,$drolin, $array[$k]['fue'],$array[$k]['fec'], $array[$k]['cco'],$array[$k]['pac'],$array[$k][$j]['art'], &$error);
													if($array[$k][$j]['artVal'])
													{
														$q="UPDATE ".$bd."_000003 "
														."     SET Fdeubi ='US' "
														."   WHERE Fdenum =".$dronum." "
														."   AND Fdelin =".$drolin." ";

														$res=mysql_query($q,$conex);
														if(mysql_affected_rows()<0)
														{
															$error=true;
														}

														$q="UPDATE ".$bd."_000002 "
														."SET Fenues='S' "
														."WHERE Fennum = ".$dronum;
														$res=mysql_query($q,$conex);
														if(mysql_affected_rows()< 1)
														{
															$desc="NO SE ACTUALIZO LA TABA DE ENCABEZADO EN MATRIX A S";
															$error=true;
														}

													}
													else
													{
														/**
					 									* No hay ninguno en UNIX
														 * Todos estan en Matrix
													 */
														$q="UPDATE ".$bd."_000002 "
														."SET Fenues='T' "
														."WHERE Fennum = ".$dronum;
														$res=mysql_query($q,$conex);

														$array[$k][$j]['clas']='error';
														$array[$k][$j]['artVal']=false;
													}
												}
												else
												{
													$array[$k][$j]['clas']='error';
													$array[$k][$j]['artVal']=false;
												}
											}
											else
											{
												$q="UPDATE ".$bd."_000003 "
												."     SET Fdeest ='on' "
												."   WHERE Fdenum =".$array[$k]['num']." "
												."   AND Fdelin =".$array[$k][$j]['lin']." ";

												$res=mysql_query($q,$conex);
												if(mysql_affected_rows()<0)
												{
													$array[$k][$j]['clas']='error';
													$array[$k][$j]['artVal']=false;
												}

												$array[$k][$j]['clas']='error';
												$array[$k][$j]['artVal']=false;
											}
										}
									}
								}

								if($array[$k][$j]['artVal'])
								{
									$array[$k][$j]['clas']='texto';
								}

								if($array[$k][$j]['artVal'])
								{
									/**
									 * Realiza el update para:
									 * Poner el detalle en su nueva ubicación US, unix sin procesar.
									 * Cambia el valor del Fdeari, en Fdeari se guarda el código que ingresa el usuario originalmente,
									 * ya que este no tiene que corresponder al código de facturación  si no un codigo de proveedor o un código especial,
									 * adicionalmente para las transacciones por duera de UNIX se almacena como primera letra si el 
									 * producto permite negativos o no al momento del registro,
									 * 
									 */
									$q = " UPDATE ".$bd."_000003 "
									."   SET Fdeubi = 'US', "
									."       Fdeari = '".$array[$k][$j]['art']['ini']."' "
									." WHERE id     = ".$array[$k][$j]['id']." ";

									$err2=mysql_query($q,$conex);
									if(mysql_affected_rows()<1)
									{
										$array[$k][$j]['clas']='error';
										$array[$k][$j]['err']['ok']='INTENTELO UNA VEZ MAS.';
										$array[$k][$j]['err']['codInt']="1006";
										$array[$k][$j]['err']['codSis']=mysql_errno();
										$array[$k][$j]['err']['descSis']=mysql_error();
									}
									else
									{
										$cambio++;
										$array[$k][$j]['clas']='texto';
										$array[$k][$j]['err']['ok']="PASO A ITDRO";
									}
								}
							}
							else
							{
								/**
								 * No hay tarifa o saldo
								 */
								$array[$k][$j]['clas']='warning';
							}
						}
						else
						{
							/**
							 * El código del artículo no existe
							 */
							$array[$k][$j]['clas']='error';
						}
					}
					else
					{
						$array[$k][$j]['clas']='alert';
						$array[$k][$j]['err']['ok']='NO PAC';
					}
				}//fin del for($numLin>0)
				/**
			 	 * Dependiendo de los ingresos exitosos a unix de los registros del detalle, 
			     * debe modificarse 
 			     */

				if($cambio == $array[$k]['numLin'])
				{
					/**
					 * Todos pasaron a estado sin procesar.
					 * Se debe
					 */
					$s = $s.", ".$array[$k]['id'];
					$array[$k]['ues']='S';
				}
				else if ($cambio >0)
				{
					/**
					 * Por lo menos un registro cambio
					 */
					$t = $t.", ".$array[$k]['id'];
					$array[$k]['ues']='T';
				}
			}
			else
			{
				/**
				 * No tiene registros en M.
				 * No se sabe si tiene o no registros, pero es otro el programa que debe comprobarlo.
				 * Si esta en M se debe cambiar
				 */
				if($array[$k]['ues'] == 'M')
				{
					$t = $t.", ".$array[$k]['id'];
					$array[$k]['ues']='T';
				}
			}
			odbc_close($conex_f);
		}//Fin del for($k=0;$k<$Connum;$k++)


		/**
		 * Modificar el estado de los encabezados( fenfac.fenues)
		 */
		if($s != "")
		{
			$q="UPDATE ".$bd."_000002 "
			."     SET Fenues='T' "
			."   WHERE id IN ( ".SUBSTR($s, 1)." )";
			$err1=mysql_query($q,$conex);
		}

		if($t != "")
		{
			$q="UPDATE ".$bd."_000002 "
			."     SET Fenues='T' "
			."   WHERE id IN ( ".SUBSTR($t, 1)." )";
			$err1=mysql_query($q,$conex);
		}

		return ($array);
	}//fin if($Connum>0)
	else
	{
		return (false);
	}

}


function imprimirRegistros($datos)
{
	$numNum=count($datos);
	if($numNum>0)
	{
		$cco="";
		$numCns="";
		for($k=0; $k<$numNum; $k++)
		{
			if($datos[$k]['numLin']>0)
			{
				if($cco != $datos[$k]['cco']['cod'])
				{
					if($k != 0)
					{
						echo "</table>";
					}
					echo "</br></br><table align='center'>";
					echo "<tr>";
					echo "<td class='titulo1' colspan='6'>".$datos[$k]['cco']['cod']."-".$datos[$k]['cco']['nom']."</td>";
					echo "</tr>";
					$cco = $datos[$k]['cco']['cod'];
				}

				if($numCns != $datos[$k]['num'])
				{
					/**
				 * Titulo de nuevo dronum
				 */

					echo "<tr>";
					echo "<td class='titulo2' colspan='1'>Historia</td>";
					echo "<td class='titulo2' colspan='1'>Ingreso</td>";
					echo "<td class='titulo2' >Fuente</td>";
					echo "<td class='titulo2' >Número</td>";
					echo "<td class='titulo2' colspan='2'>Comentario</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td class='".$datos[$k]['cla']."' colspan='1'>".$datos[$k]['pac']['his']."</td>";
					echo "<td class='".$datos[$k]['cla']."' colspan='1'>".$datos[$k]['pac']['ing']."</td>";
					echo "<td class='".$datos[$k]['cla']."' colspan='1'>".$datos[$k]['fue']."</td>";
					echo "<td class='".$datos[$k]['cla']."' colspan='1'>".$datos[$k]['num']."</td>";
					echo "<td class='".$datos[$k]['cla']."' colspan='2'>".$datos[$k]['com']."</td>";
					echo "</tr>";

					echo "<tr>";
					echo "<td class='titulo2' >Lin</td>";
					echo "<td class='titulo2' >Código</td>";
					echo "<td class='titulo2' colspan='2'>Artículo</td>";
					echo "<td class='titulo2' >Cant.</td>";
					echo "<td class='titulo2' >Estado</td>";
					echo "</tr>";
					$numCns=$datos[$k]['num'];
				}

				for($j=0;$j<$datos[$k]['numLin'];$j++)
				{
					echo "<tr>";
					echo "<td class='".$datos[$k][$j]['clas']."' >".$datos[$k][$j]['lin']."</td>";
					echo "<td class='".$datos[$k][$j]['clas']."' >".$datos[$k][$j]['art']['cod']."</td>";
					echo "<td class='".$datos[$k][$j]['clas']."' colspan='2'>".$datos[$k][$j]['art']['nom']."</font></td>";
					echo "<td class='".$datos[$k][$j]['clas']."' >".$datos[$k][$j]['art']['can']."</td>";
					echo "<td class='".$datos[$k][$j]['clas']."' >".$datos[$k][$j]['err']['ok']."</td>";
					echo "</tr>";
				}
			}
		}
	}
}

include_once("movhos/validacion_hist.php");
include_once("movhos/fxValidacionArticulo.php");
include_once("movhos/registro_tablas.php");
include_once("movhos/otros.php");




connectOdbc(&$conex_o, 'inventarios');
$conex_o = odbc_connect('inventarios','','');
if($conex_o != 0)
{
	$datos=ingresoItdroBatch();
	if($datos)
	{
		imprimirRegistros($datos);
	}
	else
	{
		echo "No hay registros para pasar a itdro";
	}
}
else
{
	echo "No hay conexión coon UNIX, no puede efectuarce la transacción";
}

?>
</body>
</html>