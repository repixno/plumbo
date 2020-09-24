<?php

   /**
    * API class for removing a member from a group.
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'pages.json' );
   import( 'website.group' );

   class APIUserGroupsRemoveMember extends JSONPage implements IValidatedView {

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
       * Remove a user from a group
       * 
       * @api-name user.groups.removemember
       * @api-auth required
       * @api-post userid Integer ID of the user to remove from the group
       * @api-post groupid Integer ID of the group
       * @api-result ids Array List containing 'userid' and 'groupid'
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */ 
      public function Execute() {

         // Extract request data.
         $memberid = $_POST[ 'userid' ];
         $groupid = $_POST[ 'groupid' ];

         if ( empty( $memberid ) ) {

            $this->result = false;
            $this->message = 'Required input parameter missing or invalid (userid)';
            return false;

         }
         if ( empty( $groupid ) ) {

            $this->result = false;
            $this->message = 'Required input parameter missing or invalid (groupid)';
            return false;

         }

         // Remove group.
         $user = new Group( $groupid );
		   $user->removeMember( $memberid );

		   $this->ids = array(
		      'userid' => $memberid,
		      'groupid' => $groupid
		      );
         $this->result = true;
         $this->message = 'OK';

      }

   }


?>
