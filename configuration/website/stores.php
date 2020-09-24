<?PHP
   
   define( 'PAYMENTTYPE_CREDITCARD', 472 );
   define( 'PAYMENTTYPE_INVOICE', 393 );
   define( 'PAYMENTTYPE_STORE', 660 );
   define( 'DELIVERYTYPE_STORE', 7109 );
   define( 'DELIVERYTYPE_STORE_SM', 3612 );
   define( 'DELIVERYTYPE_STORE_NM', 4289 );

   Settings::Set( 'storedelivery', 'drefid', DELIVERYTYPE_STORE );
   Settings::Set( 'storedelivery', 'drefidsm', DELIVERYTYPE_STORE_SM );
   Settings::Set( 'storedelivery', 'drefidnm', DELIVERYTYPE_STORE_NM );
   
   Settings::Set( 'storedelivery', 'storegroup', array(
      'EF-997' => array( 'Japan Photo'),
      'TK-001' => array( 'Japan Photo' ),
      'FK-001' => array( 'Japan Photo' ),
      'FC-001' => array( 'Fotoclick' ),
      'UP-001' => array( 'Japan Photo'),
      'DM-001' => array( 'Japan Photo'),
      'SM-001' => array( 'Japan Photo'),
      'JP-SE'  => array( 'CEWE'),
      'NO-MERK' => array( 'Japan Photo'),
       'FOTONO' => array( 'Fotono' ),
         'FOTOPIX' => array( 'Fotopix' )
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
   
   // Fotono
   Settings::Set( 'storedelivery', 'Fotono', array(
               
               'stores' => array(
                
                
              //  hentibutikk_barcode@fotono.no= 1341644
                // Fotono
                  30  => array( 
                     'name' => 'Fotono Barcode ', 
                     'id' => 30, 
                     'address' => 'Dronning Eufemias gate 12, 0191 Oslo', 
                     'userid'  => 1341644 ,
                     'shopId' => 1341644 ,
                  ),
                  
                  
                  //hentibutikk_repix@fotono.no =1341645
                 31 => array(
                      'name' => "Repix Reed Senter ",
                     'id'   => 31,
                     'address' => "Reed Senter, 6827 Breim",
                     'userid'  => 1341645,
                     'shopId' => 1341645,
                  )
                 
                  
                  
                  ),
               'prefid' => PAYMENTTYPE_STORE,
               'paymentmethodwhitelist' => array( PAYMENTTYPE_CREDITCARD, PAYMENTTYPE_INVOICE )
               
   ));
                  
                  
                   // hentibutikk_repix@repix.no =  1341651
   Settings::Set( 'storedelivery', 'Fotopix', array(
               
               'stores' => array(
                
                
                  
                  //butikk_repix@fotono.no =1341645
                 27 => array(
                     'name' => "Repix Reed Senter ",
                     'id'   => 27,
                     'address' => "Reed Senter, 6827 Breim",
                     'userid'  =>  1341651,
                     'shopId' =>  1341651,
                  )
                 
                  
                  
                  ),
                 'prefid' => PAYMENTTYPE_STORE,
               'paymentmethodwhitelist' => array( PAYMENTTYPE_CREDITCARD, PAYMENTTYPE_INVOICE )
               
   ));
                  
                  
                  
                  
   
   // Eurofoto
   Settings::Set( 'storedelivery', 'Japan Photo', array(
               
               'stores' => array(
                 //eurofoto
                 19 => array(
                     'name' => "Eurofoto",
                     'id'   => 19,
                     'address' => "Reed Senter, 6827 Breim",
                     'userid'  => 800223,
                     'shopId' => 10000,
                  ),
                 
                  // Japan Photo
                  0  => array( 
                     'name' => 'Japan Photo Byporten', 
                     'id' => 0, 
                     'address' => 'Jernbanetorget 6, 0154 Oslo', 
                     'userid'  => 800097,
                     'shopId' => 11878,
                  ),
                  1  => array( 
                     'name' => 'Japan Photo Bogstadveien', 
                     'id' => 1, 
                     'address' => 'Bogstadveien 23B, 0355 Oslo', 
                     'userid'  => 800098,
                     'shopId' => 70129,
                  ),
                  2  => array( 
                     'name' => 'Japan Photo Sandvika', 
                     'id' => 2, 
                     'address' => 'Brodtkorbsgate 7, 1338 Sandvika',
                     'userid'  => 800102,
                     'shopId' => 77331,
                  ),
                  3  => array( 
                     'name' => 'Japan Photo Ski', 
                     'id' => 3, 
                     'address' => 'Torgveien 4, (Vis à vis Ski Storsenter), 1400 Ski',
                     'userid'  => 800104,
                     'shopId' => 60310,
                  ),
                  5  => array( 
                     'name' => 'Japan Photo Strømmen', 
                     'id' => 5, 
                     'address' => 'Stasjons veien 6, 2010 Strømmen',
                     'userid'  => 800111,
                     'shopId' => 70265,
                  ),
                  6  => array( 
                     'name' => 'Japan Photo Drammen', 
                     'id' => 6, 
                     'address' => 'Nedre Storgate 10, 3017 Drammen',
                     'userid'  => 800112,
                     'shopId' => 70174,
                  ),
                  7  => array( 
                     'chain' => 'Japan Photo', 
                     'name' => 'Japan Photo Moss', 
                     'id' => 7, 
                     'address' => 'Dronningensgt. 16, 1531 Moss',
                     'userid'  => 800113,
                     'shopId' => 75646,
                  ), 
                  8  => array( 
                     'name' => 'Japan Photo Fredrikstad', 
                     'id' => 8, 
                     'address' => 'Farmannsgate 1, 1607 Fredrikstad',
                     'userid'  => 800114,
                     'shopId' => 70232,
                  ),
                  9  => array( 
                     'name' => 'Japan Photo Porsgrunn', 
                     'id' => 9, 
                     'address' => 'Storgata 125, 3915 Porsgrunn',
                     'userid'  => 800116,
                     'shopId' => 10044,
                  ),
                  10 => array( 
                     'name' => 'Japan Photo Kristiansand', 
                     'id' => 10, 
                     'address' => 'Henrik Wergelandsgt 11, 4612 Kristiansand',
                     'userid'  => 800117,
                     'shopId' => 70356,
                  ),
                  11 => array( 
                     'name' => 'Japan Photo Stavanger', 
                     'id' => 11, 
                     'address' => 'Søregaten 21, 4006 Stavanger',
                     'userid'  => 800118,
                     'shopId' => 70345,
                  ),
                  12 => array( 
                     'name' => 'Japan Photo Sandnes', 
                     'id' => 12, 
                     'address' => 'Langgata 15, 4306 Sandnes',
                     'userid'  => 800215,
                     'shopId' => 70787,
                  ),
                  13 => array( 
                     'name' => 'Japan Photo Haugesund', 
                     'id' => 13, 
                     'address' => 'Haraldsgaten 94, Inngang Strandgaten, 5527 Haugesund',
                     'userid'  => 800216,
                     'shopId' => 700373,
                  ),
                  14 => array( 
                     'name' => 'Japan Photo Bergen', 
                     'id' => 14, 
                     'address' => 'Olav Kyrres gate 41, 5015 Bergen',
                     'userid'  => 800217,
                     'shopId' => 71040,
                     
                  ),
                  15 => array( 
                     'name' => 'Japan Photo Trondheim', 
                     'id' => 15, 
                     'address' => 'Munkegaten 35, 7011 Trondheim',
                     'userid'  => 800218,
                     'shopId' => 70301,
                     ),
                  16 => array( 
                     'name' => 'Japan Photo Tønsberg', 
                     'id' => 16, 
                     'address' => 'Fayesgate 5, 3110 Tønsberg',
                     'userid'  => 800220,
                     'shopId' => 752398,
                  ),
                  17 => array( 
                     'name' => 'Japan Photo Tveita', 
                     'id' => 17, 
                     'address' => 'Tvetenveien 150, 0671 Oslo',
                     'userid'  => 800221,
                     'shopId' => 753866,
                  ),
                 
                  26 => array( 
                     'name' => 'Japan Photo Tromsø', 
                     'id' => 26, 
                     'address' => 'Jekta Storsenter Karlsøyvegen 12, 9015 Tromsø',
                     'userid'  => 1300709,
                     'shopId' => 755409,
                  ),
                  
                  18 => array( 
                     'name' => 'Japan Photo Lambertseter', 
                     'id' => 18, 
                     'address' => 'Cecilie Thoresens vei 17, 1153 Oslo',
                     'userid'  => 800222,
                     'shopId' => 754560,
                  ),
                  
                  20 => array(
                     'name' => "Japan Photo Lillehammer",
                     'id'   => 20,
                     'address' => "Storgata 61, 2609 Lillehammer",
                     'userid'  => 800223,
                     'shopId' => 754700,
                  ),
                  21 => array(
                     'name' => "Japan Photo Kvadrat",
                     'id'   => 21,
                     'address' => "Gamle Stokkav. 1, L-85, 4313 Sandnes",
                     'userid'  => 990230,
                     'shopId' => 754442,
                  ),
                  23 => array(
                     'name' => "Japan Photo City Lade",
                     'id'   => 23,
                     'address' => "Haakon VIIs gate 9, 7041 Trondheim",
                     'userid'  => 990230,
                     'shopId' => 755232,
                  ),
                  
                  
                  
                  
                  24 => array(
                     'name' => "Japan Photo Skien",
                     'id'   => 24,
                     'address' => "Liegata 10, 3717 Skien",
                     'userid'  => 1121109,
                     'shopId' => 755266,
                  ),
                  
                  
                  
                  25 => array(
                     'name' => "Japan Photo Jessheim",
                     'id'   => 25,
                     'address' => "Storgata 6, 2050 Jessheim",
                     'userid'  => 1295830,
                     'shopId' => 755408,
                  )
                  
                  
                  
                  
                  // Add other chains here. chain will be used as optgroup in html
               ),
               'prefid' => PAYMENTTYPE_STORE,
               'paymentmethodwhitelist' => array( PAYMENTTYPE_CREDITCARD, PAYMENTTYPE_INVOICE )
               
   ));
   
   
   // Fotoclick
   Settings::Set( 'storedelivery', 'Fotoclick', array(
               
               'stores' => array(
                 0  => array( 
                     'name' => 'Foto Almenning', 
                     'id' => 0, 
                     'address' => ' Nordstrandsvegen 13, 6823 Sandane', 
                  ),
                  1  => array( 
                     'name' => 'Herheim Foto', 
                     'id' => 1, 
                     'address' => 'Vangsgt. 46, 5702 Voss', 
                  ),
                  2  => array( 
                     'name' => 'Ar Foto AS', 
                     'id' => 2,
                     'address' => 'Kinogata 2, 2850 LENA',
                  ),
                  3  => array( 
                     'name' => 'Aril Foto', 
                     'id' => 3,
                     'address' => 'Storgt. 14, 1890 Rakkestad',
                  ),
                  5  => array( 
                     'name' => 'Skredegård Foto AS', 
                     'id' => 5,
                     'address' => 'Sentrumsvn. 104, pb. 144 , 3551 Gol',
                  ),
                  6  => array( 
                     'name' => 'Foto Hasselberg', 
                     'id' => 6,
                     'address' => 'Torget 14, 8300 Svolvær',
                  ),
                  7  => array( 
                     'name' => 'Joar Foto', 
                     'id' => 7,
                     'address' => 'Storgt. 4, 1440 Drøbak',
                  ),
                  8  => array( 
                     'name' => 'Knips AS', 
                     'id' => 8,
                     'address' => 'Kongens gate 37, 7713 Steinkjer',
                  ),
                  9  => array( 
                     'name' => 'Galleri Galåen', 
                     'id' => 9,
                     'address' => 'Kjerkgt. 8, 7374 Røros',
                  ),
                  10  => array( 
                     'name' => 'Ås Foto', 
                     'id' => 10,
                     'address' => 'Moerveien 6, 1431 Ås',
                  ),
                  11  => array( 
                     'name' => 'Fotolabben Moss', 
                     'id' => 11,
                     'address' => 'Carlbergveien 2, 1526 Moss',
                  ),
                  12  => array( 
                     'name' => 'Fotograf P.P. Lyshol', 
                     'id' => 12,
                     'address' => 'Aure, 6230 Sykkylven',
                  ),
                  13  => array( 
                     'name' => 'Stathelle Foto', 
                     'id' => 13,
                     'address' => 'Gangveien 10, 3960 Stathelle',
                  ),
                
                  
                  // Add other chains here. chain will be used as optgroup in html
               ),
               'prefid' => PAYMENTTYPE_STORE,
               'paymentmethodwhitelist' => array( PAYMENTTYPE_CREDITCARD )
   ));
   
?>
