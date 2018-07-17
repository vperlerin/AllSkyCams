<?php

class imo_admin_api_controller extends Template_Controller {

    function __construct($cont, $func) {
        parent::__construct($cont, $func);
    }
	
    // Unlink a report from an event (after YOUTUBE Fail)
    public function unlink_report_auto($report_id,$event_id) {
           // Test if the report has a media
           $rep = Imo_report_model::has_media(array('report_ids' => array($report_id)), false);
           
           if(!empty($rep)):
  
						// We get all the report info
						$report_data = Imo_Report_Model::get_report(array('report_id'=>$report_id));
						$report_data = $report_data[0]; 
                          
                        // Has a Photo    
						if(!empty($report_data['photo']) && !empty($report_data['photo_ext'])): 
                                Imo_photo_model::unlink_from_event(array(
                                    'photo_id' => $report_data['photo']
                                ));    
                        endif;
                        
						// Has a Video    
						if(!empty($report_data['video_id'])):  
                                Imo_video_model::unlink_from_event(array(
                                    'video_id' => $report_data['video_id']
                                ));    
                        endif;                       
                
           endif;	
 
           Event_Model::unlink_report(array('report_id' => $report_id, 'event_id' => $event_id));
           
    }
    
    
	// Unlink a report from an event (JSON VERSION)
	public function unlink_report() {
		$status = false;
        $errors = array();
   		
        if( Auth::is_user_logged_in()) {
			 if(empty($this->input['report_id']) || empty($this->input['event_id'])) {
			 	$errors[] = _('Please, enter a report_id and an event_id.');
            } else {

            	// Test if the report has a media
            	$rep = Imo_report_model::has_media(array('report_ids' => array($this->input['report_id'])), false);
            
            	if(!empty($rep)):
  
						// We get all the report info
						$report_data = Imo_Report_Model::get_report(array('report_id'=>$this->input['report_id']));
						$report_data = $report_data[0]; 
                          
                        // Has a Photo    
						if(!empty($report_data['photo']) && !empty($report_data['photo_ext'])): 
                                Imo_photo_model::unlink_from_event(array(
                                    'photo_id' => $report_data['photo']
                                ));    
                        endif;
                        
						// Has a Video    
						if(!empty($report_data['video_id'])):  
                                Imo_video_model::unlink_from_event(array(
                                    'video_id' => $report_data['video_id']
                                ));    
                        endif;                       
                
            	endif;	
 
				Event_Model::unlink_report(array('report_id' => $this->input['report_id'], 'event_id' => $this->input['event_id']));
                $status = 1;
 
            }
		} else  {
            $errors[] = "Access denied.";
        }

        $json = new JSON_Response();
        $json->status = $status;
        $json->input  = $this->input;
        $json->result = array();
        $json->errors = $errors;
        $json->print_response();
	}
	
	
	
	// Inactivate a set of reports 
	public function inactivate_reports() {
        $status = false;
        $errors = array();
		
        if( Auth::is_user_logged_in()) {
            if(empty($this->input['report_ids'])) {
			    $errors[] = _('Please, enter at least one report_id.');
            } else {
                 
                $binds = array('report_ids' => $this->input['report_ids']);
                
                // Get the reports details to know if it has a photo and/or a video
                $rep = Imo_report_model::has_media($binds);
             
                 if(!empty($rep)):
                 
                    foreach($rep as $report):
                        
                        // Remove Photos
                        if(!empty($report['photo']) && !empty($report['photo_ext'])):
                            
                            // Get photo dir 
                            $dir = IMO_photo_model::get_dir(array('photo_id'=>$report['photo']));  
                             
                            // We remove the photo from the report: it's lost forever
                            Imo_report_model::remove_photo(array('report_id'=>$report['report_id']));   
                       
                            // We remove the photo itself from the DB
                            Photo_Helper::delete_photo(array('photo_id'=>$report['photo'],'user_id'=>$report['user_id']));
                             
                        endif;
                        
                        
                        // Remove Videos
                        if(!empty($report['video_id'])):
                            
                            // Get the video info 
                            $video = IMO_video_model::get_video(array('video_id'=>$report['video_id']));
                            
                            if(empty($video['youtube_id']) && empty($video['vimeo'])): 
                            
                                    // If it's an uploaded video 
                                    
                                    // We remove the video first 
                                    // http://dev.amsmeteors.vm/members/upload/videos/2017/522.avi
                                    unlink(PUBLIC_UPLOAD . '/videos/'  . $video['video_dir'] . "/" .   $video['video_id']  . "." .  $video['ext']);
                            endif;
                            
                             // It's a YT or Vimeo video
                             IMO_video_model::remove_video(array('video_id'=>$report['video_id']));
                              
                             // We remove the video from the report: it's lost forever
                             Imo_report_model::remove_video(array('report_id'=>$report['report_id']));   
                         
                        endif;
                        
                    endforeach;
                endif;
                
			 
                Report_Model::inactivate_reports($binds);
                $status = 1;
            }
		} else  {
            $errors[] = "Access denied.";
        }

        $json = new JSON_Response();
        $json->status = $status;
        $json->input  = $this->input;
        $json->result = array();
        $json->errors = $errors;
        $json->print_response();

	}
	
	
	// Get Single report (used to refresh table row after a report has been edited on the admin)
	public function get_report() {
			$result = Data_cleaner::clean_reports_for_admin(Report_model::get_report_by_id(array('report_id'=>$this->input['report_id'])));
			$json = new JSON_Response();
			$json->status = $status;
			$json->input  = $this->input;
			$json->result = $result;
			$json->errors = $errors;
			$json->print_response();
	}
	
	
	// Edit report 
	public function edit_report() {
        $status = false;
        $errors = array();
		$result = false;
		
        if( Auth::is_user_logged_in()) {
			   	// Clean data (TODO: validation here)
				$binds['report_id'] 				= $this->input['report_id'];
			   	$binds['address'] 					= $this->input['address'];
			 	$binds['city'] 	  					= $this->input['city'];			
			 	$binds['state']	  					= $this->input['state'];	
				$binds['country'] 					= $this->input['country']; 	// Abbreviation
				$binds['report_date_local'] 		= $this->input['report_date_local'];
			   	$binds['report_date_utc'] 			= $this->input['report_date_utc'];
			   	$binds['timezone'] 					= $this->input['timezone'];
			   	$binds['time_type'] 				= (date("I", strtotime($binds['report_date_local']))) ? "DAYLIGHTSAVING" : "STANDARD";
				$binds['latitude']  				= $this->input['latitude'];
				$binds['longitude'] 				= $this->input['longitude'];
				$binds['altitude']  				= $this->input['altitude'];
				$binds['moving_direction']  		= $this->input['moving_direction'];
				$binds['descent_angle']				= $this->input['descent_angle'];
				$binds['magnitude']  				= $this->input['magnitude'];		
                $binds['duration']  				= $this->input['durationNormalized'];	
				$binds['color']  					= $this->input['color'];      
                $binds['looking_azimuth']			= $this->input['looking_azimuth'];
				$binds['initial_azimuth']			= $this->input['initial_azimuth'];	
				$binds['final_azimuth']				= $this->input['final_azimuth'];
				$binds['initial_altitude']			= $this->input['initial_altitude'];
				$binds['final_altitude']			= $this->input['final_altitude'];
				$binds['train']						= $this->input['train'];
				$binds['train_duration']			= $this->input['train_duration'];
				$binds['train_length']				= $this->input['train_length'];
				$binds['train_remarks']				= !empty($this->input['train_remarks'])?$this->input['train_remarks']:'';
				$binds['terminal_flash']			= $this->input['terminal_flash'];
				$binds['terminal_flash_remarks'] 	= $this->input['terminal_flash_remarks'];
                $binds['fragmentation']				= $this->input['fragmentation'];         
                $binds['fragmentation_remarks'] 	= $this->input['fragmentation_remarks'];         
				$binds['concurrent_sound']			= $this->input['concurrent_sound'];         
                $binds['concurrent_sound_remarks'] 	= $this->input['concurrent_sound_remarks']; 
                $binds['delayed_sound']				= $this->input['delayed_sound'];         
                $binds['delayed_sound_remarks'] 	= $this->input['delayed_sound_remarks']; 
                $binds['general_remarks'] 			= $this->input['general_remarks'];
                $binds['rating']		  			= $this->input['rating'];
				$binds['first_name']				= $this->input['first_name'];
				$binds['last_name']					= $this->input['last_name'];
				$binds['email']						= $this->input['email'];
 
				$result = Report_model::edit_report($binds);
				
				// If the report belongs to an event
				// the event needs to be updated too
				
           	
		} else  {
            $errors[] = "Access denied.";
        }

        $json = new JSON_Response();
        $json->status = $status;
        $json->input  = $this->input;
        $json->result = $result;
        $json->errors = $errors;
        $json->print_response();
	}
	 
	 
	 
	/**
	 * Link a set of reports to an event 
	*/
	public function link_reports_to_event() {
		if(empty($this->input['event_id']) || empty($this->input['report_ids'])) {
			$errors[] = _('Please, enter an event_id and at least a report_id.');
			$new_old_witness_id = -1;
			$old_id = -1;
		
		} else {
			
			// COULD BE OPTIMIZED
			
			$array_of_witness_ids = array();
			$array_of_report_ids = json_decode($this->input['report_ids']);
				
			foreach($array_of_report_ids as $report_id) {
					
					// Get witness ID for current event
					$new_old_witness_id = $this->get_latest_witness_id($this->input['event_id']);
					$array_of_witness_ids[] = $new_old_witness_id;
					
					// Update ams_report table
					Report_model::link_report_to_event(
						array('old_witness_id' => $new_old_witness_id,
							  'event_id'	   => $this->input['event_id'],
							  'report_id'	   => $report_id
					));
					
					// Update ams_event, event_country and event_center tables
					Event_model::update_event(array('event_id'	 => $this->input['event_id'], 'report_id' => $report_id));

					// Get Event year and Event ID Year (type year|id) - just to be able to display the link to the event from the admin 
					$old_id = Event_model::get_event_id_and_year_from_db_format(array('event_id'=> $this->input['event_id']));		

					// Test if the report has a media
					$rep   = Imo_report_model::has_media(array('report_ids' => array($report_id)), false);
                    
					if(!empty($rep)):
 
						// We get all the report info
						$report_data = Imo_Report_Model::get_report(array('report_id'=>$report_id));
						$report_data = $report_data[0];
                        
						// Test if we have the email in the database
						$user_id = Users_model::get_user_id_from_email(array('email'=>$report_data['email']));  
                          	
                        // Has a photo    
						if(!empty($report_data['photo']) && !empty($report_data['photo_ext'])): 

                            // Update the photo with the data collected from the report
                            $binds = array(
								'fireball_event_id' => $this->input['event_id'],
								'photo_name'        => ORGANIZATION_ABBREVIATION . ' event #' . $old_id[1] . '-' . $old_id[0],
								'photo_credit'      => $report_data['first_name'][0] . '. ' . $report_data['last_name'],
								'photo_desc'        => 'AMS Event: <a href="/members/imo_view/event/'.$old_id[1].'/'. $old_id[0] . '">'.  $old_id[1] . '-' . $old_id[0] . '</a>, Report #' . $report_id. ' (' . $old_id[1] . $new_old_witness_id . '-' . $old_id[0] .')',
								'photo_date'  		=> date('Y-m-d',strtotime($report_data['report_date_utc'])),
								'photo_date_local'  => $report_data['report_date_local'],
								'photo_date_utc'    => $report_data['report_date_utc'],
								'lat'               => $report_data['latitude'],
								'lng'               => $report_data['longitude'],
								'elv'               => $report_data['altitude'],
								'location'          => $report_data['city'] . ' ' . $report_data['country'],
								'time_zone'         => $report_data['timezone'],
								'photo_time'		=> date('H:i:s',strtotime($report_data['report_date_local'])),
								'fireball'          => 1,
								'thumb_x'           => 0,
								'thumb_y'           => 0,
								'thumb_w'           => 0,
								'thumb_h'           => 0,
								'photo_id'			=> $report_data['photo']
                            );
                                
                               
                            if($user_id):
                                $binds['user_id'] = $user_id;
                            endif;      
   
							Imo_photo_model::add_photo($binds,0);    
 
						endif;
                         
                         
						// Has a video
						if(!empty($report_data['video_id'])):
                            $binds = array(
                                'fireball_event_id' => $this->input['event_id'],
                                'video_date_utc'    => $report_data['report_date_utc'],
                                'video_name'        => ORGANIZATION_ABBREVIATION . ' event #' . $old_id[1] . '-' . $old_id[0],
                                'video_credit'      => $report_data['first_name'][0] . '. ' . $report_data['last_name'],
                                'video_desc'        => 'This video has been uploaded on the American Meteor Society Website. AMS Event: ' . $old_id[1] . '-' . $old_id[0] . ', Report ' . $report_id. ' (' . $old_id[1] . $new_old_witness_id . '-' . $old_id[0] .')',
                                'lat'               => $report_data['latitude'],
								'lng'               => $report_data['longitude'],
								'elv'               => $report_data['altitude'],
								'location'          => $report_data['city'] . ' ' . $report_data['country'],
								'time_zone'         => $report_data['timezone'],
								'video_date'        => date('Y-m-d',strtotime($report_data['report_date_utc'])),
								'video_date_local'  => $report_data['report_date_local'],
								'video_time '       => date('H:i:s',strtotime($report_data['report_date_local'])),
                                'video_id'          => $report_data['video_id'],
                                'fireball'          => 1 
                             );
                             
                             if($user_id):
                                $binds['user_id'] = $user_id;
                             endif;  
                             
                             Imo_video_model::update_video($binds);
                 
                             $r = Video_Helper::approve_video_from_report($report_data['video_id']);
                                 
                             if($r!=1):
                                $errors[] = "Impossible to upload the video on Youtube.<br/>"  . $r;
                                
                                // In this case we cannot create the event. 
                                // So we have to remove the report from the event 
                                // And eventually remove the event - as it can't be create with the video
                                $this::unlink_report_auto($report_id,$this->input['event_id']);
                                
                             endif;
                        endif;
                        
                        
					endif;
					
			}
		}
		
		$json = new JSON_Response();
		
        $json->witness_ids =  json_encode($array_of_witness_ids);
        $json->event_id = $this->input['event_id'];
        $json->old_event_id = $old_id[0].'|'.$old_id[1];
		$json->input  = $this->input;
        $json->result = array();
        $json->errors = $errors;
        $json->print_response();
		
	}
	 
	 
	/**
	 * Link a report to an event
	*/
	public function link_report_to_event() {
		$errors = array();
		
		if(empty($this->input['event_id']) || empty($this->input['report_id'])) {
			
            $errors[] = _('Please, enter an event_id and a report_id.');
			$new_old_witness_id = -1;
			$old_id = -1;
            
		} else {
		
			// Get witness ID for current event
			$new_old_witness_id = $this->get_latest_witness_id($this->input['event_id']);
			
			// Update ams_report table
			Report_model::link_report_to_event(array('old_witness_id' => $new_old_witness_id,
											  'event_id'	   => $this->input['event_id'],
											  'report_id'	   => $this->input['report_id']
			));
			
			// Update ams_event, event_country and event_center tables
			Event_model::update_event(array('event_id'	 => $this->input['event_id'], 'report_id' => $this->input['report_id']));
			
			// Get Event year and Event ID Year (type year|id) - just to be able to display the link to the event from the admin 
			$old_id = Event_model::get_event_id_and_year_from_db_format(array('event_id'=> $this->input['event_id']));

			// Test if the report has a media
			$rep   = Imo_report_model::has_media(array('report_ids' => array($this->input['report_id'])), false);
                    
			if(!empty($rep)):
 
				// We get all the report info
				$report_data = Imo_Report_Model::get_report(array('report_id'=>$this->input['report_id']));
                $report_data = $report_data[0];
                
                // Test if we have the email in the database
                $user_id = Users_model::get_user_id_from_email(array('email'=>$report_data['email']));  
                
                          	
				if(!empty($report_data['photo']) && !empty($report_data['photo_ext'])):
 
                    // Update the photo with the data collected from the report
                  $binds = array(
                      'fireball_event_id' => $this->input['event_id'],
                      'photo_name'        => ORGANIZATION_ABBREVIATION . ' event #' . $old_id[1] . '-' . $old_id[0],
                      'photo_credit'      => $report_data['first_name'][0] . '. ' . $report_data['last_name'],
                      'photo_desc'        => 'AMS Event: <a href="/members/imo_view/event/ '.$old_id[1].'/'. $old_id[0] . '">'.  $old_id[1] . '-' . $old_id[0] . '</a>, Report #' . $report_id . ' (' . $old_id[1] . $new_old_witness_id . '-' . $old_id[0] .')',
                      'photo_date'  		=> date('Y-m-d',strtotime($report_data['report_date_utc'])),
                      'photo_date_local'  => $report_data['report_date_local'],
                      'photo_date_utc'    => $report_data['report_date_utc'],
                      'lat'               => $report_data['latitude'],
                      'lng'               => $report_data['longitude'],
                      'elv'               => $report_data['altitude'],
                      'location'          => $report_data['city'] . ' ' . $report_data['country'],
                      'time_zone'         => $report_data['timezone'],
                      'photo_time'		=> date('H:i:s',strtotime($report_data['report_date_local'])),
                      'fireball'          => 1,
                      'thumb_x'           => 0,
                      'thumb_y'           => 0,
                      'thumb_w'           => 0,
                      'thumb_h'           => 0,
                      'photo_id'			=> $report_data['photo']
                  );
                      
                     
                  if($user_id):
                      $binds['user_id'] = $user_id;
                  endif;      

                  Imo_photo_model::add_photo($binds,0);    
                               
				endif;
                
                
                // Has a video
                if(!empty($report_data['video_id'])):
                    $binds = array(
                        'fireball_event_id' => $this->input['event_id'],
                        'video_date_utc'    => $report_data['report_date_utc'],
                        'video_name'        => ORGANIZATION_ABBREVIATION . ' event #' . $old_id[1] . '-' . $old_id[0],
                        'video_credit'      => $report_data['first_name'][0] . '. ' . $report_data['last_name'],
                         'video_desc'        => 'This video has been uploaded on the American Meteor Society Website. AMS Event: ' . $old_id[1] . '-' . $old_id[0] . ', Report ' . $report_id. ' (' . $old_id[1] . $new_old_witness_id . '-' . $old_id[0] .')',
                        'lat'               => $report_data['latitude'],
                        'lng'               => $report_data['longitude'],
                        'elv'               => $report_data['altitude'],
                        'location'          => $report_data['city'] . ' ' . $report_data['country'],
                        'time_zone'         => $report_data['timezone'],
                        'video_date'        => date('Y-m-d',strtotime($report_data['report_date_utc'])),
                        'video_date_local'  => $report_data['report_date_local'],
                        'video_time '       => date('H:i:s',strtotime($report_data['report_date_local'])),
                        'video_id'          => $report_data['video_id'],
                        'fireball'          => 1 
                     );
                     
                     if($user_id):
                        $binds['user_id'] = $user_id;
                     endif;  
                     
                     Imo_video_model::update_video($binds);
         
                      $r = Video_Helper::approve_video_from_report($report_data['video_id']);
                      if($r!=1):
                        $errors[] = "Impossible to upload the video on Youtube.<br/>"  . $r;
                        
                        // In this case we cannot create the event. 
                        // So we have to remove the report from the event 
                        // And eventually remove the event - as it can't be create with the video
                        $this::unlink_report_auto($this->input['report_id'],$this->input['event_id']);
                     endif;
                endif;
                         
			endif;

		}
		
		$json = new JSON_Response();
        $json->result     = $new_old_witness_id;
		$json->old_event_id	  = $old_id[1];
		$json->event_year	  = $old_id[0];
        $json->errors     = $errors;
        $json->input      = $this->input;
        $json->print_response();
	}
	
	
	/**
	* Create a new event from one or more reports (report_ids passed in a JSON array)
	*/
	public function create_event() {
       
		 if( Auth::is_user_logged_in()) {
             
            if(empty($this->input['report_ids'])) {
			    $errors[] = _('Please, enter at least one report_id.');
            } else {
                
				$array_of_witness_ids = array();
				$array_of_report_ids  = json_decode($this->input['report_ids']); 
				
				// Get Event Id within the year (of the first report passed into arg)
				// The event id is for the id with the format YEAR|ID
				$event_old_id = Event_model::get_new_old_id(array('report_id'=>$array_of_report_ids[0]));
  				
				// Create empty event to fill with all the reports data 
				$event_id = Event_model::create_empty_event($event_old_id);
				
				// Create empty row in event_center
				Geoloc_model::create_empty_center_point(array('event_id'=>$event_id));
  				
				// Link each report to this event 
				foreach($array_of_report_ids as $report_id) {
 				 
					// Get witness ID for current event
					$new_old_witness_id = $this->get_latest_witness_id($event_id); 
					
					// To Return to JSON
					$array_of_witness_ids[] = $new_old_witness_id;
					
					// Update ams_report table
					Report_model::link_report_to_event(
						array('old_witness_id' => $new_old_witness_id,
					          'event_id'	   => $event_id,
					          'report_id'	   => $report_id
					));

					// Update ams_event, event_country and event_center tables
					Event_model::update_event(array('event_id' => $event_id, 'report_id' => $report_id));
					
                    // Test if the report has a media
                    $rep  = Imo_report_model::has_media(array('report_ids' => array($report_id)), false);
                    
                     if(!empty($rep)):
  
						// We get all the report info
						$report_data = Imo_Report_Model::get_report(array('report_id'=>$report_id));
						$report_data = $report_data[0]; 
                         
                        $old_id = explode('|',$event_old_id); 
                        
                        // Test if we have the email in the database
                        $user_id = Users_model::get_user_id_from_email(array('email'=>$report_data['email']));     
                           	
                        // Has a Photo    
						if(!empty($report_data['photo']) && !empty($report_data['photo_ext'])): 
         
                            // Update the photo with the data collected from the report
                            $binds = array(
								'fireball_event_id' => $event_id,
								'photo_name'        => ORGANIZATION_ABBREVIATION . ' event #' . $old_id[1] . '-' . $old_id[0],
								'photo_credit'      => $report_data['first_name'][0] . '. ' . $report_data['last_name'],
                                'photo_desc'        => "AMS Event: <a href='/members/imo_view/event/".$old_id[1]."/". $old_id[0] . "'>".  $old_id[1] . "-" . $old_id[0] . "</a>, Report #" . $report_id. " (" . $old_id[1] . $new_old_witness_id . "-" . $old_id[0] .")",
                                'photo_date'  		=> date('Y-m-d',strtotime($report_data['report_date_local'])),
								'photo_date_local'  => $report_data['report_date_local'],
								'photo_date_utc'    => $report_data['report_date_utc'],
								'lat'               => $report_data['latitude'],
								'lng'               => $report_data['longitude'],
								'elv'               => $report_data['altitude'],
								'location'          => $report_data['city'] . ' ' . $report_data['country'],
								'time_zone'         => $report_data['timezone'],
								'photo_time'		=> date('H:i:s',strtotime($report_data['report_date_local'])),
								'fireball'          => 1,
								'thumb_x'           => 0,
								'thumb_y'           => 0,
								'thumb_w'           => 0,
								'thumb_h'           => 0,
								'photo_id'			=> $report_data['photo']
                            );
                                
                            if($user_id):
                                $binds['user_id'] = $user_id;
                            endif;  
   
							Imo_photo_model::add_photo($binds,0);    
                            
                          endif; 
                          
                        // Has a Video
						if(!empty($report_data['video_id'])):  
                        
                            $binds = array(
                                'fireball_event_id' => $event_id,
                                'video_date_utc'    => $report_data['report_date_utc'],
                                'video_name'        => ORGANIZATION_ABBREVIATION . ' event #' . $old_id[1] . '-' . $old_id[0],
                                'video_credit'      => $report_data['first_name'][0] . '. ' . $report_data['last_name'],
                                'video_desc'        => 'This video has been uploaded on the American Meteor Society Website. AMS Event: ' . $old_id[1] . '-' . $old_id[0] . ', Report ' . $report_id. ' (' . $old_id[1] . $new_old_witness_id . '-' . $old_id[0] .')',
                                'lat'               => $report_data['latitude'],
								'lng'               => $report_data['longitude'],
								'elv'               => $report_data['altitude'],
								'location'          => $report_data['city'] . ' ' . $report_data['country'],
								'time_zone'         => $report_data['timezone'],
								'video_date'        => date('Y-m-d',strtotime($report_data['report_date_utc'])),
								'video_date_local'  => $report_data['report_date_local'],
								'video_time '       => date('H:i:s',strtotime($report_data['report_date_local'])),
                                'video_id'          => $report_data['video_id'],
                                'fireball'          => 1 
                             );
                             
                             if($user_id):
                                $binds['user_id'] = $user_id;
                             endif;  
                             
                             Imo_video_model::update_video($binds);
                 
                             $r = Video_Helper::approve_video_from_report($report_data['video_id']);
                                 
                             if($r!=1):
                                $errors[] = "Impossible to upload the video on Youtube.<br/>"  . $r;
                                
                                // In this case we cannot create the event. 
                                // So we have to remove the report from the event 
                                // And eventually remove the event - as it can't be create with the video
                                $this::unlink_report_auto($report_id,$event_id);
                                 
                                
                             endif;
                          
						endif;
                        
                        
						unset($old_id,$binds);
                        
                    endif; 
					
				}
				
			
                $status = 1;
			
            	
			
			}
		} else  {
            $errors[] = "Access denied.";
        }
 
        $json = new JSON_Response();
		$json->witness_ids  =  json_encode($array_of_witness_ids);
        $json->event_id     = $event_id;
        $json->old_event_id = $event_old_id;
		$json->input        = $this->input;
        $json->result       = array();
        $json->errors       = $errors;
        $json->print_response();
	}

   
	/* 
	 * Get next witness id for a given event 
	 * this id will be use as the next witness id
	 */
	public function get_latest_witness_id($event_id) {
		
		$binds = array('event_id'  =>  $event_id);
		$ams_record_id = Report_Model::get_latest_witness_id($binds);
			
			// Trim witness IDS in a new array
			$longestIDLength = 0;
			foreach($ams_record_id as $id) {
				$trimmed_ids[] = trim($id['old_witness_id']);	
				if (strlen(trim(trim($id['old_witness_id']))) > $longestIDLength) {
				  $longestIDLength = strlen(trim($id['old_witness_id']));
			   }
			}
			
			// Create a new array with all the values with length = $longestIDLength
			if(!empty($trimmed_ids)) {
				foreach($trimmed_ids as $tid) {
					if(strlen($tid)==$longestIDLength) {
						$final_arr[] = $tid;	
					}
				}
				
				// Sort the final array
				sort($final_arr);
			
				// Latest ID: 
				$latest_id = trim($final_arr[count($final_arr)-1]);
			} else {
				$latest_id = '';
			}
		
			
			// Next ID: 
			if($latest_id=='1' || empty($latest_id)) {
				$next_id ='a';
			} else {
				++$latest_id;
				$next_id = $latest_id;
			}
		
		
		return $next_id;
	}
	 
	 
	/* 
	 * Get next witness id for a given event 
	 * this id will be use as the next witness id
	 * JSON VERSION 
	 */
	public function get_latest_witness_id_json() {
		
		$errors = array();
		
        if(empty($this->input['event_id'])) {
			$errors[] = _('Please, enter an event_id.');
		} else  {
			$binds = array('event_id'  =>  $this->input['event_id']);
			
			$ams_record_id = Report_Model::get_latest_witness_id($binds);
			
			// Trim witness IDS in a new array
			$longestIDLength = 0;
			foreach($ams_record_id as $id) {
				$trimmed_ids[] = trim($id['old_witness_id']);	
				if (strlen(trim(trim($id['old_witness_id']))) > $longestIDLength) {
				  $longestIDLength = strlen(trim($id['old_witness_id']));
			   }
			}
			
			// Create a new array with all the values with length = $longestIDLength
			foreach($trimmed_ids as $tid) {
				if(strlen($tid)==$longestIDLength) {
					$final_arr[] = $tid;	
				}
			}
			
			// Sort the final array
			sort($final_arr);
		
			// Latest ID: 
			$latest_id = trim($final_arr[count($final_arr)-1]);
			
			// Next ID: 
			if($latest_id=='1') {
				$next_id ='a';
			} else {
				++$latest_id;
				$next_id = $latest_id;
			}
	
		}
		
        $json = new JSON_Response();
		$json->latest     = $latest_id;
        $json->result     = $next_id;
        $json->errors     = $errors;
        $json->input      = $this->input;
        $json->print_response();
	}
    
    
    // Simulate new RA/Dec on Trajectory
    public function similate_ra_dec() {
        
        $best_end_point     = new GeoPoint($this->input['end_lat'],$this->input['end_lon']);
        $best_start_point   = new GeoPoint($this->input['start_lat'],$this->input['start_lon']);
        
        $avg_time 			= $this->input['sim_day'] . ' ' . $this->input['sim_hour'];
		$avg_start_height 	= $this->input['start_alt'];
		$height_ratio 		= ATMOSPHERE_ALTITUDE/$avg_start_height;
		$avg_end_height 	= $this->input['end_alt'];
		$avg_start_height 	= $avg_start_height * $height_ratio;
		$avg_end_height 	= $avg_end_height * $height_ratio;
		$avg_azi			= $best_end_point->bearingTo($best_start_point);
		$avg_alt 			= rad2deg(atan(($avg_start_height-$avg_end_height)/(($best_start_point->distanceTo($best_end_point))*1000)));
		$avg_height_diff 	= $avg_start_height-$avg_end_height;
		$distance_to_ground = ($avg_end_height/$avg_height_diff)*$best_end_point->distanceTo($best_start_point);
		$new_bearing		= ($avg_azi+180)%360;
		$impact_point		= $best_end_point->destinationPoint($new_bearing,$distance_to_ground);
		$sol_ra_dec 		= Trajectory::find_ra_dec($avg_alt,$avg_azi,$impact_point->getLatitude(),$impact_point->getLongitude(),$avg_time);
		
		$sol_ra_dec["start_visible_location_lat"] 	= $best_start_point->getLatitude();
		$sol_ra_dec["start_visible_location_lon"] 	= $best_start_point->getLongitude();
		$sol_ra_dec["end_visible_location_lat"]   	= $best_end_point->getLatitude();
		$sol_ra_dec["end_visible_location_lon"]   	= $best_end_point->getLongitude();
		$sol_ra_dec["impact_location_lat"]   		= $impact_point->getLatitude();
		$sol_ra_dec["impact_location_lon"]   		= $impact_point->getLongitude();
		$sol_ra_dec["avg_start_height"] 	  		= $avg_start_height;
        $sol_ra_dec["avg_end_height"]               = $avg_end_height;
        
        
        $json = new JSON_Response();
        $json->result     = $sol_ra_dec;
        $json->errors     = $errors;
        $json->input      = $this->input;
        $json->print_response();
		return $sol_ra_dec;
        
    }
	 
	 
}
