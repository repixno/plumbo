<?PHP
   
   import( 'core.settings' );
   config( 'dinmerkelapp.portals.sparelappen' );
   config( 'dinmerkelapp.portals.merkelappsv' );
   config( 'dinmerkelapp.portals.seniorlappen' );
   config( 'dinmerkelapp.portals.utestemme' );
   
   Settings::set( 'default', 'hostname', 'dinmerkelapp.no' );
   Settings::Set( 'default', 'protocol', 'http' );
   

   Settings::SetSection( 'domainMap', array(
      // Default
      'www.dinmerkelapp.no' => array(
            'siteroot'      => 'dinmerkelapp',
            'template'      => 'merkelapp3.0',
            'fallback'      => 'merkelapp',
            'language'      => 'nb_NO',
            'portal'        => 'DM-001',
            'logingroup'    => 'DINMERKELAPP',
            'siteid'        => 4,
            'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
            'redirect'      => false,
            'https'         => false,
            'customattr'    => array(
               'countryid'  => 160,
               'portalid'   => 30,
               'google'     => array(
                  'analytics'                 => 'UA-41488546-1',
                  'google-site-verification'  => '',
            )
        )
      )
   ) );
   
   
   Settings::SetSection( 'domainMap', array(
      // Default
      'dinmerkelapp.no' => array(
            'siteroot'      => 'dinmerkelapp',
            'template'      => 'merkelapp3.0',
            'fallback'      => 'merkelapp',
            'language'      => 'nb_NO',
            'portal'        => 'DM-001',
            'logingroup'    => 'DINMERKELAPP',
            'siteid'        => 4,
            'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
            'redirect'      => false,
            'https'         => false,
            'customattr'    => array(
               'countryid'  => 160,
               'portalid'   => 30,
               'google'     => array(
                  'analytics'                 => 'UA-41488546-1',
                  'google-site-verification'  => '',
            )
        )
      )
   ) );
   
   
   
     
   
   
      
      
       Settings::SetSection( 'domainMap', array(
      // Default
      'gratis.dinmerkelapp.no' => array(
            'siteroot'      => 'dinmerkelapp',
            'template'      => 'gratis',
         //   'fallback'      => 'dinmerkelapp',
            'language'      => 'nb_NO',
            'portal'        => 'DM-002',
            'logingroup'    => 'GRATISDINMERKELAPP',
            'siteid'        => 4,
            'redirect'      => false,
            'https'         => false,
            'customattr'    => array(
               'countryid'  => 160,
               'portalid'   => 45,
               'google'     => array(
                  'analytics'                 => 'UA-41488546-1',
                  'google-site-verification'  => '',
            )
        )
      )
   ) );
       
    
    
      Settings::SetSection( 'domainMap', array(
      // Default
      'beta.dinmerkelapp.no' => array(
           'siteroot'      => 'dinmerkelapp',
            'template'      => 'dinmerkelapp',
            'fallback'      => 'dinmerkelapp',
            'language'      => 'nb_NO',
            'portal'        => 'DM-001',
            'logingroup'    => 'DINMERKELAPP',
            'siteid'        => 4,
            'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
            'redirect'      => false,
            'https'         => false,
            'customattr'    => array(
               'countryid'  => 160,
               'portalid'   => 30,
               'google'     => array(
                  'analytics'                 => 'UA-41488546-1',
                  'google-site-verification'  => '',
            )
        )
      )
   ) );
      
      
       Settings::SetSection( 'domainMap', array(
      // Default
      'new.dinmerkelapp.no' => array(
            'siteroot'      => 'dinmerkelapp',
            'template'      => 'dinmerkelapp',
            'fallback'      => 'dinmerkelapp',
            'language'      => 'nb_NO',
            'portal'        => 'DM-001',
            'logingroup'    => 'DINMERKELAPP',
            'siteid'        => 4,
            'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
            'redirect'      => false,
            'https'         => false,
            'customattr'    => array(
               'countryid'  => 160,
               'portalid'   => 30,
               'google'     => array(
                  'analytics'                 => 'UA-41488546-1',
                  'google-site-verification'  => '',
            )
        )
      )
   ) );
   
   
   
   
   
   function dinmerkelapptemplate(){            
            $detect = new Mobile_Detect;
            if( $detect->isMobile() && !$detect->isTablet() ){
               return 'merkelapp_mobile';
            }else{
               return 'merkelapp3.0'; 
            }

      }
      
      
      
?>
