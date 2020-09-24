<?php
    import( 'website.felix');
    import( 'website.cart' );
    import( 'website.order.default' );  
    class BekreftaFelix extends WebPage implements IView {
       
        protected $template = 'bekrefta.index';
        private $jsonfolder = '/data/pd/felix/canvas/'; 
        private $thumbfolder = '/data/pd/felix/thumb/';
         
        public function Execute($id) {
            
            
            $cart = new Cart();
           
           
           if( $_POST ){
       
                $userinfo = $_POST;
                
                $email = $_POST['email'];
                $newsletter = $_POST['newsletterjp']?true:false;
                $newsletterfelix = $_POST['newsletterfelix']?true:false;
       
                $user = User::fromUsernameAndPortal($email, 'FE-001');

                if( $user ){
                    $user = new User( $user->uid );
                    $user->setFullname( $userinfo['name'] );
                    $user->setCellPhone( $userinfo["phone"] );
                    $user->streetaddress = $userinfo['address'];
                    $user->zipcode = $userinfo['zip'];
                    $user->city = $userinfo['city'];
                    if( $newsletter ) {
                        $user->newsletter = true;
                        $user->html = true;
                    }
                    if( $newsletterfelix ){
                        $user->newsletter_others = true;
                    }
                    
                    $user->save();
                    
                    if( !Login::isLoggedIn() ){
                        if( !Login::byUserObject( $user ) ) {
                            throw new CriticalException( 'Unable to login' );
                        }
                    }
                    
                }else{
                    
                    try {
                        // Fine - passed all checks
                        // Let's create a new user
                        
                        $user = new User();
                        $user->username = $email;
                        $user->password = 'nopass';
                        $user->portal = Dispatcher::getPortal();
                        
                        if( $newsletter ) {
                           $user->newsletter = true;
                           $user->html = true;
                        }
                        if( $newsletterfelix ){
                            $user->newsletter_others = true;
                        }
                        
                        $user->setFullname( $userinfo['name'] );
                        $user->streetaddress = $userinfo['address'];
                        $user->zipcode = $userinfo['zip'];
                        
                        
                        $user->country = 203;
                        $user->city = $userinfo['city'];
                        $user->created = date( 'Y-m-d H:i:s' );
    
                        $user->save();
            
                        // New user is created and saved
                        // Continue and list delivery methods
                        if( !Login::byUserObject( $user ) ) {
                            throw new CriticalException( 'Unable to login' );
                        }
                        
                    } catch( Exception $e ) {
                        Session::pipe( 'registererror', "Unknown error trying to create new user" );
                        relocate( 'bekrefta' );
                        die();  
                    }
                    
                }
                
                
                
                $items = explode( ' ', $userinfo['name'] );
                $firstname = trim( array_shift( $items ) );
                $lastname = trim( array_pop( $items ) );
                $middlename = trim( implode( ' ', $items ) );
                $firstname = $firstname." ".$middlename;
                
                
                $userorder = new UserOrder();
                $contact["name"] = $userinfo['name'];
                $contact["firstname"] = $firstname;
                $contact["lastname"] = $lastname;
                $contact["zipcode"] = $userinfo['zip'];
                $contact["city"] = $userinfo['city'];
                $contact["address"] = $userinfo['address'];
                $contact["country"] =$userinfo['country'];
                $contact["mphone"] = $userinfo['phone'];
                
                // Setup delivery info
                $delivery["name"] = $userinfo['name'];
                $delivery["firstname"] = $firstname;
                $delivery["lastname"] = $lastname;
                $delivery["zipcode"] =  $userinfo['zip'];
                $delivery["city"]    =  $userinfo['city'];
                $delivery["address"] =  $userinfo['address'];
                $delivery["country"] =  $userinfo['phone'];
                
                $userorder->setContactInfo( $contact );
                $userorder->setDeliveryInfo( $delivery );
                
                if( $orderidresult = $userorder->executeOrder() ){
                    $felix = new Felix($id);
                    $felix->orderid = $orderidresult;
                    $felix->save();
                    UserSessionArray::clearItems( 'purchased_cart' );
                    UserSessionArray::addItem( 'purchased_cart', $cart->enum() );
                  
                    relocate( '/bekrafta/finished/' .  $orderidresult );
                    die();
                  
                }else {
                    //Util::Debug("ka farskebn");
                    mail( 'tor.inge@eurofoto.no' , "Klarna order failed ", $message  );
                    relocate( '/checkout/error' );   
                }
                
                exit;
           }else{
                 
                $orderid = DB::query( "SELECT orderid FROM order_felix WHERE id = ?", $id )->fetchSingle();
                 
                if( $orderid > 0 ){
                    relocate( '/skapa' );
                }
                else{
                    $this->articleid = DB::query( "SELECT productid FROM order_felix WHERE id = ?", $id )->fetchSingle();
                    $this->thumb = file_get_contents( $this->thumbfolder . $id ); 
                }
                
           }     
        }
        
        
        public function Finished($id) {
            
            $this->template = 'bekrefta.complete';
            
            $cart = new Cart();
            $cart->clear();
            Login::logout();
            
            $this->orderid = $id;
            
            //Util::Debug( "ditt ordrenr er $id" );
            //exit;
            
        }
       
    } 
?>