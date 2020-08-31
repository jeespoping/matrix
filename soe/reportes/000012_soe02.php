<html>

<head>
  <title>CONTRATO</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
  
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
	$key = substr($user,2,strlen($user));
	

	

    
	
	$exp=explode("-",$paci);	
	$exp1=explode("-",$odonto);
	$exp2=explode("/",$trata);
	
	
	$n= count ($exp2);
	//echo $n;
	
	for ($i=1;$i<$n;$i++){
		$exp3=explode("-",$exp2[$i]);
		
		if($i==1)
		$trat1=$exp3[1];
		else
		$trat1=$trat1.', '.$exp3[1];
	}
	
	
	
	$query="select Valor_presupuesto, Descuento FROM	soe1_000012 where Codigo=".$cod." and Identificacion='".$paci."'and Odontologo='".$odonto."'";  // para el valor del presupuesto
	$soe = mysql_query($query,$conex);
	$num = mysql_num_rows($soe);
	if ($num > 0)
							{
     							$row=mysql_fetch_row($soe);
								 $valor= $row[0];
								 $dcto= $row[1];
     						} 				
	
     						$exp3=explode("-",$dcto);
     						
     $desc=($valor*$exp3[0])/100;				// el valor del presupuesto para el dcto en word
     $val=$valor-$desc;						
     $ppesto=number_format( $val,0, '', "."); 		
     				
	//echo "$ppesto<br>";
	//echo "$exp3[0]";
	// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(paciente medico y fecha)
	echo "<form action='000012_soe01.php' method=post>";
	echo "<center><table border=0 width=700>";
	echo "<tr><td align=center colspan=2><b><font color=#000066>PROMOTORA MEDICA LAS AMERICAS</font></b></td></tr>";
	echo "<tr><td align=center colspan=2><font color=#000066>UNIDAD ODONTOLOGICA</font></td></tr>";
	echo "<tr></tr>";
	echo "<input type='hidden' name='fecha' value='".$fech."'>";
	echo "<input type='hidden' name='codigo' value='".$cod."'>";
	echo "<input type='hidden' name='vppto' value='$".$ppesto."'>";
	echo "<tr><td bgcolor=#cccccc><font color=#000066>ODONTOLOGO: </font></td>";	
	echo "<td bgcolor=#cccccc colspan=1><input type='hidden' name='odonto' value='".$exp1[1]."' size='50'>".$exp1[1]."</td></tr>";
	echo "<tr><td bgcolor=#cccccc  colspan=1><font color=#000066>PACIENTE: </font></td>";	
	echo "<td bgcolor=#cccccc colspan=1><input type='hidden' name='paci' value='".$exp[1]." ".$exp[2]." ".$exp[3]." ".$exp[4]."' size='50'>".$exp[1]." ".$exp[2]." ".$exp[3]." ".$exp[4]."</td></tr>";
	echo "<tr><td bgcolor=#cccccc  colspan=1><font color=#000066>Nº DE HISTORIA: </font></td>";	
	echo "<td bgcolor=#cccccc colspan=1><input type='hidden' name='nuhis' size='50' value='".$exp[0]."'>".$exp[0]."</td></tr>";
	echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>ESPECIALIDAD: </font></td>";	
	echo "<td bgcolor=#cccccc colspan=1><select name='espe' ><option>Ortodoncia</option><option>Odontopediatria</option><option>Estetica</option><option>Endodoncia</option><option>Periodoncia</option><option>Rehabilitacion</option><option>Odontologia General</option><option>Implantes</option><option>Estomatologia</option></td></tr>";
	echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>TRATAMIENTO: </font></td>";	
	echo "<td bgcolor=#cccccc colspan=1><input type='hidden' name='trata' size='50'value='".$trat1."'>".$trat1."</td></tr>";
	echo "<tr><td bgcolor=#cccccc colspan=2><font color=#000066><b>EL PACIENTE SE RESPONSABILIZA DE CUMPLIR DEBIDAMENTE EN TODAS LAS INDICACIONES QUE RECIBA DE PARTE DEL ESPECIALISTA EN: </b></font></td></tr>";
	echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>EL USO DE: </font></td>";	
	echo "<td bgcolor=#cccccc colspan=1><textarea ALIGN='CENTER' cols='50' ROWS='6' name='uso' ></textarea></td></tr>";
	echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>EL MANTENIMIENTO DE: </font></td>";	
	echo "<td bgcolor=#cccccc colspan=1><textarea ALIGN='CENTER' cols='50' ROWS='6' name='mantenimiento' ></textarea></td></tr>";
	echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>EL TRATAMIENTO INCLUYE: </font></td>";	
	echo "<td bgcolor=#cccccc colspan=1><textarea ALIGN='CENTER'  cols='50' ROWS='6' name='incluye'></textarea></td></tr>";
	echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>EL TRATAMIENTO NO INCLUYE: </font></td>";	
	echo "<td bgcolor=#cccccc colspan=1><textarea ALIGN='CENTER'  cols='50' ROWS='6' name='noincluye'></textarea></td></tr>";
	echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>TIEMPO DEL TRATAMIENTO (en meses): </font></td>";	
	echo "<td bgcolor=#cccccc colspan=1><textarea ALIGN='CENTER'  cols='50' ROWS='6' name='ttrata'></textarea></td></tr>";
	echo "<tr><td bgcolor=#cccccc colspan=1><font color=#000066>SI SE CUMPLE ANTES DE (en meses): </font></td>";	
	echo "<td bgcolor=#cccccc colspan=1><textarea ALIGN='CENTER'  cols='50' ROWS='6' name='antes'></textarea></td></tr>";
	echo "<tr><td align='center' bgcolor=#cccccc colspan=2><input type='submit' name='aceptar' value='ACEPTAR'></td><tr>";	
	echo "<tr></tr>";
	
	include_once("free.php");
}
?>
</body>
</html>