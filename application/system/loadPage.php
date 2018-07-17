<?php
 
    // Set the folders & controllers
    $func = 'index';
    $cont = '';
    $fold = '';
    $_serval_vars = array();

    $url = $_SERVER['REQUEST_URI'];
    if (!empty($_SERVER['REDIRECT_URL'])) {
        $url = $_SERVER['REDIRECT_URL'];
    }
    list($page) = explode('?', substr($url, 1));
  
    
    if (!empty($page)) {
        $parts = explode('/', $page);
 
	if ($parts[0] == 'members') {
            array_shift($parts);
        }

        $cont = array_shift($parts);

        if (empty($parts)) {
            $func = 'index';
        } else {
            $func = array_shift($parts);
            $_serval_vars = $parts;
        }
    } else {
        $cont = 'index';
    }

    if (file_exists( APP_DIR . '/controllers/' . $cont)) {
        $fold = "$cont/";
        $cont = $func;
    
        if (!empty($_serval_vars)) {
            $func = array_shift($_serval_vars);
        } else {
            $func = 'index';
        }
    }

    // Check that the controller exists
    if (!file_exists( APP_DIR . "/controllers/$fold$cont.php" )) {
        array_push($_serval_vars, $func);
        $func = $cont;
        $cont = 'index';
    }
 
    require_once( APP_DIR . "/controllers/$fold$cont.php" );
  
    // Cleanup & rename function namespace
    $_serval_function = $func;
    $_serval_controller = $cont . '_controller';

    // Create an instance of the controller class and call the function
    $_instance = new $_serval_controller($cont, $func);

    if (method_exists($_instance, $_serval_function)) {
        $_instance->$_serval_function($_serval_vars);
    } else {
        if ($cont == 'index') {
            //Logger::urlLog($_SERVER['REQUEST_URI'] . ' ::: Referrer: ' . $_SERVER['HTTP_REFERRER']);
        }

        array_push($_serval_vars, $_serval_function);
        $_instance->index($_serval_vars);
    }

    unset($cont, $func); 
    $_instance->render(); 