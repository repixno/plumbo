<?

   library( 'jcryption.jcryption' );
   
   import( 'pages.json' );
   
   class ApiFriendsImportGenerateKeyPair extends JSONPage implements IView {

      function Execute() {
         
         $keylength = 64;
         
         $jcryption = new jCryption();
         
         $keys = $jcryption->generateKeypair($keylength);
         
         $_SESSION['key_e'] = array('int' => $keys['e'], 'hex' => $jcryption->dec2string($keys['e'],16));
         $_SESSION['key_d'] = array('int' => $keys['d'], 'hex' => $jcryption->dec2string($keys['d'],16));
         $_SESSION['key_n'] = array('int' => $keys['n'], 'hex' => $jcryption->dec2string($keys['n'],16));
         
         $this->e = $_SESSION['key_e']['hex'];
         $this->n = $_SESSION['key_n']['hex'];
         $this->maxdigits = intval( $keylength * 2/16 + 3 );
         
         $this->result = true;
         $this->message= 'OK';
         
      }
      
   }
   
?>