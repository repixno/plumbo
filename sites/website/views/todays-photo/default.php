<?PHP
   
   /**
    * 
    * View for todays photo
    * Gets all albums and images for todays photo
    * 
    * It is distributed through several different
    * albums without specific dates. Sorted by
    * album names.
    * 
    * @author Andreas Färnstrand <andreas.farnstrand@interweb.no>
    * 
    */

   import( 'website.helper' );
   
   class TodaysPhotoView extends WebPage implements IView {
      
      protected $template = 'todays-photo.index';
      
      /**
       * Show todays photo archive
       *
       * @param integer $year
       * @param string $month
       * 
       * @author Andreas Färnstrand
       */
      public function Execute( $year = null, $month = null, $step = 0 ) {
         
         $yeartoday = date( 'Y' );
         $monthtoday = date( 'm' );
         
         // If just using short version
         if( count( func_get_args() ) == 0 ) {
            $year = $yeartoday;
            $month = $monthtoday;
            $step = 0;
         }
         
         if( count( func_get_args() ) == 1 ) {
            $year = $yeartoday;
            $month = $monthtoday;
            $step = func_get_arg( 0 );
         }

         // Current month needs to be treated a 
         //bit different than archived months.
         if( $year == $yeartoday && $month == $monthtoday ) {
            
            $archive = array();
            $images = array();
            $tenlatest = array();
            
            $imageid = (int) WebsiteHelper::getTodaysImage( $step );
            $numimages = 0;
            
            if( $imageid > 0 ) {
               
               // load the image and return it
               try {
                  $image = new Image( $imageid );
                  $this->image = $image->asArray();
                  
               } catch( Exception $e ) {}
               
            }
            
            config( 'website.todaysphoto' );
            $mapping = Settings::Get( 'todaysphoto', 'albums', array() );
            $albumid = (int) $mapping[Dispatcher::getPortal()];
            
            if( $albumid > 0 ) {
               
               $album = new Album( $albumid );
               $tmpimages = $album->listImageIDs();
               if( count( $tmpimages ) > 0 ) {

                  foreach( $tmpimages as $imageid ) {
                     
                     // Grant access to image
                     PermissionManager::current()->grantAccessTo( $imageid, 'image', PERMISSION_PUBLIC );
                                        
                     // Add the image to images global
                     $tmpimage = new Image( $imageid );
                     if( $tmpimage instanceof Image && $tmpimage->isLoaded() ) {
                        $tempsource = $tmpimage->asArray();
                        if( count( $images ) > 0 ) {
                           $links []= "/todays-photo/".count( $images ); 
                        } else {
                           $links []= "/todays-photo";
                        }
                        $images []= $tmpimage->asArray();
                     }
                  }
                  
                  $result = $this->getArchive( $year, $month );
                  
               }
               
               $numimages = $album->getNumImages();

            }
            
            $this->images = $images;
            $this->numimages = $numimages;
            $this->archive = $result["archive"];
            $this->archivelinks = $result["links"];
            $this->links = $links;
            
         }  else { // Archived months
         
            config( 'website.todaysphoto' );
            
            if( !isset( $year ) ) {
               $year = date( 'Y' );
            } else {
               $year = (int) $year;
            }
            
            // Get correct format of date
            if( !isset( $month ) || $month < 1 || $month > 12 ) {
               $month = date( 'm' );
            } else {
               $month = (int) $month;
            }
            
            if( $month < 10 && strlen( $month ) < 2 ) {
               $month = '0'.$month;
            }
            
            // Setup album id and other settings
            $mapping = Settings::Get( 'todaysphoto', 'albums', array() );
            $albumid = (int) $mapping[Dispatcher::getPortal()];
            $albumname = $year.'-'.$month;
            $archive = array();
            $images = array();
            
            $result = $this->getArchive( $year, $month );
            
            $this->images = $result["images"];
            $this->archive = $result["archive"];
            $this->links = $result["links"];
            $this->image = $this->images[$step];
            $this->numImages = count( $this->images );
            
         }
         
         
         
      }
      
      
      /**
       * Get all album details in archived albums
       *
       * @param integer $year
       * @param integer $month
       * @return array
       */
      private function getArchive( $year, $month ) {
         
         // Setup album id and other settings
         $mapping = Settings::Get( 'todaysphoto', 'albums', array() );
         $albumid = (int) $mapping[Dispatcher::getPortal()];
         $albumname = $year.'-'.$month;
         $archive = array();
         $images = array();
         $links = array();

         if( $albumid > 0 ) {

            // Grant access to user to view the todays photo album
            PermissionManager::current()->grantAccessTo( $albumid, 'album', PERMISSION_PUBLIC );
            $checkalbum = new Album( $albumid );

            if( $checkalbum instanceof Album && $checkalbum->isLoaded() ) {

               if( isset( $albumname ) ) {

                  // Get all albums for this user, archived todays photos
                  $res = DB::query( "SELECT aid FROM bildealbum WHERE uid = ? AND namn LIKE( '%-%' ) AND access = 2 ORDER BY namn DESC", $checkalbum->getOwnerId() );

                  // Loop through result
                  while( list( $aid ) = $res->fetchRow() ) {

                     $aid = (int) $aid;

                     if( $aid > 0 ) {

                        // Grant access to album.
                        // - "Damn, damn you dirty monkey, damn you to HELL".
                        // You know who you are and why.
                        PermissionManager::current()->grantAccessTo( $aid, 'album', PERMISSION_PUBLIC );
                        $album = new Album( $aid );

                        if( $album instanceof Album && $album->isLoaded() ) {
                           
                           $nameparts = explode( '-', $album->title );

                           // Simple check to not get other albums than date formatted
                           if( count( $nameparts ) == 2 ) {

                              if( !is_null( $album->default_bid ) ) {
                                 // Grant access to image
                                 PermissionManager::current()->grantAccessTo( $album->default_bid, 'image', PERMISSION_PUBLIC );
                              } else {
                                 $res3 = DB::query( "SELECT bid FROM bildeinfo WHERE aid= ? AND deleted_at IS NULL ORDER BY dato ASC LIMIT 1", $album->id );
                                 list( $defaultimageid ) = $res3->fetchRow();
                                 if( isset( $defaultimageid ) ) {
                                    // Grant access to image
                                    PermissionManager::current()->grantAccessTo( $defaultimageid, 'image', PERMISSION_PUBLIC );
                                 }
                              }
                              
                              $tempalbum = $album->asArray( false, false );
                              $tempalbum['urls']['thumbnailurl'] = $album->getThumbnailUrl();
                              
                              // Add album to archive global
                              if( $album->title == $albumname ) {
                                 $links[$nameparts[0]][$nameparts[1]]['url'] = "/todays-photo/".$nameparts[0]."/".$nameparts[1];
                                 $links[$nameparts[0]][$nameparts[1]]['thumbnailurl'] = $tempalbum['urls']['thumbnailurl'];
                                 $archive[$nameparts[0]] []= array_merge( array( 'current' => true ), $tempalbum );
                                 
                              } else {
                                 $archive[$nameparts[0]] []= $tempalbum;
                                 $links[$nameparts[0]][$nameparts[1]]['url'] = "/todays-photo/".$nameparts[0]."/".$nameparts[1];
                                 $links[$nameparts[0]][$nameparts[1]]['thumbnailurl'] = $tempalbum['urls']['thumbnailurl'];
                              }

                              // If album is the one we are looking for?
                              if( $album->title == $albumname ) {

                                 $this->album = $album->asArray( true, false );

                                 // Get all images in this album
                                 $res2 = DB::query( "SELECT bid FROM bildeinfo WHERE aid = ? AND owner_uid = ? AND deleted_at IS NULL ORDER BY sorting DESC", $album->id, $checkalbum->getOwnerId() );

                                 while( list( $imageid ) = $res2->fetchRow() ) {

                                    $imageid = (int) $imageid;
                                    if( $imageid > 0 ) {

                                       // Grant access to image
                                       PermissionManager::current()->grantAccessTo( $imageid, 'image', PERMISSION_PUBLIC );

                                       // Add the image to images global
                                       $image = new Image( $imageid );
                                       if( $image instanceof Image && $image->isLoaded() ) {
                                          $tempsource = $image->asArray();
                                          if( count( $images ) > 0 ) {
                                             $tempsource['urls']['todaysphoto'] = "/todays-photo/".$nameparts[0]."/".$nameparts[1]."/".count( $images );
                                          } else {
                                             $tempsource['urls']['todaysphoto'] = "/todays-photo/".$nameparts[0]."/".$nameparts[1];
                                          }
                                          $images []= $tempsource;
                                       }

                                    }

                                 }

                              }

                           }

                        }

                     }

                  }

               }

            }

         }
         
         return array(
            "archive" => $archive,
            "links" => $links,
            "images" => $images,
         );
         
      }
      
   }
   
?>