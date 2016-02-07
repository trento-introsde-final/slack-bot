<?php 



//var_dump($client->__getTypes());

$params = array (
    "slack_user_id" => "U43F341",
    "user_name" => "damiano.fossa",
);


try {
    $client = new SoapClient("https://process-centric-services.herokuapp.com/processCentricServices?wsdl");
    $response = $client->initializeUser($params);
} catch (SoapFault $fault) {
    trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
}

$response = $client->initializeUser($params);
$id = $response->id;
$message = "";

if($id == -1){
	$message = "Bad parameters";
} else if($id == -2){
	$message = "No error, but got bad response";
} else if($id == -3){
	$message = "You are already registered";
} else if($id > 0){
	$message = "You have been registered! Here is your id: " . $response->id;
}

echo $id . " " . $message;

//var_dump($response->id);


/*$params = array (
    "slack_user_id" => "UK12344",
    "user_name" => "damiano.fossa",
);

$response = $client->initializeUser($params);

var_dump($response);*/

?>