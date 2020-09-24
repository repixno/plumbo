<?PHP


   import( 'pages.json' );

   class Loadpage extends JSONPage implements NoAuthRequired, IView {
    
         public function Execute(){
            
                $malid = $_POST['malid']; 
                $malpageid = $_POST['malpageid'];
                
                
                $data = DB::query( "SELECT * FROM producteditor_pages WHERE templateid = ? AND id = ?", $malid, $malpageid )->fetchAll( DB::FETCH_ASSOC );
                
                //$data2 = unserialize( $data );
                $this->result = true;
                $this->data = $data[0];
                $this->message = 'OK';
                return true;
         }      
   }



?>
