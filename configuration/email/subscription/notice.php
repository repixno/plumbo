<?php

   /**
    * Wrapping of stuff related to 
    * creating correct template look 
    * of subscription notice emails
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

    Settings::setSection(
      'notice', array(
         'topimage' => array(
            'EF-997' => 'ef_topp.jpg',
            'EF-VG' => 'vg_topp.jpg',
            'VG-997' => 'vg_topp.jpg',
            'SN-997' => 'sol_topp.jpg',
            'NSK-001' => 'studentkort_topp.jpg',
            'AM-997' => 'aftenposten_topp.jpg',
         ),
         'color' => array(
            'EF-997' =>  '#3E9DD9',
            'VG-997' => '#E11325',
            'EF-VG'  => '#E11325',
            'SN-997' => '#C6D6DE',
            'NSK-001'   => '#FF7800',
            'AM-997' => '#347ECF',
         ),
         'baseurl' => array(
            'EF-997' => 'http://www.eurofoto.no/',
            'VG-997' => 'http://foto.vg.no/',
            'EF-VG' => 'http://foto.vg.no/',
            'SN-997' => 'http://photobook.sol.no/',
            'NSK-001' => 'http://foto.norskstudentkort.no/',
            'AM-997' => 'http://foto.aftenposten.no/',
         ),
      )
   );


?>