<?php

   /**
    * API class for deleting a group.
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'pages.json' );
   import( 'website.group' );

   class APIUserGroupsRemove extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'groupid' => VALIDATE_INTEGER,
               )
            )
         );
      }

      /**
       * Remove a group
       * 
       * @api-name user.groups.remove
       * @api-auth required
       * @api-post groupid Integer ID of the group to remove
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */ 
      public function Execute() {

         // Extract request data.
         $id = $_POST[ 'groupid' ];

         if ( empty( $id ) ) {

            $this->result = false;
            $this->message = 'No group is given';
            return false;

         }

         // Remove group.
         $group = new Group( $id );
         if ( $group->delete() ) {

            $this->result = true;
            $this->message = 'OK';

         } else {

            $this->result = false;
            $this->message = 'Could not delete group';

         }

      }

   }


?>
