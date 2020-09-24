<?PHP
   import( 'pages.admin' ); 
   import('website.photocompetition.index');
   
   
    /**
    * PhotoCompetition Admin
    * Admintool for editing and creating PhotoCompetition
    *
    * @author Tor Inge Løvland <tor.inge@eurofoto.no>
    */
   
   
   class PhotoCompetitionViewer extends AdminPage implements IView {
      
      protected $template = 'competitions.photo.default';
       
      public function Execute() {
         
         $this->header = "Eurofoto Competition Admin";
         
         $competition = new PhotoCompetition();
         
         $entries = array();
     		foreach( $competition->collection( null, null, 'id ASC' )->fetchAllAs('PhotoCompetition') as $competitiondata ) {
            
     		   $entries[] = array( 
     		      'id'    => $competitiondata->id,
     		      'title' => $competitiondata->title,
     		      'urlname' => $competitiondata->urlname,
            );
            
         }
         
         $this->entries = $entries;
         
      }
      
      public function Edit( $photocompetitionid = Model::CREATE ) {
         
         $this->setTemplate( 'competitions.photo.edit' );
         
         $competition = new PhotoCompetition( $photocompetitionid );
         
         if( isset( $_POST['competition'] ) ) {
            foreach( $_POST['competition'] as $key => $value ) {
               $competition->$key = $value;
            }
            $competition->save();
         }
         
         $this->competitiondata = $competition->asArray();
         
      }
      
   }
   
?>