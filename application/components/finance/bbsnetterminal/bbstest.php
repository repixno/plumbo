<?PHP
   
   include "bbsnetterminal.php";
   
   $merchantid = '317810';
   $token = '7i-BeE3=';
   $mode = BBSNETTERMINAL_MODE_TEST;
   
   $bbsclient = new BBSNetTerminal( $merchantid, $token, $mode );
   
   if( isset( $_GET['BBSePay_transaction'] ) ) {
      
      $result = $bbsclient->ProcessSetup( $_GET['BBSePay_transaction'] );
      list( $transactionid, $source, $code, $text, $cardtype ) = $result;
      
      switch( $cardtype ) {
         case 3: $cardissuer = 'VISA'; break;
         case 4: $cardissuer = 'MasterCard'; break;
         default: $cardissuer= 'Unknown'; break;
      }
      
      switch( $code ) {
      
         case BBSNETTERMINAL_TRANSACTION_OK:
            
            echo "OK, All is well, proceeding with authorization...";
            
            
            list( $transactionid, $source, $code, $text ) = $bbsclient->auth( $transaction->objectid );
            
            switch( $code ) {
               
               case BBSNETTERMINAL_TRANSACTION_OK:
                  
                  echo "OK, All is well!";
                  break;
                  
               default:
                  
                  echo "Massive failure to authorize!";      
                  break;
                  
            }
            
            break;
            
         case BBSNETTERMINAL_TRANSACTION_CANCELLED:
            
            echo "CANCELLED!";
            
            break;
            
         default:
            
            echo "FAIL!";
            
            break;
            
      }
      
   } else {
      
      echo $amount = 100.00; // angitt i kroner
      
      echo "<br />\n";
      
      // du bør ha en tabell i stedet med f.eks. en autoincrement int :)
      echo $transactionid = sprintf( '%04x%04x-%04x-%04x-%04x',
         mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
         mt_rand( 0, 0x0fff ) | 0x4000,
         mt_rand( 0, 0x3fff ) | 0x8000
      );
      
      $action = $bbsclient->getAction();
      $transaction = $bbsclient->kickoff( array(
         'currencyCode'        => 'NOK',
         'transactionId'       => $transactionid,
         'amount'              => round( $amount, 2 ) * 100, // angitt i øre
         'orderNumber'         => 'orderno',
         'orderDescription'    => 'elementname',
         'customerEmail'       => 'customer-email',
         'customerPhoneNumber' => 'customer-phonecell',
         'description'         => sprintf( 'Kickoff #%d', 'orderno' ),
         'redirectUrl'         => sprintf( 'http://%s%s', $_SERVER['HTTP_HOST'], $_SERVER['PHP_SELF'] ),
         'language'            => 'no_NO', // i18n::$language, (no_NO, sv_SE, en_GB)
         'sessionId'           => session_id(),
      ) );
      
      echo <<<HTML
      <form method="post" action="$action">
         $transaction
         <input type="submit" value="Pay up!" />
      </form>
HTML;

   }
   
?>