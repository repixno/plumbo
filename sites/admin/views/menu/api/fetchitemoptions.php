<?php

import( 'website.menu' );
import( 'pages.json' );

class APIFetchItemOptions extends JSONPage implements IValidatedView {

   public function Validate() {

      return array(
         'execute' => array(
            'post' => array(
               'menu' => VALIDATE_INTEGER
            )
         )
      );

   }

   public function Execute() {

      $this->setTemplate();

      $menuId = $_POST[ 'menu' ];

      // Fetch list of all portals this menu item is connected to.
      $menu = new MenuItem( $menuId );

      $portals = $menu->getPortals();

      // Set return values.
      $this->menuitemid = $menuId;
      $this->portals = $portals;
      $this->result = true;
      $this->message = 'OK';

   }

}

?>
