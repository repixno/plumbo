<?PHP
   
   define( 'PAYMENTTYPE_CREDITCARD', 472 );
   define( 'PAYMENTTYPE_INVOICE', 393 );
   define( 'PAYMENTTYPE_STORE', 660 );
   define( 'DELIVERYTYPE_STORE', 7109 );
   define( 'DELIVERYTYPE_STORE_SM', 3612 );

   
   Settings::Set( 'storedelivery', 'drefid', DELIVERYTYPE_STORE );
   Settings::Set( 'storedelivery', 'drefidsm', DELIVERYTYPE_STORE_SM );
   Settings::Set( 'storedelivery', 'storegroup', array(
      'STU-SV'  => array( 'CEWE')
   ));
   
   
   // Eurofoto
   Settings::Set( 'storedelivery', 'CEWE', array(
               
'stores' => array(
// Japan Photo
0  => array( 
'name' => 'Göteborg', 
'id' => 0, 
'address' => 'Norra Hamngatan 40, 411 06 Göteborg', 
'userid'  => 346812,
'shopId' => 201,
),

1  => array( 
'name' => 'Skövde', 
'id' => 1, 
'address' => 'Kyrkogatan 16, 541 30 Skövde',
'userid'  => 346812,
'shopId' => 203,
),
2  => array( 
'name' => 'Stockholm – Götgatan', 
'id' => 2, 
'address' => 'Götgatan 92, 118 62 Stockholm',
'userid'  => 346812,
'shopId' => 205,
),
3  => array( 
'name' => 'Stockholm – Hötorgspassagen', 
'id' => 3, 
'address' => 'Drottninggatan 56, 111 21 Stockholm',
'userid'  => 346812,
'shopId' => 204,
),
4  => array( 
'name' => 'Stockholm – Fleminggatan', 
'id' => 4, 
'address' => 'Fleminggatan 38, 112 33 Stockholm',
'userid'  => 346812,
'shopId' => 206,
),

5  => array( 
'name' => 'Stockholm – Kista', 
'id' => 5, 
'address' => 'Hanstavägen 55F, 16491 Kista',
'userid'  => 346812,
'shopId' => 207, //denne er ikke oppretta
),

6  => array( 
'name' => 'Malmö – Mobilia köpcenter', 
'id' => 6, 
'address' => 'Per Albin Hanssons väg 40, 214 32 Malmö',
'userid'  => 346812,
'shopId' => 208, //denne er ikke oppretta
),

7  => array( 
'name' => 'Malmö', 
'id' => 7, 
'address' => 'Södra Förstadsgatan 5, 211 43 Malmö', 
'userid'  => 346812,
'shopId' => 202,
),

8 => array( 
'name' => 'Uppsala – Gränbystaden', 
'id' => 8, 
'address' => 'Marknadsgatan 1, 754 60 Uppsala',
'userid'  => 346812 ,
'shopId' => 210, //denne er ikke oppretta
),

9=> array( 
'name' => 'Örebro – Marieberg Galleria', 
'id' => 9, 
'address' => 'Säljarevägen 1, 702 36 Örebro',
'userid'  => 346812 ,
'shopId' => 209, //denne er ikke oppretta
),
           
           
         
           
         
         
         
         
         // Add other chains here. chain will be used as optgroup in html
      ),
      'prefid' => PAYMENTTYPE_STORE,
      'paymentmethodwhitelist' => array( PAYMENTTYPE_CREDITCARD, PAYMENTTYPE_INVOICE )  
   ));
   
?>
