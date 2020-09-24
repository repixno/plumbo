#!/usr/local/bin/php -q
<?php

   chdir( dirname( __FILE__ ) );

   include( '../../bootstrap.php' );
   config( 'website.config' );

   include( 'MimeMailParser.class.php' );

   import( 'system.cli' );
   import( 'website.user' );
   import( 'website.album' );
   import( 'website.image' );
   import( 'storage.util' );
   import( 'math.zbase32' );
   import( 'core.session' ); 
   import( 'mail.send' );
  
   class MailUploadHandler extends Script {
      
      public function Main() {
         try {
            
            $mailparse = new MimeMailParser();
            $mailparse->setStream( STDIN );
   
            $to = iconv_mime_decode( $mailparse->getHeader( 'to' ) );
            $from = iconv_mime_decode( $mailparse->getHeader( 'from' ) );
            $subject = iconv_mime_decode( trim( $mailparse->getHeader( 'subject' ) ) );
            $text = trim( $mailparse->getMessageBody( 'text' ) );
            $html = trim( $mailparse->getMessageBody( 'html' ) );
   
            $attachments = $mailparse->getAttachments();
   
            preg_match( '/(\S+)@(\S+)/', $to, $matches );
   
            $token = zBase32::decode( $matches[1] );
   
            $user = User::getUserFromMailtoken( $token );
   
            Login::byUserId( $user->uid );
   
            Session::id( md5( rand( 0, 999999999 ) ) );
   
            if ( $user->uid > 0 ) {
               
               $album = new Album( );
   
               if ( !empty( $subject ) ) {
   
                  if ( is_int( $subject ) ) {

                     $titlealbum = new Album( (int)$subject );

                  } else {

                     $titlealbum = Album::getUserAlbumByTitle( $user->uid, $subject );
                  }

                  if ( $titlealbum->aid > 0 ) {
      
                     $album = $titlealbum;
      
                  } else {
      
                     $album->title = $subject;
                     $album->uid = $user->uid;
                     $album->description = $from;
                     
                     $album->save();
                  }
                 
               }

               $count = 0;
   
               foreach( $attachments as $attachment ) {
                  
                  try {

                     $filename = $attachment->filename;
                     
                     $data = null;
   
                     while ( $bytes = $attachment->read() ) {
   
                        $data .= $bytes;
   
                     }
                  
                     StorageUtil::uploadImageString( $user->uid, $album->aid, $data, null, $filename, $description = '' );
                  
                     $count++;
                  
                  } catch ( Exception $x ) {
                     
                     mail('eivind@intelesms.no', 'exception', $x->getMessage() );
                     
                     
                  }

               }
               
               $maildata['from'] = $from;
               $maildata['to'] = $to;
               $maildata['count'] = $count;
               $maildata['album'] = $album->asArray();
               $maildata['rooturl'] = WebsiteHelper::rootBaseUrl();
               
               $subject = __('Upload to Eurofoto');
               
               MailSend::Simple( $from, $subject, 'email.upload', $maildata );
               
            }
            
         } catch ( Exception $e ) {
            
            mail('eivind@intelesms.no', 'exception', $e->getMessage() );
            
         }

      }
  
   }
   
   CLI::Execute();

?>
