<?php


   class SetupRequest {
     
       public $Amount;
       public $CurrencyCode;
       public $CustomerEmail;
       public $CustomerPhoneNumber;
       public $CustomerNumber;
       public $Description;
       public $Language;
       public $OrderDescription;
       public $OrderNumber;
       public $PanHash;
       public $RecurringExpiryDate;
       public $RecurringFrequency;
       public $RecurringType;
       public $RedirectUrl;
       public $ServiceType;
       public $SessionId;
       public $TransactionId;
       
       function SetupRequest( $parameters ) {
          
          
          foreach( $parameters as $key => $value ) {
             
             $this->$key = $value;
             
          }
          
       }
     
   }


?>
