<?php 

class support_controller extends Template_Controller {


    /*
	 * All unknown pages will hit here 
	 */
	public function index() {

 		$content = new View('/support/index.html');
		
		$this->template->header = new View('/shared/header.html');
		$this->template->header->activeMenu = "support"; 
		$this->template->header->title = "AllSkyCams Support"; 


		if(!empty($this->input['submit'])):
			$this->input['res'] = Mail::send_contact_email(
				$this->input['email'],
				$this->input['firstname'] . ' ' . $this->input['lastname'],
				'AllSkyCams Support - ' . $this->input['subject'],
				str_replace("\n", '<br />', $this->input['message'] )
			);
		endif;
  
		$this->template->content = $content;
		$this->template->content->input = $this->input;
		$this->template->footer = new View('/shared/footer.html');
		
		
	}
	

 
    
    
	
}