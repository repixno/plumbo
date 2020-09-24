<?php

import( 'website.menu' );
import( 'pages.json' );

class APINewConnectedItem extends JSONPage implements IValidatedView {

   public function Validate() {

      return array(
         'execute' => array(
            'post' => array(
               'menuitem' => VALIDATE_INTEGER,
               'content' => VALIDATE_INTEGER,
               'position' => VALIDATE_INTEGER,
               'section' => VALIDATE_DEFAULT
            )
         )
      );

   }

   public function Execute() {

      $this->setTemplate();

      $menuItemId = $_POST[ 'menuitem' ];
      $contentId  = $_POST[ 'content' ];
      $position   = $_POST[ 'position' ];
      $section   = $_POST[ 'section' ];

      //$newPosition = Menu::getRootSize();

      $menuitem = new MenuItem( $menuItemId );

      $menuitem->addContent( $contentId, $position, $section );
      $menuitem->save();

      $this->contentid = $contentId;
      $this->menuitemid = $menuItemId;
      $this->result = true;
      $this->message = 'OK';

   }

}

?>
