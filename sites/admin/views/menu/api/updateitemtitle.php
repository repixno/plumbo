<?php

import( 'website.menu' );
import( 'pages.json' );
import( 'core.util' );

class APIUpdateItemTitle extends JSONPage implements IValidatedView {

   public function Validate() {

      return array(
         'execute' => array(
            'post' => array(
               'menu' => VALIDATE_INTEGER,
               'newtitle' => VALIDATE_STRING,
               'translated' => VALIDATE_STRING
            )
         )
      );

   }

   public function Execute() {

      $this->setTemplate();

      // Extract request data.
      $menuId = $_POST[ 'menu' ];
      $title = $_POST[ 'newtitle' ];
      $translated = json_decode( $_POST['translated'] );
      $titletranslations = array();
      if( is_array( $translated ) ) {
         foreach( $translated as $translation ) {
            $titletranslations[$translation->code] = $translation->title;
         }
      }

      // Save new urlname.
      $menuitem = new MenuItem( $menuId );
      $menuitem->title = $title;
      $menuitem->setTranslatedTitles( $titletranslations );
      $menuitem->save();
      
      // Set return values.
      $this->menuitemid = $menuId;
      $this->title = $menuitem->getTitle( false );
      $this->display = $menuitem->title;
      $this->translated = $translated;
      $this->result = true;
      $this->message = 'OK';

   }

}

?>
