<?php

   /**
    * Share a album to friends
    *
    */

   import( 'pages.json' );
   import( 'mail.send' );
   import( 'website.user' );
   import( 'website.album' );
   import( 'website.userfriend' );
   
   class APIAlbumShareSubscribe extends JSONPage implements IValidatedView {
      
      /**
       * Validate
       *
       * @return Array
       */
      
      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'albumid'      => VALIDATE_INTEGER,
                  'friendhash'   => VALIDATE_STRING,
                  'signature'    => VALIDATE_STRING
               ),
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_STRING,
                  VALIDATE_STRING
               )
               
            )
         ); 
      
      }
      
      /**
       * Execute
       *
       * @api-name album.share.subscribe
       * @api-param-optional albumid Integer ID of the album
       * @api-post-optional albumid Integer ID of the album
       * @api-param-optional friendhash String Friend hash
       * @api-post-optional friendhash String Friend hash
       * @api-param-optional Signature String Signature
       * @api-post-optional friendhash String Signature
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      
      function Execute( $albumid = 0, $friendhash = '', $signature = '' ) {
         
         $albumid = $_POST['albumid'] ? $_POST['albumid'] : $albumid;
         $friendhash = $_POST['friendhash'] ? $_POST['friendhash'] : $friendhash;
         $signature = $_POST['signature'] ? $_POST['signature'] : $signature;
         
         try {

            $friend = UserFriend::getFriendFromHash( $friendhash, $signature );
            
            $status = Album::getSharingStatus( $albumid, $friend->friendid, 2 );
   
            if ( $status > 0 ) {
               
               if ( Album::subscribeToSharedAlbum( $albumid, $friend->friendid ) ) {
                  
                  $this->result = true;
                  $this->message = 'OK';
                  
               } else {
                  
                  $this->result = true;
                  $this->message = 'Subscribing to album failed';
                  
               }
   
            } else {
               
               $this->result = false;
               $this->message = 'Friend verification failed';
               
            }
         } catch ( Exception $e ) {
            
            $this->result = false;
            $this->message = 'Failed';
            
         }
         
      }
      
      
   }
   
?>
