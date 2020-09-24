<?PHP

   import( 'pages.admin' );
   import( 'website.article' );

   model( 'i18n.language' );

   class AdminMenu extends AdminPage implements IView {

      protected $template = 'dialogs.menu';

      public function Execute( $unlockTry = null ) {

         $languages = array();

         $collection = new Language();
         foreach( $collection->collection( array( 'languageid', 'code', 'elementname', 'short' ) )->fetchAll() as $language ) {

            list( $languageid, $langcode, $langname, $short ) = $language;

            $languages[] = array(
               'id' => $languageid,
               'code' => $langcode,
               'short' => $short,
               'title' => $langname,
            );

         }

         $articles = array( array( 'id' => 0, 'title' => __( 'No article' ) ) );
         $collection = new Article();
         foreach( $collection->collection( array( 'id' ), array( 'grouping' => array( 'LIKE', '%menuitem%' )))->fetchAllAs('Article') as $article ) {

            $articles[] = array(
               'id' => $article->id,
               'title' => $article->title,
            );

         }

         $this->languages = $languages;
         $this->articles = $articles;

         // Handle edit lockness.
         $unlockHash = md5( date( 'Ymd' ) );
         $this->unlockhash = $unlockHash;

         if ( $unlockTry == $unlockHash ) {

            $this->isunlocked = true;

         }

      }

   }

?>