<?PHP
   
   import( 'core.settings' );

   
      
   Settings::set( 'default', 'hostname', 'reedfoto.no' );
   Settings::Set( 'default', 'protocol', 'http' );
   
   Settings::SetSection( 'domainMap', array(
      
      // Default
      'fotony.reedfoto.no' => array(
         'siteroot'      => 'reedfoto2.0',
         'template'      => 'reedfoto3',
         'fallback'		 => 'reedfoto',
         'language'      => 'nb_NO',
         'portal'        => 'RF-002',
         'siteid'        => 1,
         'customattr'    => array(
            'countryid'  => 160,
            'portalid'   => 43,
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
