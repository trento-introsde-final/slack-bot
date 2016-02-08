<?php 

ini_set('default_socket_timeout', 360);
ini_set('soap.wsdl_cache_enabled', 0);

/*$params = array (
    "slack_user_id" => "U43F341",
    "user_name" => "damiano.fossa",
);


$params = array (
    "slack_user_id" => "U43F341",
    "distance" => "7000",
    "time" => "3600",
    "calories" => "300",
);


try {
    $client = new SoapClient("https://process-centric-services.herokuapp.com/processCentricServices?wsdl");
    $response = $client->updateRunInfo($params);
} catch (SoapFault $fault) {
    trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
}

var_dump($response);*/

/////////////////////////////////////

/*$params = array (
    "slack_user_id" => "U43F341",
);


try {
    //$client = new SoapClient("http://192.168.0.101:6900/processCentricServices?wsdl");
    $client = new SoapClient("https://process-centric-services.herokuapp.com/processCentricServices?wsdl", array("connection_timeout" => 360));
    $response = $client->checkGoalStatus($params);
} catch (SoapFault $fault) {
    trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
}

//var_dump($response->goal->goalStatusList);

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

$our_message = $response->goal->messages[0]->content;

$motivation = $response->goal->messages[2]->content;

$last_message = $our_message . "\n\n" . $message . "\n\n" .$motivation;

$data = "payload=" . json_encode(array(
        "channel"       =>  "#tests",
        "text"          =>  $last_message,
    ));

$ch = curl_init("https://hooks.slack.com/services/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");

// Set URL to download
//curl_setopt($ch, CURLOPT_URL, $inWebhookUrl);

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Download the given URL, and return output
$output = curl_exec($ch);

// Close the cURL resource, and free system resources
curl_close($ch);


/////////////////////////////////////

/*$params = array (
    "slack_user_id" => "U43F341",
    "user_name" => "damiano.fossa",
);

try {
    $client = new SoapClient("https://process-centric-services.herokuapp.com/processCentricServices?wsdl");
    $response = $client->initializeUser($params);
} catch (SoapFault $fault) {
    trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
}

var_dump($response);*/

/*$params = array (
    "slack_user_id" => "U43F341",
    "distance" => "2000",
    "moving_time" => "1800",
    "calories" => "200",
);


try {
    //$client = new SoapClient("http://192.168.0.101:6900/processCentricServices?wsdl");
    $client = new SoapClient("https://process-centric-services.herokuapp.com/processCentricServices?wsdl", array("connection_timeout" => 360));
    $response = $client->updateRunInfo($params);
} catch (SoapFault $fault) {
    trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
}

var_dump($response->person->messages[0]->content);
var_dump($response->person->messages[1]->content);*/


/*$params = array (
    "slack_user_id" => "U43F341",
    "goal_type" => "distance",
    "target" => "2000",
    "period" => "daily",
);


try {
    //$client = new SoapClient("http://192.168.0.101:6900/processCentricServices?wsdl", array("connection_timeout" => 360));
    $client = new SoapClient("https://process-centric-services.herokuapp.com/processCentricServices?wsdl", array("connection_timeout" => 360));
    $response = $client->setGoal($params);
} catch (SoapFault $fault) {
    trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
}

var_dump($response->person->messages->content);*/


$params = array (
    "slack_user_id" => "U0L5MAMPH",
);

/*$params = array (
    "slack_user_id" => "U43F341",
);*/


try {
    //$client = new SoapClient("http://192.168.0.101:6900/processCentricServices?wsdl");
    $client = new SoapClient("https://process-centric-services.herokuapp.com/processCentricServices?wsdl", array("connection_timeout" => 360));
    $response = $client->checkGoalStatus($params);
} catch (SoapFault $fault) {
    var_dump($fault->faultstring);
    trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
}

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

$image = $response->goal->messages[1]->content;

$motivation = $response->goal->messages[2]->content;

//$last_message = $our_message . "\n\n" . $message . "\n\n" .$motivation;

$last_message = $image;

$channel = "#tests";

$attachment = array([
            'fallback' => 'Lorem ipsum',
            'pretext'  => 'Lorem ipsum',
            'color'    => '#ff6600',
            'fields'   => array(
                [
                    'title' => 'Message',
                    'value' => $our_message,
                    'short' => false
                ],
                [
                    'title' => 'Motivation',
                    'value' => $motivation,
                    'short' => false
                ]
            )
        ]);

$data = "payload=" . json_encode(array(
        "channel"       =>  $channel,
        "text"          =>  $image,
        "attachment"    =>  $attachment,
    ));

$ch = curl_init("https://hooks.slack.com/services/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");

//echo "<pre>"; 
print_r($data);
//echo "</pre>";

curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Download the given URL, and return output
$output = curl_exec($ch);

// Close the cURL resource, and free system resources
curl_close($ch);
        












?>
