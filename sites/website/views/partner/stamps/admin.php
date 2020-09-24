<?PHP
   
   import( 'pages.protected' );
   import( 'website.stamp' );
   
   class StampPartnerAdmin extends ProtectedPage implements IValidatedView {
      
      protected $template = 'partner.stamps.admin';
      
      public function Validate() {
         
         return array(
            "approve"   => array(
               "post"   => array(
                  "stampid"   => VALIDATE_INTEGER,
               )
            ),
            "decline"   => array(
               "post"   => array(
                  "stampid"   => VALIDATE_INTEGER,
                  "reason"    => VALIDATE_STRING,
               )
            )
         );
         
      }
      
      public function Execute() {
         
         $this->jobs = Stamp::enumNotYetApproved();
         
      }
      
      
      public function approve() {
         
         $this->setTemplate( '' );
         $stampid = $_POST['stampid'];
         
         if( $stampid > 0 ) {
            
            $stamp = new Stamp( $stampid );
            $stamp->approved = date( 'Y-m-d H:i:s' );
            $stamp->processedby = Login::userid();
            $stamp->save();
            
            echo json_encode( array( 'result' => true ) );
            die();
            
            
         }

          echo json_encode( array( 'result' => false ) );
          die();
         
      }
      
      
      public function decline() {
         
         $this->setTemplate( '' );

         $stampid = $_POST['stampid'];
         $reason = $_POST['reason'];
         
         if( $stampid > 0 && strlen( $reason ) > 0 ) {
            
            $stamp = new Stamp( $stampid );
            $stamp->declined = date( 'Y-m-d H:i:s' );
            $stamp->declinereason = $reason;
            $stamp->processedby = Login::userid();
            $stamp->save();
            
            echo json_encode( array( 'result' => true ) );
            die();
         }
         
         echo json_encode( array( 'result' => false ) );
         die();
         
      }
      
   }
   
?>