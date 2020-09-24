<?php

import( 'website.menu' );
import( 'pages.json' );

class APISaveMenuStruct extends JSONPage implements IValidatedView {

   public function Validate() {

      return array(
         'execute' => array(
            'post' => array(
               'stuct' => VALIDATE_STRING
            )
         )
      );

   }

   public function Execute() {

      $this->setTemplate();

      $newTitle = $_POST[ 'title' ];

      $newPosition = Menu::getRootSize();

      $menuitem = new MenuItem();
      $menuitem->title = $newTitle;
      $menuitem->save();

      $this->title = $newTitle;
      $this->id = $menuitem->id;
      $this->result = true;
      $this->message = 'OK';
      
   }

}

?>
