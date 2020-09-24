<?PHP
   
   import( 'pages.admin' );
   import( 'website.product' );
   import( 'website.article' );
   
   class AdminContentDefault extends AdminPage implements IView {
      
      protected $template = 'content.default';
      
      public function Execute( $limit = 10 ) {
         
         $entities = array( 'product' => new Product(), 'article' => new Article() );
         $limit = $limit > 25 ? 25 : $limit;
         
         foreach( $entities as $entitytype => $entity ) {
            
            $list = array();
            foreach( $entity->collection( null, array( 'deleted' => null, 'siteid' => Session::get( 'adminsiteid', 1 ) ), 'id DESC', $limit )->fetchAllAs( ucfirst( $entitytype ) ) as $values ) {
               
               if( $entitytype == 'product' && strlen( $values->images ) > 0 ) {
                  
                  // Select the last image added, if any
                  $values->images = sprintf( '%s/images/products/thumbs/squareaspect/100/%s', WebsiteHelper::staticBaseUrl(), end( explode( ',', $values->images ) ) );
                                    
               } else {
                  
                  $values->images = false;
                  
               }
               
               $list[] = $values;
               
            }
                        
            # $this->product || $this->article
            $this->$entitytype = $list;
            
         }
         
         
      }
      
   }
   
?>