<?php


   import( 'website.user' );
   import( 'website.album' );
   
   import( 'pages.protected' );
   
   class AccountUpload extends ProtectedPage implements IView {
      
      protected $template = 'account.upload.index';
      
            
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
               ),
               'post' => array(
                  'albumid' => VALIDATE_INTEGER,
               )
            )
         );
         
		}
		
		/**
		 * Execute
		 * 
		 * adds albumid, selectedalbumid and albums to view
		 *
		 * @param Integer $uploadaid
		 */

      public function Execute( $albumid = 0 ) {
          
         $albumid = $_POST['albumid'] ? $_POST['albumid'] : $albumid;
         
         $redirecturl = '';
         
         if ( $albumid <= 0 ) {
            
            if ( $_SESSION['upload_aid'] > 0 ) {
               
               // change upload_aid to albumid
               
               $albumid = $_SESSION['upload_aid'];
               
            } else {
               
               if( Login::isLoggedIn() ) {
               
                  $redirecturl = '/account/albums/inbox/';
                  
               }
               
            }
               
         }
                        
         try {
            
            $album = new Album( $albumid );
            
            if( $album->uid == Login::userid() ) {
               
               // change upload_aid to albumid
               
               $_SESSION['upload_aid'] = $album->aid;
               
               $redirecturl = $album->albumurl;
               
            }
            
         } catch( Exception $e ) { }
         
         $uploadreturnurl = Session::pipe( 'uploadreturnurl', null, false, true );
         
         if( $uploadreturnurl ) {
            
            $redirecturl = $uploadreturnurl;
            
         }
         
         if( $redirecturl ) {
            
            $this->redirecturl = $redirecturl;
            
         }
         
         $this->albumid = $albumid;
         $this->albums = Album::enum( 0, 0, false, false, true );
         
         // change upload_aid to albumid
         
         $this->selectedalbumid = (int) $_SESSION['upload_aid'];
         
      }        
      
   }
   
?>