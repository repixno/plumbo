<?php

   import( 'website.paymenttype' );
   
   Dispatcher::extendView( 'content.textentity' );
   
   class PaymentTypeEditor extends TextEntityEditor {
      
      protected $objectclass = 'PaymentType';
      
       protected function editFields( Array $record, DBTextEntity $object ) {
         
         $record['ispaymenttype'] = $object->type == 'paymenttype' ? true : false;
         $record['refid'] = $object->refid;
         return $record;
         
      }
      
   }


?>