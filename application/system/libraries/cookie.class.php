<?php  /**
 * @author     Derek Sanford
 * @copyright  (c) 2011 servalphp.com
 */

class Cookie {
    function set($name, $value, $time = false) {

        $expire = 0;
        if ($time) {
            $expire = time() + COOKIE_EXPIRES;
        } else {
            //by default, the cookie lasts one day
            $expire = time() + 60 * 60 * 24;
        }
 
        if (setcookie($name, $value, $expire, '/', '.'.SITE_URL)) {
            $_COOKIE[$name] = $value;
            return true;
        }

        return false;
    }

    public static function get($name) {
        if (isset($_COOKIE[$name])) {
            return $_COOKIE[$name];
        }

        return false;
    }

    function getAll() {
        return $_COOKIE;
    }

    function delete($name) {
        if (setcookie($name, '', time() - 3600, '/')) {
            unset($_COOKIE[$name]);
            return true;
        }

        return false;
    }

    function debug() {
        pp($_COOKIE);
    }
}

