<?php

import( 'website.menu' );
import( 'pages.json' );

class APIFetchTemplates extends JSONPage implements IValidatedView {

   public function Validate() {

      return array(
         'execute' => array()
      );

   }

   public function Execute() {

      $this->setTemplate();

      // Elements to put at start of returned list.
      $staticListElements = array(
         array(
            'id' => '',
            'title' => '- ' . __( 'No template' )
            )
         );
      
      $siteid = Session::get( 'adminsiteid', 1 );
      $sites = Settings::Get( 'application', 'sites' );
      if( isset( $sites[$siteid]['short'] ) ) {
         try {
            config( '%s.cms', $sites[$siteid]['short'] );
         } catch( Exception $e ) {}
      }
         
      // Fetch list of all templates in db.
      $templates = Settings::get( 'cms', 'templates', array() );
      $ret = array();
      
      foreach( $templates['menuitem'] as $id => $title ) {
         $ret[] = array(
            'id' => $id,
            'title' => $title,
         );
      }
      // Set return values.
      $this->templates = array_merge( $staticListElements, $ret );
      $this->result = true;
      $this->message = 'OK';
      
   }

}

?>
