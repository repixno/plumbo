<?php


   import( 'pages.json' );

   
   class APIPriceGet extends JSONPage implements NoAuthRequired,IView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'fields' => array(
                  'folder' => VALIDATE_STRING,
                  'quantity' => VALIDATE_INTEGER,
               ),
               'post' => array(
                  'folder' => VALIDATE_STRING,
                  'quantity' => VALIDATE_INTEGER,
               )
            ),
         );
         
      }

      
      public function Execute( $folder = '', $quantity = 1 ) {
         
        //$folder = '/home/produksjon/man_ord/*';
        
        
        $folder = $_POST['folder'] . "/*";
        $folders = array();          
        $last = true;

        
        $filetypes = array( 'jpg', 'JPG', 'jpeg', 'JPEG', 'tif' );
        
        
        foreach ( glob( $folder ) as $res ){
           $last = true;
           
           $filecount = 0;
           foreach ( $filetypes as $type ){
               if (glob($res . "/*.$type") != false){
                  $filecount += count(glob($res . "/*.$type"));
               }
           }
           
           if( $filecount == 0 ){
            $last = false;
           }
           
           
           $folders[] = array(
               'filecount' => $filecount,
               'folder' => $res,
               'last' => $last
               ); 
        }

         
         $this->folders = $folders;
         $this->imagefolder = $last;
         $this->result = true;
         $this->message = 'OK';
         return true;
            
      }
      
   }

?>