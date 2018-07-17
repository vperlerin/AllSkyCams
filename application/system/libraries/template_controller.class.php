<?php

class Template_Controller {
    public $input;
    public $template;

    public function __construct($cont, $func) {
        $this->input = new stdClass();
        $this->input = array_merge($_GET, $_POST);
        $this->template = new stdClass();

        $this->user_auth_details = array();
        if (Cookie::get('sc_381') && isset($_SESSION[Cookie::get('sc_381')])) {
            $this->user_auth_details = $_SESSION[Cookie::get('sc_381')];
        }

        $this->page = $func;
        if ($func == 'index') {
            $this->page = $cont;
        }       
        
        unset ($cont, $func);
    }

    public final function render() {
        $_serval_page = '';
        $page_title = '';
		$active_menu ='';
        if (!empty($this->template)) {
            foreach ($this->template as $view) {
                $filename = $view->template;
                unset($view->template);

                ob_start();

                $vars = get_object_vars($view);

                @extract($vars, EXTR_SKIP);

          
                // Add user data to every page
                $user_auth_details = $this->user_auth_details;
                
                try {
                    include VIEWS . $filename;
                } catch (Exception $e) {
                    ob_end_clean();
                    throw $e;
                }

                $_serval_page .= ob_get_clean();
            }
        }

        DBF::close();

        // COMPRESSED HTML RENDERING
        print_r(preg_replace(array('/<!--(.*)-->/Uis',"/[[:blank:]]+/"),array('',' '),str_replace(array("\n","\r","\t"),'',$_serval_page)));
       // print_r($_serval_page  );
 

        // Cleanup all instaniated variables
        unset ($page, $vars, $view, $this);

        $defined_vars = get_defined_vars();
        if (!empty($defined_vars)) {
            foreach ($defined_vars as $var => $val) {
                unset ($$var);
            }

            unset ($defined_vars);
        }

 
    }

    public final function component() {
        $page = '';
        if (!empty($this->component)) {
            $filename = $this->component->component;
            unset($this->component->component);

            ob_start();

            $vars = get_object_vars($this->component);

            @extract($vars, EXTR_SKIP);

            $user_auth_details = $this->user_auth_details;

            try {
                include VIEWS . $filename;
            } catch (Exception $e) {
                ob_end_clean();
                throw $e;
            }

            $page .= ob_get_clean();
        }

        unset ($this->component, $vars, $filename);

        return $page;
    }
}

