<?php

   /**
    * Class for production conversions
    * 
    * @author Adele Skjerdal Storøy
    *
    */

    /*
     CREATE TABLE ecwid (
          id serial,
          ecwidid integer,
          downloaded timestamp with time zone,
          toproduction timestamp with time zone,
          sendt timestamp with time zone,
          fakturert timestamp with time zone,
          payment integer );
     */
   class DBEcwid extends Model {
      
      static $table = 'ecwid';
      
      static $fields = array(
            'id'        => array(
               'primary'   => true,
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => false,
            ),
            'ecwidid'  => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => false,
            ),
            'downloaded'  => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null,
            ),
            'toproduction'  => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null,         
            ),
            'sendt'  => array(
               'type'      => DB_TYPE_DATETIME,
               'null'      => true,
               'default'   => null,         
            ),
            
            'payment'  => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 1024,
               'null'      => true,
            ),
            
            
            'fakturert'    => array(
               'type'      => DB_TYPE_STRING,
               'size'      => 255,
               'null'      => true,   
               'default'   => null,
            ),
            
            'eforderid' => array(
               'type'      => DB_TYPE_INTEGER,
               'size'      => 11,
               'null'      => false,
            ),
         );

   }