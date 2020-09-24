<?php




// liten pakke: a+b+c
//stor pakke a+b+c+g+j
//medium pakke a+b+c+g
// A=7379
// B=7380
// C=7386
// J=7384
// G=7385
//kalender =7397
 
settings::set("reedfoto", "modules", array(
            1032 => array( //'15x20'
                'size' => array( 'x' => 2398, 'y' => 1795 ),
                'template' => array(
                      0 => array( 'x' => 0, 'y' => 0, 'dx' => 2398, 'dy' => 1795, 'deg' => -90 )
                  )
            ),
            7379 => array( //'Modul A'
                'size' => array( 'x' => 3602, 'y' => 2398 ),
                'template' => array(
                    0 => array( 'x' => 0, 'y' => -150, 'dx' => 1795, 'dy' => 2693, 'deg' => 0 ),
                    1 => array( 'x' => 1810, 'y' => 0, 'dx' => 1200, 'dy' => 1800, 'deg' => 0 ),
                    2 => array( 'x' => 1820, 'y' => 1807 , 'dx' => 430, 'dy' => 594,  'deg' => 0 , 'repx' => 4 ,
                           'stepx' => 0, 'repy' => 1, 'stepy' => 0 ),
                    3 => array( 'x' => 3011, 'y' => 5 , 'dx' => 534, 'dy' => 356,  'deg' => -90 , 'repx' => 1 ,
                           'stepx' => 0, 'repy' => 5, 'stepy' => 0, 'color' => 'bw'  )
                )
            ),
            7380 => array( //'Modul B'
                'size' => array( 'x' => 3602, 'y' => 2398 ),
                'template' => array(
                    0 => array( 'x' => 0, 'y' => 0, 'dx' => 1795, 'dy' => 2398, 'deg' => 0 ),
                    1 => array( 'x' => 1795, 'y' => 5 , 'dx' => 1795, 'dy' => 1205,  'deg' => 90 , 'repx' => 1 ,
                           'stepx' => 0, 'repy' => 2, 'stepy' => 0 )
                )
            ),
            7386 => array( //'Modul C'
                'size' => array( 'x' => 3602, 'y' => 2398 ),
                'template' => array(
                    0 => array( 'x' => 170, 'y' => 110 , 'dx' => 3264, 'dy' => 2173, 'type' => 'gruppe'  ),
                    1 => array( 'x' => 0, 'y' => 0, 'mal' => "fellesbilde.png"  ),
                    2 => array( 'text' => 'schoolname')
                    )
            ),
            73860 => array( //'Modul C'
                'size' => array( 'x' => 3602, 'y' => 2398 ),
                'template' => array(
                    0 => array( 'x' => 0, 'y' => -2, 'dx' => 3602, 'dy' => 2402, 'type' => 'gruppe'  )
                    )
            ),
            7384 => array( //'Modul J'
                'size' => array( 'x' => 3602, 'y' => 2398 ),
                'template' => array(
                    0 => array( 'x' => 0, 'y' => 0, 'dx' => 1795, 'dy' => 2398, 'deg' => 0 , 'repx' => 2, 'repy' => 1, 'stepx' => 10 ),
                    )
            ),
            7385 => array( //'Modul G'
                'size' => array( 'x' => 3543, 'y' => 2398 ),
                'template' => array(
                    0 => array( 'x' => -20 , 'y' => 0 , 'dx' => 3602, 'dy' => 2398, 'deg' => 90  )
                    )
            ),
            7504 => array( //'Modul B'
                'size' => array( 'x' => 3602, 'y' => 2398 ),
                'template' => array(
                    1 => array( 'x' => 0, 'y' => 5 , 'dx' => 1795, 'dy' => 1205,  'deg' => 90 , 'repx' => 2 ,
                           'stepx' => 0, 'repy' => 2, 'stepy' => 0 )
                )
            ),
            7291 => array( //'Digitalfil'
                'size' => array( 'x' => 3602, 'y' => 2398 ),
                'template' => array(
                    0 => array( 'x' => -20 , 'y' => 0 , 'dx' => 3602, 'dy' => 2398, 'deg' => 90  )
                    )
            ),
            7382 => array( //'Medium pakke'
                'modules' => array( 7379, 7380, 7386, 7385 )
            ),
            7381 => array( //'Stor pakke'
                'modules' => array( 7379, 7380, 7386, 7385, 7384, 7397 )
            ),
            7383 => array( //'liten pakke'
                'modules' => array( 7379, 7386, 7380)
            ),
            7286 => array( //'Portrettpakke'
                'modules' => array( 7379, 7380, 7385 )
            ),
            7485 => array(//norwaycup kalender)
            'size' => array( 'x' => 3602, 'y' => 4795 ),
            'template' => array(
                0 => array( 'x' => 190, 'y' => 89 , 'dx' => 3199, 'dy' => 2395, 'deg'=>-90 ),
                1 => array( 'x' => 0, 'y' => 0, 'mal' => "nckalender.png"  ),
                2 => array( 'text' => 'lagname')
                )
            ),
            7397 => array(//Reed Foto kalender)
            'size' => array( 'x' => 3616, 'y' => 2525 ),
            'template' => array(
                0 => array( 'x' => 324, 'y' => 345, 'dx' => 1261, 'dy' => 1876 ),
                1 => array( 'x' => 0, 'y' => 0, 'mal' => "kalender.png"  ),
                )
            ),
            5011 => array(
                0 => array( 'x' => 48, 'y' => 97, 'dx' => 2304, 'dy' => 1524, 'deg' => 90, 'color' => 'bw'  ),
                1 => array( 'x' => 48, 'y' => 1702 , 'dx' => 1141, 'dy' => 1742,  'deg' => 0  ),
                2 => array( 'x' => 1251, 'y' => 1702 , 'dx' => 354, 'dy' => 521,  'deg' => 0 , 'repx' => 3 ,
                           'stepx' => 15, 'repy' => 3, 'stepy' => 68 )
            ),
            5012 => array(
                0 => array( 'x' => 12, 'y' => 16, 'dx' => 1177, 'dy' => 1766, 'deg' => 0  ),
                1 => array( 'x' => 1207, 'y' => 16, 'dx' => 825, 'dy' => 1238, 'deg' => 0,'border' => '0.1x1-F.175x261-0.2x2'   ),
                2 => array( 'x' => 74, 'y' => 1860, 'dx' => 1061, 'dy' => 706, 'deg' => 90  ),
                3 => array( 'x' => 74, 'y' => 2687 , 'dx' => 521, 'dy' => 354,  'deg' => 90 , 'repx' => 2,
                           'stepx' => 60, 'repy' => 2, 'stepy' => 60 ),
                4 => array( 'x' => 1260, 'y' => 1860, 'dx' => 1061, 'dy' => 1593, 'deg' => 0  ), 
            ),
            5013 => array(
                0 => array( 'x' => 249, 'y' => 1194, 'dx' => 1812, 'dy' => 1204, 'deg' => 90  ),
                1 => array( 'x' => 0, 'y' => 0, 'mal' => "5013.png"  )
            )
        )
    );