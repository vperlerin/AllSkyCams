<?php 

class network_controller extends Template_Controller {


    /*
	 * All unknown pages will hit here 
	 */
	public function index() {

 		$content = new View('/network/index.html');
		
		$this->template->header = new View('/shared/header.html');
		$this->template->header->activeMenu = "network"; 
		$this->template->header->title = "AllSkyCams Network"; 
  
		$this->template->content = $content;
		$this->template->footer = new View('/shared/footer.html');
		
		
	}
	

 
    
    
	
}