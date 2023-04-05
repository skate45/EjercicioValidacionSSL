<?php
    // fuente: https://roytuts.com/how-to-generate-and-validate-jwt-using-php-without-using-third-party-api/

    function base64url_encode($str) {
        return rtrim(strtr(base64_encode($str), '+/', '-_'), '=');
    }

    function generate_jwt($headers, $payload) {
        $headers_encoded = base64url_encode(json_encode($headers));
        
        $payload_encoded = base64url_encode(json_encode($payload));
        
        $signature = hash_hmac('SHA256', "$headers_encoded.$payload_encoded", $llavePrivadaDeToken, true);
        $signature_encoded = base64url_encode($signature);
        
        $jwt = "$headers_encoded.$payload_encoded.$signature_encoded";
        
        return $jwt;
    }

    
    $segundosDuracionToken=60;
    
    $headers = array('alg'=>'HS256','typ'=>'JWT');
    $payload = array('exp'=>(time() + $segundosDuracionToken));
    $jwt = generate_jwt($headers, $payload);
    echo $jwt;
    
?>