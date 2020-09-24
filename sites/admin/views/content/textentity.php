<?PHP
   
   import( 'pages.admin' );
   import( 'math.uuid' );
   model( 'i18n.language' );
   
   library( 'fckeditor.fckeditor_php5' );
   
   class TextEntityEditor extends AdminPage implements IView {
      
      protected $objectclass = false;
      
      public function Execute( $id = null, $boolean = null ) {
         
         if( !class_exists( $this->objectclass ) ) return false;
         $this->classname = $this->objectclass;
         
         if( isset( $boolean ) ) {
            $this->$boolean = true;
         }
         
         if( isset( $id ) ) {

            $this->entityid = $id;
            return $this->editEntity( $id );
                        
         } else {
            
            $this->entitylist = $this->listEntities();
            
         }
         
      }
      
      protected function getEditorRoot() {
         
         return sprintf( '/content/%ss', strtolower( $this->objectclass ) );
         
      }
      
      public function Delete( $id ) {
         
         try {
            
            $this->deleteEntity( $id );
            
            relocate( $this->getEditorRoot() );
            
         } catch ( Exception $e ) {
            
            relocate( "%s/errormessage/%s", $this->getEditorRoot(), $e->getMessage() );
            
         }
         
      }
      
      protected function listEntities() {
         
         $this->setTemplate( 'content.textentity' );
         
         $entity['header'] = __( '%s list', $this->objectclass );
         $entity['addlink'] = sprintf( '%s/0', $this->getEditorRoot() );
         $entity['backlink'] = $this->getEditorRoot();
         
         $entities = array();
         $groups = array();
         
         $collection = new $this->objectclass();
         if( count( $collection ) ) foreach( $collection->collection( array( 'id' ), array( 'deleted' => null, 'siteid' => Session::get( 'adminsiteid', 1 ) ) )->fetchAllAs( $this->objectclass ) as $textentity ) {
         	
            if( !$textentity->id ) continue;
            
            $entities[] = array(
               'id'        => $textentity->id,
               'title'     => $textentity->title,
               'edit'      => sprintf( '%s/%d', $this->getEditorRoot(), $textentity->id ),
               'delete'    => sprintf( '%s/delete/%d', $this->getEditorRoot(), $textentity->id ),
               'type'      => $textentity->type ? __( ucfirst( $textentity->type ) ) : __( ucfirst( $this->objectclass ) ),
               'grouping'  => $textentity->grouping,
               'refid'  => $textentity->refid,
            );
            
            foreach( explode( ' ', $textentity->grouping ) as $group ) {
            	if( $group == '' ) continue;
               $groups[$group] = $group;
            }
            
         }
         
         if( !count( $entities ) ) {
            return array(
               'header' => __( 'No %ss', $this->objectclass ),
               'addlink'=> $entity['addlink'],
               'list'   => array(),
               'groups' => array(),
               'type'   => strtolower( $this->objectclass ),
               'backlink'=>$this->getEditorRoot(),
            );
         }
         
         // Do post-op sorting
         function sortentities( $a, $b ) {
            return strcasecmp( $a['title'], $b['title'] );
         }  usort( $entities, 'sortentities' );
         
         $groups[] = 'nogroup';
         
         $entity['list'] =    $entities;
         $entity['groups'] =  $groups;
         $entity['type'] =    strtolower( $this->objectclass );
         
         return $entity;
         
      }
      
      protected function editEntity( $id ) {
         
         $this->setTemplate( 'content.editor' );
         
         $object = new $this->objectclass( $id );
         
         if( isset( $_POST['save'] ) ) $this->saveEntity( $object, $_POST, $_FILES );
         
         $this->header = __( 'Editing %s', $object->title );
         
         $collection = new Language();
         foreach( $collection->collection( array( 'languageid' ), null, 'languageid' )->fetchAllAs( 'Language' ) as $language ) {

            if ($language->active) {
               $record = array(
                  'name'      => $language->elementname,
                  'segment'   => $language->code,
                  'country'   => $language->country,
                  'short'     => $language->short,
                  'title'     => $object->getTitle( $language->code ),
                  'shorttitle'=> $object->getShortTitle( $language->code, true ),
                  'urlname'   => $object->getUrlName( $language->code ),
                  'ingress'   => $object->getIngress( $language->code ),
                  'body'      => $object->getBody( $language->code ),
               );
               
               $languages[] = $record;
            
            }
         }
         
         // Elements to put at start of returned list.
         $alltemplates = array(
            array(
               'id' => '',
               'title' => '- ' . __( 'No template' ),
               'selected' => false,
               )
            );
      
            
         // load site specific CMS settings
         $siteid = Session::get( 'adminsiteid', 1 );
         $sites = Settings::Get( 'application', 'sites' );
         if( isset( $sites[$siteid]['short'] ) ) {
            try {
               config( '%s.cms', $sites[$siteid]['short'] );
            } catch( Exception $e ) {}
         }
         
         // Fetch list of all templates in db.
         $templates = Settings::get( 'cms', 'templates', array() );
         
         if( count( $templates['textentity'] ) > 0 )
         foreach( $templates['textentity'] as $template => $title ) {
            $alltemplates[] = array(
               'id' => $template,
               'title' => $title,
               'selected' => $template == $object->template ? true : false,
            );
         }
         
         $common = array(
            "attachments" => $object->getAttachments(),
            'identifier'=> $object->identifier,
            'validfrom' => $object->validfrom,
            'validto'   => $object->validto,
            'backlink'  => $this->getEditorRoot(),
            'templates' => $alltemplates,
            'grouping'  => $object->grouping,
            'customcss' => $object->customcss,
            'customjs' => $object->customjs,
            'comment' => $object->comment,
         );
         
         $this->common = $this->editFields( $common, $object );
         
         $this->editForms( $object );
         
         $this->languages = $languages;
         
         $published = array();
         foreach( DB::query( 'SELECT menuid FROM site_menu_contents WHERE textentityid = ?', $id )->fetchAll() as $row ) {
            list( $menuid ) = $row;
            $menu = new MenuItem( $menuid );
            $published[] = array(
               'id' => $menuid,
               'selected' => $menuid == $object->defaultmenuid,
               'url' => $menu->getTranslatedUrl(i18n::languageCode()),
            );
         }
         
         $this->published = $published;
         
         return true;
         
      }
      
      protected function editFields( Array $record, DBTextEntity $object ) {
         
         return $record;
         
      }
      
      protected function editForms( DBTextEntity $object ) {
         
      }
      
      protected function saveEntity( $object, $post, $files ) {
         
         // make sure it has a valid objectid before setting vars
         if( !$object->isLoaded() ) $object->save();
         
         // Normal values
         foreach( $post['save'] as $language => $languagevalues ) {
            
            foreach( $languagevalues as $key => $value ) {
               
               switch( $key ) {
                  case 'title':
                     $object->setTitle( $value, $language );
                     break;
                  case 'shorttitle':
                     $object->setShortTitle( $value, $language );
                     break;
                  case 'urlname':
                     $object->setUrlName( ( $value ? $value : $languagevalues['title'] ), $language );
                     break;
                  case 'body':
                     $object->setBody( $value, $language );
                     break;
                  case 'ingress':
                     $object->setIngress( $value, $language );
                     break;
                  default:
                     $object->$key = $value;
                     break;
                  
               }
               
            }
            
            // create a fresh revision
            $object->createRevision( $language );
            
         }
         
         // save the object
         $object->save();
         
         // relocate back
         relocate( '%s/%d', $this->getEditorRoot(), $object->id );
         
         // return successful
         return true;
         
      }
      
      protected function deleteEntity( $id ) {
         
         $object = new $this->objectclass( $id );
         return $object->delete();
         
      }
	  
	  public function revision( $id, $revisionid ){
         $this->template = null;
         Util::Debug( $id );
         Util::Debug( $revisionid );
		 
		 $revision = DB::query( 'SELECT * FROM site_textentity_revisions WHERE textrevisionid = ?',  $revisionid )->fetchAll( DB::FETCH_ASSOC );

		 $body = $revision[0]['body'];
		 $languageid = $revision[0]['languageid'];
		 
		 DB::query( "UPDATE site_textentity_content
				   SET body = ? WHERE textentityid = ? and languageid = ?",
				   $body,
				   $id,
				   $languageid
				   );
		 
		 CacheEngine::write( sprintf( 'textentity-body-%d-%d', $id, $languageid ), $body );
		 
         relocate( '/content/articles/' . $id );
         
      }
      
      
   }
   
?>
