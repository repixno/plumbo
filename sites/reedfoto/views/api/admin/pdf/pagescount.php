<?PHP

   import( 'reedfoto.pages.json' );
   
   class ReedFotoApiAdminPdfPagesCount extends JSONPage implements IValidatedView  {
      
      /**
       * Validates the data going into this API
       *
       * @return array of validation rules.
       */
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'filehash' => VALIDATE_STRING
               ),
               'fields' => array(
                  'filehash' => VALIDATE_STRING
               ),
            )
         );
         
      }
      
      /**
       * Count pages in PDF file
       *
       * @api-name admin.pdf.pagescount
       * @api-javascript yes
       * @api-post-optional filehash String Hash of the PDF-file to return pagecount on 
       * @api-param-optional filehash String Hash of the PDF-file to return pagecount on 
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */           
      public function Execute( $filehash = '' ) {
         
         $filehash = $_POST['filehash'] ? $_POST['filehash'] : $filehash;
          
         $storagepath = sprintf( '%s/%s', getRootPath(), Settings::Get( 'reedfoto', 'storageroot', 'data/reedfoto/corrections' ) );
         $uploadfolder = Settings::Get( 'reedfoto', 'uploadfolder', 'upload' );
         
         try {
            
            if ( $filepointer = @fopen( sprintf( '%s/%s/%s.pdf', $storagepath, $uploadfolder, basename ( $filehash ) ), 'r' ) ) {
               
               $pages = 0;
               
               while( !feof( $filepointer ) ) {
                      $line = fgets( $filepointer, 255 );
                      
                      if ( preg_match( '/\/Count [0-9]+/' , $line, $countlines ) ) {
                         
                         preg_match( '/[0-9]+/',$countlines[0], $numbers);
                         
                         if ( $pages < $numbers[0] ) $pages = $numbers[0];
                      }
               }
               
               fclose( $filepointer );
               
               $this->result = true;
               $this->pages = $pages;
               $this->message = "OK";
               
            } else {
               $this->result = false;
               
               $this->message = 'Cannot read file';
               
            }
            
         } catch (Exception $e) {
            
            $this->message = $e;
            $this->result = false;
            
         }

      }
   }
   
?>