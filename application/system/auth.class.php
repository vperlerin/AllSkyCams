<?php

class Auth {

    // Binds only needs to contain a user id if reload is false
    // or an email and password if reload is true for a site login
    // or just an email if reload is true and it's a facebook login
    public function check_user($binds, $reload = false) {
         
        if ($reload) {
             $result = Users_Model::get_user_details($binds);
        } else {
             $result = Users_Model::get_user_auth_details($binds);
        }
  
        if (!empty($result)) {
            
             $result['auth_type'] = $binds['auth_type'];        

            if (!empty($binds['access_token']) ) {
                $result['access_token'] = $binds['access_token'];
            }

            $session = md5(date("YmdHis") . $result['email'] . rand(99999,999999));
            
            if ($reload) {
                $session = Cookie::get('sc_381');
            } else {
                Cookie::set('sc_381', $session, false);
            }
            
            $_SESSION[$session] = $result;
            
            // New for paid members
            // Berfore return true;
            return array('result'=>true,'user_id'=>$result['user_id']);
        } 

        // New for paid members
        // Before return false;
        return array('result'=>false,'user_id'=>'');
    }

    public function admin_auth_required() {
        if(!empty($_SESSION[Cookie::get('sc_381')]) && $this->user_auth_details['user_type'] == 1) {
            return true;
        }
        redirect(BASE_URL); 
    }
    
    public function is_paid_member() {
        return !empty($_SESSION[Cookie::get('sc_381')]) && $this->user_auth_details['member_type']!=0 ;
    }
    
    public function paid_members_only($url='') {
        // We don't check the due date as it is done during login
        // (we should though - for security reason?)
        if(!empty($_SESSION[Cookie::get('sc_381')]) && $this->user_auth_details['member_type']!=0   ) {
             return true;
        }
        
        redirect("/members/user/paid_members_only?cur_page=". (empty($url)?$_SERVER['HTTP_REFERER']:$url)); 
    }
    
    //if page is set, the user will be redirected to the page he/she was trying to get to
    public function user_auth_required($page = "") {
 
        if (!isset($_SESSION[Cookie::get('sc_381')]) || !$_SESSION[Cookie::get('sc_381')]) {
            redirect('/members/user/login?redirect=/members' . $page);
        } else {
            // Update the cookie time;
            $sc = Cookie::get('sc_381');
            //Cookie::set('sc_381', $sc, true);
            Cookie::set('sc_381', $sc, false);
            unset($sc);
        }

        return true;
    }

    public static function is_user_logged_in() {
        if (!isset($_SESSION[Cookie::get('sc_381')]) || !$_SESSION[Cookie::get('sc_381')]) {
           return false;
        }
        return true;
    }
    
    // Check user type 
    // if user_type is found a cookie is set so we don't have to check the db all the time
    public static function get_member_type_and_due_date($user_id) {
         return Users_Model::get_member_type(array("user_id"=>$user_id));
    }
    
  

}
