 <title>NOTAS CREDITO V1.00</title>
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
		echo "<form action='notas_credito.php' method=post>";
		echo "<center><table border=0 width=300>";
		echo "<tr><td align=center colspan=2><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=2>SEGUIMIENTO DE FACTURAS Y AUDITORIA</td></tr>";
		echo"<tr><td align=center bgcolor=#cccccc >FACTURA N°:</TD><td align=center bgcolor=#cccccc ><input type='input' name='fact'></td></tr>";
		echo"<tr><td align=center bgcolor=#cccccc colspan=2><input type='submit' value='ACEPTAR'></td></tr></form>";
	}
	else if (!isset($ent))
	{
		echo "<form action='notas_credito.php' method=post>";
		$query = "SELECT * FROM calidad_000003 where factura='".$fact."'  and Tipo_nota='02-CREDITO'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if($num>0)
		{
			/*Si existen facturas para hacerles nota credito*/
			if(!isset($mes))
			{
				/*traigo la informacion correspondiente a la factura*/
				$query1 = "SELECT Responsable,Ano_mes,Fecha FROM calidad_000001 where factura='".$fact."' ";
				//echo $query1;
				$err1 = mysql_query($query1,$conex);
				$num1 = mysql_num_rows($err1);
				if($num1>0)
				{
					$row1=mysql_fetch_array($err1);
					$ent=$row1['Responsable'];
					$fecha=$row1['Fecha'];
					$ini=strrpos($row1['Ano_mes'],"/");
					$ano=substr($row1['Ano_mes'],0,$ini);
					$mes=substr($row1['Ano_mes'],$ini+1);
					echo "<input type='hidden' name='fecha' and value='".$row1['Fecha']."'> ";
					echo "<input type='hidden' name='ent' and value='$ent'> ";
					echo "<input type='hidden' name='ano' and value='$ano'> ";
					echo "<input type='hidden' name='mes' and value='$mes'> ";
				}
			}
			echo "<center><table border=0 width=340>";
			echo "<tr><td align=center colspan=2><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
			echo "<tr><td align=center colspan=2><b>FACTURA # </b>".$fact."</td></tr>";
			echo "<tr><td align=center colspan=2  bgcolor=#cccccc>ENTIDAD <input type='text' name='entidad' value='$ent' size='55'></td></tr>";
			echo "<tr><td bgcolor=#cccccc>TOTAL<input type=radio name='total' value='1'>";
			echo "<td  bgcolor=#cccccc >PARCIAL <input type=radio name='total' value='0'></td><td align=center></tr>";
			echo "<tr><td bgcolor=#cccccc>REEMPLAZA POR </td><td bgcolor=#cccccc><input type='text' name='reem'></td></tr>";
			echo "<tr><td bgcolor=#FFFFFF colspan='2'align='center'><BR><b>NOTAS CREDITO </td></tr>";
			/*Traigo los diferentes conceptos para cada factura*/
			$select="";
			$conex_o = odbc_connect('facturacion','','');
			$queryo= "select distinct(movdetcon) from famovdet where movdetano='$ano' and movdetmes='$mes' and movdetfec='$fecha' and movdetdoc='$fact' ";
			$err_o = odbc_do($conex_o,$queryo);
			$campos= odbc_num_fields($err_o);
			While (odbc_fetch_row($err_o))
			{
				$odbc=array();
				$odbc[1]=odbc_result($err_o,1);
				$select=$select."<option>$odbc[1]</option>";
			}
			/*termine de traer los conceptos*/
			/*Se crea un formulario para que se modifique la información existente
				y para que se ingrese la que hace falta*/
			for($i=1; $i<=$num; $i++)
			{
				$row=mysql_fetch_array($err);
				echo "<tr><td bgcolor=#cccccc colspan='2'><input type='checkbox' name='ch[".$i."]' checked><b>NOTA CREDITO INC. # ".$row["Codigo"]."</td></tr>";
				echo "<tr><td bgcolor=#cccccc>Concepto:</td><td bgcolor=#cccccc><select name='concepto[".$i."]'>".$select;
				echo "</select></td></tr>";
				echo "<tr><td bgcolor=#cccccc>Valor:</td><td bgcolor=#cccccc><input type='text' value='".$row["Valor_nota"]."' name='valor[".$i."]'></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=left  colspan='2'><b>Observaciones</b>";
				echo "<br><textarea cols='29' rows='4' name='obs[".$i."]'>".$row["Observaciones"]."</textarea></TD>";
				echo "<input type='hidden' name='inc[".$i."]' value='".$row["Codigo"]."'>";
				$inia=strpos($row["Causa"],"-");
				$inib=strpos($row["Causa"],"-",$inia +1 );
				echo "<input type='hidden' name='causa[".$i."]' value='".substr($row["Causa"],$inia+1,$inib - $inia -1)."'>";
			}
			echo "<input type='hidden' name='num' value='".$num."'>";
			echo "<input type='hidden' name='fact' value='".$fact."'>";
			echo "<TR><TD COLSPAN=2 align='center' BGCOLOR=#CCCCCC><input type='submit' name='aceptar' value='ACEPTAR'></TR></TD>";
			
			
			odbc_close($conex_o);
			odbc_close_all();
		}
		else
			echo "NO EXISTEN NOTAS CREDITO SIN DILIGENCIAR PARA ESTA FACTURA";
	}
	else 
	{
		/*Buscar notas debito ultimar deltalles de terceros y valor Notas credito*/
		echo "<form action='notas_credito1.php' method=post>";
		echo "<center><table border=0 width=340>";
		echo "<tr><td align=center colspan=2><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
		echo "<tr><td align=center colspan=2><b>FACTURA</b> # ".$fact."</td></tr>";
		echo "<tr><td align=center   bgcolor=#cccccc colspan=2>ENTIDAD <input type='text' name='entidad' value='$ent' size='50'></td></tr>";
		if($total == 1)
		{
			echo "<tr><td bgcolor=#cccccc >TOTAL<input type=radio name='total' value='1' checked>";
			echo "<td bgcolor=#cccccc  >PARCIAL <input type=radio name='total' value='0'></tr>";
			echo "<tr><td bgcolor=#cccccc >REEMPLAZA POR </td><td bgcolor=#cccccc><input type='text' name='reem' value='$reem'></td></tr>";
		}
		else
		{
			echo "<tr><td bgcolor=#cccccc >TOTAL<input type=radio name='total' value='1' >";
			echo "<td bgcolor=#cccccc  >PARCIAL <input type=radio name='total' value='0' checked></tr>";
		}
		echo "<tr><td bgcolor='#cccccc' colspan='2' align='center'>SOLICITANTE<br><select name='sol'>";
		/*Traigo la seleccion en donde estan los autorizados para hacer la solicitud*/
		$query="select Subcodigo,Descripcion From det_selecciones where medico='calidad' and codigo='06'";
		$err = mysql_query($query,$conex);
		$numo = mysql_num_rows($err);
		if($numo>0)
		{
			for($i=1; $i<=$numo; $i++)
			{
				$row=mysql_fetch_array($err);
				echo "<option>$row[0]-$row[1]</option>";
			}
		}
		echo "</td></tr>";
		/*Termine de traer los solicitantes autorizados*/
		echo "<input type='hidden' name='fecha' and value='$fecha'> ";
		echo "<input type='hidden' name='ent' and value='$ent'> ";
		echo "<input type='hidden' name='ano' and value='$ano'> ";
		echo "<input type='hidden' name='mes' and value='$mes'> ";
		echo "<input type='hidden' name='fact' and value='$fact'> ";
		/*Genero el código de la solicitud de Nota Credito  */
		$query="select MAX(Codigo) from calidad_000007 where factura='$fact'";
		$err = mysql_query($query,$conex);
		$numo = mysql_num_rows($err);
		if($numo>0)
		{
			$row=mysql_fetch_row($err);
			$cod=$row[0]+1;
			/*Si ya existe una solicitud de nota para esta factura
				debo buscar para que concepto y que tercero y sumarlos*/
			$query1="select Cc,Concepto,Nit_tercero,Valor from calidad_000008 where Factura_cod_inc like '$fact-%' order by Concepto,Nit_tercero";
			$err1 = mysql_query($query1,$conex);
			$num1 = mysql_num_rows($err1);
			if($num1>0)
			{
				for($i=1;$i<=$num1;$i++)
				{
					$row=mysql_fetch_row($err1);
					$string="$row[0],$row[1],$row[2]";
					if(isset($max[$string]))
						$max[$string]=$max[$string]+$row[3];
					else
						$max[$string]=$row[3];
					//echo "max[$string]=$max[$string]<br>";
				}
			}
		}
		else
			$cod=1;
		echo "<input type='hidden' name='cod' value='$cod'>";
			/*Termine de generar el código*/
		$j=0;
		echo "<tr><td bgcolor=#FFFFFF colspan='2'align='center'><BR><b>NOTAS CREDITO</td></tr>";
		for($i=1;$i<=$num;$i++)
		{
			if(isset($ch[$i]) and $ch[$i] == 'on')
			{
				echo "<tr><td bgcolor=#cccccc colspan='2'><input type='checkbox' name='ch[".$i."]' checked><b>NOTA CREDITO INC. # $inc[$i]</td></tr>";
				echo "<tr><td bgcolor=#cccccc>Concepto:</td><td bgcolor=#cccccc>$concepto[$i]<input type='hidden' name='concepto[".$i."]' value='$concepto[$i]'></td></tr>";
				if(isset($concepto[$i]))
				{
					echo "<tr><td bgcolor=#cccccc>CC-NIT-Valor(máx) (Tercero):</td><td bgcolor=#cccccc>";
					$conex_o = odbc_connect('facturacion','','');
					$queryo="select movdetcco,movdetnit,movdetval from famovdet where movdetano='$ano' and movdetmes='$mes' and movdetfec='$fecha' and movdetdoc='$fact' and movdetcon='$concepto[$i]'";
					echo "<select  name='tercero[".$i."]'>";
					$err_o = odbc_do($conex_o,$queryo);
					$campos= odbc_num_fields($err_o);
					While (odbc_fetch_row($err_o))
					{
						$odbc=array();
						for($m=1;$m<=$campos;$m++)
						{
							$odbc[$m]=odbc_result($err_o,$m);
						}
						$string=trim($odbc[1]).",".trim($concepto[$i]).",".trim($odbc[2]);
						if(isset($max[$string]))
						{
							$odbc[3]=$odbc[3]-$max[$string];
							if($odbc[3]>0)
								echo "<option>$odbc[1]-$odbc[2]-$".$odbc[3]."</option>";
							else
								echo "<option>$odbc[1]-$odbc[2]-<B>NO ES POSIBLE VALOR MAX SOLICITABLE=-0</B></option>";
						}
						else
							echo "<option>$odbc[1]-$odbc[2]-$".$odbc[3]."</option>";
					}
					echo "</select></td></tr>";
										
					odbc_close($conex_o);
					odbc_close_all();
				}
				echo "<tr><td bgcolor=#cccccc>Valor:</td><td bgcolor=#cccccc><input type='text' value='$valor[$i]' name='valor[".$i."]'></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=left  colspan='2'><b>Observaciones</b>";
				echo "<br><textarea cols='29' rows='4' name='obs[".$i."]'>$obs[$i]</textarea></TD>";
				echo "<input type='hidden' name='inc[".$i."]' value='$inc[$i]'>";
				echo "<input type='hidden' name='causa[".$i."]' value='$causa[$i]'>";
				//echo "<br>$causa[$i]<br>";
				$j++;
			}
		}
		echo "<input type='hidden' name='num' value='".$num."'>";
		/*NOTAS DEBITO  de la factura*/
		$query = "SELECT * FROM calidad_000003 where factura='".$fact."'  and Tipo_nota='03-DEBITO'";
		$err = mysql_query($query,$conex);
		$numd = mysql_num_rows($err);
		if($numd>0)
		{
			echo "<tr><td bgcolor=#FFFFFF colspan='2'align='center'><BR><b>NOTAS DEBITO </td></tr>";
			/*Se crea un formulario para que se modifique la información existente
				y para que se ingrese la que hace falta*/
			for($i=1; $i<=$numd; $i++)
			{
				$row=mysql_fetch_array($err);
				echo "<tr><td bgcolor=#cccccc colspan='2'><input type='checkbox' name='chd[".$i."]'><b>NOTA DEBITO INC. # ".$row["Codigo"]."</td></tr>";
				echo "<tr><td bgcolor=#cccccc>Concepto:</td><td bgcolor=#cccccc><input type='text' name='conceptod[".$i."]'></td></tr>";
				echo "<tr><td bgcolor=#cccccc>CC:</td><td bgcolor=#cccccc><input type='text' name='ccd[".$i."]'></td></tr>";
				echo "<tr><td bgcolor=#cccccc>NIT:</td><td bgcolor=#cccccc><input type='text' name='nitd[".$i."]'></td></tr>";
				echo "<tr><td bgcolor=#cccccc>Valor:</td><td bgcolor=#cccccc><input type='text' value='".$row["Valor_nota"]."' name='valord[".$i."]'></td></tr>";
				echo "<tr><td bgcolor=#cccccc align=left  colspan='2'><b>Observaciones</b>";
				echo "<br><textarea cols='29' rows='4' name='obsd[".$i."]'>".$row["Observaciones"]."</textarea></TD>";
				echo "<input type='hidden' name='incd[".$i."]' value='".$row["Codigo"]."'>";
				$inia=strpos($row["Causa"],"-");
				$inib=strpos($row["Causa"],"-",$inia +1 );
				echo "<input type='hidden' name='causad[".$i."]' value='".substr($row["Causa"],$inia+1,$inib - $inia -1)."'>";
			}
			echo "<input type='hidden' name='numd' value='".$numd."'>";
			
		}
		else
			echo "NO EXISTEN NOTAS DEBITO SIN DILIGENCIAR PARA ESTA FACTURA";
			
		/*FIN NOTAS DEBITO*/	
		
		echo "<TR><TD COLSPAN=2 align='center' BGCOLOR=#CCCCCC><input type='submit' name='aceptar' value='ACEPTAR'></TR></TD></form></table>";
	}
}
include_once("free.php");
//UPDATE `calidad_000003` SET Tipo_nota='02-CREDITO' WHERE Tipo_nota like '04-%'
?>
</body>
</html>