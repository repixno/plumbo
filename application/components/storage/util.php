<?php

   import( 'storage.stream' );
   import( 'website.permissions' );
   config( 'website.image' );

   class StorageUtil {

      static $invalidPattern = '/[\\+\*\?\[\^\]\$\(\)\{\}\=\!\<\>\|\:\']/';

      static function readImage( $imageid, $dx = 0, $dy = 0, $return = false ) {

         $settings = Settings::GetSection( 'image' );

         if ( $dx == 0 || $dy == 0 ) return self::readOriginal( 'storage://eurofoto/' . $imageid, $return );

         $substream = '/preview';
         if ( $dx > $settings[ 'preview' ][ 'dx' ] || $dy > $settings[ 'preview' ][ 'dy' ] ) {

            $substream = '';

         }

         return self::readStream( 'storage://eurofoto/' . $imageid . $substream, $dx, $dy, $return );

      }


      static function readThumbnail( $imageid, $dx = 0, $dy = 0, $return = false  ) {

         $settings = Settings::GetSection( 'image' );

         if ( $dx == 0 || $dy == 0 ) return self::readOriginal( 'storage://eurofoto/' . $imageid , '/preview', $return );

         $dx = $dx > $settings[ 'preview' ][ 'dx' ] ? $settings[ 'preview' ][ 'dx' ] : $dx;
         $dy = $dy > $settings[ 'preview' ][ 'dy' ] ? $settings[ 'preview' ][ 'dy' ] : $dy;

         return self::readStream( 'storage://eurofoto/' . $imageid . '/preview', $dx, $dy, $return );

      }

      static function readOriginal( $stream, $return ) {
         
         if ( $return ) {
            
            return file_get_contents( $stream );
            
         } else {
            
            header( 'Content-Type: image/jpeg' );
            header( 'Content-Length: '.filesize( $stream ) );
            return readfile( $stream );
            
         }

      }

      static function readStream( $stream, $dx, $dy, $return ) {

         $fh = fopen( $stream, 'rb' );

         $im = new Imagick();
         $im->readImageFile( $fh );
         $im->thumbnailImage( $dx, $dy, true );

         $result = true;
         if ( !$return ) {

            header( 'Content-Type: image/jpeg' );
            header( 'Content-Length: '.strlen( $im ) );
            echo $im;

         } else {

            $result = $im->getImageBlob();

         }

         $im->clear();
         $im->destroy();

         fclose( $fh );

         return $result;

      }

      static function uploadImage( $ownerid, $albumid, $localimagepath, $contenttype = null, $title = null, $description = '', $imageid = 0 ) {

         if ( !file_exists( $localimagepath ) ) {

            return false;

         }


         
         $hashcode = md5_file( $localimagepath );

         $metaData = self::getImageMeta( $localimagepath );
         $imageData = array(
            'ownerid' => $ownerid,
            'hashcode' => $hashcode,
            'albumid' => $albumid,
            'contenttype' => $contenttype,
            'title' => $title,
            'description' => $description
            );
         
         if ( $imageid = self::saveImageData( $imageData, $metaData, $imageid ) ) {

            $fh = fopen( $localimagepath, 'rb' );
            file_put_contents( 'storage://eurofoto/' . $imageid, $fh );
            fclose( $fh );
            
            self::rotateByExif( $imageid );
            
            return $imageid;

         }

         return false;

      }
      
      static function rotateByExif( $imageid ) {
         
         if( $imageid > 0 ) {
         
            $image = new Image( $imageid );
            
            switch( $image->exif_orientation ) {
               default:
                  break;
               case 3:
               case 4:
                  $image->rotate( 180 );
                  break;
               case 5:
               case 6:
                  $image->rotate( 90 );
                  break;
               case 7:
               case 8:
                  $image->rotate( 270 );
                  break;
            }
            
            $image->save();
            
         }
         
      }
      
      static function uploadImageString( $ownerid, $albumid, $data, $contenttype = null, $title = null, $description = '', $imageid = Model::CREATE ) {

         if ( empty( $data ) ) {

            return false;

         }

         $hashcode = md5( $data );

         $metaData = self::getImageMeta( null, $data );
         $imageData = array(
            'ownerid' => $ownerid,
            'hashcode' => $hashcode,
            'albumid' => $albumid,
            'contenttype' => $contenttype,
            'title' => $title,
            'description' => $description
         );
         
         if ( $imageid = self::saveImageData( $imageData, $metaData, $imageid ) ) {
            
            file_put_contents( 'storage://eurofoto/' . $imageid, $data );
            
            self::rotateByExif( $imageid );
            
            return $imageid;
            
         }

         return false;

      }

      static function saveImageData( $imageData, $metaData, $imageid = Model::CREATE ) {
         
         // create a new image object or open up an existing one
         $image = new Image( $imageid );
         $image->ownerid = $imageData[ 'ownerid' ];
         $image->aid = isset( $imageData[ 'albumid' ] ) ? $imageData[ 'albumid' ] : null;
         $image->filetype = isset( $imageData[ 'contenttype' ] )
                               ? ( 
                                    strpos( $imageData['contenttype'], 'image/' ) !== false 
                                  ? str_replace( 'image/', '', $imageData[ 'contenttype' ] )
                                  : 'jpeg' // <-- hack for application/octet-stream
                                 )
                               : 'jpeg';
                               
                               
         if(  !empty($metaData[ 'exif' ]['title']) ){                                
            $image->title = $metaData[ 'exif' ]['title'];
         }else{
            $image->title = $imageData[ 'title' ];
         }
                                   
                                   
               
               
         
         $image->description = $imageData[ 'description' ];
         $image->date = date( 'Y-m-d' );
         $image->x = $metaData[ 'geometry' ][ 'width' ];
         $image->y = $metaData[ 'geometry' ][ 'height' ];
         $image->size = $metaData[ 'fileSize' ];
         $image->hashcode = $imageData[ 'hashcode' ];
         
         // store EXIF information
         $image->exif_date = self::getExifDate( $metaData[ 'exif' ] );
         $image->exif_x_res = self::getExifXRes( $metaData[ 'exif' ] );
         $image->exif_y_res = self::getExifYRes( $metaData[ 'exif' ] );
         $image->exif_make = self::getExifMake( $metaData[ 'exif' ] );
         $image->exif_model = self::getExifModel( $metaData[ 'exif' ] );
         $image->exif_orientation = self::getExifOrientation( $metaData[ 'exif' ] );
         $image->exif_exposure_time = self::getExifExposureTime( $metaData[ 'exif' ] );
         
         // set GPS coordinates
         $image->exif_gps_longitude = $metaData['GPS']['Longitude'];
         $image->exif_gps_latitude = $metaData['GPS']['Latitude'];
         $image->exif_gps_altitude = $metaData['GPS']['Altitude'];
         
         // save the image to DB
         if ( $image->save() ) {

            // temporarily grant owner access to this image
            PermissionManager::current()->grantAccessTo( $image->imageid, 'image', PERMISSION_OWNER );
            
            // return the imageid
            return $image->imageid;

         }
         
         return false;

      }

      static function getImageMeta( $filepath = null, $imagedata = null ) {

         $fileSize = null;
         $im = new Imagick();
         $info = array();
         if ( isset( $filepath ) ) {

            $fileSize = filesize( $filepath );

            $im->pingImage( $filepath );
            $info[ 'GPS' ] = self::fetchFileGPSExif( $filepath );

         } else if ( isset( $imagedata ) ) {


            $fileSize = strlen( $imagedata );

            $im->pingImageBlob( $imagedata );

         } else {

            return false;

         }

         $info[ 'exif' ] = self::normalizeExif( $im->getImageProperties( 'exif:*' ) );

         $info = array_merge( $info, $im->identifyImage() );
         $im->clear();
         $im->destroy();

         $info[ 'fileSize' ] = $fileSize;

         return $info;

      }

      static function fetchFileGPSExif( $filepath ) {
         
         try {
            
            $exif = @read_exif_data( $filepath );
            
         } catch( Exception $e ) {
            
            return false;
            
         }
         
         //die(print_r($exif,true));

         if ( isset( $exif[ 'GPSVersion' ] ) || isset( $exif[ 'GPSTimeStamp' ] ) || isset( $exif[ 'GPSLongitude' ] ) || isset( $exif[ 'GPSLatitude' ] ) ) {

            return self::getGPSTags( $exif );

         }

         return false;

      }

      static function normalizeExif( $data ) {

         $exifValues = array();
         foreach ( $data as $key=>$value ) {

            $key = str_replace( 'exif:', '', $key );
            $exifValues[ $key ] = $value;

         }

         return $exifValues;

      }

      static function getExifDate( $exifData ) {

         $defaultDate = null;
         $nullDate = '0000:00:00 00:00:00';

         if ( isset( $exifData[ 'DateTimeOriginal' ] ) && $exifData[ 'DateTimeOriginal' ] != $nullDate ) {

            return $exifData[ 'DateTimeOriginal' ];

         }

         if ( isset( $exifData[ 'DateTimeDigitized' ] ) && $exifData[ 'DateTimeDigitized' ] != $nullDate ) {

            return $exifData[ 'DateTimeDigitized' ];

         }

         if ( isset( $exifData[ 'DateTime' ] ) && $exifData[ 'DateTime' ] != $nullDate ) {

            return $exifData[ 'DateTime' ];

         }

         return $defaultDate;

      }

      static function getExifXRes( $exifData ) {

         $defaultRes = -1;

         if ( isset( $exifData[ 'XResolution' ] ) ) {

            $resParts = explode( '/', $exifData[ 'XResolution' ] );
            if ( is_numeric( $resParts[ 0 ] ) ) {

               return $resParts[ 0 ];

            }

         }

         return $defaultRes;

      }

      static function getExifYRes( $exifData ) {

         $defaultRes = -1;

         if ( isset( $exifData[ 'YResolution' ] ) ) {

            $resParts = explode( '/', $exifData[ 'YResolution' ] );
            if ( is_numeric( $resParts[ 0 ] ) ) {

               return $resParts[ 0 ];

            }

         }

         return $defaultRes;

      }

      static function getExifMake( $exifData ) {

         $defaultMake = 'n/a';
         $ret = null;
         if ( isset( $exifData[ 'Make' ] ) && !empty( $exifData[ 'Make' ] ) && is_string( $exifData[ 'Make' ] ) ) {

            $ret = $exifData[ 'Make' ];

         }

         $p = preg_match( self::$invalidPattern, $ret );
         if ( isset( $ret ) && $p ) {

            $ret = null;

         }

         return isset( $ret ) ? $ret : $defaultMake;

      }

      static function getExifModel( $exifData ) {

         $defaultModel = 'n/a';
         $ret = null;
         if ( isset( $exifData[ 'Model' ] ) && !empty( $exifData[ 'Model' ] ) && is_string( $exifData[ 'Model' ] ) ) {

            $ret = $exifData[ 'Model' ];

         }

         $p = preg_match( self::$invalidPattern, $ret );
         if ( isset( $ret ) && $p ) {

            $ret = null;

         }

         return isset( $ret ) ? $ret : $defaultModel;

      }

      static function getExifOrientation( $exifData ) {

         $minOrientation = 1;
         $maxOrientation = 8;
         if ( isset( $exifData[ 'Orientation' ] ) && $exifData[ 'Orientation' ] >= $minOrientation && $exifData[ 'Orientation' ] <= $maxOrientation ) {

            return $exifData[ 'Orientation' ];

         }

         return $minOrientation;

      }

      static function getExifExposureTime( $exifData ) {

         $defaultET = 'n/a';

         if ( !isset( $exifData[ 'ExposureTime' ] ) ) {

            return $defaultET;

         }

         $fracParts = explode( '/', $exifData[ 'ExposureTime' ] );

         if ( $fracParts[0] > 0 ) {

            $fracDenom = round( $fracParts[1]/$fracParts[0] );

            if ( $fracDenom > 0 ) {

               return "1/$fracDenom";

            }

         }

         return $defaultET;

      }

      static function getGPSTags( $exifData ) {
         
         // Check if required GPS data is present.
         if ( !isset( $exifData[ 'GPSLatitude' ] ) || !isset( $exifData[ 'GPSLongitude' ] ) ) {

            return false;

         }

         // Create return array.
         $ret = array(
            'LatitudeRef' => $exifData[ 'GPSLatitudeRef' ],
            'Latitude' => self::gpsDMS2Dec( $exifData[ 'GPSLatitude' ], $exifData[ 'GPSLatitudeRef' ] ),
            'LongitudeRef' => $exifData[ 'GPSLongitudeRef' ],
            'Longitude' => self::gpsDMS2Dec( $exifData[ 'GPSLongitude' ], $exifData[ 'GPSLongitudeRef' ] ),
            'AltitudeRef' => $exifData[ 'GPSAltitudeRef' ],
            'Altitude' => self::gpsDMS2Dec( $exifData[ 'GPSAltitude' ], $exifData[ 'GPSAltitudeRef' ] ),
            // 'Altitude' => isset( $exifData[ 'GPSAltitude' ] ) ? self::evalFraction( $exifData[ 'GPSAltitude' ] ) : 0,
         );
         
         // Return GPS data.
         return $ret;

      }

      // Converts from Degrees Minutes Seconds format to decimal format.
      static function gpsDMS2Dec( $degArr, $zeroRef  ) {

         for ( $i=0; $i<count($degArr);$i++ ) {

            $degArr[ $i ] = self::evalFraction( $degArr[ $i ] );

         }

         $totSec = ($degArr[ 1 ] * 60) + $degArr[ 2 ];
         $secFrac = $totSec / 3600;
         $totDec = $degArr[ 0 ] + $secFrac;

         switch ( strtoupper( $zeroRef ) ) {

            case 'W':
            case 'S':
               $totDec = -$totDec;
               break;

         }

         return $totDec;

      }

      static function evalFraction( $fracStr ) {
         
         $decStr = null;
         if( is_string( $fracStr ) && stripos( $fracStr, '/' ) !== false ) {
            
            $parts = explode( '/', $fracStr );
            if( (int) $parts[ 1 ] > 0 ){
               $decStr = (int) $parts[ 0 ] / (int) $parts[ 1 ];
            }
         }

         if ( is_numeric( $decStr ) ) {

            return $decStr;

         }
         
         return $fracStr;
         
      }

   }

?>
