<?php
   
   class BetaSystem extends WebPage implements IView {
      
      public function Execute() {
         
         #setCookie( 'betatest', true, time() + ( 86400 * 60 ), '/', '.eurofoto.no' );
         setCookie( 'oldsite', "", time() - 3600, '/', '.eurofoto.no' );
         
         relocate( '/frontpage' );
         
      }
      
      public function Revert() {
         
         #setCookie( 'betatest', "", time() - 3600, '/', '.eurofoto.no' );
         setCookie( 'oldsite', true, time() + ( 86400 * 60 ), '/', '.eurofoto.no' );
         
         relocate( '/' );
         
      }
      
   }
   
?>
