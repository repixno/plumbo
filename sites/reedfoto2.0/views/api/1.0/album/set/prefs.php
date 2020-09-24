<?php

   /**
    *
    * Set preferences of an album
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'pages.json' );
   import( 'website.album' );

   class APIAlbumSetPrefs extends JSONPage implements IValidatedView {

      /**
       * Validate the input
       *
       * @return unknown
       */
      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'albumid' => VALIDATE_INTEGER,
                  'purchaseaccess' => VALIDATE_INTEGER,
                  'downloadaccess' => VALIDATE_INTEGER,
                  'year' => VALIDATE_STRING,
                  'defaulbid' => VALIDATE_STRING,
               )
            )
         );

      }

      /**
       * Sets preferences for a given album
       * 
       * @api-name album.set.prefs
       * @api-auth required
       * @api-post-optional albumid Integer ID of the album to set preferences for
       * @api-post-optional purchaseaccess Integer Purchase access. 1: public access to the album, 0: private access to the album
       * @api-post-optional downloadaccess Integer Download access. 1: public access to the album, 0: private access to the album
       * @api-post-optional year Integer The year the album was created
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */  
      public function Execute() {

         $id = (int) $_POST[ 'albumid' ];
         $purchaseAccess = $_POST[ 'purchaseaccess' ];
         $downloadAccess = $_POST[ 'downloadaccess' ];
         $year = $_POST[ 'year' ];
         $defaultbid = $_POST['defaulbid'];
         
         $res = false;
         $msg = '';
         
         $album = new Album( $id );
         
         if( $album->isLoaded() && $album instanceof Album ) {
            
            if( Login::userid() == $album->uid ) {
               
               $album->year = $year;
               $album->purchaseaccess = $purchaseAccess;
               $album->downloadaccess = $downloadAccess;
               $album->default_bid = $defaultbid;
               
               if( $album->save() ) {

                  $res = true;
                  $msg = 'OK';

               } else {

                  $msg = 'Could not save album preferences.';

               }

            } else {

               $msg = 'User is not owner of album.';

            }

         } else {

            $msg = 'Could not load album.';

         }

         $this->result = $res;
         $this->message = $msg;

      }

   }

?>