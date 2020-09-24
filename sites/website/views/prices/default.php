<?php

   /**
    * List prices for all products
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * @author Oyvind Selbek <oyvind@selbek.com>
    */

   import( 'website.textentity' );
   import( 'website.product' );
   
   class PriceList extends WebPage implements IView {
      
      protected $template = 'prices.index';
      
      static $maximumproductsperpage = 16;
      
      public function Execute( $regenerate = false ) {
         
         
         
         if( Dispatcher::getPortal() == 'TK-001' ){
            
            $this->cewepricelist = $this->ceweprices();
            
         }
         
         
         $this->DataSource( $regenerate ); 
         
      }
      
      public function XML( $regenerate = false ) {
         
         header( 'Content-Type: text/xml' );
         $this->setTemplate( 'prices.xml' );
         return $this->Execute( $regenerate );
         
      }
      
      private function DataSource( $regenerate ) {
         
         function sortProductItems( $a, $b ) {
            return strcmp( $a['product']['title'], $b['product']['title'] );
         }
         
         function sortMenuItems( $a, $b ) {
            return strcmp( $a['menu']['title'], $b['menu']['title'] );
         }
         
         function sortSubItems( $a, $b ) {
            return strcmp( $a['title'], $b['title'] );
         }
         
         $cachename = sprintf( 'test-pricelist-%s-%s', Dispatcher::getPortal(), i18n::languageCode() );
         $pricelist = CacheEngine::read( $cachename );
         
         $siteid = Session::get( 'siteid', 1 );
         
         if( $regenerate || !is_array( $pricelist ) ) {
            
            $res = DB::query( "SELECT DISTINCT(sm.id),
                                      sm.parentid, 
                                      sm.url 
                                 FROM site_menu sm 
                            LEFT JOIN site_menu_portals smp
                                   ON sm.id = smp.menuid
                                WHERE sm.url LIKE '/products/%' 
                                  AND sm.deleted IS NULL 
                                  AND sm.siteid = ?
                                  AND ( smp.portal = 'all' OR smp.portal = ? )
                             ORDER BY sm.url ASC", $siteid, Dispatcher::getPortal() );
            while( list( $id, $parentid, $url ) = $res->fetchRow() ) {
               $parented[$parentid][$id] = $url;
               $allitems[$id] = $url;
            }
            

            
            $res = DB::query( "SELECT smc.menuid, 
                                      smc.textentityid 
                                 FROM site_menu_contents smc 
                            LEFT JOIN site_menu sm 
                                   ON sm.id = smc.menuid
                            LEFT JOIN site_textentity t 
                                   ON t.id=smc.textentityid 
                                WHERE t.type = 'product' 
                                  AND t.deleted IS NULL 
                                  AND sm.siteid = ?
                             ORDER BY smc.sortorder", $siteid );
            while( list( $menuid, $productid ) = $res->fetchRow() ) {
               $entities[$menuid][$productid] = true;
            }
            

            
            $items = $this->buildTree( $parented, $entities );
            
            
            $output = array();
            foreach( $items as $parentid => $productitems ) {
               
               // did this section have more than X products?
               if( count( $productitems ) <= PriceList::$maximumproductsperpage ) {
                  
                  usort( $productitems, 'sortProductItems' );
                  
                  $sections = array(
                     array(
                        'title' => 'General',
                        'products' => $productitems,
                     ),
                  );
                  
                  $singlesection = true;
                  
               } else {
                  
                  // create a subtree for subsections of this item
                  $subitems = $this->buildTree( $parented, $entities, array(), $parentid );
                  $sections = array();
                  
                  foreach( $subitems as $subitemid => $productitems ) {
                     
                     usort( $productitems, 'sortProductItems' );
                     
                     $submenuitem = new MenuItem( $subitemid );
                     
                     $sections[] = array(
                        'title' => $submenuitem->title,
                        'products' => $productitems,
                     );
                     
                  }
                  
                  uasort( $sections, 'sortSubItems' );
                  
                  $singlesection = false;
                  
               }
               
               $menuitem = new MenuItem( $parentid );
               $output[] = array(
                  'menu' => $menuitem->asArray(),
                  'single' => $singlesection,
                  'sections' => $sections,
               );
               
            }
            
            usort( $output, 'sortMenuItems' );
            
            // Setup all product data for template. Use product tagged as print
            $res = DB::query( "
               SELECT po.prodno
               FROM site_product_option AS po LEFT JOIN site_textentity AS te ON po.id = te.id 
               WHERE po.tags 
               LIKE( '%print%' ) OR 
                  ( po.tags LIKE( '%enlargement%' ) AND te.deleted IS NULL ) OR 
                  ( po.tags LIKE( '%productionmethod%' ) AND te.deleted IS NULL ) OR 
                  ( po.tags LIKE( '%correctionmethod%' ) AND te.deleted IS NULL ) OR 
                  ( po.tags LIKE( '%paperquality%' ) AND deleted IS NULL )" );
            
            $prints = array();
            $enlargements = array();
            $productionmethods = array();
            $correctionmethod = array();
            $paperquality = array();
            
            while( list( $prodno ) = $res->fetchRow() ) {
               
               $productoption = ProductOption::fromProdNo( $prodno );
               if( $productoption->isLoaded() && $productoption instanceof ProductOption ) {
                  
                  $product = new Product( $productoption->productid );
                  if( $product->isLoaded() ) {
                     $producttitle = $product->title;
                     $productbody = $product->body;
                     $productingress = $product->ingress;
                  }
                  
                  $productarray = $productoption->asArray();
                  $productarray["title"] = $producttitle;
                  $productarray["body"] = $productbody;
                  $productarray["ingress"] = $productingress;
                  
                  if( stristr( $productoption->tags, 'print' ) ) {
                     $prints []= $productarray;
                  } else if( stristr( $productoption->tags, 'enlargement' ) ) {
                     $enlargements []= $productarray;
                  } else if( stristr( $productoption->tags, 'productionmethod' ) ) {
                     $productionmethods []= $productarray;                  
                  } else if( stristr( $productoption->tags, 'correctionmethod' ) ) {
                     $correctionmethod []= $productarray;
                  } else if( stristr( $productoption->tags, 'paperquality' ) ) {
                     $paperquality []= $productarray;
                  }
                  
               }
               
            }
            
            // alphabetize subcategories
            uasort( $prints, 'sortSubItems' );
            uasort( $enlargements, 'sortSubItems' );
            uasort( $productionmethods, 'sortSubItems' );
            uasort( $correctionmethod, 'sortSubItems' );
            uasort( $paperquality, 'sortSubItems' );
            
            // produce the complete dataset
            $pricelist = array(
               'products' => $output,
               'prints' => $prints,
               'enlargements' => $enlargements,
               'productionmethods' => $productionmethods,
               'correctionmethods' => $correctionmethod,
               'paperquality' => $paperquality,
            );
            
            // write it to memory cache
            CacheEngine::write( $cachename, $pricelist, 86400 );
            
         }
         
         // assign the variable to the view
         $this->pricelist = $pricelist;
         
      }
      
      private function buildTree( $parented, $entities, $items = array(), $itemid = 0, $parentid = 0 ) {
         
         if( count( $parented[$itemid] ) )
         foreach( $parented[$itemid] as $id => $url ) {
            
            $segmentid = $parentid > 1 ? $parentid : $id;
            
            if( count( $entities[$id] ) && $id != 1 )
            foreach( $entities[$id] as $productid => $d ) {
               
               $product = new Product( $productid );
               $items[$segmentid][$productid] = array( 
                  'product' => $product->asArray(),
                  'url' => $url,
               );
               
            }
            
            $items = $this->buildTree( $parented, $entities, $items, $id, $segmentid );
            
         }
         
         return $items;
         
      }
      
      
      
      private function ceweprices( ){
         $lang = 'NO';
         
         $serialized =  file_get_contents( '/data/pd/ef28/takkekort/ceweprice-' . $lang . '.txt', $serialized  );
            
         $unserialized =  unserialize( $serialized ) ;
         return $unserialized;
         

      }
      
   }
   
?>
