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
  
		$this->template->content = $content;
		$this->template->footer = new View('/shared/footer.html');
		
		
	}
	

 
    
    
	
}