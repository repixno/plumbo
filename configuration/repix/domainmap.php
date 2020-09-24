<?PHP
   
   import( 'core.settings' );

   
      
   Settings::set( 'default', 'hostname', 'repix.no' );
   Settings::Set( 'default', 'protocol', 'http' );
   
   Settings::SetSection( 'domainMap', array(
      
      // Default
      'new.repix.no' => array(
         'siteroot'      => 'repix',
         'template'      => 'repix',
         'fallback'		 => 'repix',
         'language'      => 'nb_NO',
         'portal'        => 'RP-001',
         'siteid'        => 10,
         'settings' => array(
                  'mediaclip.nologinrequired' => false,
               ),
         'customattr'    => array(
            'countryid'  => 160,
            'portalid'   => 44,
            'google'     => array(
               'analytics'                 => 'UA-390524-10',
               'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
            ),
         ),
         'redirect'      => false,
         'logingroup'    => 'RP-001',
         'https'         => false,
      ),
      
      
      
            // Default
      'repix.no' => array(
         'siteroot'      => 'repix',
         'template'      => 'repix',
         'fallback'		 => 'repix',
         'language'      => 'nb_NO',
         'portal'        => 'RP-001',
         'siteid'        => 10,
         'settings' => array(
                  'mediaclip.nologinrequired' => false,
               ),
         'customattr'    => array(
            'countryid'  => 160,
            'portalid'   => 44,
            'google'     => array(
               'analytics'                 => 'UA-390524-10',
               'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
            ),
         ),
         'redirect'      => false,
          'logingroup'    => 'RP-001',
         'https'         => false,
      ),
      
      
                  // Default
      'butikk.repix.no' => array(
         'siteroot'      => 'repix',
         'template'      => 'repix',
         'fallback'		 => 'repix',
         'language'      => 'nb_NO',
         'portal'        => 'RP-001',
         'siteid'        => 10,
         'settings' => array(
                  'mediaclip.nologinrequired' => false,
               ),
         'customattr'    => array(
            'countryid'  => 160,
            'portalid'   => 44,
            'google'     => array(
               'analytics'                 => 'UA-390524-10',
               'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
            ),
         ),
         'redirect'      => false,
          'logingroup'    => 'RP-001',
         'https'         => false,
      ),
      
      
      
          // Default
      'www.repix.no' => array(
         'siteroot'      => 'repix',
         'template'      => 'repix',
         'fallback'		 => 'repix',
         'language'      => 'nb_NO',
         'portal'        => 'RP-001',
         'siteid'        => 10,
         'settings' => array(
                  'mediaclip.nologinrequired' => false,
               ),
         'customattr'    => array(
            'countryid'  => 160,
            'portalid'   => 44,
            'google'     => array(
               'analytics'                 => 'UA-390524-10',
               'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
            ),
         ),
         'redirect'      => false,
          'logingroup'    => 'RP-001',
         'https'         => false,
      ),
      
      
      
      
      
      
      
   ) );
   
?>
