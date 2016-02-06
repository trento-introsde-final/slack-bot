<?php 
	
$content = file_get_contents("php://input");
$update = json_decode($content, true);

$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => "{\"text\": \"This is a line of text in a channel.\nAnd this is another line of text.\"}",
    ),
);

$url = "https://hooks.slack.com/services/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";

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
