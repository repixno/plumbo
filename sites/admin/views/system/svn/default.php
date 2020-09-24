<?php

   import( 'pages.admin' );
   import( 'website.server' );
   
   class SVNManager extends AdminPage implements IView {
      
      protected $template = 'system.svn';
      
      public function Execute() {
         
         $this->header = 'SVN Update';
         
         $servers = array();
         foreach( $this->getServers() as $server ) {
            $servers[] = $server->asArray();
         }
         
         $this->servers = $servers;
         
      }
      
      private function getServers() {
         
         $servers = array();
         $collection = new Server();
         foreach( $collection->collection( array( 'serverid' ), array( 'active' => true ), 'serverid' )->fetchAllAs('Server') as $server ) {
            $servers[] = $server;
         }
         return $servers;
         
      }
      
   }

?>