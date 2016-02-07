<?php 

$client = new SoapClient("https://process-centric-services.herokuapp.com/processCentricServices?wsdl");

//var_dump($client->__getFunctions()); 

$params = array (
    "slack_user_id" => "UF1244",
    "user_name" => "damiano.fossa",
);

//$response = $client->__soapCall('initializeUser', array($params));
$response = $client->initializeUser($params);

echo "response " . $response->id;

?>