<?php
    try{
        include_once('ValidacionDeToken.php');

        if($_POST['action'] == 'validarDominio') {
            if(is_jwt_valid($_POST['token'])){

                $dominio=filter_var($_POST['dominio'],FILTER_SANITIZE_URL); //fuente: https://www.geeksforgeeks.org/what-are-the-best-input-sanitizing-functions-in-php/
                echo "Hola, validando dominio ".$dominio."\n\n";

                $streamConexionDominio = stream_context_create (
                    array(
                        "ssl" => array("capture_peer_cert" => true)
                    )
                );

                $archivoConexionDominio = fopen($dominio, "rb", false, $streamConexionDominio);
                $parametrosDeArchivoDeDominio = stream_context_get_params($archivoConexionDominio);
                $contieneOpcionesDeCerificadoSsl = ($parametrosDeArchivoDeDominio["options"]["ssl"]["peer_certificate"]);
                $resultadoValidacionSsl = (!is_null($contieneOpcionesDeCerificadoSsl)) ? true : false;
                
                //print_r(array_values($parametrosDeArchivoDeDominio["options"]["ssl"]));

                if($resultadoValidacionSsl){
                    echo "\n"."El certificado ssl SI es válido";
                }else{
                    echo "\n"."El certificado ssl NO es válido";
                }
                
                //------------------- Alternativa #2 --------------------------------------------

                //Datos a detalle: https://stackoverflow.com/questions/3081042/how-to-get-ssl-certificate-info-with-curl-in-php
                $urlOriginal = parse_url($dominio, PHP_URL_HOST);
                $contextoACapturar = stream_context_create(array("ssl" => array("capture_peer_cert" => TRUE)));
                $clienteSocket = stream_socket_client("ssl://".$urlOriginal.":443", $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $contextoACapturar);
                $parametrosDeClienteSocket = stream_context_get_params($clienteSocket);
                $detalleDeCertificado = openssl_x509_parse($parametrosDeClienteSocket['options']['ssl']['peer_certificate']);
                
                echo "\nDetalle:\n";
                print_r($detalleDeCertificado);
            }else{
                echo "Token inválido, favor de intentar de nuevo...".$_POST['token'];
            }

        }else{
            echo "No se recibió petición de validación correctamente.";
        }
    }catch(Exception $excepcionGeneralValidador){
        echo "Ocurrió un error: ".$excepcionGeneralValidador->getMessage();
    }
?>