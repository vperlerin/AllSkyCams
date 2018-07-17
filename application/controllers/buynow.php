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

		if(!empty($this->input['submit'])):
			$message = "Hi Mike,<br/><br/>";
			$message .= $this->input['firstname'] . ' ' . $this->input['lastname'] . ' (' .  $this->input['email'] . ' ) is interested in buying a <br/>';

			switch ($this->input['kits']) {
				case 'kit6-1':
					$message .= "<strong>A fully assembled AllSky6 kit ($1,899)</strong>";
					break;
				case 'kit6-2':
					$message .= "<strong>A fully assembled AllSky6 kit WITHOUT computer ($1,599)</strong>";
					break;
				case 'kit6-3':
					$message .= "<strong>A DIY AllSky6 kit ($1,199)</strong>";
					break;
				case 'kit6-4':
					$message .= "<strong>A DIY AllSky6 kit WITHOUT computer ($899)</strong>";
					break;
			}

			if(!empty($this->input['message'])):
				$message .= "<br/><br/>He added the following message to his order:<br/>" . str_replace("\n", '<br />', $this->input['message'] );
			endif;

			
			$this->input['res'] = Mail::send_contact_email(
				$this->input['email'],
				$this->input['firstname'] . ' ' . $this->input['lastname'],
				'AllSkyCams BUY - ' . $this->input['subject'],
				$message 
			);
			unset($message);
		endif;
  
		$this->template->content = $content;
		$this->template->content->input = $this->input;
		$this->template->footer = new View('/shared/footer.html');
		
		
	}
	

 
    
    
	
}