<?PHP
   
   import( 'core.settings' );
   
   Settings::set( 'default', 'hostname', 'normerk.no' );
   Settings::Set( 'default', 'protocol', 'http' );
   
   
   
   Settings::SetSection( 'domainMap', array(
         'normerk.no' => array(
            'siteroot'     => 'normerk',
            'template'     => 'normerk',
            'language'     => 'nb_NO',
            'portal'       => 'NO-MERK',
            'siteid'        => 8,
            'logingroup'    => 'NORMERK',
            'customattr'    => array(
               'portalemail' => 'post@normerk.no',
               'countryid'  => 160,
               'portalid'   => 101,
               'lyrishq'    => '',
               'google'     => array(
                  'analytics'                 => '',
                  'google-site-verification'  => '',
               ),
            ),
            'redirect'      => false,
            'https'         => false,
         )
         )
    );
   
   Settings::SetSection( 'domainMap', array(
         'www.normerk.no' => array(
            'siteroot'     => 'normerk',
            'template'     => 'normerk',
            'language'     => 'nb_NO',
            'portal'       => 'NO-MERK',
            'siteid'        => 8,
            'logingroup'    => 'NORMERK',
            'customattr'    => array(
               'portalemail' => 'post@normerk.no',
               'countryid'  => 160,
               'portalid'   => 101,
               'lyrishq'    => '',
               'google'     => array(
                  'analytics'                 => '',
                  'google-site-verification'  => '',
               ),
            ),
            'redirect'      => false,
            'https'         => false,
         )
         )
    );
   
   Settings::SetSection( 'domainMap', array(
         'normerk.eurofoto.no' => array(
            'siteroot'     => 'normerk',
            'template'     => 'normerk',
            'language'     => 'nb_NO',
            'portal'       => 'NO-MERK',
            'siteid'        => 8,
            'logingroup'    => 'NORMERK',
            'customattr'    => array(
               'portalemail' => 'post@normerk.no',
               'countryid'  => 160,
               'portalid'   => 101,
               'lyrishq'    => '',
               'google'     => array(
                  'analytics'                 => '',
                  'google-site-verification'  => '',
               ),
            ),
            'redirect'      => false,
            'https'         => false,
         )
         )
    );
   
?>