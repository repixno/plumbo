<?php

   Dispatcher::extendView( 'content.products' );

   class EXtendedProductList extends ArticleEditor {

      //protected $template = 'content.textentity';

      public function Execute( $id = null, $boolean = null ) {

         if( !class_exists( $this->objectclass ) ) return false;
         $this->classname = $this->objectclass;

         $this->entitylist = $this->listEntities();

      }

      protected function listEntities() {

         $this->setTemplate( 'content.productsext' );

         $entity['header'] = __( '%s list', $this->objectclass );
         $entity['addlink'] = sprintf( '%s/0', $this->getEditorRoot() );
         $entity['backlink'] = $this->getEditorRoot();

         $entities = array();
         $groups = array();

         $collection = new $this->objectclass();
         if ( count( $collection ) ) {

            $counter = 0;
            foreach( $collection->collection( array( 'id' ), array( 'deleted' => null ) )->fetchAllAs( $this->objectclass ) as $productentity ) {

               if( !$productentity->id ) continue;

               // Build options array.
               $optorg = $productentity->options;
               $options = array();
               for ( $i=0; $i<count( $optorg ); $i++ ) {

                  // Tags.
                  $tags = explode( ' ', $optorg[ $i ]->tags );
                  if ( !count( $tags ) ) $tags = null;

                  // Add sequencing number to options array.
                  $options[ $i ] = array(
                     'seqno' => $i,
                     'id' => $optorg[ $i ]->id,
                     'title' => $optorg[ $i ]->title,
                     'price' => $optorg[ $i ]->price,
                     'tags' => $tags,
                     'weight' => $optorg[ $i ]->unitweight
                     );

               }
               if ( !count( $options ) ) {

                  $options[] = array( 'seqno' => 0 );

               }

               // Product images.
               $images = strlen( trim( $productentity->images ) ) ? count( explode( ',', $productentity->images ) ) : 0;

               // Menu items.
               $menus = $productentity->menuitems;
               if ( !count( $menus ) ) $menus = null;

               $entities[] = array(
                  'id'        => $productentity->id,
                  'title'     => $productentity->title,
                  'edit'      => sprintf( '%s/%d', $this->getEditorRoot(), $productentity->id ),
                  'delete'    => sprintf( '%s/delete/%d', $this->getEditorRoot(), $productentity->id ),
                  'type'      => $productentity->type ? __( ucfirst( $productentity->type ) ) : __( ucfirst( $this->objectclass ) ),
                  'grouping'  => $productentity->grouping,
                  'refid'     => $productentity->refid,
                  'options'   => $options,
                  'optionsize' => count( $options ),
                  'menus'     => $menus,
                  'menuhost'  => sprintf( '%s://%s', Settings::get( 'default', 'protocol' ), Settings::get( 'default', 'hostname' ) ),
                  'images'    => $images,
                  'zebra'     => ($counter++%2) ? 'even' : 'odd'
               );

            }

         }

         if( !count( $entities ) ) {
            return array(
               'header' => __( 'No %ss', $this->objectclass ),
               'addlink'=> $entity['addlink'],
               'list'   => array(),
               'groups' => array(),
               'type'   => strtolower( $this->objectclass ),
               'backlink'=>$this->getEditorRoot(),
            );
         }

         // Do post-op sorting
         function sortentities( $a, $b ) {
            return strcasecmp( $a['title'], $b['title'] );
         }  usort( $entities, 'sortentities' );

         $groups[] = 'nogroup';

         $entity['list'] =    $entities;
         $entity['type'] =    strtolower( $this->objectclass );

         return $entity;

      }


   }


?>
