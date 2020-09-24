<?PHP
   import( 'pages.admin' ); 
   import( 'website.photocompetition.index');
   import( 'website.photocompetition.image' );
   import( 'website.album');
   
   
    /**
    * PhotoCompetition Admin
    * Admintool for editing and creating PhotoCompetition
    *
    * @author Tor Inge Løvland <tor.inge@eurofoto.no>
    */
   
   
   class PhotoCompetitionViewer extends UserPage implements IView {
      

      protected $template = 'partner.competitions.photo';
      
      public function Execute($urlname = '') {
         
         $this->header = "Eurofoto Competition Admin";
         
         $competition = PhotoCompetition::fromUrlName( $urlname );
         if( !$competition->isLoaded() || $competition->userid != $_SESSION['uid'] ) {
            
            relocate( WebsiteHelper::rootBaseUrl() );
            die();
            
         } else{
            
            $this->competition = $competition->asArray();
            $entries = array();
            
            $competition_images = new PhotoCompetitionImage();
            foreach( $competition_images->collection( array( 'bid' ), array( 'photocompetitionid' => $competition->id ), 'bid ASC' )->fetchAll() as $row ) {
               
               try {
                  
                  list( $bid ) = $row;
                  
                  $image = new PhotoCompetitionImage( $bid ); 
                  $imageinfo = new Image($bid);
                  
                  $entries[] = array( 
           		      'image'       => $imageinfo->asArray(),
           		      'title'       => $image->title,
           		      'description' => $image->description,
           		      'approved'    => $image->approved,
           		      'fielddata'   => unserialize($image->fielddata),
                  );
                  
               } catch( Exception $e ) {
                  
               }
               
            }

            $this->entries = $entries;
            $this->urlname = $competition->urlname;
            
         }
         
      }
      
      public function edit(){
         $this->template = '';
         
         if( isset( $_POST['submit_data'] ) ) {
            $type = $_POST['submit_data'] ;
            
            if(strtolower($type) == 'revoke'){
                $imageids = $_POST['imageids_approved'];
                foreach ($imageids as $imageid){ 
                  $image = new PhotoCompetitionImage( $imageid );
                  $competition = new PhotoCompetition( $image->photocompetitionid );
                  
                  $image->approved = null;
                  $image->aid = $competition->uploadaid;
                  $image->save();
                }
                  
                  relocate('/partner/competitions/photo/mammanett');
            
            }
            else if (strtolower($type) == 'approve'){
               
               $imageids = $_POST['imageids_pending'];
               foreach ($imageids as $imageid){  
                  $image = new PhotoCompetitionImage( $imageid );
                  $competition = new PhotoCompetition( $image->photocompetitionid );
                  
                  $image->approved = 1;
                  $image->aid = $competition->approvedaid;
                  $image->save();
                  
                  relocate('/partner/competitions/photo/mammanett');

               }
            
            }
         
         
         }
         else{
            relocate('/partner/competitions/photo/mammanett');
         }
      }
   }
   
?>