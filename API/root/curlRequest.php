<?php
class CurlRequest{

    private $url        = "";
    private $token      = "";
    private $parametros = "";

    public function __construct( $url, $token, $parametros ){

        $this->url        = $url;
        $this->token      = $token;
        $this->parametros = $parametros;
    }

    public function sendPost(){
        //datos a enviar
        $data = $this->parametros;
        $authorization = "Authorization: Bearer ".$this->token;
        //url contra la que atacamos
        $ch = curl_init( $this->url );
        //a true, obtendremos una respuesta de la url, en otro caso,
        //true si es correcto, false si no lo es
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //token setting in the header
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        //establecemos el verbo http que queremos utilizar para la petición
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        //enviamos el array data
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data );
        //obtenemos la respuesta
        $response = curl_exec($ch);
        // Se cierra el recurso CURL y se liberan los recursos del sistema
        curl_close($ch);
        if(!$response) {
            return false;
        }else{
            return $response;
        }
    }

    public function sendPut(){
        //datos a enviar
        $data = $this->parametros;
        //url contra la que atacamos
        $ch = curl_init( $this->url );
        //a true, obtendremos una respuesta de la url, en otro caso,
        //true si es correcto, false si no lo es
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //establecemos el verbo http que queremos utilizar para la petición
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        //obtenemos la respuesta
        $response = curl_exec($ch);
        // Se cierra el recurso CURL y se liberan los recursos del sistema
        curl_close($ch);
        if(!$response) {
            return false;
        }else{
            return($response);
        }
    }

    public function sendGet(){
        //datos a enviar
        $data = $this->parametros;
        //seteo del token en una variable a inyectar posteriormente
        $authorization = "Authorization: Bearer ".$this->token;
        //url contra la que atacamos
        $ch = curl_init( $this->url );
        //a true, obtendremos una respuesta de la url, en otro caso,
        //true si es correcto, false si no lo es
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //inyección del token de autorización a la petición
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));
        //establecemos el verbo http que queremos utilizar para la petición
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        //obtenemos la respuesta
        $response = curl_exec($ch);
        // Se cierra el recurso CURL y se liberan los recursos del sistema
        curl_close($ch);
        if(!$response) {
            return false;
        }else{
            return( $response );
        }
    }

    public function sendDelete(){
        //datos a enviar
        $data = $this->parametros;
        //url contra la que atacamos
        $ch = curl_init( $this->url );
        //a true, obtendremos una respuesta de la url, en otro caso,
        //true si es correcto, false si no lo es
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //establecemos el verbo http que queremos utilizar para la petición
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        //obtenemos la respuesta
        $response = curl_exec($ch);
        // Se cierra el recurso CURL y se liberan los recursos del sistema
        curl_close($ch);
        if(!$response) {
            return false;
        }else{
            return($response);
        }
    }
}
?>