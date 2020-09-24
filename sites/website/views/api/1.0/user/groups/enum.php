<?php

   /**
    * Class to enumerate Groups
    *
    */

   import( 'pages.json' );
   import( 'website.usergroup' );

   class APIUserGroupsEnum extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'execute' => array(
               'post' => array(
               )
            )
         );
      }
      /**
       * Enumerate Groups
       * 
       * @api-name user.groups.enum
       * @api-auth required
       * @api-result Array Groups
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */ 
      public function Execute() {
         
         $group = new UserGroup();
         $this->groups = $group->getGroups();

         $this->result = true;
         $this->message = 'OK';

      }

   }


?>