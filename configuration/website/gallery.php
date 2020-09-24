<?PHP
   
   /**
    * Album mapping for todays photo albums on various portals
    */
   if( !Settings::has( 'gallery', 'portalkeymap' ) )
   Settings::set( 'gallery', 'portalkeymap', array(
      'default' => 'eurofoto',
      'EF-997'  => 'eurofoto',
      'ES-997'  => 'eurofotose',
      'SN-997'  => 'solno',
      'AP-001'  => 'apressen',
      'VG-997'  => 'vg',
      'EF-VG'   => 'vg',
   ) );
   
?>