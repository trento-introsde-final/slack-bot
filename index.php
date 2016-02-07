<?php
	
//$content = file_get_contents("php://input");

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
	$id = $response->id;

	if($id == -1){
		$message = "Bad parameters";
	} else if($id == -2){
		$message = "Error in some called server";
	} else if($id == -3){
		$message = "No error, but got bad response";
	} else if($id == -4){
		$message = "You are already registered";
	} else if($id > 0){
		$message = "You have been registered! Here is your id: " . $response->id;
	}

	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => "{\"text\": \"Response: " . $message . "\"}",
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
	    "distance" => $distance,
	    "time" => $time,
	    "calories" => $calories,
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

	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => "{\"text\": \"response: " .$response->goal. " " . "trigger_word " . $trigger_word . "\"}",
	    ),
	);
	
} else if($trigger_word == "setgoal"){

	$text = $_REQUEST['text'];
	$text_array = explode(" ", $text);
	$target_value = $text_array[1];
	$type = $text_array[2];
	$goal_measure_type = $text_array[3];
	$goal_period = $text_array[4];

	$params = array (
	    "slack_user_id" => $slack_user_id,
	    "target_value" => $target_value,
	    "type" => $type,
	    "goal_measure_type" => $goal_measure_type,
	    "goal_period" => $goal_period,
	);

	$response = $client->setGoal($params);
}


$inWebhookUrl = "https://hooks.slack.com/services/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";

$context = stream_context_create($options);
$result = file_get_contents($inWebhookUrl, false, $context);
