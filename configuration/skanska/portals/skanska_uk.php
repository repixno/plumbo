<?PHP
   
   import( 'core.settings' );
 
    library( 'mobiledetect.Mobile_Detect' );
   
   Settings::set( 'default', 'hostname', 'ukstickers.repix.no' );
   Settings::Set( 'default', 'protocol', 'http' );
   
  
 
    
       Settings::SetSection( 'domainMap', array(
         'ukstickers.repix.no' => array(
            'siteroot'      => 'skanska',
            //'template'      => 'skanska',
            'template'      => skanskauktemplate(),
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
      
   
      function skanskauktemplate(){            
            $detect = new Mobile_Detect;
            if( $detect->isMobile() && !$detect->isTablet() ){
               return 'skanskamobile_uk';
            }else{
               return 'skanska_uk'; 
            }

      }
   
    
    
?>