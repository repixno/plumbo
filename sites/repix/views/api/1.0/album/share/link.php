<?php

   /**
    * Shares a single album by link
    * 
    * @author Oyvind Selbek <oyvind@selbek.com>
    * 
    */

   import( 'pages.json' );
   import( 'mail.send' );
   import( 'website.user' );
   import( 'website.album' );
   
   class APIAlbumShareByLink extends JSONPage implements IValidatedView {
      
      public function Validate() {
       
         return array(
            'enable' => array(
               'post' => array(
                  'albumid' => VALIDATE_INTEGER,
               ),
               'fields' => array(
                  'albumid' => VALIDATE_INTEGER,
               )
            ),
            'disable' => array(
               'post' => array(
                  'albumid' => VALIDATE_INTEGER,
               ),
               'fields' => array(
                  'albumid' => VALIDATE_INTEGER,
               )
            )
         );
         
      }
      
      /**
       * Enable album sharing (by link)
       * 
       * @api-name album.share.link.enable
       * @api-auth required
       * @api-example
       * @api-post-optional albumid Integer The id of the album to share
       * @api-param-optional albumid Integer The id of the album to share
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      public function Enable( $id = 0 ) {
         
         if( empty( $id ) ) {
            $id = (int) $_POST["albumid"];
         }
         
         $this->result = false;
         $this->message = "Required input parameter missing or invalid (albumid)";
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
         
         $album->access = 2;
         $album->save();
         
         $this->result = true;
         $this->message = 'OK';
         
         return true;
         
      }

      /**
       * Disable album sharing (by link)
       * 
       * @api-name album.share.link.disable
       * @api-auth required
       * @api-example
       * @api-post-optional albumid Integer The id of the album to disable sharing for
       * @api-param-optional albumid Integer The id of the album to disable sharing for
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Disable( $id = 0 ) {
         
         if( empty( $id ) ) {
            $id = (int) $_POST["albumid"];
         }
         
         $this->result = false;
         $this->message = "Required input parameter missing or invalid (albumid)";
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
         
         $album->access = 0;
         $album->save();
         
         $this->result = true;
         $this->message = 'OK';
         
         return true;
         
      }
      
   }
   
?>