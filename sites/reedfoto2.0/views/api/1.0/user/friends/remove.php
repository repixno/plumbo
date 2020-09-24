<?php

   /**
    * Class remove friend from friendlist
    *
    * @author Kristoffer Sivertsen <kristoffer@eurofoto.no>
    *
    */

   import( 'pages.json' );
   import( 'website.user' );

   class APIUserFriendsRemove extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            "execute" => array(
               "post" => array(
                  "userid" => VALIDATE_INTEGER,
               )
            )
         );
      }
      
      /**
       * Remove friend
       * 
       * @api-name user.friends.remove
       * @api-auth required
       * @api-post userid Integer ID of the user to remove
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */
      public function Execute() {

         // Extract request data.
         $id = $_POST[ 'userid' ];

         if ( empty( $id ) ) {

            $this->result = false;
            $this->message = "No friend is given";
            return false;

         }

         // Remove friend.
         $user = new User( Login::userid() );
         $user->removeFriend( $id );

         $this->result = true;        
         $this->userid = $id;
         $this->message = "OK";

      }

   }


?>