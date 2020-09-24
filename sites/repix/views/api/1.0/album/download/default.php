<?php

   /**
    * API for requesting user album downloads
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    *
    */


   define( 'MAXFILES', 100 );

   import( 'pages.json' );
   import( 'website.album' );
   library( 'pear.http.request' );
   
   class APIAlbumDownload extends JSONPage implements IValidatedView {
      
      private $secret = '8w8dLnvlMXCY3hi7onCsRP5PRkdsYURouk5jFLBU4ClJp54C5ibLxb5oL68k8qn';
      private $zipserver = "http://zip.eurofoto.no/zip/index.php";
      
      // User for testing
      //private $zipserver = "http://snake.ef.interweb.no/zip/index.php";
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
               ),
               'post' => array(
                  'albumid' => VALIDATE_INTEGER,
               )
            )
         );
         
      }
      
      /**
       * Compress album in a ZIP-archive and return a download URL
       * 
       * @api-name album.download
       * @api-auth required
       * @api-post-optional albumid Integer ID of the album to compress
       * @api-param-optional albumid Integer ID of the album to compress
       * @api-result albumdownloadpath String URL to ZIP-archive
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */     
      public function Execute( $id = 0 ) {
         
         if( isset( $_POST['albumid'] ) ) {
            $id = (int) $_POST['albumid'];
         }
         
         $this->result = 'false';
         $this->message = 'Required input parameter missing or invalid (albumid)';
         if( !$id > 0 ) return false;
         
         $this->result = false;
         $this->message = 'Not logged in';
         if( !Login::isLoggedIn() ) return false;
         
         try {
            
            $album = new Album( $id );
            if( !$album instanceof Album || !$album->isLoaded() ) return false;
            
            $this->result = false;
            $this->message = 'Not album owner';
            if( $album->ownerid != Login::userid() ) return false;
            
            // Used to have a max limit for files. Removing this on popular demand  :)
            //$this->result = false;
            //$this->message = 'To many images in album. Needs to be fewer than '.MAXFILES;
            //if( $album->numimages > MAXFILES ) return false;
   
            // Passed all checkpoints. Try to download
            $hash = md5( Login::userid().$id.$this->secret );
            $user = new User( Login::userid() );
            $username = $user->email;
            $host = $_SERVER['HTTP_HOST'];
         
            $request = new HTTP_Request( $this->zipserver );
            //$request->setMethod( 'GET' );
            $request->setMethod( 'POST' );
            //$request->addQueryString( 'a', Login::userid(), true );
            $request->addPostData( 'a', Login::userid(), true );
            //$request->addQueryString( 'b', $id, true );
            $request->addPostData( 'b', $id, true );
            //$request->addQueryString( 'c', $hash, true );
            $request->addPostData( 'c', $hash, true );
            //$request->addQueryString( 'host', $host, true );
            $request->addPostData( 'host', $host, true );
            $request->sendRequest( true );
   
            $response = $request->getResponseBody();

            // Something went wrong on download?
            $this->result = false;
            $this->message = 'Failed to download album from server';
            if( stristr( $response, 'ZIP_FAIL' ) ) return false;
            
            
            // All is well
            $this->result = true;
            $this->message = 'OK';
            $this->albumdownloadpath = json_decode( $response );
            
         } catch ( Exception $e ) {
            
            $this->result = false;
            $this->message = 'An error occured when trying to process request.';
            return false;
            
         }
         
      }
      
   }


?>