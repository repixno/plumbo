<?PHP
/*
   CREATE TABLE site_card_category (
        id serial NOT NULL,
        articleid integer,
        grouping text,
        catid integer,
        template text,
        hit int,
        sort int,
        thumbnail text,
        visible boolean,
        created timestamp without time zone,
        title text,
    );
*/

//sudo mount -t cifs //marge.eurofoto.no/tanja /mnt/marge -o username=Administrator,password=system,rw,iocharset=utf8,file_mode=0755,dir_mode=0755
   import( 'pages.admin' );
   model( 'site.cardcategory' );
   
   class CardTemplateList extends AdminPage implements IView {
        protected $template = 'content.cardtemplate';
        private $folder = "/mnt/marge/ECommerceBridge/Library/GreetingCard/Themes/";
        
        
        protected $themecatarray = array(
                                         939 => array ( "10x18_Landscape", "10x18_Portrait" ),
                                         7105 => array( "Folded_15x15" ),
                                         7237 => array( "Collage_10x15_portrait" ),
                                         940 => array( "15x15" ),
                                         7233 => array( "Folded_10x15_portrait"),
                                         7234 => array( "Folded_10x21_Landscape"),
                                         7136 => array( "Greetingcard XXL" ),
                                         7238 => array( "Collage_10x21_landscape" ),
                                         7239 => array( "Postcard_10x15"),
                                         7240 => array( "Postcard_10x21" ),
                                          );
        
        public function Execute( $catid = null){

                $tempcat = DB::query( " SELECT * from site_menu where parentid = 457;" )->fetchAll( DB::FETCH_ASSOC );            
                $catarray = array();
                
                foreach( $tempcat as $cat ){
                    
                    $catarray[] = $cat['title'];
                    
                }
                $this->catarray = $tempcat;
                
                
                if( $catid != null ){
                    
                    $category  = DB::query( "SELECT title from site_menu where id = ?;", $catid )->fetchSingle();
                    $this->selectedcat = $category;
                    $cards = DB::query( "SELECT * FROM site_card_category WHERE catid = ? AND visible = 't' order by sort" , $catid )->fetchAll( DB::FETCH_ASSOC );
                    $this->cards  = $cards;
                    $this->selectedcatid = $catid;
                    
                }
         
        }
        
        
     public function Create($catid = null){
          if( $catid == null ){
              relocate( "/content/cardtemplate" );
          }
          if( $_POST ){
              Util::Debug( $_POST );
              
              $newtheme = new DBCardcatergory();
              
              $template = $_POST['themecategory'] . "/" . $_POST['themesub'] . "/" . $_POST['template'];
              $newtheme->articleid = $_POST['articleid'];
              $newtheme->grouping = $_POST['grouping'];
              $newtheme->catid = $catid;
              $newtheme->template = $template; 
              $newtheme->thumbnail = $template . "/previews/0.jpg";
              $newtheme->created = date( 'Y-m-d H:i:s' );
              $newtheme->title = $_POST['title'];
              
               $newtheme->hit = 0;
               $newtheme->sort = 0;
              
              $newtheme->save();
              
              relocate( "/content/cardtemplate/" . $catid  );
              die();
          }else{
              
              $this->template = 'content.createtemplate';
              $this->catid = $catid;
              $this->selectedcat  = DB::query( "SELECT title from site_menu where id = ?;", $catid )->fetchSingle();
          }
     }
        
     public function delete( $id ,$catid ){
          
          $template = new DBCardcatergory($id);
          $template->visible = false;
          $template->save();
          relocate( "/content/cardtemplate/" . $catid  );
            
     }
        
     public function Edit($id){
          
          $this->template = null;
          
          $template = new DBCardcatergory($id);
          
          if( $_POST ){
               Util::Debug( $_POST );
               
               $templatename = $_POST['themecategory'] . "/" . $_POST['themesub'] . "/" . $_POST['template'];
               $template->articleid = $_POST['articleid'];
               $template->grouping = $_POST['grouping'];
               //$newtheme->catid = $catid;
               $template->template = $templatename; 
               $template->thumbnail = $templatename . "/previews/0.jpg";
               $template->created = date( 'Y-m-d H:i:s' );
               $template->title = $_POST['title'];
              
               $template->save();
              
               relocate( "/content/cardtemplate/" . $template->catid  );
               
          }else{
               
               $this->template = 'content.edittemplate';
               Util::Debug( $template );
               
               $edittemplate = array(
                    'id' => $template->id,
                    'articleid' => $template->articleid,
                    'grouping' => $template->grouping,
                    'catid' => $template->catid,
                    'template' => $template->template,
                    'sort' => $template->sort,
                    'thumbnail' => $template->thumbnail,
                    'visible' => $template->visible,
                    'created' => $template->created,
                    'title' => $template->title,
               );
               
               Util::Debug( $edittemplate );
               
               
               $this->edittemplate = $edittemplate;
               
          } 
     }
        
        
        
        public function GetThemecat( $articleid ){
            $this->template = null;
            echo   json_encode( $this->themecatarray[$articleid] );
            die();
        }
        
        public function GetTheme(){
            $this->template = null;
            
            $themecategory= $_POST['themecategory'];
            $themesub = $_POST['themesub'];
            $catid =  $_POST['catid'];
            /*
            $server = "http://marge.eurofoto.no/";
            $library = "ECommerceBridge/Library/";
            $type = "GreetingCard/Themes/";*/
            
            $themes =  sprintf( "%s%s/%s" , $this->folder ,$themecategory, $themesub ) ;
            
            $dirarray = array();
            foreach ( glob(  $themes . '/*') as $dir){
                if( is_dir($dir) ){
                    
                    $template = sprintf( "%s/%s/%s" , $themecategory , $themesub , basename( $dir ) );
                    if( !DB::query( "SELECT id FROM site_card_category WHERE catid = ? AND template = ?", $catid, $template)->fetchSingle() ){
                        $dirarray[] = array(
                          "theme" => basename( $dir ),
                          "used" => "false"
                        );
                    }else{
                         $dirarray[] = array(
                          "theme" => basename( $dir ),
                          "used" => "true"
                        );
                    }

                    
                    
                }
            }
            
            echo json_encode( $dirarray );

            
        }
        
        
        public function GetSubCategories(){
            $this->template = null;
            
            $themecategory= $_POST['themecategory'];
            
            $themecat = $this->folder. $themecategory;
            
            $dirarray = array();
            foreach ( glob(  $themecat . '/*') as $dir){
                
                if( is_dir($dir) ){
                    $dirarray[] = basename( $dir );
                }

            }
            
            echo json_encode( $dirarray );
            
        }
        
        
        public function Sorting(){
          
          $this->template = null;
          
          $cardarray = $_POST['cardarray'];
          $i = 0;
          foreach( $cardarray as $card ){
               $i++;
               $sort = new DBCardcatergory($card);
               $sort->sort = $i;
               $sort->save();
               
               util::debug( $sort );
          }
          
          
          
          echo json_encode( "OK" );
        }
      
   }
   
?>
