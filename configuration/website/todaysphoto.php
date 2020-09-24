<?PHP
   
   /**
    * Album mapping for todays photo albums on various portals
    */
   if( !Settings::Has( 'todaysphoto', 'albums' ) )
   Settings::set( 'todaysphoto', 'albums', array(
      '' => 10891,
      'eurofoto' => 10891,
      'vg' => 1373,
      'eurofotose' => 716471,
   	'eurofotodk' => 913399,
      'eurofotocouk' => 752831,
   ) );
   
?>