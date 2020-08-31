 <title>REPORTE FACTURA V1.0</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");

   /*************************************************
	*	REPORTE DE AUDITORIA DE FACTURAS 			*
	*************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	
/*
	echo "<table align=center border=1 bordercolor=#000080 width=555 style='border:solid;'><tr><td align='center' colspan='0'>";
	echo "<img SRC='\MATRIX\images\medical\general\logo_promo.GIF' width=208 high=62></td>";
	echo "<td align=center colspan='4' ><font size=4 color='#000080' face='arial'><B>SOLICITUD DE NOTA CREDITO</b></font></td></table>";*/
	if($num>0)
	{
		/* si va a diligenciar la solicitud*/
		if($total == 0)
			$reem="";	// no reemplaza nada por que la nota credito es parcial
		$table[1]="";
		$error="";
		$valor_total[1]=0;
		$j=0;
		//echo "num=$num<br>";
		for($i=1;$i<=$num;$i++)
		{
			//echo "ch[$i]=$ch[$i]<br>";
			if(isset($ch[$i]) and $ch[$i] == 'on')
			{
				$ini=strpos($tercero[$i],"-");
				$ini1=strrpos($tercero[$i],"-");
				if((integer)(substr($tercero[$i],$ini1+2))==0)
					echo"<h2><b>SE HA PASADO EL LIMITE PARA EL VALOR NOTA CREDITO DEL CONCEPTO $concepto[$i]<br> Y EL TERECERO $tercero[$i]</b></h2>";
				$j++;
				$table[1]=$table[1]."<tr><td ><font size=2 face='arial'><b>Credito</b></td>";//tipo:credito
				$table[1]=$table[1]."<td ><font size=2 face='arial'>".$concepto[$i]."</td>";//concepto
				$table[1]=$table[1]."<td ><font size=2 face='arial'>".substr($tercero[$i],0,$ini)."</td>";//CC
				$table[1]=$table[1]."<td><font size=2 face='arial'>".substr($tercero[$i],$ini+1,$ini1 - $ini-1)."</td>";//nit
				$table[1]=$table[1]."<td><font size=2 face='arial'>".$causa[$i]."</td>";
				$table[1]=$table[1]."<td><font size=2 face='arial'>".$obs[$i]."</td>";
				$table[1]=$table[1]."<td><font size=2 face='arial'><b>".number_format($valor[$i],0,',','.')."</b></TD></tr>";
				$valor_total[1]=$valor_total[1]+$valor[$i];
				$queryf[$i]="$j,$cod,'$fact-$inc[$i]','$concepto[$i]','".substr($tercero[$i],0,$ini)."','".substr($tercero[$i],$ini+1,$ini1 - $ini-1)."','$obs[$i]',$valor[$i],'c-calidad')";
				//echo $queryf[$i]."<br>";
				$max[$i]=(integer)(substr($tercero[$i],$ini1+2))-$valor[$i];// si no hay otro queda intacto apesar del for
				if($max[$i]<0)
				{
					$error=$error."<br>El item correspondiente a la INCONSISTENCIA $inc[$i] ha sobrepasado el valor para el concepto $concepto[$i] para el tercero ".substr($tercero[$i],$ini+1,$ini1 - $ini-1);
					$error=$error."<br>El valor máx es ".substr($tercero[$i],$ini1+1)." y  se sobrepasa en $".($max[$i]*(-1))."<br>";
				}
				for($u=1;$u<$i;$u++)
				{
					if(isset($ch[$i]) and $ch[$i] == 'on' and isset($concepto[$i]) and isset($concepto[$u]) and isset($tercero[$i]) and $concepto[$i]==$concepto[$u] and $tercero[$i]==$tercero[$u])
					{
						$max[$i]=$max[$u]=$max[$u]-$valor[$i];
						if($max[$i]<0)
						{
							$error=$error."<br> Se ha sobrepasado el valor para el concepto $concepto[$i] para el tercero ".substr($tercero[$i],$ini+1,$ini1 - $ini-1);
							$error=$error."<br>El valor máx es ".substr($tercero[$i],$ini1+1)." y el total en la solicitud se sobrepasa en $".($max[$i]*(-1))."<br>";
						}
					}
				}
			}
		}
		
		
		$valor_credito[1]=$valor_total[1];
		if(!isset($numd))
		{
			$numd=0;
			$debito="";
		}
		else
			$debito="- DEBITO";
		if($error== "" )
		{
			/*Nota Debito*/
			for($i=1;$i<=$numd;$i++)
			{
				if(isset($chd[$i]) and $chd[$i] == 'on')
				{
					$j++;
					$table[1]=$table[1]."<tr><td ><font size=2 face='arial'><b><i>Debito</i></b></td>";//tipo:credito
					$table[1]=$table[1]."<td ><font size=2 face='arial'><i>".$conceptod[$i]."</i></td>";//concepto
					$table[1]=$table[1]."<td ><font size=2 face='arial'><i>$ccd[$i]</i></td>";//CC
					$table[1]=$table[1]."<td><font size=2 face='arial'><i>$nitd[$i]</i></td>";//nit
					$table[1]=$table[1]."<td><font size=2 face='arial'><i>".$causad[$i]."</i></td>";
					$table[1]=$table[1]."<td><font size=2 face='arial'><i>".$obsd[$i]."</i></td>";
					$table[1]=$table[1]."<td><font size=2 face='arial'><b><i>".number_format($valord[$i],0,',','.')."</i></b></TD></tr>";
					$valor_total[1]=$valor_total[1]-$valord[$i];
					$querydf[$i]="$j,$cod,'$fact-$incd[$i]','$conceptod[$i]','$ccd[$i]','$nitd[$i]','$obsd[$i]',-$valord[$i],'c-calidad')";
				}
			}
			
			
			if($total == 1)
			{
				$total[1]= "PARCIAL <input type='checkbox'></td><td align='left'><font size=2 face='arial'>TOTAL <input type='checkbox' checked></td>";
				$Reem[1]= "<b>REEPLAZA POR: </b>".$reem;
			}
			else
			{
				$total[1]= "PARCIAL <input type='checkbox' checked></td><td align='left'><font size=2 face='arial'>TOTAL <input type='checkbox'></td>";
				$Reem[1]="";
				$reem="NOTA PARCIAL";
			}
			echo $total[1];
			/*Ingreso en la ttabla de solicitud de Notas Credito*/
			$fecha_act[1]=DATE('Y')."-".DATE('m')."-".DATE('d');
			$hora_act=date('h').":".date('m').":".date('s');
			$query= "insert into calidad_000007 (Medico,Fecha_data,Hora_data,Codigo,Factura,Reemplaza,Valor_total,Solicita,Seguridad) ";
			$query= $query."values ('calidad','$fecha_act[1]','$hora_act',$cod,'$fact','$reem',$valor_total[1],'$sol','c-calidad')";
			$err=mysql_query($query,$conex);
			if((integer)(mysql_affected_rows($conex)) != 1)
			{
				$error="NO FUE INGRESADA A MATRIX LA SOLICITUD DE NOTA CREDITO # ".$cod."<BR> ";
			}
			else
			{
				/*Nota Credito
					Insertar Item de Solicitud
					Update de la Inconsistencia Nota Credito Diligenciada*/
				$QUERYf="insert into calidad_000008 (Medico,Fecha_data,Hora_data,Codigo,Codigo_nota_credito,Factura_cod_inc,Concepto,Cc,Nit_tercero,Observaciones,Valor,Seguridad) values ('calidad','$fecha_act[1]','$hora_act',";
				for($i=0;$i<=$num;$i++)
				{
					if(isset($ch[$i]) and $ch[$i] == 'on')
					{
						$j++;
						$queryf[$i]=$QUERYf.$queryf[$i];
						$err=mysql_query($queryf[$i],$conex);
						//echo $queryf[$i];
						if((integer)(mysql_affected_rows($conex)) != 1)
						{
							$error=$error."NO FUE INGRESADO EL ITEM DE LA SOLICITUD DE NOTA CREDITO CORRESPONDIENTE A LA INC ".$inc[$i]." (CREDITO)<BR>YA SE HABIA HECHO UNA SOLICITUD PARA ESTA INCONSISTENCIA<BR>";
						}
						else
						{
							$query= "update calidad_000003 set Tipo_nota='04-NOTA CREDITO DILIGENCIADA' where Factura='".$fact."' and Codigo='".$inc[$i]."'";
							$err=mysql_query($query,$conex);
							if((integer)(mysql_affected_rows($conex)) != 1)
							{
								$error=$error."NO FUE MODIFICADO EL REGISTRO DE LA NOTA CREDITO PARA LA INC # ".$inc[$i]." (CREDITO)<BR>DEBE HACRELO MANUALMENTE";
							}
						}
					}
				}
				/*Fin nota credito*/
				/*Nota DEBITO
					Insertar Item de Solicitud
					Update de la Inconsistencia Nota Debito Diligenciada*/
				$QUERYf="insert into calidad_000008 (Medico,Fecha_data,Hora_data,Codigo,Codigo_nota_credito,Factura_cod_inc,Concepto,Cc,Nit_tercero,Observaciones,Valor,Seguridad) values ('calidad','$fecha_act[1]','$hora_act',";
				for($i=0;$i<=$numd;$i++)
				{
					if(isset($chd[$i]) and $chd[$i] == 'on')
					{
						$j++;
						$queryf[$i]=$QUERYf.$querydf[$i];
						$err=mysql_query($queryf[$i],$conex);
						//echo $queryf[$i];
						if((integer)(mysql_affected_rows($conex)) != 1)
						{
							$error=$error."NO FUE INGRESADO EL ITEM DE LA SOLICITUD DE NOTA CREDITO CORRESPONDIENTE A LA INC ".$incd[$i]." (DEBITO)<BR>YA SE HABIA HECHO UNA SOLICITUD PARA ESTA INCONSISTENCIA<BR>";
						}
						else
						{
							$query= "update calidad_000003 set Tipo_nota='06-NOTA DEBITO DILIGENCIADA' where Factura='".$fact."' and Codigo='".$incd[$i]."'";
							$err=mysql_query($query,$conex);
							if((integer)(mysql_affected_rows($conex)) != 1)
							{
								$error=$error."NO FUE MODIFICADO EL REGISTRO DE LA NOTA CREDITO PARA LA INC # ".$incd[$i]."(DEBITO)<BR>DEBE HACRELO MANUALMENTE";
							}
						}
					}
				}
				/*Fin nota debito*/
				/*Termino el ingreos de info a tablas y la modificación de registros*/
				$ini=strpos($sol,"-");
				$string=(substr($sol,$ini+1));
				include_once('tipo_titulo.php');
				$sol=array(1=> $string);
				$i=1;
				$copia="";
				include_once("calidad/notas_credito3.php");//Se encarga de la impresión de la nota
				/*UPDATE `calidad_000003` set Tipo_nota='02-CREDITO';*/
			}
		}
		echo "<b><CENTER><font size=4 face='arial' color='#FF0000'>".$error;
		
	}
include_once("free.php");
}
?>
</body>