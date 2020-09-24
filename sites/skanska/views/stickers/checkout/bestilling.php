<?php
   
   import( 'website.product' );
   import( 'website.gifttemplate' );
   import( 'website.uploadhelper' );
   import( 'website.album' );
   import( 'website.image' );
   model( 'order.leverpostei' );
   
   class SkanskaEditor extends WebPage implements IView {
      
      
      public function Execute(){
         
         $this->template = 'skanska.order';
         
         $lokkid =  Session::get( 'stabburet-lokkid' );
         $leverpostei = new DBLeverpostei( $lokkid );
         $this->lokk  = array(
               'id' => $leverpostei->id,
               'thumbid' => $leverpostei->thumbid,
               'imageid' => $leverpostei->imageid,
               'imagepos' => $leverpostei->imagepos,
               'name' => $leverpostei->name,
               'year' => $leverpostei->year,
               'malsize' => $leverpostei->malsize,
         );
               
        // $productarray = array( 3322, 3320, 3318 ,3334, 3326, 4003 );
        // 4003 smekke
        // kjøleskapsmagnet mobile 5499
         $productarray = array( );
         $productarray_mobile = array();
         $product = array();
         $product_mobile = array();
         foreach ( $productarray as $ret ){
            $productcontainer = new Product( $ret );             
            $product[] = $productcontainer->asArray();
         }
         foreach ( $productarray_mobile as $ret ){
            $productcontainer = new Product( $ret );             
            $product_mobile[] = $productcontainer->asArray();
         }
         $this->sections = array(
            'products' => $product,
            'products_mobile' => $product_mobile
         );
         
         if( Login::isLoggedIn() ) {
            $user = new User( Login::userid() );
         } else {
             $user = new User();
         }   
         $this->user = $user->asArray();
      }
   }