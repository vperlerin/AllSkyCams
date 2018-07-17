<?php 

class index_controller extends Template_Controller {


    /*
	 * All unknown pages will hit here 
	 */
	public function index() {
 
        $content = new View('/index.html');
		
		$this->template->header = new View('/shared/header.html');
		$this->template->header->bodyClass = "home";
		$this->template->header->activeMenu = "home";
		$this->template->header->scrollToTop = true;
 
	    $this->template->content = $content;
	    $this->template->footer = new View('/shared/footer.html');
		
	}
	 
    
    
	
}