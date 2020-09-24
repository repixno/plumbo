<?PHP
   
   class String {
      
      static function saferHTML( $document ) {
         
         $search = array( '@<script[^>]*?>.*?</script>@si', '@<style[^>]*?>.*?</style>@siU', '@<![\s\S]*?--[ \t\n\r]*>@' );
         return preg_replace( $search, '', $document );
         
      }
      
      static function enhanceAsHTML( $content ) {
         
         if( trim( $content ) == '' ) return '&nbsp;';
         
         $content = String::saferHTML( $content );
         $content = preg_replace( "/(?:^|\b)((((http|https|ftp):\/\/)|(www\.))([\w\.]+)([,:%#&\/?~=\w+\@\(\)\|\.-]+))(?:\b|$)/ise","highlightURLs(\"$1\")", $content );
         $content = preg_replace( "/(([A-Za-z0-9]+_+)|([A-Za-z0-9]+\-+)|([A-Za-z0-9]+\.+)|([A-Za-z0-9]+\++))*[A-Za-z0-9]+@((\w+\-+)|(\w+\.))*\w{1,63}\.[a-zA-Z]{2,6}/","<a href=\"mailto:$0\">$0</a>", $content );
         $content = nl2br( $content );
         $content = str_replace( array( "\r", "\n", "\r\n" ), '', $content );
         
         return $content;
         
      }
      
   }
   
   function highlightURLs( $url ) {
		
      $link = ( substr( strtolower( $url ),0,7 )!=='http://' && substr( strtolower( $url ),0,8 )!=='https://' &&substr( strtolower( $url ),0,6 )!=='ftp://' ) ? 'http://'.$url : $url;
	   if( strlen( $url ) > 80 ) $url = substr( $url, 0, 80 ).'...';
		return "<a href=\"$link\" title=\"$link\" target=\"_blank\">$url</a>";
	   
	}
	
?>
