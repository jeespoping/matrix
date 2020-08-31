<title>BORRADO NOTAS V1.00</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");

/*	*************************************************
	*	REPORTE DE AUDITORIA DE FACTURAS 			*
	*************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

	IF(!isset($fact))
	{
		echo "<form action='' method=post>";
		echo "<center><table border=0 width=300>";
		echo "<tr><td align=center colspan=2><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=2>SEGUIMIENTO DE FACTURAS Y AUDITORIA</td></tr>";
		echo"<tr><td align=center bgcolor=#cccccc >FACTURA N°:</TD><td align=center bgcolor=#cccccc ><input type='input' name='fact'></td></tr>";
		echo"<tr><td align=center bgcolor=#cccccc colspan=2><input type='submit' value='ACEPTAR'></td></tr></form>";
	}
	else if (!isset($entidad))
	{
		echo "<form action='' method=post>";
		$query = "SELECT * FROM calidad_000007 where factura='".$fact."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			/*Existen solicitudes para la factura*/
			if(!isset($ent))
			{
				/*traigo la informacion correspondiente a la factura*/
				$query1 = "SELECT Responsable,Ano_mes,Fecha FROM calidad_000001 where factura='".$fact."' ";
				$err1 = mysql_query($query1,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1>0)
				{
					$row1=mysql_fetch_array($err1);
					$entidad=$row1['Responsable'];
					echo "<input type='hidden' name='fecha' and value='".$row1['Fecha']."'> ";
					echo "<input type='hidden' name='entidad' and value='$entidad'> ";//pOR QUE
				}
			}
			echo "<center><table border=0 width=340>";
			echo "<tr><td align=center colspan=2><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
			echo "<tr><td align=center colspan=2><b>FACTURA # </b>".$fact."</td></tr>";
			echo "<tr><td align=center colspan=2  bgcolor=#cccccc><b>ENTIDAD <input type='text' name='entidad' value='$entidad' size='52'></td></tr>";
			for($i=1; $i<=$num; $i++)
			{
				$row=mysql_fetch_array($err);
				/*Desplegamos las solicitude en pantalla con sus datos para que escojan la que dessen*/
				$ini=strpos($row['Solicita'],"-");
				$string=(substr($row['Solicita'],$ini+1));
				include_once('tipo_titulo.php');
				echo "<tr><td bgcolor=#cccccc colspan='2'><input type='checkbox' name='ch[".$i."]' checked><b>SOLICITUD ".$row["Codigo"]."</td></tr>";
				echo "<tr><td bgcolor=#cccccc>Fecha:</td><td bgcolor=#cccccc><input type='text' value='".$row["Fecha_data"]."' name='fecha[".$i."]' size='8'></td></tr>";
				echo "<tr><td bgcolor=#cccccc>Valor Total:</td><td bgcolor=#cccccc><input type='text' value='".$row["Valor_total"]."' name='valor_total[".$i."]'size='8'></td></tr>";
				if($row['Reemplaza'] != "NOTA PARCIAL" )
				{
					echo "<tr><td bgcolor=#cccccc >Total<input type='radio' name='total[$i]' value='1'  checked>";
					echo "<td bgcolor=#cccccc  >Parcial <input type='radio' name='total[$i]' value='0'  ></tr>";
					echo "<tr><td bgcolor=#cccccc >Reemplaza Por </td><td bgcolor=#cccccc><input type='text' name='reem[$i]' value='".$row['Reemplaza']."' size='11'></td></tr>";
				}
				else
				{
					echo "<tr><td bgcolor=#cccccc >Total<input type='radio' value='1' name='total[$i]' >";
					echo "<td bgcolor=#cccccc  >Parcial <input type='radio' value='0' name='total[$i]' checked></tr>";
				}
				echo "<tr><td bgcolor=#cccccc>Solicita:</td><td bgcolor=#cccccc><input type='text' value='$string' name='sol[".$i."]' size='30'></td></tr>";
				echo "<input type='hidden' name='cod[".$i."]' value='".$row["Codigo"]."'>";
			}
			echo "<input type='hidden' name='nums' value='".$num."'>";
			echo "<input type='hidden' name='fact' value='".$fact."'>";
			echo "<TR><TD COLSPAN=2 align='center' BGCOLOR=#CCCCCC><input type='submit' name='aceptar' value='ACEPTAR'></TR></TD>";
		}
		else
			echo "NO EXISTEN SOLICITUDES DE NOTA CREDITO PARA ESTA FACTURA";
	}
	else if (!isset($numb))
	{
		/*Imprimo las solicitudes en Pantalla*/
		echo "<form action='' method=post>";
		echo "<input type='hidden' name='fact' value='".$fact."'>";
		echo "<input type='hidden' name='numb' value='".$nums."'>";
		echo "<input type='hidden' name='entidad' value='".$entidad."'>";
		for($i=1; $i<=$nums;$i++)
		{
			if(isset($ch[$i]) and $ch[$i] == 'on')
			{
				/*En nums tengo la información del numero de solicitudes a imprimir*/
				echo "<input type='hidden' name='cod[".$i."]' value='".$cod[$i]."'>";
				$query= "select * from calidad_000008 where Codigo_nota_credito='$cod[$i]' and Factura_cod_inc like '$fact-%' ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num>0)
				{
					$fecha_act[$i]=$fecha[$i];
					if($total[$i] == 1)
					{
						$total[$i]= "PARCIAL <input type='checkbox'></td><td align='left'><font size=2 face='arial'>TOTAL <input type='checkbox' checked></td>";
						$Reem[$i]= "<b>REEPLAZA POR: </b>".$reem[$i];
					}
					else
					{
						$total[$i]= "PARCIAL <input type='checkbox' checked></td><td align='left'><font size=2 face='arial'>TOTAL <input type='checkbox'></td>";
						$Reem[$i]="";
					}
					$table[$i]="";
					$valor_credito[$i]=0;
					$valor_total[$i]=0;
					/* Proceso Para el numero de items a diligenciar*/
					for($s=1;$s<=$num;$s++)
					{
						$row=mysql_fetch_array($err);
						$inia=strpos($row['Factura_cod_inc'],"-");
						$query1= "select Causa,Tipo_nota from calidad_000003 where Factura='$fact' and Codigo='".substr($row['Factura_cod_inc'],$inia+1)."'";
						$err1 = mysql_query($query1,$conex);
						$row1=mysql_fetch_array($err1);
						$inia=strpos($row1["Causa"],"-");
						$inib=strpos($row1["Causa"],"-",$inia +1 );
						$row1['Causa']=substr($row1["Causa"],$inia+1,$inib - $inia -1);
						if($row1["Tipo_nota"] == "04-NOTA CREDITO DILIGENCIADA")
						{
							/*Sumo los valores de Credito y cambio el tipo*/
							$valor_total[$i]=$valor_total[$i]+$row["Valor"];
							$valor_credito[$i]=$valor_credito[$i]+$row["Valor"];
							$tipo="Credito";
							$font="";
							$row1["Tipo_nota"]=2;
							echo "<input type='hidden' name='tipo_n[".$i."][".$s."]' value='02-CREDITO'>";
						}
						ELSE
						{
							$font="<i>";
							$row["Valor"]=$row["Valor"]*(-1);
							$valor_total[$i]=$valor_total[$i]-$row["Valor"];
							$tipo="Debito";
							$debito="DEBITO";
							$row1["Tipo_nota"]=3;
							echo "<input type='hidden' name='tipo_n[".$i."][".$s."]' value='03-DEBITO'>";
						}
						$table[$i]=$table[$i]."<tr><td ><font size=2 face='arial'>$font<b>".$tipo."</b></td>";//Credito Debito
						$table[$i]=$table[$i]."<td ><font size=2 face='arial'>$font".$row['Concepto']."</td>";//concepto
						$table[$i]=$table[$i]."<td ><font size=2 face='arial'>$font".$row['Cc']."</td>";//CC
						$table[$i]=$table[$i]."<td><font size=2 face='arial'>$font".$row['Nit_tercero']."</td>";//nit
						$table[$i]=$table[$i]."<td><font size=2 face='arial'>$font".$row1['Causa']."</td>";//causa
						$table[$i]=$table[$i]."<td><font size=2 face='arial'>$font".$row['Observaciones']."</td>";
						$table[$i]=$table[$i]."<td><font size=2 face='arial'>$font".$row['Valor']."</TD></tr>";
						echo "<input type='hidden' name='fact_inc[$i][$s]' value='".$row['Factura_cod_inc']."'>";
						if(!isset($debito))
							$debito="";
					}
					/*Proceso para el número de items por solicitud*/
					$copia=" BORRAR ";
					echo "<B><CENTER><font size=4 face='arial' color='#FF0000'>¿ESTA SEGURO DE QUE DESEA BORRAR LA SOLICITUD # $cod[$i] ?<br>SI ESTOY SEGURO <input type='checkbox' checked name='borr[$i]'>";
					include_once('calidad/notas_credito3.php');
					echo "<input type='hidden' name='items[".$i."]' value='".$num."'>";
				}
			}/*Termino ciclo de for de impresión*/
		}
	echo "<CENTER><input type='submit' name='ACEPTAR' value='ACEPTAR'>";
	}/*Termino la impresion en pantalla*/
	else
	{
		/*Borro las solicitudes a borrar*/
		for($i=1; $i<=$numb;$i++)
		{
			if(isset($borr[$i]) and $borr[$i] == 'on')
			{
				$query= "delete from calidad_000008 where Codigo_nota_credito='$cod[$i]' and Factura_cod_inc like '$fact-%'";
				$err = mysql_query($query,$conex);
				if((integer)(mysql_affected_rows($conex)) >= 1)
				{
					$query= "delete from calidad_000007 where Codigo='$cod[$i]' and Factura='$fact'";
					$err = mysql_query($query,$conex);
					if((integer)(mysql_affected_rows($conex)) < 1)
						echo "<B><CENTER><font size=4 face='arial' color='#FF0000'>NO FUE POSIBLE BORRAR LA SOLICITUD DE CREDITO $cod[$i]";
					else
					{
						$error="";
						for($s=1;$s<=$items[$i];$s++)
						{
							$ini=strpos($fact_inc[$i][$s],'-');
							$query= "update calidad_000003 set Tipo_nota='".$tipo_n[$i][$s]."' where Factura='".$fact."' and Codigo='".substr($fact_inc[$i][$s],$ini+1)."'";
							$err=mysql_query($query,$conex);
							if((integer)(mysql_affected_rows($conex)) != 1)
								$error= $error."<B><CENTER><font size=4 face='arial' color='#FF0000'>NO FUE MODIFICADO EL REGISTRO DE LA NOTA CREDITO PARA LA INC # ".substr($fact_inc[$i][$s],$ini+1)."<BR>DEBE HACRELO MANUALMENTE";
						}
						if($error=="")
							echo "<B><CENTER><font size=4 face='arial' color='#FF0000'>LA SOLICITUD DE CREDITO $cod[$i] FUE BORRADA EXITOSAMENTE!!!";
						else
							echo "<B><CENTER><font size=4 face='arial' color='#FF0000'><BR>LA SOLICITUD DE CREDITO $cod[$i] NO FUE BORRADA EXITOSAMENTE!!! <br> POR FAVOR HAGA LOS AJUSTES MANUALES NECESARIOS PARA COMPLETAR EL PROCESO DE BORRADO";
						ECHO $error;
							
					}
				}
				else
				{
					echo "<B><CENTER><font size=4 face='arial' color='#FF0000'>NO FUE POSIBLE BORRAR LOS ITEMS DE LA SOLICITUD DE CREDITO $cod[$i]";
				}
			}
		}
		
	}/*Termino el borrado*/
}
include_once("free.php");
?>
</body>
</html>