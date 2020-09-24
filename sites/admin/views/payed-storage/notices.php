<?php

   /**
    * 
    * Display all planed notices
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'pages.admin' );
   model( 'subscription.batch' );

   class AdminPayedStorageNotices extends AdminPage implements IView {
      
      protected $template = 'payed-storage.notices';
      
      public function Execute() {
         
         $noticessetup = array();
         
         $notices = new DBSubscriptionMailBatch();
         foreach( $notices->collection( 'id', array(), 'id DESC'  )->fetchAllAs('DBSubscriptionMailBatch') as $batchjob ) {
            $data = $batchjob->asArray();
            if( strlen( $batchjob->recipients_processing ) == 0 || ( strlen( $batchjob->recipients_processing ) > 0 && strlen( $batchjob->recipients_processed ) > 0 && strlen( $batchjob->started_sending ) == 0 ) ) {
               $data['deletable'] = true;
            } else {
               $data['deletable'] = false;
            }
            
            $data['send_date'] = date( 'Y-m-d', strtotime( $data['send_date'] ) );
            $data['expire_date'] = date( 'Y-m-d', strtotime( $data['expire_date'] ) );
			   $noticessetup []= $data;
			 
			}
			
			$this->batchjobs = $noticessetup;
         
      }
      
   }


?>