<?php 

ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 900);
ini_set('default_socket_timeout', 15);
	
//$content = file_get_contents("php://input");

$inWebhookUrl = "https://hooks.slack.com/services/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";

$trigger_word = $_REQUEST['trigger_word'];

/*if($trigger_word == "register"){

	$user_name = $_REQUEST['user_name'];
	$slack_user_id = $_REQUEST['user_id'];

	$params = array (
	    "slack_user_id" => $slack_user_id,
	    "user_name" => $user_name,
	);

	//$response = $client->__soapCall('initializeUser', array($params));
	$response = $client->initializeUser($params);

	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => "{\"text\": \"response: " .$response->id. " " . "trigger_word " . $trigger_word . "\"}",
	    ),
	);

} else if($trigger_word == "run"){

	$text = $_REQUEST['text'];
	$text_array = explode(" ", $text);
	$distance = $text_array[1];
	$time = $text_array[2];
	$calories = $text_array[3];

	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => "{\"text\": \"distance: " .$distance. " " . "time " . $time . "\"}",
	    ),
	);

} else if($trigger_word == "goalstatus"){
	
} else if($trigger_word == "setgoal"){
	
}*/

$user_name = $_REQUEST['user_name'];
$slack_user_id = $_REQUEST['user_id'];

$params = array (
    "slack_user_id" => $slack_user_id,
    "user_name" => $user_name,
);

try {
	libxml_disable_entity_loader(false);
	$client = new SoapClient('https://process-centric-services.herokuapp.com/processCentricServices?wsdl');
	$response = $client->initializeUser($params);
} catch(Exception $e) {
	die($e->getMessage());
}

/*try{
	file_get_contents("https://process-centric-services.herokuapp.com/processCentricServices?wsdl");
} catch(Exception $e) {
	echo "file_get_contents ";
	die($e->getMessage());
}*/

//$response = $client->__soapCall('initializeUser', array($params));


$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => "{\"text\": \"response: " .$response->id. " " . "trigger_word " . $trigger_word . "\"}",
    ),
);


$context = stream_context_create($options);
$result = file_get_contents($inWebhookUrl, false, $context);
