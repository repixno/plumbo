<?php

import( 'website.textentity' );
import( 'pages.json' );

class APIFetchContent extends JSONPage implements IValidatedView {

   public function Validate() {

      return array(
         'execute' => array(
            'post' => array(
               'type' => VALIDATE_STRING
            )
         )
      );

   }

   public function Execute() {

      $this->setTemplate();

      // Extract request data.
      $contentType = $_POST[ 'type' ];

      // Fetch list of all content of given type.
      $contents = TextEntity::getAll( $contentType );

      // Set return values.
      $this->contentlist = $contents;
      $this->result = true;
      $this->message = 'OK';
      
   }

}

?>
