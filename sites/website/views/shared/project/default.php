<?php

   /**
    * Show a shared project
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'website.album' );
   import( 'website.image' );

   class SharedProjectDefault extends UserPage implements IValidatedView {
      
      private $anonymous = 639866;

      public function Validate() {

         return array(
            'execute' => array(
               'fields' => array(
                  VALIDATE_STRING
                  )
               )
            );

      }

      public function Execute( $id = null ) {

         $project = Project::fromShareId( $id );
          Session::set('login_warning','false');

         if($project->isLoaded() ) {

            $clonedProject = clone $project;
            $clonedProject->userid = $this->DefineUserid();
            $clonedProject->share = false;
            $clonedProject->share_id = '';
            $clonedProject->save();
            
            Session::pipe( "sharedproject", true );

            $type = $clonedProject->type ? $clonedProject->type : 'mediaclip';

            relocate( sprintf( '/create/%s/edit/%s', $type, $clonedProject->id ) );

         }

      }
      
      public function DefineUserid(){
      //add support for not_loggedin
      if ( Login::isLoggedIn() ) {
      
         return Login::userid();
      
      } else {
      
         return $this->anonymous;
      
      }
      }

   }

?>
