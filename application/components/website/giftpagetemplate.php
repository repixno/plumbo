<?PHP
   
   model( 'gift.pagetemplate' );
   
   class GiftPageTemplate extends DBGiftPageTemplate {
      
      static function fromTemplateIdAndPageId( $templateid, $pageid ) {
         
         try {
            
            $res = DB::query( "
               SELECT 
                  malpageid 
               FROM 
                  malpage 
               WHERE 
                  malid = ? AND 
                  pagenr = ?
            ", $templateid, $pageid );
            
            if( $res->count() > 0 ) {
               
               list( $id ) = $res->fetchRow();
               return new GiftPageTemplate( $id );
               
            }
            
         } catch( Exception $e ) {
            
            return null;
            
         }
         
         return null;
         
      }
      
      
      
   }
      
   
   
?>