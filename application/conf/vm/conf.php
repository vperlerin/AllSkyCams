<?
 
error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors', 'on'); 

// Require the database settings
//require('database.php');
 

define('MYSQL_DEBUG', true);

define ('DEV_OR_PROD', 'dev');
define ('SITE_URL','dev.allskycams.vm'); // Use for cookies on www.allskycams.org & allskycams.org

define ('BASE_URL', 'http://dev.allskycams.vm');
define ('ROOT_DIR', '/var/www/projects/AllSkyCams/site');
define ('APP_DIR',  ROOT_DIR . '/application');
define ('HTDOCS_DIR', ROOT_DIR . '/htdocs');
 
// Turn logging on or off
define ('ENABLE_LOGGING', true);
define ('LOG_DIR', ROOT_DIR . '/logs');

// Cookie Variables
define ('COOKIE_EXPIRE', 31536000);

// Memcache Settings
define ('MEMCACHE_ADDR', 'localhost');
define ('MEMCACHE_PORT', 11211);

// Add extra setup below
define ('SETUP', ROOT_DIR . '/application/conf/vm/setup/');
require_once( SETUP ."smtp.php");
