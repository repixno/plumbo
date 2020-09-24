<?php

   model( 'gift.order.template' );

   class GiftOrderTemplate extends DBGiftOrderTemplate {
      
      
      public function asArray() {
         
         return array(
            "id"  => $this->id,
            "artnr" => $this->refid,
            "templateid" => $this->templateid,
            "userid" => $this->userid,
            "imageid" => $this->imageid,
            "pageid" => $this->pageid,
            "x"   => $this->x,
            "y"   => $this->y,
            "dx"  => $this->dx,
            "dy"  => $this->dy,
            "rotate" => $this->rotate,
            "text"   => $this->text,
            "usermod" => $this->user_mod,
            "editor_x" => $this->editor_x,
            "editor_y" => $this->editor_y,
            "printsize_x" => $this->printsize_x,
            "printsize_y" => $this->printsize_y,
            "printtype" => $this->printtype,
         );
         
      }
      
      
      
      static function addOrder( $order = array() ) {
         
         $templates = $order["editor_bildeinfo"];
         
      }
      
      
      
   }


?>