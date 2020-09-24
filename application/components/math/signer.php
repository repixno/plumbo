<?PHP
   
   // this package uses core.security
   import( 'core.security' );
   
   /**
    * Signature module
    * 
    * @package components
    * @subpackage math
    * @author Oyvind Selbek <oyvind@selbek.com>
    */
   class Signer {
      
      /**
       * Sign a piece of data using an optional extra key
       *
       * @param string $data The data or publickey to sign
       * @param string $twofactorkey An optional extra key used to sign the data
       * @return string the signature of the data
       */
      static function sign( $data, $twofactorkey = '' ) {
         
         $signkey = SecurityKeys::get( SECURITY_KEY_SEEDKEY );
         return md5( $data . $signkey . $twofactorkey );
         
      }
      
      /**
       * Validate an already created signature
       *
       * @param string $data The data or publickey to validate
       * @param string $signature The signature to validate
       * @param string $twofactorkey An optional extra key used to sign the data
       * @return bool True on success, false if the signature does not match
       */
      static function validate( $data, $signature, $twofactorkey = '' ) {
         
         $signkey = SecurityKeys::get( SECURITY_KEY_SEEDKEY );
         return md5( $data . $signkey . $twofactorkey ) == $signature ? true : false;
         
      }
      
      /**
       * Creates a publickey/data segment along with a matching signature
       *
       * @param string $twofactorkey An optional extra key used to sign the data
       * @return array two array members, index 0: publickey, index 1: signature.
       */
      static function createSignature( $twofactorkey = '' ) {
         
         $data = md5( rand( 0, 2147483647 ) . $twofactorkey );
         return array( $data, Signer::sign( $data, $twofactorkey ) );
         
      }
      
   }
   
?>