<?php

import( 'website.menu' );
import( 'pages.json' );

class APIUpdateConnectedOrder extends JSONPage implements IValidatedView {

   public function Validate() {

      return array(
         'execute' => array(
            'post' => array(
               'id' => VALIDATE_INTEGER,
               'orderlist' => VALIDATE_DEFAULT
            )
         )
      );

   }

   public function Execute() {

      $this->setTemplate();

      $menuId = $_POST[ 'id' ];

      $orderList = explode( ',', $_POST[ 'orderlist' ] );

      // Fetch list of all content for menu item.
      $menu = new MenuItem( $menuId );

      $duplicate = false;
      $duplicates = array();

      for ( $i=0; $i<count($orderList);$i++ ) {

         // Extract item id and section id from the received list.
         list( $id, $section, $oldsection ) = explode( '::', $orderList[ $i ] );
         if ( $oldsection == '_default') $oldsection = '';

         if ( isset( $duplicates[ $id. $section ] ) ) {

            $duplicate = true;

         } else {

            $duplicates[ $id . $section ] = true;

            // Update order and section.
            $menu->setContentOrder( $id, $i, $section, $oldsection );

         }

      }

      $connected = $menu->getContentList();

      $ret = array();
      foreach ( $connected as $key=>$val ) {

         $ret[] = array(
            'id' => $val[ 'textentityid' ],
            'title' => $val[ 'title' ],
            'section' => $val[ 'section' ]
            );

      }

      $this->menuitemid = $menuId;
      $this->connected = $ret;
      $this->result = $duplicate ? false : true;
      $this->message = $duplicate ? 'No duplicate elements in the same section' : 'OK';

   }

}

?>
