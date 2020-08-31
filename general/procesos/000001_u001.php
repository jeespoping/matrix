<html>
<head>
  <title>ENCUESTA UCI V.1.00</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");

/***********************************************************
 *		 	REALIZA UNA LA EVALUACION SELECCIONADA         *
 *	 				A UN ENCUESTADO 	V.1.00             *
 *						CONEX, FREE => OK				   *
 ***********************************************************/

session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{
		$key = substr($user,2,strlen($user));
		

		

		/*Si acaba de responder una pregunta no entra por aqui por que opcion isset con la respuesta*/
		if(!isset($opcion))
		{
			/* Comprobar que los parametros para empezar esten set */
			if(!isset($encuestado)  or !isset($encuesta))
			{
				echo "<form action='000001_u001.php' method=post>";
				echo "<center><table border=0>";
				echo "<tr><td align=center colspan=2><b>CLÍNICA MEDICA LAS AMERICAS </b></td></tr>";
				echo "<tr><td align=center colspan=2>APLICACION ENCUESTAS UCI</td></tr>";
				echo "<tr> </tr>";
				echo "<tr><td bgcolor=#cccccc>NOMBRE</td>";	
				echo "<td bgcolor=#cccccc><select name='encuestado'>";
				// Buscar a las personas registradas en la tabla población y desplegarlas en un drop down
				$query = "select Codigo,Nombre from uci_000001 ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num>0)
				{
					for ($j=0;$j<$num;$j++)
					{	
						$row = mysql_fetch_array($err);
						echo "<option>".$row[0]."-".$row[1]."</option>";
					}	
				}
				echo"</td></tr>";
				echo "<tr><td bgcolor=#cccccc>SELECCIONE LA ENCUESTA</td>";
				echo "<td bgcolor=#cccccc><select name='encuesta'>";
				/*Taer todas las encuestas de la tabla encuestas para ponerlas en un drop down */
				$query = "select Codigo,Descripcion from uci_000002";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				for ($j=0;$j<$num;$j++)
				{	
					$row = mysql_fetch_array($err);
					echo "<option>".$row[0]."-".$row[1]."</option>";
				}
				/*	
				echo"</td></tr>";
				echo "<tr><td bgcolor=#cccccc>FECHA</td>";
				echo"<td bgcolor=#cccccc><input type='text' name='fecha'></td></tr>";*/
				echo "<tr><td  align=center bgcolor=#cccccc colspan=2>";
				echo"<input type='submit' name='ACEPTAR' value='ACEPTAR'></td></tr>";
				echo "</form>"; 
				$opcion='';
			}
			/*Si no ha respondido una pregunta aun para la session que acaba de iniciar
			 pero los parametros de poblacion ya estan llenos*/
			else 
				$opcion="null";
		}	
			
		/*****************************************************************
		* SI  OPCION ES DIFERENTE DE "" ES POR QUE YA ESTAN SET LOS PARAMETROS		*
		*						 Y/O ACABA DE RESPONDER UNA PREGUNTA												*
		******************************************************************/
		if(strcmp($opcion,"")!=0)
		{
			$ini1=strpos($encuestado,"-");
			echo "<center><table border=1 width='520'>";
			echo "<tr><td align=center colspan=3 color bgcolor=#333399><font size='3.8' color='#FFFFFF'><b>CLINICA MEDICA LAS AMERICAS S.A.<b></font></td></tr>";
			echo "<tr><td align=center colspan=3 color bgcolor=#0066CC><font size='3.8' color='#FFFFFF'>APLICACION ENCUESTAS UCI</font></td></tr><tr><td align=center colspan=3 >";
			
			if(strcmp($opcion,'null')==0 and isset($ulpre))
			{
				echo"<table border=0 width='500'><tr><td align=center><font  color='#CC3333'>NO SELECCIONO NINGUNA OPCIÓN COMO RESPUESTA, VUELVA A INTENTARLO.</font></td></tr>";
				echo"<form action='000001_u001.php' method ='post'>";
				echo"<input type='hidden' name='encuestado' value='".$encuestado."'>";
				echo"<input type='hidden' name='encuesta' value='".$encuesta."'>";
				echo"<input type='hidden' name='fecha' value='".date('Y-m-d')."'>";
				echo"<tr><td colspan=3 align='center'><input type='submit'name='aceptar' value='ACEPTAR'></td></tr></tr><tr><td align='center' colspan=3 >";
				exit;
			}
			
			/*si acaba de responder una pregunta $opcion diferente de null*/
			if (strcmp($opcion,'null')!=0)
			{
				$query = "select * from uci_000005   where Codigo_pob='".substr($encuestado,0,$ini1 	)."' and Codigo_enc='".substr($encuesta,0,4)."' and Fecha='".$fecha."' and Ultima_pregunta='".$ulpre."' ";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num>0)
					$value= "<font  color='#CC3333'>USTED YA HA RESPONDIDO ESTA PREGUNTA,<BR> LA INFORMACION NO PUEDE SER MODIFICADA.<BR> CONTINUE EL EXAMEN.</font></td></tr>";
				else
				{
					if(strcmp($corr,$opcion)==0)//respuesta correcta
					{
						$preok++;	
						$value= "<font  color='#CC3333'><img SRC='\MATRIX\images\medical\uci\brillante.gif'>  RESPONDIO <B> CORRECTAMENTE </b>LA PREGUNTA ANTERIOR.  <img SRC='\MATRIX\images\medical\uci\\riendose.gif'></font></td></tr>";
					}
					else		//respuesta incorrecta
						$value= "<font  color='#CC3333'><img SRC='\MATRIX\images\medical\uci\furioso.gif'>  RESPONDIO<B> INCORRECTAMENTE </b>LA PREGUNRA ANTERIOR.  <img SRC='\MATRIX\images\medical\uci\cry.gif'></font></td>"; 
					$query = "update uci_000005 set Fecha='".$fecha."', Ultima_pregunta='".$ulpre."', Preguntas_ok='".$preok."', Calificacion='".(($preok/$ulpre)*10)."' where Codigo_pob='".substr($encuestado,0,$ini1 	)."' and Codigo_enc='".substr($encuesta,0,4)."' ";
					$err = mysql_query($query,$conex);
					if ($err!=1)
						echo"<table border=0 width='500'><tr><td>Error al guardar los datos</td></tr>";
				}
			}
			
			/*El usuario tomara la encuesta por primera vez*/
			if(isset($new))
			{
				$query=" insert into uci_000005 set Medico='uci', Fecha_data='".date('Y-m-d')."', Hora_data='".date('H:i:s')."', Fecha='".date('Y-m-d')."', Ultima_pregunta='0', Preguntas_ok='0', Calificacion='0', Codigo_pob='".substr($encuestado,0,$ini1	)."',Codigo_enc='".substr($encuesta,0,4)."', Seguridad='C-uci' ";
				$err = mysql_query($query,$conex);
				if(mysql_affected_rows()==0)
				{
					echo"<table border=0 width='500'><tr><td>Debido a un error en el sistema usted no podra tomar la encuesta.<br> Comuniquese con el administrador</br></tr><td>";		
					exit;
				}
			}
			
			$query = "select * from uci_000005 where Codigo_pob='".substr($encuestado,0,$ini1)."' and  Codigo_enc='".substr($encuesta,0,4)."' ";
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if($num>0)
			{//La encuesta en cuestion fue tomada por ultima vez por el encuestado en la fecha seleccionada
				$row = mysql_fetch_array($err);
	
				$ce=$row[3];
				$preok=$row[6];
				$ulpre=$row[7];
				$score=$row[8];
				$fechaenc=$row[5];
				//if(strcmp($fechaenc,$fecha)==0)
				//{
						// $encuesta=codigo-descripcion   :. sustr($encuesta,0,4)=codigo  tabla encuesta
						$query = "select Nro_preguntas from uci_000002 where Codigo='".substr($encuesta,0,4)."' ";
						$err = mysql_query($query,$conex);
					    $row = mysql_fetch_array($err);
					    $numax=$row[0];
					    if($numax<=$ulpre)
					    /*Ya respondio todas las preguntas*/
					    {
						    echo "<table border=0 width='500'><tr><td align=center colspan=2><B>USTED YA HA RESPONDIDO LAS ".$numax." PREGUNTAS CORRESPONDIENTES A ESTA EVALUACIÓN.</B></td></tr>";
						    echo "<tr><td align=center colspan=2>CON UN PUNTAJE DE: ".$preok." / ".$numax."</td></tr>";
						    echo "<tr><td align=center colspan=2>VALOR DE LA CALIFICACIÓN: ".(($preok/$numax)*10).", EN UNA ESCALA DE 0 A 10.</td></tr>";
						    echo "<tr><td align=center colspan=2><B>HASTA LUEGO.</B></td></tr>";
						    echo "<tr><td align=center colspan=2><img align='center' SRC='\MATRIX\images\medical\uci\\the_end.gif'></td></tr>";
						}
						else  
						// Aun no ha respondido todas las preguntas
						{
							if(!isset($salir))
							$salir='off';
							
							if(strcmp($salir,'off')==0)
							/* No a Checkeado el boolean para salir (Deseo terminar mi evaluación) */
							{
								echo "ENCUESTADO:<BR><B>".$encuestado."</B></td></tr>";
								if(isset($value))
									echo  "<tr><td align=center colspan=3>".$value;
								echo "<tr><td align=center>TOTAL PREGUNTAS:<B> ".$numax."</B></td>";
								echo"<td align=center>PREGUNTAS EVALUADAS:<B> ".$ulpre."</B></td>";
								echo"<td align=center>RESPUESTAS CORRECTAS:<B> ".$preok."</B></td></tr><tr><td align=center colspan=3>";
								$ulpre++;
								// traer la Descripcion y la opcion correcta para la pregunta ulpre++ de la tabla PREGUNTAS
								$query="select Descripcion,Respuesta from uci_000003 where Codigo_enc='".substr($encuesta,0,4)."' and Numero='".$ulpre."' ";
								//echo $query;
								$err = mysql_query($query,$conex);
								$row = mysql_fetch_array($err);
								$num = mysql_num_rows($err);
								$corr=$row[1];
								echo "<table border=0 width='500'><tr><td align=center colspan=3>SELECCIONE LA RESPUESTA CORRECTA PARA LA SIGUIENTE PREGUNTA</B></td></tr>";
								echo"<form action='000001_u001.php'  method='post'>";
								echo "<tr><td  colspan=3> <B> ".$ulpre.". </B><font  color='#333399'>".strtoupper($row[0])." :<br /></td></tr>";
								echo "<tr><td align=center colspan=2>OPCIONES:</td></tr>";
								$query="select Opcion, Descripcion from uci_000004 where Codigo_enc='".substr($encuesta,0,4)."' and Numero_pre='".$ulpre."' order by Opcion";
								$err = mysql_query($query,$conex);
								$num = mysql_num_rows($err);
								for ($i=0;$i<$num;$i++)
								{
									$row = mysql_fetch_array($err);
									echo"<tr><td width=60></td><td><input type=radio  name='opcion' value='".$row[0]."'><b>".$row[0].".</b> " ;
									echo "<font  color='#333399'>".strtoupper($row[1]).".</td><td></td></tr>"; 
								}			
								echo"<input type='hidden' name='encuestado' value='".$encuestado."'>";
								echo"<input type='hidden' name='encuesta' value='".$encuesta."'>";
								echo"<input type='hidden' name='fecha' value='".date('Y-m-d')."'>";
								echo"<input type='hidden' name='ulpre' value='".$ulpre."'>";
								echo"<input type='hidden' name='corr' value='".$corr."'>";
								echo"<input type='hidden' name='preok' value='".$preok."'>";
								echo "<tr><td colspan=3>DESEO TERMINAR MI EVALUACIÓN:<input type='checkbox' name='salir'> </td><tr>";
								echo"<tr><td colspan=3 align='center'><input type='submit'name='aceptar' value='ACEPTAR'></td></tr>";
								echo "</form>"; 
							}// fin strcmp($salir,'off')==0
							
							else if(strcmp($salir,'on')==0)
							//  Checkeo el boolean para salir (Deseo terminar mi evaluación)
							{
								 echo "<table border=0 width='500'><tr><td align=center colspan=2><b>HA DECIDIDO TERMINAR SU EVALUACIÓN POR EL MOMENTO.</b></td></tr>";
							     echo "<tr><td align=center colspan=2>CON UN PUNTAJE DE: ".$preok." / ".$ulpre.". DE ".$numax." PREGUNTAS.</td></tr>";
							     echo "<tr><td align=center colspan=2>VALOR DE LA CALIFICACIÓN: ".(($preok/$ulpre)*10).", EN UNA ESCALA DE  0 A 10.</td></tr>";
							     echo "<tr><td align=center colspan=2><B>RECUERDE QUE PUEDE RETOMAR LA EVALUACIÓN CUANDO LO DESEE. <BR> HASTA PRONTO.</B></td></tr>";
							}
						}// fin de else del if de $numax<=$ulpre, es decir dentro de el else se hacen las preguntas que faltan
				/*}	
				else
				{
							/*La encuesta fue tomada por el usuario en una fecha difernte*
							echo"<table border=0 width='500'><tr><td colspan=2>LA EVALUACIÓN SELECCIONADA FUE TOMADA POR ULTIMA VEZ EN UNA FECHA DIFERENTE, POR ".$encuestado."</td>";
							echo"<form action='000001_u001.php' method ='post'>";
							echo"<input type='hidden' name='encuestado' value='".$encuestado."'>";
							echo"<input type='hidden' name='encuesta' value='".$encuesta."'>";
							echo"<input type='hidden' name='fecha' value='".$fechaenc."'>";
							echo"<tr><td align=center><input type='submit' name='nfecha' bgcolor=#003344 value='DESEO TOMAR LA EVALUACIÓN AHORA'></form></td>";
							echo"<td align=center><form action='000001_u001.php' method ='post'><input type='submit' name='new' value='VOLVER'></form></td>";
				} */
						
			}
			else 
			{ // La encuesta seleccionada nunca ha sido tomada por el encuestado 
				echo"<table border=0 width='500'><tr><td colspan=2>LA ENCUESTA SELECCIONADA NUNCA HA SIDO TOMADA POR ".$encuestado."</td>";
				echo"<form action='000001_u001.php' method ='post'>";
				echo"<input type='hidden' name='encuestado' value='".$encuestado."'>";
				echo"<input type='hidden' name='encuesta' value='".$encuesta."'>";
				echo"<input type='hidden' name='fecha' value='".date('Y-m-d')."'>";
				echo"<tr><td align=center><input type='submit' name='new' value='DESEO TOMAR LA EVALUACIÓN AHORA'></form></td>";
				echo"<td align=center><form action='000001_u001.php' method ='post'><input type='submit' name='new' value='VOLVER'></form></td>";
				
			}
			echo"</table></td></table>";
		}
	include_once("free.php");
}		
?>		