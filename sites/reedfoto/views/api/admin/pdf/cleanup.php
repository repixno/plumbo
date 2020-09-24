<?PHP

   import( 'reedfoto.pages.json' );
   import('math.uuid');
   
   class ReedFotoApiAdminPdfExtract extends JSONPage implements IValidatedView  {
      
      /**
       * Validates the data going into this API
       *
       * @return array of validation rules.
       */
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'filehash' => VALIDATE_STRING,
               ),
               'fields' => array(
                  'filehash' => VALIDATE_STRING,
               ),
            )
         );
         
      }
      
      /**
       * Cleanup after PDF extract process
       *
       * @api-name admin.pdf.cleanup
       * @api-javascript yes
       * @api-post-optional filehash String Hash of the PDF-file to extract page from
       * @api-param-optional filehash String Hash of the PDF-file to extract page from
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */      
      public function Execute( $filehash = '' ) {
         try {
            $filehash = $_POST['filehash'] ? $_POST['filehash'] : $filehash;
            
            $storagepath = sprintf( '%s/%s', getRootPath(), Settings::Get( 'reedfoto', 'storageroot', 'data/reedfoto/corrections' ) );
            $uploadfolder = Settings::Get( 'reedfoto', 'uploadfolder', 'upload' );
            
            try {
               unlink( sprintf( '%s/%s/%s.pdf', $storagepath, $uploadfolder, basename( $filehash ) ) );
            } catch ( Exception $f ) {
               
            }

            try {
               unlink( sprintf( '%s/%s/%s.txt', $storagepath, $uploadfolder, basename( $filehash ) ) );
            } catch ( Exception $f ) {
               
            }
            
            $this->result = true;
            $this->message = 'OK';
            
         } catch ( Exception $e ) {
            
            $this->result = false;
            $this->message = sprintf( 'Exception: %s', $e );
            
         }
      }
   }
   
?>