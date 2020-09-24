<?PHP
   
   model( 'site.feedback' );
   
   class Feedback extends DBFeedback {
      
      public function asArray() {
         
         $fields = @unserialize( $this->fields );

         if( !is_array( $fields ) ) 
         $fields = array();
         $extra = array();
         $user = false;
         
         foreach( $fields as $key => $value ) {
            
            switch( $key ) {
               
               case 'trace':
               case 'content':
                  break;
                  
               default:
                  $extra[] = array(
                     'key' => $key,
                     'value' => nl2br( $value ),
                     'raw' => $value,
                  );
                  break;
               
            }
            
         }
         
         if( $this->userid > 0 ) {
            $user = new User( $this->userid );
         }
         
         return array(
            'id' => $this->feedbackid,
            'user' => $user instanceof User ? $user->asArray() : '',
            'anonymous' => $user instanceof User ? false : true,
            'trace' => isset( $fields['trace'] ) ? $fields['trace'] : '',
            'content' => isset( $fields['content'] ) ? $fields['content'] : '',
            'fields' => $extra,
            'ipaddress' => $this->ipaddress,
            'serverhost' => $this->serverhost,
            'logged' => $this->logged,
            'replied' => $this->replied,
         );
         
      }
      
   }
   
?>