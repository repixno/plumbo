<?php

import( 'website.menu' );
import( 'pages.json' );
import( 'templating.sectionparser' );
model( 'site.product' );

class APIFetchConnected extends JSONPage implements IValidatedView {

   public function Validate() {

      return array(
         'execute' => array(
            'post' => array(
               'id' => VALIDATE_STRING
            )
         )
      );

   }

   public function Execute() {

      $this->setTemplate();

      $menuId = $_POST[ 'id' ];

      // Fetch list of all content for menu item.
      $menu = new MenuItem( $menuId );
      $connected = $menu->getContentList();

      $cssClasses = array(
         'product' => 'ss_basket',
         'article' => 'ss_page'
         );

      $ret = array();
      foreach ( $connected as $key=>$val ) {

         if ( empty( $val[ 'section' ] ) ) {

            $val[ 'section' ] = '_default';

         }

         $ret[] = array(
            'id' => $val[ 'textentityid' ],
            'title' => $val[ 'title' ],
            'type' => $val[ 'type' ],
            'cssclass' => $cssClasses[ $val[ 'type' ] ],
            'position' => $val[ 'sortorder' ],
            'section' => $val[ 'section' ]
            );

      }

      // Set return values.
      $this->menuitemid = $menuId;
      $this->connected = $ret;
      $this->sections = $this->listSections( $menu->template );
      $this->result = true;
      $this->message = 'OK';

   }

   private function listSections( $template ) {

      // Fetch list of all sections for this template.
      $retSections = array(
         array(
            'id' => '_default',
            'title' => __( 'Default' ),
            'sysid' => ''
            )
         );

      foreach ( TemplatingSectionParser::ParseTemplate( $template ) as $key=>$val ) {

         $retSections[] = array(
            'id' => $key,
            'title' => $val,
            'sysid' => $key
            );

      }

      return $retSections;

   }

}

?>
