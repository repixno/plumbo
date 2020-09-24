<?php

import( 'website.menu' );
import( 'pages.json' );

class APIDeleteConnectedItem extends JSONPage implements IValidatedView {

   public function Validate() {

      return array(
         'execute' => array(
            'post' => array(
               'menuitem' => VALIDATE_INTEGER,
               'content' => VALIDATE_INTEGER,
               'section' => VALIDATE_STRING
            )
         )
      );

   }

   public function Execute() {

      $this->setTemplate();

      // Extract request data.
      $menuItemId = $_POST[ 'menuitem' ];
      $contentId  = $_POST[ 'content' ];
      $section    = $_POST[ 'section' ];

      // support for default sections
      if( $section == '_default' ) $section = '';
      
      //$newPosition = Menu::getRootSize();

      $menuitem = new MenuItem( $menuItemId );
      $menuitem->removeContent( $contentId, $section );
      $menuitem->save();

      // Find out if textentity is used in another menu or section
      $menuid = DB::query( "SELECT menuid FROM site_menu_contents WHERE textentityid = ?", $contentId )->fetchSingle();

      $this->connected = !empty( $menuid ) ? true : false;
      $this->menuitemid = $menuItemId;
      $this->result = true;
      $this->message = 'OK';

   }

}

?>
