<?php
   import( 'website.product' );
   import( 'website.gifttemplate' );

   class CreateUkeplan extends WebPage implements IView {
      
      protected $template = 'editorukeplan.editor';
      
      public function execute( $productid = 0, $productoptionid = 0, $gifttemplateid = 0, $orientation = 'portrait' ) {
         
         if( $orientation == 'landscape' ){
            $this->template = 'editorukeplan.editor_landscape';
         }

         $product = new Product( $productid );
         $productoption = new ProductOption( $productoptionid );
         
         $maleroptions = array();
         
         $maler = DB::query( "SELECT id FROM site_product_option WHERE productid = ?" , $productid )->fetchAll( DB::FETCH_ASSOC );
         
         foreach( $maler as $mal ){
            $productmal = new ProductOption( $mal['id'] );
            $productmaltitle = $productmal->asArray();
            
            if( $mal['id'] == $productoptionid ){
               $productmaltitle['default'] = "true";
                $maleroptions[]= $productmaltitle;
            }else{
                $maleroptions[] = $productmaltitle;
            }
           
         }
         if( count($maleroptions) > 1  ){
            $this->maleroptions = $maleroptions;
         }
         
         $template = new GiftTemplate( $gifttemplateid );
         $templateArray = $template->asArray();
         $pages = $templateArray['pages'][0];
         
         //5000 serie er danske ugeplan
         
         $backgroundarray = array( 2762, 2760, 2764, 2766, 2768,5060,5062,5065,5065,5068,5070,5260 );
         
         if( $pages['bgcolor'] == "000000" ){
            $textcolor = "#ffffff";
            $velgbilde  =  "_black";
         }else{
            $textcolor = "#000000";
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
         
         
         $this->selected = array(
            'templateid' => $gifttemplateid,
            'template' => $template->asArray(),
            'productoptionid' => $productoptionid,
            'productoption' => $productoption->asArray(),
            'productid' => $productid,
            'product' => $product->asArray(),
            'templatefile' => $templatearray['pages'][0]['fullsize_src']
         );

         $this->ukeplantemplates = $templates;
         
      }
      
      public function clipart(){
         
      }
      
      
      
   }
   
?>