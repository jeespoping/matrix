<?php
include_once("conex.php");

/*
-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
2016-05-17 ( camilo Zapata ):
 se hacen todas las modificaciones necesarias para empezar a guardar las respuestas a las glosas segun la resolución 3047, cuando una glosa se acepta parcial, total o no se acepta por rechazo o subsanación
 en caso de ser necesario sería util buscar la palabra "respuestaGlosa" para hacer seguimiento a los algoritmos

2014-11-18 ( camilo Zapata ):
 se modificó el query que busca las facturas que se van a enviar para que haga el calculo del valor cancelado utilizando la tabla de conceptos y el factor de movimiento( 1 o -1 )
2014-10-30 ( camilo Zapata ):
 se modificó el query que busca las facturas radicadas que se pueden enviar, para que las agrupe, evitando la duplicación de facturas, en pantalla
2014-09-08 ( camilo Zapata ):
 se modificó el programa agregandole un join con la tabla 18 para ordenar por fecha de facturación, a la hora de consultar las facturas incluídas en un documento.
2014-01-14 ( Camilo Zapata ):
 Este programa se modificó casi en su totalidad, aunque no precisamente en su lógica, las modificaciones se realizaron en la éstructura de la programación, implementandose la tecnología
 ajax, pero la forma en que los movimientos de las facturas se hacen continuan teniendo las mismas reglas y el mismo flujo del proceso.
 Cabe anotar que se agregó una fuente mas( Cobros Juridicos ) la cual fue solicitada por el personal de contabilidad en la clinica del sur.
-----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
*/
if(isset($vaciar45))
{




	$fglo = explode("-",$fglo);
	$usua = explode("-", $usuario);
	//pilas borrar por fuente(que no sea de glosa) y por usuario
	$q = "DELETE
		    FROM {$wbasedato}_000045
		   WHERE Temfue <> '{$fglo[0]}'
			 AND Seguridad = 'A-{$usua[1]}'";
	//echo $q;
	$rs = mysql_query($q, $conex);

	return;
}

if(isset($docajax))//php que retorna el número del documento por petición ajax
{




	$fu = explode("-",$nfuente);

		$query =  " SELECT Carcon
						FROM ".$wbasedato."_000040
						WHERE Carfue='".$fu[0]."'
						AND Carest='on'";
		$err = mysql_query($query,$conex);
		//echo mysql_errno() ."=". mysql_error();
		$num = mysql_fetch_array($err);

		if ($num[0]=='')
		$num=1;
		else
		$num=$num[0]+1;

		echo "<input type='hidden' id='dcto' name='dcto' value='".$num."'>";
		echo "<input type='hidden' id='fue' name='fue' value='".$fu[0]."'>";
		echo "<td align=left class='fila2'><b>Documento Nro: <br></b>".$num."</td>";
   return;
}

if(isset($opciajax))//construye las opciones
{
	$fu = explode("-", $opciajax);
	if($fu[0]==80)//envios
	{

		//echo "<select id=menu onchange='ejecutarOpc(this.value);'>";
			/*echo "<input type='radio' name='opcion' onclick='ejecutarOpc(this.value)' value='00'>--<br>";*/
			//echo "<option value='00'>--</option>";
			echo "<input type='radio' name='opcion' onclick='ejecutarOpc(this.value)' value='01'>Generar  por rango de fechas<br>";
			//echo "<option value='01'>Generar Documento por rango de fechas</option>";
			echo "<input type='radio' name='opcion' onclick='ejecutarOpc(this.value)' value='02'>Generar  por ingresos Manuales<br>";
			//echo "<option value='02'>Generar Documento por ingresos Manuales</option>";
			echo "<input type='radio' name='opcion' onclick='ejecutarOpc(this.value)' value='03'>Consultar/Anular Documento<br>";
			//echo "<option value='03'>Consultar/Anular Documento</option>";
		//echo "</select>";
		////echo "</select>";
		echo "<input type='hidden' id='fue' name='fue' value='".$fu[0]."'>";
		//echo "<input type='hidden' id='fue' name='fue' value='".$fu[0]."'>";
	}
	if($fu[0]==82)//radicacion
	{
		//echo "<select id=menu onchange='ejecutarOpc(this.value);'>";
		////echo "<select id=menu onchange='ejecutarOpc(this.value);'>";
			/*echo "<input type='radio' name='opcion' onclick='ejecutarOpc(this.value)' value='00'>--<br>";*/
			//echo "<option value='00'>--</option>";
			echo "<input type='radio' name='opcion' onclick='ejecutarOpc(this.value)' value='01'>Generar por rango de fechas<br>";
			//echo "<option value='01'>Generar Radicaci&oacute;n por rango de fechas</option>";
			echo "<input type='radio' name='opcion' onclick='ejecutarOpc(this.value)' value='02'>Generar por Env&iacute;o<br>";
			//echo "<option value='02'>Generar Radicaci&oacute;n por Env&iacute;o</option>";
			echo "<input type='radio' name='opcion' onclick='ejecutarOpc(this.value)' value='03'>Consultar/Anular Radicaci&oacute;n<br>";
			//echo "<option value='03'>Consultar/Anular Radicaci&oacute;n</option>";
		//echo "</select>";
		////echo "</select>";
		echo "<input type='hidden' id='fue' name='fue' value='".$fu[0]."'>";
		//echo "<input type='hidden' id='fue' name='fue' value='".$fu[0]."'>";
	}
	if($fu[0]==81)
	{
		//echo "<select id=menu onchange='ejecutarOpc(this.value);'>";
		////echo "<select id=menu onchange='ejecutarOpc(this.value);'>";
			/*echo "<input type='radio' name='opcion' onclick='ejecutarOpc(this.value)' value='00'>--<br>";*/
			//echo "<option value='00'>--</option>";
			echo "<input type='radio' name='opcion' onclick='ejecutarOpc(this.value)' value='01'>Generar por rango de fechas<br>";
			//echo "<option value='01'>Generar Devoluci&oacute;n por rango de fechas</option>";
			echo "<input type='radio' name='opcion' onclick='ejecutarOpc(this.value)' value='02'>Generar por Radicaci&oacute;n<br>";
			//echo "<option value='02'>Generar Devoluci&oacute;n por Radicaci&oacute;n</option>";
			echo "<input type='radio' name='opcion' onclick='ejecutarOpc(this.value)' value='03'>Consultar/Anular Devoluci&oacute;n<br>";
			//echo "<option value='03'>Consultar/Anular Devoluci&oacute;n</option>";
		//echo "</select>";
		////echo "</select>";
		echo "<input type='hidden' id='fue' name='fue' value='".$fu[0]."'>";
		//echo "<input type='hidden' id='fue' name='fue' value='".$fu[0]."'>";
	}
	if($fu[0]==85)
	{
		//echo "<select id=menu onchange='ejecutarOpc(this.value);'>";
		////echo "<select id=menu onchange='ejecutarOpc(this.value);'>";
			/*echo "<input type='radio' name='opcion' onclick='ejecutarOpc(this.value)' value='00'>--<br>";*/
			//echo "<option value='00'>--</option>";
			echo "<input type='radio' name='opcion' onclick='ejecutarOpc(this.value)' value='02'>Generar Glosas<br>";
			//echo "<option value='02'>Generar Glosas</option><br>			//echo "<option value='02'>Generar Devoluci&oacute;n por Radicaci&oacute;n</option>";
			////echo "<option value='02'>Generar Devoluci&oacute;n por Radicaci&oacute;n</option>";
			echo "<input type='radio' name='opcion' onclick='ejecutarOpc(this.value)' value='03'>Consultar/Anular Glosa<br>";
			//echo "<option value='03'>Consultar/Anular Glosa</option>";
		//echo "</select>";
		////echo "</select>";
		echo "<input type='hidden' id='fue' name='fue' value='".$fu[0]."'>";
		//echo "<input type='hidden' id='fue' name='fue' value='".$fu[0]."'>";
	}

	if($fu[0]==86)
	{
		//echo "<select id=menu onchange='ejecutarOpc(this.value);'>";
		////echo "<select id=menu onchange='ejecutarOpc(this.value);'>";
			/*echo "<input type='radio' name='opcion' onclick='ejecutarOpc(this.value)' value='00'>--<br>";*/
			//echo "<option value='00'>--</option>";
			echo "<input type='radio' name='opcion' onclick='ejecutarOpc(this.value)' value='01'>Generar por rango de fechas<br>";
			//echo "<option value='01'>Generar Cobro Juridico por rango de fechas</option>";
			echo "<input type='radio' name='opcion' onclick='ejecutarOpc(this.value)' value='02'>Generar por ingresos manuales<br>";
			//echo "<option value='02'>Generar Cobro Juridico por ingresos manuales</option>";
			echo "<input type='radio' name='opcion' onclick='ejecutarOpc(this.value)' value='03'>Consultar/Anular Cobro Juridico<br>";
			//echo "<option value='03'>Consultar/Anular Cobro Juridico</option>";
		//echo "</select>";
		////echo "</select>";
		echo "<input type='hidden' id='fue' name='fue' value='".$fu[0]."'>";
		//echo "<input type='hidden' id='fue' name='fue' value='".$fu[0]."'>";
	}
	echo "<input type='hidden' id='menu' value=''>";
	/*echo "</tr>";
	//echo "</tr>";
	echo "</table></center>";*/
	//echo "</table></center>";*/
	return;
}

if(isset($ajaxBusc))// busca el responsable cuando se escribe
{





	if($ajaxBusc=='rf')//para buscar por rango de fechas inicialmente
	{
		echo "<center><table width='90%' id='tbresponsable'>";
		echo "<tr class='fila1' align='center''><td id='filares' colspan=3><b> Responsable:<br> <input type='text' id='busc' name='busc' value='".$busc."' onblur='buscarResponsable(\"nm\");' onkeypress='if (validar(event)) buscarResponsable(\"nm\")'> ";
		echo "<select id='responsable' name='responsable'>";
					$query =  " SELECT empcod, empnit, empnom
							FROM ".$wbasedato."_000024
							WHERE Empcod=Empres
							ORDER BY empcod ";
					$err = mysql_query($query,$conex);
					$num = mysql_num_rows($err);
					for ($i=1;$i<=$num;$i++)
					{
						$row = mysql_fetch_array($err);
						echo "<option value='".$row[0]."-".$row[1]."-".$row[2]."'>".$row[0]." - ".$row[1]." - ".$row[2]."</option>";
					}
					echo "</select></td>";
		echo "</tr>";
		echo "</table></center>";
		return;
	}

	if($ajaxBusc!='im')
	{
		if($ajaxBusc=='cd')
		{
			$fuecon=explode("-", $fuente);
			$query =  " SELECT Empcod, Empnit, Empnom
					FROM ".$wbasedato."_000020, ".$wbasedato."_000024
					WHERE  Rennum = '".$codoc."'
					AND Rencod=Empcod
					AND Renfue = '".$fuecon[0]."'";
	            $err = mysql_query($query,$conex);
	            $respo1 = mysql_fetch_array($err);
	            $respo=$respo1[0]."-".$respo1[1]."-".$respo1[2];

	          echo "<center><table width='90%' id='tbresponsable'>";
			  echo "<tr><td align=center id='filares' class='fila1' colspan=4 ><b> Responsable: <br></b>".$respo."</td></tr>";
	          echo "<input type='hidden' id='responsable' name='responsable' value='".$respo."'>";
			  echo "</table></center>";
		}else{
				echo "<center><table width='90%' id='tbresponsable'>";
				echo "<tr><td align=center class='fila1' id='filares' colspan=4><b> Responsable: <br></b><INPUT TYPE='text'  id='busc' name='busc' VALUE='".$busc."' onblur='buscarResponsable(\"nm\");' onkeypress='if (validar(event)) buscarResponsable(\"nm\")'>"
					."<select id='responsable' name='responsable'>";
				$query =  " SELECT empcod, empnit, empnom
							 FROM ".$wbasedato."_000024
							WHERE Empcod=Empres
							AND (Empcod like'%".$busc."%'
							OR Empnit like'%".$busc."%'
							OR Empnom like'%".$busc."%')
							ORDER BY empcod ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				for ($i=1;$i<=$num;$i++)

				{
				   $row = mysql_fetch_array($err);
				   echo "<option value=".$row[0]."-".$row[1]."-".$row[2].">".$row[0]." - ".$row[1]." - ".$row[2]."</option>";
				}
				echo "</select></td></tr>";
				echo "</table></center>";
			 }
	}else
		{
			$query =  " SELECT *
                FROM ".$wbasedato."_000030, ".$wbasedato."_000045
              	WHERE Cjecco=Temsuc
              	AND Cjecaj=Temcaj
              	AND Cjeusu='".$usua[1]."'";
			$err1 = mysql_query($query,$conex);
			$si = mysql_num_rows($err1);
			if ($si=='0')
			{
				$query =  " SELECT DISTINCT empcod, empnit, empnom
							FROM ".$wbasedato."_000018, ".$wbasedato."_000024
				     	   WHERE Fenffa='".$fue."'
							 AND Fenfac='".$fact."'
						 	 AND Fencod=Empcod";

				$err1 = mysql_query($query,$conex);
				$respo = mysql_fetch_array($err1);
				$responsable=$respo[0]."-".$respo[1]."-".$respo[2];
				//echo $responsable;
				echo "<center><table width='90%' id='tbresponsable'>";
				echo "<tr><td align=center id='filares' class='fila1' colspan=4 ><b> Responsable: <br></b>".$responsable."</td></tr>";
				echo "<input type='hidden' id='responsable' name='responsable' value='".$responsable."'>";
				echo "</table></center>";
			}
		}
	return;
}

////********************************************************************FUNCIONES PHP PARA LAS ENVIOS*********************************************************************************
if(isset($ajaxEnvT))// lista todas las facturas que se pueden enviar(ge, gl, dv)
{




	$cc=explode('-',$suc);
  function stripAccents($string)
  {
    $string = str_replace("ç","c",$string);
    $string = str_replace("Ç","C",$string);
    $string = str_replace("Ý","Y",$string);
    $string = str_replace("ý","y",$string);
    $string = str_replace("?","",$string);
    $string = str_replace("Â","",$string);
    $string = str_replace("Ã","",$string);
    $string = str_replace("?","",$string);
    $string = str_replace("Ãƒâ€˜","Ñ",$string);
    return $string;
  }

  $queryGlosa =  "SELECT Carfue
					 FROM {$wbasedato}_000040
					WHERE Carglo='on'
					  AND Carest='on'";
	$rsGlosa = mysql_query($queryGlosa,$conex);
	$rowGlosa = mysql_fetch_array($rsGlosa);
	$fuenteGlosa = $rowGlosa[0];

    $res=explode("-",$responsable);
    if (isset($emp))
	    {
	 	$emp=$res[0]."-".$res[2];
	    }

	//2008-11-24
	// se pone pa que traiga los datos sin importar si no tiene historia 2009-11-03
	// se cambia fencod
	$tabla='';
    if($ajaxEnvT=="todo")
	{
		$query =  " SELECT Fenfac, Fenval, Fenffa, Fennpa, Fennit, fenesf
					  FROM ".$wbasedato."_000018
					 WHERE Fenres='".trim($res[0])."'
					   AND Fenfec between '".$fec1."' and '".$fec2."'
					   AND (fenesf='GE'
						OR 	fenesf='DV'
						OR 	fenesf='GL')
					   AND Fenest='on'
					   AND Fensal>0";
	}else
		{
			$query =  " SELECT Fenfac, Fenval, Fenffa, Fennpa, Fennit, fenesf
					  FROM ".$wbasedato."_000018
					 WHERE Fenfac='".$nfac."'
					   AND Fenffa='".$fue."'
					   AND Fenres='".trim($res[0])."'
					   AND (fenesf='GE'
						OR 	fenesf='DV'
						OR 	fenesf='GL')
					   AND Fenest='on'
					   AND Fensal>0";
			//echo $query;
		}
    $err = mysql_query($query,$conex) or die( mysql_error() );
	//or die (mysql_errno() ."=". mysql_error())
    $num = mysql_num_rows($err);
	$ocultas=array();
	if ($num==0 or !isset($num))
        {
			$data=array('tabla'=>utf8_encode($tabla));
			echo json_encode($data);
			return;
        }else
			{
				$facpantalla=$num;
				$inicio=1;
				if($ism>0)
				{
					$inicio=$ism+1;
					$num+=$ism;
					$facpantalla=$ism;
				}

			  for ($i=$inicio;$i<=$num;$i++)
				{
					$row                      = mysql_fetch_array($err);
					$arr[$i]['nf']            = $row[0];
					$arr[$i]['vf']            = $row[1];
					$arr[$i]['ffa']           = $row[2];
					$arr[$i]['nom']           = $row[3];
					$arr[$i]['rs']            = $row[4];
					$arr[$i]['estadoFactura'] = $row[5];

					$numeroGlosa = "";
					if( $row[5] == "GL" ){

						//--> SE CONSULTA LA GLOSA VIGENTE
						$queryGlosa = " SELECT Rdenum, Rdefue, Renfec, SUM(Rdevca)*1 valorGlosa
										  FROM {$wbasedato}_000021,{$wbasedato}_000020
										 WHERE Rdeffa = '{$row[2]}'
										   AND Rdefac = '{$row[0]}'
										   AND Rdefue = '{$fuenteGlosa}'
										   AND renfue = rdefue
										   AND rennum = rdenum
										   AND Rdeest = 'on'
										 GROUP BY 1,2,3
										 ORDER BY Renfec desc
										 LIMIT 1";
						$rsGlosa    = mysql_query($queryGlosa,$conex);
						$rowGlosa   = mysql_fetch_array($rsGlosa);


					    if( $rowGlosa[0] != "" ){//-> si tiene número de glosa ( si está en estado GL, tiene que tener número de glosa)

							$numeroGlosa        = $rowGlosa[1]."-".$rowGlosa[0];
							$valorGlosa         = $rowGlosa['valorGlosa'];
							$respuestaGlosa     = "";
							//--> consulto si esta glosa ya tiene respuesta en la tabla 000261
							$qResGlosa = " SELECT Rescod
											 FROM {$wbasedato}_000262
										    WHERE Resffa = '{$row[2]}'
										      AND Resfac = '{$row[0]}'
										      AND Resfue = '{$fuenteGlosa}'
										      AND Resnum = '{$rowGlosa[0]}'
										      AND Resest = 'on'";
							$rsResGlosa = mysql_query($qResGlosa,$conex) or die(mysql_error());
							while( $rowResGlosa = mysql_fetch_array($rsResGlosa) ){
								$respuestaGlosa = $rowResGlosa[0];
							}

							$porcentajeAceptado = 0;

							if( $respuestaGlosa == ""){
						    	//-> SE BUSCAN NOTAS CRÉDITO ASOCIADAS A ESTA GLOSA
						    	$queryGlosa = " SELECT Rdenum, Rdefue, SUM(Rdevco)*1 valorGlosaAceptado
												  FROM {$wbasedato}_000021
												 WHERE Rdeffa = '{$row[2]}'
												   AND Rdefac = '{$row[0]}'
												   AND Rdeglo= '$numeroGlosa'
											   	   AND Rdeest = 'on'
											   	 GROUP BY 1,2";

								$rsGlosa    = mysql_query($queryGlosa,$conex);
								$valorAceptado = 0;
								while( $rowGlosa   = mysql_fetch_array($rsGlosa) ){
									$valorAceptado += $rowGlosa['valorGlosaAceptado'];
								}

								if( $valorAceptado == $valorGlosa){
									$porcentajeAceptado = 100;
								}
								if( $valorGlosa > $valorAceptado and $valorAceptado > 0 ){
									$porcentajeAceptado = 90; //--> este es un valor arbitrario, ya que solo nos interesa que se aceptó parcialmente, al 100%
								}
							}
					    }else{

					    }
					    //$ocultas[$i] .= "<input type='hidden' name='arr[".$i."][numeroGlosa]' value='".$arr[$i]['numeroGlosa']."'>";/////////////////nombre
					}

				    $ocultas[$i] .= "<input type='hidden' id='arr[".$i."][nf]' name='arr[".$i."][nf]' value='".$arr[$i]['nf']."'>";
					$ocultas[$i] .= "<input type='hidden' name='arr[".$i."][vf]' value='".$arr[$i]['vf']."'>";
					$ocultas[$i] .= "<input type='hidden' id='arr[".$i."][ffa]' name='arr[".$i."][ffa]' value='".$arr[$i]['ffa']."'>";
					$ocultas[$i] .= "<input type='hidden' name='arr[".$i."][nom]' value='".$arr[$i]['nom']."'>";/////////////////nombre
					$ocultas[$i] .= "<input type='hidden' name='arr[".$i."][estadoFactura]' value='".$arr[$i]['estadoFactura']."' fuenteFactura='{$arr[$i]['ffa']}' numeroFactura='{$arr[$i]['nf']}' numeroDeGlosa='{$numeroGlosa}' respuestaGlosa='{$respuestaGlosa}' valorGlosa='{$valorGlosa}' valorGlosaAceptado='{$valorAceptado}' porcentajeAceptado='{$porcentajeAceptado}'>";/////////////////nombre

					////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA CONSULTA QUE BUSCA LAS NOTAS Y RECIBOS DE CADA FACTURA

					 /////////////////////////////////////////////////////////////////////////////////////////////////nota debito
					 $query =  " SELECT DISTINCT Renfue, Rennum,Renvca
				                FROM ".$wbasedato."_000021 a, ".$wbasedato."_000040, ".$wbasedato."_000020, ".$wbasedato."_000024
				              	WHERE Rdefac='".$arr[$i]['nf']."'
				              	AND Carfue=Rdefue
				              	AND Carndb='on'
				              	AND Rdeest='on'
								AND Empnit = '".$arr[$i]['rs']."'
								AND Empres = Rencod
				              	AND Carest='on'
				              	AND Renfue=Rdefue
				              	AND Rennum=Rdenum
								HAVING (Max(a.id))";

					$err1 = mysql_query($query,$conex);
					$numnd = mysql_num_rows($err1);
					$y=0;

					for ($j=1;$j<=$numnd;$j++)
						{

							$rownd = mysql_fetch_array($err1);
							$fn[$i][$j]=$rownd[0];
							$nn[$i][$j]=$rownd[1];
							$vn[$i][$j]=$rownd[2];
							$in[$i][$j]='nd';
							$y=1;
						}
					/////////////////////////////////////////////////////////////////////////////////////////////////nota credito
					if( $wemp_pmla != "02" )
						{	//Se quema temporalmente este query para evitar problemas con los envios

							$query = " SELECT DISTINCT Renfue, Rennum, Renvca
										FROM ".$wbasedato."_000021 a, ".$wbasedato."_000040, ".$wbasedato."_000020, ".$wbasedato."_000024
										WHERE Rdefac='".$arr[$i]['nf']."'
										AND Carfue=Rdefue
										AND Carncr='on'
										AND Rdeest='on'
										AND Carest='on'
										AND Empnit = '".$arr[$i]['rs']."'
										AND Empres = Rencod
										AND Renfue=Rdefue
										AND Rennum=Rdenum
										HAVING (Max(a.id))";
							//echo $query;
						}else
							{
								 $query = " SELECT DISTINCT Renfue, Rennum, Renvca
											FROM ".$wbasedato."_000021 a, ".$wbasedato."_000040, ".$wbasedato."_000020, ".$wbasedato."_000024
											WHERE Rdefac='".$arr[$i]['nf']."'
											AND Carfue=Rdefue
											AND Carncr='on'
											AND Rdeest='on'
											AND Carest='on'
											AND Empnit = '".$arr[$i]['rs']."'
											AND Empres = Rencod
											AND Renfue=Rdefue
											AND Rennum=Rdenum
											AND Carcfa='on'
											HAVING (Max(a.id))";;	//Enero 06 de 2012, Se agrega esta condicion AND Carcfa='on', este cambio es temporal
							}

					$err2 = mysql_query($query,$conex);
					$numnc = mysql_num_rows($err2);


					$suma=$numnd+$numnc;
					for ($j=$numnd+1;$j<=$suma;$j++)
					{
						$rownc = mysql_fetch_array($err2);
						$fn[$i][$j]=$rownc[0];
						$nn[$i][$j]=$rownc[1];
						$vn[$i][$j]=$rownc[2];
						$in[$i][$j]='nc';
						$y=1;
					}
					/////////////////////////////////////////////////////////////////////////////////para los recibos
					$query =  " SELECT Rdefue, Rdenum, (Rdevca + ( Rdevco*conmul*-1)) as Rdevca
				                FROM ".$wbasedato."_000021 a, ".$wbasedato."_000040, ".$wbasedato."_000020, ".$wbasedato."_000024, ".$wbasedato."_000044
				              	WHERE Rdefue=Carfue
				              	AND Rdeest='on'
				              	AND Rdefac='".$arr[$i]['nf']."'
				              	AND Rdereg = 0
								AND Empnit = '".$arr[$i]['rs']."'
								AND Empres = Rencod
								AND Renfue=Rdefue
								AND Rennum=Rdenum
				              	AND Carrec='on'
				              	AND Carfue not in (select Ccofrc from ".$wbasedato."_000003 where Ccoest='on')
				              	AND Carest='on'
				              	AND conest ='on'
				              	AND confue =rdefue
				                AND concod =mid(rdecon,1,instr(rdecon,'-')-1)";
					$err3 = mysql_query($query,$conex);
					$numrc = mysql_num_rows($err3);
					//echo mysql_errno() ."=". mysql_error();
					$suma1=$numnd+$numnc+$numrc;

					for ($j=$suma+1;$j<=$suma1;$j++)
						{

							$rowrc = mysql_fetch_array($err3);
							$fn[$i][$j]=$rowrc[0];
							$nn[$i][$j]=$rowrc[1];
							$vn[$i][$j]=$rowrc[2];
							$in[$i][$j]='rc';
							$y=1;

						}


					//////////////////////////////////////////////////////////////////////////////////////////////para facturas sin nada
					if ($y==0)
					{
						$fn[$i][1]=0;
						$in[$i][1]='';
						$nn[$i][$j]=0;
						$vn[$i][$j]=0;
					}
				}
			}

			$numero=count($arr);
			//////////////////////////////////////aca comieza a pintar
            if ($arr!=0)
            {
                ///////////////////////////////////////////////////query para traer los campos obligatotrios por empresa
	            $res=explode('-',$responsable);

				$query =  " SELECT Cobnpa, Cobnta
				             FROM ".$wbasedato."_000121
				             WHERE Cobest='on'
				             AND Cobcod='".$res[0]."'";
	            $err = mysql_query($query,$conex);
	            $numco = mysql_num_rows($err);

	            //echo "<input type='hidden' name='numco' value='".$numco."'>";

				$csp=0;
				$csp=12+$numco;
				$tamCol='style=width:90px;';

				$tmtb=$csp*90;
				/*if($ism>0)
					$tmtb='100%';*/
					if ($numco>0)
					   {
							if($ism==0)
							{
								$tabla .= "<center><table border='0' width='".$tmtb."'>";
								$tabla .= "<tr><td class='encabezadoTabla' colspan=".($csp-2)." align=center><b>DETALLE DE LAS FACTURAS QUE VAN A SER ENVIADAS</b></td><td class='encabezadoTabla'>TOTAL DOC</td><td class='encabezadoTabla' id='total2' align='rigth'>0</td></tr>";
								$tabla .= "<tr><td class='fondoBlanco' colspan=".$csp." align='left'>&nbsp;<input type='checkbox' id='todas' name='todas' onclick='cambiarTodas(\"$fuente\");'>Seleccionar todas</td></tr>";
								$tabla .= "<tr id='encabezado' class='encabezadoTabla'><td ".$tamCol."><b>Enviar</b></td>";
							}
							for ($l=1;$l<=$numco;$l++)
							{
								$rowco = mysql_fetch_array($err);
								$arr[$l]['co']=$rowco[1];
								//echo "<input type='hidden' name='arr[".$l."][co]' value='".$arr[$l]['co']."'>";
								if($ism==0)
								{
								$tabla .= "<td ".$tamCol."><b>".$rowco[0]."</b></td>";
								}
							}

							if($ism==0)
							{
							$tabla .= "<td ".$tamCol."><b>Fuente de la Factura</b></td><td ".$tamCol."><b>Nro Factura</td><td ".$tamCol."><b>Valor Factura</b></td><td ".$tamCol."><b>Nombre</b></td><td ".$tamCol."><b>Nro Nota Credito</b></td><td ".$tamCol."><b>Valor Nota Credito</b></td>"
									  ."<td ".$tamCol."><b>Nro Nota Debito</b></td><td ".$tamCol."><b>Valor Nota Debito</b></td><td ".$tamCol."><b>Nro Recibo</b></td><td ".$tamCol."><b>Valor Recibo</b></td><td ".$tamCol."><b>Valor Total Factura</b></td></tr>";
							}

					   }else{
								if($ism==0)
								{
									$tabla .= "<center><table border='0' width='".$tmtb."'>";
									$tabla .= "<tr><td class='encabezadoTabla' colspan=10 align=center><b>DETALLE DE LAS FACTURAS QUE VAN A SER ENVIADAS</b></td><td class='encabezadoTabla'>TOTAL DOC</td><td class='encabezadoTabla' id='total2' align='right'>0</td></tr>";
									$tabla .= "<tr><td class='fondoBlanco' colspan=12 align='left'>&nbsp;<input type='checkbox'  id='todas' name='todas' onclick='cambiarTodas(\"$fuente\");'>Seleccionar todas</td></tr>";
									$tabla .= "<tr id='encabezado' class='encabezadoTabla'><td ".$tamCol."><b>Enviar</b></td><td ".$tamCol."><b>Fuente de la Factura</b></td><td ".$tamCol."><b>Nro Factura</b></td><td ".$tamCol."><b>Valor Factura</b></td><td ".$tamCol."><b>Nombre</b></td><td ".$tamCol."><b>N Nota Credito</b></td><td ".$tamCol."><b>Valor Nota Credito</b></td>
											<td ".$tamCol."><b>Nro Nota Debito</b></td><td ".$tamCol."><b>Valor Nota Debito</b></td><td ".$tamCol."><b>Nro Recibo</b></td><td ".$tamCol."><b>Valor Recibos</b></td><td ".$tamCol."><b>Valor Total Factura</b></td></tr>";
								}
					   }
            }

			$inicio=1;
			if($ism>0)
			{
				$inicio=$ism+1;
				$numero+=$ism;
			}
            for ($i=$inicio;$i<=$numero;$i++)
            {
				if (is_int ($i/2))
				   $wcf="fila1";  // color de fondo de la fila
				else
				   $wcf="fila2"; // color de fondo de la fila

			   $j=1;
                while (isset($fn[$i][$j]))
                {
                    if ($j==1)
                    {
                    	////////////////////////////////////////para las notas debito
                        if ($in[$i][$j]=='nd')
                        {
                            $stf[$i]['stf']=$arr[$i]['vf']+$vn[$i][$j];
                            $tabla .= "<tr class=".$wcf."><td align='center' ".$tamCol."><input type='checkbox' id='wgrabar[".$i."]' name='wgrabar[".$i."]' onclick='cambiarEstado($i, \"off\");'/></td>";

                            //////////////////////////////////query pa traer los numeros obligatorios y ponerlos en un textbox
                             for ($l=1;$l<=$numco;$l++)
	            			{
			                	$query =  " SELECT ".$arr[$l]['co']."
								             FROM ".$wbasedato."_000101, ".$wbasedato."_000018
								             WHERE Fenhis=Inghis
								             AND Fening=Ingnin
								             AND Fenfac='".$arr[$i]['nf']."'
								             AND Fenest='on'";

					           $err = mysql_query($query,$conex);
				           	   $numdo = mysql_num_rows($err);
					            //echo mysql_errno() ."=". mysql_error();

				                $rowdo = mysql_fetch_array($err);
				               	$arr[$l][$i]=$rowdo[0];

				               	$tabla .= "<td ".$tamCol."><INPUT TYPE='text' NAME='arrn[".$l."][".$i."]' VALUE='".$arr[$l][$i]."' SIZE=13></td>";
	            			}

                            $tabla .= "<td ".$tamCol.">&nbsp</td><td ".$tamCol.">".$arr[$i]['nf']."</td><td align=right ".$tamCol.">".number_format($arr[$i]['vf'],0,'.',',')."</td><td ".$tamCol.">".stripAccents($arr[$i]['nom'])."</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">".$fn[$i][$j]."-".$nn[$i][$j]."</td><td align=right ".$tamCol.">".number_format($vn[$i][$j],0,'.',',')."</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td>";
                            if (!isset($fn[$i][$j+1]))
                            $tabla .= "<td align=right ".$tamCol." id='st".$i."'>".number_format($stf[$i]['stf'],0,'.',',')."</td></tr>";
                            else
                            $tabla .= "<td ".$tamCol.">&nbsp</td></tr>";
                        }
                        ////////////////////////////////////////para las notas credito
                        else if ($in[$i][$j]=='nc')
                        {
                            $stf[$i]['stf']=$arr[$i]['vf']-$vn[$i][$j];
                            $tabla .= "<tr class=".$wcf."><td align='center' ".$tamCol."><input type='checkbox' id='wgrabar[".$i."]' name='wgrabar[".$i."]' onclick='cambiarEstado($i, \"off\");'/></td>";

                             //////////////////////////////////query pa traer los numeros obligatorios y ponerlos en un textbox
                           for ($l=1;$l<=$numco;$l++)
	            			{
			                	$query =  " SELECT ".$arr[$l]['co']."
								             FROM ".$wbasedato."_000101, ".$wbasedato."_000018
								             WHERE Fenhis=Inghis
								             AND Fening=Ingnin
								             AND Fenfac='".$arr[$i]['nf']."'
								             AND Fenest='on'";

					           $err = mysql_query($query,$conex);
				           	   $numdo = mysql_num_rows($err);
					            //echo mysql_errno() ."=". mysql_error();

				                $rowdo = mysql_fetch_array($err);
				               	$arr[$l][$i]=$rowdo[0];

				               	$tabla .= "<td ".$tamCol."><INPUT TYPE='text' NAME='arrn[".$l."][".$i."]' VALUE='".$arr[$l][$i]."' SIZE=13/></td>";
	            			}

	            			$tabla .= "<td ".$tamCol.">".$arr[$i]['ffa']."</td><td ".$tamCol.">".$arr[$i]['nf']."</td><td align=right ".$tamCol.">".number_format($arr[$i]['vf'],0,'.',',')."</td><td ".$tamCol.">".$arr[$i]['nom']."</td><td ".$tamCol.">".$fn[$i][$j]."-".$nn[$i][$j]."</td><td align=right ".$tamCol.">".number_format($vn[$i][$j],0,'.',',')."</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td>";
                            if (!isset($fn[$i][$j+1]))
                            $tabla .= "<td align=right ".$tamCol." id='st".$i."'>".number_format($stf[$i]['stf'],0,'.',',')."</td></tr>";
                            else
                            $tabla .= "<td ".$tamCol.">&nbsp</td></tr>";
                        }
                        ////////////////////////////////////////para los recibos
                        else if ($in[$i][$j]=='rc')
                        {
                            $stf[$i]['stf']=$arr[$i]['vf']-$vn[$i][$j];
                            $tabla .= "<tr class=".$wcf."><td ".$tamCol." align=center><input type='checkbox' id='wgrabar[".$i."]' name='wgrabar[".$i."]' onclick='cambiarEstado($i, \"off\");'/></td>";
                             //////////////////////////////////query pa traer los numeros obligatorios y ponerlos en un textbox
                           for ($l=1;$l<=$numco;$l++)
	            			{
			                	$query =  " SELECT ".$arr[$l]['co']."
								             FROM ".$wbasedato."_000101, ".$wbasedato."_000018
								             WHERE Fenhis=Inghis
								             AND Fening=Ingnin
								             AND Fenfac='".$arr[$i]['nf']."'
								             AND Fenest='on'";

					           $err = mysql_query($query,$conex);
				           	   $numdo = mysql_num_rows($err);
					            //echo mysql_errno() ."=". mysql_error();

				                $rowdo = mysql_fetch_array($err);
				               	$arr[$l][$i]=$rowdo[0];

				               	$tabla .= "<td ".$tamCol."><INPUT TYPE='text' NAME='arrn[".$l."][".$i."]' VALUE='".$arr[$l][$i]."' SIZE=13></td>";
	            			}

	            			$tabla .= "<td ".$tamCol.">".$arr[$i]['ffa']."</td><td ".$tamCol.">".$arr[$i]['nf']."</td><td align=right ".$tamCol.">".number_format($arr[$i]['vf'],0,'.',',')."</td><td ".$tamCol.">".$arr[$i]['nom']."</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">".$fn[$i][$j]."-".$nn[$i][$j]."</td><td align=right ".$tamCol.">".number_format($vn[$i][$j],0,'.',',')."</td>";
                            if (!isset($fn[$i][$j+1]))
                            $tabla .= "<td align=right ".$tamCol." id='st".$i."'>".number_format($stf[$i]['stf'],0,'.',',')."</td></tr>";
                            else
                            $tabla .= "<td ".$tamCol.">&nbsp</td></tr>";

                        }else/////////////////////////////////////para facturas sin notas ni recibos
                        {
                           $stf[$i]['stf']=$arr[$i]['vf'];
                            $tabla .= "<tr class=".$wcf."><td align='center' ".$tamCol."><input type='checkbox' id='wgrabar[".$i."]' name='wgrabar[".$i."]' onclick='cambiarEstado($i, \"off\");'></td>";
                            //////////////////////////////////query pa traer los numeros obligatorios y ponerlos en un textbox
                           for ($l=1;$l<=$numco;$l++)
	            			{
			                	$query =  " SELECT ".$arr[$l]['co']."
								              FROM ".$wbasedato."_000101, ".$wbasedato."_000018
								             WHERE Fenhis=Inghis
								               AND Fening=Ingnin
								               AND Fenfac='".$arr[$i]['nf']."'
								               AND Fenest='on'";

								$err = mysql_query($query,$conex);
								$numdo = mysql_num_rows($err);
								//echo mysql_errno() ."=". mysql_error();

				               	$rowdo = mysql_fetch_array($err);
				               	$arr[$l][$i]=$rowdo[0];

				               	$tabla .= "<td ".$tamCol."><INPUT TYPE='text' NAME='arrn[".$l."][".$i."]' VALUE='".$arr[$l][$i]."' SIZE=13></td>";
	            			}
                            $tabla .= "<td ".$tamCol.">".$arr[$i]['ffa']."</td><td ".$tamCol.">".$arr[$i]['nf']."</td><td align=right ".$tamCol.">".number_format($arr[$i]['vf'],0,'.',',')."</td><td ".$tamCol.">".stripAccents($arr[$i]['nom'])."</td><td ".$tamCol.">&nbsp</td><td align=right ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td>";
                            if (!isset($fn[$i][$j+1]))
                            $tabla .= "<td align=right ".$tamCol." id='st".$i."'>".number_format($stf[$i]['stf'],0,'.',',')."</td></tr>";
                            else
                            $tabla .= "<td ".$tamCol.">&nbsp</td></tr>";
                        }
                    }else
                    {
                        if ($in[$i][$j]=='nd')
                        {
                        	if ($numco>0){
                        					$stf[$i]['stf']=$stf[$i]['stf']+$vn[$i][$j];
				                            $tabla .= "<tr class=".$wcf.">";

				                            for ($c=1;$c<=$numco;$c++)
			            						{
			            							$tabla .= "<td ".$tamCol.">&nbsp</td>";
			            						}
				                            $tabla .= "<td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td align=right ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td align=right ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">".$fn[$i][$j]."-".$nn[$i][$j]."</td><td align=right ".$tamCol.">".number_format($vn[$i][$j],0,'.',',')."</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td>";
				                            if (!isset($fn[$i][$j+1]))
				                            $tabla .= "<td align=right ".$tamCol." id='st".$i."'>".number_format($stf[$i]['stf'],0,'.',',')."</td></tr>";
				                            else
				                            $tabla .= "<td ".$tamCol.">&nbsp</td></tr>";

                        	}else{

                            $stf[$i]['stf']=$stf[$i]['stf']+$vn[$i][$j];
                            $tabla .= "<tr class=".$wcf."><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td align=right ".$tamCol.">&nbsp</td><td align=right ".$tamCol.">&nbsp</td><td ".$tamCol.">".$fn[$i][$j]."-".$nn[$i][$j]."</td><td align=right ".$tamCol.">".number_format($vn[$i][$j],0,'.',',')."</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td>";
                            if (!isset($fn[$i][$j+1]))
                            $tabla .= "<td align=right ".$tamCol." id='st".$i."'>".number_format($stf[$i]['stf'],0,'.',',')."</td></tr>";
                            else
                            $tabla .= "<td ".$tamCol.">&nbsp</td></tr>";

                        	}

                        }else if ($in[$i][$j]=='nc')
                        {

                        	if ($numco>0){

                        		$stf[$i]['stf']=$stf[$i]['stf']-$vn[$i][$j];
	                            $tabla .= "<tr class=".$wcf.">";

	                            for ($c=1;$c<=$numco;$c++)
            						{
            							$tabla .= "<td ".$tamCol.">&nbsp</td>";
            						}
	                            $tabla .= "<td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td align=right ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">".$fn[$i][$j]."-".$nn[$i][$j]."</td><td align=right ".$tamCol.">".number_format($vn[$i][$j],0,'.',',')."</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td>";
	                            if (!isset($fn[$i][$j+1]))
	                            $tabla .= "<td align=right ".$tamCol." id='st".$i."'>".number_format($stf[$i]['stf'],0,'.',',')."</td></tr>";
	                            else
	                            $tabla .= "<td ".$tamCol.">&nbsp</td></tr>";

                        	}
                        	else{

	                        		$stf[$i]['stf']=$stf[$i]['stf']-$vn[$i][$j];
		                            $tabla .= "<tr class=".$wcf."><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td align=right ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">".$fn[$i][$j]."-".$nn[$i][$j]."</td><td align=right ".$tamCol.">".number_format($vn[$i][$j],0,'.',',')."</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td>";
		                            if (!isset($fn[$i][$j+1]))
		                            $tabla .= "<td align=right ".$tamCol." id='st".$i."'>".number_format($stf[$i]['stf'],0,'.',',')."</td></tr>";
		                            else
		                            $tabla .= "<td ".$tamCol.">&nbsp</td></tr>";
                        	}


                        }else if ($in[$i][$j]=='rc')
                        {

                        	if ($numco>0){

                        		$stf[$i]['stf']=$stf[$i]['stf']-$vn[$i][$j];
	                            $tabla .= "<tr class=".$wcf.">";

	                            for ($c=1;$c<=$numco;$c++)
            						{
            							$tabla .= "<td ".$tamCol.">&nbsp</td>";
            						}
	                            $tabla .= "<td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td align=right ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td align=right ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">".$fn[$i][$j]."-".$nn[$i][$j]."</td><td align=right ".$tamCol.">".number_format($vn[$i][$j],0,'.',',')."</td>";
	                            if (!isset($fn[$i][$j+1]))
	                            $tabla .= "<td align=right ".$tamCol." id='st".$i."'>".number_format($stf[$i]['stf'],0,'.',',')."</td></tr>";
	                            else
	                            $tabla .= "<td ".$tamCol.">&nbsp</td></tr>";

                        	}
                        	else{

	                        		$stf[$i]['stf']=$stf[$i]['stf']-$vn[$i][$j];
		                            $tabla .= "<tr class=".$wcf."><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td align=right ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">".$fn[$i][$j]."-".$nn[$i][$j]."</td><td align=right ".$tamCol.">".number_format($vn[$i][$j],0,'.',',')."</td>";
		                            if (!isset($fn[$i][$j+1]))
		                            $tabla .= "<td align=right ".$tamCol." id='st".$i."'>".number_format($stf[$i]['stf'],0,'.',',')."</td></tr>";
		                            else
		                            $tabla .= "<td ".$tamCol.">&nbsp</td></tr>";
                        	}


                        }

                    }
                    $ocultas[$i] .= "<input type='hidden' name='fn[".$i."][".$j."]' value='".$fn[$i][$j]."'>";
                    $ocultas[$i] .= "<input type='hidden' name='nn[".$i."][".$j."]' value='".$nn[$i][$j]."'>";
                    $ocultas[$i] .= "<input type='hidden' name='vn[".$i."][".$j."]' value='".$vn[$i][$j]."'>";
                    $ocultas[$i] .= "<input type='hidden' name='in[".$i."][".$j."]' value='".$in[$i][$j]."'>";
					if($ism>0)
					{
						$ocultas[$i] .= "<input type='hidden' id='facpantalla' name='facpantalla' value='".$facpantalla."'>";
					}
					$tabla 	.= "<tr style='display:none'><td>".$ocultas[$i]."</td></tr>";
                    //echo "<input type='hidden' name='stf[".$i."][stf]' value='".$stf[$i]['stf']."'>";
                    $j++;
                }

            }
			if($ism==0)
			{
				$tabla .= "</table></center>";
				$tabla .= "<input type='hidden' id='facpantalla' name='facpantalla' value='".$facpantalla."'>";
			}
				//$tabla .= "<div id='tbdetalle'></div>";

			$total=0;
			$totalreenv=0;

            if ($arr!=0)
            {
				if($ism==0)
				{
					$tabla .= "<center><table border='0' width='70%'>";
				   if ($numco>0) /////////////////////////////// si tiene campos obligatorios
					   {
						$csp=7+$numco;
						$csp1=12+$numco;

						$tabla .= "<tr class='encabezadoTabla' align=center><td colspan=7 style=width='60%' ><b>VALOR TOTAL DEL DOCUMENTO</td><td colspan=".$csp." width='60%' align=right id='total'>".number_format($total,0,'.',',')."</td></tr>";
						$tabla .= "<tr class='encabezadoTabla' align=center ><td colspan=7 width='60%' ><b>VALOR REENVIADO DEL DOCUMENTO</td><td colspan=".$csp."  width='60%'  align=right id='totalreenv'>".number_format($totalreenv,0,'.',',')."</td></tr>";
						$tabla .= "<tr align=center><td colspan=".$csp1."><b>&nbsp</td></tr>";
						$tabla .= "<tr class='fila1' align=center ><td colspan=".$csp1."><b>OBSERVACION GENERAL DEL DOCUMENTO</td></tr>";
						if (!isset($obser)){
						$obser='';
						}
						$tabla .= "<tr align=center><td colspan=".$csp1." ><TEXTAREA name='obser' id='obser' COLS=100 ROWS=2 align=center>".$obser."</TEXTAREA></td></tr>";
						$tabla .= "<tr align=center><td align=center colspan=".$csp1."><input type='button' value='GRABAR' onclick='guardarEnv();'/></td></tr>";
						$tabla .= "<input type='hidden' name='total' value='".$total."'>";

					   }else{
								$tabla .= "<tr class='encabezadoTabla' align=center ><td colspan=7 width='60%' ><b>VALOR TOTAL DEL DOCUMENTO</td><td colspan=6  width=40%  align=right id='total'>".number_format($total,0,'.',',')."</td></tr>";
								$tabla .= "<tr class='encabezadoTabla' align=center ><td colspan=7 width='60%' ><b>VALOR REENVIADO DEL DOCUMENTO</td><td colspan=6  width=40%  align=right id='totalreenv'>".number_format($totalreenv,0,'.',',')."</td></tr>";
								$tabla .= "<tr align=center ><td colspan=12><b>&nbsp</td></tr>";
								$tabla .= "<tr class='fila1' align=center ><td colspan=12><b>OBSERVACION GENERAL DEL DOCUMENTO</td></tr>";
								if (!isset($obser)){
									$obser='';
								}
								$tabla .= "<tr align=center><td colspan=12 ><textarea  name='obser' id='obser' COLS=100 ROWS=2 align=center>".$obser."</TEXTAREA></td></tr>";
								$tabla .= "<tr align=center><td align=center colspan=12><input type='button' value='GRABAR' onclick='guardarEnv();'/></td></tr>";
								$tabla .= "<input type='hidden' id='total' name='total' value='".$total."'>";
							}
					$tabla .= "</table></center>";
                }
            }
	$data=array('tabla'=>utf8_encode($tabla) );
	echo json_encode($data);
	return;
}

if(isset($ajaxRegTem))//funcion que registra en la tabla temporal aquellos datos a los que se les dió check
{




	$usua=explode('-', $user);
	$cc=explode('-',$suc);
	$respo=explode('-',$responsable);
	if($ajaxRegTem!="add")
	{
		$q = "DELETE"
			."  FROM ".$wbasedato."_000045 "
			." WHERE Temfue ='".$fuente."'"
			."   AND Temdoc ='".$dcto."'"
			."	 AND Temsuc ='".$suc."'"
			."	 AND Temcaj ='".$caja."'"
			."   AND Temres ='".$responsable."'"
			."   AND Temnfa ='".$numfac."'"
			."   AND Temffa ='".$fue."'";
		$err = mysql_query($q,$conex);
	}else
		{
			$i=0;////////////////pongo $i en cero para no modificar mucho el algorimo ya que la i no se tiene en cuenta para ingreso manual
			$res=explode("-",$responsable);
		  //2008-11-24
		  //2009-11-03
		  //se cambia fencod por fenres
			if( $esEnvio == 'on'){
				$estadosPrevios = "AND (fenesf='GE'
					OR 	fenesf='DV'
					OR 	fenesf='GL')";
			}else{
				$estadosPrevios = "AND (fenesf='RD'
					OR 	fenesf='DV'
					OR 	fenesf='GL')";
			}
			$query =" SELECT Fennit, Fencod, Fenres, Fennpa, Fenval, Fennit
					FROM ".$wbasedato."_000018
					WHERE Fenres='".$res[0]."'
					    ".$estadosPrevios."
					AND Fenest='on'
					AND Fenfac='".$numfac."'
					AND Fenffa='".$fue."'
					AND Fensal>0";
			$err = mysql_query($query,$conex);
			//echo mysql_errno() ."=". mysql_error();
			$num = mysql_num_rows($err);
			//echo $query;
			/////////////////////////////////////////query de validacion para no ingresar una factura mas de dos veces

				 $query1 =  " SELECT DISTINCT Temnfa
							FROM ".$wbasedato."_000045
							WHERE Temfue='".$fuente."'
							AND Temdoc='".$dcto."'
							AND Temsuc='".$suc."'
							AND Temnfa='".$numfac."'
							AND Temffa='".$fue."'
							AND Temcaj='".$caja."'
							AND Temres='".$responsable."'";
				$err1 = mysql_query($query1,$conex);
				$numftem = mysql_num_rows($err1);
				//echo mysql_errno() ."=". mysql_error();

			if ($num==1 and (isset($numftem) and $numftem==0))
			{
				$row = mysql_fetch_array($err);
				$nom=$row[3];
				echo "<input type='hidden' name='nom' value='".$nom."'>";/////////////////nombre
				echo "<input type='hidden' name='row[4]' value='".$row[4]."'>";/////////////////valor factura

			  //////////////////////////////////////////////////////////////////////////////el if si no cumple lo de recibos
			   if (0<1)
			   //if ($numrc<=0)
			   {

				/////////////////////////////////////////////////////////////////////////////////////////////////nota debito
					$query =  " SELECT DISTINCT Rdefue, Rdenum,Renvca
								FROM ".$wbasedato."_000021, ".$wbasedato."_000040, ".$wbasedato."_000020, ".$wbasedato."_000024
								WHERE Rdefac='".$numfac."'
								AND Carfue=Rdefue
								AND Carndb='on'
								AND Carest='on'
								AND Empnit = '".$row[5]."'
								AND Empres = Rencod
								AND Renfue=Rdefue
								AND Rennum=Rdenum";
					$err1 = mysql_query($query,$conex);
					$numnd = mysql_num_rows($err1);
					$y=0;
					for ($j=1;$j<=$numnd;$j++)
					{

						$rownd = mysql_fetch_array($err1);


						$fn[$i][$j]=$rownd[0];
						$nn[$i][$j]=$rownd[1];
						$vn[$i][$j]=$rownd[2];
						$in[$i][$j]='nd';
						$y=1;
					}
					/////////////////////////////////////////////////////////////////////////////////////////////////nota credito
					$query =  " SELECT DISTINCT Rdefue, Rdenum, Renvca
								FROM ".$wbasedato."_000021, ".$wbasedato."_000040, ".$wbasedato."_000020
								WHERE Rdefac='".$numfac."'
								AND Carfue=Rdefue
								AND Carncr='on'
								AND Carest='on'
								AND Renfue=Rdefue
								AND Rennum=Rdenum";
					$err2 = mysql_query($query,$conex);
					$numnc = mysql_num_rows($err2);


					$suma=$numnd+$numnc;
					for ($j=$numnd+1;$j<=$suma;$j++)
					{
						$rownc = mysql_fetch_array($err2);

						$fn[$i][$j]=$rownc[0];
						$nn[$i][$j]=$rownc[1];
						$vn[$i][$j]=$rownc[2];
						$in[$i][$j]='nc';
						$y=1;
					}
					/////////////////////////////////////////////////////////////////////////////////para los recibos
					$query =  " SELECT Rdefue, Rdenum, Rdevca
								FROM ".$wbasedato."_000021, ".$wbasedato."_000040, ".$wbasedato."_000020, ".$wbasedato."_000024
								WHERE Rdefue=Carfue
								AND Rdeest='on'
								AND Rdefac='".$numfac."'
								AND Rdereg = 0
								AND Carrec='on'
								AND Carfue not in (select Ccofrc from ".$wbasedato."_000003 where Ccoest='on')
								AND Carest='on'
								AND Empnit = '".$row[5]."'
								AND Empres = Rencod
				              	AND Renfue=Rdefue
				              	AND Rennum=Rdenum";
					$err3 = mysql_query($query,$conex);
					$numrc = mysql_num_rows($err3);
					//echo mysql_errno() ."=". mysql_error();
					$suma1=$numnd+$numnc+$numrc;

					for ($j=$suma+1;$j<=$suma1;$j++)
					{

						$rowrc = mysql_fetch_array($err3);

						$fn[$i][$j]=$rowrc[0];
						$nn[$i][$j]=$rowrc[1];
						$vn[$i][$j]=$rowrc[2];
						$in[$i][$j]='rc';
						$y=1;

					}

					/////////////////////////////////////////////////////////////////////////////////////////////////facturas sin nada
					if ($y==0)
					{
						$fn[$i][1]=0;
						$in[$i][1]='';
						$nn[$i][$j]=0;
						$vn[$i][$j]=0;
					}


				//////////////////////////////////////aca graba en la temporal
				$fec=date("Y-m-d");
				$hora=date("H:i:s");
				if(!isset($fecha))
					$fecha=$fec;
				//echo $responsable;
				$j=1;
				while (isset($fn[$i][$j]))
				{
					//echo $nom;
					   $query="INSERT INTO  ".$wbasedato."_000045
								(Medico,
								Fecha_data,
								Hora_data,
								Temfue,
								Temdoc,
								Temfec,
								Temsuc,
								Temcaj,
								Temres,
								Temvre,
								Temnfa,
								Temvfa,
								Temsfa,
								Temvcf,
								Temcon,
								Temdco,
								Temffa,
								Temnom,
								Seguridad)
						VALUES ('".$wbasedato."',
								'".$fec."',
								'".$hora."',
								'".$fuente."',
								'".$dcto."',
								'".$fecha."',
								'".$suc."',
								'".$caja."',
								'".$responsable."',
								'".$in[$i][$j]."',
								'".$numfac."',
								'".$row[4]."',
								'saldo',
								'".$fn[$i][$j]."-".$nn[$i][$j]."',
								'".$vn[$i][$j]."',
								'".$nom."',
								'".$fue."',
								'".$nom."',
								'A-".$usua[1]."')";
					$err = mysql_query($query,$conex);

					 $j++;

					}

				}
			}

			//acá consultamos si es una factura reenviada.
			$qenvs="SELECT COUNT(*)"
				  ."  FROM ".$wbasedato."_000021 , ".$wbasedato."_000020"
				  ." WHERE rdefue='".$fuente."'"
				  ."   AND rdeffa='".$fue."'"
				  ."   AND rdefac='".$numfac."'"
				  ."   AND rennum=rdenum"
				  ."   AND renfue=rdefue"
				  ."   AND rencod='".$respo[0]."'"
				  ."   AND rdeest='on'";
			$rsenvs = mysql_query($qenvs, $conex);
			$rowenvs= mysql_fetch_array($rsenvs);
			$totReenv=0;

			if($rowenvs[0]>1)// si esa factura está asociada a mas de un documento de envios se busca el valor reenviado
			{
				$qvalf="SELECT fensal"
				  ."  FROM ".$wbasedato."_000018"
				  ." WHERE fenffa='".$fue."'"
				  ."   AND fenfac='".$numfac."'"
				  ."   AND fencod='".$respo[0]."'";
				$rsvalf = mysql_query($qvalf,$conex);
				$rowvalf= mysql_fetch_array($rsvalf);
				$totReenv=$rowvalf[0];   //valor reenviado.
			}
			echo "<input type='hidden' id='fac[".$numero_factura_pantalla."][valreenv]'  value=".$totReenv.">";

		}
return;
}

if( isset( $guardarRespuestaGlosa ) ){





	$hoy   = date("Y-m-d");
	$hora  = date("H:i:s");
	$usua  = explode('-', $user);
	$glosa = explode("-", $numeroGlosa);

	if( $accion == "add" ){
		if( $respuestaSeleccionada == "" ){
			$query = " SELECT regcod, regval, regcon
						 FROM {$wbasedato}_000261
						WHERE regest = 'on'";
			$rs    = mysql_query($query,$conex) or die(mysql_error());

			while( $row = mysql_fetch_array($rs) ){
				switch($row[2]){
					case '==':
						if( $porcentajeAceptado == $row[1] ){
							$respuestaAguardar = $row[0];
						}
						break;
					case '<':
						if( $porcentajeAceptado < $row[1] ){
							$respuestaAguardar = $row[0];
						}
						break;
					default:
						# code...
						break;
				}
			}
		}else{
			$respuestaAguardar = $respuestaSeleccionada;
		}
		$query = "INSERT INTO {$wbasedato}_000262 (   medico     ,  fecha_data  ,  hora_data  ,    Resfue  ,            Resnum   ,     Resffa,            Resfac,       Rescod,           Resest,    Seguridad )
						                   VALUES (  '{$wbasedato}', '{$hoy}',      '{$hora}',       '{$glosa[0]}',    '{$glosa[1]}', '{$fuenteFactura}', '{$numfac}', '{$respuestaAguardar}', 'on', 'C-{$usua[1]}'  )";
		$rs    = mysql_query( $query, $conex ) or die( mysql_error()." $query");
	}

	if( $accion == "rm" ){

		$query = " DELETE
		             FROM {$wbasedato}_000262
					WHERE resffa = '{$fuenteFactura}'
					  AND resfac = '{$numfac}'
					  AND resfue = '{$glosa[0]}'
					  AND resnum = '{$glosa[1]}'
					  AND rescod = '{$respuestaSeleccionada}'";
		//echo $query;
		$rs    = mysql_query( $query, $conex ) or die( mysql_error()." $query");

		$respuestaSeleccionada = "";
		$respuestaAguardar     = $respuestaSeleccionada;
	}
	echo $respuestaAguardar;
	return;
}

if(isset($ajaxGuard))//funcion que guardará en las tablas correspondientes lo que exista en la tabla temporal.
{





	$cc=explode('-',$suc);
	$cod=explode('-',$responsable);
	$usua=explode('-', $user);
	$fecha=date("Y-m-d");
	$hora=date("H:i:s");


	$query =  " SELECT DISTINCT Temnfa, Temffa, Temvfa
					  FROM ".$wbasedato."_000045
					 WHERE Temfue='".$fuente."'
					   AND Temdoc='".$dcto."'
					   AND Temsuc='".$suc."'
					   AND Temcaj='".$caja."'
					   AND Temres='".$responsable."'";
	$err1 = mysql_query($query,$conex);
	$numf = mysql_num_rows($err1);
	/*if( $usua[1]=="0207013" )
		echo $query;*/

	if( $numf > 0){
	////////////////////////////////VERIFICACION PARA GRABAR EL NUMERO DEL ENVIO

	             ////////////////////////////////SE BLOQUEA LA TABLA DE LAS FUENTES
			     $q = "lock table ".$wbasedato."_000040 LOW_PRIORITY WRITE";
				// $errlock = mysql_query($q,$conex);

			     $query =  " SELECT Carcon
							FROM ".$wbasedato."_000040
							WHERE Carfue='".$fuente."'
							AND Carest='on'";
            	$err = mysql_query($query,$conex);
            	//echo mysql_errno() ."=". mysql_error();
            	$num = mysql_fetch_array($err);

            	if ($num[0]=='')
            	$num=1;
            	else
            	$dcto=$num[0]+1;

				 /////////////////Aca actualizo el consecutivo de la fuente
				$q= "   UPDATE ".$wbasedato."_000040 "
				."      SET carcon = carcon + 1 "
				."      WHERE carfue ='".$fuente."'"
				."      AND carest = 'on' ";

				$res1 = mysql_query($q,$conex);


				/////////////////////////////////SE DESBLOQUEA LA TABLA DE FUENTES
				$q = " UNLOCK TABLES";
				$errunlock = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

		////////////////////////////////////////////////////////////////////////////////////////////

		/////////////////////////////////////////////////query pa los campos obligatorios por responsable
		$res=explode('-',$responsable);

		$query =" SELECT Cobnpa, Cobnta
				    FROM ".$wbasedato."_000121
				   WHERE Cobest='on'
				     AND Cobcod='".$res[0]."'";
		$err = mysql_query($query,$conex);
		$numco = mysql_num_rows($err);
		//echo "campos obligatorios-".$query."<br>";
		for($m=1; $m<=$numco; $m++)
		{
			$rowco = mysql_fetch_array($err);
			$arr[$m]['co']=$rowco[1];
		}

		for ($j=1;$j<=$numf;$j++)
		{
			$rowf = mysql_fetch_array($err1);
			$fd[$j]['nufac']=$rowf['Temnfa'];
			$fd[$j]['fuefac']=$rowf['Temffa'];
			$fd[$j]['valfac']=$rowf['Temvfa'];

			for ($l=1;$l<=$numco;$l++)
			{
				///////////////////////////////////////////////////////////////////query para actualizar los campos obligatorios de ingreso manual
				//$rowco = mysql_fetch_array($err);
				$arr[$l]['co']=$rowco[1];

				$query =  " SELECT ".$arr[$l]['co']."
							  FROM ".$wbasedato."_000101, ".$wbasedato."_000018
							 WHERE Fenhis=Inghis
						       AND Fening=Ingnin
							   AND Fenfac='".$fd[$j]['nufac']."'
							   AND Fenest='on'";

				//echo $query."<br>";
				$err = mysql_query($query,$conex) or die(mysql_error());
				$numdo = mysql_num_rows($err);
				$rowdo = mysql_fetch_array($err);
				$arrn[$l][$j]=$rowdo[0];
				$query="UPDATE ".$wbasedato."_000101, ".$wbasedato."_000018
						   SET ".$arr[$l]['co']."='".$arrn[$l][$j]."'
					 	 WHERE Fenfac='".$fd[$j]['nufac']."'
						   AND Fenhis=Inghis
						   AND Fening=Ingnin
						   AND Fenest='on'";

				$err = mysql_query($query,$conex);
			}
			///////////////////////////////////////////////////////query para ingresar las facturas
			$query1="INSERT INTO  ".$wbasedato."_000021
					(Medico,
					Fecha_data,
					Hora_data,
					Rdefue,
					Rdenum,
					Rdecco,
					Rdefac,
					Rdevta,
					Rdevca,
					Rdeest,
					Rdecon,
					Rdevco,
					Rdeffa,
					Seguridad)
			VALUES ('".$wbasedato."',
					'".$fecha."',
					'".$hora."',
					'".$fuente."',
					'".$dcto."',
					'".$cc[0]."',
					'".$fd[$j]['nufac']."',
					'',
					'".$fd[$j]['valfac']."',
					'on',
					'',
					'0',
					'".$fd[$j]['fuefac']."',
					'A-".$usua[1]."')";

			$err2 = mysql_query($query1,$conex);


		    $query= "UPDATE ".$wbasedato."_000018
					SET Fenesf='{$nuevoEstado}'
					WHERE Fenfac='".$fd[$j]['nufac']."'";

			$err = mysql_query($query,$conex);
			/*if( $usua[1]=="0207013" )
					echo $query."<br>";*/

			$query =  " SELECT Temffa, Temnfa, Temvfa, Temnom, Temvre, Temvcf, Temcon
						FROM ".$wbasedato."_000045
						WHERE Temfue='".$fuente."'
						AND Temnfa='".$fd[$j]['nufac']."'
						AND Temdoc='".$dcto."'
						AND Temsuc='".$suc."'
						AND Temcaj='".$caja."'
						AND Temres='".$responsable."'";

			$err = mysql_query($query,$conex);
			$numg = mysql_num_rows($err);


			for ($i=1;$i<=$numg;$i++)
		    {
				//echo mysql_errno() ."=". mysql_error();
				$row = mysql_fetch_array($err);
				$fd[$i]['ncd']=$row['Temvre'];
				$fd[$i]['numnot']=$row['Temvcf'];
				$fd[$i]['valnot']=$row['Temcon'];

				$fuent=explode('-',$fd[$i]['numnot']);//////explode para las funtes de las notas

				/////////////////////////////////////////////////////////////////////////////////este es el insert del encabezado

				$responsa=explode('-',$responsable);//////explode para ingresar el nombre del responsable unicamente
				//echo $responsa[2];

				// Obtengo días de holgura para la fuente
				$qholgura =  " SELECT Cardia
								 FROM ".$wbasedato."_000040
								WHERE Carfue='".$fue[0]."'
								  AND Carest='on'";
				$errholgura = mysql_query($qholgura,$conex);
				$rowholgura = mysql_fetch_array($errholgura);
				$holgura = $rowholgura['Cardia'];
				//echo "<br>Holgura".$holgura;
				if(!isset($holgura) || !$holgura || $holgura=="")
					$holgura = 0;

				// Si no viene definida la fecha desde el formulario asigno la fecha actual
				//echo "<br>Fecha".$wfecha;
				if(!isset($wfecha) || !$wfecha || $wfecha=="" || $wfecha=="0")
					$wfecha = $fecha;

				$querye="INSERT INTO  ".$wbasedato."_000020
							(Medico,
							Fecha_data,
							Hora_data,
							Renfue,
							Rennum,
							Renvca,
							Rencod,
							Rennom,
							Rencaj,
							Renusu,
							Rencco,
							Renest,
							Renfec,
							Rendia,
							Renobs,
							Seguridad)
					VALUES ('".$wbasedato."',
							'".$fecha."',
							'".$hora."',
							'".$fuente."',
							'".$dcto."',
							'".$totalm."',
							'".$cod[0]."',
							'".$responsa[2]."',
							'".$cj[0]."',
							'".$usua[1]."',
							'".$cc[0]."',
							'on',
							'".$wfecha."',
							".$holgura.",
							'".$obser."',
							'A-".$usua[1]."')";

				$erre = mysql_query($querye,$conex);
				if (($fd[$i]['ncd']=='nc')or ($fd[$i]['ncd']=='nd') or ($fd[$i]['ncd']=='rc'))
				{
					//////////////////////////////////////////////query para ingresar las notas
					$query="INSERT INTO  ".$wbasedato."_000021
								(Medico,
								Fecha_data,
								Hora_data,
								Rdefue,
								Rdenum,
								Rdecco,
								Rdefac,
								Rdevta,
								Rdevca,
								Rdeest,
								Rdecon,
								Rdevco,
								Rdeffa,
								Seguridad)
						VALUES ('".$wbasedato."',
								'".$fecha."',
								'".$hora."',
								'".$fuente."',
								'".$dcto."',
								'".$cc[0]."',
								'".$fd[$i]['numnot']."',
								'',
								'".$fd[$i]['valnot']."',
								'on',
								'',
								'0',
								'".$fuent[0]."',
								'A-".$usua[1]."')";
					$err4 = mysql_query($query,$conex);
				}
			}
		}
		///////////////////////////////////////query pa borrar la tabla temporal
		$query =  "DELETE
		FROM ".$wbasedato."_000045
		WHERE Temsuc='".$suc."'
		AND Temcaj='".$caja."'
		AND Seguridad='A-".$usua[1]."'";

		$errdel = mysql_query($query,$conex);
		$hyper="<A HREF='/matrix/ips/procesos/Imp_EnRaDe.php?empresa=".$wbasedato."&amp;cc=".$cc[0]."&amp;dcto=".$dcto."&amp;fuente=".$fuente."' target='new' class='vinculo' style='text-decoration:none; font-weight:bold'>Imprimir Documento</a><br><br>";
		echo "<center><table>";
		echo "<tr><td colspan = '4'><font size='3' color='blue'> EL DOCUMENTO HA SIDO GENERADO</font></td></tr>";
		echo "<tr><td  colspan= '4' align=center>$hyper</td></tr>";
		echo "</table></center>";
	}else{
		echo "error";
	}
	return;
}

if(isset($ajaxcd))//funcion que muestra en pantalla un documento(envio) consultado
{




	$fue= explode("-",$fuente);
	$cod=explode("-",$responsable);

	$cc=explode('-',$suc);

	///////////////////////////////////////////////////query para traer el numero de campos obligatotrios por empresa
	//2007-04-26
	$query =  " SELECT Renvca, Renfec, Rennum, Renest
							FROM ".$wbasedato."_000020
							WHERE Renfue='".$fue[0]."'
							AND Rennum='".$codoc."'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	if($num==0)
	{
		echo "<p><font color='blue' size='5'>EL DOCUMENTO {$codoc} NO EXISTE</font></p>";
		return;
	}
	$row3 = mysql_fetch_array($err);

	$query =  " SELECT Cobnpa, Cobnta
				 FROM ".$wbasedato."_000121
				 WHERE Cobest='on'
				 AND Cobcod='".$cod[0]."'";
	$err1 = mysql_query($query,$conex);
	$numco = mysql_num_rows($err1);


	if ($row3[3]=='off')// muestra el letrero de documento anulado en envios
	{
		echo "<center><h1><b>DOCUMENTO ANULADO</b></h1></center>";
	}

	function stripAccents($string)
	{
		$string = str_replace("ç","c",$string);
		$string = str_replace("Ç","C",$string);
		$string = str_replace("Ý","Y",$string);
		$string = str_replace("ý","y",$string);
		$string = str_replace("?","",$string);
		$string = str_replace("Â","",$string);
		$string = str_replace("Ã","",$string);
		$string = str_replace("?","",$string);
		$string = str_replace("Ãƒâ€˜","Ñ",$string);
		return $string;
	}
	echo "</table>";
	echo "<table align='center' border=0>";
	echo "<tr><td class='encabezadoTabla' colspan=7 align=center><b>DETALLE DE LAS FACTURAS CONSULTADAS</b></td></tr>";

	$clp=2+$numco;///////////////campos obligatorios

	echo "<tr><td class='fila2' colspan=3 align=center><b>DOCUMENTO Nro: ".$row3[2]."</b></td><td class='fila2' colspan=".$clp." align=center><b>FECHA DEL Dcto: ".$row3[1]."</b></td></tr>";
	echo "<tr class='encabezadoTabla'><td><b>Fuente de la Factura</td><td><b>Nro Factura</td><td> Fecha Factura </td>";

	for ($l=1;$l<=$numco;$l++)
			{
			$rowco = mysql_fetch_array($err1);
			$arr[$l]['co']=$rowco[1];

			echo "<input type='hidden' id='arr[".$l."][co]' value='".$arr[$l]['co']."'>";

			echo "<td align=center><b>".$rowco[0]."</td>";
			}

	echo "<td><b>Nombre</td><td><b>Valor Factura</td></tr>";

	//2007-04-26
	$query =  " SELECT Rdefac, Rdevca, a.id, Rdeffa, Rdeest, b.fenfec, b.id idfactura
							FROM ".$wbasedato."_000021 a, ".$wbasedato."_000018 b
							WHERE Rdefue='".$fue[0]."'
							AND Rdenum='".$codoc."'
							AND fenffa = rdeffa
							AND fenfac = rdefac
							ORDER BY fenfec asc, idfactura asc ";
	$err = mysql_query($query,$conex) or die(mysql_error());
	$num = mysql_num_rows($err);
	//echo mysql_errno() ."=". mysql_error();
	echo "<input type='hidden' name='nan' id='nan' value='".$num."'>";

	for ($i=1;$i<=$num;$i++)
	{
		if (is_int ($i/2))
		   $wcf="fila1";  // color de fondo de la fila
		else
		   $wcf="fila2"; // color de fondo de la fila

		$row2 = mysql_fetch_array($err);
		$fan[$i]['nf']=$row2[0];
		$fan[$i]['ffa']=$row2[3];
		echo "<input type='hidden' id='fan[".$i."][nf]' value='".$fan[$i]['nf']."'>";
		echo "<input type='hidden' id='fan[".$i."][ffa]' value='".$fan[$i]['ffa']."'>";


		//2009-11-03
		$query =  "   SELECT Fennpa
						FROM ".$wbasedato."_000018 a, ".$wbasedato."_000021
					   WHERE Rdefac='".$fan[$i]['nf']."'
						 AND Rdefac=Fenfac
						 AND Rdeffa=Fenffa";
		$err1 = mysql_query($query,$conex);
		$row5 = mysql_fetch_array($err1);
		//echo mysql_errno() ."=". mysql_error();
		$nom=$row5[0];

		echo "<tr class=".$wcf."><td>".$row2[3]."</td><td>".$row2[0]."</td><td align='center'>".$row2[5]."</td>";

		//////////////////////////////////////////este es el for para traer los campos obligatorios
		 for ($l=1;$l<=$numco;$l++)
				{

					$query =  " SELECT ".$arr[$l]['co']."
								 FROM ".$wbasedato."_000101, ".$wbasedato."_000018
								 WHERE Fenhis=Inghis
								 AND Fening=Ingnin
								 AND Fenfac='".$fan[$i]['nf']."'
								 AND Fenest='on'";

				   $err2 = mysql_query($query,$conex);
				   $numdo = mysql_num_rows($err2);
					//echo mysql_errno() ."=". mysql_error();

					$rowdo = mysql_fetch_array($err2);
					$arr[$l][$i]=$rowdo[0];

					echo "<td align=center>".$arr[$l][$i]."&nbsp</td>";

				}



		echo "<td>".stripAccents($nom)."&nbsp</td><td align=right>".number_format($row2[1],0,'.',',')."</td></tr>";
	}
	if($row3[3]!='off')
	 $anular="<input type='checkbox' id='anularEnv' name='anularEnv' onclick='anularTodo();'>";
	 else
	  $anular="&nbsp";
	echo "<tr class='encabezadoTabla'><td colspan=3><b>VALOR TOTAL DEL DOCUMENTO</td><td colspan=".$clp." align=right>".number_format($row3[0],0,'.',',')."</td></tr>";
	if($row3[3]!='off')
	echo "<tr class='fila1'><td colspan=3><b>ANULAR DOCUMENTO</td><td colspan=".$clp." align=right>".$anular."</td></tr>";
	echo "</table>";

	echo "<br>";
	$fuentearr = explode("-",$fuente);
	$fuentef = $fuentearr[0];
	$hyper = "<A HREF='/matrix/ips/procesos/Imp_EnRaDe.php?empresa=".$wbasedato."&amp;cc=".$cc[0]."&amp;dcto=".$codoc."&amp;fuente=".$fuente."' target='new' class='vinculo' style='text-decoration:none; font-weight:bold'>Imprimir Documento</a><br><br>";
    $hyper .= "<A HREF='/matrix/ips/reportes/formato_furips.php?wemp_pmla=".$wemp_pmla."&amp;dcto=".$codoc."&amp;fuente=".$fuentef."' target='new' class='vinculo' style='text-decoration:none; font-weight:bold'>Generar Furips</a><br><br>";
	echo "<center><table>";
	echo "<tr><td  colspan= '4' align=center>$hyper</td></tr>";
	echo "</table></center>";
	return;
}

if(isset($ajaxAnulard))//funcion que anula las facturas de un envio o la totalidad del envio
{




	echo "<br>";
	$fue= explode("-",$nfuente);
	//$cod=explode("-",$responsable);
	if($ajaxAnulard=='add')
		$nest='off';
		else
		 $nest='on';

	$query= "UPDATE ".$wbasedato."_000021
							SET Rdeest='".$nest."'
							WHERE Rdefue='".$fue[0]."'
							AND Rdenum='".$codoc."'"; //actualizar solamente la factura
	$err = mysql_query($query,$conex);

	$query= "UPDATE ".$wbasedato."_000020
							SET Renest='".$nest."'
							WHERE Renfue='".$fue[0]."'
							AND Rennum='".$codoc."'";
	$err = mysql_query($query,$conex);

	 $query= "SELECT *
			FROM ".$wbasedato."_000021, ".$wbasedato."_000040
			WHERE Rdefac='".$fac."'
			AND Rdeffa='".$ffa."'
			AND Rdefue=Carfue
			AND Cardev='on'";
	$errdv = mysql_query($query,$conex);
	$numdv = mysql_num_rows($errdv);
	//echo mysql_errno() ."=". mysql_error();

	if ($numdv>0)
		$estfac='DV';
		else
			$estfac='GE';
	if($ajaxAnulard=='rm')
		$estfac='EV';

	$query= "UPDATE ".$wbasedato."_000018
				SET Fenesf='".$estfac."'
			  WHERE Fenfac='".$fac."'
			    AND Fenffa='".$ffa."'";
	$err = mysql_query($query,$conex);

	echo "<div><font size=3 color=blue><b> EL DOCUMENTO No: ".$codoc." HA SIDO ANULADO </b></font></div>";

	return;
}

////********************************************************************FUNCIONES PHP PARA LAS RADICACIONES*********************************************************************************
if(isset($ajaxRadT))//lista las facturas que pueden ser radicadas, se puede partir desde un envío
{




	$res=explode("-",$responsable);
	$row1=explode("-",$fuente);
	function stripAccents($string)
	  {
		$string = str_replace("ç","c",$string);
		$string = str_replace("Ç","C",$string);
		$string = str_replace("Ý","Y",$string);
		$string = str_replace("ý","y",$string);
		$string = str_replace("?","",$string);
		$string = str_replace("Â","",$string);
		$string = str_replace("Ã","",$string);
		$string = str_replace("?","",$string);
		$string = str_replace("Ãƒâ€˜","Ñ",$string);
		return $string;
	  }
	// Obtengo días de holgura para la fuente
	$qholgura =  " SELECT Cardia
				FROM ".$wbasedato."_000040
				WHERE Carrad='on'
				AND Carest='on'";
	$errholgura = mysql_query($qholgura,$conex);
	$rowholgura = mysql_fetch_array($errholgura);
	$holgura = $rowholgura['Cardia'];
	if(!isset($holgura) || !$holgura || $holgura=="")
		$holgura = 0;

	$fecha_actual = date("Y-m-d");


	if($ajaxRadT=='env')
	{
		$query =  " SELECT *
						FROM ".$wbasedato."_000020
					   WHERE Renfue = '".$row1[0]."'
					     AND Rennum = '".$nuenv."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		( $num > 0 ) ? $existeElDocumento = true : $existeElDocumento = false;

		$query =  " SELECT Fenfac, Fenval, Fensal, Fenffa, Renfec, Rencod
						FROM ".$wbasedato."_000018, ".$wbasedato."_000021, ".$wbasedato."_000020
						WHERE fenesf = 'EV'
						AND Fenest = 'on'
						AND Fenfac = Rdefac
						AND Fenffa = Rdeffa
						AND Rdeest='on'
						AND Rdefue=Renfue
						AND Rdenum=Rennum
						AND Rdenum = '".$nuenv."'
						AND Rdefue = '".$row1[0]."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
	}else{
			// Se cambia fencod por fenres
			$query =  " SELECT  Fenfac, Fenval, Fensal, Fenffa, Renfec
				FROM ".$wbasedato."_000018, ".$wbasedato."_000021, ".$wbasedato."_000020
				WHERE Fenres='".$res[0]."'
				AND Renfec between '".$fec1."' and '".$fec2."'
				AND fenesf='EV'
				AND Fenest='on'
				AND Fenfac=Rdefac
				AND Rdeest='on'
				AND Rdefue='".$row1[0]."'
				AND Rdefue=Renfue
				AND Rdenum=Rennum
				AND Fenffa = Rdeffa
				ORDER BY Fenfac";
			$err = mysql_query($query,$conex);
			//echo mysql_errno() ."=". mysql_error();
			$num = mysql_num_rows($err);
			//echo "<br>2. ".$query;
		 }
	if($num==0)
	{
		if( $existeElDocumento ){
			echo "<p><font color='blue' size='5'>EL DOCUMENTO {$codoc} TIENE MOVIMIENTOS POSTERIORES </font></p>";
		}else{
			echo "<p><font color='blue' size='5'>EL DOCUMENTO {$codoc} NO EXISTE</font></p>";
		}
		return;
	}

	echo "<input type='hidden' name='wholgura' value='".$holgura."'>";
	echo "<input type='hidden' name='fecha_actual' value='".$fecha_actual."'>";
	echo "<input type='hidden' id='facpantalla' value='".$num."'>";
	echo "<br>";
	echo "</table>";
	echo "<table align='center' border=0>";
	echo "<tr><td class='encabezadoTabla' colspan=9 align=center><b>DETALLE DE LAS FACTURAS QUE VAN A SER RADICADAS</b></td></tr>";
	echo "<tr><td class='fondoBlanco' colspan=9 align='left'>&nbsp;<input type='checkbox' id='todas' name='todas' onclick='cambiarTodas(\"$fuenteRad\");'>Seleccionar todas</td></tr>";
	echo "<tr class='encabezadoTabla'><td align='center'><b>Radicar</td><td align='center'><b>Fuente de la Factura</td><td align='center'><b>Nro Factura</td><td align='center'><b>Nombre</td><td align='center'><b>Valor Factura</td></tr>";

	for ($i=1;$i<=$num;$i++)
	{
		$row = mysql_fetch_array($err);
		if($ajaxRadT=='env')//si se consultó un envio entonces busco el responsable de dicho envio.
			{
				$qresp=" SELECT Empcod"
					   ."  FROM ".$wbasedato."_000024 "
					   ." WHERE empcod='".$row[5]."'";
				$rsresp=mysql_query($qresp,$conex);
				$rowresp=mysql_fetch_row($rsresp);
				echo "<input type='hidden' id='responsableEnvio' value='".$rowresp[0]."'>";
			}
		$arr[$i]['nf']=$row[0];
		$arr[$i]['vf']=$row[2];
		$arr[$i]['ffa']=$row[3];
		$arr[$i]['fec']=$row[4];

		echo "<input type='hidden' name='arr[".$i."][nf]' id='arr[".$i."][nf]' value='".$arr[$i]['nf']."'>";
		echo "<input type='hidden' name='arr[".$i."][vf]' id='arr[".$i."][vf]' value='".$arr[$i]['vf']."'>";
		echo "<input type='hidden' name='arr[".$i."][ffa]' id='arr[".$i."][ffa]' value='".$arr[$i]['ffa']."'>";
		if($i==1)
			echo "<input type='hidden' name='fecha_envio' value='".$arr[$i]['fec']."'>";

		//2009-11-03
		$query =  " SELECT Fennpa
					  FROM ".$wbasedato."_000018, ".$wbasedato."_000021
					  WHERE Rdefac='".$arr[$i]['nf']."'
					  AND Rdefac=Fenfac
					  AND Rdeffa=Fenffa";
		$err1 = mysql_query($query,$conex);
		$row5 = mysql_fetch_array($err1);
		$nom=$row5[0];

		if (is_int ($i/2))
		   $wcf="fila1";  // color de fondo de la fila
		else
		   $wcf="fila2"; // color de fondo de la fila

		echo "<tr class=".$wcf."><td align='center'><input type='checkbox' name='wgrabar[".$i."]' id='wgrabar[".$i."]' onclick='cambiarEstadoRad(".$i.");'></td><td>".$arr[$i]['ffa']."</td><td>".$arr[$i]['nf']."</td><td>".stripAccents($nom)."</td><td align=right id='st".$i."'>".number_format($arr[$i]['vf'],0,'.',',')."</td>";

	}

	$total=0;

	echo "<tr class='encabezadoTabla' align=center ><td colspan=3><b>VALOR TOTAL DEL DOCUMENTO</td><td colspan=2 align=right id='total'>".number_format($total,0,'.',',')."</td></tr>";
	echo "<tr align=center ><td colspan=9>&nbsp</td></tr>";
	echo "<tr class='fila1' align=center ><td colspan=9><b>OBSERVACION GENERAL DEL DOCUMENTO</td></tr>";
   if (!isset($obser))
   {
		$obser='';
   }
			echo "<tr align=center><td colspan=9 ><TEXTAREA NAME='obser' id='obser' COLS=100 ROWS=2 align=center>".$obser."</TEXTAREA></td></tr>";
	echo "<input type='hidden' name='total' value='".$total."'>";

   echo "</table></center>";

    echo "<div align='center'><input type='button' id='grabarRadicacion' value='GRABAR' onclick='grabarRad();'/></div>";

	$numero=count($arr);
	echo "<input type='hidden' name='numero' value='".$numero."'>";
	return;
}

if(isset($ajaxGrabRad))//Realiza en las tablas los movimientos necesarios para generar la radicación
{





	//echo 'graba RADICACION';
	//variables necesarias;
	if(!isset($respo))
	{
		$respo=" - - ";
		$cod=explode('-',$respo);
	}else
		{
			$qresp=" SELECT Empcod, Empnit, Empnom"
		           ."  FROM ".$wbasedato."_000024 "
		           ." WHERE empcod='".$respo."'";
     		$rsresp=mysql_query($qresp,$conex);
	    	$cod=mysql_fetch_row($rsresp);
		}
	$fecha=date("Y-m-d");
	$hora=date("H:i:s");
	$fue=explode('-',$fuente);
	//$cod=explode('-',$respo);
	$cc=explode('-',$suc);
	$cj=explode('-',$caja);
	$usua=explode('-', $user);
	// Si no viene definida la fecha desde el formulario asigno la fecha actual
	//echo "<br>Fecha".$wfecha;
	if(!isset($wfecha) || !$wfecha || $wfecha=="" || $wfecha=="0")
		$wfecha = $fecha;

	$responsable=$respo;
	$responsa=explode('-',$responsable);//////explode para ingresar el nombre del responsable unicamente

	if($ingreso==1)// si es la primera vez que ingreso.
	{
	    ////////////////////////SE BLOQUEA LA TABLA DE LAS FUENTES
		 $q = "lock table ".$wbasedato."_000040 LOW_PRIORITY WRITE";
		 $errlock = mysql_query($q,$conex);

		 $query =  " SELECT Carcon
					FROM ".$wbasedato."_000040
					WHERE Carfue='".$fue[0]."'
					AND Carest='on'";
		$err = mysql_query($query,$conex);
		$num = mysql_fetch_array($err);


		 /////////////////Aca actualizo el consecutivo de la fuente
		$q= "   UPDATE ".$wbasedato."_000040 "
		."      SET carcon = carcon + 1 "
		."      WHERE carfue ='".$fue[0]."'"
		."      AND carest = 'on' ";
		/*if( $usua[1]=="0207013" )
					echo $q."<br>";*/
		$res1 = mysql_query($q,$conex);

		/////////////////////////////////SE DESBLOQUEA LA TABLA DE FUENTES
		$q = " UNLOCK TABLES";
		$errunlock = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

		/////////////////////////////////CREAR EL ENCABEZADO DEL DOCUMENTO.

		// Obtengo días de holgura para la fuente
		$qholgura =  " SELECT Cardia
					FROM ".$wbasedato."_000040
					WHERE Carfue='".$fue[0]."'
					AND Carest='on'";
		$errholgura = mysql_query($qholgura,$conex);
		$rowholgura = mysql_fetch_array($errholgura);
		$holgura = $rowholgura['Cardia'];
		//echo "<br>Holgura".$holgura;
		if(!isset($holgura) || !$holgura || $holgura=="")
			$holgura = 0;

		$query="INSERT INTO  ".$wbasedato."_000020
				(Medico,
				Fecha_data,
				Hora_data,
				Renfue,
				Rennum,
				Renvca,
				Rencod,
				Rennom,
				Rencaj,
				Renusu,
				Rencco,
				Renest,
				Renfec,
				Rendia,
				Renobs,
				Seguridad)
		VALUES ('".$wbasedato."',
				'".$fecha."',
				'".$hora."',
				'".$fue[0]."',
				'".$doc."',
				'".$total."',
				'".$cod[0]."',
				'".$cod[2]."',
				'".$cj[0]."',
				'".$usua[1]."',
				'".$cc[0]."',
				'on',
				'".$wfecha."',
				".$holgura.",
				'".$obser."',
				'A-".$usua[1]."')";

		$err = mysql_query($query,$conex);
	}

	//SE CREAN LOS REGISTROS EN LA 21 Y ADEMÁS SE ACTUALIZA EL ESTADO EN LA 18

	$query= "UPDATE ".$wbasedato."_000018
				SET Fenesf='RD'
				WHERE Fenfac='".$numFac."'";
	$err = mysql_query($query,$conex);
	$query="INSERT INTO  ".$wbasedato."_000021
				(Medico,
				Fecha_data,
				Hora_data,
				Rdefue,
				Rdenum,
				Rdecco,
				Rdefac,
				Rdevta,
				Rdevca,
				Rdeest,
				Rdecon,
				Rdevco,
				Rdeffa,
				Seguridad)
		VALUES ('".$wbasedato."',
				'".$fecha."',
				'".$hora."',
				'".$fue[0]."',
				'".$doc."',
				'".$cc[0]."',
				'".$numFac."',
				'',
				'".$valFac."',
				'on',
				'',
				'0',
				'".$fueFac."',
				'A-".$usua[1]."')";

	$err = mysql_query($query,$conex);
	$hyper="<A HREF='/matrix/ips/procesos/Imp_EnRaDe.php?empresa=".$wbasedato."&amp;cc=".$cc[0]."&amp;dcto=".$doc."&amp;fuente=".$fuente."' target='new' class='vinculo' style='text-decoration:none; font-weight:bold'>Imprimir Documento</a><br><br>";
	echo "<center><table>";
	echo "<tr><td colspan = '4'><font size='3' color='blue'> EL DOCUMENTO HA SIDO RADICADO</font></td></tr>";
	echo "<tr><td  colspan= '4' align=center>$hyper</td></tr>";
	echo "</table></center>";
	return;
}

if(isset($ajaxcdRad))//Consulta un número de radicación.
{




	$fue=explode('-',$fuente);
	$cod=explode('-',$responsable);
	$cc=explode('-',$suc);

	$query = "SELECT Renvca, Renfec, Rennum, Renest, fecha_data, hora_data
				FROM ".$wbasedato."_000020
				WHERE Renfue='".$fue[0]."'
				  AND Rennum='".$codoc."'
				  AND Rencod='".$cod[0]."'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	$row3 = mysql_fetch_array($err);
	if($num==0)
	{
		echo "<p><font color='blue' size='5'>EL DOCUMENTO {$codoc} NO EXISTE</font></p>";
		return;
	}
	if ($row3[3]=='off')// muestra el letrero de documento anulado en envios
	{
		echo "<center><h1><b>DOCUMENTO ANULADO</b></h1></center>";
	}
	echo "<input type='hidden' id='fechaDoc' value='".$row3[4]."'>";
	echo "<input type='hidden' id='horaDoc' value='".$row3[5]."'>";
	echo "<table align='center' border=0>";
	echo "<tr><td class='encabezadoTabla' colspan=7 align=center><b>DETALLE DE LAS FACTURAS CONSULTADAS</b></td></tr>";
	echo "<tr><td class='fila2' colspan=3 align=center><b>DOCUMENTO Nro: ".$row3[2]."</b></td><td class='fila2' colspan=3 align=center><b>FECHA DEL Dcto: ".$row3[1]."</b></td></tr>";
	//echo "<tr class='encabezadoTabla'><td><b>Anular</td><td><b>Fuente de la Factura</td><td><b>Nro Factura</td><td><b>Nombre</td><td><b>Valor Factura</td></tr>";
	echo "<tr class='encabezadoTabla'><td><b>Fuente de la Factura</td><td><b>Nro Factura</td><td><b>Nombre</td><td><b>Valor Factura</td></tr>";
	$query =  " SELECT Rdefac, Rdevca, a.id, Rdeffa, b.fenfec, b.id idfactura
							FROM ".$wbasedato."_000021 a, ".$wbasedato."_000018 b
							WHERE Rdefue='".$fue[0]."'
							AND Rdenum='".$codoc."'
							AND fenffa = rdeffa
							AND fenfac = rdefac
							ORDER BY fenfec asc, idfactura asc";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);

	echo "<input type='hidden' id='nan' value='".$num."'>";
	echo "<input type='hidden' id='checkadas' value=0>";
	$totan=0;
	for ($i=1;$i<=$num;$i++)
	{
		if (is_int ($i/2))
		   $wcf="fila1";  // color de fondo de la fila
		else
		   $wcf="fila2"; // color de fondo de la fila

		$row2 = mysql_fetch_array($err);
		$fan[$i]['nf']=$row2[0];
		$fan[$i]['ffa']=$row2[3];
		echo "<input type='hidden' id='fan[".$i."][nf]' value='".$fan[$i]['nf']."'>";
		echo "<input type='hidden' id='fan[".$i."][ffa]' value='".$fan[$i]['ffa']."'>";


		//2009-11-03
		$query =  " SELECT Fennpa
					  FROM ".$wbasedato."_000018, ".$wbasedato."_000021
					  WHERE Rdefac='".$fan[$i]['nf']."'
					  AND Rdefac=Fenfac
					  AND Rdeffa=Fenffa";
		$err1 = mysql_query($query,$conex);
		$row5 = mysql_fetch_array($err1);

		$nom=$row5[0];

		$query=" SELECT Fenfac, Fensal
							FROM ".$wbasedato."_000018
							WHERE Fenfac='".$fan[$i]['nf']."'
							AND Fenffa='".$fan[$i]['ffa']."'
							AND Fenest='on'";
		$err1 = mysql_query($query,$conex);

		$numfac = mysql_num_rows($err1);
		$rowfac = mysql_fetch_array($err1);
		$fa[$i]['sf']=$rowfac[1];
		echo "<input type='hidden' id='fa[".$i."][sf]' value='".$fa[$i]['sf']."'>";

		if ($rowfac[0]==$row2[0])
		$vbl="<input type='checkbox' id='anular[".$i."]' onclick='anularRad(".$i.");'>";
		else
		$vbl="&nbsp";

		//echo "<tr class=".$wcf."><td>$vbl</td><td>".$row2[3]." </td><td>".$row2[0]." </td><td>".$nom."</td><td align=right>".number_format($row2[1],0,'.',',')."</td></tr>";
		echo "<tr class=".$wcf."><td>".$row2[3]."</td><td>".$row2[0]." </td><td>".$nom."</td><td align=right>".number_format($row2[1],0,'.',',')."</td></tr>";
		if (isset($anular[$i]))
		{
			$totan=$totan+$fa[$i]['sf'];
		}

	}
	if($row3[3]!='off')
	 $anular="<input type='checkbox' id='anularad' onclick='guardarAnulacionRad();'>";
	 else
	  $anular="&nbsp";
	echo "<tr class='encabezadoTabla'><td colspan=2><b>VALOR TOTAL DEL DOCUMENTO</td><td colspan=3 id='totaldoc' align=right>".number_format($row3[0],0,'.',',')."</td></tr>";
	echo "<input type='hidden' id='totdc' value='".$row3[0]."'>";
	//echo "<tr class='encabezadoTabla'><td colspan=2><b>VALOR TOTAL DE LA ANULACION</td><td colspan=3 id='totalAnulado' align=right>".number_format($totan,0,'.',',')."</td></tr>";
	echo "<input type='hidden' id='totan' value='".$totan."'>";
	if($row3[3]!='off')
	echo "<tr class='encabezadoTabla'><td colspan=2><b>ANULAR  TODO EL DOCUMENTO</td><td colspan=3 align=right>".$anular."</td></tr>";

	echo "</table>";

	echo "<br>";
	$hyper="<A HREF='/matrix/ips/procesos/Imp_EnRaDe.php?empresa=".$wbasedato."&amp;cc=".$cc[0]."&amp;dcto=".$codoc."&amp;fuente=".$fuente."' target='new' class='vinculo' style='text-decoration:none; font-weight:bold'>Imprimir Documento</a><br><br>";
	echo "<center><table>";
	echo "<tr><td  colspan= '4' align=center>$hyper</td></tr>";
	echo "<tr><td></td></tr>";
//	echo "<tr id='guardarAnulacion' style='display:none;'><td align='center'><input type='button' id='guarAnulRad' value='Anular' onclick='guardarAnulacionRad();'></td></tr>";
	echo "</table></center>";
	return;
}

if(isset($ajaxAnuRad))//realiza los movimientos necesarios en las tablas para anular una radicacion
{




  $cc=explode('-',$suc);
  $fue=explode('-',$fuente);
  $respo=explode('-', $responsable);
  $usua=explode('-', $user);
  if (($anularad=='si') && ($ingreso==1))
  {
	$fue=explode('-',$fuente);

	$query= "UPDATE ".$wbasedato."_000021
							SET Rdeest='off'
							WHERE Rdefue='".$fue[0]."'
							AND Rdenum='".$codoc."'";
	$err = mysql_query($query,$conex);

	$query= "UPDATE ".$wbasedato."_000020
							SET Renest='off'
							WHERE Renfue='".$fue[0]."'
							AND Rennum='".$codoc."'";
	$err = mysql_query($query,$conex);

   }

	$query= "UPDATE ".$wbasedato."_000018
				SET Fenesf='EV'
			  WHERE Fenfac='".$nfac."'
				AND Fenffa='".$ffa."'
				AND Fennit='".trim($respo[1])."'";

	$err = mysql_query($query,$conex);
	echo "<center><div><font size=3 color=blue><b> EL DOCUMENTO No: ".$codoc." HA SIDO ANULADO </b></font></div></center>";
	return;
}

if(isset($ajaxMovimientosPost))//valida si se le hizo algún movimiento posterior a alguna de las facturas, si esto sucede no se debe permitir anular la radicación.
{




  $cc=explode('-',$suc);
  $fue=explode('-',$fuente);
  $respo=explode('-', $responsable);


  $query= "SELECT COUNT(*)"
		  ." FROM ".$wbasedato."_000021 a, ".$wbasedato."_000020 b "
		  ."WHERE Rdefac='".$nfa."' "
		   ." AND Rdeffa='".$ffa."' "
		   ." AND b.fecha_data >='".$fecha_data."'"
		   ." AND b.hora_data >='".$hora_data."'"
		   ." AND Rdefue!= '".$fue[0]."'"
		   ." AND Rdenum != '".$codoc."'"
		   ." AND Rdefue=Renfue "
		   ." AND Rdenum=Rennum "
		   ." AND Rencod='".$respo[0]."' "
		   ." AND Renest='on'";
	$rs= mysql_query($query, $conex);
	$row=mysql_fetch_array($rs);
	if($row[0]>0)
		echo 'si';
	return;
}

/////*****************************************************************FUNCIONES PHP PARA LAS DEVOLUCIONES************************************************************************************

if(isset($ajaxDevT))////lista las facturas que pueden ser devueltas, se puede partir desde una radicación o por rango de fechas.
{




	if(isset($responsable) && $responsable!="")
	{
		$res=explode("-",$responsable);
		$emp=$res[0]."-".$res[2];
	}
	else
	{
		$responsable = " - - ";
		$res=explode("-",$responsable);
		$emp=$res[0]."-".$res[2];
	}
	function stripAccents($string)
	  {
		$string = str_replace("ç","c",$string);
		$string = str_replace("Ç","C",$string);
		$string = str_replace("Ý","Y",$string);
		$string = str_replace("ý","y",$string);
		$string = str_replace("?","",$string);
		$string = str_replace("Â","",$string);
		$string = str_replace("Ã","",$string);
		$string = str_replace("?","",$string);
		$string = str_replace("Ãƒâ€˜","Ñ",$string);
		return $string;
	  }
	$fue=explode('-',$frad);
	if($ajaxDevT=='rad')
	{
		$query =  " SELECT Fenfac, Fenval, Fensal, Fenffa
				FROM ".$wbasedato."_000018, ".$wbasedato."_000021
				WHERE Fenesf='RD'
				AND Fenest='on'
				AND Fenfac=Rdefac
				AND Fenffa = Rdeffa
				AND Rdeest='on'
				AND Rdenum='".$nurad."'
				AND Rdefue='".$fue[0]."'";
		$err = mysql_query($query,$conex);
		//echo mysql_errno() ."=". mysql_error();
		$num = mysql_num_rows($err);
	}else
		{
		// se cambia fencod por fenres
			$query =  " SELECT  Fenfac, Fenval, Fensal, Fenffa
				FROM ".$wbasedato."_000018, ".$wbasedato."_000021, ".$wbasedato."_000020
				WHERE Fenres='".trim($res[0])."'
				AND Renfec between '".$fec1."' and '".$fec2."'
				AND fenesf='RD'
				AND Fenest='on'
				AND Fenfac=Rdefac
				AND Rdeest='on'
				AND Rdefue='".$fue[0]."'
				AND Rdefue=Renfue
				AND Rdenum=Rennum
				AND Fenffa = Rdeffa
			  GROUP by 1,2,3,4
			  ORDER BY Fenfac";
			$err = mysql_query($query,$conex);
			//echo mysql_errno() ."=". mysql_error();
			$num = mysql_num_rows($err);

		}
	echo "<input type='hidden' id='facpantalla' value='".$num."'>";
	echo "<br>";
	echo "<table align='center' border=0>";
	echo "<tr><td class='encabezadoTabla' colspan=9 align=center><b>DETALLE DE LAS FACTURAS QUE VAN A SER DEVUELTAS</b></td></tr>";
	echo "<tr><td class='fondoBlanco' colspan=9 align='left'>&nbsp;<input type='checkbox' id='todas' onclick='cambiarTodas(\"$fdev\")'>Seleccionar todas</td></tr>";
	echo "<tr class='encabezadoTabla'><td><b>Devolver</td><td><b>Fuente de la Factura</td><td><b>Nro Factura</td><td><b>Nombre</td><td><b>Valor Factura</td></tr>";

	for ($i=1;$i<=$num;$i++)
	{
		$row = mysql_fetch_array($err);

		$arr[$i]['nf']=$row[0];
		$arr[$i]['vf']=$row[2];
		$arr[$i]['ffa']=$row[3];

		echo "<input type='hidden' id='arr[".$i."][nf]' value='".$arr[$i]['nf']."'>";
		echo "<input type='hidden' id='arr[".$i."][vf]' value='".$arr[$i]['vf']."'>";
		echo "<input type='hidden' id='arr[".$i."][ffa]' value='".$arr[$i]['ffa']."'>";

		//2009-11-03
		$query =  " SELECT Fennpa
					  FROM ".$wbasedato."_000018, ".$wbasedato."_000021
					  WHERE Rdefac='".$arr[$i]['nf']."'
					  AND Rdefac=Fenfac
					  AND Rdeffa=Fenffa";
		$err1 = mysql_query($query,$conex);
		$row5 = mysql_fetch_array($err1);
		//echo mysql_errno() ."=". mysql_error();
		$nom=$row5[0];

		if (is_int ($i/2))
		   $wcf="fila1";  // color de fondo de la fila
		else
		   $wcf="fila2"; // color de fondo de la fila

	   echo "<tr class=".$wcf."><td align='center'><input type='checkbox' id='wgrabar[".$i."]' onclick='cambiarEstadoDev(".$i.");'></td><td align='center'>".$arr[$i]['ffa']."</td><td>".$arr[$i]['nf']."</td><td>".stripAccents($nom)."</td><td align=right id='st".$i."'>".number_format($arr[$i]['vf'],0,'.',',')."</td>";
	}

	$total=0;
	// para el proceso de causas
	echo "<input type='HIDDEN' id='ingCau' value='0'>";
	echo "<input type='HIDDEN' id='eliCau' value='0'>";
	echo "<input type='HIDDEN' id='index' value='0'>";

	echo "<tr class='encabezadoTabla' align=center ><td colspan=3><b>VALOR TOTAL DEL DOCUMENTO</td><td colspan=2 id='total' align=right>".number_format($total,0,'.',',')."</td></tr>";
	echo "<tr align=center ><td colspan=9>&nbsp</td></tr>";
	echo "<tr class='fila1' align=center ><td colspan=9><b>CAUSAS DE DEVOLUCION DEL DOCUMENTO</td></tr>";

	////////////////////////////////////////////////////////////para las causas de devolucion

	$q="SELECT caucod, caunom
		  FROM ".$wbasedato."_000072
		 WHERE cauest='on' and Caudev='on' ";

	$res1 = mysql_query($q,$conex);
	$num1 = mysql_num_rows($res1);


	echo "<tr>";
		echo "<td colspan=9 align=center><select id='cauSel' onchange='agregarCausa(\"cauSel\",\"add\",\"\");'>";
				echo "<option value='' selected>--</option>";
	for ($i=1;$i<=$num1;$i++)
	{
		$row1 = mysql_fetch_array($res1);
		echo "<option value='".$row1[0]."'>".$row1[0]." - ".$row1[1]."</option>";
	}

	echo "</select></td></tr>";

	echo "<input type='hidden' id='lista_causas' value=''/>";

	echo "<tr><td colspan=9 class='fila2'><div id='div_causas' width='600px; text-align:left;' align='center'></div></td></tr>";
	echo "<tr class='fila1' align=center ><td colspan=9><b>OBSERVACION GENERAL DEL DOCUMENTO</td></tr>";

	if (!isset($obser)){
		$obser='';
		}
			echo "<tr align=center><td colspan=9 ><TEXTAREA  id='obser' COLS=100 ROWS=2 align=center>".$obser."</TEXTAREA></td></tr>";
	echo "</table></center>";
	echo "<input type='hidden' name='total' value='".$total."'>";
	echo "<div align='center'><input type='button' id='grabarRadicacion' value='GRABAR' onclick='grabarDevolucion();'/></div>";
	return;
}

if(isset($accion))//// funcion que modifica en pantalla las causas, agrega al listado y elimina del select o agrega al select y elimina del listado
{




	//$data=array('opciones'=>'', 'causa'=>'', 'lista'=>'');
	$causa='';
	switch($accion)
	{
	 case 'add':
			$listado = '';
			if($lista_causas!=''){
				$listado=explode(",",$lista_causas);
				$listado=implode("','",$listado);
				$lista_causas .= ','.$seleccionado;
			}
			else
			{
				$lista_causas = $seleccionado;
			}
			//$listado[]=$seleccionado;

			$q="SELECT caucod, caunom
				  FROM ".$wbasedato."_000072
			     WHERE cauest='on' and Caudev='on'
				   AND caucod not in('".$listado."')";
			$res1 = mysql_query($q,$conex);
			$num1 = mysql_num_rows($res1);
			$opcion = "<option value='' selected>--</option>";
			for ($i=1;$i<=$num1;$i++)
			{
				$row1 = mysql_fetch_array($res1);
				if((String)$row1[0]!==(String)$seleccionado)
				  $opcion .= "<option value='".$row1[0]."'>".$row1[0]." - ".$row1[1]."</option>";
					else
					$causa="<div id='div_".$row1[0]."' align='left'><li><span style='color:blue; cursor:pointer;' onclick='agregarCausa(\"cauSel\",\"rm\",\"".$row1[0]."\");'>ELIMINAR</span>   ".$row1[0]." - ".$row1[1]."</li></div>";
			}
			break;
	  case 'rm':
		$listado=explode(",",$lista_causas);
		$encontrado=array_search($seleccionado, $listado);//buscamos si existe
		if($encontrado!==false)
		{
			unset($listado[$encontrado]);
			$lista_causas=implode(",",$listado);
		}
		$listado=implode("','", $listado);
		$q="SELECT caucod, caunom
				  FROM ".$wbasedato."_000072
			     WHERE cauest='on' and Caudev='on'
				   AND caucod not in('".$listado."')";
			$res1 = mysql_query($q,$conex);
			$num1 = mysql_num_rows($res1);
			$opcion = "<option value='' selected>--</option>";
			while($row1 = mysql_fetch_array($res1))
			{
				  $opcion .= "<option value='".$row1[0]."'>".$row1[0]." - ".$row1[1]."</option>";
			}
			break;

    }
	$data=array('opciones'=>$opcion, 'causa'=>$causa, 'lista'=>$lista_causas);
	echo json_encode($data);
	return;
}

if(isset($ajaxGrabar))//// funcion que realiza los movimientos necesarios en las tablas para grabar una devolucion
{




	$fecha=date("Y-m-d");
	$hora=date("H:i:s");
	$fue=explode('-',$fuente);
	$cod=explode('-',$respo);
	$cc=explode('-',$suc);
	$cj=explode('-',$caja);
	$usua=explode('-', $user);


	// Si no viene definida la fecha desde el formulario asigno la fecha actual
	//echo "<br>Fecha".$wfecha;
	if(!isset($wfecha) || !$wfecha || $wfecha=="" || $wfecha=="0")
		$wfecha = $fecha;

	if($ingreso=="1")
	{
	 ////////////////////////////////SE BLOQUEA LA TABLA DE LAS FUENTES
	 $q = "lock table ".$wbasedato."_000040 LOW_PRIORITY WRITE";
	 $errlock = mysql_query($q,$conex);

	 $query =  " SELECT Carcon
				FROM ".$wbasedato."_000040
				WHERE Carfue='".$fue[0]."'
				AND Carest='on'";
	 $err = mysql_query($query,$conex);
	 $num = mysql_fetch_array($err);

	 if ($num[0]=='')
	  $dcto=1;

	 /////////////////Aca actualizo el consecutivo de la fuente
	 $q= " UPDATE ".$wbasedato."_000040 "
	 ."       SET carcon = carcon + 1 "
	 ."     WHERE carfue ='".$fue[0]."'"
	 ."       AND carest = 'on' ";
	 $res1 = mysql_query($q,$conex);
	 /////////////////////////////////SE DESBLOQUEA LA TABLA DE FUENTES
	 $q = " UNLOCK TABLES";
	 $errunlock = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());

	 $causas=explode(",",$lista_causas);
	 for ($j=0;$j<sizeof($causas);$j++)
		{
			$q= " INSERT INTO ".$wbasedato."_000071
						(Medico,
						Fecha_data,
						Hora_data,
						docfue,
						docnum,
						doccau,
						docest,
						Seguridad)
				VALUES ('".$wbasedato."',
						'".$fecha."',
						'".$hora."' ,
						'".$fue[0]."',
						".strtoupper($dcto).",
						'".$causas[$j]."',
						'on',
	 					'C-".$usua[1]."')";
			//echo "<br>causa ".$q;
	 		$err = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());
	 	}

	 if(!isset($total) || ($total==''))
		$total=0;
	 //Acá se hace la grabación de la factura

	 $responsable=$respo;
	 $responsa=explode('-',$responsable);//////explode para ingresar el nombre del responsable unicamente

	 // Obtengo días de holgura para la fuente
	 $qholgura =  " SELECT Cardia
					  FROM ".$wbasedato."_000040
					 WHERE Carfue='".$fue[0]."'
					   AND Carest='on'";
	 $errholgura = mysql_query($qholgura,$conex);
	 $rowholgura = mysql_fetch_array($errholgura);
	 $holgura = $rowholgura['Cardia'];
	 //echo "<br>Holgura".$holgura;
	 if(!isset($holgura) || !$holgura || $holgura=="")
		$holgura = 0;

	 $query="INSERT INTO  ".$wbasedato."_000020
				(Medico,
				Fecha_data,
				Hora_data,
				Renfue,
				Rennum,
				Renvca,
				Rencod,
				Rennom,
				Rencaj,
				Renusu,
				Rencco,
				Renest,
				Renfec,
				Rendia,
				Renobs,
				Seguridad)
		VALUES ('".$wbasedato."',
				'".$fecha."',
				'".$hora."',
				'".$fue[0]."',
				'".$dcto."',
				'".$total."',
				'".trim($cod[0])."',
				'".trim($responsa[2])."',
				'".$cj[0]."',
				'".$usua[1]."',
				'".$cc[0]."',
				'on',
				'".$wfecha."',
				'".$holgura."',
				'".$obser."',
				'A-".$usua[1]."')";
	 $err = mysql_query($query,$conex);
	// echo "<br> actualizacion en la 20".$query;
	}
	$query= "UPDATE ".$wbasedato."_000018
				SET Fenesf='DV'
		      WHERE Fenfac='".$nfac."'
			    AND Fenffa='".$nffa."'";
	//echo "<br> actualizacion en la 18".$query;
	$err = mysql_query($query,$conex);

	$query="INSERT INTO  ".$wbasedato."_000021
				(Medico,
				Fecha_data,
				Hora_data,
				Rdefue,
				Rdenum,
				Rdecco,
				Rdefac,
				Rdevta,
				Rdevca,
				Rdeest,
				Rdecon,
				Rdevco,
				Rdeffa,
				Seguridad)
		VALUES ('".$wbasedato."',
				'".$fecha."',
				'".$hora."',
				'".$fue[0]."',
				'".$dcto."',
				'".$cc[0]."',
				'".$nfac."',
				'',
				'".$vfac."',
				'on',
				'',
				'0',
				'".$nffa."',
				'A-".$usua[1]."')";
	//echo "<br> actualizacion en la 21".$query;
	$err = mysql_query($query,$conex);// $arr[$i]['vf' ojo;
	$hyper="<A HREF='/matrix/ips/procesos/Imp_EnRaDe.php?empresa=".$wbasedato."&amp;cc=".$cc[0]."&amp;dcto=".$dcto."&amp;fuente=".$fuente."' target='new' class='vinculo' style='text-decoration:none; font-weight:bold'>Imprimir Documento</a><br><br>";
	$notificacion = "<center><table><tr><td colspan='4'>LA DEVOLUCIÓN SE HA REALIZADO SATISFACTORIAMENTE</td></tr>";
	$notificacion .= "<tr><td  colspan= '4' align=center>".$hyper."</td></tr></table></center>";
	$datos=array('respuesta'=>utf8_encode($notificacion));
	echo json_encode($datos);
	return;
}

if(isset($ajaxcdDev)) // funcion que permite consultar una devolucion apartir de su número
{




	$fue=explode('-',$fuente);
	$cod=explode('-',$responsable);
	$cc=explode('-',$suc);

	$query =  " SELECT Renvca, Renfec, Rennum, Renest,Hora_data
				  FROM ".$wbasedato."_000020
				 WHERE Renfue='".$fue[0]."'
				   AND Rennum='".$codoc."'
				   AND Rencod='".$cod[0]."'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	$row3 = mysql_fetch_array($err);
	if($num==0)
	{
		echo "<p><font color='blue' size='5'>EL DOCUMENTO {$codoc} NO EXISTE</font></p>";
		return;
	}
	if ($row3[3]=='off')// muestra el letrero de documento anulado en envios
	{
		echo "<center><h1><b>DOCUMENTO ANULADO</b></h1></center>";
	}

	echo "<br>";
	echo "<table align='center' border=0>";
	echo "<tr><td class='encabezadoTabla' colspan=6 align=center><b>DETALLE DE LAS FACTURAS CONSULTADAS</b></td></tr>";
	echo "<tr><td class='fila2' colspan=2 align=center><b>DOCUMENTO Nro: ".$row3[2]."</b></td><td class='fila2' colspan=3 align=center><b>FECHA DEL Dcto: ".$row3[1]."</b></td></tr>";
	//echo "<tr class='encabezadoTabla'><td><b>Anular</td><td><b>Fuente de la Factura</td><td><b>Nro Factura</td><td><b>Nombre</td><td><b>Valor Factura</td></tr>";
	echo "<tr class='encabezadoTabla'><td><b>Fuente de la Factura</td><td><b>Nro Factura</td><td><b>Nombre</td><td><b>Valor Factura</td></tr>";
	echo "<input type='hidden' id='fechaDoc' value='".$row3[1]."'>";
	echo "<input type='hidden' id='horaDoc' value='".$row3[4]."'>";
	$query =  " SELECT Rdefac, Rdevca, id, Rdeffa
				  FROM ".$wbasedato."_000021
				 WHERE Rdefue='".$fue[0]."'
				   AND Rdenum='".$codoc."'
				 ORDER BY id";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	echo "<input type='hidden' id='nan' value='".$num."'>";
	echo "<input type='hidden' id='checkadas' value=0>";
	$totan=0;
	for ($i=1;$i<=$num;$i++)
	{
		if (is_int ($i/2))
		   $wcf="fila1";  // color de fondo de la fila
		else
		   $wcf="fila2"; // color de fondo de la fila
		$row2 = mysql_fetch_array($err);
		$fan[$i]['nf']=$row2[0];
		$fan[$i]['ffa']=$row2[3];

		echo "<input type='hidden' id='fan[".$i."][nf]' value='".$fan[$i]['nf']."'>";
		echo "<input type='hidden' id='fan[".$i."][ffa]' value='".$fan[$i]['ffa']."'>";

		//2009-11-03
		$query =  " SELECT Fennpa
					  FROM ".$wbasedato."_000018, ".$wbasedato."_000021
					 WHERE Rdefac='".$fan[$i]['nf']."'
					   AND Rdefac=Fenfac
					   AND Rdeffa=Fenffa";
		$err1 = mysql_query($query,$conex);
		$row5 = mysql_fetch_array($err1);
		//echo mysql_errno() ."=". mysql_error();
		$nom=$row5[0];

		$query=" SELECT Fenfac, Fensal
				   FROM ".$wbasedato."_000018
				  WHERE Fenfac='".$fan[$i]['nf']."'
				    AND Fenffa='".$fan[$i]['ffa']."'
				    AND Fenest='on'";
		$err1 = mysql_query($query,$conex);
		//echo mysql_errno() ."=". mysql_error();
		$numfac = mysql_num_rows($err1);
		$rowfac = mysql_fetch_array($err1);
		$fa[$i]['sf']=$rowfac[1];
		echo "<input type='hidden' id='fa[".$i."][sf]' value='".$fa[$i]['sf']."'>";

		if ($rowfac[0]==$row2[0])
		$vbl="<input type='checkbox' id='anular[".$i."]' onclick='anularDev(".$i.")'>";
		else
		$vbl="&nbsp";

		//echo "<tr class=".$wcf."><td>$vbl</td><td>".$row2[3]." </td><td>".$row2[0]." </td><td>".$nom."</td><td align=right>".number_format($row2[1],0,'.',',')."</td></tr>";
		echo "<tr class=".$wcf."><td>".$row2[3]." </td><td>".$row2[0]." </td><td>".$nom."</td><td align=right>".number_format($row2[1],0,'.',',')."</td></tr>";

	}
	echo "<tr class='encabezadoTabla'><td colspan=2><b>VALOR TOTAL DEL DOCUMENTO</td><td colspan=3 align=right>".number_format($row3[0],0,'.',',')."</td></tr>";
	echo "<input type='hidden' id='total' value='".$row3[0]."'>";
	/*echo "<tr class='encabezadoTabla'><td colspan=2><b>VALOR TOTAL DE LA ANULACION</td><td colspan=3 id='totalAnulado' align=right>".number_format($totan,0,'.',',')."</td></tr>";
	echo "<input type='hidden' id='totan' value='".$totan."'>";*/
	//echo "<tr class='encabezadoTabla'><td colspan=2><b>ANULAR  TODO EL DOCUMENTO</td><td colspan=3 align=right><input type='checkbox' id='anuladev' onclick='anularTodosDev(this);'></td></tr>";
	if ($row3[3]!='off')// muestra el letrero de documento anulado en envios
	{
		echo "<tr class='encabezadoTabla'><td colspan=2><b>ANULAR  TODO EL DOCUMENTO</td><td colspan=3 align=right><input type='checkbox' id='anuladev' onclick='guardarAnulacionDev();'></td></tr>";
	}
	echo "<tr><td></td></tr>";
	echo "<tr id='guardarAnulacion' style='display:none;'><td align='center' colspan=5><input type='button' id='guarAnulRad' value='Anular' onclick='guardarAnulacionDev();'></td></tr>";
	echo "</table>";

	echo "<br>";
	echo "<center><table>";
	$hyper="<A HREF='/matrix/ips/procesos/Imp_EnRaDe.php?empresa=".$wbasedato."&amp;cc=".$cc[0]."&amp;dcto=".$codoc."&amp;fuente=".$fuente."' target='new' class='vinculo' style='text-decoration:none; font-weight:bold'>Imprimir Documento</a><br><br>";
	echo "<tr><td  colspan= '4' align=center>$hyper</td></tr>";
	echo "</table></center>";
 return;
}

if(isset($ajaxAnuDev)) // funcion que realiza los movimientos necesarios en las tablas para anular una devolucion sea por facturas o de una radicación completa
{




	$cc=explode('-',$suc);
	$fue=explode('-',$fuente);

	$nval=$totdc-$totan;////////////////////////////////////esta es la resta de la anulacion
	/*if($ingreso==1)
	{*/
		if($anularDoc=='si' || $nval==0)
		{
			$query= "UPDATE ".$wbasedato."_000021
						SET Rdeest='off'
					  WHERE Rdefue='".$fue[0]."'
						AND Rdenum='".$codoc."'";
			$err = mysql_query($query,$conex);
			$query= "UPDATE ".$wbasedato."_000020
						SET Renest='off'
					  WHERE Renfue='".$fue[0]."'
						AND Rennum='".$codoc."'";
			$err = mysql_query($query,$conex);
			$query= "UPDATE ".$wbasedato."_000071
						SET Docest='off'
					  WHERE Docfue='".$fue[0]."'
						AND Docnum='".$codoc."'";
			$err = mysql_query($query,$conex);
		}else
		 {
			$query= "UPDATE ".$wbasedato."_000020
						SET Renvca=".$nval."
					  WHERE Renfue='".$fue[0]."'
						AND Rennum='".$codoc."'
						AND Rencco='".$cc[0]."'";
			$err = mysql_query($query,$conex);
		  }
	/*}*/

	$query= "UPDATE ".$wbasedato."_000018
				SET Fenesf='RD'
			  WHERE Fenfac='".$nfac."'
				AND Fenffa='".$ffa."'";
	$err = mysql_query($query,$conex);


	$err = mysql_query($query,$conex);

	$query= "UPDATE ".$wbasedato."_000021
			    SET Rdeest='off'
			  WHERE Rdefac='".$nfac."'
				AND Rdeffa='".$ffa."'
				AND Rdefue='".$fue[0]."'
				AND Rdenum='".$codoc."'";
	$err = mysql_query($query,$conex);
	/*if($nval!=0)
	echo "<center><div><font size=3 color=blue><b> ANULACI&Oacute;N EXITOSA </b></font></div></center>";
	 else*/
	 echo "<center><div><font size=3 color=blue><b> EL DOCUMENTO No: ".$codoc." HA SIDO ANULADO </b></font></div></center>";
	return;
}
////**************************************************************************FUNCIONES PHP PARA LAS GLOSAS**********************************************************************************
if(isset($ajaxAddGlosar))//trae los datos de una factura, en caso de no estar ya ingresada.
{




	$respuesta='';
	$fec=date("Y-m-d");// para tener la fecha actual
    $hora=date("H:i:s");// para tener la hora actual
	$usua=explode("-",$usuario);
	$fuente = explode("-",$fuente);
	$respo =  explode("-",$responsable);
	//primero verificamos que no esté en la 45
	$qval = "SELECT COUNT(*)
			   FROM ".$wbasedato."_000045
			  WHERE Temnfa='".$pre."-".$nufa."'
			    AND Temffa='".$fue."'
				AND Temfue='".$fuente[0]."'
				AND Temres like '%{$respo[0]}%'";
	$rs = mysql_query($qval, $conex);
	$res=mysql_fetch_array($rs);
	if($res[0]>0)
	{
		$respuesta='repetido';
	}else
	 {
		//Se cambia fencod por fenres
		$query =  " SELECT Fenffa, Fenfac, Fennpa, Fensal
					  FROM ".$wbasedato."_000018
					 WHERE Fenesf='RD'
					   AND Fenres='".trim($respo[0])."'
					   AND Fenest='on'
					   AND Fenfac='".$pre."-".$nufa."'
					   AND Fenffa='".$fue."'
					   AND Fensal>0";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num==0)
		{
			$respuesta='NE';
		}
		for ($i=1;$i<=$num;$i++)
		 {
			$row = mysql_fetch_array($err);

			$fuefac=$row[0];
			$numfac=$row[1];
			$nomfac=$row[2];
			$salfac=$row[3];

			$query="INSERT INTO  ".$wbasedato."_000045
							(   Medico,     Fecha_data, Hora_data,    Temfue,     Temdoc,       Temfec,    Temsuc,     Temcaj,        Temres,    Temvre,         Temnfa,          Temvfa,    Temsfa,      Temvcf,  Temcon,  Temdco,   Temffa,   Temccc, Temglo,  Temnom, Seguridad)
					 VALUES ('".$wbasedato."','".$fec."','".$hora."','".$fuente[0]."','".$dcto."','".$fecha."','".$suc."','".$caja."','".$responsable."',''   ,'".$pre."-".$nufa."',     '',    '".$salfac."',    '',      '',  '".$nomfac."',   '".$fue."',  '', 'on'  ,'".$nomfac."', 'A-".$usua[1]."')";
			$errins = mysql_query($query,$conex);
			$respuesta='guardado';
		 }
	 }
	$data=array('respuesta'=>$respuesta);
	echo json_encode($data);
	return;
}

if(isset($ajaxVerGlosar))//funcion que va a mostrar en pantalla las facturas que se van a glosar.
{




	$fuente=explode("-",$fuente);
	$filtrarResponsable='';
	$filtrarUltimo = '';
	$tabla='';
	$filtrarResponsable=" AND Temres LIKE '%".$responsable."%'";
	if($up45=='no')
	{
		$filtrarUltimo = " ORDER BY id Desc
							LIMIT 1 ";
	}
	$qact = "UPDATE ".$wbasedato."_000045 "
		   ."   SET Temdoc = '".$dcto."',"
		   ."		Temfue='".$fuente[0]."'"
		   ." WHERE Temsuc='".$suc."'
				AND Temcaj='".$caja."'
				AND Temglo='on'"
		   ."".$filtrarResponsable."";
	$err = mysql_query($qact,$conex);

	$query =  " SELECT Temffa, Temnfa, Temnom, Temsfa, Temres, Temvcf, id
				  FROM ".$wbasedato."_000045
				 WHERE Temfue='".$fuente[0]."'
				   AND Temdoc='".$dcto."'
				   AND Temsuc='".$suc."'
				   AND Temcaj='".$caja."'
				   AND Temglo='on'"
				   ."".$filtrarResponsable.""
				   ."".$filtrarUltimo."";
	$err = mysql_query($query,$conex);
	//echo mysql_errno() ."=". mysql_error();
	$numte = mysql_num_rows($err);


	$totsfac=0;// para el total de los saldos de las facturas
	$totvglo=0;// para el total de los valores glosados
	if($numte>0 && $ingreso==0)
	{
		$tabla = "<center><table border='0' WIDTH=1000 id='tbFacturasGlosadas'>";
		$tabla .= "<tr id='trFacturasInvalidas' style='display:none;'><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align='center' nowrap='nowrap' bgcolor='yellow'  style='CURSOR: pointer' onclick='mostrarFacturasInvalidas()'><font size=2><i>Ver Facturas Invalidas</i></font></td></tr>";
		$tabla .= "<tr class='encabezadoTabla'><td colspan=4 id='tabla_facturas_glosar' align=center><b>DETALLE DE LAS FACTURAS QUE VAN A SER GLOSADAS</b></td><td>TOTAL</td><td align='right' id='totGlosado2'>0</td></tr>";
	    $tabla .= "<tr class='encabezadoTabla' id='encabezadoGlosas'><td WIDTH=100><b>CANCELAR</td><td WIDTH=75><b>Fuente de la Factura</td><td WIDTH=150><b>".utf8_encode("Nro. Factura")."</td><td WIDTH=375><b>Nombre</td><td WIDTH=150><b>Saldo Factura</td><td WIDTH=150><b>Valor Glosado</td></tr>";
	}
	$facturas_mostradas=$ingreso+$numte;
	for ($i=$ingreso+1;$i<=$facturas_mostradas;$i++)
	{
		if (is_int ($i/2))
		   $wcf="fila1";  // color de fondo de la fila
		else
		   $wcf="fila2"; // color de fondo de la fila

		$rowte = mysql_fetch_array($err);
		$arr[$i]['fuf']=$rowte[0];
		$arr[$i]['nuf']=$rowte[1];
		$arr[$i]['nof']=$rowte[2];
		$arr[$i]['saf']=$rowte[3];
		$arr[$i]['resp']=$rowte[4];
		$arr[$i]['vgl'] = $rowte[5];


		$tr  = "<tr class=".$wcf." id='tr".$i."' rel='".$i."'><td align=center><input type='checkbox' name='delete[".$i."]' onclick='quitarFactura(".$i.");'></td>";
		$tr .="<td>".$arr[$i]['fuf']."</td><td>".$arr[$i]['nuf']."</td><td>".utf8_encode($arr[$i]['nof'])."</td><td align=right>".number_format($arr[$i]['saf'],0,'.',',')."</td>";
		$tr .="<td><INPUT TYPE='text' id='arr[".$i."][vgl]' VALUE='".$arr[$i]['vgl']."' SIZE=13 onkeypress='if(validar(event)) actualizarSaldoFacturasGlosar(".$i.", \"glosado\");' onblur='actualizarSaldoFacturasGlosar(".$i.", \"glosado\");'></td>
			   <td  style='display:none;'><input type='hidden' id='arr[".$i."][nuf]' value='".$arr[$i]['nuf']."'>
										  <input type='hidden' id='arr[".$i."][fuf]' value='".$arr[$i]['fuf']."'>
										  <input type='hidden' id='arr[".$i."][saf]' value='".$arr[$i]['saf']."'>
										  <input type='hidden' id='respo".$i."' value='".$arr[$i]['resp']."'></td></tr>";

		if($ingreso==0)//si está cargando desde la temporal.
		{
		 $tabla .= $tr;
		}else
			{
				$data = array('tr'=>$tr, 'saldo'=>$arr[$i]['saf']);
				echo json_encode($data);
				return;
			}
		 $tabla .= "<input type='hidden' id='numte' value='".$numte."'>";
		$arr[$i]['vgl'] = ( !isset( $arr[$i]['vgl'] ) or trim($arr[$i]['vgl']) == "" ) ? 0 : $arr[$i]['vgl'];

		$totsfac=$totsfac+$arr[$i]['saf'];
		$totvglo=$totvglo+$arr[$i]['vgl'];

		//echo "<input type='hidden' name='totvglo' value='".$totvglo."'>";
	}
	if($numte>0 && $ingreso==0)
	{

		/************************************************************************
		* Se agrega los campos causas y observacion para las facturas glosadas	*
		************************************************************************/

		// para el proceso de causas
		$tabla .= "</table></center>";
		$tabla .= "<center><table border='0' WIDTH=1000 >";
		$tabla .= "<tr class='encabezadoTabla'><td WIDTH=700><b>TOTAL</td><td align=right WIDTH=150 id='totSalFac'>".number_format($totsfac,0,'.',',')."</td><td align=right WIDTH=150 id=totGlosado>".number_format($totvglo,0,'.',',')."</td></tr>";
		$tabla .= "<tr class='fila1' align=center ><td colspan=6><b>CAUSAS DE LA GLOSA</td></tr>";

		////////////////////////////////////////////////////////////para las causas de devolucion
		$q="SELECT caucod, caunom
			FROM ".$wbasedato."_000072
			WHERE cauest='on'
			AND Caudev='on' ";

		$res1 = mysql_query($q,$conex);
		$num1 = mysql_num_rows($res1);

		$tabla .= "<tr>";
				$tabla .= "<td colspan=9 align=center><select id='cauSel' onchange='agregarCausa(\"cauSel\",\"add\",\"\");'>";
						$tabla .= "<option value='' selected>--</option>";

		for ($i=1;$i<=$num1;$i++)
		{
			$row1 = mysql_fetch_array($res1);
			$tabla .= "<option value='".$row1[0]."'>".$row1[0]." - ".$row1[1]."</option>";
		}

		$tabla .= "</select></td></tr>";
		$tabla .= "<input type='hidden' id='lista_causas' value=''/>";

		$tabla .= "<tr><td colspan=9 class='fila2'><div id='div_causas' width='600px; text-align:left;' align='center'></div></td></tr>";

		$tabla .= "<tr class='fila1' align=center ><td colspan=6><b>OBSERVACION GENERAL DEL DOCUMENTO</td></tr>";

		$tabla .= "<tr align=center><td colspan=6><TEXTAREA id='obser' COLS=100 ROWS=2 align=center>".$obser."</TEXTAREA></td></tr>";
		//echo "<tr><td align=center colspan=6><input type='submit' value='OK'></td></tr>";

		$tabla .= "</table></center>";
		$tabla .= "<div align='center'><input type='button' id='grabarGlosa' value='GRABAR' onclick='grabarGlosa2();'/></div>";
	}
	$tabla .= "<input type='hidden' id='facturas_mostradas' value='".$facturas_mostradas."'>";
	//se arma el detalle de las facturas que no se pueden glosar desde el documento;
	$qinva =  " SELECT Temffa, Temnfa, Temnom, Temsfa, Temres, Temvcf, id, Temter
				  FROM ".$wbasedato."_000045
				 WHERE Temfue='".$fuente[0]."'
				   AND Temdoc='".$dcto."'
				   AND Temsuc='".$suc."'
				   AND Temcaj='".$caja."'
				   AND Temglo='off'"
				   ."".$filtrarResponsable.""
				   ."".$filtrarUltimo."";
	$rsInva = mysql_query($qinva,$conex);
	$invalidas = "<center><table>
				  <tr class='encabezadotabla'><td colspan=3>FACTURAS QUE NO PUDIERON SER GLOSADAS</td></tr>
				  <tr class='encabezadoTabla'>
					<td>FUENTE</td>
					<td>FACTURA</td>
					<td>ESTADO</td>
				  <tr>";
	$numInvalidas = 0;
	while($rowInv = mysql_fetch_array($rsInva))
	{
		$numInvalidas++;
		if(is_int($numInvalidas/2))
			$wclass = 'fila1';
			else
				$wclass= 'fila2';
		switch($rowInv['Temter'])
		{
			case 'NRD':
				$descripcion = 'NO EST&Aacute; RADICADA';
				break;
			case 'NE':
				$descripcion = 'RESPONSABLE ERRONEO';
				break;
			case 'REP':
				$descripcion = 'YA ESTA SIENDO GLOSADA';
				break;

		}
		$invalidas .= "<tr class='{$wclass}'><td>{$rowInv['Temffa']}</td><td>{$rowInv['Temnfa']}</td><td>{$descripcion}</td></tr>";
		$qdelete = "DELETE
					  FROM {$wbasedato}_000045
					 WHERE Temfue='".$fuente[0]."'
					   AND Temffa='".$rowInv['Temffa']."'
					   AND Temnfa='".$rowInv['Temnfa']."'
					   AND Temdoc='".$dcto."'
					   AND Temsuc='".$suc."'
					   AND Temcaj='".$caja."'
					   AND Temglo='off'";
		$rsDelete = mysql_query($qdelete, $conex);
	}
	$invalidas .= "<tr><td colspan=3>&nbsp;</td></tr>";
	$invalidas .= "<tr><td colspan=3>&nbsp;</td></tr>";
	$invalidas .= "<tr align='center'><td colspan=3><input type='button' id='ocultarFacturasInvalidas' value='CERRAR' onclick='ocultarFacturasInvalidas()'></td></tr>";
	$invalidas .= "</table></center>";

	if(($up45=='si') and ($numte==0))
	{
		$tabla='';
		$data = array('tabla'=>$tabla, 'invalidas'=>$invalidas);
		echo json_encode($data);
		return;
	}

	if($numInvalidas==0)
		$invalidas = '';
	$data = array('tabla'=>$tabla, 'invalidas'=>$invalidas);
	echo json_encode($data);
	return;
}

if(isset($ajaxQuitarFactura))//funcion que elimina  de la 45 y de la pantalla una factura.
{




	$fuente = explode("-",$fuente);
	$query = "DELETE
			    FROM ".$wbasedato."_000045
			   WHERE Temffa='".$fuente[0]."'
				 AND Temnfa='".$factura."'";
	$rs=mysql_query($query, $conex);
	//echo $query;
	return;
}

if(isset($ajaxGrabarGlosa))//funcion que vacea la 45 y va haciendo los movimientos necesarios en las tablas para hacer efectiva la glosa
{




	$fec=date("Y-m-d");// para tener la fecha actual
	$hora=date("H:i:s");// para tener la hora actual
	$res=explode("-",$responsable);// para solo el nombre necesario del responsable
	$fue=explode("-",$fuente);// para poner solo el codigo de la fuente
	$caj=explode("-",$caja);// para poner solo el numero de la caja
	$cc=explode("-",$suc);// para poner solo el numero de centro de costos
	$usua=explode("-", $usuario);//para el usuario

	if($ingreso=='1')// si es la primera vez que entra se crea el encabezado
	{
		 ////////////////////////////////SE BLOQUEA LA TABLA DE LAS FUENTES
		 $q = "lock table ".$wbasedato."_000040 LOW_PRIORITY WRITE";
		 $errlock = mysql_query($q,$conex);

		 $query =" SELECT Carcon
					 FROM ".$wbasedato."_000040
					WHERE Carfue='".$fue[0]."'
					  AND Carest='on'";
		$err = mysql_query($query,$conex);
		//echo mysql_errno() ."=". mysql_error();
		$num = mysql_fetch_array($err);

		if ($num[0]=='')
		$num=1;
		else
		$dcto=$num[0]+1;

		 /////////////////Aca actualizo el consecutivo de la fuente
		$q= "   UPDATE ".$wbasedato."_000040 "
		."      SET carcon = carcon + 1 "
		."      WHERE carfue ='".$fue[0]."'"
		."      AND carest = 'on' ";
		//echo "<br>-------- ".$q;
		$res1 = mysql_query($q,$conex);

		/////////////////////////////////SE DESBLOQUEA LA TABLA DE FUENTES
		$q = " UNLOCK TABLES";
		$errunlock = mysql_query($q,$conex) or die (mysql_errno()." - ".mysql_error());


		// Obtengo días de holgura para la fuente
		$qholgura =  " SELECT Cardia
					FROM ".$wbasedato."_000040
					WHERE Carfue='".$fue[0]."'
					AND Carest='on'";
		//echo "<br>-------- holgura: ".$qholgura;
		$errholgura = mysql_query($qholgura,$conex);
		$rowholgura = mysql_fetch_array($errholgura);
		$holgura = $rowholgura['Cardia'];
		//echo "<br>Holgura".$holgura;
		if(!isset($holgura) || !$holgura || $holgura=="")
			$holgura = 0;

		// Si no viene definida la fecha desde el formulario asigno la fecha actual
		//echo "<br>Fecha".$wfecha;
		if(!isset($wfecha) || !$wfecha || $wfecha=="" || $wfecha=="0")
			$wfecha = $fec;

		// este query inserta el encabezado del documento
		$query="INSERT INTO  ".$wbasedato."_000020
							(   Medico,     Fecha_data, Hora_data,    Renfue,     Rennum,       Renvca,      Rencod,       Rennom,          Rencaj,        Renusu,        Rencco,     Renest,    Renfec,   Rendia,    Renobs,  Seguridad)
					 VALUES ('".$wbasedato."','".$fec."','".$hora."','".$fue[0]."','".$dcto."','".$totvglo."','".$res[0]."','".$res[2]."','".$caj[0]."','".$usua[1]."',  '".$cc[0]."',    'on',   '".$wfecha."',   ".$holgura.",  '".$obser."' , 'A-".$usua[1]."')";
		$errine = mysql_query($query,$conex);
		//echo "<br>-------- insertando encabezado".$query;

	}
	 // este query le cambia el estado a la factura por glosado
	 $query= "UPDATE ".$wbasedato."_000018
				 SET Fenesf='GL'
			   WHERE Fenfac='".$nfac."'
				 AND Fenffa='".$ffac."'  ";
	$erru = mysql_query($query,$conex);
	//echo "<br>-------- Actualizando estado en la 18 ".$query;

	// este query inserta el detalle del documento
	 $query="INSERT INTO  ".$wbasedato."_000021
						 (    Medico,      Fecha_data, Hora_data,     Rdefue,      Rdenum,     Rdecco,         Rdefac,         Rdevta,        Rdevca,         Rdeest,  Rdecon,  Rdevco,         Rdeffa,                Rdesfa,          Seguridad)
				  VALUES ('".$wbasedato."','".$fec."', '".$hora."','".$fue[0]."','".$dcto."','".$cc[0]."','".$nfac."',   '',   '".$vglfac."',  'on',     '',     '0',   '".$ffac."','".$salfac."','A-".$usua[1]."')";
	$errind = mysql_query($query,$conex);
	//echo "<br>-------- insertando datos en la 21".$query;


	$causas=explode(",",$lista_causas);
	for ($j=0;$j<count($causas);$j++)
	{
		$causa=explode('-',$causas[$j]);
		$q= " INSERT INTO ".$wbasedato."_000071
					(Medico,
					Fecha_data,
					Hora_data,
					docfue,
					docnum,
					doccau,
					docest,
					Seguridad)
			VALUES ('".$wbasedato."',
					'".$fec."',
					'".$hora."' ,
					'".$fue[0]."',
					".strtoupper($dcto).",
					'".$causa[0]."',
					'on',
					'C-".$usua[1]."')";
		$err = mysql_query($q,$conex);
		//echo "<br>-------- insertando causas".$q;
	}
	///////////////////////////////////////query pa borrar la tabla temporal
   $query ="DELETE
			  FROM ".$wbasedato."_000045
			 WHERE Temsuc='".$suc."'
			   AND Temcaj='".$caja."'
			   AND Temnfa='".$nfac."'
			   AND Temffa='".$ffac."'
			   AND Seguridad='A-".$usua[1]."'";
	 $errdel = mysql_query($query,$conex);
	// echo "<br>-------- eliminando la factura de la 45: ".$query;

	// este es el hipervinculo para mostrar el documento cuando se graba

	$hyper="<A HREF='/matrix/ips/procesos/Imp_EnRaDe.php?empresa=".$wbasedato."&amp;cc=".$cc[0]."&amp;dcto=".$dcto."&amp;fuente=".$fuente."' target='new' class='vinculo' style='text-decoration:none; font-weight:bold'>Imprimir Documento</a><br><br>";
	$notificacion = "<center><table><tr><td colspan='4'>LA GLOSA SE HA REALIZADO SATISFACTORIAMENTE</td></tr>";
	$notificacion .= "<tr><td  colspan= '4' align=center>".$hyper."</td></tr></table></center>";
	$datos=array('respuesta'=>utf8_encode($notificacion));
	echo json_encode($datos);

	 return;
}

if(isset($ajaxcdGlo))// funcion para consultar una glosa
{




	//echo 'sirve';
	$fue=explode('-',$fuente);
	$cod=explode('-',$responsable);// se trae el codigo del responsable de la consulta
	$cc=explode('-',$suc);

	// este query es para traer los datos del encabezado del documento cuando se consulta
	$query =  " SELECT Renvca, Renfec, Rennum, Renest
				  FROM ".$wbasedato."_000020
				 WHERE Renfue='".$fue[0]."'
				   AND Rennum='".$codoc."'
				   AND Rencod='".$cod[0]."'";
	$err = mysql_query($query,$conex);
	$rowen = mysql_fetch_array($err);
	$numgl=mysql_num_rows($err);
	//echo $query;
	if($numgl==0)
	{
		echo "<p><font color='blue' size='5'>EL DOCUMENTO {$codoc} NO EXISTE</font></p>";
		return;
	}

	if ($rowen[3]=='off')
	{
		echo "<center><h1><b>DOCUMENTO ANULADO</b></h1></center>";
	}
	echo "</table>";
	echo "<table align='center' border=0>";
	echo "<tr><td class='encabezadoTabla' colspan=6 align=center><b>DETALLE DE LAS FACTURAS CONSULTADAS</b></td></tr>";
	echo "<tr><td class='fila2' colspan=3 align=center><b>DOCUMENTO Nro: ".$rowen[2]."</b></td><td class='fila2' colspan=3 align=center><b>FECHA DEL Dcto: ".$rowen[1]."</b></td></tr>";
	echo "<tr class='encabezadoTabla'><td><b>Fuente de la Factura</td><td><b>Nro Factura</td><td><b>Nombre</td><td><b>Saldo Factura</td><td><b>Valor Glosado</td></tr>";

	// este query es para traer los datos del detalle del documento cuando se consulta
	$query =  " SELECT Rdeffa, Rdefac, Fennpa, Fensal, Rdevca, ".$wbasedato."_000021.id
				  FROM ".$wbasedato."_000021, ".$wbasedato."_000018
				 WHERE Rdefue='".$fue[0]."'
				   AND Rdenum='".$codoc."'
				   AND Rdefac=Fenfac
				   AND Rdeffa=Fenffa
			  ORDER BY id";
	$err = mysql_query($query,$conex);
	$numde = mysql_num_rows($err);
	for ($i=1;$i<=$numde;$i++)
	{
		if (is_int ($i/2))
		   $wcf="fila1";  // color de fondo de la fila
		else
		   $wcf="fila2"; // color de fondo de la fila

		$rowde = mysql_fetch_array($err);
		$arr[$i]['fuf']=$rowde[0];
		$arr[$i]['nuf']=$rowde[1];
		echo "<input type='hidden' id='arr[".$i."][fuf]' value='".$arr[$i]['fuf']."'>";// se envian las vbles para cuando se va a anular el docto
		echo "<input type='hidden' id='arr[".$i."][nuf]' value='".$arr[$i]['nuf']."'>";
		echo "<input type='hidden' id='fecglo' value='".$rowen[1]."'>";
		echo "<tr class=".$wcf."><td>".$rowde[0]."</td><td>".$rowde[1]." </td><td>".$rowde[2]." </td><td>".number_format($rowde[3],0,'.',',')."</td><td align=right>".number_format($rowde[4],0,'.',',')."</td></tr>";

	}
	echo "<input type='hidden' id='nan' value='".$numde."'>";
	echo "<tr class='encabezadoTabla'><td colspan=2><b>VALOR TOTAL DEL DOCUMENTO</td><td colspan=3 align=right>".number_format($rowen[0],0,'.',',')."</td></tr>";

	if ($rowen[3]!='off')
	{
		echo "<tr class='encabezadoTabla'><td colspan=2><b>ANULAR  TODO EL DOCUMENTO</td><td colspan=3 align=right><input type='checkbox' id='anulaglo' onclick='anularGlosa();'></td></tr>";
	}
	echo "</table>";

	// este es el hipervinculo para mostrar el documento cuando se consulta
	echo "<br>";
	echo "<center><table>";
	$hyper="<A HREF='/matrix/ips/procesos/Imp_EnRaDe.php?empresa=".$wbasedato."&amp;cc=".$cc[0]."&amp;dcto=".$rowen[2]."&amp;fuente=".$fuente."' target='new' class='vinculo' style='text-decoration:none; font-weight:bold'>Imprimir Documento</a><br><br>";
	echo "<tr><td  colspan= '4' align=center>$hyper</td></tr>";
	echo "</table></center>";

return;
}

if(isset($ajaxAnuGlo))//funcion para anular una glosa
{




	// esto es para hacer la validacion del mes contable, es decir no se puede anular glosas sin el mes ya paso
   $fecha=date("Y-m-d");// para tener la fecha actual
   $fgl=explode('-',$fecglo); // fecha del docto
   $fac=explode('-',$fecha); // fecha actual

   // este query es para la validacion de los dias de holgura
   $query =  " SELECT cardia
				 FROM ".$wbasedato."_000040
				WHERE Carglo='on'";
	$errdia = mysql_query($query,$conex) or die (mysql_error());
	$rowdia= mysql_fetch_array($errdia);

  if ((($fgl[0] == $fac[0]) and ($fgl[1] == $fac[1])) or (($fgl[1] != $fac[1]) and ($fac[1]-$fgl[1]==1) and ($fac[2]<=$rowdia[0]))) // solo deja anular si no ha pasado el mes contable o si tiene dias de holgura
   {
	$fue=explode('-',$fuente);
		if($ingreso==1)
		{
			// se ponen en off el detalle del docto
			$query= "UPDATE ".$wbasedato."_000021
						SET Rdeest='off'
					  WHERE Rdefue='".$fue[0]."'
						AND Rdenum='".$codoc."'";
			$err = mysql_query($query,$conex);
			//echo "<br>".$query;

			// se pone en off el encabezado del docto
			$query= "UPDATE ".$wbasedato."_000020
						SET Renest='off'
					  WHERE Renfue='".$fue[0]."'
						AND Rennum='".$codoc."'";
			$err = mysql_query($query,$conex);
			//echo "<br>".$query;

			$query= "UPDATE ".$wbasedato."_000071
								SET Docest='off'
								WHERE Docfue='".$fue[0]."'
								AND Docnum='".$codoc."'";
			$err = mysql_query($query,$conex);
			//echo "<br>".$query;
		// se regresa al estado de radicada la factura
		}
		//echo "<br>".$query;
		$query= "UPDATE ".$wbasedato."_000018
					SET Fenesf='RD'
				  WHERE Fenfac='".$nfac."'
					AND Fenffa='".$ffa."'";
		//echo "<br>".$query;
		$err = mysql_query($query,$conex);
		/*echo $query;
		echo "<br>";*/
		echo "<center><div><font size=3 color=blue><b> EL DOCUMENTO No: ".$codoc." HA SIDO ANULADO </b></font></div></center>";

	}
	else
	{
		echo "<center><div><font size=3 color=blue><b>EL DOCUMENTO NO SE PUEDE ANULAR DEBIDO A QUE YA HA PASADO EL MES CONTABLE </b></font></div></center>";
	}
	return;
}

if(isset($ajaxGlosarDocumento))//funcion que lista en pantalla y agrega a la 45 un conjunto de facturas que llegan de un documento.
{




	$wusuario=explode('-', $wusuario);
	$wusuario=$wusuario[1];
	function guardarTemporalArchivo($wfecha, $hora, $wfuente, $wnrodoc, $wfecdoc, $wcco, $wcaja, $wempresa, $prefac, $numfac, $canfac, $wvalfac, $wsalfac, $centro, $tipo, $fuefac, $multi, $wusuario, $nomcon, $cancon, $wcarcca, $wcarcfa, $wcarrec, $wcarndb, $estado, $glosa, $refval = 'off', $nombre )
	{
	global $conex;
	global $wbasedato;
	global $wemp_pmla;
	$q= " INSERT INTO ".$wbasedato."_000045 (       Medico  ,   Fecha_data ,    Hora_data,      Temfue,       temdoc  ,        temfec  ,     temsuc,      temcaj,       temres  ,  temvre,          temnfa         ,     temvfa   ,       temsfa,        temvcf ,    temcon   ,   temdco ,   temvco      ,  temffa,     temfco,      temccc,        temcco,      temter,  temglo, temref, Seguridad , temnom ) "
	."                            	VALUES ('".$wbasedato."','".$wfecha."' ,'".$hora."' , '".$wfuente."','".$wnrodoc."','".$wfecdoc."' ,'".$wcco."','".$wcaja."','".$wempresa."', '0'   , '".$prefac."-".$numfac."', '".$wvalfac."','".$wsalfac."','".$canfac."','".$nomcon."',  $multi,'".$cancon."',  '".$fuefac."', '".$tipo."', '".$centro."'  ,'',        '".$estado."'  , '".$glosa."' ,'$refval', 'A-".$wusuario."', '$nombre')";

	$res2 = mysql_query($q,$conex) or die (mysql_errno()." -NO SE HA PODIDO GUARDAR EL NUEVO REGISTRO EN LA TABLA TEMPORAL - $q ".mysql_error());
	}

	$filaFacturaInvalida=0;
	$respo=explode("-",$wempresa);
	$nitEmpresa = explode( "-", $wempresa );
	$hora = date("H:i:s");
	$saldos = Array();
	$valorFactura = Array();
	$fuenteFactura = Array();
	$facturasValidas = Array();
	$totalCancelar = Array();
	$responsableFactura = Array();
	$num=(count($_FILES['files']['name'])-1)*1;
	$informacionCargada = Array();
	$respuesta='sin hacer nada';

	//Si el archivo es de tamaño 0, no se ejecuta el proceso
	if( !$_FILES['files']['size'] > 0 ){
		$respuesta='tamaño cero';
		$data=array('respuesta'=>utf8_encode($respuesta));
		echo json_encode($data);
		return;
	}
	$nombre = basename($_FILES['files']['name']);
	if(!copy($_FILES['files']['tmp_name'], $nombre))
		$respuesta='error';
		$file = fopen( $_FILES['files']['tmp_name'], "r" );
	if( $file ){
		//$responsable = '';
		for( $i = 0;!feof($file); $i++ ){
			$guardar = false; //se parte de que no se puede glosar la factura.
			//Leno una linea entera del archivo
			$linea = fgets( $file, 10000 );

			if( strlen( $linea ) > 0 ){

				$exp = explode( ";", $linea );

				$ffaFactura = trim($exp[0]);	//FUENTE DE FACTURA
				$nroFactura = trim($exp[1]);		//Nro de factura
				//$valorAGlosar = $exp[2];	//Valor a cancelar
				$valorCancelar = $exp[2];
				//$valorCancelar = 0;	//Valor a cancelar, original
				$concepto = @$exp[3];	//Concepto
				$valorConcepto = @$exp[4];	//Concepto
				$centroCostos = @$exp[5];

				if( empty( $valorConcepto ) ){
					$valorConcepto = 0;
				}

				$exp = explode( "-", $nroFactura );
				$prefijoFactura = @$exp[0];
				$postFijoFactura = @$exp[1];

				//Consultando valor de la factura y saldo de la facutra
				$sql = "SELECT
							Fensal, Fenval, Fenffa, Fencod, Fenres, Fennpa, Fenesf
						FROM
							".$wbasedato."_000018
						  WHERE fenfac = '{$nroFactura}'
							AND	fenffa = '{$ffaFactura}'
							AND fenres = '".trim($respo[0])."'
							AND Fensal>0
							AND fenest='on'
						";
				$res = mysql_query( $sql, $conex ) or die( mysql_errno()." - Error en el query $sql - ".mysql_error() );
				$nums= mysql_num_rows($res);
				if( $nums>0  ){
					$rows = mysql_fetch_array( $res );
					if($rows['Fenesf']=='RD')
						$guardar = 'RD'; //factura radicada, habilitada para glosarse
						else
							$guardar='NRD'; //factura no radicada, no habilitada para glosarse

					$saldos[ $nroFactura ][ $ffaFactura ] = $rows[ 'Fensal' ];
    //											$valorFactura[ $nroFactura ][ $ffaFactura ] = $rows[ 'Fenval'];
					$valorFactura[ $nroFactura ][ $ffaFactura ] = '';
					$fuenteFactura[ $nroFactura ][ $ffaFactura ] = $ffaFactura;
					$responsableFactura[ $nroFactura ][ $ffaFactura ] = $rows[ 'Fenres'];
					$nombrePaciente[ $nroFactura ][ $ffaFactura ] = $rows[ 'Fennpa'];

					}
					else
					{
						$guardar='NE';// No existe para el responsable elegido
					}

				@$totalCancelar[ $nroFactura ][ $ffaFactura ] += $valorCancelar;

				$exp = explode( "-", $wfcon );
				$wfuente = trim( $exp[0] );

				$ccoValido = true;

				$multi = 0;

				$informacionCargada[ $nroFactura ][ $ffaFactura ][ $responsableFactura[ $nroFactura ][ $ffaFactura ] ][$i]['concepto'] = $valorConcepto*$multi*(-1);

				$informacionCargada[ $nroFactura ][ $ffaFactura ][ $responsableFactura[ $nroFactura ][ $ffaFactura ] ][$i]['cancelar'] = $valorCancelar;

				//if( $responsable == $responsableFactura[ $nroFactura ][ $ffaFactura ] ){
					if($guardar=='RD')
					{
						$glosa='on';
						$qrep = "SELECT * "//verificamos si ya existe en la 45
							   ."  FROM ".$wbasedato."_000045 "
							   ." WHERE Temfue = '{$wfuente}'"
							   ."	AND Temffa = '{$ffaFactura}'"
							   ."	AND Temnfa = '{$nroFactura}'"
							   ."	AND Temnom = '{$nombrePaciente[ $nroFactura ][ $ffaFactura ]}'";
						$rsrep = mysql_query($qrep,$conex);
						//echo $qrep."<br>";
						$num_rep = mysql_num_rows($rsrep);
						if($num_rep>0)
						{
							$guardar='REP';
							$glosa='off';
						}
					}else
						{
							$glosa='off';
						}
						guardarTemporalArchivo( date( "Y-m-d"), date( "H:i:s" ), $wfuente, $wnrodoc, $wfecdoc, $wcco, $wcaja, $wempresa, $prefijoFactura, $postFijoFactura, $valorCancelar, $valorFactura[ $nroFactura ][ $ffaFactura ], $saldos[ $nroFactura ][ $ffaFactura ], $centroCostos, 'off', $ffaFactura, $multi, @$wusuario, @$concepto, $valorConcepto, @$wcarcca, @$wcarcfa, @$wcarrec, @$wcarndb, @$guardar, $glosa, 'off', $nombrePaciente[ $nroFactura ][ $ffaFactura ] );
				//}

			}
		}
		if($filaFacturaInvalida==0)
		{
			$facturasInvalidas='';
		}
	}
	fclose( $file );
	unlink(  $nombre );

	$data=array('respuesta'=>utf8_encode($respuesta), 'error'=>'', 'facturasInvalidas'=>'wepaaa');
	echo json_encode($data);
	return;
}

if(isset($ajaxActGlosa))
{




  $fueGlo = explode("-",$fglo);
  $fglo = $fueGlo[0];
  if(!isset($valor) or ($valor==''))
  $valor=0;
  $query = "UPDATE {$wbasedato}_000045
			   SET Temvcf = {$valor}
			 WHERE Temfue = '{$fglo}'
			   AND Temnfa = '{$fac}'
			   AND Temffa = '{$fuefac}'";

  $rs = mysql_query($query, $conex);
  return;
}

////************************************************************************FUNCIONES PARA LOS COBROS JURIDICOS*****************************************************************************
if(isset($ajaxCojT)){





	$cc=explode('-',$suc);
  function stripAccents($string)
  {
    $string = str_replace("ç","c",$string);
    $string = str_replace("Ç","C",$string);
    $string = str_replace("Ý","Y",$string);
    $string = str_replace("ý","y",$string);
    $string = str_replace("?","",$string);
    $string = str_replace("Â","",$string);
    $string = str_replace("Ã","",$string);
    $string = str_replace("?","",$string);
    $string = str_replace("Ãƒâ€˜","Ñ",$string);
    return $string;
  }

    $res=explode("-",$responsable);
    if (isset($emp))
	    {
	 	$emp=$res[0]."-".$res[2];
	    }

	//2008-11-24
	// se pone pa que traiga los datos sin importar si no tiene historia 2009-11-03
	// se cambia fencod
	$tabla='';
    if($ajaxCojT=="todo")
	{
		$query =  " SELECT Fenfac, Fenval, Fenffa, Fennpa, Fennit
					  FROM ".$wbasedato."_000018
					 WHERE Fenres='".trim($res[0])."'
					   AND Fenfec between '".$fec1."' and '".$fec2."'
					   AND (fenesf='RD'
						OR 	fenesf='DV'
						OR 	fenesf='GL')
					   AND Fenest='on'
					   AND Fensal>0";
	}else
		{
			$query =  " SELECT Fenfac, Fenval, Fenffa, Fennpa, Fennit
					  FROM ".$wbasedato."_000018
					 WHERE Fenfac='".$nfac."'
					   AND Fenffa='".$fue."'
					   AND Fenres='".trim($res[0])."'
					   AND (fenesf='RD'
						OR 	fenesf='DV'
						OR 	fenesf='GL')
					   AND Fenest='on'
					   AND Fensal>0";
			//echo $query;
		}
    $err = mysql_query($query,$conex);
	//or die (mysql_errno() ."=". mysql_error())
    $num = mysql_num_rows($err);
	$ocultas=array();
	if ($num==0 or !isset($num))
        {
			$data=array('tabla'=>utf8_encode($tabla));
			echo json_encode($data);
			return;
        }else
			{
				$facpantalla=$num;
				$inicio=1;
				if($ism>0)
				{
					$inicio=$ism+1;
					$num+=$ism;
					$facpantalla=$ism;
				}

			  for ($i=$inicio;$i<=$num;$i++)
				{
					$row = mysql_fetch_array($err);
					$arr[$i]['nf']=$row[0];
					$arr[$i]['vf']=$row[1];
					$arr[$i]['ffa']=$row[2];
					$arr[$i]['nom']=$row[3];
					$arr[$i]['rs']=$row[4];

				    $ocultas[$i] .= "<input type='hidden' id='arr[".$i."][nf]' name='arr[".$i."][nf]' value='".$arr[$i]['nf']."'>";
					$ocultas[$i] .= "<input type='hidden' name='arr[".$i."][vf]' value='".$arr[$i]['vf']."'>";
					$ocultas[$i] .= "<input type='hidden' id='arr[".$i."][ffa]' name='arr[".$i."][ffa]' value='".$arr[$i]['ffa']."'>";
					$ocultas[$i] .= "<input type='hidden' name='arr[".$i."][nom]' value='".$arr[$i]['nom']."'>";/////////////////nombre

					////////////////////////////////////////////////////////////////////////////ACA COMIENZA LA CONSULTA QUE BUSCA LAS NOTAS Y RECIBOS DE CADA FACTURA

					 /////////////////////////////////////////////////////////////////////////////////////////////////nota debito
					 $query =  " SELECT DISTINCT Renfue, Rennum,Renvca
				                FROM ".$wbasedato."_000021 a, ".$wbasedato."_000040, ".$wbasedato."_000020, ".$wbasedato."_000024
				              	WHERE Rdefac='".$arr[$i]['nf']."'
				              	AND Carfue=Rdefue
				              	AND Carndb='on'
				              	AND Rdeest='on'
								AND Empnit = '".$arr[$i]['rs']."'
								AND Empres = Rencod
				              	AND Carest='on'
				              	AND Renfue=Rdefue
				              	AND Rennum=Rdenum
								HAVING (Max(a.id))";

					$err1 = mysql_query($query,$conex);
					$numnd = mysql_num_rows($err1);
					$y=0;

					for ($j=1;$j<=$numnd;$j++)
						{

							$rownd = mysql_fetch_array($err1);
							$fn[$i][$j]=$rownd[0];
							$nn[$i][$j]=$rownd[1];
							$vn[$i][$j]=$rownd[2];
							$in[$i][$j]='nd';
							$y=1;
						}
					/////////////////////////////////////////////////////////////////////////////////////////////////nota credito
					if( $wemp_pmla != "02" )
						{	//Se quema temporalmente este query para evitar problemas con los envios

							$query = " SELECT DISTINCT Renfue, Rennum, Renvca
										FROM ".$wbasedato."_000021 a, ".$wbasedato."_000040, ".$wbasedato."_000020, ".$wbasedato."_000024
										WHERE Rdefac='".$arr[$i]['nf']."'
										AND Carfue=Rdefue
										AND Carncr='on'
										AND Rdeest='on'
										AND Carest='on'
										AND Empnit = '".$arr[$i]['rs']."'
										AND Empres = Rencod
										AND Renfue=Rdefue
										AND Rennum=Rdenum
										HAVING (Max(a.id))";
							//echo $query;
						}else
							{
								 $query = " SELECT DISTINCT Renfue, Rennum, Renvca
											FROM ".$wbasedato."_000021 a, ".$wbasedato."_000040, ".$wbasedato."_000020, ".$wbasedato."_000024
											WHERE Rdefac='".$arr[$i]['nf']."'
											AND Carfue=Rdefue
											AND Carncr='on'
											AND Rdeest='on'
											AND Carest='on'
											AND Empnit = '".$arr[$i]['rs']."'
											AND Empres = Rencod
											AND Renfue=Rdefue
											AND Rennum=Rdenum
											AND Carcfa='on'
											HAVING (Max(a.id))";;	//Enero 06 de 2012, Se agrega esta condicion AND Carcfa='on', este cambio es temporal
							}

					$err2 = mysql_query($query,$conex);
					$numnc = mysql_num_rows($err2);


					$suma=$numnd+$numnc;
					for ($j=$numnd+1;$j<=$suma;$j++)
					{
						$rownc = mysql_fetch_array($err2);
						$fn[$i][$j]=$rownc[0];
						$nn[$i][$j]=$rownc[1];
						$vn[$i][$j]=$rownc[2];
						$in[$i][$j]='nc';
						$y=1;
					}
					/////////////////////////////////////////////////////////////////////////////////para los recibos
					$query =  " SELECT Rdefue, Rdenum, Rdevca
				                FROM ".$wbasedato."_000021 a, ".$wbasedato."_000040, ".$wbasedato."_000020, ".$wbasedato."_000024, ".$wbasedato."_000044
				              	WHERE Rdefue=Carfue
				              	AND Rdeest='on'
				              	AND Rdefac='".$arr[$i]['nf']."'
				              	AND Rdereg = 0
								AND Empnit = '".$arr[$i]['rs']."'
								AND Empres = Rencod
								AND Renfue=Rdefue
								AND Rennum=Rdenum
				              	AND Carrec='on'
				              	AND Carfue not in (select Ccofrc from ".$wbasedato."_000003 where Ccoest='on')
				              	AND Carest='on'
								AND conest ='on'
				              	AND confue =rdefue
				                AND concod =mid(rdecon,1,instr(rdecon,'-')-1)";
					$err3 = mysql_query($query,$conex);
					$numrc = mysql_num_rows($err3);
					//echo mysql_errno() ."=". mysql_error();
					$suma1=$numnd+$numnc+$numrc;

					for ($j=$suma+1;$j<=$suma1;$j++)
						{

							$rowrc = mysql_fetch_array($err3);
							$fn[$i][$j]=$rowrc[0];
							$nn[$i][$j]=$rowrc[1];
							$vn[$i][$j]=$rowrc[2];
							$in[$i][$j]='rc';
							$y=1;

						}


					//////////////////////////////////////////////////////////////////////////////////////////////para facturas sin nada
					if ($y==0)
					{
						$fn[$i][1]=0;
						$in[$i][1]='';
						$nn[$i][$j]=0;
						$vn[$i][$j]=0;
					}
				}
			}

			$numero=count($arr);
			//////////////////////////////////////aca comieza a pintar
            if ($arr!=0)
            {
                ///////////////////////////////////////////////////query para traer los campos obligatotrios por empresa
	            $res=explode('-',$responsable);

				$query =  " SELECT Cobnpa, Cobnta
				             FROM ".$wbasedato."_000121
				             WHERE Cobest='on'
				             AND Cobcod='".$res[0]."'";
	            $err = mysql_query($query,$conex);
	            $numco = mysql_num_rows($err);

	            //echo "<input type='hidden' name='numco' value='".$numco."'>";

				$csp=0;
				$csp=12+$numco;
				$tamCol='style=width:90px;';

				$tmtb=$csp*90;
				/*if($ism>0)
					$tmtb='100%';*/
					if ($numco>0)
					   {
							if($ism==0)
							{
								$tabla .= "<center><table border='0' width='".$tmtb."'>";
								$tabla .= "<tr><td class='encabezadoTabla' colspan=".($csp-2)." align=center><b>DETALLE DE LAS FACTURAS QUE VAN A SER ENVIADAS</b></td><td class='encabezadoTabla'>TOTAL DOC</td><td class='encabezadoTabla' id='total2' align='rigth'>0</td></tr>";
								$tabla .= "<tr><td class='fondoBlanco' colspan=".$csp." align='left'>&nbsp;<input type='checkbox' id='todas' name='todas' onclick='cambiarTodas(\"$fuente\");'>Seleccionar todas</td></tr>";
								$tabla .= "<tr id='encabezado' class='encabezadoTabla'><td ".$tamCol."><b>Enviar</b></td>";
							}
							for ($l=1;$l<=$numco;$l++)
							{
								$rowco = mysql_fetch_array($err);
								$arr[$l]['co']=$rowco[1];
								//echo "<input type='hidden' name='arr[".$l."][co]' value='".$arr[$l]['co']."'>";
								if($ism==0)
								{
								$tabla .= "<td ".$tamCol."><b>".$rowco[0]."</b></td>";
								}
							}

							if($ism==0)
							{
							$tabla .= "<td ".$tamCol."><b>Fuente de la Factura</b></td><td ".$tamCol."><b>Nro Factura</td><td ".$tamCol."><b>Valor Factura</b></td><td ".$tamCol."><b>Nombre</b></td><td ".$tamCol."><b>Nro Nota Credito</b></td><td ".$tamCol."><b>Valor Nota Credito</b></td>"
									  ."<td ".$tamCol."><b>Nro Nota Debito</b></td><td ".$tamCol."><b>Valor Nota Debito</b></td><td ".$tamCol."><b>Nro Recibo</b></td><td ".$tamCol."><b>Valor Recibo</b></td><td ".$tamCol."><b>Valor Total Factura</b></td></tr>";
							}

					   }else{
								if($ism==0)
								{
									$tabla .= "<center><table border='0' width='".$tmtb."'>";
									$tabla .= "<tr><td class='encabezadoTabla' colspan=10 align=center><b>DETALLE DE LAS FACTURAS QUE VAN A SER ENVIADAS</b></td><td class='encabezadoTabla'>TOTAL DOC</td><td class='encabezadoTabla' id='total2' align='right'>0</td></tr>";
									$tabla .= "<tr><td class='fondoBlanco' colspan=12 align='left'>&nbsp;<input type='checkbox'  id='todas' name='todas' onclick='cambiarTodas(\"$fuente\");'>Seleccionar todas</td></tr>";
									$tabla .= "<tr id='encabezado' class='encabezadoTabla'><td ".$tamCol."><b>Enviar</b></td><td ".$tamCol."><b>Fuente de la Factura</b></td><td ".$tamCol."><b>Nro Factura</b></td><td ".$tamCol."><b>Valor Factura</b></td><td ".$tamCol."><b>Nombre</b></td><td ".$tamCol."><b>N Nota Credito</b></td><td ".$tamCol."><b>Valor Nota Credito</b></td>
											<td ".$tamCol."><b>Nro Nota Debito</b></td><td ".$tamCol."><b>Valor Nota Debito</b></td><td ".$tamCol."><b>Nro Recibo</b></td><td ".$tamCol."><b>Valor Recibos</b></td><td ".$tamCol."><b>Valor Total Factura</b></td></tr>";
								}
					   }
            }

			$inicio=1;
			if($ism>0)
			{
				$inicio=$ism+1;
				$numero+=$ism;
			}
            for ($i=$inicio;$i<=$numero;$i++)
            {
				if (is_int ($i/2))
				   $wcf="fila1";  // color de fondo de la fila
				else
				   $wcf="fila2"; // color de fondo de la fila

			   $j=1;
                while (isset($fn[$i][$j]))
                {
                    if ($j==1)
                    {
                    	////////////////////////////////////////para las notas debito
                        if ($in[$i][$j]=='nd')
                        {
                            $stf[$i]['stf']=$arr[$i]['vf']+$vn[$i][$j];
                            $tabla .= "<tr class=".$wcf."><td align='center' ".$tamCol."><input type='checkbox' id='wgrabar[".$i."]' name='wgrabar[".$i."]' onclick='cambiarEstado($i,\"off\");'/></td>";

                            //////////////////////////////////query pa traer los numeros obligatorios y ponerlos en un textbox
                             for ($l=1;$l<=$numco;$l++)
	            			{
			                	$query =  " SELECT ".$arr[$l]['co']."
								             FROM ".$wbasedato."_000101, ".$wbasedato."_000018
								             WHERE Fenhis=Inghis
								             AND Fening=Ingnin
								             AND Fenfac='".$arr[$i]['nf']."'
								             AND Fenest='on'";

					           $err = mysql_query($query,$conex);
				           	   $numdo = mysql_num_rows($err);
					            //echo mysql_errno() ."=". mysql_error();

				                $rowdo = mysql_fetch_array($err);
				               	$arr[$l][$i]=$rowdo[0];

				               	$tabla .= "<td ".$tamCol."><INPUT TYPE='text' NAME='arrn[".$l."][".$i."]' VALUE='".$arr[$l][$i]."' SIZE=13></td>";
	            			}

                            $tabla .= "<td ".$tamCol.">&nbsp</td><td ".$tamCol.">".$arr[$i]['nf']."</td><td align=right ".$tamCol.">".number_format($arr[$i]['vf'],0,'.',',')."</td><td ".$tamCol.">".stripAccents($arr[$i]['nom'])."</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">".$fn[$i][$j]."-".$nn[$i][$j]."</td><td align=right ".$tamCol.">".number_format($vn[$i][$j],0,'.',',')."</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td>";
                            if (!isset($fn[$i][$j+1]))
                            $tabla .= "<td align=right ".$tamCol." id='st".$i."'>".number_format($stf[$i]['stf'],0,'.',',')."</td></tr>";
                            else
                            $tabla .= "<td ".$tamCol.">&nbsp</td></tr>";
                        }
                        ////////////////////////////////////////para las notas credito
                        else if ($in[$i][$j]=='nc')
                        {
                            $stf[$i]['stf']=$arr[$i]['vf']-$vn[$i][$j];
                            $tabla .= "<tr class=".$wcf."><td align='center' ".$tamCol."><input type='checkbox' id='wgrabar[".$i."]' name='wgrabar[".$i."]' onclick='cambiarEstado($i, \"off\");'/></td>";

                             //////////////////////////////////query pa traer los numeros obligatorios y ponerlos en un textbox
                           for ($l=1;$l<=$numco;$l++)
	            			{
			                	$query =  " SELECT ".$arr[$l]['co']."
								             FROM ".$wbasedato."_000101, ".$wbasedato."_000018
								             WHERE Fenhis=Inghis
								             AND Fening=Ingnin
								             AND Fenfac='".$arr[$i]['nf']."'
								             AND Fenest='on'";

					           $err = mysql_query($query,$conex);
				           	   $numdo = mysql_num_rows($err);
					            //echo mysql_errno() ."=". mysql_error();

				                $rowdo = mysql_fetch_array($err);
				               	$arr[$l][$i]=$rowdo[0];

				               	$tabla .= "<td ".$tamCol."><INPUT TYPE='text' NAME='arrn[".$l."][".$i."]' VALUE='".$arr[$l][$i]."' SIZE=13/></td>";
	            			}

	            			$tabla .= "<td ".$tamCol.">".$arr[$i]['ffa']."</td><td ".$tamCol.">".$arr[$i]['nf']."</td><td align=right ".$tamCol.">".number_format($arr[$i]['vf'],0,'.',',')."</td><td ".$tamCol.">".$arr[$i]['nom']."</td><td ".$tamCol.">".$fn[$i][$j]."-".$nn[$i][$j]."</td><td align=right ".$tamCol.">".number_format($vn[$i][$j],0,'.',',')."</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td>";
                            if (!isset($fn[$i][$j+1]))
                            $tabla .= "<td align=right ".$tamCol." id='st".$i."'>".number_format($stf[$i]['stf'],0,'.',',')."</td></tr>";
                            else
                            $tabla .= "<td ".$tamCol.">&nbsp</td></tr>";
                        }
                        ////////////////////////////////////////para los recibos
                        else if ($in[$i][$j]=='rc')
                        {
                            $stf[$i]['stf']=$arr[$i]['vf']-$vn[$i][$j];
                            $tabla .= "<tr class=".$wcf."><td ".$tamCol." align=center><input type='checkbox' id='wgrabar[".$i."]' name='wgrabar[".$i."]' onclick='cambiarEstado($i, \"off\");'/></td>";
                             //////////////////////////////////query pa traer los numeros obligatorios y ponerlos en un textbox
                           for ($l=1;$l<=$numco;$l++)
	            			{
			                	$query =  " SELECT ".$arr[$l]['co']."
								             FROM ".$wbasedato."_000101, ".$wbasedato."_000018
								             WHERE Fenhis=Inghis
								             AND Fening=Ingnin
								             AND Fenfac='".$arr[$i]['nf']."'
								             AND Fenest='on'";

					           $err = mysql_query($query,$conex);
				           	   $numdo = mysql_num_rows($err);
					            //echo mysql_errno() ."=". mysql_error();

				                $rowdo = mysql_fetch_array($err);
				               	$arr[$l][$i]=$rowdo[0];

				               	$tabla .= "<td ".$tamCol."><INPUT TYPE='text' NAME='arrn[".$l."][".$i."]' VALUE='".$arr[$l][$i]."' SIZE=13></td>";
	            			}

	            			$tabla .= "<td ".$tamCol.">".$arr[$i]['ffa']."</td><td ".$tamCol.">".$arr[$i]['nf']."</td><td align=right ".$tamCol.">".number_format($arr[$i]['vf'],0,'.',',')."</td><td ".$tamCol.">".$arr[$i]['nom']."</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">".$fn[$i][$j]."-".$nn[$i][$j]."</td><td align=right ".$tamCol.">".number_format($vn[$i][$j],0,'.',',')."</td>";
                            if (!isset($fn[$i][$j+1]))
                            $tabla .= "<td align=right ".$tamCol." id='st".$i."'>".number_format($stf[$i]['stf'],0,'.',',')."</td></tr>";
                            else
                            $tabla .= "<td ".$tamCol.">&nbsp</td></tr>";

                        }else/////////////////////////////////////para facturas sin notas ni recibos
                        {
                           $stf[$i]['stf']=$arr[$i]['vf'];
                            $tabla .= "<tr class=".$wcf."><td align='center' ".$tamCol."><input type='checkbox' id='wgrabar[".$i."]' name='wgrabar[".$i."]' onclick='cambiarEstado($i, \"off\");'></td>";
                            //////////////////////////////////query pa traer los numeros obligatorios y ponerlos en un textbox
                           for ($l=1;$l<=$numco;$l++)
	            			{
			                	$query =  " SELECT ".$arr[$l]['co']."
								              FROM ".$wbasedato."_000101, ".$wbasedato."_000018
								             WHERE Fenhis=Inghis
								               AND Fening=Ingnin
								               AND Fenfac='".$arr[$i]['nf']."'
								               AND Fenest='on'";

								$err = mysql_query($query,$conex);
								$numdo = mysql_num_rows($err);
								//echo mysql_errno() ."=". mysql_error();

				               	$rowdo = mysql_fetch_array($err);
				               	$arr[$l][$i]=$rowdo[0];

				               	$tabla .= "<td ".$tamCol."><INPUT TYPE='text' NAME='arrn[".$l."][".$i."]' VALUE='".$arr[$l][$i]."' SIZE=13></td>";
	            			}
                            $tabla .= "<td ".$tamCol.">".$arr[$i]['ffa']."</td><td ".$tamCol.">".$arr[$i]['nf']."</td><td align=right ".$tamCol.">".number_format($arr[$i]['vf'],0,'.',',')."</td><td ".$tamCol.">".stripAccents($arr[$i]['nom'])."</td><td ".$tamCol.">&nbsp</td><td align=right ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td>";
                            if (!isset($fn[$i][$j+1]))
                            $tabla .= "<td align=right ".$tamCol." id='st".$i."'>".number_format($stf[$i]['stf'],0,'.',',')."</td></tr>";
                            else
                            $tabla .= "<td ".$tamCol.">&nbsp</td></tr>";
                        }
                    }else
                    {
                        if ($in[$i][$j]=='nd')
                        {
                        	if ($numco>0){
                        					$stf[$i]['stf']=$stf[$i]['stf']+$vn[$i][$j];
				                            $tabla .= "<tr class=".$wcf.">";

				                            for ($c=1;$c<=$numco;$c++)
			            						{
			            							$tabla .= "<td ".$tamCol.">&nbsp</td>";
			            						}
				                            $tabla .= "<td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td align=right ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td align=right ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">".$fn[$i][$j]."-".$nn[$i][$j]."</td><td align=right ".$tamCol.">".number_format($vn[$i][$j],0,'.',',')."</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td>";
				                            if (!isset($fn[$i][$j+1]))
				                            $tabla .= "<td align=right ".$tamCol." id='st".$i."'>".number_format($stf[$i]['stf'],0,'.',',')."</td></tr>";
				                            else
				                            $tabla .= "<td ".$tamCol.">&nbsp</td></tr>";

                        	}else{

                            $stf[$i]['stf']=$stf[$i]['stf']+$vn[$i][$j];
                            $tabla .= "<tr class=".$wcf."><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td align=right ".$tamCol.">&nbsp</td><td align=right ".$tamCol.">&nbsp</td><td ".$tamCol.">".$fn[$i][$j]."-".$nn[$i][$j]."</td><td align=right ".$tamCol.">".number_format($vn[$i][$j],0,'.',',')."</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td>";
                            if (!isset($fn[$i][$j+1]))
                            $tabla .= "<td align=right ".$tamCol." id='st".$i."'>".number_format($stf[$i]['stf'],0,'.',',')."</td></tr>";
                            else
                            $tabla .= "<td ".$tamCol.">&nbsp</td></tr>";

                        	}

                        }else if ($in[$i][$j]=='nc')
                        {

                        	if ($numco>0){

                        		$stf[$i]['stf']=$stf[$i]['stf']-$vn[$i][$j];
	                            $tabla .= "<tr class=".$wcf.">";

	                            for ($c=1;$c<=$numco;$c++)
            						{
            							$tabla .= "<td ".$tamCol.">&nbsp</td>";
            						}
	                            $tabla .= "<td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td align=right ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">".$fn[$i][$j]."-".$nn[$i][$j]."</td><td align=right ".$tamCol.">".number_format($vn[$i][$j],0,'.',',')."</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td>";
	                            if (!isset($fn[$i][$j+1]))
	                            $tabla .= "<td align=right ".$tamCol." id='st".$i."'>".number_format($stf[$i]['stf'],0,'.',',')."</td></tr>";
	                            else
	                            $tabla .= "<td ".$tamCol.">&nbsp</td></tr>";

                        	}
                        	else{

	                        		$stf[$i]['stf']=$stf[$i]['stf']-$vn[$i][$j];
		                            $tabla .= "<tr class=".$wcf."><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td align=right ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">".$fn[$i][$j]."-".$nn[$i][$j]."</td><td align=right ".$tamCol.">".number_format($vn[$i][$j],0,'.',',')."</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td>";
		                            if (!isset($fn[$i][$j+1]))
		                            $tabla .= "<td align=right ".$tamCol." id='st".$i."'>".number_format($stf[$i]['stf'],0,'.',',')."</td></tr>";
		                            else
		                            $tabla .= "<td ".$tamCol.">&nbsp</td></tr>";
                        	}


                        }else if ($in[$i][$j]=='rc')
                        {

                        	if ($numco>0){

                        		$stf[$i]['stf']=$stf[$i]['stf']-$vn[$i][$j];
	                            $tabla .= "<tr class=".$wcf.">";

	                            for ($c=1;$c<=$numco;$c++)
            						{
            							$tabla .= "<td ".$tamCol.">&nbsp</td>";
            						}
	                            $tabla .= "<td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td align=right ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td align=right ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">".$fn[$i][$j]."-".$nn[$i][$j]."</td><td align=right ".$tamCol.">".number_format($vn[$i][$j],0,'.',',')."</td>";
	                            if (!isset($fn[$i][$j+1]))
	                            $tabla .= "<td align=right ".$tamCol." id='st".$i."'>".number_format($stf[$i]['stf'],0,'.',',')."</td></tr>";
	                            else
	                            $tabla .= "<td ".$tamCol.">&nbsp</td></tr>";

                        	}
                        	else{

	                        		$stf[$i]['stf']=$stf[$i]['stf']-$vn[$i][$j];
		                            $tabla .= "<tr class=".$wcf."><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td align=right ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">&nbsp</td><td ".$tamCol.">".$fn[$i][$j]."-".$nn[$i][$j]."</td><td align=right ".$tamCol.">".number_format($vn[$i][$j],0,'.',',')."</td>";
		                            if (!isset($fn[$i][$j+1]))
		                            $tabla .= "<td align=right ".$tamCol." id='st".$i."'>".number_format($stf[$i]['stf'],0,'.',',')."</td></tr>";
		                            else
		                            $tabla .= "<td ".$tamCol.">&nbsp</td></tr>";
                        	}


                        }

                    }
                    $ocultas[$i] .= "<input type='hidden' name='fn[".$i."][".$j."]' value='".$fn[$i][$j]."'>";
                    $ocultas[$i] .= "<input type='hidden' name='nn[".$i."][".$j."]' value='".$nn[$i][$j]."'>";
                    $ocultas[$i] .= "<input type='hidden' name='vn[".$i."][".$j."]' value='".$vn[$i][$j]."'>";
                    $ocultas[$i] .= "<input type='hidden' name='in[".$i."][".$j."]' value='".$in[$i][$j]."'>";
					if($ism>0)
					{
						$ocultas[$i] .= "<input type='hidden' id='facpantalla' name='facpantalla' value='".$facpantalla."'>";
					}
					$tabla 	.= "<tr style='display:none'><td>".$ocultas[$i]."</td></tr>";
                    //echo "<input type='hidden' name='stf[".$i."][stf]' value='".$stf[$i]['stf']."'>";
                    $j++;
                }

            }
			if($ism==0)
			{
				$tabla .= "</table></center>";
				$tabla .= "<input type='hidden' id='facpantalla' name='facpantalla' value='".$facpantalla."'>";
			}
				//$tabla .= "<div id='tbdetalle'></div>";

			$total=0;
			$totalreenv=0;

            if ($arr!=0)
            {
				if($ism==0)
				{
					$tabla .= "<center><table border='0' width='70%'>";
				   if ($numco>0) /////////////////////////////// si tiene campos obligatorios
					   {
						$csp=7+$numco;
						$csp1=12+$numco;

						$tabla .= "<tr class='encabezadoTabla' align=center><td colspan=7 style=width='60%' ><b>VALOR TOTAL DEL DOCUMENTO</td><td colspan=".$csp." width='60%' align=right id='total'>".number_format($total,0,'.',',')."</td></tr>";
						//$tabla .= "<tr class='encabezadoTabla' align=center ><td colspan=7 width='60%' ><b>VALOR REENVIADO DEL DOCUMENTO</td><td colspan=".$csp."  width='60%'  align=right id='totalreenv'>".number_format($totalreenv,0,'.',',')."</td></tr>";
						$tabla .= "<tr align=center><td colspan=".$csp1."><b>&nbsp</td></tr>";
						$tabla .= "<tr class='fila1' align=center ><td colspan=".$csp1."><b>OBSERVACION GENERAL DEL DOCUMENTO</td></tr>";
						if (!isset($obser)){
						$obser='';
						}
						$tabla .= "<tr align=center><td colspan=".$csp1." ><TEXTAREA name='obser' id='obser' COLS=100 ROWS=2 align=center>".$obser."</TEXTAREA></td></tr>";
						$tabla .= "<tr align=center><td align=center colspan=".$csp1."><input type='button' value='GRABAR' onclick='guardarCoj();'/></td></tr>";
						$tabla .= "<input type='hidden' name='total' value='".$total."'>";

					   }else{
								$tabla .= "<tr class='encabezadoTabla' align=center ><td colspan=7 width='60%' ><b>VALOR TOTAL DEL DOCUMENTO</td><td colspan=6  width=40%  align=right id='total'>".number_format($total,0,'.',',')."</td></tr>";
								//$tabla .= "<tr class='encabezadoTabla' align=center ><td colspan=7 width='60%' ><b>VALOR REENVIADO DEL DOCUMENTO</td><td colspan=6  width=40%  align=right id='totalreenv'>".number_format($totalreenv,0,'.',',')."</td></tr>";
								$tabla .= "<tr align=center ><td colspan=12><b>&nbsp</td></tr>";
								$tabla .= "<tr class='fila1' align=center ><td colspan=12><b>OBSERVACION GENERAL DEL DOCUMENTO</td></tr>";
								if (!isset($obser)){
									$obser='';
								}
								$tabla .= "<tr align=center><td colspan=12 ><textarea  name='obser' id='obser' COLS=100 ROWS=2 align=center>".$obser."</TEXTAREA></td></tr>";
								$tabla .= "<tr align=center><td align=center colspan=12><input type='button' value='GRABAR' onclick='guardarCoj();'/></td></tr>";
								$tabla .= "<input type='hidden' id='total' name='total' value='".$total."'>";
							}
					$tabla .= "</table></center>";
                }
            }
	$data=array('tabla'=>utf8_encode($tabla));
	echo json_encode($data);
	return;
}

if(isset($ajaxcdCoj)){




	$fue=explode('-',$fuente);
	$cod=explode('-',$responsable);
	$cc=explode('-',$suc);

	$query = "SELECT Renvca, Renfec, Rennum, Renest, fecha_data, hora_data
				FROM ".$wbasedato."_000020
				WHERE Renfue='".$fue[0]."'
				  AND Rennum='".$codoc."'
				  AND Rencod='".$cod[0]."'";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	$row3 = mysql_fetch_array($err);
	if($num==0)
	{
		echo "<p><font color='blue' size='5'>EL DOCUMENTO {$codoc} NO EXISTE</font></p>";
		return;
	}
	if ($row3[3]=='off')// muestra el letrero de documento anulado en envios
	{
		echo "<center><h1><b>DOCUMENTO ANULADO</b></h1></center>";
	}
	echo "<input type='hidden' id='fechaDoc' value='".$row3[4]."'>";
	echo "<input type='hidden' id='horaDoc' value='".$row3[5]."'>";
	echo "<table align='center' border=0>";
	echo "<tr><td class='encabezadoTabla' colspan=7 align=center><b>DETALLE DE LAS FACTURAS CONSULTADAS</b></td></tr>";
	echo "<tr><td class='fila2' colspan=2 align=center><b>DOCUMENTO Nro: ".$row3[2]."</b></td><td class='fila2' colspan=3 align=center><b>FECHA DEL Dcto: ".$row3[1]."</b></td></tr>";
	//echo "<tr class='encabezadoTabla'><td><b>Anular</td><td><b>Fuente de la Factura</td><td><b>Nro Factura</td><td><b>Nombre</td><td><b>Valor Factura</td></tr>";
	echo "<tr class='encabezadoTabla'><td><b>Fuente de la Factura</td><td><b>Nro Factura</td><td><b>Nombre</td><td><b>Valor Factura</td></tr>";
	$query =  " SELECT Rdefac, Rdevca, id, Rdeffa
							FROM ".$wbasedato."_000021
							WHERE Rdefue='".$fue[0]."'
							AND Rdenum='".$codoc."'
							ORDER BY id";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);

	echo "<input type='hidden' id='nan' value='".$num."'>";
	echo "<input type='hidden' id='checkadas' value=0>";
	$totan=0;
	for ($i=1;$i<=$num;$i++)
	{
		if (is_int ($i/2))
		   $wcf="fila1";  // color de fondo de la fila
		else
		   $wcf="fila2"; // color de fondo de la fila

		$row2 = mysql_fetch_array($err);
		$fan[$i]['nf']=$row2[0];
		$fan[$i]['ffa']=$row2[3];
		echo "<input type='hidden' id='fan[".$i."][nf]' value='".$fan[$i]['nf']."'>";
		echo "<input type='hidden' id='fan[".$i."][ffa]' value='".$fan[$i]['ffa']."'>";


		//2009-11-03
		$query =  " SELECT Fennpa
					  FROM ".$wbasedato."_000018, ".$wbasedato."_000021
					  WHERE Rdefac='".$fan[$i]['nf']."'
					  AND Rdefac=Fenfac
					  AND Rdeffa=Fenffa";
		$err1 = mysql_query($query,$conex);
		$row5 = mysql_fetch_array($err1);

		$nom=$row5[0];

		$query=" SELECT Fenfac, Fensal
							FROM ".$wbasedato."_000018
							WHERE Fenfac='".$fan[$i]['nf']."'
							AND Fenffa='".$fan[$i]['ffa']."'
							AND Fenest='on'";
		$err1 = mysql_query($query,$conex);

		$numfac = mysql_num_rows($err1);
		$rowfac = mysql_fetch_array($err1);
		$fa[$i]['sf']=$rowfac[1];
		echo "<input type='hidden' id='fa[".$i."][sf]' value='".$fa[$i]['sf']."'>";

		if ($rowfac[0]==$row2[0])
		$vbl="<input type='checkbox' id='anular[".$i."]' onclick='anularCoj(".$i.");'>";
		else
		$vbl="&nbsp";

		//echo "<tr class=".$wcf."><td>$vbl</td><td>".$row2[3]." </td><td>".$row2[0]." </td><td>".$nom."</td><td align=right>".number_format($row2[1],0,'.',',')."</td></tr>";
		echo "<tr class=".$wcf."><td>".$row2[3]."</td><td>".$row2[0]." </td><td>".$nom."</td><td align=right>".number_format($row2[1],0,'.',',')."</td></tr>";
		if (isset($anular[$i]))
		{
			$totan=$totan+$fa[$i]['sf'];
		}

	}
	if($row3[3]!='off')
	 $anular="<input type='checkbox' id='anularcoj' onclick='guardarAnulacionCoj();'>";
	 else
	  $anular="&nbsp";
	echo "<tr class='encabezadoTabla'><td colspan=2><b>VALOR TOTAL DEL DOCUMENTO</td><td colspan=3 id='totaldoc' align=right>".number_format($row3[0],0,'.',',')."</td></tr>";
	echo "<input type='hidden' id='totdc' value='".$row3[0]."'>";
	//echo "<tr class='encabezadoTabla'><td colspan=2><b>VALOR TOTAL DE LA ANULACION</td><td colspan=3 id='totalAnulado' align=right>".number_format($totan,0,'.',',')."</td></tr>";
	echo "<input type='hidden' id='totan' value='".$totan."'>";
	if($row3[3]!='off')
	echo "<tr class='encabezadoTabla'><td colspan=2><b>ANULAR  TODO EL DOCUMENTO</td><td colspan=3 align=right>".$anular."</td></tr>";

	echo "</table>";

	echo "<br>";
	$hyper="<A HREF='/matrix/ips/procesos/Imp_EnRaDe.php?empresa=".$wbasedato."&amp;cc=".$cc[0]."&amp;dcto=".$codoc."&amp;fuente=".$fuente."' target='new' class='vinculo' style='text-decoration:none; font-weight:bold'>Imprimir Documento</a><br><br>";
	echo "<center><table>";
	echo "<tr><td  colspan= '4' align=center>$hyper</td></tr>";
	echo "<tr><td></td></tr>";
	//	echo "<tr id='guardarAnulacion' style='display:none;'><td align='center'><input type='button' id='guarAnulRad' value='Anular' onclick='guardarAnulacionRad();'></td></tr>";
	echo "</table></center>";
	return;
}

if(isset($ajaxAnuCoj))//realiza los movimientos necesarios en las tablas para anular un cobro jurídico
{




  $cc=explode('-',$suc);
  $fue=explode('-',$fuente);
  $respo=explode('-', $responsable);
  $estadosFacturas = array();

  $aux = explode( ",", $fuentesCompletas );
  foreach( $aux as $i=>$datos){
  	$aux2 = explode("-", $datos);
  	$estadosFacturas[$aux2[0]] = $aux2[1];
  }

  function consultarEstadoAnterior( $numeroFactura, $fuenteFactura, $nitResponsable, $fecha_documento, $wnumro, $hora_documento, $fuenteDocumento ){
		global $conex, $wbasedato, $wemp_pmla;

		$query = "SELECT  fecha_data, hora_data, Rdefue, Rdenum
				    FROM {$wbasedato}_000021 a
				   WHERE Rdeffa = '{$fuenteFactura}'
				     AND Rdefac = '{$numeroFactura}'
				     AND ( (a.Fecha_data = '{$fecha_documento}' and a.hora_data < '{$hora_documento}') or ( a.Fecha_data < '{$fecha_documento}') )
				     AND CONCAT(Rdefue, '-',Rdenum ) != '{$fuenteDocumento}-{$wnumro}'
				     AND Rdesfa =''
				     AND Rdeest ='on'
				   GROUP by 1, 2, 3, 4
				   ORDER BY fecha_data DESC, hora_data DESC
				   LIMIT 1";
		$rs  = mysql_query( $query, $conex );
		$row = mysql_fetch_array( $rs );
		return( $row['Rdefue'] );
  }

  if (($anularCoj=='si') && ($ingreso==1))
  {

	$fue=explode('-',$fuente);

	$query= "UPDATE ".$wbasedato."_000021
							SET Rdeest='off'
							WHERE Rdefue='".$fue[0]."'
							AND Rdenum='".$codoc."'";
	$err = mysql_query($query,$conex);

	$query= "UPDATE ".$wbasedato."_000020
							SET Renest='off'
							WHERE Renfue='".$fue[0]."'
							AND Rennum='".$codoc."'";
	$err = mysql_query($query,$conex);

   }

    $estadoAnterior = consultarEstadoAnterior( $nfac, $ffa, trim($respo[1]), $fechaDocu, $codoc, $horaDocu, trim($fue[0]) );
    if( !isset($estadoAnterior) or (trim($estadoAnterior=="")) ){
    	$estadoAnterior = $fenv;
    }
    $estadoActualizado = $estadosFacturas[$estadoAnterior];
	$query= "UPDATE ".$wbasedato."_000018
				SET Fenesf='{$estadoActualizado}'
			  WHERE Fenfac='".$nfac."'
				AND Fenffa='".$ffa."'
				AND Fennit='".trim($respo[1])."'";
	$err = mysql_query($query,$conex);
	echo "<center><div><font size=3 color=blue><b> EL DOCUMENTO No: ".$codoc." HA SIDO ANULADO </b></font></div></center>";
	return;
}

?>
<html>
<head>
<meta http-equiv="Content-type" content="text/html;charset=ISO-8859-1">
<title>ENVIO, RADICACION Y DEVOLUCION</title>
</head>
<body >
<!--<script src="../../../include/root/jquery-latest.pack.js"></script>-->
<!--<script type="text/javascript" src="../../../include/root/jquery-1.3.2.min.js"></script>
<script type="text/javascript" src="../../../include/root/jquery.blockUI.min.js"></script>-->
<style type="text/css">
	.no-close .ui-dialog-titlebar-close {
	  display: none;
	}
</style>
<script src="../../../include/root/jquery_1_7_2/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<script src="../../../include/root/jquery.blockUI.min.js" type="text/javascript"></script>
<link rel="stylesheet" href="../../../include/root/jqueryui_1_9_2/cupertino/jquery-ui-cupertino.css" />
<script src="../../../include/root/jqueryui_1_9_2/jquery-ui.js" type="text/javascript"></script>
<script type="text/javascript">

function validar(e)
{
   var esIE=(document.all);
   var esNS=(document.layers);
   var tecla=(esIE) ? event.keyCode : e.which;
   if (tecla==13){
	setTimeout(function(){
	$(".calendar").hide();
	},20);
	return true;
   }
   else return false;
}

function formato_numero(numero, decimales, separador_decimal, separador_miles) // funcion que da formato al total en pantalla
{
    numero=parseFloat(numero);
    if(isNaN(numero)){
        return "";
    }

    if(decimales!==undefined){
        // Redondeamos
        numero=numero.toFixed(decimales);
    }

    // Convertimos el punto en separador_decimal
    numero=numero.toString().replace(".", separador_decimal!==undefined ? separador_decimal : ",");

    if(separador_miles){
        // Añadimos los separadores de miles
        var miles=new RegExp("(-?[0-9]+)([0-9]{3})");
        while(miles.test(numero)) {
            numero=numero.replace(miles, "$1" + separador_miles + "$2");
        }
    }

    return numero;
}

function reiniciar() //reinicia la aplicacion
{
	wemp_pmla=document.getElementById("wemp_pmla").value;
	location.href='EnRaDe.php?wemp_pmla='+wemp_pmla;
}

function generarOpciones(nf)//genera las opciones apropiadas para cada fuente ("ver, grabar, ingreso manual, ... etc").
{

	document.getElementById('filafec').style.display='none';
	var wemp_pmla= document.getElementById("wemp_pmla").value;
	var wbd= document.getElementById("wbd").value;
	var filao = document.getElementById("opciones");
	//var fo = document.getElementById("fop");
	var parametros="opciajax="+nf+"wemp_pmla="+wemp_pmla+"&wbasedato="+wbd+"&nfuente="+nf;
	var trres= document.getElementById('filares');
	try
	{
		var ajax = nuevoAjax();
		ajax.open("POST", "EnRaDe.php",true);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		ajax.onreadystatechange=function()
		{
			if (ajax.readyState==4)
			{
				filao.innerHTML=ajax.responseText;
				document.getElementById('tdopc').style.display='';
			}
		}
	}catch(e){	}
	if(nf!='85-GLOSAS')
	{
		trres.style.display='';
	}else
		{
				trres.style.display='none';
		}
}

function nuevaFuente(nf)//funcion javascript que genera el encabezado por cada fuente, especificamente busca el número del siguiente documento correspondiente al movimiento.
{
	if(nf!='na')
		$("#btnRetornar").show();
		else
		$("#btnRetornar").hide();
	var trfec = document.getElementById('fechas');
	var dvRes = document.getElementById('dvresponsable');
	dvRes.style.display='none';
	document.getElementById("divlist").innerHTML=''; //reseteo todo lo que haya en la tabla de resultados
	document.getElementById("pruebas").innerHTML='';
	document.getElementById("movimiento").value=nf;
	document.getElementById('buscEnvio').style.display='none';
	document.getElementById('buscFactura').style.display='none';
	document.getElementById('buscRadica').style.display='none';
	document.getElementById('tr_cargar_archivo').style.display='none';
	document.getElementById('mostrar_cargar_archivo').style.display='none';
	document.getElementById('generarPorBusqueda').style.display='none';
	document.getElementById('encabezadoIngresosManuales').style.display='none';
	document.getElementById('ingresos_glosar').value='0';
	document.getElementById('trbok').style.display='none';
	vaciar45(nf);

	//mostrar  menú de fechas;
	//buscar el número de documento correspondiente al tipo de movimiento.
	var wemp_pmla= document.getElementById("wemp_pmla").value;
	var wbd= document.getElementById("wbd").value;
	var parametros = "docajax=s&wemp_pmla="+wemp_pmla+"&wbasedato="+wbd+"&nfuente="+nf;
	var tddoc=document.getElementById("wdoc");
	try
	{
		var ajax = nuevoAjax();
		ajax.open("POST", "EnRaDe.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		tddoc.innerHTML=ajax.responseText;
		tddoc.style.display='';
		document.getElementById("filares").colSpan=4;
		if(document.getElementById("filafec").colSpan==3)
		{
			document.getElementById("filafec").colSpan=4;
		}

	}catch(e){}
	if(document.getElementById('fuenteAnt').value!=nf)
	{
		generarOpciones(nf);
		document.getElementById('fuenteAnt').value=nf;
	}
}

function porFechas()//función que habilita el menú para cuando se va a hacer una consulta por rango de fechas.
{
	//quitarChecks(id);
	buscarResponsable('rf');
	var mi=document.getElementById('menuIni');
	var dvRes = document.getElementById('dvresponsable');
	//var ge=document.getElementById('')
	var nf = document.getElementById('fuente').value;
	var trfec = document.getElementById('fechas');
	if(nf!='85-GLOSAS')// las glosas no se han por rango de fechas.
	{
		trfec.style.display='';
		dvRes.style.display='';
	}else
		{
			if(trfec.style.display!='none')
			{
				trfec.style.display='none';
			}
			dvRes.style.display='none';
		}
}

function ejecutarOpc(opc)//está función permite que en la pantalla se presenten los resultados visuales que son cookRangoFechas();nsecuencia de elegir una opción, reiniciando campos, ocultando y mostrando lo necesario
{
	var fue=document.getElementById('fue').value;
	var nfuente=document.getElementById('fuente').value;
	$("#menu").val( opc );
	switch(opc)
	{
	 case '00':
		alert('Por favor elija una opción');
		document.getElementById("dvresponsable").style.display='none';
		document.getElementById("fbusFue").value='';
		document.getElementById("fbusPref").value='';
		document.getElementById("fbusNum").value='';
		document.getElementById("envbus").value='';
		document.getElementById("pruebas").innerHTML='';
		document.getElementById("divlist").style.display='none';
		document.getElementById("filafec").style.display='';
		document.getElementById("generarPorBusqueda").style.display='none';
		document.getElementById("encabezadoIngresosManuales").style.display='none';
		document.getElementById("buscFactura").style.display='none';
		document.getElementById("buscEnvio").style.display='none';
		document.getElementById("buscRadica").style.display='none';
		document.getElementById("trbok").style.display='none';
		document.getElementById('tr_cargar_archivo').style.display='none';
		document.getElementById('mostrar_cargar_archivo').style.display='none';
		document.getElementById("fbusFue").value='';
		document.getElementById("fbusPref").value='';
		document.getElementById("fbusNum").value='';
		document.getElementById('ism').value='0';
		document.getElementById('ingresos_glosar').value='0';
		break;
	 case '01':
		document.getElementById("divlist").style.display='none';
		document.getElementById("filafec").style.display='';
		document.getElementById("envbus").value='';
		document.getElementById('ism').value='0';
		document.getElementById('ingresos_glosar').value='0';
		document.getElementById("generarPorBusqueda").style.display='none';
		document.getElementById("encabezadoIngresosManuales").style.display='none';
		document.getElementById("buscFactura").style.display='none';
		document.getElementById("buscEnvio").style.display='none';
		document.getElementById("buscRadica").style.display='none';
		document.getElementById('tr_cargar_archivo').style.display='none';
		document.getElementById('mostrar_cargar_archivo').style.display='none';
		//document.getElementById("bok").style.display='';
		document.getElementById("fbusFue").value='';
		document.getElementById("fbusPref").value='';
		document.getElementById("fbusNum").value='';
		nuevaFuente(nfuente);
		document.getElementById("trbok").style.display='';
		document.getElementById("bok").style.display='';
		document.getElementById("pruebas").innerHTML='';
		porFechas();
		break;
	 case '02':
		nuevaFuente(nfuente);
		document.getElementById("divlist").style.display='none';
		document.getElementById("generarPorBusqueda").style.display='';
		document.getElementById("encabezadoIngresosManuales").style.display='';
		document.getElementById("trbok").style.display='none';
		document.getElementById("pruebas").innerHTML='';
		ingresoManual();
		break;
	 case '03':
		document.getElementById("divlist").style.display='none';
		document.getElementById("dvresponsable").style.display='none';
		document.getElementById("trbok").style.display='none';
		document.getElementById("fbusFue").value='';
		document.getElementById("fbusPref").value='';
		document.getElementById("fbusNum").value='';
		document.getElementById("envbus").value='';
		document.getElementById("generarPorBusqueda").style.display='none';
		document.getElementById("encabezadoIngresosManuales").style.display='none';
		document.getElementById("buscFactura").style.display='none';
		document.getElementById("buscEnvio").style.display='none';
		document.getElementById("buscRadica").style.display='none';
		document.getElementById('tr_cargar_archivo').style.display='none';
		document.getElementById('mostrar_cargar_archivo').style.display='none';
		document.getElementById('ism').value='0';
		document.getElementById('ingresos_glosar').value='0';
		document.getElementById("pruebas").innerHTML='';
		pedirDocumento(fue);
		break;
	}
}

function okRangoFechas()//dependiendo del valor actual de movimiento ejecutan listar envios, radicacion o glosas
{
	var fenv=document.getElementById('fenv').value;
	var frad=document.getElementById('frad').value;
	var fdev=document.getElementById('fdev').value;
	var fglo=document.getElementById('fglo').value;
	var fcoj=document.getElementById('fcoj').value;
	var movimiento=document.getElementById('movimiento').value;

	switch(movimiento)
	{
		case fenv:
			listarEnv(" ");
			break;
		case frad:
			ListarRad('fechas');
			break;
		case fdev:
			ListarDev('fechas');
			break;
		case fglo:
			alert('glosas');
			break;
		case fcoj:
			listarCoj(" ");
			break;
	}
}

function actualizarTotal(i) //funcion que actualiza el total en pantalla a medida que se seleccionan las facturas a ser enviadas.
{
	var totpan = document.getElementById('total');
	var totpan2 = document.getElementById('total2');
	var totActual = totpan.innerHTML;
	totActual=totActual.replace(/,/g, "")*1;
	var nvalpan = document.getElementById('st'+i);
	var nval = nvalpan.innerHTML;
	nval=nval.replace(/,/g, "")*1;

	if(document.getElementById('wgrabar['+i+']').checked==false)
	{
		var ntotal= totActual-nval;
	}else
		{
			var ntotal= totActual+nval;
		}
	ntotal=formato_numero(ntotal,0,"",",")
	totpan.innerHTML=ntotal;
	if(totpan2!=null)
	{
		totpan2.innerHTML=ntotal;
	}
}

function consultarDocumento(tipo)//función que pide la información del envio consultado, para que muestre tanto la información como la posibilidad de anular.
{
	if($("#codoc").val()=='')
		{
			if(tipo=='key')
			alert('Por favor ingrese un número de documento');
			return;
		}
	//variables para decisión de busqueda
	fenv=document.getElementById('fenv').value;
	frad=document.getElementById('frad').value;
	fdev=document.getElementById('fdev').value;
	fglo=document.getElementById('fglo').value;
	fcoj=document.getElementById('fcoj').value;
	document.getElementById("dvresponsable").style.display='none';
	buscarResponsable('cd');
	document.getElementById('fechas').style.display='none';
	/*var dl=document.getElementById('divlist');
	dl.style.display='none'; //reseteo todo lo que haya en la tabla de resultados*/
	var fuente = document.getElementById('fuente').value; // esta es la fuente del documento
	$("#divlist").html('');
	var wemp_pmla= document.getElementById('wemp_pmla').value;
	var wbd= document.getElementById('wbd').value;
	var doc = document.getElementById('codoc').value;
	var wres= document.getElementById('responsable').value;
	if(fuente==fenv)
	  parametros="ajaxcd=cd&codoc="+doc+"&fuente="+fuente+"&wemp_pmla="+wemp_pmla+"&wbasedato="+wbd+"&responsable="+wres;
	if(fuente==frad)
	  parametros="ajaxcdRad=cd&codoc="+doc+"&fuente="+fuente+"&wemp_pmla="+wemp_pmla+"&wbasedato="+wbd+"&responsable="+wres;
	if(fuente==fdev)
		parametros="ajaxcdDev=cd&codoc="+doc+"&fuente="+fuente+"&wemp_pmla="+wemp_pmla+"&wbasedato="+wbd+"&responsable="+wres;
	if(fuente==fglo)
		parametros="ajaxcdGlo=cd&codoc="+doc+"&fuente="+fuente+"&wemp_pmla="+wemp_pmla+"&wbasedato="+wbd+"&responsable="+wres;
	if(fuente==fcoj)
		parametros="ajaxcdCoj=cd&codoc="+doc+"&fuente="+fuente+"&wemp_pmla="+wemp_pmla+"&wbasedato="+wbd+"&responsable="+wres;

	var ajax = nuevoAjax();
	ajax.open("POST", "EnRaDe.php",false);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send(parametros);
	/*ajax.onreadystatechange=function()
	{
		if (ajax.readyState==4)
		{*/
		    $("#divlist").show();
			rs=ajax.responseText;
			$("#divlist").html(rs);
		/*}
	}*/
}

function agregarFactura()//activa las funciones que agregan facturas en envios o en glosas de manera manual.
{
	var fuenteActual = $('#fuente').val();
	var fenv = $('#fenv').val();
	var fglo = $('#fglo').val();
	switch(fuenteActual)
	{
		case fenv:
			listarEnv('add');
			break;
		case fglo:
			listarGlo('addGlo');
			break;
	}
}

function vaciar45(nf)//elimina todo lo que hay en la 45 pendiente escepto las glosas(para el usuario que está logueado)
{
	//alert('si');
	if(nf!='85-GLOSAS')
	{
		wemp = $("#wbd").val();
		user = $("#usuario").val();
		fglo = $("#fglo").val();
		$.ajax
		({
			type:	'POST',
			url:	'EnRaDe.php',
			data: 	{ vaciar45: 'si', wbasedato: wemp, fglo: fglo, usuario: user }

		});
	}
}

////FUNCIONES JS PARA LOS ENVIOS.
function quitarChecks(id)//cuando se selecciona una opción se quitan las que estén seleccionadas.
{
	var opcs = document.getElementById('nopc').value;
	for(var i=1; i<=opcs; i++)
	{
		if("op"+i != id)
		document.getElementById("op"+i).checked=false;
	}
}

function listarEnv(id)// funcion ajax que busca las facturas que se pueden mover.
{
	var wemp= document.getElementById("wemp_pmla").value;
	var wbd= document.getElementById("wbd").value;
	var dl=document.getElementById("divlist");
	var fr = document.getElementById("filares");
	var fuente = document.getElementById("fenv").value;
	var ing=false;
	var continuar=true;
	var rs;

	if(document.getElementById("menu").value=='01')//esto significa que la busqueda se realiza por rango de fechas
	{
		var wfecI= document.getElementById("fechaini").value;
		var wfecF= document.getElementById("fechafin").value;
		var wres= document.getElementById('responsable').value;

		if(wfecI=="" || wfecF=="")
		{
			alert("Rango de fechas no válido");
			return;
		}

		if(wfecI>wfecF)
		{
			alert("La fecha inicial debe ser inferior a la fecha final");
			return;
		}
	}else //en caso de que se haya hecho un ingreso manual.
		{
			ing=true;
			var nufac = document.getElementById('fbusNum').value;
			var prfac = document.getElementById('fbusPref').value;
			var ffue = document.getElementById('fbusFue').value;
			var ism = document.getElementById('ism').value*1;
			if(nufac=="" || prfac=="" || ffue=="")
			{
				alert("Datos incorrectos");
				return;
			}
			else{
					nfac=prfac+"-"+nufac;
					continuar=verificarRepetido(nfac, ffue);
					if(continuar){
						//buscarResponsable("rf");
						var wres= document.getElementById('responsable').value;
						document.getElementById("dvresponsable").style.display='';
					}else
					{
						alert('La factura ya ha sido agregada');
						return;
					}

				}
		}

		if(!ing)//si no es un ingreso manual se hace la siguiente llamada ajax
		{
			$.post
			(
				"EnRaDe.php",
				{
					ajaxEnvT: 	'todo',
					fec1: 		wfecI,
					wemp_pmla:	wemp,
					fec2:		wfecF,
					wbasedato: 	wbd,
					responsable:	wres,
					fuente:		fuente
				},
				function(data)
				{
					if(data.tabla!='')
					{
						$("#divlist").html(data.tabla );
						document.getElementById('trbok').style.display='none';
						document.getElementById('divlist').style.display='';
					}else
						 {
							alert("NO EXISTEN FACTURAS PARA SER ENVIADAS CON ESAS CONDICIONES");
						 }
				},
				"json"
			);
		}else
			{
				$.post
				(
					"EnRaDe.php",
					{
						ajaxEnvT:		'im',
						ism:			ism,
						wemp_pmla:		wemp,
						wbasedato:  	wbd,
						nfac: 			nfac,
						fue:			ffue,
						fuente:			fuente,
						responsable:	wres
					},
					function(data)
					{
						if(ism>0)
						{
							if(data.tabla!='')
							{
								document.getElementById('ism').value = ism+1;
								$("#facpantalla").remove();
								$("#encabezado").after(data.tabla);
								check = document.getElementById('wgrabar['+document.getElementById('ism').value+']');
								check.click();
							}else
							 {
								alert('NO SE ENCONTRARON FACTURAS CON DICHOS PARÁMETROS');
							 }
						}else
							{
								if(data.tabla!='')
									{
										document.getElementById('ism').value = ism+1;
										$("#divlist").append(data.tabla);
										$("#todas").attr('checked','true');
										check = document.getElementById('wgrabar['+document.getElementById('ism').value+']');
										check.click();
									}
									else
									 {
										alert('NO EXISTEN FACTURAS PARA SER ENVIADAS CON ESAS CONDICIONES');
									 }
							}
						document.getElementById('fbusNum').value='';
						if(id!='add')
						{
							document.getElementById(id).checked=false;
						}
					},
					"json"
				);
			}
	document.getElementById('divlist').style.display='';
}

function buscarResponsable(bus)// busca el responsable o el conjunto de responsables por tipo de fuente.
{
	var wemp_pmla= document.getElementById("wemp_pmla").value;
	var wbd= document.getElementById("wbd").value;
	var rs=document.getElementById("dvresponsable");
	var fuente = document.getElementById('fuente').value;
	if(bus=='rf')
	{
		var parametros = "ajaxBusc=rf&wemp_pmla="+wemp_pmla+"&wbasedato="+wbd;
	}
	if(bus=='cd')
	{
		if(document.getElementById("generarPorBusqueda").style.display=='none')//se está consultando un número de documento
		{
			var doc = document.getElementById('codoc').value;
		}else // se está generando un documento apartir de uno anterior.
			{
				var movimiento = document.getElementById('fuente').value;
				var fenv = document.getElementById('fenv').value;
				var fdev = document.getElementById('fdev').value;
				var frad = document.getElementById('frad').value;
				switch (movimiento)
				{
					case frad:
					var fuente = fenv;
					var doc = document.getElementById('envbus').value;
					break;
					case fdev:
					var fuente = frad;
					var doc = document.getElementById('radbus').value;
					break;
				}


			}
		var parametros = "ajaxBusc=cd&wemp_pmla="+wemp_pmla+"&wbasedato="+wbd+"&codoc="+doc+"&fuente="+fuente;
	}else
		{
			if(bus=='im')//si se hizo un ingreso manual
			{
				//acá se busca el responsable del ingreso manual....ARMAR LA FACTURA.
				var nufac = document.getElementById('fbusNum').value;
				var prfac = document.getElementById('fbusPref').value;
				var ffue = document.getElementById('fbusFue').value;
				var fact = prfac+"-"+nufac;
				var parametros = "ajaxBusc=im&wemp_pmla="+wemp_pmla+"&wbasedato="+wbd+"&fact="+fact+"&fue="+ffue;
			}else
				{
					if(bus!='rf')
					{
						var wparte= document.getElementById("busc").value;
						var parametros = "ajaxBusc=nm&wemp_pmla="+wemp_pmla+"&wbasedato="+wbd+"&busc="+wparte;
					}
				}
		}
		var ajax = nuevoAjax();
		ajax.open("POST", "EnRaDe.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		rs.innerHTML=ajax.responseText;
		rs.style.display='';
		fglo = document.getElementById('fglo').value;
		if(fuente==fglo && bus=='nm')
		{
			$("#divlist").html('');
			$("#ingresos_glosar").val('0');
			pintar45('si','n');
		}

}

function gestionarRespuestaGlosas( fuenteFactura, numfac, numeroGlosa, porcentajeAceptado, respuestaGlosa, accion ){

	var wemp_pmla = document.getElementById("wemp_pmla").value;

	//if( (porcentajeAceptado*1 > 0 && respuestaGlosa == "") || (porcentajeAceptado*1 == 0 && respuestaGlosa != "") ){

		$.ajax({
            url     : "EnRaDe.php",
            type    : "POST",
            async   : true,
            data    : {
							guardarRespuestaGlosa: "guardarRespuestaGlosa",
							wemp_pmla            : $("#wemp_pmla").val(),
							fuenteFactura        : fuenteFactura,
							numfac               : numfac,
							numeroGlosa          : numeroGlosa,
							PorcentajeAceptado   : porcentajeAceptado,
							wbasedato            : $("#wbd").val(),
							accion               : accion,
							respuestaSeleccionada: respuestaGlosa
                      },
            success : function(data){
            	$("input[name$='[estadoFactura]'][value='GL'][numeroDeGlosa='"+numeroGlosa+"'][fuenteFactura='"+fuenteFactura+"'][numeroFactura='"+numfac+"']").attr("respuestaglosa", data );
            	nuevaFila  = $("tr[name='fila_respuesta_glosa'][tipo='ppal']");
				$(nuevaFila).attr("numeroDeGlosa", "" );
				$(nuevaFila).attr("fue", "" );
				$(nuevaFila).attr("numfac", "" );
				$(nuevaFila).attr("porcentajeAceptado", "" );
				$(nuevaFila).attr("respuesta_seleccionada", "" );
				$(nuevaFila).attr("tipo", "ppal" );
				$(nuevaFila).find("td").eq(0).html( "" );
				$(nuevaFila).find("td").eq(1).html( "" );
				$(nuevaFila).find("td").eq(2).html( "" );
				$("tr[name='fila_respuesta_glosa'][tipo='sec']").remove();
				$("tr[name='fila_respuesta_glosa'][tipo='ppal']").find("select > option[value='']").attr("selected", "selected");
            }
        });


	//}
}

function cambiarEstado(i, masivo)//esta funcion agrega o quita un registro de la tabla temporal cuando se le dá check.
{
	actualizarTotal(i);
	var fuente    = document.getElementById('fue').value; // esta es la fuente del documento
	var wemp_pmla = document.getElementById("wemp_pmla").value;
	var usua      = document.getElementById("usuario").value;
	var wbd       = document.getElementById("wbd").value;
	var doc       = document.getElementById('dcto').value;
	var suc       = document.getElementById('suc').value;
	var caja      = document.getElementById('caja').value;
	var fila      = document.getElementById('wgrabar['+i+']');
	var numfac    = document.getElementById('arr['+i+'][nf]').value;
	var fue       = document.getElementById('arr['+i+'][ffa]').value; //esta es la fuente de la factura
	var fenv      = document.getElementById('fenv').value;
	var estadoFac = $("[name='arr["+i+"][estadoFactura]']").val();
	var respuestaGlosa = $("[name='arr["+i+"][estadoFactura]']").attr("respuestaGlosa");

	var codigoEnvio = fenv.split( "-" )
	codigoEnvio = $.trim( codigoEnvio[0] );

	if( fuente == codigoEnvio)
		var esEnvio = 'on';
		else
			var esEnvio = 'off';

	try {var wres= document.getElementById('responsable').options[document.getElementById('responsable').selectedIndex].text;}catch(e){}
	if(wres==null)
		var wres= $("#responsable").val();
	var ings=document.getElementById('ings').value*1;
	if(fila.checked==false)
		{
			var parametros = "ajaxRegTem=rm&numfac="+numfac+"&wemp_pmla="+wemp_pmla+"&fuente="+fuente+"&wbasedato="+wbd+"&responsable="+wres+"&dcto="+doc+"&suc="+suc+"&caja="+caja+"&fue="+fue+"&user="+usua;
			ings=ings-1;
			document.getElementById('ings').value=ings;
			document.getElementById('todas').checked=false
			var numeroDeGlosa      = $("[name='arr["+i+"][estadoFactura]']").attr("numeroDeGlosa");
			var porcentajeAceptado = $("[name='arr["+i+"][estadoFactura]']").attr("porcentajeAceptado");
			if( estadoFac == "GL" && respuestaGlosa !="" && fuente == codigoEnvio){

				gestionarRespuestaGlosas( fue, numfac, numeroDeGlosa, porcentajeAceptado, respuestaGlosa, "rm" );

			}
		}else
		 {

			var parametros = "ajaxRegTem=add&numfac="+numfac+"&wemp_pmla="+wemp_pmla+"&fuente="+fuente+"&wbasedato="+wbd+"&responsable="+wres+"&dcto="+doc+"&suc="+suc+"&caja="+caja+"&fue="+fue+"&user="+usua+"&numero_factura_pantalla="+i+"&esEnvio="+esEnvio;
			ings=ings+1;
			document.getElementById('ings').value=ings;
		 }

		var ajax = nuevoAjax();
		ajax.open("POST", "EnRaDe.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);

		document.getElementById('pruebas').innerHTML+=ajax.responseText;//acá tengo el valor reenviado
		actualizarTotalReenviado(i);
		if( estadoFac == "GL" && fila.checked==true && respuestaGlosa=="" && fuente == codigoEnvio){

			var numeroDeGlosa      = $("[name='arr["+i+"][estadoFactura]']").attr("numeroDeGlosa");
			var porcentajeAceptado = $("[name='arr["+i+"][estadoFactura]']").attr("porcentajeAceptado");
			var respuestaGlosa     = $("[name='arr["+i+"][estadoFactura]']").attr("respuestaGlosa");

			if( porcentajeAceptado*1 == 100 || porcentajeAceptado*1 == 90 ){

		 		if( numeroDeGlosa != "" ){
		 			gestionarRespuestaGlosas( fue, numfac, numeroDeGlosa, porcentajeAceptado, respuestaGlosa, "add" );
		 		}
			}

			if( porcentajeAceptado == 0 ){
				cantGlosas = $("tr[name='fila_respuesta_glosa'][tipo='ppal'][numeroDeGlosa!='']").length;
				if( cantGlosas > 0 ){
					nuevaFila  = $("tr[name='fila_respuesta_glosa'][tipo='ppal']").clone();
					$(nuevaFila).attr("numeroDeGlosa", numeroDeGlosa );
					$(nuevaFila).attr("fue", fue );
					$(nuevaFila).attr("numfac", numfac );
					$(nuevaFila).attr("porcentajeAceptado", porcentajeAceptado );
					$(nuevaFila).attr("respuesta_seleccionada", "" );
					$(nuevaFila).attr("tipo", "sec" );
					$(nuevaFila).find("td").eq(0).html( numeroDeGlosa );
					$(nuevaFila).find("td").eq(1).html( fue );
					$(nuevaFila).find("td").eq(2).html( numfac );
					$("#tbl_respuestas_glosas").append( nuevaFila );

				}else{
					nuevaFila  = $("tr[name='fila_respuesta_glosa'][tipo='ppal']");
					$(nuevaFila).attr("numeroDeGlosa", numeroDeGlosa );
					$(nuevaFila).attr("fue", fue );
					$(nuevaFila).attr("numfac", numfac );
					$(nuevaFila).attr("porcentajeAceptado", porcentajeAceptado );
					$(nuevaFila).attr("respuesta_seleccionada", "" );
					$(nuevaFila).attr("tipo", "ppal" );
					$(nuevaFila).find("td").eq(0).html( numeroDeGlosa );
					$(nuevaFila).find("td").eq(1).html( fue );
					$(nuevaFila).find("td").eq(2).html( numfac );
				}


				if( masivo == "off" ){
					cantGlosas = $("tr[name='fila_respuesta_glosa'][tipo='ppal'][glosa!='']").length;
					if(cantGlosas > 0){
						$("#div_respuestas_glosas").dialog({
							dialogClass: "no-close",
							modal	: true,
							width	: 'auto',
							title	: "<div align='center'>Respuesta Glosas No Aceptadas. </div>",
							show	: { effect: "slide", duration: 600 },
							hide	: { effect: "fold", duration: 600 },
							close: function( event, ui ) {
								$("tr[name='fila_respuesta_glosa']").each(function(index, el) {
									fue                    = $(this).attr("fue");
									numfac                 = $(this).attr("numfac");
									numeroDeGlosa          = $(this).attr("numeroDeGlosa");
									porcentajeAceptado     = $(this).attr("porcentajeAceptado");
									respuesta_seleccionada = $(this).find("select > option:selected").val();
									gestionarRespuestaGlosas( fue, numfac, numeroDeGlosa, porcentajeAceptado, respuesta_seleccionada, "add" );

								});
							}
						});
					}
				}
			}

	 	}else{
	 		console.log( "ya tiene una respuesta a la glosa" );
	 	}
}

function agregarRespuestaGlosa(){

	cerrar = true;
	$("select[name='slc_respuestas_glosas']").each(function(){
		res = $(this).find("option:selected").val();
		if( res == "" ){
			$(this).parent().parent().addClass('fondoAmarillo');
			cerrar = false;
			return false;
		}
	});
	if(cerrar){
		$("#div_respuestas_glosas").dialog("close");
	}
}

function seleccionarRespuestaGlosa( select ){
	var seleccionado = $(select).find("option:selected").val();
	if( seleccionado != "" ){
		$(select).parent().parent().removeClass('fondoAmarillo');
	}else{
		$(select).parent().parent().addClass('fondoAmarillo');
	}
}

function ingresoManual()//hace los cambios visuales necesarios para que se pueda realizar un ingreso manual.
{
  //quitarChecks(id);

  fueActual = document.getElementById("fuente").value;
  fueEnv    = document.getElementById("fenv").value;
  fueRad    = document.getElementById("frad").value;
  fueDev    = document.getElementById("fdev").value;
  fueGlo    = document.getElementById("fglo").value;
  fueCoj    = document.getElementById("fcoj").value;

  document.getElementById("divlist").style.diplay='none'; //reseteo todo lo que haya en la tabla de resultados
  document.getElementById("fechas").style.display='none';
  document.getElementById("dvresponsable").style.display='none';

 if(fueActual==fueEnv)
  {
	document.getElementById("buscFactura").style.display='';
	document.getElementById("buscFactura").colspan=2;
	document.getElementById("addGlo").style.display='none';
	document.getElementById("add").style.display='';
	buscarResponsable('rf');
	document.getElementById("dvresponsable").style.display='';
  }
  if(fueActual==fueRad)
  {
	document.getElementById("buscEnvio").style.display='';
	document.getElementById("buscEnvio").colspan=2;
  }

  if(fueActual==fueDev)
  {
	document.getElementById("buscRadica").style.display='';
	document.getElementById("buscRadica").colspan=2;
  }
  if(fueActual==fueGlo)
  {
	document.getElementById("buscFactura").style.display='';
	document.getElementById("buscFactura").colspan=2;
	document.getElementById("add").style.display='none';
	document.getElementById("addGlo").style.display='';
	document.getElementById('mostrar_cargar_archivo').style.display='';
	buscarResponsable('rf');
	document.getElementById("dvresponsable").style.display='';
	$("#buscFactura").attr('class','fila2');

	listarGlo('up45');
  }
  if(fueActual==fueCoj)
  {
	document.getElementById("buscFactura").style.display='';
	document.getElementById("buscFactura").colspan=2;
	document.getElementById("addGlo").style.display='none';
	document.getElementById("add").style.display='';
	buscarResponsable('rf');
	document.getElementById("dvresponsable").style.display='';
  }
  //document.getElementById("envOpc").colspan=2;
}

function cambiarTodasFin(mov){
	if(document.getElementById('ism').value*1==0)
		var filas = document.getElementById('facpantalla').value*1
	else
	    var filas = document.getElementById('ism').value*1
	var todas = document.getElementById('todas');
	var fenv = document.getElementById('fenv').value;
	var frad = document.getElementById('frad').value;
	var fdev = document.getElementById('fdev').value;
	var fglo = document.getElementById('fglo').value;
	var fcoj = document.getElementById('fcoj').value;

	for(var i=1; i<=filas; i++)
	{

	if(document.getElementById('wgrabar['+i+']').checked!=true)
		{
			if(todas.checked==true)
			{
				document.getElementById('wgrabar['+i+']').checked=true;
				if(mov==fenv)
				cambiarEstado(i, "on");
				if(mov==frad)
				cambiarEstadoRad(i);
				if(mov==fdev)
				cambiarEstadoDev(i);
				if(mov==fcoj)
				cambiarEstado(i, "on");
			}
		}else//si no está checkada la fila
			{
				if(todas.checked==false)
				{
					document.getElementById('wgrabar['+i+']').checked=false;
					if(mov==fenv)
					cambiarEstado(i, "on");
					if(mov==frad)
					cambiarEstadoRad(i);
					if(mov==fdev)
					cambiarEstadoDev(i);
					if(mov==fcoj)
					cambiarEstado(i, "on");
				}
			}
	}

	$.unblockUI();
	var codigoEnvio = fenv.split( "-" )
	codigoEnvio = $.trim( codigoEnvio[0] );
	if( todas.checked==true && mov == fenv ){
		cantGlosas = $("tr[name='fila_respuesta_glosa'][tipo='ppal'][glosa!='']").length;
		if(cantGlosas > 0){
			$("#div_respuestas_glosas").dialog({
				dialogClass: "no-close",
				modal	: true,
				width	: 'auto',
				title	: "<div align='center'>Respuesta Glosas No Aceptadas. </div>",
				show	: { effect: "slide", duration: 600 },
				hide	: { effect: "fold", duration: 600 },
				close: function( event, ui ) {
					$("tr[name='fila_respuesta_glosa']").each(function(index, el) {
						fue                    = $(this).attr("fue");
						numfac                 = $(this).attr("numfac");
						numeroDeGlosa          = $(this).attr("numeroDeGlosa");
						porcentajeAceptado     = $(this).attr("porcentajeAceptado");
						respuesta_seleccionada = $(this).find("select > option:selected").val();
						gestionarRespuestaGlosas( fue, numfac, numeroDeGlosa, porcentajeAceptado, respuesta_seleccionada, "add" );

					});
				}
			});
		}
	}
}

function cambiarTodas(mov)// selecciona o quita TODAS los registros que pueden ser movidos que están en pantalla.
{
	$.blockUI({ message: $('#msjEspere')});
	setTimeout(function(){ cambiarTodasFin(mov) }, 500);
}

function pedirDocumento(fuente)//pide el documento correspondiente al tipo de movimiento que se va a realizar.
{
	//quitarChecks(id);
	var indoc= '<td align=left class="fila2"><b>Documento Nro: <br></b><input name="codoc" id="codoc" type="text"  onkeypress="if (validar(event)) consultarDocumento(\'key\');"/>';
	var tddoc=document.getElementById("wdoc");
	tddoc.innerHTML=indoc;
	document.getElementById("fechas").style.display='none';
	document.getElementById("divlist").innerHTML=''; //reseteo todo lo que haya en la tabla de resultados
}

function guardarEnv()//funcion que basea lo que hay en la tabla temporal, el parámetro tipo(agregarlo) indica si se va a guardar un envio o una radicación, etc...
{
	var ings=document.getElementById('ings')*1;
	var totpan=document.getElementById('total');
	var totActual = totpan.innerHTML;
	totActual=totActual.replace(/,/g, "")*1;
	if(ings==0 || totActual==0)
	{
		alert('No hay datos pendientes para guardar');
	}else
	  {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////variables
		var wemp_pmla= document.getElementById("wemp_pmla").value;
		var wbd= document.getElementById("wbd").value;
		var fuente = document.getElementById('fue').value; // esta es la fuente del documento
		var doc = document.getElementById('dcto').value;
		var suc = document.getElementById('suc').value;
		var caja = document.getElementById('caja').value;
		var nuevoEstado = $("input[tipo='clasesCompletas'][numeroFuente='"+fuente+"']").attr("estadoEquivalente");
		console.log(nuevoEstado);
		try {var wres= document.getElementById('responsable').options[document.getElementById('responsable').selectedIndex].text;}catch(e){}
		if(wres==null)
		var wres= $("#responsable").val();
		var obser= document.getElementById("obser").value;


		///////////////////////////////////////////////////////////////////////////////////////////////////////////////fin variables
		var parametros="ajaxGuard=o&wemp_pmla="+wemp_pmla+"&fuente="+fuente+"&wbasedato="+wbd+"&responsable="+wres+"&dcto="+doc+"&suc="+suc+"&caja="+caja+"&totalm="+totActual+"&obser="+obser+"&nuevoEstado="+nuevoEstado;
		try
		{
			try
			{
				$.blockUI({ message: $('#msjEspere') });
			} catch(e){ }
			var ajax = nuevoAjax();
			ajax.open("POST", "EnRaDe.php",true);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

			ajax.onreadystatechange=function()
			{
				if (ajax.readyState==4)
				{
					if(ajax.responseText == "error"){
						alerta( "El documento no pudo ser generado, verifique el estado de las facturas" );
					}else{
						var a=document.getElementById('divlist');
						a.innerHTML=ajax.responseText;
						$.unblockUI();
					}
				}try{

					} catch(e){ }
			}
		}catch(e){	}
		//quitarChecks(id);

	  }

}

function anularEnvio(id, tipo, filas)//funcion que permite anular facturas individuales.
{
	var fuente = document.getElementById('fuente').value; // esta es la fuente del documento
	var wemp_pmla = document.getElementById('wemp_pmla').value;
	var wbd = document.getElementById('wbd').value;
	var doc = document.getElementById('codoc').value;
	var wres = document.getElementById('responsable').value;
	var ffa = document.getElementById('fan['+id+'][ffa]').value;
	var fac = document.getElementById('fan['+id+'][nf]').value;
	parametros='ajaxAnulard='+tipo+'&nfuente='+fuente+'&wemp_pmla='+wemp_pmla+'&wbasedato='+wbd+'&codoc='+doc+'&ffa='+ffa+'&fac='+fac;

	var ajax = nuevoAjax();
	ajax.open("POST", "EnRaDe.php",false);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send(parametros);
	if(id>=filas)
	{
		var a=document.getElementById('divlist');
		a.innerHTML=ajax.responseText;
	}
}

function anularTodoFin(tipo,filas)
{
	for(var i=1; i<=filas; i++)
	{
		anularEnvio(i, tipo, filas);
	}
	$.unblockUI();
}

function anularTodo()//funcion que anula la totalidad de un documento(por ahora envio)
{
	var filas = document.getElementById('nan').value*1;
	var todas=document.getElementById('anularEnv');
	if(todas.checked==true)
	{
		if(!confirm('¿Está seguro que desea realizar la anulación del documento?')){
				todas.checked=false;
				return;
			}
		var tipo='add';
	}else
		{ var tipo='rm'}

	$.blockUI({ message: $('#msjEspere')});
	setTimeout(function(){anularTodoFin(tipo,filas)}, 500);


}

function actualizarTotalReenviado(i)//funcion que actualiza el valor de reenvios en el documento
{
	var totpan = document.getElementById('totalreenv');
	if( totpan != null){
		var totActual = totpan.innerHTML;
		totActual=totActual.replace(/,/g, "")*1;
		var nval = document.getElementById('fac['+i+'][valreenv]').value*1;

		if(document.getElementById('wgrabar['+i+']').checked==false)
		{
			var ntotal= totActual-nval;
		}else
			{
				var ntotal= totActual+nval;
			}
		ntotal=formato_numero(ntotal,0,"",",")
		totpan.innerHTML=ntotal;
 	}
}

function verificarRepetido(numeroFactura, nuevafuente) //valida que no se ingrese la misma factura mas de una vez en el envio
{
	var filas = document.getElementById('ism').value*1; //cantidad de facturas en pantalla
	for(var m=1; m<=filas; m++)
	{
		fac = document.getElementById("arr["+m+"][nf]").value;
		numeroFactura =  numeroFactura.toUpperCase();
		fuente = document.getElementById("arr["+m+"][ffa]").value;
		if((nuevafuente==fuente)&&(numeroFactura==fac))
		{
			return false;
		}
	}
	return true;
}

//FUNCIONES JS PARA LAS RADICACIONES.

function ListarRad(id)// muestra en pantalla las facturas que pueden ser radicadas. está función permite la busqueda por rango de fechas o por número de envío
{
	var wemp_pmla= document.getElementById("wemp_pmla").value;
	var wbd= document.getElementById("wbd").value;
	var dl=document.getElementById("divlist");
	var fr = document.getElementById("filares");
	var wfuente = document.getElementById("fenv").value;
	var wfuenteRad = document.getElementById("frad").value;
	if(id=='fechas')//esto significa que la busqueda se realiza por rango de fechas
	{
		var wfecI= document.getElementById("fechaini").value;
		var wfecF= document.getElementById("fechafin").value;
		var wres= document.getElementById('responsable').value;

		if(wfecI=="" || wfecF=="")
		{
			alert("Rango de fechas no válido");
			return;
		}

		if(wfecI>wfecF)
		{
			alert("La fecha inicial debe ser inferior a la fecha final");
			return;
		}
		var parametros = "ajaxRadT=todo&fec1="+wfecI+"&wemp_pmla="+wemp_pmla+"&fec2="+wfecF+"&wbasedato="+wbd+"&responsable="+wres+"&fuente="+wfuente+"&fuenteRad="+wfuenteRad;
	}
	if(id=='buscarEnvio')
	{
		var nuenvio=document.getElementById('envbus').value;
		var wfuente=document.getElementById('fenv').value;
		if(nuenvio=='')
		{
			alert('POR FAVOR INGRESE EL NÚMERO DEL ENVIO');
			return;
		}else
		  {
			var parametros = "ajaxRadT=env&wemp_pmla="+wemp_pmla+"&wbasedato="+wbd+"&nuenv="+nuenvio+"&fuente="+wfuente+"&fuenteRad="+wfuenteRad;
		  }

	}

	var ajax = nuevoAjax();
	ajax.open("POST", "EnRaDe.php",true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send(parametros);

	ajax.onreadystatechange=function()
	{
		if (ajax.readyState==4)
		{
			rs=ajax.responseText;
			if(rs!='')
			{
			 dl.innerHTML=rs;
			 dl.style.display='';
			 document.getElementById('bok').style.display='none';
			}else
			 {
				alert("NO EXISTEN FACTURAS PARA SER RADICADAS CON ESAS CONDICIONES");
				document.getElementById('bok').style.display='';
			 }
		}
	}
}

function cambiarEstadoRad(i)//función que permite realizar los calculos pertinentes despues de un check en una factura
{
	actualizarTotal(i);
}

function grabarRad()// función que grabar las facturas en una radicación.
{
 var total=document.getElementById('total').innerHTML;
 if(total=='0')
 {
	alert('Debe seleccionar por lo menos una factura');
	return;
 }
 //creacion de ajax que graba el nuevo documento
 //variables
 var wemp_pmla= document.getElementById("wemp_pmla").value;
 var wbd= document.getElementById("wbd").value;
 var fuente = document.getElementById('fue').value; // esta es la fuente del documento
 var doc = document.getElementById('dcto').value;
 var total = document.getElementById('total').innerHTML;
 total=total.replace(/,/g, "")*1;
 var resEnv= document.getElementById('responsableEnvio');
 if(resEnv!=null)
 {
	var wres=resEnv.value;
 }else
    {
      var wres= document.getElementById('responsable').value;
	}
 var caja = document.getElementById('caja').value;
 var suc = document.getElementById('suc').value;
 var usua= document.getElementById("usuario").value;
 var obser= document.getElementById("obser").value;
 var facturas =document.getElementById('facpantalla').value*1;
  var parametros
 var numFac;
 var fueFac;
 var valFac;
 var j=0;//variable para controlas los ingresos, esta es necesaria para realizar solo una vez el registro en la 20
 var dl=document.getElementById('divlist');
 var ajax = nuevoAjax();
 ///ACÁ NECESITAMOS RECORRER TODO LA PANTALLA VERIFICANDO FACTURAS CHECKADAS PARA ESTAR HACIENDO LAS PETICIONES AJAX.
 for(var i=1; i<=facturas; i++)
 {
	if(document.getElementById('wgrabar['+i+']').checked==true)
	{
		j=j+1;
		numFac=document.getElementById('arr['+i+'][nf]').value;
		fueFac=document.getElementById('arr['+i+'][ffa]').value;
		valFac=document.getElementById('arr['+i+'][vf]').value;
		parametros="ajaxGrabRad=1&wemp_pmla="+wemp_pmla+"&wbasedato="+wbd+"&fuente="+fuente+"&doc="+doc+"&total="+total+"&respo="+wres+"&caja="+caja+"&suc="+suc+"&user="+usua+"&obser="+obser+"&numFac="+numFac+"&fueFac="+fueFac+"&valFac="+valFac+"&ingreso="+j;
		ajax.open("POST", "EnRaDe.php",false);
		ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		ajax.send(parametros);
		rs=ajax.responseText;
		if(rs!='')
		{

		}else
		 {
			alert("NO EXISTEN FACTURAS PARA SER RADICADAS CON ESAS CONDICIONES");
		 }
	}
 }
 dl.innerHTML='';
 dl.innerHTML=rs;
 dl.style.display='';
 document.getElementById('bok').style.display='none';

}

function anularRad(i)//funcion que actualizaba los totales cuando se daba click en una factura radicada que se deseaba anular(NO SE ESTÁ USANDO DEBIDO A QUE LA RADICACIÓN SE DEBE ANULAR COMPLETA)
{
	//acá capturamos el valor de dicha factura
	var fackcheck=document.getElementById('anular['+i+']');
	var facturaCambiada = document.getElementById('fa['+i+'][sf]');
	var valFactura =facturaCambiada.value*1;
	var totalDocumento = document.getElementById('totdc');
	var valorTotalDoc=totalDocumento.value*1;
	var totalAnulado = document.getElementById('totan');
	var totalAnu= totalAnulado.value*1;
	var checkadas=document.getElementById('checkadas').value*1;
	if(fackcheck.checked==true)
	{
		totalAnu= totalAnu+valFactura;
		document.getElementById('guardarAnulacion').style.display='';
		document.getElementById('checkadas').value=checkadas+1;
	}else
		{
			totalAnu=totalAnu-valFactura;
			checkadas=checkadas-1;
			document.getElementById('checkadas').value=checkadas;
			if(checkadas==0)
			document.getElementById('guardarAnulacion').style.display='none';
		}
	totalAnulado.value=totalAnu;
	totalAnu=formato_numero(totalAnu,0,"",",")
	document.getElementById('totalAnulado').innerHTML=totalAnu;
}

function anularTodoRad(id)//funcion que actualizaba los totales dando click en todas las facturas listadas para  anular toda la radicacion(NO SE ESTÁ USANDO DEBIDO A QUE LA RADICACIÓN SE DEBE ANULAR COMPLETA)
{
	var todas=document.getElementById(id);
	var facsPantalla=document.getElementById('nan').value;
	if(todas.checked==true)
	{
		for(var i=1; i<=facsPantalla; i++)
		{
			if(document.getElementById('anular['+i+']').checked==false)
			{
				document.getElementById('anular['+i+']').checked=true;
				anularRad(i);
			}
		}
	}else
		{
			for(var i=1; i<=facsPantalla; i++)
			{
				if(document.getElementById('anular['+i+']').checked==true)
				{
					document.getElementById('anular['+i+']').checked=false;
					anularRad(i);
				}
			}
		}
}

function validarMovimientos(fuente, wemp_pmla, wbd, suc, res, facsPantalla, doc )//función que evalua si al intentar cancelar una radicación las facturas pertenecientes ya tienen movimientos posteriores, en caso de ser así no se puede anular la radicacion
{
	var ajax = nuevoAjax();
	var fecData = document.getElementById('fechaDoc').value;
	var horaData = document.getElementById('horaDoc').value;
	for(var k=1; k<=facsPantalla; k++)
	{
		 var ffa = document.getElementById('fan['+k+'][ffa]').value;
		 var fac = document.getElementById('fan['+k+'][nf]').value;
		 parametros="ajaxMovimientosPost=1&wemp_pmla="+wemp_pmla+"&wbasedato="+wbd+"&fuente="+fuente+"&suc="+suc+"&nfa="+fac+"&ffa="+ffa+"&responsable="+res+"&codoc="+doc+"&fecha_data="+fecData+"&hora_data="+horaData;
		 ajax.open("POST", "EnRaDe.php",false);
 		 ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
		 ajax.send(parametros);
		 rs=ajax.responseText;
		 if(rs=='si')
		 return false;
	}
	return true;
}

function guardarAnulacionRad()//Hace efectiva la anulación de una radicación siempre y cuando se cumplan las condiciones.
{
	var facsPantalla = document.getElementById('nan').value*1;
	if(!confirm('¿Está seguro que desea realizar la anulación del documento?'))
	{
		todas=document.getElementById('anularad');
		todas.checked=false;
		//anularTodoRad('anularad');
		return;
	}else
	    {
			//variables globales necesarias.
			var anularDoc='si';
			var fuente = document.getElementById('fuente').value; // esta es la fuente del documento
			var wemp_pmla = document.getElementById('wemp_pmla').value;
			var wbd = document.getElementById('wbd').value;
			var suc = document.getElementById('suc').value;
			var res = document.getElementById('responsable').value;
			var doc = document.getElementById('codoc').value;

			//validar que ninguna factura tenga movimientos posteriores.
			var sePuedeAnular=validarMovimientos(fuente, wemp_pmla, wbd, suc, res, facsPantalla, doc );
			if(sePuedeAnular==false)
			{
				alert("Una o mas facturas pertenecientes a este documento ha sufrido movimientos posteriores, no puede anular la radicación");
				document.getElementById('anularad').checked=false;
				return;
			}
			var ajax= nuevoAjax();

			//recorrer todas las facturas, verificar si están checkadas y hacer la solicitud ajax
			for(var i=1; i<= facsPantalla; i++)
			{
				 var ffa = document.getElementById('fan['+i+'][ffa]').value;
				 var fac = document.getElementById('fan['+i+'][nf]').value;
				 parametros="ajaxAnuRad=1&wemp_pmla="+wemp_pmla+"&wbasedato="+wbd+"&fuente="+fuente+"&codoc="+doc+"&suc="+suc+"&nfac="+fac+"&ffa="+ffa+"&ingreso="+i+"&anularad="+anularDoc+"&responsable="+res;
				 ajax.open("POST", "EnRaDe.php",false);
 				 ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				 ajax.send(parametros);
				 rs=ajax.responseText;
				 /*document.getElementById('pruebas').innerHTML+=rs;*/
			}
			var a=document.getElementById('divlist');
			a.innerHTML=rs;

		}
}

//FUNCIONES JS PARA LAS DEVOLUCIONES.

function ListarDev(id)//funcion que lista las facturas que pueden ser devueltas a partir de un rango de fechas o de una radicacion
{
	var wemp_pmla= document.getElementById("wemp_pmla").value;
	var wbd= document.getElementById("wbd").value;
	var dl=document.getElementById("divlist");
	var fr = document.getElementById("filares");
	var wfuente= document.getElementById("frad").value;
	var wfdev= document.getElementById("fdev").value;
	if(id=='fechas')//esto significa que la busqueda se realiza por rango de fechas
	{
		var wfecI= document.getElementById("fechaini").value;
		var wfecF= document.getElementById("fechafin").value;
		var wres= document.getElementById('responsable').value;
		if(wfecI=="" || wfecF=="")
		{
			alert("Rango de fechas no válido");
			return;
		}

		if(wfecI>wfecF)
		{
			alert("La fecha inicial debe ser inferior a la fecha final");
			return;
		}
		var parametros = "ajaxDevT=rango&fec1="+wfecI+"&wemp_pmla="+wemp_pmla+"&fec2="+wfecF+"&wbasedato="+wbd+"&responsable="+wres+"&frad="+wfuente+"&fdev="+wfdev;
	}
	if(id=='buscarRad')
	{
		var nuradicacion=document.getElementById('radbus').value;
		var wfuente=document.getElementById('frad').value;
		if(nuradicacion=='')
		{
			alert('POR FAVOR INGRESE EL NÚMERO DE LA RADICACIÓN');
			return;
		}else
		  {
			buscarResponsable('cd');
			document.getElementById('filares').style.display='';
			var parametros = "ajaxDevT=rad&wemp_pmla="+wemp_pmla+"&wbasedato="+wbd+"&nurad="+nuradicacion+"&frad="+wfuente+"&fdev="+wfdev;
		  }
	}
	var ajax = nuevoAjax();
	ajax.open("POST", "EnRaDe.php",true);
	ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	ajax.send(parametros);

	ajax.onreadystatechange=function()
	{
		if (ajax.readyState==4)
		{
			rs=ajax.responseText;
			if(rs!='')
			{
			 dl.innerHTML=rs;
			 dl.style.display='';
			 document.getElementById('bok').style.display='none';
			}else
			 {
				alert("NO EXISTEN FACTURAS PARA SER DEVUELTAS CON ESAS CONDICIONES");
			 }
		}
	}
}

function agregarCausa(elemento, accionS, id_causa)//funcion que permite agregar las causas a una devolución.
{
	var causaAdd='';
	if(accionS=='add')
	{
		causaAdd = $("#"+elemento).val();
	}
		else
		{
			$("#div_"+id_causa).hide('slow',function(){$("#div_"+id_causa).remove();});
			causaAdd=id_causa;
		}
	$.post(
		"EnRaDe.php",
		{
			consultaAjax:	'',
			seleccionado:	causaAdd,
			wemp_pmla:		$("#wemp_pmla").val(),
			wbasedato:		$("#wbd").val(),
			accion:			accionS,
			lista_causas:	$("#lista_causas").val()
		},
		function(data)
		{
			$("#lista_causas").val(data.lista);
			$("#"+elemento).html(data.opciones);
			//acá va la validación de si es add o rm, para saber si se agrega o se quita
			$("#div_causas").prepend(data.causa);
		},
		"json"
	);

}

function cambiarEstadoDev(i) //para elegir una factura como candidata a ser devuelta.
{
	actualizarTotal(i);
}

function grabarDevolucion() //hace la petición ajax que mueve las tablas de tal manera que devuelve las facturas deseadas.
{
 facturas_pantalla = $('#facpantalla').val()*1;
 wemp = $('#wemp_pmla').val();
 wbd = $('#wbd').val();
 wfue = $('#fuente').val();
 wfdev = $('#fdev').val();
 wlista_causas= $('#lista_causas').val();
 wsuc = $('#suc').val();
 wcaja = $('#caja').val();
 wuser = $('#usuario').val();
 wobser = $('#obser').val();
 wrespo = $('#responsable').val();
 wtotal= $('#total').html();
 wtotal=wtotal.replace(/,/g, "")*1;
 var wdoc= ($('#dcto').val()*1);
 var ingreso = 0;
 //alert(wdoc);
 for(var k=1; k<=facturas_pantalla; k++)
 {
	if(document.getElementById('wgrabar['+k+']').checked==true)
	{
		numFac=document.getElementById('arr['+k+'][nf]').value;
		fueFac=document.getElementById('arr['+k+'][ffa]').value;
		valFac=document.getElementById('arr['+k+'][vf]').value;
		valFac=valFac.replace(/,/g, "")*1;
		ingreso++;

		$.post(
			"EnRaDe.php",
			{
			  consultaAjax:		'',
			  ajaxGrabar:		1,
			  ingreso:			ingreso,
			  lista_causas:		wlista_causas,
			  nfac:				numFac,
			  nffa:				fueFac,
			  vfac:				valFac,
			  wemp_pmla:		wemp,
			  wbasedato:		wbd,
			  fuente:			wfue,
			  fdev:				wfdev,
			  suc:				wsuc,
			  caja:				wcaja,
			  user:				wuser,
			  obser:			wobser,
			  total:			wtotal,
			  respo:			wrespo,
			  dcto:				wdoc
			},
			function(datos)
			{
				$('#divlist').html(datos.respuesta);
			},
			"json"
		);
	}
 }
}

function anularDev(i) //hace la petición ajax para anular una devolución.
{
	var fackcheck=document.getElementById('anular['+i+']');
	var facturaCambiada = document.getElementById('fa['+i+'][sf]');
	var valFactura =facturaCambiada.value*1;
	var totalDocumento = document.getElementById('total');
	var valorTotalDoc=totalDocumento.value*1;
	var totalAnulado = document.getElementById('totan');
	var totalAnu= totalAnulado.value*1;
	var checkadas=document.getElementById('checkadas').value*1;
	if(fackcheck.checked==true)
	{
		totalAnu= totalAnu+valFactura;
		document.getElementById('guardarAnulacion').style.display='';
		document.getElementById('checkadas').value=checkadas+1;
	}else
		{
			totalAnu=totalAnu-valFactura;
			checkadas=checkadas-1;
			document.getElementById('checkadas').value=checkadas;
			if(checkadas==0)
			document.getElementById('guardarAnulacion').style.display='none';
		}
	totalAnulado.value=totalAnu;
	totalAnu=formato_numero(totalAnu,0,"",",")
	document.getElementById('totalAnulado').innerHTML=totalAnu;
}

function anularTodosDev(todas) //anula todas las facturas en pantalla.
{
	var facsPantalla=document.getElementById('nan').value;
	if(todas.checked==true)
	{
		for(var i=1; i<=facsPantalla; i++)
		{
			if(document.getElementById('anular['+i+']').checked==false)
			{
				document.getElementById('anular['+i+']').checked=true;
				anularDev(i);
			}
		}
	}else
		{
			for(var i=1; i<=facsPantalla; i++)
			{
				if(document.getElementById('anular['+i+']').checked==true)
				{
					document.getElementById('anular['+i+']').checked=false;
					anularDev(i);
				}
			}
		}
}

function guardarAnulacionDev()//Hace efectiva la anulación de una devolucion siempre y cuando se cumplan las condiciones.
{
	var facsPantalla = document.getElementById('nan').value*1;
	if(!confirm('¿Está seguro que desea realizar la anulación del documento?'))
	{
		todas=document.getElementById('anuladev');
		todas.checked=false;
		return;
	}else
	    {
			//variables globales necesarias.
			var anularDoc='si';
			var fuente = document.getElementById('fuente').value; // esta es la fuente del documento
			var wemp_pmla = document.getElementById('wemp_pmla').value;
			var wbd = document.getElementById('wbd').value;
			var suc = document.getElementById('suc').value;
			var res = document.getElementById('responsable').value;
			var doc = document.getElementById('codoc').value;
			var total = document.getElementById('total').value;
			var totanu = total;

			//validar que ninguna factura tenga movimientos posteriores.
			var sePuedeAnular=validarMovimientos(fuente, wemp_pmla, wbd, suc, res, facsPantalla, doc );
			if(sePuedeAnular==false)
			{
				alert("Una o mas facturas pertenecientes a este documento ha sufrido movimientos posteriores, no puede anular la devolucion");
				document.getElementById('anuladev').checked=false;
				return;
			}
			var ajax= nuevoAjax();
			//recorrer todas las facturas, verificar si están checkadas y hacer la solicitud ajax
			for(var i=1; i<= facsPantalla; i++)
			{
				 /*if(document.getElementById('anular['+i+']').checked==true)
				 {*/
					 var ffa = document.getElementById('fan['+i+'][ffa]').value;
					 var fac = document.getElementById('fan['+i+'][nf]').value;
					 parametros="ajaxAnuDev=1&wemp_pmla="+wemp_pmla+"&wbasedato="+wbd+"&fuente="+fuente+"&codoc="+doc+"&suc="+suc+"&nfac="+fac+"&ffa="+ffa+"&ingreso="+i+"&responsable="+res+"&totdc="+total+"&totan="+totanu+"&anularDoc="+anularDoc;
					 ajax.open("POST", "EnRaDe.php",false);
					 ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
					 ajax.send(parametros);
					 rs=ajax.responseText;
					 //document.getElementById('pruebas').innerHTML+=rs;
				/* }*/
			}
			var a=document.getElementById('divlist');
			a.innerHTML=rs;

		}
}

//FUNCIONES JS PARA LAS GLOSAS
function listarGlo(id)  //funcion que hace la petición ajax para glosar las facturas que se pueden glosar correspondientes a los parámetros de búsqueda
{
	var accion='no'; //no es la primera vez
	if(id=='up45')
	{
		accion='si';
		pintar45(accion,'n');
		return;
	}
	var fglo = document.getElementById('fglo').value;
	var res_guardado;
	var numero = document.getElementById('fbusNum').value;
	var prefijo = document.getElementById('fbusPref').value;
	var ffue = document.getElementById('fbusFue').value;
	var wres = $("#responsable").val();
	var ingGlosar = $("#ingresos_glosar").val()*1;
	if(numero=="" || prefijo=="" || ffue=="")
	{
		alert("Datos incompletos");
		return;
	}
	//buscarResponsable("im");
	//CONSULTAR RESPONSABLE DE ESA FACTURA.

	//mostramos en pantalla lo que hay en la 45
	$.post//ESTA FUNCIÓN VA Y GUARDAN EN LA 45
	(
		"EnRaDe.php",
		{
			consultaAjax:	'',
			nufa:			numero,
			pre:			prefijo,
			fue:			ffue,
			ingreso:		ingGlosar,
			fuente:			fglo,
			dcto:			$("#dcto").val(),
			suc:			$("#suc").val(),
			caja:			$('#caja').val(),
			responsable:	$("#responsable").val(),
			usuario:		$("#usuario").val(),
			wemp_pmla:		$("#wemp_pmla").val(),
			wbasedato:		$("#wbd").val(),
			ajaxAddGlosar:  1
		},
		function(data)
		{
			res_guardado=data.respuesta;
			$("#fbusNum").val('');
			if(res_guardado=='repetido')
			{
				alert('ya ha ingresado dicha factura');
				return;
			}else
				{
					if(res_guardado=='NE')
					{
						alert('no hay facturas con esas características');
						return;
					}
					pintar45(accion,'n');
					$("#ingresos_glosar").val(ingGlosar+1)*1;

				}
		},
		"json"
	);
}

function pintar45(accion, doc) //pinta en pantalla lo que se ha guardado en la tabla temporal
{
	var fglo = document.getElementById('fglo').value;
	var wingresos = document.getElementById('ingresos_glosar').value;
	//alert($("#divlist"));
	if(accion=='si' || wingresos==0)
	{
		$.post
		(
			"EnRaDe.php",
			{
				consultaAjax:		'',
				ajaxVerGlosar:		1,
				up45:				accion,
				doc:				doc,
				ingreso:			wingresos,
				wemp_pmla:			$("#wemp_pmla").val(),
				wbasedato:			$("#wbd").val(),
				fuente:				fglo,
				dcto:				$("#dcto").val(),
				suc:				$("#suc").val(),
				caja:				$('#caja').val(),
				responsable:		$("#responsable").val()
			},
			function(data)
			{
				if(data.tabla!='')
				{
					$("#divlist").html(data.tabla);
					$("#divlist").show();
					$("#totGlosado2").html($("#totGlosado").html());
					if(data.invalidas!='' && doc=='s')
					{
						$("#pruebas").html(data.invalidas);
						$("#trFacturasInvalidas").show();
					}
				}else
				 {
					if (doc=='s')
					{
						alert('los datos no arrojaron facturas validas para glosar');
					}
					$("#divlist").html('');
					$("#ingresos_glosar").val(0);
				 }
				if((accion=='si') && ($("#divlist").html()!=''))
				{
					$("#ingresos_glosar").val($("#facturas_mostradas").val()*1);
				}
			},
			"json"
		);
	}else
	{
		$.post
		(
			"EnRaDe.php",
			{
				consultaAjax:		'',
				ajaxVerGlosar:		1,
				up45:				accion,
				ingreso:			wingresos,
				wemp_pmla:			$("#wemp_pmla").val(),
				wbasedato:			$("#wbd").val(),
				fuente:				fglo,
				dcto:				$("#dcto").val(),
				suc:				$("#suc").val(),
				caja:				$('#caja').val(),
				responsable:		$("#responsable").val()
			},
			function(data)
			{
				$("#encabezadoGlosas").after(data.tr);
				totalSaldos = $("#totSalFac").html();
				totalSaldos=totalSaldos.replace(/,/g, "")*1;
				saldoAsumar=data.saldo;
				saldoAsumar=saldoAsumar.replace(/,/g, "")*1;
				nuevoSaldo=totalSaldos+saldoAsumar;
				nuevoSaldo=formato_numero(nuevoSaldo,0,"",",");
				$("#totSalFac").html(nuevoSaldo);
			},
			"json"
		);

	}
	return;
}

function actualizarSaldoFacturasGlosar(i, total) //actualiza los saldos cuando se cambia el valor a glosar de un registro en pantalla
{
	actualizarGlosa45(i);
	if(total=='glosado')
	{
		var totalAglosar=0;
		var facsPantalla=$("#ingresos_glosar").val();
		//lo mas sencillo es cada que cambie, sumar los valores de todos.
		for(var k=1; k<=facsPantalla; k++)
		{
			try{
			valorAglosar= document.getElementById("arr["+k+"][vgl]").value;
			valorAglosar=valorAglosar.replace(/,/g,"")*1;
			valorAglosar=parseInt(valorAglosar);
			if(isNaN(valorAglosar) || valorAglosar<0)
			{
				alert("Valor Ingresado incorrecto");
				document.getElementById("arr["+k+"][vgl]").value='';
				return;
			}
			totalAglosar+=valorAglosar;
			}catch(e){}
		}
		totalAglosar=formato_numero(totalAglosar,0,"",",");
		$("#totGlosado").html(totalAglosar);
		$("#totGlosado2").html(totalAglosar);

	}
}

function actualizarGlosa45(i) //funcion que va a actualizar el valor glosado en la tabla 45 para la factura correspondiente;
{
	fuenteFactura = document.getElementById('arr['+i+'][fuf]').value;
	factura = document.getElementById('arr['+i+'][nuf]').value;
	valor = document.getElementById('arr['+i+'][vgl]').value;
	fuenteGlosa = $('#fglo').val();
	wemp = $("#wemp_pmla").val(),
	wbd = $("#wbd").val(),

	//funcion ajax que actualizará el valor de la glosa para la factura.
	$.ajax({
		type: "POST",
		url:  "EnRaDe.php",
		data: {
				ajaxActGlosa: "1",
				fuefac:	fuenteFactura,
				fac:	factura,
				valor: valor,
				fglo: fuenteGlosa,
				wemp_pmla: wemp,
				wbasedato: wbd
			  },
		success: function(){}
	});
}

function quitarFactura(i) //quita la factura i de la pantalla
{
	if(!confirm('¿Está seguro que desea quitar esta factura?'))
	{
		factu=document.getElementById('delete['+i+']');
		factu.checked=false;
		return;
	}
	var saldoFactura=document.getElementById("arr["+i+"][saf]").value;
	saldoFactura=saldoFactura.replace(/,/g,"")*1;
	var saldoGlosadoFactura=document.getElementById("arr["+i+"][vgl]").value;
	saldoGlosadoFactura=saldoGlosadoFactura.replace(/,/g,"")*1;
	saldoTotal=$("#totSalFac").html();
	saldoTotal=saldoTotal.replace(/,/g,"")*1;
	saldoGlosado=$("#totGlosado").html();
	saldoGlosado=saldoGlosado.replace(/,/g,"")*1;
	nuevoSaldoTotal=saldoTotal-saldoFactura;
	nuevoSaldoGlosado=saldoGlosado-saldoGlosadoFactura;
	nuevoSaldoTotal=formato_numero(nuevoSaldoTotal,0,"",",");
	nuevoSaldoGlosado=formato_numero(nuevoSaldoGlosado,0,"",",");
	saldoTotal=$("#totSalFac").html(nuevoSaldoTotal);
	saldoTotal=$("#totGlosado").html(nuevoSaldoGlosado);
	saldoTotal=$("#totGlosado2").html(nuevoSaldoGlosado);
	numeroFactura=document.getElementById("arr["+i+"][nuf]").value;
	fuenteFactura=document.getElementById("arr["+i+"][fuf]").value;
	//está petición ajax va a eliminar la factura de la 45
	$.post
	(
		"EnRaDe.php",
		{
			consultaAjax:		'',
			ajaxQuitarFactura:	1,
			wbasedato:			$("#wbd").val(),
			wemp_pmla:			$("#wemp_pmla").val(),
			fuente:				fuenteFactura,
			factura:			numeroFactura
		},
		function()
		{
			$("#tr"+i).hide('slow',function(){$("#tr"+i).remove();});
		}
	);

}

function grabarGlosa2()
{
	var facsPantalla = $("#ingresos_glosar").val();
	var ingresos=0;
	var correcto=verificarDatosGrabar(facsPantalla);
	if(correcto==false)
	{
		alert('valores de glosas incompletos');
		return;
	}
	var totalGlosado = $("#totGlosado").html();
	totalGlosado = totalGlosado.replace(/,/g,"")*1;
	for(var k=1; k<=facsPantalla; k++)
	{
		try//si existe
		{
			var fila = $("#tr"+k).attr("id");
			if(fila!=null) // si existe la fila traigo todos los datos para hacer la petición
			{
				ingresos++;
				numFactura = document.getElementById("arr["+k+"][nuf]").value;
				fueFactura = document.getElementById("arr["+k+"][fuf]").value;
				saldoFactura = document.getElementById("arr["+k+"][saf]").value;
				valGloFactura = document.getElementById("arr["+k+"][vgl]").value;
				valGloFactura = valGloFactura.replace(/,/g,"")*1;
				wresponsable = $("#respo"+k).val();
				$.post
				(
					"EnRaDe.php",
					{
						consultaAjax:		'',
						ajaxGrabarGlosa:	1,
						ingreso:			ingresos,
						wemp_pmla:			$("#wemp_pmla").val(),
						wbasedato:			$("#wbd").val(),
						fuente:				$("#fuente").val(),
						nfac:				numFactura,
						ffac:				fueFactura,
						salfac:				saldoFactura,
						vglfac:				valGloFactura,
						totvglo:			totalGlosado,
						suc:				$("#suc").val(),
						caja:				$("#caja").val(),
						usuario:			$("#usuario").val(),
						dcto:				$("#dcto").val(),
						responsable:		wresponsable,
						obser:				$("#obser").val(),
						lista_causas:		$("#lista_causas").val()
					},
					function(datos)
					{
						$('#divlist').html(datos.respuesta);
						$('#buscFactura').hide();
					},
					"json"
				)
			}
		}catch(e){}
	}

	//
}

function verificarDatosGrabar(facs) //verifica que hayan valor en pantalla con los valores de las glosas
{
	continuar=true;
	for (var j=1; j<=facs; j++)
	{
		try
		{
			valor=document.getElementById("arr["+j+"][vgl]").value;
			if(valor!=null)
			{
				valor = valor.replace(/,/g,"")*1;
				if(valor==0)
				{
					continuar=false;
					return continuar;
				}
			}
		}catch(e){}
	}
}

function anularGlosa() //anula una glosa
{
	var facsPantalla = document.getElementById('nan').value*1;
	if(!confirm('¿Está seguro que desea realizar la anulación del documento?'))
	{
		todas=document.getElementById('anulaglo');
		todas.checked=false;
		return;
	}else
	    {
			//variables globales necesarias.
			var fuente = document.getElementById('fuente').value; // esta es la fuente del documento
			var wemp_pmla = document.getElementById('wemp_pmla').value;
			var wbd = document.getElementById('wbd').value;
			var suc = document.getElementById('suc').value;
			var res = document.getElementById('responsable').value;
			var doc = document.getElementById('codoc').value;
			var fecglo = document.getElementById('fecglo').value;

			var ajax= nuevoAjax();
			//recorrer todas las facturas, verificar si están checkadas y hacer la solicitud ajax
			for(var i=1; i<= facsPantalla; i++)
			{
				 var ffa = document.getElementById('arr['+i+'][fuf]').value;
				 var fac = document.getElementById('arr['+i+'][nuf]').value;
				 parametros="ajaxAnuGlo=1&wemp_pmla="+wemp_pmla+"&wbasedato="+wbd+"&fuente="+fuente+"&codoc="+doc+"&suc="+suc+"&nfac="+fac+"&ffa="+ffa+"&ingreso="+i+"&responsable="+res+"&fecglo="+fecglo+"&ingreso="+i;
				 ajax.open("POST", "EnRaDe.php",false);
				 ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				 ajax.send(parametros);
				 rs=ajax.responseText;
				 //document.getElementById('pruebas').innerHTML+=rs;
			}
			var a=document.getElementById('divlist');
			a.innerHTML=rs;

		}
}

function subirArchivo(formName) //primer intento de subir archivo... está inactiva
{
	//formName = document.forms[0];
	var input = document.getElementById("files"),
		formdata = false;

	if (window.FormData) {
  		formdata = new FormData();
  		//document.getElementById("btn").style.display = "none";
	}

	var i = 0, len = formName.files.length, img, reader, file;

		for ( ; i < len; i++ ) {
			file = formName.files[i];

			if (!file.type.match(/image.*/)) {
				if ( window.FileReader ) {
					reader = new FileReader();
					reader.onloadend = function (e) {
						//showUploadedItem(e.target.result, file.fileName);
					};
					reader.readAsDataURL(file);
				}
				if (formdata) {
					formdata.append("files", file);
				}
			}
		}

		wnrodoc = $("#dcto").val();
		suc = $("#suc").val();
		caja = $('#caja').val();
		usuario = $("#usuario").val();
		wemp_pmla = $("#wemp_pmla").val();
		wbasedato = $("#wbd").val();
		wfcon = $("#fuente").val();
		if (formdata) {
			$.ajax({
				url: "EnRaDe.php?ajaxGlosarDocumento=si&wbasedato="+wbasedato+"&wnrodoc="+wnrodoc+"&wfcon="+wfcon+"&wcaja="+caja+"&wcco="+suc+"&wusuario="+usuario,
				type: "POST",
				data: formdata,
				processData: false,
				contentType: false,
				success: function (res) {
					//document.getElementById("response").innerHTML = res;
					pintar45('si','s');
				}
			}
			);
		}
}

function mostrarFacturasInvalidas() //mostra el div oculto con las facturas invalidas resultantes de subir un archivo para glosar.
{
	div="pruebas";
	$.blockUI({ message: $("#"+div),
							css: { left: '15%',
								    top: '15%',
								  width: '30%',
								  height: '65%'
								 }
					  });
}

function ocultarFacturasInvalidas(seccion, cco)
{
	$.unblockUI();
}


function mostrarSubirArchivo()//muestra el panel de ingreso del archivo que se desea subir cuando este es deseado.
{
	$("#mostrar_cargar_archivo").css('display','none');
	$("#tr_cargar_archivo").show();
}

/* ---------------------------------------  funciones para los cobros jurídicos --------------------------------------------------*/
function listarCoj( id ){
	var wemp= document.getElementById("wemp_pmla").value;
	var wbd= document.getElementById("wbd").value;
	var dl=document.getElementById("divlist");
	var fr = document.getElementById("filares");
	var fuente = document.getElementById("fcoj").value;
	var ing=false;
	var continuar=true;
	var rs;

	if(document.getElementById("menu").value=='01')//esto significa que la busqueda se realiza por rango de fechas
	{
		var wfecI= document.getElementById("fechaini").value;
		var wfecF= document.getElementById("fechafin").value;
		var wres= document.getElementById('responsable').value;

		if(wfecI=="" || wfecF=="")
		{
			alert("Rango de fechas no válido");
			return;
		}

		if(wfecI>wfecF)
		{
			alert("La fecha inicial debe ser inferior a la fecha final");
			return;
		}
	}else //en caso de que se haya hecho un ingreso manual.
		{
			ing=true;
			var nufac = document.getElementById('fbusNum').value;
			var prfac = document.getElementById('fbusPref').value;
			var ffue = document.getElementById('fbusFue').value;
			var ism = document.getElementById('ism').value*1;
			if(nufac=="" || prfac=="" || ffue=="")
			{
				alert("Datos incorrectos");
				return;
			}
			else{
					nfac=prfac+"-"+nufac;
					continuar=verificarRepetido(nfac, ffue);
					if(continuar){
						//buscarResponsable("rf");
						var wres= document.getElementById('responsable').value;
						document.getElementById("dvresponsable").style.display='';
					}else
					{
						alert('La factura ya ha sido agregada');
						return;
					}

				}
		}

		if(!ing)//si no es un ingreso manual se hace la siguiente llamada ajax
		{
			$.post
			(
				"EnRaDe.php",
				{
					ajaxCojT: 	'todo',
					fec1: 		wfecI,
					wemp_pmla:	wemp,
					fec2:		wfecF,
					wbasedato: 	wbd,
					responsable:	wres,
					fuente:		fuente
				},
				function(data)
				{
					if(data.tabla!='')
					{
						$("#divlist").html(data.tabla);
						document.getElementById('trbok').style.display='none';
						document.getElementById('divlist').style.display='';
					}else
						 {
							alert("NO EXISTEN FACTURAS PARA SER ENVIADAS CON ESAS CONDICIONES");
						 }
				},
				"json"
			);
		}else
			{
				$.post
				(
					"EnRaDe.php",
					{
						ajaxCojT:		'im',
						ism:			ism,
						wemp_pmla:		wemp,
						wbasedato:  	wbd,
						nfac: 			nfac,
						fue:			ffue,
						fuente:			fuente,
						responsable:	wres
					},
					function(data)
					{
						if(ism>0)
						{
							if(data.tabla!='')
							{
								document.getElementById('ism').value = ism+1;
								$("#facpantalla").remove();
								$("#encabezado").after(data.tabla);
								check = document.getElementById('wgrabar['+document.getElementById('ism').value+']');
								check.click();
							}else
							 {
								alert('NO SE ENCONTRARON FACTURAS CON DICHOS PARÁMETROS');
							 }
						}else
							{
								if(data.tabla!='')
									{
										document.getElementById('ism').value = ism+1;
										$("#divlist").append(data.tabla);
										$("#todas").attr('checked','true');
										check = document.getElementById('wgrabar['+document.getElementById('ism').value+']');
										check.click();
									}
									else
									 {
										alert('NO EXISTEN FACTURAS PARA SER ENVIADAS CON ESAS CONDICIONES');
									 }
							}
						document.getElementById('fbusNum').value='';
						if(id!='add')
						{
							document.getElementById(id).checked=false;
						}
					},
					"json"
				);
			}
	document.getElementById('divlist').style.display='';
}

function guardarCoj(){
	var ings=document.getElementById('ings')*1;
	var totpan=document.getElementById('total');
	var totActual = totpan.innerHTML;
	totActual=totActual.replace(/,/g, "")*1;
	if(ings==0 || totActual==0)
	{
		alert('No hay datos pendientes para guardar');
	}else
	  {
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////variables
		var wemp_pmla   = document.getElementById("wemp_pmla").value;
		var wbd         = document.getElementById("wbd").value;
		var fuente      = document.getElementById('fue').value; // esta es la fuente del documento
		var nuevoEstado = $("#fuente").find("option:selected").attr("abreviacion");
		var doc         = document.getElementById('dcto').value;
		var suc         = document.getElementById('suc').value;
		var caja        = document.getElementById('caja').value;
		var nuevoEstado = $("input[tipo='clasesCompletas'][numeroFuente='"+fuente+"']").attr("estadoEquivalente");
		try {var wres= document.getElementById('responsable').options[document.getElementById('responsable').selectedIndex].text;}catch(e){}
		if(wres==null)
		var wres= $("#responsable").val();
		var obser= document.getElementById("obser").value;
		///////////////////////////////////////////////////////////////////////////////////////////////////////////////fin variables
		var parametros="ajaxGuard=o&wemp_pmla="+wemp_pmla+"&fuente="+fuente+"&wbasedato="+wbd+"&responsable="+wres+"&dcto="+doc+"&suc="+suc+"&caja="+caja+"&totalm="+totActual+"&obser="+obser+"&nuevoEstado="+nuevoEstado;
		try
		{
			try
			{
				$.blockUI({ message: $('#msjEspere') });
			} catch(e){ }
			var ajax = nuevoAjax();
			ajax.open("POST", "EnRaDe.php",true);
			ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			ajax.send(parametros);

			ajax.onreadystatechange=function()
			{
				if (ajax.readyState==4)
				{
					if(ajax.responseText == "error"){
						alerta( "El documento no pudo ser generado, verifique el estado de las facturas" );
					}else{
						var a=document.getElementById('divlist');
						a.innerHTML=ajax.responseText;
						$.unblockUI();
					}
				}try{

					} catch(e){ }
			}
		}catch(e){	}
		//quitarChecks(id);

	  }
}

function listarEnvCoj(id){

	var fcoj=document.getElementById('fcoj').value;
	var fenv=document.getElementById('fenv').value;
	var fuenteActual=document.getElementById('fuente').value;

	if( fuenteActual == fenv ){
		listarEnv( id);
	}
	if( fuenteActual == fcoj ){
		listarCoj( id );
	}
}

function guardarAnulacionCoj(){
	var facsPantalla = document.getElementById('nan').value*1;
	if(!confirm('¿Está seguro que desea realizar la anulación del documento?'))
	{
		todas=document.getElementById('anularcoj');
		todas.checked=false;
		//anularTodoRad('anularad');
		return;
	}else
	    {
			//variables globales necesarias.
			var anularDoc ='si';
			var fuente    = document.getElementById('fuente').value; // esta es la fuente del documento
			var wemp_pmla = document.getElementById('wemp_pmla').value;
			var wbd       = document.getElementById('wbd').value;
			var suc       = document.getElementById('suc').value;
			var res       = document.getElementById('responsable').value;
			var doc       = document.getElementById('codoc').value;
			var fechaDocu = document.getElementById('fechaDoc').value;
			var horaDocu  = document.getElementById('horaDoc').value;
			var fenv      =document.getElementById('fenv').value;
			var fuentes   = "";

			$("input[tipo='clasesCompletas']").each(function(){
				if( fuentes == "" )
					fuentes = fuentes+$(this).attr("numeroFuente")+"-"+$(this).attr("estadoEquivalente");
					else
						fuentes = fuentes+","+$(this).attr("numeroFuente")+"-"+$(this).attr("estadoEquivalente");

			});
			//validar que ninguna factura tenga movimientos posteriores.
			var sePuedeAnular=validarMovimientos(fuente, wemp_pmla, wbd, suc, res, facsPantalla, doc );
			if(sePuedeAnular==false)
			{
				alert("Una o mas facturas pertenecientes a este documento ha sufrido movimientos posteriores, no puede anular el cobro Jurídico");
				document.getElementById('anularcoj').checked=false;
				return;
			}
			var ajax= nuevoAjax();

			//recorrer todas las facturas, verificar si están checkadas y hacer la solicitud ajax
			for(var i=1; i<= facsPantalla; i++)
			{
				 var ffa = document.getElementById('fan['+i+'][ffa]').value;
				 var fac = document.getElementById('fan['+i+'][nf]').value;
				 parametros="ajaxAnuCoj=1&wemp_pmla="+wemp_pmla+"&wbasedato="+wbd+"&fuente="+fuente+"&codoc="+doc+"&suc="+suc+"&nfac="+fac+"&ffa="+ffa+"&ingreso="+i+"&anularCoj="+anularDoc+"&responsable="+res+"&fechaDocu="+fechaDocu+"&horaDocu="+horaDocu+"&fuentesCompletas="+fuentes+"&fenv="+fenv;
				 ajax.open("POST", "EnRaDe.php",false);
 				 ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				 ajax.send(parametros);
				 rs=ajax.responseText;
				 /*document.getElementById('pruebas').innerHTML+=rs;*/
			}
			var a=document.getElementById('divlist');
			a.innerHTML=rs;

		}
}


function alerta( txt ){
	$("#textoAlerta").text( txt );
	$.blockUI({ message: $('#msjAlerta') });
		setTimeout( function(){
						$.unblockUI();
					}, 1600 );
}
</script>
<?php
/*======================================================================================FUNCIONES===============================================================================*/
function listarFuentes()
{
	global $conex;
	global $wbasedato;
	global $usua;
	global $fuente;
	// query para traerme las fuentes a las cuales tiene permiso el usuario
    echo "<td><b>Fuente:</b><br>";
	if($fuen)
	{
		echo "<select name='fuente' id='fuente' onchange='nuevaFuente(this.value)'>";
		$query =  " SELECT  carfue, cardes
					  FROM ".$wbasedato."_000040, ".$wbasedato."_000030
					 WHERE (Carenv='on' AND Cjeenv ='on' AND Cjeusu ='".$usua[1]."')
						OR (Carrad='on' AND Cjerad ='on' AND Cjeusu ='".$usua[1]."')
						OR (Cardev='on' AND Cjedev ='on' AND Cjeusu ='".$usua[1]."')
						OR (Carglo='on' AND cjeglo ='on' AND Cjeusu ='".$usua[1]."')
						OR (Carcoj='on' AND Cjecoj ='on' AND Cjeusu ='".$usua[1]."')
				  ORDER BY carfue ";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);

		echo "<option value='na' selected>--</option>";

		for ($i=1;$i<=$num;$i++)
		{
			$row = mysql_fetch_array($err);
			echo "<option value='".$row[0]."-".$row[1]."'>".$row[0]."-".$row[1]."</option>";
		}
		echo "</select>";
	}else
		{
			echo "<select name='fuente' id='fuente' onchange='nuevaFuente(this.value)'>";
			// query para pintar las fuentes diferentes a la que fue seleccionada
			$query =  " SELECT DISTINCT carfue, cardes, Caresf
						  FROM ".$wbasedato."_000040, ".$wbasedato."_000030
						 WHERE (Carenv='on' AND Cjeenv ='on' AND Cjeusu ='".$usua[1]."')
							OR (Carrad='on' AND Cjerad ='on' AND Cjeusu ='".$usua[1]."')
							OR (Cardev='on' AND Cjedev ='on' AND Cjeusu ='".$usua[1]."')
							OR (Carglo='on' AND Cjeglo ='on' AND Cjeusu ='".$usua[1]."')
							OR (Carcoj='on' AND Cjecoj ='on' AND Cjeusu ='".$usua[1]."')
					  ORDER BY carfue ";

			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);

			echo "<option value='na' selected> </option>";
			//echo "<option value='".$fuente."'>".$fuente."</option>";

			for ($i=1;$i<=$num;$i++)
			{
				$row = mysql_fetch_array($err);
				if($row[0]."-".$row[1]==$fuente)
					echo "<option value='".$row[0]."-".$row[1]."' abreviacion='".$row[2]."'>".$row[0]."-".$row[1]."</option>";
				else
					echo "<option value='".$row[0]."-".$row[1]."' abreviacion='".$row[2]."'>".$row[0]."-".$row[1]."</option>";

			}
			echo "</select>";
		}
		echo "</td>";
		echo "<td id='tdopc' style='display:none;'> <b>Opciones</b><br><div id='opciones'></div></td>";
		echo "<input type='hidden' id='fuenteAnt' value=' '>";//esta variable indica si es la primera vez que se entra a la fuente, para generar o no las opciones
}

function consultarSucursal()
{
	global $wbasedato;
	global $conex;
	global $fecha;
	global $usua;
	global $carfue;
	$query =  " SELECT Carfmo
					FROM ".$wbasedato."_000040
					WHERE Carfue='".$carfue."'
					AND Carest='on'";
	$err = mysql_query($query,$conex) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
	//echo $query;
	$row = mysql_fetch_array($err);
	echo "<td id='wdoc' align='left' class='fila2' style='display:none;'></td>";
	echo "<td align='left' class='fila2'><b> Fecha: <br></b>";
		if($row['Carfmo']!='on') {
			echo $fecha;
			echo "<input type='hidden' name='wfecha' value='0'>";
		}
		else
			campoFechaDefecto("wfecha", $fecha);
    echo "</td>";

    $query =  " SELECT Cjecco, Cjecaj
                FROM ".$wbasedato."_000030
              	WHERE Cjeusu='".$usua[1]."'";
    $err = mysql_query($query,$conex) or die( mysql_errno()." - Error en el query $query - ".mysql_error() );
    $nom = mysql_fetch_array($err);

    echo "<td align=left class='fila2'><b> Sucursal: <br></b>".$nom[0]."</td>";///////////////////////////////////////SUCURSAL
    echo "<input type='hidden' id='suc' name='suc' value='".$nom[0]."'>";
    echo "<input type='hidden' id='caja' name='caja' value='".$nom[1]."'>";
}

function menuResponsable()
{
	global $conex;
	global $wbasedato;
	echo "<center><table width='90%' id='tbresponsable'>";
	//echo "<tr class='fila1' align='center''><td id='filares' colspan=3><b> Responsable:<br> <input type='text' id='busc' name='busc' value='".$busc."' onchange='buscarResponsable(\"nm\");'> ";
	echo "<tr class='fila1' align='center''><td id='filares' colspan=3><b> Responsable:<br> <input type='text' id='busc' name='busc' value='".$busc."' onblur='buscarResponsable(\"nm\");' onkeypress='if (validar(event)) buscarResponsable(\"nm\")'> ";
	echo "<select id='responsable' name='responsable'>";
				$query =  " SELECT empcod, empnit, empnom
						FROM ".$wbasedato."_000024
						WHERE Empcod=Empres
						ORDER BY empcod ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				for ($i=1;$i<=$num;$i++)
				{
					$row = mysql_fetch_array($err);
					echo "<option value='".$row[0]."-".$row[1]."-".$row[2]."'>".$row[0]." - ".$row[1]." - ".$row[2]."</option>";
				}
				echo "</select></td>";
	echo "</tr>";
	echo "</table></center>";
}

function menuFechas()
{
	global $fecha;
	if(!isset($fechaini) and !isset($fechafin))
		{
			$fechaini = $fechafin = $fecha;
		}
	echo "<tr id='fechas' style='display:none;' align='center'>";
	echo "<td id='filafec' class='fila2' algin=center colspan=3><b>Fecha inicial: ";
	campoFechaDefecto( "fechaini", $fechaini );
	echo " Fecha final: </b>";
	campoFechaDefecto( "fechafin", $fechafin );
	echo "</td>";
	echo "</tr>";
}

function fuentesControl()//funcion que busca el numero y la descripcion de las fuentes correspondientes a cada movimiento
{
	global $usua;
	global $wbasedato;
	global $conex;

	$query =  " SELECT  carfue, cardes
				  FROM ".$wbasedato."_000040, ".$wbasedato."_000030
				 WHERE Carenv='on' AND Cjeenv ='on' AND Cjeusu ='".$usua[1]."'";
	$err = mysql_query($query,$conex);
	$rowev=mysql_fetch_array($err);
	$fenv=$rowev[0]."-".$rowev[1];

	$query =  " SELECT  carfue, cardes
				  FROM ".$wbasedato."_000040, ".$wbasedato."_000030
				 WHERE Carrad='on' AND Cjerad ='on' AND Cjeusu ='".$usua[1]."'";
	$err = mysql_query($query,$conex);
	$rowra=mysql_fetch_array($err);
	$frad=$rowra[0]."-".$rowra[1];

	$query =  " SELECT  carfue, cardes
				  FROM ".$wbasedato."_000040, ".$wbasedato."_000030
				 WHERE Cardev='on' AND Cjedev ='on' AND Cjeusu ='".$usua[1]."'";
	$err = mysql_query($query,$conex);
	$rowde=mysql_fetch_array($err);
	$fdev=$rowde[0]."-".$rowde[1];

	$query =  " SELECT  carfue, cardes
				  FROM ".$wbasedato."_000040, ".$wbasedato."_000030
				 WHERE Carglo='on' AND cjeglo ='on' AND Cjeusu ='".$usua[1]."'";
	$err = mysql_query($query,$conex);
	$rowgl=mysql_fetch_array($err);
	$fglo=$rowgl[0]."-".$rowgl[1];

	$query =  " SELECT  carfue, cardes
				  FROM ".$wbasedato."_000040, ".$wbasedato."_000030
				 WHERE Carcoj='on' AND cjecoj ='on' AND Cjeusu ='".$usua[1]."'";
	$err = mysql_query($query,$conex);
	$rowgl=mysql_fetch_array($err);
	$fcoj=$rowgl[0]."-".$rowgl[1];

	echo "<input type='hidden' id='fenv' value='".$fenv."'>";
	echo "<input type='hidden' id='frad' value='".$frad."'>";
	echo "<input type='hidden' id='fdev' value='".$fdev."'>";
	echo "<input type='hidden' id='fglo' value='".$fglo."'>";
	echo "<input type='hidden' id='fcoj' value='".$fcoj."'>";
}

function fuentesCompletas(){
	global $conex, $wbasedato;
	$fuentesCompletas = "";
	$query =  " SELECT  carfue, caresf
					  FROM ".$wbasedato."_000040, ".$wbasedato."_000030
					 WHERE (Carenv='on' AND Cjeenv ='on')
						OR (Carrad='on' AND Cjerad ='on')
						OR (Cardev='on' AND Cjedev ='on')
						OR (Carglo='on' AND cjeglo ='on')
						OR (Carcoj='on' AND cjecoj ='on')
				  GROUP BY 1,2
				  ORDER BY carfue ";
	$err = mysql_query($query,$conex);
	while( $row = mysql_fetch_array( $err ) ){
		$fuentesCompletas .= "<input type='hidden' tipo='clasesCompletas' numeroFuente='{$row[0]}' estadoEquivalente='{$row[1]}'>";
	}
	return( $fuentesCompletas );
}
/*====================================================================================FIN FUNCIONES=============================================================================*/

if(!isset($_SESSION['user']))
echo "error";
else
{
include_once("root/comun.php");




//vamos a hacer la presentación

//traigo los datos del usuario y la empresa.
$institucion = consultarInstitucionPorCodigo($conex, $wemp_pmla);
$ingresos=0;
$facturas_glosar=0;
$wbasedato = strtolower($institucion->baseDeDatos);
$wactualiz = '2016-05-16';
$winstitucion = $institucion->nombre;
encabezado("ENVIO, RADICACION, DEVOLUCION, GLOSAS Y COBROS JURIDICOS", $wactualiz, "logo_".$wbasedato);
$usua=explode('-',$user);
$fecha=date("Y-m-d");

echo "<form id='enrade' name='enrade' method=post  accept-charset='utf-8' action='EnRaDe.php' enctype='multipart/form-data'>";
echo "<input type='hidden' id='wbd' name='wbd' value='".$wbasedato."'>";
echo "<input type='hidden' id='wemp_pmla' name='wemp_pmla' value='".$wemp_pmla."'>";
echo "<input type='hidden' id='usuario' name='usuario' value='".$user."'>";
echo "<input type='hidden' id='ings' name='ings' value=".$ingresos.">";
echo "<input type='hidden' id='ingresos_glosar' name='ingresos_glosar' value=0>";
echo fuentesCompletas();
echo "<input type='hidden' id='movimiento' name='movimiento'>";
fuentesControl();//variables de fuentes
//MENÚ PRINCIPAL.
echo "<center><table width='90%'>";
echo "<tr class='encabezadotabla' align='center' ><td colspan=5>ELIJA LA FUENTE Y POSTERIORMENTE LA OPCI&Oacute;N CORRESPONDIENTES A LA OPERACI&Oacute;N DESEADA</td></tr>";
echo "<tr class='fila2'>";
listarFuentes();
consultarSucursal();
echo "</tr>";
echo "</table></center>";
echo "<div id=dvresponsable style='display:none;'>";
MenuResponsable();
echo "</div>";
echo "<center><table width='90%'>";
menuFechas();
echo "</table></center>";
//contenedor de campos para ingresos manuales.
echo "<center><table width='90%'>";
echo "<tr id='encabezadoIngresosManuales' class='encabezadotabla' style='display:none'><td align='center'>INGRESE LOS DATOS</td></tr>";
echo "<tr align='center' class='fila1' id='generarPorBusqueda' style='display:none;'>";
echo "<td  id='buscFactura' colspan='4' style='display:none;'>";
	echo "<b>Fuente-Prefijo-N&uacute;mero </b><br><input type='text' value='' id='fbusFue'>";
	echo " <input type='text' value='' id='fbusPref'/>";
	echo " <input type='text' value='' id='fbusNum' onkeypress='if (validar(event)) listarEnvCoj( \"add\" )'/>";
	echo "<input type='button' id='add' value='Agregar' style='display:none;' onclick='listarEnvCoj(this.id);'/>";
	echo "<input type='button' id='addGlo' value='Agregar' style='display:none;' onclick='listarGlo(this.id);'/>";
	echo " <input type='hidden' value=0 id='ism'/>";
echo "</td>";
//este es para buscar un envio para ser radicado
echo "<td id='buscEnvio' style='display:none;'>";
echo " <b>N&uacute;mero de Envio: </b><input type='text' value='' id='envbus' onkeypress='if (validar(event)) ListarRad(\"buscarEnvio\")'/>&nbsp;";
echo "<input type='button' id='buscarEnvio' value='Buscar' onclick='ListarRad(this.id);'>";
echo "</td>";
//este es para buscar una radicación para ser devuelta.
echo "<td id='buscRadica' style='display:none;'>";
echo " <b>N&uacute;mero de Radicación: </b><input type='text' value='' id='radbus'  onkeypress='if (validar(event)) ListarDev(\"buscarRad\");' />&nbsp;";
echo "<input type='button' id='buscarRad' value='Buscar' onclick='ListarDev(this.id);'>";
echo "</td>";
echo "</tr>";
echo "<tr id='mostrar_cargar_archivo' align='center' style='display:none'><td colspan=3><font color='blue' style='cursor:pointer' onclick='mostrarSubirArchivo()'>Subir Archivo</font></td></tr>";
echo "<tr align='center' id='tr_cargar_archivo' style='display:none;'><td colspan='3'>Cargar archivo: <INPUT type='file' id='files' name='files' onchange='return ajaxFileUpload();'>";//&nbsp&nbsp&nbsp<button class='button' id='buttonUpload' onclick='return ajaxFileUpload();'>Subir</button></td></tr>";
echo "</table></center>";
echo "<div id='fopc' align='center' style='display:none;'></div>";
//FIN MENÚ PRINCIPAL

//contenedores de resultados
echo "<center><table>";
echo "<tr><td>";
	echo "<div id='divlist' name='divlist'>";
	echo "</div>";
echo "</td></tr>";
echo "<br>";
echo "<tr id='trbok' style='display:none;'><td><input type='button' id='bok' onclick='okRangoFechas();' value='BUSCAR'/></td></tr>";
echo"</table></center>";

echo "<div id='msjEspere' name='msjEspere' style='display:none;'>";
echo "<br /><img src='../../images/medical/ajax-loader5.gif'/><br /><br />Por favor espere un momento ... <br /><br />";
echo "</div>";
echo "<div id='pruebas' name='pruebas' align='center' style='display:none; cursor:default; background:none repeat scroll 0 0; position:relative; width:98%; height:98%; overflow:auto;'></div>";
echo "<center><table>";
	echo "<tr><td>&nbsp;</td></tr>";
	echo "<tr id='btnRetornar' style='display:none'><td align='center'><a href='EnRaDe.php?wemp_pmla=".$wemp_pmla."'>Retornar</a></td></tr>";
	echo "<tr><td>&nbsp;</td></tr>";
	echo "<tr><td align=center colspan=9><input type=button value='Cerrar Ventana' onclick='javascript:window.close();'></td></tr>";
echo "</table></center>";
echo "<div id='msjAlerta' style='display:none;'>";
echo '<br>';
echo "<img src='../../images/medical/root/Advertencia.png'/>";
echo "<br><br><div id='textoAlerta'></div><br><br>";
echo '</div>';

//--------------------->formulario para selección de las respuesta de las glosas a las entidades
echo "<div id='div_respuestas_glosas' align='center' style='display:none;'>";
	echo "<br><br>";
		echo "<span class='subtituloPagina2'><font size='1'>Las siguientes facturas están glosadas y se reenviarán sin nota crédito asociada<br> debe elegir una respuesta justificando el por qué  se rechazó totalmente</font></span>";
	echo "<br><br>";
	echo "<table id='tbl_respuestas_glosas'>";
		echo "<tr class='encabezadoTabla'><td colspan='4'> SELECCIONE LA RAZÓN DEL RECHAZO DE LA GLOSA <SPAN id='spn_numero_glosa'></span></td></tr>";
		echo "<tr class='encabezadoTabla'><td align='center' > GLOSA </td><td align='center' > FUENTE </td><td align='center' > FACTURA </td><td align='center'> Respuesta </td></tr>";
		echo "<tr class='fila2' align='center' name='fila_respuesta_glosa' tipo='ppal' numeroDeGlosa=''><td></td><td></td><td></td><td>
					<select name='slc_respuestas_glosas' onChange='seleccionarRespuestaGlosa(this)'>";

			$query = " SELECT regcod, regnom
						 FROM {$wbasedato}_000261
						WHERE regval = 'on'
						  AND regcon = '=='";
			$rs   = mysql_query($query,$conex);
			echo "<option value=''>Seleccione...</option>";
			while( $row = mysql_fetch_array( $rs ) ){
				echo "<option value='{$row[0]}'>{$row[0]}-->{$row[1]}</option>";
			}
		echo "		</select>
			  </td></tr>";
	echo "</table>";
	echo "<br><input type='button' id='btn_respuesta_glosa' value='GUARDAR' onClick='agregarRespuestaGlosa()'>";
	echo "<br><br>";
	echo "<input type='hidden' id='respuesta_seleccionada' value=''>";
echo "</div>";
echo "</form>";

}
?>
<script>
function ajaxFileUpload()
	{
		wnrodoc = $("#dcto").val();
		suc = $("#suc").val();
		caja = $('#caja').val();
		usuario = $("#usuario").val();
		wemp_pmla = $("#wemp_pmla").val();
		wbasedato = $("#wbd").val();
		wfcon = $("#fuente").val();
		wres = $("#responsable").val();
		$.ajaxFileUpload
		(
			{
				url: "EnRaDe.php?ajaxGlosarDocumento=si&wbasedato="+wbasedato+"&wnrodoc="+wnrodoc+"&wfcon="+wfcon+"&wcaja="+caja+"&wcco="+suc+"&wusuario="+usuario+"&wempresa="+wres,
				secureuri:false,
				fileElementId:'files',
				dataType: 'json',
				data:{name:'logan', id:'id'},
				success: function (data, status)
				{
					if(typeof(data.error) != 'undefined')
					{
						if(data.error != '')
						{
							//alert(data.error);

						}else
						{
							//alert(data.error);
						}
					}else
						{
							return false;
						}
				},
				error: function (data, status, e)
				{
					//alert(e);
				}
			}
		);
		//pintar45('si');
		return false;

	}

	jQuery.extend({


    createUploadIframe: function(id, uri)
	{
			//create frame
            var frameId = 'jUploadFrame' + id;
            var iframeHtml = '<iframe id="' + frameId + '" name="' + frameId + '" style="position:absolute; top:-9999px; left:-9999px"';
			if(window.ActiveXObject)
			{
                if(typeof uri== 'boolean'){
					iframeHtml += ' src="' + 'javascript:false' + '"';

                }
                else if(typeof uri== 'string'){
					iframeHtml += ' src="' + uri + '"';

                }
			}
			iframeHtml += ' />';
			jQuery(iframeHtml).appendTo(document.body);

            return jQuery('#' + frameId).get(0);
    },
    createUploadForm: function(id, fileElementId, data)
	{
		//create form
		var formId = 'jUploadForm' + id;
		var fileId = 'jUploadFile' + id;
		var form = jQuery('<form  action="" method="POST" name="' + formId + '" id="' + formId + '" enctype="multipart/form-data" accept-charset="UTF-8"></form>');
		if(data)
		{
			for(var i in data)
			{
				jQuery('<input type="hidden" name="' + i + '" value="' + data[i] + '" />').appendTo(form);
			}
		}
		var oldElement = jQuery('#' + fileElementId);
		var newElement = jQuery(oldElement).clone();
		jQuery(oldElement).attr('id', fileId);
		jQuery(oldElement).before(newElement);
		jQuery(oldElement).appendTo(form);



		//set attributes
		//jQuery(form).css('position', 'absolute');
		//jQuery(form).css('top', '-1200px');
		//jQuery(form).css('left', '-1200px');
		jQuery(form).appendTo('body');
		return form;
    },

    ajaxFileUpload: function(s) {
        // TODO introduce global settings, allowing the client to modify them for all requests, not only timeout
        s = jQuery.extend({}, jQuery.ajaxSettings, s);
        var id = new Date().getTime()
		var form = jQuery.createUploadForm(id, s.fileElementId, (typeof(s.data)=='undefined'?false:s.data));
		var io = jQuery.createUploadIframe(id, s.secureuri);
		var frameId = 'jUploadFrame' + id;
		var formId = 'jUploadForm' + id;
        // Watch for a new set of requests
        if ( s.global && ! jQuery.active++ )
		{
			jQuery.event.trigger( "ajaxStart" );
		}
        var requestDone = false;
        // Create the request object
        var xml = {}
        if ( s.global )
            jQuery.event.trigger("ajaxSend", [xml, s]);
        // Wait for a response to come back
        var uploadCallback = function(isTimeout)
		{
			var io = document.getElementById(frameId);
            try
			{
				if(io.contentWindow)
				{
					 xml.responseText = io.contentWindow.document.body?io.contentWindow.document.body.innerHTML:null;
                	 xml.responseXML = io.contentWindow.document.XMLDocument?io.contentWindow.document.XMLDocument:io.contentWindow.document;

				}else if(io.contentDocument)
				{
					 xml.responseText = io.contentDocument.document.body?io.contentDocument.document.body.innerHTML:null;
                	xml.responseXML = io.contentDocument.document.XMLDocument?io.contentDocument.document.XMLDocument:io.contentDocument.document;
				}
            }catch(e)
			{
				jQuery.handleError(s, xml, null, e);
			}
            if ( xml || isTimeout == "timeout")
			{
                requestDone = true;
                var status;
                try {
                    status = isTimeout != "timeout" ? "success" : "error";
                    // Make sure that the request was successful or notmodified
                    if ( status != "error" )
					{
                        // process the data (runs the xml through httpData regardless of callback)
                        var data = jQuery.uploadHttpData( xml, s.dataType );
                        // If a local callback was specified, fire it and pass it the data
                        if ( s.success )
                            s.success( data, status );

                        // Fire the global callback
                        if( s.global )
                            jQuery.event.trigger( "ajaxSuccess", [xml, s] );
                    } else
                        {
							////jQuery.handleError(s, xml, status);
						}

                } catch(e)
				{
                    ////status = "error";
                    ////jQuery.handleError(s, xml, status, e);
                }

                // The request was completed
                if( s.global )
                    jQuery.event.trigger( "ajaxComplete", [xml, s] );

                // Handle the global AJAX counter
                if ( s.global && ! --jQuery.active )
                    jQuery.event.trigger( "ajaxStop" );

                // Process result
                if ( s.complete )
                    s.complete(xml, status);

                jQuery(io).unbind()

                setTimeout(function()
									{	try
										{
											jQuery(io).remove();
											jQuery(form).remove();
											$("#divlist").html('');
											$("#ingresos_glosar").val('0');
											$("#mostrar_cargar_archivo").show();
											$("#tr_cargar_archivo").css('display','none');
											pintar45('si','s');

										} catch(e)
										{
											////jQuery.handleError(s, xml, null, e);
										}

									}, 0)

                xml = null

            }
        }
        // Timeout checker
        if ( s.timeout > 0 )
		{
            setTimeout(function(){
                // Check to see if the request is still happening
                if( !requestDone ) uploadCallback( "timeout" );
            }, s.timeout);
        }
        try
		{

			var form = jQuery('#' + formId);
			jQuery(form).attr('action', s.url);
			jQuery(form).attr('method', 'POST');
			jQuery(form).attr('target', frameId);
            if(form.encoding)
			{
				jQuery(form).attr('encoding', 'multipart/form-data');
            }
            else
			{
				jQuery(form).attr('enctype', 'multipart/form-data');
            }
            jQuery(form).submit();

        } catch(e)
		{
            ////jQuery.handleError(s, xml, null, e);
        }

		jQuery('#' + frameId).load(uploadCallback	);
        return {abort: function () {}};

    },

    uploadHttpData: function( r, type ) {
        var data = !type;
        data = type == "xml" || data ? r.responseXML : r.responseText;
        // If the type is "script", eval it in global context
        if ( type == "script" )
            jQuery.globalEval( data );
        // Get the JavaScript object, if JSON is used.
        if ( type == "json" )
            eval( "data = " + data );
        // evaluate scripts within html
        if ( type == "html" )
            jQuery("<div>").html(data).evalScripts();
		return data;
    }
})


</script>
