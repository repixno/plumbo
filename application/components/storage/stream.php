<?php

   config( 'website.storage' );
   config( 'website.image' );
   import( 'website.image' );

   class StorageStream {

      private $position;
      private $stream;
      private $streamType;
      private $streamPath;
      private $streamPostfix;
      private $imageid;
      private $mode;
      private $storagePath;
      private $hasWritten = false;
      private $settings = array();

      public function stream_open( $path, $mode, $options = 0, &$opened_path = '' ) {

         // Import settings.
         $this->settings[ 'storage' ] = Settings::GetSection( 'storage' );
         $this->settings[ 'image' ] = Settings::GetSection( 'image' );

         $this->mode = $mode;
         $parsedUrl = parse_url( $path );
         list( , $this->imageid, $this->streamType ) = explode( '/', $parsedUrl[ 'path' ] );

         // Build filename postfix if explisit stream is given.
         $this->streamPostfix = !empty( $this->streamType ) ? sprintf( '.%s.%s', $this->streamType, 'jpg' ) : '';

         $image = new Image( $this->imageid );

         $filePath = $image->filename;

         if ( empty( $this->streamType ) && empty( $filePath ) ) {

            if ( $this->isWriteMode() ) {

               $file = $this->createPath( $this->imageid );

               if ( $file !== false ) {

                  $filePath = $file[ 'path' ];

                  // Save file data to database.
                  $image->filename = $filePath;
                  //$image->title = $file[ 'filename' ];
                  $image->sessionid = $file[ 'sessionid' ];
                  $image->save();

               } else {

                  return false;

               }

            }

         } else if ( !empty( $this->streamType ) && !$this->isWriteMode() ) {

            // Generate thumbnail if it doesn't exist.
            if ( !file_exists( $filePath . $this->streamPostfix ) ) {

               $this->streamPath = $filePath;
               $this->generateThumbnail();

            }

         }

         $this->streamPath = $filePath;

         $localpath = $this->settings[ 'storage' ][ 'path' ] . $filePath . $this->streamPostfix;
         // TODO: Check for STREAM_USE_PATH in $options and set $opened_path to localpath if found
         
         // Open the stream.
         $this->stream = fopen( $localpath, $mode );

         return true;

      }

      public function stream_read( $count ) {

         return fread( $this->stream, $count );

      }

      public function stream_write( $data ) {

         $res = fwrite( $this->stream, $data );
         if ( $res !== false ) {

            $this->hasWritten = true;

         }

         return $res;

      }
      
      public function url_stat( $path ) {
         
         $stream = new StorageStream();
         $stream->stream_open( $path, 'r' );
         $stats = $stream->stream_stat();
         $stream->stream_close();
         
         return $stats;
         
      }
      
      public function stream_tell() {

         return ftell( $this->stream );

      }

      public function stream_eof() {

         return feof( $this->stream );

      }

      public function stream_seek( $offset, $whence ) {

         return fseek( $this->stream, $offset, $whence );

      }

      public function stream_stat() {

         return fstat( $this->stream );

      }

      public function stream_close() {

         if ( $res = fclose( $this->stream ) !== false && $this->hasWritten && empty( $this->streamType ) ) {

            $this->generateThumbnail();

         }

         return $res;

      }

      private function generateThumbnail() {

         $im = new Imagick( $this->settings[ 'storage' ][ 'path' ] . $this->streamPath );

         $im->thumbnailImage( $this->settings[ 'image' ][ 'preview' ][ 'dx' ], $this->settings[ 'image' ][ 'preview' ][ 'dy' ], true );
         $im->writeImage(  $this->settings[ 'storage' ][ 'path' ] . $this->streamPath . $this->settings[ 'image' ][ 'preview' ][ 'postfix' ] );

         $im->clear();
         $im->destroy();
         
      }

      private function createPath( $imageid ) {

         $sessionId = Session::id();
         $fileName = $imageid . '.jpg';
         $path = sprintf( '%s/%s/%s/%s', $this->settings[ 'storage' ][ 'currentpartition' ], date( 'Y-m-d' ), $sessionId, $fileName );

         if ( !file_exists( dirname( $this->settings[ 'storage' ][ 'path' ] . $path ) ) ) {

            @mkdir( dirname( $this->settings[ 'storage' ][ 'path' ] . $path ), 0700, true );

         }

         if ( touch( $this->settings[ 'storage' ][ 'path' ] . $path ) ) {

            return array(
               'path' => $path,
               'sessionid' => $sessionId,
               'filename' => $fileName
               );

         }

         return false;

      }

      private function isWriteMode() {

         if ( stripos( $this->mode, 'w' ) !== false ) {

            return true;

         }

         return false;

      }

      static function isLocal() {

         return true;

      }

      static function expandLocalPath( $path ) {



      }

   }

   stream_wrapper_register( 'storage', 'StorageStream', STREAM_IS_URL ) || die( 'Failed to register protocol' );


?>
