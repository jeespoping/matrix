<?php
include_once("conex.php");
$descx1=utf8_decode($_GET['descx1']);$descx2=utf8_decode($_GET['descx2']);$descx3=utf8_decode($_GET['descx3']);$fechacx1=$_GET['fechacx1'];$tcx=$_GET['tcx'];
$descem=utf8_decode($_GET['descem']);$fechaem=$_GET['fechaem'];$tem=$_GET['tem'];
$descfis=utf8_decode($_GET['descfis']);$fechafis=$_GET['fechafis'];$tef=$_GET['tef'];

if($tcx == 1)
{
    if($descx1 != null)
    {
        ?>
        <p align="center">CIRUGIAS</p>
        <label style="font-weight: bold; font-size: small">Cirugia 1: </label><label><?php echo $descx1 ?></label>
        <label style="font-weight: bold; font-size: small">Fecha: </label><label><?php echo $fechacx1 ?></label>
        <br>
        <?php
        if($descx2 != null)
        {
            ?>
            <label style="font-weight: bold; font-size: small">Cirugia 2: </label><label><?php echo $descx2 ?></label>
            <br>
            <?php
        }
        if($descx3 != null)
        {
            ?>
            <label style="font-weight: bold; font-size: small">Cirugia 3: </label><label><?php echo $descx3 ?></label>
            <?php
        }
        ?>
        <br>
        <input type="button" style="margin-left: 190px" value="Aceptar" onclick="window.close()">
        <?php
    }
}

if($tem == 2)
{
    if($descem != null)
    {
        ?>
        <p align="center">HEMODINAMIA</p>
        <label style="font-weight: bold; font-size: small">Procedimiento: </label><label><?php echo $descem ?></label>
        <label style="font-weight: bold; font-size: small">Fecha: </label><label><?php echo $fechaem ?></label>
        <br>
        <br>
        <input type="button" style="margin-left: 190px" value="Aceptar" onclick="window.close()">
        <?php
    }
}

if($tef == 3)
{
    if($descfis != null)
    {
        ?>
        <p align="center">ELECTROFISIOLOGIA</p>
        <label>Procedimiento: </label><label><?php echo $descfis ?></label>
        <label style="font-weight: bold; font-size: small">Fecha: </label><label><?php echo $fechafis ?></label>
        <br>
        <br>
        <input type="button" style="margin-left: 190px" value="Aceptar" onclick="window.close()">
        <?php
    }
}