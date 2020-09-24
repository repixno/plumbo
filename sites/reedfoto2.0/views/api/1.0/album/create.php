<?php

   /**
    * Class tries to create a new album
    *
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */
   
   import( 'pages.json' );
   import( 'website.album' );
   import( 'website.user' );
   
   class APIAlbumCreate extends JSONPage implements IValidatedView {
      
      /**
       * Validates the data going into this API
       *
       * @return array of validation rules.
       */
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'title' => VALIDATE_STRING,
                  'description' => VALIDATE_STRING,
                  'identifier' => VALIDATE_STRING,
                  'forsale' => VALIDATE_BOOLEAN,
                  'fordownload' => VALIDATE_BOOLEAN,
               ),
            )
         );
         
      }
      
      /**
       * Creates an album as the currently logged in user.
       * 
       * @api-name album.create
       * @api-auth required
       * @api-post title String The title of the new Album
       * @api-post description String The description of the new Album
       * @api-post-optional forsale Boolean Whether this album, if shared with someone, can be bought.
       * @api-post-optional fordownload Boolean Whether this album, if shared with someone, can be downloaded.
       * @api-post-optional identifier String Unique identifier for the album. If given, any matching albu, is returned in place of creating a new album.
       * @api-result album Object Album object
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute() {

         // Setup params
         $title = isset( $_POST["title"] ) ? $_POST["title"] : null;
         $description = isset( $_POST["description"] ) ? $_POST["description"] : null;
         $forsale = isset( $_POST["forsale"] ) ? true : false;
         $fordownload = isset( $_POST["fordownload"] ) ? true : false;
         
         $this->result = false;
         $this->message = "No title given";
         if( empty( $title ) ) return false;
         
         $user = new User( Login::userid() );
         
         $this->result = false;
         $this->message = "Failed initializing user";
         if( !$user->isLoaded() || !$user instanceof User ) return false;
         
         try {
            
            if( isset( $_POST['identifier'] ) && $_POST['identifier'] ) {
               $album = Album::fromIdentifier( $_POST['identifier'] );
            } else {
               $album = false;
            }
            
            if ( ( !$album instanceof Album ) || ( !$album->isLoaded() ) ) {

               // Setup new album
               $album = new Album();
               $album->ownerid = Login::userid();
               $album->title = $title;
               $album->description = $description;
               $album->access = 0;
               $album->purchaseaccess = $forsale;
               $album->downloadaccess = $fordownload;
               $album->created = date( 'Y-m-d H:i:s' );
               $album->views = 0;
               $album->grow_views = 0;
               $album->year = date( "Y" );
               $album->country = isset( $user->country ) ? $user->country : 160;
               $album->identifier = $_POST['identifier'] ? $_POST['identifier'] : null;
               $album->save();
               
            }
            
            // store the result
            $this->result = true;
            $this->message = "Album created";
            $this->album = $album->asArray();
         
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = 'Album creation failed';
            
         }
         
      }
      
   }


?>