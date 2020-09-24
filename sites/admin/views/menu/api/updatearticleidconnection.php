<?php

import( 'website.menu' );
import( 'pages.json' );

class APIUpdateArticleConnection extends JSONPage implements IValidatedView {

   public function Validate() {

      return array(
         'execute' => array(
            'post' => array(
               'menu' => VALIDATE_INTEGER,
               'articleid' => VALIDATE_INTEGER
            )
         )
      );

   }

   public function Execute() {

      $this->setTemplate();

      // Extract request data.
      $menuId = $_POST[ 'menu' ];
      $articleid = $_POST[ 'articleid' ];

      // Initiate menu item object.
      $menu = new MenuItem( $menuId );

      // Update template. Remove if no template is given.
      $menu->articleid = $articleid;
      $menu->save();

      // Set return values.
      $this->menuitemid = $menuId;
      $this->updatedarticleid = $menu->articleid;
      $this->result = true;
      $this->message = 'OK';

   }

}

?>
