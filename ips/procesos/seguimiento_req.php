
<head>
  	<title>MATRIX  Seguimiento de Requerimientos - S.G.R</title>
</head>

<script type="text/javascript">
function enter()
{
	document.forms.seguimiento_req.submit();
}
</script>

<?php
include_once("conex.php");

/* **********************************************************
   *     PROGRAMA PARA LA GESTION Y ADMINISTRACION DE       *
   *                   REQUERIMIENTOS                       *
   **********************************************************/

//==================================================================================================================================
//PROGRAMA                   : seguimiento_req.php
//AUTOR                      : Juan David Jaramillo R.
$wautor="Juan D. Jaramillo R.";
//FECHA CREACION             : Diciembre 14 de 2006
//FECHA ULTIMA ACTUALIZACION :
$wactualiz="(Version Diciembre 14 de 2006)";
//DESCRIPCION
//================================================================================================================================\\
//Este programa permite consultar a los usuarios los requerimientos grabados previmente, mediante el numero de caso asignado por  \\
//el programa de grabacion.    Tambien permtira realizar seguimiento de los casos adicionando informacion y realizando escalamien-\\
//to hacia sistemas de la Clinica las Americas.
//================================================================================================================================\\

//================================================================================================================================\\
//================================================================================================================================\\
//ACTUALIZACIONES                                                                                                                 \\
//================================================================================================================================\\
//                                                                                                                                \\
//________________________________________________________________________________________________________________________________\\
//X X X X X X X X X  ## DE 2006:                                                                                                  \\
//________________________________________________________________________________________________________________________________\\
//xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx\\
//                                                                                                                                \\
//________________________________________________________________________________________________________________________________\\

// COLORES
$wcf="DDDDDD";   //COLOR DEL FONDO    -- Gris claro
$wcf2="006699";  //COLOR DEL FONDO 2  -- Azul claro
$wclfa="FFFFFF"; //COLOR DE LA LETRA  -- Blanca CON FONDO Azul claro
$wclfg="003366"; //COLOR DE LA LETRA  -- Azul oscuro CON FONDO Gris claro
$wclcy="#A4E1E8"; //COLOR DE TITULOS  -- Cyan
$color="#999999";


// FUNCIONES

function enviar_correo($wcaso,$email,$wasunto,$wmensaje)
{
	global $wsgrclr;
	global $mail;
	
	if ($wsgrclr=="H")
	   $e_mail="sistemas@clinicadelsur.com";
	  else
	     $e_mail="juanc@pmamericas.com"; 
	     
	$mail->IsSMTP(); // telling the class to use SMTP
	//$mail->Host = "192.168.1.100"; // SMTP server
	$mail->Host = "132.1.18.1"; // SMTP server
	$mail->From = $e_mail;
	$mail->AddAddress($email);
	$mail->Subject = $wasunto;
	$mail->Body = $wmensaje;
	$mail->WordWrap = 100;

	if(!$mail->Send())
		$resulMai=0;
	else
		$resulMai=1;

	return $resulMai;
}

function cargar_datos()
{
	global $wsgrcsc;
	global $wsgrnom;
	global $wsgrext;
	global $wsgrare;
	global $wsgrmai;
	global $wbasedato;
	global $conex;
	global $wusuario;

	$q= " SELECT ugrnom,ugrext,ugrare,ugrema ".
		"   FROM ".$wbasedato."_000124 ".
		"  WHERE ugrcod ='".$wusuario."'".
		"    AND ugrest = 'on'";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$row = mysql_fetch_array($res);

		$wsgrnom=$row[0];
		$wsgrext=$row[1];
		$wsgrare=$row[2];
		$wsgrmai=$row[3];

		$existe="true";
	}
	else
	{
		$existe="false";
	}

	return $existe;
}

function traer_mail($ususis)
{
	global $wbasedato;
	global $conex;

	$q= " SELECT ugsmai ".
		"   FROM ".$wbasedato."_000126 ".
		"  WHERE ugscod = (mid('".$ususis."',1,instr('".$ususis."','-')-1)) ".
		"    AND ugsest = 'on'";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	$row = mysql_fetch_array($res);

	return $row[0];
}

function traer_empresa($ususis)
{
	global $wbasedato;
	global $conex;

	$q= " SELECT empresa ".
		"   FROM usuarios ".
		"  WHERE codigo = '".$ususis."'".
		"    AND activo = 'A'";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	$row = mysql_fetch_array($res);

	return $row[0];
}

function validar_usu()
{
	global $wbasedato;
	global $conex;
	global $wusuario;

	$q= " SELECT ugsnom,ugstip ".
		"   FROM ".$wbasedato."_000126 ".
		"  WHERE ugscod ='".$wusuario."'".
		"    AND ugsest = 'on'";

	$res = mysql_query($q,$conex);
	$num = mysql_num_rows($res);

	if ($num > 0)
	{
		$row = mysql_fetch_array($res);

		$wugsnom=$row[0];
		$wugstip=$row[1];

		$wvalusu="on";
	}
	else
	{
		$wvalusu="off";
		$wugsnom="";
		$wugstip="";
	}

	return array($wvalusu,$wugsnom,$wugstip);
}

function iniciar()
{
	$wsgrcsc="";
	$wsgrnom="";
	$wsgrext="";
	$wsgrare="";
	$wsgrmai="";
	$wsgrapl="";
	$wsgrmen="";
	$wsgrpro="";
	$wsgrtpr="";
	$wsgrurg="";
	$wdes="";

	echo "<input type='HIDDEN' name= 'wsgrcsc' value='".$wsgrcsc."'>";
	echo "<input type='HIDDEN' name= 'wsgrnom' value='".$wsgrnom."'>";
	echo "<input type='HIDDEN' name= 'wsgrext' value='".$wsgrext."'>";
	echo "<input type='HIDDEN' name= 'wsgrare' value='".$wsgrare."'>";
	echo "<input type='HIDDEN' name= 'wsgrmai' value='".$wsgrmai."'>";
	echo "<input type='HIDDEN' name= 'wsgrapl' value='".$wsgrapl."'>";
	echo "<input type='HIDDEN' name= 'wsgrmen' value='".$wsgrmen."'>";
	echo "<input type='HIDDEN' name= 'wsgrpro' value='".$wsgrpro."'>";
	echo "<input type='HIDDEN' name= 'wsgrtpr' value='".$wsgrtpr."'>";
	echo "<input type='HIDDEN' name= 'wsgrurg' value='".$wsgrurg."'>";
	echo "<input type='HIDDEN' name= 'wdes' value='".$wdes."'>";
}

// INICIALIZACION DE VARIABLES
require("class.phpmailer.php");
$mail = new PHPMailer();

$valido=1;

session_start();
if (!isset($user))
{
	if(!isset($_SESSION['user']))
		session_register("user");
}

if(!isset($_SESSION['user']))
	echo "Error, Usuario NO Registrado";
else
{
	echo "<form name='seguimiento_req' action='seguimiento_req.php' method=post>";
	

	

	echo "<input type='HIDDEN' name= 'wbasedato' value='".$wbasedato."'>";

	if (isset($wsgrcsc) and $wsgrcsc!="") echo "<input type='HIDDEN' name= 'wsgrcsc' value='".$wsgrcsc."'>";
	
	$pos = strpos($user,"-");
    $wusuario = substr($user,$pos+1,strlen($user));

    $wfecha=date("Y-m-d");
    $whora = (string)date("H:i:s");
    ////$wbd="clisur";
    //$wusuemp=traer_empresa($wusuario);
    $wusuemp="CLISUR";

    if (!isset($wgrabar) or $wgrabar=="")
       {
	    //=========================================================================================================   
	    //=========================================================================================================
	    $wordeno="off";
    
	    //ESTO PARA PODER HACER EL ORDEN DE LA INFORMACION EN EL QUERY
	    if (isset($fec) or isset($usu) or isset($imp) or isset($res))
	       {
		    //Ordenado por Fecha y Hora 
	        if (isset($fec))
	           $worden="ORDER BY 5 ".$fec.",6 desc";  
	           
	        //Ordenado por Usuario que coloca el caso 
	        if (isset($usu))
	           $worden="ORDER BY 1 ".$usu;  
	           
	        //Ordenado por Importancia 
	        if (isset($imp))
	           $worden="ORDER BY 16 ".$imp; 
	           
	        //Ordenado por Responsable de Solucion
	        if (isset($res))
	           $worden="ORDER BY 10 ".$res;      
	        
	        $wordeno="on";   
	       }
	      else
	        $worden = "ORDER BY 5 desc, 6 desc "; 
	        
	    
	    if (!isset($worden) or $worden=="") $worden="ORDER BY 5, 6 desc ";  
	    
	    //=========================================================================================================   
	    //========================================================================================================= 
	       
	    ////echo "<p align=right><font size=2><b>Autor: ".$wautor."</b></font></p>";
		echo "<table align=center border=6>";
		////echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
		echo "<tr><td align=CENTER colspan=10><img src='/matrix/images/medical/pos/logo_".$wbasedato.".png' WIDTH=340 HEIGHT=100></td></tr>";
		echo "<tr><td colspan=10>&nbsp;</td></tr>";
		echo "<tr><td align=center bgcolor=".$wcf." colspan=10><b>Seguimiento a Requerimientos de Sistemas</b></td></tr>";
		echo "</table>";
		
		echo "<br>";

		//Verifico que este matriculado en la tabla de usuarios del sistema de requerimientos
		$q= "SELECT ugrnom "
		   ."  FROM ".$wbasedato."_000124 "
		   ." WHERE ugrcod = '".$wusuario."'"
		   ."  UNION "
		   ." SELECT ugsnom "
		   ."  FROM ".$wbasedato."_000126 "
		   ." WHERE ugscod = '".$wusuario."'";
		$err = mysql_query($q,$conex) or die (mysql_errno().":".mysql_error());   
		$num = mysql_num_rows($err);
		$rowusu = mysql_fetch_array($err);
		
		if ($num > 0)
		   {
			//Verifico si es un usuario del personal de sistemas
			$q= "SELECT ugstip "
			   ."  FROM ".$wbasedato."_000126 "
			   ." WHERE ugscod = '".$wusuario."'";
			$err = mysql_query($q,$conex) or die (mysql_errno().":".mysql_error());   
			$row = mysql_fetch_array($err);
		   
			if ($row[0]=="C")  //===> Coordinador
		       {
				//Aca selecciono todos los casos pendientes
				$query = " SELECT  sgrnom, sgrext, sgrare, sgrmai, ".$wbasedato."_000122."."fecha_data, ".$wbasedato."_000122."."hora_data, sgresc, "
				        ."         sgrfes, sgrhes, sgrres, sgrest, sgrfre, sgrhre, sgrdes, sgrtre, sgrurg, sgrapl, sgrcsc "
				        ."   FROM  ".$wbasedato."_000122, ".$wbasedato."_000126 "
				        ."  WHERE  ugsnom = sgrres "
				        ."    AND  sgrest not in ('C','R','NO VALIDO') ".$worden;   
			   }	
			  else             //===> Analista
			     {
				  if ($row[0] != "")  //Quiere decir que es un analista de sistemas
				     {   
					  //Aca selecciono todos los casos pendientes
					  $query = " SELECT  sgrnom, sgrext, sgrare, sgrmai, ".$wbasedato."_000122."."fecha_data, ".$wbasedato."_000122."."hora_data, sgresc, "
					          ."         sgrfes, sgrhes, sgrres, sgrest, sgrfre, sgrhre, sgrdes, sgrtre   , sgrurg, sgrapl, sgrcsc "
					          ."   FROM  ".$wbasedato."_000122, ".$wbasedato."_000126 "
					          ."  WHERE  ugscod = '".$wusuario."'"
					          ."    AND  ugsnom = sgrres "
					          ."    AND  sgrest not in ('C','R','NO VALIDO') ".$worden; 
				     }
				    else
				      {   
					   //Aca selecciono todos los casos pendientes de un usuario final
					   $query = " SELECT  sgrnom, sgrext, sgrare, sgrmai, ".$wbasedato."_000122."."fecha_data, ".$wbasedato."_000122."."hora_data, sgresc, "
					           ."         sgrfes, sgrhes, sgrres, sgrest, sgrfre, sgrhre, sgrdes, sgrtre   , sgrurg, sgrapl, sgrcsc "
					           ."   FROM  ".$wbasedato."_000122 "
					           ."  WHERE  sgrnom = '".$rowusu[0]."'"
					           ."    AND  sgrest not in ('C','R','NO VALIDO') ".$worden; 
				      }          
				 }    
			     
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
			$num = mysql_num_rows($err);      	
			
			if ($num > 0 and (!isset($wsgrcsc) or $wsgrcsc==""))
			   {	
				$hora = (string)date("H:i:s");   
				echo "<table border=0>";
			    echo "<tr><td align=left><font size=2 text color=#CC0000><b>Hora: ".$hora."&nbsp&nbsp  ===> &nbsp&nbsp Cantidad de Requerimientos Pendientes: ".$num."</b></font></td></tr>";
			    echo "</table>";   
				   
				echo "<center><table border='6'>";
			    echo "<tr>";
			    echo "<th bgcolor=#3333FF colspan=1><a href='seguimiento_req.php?fec=asc"."&amp;wbasedato=".$wbasedato."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><font size=4 color=FFFF33>Fecha</font><a href='seguimiento_req.php?fec=desc"."&amp;wbasedato=".$wbasedato."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></th>";
			    echo "<th bgcolor=#3333FF colspan=1><font size=4 color=FFFF33>Nro Caso</font></th>";
			    echo "<th bgcolor=#3333FF colspan=1><a href='seguimiento_req.php?usu=asc"."&amp;wbasedato=".$wbasedato."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><font size=4 color=FFFF33>Usuario</font><a href='seguimiento_req.php?usu=desc"."&amp;wbasedato=".$wbasedato."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></th>";
			    echo "<th bgcolor=#3333FF colspan=1><font size=4 color=FFFF33>Asunto</font></th>";
			    echo "<th bgcolor=#3333FF colspan=1><a href='seguimiento_req.php?imp=asc"."&amp;wbasedato=".$wbasedato."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><font size=4 color=FFFF33>Importancia</font><a href='seguimiento_req.php?imp=desc"."&amp;wbasedato=".$wbasedato."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></th>";
			    echo "<th bgcolor=#3333FF colspan=1><font size=4 color=FFFF33>Area</font></th>";
			    echo "<th bgcolor=#3333FF colspan=1><a href='seguimiento_req.php?res=asc"."&amp;wbasedato=".$wbasedato."'><img src='/matrix/images/medical/iconos/gifs/i.p.previous[1].gif'></a><font size=4 color=FFFF33>Responsable</font><a href='seguimiento_req.php?res=desc"."&amp;wbasedato=".$wbasedato."'><img src='/matrix/images/medical/iconos/gifs/i.p.next[1].gif' ></a></th>";
			    echo "<th bgcolor=#3333FF colspan=1><font size=4 color=FFFF33>Estado</font></th>";
			    echo "<th bgcolor=#3333FF colspan=1><font size=4 color=FFFF33>&nbsp</font></th>";
			    echo "</tr>";
			    
			    for ($i=1;$i<=$num;$i++)
			       {
				    $row = mysql_fetch_array($err);
				       
				    //===========================================================================
				    $wfec=explode("-",$row[4]); //Aca separo la fecha para tomar el mes y el día
			        switch ($wfec[1])
			           {
				        case "01":
				           { $wmes="Ene-".$wfec[2]; }
				           break;
				        case "02":
				           { $wmes="Feb-".$wfec[2]; }
				           break; 
				        case "03":
				           { $wmes="Mar-".$wfec[2]; }
				           break;
				        case "04":
				           { $wmes="Abr-".$wfec[2]; }
				           break;
				        case "05":
				           { $wmes="May-".$wfec[2]; }
				           break; 
				        case "06":
				           { $wmes="Jun-".$wfec[2]; }
				           break;
				        case "07":
				           { $wmes="Jul-".$wfec[2]; }
				           break;
				        case "08":
				           { $wmes="Ago-".$wfec[2]; }
				           break; 
				        case "09":
				           { $wmes="Sep-".$wfec[2]; }
				           break;
				        case "10":
				           { $wmes="Oct-".$wfec[2]; }
				           break;
				        case "11":
				           { $wmes="Nov-".$wfec[2]; }
				           break; 
				        case "12":
				           { $wmes="Dic-".$wfec[2]; }
				           break;                        
			           }
				    //===========================================================================
				    
				    //===========================================================================
				    //Importancia
				    //===========================================================================
				    switch ($row[15])
				       {
					    case "A":
					      {$wimportancia="ALTA";}
					      break;
					    case "M":
					      {$wimportancia="MEDIA";}
					      break;  
					    case "B":
					      {$wimportancia="BAJA";}
					      break; 
					    default:
					      {$wimportancia="NO DEFINIDA";}
					      break;       
					   }    
				    
				    //===========================================================================
				    //Importancia
				    //===========================================================================
				    switch ($row[10])
				       {
					    case "S":
					      {$westado="SIN EVALUAR";}
					      break;
					    case "P":
					      {$westado="EN PROMOTORA";}
					      break;  
					    case "T":
					      {$westado="EN TRAMITE";}
					      break;  
					    case "R":
					      {$westado="RECHAZADO";}
					      break;
					    case "V":
					      {$westado="CERRADO";}
					      break;  
					    default:
					      {$westado="NO DEFINIDO";}
					      break;        
					   } 
					   
				    if ($i%2==0)
		                $wcolor="00FFFF";
		               else
		                  $wcolor="";
				    
				    echo "<tr>";
				    echo "<td bgcolor='".$wcolor."'>".$wmes."</td>";                                     //Fecha
				    echo "<td bgcolor='".$wcolor."'>".$row[17]."</td>";                                  //Nro Caso
				    echo "<td bgcolor='".$wcolor."'>".$row[0]."</td>";                                   //Usuario que coloca el caso
				    echo "<td bgcolor='".$wcolor."'><font size=2>".substr($row[13],0,30)."</font></td>"; //Caso o Asunto
				    echo "<td bgcolor='".$wcolor."' align=center>".$wimportancia."</td>";                //Importancia
				    echo "<td bgcolor='".$wcolor."'>".$row[2]."</td>";                                   //Area
				    echo "<td bgcolor='".$wcolor."'>".$row[9]."</td>";                                   //Responsable
				    echo "<td bgcolor='".$wcolor."'>".$westado."</td>";                                  //Estado del caso
				    echo "<td bgcolor='".$wcolor."'><font size=4 color=FFFF33><a href='seguimiento_req.php?wsgrcsc=".$row[17]."&amp;wbasedato=".$wbasedato."&amp;wswseg=on'>Seguimiento</a></font></td>";
				   }    
		       }
		      else
		         if (isset($wswseg) and $wswseg!="on")
			        {
				     ?>	    
				      <script>
				         alert ("!!!! ATENCION !!!! NO tiene requerimientos pendientes");
				      </script>
				     <?php   
				    }     	   
	       } 
        }        
        
        
		if (isset($wsgrcsc) and trim($wsgrcsc)!="")
		   {
            $query = " SELECT  sgrnom, sgrext, sgrare, sgrmai, fecha_data, hora_data, sgresc, sgrfes, sgrhes, "
                    ."         sgrres, sgrest, sgrfre, sgrhre, sgrdes    , sgrtre   , sgrurg, sgrapl, Sgrclr ".
			         "   FROM  ".$wbasedato."_000122 ".
			         " 	WHERE  sgrcsc= ".$wsgrcsc;
			$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
		    $num = mysql_num_rows($err);
		         
			if ($num>0)
			   {
				$row = mysql_fetch_array($err);
				$wsgrnom=$row[0];
				$wsgrext=$row[1];
				$wsgrare=$row[2];
				$wsgrmai=$row[3];
				$wsgrfgr=$row[4];
				$wsgrhgr=$row[5];
				$wsgresc=$row[6];
				$wsgrfes=$row[7];
				$wsgrhes=$row[8];
				$wsgrres=$row[9];
				$wsgrest=$row[10];
				$wsgrfre=$row[11];
				$wsgrhre=$row[12];
				$wdesc=$row[13];
				$wsgrtre=$row[14];
				$wsgrurg=$row[15];
				$wsgrapl=$row[16];
				$wsgrclr=$row[17];

				switch($wsgrest)
				   {
					case "S":
					$wdesest= "SIN EVALUAR";
					break;
					case "P":
					$wdesest= "EN PROMOTORA";
					break;
					case "T":
					$wdesest= "EN TRAMITE";
					break;
					case "R":
					$wdesest= "RECHAZADO";
					break;
					case "C":
					$wdesest= "CERRADO";
					break;
					default:
					$wdesest= "NO VALIDO";
				   }

				switch($wsgrtre)
				   {
					case "S":
					$wdestre= "SOLICITUD";
					break;
					case "E":
					$wdestre= "ERROR";
					break;
					default:
					$wdestre= "NO APLICA";
				   }

				switch($wsgrurg)
				   {
					case "A":
					$wdesurg= "ALTA";
					break;
					case "M":
					$wdesurg= "MEDIA";
					break;
					case "B":
					$wdesurg= "BAJA";
					break;
					default:
					$wdesurg= "NO APLICA";
				   }

				echo "<table border=0 align=center width='65%'>";
				echo "<tr>";
				echo "<td><font text size=2>Nombre del Usuario:</td>";
				echo "<td><font text size=2>".$wsgrnom."</font></td>";
				echo "<td><font text size=2>Asignado A:</td>";
				echo "<td><font text size=2>".$wsgrres."</font></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td><font text size=2>Extension:</font></td>";
				echo "<td><font text size=2>".$wsgrext."</font></td>";
				echo "<td><font text size=2>Fecha Escalamiento:</font></td>";
				echo "<td><font text size=2>".$wsgrfes."</font></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td><font text size=2>Area:</font></td>";
				echo "<td><font text size=2>".$wsgrare."</font></td>";
				echo "<td><font text size=2>Hora Escalamiento:</font></td>";
				echo "<td><font text size=2>".$wsgrhes."</font></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td><font text size=2>e-mail:</font></td>";
				echo "<td><font text size=2>".$wsgrmai."</font></td>";
				echo "<td><font text size=2>Fecha Cierre:</font></td>";
				echo "<td><font text size=2>".$wsgrfre."</font></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td><font text size=2>Fecha Creacion:</font></td>";
				echo "<td><font text size=2>".$wsgrfgr."</font></td>";
				echo "<td><font text size=2>Hora Cierre:</font></td>";
				echo "<td><font text size=2>".$wsgrhre."</font></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td><font text size=2>Hora Creacion:</font></td>";
				echo "<td><font text size=2>".$wsgrhgr."</font></td>";
				echo "<td><font text size=2>Sistema:</font></td>";
				echo "<td><font text size=2>".$wsgrapl."</font></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td><font text size=2>Tipo:</font></td>";
				echo "<td bgcolor=".$wcf."><font text size=2>".$wdestre."</font></td>";
				echo "<td><font text size=2>Estado:</font></td>";
				echo "<td bgcolor=".$wcf."><font text size=2>".$wdesest."</font></td>";
				echo "</tr>";
				echo "<tr>";
				echo "<td><font text size=2>Prioridad:</font></td>";
				echo "<td bgcolor=".$wcf."><font text size=2>".$wdesurg."</font></td>";
				echo "</tr>";


				$query = " SELECT  segnid, fecha_data, hora_data, segdes, segres ".
						 "   FROM  ".$wbasedato."_000125 ".
						 " 	WHERE  segcsc= ".$wsgrcsc;

				$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
				$num = mysql_num_rows($err);

				if (!isset($wseg))
					$wseg="";

				if ($num>0)
				   {
					for ($i=1;$i<=$num;$i++)
	        		   {
	        		   	$row = mysql_fetch_array($err);
	        		   	$wsegnid=$row[0];
						$wsegfec=$row[1];
						$wseghor=$row[2];
						$wsegdes=$row[3];
						$wsegres=$row[4];

						$wlin="   \n";
						$wid="Id.Seg: ".$wsegnid."\n";
						$wfec="Fec.Seg: ".$wsegfec." - Hora.Seg: ".$wseghor."\n";
						$wres="Responsable: ".$wsegres."\n";
						$wdes=$wsegdes."\n";
						$wseg=$wseg.$wlin.$wid.$wfec.$wres.$wdes;
	        		   }
	        		$wdesc=$wdesc."\n".$wseg; 
				   }

				echo "<tr><td colspan=10>&nbsp;</td></tr>";
				echo "<tr><td colspan=4 align=left bgcolor=".$wcf."><font text size=2>Descripcion del Requerimiento:</font></td></tr>";
				echo "<tr>";
				echo "<td bgcolor='".$wcf."' colspan=4 align=center><b><textarea name='wdes' cols='80' rows='10' readonly>".$wdesc."</textarea></td>";
				echo "</tr>";
   
					//Tipo de requerimiento Hardware o Software
					if ($wsgrclr=="H")
					   $wsistemas="Departamento de Sistemas Clinica del Sur.";
					  else
					     $wsistemas="Dirección de Informática PMLA.";   
				
					list ($wusuval,$wugsnom,$wugstip)=validar_usu();

					if (($wusuval=="on") and ($wsgrest <> "R") and ($wsgrest <> "C"))
					  {
						echo "<tr><td colspan=10>&nbsp;</td></tr>";
						echo "<tr><td colspan=4 align=left bgcolor=".$wcf."><font text size=2><b>Grabacion del Seguimiento:</b></font></td></tr>";

						if(!isset($wsegree))				// Asigna a la vble responsable del seguimiento el responsable actual del caso.
							$wsegree=$wsgrres;

						echo "<tr>";
						if ($wugstip=="C")
						  {
							if($wsgresc == "on")
								echo "<td colspan=2 align=left bgcolor=".$wcf."><b><font text color=".$wclfg." size=2><b>Escalar Requerimiento: &nbsp&nbsp</b><input type='checkbox' name='wescalar' onclick='enter()' checked disabled></font></td>";
							else
							   {
								if(!isset($wescalar))
									echo "<td colspan=2 align=left bgcolor=".$wcf."><b><font text color=".$wclfg." size=2><b>Escalar Requerimiento: &nbsp&nbsp</b><input type='checkbox' name='wescalar' onclick='enter()'></font></td>";
								else
									echo "<td colspan=2 align=left bgcolor=".$wcf."><b><font text color=".$wclfg." size=2><b>Escalar Requerimiento: &nbsp&nbsp</b><input type='checkbox' name='wescalar' onclick='enter()' checked></font></td>";

								if (isset($wescalar))
								   {
									$wsgrfes=$wfecha;	// Muevo la fecha para el control de escalamiento.
									$wsgrhes=$whora;	// Muevo la hora para el control de escalamiento.
									$wsgresc="on";		// Llevo on al campo de escalamiento de requerimientos.
								   }
							   }

							if (!isset($wususis))
							  {
								echo "<td colspan=2 align=left bgcolor=".$wcf."><b><font text color=".$wclfg." size=2>Asignar A: &nbsp&nbsp </font></b><select name='wususis'>";

        							$query= " SELECT ugscod,ugsnom ".
											"   FROM ".$wbasedato."_000126 ".
											"  WHERE ugsest = 'on'".
											"  ORDER BY ugsnom ";

									$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
    								$num = mysql_num_rows($err);

    								echo "<option></option>";
    								for ($i=1;$i<=$num;$i++)
        							   {
            							$row = mysql_fetch_array($err);
            							echo "<option>".$row[0]."-".$row[1]."</option>";
        							   }
        							echo "</select></td>";
    						  }
    						else
    						  {
    							echo "<td colspan=2 align=left bgcolor=".$wcf."><b><font text color=".$wclfg." size=2>Asignar A: &nbsp&nbsp </font></b><select name='wususis' onchange='enter()' ondblclick='enter()'>";
    							echo "<option>".$wususis."</option>";

								$query= " SELECT ugscod,ugsnom ".
										"   FROM ".$wbasedato."_000126 ".
										"  WHERE ugscod != (mid('".$wususis."',1,instr('".$wususis."','-')-1)) ".
										"    AND ugsest = 'on'".
										"  ORDER BY ugsnom ";

    							$err = mysql_query($query,$conex) or die (mysql_errno().":".mysql_error());
								$num = mysql_num_rows($err);

    							for ($i=1;$i<=$num;$i++)
    							   {
        							$row = mysql_fetch_array($err);
        							echo "<option>".$row[0]."-".$row[1]."</option>";
        						   }
    							echo "</select></td>";
    						  }
    						echo "</tr>";

    						if (isset($wususis))
    						  {
    							$wugsmai=traer_mail($wususis);
    							$wugsusu=explode("-",$wususis);
    							if (isset($wugsusu[1]))
    								$wsegree=$wugsusu[1];
    						  }
						 }
						else
						  {
							if(!isset($wsgresc))
								echo "<td colspan=2 align=left bgcolor=".$wcf."><b><font text color=".$wclfg." size=2><b>Escalar Requerimiento: &nbsp&nbsp</b><input type='checkbox' name='wescalar' onclick='enter()' disabled></font></td>";
							else
								echo "<td colspan=2 align=left bgcolor=".$wcf."><b><font text color=".$wclfg." size=2><b>Escalar Requerimiento: &nbsp&nbsp</b><input type='checkbox' name='wescalar' onclick='enter()' checked disabled></font></td>";

							echo "<td colspan=2 align=left bgcolor=".$wcf."><b><font text color=".$wclfg." size=2>Asignar A: &nbsp&nbsp </font></b><input type='TEXT' name='wsegree' value='".$wsegree."' size=30 maxlength=30 disabled></td></tr>";
						  }

						echo "<tr>";
						echo "<td colspan=2	 align=left bgcolor=".$wcf."><b><font text color=".$wclfg." size=2> Estado: </font></b><select name='wsegest'>";
						if (isset($wsegest))
						  {
							if ($wsegest== "EN TRAMITE")
							  {
								echo "<option selected>EN TRAMITE</option>";
								echo "<option>EN PROMOTORA</option>";
								echo "<option>RECHAZADO</option>";
								echo "<option>CERRADO</option>";
							  }
							else
							  {
								if ($wsegest== "EN PROMOTORA")
								  {
									echo "<option selected>EN PROMOTORA</option>";
									echo "<option>EN TRAMITE</option>";
									echo "<option>RECHAZADO</option>";
									echo "<option>CERRADO</option>";
								  }
								 else
								   {
									if ($wsegest== "RECHAZADO")
									  {
										echo "<option selected>RECHAZADO</option>";
										echo "<option>EN TRAMITE</option>";
										echo "<option>EN PROMOTORA</option>";
										echo "<option>CERRADO</option>";
									   }
									 else
									   {
										if ($wsegest== "CERRADO")
										   {
											echo "<option selected>CERRADO</option>";
											echo "<option>EN TRAMITE</option>";
											echo "<option>EN PROMOTORA</option>";
											echo "<option>RECHAZADO</option>";
										   }
										  else
										     {
											  echo "<option></option>";
											  echo "<option>EN TRAMITE</option>";
											  echo "<option>EN PROMOTORA</option>";
											  echo "<option>RECHAZADO</option>";
											  echo "<option>CERRADO</option>";
										     }
									   }
								   }
							  }
						  }
						 else
						   {
							echo "<option></option>";
							echo "<option>EN TRAMITE</option>";
							echo "<option>EN PROMOTORA</option>";
							echo "<option>RECHAZADO</option>";
							echo "<option>CERRADO</option>";
						   }
						echo "</select></td>";

						if (isset($wsegest) and ($wsegest== "EN TRAMITE"))
						  {
							if(!isset($winfseg))
								echo "<td colspan=2 align=left bgcolor=".$wcf."><b><font text color=".$wclfg." size=2><b>Enviar Seguimiento al Usuario: &nbsp&nbsp</b><input type='checkbox' name='winfseg' onclick='enter()'></font></td>";
							else
						       echo "<td colspan=2 align=left bgcolor=".$wcf."><b><font text color=".$wclfg." size=2><b>Enviar Seguimiento al Usuario: &nbsp&nbsp</b><input type='checkbox' name='winfseg' onclick='enter()' checked></font></td>";
						  }
						else 	
						   echo "<td colspan=2 align=left bgcolor=".$wcf."><b><font text color=".$wclfg." size=2><b>Enviar Seguimiento al Usuario: &nbsp&nbsp</b><input type='checkbox' name='winfseg' onclick='enter()' disabled></font></td>";

						echo "</tr>";
						echo "<tr><td colspan=4 bgcolor='".$wcf."'align=left><b><font text color=".$wclfg." size=2>Descripcion del Seguimiento:</font></b></td></tr>";
						if (!isset($wsegseg))
							$wsegseg='';
						echo "<tr><td colspan=4 bgcolor='".$wcf."' align=center><b><textarea name='wsegseg' cols='80' rows='5'>".$wsegseg."</textarea></td></tr>";

						echo "<input type='HIDDEN' NAME= 'resultado' value='1'>";
						
						echo "<tr><td colspan=10>&nbsp;</td></tr>";
						echo "<tr><td align=center bgcolor=#dddddd colspan=4><font size=2><b>Grabar el Seguimiento</b><input type='checkbox' name='wgrabar' onchange='enter'()></font></td></tr>";
						echo "<td align=left bgcolor=#cccccc><a href='seguimiento_req.php?wbasedato=".$wbasedato."'><font size=4><b>Retornar</b></font></a></td>";
						echo "<td align=center bgcolor=#cccccc colspan=2><input type='submit' value='OK'></td>";
						echo "<td align=right bgcolor=#cccccc><a href='seguimiento_req.php?wbasedato=".$wbasedato."'><font size=4><b>Retornar</b></font></a></td>";
						echo "<tr><td colspan=10>&nbsp;</td></tr>";
						echo "</table>";

						if (isset($wgrabar))
						   {
							switch($wsegest)
							   {
								case "EN TRAMITE":
								$wsgrest= "T";
								break;
								case "EN PROMOTORA":
								$wsgrest= "P";
								break;
								case "RECHAZADO":
								$wsgrest= "R";
								break;
								case "CERRADO":
								$wsgrest= "C";
								break;
							   }

							if ((!isset($wsegest) or ($wsegest == "")) and  $valido==1)
							  {
								?>
									<script>
									alert ("Debe Seleccionar un Estado para el Seguimiento");
									function ira(){document.seguimiento_req.wsegest.focus();}
									</script>
								<?php
								$valido=0;
							  }
							if ((!isset($wsegseg) or ($wsegseg == "")) and $valido==1)
							  {
								?>
									<script>
									alert ("Debe Ingresar una Descripcion del Seguimiento");
									function ira(){document.seguimiento_req.wsegseg.focus();}
									</script>
								<?php
								$valido=0;
							  }

							if ($valido==1)
							   {
								if ((isset($wususis)) and ($wususis <> "") and ($wsgrest <> "R") and ($wsgrest <> "C") and $valido==1)
								  {
									$wmailto=$wugsmai;
									$wasunto="Solicitud de Atencion a un Requerimiento!!!";
									$wmensaje="\nCordial Saludo! \n\n\nSe ha grabado un nuevo seguimiento al requerimiento Nro: ".$wsgrcsc." !!!, el cual le fue asignado para que usted lo resuelva. \n\n\n Cordialmente, \n\n\n ".$wsistemas.
									          "\n\n\nEste mensaje ha sido enviado de manera Automatica por la Aplicacion de Gestion de Requerimientos. \n";

									$valido=enviar_correo($wsgrcsc,$wmailto,$wasunto,$wmensaje);
								  }

								if (($wsgrest=="R") and $valido==1)
								  {
									$wsgrfre=$wfecha;	// Muevo la fecha para el cierre del requerimiento.
									$wsgrhre=$whora;	// Muevo la hora para el cierre del requerimiento.
									$wmailto=$wsgrmai;	// Llevo el email del Usuario.

									$wasunto="Respuesta de Sistemas a su Requerimiento!!!";
									$wmensaje="\nCordial Saludo! \n\n\nSu requerimiento Nro. ".$wsgrcsc.", ha sido evaluado y analizado por el Dpto. de Sistemas, encontrandolo ".
									          "no viable y por lo tanto ha sido rechazado.   Descripción del motivo: ".$wsegseg.". \n\n\n Cordialmente, \n\n\n ".$wsistemas.
									          "\n\n\nEste mensaje ha sido enviado de manera Automatica por la Aplicacion de Gestion de Requerimientos. \n";
									$valido=enviar_correo($wsgrcsc,$wmailto,$wasunto,$wmensaje);
								  }

								if (($wsgrest=="C") and $valido==1)
								  {
									$wsgrfre=$wfecha;	// Muevo la fecha para el cierre del requerimiento.
									$wsgrhre=$whora;	// Muevo la hora para el cierre del requerimiento.
									$wmailto=$wsgrmai;  // Llevo el email del usuario.

									$wasunto="Respuesta de Sistemas a su Requerimiento!!!";
									$wmensaje="\nCordial Saludo! \n\n\nSu requerimiento Nro. ".$wsgrcsc.", ha sido evaluado y analizado por la Dirección de Informática, que luego  ".
									          "de realizar los procedimientos necesarios lo ha resuelto.   Descripción de la solución: ".$wsegseg.". \n\n\n Cordialmente, \n\n\n ".$wsistemas.
									          "\n\n\nEste mensaje ha sido enviado de manera Automatica por la Aplicacion de Gestion de Requerimientos. \n";
									$valido=enviar_correo($wsgrcsc,$wmailto,$wasunto,$wmensaje);
								  }

								if (($wsgrest=="T") and (isset($winfseg)) and ($winfseg=="on") and $valido==1)
								  {
									$wmailto=$wsgrmai;  // Llevo el email del usuario.

									$wasunto="Seguimiento de Sistemas a su Requerimiento!!!";
									$wmensaje="\nCordial Saludo! \n\n\nSu requerimiento Nro. ".$wsgrcsc.", ha sido evaluado y analizado por el Dpto. de Sistemas, el cual ".
									          "le informa que este está en proceso de desarrollo.  Para su informacion y control se ha grabado un seguimiento. Por favor revise el detalle de esta respuesta en el programa de Seguimiento a Requerimientos. \n\n\nCordialmente, \n\n\n ".$wsistemas.
									          "\n\n\nEste mensaje ha sido enviado de manera Automatica por la Aplicacion de Gestion de Requerimientos. \n";
									$valido=enviar_correo($wsgrcsc,$wmailto,$wasunto,$wmensaje);
								  }

								if ($valido==1)         // Se graba el seguimiento y se actualiza la informacion en el encabezado del requerimiento
								  {
									if (!isset($wsegnid))
										$wsegnid=0;

									$wsegnid=$wsegnid + 1;     // Incremento el consecutivo del Seguimiento...

									$q= " INSERT INTO ".$wbasedato."_000125 (medico,fecha_data,hora_data,segcsc,segnid,segdes,segres,seguridad) ".
										" VALUES ('".$wbasedato."','".$wfecha."','".$whora."',".$wsgrcsc.",".$wsegnid.",'".$wsegseg."','".$wugsnom.
									    "','C-".$user."')";
									$res = mysql_query($q,$conex) or die ("ERROR AL INSERTAR EL DETALLE DE SEGUIMIENTOS: ".mysql_errno()." - ".mysql_error());

									$q=  " UPDATE ".$wbasedato."_000122 SET sgresc='".$wsgresc."',sgrfes='".$wsgrfes."',sgrhes='".$wsgrhes."',sgrres='".$wsegree."',sgrfre='".$wsgrfre."',sgrhre='".$wsgrhre."',sgrest='".$wsgrest."' where sgrcsc= '".$wsgrcsc."'";
									$res1 = mysql_query($q,$conex) or die("ERROR AL ACTUALIZAR EL ENCABEZADO DE REQUERIMIENTOS: ".mysql_errno().":".mysql_error());

									echo "<tr>";
									echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
									echo "<tr><td align='center'><font size=2 color='#000080' face='arial'><b>Su Seguimiento fue Grabado Exitosamente.</td><tr>";
									echo "</table>";
									
									$wmailto=$wsgrmai;  // Llevo el email del usuario.

									$wasunto="Seguimiento de Sistemas a su Requerimiento!!!";
									$wmensaje="\nCordial Saludo! \n\n\nSu requerimiento Nro. ".$wsgrcsc.", ha sido evaluado y analizado por el Dpto. de Sistemas, el cual ".
									          "le informa que este está en proceso de desarrollo.  Para su informacion y control se ha grabado un seguimiento. Por favor revise el detalle de esta respuesta en el programa de Seguimiento a Requerimientos. \n\n\nCordialmente, \n\n\n ".$wsistemas.
									          "\n\n\nEste mensaje ha sido enviado de manera Automatica por la Aplicacion de Gestion de Requerimientos. \n";
									$valido=enviar_correo($wsgrcsc,$wmailto,$wasunto,$wmensaje);
								  }
								 else
								    {
								     echo "<tr>";
									 echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
									 echo "<tr><td align='center'><font size=2 color='#000080' face='arial'><b>Su Requerimiento NO fue Grabado, Favor Verifique Su Cuenta De Correo Que Sea Valida!!!</td><tr>";
									 echo "</table>";
								    }
								unset ($wgrabar);
								unset ($wsgrcsc);
								$wgrabar="";
								$wsgrcsc="";
								echo "<input type='HIDDEN' name= 'wsgrcsc' value='".$wsgrcsc."'>";
								echo "<input type='HIDDEN' name= 'wgrabar' value='".$wgrabar."'>";
							   }
						   }
					  }
					 else
					   {
						if (($wsgrest == "C") or ($wsgrest == "R"))
						   {
							echo "<tr><td colspan=10>&nbsp;</td></tr>";
							echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
							echo "<tr><td align='center'><font size=2 color='#000080' face='arial'><b>El Requerimiento Ya Se Encuentra Cerrado!!!</td><tr>";
							echo "</table>";

							echo "<table border=0 align=center width='65%'>";
							echo "<tr><td colspan=10>&nbsp;</td></tr>";
							echo "</table>";
						   }
						 else
						   {
							echo "<tr><td colspan=10>&nbsp;</td></tr>";
							echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
							echo "<tr><td align='center'><font size=2 color='#000080' face='arial'><b>Su Usuario No esta Autorizado Para Grabar Seguimientos!!!  Por Favor Comuniquese con Sistemas...</td><tr>";
							echo "</table>";

							echo "<table border=0 align=center width='65%'>";
							echo "<tr><td colspan=10>&nbsp;</td></tr>";
							echo "</table>";
						   }
					   }
			  }
			 else
			   {
				echo "<table align='center' border=0 bordercolor=#000080 width=500 style='border:solid;'>";
				echo "<tr><td align='center'><font size=2 color='#000080' face='arial'><b>No se encontro informacion para el Id. del Requerimiento</td><tr>";
				echo "</table>";
			   }
		}

	////}


}

?>

