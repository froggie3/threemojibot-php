<?php

$configFile = __DIR__ . '/config/webhook_url.txt';

function getWebhookURL($arg)
{
	/* $arg = path for config file,
	 * $o = output
	 */

	// opens config file
	$f = file($arg, 0);
	$o = str_replace(PHP_EOL, '', array_pop($f));

	#var_dump($URL);
	return $o;
}

function send_to_discord($arg1, $arg2)
{
	/* $arg1 = message contents,
	 * $arg2 = URL for Webhook
	 */

	// options as a context stream
	$options = array(
		'http' => array(
			'method' => 'POST',
			'header' => 'Content-Type: application/json',
			'content' => json_encode($arg1),
		)
	);
	$context = stream_context_create($options);
	$fp = fopen($arg2, 'r', false, $context);

	fpassthru($fp);
	fclose($fp);
}

