<?php

   /**
    * API for friends related stuff.
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'pages.json' );
   import( 'website.user' );

   class APIUserGetInfo extends JSONPage implements IView {
      /**
       * Get user info
       * 
       * @api-name user.get.info
       * @api-auth required
       * @api-result info Array List of info containing 'fullname', 'firstname', 'middlename', 'lastname', 'username', 'address', 'zipcode', 'city' and 'country'
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */ 
      public function Execute() {
         
         $user = new User( Login::userid() );
         
         $this->result = false;
         $this->message = 'No such user';
         if( !$user instanceof User || !$user->isLoaded() ) return false;
         
         $this->message = 'OK';
         $this->result = true;
         $this->info = array(
            'fullname' => $user->getFullname(),
            'firstname' => $user->firstname,
            'middlename' => $user->middlename,
            'lastname' => $user->lastname,
            'username' => $user->email,
            'address' => $user->streetaddress,
            'zipcode' => $user->zipcode,
            'city' => $user->city,
            'country' => $user->country,
         );
         
      }

   }

?>