<?php

   model( 'reedfoto.correction' );
   Import( 'reedfoto.page' );

   class RFCorrection extends DBRFCorrection implements ModelCaching {

      static function enum( $userid ) {

         $corrections = new RFCorrection();

         $ret = array();
         foreach ( $corrections->collection( array( 'id' ), array( 'userid' => $userid ) )->fetchAllAs( 'RFCorrection' ) as $correction ) {

            $ret[] = $correction;

         }

         return $ret;

      }

      public function asArray() {

         $ret = array(
            'id' => $this->id,
            'userid' => $this->userid,
            'title' => $this->title,
            'comment' => $this->comment,
            'state' => $this->state,
         );

         return $ret;

      }
      
      public function delete() {

         $pages = new RFPage();

         foreach ( $pages->collection( array( 'id' ), array( 'correctionid' => $this->id ) )->fetchAllAs( 'RFPage' ) as $page ) {
            
            $page->delete();

         }
         
         parent::delete();
         
         return true;
         
      }

   }

?>