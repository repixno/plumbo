<?PHP
   
   class MyAccountSearch extends UserPage implements IValidatedView {
      
      protected $template = 'myaccount.search.index';
      
      public function Validate() {
         
         return array(
            'execute' => array(),
            'result' => array(
               'post' => array(
                  'string' => VALIDATE_STRING,
               )
            ),
         );
         
      }
      
      public function Execute() {
         
         $this->string = '';
         
      }
      
      public function Result() {
         
         $this->setTemplate( 'myaccount.search.result' );
         
         // fetch/trim the string from POST
         $string = trim( $_POST['string'] );
         
         $this->string = $string;
         
         // decode it for use in searches
         $string = $string;
         
         // validate its length
         if( strlen( $string ) < 3 ) {
            
            // something bad happened!
            $this->error = 'Search string must be at least 3 characters long';
            
            return true;
            
         }
         
         // find the search-subitems
         $strings = preg_split( "/ /", $string, 0, PREG_SPLIT_NO_EMPTY );
         
         // execute the sub-searches
         $albums = $this->searchAlbums( $strings );
         $images = $this->searchImages( $strings );
         $friendsalbums = $this->searchFriendsAlbums( $strings );
         $friendsimages = $this->searchFriendsImages( $strings );
         
         // prepare the results
         $result = array();
         
         // add to the template
         if( count( $images ) ) $result['images'] = $images;
         if( count( $albums ) ) $result['albums'] = $albums;
         if( count( $friendsalbums ) ) $result['friendsalbums'] = $friendsalbums;
         if( count( $friendsimages ) ) $result['friendsimages'] = $friendsimages;
         if( count( $result ) ) $this->result = $result;
         
      }
      
      private function searchFriendsAlbums( $strings ) {
         
         // search shared
         $value = array( Login::userid() );
         $names = array();
         foreach( $strings as $string ) {
            $names[] = 'namn ILIKE ?';
            $value[] = '%'.$string.'%';
         }
         $names = implode( ' AND ', $names );
         
         $descs = array();
         foreach( $strings as $string ) {
            $descs[] = 'description ILIKE ?';
            $value[] = '%'.$string.'%';
         }
         $descs = implode( ' AND ', $descs );
         
         $query = 'SELECT b.aid 
                     FROM tilgangtilalbum_dedikert d
                LEFT JOIN bildealbum b
                       ON b.aid = d.aid
                    WHERE d.uid = ? 
                      AND b.deleted_at IS NULL 
                      AND (('.$names.') OR ('.$descs.'))';
         
         $friendsalbumshits = array();
         foreach( DB::query( $query, $value )->fetchAll() as $row ) {
            list( $aid ) = $row;
            $friendsalbumshits[$aid] = true;
         }
         
         $friendsalbums = array();
         foreach( $friendsalbumshits as $aid => $b ) {
            
            try {
               $album = new Album( $aid );
               $friendsalbums[] = $album->asArray();
            } catch( Exception $e ) {}
            
         }
         
         return $friendsalbums;
         
      }
      
      private function searchAlbums( $strings ) {
         
         // search albums
         $value = array( Login::userid() );
         $names = array();
         foreach( $strings as $string ) {
            $names[] = 'namn ILIKE ?';
            $value[] = '%'.$string.'%';
         }
         $names = implode( ' AND ', $names );
         
         $descs = array();
         foreach( $strings as $string ) {
            $descs[] = 'description ILIKE ?';
            $value[] = '%'.$string.'%';
         }
         $descs = implode( ' AND ', $descs );
         
         $query = 'SELECT aid 
                     FROM bildealbum 
                    WHERE uid = ? 
                      AND deleted_at IS NULL 
                      AND (('.$names.') OR ('.$descs.'))';
         
         $albumhits = array();
         foreach( DB::query( $query, $value )->fetchAll() as $row ) {
            list( $aid ) = $row;
            $albumhits[$aid] = true;
         }
         
         $albums = array();
         foreach( $albumhits as $aid => $b ) {
            
            try {
               $album = new Album( $aid );
               $albums[] = $album->asArray();
            } catch( Exception $e ) {}
            
         }
         
         return $albums;
         
      }
      
      private function searchImages( $strings ) {
         
         // search albums
         $value = array( Login::userid() );
         $names = array();
         foreach( $strings as $string ) {
            $names[] = 'tittel ILIKE ?';
            $value[] = '%'.$string.'%';
         }
         $names = implode( ' AND ', $names );
         
         $descs = array();
         foreach( $strings as $string ) {
            $descs[] = 'tekst ILIKE ?';
            $value[] = '%'.$string.'%';
         }
         $descs = implode( ' AND ', $descs );
         
         $query = 'SELECT bid 
                     FROM bildeinfo
                    WHERE owner_uid = ? 
                      AND deleted_at IS NULL 
                      AND (('.$names.') OR ('.$descs.'))';
         
         $imagehits = array();
         foreach( DB::query( $query, $value )->fetchAll() as $row ) {
            list( $bid ) = $row;
            $imagehits[$bid] = true;
         }
         
         $images = array();
         foreach( $imagehits as $bid => $b ) {
            
            try {
               $image = new Image( $bid );
               $images[] = $image->asArray();
            } catch( Exception $e ) {}
            
         }
         
         return $images;
         
      }
      
      private function searchFriendsImages( $strings ) {
         
         // search albums
         $value = array( Login::userid() );
         $names = array();
         foreach( $strings as $string ) {
            $names[] = 'tittel ILIKE ?';
            $value[] = '%'.$string.'%';
         }
         $names = implode( ' AND ', $names );
         
         $descs = array();
         foreach( $strings as $string ) {
            $descs[] = 'tekst ILIKE ?';
            $value[] = '%'.$string.'%';
         }
         $descs = implode( ' AND ', $descs );
         
         $query = 'SELECT b.bid 
                     FROM tilgangtilalbum_dedikert d
                LEFT JOIN bildeinfo b
                       ON b.aid = d.aid
                    WHERE d.uid = ? 
                      AND b.deleted_at IS NULL 
                      AND (('.$names.') OR ('.$descs.'))';
         
         $imagehits = array();
         foreach( DB::query( $query, $value )->fetchAll() as $row ) {
            list( $bid ) = $row;
            $imagehits[$bid] = true;
         }
         
         $images = array();
         foreach( $imagehits as $bid => $b ) {
            
            try {
               $image = new Image( $bid );
               $images[] = $image->asArray();
            } catch( Exception $e ) {}
            
         }
         
         return $images;
         
      }
      
   }
   
?>