<?PHP

   Settings::SetSection( 'domainMap', array(
		'mykidbeta.eurofoto.no' => array(
         'siteroot'      => 'website',
         'template'      => 'mykid',
         'language'      => 'nb_NO',
         'portal'        => 'MY-KID',
         'logingroup'	 => 'MY-KID',
         'fallback'      => 'eurofoto',
         'siteid'        => 1,
         'customattr'    => array(
            'countryid'  => 160,
            'portalid'   => 33,
            'lyrishq'    => '',
            'google'     => array(
               'analytics'                 => 'UA-3584367-21',
               'google-site-verification'  => '',
            ),
            'login' => array(
               'bytoken' => '/bestill-ett-bilde',
            ),
         ),
         'redirect'      => false,
         'https'         => false,
      ),
		'foto.mykid.no' => array(
         'siteroot'      => 'website',
         'template'      => 'mykid',
         'language'      => 'nb_NO',
         'portal'        => 'MY-KID',
         'logingroup'	 => 'MY-KID',
         'fallback'      => 'eurofoto',
         'siteid'        => 1,
         'customattr'    => array(
            'countryid'  => 160,
            'portalid'   => 33,
            'lyrishq'    => '',
            'google'     => array(
               'analytics'                 => 'UA-3584367-21',
               'google-site-verification'  => '',
            ),
            'login' => array(
               'bytoken' => '/bestill-ett-bilde',
            ),
         ),
         'redirect'      => false,
         'https'         => false,
      ),
   ) );
   
?>