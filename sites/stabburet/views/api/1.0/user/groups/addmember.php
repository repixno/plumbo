<?php

   /**
    * API class for adding a member to a group.
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'pages.json' );
   import( 'website.group' );

   class APIUserGroupsAddMember extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'userid' => VALIDATE_INTEGER,
                  'groupid' => VALIDATE_INTEGER
               )
            )
         );
      }

      /**
       * Add user to group
       * 
       * @api-name user.groups.addmember
       * @api-auth required
       * @api-post userid Integer ID of the user to add to group
       * @api-post groupid Integer ID of the group
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */      
      public function Execute() {

         // Extract request data.
         $memberid = $_POST[ 'userid' ];
         $groupid = $_POST[ 'groupid' ];

         if ( empty( $memberid ) ) {

            $this->result = false;
            $this->message = 'Missing member id';
            return false;

         }
         if ( empty( $groupid ) ) {

            $this->result = false;
            $this->message = 'Missing group id';
            return false;

         }

         // Remove group.
         $group = new Group( $groupid );
		   $group->addMember( $memberid );

         $this->result = true;
         $this->message = 'OK';

      }

   }


?>
