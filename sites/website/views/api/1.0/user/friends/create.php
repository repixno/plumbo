<?php

   /**
    * Class create friend in users friend list
    *
    * @author Kristoffer Sivertsen <kristoffer@eurofoto.no>
    *
    */

   import( 'pages.json' );
   import( 'website.user' );
   import( 'website.user' );
   import( 'validate.email' );
   model( 'user.groups' );

   class APIUserFriendsCreate extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            "execute" => array(
               "post" => array(
                  "email" => VALIDATE_STRING,
                  "namefirst" => VALIDATE_STRING,
                  "namelast" => VALIDATE_STRING
               )
            )
         );
      }
      /**
       * Create friend
       * 
       * @api-name user.friends.create
       * @api-auth required
       * @api-post email String E-mail
       * @api-post namefirst String Firstname
       * @api-post namelast String Lastname
       * @api-result email String Provided email address
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */ 
      public function Execute() {

         // Extract request data.
         $email = $_POST[ 'email' ];
         $nameFirst = $_POST[ 'namefirst' ];
         $nameLast = $_POST[ 'namelast' ];

         $this->result = false;
         $this->message = "No email given";

         if ( empty( $email ) || !ValidateEmail::validate( $email ) ) return false;

         $user = new User();
         $user->addFriend( $email, $nameFirst, $nameLast );

         $this->result = true;
         $this->email = $email;
         $this->message = "OK";

      }

   }


?>