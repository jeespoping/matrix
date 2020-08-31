<?php
include_once("conex.php");
	

				    
	

	if ($wnrofac != "")
	{
		echo "<left><table border=0>";
		$query = " SELECT count(*)  FROM ".$wbasedato."_000016 ";
		$query .="  WHERE Vennfa = '".$wnrofac."'";
		$err = mysql_query($query,$conex) or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
		   	$row = mysql_fetch_array($err);
		   	$numfac=$row[0];
	   	}
		$query = " SELECT ".$wbasedato."_000018.Fecha_data, ".$wbasedato."_000018.Hora_data, Empnom, Empnit, Fenval, Fenviv, Fencop, Fencmo FROM ".$wbasedato."_000018,".$wbasedato."_000024 ";
		$query .="  WHERE Fenfac = '".$wnrofac."'";
		//$query .="        and mid(Fentip,1,locate('-',Fentip)-1) = Empcod ";
		$query .="        and fencod = Empcod "; //cambia Gustavo 25 de mayo , ya que lida lo necesita urgente.
		$err = mysql_query($query,$conex) or die (mysql_errno()." - ".mysql_error());
		$num = mysql_num_rows($err);
		if ($num > 0)
		{
			$row = mysql_fetch_array($err);
			$query = " SELECT cfgnit, cfgnom, cfgtre, cfgtel, cfgdir, cfgfran, cfgfian, cfgffan, cfgran, cfgfcar, cfgfrac, cfgfiac, cfgffac, cfgrac, cfgpin, cfgmai, cfgdom, Cfgret ";
			$query .= "   FROM ".$wbasedato."_000049  where Cfgcco = '".$wcco."'";
			$err1 = mysql_query($query,$conex) or die (mysql_errno()." - ".mysql_error());
			$row1 = mysql_fetch_array($err1);
			$wnit_pos  =$row1[0];
			$wnomemppos=$row1[1];
			$wtipregiva=$row1[2];
			$wtel_pos  =$row1[3];
			$wdir_pos  =$row1[4];
			if ($row1[9] > $row[0])
			{
			    $wnrores=$row1[8];
			    $wfecres=$row1[5];
			    $wfacini=$row1[6];
			    $wfacfin=$row1[7];
			}
			 else
			{
			  $wnrores=$row1[13];
			  $wfecres=$row1[10];
			  $wfacini=$row1[11]; 
			  $wfacfin=$row1[12];
			}
			$wpagintern=$row1[14];
			$wemail_pos=$row1[15];
			$wteldompos=$row1[16];
			$wretenedores=$row1[17];
			echo "<tr><td colspan=5>&nbsp</td></tr>"; 
			echo "<tr><td align=center colspan=5><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png'><font size=2> Ver. 03/01/2006</font></td></tr>";
			echo "<tr><td colspan=5 align=center><font size=6>".$wnomemppos."</font></td></tr>";
			echo "<tr><td colspan=5 align=center><font size=5>Nit. : ".$wnit_pos."</font></td></tr>";
			echo "<tr><td colspan=5 align=center><font size=5>Tel.: ".$wtel_pos." Dir.: ".$wdir_pos."</font></td></tr>";
			echo "<tr><td colspan=5 align=center><font size=5>".$wtipregiva."</font></td></tr>";
			if( $wretenedores != "" ) echo "<tr><td colspan=5 align=center><font size=5>".$wretenedores."</font></td></tr>";
			echo "<tr><td colspan=3 align=left><font size=5>Hora: ".$row[1]."</font></td>";
			echo "<td colspan=2 align=right><font size=5>Fecha: ".$row[0]."</font></td></tr>";
			echo "<tr><td colspan=5><font size=5>Factura Cambiaria de Compra-Venta Nro: ".$wnrofac."</font></td></tr>";   
		    echo "<tr><td colspan=5><font size=5>Responsable: ".$row[2]."</font></td></tr>";
			echo "<tr><td colspan=5><font size=5>Cédula/Nit: ".$row[3]."</font></td></tr>";
			echo "<tr><td colspan=5>&nbsp</td></tr>";
			echo "<th align=left bgcolor=DDDDDD colspan=4><font size=5>Descripción</font></th>";
			echo "<th align=RIGHT bgcolor=DDDDDD><font size=5>Valor</font></th>";
			$valven=$row[4] - $row[5];
			$totpag=$row[4] - $row[7];
	        echo "<tr><td align=left colspan=4><font size=5> + VALOR DE LAS VENTAS</font></td><td align=right><font size=5>".number_format($valven,0,'.',',')."</font></td></tr>";
	        echo "<tr><td align=left colspan=4><font size=5>  - VALOR X CUOTAS MODERADORAS</font></td><td align=right><font size=5>".number_format($row[7],0,'.',',')."</font></td></tr>";
	        echo "<tr><td align=left colspan=4><font size=5> + VALOR IVA</font></td><td align=right><font size=5>".number_format($row[5],0,'.',',')."</font></td></tr>";
	        echo "<tr><td align=left bgcolor=DDDDDD colspan=4><font size=5><b>TOTAL A PAGAR</b></font></td><td align=right bgcolor=DDDDDD><font size=5><b>".number_format($totpag,0,'.',',')."</b></font></td></tr>";
			echo "<tr><td colspan=5 align=center>&nbsp</td></tr>";
			echo "<tr><td align=left colspan=4><font size=5>Numero de Pacientes Atendidos</font></td><td align=right><font size=5>".number_format($numfac,0,'.',',')."</font></td></tr>";
			echo "<tr><td colspan=5 align=center>&nbsp</td></tr>";
			echo "<tr><td colspan=5 align=center>&nbsp</td></tr>";
			echo "<tr><td colspan=5 align=center>&nbsp</td></tr>";
			echo "<tr><td colspan=4 align=left>___________________</td><td align=right>___________________</td></tr>";
			echo "<tr><td colspan=4 align=left>   ELABORADO POR</td><td align=right>RECIBIDO POR  </td></tr>";
			echo "<tr><td colspan=5 align=center>&nbsp</td></tr>";
			echo "<tr><td colspan=5 align=center><font size=5>Resolución Nro: ".$wnrores." del ".$wfecres.", </font></td></tr>";    
			echo "<tr><td colspan=5 align=center><font size=5>factura ".$wfacini." a la factura ".$wfacfin.".</font></td></tr>";    
			echo "<tr><td colspan=5 align=center><font size=5>Esta factura cambiaria de compraventa se asimila en </font></td></tr>";    
			echo "<tr><td colspan=5 align=center><font size=5>todos sus efectos a una letra de cambio, Art. 621 y </font></td></tr>";    
			echo "<tr><td colspan=5 align=center><font size=5>SS, 671 y SS 772, 773, 770 y SS del código de comercio.</font></td></tr>";    
			echo "<tr><td colspan=5 align=center><font size=5>Factura impresa por computador cumpliendo con los</font></td></tr>";    
			echo "<tr><td colspan=5 align=center><font size=5>requisitos del Art. 617 del E.T.</font></td></tr>";    
			echo "<tr><td colspan=5>&nbsp</td></tr>";
			echo "<tr><td colspan=5>&nbsp</td></tr>";
			echo "<tr>";
			if ($wpagintern != "" and $wpagintern != "NO APLICA")    //Pagina de Internet
				echo "<tr><td colspan=5 align=center><font size=5><b>Visitenos en: ".$wpagintern."</b></font></td></tr>";
			if ($wemail_pos != "" and $wemail_pos != "NO APLICA")    //Email
				echo "<tr><td colspan=5 align=center><font size=5><b>Escribanos a: ".$wemail_pos."</b></font></td></tr>";
			if ($wteldompos != "" and $wteldompos != "NO APLICA")    //Telefono domicilio
				echo "<tr><td colspan=5 align=center><font size=5><b>Para sus domicilios comuniquese al ".$wteldompos."</b></font></td></tr>";
			echo "<tr>";
		}
		else
		{
			echo "<br><br><center><table border=0 aling=center>";
			echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table>";
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR NUMERO DE FACTURA NO EXISTE</MARQUEE></FONT>";
			echo "<input type='submit' value='Continuar'></center>";
			echo "<br><br>";
		}
		echo "</table>";  
     }
     else
	{
		echo "<br><br><center><table border=0 aling=center>";
		echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table>";
		echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ERROR NO ESCOGIO NUMERO DE FACTURA</MARQUEE></FONT>";
		echo "<input type='submit' value='Continuar'></center>";
		echo "<br><br>";
	}
?>
