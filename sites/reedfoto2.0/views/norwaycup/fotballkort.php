<?php
   
   class Fotballkort extends WebPage implements IView {
      
        protected $template = 'norwaycup.fotballkort';
      
        public function Execute( $imageid = 0 ) {
            
            
            $fotballkort = DB::query( "SELECT * FROM project_fotballkort WHERE imageid = ? " , $imageid )->fetchAll( DB::FETCH_ASSOC ); 
            
            
            $this->fotballkort = array( 'imageid' => $imageid, 'name' => $fotballkort[0]['name'] );
        
        }
      
   }
   
?>