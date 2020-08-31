<head>
  <title>LISTADO REPORTE DE HORAS</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<?php
include_once("conex.php");
  /***************************************************
	*	       LISTADO DEL REPORTE DE HORAS          *
	*	                PARA NOMINA                  *
	*				CONEX, FREE => OK				 *
	**************************************************/
session_start();
if(!isset($_SESSION['user']))
	echo "error";
else
{

	$key = substr($user,2,strlen($user));

	$conexunix = odbc_connect('nomina','informix','sco')
  					    or die("No se ralizo Conexion con el Unix");

	

	


	$wactualiz="(Versión Noviembre 16 de 2004)";

	echo "<form action='000003_rh02_1.php' method=post>";
    echo "<center><table border=2 width=400>";
    echo "<tr><td align=center colspan=300 bgcolor=#fffffff><font size=6 text color=#CC0000><b>CLINICA LAS AMERICAS</b></font></td></tr>";
    echo "<tr><td align=center colspan=300 bgcolor=#fffffff><font size=4 text color=#CC0000><b>LISTADO DEL REPORTE DE HORAS RESUMIDO</b></font></td></tr>";
    echo "<tr><td align=center colspan=300 bgcolor=#fffffff><font size=3 text color=#CC0000><b>".$wactualiz."</b></font></td></tr>";

	// COMPROBAR QUE LOS PARAMETROS ESTEN puestos(Año, Mes, Quincena)
	if(!isset($wano)  or !isset($wmes) or !isset($wqui) or !isset($wcco))
	  {
	    //AÑO
        echo "<td bgcolor=#cccccc ><font size=4><b>Año:</b></font><select name='wano'>";
        for($f=2004;$f<2051;$f++)
	       {
	        if($f == $wano)
	          echo "<option selected>".$f."</option>";
	         else
	            echo "<option>".$f."</option>";
	       }
		   echo "</select>";

		 //MES
	     echo "<td bgcolor=#cccccc ><font size=4><b>Mes :</b></font><select name='wmes'>";
	     for($f=1;$f<13;$f++)
	       {
	        if($f == $wmes)
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
		   echo "</select>";

	     //QUINCENA
	     echo "<td bgcolor=#cccccc ><font size=4><b>Quincena :</b></font><select name='wqui'>";
	     for($f=1;$f<3;$f++)
	       {
	        echo "<option>".$f."</option>";
	       }
		   echo "</td></select></td></tr>";


		 //Aca traigo el usuario, para poder saber ma adelante a que centro de costo pertenece
		 $pos = strpos($user,"-");
		 $wusuario = substr($user,$pos+1,strlen($user));

		 //Aca selecciono los empleados que pertenecen al centro de costo de acuerdo al centro de costo que tiene asignado el usuario
		 //autorizado para ingresar a este proceso.
		 $q = "         SELECT Carne_nomina ";
		 $q = $q."        FROM rephor_000001 ";
		 $q = $q."       WHERE Usuario_matrix = '".$wusuario."'";

		 $res = mysql_query($q,$conex);
         $row = mysql_fetch_array($res);

         if ($row[0] <> "" )   //Si es diferente de null, es porque el usuario esta autorizado a ingresar al proceso
	       {	//If de usuario autorizado a entrar
	         //Traigo el centro de costo del usuario autorizado, con el carne busco en Nomina (Unix)
	         //autorizado para ingresar a este proceso.
	         $q= "       SELECT percco ";
	         $q= $q."      FROM noper ";
	         $q= $q."     WHERE percod = '".$row[0]."'";
	         $q= $q."       AND peretr = 'A' ";       //si esta activo
             $res = odbc_do($conexunix,$q);

             if (odbc_result($res,1) <> "")
                $wcco = odbc_result($res,1);
	       }

	     if ($wusuario == "rephor")
	        {
		     //CENTROS DE COSTO
	         echo "<td bgcolor=#cccccc colspan = 3><font size=4><b>Centro de Costo :</b></font><select name='wcco'>";
	         $query = " SELECT ccocod, cconom "
                     ."   FROM cocco "
                     ."  ORDER BY ccocod ";

    	     $res = odbc_do($conexunix,$query);
    	     echo "<option selected>*- Todos los centros de costo </option>";
    	    }
    	   else
    	      {
		       //CENTROS DE COSTO
	           echo "<td bgcolor=#cccccc colspan = 3><font size=4><b>Centro de Costo :</b></font><select name='wcco'>";
	           $query = " SELECT ccocod, cconom "
                       ."   FROM cocco "
                       ."  WHERE ccocod = '".$wcco."'"
                       ."  ORDER BY ccocod ";

    	       $res = odbc_do($conexunix,$query);
    	      }

    	 ///echo "<option selected>*- Todos los centros de costo </option>";
	     while(odbc_fetch_row($res))
	         {
		      echo "<option value>".odbc_result($res,1)."-".odbc_result($res,2)."</option>";
	         }
	     echo "</SELECT></td></tr></table><br><br>";
	     //////// Aca se termina la captura de las variables


	     echo"<tr><td align=center bgcolor=#cccccc colspan=3 ><input type='submit' value='ACEPTAR'></td></tr></form>";
	  }
	else
	 /********************************
	  * TODOS LOS PARAMETROS ESTAN SET *
	  ********************************/
	  {

		echo "<tr><td align=center colspan=300 bgcolor=#fffffff><font size=4 text color=#CC0000><b>AÑO : ".$wano." - MES : ".$wmes." - QUINCENA : ".$wqui."</b></font></td></tr>";
		echo "<tr><td align=center colspan=300 bgcolor=#fffffff><font size=4 text color=#CC0000><b>CENTRO DE COSTO : ".$wcco." </b></font></td></tr>";
		$wwcco = substr($wcco,0,strpos($wcco,"-"));

		if ($wwcco == "*")
		    $wwcco = "";

	    /*query al reporte de horas*/
		$querya = " SELECT Cco, Empleado, substr(Tipo_hora_dia,1,instr(Tipo_hora_dia,'-')-1) AS conc, sum(Cantidad) AS cant "
		         ."   FROM rephor_000003 "
		         ."  WHERE Ano      = '".$wano."'"
		         ."    AND Mes      = '".$wmes."'"
		         ."    AND Quincena = '".$wqui."'"
		         ."    AND Cco      like '%".$wwcco."%'"
		         ."  GROUP BY Cco, Empleado, conc "
		         ."  ORDER BY Cco, Empleado ";

		$err = mysql_query($querya,$conex);
		$num = mysql_num_rows($err);

		if($num>0)  //Si hay empleados con hora reportadas
		  {
			//Aca traigo todos los conceptos que existen
		    $q="  SELECT subcodigo "
		      ."    FROM det_selecciones "
		      ."   WHERE lcase(medico) = 'rephor' "   //Nombre del usuario dueño de la seleccion
		      ."     AND codigo = '001' "             //Codigo de la seleccion en la tabla det_selecciones
		      ."   ORDER BY subcodigo ";
		    $res1 = mysql_query($q,$conex);
		    $num1 = mysql_num_rows($res1);

			echo "<th bgcolor=#00CCFF>C.Costo</th>";
			echo "<th bgcolor=#00CCFF>Codigo</th>";
			echo "<th bgcolor=#00CCFF>Nombre Empleado</th>";

			if ($num1 > 0)  //Si hay conceptos
			  {
				for ($l=0;$l<=$num1;$l++)  //For de los conceptos
			       {
				    $row = mysql_fetch_row($res1);
				    if ($row[0] <> "")
				       echo "<th bgcolor=#00CCFF>".$row[0]."</th>";
				    $conceptos[$l] = $row[0];
				    $indi = $l;
			       }
		      }

			//Inicializo la MATRIZ
		    for ($j=0;$j<=$num;$j++)
			   {
			    for ($l=0;$l<=$indi+4;$l++)
			       {
				    $matriz[$j][$l]=0;
			       }
		       }


			//For de la consulta de todas las horas digitadas
			$row = mysql_fetch_row($err);   //Esta es una fila de la consulta que tiene CCo, CodEmp, Concepto, Cantidad
			$fields=mysql_num_fields($err); //Aca segun el query el valor debe ser 4
			$l1=0;

			for ($l=0;$l<=$num;$l++)        //$num es el numero de registros que tiene el query
			   {
				$wcodemp = $row[1];

				echo "Empleado : ".$wcodemp."  row : ".$row[0].$row[1].$row[2].$row[3];

				while ($row[1]==$wcodemp and $l<=$num and $wcodemp <> "" and $row[1] <> "" )
				    {
					 $l1=($l1+1);
					 for ($j=0;$j<=$fields-2;$j++)                       //Aca es -2 porque empiezo desde 0 y mas adelante avanzo al otro campo forzado $row[$j+1]
					    {
						 if ($j==2)                                      //Campo correspondiente al concepto
					        {
						     for ($i=$j+1;$i<=$indi+2;$i++)
						        {
							     if ($conceptos[$i-3] == $row[$j])
								    {
									 $matriz[$l][$i] = $row[$j+1];       //Le paso la cantidad a la matriz
									}
							       else
							          {
								       if ($matriz[$l][$i] == "")
								         {
									      $matriz[$l][$i] = "-";
						                 }
						              }
					            }
					        }
					       else
					         {
						      //Traigo el nombre del codigo del empleado
						      if ($j==1)
					             {
						          //Traigo el nombre del empleado desde el UNIX
						          $q = "    SELECT perno1, perno2, perap1, perap2 "
						              ."     FROM noper "
						              ."    WHERE percod = '".$row[$j]."'";

	                              $res2 = odbc_do($conexunix,$q);

						          $nomemp = odbc_result($res2,1)." ".odbc_result($res2,2)." ".odbc_result($res2,3)." ".odbc_result($res2,4);

						          $matriz[$l][$j] = $row[$j];
						          $matriz[$l][$j+1] = $nomemp;
						         }
					            else
					               {
						            $matriz[$l][$j] = $row[$j];
				                   }
				             }
			            }
			         $row = mysql_fetch_row($err);                       //Avanzo un registro del query
			        }
	           }

	        for ($l=0;$l<=$l1;$l++)
			   {
				if ($matriz[$l][0] <> "0")
				  {
				   echo "<tr>";
				   for ($j=0;$j<=($fields+$indi-2);$j++)
				      {
				       if ($j < 3)
				          echo "<td ALIGN=LEFT>".$matriz[$l][$j]."</td>";
				         else
				            echo "<td ALIGN=RIGHT>".$matriz[$l][$j]."</td>";
			           }
				   echo "</tr>";
			      }
			   }
	      }
         else
            {
             echo "</table><br><br><br><TABLE><TR><TD><b><font size=5>NO EXISTEN DATOS EN MATRIX PARA LA QUINCENA DIGITADA</font></b></TD></TR></TABLE>";
             echo "<font size=3><A href=000003_rh01.php"."> Retornar</A></font>";
            }
       echo "<font size=3><A href=000003_rh02.php"."> Retornar</A></font>";
    }
	
	odbc_close($conexunix);
	odbc_close_all();
}
include_once("free.php");
?>