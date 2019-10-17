<?php 

class about_controller extends Template_Controller {


    /*
	 * All unknown pages will hit here 
	 */
	public function index() {

 		$content = new View('/about/index.html');
		
		$this->template->header = new View('/shared/header.html');
		$this->template->header->activeMenu = "about"; 
      $this->template->header->title = "About AllSkyCams"; 
      $this->template->header->description = "Who's behind AllSkyCams? Learn more about Mike Hankey.";
  
		$this->template->content = $content;
		$this->template->footer = new View('/shared/footer.html');
		
		
	}
	

 
    
    
	
}