<?PHP

    /***********************************************
     *For resetting mediaclip projects to a
     *previous state
     *@author Tor Inge Lovland <tor.inge@eurofoto.no>
     ************************************************/
    
   import( 'pages.admin' );
   import( 'core.version' );
   
   class DatabaseExport extends AdminPage implements IView {
      
      protected $template = 'project.version';
      
      public function Execute() {
            if( $_GET['projectid'] ){
                
                
                $projectid = $_GET['projectid'];
                
                $projects = DB::query( "SELECT id, date_saved, project_id FROM mediaclip_versions where project_id = ? order by date_saved", $projectid )->fetchAll( DB::FETCH_ASSOC );
                $this->projects = $projects;
                $this->check = base64_encode( date('Y-m-d H:i:s' ) );
            }
            
            
         
      }
      
      public function Recover( $projectid, $versionid, $check ){
        
        $this->template = null;
        $checkdate = base64_decode( $check );
        $checkdate = date( 'Y-m-d H:i:s' , strtotime( $checkdate  . '+10 minute' ) );
        $actualdate = date( 'Y-m-d H:i:s');
        
        if(  $checkdate > $actualdate ){
            
            if( is_numeric( $projectid ) && is_numeric( $versionid ) ){
            
                DB::query( "UPDATE mediaclip_projects SET project_xml = ( SELECT project_xml FROM mediaclip_versions WHERE id = ? )  WHERE id = ?", $versionid , $projectid );
                
                Util::Debug( "Prosjektet er oppdatert");
            }
            
        }else{
            
            Util::Debug( "Forespørselen er ikkje lenger gyldig, prøv på nytt" );
            
        }

        echo '<a href="/project/version">Gå tilbake</a>';
        
        
      }
      
   }
   
?>