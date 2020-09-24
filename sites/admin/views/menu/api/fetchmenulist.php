<?php

import( 'website.menu' );
import( 'pages.json' );
model( 'site.portal' );

class APIFetchMenuList extends JSONPage implements IValidatedView {

   public function Validate() {

      return array(
         'execute' => array(
            'post' => array(
               'portal' => VALIDATE_STRING
            )
         )
      );

   }

   public function Execute() {

      $this->setTemplate();

      $portal = $_POST[ 'portal' ];
      if ( $portal == 'null' || $portal == 'all' ) $portal = null;

      $items = Menu::listMenuItems( $portal );

      if ( count( $items ) ) {

         $this->menuitems = $this->buildSubTree( 0, 0, $items );

      }

      // Set return values.
      $this->result = true;
      $this->message = 'OK';

   }

   private function buildSubTree( $parent, $level, $list ) {

      $ret = array();
      foreach ( $list as $item ) {

         if ( $item->parentid == $parent ) {

            $newNode = array(
               'id' => $item->id,
               'title' => $item->getTitle( false ),
               'display' => $item->title,
               'parentid' => $item->parentid,
               'position' => $item->sortorder,
               'identifier' => $item->identifier,
               'translated' => array(
                  'urlnames' => $item->getTranslatedURLNames(),
                  'titles' => $item->getTranslatedTitles(),
               ),
               'level' => null,
               'image' => $item->image,
               'template' => $item->template,
               'articleid' => $item->articleid,
               'urlname' => $item->urlname,
               'url' => $item->url,
               'level' => $level,
               'score' => $item->score,
            );

            $ret[] = $newNode;

            $subs = $this->buildSubTree( $item->id, $level+1, $list );

            if ( count( $list ) ) {

               $ret = array_merge( $ret, $subs );

            }

         }

      }

      return $ret;

   }

}

?>
