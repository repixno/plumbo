<?php

import( 'website.textentity' );
import( 'pages.json' );

class APIFetchMenuList extends JSONPage implements IValidatedView {
   
   public function Validate() {

      return array(
         'execute' => array(
            'post' => array(
               'type' => VALIDATE_STRING,
            ),
         ),
         
         'grouplist' => array(
            'post' => array(
               'type' => VALIDATE_STRING,
            ),
         )
      );

   }
   
   private function getGroups( $type = false ) {
      
      if( $type == false ) return false;
      
      $entities = new TextEntity();
      $entities = $entities->getAll( $type );
      $entitylist = array();
      
      if( !count( $entities ) ) return true;
      
      foreach( $entities as $entity ) {
         
         if( !isset( $entity['grouping'] ) ) continue;
         $groups = explode( ' ', $entity['grouping'] );
         
         foreach( $groups as $group ) {
            if( $group == '0' ) continue;
            $grouplist[$group][] = $entity['id'];
         }
         
      }
            
      return $grouplist;
      
   }

   public function Execute() {
      
      $list = $this->getGroups( $_POST['type'] );

      // Set return values.
      if( $_POST['type'] ) {
         $this->result = false;
         $this->message = 'Missing entity type';
         return false;
      }
         
      $this->list = $list;
      $this->result = true;
      $this->message = 'OK';
      return true;
      
   }
   
   public function groupList() {
      
      $groups = $this->getGroups( $_POST['type'] );
      $grouplist = array();
      
      if( $groups === true ) {
         
         $grouplist = array();
         
      } elseif( !count( $groups ) ) {
         
         $this->result = false;
         $this->message = 'Missing entity type';
         return false;
         
      } else {
         
         foreach( $groups as $group => $tmp ) {
         
            $grouplist[] = $group;
            
         }
         
      }
      
      // Set return values.
      $this->list = $grouplist;
      $this->result = true;
      $this->message = 'OK';
      return true;
      
   }

}

?>
