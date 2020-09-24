<?php

   /**
    * Special config setting for mediaclip use
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   Settings::setSection(
      'mediaclip', array(
         'notloggedinuser' => 639866,
         'predefineduser' => 642124,
         'server' => 'jasmin.eurofoto.no',
         'thumbpath' => '/data/pd/ef28/mediaclip/thumbs/',
         'thumbpathcart' => '/data/pd/ef28/mediaclip/cart/',
         'extrapages' => array(
            'hardcover'    => 878,
            'softcover'    => 878,
         )
      )
   );


?>