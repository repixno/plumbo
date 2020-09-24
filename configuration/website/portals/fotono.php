<?PHP
   
   import( 'core.settings' );
  
      
   Settings::set( 'default', 'hostname', 'eurofoto.no' );
   Settings::Set( 'default', 'protocol', 'http' );
   
   Settings::SetSection( 'domainMap', array(
      
  
      
      
    // Default Julie
      'repix.se' => array(
         'siteroot'      => 'website',
         'template'      => 'fotono',
       
         'language'      => 'nb_NO',
         'logingroup'    => 'FOTONO',
         'portal'       => 'FOTONO',
         'siteid'        => 1,
         'customattr'    => array(
         'portalname' => "FOTONO",
          'portalemail' => 'bilder@foto.no',
         'countryid'  => 160,
         'portalid'   => 38,
            'google'     => array(
               'analytics'                 => 'UA-390524-10',
               'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
            ),
         ),
         'redirect'      => false,
         'https'         => false,
      ),
      
      
      // Default Julie
      'www.repix.se' => array(
         'siteroot'      => 'website',
         'template'      => 'fotono',
       
         'language'      => 'nb_NO',
         'logingroup'    => 'FOTONO',
         'portal'       => 'FOTONO',
         'siteid'        => 1,
         'customattr'    => array(
         'portalname' => "FOTONO",
          'portalemail' => 'bilder@foto.no',
         'countryid'  => 160,
         'portalid'   => 38,
            'google'     => array(
               'analytics'                 => 'UA-390524-10',
               'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
            ),
         ),
         'redirect'      => false,
         'https'         => false,
      ),
      
      
      
      
       // Default template Frida
      'fotono.repix.no' => array(
         'siteroot'      => 'website',
         'template'      => 'fotono',
       
         'language'      => 'nb_NO',
         'logingroup'    => 'FOTONO',
         'portal'       => 'FOTONO',
         'siteid'        => 1,
         'customattr'    => array(
         'portalname' => "FOTONO",
          'portalemail' => 'bilder@foto.no',
         'countryid'  => 160,
         'portalid'   => 38,
            'google'     => array(
               'analytics'                 => 'UA-390524-10',
               'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
            ),
         ),
         'redirect'      => false,
         'https'         => false,
      ),
      
       // Default template Frida
      'bilder.foto.no' => array(
         'siteroot'      => 'website',
         'template'      => 'fotono',
       
         'language'      => 'nb_NO',
         'logingroup'    => 'FOTONO',
         'portal'       => 'FOTONO',
         'siteid'        => 1,
         'customattr'    => array(
         'portalname' => "FOTONO",
          'portalemail' => 'bilder@foto.no',
         'countryid'  => 160,
         'portalid'   => 38,
            'google'     => array(
               'analytics'                 => 'UA-390524-10',
               'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
            ),
         ),
         'redirect'      => false,
         'https'         => false,
      ),
      
      
       // Default template Frida
      'beta.repix.no' => array(
         'siteroot'      => 'website',
         'template'      => 'fotono',
       
         'language'      => 'nb_NO',
         'logingroup'    => 'FOTONO',
         'portal'       => 'FOTONO',
         'siteid'        => 1,
         'customattr'    => array(
         'portalname' => "FOTONO",
          'portalemail' => 'bilder@foto.no',
         'countryid'  => 160,
         'portalid'   => 38,
            'google'     => array(
               'analytics'                 => 'UA-390524-10',
               'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
            ),
         ),
         'redirect'      => false,
         'https'         => false,
      ),
      
        // Default template Frida
      'beta.fotono.repix.no' => array(
         'siteroot'      => 'website',
         'template'      => 'fotono',
       
         'language'      => 'nb_NO',
         'logingroup'    => 'FOTONO',
         'portal'       => 'FOTONO',
         'siteid'        => 1,
         'customattr'    => array(
         'portalname' => "FOTONO",
          'portalemail' => 'bilder@foto.no',
         'countryid'  => 160,
         'portalid'   => 38,
            'google'     => array(
               'analytics'                 => 'UA-390524-10',
               'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
            ),
         ),
         'redirect'      => false,
         'https'         => false,
      ),
      
      
       // Default template Frida
      'eurofoto.foto.no' => array(
         'siteroot'      => 'website',
         'template'      => 'fotono',
       
         'language'      => 'nb_NO',
         'logingroup'    => 'FOTONO',
         'portal'       => 'FOTONO',
         'siteid'        => 1,
         'customattr'    => array(
         'portalname' => "FOTONO",
          'portalemail' => 'bilder@foto.no',
         'countryid'  => 160,
         'portalid'   => 38,
            'google'     => array(
               'analytics'                 => 'UA-390524-10',
               'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
            ),
         ),
          'redirect'      => 'http://fotono.repix.no',
         'https'         => false,
      ),
      
      
      // Default template Julie
      'fotonodev.repix.no' => array(
         'siteroot'      => 'website',
         'template'      => 'fotono',
       
         'language'      => 'nb_NO',
         'logingroup'    => 'FOTONO',
         'portal'       => 'FOTONO',
         'siteid'        => 1,
         'customattr'    => array(
         'portalname' => "FOTONO",
         'countryid'  => 160,
         'portalid'   => 38,
            'google'     => array(
               'analytics'                 => 'UA-390524-10',
               'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
            ),
         ),
         'redirect'      => false,
         'https'         => false,
      ),
      
      
      
      
      
   ) );
   
?>
