<?PHP
   
   model( 'site.article' );
   
   class Article extends DBArticle {
      
      public function asArray() {
         
         try {
            $creator = new User( $this->createdby );
            $createdby = $creator->fullname;
         } catch( Exception $e ) {
            $createdby = 'unknown author';
         }
         
         $result = array(
            'id' => $this->id,
            'title' => $this->title,
            'shorttitle' => $this->shorttitle,
            'urlname' => $this->urlname,
            'ingress' => $this->ingress,
            'attachments' => $this->getAttachments(),
            'body' => $this->body,
            'type' => $this->type,
            'created' => $this->created,
            'createdby' => $createdby,
         );
         
         if( !empty( $this->customjs ) )  $result['customjs'] = $this->customjs;
         if( !empty( $this->customcss ) ) $result['customjs'] = $this->customcss;
         if( !empty( $this->comment ) )   $result['customjs'] = $this->comment;

         
         
         $meta = '';
         
         $istr = $result['ingress'] == '&nbsp;' ? '' : $result['ingress'];
         $bstr = $result['body'] == '&nbsp;' ? '' : $result['body'];
         
         $ilen = strlen( $istr );
         $blen = strlen( $bstr );
         
         if( $ilen > 0 && $blen > 0 && $blen > $ilen ) {
            $meta = $result['ingress'];
         } else 
         if( $ilen > 0 && $blen > 0 && $blen < $ilen ) {
            $meta = $result['body'];
         } else 
         if( $ilen > 0 ) {
            $meta = $result['ingress'];
         } else 
         if( $blen > 0 ) {
            $meta = $result['body'];
         }
         
         $meta = trim( substr( str_replace( '  ', ' ', str_replace( array( "\r\n", "\r", "\n" ), ' ', strip_tags( $meta ) ) ), 0, 150 ) );
         if( !empty( $meta ) ) {
            $result['metadesc'] = $meta;
         }
         
         return $result;
         
      }
      
   }
   
?>