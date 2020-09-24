<?php

   /**
    * User credited articles
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'core.model' );

   class DBCredit extends Model {
      
      static $table = 'tilgode';

      static $fields = array(
         'id'       => array(
            'primary'   => true,
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => false,
            'default'   => 0
         ),
         'uid'    => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => false,
            'alias'     => 'userid',
         ),
         'artikkelnr'=> array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'null'      => false,
            'alias'     => 'refid',
         ),
         'antall' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'alias'     => 'quantity', 
         ),
         'tekst'  => array(
            'type'   => DB_TYPE_STRING,
            'size'   => 255,
            'alias'  => 'text',
         ),
         'tidspunkt' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
            'alias'   => 'time',
         ),
      );
      
      
      public function getText() {
         
         return $this->tekst;
         
      }
      
      public function setText( $text ) {
         
         $this->tekst = $text ;
         
      }
      
   }


?>