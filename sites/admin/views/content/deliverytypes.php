<?php

   import( 'website.deliverytype' );
   
   Dispatcher::extendView( 'content.textentity' );
   
   class DeliveryTypeEditor extends TextEntityEditor {
      
      protected $objectclass = 'DeliveryType';
      
       protected function editFields( Array $record, DBTextEntity $object ) {
         
         $record['isdeliverytype'] = $object->type == 'deliverytype' ? true : false;
         $record['refid'] = $object->refid;
         return $record;
         
      }
      
   }


?>