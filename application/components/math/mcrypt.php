<?PHP
   
   import( 'core.security' );
   
   class mCrypt {
      
      static function Encrypt( $string, $twofactorkey = '' ) {
         
         if( !extension_loaded( 'mcrypt' ) ) {
            throw new CriticalException( 'Encryption requires the mCrypt module to be loaded!', 400 );
         }
         
         $encryptionkey = SecurityKeys::get( SecurityKeys::COMMON_ROOT_KEY );
         if( empty( $encryptionkey ) ) {
            throw new CriticalException( 'No valid Common Root Key defined!', 401 );
         }
         $encryptionkey .= $twofactorkey;
         
         // find the size of iv for the chosen encryption algorithm
         $size = mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CFB );
         $iv = mcrypt_create_iv( $size, MCRYPT_RAND );
         
         // encrypt the string using the given encryptionkey.
         $encrypted = $iv.mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $encryptionkey ), $string, MCRYPT_MODE_CFB, $iv );
         
         // base64 encode and return the data
         return base64_encode( $encrypted );
         
      }
      
      static function Decrypt( $string, $twofactorkey = '' ) {
         
         if( !extension_loaded( 'mcrypt' ) ) {
            throw new CriticalException( 'Encryption requires the mCrypt module to be loaded!', 400 );
         }
         
         $encryptionkey = SecurityKeys::get( SecurityKeys::COMMON_ROOT_KEY );
         if( empty( $encryptionkey ) ) {
            throw new CriticalException( 'No valid Common Root Key defined!', 401 );
         }
         $encryptionkey .= $twofactorkey;
         
         // decode the input string
         $string = base64_decode( $string );
         
         // find the size of iv for the chosen encryption algorithm
         $size = mcrypt_get_iv_size( MCRYPT_RIJNDAEL_256, MCRYPT_MODE_CFB );
         
         // make sure we have some valid data to decrypt
         if( strlen( $string ) < $size + 1 ) return '';
         
         // first, find the two parts of the string
         $iv = substr( $string, 0, $size );
         $encrypted = substr( $string, $size );

         // secondly, decrypt the encrypted output using the static key
         $plaintext = mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $encryptionkey ), $encrypted, MCRYPT_MODE_CFB, $iv );
         
         // strip padded string data and return it
         return str_replace( "\x0", '', $plaintext );
         
      }
      
      static function encode_get( $string ) {  
         $key = md5( SecurityKeys::get( SECURITY_KEY_SEEDKEY ) );
         $time = strtotime(date('Y-m-d'));
         $string = $string . "%" . $time;
         $hash = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $key, $string, MCRYPT_MODE_CBC, md5($key)));
         return strtr($hash, '+/=', '-_,');
      }
      
      static function decode_get($string) {     
         $string = strtr($string, '-_,', '+/=');
         $key = md5( SecurityKeys::get( SECURITY_KEY_SEEDKEY ) );
         $hash = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $key, base64_decode($string), MCRYPT_MODE_CBC, md5($key)), "\0");
         $string = explode('%', $hash);
         
         if($string[1] + strtotime('+1 day') > time()){
            $info = pathinfo($string[0]);
            $file_name =  basename($string[0],'.'.$info['extension']);
            
            return array(
               'bid'=>$file_name,
               'filename'=>$string[0]
            );
         }
      }
      
   }
   
?>