<?php

ini_set('default_socket_timeout', 600);

try {
    $client = new SoapClient("https://process-centric-services.herokuapp.com/processCentricServices?wsdl", array("connection_timeout" => 360));

    $channel = "#tests";

    $trigger_word = $_REQUEST['trigger_word'];
	$trigger_word = strtolower($trigger_word);

	$slack_user_id = $_REQUEST['user_id'];

	$user_name = $_REQUEST['user_name'];

	if($trigger_word == "register"){

		$inWebhookUrl = "https://hooks.slack.com/services/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";

		$params = array (
		    "slack_user_id" => $slack_user_id,
		    "user_name" => $user_name,
		);

		$response = $client->initializeUser($params);

		$message = "";

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
		        'content' => "{\"text\": \"" . $message . "\"}",
		    ),
		);

		$context = stream_context_create($options);
		$result = file_get_contents($inWebhookUrl, false, $context);

	} else if($trigger_word == "run"){

		$text = $_REQUEST['text'];
		$text_array = explode(" ", $text);
		$distance = $text_array[1];
		$time = $text_array[2];
		$calories = $text_array[3];

		$params = array (
		    "slack_user_id" => $slack_user_id,
		    "distance" => $distance,
		    "moving_time" => $time,
		    "calories" => $calories,
		);

		$response = $client->updateRunInfo($params);

		$message = $response->person->messages[0]->content . "\n\n";
		$message .= $response->person->messages[1]->content;

		$data = "payload=" . json_encode(array(
	        "channel"       =>  "#tests",
	        "text"          =>  $message,
	    ));

		$ch = curl_init("https://hooks.slack.com/services/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");

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Download the given URL, and return output
		$output = curl_exec($ch);

		// Close the cURL resource, and free system resources
		curl_close($ch);

	} else if($trigger_word == "goalstatus"){

		$params = array (
		    "slack_user_id" => $slack_user_id,
		);

		$response = $client->checkGoalStatus($params);

		$message = "";

		if(!empty($response->goal->goalStatusList)){
	    if(count($response->goal->goalStatusList) > 1){
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
	    } elseif(count($response->goal->goalStatusList) === 1){
		        if($response->goal->goalStatusList->goal_met == 1){
		            $goal_met = "no";
		        } elseif($response->goal->goalStatusList->goal_met == 0){
		            $goal_met = "yes";
		        }

		        $message .= "Count: " . $response->goal->goalStatusList->count . "\n";
		        $message .= "Goal met: " . $goal_met . "\n";
		        $message .= "Name: " . $response->goal->goalStatusList->name . "\n";
		        $message .= "Period: " . $response->goal->goalStatusList->period . "\n";
		        $message .= "End date: " . $response->goal->goalStatusList->period_end . "\n";
		        $message .= "Start date: " . $response->goal->goalStatusList->period_start . "\n";
		        $message .= "Target: " . $response->goal->goalStatusList->target . "\n";
		        $message .= "Type: " . $response->goal->goalStatusList->type . "\n";
		        $message .= "Units: " . $response->goal->goalStatusList->units . "\n";
		        $message .= "\n\n";
		    }
		}

		$our_message = $response->goal->messages[0]->content;

		$image = "<" . $response->goal->messages[1]->content . ">";

		$motivation = $response->goal->messages[2]->content;

		$last_message = $our_message . "\n\n" . $image . "\n\n" . $message . "\n\n" .$motivation;

	    $data = "payload=" . json_encode(array(
	        "channel"       =>  $channel,
	        "text"          =>  $last_message,
	    ));

		$ch = curl_init("https://hooks.slack.com/services/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");

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Download the given URL, and return output
		$output = curl_exec($ch);

		// Close the cURL resource, and free system resources
		curl_close($ch);
		
	} else if($trigger_word == "setgoal"){

		$text = $_REQUEST['text'];
		$text_array = explode(" ", $text);
		$type = $text_array[1];
		$target_value = $text_array[2];
		$goal_period = $text_array[3];

		$params = array (
		    "slack_user_id" => $slack_user_id,
		    "goal_type" => $type,
		    "target" => $target_value,
		    "period" => $goal_period,
		);

		$response = $client->setGoal($params);

		$message = $response->person->messages->content;

		$data = "payload=" . json_encode(array(
	        "channel"       =>  $channel,
	        "text"          =>  $message,
	    ));

		$ch = curl_init("https://hooks.slack.com/services/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");

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Download the given URL, and return output
		$output = curl_exec($ch);

		// Close the cURL resource, and free system resources
		curl_close($ch);
	} elseif(($trigger_word != "register" || $trigger_word != "run" || $trigger_word != "goalstatus" || $trigger_word != "setgoal") && $user_name != "slackbot") {
		
		$message = "Wrong command! These are the commands available:\n\n *register*\n *run* [distance] [time] [calories]\n *goalstatus*\n *setgoal* [goal_type] [target_value] [period]";

		$data = "payload=" . json_encode(array(
	        "channel"       =>  $channel,
	        "text"          =>  $message,
	    ));

		$ch = curl_init("https://hooks.slack.com/services/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");

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Download the given URL, and return output
		$output = curl_exec($ch);

		// Close the cURL resource, and free system resources
		curl_close($ch);
		die;
	}
} catch (SoapFault $fault) {

	$message = "Something went wrong :( Try again";

	$data = "payload=" . json_encode(array(
        "channel"       =>  $channel,
        "text"          =>  $message,
    ));


    $ch = curl_init("https://hooks.slack.com/services/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");

	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	// Download the given URL, and return output
	$output = curl_exec($ch);

	// Close the cURL resource, and free system resources
	curl_close($ch);
}



