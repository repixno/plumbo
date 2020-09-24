<?PHP
   
   import( 'core.settings' );
   
   Settings::set( 'default', 'hostname', 'reedfoto.no' );
   Settings::Set( 'default', 'protocol', 'http' );
   
   Settings::SetSection( 'domainMap', array(
      
      // Default
      'corrections.reedfoto.no' => array(
         'siteroot'      => 'reedfoto',
         'template'      => 'correction',
         'language'      => 'nb_NO',
         'portal'        => '',
         'customattr'    => array(),
         'redirect'      => false,
         'https'         => false,
      ),
      
   ) );
?>