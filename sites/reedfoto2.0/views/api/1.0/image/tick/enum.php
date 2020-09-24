<?php

   /**
    * Enumerate previously ticked for purchase images
    * 
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    *
    */

   import( 'pages.json' );
   import( 'session.usersessionarray' );

   class APIEnumTickedImages extends JSONPage implements IView {

      /**
       * Enumerate previously ticked for purchase images
       * 
       * @api-name image.tick.enum
       * @api-result images Array List of chosen images
       * @api-result count Integer Number of items in image list
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */        
      public function Execute() {

         $this->result = false;
         $this->message = "No images";
         
         $images = UserSessionArray::getItems( "choosenimages" );
         $images = reset( $images );
         
         if( empty( $images ) || count( $images ) == 0 ) return false;
            
         $this->images = $images;
         $this->result = true;
         $this->count = count( $images );
         $this->message = 'OK';
         
      }
      
   }


?>