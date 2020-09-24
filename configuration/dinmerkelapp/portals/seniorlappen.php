<?PHP
 library( 'mobiledetect.Mobile_Detect' );
 
 
   Settings::SetSection( 'domainMap', array(
         'seniorlappen.no' => array(
           'siteroot'      => 'dinmerkelapp',
           'template'      => template_senior(),
            'language'      => 'nb_NO',
            'portal'        => 'SL-001',
            'logingroup'    => 'SL-001',
            'fallback'      => 'merkelapp',
            'siteid'        => 4,
            'customattr'    => array(
               'portalname' => "seniorlappen",
               'countryid'  => 160,
               'portalid'   => 39,
               'login'      => '/bestilling',
                'google'     => array(
                   'analytics'                 => 'UA-41488546-1',
                   'google-site-verification'  => '',
               )
            ),
            'redirect'      => false,
            'https'         => false,
         )
         )
      );
     
      
      
      Settings::SetSection( 'domainMap', array(
      // Default
      'beta.seniorlappen.no' => array(
           'siteroot'      => 'dinmerkelapp',
            'template'      => template_senior(),
            'language'      => 'nb_NO',
            'portal'        => 'SL-001',
            'logingroup'    => 'SL-001',
            
            'siteid'        => 4,
            'customattr'    => array(
               'portalname' => "seniorlappen",
               'countryid'  => 160,
               'portalid'   => 39,
               'login'      => '/bestilling',
                'google'     => array(
                   'analytics'                 => 'UA-41488546-1',
                   'google-site-verification'  => '',
            )
        )
      )
   ) );
      
      
      
      Settings::SetSection( 'domainMap', array(
      // Default
      'dev.seniorlappen.no' => array(
           'siteroot'      => 'dinmerkelapp',
            'template'      => 'senior',
            'language'      => 'nb_NO',
            'portal'        => 'SL-001',
            'logingroup'    => 'SL-001',
            
            'siteid'        => 4,
            'customattr'    => array(
               'portalname' => "seniorlappen",
               'countryid'  => 160,
               'portalid'   => 39,
               'login'      => '/bestilling',
                'google'     => array(
                   'analytics'                 => 'UA-41488546-1',
                   'google-site-verification'  => '',
            )
        )
      )
   ) );
      
      
         Settings::SetSection( 'domainMap', array(
         'www.seniorlappen.no' => array(
            'siteroot'      => 'dinmerkelapp',
           'siteroot'      => 'dinmerkelapp',
            'template'      => 'senior',
            'language'      => 'nb_NO',
            'portal'        => 'SL-001',
            'logingroup'    => 'SL-001',
            'fallback'      => 'merkelapp',
            'siteid'        => 4,
            'customattr'    => array(
               'portalname' => "seniorlappen",
               'countryid'  => 160,
               'portalid'   => 39,
               'login'      => '/bestilling',
                'google'     => array(
                   'analytics'                 => 'UA-41488546-1',
                   'google-site-verification'  => '',
               )
            ),
            'redirect'      => false,
            'https'         => false,
         )
         )
      );
         
         
         
         
          function template_senior(){            
            $detect = new Mobile_Detect;
            if( $detect->isMobile() && !$detect->isTablet() ){
               return 'senior_mobile';
            }else{
               return 'senior'; 
            }

      }
      
      
      

?>