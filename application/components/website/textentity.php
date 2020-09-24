<?PHP

   model( 'site.textentity' );

   class TextEntity extends DBTextEntity {

      static function findEntityInMenuSet( $urlname, $textentityids = array(), $preferredlanguagecode = null ) {

         array_unshift( $textentityids, $noitem = 0 );
         $textentityids = implode( ',', $textentityids );

         try {

            if( isset( $preferredlanguagecode ) ) {
               
               $result = DB::query( "SELECT tc.textentityid
                                       FROM site_textentity_content tc
                                  LEFT JOIN i18n_language l
                                         ON l.languageid = tc.languageid
                                  LEFT JOIN site_textentity te
                                         ON te.id = tc.textentityid
                                      WHERE tc.urlname = ?
                                        AND te.deleted IS NULL
                                        AND l.code = ?
                                        AND tc.textentityid IN ($textentityids)",
                                            $urlname, $preferredlanguagecode );
               // success?
               if( $result->count() ) {
   
                  list( $textentityid ) = $result->fetchRow();
                  
                  return new TextEntity( $textentityid );
   
               }
               
            }
            
            $result = DB::query( "SELECT tc.textentityid,
                                         l.code
                                    FROM site_textentity_content tc
                               LEFT JOIN i18n_language l
                                      ON l.languageid = tc.languageid
                               LEFT JOIN site_textentity te
                                      ON te.id = tc.textentityid
                                   WHERE tc.urlname = ?
                                     AND te.deleted IS NULL
                                     AND tc.textentityid IN ($textentityids)",
                                         $urlname );
            // success?
            if( $result->count() ) {

               list( $textentityid, $language ) = $result->fetchRow();

               i18n::setLanguage( $language );

               return new TextEntity( $textentityid );

            }

            return false;

         } catch ( Exception $e ) {

            return false;

         }

      }

      static function fromIdentifier( $identifier ) {

         $textentity = TextEntity::fromFieldValue( array( 'identifier' => $identifier, 'deleted' => null ), 'TextEntity' );
         if( $textentity instanceof TextEntity && $textentity->isLoaded() ) {

            return $textentity;

         } else {

            return false;

         }

      }

      static function getAll( $type ) {

         $siteid = Session::get( 'adminsiteid', 0 );
         if( !$siteid ) {
            $siteid = Session::get( 'siteid', 1 );
         }
         
         if ( !in_array( $type, array( 'article', 'product' ) ) ) return false;

         $result = DB::query( "
            SELECT a.id, 
                   b.title, 
                   a.grouping
              FROM site_textentity a
         LEFT JOIN site_textentity_content b
               ON a.id=b.textentityid
            WHERE b.languageid = ? 
              AND a.deleted IS NULL
              AND a.type = ?
              AND a.siteid = ?",
           i18n::languageid(),
           $type, $siteid )->fetchAll( DB::FETCH_ASSOC );
           
         $ret = array();
         foreach ( $result as $val ) {

            // Find out if textentity is used in a menu
            $menuid = DB::query( "SELECT menuid FROM site_menu_contents WHERE textentityid = ?", $val['id'] )->fetchSingle();
            $val['connected'] = !empty( $menuid ) ? true : false;

            $ret[] = $val;

         }
         return $ret;

      }

   }

?>