<?PHP
   
   import( 'pages.admin' );

   class Betatesters extends AdminPage implements IView {
      
      public function CSV() {
         
         $this->setTemplate( false );
         header( 'content-type: text/plain' );
         
         echo "EMAIL;UID;STARTED;LASTLOGIN\n";
         
         foreach( DB::query( 'SELECT uid, started, lastlogin FROM site_betatesters ORDER BY lastlogin DESC' )->fetchAll() as $row ) {
            
            list( $userid, $started, $lastlogin ) = $row;
            $user = new User( $userid );
            echo sprintf( "%s;%d;%s;%s\n", $user->email, $userid, $started, $lastlogin );
            
         }
         
      }
      
      
   }
   
?>