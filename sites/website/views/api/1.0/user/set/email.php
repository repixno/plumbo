<?php

   /**
    * Change user's email address
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */
   
   import( 'pages.json' );
   import( 'website.user' );
   import( 'validate.email' );

   class APIUserSetEmail extends JSONPage implements IValidatedView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_STRING,
                  VALIDATE_STRING,
               ),
               'post' => array(
                  'newemail' => VALIDATE_STRING,
                  'newemailrep' => VALIDATE_STRING,
               ),
               'get' => array(
                  'newemail' => VALIDATE_STRING,
                  'newemailrep' => VALIDATE_STRING,
               )
            )
         );
         
      }
      
      public function Execute( $newemail = null, $newemailrep = null ) {
         
         if( empty( $newemail ) ) $newemail = $_POST['newemail'] ? $_POST['newemail'] : $_GET['newemail'];
         if( empty( $newemailrep ) ) $newemailrep = $_POST['newemailrep'] ? $_POST['newemailrep'] : $_GET['newemailrep'];
         
         $this->result = false;
         $this->message = 'Required parameter missing';
         $this->errorcode = 1;
         if( empty( $newemail ) || empty( $newemailrep ) ) return false;
         
         $this->result = false;
         $this->message = 'Emails do not match';
         $this->errorcode = 2;
         if( $newemail != $newemailrep ) return false;
         
         $this->result = false;
         $this->message = 'Failed to init user';
         $this->errorcode = 3;
         $user = new User( Login::userid() );
         if( !$user instanceof User || !$user->isLoaded() ) return false;
            
         $this->result = false;
         $this->message = 'Email validation failed';
         $this->errorcode = 4;
         if( !ValidateEmail::validate( $newemail ) ) return false;
         
         $this->result = false;
         $this->message = 'New email address is the same as the old address';
         $this->errorcode = 5;
         if( $user->email == $newemail ) return false;
         
         $this->result = false;
         $this->message = 'User already registered';
         $this->errorcode = 6;
         if( User::registered( $newemail ) ) return false;
         
         $this->result = true;
         $this->message = 'OK';
         //$user->email = $email;
         //$user->save();
         
         
         
         // Update session data.
         //$sessiondata = Session::get( 'logindata' );
         //$sessiondata[ 'username' ] = $user->email;
         //Session::set( 'logindata', $sessiondata );
         
         return true;
         
      }
      
      
   }


?>