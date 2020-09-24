<?PHP
   
   class Version {
      
      static function GetCodeVersion() {
         
         return Settings::Get( 'application', 'version', 1.80 );
         
      }
      
      static function GetDatabaseVersion() {
         
         return DB::query( 'SELECT dbversion FROM site_version' )->fetchSingle();
         
      }
      
   }
   
?>