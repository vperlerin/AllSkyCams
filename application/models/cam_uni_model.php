<?php
   /* 
  // For the CAMS
    CREATE TABLE cam_uni (
        cam_id  INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        cam_network_id  INT(11),
        cam_code  VARCHAR(10) DEFAULT NULL,
        cam_geo_lat  DECIMAL(15,12) DEFAULT NULL,
        cam_geo_lng  DECIMAL(15,12) DEFAULT NULL,
        cam_geo_height  DECIMAL(20,12) DEFAULT NULL,
        cam_country VARCHAR(64) DEFAULT NULL,
        cam_geo_name  VARCHAR(50) DEFAULT NULL,
        operated_by VARCHAR(64) DEFAULT NULL,
        operated_by_user_id INT(11) DEFAULT NULL,
        link VARCHAR(200) DEFAULT NULL,
        public_notes VARCHAR(1000) DEFAULT NULL,
        reg_date TIMESTAMP
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8
  */

class Cam_Uni_Model {

    static private $cam_uni_optional_fields = array(
            'cam_network_id'        => 'auto',
            'cam_geo_name'          => 50,
            'cam_code'              => 10,
            'cam_country'           => 64,
            'operated_by'           => 64,
            'link'                  => 200,
            'public_notes'          => 1000,
            'operated_by_user_id'   => 'auto',
            'cam_geo_lat'           => 'auto',
            'cam_geo_lng'           => 'auto',
            'cam_geo_height'        => 'auto',
    );


    /*
    * Register several cams at once 
    */
    public function register_cams($cam_network_id, $all_cams) {

        $colums = array_keys(self::$cam_uni_optional_fields);
        $sql = "INSERT INTO cam_uni ";
        $sql .= "(".implode(',', $colums).") VALUES ";
      
        $values = array();
        $all_values = array();
 
        foreach($all_cams as $k=>$cam):
            $values[$k]['cam_network_id'] = $cam_network_id;
            $where[$k][] = 'cam_network_id = ' . $values[$k]['cam_network_id'];
            foreach($colums as $col):
                if($col!='cam_network_id'):
                    $values[$k][$col] = !empty($cam[$col])?(is_numeric($cam[$col])?$cam[$col]:'"'.$cam[$col].'"'):'NULL'; 
                    $where[$k][]        = $col . ' = ' . $values[$k][$col];
                endif;
            endforeach;
            $all_values[] = '(' . implode(',',$values[$k]) . ')';
        endforeach; 
       
        $sql .= implode(',',$all_values);
        unset($columns,$values, $all_values);  

        // We insert all the cams
        DBF::set($sql,array());

        // We return the all list of cam_ids/cam_code for the current network
        $sql = "SELECT cam_id, cam_code
                FROM   cam_uni
                WHERE  cam_network_id = " . $cam_network_id;

        return DBF::query($sql,array(),'num'); 
    }

    /*
    * Register a new camera
    */
    public function register_cam($binds) {
        $set = array();
        $errors = array();
        $sql = "INSERT INTO cam_uni";

        // Note: The required field are dealed with from the API itsefl
 
        // Test fields
        foreach($binds as $k=>$b):
            if(array_key_exists($k, self::$cam_uni_optional_fields)):
             
                if((strlen($b)<=self::$cam_uni_optional_fields[$k] && self::$cam_uni_optional_fields[$k]!='auto') || self::$cam_uni_optional_fields[$k]=='auto'):
                    $set[] = $k . '= :' . $k;
                else:
                    $errors[] = $k . " max lenght is " . self::$cam_uni_network_fields[$k] . " the length of " . $b . " is " . strlen($b); 
                endif;   

            endif;
         
        endforeach; 
         
        if(empty($errors)):
            $sql .= ' SET ' . implode(', ', $set);

            return DBF::set($sql,$binds);       
        else:
            return $errors;
        endif; 	
    }

    // Test is a cam_code (table cam_uni) is already in used in the network
    static public function test_cam_code_unique_in_network($binds) {
        
        $sql = "SELECT cam_id 
                FROM   cam_uni
                WHERE  cam_code = :cam_code
                AND    cam_network_id = :cam_network_id";
        $res = DBF::query($sql,$binds,'assoc');
        return empty($res);             
    }

    // Test is a combination cam_geo_lat|cam_geo_lng  is already in used in the network
    static public function test_cam_geoloc_unique_in_network($binds) {
        $sql = "SELECT cam_id 
                FROM   cam_uni
                WHERE  cam_geo_lat = :cam_geo_lat
                AND    cam_geo_lng = :cam_geo_lng 
                AND    cam_network_id = :cam_network_id";
        $res = DBF::query($sql,$binds,'assoc');
        return empty($res);             
    }


    // Test is a combination cam_code(s)|cam_network_id
    static public function test_cam_codes_cam_network_id($binds) {
        // Here we remove the duplicate IDs (we can have duplicates 
        // since the order of the ids is used to identified the source of the media)
        $tmp['cam_codes'] = array_unique($binds['cam_codes']);
   
        $sql = "SELECT cam_id 
                FROM   cam_uni
                WHERE  cam_network_id = :cam_network_id
                AND    cam_code IN ('".  implode("','",$tmp['cam_codes'])."') 
                AND    cam_code IS NOT NULL";

        $res = DBF::query($sql,$binds,'num');
      
        if(!empty($res) && count($binds['cam_codes'])===count($res)):
            // Default case: we have only unique cam_codes
            // Return list of cam_ids
            $to_return = array();
            foreach($res as $r):
                    $to_return[] = $r['cam_id'];
            endforeach;
            return $to_return;
        
        elseif(!empty($res)):
            $to_return = array();

            // It means we have at least twice the same cam_code
            // So we need to check them one by one
            foreach($binds['cam_codes'] as $cam_code):
                $sql = "SELECT  cam_id
                        FROM    cam_uni
                        WHERE   cam_code = '" . $cam_code . "'
                        AND     cam_network_id = :cam_network_id";
                $res = DBF::query($sql,$binds,'assoc');
                if(empty($res)):
                    return false;
                else:
                    $to_return[] = $res['cam_id'];
                endif;
            endforeach;
            
            return $to_return;
        else:
            return false;
        endif;
    }

    // Test is a combination cam_ids(s)|cam_network_id
    static public function test_cam_ids_cam_network_id($binds) {

        // Here we remove the duplicate IDs (we can have duplicates 
        // since the order of the ids is used to identified the source of the media)
        $tmp['cam_ids'] = array_unique($binds['cam_ids']);
 
        $sql = "SELECT cam_id 
                FROM   cam_uni
                WHERE  cam_network_id = :cam_network_id
                AND    cam_id IN ('".  implode("','",$tmp['cam_ids'])."') 
                ";
        $res = DBF::query($sql,$binds,'num');

        if(!empty($res)&&count($binds['cam_ids'])==count($res)):
            return true;
        elseif(!empty($res)):
            $to_return = array();

            // It means we have at least twice the same cam_id
            // So we need to check them one by one
            foreach($binds['cam_ids'] as $cam_id):
                $sql = "SELECT  cam_id
                        FROM    cam_uni
                        WHERE   cam_id = '" . $cam_id . "'
                        AND     cam_network_id = :cam_network_id";
                $res = DBF::query($sql,$binds,'assoc');
                if(empty($res)):
                    return false;
                else:
                    $to_return[] = $res['cam_id'];
                endif;
            endforeach;
            
            return $to_return;
        else:
            return false;
        endif;
    }

    /**
    * Test single cam_id toward cam_network
    */
    static public function test_cam_id_cam_network_id($binds) {
        
        $sql = "SELECT  cam_id
                FROM    cam_uni
                WHERE   cam_id = :cam_id
                AND     cam_network_id = :cam_network_id";
        $res = DBF::query($sql,$binds,'assoc');

        return(!empty($res));

    } 


    /**
    * Test single cam_code toward cam_network (return the cam_id)
    */
    static public function test_cam_code_cam_network_id($binds) {
        $sql = "SELECT  cam_id
                FROM    cam_uni
                WHERE   cam_code = :cam_code
                AND     cam_network_id = :cam_network_id";
        $res = DBF::query($sql,$binds,'assoc');

        if(empty($res)):
            return false;
        else:
            return $res['cam_id'];
        endif;  
    }  


    /**
    * Get the average geolocation of a set of cams passed in arg
    * via cam_ids
    */
    static public function get_avg_geoloc($binds) {
        $sql = "SELECT  AVG(cam_geo_lat) as event_lat,
                        AVG(cam_geo_lng) as event_lng
                FROM    cam_uni
                WHERE   cam_id IN ('".  implode("','",$binds['cam_ids'])."')";
 
        return DBF::query($sql,$binds,'num');
    }
}