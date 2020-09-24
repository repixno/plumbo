<?PHP


   Settings::SetSection( 'domainMap', array(
            'devef.takkekort.no' => array(
               'siteroot'      => 'website',
               'template'      => 'takkekort',
               'language'      => 'nb_NO',
               'portal'        => 'TK-001',
               'logingroup'    => 'TK-001',
               'fallback'      => 'eurofoto',
               'siteid'        => 1,
               'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
               'customattr'    => array(
                  'countryid'  => 160,
                  'plattform'  => mobiletemplate(),
                  'login'      => array(
                        'default'   => "https://as.photoprintit.com/web/85021473/loginRegister.do?returnurl=http://devef.takkekort.no/login/bycewetoken&extid=eurofoto",
                        'logouturl'    => "https://as.photoprintit.com/web/85021473/logout.do"
                  ),
                  'portalid'   => 27,
                  'lyrishq'    => 'yOJV8Mvwz4EXti&amp;d=ekspresstakkekortno',
                  'google'     => array(
                     'analytics'                 => '',
                     'google-site-verification'  => '',
                  ),
               ),
               'redirect'      => false,
               'https'         => true,
            ),
            'takkekortbeta.eurofoto.no' => array(
               'siteroot'      => 'takkekort',
               'template'      => 'takkekort',
               'language'      => 'nb_NO',
               'portal'        => 'TK-001',
               'logingroup'    => 'TK-001',
               'fallback'      => 'eurofoto',
               'siteid'        => 1,
               'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
               'customattr'    => array(
                  'countryid'  => 160,
                  'plattform'  => mobiletemplate(),
                  'portalid'   => 27,
                  'lyrishq'    => 'yOJV8Mvwz4EXti&amp;d=ekspresstakkekortno',
                  'google'     => array(
                     'analytics'                 => '',
                     'google-site-verification'  => '',
                  ),
               ),
               'redirect'      => false,
               'https'         => false,
            ),
            'ekspress.takkekort.no' => array(
               'siteroot'      => 'website',
               'template'      => 'takkekort',
               'language'      => 'nb_NO',
               'portal'        => 'TK-001',
               'logingroup'    => 'TK-001',
               'fallback'      => 'eurofoto',
               'siteid'        => 1,
               'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
               'customattr'    => array(
                  'countryid'  => 160,
                  'plattform'  => mobiletemplate(),
                  'login'      => array(
                        'default'   => "https://as.photoprintit.com/web/85021473/loginRegister.do?returnurl=http://www.takkekort.no/login/bycewetoken&extid=eurofoto",
                        'logouturl'    => "https://as.photoprintit.com/web/85021473/logout.do"
                  ),
                  'portalid'   => 27,
                  'lyrishq'    => 'yOJV8Mvwz4EXti&amp;d=ekspresstakkekortno',
                  'google'     => array(
                     'analytics'                 => '',
                     'google-site-verification'  => '',
                  ),
               ),
               'redirect'      => false,
               'https'         => false,
            ),
            'takkekort.no' => array(
               'redirect'      => 'https://www.takkekort.no',
               'https'         => true,
            ),
            'www.takkekort.no' => array(
               'siteroot'      => 'website',
               'template'      => 'takkekort',
               'language'      => 'nb_NO',
               'portal'        => 'TK-001',
               'logingroup'    => 'TK-001',
               'fallback'      => 'eurofoto',
               'siteid'        => 1,
               'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
               'customattr'    => array(
                  'countryid'  => 160,
                  'plattform'  => mobiletemplate(),
                  'login'      => array(
                        'default'   => "https://as.photoprintit.com/web/85021473/loginRegister.do?returnurl=http://www.takkekort.no/login/bycewetoken&extid=eurofoto",
                        'logouturl'    => "https://as.photoprintit.com/web/85021473/logout.do"
                  ),
                  'portalid'   => 27,
                  'lyrishq'    => 'yOJV8Mvwz4EXti&amp;d=ekspresstakkekortno',
                  'google'     => array(
                     'analytics'                 => '',
                     'google-site-verification'  => '',
                  ),
               ),
               'redirect'      => false,
               'https'         => true,
            ),
            'devef.tackkort.se' => array(
               'siteroot'      => 'website',
               'template'      => 'tackkortse',
               'language'      => 'sv_SE',
               'portal'        => 'TK-SV',
               'logingroup'    => 'TK-SV',
               'fallback'      => 'eurofoto',
               'siteid'        => 1,
               'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
               'customattr'    => array(
                  'countryid'  => 208,
                  'plattform'  => mobiletemplate(),
                  'login'      => array(
                        'default'   => "https://as.photoprintit.com/web/85021449/loginRegister.do?returnurl=http://devef.tackkort.se&extid=eurofoto",
                        'logouturl'    => "https://as.photoprintit.com/web/85021449/"
                  ),
                  'portalid'   => 27,
                  'lyrishq'    => 'yOJV8Mvwz4EXti&amp;d=ekspresstakkekortno',
                  'google'     => array(
                     'analytics'                 => '',
                     'google-site-verification'  => '',
                  ),
               ),
               'redirect'      => false,
               'https'         => true,
            ),
            'www.tackkort.se' => array(
               'siteroot'      => 'website',
               'template'      => 'tackkortse',
               'language'      => 'sv_SE',
               'portal'        => 'TK-SV',
               'logingroup'    => 'TK-SV',
               'fallback'      => 'eurofoto',
               'siteid'        => 1,
               'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
               'customattr'    => array(
                  'countryid'  => 208,
                  'plattform'  => mobiletemplate(),
                  'login'      => array(
                        'default'   => "https://as.photoprintit.com/web/85021449/loginRegister.do?returnurl=http://www.tackkort.se&extid=eurofoto",
                        'logouturl'    => "https://as.photoprintit.com/web/85021449/"
                  ),
                  'portalid'   => 27,
                  'lyrishq'    => 'yOJV8Mvwz4EXti&amp;d=ekspresstakkekortno',
                  'google'     => array(
                     'analytics'                 => '',
                     'google-site-verification'  => '',
                  ),
               ),
               'redirect'      => false,
               'https'         => true,
            ),
            'tackkort.se' => array(
               'siteroot'      => 'website',
               'template'      => 'tackkortse',
               'language'      => 'sv_SE',
               'portal'        => 'TK-SV',
               'logingroup'    => 'TK-SV',
               'fallback'      => 'eurofoto',
               'siteid'        => 1,
               'settings' => array(
                  'mediaclip.nologinrequired' => true,
               ),
               'customattr'    => array(
                  'countryid'  => 208,
                  'plattform'  => mobiletemplate(),
                  'login'      => array(
                        'default'   => "https://as.photoprintit.com/web/85021449/loginRegister.do?returnurl=http://tackkort.se&extid=eurofoto",
                        'logouturl'    => "https://as.photoprintit.com/web/85021449/"
                  ),
                  'portalid'   => 27,
                  'lyrishq'    => 'yOJV8Mvwz4EXti&amp;d=ekspresstakkekortno',
                  'google'     => array(
                     'analytics'                 => '',
                     'google-site-verification'  => '',
                  ),
               ),
               'redirect'      => false,
               'https'         => true,
            ),
         )
      );






   function mobiletemplate(){
         $mobile_browser = '0';
 
         if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|ipad)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            $mobile_browser++;
         }
 
         if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
            $mobile_browser++;
         }    
 
         $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
         $mobile_agents = array(
            'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
            'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
            'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
            'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
            'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
            'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
            'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
            'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
            'wapr','webc','winw','winw','xda ','xda-');
 
            if (in_array($mobile_ua,$mobile_agents)) {
                $mobile_browser++;
            }
             
            if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini') > 0) {
                $mobile_browser++;
            }
             
            if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows') > 0) {
                $mobile_browser = 0;
            }
            
            
            if( $mobile_browser > 0 ){
               return 'mobile';
            }else{
               return 'standard'; 
            }
      }






?>