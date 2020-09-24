<?php

import( 'website.menu' );
import( 'pages.json' );
import( 'core.util' );

class APIUpdateItemUrl extends JSONPage implements IValidatedView {

   public function Validate() {

      return array(
         'execute' => array(
            'post' => array(
               'menu' => VALIDATE_INTEGER,
               'newurlname' => VALIDATE_STRING,
               'translated' => VALIDATE_STRING
            )
         )
      );

   }

   public function Execute() {

      $this->setTemplate();

      // Extract request data.
      $menuId = $_POST[ 'menu' ];
      $urlName = $_POST[ 'newurlname' ];
      $translated = json_decode( $_POST['translated'] );
      $urlnametranslations = array();
      if( is_array( $translated ) ) {
         foreach( $translated as $translation ) {
            $urlnametranslations[$translation->code] = $translation->urlname;
         }
      }

      
      // Save new urlname.
      $menuitem = new MenuItem( $menuId );

      if ( $menuitem->urlExists( $urlName ) ) {

         $this->result = false;
         $this->message = 'Url already exists. Try a different URL name.';
         return false;

      }

      $menuitem->urlname = $urlName;
      $menuitem->setTranslatedURLNames( $urlnametranslations );
      $menuitem->save();
      $menuitem->updateDescendantUrls();

      // Set return values.
      $this->menuitemid = $menuId;
      $this->translated = $translated;
      $this->urlname = $menuitem->urlname;
      $this->url = $menuitem->url;
      $this->result = true;
      $this->message = 'OK';

   }

}

?>
