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
  
		$this->template->content = $content;
		$this->template->footer = new View('/shared/footer.html');
		
		
	}
	

 
    
    
	
}