<?php

   /**
    * List prices for all ukeplan products
    * 
    */

   import( 'website.textentity' );
   import( 'website.product' );
   
   class UkeplanPricelist extends WebPage implements IView {
      
      
      protected $template = 'prices.ukeplan';
      
      public function Execute(){
         
         $products = array();
         try{
         $kake = DB::query("SELECT *
                            FROM site_menu sm 
                            LEFT JOIN site_menu_portals smp
                              ON sm.id = smp.menuid  
                            WHERE sm.parentid = ?
                            AND smp.portal = ? AND smp.menuid NOT IN (390)
                            ORDER BY sm.sortorder " ,1,
                             Dispatcher::getPortal() 
                             
                        )->fetchAll( DB::FETCH_ASSOC  );            
         }catch ( Exception  $e){
            util::Debug( $e );
         }
         
         
         
         foreach ( $kake as $res ){
            
            $translation = unserialize(  $res['translation'] );
            
            $products = DB::query("SELECT * 
                                   FROM site_menu_contents
                                   WHERE menuid = ? order by section, sortorder
                                   ", $res['id']
                        )->fetchAll( DB::FETCH_ASSOC  );
           
           $ret = array();
           try{
              foreach ( $products as $product ){
                 
                 if( $product['textentityid']  == 3016 ){
                    $ret[] = array(
                        'title' => "Ta kontakt for pris",
                        'url'   => "/om-ukeplan/"
                    );
                 }
                 else if( $product['textentityid']  == 3015 ){
                    $ret[] = array(
                        'title' => "Ta kontakt for pris",
                        'url'   => "/om-ukeplan/"
                    );  
                 }
                 else{
                    $productdetails = new Product( $product['textentityid']);
                    $ret[] = $productdetails->asArray();
                 }
              }
           }catch ( Exception  $e ){

           }

           $prodlist[] = array(
                  'title' => $translation[i18n::languageCode()]['title'],
                  'products' => $ret
           
           );
           
         }
                      
         
         $this->pricelist = $prodlist;
           
      }
      
      
   }