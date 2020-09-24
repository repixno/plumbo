<?php

import( 'website.menu' );
import( 'pages.json' );

class APINewMenuItem extends JSONPage implements IValidatedView {

   public function Validate() {

      return array(
         'execute' => array(
            'post' => array(
               'title' => VALIDATE_STRING,
               'portal' => VALIDATE_STRING
            )
         )
      );

   }

   public function Execute() {

      $this->setTemplate();

      // Extract request data.
      $newTitle = $_POST[ 'title' ];
      $newPortal = $_POST[ 'portal' ];

      if ( empty( $newTitle ) ) return false;

      $newPosition = Menu::getRootSize();

      $menuitem = new MenuItem();
      $menuitem->title = $newTitle;
      $menuitem->sortorder = $newPosition;
      $siteid = Session::get( 'adminsiteid', 0 );
      if( !$siteid ) $siteid = Session::get( 'siteid', 1 );
      $menuitem->siteid = $siteid;
      $menuitem->save();

      if ( !empty( $newPortal ) ) {

         $menuitem->addPortal( $newPortal );

      }

      // Build options array
      $options = array(
         'id' => $menuitem->id,
         'title' => $menuitem->getTitle(false),
         'display' => $menuitem->title,
         'parentid' => $menuitem->parentid,
         'position' => $menuitem->sortorder,
         'level' => null,
         'template' => $menuitem->template,
         'url' => $menuitem->url,
         'urlname' => $menuitem->urlname,
         'score' => $menuitem->score,
      );

      // Set return values.
      $this->options = $options;
      $this->id = $menuitem->id;
      $this->result = true;
      $this->message = 'OK';

   }

}

?>
