<?php
include_once("conex.php");
function Consulta($wuse)
{
    $query = mysql_query("select * from regact_000003 WHERE Codigo = '$wuse'");
    $dato = mysql_fetch_array($query);
    return $dato;
}
?>

<?php
function guardar($titulo,$caso,$responsable,$seguridad,$dia)
{
    $fecha = date('Y-m-d');
    $hora = date('H:i:s');

    mysql_query("insert into regact_000001(Medico,Fecha_data,Hora_data,Titulo,Caso,Rol,Dia,Estado,Seguridad) VALUES ('regact','$fecha','$hora','$titulo','$caso','$responsable','$dia','on','$seguridad')");
    ?>
    <div align="center">
        <form method="post" action="regact01.php">
            <label>Datos Almacenados Correctamente</label>
            <br><br>
            <input type="hidden" name="casoB" value="<?php echo $titulo ?>">
	        <input type="submit" class="text-success" value="ACEPTAR"  />
        </form>
    </div>
    <?php
}
?>

<?php
function actualizar($caso,$id_Registro,$cas,$idCaso,$dia,$titulo,$responsable,$parametro,$palabraclave)
{
    $queryRol = mysql_query("SELECT * FROM regact_000004 WHERE Descripcion LIKE '$responsable'");
    $datoRol = mysql_fetch_array($queryRol);
    $responsableFin = $datoRol['Codrol'];

    mysql_query("update regact_000001 set Caso = '$caso',Dia = '$dia',Titulo = '$titulo',Rol = '$responsableFin' WHERE id = '$id_Registro'");
    ?>
    <div align="center">
        <form method="post" action="regact01.php">
            <label>Datos Actualizados Correctamente</label>
            <br><br>
            <input type="hidden" name="casoB" value="<?php echo $id_Registro ?>">
            <input type="hidden" name="idCaso" value="<?php echo $idCaso ?>">
            <input type="hidden" name="responsable" value="<?php echo $responsable ?>">
            <input type="hidden" name="selparam" value="<?php echo $parametro ?>">
            <input type="hidden" name="dia" value="<?php echo $dia ?>">
            <input type="hidden" name="buscar" value="<?php echo $palabraclave ?>">
            <input type="submit" class="text-success" value="ACEPTAR"  />
        </form>
    </div>
    <?php
}
?>
