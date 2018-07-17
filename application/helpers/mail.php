<?php
 
// Create and send messages
Class Mail {
	
	
	/**
	* Build Mailer
	*/
	private static function getMailer() {
		
		require_once(ROOT_DIR. '/application/system/libraries/PHPMailer/class.phpmailer.php');
		require_once(ROOT_DIR. '/application/system/libraries/PHPMailer/class.smtp.php');
		require_once(ROOT_DIR. '/application/system/libraries/PHPMailer/class.pop3.php');
		$mail = new PHPMailer;
		$mail->isSMTP();										// Set mailer to use SMTP
		$mail->Host 			= SMTP_HOST;					// Specify main and backup SMTP servers
		$mail->SMTPAuth 		= true;							// Enable SMTP authentication
		$mail->Username 		= SMTP_USER;					// SMTP username
		$mail->Password 		= SMTP_PWD;             		// SMTP password
		$mail->SMTPSecure		= SMTPSecure;					// Enable TLS encryption, `ssl` also accepted
		$mail->Port 			= SMTP_TLS_PORT;				// TCP port to connect to	
		return $mail;
	}
	
	/*
	* Send an email from CONTACT FORM
	*/
	public static function send_contact_email($email, $email_name='', $subject, $message) {
		
 		if(!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)):
 		
			$mail = Mail::getMailer();
			$mail->setFrom(SMTP_USER_EMAIL, SMTP_USER_Name);;
 			  
			$mail->addAddress('Mike Hankey', 'mike.hankey@gmail.com'); // Add a recipient
			$mail->addReplyTo($email,$email_name);
			$mail->addBCC('vperlerin@gmail.com');
			
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
