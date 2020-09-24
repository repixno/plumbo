<?php


   import( 'pages.json' );

   
   class APIFolderGet extends JSONPage implements NoAuthRequired,IView {
      
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

        
        foreach ( glob( $folder ) as $res ){
           $last = true;
           if (glob($res . "/*.jpg") != false){
               $filecount = count(glob($res . "/*.jpg"));
           }
           else if (glob($res . "/*.jpeg") != false){
               $filecount += count(glob($res . "/*.jpeg"));
               
           }else{
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