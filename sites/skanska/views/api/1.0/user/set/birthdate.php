<?php

   /**
    * Save user birthdate
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */


   import( 'pages.json' );

   class APIUserSetBirthDate extends JSONPage implements IValidatedView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'birthdate' => VALIDATE_STRING,
               ),
               'get' => array(
                  'birthdate' => VALIDATE_STRING,
               )
            )
         );
         
      }
      
      public function Execute() {
         
         $birthdate = $_POST['birthdate'] ? $_POST['birthdate'] : $_GET['birthdate'];
         
         if( strlen( $birthdate ) != 10 ) {
            
            $this->result = false;
            $this->message = 'Not a valid date';
            return false;
            
         }
         
         try {
            
            $birthdate = date( 'Y-m-d', strtotime( $birthdate ) );
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = 'Not a valid date';
            return false;
            
         }
         
         
         try {
            
            $user = new User( Login::userid() );
            $user->birthdate = $birthdate;
            $user->save();
            
            $this->result = true;
            $this->message = 'OK';
            return true;
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = 'Failed to init user';
            return false;
            
         }
         
      }
      
      
   }


?>