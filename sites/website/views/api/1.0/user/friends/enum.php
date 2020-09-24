<?php

   /**
    * Class to enumerate friends
    *
    */

   import( 'pages.json' );
   import( 'website.userfriend' );
   
   class APIUserFriendsEnum extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
               )
            )
         );
      }
      /**
       * Enumerate friends
       * 
       * @api-name user.friends.enum
       * @api-auth required
       * @api-result Array Friends
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */ 
      public function Execute() {
         
         $friend = new UserFriend();
         $this->friends = $friend->getFriends();

         $this->result = true;
         $this->message = 'OK';

      }

   }


?>