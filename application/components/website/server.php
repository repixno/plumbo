<?php

   model( 'site.server' );
   
   class Server extends DBServer {
      
      public function asArray() {
         
         return array( 
            'id' => $this->serverid,
            'host' => $this->hostname,
            'port' => $this->port,
            'user' => $this->username,
            'active' => $this->active ? true : false,
            'updated' => $this->updated,
         );
         
      }
      
   }
   
?>