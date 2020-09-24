<?PHP
   
   import( 'core.settings' );
   
   Settings::set( 'default', 'hostname', 'studentskylt.japanphoto.se' );
   Settings::Set( 'default', 'protocol', 'http' );
   
   Settings::SetSection( 'domainMap', array(
      // Default
      'studentskylt.eurofoto.no' => array(
            'siteroot'      => 'studentskylt',
            'template'      => 'studentskylt',
            'language'      => 'sv_SE',
            'portal'        => 'STU-SV',
            'logingroup'    => 'studentskylt',
            'siteid'        => 7,
            'redirect'      => false,
            'https'         => false,
            'customattr'    => array(
               'countryid'  => 203,
               'portalid'   => 36,
        )
      )
   ) );
   
   
   Settings::SetSection( 'domainMap', array(
      // Default
      'studentplakat.japanphoto.se' => array(
            'siteroot'      => 'studentskylt',
            'template'      => 'studentskylt',
            'language'      => 'sv_SE',
            'portal'        => 'STU-SV',
            'logingroup'    => 'studentskylt',
            'siteid'        => 7,
            'redirect'      => false,
            'https'         => false,
            'customattr'    => array(
               'countryid'  => 203,
               'portalid'   => 36,
        )
      )
   ) );
   
   
   
   
   settings::SetSection( 'domainMap', array(
      // Default
      'studentbeta.eurofoto.se' => array(
            'siteroot'      => 'studentskylt',
            'template'      => 'studentskylt',
            'language'      => 'sv_SE',
            'portal'        => 'STU-SV',
            'logingroup'    => 'studentskylt',
            'siteid'        => 7,
            'redirect'      => false,
            'https'         => false,
            'customattr'    => array(
               'countryid'  => 203,
               'portalid'   => 36,
        )
      )
   ) );
   
   Settings::SetSection( 'domainMap', array(
      // Default
      'student.japanphoto.se' => array(
            'siteroot'      => 'studentskylt',
            'template'      => 'studentskylt',
            'language'      => 'sv_SE',
            'portal'        => 'STU-SV',
            'logingroup'    => 'studentskylt',
            'siteid'        => 7,
            'redirect'      => false,
            'https'         => false,
            'customattr'    => array(
               'countryid'  => 203,
               'portalid'   => 36,
        )
      )
   ) );
   
    Settings::SetSection( 'domainMap', array(
      // Default
      'student.repix.no' => array(
            'siteroot'      => 'studentskylt',
            'template'      => 'studentskylt',
            'language'      => 'sv_SE',
            'portal'        => 'STU-SV',
            'logingroup'    => 'studentskylt',
            'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
            'siteid'        => 7,
            'redirect'      => false,
            'https'         => false,
            'customattr'    => array(
               'countryid'  => 203,
               'portalid'   => 36,
        )
      )
   ) );
   
   
   
   
    
    
?>