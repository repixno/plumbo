<?php

import( 'website.menu' );
import( 'pages.json' );

class APIDeleteMenuItem extends JSONPage implements IValidatedView {

   public function Validate() {

      return array(
         'execute' => array(
            'post' => array(
               'tobedeleted' => VALIDATE_STRING,
               'parentid' => VALIDATE_INTEGER,
               'position' =>  VALIDATE_INTEGER
            )
         )
      );

   }

   public function Execute() {

      $this->setTemplate();

      // Extract request data.
      $menuItems = $_POST[ 'tobedeleted' ];
      $parentId = $_POST[ 'parentid' ];
      $position = $_POST[ 'position' ];

      $menuItemList = explode( ',', $menuItems );

      $menuitem = new Menu();
      $menuitem->deleteMenuItems( $menuItemList, $parentId, $position );

   }

}

?>
