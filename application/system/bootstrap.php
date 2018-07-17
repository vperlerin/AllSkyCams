<? 

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 'on'); 

if(!defined('STARTUP_ONLY')) {
    session_start(); 
}

// Pull in the necessary classes for use if needed
$paths = array(
    'conf'      => APP_DIR . '/conf',
    'lib'       => APP_DIR . '/system/libraries',
    'models'    => APP_DIR . '/models',
    'helpers'   => APP_DIR . '/helpers',
);

foreach ($paths as $path) {
    set_include_path(get_include_path() . PATH_SEPARATOR . $path);
 }

require_once('config.php');

spl_autoload_extensions(".class.php,.php");
spl_autoload_register();

 if(!defined('STARTUP_ONLY')) {
        require_once('loadPage.php');
 }

// Poor Man's WAF
if (!defined('FROM_CRON')) {
	poorWAF::hacker_check();
}
?>

