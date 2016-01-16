<?php

namespace chrisgherbert\ServerMonitorBypassCloudflare;

class ServerChecker {

	protected $host;
	protected $ip;
	protected $response_code;

	public function __construct($host, $ip){
		$this->host = $host;
		$this->ip = $ip;
	}

	public function check(){

		$response_code = $this->get_response_code();

		var_dump($response_code);

		return $this->is_response_code_valid($response_code);

	}

	public function get_response_code(){

		if (isset($this->response_code)){
			return $this->response_code;
		}

		// init
		$ch = curl_init($this->ip);

		// Set the host name
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: {$this->host}"));

		// After six seconds, give up
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 6);
		curl_setopt($ch, CURLOPT_TIMEOUT, 12);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		curl_exec($ch);
		$info = curl_getinfo($ch);

		if (isset($info['http_code'])){

			$this->response_code = $info['http_code'];

			return $this->response_code;

		}

	}

	///////////////
	// Protected //
	///////////////

	protected function is_response_code_valid($response_code){

		$bad_response_codes = array(
			'0',
			'5',
			'4'
		);

		$response_code = (string) $response_code;

		if (in_array($response_code[0], $bad_response_codes)){
			return false;
		}
		else {
			return true;
		}

	}

}

