<?PHP

   import( 'reedfoto.pages.json' );
   import( 'reedfoto.correction' );
   
   class ReedFotoApiUserCorrectionState extends JSONPage implements IValidatedView  {
      
      /**
       * Validates the data going into this API
       *
       * @return array of validation rules.
       */
      public function Validate() {
      
         return array(
            'execute' => array(
               'post' => array(
                  'correctionid' => VALIDATE_INTEGER,
                  'state' => VALIDATE_STRING,
               ),
               'fields' => array(
                  'correctionid' => VALIDATE_INTEGER,
                  'state' => VALIDATE_STRING,
               ),
            )
         );
      
      }
      
      /*
      * Set correction state
      *
      * @api-name user.correction.state
      * @api-javascript yes
      * @api-post-optional correctionid Integer ID of the correction
      * @api-param-optional correctionid Integer ID of the correction
      * @api-post-optional state String State to set. 'setup' 'ready' 'opened' 'approved'
      * @api-param-optional state String State to set. 'setup' 'ready' 'opened' 'approved'
      * @api-result result Boolean true/false
      * @api-result message String Describes the result of the operation in US English
      */           
      public function Execute( $correctionid = 0, $state = 'setup' ) {
         
         $state = $_POST['state'] ? $_POST['state'] : $state;
         $correctionid = $_POST['correctionid'] ? $_POST['correctionid'] : $correctionid;
        
         $correctionemail = Settings::Get( 'reedfoto', 'correctionemail', 'post@reedfoto.no' );
         
         try {
   
            $correction = new RFCorrection( (int)$correctionid );
   
            switch ( $state ) {
               case 'setup':
                  $correction->setup = date( 'Y-m-d H:i:s' );
                  $correction->setupby = Login::userid();
                  
                  break;
               case 'ready':
                  $correction->ready = date( 'Y-m-d H:i:s' );
                  $correction->readyby = Login::userid();
                  
                  break;
               case 'opened':
                  $correction->opened = date( 'Y-m-d H:i:s' );
                  $correction->openedby = Login::userid();
                  
                  break;
               case 'approved':
                  $correction->approved = date( 'Y-m-d H:i:s' );
                  $correction->approvedby = Login::userid();
                  
                  mail( $correctionemail, 'En korrigering er godkjent', sprintf( 'http://corrections.reedfoto.no/admin/correction/%s', $correctionid ) );
                  
                  break;
               default:
                  $correction->setup = date( 'Y-m-d H:i:s' );
                  $correction->setupby = Login::userid();
                  
                  $state = 'setup';
                  
                  break;
            }
            
            $correction->state = $state;
            
            $correction->save(); 
            
            $this->result = true;
            $this->message = 'OK';
            
         } catch ( Exception $e ) {
            
            $this->result = false;
            $this->message = sprintf( 'Exception %s', $e );
            
         }
      }
   }

?>