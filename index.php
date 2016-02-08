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
		$attachment = "";

		if($response->id == -1){
			$message = "Bad parameters";
		} else if($response->id == -2){
			$message = "Error in some called server!";
		} else if($response->id == -3){
			$message = "No error, but got bad response!";
		} else if($response->id == -4){
			$message = "You are already registered! Here is what you can do:";
				$attachment = array([
	            'color'    => '#5AAC56',
	            'fields'	=> array(
	            		array(
	            			'title' => 'setgoal [goal_type] [target_value] [period]',
	            			'value' => 'Set a fitness goal',
	            			'short' => false
	        			),
	        			array(
	            			'title' => 'run [distance] [time] [calories]',
	            			'value' => 'Add a new run',
	            			'short' => false
	        			),
						array(
	            			'title' => 'goalstatus',
	            			'value' => 'Check where you stand',
	            			'short' => false
	        			)
	            	),
	        ]);

		} else if($response->id > 0){
			$message = "You have been registered! Here is what you can do: ";
			$attachment = array([
	            'color'    => '#5AAC56',
	            'fields'	=> array(
	            		array(
	            			'title' => 'setgoal [goal_type] [target_value] [period]',
	            			'value' => 'Set a fitness goal',
	            			'short' => false
	        			),
	        			array(
	            			'title' => 'run [distance] [time] [calories]',
	            			'value' => 'Add a new run',
	            			'short' => false
	        			),
						array(
	            			'title' => 'goalstatus',
	            			'value' => 'Check where you stand',
	            			'short' => false
	        			)
	            	),
	        ]);
		}

		$data = json_encode(array(
	        "channel"       =>  $channel,
	        "text"          =>  '*' . $user_name . '* ' . $message,
	        "attachments"   =>  $attachment,
	    ));

		$ch = curl_init("https://hooks.slack.com/services/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");

		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		// Download the given URL, and return output
		$output = curl_exec($ch);

		// Close the cURL resource, and free system resources
		curl_close($ch);

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

		$message = $response->person->messages[0]->content;
		$motivation = $response->person->messages[1]->content;
		
		if($response->person->messages[1]->type == "image"){
			$attachments =  array([
	                'fallback' => $message,
	                'color'    => '#ff6600',
	                'image_url'    => $motivation,
	        ]);
		} elseif($response->person->messages[1]->type == "quote") {
			$attachments =  array([
	                'fallback' => $message,
	                'color'    => '#ff6600',
	                'title'    => $motivation,
	        ]);
		}

		$data = json_encode(array(
	        "channel"  =>  $channel,
            'text' =>  $message,
	        "attachments"   =>  $attachments,
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

	    $goal_met = "";

	    $color = "";

	    $attachments = array();

    if(!empty($response->goal->goalStatusList)){
        if(count($response->goal->goalStatusList) > 1){
            $i = 1;
            foreach ($response->goal->goalStatusList as $value) {
                if($value->goal_met == false){
                    $goal_met = "no";
                } elseif($value->goal_met == true){
                    $goal_met = "yes";
                }
                $message = "*Goal #".$i . "*  ";
                $message .= $value->target . " " . $value->units . " " . $value->period . ". From " . date("d/m/Y", ($value->period_start)/1000) 
                            . " to " . date("d/m/Y", ($value->period_end)/1000) . ": ";

                if($goal_met == "yes"){
                    $message .= "Goal achieved! :smile:";
                    $color = "5AAC56";
                } elseif($goal_met == "no") {
                    $message .= "*".(intval($value->target) - intval($value->count)) . " left to go*";
                    $color = "F0D84F";
                }
                
                $message .= "\n\n";

                $attachments[] =  array(
                    'fallback' =>  $message,
                    'color'    => $color,
                    'text' =>  $message,
                    'mrkdwn_in' => array('text'),
                );

                $i++;
            }
        } elseif(count($response->goal->goalStatusList) === 1){
            if($response->goal->goalStatusList->goal_met == false){
                $goal_met = "no";
                $color = "5AAC56";
            } elseif($response->goal->goalStatusList->goal_met == true){
                $goal_met = "yes";
                $color = "F0D84F";
            }

            $message = "*Goal #1*  ";
            $message .= $value->target . " " . $value->units . " " . $value->period . ". From " . date("d/m/Y", ($value->period_start)/1000) 
                            . " to " . date("d/m/Y", ($value->period_end)/1000) . ": ";
            if($goal_met == "yes"){
                $message .= "Goal achieved! :smile:";
            } elseif($goal_met == "no") {
                $message .= "*".(intval($value->target) - intval($value->count)) . " left to go*";
            }
            $message .= "\n\n";

            $attachments[] =  array(
                'fallback' =>  $message,
                'color'    => $color,
                'text' =>  $message,
                'mrkdwn_in' => array('text'),
            );
        }
    }

    $our_message = $response->goal->messages[0]->content;

    $image = $response->goal->messages[1]->content;

    $motivation = $response->goal->messages[2]->content;

    //$last_message = $our_message . "\n\n" . $image . "\n\n" . $message . "\n\n" .$motivation;

    $attachments[] =  array(
                'fallback' => '*'.$user_name.'*: '.$our_message,
                'mrkdwn_in' => array('pretext'),
                'color'    => '#ff6600',
                'title'    => $motivation,
                'image_url'    => $image,
            );

    $data = json_encode(array(
        "channel"       =>  $channel,
        "text"             => '*'.$user_name.'*: '.$our_message,
        "attachments"    =>  $attachments,
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
	} elseif($trigger_word == "help") {
		
		$message = "We are here to help you! Here is what you can do: ";
		$attachment = array([
            'color'    => '#5AAC56',
            'fields'	=> array(
            		array(
            			'title' => 'register',
            			'value' => 'Create an account before getting started',
            			'short' => false
        			),
            		array(
            			'title' => 'setgoal [goal_type] [target_value] [period]',
            			'value' => 'Set a fitness goal',
            			'short' => false
        			),
        			array(
            			'title' => 'run [distance] [time] [calories]',
            			'value' => 'Add a new run',
            			'short' => false
        			),
					array(
            			'title' => 'goalstatus',
            			'value' => 'Check where you stand',
            			'short' => false
        			)
            	),
        ]);

		$data = json_encode(array(
	        "channel"       =>  $channel,
	        "text"          =>  'Hi *' . $user_name . '*. ' . $message,
	        "attachments"   =>  $attachment,
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



