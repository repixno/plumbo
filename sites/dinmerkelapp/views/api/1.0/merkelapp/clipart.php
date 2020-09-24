<?php


   import( 'pages.json' );

   class ListClipart extends JSONPage implements NoAuthRequired, IView {

         public function Execute($type=null, $category=null) {
            
         
         $type = $_POST['type'] ? $_POST['type']: $type ;  
         $category = $_POST['clipart'] ? $_POST['clipart']: $category;   
         
         if( $type == 'fargelapp' ){
            $type = 'motiv';
         }else{
            $type = 'clipart';
         }
         $images = array();
         $xmlfile = ("/var/www/repix/sites/dinmerkelapp/webroot/gfx/merkelapp/$type/$category/category.xml");		               
		
         // viss fila "category.xml" fins, loadar en fila, sett $tags som string for alle <file> objekta inni xml-en,
         // og kjøyrar dei inni $images arrayet slik dei blir printa ein etter ein.
         if( file_exists($xmlfile ) ){
            
			$xml = simplexml_load_file($xmlfile);
			foreach($xml->files->file as $tags ) {
			   $images[] = (string)$tags;
			}

	  
			// om fila ikkje eksisterar, laga en ein ny xml-fil, med <clipart> som hovudParenten av objekta.
			// no som $xml er referert som <clipart>, addar man ein Attribute som seier kalaks mappe dessa filene er i, also ..catname="Dyr".. mappa.
			// Deretter adda ein child til <clipart> som skal vere parenten til alla filnamna som blir printa ut i scripte.
	 
			//$xml = new SimpleXMLElement("<clipart></clipart>");
			//$xml->addAttribute('catname', $category );
			//$files = $xml->addChild('files');
			//foreach( glob( dirname($xmlfile) . '/*.png' ) as $imagefile ){
			//	 $file = $files->addChild('file', basename( $imagefile )  );
			//	 $file->addAttribute('title', basename( $imagefile, '.png' )  );
			//	 $images[] = basename( $imagefile ) ;
			 //}
		 
			/*
			$dom = new DOMDocument("1.0");
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$dom->loadXML($xml->asXML());
			$dom->save($xmlfile);*/

            
			//$xml->asXml($xmlfile);
         }else{
            
            foreach( glob('/var/www/repix/sites/dinmerkelapp/webroot/gfx/merkelapp/'. $type . '/' .$category. '/*.png') as $res){
                        $images[] = basename( $res );
               }
            
            
         }

        
        // med glob(), setter ein alle filene som held seg i parent-mappa til $xmlfile, og sett dei inni $stringen imagefile. 
        // deretter legg til ein child til <files> som <file>, og bruker foreach til å ta ein og ein .png fil inni ein og ein <file> tag.
        // legger til ein attribute for <file> der basename viser tagen pathen til der fila er, og tar vekk ".png". etter de set basename($imagefile)
        // ein <file> tag med ein fil inni arrayet, og loopar prossesen til alle filene har blitt printa ut i $images[]-arrayet.
        // asxml returnera ein xml string basert på $xml(stringen som går på simpleXML).
                
                
                
                $this->result = true;
                $this->images = $images;
                $this->message = 'OK';
                return true;
         }  
   }

                
                
?>