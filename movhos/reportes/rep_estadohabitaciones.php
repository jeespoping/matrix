<html>
<head>
<title>Consulta Estado de Habitaciones</title>
<link href="/matrix/root/caro.css" rel="stylesheet" type="text/css" />
</head>
<font face='arial'>
<BODY TEXT="#000066">

<script type="text/javascript">
	function enter()
	{
		document.forms.rep_estadohabitaciones.submit();
	}
</script>


<?php
include_once("conex.php");

/**
* CONSULTA ESTADO DE HABITACIONES	                                                   *
*/
// ===========================================================================================================================================
// PROGRAMA				      :Reporte para saber los articulos aplicados por paciente.                                                      |
// AUTOR				      :Juan C Hernandez m..                                                                                          |
// FECHA CREACION			  :Noviembre 1 DE 2007.                                                                                          |
// FECHA ULTIMA ACTUALIZACION :03 de Octubre de 2007.                                                                                        |
// DESCRIPCION			      :Este reporte sirve para ver por centro de costos-habitacio y paciente que saldo tiene pendiente de aplicar.   |
//                                                                                                                                           |
// TABLAS UTILIZADAS :                                                                                                                       |
// root_000050       : Tabla de Empresas para escojer empresa y esta traer un campo para saber que centros de costos escojer.                |
// costosyp_000005   : Tabla de Centros de costos de Clinica las Americas, Laboratorio de Patologia, y Laboratorio Medico.                   |
// clisur_000003     : Tabla de Centros de costos de clinica del sur.                                                                        |
// farstore_000003   : Tabla de Centros de costos de farmastore.                                                                             |
// root_000041       : Tabla de Tipos de requerimientos.                                                                                     |
// root_000042       : Tabla de Responsables por centro de costos.                                                                           |
// usuarios          : Tabla de Usuarios con su codigo y descripcion.                                                                        |
// root_000040       : Tabla de Requerimientos.                                                                                              |
// root_000043       : Tabla de Clases.                                                                                                      |
// root_000049       : Tabla de Estados.                                                                                                     |
// ==========================================================================================================================================
$wactualiz = "Ver. 2007-10-26";

session_start();
if (!isset($_SESSION['user']))
{
    echo "error";
} 
else
{
    $empresa = 'root';

    

    


    echo '<div id="header">';
    echo '<div id="logo">';
    echo '<h1><a href="rep_estadohabitaciones.php">CONSULTA ESTADO DE HABITACIONES</a></h1>';
    echo '<h2>CLINICA LAS AMERICAS <b>' . $wactualiz . '</h2>';
    echo '</div>';
    echo '</div></br></br></br></br></br>';

        
    $query = " SELECT Detapl,Detval"
     		."   FROM " . $empresa . "_000051"
     		."  WHERE Detemp = '" . $wemp . "'";
	$err = mysql_query($query, $conex);
    $num = mysql_num_rows($err);

    $empre1 = "";
    $empre2 = "";

    for ($i = 1;$i <= $num;$i++)
    {
        $row = mysql_fetch_array($err);

        IF ($row[0] == 'cenmez')
        {
            $wcenmez = $row[1];
        } 
        else
        {
            if ($row[0] == 'movhos')
            {
                $wbasedato = $row[1];
            } 
           else
              if ($row[0] == 'tabcco')
	            {
	              $wtabcco = $row[1];
	            } 
        } 
    } 

    echo '<div id="page" align="center">';
    echo '<div id="feature" class="box-orange" align="center">';
        
    $q = " SELECT habcod, habhis, habing, habali, habdis, habest, habfal, habhal, habpro, habcco, cconom "
       . "   FROM ".$wbasedato."_000020, ".$wtabcco
       ."   WHERE habcco = ccocod "
       . "  ORDER BY 11, 1 ";
       //. "  WHERE habcod = '".$whab."'";
    $res = mysql_query($q, $conex) or die("ERROR EN QUERY");
    $wnum = mysql_num_rows($res); 
    
    
    echo '<div class="content">';
    echo '<table align=center>';

    
    if ($wnum > 0)
       {
        echo "<br>";
        
        $i=1;
        $row = mysql_fetch_array($res);
        while ($i <= $wnum)
           {
	        
	        $wcco = $row[9];
	        $wnomcco=$row[10];
	        
	        echo "<tr><th colspan=5 align=left><font size=4><b>Servicio: ".$wcco." - ".$wnomcco."</b></font></th></tr>";
	        
	        echo "<th><b>Habitación</b></th>";
	        echo "<th><b>Historia</b></th>";
	        echo "<th><b>Paciente</b></th>";
	        echo "<th><b>Estado</b></th>";
	        echo "<th><b>Observaciones</b></th>";
	        
	        while ($i<=$wnum and $wcco==$row[9])
	           {
		        $whab=$row[0];
		        $whis=$row[1];
		        $wing=$row[2];
		        $wali=$row[3];
		        $wdis=$row[4];
		        $west=$row[5];
		        $wfal=$row[6];
		        $whal=$row[7];
		        $wpro=$row[8];
		        $wcco=$row[9];
		        
		        if ($i % 2 == 0)
				   $wcf = "FFFFFF";
				  else
				     $wcf = "99CCFF";
		        
				if (isset($whis) and $whis!="" and trim($whis)!="NO APLICA")
	               {     
		            $q = " SELECT pacno1, pacno2, pacap1, pacap2, ubialp, ubiald, ubiptr, ubihan, ubihac "
		                ."   FROM root_000037, root_000036, ".$wbasedato."_000018 "
		                ."  WHERE oriori = '".$wemp."'"
		                ."    AND orihis = '".$whis."'"
		                ."    AND oriing = '".$wing."'"
		                ."    AND oriced = pacced "
		                ."    AND orihis = ubihis "
		                ."    AND oriing = ubiing ";
		            $res1 = mysql_query($q, $conex) or die("ERROR EN QUERY");
		            $wnum1 = mysql_num_rows($res1);
		            $row1 = mysql_fetch_array($res1);
		            
		            $wpac=$row1[0]." ".$row1[1]." ".$row1[2]." ".$row1[3];
		            $walp=$row1[4];
		            $wald=$row1[5];
		            $wptr=$row1[6];
		            $whan=$row1[7];
		            $whac=$row1[8];    
		           }
		          else
		             {
			          $wpac="";
			          $walp="";
			          $wald="";
			          $wptr="";
			          $whan="";
			          $whac=""; 
			          $wnum1=0;     
			         }      
		           
	            echo "<tr>";
		        echo "<td bgcolor=".$wcf."><font color=003300><b>".$whab."</b></font></td>";
		        echo "<td bgcolor=".$wcf."><font color=003300>".$whis." - ".$wing."</font></td>";
		        echo "<td bgcolor=".$wcf."><font color=003300>".$wpac."</font></td>";
		        
		        if ($wnum1 > 0)                                     //Tiene historia asiganada en la habitacion
		           echo "<td bgcolor=".$wcf." align=center><font color=003300><b>Ocupada</b></font></td>";
		          else
		             echo "<td bgcolor=".$wcf." align=center><font color=003300><b>Desocupada</b></font></td>"; 
		           
		        if ($wptr=="on")
		           $wrecibo= "Pendiente de recibir ";    
		             
		        if ($wali=="on" and $west=="on")                    //Esta para alistar y activa
		           echo "<td bgcolor=".$wcf."><font color=003300>En Central de Habitaciones desde: (".$row[6].") (".$row[7].")</font></td>";
		          else
		             {
		              if ($wdis=="on" and $wpro=="off" and $west=="on")   //Esta disponible, no asignada y activa
		                 echo "<td bgcolor=".$wcf."><font color=003300>Disponible en Admisiones</td>";
		                else 
		                   {
			                if ($wdis=="on" and $wpro=="on" and $west=="on")    //Esta disponible, Asignada y activa
		                       echo "<td bgcolor=".$wcf."><font color=003300>En proceso de ocupación, asignada por Admisiones</font></td>";   
			                  else 
			                     {
				                  if ($walp=="on" and $wdis!="on")                    //Esta en proceso de alta
							         {
								      $q= " SELECT cuefac, cuegen, cuepag, cuecok "
								          ."  FROM ".$wbasedato."_000022 "
								          ." WHERE cuehis = '".$whis."'"
						                  ."   AND cueing = '".$wing."'";
						              $res2 = mysql_query($q, $conex) or die("ERROR EN QUERY");
						              $wnum2 = mysql_num_rows($res2); 
								           
								      if ($wnum2 > 0)         //Si hay registros es porque ya el facturador hizo el chequeo para facturar
								         {
									      $row2 = mysql_fetch_array($res2);
									      $wfac=$row2[0];
									      $wgen=$row2[1];
									      $wpag=$row2[2];
									      $wcok=$row2[3];
									  
									      if ($wcok == "on")                        //Puede facturar
									         {
									          if ($wgen=="on")                      //Genero factura
									             {
									              if ($wpag=="on")                  //Ya se hizo el pago
								                     {
									                   if ($wptr=="on")
									                      echo "<td bgcolor=".$wcf."><font color=003300>".$wrecibo." Con alta administrativa, pendiente de alta definitiva en el servicio</font></td>"; 
									                     else   
									                        echo "<td bgcolor=".$wcf."><font color=003300>Con alta administrativa, pendiente de alta definitiva en el servicio</font></td>"; 
									                 }
								                    else
								                       { 
									                    if ($wptr=="on")
									                       echo "<td bgcolor=".$wcf."><font color=003300>".$wrecibo." En proceso de Alta, pendiente de que el responsable cancele en caja</font></td>"; 
									                      else 
									                         echo "<td bgcolor=".$wcf."><font color=003300>En proceso de Alta, pendiente de que el responsable cancele en caja</font></td>"; 
									                   }
							                     }      
								                else  
								                   if ($wptr=="on")
								                      {                            //No ha generado factura
								                       echo "<td bgcolor=".$wcf."><font color=003300>".$wrecibo." En Proceso de Alta, pendiente que el facturador genere la factura</font></td>"; 
								                      } 
								                     else
								                        echo "<td bgcolor=".$wcf."><font color=003300>En Proceso de Alta, pendiente que el facturador genere la factura</font></td>";  
							                 }
							                else                                    //No se puede facturar todavia
							                   { 
								                if ($wptr=="on")   
								                   echo "<td bgcolor=".$wcf."><font color=003300>".$wrecibo." En proceso de Alta, pendiente de la devolucion o que se corrijan documentos en el integrador</font></td>"; 
								                  else
								                     echo "<td bgcolor=".$wcf."><font color=003300>En proceso de Alta, pendiente de la devolucion o que se corrijan documentos en el integrador</font></td>";  
								               } 
								         }
							            else   
							               if ($wptr=="on")                                     
							                  echo "<td bgcolor=".$wcf."><font color=003300>".$wrecibo." En Proceso de Alta pero falta la devolucion o que el facturador chequee si puede generar factura</font></td>";   
							                 else
							                    echo "<td bgcolor=".$wcf."><font color=003300>En Proceso de Alta pero falta la devolucion o que el facturador chequee si puede generar factura</font></td>";    
							         }
							        else       
							           if ($wptr=="on")
							              echo "<td bgcolor=".$wcf."><font color=003300>".$wrecibo." </font></td>";
							             else
		                                   echo "<td bgcolor=".$wcf.">&nbsp</td>";          
				                 }
			               } 
	                 }      
		          
		        if ($west=="off")                                   //La habitacion esta inactiva a fuera de servicio
		           echo "<td bgcolor=".$wcf."><font color=003300>Fuera de servicio desde: (".$wfal.") (".$whal.")</font></td>";    
		           
		        echo "</tr>"; 
		        
		        $row = mysql_fetch_array($res);
		        $i++;
	           }   
           } 
       }
       
       echo "<tr></tr>";
       echo "<tr><td align=center colspan=5><A href='rep_estadohabitaciones.php?wemp=".$wemp."' id='searchsubmit'> Retornar</A></td></tr>";
	   echo "</table>"; // cierra la tabla o cuadricula de la impresión
} 
?>