<?php

class poorWAF {

    private static $ip;
    private static $log_file;
    private static $ip_blacklist;

    public static function hacker_check() {
        self::$log_file = LOG_DIR . '/ip_blacklist.txt';
        self::$ip_blacklist = Cache::get('ip_blacklist');

        self::proxy_check();
        self::check_blacklist();
        self::scrub_input_values();
    }
    
    private static function check_blacklist() {
        if (empty(self::$ip_blacklist)) {
            self::load_blacklist_from_file();
        }
         /*
        if (in_array(self::$ip, self::$ip_blacklist) && self::$ip!='85.170.99.135') {
            redirect('/error1.html');
        }
 	 	 */
        return;
    }

    private static function load_blacklist_from_file() {
        $ip_blacklist = array();
        foreach(file(self::$log_file) as $row) {
            list($ip, $reason) = explode(' - ', $row);
            $ip_blacklist[] = trim($ip);
        }        
          
        self::$ip_blacklist = $ip_blacklist; 
        Cache::add('ip_blacklist', $ip_blacklist);

    }
    

    public static function scrub_input_values() {
        
        $str = http_build_query(array($_POST, $_GET, $_SERVER));
       
        $scanPattern = '~(\b(truncate|left join|right join|show databases|show tables|getcwd|eval|fopen|file_put_contents|phpinfo|httpd.conf|\x00)\b|\x00)~i';

        if (preg_match_all($scanPattern, $str, $matches)) {
            $first_offender = str_replace("\x00", '\x00', $matches[0][0]);

            $log_entry = self::$ip . ' - ' . $first_offender . "\n";
            file_put_contents(self::$log_file, $log_entry, FILE_APPEND);
            
            $subject = "AMS WAF Blocking Alert";
            Mail::send(DEVELOPER_EMAIL_ADDRESSES, "Developer Alert <developer-alert@amsmeteors.org>", $subject, $log_entry);
            self::load_blacklist_from_file();
            self::check_blacklist();
        }
        
        return;
    }

    private static function proxy_check () {
        $proxy = false;
        $ip    = $_SERVER['REMOTE_ADDR'];
 
        if (!empty($_SERVER['HTTP_VIA']) || !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $proxy = true;
        } elseif (!empty($_SERVER['REMOTE_HOST'])) {
            $aProxyHosts = array(
                'proxy',
                'cache',
                'inktomi');

            foreach ($aProxyHosts as $proxyName) {
                if (strpos($_SERVER['REMOTE_HOST'], $proxyName) !== false) {
                    $proxy = true;
                    break;
                }
            }
        }

        if ($proxy) {
            $aHeaders = array(
                'HTTP_FORWARDED',
                'HTTP_FORWARDED_FOR',
                'HTTP_X_FORWARDED',
                'HTTP_X_FORWARDED_FOR',
                'HTTP_CLIENT_IP');

            foreach ($aHeaders as $header) {
                if (!empty($_SERVER[$header])) {
                    $ip = $_SERVER[$header];
                    break;
                }
            }
        }

        self::$ip = $ip;

        return;
    }
}

?>
