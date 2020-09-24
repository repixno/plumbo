<?php

   /**
    * Class to return list of Photofunia effects.
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'pages.json' );
   import( 'services.photofunia.photofunia' );

   class APIImagePhotofuniaEffectList extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array()
            )
         );
      }

      /**
       * Return list of effects from Photofunia service
       *
       * @api-name services.photofunia.effectlist
       * @api-javascript yes
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       * @api-result effects Array List of effects
       */
      public function Execute() {

         $photofun = new PhotoFunia();

         $this->result = true;
         $this->message = 'Photofunia effects fetched';
         $this->effects = $photofun->getEffectList();

      }

   }

?>
