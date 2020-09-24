<?php

   /**
    * Set gender of user
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.json' );

   class APIUserSetGender extends JSONPage implements IValidatedView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'gender' => VALIDATE_STRING,
               ),
               'get' => array(
                  'gender' => VALIDATE_STRING,
               )
            )
         );
         
      }
      
      
      public function Execute() {
         
         $gender = $_POST['gender'] ? $_POST['gender'] : $_GET['gender'];
         
         
         
         try {
            
            if( !in_array( strtolower( $gender ), array( 'm', 'f' ) ) ) $gender = null;
            
            $user = new User( Login::userid() );
            $user->gender = $gender;
            $user->save();
            
            $this->result = true;
            $this->message = 'OK';
            return true;
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = 'Failed to set user gender';
            return false;
            
         }
         
         
      }
      
      
   }


?>