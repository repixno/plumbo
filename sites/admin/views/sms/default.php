<?php

   /**
    * Admin tools for creating/changing system SMS
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.admin' );
   import( 'sms.send' );
   import( 'validate.phone.cell' );
   model( 'site.sms' );

   class AdminSMS extends AdminPage implements IView {
      
      protected $template = 'sms.list';

      /**
       * List all SMS in db
       *
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      public function Execute() {
         
         $smscollection = new DBSiteSMS();
         $sms = array();
         foreach( $smscollection->collection( array( 'id', 'message', 'price' ), array() )->fetchAllAS( 'DBSiteSMS' ) as $data ) {
            $sms []= array(
               'id'        => $data->id,
               'message'   => $data->message,
               'price'     => $date->price,
            );
         }
         
         $this->sms = $sms;
         
      }
      
      
      /**
       * View SMS with given id
       *
       * @param integer $id
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      public function view( $id = 0 ) {
         
         $this->setTemplate( 'sms.view' );
         
         $dbsitesms = new DBSiteSMS( $id );
         if( $dbsitesms instanceof DBSiteSMS && $dbsitesms->isLoaded() ) {
            
            $sms = array(
               'id'        => $dbsitesms->id,
               'message'   => $dbsitesms->message,
               'price'     => $dbsitesms->price,
            );
            
         } else {
            
            $sms = array(
               'id'        => 0,
               'message'   => '',
               'price'     => 0,
            );
            
         }
         
         $this->sms = $sms;
         
      }
      
      
      /**
       * Save SMS with given id or
       * create a new one with data
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       *
       */
      public function save() {
         
         $id = $_POST['id'];
         $message = $_POST['message'];
         $price = (int) $_POST['price'];
         $replacements = array();
         
         $dbsitesms = new DBSiteSMS( $id );
         
         if( $id == 0 ) {
            
            // If string contains %s then we need to 
            // replace them to get by the sprintf in __()
            if( substr_count( $message, '%s' ) > 0 ) {
               $replacements = array_fill( 0, substr_count( $message, '%s' ), '%s' );
            }
            
            $dbsitesms = new DBSiteSMS();
            $dbsitesms->message = __( $message, $replacements );
            $dbsitesms->price = (int) floor( $price );
            $dbsitesms->save();
            
            relocate( '/sms/view/'.$dbsitesms->id );
            die();
         }
         
         if( $dbsitesms instanceof DBSiteSMS && $dbsitesms->isLoaded() ) {
            
            $dbsitesms->message = $message;
            $dbsitesms->price = (int) floor( $price );
            $dbsitesms->save();
            
            relocate( '/sms/view/'.$dbsitesms->id );
            die();
            
         }
         
      }
      
      
      /**
       * Delete SMS with given id
       *
       * @param integer $id
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       * 
       */
      public function delete( $id = 0 ) {
         
         if( $id > 0 ) {
            
            $dbsitesms = new DBSiteSMS( $id );
            if( $dbsitesms instanceof DBSiteSMS && $dbsitesms->isLoaded() ) {
               
               $dbsitesms->delete();
               relocate( '/sms' );
               die();
            }
            
         }
         
      }
      
      
      /**
       * Input testdata to send in sms
       *
       * @param integer $id
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
       */
      public function test( $id = 0 ) {
         
         if( $id > 0 ) {
            
            
            $this->setTemplate( 'sms.test' );
            $dbsitesms = new DBSiteSMS( $id );
            if( $dbsitesms instanceof DBSiteSMS && $dbsitesms->isLoaded() ) {
               
               $tmp = 0;
               $smsreplacements = array();
               $this->smsreplacements = array();
               $this->smsid = $dbsitesms->id;
               $this->smsmessage = $dbsitesms->message;
               $tmp = substr_count( $dbsitesms->message, '%s' );
               
               for( $i=0; $i<$tmp; $i++ ) {
                  $smsreplacements []= 'To bad I suck at TAL so I have to do this';
               }
               
               $this->smsreplacements = $smsreplacements;
               
            } else {
               
               relocate( '/sms' );
               die();
               
            }
            
         } else {
            
            relocate( '/sms' );
            die();
            
         }
         
         
      }
      
      
      /**
       * Send a test SMS to given user with given params
       * 
       * @author Andreas Färnstrand <andreas.farnstrand@interweb.no> 
       *
       */
      public function send() {
         
         $this->setTemplate( 'sms.send' );
         
         $id =  (int) $_POST['id'];
         $replacements = $_POST['replacement'];
         $tonumber = $_POST['tonumber'];
         $fromnumber = 'Eurofoto';
                  
         if( $id > 0 ) {
            
            $this->smsid = $id;
            $dbsitesms = new DBSiteSMS( $id );
            if( $dbsitesms instanceof DBSiteSMS && $dbsitesms->isLoaded() ) {
               
               $message = $dbsitesms->message;
               if( count( $replacements ) > 0 ) {
                  $message = vsprintf( $message, array_values( $replacements ) );
               }
               
               if( ValidateCellPhone::validate( $tonumber ) ) {
                  
                  $sms = new SMS();
                  if( $sms->send( $tonumber, $message, 0, 0 ) ) {
                     
                     $this->result = __( "SMS sent." );
                     
                  } else {
                     
                     $this->result = __( "Failed to send SMS." );
                     
                  }
                  
               } else {
                  
                  $this->result = __( 'Not a valid number.' );
                  
               }
               
            } else {
               
               $this->result = __( 'Failed to load model of SMS' );
               
            }
            
         } else {
            
            relocate( '/sms' );
            die();
            
         }
         
      }
      
   }

?>