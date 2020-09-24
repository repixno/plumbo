<?PHP
   
   import( 'core.settings' );
   config( 'skanska.portals.skanska_uk' );
    library( 'mobiledetect.Mobile_Detect' );
   
   Settings::set( 'default', 'hostname', 'skanska.repix.no' );
   Settings::Set( 'default', 'protocol', 'http' );
   
  
   
    Settings::SetSection( 'domainMap', array(
      // Default
      'skanska.repix.no' => array(
            'siteroot'      => 'skanska',
             'template'      => skanskatemplate(),
            'language'      => 'nb_NO',
            'portal'        => 'SKA-001',
            
               
            'redirect'      => false,
            'https'         => false,
            'customattr'    => array(
               'countryid'  => 160,
              
        )
      )
   ) );
   
      
   
    
       Settings::SetSection( 'domainMap', array(
         'stickers.repix.no' => array(
            'siteroot'      => 'skanska',
            //'template'      => 'skanska',
            'template'      => skanskatemplate(),
            'language'      => 'nb_NO',
            'portal'        => 'SKA-001',
         
            //'portal'        => '',
           
            'customattr'    => array(
               'countryid'  => 160,
               'portalid'   => 0,
               'login'      => '/frontpage',
               'google'     => array(
                  'analytics'                 => 'UA-390524-10',
                  'google-site-verification'  => 'p-dLuxTl30qH-AhKICmehN2DalJsJNGjjAGrzom1DNQ',
               )
            ),
            
            'https'         => false,
         )
         )
      );
      
   
      function skanskatemplate(){            
            $detect = new Mobile_Detect;
            if( $detect->isMobile() && !$detect->isTablet() ){
               return 'skanskamobile';
            }else{
               return 'skanska'; 
            }

      }
   
    
    
?>