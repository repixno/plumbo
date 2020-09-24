<?PHP
   
   import( 'core.model' );
   import( 'core.util' );
   import( 'math.uuid' );
   
   model( 'site.textentityrevision' );
   
   class DBTextEntity extends Model implements ModelCaching {
      
      static $table = 'textentity';
      
      static $basename = 'site';
      
      static $fields = array(
         'id' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'type' => array( 
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'siteid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 1,
         ),
         'template'   => array( 
            'type'    => DB_TYPE_STRING,
            'size'    => 36,
            'default' => '',
         ),
         'identifier' => array( 
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'showtitle'  => array(
            'type'    => DB_TYPE_ENUM,
            'default' => true,
         ),
         'defaultmenuid'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'validfrom'  => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'validto'  => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'created'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'createdby'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'updated'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'updatedby'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'deleted'    => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'deletedby'  => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'grouping'   => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
          'customcss' => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ),
          'customjs' => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ),
          'comment' => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ),
         'attachments' => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'null'      => true,
            'default'   => null,
         ), 
         
      );
      
      public function __postSetup() {
         
         // setup some defaults for new objects
         if( $this->id == Model::CREATE ) {
            $this->createdby = Login::userid();
            $this->created = date( 'Y-m-d H:i:s' );
            $this->type = strtolower( get_class( $this ) );
            $this->identifier = UUID::create();
            
            $siteid = Session::get( 'adminsiteid', 0 );
            if( !$siteid ) $siteid = Session::get( 'siteid', 1 );
            $this->siteid = $siteid;
         }
         
         if( !$this->template ) {
            // has this object type a default template set?
            $type = strtolower( get_class( $this ) );
            $templates = Settings::get( 'cms', 'templates', array() );
            if( isset( $templates['textentity_defaults'][$type] ) ) {
               $this->template = $templates['textentity_defaults'][$type];
            }
         }
         
         // run the parent setup
         return parent::__postSetup();
         
      }
      
      public function delete() {
         
         // drop this article/product/whatever from the menu link table
         DB::query( 'DELETE FROM site_menu_contents WHERE textentityid = ?', $this->id );
         
         // mark the item as deleted
         $this->deleted = date( 'Y-m-d H:i:s' );
         $this->deletedby = login::userid();
         $this->save();
         
      }
      
      public function save() {
         
         $this->updated = date( 'Y-m-d H:i:s' );
         $this->updatedby = login::userid();
         
         return parent::save();
         
      }
      
      public function createRevision( $language = false ) {
         
         if( !$this->id ) return false;
         
         $languageid = (int) i18n::getLanguageId( $language );
         if( !$languageid ) return false;
         
         $revision = new DBTextEntityRevision();
         $revision->textentityid = $this->id;
         $revision->languageid = $languageid;
         $revision->title = $this->getTitle( $language );
         $revision->ingress = $this->getIngress( $language );
         $revision->body = $this->getBody( $language );
         $revision->urlname = $this->getUrlName( $language );
         $revision->save();
         
         return true;
         
      }
      
      public function getTitle( $language = false ) {
         
         if( !$this->id ) return '';
         
         $languageid = i18n::getLanguageId( $language );
         if( !$languageid ) return '';
         
         $title = CacheEngine::read( sprintf( 'textentity-title-%d-%d', $this->id, $languageid ) );
         if( !is_null( $title ) ) return $title;
         
         $title = DB::query( "SELECT title FROM site_textentity_content WHERE textentityid = ? AND languageid = ?", $this->id, $languageid )->fetchSingle();
         
         CacheEngine::write( sprintf( 'textentity-title-%d-%d', $this->id, $languageid ), $title );
         
         return empty( $title ) ? '' : $title;
         
      }
      
      public function setTitle( $title, $language = false ) {
         
         if( !$this->id ) return false;
         $languageid = i18n::getLanguageId( $language );
         if( !DB::query( "UPDATE site_textentity_content SET title = ?, updatedby = ?, updated = NOW() WHERE textentityid = ? AND languageid = ?", $title, Login::userid(), $this->id, $languageid )->count() ) {
            DB::query( "INSERT INTO site_textentity_content (textentityid, languageid, title, updatedby, updated ) VALUES (?, ?, ?, ?, NOW())", $this->id, $languageid, $title, Login::userid() );
         }
         
         CacheEngine::write( sprintf( 'textentity-title-%d-%d', $this->id, $languageid ), $title );
         
         if( !$this->getUrlName( $language ) ) {
            $this->setURLName( $title, $language );
         }
         return true;
         
      }
      
      public function getShortTitle( $language = false, $returnblank = false ) {
         
         if( !$this->id ) return '';
         
         $languageid = i18n::getLanguageId( $language );
         if( !$languageid ) return '';
         
         $shorttitle = CacheEngine::read( sprintf( 'textentity-shorttitle-%d-%d', $this->id, $languageid ) );
         if( !is_null( $shorttitle ) ) return $shorttitle;
         
         $shorttitle = DB::query( "SELECT shorttitle FROM site_textentity_content WHERE textentityid = ? AND languageid = ?", $this->id, $languageid )->fetchSingle();
         
         CacheEngine::write( sprintf( 'textentity-shorttitle-%d-%d', $this->id, $languageid ), $shorttitle );
         
         return empty( $shorttitle ) ? ( $returnblank ? '' : $this->getTitle( $language ) ) : $shorttitle;
         
      }
      
      public function setShortTitle( $shorttitle, $language = false ) {
         
         if( !$this->id ) return false;
         $languageid = i18n::getLanguageId( $language );
         if( !DB::query( "UPDATE site_textentity_content SET shorttitle = ?, updatedby = ?, updated = NOW() WHERE textentityid = ? AND languageid = ?", $shorttitle, Login::userid(), $this->id, $languageid )->count() ) {
            DB::query( "INSERT INTO site_textentity_content (textentityid, languageid, shorttitle, updatedby, updated ) VALUES (?, ?, ?, ?, NOW())", $this->id, $languageid, $shorttitle, Login::userid() );
         }
         
         CacheEngine::write( sprintf( 'textentity-shorttitle-%d-%d', $this->id, $languageid ), $shorttitle );
         
         return true;
         
      }
      
      public function getUrlName( $language = false ) {
         
         if( !$this->id ) return '';
         
         $languageid = i18n::getLanguageId( $language );
         if( !$languageid ) return '';
         
         $urlname = CacheEngine::read( sprintf( 'textentity-urlname-%d-%d', $this->id, $languageid ) );
         if( !is_null( $urlname ) ) return $urlname;
         
         $urlname = DB::query( "SELECT urlname FROM site_textentity_content WHERE textentityid = ? AND languageid = ?", $this->id, $languageid )->fetchSingle();
         
         CacheEngine::write( sprintf( 'textentity-urlname-%d-%d', $this->id, $languageid ), $urlname );
         
         return empty( $urlname ) ? '' : $urlname;
         
      }
      
      public function setUrlName( $urlname, $language = false ) {
         
         if( !$this->id ) return false;
         
         $urlname = Util::urlize( $urlname );
         
         $languageid = i18n::getLanguageId( $language );
         if( !DB::query( "UPDATE site_textentity_content SET urlname = ?, updatedby = ?, updated = NOW() WHERE textentityid = ? AND languageid = ?", $urlname, Login::userid(), $this->id, $languageid )->count() ) {
            DB::query( "INSERT INTO site_textentity_content (textentityid, languageid, urlname, updatedby, updated ) VALUES (?, ?, ?, ?, NOW())", $this->id, $languageid, $urlname, Login::userid() );
         }
         
         CacheEngine::write( sprintf( 'textentity-urlname-%d-%d', $this->id, $languageid ), $urlname );
         
         return true;
         
      }
      
      public function getIngress( $language = false ) {
         
         if( !$this->id ) return '';
         
         $languageid = i18n::getLanguageId( $language );
         if( !$languageid ) return '';
         
         $ingress = CacheEngine::read( sprintf( 'textentity-ingress-%d-%d', $this->id, $languageid ) );
         if( !is_null( $ingress ) ) return $ingress;
         
         $ingress = DB::query( "SELECT ingress FROM site_textentity_content WHERE textentityid = ? AND languageid = ?", $this->id, $languageid )->fetchSingle();
         
         CacheEngine::write( sprintf( 'textentity-ingress-%d-%d', $this->id, $languageid ), $ingress );
         
         return empty( $ingress ) ? '' : $ingress;
         
      }
      
      public function setIngress( $ingress, $language = false ) {
         
         if( !$this->id ) return false;
         
         $languageid = i18n::getLanguageId( $language );
         if( !DB::query( "UPDATE site_textentity_content SET ingress = ?, updatedby = ?, updated = NOW() WHERE textentityid = ? AND languageid = ?", $ingress, Login::userid(), $this->id, $languageid )->count() ) {
            DB::query( "INSERT INTO site_textentity_content (textentityid, languageid, ingress, updatedby, updated ) VALUES (?, ?, ?, ?, NOW())", $this->id, $languageid, $ingress, Login::userid() );
         }
         
         CacheEngine::write( sprintf( 'textentity-ingress-%d-%d', $this->id, $languageid ), $ingress );
         
         return true;
         
      }
      
      public function getBody( $language = false ) {
         
         if( !$this->id ) return '';
         
         $languageid = i18n::getLanguageId( $language );
         if( !$languageid ) return '';
         
         
         
         if(  $this->isSecure() ){
            
            $body = CacheEngine::read( sprintf( 'ssl-textentity-body-%d-%d', $this->id, $languageid ) );
            if( !is_null( $body ) ) return $body;
            
            $body = DB::query( "SELECT body FROM site_textentity_content WHERE textentityid = ? AND languageid = ?", $this->id, $languageid )->fetchSingle();
            
            $sslstatic = 'static';
            
            $body = str_replace( 'http://static', '//' . $sslstatic , $body  );
            $body = str_replace( 'http://a.static', '//' . $sslstatic , $body  );
            $body = str_replace( 'http://b.static', '//' . $sslstatic , $body  );
            $body = str_replace( 'http://c.static', '//' . $sslstatic , $body  );
            $body = str_replace( 'http://d.static', '//' . $sslstatic , $body  );
            $body = str_replace( '//d.static', '//' . $sslstatic , $body  );
            $body = str_replace( '//a.static', '//' . $sslstatic , $body  );
            CacheEngine::write( sprintf( 'ssl-textentity-body-%d-%d', $this->id, $languageid ), $body );
            
            return empty( $body ) ? '' : $body;
         }else{
            
            $body = CacheEngine::read( sprintf( 'textentity-body-%d-%d', $this->id, $languageid ) );
            if( !is_null( $body ) ) return $body;
            
            $body = DB::query( "SELECT body FROM site_textentity_content WHERE textentityid = ? AND languageid = ?", $this->id, $languageid )->fetchSingle();
            CacheEngine::write( sprintf( 'textentity-body-%d-%d', $this->id, $languageid ), $body );
            
            return empty( $body ) ? '' : $body;
         }
         
      }
      
      private function isSecure() {
         return
           (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
           || $_SERVER['SERVER_PORT'] == 443 || $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https';
       }
      
      public function setBody( $body, $language = false ) {
         
         if( !$this->id ) return false;
         
         $languageid = i18n::getLanguageId( $language );
         if( !DB::query( "UPDATE site_textentity_content SET body = ?, updatedby = ?, updated = NOW() WHERE textentityid = ? AND languageid = ?", $body, Login::userid(), $this->id, $languageid )->count() ) {
            DB::query( "INSERT INTO site_textentity_content (textentityid, languageid, body, updatedby, updated ) VALUES (?, ?, ?, ?, NOW())", $this->id, $languageid, $body, Login::userid() );
         }
         
         CacheEngine::write( sprintf( 'textentity-body-%d-%d', $this->id, $languageid ), $body );
         
         return true;
         
      }
      
      public function setGrouping( $grouping ) {
         
         $this->fieldSet( 'grouping', implode( ' ', preg_split( '/ /', $grouping, 0, PREG_SPLIT_NO_EMPTY ) ) );
         
      }
      
      public function setAttachments( Array $attachments ) {
         
         $encoded = array();
         foreach( $attachments as $slotid => $filename ) {
            $encoded[] = sprintf( '%s|%s', $slotid, $filename );
         }
         
         $this->fieldSet( 'attachments', implode( ',', $encoded ) );
         return true;
         
      }
      
      public function addAttachment( $slotid, $filename, $diskfile ) {
         
         try {
            $targetfolder = sprintf( '%s/data/attachments/%d', getRootPath(), $this->id );
            if( !file_exists( $targetfolder ) ) {
               mkdir( $targetfolder, 0755, true );
            }
            
            $filename = Util::urlize( $filename );
            
            copy( $diskfile, sprintf( '%s/%s', $targetfolder, $filename ) );
            
            $attachments = $this->getAttachments();
            if( isset( $attachments[$slotid] ) ) {
               $this->removeAttachment( $slotid );
            }
            $attachments[$slotid] = $filename;
            
            $this->setAttachments( $attachments );
            return true;
            
         } catch( Exception $e ) {
            
            return false;
         }
         
      }
      
      public function removeAttachment( $slotid ) {
         
         $attachments = $this->getAttachments();
         
         $targetfile = sprintf( '%s/data/attachments/%d/%s', getRootPath(), $this->id, $attachments[$slotid] );
         if( file_exists( $targetfile ) ) {
            unlink( $targetfile );
         }
         
         unset( $attachments[$slotid] );
         $this->setAttachments( $attachments );
         return true;
         
      }
      
      public function getAttachments() {
         
         $attachments = array();
         $data = $this->fieldGet('attachments' );
         if( $data ) {
            foreach( explode( ',', $data ) as $attachment ) {
               
               list( $slotid, $filename ) = explode( '|', $attachment );
               $attachments[$slotid] = $filename;
               
            }
         }
         
         return $attachments;
         
      }
      
   }
   
?>