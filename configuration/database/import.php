<?PHP
   
   if( !Settings::Has( 'database', 'import' ) )
   Settings::Set( 'database', 'import', array(
      'enabled' => false,
      'tables' => array(
         
      ),
   ) );
   
?>