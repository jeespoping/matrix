<?php
function datosUsuario($usuario,$conex,$parametro)
{
    $queryUser = "select * from usuarios WHERE Codigo = '$usuario'";
    $comQryUser = mysql_query($queryUser, $conex) or die (mysql_errno()." - en el query: ".$queryUser." - ".mysql_error());
    $datoUser = mysql_fetch_assoc($comQryUser);
    $nomUser = $datoUser['Descripcion'];

    switch($parametro)
    {
        case 1: return $nomUser; break;
    }
}

function getEmpUser($wusuario,$conex)
{
    // Segun el usuario y empresa de éste consulto la tabla
    // de información de empleados correspondiente a esa empresa
    $query  = "SELECT Detval ";
    $query .= "  FROM usuarios, root_000051 ";
    $query .= " WHERE Codigo = '".$wusuario."' ";
    $query .= "   AND Activo = 'A' ";
    $query .= "   AND Empresa = Detemp ";
    $query .= "   AND Detapl = 'informacion_empleados' ";

    $rsEmpresa = mysql_query($query,$conex);
    $numEmpresa = mysql_num_rows($rsEmpresa);
    if($numEmpresa > 0)
    {
        // Obtengo la tabla de información de empleados que se debe consultar
        $rowEmpresa = mysql_fetch_array($rsEmpresa);
        $tbInfoEmpleado = $rowEmpresa['Detval'];
        //$empresaEmp = $rowEmpresa['Detemp'];

        return $tbInfoEmpleado;
    }
}

function getEmpresaUser($wusuario,$conex)
{
    // Segun el usuario y empresa de éste consulto la tabla
    // de información de empleados correspondiente a esa empresa
    $query  = "SELECT Detemp ";
    $query .= "  FROM usuarios, root_000051 ";
    $query .= " WHERE Codigo = '".$wusuario."' ";
    $query .= "   AND Activo = 'A' ";
    $query .= "   AND Empresa = Detemp ";
    $query .= "   AND Detapl = 'informacion_empleados' ";

    $rsEmpresa = mysql_query($query,$conex);
    $numEmpresa = mysql_num_rows($rsEmpresa);
    if($numEmpresa > 0)
    {
        // Obtengo la tabla de información de empleados que se debe consultar
        $rowEmpresa = mysql_fetch_array($rsEmpresa);
        //$tbInfoEmpleado = $rowEmpresa['Detval'];
        $empresaEmp = $rowEmpresa['Detemp'];

        return $empresaEmp;
    }
}

function datUserxEmp($wusuario,$conex,$parametro)
{
    // CONSULTAR LA TABLA DE INFORMACION DEL USUARIO, SEGUN SU EMPRESA:
    //$tbInfoEmpleado = getEmpUser($wusuario,$conex);
    //$empresaEmp = getEmpUser($wusuario,$conex);
    //if($tbInfoEmpleado == 'thsoe'){$tbInfoEmpleado = 'talhuma';}
    //if($tbInfoEmpleado == 'thidc'){$tbInfoEmpleado = 'talhuma';}
    //if($tbInfoEmpleado == 'latalhum'){$tbInfoEmpleado = 'talhuma';}
    //if($tbInfoEmpleado == 'cstalhum'){$tbInfoEmpleado = 'talhuma';}
    //if($tbInfoEmpleado == 'thhonmed'){$tbInfoEmpleado = 'talhuma';}
    $tbInfoEmpleado = 'talhuma_000013';

    // Consulto la cédula del empleado
    $query  = "SELECT * ";
    $query .= "  FROM usuarios, $tbInfoEmpleado ";
    $query .= " WHERE Codigo = '".$wusuario."' ";
    $query .= "   AND ( ( CONCAT(Codigo,'-',Empresa) = Ideuse OR  CONCAT(SUBSTRING(Codigo,3),'-',Empresa) = Ideuse)";
    $query .= "   OR ( Codigo = Ideuse OR  SUBSTRING(Codigo,3) = Ideuse) ) ";
    $query .= "   AND Ideest = 'on' ";
    $query .= "   AND Activo = 'A' ";
    $rs = mysql_query($query,$conex);
    $num = mysql_num_rows($rs);

    if($num > 0)
    {
        $query2 = "SELECT * FROM talento_000006 WHERE Usucod = '$wusuario'";
        $comQry2 = mysql_query($query2,$conex);
        $row2 = mysql_fetch_array($comQry2);
        $Ideemer = $row2['Usuemer'];        $Ideraz = $row2['Usuraza'];         $telEmer = $row2['telemer'];    $UsuGasto = $row2['Usugasto'];  $Ususit = $row2['Ususitua'];
        $posFam = $row2['Usuposfam'];       $UsuCuHij = $row2['Usucuhij'];      $UsuSuv = $row2['Ususubvi'];    $ahoViv = $row2['Usuahviv'];    $UsuMonah = $row2['Usumonaho'];
        $Usfariesvi = $row2['Usfariesvi'];  $UsuMejoVi = $row2['Usumejovi'];    $UsProFi = $row2['Usuprofi'];   $UsMoCre = $row2['Usumocre'];   $UsEpAcu = $row2['Usepacu'];
        $UsLcInt = $row2['UsLcInt'];        $UsIaAho = $row2['UsIaAho'];        $UsNefor = $row2['UsNefor'];    $UsHobie = $row2['UsHobie'];    $UsQpTie = $row2['UsQpTie'];
        $UsHhTli = $row2['UsHhTli'];        $UsBuTli = $row2['UsBuTli'];

        // Obtengo los datos del Empleado empleado
        $row = mysql_fetch_array($rs);
        $Idefnc = $row['Idefnc'];   $Idegen = $row['Idegen'];   $Ideced = $row['Ideced'];   $Ideuse = $row['Ideuse'];
        $Idepas = $row['Idepas'];   $Idevis = $row['Idevis'];   $Idepvi = $row['Idepvi'];   $empresa = $row['Empresa'];
        $Ideciv = $row['Ideesc'];   $Idedir = $row['Idedir'];   $Ideinc = $row['Ideinc'];   $Idestt = $row['Idestt'];
        $Idempo = $row['Idempo'];   $Idebrr = $row['Idebrr'];   $Idetel = $row['Idetel'];   $Idecel = $row['Idecel'];
        $Ideeml = $row['Ideeml'];   $Idesrh = $row['Idesrh'];   $Ideext = $row['Ideext'];   $Ideeps = $row['Ideeps'];
        $Idepol = $row['Idescs'];

        if($Ideciv == '02'){$Ideciv = 'Casado(a)';}

        switch($parametro)
        {
            case 1: echo $Idefnc; break;    case 2: echo $Idegen; break;    case 3: echo $Ideced; break;        case 4: echo $Ideuse; break;
            case 5: return $Idepas; break;  case 6: return $Idevis; break;  case 7: echo $Idepvi; break;        case 8: echo $empresa; break;
            case 9: return $Ideciv; break;  case 10: echo $Idedir; break;   case 11: echo $Ideinc; break;       case 12: echo $Idestt; break;
            case 13: return $Idempo; break; case 14: return $Idebrr; break; case 15: echo $Idetel; break;       case 16: echo $Idecel; break;
            case 17: echo $Ideeml; break;   case 18: return $Idesrh; break;   case 19: echo $Ideext; break;       case 20: return $Ideuse; break;
            case 21: return $Ideeps; break; case 22: return $Idepol;break;  case 23: return $Idegen; break;     case 24: return $Ideced; break;
            case 25: echo $Ideemer; break;  case 26: return $Ideraz; break; case 27: return $telEmer; break;    case 28: return $UsuGasto; break;
            case 29: return $Ususit; break; case 30: return $posFam; break; case 31: return $UsuCuHij;break;    case 32: return $UsuSuv; break;
            case 33: return $ahoViv; break; case 34: return $UsuMonah;break;case 35: return $Usfariesvi; break; case 36: return $UsuMejoVi; break;
            case 37: return $UsProFi;break; case 38: return $UsMoCre;break; case 39: return $UsEpAcu; break;    case 40: return $UsLcInt; break;
            case 41: return $UsIaAho;break; case 42: return $UsNefor;break; case 43: return $UsHobie; break;    case 44: return $UsQpTie; break;
            case 45: return $UsHhTli;break; case 46: return $UsBuTli;break;
        }
    }
}

function tablaUser($wusuario,$conex)
{
    $tbInfoEmpleado = getEmpUser($wusuario,$conex);
    $tablaArray = explode('_',$tbInfoEmpleado);
    $tablaEmp = $tablaArray[0];

    return $tablaEmp;
}

function datoEscolar($Edugrd,$conex)
{
    //$queryEscolar = "select Scodes from root_000066 WHERE Scocod = '$Edugrd'";
    $queryEscolar = "select Scodes from talento_000007 WHERE Scocod = '$Edugrd'";
    $comQryEscolar = mysql_query($queryEscolar, $conex) or die (mysql_errno()." - en el query: ".$queryEscolar." - ".mysql_error());
    $datoEscolar = mysql_fetch_assoc($comQryEscolar);
    $gradoEscolar = $datoEscolar['Scodes'];

    return $gradoEscolar;
}

function obtenerOpenTab($conex,$wuse,$fechaActual,$tab)
{
    $queryTab = "select tabMtx from equipos_000017 WHERE codMtx = '$wuse'";
    $commitQryTab = mysql_query($queryTab, $conex) or die (mysql_errno()." - en el query: ".$queryTab." - ".mysql_error());
    $datoTab = mysql_fetch_assoc($commitQryTab);
    $activeTab = $datoTab['tabMtx'];

    //return $activeTab;

    if($activeTab != null)
    {
        $queryUpTab = "update equipos_000017 set tabMtx = '$tab' WHERE codMtx = '$wuse'";
        $comUpTab = mysql_query($queryUpTab, $conex) or die (mysql_errno()." - en el query: ".$queryUpTab." - ".mysql_error());
    }
    else
    {
        $queryInsTab = "insert into equipos_000017 VALUES('','$wuse','$tab','$fechaActual')";
        $comInsTab = mysql_query($queryInsTab, $conex) or die (mysql_errno()." - en el query: ".$queryInsTab." - ".mysql_error());
    }
}

function returnTab($conex,$wuse)
{
    $query = "select tabMtx from equipos_000017 WHERE codMtx = '$wuse' ";
    $commit = mysql_query($query, $conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());
    $dato = mysql_fetch_assoc($commit);
    $tab = $dato['tabMtx'];

    return $tab;
}

function obtenerParentesco($conex,$grupar)
{
    $query = "select Pardes from root_000067 WHERE Parcod = '$grupar' AND Parest = 'on'";
    $commit = mysql_query($query, $conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());
    $dato = mysql_fetch_assoc($commit);
    $parentesco = $dato['Pardes'];

    return $parentesco;
}

function obtenerOcupacion($Ocucod,$conex)
{
    $query = "select Ocudes from root_000078 WHERE Ocucod = '$Ocucod' AND Ocuest = 'on'";
    $commit = mysql_query($query, $conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());
    $dato = mysql_fetch_assoc($commit);
    $ocupacion = $dato['Ocudes'];

    return $ocupacion;
}

function obtenerDatoEps($epsAct,$conex)
{
    $query = "select Epscod,Epsnom from root_000073 WHERE Epscod = '$epsAct'  AND Epsest = 'on'";
    $commit = mysql_query($query, $conex) or die (mysql_errno()." - en el query: ".$query." - ".mysql_error());
    $dato = mysql_fetch_assoc($commit);
    $codEps = $dato['Epscod'];  $descEps = $dato['Epsnom'];
    $epsActual = $codEps.'-'.$descEps;

    return $epsActual;

}

function datosMunicipio($codMuni,$conex)
{
    $QueryMuni = "select Nombre from root_000006 WHERE Codigo = '$codMuni'";
    $commMuni = mysql_query($QueryMuni, $conex) or die (mysql_errno()." - en el query: ".$QueryMuni." - ".mysql_error());
    $datoMuni = mysql_fetch_assoc($commMuni);
    $descMuni = $datoMuni['Nombre'];

    return $descMuni;
}

function datosBarrio($codBarr,$codMuni,$conex)
{
    $QueryBarr = "select Bardes from root_000034 WHERE Barcod = '$codBarr' AND Barmun = '$codMuni'";
    $commBarr = mysql_query($QueryBarr, $conex) or die (mysql_errno()." - en el query: ".$QueryBarr." - ".mysql_error());
    $datoBarr = mysql_fetch_assoc($commBarr);
    $descBarr = $datoBarr['Bardes'];

    return $descBarr;
}

function datosCondVida($wusuario,$conex,$tablaEmp,$parametro)
{
    $queryConVida = "select * from".' '."$tablaEmp"."_000024 WHERE Cviuse = '$wusuario'";
    $commConVida = mysql_query($queryConVida, $conex) or die (mysql_errno()." - en el query: ".$queryConVida." - ".mysql_error());
    $datoConVida = mysql_fetch_array($commConVida);

    $codTenViv = $datoConVida['Cviviv'];    $codTipoViv = $datoConVida['Cvitvi'];   $terraza = $datoConVida['Cvitrz'];
    $lote = $datoConVida['Cvilot'];         $estadoViv = $datoConVida['Cvisvi'];    $codServP = $datoConVida['Cvissp'];
    $codTrasp = $datoConVida['Cvitra'];     $otroTrans = $datoConVida['Cviotr'];    $codAlmu = $datoConVida['Cvical'];
    $otrosAl = $datoConVida['Cvioal'];      $credito = $datoConVida['Cvicre'];      $hobbie = $datoConVida['Cvihbb'];
    $actEdu = $datoConVida['Cviapa'];       $horaEdu = $datoConVida['Cvidod'];

    switch($parametro)
    {
        case 1: return $codTenViv; break;   case 2: return $codTipoViv; break;  case 3: return $terraza; break;
        case 4: return $lote; break;        case 5: return $estadoViv; break;   case 6: return $codServP; break;
        case 7: return $codTrasp; break;    case 8: return $otroTrans; break;   case 9: return $codAlmu; break;
        case 10: return $otrosAl; break;    case 11: return $credito; break;    case 12: return $hobbie; break;
        case 13: return $actEdu; break;     case 14: return $horaEdu; break;
    }
}

function datosCreditos($wusuario,$conex,$tablaEmp,$parametro)
{
    $queryCreditos = "select * from".' '."$tablaEmp"."_000025 WHERE Creuse = '$wusuario'";
    $commCreditos = mysql_query($queryCreditos, $conex) or die (mysql_errno()." - en el query: ".$queryCreditos." - ".mysql_error());
    $datoCredito = mysql_fetch_array($commCreditos);

    $cremot = $datoCredito['Cremot'];   $creent = $datoCredito['Creent'];   $creval = $datoCredito['Creval'];
    $crecuo = $datoCredito['Crecuo'];   $idCred = $datoCredito['id'];

    switch($parametro)
    {
        case 1: return $cremot;break;   case 2: return $creent;break;   case 3: return $creval;break;
        case 4: return $crecuo;break;   case 5: return $idCred;break;
    }
}

function datosVivienda($codTenViv,$conex,$parametro)
{
    if($parametro == 1)
    {
        $QueryVivi = "select Tendes from root_000068 WHERE Tencod = '$codTenViv'";
        $commVivi = mysql_query($QueryVivi, $conex) or die (mysql_errno()." - en el query: ".$QueryVivi." - ".mysql_error());
        $datoVivi = mysql_fetch_assoc($commVivi);
        $descVivi = $codTenViv.'-'.$datoVivi['Tendes'];

        return $descVivi;
    }
    if($parametro == 2)
    {
        $QueryVivi = "select Tpvdes from root_000069 WHERE Tpvcod = '$codTenViv'";
        $commVivi = mysql_query($QueryVivi, $conex) or die (mysql_errno()." - en el query: ".$QueryVivi." - ".mysql_error());
        $datoVivi = mysql_fetch_assoc($commVivi);
        $descVivi = $codTenViv.'-'.$datoVivi['Tpvdes'];

        return $descVivi;
    }
    if($parametro == 3)
    {
        $QueryVivi = "select Esvdes from root_000070 WHERE Esvcod = '$codTenViv'";
        $commVivi = mysql_query($QueryVivi, $conex) or die (mysql_errno()." - en el query: ".$QueryVivi." - ".mysql_error());
        $datoVivi = mysql_fetch_assoc($commVivi);
        $descVivi = $codTenViv.'-'.$datoVivi['Esvdes'];

        return $descVivi;
    }
}

function datosServicios($var)
{
    switch($var)
    {
        case '01': return 'energia'; break;     case '02': return 'telefono'; break;    case '03': return 'acueducto'; break;
        case '04': return 'alcantar'; break;    case '05': return 'aseo'; break;        case '06': return 'gas'; break;
        case '07': return 'internet'; break;
    }

}

function datosTrasporte($var)
{
    switch($var)
    {
        case '01': return 'bus'; break;     case '02': return 'metro'; break;   case '03': return 'particular'; break;
        case '04': return 'moto'; break;    case '05': return 'taxi'; break;    case '06': return 'contratado'; break;
        case '07': return 'bici'; break;    case '08': return 'camina'; break;  case '09': return 'otro'; break;
    }
}

function datosAlimentacion($var)
{
    switch($var)
    {
        case '01': return 'trae'; break;    case '02': return 'comBoca'; break;   case '03': return 'comOtros'; break;
        case '04': return 'casa'; break;    case '05': return 'otros'; break;
    }

}

function datosOtros($wusuario,$conex,$parametro)
{
    $queryCreditos = "select * from talhuma_000060 WHERE Otruse = '$wusuario'";
    $commCreditos = mysql_query($queryCreditos, $conex) or die (mysql_errno()." - en el query: ".$queryCreditos." - ".mysql_error());
    $datoCredito = mysql_fetch_array($commCreditos);

    $lugParquea = $datoCredito['Otrpar'];           $locker = $datoCredito['Otrlocker'];    $actRecre = $datoCredito['Otractrec'];
    $actRecreHora = $datoCredito['Otractrechor'];   $actCult = $datoCredito['Otractcul'];   $actCultHora = $datoCredito['Otractculhor'];
    $roles = $datoCredito['Otrrol'];                $otroRol = $datoCredito['Otrroles'];    $comites = $datoCredito['Otrcomite'];
    $despTime = $datoCredito['OtrTime'];            $otrTurno = $datoCredito['OtrTurno'];   $actExtra = $datoCredito['OtrExtra'];
    $otraExtra = $datoCredito['OtrExOtra'];         $ranSal = $datoCredito['OtrSalar'];

    switch($parametro)
    {
        case 1: return $lugParquea;break;   case 2: return $locker;break;       case 3: return $actRecre;break;
        case 4: return $actRecreHora;break; case 5: return $actCult;break;      case 6: return $actCultHora;break;
        case 7: return $roles;break;        case 8: return $otroRol;break;      case 9: return $comites;break;
        case 10: return $despTime;break;    case 11: return $otrTurno;break;    case 12: return $actExtra;break;
        case 13: return $otraExtra;break;   case 14: return $ranSal;break;
    }
}

function obtenerSal($conex,$parametro)
{
    $query1 = "select Topmin from cliame_000194 WHERE Topest = 'on'";
    $comQry1 = mysql_query($query1, $conex) or die (mysql_errno()." - en el query: ".$query1." - ".mysql_error());
    $datoMin = mysql_fetch_array($comQry1);
    $topMin = $datoMin['Topmin'];
    $salMin = $topMin * 30;//SALARIO MINIMO
    $min2 = $salMin * 2;//2 SALARIOS MINIMOS
    $min4 = $salMin * 4;//4 SALARIOS MINIMOS
    $min6 = $salMin * 6;//6 SALARIOS MINIMOS

    switch($parametro)
    {
        case 1: return $salMin; break;  case 2: return $min2; break;
        case 3: return $min4; break;    case 4: return $min6; break;
    }
}

function saveTal_24($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Cviviv,$Cvitvi,$Cvisvi,$chEner,$chTele,$chAcue,
                    $chAlca,$chAseo,$chGas,$chInter,$chBus,$chMetro,$chPart,$chMoto,$chTaxi,$chContra,$chBici,$chCamina,$chOtroT,
                    $chTrae,$chComBoca,$chComOtros,$chCasa,$chOtrosAl,$chClaInfo,$Cvitrz,$Cvilot,$Cvicre,$Cviotr,$horaEdu,$Cvihbb,
                    $Cvioal,$wuse)
{
    $query1 = "select id from".' '."$tablaUser"."_000024 where Cviuse = '$usuarioMtx'";
    $commit1 = mysql_query($query1, $conex) or die (mysql_errno()." - en el query: ".$query1." - ".mysql_error());
    $datoEmpl = mysql_fetch_assoc($commit1);
    $idEmpl = $datoEmpl['id'];

    $cadena = explode("-", $Cviviv);
    $Cviviv = $cadena[0];   //CODIGO TIPO TENENCIA
    $cadena3 = explode("-", $Cvitvi);
    $Cvitvi = $cadena3[0];  //CODIGO TIPO VIVIENDA
    $cadena2 = explode("-", $Cvisvi);
    $Cvisvi = $cadena2[0];  //CODIGO ESTADO VIVIENDA
    if($chEner == 'on'){$chEner = '01';} else{$chEner = '00';} if($chTele == 'on'){$chTele = '02';} else{$chTele = '00';}
    if($chAcue == 'on'){$chAcue = '03';} else{$chAcue = '00';} if($chAlca == 'on'){$chAlca = '04';} else{$chAlca = '00';}
    if($chAseo == 'on'){$chAseo = '05';} else{$chAseo = '00';} if($chGas == 'on'){$chGas = '06';} else{$chGas = '00';}
    if($chInter == 'on'){$chInter = '07';} else{$chInter = '00';}
    $Cvissp = $chAcue.','.$chAlca.','.$chAseo.','.$chEner.','.$chInter.','.$chGas.','.$chTele;  //CODIGOS SERVICIOS PUBLICOS

    if($chBus == 'on'){$chBus = '01';} else{$chBus = '00';}     if($chMetro == 'on'){$chMetro = '02';} else{$chMetro = '00';}
    if($chPart == 'on'){$chPart = '03';} else{$chPart = '00';}  if($chMoto == 'on'){$chMoto = '04';} else{$chMoto = '00';}
    if($chTaxi == 'on'){$chTaxi = '05';} else{$chTaxi = '00';}  if($chContra == 'on'){$chContra = '06';} else{$chContra = '00';}
    if($chBici == 'on'){$chBici = '07';} else{$chBici = '00';}  if($chCamina == 'on'){$chCamina = '08';} else{$chCamina = '00';}
    if($chOtroT == 'on'){$chOtroT = '09';} else{$chOtroT = '00';}
    $Cvitra = $chBici.','.$chBus.','.$chCamina.','.$chPart.','.$chMetro.','.$chMoto.','.$chOtroT.','.$chTaxi.','.$chContra; //CODIGOS TRANSPORTES

    if($chTrae == 'on'){$chTrae = '01';} else{$chTrae = '00';}              if($chComBoca == 'on'){$chComBoca = '02';} else{$chComBoca = '00';}
    if($chComOtros == 'on'){$chComOtros = '03';} else{$chComOtros = '00';}  if($chCasa == 'on'){$chCasa = '04';} else{$chCasa = '00';}
    if($chOtrosAl == 'on'){$chOtrosAl = '05';} else{$chOtrosAl = '00';}
    $Cvical = $chTrae.','.$chComBoca.','.$chComOtros.','.$chCasa.','.$chOtrosAl; //CODIGOS TIPOS DE ALMUERZO

    $Cviapa = $chClaInfo;   //CODIGOS ACTIVIDADES EDUCATIVAS

    if($Cvitrz == 'SI'){$Cvitrz = 'on';} else{$Cvitrz = 'off';} //TERRAZA
    if($Cvilot == 'SI'){$Cvilot = 'on';} else{$Cvilot = 'off';} //LOTE

    if($idEmpl == null)
    {
        $queryIns1 = "insert into".' '."$tablaUser"."_000024
                          values('talhuma','$fechaActual','$horaActual','$Cviviv','$Cvitvi','$Cvitrz','$Cvilot','$Cvisvi','$Cvissp',
                          '$Cvicre','$Cvitra','$Cviotr','','$horaEdu','','','$Cviapa','$Cvihbb','$Cvical','$Cvioal','','','$usuarioMtx','C-$wuse','on','')";
        $comQryIns1 = mysql_query($queryIns1, $conex) or die (mysql_errno()." - en el query: ".$queryIns1." - ".mysql_error());
    }
    else
    {
        $queryUpd2 = "update".' '."$tablaUser"."_000024 set Cviviv='$Cviviv',Cvitvi='$Cvitvi',Cvitrz='$Cvitrz',Cvilot='$Cvilot',
                          Cvisvi='$Cvisvi',Cvissp='$Cvissp',Cvicre='$Cvicre',Cvitra='$Cvitra',Cviotr='$Cviotr',Cvical='$Cvical',
                          Cvioal='$Cvioal',Cviapa='$Cviapa',Cvihbb='$Cvihbb',Cvidod='$horaEdu'
                          where Cviuse = '$usuarioMtx'";
        $comUpd2 = mysql_query($queryUpd2, $conex) or die (mysql_errno()." - en el query: ".$queryUpd2." - ".mysql_error());
    }
}

function saveTal_60($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$chAudit,$chComite,$chBrigada,$chOtroRol,$chCoCon,$chGesIn,$chCoDoc,
                    $chGesTe,$chCoInv,$chHisTe,$chCoCom,$chInfInt,$chCopas,$chMedTra,$chCrede,$chMeCon,$chEtiIn,$chMoSe,$chEtiHo,$chSePac,$chEvCal,
                    $chTransp,$chFarTe,$chViEpi,$chGesAm,$Otrlocker,$chTorBol,$chTorPla,$chTorVol,$chTorBal,$chTorTen,$chCamin,$chBaile,$chYoga,
                    $chEnPare,$chCiclo,$chMara,$chTarHob,$chGruTea,$chArtPla,$chManual,$chGastro,$chClaIng,$chConPle,$chTarPic,$chOtrAct,$Otrpar,
                    $Otractrechor,$Otractcul,$Otractculhor,$Otrroles,$timeDesp,$turnEmp,$actExtra,$otraExtra,$ranSal,$wuse)
{
    $QuerySel1 = "select id from talhuma_000060 where Otruse = '$usuarioMtx'";
    $commSel1 = mysql_query($QuerySel1, $conex) or die (mysql_errno()." - en el query: ".$QuerySel1." - ".mysql_error());
    $dato1 = mysql_fetch_assoc($commSel1);
    $idTal19 = $dato1['id'];

    if($chAudit == 'on'){$chAudit = '01';} else{$chAudit = '00';}       if($chComite == 'on'){$chComite = '02';} else{$chComite = '00';}
    if($chBrigada == 'on'){$chBrigada = '03';} else{$chBrigada = '00';} if($chOtroRol == 'on'){$chOtroRol = '04';} else{$chOtroRol = '00';}
    $Otrrol = $chAudit.','.$chComite.','.$chBrigada.','.$chOtroRol; //ROLES EMPLEADO

    if($chCoCon == 'on'){$chCoCon = '01';} else{$chCoCon = '00';}   if($chGesIn == 'on'){$chGesIn = '02';} else{$chGesIn = '00';}
    if($chCoDoc == 'on'){$chCoDoc = '03';} else{$chCoDoc = '00';}   if($chGesTe == 'on'){$chGesTe = '04';} else{$chGesTe = '00';}
    if($chCoInv == 'on'){$chCoInv = '05';} else{$chCoInv = '00';}   if($chHisTe == 'on'){$chHisTe = '06';} else{$chHisTe = '00';}
    if($chCoCom == 'on'){$chCoCom = '07';} else{$chCoCom = '00';}   if($chInfInt == 'on'){$chInfInt = '08';} else{$chInfInt = '00';}
    if($chCopas == 'on'){$chCopas = '09';} else{$chCopas = '00';}   if($chMedTra == 'on'){$chMedTra = '10';} else{$chMedTra = '00';}
    if($chCrede == 'on'){$chCrede = '11';} else{$chCrede = '00';}   if($chMeCon == 'on'){$chMeCon = '12';} else{$chMeCon = '00';}
    if($chEtiIn == 'on'){$chEtiIn = '13';} else{$chEtiIn = '00';}   if($chMoSe == 'on'){$chMoSe = '14';} else{$chMoSe = '00';}
    if($chEtiHo == 'on'){$chEtiHo = '15';} else{$chEtiHo = '00';}   if($chSePac == 'on'){$chSePac = '16';} else{$chSePac = '00';}
    if($chEvCal == 'on'){$chEvCal = '17';} else{$chEvCal = '00';}   if($chTransp == 'on'){$chTransp = '18';} else{$chTransp = '00';}
    if($chFarTe == 'on'){$chFarTe = '19';} else{$chFarTe = '00';}   if($chViEpi == 'on'){$chViEpi = '20';} else{$chViEpi = '00';}
    if($chGesAm == 'on'){$chGesAm = '21';} else{$chGesAm = '00';}
    $rolesEmp = $chCoCon.','.$chGesIn.','.$chCoDoc.','.$chGesTe.','.$chCoInv.','.$chHisTe.','.$chCoCom.','.$chInfInt.','.$chCopas.','.
        $chMedTra.','.$chCrede.','.$chMeCon.','.$chEtiIn.','.$chMoSe.','.$chEtiHo.','.$chSePac.','.$chEvCal.','.$chTransp.','.
        $chFarTe.','.$chViEpi.','.$chGesAm;  //PERTENENCIA A COMITES

    if($Otrlocker == 'SI'){$Otrlocker = 'on';} else{$Otrlocker = 'off';}  //LOCKER

    //DATOS ACTIVIDADES RECREATIVAS:
    if($chTorBol == 'on'){$chTorBol = '01';} else{$chTorBol = '00';} if($chTorPla == 'on'){$chTorPla = '02';} else{$chTorPla = '00';}
    if($chTorVol == 'on'){$chTorVol = '03';} else{$chTorVol = '00';} if($chTorBal == 'on'){$chTorBal = '04';} else{$chTorBal = '00';}
    if($chTorTen == 'on'){$chTorTen = '05';} else{$chTorTen = '00';} if($chCamin == 'on'){$chCamin = '06';} else{$chCamin = '00';}
    if($chBaile == 'on'){$chBaile = '07';} else{$chBaile = '00';}    if($chYoga == 'on'){$chYoga = '08';} else{$chYoga = '00';}
    if($chEnPare == 'on'){$chEnPare = '09';} else{$chEnPare = '00';} if($chCiclo == 'on'){$chCiclo = '10';} else{$chCiclo = '00';}
    if($chMara == 'on'){$chMara = '11';} else{$chMara = '00';}       if($chTarHob == 'on'){$chTarHob = '12';} else{$chTarHob = '00';}
    if($chGruTea == 'on'){$chGruTea = '13';} else{$chGruTea = '00';} if($chArtPla == 'on'){$chArtPla = '14';} else{$chArtPla = '00';}
    if($chManual == 'on'){$chManual = '15';} else{$chManual = '00';} if($chGastro == 'on'){$chGastro = '16';} else{$chGastro = '00';}
    if($chClaIng == 'on'){$chClaIng = '17';} else{$chClaIng = '00';} if($chConPle == 'on'){$chConPle = '18';} else{$chConPle = '00';}
    if($chTarPic == 'on'){$chTarPic = '19';} else{$chTarPic = '00';} if($chOtrAct == 'on'){$chOtrAct = '20';} else{$chOtrAct = '00';}
    $actiEmp = $chTorBol.','.$chTorPla.','.$chTorVol.','.$chTorBal.','.$chTorTen.','.$chCamin.','.$chBaile.','.$chYoga.','.$chEnPare.','.$chCiclo.','.
        $chMara.','.$chTarHob.','.$chGruTea.','.$chArtPla.','.$chManual.','.$chGastro.','.$chClaIng.','.$chConPle.','.$chTarPic.','.$chOtrAct;

    if($idTal19 == null)
    {
        $queryInsT19 = "insert into talhuma_000060
                            values('talhuma','$fechaActual','$horaActual','$usuarioMtx','$Otrpar','$Otrlocker','$actiEmp','$Otractrechor',
                            '$Otractcul','$Otractculhor','$Otrrol','$Otrroles','$rolesEmp','$timeDesp','$turnEmp','$actExtra','$otraExtra',
                            '$ranSal','C-$wuse','')";
        $comQryInsT19 = mysql_query($queryInsT19, $conex) or die (mysql_errno()." - en el query: ".$queryInsT19." - ".mysql_error());

        obtenerOpenTab($conex,$wuse,$fechaActual,1);

        ?><script>window.close()</script><?php
    }
    else
    {
        $queryUpdT19 = "update talhuma_000060 set Otrpar='$Otrpar',Otrlocker='$Otrlocker',Otractrec='$actiEmp',Otractrechor='$Otractrechor',
                                       Otractcul='$Otractcul',Otractculhor='$Otractculhor',Otrrol='$Otrrol',Otrroles='$Otrroles',Otrcomite='$rolesEmp',
                                       OtrTime='$timeDesp',OtrTurno='$turnEmp',OtrExtra='$actExtra',OtrExOtra='$otraExtra',OtrSalar='$ranSal'
                                where Otruse = '$usuarioMtx'";
        $commUpdT19 = mysql_query($queryUpdT19, $conex) or die (mysql_errno()." - en el query: ".$queryUpdT19." - ".mysql_error());
    }

}

function saveTal_13($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$Idepas,$Idevis,$estcivUser,$lunaUser,$munUser,$barUser,$estUser,
                    $tisanUser,$wuse,$Idegen,$Idefnc,$Idedir,$telUser,$celUser,$corUser,$extUser,$cedula)
{
    //OBTENER CODIGO DEL MUNICIPIO:
    $QueryMuni = "select Codigo from root_000006 WHERE Nombre LIKE '$munUser'";
    $commMuni = mysql_query($QueryMuni, $conex) or die (mysql_errno()." - en el query: ".$QueryMuni." - ".mysql_error());
    $datoMuni = mysql_fetch_assoc($commMuni);
    $codMuni = $datoMuni['Codigo'];

    //OBTENER CODIGO DEL BARRIO:
    $QueryBarr = "select Barcod from root_000034 WHERE Bardes LIKE '$barUser' AND Barmun = '$codMuni'";
    $commBarr = mysql_query($QueryBarr, $conex) or die (mysql_errno()." - en el query: ".$QueryBarr." - ".mysql_error());
    $datoBarr = mysql_fetch_assoc($commBarr);
    $codBarr = $datoBarr['Barcod'];

    //OBTENER ESTADO CIVIL:
    $QueryEstCiv = "select Scvcod from root_000065 WHERE Scvdes LIKE '$estcivUser'";
    $commEstCiv = mysql_query($QueryEstCiv, $conex) or die (mysql_errno()." - en el query: ".$QueryBarr." - ".mysql_error());
    $datoEstCiv = mysql_fetch_assoc($commEstCiv);
    $codEstCiv = $datoEstCiv['Scvcod'];

    if($Idegen == 'Masculino'){$Idegen = 'M';} else{$Idegen = 'F';}

    //INFORMACION GENERAL
    $queryUpd1 = "update".' '."$tablaUser"."_000013 set Fecha_data = '$fechaActual',Hora_data = '$horaActual',Idepas = '$Idepas',
                                        Idevis = '$Idevis',Idepvi = '',Ideesc = '$codEstCiv',Ideinc = '$lunaUser',Idempo = '$codMuni',Idebrr = '$codBarr',
                                        Idestt = '$estUser',Idesrh = '$tisanUser',Idefnc = '$Idefnc',Idegen = '$Idegen',Idedir = '$Idedir',Idetel = '$telUser',
                                        Idecel = '$celUser',Ideeml = '$corUser',Ideext = '$extUser'
                                      where Ideuse = '$usuarioMtx'";
    $comUpd1 = mysql_query($queryUpd1, $conex) or die (mysql_errno()." - en el query: ".$queryUpd1." - ".mysql_error());
}

function saveTal_06($conex,$tablaUser,$fechaActual,$horaActual,$usuarioMtx,$wuse,$contEmer,$usuRaza,$telEmer,$usuGasto,$usuSitua,$posFam,$usuCuHi,
                    $subViv,$ahoViv,$montoHa,$Usfariesvi,$UsuMejoVi,$UsProFi,$UsMoCre,$UsEpAcu,$UsLcInt,$UsIaAho,$UsNefor,$UsHobie,$UsQpTie,$UsHhTli,
                    $UsBuTli)
{
    $query06 = "select id from talento_000006 where Usucod = '$wuse'";
    $commit06 = mysql_query($query06, $conex) or die (mysql_errno()." - en el query: ".$query06." - ".mysql_error());
    $datoEmp06 = mysql_fetch_assoc($commit06);
    $idEmp06 = $datoEmp06['id'];

    if($idEmp06 == null)
    {
        $queryIns6 = "insert into talento_000006 VALUES('talento','$fechaActual','$horaActual','$wuse','$contEmer','$telEmer',
                                                        '$usuRaza','$usuGasto','$usuSitua','$posFam','$usuCuHi','$subViv','$ahoViv',
                                                        '$montoHa','$Usfariesvi','$UsuMejoVi','$UsProFi','$UsMoCre','$UsEpAcu','$UsLcInt',
                                                        '$UsIaAho','$UsNefor','$UsHobie','$UsQpTie','$UsHhTli','$UsBuTli','C-$wuse','')";
        $comIns6 = mysql_query($queryIns6, $conex) or die (mysql_errno()." - en el query: ".$queryIns6." - ".mysql_error());
    }
    else
    {
        $queryUpd06 = "update talento_000006 set Fecha_data = '$fechaActual',Hora_data = '$horaActual',Usuemer = '$contEmer',
                              telemer = '$telEmer',Usuraza = '$usuRaza',Usugasto = '$usuGasto',Ususitua = '$usuSitua',Usuposfam = '$posFam',
                              Usucuhij = '$usuCuHi',Ususubvi = '$subViv',Usuahviv = '$ahoViv',Usumonaho = '$montoHa',Usfariesvi = '$Usfariesvi',
                              Usumejovi = '$UsuMejoVi',Usuprofi = '$UsProFi',Usumocre = '$UsMoCre',Usepacu = '$UsEpAcu',UsLcInt = '$UsLcInt',
                              UsIaAho = '$UsIaAho',UsNefor = '$UsNefor',UsHobie = '$UsHobie',UsQpTie = '$UsQpTie',UsHhTli = '$UsHhTli',UsBuTli = '$UsBuTli'
                       WHERE Usucod = '$wuse'";
        $comUpd06 = mysql_query($queryUpd06, $conex) or die (mysql_errno()." - en el query: ".$queryUpd06." - ".mysql_error());
    }
}

function saveTal_08($conex,$fechaActual,$horaActual,$wuse,$IdecedP)
{
    $query06 = "select id from talento_000008 where codEmp = '$wuse'";
    $commit06 = mysql_query($query06, $conex) or die (mysql_errno()." - en el query: ".$query06." - ".mysql_error());
    $datoEmp06 = mysql_fetch_assoc($commit06);
    $idEmp08 = $datoEmp06['id'];

    if($idEmp08 == null)
    {
        $queryIns6 = "insert into talento_000008 VALUES('talento','$fechaActual','$horaActual','$wuse','$IdecedP','on','C-$wuse','')";
        $comIns6 = mysql_query($queryIns6, $conex) or die (mysql_errno()." - en el query: ".$queryIns6." - ".mysql_error());
    }
    else
    {
        $queryUpd06 = "update talento_000008 set Fecha_data = '$fechaActual',Hora_data = '$horaActual'
                       WHERE codEmp = '$wuse'";
        $comUpd06 = mysql_query($queryUpd06, $conex) or die (mysql_errno()." - en el query: ".$queryUpd06." - ".mysql_error());
    }
}
?>