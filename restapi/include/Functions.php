<?php
/**
 *
 * @About:      common functions rest api
 * @File:       functions.php
 * @Date:       2018
 * @Version:    $Rev:$ 1.0
 * @Developer:  Mario Rubio
 **/

 /*********************** USEFULL FUNCTIONS **************************************/

 /**
 * Verificando los parametros requeridos en el metodo o endpoint
 */

function generateOutput($response, $error, $resNum, $message, $resultObjName, $result){
    $output = array();    
    $output["error"] = $error;
    $output["message"] = $message;
    if(isset($resultObjName)){
    $output[$resultObjName] = $result;
    }
    return $response->withJson($output, $resNum);
     
}

 /**
 * Verificando los parametros requeridos en el metodo o endpoint
 */
function verifyRequiredParams($request,$required_fields) {
    $error = false;
    $error_fields = "";
    $request_params = array();
    $request_params = $_REQUEST;
    // Handling PUT request params
    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {        
        parse_str($request->getBody(), $request_params);
    }
    foreach ($required_fields as $field) {
        if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
            $error .= $field . ', ';
        }
    }
    return $error;
}

/**
 * Mostrando la respuesta en formato json al cliente o navegador
 * @param String $status_code Http response code
 * @param Int $response Json response
 */
function echoResponse($status_code, $response) {
    $app = \Slim\Slim::getInstance();
    // Http response code
    $app->status($status_code);
 
    // setting response content type to json
    $app->contentType('application/json');
 
    echo json_encode($response);
}

/**
 * Agregando un leyer intermedio e autenticación para uno o todos los metodos, usar segun necesidad
 * Revisa si la consulta contiene un Header "Authorization" para validar
 */
function authenticate(\Slim\Route $route) {
    // Getting request headers
//    $headers = apache_request_headers();
//    $response = array();
//    $app = \Slim\Slim::getInstance();
 
    // Verifying Authorization Header
//    if (isset($headers['authorization'])) {
        //$db = new DbHandler(); //utilizar para manejar autenticacion contra base de datos
 
        // get the api key
//        $token = $headers['authorization'];
        
        // validating api key
//        if (!($token == API_KEY)) { //API_KEY declarada en Config.php
            // api key is not present in users table
//            $response["error"] = true;
//            $response["message"] = "Acceso denegado. Token inválido";
//            echoResponse(401, $response);
//            $app->stop(); //Detenemos la ejecución del programa al no validar
            
//        } else {
            //procede utilizar el recurso o metodo del llamado
//        }
//    } else {
       

        // api key is missing in header
//        $response["error"] = true;
        #$response["message"] = $messageTmp;
//        $response["message"] = "Falta token de autorización ";
//        echoResponse(400, $response);
//        $app->stop();
//    }
}


?>