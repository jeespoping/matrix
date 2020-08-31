<html>
<head>
  <title>MATRIX</title>
</head>
<body BGCOLOR="">
<BODY TEXT="#000066">
<center>
<table border=0 align=center>
<tr><td align=center bgcolor="#cccccc"><A NAME="Arriba"><font size=5>Detalle de Formularios</font></a></tr></td>
<tr><td align=center bgcolor="#cccccc"><font size=2> <b> det_detform.php Ver. 2006-01-04</b></font></tr></td></table>
</center>
<?php
include_once("conex.php");
/**********************************************************************************************************************
     Programa :  det_detform.php
     Fecha de Liberación : 2003-09-30
     Realizado por : Pedro Ortiz Tamayo
     Version Actual : 2006-01-04

    OBJETIVO GENERAL : Este programa permite modificar la estructura de  un campo de un formulario, siempre y cuendo la tabla no haya sido
    generada. Si la tabla ya se genero permite modificar el campo de comentarios. trabaja con los mismos tipos de campos definidos en det_registro.
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
                        10 - Booleano
                        11 - Hora
                        12 - Algoritmico si la variable wswalg = on la salida es controlada x el usuario
                        13 - Titulo  *** No se Almacena en Matrix ***
                        14 - Hipervinculo
                        15 - Algoritmico_M (Modificable)
                        16 - Protegido o Password
                        17 - Auxiliar *** No se Almacena en Matrix *** Permite ejecucion de algoritmos com salida variable
                        18 - Relacion_NE Campos de relacion NO Especifica. Su funcion es identica ala campo de Relacion pero solo almacena la primera relacion


   REGISTRO DE MODIFICACIONES :

   .2006-01-04
        Se crea el tipo de Relacion_NE para otimizar la grabacion  de los campos relacionados con otros formularios. En estos campos solo se almacena
        el primer campo relacionado.

    .2003-09-30
        Ultima Modificacion Registrada.
***********************************************************************************************************************/
    echo "<form action='det_detform.php' method=post>";
    

    $superglobals = array($_SESSION,$_REQUEST);
	foreach ($superglobals as $keySuperglobals => $valueSuperglobals)
	{
		foreach ($valueSuperglobals as $variable => $dato)
		{
			$$variable = $dato; 
		}
	}

    if (isset($R[10]) and $R[10]=="on")
    {
        if (substr($R[7],0,1) == "A" or substr($R[7],0,1) == "P")
        {
            //**** VALIDACIONES ****
            $tiperr =0;
            switch ($wpar)
            {
                case 1:
                    if (strlen($R[3])==0 or strlen($R[4])==0)
                        $tiperr = 1;
                case 2:
                    if (strlen($R[1])==0 or strlen($R[2])==0 or strlen($R[3])==0 or strlen($R[4])==0)
                        $tiperr = 1;
            }
            if ($tiperr == 0)
            {
                $ini = strpos($R[1],"-");
                $ini1 = strpos($R[4],"-");
                $R[3] = strtolower($R[3]);
                $R[3] = ucwords($R[3]);
                $R[3] = str_replace(" ","_",$R[3]);
                switch ($wpar)
                {
                    case 1:
                    if ($wsw == 0)
                        $query = "update det_formulario set descripcion='".$R[3]."', tipo='".substr($R[4],0,$ini1)."', posicion=".$R[5].", comentarios='".$R[6]."', activo='".substr($R[7],0,1)."' where medico='".$R[0]."' and codigo='".substr($R[1],0,$ini)."' and campo='".$R[2]."'";
                    else
                        if (substr($R[7],0,1) == "A" or substr($R[7],0,1) == "P")
                            $query = "update det_formulario set comentarios='".$R[6]."', activo='".substr($R[7],0,1)."' where medico='".$R[0]."' and codigo='".substr($R[1],0,$ini)."' and campo='".$R[2]."'";
                        else
                            $query = "update det_formulario set comentarios='".$R[6]."' where medico='".$R[0]."' and codigo='".substr($R[1],0,$ini)."' and campo='".$R[2]."'";
                    $err = mysql_query($query,$conex);
                    break;
                    case 2:
                    if ($totalc < 10000)
                    {
                        $query = "insert into det_formulario (medico,codigo,campo,descripcion,tipo,posicion,comentarios,activo) values ('".$R[0]."','".substr($R[1],0,$ini)."','".$R[2]."','".$R[3]."','".substr($R[4],0,$ini1)."',".$R[5].",'".$R[6]."','".substr($R[7],0,1)."')";
                        $err = mysql_query($query,$conex);
                        if ($err != 1)
                        {
                            echo "<center><table border=0 aling=center>";
                            echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
                            echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>YA EXISTE EL CODIGO DEL CAMPO -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
                            echo "<br><br>";
                        }
                        else
                            $call = 0;
                    }
                    else
                    {
                        echo "<center><table border=0 aling=center>";
                        echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
                        echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#33FFFF LOOP=-1>ESTE FORMULARIO EXCEDE EL NUMERO DE CAMPOS PERMITIDOS DE 9999 !!!!</MARQUEE></FONT>";
                        echo "<br><br>";
                    }
                }
            }
            else
            {
                echo "<center><table border=0 aling=center>";
                echo "<tr><td><IMG SRC='/matrix/images/medical/root/cabeza.gif' ></td><tr></table></center>";
                switch ($tiperr)
                {
                    case 1:
                    echo "<font size=3><MARQUEE BEHAVIOR=SCROLL BGCOLOR=#CCCCFF LOOP=-1>LOS DATOS ESTAN INCOMPLETOS -- INTENTELO NUEVAMENTE!!!!</MARQUEE></FONT>";
                    echo "<br><br>";
                    break;
                }
            }
        }
        else
        {
            $ini = strpos($R[1],"-");
            $query = "delete from det_formulario where medico='".$R[0]."' and codigo='".substr($R[1],0,$ini)."' and campo='".$R[2]."'";
            $err = mysql_query($query,$conex);
            $call = 0;
            $delete=1;
        }
        $ini = strpos($R[1],"-");
        $query = "select * from det_formulario where medico='".$R[0]."' and codigo='".substr($R[1],0,$ini)."' order by posicion,id";
        $err = mysql_query($query,$conex);
        $num = mysql_num_rows($err);
        $wcampo=9999;
        for ($i=0;$i<$num;$i++)
        {
            $posi=$i+1;
            $row = mysql_fetch_array($err);
            $wcampos=(string)($wcampo-$i);
            $query =  "update det_formulario set campo='".$wcampos."',posicion=".$posi." where medico='".$R[0]."' and codigo='".substr($R[1],0,$ini)."' and campo='".$row[2]."'";
            $err1 = mysql_query($query,$conex);
        }
        $query = "select * from det_formulario where medico='".$R[0]."' and codigo='".substr($R[1],0,$ini)."' order by posicion,id";
        $err = mysql_query($query,$conex);
        $num = mysql_num_rows($err);
        for ($i=0;$i<$num;$i++)
        {
            $row = mysql_fetch_array($err);
            $wcampos=(string)((integer)$row[5]);
            while (strlen($wcampos) < 4)
                $wcampos="0".$wcampos;
            $query =  "update det_formulario set campo='".$wcampos."' where medico='".$R[0]."' and codigo='".substr($R[1],0,$ini)."' and campo='".$row[2]."'";
            $err1 = mysql_query($query,$conex);
        }
    }
    switch ($call)
    {
        case 0:
        for ($i=1;$i<7;$i++)
        {
            $R[$i] = "";
        }
        $R[0] = $pos1;
        $R[1] = $pos2;
        if(!isset($delete))
            $R[2] = $pos3;
        else
            $delete=0;
        break;
        case 1:
        $R=array();
        $R[0] = $pos1;
        $R[1] = $pos2;
        $R[2] = $pos3;
        break;
    }
    if (strlen($R[1])>6)
        $ini = strpos($R[1],"-");
    else
        $ini = strlen($R[1]);
    $query = "select * from det_formulario where medico='".$R[0]."' and codigo='".substr($R[1],0,$ini)."' order by posicion";
    $err = mysql_query($query,$conex);
    $num = mysql_num_rows($err);
    $totalc =$num;
    for ($i=0;$i<$num;$i++)
    {
        $row = mysql_fetch_array($err);
    }
    if($num>0)
    {
        echo "<li>Ultimo Campo Grabado : ".$row[3];
        echo "<li>Numero de Campos Actuales : ".$num;
    }
    $query = "select * from det_formulario where medico='".$R[0]."' and codigo='".substr($R[1],0,$ini)."' and campo='".$R[2]."'";
    $err = mysql_query($query,$conex);
    $num = mysql_num_rows($err);
    $row = mysql_fetch_array($err);
    echo "<li><A HREF='detform.php?Form=".$pos2."' target='main'>Retornar</A>";
    echo "<br><br>";
    echo "<table border=0 align=center>";
    if ($num > 0)
    {
        for ($i=0;$i<8;$i++)
        {
            if (!isset($R[$i]))
                $R[$i] = $row[$i];
        }
        if (strlen($R[1])>6)
            $ini = strpos($R[1],"-");
        else
            $ini = strlen($R[1]);
        echo "<tr>";
        echo "<td bgcolor=#999999><b>Item</td></b>";
        echo "<td bgcolor=#999999><b>Valor</b></td>";
        echo "</tr>";
        echo "<tr>";
        $query = "select * from formulario where medico='".$R[0]."' and activo='A' and codigo='".substr($R[1],0,$ini)."'";
        $err1 = mysql_query($query,$conex);
        $num1 = mysql_num_rows($err1);
        $row1 = mysql_fetch_array($err1);
        echo "<input type='HIDDEN' name= 'pos1' value='".$pos1."'>";
        echo "<input type='HIDDEN' name= 'pos2' value='".$pos2."'>";
        echo "<input type='HIDDEN' name= 'pos3' value='".$pos3."'>";
        echo "<input type='HIDDEN' name= 'wsw' value='".$wsw."'>";
        echo "<td bgcolor=#cccccc>Formulario</td>";
        echo "<td bgcolor=#cccccc>".$row1[1]."-".$row1[2]."</td>";
        $val=1;
        echo "<input type='HIDDEN' name= 'R[".$val."]' value='".$row1[1]."-".$row1[2]."'>";
        $val=2;
        echo "<input type='HIDDEN' name= 'R[".$val."]' value='".$R[2]."'>";
        $val=0;
        echo "<input type='HIDDEN' name= 'R[".$val."]' value='".$R[0]."'>";
        echo "</tr>";
        echo "<tr>";
        echo "<td bgcolor=#cccccc>Campo</td>";
        echo "<td bgcolor=#cccccc>".$R[2]."</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td bgcolor=#cccccc>Descripcion</td>";
        $val=3;
        echo "<td bgcolor=#cccccc><input type='TEXT' name='R[".$val."]' size=50 maxlength=50 value='".$R[3]."'></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td bgcolor=#cccccc>Tipo</td>";
        echo "<td bgcolor=#cccccc>";
        $val=4;
        if (strlen($R[4])>6)
            $ini = strpos($R[4],"-");
        else
            $ini = strlen($R[4]);
        echo "<select name='R[".$val."]'>";
        if (substr($R[4],0,$ini) == substr("0-Caracteres", 0, 1))
            echo "<option selected>0-Caracteres</option>";
        else
            echo "<option>0-Caracteres</option>";
        if (substr($R[4],0,$ini) == substr("1-Entero", 0, 1))
            echo "<option selected>1-Entero</option>";
        else
            echo "<option>1-Entero</option>";
        if (substr($R[4],0,$ini) == substr("2-Real", 0, 1))
            echo "<option selected>2-Real</option>";
        else
            echo "<option>2-Real</option>";
        if (substr($R[4],0,$ini) == substr("3-Fecha", 0, 1))
            echo "<option selected>3-Fecha</option>";
        else
            echo "<option>3-Fecha</option>";
        if (substr($R[4],0,$ini) == substr("4-Texto", 0, 1))
            echo "<option selected>4-Texto</option>";
        else
            echo "<option>4-Texto</option>";
        if (substr($R[4],0,$ini) == substr("5-Seleccion", 0, 1))
            echo "<option selected>5-Seleccion</option>";
        else
            echo "<option>5-Seleccion</option>";
        if (substr($R[4],0,$ini) == substr("6-Formula", 0, 1))
            echo "<option selected>6-Formula</option>";
        else
            echo "<option>6-Formula</option>";
        if (substr($R[4],0,$ini) == substr("7-Grafico", 0, 1))
            echo "<option selected>7-Grafico</option>";
        else
            echo "<option>7-Grafico</option>";
        if (substr($R[4],0,$ini) == substr("8-Automatico", 0, 1))
            echo "<option selected>8-Automatico</option>";
        else
            echo "<option>8-Automatico</option>";
        if (substr($R[4],0,$ini) == substr("9-Relacion", 0, 1))
            echo "<option selected>9-Relacion</option>";
        else
            echo "<option>9-Relacion</option>";
        if (substr($R[4],0,$ini) == substr("10-Booleano", 0, 2))
            echo "<option selected>10-Booleano</option>";
        else
            echo "<option>10-Booleano</option>";
        if (substr($R[4],0,$ini) == substr("11-Hora", 0, 2))
            echo "<option selected>11-Hora</option>";
        else
            echo "<option>11-Hora</option>";
        if (substr($R[4],0,$ini) == substr("12-Algoritmico", 0, 2))
            echo "<option selected>12-Algoritmico</option>";
        else
            echo "<option>12-Algoritmico</option>";
        if (substr($R[4],0,$ini) == substr("13-Titulo", 0, 2))
            echo "<option selected>13-Titulo</option>";
        else
            echo "<option>13-Titulo</option>";
        if (substr($R[4],0,$ini) == substr("14-Hipervinculo", 0, 2))
            echo "<option selected>14-Hipervinculo</option>";
        else
            echo "<option>14-Hipervinculo</option>";
        if (substr($R[4],0,$ini) == substr("15-Algoritmico_M", 0, 2))
            echo "<option selected>15-Algoritmico_M</option>";
        else
            echo "<option>15-Algoritmico_M</option>";
        if (substr($R[4],0,$ini) == substr("16-Protegido", 0, 2))
            echo "<option selected>16-Protegido</option>";
        else
            echo "<option>16-Protegido</option>";
        if (substr($R[4],0,$ini) == substr("17-Auxiliar", 0, 2))
            echo "<option selected>17-Auxiliar</option>";
        else
            echo "<option>17-Auxiliar</option>";
        if (substr($R[4],0,$ini) == substr("18-Relacion_NE", 0, 2))
            echo "<option selected>18-Relacion_NE</option>";
        else
            echo "<option>18-Relacion_NE</option>";
        echo "</select>";
        echo "<select name='formulas'>";
        echo "<option>NO</option>";
        echo "<option>sin--</option>";
        echo "<option>cos--</option>";
        echo "<option>tan--</option>";
        echo "<option>atan-</option>";
        echo "<option>log10</option>";
        echo "<option>log--</option>";
        echo "<option>exp--</option>";
        echo "<option>abs--</option>";
        echo "<option>sqrt-</option>";
        echo "<option>pow--</option>";
        echo "<option>pi---</option>";
        echo "</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td bgcolor=#cccccc>Posicion</td>";
        $val=5;
        echo "<td bgcolor=#cccccc><input type='TEXT' name='R[".$val."]' size=5 maxlength=5 value='".$R[5]."'></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td bgcolor=#cccccc>Seleccion Asociada</td>";
        echo "<td bgcolor=#cccccc>";
        $val=8;
        echo "<select name='R[".$val."]'>";
        $query = "select * from selecciones where medico='".$R[0]."' and activo='A'";
        $err1 = mysql_query($query,$conex);
        $num1 = mysql_num_rows($err1);
        for ($i=0;$i<$num1;$i++)
        {
            $row1 = mysql_fetch_array($err1);
            echo "<option>".$row1[1]."-".$row1[2]."</option>";
        }
        echo "</tr>";
        echo "<tr>";
        echo "<td bgcolor=#cccccc>Formulario-Campo Asociado</td>";
        echo "<td bgcolor=#cccccc>";
        $val=9;
        echo "<select name='R[".$val."]'>";
        $query = "select formulario.medico,formulario.codigo,formulario.nombre,det_formulario.campo,det_formulario.descripcion from formulario,det_formulario where (formulario.medico='".$R[0]."' or formulario.tipo='A') and formulario.activo='A' and formulario.medico=det_formulario.medico and formulario.codigo=det_formulario.codigo order by  formulario.tipo desc,det_formulario.codigo,det_formulario.campo";
        $err1 = mysql_query($query,$conex);
        $num1 = mysql_num_rows($err1);
        for ($i=0;$i<$num1;$i++)
        {
            $row1 = mysql_fetch_array($err1);
            echo "<option>".$row1[0]."-".$row1[1]."-".$row1[2]."-".$row1[3]."-".$row1[4]."</option>";
        }
        echo "</tr>";
        echo "<tr>";
        $val=6;
        if (isset($R[4]))
        {
            $ini = strpos($R[4],"-");
            if(substr($R[4],0,$ini)=="6" or substr($R[4],0,$ini)=="9" or substr($R[4],0,$ini)=="18")
                switch (substr($R[4],0,$ini))
                {
                    case "6":
                        if(isset($formulas) and $formulas=="NO")
                        {
                            $lin= strpos($R[1],"-");
                            $lin2=strrpos($R[9],"-");
                            if(substr($R[9],0,6) ==substr($R[1],0,$lin))
                                $R[6]=$R[6]."$".substr($R[9],$lin2-4,4);
                        }
                        else
                            $R[6]=$R[6].$formulas;
                    break;
                    case "9":
                    if (strlen($R[6])>0)
                    {
                        $lin2=strpos($R[9],"-");
                        $owner1=substr($R[9],0,$lin2);
                        $R[9]=substr($R[9],$lin2+1);
                        $lin= strpos($R[6],"-");
                        if(!is_numeric(substr($R[6],$lin+1,1)))
                        {
                            $o=substr($R[6],$lin+1);
                            $lin3= strpos($o,"-");
                            $owner2=substr($o,0,$lin3);
                            $inc=2;
                        }
                        else
                        {
                            $owner2="";
                            $lin3=0;
                            $inc=1;
                        }
                        if(substr($R[9],0,6) == substr($R[6],$lin+$lin3+$inc,6) and ($owner2==$owner1 or ($owner2=="" and $owner1==$pos1)))
                        {
                            $lin2=strrpos($R[9],"-");
                            $R[6]=(string)((integer)substr($R[6],0,$lin)+1)."-".substr($R[6],$lin+1,strlen($R[6]))."-".substr($R[9],$lin2-4,4);
                        }
                    }
                    else
                    {
                        $lin2=strpos($R[9],"-");
                        $owner=substr($R[9],0,$lin2);
                        $R[9]=substr($R[9],$lin2+1);
                        $lin2=strrpos($R[9],"-");
                        if ($owner != $pos1)
                            $R[6]="1-".$owner."-".substr($R[9],0,6)."-".substr($R[9],$lin2-4,4);
                        else
                            $R[6]="1-".substr($R[9],0,6)."-".substr($R[9],$lin2-4,4);
                    }
                    break;
                    case "18":
                    if (strlen($R[6])>0)
                    {
                        $lin2=strpos($R[9],"-");
                        $owner1=substr($R[9],0,$lin2);
                        $R[9]=substr($R[9],$lin2+1);
                        $lin= strpos($R[6],"-");
                        if(!is_numeric(substr($R[6],$lin+1,1)))
                        {
                            $o=substr($R[6],$lin+1);
                            $lin3= strpos($o,"-");
                            $owner2=substr($o,0,$lin3);
                            $inc=2;
                        }
                        else
                        {
                            $owner2="";
                            $lin3=0;
                            $inc=1;
                        }
                        if(substr($R[9],0,6) == substr($R[6],$lin+$lin3+$inc,6) and ($owner2==$owner1 or ($owner2=="" and $owner1==$pos1)))
                        {
                            $lin2=strrpos($R[9],"-");
                            $R[6]=(string)((integer)substr($R[6],0,$lin)+1)."-".substr($R[6],$lin+1,strlen($R[6]))."-".substr($R[9],$lin2-4,4);
                        }
                    }
                    else
                    {
                        $lin2=strpos($R[9],"-");
                        $owner=substr($R[9],0,$lin2);
                        $R[9]=substr($R[9],$lin2+1);
                        $lin2=strrpos($R[9],"-");
                        if ($owner != $pos1)
                            $R[6]="1-".$owner."-".substr($R[9],0,6)."-".substr($R[9],$lin2-4,4);
                        else
                            $R[6]="1-".substr($R[9],0,6)."-".substr($R[9],$lin2-4,4);
                    }
                    break;
                }
            if((substr($R[4],0,$ini)=="4" or substr($R[4],0,$ini)=="5") and strlen($R[6])==0 and isset($R[8]))
                $R[6]=$R[6].$R[8];
            echo "<td bgcolor=#cccccc>Comentarios</td>";
            echo "<td bgcolor=#cccccc><textarea name='R[".$val."]' cols=60 rows=5>".$R[6]."</textarea>";
        }
        else
        {
            echo "<td bgcolor=#cccccc>Comentarios</td>";
            echo "<td bgcolor=#cccccc><textarea name='R[".$val."]' cols=60 rows=5>".$row[6]."</textarea>";
        }
        echo "</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td bgcolor=#cccccc>Activo</td>";
        echo "<td bgcolor=#cccccc>";
        $val=7;
        if (strlen($R[7]) > 1)
            $R[7]=substr($R[7],0,1);
        echo "<select name='R[".$val."]'>";
        if ($R[7] == substr("A-Activo",0,1))
            echo "<option selected>A-Activo</option>";
        else
            echo "<option>A-Activo</option>";
        if ($R[7] == substr("I-Inactivo",0,1))
            echo "<option selected>I-Inactivo</option>";
        else
            echo "<option>I-Inactivo</option>";
        if ($R[7] == substr("P-Protegido",0,1))
            echo "<option selected>P-Protegido</option>";
        else
            echo "<option>P-Protegido</option>";
        echo "</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td bgcolor=#cccccc>Datos Completos</td>";
        $val=10;
        if (!isset($R[10]))
            echo "<td bgcolor=#cccccc><input type='checkbox' name='R[".$val."]'></td>";
        elseif ($R[10]=="on")
                echo "<td bgcolor=#cccccc><input type='checkbox' name='R[".$val."]' checked></td>";
            else
                echo "<td bgcolor=#cccccc><input type='checkbox' name='R[".$val."]'></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td bgcolor=#cccccc colspan=2 align=center><input type='submit' value='GRABAR'></td>";
        echo "<input type='HIDDEN' name= 'wpar' value='1'>";
        echo "<input type='HIDDEN' name= 'call' value='2'>";
        echo "</tr>";
        echo "</tabla>";
    }
    else
    {
        echo "<tr>";
        echo "<td bgcolor=#999999><b>Item</td></b>";
        echo "<td bgcolor=#999999><b>Valor</b></td>";
        echo "</tr>";
        echo "<tr>";
        $val=0;
        echo "<input type='HIDDEN' name= 'R[".$val."]' value='".$R[0]."'>";
        echo "<input type='HIDDEN' name= 'call' value='2'>";
        echo "<input type='HIDDEN' name= 'pos1' value='".$pos1."'>";
        echo "<input type='HIDDEN' name= 'pos2' value='".$pos2."'>";
        echo "<input type='HIDDEN' name= 'pos3' value='".$pos3."'>";
        $query = "select * from formulario where medico='".$R[0]."' and activo='A' and codigo='".substr($R[1],0,$ini)."'";
        $err1 = mysql_query($query,$conex);
        $num1 = mysql_num_rows($err1);
        $row1 = mysql_fetch_array($err1);
        echo "<td bgcolor=#cccccc>Formulario</td>";
        echo "<td bgcolor=#cccccc>".$row1[1]."-".$row1[2]."</td>";
        $val=1;
        echo "<input type='HIDDEN' name= 'R[".$val."]' value='".$row1[1]."-".$row1[2]."'>";
        echo "</tr>";
        echo "<tr>";
        echo "<td bgcolor=#cccccc>Campo</td>";
        $val=2;
        $totalc++;
        $R[2]=$totalc;
        while(strlen($R[2]) <4)
            $R[2]="0".$R[2];
        echo "<td bgcolor=#cccccc>".$R[2]."</td>";
        echo "<input type='HIDDEN' name= 'R[".$val."]' value='".$R[2]."'>";
        echo "<input type='HIDDEN' name= 'totalc' value='".$totalc."'>";
        echo "</tr>";
        echo "<tr>";
        echo "<td bgcolor=#cccccc>Descripcion</td>";
        $val=3;
        if (!isset($R[3]))
            echo "<td bgcolor=#cccccc><input type='TEXT' name='R[".$val."]' size=50 maxlength=50></td>";
        else
            echo "<td bgcolor=#cccccc><input type='TEXT' name='R[".$val."]' size=50 maxlength=50 value='".$R[3]."'></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td bgcolor=#cccccc>Tipo</td>";
        echo "<td bgcolor=#cccccc>";
        $val=4;
        echo "<select name='R[".$val."]'>";
        if (isset($R[4]))
        {
            $ini = strpos($R[4],"-");
            if(substr($R[4],0,$ini)=="0")
                echo "<option selected>0-Caracteres</option>";
            else
                echo "<option>0-Caracteres</option>";
            if(substr($R[4],0,$ini)=="1")
                echo "<option selected>1-Entero</option>";
            else
                echo "<option>1-Entero</option>";
            if(substr($R[4],0,$ini)=="2")
                echo "<option selected>2-Real</option>";
            else
                echo "<option>2-Real</option>";
            if(substr($R[4],0,$ini)=="3")
                echo "<option selected>3-Fecha</option>";
            else
                echo "<option>3-Fecha</option>";
            if(substr($R[4],0,$ini)=="4")
                echo "<option selected>4-Texto</option>";
            else
                echo "<option>4-Texto</option>";
            if(substr($R[4],0,$ini)=="5")
                echo "<option selected>5-Seleccion</option>";
            else
                echo "<option>5-Seleccion</option>";
            if(substr($R[4],0,$ini)=="6")
                echo "<option selected>6-Formula</option>";
            else
                echo "<option>6-Formula</option>";
            if(substr($R[4],0,$ini)=="7")
                echo "<option selected>7-Grafico</option>";
            else
                echo "<option>7-Grafico</option>";
            if(substr($R[4],0,$ini)=="8")
                echo "<option selected>8-Automatico</option>";
            else
                echo "<option>8-Automatico</option>";
            if(substr($R[4],0,$ini)=="9")
                echo "<option selected>9-Relacion</option>";
            else
                echo "<option>9-Relacion</option>";
            if (substr($R[4],0,$ini) == substr("10-Booleano", 0, 2))
                echo "<option selected>10-Booleano</option>";
            else
                echo "<option>10-Booleano</option>";
            if (substr($R[4],0,$ini) == substr("11-Hora", 0, 2))
                echo "<option selected>11-Hora</option>";
            else
                echo "<option>11-Hora</option>";
            if (substr($R[4],0,$ini) == substr("12-Algoritmico", 0, 2))
                echo "<option selected>12-Algoritmico</option>";
            else
                echo "<option>12-Algoritmico</option>";
            if (substr($R[4],0,$ini) == substr("13-Titulo", 0, 2))
                echo "<option selected>13-Titulo</option>";
            else
                echo "<option>13-Titulo</option>";
            if (substr($R[4],0,$ini) == substr("14-Hipervinculo", 0, 2))
                echo "<option selected>14-Hipervinculo</option>";
            else
                echo "<option>14-Hipervinculo</option>";
            if (substr($R[4],0,$ini) == substr("15-Algoritmico_M", 0, 2))
                echo "<option selected>15-Algoritmico_M</option>";
            else
                echo "<option>15-Algoritmico_M</option>";
            if (substr($R[4],0,$ini) == substr("16-Protegido", 0, 2))
                echo "<option selected>16-Protegido</option>";
            else
                echo "<option>16-Protegido</option>";
            if (substr($R[4],0,$ini) == substr("17-Auxiliar", 0, 2))
                echo "<option selected>17-Auxiliar</option>";
            else
                echo "<option>17-Auxiliar</option>";
            if(substr($R[4],0,$ini)== substr("18-Relacion_NE", 0, 2))
                echo "<option selected>18-Relacion_NE</option>";
            else
                echo "<option>18-Relacion_NE</option>";
        }
        else
        {
            echo "<option>0-Caracteres</option>";
            echo "<option>1-Entero</option>";
            echo "<option>2-Real</option>";
            echo "<option>3-Fecha</option>";
            echo "<option>4-Texto</option>";
            echo "<option>5-Seleccion</option>";
            echo "<option>6-Formula</option>";
            echo "<option>7-Grafico</option>";
            echo "<option>8-Automatico</option>";
            echo "<option>9-Relacion</option>";
            echo "<option>10-Booleano</option>";
            echo "<option>11-Hora</option>";
            echo "<option>12-Algoritmico</option>";
            echo "<option>13-Titulo</option>";
            echo "<option>14-Hipervinculo</option>";
            echo "<option>15-Algoritmico_M</option>";
            echo "<option>16-Protegido</option>";
            echo "<option>17-Auxiliar</option>";
            echo "<option>18-Relacion_NE</option>";
        }
        echo "</select>";
        echo "<select name='formulas'>";
        echo "<option>NO</option>";
        echo "<option>sin--</option>";
        echo "<option>cos--</option>";
        echo "<option>tan--</option>";
        echo "<option>atan-</option>";
        echo "<option>log10</option>";
        echo "<option>log--</option>";
        echo "<option>exp--</option>";
        echo "<option>abs--</option>";
        echo "<option>sqrt-</option>";
        echo "<option>pow--</option>";
        echo "<option>pi---</option>";
        echo "</td>";
        echo "</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td bgcolor=#cccccc>Posicion</td>";
        $val=5;
        if (!isset($R[5]))
            echo "<td bgcolor=#cccccc><input type='TEXT' name='R[".$val."]' size=5 maxlength=5></td>";
        else
            echo "<td bgcolor=#cccccc><input type='TEXT' name='R[".$val."]' size=5 maxlength=5 value='".$R[5]."'></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td bgcolor=#cccccc>Seleccion Asociada</td>";
        echo "<td bgcolor=#cccccc>";
        $val=8;
        echo "<select name='R[".$val."]'>";
        $query = "select * from selecciones where medico='".$R[0]."' and activo='A'";
        $err1 = mysql_query($query,$conex);
        $num1 = mysql_num_rows($err1);
        for ($i=0;$i<$num1;$i++)
        {
            $row1 = mysql_fetch_array($err1);
            echo "<option>".$row1[1]."-".$row1[2]."</option>";
        }
        echo "</tr>";
        echo "<tr>";
        echo "<td bgcolor=#cccccc>Formulario-Campo Asociado</td>";
        echo "<td bgcolor=#cccccc>";
        $val=9;
        echo "<select name='R[".$val."]'>";
        $query = "select formulario.medico,formulario.codigo,formulario.nombre,det_formulario.campo,det_formulario.descripcion from formulario,det_formulario where  (formulario.medico='".$R[0]."' or formulario.tipo='A') and formulario.activo='A' and formulario.medico=det_formulario.medico and formulario.codigo=det_formulario.codigo order by  formulario.tipo desc,det_formulario.codigo,det_formulario.campo";
        $err1 = mysql_query($query,$conex);
        $num1 = mysql_num_rows($err1);
        for ($i=0;$i<$num1;$i++)
        {
            $row1 = mysql_fetch_array($err1);
            echo "<option>".$row1[0]."-".$row1[1]."-".$row1[2]."-".$row1[3]."-".$row1[4]."</option>";
        }
        echo "</tr>";
        echo "<tr>";
        $val=6;
        if (isset($R[4]))
        {
            $ini = strpos($R[4],"-");
            if(substr($R[4],0,$ini)=="6" or substr($R[4],0,$ini)=="9" or substr($R[4],0,$ini)=="18")
                switch (substr($R[4],0,$ini))
                {
                    case "6":
                    if(isset($formulas) and $formulas=="NO")
                    {
                        $lin= strpos($R[1],"-");
                        $lin2=strrpos($R[9],"-");
                        if(substr($R[9],0,6) ==substr($R[1],0,$lin))
                            $R[6]=$R[6]."$".substr($R[9],$lin2-4,4);
                    }
                    else
                        $R[6]=$R[6].$formulas;
                    break;
                    case "9":
                    if (strlen($R[6])>0)
                    {
                        $lin2=strpos($R[9],"-");
                        $owner1=substr($R[9],0,$lin2);
                        $R[9]=substr($R[9],$lin2+1);
                        $lin= strpos($R[6],"-");
                        if(!is_numeric(substr($R[6],$lin+1,1)))
                        {
                            $o=substr($R[6],$lin+1);
                            $lin3= strpos($o,"-");
                            $owner2=substr($o,0,$lin3);
                            $inc=2;
                        }
                        else
                        {
                            $owner2="";
                            $lin3=0;
                            $inc=1;
                        }
                        if(substr($R[9],0,6) == substr($R[6],$lin+$lin3+$inc,6) and ($owner2==$owner1 or ($owner2=="" and $owner1==$pos1)))
                        {
                            $lin2=strrpos($R[9],"-");
                            $R[6]=(string)((integer)substr($R[6],0,$lin)+1)."-".substr($R[6],$lin+1,strlen($R[6]))."-".substr($R[9],$lin2-4,4);
                        }
                    }
                    else
                    {
                        $lin2=strpos($R[9],"-");
                        $owner=substr($R[9],0,$lin2);
                        $R[9]=substr($R[9],$lin2+1);
                        $lin2=strrpos($R[9],"-");
                        if ($owner != $pos1)
                            $R[6]="1-".$owner."-".substr($R[9],0,6)."-".substr($R[9],$lin2-4,4);
                        else
                            $R[6]="1-".substr($R[9],0,6)."-".substr($R[9],$lin2-4,4);
                    }
                    break;
                    case "18":
                    if (strlen($R[6])>0)
                    {
                        $lin2=strpos($R[9],"-");
                        $owner1=substr($R[9],0,$lin2);
                        $R[9]=substr($R[9],$lin2+1);
                        $lin= strpos($R[6],"-");
                        if(!is_numeric(substr($R[6],$lin+1,1)))
                        {
                            $o=substr($R[6],$lin+1);
                            $lin3= strpos($o,"-");
                            $owner2=substr($o,0,$lin3);
                            $inc=2;
                        }
                        else
                        {
                            $owner2="";
                            $lin3=0;
                            $inc=1;
                        }
                        if(substr($R[9],0,6) == substr($R[6],$lin+$lin3+$inc,6) and ($owner2==$owner1 or ($owner2=="" and $owner1==$pos1)))
                        {
                            $lin2=strrpos($R[9],"-");
                            $R[6]=(string)((integer)substr($R[6],0,$lin)+1)."-".substr($R[6],$lin+1,strlen($R[6]))."-".substr($R[9],$lin2-4,4);
                        }
                    }
                    else
                    {
                        $lin2=strpos($R[9],"-");
                        $owner=substr($R[9],0,$lin2);
                        $R[9]=substr($R[9],$lin2+1);
                        $lin2=strrpos($R[9],"-");
                        if ($owner != $pos1)
                            $R[6]="1-".$owner."-".substr($R[9],0,6)."-".substr($R[9],$lin2-4,4);
                        else
                            $R[6]="1-".substr($R[9],0,6)."-".substr($R[9],$lin2-4,4);
                    }
                    break;
                }
            if((substr($R[4],0,$ini)=="4" or substr($R[4],0,$ini)=="5") and strlen($R[6])==0)
                $R[6]=$R[6].$R[8];
            echo "<td bgcolor=#cccccc>Comentarios</td>";
            echo "<td bgcolor=#cccccc><textarea name='R[".$val."]' cols=60 rows=5>".$R[6]."</textarea>";
        }
        else
        {
            echo "<td bgcolor=#cccccc>Comentarios</td>";
            echo "<td bgcolor=#cccccc><textarea name='R[".$val."]' cols=60 rows=5></textarea>";
        }
        echo "</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td bgcolor=#cccccc>Activo</td>";
        echo "<td bgcolor=#cccccc>";
        $val=7;
        echo "<select name='R[".$val."]'>";
        if (isset($R[7]))
        {
            if(substr($R[7],0,$ini)=="A")
                echo "<option selected>A-Activo</option>";
            else
                echo "<option>A-Activo</option>";
            if(substr($R[7],0,$ini)=="I")
                echo "<option selected>I-Inactivo</option>";
            else
                echo "<option>I-Inactivo</option>";
            if(substr($R[7],0,$ini)=="P")
                echo "<option selected>P-Protegido</option>";
            else
                echo "<option>P-Protegido</option>";
        }
        else
        {
            echo "<option>A-Activo</option>";
            echo "<option>I-Inactivo</option>";
            echo "<option>P-Protegido</option>";
        }
        echo "</td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td bgcolor=#cccccc>Datos Completos</td>";
        $val=10;
        if (!isset($R[10]))
            echo "<td bgcolor=#cccccc><input type='checkbox' name='R[".$val."]'></td>";
        elseif ($R[10]=="on")
                echo "<td bgcolor=#cccccc><input type='checkbox' name='R[".$val."]' checked></td>";
            else
                echo "<td bgcolor=#cccccc><input type='checkbox' name='R[".$val."]'></td>";
        echo "</tr>";
        echo "<tr>";
        echo "<td bgcolor=#cccccc colspan=2 align=center><input type='submit' value='GRABAR'></td>";
        echo "<input type='HIDDEN' name= 'wpar' value='2'>";
        echo "</tr>";
        echo "</tabla>";
    }
    echo "</tabla>";
    echo "<table border=0 align=center>";
    echo "<tr><td><A HREF='#Arriba'><B>Arriba</B></A></td></tr></table>";
    mysql_free_result($err);
    mysql_close($conex);
?>
</body>
</html>
