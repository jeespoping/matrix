<html>
<head>
<title>Prerequisitos</title>
<script type="text/javascript"></script>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body class='fila2'>
<script type="text/javascript">
    function enter()
	{
	   document.forms.prerequisitos.submit();
	}
	
	function cerrarVentana()
	 {
      window.close()		  
     } 
     
    
	function iniciar(k)
	{
		if (k > 1)
		{ 
			if(  document.getElementById("wopc_"+k.toString()) != null  ){
				document.getElementById("wopc_"+k.toString()).checked = false;
			}		   
		}
	} 
   
</script>

<!-- Programa en PHP -->
<?php
include_once("conex.php");





include_once("root/magenta.php");
include_once("root/comun.php");

$wactualiz = "(Junio 3 de 2010)";


//=================================
//Declaracion de variables globales
//=================================
global $wusuario;
global $wbasedato;
global $wfecha;
global $whora;


global $wok;
global $wformulario;
global $wnompro;

//=================================

$wbasedato=consultarAliasPorAplicacion($conex, $wemp_pmla, "hce");
$wfecha=date("Y-m-d");   
$whora = (string)date("H:i:s");

$pos = strpos($user,"-");
$wusuario = substr($user,$pos+1,strlen($user)); 



//=================================================================================================================================
//***************************************** D E F I N I C I O N   D E   F U N C I O N E S *****************************************
//=================================================================================================================================
  
//======================================================================================================================================
function grabar()
  {
   global $conex;	  
   global $wbasedato;
   global $wusuario;
   global $wfecha;
   global $whora;
   global $wformulario;
   
   global $wok;
   
   global $matriz;
   
   $k = sizeof($matriz);
   
   //Definir variables globales con las opciones del arbol
   for ($i=1; $i<=($k); $i++)
      {
       for ($j=1; $j<=4; $j++)
	      {
		   if ($matriz[$i][$j][3]!='on')
	          {
	           $wop='wopc_'.$matriz[$i][$j][1];
		        
		       global $$wop;
		      } 
		  }
	   }
	   
   //======================================================================================================================================= 	  
   //=======================================================================================================================================    
   //***** A R B O L *****  
   //=======================================================================================================================================
   //=======================================================================================================================================  
   $q = " SELECT Precod, REPLACE(Preurl,'F=','') "
	   ."   FROM ".$wbasedato."_000009 "
	   ."  WHERE Preest = 'on' "  
	   ."  ORDER BY 1 ";

   $res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
   $num = mysql_num_rows($res);

   $k=($num/4);

   //Borro la OPCIONES del ARBOL que tenia el ROL 
   $q = " DELETE FROM ".$wbasedato."_000004 "
       ."  WHERE prepro = '".trim($wformulario)."'";
   $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
   
   //=========================================================== 
   //GRABO LAS OPCIONES DEL ARBOL DEL ROL
   //=========================================================== 
   $chain_insert= "";
   for ($j=1; $j<=4; $j++)
      {
	   for ($i=1; $i<=$k; $i++)
	      {
		   $row = mysql_fetch_array($res);   
		      
		   $wvar_opc="wopc_".$row[0];
		   if (isset($$wvar_opc) and $$wvar_opc == "on")
		      {
				if( strlen( $row[1] ) < 10 )
					$chain_insert.= $row[1].",";
			   //Inserto las VISTAS ASOCIADAS al formulario seleccionado  Vaspro  Vaspas  Vasnom  Vasest 
			  // $q= " INSERT INTO ".$wbasedato."_000005 (   Medico       ,   Fecha_data ,   Hora_data,   vaspro               ,   vaspas     ,   vasnom     , vasest,      Seguridad   ) "
			   //   ."                            VALUES ('".$wbasedato."','".$wfecha."' ,'".$whora."','".trim($wformulario)."','".$row[0]."' ,'".$row[1]."' , 'on'  , 'C-".$wusuario."') ";
			  // $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());   
			  }
		   }
	  }
	if( $chain_insert != '' ){
		$chain_insert = substr($chain_insert, 0, -1); //quitar la ultima coma
		 $q= " INSERT INTO ".$wbasedato."_000004 (   Medico       ,   Fecha_data ,   Hora_data,         prepro               ,   precpr     ,   preres     , preobs,   preest,      Seguridad   ) "
			      ."                        VALUES ('".$wbasedato."','".$wfecha."' ,'".$whora."','".trim($wformulario)."','".$chain_insert."' ,'off' ,       '',       'on'  , 'C-".$wusuario."') ";
			 $res1 = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());   
			
	}
   //======================================================================================================================================= 	  
   // TERMINA GRABACION DEL ARBOL
   //=======================================================================================================================================   
  }	  

 
//========================================================================================================================================
//Function para imprimir el arbol y a su vez definir las variables INPUT del arbol como globales.
//========================================================================================================================================
function mostrar_arbol()
  {
   global $matriz;
   global $wini;
   
   $k = sizeof($matriz);
   
   //Definir variables globales con las opciones del arbol
   for ($i=1; $i<=($k); $i++)
      {
       for ($j=1; $j<=4; $j++)
	      {
		   if ($matriz[$i][$j][3]!='on')
	          {
	           $wop='wopc_'.$matriz[$i][$j][1];
		       global $$wop;
		      } 
		  }
	   }
	  
   //=================================================================================================================
   //**** O P C I O N E S   D E L   A R B O L   D E   P R E SE N T A C I O N ****
   //=================================================================================================================      
   echo "<br>";
   echo "<center><table style='border: 1px solid blue'>";
   echo "<tr class=fila1><td align=center colspan=8><b><font size=5>ARBOL DE FORMULARIOS HCE</font></b></td></tr>";

   echo "<tr class=encabezadoTabla>";
   echo "<th>Sel.</th>";
   echo "<th>Opci�n</th>";
   echo "<th >Sel.</th>";
   echo "<th>Opci�n</th>";
   echo "<th>Sel.</th>";
   echo "<th>Opci�n</th>";
   echo "<th>Sel.</th>";
   echo "<th>Opci�n</th>";
   echo "</tr>";

   if ($wini=="on")
      {
	   $wcolor="";
	   $wini="off";
      } 
     else 
        $wcolor="FFFF99";
        
   
   for ($i=1; $i<=($k); $i++)
      {
       echo "<tr class=fila1>";   
	   for ($j=1; $j<=4; $j++)
	      {
		   if ($matriz[$i][$j][3]=='on')  //Si es un nodo
	         {
	          echo "<td colspan=2 bgcolor='dddddd'><b>".$matriz[$i][$j][2]."</b></td>";
	         }   
	        else 
	           {
		        $wvar = "wopc_".$matriz[$i][$j][1]; 
		        
		        if (isset($$wvar) and $$wvar!="off")        //Si esta seleccionada la opcion
		           {
			        echo "<td bgcolor='".$wcolor."'><input type='checkbox' name='wopc_".$matriz[$i][$j][1]."' id='wopc_".$matriz[$i][$j][1]."' CHECKED></td>";
			        echo "<td>".$matriz[$i][$j][2]."</td>";   
		           }
		          else
		             {       
			          echo "<td><input type='checkbox' name='wopc_".$matriz[$i][$j][1]."' id='wopc_".$matriz[$i][$j][1]."'></td>";
				      echo "<td>".$matriz[$i][$j][2]."</td>"; 
	                 }
		       }
		  }
	    echo "</tr>";       
	   }
	echo "</table>";      	  	  
  }	    
  

function vaciar_matriz()
  {
   //Vaceo la matriz cuando se presiona el boton "Iniciar", es decir coloco todas las opciones del arbol en 'off' 
   //esto porque despues de esta funci�n sigue la funci�n 'imprimir o mostrar el arbol'.	  
   global $matriz;
   
   	  
   $k = sizeof($matriz);	  
	
   for ($j=1; $j<=4; $j++)
	   {
		for ($i=1; $i<=$k; $i++)
	       {
		    $matriz[$i][$j][1]="off";
		   }
	   }   
  }	      
  
   
//======================================================================================================================================= 
function llenar_matriz_con_arbol()
  {
	global $conex;	  
    global $wbasedato; 
    global $matriz;
    global $k;
    
      
	$q = " SELECT Precod, Predes, Prenod "
	    ."   FROM ".$wbasedato."_000009 "
	    ."  WHERE Preest = 'on' "  
	    ."  ORDER BY 1 ";
	$res = mysql_query($q,$conex) or die (mysql_errno()." - en el query: ".$q." - ".mysql_error());
	$num = mysql_num_rows($res);

	$k=($num/4);

	//======================================================================================================================================= 
	//Lleno una matriz tal como se debe de mostrar
	///======================================================================================================================================= 
	for ($j=1; $j<=4; $j++)
	   {
		for ($i=1; $i<=$k; $i++)
	       {
		    $row = mysql_fetch_array($res);
		    
		    $matriz[$i][$j][1]=$row[0];
		    $matriz[$i][$j][2]=$row[1];
		    $matriz[$i][$j][3]=$row[2];
		   }
	   }
   }
//======================================================================================================================================= 
  
  
//=======================================================================================================================================    
function consultar_detalle_arbol()
  {
   global $conex;	  
   global $wbasedato;
   global $wusuario;
   global $wfecha;
   global $whora;
   global $matriz;
   global $wformulario;
   
   llenar_matriz_con_arbol();
   
   $k = sizeof($matriz);
    
   //Definir variables globales con las opciones del arbol
   for ($i=1; $i<=($k); $i++)
      {
       for ($j=1; $j<=4; $j++)
	      {
	       if ($matriz[$i][$j][3]!='on')
	          {
	           $wop='wopc_'.$matriz[$i][$j][1];
		       global $$wop;
		      } 
		  }
	   }
	   
   //Consulto los formularios prerequisitos del actual
   $q = " SELECT  Precod  "
       ."   FROM ".$wbasedato."_000004 A, ".$wbasedato."_000009 B"
       ."  WHERE Prepro = '".trim($wformulario)."'"
       ."    AND A.Preest = 'on' "
       ."    AND instr(precpr, REPLACE(Preurl,'F=','')) ";
   $res = mysql_query($q,$conex) or die ("Error: ".mysql_errno()." - en el query: ".$q." - ".mysql_error());
   $num = mysql_num_rows($res);
   if ($num > 0)
      {
	   for ($i=1;$i<=$num;$i++)
	      {
		   $row = mysql_fetch_array($res);   
		      
		   $wvar="wopc_".$row[0];
	       $$wvar="on";
	      }     
	  }

  }
//=======================================================================================================================================  
  

//=======================================================================================================================================
function consultar()
  {
   global $conex;	  
   global $wbasedato;
   global $wusuario;
   global $wfecha;
   global $whora;
   
   consultar_detalle_arbol(); 
  } 
   
   
function iniciar($wtipo)
  {
   global $conex;	  
   global $wbasedato; 
   global $wusuario;
   global $wfecha;
   global $whora;
   
   global $wcodusu;
   global $wnomusu;
   global $wclave;
   global $wrol;
   global $wfecven;
   global $wresidente;
   global $wactivo;
   
   global $matriz;
   global $matriz_pgm;
   
   
   switch ($wtipo)
	  {  
	   case "Arbol":
	      $k = sizeof($matriz);
	      for ($i=1; $i<=($k); $i++)
		      {
		       for ($j=1; $j<=4; $j++)
			      {
				    echo "<script language='Javascript'>";   
				       echo "iniciar("."\"".$matriz[$i][$j][1]."\"".")";
				    echo "</script>";     
			      }
		      }
		  break;    
      }
  }  
  

//=================================================================================================================================
//***************************** T E R M I N A   L A   D E F I N I C I O N   D E   F U N C I O N E S *******************************
//=================================================================================================================================


echo "<form name='prerequisitos' action='Prerequisitos.php' method='post'>";

//*******************************************************************************************************************************
//*********   A C A   C O M I E N Z A   E L   B L O Q U E  **** <<< P R I C I P A L >>> ****  D E L   P R O G R A M A   *********
//*******************************************************************************************************************************
//=================================================================================================================
//E N C A B E Z A D O   V I S T A S  A S O C I A D A S
//=================================================================================================================      
$wtitulo="VISTAS ASOCIADAS";

//encabezado($wtitulo, $wactualiz, 'clinica');

global $wini;

$wini="off";

//Se evalua el boton presionado
if (isset($Actualizar) or isset($Consultar) or isset($Iniciar))
   {
	 if (isset($Actualizar) and $wformulario!="")
	   {
		//validar_campos();
		
		if (isset($wusuario) and $wusuario != "")
		   {
			llenar_matriz_con_arbol();
			Grabar();
			
			mostrar_arbol();
			consultar();
						
			if ($wok)
			   {
				?>	    
			      <script> alert ("El Registro fue Actualizado"); </script>
			    <?php
		       }
	       }
	      else
	         {
		      ?>	    
			      <script> alert ("Debe recargar la pagina o volver a ingresar, porque no se detecto actividad en los �ltimos 5 minutos"); </script>
			  <?php   
		     }         
       }	
          
	 if (isset($Consultar))
	   { 
		 consultar();
		 mostrar_arbol();
	   }    
            
     if (isset($Iniciar))
	   { 
		 $wini="on";  
		 vaciar_matriz();
		 llenar_matriz_con_arbol();
		 mostrar_arbol();
		 iniciar("Arbol");
	   }
	} //fin del if (Grabar or Modificar or Consultar or Borrar)
   else
      { 
	   vaciar_matriz();
	   llenar_matriz_con_arbol();
	   
	   iniciar("Arbol");
	   consultar();
	   mostrar_arbol();
	  } 
   	   
echo "<input type='HIDDEN' name=wformulario  value='".$wformulario."'>";       
echo "<input type='HIDDEN' name='wemp_pmla'  value='".$wemp_pmla."'>";       

echo "</table>";  

echo "<br><br>";
echo "<center><table>";
echo "<input type='submit' name='Consultar' value='Consultar'>";
echo "&nbsp;&nbsp;|&nbsp;&nbsp";
echo "<input type='submit' name='Actualizar' value='Actualizar'>";
echo "&nbsp&nbsp&nbsp;|&nbsp"; 
echo "<input type='submit' name='Iniciar' value='Iniciar'>";
echo "&nbsp&nbsp;|&nbsp"; 
echo "<input type='submit' name='Salir' value='Salir' onclick='cerrarVentana()'>";
echo "</table>";
//=================================================================================================================
?>
</form>
</body>
</html>
