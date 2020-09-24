<?PHP


   Settings::SetSection( 'domainMap', array(
         'mydaystudio.eurofoto.no' => array(
            'siteroot'      => 'website',
            'template'      => 'modelday',
            'language'      => 'nb_NO',
            'portal'        => 'MD-001',
            'fallback'      => 'eurofoto',
            'siteid'        => 1,
            'customattr'    => array(
               'countryid'  => 160,
               'portalid'   => 26,
               'lyrishq'    => '',
               'google'     => array(
                  'analytics'                 => '',
                  'google-site-verification'  => '',
               ),
               'login' => array(
                  'bytoken' => '/bestill-ett-bilde',
               ),
            ),
            'redirect'      => false,
            'https'         => false,
         ),
         'modelday.eurofoto.no' => array(
            'redirect'      => 'http://mydaystudio.eurofoto.no',
            'https'         => false,
         ),
         )
      );













?>