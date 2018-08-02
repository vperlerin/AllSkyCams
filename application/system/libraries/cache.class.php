<?php


class Cache {
    private $conn;

    private static function conn() {
        $conn = new Memcached();
        $conn->addServer(MEMCACHE_ADDR, MEMCACHE_PORT); 
        return $conn;
    }
 
    public static function add($key, $val, $flag = false, $expire = 14400) {
        $conn = self::conn();
        
        if (!empty($key)) {
            if (!$conn->replace($key, $val, $expire)) {
                return $conn->set($key, $val, $expire);
            }
            return true;
        } 

        return false;
    }

    public static function get($key) {
         $conn = self::conn();
        
        if (!empty($key)) {
            return $conn->get($key);
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

