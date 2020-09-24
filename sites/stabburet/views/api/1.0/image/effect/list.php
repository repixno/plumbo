<?php

   /**
    * Class to return list of effects.
    *
    *
    */

   import( 'pages.json' );
   import( 'website.image' );
   import( 'website.imageeffects' );

   class APIImageEffectList extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'imageid' => VALIDATE_INTEGER,
               ),
               'fields' => array(
                  'imageid' => VALIDATE_INTEGER,
               ),
            )
         );
      }

      /**
       * Return list of effects
       *
       * @api-name image.effect.list
       * @api-javascript yes
       * @api-post-optional imageid Integer Optional ImageID for preview URLs of effects
       * @api-param-optional imageid Integer Optional ImageID for preview URLs of effects
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       * @api-result effects Array List of effects
       */
      public function Execute( $imageid = 0 ) {
         
         if( $_POST['imageid'] ) $imageid = $_POST['imageid'];
         
         try {
            
            $effects = ImageEffects::getEffectList();
            
            if( $imageid ) {
               
               // make sure this is our image
               $image = new Image( $imageid );
               
               // prefill the preview field
               foreach( $effects as $effectkey => $effect ) {
                  $effects[$effectkey]['preview'] = sprintf( '/images/effects/sample/%d/%d.jpg', $effect['id'], $imageid );
               }
               
            }
            
            $this->effects = $effects;
            $this->result = true;
            $this->message = 'OK';
            
         } catch( Exception $e ) {
            
            $this->result = false;
            $this->message = $e->getMessage();
            
         }
         
      }
   }
?>
