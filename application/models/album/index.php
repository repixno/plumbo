<?php

   /**
    * @author Andreas Farnstrand <andreas.farnstrand@interweb.no>
    *
    */

   Import( 'core.model' );

   class DBAlbum extends Model implements ModelCaching {

      static $table = 'bildealbum';

      static $fields = array(
         'aid' => array(
            'primary' => true,
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => false,
            'alias'   => 'id',
            'default' => 0,
         ),
         'uid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => false,
            'default' => 0,
         ),
         'namn' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 65535,
            'null'    => false,
            'default' => '',
         ),
         'description' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 65535,
            'default' => '',
         ),
         'access' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 3,
            'default' => 0,
         ),
         'cid' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 3,
            'null'    => true,
            'default' => null,
         ),
         'bid'    => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
         'passord' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 65535,
            'null'    => true,
            'default' => null,
         ),
         'for_sale' => array(
            'type'    => DB_TYPE_BOOLEAN,
            'size'    => 1,
            'default' => 'f',
         ),
         'for_download' => array(
            'type'    => DB_TYPE_BOOLEAN,
            'size'    => 1,
            'default' => 'f',
         ),
         'created_time' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'publicshare_time' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'views'    => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => 0,
         ),
         'grow_views' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => 0,
         ),
         'portal_key' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'country'    => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
         'sorting'    => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
         'default_bid'    => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
         'images' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 0,
         ),
         'sharekey' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => '',
         ),
         'deleted_at' => array(
            'type'    => DB_TYPE_DATETIME,
            'null'    => true,
            'default' => null,
         ),
         'sort_asc' => array(
            'type'    => DB_TYPE_BOOLEAN,
            'null'    => true,
            'default' => true,
         ),
         'sort_type' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'default' => 1,
         ),
         'year' => array(
            'type'    => DB_TYPE_INTEGER,
            'size'    => 11,
            'null'    => true,
            'default' => null,
         ),
         'identifier' => array(
            'type'    => DB_TYPE_STRING,
            'size'    => 255,
            'default' => null,
            'null'    => true,
         ),
      );

      public function __postSetup() {

         // setup some defaults for new objects
         if( !$this->created_time ) {
            $this->created_time = date( 'Y-m-d H:i:s' );
            $this->year = date( 'Y' );
         }
         
         // ensure this object has a sharekey
         /*
         if( !$this->sharekey ) {
            import( 'math.uuid' );
            $this->sharekey = UUID::create();
            if( $this->aid ) $this->save();
         }
         */
         
         // run the parent setup
         return parent::__postSetup();

      }

      /**
       * Get the owner id of this album
       *
       * @return integer
       */
      public function getOwnerId() {

         return $this->uid;

      }

      public function setOwnerId( $ownerid ) {

         return $this->uid = $ownerid;

      }

      /**
      * Get the name of this album
      *
      * @return string
      */
      public function getTitle() {

        return $this->fieldGet( 'namn' );

      }

      /**
      * Get the description of this album
      *
      * @return string
      */
      public function getDescription() {

        return$this->fieldGet( 'description' );

      }


      /**
      * Get album creation time
      *
      * @return string
      */
      public function getCreationTime() {

        return $this->created_time;

      }

      public function setCreated( $time ) {

         $this->created_time = $time;

      }


      /**
       * Return album creation time
       *
       * @return datetime
       */
      public function getCreated() {

         return $this->created_time;

      }

      /**
      * get the number of views for this album
      *
      * @return integer
      */
      public function getNumViews() {

        return (int)$this->views;

      }


      /**
      * Get sorting order for this album
      *
      * @return integer
      */
      public function getSortOrder() {

        return (int)$this->sorting;

      }


      /**
      * Get the exif year of this album
      *
      * @return integer
      */
      public function getYear() {

         return $this->fieldGet( 'year' );

      }


      /**
      * Get the access right for this album
      *
      * @return integer
      */
      public function getPermission() {

        return (int)$this->access;

      }

      /**
      * Get the deletion date for this album
      *
      * @return string
      */
      public function getDeletedTime() {

        return $this->deleted_at;

      }

      /**
      * Get the time when album was shared to gallery
      *
      * @return string
      */
      public function getPublicShareTime() {

        return $this->publicshare_time;

      }

      /**
      * Is this album for sale?
      *
      * @return boolean
      */
      public function forSale() {

        if( empty( $this->for_sale ) ) {
           return false;
        }

        return $this->for_sale;

      }

      /**
      * Get the password for this album
      *
      * @return string
      *
      */
      public function getPassword() {

         return $this->passord;

      }

      public function setPassword( $password ) {

         $this->passord = $password;

      }

      /**
      * Get the thumbnail for this album, if not set, take the first.
      *
      * @return string
      *
      */
      public function getThumbnail() {

      	if( $this->bid == '0') {

      		return $this->default_bid;

      	}

        return $this->bid;

      }


      /**
       * Set the name of this album
       *
       * @param string $name
       */
      public function setTitle( $title = '' ) {

         if( !empty( $title ) ) {
            $this->namn = $title;
            return true;

         }

         return false;

      }


      /**
       * Set the description of album
       *
       * @param string $description
       */
      public function setDescription( $description = '' ) {

         $this->fieldSet( 'description', $description );

         return false;
      }


      /**
       * Set the year of this album
       *
       * @param integer $year
       * @return boolean
       *
       */
      public function setYear( $year = 0 ) {

         if( !empty( $year ) && strlen( $year ) == 4 ) {
            $this->fieldSet( 'year',  $year );
            return true;

         }

         return false;

      }



      /**
       * Set if other than owner has access to but album
       *
       * @param string $purchaseAccess
       * @return boolean
       *
       */
      public function setPurchaseAccess( $purchaseAccess = false ) {

         $this->for_sale = $purchaseAccess ? true : false;
         return true;

      }

      public function getPurchaseAccess() {

         return $this->for_sale ? true : false;

      }

      /**
       * Set if other users have access to download images from album
       *
       * @param string $downloadAccess
       * @return boolean
       *
       */
      public function setDownloadAccess( $downloadAccess = false ) {

         $this->for_download = $downloadAccess ? true : false;
         return true;

      }

      public function getDownloadAccess() {

         return $this->for_download ? true : false;

      }


      /**
       * Set this album as deleted instead of deleting it from disk
       */
      public function delete() {
         
         $this->deleted_at = date( 'Y-m-d H:i:s' );
         $this->save();
         
      }

      public function getDeleted() {

         return $this->deleted_at;

      }

      public function setFileSortOrder( $asc ) {

         $sortOrder = $asc;
         if ( is_string( $asc ) && in_array( $asc, Album::$validAlbumSortOrders ) ) {

            switch ( $asc ) {

               case 'asc':
                  $sortOrder = true; break;
               case 'desc':
                  $sortOrder = false; break;
               default:
                  $sortOrder = false; break;

            }

         }

         if ( is_bool( $sortOrder ) ) {

            return $this->fieldSet( 'sort_asc', $sortOrder );

         } else {

            return null;

         }

      }

      public function getFileSortOrder() {

         return $this->fieldGet( 'sort_asc' ) == 't' ? 'asc' : 'desc';

      }

      public function setFileSortType( $type ) {

         $sortType = $type;
         if ( is_string( $type ) && in_array( $type, Album::$validAlbumSortTypes ) ) {

            $sortType = array_search( $type, Album::$validAlbumSortTypes );

         } else if ( is_int( $type ) && $type >= 0 && $type < sizeof( Album::$validAlbumSortTypes ) ) {

            $sortType = $type;

         }

         if ( is_int( $sortType ) ) {

            return $this->fieldSet( 'sort_type', $sortType );

         } else {

            return null;

         }

      }

      public function getFileSortType() {

         return Album::$validAlbumSortTypes[ $this->fieldGet( 'sort_type' ) ];

      }


   }

?>