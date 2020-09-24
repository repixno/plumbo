<?php

   import( 'website.blog.index' );
   import( 'website.blog.post' );
   import( 'website.blog.image' );
   import( 'website.album' );
   import( 'website.user' );
   import( 'storage.util' );
   import( 'validate.phone.cell' );
   import( 'sms.send' );
   
   class BlogMMS extends WebPage implements IView {
      
      public function Execute( $mobile = '', $keyword = '', $subject = '' ) {
         
         try {
            
            $this->template = false;
           
            $albumtitle = 'MMS';
            
            $userid = User::UserIDfromValidatedMobile( substr( $mobile, 2 ) );
            
            $subject = trim( eregi_replace( sprintf( '%s ', $keyword), '', $subject ) );
            
            if ( $userid > 0 ) {
               
               Login::byUserId( $userid );
      
               $blog = Blog::fromUserID( Login::userid() );
               
               $album = new Album( );
         
               $mmsalbum = Album::getUserAlbumByTitle( Login::userid(), $albumtitle );
      
               if ( $mmsalbum->aid > 0 ) {
      
                  $album = $mmsalbum;
      
               } else {
      
                  $album->title = $albumtitle;
                  $album->uid = Login::userid();
                  $album->description = $subject;
                  
                  $album->save();
               }
               
               $tmppath = '/tmp';
               $filepath = sprintf( '%s/%s', $tmppath, $mobile );
               
               $unzip = '/usr/bin/unzip';
               
               try {
      
                  mkdir( $filepath );
                  
                  @exec( sprintf( '%s %s -d %s', $unzip, $_FILES['file']['tmp_name'], $filepath ) );
               
               } catch ( Exception $e ) {
                  
               }
                  
               $files = dir( $filepath );
               
               while ( $file = $files->read() ) {
                  
                  $ext = strtolower( substr( $file, strrpos( $file, '.' ) + 1, 5 ) );

                  if ( !empty( $ext ) ) {
                     
                     $fullfilepath = sprintf( '%s/%s', $filepath, basename( $file ) );
                     
                     switch ( $ext ) {
                        
                        case 'jpg':
                        case 'jpeg':
                           
                           $images[] = StorageUtil::uploadImageString( Login::userid(), $album->aid, file_get_contents( $fullfilepath ), null, $file, $description = '' );
                     
                           break;
                        case 'txt':
                           
                           $blogtext .= file_get_contents( $fullfilepath );
                           
                           break;
                           
                        default:
                           
                           break;
                           
                     }
                     
                     unlink( $fullfilepath );
                     
                  }
                  

               }
               
               $blogtext = eregi_replace( sprintf( '%s ', $keyword), '', $blogtext );
               
               rmdir ( $filepath );
               
               if ( empty( $subject ) ) {
                  $subject = $this->getSubjectFromText( $blogtext );
               }
               
               $post = $blog->addPost( $subject, $subject, $blogtext );
               
               if ( count( $images ) > 0 ) {
               
                  foreach ( $images as $imageid ) {
                                 
                     $blogimage = new BlogImage();
                     $blogimage->postid = $post->postid;
                     $blogimage->imageid = $imageid;
                     $blogimage->save();
                     
                  }
               
               }
               
               Login::clear();
               
               die( 'OK' );
               
            } else {
               
               die( 'FAILED!' );
            }
            
         } catch ( Exception $e ) {
            
            die ( $e->getMessage() );
            
         }
         
      }
      
      function getSubjectFromText( $text ) {

         $length = 35;
   
         $subject = $text;
   
         $chars = array(".", "!", ",", "?",":",";");
   
         foreach ($chars as $char) {
            //if (strpos($subject, $char) <= $length) {
               if ( strpos( $subject, $char ) < strlen( $subject ) && ( strpos( $subject, $char ) > 0 ) ) {
                  $subject = substr( $subject, 0, strpos( $subject, $char ) );
               }
            //}
         }
   
         if ( ( strlen( $subject ) == 0 ) || ( strlen( $subject ) > $length ) ) {
            $text_ = explode( "\n", wordwrap( $text, $length, "\n" ) );
            $subject = $text_[0];
         }
   
         return $subject;
      }
   
         
   }
   
?>