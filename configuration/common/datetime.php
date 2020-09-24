<?PHP

   // import the required modules
   import( 'core.settings' );
   
   // setup the default timezone
   date_default_timezone_set( 'Europe/Oslo' );
            
   // setup date settings
   Settings::SetSection( 'datetime', array(
      'dateformat'             => '%A %d. %B %Y',
      'dateformatshort'        => '%d.%m.%Y',
      'datetimeformat'         => '%A %d. %B %Y %H:%M:%S',
      'datetimeformatshort'    => '%d.%m.%Y %H:%M:%S',
      'datetimeformatnosec'    => '%d.%m.%Y %H:%M',
   ) );
   
?>