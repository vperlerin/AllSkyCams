<?php 

class products_controller extends Template_Controller {


    /*
	 * All unknown pages will hit here 
	 */
	public function index() {

		$content = new View('/products/index.html');
		
		$this->template->header = new View('/shared/header.html');
		$this->template->header->activeMenu = "products"; 
		$this->template->header->title = "AllSkyCams Products"; 
      $this->template->header->description = "Discover all AllSkyCams Products: a complete solution for meteor and fireball detection, reduction and analysis.";

		$this->template->content = $content;
		$this->template->footer = new View('/shared/footer.html');
		
		
	}
	

 
    
    
	
}