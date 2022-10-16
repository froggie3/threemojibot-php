<?php

declare(strict_types=1);

class cfgperser
{
	public $configFile;
	public function iscfgvalid(): bool
	{
		$bool = (file_exists($this->configFile)) ? true : false;
		return $bool;
	}

	public function format(): string
	{
		// opens config file and eliminates unwanted spaces 
		if ($this->iscfgvalid() === true) {

			// $configFile = path for config file,
			$f = file($this->configFile, 0);

			// output
			$o = str_replace(PHP_EOL, '', array_pop($f));
			return $o;
		} else {
			// output error message
			$msg = $this->configFile . 'not found';
			error_log($msg);
			exit(1);
		}
	}
}

class webhook
{
	public $url = "";
	public $msg = [];
	function send_to_discord()
	{
		/* $arg1 = message contents,
		 * $this->url = URL for Webhook
		 */

		// options as a context stream
		$options = array(
			'http' => array(
				'method' => 'POST',
				'header' => 'Content-Type: application/json',
				'content' => json_encode($this->msg),
			)
		);
		$context = stream_context_create($options);
		$fp = fopen($this->url, 'r', false, $context);

		fpassthru($fp);
		fclose($fp);
	}
}
