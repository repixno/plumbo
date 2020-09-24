<?php

   /**
    * Class to create photofunia photo.
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'pages.json' );
   import( 'services.photofunia.photofunia' );
   import( 'website.image' );

   class APIImagePhotofuniaCreate extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'imageid' => VALIDATE_INTEGER,
                  'effectid' => VALIDATE_INTEGER,
                  'albumid' => VALIDATE_INTEGER
               )
            )
         );
      }

      /**
       * Create a new image with effects using Photofunia service
       *
       * @api-name services.photofunia.create
       * @api-javascript yes
       * @api-post imageid Integer Id of source image
       * @api-post effectid Integer Id of effect at Photofunia service
       * @api-post albumid Integer Id of album where result image will be saved
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       * @api-result newimage Array Properties of result image
       */
      public function Execute() {

         $imageid = $_POST[ 'imageid' ];
         $effectid = $_POST[ 'effectid' ];
         $albumid = $_POST[ 'albumid' ];

         $photofun = new PhotoFunia();

         if ( empty( $albumid ) ) {

            $albumid = $photofun->createDefaultAlbum();

         }

         $newimage = $photofun->processPhoto( $imageid, $effectid, $albumid );

         $image = new Image( $newimage );

         $this->result = true;
         $this->message = 'Photofunia photo created';
         $this->newimage = $image->asArray();

      }

   }

?>
