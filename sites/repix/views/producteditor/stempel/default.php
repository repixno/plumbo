<?php


   model( 'producteditor.category' );
   model( 'producteditor.assets'  );
   model( 'producteditor.templates'  );
   model( 'producteditor.pages'  );
   
   class Producteditor extends WebPage implements IView {
      
      protected $template = 'producteditor.stempel.index';
      
      
      public function execute( $product = null, $templateid = null ) {
         
            $pages = DB::query( "SELECT * FROM producteditor_pages WHERE templateid = ? ORDER BY id", $templateid )->fetchAll( DB::FETCH_ASSOC );
            $malpages = array();
            
            if( $pages ){
                
                foreach( $pages as $page ){
                        
                    $x = $page['printwidth'];
                    $y = $page['printheight'];
                    $editx = 750;
                    $edity = (int)($y / ( $x / 550 ) );
                        
                        
                    $malpages[] = array(
                        'malpageid' => $page['id'], 
                        'x' => $x,
                        'y' => $y,
                        'editx' => $editx,
                        'edity' => $edity,
                        'thumbnail' => str_replace( "\"" , "" , $page['thumbnail'] )
                    );
                }
            }else{
                $malpages = array(
                    'x' => 500,
                    'y' => 300,
                    'artnr' => 0,
                    'editx' => 500,
                    'edity' => 300,  
                );
                
            }
            
            $albums = Album::enum();
            $this->templateid = $templateid;
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