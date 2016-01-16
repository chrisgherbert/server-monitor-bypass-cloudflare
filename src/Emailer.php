<?php

namespace chrisgherbert\ServerMonitorBypassCloudflare;

class Emailer {

	protected $email_addresses;
	protected $email_subject;
	protected $email_text;
	protected $smtp_config;

	public function set_email_addresses(array $email_addresses){

		$this->validate_email_addresses($email_addresses);
		$this->email_addresses = $email_addresses;

	}

	public function set_subject($subject){
		$this->email_subject = $subject;
	}

	public function set_text($email_text){
		$this->email_text = $email_text;
	}

	public function set_smtp_config($config){

		$this->validate_smtp_server_settings($config);
		$this->smtp_config = $config;

	}

	public function send(){

		$mail = $this->setup_phpmailer();

		if(!$mail->send()) {
			echo 'Message could not be sent.';
			echo 'Mailer Error: ' . $mail->ErrorInfo;
		} else {
			echo 'Message has been sent';
		}

	}

	///////////////
	// Protected //
	///////////////

	protected function setup_phpmailer(){

		$mail = new \PHPMailer;
		$mail->isSMTP();

		// Configure SMTP Server
		$mail->Host = $this->smtp_config->host;
		$mail->SMTPAuth = $this->smtp_config->auth;
		$mail->Port = $this->smtp_config->port;
		$mail->setFrom($this->smtp_config->send_email, 'Server Monitor');

		// Optional SMTP settings

		if (isset($this->smtp_config->secure)){
			$email->SMTPSecure = $this->smtp_config->secure;
		}

		if (isset($this->smtp_config->auth)){
			$mail->Username = $this->smtp_config->username;
			$mail->Password = $this->smtp_config->password;
		}

		// Add subject
		$mail->Subject = $this->email_subject;

		// Add text
		$mail->Body = $this->email_text;

		// Add recipients
		$mail = $this->add_email_addresses($mail, $this->email_addresses);

		return $mail;

	}

	protected function add_email_addresses(\PHPMailer $mail, array $email_addresses){

		if ($email_addresses){

			foreach ($email_addresses as $email){

				$mail->addAddress($email);

			}

		}

		return $mail;

	}

	protected function validate_smtp_server_settings($smtp_config){

		$errors = array();

		if (!isset($smtp_config->host) || !$smtp_config->host){
			$errors[] = "Missing SMTP host";
		}

		if (!isset($smtp_config->send_email) || !$smtp_config->send_email){
			$errors[] = "Missing SMTP sent from email address";
		}

		if (!isset($smtp_config->port) || !$smtp_config->port){
			$errors[] = "Missing SMTP port";
		}

		if (!isset($smtp_config->auth)){
			$errors[] = "Missing STMP auth setting";
		}

		if (isset($smtp_config->auth) && $smtp_config->auth){

			if (!isset($smtp_config->username) || !$smtp_config->username){
				$errors[] = "SMTP username is required when auth is enabled";
			}

			if (!isset($smtp_config->password) || !$smtp_config->password){
				$errors[] = "SMTP password is required when auth is enabled";
			}

		}

		if ($errors){
			throw new \Exception(implode(', ', $errors));
		}

	}

	protected function validate_email_addresses(array $email_addresses){

		$errors = array();

		if (!$email_addresses){
			$errors[] = "At least one recipient email is required";
		}

		foreach ($email_addresses as $email){

			if (!filter_var($email, FILTER_VALIDATE_EMAIL)){
				$errors[] = "Invalid Email: $email";
			}

		}

		if ($errors){
			throw new \Exception(implode(', ', $errors));
		}

	}

}

