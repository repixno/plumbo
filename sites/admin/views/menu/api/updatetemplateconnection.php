<?php

import( 'website.menu' );
import( 'pages.json' );

class APIUpdateTemplateConnection extends JSONPage implements IValidatedView {

   public function Validate() {

      return array(
         'execute' => array(
            'post' => array(
               'menu' => VALIDATE_INTEGER,
               'template' => VALIDATE_STRING
            )
         )
      );

   }

   public function Execute() {

      $this->setTemplate();

      // Extract request data.
      $menuId = $_POST[ 'menu' ];
      $template = $_POST[ 'template' ];

      // Initiate menu item object.
      $menu = new MenuItem( $menuId );

      // Update template. Remove if no template is given.
      $menu->template = $template;
      $menu->save();

      // Set return values.
      $this->menuitemid = $menuId;
      $this->updatedtemplate = $menu->template;
      $this->result = true;
      $this->message = 'OK';

   }

}

?>
