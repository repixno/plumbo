<?php

   /**
    * @author Andr� Nordstrand <andre@iw.no>
    *
    */

   import( 'core.util' );
   import( 'website.user' );
   model( 'user.friends' );
   
   class Friends extends DBUserFriends {

      public function asArray() {

         return array(
            'userid'    => $this->userId,
            'friendid'  => $this->friendId,
            'username'  => $this->userName,
            'nickname'  => $this->nickName,
            'created'   => $this->creationDate,
            'firstname' => $this->firstName,
            'middlename'=> $this->middleName,
            'surname'   => $this->surName,
            'cellphone' => $this->cellphoneNumber,
            'address'   => $this->address,
            'zipcode'   => $this->zipcode,
            'postoffice'=> $this->postOffice,
            'phone'     => $this->phoneNumber,
            'country'   => $this->countryCode,
			);

      }

      public function addFriend( $email ) {

         // Check for existing user.
         $user = User::fromUsernameAndPortal( $email, Dispatcher::getPortal() );
         if ( !isset( $user ) ) {
            
            $user = new User();
            $user->username = $email;
            $user->password = 'nopass';
            $user->created = date('Y-m-d H:i:s');
            $user->save();
            
         }
         
         if ( !isset( $user ) ) {
            
            return false;
            
         }
         
         $friend = new Friends();
         $friend->userid = Login::userid();
         $friend->friendid = $user->userid;
         $friend->username = $user->username;
         $friend->creationdate = date('Y-m-d H:i:s');
         $friend->save();
         
         die();

      }

   }

?>