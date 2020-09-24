<?PHP
   import( 'core.settings' );
 library( 'mobiledetect.Mobile_Detect' );
 
 
   Settings::SetSection( 'domainMap', array(
         'stabburet.repix.no' => array(
            'siteroot'      => 'stabburet',
            //'template'      => 'stabburet_mobile',
            'template'      => templatestabburet(),
            'language'      => 'nb_NO',
            'portal'        => 'ST-001',
            'logingroup'    => 'STABBURET',
            //'portal'        => '',
            'fallback'      => 'eurofoto',
            'customattr'    => array(
               'countryid'  => 160,
               'portalid'   => 0,
               'login'      => '/frontpage',
               'google'     => array(
                  'analytics'                 => 'UA-390524-10',
                  'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
               )
            ),
            'redirect'      => tidsplan(),
            'https'         => false,
         )
         )
      );
   
   
     
      
      
      Settings::SetSection( 'domainMap', array(
         'kampanje.stabburetleverpostei.no' => array(
            'siteroot'      => 'stabburet',
            //'template'      => 'stabburet',
            'template'      => templatestabburet(),
            'language'      => 'nb_NO',
            'portal'        => 'ST-001',
            'logingroup'    => 'STABBURET',
            //'portal'        => '',
            'fallback'      => 'eurofoto',
            'customattr'    => array(
               'countryid'  => 160,
               'portalid'   => 0,
               'login'      => '/frontpage',
               'google'     => array(
                  'analytics'                 => 'UA-390524-10',
                  'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
               )
            ),
            'redirect'      => false,
            'https'         => false,
         )
         )
      );
      
      Settings::SetSection( 'domainMap', array(
         'www.stabburetleverpostei.no' => array(
            'siteroot'      => 'stabburet',
            //'template'      => 'stabburet',
            'template'      => templatestabburet(),
            'language'      => 'nb_NO',
            'portal'        => 'ST-001',
            'logingroup'    => 'STABBURET',
            //'portal'        => '',
            'fallback'      => 'eurofoto',
            'customattr'    => array(
               'countryid'  => 160,
               'portalid'   => 0,
               'login'      => '/frontpage',
               'google'     => array(
                  'analytics'                 => 'UA-390524-10',
                  'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
               )
            ),
            'redirect'      => tidsplan(),
            'https'         => false,
         )
         )
      );
   
 
      
      
      Settings::SetSection( 'domainMap', array(
         'stabburetbeta.repix.no' => array(
            'siteroot'      => 'stabburet',
            //'template'      => 'stabburet',
            'template'      => templatestabburet(),
            'language'      => 'nb_NO',
            'portal'        => 'ST-001',
            'logingroup'    => 'STABBURET',
            //'portal'        => '',
            'fallback'      => 'eurofoto',
            'customattr'    => array(
               'countryid'  => 160,
               'portalid'   => 0,
               'login'      => '/frontpage',
               'google'     => array(
                  'analytics'                 => 'UA-390524-10',
                  'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
               )
            ),
            'redirect'      => false,
            'https'         => false,
         )
         )
      );
      
      
      
  
      
      
      
      
      function tidsplan(){
        $datestr="2015-09-03 00:00:00";
         
        $date=strtotime($datestr);
         
        if( time() < $date ){
            return  "http://www.stabburetleverpostei.no";
        }else{
            return false;
        }
      }
      
      
      function templatestabburet(){            
            $detect = new Mobile_Detect;
            if( $detect->isMobile() && !$detect->isTablet() ){
               return 'stabburet_mobile';
            }else{
               return 'stabburet'; 
            }

      }
?>