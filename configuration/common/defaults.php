<?PHP
   
   // import the required modules
   import( 'core.settings' );
   
   // define email settings
   Settings::Set( 'default', 'domain', 'eurofoto.no' );
   
   // configure available sites
   Settings::set( 'application', 'sites', array(
      1 => array(
         'id' => 1,
         'short' => 'website',
         'templates' => 'eurofoto',
         'desc' => 'Eurofoto',
      ),
      
     
      4 => array(
         'id' => 4,
         'short' => 'dinmerkelapp',
         'templates' => 'dinmerkelapp',
         'desc' => 'Dinmerkelapp',
      ),
      5 => array(
         'id' => 5,
         'short' => 'ukeplan',
         'templates' => 'ukeplan',
         'desc' => 'Ukeplan',
      ),
      
      
      7 => array(
         'id' => 7,
         'short' => 'studentskyltar',
         'templates' => 'studentskyltar',
         'desc' => 'Studentskilt',
      ),
      
      8 => array(
        'id' => 8,
        'short' => 'normerk',
        'templates' => 'normerk',
        'desc' => 'Normerk'
      ),
      
      
      9 => array(
        'id' => 9,
        'short' => 'skanska',
        'templates' => 'skanska',
        'desc' => 'Stickers.no'
      ),
      
      
      
      10 => array(
        'id' =>10,
        'short' => 'repix',
        'templates' => 'repix',
        'desc' => 'Repix.no'
      ),
      
      
   ) );
   
?>
