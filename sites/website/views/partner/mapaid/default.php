<?PHP
   /**
 CREATE TABLE production_mapaid (
     id serial PRIMARY KEY,
     mapaidid integer,
     downloaded timestamp without time zone,
     toproduction timestamp without time zone,
     sent timestamp without time zone
 );
    *
    */
   
   
   model( 'production.mapaid' );

   
   class MapaidPartnerAdmin extends WebPage implements IView{
      
      protected $template = false;

      public function Execute() {
         
         util::Debug( "test " );
         
      }
      
      
   }
   
?>