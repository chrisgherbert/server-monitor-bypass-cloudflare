<?php

use chrisgherbert\ServerMonitorBypassCloudflare\ServerChecker;
use chrisgherbert\ServerMonitorBypassCloudflare\ConfigReader;
use chrisgherbert\ServerMonitorBypassCloudflare\Emailer;

require_once(__DIR__ . '/vendor/autoload.php');
require_once('src/ServerChecker.php');
require_once('src/ConfigReader.php');
require_once('src/Emailer.php');

function check(){

	$config = new ConfigReader('config.json');
	$servers = $config->get_servers();
	$recipients = $config->get_recipients();
	$smtp_settings = $config->get_smtp();

	if ($servers){

		foreach ($servers as $server){

			$server_checker = new ServerChecker($server->host, $server->ip);

			if (!$server_checker->check()){

				$text = "Server IP: {$server->ip} \r\n";
				$text .= "Server Host: {$server->host} \r\n";
				$text .= "Response Code: {$server_checker->get_response_code()} ";
				$text .= "({$server_checker->get_reponse_code_description($server_checker->get_response_code())})";

				$mailer = new Emailer;
				$mailer->SMTPSecure = false;
				$mailer->SMTPAutoTLS = false;
				$mailer->set_email_addresses($recipients);
				$mailer->set_subject("Uh oh! Server {$server->host} may be down.");
				$mailer->set_text($text);
				$mailer->set_smtp_config($smtp_settings);

				echo "Potential problem with {$server->host} (Response Code: {$server_checker->get_response_code()}). Sending email. \r\n";

				echo '-- ' . $mailer->send() . "\r\n";

			}
			else {
				echo "Looking good with {$server->host} (Response Code: {$server_checker->get_response_code()}) \r\n";
			}

		}

	}

}

echo check();

