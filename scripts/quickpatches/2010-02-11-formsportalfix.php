<?PHP

   include "../../bootstrap.php";
   config('website.config');
   
   import( 'website.form.submission' );
   import( 'website.user' );
   
   $collection = new FormSubmission();
   foreach( $collection->collection( array('submissionid') )->fetchAllAs('FormSubmission') as $submission ) {

      $email = trim( $submission->email );
      
      if( $submission->uid > 0 ) {
         
         $user = new User( $submission->uid );
         
      } elseif( $email != '' ) {
         
         $user = User::fromUsername( $email );
         
      } else {
         
         $user = false;
         
      }
      
      if( $user ) {
         
         $portal = $user->portal;
         $portal = $portal ? $portal : 'EF-997';
         
         echo "$submission->submissionid\t$submission->email => $user->uid / $portal\n";
         
         $submission->portal = $portal;
         $submission->uid = $user->uid;
         $submission->save();
         
      }
      
   }

?>
