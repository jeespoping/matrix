<?php
require_once('vendor/autoload.php');

use Firebase\JWT\JWT;


class Authorization{

    private $jwt       = null;//--> string codificado
    private $payload   = null;
    private $secretKey = 'pml@sk2@piA';
    private $time      = null;

    public function __construct(){
    }

    public function getToken( $dataUsuario ){

        $this->time = time();
        $this->payload = array(
            'iat'  => $this->time, // Tiempo que inició el token
            'data' => $dataUsuario,
        );
        if( $dataUsuario['validateIP'] == "on" ){
            $this->payload['aud'] = $this->aud();
        }
        if( $dataUsuario['tokenExpires'] == "on" ){
            $this->payload['exp'] = $this->time + (60*60); // Tiempo que expirará el token (+1 hora)
        }
        $this->jwt = JWT::encode($this->payload, $this->secretKey);

        return ( $this->jwt );
    }

    public function validateToken( $token ){

        //---> checking token if it comes with "bearer";
        $tokenAux = $this->getBearerToken($token);
        $token    = ( $tokenAux === null ) ? $token : $tokenAux;
        if( empty( $token ) ){
            throw new Exception("Invalid token supplied.");
        }

        try{
            $data = JWT::decode($token, $this->secretKey, array('HS256') );
            $this->payload = $data;
        }catch( Exception $e ){
            throw new Exception("The session has expired, please login again");
        }

        if( ( $data->data->validateIP == "on" ) and ( $data->aud !== $this->aud() ) ){
            throw new Exception("Invalid logged in.");
        }
    }

    public function decodeToken( $token ){
        $data = JWT::decode($token, $this->secretKey, array('HS256') );
        return( $data );
    }

    private function aud(){
        $aud = '';

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $aud = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $aud = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $aud = $_SERVER['REMOTE_ADDR'];
        }

        $aud .= @$_SERVER['HTTP_USER_AGENT'];
        $aud .= gethostname();

        return sha1($aud);
    }

    public function getPayload(){
        return( $this->payload );
    }

    private function getBearerToken( $token ) {
    // HEADER: Get the access token from the header
    if (!empty($token)) {
        if (preg_match('/Bearer\s(\S+)/', $token, $matches)) {
            return $matches[1];
        }
    }
    return null;
}
}

?>