<?php

   if( Dispatcher::getPortal() == 'VP-001' ){
      Settings::Set( 'finance', 'netsparameters', array(
         'testmerchantid'   => '344892',
         'testtoken'        => '2Bq!p-F8',
         'testwsdl'         => 'https://epayment-test.bbs.no/Netaxept.svc?wsdl',
         'testterminal'     => 'https://epayment-test.bbs.no/terminal/default.aspx',
         'redirecturl'      => "http://" . $_SERVER["HTTP_HOST"] ."/checkout/execute",
         'merchantid'       => '344892',
         'token'            => 's!9D+Lt8',
         'terminal'         => 'https://epayment.bbs.no/terminal/default.aspx',
         'wsdl'             => 'https://epayment.bbs.no/Netaxept.svc?wsdl',
         'dnbkonto'         => '0000.00.00000',
         'testdnbkonto'     => '0000.00.00000',
      ) );
   }
   else if( Dispatcher::getPortal() == 'STU-SV' ){
      Settings::Set( 'finance', 'netsparameters', array(
         'testmerchantid'   => '344892',
         'testtoken'        => '2Bq!p-F8',
         'testwsdl'         => 'https://epayment-test.bbs.no/Netaxept.svc?wsdl',
         'testterminal'     => 'https://epayment-test.bbs.no/terminal/default.aspx',
         'redirecturl'      => "http://" . $_SERVER["HTTP_HOST"] ."/checkout/execute",
         'merchantid'       => '344892',
         'token'            => 's!9D+Lt8',
         'terminal'         => 'https://epayment.bbs.no/terminal/default.aspx',
         'wsdl'             => 'https://epayment.bbs.no/Netaxept.svc?wsdl',
         'dnbkonto'         => '0000.00.00000',
         'testdnbkonto'     => '0000.00.00000',
      ) );
   }
   else if( Dispatcher::getPortal() == 'DM-SV' ){
      Settings::Set( 'finance', 'netsparameters', array(
         'testmerchantid'   => '344892',
         'testtoken'        => '2Bq!p-F8',
         'testwsdl'         => 'https://epayment-test.bbs.no/Netaxept.svc?wsdl',
         'testterminal'     => 'https://epayment-test.bbs.no/terminal/default.aspx',
         'redirecturl'      => "http://" . $_SERVER["HTTP_HOST"] ."/checkout/execute",
         'merchantid'       => '344892',
         'token'            => 's!9D+Lt8',
         'terminal'         => 'https://epayment.bbs.no/terminal/default.aspx',
         'wsdl'             => 'https://epayment.bbs.no/Netaxept.svc?wsdl',
         'dnbkonto'         => '0000.00.00000',
         'testdnbkonto'     => '0000.00.00000',
      ) );
   }
   else if( Dispatcher::getPortal() == 'UP-DK' ){
      Settings::Set( 'finance', 'netsparameters', array(
         'testmerchantid'   => '344892',
         'testtoken'        => '2Bq!p-F8',
         'testwsdl'         => 'https://epayment-test.bbs.no/Netaxept.svc?wsdl',
         'testterminal'     => 'https://epayment-test.bbs.no/terminal/default.aspx',
         'redirecturl'      => "http://" . $_SERVER["HTTP_HOST"] ."/checkout/execute",
         'merchantid'       => '506526',
         'token'            => 'J/x29tQ!',
         'terminal'         => 'https://epayment.bbs.no/terminal/default.aspx',
         'wsdl'             => 'https://epayment.bbs.no/Netaxept.svc?wsdl',
         'dnbkonto'         => '0000.00.00000',
         'testdnbkonto'     => '0000.00.00000',   
      ) );
   }else if( Dispatcher::getPortal() == 'ST-001' ){
      Settings::Set( 'finance', 'netsparameters', array(
         'testmerchantid'   => '10000171',
         'testtoken'        => '8Ar=!n5T',
         'testwsdl'         => 'https://epayment-test.bbs.no/Netaxept.svc?wsdl',
         'testterminal'     => 'https://epayment-test.bbs.no/terminal/default.aspx',
         'redirecturl'      => "http://" . $_SERVER["HTTP_HOST"] ."/checkout/execute",
         //'merchantid'       => '488803',
         //'token'            => 'G?q2w3=S',
         'merchantid'       => '323561',
         'token'            => 'C-i7z9%E',
         'terminal'         => 'https://epayment.bbs.no/terminal/default.aspx',
         'wsdl'             => 'https://epayment.bbs.no/Netaxept.svc?wsdl',
         'dnbkonto'         => '0000.00.00000',
         'testdnbkonto'     => '0000.00.00000',   
      ) );  
   }else{
      Settings::Set( 'finance', 'netsparameters', array(
         'testmerchantid'   => '10000171',
         'testtoken'        => '8Ar=!n5T',
         'testwsdl'         => 'https://epayment-test.bbs.no/Netaxept.svc?wsdl',
         'testterminal'     => 'https://epayment-test.bbs.no/terminal/default.aspx',
         'redirecturl'      => "http://" . $_SERVER["HTTP_HOST"] ."/checkout/execute",
         //'merchantid'       => '488803',
         //'token'            => 'G?q2w3=S',
         'merchantid'       => '323561',
         'token'            => 'C-i7z9%E',
         'terminal'         => 'https://epayment.bbs.no/terminal/default.aspx',
         'wsdl'             => 'https://epayment.bbs.no/Netaxept.svc?wsdl',
         'dnbkonto'         => '0000.00.00000',
         'testdnbkonto'     => '0000.00.00000',   
      ) );  
   }


?>