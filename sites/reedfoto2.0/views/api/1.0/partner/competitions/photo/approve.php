<?PHP
   
   import( 'pages.json' );
   import( 'website.photocompetition.image' );
   import( 'website.photocompetition.index' );
   
   class PhotoCompetitionApproveAPI extends JSONPage implements IValidatedView {
      
      public function Validate() {
         
         return array(
            'execute' => array(
               'post' => array(
                  'imageid' => VALIDATE_INTEGER,
               ),
               'params' => array(
                  'imageid' => VALIDATE_INTEGER,
               ),
            ),
         );
         
      }
      
      public function Execute( $imageid = 0 ) {
         
         if( isset( $_POST['imageid'] ) ) {
            $imageid = $_POST['imageid'];
         }
         
         $image = new PhotoCompetitionImage( $imageid );
         $competition = new PhotoCompetition( $image->photocompetitionid );
         
         $image->approved = 1;
         $image->aid = $competition->approvedaid;
         $image->save();
         
         $this->result = true;
         
      }
      
   }
   
?>