<?php


    import( 'website.product' );
    import( 'website.gifttemplate' );
    import( 'website.uploadhelper' );
    import( 'website.album' );
    import( 'website.image' );
   
    import( 'legacy.ef2x' );
   
   
    class Producteditor extends WebPage implements IView {
      
      protected $template = 'producteditor.test';
      
      
        public function Execute( $productid = 0, $productoptionid = 0, $gifttemplateid = 0, $imageid = 0 ) {
           
           
           
           
           
           $mal = DB::query("SELECT * FROM mal WHERE malid = ?", $gifttemplateid )->fetchAssoc();
         
         
            $template = new GiftTemplate( $gifttemplateid );
         
         
            $malpages = $template->asArray();
            
            Util::Debug($template->asArray());
            
            //exit;
         
         
            $albums = Album::enum();
            $this->templateid = $gifttemplateid;
            $this->albums = $albums;
            $this->malpages = $malpages;
            $this->backgrounds = DB::query( "SELECT * FROM producteditor_assets WHERE type = ?", 'backgrounds')->fetchAll( DB::FETCH_ASSOC );
            $this->cliparts = DB::query( "SELECT * FROM producteditor_assets WHERE type = ?", 'clipart')->fetchAll( DB::FETCH_ASSOC );
           
        }
      
      private function Backgrounds(){
         
         $backgroundlist = array();
         
         $categories = DB::query( "SELECT * FROM producteditor_category")->fetchAll( DB::FETCH_ASSOC );
         
         foreach( $categories as $cat ){
            
            $backgroundlist[] = array(
               'id' => $cat['id'],
               'title' => $cat['title']
            );
            
         }
         return $backgroundlist;
      }
      
   }
   
?>