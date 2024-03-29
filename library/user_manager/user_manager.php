<?php

class Manage_User{
    private $sessionPrefix;
    public function __construct(){
	
	   $config=new config;
	   
	  $this->sessionPrefix=$config->session_Prefix;
	  
	}
    public function isLoggedIn($guest_exception=true){
	
	GLOBAL $db;
	
	$usr=$db->getUserSession();
	
    if(isset($_SESSION[$this->sessionPrefix.'user_session'])){
       
	   if(($usr->id==0)&&($guest_exception==false))
	   return false;
	   
      $session=unserialize($_SESSION[$this->sessionPrefix.'user_session']);
       
      if(isset($_SESSION[$this->sessionPrefix.'expired'])){
	  	  
         if((!defined("OPEN_PAGE"))&in_array($session->session_id, unserialize($_SESSION[$this->sessionPrefix.'expired'])) || $session->parent_id!=PARENT || $this->isBlocked()){
        
           if(!defined("ERROR_MESSAGE")){
        
		      if($session->parent_id!=PARENT){
		       
               return false;
			   
              }else{
			  
			   define("ERROR_MESSAGE","");
			 
		  }
			 
            }
		  
		                return false;
       
         }else{
           $user=unserialize($_SESSION[$this->sessionPrefix.'user_session']);
		   if(!defined("OPEN_PAGE")&&($user->user_type<0))
		   return false;
		     
             return true;
         }
      }else{
        
	    if(!$this->isBlocked()){
		
		 if($session->parent_id!=PARENT){
		 
		 return false;
		 
		 }else{
		  
		 
		  
		 if((defined("OPEN_PAGE"))||($session->parent_id==PARENT && !$this->isBlocked())){
			 
		   $user=unserialize($_SESSION[$this->sessionPrefix.'user_session']);
		   if(!defined("OPEN_PAGE")&&($user->user_type<0))
		   return false;
			 
           return true ;
         //return true;
		 }else{
		 if(!defined("ERROR_MESSAGE")){
        
		      if($session->parent_id!=PARENT){
		       
               return false;
			   
			   
			   
              }else{
			  
			  
			  
			   define("ERROR_MESSAGE","");
			 
			  }
			            
		   }
		 return false;
		  
		 
		 }
		 
		 }
		}else{
         return false;
        }
      
	  }
	  
      }else{
        
        if(!defined("ERROR_MESSAGE")):
        
         define("ERROR_MESSAGE","");
     
        endif;
        
        return false;
      
     }
	
    }
    public function logIn($username,$password,$parent_id=0){
        
        GLOBAL $db;
		
		if( trim(str_replace(" ","",$username))=="" and $password==""){
		  define("ERROR_MESSAGE","Invalid username or password.");
		  return false;
		  
		}
        				
        if(!isset($_POST['identifier'])){
            define("ERROR_MESSAGE","Invalid login.");
            return NULL;
        }
        
        $addclause="";
        
        if(defined("ADMIN_ROOT")):
        
        $addclause=" and user_type=9";
        
        endif;
		
		$parent_option="and parent_id=".$parent_id." ";
		
		if(PARENT==0){
		
		   $parent_option="";
		
		}
		                
        $user_details=$db->getUserDetails(" where user_status=1 and username='".str_replace("'","",$username)."' and password='".$db->hashPassword($password)."' ".$parent_option.$addclause);
	   
	    if(count($user_details)>0){
         
           if(isset($_POST[$this->sessionPrefix.'expired'])){
            			
			
            if(in_array($_POST['identifier']."_".PARENT, unserialized($_SESSION[$this->sessionPrefix.'expired']))){
            
              return false;
        
           }
          
         }
        
        $user_details[0]->session_id=$_POST['identifier'].'_'.PARENT;
        
        
        $_SESSION[$this->sessionPrefix.'user_session']=serialize($user_details[0]);
        
		unset($_SESSION[$this->sessionPrefix.'plugin']);
		
        unset($_SESSION[$this->sessionPrefix.'menus']);
        
		
		define("LOGGED","<script>
		document.location='http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']."';
		</script>");
        
         define("ERROR_MESSAGE","");
     
		$plugins=$db->getPlugins(" where plugin_type=4 and status=1");
		
		 for($i=0;$i<count($plugins);$i++){
		 
		  if(file_exists(ROOT."plugins/{$plugins[$i]->plugin_name}/{$plugins[$i]->plugin_name}.php")){
		   
		    include_once(ROOT."plugins/{$plugins[$i]->plugin_name}/{$plugins[$i]->plugin_name}.php");
			
			$func=$plugins[$i]->plugin_name;
			
			$func();
		   
		   }
		 
		 }
        
        }else{
		
		$parent_option="and parent_id=".$parent_id." ";
		
		if(PARENT==0){
		
		   $parent_option="";
		
		}
		
		
		 $user_details=$db->getUserDetails(" where user_status=0 and username='".str_replace("'","",$username)."' and password='".$db->hashPassword($password)."' ".$parent_option.$addclause);
          
		  if(count($user_details)>0){
		  
		  if(!defined("REASON"))
		   define("REASON","This account has been disabled!.Please contact the administrator.");
		  
		  }else{		    
          
		   if(!defined("ERROR_MESSAGE")){
		    define("ERROR_MESSAGE","Invalid username or password!");
           }
		  }
        }
    }
    public function logOut(){
        
        if(!isset($_SESSION[$this->sessionPrefix.'user_session'])){
        return NULL;
        }
        
        $userdet=unserialize($_SESSION[$this->sessionPrefix.'user_session']);
        
        if(isset($_SESSION[$this->sessionPrefix.'expired'])){
            
         $arr=unserialize($_SESSION[$this->sessionPrefix.'expired']);
         
         $arr[]=$userdet->session_id;
         
         $_SESSION[$this->sessionPrefix.'expired']=serialize($arr);
            
        }else{
         
         $_SESSION[$this->sessionPrefix.'expired']= serialize(array($userdet->session_id));
            
        }
        
        unset($_SESSION[$this->sessionPrefix.'user_session']);
        
    }
    public function isBlocked(){
        
				
        $user=unserialize($_SESSION[$this->sessionPrefix.'user_session']);
        
                
        if(file_exists(ROOT."library/session_blockers/{$user->id}.xss")){
            
            if(!defined("REASON")){
            
              define("REASON","This account has been disabled. Please contact the administrator.");
            
            }
            
            if(!defined("ERROR_MESSAGE")){
                
                define("ERROR_MESSAGE","");
                
            }
            
            $this->logOut();
            
            return true;
        
        }else{
        
            return false;
        
        }
        
    }
    public function hasBlocker($id){
        
		               
        if(file_exists(ROOT."library/session_blockers/$id.xss")){
        
            return true;
        
        }else{
        
            return false;
        
        }    
        
    }
    public function removeSessionBlockers($ids){
	
	  for($i=0;$i<count($ids);$i++){
	
       if(file_exists(ROOT."library/session_blockers/{$ids[$i]}.xss")){
	    
         @unlink(ROOT."library/session_blockers/{$ids[$i]}.xss");
       
	   }
	   
	  }
	    
    }
	public function createSessionBlockers($ids){
	
	 for($i=0;$i<count($ids);$i++){
	   @file_put_contents(ROOT."library/session_blockers/{$ids[$i]}.xss","block_file");
	 }
	 
	}
	
	public function lastUpdate($option="globalupdate"){
	
	  $content=scandir(ROOT."library/session_updates");
	  
	  return $this->getUpdateType($content,$option);
	  	
	}
	
	public function recordUpdate($option="globalupdate"){
	  $this->removeUpdate($option);
	  file_put_contents(ROOT."library/session_updates/$option"."_".time().".tmx"," ");
	}
	
	private function removeUpdate($option="globalupdate"){
	
	unlink(ROOT."library/session_updates/".$option."_".$this->lastUpdate($option).".tmx");
	 
	}
	
	private function getUpdateType($content,$type){
	
	for($i=0;$i<count($content);$i++){
	
	   if(preg_match("/$type/i",$content[$i])){
	     
		 $file_name=explode('_',$content[$i]);
		  
		 $file_name=explode('.',$file_name[1]);
		 
		 return $file_name[0];
	   
	   } 

    }

	 return 0;
	 
	}
	
    
}
?>