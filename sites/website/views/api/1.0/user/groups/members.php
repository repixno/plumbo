<?php

   /**
    * Class to get group members
    *
    */

   import( 'pages.json' );
   import( 'website.usergroup' );

   class APIUserGroupsMembers extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
                  'groupid' => VALIDATE_INTEGER
               )
            )
         );
      }
      /**
       * Get Members
       * 
       * @api-name user.groups.members
       * @api-auth required
       * @api-post groupid Id of the group
       * @api-result members Array member list
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */ 
      public function Execute() {
         
         $group = new UserGroup( $_POST['groupid'] );
         
         $this->members = $group->getMembers();

         $this->result = true;
         $this->message = 'OK';

      }

   }


?>