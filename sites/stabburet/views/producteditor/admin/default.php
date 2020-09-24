<?php
    
    model( 'producteditor.category' );
    model( 'producteditor.assets'  );
    model( 'producteditor.templates'  );
    model( 'producteditor.pages'  );
    
    class ProducteditorAdmin extends WebPage implements IView {
      
        protected $template = 'producteditor.admin.index';
      
      
        public function execute( $productid = null ){
            
            
            //$productid = $_GET['productid'];
            
            
            if( $productid == null ){
                
                $this->template = 'producteditor.admin.preindex';
                
            }else{
                
                $templates = DB::query( "SELECT * FROM producteditor_templates WHERE productid = ? ORDER BY id", $productid )->fetchAll( DB::FETCH_ASSOC );
                
                $this->maler = $templates;
                $this->productid = $productid;
                
                
                if( $_POST['x'] > 1 && $_POST['y'] > 1  && $_POST['artnr']  > 0 ){
                    
                    
                    $title = $_POST['title'];
                    $productid = $_POST['artnr'];
                    $printheight = $_POST['y'];
                    $printwidth = $_POST['x'];
                    
                    $newmal = new DBproducteditorTemplates();
                    $newmal->productid = $productid;
                    $newmal->title = $title;
                    $newmal->category = 1;
                    $newmal->printheigth = (int)$printheight;
                    $newmal->printwidht = (int)$printwidth;
                    $newmal->created = date( 'Y-m-d H:i:s' );
                    $newmal->saved = date( 'Y-m-d H:i:s' );
                    $newmal->visible = false;
                    
                    $newmal->save();
                    
                    relocate( $_SERVER['REQUEST_URI'] );
                }   
            }
        }
        
        
        public function edit(){
            
            $this->template = 'producteditor.admin.edit';
            $this->backgrounds = DB::query( "SELECT * FROM producteditor_assets WHERE type = ?", 'backgrounds' )->fetchAll( DB::FETCH_ASSOC );
            $this->cliparts = DB::query( "SELECT * FROM producteditor_assets WHERE type = ?", 'clipart' )->fetchAll( DB::FETCH_ASSOC );
            
            $malid = $_GET['malid'];
            
            if( !$malid ){
                relocate( '/producteditor/admin/' );
            }
            $mal = new DBproducteditorTemplates( $malid );

            if( $_POST['newpage'] ){
                
                $newpage = new DBproducteditorPages();
                $newpage->templateid = $malid;
                $newpage->title = $malid;
                $newpage->printwidth = $mal->printwidth;
                $newpage->printheight = $mal->printheight;
                
                $newpage->save();
                relocate( $_SERVER['REQUEST_URI'] );
                
            }
    
            $this->mal = $mal;
            
            $pages = DB::query( "SELECT * FROM producteditor_pages WHERE templateid = ? ORDER BY id", $mal->id )->fetchAll( DB::FETCH_ASSOC );
            
            
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
            
            $this->malpages = $malpages;
        }
      
   }
   
?>