<?php

   /**
    * Settings for some cart things not necessary
    * to tag.
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */
   
   
   Settings::setSection(
      'cart', array(
	'varnish' => array(
            'refid' => 7252,
	),
        'upgrade' => array(
            'refid' => 7264,
	),
         'maskit'    => array(
            'refid' => 7202,
         ),
         'redeyeremoval' => array(
            'refid' => 442,
         ),
         'redeyeremoval_calendar' => array(
            'refid' => 973,
         ),
         'calendar_group' => array(
            'refid' => 53,
         ),
			
			'oppheng' => array(
            'refid' => 444,445,
         ),
        
		  
		  
		   'oppheng_group' => array(
            'refid' => 95,80,
         ),
			
			
			
			
			'canvas_group' => array(
            'refid' => 79,54,
				
         ),
			
			
         'photobook_group' => array(
            'refid' => 50,
         ),
         'mediaclipextrapages' => array(
            'refid' => 878,
         ),
         'freeitemrefid' => array(
            'refid' => 636,
         ),
         'discountrefid' => array(
            'refid' => 695,
         ),
         'set' => array(
            'optionidarray' => array( 448, 446, 3527 ),
            'types' => array(
               448 => 'DVD',
               446 => 'CD',
               3527 => 'MINNEPENN'
            ),
            'options' => array(
               'DVD' => 448,
               'CD'  => 446,
               'MINNEPENN' => 3527
            )
         ),
         'archive' => array(
            'optionidarray' => array( 481, 482, 7343 ),
            'types' => array(
               482 => 'DVD',
               481 => 'CD',
               7343 => 'MINNEPENN'
            ),
            'options' => array(
               'DVD' => 482,
               'CD'  => 481,
               'MINNEPENN' => 7343
            )
            
         )
      )
   );


?>