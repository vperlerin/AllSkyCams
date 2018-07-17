<?php


class Cache {
    private $conn;

    private static function conn() {
        
        if (class_exists('Memcache')) {
            $conn = new Memcache();
        } else {
            $conn = new Memcached();
        }
        
        $conn->addServer(MEMCACHE_ADDR, MEMCACHE_PORT); 
        return $conn;
    }

   // $expire used to default to 3600 (one hour)
   // 14400 = > 4 hours
   // before it defaulted to 30 days  (2592000)
    public static function add($key, $val, $flag = false, $expire = 14400) {
        
 
        $conn = self::conn();
        
        if (!empty($key)) {
            if (!$conn->replace($key, $val,false,$expire)) {
                return $conn->set($key, $val,false,$expire);
            }
         
            return true;
        } 

        return false;
    }

    public static function get($key) {
         $conn = self::conn();
        
        if (!empty($key)) {
            return $conn->get($key,false);
        }

        return false;
    }


    public static function flush() {
        $conn = self::conn();
        $conn->flush();
    }

    function delete($key) {
        if (!empty($key)) {
            $conn = self::conn();
            return $conn->delete($key);
        }

        return false;
    }
} 

