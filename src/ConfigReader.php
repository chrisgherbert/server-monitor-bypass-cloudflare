<?php

namespace chrisgherbert\ServerMonitorBypassCloudflare;

class ConfigReader {

	protected $config_json;
	protected $config;

	public function __construct($config_file_path){

		if (!file_exists($config_file_path)){
			throw new \Exception('Cannot find the config file!');
		}

		$this->config_json = file_get_contents($config_file_path);

	}

	public function get_servers(){

		$config = $this->get_config();

		if (isset($config->servers)){
			return $config->servers;
		}

	}

	public function get_recipients(){

		$config = $this->get_config();

		if (isset($config->recipients)){
			return $config->recipients;
		}

	}

	public function get_smtp(){

		$config = $this->get_config();

		if (isset($config->smtp)){
			return $config->smtp;
		}

	}

	///////////////
	// Protected //
	///////////////

	protected function get_config(){

		if ($this->config){
			return $this->config;
		}

		$config = json_decode($this->config_json);

		if ($config === null){
			throw new \Exception("{$this->config_file_path} is not valid JSON");
		}

		$this->config = $config;

		return $config;

	}

	protected function validate_config(){

		$config = $this->get_config();

		$errors = array();

		// Check for valid SMTP settings

		if (!isset($config->smtp->host)){
			$errors[] = "SMTP host not set";
		}

		if (!isset($config->smtp->send_email)){
			$errors[] = "Sending email address not set";
		}

	}

	protected function validate_recipients(array $recipients){

		if (!$recipients){
			throw new \Exception('No email recipients set');
		}

		foreach ($recipients as $recipient){
			if (!$recipient->email){
				throw new \Exception('Invalid recipient email');
			}
		}

	}

}