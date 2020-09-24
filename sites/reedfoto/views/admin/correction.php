<?PHP

   import( 'reedfoto.correction' );
   import( 'reedfoto.page' );

   class ReedFotoAdminCorrection extends AdminPage implements IView {

      protected $template = 'admin.correction';

      public function Execute( $corrid ) {

         $correction = new RFCorrection( $corrid );
         $this->correctiondata = $correction->asArray();

         $ret = array();
         foreach ( RFPage::enum( $corrid ) as $page ) {

            $ret[] = $page->asArray();

         }

         $this->pages = $ret;

      }

   }

?>
