<?php 

class buynow_controller extends Template_Controller {


    /*
	 * All unknown pages will hit here 
	 */
	public function index() {

 		$content = new View('/buynow/index.html');
		
		$this->template->header = new View('/shared/header.html');
		$this->template->header->activeMenu = "buynow"; 
      	$this->template->header->title = "Buy AllSkyCams Now"; 
      	$this->template->header->description = "Buy AllSkyCams products and get the latest technology for sky 24/7 survey.";


		if(!empty($this->input['submit'])):
			$message = "Hi Mike,<br/><br/>";
			$message .= $this->input['firstname'] . ' ' . $this->input['lastname'] . ' (' .  $this->input['email'] . ' ) is interested in buying ';

			switch ($this->input['kits']) {
				case 'kit7-1':
					$message .= "<strong>An AllSky7 bundle WITH computer ($1,599)</strong>";
					break;
				case 'kit7-2':
					$message .= "<strong>An AllSky7 bundle WITHOUT computer ($1,299)</strong>";
					break; 
			}

			if(!empty($this->input['message'])):
				$message .= "<br/><br/>He added the following message to his order:<br/>" . str_replace("\n", '<br />', $this->input['message'] );
			endif;

			
			$this->input['res'] = Mail::send_contact_email(
				$this->input['email'],
				$this->input['firstname'] . ' ' . $this->input['lastname'],
				'AllSkyCams BUY',
				$message 
         );
         
         #pp($this->input);
         #exit;
			unset($message);
		endif;
  
		$this->template->content = $content;
		$this->template->content->input = $this->input;
		$this->template->footer = new View('/shared/footer.html');
		
		
	}
	

 
    
    
	
}