<?PHP
    model( 'producteditor.pages'  );
    import( 'pages.json' );

    class SavePage extends JSONPage implements NoAuthRequired, IView { 
         
        public function Execute() {
            
            $malid = $_POST['malid']; 
            $malpageid = $_POST['malpageid'];
            $data = $_POST['data'];
            $thumb = $_POST['thumb'];
            
            $page = new DBproducteditorPages( $malpageid );
            
            $page->content = $data;
            $page->thumbnail = $thumb;
            $page->save();
            
            $this->result = true;
            $this->data =  $data;
            $this->message = $message;
            return true;
        }      
    }



?>
