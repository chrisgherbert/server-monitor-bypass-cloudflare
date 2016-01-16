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

				$mailer = new Emailer;
				$mailer->set_email_addresses($recipients);
				$mailer->set_subject("Uh oh! Server {$server->host} may be down.");
				$mailer->set_text("Invalid response code received from {$server->ip} using host {$server->host}. Better check it out ASAP.");
				$mailer->set_smtp_config($smtp_settings);

				return $mailer->send();

			}
			else {
				return "Looking good with all the servers";
			}

		}

	}

}

echo check();

