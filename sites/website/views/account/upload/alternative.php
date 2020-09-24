<?PHP
   
   import( 'website.album' );
   import( 'website.uploadhelper' );
   
   import( 'pages.protected' );
   
   class AccountUploadAlternative extends ProtectedPage implements IValidatedView {
      
      protected $template = 'account.upload.alternative';
            
      /**
       * Validator
       *
       * @return array array of fields
       * 
       */

		public function Validate() {

         return array(
            'execute' => array( 
               'fields' => array(
                  'albumid' => VALIDATE_INTEGER,
               )
            )
         );
         
		}
		
		/**
		 * Execute
		 * 
		 * adds selectedalbumid, batchid and albums to view
		 *
		 * @param Integer $albumid
		 */
      
      public function Execute( $albumid = 0 ) {
         
         if( $albumid > 0 ) {
            
            try {
               
               $album = new Album( $albumid );
               $this->selectedalbumid = $albumid;
               $this->selectedalbum = $album->asArray();
               
            } catch( Exception $e ) {
               
               $this->selectedalbumid = 0; 
               
            }
            
         } else {
            
            $this->selectedalbumid = 0;
            
         }
         
         $this->batchid = UploadHelper::getBatchId();
         $this->albums = Album::enum( 0, 0, false, false, true );
         
      }

   }
?>