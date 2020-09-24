<?PHP
   
   import( 'core.settings' );
  
      
   Settings::set( 'default', 'hostname', 'repix.no' );
   Settings::Set( 'default', 'protocol', 'http' );
   
   Settings::SetSection( 'domainMap', array(
      
  
      
       // Admin sites for eurofoto
      'repix.no' => array(
         'siteroot'      => 'website',
         'template'      => 'repix2',
         'language'      => 'nb_NO',
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'portal'        => '',
         'redirect'      => false,
         'https'         => false,
      ),
    
      
        // ukeplan sites for eurofoto
      'ukeplan.repix.no' => array(
         'siteroot'      => 'website',
         'template'      => 'repix',
         'language'      => 'nb_NO',
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'portal'        => '',
         'redirect'      => false,
         'https'         => false,
      ),
      
      
        // ukeplan sites for eurofoto
      'ugeplan.repix.no' => array(
         'siteroot'      => 'website',
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
      'admin.repix.no' => array(
         'siteroot'      => 'admin',
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
      'admin2.repix.no' => array(
         'siteroot'      => 'admin',
         'template'      => 'nellygreen',
         'language'      => 'nb_NO',
         'customattr'    => array(
            'countryid'    => 160,
            'portalid'     => 0,
         ),
         'portal'        => '',
         'redirect'      => false,
         'https'         => false,
      ),
      
      
            
    
      
      
      // Default
      'fotono.repix.no' => array(
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
