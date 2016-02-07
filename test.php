<?php 



//var_dump($client->__getTypes());

$params = array (
    "slack_user_id" => "U43F341",
);


try {
    $client = new SoapClient("https://process-centric-services.herokuapp.com/processCentricServices?wsdl");
    $response = $client->checkGoalStatus($params);
} catch (SoapFault $fault) {
    trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
}


var_dump($response);


/*$params = array (
    "slack_user_id" => "UK12344",
    "user_name" => "damiano.fossa",
);

$response = $client->initializeUser($params);

var_dump($response);*/

?>