<?php

import( 'website.menu' );
import( 'pages.json' );

class APIUpdateMenuOrder extends JSONPage implements IValidatedView {

   public function Validate() {

      return array(
         'execute' => array(
            'post' => array(
               'portal' => VALIDATE_STRING,
               'changestart' => VALIDATE_DEFAULT,
               'changeend' => VALIDATE_DEFAULT
            )
         )
      );

   }

   public function Execute() {

      $this->setTemplate();

      $portalId = $_POST[ 'portal' ] != 'null' ? $_POST[ 'portal' ] : null;
      $changeStart = explode( ',', $_POST[ 'changestart' ] );
      $changeEnd = explode( ',', $_POST[ 'changeend' ] );
      $menuId = $changeStart[ 1 ];

      $oldData = array(
         'itemid' => $changeStart[ 1 ],
         'parentid' => $changeStart[ 0 ],
         'position' => $changeStart[ 2 ]
         );
      $newData = array(
         'itemid' => $changeEnd[ 1 ],
         'parentid' => $changeEnd[ 0 ],
         'position' => $changeEnd[ 2 ]
         );

      // Fetch list of all content for menu item.
      $menuitem = new MenuItem( $menuId );

      // Update the sort order of menu items.
      $menuitem->updateSortOrder( $oldData, $newData );

      // Return list of all menu items to update options.
      $ret = array();

      foreach ( Menu::listMenuItems( $portalId ) as $item ) {

         $nodeData = array(
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
            'level' => substr_count( $item->url, '/' )-2
            );
            
         $ret[] = $nodeData;

      }

      $this->menulist = $ret;

      $this->menuitemid = $menuId;
      $this->result = true;
      $this->message = 'OK';

   }

}

?>
