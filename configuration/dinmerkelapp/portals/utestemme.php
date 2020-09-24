<?PHP
 library( 'mobiledetect.Mobile_Detect' );
  
  
   
   
     Settings::SetSection( 'domainMap', array(
      // Default
      'utelappen.dinmerkelapp.no' => array(
           'siteroot'      => 'dinmerkelapp',
           'template'      => template_utestemme(),
            'language'      => 'nb_NO',
            'portal'        => 'UL-001',
            'logingroup'    => 'UL-001',
            
            'siteid'        => 4,
            'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
            'customattr'    => array(
               'portalname' => "Utelappen",
               'countryid'  => 160,
               'portalid'   => 40,
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
      'navnelapp.utestemme.dinmerkelapp.no' => array(
           'siteroot'      => 'dinmerkelapp',
           'template'      => template_utestemme(),
            'language'      => 'nb_NO',
            'portal'        => 'UL-001',
            'logingroup'    => 'UL-001',
            
            'siteid'        => 4,
            'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
            'customattr'    => array(
               'portalname' => "Utelappen",
               'countryid'  => 160,
               'portalid'   => 40,
               'login'      => '/bestilling',
                'google'     => array(
                   'analytics'                 => 'UA-41488546-1',
                   'google-site-verification'  => '',
            )
        )
      )
   ) );
     
     
     
    
    function template_utestemme(){            
            $detect = new Mobile_Detect;
            if( $detect->isMobile() && !$detect->isTablet() ){
               return 'utestemme_mobile';
            }else{
               return 'utestemme'; 
            }
    }
     

?>