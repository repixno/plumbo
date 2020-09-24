<?php

   /**
    * Password-protect a shared album
    * 
    * @author Oyvind Selbek <oyvind@selbek.com>
    * 
    */

   import( 'pages.json' );
   import( 'mail.send' );
   import( 'website.user' );
   import( 'website.album' );
   
   class APIAlbumSetPassword extends JSONPage implements IValidatedView {
      
      public function Validate() {
       
         return array(
            'execute' => array(
               'post' => array(
                  'albumid' => VALIDATE_INTEGER,
                  'password' => VALIDATE_STRING,
               ),
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_STRING,
               )
            ),
         );
         
      }

      /**
       * Password-protect a shared album
       * 
       * @api-name album.set.password
       * @api-auth required
       * @api-param-optional albumid Integer ID of the album to update password on
       * @api-param-optional password String The password to set
       * @api-post-optional albumid Integer ID of the album to update password on
       * @api-post-optional password String The password to set
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */        
      public function Execute( $id = 0, $password = '' ) {
         
         if( empty( $id ) ) {
            $id = (int) $_POST["albumid"];
         }
         
         if( empty( $password ) ) {
            $password = $_POST['password'];
         }
         
         $this->result = false;
         $this->message = "No album id given";
         if( empty( $id ) ) return false;
         
         try {
            
            $album = new Album( $id );
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = 'No such album or no access to this album';
            return false;
            
         }
         
         $this->result = false;
         $this->message = 'Failed to load album';
         if( is_null( $album ) || !$album->isLoaded() || !$album instanceof Album ) return false;
         
         $this->result = false;
         $this->message = 'No access to this album';
         if( $album->ownerid != Login::userid() ) return false;
         
         $album->password = empty( $password ) ? null : $password;
         $album->save();
         
         $this->result = true;
         $this->message = 'OK';
         
         return true;
         
      }
      
   }
   
?>