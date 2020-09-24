<?php

   /**
    * @author Andr� Nordstrand <andre@iw.no>
    */

	import( 'core.util' );
	import( 'website.user' );
	import( 'website.group' );

	class MyAccountFriends extends UserPage implements IValidatedView {

		protected $template = 'myaccount.friends';

		public function Validate() {

         return array(
            'execute' => array(),
            'addfriend' => array(
               'post' => array(
                  'email' => VALIDATE_STRING,
                  'firstname' => VALIDATE_STRING,
                  'lastname' => VALIDATE_STRING,
               )
            ),
            'deletefriend' => array(
               'fields' => array(
                  VALIDATE_INTEGER
               )
            ),
            'editfriend' => array(
               'post' => array(
                  'id' => VALIDATE_INTEGER,
                  'namefirst' => VALIDATE_STRING,
                  'namelast' => VALIDATE_STRING,
                  'email' => VALIDATE_STRING
               )
            ),
            'addgroup' => array(
               'post' => array(
                  'groupname' => VALIDATE_STRING
               )
            ),
            'addtogroup' => array(
               'post' => array(
                  'item' => VALIDATE_INTEGER,
                  'group' => VALIDATE_INTEGER
               )
            ),
            'removefromgroup' => array(
               'fields' => array(
                  VALIDATE_INTEGER,
                  VALIDATE_INTEGER
               )
            ),
            'removegroup' => array(
               'fields' => array(
                  VALIDATE_INTEGER
               )
            )
         );

      }

		public function Execute() {

		   $user = new User( Login::userid() );
         $this->friends = $user->listFriends();

         $groups = new Group();
         $groupList = array();
         foreach( $groups->collection( array( 'groupid' ), array( 'uid' => Login::userid() ) )->fetchAll() as $groupIdent ) {

            $group = new Group( $groupIdent[ 0 ] );
            $groupList[] = array(
               'id' => $group->id,
               'name' => $group->name
               );

         }

         //$this->groups = $groupList;

         $groupMembers = array();
         foreach ( $groupList as $grid ) {

            $groupMembers[] = array(
               'id' => $grid[ 'id' ],
               'name' => $grid[ 'name' ],
               'members' => $user->listFriends( $grid[ 'id' ] )
               );

         }

         $this->groups = $groupMembers;

         // Add message if any.
         $message = Session::pipe( 'friendseditmessage' );
         if ( !empty( $message ) ) {

            $this->message = $message;

         }

		}

		public function addFriend() {

		   // Extract request data.
		   $email = $_POST[ 'friendemail' ];
		   $nameFirst = $_POST[ 'friendnamefirst' ];
		   $nameLast = $_POST[ 'friendnamelast' ];

		   if ( empty( $email ) || !ValidateEmail::validate( $email ) ) {

		      Session::pipe( 'friendseditmessage', 'Email address is invalid' );


		   } else {

            $user = new User();
            if ( !$user->addFriend( $email, $nameFirst, $nameLast ) ) {

               Session::pipe( 'friendseditmessage', 'Email address already exists' );

            }

         }

		   relocate( '/myaccount/friends' );

		}

		public function deleteFriend( $friend ) {
		   $this->setTemplate();

		   $user = new User();
		   $user->removeFriend( $friend );

		   $ret = array(
		      'status' => false,
		      'msg' => '',
		      'result' => array()
		   );


		   $ret[ 'status' ] = true;
		   $ret[ 'msg' ] = 'OK';

		   header( 'Content-Type: application/json' );
		   echo json_encode( $ret );

		}

		public function editfriend() {

		   $id = $_POST[ 'id' ];
		   $email = $_POST[ 'email' ];
		   $nameFirst = $_POST[ 'namefirst' ];
		   $nameLast = $_POST[ 'namelast' ];

		   $data = array(
		      'namefirst' => $nameFirst,
		      'namelast' => $nameLast,
		      'email' => $email
	      );

		   $user = new User();
		   if ( !$user->editFriend( $id, $data ) ) {

		      Session::pipe( 'friendaction', 'Email already exists' );

		   }

		   relocate( '/myaccount/friends' );

		}

		public function addGroup() {

		   // Extract request data.
		   $groupname = $_POST[ 'groupname' ];

		   if ( empty( $groupname ) ) {

		      Session::pipe( 'friendaction', 'Group name is empty' );

		   } else {

            $group = new Group();
            $group->userid = Login::userid();
            $group->name = $groupname;
            $group->save();

         }

		   relocate( '/myaccount/friends' );

		}

		public function removeGroup( $gid ) {

		   // Remove group.
         $group = new Group( $gid );
         if ( $group->delete() ) {

         }

         relocate( '/myaccount/friends' );

		}

		public function addToGroup() {

		   $this->setTemplate();

		   $friend = $_POST[ 'item' ];
		   $group = $_POST[ 'group' ];

		   $ret = array(
		      'status' => false,
		      'msg' => '',
		      'result' => array()
	      );

		   $group = new Group( $group );
		   if ( $group->addMember( $friend ) !== false ) {

		      // Retrieve friends data.
		      $user = new User();
		      if ( $fdata = $user->getFriend( $friend ) ) {

		         $ret[ 'result' ] = array(
		            'id' => $fdata[ 'friend' ],
		            'name' => trim( $fdata[ 'fnamn' ].' '.$fdata[ 'enamn' ] ),
		            'username' => $fdata[ 'brukarnamn' ]
	            );

		      }

		      $ret[ 'status' ] = true;
		      $ret[ 'msg' ] = 'OK';

		   } else {

		      $ret[ 'msg' ] = 'Something went wrong';

		   }

		   header( 'Content-Type: application/json' );
         echo json_encode( $ret );

		}

		public function removeFromGroup( $item, $group ) {

		   $user = new User();
		   $user->removeFriendFromGroup( $item, $group );

		   relocate( '/myaccount/friends' );

		}

	}

?>