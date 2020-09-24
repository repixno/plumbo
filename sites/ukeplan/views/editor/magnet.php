<?php
   import( 'website.product' );
   import( 'website.gifttemplate' );

   class CreateMagnetUkeplan extends WebPage implements IView {
      
      protected $template = 'editor.magnet';
      
      public function execute( $productid = 0, $productoptionid = 0, $gifttemplateid = 0 ) {
         
         
         if( $productid == 3077 ){
            $this->template = 'editor.dinner';
         }

         $product = new Product( $productid );
         $productoption = new ProductOption( $productoptionid );

         $template = new GiftTemplate( $gifttemplateid );
         $templateArray = $template->asArray();
         $pages = $templateArray['pages'][0];
         
         
         
         $backgroundarray = array( 2762, 2760, 2764, 2766, 2768,5060,5062,5065,5065,5068,5070 );
         
         if( $pages['bgcolor'] == "010101" ){
            $textcolor = "#ffffff";
            $velgbilde  =  "_black";
         }else{
            $textcolor = "#010101";
            $velgbilde  =  "";
         }
         
         $templates = array(
            1 => array(
               'id'   => $templateArray['id'],
               'mal_width'       => $pages['fullsize_x'],
               'mal_height'      => $pages['fullsize_y'],
               'imagefield_height'=> $pages['fullsize_pos_dy'],
               'imagefield_width'=> $pages['fullsize_pos_dx'],
               'imagefield_x'    => $pages['fullsize_pos_x'],
               'imagefield_y'    => $pages['fullsize_pos_y'],
               'imagefield_space'=> 35,
               'backgroundcolor' => "#" . $pages['bgcolor'],
               'textcolor'       => $textcolor,
               'velgbilde'       => $velgbilde
            )
         
         );
         
         if( in_array( $productid , $backgroundarray  ) ){
            
            $this->choosebackground = 'true';
            
         }
         
         $templatearray = $template->asArray();
         
         
         $portal = Dispatcher::getPortal();
         
         if( $portal == 'VP-001' ){
            $ext = "SV_";
         }
         else if( $portal == 'UP-DK'){
            $ext = 'DK_';
         }
         else{
            $ext = '';
         }

         $this->selected = array(
            'templateid' => $gifttemplateid,
            'template' => $templatearray,
            'productoptionid' => $productoptionid,
            'productoption' => $productoption->asArray(),
            'productid' => $productid,
            'product' => $product->asArray(),
            'templatefile' =>  $templatearray['pages'][0]['fullsize_src']
         );

         $this->ukeplantemplates = $templates;
         
      }
      
   }
   
?>