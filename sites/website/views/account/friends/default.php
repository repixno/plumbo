<?php

   import( 'website.userfriend' );
   import( 'website.usergroup' );
   
   import( 'pages.protected' );
   
   class AccountFriends extends ProtectedPage implements IView {

      protected $template = 'account.friends.index';
      
      function Execute() {
         
         $friend = new UserFriend();
         $group = new UserGroup();
         
         $this->friends = $friend->getFriends();
         $this->groups = $group->getGroups();
         
      }
      
   }
      
?>