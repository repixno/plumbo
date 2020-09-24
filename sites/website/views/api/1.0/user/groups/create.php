<?php

   /**
    * API class for creating a group.
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'pages.json' );
   import( 'website.group' );

   class APIUserGroupsCreate extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'title' => VALIDATE_INTEGER,
               )
            )
         );
      }

      /**
       * Create a group
       * 
       * @api-name user.groups.create
       * @api-auth required
       * @api-post title Name of the group to create
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */ 
      public function Execute() {

         // Extract request data.
         $groupname = $_POST[ 'title' ];

         if ( empty( $groupname ) ) {

            $this->result = false;
            $this->message = 'No group name is given';
            return false;

         }

         // Create group.
		   $group = new Group();
		   $group->userid = Login::userid();
		   $group->name = $groupname;
		   $group->save();

         $this->result = true;
         $this->message = 'OK';

      }

   }


?>
