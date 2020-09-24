<?php

import( 'website.menu' );
import( 'pages.json' );

class APIUpdatePortalConnection extends JSONPage implements IValidatedView {

   public function Validate() {

      return array(
         'execute' => array(
            'post' => array(
               'action' => VALIDATE_STRING,
               'menu' => VALIDATE_INTEGER,
               'portal' => VALIDATE_STRING
            )
         )
      );

   }

   public function Execute() {

      $this->setTemplate();

      $menuId = $_POST[ 'menu' ];
      $portalId = $_POST[ 'portal' ];
      $action = $_POST[ 'action' ];

      // Fetch list of all content for menu item.
      $menu = new MenuItem( $menuId );

      switch ( $action ) {

         case 'add':
            $menu->addPortal( $portalId );
            break;

         case 'remove':
            $menu->removePortal( $portalId );
            break;

      }

      $this->menuitemid = $menuId;
      $this->result = true;
      $this->message = 'OK';
      
   }

}

?>
