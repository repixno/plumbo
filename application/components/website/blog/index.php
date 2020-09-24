<?PHP
   
   model( 'site.blog.index' );
   import( 'website.blog.post' );
   import( 'math.zbase32' );
   
   class Blog extends DBSiteBlog {
      
      static function fromShortname( $shortname ) {
         
         $blog = Model::fromFieldValue( array( 'shortname' => $shortname ), 'Blog', false );
         if( $blog instanceof Blog ) {
            
            return $blog;
            
         } else {
            
            return false;
            
         }
         
      }
      
      static function fromUserID( $userid ) {
         
         $blog = Model::fromFieldValue( array( 'uid' => $userid ), 'Blog', false );
         if( $blog instanceof Blog ) {
            
            return $blog;
            
         } else {
            
            return false;
            
         }
         
      }
      
      static $shortnamecache;
      
      static function shortNameFromBlogId( $blogid ) {
         
         if( isset( Blog::$shortnamecache[$blogid] ) ) {
            
            return Blog::$shortnamecache[$blogid];
            
         } else {
            
            $blog = new Blog();
            $shortname = $blog->collection( array( 'shortname' ), array( 'blogid' => $blogid ) )->fetchSingle();
            if( $shortname ) {
               Blog::$shortnamecache[$blogid] = $shortname;
            }
            
            return $shortname;
            
         }
         
      }
      
      public function __setup() {
         
         $setup = parent::__setup();
         
         $this->uid = Login::userid();
         $this->created = 'now';
         
         return $setup;
         
      }
      
      public function asArray() {
         
         try {
            
            return array(
               'id' => $this->blogid,
               'yours' => $this->uid == Login::userid(),
               'ownerid' => $this->uid,
               'owner' => User::getNameFromUid( $this->uid ),
               'shortname' => $this->shortname,
               'title' => $this->title,
               'description' => $this->description,
               'numposts' => $this->numposts,
               'created' => $this->created,
               'updated' => $this->updated,
            );
            
         } catch( Exception $e ) {
            
            return false;
            
         }
         
      }
      
      public function addPost( $title = '', $intro = '', $body = '' ) {
         
         if( $this->uid != Login::userid() ) return false;
         
         $post = new BlogPost();
         $post->blogid = $this->blogid;
         $post->title = $title;
         $post->intro = $intro;
         $post->body = $body;
         $post->save();
         
         $this->updateNumPosts();
         
         return $post;
         
      }
      
      public function updateNumPosts() {
         
         if( $this->uid != Login::userid() ) return false;
         
         if( $this->blogid ) {
            $this->numposts = DB::query( 'SELECT COUNT(postid) FROM site_blog_posts WHERE blogid = ?', $this->blogid );
            $this->save();
         }
         
      }
      
      public function getPosts( $year = null, $month = null, $limit = 20, $offset = 0 ) {
         
         $posts = array();
         $where = array(
            'blogid' => $this->blogid,
         );
         
         if( $year && !$month ) {
            $where['created'] = array( 'BETWEEN', array( sprintf( '%04d-01-01 00:00:00', $year ),  sprintf( '%04d-12-31 23:59:59', $year ) ) );
         } elseif( $year && $month ) {
            $periodstart = sprintf( '%04d-%02d-01 00:00:00', $year, $month );
            $where['created'] = array( 'BETWEEN', array( $periodstart, date( 'Y-m-d H:i:s', strtotime( '+1 month -1 second', strtotime( $periodstart ) ) ) ) );
         }
         
         $collection = new BlogPost();
         foreach( $collection->collection( null, $where, 'created DESC, postid DESC', $limit, $offset )->fetchAllAs( 'BlogPost' ) as $blogpost ) {
            $post = $blogpost->asArray();
            if( $post && is_array( $post ) ) {
               $posts[] = $post;
            }
         }
         
         return $posts;
         
      }
      
   }
   
?>