
<html>
<head>
<title>Antecedentes</title>

<script type="text/javascript">
   function enter(variable)
   {
   	window.open("odontograma.php?paciente="+variable);
   }
    </script>

</head>
<body bgcolor="#FFFFFF" text="#000066">
<font face='arial'>
<?php
include_once("conex.php");
/*
 * Created on 23/02/2007
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
 /********************************************************
*     APLICACION PARA INGRESAR LOS ANTECEDENTES  		*
*														*
*********************************************************/

//==================================================================================================================================
//PROGRAMA						:APLICACION PARA INGRESAR LOS ANTECEDENTES 
//AUTOR							:Juan David Londoño
//FECHA CREACION				:FEBRERO DE 2007 2006
//FECHA ULTIMA ACTUALIZACION 	:
//DESCRIPCION					:Aplicacion mediante la cual se van a ingresar los antecedentes personales en la unidad odontologica
//								 
//==================================================================================================================================
$wactualiz="Ver. 2007-02-23";

session_start();
if(!isset($_SESSION['user']))
echo "error";
else
{
	$empresa='soe';
	
	

	


	echo "<form name=antecedentes1 action='' method=post>";

	if (!isset($pac))
	{
		echo "<center><table border=0 width=400>";
		echo "<tr><td colspan=3 align=center><img SRC='\MATRIX\images\medical\SOE\SOE1.JPG' width='242' height='133'></td>";
		echo "</select></tr><tr><td bgcolor=#99CCFF colspan=1 align=center><font color=#000066 font face='tahoma'><b>PACIENTE: </b></font></td>";

		/* Si el paciente no esta set construir el drop down */
		//if(isset($medico) and isset($pac1)) V1.03
		if(isset($pac1))
		{
			/* Si el medico  ya esta set traer los pacientes a los cuales les ha hecho seguimiento*/

			$query="select DISTINCT Pachis, Pacdoc, Pacno1, Pacno2, Pacap1, Pacap2  " 
				    ."from ".$empresa."_000100 "
			      ." where Pachis like '%".$pac1."%'" 
			          ."or Pacdoc  like '%".$pac1."%' " 
			          ."or Pacno1 like '%".$pac1."%'"
					 ." or Pacno2 like '%".$pac1."%' " 
					  ."or Pacap1 like '%".$pac1."%' " 
					  ."or  Pacap2  like '%".$pac1."%' " 
				   ."order by Pacdoc";
			echo "</td><td bgcolor=#99CCFF colspan=2><select name='pac'>";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{
				for ($j=0;$j<$num;$j++)
				{
					$row = mysql_fetch_array($err);
					if($row[0]==$pac)
					echo "<option selected>".$row[0]."</option>";
					else
					echo "<option>".$row[0]."-".$row[1]."-".$row[2]."-".$row[3]."-".$row[4]."</option>";
				}
			}	// fin $num>0
			echo "</select></td></tr>";
			echo "</td></tr><input type='hidden' name='pac1' value='".$pac1."'>";
		}	//fin isset medico
		else
		{
			echo "</td><td bgcolor=#99CCFF colspan=2><input type='text' name='pac1'>";
			echo "</td></tr>";
		}

		echo"<tr><td align=center bgcolor=#99CCFF colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";

		//echo"<tr><td align=center colspan=3 ><A HREF ='/matrix/det_registro.php?id=0&amp;pos1=soe1&amp;pos2=0&amp;pos3=0&amp;pos4=000002&amp;pos5=0&amp;pos6=".substr($user,2)."&amp;tipo=P&amp;Valor=&amp;Form=soe1_000002&amp;call=2&amp;change=&amp;key=".$key."&amp;Pagina='>Ingresar nuevo paciente</a></td></tr>";
		
	}
	// APATIR DE ACA COMIEMZA LA IMPRESION DE LOS ANTECEDENTES
	else 
	{
			$dat=explode("-",$pac);
			$hist=$pac;
			$histo=$dat[0];
			
			echo "</td></tr><input type='hidden' name='pac' value='".$pac."'>";
			
			/*$empresa="soe";
			$hist='5414-70552255-PEDRO GUILLERMO ORTIZ TAMAYO';
			$histo=5414;*/
			
			echo "<center><table border=1 WIDTH=950>";
			
			$wtabla="soe_000128";
			
			$wtab=explode("_",$wtabla);
			
			$query=" SELECT *
					  FROM ".$wtabla."
					 WHERE Hiscli='".$histo."'";
		   $err = mysql_query($query,$conex);
		   $numhis = mysql_num_rows($err);
		 	
		 	$fecha=date("Y-m-d");
	        $hora=date("H:i:s");
		 	
		 	if ($numhis==0)
		 	{
		 		$query="INSERT INTO ".$wtabla." (Medico, Fecha_data, Hora_data, Hiscli, Seguridad)" .
		 				" VALUES ('".$wtab[0]."', '".$fecha."', '".$hora."', '".$histo."', 'C-$wtab[0]')";
		 		$erri = mysql_query($query,$conex);		 
		 	}
			
			$query =  " SELECT max(campos)
			              FROM det_formulario, root_000052
			             WHERE det_formulario.Medico   = '".$wtab[0]."'
			               AND det_formulario.codigo   ='".$wtab[1]."'
			               AND det_formulario.posicion > 2	 
			               AND det_formulario.medico   = mid(tabla,1,instr(tabla,'_')-1)
			               AND det_formulario.codigo 	= mid(tabla,instr(tabla,'_')+1,length(tabla))
			               AND det_formulario.descripcion = camnom
			             Order By campos";
			$err = mysql_query($query,$conex);
			$row = mysql_fetch_array($err);
			$wmaxcam=$row[0];
			
			//echo mysql_errno() ."=". mysql_error();
			$query =  " SELECT descripcion, tipo, comentarios, campos
			              FROM det_formulario, root_000052  
			             WHERE det_formulario.Medico   = '".$wtab[0]."'
			               AND det_formulario.codigo   ='".$wtab[1]."'
			               AND det_formulario.posicion > 2	 
			               AND det_formulario.medico   = mid(tabla,1,instr(tabla,'_')-1)
			               AND det_formulario.codigo 	= mid(tabla,instr(tabla,'_')+1,length(tabla))
			               AND det_formulario.descripcion = camnom
			             Order By campos";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			echo $query;
			//echo mysql_errno() ."=". mysql_error();
			echo "<center><table border=1 >";
			echo "<tr><td align=center rowspan=2 colspan=2><img src='/MATRIX/images/medical/SOE/SOE1.JPG' width='200' height='100'></td></tr>";
			echo "<tr><td align=center colspan=1 bgcolor=#006699><font size=4 text color=#FFFFFF><b>ANTECEDENTE MEDICOS</b></font><br><font size=3 text color=#FFFFFF><b>".$wactualiz."</b></font></td></tr>";
			echo "<tr><td align=center colspan=3 bgcolor=#006699><font size=4 text color=#FFFFFF>".$hist."</font></font></td></tr>";
			echo "</table>";
			echo "<br>";
			if ($num > 0)
			   {
			echo "<input type='hidden' name='inicio' value='1'>";   	
			   
			   	for ($h=1;$h<=$num;$h++) // toda la informacion la traigo en este arreglo
			   	  {
			   	   $row = mysql_fetch_array($err);	
			   	  
			   	   $arr[$h]['desc']=$row[0];
			   	   $arr[$h]['tipo']=$row[1];
			   	   $arr[$h]['come']=$row[2];
			   	   $arr[$h]['camp']=$row[3];
			   	  }
			  
			    echo "<center><table  bgcolor=#BDE9F8  border=0 >";
				$i=1;
				$h=1;  //es el sunindice del arreglo, hace las veces de resultado del query, esto me facilita adelantarme o devolverme
				$wr=2;
				while ($i<=$wmaxcam) // $i tiene que se menor que el maximo numero que exista en la tabla de root
				   {
				 	$wcol=4; // este es el colspan
				 	$j=1;
				 	$wcolor="#BDE9F8";
					echo "<tr>";
					if ($wr==2 and $i !=1) //este es if que me marca la linea cada dos <tr>
						{
						 echo "<td colspan=".$wcol."><hr></td></tr>";
						 $wr=1; // se pone en uno para que me coloque la raya cada 2
						 }else
						 	$wr=$wr+1;
					while(($j<=$wcol) and ($i<=$wmaxcam) )
				 	  {
				 	   if ($arr[$h]['tipo']==13 or $arr[$h]['tipo']==4)  // 13: titulo u observacion
				 	      {
				 	      $wr=1; // se pone en uno para que me coloque la raya cada 2
				 	       echo "</tr>";
				 	       if ($arr[$h]['tipo']==4) // 4: tipo texto
				 	       {
				 	       // este es el query pa traer los datos de las tabla	
				 	       $query=" SELECT ".$arr[$h]['desc']."  
									  FROM ".$wtabla."
									 WHERE Hiscli='".$histo."'";
							$errd = mysql_query($query,$conex);
							$numx = mysql_num_rows($errd);
							/*if ($numhis==0)// para que lo pinte cuendo lo va a insertar
								$numx=1;*/
							for ($x=1;$x<=$numx;$x++) //la informacion de los datos del arreglo arreglo
				   	  				{
				   	  					$rowd = mysql_fetch_array($errd);
						 	          	$arr[$x][$h]=$rowd[0];
						 	          	if (isset ($arrn[$x][$h]) and $arr[$x][$h] != $arrn[$x][$h])// para cuando se actualiza el valor lo tome
						 	          		$wval=$arrn[$x][$h];
						 	          		else
						 	          			$wval=$arr[$x][$h];
				   	  					echo "<tr bgcolor=".$wcolor." ><td colspan=".$wcol." align=center><font size=2><b>".$arr[$h]['come']."<TEXTAREA Name='arrn[".$x."][".$h."]' rows='2' cols='100'>".$wval."</TEXTAREA></td></tr>";
				   	  				}
					 	    }
					 	       else
						 	       echo "<tr bgcolor=#D7D7D7 ><td colspan=".$wcol."><font size=3><b>".$arr[$h]['come']."</td></tr>";
					 	       echo "<tr>";
					 	       $j=0;
					 	       $h++;
				 	      } 
				 	     else
				 	       {
				 	       if ($i == $arr[$h]['camp'])
				 	          {
				 	           if ((isset($arr[$h+1]['tipo']) and ($arr[$h+1]['tipo']==13 or $arr[$h+1]['tipo']==4)) or $arr[$h]['camp']==$wmaxcam)
				 	              {
					 	           $wcolspan=$wcol-$j+1; // esta es la variable del colspan para ponerla antes del titulo
					 	          } 
					 	         else
					 	            $wcolspan=1; 
					 	          	 
					 	       if ($arr[$h]['tipo'] == 0 )  // 0: caracter
					 	          {
					 	          	// este es el query pa traer los datos de las tabla
					 	          	$query=" SELECT ".$arr[$h]['desc']."
											  FROM ".$wtabla."
											 WHERE Hiscli='".$histo."'";
									$errd = mysql_query($query,$conex);
									$numx = mysql_num_rows($errd);
									/*if ($numhis==0)// para que lo pinte cuendo lo va a insertar
										$numx=1;*/
									for ($x=1;$x<=$numx;$x++) //la informacion de los datos del arreglo arreglo
				   	  				{
						 	          	$rowd = mysql_fetch_array($errd);
						 	          	$arr[$x][$h]=$rowd[0];
						 	          	if (isset ($arrn[$x][$h]) and $arr[$x][$h] != $arrn[$x][$h])// para cuando se actualiza el valor lo tome
						 	          		$wval=$arrn[$x][$h];
						 	          		else
						 	          			$wval=$arr[$x][$h];
						 	          	//echo $arr[$x][$h]."-".$arrn[$x][$h];
						 	          	echo "<td bgcolor=".$wcolor." colspan=".$wcolspan."><font size=2>".$arr[$h]['desc'].": <INPUT TYPE='text' NAME='arrn[".$x."][".$h."]' VALUE='".$wval."' SIZE=10></td>";
				   	  				}
				   	  			 }					 	        
					 	        else // 10: check box
					 	        {
					 	        // este es el query pa traer los datos de las tabla	
					 	        $query=" SELECT ".$arr[$h]['desc']."
											  FROM ".$wtabla."
											 WHERE Hiscli='".$histo."'";
								$errd = mysql_query($query,$conex);
								$numx = mysql_num_rows($errd);
								/*if ($numhis==0)// para que lo pinte cuendo lo va a insertar
								$numx=1;*/
								for ($x=1;$x<=$numx;$x++) //la informacion de los datos del arreglo arreglo
				   	  				{
				   	  				$rowd = mysql_fetch_array($errd);
				   	  				$arr[$x][$h]=$rowd[0];
				   	  				//echo $arr[$x][$h]."-".$arrn[$x][$h];
						 	         if (($arr[$x][$h]=='on' and !isset ($inicio)) or (isset ($arrn[$x][$h]) and $arrn[$x][$h]=='on'))// para cuando se actualiza el valor lo tome
						 	          		$chk='checked';
						 	          		else if (!isset ($arrn[$x][$h]))					 	          		
						 	          			$chk='';
						 	          	echo "<td bgcolor=".$wcolor." colspan=".$wcolspan."><font size=2>".$arr[$h]['desc'].": <input type='checkbox' name='arrn[".$x."][".$h."]' $chk></td>";
					   	  			}
					 	        }					 	   
						 	   $h++;
						 	  }     
					 	     else 
					 	        echo "<td bgcolor=".$wcolor.">&nbsp</td>";
					 	       }
				 	   $j=$j+1;
				 	   $i=$i+1;
				 	  }
				   echo "</tr>";
				   
				  }
				echo "<tr><td colspan=".$wcol."><hr></td></tr>";  
				echo "<tr><td align=center bgcolor=#cccccc colspan=".$wcol.">DATOS COMPLETOS: <input type='checkbox' name='wactu'></td></tr>";
				echo "<tr><td align=center bgcolor=#cccccc colspan=".$wcol."><input type='submit' value='OK'></td></tr>";
				echo "</table>";
			   }	
			echo "<br>";
	/////////////////////////////////////desde este punto se hace el insert o el update
		if (isset ($inicio) and isset($wactu))
		{
			if ($numhis!=0)
			   		{
			   			// update para cuendo ya tiene antecedentes
			   		for ($h=1;$h<=$num;$h++) // toda la informacion la traigo en este arreglo
					   	  {
					   	   for ($x=1;$x<=$numx;$x++) // toda la informacion la traigo en este arreglo
						   	  {
							   	   if (isset($arrn[$x][$h]))
							   	   {
							   	   $query=" UPDATE ".$wtabla."
											  SET ".$arr[$h]['desc']." = '".$arrn[$x][$h]."'
											 WHERE Hiscli='".$histo."'";
									$erru = mysql_query($query,$conex);
									echo "<input type='hidden' name='arr[".$x."][".$h."]' value='".$arrn[$x][$h]."'>";
									 
								 	}else
									   	  {
									   	  	$query=" UPDATE ".$wtabla."
														  SET ".$arr[$h]['desc']." = 'off'
														 WHERE Hiscli='".$histo."'";
									   		$erru = mysql_query($query,$conex);
						   	  			  }
							 }
					   	      	  
					   	  }
					   	  
			   		}
		   
			//$inicio=0;
		}
		//$cedula="70552255";
		$cedula=$dat[1];
		$variable='enter("'.$cedula.'")';
		echo "<hr><a onclick='".$variable."'><img src='/matrix/images/medical/soe/risa.gif'></a><br><b>ODONTOGRAMA</b></hr>";
		
			
			/*
			echo "<center><table border=1 >"; 
			echo "<tr bgcolor=#D7D7D7 ><td colspan=4><font size=3><b>ANTECEDENTES MEDICOS PERSONALES: Señale si presenta o ha presentado alguno</td></tr>";
			echo "<tr bgcolor=#BDE9F8><td><font size=2>1. Tratamiento medico: <input type='checkbox' name='chk11' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='tam' SIZE=10></td>";
			echo "<td><font size=2>2. Hospitalizado: <input type='checkbox' name='chk12' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='hos' SIZE=10></td>";
			echo "<td><font size=2>3. Cirugia: <input type='checkbox' name='chk13' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='cir' SIZE=10></td>";
			echo "<td><font size=2>4. Fuma: <input type='checkbox' name='chk14' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='fum' SIZE=10></td></tr>";
			echo "<tr bgcolor=#BDE9F8><td><font size=2>5. Reacciones Alergicas: <input type='checkbox' name='chk15' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='rea' SIZE=10></td>";
			echo "<td><font size=2>6. Fiebre reumatica: <input type='checkbox' name='chk16' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='fir' SIZE=10></td>";
			echo "<td><font size=2>7. Hipertension : <input type='checkbox' name='chk17' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='hpe' SIZE=10></td>";
			echo "<td><font size=2>8. Hipotension: <input type='checkbox' name='chk18' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='hpo' SIZE=10></td></tr>";
			echo "<tr bgcolor=#BDE9F8><td><font size=2>9. Enfermedades Cardiovasculares: <input type='checkbox' name='chk19' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='eca' SIZE=10></td>";
			echo "<td><font size=2>10. Enfermedades Respiratorias: <input type='checkbox' name='chk110' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='ere' SIZE=10></td>";
			echo "<td><font size=2>11. Trastornos Hemorragicos: <input type='checkbox' name='chk111' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='the' SIZE=10></td>";
			echo "<td><font size=2>12. Anemia: <input type='checkbox' name='chk112' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='ane' SIZE=10></td></tr>";
			echo "<tr bgcolor=#BDE9F8><td><font size=2>13. Convulsiones o Epilepsia: <input type='checkbox' name='chk113' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='epi' SIZE=10></td>";
			echo "<td><font size=2>14. Enfermedades de la Tiroides: <input type='checkbox' name='chk114' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='tir' SIZE=10></td>";
			echo "<td><font size=2>15. Enfermedades Renales: <input type='checkbox' name='chk115' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='ren' SIZE=10></td>";
			echo "<td><font size=2>16. Enfermedades Hepaticas: <input type='checkbox' name='chk116' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='hep' SIZE=10></td></tr>";
			echo "<tr bgcolor=#BDE9F8><td><font size=2>17. Enfermedades Infectocontagiosas: <input type='checkbox' name='chk117' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='inf' SIZE=10></td>";
			echo "<td><font size=2>18. Trastornos Inmunologicos: <input type='checkbox' name='chk118' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='inm' SIZE=10></td>";
			echo "<td><font size=2>19. Gastritis: <input type='checkbox' name='chk119' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='gas' SIZE=10></td>";
			echo "<td><font size=2>20. Diabetes: <input type='checkbox' name='chk120' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='dia' SIZE=10></td></tr>";
			echo "<tr bgcolor=#BDE9F8><td><font size=2>21. Tumores: <input type='checkbox' name='chk121' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='tum' SIZE=10></td>";
			echo "<td><font size=2>22. Artritis: <input type='checkbox' name='chk122' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='art' SIZE=10></td>";
			echo "<td><font size=2>23. Esta Tomando Medicamentos: <input type='checkbox' name='chk123' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='med' SIZE=10></td>";
			echo "<td><font size=2>24. Esta en Embarazo: <input type='checkbox' name='chk124' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='emb' SIZE=10></td></tr>";
			echo "<tr bgcolor=#BDE9F8><td colspan=4><font size=2>25. Otras Enfermedades: <input type='checkbox' name='chk125' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='oen' SIZE=10></td></tr>";
			
			echo "<tr bgcolor=#D7D7D7><td colspan=4><font size=3><b>ANTECEDENTES MEDICOS FAMILIARES: Señale si presenta o ha presentado alguno</td></tr>";
			echo "<tr bgcolor=#BDE9F8 ><td align=center colspan=4><font size=2><TEXTAREA Name='comments' rows='2' cols='100'></TEXTAREA></td><tr>";
			
			echo "<tr bgcolor=#D7D7D7><td colspan=4><font size=3><b>ANTECEDENTES ODONTOLOGICOS: Señale si presenta o ha presentado alguno</td></tr>";
			echo "<tr bgcolor=#BDE9F8><td><font size=2>1. Operatorio: <input type='checkbox' name='chk21' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='tam' SIZE=10></td>";
			echo "<td><font size=2>2. Endodoncia: <input type='checkbox' name='chk22' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='hos' SIZE=10></td>";
			echo "<td><font size=2>3. Periodoncia: <input type='checkbox' name='chk23' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='tam' SIZE=10></td>";
			echo "<td><font size=2>4. Cirugia odontonlogica: <input type='checkbox' name='chk24' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='hos' SIZE=10></td></tr>";
			echo "<tr bgcolor=#BDE9F8><td><font size=2>5. Protesis: <input type='checkbox' name='chk25' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='tam' SIZE=10></td>";
			echo "<td><font size=2>6. Ortodoncia: <input type='checkbox' name='chk26' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='hos' SIZE=10></td>";
			echo "<td><font size=2>7. Urgencias Odontologicas: <input type='checkbox' name='chk27' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='tam' SIZE=10></td>";
			echo "<td><font size=2>8. Anestesia Local: <input type='checkbox' name='chk28' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='hos' SIZE=10></td></tr>";
			echo "<tr bgcolor=#BDE9F8><td><font size=2>9. Blanqueamiento: <input type='checkbox' name='chk29' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='tam' SIZE=10></td>";
			echo "<td colspan=3><font size=2>10. Otros: <input type='checkbox' name='chk210' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='hos' SIZE=10></td></tr>";
			
			echo "<tr bgcolor=#D7D7D7><td colspan=4><font size=3><b>EXAMEN ESTOMATOLOGICO: Señale si son anormales</td></tr>";
			echo "<tr bgcolor=#BDE9F8><td><font size=2>1. Labios: <input type='checkbox' name='chk31' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='tam' SIZE=10></td>";
			echo "<td><font size=2>2. Carrillos: <input type='checkbox' name='chk32' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='hos' SIZE=10></td>";
			echo "<td><font size=2>3. Frenillos: <input type='checkbox' name='chk33' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='tam' SIZE=10></td>";
			echo "<td><font size=2>4. Paladar Blando: <input type='checkbox' name='chk34' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='hos' SIZE=10></td></tr>";
			echo "<tr bgcolor=#BDE9F8><td><font size=2>5. Paladar Duro: <input type='checkbox' name='chk35' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='tam' SIZE=10></td>";
			echo "<td><font size=2>6. Senos Paranasales: <input type='checkbox' name='chk36' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='hos' SIZE=10></td>";
			echo "<td><font size=2>7. Orofaringe: <input type='checkbox' name='chk37' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='tam' SIZE=10></td>";
			echo "<td><font size=2>8. Piso Boca: <input type='checkbox' name='chk38' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='hos' SIZE=10></td></tr>";
			echo "<tr bgcolor=#BDE9F8><td><font size=2>9. Glandulas Salivales Cardiovasculares: <input type='checkbox' name='chk39' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='tam' SIZE=10></td>";
			echo "<td><font size=2>10. Lengua: <input type='checkbox' name='chk310' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='hos' SIZE=10></td>";
			echo "<td colspan=2><font size=2>11. Atm: <input type='checkbox' name='chk311' onclick='enter()'><br>Observacion: <INPUT TYPE='text' NAME='tam' SIZE=10></td></tr>";
			
			echo "</table>";*/
	}	
}
?>
</body>
</html>
  