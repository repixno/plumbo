<?PHP


   import( 'pages.json' );

   class ListBackgrounds extends JSONPage implements NoAuthRequired, IView {
      

         public function Execute() {
            
            $category = $_POST['backgroundcat'];    
            $backgrounds = DB::query( "SELECT * FROM producteditor_assets WHERE type =? AND category = ?" ,'backgrounds', $category )->fetchAll( DB::FETCH_ASSOC);
            
            foreach( $backgrounds as $res){
                     $images[] = $res['filename'];
            }
            $this->result = true;
            $this->images = $images;
            $this->message = 'OK';
            return true;
         }      
   }



?>
