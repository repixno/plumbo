<?PHP
   class KampanjeSites extends WebPage implements IView {
      
      protected $template = null;
      
      public function Execute() {
         
         relocate( '/frontpage' );
         
      }
      
      
      public function Nikon(){
         
         $this->template = 'kampanje.nikon';
         
      }
      
      
      
    }
    
    

?>