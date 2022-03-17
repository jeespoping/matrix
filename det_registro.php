<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Registro y Consulta de Informacion</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> det_registro.php Ver. 2008-11-06</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
/**********************************************************************************************************************  
     Programa :  det_registro.php
     Fecha de Liberación : 2003-09-30
   	 Realizado por : Pedro Ortiz Tamayo
     Version Actual : 2006-10-11
    
    OBJETIVO GENERAL : Este programa permite ingresar los datos a los formularios de forma dinamica segun su estructuracon una validacion estandar de acuerdo al
    tipo de dato.
  	Los tipos de datos que se manejan son :
  						0   - Alfanumerico
  						1   - Entero
  						2   - Real
  						3   - Fecha
  						4   - Texto
  						5   - Seleccion
  						6   - Formula
  						7   - Grafico
  						8   - Automatico
  						9   - Relacion
  						10  - Booleano
  						11  - Hora
  						12  - Algoritmico si la variable wswalg = on la salida es controlada x el usuario
  						13  - Titulo  *** No se Almacena en Matrix ***
  						14  - Hipervinculo
  						15  - Algoritmico_M (Modificable)
  						16  - Protegido o Password
  						17  - Auxiliar *** No se Almacena en Matrix *** Permite ejecucion de algoritmos com salida variable
  						18  - Relacion_NE Campos de relacion NO Especifica. Su funcion es identica ala campo de Relacion pero solo almacena la primera relacion

  			        La variable call o de llamado puede tener los siguientes parametros:
  						1. call = 0  llamado standard, graba los registros con el usuario propietario de la tabla
  						2. call = 1  llamado especifico, se llama el programa especificando un solo formulario para interactuar con el.
  						3. call = 2  llamado directo, se pasa al programa det_registro.php sin pasar por el programa registro.php
  						4. call = 3  llamado con cambio de usuario. El programa llama a det_registro.php y se graban los registros con el usuario de login.
  					
   REGISTRO DE MODIFICACIONES :
   .2008-11-06
   		Se modifico el programa para permitir que los campos de texto asociados a otros campos del mismo formulario puedan llenarse mas facilmente.
   		Cuando se selecciona con ampersan (&) el campo relacionado este lo pega y desaparece el & para una nueva seleccion ademas coloca el rag <BR>
   		de forma automatica.
   
   .2006-10-11
   		Se modifica la seleccion de los campos de relacion para que haga el query con un order by por el campo por el que se busca.
   		
   .2006-03-15
   		Se modifica la inicializacion de la hora con la del sistema.
   
   .2006-01-04
   		Se crea el tipo de Relacion_NE para otimizar la grabacion  de los campos relacionados con otros formularios. En estos campos solo se almacena
   		el primer campo relacionado.
   		Se crea la tabla de root numero 30 donde se encuentra el diccionario de datos. Si el campo este en el diccionario muestra su descripcion, en caso
   		contrario muestra el nombre del campo definido en el detalle del formulario.
   		Se modifico el campo Auxiliar para ejecutar algoritmos de ser necesario con salida variable definida desde el archivo incluido mediante la variable
   		wswalg.
   		 wswalg = on  el usuario en el archivo includo escoje el tipo de salida.
   		 wswalg = off  salida estandard.
   			
  
    .2005-02-21
    	Ultima Modificacion Registrada.
***********************************************************************************************************************/
   
   
/*FUNCION PARA DETERMINAR SI UN AÑO ES BISIESTO
    si es multiplo de 4 y no es multiplo de 100 o es multiplo de 400*/
	function bisiesto($year)
	{
		return(($year % 4 == 0 and $year % 100 != 0) or $year % 400 == 0);
	}
/*FUNCION DE VALIDACIÓN DE DATOS MEDIANTE EXPRESIONES REGULARES*/	
	function validar($chain,$type)
	{
		
		switch ($type)
		{
			case 0: 
				/*VARIABLES ALFANUMERICAS*/			
				$regular="/^([=a-zA-Z0-9\sñÑ@\/#-.;_<>])+$/i"; // edwin
				return (preg_match($regular,$chain));
				break;
			case 1:
			/*VARIABLES ENTERAS*/
				$regular="/^(\+|-)?([[:digit:]]+)$/";
				if (preg_match($regular,$chain,$occur))
					if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
						return false;
					else
						return true;
				else
					return false;
				break;
			case 2:
			/*VARIABLES REALES O DE PUNTO FLOTANTE CON NOTACIÓN NORMAL O CIENTIFICA*/
				$decimal ="/^(\+|-)?([[:digit:]]+)\.([[:digit:]]+)$/";
				$cientifica ="/^(\+|-)?([[:digit:]]+)\.([[:digit:]]+)e(\+|-)?([[:digit:]]+)$/i";
				if (preg_match($decimal,$chain,$occur))
					if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
						return false;
					else
						return true;
				else
					if(preg_match($cientifica,$chain,$occur))
						if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
							return false;
						else
							return true;
					else
						return false;
				break;
			case 3:
			/*LA VARIABLE ES TIPO FECHA*/
				#$fecha="^([[:digit:]]{1,2})/([[:digit:]]{1,2})/([[:digit:]]{4})$";
				$fecha="/^([[:digit:]]{4})-([[:digit:]]{1,2})-([[:digit:]]{1,2})$/";
				if(preg_match($fecha,$chain,$occur))
				{
					if($occur[2] < 0 or $occur[2] > 12)
						return false;
					if(($occur[3] < 0 or $occur[3] > 31) or 
					  ($occur[2] == 4 and  $occur[3] > 30) or 
					  ($occur[2] == 6 and  $occur[3] > 30) or 
					  ($occur[2] == 9 and  $occur[3] > 30) or 
					  ($occur[2] == 11 and $occur[3] > 30) or 
					  ($occur[2] == 2 and  $occur[3] > 29 and bisiesto($occur[1])) or 
					  ($occur[2] == 2 and  $occur[3] > 28 and !bisiesto($occur[1])))
						return false;
					return true;
				}
				else
					return false;
				break;	
			case 6:
			/*EVALUACIÓN DEL CALCULO DE UNA FORMULA*/
				$decimal ="/^(\+|-)?([[:digit:]]+)\.([[:digit:]]+)$/";
				$cientifica ="/^(\+|-)?([[:digit:]]+)\.([[:digit:]]+)e(\+|-)?([[:digit:]]+)$/i";
				if (preg_match($decimal,$chain,$occur))
					if(substr($occur[2],0,1)==0 and strlen($occur[2])!=1)
						return false;
					else
						return true;
				else
					if(preg_match($cientifica,$chain,$occur))
						if((substr($occur[2],0,1)==0 and strlen($occur[2])!=1) or (substr($occur[4],0,1)==0 and strlen($occur[4])!=1))
							return false;
						else
							return true;
					else
						return false;
				break;
			case 11:
			/*VALIDACION DE LA VARIABLE TIPO HORA*/
				$hora="/^([[:digit:]]{1,2}):([[:digit:]]{1,2}):([[:digit:]]{1,2})$/";
				if(preg_match($hora,$chain,$occur))
					if($occur[1] < 0 or $occur[1] >23 or $occur[2]<0 or $occur[2]>59)
						return false;
					else
						return true;
				else
					return false;
				break;
			}
	}
			
/*EVALUACIÓN RECURSIVA DE FORMULAS ESCRITAS EN MODO POSFIJO*/		
	function fx($cadena,$valores,$items)
	{
		$pila = array();
		$position = 0;
		$inc=-1;
		$const="";
		while ($position <= (strlen($cadena)-1))
		{
			if(is_numeric(substr($cadena,$position,1)) or substr($cadena,$position,1)==".")
			{
				$const=$const.substr($cadena,$position,1);
				$position++;
			}
			else
			{
				if (strlen($const)>0)
				{
					$inc++;
					$pila[$inc]=(double)$const;
					$const="";
				}
				switch (substr($cadena,$position,1))
				{
					/* Iidentificamos la variable con $ y lo demas son operaciones*/
					case "$":
						$inc++;
						$pila[$inc]=(double)$valores[(double)substr($cadena,$position+1,4)-1];
						$position=$position+5;
						break;
					case "+":
						$pila[$inc-1]=$pila[$inc-1]+$pila[$inc];
						$inc--;
						$position++;
						break;
					case "-":
						$pila[$inc-1]=$pila[$inc-1]-$pila[$inc];
						$inc--;
						$position++;
						break;
					case "*":
						$pila[$inc-1]=$pila[$inc-1]*$pila[$inc];
						$inc--;
						$position++;
						break;
					case "/":
						if ($pila[$inc]!=0)
							$pila[$inc-1]=$pila[$inc-1]/$pila[$inc];
						else
							$pila[$inc-1]=0;
						$inc--;
						$position++;
						break;
					default:
						switch (substr($cadena,$position,5)) 
						{
							case "sin--":
								$pila[$inc]=sin($pila[$inc]);
								$position=$position+5;
								break;
							case "cos--":
								$pila[$inc]=cos($pila[$inc]);
								$position=$position+5;
								break;
							case "tan--":
								$pila[$inc]=tan($pila[$inc]);
								$position=$position+5;
								break;
							case "atan-":
								$pila[$inc]=atan($pila[$inc]);
								$position=$position+5;
								break;
							case "log10":
								$pila[$inc]=log10($pila[$inc]);
								$position=$position+5;
								break;
							case "log--":
								$pila[$inc]=log($pila[$inc]);
								$position=$position+5;
							break;
							case "exp--":
								$pila[$inc]=exp($pila[$inc]);
								$position=$position+5;
								break;
							case "abs--":
								$pila[$inc]=abs($pila[$inc]);
								$position=$position+5;
								break;
							case "sqrt-":
								$pila[$inc]=sqrt($pila[$inc]);
								$position=$position+5;
								break;
							case "pow--":
								$pila[$inc-1]=pow($pila[$inc-1],$pila[$inc]);
								$inc--;
								$position=$position+5;
								break;
							case "pi---":
								$inc++;
								$pila[$inc]=pi();
								$position=$position+5;
								break;
						}
				}
			}
		}
		$fx=$pila[$inc];
		return $fx;
	}	
	/**********************
	  *  INICIO DEL PROGRAMA   *
	  **********************/
	echo "<form action='det_registro.php' method=post>";
	

	$superglobals = array($_SESSION,$_REQUEST);
	foreach ($superglobals as $keySuperglobals => $valueSuperglobals)
	{
		foreach ($valueSuperglobals as $variable => $dato)
		{
			$$variable = $dato; 
		}
	}

	//**** VALIDACIONES ****
	/*$cardinal= variable que almacena el numero de campos del formulario que esta siendo llenado
		$registro[$cardinal]= último campo del formulario= datos completos */
	if (isset($cardinal) and isset($registro[$cardinal]) and $registro[$cardinal]=="on")
	{
		/*Empiezan las validaciones para guardar la info del formulario*/
		$tiperr = array(); 						//arreglo de los errores tiene tantos elementos como la cardinalidad del formulario
		$sumaerr=0;
		for ($i=0;$i<$cardinal;$i++)
		{
			$tiperr[$i] =-1;
		}
		/*$pos1=usuario; $pos4=formulario   
		 Es necesario saber si el registro existe o no, para definir si se va a modificar un registro preexistente o se va a crear uno nuevo */
		$query = "select * from ".$pos1."_".$pos4." where medico='".$pos1."'  and fecha_data='".$pos2."' and hora_data='".$pos3."'";
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		$registros=$num;
		$automaticos=array();	/*arreglo de control de numeración de los campos aumaticos*/
		$w=-1;
		for ($i=0;$i<$cardinal;$i++)
		{
			if($tipos[$i]==99)				// Seguridad
				if($registro[$i] == "A" or ($registro[$i] == "C" and $Usuario == "NO"))
					$registro[$i]=$registro[$i]."-".$pos6;
				else
					$registro[$i]=$registro[$i]."-".$pos6."-".$Usuario;
			/*Si el registro no existe*/	
			if(!isset($registro[$i]))
				if($tipos[$i]==10) 		//Booleano
					$registro[$i]="off";		//inicializa en off los booleanos
				else
					$registro[$i]="on";		// el resto en on
			if($tipos[$i] == 18 and strpos($registro[$i],"-") !== false) // Campos de Relacion_NE
				$registro[$i]=substr($registro[$i],0,strpos($registro[$i],"-")); //Se Graba con el primer campo de la relacion
			if (strlen($registro[$i])==0 and $tipos[$i] != 8)		// el registro no es automatico y esta vacio
				$tiperr[$i] = 0;																		//error por ausencia
			if ($tipos[$i]== 3 and ($year[$i] != "1993" or $month[$i] != "01" or $day[$i] != "01")) // si el tipo es fecha y se modifico el drop down inicializa con esta info
				$registro[$i] = $year[$i]."-".$month[$i]."-".$day[$i];
			if ($tipos[$i]== 11 and ($horas[$i] != "00" or $min[$i] != "00" or $sec[$i] != "00"))  // si el tipo es hora y se modifico el drop down inicializa con esta info
				$registro[$i] = $horas[$i].":".$min[$i].":".$sec[$i];
				/*Validación con función de las variables alfanumericas, enteras, reales, fecha, formula, hora siempre que no esten ausentes (vacias)*/
			if (($tipos[$i]== 0 or $tipos[$i]== 1 or $tipos[$i]== 2 or $tipos[$i]== 3 or $tipos[$i]== 6 or $tipos[$i]== 11) and $tiperr[$i]== -1)
			{
				if(!validar($registro[$i],$tipos[$i]))
					$tiperr[$i]=1;
			}
			if($tipos[$i] == 8 and $registros == 0)
			{	
				/*Busca en el archivo de numeración del formulario el acumulado para ese campo y lo aumenta en 1*/
				$query = "select * from numeracion where medico='".$pos1."' and formulario='".$pos4."' and campo='".$tuplas[$i]."'";
				$err = mysql_query($query,$conex);
				$num = mysql_num_rows($err);
				if($num > 0)
				{
					$w=$w+1;
					$automaticos[$w]=$i;
				}
				else
					$tiperr[$i]=1;
			}		
			if($tipos[$i]==12 or $tipos[$i]==15)			// si el campo es algoritmico
			{
				$ini=strpos($coments[$i],"c");
				include_once($grp."/".substr($coments[$i],$ini,16));		//llama el archivo include de validación OJO!!! ESTE ARCHIVO DEBE INICIALIZAR LA VARIABLE $tiperr[$i] EN 1 SI LA VALIDACIÓN ES INCORRECTA
			}
			$sumaerr=$sumaerr+$tiperr[$i];	
		}
		$query = "select * from ".$pos1."_".$pos4." where id=".$id;
		$err = mysql_query($query,$conex);
		$num = mysql_num_rows($err);
		if ($sumaerr == (-1)*$cardinal and $num == 0) // Todas las variables estan OK
		{
			for ($i=0;$i<=$w;$i++) //$w tiene el numero de automaticos
			{
				$query = "lock table numeracion LOW_PRIORITY WRITE"; // bloqueo la tabla de numeracion para evitar los registros repetidos si hay otros usuarios
				$err1 = mysql_query($query,$conex);
				$query =  " update numeracion set secuencia = secuencia + 1 where medico='".$pos1."' and formulario='".$pos4."' and campo='".$tuplas[$automaticos[$i]]."'";
				//Se incrementa el valor del campo en el archivo de numeracion (el nombre del campo esta en $tuplas)
				$err2 = mysql_query($query,$conex);
				$query = "select * from numeracion where medico='".$pos1."' and formulario='".$pos4."' and campo='".$tuplas[$automaticos[$i]]."'";
				$err3 = mysql_query($query,$conex);
				$row = mysql_fetch_array($err3);
				$registro[$automaticos[$i]]=$row[3];
				$query = " UNLOCK TABLES";														//Desbloqueo la tabla
				$res = mysql_query($query,$conex);				
				if ($err1 != 1 or $err2 != 1 or $res != 1)
				{							//Busco los errores
					$tiperr[$automaticos[$i]]=1;	//asigno el error 
					$sumaerr=$sumaerr + $tiperr[$automaticos[$i]];						
				}
			}
		}	
		if ($sumaerr == (-1)*$cardinal)  // si todo esta ok
		{
			echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CCCCFF LOOP=-1>LOS DATOS ESTAN OK!!!!</MARQUEE></FONT>";
			echo "<br><br>";
			$query = "select * from ".$pos1."_".$pos4." where id=".$id;
			/*EL $id VIENE POR METODO POST,
				Cuando se esta generando un registro nuevo el id es cero , cuando se edita este ya existe, 
				si el id es cero la busqueda va a ser nula lo que nos lleva a crear el registro en la tabla (insert), 
				en otro caso estamos editando el registro asi que vamos a modificat la tabla (update) */
			$err = mysql_query($query,$conex);
			$num = mysql_num_rows($err);
			if ($num > 0)
			{
				$store="U";						// construcción del update
				$query="update ".$pos1."_".$pos4." set ";
			}
			else
			{
				$store="I";						// construcción del insert
				$query="insert into ".$pos1."_".$pos4." (medico,fecha_data,hora_data";
				for ($i=0;$i<$cardinal;$i++)
				{
					if($tipos[$i] != 13 and $tipos[$i] != 17)
						$query=$query.",".$items[$i]; //se traen los nombres de todos los campos
				}
				$query=$query.")";
				$query=$query." values('".$pos1."','".$pos2."','".$pos3."'"; // empiezan los valores delos campos
			}
			for ($i=0;$i<$cardinal;$i++)
			{
				if($tipos[$i] != 13 and $tipos[$i] != 17)
				{
					switch ($store)
					{
						case "U":
						if($tipos[$i]== 1 or $tipos[$i]== 2 or $tipos[$i]== 6 or $tipos[$i]== 8) // tipos que no llevan comilla
						{
							if (substr($query,strlen($query)-5,5) !=  " set " and $proteccion[$i] == "A")
								$query=$query.",";
							if ($proteccion[$i] == "A")
								$query=$query.$items[$i]."=".$registro[$i]; // nombre del campo= valor 
						}
						else	// si llevan comilla
						{
							if (substr($query,strlen($query)-5,5) != " set " and $proteccion[$i] == "A")
								$query=$query.",";
							if ($proteccion[$i] == "A")
								$query=$query.$items[$i]."='".$registro[$i]."'";
						}
						break;
						case "I":
						if($tipos[$i]== 1 or $tipos[$i]== 2 or $tipos[$i]== 6 or $tipos[$i]== 8)
						{
							if ($i >= 0)
								$query=$query.",";
							$query=$query.$registro[$i];
						}
						else
						{
							if ($i >= 0)
								$query=$query.",'";
							$query=$query.$registro[$i]."'";	
						}
						break;
					}
				}
			}
			switch ($store)
			{
				case "U":										// si es update el id opera
					$query =$query." where id=".$id;
					#echo $query."<br>";
					$err = mysql_query($query,$conex);
					break;
					case "I":									// el id no opera por ser insert	
					$query =$query.")";
					#echo $query."<br>";
					$err = mysql_query($query,$conex);		
					 if ($err != 1)
					 // hay un error en el insert duplicidad del id
					 {
						#echo mysql_errno().":".mysql_error()."<br>";
						$tiperr[$i]=1;
						echo "<center><table border=0 aling=center>";
						echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
						echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>YA EXISTE EL CODIGO DEL CAMPO -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
						echo "<br><br>";
					}
			}
		}
		if ($sumaerr != (-1)*$cardinal) // EXISTE AL MENOS UN ERROR O UN DATO INCOMPLETO
		{
				echo "<center><table border=0 aling=center>";
				echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>"; // el muñequito dandose en la cabeza
				for ($i=0;$i<$cardinal;$i++)
				{
					switch ($tiperr[$i])
					{
						case 0:
						echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CCCCFF LOOP=-1>LOS DATOS ESTAN INCOMPLETOS EN ".$items[$i]." -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
						echo "<br><br>";		
						break;
						case 1:
						echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#FF0000 LOOP=-1>LOS DATOS ESTAN ERRONEOS EN ".$items[$i]." -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
						echo "<br><br>";
						break;
					}
				}
			}
			else
			{
				$pos5=0;							//Si no hay errores inicializa para el siguiente
				$pos2 = date("Y-m-d");
				$pos3 = (string)date("H:i:s");
			}
	}
	/*******************************************
	  * 								INICIO DEL PROGRAMA                     *
	  *******************************************/
	  // Se seleccione el grupo al cual pertenece el usuario
	
	$query = "select grupo from usuarios where codigo='".$pos1."' ";
	$err5 = mysql_query($query,$conex);
	$row5 = mysql_fetch_array($err5);
	$grp=$row5[0];
	  /*Traer la estructura del formulario en ejecución para inicializar variables*/
	$query = "select * from det_formulario where medico='".$pos1."' and codigo='".$pos4."' order by posicion";
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	$items=array();		// nombres de los campos del formulario
	$tipos=array();		// tipos de cada campo
	$tuplas=array();	//codigo numerico del campo
	$diccionary=array(); //Diccionario de datos
	$coments=array(); //comentarios asociados a cada campo
	$proteccion=array(); //determinacion del tipo de proteccion del campo
	$worden=array(); //arreglo con los campos de un archivo relacionado
	$wswalg="off"; //on para algoritmicos de seleccion. off para algoritmicos comunes
	if($pos5==0) // inicializo las varibles por que se 'pinta' el formulario por primera vez
	{
		$criterio=array();	//objetos auxiliares que estan en ciertos tipos de campo como el d relacion (lo que va en la ultima columna de la derecha)
		$registro=array();
		$pos5=1;
	}
	if ($num > 0)
	{
		for ($i=0;$i<$num;$i++) // inicializacion con la info del query
		{
			$row = mysql_fetch_array($err);
			$tuplas[$i] = $row[2];
			$query = "select Dic_Descripcion from root_000030 where Dic_Usuario='".$pos1."' and Dic_Formulario='".$pos4."'  and Dic_Campo='".$row[2]."'"; 
			$err2 = mysql_query($query,$conex);
			$num2 = mysql_num_rows($err2);
			if($num2 > 0)
			{
				$row2 = mysql_fetch_array($err2);
				$diccionary[$i]=$row2[0];
			}
			else
				$diccionary[$i]=$row[3];
			$items[$i] = $row[3];
			$tipos[$i] = $row[4];
			$coments[$i] = $row[6];
			$proteccion[$i] = $row[7];
			if(!isset($criterio[$i]))			
				$criterio[$i] = "";
		}
		$tuplas[$num] = "9999";
		$items[$num] = "Seguridad";
		$tipos[$num] = 99;
		$coments[$num] = "";
		$proteccion[$num] = "A";
		$diccionary[$num]="Seguridad";
	}
	$query = "select * from  ".$pos1."_".$pos4." where id=".$id; // verifico si el registro existe o no con ayuda del id
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	$wsw = 1;	
	if ($num == 0)		// el registro no existe
	{
		$pos2 = date("Y-m-d");	// inicializo con fecha y hora del sistema
		$pos3 = (string)date("H:i:s");
		$wsw = 0;
	}
	else
		$row = mysql_fetch_array($err); // en row queda toda la info del registro si existe
	$query = "select * from det_formulario where medico='".$pos1."' and codigo='".$pos4."'  order by posicion"; //traigo la estructura del formulario para 'pintarlo'
	$err = mysql_query($query,$conex);
	$num = mysql_num_rows($err);
	$num = $num + 1;
	//hypervinculo a  registro con las condiciones que tenia al hacer el llamado
	if($call != 2)
		echo "<A HREF='registro.php?tipo=".$tipo."&amp;Valor=".$Valor."&amp;Form=".$Form."&amp;Frm=".$Form."&amp;Vlr=".$Valor."&amp;call=".$call."&amp;change=".$change."&amp;key=".$key."&amp;Pagina=".$Pagina."'>Retornar</a>"; 
	echo "<table border=0 align=center>";
	// variable que siempre debo tener
	echo "<input type='HIDDEN' name= 'id' value='".$id."'>";
	echo "<input type='HIDDEN' name= 'pos1' value='".$pos1."'>";
	echo "<input type='HIDDEN' name= 'pos2' value='".$pos2."'>";
	echo "<input type='HIDDEN' name= 'pos3' value='".$pos3."'>";
	echo "<input type='HIDDEN' name= 'pos4' value='".$pos4."'>";
	echo "<input type='HIDDEN' name= 'pos5' value='".$pos5."'>";
	echo "<input type='HIDDEN' name= 'pos6' value='".$pos6."'>";
	echo "<input type='HIDDEN' name= 'tipo' value='".$tipo."'>";
	echo "<input type='HIDDEN' name= 'Form' value='".$Form."'>";
	echo "<input type='HIDDEN' name= 'call' value='".$call."'>";
	echo "<input type='HIDDEN' name= 'change' value='".$change."'>";
	//echo "<input type='HIDDEN' name= 'key' value='".$key."'>";
	echo "<input type='HIDDEN' name= 'Pagina' value='".$Pagina."'>";
	echo "<input type='HIDDEN' name= 'Valor' value='".$Valor."'>";
	echo "<input type='HIDDEN' name= 'grp' value='".$grp."'>";
	echo "<tr>";
	//encabezado de la tabla
	echo "<td bgcolor=#999999><b>Item</td></b>";
	echo "<td bgcolor=#999999><b>Valor</b></td>";
	echo "<td bgcolor=#999999><b>Criterio</b></td>";
	echo "</tr>";
	//tres variables fijas asignadas por el sistema
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Formulario</td>";			
	echo "<td bgcolor=#cccccc>".$pos4."</td>";
	echo "<td bgcolor=#cccccc></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Fecha</td>";			
	echo "<td bgcolor=#cccccc>".$pos2."</td>";
	echo "<td bgcolor=#cccccc></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Hora</td>";			
	echo "<td bgcolor=#cccccc>".$pos3."</td>";
	echo "<td bgcolor=#cccccc></td>";
	echo "</tr>";
	$m=-1;
	//PINTO EL FORMULARIO SEGUN LA ESTRUCTURA
	$cam=-1;
	for ($i=0;$i<$num;$i++)
	{
		if (!isset($registro[$i]))
			if ($wsw == 1)
				if ($tipos[$i] == 13 or $tipos[$i] == 17)
					$registro[$i] = "NO APLICA";
				else
				{
					$cam++;
					$registro[$i] = $row[$cam+3];
				}
			else
				if(isset($r[$i]))
					$registro[$i] = $r[$i];
				else
					$registro[$i] = "";
		else
			if ($tipos[$i] != 13 and $tipos[$i] != 17)
				$cam++;
		if($tipos[$i]==99 and $wsw==1)
		{
			$W=substr($registro[$i],2);
			$ini_W=strpos($W,"-");
			if(is_numeric($ini_W))
			{
				if(substr($W,0,$ini_W) == $pos6)
					$Usuario=substr($W,$ini_W+1);
				else
					$Usuario=substr($W,0,$ini_W);
			}
			$registro[$i]=substr($registro[$i],0,1);
		}
		echo "<tr>";
		if($tipos[$i] == 13)
			echo  "<td bgcolor=#999999 colspan=3 align=center><b>".$coments[$i]."</b></td>";
		else
			echo "<td bgcolor=#cccccc>".$diccionary[$i]."</td>";
		switch ($tipos[$i])
		{
				case 0:	// tipo alfanumerico
				if(strlen($registro[$i])==0)
					$registro[$i]=$registro[$i]."NO APLICA";		//inicialización
				echo "<td bgcolor=#cccccc><input type='TEXT' name='registro[".$i."]' size=54 maxlength=80 value='".$registro[$i]."'></td>";
				echo "<td bgcolor=#cccccc></td>";
				echo "</tr>";
				break;
				case 1:		//tipo entero
				if(strlen($registro[$i])==0)
					$registro[$i]="0";		//inicialización
				echo "<td bgcolor=#cccccc><input type='TEXT' name='registro[".$i."]' size=16 maxlength=16 value='".$registro[$i]."'></td>";
				echo "<td bgcolor=#cccccc></td>";
				echo "</tr>";
				break;
				case 2:	//tipo real
				if (strpos($registro[$i],".") === false) 
					if(strlen($registro[$i]) == 0)
						$registro[$i]="0.0";
					else
						$registro[$i]=$registro[$i].".0";				//inicialización
				echo "<td bgcolor=#cccccc><input type='TEXT' name='registro[".$i."]' size=16 maxlength=16 value='".$registro[$i]."'></td>";
				echo "<td bgcolor=#cccccc></td>";
				echo "</tr>";
				break;
				case 3:	//tipo fecha
				if(strlen($registro[$i])==0)		// si el registro viene vacio
				{
					$registro[$i]=date("Y-m-d");	// inicializa con la fecha del sistema
					$year[$i]=substr($registro[$i],0,4); // primera inicializacion para las variables de fecha
					$month[$i]=substr($registro[$i],5,2);
					$day[$i]=substr($registro[$i],8,2);
				}
				else		// si el registro trae algo
					if(isset($year[$i]) and ($year[$i] != substr($registro[$i],0,4) or $month[$i] != substr($registro[$i],5,2) or $day[$i] !=substr($registro[$i],8,2)))// y el drop down se modifico
					{
						$registro[$i] = $year[$i]."-".$month[$i]."-".$day[$i];
					}
					else			
					{
						$year[$i]=substr($registro[$i],0,4);
						$month[$i]=substr($registro[$i],5,2);
						$day[$i]=substr($registro[$i],8,2);
					}
				echo "<td bgcolor=#cccccc><input type='hidden' name='registro[".$i."]' value='".$registro[$i]."'>".$registro[$i]."</td>";
				echo "<td bgcolor=#cccccc>";
				// inicialización de la ayuda (drop down de fecha)
				echo "<select name='year[".$i."]'>";
				echo "<option>0000</option>";
				for($f=1900;$f<2051;$f++)
				{
					if($f == $year[$i])
						echo "<option selected>".$f."</option>";
					else
						echo "<option>".$f."</option>";
				}
				echo "</select><select name='month[".$i."]'>";
				echo "<option>00</option>";
				for($f=1;$f<13;$f++)
				{
					if($f == $month[$i])
						if($f < 10)
							echo "<option selected>0".$f."</option>";
						else
							echo "<option selected>".$f."</option>";
					else
						if($f < 10)
							echo "<option>0".$f."</option>";
						else
							echo "<option>".$f."</option>";
				}
				echo "</select><select name='day[".$i."]'>";
				echo "<option>00</option>";
				for($f=1;$f<32;$f++)
				{
				if($f == $day[$i])
					if($f < 10)
						echo "<option selected>0".$f."</option>";
					else
						echo "<option selected>".$f."</option>";
				else
					if($f < 10)
						echo "<option>0".$f."</option>";
					else
							echo "<option>".$f."</option>";
				}
				echo "</select></td></tr>";
				break;
				case 4: // tipo texto
				if(strlen($registro[$i])==0)		// si el registro esta vacio 
					$registro[$i]=$registro[$i].".";		// se inicializa con '.'
				//texto asociado a selecciones; 	$multiple[$i] es la opcion escogida de la selección asociada
				if(isset($multiple[$i])  and  is_int(strpos($registro[$i], "&"))  and strlen($coments[$i])>0 and substr($coments[$i],0,1) != 'R')
				{
					if(strlen($registro[$i])>0)		
						$registro[$i]=$registro[$i].$multiple[$i]."<br>".chr(10);
					else
						$registro[$i]=$multiple[$i]."<br>".chr(10);
				}
				else
				{
					// texto asociados a campos de relación del mismo formulario debe ser creado en el comentario con una R al principio
					if (strlen($coments[$i])>0  and substr($coments[$i],0,1) == 'R' and is_int(strpos($registro[$i], "&")))
					{
						$rel=(integer)substr($coments[$i],2,4);
						if($registro[$rel-1] != "NO APLICA")
						{
							// modificacion pendiente de validacion para campos de texto con formulario s asociados
							$registro[$i]=$registro[$i]."<br>".chr(10).$registro[$rel-1];
							if(strpos($registro[$i],"&") > 0 and strpos($registro[$i],"&") < strlen($registro[$i]))
								$registro[$i]=substr($registro[$i],0,strpos($registro[$i],"&"))." ".substr($registro[$i],strpos($registro[$i],"&")+1);
							else
								if(strpos($registro[$i],"&") == 0)
									$registro[$i]=substr($registro[$i],strpos($registro[$i],"&")+1);
								else
									$registro[$i]=substr($registro[$i],0,strpos($registro[$i],"&"));
						}
					}
				}
				echo "<td><textarea name='registro[".$i."]' cols=40 rows=5>".$registro[$i]."</textarea>";
				echo "<td bgcolor=#cccccc>";
				// Llenar la seleccion asociada 
				if (strlen($coments[$i])>0 and strpos($coments[$i],"-")>0 and substr($coments[$i],0,1) != 'R')
				{
					echo "<select name='multiple[".$i."]'>";
					$ini = strpos($coments[$i],"-");
					$query = "select * from det_selecciones where medico='".$pos1."' and codigo='".substr($coments[$i],0,$ini)."' and activo='A'";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					for ($j=0;$j<$num1;$j++)
					{	
					$row1 = mysql_fetch_array($err1);
					echo "<option>".$row1[2]."-".$row1[3]."</option>";
					}	
				}
				echo "</td></tr>";
				break;
				case 5:		// tipo selección	
				echo "<td bgcolor=#cccccc>";
				if (strlen($coments[$i])>0)
				{
					echo "<select name='registro[".$i."]'>";
					$ini = strpos($coments[$i],"-"); // la seleccion definida esta en el comentario y los numero antes del guion es el codigo de la selección en det_selecciones
					$query = "select * from det_selecciones where medico='".$pos1."' and codigo='".substr($coments[$i],0,$ini)."' and activo='A'";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					for ($j=0;$j<$num1;$j++)
					{	
					$row1 = mysql_fetch_array($err1);
					$ini = strpos($registro[$i],"-");
					if (substr($registro[$i],0,$ini)==$row1[2])
						echo "<option selected>".$row1[2]."-".$row1[3]."</option>";
					else
						echo "<option>".$row1[2]."-".$row1[3]."</option>";
					}	
				}
				echo "</td>";
				echo "<td bgcolor=#cccccc></td>";
				echo "</tr>";
				break;	
				case 6:			// tipo formula
				$registro[$i]=fx($coments[$i],$registro,$i); // llamada a la funcion de calculo
				if(strpos($registro[$i],".")===false)
					$registro[$i]=$registro[$i].".0";
				echo "<td bgcolor=#cccccc><input type='TEXT' name='registro[".$i."]' size=16 maxlength=16 value='".$registro[$i]."'></td>";
				echo "<td bgcolor=#cccccc>".$coments[$i]."</td>";
				echo "</tr>";
				break;
				case 7:		// grafico
				if(strlen($registro[$i])==0)
					$registro[$i]=$registro[$i]."NO APLICA";					
					// abre una nueva ventana por el target= blank con la grafica cuyo nombre se encuentra en $registro[$i]
				echo "<td bgcolor=#cccccc><input type='TEXT' name='registro[".$i."]' size=54 maxlength=80 value='".$registro[$i]."'></td>";
				echo "<td bgcolor=#cccccc><A HREF='graficas.php?Graph=".$registro[$i]."&amp;usuario="."1-".$key."' target = '_blank'>Ver grafica</td>";
				echo "</tr>";
				break;
				case 8:		// automatico	
				echo "<input type='HIDDEN' name= 'registro[".$i."]' value='".$registro[$i]."'>";
				echo "<td bgcolor=#cccccc>".$registro[$i]."</td>";
				echo "<td bgcolor=#cccccc></td>";
				echo "</tr>";
				break;
				case 9:			// tipo relacion
				echo "<td bgcolor=#cccccc>";
				if (strlen($coments[$i])>0 and isset($criterio[$i]))
				{
					$campos=array();	// codigo numerico de los campos con los que se relaciona sale del comentario
					$item1=array();	 //codigo numerico del campo
					$item2=array();	// nombre del campo
					$ini = strpos($coments[$i],"-"); 
					$numero = (integer)substr($coments[$i],0,$ini); // numero de campos con los cuales se relaciona
					if(!is_numeric(substr($coments[$i],$ini+1,1)))
					{
						$o = substr($coments[$i],$ini+1);
						$ini_w= strpos($o,"-"); 
						$DU=substr($o,0,$ini_w);
						$formulary = substr($o,$ini_w+1,6);
						$ini=$ini+$ini_w+8;
					}
					else
					{
						if(isset($DU))
							unset($DU);
						$formulary = substr($coments[$i],$ini+1,6);	// codigo numerico del formulario con el que se relaciona
						$ini=$ini+7;			// 7 caracteres del cod del formulario mas el guion
					}
					for ($j=0;$j<$numero;$j++)
					{
						$campos[$j]=substr($coments[$i],$ini+1,4); //codigo numerico de cada campo
						$ini=$ini+5;			// 5 caracteres del cod del campo mas el guion
					}
					$ini = strpos($criterio[$i],"-");
					if(isset($DU))
						$query = "select * from det_formulario where medico='".$DU."' and codigo='".$formulary."'";
					else
						$query = "select * from det_formulario where medico='".$pos1."' and codigo='".$formulary."'";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					$codsn=0;
					for ($j=0;$j<$num1;$j++)
					{
						$row1 = mysql_fetch_array($err1);
						$worden[0][$j]=$row1[2];
						$worden[1][$j]=$row1[3];
					}
					for($k=0;$k<$numero;$k++) // numero= numero de campos con los que se relaciona
					{
						for ($j=0;$j<$num1;$j++)
						{
							if($campos[$k]==$worden[0][$j]) // si el codigo del campo encontrado coincide con el que venia el el comentario
							{
								$item1[$codsn]=$worden[0][$j];	//codigo numerico del campo de relacion	
								$item2[$codsn]=$worden[1][$j];	//nombre del campo de relacion
								$codsn=$codsn+1;
							}
						}
					}
					if(isset($opciones[$i])) // en opciones se encuetra el codigo y los campos en modo selección
						$ini1 = strpos($opciones[$i],"-");	
					else
					{
						$opciones =$item1[0]."-".$item2[0];	
						$ini1= strpos($opciones,"-");	
					}
					if($criterio[$i] == "") // inicialización  si el criterio de busqueda esta vacio y del registro
						$criterio[$i]="@#$%^&*";
					if( strlen($registro[$i]) == 0)
						$registro[$i]="";
					
					$query="select "; //empieza la construcción de la busqueda dado un criterio
					for ($j=0;$j<$codsn;$j++)
					{
						$query=$query." ".$item2[$j]; // se usa el nombre del campo pasa por todos los campos seleccionados
						if($j==$codsn-1)
							$query=$query." "; //si es el ultimo campo va un espacio
						else
							$query=$query.","; // si no lo es separa con moma
					}
					echo "<select name='registro[".$i."]'>";
					/* $opciones[$i]= campo a relacionar por medio del criterio 
						$ini1+1= posicion despues del guion es decir donde empieza el nombre del campo
						substr($opciones[$i],$ini1+1,strlen($opciones[$i]))= nombre del campo   
						$criterio[$i]= criterio para hacer el query*/
					if($criterio[$i] != "@#$%^&*")
					{
						if(isset($DU))
							$query =$query. " from ".$DU."_".$formulary." where medico='".$DU."' and ".substr($opciones[$i],$ini1+1,strlen($opciones[$i]))." like '%".$criterio[$i]."%' order by ".substr($opciones[$i],$ini1+1,strlen($opciones[$i]));
						else
						{
							$query =$query. " from ".$pos1."_".$formulary." where medico='".$pos1."' and ".substr($opciones[$i],$ini1+1,strlen($opciones[$i]))." like '%".$criterio[$i]."%'";
							$query=$query." and (seguridad like 'A-%'  or seguridad like '%".$pos6."%') order by ".substr($opciones[$i],$ini1+1,strlen($opciones[$i]));
						}
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						if($num1>0)
						{
							// Construir la selección
							for ($j=0;$j<$num1;$j++)
							{	
								$row1 = mysql_fetch_array($err1); // array con tantos elementos como campos se relacionen
								$conjunto="";
								for ($k=0;$k<$codsn;$k++)
								{
									$conjunto=$conjunto.$row1[$k];// se va pegando cada elmento del array para formar la linea con la info de la relación
									echo $row1[$k]."<br>";
									if($k==$codsn-1)
										$conjunto=$conjunto." "; // si es el ultimo se pone espacio
									else
										$conjunto=$conjunto."-"; // en caso contrario se pone guion
								}
								if ($registro[$i]==$conjunto)
									echo "<option selected>".$conjunto."</option>";  
								else
									echo "<option>".$conjunto."</option>";
							}
						}
						else
							echo "<option>".$registro[$i]."</option>";	// si el query no da resultados poner lo que habia 	
					$criterio[$i]="@#$%^&*";		
					}
					else
						echo "<option>".$registro[$i]."</option>";	// si el query no da resultados poner lo que habia 	
				}
				echo "</td>";
				echo "<td bgcolor=#cccccc>";
				echo "<select name='opciones[".$i."]'>";  // crear la seleccion de campos con los que se puede relacionar
				for ($j=0;$j<$codsn;$j++)
				{
					echo "<option>".$item1[$j]."-".$item2[$j]."</option>";		// codigo numerico del campo - nombre del campo
				}	
				echo "<input type='TEXT' name='criterio[".$i."]' size=20 maxlength=80 value='".$criterio[$i]."'></td>"; // donde se envia el criterio de busqueda
				echo "</tr>";
				break;
				case 10: // tipo boolean
				echo "<td bgcolor=#cccccc>";
				if($registro[$i]=="" or $registro[$i]=="off")	// si esta off o no titne nada solo pintelo
					echo "<input type='checkbox' name='registro[".$i."]'>";
				else // esta en on pintelo checked
					echo "<input type='checkbox' name='registro[".$i."]' checked>";
				echo "</td><td bgcolor=#cccccc></td></tr>";
				break;
				case 11: // tipo hora
				if(strlen($registro[$i])==0)
				{
					//$registro[$i]="00:00:00";
					$registro[$i]=date("H:i:s");	
					$horas[$i]=substr($registro[$i],0,2);
					$min[$i]=substr($registro[$i],3,2);
					$sec[$i]=substr($registro[$i],6,2);
				}
				else
				{				
					if(!isset($horas))
					{
						$horas[$i]=substr($registro[$i],0,2);
						$min[$i]=substr($registro[$i],3,2);
						$sec[$i]=substr($registro[$i],6,2);
					}
					if(isset($horas[$i]) and ($horas[$i] != "00" or $min[$i] != "00" or $sec[$i] != "00")) // si esta modificada la ayuda configurar el registro según la modificación
						$registro[$i] = $horas[$i].":".$min[$i].":".$sec[$i];	
				}
				echo "<td bgcolor=#cccccc><input type='hidden' name='registro[".$i."]'  value='".$registro[$i]."'>".$registro[$i]."</td>";
				echo "<td bgcolor=#cccccc>";
				//crear la ayuda para generar los drop down
				echo "<select name='horas[".$i."]'>";
				for($f=0;$f<24;$f++)
				{
					if($f == $horas[$i])
						if($f < 10)
							echo "<option selected>0".$f."</option>";
						else
							echo "<option selected>".$f."</option>";
					else
						if($f < 10)
							echo "<option>0".$f."</option>";
						else
							echo "<option>".$f."</option>";
				}
				echo "</select><select name='min[".$i."]'>";
				for($f=0;$f<60;$f++)
				{
					if($f == $min[$i])
						if($f < 10)
							echo "<option selected>0".$f."</option>";
						else
							echo "<option selected>".$f."</option>";
					else
						if($f < 10)
							echo "<option>0".$f."</option>";
						else
							echo "<option>".$f."</option>";
				}
				echo "</select><select name='sec[".$i."]'>";
				for($f=0;$f<60;$f++)
				{
				if($f == $sec[$i])
					if($f < 10)
						echo "<option selected>0".$f."</option>";
					else
						echo "<option selected>".$f."</option>";
				else
					if($f < 10)
						echo "<option>0".$f."</option>";
					else
							echo "<option>".$f."</option>";
				}
				echo "</select></td></tr>";
				break;
				case 12: // tipo algoritmico
				$ini=strpos($coments[$i],"v");
				
				include_once($grp."/".substr($coments[$i],$ini,16)); // llamar el archivo a ejecutar siempre debe tener 16 caracteres y empezar con una v pequeña
				if(!isset($wswalg) or $wswalg == "off")
				{
					echo "<input type='HIDDEN' name= 'registro[".$i."]' value='".$registro[$i]."'>";
					echo "<td bgcolor=#cccccc>".$registro[$i]."</td>";
				}
				echo "<td bgcolor=#cccccc></td>";
				echo "</tr>";
				break;
				case 13:	// tipo titulo
				$registro[$i]=" ";
				echo "<input type='HIDDEN' name= 'registro[".$i."]' value='".$registro[$i]."'>";
				#echo "<td bgcolor=#cccccc></td>";
				#echo "<td bgcolor=#cccccc></td>";
				echo "</tr>";
				break;
				case 14: // tipo Hipervinculo
				if(strlen($registro[$i])==0)		// si el registro esta vacio 
					$registro[$i]=$registro[$i].".";		// se inicializa con '.'
				echo "<td><textarea name='registro[".$i."]' cols=40 rows=5>".$registro[$i]."</textarea>";
				if($registro[$i] == ".")
					echo "<td bgcolor=#cccccc></td></tr>";
				else
					echo "<td bgcolor=#cccccc><A HREF='".$registro[$i]."' target='_blanc'>".$registro[$i]."</A></td></tr>";
				break;
				case 15: // tipo algoritmico modificable
				if(strlen($registro[$i])==0)		// si el registro esta vacio 
					$registro[$i]=$registro[$i].".";		// se inicializa con '.'
				$ini=strpos($coments[$i],"v");
				include_once($grp."/".substr($coments[$i],$ini,16)); // llamar el archivo a ejecutar siempre debe tener 16 caracteres y empezar con una v pequeña
				echo "<input type='HIDDEN' name= 'registro[".$i."]' value='".$registro[$i]."'>";
				echo "<td bgcolor=#cccccc><textarea name='registro[".$i."]' cols=40 rows=5>".$registro[$i]."</textarea></td>";
				echo "<td bgcolor=#cccccc></td>";
				echo "</tr>";
				break;
				case 16:	// tipo protegido
				if(strlen($registro[$i])==0)
					$registro[$i]=$registro[$i].".";		//inicialización
				echo "<td bgcolor=#cccccc><input type='password' name='registro[".$i."]' size=8 maxlength=8 value='".$registro[$i]."'></td>";
				echo "<td bgcolor=#cccccc></td>";
				echo "</tr>";
				break;
				case 17:	// tipo auxiliar
				if(strlen($coments[$i]) == 0)	
				{
					if(strlen($registro[$i])==0)
						$registro[$i]=$registro[$i]."NO APLICA";		//inicialización
					echo "<td bgcolor=#cccccc><input type='TEXT' name='registro[".$i."]' size=54 maxlength=80 value='".$registro[$i]."'></td>";
				}
				else
				{
					$ini=strpos($coments[$i],"v");
					include_once($grp."/".substr($coments[$i],$ini,16)); // llamar el archivo a ejecutar siempre debe tener 16 caracteres y empezar con una v pequeña
				}
				echo "<td bgcolor=#cccccc></td>";
				echo "</tr>";
				break;
				case 18:			// tipo relacion_NE
				echo "<td bgcolor=#cccccc>";
				if (strlen($coments[$i])>0 and isset($criterio[$i]))
				{
					$campos=array();	// codigo numerico de los campos con los que se relaciona sale del comentario
					$item1=array();	 //codigo numerico del campo
					$item2=array();	// nombre del campo
					$ini = strpos($coments[$i],"-"); 
					$numero = (integer)substr($coments[$i],0,$ini); // numero de campos con los cuales se relaciona
					if(!is_numeric(substr($coments[$i],$ini+1,1)))
					{
						$o = substr($coments[$i],$ini+1);
						$ini_w= strpos($o,"-"); 
						$DU=substr($o,0,$ini_w);
						$formulary = substr($o,$ini_w+1,6);
						$ini=$ini+$ini_w+8;
					}
					else
					{
						if(isset($DU))
							unset($DU);
						$formulary = substr($coments[$i],$ini+1,6);	// codigo numerico del formulario con el ue se relaciona
						$ini=$ini+7;			// 7 caracteres del cod del formulario mas el guion
					}
					for ($j=0;$j<$numero;$j++)
					{
						$campos[$j]=substr($coments[$i],$ini+1,4); //codigo numerico de cada campo
						$ini=$ini+5;			// 5 caracteres del cod del campo mas el guion
					}
					$ini = strpos($criterio[$i],"-");
					if(isset($DU))
						$query = "select * from det_formulario where medico='".$DU."' and codigo='".$formulary."'";
					else
						$query = "select * from det_formulario where medico='".$pos1."' and codigo='".$formulary."'";
					$err1 = mysql_query($query,$conex);
					$num1 = mysql_num_rows($err1);
					$codsn=0;
					for ($j=0;$j<$num1;$j++)
					{
						$row1 = mysql_fetch_array($err1);
						$worden[0][$j]=$row1[2];
						$worden[1][$j]=$row1[3];
					}
					for($k=0;$k<$numero;$k++) // numero= numero de campos con los que se relaciona
					{
						for ($j=0;$j<$num1;$j++)
						{
							if($campos[$k]==$worden[0][$j]) // si el codigo del campo encontrado coincide con el que venia el el comentario
							{
								$item1[$codsn]=$worden[0][$j];	//codigo numerico del campo de relacion	
								$item2[$codsn]=$worden[1][$j];	//nombre del campo de relacion
								$codsn=$codsn+1;
							}
						}
					}
					if(isset($opciones[$i])) // en opciones se encuetra el codigo y los campos en modo selección
						$ini1 = strpos($opciones[$i],"-");	
					else
					{
						$opciones =$item1[0]."-".$item2[0];	
						$ini1= strpos($opciones,"-");	
					}
					if($criterio[$i] == "") // inicialización  si el criterio de busqueda esta vacio y del registro
						$criterio[$i]="@#$%^&*";
					if( strlen($registro[$i]) == 0)
						$registro[$i]="";
					
					$query="select "; //empieza la construcción de la busqueda dado un criterio
					for ($j=0;$j<$codsn;$j++)
					{
						$query=$query." ".$item2[$j]; // se usa el nombre del campo pasa por todos los campos seleccionados
						if($j==$codsn-1)
							$query=$query." "; //si es el ultimo campo va un espacio
						else
							$query=$query.","; // si no lo es separa con moma
					}
					echo "<select name='registro[".$i."]'>";
					/* $opciones[$i]= campo a relacionar por medio del criterio 
						$ini1+1= posicion despues del guion es decir donde empieza el nombre del campo
						substr($opciones[$i],$ini1+1,strlen($opciones[$i]))= nombre del campo   
						$criterio[$i]= criterio para hacer el query*/
					if($criterio[$i] != "@#$%^&*")
					{
						if(isset($DU))
						{
							$query =$query. " from ".$DU."_".$formulary." where medico='".$DU."' and ".substr($opciones[$i],$ini1+1,strlen($opciones[$i]))." like '%".$criterio[$i]."%'";
						}
						else
						{
							$query =$query. " from ".$pos1."_".$formulary." where medico='".$pos1."' and ".substr($opciones[$i],$ini1+1,strlen($opciones[$i]))." like '%".$criterio[$i]."%'";
							$query=$query." and (seguridad like 'A-%'  or seguridad like '%".$pos6."%')";
						}
						$err1 = mysql_query($query,$conex);
						$num1 = mysql_num_rows($err1);
						if($num1>0)
						{
							// Construir la selección
							for ($j=0;$j<$num1;$j++)
							{	
								$row1 = mysql_fetch_array($err1); // array con tantos elementos como campos se relacionen
								$conjunto="";
								for ($k=0;$k<$codsn;$k++)
								{
									$conjunto=$conjunto.$row1[$k];// se va pegando cada elmento del array para formar la linea con la info de la relación
									echo $row1[$k]."<br>";
									if($k==$codsn-1)
										$conjunto=$conjunto." "; // si es el ultimo se pone espacio
									else
										$conjunto=$conjunto."-"; // en caso contrario se pone guion
								}
								
								if ($registro[$i]==$conjunto)
									echo "<option selected>".$conjunto."</option>";  
								else
									echo "<option>".$conjunto."</option>";
							}
						}
						else
							echo "<option>".$registro[$i]."</option></select>";	// si el query no da resultados poner lo que habia 	
					$criterio[$i]="@#$%^&*";		
					}
					else
					{
						if($registro[$i] != "NO APLICA")
						{
							if(strpos($registro[$i],"-") !== false) 
								$registro[$i]=substr($registro[$i],0,strpos($registro[$i],"-")); 
							if(isset($DU))
								$query =$query. " from ".$DU."_".$formulary." where ".$item2[0]."='".$registro[$i]."'";
							else
								$query =$query. " from ".$pos1."_".$formulary." where ".$item2[0]."='".$registro[$i]."'";
							$err1 = mysql_query($query,$conex);
							$row1 = mysql_fetch_array($err1); // array con tantos elementos como campos se relacionen
							$conjunto="";
							for ($k=0;$k<$codsn;$k++)
							{
								$conjunto=$conjunto.$row1[$k]; // se va pegando cada elmento del array para formar la linea con la info de la relación
								if($k==$codsn-1)
									$conjunto=$conjunto." "; // si es el ultimo se pone espacio
								else
									$conjunto=$conjunto."-"; // en caso contrario se pone guion
							}
							$registro[$i]=$conjunto;
						}
						echo "<option>".$registro[$i]."</option></select>";
					}
				}
				echo "</td>";
				echo "<td bgcolor=#cccccc>";
				echo "<select name='opciones[".$i."]'>";  // crear la seleccion de campos con los que se puede relacionar
				for ($j=0;$j<$codsn;$j++)
				{
					echo "<option>".$item1[$j]."-".$item2[$j]."</option>";		// codigo numerico del campo - nombre del campo
				}	
				echo "<input type='TEXT' name='criterio[".$i."]' size=20 maxlength=80 value='".$criterio[$i]."'></td>"; // donde se envia el criterio de busqueda
				echo "</tr>";
				break;
				case 99: // seguridad
				if ($registro[$i]=="")
				{
					echo "<td bgcolor=#cccccc><input type='radio' name= 'registro[".$i."]' value='A'>Abierto - "; 
					echo "<input type='radio' name= 'registro[".$i."]' value='C' checked>Cerrado</td>"; 
				}
				else
					if($registro[$i]=="A")
					{
						echo "<td bgcolor=#cccccc><input type='radio' name= 'registro[".$i."]' value='A' checked>Abierto - "; 
						echo "<input type='radio' name= 'registro[".$i."]' value='C' >Cerrado</td>"; 
					}
					else
					{
						echo "<td bgcolor=#cccccc><input type='radio' name= 'registro[".$i."]' value='A' >Abierto - "; 
						echo "<input type='radio' name= 'registro[".$i."]' value='C' checked>Cerrado</td>"; 
					}
				echo "<td bgcolor=#cccccc>Usuarios del Grupo : ";
				$query = "select grupo from usuarios where codigo='".$pos1."' ";
				$err5 = mysql_query($query,$conex);
				$grupo = mysql_fetch_array($err5);
				$query = "select codigo from usuarios where grupo='".$grupo[0]."' and codigo != '".$pos6."' ";
				$err5 = mysql_query($query,$conex);
				$num5 = mysql_num_rows($err5);
				echo "<select name='Usuario'>";
				if(isset($Usuario) and $Usuario == "NO")
					echo "<option selected>NO</option>";
				else
					echo "<option>NO</option>";
				for ($j=0;$j<$num5;$j++)
				{	
					$row5 = mysql_fetch_array($err5);
					if(isset($Usuario) and $Usuario == $row5[0])
						echo "<option selected>".$row5[0]."</option>";
					else
						echo "<option>".$row5[0]."</option>";
				}
				echo "</td></tr>";
		}
	}
	echo "<tr>";
	echo "<td bgcolor=#cccccc>Datos Completos</td>";
	$val=$num;
	// envio de la informacion necesaria para regenerar el formulario despues del submit
	echo "<input type='HIDDEN' name= 'cardinal' value='".$val."'>";
	for ($w=0;$w<$val;$w++)
	{
		echo "<input type='HIDDEN' name= 'key' value='".$key."'>";
		echo "<input type='HIDDEN' name= 'tipos[".$w."]' value='".$tipos[$w]."'>";
		echo "<input type='HIDDEN' name= 'items[".$w."]' value='".$items[$w]."'>";
		echo "<input type='HIDDEN' name= 'tuplas[".$w."]' value='".$tuplas[$w]."'>";
		echo "<input type='HIDDEN' name= 'coments[".$w."]' value='".$coments[$w]."'>";
		echo "<input type='HIDDEN' name= 'proteccion[".$w."]' value='".$proteccion[$w]."'>";
	}
	// check box de datos completos
	if (!isset($registro[$val]))			
		echo "<td bgcolor=#cccccc><input type='checkbox' name='registro[".$val."]'></td>";
	else
		if ($registro[$val]=="on")
			echo "<td bgcolor=#cccccc><input type='checkbox' name='registro[".$val."]' checked></td>";
	echo "<td bgcolor=#cccccc></td>";
	echo "</tr>";
	echo "<tr>";
	echo "<td bgcolor=#cccccc colspan=3 align=center><input type='submit' value='GRABAR'></td>"; // submit
	echo "</tr>";
	echo "</tabla>";
	echo "<table border=0 align=center>";
	echo "<tr><td><A HREF='#Arriba'><B>Arriba</B></A></td></tr></table>";
	include_once("free.php");
?>
</body>
</html>
