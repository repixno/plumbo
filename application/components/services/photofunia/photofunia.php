<?php

   import( 'storage.util' );
   import( 'website.image' );
   import( 'website.album' );
   import( 'website.user' );


   /**
   * Class for PhotoFunia functionality.
   *
   * @package  services.photofunia
   * @access   public
   * @version  0.1
   * @author   Svein Arild Bergset <sab@interweb.no>
   */
   class PhotoFunia {

      private $accessKey = 'de425401573bf8f05d07d4f96ebd807c';
      private $urlEffectList = 'http://www.photofunia.com/effects.xml?access_key=%s';
      private $urlEffectDetail = 'http://www.photofunia.com/effects/%s.xml?access_key=%s';
      private $urlEffectProcess = 'http://www.photofunia.com/process.xml';
      private $urlFetchEffectIconFile = 'http://www.photofunia.com';
      private $urlFetchProcessedFile = 'http://www.photofunia.com';
      private $urlLocalPassThrough = '/photofun/resource/';

      private $defaultAlbumName = 'Photofunia';

      /**
      * Class constructor.
      *
      * @access   public
      * @version  0.1
      * @author   Svein Arild Bergset <sab@interweb.no>
      */
      public function __construct() {


      }

      public function getEffectList() {

         // Get list of Photofunia effects from cache.
         $effectsXML = CacheEngine::read( 'photofuniaeffects' );
         if ( !$effectsXML ) {

            // Get list of Photofunia effects from external and write to cache.
            $effectsXML = file_get_contents( sprintf( $this->urlEffectList, $this->accessKey ) );
            CacheEngine::write( 'photofuniaeffects', $effectsXML );

         }

         // Which effect preferences we want to expose.
         $exposableFields = array( 'id', 'key', 'title' );

         // Parse XML.
         $xml = simplexml_load_string( $effectsXML );
         $effects = $xml->xpath( "/groups/item/effects/item" );

         // Build array with all effects.
         $ret = array();
         foreach ( $effects as $effect ) {

            $cont = array();
            foreach ( $effect as $key=>$value ) {

               $val = (string) $value;

               if ( $key == 'id' ) {

                  $cont[ 'thumburl' ] = WebsiteHelper::staticBaseUrl() . '/images/photofuniathumb/' . $val;

               }

               if ( !in_array( $key, $exposableFields ) ) {

                  continue;

               }


               $cont[ $key ] = $val;

            }

            $ret[] = $cont;

         }

         // Return list of effects.
         return $ret;

      }

      public function getRandomEffectList( $size = 5 ) {

         $listIn = $this->getEffectList();
         $listOut = array();
         foreach ( array_rand( $listIn, $size ) as $key ) {

            $listOut[] = $listIn[ $key ];

         }
         return $listOut;

      }

      public function getEffectInfo( $id ) {

         // Get list of effects from Photofunia.
         $effectXML = file_get_contents( sprintf( $this->urlEffectDetail, $id, $this->accessKey ) );

         // Parse XML.
         $xml = simplexml_load_string( $effectXML );

         $ret = array(
            'title' => (string) $xml->general->title,
            'preview' => $this->urlLocalPassThrough . $this->scrambleString( array( $this->urlFetchEffectIconFile . (string) $xml->images->preview, 'image/jpeg' ) )
            );

         return $ret;

      }

      public function processPhoto( $imageid, $effectid = 0, $albumid = null ) {

         try {

            $postFields = array(
               'access_key' => $this->accessKey,
               'script_id' => $effectid
               );
            $binaryInfo = array(
               'field' => 'image',
               'data' => StorageUtil::readImage( $imageid, 0, 0, true ),
               'filename' => 'myfunphoto.jpg',
               'filetype' => 'image/jpeg'
               );

            $responseBody = $this->postBinary( $postFields, $binaryInfo );

            list( $headers, $data ) = $this->parseHTTPResponse($responseBody);
            $status = $headers[ 'HTTPStatusCode' ];

         } catch ( HTTP_Request2_Exception $e ) {

            print "Error: " . $e->getMessage();
            die();

         } catch ( Exception $e) {

            print "Error: " . $e->getMessage();
            die();

         }

         // Do post processing.
         if ( $status == 200 ) {

            // Return saved image object.
            return $this->savePhoto( $data, $albumid );

         } else {

            // Error.
            return false;

         }

      }

      private function postBinary( $params, $binaryInfo ) {

         $parsedUrl = parse_url( $this->urlEffectProcess );

         $boundary = md5( uniqid() );

         $contentType = "multipart/form-data; boundary=$boundary";

         $items = array();
         foreach ( $params as $key=>$value ) {

            array_push( $items, "--$boundary\r\nContent-Disposition: form-data; name=\"$key\"\r\n\r\n$value\r\n");

         }
         array_push( $items, "--$boundary\r\nContent-Disposition: form-data; name=\"{$binaryInfo['field']}\"; filename=\"{$binaryInfo['filename']}\"\r\n");
         array_push($items, "Content-Type: '{$binaryInfo['filetype']}'\r\nContent-Transfer-Encoding: binary\r\n\r\n{$binaryInfo['data']}\r\n--$boundary--\r\n");
         $data = implode('', $items);

         $contentLength = strlen($data);
         $fp = fsockopen( $parsedUrl[ 'host' ], 80);
         fputs($fp, "POST {$parsedUrl[ 'path' ]} HTTP/1.1\r\n");
         fputs($fp, "Host: {$parsedUrl[ 'host' ]}\r\n");
         fputs($fp, "Content-Type: $contentType\r\n");
         fputs($fp, "Content-Length: $contentLength\r\n");
         fputs($fp, "Connection: close\r\n\r\n");
         fputs($fp, $data, $contentLength);

         $httpResponse = stream_get_contents( $fp );
         fclose( $fp );

         return $httpResponse;

      }

      function parseHTTPResponse( $string ) {

         $status = $version = '';
         $headers = array();
         $content = '';
         $str = strtok( $string, "\n" );
         $h = null;
         while ($str !== false) {

            if ( $h && trim($str) === '' ) {

               $h = false;
               continue;

            }

            if ( $h !== false && strpos( $str, ':' ) !== false ) {

               $h = true;
               list( $headername, $headervalue ) = explode( ':', trim( $str ), 2);
               $headername = strtolower( $headername );
               $headervalue = ltrim( $headervalue );
               if ( isset( $headers[ $headername ] ) )

                  $headers[ $headername ] .= ',' . $headervalue;

               else

                  $headers[ $headername ] = $headervalue;

            } else if ( substr( $str, 0, 4 ) == 'HTTP' ) {

               $h = true;
               list( $version, $status ) = explode( ' ', trim( $str ), 2 );
               $headers[ 'HTTPVersion' ] = $version;
               $headers[ 'HTTPStatus' ] = $status;
               list( $headers[ 'HTTPStatusCode' ] ) = explode( ' ', $status );

            }

            if ( $h === false ) {

               $content .= $str . "\n";

            }

            $str = strtok( "\n" );

         }

         $content = $this->decodeContent( $content, $headers[ 'transfer-encoding' ] );

         return array( $headers, trim( $content ) );

      }

      private function decodeContent( $content, $xferenc = 'plain' ) {

         switch ( $xferenc ) {

            case 'chunked':
               return http_chunked_decode( $content );
               break;
            case 'plain':
            default:
               return $content;
               break;

         }

      }

      /**
      * dechunk an http 'transfer-encoding: chunked' message
      *
      * @param string $chunk the encoded message
      * @return string the decoded message.  If $chunk wasn't encoded properly it will be returned unmodified.
      * @author Marques Johansson (http://no2.php.net/manual/en/function.http-chunked-decode.php#89786)
      */
      private function http_chunked_decode($chunk) {

         $pos = 0;
         $len = strlen($chunk);
         $dechunk = null;
         print $pos . "::".$len."\n";
         while ( ($pos < $len)
            && ($chunkLenHex = substr($chunk,$pos, ($newlineAt = strpos($chunk,"\n",$pos+1))-$pos ) ) ) {

            if ( !$this->is_hex($chunkLenHex)) {
               trigger_error('Value is not properly chunk encoded', E_USER_WARNING);
               return $chunk;
            }

            $pos = $newlineAt + 1;
            $chunkLen = hexdec(rtrim($chunkLenHex,"\r\n"));
            $dechunk .= substr($chunk, $pos, $chunkLen);
            $pos = strpos($chunk, "\n", $pos + $chunkLen) + 1;
         }
         return $dechunk;

      }

      /**
      * determine if a string can represent a number in hexadecimal
      *
      * @param string $hex
      * @return boolean true if the string is a hex, otherwise false
      * @author Marques Johansson (http://no2.php.net/manual/en/function.http-chunked-decode.php#89786)
      */
      private function is_hex($hex) {

         // regex is for weenies
         $hex = strtolower( trim( ltrim( $hex, "0" ) ) );
         if ( empty( $hex ) ) $hex = 0;
         $dec = hexdec( $hex );
         return ( $hex == dechex( $dec ) );

      }

      private function savePhoto( $returndata, $albumid = null ) {

         // Parse XML from Photofunia service.
         $xml = simplexml_load_string( $returndata );

         // Parse image types.
         $types = array();
         foreach ( $xml->sizes->item as $item ) {

            $sub = array();
            foreach ( $item as $key=>$value ) {

               $sub[ $key ] = (string) $value;

            }
            $types[ $sub[ 'key' ] ] = $sub;

         }

         // Use print quality if processed.
         $useType = isset( $types[ 'print' ] ) ? $types[ 'print' ] : $types[ 'default' ];

         // Create remote URL for finished image.
         $remoteFileUrl = $this->urlFetchProcessedFile . $xml->path . '/' . $xml->key . $useType[ 'suffix' ] . '.' . $useType[ 'format' ];

         // Fetch file data.
         $filedata = file_get_contents( $remoteFileUrl );

         // Save image and return new image id.
         $newId = StorageUtil::uploadImageString( Login::userid(), $albumid, $filedata, 'image/jpeg', 'Photofunia', 'Photofunia' );

         // Return image object.
         return $newId;

      }

      public function makeTemplateReadyEffectList( $context = 'normal' ) {

         // Fetch list of photofun effects.
         $effects = $context == 'random' ? $this->getRandomEffectList() : $this->getEffectList();

         $effectsOut = array();
         foreach( $effects as $effect ) {

            $effectsOut[] = array(
               'icon' => $this->urlLocalPassThrough . $this->scrambleString( array( $effect[ 'icon' ], 'image/jpeg' ) ),
               'url' => '/photofun/match/' . $effect[ 'id' ]
               );

         }

         return $effectsOut;

      }

      public function scrambleString( $str ) {

         return base64_encode( serialize( $str ) );

      }

      public function createDefaultAlbum() {

         $queryString = "
            SELECT
               aid
            FROM
               bildealbum
            WHERE
               uid=? AND
               namn=? AND
               deleted_at IS NULL
            ";
         $exists = DB::query( $queryString, Login::userid(), $this->defaultAlbumName )->fetchSingle();

         if ( !empty( $exists ) && is_numeric( $exists ) ) {

            return $exists;

         }

         $user = new User( Login::userid() );

         $album = new Album();
         $album->ownerid = Login::userid();
         $album->title = $this->defaultAlbumName;
         $album->access = 0;
         $album->purchaseaccess = false;
         $album->downloadaccess = false;
         $album->created = date( 'Y-m-d H:i:s' );
         $album->views = 0;
         $album->grow_views = 0;
         $album->year = date( "Y" );
         $album->country = isset( $user->country ) ? $user->country : 160;

         if ( $album->save() ) {

            return $album->aid;

         }

         return false;

      }

   }

?>