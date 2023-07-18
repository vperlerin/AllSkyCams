<?php

//namespace PHPMailer\PHPMailer; !!!!!!

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\OAuth;
use League\OAuth2\Client\Provider\Google;

// Create and send messages
Class Mail {

	/**
	* Build Mailer
	*/
	private static function getMailer() {

		date_default_timezone_set('Etc/UTC');
		require ROOT_DIR . '/vendor/autoload.php';

		$mail = new PHPMailer;
		//$mail->SMTPDebug = 4;


		$mail->isSMTP();

		$mail->Host 			= SMTP_HOST;					// Specify main and backup SMTP servers
		$mail->Port 			= SMTP_TLS_PORT;				// TCP port to connect to
		$mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
		$mail->SMTPAuth = true;
		$mail->AuthType = 'XOAUTH2';

		$email = SMTP_USER;
		$clientId = SMTP_clientId;
		$clientSecret = SMTP_clientSecret;
		$refreshToken =  SMTP_refreshToken;

		$provider = new Google(
			[
				'clientId' => $clientId,
				'clientSecret' => $clientSecret,
			]
		);
		$mail->setOAuth(
			new OAuth(
				[
					'provider' => $provider,
					'clientId' => $clientId,
					'clientSecret' => $clientSecret,
					'refreshToken' => $refreshToken,
					'userName' => $email,
				]
			)
		);

		$mail->CharSet = PHPMailer::CHARSET_UTF8;
		return $mail;
	}

	/*
	* Send an email from CONTACT FORM
	*/
	public static function send_contact_email($email, $email_name='', $subject, $message) {

 		if(!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)):
	        $mail = Mail::getMailer();
    	    $mail->setFrom(SMTP_USER_EMAIL, SMTP_USER_Name);;

			$mail->addAddress('mike.hankey@gmail.com','Mike Hankey'); // Add a recipient
			$mail->addBCC('vperlerin@gmail.com');
			$mail->addReplyTo($email,$email_name);

			$mail->isHTML(true);  // HTML
			$mail->Subject  = $subject;
			$mail->Body 	= $message;
			$mail->AltBody 	= $message;

			return $mail->send();

		 else:

		 	return false;
		 endif;
	}


}

?>
