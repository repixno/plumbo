<?PHP
   
   import( 'core.settings' );
  
      
   Settings::set( 'default', 'hostname', 'eurofoto.no' );
   Settings::Set( 'default', 'protocol', 'http' );
   
   Settings::SetSection( 'domainMap', array(
      
  
      Settings::SetSection( 'domainMap', array(
         'fotopix.no' => array(
            'redirect'      => 'https://www.fotopix.no',
            'https'         => false,
         )
    )
   ),
      
   
      
       // Dev Julie
      'beta.fotopix.no' => array(
         'siteroot'      => 'website',
         'template'      => 'fotopix',
       
         'language'      => 'nb_NO',
         'logingroup'    => 'FOTOPIX',
         'portal'       => 'FOTOPIX',
         'siteid'        => 1,
         'customattr'    => array(
         'portalname' => "FOTOPIX",
          'portalemail' => 'adele@fotopix.no',
         'countryid'  => 160,
         'portalid'   => 37,
            'google'     => array(
               'analytics'                 => 'UA-390524-10',
               'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
            ),
         ),
         'redirect'      => false,
         'https'         => false,
      ),
      
       // Dev Julie
      'dev.fotopix.no' => array(
         'siteroot'      => 'website',
         'template'      => 'fotopix',
       
         'language'      => 'nb_NO',
         'logingroup'    => 'FOTOPIX',
         'portal'       => 'FOTOPIX',
         'siteid'        => 1,
         'customattr'    => array(
         'portalname' => "FOTOPIX",
          'portalemail' => 'adele@fotopix.no',
         'countryid'  => 160,
         'portalid'   => 37,
            'google'     => array(
               'analytics'                 => 'UA-390524-10',
               'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
            ),
         ),
         'redirect'      => false,
         'https'         => false,
      ),
      
            
    // Default
      'www.fotopix.no' => array(
         'siteroot'      => 'website',
         'template'      => 'fotopix',
       
         'language'      => 'nb_NO',
         'logingroup'    => 'FOTOPIX',
         'portal'       => 'FOTOPIX',
         'siteid'        => 1,
         'customattr'    => array(
         'portalname' => "FOTOPIX",
          'portalemail' => 'adele@fotopix.no',
         'countryid'  => 160,
         'portalid'   => 37,
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
