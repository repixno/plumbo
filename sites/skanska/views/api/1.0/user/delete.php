<?php


   /**
    * API for deleting users
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */

   
   import( 'pages.json' );
   import( 'website.user' );
   import( 'website.login' );
   
   class APIUserDelete extends JSONPage implements IValidatedView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
               ),
               'post' => array(
                  'userid' => VALIDATE_INTEGER,
               ),
               'get' => array(
                  'userid' => VALIDATE_INTEGER,
               )
            )
         );
         
      }
      
      public function Execute( $userid = null ) {
         
         try {
            
            $admin = new Admin( Login::userid() );
            if( !$userid ) $userid = $_POST['userid'] ? $_POST['userid'] : $_GET['userid'];
            
         } catch( Exception $notadmin ) { // User is not an admin
            
            $userid = Login::userid();
            
         }
         
         $this->result = false;
         $this->message = 'Missing parameter';
         if( empty( $userid ) ) return false;
         
         try {
            
            $user = new User( $userid );
            if( !$user instanceof User || !$user->isLoaded() ) return false;
            
         } catch( Exception $nosuchuser ) {
            
            $this->result = false;
            $this->message = 'Failed to load user';
            return false;
            
         }
   
         // Everything checks out. Save
         $user->deleted = date( 'Y-m-d H:i:s' );
         $user->save();
         
         // Clear user session data
         // and clear uploaded images
         // but only if it's your own user
         if( $userid == Login::userid() ) {
            Login::logout();
         }
         
         $this->result = true;
         $this->message = 'User deleted';
         return true;
         
      }
      
      
   }


?>