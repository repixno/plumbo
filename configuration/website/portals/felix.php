<?PHP

    Settings::SetSection( 'domainMap', array(
            'felixbeta.eurofoto.se' => array(
                'siteroot'      => 'website',
                //'template'      => 'stabburet_mobile',
                'template'      => 'felix',
                'language'      => 'sv_SE',
                'portal'        => 'FE-001',
                'logingroup'    => 'FELIX',
                //'portal'        => '',
                'fallback'      => 'eurofoto',
                'customattr'    => array(
                    'countryid'  => 208,
                    'portalid'   => 9,
                    'login'      => '/frontpage',
                    'google'     => array(
                        'analytics'                 => '',
                        'google-site-verification'  => '',
                    )  
                ),
                'redirect'      => false,
                'https'         => false,
            )
        )
    );
    
    Settings::SetSection( 'domainMap', array(
            'sagdetmedketchup.eurofoto.se' => array(
                'siteroot'      => 'website',
                //'template'      => 'stabburet_mobile',
                'template'      => 'felix',
                'language'      => 'sv_SE',
                'portal'        => 'FE-001',
                'logingroup'    => 'FELIX',
                //'portal'        => '',
                'fallback'      => 'eurofoto',
                'customattr'    => array(
                    'countryid'  => 208,
                    'portalid'   => 9,
                    'login'      => '/frontpage',
                    'google'     => array(
                        'analytics'                 => '',
                        'google-site-verification'  => '',
                    )  
                ),
                'redirect'      => false,
                'https'         => false,
            )
        )
    );
   


?>