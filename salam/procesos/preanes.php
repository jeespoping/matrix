<html>
<head>
  <title>PREANESTESIA</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
	/*****************************************************
	 *          REPORTE DE IMPRESION DE PREANESTESIA     *
	 *					CONEX, FREE => OK				 *
	 *****************************************************/
		session_start();
		if(!isset($_SESSION['user']))
			echo "<br>error";
		else
		{
			   $hora_inicio=$hi;
				$key = 	substr($user,2,strlen($user));
				

				
  
				$query = "select * from salam_000005  where  Paciente like '".$cedula."%' AND Dia_cirugia='".$fechacx."' AND Hora_cirugia='".$hora_inicio."' " ;
				$err= mysql_query($query); 
				$num = mysql_num_rows ($err);
				if	 ($num>0)
				{
					$row = mysql_fetch_row($err);
					
					FOR ($i=23;$i<=25;$i++)
					{
						if(strcmp($row[$i],'on')==0)
						$row[$i]="checked";
						else if(strcmp($row[$i],'off')==0)
						$row[$i]=" ";
					}
					
					FOR ($i=35;$i<=40;$i++)
					{
						if(strcmp($row[$i],'on')==0)
						$row[$i]="checked";
						else if(strcmp($row[$i],'off')==0)
						$row[$i]=" ";
					}
					
					FOR ($i=48;$i<=74;$i++)
					{
						if(strcmp($row[$i],'on')==0)
						$row[$i]="checked";
						else if(strcmp($row[$i],'off')==0)
						$row[$i]=" ";
					}
					$row[3]=substr($row[3],3);
					$row[8]=substr($row[8],3);
					$row[9]=strtolower(substr($row[9],3));
					$row[14]=substr($row[14],4);
					$row[22]=substr($row[22],3);
					$row[32]=strtolower(substr($row[32],4));
					$row[39]=strtolower(substr($row[39],4));
					$row[84]=strtolower(substr($row[84],3));
					$row[89]=strtolower(substr($row[89],3));
					$row[94]=strtolower(substr($row[94],3));
					$row[99]=strtolower(substr($row[99],3));
					$row[104]=strtolower(substr($row[104],3));
					$row[109]=strtolower(substr($row[109],3));
					$row[114]=strtolower(substr($row[114],3));
					$ini1=strpos($row[7],":");
					$ini2=strpos($row[7],":",$ini1+2);
					$ini3=strpos($row[7],":",$ini2+1);
					$ini4=strpos($row[7],":",$ini3+1);
					$ini5=strpos($row[7],":",$ini4+1);
					$dx=substr($row[7],$ini1+1,$ini2-$ini1-8);
					$cx=substr($row[7],$ini2+1,$ini3-$ini2-9);
					$cj=substr($row[7],$ini3+1,$ini4-$ini3-9);
					$ent=substr($row[7],$ini4+5, strlen($row[7]));
					?>
					
					
					<table border=1 width="710">	&nbsp;
					<tr>	<td rowspan=3 align='center' colspan="0" width="184">
                  					<img SRC='\MATRIX\images\medical\salam\cirugia.jpg' size=90% width=90%></td>			
							<td align=center colspan="4" width="604"><font size=4 color="#000080" face="arial"><B>CLÍNICA LAS AMÉRICAS</b></font></td>
					</tr>
					<tr>	<td  align=center colspan="4" width="604"><font size=3 color="#000080" face="arial"><B>UNIDAD DE CIRUGÍA</b></font>
					</tr>
					<tr>	<td  align=center colspan="4" width="604"><font size=2 color="#000080" face="arial"><B>PREANESTESIA</b></font>
               				 </tr>		
               				 </table>
                             
                
                			<TABLE  border=1 width="710">
							<tr>		
                              <td align=left width="700" colspan="5" bgcolor="#cccccc" height="15"><font size="2" color="#000066" face="arial">
                						<b>DATOS GENERALES</b></font></td>
				        </tr>
				        <tr>	<td align=left colspan="1" width="270"><font size="2"  color="#000080" face='arial'>FECHA PRENES::<b> <?php echo $row[1];?>&nbsp;</b></font></td>		
				        		<td align=left colspan="2" width="269"><font size="2" color="#000080" face="arial">FECHA Cx:<b> <?php echo $row[5];?>&nbsp;</b></font></td>			
	               					<td align=left colspan="1" width="148"><font size="2"  color="#000080" face='arial'>HORA Cx:<b> <?php echo $row[6];?>&nbsp;</b></font></td>			
					</tr>
               				 <tr>	<td align=left width="270"><font size="2" color="#000080" face="arial">PACIENTE:<b> <?php echo $row[4];?>&nbsp;</font></b></td>			
							<td align=left width="100"><font size="2" color="#000080" face="arial">PESO:<b> <?php echo $row[12];?></b></font></td>
               				 		<td align=left width="163"><font size="2" color="#000080" face="arial">EDAD:<b><?php echo $row[10];?></b></font></td>			
							<td align=left width="148"><font size="2" color="#000080" face="arial">ESTATURA:<b> <?php echo $row[11];?></b></font></td>			
				       </tr>
					<tr>	<td align=left  colspan="3" width="545"><font size="2" color="#000080" face="arial">DIAGNOSTICO:<b> <?php echo $dx;?></b></font></td>
							<td align=left  colspan="1" width="148"><font size="2" color="#000080" face="arial">ESTADO_FÍSICO:<b><?php echo $row[8]; ?></b></font></td>
					</tr>
							<td align=left  colspan="1" width="270"><font size="2" color="#000080" face="arial">TIPO Cx:<b> <?php echo $row[9];?></b></font></td>
					  		<td align=left colspan="2" colspan="1" width="269" ><font size="2" color="#000080" face="arial">OPERACIÓN PROPUESTA:<b> <?php echo $cx; ?></b></font></td>
					  		<td align=left  colspan="1" width="148"><font size="2" color="#000080" face="arial">ENTIDAD:<b><?php echo $ent; ?></b></font></td>			

					</tr>
					<tr>	<td align=left colspan="1" width="270"><font size="2" color="#000080" face="arial">ANESTESIÓLOGO:<b> <?php echo $row[3];?></b></font></td>
							<td align=left colspan="3" width="423"><font size="2" color="#000080" face="arial">CIRUJANO:<b><?php echo $cj; ?></b></font></td>
					</tr>		
					</table>		

				       
				
				       
					<TABLE  border=1 width="710">
					<tr>	<td align=left width="551" colspan="5" bgcolor="#cccccc" height="15"><font size="2" color="#000066" face="arial">
                  					<b>LABORATORIO&nbsp;</font></b></td>
				        </tr>
                  			<tr>	<td align=left width="488"><font size="2" color="#000080" face="arial">GRUPO SANG.:<b> <?php echo $row[14];?></b></font></td>
							<td align=left width="455"><font size="2" color="#000080" face="arial">HEMOGLOBINA:<b> <?php echo $row[15];?></b></font></td>
							<td align=left width="455"><font size="2" color="#000080" face="arial">HEMATOCRITO:<b> <?php echo $row[16];?></b></font></td>
							<td align=left width="551"><font size="2" color="#000080" face="arial">T. PROTOTROMBINA:<b> <?php echo $row[17];?></b></font></td>
					</tr>
					<tr>	<td align=left width="488"><font size="2" color="#000080" face="arial">T. PROTROMBINA:<b> <?php echo $row[18];?></b></font></td>
							<td align=left width="455"><font size="2" color="#000080" face="arial">T. COAGULACIÓN:<b> <?php echo $row[19];?></b></font></td>
							<td align=left width="455"><font size="2" color="#000080" face="arial">CPK MB:<b> <?php echo $row[21];?></b></font></td>
						<td align=left width="551"><font size="2" color="#000080" face="arial">PLAQUETAS:<b> <?php echo $row[20];?></b></font></td>
	            			</tr>
					</table>
				
				
	            
					<TABLE  border=1 width="710" >
					<tr>	<td align=left width="551" colspan="6" bgcolor="#cccccc" height="15"><font size="2" color="#000066" face="arial">
		                  			<b>INFORMACIÓN ESPECIAL</b></font></td>
					</tr>
                			<tr>	<td align=left width="488"><font size="2" color="#000080" face="arial">NA:<b> <?php echo $row[26];?></b></font></td>
							<td align=left width="427"><font size="2" color="#000080" face="arial">K:<b> <?php echo $row[28];?></b></font></td>
							<td align=left width="556"><font size="2" color="#000080" face="arial">CL:<b> <?php echo $row[30];?></b></font></td>
							<td align=left width="478"><font size="2" color="#000080" face="arial">BUN.:<b> <?php echo $row[27];?></b></font></td>
							<td align=left width="486"><font size="2" color="#000080" face="arial">CREATININA:<b> <?php echo $row[29];?></b></font></td>
							<td align=left width="616"><font size="2" color="#000080" face="arial">GLIC.:<b> <?php echo $row[31];?></b></font></td>
					</tr>
					<tr>	<td align=left width="533" colspan="2"><font size="2" color="#000080" face="arial">
							DIENTES NATURALES&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</font><input type="checkbox"  name="C1" <?php echo $row[23];?> value="ON"></b></font></td>
							<td align=left width="410" colspan="2"><font size="2" color="#000080" face="arial">
                    					AYUNO MAYOR A 6 HORAS&nbsp; </font><input type="checkbox"  name="C1" <?php echo $row[24];?> value="ON"></b></font></td>
							<td align=left width="463" colspan="1"><font size="2" color="#000080" face="arial">
							PRÓTESIS&nbsp;&nbsp;&nbsp;&nbsp;</font><input type="checkbox"  name="C1" <?php echo $row[25];?> value="ON"></b></font></td>
							<td align=left width="543" colspan="1"><font size="2" color="#000080" face="arial">UROANÁLISIS:<b><?php echo $row[32];?></b></font></td>
					</tr>
					<tr>	<td align=left width="455" colspan="6"><font size="2" color="#000080" face="arial">MEDICAMENTO TOMADO ACTUALMENTE:<b> <?php echo $row[33];?></b></font></td>
                			</table>
                
                
                
                			<TABLE  border=1 width="710" height="167">
					<tr>	<td align=left width="551" colspan="5" bgcolor="#cccccc" height="15"><font size="2" color="#000066" face="arial">
							<b>SISTEMA RESPIRATORIO</b></font></td>
					</tr>
					<tr>	<td align=left width="488"colspan="2" height="21"><font size="2" color="#000080" face="arial">
							ASMA&nbsp;&nbsp;&nbsp;</font><input type="checkbox"  name="C1"  <?php echo $row[35];?> value="ON"></b></font></td>
							<td align=left width="455" colspan="2" height="21"><font size="2" color="#000080" face="arial">
							PULMONES LIMPIOS:</font><input type="checkbox"  name="C1" <?php echo $row[36];?> value="ON"></b></font></td>
							<td align=left width="528" height="21"><font size="2" color="#000080" face="arial">
							TOS</font><input type="checkbox"  name="C1" <?php echo $row[37];?> value="ON"></b></font></td>
					</tr>
					<tr>	<td align=left width="488" colspan="2" height="21"><font size="2" color="#000080" face="arial">
							TABAQUISMO</font><input type="checkbox"  name="C1" <?php echo $row[40];?> value="ON"></b></font></td>
							<td align=left width="455" COLSPAN="2" height="21"><font size="2" color="#000080" face="arial">
							SÍNDROME GRIPAL</font><input type="checkbox"  name="C1" <?php echo $row[38];?> value="ON"></b></font></td>
							<td align=left width="528" height="21"><font size="2" color="#000080" face="arial">
							RAYOS X:<b> <?php echo $row[39];?>&nbsp;</b></font></td>
					</tr>
					<tr>	<td align=left width="487" height="15"><font size="2" color="#000080" face="arial">PH:<b> <?php echo $row[41];?></b></font></td>
							<td align=left width="456" height="15"><font size="2" color="#000080" face="arial">PO2:<b> <?php echo $row[42];?></b></font></td>
							<td align=left width="528" height="15"><font size="2" color="#000080" face="arial">PCO2:<b> <?php echo $row[43];?></b></font></td>
							<td align=left width="478" height="15"><font size="2" color="#000080" face="arial">BICARBONATO:<b> <?php echo $row[44];?></b></font></td>
							<td align=left width="551" height="15"><font size="2" color="#000080" face="arial">% SATURACIÓN:<b><?php echo $row[45];?></b></font></td>
					</tr>
					<tr>	<td align=left width="487" colspan="5" bgcolor="#99CCFF" height="65"><font size="2" color="#000080" face="arial">
                  					<span style="background-color: #99CCFF">OBSERVACIONES</span><b><span style="background-color: #99CCFF"> 
                  					: </span><textarea ALIGN="CENTER"  ROWS="2" name="S1" cols="84" style="font-family: 12pt; color:#000080;  font-weight: bold"><?php echo $row[46];?></textarea></td>
                			</tr>
                  			</table>
                  
					<TABLE  border=1 width="710" >
					<tr>	<td align=left width="551" colspan="5" bgcolor="#cccccc" height="15"><font size="2" color="#000066" face="arial">
							<b>SISTEMA CIRCULATORIO</b></font></td>
					</tr>
					<tr>	<td align=left width="1128" height="21"><font size="2" color="#000080" face="arial">
                    					RUIDOS CARDIACOS RÍTMICOS Y SIN SOPLOS&nbsp;</font><input type="checkbox" <?php echo $row[48];?> name="C1" value="ON"></td>
							<td align=left width="180" height="21"><font size="2" color="#000080" face="arial">
                    					HIPERTENSIÓN:</font><input type="checkbox" <?php echo $row[49];?> name="C1" value="ON"></td>
							<td align=left width="481" height="21"><font size="2" color="#000080" face="arial">
							PA:<b><?php echo $row[51];?></b></font></td>
							<td align=left width="100" height="21"><font size="2" color="#000080" face="arial">
							PULSO:<b><?php echo $row[52];?></b></font></td>
					</tr>
					<tr>	<td align=left width="618"colspan="4" height="21"><font size="2" color="#000080" face="arial">ECG:<b><?php echo $row[50];?></b></font>
					</tr>
					<tr>       <td align=left width="436" colspan="4" bgcolor="#99CCFF" height="65" colspan="4"><font size="2" color="#000080" face="arial">
                  					<span style="background-color: #99CCFF">OBSERVACIONES</span></font><span style="background-color: #99CCFF"> : </span>
                  					<textarea  ALIGN="CENTER" ROWS="2" name="S1" cols="84" style="font-family: 12pt; color:#000080;  font-weight: bold"><?php echo $row[53];?></textarea></td>
                			</tr>
                			</table>
               
					<TABLE  border=1 width="710" >
					<tr>	<td align=left width="551" colspan="5" bgcolor="#cccccc" height="15"><font size="2" color="#000066" face="arial">
							<b>OTROS SISTEMAS</b></font></td>
					</tr>
					<tr>	<td align=left width="1128" height="21"><font size="2" color="#000080" face="arial">
							EPILEPSIA&nbsp; </font><input type="checkbox" <?php echo $row[55];?> name="C1" value="ON"></td>
							<td align=left width="180" height="21"><font size="2" color="#000080" face="arial">
                    					PARÁLISIS</font><input type="checkbox" <?php echo $row[56];?> name="C1" value="ON"></td>
							<td align=left width="481" height="21"><font size="2" color="#000080" face="arial">
                    					CONVULSIÓNES</font><input type="checkbox"  <?php echo $row[57];?>  name="C1" value="ON"></td>
                    			</tr>		
					<tr>	<td align=left width="1128" height="21" COLSPAN="3"><font size="2" color="#000080" face="arial">
							ENFERMEDADE HEPATICA:
							<input type="checkbox"<?php echo $row[58];?>  name="C1" value="ON">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
							CUAL:<b> <?php echo $row[59];?></b></FONT></td>
					</tr>
					<tr>	<td align=left width="1128" height="21" COLSPAN="3"><font size="2" color="#000080" face="arial">
                  					ENFERMEDAD RENAL:
							<input type="checkbox" <?php echo $row[60];?> name="C1" value="ON">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
					 		CUAL:<b> <?php echo $row[61];?></b></font></td>
					 </tr>
					<tr>	<td align=left width="1128" height="21" COLSPAN="3"><font size="2" color="#000080" face="arial">
                  					ENFERMEDAD TIROIDES:
							<input type="checkbox" <?php echo $row[62];?> name="C1" value="ON">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
							CUAL:<b> <?php echo $row[63];?></b></font></td>
					</tr>
					<tr>       <td align=left width="436" colspan="4" bgcolor="#99CCFF" height="65" colspan="4"><font size="2" color="#000080" face="arial">
                  					<span style="background-color: #99CCFF">OBSERVACIONES</span></font><span style="background-color: #99CCFF"> 
                  					: </span><textarea ALIGN="CENTER" ROWS="2" name="S1" cols="84" style="font-family: 12pt; color:#000080;  font-weight: bold"><?php echo $row[64];?></textarea></td>
                  			</tr>		
                			</table>
          
			                <TABLE  border=1 width="710" >
					<tr>	<td align=left width="551" colspan="5" bgcolor="#cccccc" height="15"><font size="2" color="#000066" face="arial">
						       <b> OTRA INFORMACIÓN</b></font></td>
					</tr>
					<tr>	<td align=left width="618"colspan="1" height="21"><font size="2" color="#000080" face="arial">
							DIABETES MELLITUS<input type="checkbox" <?php echo $row[69];?> name="C1" value="ON"></font>
							<td align=left width="618"colspan="2" height="21"><font size="2" color="#000080" face="arial">
							ALERGIAS:<b> <?php echo $row[66];?></b></font>
					<tr>	<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">
							TOXICODERMIAS&nbsp; </font><input type="checkbox" <?php echo $row[67];?> name="C1" value="ON"></td>
							<td align=left width="618"colspan="2" height="21"><font size="2" color="#000080" face="arial">
							DIÁTESIS HEMORRÁGICA<input type="checkbox" <?php echo $row[70];?> name="C1" value="ON"></font>
					</tr>
					<tr>	<td align=left width="384" height="21"><font size="2" color="#000080" face="arial">
                    					DROGADICCIÓN</font><input type="checkbox" <?php echo $row[68];?> name="C1" value="ON"></td>
							<td align=left width="563" height="21" colspan="2"><font size="2" color="#000080" face="arial">
                    					COLÍN ESTERAZAS ATÍPICAS</font><input type="checkbox" <?php echo $row[71];?> name="C1" value="ON"></td>
					</tr>
                			<tr>	<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">
							HIPERTERMIA MALIGNA</font><input type="checkbox" <?php echo $row[72];?> name="C1" value="ON"></td>
							<td align=left width="384" height="21"><font size="2" color="#000080" face="arial">
							MIASTENIA GRAVIS</font><input type="checkbox" <?php echo $row[73];?> name="C1" value="ON"></td>
							<td align=left width="563" height="21"><font size="2" color="#000080" face="arial">
							NO CONOCIDAS</font><input type="checkbox" <?php echo $row[74];?> name="C1" value="ON"></td>
					</tr>
					<tr>	<td align=left width="436" colspan="4" bgcolor="#99CCFF" height="65" colspan="4"><font size="2" color="#000080" face="arial">
                    					<span style="background-color: #99CCFF">OBSERVACIONES</span></font><span style="background-color: #99CCFF"> 
                    					: </span><textarea ALIGN="CENTER" ROWS="2" name="S1" cols="84" style="font-family: 12pt; color:#000080;  font-weight: bold"><?php echo $row[75];?></textarea></td>
                    			</tr>
                			</table>
                
					<TABLE  border=1 width="710" >
					<tr>	<td align=left  width="551" colspan="5" bgcolor="#cccccc" height="15"><font size="2" color="#000066" face="arial">
                  					<b>ANTECEDENTES QUIRÚRGICOS</b></font></td>
					</tr>
                			<tr>	<td align=left width="551" height="21"><font size="2" color="#000080" face="arial">
                					COMPLICACIONES ANESTÉSICAS EN LA HISTORIA FAMILIAR</font><input type="checkbox" <?php echo $row[77];?> name="C1" value="ON"></td>
					</tr>
					<tr>	<td align=left width="436" colspan="1" bgcolor="#99CCFF" height="65" colspan="4"><font size="2" color="#000080" face="arial">
                  					<span style="background-color: #99CCFF">ANESTESIAS PREVIAS Y COMPLICACIONES:</span></font><span style="background-color: #99CCFF"> 
                  					: </span><textarea ALIGN="CENTER" ROWS="2" name="S1" cols="84" style="font-family: 12pt; color:#000080;  font-weight: bold"><?php echo $row[78];?></textarea></td>
                			</tr>
                			</table>

					<TABLE  border=1 width="710" >
					<tr>	<td align=left width="551" colspan="5" bgcolor="#cccccc" height="15"><font size="2" color="#000066" face="arial">
                  					<b>MEDICACIÓN PREANESTESIA</b></font></td>
                  
					<tr>	<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">FECHA:<b><?php echo $row[80];?></b></font></TD>
							<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">HORA: <b> <?php echo $row[81];?></b></font></TD>
							<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">VÍA: <b> <?php echo $row[84];?></b></font></TD>
							<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">DOSIS: <b> <?php echo $row[83];?></b></font></TD>
					</tr>
					<tr>	<td align=left width="842" colspan="4" height="21"><font size="2" color="#000080" face="arial">MEDICAMENTO:<b> <?php echo $row[82];?></b></font></TD>
					</tr>
					<tr>	<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">FECHA:<b><?php echo $row[85];?></b></font></TD>
							<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">HORA: <b> <?php echo $row[86];?></b></font></TD>
							<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">VÍA: <b> <?php echo $row[89];?></b></font></TD>
							<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">DOSIS: <b> <?php echo $row[88];?></b></font></TD>
					</tr>
					<tr>	<td align=left width="842" colspan="4" height="21"><font size="2" color="#000080" face="arial">MEDICAMENTO:<b> <?php echo $row[87];?></b></font></TD>
					</tr>
					<tr>	<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">FECHA:<b><?php echo $row[90];?></b></font></TD>
							<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">HORA: <b> <?php echo $row[91];?></b></font></TD>
							<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">VÍA: <b> <?php echo $row[94];?></b></font></TD>
							<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">DOSIS: <b> <?php echo $row[93];?></b></font></TD>
					</tr>
					<tr>	<td align=left width="842" colspan="4" height="21"><font size="2" color="#000080" face="arial">MEDICAMENTO:<b> <?php echo $row[92];?></b></font></TD>
					</tr>
					<tr>	<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">FECHA:<b><?php echo $row[95];?> </font></b></font></TD>
							<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">HORA: <b> <?php echo $row[96];?></font></b></font></TD>
							<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">VÍA: <b> <?php echo $row[99];?></b></font></TD>
							<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">DOSIS: <b> <?php echo $row[98];?></b></font></TD>
					</tr>
					<tr>	<td align=left width="842" colspan="4" height="21"><font size="2" color="#000080" face="arial">MEDICAMENTO:<b> <?php echo $row[97];?></b></font></TD>
					</tr>
					<tr>	<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">FECHA:<b><?php echo $row[100];?></font></b></font></TD>
							<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">HORA: <b> <?php echo $row[101];?></font></b></font></TD>
							<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">VÍA:<b> <?php echo $row[104];?></b></font></TD>
							<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">DOSIS: <b> <?php echo $row[103];?></b></font></TD>
					</tr>
					<tr>	<td align=left width="842" colspan="4" height="21"><font size="2" color="#000080" face="arial">MEDICAMENTO:<b> <?php echo $row[102];?></b></font></TD>
 					</tr>
 					<tr>	<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">FECHA:<b><?php echo $row[105];?></b></font></TD>
							<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">HORA: <b> <?php echo $row[106];?></b></font></TD>
							<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">VÍA: <b> <?php echo $row[109];?></b></font></TD>
							<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">DOSIS: <b> <?php echo $row[108];?></b></font></TD>
				
					<tr>	<td align=left width="842" colspan="4" height="21"><font size="2" color="#000080" face="arial">MEDICAMENTO:<b> <?php echo $row[107];?></b></font></TD>
					</tr>
								</tr>
					<tr>	<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">FECHA:<b><?php echo $row[110];?></b></font></TD>
							<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">HORA: <b> <?php echo $row[111];?></b></font></TD>
							<td align=left width="842" height="21"><font size="2" color="#000080" face="arial"> VÍA: <b> <?php echo $row[114];?></b></font></TD>
							<td align=left width="842" height="21"><font size="2" color="#000080" face="arial">DOSIS: <b> <?php echo $row[113];?></b></font></TD>
					</tr>
										<tr>	<td align=left width="842" colspan="4" height="21"><font size="2" color="#000080" face="arial">MEDICAMENTO:<b> <?php echo $row[112];?></b></font></TD>
					</tr>

					<tr>	<td align=left width="436" colspan="4" bgcolor="#99CCFF" height="65" colspan="4"><B><font size="2" color="#000080" face="arial">
                  					<span style="background-color: #99CCFF">OBSERVACIONES GENERALES:</span></b></font><span style="background-color: #99CCFF">
                  					</span><textarea ALIGN="CENTER" ROWS="2" name="S1" cols="84"style="font-family: 12pt; color:#000080;  font-weight: bold"><?php echo $row[115];?></textarea></td>
                  			</tr>
                			</table>

						
					
	<?php		}	
				
				 else
				{
					echo "<center><table border=0 aling=center>";
					echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
					echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>NO EXISTE PREANESTESIA PARA EL PACIENTE !!!!</MARQUEE></FONT>";
					echo "<br><br>";
				}
		}
		include_once("free.php");
?>
</body>
</html>