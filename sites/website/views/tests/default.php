<?PHP
   
   class TestsIndex extends WebPage implements IView {
      
      protected $template = null;
      
      public function Execute() {

	        $homepage = file_get_contents('http://nelly.eurofoto.no/api/1.0/user/getip');
            echo $homepage;
        
      }
      
   }
   
?>
