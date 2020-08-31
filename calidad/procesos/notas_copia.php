<title>NOTAS CREDITO V1.01</title>
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
	else 
	{
		/*Imprimo las solicitudes en Pantalla*/
		for($i=1; $i<=$nums;$i++)
		{
			if(isset($ch[$i]) and $ch[$i] == 'on')
			{
				$valor_credito[$i]=0;
				$valor_total[$i]=0;
				/*En nums tengo la información del numero de solicitudes a imprimir*/
				$query= "select * from calidad_000008 where Codigo_nota_credito='$cod[$i]' and Factura_cod_inc like '$fact-%'";
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
						}
						ELSE
						{
							$font="<i>";
							$row["Valor"]=$row["Valor"]*(-1);
							$valor_total[$i]=$valor_total[$i]-$row["Valor"];
							$tipo="Debito";
							$debito="DEBITO";
						}
						
						if(!isset($debito))
							$debito="";
						$table[$i]=$table[$i]."<tr><td ><font size=2 face='arial'>$font<b>".$tipo."</b></td>";//Credito Debito
						$table[$i]=$table[$i]."<td ><font size=2 face='arial'>$font".$row['Concepto']."</td>";//concepto
						$table[$i]=$table[$i]."<td ><font size=2 face='arial'>$font".$row['Cc']."</td>";//CC
						$table[$i]=$table[$i]."<td><font size=2 face='arial'>$font".$row['Nit_tercero']."</td>";//nit
						$table[$i]=$table[$i]."<td><font size=2 face='arial'>$font".$row1['Causa']."</td>";//Causa
						$table[$i]=$table[$i]."<td><font size=2 face='arial'>$font".$row['Observaciones']."</td>";
						$table[$i]=$table[$i]."<td><font size=2 face='arial'>$font".$row['Valor']."</TD></tr>";
					}
					/*Proceso para el número de items por solicitud*/
					$copia="COPIA ";
					include_once('calidad/notas_credito3.php');
				}
			}/*Termino ciclo de for de impresión*/
		}
	}/*Termino la impresion en pantalla*/
}
include_once("free.php");
?>
</body>
</html>