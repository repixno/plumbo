<?php
   import( 'pages.json' );
   import( 'website.album' );
      
   class APIAlbumsMerge extends JSONPage implements IValidatedView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'albumid' => VALIDATE_INTEGER,
                  'albums' => VALIDATE_STRING,
               ),
               'fields' => array(
                  'albumid' => VALIDATE_INTEGER,
                  'albums' => VALIDATE_STRING,
               )
            )
         );
         
      }
            
      /**
       * Merge a list of user's albums into the albumid specified
       * 
       * @api-name albums.merge
       * @api-auth required
       * @api-example
       * @api-post-optional albumid Integer ID of the album to merge albums to
       * @api-param-optional albumid Integer ID of the album to merge albums to
       * @api-post-optional albums String Comma-separated list of album ID's, or one ID
       * @api-param-optional albums String Comma-separated list of album ID's, or one ID
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
       
      public function Execute( $albumid = 0, $albums = '' ) {
         
         $mergealbumid = $_POST['albumid'] ? $_POST['albumid'] : $albumid;
         $albums = $_POST['albums'] ? $_POST['albums'] : $albums;
         
         if( empty( $albums ) ) {
            
            $this->result = false;
            $this->message = "Required input parameter missing or invalid (albums)";
         
            return false;
         }

         if( empty( $mergealbumid ) ) {
            
            $this->result = false;
            $this->message = "Required input parameter missing or invalid (albumid)";
            
            return false;
            
         }
         
         $albumarray = explode( ',', $albums );
         
         if( count( $albumarray ) == 0 ) $albumarray[0] = $albumarray;
         
         try {
            
            $mergealbum = new Album( $mergealbumid );
            
            if( $mergealbum->uid == Login::userid() && ( $mergealbum->id > 0 ) ) {
         
               foreach( $albumarray as $albumid ) {
                  
                  $albumid = (int)$albumid;
                  
                  if ( $albumid > 0 ) {
                     
                     $album = new Album( $albumid );
                     
            			if( $album->uid == Login::userid() ) {
            			   
                        $images = new Image();
                        
                        foreach( $images->collection( 'bid', array( 'owner_uid' => Login::userid(), 'aid' => $albumid, 'deleted_at' => null ) )->fetchAllAs('Image') as $image ) {
                           
                           $image->aid = $mergealbum->id;
                           $image->save();
                           
                        }
                        
                        if ( $albumid != $mergealbumid ) { 
                        
             			     $album->delete();
             			   
                        }

                     } else {
                        
                        $this->result = false;
                        $this->message = 'No access to album';
                        
                     }
                  }
               }
               
               $this->result = true;
               $this->message = 'OK';
               
            } else {
               
               $this->result = false;
               $this->message = 'No access to album';
               
            }

         } catch (Exception $e) {
            
            $this->result = false;
            $this->message = 'Merge failed.';
               
         }
         
      }
   
   }

?>