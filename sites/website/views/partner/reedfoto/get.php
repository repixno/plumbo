<?php


   import( 'pages.json' );

   //sudo mount -t cifs //zetta.eurofoto.no/reedfoto /home/reedfoto -o username=produksjon,password=produksjon,gid=16,uid=16,rw,iocharset=utf8,file_mode=0777,dir_mode=0777
   
   
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
         
         $folder = $_POST['folder'] . "/*";
         if(  empty( $folder )  ){
            $folder = '/home/reedfoto/man_ord/man_ord/*';
         }

        
         
        $folders = array();          
        $last = true;

        $filetypes = array( 'jpg', 'JPG', 'jpeg', 'JPEG', 'tif' );

        foreach ( glob( $folder ) as $res ){
            $last = true;
            $pathinfo = pathinfo( $res );
            $basename = $pathinfo['basename'];
            $dirname = $pathinfo['dirname'];
            $sanitized_folder  = preg_replace('/[^0-9a-z\.\_\-]/i','_', $basename );
            if ($res != $sanitized_folder) {
               $command = sprintf( 'mv "%s/%s" "%s/%s"' , $dirname , $basename ,  $dirname ,  $sanitized_folder );
               system(  $command );
               $res = $dirname . '/'.   $sanitized_folder;
            }
            
            $filecount = 0;
            foreach ( $filetypes as $type ){
               if (glob($res . "/*.$type") != false){

                  foreach( glob($res . "/*.$type") as $imagefile ){
                   
                     $imagefilename = basename( $imagefile );   
                     $sanitized_name = preg_replace('/[^0-9a-z\.\_\-]/i','_',$imagefilename);
                     if ($sanitized_name == $imagefilename) {
                     } else {
                        $command = sprintf( 'mv "%s/%s" "%s/%s"' ,$res, $imagefilename, $res ,  $sanitized_name );
                        system(  $command );
                     }  
                     $filecount++;
                     
                  }
                  
                  
                  //$filecount += count(glob($res . "/*.$type"));
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