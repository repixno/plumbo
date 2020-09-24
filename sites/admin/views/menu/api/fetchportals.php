<?php

import( 'website.menu' );
import( 'pages.json' );
model( 'site.portal' );

class APIFetchPortals extends JSONPage implements IValidatedView {

   public function Validate() {

      return array(
         'execute' => array()
      );

   }

   public function Execute() {

      $this->setTemplate();

      // Elements to put at start of returned list.
      $staticListElements = array(
         array(
            'id' => 'all',
            'title' => '- ' . __( 'All' )
            )
         );

      // Fetch list of all protals in db.
      $portals = new DBPortal();
      
      $siteid = Session::get( 'adminsiteid', 1 );

   
      
      $ret = array();
      if( $siteid == 1 ){
         $active_portals = array('',  'FK-001', 'EF-VG', 'RF-001', 'UP-001','VP-001', 'BP-001', 'UP-DK', 'ST-001', 'FOTONO', 'FOTOPIX');
      //'PA-EF',
                                
      }
      
      
 
      else if( $siteid == 3){
         $active_portals = array('');
      }
      else if( $siteid == 4 ){
         
        $active_portals = array( 'DM-001' ,'DM-002', 'SPL-001', 'SL-001', 'UL-001' );

             

      }
      else if( $siteid == 5 ){
          $active_portals = array( 'UP-001' , 'VP-001', 'UP-DK' );
      }

      else if( $siteid == 7 ){
          $active_portals = array( 'STU-SV' );
          
            }
      else if( $siteid == 8 ){
         $active_portals = array('NO-MERK');
         
      }
      
      
      else if( $siteid == 9 ){
         $active_portals = array('SKA-001');
         
      }
      
       else if( $siteid == 10){
         $active_portals = array('RP-001');
         
      }
      


      
      foreach ( $portals->collection( array( 'code', 'title' ) )->fetchAll() as $portal ) {
         
         if( in_array( $portal[0], $active_portals ) ){
            
         if( $portal[ 0 ] == '' ){
            $ret[] = array(
               'id' => $portal[ 0 ],
               'title' => "Eurofoto"
            );

         }
         else{
            $ret[] = array(
               'id' => $portal[ 0 ],
               'title' => $portal[ 1 ]
            );
         }

            
         }

      }

      // Set return values.
      $this->portals = array_merge( $staticListElements, $ret );
      $this->result = true;
      $this->message = 'OK';
      
   }

}

?>
