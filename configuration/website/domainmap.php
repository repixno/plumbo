<?PHP
   
   import( 'core.settings' );
 
config( 'website.portals.fotono');
config( 'website.portals.fotopix');
   config( 'website.portals.repix');

   
      
   Settings::set( 'default', 'hostname', 'repix.no' );
   Settings::Set( 'default', 'protocol', 'http' );
   
   Settings::SetSection( 'domainMap', array(
      
      
       // Default
      'repix.no' => array(
         'siteroot'      => 'website',
         'template'      => 'repix2',
         'fallback'		 => 'eurofoto2',
         'language'      => 'nb_NO',
         'portal'        => '',
         'siteid'        => 1,
         'customattr'    => array(
            'countryid'  => 160,
            'portalid'   => 0,
            'google'     => array(
               'analytics'                 => 'UA-390524-10',
               'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
            ),
         ),
         'redirect'      => false,
         'https'         => false,
      ),
      
      
      
      
      
       // Default
      'www.repix.no' => array(
         'siteroot'      => 'website',
         'template'      => 'repix2',
         'fallback'		 => 'eurofoto2',
         'language'      => 'nb_NO',
         'portal'        => '',
         'siteid'        => 1,
         'customattr'    => array(
            'countryid'  => 160,
            'portalid'   => 0,
            'google'     => array(
               'analytics'                 => 'UA-390524-10',
               'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
            ),
         ),
         'redirect'      => false,
         'https'         => false,
      ),
      
      
      // Default
      'eurofoto.no' => array(
         'siteroot'      => 'website',
         'template'      => 'repix',
         'fallback'		 => 'eurofoto',
         'language'      => 'nb_NO',
         'portal'        => '',
         'siteid'        => 1,
         'customattr'    => array(
            'countryid'  => 160,
            'portalid'   => 0,
            'google'     => array(
               'analytics'                 => 'UA-390524-10',
               'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
            ),
         ),
         'redirect'      => false,
         'https'         => false,
      ),
      
      
      
      
      
      
      // Default
      'julie.eurofoto.no' => array(
         'siteroot'      => 'website',
         'template'      => 'repix',
         'fallback'		 => 'eurofoto',
         'language'      => 'nb_NO',
         'portal'        => '',
         'siteid'        => 1,
         'customattr'    => array(
            'countryid'  => 160,
            'portalid'   => 0,
            'google'     => array(
               'analytics'                 => 'UA-390524-10',
               'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
            ),
         ),
         'redirect'      => false,
         'https'         => false,
      ),
      
      
      
      
    
      
      
      
      
           // Default
      'kunde.eurofoto.no' => array(
         'siteroot'      => 'website',
         'template'      => 'eurofotobackend',
         'language'      => 'nb_NO',
         'portal'        => '',
         'siteid'        => 1,
         'customattr'    => array(
            'countryid'  => 160,
            'portalid'   => 0,
            'google'     => array(
               'analytics'                 => 'UA-390524-10',
               'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
            ),
         ),
         'redirect'      => false,
         'https'         => false,
      ),
    
      
      // upload cluster
      'upload.eurofoto.no' => array(
         'siteroot'      => 'website',
         'template'      => 'eurofoto',
         'language'      => 'nb_NO',
         'portal'        => '',
         'siteid'        => 1,
         'customattr'    => array(
            'countryid'  => 160,
            'portalid'   => 0,
            'google'     => array(
               'analytics'                 => 'UA-390524-10',
               'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
            ),
         ),
         'redirect'      => false,
         'https'         => false,
      ),
      
      'www.eurofoto.no' => array(
         'siteroot'      => 'website',
         'template'      => 'eurofoto2',
         'fallback'		 => 'eurofoto',
         'language'      => 'nb_NO',
         'portal'        => '',
         'siteid'        => 1,
         'customattr'    => array(
            'countryid'  => 160,
            'portalid'   => 0,
            'google'     => array(
               'analytics'                 => 'UA-390524-10',
               'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
            ),
         ),
         'redirect'      => false,
         'https'         => false,
      ),
      
      'eurofoto.se' => array(
         'siteroot'      => 'website',
         'template'      => 'eurofoto',
         'language'      => 'sv_SE',
         'portal'        => 'ES-997',
         'customattr'    => array(
            'countryid'    => 208,
            'portalid'     => 9,
            'google'     => array(
               'analytics'                 => 'UA-390524-3',
               'google-site-verification'  => 'yVZAi1Imf7l5NFNewtiPn33wl7hcqzEApmbvTJcsAbw',
             )
         ),
         'redirect'      => false,
         'https'         => false,
      ),
      
     
      // Mine bilder
      'minebilder.no' => array(
         'redirect'      => 'http://eurofoto.no',
         'https'         => false,
      ),
      'www.minebilder.no' => array(
         'redirect'      => 'http://eurofoto.no',
         'https'         => false,
      ),       
      
      
      // Static sites for eurofoto
      'static.repix.no' => array(
         'siteroot'      => 'static',
         'template'      => 'eurofoto',
         'language'      => 'nb_NO',
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'portal'        => '',
         'redirect'      => false,
         'https'         => false,
      ),
      // Static sites for mercedes.eurofoto
      'static.mercedes.eurofoto.no' => array(
         'siteroot'      => 'static',
         'template'      => 'eurofoto',
         'language'      => 'nb_NO',
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'portal'        => '',
         'redirect'      => false,
         'https'         => false,
      ),

      
      // Static sites for eurofoto (a-d)
      'a.static.eurofoto.no' => array(
         'siteroot'      => 'static',
         'template'      => 'eurofoto',
         'language'      => 'nb_NO',
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'portal'        => '',
         'redirect'      => false,
         'https'         => false,
      ),
      'b.static.eurofoto.no' => array(
         'siteroot'      => 'static',
         'template'      => 'eurofoto',
         'language'      => 'nb_NO',
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'portal'        => '',
         'redirect'      => false,
         'https'         => false,
      ),
      'c.static.eurofoto.no' => array(
         'siteroot'      => 'static',
         'template'      => 'eurofoto',
         'language'      => 'nb_NO',
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'portal'        => '',
         'redirect'      => false,
         'https'         => false,
      ),
      'd.static.eurofoto.no' => array(
         'siteroot'      => 'static',
         'template'      => 'eurofoto',
         'language'      => 'nb_NO',
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'portal'        => '',
         'redirect'      => false,
         'https'         => false,
      ),
      
      
      
      
      
      // Static sites for eurofoto (a-d)
      'a.static.repix.no' => array(
         'siteroot'      => 'static',
         'template'      => 'eurofoto',
         'language'      => 'nb_NO',
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'portal'        => '',
         'redirect'      => false,
         'https'         => false,
      ),
      'b.static.repix.no' => array(
         'siteroot'      => 'static',
         'template'      => 'eurofoto',
         'language'      => 'nb_NO',
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'portal'        => '',
         'redirect'      => false,
         'https'         => false,
      ),
      'c.static.repix.no' => array(
         'siteroot'      => 'static',
         'template'      => 'eurofoto',
         'language'      => 'nb_NO',
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'portal'        => '',
         'redirect'      => false,
         'https'         => false,
      ),
      'd.static.repix.no' => array(
         'siteroot'      => 'static',
         'template'      => 'eurofoto',
         'language'      => 'nb_NO',
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'portal'        => '',
         'redirect'      => false,
         'https'         => false,
      ),
      
      
      
      // Special static sites for eurofoto
      'static-backend.eurofoto.no' => array(
         'siteroot'      => 'static',
         'template'      => 'eurofoto',
         'language'      => 'nb_NO',
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'portal'        => '',
         'redirect'      => false,
         'https'         => false,
      ),
      
      'static.eurofoto.mac' => array(
         'siteroot'      => 'static',
         'template'      => 'eurofoto',
         'language'      => 'nb_NO',
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'portal'        => '',
         'redirect'      => false,
         'https'         => false,
      ),
      
      // Admin sites for eurofoto
      'admin.eurofoto.no' => array(
         'siteroot'      => 'admin',
         'template'      => 'blue',
         'language'      => 'nb_NO',
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'portal'        => '',
         'redirect'      => false,
         'https'         => false,
      ),
      // Admin sites for eurofoto
      'admin2.eurofoto.no' => array(
         'siteroot'      => 'admin',
         'template'      => 'blue',
         'language'      => 'nb_NO',
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'portal'        => '',
         'redirect'      => false,
         'https'         => false,
      ),
     
     
     
     // Admin sites for eurofoto
      'admin.repix.no' => array(
         'siteroot'      => 'admin',
         'template'      => 'blue',
         'language'      => 'nb_NO',
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'portal'        => '',
         'redirect'      => false,
         'https'         => false,
      ),
      // Admin sites for eurofoto
      'admin2.repix.no' => array(
         'siteroot'      => 'admin',
         'template'      => 'blue',
         'language'      => 'nb_NO',
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'portal'        => '',
         'redirect'      => false,
         'https'         => false,
      ),
     
     
     
     // Admin sites for eurofoto
      'adminbeta.repix.no' => array(
         'siteroot'      => 'admin1',
         'template'      => 'greensusanne',
         'language'      => 'nb_NO',
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'portal'        => '',
         'redirect'      => false,
         'https'         => false,
      ),
      // Admin sites for eurofoto
      'admin2beta.repix.no' => array(
         'siteroot'      => 'admin2',
         'template'      => 'greenmarie',
         'language'      => 'nb_NO',
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'portal'        => '',
         'redirect'      => false,
         'https'         => false,
      ),
      
      
      
      
      
      
      
   ) );
   
?>
