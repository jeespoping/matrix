<html>
<head>
  <title>MATRIX Impresion de Votos Para Asamblea de Accionistas / Copropietarios 2009-12-09</title>
</head>
<script type='text/javascript' src='../../../include/root/jquery-1.3.2.js'></script>
<script>
    window.onload = function(){
        //paginar( document.getElementById( 'votosfondos' ), document.getElementById( 'divfondos' ) );
    }

        alturaPagina = 24/0.026458333;
	paginas = 0;
	restoPaginas = 0;
 	 
 	function findPosY(obj)
 	{
 		var curtop = 0;
 		if(obj.offsetParent)
 	    	while(1)
 	        {
 	          curtop += obj.offsetTop;
 	          if(!obj.offsetParent)
 	            break;
 	          obj = obj.offsetParent;
 	        }
 	    else if(obj.y)
 	        curtop += obj.y;
 	    return curtop;
 	 }
 	 
 	function agregarFila( tabla ){
		
		try{
	
			var fila = tabla.insertRow( tabla.rows.length );
	
			for( var  i = 0; i < tabla.rows[0].cells.length ; i++){
				fila.appendChild( fila.insertCell(i) );
			}
			
			return fila;
		
		}
		catch(e){
			alert( "Error en agregar Fila: "+e );
		}

	}

 	function paginar( campo, principal ){

 	 	if( campo ){
 	 	 	if( campo.tagName ){

 	 	 		var cabecera = document.getElementById('hiPaciente').value;

 	 	 	 	switch( campo.tagName ){
 	 	 	 	
 	 	 	 		case 'TABLE':
 	 	 	 			var aux = document.createElement( "div" );
 	 	 	 			aux.innerHTML = "<table align='center' style='border: 1px solid black; border-collapse: collapse; font-size: 8pt; width: 100%;'></table>";

 	 	 	 			tabla = campo.cloneNode(true);

 	 	 	 			var sumaAltura = 0;

 	 	 	 			for( var i = 0; i < campo.rows.length; i++ ){

							posFila = findPosY( campo.rows[i] );

							sumaAltura = sumaAltura + campo.rows[i].clientHeight;
							
							posFila = posFila+campo.rows[i].clientHeight;


							if( sumaAltura > alturaPagina ){

								restoPaginas = restoPaginas+(alturaPagina+paginas*alturaPagina-posFila+campo.rows[i].clientHeight );
								paginas++;

								sumaAltura = campo.rows[i].clientHeight;

								var aux2 = document.createElement( "div" );
								aux2.innerHTML = "<a>Página: "+paginas+"<br><br></a>";
								aux2.innerHTML = "<a><table width='100%' style='font-size:10pt'><tr><td>Página: "+parseInt( paginas )+"</td><td align='center'>"+cabecera+"</td></tr></table><br><br></a>"
								principal.appendChild( aux2.firstChild );
								
								principal.appendChild( aux.firstChild );

								aux.innerHTML = "<div class='saltopagina'></div>";
								principal.appendChild( aux.firstChild );

								aux.innerHTML = "<table align='center' style='border: 1px solid black; border-collapse: collapse; font-size: 8pt; width: 100%;'></table>";
							}

							var fila = aux.firstChild.insertRow( aux.firstChild.rows.length );
		 	 	 	 		
	 	 	 				for( var  j = 0; j < tabla.rows[i].cells.length ; j++){
	 	 	 					fila.appendChild( tabla.rows[ i ].cells[j] );
	 	 	 				}
 	 	 	 			}

 	 	 	 			paginas++;
	 	 	 	 		var aux2 = document.createElement( "div" );
						aux2.innerHTML = "<a>Página: "+paginas+"<br><br></a>";
						aux2.innerHTML = "<a><table width='100%' style='font-size:10pt'><tr><td>Página: "+parseInt( paginas )+"</td><td align='center'>"+cabecera+"</td></tr></table><br><br></a>"
						principal.appendChild( aux2.firstChild );

 	 	 	 			campo.style.display = 'none';
 	 	 	 			principal.appendChild( aux.firstChild );
 	 	 	 			
 	 	 	 	 	break;

 	 	 	 	 	case 'DIV':{
 	 	 	 	 		inicial = findPosY( campo );

 	 	 	 	 		var aux = document.createElement( "div" );	
	 	 	 			aux.innerHTML = "<a>Página: "+parseInt( paginas+1 )+"<br><br></a>";
	 	 	 			aux.innerHTML = "<a><table width='100%' style='font-size:10pt'><tr><td>Página: "+parseInt( paginas+1 )+"</td><td align='center'>"+cabecera+"</td></tr></table><br><br></a>"

	 	 	 			campo.insertBefore( aux.firstChild, campo.childNodes[0] );

// 	 	 	 	 		if( campo.clientHeight > alturaPagina ){
 	 	 	 	 		if( campo.scrollHeight > alturaPagina ){

 	 	 	 	 			var Pagina2 = 0;
	 	 	 	 	 		sumaAltura = 0;

	 	 	 	 	 		var totalPaginaCampo = parseInt( campo.clientHeight/alturaPagina );
	 	 	 	 	 		var totalPaginaCampo = parseInt( campo.scrollHeight/alturaPagina );

	 	 	 	 	 		Pagina2 = parseInt( paginas+totalPaginaCampo );

	 	 	 	 	 		paginas = paginas + parseInt( totalPaginaCampo )+1;

	 	 	 	 	 		for( var j = 0; j < totalPaginaCampo; j++ ){

		 	 	 	 	 	 	for( var i = campo.childNodes.length-1; i >= 0; i-- ){
		
		 	 	 	 	 	 		posFila = findPosY( campo.childNodes[i] );
									
		 	 	 	 	 	 		if( posFila-inicial < alturaPagina+alturaPagina*(totalPaginaCampo-j-1) && posFila > 0 ){

			 	 	 	 	 	 		var aux = document.createElement( "div" );
			 	 	 	 	 	 		aux.innerHTML = "<div class='saltopagina'></div>";
	
			 	 	 	 	 	 		campoReferencia = campo.childNodes[i];

			 	 	 	 	 			if( campoReferencia ){
			 	 	 	 	 	 			campo.insertBefore( aux.firstChild, campoReferencia );
			 	 	 	 	 			}
			 	 	 	 	 			else{
				 	 	 	 	 			campo.appendChild( aux.firstChild );
			 	 	 	 	 			}

			 	 	 	 	 	 		var aux = document.createElement( "div" );	
		 	 	 	 	 	 			aux.innerHTML = "<a><table width='100%' style='font-size:10pt'><tr><td>Página: "+parseInt( Pagina2+1 )+"</td><td align='center'>"+cabecera+"</td></tr></table><br><br></a>";

		 	 	 	 	 	 			if( campoReferencia ){
		 	 	 	 	 	 				campo.insertBefore( aux.firstChild, campoReferencia );
		 	 	 	 	 	 			}
		 	 	 	 	 	 			else{
		 	 	 	 	 	 				campo.appendChild( aux.firstChild );
		 	 	 	 	 	 			}

		 	 	 	 	 	 			Pagina2--;

//		 	 	 	 	 	 			i--;
//		 	 	 	 	 	 			i--;
	
		 	 	 	 	 	 			break;
		 	 	 	 	 	 		}
		 	 	 	 	 	 	}
	 	 	 	 	 		}
 	 	 	 	 		}
 	 	 	 	 		else{
 	 	 	 	 	 		paginas++;
 	 	 	 	 		}
 	 	 	 	 	 	
 	 	 	 	 	} break;
 	 	 	 	}
 	 	 	}
 	 	}
 	}
 	/**
 	 * FIN DE PAGINAR
 	 */

</script>
<body BGCOLOR="">
<BODY TEXT="#000000">
<?php
include_once("conex.php");
@session_start();
       

if(!isset($_SESSION['user']))
	echo "error";
else
	{
		$key = substr($user,2,strlen($user));
                global $wsepara;
                global $wsepara1;
                global $wsepavaerti;
                global $resto;
		echo "<form action='votosFondos.php' method=post>";
		

		

                $k=0;
		echo "<input type='HIDDEN' name= 'empresa' value='".$empresa."'>";
		if(!isset($wemp) or !isset($wtip) or !isset($wpar) or !isset($ci) or !isset($cf) or !isset($wtiv) or !isset($wfec))
		{
			echo "<center><table border=0>";
			echo "<tr><td align=center colspan=2><b>IMPRESION DE VOTOS ASAMBLEAS DE LAS AMERICAS Ver. 2009-03-09</b></td></tr>";
			echo "<tr>";
			echo "<td bgcolor=#cccccc>Empresa</td>";			
			echo "<td bgcolor=#cccccc>";
			echo "<select name='wemp'>";
			$query = "select Empcod, Empdes from ".$empresa."_000004 order by Empcod";
			$err1 = mysql_query($query,$conex);
			$num1 = mysql_num_rows($err1);
			for ($i=0;$i<$num1;$i++)
			{	
				$row1 = mysql_fetch_array($err1);
				if(isset($wemp) and $wemp == $row1[0]."-".$row1[1])
					echo "<option selected>".$row1[0]."-".$row1[1]."</option>";
				else
					echo "<option>".$row1[0]."-".$row1[1]."</option>";
			}
			echo "</td></tr>";
			if(isset($wemp))
			{
				echo "<td bgcolor=#cccccc>Tipo de Voto</td>";			
				echo "<td bgcolor=#cccccc>";
				$query = "select Parcod, Pardes from ".$empresa."_000002 where Paremp = '".substr($wemp,0,2)."' order by Parcod";
				$err1 = mysql_query($query,$conex);
				$num1 = mysql_num_rows($err1);
				echo "<select name='wtiv'>";
				for ($i=0;$i<$num1;$i++)
				{	
					$row1 = mysql_fetch_array($err1);
					echo "<option>".$row1[0]."-".$row1[1]."</option>";
				}
				echo "</td></tr>";
				echo "<tr><td bgcolor=#cccccc align=center>Fecha de la Asamblea</td><td bgcolor=#cccccc align=center><input type='TEXT' name='wfec' size=10 maxlength=10></td></tr>";
			}
			
			echo "<tr><td bgcolor=#cccccc>TIPO DE ASAMBLEA</td>";			
			if(isset($wtip))
				if($wtip == "0")
					echo "<td bgcolor=#cccccc><input type='RADIO' name='wtip' value='0' checked> ORDINARIA <input type='RADIO' name='wtip' value='1'> EXTRAORDINARIA </td></tr>";
				else
					echo "<td bgcolor=#cccccc><input type='RADIO' name='wtip' value='0'> ORDINARIA <input type='RADIO' name='wtip' value='1' checked> EXTRAORDINARIA </td></tr>";
			else
				echo "<td bgcolor=#cccccc><input type='RADIO' name='wtip' value='0' checked> ORDINARIA <input type='RADIO' name='wtip' value='1'> EXTRAORDINARIA </td></tr>";
			echo "<tr><td bgcolor=#cccccc>TIPO DE PARTICIPACION</td>";	
			if(isset($wpar))
				if($wpar == "0")
					echo "<td bgcolor=#cccccc><input type='RADIO' name='wpar' value='0' checked> ACCIONES <input type='RADIO' name='wpar' value='1'> % COPROPIEDAD </td></tr>";
				else
					echo "<td bgcolor=#cccccc><input type='RADIO' name='wpar' value='0'> ACCIONES <input type='RADIO' name='wpar' value='1' checked> % COPROPIEDAD </td></tr>";
			else
				echo "<td bgcolor=#cccccc><input type='RADIO' name='wpar' value='0' checked> ACCIONES <input type='RADIO' name='wpar' value='1'> % COPROPIEDAD </td></tr>";
			if(isset($ci))
				echo "<tr><td bgcolor=#cccccc align=center>Codigo Inicial</td><td bgcolor=#cccccc align=center><input type='TEXT' name='ci' size=12 maxlength=12 value='".$ci."' ></td></tr>";
			else
				echo "<tr><td bgcolor=#cccccc align=center>Codigo Inicial</td><td bgcolor=#cccccc align=center><input type='TEXT' name='ci' size=12 maxlength=12></td></tr>";
			if(isset($cf))
				echo "<tr><td bgcolor=#cccccc align=center>Codigo Final</td><td bgcolor=#cccccc align=center><input type='TEXT' name='cf' size=12 maxlength=12 value='".$cf."'></td></tr>";
			else
				echo "<tr><td bgcolor=#cccccc align=center>Codigo Final</td><td bgcolor=#cccccc align=center><input type='TEXT' name='cf' size=12 maxlength=12></td></tr>";
			echo "<tr><td bgcolor=#cccccc>TIPO DE IMPRESION</td>";
			echo "<td bgcolor=#cccccc><input type='RADIO' name='wtipv' value='0' checked>NORMAL<input type='RADIO' name='wtipv' value='1'>DE SI/NO</td></tr>";
			echo "<tr><td bgcolor=#cccccc>ORDEN DE IMPRESION</td>";
			echo "<td bgcolor=#cccccc><input type='RADIO' name='word' value='0' checked>ALFABETICO<input type='RADIO' name='word' value='1'>DE INSCRIPCION</td></tr>";
			echo "<tr><td bgcolor=#cccccc align=center colspan=2><input type='submit' value='IR'></td></tr></table>";
		}
		else
		{
			switch ($wtip)
			{
				case "0":
					$wtit="ORDINARIA";
				break;
				case "1":
					$wtit="EXTRAORDINARIA";
				break;
			}
			switch ($wpar)
			{
				case "0":
					$wdec=0;
					$wtit2="ACCIONES";
				break;
				case "1":
					$wdec=4;
					$wtit2="% COPROPIEDAD";
				break;
			}
			$query = "select Pardes from ".$empresa."_000002 where Paremp = '".substr($wemp,0,2)."' and Parcod = '".substr($wtiv,0,strpos($wtiv,"-"))."' order by Parcod";
			$err1 = mysql_query($query,$conex);
			$row1 = mysql_fetch_array($err1);
			if($word == "0")
                        {
			       $query1 = "SELECT  Acccod, Accnom, Accvap, Pardes
                                            FROM ".$empresa."_000001, ".$empresa."_000002 
                                           WHERE  Accact='on' 
                                             AND  Accemp='".substr($wemp,0,2)."' 
                                             AND  Acccod BETWEEN  '".$ci."' 
                                             AND  '".$cf."'
                                             AND  Accemp = Paremp                                         
                                        ORDER BY  Accnom";                               
                               
                        }
			else
				{
			       $query1 = "SELECT  Acccod, Accnom, Accvap, Pardes 
                                            FROM ".$empresa."_000001 A, ".$empresa."_000002 B
                                           WHERE  Accact='on' 
                                             AND  Accemp='".substr($wemp,0,2)."'
                                             AND  Acccod BETWEEN  '".$ci."' 
                                             AND  '".$cf."'
                                             AND  Accemp = Paremp                                                                                   
                                        ORDER BY  A.id";                               
                        }                        
			                          
                        $res1 = mysql_query($query1, $conex) or die(mysql_error());                                           
                        $num1 = mysql_num_rows($res1);   
                        echo "<input type='hidden' id='hiPaciente'>";
                        echo "<div id=divfondos>";
                       // echo "<table align=center border =1 id=votosfondos>";

                        $columnes = 2; # Número de columnas (variable)

                        if (($num=mysql_num_rows($res1))==0) {
                        echo "<tr><td colspan=$columnes>No hay resultados en la BD.</td></tr> ";
                        echo "<tr><td colspan=$columnes><a href='votosFondos.php?empresa=".$empresa."'>Volver</td></tr> ";
                        } 
                           //echo "<div id='bcTarget'></div>";
                            
                        for ($i=1; $i <= $num; $i++) 
                        {  
                       $k++;
                       
                        if ($k%10==0)
                            {
                                echo "<div style=page-break-before: always>";
                            }
                      // echo $k;
                        $row = mysql_fetch_array($res1);                             
                        $resto = ($i % $columnes); # Número de celda del <tr> en que nos encontramos
                        if ($resto == 1)
                            {
                            
                            echo "<table align=center><tr>";                            
                            } # Si es la primera celda, abrimos <tr>
                           
                                                     
                            
                            if (($k%2)==0)
                            {
                                $wsepavaerti = 
                                '<table style=text-align: left; width: 14px; border=0 cellpadding=2 cellspacing=2>
                                <tbody>
                                    <tr>
                                    <td style=width: 4px;>|</td>
                                    </tr>
                                    <tr>
                                    <td style=width: 4px;>|</td>
                                    </tr>
                                    <tr>
                                    <td style=width: 4px;>|</td>
                                    </tr>
                                    <tr>
                                    <td style=width: 4px;>|</td>
                                    </tr>
                                    <tr>
                                    <td style=width: 4px;>|</td>
                                    </tr>
                                    <tr>
                                    <td style=width: 4px;>|</td>
                                    </tr>
                                    <tr>
                                    <td style=width: 4px;>|</td>
                                    </tr>
                                    <tr>
                                    <td style=width: 4px;>|</td>
                                    </tr>
                                    <tr>
                                    <td style=width: 4px;>|</td>
                                    </tr>
                                </tbody>
                                </table>'; 
                                
                                
                            }
                            else
                            {
                              $wsepavaerti = '';  
                            }
                            
                            echo "<td>
                                ".$wsepavaerti."
                                </td>
                                <td>
                                
                                    <hr>                                     
                                    <table style='align: center; width: 400px;' border='0'>
                                    <tbody>
                                    <tr>
                                    <td colspan='2' rowspan='1' style=' font-weight: bold; text-align: center;'>".substr($wemp,strpos($wemp,"-")+1)."</td>
                                    </tr>
                                    <tr>
                                    <td colspan='2' rowspan='1' style=' text-align: center;'>ASAMBLEA ".$wtit."</td>
                                    </tr>
                                    <tr>
                                    <td style='text-align: center; font-weight: bold; '>".$row['Pardes']."</td>                                   
                                    </tr>
                                    <tr>
                                    <td style='text-align: center;'>".$row['Accnom']."</td>                                   
                                    </tr>
                                    <tr>
                                    <td style='text-align: center;'>".$row['Acccod']."</td>
                                    
                                    </tr>
                                    
                                    <tr>
                                    <td style='text-align: center;>".$row1['Pardes']."</td>
                                   
                                    </tr>
                                    <tr>
                                    <td style='text-align: center; font-height: 20;'><big><big>[&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;]</big></big></td>
                                    
                                    </tr>
                                    <tr>
                                    <td style='text-align: center;'><font size=13 face='3 of 9 barcode'>*".$row['Acccod']."*</font></td>
                                    
                                    </tr>
                                    <tr>
                                    <td colspan='2' rowspan='1'></td>
                                    </tr>
                                    <tr>
                                    </tr>
                                    <tr>
                                    <td></td>
                                    <td></td>
                                    </tr>
                                    </tbody>
                                </table></td>";	
                            
                            }
                             
                        if ($resto == 0) 
                            {
                            echo "</tr></table>";
                            
                            if ($k%14==0)
                            {
                                echo "</div>";
                            }
                            
                            } # Si es la última celda, cerramos </tr>
                        
                        if ($resto <> 0) { # Si el resultado no es múltiple de $columnes acabamos de rellenar los huecos
                        $ajust = $columnes - $resto; # Número de huecos necesarios
                        for ($j = 0; $j < $ajust; $j++) 
                        {
                            echo "<td>&nbsp;</td>";                            
                            
                            }
                        echo "</tr>"; # Cerramos la última línea </tr>
                        
                        
                        }
                        
                       // echo "</table>"; 
                        echo "</div>";
                             
					
				
				echo "<br><br><br>Nro de Votos Impresos : ".$num."<br>";
		
		}
	}
?>
</body>
</html>