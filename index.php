<?php
	
//$content = file_get_contents("php://input");

$client = new SoapClient('https://process-centric-services.herokuapp.com/processCentricServices?wsdl');

$trigger_word = $_REQUEST['trigger_word'];
$trigger_word = strtolower($trigger_word);

$slack_user_id = $_REQUEST['user_id'];

if($trigger_word == "register"){

	$user_name = $_REQUEST['user_name'];

	$params = array (
	    "slack_user_id" => $slack_user_id,
	    "user_name" => $user_name,
	);

	$response = $client->initializeUser($params);

	if($response->id == -1){
		$message = "Bad parameters";
	} else if($response->id == -2){
		$message = "Error in some called server!";
	} else if($response->id == -3){
		$message = "No error, but got bad response!";
	} else if($response->id == -4){
		$message = "You are already registered!";
	} else if($response->id > 0){
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
	        'content' => "{\"text\": \"Response: " .$distance. " " . "time " . $time . "\"}",
	    ),
	);

} else if($trigger_word == "goalstatus"){

	$params = array (
	    "slack_user_id" => $slack_user_id,
	);

	$response = $client->checkGoalStatus($params);

	$message = "";

	if(count($response->goal->goalStatusList) > 0){
		foreach ($response->goal->goalStatusList as $value) {
		    if($value->goal_met == 1){
		        $goal_met = "no";
		    } elseif($value->goal_met == 0){
		        $goal_met = "yes";
		    }
		    $message .= "Count: " . $value->count . "\n";
		    $message .= "Goal met: " . $goal_met . "\n";
		    $message .= "Name: " . $value->name . "\n";
		    $message .= "Period: " . $value->period . "\n";
		    $message .= "End date: " . $value->period_end . "\n";
		    $message .= "Start date: " . $value->period_start . "\n";
		    $message .= "Target: " . $value->target . "\n";
		    $message .= "Type: " . $value->type . "\n";
		    $message .= "Units: " . $value->units . "\n";
		    $message .= "\n\n";
		}
	}
	
	/*$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => "{\"text\": " .$response->goal->messages[0]->content. "\n\n" .$message. "\n\n" .$response->goal->messages[2]->content. "\"}",
	    ),
	);*/

	$options = array(
	    'http' => array(
	        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
	        'method'  => 'POST',
	        'content' => "{\"text\": " .$message. "\"}",
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
var_dump($options);

//$context = stream_context_create($options);
//$result = file_get_contents($inWebhookUrl, false, $context);

$data = "payload=" . json_encode(array(
            "channel"       =>  "#tests",
            "text"          =>  $message,
        ));
	
	// You can get your webhook endpoint from your Slack settings
$ch = curl_init("WEBHOOK ENDPOINT GOES HERE");
curl_setopt($ch, CURLOPT_URL, $inWebhookUrl);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);


/*$curl_handle=curl_init();
curl_setopt($curl_handle, CURLOPT_URL,$inWebhookUrl);
curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($curl_handle, CURLOPT_POST, 1);
curl_setopt($curl_handle, CURLOPT_POSTFIELDS,$options);
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
$query = curl_exec($curl_handle);
curl_close($curl_handle);*/









