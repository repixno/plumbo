<?php

   /**
    * API for friends related stuff.
    *
    * @author Svein Arild Bergset <sab@interweb.no>
    *
    */

   import( 'pages.json' );
   import( 'website.user' );

   class APIUserFriends extends JSONPage implements IValidatedView {

      public function Validate() {

         return array(
            'removefromgroup' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_INTEGER
               ),
               'post' => array(
                  'userid' => VALIDATE_INTEGER,
                  'groupid' => VALIDATE_INTEGER
                  )
            )
         );

      }


      /**
       * Remove a friend from group
       * 
       * @api-name user.friends.removefromgroup
       * @api-auth required
       * @api-param-optional item Integer  to remove
       * @api-param-optional group Integer Group to remove Item from
       * @api-post-optional item Integer Item to remove
       * @api-post-optional group Integer Group to remove Item from
       * @api-result ids Array List containing 'userid' and 'groupid'
       * @api-result result Boolean true/false
       * @api-result message String Describes the result of the operation in US English
       */ 
      public function removeFromGroup( $userid = null, $groupid = null ) {

         $userid = isset( $userid ) ? $userid : $_POST[ 'userid' ];
         $groupid = isset( $groupid ) ? $groupid : $_POST[ 'groupid' ];

         $user = new User();
		   $user->removeFriendFromGroup( $userid, $groupid );

		   $this->ids = array( 'userid' => $userid, 'groupid' => $groupid );
		   $this->result = true;
		   $this->message = 'OK';


      }

   }

?>
