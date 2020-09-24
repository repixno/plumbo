<?php

   /**
    * Class to create a effectprocessed image
    */

   import( 'pages.json' );
   import( 'website.image' );
   import( 'website.imageeffects' );

   class APIImageEffectCreate extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'imageid' => VALIDATE_INTEGER,
                  'effectid' => VALIDATE_INTEGER,
                  'albumid' => VALIDATE_INTEGER
               ),
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_INTEGER,
                  VALIDATE_INTEGER
               )
            )
         );
      }

      /**
       * Create a new image with the effect
       *
       * @api-name image.effect.create
       * @api-javascript yes
       * @api-post imageid Integer Id of source image
       * @api-post effectid Integer Id of effect
       * @api-post albumid Integer Id of the album to place the new image in
       **/
   
      function Execute( $imageid = 0, $effectid = 0, $albumid = 0 ) {

         $imageid = $imageid ? $imageid : $_POST[ 'imageid' ];
         $effectid = $effectid ? $effectid : $_POST[ 'effectid' ];
         $albumid = $albumid ? $albumid : $_POST[ 'albumid' ];

         $effect = new ImageEffects();

         if ( empty( $albumid ) ) {

            $album = new Album();
            $album->name = $effect->defaultAlbumName();
            $album->save();

            $albumid = $album->id;

         }

         $newimage = $effect->processImage( $imageid, $effectid, $albumid );

         $image = new Image( $newimage );

         $this->result = true;
         $this->message = 'OK';

         $this->newimage = $image->asArray();

      }

}
?>
