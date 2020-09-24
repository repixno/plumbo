<?PHP

   /***********************************
    * Load a saved project to editor
    *
    *
    **********************************/

   import( 'pages.json' );
   model( 'producteditor.pages'  );

   class SaveProject extends JSONPage implements NoAuthRequired, IView {
         
         public function Execute(){
            
                $category = 'twopages'; 
                $malpageid = $_POST['malpageid'];
                $page = new DBproducteditorPages( $malpageid );
                $array = array(
                        'printheight' => $page->printheight,
                        'printwidth' => $page->printwidth,
                        'content' => $page->content,
                    );
                
                //$data2 = unserialize( $data );
                $this->result = true;
                $this->data = $array;
                $this->thumbs = $thumbarray;
                $this->message = 'OK';
                return true;
         }      
   }



?>
