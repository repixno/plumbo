<?php

   import( 'core.settings' );
   
   // Live gateway
   Settings::Set( 'sms', 'gateway', 1933 );
   
   // Gateway for international testing.
   // Works on all numbers
   //Settings::Set( 'sms', 'gateway', 99700999 );
   
   Settings::Set( 'sms', 'fromnumber', 'Eurofoto' );
   Settings::Set( 'sms', 'password', 'eurosms' );
   Settings::Set( 'sms', 'customerid', 26 );
   
   // This is the live url
   Settings::Set( 'sms', 'serviceurl', 'https://smsgw.intelesms.no/pushsms/out.aspx?gateway=%d&customer_id=%d&pass=%s&type=00&to_number=%s&from_number=%s&price_class=%d&msgid=%d&msg=%s' );
   
   // This is used for testing purposes. Does not actually send SMS but returns errors and statuses.
   //Settings::Set( 'sms', 'serviceurl', 'https://test.smsgw.intelesms.no/pushsms/out.aspx?gateway=%d&customer_id=%d&pass=%s&type=00&to_number=%s&from_number=%s&price_class=%d&msgid=%d&msg=%s' );
   
   // Greetings for different portals
   Settings::Set( 'sms', 'sender', array(
      'EF-997' => 'Eurofoto',
      'MB-997' => 'Eurofoto',
      'AM-997' => 'Aftenposten Fotoalbum',
      'VG-997' => 'VG Nett Fotoalbum',
      'EF-VG' => 'VG Nett Fotoalbum',
      'SN-997' => 'SOL Foto',
      'IF-NO' => 'IF Mine album',
      'NSK-001' => 'Norsk Studentkort Fotoalbum',
      'AP-001' => 'A-Pressen Fotoalbum',
   ) );
   

?>