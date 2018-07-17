<?php

 

// Create and send messages
Class Mail {
	
	
	/**
	* Build Mailer
	*/
	private static function getMailer() {
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
	public static function send_contact_email($email, $subject, $message, $email_name='', $reply_email, $reply_name) {
		
 		if(!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)):
 		
 			$mail = Mail_Pages::getMailer();
 			
			$mail->setFrom('webserver@imo.net', 'International Meteor Organization');
 		 
			$mail->addAddress($email, $email_name); // Add a recipient
			$mail->addReplyTo($reply_email,$reply_name);
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
	
	
	/*
	* Send an email to an email address (no-reply)
	*/
	public static function send_email($email, $subject, $message, $email_name='') {
		
 		if(!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)):
 		
 			$mail = Mail_Pages::getMailer();
 			
			$mail->setFrom('webserver@imo.net', 'International Meteor Organization');
			$mail->addAddress($email, $email_name); // Add a recipient
			$mail->addReplyTo('noreply@imo.net','DO NOT REPLY');
			//$mail->addBCC('vperlerin@gmail.com');
			
			$mail->isHTML(true);  // HTML
			$mail->Subject  = $subject;
			$mail->Body 	= $message;
			$mail->AltBody 	= $message;
			
			return $mail->send();
		 
		 else:
		 
		 	return false;
		 endif;
	}
	
	
	/*
	* Send an email to an email address (no-reply) and BCC an array of emails
	*/
	public static function send_emailBCC($email, $subject, $message, $email_name='', $bcc) {
		
 		if(!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)):
 		
 			$mail = Mail_Pages::getMailer();
 			
			$mail->setFrom('webserver@imo.net', 'International Meteor Organization');
			$mail->addAddress($email, $email_name);     							// Add a recipient
			
			foreach($bcc as $c):
				$mail->addBCC($c);
			endforeach;
			
			$mail->addReplyTo('noreply@imo.net','DO NOT REPLY');
			$mail->addBCC('vperlerin@gmail.com');
			
			$mail->isHTML(true);    												// HTML
			$mail->Subject  = $subject;
			$mail->Body 	= $message;
			$mail->AltBody 	= $message;
			
			return $mail->send();
		 
		 else:
		 
		 	return false;
		 endif;
	}
	
    
      /*
      * Send an email to a member
      */
      public static function send_email_to_mulitple_users($users, $subject, $message) {
            
           $mail = Mail_Pages::getMailer();
           $mail->setFrom('webserver@imo.net', 'International Meteor Organization');
           $mail->addAddress('webserver@imo.net', 'International Meteor Organization');     							 
           $mail->addReplyTo('noreply@imo.net','DO NOT REPLY');
           
           $mail->isHTML(true);    												 
           $mail->Subject  = $subject;
           $mail->Body 	= $message;
           $mail->AltBody 	= $message; 
            
           foreach($users as $res):  
		 
                if(!empty($res['email']) && filter_var($res['email'], FILTER_VALIDATE_EMAIL)):
			
				    $mail->addBCC($res['email'],$res['first_name'] . ' ' . $res['last_name']);
			    endif;
            
            endforeach;
          
            $mail->addBCC('vperlerin@gmail.com','Vincent Perlerin');
            $mail->addBCC('marc.gyssens@uhasselt.be','Marc Gyssens');
          
            return $mail->send();
            
      }
    
		 
      /*
      * Send an email to a member
      */
      public static function send_email_to_user($user_id, $subject, $message) {
            $res = IMO_Members_Model::get_users(array('user_id' => $user_id),'assoc');
		 
            if(!empty($res['email']) && filter_var($res['email'], FILTER_VALIDATE_EMAIL)):
			
				$mail = Mail_Pages::getMailer();
				$mail->setFrom('webserver@imo.net', 'International Meteor Organization');
				$mail->addAddress($res['email'], $res['first_name'] . ' ' . $res['last_name']);     							 
				$mail->addReplyTo('noreply@imo.net','DO NOT REPLY');
				$mail->addBCC('vperlerin@gmail.com');
				$mail->isHTML(true);    												 
				$mail->Subject  = $subject;
				$mail->Body 	= $message;
				$mail->AltBody 	= $message;
			
				return $mail->send();
            else:
                echo "EMAIL ERROR - please contact the administrator of the site";
                exit;
            endif;
      }
 	
	
	
	/*
	* Send a HTML email with an attachment
	*/
	public static function send_html_email_with_attachment($path,$filename,$message,$subject,$email) {
		
		$mail = Mail_Pages::getMailer();
		$mail->setFrom('webserver@imo.net', 'International Meteor Organization');
		$mail->addAddress($res['email'], $res['first_name'] . ' ' . $res['last_name']);     							 
		$mail->addReplyTo('noreply@imo.net','DO NOT REPLY');
		$mail->addBCC('vperlerin@gmail.com');
		$mail->addAttachment($path);         // Add attachments
		$mail->isHTML(true);    												 
		$mail->Subject  = $subject;
		$mail->Body 	= $message;
		$mail->AltBody 	= $message;
		return $mail->send();
 		 
		 
 	}
	
	
	/*
	* Send an ASCII email with an attachment
	*/
	public static function send_email_with_attachment($path,$filename,$message,$subject,$email) {
		$mail = Mail_Pages::getMailer();
		$mail->setFrom('webserver@imo.net', 'International Meteor Organization');
		$mail->addAddress($res['email'], $res['first_name'] . ' ' . $res['last_name']);     							 
		$mail->addReplyTo('noreply@imo.net','DO NOT REPLY');
		$mail->addBCC('vperlerin@gmail.com');
		$mail->addAttachment($path);         // Add attachments
		$mail->isHTML(false);				// ASCII    												 
		$mail->Subject  = $subject;
		$mail->Body 	= $message;
		$mail->AltBody 	= $message;
		return $mail->send();
 	}
	


	/*
	* Send an email to all the VMDB admins with an attachment
	*/
	public static function send_vmdb_admins_with_attachment($path,$filename,$message,$subject) {
		
 		$mail = Mail_Pages::getMailer();
		$mail->setFrom('webserver@imo.net', 'International Meteor Organization');
		
		$admins = unserialize(VMDB_ADMIN); 
		foreach($admins as $k=>$admin) :
			$mail->addAddress($admin,$k);     	
  		endforeach;	
 								 
		$mail->addReplyTo('noreply@imo.net','DO NOT REPLY');
		$mail->addBCC('vperlerin@gmail.com');
		$mail->addAttachment($path);         // Add attachments
		$mail->isHTML(true);				// ASCII    												 
		$mail->Subject  = $subject;
		$mail->Body 	= $message;
		$mail->AltBody 	= $message;
		return $mail->send();
   		 
	}
	



	
	/*
	* Send an email to all the VMDB admins with an attachment
	*/
	public static function send_admins_with_attachment($path,$filename,$message,$subject) {
		
 		$mail = Mail_Pages::getMailer();
		$mail->setFrom('webserver@imo.net', 'International Meteor Organization');
		
		$admins = Imo_Admin_User_Model::get_admin_users(array());  
		foreach($admins as $admin) :
			$mail->addAddress($admin['email']);     	
  		endforeach;	
 								 
		$mail->addReplyTo('noreply@imo.net','DO NOT REPLY');
		$mail->addBCC('vperlerin@gmail.com');
		$mail->addAttachment($path);         // Add attachments
		$mail->isHTML(false);				// ASCII    												 
		$mail->Subject  = $subject;
		$mail->Body 	= $message;
		$mail->AltBody 	= $message;
		return $mail->send();
   		 
	}
	
 	/*
	* Send an email to an email address
	*/
    public static function send_ascii($email, $subject, $message) {
 		$mail = Mail_Pages::getMailer();
		$mail->setFrom('webserver@imo.net', 'International Meteor Organization');
		$mail->addAddress($email);     	
 		$mail->addReplyTo('noreply@imo.net','DO NOT REPLY');
		$mail->addBCC('vperlerin@gmail.com');
 		$mail->isHTML(false);				// ASCII    												 
		$mail->Subject  = $subject;
		$mail->Body 	= $message;
		$mail->AltBody 	= $message;
		return $mail->send();
	}
	
 	/*
 	* Send an email to all the super admins
 	*/
 	public static function send_email_to_super_admins($subject, $message) {
		$mail = Mail_Pages::getMailer();
		$mail->setFrom('webserver@imo.net', 'International Meteor Organization');
		$mail->addAddress(ADMIN_EMAIL_ADDRESSES);     	
 		$mail->addReplyTo('noreply@imo.net','DO NOT REPLY');
		$mail->addBCC('vperlerin@gmail.com');
		$mail->addAttachment($path);         // Add attachments
		$mail->isHTML(false);				// ASCII    												 
		$mail->Subject  = $subject;
		$mail->Body 	= $message;
		$mail->AltBody 	= $message;
		return $mail->send();
   	}  
    
        
    // Send an email to all the admins
      public static function send_email_to_admins($subject, $message) {
            $admins = Imo_Admin_User_Model::get_admin_users(array());   
            foreach($admins as $admin) :
                $headers = "From: ".ORGANIZATION_NAME." <no-reply@".ORGANIZATION_EMAIl_DOMAIN.">\r\n"
                         . 'Reply-To: ' . $admin['email'] . "\r\n"
                         . 'X-Mailer: PHP/' . phpversion() . "\r\n"
                         . "MIME-Version: 1.0\r\n"
                         . 'Content-type: text/html; charset=UTF-8' . "\r\n";
          	    Mail::send($admin['email'], ORGANIZATION_NAME." <no-reply@".ORGANIZATION_EMAIl_DOMAIN.">", $subject,$message, $headers); 
            endforeach;
     }
    
    
    
      // Send emails for new media
      public  static function send_emails_new_media($subject,$input,$type) {
        // To author
        Mail_pages::send_email_to_user($input['user_id'],$subject,Mail_pages::add_new_media_to_author($input,$type));
        // Send email to admins
        Mail_pages::send_email_to_super_admins($subject,Mail_pages::add_new_media_to_admins($input,$type));   
      }
    




      // New Media posted
      // type = photo | video
      public static function add_new_media_to_admins($binds, $type) {
          
          // Mail to the user
          if($type=="photo"): 
             $message  = "<strong>". _('A new photo has been posted.') . "</strong><br/><br/>";
          elseif($type=="video"):  
             $message  = "<strong>". _('A new video has been posted.') . "</strong><br/><br/>";
          elseif($type=="camera"):     
             $message  = "<strong>". _('A new camera has been registered.') . "</strong><br/><br/>";
          endif;
          
          if($type!=="camera"): 
            $message .=  "This media needs <a href='". BASE_URL. "/members/imo_admin/'>to be approved</a>."; 
          else:
            $message .=  "You can review the camera <a href='". BASE_URL. "/cameras/'>here</a>."; 
          endif; 
          
		  $message .= _('Thank you!') . '<br/>';	
		  $message .= "<a href='http://" . ORGANIZATION_WEBSITE . "'>".ORGANIZATION_NAME."</a><br/>";
		  return $message;	
      }
      

      // New Media posted
      // type = photo | video
      public static function add_new_media_to_author($binds, $type) {
          
          // Mail to the user
          if($type=="photo"): 
             $message  = "<strong>". _('Thank you for your recent photo.') . "</strong><br/><br/>";
          elseif($type=="video"):  
             $message  = "<strong>". _('Thank you for your recent video.') . "</strong><br/><br/>";
          elseif($type=="camera"):
             $message  = "<strong>". _('Thank you for registering a new camera.') . "</strong><br/><br/>";
          endif;
          
          if($type=="photo"): 
              if($binds['pending'] == 1): 
                $message .= _("Your photo will be soon analysed by our team. As soon as it is approved, the photo will appear on your profile and on our photo gallery."). "<br/>";
              else: 
                $message .= _("The photo now appears on your profile and on our photo gallery. "). "<br/>";
              endif;
          elseif($type=="video"):  
               if($binds['pending'] == 1): 
                $message .= _("Your video will be soon analysed by our team. As soon as it is approved, the video will appear on your profile and on our video gallery."). "<br/>";
               else: 
                $message .= _("The video now appears on your profile and on our video gallery."). "<br/>";
               endif;
          elseif($type=="camera"):
                $message .= _("Your now appears on your profile and on our All Sky Camera list."). "<br/>";
          endif;
          
		  $message .= _("Thank you!") . "<br/>";	
		  $message .= "<a href='http://" . ORGANIZATION_WEBSITE . "'>".ORGANIZATION_NAME."</a><br/>";
		  return $message;	
      }

		
	  // Message to report author
	  public function add_new_report_message_to_author($binds) {
		  $message  = '<strong>'. _('Thank you for your recent report.') . '</strong><br/><br/>';
 		  $message .= _('Your report will be soon analysed by our team.'). '<br/>';
 		  
		  $message .= _('Your report will appear among the pending reports in few hours: '). '<br/>';
		  $message .= BASE_URL.'/members/imo_view/browse_reports?event=PENDING' . '<br/><br/>';
		  
		  $message .= _('If several people saw the same phenomenon and if this phenomenon is a fireball, your report will be grouped by our team with other reports into an event:') . '<br/>';
		  $message .=  BASE_URL.'/members/imo_view/browse_events' . '<br/><br/>';
		  
          if(!empty($binds['edit_link'])): 
            $message .= '<strong>'._('IMPORTANT:').'</strong>';
            $message .= _('You can modify your report only during the next 24 hours by clicking the following link:').'<br/>';
            $message .=  BASE_URL.'/members/tmp_edit/edit_report?edit_link=' .$binds['edit_link']. '<br/><br/>';
          endif;
          
		  if(defined('GOOGLE_PLAY_LINK') || defined('APPLE_STORE_APP_LINK')):  
          
              $message .= _('You can download the AMS app from the Apple App Store or the Android Play Store and update your report by entering the following key and your email in the Fireball Report section:<br/>'); 
              $message .=  '<strong>'.$binds['ams_app_id'] . '</strong><br/><br/>';
              
              if(defined('GOOGLE_PLAY_LINK')):  
                $message .=  _('Download the AMS App for Android device: '). '<br/>';
                $message .= GOOGLE_PLAY_LINK. '<br/><br/>';
              endif;		
              
              if(defined('APPLE_STORE_APP_LINK')):  
                $message .=  _('Download the AMS App for Iphone: '). '<br/>';
                $message .= APPLE_STORE_APP_LINK. '<br/><br/>';
              endif;	
              
          endif;     
			
		  $message .= _('Thank you!') . '<br/>';	
		  $message .= '<a href="http://' . ORGANIZATION_WEBSITE . '">'.ORGANIZATION_NAME.'</a><br/>';
		  return $message;	
		   
	  }
		
	
}

?>
