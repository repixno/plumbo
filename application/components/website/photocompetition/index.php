<?php
   
   model( 'site.photocompetition.index' );
   
   class PhotoCompetition extends DBPhotoCompetition {
      
      static function fromUrlName( $urlname ) {
         
         return Model::fromFieldValue( 
            array( 
               'urlname' => $urlname,
            ), 'PhotoCompetition'
         );
         
      }
      
      public function asArray() {
         
         return array(
            
            'id' => $this->id,
            'userid' => $this->userid,
            'uploadaid' => $this->uploadaid,
            'approvedaid' => $this->approvedaid,
				'title' => $this->title,
				'urlname' => $this->urlname,
				'template' =>  $this->template,
				'description' => $this->description,
				'oncompleted' => $this->oncompleted,
            
         );
         
      }
      
   }
   
?>