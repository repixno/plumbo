<?PHP
/*
   CREATE TABLE site_card_category_cewe (
        id serial NOT NULL,
        articleid integer,
        grouping text,
        catid integer,
        link text,
        title text,
        hit int,
        sort int,
        thumbnail text,
        visible boolean,
        created timestamp without time zone,
    );
*/

    import( 'pages.admin' );
    model( 'site.cardcategorycewe' );
    
     class CardTemplateListCewe extends AdminPage implements IView {
        
        protected $template = 'content.cardtemplatecewe';
        private $thumbfolder = '/data/pd/ef28/cms/takkkort/cewethumbs/';
        
        public function Execute( $catid = null){

                if( $catid && !is_numeric( $catid )){
                    relocate( "/content/cardtemplatecewe/" );
                }
                $tempcat = DB::query( " SELECT * from site_menu where parentid = 457;" )->fetchAll( DB::FETCH_ASSOC );            
                $catarray = array();
                
                foreach( $tempcat as $cat ){
                    
                    $catarray[] = $cat['title'];
                    
                }
                $this->catarray = $tempcat;
                
                
                if( $catid != null ){
                    
                    $category  = DB::query( "SELECT title from site_menu where id = ?;", $catid )->fetchSingle();
                    $this->selectedcat = $category;
                    $cards = DB::query( "SELECT * FROM site_card_category_cewe WHERE catid = ? AND visible = 't'" , $catid )->fetchAll( DB::FETCH_ASSOC );
                    $this->cards  = $cards;
                    $this->selectedcatid = $catid;
                    
                }
         
        }
        
        
        public function Create($catid = null){
            if( $catid == null ){
                relocate( "/content/cardtemplatecewe" );
            }
            
            if( $_POST ){
                
                //Util::Debug( $_FILES );
                //Util::Debug( $_POST );
                
                
                $tmp_name = $_FILES["thumbnail"]["tmp_name"];
                $name = urlencode(  $_FILES["thumbnail"]["name"] );
                
                
                //Util::Debug( $this->thumbfolder . $name );
                
                move_uploaded_file($tmp_name,  $this->thumbfolder . $name);
    
                $newtheme = new DBCardcatergoryCewe();
                
                $newtheme->articleid = $_POST['articleid'];
                $newtheme->grouping = $_POST['grouping'];
                $newtheme->catid = $catid;
                $newtheme->link = $_POST['link']; 
                $newtheme->thumbnail = $name;
                $newtheme->created = date( 'Y-m-d H:i:s' );
                $newtheme->title = $_POST['title'];
                $newtheme->title_sv = $_POST['title_sv'];
                $newtheme->hit = 0;
                
                $newtheme->save();
                
                //Util::Debug( $newtheme );
                
                relocate( "/content/cardtemplatecewe/" . $catid  );
                die();
            }else{
                $this->template = 'content.createtemplatecewe';
                $this->selectedcat  = DB::query( "SELECT title from site_menu where id = ?;", $catid )->fetchSingle();

            }
        }
        
        
        public function Delete( $id, $catid ){
         
            $template = new DBCardcatergorycewe($id);
            $template->visible = false;
            $template->save();
            relocate( "/content/cardtemplatecewe/" . $catid  );
        }
        
        public function Edit( $id ){
            
            $this->template = 'content.edittemplatecewe';
            
            if( !$id ){
                relocate( "/content/cardtemplatecewe/" );
            }
            
            $template = new DBCardcatergorycewe($id);
            
            if( $_POST ){

                
                if( $_FILES['thumbnail']['name'] && $_FILES['thumbnail']['name'] == 0 ){
                    $tmp_name = $_FILES["thumbnail"]["tmp_name"];
                    $name = urlencode(  $_FILES["thumbnail"]["name"] );
                    move_uploaded_file($tmp_name,  $this->thumbfolder . $name);
                    $template->thumbnail = $name;
                    
                }
                
                $template->articleid = $_POST['articleid'];
                $template->grouping = $_POST['grouping'];
                $template->link = $_POST['link']; 
                $template->title = $_POST['title'];
                $template->title_sv = $_POST['title_sv'];
                
                $template->save();
                
                relocate( "/content/cardtemplatecewe/" . $template->catid  );
                
            }else{
                $edittemplate = array(
                    'id' => $template->id,
                    'articleid' => $template->articleid,
                    'grouping' => $template->grouping,
                    'catid' => $template->catid,
                    'link' => $template->link,
                    'sort' => $template->sort,
                    'thumbnail' => $template->thumbnail,
                    'visible' => $template->visible,
                    'created' => $template->created,
                    'title' => $template->title,
                    'title_sv' => $template->title_sv,
                );
                
                
                $this->edittemplate = $edittemplate;
            }
            
        }
        
        
     }



?>