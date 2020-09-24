<?php

import( 'website.menu' );
import( 'pages.json' );
import( 'core.util' );

class APIUpdateItemScore extends JSONPage implements IValidatedView {

   public function Validate() {

      return array(
         'execute' => array(
            'post' => array(
               'menu' => VALIDATE_INTEGER,
               'score' => VALIDATE_FLOAT,
            )
         )
      );

   }

   public function Execute() {
      
      $this->setTemplate();
      
      // Extract request data.
      $menuId = $_POST[ 'menu' ];
      $score = $_POST[ 'score' ];
      
      // Save new urlname.
      $menuitem = new MenuItem( $menuId );
      $menuitem->score = $score;
      $menuitem->save();
      
      // Set return values.
      $this->menuitemid = $menuId;
      $this->score = $score;
      $this->result = true;
      $this->message = 'OK';
      
   }

}

?>
