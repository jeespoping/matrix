<?php
    $direccion_ipunix = "132.1.18.9";

    //$str = shell_exec("ping -c 1 www.google.com");
    $str = `ping -c 1 {$direccion_ipunix}`;

    echo "<br>joder-->".$str;
    if(preg_match('/(1 received)/', $str)){
        echo "<br> edb-> entra aca despues del pregmatch :)";
        $ret = true;
    }

?>