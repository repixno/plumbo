<?php
   /**
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    *
    */


   import( 'core.model' );
   import( 'website.product' );
   import( 'website.image' );

   class DBUserProject extends Model {

      static $table = 'projects';
      static $basename = 'mediaclip';

      static $fields = array(

         'id'           => array(
            'primary'   => true,
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
         ),
         'user_id'      => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
         ),
         'title'        => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
         ),
         'project_xml'  => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 16581375,
            'default'   => '',
         ),
         'description'  => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 65535,
            'default'   => '',
         ),
         'productid'    => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
         ),
         'created'      => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'saved'        => array(
            'type'      => DB_TYPE_DATETIME,
            'null'      => true,
            'default'   => null
         ),
         'predefinert'  => array(
            'alias'     => 'predefined',
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
         ),
         'predefined_project_id' => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
         ),
         'share_id'     => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
         ),
         'share'        => array(
            'type'      => DB_TYPE_INTEGER,
            'size'      => 11,
            'default'   => 0,
         ),
         'type'         => array(
            'type'      => DB_TYPE_STRING,
            'size'      => 255,
            'default'   => '',
         ),
         'productoptionid' => array(
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
            'default' => 0,
         ),
         'sheetcount' => array(
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
            'default' => 0,
         ),
         'xtra' => array(
            'type' => DB_TYPE_INTEGER,
            'size' => 11,
            'default' => 0,
         )

      );

      public function getUserId() {

         return $this->user_id;

      }

      public function setUserId( $userid ) {

         return $this->user_id = $userid;

      }

      public function getTitle() {

         return $this->fieldGet( 'title' );

      }

      public function setTitle( $title ) {

         return $this->fieldSet( 'title', $title );

      }

      public function getDescription() {

         return $this->fieldGet( 'description' );

      }

      public function setDescription( $desc ) {

         return $this->fieldSet( 'description', $desc );

      }

      public function getProjectXML() {

         return $this->fieldGet( 'project_xml' );

      }


      public function setProjectXML( $xml ) {

         $this->fieldSet( 'project_xml', $xml );

      }

      public function getArticleNo() {
         
         $length  = strlen( $this->productid );
         return (int) substr( (string) $this->productid, -$length, $length-3 );
         

      }

      public function getProductArray() {

         try {
            if( $this->articleno > 0 ) {

               return $this->getProduct()->asArray();

            }
         } catch( Exception $e ) {}

         return null;

      }

      public function getProduct() {

         return new Product( $this->getProductOption()->productid );

      }

      public function getProductOption() {

         return ProductOption::fromRefId( $this->articleno );

      }

      public function getSheetCount() {

         if ( $this->fieldGet( 'sheetcount' ) < 1 ) {

            $this->updatePageCounts();

         }

         return $this->fieldGet( 'sheetcount' );

      }

      public function getExtraPages() {

         if ( $this->fieldGet( 'sheetcount' ) < 1 ) {

            $this->updatePageCounts();

         }

         return $this->fieldGet( 'xtra' );

      }

      private function updatePageCounts() {

         $numSheets = $defaultNumSheets = 1;

         if ( $this->type == 'Photobook' ) {

            $numSheets = substr_count( $this->fieldGet( 'project_xml' ), '<photobook:page ' );
            $numSheets /= 2;

            $queryString = "
               SELECT
                  pagesmin
               FROM
                  mediaclip_templateoptions
               WHERE
                  artnum=?
               ";
            $defaultNumSheets = DB::query( $queryString, $this->articleno )->fetchSingle();

            if ( !$defaultNumSheets ) {

               $defaultNumSheets = 15;

            }

         } else if ( $this->type == '' ) {

            $defaultNumSheets = 15;

         }

         $this->fieldSet( 'sheetcount', $numSheets );
         $this->fieldSet( 'xtra', $numSheets-$defaultNumSheets );
         $this->save();

      }

   }

?>