<?PHP

   define( 'JPEGTRAN_ROTATE', '( /usr/bin/jpegtran -rot %d -copy all -perfect %s || /usr/bin/djpeg %s| /usr/bin/pnmflip -r%d | /usr/bin/cjpeg -quality 100 ) > %s' );
   define( 'JPEGTRAN_ROTATE1', '( /usr/bin/jpegtran -rot %d -copy all -perfect %s || /usr/bin/jpegtran -rot %s -trim %s ) > %s' );
   define( 'JPEGTRAN_ROTATE2', '/usr/bin/jpegtran -rot %s -trim %s > %s' );
   define( 'JHEAD_ROTSTRIP', '/usr/bin/jhead -norot %s' );
   
   model( 'object.index' );
   import( 'website.permissions' );
   import( 'website.album' );
   model( 'site.productarticle' );

   class Image extends DBObject {
      private $secure = "iuSh1xae";

      public function isLoaded() {

         if( $this->deleted_at ) {

            throw new SecurityException( 'This image no longer exists!' );

         }

         if( !$this->hasAccess() ) {

            throw new SecurityException( 'You have no access to this image' );

         }

         return parent::isLoaded();

      }

      /**
       * @todo Make it return false if the user has no access to this object
       */
      public function hasAccess() {

         return PermissionManager::current()->hasAccessTo( $this->bid, 'image' );

      }

      public function asArray() {

         import( 'math.signer' );
         
         if( $this->aid ) {
            try {
               $album = new Album( $this->aid );
               $urlizedalbumtitle = Util::urlize( $album->title );
            } catch( Exception $e ) {
               $urlizedalbumtitle = '';
            }
         } else {
            $urlizedalbumtitle = Util::urlize( __( 'Inbox' ) );
         }
         
         if( $this->quarantined_at > 0 ){
            $imageurl = '/support/pictures-in-quarantine/';
         }else{
            $imageurl = sprintf( '/myaccount/album/image/%d', $this->bid );
         }
         
         

         return array(
				'id'                => $this->bid,
				'date'              => $this->date,
				'title'             => $this->title,
				'description'       => $this->description,
				'identifier'        => $this->identifier,
				'urlname'           => Util::urlize( $this->title ),
				'thumbnail'         => $this->getThumbnail(),
				'screensize'        => $this->getURL( 630, 400 ),
				'fullsize'          => $this->getURL(),
				'aid'               => $this->aid,
				'quarantined'       => $this->quarantined_at,
				'urls'              => array(
				  'private'            => $imageurl,
				  'privatealbum'       => sprintf( '/myaccount/album/%d/%s', $this->aid, $urlizedalbumtitle ),
				  'gallery'            => sprintf( '/gallery/album/image/%d', $this->bid ),
				  'galleryalbum'       => sprintf( '/gallery/album/%d/%s', $this->aid, $urlizedalbumtitle ),
				  'shared'             => sprintf( '/shared/album/image/%d', $this->bid ),
				  'sharedalbum'        => sprintf( '/shared/album/%d/%s', $this->aid, $urlizedalbumtitle ),
				  'sharedstream'       => sprintf( '/shared/album/image/stream/%d/%s', $this->bid, Signer::sign( $this->bid, 'share' ) ),
				),
				'exif'              => $this->getExifData(),
				'imagedate'        => $this->getImageDate(),
				'x'                 => $this->x,
				'y'                 => $this->y,
				'permission'        => PermissionManager::current()->permissionType( $this->bid, 'image' ),
            'owner'             => array(
               'uid'               => $this->owner_uid,
			      'name'              => User::getNameFromUid( $this->owner_uid ),
			      'yours'             => $this->owner_uid == login::userid(),

            ),
            'license'           => array(
               'type'              => $this->licensetype,
               'fee'               => $this->licensefee,
            ),
			);

      }
      
      public function GetImageDate(){
         
         if(strtotime($this->exif_date) <= strtotime('1970-01-01')){
            return strtotime($this->date);
         }
         else{
            return strtotime($this->exif_date);
         }
      }

      public function getExifData() {

         return array(
            'date'   => $this->exif_date,
            'width'  => $this->x,
            'height' => $this->y,
            'xres'   => $this->exif_x_res,
            'yres'   => $this->exif_y_res,
            'make'   => $this->exif_make,
            'model'  => $this->exif_model,
            'orientation' => $this->exif_orientation,
            'exposuretime' => $this->exif_exposure_time,
            'gps' => array(
               'altitude' => $this->exif_gps_altitude,
               'latitude' => $this->exif_gps_latitude,
               'longitude' => $this->exif_gps_longitude,
            ),
         );

      }
      
      static function select( $imageid = 0 ) {

         Session::set( 'selectedimageid', $imageid );

      }

      static function selected() {

         return Session::get( 'selectedimageid', 0 );

      }

      public function getURL( $xres = 0, $yres = 0 ) {

         if( $this->quarantined_at ) {
            return sprintf( '%s/layout_grafikk/quarantine.jpg', WebsiteHelper::rootBaseUrl() );
         }

         $expires = time() + 3600;
         $code = base64_encode( $expires
                                 . '|'
                                 . md5( 'yz9987gd'
                                      . $this->owner_uid
                                      . $this->bid
                                      . $expires
                                      . $_SERVER['REMOTE_ADDR']
                                   )
                                );

         if( $xres > 0 && $yres > 0 ) {

            return sprintf('%s/images/stream/image/%s/%s/%s',
               WebsiteHelper::rootBaseUrl(),
               $this->bid,
               $xres,
               $yres
            );

         } else {

            return sprintf('%s/images/stream/image/%s',
               WebsiteHelper::rootBaseUrl(),
               $this->bid
            );

         }

      }

      public function getThumbnail() {

         if( $this->quarantined_at ) {
            return sprintf( '%s/layout_grafikk/quarantine.jpg', WebsiteHelper::rootBaseUrl() );
         }


         return sprintf('%s/images/stream/thumbnail/%d',
			WebsiteHelper::rootBaseUrl(),
			$this->bid
	       );
  
      }

      public function qualityRating( $prodid ) {

         $defResolution = 300;

         $prodOpt = ProductOption::fromProdNo( $prodid );
         try {
         $article = new DBProductArticle( $prodOpt->refid );
         } catch ( Exception $e ) {}
         $sizeInches = array(
            'long' => max( $article->printx, $article->printy )/$defResolution,
            'short' => min( $article->printx, $article->printy )/$defResolution
            );

         return $this->calculateQuality( $sizeInches, false );

      }

      /**
      * Rate printed quality of image.
      *
      * @access      public
      * @author      Svein Arild Bergset <sab@interweb.no>
      * @param       string/array      $size       Print size to be checked. Example: '4x6' , array( '4x5', '4x6' )
      * @param       boolean           $isCm       Set which length unit the size represent (cm=true/inch=false).
      * @version     0.1 - 11.02.2010 14:40
      * @since       10.02.2010 14:02
      *
      */
      public function calculateQuality( $size = null, $isCm = false ) {

         // Return empty if size of image is not set.
         if ( !($size[ 'long' ] > 0 && $size[ 'short' ] > 0) ) {

            return null;

         }

         // Conversion values.
         $cmprin = 2.54;
         $inprcm = 1/$cmprin;

         // Find which is min and max of the image sides.
         $maxDim = max( $this->x, $this->y );
         $minDim = min( $this->x, $this->y );

         // Extract sides and find min max.
         $maxSS = $size[ 'long' ];
         $minSS = $size[ 'short' ];

         // Convert to inches.
         if ( $isCm ) {

            $maxSS *= $inprcm;
            $minSS *= $inprcm;

         }

         // Find DPIs of min and max sides.
         $maxDpi = $maxDim/$maxSS;
         $minDpi = $minDim/$minSS;

         // Find the limiting DPI.
         $lessDpi = min( $maxDpi, $minDpi );

         // Set weighted quality.
         $ret = null;
         if ( $lessDpi >= 300 ) {

            $ret = 100;

         } else if ( $lessDpi >= 150 ) {

            $ret = 50;

         } else {

            $ret = 0;

         }

         // Return results.
         return $ret;

      }
      
      public function rotate( $degrees ) {
         
         import( 'storage.util' );
         
         switch( $degrees ) {
            case 90:
            case 180:
            case 270:
               break;
            case -90:
               $degrees = 270;
               break;
            case -180:
               $degrees = 180;
               break;
            case -270:
               $degrees = 90;
               break;
            default:
               $degrees = 0;
               break;
         }
         
         unlink( $tmpfile = tempnam( '/tmp/', 'jpegrotate' ) );
         
         $outfile = $tmpfile.'.out.jpg';
         $tmpfile = $tmpfile.'.jpg';
         
         // read out hte original stream...
         file_put_contents( $tmpfile, file_get_contents( 'storage://eurofoto/'.$this->bid ) );
         
         // rotate it using a lossless transformation
         //@exec( sprintf( JPEGTRAN_ROTATE, $degrees, $tmpfile, $tmpfile, $degrees, $outfile ) );
         @exec( sprintf( JPEGTRAN_ROTATE1, $degrees, $tmpfile, $degrees, $tmpfile, $outfile ) );
         //@exec( sprintf( JPEGTRAN_ROTATE2, $degrees, $tmpfile, $outfile ) );
         
         // strip any EXIF rotation information in the file
         @exec( sprintf( JHEAD_ROTSTRIP, $outfile ) );
         
         // remove the temporary file
         @unlink( $tmpfile );
         
         // does the output file exist?
         if( file_exists( $outfile ) && filesize( $outfile ) > 0 ) {
            
            /// ...and write it back to the storage
            $fh = fopen( $outfile, 'rb' );
            file_put_contents( 'storage://eurofoto/' . $this->bid, $fh );
            fclose( $fh );
            
            // calculate the new hash
            $this->hashcode = md5_file( $outfile );
            
            // find the new filesize
            $this->size = filesize( $outfile );
            
            // remove the out-file
            @unlink( $outfile );
            
            // swap width/height?
            switch( $degrees ) {
               
               case 90:
               case 270:
                  $height = $this->x;
                  $width = $this->y;
                  $this->y = $height;
                  $this->x = $width;
                  $this->save();
               
            }
            
            // success!
            return true;
            
         } else {
            
            // remove the out-file
            @unlink( $outfile );
            
            return false;
            
         }
         
      }
      
      public function populateCountMatrix( $matrix, $quality, $prodno ) {

         if ( !isset( $matrix ) ) {

            $matrix = array();

         }
         
         if ( !isset( $matrix[ $prodno ] ) ) {
            
            $matrix[ $prodno ] = array( 0,0,0,0 );
            
         }

         if ( $quality > 75 ) {

            $matrix[ $prodno ][3]++;

         } else if ( $quality > 50 ) {

            $matrix[ $prodno ][2]++;

         } else if ( $quality > 25 ) {

            $matrix[ $prodno ][1]++;

         } else {

            $matrix[ $prodno ][0]++;

         }

         return $matrix;

      }
      
      public function delete() {

         $albums = new Album();
         foreach ( $albums->collection( array( 'aid' ), array( 'default_bid' => $this->imageid, 'deleted_at' => null ) )->fetchAllAs( 'Album' ) as $album ) {

            $album->default_bid = null;
            $album->save();
            
         }
         
         return parent::delete();
         
      }
      
      public function dl_image(){
         config( 'website.storage' );
         $album = new Album( $this->aid );
         $albumArray = $album->asArray();
         
         if( $albumArray['owner']['yours'] || $albumArray['access']['download'] ){
         
            $imagespath = Settings::Get( 'storage', 'path');
            $filename = $imagespath . $this->filnamn;

            //First, see if the file exists
            if (!is_file( $filename )) { die("<b>404 File not found!</b>"); }
            
            //Gather relevent info about file
            $len = filesize( $filename );
            
            $info = pathinfo( $this->title );
            $name = basename($this->title,'.'.$info['extension']);
            
            
            $file_extension = $this->filtype;
            
            
            //This will set the Content-Type to the appropriate setting for the file
            switch( $file_extension ) {
               case "gif": 
                  $ctype="image/gif";
                  $extension = 'gif';
                  break;
               case "png": 
                   $ctype="image/png"; 
                   $extension = 'png';
                  break;
               case "jpeg":
               case "jpg":
                  $ctype="image/jpg";
                  $extension = 'jpg';
                  break;
               case "tif":
               case "tiff":
                  $ctype="image/tif";
                  $extension = 'tif';
                  break;
            }
            
            //Begin writing headers
            header("Pragma: public");
            header("Expires: 0");
            header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
            header("Cache-Control: public");
            header("Content-Description: File Transfer");
            
            //Use the switch-generated Content-Type
            header("Content-Type: $ctype");
            
            //Force the download
            $header="Content-Disposition: attachment; filename=" . str_replace(' ', '_' , $name )  . "." . $extension .";";
            header( $header );
            header("Content-Transfer-Encoding: binary");
            header("Content-Length: " . $len);
            readfile( $filename );
            
            return true;

         }
         
      }
      
      public function setAid( $aid ) {
         
         if( $aid ) {
            try {
               $album = new Album( $aid );
               if( $album->uid != Login::userid() ) {
                  $aid = null;
               }
            } catch( Exception $e ) {
               $aid = null;
            }
         } else {
            $aid = null;
         }
         
         return parent::setAid( $aid );
         
      }
      
   }
   
?>