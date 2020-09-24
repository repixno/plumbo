<?PHP
   
   import( 'core.settings' );

   config( 'ukeplan.portals.ugeplan' );
   config( 'ukeplan.portals.veckoplan' );
   //config( 'ukeplan.portals.ukeplan_de' );
   
   Settings::set( 'default', 'hostname', 'ukeplan.no' );
   Settings::Set( 'default', 'protocol', 'http' );
   
    Settings::SetSection( 'domainMap', array(
           'bedriftsplan.no' => array(
              'siteroot'     => 'ukeplan',
              'template'     => 'ukeplan',
              'language'     => 'nb_NO',
              'portal'       => 'UP-001',
              'logingroup'    => 'UKEPLAN',
              'siteid'        => 5,
              'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
              'customattr'    => array(
                 'countryid'  => 160,
                 'portalid'   => 29,
                 'lyrishq'    => '',
                 'google'     => array(
                    'analytics'                 => 'UA-41489538-1',
                    'google-site-verification'  => '',
                 ),
              ),
              'redirect'      => false,
              'https'         => false,
           )
           )
      );
      Settings::SetSection( 'domainMap', array(
           'www.bedriftsplan.no' => array(
              'siteroot'     => 'ukeplan',
              'template'     => 'ukeplan',
              'language'     => 'nb_NO',
              'portal'       => 'UP-001',
              'logingroup'    => 'UKEPLAN',
              'siteid'        => 5,
              'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
              'customattr'    => array(
                 'countryid'  => 160,
                 'portalid'   => 29,
                 'lyrishq'    => '',
                 'google'     => array(
                    'analytics'                 => 'UA-41489538-1',
                    'google-site-verification'  => '',
                 ),
              ),
              'redirect'      => false,
              'https'         => false,
           )
           )
      );
     Settings::SetSection( 'domainMap', array(
           'bestilling.ukeplan.no' => array(
              'siteroot'     => 'ukeplan',
              'template'     => 'ukeplan',
              'language'     => 'nb_NO',
              'portal'       => 'UP-001',
              'logingroup'    => 'UKEPLAN',
              'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
              'customattr'    => array(
                 'countryid'  => 160,
                 'portalid'   => 29,
                 'lyrishq'    => '',
                 'google'     => array(
                    'analytics'                 => 'UA-41489538-1',
                    'google-site-verification'  => '',
                 ),
              ),
              'redirect'      => false,
              'https'         => false,
           )
           )
      );
      
      Settings::SetSection( 'domainMap', array(
           'www.ukeplan.no' => array(
              'siteroot'     => 'ukeplan',
              'template'     => 'ukeplan',
              'language'     => 'nb_NO',
              'portal'       => 'UP-001',
              'logingroup'    => 'UKEPLAN',
              'siteid'        => 5,
              'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
              'customattr'    => array(
                 'countryid'  => 160,
                 'portalid'   => 29,
                 'lyrishq'    => '',
                 'google'     => array(
                    'analytics'                 => 'UA-41489538-1',
                    'google-site-verification'  => '',
                 ),
              ),
              'redirect'      => false,
              'https'         => false,
           )
           )
      );
      
      
      
      Settings::SetSection( 'domainMap', array(
           'dev.ukeplan.no' => array(
              'siteroot'     => 'ukeplan',
              'template'     => 'ukeplan',
              'language'     => 'nb_NO',
              'portal'       => 'UP-001',
              'logingroup'    => 'UKEPLAN',
              'siteid'        => 5,
              'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
              'customattr'    => array(
                 'countryid'  => 160,
                 'portalid'   => 29,
                 'lyrishq'    => '',
                 'google'     => array(
                    'analytics'                 => 'UA-41489538-1',
                    'google-site-verification'  => '',
                 ),
              ),
              'redirect'      => false,
              'https'         => false,
           )
           )
      );
      
      Settings::SetSection( 'domainMap', array(
           'ukeplan.no' => array(
              'siteroot'     => 'ukeplan',
              'template'     => 'ukeplan',
              'language'     => 'nb_NO',
              'portal'       => 'UP-001',
              'logingroup'    => 'UKEPLAN',
              'siteid'        => 5,
              'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
              'customattr'    => array(
                 'countryid'  => 160,
                 'portalid'   => 29,
                 'lyrishq'    => '',
                 'google'     => array(
                    'analytics'                 => 'UA-41489538-1',
                    'google-site-verification'  => '',
                 ),
              ),
              'redirect'      => false,
              'https'         => false,
           )
           )
      );
  
  
  
     Settings::SetSection( 'domainMap', array(
           'beta.ukeplan.no' => array(
              'siteroot'     => 'ukeplan',
              'template'     => 'ukeplan',
              'language'     => 'nb_NO',
              'portal'       => 'UP-001',
              'logingroup'    => 'UKEPLAN',
              'siteid'        => 5,
              'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
              'customattr'    => array(
                 'countryid'  => 160,
                 'portalid'   => 29,
                 'lyrishq'    => '',
                 'google'     => array(
                    'analytics'                 => 'UA-41489538-1',
                    'google-site-verification'  => '',
                 ),
              ),
              'redirect'      => false,
              'https'         => false,
           )
           )
        );
     
      Settings::SetSection( 'domainMap', array(
           'newukeplan.repix.no' => array(
              'siteroot'     => 'ukeplan',
              'template'     => 'ukeplan',
              'language'     => 'nb_NO',
              'portal'       => 'UP-001',
              'logingroup'    => 'UKEPLAN',
              'siteid'        => 5,
              'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
              'customattr'    => array(
                 'countryid'  => 160,
                 'portalid'   => 29,
                 'lyrishq'    => '',
                 'google'     => array(
                    'analytics'                 => 'UA-41489538-1',
                    'google-site-verification'  => '',
                 ),
              ),
              'redirect'      => false,
              'https'         => false,
           )
           )
        );

        
         Settings::SetSection( 'domainMap', array(
           'beta.ukeplan.no' => array(
              'siteroot'     => 'ukeplan',
              'template'     => 'ukeplan',
              'language'     => 'nb_NO',
              'portal'       => 'UP-001',
              'logingroup'    => 'UKEPLAN',
              'siteid'        => 5,
              'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
              'customattr'    => array(
                 'countryid'  => 160,
                 'portalid'   => 29,
                 'lyrishq'    => '',
                 'google'     => array(
                    'analytics'                 => 'UA-41489538-1',
                    'google-site-verification'  => '',
                 ),
              ),
              'redirect'      => false,
              'https'         => false,
           )
           )
        );
      
   

?>