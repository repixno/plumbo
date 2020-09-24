<?PHP
   
   exit;

   include "../../bootstrap.php";
   config('website.config');
   import( 'website.user' );
   
   $users = array(
      'eldrid.odden.otterlei@posten.no' => 'Eldrid odden Otterlei', //    761564
      // 'jorid.rekstad@posten.no' => 'Jorid Rekstad',              //    606564
      'marie.haugenvindal@posten.no' => 'Marie Haugen Vindal',      //    761565
      'rune.oyen@posten.no' => 'Rune Øyen',                         //    761566
      'torhild.karlstad@posten.no' => 'Torhild Karlstad',           //    761567
   );
   
   foreach( $users as $email => $fullname ) {
      
      $customer = new User();
      $customer->fullname = $fullname;
      $customer->username = $email;
      $customer->password = crypt('posten123');
      $customer->save();
      
   }
   
?>