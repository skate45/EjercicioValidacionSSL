<!DOCTYPE html>
<html lang="es-mx">
  <meta charset="UTF-8">
	<head>
		<title>Validar Dominio SSL</title>

    <style>
        #inputTxtDominio{
          width: 700px;
          height: 60px;
          font-size: 25px;
        }
        #btnValidarDominio{
          width: 180px;
          height:50px;
          border-radius:15px;
          margin:15px;
          font-size: 25px;
          background-color: rgba(0,0,10,.2);
        }
        #btnValidarDominio:hover{
          width: 190px;
          height:60px;
          cursor: pointer;
        }
    </style>

	</head>

  <?php
    $llavePrivadaDeToken='secretPrivateKey123*_';
    include_once("CreacionDeToken.php");
  ?>

	<body>
    	<label for="inputTxtDominio" class="txtDominio">Dominio:</label>
    	<input id="inputTxtDominio" name="inputTxtDominio" type="text" placeholder="Introduce un dominio..."/>
        
        <br>
        
        <button id="btnValidarDominio" onclick="validarDominio()">Validar</button>
        <br>

        <label for="inputTxtResultadoValidacionDominio">Resultado de la validación:</label>
        <textarea id="inputTxtResultadoValidacionDominio" name="inputTxtResultadoValidacionDominio" readonly type="text" rows="50" cols="300"> </textarea>
	</body>
    
    <script type="text/javascript" src="https://code.jquery.com/jquery-3.6.4.min.js"> </script>
    
    <script>
     
        $("#inputTxtDominio").val("https://www.google.com.mx");//Inicialización default

        var tokenActual=null,expiracionToken=null,segundosDuracionToken=60;

        function validarDominio() {
          
          obtenerTokenJWT().then((tokenNew)=>{
            //Lógica fuente: https://stackoverflow.com/questions/19323010/execute-php-function-with-onclick
            $.ajax({
                  type: "POST",
                  url: 'ServicioValidador.php',
                  data:{
                    action:'validarDominio',
                    dominio: $("#inputTxtDominio").val(),
                    token: tokenNew
                  },
                  success:function(respuestaValidador) {
                    $("#inputTxtResultadoValidacionDominio").val(respuestaValidador);
                  },
                  error: function(XMLHttpRequest, textStatus, errorThrown) { 
                      alert("Ocurrió un error, avisar al administrador: estatus: [" + textStatus +"], Detalle: ["+errorThrown+"]");
                  } 
              });
        
          }).catch(
            error => alert("Error al actualizar token JWT.")
          );
        }
        
        function obtenerTokenJWT(){
          return new Promise((resolve, reject) => { //Manejo de promesas js: https://stackoverflow.com/questions/53110707/javascript-promises-with-ajax
            if(tokenActual!=null && expiracionToken.getTime()>(new Date().getTime())){
              resolve(tokenActual);
            }else{
              $.ajax({
                  type: "POST",
                  url: 'ActualizacionDeToken.php',
                  data:{},
                  success:function(respuestaNuevoToken) {
                    tokenActual=respuestaNuevoToken;
                    expiracionToken=new Date(Date.now() + segundosDuracionToken*1000);// https://stackoverflow.com/questions/7687884/add-10-seconds-to-a-date
                    resolve(respuestaNuevoToken);
                  },
                  error: function(XMLHttpRequest, textStatus, errorThrown) { 
                    reject(errorThrown);
                  }
              });
            }
          });
        }

    </script>

</html>