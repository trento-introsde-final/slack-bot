<?php
	
//$content = file_get_contents("php://input");

$inWebhookUrl = "https://hooks.slack.com/services/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";

$client = new SoapClient('https://process-centric-services.herokuapp.com/processCentricServices?wsdl');

$trigger_word = $_REQUEST['trigger_word'];

$slack_user_id = $_REQUEST['user_id'];

if($trigger_word == "register"){

	$user_name = $_REQUEST['user_name'];

	$params = array (
	    "slack_user_id" => $slack_user_id,
	    "user_name" => $user_name,
	);

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

	$params = array (
	    "slack_user_id" => $slack_user_id,
	    "user_name" => $user_name,
	);

	$response = $client->updateRunInfo($params);

	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => "{\"text\": \"distance: " .$distance. " " . "time " . $time . "\"}",
	    ),
	);

} else if($trigger_word == "goalstatus"){

	$params = array (
	    "slack_user_id" => $slack_user_id,
	);

	$response = $client->checkGoalStatus($params);

	var_dump($response->goal);

	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => "{\"text\": \"response: " .(string)$response->goal. " " . "trigger_word " . $trigger_word . "\"}",
	    ),
	);
	
} else if($trigger_word == "setgoal"){
	
}

$user_name = $_REQUEST['user_name'];
$slack_user_id = $_REQUEST['user_id'];

$params = array (
    "slack_user_id" => $slack_user_id,
    "user_name" => $user_name,
);


$context = stream_context_create($options);
$result = file_get_contents($inWebhookUrl, false, $context);
