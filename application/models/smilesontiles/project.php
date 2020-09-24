<?php

   /**
    * Class for Smilesontiles project
    * 
    * @author Tor Inge <tor.inge@eurofoto.no>
    *
    **/

   class DBSmilesProject extends Model {
    
      
      static $table = 'smilesontiles';
      static $basename = 'project';

      
      static $fields = array(
            'id'=> array(
                  'primary'   => true,
                  'type'      => DB_TYPE_INTEGER,
                  'size'      => 11,
                  'default'   => 0,
                ),
                'orderid'       => array(
                    'type'      => DB_TYPE_INTEGER,
                    'size'      => 11,
                    'null'      => true,
                ),
                'data'  => array(
                    'type'      => DB_TYPE_STRING,
                    'size'      => 16581375,
                    'default'   => '',
                ),
                'clipsize'  => array(
                    'type'      => DB_TYPE_STRING,
                    'size'      => 16581375,
                    'default'   => '',
                ),
                'created' => array(
                    'type'      => DB_TYPE_DATETIME,
                    'null'      => true,
                    'default'   => null,
                ),
                'processed' => array(
                    'type'      => DB_TYPE_DATETIME,
                    'null'      => true,
                    'default'   => null,
                ),
                'laminate' => array(
                    'type'      => DB_TYPE_BOOLEAN,
                    'null'      => true,
                    'default'   => 'f',
                ),
                'thumb'    => array(
                     'type'   => DB_TYPE_STRING,
                     'size'      => 16581375,
                     'default'   => '',
                )
         
      );
      
   }


?>